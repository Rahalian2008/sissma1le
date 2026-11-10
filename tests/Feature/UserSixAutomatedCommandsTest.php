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
use Illuminate\Support\Facades\Artisan;

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

test('1. pada jam 14.00 WIB siswa alpa presensi utama otomatis input pelanggaran Terlambat/Alpa tanpa keterangan tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-P13'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-P13-001',
        'nisn' => '0098765001',
        'name' => 'Siswa Alpa Jam 14',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-15';

    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $class->id,
        'date' => $today,
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);

    // Jalankan otomasi jam 14:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '14:00',
        '--date' => $today,
    ]);

    $violation = Violation::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->first();

    expect($violation)->not->toBeNull();
    expect($violation->points)->toBe(5);
    expect($violation->status)->toBe('DIVERIFIKASI');
    expect($violation->time)->toBe('14:00:00');
    expect($violation->chronology)->toContain('14.00 WIB');
});

test('2. pada jam 14.00 WIB siswa alpa berturut-turut 3 hari otomatis input pelanggaran P1.7 tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-P17'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-P17-002',
        'nisn' => '0098765002',
        'name' => 'Siswa Alpa 3 Hari Berturut',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $day1 = '2026-10-13';
    $day2 = '2026-10-14';
    $day3 = '2026-10-15'; // Hari ini jam 14.00 WIB (hari ke-3 berturut-turut ALPA)

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

    // Jalankan otomasi jam 14:00 WIB pada hari ke-3
    Artisan::call('discipline:process-automations', [
        '--time' => '14:00',
        '--date' => $day3,
    ]);

    $itemP17 = app(DisciplineAutomationService::class)->getViolationItemP17();
    $violation = Violation::where('student_id', $student->id)
        ->where('item_id', $itemP17->id)
        ->whereDate('date', $day3)
        ->first();

    expect($violation)->not->toBeNull();
    expect($violation->title)->toContain('Tidak masuk selama 3 (tiga hari)');
    expect($violation->points)->toBe(10);
    expect($violation->status)->toBe('DIVERIFIKASI');
});

test('3. pada jam 11.00 WIB siswa presensi shalat dhuha hadir otomatis input prestasi Melaksanakan shalat dhuha bersama tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-DHUHA'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-DHUHA-003',
        'nisn' => '0098765003',
        'name' => 'Siswa Dhuha Hadir',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $today = '2026-10-15';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $today,
        'time' => '08:00:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // Jalankan otomasi jam 11:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '11:00',
        '--date' => $today,
    ]);

    $itemR12 = app(DisciplineAutomationService::class)->getAchievementItemR12();
    $achievement = Achievement::where('student_id', $student->id)
        ->where('item_id', $itemR12->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
    expect($achievement->description)->toContain('11.00 WIB');
});

test('4. pada jam 14.00 WIB siswa presensi shalat dzuhur/jumat hadir otomatis input prestasi Melaksanakan shalat dzuhur berjamaah tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-DZUHUR'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-DZUHUR-004',
        'nisn' => '0098765004',
        'name' => 'Siswa Dzuhur Hadir',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-15';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $today,
        'time' => '12:15:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // Jalankan otomasi jam 14:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '14:00',
        '--date' => $today,
    ]);

    $itemR11 = app(DisciplineAutomationService::class)->getAchievementItemR11();
    $achievement = Achievement::where('student_id', $student->id)
        ->where('item_id', $itemR11->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
});

test('5. pada jam 14.30 WIB siswa presensi Keputrian hadir otomatis input prestasi Melaksanakan kegiatan keputrian tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-PUTRI'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-PUTRI-005',
        'nisn' => '0098765005',
        'name' => 'Siswi Keputrian Hadir',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $today = '2026-10-15';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
        'date' => $today,
        'time' => '12:00:00',
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // Jalankan otomasi jam 14:30 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '14:30',
        '--date' => $today,
    ]);

    $itemR16 = app(DisciplineAutomationService::class)->getAchievementItemR16();
    $achievement = Achievement::where('student_id', $student->id)
        ->where('item_id', $itemR16->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->title)->toBe('Melaksanakan kegiatan keputrian');
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI');
    expect($achievement->description)->toContain('14.30 WIB');
});

test('6. setor hafalan al-quran juz 30 terisi 20 ayat atau lebih otomatis input poin prestasi sub poin Melaksanakan hafalan Al-quran sebanyak 20 ayat sekali duduk tanpa approve admin', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-TAHFIDZ'], ['grade' => '10']);
    $examiner = User::factory()->create(['role' => 'wali_kelas', 'roles' => ['wali_kelas']]);
    $teacher = Teacher::create([
        'user_id' => $examiner->id,
        'name' => 'Guru Penguji',
        'nip' => '199001012026011001',
    ]);
    $class->update(['homeroom_teacher_id' => $teacher->id]);

    $student = Student::create([
        'nis' => 'TEST-QURAN-006',
        'nisn' => '0098765006',
        'name' => 'Siswa Hafalan 20 Ayat',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-15';

    // Submit form setor hafalan 20 ayat
    $response = $this->actingAs($examiner)->post(route('tahfidz.store'), [
        'student_id' => $student->id,
        'surah_number' => 90, // Al-Balad
        'ayat_range' => 'Ayat 1 s.d. 20 (Selesai)',
        'ayat_count' => 20,
        'makhraj' => 'Baik',
        'tajwid' => 'Baik',
        'kelancaran' => 'Baik',
        'overall_grade' => 'Lulus',
        'deposit_date' => $today,
        'deposit_time' => '10:00',
        'examiner_name' => 'Guru Penguji',
    ]);

    $response->assertRedirect(route('tahfidz.student', $student->id));

    $itemR13 = app(DisciplineAutomationService::class)->getAchievementItemR13();
    $achievement = Achievement::where('student_id', $student->id)
        ->where('item_id', $itemR13->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->title)->toBe("Melaksanakan hafalan Al-qur'an sebanyak 20 ayat sekali duduk");
    expect($achievement->points)->toBe(10);
    expect($achievement->status)->toBe('DIVERIFIKASI');
});
