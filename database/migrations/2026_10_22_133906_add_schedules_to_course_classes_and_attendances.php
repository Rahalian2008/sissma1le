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
        Schema::table('course_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('course_classes', 'schedules')) {
                $table->json('schedules')->nullable()->after('end_time');
            }
        });

        Schema::table('lms_subject_attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_subject_attendances', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('is_open');
            }
            if (! Schema::hasColumn('lms_subject_attendances', 'recurring_days')) {
                $table->json('recurring_days')->nullable()->after('is_recurring');
            }
            if (! Schema::hasColumn('lms_subject_attendances', 'recurring_schedules')) {
                $table->json('recurring_schedules')->nullable()->after('recurring_days');
            }
            if (! Schema::hasColumn('lms_subject_attendances', 'parent_attendance_id')) {
                $table->foreignId('parent_attendance_id')->nullable()->constrained('lms_subject_attendances')->nullOnDelete()->after('recurring_schedules');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_subject_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('lms_subject_attendances', 'parent_attendance_id')) {
                $table->dropForeign(['parent_attendance_id']);
                $table->dropColumn('parent_attendance_id');
            }
            if (Schema::hasColumn('lms_subject_attendances', 'recurring_schedules')) {
                $table->dropColumn('recurring_schedules');
            }
            if (Schema::hasColumn('lms_subject_attendances', 'recurring_days')) {
                $table->dropColumn('recurring_days');
            }
            if (Schema::hasColumn('lms_subject_attendances', 'is_recurring')) {
                $table->dropColumn('is_recurring');
            }
        });

        Schema::table('course_classes', function (Blueprint $table) {
            if (Schema::hasColumn('course_classes', 'schedules')) {
                $table->dropColumn('schedules');
            }
        });
    }
};
