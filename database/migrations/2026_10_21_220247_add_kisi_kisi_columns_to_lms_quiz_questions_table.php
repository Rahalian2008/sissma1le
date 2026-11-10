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
        Schema::table('lms_quiz_questions', function (Blueprint $table) {
            $table->text('stimulus')->nullable()->after('question_text');
            $table->text('indikator_soal')->nullable()->after('question_type');
            $table->string('level_kognitif', 30)->nullable()->default('L3 / C4 (Penalaran)')->after('indikator_soal');
            $table->string('materi_pokok', 255)->nullable()->after('level_kognitif');
            $table->text('pembahasan')->nullable()->after('correct_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_quiz_questions', function (Blueprint $table) {
            $table->dropColumn(['stimulus', 'indikator_soal', 'level_kognitif', 'materi_pokok', 'pembahasan']);
        });
    }
};
