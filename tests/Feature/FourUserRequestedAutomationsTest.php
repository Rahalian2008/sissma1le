<?php

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
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
});

test('perintah 1: presensi utama status ALPA hingga jam 11.30 WIB otomatis input pelanggaran P1.3 tanpa approve admin (1 hari 1x)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-AUTO'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-AUTO-P13',
        'nisn' => '0098765431',
        'name' => 'Siswa Alpa Harian',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-14';

    // Buat presensi utama status ALPA
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

    // Jalankan otomasi jam 11:30 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '11:30',
        '--date' => $today,
    ]);

    // Cek pelanggaran terbuat
    $violation = Violation::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->first();

    expect($violation)->not->toBeNull();
    expect($violation->title)->toContain('Alpa');
    expect($violation->points)->toBe(5);
    expect($violation->status)->toBe('DIVERIFIKASI'); // Tidak perlu approve admin
    expect($violation->verified_at)->not->toBeNull();

    // Uji Idempotensi: Jalankan lagi di hari yang sama, tidak boleh menduplikasi pelanggaran (1 hari 1x)
    Artisan::call('discipline:process-automations', [
        '--time' => '11:30',
        '--date' => $today,
    ]);

    $count = Violation::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->count();

    expect($count)->toBe(1);
});

test('perintah 2: presensi utama ALPA 3 hari berturut-turut pada hari ke-4 jam 08.00 WIB otomatis input pelanggaran P1.7 (1x seminggu)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-AUTO-3H'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-AUTO-P17',
        'nisn' => '0098765432',
        'name' => 'Siswa Alpa Tiga Hari',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Hari 1, 2, 3 ALPA
    $day1 = '2026-10-12';
    $day2 = '2026-10-13';
    $day3 = '2026-10-14';
    $day4 = '2026-10-15'; // Hari ke-4

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

    // Jalankan otomasi hari ke-4 jam 08:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '08:00',
        '--date' => $day4,
    ]);

    $violation = Violation::where('student_id', $student->id)
        ->whereDate('date', $day4)
        ->first();

    expect($violation)->not->toBeNull();
    expect($violation->title)->toContain('Tidak masuk selama 3 (tiga hari)');
    expect($violation->points)->toBe(10);
    expect($violation->status)->toBe('DIVERIFIKASI'); // Tanpa approve admin

    // Uji Batasan 1x dalam 1 minggu: Jalankan lagi di minggu yang sama (misal hari ke-5)
    $day5 = '2026-10-16';
    Artisan::call('discipline:process-automations', [
        '--time' => '08:00',
        '--date' => $day5,
    ]);

    $itemP17 = app(DisciplineAutomationService::class)->getViolationItemP17();
    $totalViolationsThisWeek = Violation::where('student_id', $student->id)
        ->where('item_id', $itemP17->id)
        ->count();

    expect($totalViolationsThisWeek)->toBe(1); // Hanya 1 kali dalam 1 minggu
});

test('perintah 3: shalat dzuhur/jumat berjamaah status HADIR hingga jam 14.00 WIB otomatis input prestasi R1.1 tanpa approve admin (1 hari 1x)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-AUTO-DZ'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-AUTO-R11',
        'nisn' => '0098765433',
        'name' => 'Siswa Rajin Shalat Dzuhur',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-14';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $today,
        'time' => '12:30:00',
        'status' => 'HADIR',
        'method' => 'QR_SCANNER',
    ]);

    // Jalankan otomasi jam 14:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '14:00',
        '--date' => $today,
    ]);

    $achievement = Achievement::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->title)->toContain('dzuhur');
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI'); // Tanpa approve admin

    // Uji Idempotensi: Jalankan lagi di hari yang sama
    Artisan::call('discipline:process-automations', [
        '--time' => '14:00',
        '--date' => $today,
    ]);

    $count = Achievement::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->count();

    expect($count)->toBe(1); // 1 hari 1 kali
});

