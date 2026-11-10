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
            if (! Schema::hasColumn('course_classes', 'kktp')) {
                $table->unsignedInteger('kktp')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            if (Schema::hasColumn('course_classes', 'kktp')) {
                $table->dropColumn('kktp');
            }
        });
    }
};
