<?php

use App\Models\Teacher;
use App\Models\User;

test('teacher getTreasurer returns bendahara official identity', function () {
    $treasurer = Teacher::getTreasurer();

    expect($treasurer['name'])->toBe('Rian Hadiansyah, S.Pd.')
        ->and($treasurer['nip'])->toBe('199501172025211109');
});

test('finance print documents display Kepala Sekolah and Bendahara', function () {
    $user = User::where('role', 'super_admin')->first() ?: User::factory()->create(['role' => 'super_admin']);

    // 1. BKU Print
    $responseBku = $this->actingAs($user)->get(route('finance.bku.print'));
    $responseBku->assertStatus(200)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Rian Hadiansyah, S.Pd.', false)
        ->assertSee('Bendahara Sekolah', false);

    // 2. Financial Reports Print
    $responseReport = $this->actingAs($user)->get(route('finance.reports.print'));
    $responseReport->assertStatus(200)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Rian Hadiansyah, S.Pd.', false)
        ->assertSee('Bendahara Sekolah', false);

    // 3. Reconciliation Print
    $responseReconcil = $this->actingAs($user)->get(route('finance.reconciliation.print'));
    $responseReconcil->assertStatus(200)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Rian Hadiansyah, S.Pd.', false);

    // 4. Subsidiary Books Print
    $responseSub = $this->actingAs($user)->get(route('finance.subsidiary.print'));
    $responseSub->assertStatus(200)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Rian Hadiansyah, S.Pd.', false);

    // 5. Taxes Print
    $responseTaxes = $this->actingAs($user)->get(route('finance.taxes.print'));
    $responseTaxes->assertStatus(200)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Rian Hadiansyah, S.Pd.', false);
});

test('kiosk display includes Slide 14 Keuangan and Slide 15 Tamu with 15 total slides', function () {
    $response = $this->get(route('kiosk.index'));
    $response->assertStatus(200)
        ->assertSee('14. Keuangan')
        ->assertSee('15. Tamu')
        ->assertSee('id="slide-13"', false)
        ->assertSee('id="slide-14"', false)
        ->assertSee('const TOTAL_SLIDES = 15;', false)
        ->assertSee('Laporan Keuangan &amp; Realisasi BKU Sekolah', false);

    // Check JSON API endpoint
    $apiResponse = $this->get(route('kiosk.data'));
    $apiResponse->assertStatus(200)
        ->assertJsonStructure([
            'finance' => [
                'fiscal_year',
                'total_allocated',
                'total_realized',
                'remaining',
                'absorption_pct',
                'bku_balance',
                'cash_balance',
                'bank_balance',
                'treasurer',
                'headmaster',
            ],
            'guests',
        ]);
});

test('sarpras maintenance print route renders official signatures', function () {
    $user = User::where('role', 'super_admin')->first() ?: User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($user)->get(route('sarpras.maintenance.print'));
    $response->assertStatus(200)
        ->assertSee('BUKU REGISTER PEMELIHARAAN &amp; SERVIS BARANG INVENTARIS (BMD)', false)
        ->assertSee('Pian Sopian, S.Pd., M.M.Pd', false)
        ->assertSee('Pengelola Sarana &amp; Prasarana', false)
        ->assertSee('Rayi Nopian Rosadi', false);
});

test('tu keuangan sub-module loads without error', function () {
    $user = User::where('role', 'super_admin')->first() ?: User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($user)->get(route('tu.keuangan.index'));
    $response->assertStatus(200)
        ->assertSee('Administrasi Keuangan Sekolah', false);
});
