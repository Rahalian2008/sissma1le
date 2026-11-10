<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    SchoolSetting::set('school_name', 'SMA Negeri 1 Lengkong');
    SchoolSetting::set('enable_attendance_rfid', '1');
    SchoolSetting::set('enable_attendance_qr', '1');
    SchoolSetting::set('enable_attendance_selfie', '1');
});

test('saveBase64Image generates valid binary image and handles corrupted inputs safely', function () {
    $service = app(AttendanceService::class);

    // 1. Valid PNG binary via GD
    $validDataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    $path1 = $service->saveBase64Image($validDataUri, 'selfies');

    expect($path1)->not->toBeEmpty();
    expect(Storage::disk('public')->exists($path1))->toBeTrue();
    $content1 = Storage::disk('public')->get($path1);
    expect(strlen($content1))->toBeGreaterThan(50);
    // Check PNG magic bytes: \x89PNG
    expect(str_starts_with($content1, "\x89PNG"))->toBeTrue();

    // 2. Corrupt / raw text input fallback to safe valid PNG
    $corruptInput = 'not-a-real-base64-random-string-that-could-corrupt-file-binary';
    $path2 = $service->saveBase64Image($corruptInput, 'selfies');

    expect($path2)->not->toBeEmpty();
    expect(Storage::disk('public')->exists($path2))->toBeTrue();
    $content2 = Storage::disk('public')->get($path2);
    // Even for corrupt input, it safely generated a valid PNG binary image!
    expect(str_starts_with($content2, "\x89PNG"))->toBeTrue();
});

test('automatic ALPA status is generated for active students and reports show accurate counts', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-MIPA-1',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Budi Santoso Alpa Test',
        'nis' => '990011',
        'nisn' => '0099001122',
        'gender' => 'L',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'rfid_uid' => 'BUDI_RFID_99',
        'is_active' => true,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-09-28 07:05:00', 'Asia/Jakarta'));
    $today = Carbon::today('Asia/Jakarta');
    $service = app(AttendanceService::class);

    // Run ensureDailyAttendanceRecords
    $service->ensureDailyAttendanceRecords($today);

    $record = Attendance::where('student_id', $student->id)
        ->whereDate('date', $today->toDateString())
        ->first();

    expect($record)->not->toBeNull();
    expect($record->status)->toBe('ALPA');
    expect($record->time)->toBeNull();
    expect($record->method)->toBe('SISTEM');

    // Admin can view report and sees ALPA record
    $admin = User::first() ?? User::create([
        'name' => 'Admin Test',
        'email' => 'admintest@sma1le.sch.id',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today->toDateString()]));
    $response->assertOk();
    $response->assertSee('Budi Santoso Alpa Test');
    $response->assertSee('ALPA');

    Carbon::setTestNow(); // Reset test time
});

test('status ALPA transforms to HADIR upon RFID tap', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-MIPA-2',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Siti Aminah Transition Test',
        'nis' => '990022',
        'nisn' => '0099002233',
        'gender' => 'P',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'rfid_uid' => 'SITI_RFID_88',
        'is_active' => true,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-09-28 07:05:00', 'Asia/Jakarta'));
    $today = Carbon::today('Asia/Jakarta');
    $service = app(AttendanceService::class);

    // Initial state: ALPA
    $service->ensureDailyAttendanceRecords($today);

    $before = Attendance::where('student_id', $student->id)->whereDate('date', $today->toDateString())->first();
    expect($before)->not->toBeNull();
    expect($before->status)->toBe('ALPA');

    // Perform RFID tap
    $result = $service->processRfidAttendance($student->rfid_uid, 'RFID-01');

    $after = Attendance::where('student_id', $student->id)->whereDate('date', $today->toDateString())->first();

    expect($result['success'])->toBeTrue();
    expect(in_array($result['status'], ['HADIR', 'TERLAMBAT']))->toBeTrue();
    expect(in_array($after->status, ['HADIR', 'TERLAMBAT']))->toBeTrue();
    expect($after->method)->toBe('RFID');
    expect($after->time)->not->toBeNull();

    Carbon::setTestNow(); // Reset test time
});

test('RFID, Barcode, and Face Recognition devices are present, configurable and retrievable via API/routes', function () {
    $admin = User::first() ?? User::create([
        'name' => 'Super Admin Test',
        'email' => 'superadmin@sma1le.sch.id',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'is_active' => true,
    ]);

    // Trigger default generation route
    $response = $this->actingAs($admin)->post(route('master.devices.generate_default'));
    $response->assertRedirect();

    $rfidDevices = AttendanceDevice::rfid()->get();
    $barcodeDevices = AttendanceDevice::barcode()->get();
    $faceDevices = AttendanceDevice::faceRecognition()->get();

    expect($rfidDevices->count())->toBeGreaterThanOrEqual(6);
    expect($barcodeDevices->count())->toBeGreaterThanOrEqual(6);
    expect($faceDevices->count())->toBeGreaterThanOrEqual(6);

    // Update a device
    $firstRfid = $rfidDevices->first();
    $updateResponse = $this->actingAs($admin)->put(route('master.devices.update', $firstRfid->id), [
        'device_code' => $firstRfid->device_code,
        'device_name' => 'Gate RFID 01 - Updated Pos Satpam Barat',
        'device_type' => 'RFID',
        'location' => 'Pos Satpam Barat Lengkong',
        'ip_address' => '192.168.1.199',
        'is_active' => '1',
    ]);

    $updateResponse->assertRedirect();
    $firstRfid->refresh();
    expect($firstRfid->device_name)->toBe('Gate RFID 01 - Updated Pos Satpam Barat');
    expect($firstRfid->location)->toBe('Pos Satpam Barat Lengkong');
    expect($firstRfid->ip_address)->toBe('192.168.1.199');
});

test('parent dashboard and public parent portal render smoothly with proper ALPA indicators', function () {
    $academicYear = AcademicYear::first() ?? AcademicYear::create([
        'name' => '2026/2027',
        'semester' => 'GANJIL',
        'is_active' => true,
        'start_date' => '2026-07-01',
        'end_date' => '2026-12-31',
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-MIPA-3',
        'level' => '10',
        'academic_year_id' => $academicYear->id,
    ]);

    $student = Student::create([
        'name' => 'Ananda Putri Ortu Test',
        'nis' => '990033',
        'nisn' => '0099003344',
        'gender' => 'P',
        'class_id' => $class->id,
        'academic_year_id' => $academicYear->id,
        'is_active' => true,
    ]);

    $parentUser = User::create([
        'name' => 'Bapak Ananda',
        'email' => 'bapakananda@ortu.sch.id',
        'password' => bcrypt('password'),
        'role' => 'orang_tua',
        'is_active' => true,
    ]);

    ParentGuardian::create([
        'user_id' => $parentUser->id,
        'student_id' => $student->id,
        'name' => 'Bapak Ananda',
        'relationship' => 'Ayah Kandung',
        'phone' => '081234567800',
    ]);

    // Parent dashboard renders without ParentGuardian class error
    $dashResponse = $this->actingAs($parentUser)->get(route('dashboard'));
    $dashResponse->assertOk();
    $dashResponse->assertSee('Pemantauan Perkembangan Anak');
    $dashResponse->assertSee('Ananda Putri Ortu Test');

    // Public Parent Portal search by NISN renders properly
    $portalResponse = $this->get(route('parent.portal', ['nisn' => $student->nisn]));
    $portalResponse->assertOk();
    $portalResponse->assertSee('Ananda Putri Ortu Test');
    $portalResponse->assertSee('0099003344');
    $portalResponse->assertSee('Tanpa Keterangan');
});
