<?php

use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Services\AiStudentAnalysisService;
use Illuminate\Support\Facades\Http;

test('ai student analysis service synthesizes 5 pillars into ethical and constructive recommendations', function () {
    $student = Student::first();
    $service = app(AiStudentAnalysisService::class);

    $analysis = $service->analyzeStudent($student);

    expect($analysis)->toBeArray();
    expect($analysis)->toHaveKeys([
        'student',
        'analyzed_at',
        'ringkasan',
        'kesimpulan',
        'rekomendasi',
        'metrics',
    ]);

    // Check non-punitive, structured guidance recommendations
    expect($analysis['rekomendasi'])->toHaveKeys([
        'wali_kelas',
        'bk',
        'orang_tua',
        'siswa',
    ]);

    expect($analysis['ringkasan'])->toBeString();
    expect(strlen($analysis['ringkasan']))->toBeGreaterThan(20);
});

test('counselor, teacher, or admin can access ai analysis dashboard', function () {
    $counselor = User::where('role', 'guru')->first() ?? User::where('role', 'admin')->first();
    $student = Student::first();

    $response = $this->actingAs($counselor)->get('/ai/analysis?student_id='.$student->id);

    $response->assertStatus(200);
    $response->assertSee('AI Analisis Perkembangan');
    $response->assertSee('Rekomendasi Pendampingan');
});

test('student dossier and print-ready view render successfully for reporting', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::first();

    // 1. Dossier Web View
    $dossierResponse = $this->actingAs($admin)->get("/reports/student/{$student->id}");
    $dossierResponse->assertStatus(200);
    $dossierResponse->assertSee('DOSSIER PERKEMBANGAN SISWA TERPADU');
    $dossierResponse->assertSee($student->name);

    // 2. Print-Ready Dossier A4 View
    $printResponse = $this->actingAs($admin)->get("/reports/student/{$student->id}/print");
    $printResponse->assertStatus(200);
    $printResponse->assertSee('SMA NEGERI 1 LENGKONG');
    $printResponse->assertSee('LAPORAN PERKEMBANGAN SISWA TERPADU');
    $printResponse->assertSee('window.print()');
});

test('gemini api response is parsed properly and marked as is_ai_generated when configured', function () {
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'ringkasan' => 'Siswa menunjukkan potensi akademik luar biasa dan konsistensi tinggi dalam 7 kebiasaan.',
                                    'kesimpulan' => [
                                        ['dimension' => 'Presensi & Disiplin', 'sentiment' => 'positive', 'insight' => 'Kehadiran sempurna tanpa keterlambatan.'],
                                        ['dimension' => 'Prestasi & Bakat', 'sentiment' => 'positive', 'insight' => 'Raihan prestasi membanggakan.'],
                                        ['dimension' => '7 Kebiasaan Baik', 'sentiment' => 'positive', 'insight' => 'Pembiasaan karakter berjalan mandiri.'],
                                    ],
                                    'rekomendasi' => [
                                        'wali_kelas' => ['focus' => 'Pengembangan kepemimpinan', 'actions' => ['Beri peran ketua kelompok']],
                                        'bk' => ['focus' => 'Perencanaan karir', 'actions' => ['Bimbingan peminatan PTN']],
                                        'orang_tua' => ['focus' => 'Apresiasi rumah', 'actions' => ['Pertahankan motivasi belajar']],
                                        'siswa' => ['focus' => 'Refleksi mandiri', 'actions' => ['Tetap rendah hati dan rajin']],
                                    ],
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    SchoolSetting::set('gemini_api_key', 'test-fake-key-12345');
    SchoolSetting::set('gemini_model', 'gemini-2.5-flash');

    $student = Student::first();
    $service = app(AiStudentAnalysisService::class);
    $analysis = $service->analyzeStudent($student);

    expect($analysis['is_ai_generated'])->toBeTrue();
    expect($analysis['provider'])->toBe('Google Gemini AI');
    expect($analysis['model'])->toBe('gemini-2.5-flash');
    expect($analysis['ringkasan'])->toContain('Siswa menunjukkan potensi akademik');
    expect($analysis['kesimpulan'])->toBeArray()->toHaveCount(3);
    expect($analysis['rekomendasi'])->toHaveKeys(['wali_kelas', 'bk', 'orang_tua', 'siswa']);

    // Clean up
    SchoolSetting::set('gemini_api_key', '');
});

test('gemini api failure falls back gracefully to local pedagogical synthesizer without crashing', function () {
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Internal Server Error'],
        ], 500),
    ]);

    SchoolSetting::set('gemini_api_key', 'test-fake-key-12345');

    $student = Student::first();
    $service = app(AiStudentAnalysisService::class);
    $analysis = $service->analyzeStudent($student);

    expect($analysis['is_ai_generated'])->toBeFalse();
    expect($analysis['provider'])->toBe('Synthesizer Pedagogis Lokal (Offline)');
    expect($analysis['ringkasan'])->toBeString();
    expect($analysis['rekomendasi'])->toHaveKeys(['wali_kelas', 'bk', 'orang_tua', 'siswa']);

    // Clean up
    SchoolSetting::set('gemini_api_key', '');
});

test('admin can update gemini api key and model via master settings', function () {
    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->post('/master/settings', [
        'school_name' => 'SMA Negeri 1 Lengkong',
        'gemini_api_key' => 'AIzaSyTestKey999',
        'gemini_model' => 'gemini-1.5-flash',
    ]);

    $response->assertRedirect();
    expect(SchoolSetting::get('gemini_api_key'))->toBe('AIzaSyTestKey999');
    expect(SchoolSetting::get('gemini_model'))->toBe('gemini-1.5-flash');

    // Clean up
    SchoolSetting::set('gemini_api_key', '');
    SchoolSetting::set('gemini_model', 'gemini-2.5-flash');
});
