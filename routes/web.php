<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;

// Portal Orang Tua Publik (Tanpa Perlu Login - Cukup Masukkan NISN Siswa)
Route::match(['get', 'post'], '/portal-ortu', [PublicPortalController::class, 'index'])->name('parent.portal');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Quick Role Switcher (Available for instant role testing & evaluation)
Route::post('/quick-switch', [AuthController::class, 'quickSwitch'])->name('quick.switch');

// RFID Hardware Device Endpoint (IoT / Card Reader Integration)
Route::post('/api/attendance/rfid', [AttendanceController::class, 'scanRfid'])->name('api.attendance.rfid');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::match(['put', 'post'], '/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard Hub
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Presensi Terpadu
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Presensi Selfie (Kamera Langsung & GPS Geofencing)
        Route::get('/selfie', [AttendanceController::class, 'selfie'])->name('selfie');
        Route::post('/selfie', [AttendanceController::class, 'storeSelfie'])->name('selfie.store');

        // Presensi QR Code
        Route::get('/qr-scanner', [AttendanceController::class, 'qrScanner'])->name('qr.scanner');
        Route::post('/qr-scanner', [AttendanceController::class, 'storeQr'])->name('qr.store');
        Route::get('/qr-session', [AttendanceController::class, 'qrSession'])->name('qr.session');
        Route::post('/qr-session', [AttendanceController::class, 'createQrSession'])->name('qr.session.create');

        // Presensi RFID
        Route::get('/rfid-simulator', [AttendanceController::class, 'rfidSimulator'])->name('rfid.simulator');
        Route::post('/rfid-scan', [AttendanceController::class, 'scanRfid'])->name('rfid.scan');

        // Ajuan Izin, Sakit & Pulang Cepat Siswa (Selfie Kamera Langsung & GPS Realtime)
        Route::get('/leave', [AttendanceController::class, 'leaveForm'])->name('leave');
        Route::get('/early-leave', fn () => redirect()->route('attendance.leave', ['type' => 'PULANG_CEPAT']))->name('early_leave');
        Route::post('/leave', [AttendanceController::class, 'storeLeave'])->name('leave.store');

        // Persetujuan Ajuan Izin & Sakit (Wali Kelas, Admin, Super Admin)
        Route::get('/leaves', [AttendanceController::class, 'leaveApprovals'])->name('leaves.index');
        Route::post('/leaves/{attendance}/approve', [AttendanceController::class, 'approveLeave'])->name('leaves.approve');
        Route::post('/leaves/{attendance}/reject', [AttendanceController::class, 'rejectLeave'])->name('leaves.reject');

        // Absen Manual (Wali Kelas, Admin, Super Admin)
        Route::get('/manual', [AttendanceController::class, 'manualForm'])->name('manual');
        Route::post('/manual', [AttendanceController::class, 'storeManual'])->name('manual.store');

        // Riwayat & Laporan Presensi Lengkap Real-time
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::post('/{attendance}/verify', [AttendanceController::class, 'verify'])->name('verify');
    });

    // Tata Tertib Siswa (Prestasi, Pelanggaran, Pembinaan, Rangking & Laporan)
    Route::prefix('discipline')->name('discipline.')->group(function () {
        Route::get('/ranking', [DisciplineController::class, 'ranking'])->name('ranking');
        Route::get('/report', [DisciplineController::class, 'report'])->name('report');
    });

    // Prestasi Siswa (R1 - R10)
    Route::get('/achievements/template', [AchievementController::class, 'template'])->name('achievements.template');
    Route::post('/achievements/import', [AchievementController::class, 'import'])->name('achievements.import');
    Route::resource('achievements', AchievementController::class);
    Route::post('/achievements/{achievement}/verify', [AchievementController::class, 'verify'])->name('achievements.verify');

    // Pelanggaran Siswa (P1 - P6)
    Route::get('/violations/template', [ViolationController::class, 'template'])->name('violations.template');
    Route::post('/violations/import', [ViolationController::class, 'import'])->name('violations.import');
    Route::resource('violations', ViolationController::class);
    Route::post('/violations/{violation}/verify', [ViolationController::class, 'verify'])->name('violations.verify');

    // Pembinaan Siswa (BK & Wali Kelas)
    Route::get('/guidance/template', [GuidanceController::class, 'template'])->name('guidance.template');
    Route::post('/guidance/import', [GuidanceController::class, 'import'])->name('guidance.import');
    Route::resource('guidance', GuidanceController::class);

    // Gerakan 7 Kebiasaan Anak Indonesia Hebat
    Route::prefix('habits')->name('habits.')->group(function () {
        Route::get('/', [HabitController::class, 'journal'])->name('journal');
        Route::post('/log', [HabitController::class, 'updateLog'])->name('log.update');
        Route::get('/summary', [HabitController::class, 'summary'])->name('summary');
        Route::get('/charts', [HabitController::class, 'charts'])->name('charts');
        Route::get('/report', [HabitController::class, 'report'])->name('report');
        Route::post('/target-times', [HabitController::class, 'updateTargetTimes'])->name('target_times.update');
        Route::post('/note', [HabitController::class, 'storeNote'])->name('note.store');
    });

    // AI Analisis Perkembangan Siswa
    Route::get('/ai/analysis', [AiAnalysisController::class, 'index'])->name('ai.analysis');

    // Laporan & Cetak Berkas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/student/{student}', [ReportController::class, 'studentDossier'])->name('student.dossier');
        Route::get('/student/{student}/print', [ReportController::class, 'printStudentDossier'])->name('student.print');
        Route::get('/print/combined', [ReportController::class, 'printCombined'])->name('print.combined');
        Route::get('/print/attendance', [ReportController::class, 'printAttendance'])->name('print.attendance');
        Route::get('/print/discipline', [ReportController::class, 'printDiscipline'])->name('print.discipline');
        Route::get('/print/habits', [ReportController::class, 'printHabits'])->name('print.habits');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export.csv');
    });

    // Master Data Terpadu (Admin & Super Admin)
    Route::prefix('master')->name('master.')->middleware('role:admin,super_admin')->group(function () {
        // Identitas Sekolah Lengkap
        Route::get('/school-profile', [MasterDataController::class, 'schoolProfile'])->name('school_profile');
        Route::post('/school-profile', [MasterDataController::class, 'updateSchoolProfile'])->name('school_profile.update');

        // Logo & KOP Surat Resmi & Nama Aplikasi (Upload & Kelola)
        Route::get('/branding', [MasterDataController::class, 'branding'])->name('branding');
        Route::post('/branding/app-name', [MasterDataController::class, 'updateAppName'])->name('branding.app_name.update');
        Route::post('/branding/logo', [MasterDataController::class, 'updateLogo'])->name('branding.logo.update');
        Route::delete('/branding/logo', [MasterDataController::class, 'destroyLogo'])->name('branding.logo.destroy');
        Route::post('/branding/kop', [MasterDataController::class, 'updateKop'])->name('branding.kop.update');
        Route::delete('/branding/kop', [MasterDataController::class, 'destroyKop'])->name('branding.kop.destroy');

        // Data Kelas
        Route::get('/classes/template', [MasterDataController::class, 'classesTemplate'])->name('classes.template');
        Route::post('/classes/import', [MasterDataController::class, 'importClasses'])->name('classes.import');
        Route::get('/classes', [MasterDataController::class, 'classes'])->name('classes');
        Route::post('/classes', [MasterDataController::class, 'storeClass'])->name('classes.store');
        Route::put('/classes/{schoolClass}', [MasterDataController::class, 'updateClass'])->name('classes.update');
        Route::delete('/classes/{schoolClass}', [MasterDataController::class, 'destroyClass'])->name('classes.destroy');

        // Data Siswa
        Route::get('/students/template', [MasterDataController::class, 'studentsTemplate'])->name('students.template');
        Route::post('/students/import', [MasterDataController::class, 'importStudents'])->name('students.import');
        Route::get('/students', [MasterDataController::class, 'students'])->name('students');
        Route::post('/students', [MasterDataController::class, 'storeStudent'])->name('students.store');
        Route::put('/students/{student}', [MasterDataController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{student}', [MasterDataController::class, 'destroyStudent'])->name('students.destroy');

        // Data Guru beserta Role Akun Loginnya
        Route::get('/teachers/template', [MasterDataController::class, 'teachersTemplate'])->name('teachers.template');
        Route::post('/teachers/import', [MasterDataController::class, 'importTeachers'])->name('teachers.import');
        Route::get('/teachers', [MasterDataController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [MasterDataController::class, 'storeTeacher'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [MasterDataController::class, 'updateTeacher'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [MasterDataController::class, 'destroyTeacher'])->name('teachers.destroy');

        // Tahun Pelajaran
        Route::get('/academic-years/template', [MasterDataController::class, 'academicYearsTemplate'])->name('academic_years.template');
        Route::post('/academic-years/import', [MasterDataController::class, 'importAcademicYears'])->name('academic_years.import');
        Route::get('/academic-years', [MasterDataController::class, 'academicYears'])->name('academic_years');
        Route::post('/academic-years', [MasterDataController::class, 'storeAcademicYear'])->name('academic_years.store');
        Route::post('/academic-years/{academicYear}/set-active', [MasterDataController::class, 'setActiveAcademicYear'])->name('academic_years.set_active');
        Route::delete('/academic-years/{academicYear}', [MasterDataController::class, 'destroyAcademicYear'])->name('academic_years.destroy');

        // Hari Libur & Tanggal Merah
        Route::get('/holidays/template', [MasterDataController::class, 'holidaysTemplate'])->name('holidays.template');
        Route::post('/holidays/import', [MasterDataController::class, 'importHolidays'])->name('holidays.import');
        Route::post('/holidays/generate-common', [MasterDataController::class, 'generateCommonHolidays'])->name('holidays.generate_common');
        Route::get('/holidays', [MasterDataController::class, 'holidays'])->name('holidays');
        Route::post('/holidays', [MasterDataController::class, 'storeHoliday'])->name('holidays.store');
        Route::put('/holidays/{schoolHoliday}', [MasterDataController::class, 'updateHoliday'])->name('holidays.update');
        Route::delete('/holidays/{schoolHoliday}', [MasterDataController::class, 'destroyHoliday'])->name('holidays.destroy');

        // Pengaturan Sistem & Geofence (Termasuk Toggle Pilihan Absen)
        Route::get('/settings', [MasterDataController::class, 'settings'])->name('settings');
        Route::post('/settings', [MasterDataController::class, 'updateSettings'])->name('settings.update');

        // Master Butir Tata Tertib (Bisa Diedit, Ditambahkan, dan Diimpor)
        Route::get('/categories/achievement-item/template', [MasterDataController::class, 'achievementItemTemplate'])->name('achievement_item.template');
        Route::post('/categories/achievement-item/import', [MasterDataController::class, 'importAchievementItems'])->name('achievement_item.import');
        Route::get('/categories/violation-item/template', [MasterDataController::class, 'violationItemTemplate'])->name('violation_item.template');
        Route::post('/categories/violation-item/import', [MasterDataController::class, 'importViolationItems'])->name('violation_item.import');
        Route::get('/categories', [MasterDataController::class, 'categories'])->name('categories');
        Route::post('/achievement-item', [MasterDataController::class, 'storeAchievementItem'])->name('achievement_item.store');
        Route::put('/achievement-item/{achievementItem}', [MasterDataController::class, 'updateAchievementItem'])->name('achievement_item.update');
        Route::delete('/achievement-item/{achievementItem}', [MasterDataController::class, 'destroyAchievementItem'])->name('achievement_item.destroy');
        Route::post('/violation-item', [MasterDataController::class, 'storeViolationItem'])->name('violation_item.store');
        Route::put('/violation-item/{violationItem}', [MasterDataController::class, 'updateViolationItem'])->name('violation_item.update');
        Route::delete('/violation-item/{violationItem}', [MasterDataController::class, 'destroyViolationItem'])->name('violation_item.destroy');

        // Perangkat Scanner IoT
        Route::post('/devices', [MasterDataController::class, 'storeDevice'])->name('devices.store');
    });

    // Notifikasi
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read_all');
    });
});
