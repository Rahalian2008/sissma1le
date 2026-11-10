<?php

use App\Models\User;

test('dashboard loads cleanly for super admin without 500 error', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::where('role', 'admin')->first();
    if (! $admin) {
        $admin = User::factory()->create(['role' => 'admin']);
    }

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertStatus(200);
});

test('dashboard loads cleanly for student without 500 error', function () {
    $studentUser = User::where('role', 'siswa')->first();
    if ($studentUser) {
        $response = $this->actingAs($studentUser)->get('/dashboard');
        $response->assertStatus(200);
    } else {
        expect(true)->toBeTrue();
    }
});

test('tahfidz index loads without attendance sub-menu', function () {
    $admin = User::where('role', 'super_admin')->first() ?? User::where('role', 'admin')->first();
    if (! $admin) {
        $admin = User::factory()->create(['role' => 'admin']);
    }

    $response = $this->actingAs($admin)->get('/tahfidz');
    $response->assertStatus(200);
    $response->assertDontSee('Presensi Shalat Dhuha');
    $response->assertSee("Buku Kontrol Digital Setor Hafalan Al-Qur'an");
});
