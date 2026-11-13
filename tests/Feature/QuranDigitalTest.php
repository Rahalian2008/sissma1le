<?php

use App\Models\User;

test('all roles can access al-quran digital index page', function ($role) {
    $user = User::factory()->create([
        'role' => $role,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('quran.index'));
    $response->assertStatus(200);
    $response->assertSee("Al-Qur'an Digital & Terjemahan", false);
    $response->assertSee('Al-Fatihah');
    $response->assertSee('An-Nas');
    $response->assertSee('114');
})->with([
    'admin',
    'guru',
    'wali_kelas',
    'siswa',
    'orang_tua',
    'kepala_sekolah',
]);

test('can view surah detail reader mode with verses and translation', function () {
    $user = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('quran.show', 1));
    $response->assertStatus(200);
    $response->assertSee('Al-Fatihah');
    $response->assertSee('Pembukaan');
    $response->assertSee('Makkiyah');
    $response->assertSee('7 AYAT', false);
    $response->assertSee('Daftar Surat', false);
});

test('tahfidz quran alias redirects cleanly to quran index', function () {
    $user = User::factory()->create([
        'role' => 'guru',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('tahfidz.quran'));
    $response->assertRedirect(route('quran.index'));
});

test('api endpoints return json for surah and tafsir', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $surahResponse = $this->actingAs($user)->get(route('quran.api.surah', 1));
    $surahResponse->assertStatus(200);
    $surahResponse->assertJsonStructure([
        'success',
        'data' => [
            'number',
            'name',
            'arabic',
            'translation',
            'verses',
        ],
    ]);

    $tafsirResponse = $this->actingAs($user)->get(route('quran.api.tafsir', 1));
    $tafsirResponse->assertStatus(200);
    $tafsirResponse->assertJsonStructure([
        'success',
        'surah_number',
    ]);
});

test('sidebar contains al-quran digital navigation link for users', function () {
    $user = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee("Al-Qur'an Digital", false);
    $response->assertSee(route('quran.index'), false);
});
