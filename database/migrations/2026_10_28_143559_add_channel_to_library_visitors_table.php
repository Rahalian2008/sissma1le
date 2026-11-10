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
        Schema::table('library_visitors', function (Blueprint $table) {
            $table->string('channel', 20)->default('offline')->after('visitor_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_visitors', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
    }
};
