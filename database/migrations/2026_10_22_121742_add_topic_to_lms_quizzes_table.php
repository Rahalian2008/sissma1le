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
        Schema::table('lms_quizzes', function (Blueprint $table) {
            $table->text('topic')->nullable()->after('quiz_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_quizzes', function (Blueprint $table) {
            $table->dropColumn('topic');
        });
    }
};
