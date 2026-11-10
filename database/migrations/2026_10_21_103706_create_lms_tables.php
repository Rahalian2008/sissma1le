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
        // 1. Master Mata Pelajaran (Subjects)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('grade', 20)->default('Semua');
            $table->string('group', 50)->default('Umum'); // Umum, Peminatan MIPA, Peminatan IPS, Pilihan
            $table->integer('kkm')->default(75);
            $table->string('color', 50)->default('indigo');
            $table->string('icon', 50)->default('book');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Kelas Kursus / Penugasan Guru ke Mapel & Rombel (Course Classes)
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('day_of_week', 30)->nullable();
            $table->string('start_time', 10)->nullable();
            $table->string('end_time', 10)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Materi Pembelajaran (LMS Materials)
        Schema::create('lms_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('topic', 150)->default('Umum');
            $table->string('type', 50)->default('dokumen'); // dokumen, video, link, teks
            $table->longText('content')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('external_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });

        // 4. Tracking Bacaan Materi Siswa (Material Reads)
        Schema::create('lms_material_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('lms_materials')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamps();
            $table->unique(['material_id', 'student_id']);
        });

        // 5. Tugas Pembelajaran (LMS Assignments)
        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('topic', 150)->nullable();
            $table->text('instructions');
            $table->string('attachment_path', 255)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->dateTime('due_date');
            $table->integer('max_score')->default(100);
            $table->boolean('allow_late')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 6. Pengumpulan Tugas Siswa (Assignment Submissions)
        Schema::create('lms_assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('lms_assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->dateTime('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });

        // 7. Kuis & Ujian Daring (LMS Quizzes)
        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('passing_score')->default(75);
            $table->boolean('is_randomized')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 8. Butir Soal Kuis (Quiz Questions)
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type', 30)->default('multiple_choice'); // multiple_choice, essay
            $table->json('options')->nullable(); // ['A' => ..., 'B' => ..., 'C' => ..., 'D' => ..., 'E' => ...]
            $table->text('correct_answer')->nullable(); // 'A', 'B', etc. or sample essay answer
            $table->integer('score_weight')->default(10);
            $table->integer('order_number')->default(1);
            $table->timestamps();
        });

        // 9. Pengerjaan Kuis Siswa (Quiz Attempts)
        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_passed')->default(false);
            $table->json('answers')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        // 10. Sesi Presensi Pertemuan Mapel (Subject Attendances)
        Schema::create('lms_subject_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->string('title', 255);
            $table->date('meeting_date');
            $table->string('start_time', 10)->nullable();
            $table->string('end_time', 10)->nullable();
            $table->boolean('is_open')->default(true);
            $table->string('attendance_method', 30)->default('both'); // manual, selfie, both
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();
        });

        // 11. Log Catatan Kehadiran Siswa per Pertemuan Mapel (Attendance Logs)
        Schema::create('lms_subject_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('lms_subject_attendances')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('status', 20)->default('HADIR'); // HADIR, TERLAMBAT, SAKIT, IZIN, ALPA
            $table->string('method', 20)->default('MANUAL'); // MANUAL, SELFIE, QR
            $table->string('selfie_path', 255)->nullable();
            $table->dateTime('recorded_at');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->unique(['attendance_id', 'student_id']);
        });

        // 12. Forum Diskusi & Tanya Jawab Mapel (LMS Discussions)
        Schema::create('lms_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lms_discussions')->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_discussions');
        Schema::dropIfExists('lms_subject_attendance_logs');
        Schema::dropIfExists('lms_subject_attendances');
        Schema::dropIfExists('lms_quiz_attempts');
        Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_quizzes');
        Schema::dropIfExists('lms_assignment_submissions');
        Schema::dropIfExists('lms_assignments');
        Schema::dropIfExists('lms_material_reads');
        Schema::dropIfExists('lms_materials');
        Schema::dropIfExists('course_classes');
        Schema::dropIfExists('subjects');
    }
};
