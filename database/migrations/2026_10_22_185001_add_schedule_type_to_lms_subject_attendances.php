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
        Schema::table('lms_subject_attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_subject_attendances', 'schedule_type')) {
                $table->string('schedule_type', 20)->default('auto')->after('attendance_method'); // auto, manual
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_subject_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('lms_subject_attendances', 'schedule_type')) {
                $table->dropColumn('schedule_type');
            }
        });
    }
};
