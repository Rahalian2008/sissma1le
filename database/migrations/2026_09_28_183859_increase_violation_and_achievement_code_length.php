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
        Schema::table('violations', function (Blueprint $table) {
            $table->string('violation_code', 50)->nullable()->change();
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->string('achievement_code', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->string('violation_code', 20)->nullable()->change();
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->string('achievement_code', 20)->nullable()->change();
        });
    }
};
