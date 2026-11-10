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
        // 1. Pengaturan Utama Rapor Kurnas & Semester
        Schema::create('kurnas_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('semester', 10)->default('1'); // '1' (Ganjil) atau '2' (Genap)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false); // Status Buka / Tutup Input Nilai oleh Admin
            $table->date('report_date')->nullable(); // Tanggal pembagian rapor
            $table->string('report_place')->default('Lengkong'); // Tempat penerbitan rapor
            $table->string('headmaster_name')->nullable();
            $table->string('headmaster_nip')->nullable();
            $table->string('headmaster_signature')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('provincial_logo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Mapping & Kelompok Mata Pelajaran Rapor Kurnas (Umum, Pilihan, Muatan Lokal, Gabungan)
        Schema::create('kurnas_subject_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->enum('group_type', ['A', 'B', 'C'])->default('A'); // A: Umum, B: Pilihan, C: Muatan Lokal
            $table->string('display_name')->nullable(); // Nama yang tercetak di Rapor (default sama dengan Mapel)
            $table->integer('order_number')->default(1);
            $table->boolean('is_combined')->default(false); // Mapel gabungan IPA/IPS Kelas X
            $table->string('combine_parent_code', 20)->nullable(); // Misal 'IPA' atau 'IPS'
            $table->timestamps();
            $table->unique('subject_id');
        });

        // 3. Tujuan Pembelajaran (TP) per Mapel & Fase
        Schema::create('kurnas_learning_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('fase', 5)->default('E'); // 'E' (Kelas X) atau 'F' (Kelas XI - XII)
            $table->string('grade', 10)->default('X'); // 'X', 'XI', 'XII'
            $table->string('semester', 10)->default('1');
            $table->string('code', 20)->default('TP 1');
            $table->text('description'); // Huruf kecil awal tanpa "peserta didik dapat..."
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Nilai Akhir Mata Pelajaran & Ceklis Ketercapaian TP (Rapor Akademik)
        Schema::create('kurnas_course_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('semester', 10)->default('1');
            $table->decimal('score', 5, 2)->default(0); // Nilai 0 - 100
            $table->json('optimal_tp_ids')->nullable(); // Array of TP IDs yang tercapai optimal
            $table->json('improvement_tp_ids')->nullable(); // Array of TP IDs yang perlu peningkatan
            $table->text('optimal_description')->nullable(); // Deskripsi capaian optimal
            $table->text('improvement_description')->nullable(); // Deskripsi capaian peningkatan
            $table->text('full_description')->nullable(); // Deskripsi lengkap untuk dicetak di rapor
            $table->timestamps();

            $table->unique(['course_class_id', 'student_id', 'semester'], 'kurnas_course_scores_unique');
        });

        // 5. Penilaian Ekstrakurikuler
        Schema::create('kurnas_extracurricular_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('semester', 10)->default('1');
            $table->string('predicate', 20)->default('Baik'); // Sangat Baik, Baik, Cukup, Kurang
            $table->text('description')->nullable(); // Catatan pembina ekskul
            $table->timestamps();

            $table->unique(['student_id', 'extracurricular_id', 'semester'], 'kurnas_ekskul_scores_unique');
        });

        // 6. Data Kokurikuler / Projek Penguatan Profil Pelajar Pancasila (P5)
        Schema::create('kurnas_cocurricular_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('semester', 10)->default('1');
            $table->string('grade', 10)->default('X');
            $table->string('fase', 5)->default('E');
            $table->string('theme'); // Misal: Gaya Hidup Berkelanjutan, Bangunlah Jiwa & Raganya, dll
            $table->string('title'); // Judul Projek
            $table->text('description')->nullable(); // Deskripsi projek
            $table->json('target_dimensions')->nullable(); // Array dimensi profil lulusan yang dinilai
            $table->foreignId('coordinator_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Penilaian Kokurikuler / P5 per Siswa
        Schema::create('kurnas_cocurricular_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('kurnas_cocurricular_projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->json('dimension_scores')->nullable(); // Associative array {dimensi: MB|SB|BSH|SAB}
            $table->text('process_notes')->nullable(); // Catatan proses kokurikuler
            $table->timestamps();

            $table->unique(['project_id', 'student_id'], 'kurnas_cocurricular_scores_unique');
        });

        // 8. Kelengkapan Rapor oleh Wali Kelas (Kehadiran, Catatan Walas, Status Kenaikan/Kelulusan)
        Schema::create('kurnas_homeroom_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('semester', 10)->default('1');
            $table->integer('sick')->default(0); // Sakit
            $table->integer('permission')->default(0); // Izin
            $table->integer('absent')->default(0); // Tanpa Keterangan / Alpa
            $table->text('note')->nullable(); // Catatan motivasi wali kelas
            $table->string('status_promotion', 30)->nullable(); // 'NAIK', 'TINGGAL', 'LULUS', 'TIDAK_LULUS'
            $table->string('promotion_target_grade', 10)->nullable(); // 'XI' atau 'XII'
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'semester'], 'kurnas_homeroom_unique');
        });

        // 9. Buku Induk & Transkrip Ijazah
        Schema::create('kurnas_transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('diploma_number')->nullable(); // Nomor seri ijazah
            $table->string('transcript_number')->nullable(); // Nomor transkrip
            $table->date('graduation_date')->nullable(); // Tanggal kelulusan
            $table->json('subject_final_scores')->nullable(); // Rata-rata semester 1-6 & ujian
            $table->json('semester_1_6_scores')->nullable();
            $table->decimal('average_score', 5, 2)->nullable();
            $table->string('status_graduation', 30)->default('LULUS');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurnas_transcripts');
        Schema::dropIfExists('kurnas_homeroom_records');
        Schema::dropIfExists('kurnas_cocurricular_scores');
        Schema::dropIfExists('kurnas_cocurricular_projects');
        Schema::dropIfExists('kurnas_extracurricular_scores');
        Schema::dropIfExists('kurnas_course_scores');
        Schema::dropIfExists('kurnas_learning_goals');
        Schema::dropIfExists('kurnas_subject_groups');
        Schema::dropIfExists('kurnas_settings');
    }
};
