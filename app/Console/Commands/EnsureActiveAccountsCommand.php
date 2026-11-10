<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnsureActiveAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:ensure-active-accounts {--reset-passwords : Reset password seluruh guru ke NIP dan siswa ke NISN}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memastikan seluruh akun guru, tenaga kependidikan (TU), dan siswa aktif serta dapat login dengan lancar.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================================');
        $this->info(' SINKRONISASI & VERIFIKASI AKUN AKTIF GURU, TU & SISWA SISSMA1LE');
        $this->info('====================================================================');

        $resetPasswords = (bool) $this->option('reset-passwords');

        // 1. Pastikan seluruh Master Role terdaftar di database
        $this->info('1. Memverifikasi daftar master peran (roles)...');
        $systemRoles = [
            'super_admin' => ['display_name' => 'Super Administrator', 'level' => 1],
            'admin' => ['display_name' => 'Administrator Sistem', 'level' => 2],
            'kepala_sekolah' => ['display_name' => 'Kepala Sekolah', 'level' => 3],
            'wakasek_kurikulum' => ['display_name' => 'Wakasek Bid. Kurikulum', 'level' => 4],
            'wakasek_kesiswaan' => ['display_name' => 'Wakasek Bid. Kesiswaan', 'level' => 4],
            'wakasek_humas' => ['display_name' => 'Wakasek Bid. Humas', 'level' => 4],
            'wakasek_sarpras' => ['display_name' => 'Wakasek Bid. Sarpras', 'level' => 4],
            'kepala_laboratorium' => ['display_name' => 'Kepala Laboratorium', 'level' => 5],
            'kepala_perpustakaan' => ['display_name' => 'Kepala Perpustakaan', 'level' => 5],
            'koordinator_bk' => ['display_name' => 'Koordinator BP/BK', 'level' => 5],
            'koordinator_keagamaan' => ['display_name' => 'Koordinator Keagamaan', 'level' => 5],
            'wali_kelas' => ['display_name' => 'Wali Kelas', 'level' => 6],
            'bk' => ['display_name' => 'Guru BK', 'level' => 6],
            'guru_piket' => ['display_name' => 'Petugas Piket (Guru)', 'level' => 6],
            'tu_piket' => ['display_name' => 'Petugas Piket (TU)', 'level' => 6],
            'petugas_piket' => ['display_name' => 'Petugas Piket', 'level' => 6],
            'pengelola_perpustakaan' => ['display_name' => 'Pengelola Perpustakaan', 'level' => 6],
            'pustakawan' => ['display_name' => 'Pustakawan', 'level' => 6],
            'pembina_ekstra' => ['display_name' => 'Pembina Ekstrakurikuler', 'level' => 6],
            'guru' => ['display_name' => 'Tenaga Pendidik (Guru Mapel)', 'level' => 7],
            'kepala_administrasi' => ['display_name' => 'Kepala Urusan Tata Usaha', 'level' => 5],
            'bendahara_bosp' => ['display_name' => 'Bendahara BOSP', 'level' => 6],
            'bendahara_bopd' => ['display_name' => 'Bendahara BOPD', 'level' => 6],
            'bendahara_gaji' => ['display_name' => 'Bendahara Gaji', 'level' => 6],
            'bendahara' => ['display_name' => 'Bendahara Sekolah', 'level' => 6],
            'operator_dapodik' => ['display_name' => 'Operator Dapodik', 'level' => 6],
            'operator' => ['display_name' => 'Operator Dapodik / Sekolah', 'level' => 6],
            'ta_kesiswaan' => ['display_name' => 'Adm. Urusan Kesiswaan', 'level' => 6],
            'ta_kepegawaian' => ['display_name' => 'Adm. Urusan Kepegawaian', 'level' => 6],
            'ta_sarpras' => ['display_name' => 'Adm. Urusan Sarpras', 'level' => 6],
            'ta_persuratan' => ['display_name' => 'Adm. Urusan Persuratan', 'level' => 6],
            'staff' => ['display_name' => 'Tenaga Kependidikan (Staf TU)', 'level' => 7],
            'tu' => ['display_name' => 'Staff Tata Usaha', 'level' => 7],
            'siswa' => ['display_name' => 'Siswa', 'level' => 8],
            'orang_tua' => ['display_name' => 'Orang Tua / Wali Siswa', 'level' => 9],
            'sekretaris_kelas' => ['display_name' => 'Sekretaris Kelas', 'level' => 8],
            'sekretaris_ekstra' => ['display_name' => 'Sekretaris Ekstrakurikuler', 'level' => 8],
        ];

        foreach ($systemRoles as $roleName => $meta) {
            Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'display_name' => $meta['display_name'],
                    'level' => $meta['level'],
                    'description' => 'Peran bawaan sistem SISSMA1LE: '.$meta['display_name'],
                ]
            );
        }

        // 2. Aktifkan seluruh akun pengguna yang berstatus non-aktif
        $reactivatedCount = User::where('is_active', false)->update(['is_active' => true]);
        $this->info("2. Mengaktifkan akun yang non-aktif: {$reactivatedCount} akun berhasil diaktifkan.");

        // 3. Sinkronisasi Akun Guru & Tenaga Kependidikan (Teacher)
        $this->info('3. Memeriksa relasi dan akun untuk seluruh Data Guru & Tenaga Kependidikan...');
        $teachers = Teacher::all();
        $syncedTeachers = 0;
        $createdTeacherUsers = 0;

        foreach ($teachers as $teacher) {
            $user = null;
            if ($teacher->user_id) {
                $user = User::find($teacher->user_id);
            }

            if (! $user && ! empty($teacher->nip)) {
                $user = User::where('username', $teacher->nip)
                    ->orWhere('nip', $teacher->nip)
                    ->orWhere('email', $teacher->nip.'@sma1le.sch.id')
                    ->first();
            }

            if (! $user) {
                $isTendik = str_contains(strtolower($teacher->title ?? ''), 'tu')
                    || str_contains(strtolower($teacher->title ?? ''), 'administrasi')
                    || str_contains(strtolower($teacher->title ?? ''), 'staf');

                $primaryRole = $isTendik ? 'staff' : 'guru';
                $email = ! empty($teacher->nip)
                    ? ($teacher->nip.'@sma1le.sch.id')
                    : ('guru.'.Str::slug($teacher->name).'.'.$teacher->id.'@sma1le.sch.id');

                // Hindari duplikasi email
                if (User::where('email', $email)->exists()) {
                    $email = 'user.'.$teacher->id.'.'.time().'@sma1le.sch.id';
                }

                $username = ! empty($teacher->nip) ? $teacher->nip : ('pegawai_'.$teacher->id);
                if (User::where('username', $username)->exists()) {
                    $username = $username.'_'.$teacher->id;
                }

                $defaultPassword = ! empty($teacher->nip) ? $teacher->nip : 'password';

                $user = User::create([
                    'name' => $teacher->name,
                    'email' => $email,
                    'username' => $username,
                    'nip' => $teacher->nip,
                    'phone' => $teacher->phone,
                    'password' => Hash::make($defaultPassword),
                    'role' => $primaryRole,
                    'roles' => [$primaryRole],
                    'is_active' => true,
                ]);

                $createdTeacherUsers++;
            }

            // Update user properties
            $user->is_active = true;
            if (! empty($teacher->nip) && empty($user->nip)) {
                $user->nip = $teacher->nip;
            }
            if ($resetPasswords && ! empty($teacher->nip)) {
                $user->password = Hash::make($teacher->nip);
            }
            $user->save();

            if ($teacher->user_id !== $user->id || ! $teacher->is_active) {
                $teacher->updateQuietly([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
                $syncedTeachers++;
            }
        }

        $this->info("   -> Guru & Tenaga Kependidikan: {$teachers->count()} diperiksa, {$createdTeacherUsers} akun baru dibuat, {$syncedTeachers} tautan relasi diperbarui.");

        // 4. Sinkronisasi Akun Pegawai (Employee)
        $employees = Employee::all();
        $syncedEmployees = 0;
        foreach ($employees as $emp) {
            $user = $emp->user_id ? User::find($emp->user_id) : null;
            if (! $user && ! empty($emp->nip)) {
                $user = User::where('nip', $emp->nip)->orWhere('username', $emp->nip)->first();
            }

            if ($user && $emp->user_id !== $user->id) {
                $emp->updateQuietly(['user_id' => $user->id, 'is_active' => true]);
                $syncedEmployees++;
            }
        }
        if ($employees->isNotEmpty()) {
            $this->info("   -> Data Kepegawaian (Employees): {$employees->count()} diperiksa, {$syncedEmployees} ditautkan.");
        }

        // 5. Sinkronisasi Akun Siswa (Student)
        $this->info('4. Memeriksa relasi dan akun untuk seluruh Data Siswa...');
        $students = Student::all();
        $syncedStudents = 0;
        $createdStudentUsers = 0;

        foreach ($students as $student) {
            $user = null;
            if ($student->user_id) {
                $user = User::find($student->user_id);
            }

            $nisn = trim((string) ($student->nisn ?? ''));
            $nis = trim((string) ($student->nis ?? ''));

            if (! $user && $nisn !== '') {
                $user = User::where('username', $nisn)
                    ->orWhere('email', $nisn.'@siswa.sma1le.sch.id')
                    ->first();
            }

            if (! $user && $nis !== '') {
                $user = User::where('username', $nis)
                    ->orWhere('email', 'siswa.'.$nis.'@siswa.sma1le.sch.id')
                    ->first();
            }

            if (! $user) {
                $username = $nisn !== '' ? $nisn : ($nis !== '' ? $nis : ('siswa_'.$student->id));
                $email = $nisn !== ''
                    ? ($nisn.'@siswa.sma1le.sch.id')
                    : ('siswa.'.($nis ?: $student->id).'@siswa.sma1le.sch.id');

                if (User::where('email', $email)->exists()) {
                    $email = 'siswa.'.$student->id.'.'.time().'@siswa.sma1le.sch.id';
                }

                if (User::where('username', $username)->exists()) {
                    $username = $username.'_'.$student->id;
                }

                $defaultPassword = $nisn !== '' ? $nisn : ($nis !== '' ? $nis : 'password');

                $user = User::create([
                    'name' => $student->name,
                    'email' => $email,
                    'username' => $username,
                    'phone' => $student->phone,
                    'password' => Hash::make($defaultPassword),
                    'role' => 'siswa',
                    'roles' => ['siswa'],
                    'is_active' => true,
                ]);

                $createdStudentUsers++;
            }

            // Update user properties
            $user->is_active = true;
            if ($resetPasswords) {
                $defaultPass = $nisn !== '' ? $nisn : ($nis !== '' ? $nis : 'password');
                $user->password = Hash::make($defaultPass);
            }
            $user->save();

            if ($student->user_id !== $user->id || ! $student->is_active) {
                $student->updateQuietly([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
                $syncedStudents++;
            }
        }

        $this->info("   -> Siswa: {$students->count()} diperiksa, {$createdStudentUsers} akun baru dibuat, {$syncedStudents} relasi ditautkan.");

        // 6. Sinkronisasi Pivot user_has_roles & active_role_id
        $this->info('5. Sinkronisasi tabel pivot user_has_roles & active_role_id untuk semua akun...');
        $allUsers = User::all();
        $rolesMap = Role::pluck('id', 'name')->toArray();

        foreach ($allUsers as $u) {
            $userRoles = $u->all_roles;
            $roleIds = [];
            foreach ($userRoles as $rName) {
                if (isset($rolesMap[$rName])) {
                    $roleIds[] = $rolesMap[$rName];
                }
            }

            if (! empty($roleIds)) {
                $u->roles()->sync($roleIds);
            }

            $currentActiveRoleName = $u->role;
            if (isset($rolesMap[$currentActiveRoleName])) {
                if ($u->active_role_id !== $rolesMap[$currentActiveRoleName]) {
                    $u->updateQuietly(['active_role_id' => $rolesMap[$currentActiveRoleName]]);
                }
            } elseif (! empty($roleIds)) {
                $u->updateQuietly(['active_role_id' => $roleIds[0]]);
            }
        }

        $this->info('====================================================================');
        $this->info(' SELESAI: Seluruh akun guru, TU, dan siswa telah aktif & siap login!');
        $this->info('====================================================================');

        return self::SUCCESS;
    }
}
