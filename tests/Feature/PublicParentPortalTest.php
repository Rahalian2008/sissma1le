<?php

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;

test('parent portal page is accessible without login', function () {
    $response = $this->get(route('parent.portal'));

    $response->assertOk();
    $response->assertSee('Portal Orang Tua');
    $response->assertSee('Tanpa Login');
});

test('parent can query student data using valid nisn without login', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-A',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::first();
    if (! $student) {
        $student = Student::create([
            'name' => 'Siswa Test Ortu',
            'nis' => '998877',
            'nisn' => '0089998877',
            'gender' => 'L',
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'is_active' => true,
        ]);
    }

    $response = $this->get(route('parent.portal', ['nisn' => $student->nisn]));

    $response->assertOk();
    $response->assertSee($student->name);
    $response->assertSee($student->nisn);
    $response->assertSee('Presensi Hari Ini');
});

test('parent portal shows not found message for invalid nisn', function () {
    $response = $this->get(route('parent.portal', ['nisn' => '999999999999999']));

    $response->assertOk();
    $response->assertSee('Data Siswa Tidak Ditemukan');
});

test('login page contains direct link to parent portal', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee(route('parent.portal'));
    $response->assertSee('Portal Khusus Orang Tua / Wali');
});
