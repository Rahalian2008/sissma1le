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
        Schema::table('student_ai_raports', function (Blueprint $table) {
            $table->unsignedInteger('worship_dhuha_total')->default(0)->after('worship_dzuhur_rate');
            $table->unsignedInteger('worship_dhuha_hadir')->default(0)->after('worship_dhuha_total');
            $table->decimal('worship_dhuha_rate', 5, 2)->default(0)->after('worship_dhuha_hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_ai_raports', function (Blueprint $table) {
            $table->dropColumn(['worship_dhuha_total', 'worship_dhuha_hadir', 'worship_dhuha_rate']);
        });
    }
};
