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
        if (Schema::hasTable('lms_materials') && ! Schema::hasColumn('lms_materials', 'learning_objective_id')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }

        if (Schema::hasTable('lms_assignments') && ! Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
            Schema::table('lms_assignments', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }

        if (Schema::hasTable('lms_quizzes') && ! Schema::hasColumn('lms_quizzes', 'learning_objective_id')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('course_class_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }

        if (Schema::hasTable('lms_quiz_questions') && ! Schema::hasColumn('lms_quiz_questions', 'learning_objective_id')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                $table->foreignId('learning_objective_id')->nullable()->after('quiz_id')->constrained('learning_objectives')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
    }
};
