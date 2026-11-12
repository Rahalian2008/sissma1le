<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('library_books')) {
            if (Schema::hasColumn('library_books', 'slug')) {
                // Ensure slug is nullable in MySQL / SQLite
                if (DB::getDriverName() === 'mysql') {
                    DB::statement('ALTER TABLE `library_books` MODIFY COLUMN `slug` VARCHAR(255) NULL DEFAULT NULL');
                } else {
                    Schema::table('library_books', function (Blueprint $table) {
                        $table->string('slug', 255)->nullable()->change();
                    });
                }

                // Backfill existing empty/null slugs
                $books = DB::table('library_books')->whereNull('slug')->orWhere('slug', '')->get(['id', 'title']);
                foreach ($books as $b) {
                    $slug = Str::slug($b->title ?: 'buku').'-'.$b->id;
                    DB::table('library_books')->where('id', $b->id)->update(['slug' => $slug]);
                }
            } else {
                Schema::table('library_books', function (Blueprint $table) {
                    $table->string('slug', 255)->nullable()->after('title');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep nullable to avoid breaking records
    }
};
