<?php

use App\Models\AiGenerationLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Ai\AiCurriculumManager;

test('teacher and kurikulum can access ai center dashboard and test connection', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Pengguna AI SMAN 1 Lengkong',
    ]);

    // 1. Akses Dashboard AI Center
    $response = $this->actingAs($teacherUser)->get(route('lms.ai.index'));
    $response->assertStatus(200);
    $response->assertSee('Pusat Layanan AI Kurikulum');
    $response->assertSee('Generator Soal HOTS Kontekstual Lokal');

    // 2. Uji Koneksi AI Latency
    $testResponse = $this->actingAs($teacherUser)->get(route('lms.ai.test_connection'));
    $testResponse->assertStatus(200);
    $testResponse->assertJson(['status' => 'ok']);
    $testResponse->assertJsonStructure(['status', 'provider', 'is_available', 'latency_ms', 'timestamp']);
});

test('ai manager logs generations to database with accurate task types', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Biologi AI',
    ]);

    // 1. Eksekusi Generator Soal HOTS Berkonteks Lokal
    $response = $this->actingAs($teacherUser)->postJson(route('lms.ai.generate_hots'), [
        'topic' => 'Keanekaragaman Hayati dan Konservasi',
        'grade' => 'X',
        'local_context' => 'Kawasan Geopark Ciletuh Sukabumi',
    ]);
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // 2. Verifikasi Pencatatan ke ai_generation_logs
    $this->assertDatabaseHas('ai_generation_logs', [
        'user_id' => $teacherUser->id,
        'task_type' => 'hots_question',
        'status' => 'SUCCESS',
    ]);

    $log = AiGenerationLog::where('task_type', 'hots_question')->latest()->first();
    expect($log)->not->toBeNull();
    expect($log->response_time_ms)->toBeGreaterThanOrEqual(0);
});

test('teacher can generate differentiated lesson plan and diagnostic remedial', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Guru Fisika AI',
    ]);

    // 1. Generator Modul Ajar Berdiferensiasi
    $diffResponse = $this->actingAs($teacherUser)->postJson(route('lms.ai.generate_differentiated'), [
        'topic' => 'Hukum Archimedes dan Fluida Statis',
        'grade' => 'XI',
        'learning_model' => 'Problem Based Learning (PBL)',
    ]);
    $diffResponse->assertStatus(200);
    $diffResponse->assertJson(['success' => true]);

    $this->assertDatabaseHas('ai_generation_logs', [
        'user_id' => $teacherUser->id,
        'task_type' => 'diferensiasi',
        'status' => 'SUCCESS',
    ]);

    // 2. Analisis Diagnostik & Remedial
    $diagResponse = $this->actingAs($teacherUser)->postJson(route('lms.ai.analyze_diagnostic'), [
        'tp_title' => 'Menerapkan Hukum Pascal pada Sistem Hidrolik',
        'failed_aspects' => ['konversi satuan pascal', 'perhitungan perbandingan luas penampang'],
    ]);
    $diagResponse->assertStatus(200);
    $diagResponse->assertJson(['success' => true]);

    $this->assertDatabaseHas('ai_generation_logs', [
        'user_id' => $teacherUser->id,
        'task_type' => 'diagnostik',
        'status' => 'SUCCESS',
    ]);
});

test('ai privacy sanitizer masks sensitive student data like NIK and phone', function () {
    $aiManager = app(AiCurriculumManager::class);

    $rawPrompt = 'Analisis siswa bernama Budi dengan NIK 3202123456789012 dan nomor HP 081234567890 untuk remedial.';

    // Jalankan draf generasi
    $result = $aiManager->generateDraft($rawPrompt, [
        'task' => 'remedial',
        'topic' => 'Uji Privasi Data Siswa',
    ]);

    expect($result['success'])->toBeTrue();

    // Pastikan log audit yang tersimpan telah disanitasi
    $log = AiGenerationLog::where('task_type', 'remedial')->latest()->first();
    expect($log)->not->toBeNull();
    expect($log->prompt_summary)->not->toContain('3202123456789012');
    expect($log->prompt_summary)->not->toContain('081234567890');
    expect($log->prompt_summary)->toContain('[NIK_TERLINDUNG]');
    expect($log->prompt_summary)->toContain('[NOMOR_TERLINDUNG]');
});

test('unauthorized users like students cannot access ai center dashboard', function () {
    $studentUser = User::factory()->create(['role' => 'siswa']);
    $schoolClass = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-11',
        'grade' => 'X',
        'academic_year' => '2026/2027',
    ]);

    Student::create([
        'user_id' => $studentUser->id,
        'class_id' => $schoolClass->id,
        'name' => 'Siswa Non-Admin AI',
        'nisn' => '9988112233',
        'nis' => '12111',
        'gender' => 'L',
    ]);

    $response = $this->actingAs($studentUser)->get(route('lms.ai.index'));
    $response->assertStatus(403);
});
