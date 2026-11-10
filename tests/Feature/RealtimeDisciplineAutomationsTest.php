<?php

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use App\Models\WorshipAttendance;
use App\Services\DisciplineAutomationService;

beforeEach(function () {
    SchoolSetting::set('school_name', 'SMA Negeri 1 Lengkong');

    AcademicYear::firstOrCreate(
        ['name' => '2026/2027'],
        ['semester' => 'GANJIL', 'is_active' => true, 'start_date' => '2026-07-01', 'end_date' => '2026-12-31']
    );

    $catP1 = ViolationCategory::firstOrCreate(
        ['code' => 'P1'],
        ['name' => 'Kehadiran', 'severity_level' => 'RINGAN']
    );

    ViolationItem::firstOrCreate(
        ['code' => 'P1.3'],
        [
            'category_id' => $catP1->id,
            'name' => 'Alpa/tidak masuk sekolah tanpa keterangan',
            'default_points' => 5,
        ]
    );

    ViolationItem::firstOrCreate(
        ['code' => 'P1.7'],
        [
            'category_id' => $catP1->id,
            'name' => 'Tidak masuk selama 3 (tiga hari) berturut turut tanpa ada keterangan',
            'default_points' => 10,
        ]
    );

    $catR1 = AchievementCategory::firstOrCreate(
        ['code' => 'R1'],
        ['name' => 'Pengembangan Keagamaan']
    );

    AchievementItem::firstOrCreate(
        ['code' => 'R1.1'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan shalat dzuhur berjamaah',
            'default_points' => 5,
        ]
    );

    AchievementItem::firstOrCreate(
        ['code' => 'R1.2'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan shalat dhuha bersama',
            'default_points' => 5,
        ]
    );

    AchievementItem::firstOrCreate(
        ['code' => 'R1.3'],
        [
            'category_id' => $catR1->id,
            'name' => "Melaksanakan hafalan Al-qur'an sebanyak 20 ayat sekali duduk",
            'default_points' => 10,
        ]
    );

    AchievementItem::firstOrCreate(
        ['code' => 'R1.6'],
        [
            'category_id' => $catR1->id,
            'name' => 'Melaksanakan kegiatan keputrian',
            'default_points' => 5,
        ]
    );
});

test('Realtime Solusi B - Presensi Dhuha HADIR langsung otomatis input prestasi R1.2 tanpa cron', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-REAL-DHUHA'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'REAL-DHUHA-01',
        'nisn' => '0098765901',
        'name' => 'Siswa Realtime Dhuha',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $date = '2026-10-15';

    // Simpan presensi ibadah Dhuha dengan status HADIR
    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $date,
        'time' => '07:45:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // Langsung terbuat seketika tanpa perlu Artisan command!
    $achievement = Achievement::where('student_id', $student->id)
        ->whereDate('date', $date)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
    expect($achievement->title)->toContain('dhuha');
});

test('Realtime Solusi B - Presensi Dzuhur HADIR langsung otomatis input prestasi R1.1 tanpa cron', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-REAL-DZUHUR'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'REAL-DZUHUR-02',
        'nisn' => '0098765902',
        'name' => 'Siswa Realtime Dzuhur',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $date = '2026-10-15';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $date,
        'time' => '12:15:00',
        'status' => 'HADIR',
        'method' => 'QR',
    ]);

    $achievement = Achievement::where('student_id', $student->id)
        ->whereDate('date', $date)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
    expect($achievement->title)->toContain('dzuhur');
});

test('Realtime Solusi B - Presensi Keputrian HADIR langsung otomatis input prestasi R1.6 tanpa cron', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-REAL-KEPUTRIAN'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'REAL-KEPUTRIAN-03',
        'nisn' => '0098765903',
        'name' => 'Siswi Realtime Keputrian',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $date = '2026-10-15';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
        'date' => $date,
        'time' => '12:00:00',
        'status' => 'HADIR',
        'method' => 'MANUAL',
    ]);

    $achievement = Achievement::where('student_id', $student->id)
        ->whereDate('date', $date)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
    expect($achievement->title)->toContain('keputrian');
});

