<?php

use App\Models\Extracurricular;
use App\Models\SchoolSetting;
use App\Models\Teacher;
use App\Models\User;

test('unauthenticated users are redirected from website content management', function () {
    $response = $this->get('/master/website-content');

    $response->assertRedirect('/login');
});

test('non-admin users cannot access website content management', function () {
    $user = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $response = $this->actingAs($user)->get('/master/website-content');

    $response->assertStatus(403);
});

test('admin can view website content management page with all tabs', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $response = $this->actingAs($admin)->get('/master/website-content');

    $response->assertStatus(200);
    $response->assertSee('Kelola Isi & Konten Website Sekolah', false);
    $response->assertSee('Berita & Kabar', false);
    $response->assertSee('Slogan & Hero', false);
    $response->assertSee('Sambutan Kepala Sekolah');
    $response->assertSee('Visi & Misi', false);
    $response->assertSee('Foto & Banner', false);
    $response->assertSee('Agenda');
    $response->assertSee('Fasilitas');
});

test('admin can update hero headline and website displays it', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $postData = [
        'website_hero_headline' => 'Mencetak Generasi Berkarakter Luhur dan Berteknologi Modern',
        'website_hero_subheadline' => 'Portal Resmi Sekolah Digital Unggulan Jawa Barat.',
        'website_humas_phone' => '628987654321',
        'website_humas_message' => 'Halo Admin Humas, saya ingin menanyakan informasi pendaftaran.',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/hero', $postData);

    $response->assertRedirect('/master/website-content?tab=hero');
    expect(SchoolSetting::get('website_hero_headline'))->toBe('Mencetak Generasi Berkarakter Luhur dan Berteknologi Modern');

    // Check on public website
    $webResponse = $this->get('/');
    $webResponse->assertStatus(200);
    $webResponse->assertSee('Mencetak Generasi Berkarakter Luhur dan Berteknologi Modern');
    $webResponse->assertSee('Portal Resmi Sekolah Digital Unggulan Jawa Barat.');
});

test('admin can update headmaster speech and website displays it', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $postData = [
        'website_headmaster_name' => 'Drs. H. Mulyana, M.Pd.',
        'website_headmaster_title' => 'Kepala SMAN 1 Lengkong Sukabumi',
        'website_headmaster_speech_title' => 'Membangun Karakter Cerdas dan Berdaya Saing Global',
        'website_headmaster_speech_p1' => 'Selamat datang di gerbang digital masa depan anak-anak kita tercinta.',
        'website_headmaster_speech_p2' => 'Kami berkomitmen menyelenggarakan pembelajaran modern yang adil dan merata.',
        'website_headmaster_speech_extended' => 'Dukungan penuh dari orang tua dan segenap keluarga besar sekolah sangat kami harapkan.',
        'website_headmaster_quote' => 'Ilmu tanpa adab bagaikan pohon tanpa buah.',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/headmaster', $postData);

    $response->assertRedirect('/master/website-content?tab=headmaster');
    expect(SchoolSetting::get('website_headmaster_name'))->toBe('Drs. H. Mulyana, M.Pd.');

    // Check on public website
    $webResponse = $this->get('/');
    $webResponse->assertStatus(200);
    $webResponse->assertSee('Drs. H. Mulyana, M.Pd.');
    $webResponse->assertSee('Membangun Karakter Cerdas dan Berdaya Saing Global');
    $webResponse->assertSee('Ilmu tanpa adab bagaikan pohon tanpa buah.');
});

test('public website displays news section and default news articles', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('id="berita"', false);
    $response->assertSee('Kabar & Warta Sekolah', false);
    $response->assertSee('Olimpiade Robotik &amp; AI Pelajar Jawa Barat 2026', false);
    $response->assertSee('Gelar Karya P5');
    $response->assertSee('Portal Publik Orang Tua (SIP-Ortu)');
});

