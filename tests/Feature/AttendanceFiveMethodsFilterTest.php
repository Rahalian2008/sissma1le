<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Carbon::setTestNow(Carbon::parse('2026-10-13 08:00:00', 'Asia/Jakarta'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('attendance report displays all five updated methods in filter and filters correctly', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII Merdeka 7', 'level' => '12']);

    $today = Carbon::today('Asia/Jakarta')->toDateString();

    // Create 5 students with 5 different methods
    $sSelfie = Student::create(['name' => 'Siswa Selfie', 'nis' => '801', 'nisn' => '888801', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $sBarcode = Student::create(['name' => 'Siswa Barcode', 'nis' => '802', 'nisn' => '888802', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $sRfid = Student::create(['name' => 'Siswa RFID', 'nis' => '803', 'nisn' => '888803', 'gender' => 'P', 'class_id' => $class->id, 'is_active' => true]);
    $sFace = Student::create(['name' => 'Siswa Face', 'nis' => '804', 'nisn' => '888804', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $sManual = Student::create(['name' => 'Siswa Manual', 'nis' => '805', 'nisn' => '888805', 'gender' => 'P', 'class_id' => $class->id, 'is_active' => true]);

    Attendance::create([
        'student_id' => $sSelfie->id, 'student_name' => $sSelfie->name, 'nis' => $sSelfie->nis, 'nisn' => $sSelfie->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:15:00', 'method' => 'SELFIE', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $sBarcode->id, 'student_name' => $sBarcode->name, 'nis' => $sBarcode->nis, 'nisn' => $sBarcode->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:20:00', 'method' => 'BARCODE', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $sRfid->id, 'student_name' => $sRfid->name, 'nis' => $sRfid->nis, 'nisn' => $sRfid->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:25:00', 'method' => 'RFID', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $sFace->id, 'student_name' => $sFace->name, 'nis' => $sFace->nis, 'nisn' => $sFace->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:28:00', 'method' => 'FACE_RECOGNITION', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $sManual->id, 'student_name' => $sManual->name, 'nis' => $sManual->nis, 'nisn' => $sManual->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:30:00', 'method' => 'MANUAL', 'status' => 'HADIR', 'verification_status' => 'DIVERIFIKASI',
    ]);

    // 1. Visit report page without filter -> must see all 5 method options
    $resAll = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id]));
    $resAll->assertStatus(200);
    $resAll->assertSee('Selfie Kamera');
    $resAll->assertSee('Barcode Scanner');
    $resAll->assertSee('Kartu RFID');
    $resAll->assertSee('Face Recognition');
    $resAll->assertSee('Presensi Manual');

    // 2. Filter by Selfie
    $resSelfie = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id, 'method' => 'SELFIE']));
    $resSelfie->assertStatus(200);
    $resSelfie->assertSee('Siswa Selfie');
    $resSelfie->assertDontSee('Siswa Barcode');
    $resSelfie->assertDontSee('Siswa RFID');
    $resSelfie->assertDontSee('Siswa Face');
    $resSelfie->assertDontSee('Siswa Manual');

    // 3. Filter by Barcode Scanner
    $resBarcode = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id, 'method' => 'BARCODE']));
    $resBarcode->assertStatus(200);
    $resBarcode->assertSee('Siswa Barcode');
    $resBarcode->assertDontSee('Siswa Selfie');
    $resBarcode->assertDontSee('Siswa RFID');
    $resBarcode->assertDontSee('Siswa Face');
    $resBarcode->assertDontSee('Siswa Manual');

    // 4. Filter by Kartu RFID
    $resRfid = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id, 'method' => 'RFID']));
    $resRfid->assertStatus(200);
    $resRfid->assertSee('Siswa RFID');
    $resRfid->assertDontSee('Siswa Selfie');
    $resRfid->assertDontSee('Siswa Barcode');
    $resRfid->assertDontSee('Siswa Face');
    $resRfid->assertDontSee('Siswa Manual');

    // 5. Filter by Face Recognition
    $resFace = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id, 'method' => 'FACE_RECOGNITION']));
    $resFace->assertStatus(200);
    $resFace->assertSee('Siswa Face');
    $resFace->assertDontSee('Siswa Selfie');
    $resFace->assertDontSee('Siswa Barcode');
    $resFace->assertDontSee('Siswa RFID');
    $resFace->assertDontSee('Siswa Manual');

    // 6. Filter by Presensi Manual
    $resManual = $this->actingAs($admin)->get(route('attendance.report', ['date' => $today, 'class_id' => $class->id, 'method' => 'MANUAL']));
    $resManual->assertStatus(200);
    $resManual->assertSee('Siswa Manual');
    $resManual->assertDontSee('Siswa Selfie');
    $resManual->assertDontSee('Siswa Barcode');
    $resManual->assertDontSee('Siswa RFID');
    $resManual->assertDontSee('Siswa Face');
});

test('attendance history filters by five methods properly', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII Merdeka 7', 'level' => '12']);

    $today = Carbon::today('Asia/Jakarta')->toDateString();

    $sSelfie = Student::create(['name' => 'Hist Siswa Selfie', 'nis' => '901', 'nisn' => '999901', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $sFace = Student::create(['name' => 'Hist Siswa Face', 'nis' => '904', 'nisn' => '999904', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);

    Attendance::create([
        'student_id' => $sSelfie->id, 'student_name' => $sSelfie->name, 'nis' => $sSelfie->nis, 'nisn' => $sSelfie->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:15:00', 'method' => 'SELFIE', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $sFace->id, 'student_name' => $sFace->name, 'nis' => $sFace->nis, 'nisn' => $sFace->nisn,
        'class_id' => $class->id, 'date' => $today, 'time' => '06:28:00', 'method' => 'FACE_RECOGNITION', 'status' => 'HADIR', 'verification_status' => 'VALID',
    ]);

    $res = $this->actingAs($admin)->get(route('attendance.history', ['method' => 'FACE_RECOGNITION']));
    $res->assertStatus(200);
    $res->assertSee('Hist Siswa Face');
    $res->assertDontSee('Hist Siswa Selfie');
});
