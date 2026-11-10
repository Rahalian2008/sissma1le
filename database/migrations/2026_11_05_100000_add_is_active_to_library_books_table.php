<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('library_books') && ! Schema::hasColumn('library_books', 'is_active')) {
            Schema::table('library_books', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_available');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('library_books') && Schema::hasColumn('library_books', 'is_active')) {
            Schema::table('library_books', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
