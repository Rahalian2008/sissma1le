<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\AiCurriculumCenterController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceCardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BkAkpdController;
use App\Http\Controllers\BkCaseConferenceController;
use App\Http\Controllers\BkCounselingSessionController;
use App\Http\Controllers\BkDashboardController;
use App\Http\Controllers\BkHomeVisitController;
use App\Http\Controllers\BkParentCallController;
use App\Http\Controllers\BkPsychologicalTestController;
use App\Http\Controllers\BkReferralController;
use App\Http\Controllers\BkRplController;
use App\Http\Controllers\BkSociometryController;
use App\Http\Controllers\Cbt\CbtController;
use App\Http\Controllers\Cbt\CbtStudentController;
use App\Http\Controllers\CurriculumDocumentController;
use App\Http\Controllers\CurriculumPlanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DigitalRaportController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\EarlyLeaveController;
use App\Http\Controllers\ExtracurricularAttendanceController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\QuranDigitalController;
use App\Http\Controllers\QuranTahfidzController;
use App\Http\Controllers\RaporKurnasController;
use App\Http\Controllers\ReflectionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolBellController;
use App\Http\Controllers\SchoolExitPermitController;
use App\Http\Controllers\SchoolWebsiteController;
use App\Http\Controllers\Student\SpmbController;
use App\Http\Controllers\SupervisionController;
use App\Http\Controllers\TeachingJournalController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\WebsiteContentController;
use App\Http\Controllers\WorshipAttendanceController;
use Illuminate\Support\Facades\Route;

// Website Resmi SMAN 1 Lengkong (Publik, Modern, Edukatif & Responsif)
Route::get('/', [SchoolWebsiteController::class, 'index'])->name('website.home');
Route::get('/api/website/live-stats', [SchoolWebsiteController::class, 'liveStats'])->name('website.live_stats');

// Layar Display KIOSK (Lobby Monitor, Hall Screen & TV Display Real-Time)
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/data', [KioskController::class, 'data'])->name('kiosk.data');

// Portal Gateway SIS-SMA1LE Terpadu
Route::get('/portal', [PublicPortalController::class, 'portalSelection'])->name('portal.select');

// Portal Orang Tua Publik (Tanpa Perlu Login - Cukup Masukkan NISN Siswa)
Route::match(['get', 'post'], '/portal-ortu', [PublicPortalController::class, 'index'])->name('parent.portal');
Route::get('/portal-ortu/raport/{student}', [PublicPortalController::class, 'printRaport'])->name('parent.portal.raport');

// Buku Tamu Kiosk Perpustakaan Mandiri (Tanpa Perlu Login - Masukkan NISN Siswa / NIP Guru-Tendik / Tamu Umum)
Route::get('/perpustakaan/buku-tamu', [LibraryController::class, 'guestBook'])->name('library.guest_book.index');
Route::get('/perpustakaan-buku-tamu', [LibraryController::class, 'guestBook'])->name('library.guest_book');
Route::post('/perpustakaan/buku-tamu', [LibraryController::class, 'storeGuestBook'])->name('library.guest_book.store');
Route::get('/perpustakaan/buku-tamu/lookup', [LibraryController::class, 'lookupGuest'])->name('library.guest_book.lookup');

// Kiosk Web Buku Tamu Digital Sekolah (Layar Resepsionis / Tablet Mandiri Tamu Umum)
Route::get('/kiosk-tamu', [GuestController::class, 'kiosk'])->name('guests.kiosk');
Route::get('/buku-tamu-sekolah', [GuestController::class, 'kiosk']);
Route::post('/kiosk-tamu', [GuestController::class, 'storeKiosk'])->name('guests.kiosk.store');
Route::post('/kiosk-tamu/checkout', [GuestController::class, 'checkout'])->name('guests.kiosk.checkout');
Route::get('/kiosk-tamu/card/{ticket}', [GuestController::class, 'card'])->name('guests.card');
Route::get('/kiosk-tamu/print/{ticket}', [GuestController::class, 'printCard'])->name('guests.print_card');

// SPMB Publik (Pendaftaran Siswa Baru Online SMAN 1 Lengkong & Cek Bukti)
Route::get('/spmb/daftar', [SpmbController::class, 'publicRegister'])->name('spmb.public.register');
Route::post('/spmb/daftar', [SpmbController::class, 'storePublicRegister'])->name('spmb.public.store');
Route::get('/spmb/bukti/{registrationNumber}', [SpmbController::class, 'publicSuccess'])->name('spmb.public.success');
Route::get('/spmb/bukti/{registrationNumber}/cetak', [SpmbController::class, 'publicPrintCard'])->name('spmb.public.print');
Route::get('/daftar-spmb', fn () => redirect()->route('spmb.public.register'));
Route::get('/ppdb/daftar', fn () => redirect()->route('spmb.public.register'));

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Two-Factor Authentication Login Challenge
    Route::get('/login/two-factor', [TwoFactorController::class, 'showChallenge'])->name('login.two_factor');
    Route::post('/login/two-factor', [TwoFactorController::class, 'verifyChallenge'])->name('login.two_factor.post');
    Route::post('/login/two-factor/cancel', [TwoFactorController::class, 'cancelChallenge'])->name('login.two_factor.cancel');
});

// Quick Role Switcher (Available for instant role testing & evaluation)
Route::post('/quick-switch', [AuthController::class, 'quickSwitch'])->name('quick.switch');

