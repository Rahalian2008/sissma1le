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
            $table->json('item_analysis')->nullable()->after('analysis_summary');
            $table->json('action_plan')->nullable()->after('study_recommendations');
            $table->json('scientific_sources')->nullable()->after('action_plan');
            $table->timestamp('ai_analyzed_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bk_psychological_test_results', function (Blueprint $table) {
            $table->dropColumn([
                'item_analysis',
                'action_plan',
                'scientific_sources',
                'ai_analyzed_at',
            ]);
        });
    }
};
