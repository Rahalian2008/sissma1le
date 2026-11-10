<?php

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentAiRaport;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WorshipAttendance;
use App\Services\AiStudentAnalysisService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    SchoolSetting::set('school_name', 'SMAN 1 Lengkong');
    SchoolSetting::set('school_city', 'Sukabumi');
    SchoolSetting::set('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
    SchoolSetting::set('headmaster_nip', '19680512 199403 1 004');
});

test('manual attendance form does not have preselected hadir radio for unrecorded students and empty bulk submit handles gracefully', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-TEST', 'grade' => 'X', 'major' => 'Umum']);

    $student = Student::create([
        'nis' => '99001',
        'nisn' => '0099001',
        'name' => 'Siswa Baru Belum Presensi',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('attendance.manual', [
        'class_id' => $class->id,
        'date' => Carbon::today()->toDateString(),
    ]));

    $response->assertOk();
    // Verify radio buttons are not forced checked
    $response->assertDontSee('value="HADIR" checked', false);

    // Submitting bulk without any selected statuses redirects back with warning
    $bulkResponse = $this->actingAs($admin)->post(route('attendance.manual.bulk'), [
        'class_id' => $class->id,
        'date' => Carbon::today()->toDateString(),
        'statuses' => [],
    ]);

    $bulkResponse->assertRedirect();
    $bulkResponse->assertSessionHas('warning');
});

