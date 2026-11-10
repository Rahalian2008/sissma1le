<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

test('admin sees school-wide leave recap on quick action menu', function () {
    $admin = User::where('role', 'admin')->first();
    $students = Student::take(2)->get();
    $studentA = $students[0];
    $studentB = $students[1];

    Attendance::create([
        'student_id' => $studentA->id,
        'class_id' => $studentA->class_id,
        'nis' => $studentA->nis,
        'nisn' => $studentA->nisn,
        'student_name' => $studentA->name,
        'class_name' => $studentA->schoolClass?->name ?? 'XI-IPA-1',
        'date' => Carbon::today()->toDateString(),
        'status' => 'IZIN',
        'method' => 'IZIN_SAKIT',
        'verification_status' => 'PERLU_VERIFIKASI',
    ]);

    Attendance::create([
        'student_id' => $studentB->id,
        'class_id' => $studentB->class_id,
        'nis' => $studentB->nis,
        'nisn' => $studentB->nisn,
        'student_name' => $studentB->name,
        'class_name' => $studentB->schoolClass?->name ?? 'X-1',
        'date' => Carbon::today()->toDateString(),
        'status' => 'SAKIT',
        'method' => 'IZIN_SAKIT',
        'verification_status' => 'DIVERIFIKASI',
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Approve Izin');
    $response->assertSee('Admin • Seluruh Kelas');
    $response->assertSee('Total:');
    $response->assertSee('Menunggu');
    $response->assertSee('Disetujui');
    $response->assertSee('Ditolak');
});

test('wali kelas sees homeroom-scoped leave recap on quick action menu', function () {
    $homeroomUser = User::where('role', 'wali_kelas')->first();
    $homeroomClass = $homeroomUser->teacher?->homeroomClass;
    expect($homeroomClass)->not->toBeNull();

    $otherClass = SchoolClass::where('id', '!=', $homeroomClass->id)->first();
    $studentHome = Student::where('class_id', $homeroomClass->id)->first();

    // Create student in other class if needed
    $studentOther = Student::where('class_id', $otherClass->id)->first();
    if (! $studentOther) {
        $studentOther = Student::create([
            'user_id' => null,
            'nis' => '9999901',
            'nisn' => '9999990001',
            'name' => 'Siswa Kelas Lain',
            'gender' => 'L',
            'class_id' => $otherClass->id,
            'academic_year_id' => $homeroomClass->academic_year_id,
            'is_active' => true,
        ]);
    }

    Attendance::create([
        'student_id' => $studentHome->id,
        'class_id' => $homeroomClass->id,
        'nis' => $studentHome->nis,
        'nisn' => $studentHome->nisn,
        'student_name' => $studentHome->name,
        'class_name' => $homeroomClass->name,
        'date' => Carbon::today()->toDateString(),
        'status' => 'IZIN',
        'method' => 'IZIN_SAKIT',
        'verification_status' => 'PERLU_VERIFIKASI',
    ]);

    Attendance::create([
        'student_id' => $studentOther->id,
        'class_id' => $otherClass->id,
        'nis' => $studentOther->nis,
        'nisn' => $studentOther->nisn,
        'student_name' => $studentOther->name,
        'class_name' => $otherClass->name,
        'date' => Carbon::today()->toDateString(),
        'status' => 'SAKIT',
        'method' => 'IZIN_SAKIT',
        'verification_status' => 'PERLU_VERIFIKASI',
    ]);

    $response = $this->actingAs($homeroomUser)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Approve Izin');
    $response->assertSee('Wali Kelas • '.$homeroomClass->name);
    $response->assertSee('1 MENUNGGU');
});

test('kesiswaan and general guru see appropriate role badges on quick action menu', function () {
    // 1. Kesiswaan
    $kesiswaanUser = User::where('role', 'kesiswaan')->first();
    if (! $kesiswaanUser) {
        $kesiswaanUser = User::create([
            'name' => 'Staf Kesiswaan Test',
            'email' => 'kesiswaan_test@sma1le.sch.id',
            'role' => 'kesiswaan',
            'password' => bcrypt('password'),
        ]);
    }

    $responseKesiswaan = $this->actingAs($kesiswaanUser)->get(route('dashboard'));
    $responseKesiswaan->assertStatus(200);
    $responseKesiswaan->assertDontSee('Approve Izin');

    // 2. Guru without homeroom
    $guruUser = User::where('role', 'guru')->first();
    if ($guruUser) {
        $responseGuru = $this->actingAs($guruUser)->get(route('dashboard'));
        $responseGuru->assertStatus(200);
        $responseGuru->assertDontSee('Approve Izin');
    }
});