// RFID Hardware Device Endpoint (IoT / Card Reader Integration)
Route::post('/api/attendance/rfid', [AttendanceController::class, 'scanRfid'])->name('api.attendance.rfid');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/user/switch-active-role', [AuthController::class, 'switchActiveRole'])->name('user.switch_active_role');
    Route::get('/user/switch-role/{role}', [AuthController::class, 'switchActiveRoleGet'])->name('user.switch_role_get');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::match(['put', 'post'], '/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Two-Factor Authentication Setup & Management (Google Authenticator)
    Route::get('/user/two-factor-setup', [TwoFactorController::class, 'setup'])->name('two_factor.setup');
    Route::post('/user/two-factor-enable', [TwoFactorController::class, 'enable'])->name('two_factor.enable');
    Route::post('/user/two-factor-disable', [TwoFactorController::class, 'disable'])->name('two_factor.disable');
    Route::post('/user/two-factor-recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two_factor.recovery_codes');

    // Dashboard Hub
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Presensi Terpadu
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Dashboard Terpadu Utama (Scanner, Selfie, Manual, Izin/Pulang & Riwayat)
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

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

        // Presensi Barcode Scanner & Face Recognition
        Route::post('/barcode-scan', [AttendanceController::class, 'scanBarcode'])->name('barcode.scan');
        Route::post('/face-recognition-scan', [AttendanceController::class, 'scanFaceRecognition'])->name('face.scan');

        // Ajuan Izin & Sakit Siswa (Selfie Kamera Langsung & GPS Realtime)
        Route::get('/leave', [AttendanceController::class, 'leaveForm'])->name('leave');
        Route::get('/early-leave', fn () => redirect()->route('attendance.early-leave.index'))->name('early_leave');
        Route::post('/leave', [AttendanceController::class, 'storeLeave'])->name('leave.store');

        // Fitur Ajuan Pulang Cepat Siswa & Surat Keterangan Pulang Cepat Resmi
        Route::prefix('early-leave')->name('early-leave.')->group(function () {
            Route::get('/', [EarlyLeaveController::class, 'index'])->name('index');
            Route::post('/', [EarlyLeaveController::class, 'store'])->name('store');
            Route::get('/recap', [EarlyLeaveController::class, 'recap'])->name('recap');
            Route::get('/recap/print', [EarlyLeaveController::class, 'printRecap'])->name('print_recap');
            Route::post('/{earlyLeave}/approve', [EarlyLeaveController::class, 'approve'])->name('approve');
            Route::post('/{earlyLeave}/reject', [EarlyLeaveController::class, 'reject'])->name('reject');
            Route::get('/{earlyLeave}/edit', [EarlyLeaveController::class, 'edit'])->name('edit');
            Route::put('/{earlyLeave}', [EarlyLeaveController::class, 'update'])->name('update');
            Route::delete('/{earlyLeave}', [EarlyLeaveController::class, 'destroy'])->name('destroy');
            Route::get('/{earlyLeave}/print', [EarlyLeaveController::class, 'printLetter'])->name('print');
        });

        // Fitur Izin Keluar Lingkungan Sekolah (Sementara) & Surat Izin Resmi (> 1 Siswa)
        Route::get('/school-exit-permit', fn () => redirect()->route('attendance.school-pass.index'))->name('school_exit_permit');
        Route::get('/school-exit-permits', fn () => redirect()->route('attendance.school-pass.index'))->name('school-exit-permits.index');
        Route::prefix('school-pass')->name('school-pass.')->group(function () {
            Route::get('/', [SchoolExitPermitController::class, 'index'])->name('index');
            Route::post('/', [SchoolExitPermitController::class, 'store'])->name('store');
            Route::get('/recap', [SchoolExitPermitController::class, 'recap'])->name('recap');
            Route::get('/recap/print', [SchoolExitPermitController::class, 'printRecap'])->name('print_recap');
            Route::post('/{permit}/approve', [SchoolExitPermitController::class, 'approve'])->name('approve');
            Route::post('/{permit}/returned', [SchoolExitPermitController::class, 'markReturned'])->name('returned');
            Route::post('/{permit}/reject', [SchoolExitPermitController::class, 'reject'])->name('reject');
            Route::get('/{permit}/edit', [SchoolExitPermitController::class, 'edit'])->name('edit');
            Route::put('/{permit}', [SchoolExitPermitController::class, 'update'])->name('update');
            Route::delete('/{permit}', [SchoolExitPermitController::class, 'destroy'])->name('destroy');
            Route::get('/{permit}/print', [SchoolExitPermitController::class, 'printLetter'])->name('print');
        });

        // Fitur Jadwal Petugas Piket (Guru & TU Piket Harian)
        Route::prefix('picket-schedules')->name('picket-schedules.')->group(function () {
            Route::get('/', [AttendanceController::class, 'picketSchedulesIndex'])->name('index');
            Route::post('/', [AttendanceController::class, 'storePicketSchedule'])->name('store');
            Route::put('/{picketSchedule}', [AttendanceController::class, 'updatePicketSchedule'])->name('update');
            Route::delete('/{picketSchedule}', [AttendanceController::class, 'destroyPicketSchedule'])->name('destroy');
            Route::get('/print', [AttendanceController::class, 'printPicketSchedule'])->name('print');
        });

        // Persetujuan Ajuan Izin & Sakit (Wali Kelas, Admin, Super Admin)
        Route::get('/leaves', [AttendanceController::class, 'leaveApprovals'])->name('leaves.index');
        Route::post('/leaves/{attendance}/approve', [AttendanceController::class, 'approveLeave'])->name('leaves.approve');
        Route::post('/leaves/{attendance}/reject', [AttendanceController::class, 'rejectLeave'])->name('leaves.reject');

        // Absen Manual (Wali Kelas, Admin, Super Admin)
        Route::get('/manual', [AttendanceController::class, 'manualForm'])->name('manual');
        Route::post('/manual', [AttendanceController::class, 'storeManual'])->name('manual.store');
        Route::post('/manual/bulk', [AttendanceController::class, 'storeManualBulk'])->name('manual.bulk');

        // Riwayat & Laporan Presensi Lengkap Real-time
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::post('/{attendance}/verify', [AttendanceController::class, 'verify'])->name('verify');
        Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');

        // Kartu Absensi Siswa (RFID & QR Code NISN - Standar CR80 / KTP)
        Route::get('/cards', [AttendanceCardController::class, 'index'])->name('cards.index');
        Route::get('/cards/print', [AttendanceCardController::class, 'print'])->name('cards.print');
        Route::get('/cards/{student}/single', [AttendanceCardController::class, 'single'])->name('cards.single');
    });

    // Tata Tertib Siswa (Prestasi, Pelanggaran, Pembinaan, Rangking & Laporan)
    Route::prefix('discipline')->name('discipline.')->group(function () {
        Route::get('/ranking', [DisciplineController::class, 'ranking'])->name('ranking');
        Route::get('/report', [DisciplineController::class, 'report'])->name('report');
        Route::post('/sync-automations', [DisciplineController::class, 'syncAutomations'])->name('sync_automations');
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

    // Bimbingan Konseling (BK) Terstandar POP-BK & Asesmen Psikotes
    Route::prefix('bk')->name('bk.')->group(function () {
        // Akses Terbuka / Khusus Peserta Didik & Guru Bina
        Route::get('/', [BkDashboardController::class, 'index'])->name('dashboard');
        Route::get('/siswa/{student}/360', [BkDashboardController::class, 'studentProfile360'])->name('student.profile360');
        Route::get('/konseling', [BkCounselingSessionController::class, 'index'])->name('counseling.index');
        Route::get('/konseling/{session}', [BkCounselingSessionController::class, 'show'])->name('counseling.show');
        Route::get('/home-visit', [BkHomeVisitController::class, 'index'])->name('home-visit.index');
        Route::get('/home-visit/{visit}', [BkHomeVisitController::class, 'show'])->name('home-visit.show');
        Route::get('/parent-call', [BkParentCallController::class, 'index'])->name('parent-call.index');
        Route::get('/parent-call/{parentCall}', [BkParentCallController::class, 'show'])->name('parent-call.show');
        Route::get('/sociometry', [BkSociometryController::class, 'index'])->name('sociometry.index');
        Route::get('/sociometry/{sociometry}', [BkSociometryController::class, 'show'])->name('sociometry.show');
        Route::post('/sociometry/{sociometry}/response', [BkSociometryController::class, 'storeResponse'])->name('sociometry.response');
        Route::get('/psikotes', [BkPsychologicalTestController::class, 'index'])->name('psychological.index');
        Route::get('/psikotes/ambil/{slug}', [BkPsychologicalTestController::class, 'takeTest'])->name('psychological.take');
        Route::post('/psikotes/ambil/{slug}', [BkPsychologicalTestController::class, 'submitTest'])->name('psychological.submit');
        Route::get('/psikotes/hasil/{result}', [BkPsychologicalTestController::class, 'result'])->name('psychological.result');
        Route::get('/akpd', [BkAkpdController::class, 'index'])->name('akpd.index');
        Route::get('/akpd/{akpd}', [BkAkpdController::class, 'show'])->name('akpd.show');
        Route::get('/akpd/{akpd}/ambil', [BkAkpdController::class, 'take'])->name('akpd.take');
        Route::post('/akpd/{akpd}/ambil', [BkAkpdController::class, 'submit'])->name('akpd.submit');
        Route::get('/rpl', [BkRplController::class, 'index'])->name('rpl.index');
        Route::get('/rpl/{rpl}', [BkRplController::class, 'show'])->name('rpl.show');
        Route::get('/case-conference', [BkCaseConferenceController::class, 'index'])->name('case-conference.index');
        Route::get('/case-conference/{caseConference}', [BkCaseConferenceController::class, 'show'])->name('case-conference.show');
        Route::get('/referral', [BkReferralController::class, 'index'])->name('referral.index');
        Route::get('/referral/{referral}', [BkReferralController::class, 'show'])->name('referral.show');

        // Fitur Lengkap / Operasional Manajemen BK (Admin BK: Koordinator BK, Kepala Sekolah, Wakasek Kesiswaan, Guru BK, serta Super Admin/Admin)
        Route::middleware('role:super_admin,admin,koordinator_bk,kepala_sekolah,wakasek_kesiswaan,guru_bk,bk')->group(function () {
            // Sesi Konseling
            Route::get('/konseling/create', [BkCounselingSessionController::class, 'create'])->name('counseling.create');
            Route::post('/konseling', [BkCounselingSessionController::class, 'store'])->name('counseling.store');
            Route::get('/konseling/{session}/edit', [BkCounselingSessionController::class, 'edit'])->name('counseling.edit');
            Route::put('/konseling/{session}', [BkCounselingSessionController::class, 'update'])->name('counseling.update');
            Route::delete('/konseling/{session}', [BkCounselingSessionController::class, 'destroy'])->name('counseling.destroy');
            Route::get('/konseling/{session}/print', [BkCounselingSessionController::class, 'printOfficial'])->name('counseling.print');

            // Kunjungan Rumah (Home Visit)
            Route::get('/home-visit/create', [BkHomeVisitController::class, 'create'])->name('home-visit.create');
            Route::post('/home-visit', [BkHomeVisitController::class, 'store'])->name('home-visit.store');
            Route::get('/home-visit/{visit}/edit', [BkHomeVisitController::class, 'edit'])->name('home-visit.edit');
            Route::put('/home-visit/{visit}', [BkHomeVisitController::class, 'update'])->name('home-visit.update');
            Route::delete('/home-visit/{visit}', [BkHomeVisitController::class, 'destroy'])->name('home-visit.destroy');
            Route::get('/home-visit/{visit}/print', [BkHomeVisitController::class, 'printOfficial'])->name('home-visit.print');

            // Pemanggilan Orang Tua
            Route::get('/parent-call/create', [BkParentCallController::class, 'create'])->name('parent-call.create');
            Route::post('/parent-call', [BkParentCallController::class, 'store'])->name('parent-call.store');
            Route::get('/parent-call/{parentCall}/edit', [BkParentCallController::class, 'edit'])->name('parent-call.edit');
            Route::put('/parent-call/{parentCall}', [BkParentCallController::class, 'update'])->name('parent-call.update');
            Route::delete('/parent-call/{parentCall}', [BkParentCallController::class, 'destroy'])->name('parent-call.destroy');
            Route::get('/parent-call/{parentCall}/print-letter', [BkParentCallController::class, 'printLetter'])->name('parent-call.print-letter');
            Route::get('/parent-call/{parentCall}/print-berita-acara', [BkParentCallController::class, 'printBeritaAcara'])->name('parent-call.print-berita-acara');

            // Sosiometri
            Route::get('/sociometry/create', [BkSociometryController::class, 'create'])->name('sociometry.create');
            Route::post('/sociometry', [BkSociometryController::class, 'store'])->name('sociometry.store');
            Route::delete('/sociometry/{sociometry}', [BkSociometryController::class, 'destroy'])->name('sociometry.destroy');
            Route::get('/sociometry/{sociometry}/print', [BkSociometryController::class, 'print'])->name('sociometry.print');

            // Psikotes & Profil Kelas
            Route::get('/psikotes/rekap-kelas', [BkPsychologicalTestController::class, 'classReport'])->name('psychological.class-report');
            Route::get('/psikotes/hasil/{result}/print', [BkPsychologicalTestController::class, 'printResult'])->name('psychological.print');

            // AKPD Analisis
            Route::get('/akpd/{akpd}/cetak-analisis', [BkAkpdController::class, 'printAnalysis'])->name('akpd.print-analysis');

            // RPL BK
            Route::get('/rpl/create', [BkRplController::class, 'create'])->name('rpl.create');
            Route::post('/rpl', [BkRplController::class, 'store'])->name('rpl.store');
            Route::get('/rpl/{rpl}/edit', [BkRplController::class, 'edit'])->name('rpl.edit');
            Route::put('/rpl/{rpl}', [BkRplController::class, 'update'])->name('rpl.update');
            Route::delete('/rpl/{rpl}', [BkRplController::class, 'destroy'])->name('rpl.destroy');
            Route::get('/rpl/{rpl}/print', [BkRplController::class, 'print'])->name('rpl.print');

            // Konferensi Kasus (Case Conference)
            Route::get('/case-conference/create', [BkCaseConferenceController::class, 'create'])->name('case-conference.create');
            Route::post('/case-conference', [BkCaseConferenceController::class, 'store'])->name('case-conference.store');
            Route::get('/case-conference/{caseConference}/edit', [BkCaseConferenceController::class, 'edit'])->name('case-conference.edit');
            Route::put('/case-conference/{caseConference}', [BkCaseConferenceController::class, 'update'])->name('case-conference.update');
            Route::delete('/case-conference/{caseConference}', [BkCaseConferenceController::class, 'destroy'])->name('case-conference.destroy');
            Route::get('/case-conference/{caseConference}/print', [BkCaseConferenceController::class, 'print'])->name('case-conference.print');

            // Alih Tangan Kasus (Referral)
            Route::get('/referral/create', [BkReferralController::class, 'create'])->name('referral.create');
            Route::post('/referral', [BkReferralController::class, 'store'])->name('referral.store');
            Route::get('/referral/{referral}/edit', [BkReferralController::class, 'edit'])->name('referral.edit');
            Route::put('/referral/{referral}', [BkReferralController::class, 'update'])->name('referral.update');
            Route::delete('/referral/{referral}', [BkReferralController::class, 'destroy'])->name('referral.destroy');
            Route::get('/referral/{referral}/print', [BkReferralController::class, 'print'])->name('referral.print');
        });
    });

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

    // Modul Presensi Ibadah & Kegiatan Keagamaan (Dhuha, Dzuhur/Jum'at, Keputrian)
    Route::prefix('worship')->name('worship.')->group(function () {
        Route::get('/{activity}', [WorshipAttendanceController::class, 'index'])->name('index');
        Route::put('/{activity}/{id}', [WorshipAttendanceController::class, 'update'])->name('update');
        Route::delete('/{activity}/{id}', [WorshipAttendanceController::class, 'destroy'])->name('destroy');
        Route::post('/{activity}/scan', [WorshipAttendanceController::class, 'scan'])->name('scan');
        Route::post('/{activity}/selfie', [WorshipAttendanceController::class, 'storeSelfie'])->name('selfie');
        Route::post('/{activity}/manual', [WorshipAttendanceController::class, 'storeManual'])->name('manual');
        Route::get('/{activity}/report', [WorshipAttendanceController::class, 'report'])->name('report');
        Route::get('/{activity}/print', [WorshipAttendanceController::class, 'printReport'])->name('print');
        Route::get('/{activity}/report/print', [WorshipAttendanceController::class, 'printReport'])->name('report.print');
    });

    // Modul Presensi Ekstrakurikuler (Metode Sama dengan Presensi Shalat Dhuha)
    Route::prefix('extracurricular')->name('extracurricular.')->group(function () {
        Route::get('/', [ExtracurricularAttendanceController::class, 'index'])->name('index');
        Route::post('/scan', [ExtracurricularAttendanceController::class, 'scan'])->name('scan');
        Route::post('/selfie', [ExtracurricularAttendanceController::class, 'storeSelfie'])->name('selfie');
        Route::post('/manual', [ExtracurricularAttendanceController::class, 'storeManual'])->name('manual');
        Route::put('/{id}', [ExtracurricularAttendanceController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExtracurricularAttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/report', [ExtracurricularAttendanceController::class, 'report'])->name('report');
        Route::get('/print', [ExtracurricularAttendanceController::class, 'printReport'])->name('print');
        Route::post('/{id}/schedule', [ExtracurricularAttendanceController::class, 'updateSchedule'])->name('schedule.update');
        Route::post('/{id}/members', [ExtracurricularAttendanceController::class, 'addMember'])->name('members.add');
        Route::put('/{id}/members/{studentId}/role', [ExtracurricularAttendanceController::class, 'updateMemberRole'])->name('members.role');
        Route::delete('/{id}/members/{studentId}', [ExtracurricularAttendanceController::class, 'removeMember'])->name('members.remove');
    });

    // Modul Buku Kontrol Digital Setoran Hafalan Al-Qur'an (Tahfidz Juz 30)
    Route::prefix('tahfidz')->name('tahfidz.')->group(function () {
        Route::get('/', [QuranTahfidzController::class, 'index'])->name('index');
        Route::get('/create', [QuranTahfidzController::class, 'create'])->name('create');
        Route::post('/', [QuranTahfidzController::class, 'store'])->name('store');
        Route::get('/{tahfidz}/edit', [QuranTahfidzController::class, 'edit'])->name('edit');
        Route::put('/{tahfidz}', [QuranTahfidzController::class, 'update'])->name('update');
        Route::get('/student/{student}', [QuranTahfidzController::class, 'student'])->name('student');
        Route::get('/student/{student}/print', [QuranTahfidzController::class, 'printStudent'])->name('print_student');
        Route::get('/student/{student}/print-card', [QuranTahfidzController::class, 'printStudent'])->name('student.print');
        Route::get('/custom-progress', [QuranTahfidzController::class, 'customProgressReport'])->name('custom_progress');
        Route::get('/student/{student}/custom-progress', [QuranTahfidzController::class, 'studentProgressReport'])->name('student_custom_progress');
        Route::get('/student/{student}/custom-progress/print', [QuranTahfidzController::class, 'printCustomProgressReport'])->name('print_custom_progress');
        Route::get('/report', [QuranTahfidzController::class, 'report'])->name('report');
        Route::get('/print', [QuranTahfidzController::class, 'printReport'])->name('print');
        Route::get('/print-report', [QuranTahfidzController::class, 'printReport'])->name('print_report');
        Route::get('/report/print', [QuranTahfidzController::class, 'printReport'])->name('report.print');
        Route::delete('/{tahfidz}', [QuranTahfidzController::class, 'destroy'])->name('destroy');
        Route::get('/quran', fn () => redirect()->route('quran.index'))->name('quran');
    });

    // Modul Al-Qur'an Digital & Terjemahan Lengkap 114 Surah (Bisa Diakses Seluruh Role)
    Route::prefix('quran')->name('quran.')->group(function () {
        Route::get('/', [QuranDigitalController::class, 'index'])->name('index');
        Route::get('/surah/{number}', [QuranDigitalController::class, 'show'])->name('show');
        Route::get('/api/surah/{number}', [QuranDigitalController::class, 'apiSurah'])->name('api.surah');
        Route::get('/api/tafsir/{number}', [QuranDigitalController::class, 'apiTafsir'])->name('api.tafsir');
    });

    // AI Analisis Perkembangan Siswa
    Route::get('/ai/analysis', [AiAnalysisController::class, 'index'])->name('ai.analysis');

    // Raport Digital Analisis Perkembangan AI (Dapat Diakses Seluruh Role)
    Route::prefix('raport-digital')->name('raport.')->group(function () {
        Route::get('/', [DigitalRaportController::class, 'index'])->name('index');
        Route::post('/generate-ai', [DigitalRaportController::class, 'generateAll'])->name('generate_ai');
        Route::post('/generate-single/{student}', [DigitalRaportController::class, 'generateSingle'])->name('generate_single');
        Route::get('/student/{student}', [DigitalRaportController::class, 'index'])->name('show');
        Route::get('/{student}/print', [DigitalRaportController::class, 'print'])->name('print');
    });

    // Laporan & Cetak Berkas
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/student/{student}', [ReportController::class, 'studentDossier'])->name('student.dossier');
        Route::get('/student/{student}/print', [ReportController::class, 'printStudentDossier'])->name('student.print');
        Route::get('/print/combined', [ReportController::class, 'printCombined'])->name('print.combined');
        Route::get('/print/class-recap', [ReportController::class, 'printClassRecap'])->name('print.class_recap');
        Route::get('/print/attendance', [ReportController::class, 'printAttendance'])->name('print.attendance');
        Route::get('/print/discipline', [ReportController::class, 'printDiscipline'])->name('print.discipline');
        Route::get('/print/habits', [ReportController::class, 'printHabits'])->name('print.habits');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export.csv');

        // Audit Log Aktivitas Sistem & Frekuensi Masuk Pengguna (Guru, Staff Administrasi, Siswa)
        Route::get('/user-activity', [ReportController::class, 'userActivityReport'])->name('user_activity');
        Route::get('/user-activity/print', [ReportController::class, 'printUserActivityReport'])->name('print_user_activity');
        Route::get('/user-activity/{user}', [ReportController::class, 'userDetailActivity'])->name('user_activity_detail');
    });

    // Shortcut Audit Log Aktivitas untuk Admin Dashboard
    Route::get('/admin/audit-logs/activity-report', [ReportController::class, 'userActivityReport'])->name('admin.user_activity');

    // Master Data Terpadu (Admin, Operator Dapodik, Kepala Urusan TU, Wakasek Kesiswaan & Kurikulum, Kepala Sekolah)
    Route::prefix('master')->name('master.')->middleware('role:admin,super_admin,operator_dapodik,operator,kepala_administrasi,wakasek_kesiswaan,wakasek_kurikulum,kepala_sekolah')->group(function () {
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
        Route::post('/students/sync-dapodik', [MasterDataController::class, 'syncDapodikIdentity'])->name('students.sync_dapodik');
        Route::get('/students', [MasterDataController::class, 'students'])->name('students');
        Route::post('/students', [MasterDataController::class, 'storeStudent'])->name('students.store');
        Route::put('/students/{student}', [MasterDataController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{student}', [MasterDataController::class, 'destroyStudent'])->name('students.destroy');
        Route::post('/students/{student}/record-face', [MasterDataController::class, 'recordStudentFace'])->name('students.record_face');
        Route::delete('/students/{student}/delete-face', [MasterDataController::class, 'deleteStudentFace'])->name('students.delete_face');

        // Data Guru beserta Role Akun Loginnya
        Route::get('/teachers/template', [MasterDataController::class, 'teachersTemplate'])->name('teachers.template');
        Route::post('/teachers/import', [MasterDataController::class, 'importTeachers'])->name('teachers.import');
        Route::get('/teachers', [MasterDataController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [MasterDataController::class, 'storeTeacher'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [MasterDataController::class, 'updateTeacher'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [MasterDataController::class, 'destroyTeacher'])->name('teachers.destroy');

        // Data Akun Pengguna
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Tahun Pelajaran
        Route::get('/academic-years/template', [MasterDataController::class, 'academicYearsTemplate'])->name('academic_years.template');
        Route::post('/academic-years/import', [MasterDataController::class, 'importAcademicYears'])->name('academic_years.import');
        Route::get('/academic-years', [MasterDataController::class, 'academicYears'])->name('academic_years');
        Route::post('/academic-years', [MasterDataController::class, 'storeAcademicYear'])->name('academic_years.store');
        Route::put('/academic-years/{academicYear}', [MasterDataController::class, 'updateAcademicYear'])->name('academic_years.update');
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

        // Perangkat Scanner IoT (10 Mesin RFID & 10 Mesin Scan QR Code)
        Route::post('/devices', [MasterDataController::class, 'storeDevice'])->name('devices.store');
        Route::put('/devices/{device}', [MasterDataController::class, 'updateDevice'])->name('devices.update');
        Route::delete('/devices/{device}', [MasterDataController::class, 'destroyDevice'])->name('devices.destroy');
        Route::post('/devices/generate-default', [MasterDataController::class, 'generateDefaultDevices'])->name('devices.generate_default');

        // Data Master Ekstrakurikuler
        Route::get('/extracurriculars', [MasterDataController::class, 'extracurriculars'])->name('extracurriculars');
        Route::post('/extracurriculars', [MasterDataController::class, 'storeExtracurricular'])->name('extracurriculars.store');
        Route::put('/extracurriculars/{id}', [MasterDataController::class, 'updateExtracurricular'])->name('extracurriculars.update');
        Route::delete('/extracurriculars/{id}', [MasterDataController::class, 'destroyExtracurricular'])->name('extracurriculars.destroy');
        Route::post('/extracurriculars/{id}/members', [MasterDataController::class, 'addExtracurricularMember'])->name('extracurriculars.members.add');
        Route::put('/extracurriculars/{id}/members/{studentId}/role', [MasterDataController::class, 'updateExtracurricularMemberRole'])->name('extracurriculars.members.role');
        Route::delete('/extracurriculars/{id}/members/{studentId}', [MasterDataController::class, 'removeExtracurricularMember'])->name('extracurriculars.members.remove');
    });

    // Kelola Konten & Tampilan Website Sekolah (Menu Mandiri Terpisah - Multi-Role)
    Route::middleware(['auth'])->group(function () {
        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/website-content', [WebsiteContentController::class, 'index'])->name('website_content');
            Route::post('/website-content/hero', [WebsiteContentController::class, 'updateHero'])->name('website_content.hero');
            Route::post('/website-content/running-text', [WebsiteContentController::class, 'updateRunningText'])->name('website_content.running_text');
            Route::post('/website-content/headmaster', [WebsiteContentController::class, 'updateHeadmaster'])->name('website_content.headmaster');
            Route::post('/website-content/speech', [WebsiteContentController::class, 'storeSpeech'])->name('website_content.speech.store');
            Route::put('/website-content/speech/{id}', [WebsiteContentController::class, 'updateSpeech'])->name('website_content.speech.update');
            Route::delete('/website-content/speech/{id}', [WebsiteContentController::class, 'destroySpeech'])->name('website_content.speech.destroy');
            Route::post('/website-content/speech/{id}/activate', [WebsiteContentController::class, 'activateSpeech'])->name('website_content.speech.activate');
            Route::post('/website-content/vision-mission', [WebsiteContentController::class, 'updateVisionMission'])->name('website_content.vision_mission');
            Route::post('/website-content/media', [WebsiteContentController::class, 'updateMedia'])->name('website_content.media');
            Route::post('/website-content/agenda', [WebsiteContentController::class, 'storeAgenda'])->name('website_content.agenda.store');
            Route::put('/website-content/agenda/{id}', [WebsiteContentController::class, 'updateAgenda'])->name('website_content.agenda.update');
            Route::delete('/website-content/agenda/{id}', [WebsiteContentController::class, 'destroyAgenda'])->name('website_content.agenda.destroy');
            Route::post('/website-content/facility', [WebsiteContentController::class, 'storeFacility'])->name('website_content.facility.store');
            Route::put('/website-content/facility/{id}', [WebsiteContentController::class, 'updateFacility'])->name('website_content.facility.update');
            Route::delete('/website-content/facility/{id}', [WebsiteContentController::class, 'destroyFacility'])->name('website_content.facility.destroy');
            Route::post('/website-content/news', [WebsiteContentController::class, 'storeNews'])->name('website_content.news.store');
            Route::put('/website-content/news/{id}', [WebsiteContentController::class, 'updateNews'])->name('website_content.news.update');
            Route::delete('/website-content/news/{id}', [WebsiteContentController::class, 'destroyNews'])->name('website_content.news.destroy');
            Route::post('/website-content/news/upload-inline-image', [WebsiteContentController::class, 'uploadNewsInlineImage'])->name('website_content.news.upload_inline_image');
            Route::post('/website-content/staff', [WebsiteContentController::class, 'storeStaff'])->name('website_content.staff.store');
            Route::put('/website-content/staff/{id}', [WebsiteContentController::class, 'updateStaff'])->name('website_content.staff.update');
            Route::delete('/website-content/staff/{id}', [WebsiteContentController::class, 'destroyStaff'])->name('website_content.staff.destroy');
            Route::post('/website-content/staff/sync', [WebsiteContentController::class, 'syncStaffFromTeachers'])->name('website_content.staff.sync');
            Route::post('/website-content/reset', [WebsiteContentController::class, 'resetSection'])->name('website_content.reset');
        });

        // Direct alias for standalone menu
        Route::get('/website-content', [WebsiteContentController::class, 'index'])->name('website_content.index');
    });

    // Notifikasi
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read_all');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Manajemen Buku Tamu Umum Sekolah (Humas, TU, Administrator)
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('index');
        Route::get('/report/print', [GuestController::class, 'printReport'])->name('report_print');
        Route::get('/{guest}', [GuestController::class, 'show'])->name('show');
        Route::put('/{guest}', [GuestController::class, 'update'])->name('update');
        Route::patch('/{guest}/status', [GuestController::class, 'updateStatus'])->name('update_status');
        Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('destroy');
    });

    // Learning Management System (LMS SMA)
    Route::prefix('lms')->name('lms.')->group(function () {
        Route::get('/', [LmsController::class, 'index'])->name('index');
        Route::get('/courses/{courseClass}', [LmsController::class, 'course'])->name('course');

        // Materi
        Route::post('/courses/{courseClass}/materials', [LmsController::class, 'storeMaterial'])->name('materials.store');
        Route::post('/courses/{courseClass}/materials/ai-generate', [LmsController::class, 'aiGenerateMaterial'])->name('materials.ai_generate');
        Route::get('/courses/{courseClass}/materials/{material}', [LmsController::class, 'showMaterial'])->name('materials.show');
        Route::get('/courses/{courseClass}/materials/{material}/preview', [LmsController::class, 'previewMaterialDocument'])->name('materials.preview');
        Route::put('/courses/{courseClass}/materials/{material}', [LmsController::class, 'updateMaterial'])->name('materials.update');
        Route::post('/courses/{courseClass}/materials/{material}/read', [LmsController::class, 'markMaterialAsRead'])->name('materials.read');
        Route::post('/courses/{courseClass}/materials/{material}/video-questions', [LmsController::class, 'storeVideoQuestion'])->name('materials.video_questions.store');
        Route::delete('/courses/{courseClass}/materials/{material}/video-questions/{questionIndex}', [LmsController::class, 'destroyVideoQuestion'])->name('materials.video_questions.destroy');
        Route::delete('/courses/{courseClass}/materials/{material}', [LmsController::class, 'destroyMaterial'])->name('materials.destroy');

        // Tugas (Assignments & PR)
        Route::post('/courses/{courseClass}/assignments', [LmsController::class, 'storeAssignment'])->name('assignments.store');
        Route::post('/courses/{courseClass}/assignments/ai-generate', [LmsController::class, 'aiGenerateAssignment'])->name('assignments.ai_generate');
        Route::get('/courses/{courseClass}/assignments/{assignment}', [LmsController::class, 'showAssignment'])->name('assignments.show');
        Route::get('/courses/{courseClass}/assignments/{assignment}/export-excel', [LmsController::class, 'exportAssignmentExcel'])->name('assignments.export_excel');
        Route::get('/courses/{courseClass}/assignments/{assignment}/print-pdf', [LmsController::class, 'printAssignmentPdf'])->name('assignments.print_pdf');
        Route::put('/courses/{courseClass}/assignments/{assignment}', [LmsController::class, 'updateAssignment'])->name('assignments.update');
        Route::post('/courses/{courseClass}/assignments/{assignment}/submit', [LmsController::class, 'submitAssignment'])->name('assignments.submit');
        Route::put('/courses/{courseClass}/assignments/{assignment}/submissions/{submission}/grade', [LmsController::class, 'gradeSubmission'])->name('assignments.grade');
        Route::delete('/courses/{courseClass}/assignments/{assignment}', [LmsController::class, 'destroyAssignment'])->name('assignments.destroy');

        // Kuis & Ujian (CBT)
        Route::post('/courses/{courseClass}/quizzes', [LmsController::class, 'storeQuiz'])->name('quizzes.store');
        Route::post('/courses/{courseClass}/quizzes/ai-generate', [LmsController::class, 'aiGenerateCbt'])->name('quizzes.ai_generate');
        Route::get('/courses/{courseClass}/quizzes/{quiz}', [LmsController::class, 'showQuiz'])->name('quizzes.show');
        Route::get('/courses/{courseClass}/quizzes/{quiz}/export-excel', [LmsController::class, 'exportQuizExcel'])->name('quizzes.export_excel');
        Route::get('/courses/{courseClass}/quizzes/{quiz}/print-pdf', [LmsController::class, 'printQuizPdf'])->name('quizzes.print_pdf');
        Route::put('/courses/{courseClass}/quizzes/{quiz}', [LmsController::class, 'updateQuiz'])->name('quizzes.update');
        Route::get('/courses/{courseClass}/quizzes/{quiz}/kisi-kisi', [LmsController::class, 'kisiKisiQuiz'])->name('quizzes.kisi_kisi');
        Route::get('/courses/{courseClass}/quizzes/{quiz}/kartu-soal', [LmsController::class, 'kartuSoalQuiz'])->name('quizzes.kartu_soal');
        Route::post('/courses/{courseClass}/quizzes/{quiz}/questions', [LmsController::class, 'storeQuizQuestion'])->name('quizzes.questions.store');
        Route::put('/courses/{courseClass}/quizzes/{quiz}/questions/{question}', [LmsController::class, 'updateQuizQuestion'])->name('quizzes.questions.update');
        Route::post('/courses/{courseClass}/quizzes/{quiz}/questions/ai-generate', [LmsController::class, 'aiGenerateQuestions'])->name('quizzes.questions.ai_generate');
        Route::delete('/courses/{courseClass}/quizzes/{quiz}/questions/{question}', [LmsController::class, 'destroyQuizQuestion'])->name('quizzes.questions.destroy');
        Route::post('/editor/upload-image', [LmsController::class, 'uploadEditorImage'])->name('editor.upload_image');
        Route::get('/quran/ayah', [LmsController::class, 'getQuranAyah'])->name('quran.ayah');
        Route::get('/courses/{courseClass}/quizzes/{quiz}/take', [LmsController::class, 'takeQuiz'])->name('quizzes.take');
        Route::post('/courses/{courseClass}/quizzes/{quiz}/submit', [LmsController::class, 'submitQuiz'])->name('quizzes.submit');
        Route::put('/courses/{courseClass}/quizzes/{quiz}/attempts/{attempt}/grade', [LmsController::class, 'gradeAttempt'])->name('quizzes.grade_attempt');
        Route::delete('/courses/{courseClass}/quizzes/{quiz}', [LmsController::class, 'destroyQuiz'])->name('quizzes.destroy');

        // Presensi Mapel (Manual / Selfie)
        Route::post('/courses/{courseClass}/attendances', [LmsController::class, 'storeAttendance'])->name('attendances.store');
        Route::get('/courses/{courseClass}/attendances/print-recap', [LmsController::class, 'printCourseAttendanceRecap'])->name('attendances.print_recap');
        Route::get('/courses/{courseClass}/attendances/{attendance}', [LmsController::class, 'showAttendance'])->name('attendances.show');
        Route::put('/courses/{courseClass}/attendances/{attendance}', [LmsController::class, 'updateAttendance'])->name('attendances.update');
        Route::get('/courses/{courseClass}/attendances/{attendance}/print', [LmsController::class, 'printAttendance'])->name('attendances.print');
        Route::post('/courses/{courseClass}/attendances/{attendance}/toggle', [LmsController::class, 'toggleAttendanceSession'])->name('attendances.toggle');
        Route::post('/courses/{courseClass}/attendances/{attendance}/manual', [LmsController::class, 'recordManualAttendance'])->name('attendances.manual');
        Route::post('/courses/{courseClass}/attendances/{attendance}/selfie', [LmsController::class, 'recordSelfieAttendance'])->name('attendances.selfie');
        Route::delete('/courses/{courseClass}/attendances/{attendance}', [LmsController::class, 'destroyAttendance'])->name('attendances.destroy');

        // KKTP Mapel (Dapat diedit oleh Guru Pengampu & Kurikulum)
        Route::put('/courses/{courseClass}/kktp', [LmsController::class, 'updateKktp'])->name('kktp.update');

        // Pemantauan Nilai & Rekap Gradebook
        Route::get('/courses/{courseClass}/grades', [LmsController::class, 'gradebook'])->name('grades');
        Route::post('/courses/{courseClass}/grades/weights', [LmsController::class, 'updateGradeWeights'])->name('grades.weights');
        Route::get('/courses/{courseClass}/grades/export', [LmsController::class, 'exportGrades'])->name('grades.export');
        Route::get('/courses/{courseClass}/grades/export-excel', [LmsController::class, 'exportGradesExcel'])->name('grades.export_excel');
        Route::get('/courses/{courseClass}/grades/print-pdf', [LmsController::class, 'printGradesPdf'])->name('grades.print_pdf');

        // Forum Diskusi
        Route::post('/courses/{courseClass}/discussions', [LmsController::class, 'storeDiscussion'])->name('discussions.store');
        Route::post('/courses/{courseClass}/discussions/{discussion}/reply', [LmsController::class, 'replyDiscussion'])->name('discussions.reply');
        Route::post('/courses/{courseClass}/discussions/{discussion}/pin', [LmsController::class, 'togglePinDiscussion'])->name('discussions.pin');
        Route::delete('/courses/{courseClass}/discussions/{discussion}', [LmsController::class, 'destroyDiscussion'])->name('discussions.destroy');
        // Salin, Import & Export Konten Lintas Kelas (Materi, Tugas, Kuis/CBT)
        Route::get('/courses/{courseClass}/source-contents/{sourceClass}', [LmsController::class, 'getSourceClassContents'])->name('source_contents');
        Route::post('/courses/{courseClass}/import-from-class', [LmsController::class, 'importFromClass'])->name('import_from_class');
        Route::post('/courses/{courseClass}/copy-to-classes', [LmsController::class, 'copyItemToClasses'])->name('copy_to_classes');
        Route::get('/courses/{courseClass}/export-package', [LmsController::class, 'exportPackage'])->name('export_package');
        Route::post('/courses/{courseClass}/import-package', [LmsController::class, 'importPackage'])->name('import_package');

        // Tatap Muka Virtual (Zoom Meet)
        Route::post('/courses/{courseClass}/zoom', [LmsController::class, 'storeZoomMeeting'])->name('zoom.store');
        Route::post('/courses/{courseClass}/zoom/{meeting}/toggle-status', [LmsController::class, 'toggleZoomMeetingStatus'])->name('zoom.toggle_status');
        Route::get('/courses/{courseClass}/zoom/{meeting}/join', [LmsController::class, 'joinZoomMeeting'])->name('zoom.join');
        Route::delete('/courses/{courseClass}/zoom/{meeting}', [LmsController::class, 'destroyZoomMeeting'])->name('zoom.destroy');

        // Fitur Keamanan Integritas Pembelajaran Siswa (Anti-Minimize, Anti-Tab Switch, Buka Blokir Guru & Admin, Toggle On/Off)
        Route::post('/courses/{courseClass}/security/violation', [LmsController::class, 'recordSecurityViolation'])->name('security.violation');
        Route::post('/courses/{courseClass}/security/unblock', [LmsController::class, 'unblockSecurityStudent'])->name('security.unblock');
        Route::post('/courses/{courseClass}/security/unblock-all', [LmsController::class, 'unblockAllSecurityStudents'])->name('security.unblock_all');
        Route::post('/courses/{courseClass}/security/toggle', [LmsController::class, 'toggleSecurityFeature'])->name('security.toggle');

        // Master Mapel & Penugasan Guru ke Kelas (Admin & Wakasek Kurikulum)
        Route::get('/master/subjects', [LmsController::class, 'subjectsIndex'])->name('master.subjects');
        Route::post('/master/subjects', [LmsController::class, 'storeSubject'])->name('master.subjects.store');
        Route::post('/master/subjects/generate-standard', [LmsController::class, 'generateStandardSubjects'])->name('master.subjects.generate_standard');
        Route::put('/master/subjects/{subject}', [LmsController::class, 'updateSubject'])->name('master.subjects.update');
        Route::delete('/master/subjects/{subject}', [LmsController::class, 'destroySubject'])->name('master.subjects.destroy');
        Route::post('/master/course-classes', [LmsController::class, 'storeCourseClass'])->name('master.course_classes.store');
        Route::put('/master/course-classes/{courseClass}', [LmsController::class, 'updateCourseClass'])->name('master.course_classes.update');
        Route::post('/master/course-classes/{courseClass}/toggle-status', [LmsController::class, 'toggleCourseClassStatus'])->name('master.course_classes.toggle_status');
        Route::delete('/master/course-classes/{courseClass}', [LmsController::class, 'destroyCourseClass'])->name('master.course_classes.destroy');

        // Health Check & Kesiapan Sistem Pembelajaran SMA1LE (Phase 1)
        Route::get('/health-check', [LmsController::class, 'systemHealth'])->name('health');

        // Perencanaan Pembelajaran & Modul Ajar (Phase 3)
        Route::prefix('planning')->name('planning.')->group(function () {
            Route::get('/', [CurriculumPlanController::class, 'index'])->name('index');
            Route::post('/outcomes', [CurriculumPlanController::class, 'storeOutcome'])->name('outcomes.store');
            Route::post('/objectives', [CurriculumPlanController::class, 'storeObjective'])->name('objectives.store');
            Route::post('/objectives/ai-generate', [CurriculumPlanController::class, 'aiGenerateObjective'])->name('objectives.ai_generate');
            Route::post('/lesson-plans', [CurriculumPlanController::class, 'storeLessonPlan'])->name('lesson_plans.store');
            Route::post('/lesson-plans/ai-generate', [CurriculumPlanController::class, 'aiGenerateLessonPlan'])->name('lesson_plans.ai_generate');
            Route::post('/lesson-plans/{plan}/submit', [CurriculumPlanController::class, 'submitLessonPlan'])->name('lesson_plans.submit');
            Route::post('/lesson-plans/{plan}/review', [CurriculumPlanController::class, 'reviewLessonPlan'])->name('lesson_plans.review');
            Route::get('/lesson-plans/{plan}/print', [CurriculumPlanController::class, 'printLessonPlan'])->name('lesson_plans.print');
        });

        // Jurnal Mengajar Harian Guru (Phase 4)
        Route::prefix('journals')->name('journals.')->group(function () {
            Route::get('/', [TeachingJournalController::class, 'index'])->name('index');
            Route::post('/', [TeachingJournalController::class, 'store'])->name('store');
            Route::delete('/{journal}', [TeachingJournalController::class, 'destroy'])->name('destroy');
            Route::post('/{journal}/verify', [TeachingJournalController::class, 'verify'])->name('verify');
            Route::get('/print', [TeachingJournalController::class, 'print'])->name('print');
        });

        // Asesmen, Bank Soal & Remedial (Phase 5)
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::get('/bank', [AssessmentController::class, 'bankIndex'])->name('bank');
            Route::post('/bank/questions', [AssessmentController::class, 'storeQuestion'])->name('bank.questions.store');
            Route::delete('/bank/questions/{question}', [AssessmentController::class, 'destroyQuestion'])->name('bank.questions.destroy');
            Route::post('/bank/ai-generate', [AssessmentController::class, 'aiGenerateQuestions'])->name('bank.ai_generate');
            Route::get('/kisi-kisi/print', [AssessmentController::class, 'printKisiKisi'])->name('kisi_kisi.print');
            Route::get('/remediation', [AssessmentController::class, 'remediationIndex'])->name('remediation');
            Route::post('/remediation', [AssessmentController::class, 'storeRemediationPlan'])->name('remediation.store');
            Route::post('/remediation/{plan}/complete', [AssessmentController::class, 'completeRemediationPlan'])->name('remediation.complete');
        });

        // Refleksi Pembelajaran Guru Pasca KBM (Phase 6)
        Route::prefix('reflections')->name('reflections.')->group(function () {
            Route::get('/', [ReflectionController::class, 'index'])->name('index');
            Route::post('/', [ReflectionController::class, 'store'])->name('store');
            Route::delete('/{reflection}', [ReflectionController::class, 'destroy'])->name('destroy');
            Route::post('/ai-generate', [ReflectionController::class, 'aiGenerateReflection'])->name('ai_generate');
            Route::get('/print', [ReflectionController::class, 'print'])->name('print');
        });

        // Portofolio Digital & Artefak Karya Siswa (Phase 6)
        Route::prefix('portfolios')->name('portfolios.')->group(function () {
            Route::get('/', [PortfolioController::class, 'index'])->name('index');
            Route::post('/', [PortfolioController::class, 'store'])->name('store');
            Route::delete('/{portfolio}', [PortfolioController::class, 'destroy'])->name('destroy');
            Route::post('/{portfolio}/verify', [PortfolioController::class, 'verify'])->name('verify');
            Route::get('/{portfolio}/print', [PortfolioController::class, 'print'])->name('print');
        });

        // Pengawasan & Supervisi Akademik Kurikulum (Phase 6)
        Route::prefix('supervision')->name('supervision.')->group(function () {
            Route::get('/', [SupervisionController::class, 'index'])->name('index');
            Route::post('/', [SupervisionController::class, 'store'])->name('store');
            Route::delete('/{supervision}', [SupervisionController::class, 'destroy'])->name('destroy');
            Route::get('/{supervision}/print', [SupervisionController::class, 'print'])->name('print');
        });

        // Pusat Layanan AI Kurikulum & Pedagogi SMA1LE (Phase 7)
        Route::prefix('ai-center')->name('ai.')->group(function () {
            Route::get('/', [AiCurriculumCenterController::class, 'index'])->name('index');
            Route::post('/generate-hots', [AiCurriculumCenterController::class, 'generateHots'])->name('generate_hots');
            Route::post('/generate-differentiated', [AiCurriculumCenterController::class, 'generateDifferentiated'])->name('generate_differentiated');
            Route::post('/analyze-diagnostic', [AiCurriculumCenterController::class, 'analyzeDiagnostic'])->name('analyze_diagnostic');
            Route::get('/test-connection', [AiCurriculumCenterController::class, 'testConnection'])->name('test_connection');
        });

        // Pusat Cetak & Arsip Dokumen Kurikulum (Phase 8)
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [CurriculumDocumentController::class, 'index'])->name('index');
            Route::get('/prota/print', [CurriculumDocumentController::class, 'printProta'])->name('prota.print');
            Route::get('/promes/print', [CurriculumDocumentController::class, 'printPromes'])->name('promes.print');
            Route::get('/atp/print', [CurriculumDocumentController::class, 'printAtp'])->name('atp.print');
            Route::get('/export/curriculum-matrix', [CurriculumDocumentController::class, 'exportCurriculumMatrix'])->name('export.matrix');
            Route::get('/export/journals', [CurriculumDocumentController::class, 'exportJournals'])->name('export.journals');
        });
    });

    // =========================================================================
    // UJIAN ONLINE (CBT - COMPUTER-BASED TEST)
    // =========================================================================
    Route::prefix('cbt')->name('cbt.')->group(function () {
        // Portal Siswa
        Route::get('/portal', [CbtStudentController::class, 'index'])->name('student.index');
        Route::post('/exams/{exam}/start', [CbtStudentController::class, 'start'])->name('student.start');
        Route::get('/exams/{exam}/take', [CbtStudentController::class, 'take'])->name('student.take');
        Route::post('/exams/{exam}/save-answer', [CbtStudentController::class, 'saveAnswer'])->name('student.save_answer');
        Route::post('/exams/{exam}/report-violation', [CbtStudentController::class, 'reportViolation'])->name('student.report_violation');
        Route::post('/exams/{exam}/finish', [CbtStudentController::class, 'finish'])->name('student.finish');
        Route::get('/exams/{exam}/result', [CbtStudentController::class, 'result'])->name('student.result');

        // Manajemen Ujian (Guru, Wali Kelas, Kurikulum, Admin)
        Route::get('/', [CbtController::class, 'index'])->name('index');
        Route::get('/exams/create', [CbtController::class, 'create'])->name('exams.create');
        Route::post('/exams', [CbtController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}', [CbtController::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/edit', [CbtController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{exam}', [CbtController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [CbtController::class, 'destroy'])->name('exams.destroy');

        // Manajemen Butir Soal (Manual, AI & Import)
        Route::post('/exams/{exam}/questions', [CbtController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::delete('/exams/{exam}/questions/{question}', [CbtController::class, 'destroyQuestion'])->name('exams.questions.destroy');
        Route::post('/exams/{exam}/ai-generate', [CbtController::class, 'generateAi'])->name('exams.ai_generate');
        Route::post('/exams/{exam}/import', [CbtController::class, 'importQuestions'])->name('exams.import');

        // Monitoring & Kontrol Pelanggaran
        Route::get('/exams/{exam}/monitor', [CbtController::class, 'monitor'])->name('exams.monitor');
        Route::post('/exams/{exam}/sessions/{session}/unblock', [CbtController::class, 'unblockStudent'])->name('exams.unblock');

        // Penilaian & Rekap Nilai
        Route::get('/exams/{exam}/results', [CbtController::class, 'results'])->name('exams.results');
        Route::post('/exams/{exam}/sessions/{session}/grade-essay', [CbtController::class, 'gradeEssay'])->name('exams.grade_essay');

        // Dokumen Kurikulum & Administrasi Ujian Resmi
        Route::get('/exams/{exam}/documents/kisi-kisi', [CbtController::class, 'printKisiKisi'])->name('documents.kisi_kisi');
        Route::get('/exams/{exam}/documents/kartu-soal', [CbtController::class, 'printKartuSoal'])->name('documents.kartu_soal');
        Route::get('/exams/{exam}/documents/kartu-peserta', [CbtController::class, 'printKartuPeserta'])->name('documents.kartu_peserta');
        Route::get('/exams/{exam}/documents/daftar-hadir', [CbtController::class, 'printDaftarHadir'])->name('documents.daftar_hadir');
        Route::get('/exams/{exam}/documents/berita-acara', [CbtController::class, 'printBeritaAcara'])->name('documents.berita_acara');
    });

    // Perpustakaan Terpadu (Fisik, E-Book Digital Flip HTML, Sirkulasi & Presensi Kunjungan)
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');

        // Katalog Buku
        Route::get('/books', [LibraryController::class, 'books'])->name('books.index');
        Route::get('/books/create', [LibraryController::class, 'createBook'])->name('books.create');
        Route::post('/books', [LibraryController::class, 'storeBook'])->name('books.store');
        Route::match(['get', 'post'], '/books/print-labels', [LibraryController::class, 'printBatchLabels'])->name('books.print_labels');
        Route::get('/books/{book}', [LibraryController::class, 'showBook'])->name('books.show');
        Route::get('/books/{book}/edit', [LibraryController::class, 'editBook'])->name('books.edit');
        Route::put('/books/{book}', [LibraryController::class, 'updateBook'])->name('books.update');
        Route::delete('/books/{book}', [LibraryController::class, 'destroyBook'])->name('books.destroy');
        Route::get('/books/{book}/labels', [LibraryController::class, 'printBookLabels'])->name('books.labels');

        // E-Book Reader Flip HTML & Safe PDF Stream
        Route::get('/books/{book}/flipbook', [LibraryController::class, 'flipbook'])->name('books.flipbook');
        Route::get('/books/{book}/stream-pdf', [LibraryController::class, 'streamPdf'])->name('books.stream_pdf');

        // Pengajuan Peminjaman Mandiri oleh Pengguna (Guru / Siswa)
        Route::post('/books/{book}/request', [LibraryController::class, 'requestLoan'])->name('books.request');
        Route::get('/my-loans', [LibraryController::class, 'myLoans'])->name('loans.my');
        Route::post('/my-loans/{loan}/cancel', [LibraryController::class, 'cancelMyLoan'])->name('loans.cancel');
        Route::get('/my-card', [LibraryController::class, 'myCard'])->name('members.my-card');

        // Sirkulasi Peminjaman & Pengembalian
        Route::get('/loans', [LibraryController::class, 'loans'])->name('loans.index');
        Route::post('/loans', [LibraryController::class, 'storeLoan'])->name('loans.store');
        Route::get('/loans/{loan}/print-receipt', [LibraryController::class, 'printLoanReceipt'])->name('loans.print_receipt');
        Route::post('/loans/{loan}/approve', [LibraryController::class, 'approveLoan'])->name('loans.approve');
        Route::post('/loans/{loan}/reject', [LibraryController::class, 'rejectLoan'])->name('loans.reject');
        Route::post('/loans/{loan}/return', [LibraryController::class, 'returnLoan'])->name('loans.return');
        Route::post('/loans/{loan}/extend', [LibraryController::class, 'extendLoan'])->name('loans.extend');

        // Data Anggota Perpustakaan
        Route::get('/members', [LibraryController::class, 'members'])->name('members.index');
        Route::post('/members', [LibraryController::class, 'storeMember'])->name('members.store');
        Route::get('/members/{member}/print-card', [LibraryController::class, 'printMemberCard'])->name('members.print_card');
        Route::get('/members/{member}/clearance', [LibraryController::class, 'printClearanceCertificate'])->name('members.clearance');
        Route::put('/members/{member}', [LibraryController::class, 'updateMember'])->name('members.update');
        Route::delete('/members/{member}', [LibraryController::class, 'destroyMember'])->name('members.destroy');
        Route::match(['get', 'post'], '/members/sync', [LibraryController::class, 'syncMembersFromMaster'])->name('members.sync');

        // Buku Tamu & Presensi Pengunjung Perpustakaan
        Route::get('/visitors', [LibraryController::class, 'visitors'])->name('visitors.index');
        Route::post('/visitors', [LibraryController::class, 'storeVisitor'])->name('visitors.store');

        // Rapor & Rangking Pengunjung (Offline & Online) + Piagam Penghargaan Keren
        Route::get('/leaderboard', [LibraryController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/certificate/{member}', [LibraryController::class, 'certificate'])->name('certificate');

        // Laporan Lengkap Inventaris, Sirkulasi & Status Buku
        Route::get('/reports', [LibraryController::class, 'reports'])->name('reports.index');
        Route::get('/reports/print', [LibraryController::class, 'printReport'])->name('reports.print');
        Route::get('/reports/export', [LibraryController::class, 'exportReport'])->name('reports.export');
        Route::get('/reports/visitors', [LibraryController::class, 'visitorReports'])->name('reports.visitors');
        Route::get('/reports/visitors/print', [LibraryController::class, 'printVisitorReports'])->name('reports.visitors_print');
        Route::get('/reports/loans', [LibraryController::class, 'loansReports'])->name('reports.loans');
        Route::get('/reports/loans/print', [LibraryController::class, 'printLoansReports'])->name('reports.loans_print');

        // Pengaturan & Penugasan Pengelola Perpustakaan (Admin)
        Route::get('/settings', [LibraryController::class, 'settings'])->name('settings');
        Route::post('/settings', [LibraryController::class, 'updateSettings'])->name('settings.update');
    });

    // Rapor Kurikulum Nasional (Kurnas) SMA Standar e-Rapor 2025
    Route::prefix('rapor-kurnas')->name('rapor_kurnas.')->group(function () {
        Route::get('/', [RaporKurnasController::class, 'index'])->name('index');

        // Tujuan Pembelajaran (TP)
        Route::get('/learning-goals', [RaporKurnasController::class, 'learningGoals'])->name('learning_goals');
        Route::post('/learning-goals', [RaporKurnasController::class, 'storeLearningGoal'])->name('learning_goals.store');
        Route::put('/learning-goals/{goal}', [RaporKurnasController::class, 'updateLearningGoal'])->name('learning_goals.update');
        Route::delete('/learning-goals/{goal}', [RaporKurnasController::class, 'destroyLearningGoal'])->name('learning_goals.destroy');

        // Input Nilai Mata Pelajaran (Guru Mapel)
        Route::get('/scores', [RaporKurnasController::class, 'scores'])->name('scores');
        Route::post('/scores', [RaporKurnasController::class, 'storeScores'])->name('scores.store');
        Route::post('/sync-course-classes', [RaporKurnasController::class, 'syncCourseClasses'])->name('sync_course_classes');
        Route::get('/scores/{courseClass}/template', [RaporKurnasController::class, 'exportScoresTemplate'])->name('scores.template');
        Route::post('/scores/{courseClass}/import', [RaporKurnasController::class, 'importScores'])->name('scores.import');

        // Nilai Ekstrakurikuler
        Route::get('/extracurriculars', [RaporKurnasController::class, 'extracurriculars'])->name('extracurriculars');
        Route::post('/extracurriculars', [RaporKurnasController::class, 'storeExtracurricularScores'])->name('extracurriculars.store');

        // Kokurikuler / Projek P5
        Route::get('/cocurricular', [RaporKurnasController::class, 'cocurricular'])->name('cocurricular');
        Route::post('/cocurricular/project', [RaporKurnasController::class, 'storeCocurricularProject'])->name('cocurricular.store_project');
        Route::post('/cocurricular/{project}/scores', [RaporKurnasController::class, 'storeCocurricularScores'])->name('cocurricular.store_scores');

        // Menu Wali Kelas
        Route::get('/homeroom', [RaporKurnasController::class, 'homeroom'])->name('homeroom');
        Route::post('/homeroom/{schoolClass}/sync-attendance', [RaporKurnasController::class, 'syncHomeroomAttendance'])->name('homeroom.sync_attendance');
        Route::post('/homeroom/{schoolClass}', [RaporKurnasController::class, 'storeHomeroom'])->name('homeroom.store');

        // Pusat Cetak Dokumen Rapor Resmi (Standar e-Rapor SMA 2025)
        Route::get('/print', [RaporKurnasController::class, 'printHub'])->name('print_hub');
        Route::get('/print/cover/{student}', [RaporKurnasController::class, 'printCover'])->name('print_cover');
        Route::get('/print/identity/{student}', [RaporKurnasController::class, 'printIdentity'])->name('print_identity');
        Route::get('/print/academic/{student}', [RaporKurnasController::class, 'printAcademic'])->name('print_academic');
        Route::get('/print/cocurricular/{student}', [RaporKurnasController::class, 'printCocurricular'])->name('print_cocurricular');
        Route::get('/print/full/{student}', [RaporKurnasController::class, 'printFull'])->name('print_full');
        Route::get('/print/leger/{schoolClass}', [RaporKurnasController::class, 'printLeger'])->name('print_leger');
        Route::get('/print/diploma/{student}', [RaporKurnasController::class, 'printDiplomaTranscript'])->name('print_diploma');
        Route::get('/print/buku-induk/{student}', [RaporKurnasController::class, 'printBukuInduk'])->name('print_buku_induk');

        // Status Penilaian & Audit Monitoring
        Route::get('/monitoring', [RaporKurnasController::class, 'statusMonitoring'])->name('monitoring');

        // Perkembangan Nilai Siswa & SNBP Tracker
        Route::get('/growth', [RaporKurnasController::class, 'growthTracker'])->name('growth');
        Route::post('/growth/snbp/{student}', [RaporKurnasController::class, 'updateSnbpEligibility'])->name('growth.update_snbp');

        // Mapel Gabungan IPA & IPS (Fase E Kelas X)
        Route::get('/combined-subjects', [RaporKurnasController::class, 'combinedSubjects'])->name('combined_subjects');
        Route::post('/combined-subjects', [RaporKurnasController::class, 'storeCombinedSubject'])->name('combined_subjects.store');
        Route::delete('/combined-subjects/{combinedSubject}', [RaporKurnasController::class, 'destroyCombinedSubject'])->name('combined_subjects.destroy');

        // Ijazah & Transkrip Kelulusan
        Route::get('/diploma', [RaporKurnasController::class, 'diplomaTranscripts'])->name('diploma');
        Route::post('/diploma', [RaporKurnasController::class, 'storeDiplomaTranscripts'])->name('diploma.store');
        Route::get('/diploma/template', [RaporKurnasController::class, 'exportDiplomaTemplate'])->name('diploma.template');
        Route::post('/diploma/import', [RaporKurnasController::class, 'importDiplomaData'])->name('diploma.import');

        // Portal Siswa / Rapor Mandiri
        Route::get('/my-report', [RaporKurnasController::class, 'myReport'])->name('my_report');

        // Transkrip & Buku Induk
        Route::get('/transcripts', [RaporKurnasController::class, 'transcripts'])->name('transcripts');

        // Pengaturan & Referensi (Admin / Kurikulum)
        Route::get('/settings', [RaporKurnasController::class, 'settings'])->name('settings');
        Route::post('/settings', [RaporKurnasController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/toggle-lock', [RaporKurnasController::class, 'toggleLock'])->name('toggle_lock');
        Route::post('/settings/groups', [RaporKurnasController::class, 'updateSubjectGroups'])->name('settings.update_groups');
    });

    // Aplikasi Bel Sekolah Terpadu & Murottal Al-Qur'an 114 Surah (Akses: Admin, Kepsek, Para Wakasek, Piket TU & Piket Guru)
    Route::prefix('school-bell')->name('school_bell.')->middleware('role:admin,super_admin,kepala_sekolah,wakasek_kesiswaan,kesiswaan,wakasek_kurikulum,kurikulum,wakasek_humas,humas,wakasek_sarpras,sarpras,tu_piket,guru_piket,petugas_piket')->group(function () {
        Route::get('/', [SchoolBellController::class, 'index'])->name('index');
        Route::get('/kiosk', [SchoolBellController::class, 'kiosk'])->name('kiosk');
        Route::post('/', [SchoolBellController::class, 'store'])->name('store');
        Route::post('/reset-defaults', [SchoolBellController::class, 'resetDefaults'])->name('reset_defaults');
        Route::post('/clear-all', [SchoolBellController::class, 'clearAll'])->name('clear_all');
        Route::put('/{schoolBell}', [SchoolBellController::class, 'update'])->name('update');
        Route::post('/{schoolBell}/toggle', [SchoolBellController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolBell}', [SchoolBellController::class, 'destroy'])->name('destroy');
        Route::get('/api/schedules', [SchoolBellController::class, 'schedulesJson'])->name('api.schedules');
        Route::get('/api/surahs', [SchoolBellController::class, 'surahsJson'])->name('api.surahs');
    });
});

// Storage fallback route (ensures photos & files are served reliably in all server environments)
Route::get('/storage/{path}', function (string $path) {
    $cleanPath = ltrim(preg_replace('/^(public\/|storage\/)+/', '', $path), '/');

    // Check possible storage locations
    $candidates = [
        storage_path('app/public/'.$cleanPath),
        public_path('storage/'.$cleanPath),
        storage_path('app/'.$cleanPath),
    ];

    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            $filePath = $candidate;
            break;
        }
    }

    $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));

    if ($filePath) {
        $size = filesize($filePath);

        // Detect real MIME type accurately via finfo or fallback to extension
        $mimeType = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);
            }
        }

        if (! $mimeType || $mimeType === 'text/plain' || $mimeType === 'application/octet-stream') {
            $mimeType = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                default => 'image/jpeg',
            };
        }

        // Auto-cache to public/storage if possible so LiteSpeed can serve it statically next time
        $publicTarget = public_path('storage/'.$cleanPath);
        if ($filePath !== $publicTarget && ! file_exists($publicTarget) && ! is_link(public_path('storage'))) {
            try {
                @mkdir(dirname($publicTarget), 0755, true);
                @copy($filePath, $publicTarget);
            } catch (Throwable) {
                // Ignore any permission issues
            }
        }

        return response(file_get_contents($filePath), 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) $size,
            'Cache-Control' => 'public, max-age=86400',
            'Content-Disposition' => 'inline',
        ]);
    }

    // If file is missing or corrupted, return appropriate SVG placeholder
    if (str_contains($cleanPath, 'kop')) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 160" width="1000" height="160">'
            .'<rect width="1000" height="160" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>'
            .'<text x="500" y="45" fill="#1e293b" font-size="20" font-family="serif" font-weight="bold" text-anchor="middle">PEMERINTAH DAERAH PROVINSI JAWA BARAT</text>'
            .'<text x="500" y="75" fill="#1e293b" font-size="22" font-family="serif" font-weight="900" text-anchor="middle">DINAS PENDIDIKAN - SMA NEGERI 1 LENGKONG</text>'
            .'<text x="500" y="105" fill="#475569" font-size="14" font-family="sans-serif" text-anchor="middle">Jalan Raya Tegalega, Kec. Lengkong, Kab. Sukabumi | sman1lengkong@gmail.com</text>'
            .'<line x1="40" y1="130" x2="960" y2="130" stroke="#0f172a" stroke-width="3"/>'
            .'<line x1="40" y1="136" x2="960" y2="136" stroke="#0f172a" stroke-width="1"/>'
            .'</svg>';
    } else {
        $label = str_contains($cleanPath, 'logo') || str_contains($cleanPath, 'branding') ? 'SMAN 1 LENGKONG' : 'FOTO PRESENSI';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">'
            .'<rect width="200" height="200" fill="#4f46e5" rx="20"/>'
            .'<circle cx="100" cy="75" r="35" fill="#c7d2fe"/>'
            .'<path d="M40 165 C40 130 70 120 100 120 C130 120 160 130 160 165 Z" fill="#c7d2fe"/>'
            .'<text x="100" y="185" fill="#ffffff" font-size="11" font-family="system-ui, sans-serif" font-weight="bold" text-anchor="middle">'.$label.'</text>'
            .'</svg>';
    }

    return response($svg, 200, [
        'Content-Type' => 'image/svg+xml; charset=utf-8',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.fallback');

// Adm. Ketatausahaan (SIKATAS) Ecosystem Routes
require __DIR__.'/tu.php';
