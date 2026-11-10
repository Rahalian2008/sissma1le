<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    // Ensure Monday (Senin) at 06:15 for base time
    Carbon::setTestNow(Carbon::parse('2026-10-12 06:15:00', 'Asia/Jakarta'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('admin dashboard realtime attendance counts hadir as total on-time plus late students', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);

    // Create 4 students
    $s1 = Student::create(['name' => 'Siswa 1', 'nisn' => '1111111101', 'nis' => '101', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $s2 = Student::create(['name' => 'Siswa 2', 'nisn' => '1111111102', 'nis' => '102', 'gender' => 'L', 'class_id' => $class->id, 'is_active' => true]);
    $s3 = Student::create(['name' => 'Siswa 3', 'nisn' => '1111111103', 'nis' => '103', 'gender' => 'P', 'class_id' => $class->id, 'is_active' => true]);
    $s4 = Student::create(['name' => 'Siswa 4', 'nisn' => '1111111104', 'nis' => '104', 'gender' => 'P', 'class_id' => $class->id, 'is_active' => true]);

    $today = Carbon::today('Asia/Jakarta')->toDateString();

    // 1 on-time, 1 late, 1 izin, 1 alpa
    Attendance::create([
        'student_id' => $s1->id, 'student_name' => $s1->name, 'nis' => $s1->nis, 'nisn' => $s1->nisn, 'date' => $today, 'time' => '06:15:00', 'status' => 'HADIR', 'class_id' => $class->id, 'method' => 'SELFIE', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $s2->id, 'student_name' => $s2->name, 'nis' => $s2->nis, 'nisn' => $s2->nisn, 'date' => $today, 'time' => '07:45:00', 'status' => 'TERLAMBAT', 'class_id' => $class->id, 'method' => 'SELFIE', 'verification_status' => 'VALID',
    ]);
    Attendance::create([
        'student_id' => $s3->id, 'student_name' => $s3->name, 'nis' => $s3->nis, 'nisn' => $s3->nisn, 'date' => $today, 'time' => '06:30:00', 'status' => 'IZIN', 'class_id' => $class->id, 'method' => 'IZIN_SAKIT', 'verification_status' => 'DIVERIFIKASI',
    ]);
    Attendance::create([
        'student_id' => $s4->id, 'student_name' => $s4->name, 'nis' => $s4->nis, 'nisn' => $s4->nisn, 'date' => $today, 'time' => null, 'status' => 'ALPA', 'class_id' => $class->id, 'method' => 'SISTEM', 'verification_status' => 'DIVERIFIKASI',
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertStatus(200);
    // In view, attendanceStats['hadir'] is the sum of hadir_tepat_waktu + terlambat
    $response->assertViewHas('attendanceStats', function ($stats) {
        return $stats['hadir'] === ($stats['hadir_tepat_waktu'] + $stats['terlambat'])
            && $stats['hadir_tepat_waktu'] >= 1
            && $stats['terlambat'] >= 1;
    });
    $response->assertSee('tepat,');
    $response->assertSee('terlambat');
});

test('presensi utama cutoff is 11:00 WIB distinguishing on-time vs late and rejecting after 11:00', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Student::create([
        'name' => 'Cutoff Test Siswa',
        'nisn' => '9999000111',
        'nis' => '99001',
        'gender' => 'L',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);

    $service = app(AttendanceService::class);
    $dummyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    // 1. Checkin at 06:20 (before on-time cutoff 06:30/07:00) -> Status HADIR
    Carbon::setTestNow(Carbon::parse('2026-10-12 06:20:00', 'Asia/Jakarta'));
    $resultOnTime = $service->processSelfieAttendance($student, [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
    ]);
    expect($resultOnTime['success'])->toBeTrue();
    expect($resultOnTime['status'])->toBe('HADIR');

    // Reset attendance for test
    Attendance::where('student_id', $student->id)->delete();

    // 2. Checkin at 08:30 (after on-time, before 11:00) -> Status TERLAMBAT
    Carbon::setTestNow(Carbon::parse('2026-10-12 08:30:00', 'Asia/Jakarta'));
    $resultLate = $service->processSelfieAttendance($student, [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
    ]);
    expect($resultLate['success'])->toBeTrue();
    expect($resultLate['status'])->toBe('TERLAMBAT');

    // Reset attendance for test
    Attendance::where('student_id', $student->id)->delete();

    // 3. Checkin at 11:15 (after cutoff 11:00) -> TIME_TOO_LATE
    Carbon::setTestNow(Carbon::parse('2026-10-12 11:15:00', 'Asia/Jakarta'));
    $resultAfterCutoff = $service->processSelfieAttendance($student, [
        'selfie_image' => $dummyImage,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
    ]);
    expect($resultAfterCutoff['success'])->toBeFalse();
    expect($resultAfterCutoff['error_code'])->toBe('TIME_TOO_LATE');
});

test('admin can edit attendance results from presensi utama', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $student = Student::create([
        'name' => 'Editable Siswa',
        'nisn' => '9999000222',
        'nis' => '99002',
        'gender' => 'L',
        'class_id' => $class->id,
        'is_active' => true,
    ]);

    $today = Carbon::today('Asia/Jakarta')->toDateString();
    $att = Attendance::create([
        'student_id' => $student->id,
        'student_name' => $student->name,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'class_id' => $class->id,
        'date' => $today,
        'time' => '08:45:00',
        'status' => 'TERLAMBAT',
        'method' => 'SELFIE',
        'verification_status' => 'VALID',
        'notes' => 'Terlambat 105 menit',
    ]);

    // Admin updates via PUT /attendance/{attendance}
    $updateResponse = $this->actingAs($admin)->put(route('attendance.update', $att->id), [
        'status' => 'HADIR',
        'time' => '06:25',
        'time_out' => '15:15',
        'notes' => 'Diperbaiki oleh admin menjadi hadir tepat waktu dengan dispensasi dinas.',
        'verification_status' => 'DIVERIFIKASI',
    ]);

    $updateResponse->assertRedirect();
    $updateResponse->assertSessionHas('success');

    $att->refresh();
    expect($att->status)->toBe('HADIR');
    expect($att->time)->toContain('06:25');
    expect($att->time_out)->toContain('15:15');
    expect($att->notes)->toContain('dispensasi dinas');
    expect($att->verification_status)->toBe('DIVERIFIKASI');

    // Presensi Utama page has Edit button for Admin
    $indexResponse = $this->actingAs($admin)->get(route('attendance.index', ['tab' => 'history']));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Edit Presensi Utama');
    $indexResponse->assertSee('openEditModal');
});