test('admin can create, update, and delete news articles', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // 1. Create a new article
    $newsData = [
        'news_title' => 'SMAN 1 Lengkong Luncurkan Program Green School Inklusif 2026',
        'news_category' => 'Kegiatan',
        'news_date' => '2026-10-25',
        'news_author' => 'Tim Adiwiyata',
        'news_excerpt' => 'Program penghijauan lingkungan sekolah terpadu dengan pemilahan sampah cerdas dan taman edukasi botani.',
        'news_content' => 'Dalam rangka mewujudkan sekolah berwawasan lingkungan hidup berkelanjutan, SMAN 1 Lengkong meresmikan gerakan penanaman bibit pohon endemik dan instalasi biopori di seluruh area kampus.',
        'is_featured' => '1',
    ];

    $createResponse = $this->actingAs($admin)->post('/master/website-content/news', $newsData);
    $createResponse->assertRedirect('/master/website-content?tab=news');
    $createResponse->assertSessionHas('success');

    // Verify stored in settings
    $articlesJson = SchoolSetting::get('website_news_json');
    expect($articlesJson)->not->toBeNull();
    $articles = json_decode($articlesJson, true);
    expect(count($articles))->toBeGreaterThanOrEqual(1);

    $created = collect($articles)->firstWhere('title', 'SMAN 1 Lengkong Luncurkan Program Green School Inklusif 2026');
    expect($created)->not->toBeNull();
    expect($created['category'])->toBe('Kegiatan');
    expect($created['is_featured'])->toBeTrue();

    // Verify visible on public website
    $webResponse = $this->get('/');
    $webResponse->assertStatus(200);
    $webResponse->assertSee('SMAN 1 Lengkong Luncurkan Program Green School Inklusif 2026');
    $webResponse->assertSee('Program penghijauan lingkungan sekolah terpadu');

    // 2. Update the article
    $updateData = [
        'news_title' => 'SMAN 1 Lengkong Raih Penghargaan Sekolah Adiwiyata Tingkat Nasional 2026',
        'news_category' => 'Prestasi',
        'news_date' => '2026-10-26',
        'news_author' => 'Humas & Adiwiyata',
        'news_excerpt' => 'Penghargaan bergengsi atas komitmen pelestarian lingkungan dan kurikulum berbasis ekologi.',
        'news_content' => 'SMAN 1 Lengkong resmi dinobatkan sebagai Sekolah Adiwiyata Nasional berkat inovasi pengelolaan sampah dan taman botani.',
        'is_featured' => '1',
    ];

    $updateResponse = $this->actingAs($admin)->put('/master/website-content/news/'.$created['id'], $updateData);
    $updateResponse->assertRedirect('/master/website-content?tab=news');
    $updateResponse->assertSessionHas('success');

    $updatedArticles = json_decode(SchoolSetting::get('website_news_json'), true);
    $updated = collect($updatedArticles)->firstWhere('id', $created['id']);
    expect($updated['title'])->toBe('SMAN 1 Lengkong Raih Penghargaan Sekolah Adiwiyata Tingkat Nasional 2026');
    expect($updated['category'])->toBe('Prestasi');

    // 3. Delete the article
    $deleteResponse = $this->actingAs($admin)->delete('/master/website-content/news/'.$created['id']);
    $deleteResponse->assertRedirect('/master/website-content?tab=news');
    $deleteResponse->assertSessionHas('success');

    $remainingArticles = json_decode(SchoolSetting::get('website_news_json'), true);
    expect(collect($remainingArticles)->firstWhere('id', $created['id']))->toBeNull();
});

test('admin can reset news section to defaults', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // Set custom news JSON
    SchoolSetting::set('website_news_json', json_encode([
        ['id' => 'temp_1', 'title' => 'Berita Percobaan', 'category' => 'Kegiatan', 'date' => '2026-10-24', 'author' => 'Tester', 'excerpt' => 'Ringkasan', 'content' => 'Isi', 'is_featured' => false],
    ]));

    $response = $this->actingAs($admin)->post('/master/website-content/reset', [
        'section' => 'news',
    ]);

    $response->assertRedirect('/master/website-content?tab=news');
    $response->assertSessionHas('success');

    // Setting should be deleted/null, falling back to DEFAULT_NEWS
    expect(SchoolSetting::get('website_news_json'))->toBeNull();
});

test('admin can view facilities tab and agenda tab seamlessly', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $facResponse = $this->actingAs($admin)->get('/master/website-content?tab=facilities');
    $facResponse->assertStatus(200);
    $facResponse->assertSee('Tambah Fasilitas Baru');
    $facResponse->assertSee('Daftar Sarana Prasarana');

    $agendaResponse = $this->actingAs($admin)->get('/master/website-content?tab=agendas');
    $agendaResponse->assertStatus(200);
    $agendaResponse->assertSee('Tambah Agenda Baru');
    $agendaResponse->assertSee('Agenda yang Sedang Tayang');
});

