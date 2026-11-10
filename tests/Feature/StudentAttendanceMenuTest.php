<?php

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

test('student has attendance access permission in active role', function () {
    $userStudent = User::factory()->create([
        'name' => 'Siswa Test Role',
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    expect($userStudent->canAccessInActiveRole('attendance'))->toBeTrue();
    expect($userStudent->canAccessInActiveRole('kesiswaan'))->toBeTrue();
    expect($userStudent->canAccessModule('kesiswaan'))->toBeTrue();
});

test('student can see attendance menu in sidebar and access attendance index', function () {
    $class = SchoolClass::firstOrCreate(['name' => 'X-A-PRES'], ['grade' => '10', 'major' => 'Umum']);

    $userStudent = User::factory()->create([
        'name' => 'Siswa Test Presensi',
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $student = Student::create([
        'user_id' => $userStudent->id,
        'class_id' => $class->id,
        'name' => 'Siswa Test Presensi',
        'gender' => 'L',
        'nisn' => '8880001111',
        'nis' => '8001',
        'is_active' => true,
    ]);

    $response = $this->actingAs($userStudent)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee(route('attendance.index'));
    $response->assertSee('Presensi Siswa');

    $attendanceResponse = $this->actingAs($userStudent)->get(route('attendance.index'));
    $attendanceResponse->assertStatus(200);
    $attendanceResponse->assertSee('Presensi');
});
