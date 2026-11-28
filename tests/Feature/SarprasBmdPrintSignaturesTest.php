<?php

use App\Models\Teacher;
use App\Models\User;

test('teacher getSarprasManager and getHeadmaster return correct official identities', function () {
    $headmaster = Teacher::getHeadmaster();
    expect($headmaster['name'])->toBe('Pian Sopian, S.Pd., M.M.Pd');
    expect($headmaster['nip'])->toBe('198009132008011005');

    $sarprasManager = Teacher::getSarprasManager();
    expect($sarprasManager['name'])->toBe('Rayi Nopian Rosadi');
    expect($sarprasManager['nip'])->toBe('199511252025211112');
});

test('sarpras kib print renders Kepala Sekolah and Pengelola Sarana & Prasarana signatures correctly', function () {
    $superAdmin = User::where('role', 'super_admin')
        ->orWhereJsonContains('roles', 'super_admin')
        ->first();

    if (! $superAdmin) {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'roles' => ['super_admin']]);
    }

    $response = $this->actingAs($superAdmin)->get(route('sarpras.kib.print', ['kib_type' => 'B']));

    $response->assertOk();
    $response->assertSee('Kepala Sekolah');
    $response->assertSee('Pian Sopian, S.Pd., M.M.Pd');
    $response->assertSee('198009132008011005');
    $response->assertSee('Pengelola Sarana & Prasarana', false);
    $response->assertSee('Rayi Nopian Rosadi');
    $response->assertSee('199511252025211112');
    $response->assertDontSee('Kepala Sekolah selaku Pengguna Barang');
    $response->assertDontSee('Super Administrator');
});

test('sarpras bibi and bibhp print render updated signatures', function () {
    $superAdmin = User::where('role', 'super_admin')
        ->orWhereJsonContains('roles', 'super_admin')
        ->first();

    if (! $superAdmin) {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'roles' => ['super_admin']]);
    }

    $bibiResponse = $this->actingAs($superAdmin)->get(route('sarpras.bibi.print'));
    $bibiResponse->assertOk();
    $bibiResponse->assertSee('Kepala Sekolah');
    $bibiResponse->assertSee('Pengelola Sarana & Prasarana', false);
    $bibiResponse->assertSee('Rayi Nopian Rosadi');
    $bibiResponse->assertDontSee('Kepala Sekolah selaku Pengguna Barang');
    $bibiResponse->assertDontSee('Super Administrator');

    $bibhpResponse = $this->actingAs($superAdmin)->get(route('sarpras.bibhp.print'));
    $bibhpResponse->assertOk();
    $bibhpResponse->assertSee('Kepala Sekolah');
    $bibhpResponse->assertSee('Pengelola Sarana & Prasarana', false);
    $bibhpResponse->assertSee('Rayi Nopian Rosadi');
    $bibhpResponse->assertDontSee('Super Administrator');
});
