<?php

use App\Models\AcademicYear;
use App\Models\AttendanceDevice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    $this->admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
        'is_active' => true,
    ]);

    $this->academicYear = AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'Ganjil',
        'is_active' => true,
        'start_date' => '2026-07-15',
        'end_date' => '2026-12-20',
    ]);

    $this->teacher = Teacher::create([
        'nip' => '198001012005011001',
        'name' => 'Guru Pengajar S.Pd',
        'gender' => 'L',
        'title' => 'Guru',
    ]);

    $this->schoolClass = SchoolClass::create([
        'name' => 'X-A',
        'grade' => 'X',
        'major' => 'Umum',
        'academic_year_id' => $this->academicYear->id,
        'homeroom_teacher_id' => $this->teacher->id,
    ]);

    $this->student = Student::create([
        'name' => 'Ahmad Siswa Lengkong',
        'nis' => '12345',
        'nisn' => '0012345678',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'rfid_uid' => 'UID-99999',
        'is_active' => true,
    ]);
});

test('generateDefaultDevices creates 6 RFID, 6 Barcode, and 6 Face Recognition devices', function () {
    $response = $this->actingAs($this->admin)->post(route('master.devices.generate_default'));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $standardRfid = AttendanceDevice::where('device_type', 'RFID')->whereIn('device_code', ['RFID-01', 'RFID-02', 'RFID-03', 'RFID-04', 'RFID-05', 'RFID-06'])->count();
    $standardBarcode = AttendanceDevice::where('device_type', 'BARCODE')->whereIn('device_code', ['BARCODE-01', 'BARCODE-02', 'BARCODE-03', 'BARCODE-04', 'BARCODE-05', 'BARCODE-06'])->count();
    $standardFace = AttendanceDevice::where('device_type', 'FACE_RECOGNITION')->whereIn('device_code', ['FACE-01', 'FACE-02', 'FACE-03', 'FACE-04', 'FACE-05', 'FACE-06'])->count();

    expect($standardRfid)->toBe(6)
        ->and($standardBarcode)->toBe(6)
        ->and($standardFace)->toBe(6);

    // Verify excess default units from old setup (RFID-07..10, QR-01..10) are removed
    expect(AttendanceDevice::where('device_code', 'RFID-07')->exists())->toBeFalse()
        ->and(AttendanceDevice::where('device_code', 'QR-01')->exists())->toBeFalse();

    // Verify presence of standard codes
    expect(AttendanceDevice::where('device_code', 'RFID-01')->exists())->toBeTrue()
        ->and(AttendanceDevice::where('device_code', 'RFID-06')->exists())->toBeTrue()
        ->and(AttendanceDevice::where('device_code', 'BARCODE-01')->exists())->toBeTrue()
        ->and(AttendanceDevice::where('device_code', 'BARCODE-06')->exists())->toBeTrue()
        ->and(AttendanceDevice::where('device_code', 'FACE-01')->exists())->toBeTrue()
        ->and(AttendanceDevice::where('device_code', 'FACE-06')->exists())->toBeTrue();
});

test('admin can store new devices of type RFID, BARCODE, and FACE_RECOGNITION', function () {
    // Add custom Face Recognition device
    $responseFace = $this->actingAs($this->admin)->post(route('master.devices.store'), [
        'device_code' => 'FACE-07',
        'device_name' => 'Terminal Wajah Tambahan Sayap Selatan',
        'device_type' => 'FACE_RECOGNITION',
        'location' => 'Lobby Selatan',
        'ip_address' => '192.168.1.135',
        'is_active' => '1',
    ]);
    $responseFace->assertRedirect();

    $device = AttendanceDevice::where('device_code', 'FACE-07')->first();
    expect($device)->not->toBeNull()
        ->and($device->device_type)->toBe('FACE_RECOGNITION')
        ->and($device->location_name)->toBe('Lobby Selatan');

    // Add custom Barcode Scanner device
    $responseBarcode = $this->actingAs($this->admin)->post(route('master.devices.store'), [
        'device_code' => 'BARCODE-07',
        'device_name' => 'Barcode Scanner Kiosk Cadangan',
        'device_type' => 'BARCODE',
        'location' => 'Kiosk Mandiri 02',
        'is_active' => '1',
    ]);
    $responseBarcode->assertRedirect();

    expect(AttendanceDevice::where('device_code', 'BARCODE-07')->exists())->toBeTrue();
});

test('admin can record student face via uploaded image file', function () {
    $file = UploadedFile::fake()->image('wajah_siswa.jpg', 400, 400);

    $response = $this->actingAs($this->admin)->post(
        route('master.students.record_face', $this->student->id),
        ['face_photo' => $file],
        ['Accept' => 'application/json']
    );

    $response->assertOk();
    $response->assertJson([
        'success' => true,
    ]);

    $this->student->refresh();
    expect($this->student->face_photo_path)->not->toBeNull()
        ->and($this->student->hasFaceRegistered())->toBeTrue()
        ->and($this->student->face_registered_at)->not->toBeNull()
        ->and($this->student->photo)->not->toBeNull();

    expect(Storage::disk('public')->exists($this->student->face_photo_path))->toBeTrue();
});

test('admin can record student face via base64 camera image', function () {
    // Generate valid 1x1 base64 png
    $base64Png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($this->admin)->post(
        route('master.students.record_face', $this->student->id),
        [
            'face_image' => $base64Png,
            'face_descriptor' => json_encode(['mock_embedding' => [0.12, 0.45, 0.78]]),
        ],
        ['Accept' => 'application/json']
    );

    $response->assertOk();
    $response->assertJson(['success' => true]);

    $this->student->refresh();
    expect($this->student->face_photo_path)->not->toBeNull()
        ->and($this->student->face_descriptor)->not->toBeNull()
        ->and($this->student->hasFaceRegistered())->toBeTrue();

    expect(Storage::disk('public')->exists($this->student->face_photo_path))->toBeTrue();
});

test('admin can delete student registered face', function () {
    // Set initial face
    $file = UploadedFile::fake()->image('wajah.jpg', 300, 300);
    $path = $file->store('faces', 'public');

    $this->student->update([
        'face_photo_path' => $path,
        'face_registered_at' => now(),
    ]);

    expect(Storage::disk('public')->exists($path))->toBeTrue();

    $response = $this->actingAs($this->admin)->delete(
        route('master.students.delete_face', $this->student->id),
        [],
        ['Accept' => 'application/json']
    );

    $response->assertOk();
    $response->assertJson(['success' => true]);

    $this->student->refresh();
    expect($this->student->face_photo_path)->toBeNull()
        ->and($this->student->face_registered_at)->toBeNull()
        ->and(Storage::disk('public')->exists($path))->toBeFalse();
});

test('settings view loads with RFID, Barcode, and Face Recognition devices', function () {
    $this->actingAs($this->admin)->post(route('master.devices.generate_default'));

    $response = $this->actingAs($this->admin)->get(route('master.settings'));
    $response->assertOk();
    $response->assertViewHas('rfidDevices');
    $response->assertViewHas('barcodeDevices');
    $response->assertViewHas('faceDevices');
    $response->assertSee('Face Recognition AI');
    $response->assertSee('FACE-01');
    $response->assertSee('BARCODE-01');
    $response->assertSee('RFID-01');
});

test('students view renders Face Recognition column and enrollment button', function () {
    $response = $this->actingAs($this->admin)->get(route('master.students'));
    $response->assertOk();
    $response->assertSee('Face Recognition');
    $response->assertSee('Belum Ada');
    $response->assertSee('Rekam Wajah');
});
