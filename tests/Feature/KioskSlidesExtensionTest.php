<?php

test('kiosk index renders slides for berita, agenda, and guru & staf with photos and names', function () {
    $response = $this->get(route('kiosk.index'));

    $response->assertStatus(200);

    // Assert Slide Navigation Pills 1 through 9
    $response->assertSee('pill-slide-0', false);
    $response->assertSee('pill-slide-5', false);
    $response->assertSee('pill-slide-6', false);
    $response->assertSee('pill-slide-7', false);
    $response->assertSee('pill-slide-8', false);

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
});

test('kiosk data json endpoint includes news, agendas, and staff directory', function () {
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
        'ticker',
    ]);

    $data = $response->json();
    expect($data['news'])->toBeArray()
        ->and($data['agendas'])->toBeArray()
        ->and($data['staff'])->toBeArray()
        ->and(count($data['staff']))->toBeGreaterThanOrEqual(1);
});
