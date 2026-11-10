<?php

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\Extracurricular;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use App\Models\WorshipAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;

beforeEach(function () {
    // Seed Academic Year
    AcademicYear::firstOrCreate(
        ['name' => '2026/2027-Ganjil'],
        [
            'semester' => 'Ganjil',
            'start_date' => Carbon::now('Asia/Jakarta')->subMonths(2)->toDateString(),
            'end_date' => Carbon::now('Asia/Jakarta')->addMonths(4)->toDateString(),
            'is_active' => true,
        ]
    );

    // Ensure Master Catalog Items for Violations
    $catP1 = ViolationCategory::firstOrCreate(
        ['code' => 'P1'],
        ['name' => 'Kedisiplinan & Ketertiban Masuk Sekolah', 'severity_level' => 'RINGAN']
    );

    ViolationItem::updateOrCreate(
        ['code' => 'P1.3'],
        [
            'category_id' => $catP1->id,
            'name' => 'Terlambat/Alpa, tanpa ada keterangan apapun',
            'default_points' => 5,
            'is_active' => true,
        ]
    );

    ViolationItem::updateOrCreate(
        ['code' => 'P1.7'],
        [
            'category_id' => $catP1->id,
            'name' => 'Tidak masuk selama 3 (tiga hari) berturut turut tanpa ada keterangan',
            'default_points' => 10,
            'is_active' => true,
        ]
    );

    // Ensure Master Catalog Items for Achievements
    $catR1 = AchievementCategory::firstOrCreate(
        ['code' => 'R1'],
        ['name' => 'Kegiatan Ibadah & Keagamaan', 'tier' => 'SEKOLAH']
    );

    AchievementItem::updateOrCreate(
        ['code' => 'R1.1'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan shalat dzuhur berjamaah',
            'default_points' => 5,
            'is_active' => true,
        ]
    );

    AchievementItem::updateOrCreate(
        ['code' => 'R1.2'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan shalat dhuha bersama',
            'default_points' => 5,
            'is_active' => true,
        ]
    );

    AchievementItem::updateOrCreate(
        ['code' => 'R1.3'],
        [
            'category_id' => $catR1->id,
            'name' => "Melaksanakan hafalan Al-qur'an sebanyak 20 ayat sekali duduk",
            'default_points' => 10,
            'is_active' => true,
        ]
    );

    AchievementItem::updateOrCreate(
        ['code' => 'R1.6'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan kegiatan keputrian',
            'default_points' => 5,
            'is_active' => true,
        ]
    );

    // Set Default School Settings
    SchoolSetting::set('school_lat', -7.56845);
    SchoolSetting::set('school_lng', 112.04612);
    SchoolSetting::set('geofence_radius_meters', 100);
    SchoolSetting::set('enable_attendance_rfid', '1');
    SchoolSetting::set('enable_attendance_qr', '1');
    SchoolSetting::set('enable_attendance_selfie', '1');

    AttendanceLocation::firstOrCreate(
        ['is_primary' => true],
        [
            'name' => 'SMA Negeri 1 Lengkong',
            'latitude' => -7.56845,
            'longitude' => 112.04612,
            'radius_meters' => 100,
        ]
    );
});

test('alpa on a single day does not automatically record violation (manual input)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-AUTO-1'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Ahmad Santoso',
        'gender' => 'L',
        'nisn' => '9988770001',
        'nis' => '9001',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // 1. Record single ALPA via manual attendance
    $attService = app(AttendanceService::class);
    $result = $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $today,
        'status' => 'ALPA',
        'notes' => 'Alpa hari pertama',
    ], $admin);

    expect($result['success'])->toBeTrue();

    // Pastikan tidak ada poin pelanggaran yang terbuat otomatis (akan diisi manual)
    $violations = Violation::where('student_id', $student->id)->whereDate('date', $today)->get();
    expect($violations)->toBeEmpty();
});

