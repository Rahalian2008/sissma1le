<?php

test('kiosk index renders slides for berita, agenda, and guru & staf with photos and names', function () {
    $response = $this->get(route('kiosk.index'));

    $response->assertStatus(200);

    // Assert Slide Navigation Pills 1 through 12
    $response->assertSee('pill-slide-0', false);
    $response->assertSee('pill-slide-5', false);
    $response->assertSee('pill-slide-6', false);
    $response->assertSee('pill-slide-7', false);
    $response->assertSee('pill-slide-8', false);
    $response->assertSee('pill-slide-9', false);
    $response->assertSee('pill-slide-10', false);
    $response->assertSee('pill-slide-11', false);

    // Assert Slide 6: Berita
    $response->assertSee('slide-6', false);
    $response->assertSee('Warta &amp; Publikasi Resmi Sekolah', false);

    // Assert Slide 7: Agenda
    $response->assertSee('slide-7', false);
    $response->assertSee('Agenda &amp; Kalender Kegiatan Akademik', false);

    // Assert Slide 8: Guru & Staf
    $response->assertSee('slide-8', false);
    $response->assertSee('Direktori Dewan Guru &amp; Tenaga Kependidikan', false);
    $response->assertSee('staffPageIndicator', false);

    // Assert Slide 9: Perpustakaan & Literasi
    $response->assertSee('slide-9', false);
    $response->assertSee('Perpustakaan &amp; Literasi Digital', false);

    // Assert Slide 10: Bimbingan & Konseling
    $response->assertSee('slide-10', false);
    $response->assertSee('Bimbingan &amp; Konseling (BK)', false);

    // Assert Slide 11: Buku Tamu & Kunjungan Sekolah
    $response->assertSee('slide-11', false);
    $response->assertSee('Buku Tamu Kedinasan &amp; Kunjungan Sekolah', false);
});

test('kiosk data json endpoint includes news, agendas, staff directory, library, bk, and guests', function () {
    $response = $this->get(route('kiosk.data'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'daily',
        'dhuha',
        'dzuhur',
        'keputrian',
        'tahfidz',
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
        ->and($data['guests'])->toBeArray();
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
