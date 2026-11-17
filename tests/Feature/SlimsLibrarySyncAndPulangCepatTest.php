<?php

use App\Models\Attendance;
use App\Models\LibraryBook;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

test('izin pulang cepat student remains counted as HADIR and displays properly', function () {
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII-MIPA-1', 'grade' => 'XII', 'major' => 'MIPA']);
    $student = Student::create([
        'nis' => '999901',
        'nisn' => '9999999901',
        'name' => 'Siswa Pulang Cepat Test',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $today = Carbon::today()->toDateString();

    // Buat presensi masuk pagi
    $attendance = Attendance::create([
        'student_id' => $student->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'class_id' => $student->class_id,
        'date' => $today,
        'time' => '06:25:00',
        'status' => 'HADIR',
        'method' => 'SELFIE',
        'verification_status' => 'VALID',
    ]);

    // Ajukan izin pulang cepat
    $attendance->update([
        'is_early_leave' => true,
        'early_leave_time' => '11:00:00',
        'early_leave_reason' => 'Sakit perut mendadak',
        'requested_status' => 'PULANG_CEPAT',
        'verification_status' => 'DIVERIFIKASI',
        'time_out' => '11:00:00',
        'out_method' => 'PULANG_CEPAT',
    ]);

    $admin = User::where('role', 'admin')->first();

    // Cek di halaman index presensi
    $responseIndex = $this->actingAs($admin)->get(route('attendance.index', ['class_id' => $class->id]));
    $responseIndex->assertStatus(200);
    // Pastikan siswa tetap dihitung sebagai Hadir
    $responseIndex->assertViewHas('totalHadir', fn ($count) => $count >= 1);
    $responseIndex->assertViewHas('totalPulangCepat', fn ($count) => $count >= 1);

    // Cek di halaman laporan presensi terpadu
    $responseReport = $this->actingAs($admin)->get(route('attendance.report', ['class_id' => $class->id, 'date' => $today]));
    $responseReport->assertStatus(200);
    $responseReport->assertViewHas('stats', function ($stats) {
        return $stats['hadir_total'] >= 1 && $stats['pulang_cepat'] >= 1 && $stats['alpa'] === 0;
    });
});

test('slims library sync command executes correctly', function () {
    $this->artisan('library:sync-slims', ['--force' => true])
        ->assertSuccessful();

    expect(LibraryBook::count())->toBeGreaterThan(0);
});
