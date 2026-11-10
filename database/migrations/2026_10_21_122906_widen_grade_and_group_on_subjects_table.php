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
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('grade', 100)->default('Semua Tingkat')->change();
            $table->string('group', 100)->default('Mata Pelajaran Wajib')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('grade', 20)->default('Semua')->change();
            $table->string('group', 50)->default('Umum')->change();
        });
    }
};
