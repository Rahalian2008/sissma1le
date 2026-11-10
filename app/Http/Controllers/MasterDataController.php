<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Extracurricular;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use App\Services\AttendanceService;
use App\Services\CsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterDataController extends Controller
{
    public function __construct(
        protected CsvImportService $csvImportService,
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Tampilan Pengaturan Branding (Logo Aplikasi, KOP Surat, & Nama Aplikasi)
     */
    public function branding(): View
    {
        $logoPath = SchoolSetting::get('school_logo');
        $kopPath = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));

        $logoUrl = $logoPath && (Storage::disk('public')->exists($logoPath) || file_exists(public_path('storage/'.$logoPath)))
            ? asset('storage/'.$logoPath)
            : null;

        $kopUrl = $kopPath && (Storage::disk('public')->exists($kopPath) || file_exists(public_path('storage/'.$kopPath)))
            ? asset('storage/'.$kopPath)
            : null;

        $province = SchoolSetting::get('school_province', 'JAWA BARAT');
        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $appName = SchoolSetting::get('app_name', 'SISWA SMA1LE');
        $appTagline = SchoolSetting::get('app_tagline', 'Sistem Informasi Siswa Terpadu SMAN 1 Lengkong');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $schoolNpsn = SchoolSetting::get('school_npsn', '20202265');
        $schoolNss = SchoolSetting::get('school_nss', '301051408001');
        $schoolAccreditation = SchoolSetting::get('school_accreditation', 'A (Unggul)');

        return view('master.branding', compact(
            'logoUrl',
            'kopUrl',
            'province',
            'schoolName',
            'appName',
            'appTagline',
            'schoolBranch',
            'schoolAddress',
            'schoolEmail',
            'schoolWebsite',
            'schoolNpsn',
            'schoolNss',
            'schoolAccreditation'
        ));
    }

    /**
     * Update Nama & Identitas Aplikasi
     */
    public function updateAppName(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_tagline' => ['nullable', 'string', 'max:255'],
        ]);

        SchoolSetting::set('app_name', trim($validated['app_name']), 'branding', 'Nama resmi aplikasi');
        if (isset($validated['app_tagline'])) {
            SchoolSetting::set('app_tagline', trim($validated['app_tagline']), 'branding', 'Tagline / Deskripsi aplikasi');
        }

        AuditLog::log('UPDATE_APP_NAME', 'SchoolSetting', null, null, $validated);

        return back()->with('success', 'Nama aplikasi berhasil diperbarui dan diterapkan ke seluruh antarmuka sistem.');
    }

    /**
     * Upload Logo Aplikasi
     */
    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:5120'],
        ]);

        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'logo_'.time().'.'.$extension;

        // Delete old logo if exists
        $oldLogo = SchoolSetting::get('school_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        if ($oldLogo) {
            @unlink(public_path('storage/'.$oldLogo));
        }

        $path = $file->storeAs('branding', $filename, 'public');
        SchoolSetting::set('school_logo', $path, 'branding', 'Path logo resmi aplikasi');

        // Synchronize with SchoolIdentity
        try {
            SchoolIdentity::current()->update(['logo_path' => $path]);
        } catch (\Throwable) {
        }

        // Mirror directly to public/storage
        try {
            $dest = public_path('storage/'.$path);
            $storedSource = Storage::disk('public')->path($path);
            if (! is_link(public_path('storage'))) {
                @mkdir(dirname($dest), 0755, true);
                if (file_exists($storedSource)) {
                    @copy($storedSource, $dest);
                }
            }
        } catch (\Throwable) {
        }

        AuditLog::log('UPDATE_SCHOOL_LOGO', 'SchoolSetting', null, null, ['path' => $path]);

        return back()->with('success', 'Logo aplikasi berhasil diperbarui dan kini menjadi ikon resmi sistem.');
    }

    /**
     * Hapus / Reset Logo Aplikasi
     */
    public function destroyLogo(): RedirectResponse
    {
        $oldLogo = SchoolSetting::get('school_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        if ($oldLogo) {
            @unlink(public_path('storage/'.$oldLogo));
        }

        SchoolSetting::where('key', 'school_logo')->delete();

        // Synchronize with SchoolIdentity
        try {
            SchoolIdentity::current()->update(['logo_path' => null]);
        } catch (\Throwable) {
        }

        AuditLog::log('DELETE_SCHOOL_LOGO', 'SchoolSetting');

        return back()->with('success', 'Logo aplikasi berhasil direset ke tampilan default.');
    }

    /**
     * Upload KOP Surat Resmi
     */
    public function updateKop(Request $request): RedirectResponse
    {
        $request->validate([
            'kop' => ['required', 'file', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
        ]);

        $file = $request->file('kop');
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'kop_'.time().'.'.$extension;

        // Delete old KOP if exists
        $oldKop = SchoolSetting::get('school_kop');
        if ($oldKop && Storage::disk('public')->exists($oldKop)) {
            Storage::disk('public')->delete($oldKop);
        }
        if ($oldKop) {
            @unlink(public_path('storage/'.$oldKop));
        }

        $path = $file->storeAs('branding', $filename, 'public');
        SchoolSetting::set('school_kop', $path, 'branding', 'Path KOP surat resmi sekolah');
        SchoolSetting::set('kop_surat_image', $path, 'branding', 'Path KOP surat resmi dokumen cetak');
        SchoolSetting::set('kop_image', $path, 'branding', 'Path KOP surat resmi');

        // Synchronize with SchoolIdentity
        try {
            SchoolIdentity::current()->update(['header_image_path' => $path]);
        } catch (\Throwable) {
        }

        // Mirror directly to public/storage
        try {
            $dest = public_path('storage/'.$path);
            $storedSource = Storage::disk('public')->path($path);
            if (! is_link(public_path('storage'))) {
                @mkdir(dirname($dest), 0755, true);
                if (file_exists($storedSource)) {
                    @copy($storedSource, $dest);
                }
            }
            self::generateDarkModeKopVariants($storedSource);
        } catch (\Throwable) {
        }

        AuditLog::log('UPDATE_SCHOOL_KOP', 'SchoolSetting', null, null, ['path' => $path]);

        return back()->with('success', 'KOP Surat resmi berhasil diunggah dan otomatis diterapkan pada seluruh cetak dokumen.');
    }

    /**
     * Hapus / Reset KOP Surat
     */
    public function destroyKop(): RedirectResponse
    {
        $oldKop = SchoolSetting::get('school_kop');
        if ($oldKop && Storage::disk('public')->exists($oldKop)) {
            Storage::disk('public')->delete($oldKop);
        }
        if ($oldKop) {
            @unlink(public_path('storage/'.$oldKop));
        }

        SchoolSetting::whereIn('key', ['school_kop', 'kop_surat_image', 'kop_image'])->delete();

        // Synchronize with SchoolIdentity
        try {
            SchoolIdentity::current()->update(['header_image_path' => null]);
        } catch (\Throwable) {
        }

        AuditLog::log('DELETE_SCHOOL_KOP', 'SchoolSetting');

        return back()->with('success', 'KOP surat berhasil direset ke format teks resmi standar Jawa Barat.');
    }

    /**
     * Otomatis membuat varian KOP mode gelap transparan (tanpa background putih)
     */
    public static function generateDarkModeKopVariants(string $sourcePath): void
    {
        if (! file_exists($sourcePath) || ! extension_loaded('gd')) {
            return;
        }

        try {
            $info = @getimagesize($sourcePath);
            if (! $info) {
                return;
            }

            $src = match ($info[2]) {
                IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
                default => null,
            };

            if (! $src) {
                return;
            }

            $w = imagesx($src);
            $h = imagesy($src);

            $dstDark = imagecreatetruecolor($w, $h);
            imagealphablending($dstDark, false);
            imagesavealpha($dstDark, true);
            $transparent = imagecolorallocatealpha($dstDark, 0, 0, 0, 127);
            imagefill($dstDark, 0, 0, $transparent);

            $minContentX = $w;
            $maxContentX = 0;
            $minContentY = $h;
            $maxContentY = 0;

            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $rgb = imagecolorat($src, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

                    if ($r > 240 && $g > 240 && $b > 240) {
                        imagesetpixel($dstDark, $x, $y, $transparent);

                        continue;
                    }

                    if ($x < $minContentX) {
                        $minContentX = $x;
                    }
                    if ($x > $maxContentX) {
                        $maxContentX = $x;
                    }
                    if ($y < $minContentY) {
                        $minContentY = $y;
                    }
                    if ($y > $maxContentY) {
                        $maxContentY = $y;
                    }

                    // Logo di sisi kiri tetap mempertahankan warna aslinya
                    if ($x < ($w * 0.35)) {
                        $alpha = $brightness > 220 ? (int) min(127, max(0, round((($brightness - 220) / 35) * 127))) : 0;
                        $col = imagecolorallocatealpha($dstDark, $r, $g, $b, $alpha);
                        imagesetpixel($dstDark, $x, $y, $col);
                    } else {
                        // Teks dan garis hitam diubah menjadi putih bersih (#FFFFFF) anti-alias
                        if ($brightness > 230) {
                            imagesetpixel($dstDark, $x, $y, $transparent);
                        } else {
                            $alpha = (int) min(127, max(0, round(($brightness / 230) * 127)));
                            $isBlueLink = ($b > $r + 30 && $b > $g + 15);
                            $col = $isBlueLink
                                ? imagecolorallocatealpha($dstDark, 147, 197, 253, $alpha)
                                : imagecolorallocatealpha($dstDark, 255, 255, 255, $alpha);
                            imagesetpixel($dstDark, $x, $y, $col);
                        }
                    }
                }
            }

            @imagepng($dstDark, public_path('storage/branding/kop_dark_mode.png'));
            @imagepng($dstDark, storage_path('app/public/branding/kop_dark_mode.png'));

            // Varian kompak tanpa whitespace kosong di kiri/kanan
            if ($maxContentX > $minContentX && $maxContentY > $minContentY) {
                $cropW = $maxContentX - $minContentX + 1;
                $cropH = $maxContentY - $minContentY + 1;
                $dstCompact = imagecreatetruecolor($cropW, $cropH);
                imagealphablending($dstCompact, false);
                imagesavealpha($dstCompact, true);
                imagefill($dstCompact, 0, 0, $transparent);
                imagecopy($dstCompact, $dstDark, 0, 0, $minContentX, $minContentY, $cropW, $cropH);

                @imagepng($dstCompact, public_path('storage/branding/kop_dark_compact.png'));
                @imagepng($dstCompact, storage_path('app/public/branding/kop_dark_compact.png'));
                imagedestroy($dstCompact);
            }

            imagedestroy($src);
            imagedestroy($dstDark);
        } catch (\Throwable) {
        }
    }

    /**
     * Identitas Sekolah Lengkap
     */
    public function schoolProfile(): View
    {
        $profile = [
            'school_name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'school_npsn' => SchoolSetting::get('school_npsn', '20539123'),
            'school_nss' => SchoolSetting::get('school_nss', '301051408001'),
            'school_accreditation' => SchoolSetting::get('school_accreditation', 'A (Unggul)'),
            'headmaster_name' => SchoolSetting::get('headmaster_name', 'Drs. H. Bambang Subagyo, M.Pd.'),
            'headmaster_nip' => SchoolSetting::get('headmaster_nip', '196803121994121002'),
            'school_address' => SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat'),
            'school_phone' => SchoolSetting::get('school_phone', '(0266) 123456'),
            'school_email' => SchoolSetting::get('school_email', 'sman1lengkong@gmail.com'),
            'school_website' => SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id'),
            'school_education_form' => SchoolSetting::get('school_education_form', 'Sekolah Menengah Atas (SMA)'),
        ];

        return view('master.school_profile', compact('profile'));
    }

    /**
     * Update Identitas Sekolah
     */
    public function updateSchoolProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_npsn' => ['required', 'string', 'max:20'],
            'school_nss' => ['nullable', 'string', 'max:30'],
            'school_accreditation' => ['required', 'string', 'max:50'],
            'school_education_form' => ['nullable', 'string', 'max:100'],
            'headmaster_name' => ['required', 'string', 'max:255'],
            'headmaster_nip' => ['required', 'string', 'max:50'],
            'school_address' => ['required', 'string', 'max:500'],
            'school_phone' => ['nullable', 'string', 'max:50'],
            'school_email' => ['required', 'email', 'max:100'],
            'school_website' => ['nullable', 'url', 'max:100'],
        ]);

        foreach ($validated as $key => $val) {
            SchoolSetting::set($key, $val, 'general');
        }

        AuditLog::log('UPDATE_SCHOOL_PROFILE', 'SchoolSetting', null, null, $validated);

        return back()->with('success', 'Identitas resmi SMA Negeri 1 Lengkong berhasil diperbarui.');
    }

    /**
     * Data Kelas
     */
    public function classes(): View
    {
        $classes = SchoolClass::with(['homeroomTeacher', 'academicYear', 'students'])->get();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('master.classes', compact('classes', 'teachers', 'academicYears'));
    }

    /**
     * Simpan Data Kelas Baru
     */
    public function storeClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade' => ['required', 'string', 'max:10'],
            'major' => ['nullable', 'string', 'max:50'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $class = SchoolClass::create($validated);
        AuditLog::log('CREATE_CLASS', 'SchoolClass', $class->id);

        return back()->with('success', 'Data kelas '.$class->name.' berhasil ditambahkan.');
    }

    /**
     * Update Data Kelas
     */
    public function updateClass(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade' => ['required', 'string', 'max:10'],
            'major' => ['nullable', 'string', 'max:50'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $schoolClass->update($validated);
        AuditLog::log('UPDATE_CLASS', 'SchoolClass', $schoolClass->id);

        return back()->with('success', 'Data kelas '.$schoolClass->name.' berhasil diperbarui.');
    }

    /**
     * Hapus Data Kelas
     */
    public function destroyClass(SchoolClass $schoolClass): RedirectResponse
    {
        $name = $schoolClass->name;
        $schoolClass->delete();
        AuditLog::log('DELETE_CLASS', 'SchoolClass', $schoolClass->id);

        return back()->with('success', 'Data kelas '.$name.' berhasil dihapus.');
    }

    /**
     * Data Siswa (Dengan Live Search & Filter)
     */
    public function students(Request $request): View
    {
        $classId = $request->input('class_id');
        $search = $request->input('search');
        $academicYearId = $request->input('academic_year_id', AcademicYear::where('is_active', true)->value('id'));

        $classes = SchoolClass::all();
        $academicYears = AcademicYear::all();
        $extracurriculars = Extracurricular::where('is_active', true)->orderBy('name')->get();

        $query = Student::with(['schoolClass', 'academicYear', 'user', 'extracurriculars']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('initials', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('rfid_uid', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('master.students', compact('students', 'classes', 'academicYears', 'classId', 'search', 'academicYearId', 'extracurriculars'));
    }

    /**
     * Sinkronisasi data identitas siswa dari file Dapodik
     */
    public function syncDapodikIdentity(): RedirectResponse
    {
        try {
            Artisan::call('student:sync-dapodik-identity');
            $output = Artisan::output();

            return back()->with('success', 'Sinkronisasi identitas siswa Dapodik berhasil dijalankan! '.trim($output));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal sinkronisasi data Dapodik: '.$e->getMessage());
        }
    }

    /**
     * Simpan Data Siswa Baru
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
            'nisn' => ['required', 'string', 'max:20', 'unique:students,nisn'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'rfid_uid' => ['nullable', 'string', 'max:50', 'unique:students,rfid_uid'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'nik' => ['nullable', 'string', 'max:30'],
            'religion' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_secretary' => ['nullable', 'boolean'],
            'is_extra_secretary' => ['nullable', 'boolean'],
            'is_adm_web' => ['nullable', 'boolean'],
            'extra_secretary_id' => ['nullable', 'exists:extracurriculars,id'],
            'extracurricular_ids' => ['nullable', 'array'],
            'extracurricular_ids.*' => ['exists:extracurriculars,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $roles = ['siswa'];
        if (! empty($validated['is_secretary'])) {
            $roles[] = 'sekretaris_kelas';
        }
        if (! empty($validated['is_extra_secretary'])) {
            $roles[] = 'sekretaris_ekstra';
        }
        if (! empty($validated['is_adm_web'])) {
            $roles[] = 'adm_web';
        }

        // Buat Akun Pengguna Siswa jika belum ada (Username & Password otomatis NISN)
        $primaryRole = in_array('adm_web', $roles, true)
            ? 'adm_web'
            : (in_array('sekretaris_kelas', $roles, true)
                ? 'sekretaris_kelas'
                : (in_array('sekretaris_ekstra', $roles, true) ? 'sekretaris_ekstra' : 'siswa'));

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['nisn'].'@siswa.sman1lengkong.sch.id',
            'username' => $validated['nisn'],
            'password' => Hash::make($validated['nisn']),
            'avatar' => $validated['photo'] ?? null,
            'role' => $primaryRole,
            'roles' => $roles,
            'is_active' => true,
        ]);

        $roleModels = Role::whereIn('name', $roles)->get();
        if ($roleModels->isNotEmpty()) {
            $user->roles()->sync($roleModels->pluck('id'));
            $primaryRoleModel = $roleModels->firstWhere('name', $primaryRole) ?? $roleModels->first();
            if ($primaryRoleModel) {
                $user->updateQuietly(['active_role_id' => $primaryRoleModel->id]);
            }
        }

        $extraSecretaryId = $validated['extra_secretary_id'] ?? null;
        $extracurricularIds = $validated['extracurricular_ids'] ?? [];
        unset($validated['is_secretary'], $validated['is_extra_secretary'], $validated['is_adm_web'], $validated['extra_secretary_id'], $validated['extracurricular_ids']);

        $validated['user_id'] = $user->id;
        $validated['is_active'] = true;

        $student = Student::create($validated);

        $extraSyncData = [];
        foreach ($extracurricularIds as $exId) {
            $extraSyncData[$exId] = [
                'role' => 'Anggota',
                'joined_at' => now(),
            ];
        }

        if (! empty($request->input('is_extra_secretary')) && $extraSecretaryId) {
            $extraSyncData[$extraSecretaryId] = [
                'role' => 'Sekretaris',
                'joined_at' => now(),
            ];
        }

        if (! empty($extraSyncData)) {
            $student->extracurriculars()->sync($extraSyncData);
        }

        AuditLog::log('CREATE_STUDENT', 'Student', $student->id);

        return back()->with('success', 'Data siswa '.$student->name.' berhasil ditambahkan dengan akun login NISN: '.$student->nisn);
    }

    /**
     * Update Data Siswa
     */
    public function updateStudent(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($student->id)],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($student->id)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'rfid_uid' => ['nullable', 'string', 'max:50', Rule::unique('students', 'rfid_uid')->ignore($student->id)],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'nik' => ['nullable', 'string', 'max:30'],
            'religion' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
            'is_secretary' => ['nullable', 'boolean'],
            'is_extra_secretary' => ['nullable', 'boolean'],
            'is_adm_web' => ['nullable', 'boolean'],
            'extra_secretary_id' => ['nullable', 'exists:extracurriculars,id'],
            'extracurricular_ids' => ['nullable', 'array'],
            'extracurricular_ids.*' => ['exists:extracurriculars,id'],
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $isExtraSec = ! empty($validated['is_extra_secretary']);
        $extraSecretaryId = $validated['extra_secretary_id'] ?? null;

        // Kelola multi-ekstrakurikuler jika field extracurricular_ids dikirim
        if ($request->has('extracurricular_ids')) {
            $submittedExtraIds = $validated['extracurricular_ids'] ?? [];
            if ($isExtraSec && $extraSecretaryId && ! in_array($extraSecretaryId, $submittedExtraIds)) {
                $submittedExtraIds[] = (int) $extraSecretaryId;
            }

            $existingPivots = $student->extracurriculars->keyBy('id');
            $syncData = [];
            foreach ($submittedExtraIds as $eid) {
                $existing = $existingPivots->get($eid);
                $existingRole = $existing ? ($existing->pivot->role ?? 'Anggota') : 'Anggota';
                if ($isExtraSec && $eid == $extraSecretaryId) {
                    $role = 'Sekretaris';
                } elseif (in_array($existingRole, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true) && (! $isExtraSec || $eid != $extraSecretaryId)) {
                    $role = 'Anggota';
                } else {
                    $role = $existingRole;
                }
                $syncData[$eid] = [
                    'role' => $role,
                    'joined_at' => $existing?->pivot?->joined_at ?? now(),
                ];
            }
            $student->extracurriculars()->sync($syncData);
        } else {
            if ($isExtraSec && $extraSecretaryId) {
                foreach ($student->extracurriculars as $existingEx) {
                    if ($existingEx->id != $extraSecretaryId && in_array($existingEx->pivot->role, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
                        $student->extracurriculars()->updateExistingPivot($existingEx->id, ['role' => 'Anggota']);
                    }
                }
                $student->extracurriculars()->syncWithoutDetaching([
                    $extraSecretaryId => [
                        'role' => 'Sekretaris',
                        'joined_at' => now(),
                    ],
                ]);
            } elseif (! $isExtraSec) {
                foreach ($student->extracurriculars as $existingEx) {
                    if (in_array($existingEx->pivot->role, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
                        $student->extracurriculars()->updateExistingPivot($existingEx->id, ['role' => 'Anggota']);
                    }
                }
            }
        }

        unset($validated['is_secretary'], $validated['is_extra_secretary'], $validated['is_adm_web'], $validated['extra_secretary_id'], $validated['extracurricular_ids']);
        $student->update($validated);

        if ($student->user) {
            $roles = ['siswa'];
            if (! empty($request->input('is_secretary'))) {
                $roles[] = 'sekretaris_kelas';
            }
            if ($isExtraSec && ($extraSecretaryId || $student->extracurriculars()->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])->exists())) {
                $roles[] = 'sekretaris_ekstra';
            }
            if (! empty($request->input('is_adm_web'))) {
                $roles[] = 'adm_web';
            }

            $primaryRole = in_array('adm_web', $roles, true)
                ? 'adm_web'
                : (in_array('sekretaris_kelas', $roles, true)
                    ? 'sekretaris_kelas'
                    : (in_array('sekretaris_ekstra', $roles, true) ? 'sekretaris_ekstra' : 'siswa'));

            $userUpdate = [
                'name' => $validated['name'],
                'username' => $validated['nisn'],
                'role' => $primaryRole,
                'roles' => $roles,
                'is_active' => $validated['is_active'],
            ];
            if (isset($validated['photo'])) {
                $userUpdate['avatar'] = $validated['photo'];
            }
            $student->user->update($userUpdate);

            $roleModels = Role::whereIn('name', $roles)->get();
            if ($roleModels->isNotEmpty()) {
                $student->user->roles()->sync($roleModels->pluck('id'));
                $primaryRoleModel = $roleModels->firstWhere('name', $primaryRole) ?? $roleModels->first();
                if ($primaryRoleModel) {
                    $student->user->updateQuietly(['active_role_id' => $primaryRoleModel->id]);
                }
            }
        }

        AuditLog::log('UPDATE_STUDENT', 'Student', $student->id);

        return back()->with('success', 'Data siswa '.$student->name.' berhasil diperbarui.');
    }

    /**
     * Hapus Data Siswa
     */
    public function destroyStudent(Student $student): RedirectResponse
    {
        $name = $student->name;
        $student->delete();
        AuditLog::log('DELETE_STUDENT', 'Student', $student->id);

        return back()->with('success', 'Data siswa '.$name.' berhasil dihapus.');
    }

    /**
     * Rekam / Daftarkan Wajah Siswa untuk Face Recognition Attendance
     */
    public function recordStudentFace(Request $request, Student $student): JsonResponse|RedirectResponse
    {
        $request->validate([
            'face_image' => ['nullable', 'string'],
            'face_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'face_descriptor' => ['nullable', 'string'],
        ]);

        $imageFile = null;
        if ($request->hasFile('face_photo')) {
            $imageFile = $request->file('face_photo');
        } elseif ($request->filled('face_image')) {
            $imageFile = $request->input('face_image');
        }

        if (! $imageFile) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto wajah tidak ditemukan atau format tidak didukung.',
                ], 422);
            }

            return back()->with('error', 'Foto wajah tidak ditemukan atau format tidak didukung.');
        }

        try {
            $storedPath = $this->attendanceService->saveBase64Image($imageFile, 'faces');

            $student->update([
                'face_photo_path' => $storedPath,
                'face_descriptor' => $request->input('face_descriptor') ?: $student->face_descriptor,
                'face_registered_at' => now(),
                'photo' => $student->photo ?: $storedPath,
            ]);

            if ($student->user && empty($student->user->avatar)) {
                $student->user->update(['avatar' => $storedPath]);
            }

            AuditLog::log('ENROLL_STUDENT_FACE', 'Student', $student->id, null, [
                'nisn' => $student->nisn,
                'photo_path' => $storedPath,
                'has_descriptor' => ! empty($request->input('face_descriptor')),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Wajah siswa '.$student->name.' berhasil direkam dan didaftarkan sebagai dasar absen Face Recognition.',
                    'face_photo_url' => $student->face_photo_url,
                    'face_photo_path' => $storedPath,
                    'face_registered_at' => $student->face_registered_at?->format('d/m/Y H:i'),
                ]);
            }

            return back()->with('success', 'Wajah siswa '.$student->name.' berhasil direkam dan didaftarkan sebagai dasar absen Face Recognition.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses foto wajah: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal memproses foto wajah: '.$e->getMessage());
        }
    }

    /**
     * Hapus Data Wajah Terdaftar Siswa
     */
    public function deleteStudentFace(Request $request, Student $student): JsonResponse|RedirectResponse
    {
        if ($student->face_photo_path && Storage::disk('public')->exists($student->face_photo_path)) {
            Storage::disk('public')->delete($student->face_photo_path);
        }

        $student->update([
            'face_photo_path' => null,
            'face_descriptor' => null,
            'face_registered_at' => null,
        ]);

        AuditLog::log('DELETE_STUDENT_FACE', 'Student', $student->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data rekaman wajah siswa '.$student->name.' berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Data rekaman wajah siswa '.$student->name.' berhasil dihapus.');
    }

    /**
     * Data Guru & Tenaga Kependidikan (PTK) beserta Role Akun Loginnya
     */
    public function teachers(Request $request): View
    {
        $search = $request->input('search');
        $type = $request->input('type'); // all, guru, tendik
        $status = $request->input('status'); // all, PNS, PPPK, Honorer

        $query = Teacher::with(['user.employee', 'homeroomClass', 'extracurriculars', 'ledExtracurriculars']);

        if ($type === 'guru') {
            $query->where(function ($q) {
                $q->where('employment_type', 'Guru')
                    ->orWhereHas('user.employee', fn ($eq) => $eq->where('employment_type', 'Guru'));
            });
        } elseif ($type === 'tendik') {
            $query->where(function ($q) {
                $q->where('employment_type', '!=', 'Guru')
                    ->orWhereHas('user.employee', fn ($eq) => $eq->where('employment_type', '!=', 'Guru'));
            });
        }

        if ($status) {
            if ($status === 'PPPK') {
                $query->where(function ($q) {
                    $q->where('employment_status', 'like', '%PPPK%')
                        ->orWhereHas('user.employee', fn ($eq) => $eq->where('employment_status', 'like', '%PPPK%'));
                });
            } elseif ($status === 'Honorer') {
                $query->where(function ($q) {
                    $q->whereIn('employment_status', ['GTT', 'PTT', 'Honorer', 'NON ASN'])
                        ->orWhereHas('user.employee', fn ($eq) => $eq->whereIn('employment_status', ['GTT', 'PTT', 'Honorer', 'NON ASN']));
                });
            } else {
                $query->where(function ($q) use ($status) {
                    $q->where('employment_status', $status)
                        ->orWhereHas('user.employee', fn ($eq) => $eq->where('employment_status', $status));
                });
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('rank_group', 'like', "%{$search}%")
                    ->orWhere('employment_status', 'like', "%{$search}%")
                    ->orWhere('teaching_subject', 'like', "%{$search}%")
                    ->orWhere('education_major', 'like', "%{$search}%")
                    ->orWhere('education_institution', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%"));
            });
        }

        $teachers = $query->orderBy('name')->get();
        $extracurriculars = Extracurricular::where('is_active', true)->orderBy('name')->get();

        $totalPtk = Teacher::count();
        $totalGuru = Teacher::where('employment_type', 'Guru')->count();
        $totalTendik = Teacher::where('employment_type', '!=', 'Guru')->count();

        return view('master.teachers', compact('teachers', 'search', 'type', 'status', 'extracurriculars', 'totalPtk', 'totalGuru', 'totalTendik'));
    }

    /**
     * Simpan Data Guru Baru
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:teachers,nip'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'title_prefix' => ['nullable', 'string', 'max:50'],
            'title_suffix' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'title' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'rank_group' => ['nullable', 'string', 'max:20'],
            'rank_tmt' => ['nullable', 'date'],
            'position_tmt' => ['nullable', 'date'],
            'years_of_service_years' => ['nullable', 'integer'],
            'years_of_service_months' => ['nullable', 'integer'],
            'education_level' => ['nullable', 'string', 'max:50'],
            'education_institution' => ['nullable', 'string', 'max:255'],
            'education_major' => ['nullable', 'string', 'max:255'],
            'education_grad_year' => ['nullable', 'string', 'max:10'],
            'teaching_subject' => ['nullable', 'string', 'max:100'],
            'additional_tasks' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'role' => ['required', 'string'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['nullable', 'string', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:6'],
            'extracurricular_ids' => ['nullable', 'array'],
            'extracurricular_ids.*' => ['exists:extracurriculars,id'],
        ]);

        $roles = $validated['roles'] ?? [];
        if (! in_array($validated['role'], $roles, true)) {
            array_unshift($roles, $validated['role']);
        }
        $roles = array_values(array_unique(array_filter($roles)));

        $identifier = $validated['nip'] ?: Str::slug($validated['name'], '.');
        $username = ! empty($validated['username']) ? $validated['username'] : $identifier;
        $initialPassword = ! empty($validated['password']) ? $validated['password'] : ($validated['nip'] ?: 'sman1lengkong');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($initialPassword),
            'role' => $validated['role'],
            'roles' => $roles,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        $roleModels = Role::whereIn('name', $roles)->get();
        if ($roleModels->isNotEmpty()) {
            $user->roles()->sync($roleModels->pluck('id'));
            $primaryRoleModel = $roleModels->firstWhere('name', $validated['role']) ?? $roleModels->first();
            if ($primaryRoleModel) {
                $user->updateQuietly(['active_role_id' => $primaryRoleModel->id]);
            }
        }

        $defaultTitle = match ($validated['role']) {
            'admin' => 'Administrator Sekolah',
            'staff' => 'Tenaga Kependidikan (Staf TU)',
            default => 'Guru Mata Pelajaran',
        };

        $pos = $validated['position'] ?? ($validated['title'] ?? $defaultTitle);
        $empType = $validated['employment_type'] ?? ($validated['role'] === 'staff' ? 'Tenaga Kependidikan' : 'Guru');

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'] ?: null,
            'nuptk' => $validated['nuptk'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'name' => $validated['name'],
            'title_prefix' => $validated['title_prefix'] ?? null,
            'title_suffix' => $validated['title_suffix'] ?? null,
            'gender' => $validated['gender'],
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'title' => $validated['title'] ?? $defaultTitle,
            'position' => $pos,
            'employment_type' => $empType,
            'employment_status' => $validated['employment_status'] ?? 'PNS',
            'rank_group' => $validated['rank_group'] ?? null,
            'rank_tmt' => $validated['rank_tmt'] ?? null,
            'position_tmt' => $validated['position_tmt'] ?? null,
            'years_of_service_years' => $validated['years_of_service_years'] ?? null,
            'years_of_service_months' => $validated['years_of_service_months'] ?? null,
            'education_level' => $validated['education_level'] ?? 'S1',
            'education_institution' => $validated['education_institution'] ?? null,
            'education_major' => $validated['education_major'] ?? null,
            'education_grad_year' => $validated['education_grad_year'] ?? null,
            'teaching_subject' => $validated['teaching_subject'] ?? null,
            'additional_tasks' => $validated['additional_tasks'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        // Sync with employees table
        Employee::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => $teacher->nip,
                'nuptk' => $teacher->nuptk,
                'nik' => $teacher->nik,
                'name' => $teacher->name,
                'title_prefix' => $teacher->title_prefix,
                'title_suffix' => $teacher->title_suffix,
                'gender' => $teacher->gender,
                'birth_place' => $teacher->birth_place,
                'birth_date' => $teacher->birth_date,
                'employment_type' => $teacher->employment_type ?: 'Guru',
                'employment_status' => $teacher->employment_status ?: 'PNS',
                'rank_group' => $teacher->rank_group,
                'rank_tmt' => $teacher->rank_tmt,
                'position_tmt' => $teacher->position_tmt,
                'years_of_service_years' => (int) ($teacher->years_of_service_years ?? 0),
                'years_of_service_months' => (int) ($teacher->years_of_service_months ?? 0),
                'position' => $teacher->position,
                'teaching_subject' => $teacher->teaching_subject,
                'additional_tasks' => $teacher->additional_tasks,
                'education_level' => $teacher->education_level ?: 'S1',
                'education_institution' => $teacher->education_institution,
                'education_major' => $teacher->education_major,
                'education_grad_year' => $teacher->education_grad_year,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'is_active' => true,
                'leave_balance' => 12,
            ]
        );

        if (! empty($validated['extracurricular_ids'])) {
            $teacher->extracurriculars()->sync($validated['extracurricular_ids']);
        }

        AuditLog::log('CREATE_TEACHER', 'Teacher', $teacher->id);

        return back()->with('success', 'Data pegawai '.$teacher->name.' dengan peran '.str_replace('_', ' ', $validated['role']).' berhasil ditambahkan (Username & Password: '.($validated['nip'] ?: $username).').');
    }

    /**
     * Update Data Guru & Role Akun Login
     */
    public function updateTeacher(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'title_prefix' => ['nullable', 'string', 'max:50'],
            'title_suffix' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'title' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'rank_group' => ['nullable', 'string', 'max:20'],
            'rank_tmt' => ['nullable', 'date'],
            'position_tmt' => ['nullable', 'date'],
            'years_of_service_years' => ['nullable', 'integer'],
            'years_of_service_months' => ['nullable', 'integer'],
            'education_level' => ['nullable', 'string', 'max:50'],
            'education_institution' => ['nullable', 'string', 'max:255'],
            'education_major' => ['nullable', 'string', 'max:255'],
            'education_grad_year' => ['nullable', 'string', 'max:10'],
            'teaching_subject' => ['nullable', 'string', 'max:100'],
            'additional_tasks' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'role' => ['required', 'string'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
            'extracurricular_ids' => ['nullable', 'array'],
            'extracurricular_ids.*' => ['exists:extracurriculars,id'],
        ]);

        $roles = $validated['roles'] ?? [];
        if (! in_array($validated['role'], $roles, true)) {
            array_unshift($roles, $validated['role']);
        }
        $roles = array_values(array_unique(array_filter($roles)));

        $empType = $validated['employment_type'] ?? ($teacher->employment_type ?: ($validated['role'] === 'staff' ? 'Tenaga Kependidikan' : 'Guru'));
        $pos = $validated['position'] ?? ($validated['title'] ?? $teacher->title);

        $teacher->update([
            'nip' => $validated['nip'] ?: null,
            'nuptk' => $validated['nuptk'] ?? $teacher->nuptk,
            'nik' => $validated['nik'] ?? $teacher->nik,
            'name' => $validated['name'],
            'title_prefix' => $validated['title_prefix'] ?? $teacher->title_prefix,
            'title_suffix' => $validated['title_suffix'] ?? $teacher->title_suffix,
            'gender' => $validated['gender'],
            'birth_place' => $validated['birth_place'] ?? $teacher->birth_place,
            'birth_date' => $validated['birth_date'] ?? $teacher->birth_date,
            'title' => $validated['title'] ?? $teacher->title,
            'position' => $pos,
            'employment_type' => $empType,
            'employment_status' => $validated['employment_status'] ?? ($teacher->employment_status ?: 'PNS'),
            'rank_group' => $validated['rank_group'] ?? $teacher->rank_group,
            'rank_tmt' => $validated['rank_tmt'] ?? $teacher->rank_tmt,
            'position_tmt' => $validated['position_tmt'] ?? $teacher->position_tmt,
            'years_of_service_years' => $validated['years_of_service_years'] ?? $teacher->years_of_service_years,
            'years_of_service_months' => $validated['years_of_service_months'] ?? $teacher->years_of_service_months,
            'education_level' => $validated['education_level'] ?? ($teacher->education_level ?: 'S1'),
            'education_institution' => $validated['education_institution'] ?? $teacher->education_institution,
            'education_major' => $validated['education_major'] ?? $teacher->education_major,
            'education_grad_year' => $validated['education_grad_year'] ?? $teacher->education_grad_year,
            'teaching_subject' => $validated['teaching_subject'] ?? $teacher->teaching_subject,
            'additional_tasks' => $validated['additional_tasks'] ?? $teacher->additional_tasks,
            'phone' => $validated['phone'] ?? $teacher->phone,
            'address' => $validated['address'] ?? $teacher->address,
            'is_active' => $validated['is_active'],
        ]);

        if (array_key_exists('extracurricular_ids', $validated)) {
            $teacher->extracurriculars()->sync($validated['extracurricular_ids'] ?? []);
        }

        if ($teacher->user) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'roles' => $roles,
                'phone' => $validated['phone'] ?? $teacher->user->phone,
                'is_active' => $validated['is_active'],
            ];

            if (! empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }

            $teacher->user->update($userUpdate);

            $roleModels = Role::whereIn('name', $roles)->get();
            if ($roleModels->isNotEmpty()) {
                $teacher->user->roles()->sync($roleModels->pluck('id'));
                $primaryRoleModel = $roleModels->firstWhere('name', $validated['role']) ?? $roleModels->first();
                if ($primaryRoleModel) {
                    $teacher->user->updateQuietly(['active_role_id' => $primaryRoleModel->id]);
                }
            }

            // Sync employee record
            Employee::updateOrCreate(
                ['user_id' => $teacher->user->id],
                [
                    'nip' => $teacher->nip,
                    'nuptk' => $teacher->nuptk,
                    'nik' => $teacher->nik,
                    'name' => $teacher->name,
                    'title_prefix' => $teacher->title_prefix,
                    'title_suffix' => $teacher->title_suffix,
                    'gender' => $teacher->gender,
                    'birth_place' => $teacher->birth_place,
                    'birth_date' => $teacher->birth_date,
                    'employment_type' => $teacher->employment_type ?: 'Guru',
                    'employment_status' => $teacher->employment_status ?: 'PNS',
                    'rank_group' => $teacher->rank_group,
                    'rank_tmt' => $teacher->rank_tmt,
                    'position_tmt' => $teacher->position_tmt,
                    'years_of_service_years' => (int) ($teacher->years_of_service_years ?? 0),
                    'years_of_service_months' => (int) ($teacher->years_of_service_months ?? 0),
                    'position' => $teacher->position,
                    'teaching_subject' => $teacher->teaching_subject,
                    'additional_tasks' => $teacher->additional_tasks,
                    'education_level' => $teacher->education_level ?: 'S1',
                    'education_institution' => $teacher->education_institution,
                    'education_major' => $teacher->education_major,
                    'education_grad_year' => $teacher->education_grad_year,
                    'phone' => $teacher->phone,
                    'address' => $teacher->address,
                    'is_active' => $teacher->is_active,
                    'leave_balance' => 12,
                ]
            );
        }

        AuditLog::log('UPDATE_TEACHER', 'Teacher', $teacher->id);

        return back()->with('success', 'Data guru/pegawai '.$teacher->name.' & kelengkapan kepegawaian berhasil diperbarui.');
    }

    /**
     * Master Data Ekstrakurikuler
     */
    public function extracurriculars(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Extracurricular::with(['teacher.user', 'teachers.user', 'students.schoolClass'])
            ->withCount('students');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('teacher', fn ($tq) => $tq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $extracurriculars = $query->orderBy('name')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $categories = Extracurricular::distinct()->pluck('category')->filter()->values();
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('master.extracurriculars', compact('extracurriculars', 'teachers', 'categories', 'search', 'category', 'allStudents'));
    }

    /**
     * Simpan Ekstrakurikuler Baru
     */
    public function storeExtracurricular(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:extracurriculars,code'],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'schedule_day' => ['nullable', 'string', 'max:150'],
            'schedule_days' => ['nullable', 'array'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'location' => ['nullable', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ]);

        $daysInput = $request->input('schedule_days') ?? $request->input('schedule_day');
        if (is_array($daysInput)) {
            $validated['schedule_day'] = implode(', ', array_filter($daysInput));
        }

        $teacherIds = $validated['teacher_ids'] ?? [];
        if (! empty($validated['teacher_id']) && ! in_array($validated['teacher_id'], $teacherIds)) {
            array_unshift($teacherIds, $validated['teacher_id']);
        }
        $teacherIds = array_values(array_unique(array_filter($teacherIds)));

        $validated['teacher_id'] = $teacherIds[0] ?? null;
        unset($validated['teacher_ids'], $validated['schedule_days']);

        $extra = Extracurricular::create($validated);

        if (! empty($teacherIds)) {
            $syncData = [];
            foreach ($teacherIds as $idx => $tid) {
                $syncData[$tid] = ['role' => ($idx === 0 ? 'Pembina Utama' : 'Pembina')];
            }
            $extra->teachers()->sync($syncData);

            foreach ($teacherIds as $tid) {
                $teacher = Teacher::with('user')->find($tid);
                if ($teacher && $teacher->user) {
                    $u = $teacher->user;
                    $roles = is_array($u->roles) ? $u->roles : [];
                    if (! in_array('pembina_ekstra', $roles, true)) {
                        $roles[] = 'pembina_ekstra';
                        $u->roles = array_values(array_unique($roles));
                        $u->save();
                    }
                }
            }
        }

        AuditLog::log('CREATE_EXTRACURRICULAR', 'Extracurricular', $extra->id);

        return back()->with('success', "Ekstrakurikuler {$extra->name} berhasil ditambahkan.");
    }

    /**
     * Update Data Ekstrakurikuler
     */
    public function updateExtracurricular(Request $request, int $id): RedirectResponse
    {
        $extra = Extracurricular::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('extracurriculars', 'code')->ignore($extra->id)],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'schedule_day' => ['nullable', 'string', 'max:150'],
            'schedule_days' => ['nullable', 'array'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'location' => ['nullable', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ]);

        $daysInput = $request->input('schedule_days') ?? $request->input('schedule_day');
        if (is_array($daysInput)) {
            $validated['schedule_day'] = implode(', ', array_filter($daysInput));
        }

        $teacherIds = $validated['teacher_ids'] ?? [];
        if (! empty($validated['teacher_id']) && ! in_array($validated['teacher_id'], $teacherIds)) {
            array_unshift($teacherIds, $validated['teacher_id']);
        }
        $teacherIds = array_values(array_unique(array_filter($teacherIds)));

        $validated['teacher_id'] = $teacherIds[0] ?? null;
        unset($validated['teacher_ids'], $validated['schedule_days']);

        $extra->update($validated);

        $syncData = [];
        foreach ($teacherIds as $idx => $tid) {
            $syncData[$tid] = ['role' => ($idx === 0 ? 'Pembina Utama' : 'Pembina')];
        }
        $extra->teachers()->sync($syncData);

        foreach ($teacherIds as $tid) {
            $teacher = Teacher::with('user')->find($tid);
            if ($teacher && $teacher->user) {
                $u = $teacher->user;
                $roles = is_array($u->roles) ? $u->roles : [];
                if (! in_array('pembina_ekstra', $roles, true)) {
                    $roles[] = 'pembina_ekstra';
                    $u->roles = array_values(array_unique($roles));
                    $u->save();
                }
            }
        }

        AuditLog::log('UPDATE_EXTRACURRICULAR', 'Extracurricular', $extra->id);

        return back()->with('success', "Data ekstrakurikuler {$extra->name} berhasil diperbarui.");
    }

    /**
     * Hapus Ekstrakurikuler
     */
    public function destroyExtracurricular(int $id): RedirectResponse
    {
        $extra = Extracurricular::findOrFail($id);
        $name = $extra->name;
        $extra->delete();

        AuditLog::log('DELETE_EXTRACURRICULAR', 'Extracurricular', $id);

        return back()->with('success', "Ekstrakurikuler {$name} berhasil dihapus.");
    }

    /**
     * Tambah Anggota Siswa ke Ekstrakurikuler
     */
    public function addExtracurricularMember(Request $request, int $id): RedirectResponse
    {
        $extra = Extracurricular::findOrFail($id);
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'role' => ['nullable', 'string', 'max:50'],
        ]);

        $role = $validated['role'] ?? 'Anggota';

        $extra->students()->syncWithoutDetaching([
            $validated['student_id'] => [
                'role' => $role,
                'joined_at' => now()->toDateString(),
            ],
        ]);

        $targetStudent = Student::with('user')->find($validated['student_id']);
        if ($targetStudent && $targetStudent->user) {
            $studentUser = $targetStudent->user;
            $roles = $studentUser->roles ?? ['siswa'];
            if (in_array($role, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
                if (! in_array('sekretaris_ekstra', $roles, true)) {
                    $roles[] = 'sekretaris_ekstra';
                    $studentUser->roles = array_values(array_unique($roles));
                    if ($studentUser->role === 'siswa') {
                        $studentUser->role = 'sekretaris_ekstra';
                    }
                    $studentUser->save();
                }
            }
        }

        AuditLog::log('ADD_EXTRACURRICULAR_MEMBER', 'Extracurricular', $extra->id, null, [
            'student_id' => $validated['student_id'],
            'role' => $role,
        ]);

        $studentName = $targetStudent?->name ?? 'Siswa';

        return redirect()->route('master.extracurriculars', ['open_members' => $extra->id])
            ->with('success', "{$studentName} berhasil ditambahkan sebagai {$role} {$extra->name}.");
    }

    /**
     * Hapus Anggota Siswa dari Ekstrakurikuler
     */
    public function removeExtracurricularMember(int $id, int $studentId): RedirectResponse
    {
        $extra = Extracurricular::findOrFail($id);
        $extra->students()->detach($studentId);

        $targetStudent = Student::with('user')->find($studentId);
        if ($targetStudent && $targetStudent->user) {
            $isStillSecretaryInOther = $targetStudent->extracurriculars()
                ->where('extracurriculars.id', '!=', $extra->id)
                ->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])
                ->exists();

            if (! $isStillSecretaryInOther) {
                $studentUser = $targetStudent->user;
                $roles = array_values(array_diff($studentUser->roles ?? ['siswa'], ['sekretaris_ekstra']));
                $studentUser->roles = $roles;
                if ($studentUser->role === 'sekretaris_ekstra') {
                    $studentUser->role = 'siswa';
                }
                $studentUser->save();
            }
        }

        AuditLog::log('REMOVE_EXTRACURRICULAR_MEMBER', 'Extracurricular', $extra->id, null, [
            'student_id' => $studentId,
        ]);

        return redirect()->route('master.extracurriculars', ['open_members' => $extra->id])
            ->with('success', "Anggota berhasil dikeluarkan dari {$extra->name}.");
    }

    /**
     * Perbarui Peran / Jabatan Anggota Ekstrakurikuler (Sekretaris / Anggota)
     */
    public function updateExtracurricularMemberRole(Request $request, int $id, int $studentId): RedirectResponse
    {
        $extra = Extracurricular::findOrFail($id);
        $validated = $request->validate([
            'role' => ['required', 'string', 'max:50'],
        ]);

        $extra->students()->updateExistingPivot($studentId, [
            'role' => $validated['role'],
        ]);

        $targetStudent = Student::with('user')->find($studentId);
        if ($targetStudent && $targetStudent->user) {
            $studentUser = $targetStudent->user;
            $roles = $studentUser->roles ?? ['siswa'];
            if (in_array($validated['role'], ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
                if (! in_array('sekretaris_ekstra', $roles, true)) {
                    $roles[] = 'sekretaris_ekstra';
                    $studentUser->roles = array_values(array_unique($roles));
                    if ($studentUser->role === 'siswa') {
                        $studentUser->role = 'sekretaris_ekstra';
                    }
                    $studentUser->save();
                }
            } else {
                $isStillSecretaryInOther = $targetStudent->extracurriculars()
                    ->where('extracurriculars.id', '!=', $extra->id)
                    ->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])
                    ->exists();

                if (! $isStillSecretaryInOther) {
                    $roles = array_values(array_diff($roles, ['sekretaris_ekstra']));
                    $studentUser->roles = $roles;
                    if ($studentUser->role === 'sekretaris_ekstra') {
                        $studentUser->role = 'siswa';
                    }
                    $studentUser->save();
                }
            }
        }

        AuditLog::log('UPDATE_EXTRACURRICULAR_MEMBER_ROLE', 'Extracurricular', $extra->id, null, [
            'student_id' => $studentId,
            'role' => $validated['role'],
        ]);

        return redirect()->route('master.extracurriculars', ['open_members' => $extra->id])
            ->with('success', 'Peran anggota ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Hapus Data Guru
     */
    public function destroyTeacher(Teacher $teacher): RedirectResponse
    {
        $name = $teacher->name;
        $teacher->delete();
        AuditLog::log('DELETE_TEACHER', 'Teacher', $teacher->id);

        return back()->with('success', 'Data guru '.$name.' berhasil dihapus.');
    }

    /**
     * Tahun Pelajaran
     */
    public function academicYears(): View
    {
        $years = AcademicYear::withCount('classes')->orderByDesc('start_date')->get();

        return view('master.academic_years', compact('years'));
    }

    /**
     * Simpan Tahun Pelajaran Baru
     */
    public function storeAcademicYear(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'semester' => ['nullable', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = (bool) ($validated['is_active'] ?? false);

        if ($isActive) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $ay = AcademicYear::create([
            'name' => $validated['name'],
            'semester' => $validated['semester'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $isActive,
        ]);

        AuditLog::log('CREATE_ACADEMIC_YEAR', 'AcademicYear', $ay->id);

        return back()->with('success', 'Tahun Pelajaran '.$ay->name.' berhasil ditambahkan.');
    }

    /**
     * Set Tahun Pelajaran Aktif
     */
    public function setActiveAcademicYear(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        AuditLog::log('SET_ACTIVE_ACADEMIC_YEAR', 'AcademicYear', $academicYear->id);

        return back()->with('success', 'Tahun Pelajaran '.$academicYear->name.' kini menjadi Tahun Pelajaran Aktif.');
    }

    /**
     * Update Tahun Pelajaran
     */
    public function updateAcademicYear(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'semester' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active');
        if ($isActive) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update([
            'name' => $validated['name'],
            'semester' => $validated['semester'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $isActive,
        ]);

        AuditLog::log('UPDATE_ACADEMIC_YEAR', 'AcademicYear', $academicYear->id);

        return back()->with('success', 'Tahun Pelajaran '.$academicYear->name.' berhasil diperbarui.');
    }

    /**
     * Hapus Tahun Pelajaran
     */
    public function destroyAcademicYear(AcademicYear $academicYear): RedirectResponse
    {
        $name = $academicYear->name;
        $academicYear->delete();
        AuditLog::log('DELETE_ACADEMIC_YEAR', 'AcademicYear', $academicYear->id);

        return back()->with('success', 'Tahun Pelajaran '.$name.' berhasil dihapus.');
    }

    /**
     * Pengaturan Sistem & Geofence
     */
    public function settings(): View
    {
        $settings = SchoolSetting::all()->groupBy('group');
        $location = AttendanceLocation::primary();
        $devices = AttendanceDevice::orderBy('device_code')->get();
        $rfidDevices = AttendanceDevice::rfid()->orderBy('device_code')->get();
        $barcodeDevices = AttendanceDevice::barcode()->orderBy('device_code')->get();
        $faceDevices = AttendanceDevice::faceRecognition()->orderBy('device_code')->get();
        $qrDevices = $barcodeDevices;
        $academicYears = AcademicYear::all();
        $classes = SchoolClass::with(['homeroomTeacher', 'academicYear'])->get();
        $teachers = Teacher::all();
        $extracurriculars = Extracurricular::with(['teacher', 'teachers'])->orderBy('name')->get();

        return view('master.settings', compact('settings', 'location', 'devices', 'rfidDevices', 'barcodeDevices', 'faceDevices', 'qrDevices', 'academicYears', 'classes', 'teachers', 'extracurriculars'));
    }

    /**
     * Update Pengaturan Sistem
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token']);

        // Explicit attendance methods toggles (5 Unified Methods: Selfie, Barcode, RFID, Face Recognition, Manual)
        $methodKeys = [
            'enable_attendance_selfie',
            'enable_attendance_barcode',
            'enable_attendance_qr',
            'enable_attendance_rfid',
            'enable_attendance_face_recognition',
            'enable_attendance_manual',
        ];
        foreach ($methodKeys as $key) {
            $inputs[$key] = $request->has($key) ? '1' : '0';
        }

        // Toggle Fitur Radius Geofence (Aktif vs Nonaktif/Bebas Radius)
        if ($request->has('enable_geofence_radius')) {
            $inputs['enable_geofence_radius'] = $request->input('enable_geofence_radius') == '1' ? '1' : '0';
        }

        // Keep barcode and legacy QR synchronized
        if ($request->has('enable_attendance_barcode')) {
            $inputs['enable_attendance_qr'] = $inputs['enable_attendance_barcode'];
        } elseif ($request->has('enable_attendance_qr')) {
            $inputs['enable_attendance_barcode'] = $inputs['enable_attendance_qr'];
        }

        // Explicit daily active days toggles (Senin s.d. Sabtu)
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        foreach ($days as $day) {
            $inputs["attendance_{$day}_is_active"] = $request->has("attendance_{$day}_is_active") ? '1' : '0';
        }

        // Keep legacy master keys synchronized with Senin settings
        if ($request->filled('attendance_senin_in_start')) {
            $inputs['attendance_start_time'] = (string) $request->attendance_senin_in_start;
        }
        if ($request->filled('attendance_senin_in_on_time')) {
            $inputs['attendance_checkin_time'] = (string) $request->attendance_senin_in_on_time;
        }
        if ($request->filled('attendance_senin_in_late_cutoff')) {
            $inputs['attendance_late_cutoff_time'] = (string) $request->attendance_senin_in_late_cutoff;
        }
        if ($request->filled('attendance_senin_out_end')) {
            $inputs['attendance_end_time'] = (string) $request->attendance_senin_out_end;
        }

        // Jam & Hari Operasional Presensi Keagamaan & Ekstrakurikuler
        $worshipExtraDaysKeys = [
            'worship_dhuha_days' => ['kamis', 'jumat'],
            'worship_dzuhur_jumat_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
            'worship_keputrian_days' => ['jumat'],
            'extra_attendance_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'],
        ];

        foreach ($worshipExtraDaysKeys as $key => $defaultDays) {
            if ($request->has($key)) {
                $rawDays = $request->input($key);
                $daysArray = is_array($rawDays) ? $rawDays : explode(',', (string) $rawDays);
                $cleanDays = array_values(array_filter(array_map('strtolower', array_map('trim', $daysArray))));
                $inputs[$key] = implode(',', $cleanDays);
            } elseif ($request->has("_present_{$key}")) {
                // Checkbox group rendered but user unchecked all
                $inputs[$key] = '';
            }
        }

        // Synchronize legacy/alias keys for time settings
        if ($request->filled('worship_dhuha_start')) {
            $inputs['worship_dhuha_start_time'] = (string) $request->worship_dhuha_start;
        }
        if ($request->filled('worship_dhuha_end')) {
            $inputs['worship_dhuha_end_time'] = (string) $request->worship_dhuha_end;
        }
        if ($request->filled('worship_dzuhur_jumat_start')) {
            $inputs['worship_dzuhur_start_time'] = (string) $request->worship_dzuhur_jumat_start;
        }
        if ($request->filled('worship_dzuhur_jumat_end')) {
            $inputs['worship_dzuhur_end_time'] = (string) $request->worship_dzuhur_jumat_end;
        }
        if ($request->filled('worship_keputrian_start')) {
            $inputs['worship_keputrian_start_time'] = (string) $request->worship_keputrian_start;
        }
        if ($request->filled('worship_keputrian_end')) {
            $inputs['worship_keputrian_end_time'] = (string) $request->worship_keputrian_end;
        }
        if ($request->filled('extra_attendance_start')) {
            $inputs['extracurricular_start_time'] = (string) $request->extra_attendance_start;
        }
        if ($request->filled('extra_attendance_end')) {
            $inputs['extracurricular_end_time'] = (string) $request->extra_attendance_end;
        }

        // Pembaruan Jadwal Khusus Per Jenis Ekstrakurikuler
        if ($request->has('extra_schedules') && is_array($request->input('extra_schedules'))) {
            foreach ($request->input('extra_schedules') as $extraId => $extraData) {
                $extraModel = Extracurricular::find($extraId);
                if (! $extraModel) {
                    continue;
                }

                $useCustom = ! empty($extraData['use_custom']);
                if ($useCustom) {
                    $rawDays = $extraData['days'] ?? [];
                    $daysArray = is_array($rawDays) ? $rawDays : explode(',', (string) $rawDays);
                    $cleanDays = array_values(array_filter(array_map('strtolower', array_map('trim', $daysArray))));

                    $formattedDays = implode(', ', array_map(function ($d) {
                        return ucfirst($d);
                    }, $cleanDays));

                    $startTime = ! empty($extraData['start_time']) ? substr($extraData['start_time'], 0, 5) : '14:00';
                    $endTime = ! empty($extraData['end_time']) ? substr($extraData['end_time'], 0, 5) : '17:30';

                    $extraModel->update([
                        'schedule_day' => $formattedDays ?: null,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                } else {
                    // Ikuti Jadwal Umum Default (reset kolom khusus agar null)
                    $extraModel->update([
                        'schedule_day' => null,
                        'start_time' => null,
                        'end_time' => null,
                    ]);
                }
            }
            unset($inputs['extra_schedules']);
        }

        foreach ($inputs as $key => $val) {
            if ($val !== null) {
                $strVal = is_array($val) ? implode(',', array_filter($val)) : (string) $val;
                SchoolSetting::set($key, $strVal);
            }
        }

        // If location is provided, update AttendanceLocation
        if ($request->filled('school_lat') && $request->filled('school_lng')) {
            $loc = AttendanceLocation::primary() ?? AttendanceLocation::first() ?? new AttendanceLocation;
            $loc->name = $request->input('school_name', 'SMA Negeri 1 Lengkong');
            $loc->latitude = (float) $request->school_lat;
            $loc->longitude = (float) $request->school_lng;
            $loc->radius_meters = (int) ($request->geofence_radius_meters ?? 100);
            $loc->is_active = ($inputs['enable_geofence_radius'] ?? SchoolSetting::get('enable_geofence_radius', '1')) === '1';
            $loc->save();
        }

        AuditLog::log('UPDATE_SCHOOL_SETTINGS', 'SchoolSetting', null, null, $inputs);

        return back()->with('success', 'Konfigurasi aturan sistem, metode presensi, dan koordinat geofence berhasil diperbarui.');
    }

    /**
     * Master Butir Tata Tertib (Katalog Prestasi & Pelanggaran)
     */
    public function categories(): View
    {
        $achievementCats = AchievementCategory::with('items')->get();
        $violationCats = ViolationCategory::with('items')->get();

        return view('master.categories', compact('achievementCats', 'violationCats'));
    }

    public function storeAchievementItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:achievement_categories,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'default_points' => ['required', 'integer', 'min:1'],
            'level' => ['required', 'string'],
        ]);

        $item = AchievementItem::create($validated);
        AuditLog::log('CREATE_ACHIEVEMENT_ITEM', 'AchievementItem', $item->id);

        return back()->with('success', 'Butir master prestasi '.$item->code.' berhasil ditambahkan.');
    }

    public function updateAchievementItem(Request $request, AchievementItem $achievementItem): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:achievement_categories,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'default_points' => ['required', 'integer', 'min:1'],
            'level' => ['required', 'string'],
        ]);

        $achievementItem->update($validated);
        AuditLog::log('UPDATE_ACHIEVEMENT_ITEM', 'AchievementItem', $achievementItem->id);

        return back()->with('success', 'Butir master prestasi '.$achievementItem->code.' berhasil diperbarui.');
    }

    public function destroyAchievementItem(AchievementItem $achievementItem): RedirectResponse
    {
        $code = $achievementItem->code;
        $achievementItem->delete();
        AuditLog::log('DELETE_ACHIEVEMENT_ITEM', 'AchievementItem', $achievementItem->id);

        return back()->with('success', 'Butir prestasi '.$code.' berhasil dihapus.');
    }

    public function storeViolationItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:violation_categories,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'default_points' => ['required', 'integer', 'min:1'],
            'guidance_recommendation' => ['nullable', 'string'],
        ]);

        $item = ViolationItem::create($validated);
        AuditLog::log('CREATE_VIOLATION_ITEM', 'ViolationItem', $item->id);

        return back()->with('success', 'Butir master pelanggaran '.$item->code.' berhasil ditambahkan.');
    }

    public function updateViolationItem(Request $request, ViolationItem $violationItem): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:violation_categories,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'default_points' => ['required', 'integer', 'min:1'],
            'guidance_recommendation' => ['nullable', 'string'],
        ]);

        $violationItem->update($validated);
        AuditLog::log('UPDATE_VIOLATION_ITEM', 'ViolationItem', $violationItem->id);

        return back()->with('success', 'Butir master pelanggaran '.$violationItem->code.' berhasil diperbarui.');
    }

    public function destroyViolationItem(ViolationItem $violationItem): RedirectResponse
    {
        $code = $violationItem->code;
        $violationItem->delete();
        AuditLog::log('DELETE_VIOLATION_ITEM', 'ViolationItem', $violationItem->id);

        return back()->with('success', 'Butir pelanggaran '.$code.' berhasil dihapus.');
    }

    public function storeDevice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'device_code' => ['required', 'string', 'max:50', 'unique:attendance_devices,device_code'],
            'device_name' => ['required', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'in:RFID,BARCODE,QR_SCANNER,QR_CODE,FACE_RECOGNITION,KIOSK'],
            'location' => ['nullable', 'string', 'max:150'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'is_active' => ['nullable'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! empty($validated['location']) && empty($validated['location_name'])) {
            $validated['location_name'] = $validated['location'];
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $device = AttendanceDevice::create($validated);
        AuditLog::log('CREATE_ATTENDANCE_DEVICE', 'AttendanceDevice', $device->id);

        return back()->with('success', 'Perangkat presensi '.$device->device_name.' berhasil didaftarkan.');
    }

    public function updateDevice(Request $request, AttendanceDevice $device): RedirectResponse
    {
        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'in:RFID,BARCODE,QR_SCANNER,QR_CODE,FACE_RECOGNITION,KIOSK'],
            'location' => ['nullable', 'string', 'max:150'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'ip_address' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! empty($validated['location']) && empty($validated['location_name'])) {
            $validated['location_name'] = $validated['location'];
        }
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $device->update($validated);
        AuditLog::log('UPDATE_ATTENDANCE_DEVICE', 'AttendanceDevice', $device->id);

        return back()->with('success', 'Perangkat presensi '.$device->device_code.' berhasil diperbarui.');
    }

    public function destroyDevice(AttendanceDevice $device): RedirectResponse
    {
        $code = $device->device_code;
        $device->delete();
        AuditLog::log('DELETE_ATTENDANCE_DEVICE', 'AttendanceDevice', $device->id);

        return back()->with('success', 'Perangkat presensi '.$code.' berhasil dihapus.');
    }

    public function generateDefaultDevices(): RedirectResponse
    {
        // Bersihkan kode default lama yang tidak lagi dipakai
        $obsoleteCodes = [
            'RFID-07', 'RFID-08', 'RFID-09', 'RFID-10',
            'QR-01', 'QR-02', 'QR-03', 'QR-04', 'QR-05', 'QR-06', 'QR-07', 'QR-08', 'QR-09', 'QR-10',
            'BARCODE-07', 'BARCODE-08', 'BARCODE-09', 'BARCODE-10',
            'FACE-07', 'FACE-08', 'FACE-09', 'FACE-10',
        ];
        AttendanceDevice::whereIn('device_code', $obsoleteCodes)->delete();

        // 1. 6 Mesin RFID Presensi Mandiri
        $rfidLocations = [
            'RFID-01' => ['name' => 'Gate RFID 01 - Gerbang Utama A', 'loc' => 'Gerbang Utama Masuk A', 'ip' => '192.168.1.101'],
            'RFID-02' => ['name' => 'Gate RFID 02 - Gerbang Utama B', 'loc' => 'Gerbang Utama Masuk B', 'ip' => '192.168.1.102'],
            'RFID-03' => ['name' => 'Gate RFID 03 - Gerbang Barat (Kendaraan)', 'loc' => 'Gerbang Samping Barat', 'ip' => '192.168.1.103'],
            'RFID-04' => ['name' => 'Gate RFID 04 - Gerbang Timur (Pejalan Kaki)', 'loc' => 'Gerbang Samping Timur', 'ip' => '192.168.1.104'],
            'RFID-05' => ['name' => 'Gate RFID 05 - Gedung Kelas X', 'loc' => 'Lobby Gedung Sayap Barat', 'ip' => '192.168.1.105'],
            'RFID-06' => ['name' => 'Gate RFID 06 - Gedung Kelas XI & XII', 'loc' => 'Lobby Gedung Sayap Timur', 'ip' => '192.168.1.106'],
        ];

        foreach ($rfidLocations as $code => $data) {
            AttendanceDevice::updateOrCreate(
                ['device_code' => $code],
                [
                    'device_name' => $data['name'],
                    'device_type' => 'RFID',
                    'location_name' => $data['loc'],
                    'ip_address' => $data['ip'],
                    'status' => 'ONLINE',
                    'is_active' => true,
                    'notes' => 'Mesin RFID Presensi Mandiri Siswa & PTK',
                ]
            );
        }

        // 2. 6 Mesin Barcode Scanner
        $barcodeLocations = [
            'BARCODE-01' => ['name' => 'Barcode Scanner 01 - Pos Keamanan Depan', 'loc' => 'Pos Satpam Gerbang Depan', 'ip' => '192.168.1.111'],
            'BARCODE-02' => ['name' => 'Barcode Scanner 02 - Lobby Tata Usaha', 'loc' => 'Lobby Utama Tata Usaha', 'ip' => '192.168.1.112'],
            'BARCODE-03' => ['name' => 'Barcode Scanner 03 - Selasar Koridor Barat', 'loc' => 'Koridor Barat Lantai 1', 'ip' => '192.168.1.113'],
            'BARCODE-04' => ['name' => 'Barcode Scanner 04 - Selasar Koridor Timur', 'loc' => 'Koridor Timur Lantai 1', 'ip' => '192.168.1.114'],
            'BARCODE-05' => ['name' => 'Barcode Scanner 05 - Perpustakaan & Hall Siswa', 'loc' => 'Pintu Masuk Perpustakaan Sekolah', 'ip' => '192.168.1.115'],
            'BARCODE-06' => ['name' => 'Barcode Scanner 06 - Ruang Bimbingan Konseling (BK)', 'loc' => 'Depan Ruang Konseling Siswa', 'ip' => '192.168.1.116'],
        ];

        foreach ($barcodeLocations as $code => $data) {
            AttendanceDevice::updateOrCreate(
                ['device_code' => $code],
                [
                    'device_name' => $data['name'],
                    'device_type' => 'BARCODE',
                    'location_name' => $data['loc'],
                    'ip_address' => $data['ip'],
                    'status' => 'ONLINE',
                    'is_active' => true,
                    'notes' => 'Mesin Barcode Scanner / QR Code Presensi Siswa',
                ]
            );
        }

        // 3. 6 Mesin Face Recognition AI
        $faceLocations = [
            'FACE-01' => ['name' => 'Face Recognition AI 01 - Gate Masuk Gerbang Utama', 'loc' => 'Gerbang Utama Masuk A', 'ip' => '192.168.1.121'],
            'FACE-02' => ['name' => 'Face Recognition AI 02 - Kiosk Lobby Gedung Utama', 'loc' => 'Lobby Utama Gedung Sekolah', 'ip' => '192.168.1.122'],
            'FACE-03' => ['name' => 'Face Recognition AI 03 - Lobby Sayap Kelas X', 'loc' => 'Selasar Gedung Sayap Barat', 'ip' => '192.168.1.123'],
            'FACE-04' => ['name' => 'Face Recognition AI 04 - Lobby Sayap Kelas XI', 'loc' => 'Selasar Gedung Sayap Timur', 'ip' => '192.168.1.124'],
            'FACE-05' => ['name' => 'Face Recognition AI 05 - Lobby Sayap Kelas XII', 'loc' => 'Lantai 2 Gedung Utama', 'ip' => '192.168.1.125'],
            'FACE-06' => ['name' => 'Face Recognition AI 06 - Laboratorium Terpadu & Server Hall', 'loc' => 'Lobby Lab Komputer & Sains', 'ip' => '192.168.1.126'],
        ];

        foreach ($faceLocations as $code => $data) {
            AttendanceDevice::updateOrCreate(
                ['device_code' => $code],
                [
                    'device_name' => $data['name'],
                    'device_type' => 'FACE_RECOGNITION',
                    'location_name' => $data['loc'],
                    'ip_address' => $data['ip'],
                    'status' => 'ONLINE',
                    'is_active' => true,
                    'notes' => 'Mesin Biometrik Wajah AI Presensi Siswa & Guru',
                ]
            );
        }

        AuditLog::log('GENERATE_DEFAULT_DEVICES', 'AttendanceDevice', null, null, [
            'rfid_count' => count($rfidLocations),
            'barcode_count' => count($barcodeLocations),
            'face_count' => count($faceLocations),
        ]);

        return back()->with('success', '6 Mesin RFID, 6 Barcode Scanner, dan 6 Face Recognition AI (Total 18 Unit) berhasil disinkronisasi & diaktifkan.');
    }

    public function classesTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_data_kelas_sman1lengkong.csv',
            ['nama_kelas', 'tingkat', 'jurusan', 'nip_wali_kelas'],
            [
                ['X-1', 'X', 'UMUM', '198001012005011001'],
                ['XI-MIPA-1', 'XI', 'IPA', '198202022006022002'],
                ['XII-IPS-1', 'XII', 'IPS', ''],
            ]
        );
    }

    public function importClasses(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importClasses($rows);

        return back()->with('success', "Impor Kelas Berhasil: {$result['imported']} ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    public function studentsTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_data_siswa_sman1lengkong.csv',
            ['nis', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'kelas', 'no_hp', 'alamat', 'rfid_uid'],
            [
                ['12051', '0071234561', 'Ahmad Fauzi', 'L', 'X-1', '081234567890', 'Jl. Lengkong No. 10', 'E2000019'],
                ['12052', '0071234562', 'Siti Rahmawati', 'P', 'X-1', '081234567891', 'Desa Banjardowo RT 02', 'E2000020'],
                ['12053', '0071234563', 'Budi Pratama', 'L', 'XI-MIPA-1', '081234567892', 'Kecamatan Lengkong', ''],
            ]
        );
    }

    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importStudents($rows);

        return back()->with('success', "Impor Siswa Berhasil: {$result['imported']} siswa & akun baru dibuat, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    public function teachersTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_data_guru_dan_pegawai_sman1lengkong.csv',
            ['nip', 'nama_lengkap', 'jenis_kelamin', 'mapel_jabatan', 'no_hp', 'email', 'username', 'peran', 'password'],
            [
                ['198501012010011005', 'Budi Santoso, S.Pd.', 'L', 'Matematika', '081234567890', 'budi@sman1lengkong.sch.id', '198501012010011005', 'guru', '198501012010011005'],
                ['198705122014022003', 'Siti Maryam, S.AP.', 'P', 'Tenaga Administrasi TU', '081234567891', 'siti.tu@sman1lengkong.sch.id', '198705122014022003', 'tenaga_administrasi', '198705122014022003'],
                ['198004172010012014', 'Anne Listiyani, S.Si., M.Pd.', 'P', 'Kepala Perpustakaan', '081234567894', 'anne@sman1lengkong.sch.id', '198004172010012014', 'kepala_perpustakaan', '198004172010012014'],
                ['199008202020121008', 'Rizki Pratama, S.Kom.', 'L', 'Operator Sekolah', '081234567895', 'operator@sman1lengkong.sch.id', '199008202020121008', 'operator', '199008202020121008'],
                ['196803151994031004', 'Drs. H. Ahmad Sudrajat, M.Pd.', 'L', 'Kepala Sekolah', '081234567892', 'kepsek@sman1lengkong.sch.id', '196803151994031004', 'kepala_sekolah', '196803151994031004'],
            ]
        );
    }

    public function importTeachers(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importTeachers($rows);

        return back()->with('success', "Impor Guru Berhasil: {$result['imported']} guru & akun baru didaftarkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    public function academicYearsTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_tahun_pelajaran_sman1lengkong.csv',
            ['nama_tahun', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_active'],
            [
                ['2026/2027', 'GANJIL', '2026-07-15', '2026-12-20', '1'],
                ['2026/2027', 'GENAP', '2027-01-05', '2027-06-25', '0'],
            ]
        );
    }

    public function importAcademicYears(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importAcademicYears($rows);

        return back()->with('success', "Impor Tahun Pelajaran Berhasil: {$result['imported']} ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    public function achievementItemTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_butir_prestasi_sman1lengkong.csv',
            ['kode_kategori', 'kode_butir', 'nama_prestasi', 'poin_default', 'tingkat'],
            [
                ['R1', 'R1.1', 'Juara 1 Lomba Akademik Tingkat Nasional', '100', 'NASIONAL'],
                ['R1', 'R1.2', 'Juara 2 Lomba Akademik Tingkat Provinsi', '60', 'PROVINSI'],
                ['R2', 'R2.1', 'Juara 1 Turnamen Olahraga Kabupaten', '40', 'KABUPATEN'],
            ]
        );
    }

    public function importAchievementItems(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importAchievementItems($rows);

        return back()->with('success', "Impor Butir Prestasi Berhasil: {$result['imported']} butir baru ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    public function violationItemTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_butir_pelanggaran_sman1lengkong.csv',
            ['kode_kategori', 'kode_butir', 'deskripsi_pelanggaran', 'poin_default', 'tingkat'],
            [
                ['P1', 'P1.1', 'Terlambat masuk sekolah lebih dari 15 menit', '5', 'RINGAN'],
                ['P2', 'P2.1', 'Seragam sekolah tidak lengkap atau tidak rapi', '5', 'RINGAN'],
                ['P4', 'P4.1', 'Membawa rokok atau merokok di lingkungan sekolah', '30', 'BERAT'],
            ]
        );
    }

    public function importViolationItems(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importViolationItems($rows);

        return back()->with('success', "Impor Butir Pelanggaran Berhasil: {$result['imported']} butir baru ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }

    /**
     * Master Data Hari Libur & Tanggal Merah
     */
    public function holidays(Request $request): View
    {
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month');
        $type = $request->input('type');
        $search = $request->input('search');

        $query = SchoolHoliday::query();

        if ($year) {
            $query->where(function ($q) use ($year) {
                $q->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year);
            });
        }

        if ($month) {
            $query->where(function ($q) use ($month) {
                $q->whereMonth('start_date', $month)
                    ->orWhereMonth('end_date', $month);
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $holidays = $query->orderBy('start_date', 'asc')->get();

        $stats = [
            'total' => SchoolHoliday::whereYear('start_date', $year)->count(),
            'nasional' => SchoolHoliday::whereYear('start_date', $year)->where('type', 'nasional')->count(),
            'khusus' => SchoolHoliday::whereYear('start_date', $year)->where('type', 'khusus')->count(),
            'sekolah' => SchoolHoliday::whereYear('start_date', $year)->where('type', 'sekolah')->count(),
        ];

        $classes = SchoolClass::orderBy('name')->get();

        return view('master.holidays', compact('holidays', 'stats', 'year', 'month', 'type', 'search', 'classes'));
    }

    /**
     * Simpan Hari Libur / Tanggal Merah Baru
     */
    public function storeHoliday(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'in:nasional,khusus,sekolah'],
            'description' => ['nullable', 'string', 'max:1000'],
            'target_type' => ['nullable', 'string', 'in:all,specific'],
            'target_classes' => ['nullable', 'array'],
            'target_classes.*' => ['integer', 'exists:school_classes,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $targetClasses = ($request->input('target_type') === 'specific' && ! empty($validated['target_classes']))
            ? array_values(array_map('intval', (array) $validated['target_classes']))
            : null;

        $holiday = SchoolHoliday::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'target_classes' => $targetClasses,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        AuditLog::log('CREATE_HOLIDAY', 'SchoolHoliday', $holiday->id, null, array_merge($validated, ['target_classes' => $targetClasses]));

        $targetNote = $holiday->target_classes ? " ({$holiday->target_summary})" : ' (Seluruh Sekolah)';

        return back()->with('success', "Hari Libur '{$holiday->name}' ({$holiday->formatted_range}){$targetNote} berhasil ditambahkan. Absensi otomatis tidak berlaku pada tanggal ini.");
    }

    /**
     * Perbarui Hari Libur / Tanggal Merah
     */
    public function updateHoliday(Request $request, SchoolHoliday $schoolHoliday): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'in:nasional,khusus,sekolah'],
            'description' => ['nullable', 'string', 'max:1000'],
            'target_type' => ['nullable', 'string', 'in:all,specific'],
            'target_classes' => ['nullable', 'array'],
            'target_classes.*' => ['integer', 'exists:school_classes,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $targetClasses = ($request->input('target_type') === 'specific' && ! empty($validated['target_classes']))
            ? array_values(array_map('intval', (array) $validated['target_classes']))
            : null;

        $schoolHoliday->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'target_classes' => $targetClasses,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        AuditLog::log('UPDATE_HOLIDAY', 'SchoolHoliday', $schoolHoliday->id, null, array_merge($validated, ['target_classes' => $targetClasses]));

        return back()->with('success', "Data Hari Libur '{$schoolHoliday->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Hari Libur
     */
    public function destroyHoliday(SchoolHoliday $schoolHoliday): RedirectResponse
    {
        $name = $schoolHoliday->name;
        $schoolHoliday->delete();

        AuditLog::log('DELETE_HOLIDAY', 'SchoolHoliday', $schoolHoliday->id);

        return back()->with('success', "Hari Libur '{$name}' berhasil dihapus.");
    }

    /**
     * Muat Otomatis Libur Nasional Standar & Kalender Pendidikan
     */
    public function generateCommonHolidays(Request $request): RedirectResponse
    {
        $year = (int) $request->input('year', date('Y'));

        $standardHolidays = [
            ['name' => 'Tahun Baru Masehi', 'start_date' => "{$year}-01-01", 'end_date' => "{$year}-01-01", 'type' => 'nasional', 'description' => 'Libur Nasional Tahun Baru Masehi'],
            ['name' => "Isra Mi'raj Nabi Muhammad SAW", 'start_date' => "{$year}-01-17", 'end_date' => "{$year}-01-17", 'type' => 'nasional', 'description' => "Peringatan Isra Mi'raj 1447 H"],
            ['name' => 'Tahun Baru Imlek', 'start_date' => "{$year}-02-17", 'end_date' => "{$year}-02-17", 'type' => 'nasional', 'description' => 'Tahun Baru Imlek 2577 Kongzili'],
            ['name' => 'Hari Suci Nyepi (Tahun Baru Saka)', 'start_date' => "{$year}-03-19", 'end_date' => "{$year}-03-19", 'type' => 'nasional', 'description' => 'Hari Suci Nyepi Tahun Baru Saka 1948'],
            ['name' => 'Hari Raya Idul Fitri 1447 H', 'start_date' => "{$year}-03-20", 'end_date' => "{$year}-03-24", 'type' => 'nasional', 'description' => 'Hari Raya Idul Fitri 1447 Hijriah & Cuti Bersama'],
            ['name' => 'Wafat Isa Al Masih', 'start_date' => "{$year}-04-03", 'end_date' => "{$year}-04-03", 'type' => 'nasional', 'description' => 'Hari Peringatan Wafat Yesus Kristus'],
            ['name' => 'Hari Buruh Internasional', 'start_date' => "{$year}-05-01", 'end_date' => "{$year}-05-01", 'type' => 'nasional', 'description' => 'Hari Buruh Internasional (May Day)'],
            ['name' => 'Kenaikan Isa Al Masih', 'start_date' => "{$year}-05-14", 'end_date' => "{$year}-05-14", 'type' => 'nasional', 'description' => 'Hari Peringatan Kenaikan Yesus Kristus'],
            ['name' => 'Hari Raya Idul Adha 1447 H', 'start_date' => "{$year}-05-27", 'end_date' => "{$year}-05-28", 'type' => 'nasional', 'description' => 'Hari Raya Idul Adha & Cuti Bersama'],
            ['name' => 'Hari Raya Waisak 2570 BE', 'start_date' => "{$year}-05-31", 'end_date' => "{$year}-05-31", 'type' => 'nasional', 'description' => 'Hari Raya Tri Suci Waisak'],
            ['name' => 'Hari Lahir Pancasila', 'start_date' => "{$year}-06-01", 'end_date' => "{$year}-06-01", 'type' => 'nasional', 'description' => 'Peringatan Hari Lahir Pancasila'],
            ['name' => 'Tahun Baru Islam 1448 H', 'start_date' => "{$year}-06-16", 'end_date' => "{$year}-06-16", 'type' => 'nasional', 'description' => 'Tahun Baru Hijriah 1 Muharram 1448 H'],
            ['name' => 'Libur Akhir Tahun Pelajaran / Semester Genap', 'start_date' => "{$year}-06-22", 'end_date' => "{$year}-07-11", 'type' => 'sekolah', 'description' => 'Libur Akhir Semester & Kenaikan Kelas Kalender Pendidikan'],
            ['name' => 'Hari Kemerdekaan Republik Indonesia', 'start_date' => "{$year}-08-17", 'end_date' => "{$year}-08-17", 'type' => 'nasional', 'description' => 'HUT Kemerdekaan RI'],
            ['name' => 'Maulid Nabi Muhammad SAW', 'start_date' => "{$year}-08-25", 'end_date' => "{$year}-08-25", 'type' => 'nasional', 'description' => 'Peringatan Maulid Nabi Muhammad SAW 1448 H'],
            ['name' => 'Hari Raya Natal', 'start_date' => "{$year}-12-25", 'end_date' => "{$year}-12-26", 'type' => 'nasional', 'description' => 'Hari Raya Natal & Cuti Bersama'],
            ['name' => 'Libur Semester Ganjil', 'start_date' => "{$year}-12-21", 'end_date' => "{$year}-12-31", 'type' => 'sekolah', 'description' => 'Libur Semester Ganjil Kalender Pendidikan'],
        ];

        $inserted = 0;
        foreach ($standardHolidays as $item) {
            SchoolHoliday::updateOrCreate(
                [
                    'name' => $item['name'],
                    'start_date' => $item['start_date'],
                ],
                [
                    'end_date' => $item['end_date'],
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );
            $inserted++;
        }

        AuditLog::log('GENERATE_COMMON_HOLIDAYS', 'SchoolHoliday', null, null, ['year' => $year, 'count' => $inserted]);

        return back()->with('success', "Berhasil memuat {$inserted} daftar Hari Libur Nasional & Kalender Pendidikan untuk tahun {$year}.");
    }

    /**
     * Download Template CSV Hari Libur
     */
    public function holidaysTemplate(): StreamedResponse
    {
        return $this->csvImportService->downloadTemplate(
            'template_hari_libur_tanggal_merah_sman1lengkong.csv',
            ['nama_hari_libur', 'tanggal_mulai', 'tanggal_selesai', 'jenis', 'keterangan'],
            [
                ['Tahun Baru Masehi', '2026-01-01', '2026-01-01', 'nasional', 'Libur Nasional'],
                ['Hari Raya Idul Fitri 1447 H', '2026-03-20', '2026-03-24', 'nasional', 'Hari Raya & Cuti Bersama'],
                ['Libur Semester Genap', '2026-06-22', '2026-07-11', 'sekolah', 'Libur Kenaikan Kelas'],
            ]
        );
    }

    /**
     * Import Data Hari Libur dari CSV
     */
    public function importHolidays(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);
        $rows = $this->csvImportService->parseCsv($request->file('file'));

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format baris tidak dapat dibaca.');
        }

        $result = $this->csvImportService->importHolidays($rows);

        return back()->with('success', "Impor Hari Libur Berhasil: {$result['imported']} data baru ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");
    }
}
