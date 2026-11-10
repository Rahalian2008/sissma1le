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
        if (Schema::hasTable('worship_attendances')) {
            Schema::table('worship_attendances', function (Blueprint $table) {
                $table->string('method', 50)->default('MANUAL')->change();
            });
        }

        if (Schema::hasTable('extracurricular_attendances')) {
            Schema::table('extracurricular_attendances', function (Blueprint $table) {
                $table->string('method', 50)->default('MANUAL')->change();
            });
        }

        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('method', 50)->default('SELFIE')->change();
                $table->string('out_method', 50)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep string for safety
    }
};
