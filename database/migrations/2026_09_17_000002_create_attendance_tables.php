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
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. SMA Negeri 1 Lengkong - Kampus Utama
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('radius_meters')->default(100); // e.g. 100 meter
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 50)->unique();
            $table->string('device_name');
            $table->enum('device_type', ['RFID', 'KIOSK', 'BARCODE'])->default('RFID');
            $table->string('location_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['ONLINE', 'OFFLINE'])->default('ONLINE');
            $table->timestamp('last_ping_at')->nullable();
            $table->string('secret_key', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->string('subject_or_activity'); // e.g. Matematika Wajib / Upacara Bendera
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('qr_code_token', 64)->unique();
            $table->timestamp('qr_expires_at')->nullable();
            $table->enum('status', ['ACTIVE', 'CLOSED'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('nis', 30);
            $table->string('nisn', 30);
            $table->string('student_name');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('method', ['SELFIE', 'QR_CODE', 'RFID'])->default('SELFIE');
            $table->string('selfie_path')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('gps_accuracy', 8, 2)->nullable(); // in meters
            $table->string('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA', 'DITOLAK'])->default('HADIR');
            $table->enum('verification_status', ['VALID', 'PERLU_VERIFIKASI', 'DIVERIFIKASI', 'DITOLAK'])->default('VALID');
            $table->text('notes')->nullable();
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->index(['date', 'status']);
        });

        Schema::create('attendance_selfies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('face_detected')->default(true);
            $table->decimal('liveness_score', 5, 2)->default(1.00);
            $table->json('client_metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_qr_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('scanned_at');
            $table->string('qr_token', 64);
            $table->boolean('is_valid')->default(true);
            $table->string('failure_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->string('rfid_uid', 64);
            $table->foreignId('device_id')->nullable()->constrained('attendance_devices')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamp('scanned_at');
            $table->boolean('is_valid')->default(true);
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_rfid_logs');
        Schema::dropIfExists('attendance_qr_logs');
        Schema::dropIfExists('attendance_selfies');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('attendance_devices');
        Schema::dropIfExists('attendance_locations');
    }
};
