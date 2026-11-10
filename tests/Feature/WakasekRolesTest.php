<?php

use App\Models\Teacher;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
});

test('all 4 wakasek users exist with correct roles and can view their dedicated dashboards', function (string $role, string $expectedHeadline) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee($expectedHeadline);
})->with([
    ['wakasek_kesiswaan', 'SMAN 1 Lengkong - Kesiswaan'],
    ['wakasek_kurikulum', 'Wakasek Bid. Kurikulum'],
    ['wakasek_humas', 'Wakasek Bid. Humas'],
    ['wakasek_sarpras', 'Wakasek Bid. Sarpras'],
]);

test('user model helper methods correctly identify wakasek roles', function () {
    $roles = [
        'wakasek_kesiswaan' => 'isWakasekKesiswaan',
        'wakasek_kurikulum' => 'isWakasekKurikulum',
        'wakasek_humas' => 'isWakasekHumas',
        'wakasek_sarpras' => 'isWakasekSarpras',
    ];

    foreach ($roles as $role => $method) {
        $user = new User(['role' => $role]);
        expect($user->isWakasek())->toBeTrue()
            ->and($user->$method())->toBeTrue()
            ->and($user->isTeacher())->toBeTrue();
    }

    $regularTeacher = new User(['role' => 'guru']);
    expect($regularTeacher->isWakasek())->toBeFalse()
        ->and($regularTeacher->isWakasekKesiswaan())->toBeFalse()
        ->and($regularTeacher->isWakasekKurikulum())->toBeFalse()
        ->and($regularTeacher->isWakasekHumas())->toBeFalse()
        ->and($regularTeacher->isWakasekSarpras())->toBeFalse();
});

test('super admin can quick switch to all 4 wakasek roles seamlessly', function (string $role, string $expectedDashboardText) {
    $superAdmin = User::where('role', 'super_admin')->first();

    $response = $this->actingAs($superAdmin)->post(route('quick.switch'), [
        'role' => $role,
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertSee($expectedDashboardText);
})->with([
    ['wakasek_kesiswaan', 'SMAN 1 Lengkong - Kesiswaan'],
    ['wakasek_kurikulum', 'Wakasek Bid. Kurikulum'],
    ['wakasek_humas', 'Wakasek Bid. Humas'],
    ['wakasek_sarpras', 'Wakasek Bid. Sarpras'],
]);

test('wakasek kurikulum can access curriculum monitoring and ai raport', function () {
    $user = User::where('role', 'wakasek_kurikulum')->first();

    $this->actingAs($user)->get(route('raport.index'))->assertStatus(200);
    $this->actingAs($user)->get(route('tahfidz.index'))->assertStatus(200);
    $this->actingAs($user)->get(route('reports.index'))->assertStatus(200);
});

test('wakasek humas can access verified achievements and public relations records', function () {
    $user = User::where('role', 'wakasek_humas')->first();

    $this->actingAs($user)->get(route('achievements.index'))->assertStatus(200);
    $this->actingAs($user)->get(route('discipline.ranking'))->assertStatus(200);
    $this->actingAs($user)->get(route('discipline.report'))->assertStatus(200);
});

test('wakasek sarpras can monitor attendance scanner devices and access reports', function () {
    $user = User::where('role', 'wakasek_sarpras')->first();

    $this->actingAs($user)->get('/dashboard')->assertStatus(200)
        ->assertSee('Perangkat IoT Terpasang')
        ->assertSee('Mesin RFID');
    $this->actingAs($user)->get(route('reports.index'))->assertStatus(200);
});

test('admin can assign wakasek roles via master data guru update', function () {
    $admin = User::where('role', 'admin')->first();
    $teacher = Teacher::where('nip', '197804122005011003')->first(); // Bambang Sugiantoro

    $response = $this->actingAs($admin)->put(route('master.teachers.update', $teacher->id), [
        'nip' => $teacher->nip,
        'name' => $teacher->name,
        'gender' => $teacher->gender,
        'title' => 'Wakasek Bid. Kurikulum / Guru Fisika',
        'phone' => $teacher->phone,
        'role' => 'wakasek_kurikulum',
        'roles' => ['wakasek_kurikulum', 'guru'],
        'email' => $teacher->user->email,
        'is_active' => 1,
    ]);

    $response->assertSessionHasNoErrors();
    $teacher->refresh();
    expect($teacher->user->role)->toBe('wakasek_kurikulum')
        ->and($teacher->user->hasRole('wakasek_kurikulum'))->toBeTrue();
});
