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

    // Pastikan default schedules telah di-seed sesuai jadwal kurikulum resmi
    $this->assertDatabaseHas('school_bells', [
        'time' => '07:15',
        'sound_type' => 'chime',
    ]);
    $this->assertDatabaseHas('school_bells', [
        'time' => '06:05',
        'sound_type' => 'murottal',
    ]);
    $this->assertDatabaseHas('school_bells', [
        'sound_type' => 'song',
    ]);
    $this->assertDatabaseCount('school_bells', 52);
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

test('can create and update school bell schedule with multiple surahs', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->post(route('school_bell.store'), [
        'title' => 'Murottal Multi Surat Pagi',
        'time' => '06:30',
        'days' => ['Senin', 'Selasa'],
        'sound_type' => 'murottal',
        'surah_numbers' => [1, 55, 112],
        'duration_seconds' => 1200,
        'volume' => 80,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $bell = SchoolBell::where('title', 'Murottal Multi Surat Pagi')->first();
    expect($bell)->not->toBeNull();
    expect($bell->surah_numbers)->toBe([1, 55, 112]);
    expect($bell->surah_number)->toBe(1);
    expect($bell->playlist_items)->toHaveCount(3);
    expect($bell->playlist_items[0]['title'])->toContain('Al-Fatihah');
    expect($bell->playlist_items[1]['title'])->toContain('Ar-Rahman');
    expect($bell->playlist_items[2]['title'])->toContain('Al-Ikhlas');

    // Update with new surahs
    $updateResponse = $this->actingAs($admin)->put(route('school_bell.update', $bell), [
        'title' => 'Murottal 3 Qul Sore',
        'time' => '15:30',
        'days' => ['Senin', 'Selasa', 'Rabu'],
        'sound_type' => 'murottal',
        'surah_numbers' => [112, 113, 114],
        'duration_seconds' => 600,
        'volume' => 90,
    ]);

    $updateResponse->assertSessionHasNoErrors();
    $bell->refresh();
    expect($bell->title)->toBe('Murottal 3 Qul Sore');
    expect($bell->surah_numbers)->toBe([112, 113, 114]);
    expect($bell->surah_number)->toBe(112);
    expect($bell->playlist_items)->toHaveCount(3);
});

test('can create and update school bell schedule with multiple national anthems', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->post(route('school_bell.store'), [
        'title' => 'Playlist Upacara Bendera',
        'time' => '07:15',
        'days' => ['Senin'],
        'sound_type' => 'song',
        'anthem_ids' => ['indonesia-raya', 'hari-merdeka', 'garuda-pancasila'],
        'duration_seconds' => 400,
        'volume' => 100,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $bell = SchoolBell::where('title', 'Playlist Upacara Bendera')->first();
    expect($bell)->not->toBeNull();
    expect($bell->anthem_ids)->toBe(['indonesia-raya', 'hari-merdeka', 'garuda-pancasila']);
    expect($bell->anthem_id)->toBe('indonesia-raya');
    expect($bell->playlist_items)->toHaveCount(3);
    expect($bell->playlist_items[0]['title'])->toBe('Indonesia Raya');
    expect($bell->playlist_items[1]['title'])->toBe('Hari Merdeka');
    expect($bell->playlist_items[2]['title'])->toBe('Garuda Pancasila');
});

test('can create and update school bell schedule with soundboard sound', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $response = $this->actingAs($admin)->post(route('school_bell.store'), [
        'title' => 'Sirine Siaga Evakuasi Darurat',
        'time' => '10:00',
        'days' => ['Senin', 'Selasa'],
        'sound_type' => 'soundboard',
        'soundboard_id' => 'siren-emergency',
        'duration_seconds' => 30,
        'volume' => 100,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $bell = SchoolBell::where('title', 'Sirine Siaga Evakuasi Darurat')->first();
    expect($bell)->not->toBeNull();
    expect($bell->sound_type)->toBe('soundboard');
    expect($bell->audio_url)->toBe('soundboard:siren_fire');
    expect($bell->playlist)->toBeArray();
    expect($bell->playlist[0]['id'])->toBe('siren-emergency');

    // Update with another soundboard item
    $updateResponse = $this->actingAs($admin)->put(route('school_bell.update', $bell), [
        'title' => 'Peluit Lapangan Apel',
        'time' => '06:45',
        'days' => ['Senin'],
        'sound_type' => 'soundboard',
        'soundboard_id' => 'whistle-picket',
        'duration_seconds' => 15,
        'volume' => 95,
    ]);

    $updateResponse->assertSessionHasNoErrors();
    $bell->refresh();
    expect($bell->title)->toBe('Peluit Lapangan Apel');
    expect($bell->time)->toBe('06:45');
    expect($bell->sound_type)->toBe('soundboard');
    expect($bell->audio_url)->toBe('soundboard:whistle');
    expect($bell->playlist[0]['id'])->toBe('whistle-picket');
});
