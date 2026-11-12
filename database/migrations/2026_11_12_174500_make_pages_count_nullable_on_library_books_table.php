<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('library_books')) {
            if (DB::getDriverName() === 'mysql') {
                if (Schema::hasColumn('library_books', 'pages_count')) {
                    DB::statement('ALTER TABLE `library_books` MODIFY COLUMN `pages_count` INT NULL DEFAULT 0');
                }
                if (Schema::hasColumn('library_books', 'page_count')) {
                    DB::statement('ALTER TABLE `library_books` MODIFY COLUMN `page_count` INT UNSIGNED NULL DEFAULT 0');
                }
            } else {
                Schema::table('library_books', function (Blueprint $table) {
                    if (Schema::hasColumn('library_books', 'pages_count')) {
                        $table->integer('pages_count')->nullable()->default(0)->change();
                    }
                    if (Schema::hasColumn('library_books', 'page_count')) {
                        $table->integer('page_count')->unsigned()->nullable()->default(0)->change();
                    }
                });
            }

            // Backfill nulls with 0
            if (Schema::hasColumn('library_books', 'pages_count')) {
                DB::table('library_books')->whereNull('pages_count')->update(['pages_count' => 0]);
            }
            if (Schema::hasColumn('library_books', 'page_count')) {
                DB::table('library_books')->whereNull('page_count')->update(['page_count' => 0]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep nullable to avoid data integrity crashes
    }
};
