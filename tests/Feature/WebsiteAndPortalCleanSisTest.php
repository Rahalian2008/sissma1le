<?php

use App\Models\SchoolSetting;

beforeEach(function () {
    SchoolSetting::set('school_name', 'SMA Negeri 1 Lengkong');
    SchoolSetting::set('app_name', 'SIS-SMA1LE');
});

test('public website home page loads successfully without sikatas references', function () {
    $response = $this->get(route('website.home'));

    $response->assertStatus(200);
    $response->assertSee('SIS-SMA1LE');
    $response->assertSee('Portal Orang Tua');
    $response->assertDontSee('SIKATAS');
    $response->assertDontSee('sassma1le.test');
});

test('portal selection page displays SIS only and no sikatas', function () {
    $response = $this->get(route('portal.select'));

    $response->assertStatus(200);
    $response->assertSee('Portal Ekosistem SIS-SMA1LE');
    $response->assertSee('Masuk Portal SIS-SMA1LE');
    $response->assertSee('Portal Orang Tua Siswa');
    $response->assertDontSee('SIKATAS');
    $response->assertDontSee('sassma1le.test');
});

test('website home page removes gambar 1 hero cta group and gambar 2 spmb banner section', function () {
    $response = $this->get(route('website.home'));

    $response->assertStatus(200);
    // Gambar 2 section removed
    $response->assertDontSee('id="spmb-section"', false);
    $response->assertDontSee('Daftar SPMB Sekarang', false);

    // Gambar 1 hero CTA button group removed
    $response->assertDontSee('Jelajahi Ekosistem ↓', false);
});

test('website home page displays all 12 sections in the exact specified user sequence', function () {
    $response = $this->get(route('website.home'));
    $html = $response->getContent();

    $expectedSectionsInOrder = [
        'id="sambutan"',
        'id="profil"',
        'id="berita"',
        'id="guru-staf"',
        'id="fasilitas"',
        'id="agenda"',
        'id="ekstrakurikuler"',
        'id="pembiasaan"',
        'id="prestasi"',
        'id="ekosistem-sis"',
        'id="layanan-peran"',
        'id="kontak"',
    ];

    $lastPos = 0;
    foreach ($expectedSectionsInOrder as $section) {
        $pos = strpos($html, $section);
        expect($pos)->not->toBeFalse("Expected section {$section} to be present in HTML");
        expect($pos)->toBeGreaterThan($lastPos, "Expected {$section} to appear after previous section in ordered flow");
        $lastPos = $pos;
    }
});

test('website navigation drawer and footer reflect the exact 12-item ordered menu', function () {
    $response = $this->get(route('website.home'));

    // Verify drawer and footer have all 12 ordered items
    $response->assertSee('Sambutan Kepala Sekolah');
    $response->assertSee('Profil Lembaga');
    $response->assertSee('Warta Sekolah');
    $response->assertSee('Tenaga Pendidik & Kependidikan', false);
    $response->assertSee('Sarana & Lingkungan Belajar', false);
    $response->assertSee('Kalender Akademik');
    $response->assertSee('Minat, Bakat & Kepemimpinan', false);
    $response->assertSee('Pembiasaan Positif Harian Siswa');
    $response->assertSee('Klasemen & Torehan Prestasi Siswa', false);
    $response->assertSee('Transformasi Digital Terpadu');
    $response->assertSee('Akses Fleksibel Berdasarkan Kebutuhan');
    $response->assertSee('Informasi & Layanan Publik', false);
});

test('website top announcement bar renders mobile-friendly text and theme-color meta', function () {
    $response = $this->get(route('website.home'));

    $response->assertStatus(200);
    $response->assertSee('name="theme-color"', false);
    $response->assertSee('viewport-fit=cover', false);
    $response->assertSee('Terakreditasi A • NPSN:', false);
});
