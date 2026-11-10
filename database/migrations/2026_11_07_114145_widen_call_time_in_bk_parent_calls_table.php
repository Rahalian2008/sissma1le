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
        if (Schema::hasTable('bk_parent_calls')) {
            Schema::table('bk_parent_calls', function (Blueprint $table) {
                $table->string('call_time', 100)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bk_parent_calls')) {
            Schema::table('bk_parent_calls', function (Blueprint $table) {
                $table->string('call_time', 20)->nullable()->change();
            });
        }
    }
};
