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
        Schema::table('cbt_exams', function (Blueprint $table) {
            if (! Schema::hasColumn('cbt_exams', 'require_token')) {
                $table->boolean('require_token')->default(false)->after('token');
            }
            if (! Schema::hasColumn('cbt_exams', 'available_rooms')) {
                $table->json('available_rooms')->nullable()->after('require_token');
            }
        });

        Schema::table('cbt_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('cbt_sessions', 'room_name')) {
                $table->string('room_name', 50)->default('Ruang 01')->after('cbt_password')->index();
            }
            if (! Schema::hasColumn('cbt_sessions', 'is_allowed')) {
                $table->boolean('is_allowed')->default(true)->after('room_name')->index();
            }
        });

        Schema::table('cbt_exam_questions', function (Blueprint $table) {
            if (! Schema::hasColumn('cbt_exam_questions', 'media_path')) {
                $table->string('media_path', 255)->nullable()->after('stimulus');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_exams', 'require_token')) {
                $table->dropColumn('require_token');
            }
            if (Schema::hasColumn('cbt_exams', 'available_rooms')) {
                $table->dropColumn('available_rooms');
            }
        });

        Schema::table('cbt_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_sessions', 'room_name')) {
                $table->dropColumn('room_name');
            }
            if (Schema::hasColumn('cbt_sessions', 'is_allowed')) {
                $table->dropColumn('is_allowed');
            }
        });

        Schema::table('cbt_exam_questions', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_exam_questions', 'media_path')) {
                $table->dropColumn('media_path');
            }
        });
    }
};
