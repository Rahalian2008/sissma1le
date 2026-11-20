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
        Schema::table('bk_psychological_test_results', function (Blueprint $table) {
            $table->json('ai_report')->nullable()->after('scientific_sources');
            $table->string('ai_provider', 100)->nullable()->after('ai_report');
            $table->string('ai_model', 100)->nullable()->after('ai_provider');
            $table->string('priority_level', 50)->nullable()->after('ai_model');
            $table->string('priority_label', 150)->nullable()->after('priority_level');
            $table->text('executive_summary')->nullable()->after('priority_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bk_psychological_test_results', function (Blueprint $table) {
            $table->dropColumn([
                'ai_report',
                'ai_provider',
                'ai_model',
                'priority_level',
                'priority_label',
                'executive_summary',
            ]);
        });
    }
};
