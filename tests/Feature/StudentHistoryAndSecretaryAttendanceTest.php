<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

test('student attendance history only displays logged-in student records and prevents viewing others', function () {
    $classA = SchoolClass::firstOrCreate(['name' => 'X-A-HIST'], ['grade' => '10', 'major' => 'Umum']);
    $classB = SchoolClass::firstOrCreate(['name' => 'X-B-HIST'], ['grade' => '10', 'major' => 'Umum']);

    $userStudent1 = User::factory()->create([
        'name' => 'Siswa Satu',
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);
    $student1 = Student::create([
        'user_id' => $userStudent1->id,
        'class_id' => $classA->id,
        'name' => 'Siswa Satu',
        'gender' => 'L',
        'nisn' => '7770001111',
        'nis' => '7001',
        'is_active' => true,
    ]);

    $userStudent2 = User::factory()->create([
        'name' => 'Siswa Dua',
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);
    $student2 = Student::create([
        'user_id' => $userStudent2->id,
        'class_id' => $classB->id,
        'name' => 'Siswa Dua',
        'gender' => 'P',
        'nisn' => '7770002222',
        'nis' => '7002',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    Attendance::create([
        'student_id' => $student1->id,
        'class_id' => $classA->id,
        'nis' => $student1->nis,
        'nisn' => $student1->nisn,
        'student_name' => $student1->name,
        'date' => $today,
        'time' => '06:55:00',
        'method' => 'RFID',
        'status' => 'HADIR',
        'verification_status' => 'DIVERIFIKASI',
    ]);

    Attendance::create([
        'student_id' => $student2->id,
        'class_id' => $classB->id,
        'nis' => $student2->nis,
        'nisn' => $student2->nisn,
        'student_name' => $student2->name,
        'date' => $today,
        'time' => '07:05:00',
        'method' => 'RFID',
        'status' => 'TERLAMBAT',
        'verification_status' => 'DIVERIFIKASI',
    ]);

    // Acting as Siswa Satu
    $response = $this->actingAs($userStudent1)->get(route('attendance.history', [
        'student_id' => $student2->id, // Attempt to query another student
        'class_id' => $classB->id,     // Attempt to query another class
        'search' => 'Siswa Dua',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Siswa Satu');
    $response->assertSee('Riwayat Presensi Mandiri Siswa');
    $response->assertDontSee('Siswa Dua');
});

test('class secretary can view daily attendance report read-only for own class and cannot verify attendance', function () {
    $classSec = SchoolClass::firstOrCreate(['name' => 'XI-SEC-CLASS'], ['grade' => '11', 'major' => 'IPA']);
    $classOther = SchoolClass::firstOrCreate(['name' => 'XI-OTHER-CLASS'], ['grade' => '11', 'major' => 'IPS']);

    $userSec = User::factory()->create([
        'name' => 'Sekretaris Kelas',
        'role' => 'sekretaris_kelas',
        'roles' => ['siswa', 'sekretaris_kelas'],
    ]);
    $studentSec = Student::create([
        'user_id' => $userSec->id,
        'class_id' => $classSec->id,
        'name' => 'Sekretaris Kelas',
        'gender' => 'P',
        'nisn' => '7770003333',
        'nis' => '7003',
        'is_active' => true,
    ]);

    $studentPeer = Student::create([
        'class_id' => $classSec->id,
        'name' => 'Teman Sekelas',
        'gender' => 'L',
        'nisn' => '7770004444',
        'nis' => '7004',
        'is_active' => true,
    ]);

    $studentOtherClass = Student::create([
        'class_id' => $classOther->id,
        'name' => 'Siswa Kelas Lain',
        'gender' => 'L',
        'nisn' => '7770005555',
        'nis' => '7005',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    $peerAtt = Attendance::create([
        'student_id' => $studentPeer->id,
        'class_id' => $classSec->id,
        'nis' => $studentPeer->nis,
        'nisn' => $studentPeer->nisn,
        'student_name' => $studentPeer->name,
        'date' => $today,
        'time' => '07:00:00',
        'method' => 'RFID',
        'status' => 'HADIR',
        'verification_status' => 'BELUM_DIVERIFIKASI',
    ]);

    $otherAtt = Attendance::create([
        'student_id' => $studentOtherClass->id,
        'class_id' => $classOther->id,
        'nis' => $studentOtherClass->nis,
        'nisn' => $studentOtherClass->nisn,
        'student_name' => $studentOtherClass->name,
        'date' => $today,
        'time' => '07:00:00',
        'method' => 'RFID',
        'status' => 'HADIR',
        'verification_status' => 'BELUM_DIVERIFIKASI',
    ]);

    // Secretary views attendance report
    $response = $this->actingAs($userSec)->get(route('attendance.report', [
        'class_id' => $classOther->id, // Attempt to select other class, should lock to own class
        'date' => $today,
    ]));

    $response->assertStatus(200);
    $response->assertSee('Mode Monitoring Sekretaris Kelas');
    $response->assertSee('Teman Sekelas');
    $response->assertDontSee('Siswa Kelas Lain');

    // Secretary attempts to verify attendance -> must be aborted 403
    $verifyResponse = $this->actingAs($userSec)->post(route('attendance.verify', $peerAtt), [
        'status' => 'HADIR',
    ]);
    $verifyResponse->assertStatus(403);
});

test('class secretary can record manual attendance for own class and other class is rejected', function () {
    $classSec = SchoolClass::firstOrCreate(['name' => 'XI-SEC-MAN'], ['grade' => '11', 'major' => 'IPA']);
    $classOther = SchoolClass::firstOrCreate(['name' => 'XI-OTHER-MAN'], ['grade' => '11', 'major' => 'IPS']);

    $userSec = User::factory()->create([
        'name' => 'Sekretaris Hebat',
        'role' => 'sekretaris_kelas',
        'roles' => ['siswa', 'sekretaris_kelas'],
    ]);
    $studentSec = Student::create([
        'user_id' => $userSec->id,
        'class_id' => $classSec->id,
        'name' => 'Sekretaris Hebat',
        'gender' => 'P',
        'nisn' => '7770006666',
        'nis' => '7006',
        'is_active' => true,
    ]);

    $studentTarget = Student::create([
        'class_id' => $classSec->id,
        'name' => 'Siswa Diabsenkan',
        'gender' => 'L',
        'nisn' => '7770007777',
        'nis' => '7007',
        'is_active' => true,
    ]);

    $studentOther = Student::create([
        'class_id' => $classOther->id,
        'name' => 'Siswa Kelas Sebelah',
        'gender' => 'L',
        'nisn' => '7770008888',
        'nis' => '7008',
        'is_active' => true,
    ]);

    $today = Carbon::now('Asia/Jakarta')->toDateString();

    // 1. Bulk attendance for other class is rejected
    $otherClassResponse = $this->actingAs($userSec)->post(route('attendance.manual.bulk'), [
        'class_id' => $classOther->id,
        'date' => $today,
        'statuses' => [
            $studentOther->id => 'HADIR',
        ],
    ]);
    $otherClassResponse->assertRedirect();
    $otherClassResponse->assertSessionHas('error');

    // 2. Bulk attendance for own class succeeds
    $bulkResponse = $this->actingAs($userSec)->post(route('attendance.manual.bulk'), [
        'class_id' => $classSec->id,
        'date' => $today,
        'statuses' => [
            $studentTarget->id => 'HADIR',
        ],
    ]);
    $bulkResponse->assertRedirect();
    $bulkResponse->assertSessionHas('success');

    $recordedBulk = Attendance::where('student_id', $studentTarget->id)->whereDate('date', $today)->first();
    expect($recordedBulk)->not->toBeNull()
        ->and($recordedBulk->status)->toBe('HADIR')
        ->and($recordedBulk->method)->toBe('MANUAL')
        ->and($recordedBulk->approved_by)->toBe($userSec->id);

    // 3. Store individual attendance with base64 selfie data
    $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

    $storeResponse = $this->actingAs($userSec)->post(route('attendance.manual.store'), [
        'student_id' => $studentTarget->id,
        'date' => $today,
        'time' => '07:15',
        'status' => 'HADIR',
        'notes' => 'Absen manual oleh sekretaris kelas beserta foto selfie.',
        'selfie_image' => $base64Image,
    ]);

    $storeResponse->assertRedirect();
    $storeResponse->assertSessionHas('success');
});
