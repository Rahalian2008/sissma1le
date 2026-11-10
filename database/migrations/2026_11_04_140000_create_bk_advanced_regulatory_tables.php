<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. AKPD (Angket Kebutuhan Peserta Didik - POP BK Kurikulum Merdeka)
        Schema::create('bk_akpds', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('grade_level', 10)->default('X'); // X, XI, XII, SEMUA
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('total_items')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bk_akpd_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akpd_id')->constrained('bk_akpds')->cascadeOnDelete();
            $table->string('code', 20)->nullable(); // P01, S01, B01, K01
            $table->enum('field_of_service', ['PRIBADI', 'SOSIAL', 'BELAJAR', 'KARIER'])->default('PRIBADI');
            $table->text('statement');
            $table->string('profile_dimension', 100)->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->index(['akpd_id', 'field_of_service']);
        });

        Schema::create('bk_akpd_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akpd_id')->constrained('bk_akpds')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->json('selected_question_ids');
            $table->json('score_per_field'); // percentage & count for Pribadi, Sosial, Belajar, Karier
            $table->string('highest_need_field', 30);
            $table->text('counselor_analysis')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['akpd_id', 'student_id']);
        });

        // 2. RPL BK 1 Lembar Kurikulum Merdeka (Rencana Pelaksanaan Layanan BK)
        Schema::create('bk_rpls', function (Blueprint $table) {
            $table->id();
            $table->string('rpl_code', 40)->unique();
            $table->string('topic');
            $table->enum('service_component', [
                'LAYANAN_DASAR',
                'LAYANAN_RESPONSIF',
                'PERENCANAAN_INDIVIDUAL',
                'DUKUNGAN_SISTEM',
            ])->default('LAYANAN_DASAR');
            $table->enum('service_type', [
                'KLASIKAL',
                'KELOMPOK',
                'INDIVIDUAL',
                'KOLABORASI',
            ])->default('KLASIKAL');
            $table->enum('field_of_service', [
                'PRIBADI',
                'SOSIAL',
                'BELAJAR',
                'KARIER',
            ])->default('PRIBADI');
            $table->string('grade_target', 30)->default('Fase E / Kelas X');
            $table->string('semester', 20)->default('Ganjil');
            $table->string('time_allocation', 50)->default('1 Pertemuan x 45 Menit');
            $table->string('profile_pelajar_pancasila')->nullable();
            $table->text('learning_objectives'); // Capaian / Tujuan Layanan
            $table->text('materials_summary')->nullable();
            $table->string('method_and_media')->nullable();
            $table->text('step_introduction')->nullable();
            $table->text('step_core')->nullable();
            $table->text('step_closing')->nullable();
            $table->text('evaluation_process')->nullable();
            $table->text('evaluation_result')->nullable();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['DRAFT', 'DISETUJUI', 'ARSIP'])->default('DISETUJUI');
            $table->timestamps();

            $table->index(['service_component', 'field_of_service']);
        });

        // 3. Konferensi Kasus (Case Conference BK)
        Schema::create('bk_case_conferences', function (Blueprint $table) {
            $table->id();
            $table->string('conference_code', 40)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('case_title');
            $table->date('conference_date');
            $table->string('conference_time', 30)->nullable();
            $table->string('place')->default('Ruang Rapat BK SMAN 1 Lengkong');
            $table->string('leader_name')->default('Kepala Sekolah / Koordinator BK');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('attendees'); // Array of {name, role, notes}
            $table->text('case_background');
            $table->text('facts_and_findings');
            $table->text('discussion_dynamics')->nullable();
            $table->text('agreed_decisions');
            $table->text('follow_up_commitments');
            $table->enum('status', ['TERJADWAL', 'TERLAKSANA', 'DITINDAKLANJUTI', 'SELESAI'])->default('TERLAKSANA');
            $table->string('minutes_file_path')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'conference_date']);
        });

        // 4. Alih Tangan Kasus (Referral / Rujukan BK)
        Schema::create('bk_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code', 40)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('referral_date');
            $table->string('target_expert_name'); // Nama Dokter/Psikolog
            $table->string('target_institution'); // RS / Lembaga Psikologi
            $table->enum('expert_category', [
                'PSIKOLOG_KLINIS',
                'PSIKIATER',
                'DOKTER_SPESIALIS',
                'DINAS_SOSIAL_PPA',
                'KEPOLISIAN_HUKUM',
                'LAINNYA',
            ])->default('PSIKOLOG_KLINIS');
            $table->text('problem_description');
            $table->text('previous_interventions');
            $table->text('referral_reason');
            $table->text('expected_support');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('headmaster_approval', ['MENUNGGU', 'DISETUJUI', 'DITOLAK'])->default('DISETUJUI');
            $table->text('expert_feedback_notes')->nullable();
            $table->enum('status', ['SURAT_DIBUAT', 'SEDANG_DIRUJUK', 'KEMBALI_KE_SEKOLAH', 'SELESAI'])->default('SEDANG_DIRUJUK');
            $table->string('referral_file_path')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'referral_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_referrals');
        Schema::dropIfExists('bk_case_conferences');
        Schema::dropIfExists('bk_rpls');
        Schema::dropIfExists('bk_akpd_responses');
        Schema::dropIfExists('bk_akpd_questions');
        Schema::dropIfExists('bk_akpds');
    }
};