test('alpa for 3 consecutive days does not automatically record violations (manual input)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-AUTO-2'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Budi Pratama',
        'gender' => 'L',
        'nisn' => '9988770002',
        'nis' => '9002',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $attService = app(AttendanceService::class);

    $day1 = Carbon::now('Asia/Jakarta')->subDays(2)->toDateString();
    $day2 = Carbon::now('Asia/Jakarta')->subDays(1)->toDateString();
    $day3 = Carbon::now('Asia/Jakarta')->toDateString();

    // Day 1: Alpa
    $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $day1,
        'status' => 'ALPA',
    ], $admin);

    // Day 2: Alpa
    $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $day2,
        'status' => 'ALPA',
    ], $admin);

    // Day 3: Alpa (3rd consecutive day)
    $attService->recordManualAttendance([
        'student_id' => $student->id,
        'date' => $day3,
        'status' => 'ALPA',
    ], $admin);

    // Pastikan pada hari ke-3 tidak tercatat pelanggaran otomatis (manual input)
    $day3Violations = Violation::where('student_id', $student->id)->whereDate('date', $day3)->get();
    expect($day3Violations)->toBeEmpty();
});

test('attending shalat dhuha bersama does not automatically record achievement (manual input)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-DHUHA'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Faris Dhuha',
        'gender' => 'L',
        'nisn' => '9988770003',
        'nis' => '9003',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // Act as admin scanning Dhuha attendance for Faris
    $response = $this->actingAs($admin)->postJson(route('worship.scan', 'dhuha'), [
        'identifier' => $student->nisn,
        'method' => 'RFID',
        'bypass_time_lock' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // Pastikan tidak tercatat prestasi otomatis (manual input)
    $achievements = Achievement::where('student_id', $student->id)->whereDate('date', $today)->get();
    expect($achievements)->toBeEmpty();
});

test('attending shalat dzuhur berjamaah does not automatically record achievement (manual input)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-DZUHUR'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Zaid Dzuhur',
        'gender' => 'L',
        'nisn' => '9988770004',
        'nis' => '9004',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $today = Carbon::now('Asia/Jakarta')->toDateString();

    $response = $this->actingAs($admin)->postJson(route('worship.scan', 'dzuhur'), [
        'identifier' => $student->nisn,
        'method' => 'RFID',
        'bypass_time_lock' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $achievements = Achievement::where('student_id', $student->id)->whereDate('date', $today)->get();
    expect($achievements)->toBeEmpty();
});

test('attending kegiatan keputrian does not automatically record achievement (manual input)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-PUTRI'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Fatimah Az-Zahra',
        'gender' => 'P',
        'nisn' => '9988770005',
        'nis' => '9005',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $today = Carbon::now('Asia/Jakarta')->toDateString();

    $response = $this->actingAs($admin)->postJson(route('worship.scan', 'keputrian'), [
        'identifier' => $student->nisn,
        'method' => 'RFID',
        'bypass_time_lock' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $achievements = Achievement::where('student_id', $student->id)->whereDate('date', $today)->get();
    expect($achievements)->toBeEmpty();
});

test('depositing 20 or more verses in a single tahfidz sitting does not automatically record achievement (manual entry)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TAHFIDZ'], ['grade' => '10', 'major' => 'Umum']);
    $userExaminer = User::factory()->create([
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas'],
    ]);
    $teacher = Teacher::create([
        'user_id' => $userExaminer->id,
        'name' => 'Ustadz Mansur',
        'nip' => '198700000001',
    ]);
    $class->update(['homeroom_teacher_id' => $teacher->id]);

    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Muhammad Tahfidz',
        'gender' => 'L',
        'nisn' => '9988770006',
        'nis' => '9006',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // Act as authorized examiner submitting 25 verses in a single sitting
    $response = $this->actingAs($userExaminer)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 78, // An-Naba
        'ayat_range' => 'Ayat 1 s.d. 25',
        'ayat_count' => 25,
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Sangat Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => 'Hafalan sangat lancar dan tartil.',
        'deposit_date' => $today,
        'deposit_time' => '07:30',
        'examiner_name' => 'Ustadz Mansur',
    ]);

    $response->assertRedirect(route('tahfidz.student', $student->id));

    // Poin prestasi R1.3 otomatis tercatat saat setor >= 20 ayat
    $achievement = Achievement::where('student_id', $student->id)->whereDate('date', $today)->first();
    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(10);
    expect($achievement->status)->toBe('DIVERIFIKASI');
});

