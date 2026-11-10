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
        Schema::table('quran_tahfidz_records', function (Blueprint $table) {
            $table->unsignedSmallInteger('ayat_count')->nullable()->after('ayat_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quran_tahfidz_records', function (Blueprint $table) {
            $table->dropColumn('ayat_count');
        });
    }
};
