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
        if (Schema::hasTable('school_bells') && ! Schema::hasColumn('school_bells', 'voice_text')) {
            Schema::table('school_bells', function (Blueprint $table) {
                $table->text('voice_text')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('school_bells') && Schema::hasColumn('school_bells', 'voice_text')) {
            Schema::table('school_bells', function (Blueprint $table) {
                $table->dropColumn('voice_text');
            });
        }
    }
};
