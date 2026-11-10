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
            $table->string('quiz_type', 50)->default('kuis')->after('title');
            $table->text('instructions')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_quizzes', function (Blueprint $table) {
            $table->dropColumn(['quiz_type', 'instructions']);
        });
    }
};
