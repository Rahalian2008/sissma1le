<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('class_learning_agendas')) {
            Schema::create('class_learning_agendas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->string('rombel', 50);
                $table->date('date');
                $table->string('time_slot', 50)->default('07:15 - 08:35');
                $table->string('subject', 100);
                $table->foreignId('teacher_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('teacher_name', 150)->nullable();
                $table->text('learning_topic');
                $table->text('activities_summary')->nullable();
                $table->integer('students_present')->default(0);
                $table->integer('students_sick')->default(0);
                $table->integer('students_permitted')->default(0);
                $table->integer('students_absent')->default(0);
                $table->text('absent_notes')->nullable();
                $table->text('class_notes')->nullable();
                $table->boolean('teacher_signed')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_learning_agendas');
    }
};
