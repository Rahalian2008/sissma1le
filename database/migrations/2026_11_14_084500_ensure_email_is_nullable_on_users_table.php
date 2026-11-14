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
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                try {
                    DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
                } catch (Throwable $e) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('email', 255)->nullable()->change();
                    });
                }
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('email', 255)->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep nullable for flexibility and prevent integrity constraint violations
    }
};
