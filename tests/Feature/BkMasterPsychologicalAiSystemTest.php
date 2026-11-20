<?php

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\BkPsychologicalTestService;

beforeEach(function () {
    $this->class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'XII-MIPA-1',
        'grade' => 'XII',
        'major' => 'MIPA',
    ]);

    $this->studentUser = User::factory()->create([
        'role' => 'siswa',
    ]);

    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'nis' => '888801',
        'nisn' => '0088880001',
        'name' => 'Siswa Master AI Test',
        'class_id' => $this->class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $this->counselorUser = User::factory()->create([
        'role' => 'guru_bk',
    ]);
});

test('submitting psikotes produces full 12-section master AI report and priority decision', function () {
    $test = BkPsychologicalTest::firstOrCreate(
        ['slug' => 'vak'],
        [
            'name' => 'Tes Gaya Belajar VAK (Visual, Auditori, Kinestetik)',
            'category' => 'GAYA_BELAJAR',
            'short_desc' => 'Asesmen modalitas sensorik siswa',
            'instructions' => 'Jawablah dengan jujur',
            'is_active' => true,
        ]
    );

    $q1 = BkPsychologicalTestQuestion::firstOrCreate(
        ['test_id' => $test->id, 'question_number' => 1],
        ['dimension' => 'VISUAL', 'question_text' => 'Saya suka bagan dan diagram.', 'options' => []]
    );
    $q2 = BkPsychologicalTestQuestion::firstOrCreate(
        ['test_id' => $test->id, 'question_number' => 2],
        ['dimension' => 'AUDITORI', 'question_text' => 'Saya suka mendengarkan penjelasan.', 'options' => []]
    );
    $q3 = BkPsychologicalTestQuestion::firstOrCreate(
        ['test_id' => $test->id, 'question_number' => 3],
        ['dimension' => 'KINESTETIK', 'question_text' => 'Saya suka praktik langsung.', 'options' => []]
    );

    $service = app(BkPsychologicalTestService::class);
    $result = $service->evaluateTest($test, $this->student, [
        $q1->id => 4,
        $q2->id => 3,
        $q3->id => 2,
    ]);

    expect($result)->toBeInstanceOf(BkPsychologicalTestResult::class);
    expect($result->dominant_dimension)->toBe('VISUAL');
    expect($result->priority_level)->not->toBeEmpty();
    expect($result->priority_label)->not->toBeEmpty();
    expect($result->ai_report)->toBeArray();

    $report = $result->ai_report;
    expect($report)->toHaveKeys([
        'ringkasan_eksekutif',
        'hasil_penilaian_keseluruhan',
        'analisis_mendalam',
        'profil_potensi',
        'kekuatan_dan_area_pengembangan',
        'rekomendasi_pendidikan',
        'rekomendasi_perguruan_tinggi_dan_karir',
        'rekomendasi_pengembangan_diri',
        'rencana_tindak_lanjut',
        'rekomendasi_pihak_sekolah',
        'kesimpulan_akhir',
        'prioritas_keputusan',
    ]);
});

test('result page renders the master AI report and priority decision badge correctly', function () {
    $test = BkPsychologicalTest::where('slug', 'vak')->first() ?? BkPsychologicalTest::create([
        'slug' => 'vak-test',
        'name' => 'Tes VAK',
        'category' => 'Gaya Belajar',
        'short_desc' => 'Desc',
        'instructions' => 'Inst',
        'is_active' => true,
    ]);

    $service = app(BkPsychologicalTestService::class);
    $result = $service->evaluateTest($test, $this->student, []);

    $response = $this->actingAs($this->studentUser)
        ->get(route('bk.psychological.result', $result));

    $response->assertStatus(200);
    $response->assertSee('Prioritas Keputusan Konseling');
    $response->assertSee('Ringkasan Eksekutif');
    $response->assertSee('Profil Potensi Siswa');
    $response->assertSee('Rencana Tindak Lanjut');
});

test('print page renders official A4 layout with letterhead and signatures', function () {
    $test = BkPsychologicalTest::where('slug', 'vak')->first() ?? BkPsychologicalTest::create([
        'slug' => 'vak-test-2',
        'name' => 'Tes VAK',
        'category' => 'Gaya Belajar',
        'short_desc' => 'Desc',
        'instructions' => 'Inst',
        'is_active' => true,
    ]);

    $service = app(BkPsychologicalTestService::class);
    $result = $service->evaluateTest($test, $this->student, []);

    $response = $this->actingAs($this->counselorUser)
        ->get(route('bk.psychological.print', $result));

    $response->assertStatus(200);
    $response->assertSee('LAPORAN ASESMEN PSIKOLOGIS');
    $response->assertSee('Koordinator BK');
    $response->assertSee('Kepala SMA Negeri 1 Lengkong');
});

test('counselor can access grand holistic 360 psychological synthesis for a student', function () {
    $response = $this->actingAs($this->counselorUser)
        ->get(route('bk.psychological.holistic', $this->student));

    $response->assertStatus(200);
    $response->assertSee('Grand Asesmen Psikologis 360°');
    $response->assertSee('Sintesis Holistik Potensi');
    $response->assertSee($this->student->name);
});

test('counselor can access grand holistic 360 official printable document with tripartite signatures', function () {
    $response = $this->actingAs($this->counselorUser)
        ->get(route('bk.psychological.holistic.print', $this->student));

    $response->assertStatus(200);
    $response->assertSee('LAPORAN KOMPREHENSIF ASESMEN PSIKOLOGIS');
    $response->assertSee('Matriks Integrasi Hasil Pengukuran Tri-Dimensi');
    $response->assertSee('Koordinator BK');
    $response->assertSee('Kepala SMA Negeri 1 Lengkong');
});
