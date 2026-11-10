<?php

use App\Models\Attendance;
use App\Models\EarlyLeaveRequest;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->school = SchoolIdentity::current();

    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'X-A'],
        ['grade' => 'X']
    );

    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->guruPiket = User::factory()->create([
        'role' => 'guru',
    ]);

    $this->studentUser1 = User::factory()->create([
        'role' => 'siswa',
    ]);

    $this->student1 = Student::create([
        'user_id' => $this->studentUser1->id,
        'name' => 'Budi Santoso',
        'nis' => '10203001',
        'nisn' => '0081234567',
        'class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $this->studentUser2 = User::factory()->create([
        'role' => 'siswa',
    ]);

    $this->student2 = Student::create([
        'user_id' => $this->studentUser2->id,
        'name' => 'Siti Aisyah',
        'nis' => '10203002',
        'nisn' => '0081234568',
        'class_id' => $this->schoolClass->id,
        'gender' => 'P',
        'is_active' => true,
    ]);
});

test('early leave dashboard renders successfully for staff and student', function () {
    $staffRes = $this->actingAs($this->guruPiket)->get(route('attendance.early-leave.index'));
    $staffRes->assertStatus(200);
    $staffRes->assertSee('Ajuan Pulang Cepat Siswa');
    $staffRes->assertSee('+ Buat Ajuan Pulang Cepat');

    $studentRes = $this->actingAs($this->studentUser1)->get(route('attendance.early-leave.index'));
    $studentRes->assertStatus(200);
    $studentRes->assertSee('Ajuan Pulang Cepat Siswa');
});

test('staff can create early leave request with direct approval and sync with attendance', function () {
    $response = $this->actingAs($this->guruPiket)->post(route('attendance.early-leave.store'), [
        'student_id' => $this->student1->id,
        'date' => now()->toDateString(),
        'leave_time' => '11:30',
        'reason_category' => 'Sakit di Sekolah (Rujukan UKS)',
        'reason' => 'Demam tinggi dan pusing saat KBM berlangsung.',
        'pickup_name' => 'Agus Santoso',
        'pickup_relationship' => 'Orang Tua (Ayah)',
        'pickup_phone' => '081234567890',
        'direct_approve' => '1',
    ]);

    $response->assertSessionHas('success');

    $earlyLeave = EarlyLeaveRequest::where('student_id', $this->student1->id)->latest()->first();
    expect($earlyLeave)->not->toBeNull();
    expect($earlyLeave->status)->toBe('APPROVED');
    expect($earlyLeave->letter_number)->toContain('421.3/SKPC/');
    expect($earlyLeave->leave_time)->toBe('11:30');
    expect($earlyLeave->pickup_name)->toBe('Agus Santoso');

    // Verify daily attendance is updated
    $attendance = Attendance::where('student_id', $this->student1->id)
        ->whereDate('date', now()->toDateString())
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->status)->toBe('PULANG_CEPAT');
    expect((bool) $attendance->is_early_leave)->toBeTrue();
    expect($attendance->early_leave_time)->toBe('11:30');
});

test('student can submit early leave request and staff can approve it', function () {
    $studentSubmitRes = $this->actingAs($this->studentUser1)->post(route('attendance.early-leave.store'), [
        'date' => now()->toDateString(),
        'leave_time' => '12:00',
        'reason_category' => 'Urusan Keluarga Mendesak',
        'reason' => 'Ada keperluan keluarga mendesak ke luar kota.',
        'pickup_name' => 'Ibu Rahayu',
        'pickup_relationship' => 'Orang Tua (Ibu)',
        'pickup_phone' => '081987654321',
    ]);

    $studentSubmitRes->assertSessionHas('success');

    $pendingLeave = EarlyLeaveRequest::where('student_id', $this->student1->id)->latest()->first();
    expect($pendingLeave)->not->toBeNull();
    expect($pendingLeave->status)->toBe('PENDING');

    // Staff approves
    $approveRes = $this->actingAs($this->guruPiket)->post(route('attendance.early-leave.approve', $pendingLeave->id), [
        'notes' => 'Diizinkan pulang bersama orang tua.',
    ]);

    $approveRes->assertSessionHas('success');
    $pendingLeave->refresh();
    expect($pendingLeave->status)->toBe('APPROVED');
    expect($pendingLeave->approved_by)->toBe($this->guruPiket->id);

    // Verify print letter renders status 200 with school Kop and required signatures (No nomor surat per user request)
    $printRes = $this->actingAs($this->guruPiket)->get(route('attendance.early-leave.print', $pendingLeave->id));
    $printRes->assertStatus(200);
    $printRes->assertSee('SURAT IZIN PULANG CEPAT');
    $printRes->assertDontSee('Nomor:');
    $printRes->assertSee(strtoupper($this->student1->name));
    $printRes->assertSee('Wali Kelas');
    $printRes->assertSee('Petugas Piket');
    $printRes->assertSee('Orang Tua / Wali');
    $printRes->assertSee('CATATAN PENTING ARSIP KETERTIBAN SEKOLAH');
    $printRes->assertSee('wajib ditandatangani oleh Orang Tua/Wali Siswa');
    $printRes->assertSee($pendingLeave->pickup_name === 'Agus Santoso' ? 'Agus Santoso' : 'Ibu Rahayu');
});

