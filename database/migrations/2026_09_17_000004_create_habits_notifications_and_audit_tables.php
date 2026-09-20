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
        // Master 7 Kebiasaan Anak Indonesia Hebat
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('order_number')->unique(); // 1 - 7
            $table->string('name'); // e.g. Bangun Pagi, Beribadah, dll.
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('target_time')->nullable(); // e.g. 04:30 - 05:30
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // Jurnal Harian 7 Kebiasaan Siswa
        Schema::create('habit_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_completed')->default(false);
            $table->time('check_time')->nullable();
            $table->string('activity_name')->nullable();
            $table->text('notes')->nullable();
            $table->text('reflection')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'habit_id', 'date']);
            $table->index(['student_id', 'date']);
        });

        // Rekapitulasi Pembiasaan (Harian, Mingguan, Bulanan, Semester)
        Schema::create('habit_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('period_type', ['DAILY', 'WEEKLY', 'MONTHLY', 'SEMESTER'])->default('WEEKLY');
            $table->string('period_key'); // e.g. 2026-W38, 2026-09, 2026-SEM1
            $table->unsignedInteger('completed_count')->default(0);
            $table->unsignedInteger('total_habits')->default(7);
            $table->decimal('score_percentage', 5, 2)->default(0.00);
            $table->enum('status', ['KONSISTEN', 'BERKEMBANG', 'PERLU_PEMBIASAAN', 'BELUM_TERPANTAU'])->default('BELUM_TERPANTAU');
            $table->timestamps();

            $table->unique(['student_id', 'period_type', 'period_key']);
        });

        // Catatan Pendampingan Guru / Wali Kelas / Orang Tua untuk 7 Kebiasaan
        Schema::create('habit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('author_role', 30);
            $table->text('comment');
            $table->timestamps();
        });

        // Notifikasi Sistem
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['ATTENDANCE', 'HABIT', 'ACHIEVEMENT', 'VIOLATION', 'GUIDANCE', 'SYSTEM'])->default('SYSTEM');
            $table->string('link_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });

        // Audit Trail Aktivitas Penting
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // LOGIN, LOGOUT, ATTENDANCE_SELFIE, INPUT_PRESTASI, VERIFIKASI_PELANGGARAN, etc.
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('habit_notes');
        Schema::dropIfExists('habit_summaries');
        Schema::dropIfExists('habit_daily_logs');
        Schema::dropIfExists('habits');
    }
};
