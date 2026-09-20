<?php

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;

test('quick role switcher can switch seamlessly between all 7 roles', function (string $role) {
    $response = $this->post('/quick-switch', [
        'role' => $role,
    ]);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success');

    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe($role);
})->with([
    'super_admin',
    'admin',
    'kepala_sekolah',
    'guru',
    'wali_kelas',
    'siswa',
    'orang_tua',
]);

test('each role is served their dedicated dashboard view and components', function (string $role, string $expectedHeading) {
    $user = User::where('role', $role)->first();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee($expectedHeading, false);
})->with([
    ['siswa', 'Dashboard Siswa'],
    ['guru', 'Pendidik & Pembina Kesiswaan'],
    ['wali_kelas', 'Wali Kelas Binaan'],
    ['kepala_sekolah', 'Kepala SMAN 1 Lengkong'],
    ['orang_tua', 'Pemantauan Perkembangan Anak'],
    ['admin', 'Administrator SMAN 1 Lengkong'],
    ['super_admin', 'Administrator SMAN 1 Lengkong'],
]);

test('unauthorized roles are forbidden from accessing master data configuration', function (string $role) {
    $user = User::where('role', $role)->first();

    $response = $this->actingAs($user)->get('/master/settings');

    // Should receive 403 Forbidden
    $response->assertStatus(403);
})->with([
    'siswa',
    'orang_tua',
    'guru',
    'kepala_sekolah',
]);

test('admin can access master data settings page', function () {
    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->get('/master/settings');

    $response->assertStatus(200);
    $response->assertSee('Konfigurasi Aturan');
    $response->assertSee('Geofence');
});

test('kepala sekolah has read-only executive access to reports and monitoring but blocked from modifications', function () {
    $kepsek = User::where('role', 'kepala_sekolah')->first();

    // 1. Can view monitoring pages
    $this->actingAs($kepsek)->get('/dashboard')->assertStatus(200)->assertSee('Pusat Pengawasan Eksekutif');
    $this->actingAs($kepsek)->get('/achievements')->assertStatus(200)->assertDontSee('+ Catat Prestasi Baru');
    $this->actingAs($kepsek)->get('/violations')->assertStatus(200)->assertDontSee('+ Catat Pelanggaran Baru');
    $this->actingAs($kepsek)->get('/guidance')->assertStatus(200)->assertDontSee('+ Tambah Catatan Pembinaan');
    $this->actingAs($kepsek)->get('/reports')->assertStatus(200);
    $this->actingAs($kepsek)->get('/attendance/history')->assertStatus(200);

    // 2. Blocked from creating or modifying data (Read-Only)
    $this->actingAs($kepsek)->get('/achievements/create')->assertStatus(403);
    $this->actingAs($kepsek)->post('/achievements', [])->assertStatus(403);
    $this->actingAs($kepsek)->get('/violations/create')->assertStatus(403);
    $this->actingAs($kepsek)->post('/violations', [])->assertStatus(403);
    $this->actingAs($kepsek)->post('/guidance', [])->assertStatus(403);
    $this->actingAs($kepsek)->get('/attendance/manual')->assertStatus(403);
});

test('siswa can login using NISN as both username and default password', function () {
    $response = $this->post(route('login.post'), [
        'email' => '0089234811',
        'password' => '0089234811',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe('siswa');
});

test('kepala sekolah can login using NIP as both username and default password', function () {
    $response = $this->post(route('login.post'), [
        'email' => '196803151994031004',
        'password' => '196803151994031004',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe('kepala_sekolah');
});

test('super admin Rh Aseng can login with SENKS, senks, or email with password 12345678', function (string $identifier) {
    $response = $this->post(route('login.post'), [
        'email' => $identifier,
        'password' => '12345678',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe('super_admin')
        ->and(auth()->user()->name)->toBe('Rh Aseng')
        ->and(auth()->user()->phone)->toBe('085887053005');
})->with([
    'SENKS',
    'senks',
    'seng@sma1le.sch.id',
]);

test('early leave route redirects to leave form with type PULANG_CEPAT', function () {
    $studentUser = User::where('role', 'siswa')->first();

    $response = $this->actingAs($studentUser)->get(route('attendance.early_leave'));

    $response->assertRedirect(route('attendance.leave', ['type' => 'PULANG_CEPAT']));
});

test('siswa can submit early leave request and wali kelas can approve it', function () {
    $studentUser = User::where('role', 'siswa')->first();
    $student = $studentUser->student ?? Student::first();

    // Student submits early leave request
    $submitResponse = $this->actingAs($studentUser)->post(route('attendance.leave.store'), [
        'status' => 'PULANG_CEPAT',
        'date' => now()->toDateString(),
        'early_leave_time' => '11:30',
        'notes' => 'Izin pulang lebih awal karena ada pemeriksaan dokter spesialis.',
        'latitude' => -7.56845,
        'longitude' => 112.04612,
        'accuracy' => 10,
        'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('fake-selfie-data'),
    ]);

    $submitResponse->assertSessionHas('success');

    $attendance = Attendance::where('student_id', $student->id)->latest('id')->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBe('PULANG_CEPAT')
        ->and($attendance->verification_status)->toBe('PERLU_VERIFIKASI')
        ->and($attendance->is_early_leave)->toBeTrue()
        ->and($attendance->early_leave_time)->toContain('11:30');

    // Super Admin / Wali Kelas approves the early leave request
    $superAdmin = User::where('username', 'SENKS')->first();
    $approveResponse = $this->actingAs($superAdmin)->post(route('attendance.leaves.approve', $attendance));

    $approveResponse->assertSessionHas('success');

    $attendance->refresh();
    expect($attendance->verification_status)->toBe('DIVERIFIKASI')
        ->and($attendance->out_method)->toBe('PULANG_CEPAT')
        ->and($attendance->time_out)->not->toBeNull();
});

test('parent can view overall school ranking with ANAK ANDA badge and access ranking page', function () {
    $parentUser = User::where('role', 'orang_tua')->first();
    $child = $parentUser->parentProfile?->student;

    // Parent dashboard shows ranking info
    $dashResponse = $this->actingAs($parentUser)->get(route('dashboard'));
    $dashResponse->assertStatus(200)
        ->assertSee('Peringkat Prestasi Keseluruhan')
        ->assertSee('Papan Peringkat Lengkap');

    // Parent can access discipline ranking page with all students and ANAK ANDA badge
    $rankingResponse = $this->actingAs($parentUser)->get(route('discipline.ranking'));
    $rankingResponse->assertStatus(200)
        ->assertSee('Papan Peringkat Siswa Berprestasi')
        ->assertSee('ANAK ANDA')
        ->assertSee('Semua Kelas (Tingkat Sekolah)');
});

test('login page renders cleanly without credential hints block', function () {
    $response = $this->get(route('login'));
    $response->assertStatus(200)
        ->assertDontSee('Petunjuk Masuk Akun:')
        ->assertDontSee('Gunakan nomor NISN sebagai Username');
});

test('quick role switcher switches to super admin Rh Aseng SENKS', function () {
    $adminUser = User::where('role', 'admin')->first();

    $response = $this->actingAs($adminUser)->post(route('quick.switch'), [
        'role' => 'super_admin',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    expect(auth()->user()->username)->toBe('SENKS')
        ->and(auth()->user()->role)->toBe('super_admin');
});
