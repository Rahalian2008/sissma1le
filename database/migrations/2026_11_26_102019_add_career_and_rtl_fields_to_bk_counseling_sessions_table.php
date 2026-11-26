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
        Schema::table('bk_counseling_sessions', function (Blueprint $table) {
            $table->string('career_path', 50)->nullable()->after('field_of_service');
            $table->string('study_admission_track', 50)->nullable()->after('career_path');
            $table->string('campus_name', 191)->nullable()->after('study_admission_track');
            $table->string('study_program', 191)->nullable()->after('campus_name');
            $table->text('campus_choices')->nullable()->after('study_program');
            $table->string('work_field', 191)->nullable()->after('campus_choices');
            $table->date('follow_up_date')->nullable()->after('follow_up_plan');
            $table->string('follow_up_status', 30)->default('PENDING')->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bk_counseling_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'career_path',
                'study_admission_track',
                'campus_name',
                'study_program',
                'campus_choices',
                'work_field',
                'follow_up_date',
                'follow_up_status',
            ]);
        });
    }
};