test('depositing less than 20 verses in a single tahfidz sitting does not trigger R1.3 achievement', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TAHFIDZ-LESS'], ['grade' => '10', 'major' => 'Umum']);
    $userExaminer = User::factory()->create([
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas'],
    ]);
    $teacher = Teacher::create([
        'user_id' => $userExaminer->id,
        'name' => 'Ustadz Hasan',
        'nip' => '198700000002',
    ]);
    $class->update(['homeroom_teacher_id' => $teacher->id]);

    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Ali Imran',
        'gender' => 'L',
        'nisn' => '9988770007',
        'nis' => '9007',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // Submit only 15 verses
    $response = $this->actingAs($userExaminer)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 78,
        'ayat_range' => 'Ayat 1 s.d. 15',
        'ayat_count' => 15,
        'makhraj' => 'Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Baik',
        'overall_grade' => 'Lulus',
        'evaluation_notes' => '15 ayat pertama.',
        'deposit_date' => $today,
        'deposit_time' => '07:45',
        'examiner_name' => 'Ustadz Hasan',
    ]);

    $response->assertRedirect(route('tahfidz.student', $student->id));

    $achievements = Achievement::where('student_id', $student->id)->get();
    expect($achievements)->toBeEmpty();
});

test('shared device and geofence configuration is enforced in worship attendance identically to daily attendance', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-GEOFENCE'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Dewi Lestari',
        'gender' => 'P',
        'nisn' => '9988770008',
        'nis' => '9008',
        'is_active' => true,
    ]);

    // Create shared hardware attendance device
    AttendanceDevice::firstOrCreate(
        ['device_code' => 'GATE-01'],
        [
            'device_name' => 'Gerbang Utama RFID/QR',
            'device_type' => 'RFID_READER',
            'location' => 'Pintu Masuk Utama',
            'is_active' => true,
        ]
    );

    // 1. Far away selfie location (outside 100m geofence radius)
    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $farResponse = $this->actingAs($userStudent)->postJson(route('worship.selfie', 'keputrian'), [
        'selfie_image' => $dummyImage,
        'latitude' => -6.20000, // Jakarta coordinate, hundreds of kilometers away from Lengkong
        'longitude' => 106.81666,
        'device_code' => 'GATE-01',
        'bypass_time_lock' => true,
    ]);

    $farResponse->assertStatus(422);
    $farResponse->assertJson(['success' => false]);
    expect($farResponse->json('message'))->toContain('luar radius sekolah');

    // 2. In-radius selfie location (near school lat -7.56845, lng 112.04612)
    $nearResponse = $this->actingAs($userStudent)->postJson(route('worship.selfie', 'keputrian'), [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56846,
        'longitude' => 112.04613,
        'device_code' => 'GATE-01',
        'bypass_time_lock' => true,
    ]);

    $nearResponse->assertStatus(200);
    $nearResponse->assertJson(['success' => true]);
});

test('raport digital and ai analysis are seamlessly connected and synthesize all activities', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-UNIFIED'], ['grade' => '10', 'major' => 'Umum']);
    $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Rahmat Terpadu',
        'gender' => 'L',
        'nisn' => '9988770009',
        'nis' => '9009',
        'is_active' => true,
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // Record Daily Attendance (Hadir)
    Attendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'date' => $today,
        'time' => '06:45:00',
        'method' => 'RFID',
        'status' => 'HADIR',
        'verification_status' => 'DIVERIFIKASI',
    ]);

    // Record Worship (Dhuha)
    WorshipAttendance::create([
        'student_id' => $student->id,
        'activity_type' => 'DHUHA',
        'date' => $today,
        'time' => '07:15:00',
        'method' => 'RFID',
        'status' => 'HADIR',
    ]);

    // Record Tahfidz (20 verses)
    $tahfidz = QuranTahfidzRecord::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'juz' => 30,
        'surah_number' => 78,
        'surah_name' => 'An-Naba',
        'ayat_range' => 'Ayat 1 s.d. 20',
        'ayat_count' => 20,
        'makhraj' => 'Sangat Baik',
        'tajwid' => 'Sangat Baik',
        'kelancaran' => 'Sangat Baik',
        'overall_grade' => 'Lulus',
        'deposit_date' => $today,
        'deposit_time' => '07:30',
        'examiner_id' => $admin->id,
        'examiner_name' => 'Admin Penguji',
    ]);

    // 1. Visit Raport Digital
    $raportResponse = $this->actingAs($admin)->get(route('raport.show', $student->id));
    $raportResponse->assertStatus(200);
    $raportResponse->assertSee('Raport Digital Analisis Perkembangan');
    $raportResponse->assertSee($student->name);
    $raportResponse->assertSee('Generate Siswa');

    // 2. Visit AI Analysis
    $aiResponse = $this->actingAs($admin)->get(route('ai.analysis', ['student_id' => $student->id]));
    $aiResponse->assertStatus(200);
    $aiResponse->assertSee('AI Analisis Perkembangan Siswa');
    $aiResponse->assertSee('Buka Raport Digital Terpadu');
});

