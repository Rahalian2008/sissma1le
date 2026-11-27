<?php

use App\Models\BkTracerAlumnus;
use App\Models\User;

test('public tracer questionnaire form renders correctly with 5 sections', function () {
    $response = $this->get(route('bk.tracer.public_form'));

    $response->assertStatus(200);
    $response->assertSee('Kuesioner Tracer Study Alumni');
    $response->assertSee('Identitas &amp; Latar Belakang Alumni', false);
    $response->assertSee('Status Kegiatan Saat Ini', false);
    $response->assertSee('Transisi ke Dunia Kerja &amp; Informasi Pekerjaan', false);
    $response->assertSee('Studi Lanjut (Khusus Melanjutkan Pendidikan / Kuliah)', false);
    $response->assertSee('Relevansi Pendidikan &amp; Evaluasi Kompetensi', false);
});

test('alumni can submit complete standard Kemdikbudristek questionnaire', function () {
    $payload = [
        'full_name' => 'Budi Santoso Alumni',
        'gender' => 'L',
        'graduation_year' => '2024',
        'high_school_major' => 'MIPA (Matematika dan Ilmu Pengetahuan Alam)',
        'study_funding_source' => 'Biaya sendiri / keluarga',
        'phone' => '081234567890',
        'email' => 'budi.santoso@example.com',
        'current_city' => 'Kota Sukabumi',
        'activity_status' => 'BEKERJA',
        'work_type' => 'Full Time',
        'company_name' => 'PT Teknologi Nusantara',
        'company_type' => 'Perusahaan swasta nasional / multinasional',
        'company_location' => 'Bandung, Jawa Barat',
        'job_position' => 'Junior Web Developer',
        'work_field' => 'Teknologi Informasi',
        'monthly_income_range' => 'Rp 5.000.000 - Rp 10.000.000',
        'job_search_start' => 'Sebelum lulus',
        'job_waiting_time' => 'Kurang dari 3 bulan',
        'job_search_method' => 'Iklan lowongan online / LinkedIn',
        'job_major_relevance' => 'Sangat Erat',
        'job_education_level_match' => 'Tingkat yang sama',
        'competencies' => [
            'ethics' => 5,
            'expertise' => 4,
            'english' => 4,
            'it' => 5,
            'teamwork' => 5,
            'self_dev' => 5,
        ],
        'learning_methods' => [
            'theory' => 4,
            'practicum' => 5,
            'internship' => 4,
            'discussion' => 5,
            'project' => 5,
        ],
        'improvement_suggestions' => 'Tingkatkan sarana laboratorium komputer dan praktik pemrograman modern.',
        'bk_service_rating' => 5,
        'bk_career_guidance_impact' => 'Sangat terbantu dalam pemetaan minat karier sejak kelas XI.',
        'school_feedback' => 'Terima kasih almamater atas bimbingan para guru.',
    ];

    $response = $this->post(route('bk.tracer.public_store'), $payload);

    $response->assertRedirect(route('bk.tracer.public_success'));

    $this->assertDatabaseHas('bk_tracer_alumni', [
        'full_name' => 'Budi Santoso Alumni',
        'high_school_major' => 'MIPA (Matematika dan Ilmu Pengetahuan Alam)',
        'company_name' => 'PT Teknologi Nusantara',
        'work_type' => 'Full Time',
        'job_major_relevance' => 'Sangat Erat',
    ]);

    $saved = BkTracerAlumnus::where('full_name', 'Budi Santoso Alumni')->first();
    expect($saved)->not->toBeNull();
    expect($saved->competencies_evaluation)->toBeArray();
    expect($saved->competencies_evaluation['it'])->toBe(5);
    expect($saved->learning_methods_evaluation['practicum'])->toBe(5);
});

test('admin can view, edit, and print standard tracer records', function () {
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $alumnus = BkTracerAlumnus::create([
        'full_name' => 'Siti Rahmawati',
        'gender' => 'P',
        'graduation_year' => 2025,
        'high_school_major' => 'IPS (Ilmu Pengetahuan Sosial)',
        'study_funding_source' => 'Biaya sendiri / keluarga',
        'phone' => '08987654321',
        'email' => 'siti.rahma@example.com',
        'current_city' => 'Bandung',
        'activity_status' => 'KULIAH',
        'college_level' => 'S1',
        'campus_name' => 'Universitas Pendidikan Indonesia',
        'study_program' => 'Pendidikan Bahasa Inggris',
        'college_start_date' => 'September 2025',
        'college_funding_source' => 'Beasiswa Pemerintah (KIP-K)',
        'competencies_evaluation' => ['ethics' => 5, 'english' => 5],
        'learning_methods_evaluation' => ['theory' => 5, 'discussion' => 5],
        'improvement_suggestions' => 'Perbanyak native speaker untuk klub bahasa.',
    ]);

    $this->actingAs($admin)
        ->get(route('bk.tracer.index'))
        ->assertStatus(200)
        ->assertSee('Siti Rahmawati');

    $this->actingAs($admin)
        ->get(route('bk.tracer.show', $alumnus))
        ->assertStatus(200)
        ->assertSee('Siti Rahmawati')
        ->assertSee('Universitas Pendidikan Indonesia')
        ->assertSee('Pendidikan Bahasa Inggris')
        ->assertSee('Perbanyak native speaker');

    $this->actingAs($admin)
        ->get(route('bk.tracer.print'))
        ->assertStatus(200)
        ->assertSee('Siti Rahmawati');
});

test('artisan optimize clear web route works correctly', function () {
    $response = $this->get('/artisan-optimize-clear');
    $response->assertStatus(200);
    $response->assertSee('Sukses! Cache Bersih');
});
