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
        // Master Kategori Prestasi (R1 - R10)
        Schema::create('achievement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // R1, R2, etc.
            $table->string('name'); // e.g. Pengembangan Keagamaan
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Butir Master Prestasi
        Schema::create('achievement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('achievement_categories')->cascadeOnDelete();
            $table->string('code', 20); // e.g. R1.1, R3.1
            $table->string('name');
            $table->integer('default_points')->default(10);
            $table->string('level', 30)->default('SEKOLAH'); // SEKOLAH, KECAMATAN, KABUPATEN, PROVINSI, NASIONAL, INTERNASIONAL
            $table->timestamps();
        });

        // Transaksi Prestasi Siswa
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('category_id')->constrained('achievement_categories')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('achievement_items')->nullOnDelete();
            $table->string('achievement_code', 20)->nullable();
            $table->string('title');
            $table->string('level', 30)->default('SEKOLAH');
            $table->integer('points')->default(10);
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('certificate_path')->nullable();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['DRAFT', 'MENUNGGU_VERIFIKASI', 'DIVERIFIKASI', 'DITOLAK'])->default('MENUNGGU_VERIFIKASI');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });

        // Master Kategori Pelanggaran (P1 - P6)
        Schema::create('violation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // P1, P2, P3, P4, P5, P6
            $table->string('name'); // e.g. Kehadiran, Seragam, Kerapian, Kedisiplinan
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Butir Master Pelanggaran
        Schema::create('violation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('violation_categories')->cascadeOnDelete();
            $table->string('code', 20); // e.g. P1.1, P3.2
            $table->string('name');
            $table->integer('default_points')->default(5);
            $table->string('guidance_recommendation')->nullable();
            $table->timestamps();
        });

        // Transaksi Pelanggaran Siswa
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('category_id')->constrained('violation_categories')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('violation_items')->nullOnDelete();
            $table->string('violation_code', 20)->nullable();
            $table->string('title');
            $table->integer('points')->default(5);
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('location')->nullable();
            $table->text('chronology')->nullable();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('evidence_path')->nullable();
            $table->text('guidance_notes')->nullable();
            $table->enum('status', ['DRAFT', 'MENUNGGU_VERIFIKASI', 'DIVERIFIKASI', 'DITOLAK'])->default('MENUNGGU_VERIFIKASI');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });

        // Catatan Pembinaan Siswa (BK & Wali Kelas)
        Schema::create('guidance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('violation_id')->nullable()->constrained('violations')->nullOnDelete();
            $table->date('date');
            $table->enum('follow_up_type', [
                'BIMBINGAN_WALI_KELAS',
                'SP1_BK',
                'SP2_BK',
                'SP3_BK_KESISWAAN',
                'RAPAT_KHUSUS',
                'KONSELING_RUTIN',
            ])->default('BIMBINGAN_WALI_KELAS');
            $table->string('agreement_letter_path')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['DALAM_PROSES', 'SELESAI', 'PEMANTAUAN_LANJUTAN'])->default('DALAM_PROSES');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guidance_records');
        Schema::dropIfExists('violations');
        Schema::dropIfExists('violation_items');
        Schema::dropIfExists('violation_categories');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('achievement_items');
        Schema::dropIfExists('achievement_categories');
    }
};