test('admin can add, update, and delete agenda', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // 1. Add Agenda with start and end date
    $addResponse = $this->actingAs($admin)->post('/master/website-content/agenda', [
        'agenda_title' => 'Simulasi UTBK Mandiri 2026',
        'agenda_date' => '2026-11-15',
        'agenda_end_date' => '2026-11-17',
        'agenda_tag' => 'Akademik',
        'agenda_description' => 'Simulasi persiapan seleksi masuk perguruan tinggi negeri.',
    ]);
    $addResponse->assertRedirect('/master/website-content?tab=agendas');
    $addResponse->assertSessionHas('success');

    $agendas = json_decode(SchoolSetting::get('website_agendas_json'), true);
    expect($agendas)->not->toBeEmpty();
    $added = collect($agendas)->firstWhere('title', 'Simulasi UTBK Mandiri 2026');
    expect($added)->not->toBeNull();
    expect($added['end_date'])->toBe('2026-11-17');
    $addedId = $added['id'];

    // 2. Update Agenda with new end date
    $updateResponse = $this->actingAs($admin)->put('/master/website-content/agenda/'.$addedId, [
        'agenda_title' => 'Simulasi UTBK Mandiri 2026 Diperbarui',
        'agenda_date' => '2026-11-18',
        'agenda_end_date' => '2026-11-20',
        'agenda_tag' => 'Akademik & UTBK',
        'agenda_description' => 'Deskripsi yang telah diperbarui.',
    ]);
    $updateResponse->assertRedirect('/master/website-content?tab=agendas');
    $updateResponse->assertSessionHas('success');

    $updatedAgendas = json_decode(SchoolSetting::get('website_agendas_json'), true);
    $updated = collect($updatedAgendas)->firstWhere('id', $addedId);
    expect($updated['title'])->toBe('Simulasi UTBK Mandiri 2026 Diperbarui');
    expect($updated['date'])->toBe('2026-11-18');
    expect($updated['end_date'])->toBe('2026-11-20');

    // 3. Delete Agenda
    $deleteResponse = $this->actingAs($admin)->delete('/master/website-content/agenda/'.$addedId);
    $deleteResponse->assertRedirect('/master/website-content?tab=agendas');
    $deleteResponse->assertSessionHas('success');

    $remaining = json_decode(SchoolSetting::get('website_agendas_json'), true);
    expect(collect($remaining)->firstWhere('id', $addedId))->toBeNull();
});

test('public website displays top 10 student achievement ranking leaderboard', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('id="prestasi"', false);
    $response->assertSee('Rangking Prestasi Siswa (Peringkat 1 - 10)', false);
    $response->assertSee('Klasemen Top 10');
    $response->assertSee('JUARA 1 • MEDALI EMAS');
    $response->assertSee('JUARA 2 • MEDALI PERAK');
    $response->assertSee('JUARA 3 • MEDALI PERUNGGU');
    $response->assertSee('Klasemen Lanjutan (Peringkat 4 s/d 10)');
});

test('admin can view staff tab in website content management', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $response = $this->actingAs($admin)->get('/master/website-content?tab=staff');

    $response->assertStatus(200);
    $response->assertSee('Dewan Guru');
    $response->assertSee('Staf Tata Usaha (TU)');
    $response->assertSee('Tambah Pegawai');
    $response->assertSee('Pilih Posisi / Kategori Pegawai');
    $response->assertSee('Mata Pelajaran (Mapel) yang Dipegang');
    $response->assertSee('Nama Tugas / Penugasan Tata Usaha (Pengganti Form Mapel)');
});

