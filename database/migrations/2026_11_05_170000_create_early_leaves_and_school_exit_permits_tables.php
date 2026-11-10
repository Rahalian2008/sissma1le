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
        // 1. Tabel Ajuan Pulang Cepat & Surat Keterangan Pulang Cepat
        if (! Schema::hasTable('early_leave_requests')) {
            Schema::create('early_leave_requests', function (Blueprint $table) {
                $table->id();
                $table->string('letter_number', 100)->unique();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
                $table->date('date');
                $table->time('leave_time');
                $table->string('reason_category', 100)->default('Sakit di Sekolah (UKS)');
                $table->text('reason');
                $table->string('pickup_name', 150)->nullable();
                $table->string('pickup_relationship', 100)->nullable();
                $table->string('pickup_phone', 50)->nullable();
                $table->string('attachment_path', 255)->nullable();
                $table->string('status', 30)->default('PENDING'); // PENDING, APPROVED, REJECTED
                $table->string('requester_type', 30)->default('SISWA'); // SISWA, WALI_KELAS, GURU_PIKET, TU_PIKET
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('approval_notes')->nullable();
                $table->string('qr_code_token', 100)->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabel Surat Izin Keluar Lingkungan Sekolah (Sementara / Dinas)
        if (! Schema::hasTable('school_exit_permits')) {
            Schema::create('school_exit_permits', function (Blueprint $table) {
                $table->id();
                $table->string('permit_number', 100)->unique();
                $table->date('date');
                $table->time('out_time');
                $table->time('expected_back_time')->nullable();
                $table->time('actual_back_time')->nullable();
                $table->string('purpose', 150); // Keperluan: Lomba, Tugas OSIS, Medis/UKS, Riset/Tugas Belajar, Urusan Mendesak
                $table->string('destination', 200)->nullable(); // Tujuan/Lokasi luar sekolah
                $table->text('reason');
                $table->string('accompanied_by', 150)->nullable(); // Guru Pendamping / Wali
                $table->boolean('is_returning')->default(true); // Kembali lagi ke sekolah atau langsung pulang
                $table->string('status', 30)->default('PENDING'); // PENDING, APPROVED, REJECTED, RETURNED
                $table->string('requester_type', 30)->default('SISWA'); // SISWA, WALI_KELAS, GURU_PIKET, TU_PIKET
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('approval_notes')->nullable();
                $table->dateTime('security_checked_out_at')->nullable();
                $table->dateTime('security_checked_in_at')->nullable();
                $table->string('qr_code_token', 100)->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Pivot Daftar Siswa dalam Izin Keluar Lingkungan Sekolah (Mendukung > 1 Siswa / Rombongan)
        if (! Schema::hasTable('school_exit_permit_students')) {
            Schema::create('school_exit_permit_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_exit_permit_id')->constrained('school_exit_permits')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('notes', 200)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_exit_permit_students');
        Schema::dropIfExists('school_exit_permits');
        Schema::dropIfExists('early_leave_requests');
    }
};
