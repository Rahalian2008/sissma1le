<?php

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use Illuminate\Support\Str;

test('verified violation and achievement show wakasek kesiswaan identity as verifikator', function () {
    $wakasek = Teacher::getWakasekKesiswaan();

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-Merdeka-1',
        'grade' => 'X',
        'major' => 'Umum',
    ]);

    $student = Student::create([
        'nis' => '99'.rand(1000, 9999),
        'nisn' => '00'.rand(1000000, 9999999),
        'name' => 'Siswa Verifikator Test',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $vCat = ViolationCategory::first() ?? ViolationCategory::create([
        'code' => 'K1',
        'name' => 'Kedisiplinan',
    ]);
    $vItem = ViolationItem::first() ?? ViolationItem::create([
        'category_id' => $vCat->id,
        'code' => 'P1.1',
        'name' => 'Terlambat',
        'points' => 5,
    ]);

    $violation = Violation::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'category_id' => $vCat->id,
        'item_id' => $vItem->id,
        'violation_code' => 'VIO-'.rand(1000, 9999),
        'title' => 'Terlambat Masuk Sekolah',
        'points' => 5,
        'date' => now()->toDateString(),
        'status' => 'DIVERIFIKASI',
        'verified_by' => 1,
        'verified_at' => now(),
    ]);

    expect($violation->verifier_display)->toContain($wakasek['name']);
    expect($violation->verifier_display)->toContain('Wakasek Kesiswaan');
    expect($violation->verifier_display)->toContain($wakasek['nip']);

    $aCat = AchievementCategory::first() ?? AchievementCategory::create([
        'code' => 'R1',
        'name' => 'Akademik',
    ]);
    $aItem = AchievementItem::first() ?? AchievementItem::create([
        'category_id' => $aCat->id,
        'code' => 'A1.1',
        'name' => 'Juara 1 Sains',
        'points' => 20,
    ]);

    $achievement = Achievement::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'category_id' => $aCat->id,
        'item_id' => $aItem->id,
        'achievement_code' => 'ACH-'.rand(1000, 9999),
        'title' => 'Juara Olimpiade Sains',
        'level' => 'KABUPATEN',
        'points' => 20,
        'date' => now()->toDateString(),
        'status' => 'DIVERIFIKASI',
        'verified_by' => 1,
        'verified_at' => now(),
    ]);

    expect($achievement->verifier_display)->toContain($wakasek['name']);
    expect($achievement->verifier_display)->toContain('Wakasek Kesiswaan');
    expect($achievement->verifier_display)->toContain($wakasek['nip']);
});

test('wali kelas can access bk dashboard and view is scoped to homeroom class', function () {
    $rand = Str::lower(Str::random(5));

    $classA = SchoolClass::create([
        'name' => "X-TestA-{$rand}",
        'grade' => 'X',
        'major' => 'Umum',
    ]);
    $classB = SchoolClass::create([
        'name' => "X-TestB-{$rand}",
        'grade' => 'X',
        'major' => 'Umum',
    ]);

    $teacherUser = User::create([
        'name' => "Guru Wali {$rand}",
        'email' => "wali_{$rand}@sman1lengkong.sch.id",
        'username' => "nip_{$rand}",
        'nip' => '19850101201001100'.rand(1, 9),
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas'],
        'password' => bcrypt('password'),
    ]);

    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => "Guru Wali {$rand}",
        'nip' => $teacherUser->nip,
        'class_id' => $classA->id,
    ]);

    $classA->update(['homeroom_teacher_id' => $teacher->id]);

    $studentA = Student::create([
        'nis' => '88'.rand(1000, 9999),
        'nisn' => '00'.rand(1000000, 9999999),
        'name' => "Siswa Binaan {$rand}",
        'class_id' => $classA->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $studentB = Student::create([
        'nis' => '89'.rand(1000, 9999),
        'nisn' => '01'.rand(1000000, 9999999),
        'name' => "Siswa Luar {$rand}",
        'class_id' => $classB->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    // Akses BK Dashboard oleh Wali Kelas
    $response = $this->actingAs($teacherUser)->get(route('bk.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Bimbingan & Konseling (BK)', false);

    // Profile 360 Siswa Binaan vs Siswa Luar
    $respA = $this->actingAs($teacherUser)->get(route('bk.student.profile360', $studentA));
    $respA->assertStatus(200);

    $respB = $this->actingAs($teacherUser)->get(route('bk.student.profile360', $studentB));
    $respB->assertStatus(403);
});