test('perintah 4: shalat dhuha bersama status HADIR hingga jam 10.00 WIB otomatis input prestasi R1.2 tanpa approve admin (1 hari 1x)', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-AUTO-DH'], ['grade' => '10']);
    $student = Student::create([
        'nis' => 'TEST-AUTO-R12',
        'nisn' => '0098765434',
        'name' => 'Siswa Rajin Shalat Dhuha',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $today = '2026-10-14';

    WorshipAttendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $today,
        'time' => '07:30:00',
        'status' => 'HADIR',
        'method' => 'QR_SCANNER',
    ]);

    // Jalankan otomasi jam 10:00 WIB
    Artisan::call('discipline:process-automations', [
        '--time' => '10:00',
        '--date' => $today,
    ]);

    $achievement = Achievement::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->first();

    expect($achievement)->not->toBeNull();
    expect($achievement->title)->toContain('dhuha');
    expect($achievement->points)->toBe(5);
    expect($achievement->status)->toBe('DIVERIFIKASI'); // Tanpa approve admin

    // Uji Idempotensi: Jalankan lagi di hari yang sama
    Artisan::call('discipline:process-automations', [
        '--time' => '10:00',
        '--date' => $today,
    ]);

    $count = Achievement::where('student_id', $student->id)
        ->whereDate('date', $today)
        ->count();

    expect($count)->toBe(1); // 1 hari 1 kali
});

test('perintah 5 (akurasi): skenario negatif membuktikan sistem tidak salah input pelanggaran atau prestasi', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-ACCURACY'], ['grade' => '10']);

    // 1. Siswa yang HADIR di presensi utama tidak boleh mendapat P1.3
    $studentHadir = Student::create([
        'nis' => 'TEST-ACC-1',
        'nisn' => '0098765435',
        'name' => 'Siswa Hadir Tepat Waktu',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = '2026-10-14';

    Attendance::create([
        'student_id' => $studentHadir->id,
        'nis' => $studentHadir->nis,
        'nisn' => $studentHadir->nisn,
        'student_name' => $studentHadir->name,
        'class_id' => $class->id,
        'date' => $today,
        'status' => 'HADIR',
        'method' => 'RFID',
    ]);

    // 2. Siswa yang ALPA hanya 2 hari (hari 1 & 3, tapi hari 2 IZIN) tidak boleh mendapat P1.7
    $studentIzin = Student::create([
        'nis' => 'TEST-ACC-2',
        'nisn' => '0098765436',
        'name' => 'Siswa Izin Hari Kedua',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    Attendance::create([
        'student_id' => $studentIzin->id,
        'nis' => $studentIzin->nis,
        'nisn' => $studentIzin->nisn,
        'student_name' => $studentIzin->name,
        'class_id' => $class->id,
        'date' => '2026-10-11',
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);
    Attendance::create([
        'student_id' => $studentIzin->id,
        'nis' => $studentIzin->nis,
        'nisn' => $studentIzin->nisn,
        'student_name' => $studentIzin->name,
        'class_id' => $class->id,
        'date' => '2026-10-12',
        'status' => 'IZIN',
        'method' => 'MANUAL',
    ]);
    Attendance::create([
        'student_id' => $studentIzin->id,
        'nis' => $studentIzin->nis,
        'nisn' => $studentIzin->nisn,
        'student_name' => $studentIzin->name,
        'class_id' => $class->id,
        'date' => '2026-10-13',
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);

    // 3. Siswa yang statusnya ALPA di shalat tidak boleh mendapat prestasi
    $studentAlpaShalat = Student::create([
        'nis' => 'TEST-ACC-3',
        'nisn' => '0098765437',
        'name' => 'Siswa Tidak Shalat',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    WorshipAttendance::create([
        'student_id' => $studentAlpaShalat->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
        'date' => $today,
        'time' => '12:30:00',
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);

    WorshipAttendance::create([
        'student_id' => $studentAlpaShalat->id,
        'class_id' => $class->id,
        'activity_type' => WorshipAttendance::ACTIVITY_DHUHA,
        'date' => $today,
        'time' => '07:30:00',
        'status' => 'ALPA',
        'method' => 'SISTEM',
    ]);

    // Jalankan seluruh otomasi
    Artisan::call('discipline:process-automations', [
        '--time' => 'all',
        '--date' => $today,
    ]);

    // Verifikasi siswa hadir tidak dapat pelanggaran P1.3
    $vHadir = Violation::where('student_id', $studentHadir->id)->count();
    expect($vHadir)->toBe(0);

    // Verifikasi siswa tidak 3 hari berturut-turut alpa tidak dapat P1.7
    $vIzin = Violation::where('student_id', $studentIzin->id)->count();
    expect($vIzin)->toBe(0);

    // Verifikasi siswa tidak shalat tidak dapat prestasi
    $aShalat = Achievement::where('student_id', $studentAlpaShalat->id)->count();
    expect($aShalat)->toBe(0);
});
