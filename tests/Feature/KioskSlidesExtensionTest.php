<?php

test('kiosk index renders all 14 slides including profile pimpinan, extracurricular, rombel lp, news, agenda, guru staf, perpus, bk, and guests', function () {
    $response = $this->get(route('kiosk.index'));

    $response->assertStatus(200);

    // Assert Slide Navigation Pills 1 through 14
    for ($i = 0; $i <= 13; $i++) {
        $response->assertSee("pill-slide-{$i}", false);
    }

    // Assert Slide 5: Profil Resmi Kelembagaan & Pimpinan
    $response->assertSee('slide-5', false);
    $response->assertSee('Profil Resmi Kelembagaan &amp; Kepemimpinan Sekolah', false);
    $response->assertSee('Pian Sopian', false);
    $response->assertSee('198009132008011005', false);
    $response->assertSee('PIMPINAN SEKOLAH', false);
    $response->assertSee('VISI SEKOLAH', false);
    $response->assertSee('MISI SEKOLAH', false);

    // Assert Slide 6: Presensi & Pembinaan Ekstrakurikuler
    $response->assertSee('slide-6', false);
    $response->assertSee('Presensi &amp; Pembinaan Ekstrakurikuler Siswa', false);
    $response->assertSee('CABANG EKSKUL', false);
    $response->assertSee('Log Presensi Ekskul Hari Ini', false);
    $response->assertSee('4 PILAR PEMBINAAN KARAKTER EKSKUL', false);

    // Assert Slide 7: Data Rombel (L/P) & Wali Kelas
    $response->assertSee('slide-7', false);
    $response->assertSee('Data Rombongan Belajar (Rombel) &amp; Direktori Wali Kelas', false);
    $response->assertSee('Total Seluruh Siswa', false);
    $response->assertSee('Laki-Laki (L)', false);
    $response->assertSee('Perempuan (P)', false);
    $response->assertSee('Total Rombel', false);
    $response->assertSee('Wali Kelas', false);

    // Assert Slide 8: Berita
    $response->assertSee('slide-8', false);
    $response->assertSee('Warta &amp; Publikasi Resmi Sekolah', false);

    // Assert Slide 9: Agenda
    $response->assertSee('slide-9', false);
    $response->assertSee('Agenda &amp; Kalender Kegiatan Akademik', false);

    // Assert Slide 10: Guru & Staf
    $response->assertSee('slide-10', false);
    $response->assertSee('Direktori Dewan Guru &amp; Tenaga Kependidikan', false);
    $response->assertSee('staffPageIndicator', false);

    // Assert Slide 11: Perpustakaan & Literasi
    $response->assertSee('slide-11', false);
    $response->assertSee('Perpustakaan &amp; Literasi Digital', false);
    $response->assertSee('Klasifikasi DDC', false);
    $response->assertSee('Duta Literasi Terpilih', false);
    $response->assertSee('Pengurus &amp; Pengelola Perpustakaan', false);
    $response->assertSee('Vamil Setiawati', false);
    $response->assertSee('Hasbi', false);

    // Assert Slide 12: Bimbingan & Konseling
    $response->assertSee('slide-12', false);
    $response->assertSee('Bimbingan &amp; Konseling (BK)', false);
    $response->assertSee('Konselor / Guru BK Sekolah', false);
    $response->assertSee('KOORDINATOR BK', false);
    $response->assertSee('GURU BK', false);
    $response->assertSee('Ida Marttin Maolidah', false);

    // Assert Slide 13: Buku Tamu & Kunjungan Sekolah
    $response->assertSee('slide-13', false);
    $response->assertSee('Buku Tamu Kedinasan &amp; Kunjungan Sekolah', false);
});

test('kiosk data json endpoint includes news, agendas, staff directory, library, bk, guests, rombels, and extracurricular', function () {
    $response = $this->get(route('kiosk.data'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'daily',
        'dhuha',
        'dzuhur',
        'keputrian',
        'tahfidz',
        'extracurricular',
        'rombels',
        'profile',
        'news',
        'agendas',
        'staff',
        'library',
        'bk',
        'guests',
        'ticker',
    ]);

    $data = $response->json();
    expect($data['news'])->toBeArray()
        ->and($data['agendas'])->toBeArray()
        ->and($data['staff'])->toBeArray()
        ->and(count($data['staff']))->toBeGreaterThanOrEqual(1)
        ->and($data['library'])->toBeArray()
        ->and($data['bk'])->toBeArray()
        ->and($data['guests'])->toBeArray()
        ->and($data['rombels'])->toBeArray()
        ->and($data['extracurricular'])->toBeArray();
});

test('library guest book kiosk loads properly with single-screen responsive layout', function () {
    $response = $this->get(route('library.guest_book.index'));

    $response->assertStatus(200);
    $response->assertSee('Buku Tamu Perpustakaan', false);
    $response->assertSee('h-screen max-h-screen overflow-hidden', false);
    $response->assertSee('kiosk-guest-form', false);
    $response->assertSee('btn-type-siswa', false);
    $response->assertSee('btn-type-guru', false);
    $response->assertSee('btn-type-umum', false);
});

test('kios satpam loads properly with single-screen responsive layout and unified header', function () {
    $response = $this->get(route('attendance.kiosk-satpam'));

    $response->assertStatus(200);
    $response->assertSee('KIOS POS SATPAM', false);
    $response->assertSee('h-screen max-h-screen overflow-hidden', false);
    $response->assertSee('section-exit-permit', false);
    $response->assertSee('section-early-leave', false);
});

test('school website renders open graph and share buttons with image thumbnail support', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('property="og:image"', false);
    $response->assertSee('property="og:image:secure_url"', false);
    $response->assertSee('name="twitter:image"', false);
    $response->assertSee('/og-image/', false);
    $response->assertSee('shareNewsWhatsApp', false);
    $response->assertSee('copyNewsLink', false);
    $response->assertSee('shareAgendaWhatsApp', false);
    $response->assertDontSee('SIAKAD SIS-SMA1LE');
});

test('og image route serves compressed jpeg for social link previews', function () {
    $response = $this->get('/og-image/school-hero.jpg');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
});

test('clean news url loads website with specific article context and open graph tags', function () {
    $response = $this->get('/berita/implementasi-kurikulum-nasional-permendikdasmen-13-2025-deep-learning');

    $response->assertStatus(200);
    $response->assertSee('property="og:image"', false);
    $response->assertSee('/og-image/implementasi-kurikulum-nasional-permendikdasmen-13-2025-deep-learning.jpg', false);
});
