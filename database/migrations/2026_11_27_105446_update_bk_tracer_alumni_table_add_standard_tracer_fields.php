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
        if (Schema::hasTable('bk_tracer_alumni')) {
            Schema::table('bk_tracer_alumni', function (Blueprint $table) {
                if (! Schema::hasColumn('bk_tracer_alumni', 'high_school_major')) {
                    $table->string('high_school_major', 100)->nullable()->after('last_class');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'study_funding_source')) {
                    $table->string('study_funding_source', 100)->nullable()->after('current_city');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'work_type')) {
                    $table->string('work_type', 50)->nullable()->after('activity_status');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'college_start_date')) {
                    $table->string('college_start_date', 50)->nullable()->after('study_program');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'college_funding_source')) {
                    $table->string('college_funding_source', 100)->nullable()->after('admission_track');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'company_type')) {
                    $table->string('company_type', 100)->nullable()->after('company_name');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'company_location')) {
                    $table->string('company_location', 191)->nullable()->after('work_field');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'job_search_start')) {
                    $table->string('job_search_start', 50)->nullable()->after('job_waiting_time');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'job_search_method')) {
                    $table->string('job_search_method', 100)->nullable()->after('job_search_start');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'job_major_relevance')) {
                    $table->string('job_major_relevance', 50)->nullable()->after('employee_count');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'job_education_level_match')) {
                    $table->string('job_education_level_match', 50)->nullable()->after('job_major_relevance');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'job_mismatch_reason')) {
                    $table->string('job_mismatch_reason', 191)->nullable()->after('job_education_level_match');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'competencies_evaluation')) {
                    $table->json('competencies_evaluation')->nullable()->after('job_mismatch_reason');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'learning_methods_evaluation')) {
                    $table->json('learning_methods_evaluation')->nullable()->after('competencies_evaluation');
                }
                if (! Schema::hasColumn('bk_tracer_alumni', 'improvement_suggestions')) {
                    $table->text('improvement_suggestions')->nullable()->after('school_feedback');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bk_tracer_alumni')) {
            Schema::table('bk_tracer_alumni', function (Blueprint $table) {
                $columns = [
                    'high_school_major',
                    'study_funding_source',
                    'work_type',
                    'college_start_date',
                    'college_funding_source',
                    'company_type',
                    'company_location',
                    'job_search_start',
                    'job_search_method',
                    'job_major_relevance',
                    'job_education_level_match',
                    'job_mismatch_reason',
                    'competencies_evaluation',
                    'learning_methods_evaluation',
                    'improvement_suggestions',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('bk_tracer_alumni', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