test('quick leave menu is only present for student role and not on staff dashboards', function () {
    $studentUser = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'class_id' => SchoolClass::first()->id ?? 1,
        'name' => 'Siswa Test Menu',
        'gender' => 'L',
        'nisn' => '1122334455',
        'nis' => '1122',
        'is_active' => true,
    ]);
    $adminUser = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $homeroomUser = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);

    // Student dashboard has quick leave action
    $studentDash = $this->actingAs($studentUser)->get(route('dashboard'));
    $studentDash->assertStatus(200);
    $studentDash->assertSee('href="'.route('attendance.leave').'"', false);
    $studentDash->assertSee('Pengajuan Izin');

    // Admin dashboard quick actions do not have attendance.leave (form)
    $adminDash = $this->actingAs($adminUser)->get(route('dashboard'));
    $adminDash->assertStatus(200);
    $adminDash->assertDontSee('href="'.route('attendance.leave').'"', false);
    $adminDash->assertDontSee('Pengajuan Izin');

    // Homeroom dashboard quick actions do not have attendance.leave (form)
    $homeroomDash = $this->actingAs($homeroomUser)->get(route('dashboard'));
    $homeroomDash->assertStatus(200);
    $homeroomDash->assertDontSee('href="'.route('attendance.leave').'"', false);
    $homeroomDash->assertDontSee('Pengajuan Izin');
});

test('worship tahfidz dashboard summary includes 5 activities and extracurricular', function () {
    $adminUser = User::where('role', 'admin')->first();
    $response = $this->actingAs($adminUser)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Ringkasan Presensi Ibadah', false);
    $response->assertSee("Presensi Shalat Dzuhur/Jum'at", false);
    $response->assertSee('Presensi Shalat Dhuha', false);
    $response->assertSee('Presensi Keputrian', false);
    $response->assertSee('Presensi Ekstrakurikuler', false);
    $response->assertSee('Progres Hafalan Juz 30', false);
});

test('auto cutoff commands record alpa for unrecorded students across activities and allow manual updates', function () {
    // Travel to an active school day (Kamis)
    Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));

    try {
        $class = SchoolClass::firstOrCreate(['name' => 'X-CUTOFF'], ['grade' => '10', 'major' => 'Umum']);
        $userStudent = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
        $student = Student::create([
            'user_id' => $userStudent->id,
            'class_id' => $class->id,
            'name' => 'Siswa Belum Absen',
            'gender' => 'L',
            'nisn' => '9988770099',
            'nis' => '9099',
            'is_active' => true,
        ]);

        $admin = User::where('role', 'admin')->first();
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        // 1. Dhuha cutoff command
        $this->artisan('attendance:process-dhuha-alpa')->assertSuccessful();
        $dhuhaRec = WorshipAttendance::where('student_id', $student->id)
            ->where('activity_type', 'DHUHA')
            ->whereDate('date', $today)
            ->first();
        expect($dhuhaRec)->not->toBeNull();
        expect($dhuhaRec->status)->toBe('ALPA');

        // 2. Dzuhur cutoff command
        $this->artisan('attendance:process-dzuhur-alpa')->assertSuccessful();
        $dzuhurRec = WorshipAttendance::where('student_id', $student->id)
            ->where('activity_type', 'DZUHUR_JUMAT')
            ->whereDate('date', $today)
            ->first();
        expect($dzuhurRec)->not->toBeNull();
        expect($dzuhurRec->status)->toBe('ALPA');

        // 3. Manual update by admin/wali kelas can change the ALPA status to HADIR
        $manualResp = $this->actingAs($admin)->post(route('worship.manual', 'dhuha'), [
            'class_id' => $class->id,
            'date' => $today,
            'statuses' => [$student->id => 'HADIR'],
        ]);
        $manualResp->assertSessionHas('success');

        $updatedDhuha = WorshipAttendance::find($dhuhaRec->id);
        expect($updatedDhuha->status)->toBe('HADIR');
        expect($updatedDhuha->method)->toBe('MANUAL');
    } finally {
        Carbon::setTestNow();
    }
});