test('print reports have exactly two signers with swapped positions: kepala sekolah on left and wakasek kesiswaan on right', function () {
    $admin = User::where('role', 'admin')->first();
    $class = SchoolClass::first();

    $startDate = Carbon::now()->startOfMonth()->toDateString();
    $endDate = Carbon::now()->toDateString();

    // Ensure teacher admin exists as wakasek kesiswaan (Winda Dwi Atyani, S.Pd., M.Pd.)
    $kesiswaan = Teacher::getWakasekKesiswaan();
    $headmaster = Teacher::getHeadmaster();

    expect($headmaster['name'])->toBe('Drs. H. Sukardi, M.Pd.');
    expect($kesiswaan['name'])->toBe('Winda Dwi Atyani, S.Pd., M.Pd.');

    // 1. Cetak 1 Dokumen Terpadu
    $resCombined = $this->actingAs($admin)->get(route('reports.print.combined', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $resCombined->assertOk();
    $resCombined->assertSee('Wakasek Bid. Kesiswaan');
    $resCombined->assertSee('Kepala Sekolah');
    $resCombined->assertSee($headmaster['name']);
    $resCombined->assertSee('Winda Dwi Atyani, S.Pd., M.Pd.');
    // Check swapped position: Kepala Sekolah appears before Wakasek Bid. Kesiswaan in HTML
    $combinedHtml = $resCombined->getContent();
    expect(strpos($combinedHtml, 'Kepala Sekolah'))->toBeLessThan(strpos($combinedHtml, 'Wakasek Bid. Kesiswaan'));
    // Ensure Wali Kelas does NOT sign the official institutional sign-off in combined report
    $resCombined->assertDontSee('Wali Kelas '.($class?->name ?? ''));

    // 2. Cetak Presensi Siswa
    $resAtt = $this->actingAs($admin)->get(route('reports.print.attendance', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $resAtt->assertOk();
    $resAtt->assertSee('Wakasek Bid. Kesiswaan');
    $resAtt->assertSee('Kepala Sekolah');
    $resAtt->assertSee($headmaster['name']);
    $resAtt->assertSee('Winda Dwi Atyani, S.Pd., M.Pd.');
    $attHtml = $resAtt->getContent();
    expect(strpos($attHtml, 'Kepala Sekolah'))->toBeLessThan(strpos($attHtml, 'Wakasek Bid. Kesiswaan'));

    // 3. Cetak Tata Tertib
    $resDisc = $this->actingAs($admin)->get(route('reports.print.discipline', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $resDisc->assertOk();
    $resDisc->assertSee('Wakasek Bid. Kesiswaan');
    $resDisc->assertSee('Kepala Sekolah');
    $resDisc->assertSee($headmaster['name']);
    $resDisc->assertSee('Winda Dwi Atyani, S.Pd., M.Pd.');
    $discHtml = $resDisc->getContent();
    expect(strpos($discHtml, 'Kepala Sekolah'))->toBeLessThan(strpos($discHtml, 'Wakasek Bid. Kesiswaan'));

    // 4. Cetak 7 Kebiasaan
    $resHabits = $this->actingAs($admin)->get(route('reports.print.habits', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $resHabits->assertOk();
    $resHabits->assertSee('Wakasek Bid. Kesiswaan');
    $resHabits->assertSee('Kepala Sekolah');
    $resHabits->assertSee($headmaster['name']);
    $resHabits->assertSee('Winda Dwi Atyani, S.Pd., M.Pd.');
    $habitsHtml = $resHabits->getContent();
    expect(strpos($habitsHtml, 'Kepala Sekolah'))->toBeLessThan(strpos($habitsHtml, 'Wakasek Bid. Kesiswaan'));

    // 5. Cetak Rekap Presensi Kelas
    $resClassRecap = $this->actingAs($admin)->get(route('reports.print.class_recap', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $resClassRecap->assertOk();
    $resClassRecap->assertSee('Wakasek Bid. Kesiswaan');
    $resClassRecap->assertSee('Kepala Sekolah');
    $resClassRecap->assertSee($headmaster['name']);
    $resClassRecap->assertSee('Winda Dwi Atyani, S.Pd., M.Pd.');
    $classRecapHtml = $resClassRecap->getContent();
    expect(strpos($classRecapHtml, 'Kepala Sekolah'))->toBeLessThan(strpos($classRecapHtml, 'Wakasek Bid. Kesiswaan'));
});

test('digital raport displays 0.0% and Belum Terlaksana when student has 0 attendances', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::create([
        'name' => 'X-ZEROATT-1',
        'grade' => 'X',
        'major' => 'Umum',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Siswa Nol Presensi',
        'nis' => '770001',
        'nisn' => '00770001',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->get(route('raport.index', ['student_id' => $student->id]));
    $response->assertOk();
    $response->assertSee('0.0%');
    $response->assertSee('Belum Terlaksana');
    $response->assertSee('Ringkasan Komprehensif');
    $response->assertSee('Karakter Profil Pelajar Pancasila');
    $response->assertSee('Penilaian Terpadu Siswa');
    $response->assertSee('Tren Perkembangan & Potensi', false);
    $response->assertDontSee('Analisis Masalah & Area Penguatan', false);
    $response->assertDontSee('Pemecahan Masalah & Rekomendasi Solusi', false);

    // Test parent portal for the same student
    $portalResponse = $this->get(route('parent.portal', ['nisn' => $student->nisn]));
    $portalResponse->assertOk();
    $portalResponse->assertSee('Belum terlaksana');
    $portalResponse->assertSee('Ringkasan Komprehensif');
    $portalResponse->assertSee('Profil Pelajar Pancasila');
    $portalResponse->assertSee('Penilaian Siswa Terpadu');
    $portalResponse->assertSee('Tren Perkembangan');
    $portalResponse->assertDontSee('Analisis Masalah & Tantangan', false);
    $portalResponse->assertDontSee('Pemecahan Masalah & Solusi', false);
});

test('raport print signature has kepala sekolah centered below orang tua and wali kelas', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $student = Student::first();

    $response = $this->actingAs($admin)->get(route('raport.print', [
        'student' => $student->id,
        'paper' => 'a4',
    ]));

    $response->assertOk();
    $html = $response->getContent();

    // Verify presence of all three signatories
    $response->assertSee('Orang Tua / Wali Siswa');
    $response->assertSee('Wali Kelas');
    $response->assertSee('Kepala Sekolah');

    // Verify layout order within the signature section:
    // Orang Tua and Wali Kelas in row 1, then Kepala Sekolah centered in row 2 (colspan="2")
    $posSig = strpos($html, 'PENGESAHAN DOKUMEN TIGA PIHAK');
    $posOrangTua = strpos($html, 'Orang Tua / Wali Siswa', $posSig);
    $posWaliKelas = strpos($html, 'Wali Kelas', $posSig);
    $posKepalaSekolah = strpos($html, 'Kepala Sekolah', $posSig);

    expect($posOrangTua)->toBeLessThan($posKepalaSekolah);
    expect($posWaliKelas)->toBeLessThan($posKepalaSekolah);
    expect($html)->toContain('colspan="2" style="text-align: center; vertical-align: top; padding-top: 15px;"');
});

test('presensi shalat dzuhur applies to both male and female students and is recorded as a dedicated assessment category in raport digital', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-TEST-DZUHUR', 'grade' => 'X', 'major' => 'Umum']);

    $maleStudent = Student::create([
        'nis' => '88001',
        'nisn' => '0088001',
        'name' => 'Ahmad Putra Dzuhur',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $femaleStudent = Student::create([
        'nis' => '88002',
        'nisn' => '0088002',
        'name' => 'Fatimah Putri Dzuhur',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    // 1. Both male and female students can scan Shalat Dzuhur on any day including Friday
    Carbon::setTestNow(Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta')); // Friday

    $scanMale = $this->actingAs($admin)->postJson(route('worship.scan', 'dzuhur-jumat'), [
        'identifier' => $maleStudent->nisn,
        'bypass_time_lock' => true,
    ]);
    $scanMale->assertOk()->assertJsonFragment(['success' => true]);

    $scanFemale = $this->actingAs($admin)->postJson(route('worship.scan', 'dzuhur-jumat'), [
        'identifier' => $femaleStudent->nisn,
        'bypass_time_lock' => true,
    ]);
    $scanFemale->assertOk()->assertJsonFragment(['success' => true]);

    // 2. Data is recorded into StudentAiRaport snapshot and AiStudentAnalysisService
    $aiService = app(AiStudentAnalysisService::class);
    $raportFemale = $aiService->generateAndSaveStudentReport($femaleStudent, $admin->id, 'MANUAL');

    expect($raportFemale->worship_dzuhur_total)->toBe(1);
    expect($raportFemale->worship_dzuhur_hadir)->toBe(1);
    expect($raportFemale->worship_dzuhur_rate)->toBe(100.0);

    // 3. Shalat Dzuhur is an explicit assessment category in conclusions
    $analysis = $aiService->analyzeStudent($femaleStudent);
    $dzuhurConclusion = collect($analysis['kesimpulan'])->firstWhere('dimension', 'Presensi Shalat Dzuhur (Putra & Putri)');

    expect($dzuhurConclusion)->not->toBeNull();
    expect($dzuhurConclusion['score'])->toBe(100.0);
    expect($dzuhurConclusion['sentiment'])->toBe('positive');

    // 4. Check web raport and print view display the category
    $raportView = $this->actingAs($admin)->get(route('raport.index', ['student_id' => $femaleStudent->id]));
    $raportView->assertOk();
    $raportView->assertSee('5. Shalat Dzuhur (Putra & Putri)', false);
    $raportView->assertSee('Putra & Putri (Semua Siswa)', false);

    $printView = $this->actingAs($admin)->get(route('raport.print', ['student' => $femaleStudent->id, 'paper' => 'a4']));
    $printView->assertOk();
    $printView->assertSee('5. Shalat Dzuhur (Putra & Putri)', false);
    $printView->assertSee('1/1 Sesi (Putra & Putri)', false);
});

test('female student account displays shalat dzuhur navigation, dashboard action button, and can perform independent self-attendance', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-02 12:00:00', 'Asia/Jakarta')); // Friday noon

    WorshipAttendance::whereHas('student', fn ($q) => $q->where('nisn', '0077001'))->delete();
    Student::where('nisn', '0077001')->delete();
    User::where('email', 'aisyah.dzuhur@student.sch.id')->delete();

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-PUTRI-DZUHUR', 'grade' => 'X', 'major' => 'Umum']);

    $femaleUser = User::factory()->create([
        'name' => 'Aisyah Siswi Putri',
        'email' => 'aisyah.dzuhur@student.sch.id',
        'role' => 'siswa',
    ]);

    $femaleStudent = Student::create([
        'user_id' => $femaleUser->id,
        'nis' => '77001',
        'nisn' => '0077001',
        'name' => 'Aisyah Siswi Putri',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    // 1. Female student accesses dashboard and sees Shalat Dzuhur options
    $dashboardResponse = $this->actingAs($femaleUser)->get(route('dashboard'));
    $dashboardResponse->assertOk();

    // Verify sidebar has Shalat Dzuhur/Jum'at link
    $dashboardResponse->assertSee(route('worship.index', 'dzuhur-jumat'), false);
    $dashboardResponse->assertSee('Presensi Shalat Dzuhur/Jum\'at', false);

    // Verify hero card has ABSEN DZUHUR MANDIRI button
    $dashboardResponse->assertSee('ABSEN DZUHUR MANDIRI', false);

    // Verify worship widget has Absen Dzuhur action button
    $dashboardResponse->assertSee('Absen Dzuhur Mandiri →', false);

    // 2. Female student visits worship.index for dzuhur-jumat
    $worshipPage = $this->actingAs($femaleUser)->get(route('worship.index', 'dzuhur-jumat'));
    $worshipPage->assertOk();
    $worshipPage->assertSee('Shalat Dzuhur');
    $worshipPage->assertSee('Presensi Selfie Kamera Langsung', false);

    // 3. Female student performs independent attendance via scan
    $scanResponse = $this->actingAs($femaleUser)->postJson(route('worship.scan', 'dzuhur-jumat'), [
        'identifier' => $femaleStudent->nisn,
        'method' => 'QR_CODE',
        'bypass_time_lock' => true,
    ]);
    $scanResponse->assertOk()->assertJsonFragment(['success' => true]);

    // 4. Verify attendance is recorded in database
    expect(WorshipAttendance::where('student_id', $femaleStudent->id)
        ->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT)
        ->exists())->toBeTrue();

    // 5. Re-visiting dashboard now shows Shalat Dzuhur recorded status
    $dashboardAfterScan = $this->actingAs($femaleUser)->get(route('dashboard'));
    $dashboardAfterScan->assertOk();
    $dashboardAfterScan->assertSee('Shalat Dzuhur', false);
    $dashboardAfterScan->assertSee('HADIR', false);
});

test('digital raport generation covers all 8 pillars accurately with single and mass generation', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-EIGHT-PILLARS', 'grade' => 'X', 'major' => 'Umum']);

    $student = Student::create([
        'nis' => '88001',
        'nisn' => '0088001',
        'name' => 'Budi Pratama Delapan Pilar',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Test route raport.generate_single
    $genResponse = $this->actingAs($admin)->post(route('raport.generate_single', $student->id));
    $genResponse->assertRedirect();
    $genResponse->assertSessionHas('success');

    // Verify snapshot in database
    $raport = StudentAiRaport::where('student_id', $student->id)->first();
    expect($raport)->not->toBeNull();
    expect($raport->worship_dhuha_total)->toBeGreaterThanOrEqual(0);
    expect($raport->worship_dzuhur_total)->toBeGreaterThanOrEqual(0);
    expect($raport->development_index)->toBeGreaterThanOrEqual(0.0);
    expect($raport->rekomendasi)->toBeArray();

    // Verify Raport Index displays all 8 pillars
    $indexResponse = $this->actingAs($admin)->get(route('raport.index', ['student_id' => $student->id]));
    $indexResponse->assertOk();
    $indexResponse->assertSee('8 PILAR PERKEMBANGAN TERPADU SISWA', false);
    $indexResponse->assertSee('1. Presensi Harian', false);
    $indexResponse->assertSee('2. Poin Prestasi', false);
    $indexResponse->assertSee('3. Pelanggaran', false);
    $indexResponse->assertSee('4. 7 Anak Hebat', false);
    $indexResponse->assertSee('5. Shalat Dzuhur (Putra & Putri)', false);
    $indexResponse->assertSee('6. Shalat Dhuha', false);
    $indexResponse->assertSee('7. Keputrian', false);
    $indexResponse->assertSee('8. Tahfidz Juz 30', false);

    // Verify Raport Print displays 8 pillars table
    $printResponse = $this->actingAs($admin)->get(route('raport.print', ['student' => $student->id, 'paper' => 'a4']));
    $printResponse->assertOk();
    $printResponse->assertSee('I. REKAPITULASI 8 PILAR PERKEMBANGAN SISWA', false);
    $printResponse->assertSee('6. Shalat Dhuha', false);
});

test('hostinger ready database dump files exist and contain full sql structure', function () {
    $fileRoot = base_path('database_hostinger_ready.sql');
    $fileDatabase = base_path('database/sissma1le_mysql.sql');

    expect(file_exists($fileRoot))->toBeTrue();
    expect(filesize($fileRoot))->toBeGreaterThan(500000); // Clean dump >= 500 KB

    expect(file_exists($fileDatabase))->toBeTrue();
    expect(filesize($fileDatabase))->toBeGreaterThan(500000); // Clean dump >= 500 KB

    // Verify dump content contains key tables and transactions
    $headerSample = file_get_contents($fileRoot, false, null, 0, 500);
    expect($headerSample)->toContain('SET FOREIGN_KEY_CHECKS = 0;');
    expect($headerSample)->toContain('START TRANSACTION;');
});

test('all critical application modules and navigation routes are reachable and connected', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $routesToTest = [
        'dashboard',
        'raport.index',
        'attendance.history',
        'attendance.report',
        'attendance.manual',
        'achievements.index',
        'violations.index',
        'habits.journal',
        'habits.summary',
        'worship.index' => ['activity' => 'dhuha'],
        'tahfidz.index',
        'reports.index',
        'master.school_profile',
        'master.teachers',
        'master.students',
        'master.classes',
        'parent.portal',
        'kiosk.index',
    ];

    foreach ($routesToTest as $key => $val) {
        $routeName = is_numeric($key) ? $val : $key;
        $params = is_array($val) ? $val : [];

        $url = route($routeName, $params);
        $response = $this->actingAs($admin)->get($url);
        expect($response->status())->toBeIn([200, 302]);
    }
});
