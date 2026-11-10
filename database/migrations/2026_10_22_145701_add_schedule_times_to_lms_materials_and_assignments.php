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
        Schema::table('lms_materials', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_materials', 'start_time')) {
                $table->dateTime('start_time')->nullable()->after('is_published');
            }
        });

        Schema::table('lms_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_assignments', 'start_time')) {
                $table->dateTime('start_time')->nullable()->after('instructions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_materials', function (Blueprint $table) {
            if (Schema::hasColumn('lms_materials', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });

        Schema::table('lms_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('lms_assignments', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }
};
