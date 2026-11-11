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
        Schema::table('school_bells', function (Blueprint $table) {
            $table->json('surah_numbers')->nullable()->after('surah_name');
            $table->json('anthem_ids')->nullable()->after('surah_numbers');
            $table->json('playlist')->nullable()->after('anthem_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_bells', function (Blueprint $table) {
            $table->dropColumn(['surah_numbers', 'anthem_ids', 'playlist']);
        });
    }
};
