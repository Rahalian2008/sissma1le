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
        Schema::create('lms_zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('topic', 150)->nullable();
            $table->text('agenda')->nullable();
            $table->text('meeting_url');
            $table->string('meeting_id', 100)->nullable();
            $table->string('passcode', 100)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('status', 20)->default('live'); // scheduled, live, ended
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('lms_zoom_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_meeting_id')->constrained('lms_zoom_meetings')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->unique(['zoom_meeting_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_zoom_participants');
        Schema::dropIfExists('lms_zoom_meetings');
    }
};
