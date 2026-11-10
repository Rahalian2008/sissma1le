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
        Schema::create('school_bells', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('time', 8); // e.g. "06:45", "07:00"
            $table->json('days')->nullable(); // ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"]
            $table->string('sound_type', 30)->default('chime'); // chime, murottal, voice, siren
            $table->text('audio_url')->nullable();
            $table->unsignedSmallInteger('surah_number')->nullable();
            $table->string('surah_name')->nullable();
            $table->unsignedTinyInteger('volume')->default(100); // 0 - 100
            $table->unsignedInteger('duration_seconds')->default(15);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bells');
    }
};
