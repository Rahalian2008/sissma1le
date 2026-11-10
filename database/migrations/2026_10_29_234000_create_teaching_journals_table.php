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
        if (! Schema::hasTable('teaching_journals')) {
            Schema::create('teaching_journals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->foreignId('lesson_plan_id')->nullable()->constrained('lesson_plans')->nullOnDelete();
                $table->foreignId('learning_objective_id')->nullable()->constrained('learning_objectives')->nullOnDelete();
                $table->foreignId('subject_attendance_id')->nullable()->constrained('lms_subject_attendances')->nullOnDelete();

                $table->date('date');
                $table->unsignedInteger('meeting_number')->default(1);
                $table->string('time_slot', 100)->nullable();
                $table->string('topic', 255);
                $table->text('teaching_activities');
                $table->json('student_attendance_summary')->nullable();
                $table->string('learning_achievement_status', 30)->default('TERCAPAI'); // TERCAPAI, SEBAGIAN_TERCAPAI, BELUM_TERCAPAI
                $table->text('classroom_dynamics_notes')->nullable();
                $table->text('follow_up_action')->nullable();

                // Verifikasi Supervisi Kurikulum / Pimpinan
                $table->boolean('is_verified_by_kurikulum')->default(false);
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('verification_notes')->nullable();

                $table->timestamps();

                $table->index(['course_class_id', 'date']);
                $table->index(['teacher_id', 'date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};
