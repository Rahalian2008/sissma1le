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
        if (Schema::hasTable('course_classes')) {
            Schema::table('course_classes', function (Blueprint $table) {
                if (! Schema::hasColumn('course_classes', 'kktp')) {
                    $table->unsignedInteger('kktp')->nullable()->after('description');
                }
                if (! Schema::hasColumn('course_classes', 'is_security_enabled')) {
                    $table->boolean('is_security_enabled')->default(true)->after('is_active');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_classes')) {
            Schema::table('course_classes', function (Blueprint $table) {
                if (Schema::hasColumn('course_classes', 'is_security_enabled')) {
                    $table->dropColumn('is_security_enabled');
                }
                if (Schema::hasColumn('course_classes', 'kktp')) {
                    $table->dropColumn('kktp');
                }
            });
        }
    }
};
