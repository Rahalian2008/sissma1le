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
        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('category', 100)->default('Olahraga'); // Jenis Ekstrakurikuler
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete(); // Pembina Utama
            $table->string('schedule_day', 30)->nullable(); // e.g. Jumat, Sabtu
            $table->time('start_time')->nullable(); // e.g. 15:00:00
            $table->time('end_time')->nullable(); // e.g. 17:00:00
            $table->string('location', 150)->nullable(); // e.g. Lapangan Olahraga
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('extracurricular_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('role', 50)->default('Pembina');
            $table->timestamps();

            $table->unique(['extracurricular_id', 'teacher_id'], 'extra_teacher_unique');
        });

        Schema::create('extracurricular_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('role', 50)->default('Anggota');
            $table->date('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['extracurricular_id', 'student_id'], 'extra_student_unique');
        });

        Schema::create('extracurricular_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA'])->default('HADIR');
            $table->enum('method', ['RFID', 'QR_CODE', 'SELFIE', 'MANUAL'])->default('MANUAL');
            $table->string('device_info')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('selfie_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['extracurricular_id', 'student_id', 'date'], 'extra_att_unique');
            $table->index(['extracurricular_id', 'date']);
            $table->index(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracurricular_attendances');
        Schema::dropIfExists('extracurricular_student');
        Schema::dropIfExists('extracurricular_teacher');
        Schema::dropIfExists('extracurriculars');
    }
};