test('Realtime Solusi B - Presensi Utama ALPA langsung otomatis input pelanggaran P1.3 dan P1.7', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-REAL-ALPA'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'REAL-ALPA-04',
        'nisn' => '0098765904',
        'name' => 'Siswa Realtime Alpa Tiga Hari',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $day1 = '2026-10-13';
    $day2 = '2026-10-14';
    $day3 = '2026-10-15';

    foreach ([$day1, $day2, $day3] as $d) {
        Attendance::create([
            'student_id' => $student->id,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'student_name' => $student->name,
            'class_id' => $class->id,
            'date' => $d,
            'status' => 'ALPA',
            'method' => 'SISTEM',
        ]);
    }

    // Pelanggaran P1.3 harus terbuat untuk setiap hari Alpa
    $p13Count = Violation::where('student_id', $student->id)->count();
    expect($p13Count)->toBeGreaterThanOrEqual(3);

    // Pelanggaran P1.7 (3 hari berturut-turut) harus terbuat secara realtime di hari ke-3!
    $itemP17 = app(DisciplineAutomationService::class)->getViolationItemP17();
    $violationP17 = Violation::where('student_id', $student->id)
        ->where('item_id', $itemP17->id)
        ->first();

    expect($violationP17)->not->toBeNull();
    expect($violationP17->points)->toBe(10);
    expect($violationP17->status)->toBe('DIVERIFIKASI');
});

test('Realtime Solusi B - Endpoint Web Sinkronisasi Otomasi berjalan sukses untuk admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('discipline.sync_automations'), [
        'date' => '2026-10-15',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('Endpoint Web Sinkronisasi Otomasi menolak akses pengguna selain admin dan wali kelas dengan 403', function () {
    $guru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $kesiswaan = User::factory()->create(['role' => 'kesiswaan', 'roles' => ['kesiswaan']]);
    $siswa = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);

    $this->actingAs($guru)
        ->post(route('discipline.sync_automations'), ['date' => '2026-10-15'])
        ->assertStatus(403);

    $this->actingAs($kesiswaan)
        ->post(route('discipline.sync_automations'), ['date' => '2026-10-15'])
        ->assertStatus(403);

    $this->actingAs($siswa)
        ->post(route('discipline.sync_automations'), ['date' => '2026-10-15'])
        ->assertStatus(403);
});

