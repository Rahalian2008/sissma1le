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

test('student selfie checkout at 06:41 WIB is rejected with CHECKOUT_TOO_EARLY', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 06:41:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII Merdeka 6',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $user = User::factory()->create([
        'role' => 'student',
    ]);

    $student = Student::create([
        'name' => 'Alfan Fadila',
        'nis' => '0088039224',
        'nisn' => '0088039224',
        'class_id' => $class->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    // Student checked in at 06:01 WIB
    Attendance::create([
        'student_id' => $student->id,
        'date' => '2026-11-18',
        'time' => '06:01:00',
        'status' => 'HADIR',
        'verification_status' => 'VALID',
        'method' => 'SELFIE',
    ]);

    // Dummy 1x1 png base64
    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($user)->postJson(route('attendance.selfie.store'), [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
        'session_type' => 'checkout',
        'bypass_time' => true, // Student attempts bypass, must be IGNORED
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'error_code' => 'CHECKOUT_TOO_EARLY',
    ]);

    // Verify record was NOT updated with premature checkout
    $att = Attendance::where('student_id', $student->id)->whereDate('date', '2026-11-18')->first();
    expect($att->time_out)->toBeNull();

    Carbon::setTestNow();
});

test('student selfie checkout at 15:30 WIB is accepted', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 15:30:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII Merdeka 7',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $user = User::factory()->create([
        'role' => 'student',
    ]);

    $student = Student::create([
        'name' => 'Budi Santoso',
        'nis' => '998877',
        'nisn' => '0099887766',
        'class_id' => $class->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    Attendance::create([
        'student_id' => $student->id,
        'date' => '2026-11-18',
        'time' => '06:05:00',
        'status' => 'HADIR',
        'verification_status' => 'VALID',
        'method' => 'SELFIE',
    ]);

    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($user)->postJson(route('attendance.selfie.store'), [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
        'session_type' => 'checkout',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'session' => 'checkout',
    ]);

    $att = Attendance::where('student_id', $student->id)->whereDate('date', '2026-11-18')->first();
    expect($att->time_out)->not->toBeNull();

    Carbon::setTestNow();
});

test('student checkin before in_start at 05:30 WIB is rejected with TIME_TOO_EARLY', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 05:30:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII Merdeka 8',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $user = User::factory()->create([
        'role' => 'student',
    ]);

    $student = Student::create([
        'name' => 'Citra Lestari',
        'nis' => '887766',
        'nisn' => '0088776655',
        'class_id' => $class->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($user)->postJson(route('attendance.selfie.store'), [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
        'session_type' => 'checkin',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'error_code' => 'TIME_TOO_EARLY',
    ]);

    Carbon::setTestNow();
});

test('rfid checkout at 06:41 WIB is rejected with CHECKOUT_TOO_EARLY', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 06:41:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII Merdeka 9',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $student = Student::create([
        'name' => 'Dewi Sartika',
        'nis' => '776655',
        'nisn' => '0077665544',
        'class_id' => $class->id,
        'rfid_uid' => 'CARD-DEWI-01',
        'is_active' => true,
    ]);

    // Student checked in at 06:02 WIB
    Attendance::create([
        'student_id' => $student->id,
        'date' => '2026-11-18',
        'time' => '06:02:00',
        'status' => 'HADIR',
        'verification_status' => 'VALID',
        'method' => 'RFID',
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->postJson(route('attendance.rfid.scan'), [
        'rfid_uid' => 'CARD-DEWI-01',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'error_code' => 'CHECKOUT_TOO_EARLY',
    ]);

    $att = Attendance::where('student_id', $student->id)->whereDate('date', '2026-11-18')->first();
    expect($att->time_out)->toBeNull();

    Carbon::setTestNow();
});

test('admin can bypass time restriction for early selfie checkout', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-18 06:41:00', 'Asia/Jakarta'));

    $class = SchoolClass::create([
        'name' => 'XII Merdeka 10',
        'level' => 'XII',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    $student = Student::create([
        'name' => 'Eko Prasetyo',
        'nis' => '665544',
        'nisn' => '0066554433',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    Attendance::create([
        'student_id' => $student->id,
        'date' => '2026-11-18',
        'time' => '06:03:00',
        'status' => 'HADIR',
        'verification_status' => 'VALID',
        'method' => 'SELFIE',
    ]);

    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($admin)->postJson(route('attendance.selfie.store'), [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
        'session_type' => 'checkout',
        'bypass_time' => true,
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'session' => 'checkout',
    ]);

    Carbon::setTestNow();
});
