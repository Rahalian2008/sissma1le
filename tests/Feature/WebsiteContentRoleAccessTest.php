<?php

use App\Models\SchoolSetting;
use App\Models\User;

test('kepala sekolah has full access to all website content tabs and features', function () {
    $principal = User::factory()->create([
        'name' => 'Drs. H. Mulyana, M.Pd.',
        'role' => 'kepala_sekolah',
        'roles' => ['kepala_sekolah'],
    ]);

    expect($principal->canManageWebsiteContent())->toBeTrue();
    expect($principal->allowedWebsiteContentTabs())->toBe([
        'news', 'hero', 'running_text', 'headmaster', 'vision', 'media', 'agendas', 'facilities', 'staff',
    ]);

    $response = $this->actingAs($principal)->get('/master/website-content');
    $response->assertStatus(200);
    $response->assertSee('Berita & Kabar', false);
    $response->assertSee('Slogan & Hero', false);
    $response->assertSee('Sambutan Kepala Sekolah', false);
    $response->assertSee('Fasilitas Sekolah', false);
    $response->assertSee('Guru & Staf TU', false);

    // Can access staff tab
    $responseStaff = $this->actingAs($principal)->get('/master/website-content?tab=staff');
    $responseStaff->assertStatus(200);
});

test('wakasek kurikulum, kesiswaan, humas, koordinator bk, kepala perpus, and koordinator keagamaan have access to news and agendas only', function () {
    $rolesToTest = [
        'wakasek_kurikulum',
        'wakasek_kesiswaan',
        'wakasek_humas',
        'koordinator_bk',
        'kepala_perpustakaan',
        'koordinator_keagamaan',
    ];

    foreach ($rolesToTest as $role) {
        $user = User::factory()->create([
            'role' => $role,
            'roles' => [$role],
        ]);

        expect($user->canManageWebsiteContent())->toBeTrue();
        expect($user->allowedWebsiteContentTabs())->toBe(['news', 'agendas']);
        expect($user->canAccessWebsiteContentTab('news'))->toBeTrue();
        expect($user->canAccessWebsiteContentTab('agendas'))->toBeTrue();
        expect($user->canAccessWebsiteContentTab('facilities'))->toBeFalse();
        expect($user->canAccessWebsiteContentTab('staff'))->toBeFalse();
        expect($user->canAccessWebsiteContentTab('hero'))->toBeFalse();

        // Index default should load news
        $response = $this->actingAs($user)->get('/master/website-content');
        $response->assertStatus(200);
        $response->assertSee('Berita & Kabar', false);
        $response->assertSee('Agenda');
        $response->assertDontSee('tab=hero');
        $response->assertDontSee('tab=facilities');
        $response->assertDontSee('tab=staff');

        // Accessing forbidden tab should be 403
        $forbiddenResponse = $this->actingAs($user)->get('/master/website-content?tab=facilities');
        $forbiddenResponse->assertStatus(403);

        $forbiddenStaff = $this->actingAs($user)->get('/master/website-content?tab=staff');
        $forbiddenStaff->assertStatus(403);
    }
});

test('wakasek sarpras has access to news, agendas, and facilities, but forbidden from staff or hero', function () {
    $sarpras = User::factory()->create([
        'role' => 'wakasek_sarpras',
        'roles' => ['wakasek_sarpras'],
    ]);

    expect($sarpras->canManageWebsiteContent())->toBeTrue();
    expect($sarpras->allowedWebsiteContentTabs())->toBe(['news', 'agendas', 'facilities']);
    expect($sarpras->canAccessWebsiteContentTab('news'))->toBeTrue();
    expect($sarpras->canAccessWebsiteContentTab('agendas'))->toBeTrue();
    expect($sarpras->canAccessWebsiteContentTab('facilities'))->toBeTrue();
    expect($sarpras->canAccessWebsiteContentTab('staff'))->toBeFalse();
    expect($sarpras->canAccessWebsiteContentTab('hero'))->toBeFalse();

    $response = $this->actingAs($sarpras)->get('/master/website-content?tab=facilities');
    $response->assertStatus(200);
    $response->assertSee('Fasilitas Sekolah');

    $forbiddenResponse = $this->actingAs($sarpras)->get('/master/website-content?tab=staff');
    $forbiddenResponse->assertStatus(403);
});

