<?php

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

test('phase 10 documentation and user guides exist and are complete', function () {
    $teacherGuidePath = base_path('docs/USER_GUIDE_TEACHER.md');
    $leadershipGuidePath = base_path('docs/USER_GUIDE_LEADERSHIP.md');
    $studentGuidePath = base_path('docs/USER_GUIDE_STUDENT.md');
    $deploymentGuidePath = base_path('docs/PRODUCTION_DEPLOYMENT_GUIDE.md');

    expect(File::exists($teacherGuidePath))->toBeTrue()
        ->and(File::exists($leadershipGuidePath))->toBeTrue()
        ->and(File::exists($studentGuidePath))->toBeTrue()
        ->and(File::exists($deploymentGuidePath))->toBeTrue();

    // Verifikasi konten dokumen esensial
    $teacherContent = File::get($teacherGuidePath);
    expect($teacherContent)->toContain('BUKU PANDUAN PENGGUNA: GURU PENGAMPU')
        ->toContain('Perencanaan & Perangkat Ajar')
        ->toContain('Jurnal Mengajar Guru')
        ->toContain('Bank Soal & Kisi-Kisi')
        ->toContain('Remedial & Pengayaan')
        ->toContain('Refleksi Mengajar')
        ->toContain('Portofolio Siswa');

    $leadershipContent = File::get($leadershipGuidePath);
    expect($leadershipContent)->toContain('BUKU PANDUAN PENGGUNA: PIMPINAN SEKOLAH & TIM KURIKULUM')
        ->toContain('Matriks Audit 5 Dimensi')
        ->toContain('Supervisi Akademik')
        ->toContain('Peninjauan & Pengesahan Modul Ajar');

    $studentContent = File::get($studentGuidePath);
    expect($studentContent)->toContain('BUKU PANDUAN PENGGUNA: SISWA & ORANG TUA')
        ->toContain('Ruang Kelas Pembelajaran (LMS)')
        ->toContain('Portofolio Digital & Etalase Karya Belajar Siswa')
        ->toContain('Karya Unggulan (Featured)');

    $deployContent = File::get($deploymentGuidePath);
    expect($deployContent)->toContain('PANDUAN DEPLOYMENT PRODUKSI & PEMELIHARAAN SISTEM')
        ->toContain('php artisan config:cache')
        ->toContain('php artisan route:cache')
        ->toContain('AI_ENABLE_FALLBACK=true');
});

test('lms health check verifies production readiness of curriculum system', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('lms.health'));
    $response->assertStatus(200);
    $response->assertJsonPath('status', 'healthy');
    $response->assertJsonPath('database.tables.curriculum_upgrade_ready', true);
});

test('all curriculum database tables from phase 1 to phase 9 exist in schema', function () {
    $requiredTables = [
        'learning_outcomes',
        'learning_objectives',
        'learning_objective_flows',
        'lesson_plans',
        'lesson_plan_tps',
        'teaching_journals',
        'question_banks',
        'remediation_enrichment_plans',
        'academic_supervisions',
        'student_portfolios',
        'teacher_reflections',
        'ai_generation_logs',
    ];

    foreach ($requiredTables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Tabel database {$table} wajib ada dalam skema sistem.");
    }
});
