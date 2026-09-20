<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

afterEach(function () {
    Carbon::setTestNow();
});

test('siswa can submit leave request before 06:30 with live camera selfie and realtime gps outside school radius', function () {
    Carbon::setTestNow(Carbon::today()->setTime(6, 15, 0));
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student ?? Student::first();

    // Dummy base64 1x1 transparent png image
    $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($studentUser)
        ->postJson(route('attendance.leave.store'), [
            'status' => 'SAKIT',
            'date' => Carbon::today()->toDateString(),
            'selfie_image' => $fakeBase64,
            // GPS coordinates far outside school (e.g. at home or clinic)
            'latitude' => -7.62000,
            'longitude' => 112.10000,
            'accuracy' => 12.5,
            'notes' => 'Demam tinggi dan istirahat dokter.',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('attendances', [
        'student_id' => $student->id,
        'status' => 'SAKIT',
        'method' => 'IZIN_SAKIT',
        'verification_status' => 'PERLU_VERIFIKASI',
        'latitude' => -7.62000,
        'longitude' => 112.10000,
    ]);
});

test('siswa cannot submit same-day leave or sick request after 06:30 morning cutoff', function () {
    Carbon::setTestNow(Carbon::today()->setTime(6, 45, 0)); // 06:45 AM (after cutoff)
    $studentUser = User::where('role', 'siswa')->first();

    $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($studentUser)
        ->postJson(route('attendance.leave.store'), [
            'status' => 'IZIN',
            'date' => Carbon::today()->toDateString(),
            'selfie_image' => $fakeBase64,
            'latitude' => -7.56845,
            'longitude' => 112.04612,
            'notes' => 'Kesiangan bangun dan minta izin.',
        ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'error_code' => 'LEAVE_CUTOFF_EXCEEDED',
    ]);
});

test('siswa can submit leave or sick request in advance for future dates at any hour', function () {
    Carbon::setTestNow(Carbon::today()->setTime(21, 30, 0)); // 09:30 PM the night before
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student ?? Student::first();
    $tomorrow = Carbon::tomorrow()->toDateString();

    $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($studentUser)
        ->postJson(route('attendance.leave.store'), [
            'status' => 'IZIN',
            'date' => $tomorrow,
            'selfie_image' => $fakeBase64,
            'latitude' => -7.56845,
            'longitude' => 112.04612,
            'notes' => 'Izin menghadiri pernikahan saudara di luar kota besok.',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $attendance = Attendance::where('student_id', $student->id)
        ->whereDate('date', $tomorrow)
        ->where('status', 'IZIN')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->verification_status)->toBe('PERLU_VERIFIKASI');
});

test('siswa can submit early leave PULANG_CEPAT during school hours even after 06:30', function () {
    Carbon::setTestNow(Carbon::today()->setTime(10, 15, 0)); // 10:15 AM
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student ?? Student::first();

    $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($studentUser)
        ->postJson(route('attendance.leave.store'), [
            'status' => 'PULANG_CEPAT',
            'date' => Carbon::today()->toDateString(),
            'early_leave_time' => '10:30',
            'selfie_image' => $fakeBase64,
            'latitude' => -7.56845,
            'longitude' => 112.04612,
            'notes' => 'Tiba-tiba pusing dan mimisan di kelas.',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('attendances', [
        'student_id' => $student->id,
        'status' => 'PULANG_CEPAT',
        'is_early_leave' => true,
    ]);
});

test('leave submission fails if no live camera selfie base64 is provided', function () {
    $studentUser = User::where('role', 'siswa')->first();

    $response = $this->actingAs($studentUser)
        ->postJson(route('attendance.leave.store'), [
            'status' => 'IZIN',
            'selfie_image' => '',
            'latitude' => -7.56845,
            'longitude' => 112.04612,
            'notes' => 'Keperluan keluarga',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['selfie_image']);
});

test('wali kelas can view and approve leave request for student in their homeroom class', function () {
    $waliUser = User::where('role', 'wali_kelas')->first();
    $teacher = $waliUser->teacher;
    $homeroomClass = $teacher?->homeroomClass ?? SchoolClass::where('homeroom_teacher_id', $teacher?->id)->first();

    if (! $homeroomClass) {
        $homeroomClass = SchoolClass::first();
        $homeroomClass->update(['homeroom_teacher_id' => $teacher?->id]);
    }

    $student = Student::updateOrCreate(
        ['nis' => '10002'],
        [
            'nisn' => '0011223355',
            'name' => 'Siti Aisyah',
            'class_id' => $homeroomClass->id,
            'gender' => 'P',
            'is_active' => true,
        ]
    );

    $attendance = Attendance::create([
        'student_id' => $student->id,
        'class_id' => $homeroomClass->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:15:00',
        'method' => 'IZIN_SAKIT',
        'status' => 'IZIN',
        'verification_status' => 'PERLU_VERIFIKASI',
        'notes' => 'Acara keluarga mendesak',
    ]);

    $response = $this->actingAs($waliUser)
        ->post(route('attendance.leaves.approve', $attendance->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $attendance->refresh();
    expect($attendance->verification_status)->toBe('DIVERIFIKASI');
    expect($attendance->approved_by)->toBe($waliUser->id);
    expect($attendance->approved_at)->not->toBeNull();
});

test('wali kelas cannot approve leave request for student in another class', function () {
    $waliUser = User::where('role', 'wali_kelas')->first();
    $teacher = $waliUser->teacher;
    $homeroomClass = $teacher?->homeroomClass ?? SchoolClass::where('homeroom_teacher_id', $teacher?->id)->first();

    // Find or create a different class
    $otherClass = SchoolClass::where('id', '!=', $homeroomClass?->id)->first();
    if (! $otherClass) {
        $otherClass = SchoolClass::create(['name' => 'X-LAIN', 'grade' => 'X', 'major' => 'Umum']);
    }

    $studentOtherClass = Student::create([
        'nis' => '10003',
        'nisn' => '0011223366',
        'name' => 'Rian Pratama',
        'class_id' => $otherClass->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $attendance = Attendance::create([
        'student_id' => $studentOtherClass->id,
        'class_id' => $otherClass->id,
        'nis' => $studentOtherClass->nis,
        'nisn' => $studentOtherClass->nisn,
        'student_name' => $studentOtherClass->name,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:20:00',
        'method' => 'IZIN_SAKIT',
        'status' => 'IZIN',
        'verification_status' => 'PERLU_VERIFIKASI',
        'notes' => 'Izin kegiatan luar',
    ]);

    $response = $this->actingAs($waliUser)
        ->post(route('attendance.leaves.approve', $attendance->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $attendance->refresh();
    expect($attendance->verification_status)->toBe('PERLU_VERIFIKASI');
});

test('admin can reject leave request with mandatory reason', function () {
    $admin = User::where('role', 'admin')->first();
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XI-1', 'grade' => 'XI', 'major' => 'Umum']);

    $student = Student::create([
        'nis' => '10004',
        'nisn' => '0011223377',
        'name' => 'Dewi Lestari',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    $attendance = Attendance::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'nis' => $student->nis,
        'nisn' => $student->nisn,
        'student_name' => $student->name,
        'date' => Carbon::today()->toDateString(),
        'time' => '07:30:00',
        'method' => 'IZIN_SAKIT',
        'status' => 'SAKIT',
        'verification_status' => 'PERLU_VERIFIKASI',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('attendance.leaves.reject', $attendance->id), [
            'reason' => 'Foto selfie tidak jelas dan tidak menyertakan surat dokter.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $attendance->refresh();
    expect($attendance->verification_status)->toBe('DITOLAK');
    expect($attendance->rejection_reason)->toBe('Foto selfie tidak jelas dan tidak menyertakan surat dokter.');
    expect($attendance->approved_by)->toBe($admin->id);
});

test('wali kelas can record manual attendance for student in their homeroom class', function () {
    $waliUser = User::where('role', 'wali_kelas')->first();
    $teacher = $waliUser->teacher;
    $homeroomClass = $teacher?->homeroomClass ?? SchoolClass::where('homeroom_teacher_id', $teacher?->id)->first();

    $student = Student::create([
        'nis' => '10005',
        'nisn' => '0011223388',
        'name' => 'Fajar Nugraha',
        'class_id' => $homeroomClass->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $response = $this->actingAs($waliUser)
        ->post(route('attendance.manual.store'), [
            'student_id' => $student->id,
            'date' => Carbon::today()->toDateString(),
            'time' => '07:05',
            'status' => 'HADIR',
            'notes' => 'Presensi manual karena lupa kartu RFID',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('attendances', [
        'student_id' => $student->id,
        'method' => 'MANUAL',
        'status' => 'HADIR',
        'verification_status' => 'DIVERIFIKASI',
        'approved_by' => $waliUser->id,
    ]);
});

test('admin can record manual attendance for any student in any class', function () {
    $admin = User::where('role', 'admin')->first();
    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'X-IPS-1', 'grade' => 'X', 'major' => 'Umum']);

    $student = Student::create([
        'nis' => '10006',
        'nisn' => '0011223399',
        'name' => 'Galih Firmansyah',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('attendance.manual.store'), [
            'student_id' => $student->id,
            'date' => Carbon::today()->toDateString(),
            'time' => '07:10',
            'status' => 'ALPA',
            'notes' => 'Tanpa keterangan saat jam pelajaran pertama',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('attendances', [
        'student_id' => $student->id,
        'method' => 'MANUAL',
        'status' => 'ALPA',
        'verification_status' => 'DIVERIFIKASI',
        'approved_by' => $admin->id,
    ]);
});

test('printable reports for attendance, discipline, and habits return status 200 with official letterhead', function () {
    $admin = User::where('role', 'admin')->first();
    $class = SchoolClass::first();

    $startDate = Carbon::now()->startOfMonth()->toDateString();
    $endDate = Carbon::now()->toDateString();

    // 1. Print Attendance
    $responseAtt = $this->actingAs($admin)->get(route('reports.print.attendance', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $responseAtt->assertStatus(200);
    $responseAtt->assertSee('LAPORAN REKAPITULASI PRESENSI SISWA');
    $responseAtt->assertSee('PEMERINTAH PROVINSI JAWA BARAT');

    // 2. Print Discipline
    $responseDisc = $this->actingAs($admin)->get(route('reports.print.discipline', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $responseDisc->assertStatus(200);
    $responseDisc->assertSee('LAPORAN TATA TERTIB SISWA');
    $responseDisc->assertSee('PEMERINTAH PROVINSI JAWA BARAT');

    // 3. Print Habits (7 Kebiasaan)
    $responseHabits = $this->actingAs($admin)->get(route('reports.print.habits', [
        'class_id' => $class?->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));
    $responseHabits->assertStatus(200);
    $responseHabits->assertSee('7 KEBIASAAN ANAK INDONESIA HEBAT');
    $responseHabits->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
});