test('Tombol Sinkronkan Otomasi Hari Ini hanya tampil di akun admin dan wali kelas pada halaman laporan tata tertib', function () {
    $ay = AcademicYear::first();
    $teacher = Teacher::create([
        'nip' => '198901012015011001',
        'name' => 'Wali Kelas X MIPA',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $class = SchoolClass::create([
        'name' => 'X MIPA 1',
        'grade' => 10,
        'academic_year_id' => $ay->id,
        'homeroom_teacher_id' => $teacher->id,
    ]);

    $waliKelas = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);
    $teacher->update(['user_id' => $waliKelas->id]);

    $admin = User::factory()->create(['role' => 'admin', 'roles' => ['admin']]);
    $guru = User::factory()->create(['role' => 'guru', 'roles' => ['guru']]);
    $kesiswaan = User::factory()->create(['role' => 'kesiswaan', 'roles' => ['kesiswaan']]);
    $siswa = User::factory()->create(['role' => 'siswa', 'roles' => ['siswa']]);

    // Admin melihat tombol
    $this->actingAs($admin)->get(route('discipline.report'))
        ->assertOk()
        ->assertSee('Sinkronkan Otomasi Hari Ini');

    // Wali kelas melihat tombol
    $this->actingAs($waliKelas)->get(route('discipline.report'))
        ->assertOk()
        ->assertSee('Sinkronkan Otomasi Hari Ini');

    // Guru TIDAK melihat tombol
    $this->actingAs($guru)->get(route('discipline.report'))
        ->assertOk()
        ->assertDontSee('Sinkronkan Otomasi Hari Ini');

    // Kesiswaan TIDAK melihat tombol
    $this->actingAs($kesiswaan)->get(route('discipline.report'))
        ->assertOk()
        ->assertDontSee('Sinkronkan Otomasi Hari Ini');

    // Siswa TIDAK melihat tombol
    $this->actingAs($siswa)->get(route('discipline.report'))
        ->assertOk()
        ->assertDontSee('Sinkronkan Otomasi Hari Ini');
});

test('Wali kelas yang mengeksekusi Sinkronkan Otomasi hanya memproses siswa kelas binaannya saja', function () {
    $ay = AcademicYear::first();

    // 1. Buat Kelas A dan Wali Kelas A
    $teacherA = Teacher::create([
        'nip' => '198701012011011001',
        'name' => 'Guru Wali A',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $classA = SchoolClass::create([
        'name' => 'X MIPA A',
        'grade' => 10,
        'academic_year_id' => $ay->id,
        'homeroom_teacher_id' => $teacherA->id,
    ]);

    $waliA = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);
    $teacherA->update(['user_id' => $waliA->id]);

    // 2. Buat Kelas B dan Wali Kelas B
    $teacherB = Teacher::create([
        'nip' => '198801012012011002',
        'name' => 'Guru Wali B',
        'gender' => 'P',
        'is_active' => true,
    ]);

    $classB = SchoolClass::create([
        'name' => 'X MIPA B',
        'grade' => 10,
        'academic_year_id' => $ay->id,
        'homeroom_teacher_id' => $teacherB->id,
    ]);

    $waliB = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);
    $teacherB->update(['user_id' => $waliB->id]);

    // 3. Siswa A (Kelas A) dan Siswa B (Kelas B)
    $studentA = Student::create([
        'name' => 'Siswa Kelas A',
        'nis' => '10001',
        'nisn' => '0010001',
        'gender' => 'L',
        'class_id' => $classA->id,
        'academic_year_id' => $ay->id,
        'is_active' => true,
    ]);

    $studentB = Student::create([
        'name' => 'Siswa Kelas B',
        'nis' => '10002',
        'nisn' => '0010002',
        'gender' => 'L',
        'class_id' => $classB->id,
        'academic_year_id' => $ay->id,
        'is_active' => true,
    ]);

    $targetDate = '2026-10-15';

    // Buat presensi Shalat Dhuha HADIR untuk kedua siswa tanpa trigger event
    WorshipAttendance::withoutEvents(function () use ($studentA, $studentB, $classA, $classB, $targetDate) {
        WorshipAttendance::create([
            'student_id' => $studentA->id,
            'class_id' => $classA->id,
            'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
            'date' => $targetDate,
            'time' => '10:00:00',
            'status' => 'HADIR',
        ]);

        WorshipAttendance::create([
            'student_id' => $studentB->id,
            'class_id' => $classB->id,
            'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
            'date' => $targetDate,
            'time' => '10:00:00',
            'status' => 'HADIR',
        ]);
    });

    // Buat presensi ALPA untuk kedua siswa tanpa trigger event
    Attendance::withoutEvents(function () use ($studentA, $studentB, $classA, $classB, $targetDate) {
        Attendance::create([
            'student_id' => $studentA->id,
            'nis' => $studentA->nis,
            'nisn' => $studentA->nisn,
            'class_id' => $classA->id,
            'student_name' => $studentA->name,
            'date' => $targetDate,
            'status' => 'ALPA',
        ]);

        Attendance::create([
            'student_id' => $studentB->id,
            'nis' => $studentB->nis,
            'nisn' => $studentB->nisn,
            'class_id' => $classB->id,
            'student_name' => $studentB->name,
            'date' => $targetDate,
            'status' => 'ALPA',
        ]);
    });

    // Pastikan belum ada prestasi dan pelanggaran
    expect(Achievement::where('student_id', $studentA->id)->count())->toBe(0);
    expect(Achievement::where('student_id', $studentB->id)->count())->toBe(0);
    expect(Violation::where('student_id', $studentA->id)->count())->toBe(0);
    expect(Violation::where('student_id', $studentB->id)->count())->toBe(0);

    // 4. Eksekusi sinkronisasi oleh Wali Kelas A!
    $response = $this->actingAs($waliA)->post(route('discipline.sync_automations'), [
        'date' => $targetDate,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Siswa A (kelas binaan waliA) HARUS mendapatkan prestasi & pelanggaran
    $achStudentA = Achievement::where('student_id', $studentA->id)->count();
    $violStudentA = Violation::where('student_id', $studentA->id)->count();
    expect($achStudentA)->toBeGreaterThanOrEqual(1);
    expect($violStudentA)->toBeGreaterThanOrEqual(1);

    // Siswa B (kelas binaan waliB) TIDAK BOLEH diproses sama sekali!
    $achStudentB = Achievement::where('student_id', $studentB->id)->count();
    $violStudentB = Violation::where('student_id', $studentB->id)->count();
    expect($achStudentB)->toBe(0);
    expect($violStudentB)->toBe(0);
});

test('Status Pelapor adalah akun pengklik sinkronisasi dan Verifikator adalah admin murni bukan administrator', function () {
    // 1. Setup user super_admin (administrator) dan admin sekolah (bukan administrator)
    $superAdmin = User::firstOrCreate(
        ['username' => 'SENKS_TEST'],
        ['name' => 'Rh Aseng Test', 'email' => 'senks_test@example.com', 'role' => 'super_admin', 'is_active' => true, 'password' => bcrypt('password')]
    );

    $adminUser = User::firstOrCreate(
        ['username' => 'ADMIN_SCHOOL_TEST'],
        ['name' => 'Winda Dwi Atyani Test', 'email' => 'admin_school_test@example.com', 'role' => 'admin', 'roles' => ['admin'], 'is_active' => true, 'password' => bcrypt('password')]
    );

    // 2. Setup wali kelas yang akan mengklik tombol sinkronisasi
    $waliUser = User::firstOrCreate(
        ['username' => 'WALI_SYNC_TEST'],
        ['name' => 'Budi Wali Kelas', 'email' => 'wali_sync_test@example.com', 'role' => 'wali_kelas', 'roles' => ['wali_kelas'], 'is_active' => true, 'password' => bcrypt('password')]
    );

    $teacher = Teacher::firstOrCreate(
        ['user_id' => $waliUser->id],
        ['nip' => '198765432101', 'name' => 'Budi Wali Kelas']
    );

    $class = SchoolClass::firstOrCreate(
        ['name' => 'XII-SYNC-TEST'],
        ['grade' => '12', 'homeroom_teacher_id' => $teacher->id]
    );

    $student = Student::create([
        'nis' => 'SYNC-REP-01',
        'nisn' => '0098765999',
        'name' => 'Cindy Siswi Test',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $targetDate = '2026-10-20';

    // 3. Setup kehadiran Dhuha, Dzuhur, Keputrian, dan ALPA harian
    WorshipAttendance::withoutEvents(function () use ($student, $class, $targetDate) {
        // Dhuha
        WorshipAttendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
            'date' => $targetDate,
            'time' => '10:00:00',
            'status' => 'HADIR',
        ]);

        // Dzuhur
        WorshipAttendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
            'date' => $targetDate,
            'time' => '12:30:00',
            'status' => 'HADIR',
        ]);

        // Keputrian
        WorshipAttendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'activity_type' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
            'date' => $targetDate,
            'time' => '13:00:00',
            'status' => 'HADIR',
        ]);
    });

    Attendance::withoutEvents(function () use ($student, $class, $targetDate) {
        Attendance::create([
            'student_id' => $student->id,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'class_id' => $class->id,
            'student_name' => $student->name,
            'date' => $targetDate,
            'status' => 'ALPA',
        ]);
    });

    // 4. Eksekusi sinkronisasi dengan user Wali Kelas
    $response = $this->actingAs($waliUser)->post(route('discipline.sync_automations'), [
        'date' => $targetDate,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 5. Verifikasi Prestasi Dhuha (R1.2)
    $expectedVerifierId = app(DisciplineAutomationService::class)->getAdminVerifierId();
    $expectedVerifierUser = User::find($expectedVerifierId);
    expect($expectedVerifierUser->role)->toBe('admin');
    expect($expectedVerifierUser->role)->not->toBe('super_admin');

    $achDhuha = Achievement::where('student_id', $student->id)
        ->whereHas('item', fn ($q) => $q->where('code', 'R1.2'))
        ->first();
    expect($achDhuha)->not->toBeNull();
    expect($achDhuha->reporter_id)->toBe($waliUser->id);
    expect($achDhuha->verified_by)->toBe($expectedVerifierId);
    expect($achDhuha->verified_by)->not->toBe($superAdmin->id);

    // 6. Verifikasi Prestasi Dzuhur (R1.1)
    $achDzuhur = Achievement::where('student_id', $student->id)
        ->whereHas('item', fn ($q) => $q->where('code', 'R1.1'))
        ->first();
    expect($achDzuhur)->not->toBeNull();
    expect($achDzuhur->reporter_id)->toBe($waliUser->id);
    expect($achDzuhur->verified_by)->toBe($expectedVerifierId);
    expect($achDzuhur->verified_by)->not->toBe($superAdmin->id);

    // 7. Verifikasi Prestasi Keputrian (R1.6)
    $achKeputrian = Achievement::where('student_id', $student->id)
        ->whereHas('item', fn ($q) => $q->where('code', 'R1.6'))
        ->first();
    expect($achKeputrian)->not->toBeNull();
    expect($achKeputrian->reporter_id)->toBe($waliUser->id);
    expect($achKeputrian->verified_by)->toBe($expectedVerifierId);
    expect($achKeputrian->verified_by)->not->toBe($superAdmin->id);

    // 8. Verifikasi Pelanggaran ALPA (P1.3)
    $violAlpa = Violation::where('student_id', $student->id)
        ->whereHas('item', fn ($q) => $q->where('code', 'P1.3'))
        ->first();
    expect($violAlpa)->not->toBeNull();
    expect($violAlpa->reporter_id)->toBe($waliUser->id);
    expect($violAlpa->verified_by)->toBe($expectedVerifierId);
    expect($violAlpa->verified_by)->not->toBe($superAdmin->id);
});
