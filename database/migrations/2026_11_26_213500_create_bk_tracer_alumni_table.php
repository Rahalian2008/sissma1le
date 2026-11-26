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
        if (! Schema::hasTable('bk_tracer_alumni')) {
            Schema::create('bk_tracer_alumni', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->string('full_name', 150);
                $table->string('nisn', 30)->nullable()->index();
                $table->string('nis', 30)->nullable();
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->string('graduation_year', 10)->index();
                $table->string('last_class', 50)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('current_address')->nullable();
                $table->string('current_city', 100)->nullable();

                // Status Aktivitas Utama: KULIAH, BEKERJA, WIRAUSAHA, KURSUS_PELATIHAN, MENCARI_KERJA
                $table->string('activity_status', 50)->default('KULIAH')->index();

                // Detail Kuliah
                $table->string('college_level', 30)->nullable(); // S1, D4, D3, Kedinasan
                $table->string('campus_name', 191)->nullable();
                $table->string('study_program', 191)->nullable();
                $table->string('admission_track', 50)->nullable(); // SNBP, SNBT, MANDIRI, BEASISWA, KEDINASAN
                $table->string('major_alignment', 50)->nullable(); // Sangat Selaras, Cukup Selaras, Lintas Jurusan

                // Detail Bekerja
                $table->string('company_name', 191)->nullable();
                $table->string('work_field', 100)->nullable();
                $table->string('job_position', 100)->nullable();
                $table->string('monthly_income_range', 50)->nullable();
                $table->string('job_waiting_time', 50)->nullable();

                // Detail Wirausaha
                $table->string('business_name', 191)->nullable();
                $table->string('business_field', 100)->nullable();
                $table->string('business_revenue_range', 50)->nullable();
                $table->unsignedInteger('employee_count')->nullable();

                // Evaluasi & Umpan Balik Layanan BK & Sekolah
                $table->unsignedTinyInteger('bk_service_rating')->nullable()->default(5); // 1-5 bintang
                $table->text('bk_career_guidance_impact')->nullable();
                $table->text('school_feedback')->nullable();

                $table->boolean('is_verified')->default(true);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_tracer_alumni');
    }
};
