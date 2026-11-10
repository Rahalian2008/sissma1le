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
        // 1. Sesi Layanan Bimbingan & Konseling (POP-BK Standar)
        Schema::create('bk_counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('session_code', 30)->unique();
            $table->enum('session_type', [
                'INDIVIDUAL',
                'KELOMPOK',
                'KLASIKAL',
                'MEDIASI',
                'KONFERENSI_KASUS',
                'KOLABORASI',
                'REFERRAL',
            ])->default('INDIVIDUAL');
            $table->enum('field_of_service', [
                'PRIBADI',
                'SOSIAL',
                'BELAJAR',
                'KARIER',
            ])->default('PRIBADI');
            $table->enum('service_category', [
                'LAYANAN_DASAR',
                'LAYANAN_RESPONSIF',
                'PERENCANAAN_INDIVIDUAL',
                'DUKUNGAN_SISTEM',
            ])->default('LAYANAN_RESPONSIF');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('place')->default('Ruang Bimbingan Konseling');
            $table->string('topic');
            $table->text('problem_description');
            $table->string('counseling_approach')->nullable();
            $table->text('agreement_result')->nullable();
            $table->text('follow_up_plan')->nullable();
            $table->enum('status', [
                'TERJADWAL',
                'SEDANG_BERLANGSUNG',
                'SELESAI',
                'DIRUJUK',
            ])->default('SELESAI');
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->index(['field_of_service', 'status']);
        });

        // 2. Kunjungan Rumah (Home Visit BK)
        Schema::create('bk_home_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('homeroom_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visit_code', 30)->unique();
            $table->string('letter_number')->nullable();
            $table->date('visit_date');
            $table->text('address_visited')->nullable();
            $table->string('parent_name')->nullable();
            $table->text('case_background');
            $table->text('family_condition')->nullable();
            $table->text('parent_commitment')->nullable();
            $table->text('visit_result')->nullable();
            $table->enum('status', ['RENCANA', 'TERLAKSANA', 'BATAL'])->default('TERLAKSANA');
            $table->string('documentation_path')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'visit_date']);
        });

        // 3. Pemanggilan Orang Tua / Wali Murid
        Schema::create('bk_parent_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('call_code', 30)->unique();
            $table->string('letter_number')->nullable();
            $table->date('call_date');
            $table->string('call_time', 20)->nullable();
            $table->string('place')->default('Ruang Bimbingan Konseling SMA Negeri 1 Leles');
            $table->text('reason');
            $table->enum('status', ['SURAT_DIKIRIM', 'HADIR', 'TIDAK_HADIR', 'SELESAI'])->default('SURAT_DIKIRIM');
            $table->text('meeting_notes')->nullable();
            $table->text('parent_agreement')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'call_date']);
        });

        // 4. Sosiometri Hubungan Sosial Kelas
        Schema::create('bk_sociometries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->date('date');
            $table->string('theme')->default('Teman Belajar & Kolaborasi');
            $table->enum('status', ['DRAFT', 'AKTIF', 'SELESAI'])->default('AKTIF');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bk_sociometry_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sociometry_id')->constrained('bk_sociometries')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('first_choice_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('second_choice_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('third_choice_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('rejection_choice_id')->nullable()->constrained('students')->nullOnDelete();
            $table->text('reasons')->nullable();
            $table->timestamps();

            $table->unique(['sociometry_id', 'student_id']);
        });

        // 5. Master Instrumen Tes Psikotes
        Schema::create('bk_psychological_tests', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name');
            $table->string('category', 50); // MINAT_BAKAT, GAYA_BELAJAR, KECERDASAN_MAJEMUK, KEPRIBADIAN
            $table->text('short_desc');
            $table->text('instructions');
            $table->integer('duration_minutes')->default(25);
            $table->integer('total_questions')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Butir Soal / Asesmen Tes Psikotes
        Schema::create('bk_psychological_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('bk_psychological_tests')->cascadeOnDelete();
            $table->integer('question_number');
            $table->text('question_text');
            $table->string('dimension', 50); // R, I, A, S, E, C, VISUAL, AUDITORI, KINESTETIK, dll
            $table->string('dimension_name')->nullable();
            $table->enum('scale_type', ['LIKERT_4', 'LIKERT_5', 'CHOICE'])->default('LIKERT_4');
            $table->json('options')->nullable();
            $table->timestamps();

            $table->index(['test_id', 'question_number']);
        });

        // 7. Hasil & Laporan Tes Psikotes Siswa
        Schema::create('bk_psychological_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('bk_psychological_tests')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('taken_at');
            $table->json('answers');
            $table->json('dimension_scores');
            $table->string('dominant_dimension', 50);
            $table->string('secondary_dimension', 50)->nullable();
            $table->string('tertiary_dimension', 50)->nullable();
            $table->string('holland_code', 10)->nullable();
            $table->string('profile_label')->nullable();
            $table->text('analysis_summary');
            $table->json('career_recommendations')->nullable();
            $table->text('study_recommendations')->nullable();
            $table->text('counselor_notes')->nullable();
            $table->enum('status', ['SUBMITTED', 'VERIFIED_BY_BK'])->default('SUBMITTED');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'test_id']);
            $table->index('dominant_dimension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_psychological_test_results');
        Schema::dropIfExists('bk_psychological_test_questions');
        Schema::dropIfExists('bk_psychological_tests');
        Schema::dropIfExists('bk_sociometry_responses');
        Schema::dropIfExists('bk_sociometries');
        Schema::dropIfExists('bk_parent_calls');
        Schema::dropIfExists('bk_home_visits');
        Schema::dropIfExists('bk_counseling_sessions');
    }
};
