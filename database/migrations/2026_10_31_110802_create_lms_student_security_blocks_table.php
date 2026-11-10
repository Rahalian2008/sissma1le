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
        Schema::create('lms_student_security_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('blockable_type', 50); // material, assignment, quiz
            $table->unsignedBigInteger('blockable_id');
            $table->string('violation_type', 50)->default('tab_switch'); // tab_switch, minimize, window_blur, app_close
            $table->string('reason', 255)->default('Terdeteksi berpindah tab browser, meminimalkan aplikasi, atau meninggalkan halaman.');
            $table->boolean('is_blocked')->default(true);
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('unblocked_at')->nullable();
            $table->foreignId('unblocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'blockable_type', 'blockable_id', 'is_blocked'], 'lms_security_student_item_idx');
            $table->index(['course_class_id', 'is_blocked'], 'lms_security_class_blocked_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_student_security_blocks');
    }
};