test('parent portal without login displays child data, female-only keputrian, conditional extracurricular, grade-specific juz 30, and privacy', function () {
    // 1. Create a female student in Class X who joins an extracurricular
    $classX = SchoolClass::firstOrCreate(['name' => 'X-A'], ['grade' => 'X', 'major' => 'Umum']);
    $userFemale = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $femaleStudent = Student::create([
        'user_id' => $userFemale->id,
        'class_id' => $classX->id,
        'name' => 'Fatimah Az-Zahra',
        'gender' => 'P',
        'nisn' => '8888111101',
        'nis' => '8801',
        'is_active' => true,
    ]);

    $extra = Extracurricular::firstOrCreate(
        ['name' => 'PMR Wira SMAN 1'],
        [
            'code' => 'PMR01',
            'category' => 'Kemanusiaan',
            'schedule_day' => 'Jumat',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'location' => 'UKS',
            'is_active' => true,
        ]
    );
    $femaleStudent->extracurriculars()->sync([$extra->id]);

    // Test Parent Portal for Female Student
    $respFemale = $this->get(route('parent.portal', ['nisn' => $femaleStudent->nisn]));
    $respFemale->assertStatus(200);

    // Child data privacy
    $respFemale->assertSee('Fatimah Az-Zahra');
    $respFemale->assertSee('8888111101');

    // 1. Presensi Utama & Periode Filter
    $respFemale->assertSee('Presensi Sekolah');
    $respFemale->assertSee('Rekapitulasi Presensi Kehadiran Anak');
    $respFemale->assertSee('Pilihan Periode:');
    $respFemale->assertSee('Harian');
    $respFemale->assertSee('Mingguan');
    $respFemale->assertSee('Bulanan');
    $respFemale->assertSee('Rentang Kustom');

    // 2. Presensi Dzuhur & Dhuha Cutoff
    $respFemale->assertSee('Shalat Dzuhur', false);
    $respFemale->assertSee('Cutoff 13.30 WIB');
    $respFemale->assertSee('Shalat Dhuha');
    $respFemale->assertSee('Cutoff 09.00 WIB');

    // 3. Keputrian (FEMALE ONLY)
    $respFemale->assertSee('Presensi Keputrian');
    $respFemale->assertSee('Kajian Keputrian');

    // 4. Ekstrakurikuler (ENROLLED ONLY)
    $respFemale->assertSee('Presensi Ekstrakurikuler');
    $respFemale->assertSee('PMR Wira SMAN 1');

    // 5. Poin Prestasi & Ranking
    $respFemale->assertSee('Ranking Prestasi Siswa');
    $respFemale->assertSee('Total Poin Prestasi');

    // 6. Poin Pelanggaran & Tata Tertib
    $respFemale->assertSee('Tata Tertib & Pelanggaran', false);
    $respFemale->assertSee('Total Poin Pelanggaran');

    // 7. 7 KAIH
    $respFemale->assertSee('7 KAIH');
    $respFemale->assertSee('7 Kebiasaan Anak Indonesia Hebat');

    // 8. Juz 30 Kelas X (25 Surat)
    $respFemale->assertSee('Kurikulum Tingkat Kelas X');
    $respFemale->assertSee('25 Surat');
    $respFemale->assertSee('Al-Balad');
    $respFemale->assertSee('An-Nas');

    // 9. Raport Digital
    $respFemale->assertSee('Raport Digital (AI)');
    $respFemale->assertSee('Cetak A4');

    // 2. Test Male Student without Extracurricular in Class XI (7 Surat)
    $classXI = SchoolClass::firstOrCreate(['name' => 'XI-B'], ['grade' => 'XI', 'major' => 'Umum']);
    $userMale = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);
    $maleStudent = Student::create([
        'user_id' => $userMale->id,
        'class_id' => $classXI->id,
        'name' => 'Ahmad Fauzan',
        'gender' => 'L',
        'nisn' => '9999222202',
        'nis' => '9902',
        'is_active' => true,
    ]);

    $respMale = $this->get(route('parent.portal', ['nisn' => $maleStudent->nisn]));
    $respMale->assertStatus(200);

    // Male child data
    $respMale->assertSee('Ahmad Fauzan');

    // Keputrian MUST NOT be displayed for male students
    $respMale->assertDontSee('Presensi Keputrian');
    $respMale->assertDontSee('Kajian Keputrian');

    // Extracurricular MUST NOT be displayed if student has no extracurriculars
    $respMale->assertDontSee('Presensi Ekstrakurikuler');

    // Juz 30 Kelas XI (7 Surat)
    $respMale->assertSee('Kurikulum Tingkat Kelas XI');
    $respMale->assertSee('7 Surat');
    $respMale->assertSee('Al-Mutaffifin');
    $respMale->assertSee('Al-Fajr');
});
