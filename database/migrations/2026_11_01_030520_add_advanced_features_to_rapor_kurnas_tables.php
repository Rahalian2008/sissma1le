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
        // 1. Tambahan kolom pada kurnas_settings
        Schema::table('kurnas_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('kurnas_settings', 'school_stamp')) {
                $table->string('school_stamp')->nullable()->after('headmaster_signature');
            }
            if (! Schema::hasColumn('kurnas_settings', 'combine_science_social')) {
                $table->boolean('combine_science_social')->default(false)->after('provincial_logo');
            }
            if (! Schema::hasColumn('kurnas_settings', 'is_published_to_students')) {
                $table->boolean('is_published_to_students')->default(true)->after('combine_science_social');
            }
            if (! Schema::hasColumn('kurnas_settings', 'show_signature_image')) {
                $table->boolean('show_signature_image')->default(true)->after('is_published_to_students');
            }
        });

        // 2. Tabel Pengaturan Gabung Mapel (IPA & IPS) Khusus Fase E (Kelas X)
        if (! Schema::hasTable('kurnas_subject_combines')) {
            Schema::create('kurnas_subject_combines', function (Blueprint $table) {
                $table->id();
                $table->string('grade', 10)->default('X');
                $table->string('combined_name', 150); // e.g. "Ilmu Pengetahuan Alam (IPA)"
                $table->string('combined_code', 20); // e.g. "IPA"
                $table->json('source_subject_ids'); // Array of subject_id yang digabung
                $table->integer('order_number')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Tambahan kolom pada kurnas_transcripts
        Schema::table('kurnas_transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('kurnas_transcripts', 'school_exam_scores')) {
                $table->json('school_exam_scores')->nullable()->after('subject_final_scores');
            }
            if (! Schema::hasColumn('kurnas_transcripts', 'semester_1_6_scores')) {
                $table->json('semester_1_6_scores')->nullable()->after('school_exam_scores');
            }
            if (! Schema::hasColumn('kurnas_transcripts', 'is_eligible_snbp')) {
                $table->boolean('is_eligible_snbp')->default(false)->after('average_score');
            }
            if (! Schema::hasColumn('kurnas_transcripts', 'cohort_rank')) {
                $table->integer('cohort_rank')->nullable()->after('is_eligible_snbp');
            }
            if (! Schema::hasColumn('kurnas_transcripts', 'status_graduation')) {
                $table->string('status_graduation', 30)->default('LULUS')->after('cohort_rank');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kurnas_transcripts')) {
            Schema::table('kurnas_transcripts', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('kurnas_transcripts', 'school_exam_scores')) {
                    $cols[] = 'school_exam_scores';
                }
                if (Schema::hasColumn('kurnas_transcripts', 'is_eligible_snbp')) {
                    $cols[] = 'is_eligible_snbp';
                }
                if (Schema::hasColumn('kurnas_transcripts', 'cohort_rank')) {
                    $cols[] = 'cohort_rank';
                }
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        Schema::dropIfExists('kurnas_subject_combines');

        if (Schema::hasTable('kurnas_settings')) {
            Schema::table('kurnas_settings', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('kurnas_settings', 'school_stamp')) {
                    $cols[] = 'school_stamp';
                }
                if (Schema::hasColumn('kurnas_settings', 'combine_science_social')) {
                    $cols[] = 'combine_science_social';
                }
                if (Schema::hasColumn('kurnas_settings', 'is_published_to_students')) {
                    $cols[] = 'is_published_to_students';
                }
                if (Schema::hasColumn('kurnas_settings', 'show_signature_image')) {
                    $cols[] = 'show_signature_image';
                }
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
