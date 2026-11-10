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
        if (Schema::hasTable('students') && ! Schema::hasColumn('students', 'is_tu_registered')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('is_tu_registered')->default(false)->after('is_active');
                $table->timestamp('tu_registered_at')->nullable()->after('is_tu_registered');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'is_tu_registered')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['is_tu_registered', 'tu_registered_at']);
            });
        }
    }
};
