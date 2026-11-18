<?php

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

test('counselor or admin can delete psychological test result from bank data', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-Merdeka-1', 'grade' => 'X', 'major' => 'Umum']);
    $student = Student::create([
        'nis' => '888801',
        'nisn' => '0123456789',
        'name' => 'Siswa Test Hapus',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $test = BkPsychologicalTest::first() ?? BkPsychologicalTest::create([
        'slug' => 'vak',
        'name' => 'Tes VAK',
        'category' => 'Gaya Belajar',
        'short_desc' => 'Deskripsi tes VAK',
        'instructions' => 'Petunjuk pengerjaan',
        'is_active' => true,
    ]);

    $result = BkPsychologicalTestResult::create([
        'student_id' => $student->id,
        'test_id' => $test->id,
        'dominant_dimension' => 'VISUAL',
        'profile_label' => 'Gaya Belajar Visual',
        'dimension_scores' => ['VISUAL' => 10, 'AUDITORI' => 5, 'KINESTETIK' => 3],
        'analysis_summary' => 'Ringkasan analisa profil belajar',
        'answers' => [1 => 'A', 2 => 'B'],
        'taken_at' => now(),
    ]);

    $admin = User::where('role', 'admin')->first() ?? User::create([
        'name' => 'Admin Test',
        'email' => 'admin_test_delete@sman1lengkong.sch.id',
        'username' => 'admin_test_delete',
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('bk.psychological.destroy', $result));

    $response->assertRedirect(route('bk.psychological.index'));
    $this->assertDatabaseMissing('bk_psychological_test_results', ['id' => $result->id]);
});

test('bk psychological print document displays coordinator bk identity', function () {
    $result = BkPsychologicalTestResult::first();
    if (! $result) {
        $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-Merdeka-1', 'grade' => 'X', 'major' => 'Umum']);
        $student = Student::create([
            'nis' => '888802',
            'nisn' => '0123456790',
            'name' => 'Siswa Test Print',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
        ]);
        $test = BkPsychologicalTest::first() ?? BkPsychologicalTest::create([
            'slug' => 'vak',
            'name' => 'Tes VAK',
            'category' => 'Gaya Belajar',
            'short_desc' => 'Deskripsi tes VAK',
            'instructions' => 'Petunjuk pengerjaan',
            'is_active' => true,
        ]);
        $result = BkPsychologicalTestResult::create([
            'student_id' => $student->id,
            'test_id' => $test->id,
            'dominant_dimension' => 'VISUAL',
            'profile_label' => 'Gaya Belajar Visual',
            'dimension_scores' => ['VISUAL' => 10, 'AUDITORI' => 5, 'KINESTETIK' => 3],
            'analysis_summary' => 'Ringkasan analisa profil belajar',
            'answers' => [1 => 'A', 2 => 'B'],
            'taken_at' => now(),
        ]);
    }

    $admin = User::where('role', 'admin')->first();
    $coordinator = Teacher::getBkCoordinator();

    $response = $this->actingAs($admin)
        ->get(route('bk.psychological.print', $result));

    $response->assertStatus(200);
    $response->assertSee($coordinator['name']);
    $response->assertSee($coordinator['nip']);
});
