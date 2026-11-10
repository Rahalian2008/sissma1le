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
        if (Schema::hasTable('library_books')) {
            Schema::table('library_books', function (Blueprint $table) {
                if (! Schema::hasColumn('library_books', 'file_path')) {
                    $table->string('file_path', 255)->nullable()->after('cover_image');
                }
                if (! Schema::hasColumn('library_books', 'is_ebook')) {
                    $table->boolean('is_ebook')->default(false)->after('file_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('library_books')) {
            Schema::table('library_books', function (Blueprint $table) {
                if (Schema::hasColumn('library_books', 'is_ebook')) {
                    $table->dropColumn('is_ebook');
                }
                if (Schema::hasColumn('library_books', 'file_path')) {
                    $table->dropColumn('file_path');
                }
            });
        }
    }
};