test('admin can add teacher with complete identity, mapel, and duty', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $postData = [
        'category' => 'guru',
        'name' => 'Dr. H. Rahmat Hidayat, M.Pd.',
        'nip' => '197505102001121005',
        'gender' => 'L',
        'employment_status' => 'PNS',
        'mapel' => 'Fisika Terapan & Robotika',
        'duty' => 'Wakasek Bidang Sarana & Prasarana',
        'order' => '1',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/staff', $postData);

    $response->assertRedirect('/master/website-content?tab=staff');
    $response->assertSessionHas('success');

    $staffJson = SchoolSetting::get('website_staff_json');
    expect($staffJson)->not->toBeNull();
    $staffList = json_decode($staffJson, true);
    $newMember = collect($staffList)->firstWhere('name', 'Dr. H. Rahmat Hidayat, M.Pd.');

    expect($newMember)->not->toBeNull();
    expect($newMember['category'])->toBe('guru');
    expect($newMember['mapel'])->toBe('Fisika Terapan & Robotika');
    expect($newMember['duty'])->toBe('Wakasek Bidang Sarana & Prasarana');
    expect($newMember['nip'])->toBe('197505102001121005');
});

test('admin can add staf tu with duty replacing mapel', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $postData = [
        'category' => 'staf_tu',
        'name' => 'Siti Nurhaliza, A.Md.Kom.',
        'nip' => '199208152022032010',
        'gender' => 'P',
        'employment_status' => 'PPPK',
        'tu_duty' => 'Administrasi Kesiswaan & Operator Dapodik',
        'order' => '2',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/staff', $postData);

    $response->assertRedirect('/master/website-content?tab=staff');
    $response->assertSessionHas('success');

    $staffJson = SchoolSetting::get('website_staff_json');
    $staffList = json_decode($staffJson, true);
    $newTu = collect($staffList)->firstWhere('name', 'Siti Nurhaliza, A.Md.Kom.');

    expect($newTu)->not->toBeNull();
    expect($newTu['category'])->toBe('staf_tu');
    expect($newTu['tu_duty'])->toBe('Administrasi Kesiswaan & Operator Dapodik');
    expect($newTu['mapel'])->toBe('');
});

test('validation requires mapel for guru and tu_duty for staf_tu', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // Guru without mapel should fail validation
    $badGuru = [
        'category' => 'guru',
        'name' => 'Guru Tanpa Mapel, S.Pd.',
        'gender' => 'L',
        'employment_status' => 'GTT',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/staff', $badGuru);
    $response->assertSessionHasErrors(['mapel']);

    // Staf TU without tu_duty should fail validation
    $badTu = [
        'category' => 'staf_tu',
        'name' => 'Staf TU Tanpa Tugas',
        'gender' => 'P',
        'employment_status' => 'PTT',
    ];

    $tuResponse = $this->actingAs($admin)->post('/master/website-content/staff', $badTu);
    $tuResponse->assertSessionHasErrors(['tu_duty']);
});

test('admin can update staff member details and toggle category', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // Seed a staff member
    $memberId = 'staff_test_99';
    SchoolSetting::set('website_staff_json', json_encode([
        [
            'id' => $memberId,
            'category' => 'guru',
            'name' => 'Budi Santoso, S.Pd.',
            'nip' => '198001012005011001',
            'gender' => 'L',
            'employment_status' => 'PNS',
            'mapel' => 'Geografi',
            'duty' => 'Guru Mapel',
            'tu_duty' => '',
            'order' => 1,
            'is_active' => true,
            'photo' => null,
        ],
    ]));

    // Update to Staf TU with TU Duty
    $updateData = [
        'category' => 'staf_tu',
        'name' => 'Budi Santoso, S.Kom.',
        'nip' => '198001012005011001',
        'gender' => 'L',
        'employment_status' => 'PNS',
        'tu_duty' => 'Pengelola Laboratorium Komputer & Jaringan',
        'order' => '1',
    ];

    $response = $this->actingAs($admin)->put('/master/website-content/staff/'.$memberId, $updateData);
    $response->assertRedirect('/master/website-content?tab=staff');
    $response->assertSessionHas('success');

    $staffList = json_decode(SchoolSetting::get('website_staff_json'), true);
    $updated = collect($staffList)->firstWhere('id', $memberId);

    expect($updated['name'])->toBe('Budi Santoso, S.Kom.');
    expect($updated['category'])->toBe('staf_tu');
    expect($updated['tu_duty'])->toBe('Pengelola Laboratorium Komputer & Jaringan');
    expect($updated['mapel'])->toBe('');

    // Delete staff member
    $delResponse = $this->actingAs($admin)->delete('/master/website-content/staff/'.$memberId);
    $delResponse->assertRedirect('/master/website-content?tab=staff');

    $remaining = json_decode(SchoolSetting::get('website_staff_json'), true);
    expect(collect($remaining)->firstWhere('id', $memberId))->toBeNull();
});

