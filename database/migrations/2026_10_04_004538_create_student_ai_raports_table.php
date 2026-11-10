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
        Schema::create('student_ai_raports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('generated_at');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('generation_type', 50)->default('MANUAL');

            // 1. Presensi Harian Keseluruhan Waktu
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->unsignedInteger('attendance_total')->default(0);
            $table->unsignedInteger('attendance_present')->default(0);
            $table->unsignedInteger('attendance_late')->default(0);
            $table->unsignedInteger('attendance_permit')->default(0);
            $table->unsignedInteger('attendance_sick')->default(0);
            $table->unsignedInteger('attendance_alpha')->default(0);

            // 2. Poin Prestasi
            $table->unsignedInteger('achievement_points')->default(0);
            $table->unsignedInteger('achievement_count')->default(0);
            $table->string('reward_tier', 100)->nullable();

            // 3. Poin Pelanggaran
            $table->unsignedInteger('violation_points')->default(0);
            $table->unsignedInteger('violation_count')->default(0);
            $table->string('guidance_tier', 100)->nullable();

            // 4. 7 Kegiatan Anak Hebat
            $table->decimal('habit_score', 5, 2)->default(0);
            $table->string('habit_status', 100)->nullable();
            $table->unsignedInteger('habit_total_logs')->default(0);
            $table->unsignedInteger('habit_completed_logs')->default(0);

            // 5. Presensi Shalat Dzuhur / Jum'at
            $table->unsignedInteger('worship_dzuhur_total')->default(0);
            $table->unsignedInteger('worship_dzuhur_hadir')->default(0);
            $table->decimal('worship_dzuhur_rate', 5, 2)->default(0);

            // 6. Presensi Keputrian (Bagi Siswi Perempuan)
            $table->unsignedInteger('worship_keputrian_total')->default(0);
            $table->unsignedInteger('worship_keputrian_hadir')->default(0);
            $table->decimal('worship_keputrian_rate', 5, 2)->default(0);
            $table->boolean('is_female')->default(false);

            // 7. Tahfidz Juz 30
            $table->unsignedInteger('tahfidz_completed_count')->default(0);
            $table->unsignedInteger('tahfidz_target_count')->default(37);
            $table->decimal('tahfidz_progress_pct', 5, 2)->default(0);
            $table->string('tahfidz_status', 100)->nullable();
            $table->decimal('avg_makhraj', 3, 1)->nullable();
            $table->decimal('avg_tajwid', 3, 1)->nullable();
            $table->decimal('avg_kelancaran', 3, 1)->nullable();

            // Indeks Perkembangan & Analisis AI
            $table->decimal('development_index', 5, 2)->default(0);
            $table->text('ringkasan_komprehensif')->nullable();
            $table->text('catatan_profil_pelajar')->nullable();
            $table->text('penilaian_siswa')->nullable();
            $table->text('perkembangan')->nullable();
            $table->text('analisis_masalah')->nullable();
            $table->text('pemecahan_masalah')->nullable();
            $table->json('rekomendasi')->nullable();
            $table->json('context_data')->nullable();
            $table->string('provider', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->boolean('is_ai_generated')->default(true);

            $table->timestamps();

            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_ai_raports');
    }
};
