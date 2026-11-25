<?php

use App\Models\BkParentCall;
use App\Models\EarlyLeaveRequest;
use App\Models\SchoolExitPermit;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;

test('bk parent call index displays edit and delete buttons and allows deletion', function () {
    $rand = Str::lower(Str::random(6));
    $admin = User::create([
        'name' => 'Admin BK Test',
        'email' => "admin_bk_{$rand}@sman1lengkong.sch.id",
        'username' => "admin_{$rand}",
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $student = Student::create([
        'nis' => '98'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Test Parent Call',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $parentCall = BkParentCall::create([
        'call_code' => 'PGL-'.rand(1000, 9999),
        'student_id' => $student->id,
        'counselor_id' => $admin->id,
        'letter_number' => '421.3/001-BK/SMA1LE/XI/2026',
        'call_date' => now()->toDateString(),
        'call_time' => '08:30 WIB',
        'place' => 'Ruang BK',
        'reason' => 'Musyawarah pembinaan kedisiplinan',
        'status' => 'SURAT_DIKIRIM',
    ]);

    $response = $this->actingAs($admin)->get(route('bk.parent-call.index'));
    $response->assertStatus(200);
    $response->assertSee('Edit');
    $response->assertSee('Hapus');
    $response->assertSee(route('bk.parent-call.edit', $parentCall));

    // Test Delete Action
    $deleteRes = $this->actingAs($admin)->delete(route('bk.parent-call.destroy', $parentCall));
    $deleteRes->assertRedirect(route('bk.parent-call.index'));
    expect(BkParentCall::find($parentCall->id))->toBeNull();
});

test('perizinan displays letter number only when approved, otherwise shows dash', function () {
    $rand = Str::lower(Str::random(6));
    $admin = User::create([
        'name' => 'Admin Perizinan Test',
        'email' => "admin_p_{$rand}@sman1lengkong.sch.id",
        'username' => "admin_p_{$rand}",
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $student = Student::create([
        'nis' => '97'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Perizinan Test',
        'gender' => 'P',
        'is_active' => true,
    ]);

    // 1. Pending Early Leave
    $pendingEarlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/2026/XI/9991',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '10:00',
        'reason_category' => 'Sakit di Sekolah',
        'reason' => 'Demam tinggi',
        'status' => 'PENDING',
        'created_by' => $admin->id,
    ]);

    // 2. Approved Early Leave
    $approvedEarlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/2026/XI/9992',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '11:00',
        'reason_category' => 'Urusan Keluarga',
        'reason' => 'Acara keluarga',
        'status' => 'APPROVED',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get(route('attendance.index'));
    $response->assertStatus(200);

    // Approved letter number should be visible
    $response->assertSee('421.3/SKPC/2026/XI/9992');

    // Pending letter number should NOT be shown in the table cell
    $response->assertDontSee('>421.3/SKPC/2026/XI/9991<', false);
});

test('perizinan has delete button on attendance index and can be deleted', function () {
    $rand = Str::lower(Str::random(6));
    $admin = User::create([
        'name' => 'Admin Del Test',
        'email' => "admin_del_{$rand}@sman1lengkong.sch.id",
        'username' => "admin_del_{$rand}",
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $student = Student::create([
        'nis' => '96'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Del Test',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $earlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/2026/XI/9993',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '10:30',
        'reason_category' => 'Sakit',
        'reason' => 'Sakit perut',
        'status' => 'APPROVED',
        'created_by' => $admin->id,
    ]);

    $exitPermit = SchoolExitPermit::create([
        'permit_number' => '421.3/SKIK/2026/XI/9994',
        'date' => now()->toDateString(),
        'out_time' => '09:00',
        'expected_back_time' => '11:00',
        'purpose' => 'Lomba OSN',
        'reason' => 'Perwakilan sekolah',
        'status' => 'APPROVED',
        'created_by' => $admin->id,
    ]);
    $exitPermit->students()->attach($student->id);

    $response = $this->actingAs($admin)->get(route('attendance.index'));
    $response->assertStatus(200);
    $response->assertSee(route('attendance.early-leave.destroy', $earlyLeave->id));
    $response->assertSee(route('attendance.school-pass.destroy', $exitPermit->id));

    // Delete Early Leave
    $delElRes = $this->actingAs($admin)->delete(route('attendance.early-leave.destroy', $earlyLeave->id));
    $delElRes->assertSessionHas('success');
    expect(EarlyLeaveRequest::find($earlyLeave->id))->toBeNull();

    // Delete School Exit Permit
    $delSpRes = $this->actingAs($admin)->delete(route('attendance.school-pass.destroy', $exitPermit->id));
    $delSpRes->assertSessionHas('success');
    expect(SchoolExitPermit::find($exitPermit->id))->toBeNull();
});

test('kios satpam displays approved early leaves and school passes with confirmation action', function () {
    $rand = Str::lower(Str::random(6));
    $student = Student::create([
        'nis' => '95'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Kios Satpam Test',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $earlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/2026/XI/8881',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '11:15',
        'reason_category' => 'Sakit Kepala',
        'reason' => 'Migrain di ruang kelas',
        'status' => 'APPROVED',
        'pickup_name' => 'Bapak Sudirman',
    ]);

    $exitPermit = SchoolExitPermit::create([
        'permit_number' => '421.3/SKIK/2026/XI/8882',
        'date' => now()->toDateString(),
        'out_time' => '10:00',
        'expected_back_time' => '12:00',
        'purpose' => 'Beli Alat Praktikum',
        'reason' => 'Praktikum Biologi',
        'status' => 'APPROVED',
        'is_returning' => true,
    ]);
    $exitPermit->students()->attach($student->id);

    // Test Kios Satpam view
    $response = $this->get(route('attendance.kiosk-satpam'));
    $response->assertStatus(200);
    $response->assertSee('KIOS POS SATPAM');
    $response->assertSee('Siswa Kios Satpam Test');
    $response->assertSee('421.3/SKPC/2026/XI/8881');
    $response->assertSee('421.3/SKIK/2026/XI/8882');
    $response->assertSee('Beli Alat Praktikum');
    $response->assertSee('Bapak Sudirman');
    $response->assertSee('DI LUAR SEKOLAH');
    $response->assertSee('Konfirmasi Kembali');

    // Test Satpam confirm returned
    $confirmRes = $this->post(route('attendance.kiosk-satpam.return', $exitPermit->id), [
        'actual_back_time' => '11:45',
    ]);
    $confirmRes->assertSessionHas('success');

    $exitPermit->refresh();
    expect($exitPermit->status)->toBe('RETURNED');
    expect($exitPermit->actual_back_time)->toBe('11:45');
});

test('picket officer user has access to approve and print permits', function () {
    $rand = Str::lower(Str::random(6));
    $piketUser = User::create([
        'name' => 'Petugas Piket Hari Ini S.Pd.',
        'email' => "piket_{$rand}@sman1lengkong.sch.id",
        'username' => "piket_{$rand}",
        'role' => 'guru_piket',
        'roles' => ['guru_piket'],
        'password' => bcrypt('password'),
    ]);

    expect($piketUser->canManageSchoolPermits())->toBeTrue();
    expect($piketUser->isPiket())->toBeTrue();
    expect($piketUser->isGuruPiket())->toBeTrue();

    $student = Student::create([
        'nis' => '94'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Siswa Test Piket',
        'gender' => 'P',
        'is_active' => true,
    ]);

    $earlyLeave = EarlyLeaveRequest::create([
        'letter_number' => '421.3/SKPC/2026/XI/7771',
        'student_id' => $student->id,
        'date' => now()->toDateString(),
        'leave_time' => '09:45',
        'reason_category' => 'Sakit',
        'reason' => 'Sakit maag',
        'status' => 'PENDING',
        'created_by' => $piketUser->id,
    ]);

    // Piket can approve early leave
    $approveRes = $this->actingAs($piketUser)
        ->post(route('attendance.early-leave.approve', $earlyLeave->id));
    $approveRes->assertSessionHas('success');

    $earlyLeave->refresh();
    expect($earlyLeave->status)->toBe('APPROVED');
    expect($earlyLeave->approved_by)->toBe($piketUser->id);

    // Piket can print letter
    $printRes = $this->actingAs($piketUser)
        ->get(route('attendance.early-leave.print', $earlyLeave->id));
    $printRes->assertStatus(200);
    $printRes->assertSee('SURAT IZIN PULANG CEPAT');
});