test('public website displays dewan guru and staf tu section with mapel and tu duties', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('id="guru-staf"', false);
    $response->assertSee('Dewan Guru & Staf Tata Usaha', false);
    $response->assertSee('Mata Pelajaran yang Dipegang:');
    $response->assertSee('Tugas / Unit Kerja Tata Usaha:');
    $response->assertSee('Dewan Guru');
    $response->assertSee('Staf Tata Usaha');
    $response->assertSee('Kepala Urusan Tata Usaha');
    $response->assertSee('Layanan Kesiswaan');
});

test('admin can update website running text and public website displays it in header while replacing menu links', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $postData = [
        'website_running_text' => 'PPDB Tahun 2026/2027 Telah Dibuka • Segera Daftarkan Putra-Putri Anda di SMAN 1 Lengkong',
        'website_running_text_badge' => 'INFO PPDB',
        'website_running_text_speed' => 'fast',
    ];

    $response = $this->actingAs($admin)->post('/master/website-content/running-text', $postData);
    $response->assertRedirect('/master/website-content?tab=running_text');
    $response->assertSessionHas('success');

    expect(SchoolSetting::get('website_running_text'))->toBe('PPDB Tahun 2026/2027 Telah Dibuka • Segera Daftarkan Putra-Putri Anda di SMAN 1 Lengkong');
    expect(SchoolSetting::get('website_running_text_badge'))->toBe('INFO PPDB');
    expect(SchoolSetting::get('website_running_text_speed'))->toBe('fast');

    // Public website displays running text
    $webResponse = $this->get('/');
    $webResponse->assertStatus(200);
    $webResponse->assertSee('header-running-track', false);
    $webResponse->assertSee('INFO PPDB');
    $webResponse->assertSee('PPDB Tahun 2026/2027 Telah Dibuka');
    $webResponse->assertDontSee('Desktop & Laptop Flexible Navigation Links', false);
});

test('admin can reset website running text to default', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    SchoolSetting::set('website_running_text', 'Teks Pengumuman Kustom', 'website');
    SchoolSetting::set('website_running_text_badge', 'PENGUMUMAN', 'website');

    $response = $this->actingAs($admin)->post('/master/website-content/reset', [
        'section' => 'running_text',
    ]);

    $response->assertRedirect('/master/website-content?tab=running_text');
    expect(SchoolSetting::get('website_running_text'))->toBeNull();
    expect(SchoolSetting::get('website_running_text_badge'))->toBeNull();
});

test('website displays actual teacher names for extracurricular advisors', function () {
    $teacher = Teacher::firstOrCreate(
        ['nip' => '198808082015011001'],
        [
            'name' => 'Drs. H. Mulyadi, M.Pd.',
            'gender' => 'L',
            'is_active' => true,
        ]
    );

    $extra = Extracurricular::firstOrCreate(
        ['code' => 'TEST_EKSKUL_WEB'],
        [
            'name' => 'Klub Robotik Digital',
            'category' => 'Sains & Teknologi',
            'teacher_id' => $teacher->id,
            'is_active' => true,
        ]
    );

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Klub Robotik Digital');
    $response->assertSee('Drs. H. Mulyadi, M.Pd.');
    $response->assertDontSee('Pendidik Sekola...');
});

test('website displays official NPSN 20202265 and student service hours Senin - Jumat: 06.30 - 15.00 WIB', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('NPSN: 20202265');
    $response->assertDontSee('NPSN: 20202278');
    $response->assertSee('Jam Layanan Pelajar');
    $response->assertSee('Senin - Jumat: 06.30 - 15.00 WIB');
    $response->assertDontSee('Senin - Jumat: 07.00 - 15.30 WIB');
});

test('website returns anti-cache headers and reflects live stats in real-time', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('no-cache');
    expect($cacheControl)->toContain('must-revalidate');
    $response->assertSee('Live Data SIS-SMA1LE');
    $response->assertSee('Tersinkronisasi Otomatis', false);
    $response->assertSee('Database SIS-SMA1LE', false);
});

