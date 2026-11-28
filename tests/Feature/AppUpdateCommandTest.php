<?php

use App\Models\User;

test('artisan app:update executes successfully with no-pull and no-cache options', function () {
    $this->artisan('app:update', [
        '--no-pull' => true,
        '--no-cache' => true,
        '--force' => true,
    ])
        ->expectsOutputToContain('SIS-SMA1LE')
        ->expectsOutputToContain('PROSES PEMBARUAN OTOMATIS APLIKASI & DATABASE')
        ->assertSuccessful();
});

test('system update web route is forbidden for guests and non-super-admin users', function () {
    // Guest
    $guestResponse = $this->post(route('master.settings.system_update'));
    $guestResponse->assertRedirect(route('login'));

    // Non-super admin (regular student)
    $studentUser = User::create([
        'name' => 'Siswa Test Update',
        'username' => 'siswatestupdate',
        'email' => 'siswatestupdate@example.com',
        'password' => bcrypt('password'),
        'role' => 'student',
        'is_active' => true,
    ]);

    $unauthorizedResponse = $this->actingAs($studentUser)->post(route('master.settings.system_update'));
    $unauthorizedResponse->assertForbidden();
});

test('system update web route works for super admin', function () {
    $superAdmin = User::create([
        'name' => 'Super Admin Test Update',
        'username' => 'superadmintestupdate',
        'email' => 'superadmintestupdate@example.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
        'roles' => ['super_admin'],
        'is_active' => true,
    ]);

    $response = $this->actingAs($superAdmin)->post(route('master.settings.system_update'));

    $response->assertRedirect();
    $response->assertSessionHas('update_success');
});
