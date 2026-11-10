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
        Schema::create('worship_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->enum('activity_type', ['DHUHA', 'DZUHUR_JUMAT', 'KEPUTRIAN']);
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'UDZUR', 'ALPA'])->default('HADIR');
            $table->enum('method', ['RFID', 'QR_CODE', 'SELFIE', 'MANUAL'])->default('MANUAL');
            $table->string('device_info')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('selfie_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'activity_type', 'date'], 'worship_att_unique');
            $table->index(['activity_type', 'date']);
            $table->index(['class_id', 'activity_type']);
        });

        Schema::create('quran_tahfidz_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->unsignedTinyInteger('juz')->default(30);
            $table->unsignedSmallInteger('surah_number')->nullable();
            $table->string('surah_name');
            $table->string('ayat_range');
            $table->enum('makhraj', ['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'])->default('Baik');
            $table->enum('tajwid', ['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'])->default('Baik');
            $table->enum('kelancaran', ['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'])->default('Baik');
            $table->enum('overall_grade', ['Lulus', 'Mengulang'])->default('Lulus');
            $table->text('evaluation_notes')->nullable();
            $table->date('deposit_date');
            $table->time('deposit_time');
            $table->foreignId('examiner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('examiner_name');
            $table->timestamps();

            $table->index(['student_id', 'deposit_date']);
            $table->index(['class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_tahfidz_records');
        Schema::dropIfExists('worship_attendances');
    }
};