test('website live-stats endpoint returns 200 with dynamic database counts', function () {
    $response = $this->get('/api/website/live-stats');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'server_time',
        'student_count',
        'teacher_count',
        'class_count',
        'extracurricular_count',
        'subject_count',
        'material_count',
        'quiz_count',
        'tahfidz_count',
        'worship_count',
        'daily_logs_count',
        'achievements_count',
    ]);
    expect($response->json('status'))->toBe('success');
});

test('website staff dynamically reflects newly created teachers in real-time', function () {
    $uniqueNip = '19991231'.rand(1000, 9999);
    $teacher = Teacher::create([
        'name' => 'Guru Baru Realtime SIS',
        'nip' => $uniqueNip,
        'gender' => 'L',
        'title' => 'Informatika Digital',
        'is_active' => true,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Guru Baru Realtime SIS');
    $response->assertSee($uniqueNip);

    // If teacher is deactivated, they disappear immediately
    $teacher->update(['is_active' => false]);

    $response2 = $this->get('/');
    $response2->assertStatus(200);
    $response2->assertDontSee('Guru Baru Realtime SIS');

    // Clean up
    $teacher->delete();
});

test('website showcases all new sis application ecosystem and reflects custom media uploads', function () {
    // Custom media & settings
    SchoolSetting::set('website_hero_photo', 'images/website/custom_hero.jpg');
    SchoolSetting::set('website_extracurriculars_photo', 'images/website/custom_ekskul.jpg');
    SchoolSetting::set('website_hero_badge', 'Peringkat 1 Sekolah Berkarakter');

    $response = $this->get('/');

    $response->assertStatus(200);

    // Verify custom media rendered
    $response->assertSee('images/website/custom_hero.jpg');
    $response->assertSee('images/website/custom_ekskul.jpg');
    $response->assertSee('Peringkat 1 Sekolah Berkarakter');

    // Verify new SIS ecosystem section
    $response->assertSee('id="ekosistem-sis"', false);
    $response->assertSee('Ekosistem Aplikasi Mandiri', false);
    $response->assertSee('Pembelajaran & Rapor Kurnas', false);
    $response->assertSee('Rapor Kurnas 2025', false);
    $response->assertSee('Permendikdasmen No. 13 Tahun 2025', false);
    $response->assertSee('Presensi 5 Metode', false);
    $response->assertSee('Literasi & E-Book 3D Flip', false);
    $response->assertSee('Ketatausahaan & SIP-Ortu', false);

    // Verify Open Graph & social rich preview tags
    $response->assertSee('property="og:image"', false);
    $response->assertSee('property="og:title"', false);
    $response->assertSee('name="twitter:card"', false);
    $response->assertSee('rel="image_src"', false);

    // Clean up
    SchoolSetting::whereIn('key', [
        'website_hero_photo',
        'website_extracurriculars_photo',
        'website_hero_badge',
    ])->delete();
});

test('website sharing link dynamically generates article og image and metadata for news link', function () {
    $response = $this->get('/?berita=news_1');

    $response->assertStatus(200);
    $response->assertSee('property="og:image"', false);
    $response->assertSee('property="og:type" content="article"', false);
    $response->assertSee('rel="image_src"', false);
    $response->assertSee('openNewsModal', false);
});

test('discipline ranking strictly caps verified achievers and guidance watchlist to top 10', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $response = $this->actingAs($admin)->get('/discipline/ranking');

    $response->assertStatus(200);
    $response->assertSee('Top 10 Peringkat Prestasi Siswa Teratas');
    $response->assertSee('Top 10 Siswa');
    $response->assertSee('Top 10 Pantauan Pembinaan');
    $response->assertSee('Top 10 Kasus');

    // Verify controller passed collections with count <= 10
    $achievers = $response->viewData('achievers');
    expect($achievers->count())->toBeLessThanOrEqual(10);

    $guidanceWatchlist = $response->viewData('guidanceWatchlist');
    expect($guidanceWatchlist->count())->toBeLessThanOrEqual(10);
});

test('website header is sticky and stays fixed when scrolling down or up', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('id="sticky-header-wrapper"', false);
    $response->assertSee('class="sticky top-0 z-50', false);
    $response->assertSee('scroll-padding-top: 135px', false);
    $response->assertSee('overflow-x: clip', false);
});
