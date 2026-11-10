<?php

use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsQuiz;
use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'SchoolMasterSeeder']);
    $this->artisan('db:seed', ['--class' => 'LmsSeeder']);
});

test('teacher can generate AI interactive material with Elemen, CP, TP, ATP, Materi, and Target Fase form fields', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->post(route('lms.materials.ai_generate', $courseClass->id), [
        'materi' => 'Struktur dan Fungsi Sel Surya Fotosintesis',
        'target_fase' => 'Fase F (Kelas XI - XII SMA)',
        'elemen' => 'Pemahaman Sains dan Keterampilan Proses',
        'capaian_pembelajaran' => 'Peserta didik menganalisis keterkaitan antara struktur sel dan proses transformasi energi.',
        'tujuan_pembelajaran' => 'Mengidentifikasi organel kloroplas dan tahapan reaksi fotosintesis pada tumbuhan hijau.',
        'alur_tujuan_pembelajaran' => 'ATP-01: Struktur Kloroplas -> ATP-02: Reaksi Terang -> ATP-03: Reaksi Gelap',
        'content_type' => 'text',
        'auto_publish' => 1,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $material = LmsMaterial::where('course_class_id', $courseClass->id)
        ->where('topic', 'Struktur dan Fungsi Sel Surya Fotosintesis')
        ->latest('id')
        ->first();

    expect($material)->not->toBeNull()
        ->and($material->type)->toBe('text');

    $content = json_decode($material->content, true);
    expect($content)->toBeArray()
        ->and($content['elemen'])->toBe('Pemahaman Sains dan Keterampilan Proses')
        ->and($content['target_fase'])->toBe('Fase F (Kelas XI - XII SMA)')
        ->and($content['materi'])->toBe('Struktur dan Fungsi Sel Surya Fotosintesis')
        ->and($content['capaian_pembelajaran'])->toContain('keterkaitan antara struktur sel')
        ->and($content['alur_tujuan_pembelajaran'])->toContain('ATP-01')
        ->and($content['uraian_materi'])->toBeArray()
        ->and($content['kartu_konsep'])->toBeArray()
        ->and($content['cek_pemahaman'])->toBeArray();

    // Verify detail page displays curriculum elements
    $showResponse = $this->actingAs($teacher)->get(route('lms.materials.show', [
        'courseClass' => $courseClass->id,
        'material' => $material->id,
    ]));

    $showResponse->assertStatus(200);
    $showResponse->assertSee('Pemahaman Sains dan Keterampilan Proses');
    $showResponse->assertSee('Fase F (Kelas XI - XII SMA)');
    $showResponse->assertSee('Alur Tujuan Pembelajaran (ATP)');
    $showResponse->assertSee('ATP-01');
});

test('teacher can generate AI assignment with Elemen, CP, TP, ATP, Materi, and Target Fase form fields', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->post(route('lms.assignments.ai_generate', $courseClass->id), [
        'materi' => 'Analisis Neraca Pembayaran Internasional',
        'target_fase' => 'Fase F (Kelas XI - XII SMA)',
        'elemen' => 'Ekonomi Internasional dan Kebijakan Fiskal',
        'capaian_pembelajaran' => 'Peserta didik mengevaluasi dampak neraca perdagangan terhadap devisa negara.',
        'tujuan_pembelajaran' => 'Menyusun laporan telaah neraca pembayaran Indonesia tahun berjalan.',
        'alur_tujuan_pembelajaran' => 'ATP-EK-03: Analisis Komponen Neraca Berjalan dan Finansial',
        'task_type' => 'tugas_autentik',
        'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $assignment = LmsAssignment::where('course_class_id', $courseClass->id)
        ->where('topic', 'Analisis Neraca Pembayaran Internasional')
        ->latest('id')
        ->first();

    expect($assignment)->not->toBeNull()
        ->and($assignment->topic)->toBe('Analisis Neraca Pembayaran Internasional')
        ->and($assignment->instructions)->toContain('Ekonomi Internasional dan Kebijakan Fiskal')
        ->and($assignment->instructions)->toContain('Fase F')
        ->and($assignment->instructions)->toContain('ATP-EK-03')
        ->and($assignment->instructions)->toContain('KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN (KKTP)');
});

test('teacher can generate AI CBT package with Elemen, CP, TP, ATP, Materi, and Target Fase form fields', function () {
    $courseClass = CourseClass::first();
    $teacher = User::where('role', 'guru')->first();

    $response = $this->actingAs($teacher)->post(route('lms.quizzes.ai_generate', $courseClass->id), [
        'materi' => 'Dinamika Rotasi dan Momen Inersia Benda Tegar',
        'target_fase' => 'Fase F (Kelas XI - XII SMA)',
        'elemen' => 'Mekanika Klasik dan Keterampilan Proses Sains',
        'capaian_pembelajaran' => 'Peserta didik menerapkan prinsip dinamika rotasi dalam pemecahan masalah teknologi.',
        'tujuan_pembelajaran' => 'Menghitung percepatan sudut silinder pejal pada bidang miring.',
        'alur_tujuan_pembelajaran' => 'ATP-FIS-04: Momen Gaya -> Momen Inersia -> Hukum Kekekalan Momentum Sudut',
        'quiz_type' => 'formatif',
        'question_count' => 3,
        'duration_minutes' => 60,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $quiz = LmsQuiz::where('course_class_id', $courseClass->id)
        ->where('topic', 'Dinamika Rotasi dan Momen Inersia Benda Tegar')
        ->with('questions')
        ->latest('id')
        ->first();

    expect($quiz)->not->toBeNull()
        ->and($quiz->quiz_type)->toBe('formatif')
        ->and($quiz->instructions)->toContain('Fase F')
        ->and($quiz->questions)->toHaveCount(3);

    $firstQuestion = $quiz->questions->first();
    expect($firstQuestion->indikator_soal)->toContain('Menghitung percepatan sudut silinder pejal')
        ->and($firstQuestion->pembahasan)->toContain('Mekanika Klasik');
});
