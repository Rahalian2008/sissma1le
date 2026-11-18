<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

test('barcode scan with session_type checkin and bypass_time records MASUK even in afternoon', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 18:52:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII MIPA 1',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'MIPA',
    ]);

    $student = Student::create([
        'name' => 'Ahmad Fulan',
        'nis' => '12345',
        'nisn' => '0012345678',
        'class_id' => $class->id,
        'rfid_uid' => 'CARD-1234',
        'is_active' => true,
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->postJson(route('attendance.barcode.scan'), [
        'barcode' => '0012345678',
        'session_type' => 'checkin',
        'bypass_time' => true,
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
    ]);

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', '2026-11-18')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBeIn(['HADIR', 'TERLAMBAT'])
        ->and($attendance->time)->not->toBeNull()
        ->and($attendance->time_out)->toBeNull();

    Carbon::setTestNow();
});

test('rfid scan with session_type checkin and bypass_time records MASUK even in afternoon', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 18:52:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII MIPA 2',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'MIPA',
    ]);

    $student = Student::create([
        'name' => 'Siti Aminah',
        'nis' => '54321',
        'nisn' => '0098765432',
        'class_id' => $class->id,
        'rfid_uid' => 'RFID-9988',
        'is_active' => true,
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->postJson(route('attendance.rfid.scan'), [
        'rfid_uid' => 'RFID-9988',
        'session_type' => 'checkin',
        'bypass_time' => true,
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
    ]);

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', '2026-11-18')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBeIn(['HADIR', 'TERLAMBAT'])
        ->and($attendance->time)->not->toBeNull()
        ->and($attendance->time_out)->toBeNull();

    Carbon::setTestNow();
});
