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
        // 1. Tambah learning_objective_id pada lms_quizzes jika belum ada
        if (Schema::hasTable('lms_quizzes') && ! Schema::hasColumn('lms_quizzes', 'learning_objective_id')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }

        // 2. Tambah learning_objective_id pada lms_quiz_questions jika belum ada
        if (Schema::hasTable('lms_quiz_questions') && ! Schema::hasColumn('lms_quiz_questions', 'learning_objective_id')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('quiz_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }

        // 3. Tabel Bank Soal Mata Pelajaran (Question Bank)
        if (! Schema::hasTable('question_banks')) {
            Schema::create('question_banks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
                $table->foreignId('learning_objective_id')->nullable()->constrained('learning_objectives')->nullOnDelete();

                $table->string('grade_level', 10)->default('X'); // X, XI, XII
                $table->string('topic', 255);
                $table->text('question_text');
                $table->text('stimulus')->nullable();
                $table->string('question_type', 30)->default('multiple_choice'); // multiple_choice, essay
                $table->string('cognitive_level', 30)->default('L2'); // L1 (Pemahaman), L2 (Aplikasi), L3 (Penalaran/HOTS)
                $table->text('indicator')->nullable(); // Indikator Butir Soal untuk Kisi-Kisi
                $table->json('options')->nullable(); // [A, B, C, D, E]
                $table->text('correct_answer')->nullable();
                $table->text('explanation')->nullable();
                $table->unsignedInteger('score_weight')->default(1);
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                $table->index(['subject_id', 'grade_level']);
                $table->index(['learning_objective_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_banks');

        if (Schema::hasTable('lms_quiz_questions') && Schema::hasColumn('lms_quiz_questions', 'learning_objective_id')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                $table->dropForeign(['learning_objective_id']);
                $table->dropColumn('learning_objective_id');
            });
        }

        if (Schema::hasTable('lms_quizzes') && Schema::hasColumn('lms_quizzes', 'learning_objective_id')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                $table->dropForeign(['learning_objective_id']);
                $table->dropColumn('learning_objective_id');
            });
        }
    }
};
