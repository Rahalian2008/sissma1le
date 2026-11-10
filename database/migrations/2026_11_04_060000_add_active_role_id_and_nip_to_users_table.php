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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 30)->nullable()->after('username');
            }
            if (! Schema::hasColumn('users', 'active_role_id')) {
                $table->foreignId('active_role_id')->nullable()->after('is_active')->constrained('roles')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'active_role_id')) {
                $table->dropForeign(['active_role_id']);
                $table->dropColumn('active_role_id');
            }
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropColumn('nip');
            }
        });
    }
};
