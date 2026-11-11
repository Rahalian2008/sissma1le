<?php

use App\Models\SchoolBell;
use App\Models\User;
use App\Services\NationalAnthemService;
use App\Services\QuranDataService;

test('school bell index page loads successfully and seeds default schedules', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->get(route('school_bell.index'));
    $response->assertStatus(200);
    $response->assertSee('Bel Sekolah', false);
    $response->assertSee('Murottal', false);
    $response->assertSee('Ar-Rahman');
    $response->assertSee('Al-Fatihah');
    $response->assertSee('An-Nas');

    // Pastikan default schedules telah di-seed
    $this->assertDatabaseHas('school_bells', [
        'time' => '07:00',
        'sound_type' => 'chime',
    ]);
});

test('can create a new school bell schedule with murottal', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->post(route('school_bell.store'), [
        'title' => 'Murottal Istirahat Siang',
        'time' => '12:15',
        'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        'sound_type' => 'murottal',
        'surah_number' => 36, // Yasin
        'duration_seconds' => 1200,
        'volume' => 85,
        'description' => 'Murottal Surah Yasin saat istirahat siang',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('school_bells', [
        'title' => 'Murottal Istirahat Siang',
        'time' => '12:15',
        'sound_type' => 'murottal',
        'surah_number' => 36,
        'surah_name' => 'Yasin',
    ]);
});

test('can toggle school bell schedule status', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $bell = SchoolBell::create([
        'title' => 'Bel Uji Coba Toggle',
        'time' => '08:00',
        'days' => ['Senin'],
        'sound_type' => 'chime',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('school_bell.toggle', $bell));
    $response->assertRedirect();

    $this->assertDatabaseHas('school_bells', [
        'id' => $bell->id,
        'is_active' => false,
    ]);
});

test('api endpoints return json data for scheduler and 114 surahs', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $scheduleResponse = $this->actingAs($admin)->get(route('school_bell.api.schedules'));
    $scheduleResponse->assertStatus(200);
    $scheduleResponse->assertJsonStructure(['success', 'day', 'current_time', 'data']);

    $surahsResponse = $this->actingAs($admin)->get(route('school_bell.api.surahs'));
    $surahsResponse->assertStatus(200);
    $surahsResponse->assertJsonStructure(['success', 'total', 'data']);
    $this->assertEquals(114, $surahsResponse->json('total'));
});

test('quran data service returns all 114 surahs accurately', function () {
    $surahs = QuranDataService::getAllSurahs();
    expect(count($surahs))->toBe(114);
    expect($surahs[1]['name'])->toBe('Al-Fatihah');
    expect($surahs[114]['name'])->toBe('An-Nas');
});

test('national anthem service provides all 10 requested songs with lyrics and notes', function () {
    $anthems = NationalAnthemService::getNationalAnthems();
    expect(count($anthems))->toBe(10);

    $titles = collect($anthems)->pluck('title')->all();
    expect($titles)->toContain('Indonesia Raya');
    expect($titles)->toContain('Indonesia Pusaka');
    expect($titles)->toContain('Hari Merdeka');
    expect($titles)->toContain('Bagimu Negeri');
    expect($titles)->toContain('Berkibarlah Benderaku');
    expect($titles)->toContain('Maju Indonesia');
    expect($titles)->toContain('Maju Tak Gentar');
    expect($titles)->toContain('Tanah Airku');
    expect($titles)->toContain('Satu Nusa Satu Bangsa');
    expect($titles)->toContain('Garuda Pancasila');
});

test('instant sounds service provides at least 10 types of sounds', function () {
    $sounds = NationalAnthemService::getInstantSounds();
    expect(count($sounds))->toBeGreaterThanOrEqual(10);

    $types = collect($sounds)->pluck('sound_type')->all();
    expect($types)->toContain('westminster');
    expect($types)->toContain('threetone');
    expect($types)->toContain('twotone');
    expect($types)->toContain('siren_fire');
    expect($types)->toContain('siren_earthquake');
    expect($types)->toContain('whistle');
    expect($types)->toContain('fanfare');
    expect($types)->toContain('gong');
});

test('can create a new school bell schedule with national anthem', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->post(route('school_bell.store'), [
        'title' => 'Lagu Indonesia Raya Pukul 10',
        'time' => '10:00',
        'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        'sound_type' => 'song',
        'anthem_id' => 'indonesia-raya',
        'duration_seconds' => 120,
        'volume' => 100,
        'description' => 'Menyanyikan lagu kebangsaan Indonesia Raya',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('school_bells', [
        'title' => 'Lagu Indonesia Raya Pukul 10',
        'time' => '10:00',
        'sound_type' => 'song',
    ]);
});

test('kiosk page loads successfully with soundboard and anthems', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->get(route('school_bell.kiosk'));
    $response->assertStatus(200);
    $response->assertSee('KIOS BEL SEKOLAH TERPADU', false);
    $response->assertSee('Soundboard Instan', false);
    $response->assertSee('Lagu-Lagu Nasional', false);
    $response->assertSee('Indonesia Raya', false);
    $response->assertSee('Garuda Pancasila', false);
});

test('sidebar has renamed menus SPMB and Bel Sekolah without old TKA badge', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('SPMB', false);
    $response->assertDontSee('SPMB Baru (TKA)', false);
    $response->assertSee('Bel Sekolah', false);
    $response->assertDontSee('Bel Sekolah & Murottal 114 Surah', false);
});

test('can update an existing school bell schedule', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $bell = SchoolBell::create([
        'title' => 'Bel Belum Diedit',
        'time' => '07:15',
        'days' => ['Senin', 'Selasa'],
        'sound_type' => 'chime',
        'volume' => 90,
        'duration_seconds' => 15,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->put(route('school_bell.update', $bell), [
        'title' => 'Bel Sesudah Diedit',
        'time' => '07:30',
        'days' => ['Senin', 'Selasa', 'Rabu'],
        'sound_type' => 'voice',
        'voice_text' => 'Pelajaran berikutnya segera dimulai.',
        'volume' => 100,
        'duration_seconds' => 20,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('school_bells', [
        'id' => $bell->id,
        'title' => 'Bel Sesudah Diedit',
        'time' => '07:30',
        'sound_type' => 'voice',
        'voice_text' => 'Pelajaran berikutnya segera dimulai.',
    ]);
});

test('can delete a school bell schedule and clearing all does not auto reseed on next index visit', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $bell = SchoolBell::create([
        'title' => 'Bel Siap Dihapus',
        'time' => '09:00',
        'days' => ['Senin'],
        'sound_type' => 'chime',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->delete(route('school_bell.destroy', $bell));
    $response->assertRedirect();
    $this->assertDatabaseMissing('school_bells', ['id' => $bell->id]);

    // Sekarang hapus seluruh jadwal
    $clearResponse = $this->actingAs($admin)->post(route('school_bell.clear_all'));
    $clearResponse->assertRedirect();
    expect(SchoolBell::count())->toBe(0);

    // Kunjungi halaman index - pastikan TIDAK diisi otomatis lagi!
    $indexResponse = $this->actingAs($admin)->get(route('school_bell.index'));
    $indexResponse->assertStatus(200);
    expect(SchoolBell::count())->toBe(0);

    // Sekarang coba reset kembali ke default
    $resetResponse = $this->actingAs($admin)->post(route('school_bell.reset_defaults'));
    $resetResponse->assertRedirect();
    expect(SchoolBell::count())->toBeGreaterThanOrEqual(12);
});