test('school exit permit dashboard renders and supports multi-student requests', function () {
    $dashRes = $this->actingAs($this->guruPiket)->get(route('attendance.school-pass.index'));
    $dashRes->assertStatus(200);
    $dashRes->assertSee('Surat Izin Keluar Lingkungan Sekolah (Sementara)');
    $dashRes->assertSee('+ Buat Izin Keluar Sekolah');

    // Create multi-student permit
    $response = $this->actingAs($this->guruPiket)->post(route('attendance.school-pass.store'), [
        'student_ids' => [$this->student1->id, $this->student2->id],
        'date' => now()->toDateString(),
        'out_time' => '09:00',
        'expected_back_time' => '11:00',
        'purpose' => 'Membeli Perlengkapan KBM / Praktikum',
        'destination' => 'Toko ATK Lengkong',
        'reason' => 'Membeli bahan praktikum kimia kelompok.',
        'accompanied_by' => 'Mandiri (Dispensasi KBM)',
        'is_returning' => '1',
        'direct_approve' => '1',
    ]);

    $response->assertSessionHas('success');

    $permit = SchoolExitPermit::with('students')->latest()->first();
    expect($permit)->not->toBeNull();
    expect($permit->status)->toBe('APPROVED');
    expect($permit->permit_number)->toContain('421.3/SIK-LS/');
    expect($permit->students)->toHaveCount(2);
    expect($permit->students->pluck('id')->toArray())->toContain($this->student1->id, $this->student2->id);

    // Print letter renders multi-student table, official Kop, and 3-signatories (No nomor surat per user request)
    $printRes = $this->actingAs($this->guruPiket)->get(route('attendance.school-pass.print', $permit->id));
    $printRes->assertStatus(200);
    $printRes->assertSee('SURAT IZIN KELUAR LINGKUNGAN SEKOLAH (SEMENTARA)');
    $printRes->assertDontSee('Nomor:');
    $printRes->assertSee('Wali Kelas');
    $printRes->assertSee('Petugas Piket');
    $printRes->assertSee('Pemohon (Siswa)');
    $printRes->assertSee(strtoupper($this->student1->name));
    $printRes->assertSee(strtoupper($this->student2->name));
    $printRes->assertSee('Toko ATK Lengkong');

    // Mark students returned
    $returnRes = $this->actingAs($this->guruPiket)->post(route('attendance.school-pass.returned', $permit->id), [
        'actual_back_time' => '10:45',
        'notes' => 'Kedua siswa kembali tepat waktu dengan selamat.',
    ]);

    $returnRes->assertSessionHas('success');
    $permit->refresh();
    expect($permit->status)->toBe('RETURNED');
    expect($permit->actual_back_time)->toBe('10:45');
});

test('presensi utama header has early leave and school pass action buttons and modals without duplicate sidebar or tab items', function () {
    $res = $this->actingAs($this->guruPiket)->get(route('attendance.index'));
    $res->assertStatus(200);

    // Header single unified action button & modal exist on Presensi Utama
    $res->assertSee('openSchoolPassModal()', false);
    $res->assertSee('quick-school-pass-modal');

    // Sidebar should NOT have duplicate submenu links for early leave or school pass
    $res->assertDontSee('data-tooltip="Ajuan Pulang Cepat"', false);
    $res->assertDontSee('data-tooltip="Izin Keluar Sekolah"', false);

    // Tab nav in attendance should NOT have duplicate tabs
    $content = $res->getContent();
    expect($content)->not->toContain('<!-- 2. Ajuan Pulang Cepat (Surat Keterangan Resmi) -->');
    expect($content)->not->toContain('<!-- 3. Izin Keluar Lingkungan Sekolah (Surat Izin Resmi) -->');
});

test('submitting early leave and school pass stores print url in session for direct printing', function () {
    // Early leave store
    $elRes = $this->actingAs($this->guruPiket)->post(route('attendance.early-leave.store'), [
        'student_id' => $this->student1->id,
        'date' => now()->toDateString(),
        'leave_time' => '10:15',
        'reason_category' => 'Sakit di Sekolah (Rujukan UKS)',
        'reason' => 'Demam dan sakit tenggorokan',
        'pickup_name' => 'Ayah Siswa',
        'direct_approve' => '1',
    ]);

    $elRes->assertSessionHas('print_url');
    $elRes->assertSessionHas('print_title');

    // School pass store
    $spRes = $this->actingAs($this->guruPiket)->post(route('attendance.school-pass.store'), [
        'student_ids' => [$this->student1->id],
        'date' => now()->toDateString(),
        'out_time' => '08:30',
        'expected_back_time' => '10:00',
        'purpose' => 'Tugas Dinas / OSIS / MPK / Ekstrakurikuler',
        'destination' => 'Kantor Desa Lengkong',
        'reason' => 'Pengantaran surat undangan OSIS',
        'direct_approve' => '1',
    ]);

    $spRes->assertSessionHas('print_url');
    $spRes->assertSessionHas('print_title');
});
