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
        Schema::create('bk_consultations', function (Blueprint $table) {
            $table->id();
            $table->string('consultation_code', 30)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('topic', 150)->default('Konsultasi & Curhat Siswa');
            $table->string('category', 50)->default('PRIBADI'); // PRIBADI, BELAJAR, SOSIAL, KARIER, LAINNYA
            $table->string('status', 30)->default('OPEN'); // OPEN, ACTIVE, RESOLVED, CLOSED
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('last_message_at')->nullable()->index();
            $table->text('last_message_preview')->nullable();
            $table->unsignedInteger('unread_counselor_count')->default(0);
            $table->unsignedInteger('unread_student_count')->default(0);
            $table->text('conclusion_notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['status', 'last_message_at']);
        });

        Schema::create('bk_consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('bk_consultations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('sender_role', 20)->default('STUDENT'); // STUDENT, COUNSELOR
            $table->text('message')->nullable();
            $table->string('attachment_type', 20)->nullable(); // IMAGE, VIDEO, DOCUMENT, LINK
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 100)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable(); // size in bytes
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['consultation_id', 'created_at']);
            $table->index(['consultation_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_consultation_messages');
        Schema::dropIfExists('bk_consultations');
    }
};