test('guru, tu, wali kelas, pembina ekstra, and guru bk can only access news', function () {
    $rolesToTest = ['guru', 'tu', 'wali_kelas', 'pembina_ekstra', 'guru_bk', 'bk'];

    foreach ($rolesToTest as $role) {
        $user = User::factory()->create([
            'role' => $role,
            'roles' => [$role],
        ]);

        expect($user->canManageWebsiteContent())->toBeTrue();
        expect($user->allowedWebsiteContentTabs())->toBe(['news']);
        expect($user->canAccessWebsiteContentTab('news'))->toBeTrue();
        expect($user->canAccessWebsiteContentTab('agendas'))->toBeFalse();
        expect($user->canAccessWebsiteContentTab('facilities'))->toBeFalse();

        $response = $this->actingAs($user)->get('/master/website-content');
        $response->assertStatus(200);
        $response->assertSee('Berita & Kabar', false);
        $response->assertDontSee('tab=hero');
        $response->assertDontSee('tab=agendas');
        $response->assertDontSee('tab=facilities');
        $response->assertDontSee('tab=staff');

        $forbiddenResponse = $this->actingAs($user)->get('/master/website-content?tab=agendas');
        $forbiddenResponse->assertStatus(403);
    }
});

test('siswa and orang tua cannot access website content management', function () {
    $student = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    expect($student->canManageWebsiteContent())->toBeFalse();
    $this->actingAs($student)->get('/master/website-content')->assertStatus(403);

    $parent = User::factory()->create([
        'role' => 'orang_tua',
        'roles' => ['orang_tua'],
    ]);

    expect($parent->canManageWebsiteContent())->toBeFalse();
    $this->actingAs($parent)->get('/master/website-content')->assertStatus(403);
});

test('news creation records publisher attribution and displays it', function () {
    $teacher = User::factory()->create([
        'name' => 'Budi Santoso, S.Kom.',
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $postData = [
        'news_title' => 'Pelatihan Robotik dan Coding SMAN 1 Lengkong 2026',
        'news_category' => 'Akademik',
        'news_date' => date('Y-m-d'),
        'news_author' => 'Tim Media TI',
        'news_excerpt' => 'Workshop intensif coding dan robotika untuk para siswa berprestasi.',
        'news_content' => 'Pelatihan diadakan selama 3 hari berturut-turut di lab komputer dengan antusiasme tinggi.',
        'is_featured' => '1',
    ];

    $response = $this->actingAs($teacher)->post('/master/website-content/news', $postData);
    $response->assertRedirect('/master/website-content?tab=news');

    $saved = json_decode(SchoolSetting::get('website_news_json', '[]'), true);
    $article = collect($saved)->firstWhere('title', 'Pelatihan Robotik dan Coding SMAN 1 Lengkong 2026');

    expect($article)->not->toBeNull();
    expect($article['created_by_name'])->toBe('Budi Santoso, S.Kom.');
    expect($article['created_by_role'])->toBe('Tenaga Pendidik (Guru Mapel)');

    // View on management page
    $managePage = $this->actingAs($teacher)->get('/master/website-content?tab=news');
    $managePage->assertStatus(200);
    $managePage->assertSee('Budi Santoso, S.Kom.');
    $managePage->assertSee('(Tenaga Pendidik (Guru Mapel))');

    // View on public website
    $publicWeb = $this->get('/');
    $publicWeb->assertStatus(200);
    $publicWeb->assertSee('Pelatihan Robotik dan Coding SMAN 1 Lengkong 2026');
    $publicWeb->assertSee('Budi Santoso, S.Kom.');
});
