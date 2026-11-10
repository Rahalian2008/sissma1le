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
        // 1. Capaian Pembelajaran (CP)
        if (! Schema::hasTable('learning_outcomes')) {
            Schema::create('learning_outcomes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->string('phase', 5)->default('E'); // E (Kelas X), F (Kelas XI/XII)
                $table->string('grade_level', 5)->default('X'); // X, XI, XII
                $table->string('code', 50)->nullable(); // e.g. CP-INF-E-01
                $table->string('element', 100)->nullable(); // e.g. Berpikir Komputasional, Analisis Data
                $table->text('description'); // Narasi resmi Capaian Pembelajaran BSKAP
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['subject_id', 'phase', 'grade_level']);
            });
        }

        // 2. Tujuan Pembelajaran (TP)
        if (! Schema::hasTable('learning_objectives')) {
            Schema::create('learning_objectives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_outcome_id')->nullable()->constrained('learning_outcomes')->nullOnDelete();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
                $table->string('tp_code', 30); // e.g. 10.1, 10.2, 11.1
                $table->string('title', 255);
                $table->text('description');
                $table->text('competency')->nullable(); // Kompetensi yang diukur
                $table->text('scope_material')->nullable(); // Ruang lingkup materi
                $table->integer('hours_allocation')->default(2); // Alokasi JP
                $table->string('target_semester', 10)->default('GANJIL'); // GANJIL, GENAP
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['subject_id', 'target_semester']);
                $table->index(['teacher_id', 'tp_code']);
            });
        }

        // 3. Alur Tujuan Pembelajaran (ATP)
        if (! Schema::hasTable('learning_objective_flows')) {
            Schema::create('learning_objective_flows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
                $table->string('title', 255);
                $table->string('phase', 5)->default('E');
                $table->string('grade_level', 5)->default('X');
                $table->json('flow_order')->nullable(); // Array urutan id TP / kode TP
                $table->text('rationale')->nullable(); // Rasionalisasi pengurutan alur
                $table->boolean('is_approved')->default(false);
                $table->timestamps();

                $table->index(['subject_id', 'academic_year_id']);
            });
        }

        // 4. Program Tahunan & Program Semester (Prota & Promes)
        if (! Schema::hasTable('annual_programs')) {
            Schema::create('annual_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
                $table->string('type', 20)->default('PROMES'); // PROTA, PROMES
                $table->string('semester', 15)->default('GANJIL'); // GANJIL, GENAP, TAHUNAN
                $table->json('distribution_data')->nullable(); // Pemetaan per minggu/bulan & kalender
                $table->integer('total_hours')->default(0);
                $table->string('status', 20)->default('DRAFT'); // DRAFT, SUBMITTED, APPROVED
                $table->timestamps();

                $table->index(['course_class_id', 'type', 'semester']);
            });
        }

        // 5. Modul Ajar / RPP Digital
        if (! Schema::hasTable('lesson_plans')) {
            Schema::create('lesson_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->string('title', 255);
                $table->integer('meeting_number')->default(1);
                $table->integer('time_allocation_minutes')->default(90);
                $table->string('grade_level', 5)->default('X');
                $table->string('semester', 10)->default('GANJIL');
                $table->json('target_profile')->nullable(); // Dimensi Profil Pelajar Pancasila
                $table->string('learning_model', 100)->nullable(); // PBL, PJBL, Discovery, dll.
                $table->json('learning_methods')->nullable(); // Diskusi, Praktik, Tanya Jawab
                $table->text('media_and_resources')->nullable();
                $table->json('learning_steps')->nullable(); // introduction, core, closing
                $table->json('assessment_plan')->nullable(); // diagnostic, formative, summative
                $table->longText('lkpd_content')->nullable();
                $table->string('file_path')->nullable();
                $table->string('status', 20)->default('DRAFT'); // DRAFT, SUBMITTED, REVISED, APPROVED
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_notes')->nullable();
                $table->boolean('is_published')->default(false);
                $table->integer('version')->default(1);
                $table->timestamps();

                $table->index(['course_class_id', 'meeting_number']);
                $table->index(['teacher_id', 'status']);
            });
        }

        // 6. Pivot Relasi Modul Ajar dengan TP
        if (! Schema::hasTable('lesson_plan_tps')) {
            Schema::create('lesson_plan_tps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_plan_id')->constrained('lesson_plans')->cascadeOnDelete();
                $table->foreignId('learning_objective_id')->constrained('learning_objectives')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['lesson_plan_id', 'learning_objective_id'], 'lp_tp_unique');
            });
        }

        // 7. Refleksi Pembelajaran Guru Pasca KBM
        if (! Schema::hasTable('teacher_reflections')) {
            Schema::create('teacher_reflections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->foreignId('lesson_plan_id')->nullable()->constrained('lesson_plans')->nullOnDelete();
                $table->date('reflection_date');
                $table->text('success_notes'); // Catatan apa yang berjalan baik
                $table->text('challenges_notes'); // Kendala / hambatan di kelas
                $table->text('follow_up_action'); // Rencana perbaikan KBM berikutnya
                $table->string('status', 20)->default('SELESAI'); // DRAFT, SELESAI
                $table->timestamps();

                $table->index(['course_class_id', 'reflection_date']);
            });
        }

        // 8. Rencana Tindak Lanjut: Remedial dan Pengayaan
        if (! Schema::hasTable('remediation_enrichment_plans')) {
            Schema::create('remediation_enrichment_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->foreignId('learning_objective_id')->nullable()->constrained('learning_objectives')->nullOnDelete();
                $table->string('type', 20)->default('REMEDIAL'); // REMEDIAL, PENGAYAAN
                $table->string('topic', 255);
                $table->text('description');
                $table->text('remedial_method')->nullable();
                $table->json('student_ids')->nullable(); // Daftar ID siswa binaan
                $table->date('scheduled_date')->nullable();
                $table->string('status', 20)->default('DRAFT'); // DRAFT, BERJALAN, SELESAI
                $table->timestamps();

                $table->index(['course_class_id', 'type', 'status']);
            });
        }

        // 9. Portofolio Digital Siswa
        if (! Schema::hasTable('student_portfolios')) {
            Schema::create('student_portfolios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('course_class_id')->nullable()->constrained('course_classes')->nullOnDelete();
                $table->foreignId('learning_objective_id')->nullable()->constrained('learning_objectives')->nullOnDelete();
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->string('category', 50)->default('Tugas Terbaik'); // Tugas Terbaik, Proyek P5, Prestasi Belajar, dll.
                $table->string('file_path')->nullable();
                $table->string('external_url', 500)->nullable();
                $table->date('artifact_date')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['student_id', 'category']);
            });
        }

        // 10. Tambah kolom relasi learning_objective_id pada lms_materials & lms_assignments jika belum ada
        if (Schema::hasTable('lms_materials')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                if (! Schema::hasColumn('lms_materials', 'learning_objective_id')) {
                    $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('lms_assignments')) {
            Schema::table('lms_assignments', function (Blueprint $table) {
                if (! Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
                    $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lms_assignments') && Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
            Schema::table('lms_assignments', function (Blueprint $table) {
                $table->dropForeign(['learning_objective_id']);
                $table->dropColumn('learning_objective_id');
            });
        }

        if (Schema::hasTable('lms_materials') && Schema::hasColumn('lms_materials', 'learning_objective_id')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                $table->dropForeign(['learning_objective_id']);
                $table->dropColumn('learning_objective_id');
            });
        }

        Schema::dropIfExists('student_portfolios');
        Schema::dropIfExists('remediation_enrichment_plans');
        Schema::dropIfExists('teacher_reflections');
        Schema::dropIfExists('lesson_plan_tps');
        Schema::dropIfExists('lesson_plans');
        Schema::dropIfExists('annual_programs');
        Schema::dropIfExists('learning_objective_flows');
        Schema::dropIfExists('learning_objectives');
        Schema::dropIfExists('learning_outcomes');
    }
};
