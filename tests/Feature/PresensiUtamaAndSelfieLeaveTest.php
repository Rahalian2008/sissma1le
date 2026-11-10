<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Carbon::setTestNow(Carbon::today()->setTime(6, 15, 0));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('sidebar navigation displays Presensi Utama directly and does not display redundant submenus', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Presensi Utama');
    // Ensure the 3 submenus are no longer present in sidebar
    $response->assertDontSee('Presensi Terpadu (Utama)');
    $response->assertDontSee('>Persetujuan Izin & Pulang<', false);
    $response->assertDontSee('>Laporan Presensi<', false);
});

test('presensi utama page displays 2 operational sessions taking time from school settings', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('Presensi Utama Siswa');
    $response->assertSee('Sesi Datang:');
    $response->assertSee('Sesi Pulang:');
});

test('leave submission strictly requires live camera selfie and rejects without selfie_image', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::where('role', 'siswa')->first() ?? User::factory()->create(['role' => 'siswa']);
    $student = Student::first() ?? Student::create([
        'name' => 'Budi Santoso',
        'nisn' => '9988776655',
        'nis' => '12345',
        'gender' => 'L',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);
    $student->update(['user_id' => $studentUser->id]);

    // Attempt submitting without selfie_image -> Must fail validation
    $response = $this->actingAs($studentUser)->post(route('attendance.leave.store'), [
        'status' => 'IZIN',
        'notes' => 'Izin keperluan keluarga mendesak',
        'date' => Carbon::today()->toDateString(),
    ]);

    $response->assertSessionHasErrors(['selfie_image']);
});

test('leave submission succeeds with mandatory selfie and optional attachment is stored when provided', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::where('role', 'siswa')->first() ?? User::factory()->create(['role' => 'siswa']);
    $student = Student::first() ?? Student::create([
        'name' => 'Siti Rahma',
        'nisn' => '9988776656',
        'nis' => '12346',
        'gender' => 'P',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);
    $student->update(['user_id' => $studentUser->id]);

    $dummyBase64Image = 'data:image/jpeg;base64,'.base64_encode('fake_camera_image_bytes_1234567890123456789012345678901234567890123456789012345678901234567890');
    $dummyFile = UploadedFile::fake()->create('surat_dokter.pdf', 100, 'application/pdf');

    // Submit with selfie and optional attachment
    $response = $this->actingAs($studentUser)->post(route('attendance.leave.store'), [
        'status' => 'SAKIT',
        'selfie_image' => $dummyBase64Image,
        'notes' => 'Demam dan flu berat, istirahat dokter',
        'attachment' => $dummyFile,
        'latitude' => -7.56845,
        'longitude' => 112.04612,
    ]);

    $response->assertSessionHasNoErrors();

    $attendance = Attendance::where('student_id', $student->id)
        ->where('requested_status', 'SAKIT')
        ->latest('id')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->selfie_path)->not->toBeNull()
        ->and($attendance->attachment_path)->not->toBeNull();

    Storage::disk('public')->assertExists($attendance->attachment_path);
});

test('leave submission succeeds without optional attachment as long as selfie is provided', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-1', 'level' => '10']);
    $studentUser = User::where('role', 'siswa')->first() ?? User::factory()->create(['role' => 'siswa']);
    $student = Student::first() ?? Student::create([
        'name' => 'Andi Pratama',
        'nisn' => '9988776657',
        'nis' => '12347',
        'gender' => 'L',
        'class_id' => $class->id,
        'user_id' => $studentUser->id,
        'is_active' => true,
    ]);
    $student->update(['user_id' => $studentUser->id]);

    $dummyBase64Image = 'data:image/jpeg;base64,'.base64_encode('fake_camera_image_bytes_1234567890123456789012345678901234567890123456789012345678901234567890');

    // Submit with selfie only (attachment is null)
    $response = $this->actingAs($studentUser)->post(route('attendance.leave.store'), [
        'status' => 'IZIN',
        'selfie_image' => $dummyBase64Image,
        'notes' => 'Acara keluarga di luar kota',
        'date' => Carbon::today()->toDateString(),
        'latitude' => -7.56845,
        'longitude' => 112.04612,
    ]);

    $response->assertSessionHasNoErrors();

    $attendance = Attendance::where('student_id', $student->id)
        ->where('requested_status', 'IZIN')
        ->latest('id')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->selfie_path)->not->toBeNull();
});
