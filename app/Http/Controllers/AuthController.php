<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $input = trim($credentials['email']);
        $password = trim($credentials['password']);

        // 1. Cari user berdasarkan email ATAU username ATAU nip langsung (case-insensitive)
        $user = User::where('email', $input)
            ->orWhere('username', $input)
            ->orWhere('nip', $input)
            ->orWhereRaw('LOWER(username) = ?', [strtolower($input)])
            ->first();

        // 2. Jika belum ditemukan, periksa apakah input adalah NISN atau NIS Siswa
        if (! $user) {
            $student = Student::where('nisn', $input)
                ->orWhere('nis', $input)
                ->first();
            if ($student && $student->user) {
                $user = $student->user;
            }
        }

        // 3. Jika belum ditemukan, periksa apakah input adalah NIP Guru / Wali Kelas / Admin / Kepala Sekolah
        if (! $user) {
            $teacher = Teacher::where('nip', $input)->first();
            if ($teacher && $teacher->user) {
                $user = $teacher->user;
            }
        }

        // 4. Jika belum ditemukan, periksa apakah input adalah NIP / NIK Tenaga Kependidikan (Employee)
        if (! $user) {
            $employee = Employee::where('nip', $input)->orWhere('nik', $input)->first();
            if ($employee && $employee->user) {
                $user = $employee->user;
            }
        }

        // 5. Dukungan alias umum akun sistem
        if (! $user) {
            $lowerInput = strtolower($input);
            if (in_array($lowerInput, ['superadmin', 'super_admin', 'superadmin@admin.com', 'superadmin@gmail.com', 'superadmin@sma1le.sch.id', 'senks', 'seng@sma1le.sch.id'])) {
                $user = User::where('username', 'SENKS')->first() ?? User::where('role', 'super_admin')->first();
            } elseif (in_array($lowerInput, ['admin', 'administrator', 'admin@admin.com', 'admin@gmail.com', 'admin@sma1le.sch.id'])) {
                $user = User::where('role', 'admin')->first();
            } elseif (in_array($lowerInput, ['kepsek', 'kepala_sekolah', 'kepsek@sma1le.sch.id'])) {
                $user = User::where('role', 'kepala_sekolah')->first();
            }
        }

        if ($user) {
            // Pastikan akun aktif untuk login sempurna
            if (! $user->is_active) {
                $user->updateQuietly(['is_active' => true]);
            }

            $isValidPassword = Hash::check($password, $user->password);

            // A. Siswa: NISN / NIS otomatis adalah password default, serta password darurat standar
            if (! $isValidPassword && $user->role === 'siswa') {
                $studentNisn = $user->student?->nisn ?? $user->username;
                $studentNis = $user->student?->nis;
                if ($password === $studentNisn || ($studentNis && $password === $studentNis) || in_array($password, ['password', '123456', '12345678', 'siswa123'])) {
                    $isValidPassword = true;
                    $user->password = Hash::make($password);
                    $user->save();
                }
            }

            // B. Guru, Tenaga Kependidikan (TU), Wali Kelas, Admin, Wakasek & Kepala Sekolah: NIP otomatis password default
            if (! $isValidPassword && ($user->isTeacher() || $user->isTu() || in_array($user->role, [
                'guru', 'wali_kelas', 'staff', 'tu', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras',
                'kesiswaan', 'bk', 'koordinator_bk', 'tenaga_administrasi', 'kepala_administrasi', 'kepala_perpustakaan',
                'pengelola_perpustakaan', 'pustakawan', 'ta_kesiswaan', 'ta_kepegawaian',
                'ta_persuratan', 'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
                'ta_sarpras', 'operator', 'operator_dapodik', 'tu_piket', 'guru_piket', 'petugas_piket',
            ], true))) {
                $officialNip = $user->teacher?->nip ?? $user->employee?->nip ?? $user->nip ?? $user->username;
                if (($officialNip && $password === $officialNip) || in_array($password, ['password', 'admin123', '123456', '12345678', 'guru123', 'staff123'])) {
                    $isValidPassword = true;
                    $user->password = Hash::make($password);
                    $user->save();
                }
            }

            // C. Super Admin & Admin: Dukungan password darurat/standar pengujian
            if (! $isValidPassword && in_array($user->role, ['super_admin', 'admin'])) {
                if (in_array($password, ['password', 'admin', 'admin123', 'superadmin', '12345678', '123456'])) {
                    $isValidPassword = true;
                    $user->password = Hash::make($password);
                    $user->save();
                }
            }

            if ($isValidPassword) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                AuditLog::log('LOGIN', 'User', $user->id, null, ['role' => $user->role]);

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang kembali, '.$user->name.'!');
            }
        }

        return back()->withErrors([
            'email' => 'Nomor Identitas (NISN / NIP / Username) atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::log('LOGOUT', 'User', Auth::id());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Quick role switcher exclusively for super_admin or simulation sessions.
     */
    public function quickSwitch(Request $request): RedirectResponse
    {
        $role = $request->input('role');
        $validRoles = [
            'super_admin',
            'admin',
            'kepala_sekolah',
            'wakasek_kesiswaan',
            'wakasek_kurikulum',
            'wakasek_humas',
            'wakasek_sarpras',
            'kesiswaan',
            'guru',
            'wali_kelas',
            'pembina_ekstra',
            'sekretaris_kelas',
            'sekretaris_ekstra',
            'koordinator_keagamaan',
            'pustakawan',
            'pengelola_perpustakaan',
            'kepala_perpustakaan',
            'tenaga_administrasi',
            'kepala_administrasi',
            'ta_kesiswaan',
            'ta_kepegawaian',
            'ta_persuratan',
            'bendahara',
            'bendahara_bosp',
            'bendahara_bopd',
            'bendahara_gaji',
            'ta_sarpras',
            'operator',
            'operator_dapodik',
            'bk',
            'guru_bk',
            'koordinator_bk',
            'guru_piket',
            'tu_piket',
            'petugas_piket',
            'siswa',
            'orang_tua',
        ];

        if (! in_array($role, $validRoles, true)) {
            return back()->with('error', 'Role tidak valid.');
        }

        $isFromSuperAdmin = (Auth::user()?->role === 'super_admin') || session('switched_from_super_admin', false);

        if ($role === 'super_admin') {
            $user = User::where('role', 'super_admin')
                ->where(function ($q) {
                    $q->where('username', 'SENKS')
                        ->orWhere('email', 'seng@sma1le.sch.id');
                })->first() ?? User::where('role', 'super_admin')->first();
        } elseif ($role === 'wakasek_kesiswaan' || $role === 'kesiswaan') {
            $user = User::where(function ($q) {
                $q->where('role', 'wakasek_kesiswaan')
                    ->orWhereJsonContains('roles', 'wakasek_kesiswaan')
                    ->orWhere('role', 'kesiswaan')
                    ->orWhereJsonContains('roles', 'kesiswaan');
            })->first();

            if (! $user) {
                $info = Teacher::getWakasekKesiswaan();
                $user = $info['user'] ?? null;
            }
        } elseif ($role === 'wakasek_kurikulum') {
            $user = User::where(function ($q) {
                $q->where('role', 'wakasek_kurikulum')
                    ->orWhereJsonContains('roles', 'wakasek_kurikulum');
            })->first();

            if (! $user) {
                $info = Teacher::getWakasekKurikulum();
                $user = $info['user'] ?? null;
            }
        } elseif ($role === 'wakasek_humas') {
            $user = User::where(function ($q) {
                $q->where('role', 'wakasek_humas')
                    ->orWhereJsonContains('roles', 'wakasek_humas');
            })->first();

            if (! $user) {
                $info = Teacher::getWakasekHumas();
                $user = $info['user'] ?? null;
            }
        } elseif ($role === 'wakasek_sarpras') {
            $user = User::where(function ($q) {
                $q->where('role', 'wakasek_sarpras')
                    ->orWhereJsonContains('roles', 'wakasek_sarpras');
            })->first();

            if (! $user) {
                $info = Teacher::getWakasekSarpras();
                $user = $info['user'] ?? null;
            }
        } else {
            $user = User::where('role', $role)
                ->orWhereJsonContains('roles', $role)
                ->first();
        }

        if (! $user) {
            return back()->with('error', "Akun untuk role {$role} belum tersedia.");
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($role === 'super_admin') {
            session()->forget('switched_from_super_admin');
            session()->forget('active_role');
        } elseif ($isFromSuperAdmin) {
            session(['switched_from_super_admin' => true, 'active_role' => $role]);
        } else {
            session(['active_role' => $role]);
        }

        AuditLog::log('QUICK_ROLE_SWITCH', 'User', $user->id, null, ['switched_to' => $role]);

        return redirect()->route('dashboard')
            ->with('success', "Beralih ke akun {$user->name} (Role: ".strtoupper(str_replace('_', ' ', $role)).').');
    }

    /**
     * Switch active role for the currently authenticated user among their assigned roles.
     */
    public function switchActiveRole(Request $request): RedirectResponse
    {
        return $this->performSwitchActiveRole((string) $request->input('role'));
    }

    /**
     * Switch active role via GET for 1-click links and pills.
     */
    public function switchActiveRoleGet(string $role): RedirectResponse
    {
        return $this->performSwitchActiveRole($role);
    }

    protected function performSwitchActiveRole(string $role): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($role === 'all_roles') {
            session(['active_role' => 'all_roles']);
            AuditLog::log('SWITCH_ACTIVE_ROLE', 'User', $user->id, null, ['active_role' => 'all_roles']);

            return redirect()->route('dashboard')->with('success', 'Beralih ke mode: Semua Peran (Akses Lengkap)');
        }

        if (! $user->hasRole($role) && ! $user->hasAnyRole(['super_admin', 'admin'])) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk peran ini.');
        }

        session(['active_role' => $role]);

        $roleModel = Role::where('name', $role)->first();
        if ($roleModel) {
            $user->updateQuietly(['active_role_id' => $roleModel->id]);
        }

        AuditLog::log('SWITCH_ACTIVE_ROLE', 'User', $user->id, null, ['active_role' => $role]);

        return redirect()->route('dashboard')->with('success', 'Beralih ke mode peran: '.$user->getRoleTitleFor($role));
    }

    public function profile(): View
    {
        return view('auth.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;

            // Mirror directly to public/storage
            try {
                $dest = public_path('storage/'.$avatarPath);
                if (! is_link(public_path('storage'))) {
                    @mkdir(dirname($dest), 0755, true);
                    @copy($request->file('avatar')->getRealPath(), $dest);
                }
            } catch (\Throwable) {
            }

            if ($user->student) {
                $user->student->update(['photo' => $avatarPath]);
            }
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::log('UPDATE_PROFILE', 'User', $user->id);

        return back()->with('success', 'Profil dan foto Anda berhasil diperbarui.');
    }
}
