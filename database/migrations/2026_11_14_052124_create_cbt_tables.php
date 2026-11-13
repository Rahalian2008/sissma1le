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
        // 1. Paket & Jadwal Ujian Online (CBT Exam)
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('exam_code', 50)->unique();
            $table->string('exam_type', 50)->default('PAS'); // UH, PTS, PAS, PAT, US, SIMULASI, ANBK, DIAGNOSTIK
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->unsignedBigInteger('teacher_id')->nullable()->index();
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            $table->integer('duration_minutes')->default(90);
            $table->string('token', 20)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_result_to_student')->default(false);

            // Keamanan & Anti-Cheat
            $table->boolean('enable_security_block')->default(true); // Auto-block jika switch tab/minimize
            $table->integer('max_violations')->default(3); // Batas pelanggaran sebelum blokir

            // Lock Lokasi (Geofencing)
            $table->boolean('enable_geofence')->default(true); // Lock lokasi radius sekolah
            $table->decimal('geofence_latitude', 10, 8)->nullable();
            $table->decimal('geofence_longitude', 11, 8)->nullable();
            $table->integer('geofence_radius')->default(100); // Radius dalam meter

            $table->decimal('pass_percentage', 5, 2)->default(75.00); // KKM / Kelulusan
            $table->string('status', 30)->default('published')->index(); // draft, published, ongoing, archived
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Rombel / Kelas Peserta Ujian
        Schema::create('cbt_exam_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cbt_exam_id')->index();
            $table->unsignedBigInteger('school_class_id')->index();
            $table->timestamps();
        });

        // 3. Butir Soal Ujian (Pilihan Ganda, PG Kompleks, B-S, Menjodohkan, Isian Singkat, Uraian)
        Schema::create('cbt_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cbt_exam_id')->index();
            $table->unsignedBigInteger('question_bank_id')->nullable()->index();
            $table->integer('question_number')->default(1);
            $table->string('question_type', 50)->default('multiple_choice');
            // multiple_choice, complex_multiple_choice, true_false, matching, short_answer, essay

            $table->longText('stimulus')->nullable(); // Wacana, bacaan, stimulus, atau media
            $table->longText('question_text');
            $table->json('options')->nullable(); // Pilihan jawaban (A-E), opsi pernyataan, atau pasangan menjodohkan
            $table->json('correct_answer')->nullable(); // Kunci jawaban
            $table->text('explanation')->nullable(); // Pembahasan
            $table->decimal('score_weight', 6, 2)->default(1.00); // Bobot skor

            // Metadata Kisi-Kisi & Kartu Soal Standar Kemdikbud
            $table->text('learning_objective')->nullable(); // Capaian Pembelajaran / KD
            $table->text('indicator')->nullable(); // Indikator Soal
            $table->string('cognitive_level', 20)->default('L2'); // L1 (C1-C2), L2 (C3), L3 (HOTS / C4-C6)
            $table->string('difficulty_level', 20)->default('sedang'); // mudah, sedang, sukar
            $table->timestamps();
        });

        // 4. Sesi Ujian Siswa (Lembar Kerja CBT Siswa)
        Schema::create('cbt_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cbt_exam_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->string('session_token', 64)->nullable()->index();
            $table->string('cbt_username', 100)->nullable()->index(); // Akun CBT di Kartu Peserta
            $table->string('cbt_password', 100)->nullable(); // PIN Login CBT

            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('remaining_seconds')->nullable();
            $table->string('status', 30)->default('not_started')->index(); // not_started, in_progress, submitted, blocked, timed_out

            // Keamanan & Pelanggaran
            $table->integer('violation_count')->default(0);
            $table->boolean('is_blocked')->default(false);
            $table->text('blocked_reason')->nullable();
            $table->dateTime('blocked_at')->nullable();
            $table->dateTime('unblocked_at')->nullable();
            $table->unsignedBigInteger('unblocked_by')->nullable();

            // Geofence & Perangkat
            $table->decimal('student_latitude', 10, 8)->nullable();
            $table->decimal('student_longitude', 11, 8)->nullable();
            $table->decimal('student_distance_meters', 8, 2)->nullable();
            $table->string('student_ip', 45)->nullable();
            $table->text('student_user_agent')->nullable();

            // Penilaian
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('max_possible_score', 8, 2)->default(0);
            $table->decimal('final_grade', 6, 2)->default(0); // Nilai akhir 0-100
            $table->boolean('is_graded')->default(false);
            $table->dateTime('graded_at')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamps();
        });

        // 5. Lembar Jawaban Siswa per Butir Soal
        Schema::create('cbt_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cbt_session_id')->index();
            $table->unsignedBigInteger('cbt_exam_question_id')->index();
            $table->json('answer')->nullable(); // Jawaban siswa (single choice, array choice, B-S map, matching pairs, text)
            $table->boolean('is_doubtful')->default(false); // Flag Ragu-ragu
            $table->boolean('is_correct')->nullable(); // Hasil koreksi otomatis (null jika belum dikoreksi/essay)
            $table->decimal('score_earned', 6, 2)->default(0);
            $table->text('teacher_feedback')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();
        });

        // 6. Log Pelanggaran Siswa (Tab Switch, Minimize, Fullscreen Exit)
        Schema::create('cbt_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cbt_session_id')->index();
            $table->string('violation_type', 50); // tab_switch, window_blur, exit_fullscreen, app_minimize, location_out_of_range
            $table->text('description')->nullable();
            $table->dateTime('violation_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_violations');
        Schema::dropIfExists('cbt_answers');
        Schema::dropIfExists('cbt_sessions');
        Schema::dropIfExists('cbt_exam_questions');
        Schema::dropIfExists('cbt_exam_classes');
        Schema::dropIfExists('cbt_exams');
    }
};
