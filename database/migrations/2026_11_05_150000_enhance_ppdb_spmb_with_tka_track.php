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
        if (Schema::hasTable('ppdb_registrations')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('ppdb_registrations', 'address')) {
                    $table->text('address')->nullable()->after('school_origin');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'distance_km')) {
                    $table->decimal('distance_km', 6, 2)->nullable()->after('address');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'coordinate_point')) {
                    $table->string('coordinate_point', 100)->nullable()->after('distance_km');
                }
                // Komponen Khusus Jalur TKA (Tes Kemampuan Akademik)
                if (! Schema::hasColumn('ppdb_registrations', 'tka_exam_number')) {
                    $table->string('tka_exam_number', 50)->nullable()->after('selection_track');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_exam_session')) {
                    $table->string('tka_exam_session', 50)->nullable()->after('tka_exam_number');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_exam_date')) {
                    $table->date('tka_exam_date')->nullable()->after('tka_exam_session');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_score_literacy')) {
                    $table->decimal('tka_score_literacy', 5, 2)->nullable()->after('tka_exam_date');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_score_numeracy')) {
                    $table->decimal('tka_score_numeracy', 5, 2)->nullable()->after('tka_score_literacy');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_score_science')) {
                    $table->decimal('tka_score_science', 5, 2)->nullable()->after('tka_score_numeracy');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_score_total')) {
                    $table->decimal('tka_score_total', 6, 2)->nullable()->after('tka_score_science');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_weighted_score')) {
                    $table->decimal('tka_weighted_score', 6, 2)->nullable()->after('tka_score_total');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_rank')) {
                    $table->integer('tka_rank')->nullable()->after('tka_weighted_score');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_status')) {
                    $table->string('tka_status', 30)->default('Belum Ujian')->after('tka_rank');
                }
                // Komponen Jalur Afirmasi, Prestasi & Mutasi Ortu
                if (! Schema::hasColumn('ppdb_registrations', 'achievement_name')) {
                    $table->string('achievement_name', 255)->nullable()->after('tka_status');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'achievement_level')) {
                    $table->string('achievement_level', 50)->nullable()->after('achievement_name');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'kip_pip_number')) {
                    $table->string('kip_pip_number', 50)->nullable()->after('achievement_level');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'parent_job')) {
                    $table->string('parent_job', 100)->nullable()->after('parent_phone');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'parent_transfer_institution')) {
                    $table->string('parent_transfer_institution', 150)->nullable()->after('parent_job');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'verified_at')) {
                    $table->dateTime('verified_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'passed_at')) {
                    $table->dateTime('passed_at')->nullable()->after('verified_by');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('passed_at');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'qr_code_token')) {
                    $table->string('qr_code_token', 64)->nullable()->after('notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ppdb_registrations')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                $columns = [
                    'address', 'distance_km', 'coordinate_point',
                    'tka_exam_number', 'tka_exam_session', 'tka_exam_date',
                    'tka_score_literacy', 'tka_score_numeracy', 'tka_score_science',
                    'tka_score_total', 'tka_weighted_score', 'tka_rank', 'tka_status',
                    'achievement_name', 'achievement_level', 'kip_pip_number',
                    'parent_job', 'parent_transfer_institution',
                    'verified_at', 'verified_by', 'passed_at', 'rejection_reason',
                    'qr_code_token',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('ppdb_registrations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
