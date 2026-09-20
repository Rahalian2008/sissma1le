<?php

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    // Set test time to 06:45 (school morning on-time check-in window: 06:00 - 07:00)
    Carbon::setTestNow(Carbon::create(2026, 9, 17, 6, 45, 0));

    // Base 64 1x1 png image
    $this->dummyBase64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
});

afterEach(function () {
    Carbon::setTestNow(); // reset
});

test('student within geofence radius can check in using live camera selfie', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $location = AttendanceLocation::first();

    // Ensure no attendance exists today for clean test
    Attendance::where('student_id', $student->id)->whereDate('date', today())->delete();

    $response = $this->actingAs($user)->postJson('/attendance/selfie', [
        'latitude' => $location->latitude,
        'longitude' => $location->longitude,
        'selfie_image' => $this->dummyBase64Image,
        'notes' => 'Presensi selfie tes di sekolah',
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', today())
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->method)->toBe('SELFIE');
    expect($attendance->status)->toBe('HADIR');
    expect($attendance->selfie)->not->toBeNull();
    expect($attendance->selfie->face_detected)->toBeTrue();
    expect($attendance->verification_status)->toBe('VALID');
});

test('student outside geofence radius is rejected', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();

    Attendance::where('student_id', $student->id)->whereDate('date', today())->delete();

    // Out of radius coordinates (~10km away)
    $response = $this->actingAs($user)->postJson('/attendance/selfie', [
        'latitude' => -7.450000,
        'longitude' => 112.150000,
        'selfie_image' => $this->dummyBase64Image,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('error_code', 'OUT_OF_RADIUS');

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', today())
        ->where('method', 'SELFIE_GPS')
        ->first();

    expect($attendance)->toBeNull();
});

test('attendance selfie rejects invalid or missing live camera image stream', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $location = AttendanceLocation::first();

    Attendance::where('student_id', $student->id)->whereDate('date', today())->delete();

    $response = $this->actingAs($user)->postJson('/attendance/selfie', [
        'latitude' => $location->latitude,
        'longitude' => $location->longitude,
        'selfie_image' => 'plain-text-not-data-uri',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('success', false);
    $response->assertJsonPath('error_code', 'INVALID_CAMERA_STREAM');
});

test('duplicate check-in on the same day is prevented', function () {
    $user = User::where('role', 'siswa')->first();
    $student = Student::where('user_id', $user->id)->first();
    $location = AttendanceLocation::first();

    Attendance::where('student_id', $student->id)->whereDate('date', today())->delete();

    // First check-in
    $firstResponse = $this->actingAs($user)->postJson('/attendance/selfie', [
        'latitude' => $location->latitude,
        'longitude' => $location->longitude,
        'selfie_image' => $this->dummyBase64Image,
    ]);
    $firstResponse->assertStatus(200);

    // Second check-in
    $secondResponse = $this->actingAs($user)->postJson('/attendance/selfie', [
        'latitude' => $location->latitude,
        'longitude' => $location->longitude,
        'selfie_image' => $this->dummyBase64Image,
    ]);

    $secondResponse->assertStatus(422);
    $secondResponse->assertJsonPath('success', false);
    $secondResponse->assertJsonPath('error_code', 'ALREADY_ATTENDED');
});

test('rfid scan endpoint records student presence with valid rfid uid', function () {
    $student = Student::whereNotNull('rfid_uid')->first();

    if (! $student) {
        $student = Student::first();
        $student->update(['rfid_uid' => 'E2806894000040123456789A']);
    }

    Attendance::where('student_id', $student->id)->whereDate('date', today())->delete();

    $response = $this->postJson('/api/attendance/rfid', [
        'rfid_uid' => $student->rfid_uid,
        'device_code' => 'RFID-GATE-01',
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', today())
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->method)->toBe('RFID');
});
