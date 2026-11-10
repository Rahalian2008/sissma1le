<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_supervisions')) {
            Schema::create('academic_supervisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
                $table->foreignId('course_class_id')->nullable()->constrained('course_classes')->nullOnDelete();
                $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
                $table->string('academic_year', 20)->default('2026/2027');
                $table->string('semester', 10)->default('GANJIL');
                $table->date('supervision_date');
                $table->string('instrument_type', 50)->default('LENGKAP');
                $table->decimal('planning_score', 5, 2)->default(0);
                $table->decimal('execution_score', 5, 2)->default(0);
                $table->decimal('assessment_score', 5, 2)->default(0);
                $table->decimal('reflection_score', 5, 2)->default(0);
                $table->decimal('final_score', 5, 2)->default(0);
                $table->string('predicate', 20)->default('BAIK');
                $table->text('notes')->nullable();
                $table->text('recommendation')->nullable();
                $table->string('status', 20)->default('FINAL');
                $table->timestamps();

                $table->index(['teacher_id', 'academic_year', 'semester']);
                $table->index(['supervisor_id', 'supervision_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_supervisions');
    }
};
