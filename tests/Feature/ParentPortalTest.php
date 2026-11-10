<?php

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Attendance;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\Student;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Carbon\Carbon;

test('parent portal can be accessed without login and displays search form', function () {
    $response = $this->get('/portal-ortu');

    $response->assertStatus(200);
    $response->assertSee('Portal Orang Tua');
    $response->assertSee('Pemantauan Perkembangan Ananda', false);
    $response->assertSee('Buka Portal');
});

test('parent portal displays student profile, overall rank, raport, presensi, prestasi, pelanggaran, and habits', function () {
    $student = Student::where('is_active', true)->first();
    if (! $student) {
        $student = Student::factory()->create(['is_active' => true, 'nisn' => '0011223344']);
    }

    // Add sample verified achievement
    $achCategory = AchievementCategory::first() ?? AchievementCategory::create(['name' => 'Akademik', 'code' => 'R1']);
    Achievement::create([
        'student_id' => $student->id,
        'class_id' => $student->class_id,
        'category_id' => $achCategory->id,
        'title' => 'Juara 1 Olimpiade Sains',
        'level' => 'Kabupaten',
        'points' => 50,
        'date' => Carbon::now()->subDays(5),
        'status' => 'DIVERIFIKASI',
    ]);

    // Add sample verified violation
    $violCategory = ViolationCategory::first() ?? ViolationCategory::create(['name' => 'Kedisiplinan', 'code' => 'P1']);
    Violation::create([
        'student_id' => $student->id,
        'class_id' => $student->class_id,
        'category_id' => $violCategory->id,
        'title' => 'Terlambat Masuk Sekolah',
        'points' => 5,
        'date' => Carbon::now()->subDays(2),
        'status' => 'DIVERIFIKASI',
    ]);

    // Add sample attendance
    Attendance::where('student_id', $student->id)
        ->where('date', Carbon::today('Asia/Jakarta')->toDateString())
        ->delete();

    Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => Carbon::today('Asia/Jakarta')->toDateString(),
        'time' => '06:45:00',
        'status' => 'HADIR',
        'method' => 'RFID_CARD',
    ]);

    // Add sample habit log
    $today = Carbon::today('Asia/Jakarta')->toDateString();
    HabitDailyLog::where('student_id', $student->id)->delete();

    $habit = Habit::first();
    if ($habit) {
        HabitDailyLog::create([
            'student_id' => $student->id,
            'habit_id' => $habit->id,
            'date' => $today,
            'is_completed' => true,
            'check_time' => '06:00:00',
        ]);
    }

    $response = $this->get('/portal-ortu?nisn='.$student->nisn);

    $response->assertStatus(200);
    $response->assertSee($student->name);
    $response->assertSee('Peringkat #');
    $response->assertSee('Se-Sekolah');
    $response->assertSee('Raport Digital');
    $response->assertSee('Juara 1 Olimpiade Sains');
    $response->assertSee('Terlambat Masuk Sekolah');
    $response->assertSee('Presensi Hari Ini');
    $response->assertSee('7 Kebiasaan');
});

test('parent portal shows error message if NISN is invalid or not found', function () {
    $response = $this->get('/portal-ortu?nisn=9999999999');

    $response->assertStatus(200);
    $response->assertSee('Data Siswa Tidak Ditemukan');
});

test('parent can access and print official digital report with valid NISN', function () {
    $student = Student::where('is_active', true)->first();
    if (! $student) {
        $student = Student::factory()->create(['is_active' => true, 'nisn' => '0011223344']);
    }

    $response = $this->get(route('parent.portal.raport', [$student->id, 'nisn' => $student->nisn]));

    $response->assertStatus(200);
    $response->assertSee('RAPORT DIGITAL PERKEMBANGAN SISWA TERPADU');
    $response->assertSee($student->name);
    $response->assertSee('Kembali ke Portal Orang Tua');
});

test('parent portal print route forbids access if NISN does not match', function () {
    $student = Student::where('is_active', true)->first();
    if (! $student) {
        $student = Student::factory()->create(['is_active' => true, 'nisn' => '0011223344']);
    }

    $response = $this->get(route('parent.portal.raport', [$student->id, 'nisn' => 'wrong_nisn']));

    $response->assertStatus(403);
});
