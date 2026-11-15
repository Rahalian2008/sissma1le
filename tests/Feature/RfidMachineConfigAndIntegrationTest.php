<?php

use App\Models\AttendanceDevice;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;

test('rfid hardware api returns status and documentation on GET request', function () {
    $response = $this->getJson('/api/attendance/rfid');

    $response->assertOk()
        ->assertJson([
            'status' => 'ready',
            'service' => 'SISSMA1LE RFID Attendance IoT Endpoint',
        ]);
});

test('rfid device ping endpoint updates last_ping_at and returns online status', function () {
    $device = AttendanceDevice::firstOrCreate(
        ['device_code' => 'RFID-TEST-01'],
        [
            'device_name' => 'Gate RFID Test 01',
            'device_type' => 'RFID',
            'location_name' => 'Gerbang Utama',
            'status' => 'OFFLINE',
        ]
    );

    $response = $this->getJson('/api/attendance/device-ping?device_code=RFID-TEST-01');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'device_code' => 'RFID-TEST-01',
            'status' => 'ONLINE',
        ]);

    $device->refresh();
    expect($device->status)->toBe('ONLINE')
        ->and($device->last_ping_at)->not->toBeNull();
});

test('rfid config page can be accessed by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('attendance.rfid.config'));

    $response->assertOk()
        ->assertSee('Konfigurasi & Integrasi Mesin RFID')
        ->assertSee('Daftar Mesin Presensi RFID Sekolah');
});

test('admin can toggle rfid attendance setting', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    SchoolSetting::set('enable_attendance_rfid', '1');

    $response = $this->actingAs($admin)->post(route('attendance.rfid.toggle'));

    $response->assertRedirect();
    expect(SchoolSetting::get('enable_attendance_rfid'))->toBe('0');

    // Toggle back
    $this->actingAs($admin)->post(route('attendance.rfid.toggle'));
    expect(SchoolSetting::get('enable_attendance_rfid'))->toBe('1');
});

test('admin can pair rfid card to student', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII MIPA 1']);
    $student = Student::first() ?? Student::create([
        'name' => 'Test Siswa RFID',
        'nis' => '12345678',
        'nisn' => '0099887766',
        'gender' => 'L',
        'class_id' => $class->id,
        'rfid_uid' => null,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('attendance.rfid.pair_card'), [
        'student_id' => $student->id,
        'rfid_uid' => 'A1B2C3D4',
    ]);

    $response->assertRedirect();
    $student->refresh();
    expect($student->rfid_uid)->toBe('A1B2C3D4');
});
