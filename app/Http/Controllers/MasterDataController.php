<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use App\Services\CsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterDataController extends Controller
{
    public function __construct(
        protected CsvImportService $csvImportService
    ) {}

    /**
     * Tampilan Pengaturan Branding (Logo Aplikasi, KOP Surat, & Nama Aplikasi)
     */
    public function branding(): View
    {
        $logoPath = SchoolSetting::get('school_logo');
        $kopPath = SchoolSetting::get('school_kop');

        $logoUrl = $logoPath && Storage::disk('public')->exists($logoPath)
            ? Storage::disk('public')->url($logoPath)
            : null;

        $kopUrl = $kopPath && Storage::disk('public')->exists($kopPath)
            ? Storage::disk('public')->url($kopPath)
            : null;

        $province = SchoolSetting::get('school_province', 'JAWA BARAT');
        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $appName = SchoolSetting::get('app_name', 'SISWA SMA1LE');
        $appTagline = SchoolSetting::get('app_tagline', 'Sistem Informasi Siswa Terpadu SMAN 1 Lengkong');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $schoolNpsn = SchoolSetting::get('school_npsn', '20539123');
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

        $path = $file->storeAs('branding', $filename, 'public');
        SchoolSetting::set('school_logo', $path, 'branding', 'Path logo resmi aplikasi');

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

        SchoolSetting::where('key', 'school_logo')->delete();

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

        $path = $file->storeAs('branding', $filename, 'public');
        SchoolSetting::set('school_kop', $path, 'branding', 'Path KOP surat resmi sekolah');

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

        SchoolSetting::where('key', 'school_kop')->delete();

        AuditLog::log('DELETE_SCHOOL_KOP', 'SchoolSetting');

        return back()->with('success', 'KOP surat berhasil direset ke format teks resmi standar Jawa Barat.');
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

        $classes = SchoolClass::all();
        $academicYears = AcademicYear::all();

        $query = Student::with(['schoolClass', 'academicYear', 'user']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('rfid_uid', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('master.students', compact('students', 'classes', 'academicYears', 'classId', 'search'));
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
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        // Buat Akun Pengguna Siswa jika belum ada (Username & Password otomatis NISN)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['nisn'].'@siswa.sman1lengkong.sch.id',
            'username' => $validated['nisn'],
            'password' => Hash::make($validated['nisn']),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $validated['user_id'] = $user->id;
        $validated['is_active'] = true;

        $student = Student::create($validated);
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
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $student->update($validated);

        if ($student->user) {
            $student->user->update([
                'name' => $validated['name'],
                'username' => $validated['nisn'],
                'is_active' => $validated['is_active'],
            ]);
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
     * Data Guru beserta Role Akun Loginnya
     */
    public function teachers(Request $request): View
    {
        $search = $request->input('search');

        $query = Teacher::with(['user', 'homeroomClass']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%"));
            });
        }

        $teachers = $query->orderBy('name')->get();

        return view('master.teachers', compact('teachers', 'search'));
    }

    /**
     * Simpan Data Guru Baru
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:teachers,nip'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:guru,wali_kelas,admin,kepala_sekolah'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['nullable', 'string', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $username = ! empty($validated['username']) ? $validated['username'] : $validated['nip'];
        $initialPassword = ! empty($validated['password']) ? $validated['password'] : $validated['nip'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($initialPassword),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'title' => $validated['title'] ?? ($validated['role'] === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru Pengajar'),
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        AuditLog::log('CREATE_TEACHER', 'Teacher', $teacher->id);

        return back()->with('success', 'Data pegawai '.$teacher->name.' dengan peran '.str_replace('_', ' ', $validated['role']).' berhasil ditambahkan (Username & Password: '.$validated['nip'].').');
    }

    /**
     * Update Data Guru & Role Akun Login
     */
    public function updateTeacher(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:guru,wali_kelas,admin,kepala_sekolah'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $teacher->update([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'title' => $validated['title'],
            'phone' => $validated['phone'],
            'is_active' => $validated['is_active'],
        ]);

        if ($teacher->user) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'phone' => $validated['phone'],
                'is_active' => $validated['is_active'],
            ];

            if (! empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }

            $teacher->user->update($userUpdate);
        }

        AuditLog::log('UPDATE_TEACHER', 'Teacher', $teacher->id);

        return back()->with('success', 'Data guru '.$teacher->name.' & role akun berhasil diperbarui.');
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
            'semester' => ['required', 'in:Ganjil,Genap'],
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
            'semester' => $validated['semester'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $isActive,
        ]);

        AuditLog::log('CREATE_ACADEMIC_YEAR', 'AcademicYear', $ay->id);

        return back()->with('success', 'Tahun Pelajaran '.$ay->name.' ('.$ay->semester.') berhasil ditambahkan.');
    }

    /**
     * Set Tahun Pelajaran Aktif
     */
    public function setActiveAcademicYear(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        AuditLog::log('SET_ACTIVE_ACADEMIC_YEAR', 'AcademicYear', $academicYear->id);

        return back()->with('success', 'Tahun Pelajaran '.$academicYear->name.' ('.$academicYear->semester.') kini menjadi Tahun Pelajaran Aktif.');
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
        $devices = AttendanceDevice::all();
        $academicYears = AcademicYear::all();
        $classes = SchoolClass::with(['homeroomTeacher', 'academicYear'])->get();
        $teachers = Teacher::all();

        return view('master.settings', compact('settings', 'location', 'devices', 'academicYears', 'classes', 'teachers'));
    }

    /**
     * Update Pengaturan Sistem
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token']);

        // Explicit attendance methods toggles (checkboxes default to '0' if unchecked)
        $methodKeys = ['enable_attendance_selfie', 'enable_attendance_qr', 'enable_attendance_rfid'];
        foreach ($methodKeys as $key) {
            $inputs[$key] = $request->has($key) ? '1' : '0';
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

        foreach ($inputs as $key => $val) {
            if ($val !== null) {
                SchoolSetting::set($key, (string) $val);
            }
        }

        // If location is provided, update AttendanceLocation
        if ($request->filled('school_lat') && $request->filled('school_lng')) {
            $loc = AttendanceLocation::primary() ?? new AttendanceLocation;
            $loc->name = $request->input('school_name', 'SMA Negeri 1 Lengkong');
            $loc->latitude = (float) $request->school_lat;
            $loc->longitude = (float) $request->school_lng;
            $loc->radius_meters = (int) ($request->geofence_radius_meters ?? 100);
            $loc->is_active = true;
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
            'device_code' => ['required', 'string', 'unique:attendance_devices,device_code'],
            'device_name' => ['required', 'string'],
            'device_type' => ['required', 'in:RFID,KIOSK,BARCODE'],
            'location_name' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'ip'],
        ]);

        $device = AttendanceDevice::create($validated);
        AuditLog::log('CREATE_ATTENDANCE_DEVICE', 'AttendanceDevice', $device->id);

        return back()->with('success', 'Perangkat presensi baru berhasil didaftarkan.');
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
            'template_data_guru_sman1lengkong.csv',
            ['nip', 'nama_lengkap', 'jenis_kelamin', 'mapel_jabatan', 'no_hp', 'email', 'username', 'peran', 'password'],
            [
                ['198501012010011005', 'Budi Santoso, S.Pd.', 'L', 'Matematika', '081234567890', 'budi@sman1lengkong.sch.id', '198501012010011005', 'guru', '198501012010011005'],
                ['196803151994031004', 'Drs. H. Ahmad Sudrajat, M.Pd.', 'L', 'Kepala Sekolah', '081234567892', 'kepsek@sman1lengkong.sch.id', '196803151994031004', 'kepala_sekolah', '196803151994031004'],
                ['197905102008011010', 'Hendra Wijaya, S.Pd.', 'L', 'Fisika & Wali Kelas', '081234567893', 'hendra@sman1lengkong.sch.id', '197905102008011010', 'wali_kelas', '197905102008011010'],
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
}
