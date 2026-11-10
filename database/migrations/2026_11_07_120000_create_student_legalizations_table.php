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
        if (! Schema::hasTable('student_legalizations')) {
            Schema::create('student_legalizations', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_number', 50)->unique();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->string('applicant_name', 150);
                $table->string('nisn', 20)->nullable();
                $table->string('nis', 20)->nullable();
                $table->string('graduation_year', 10)->nullable();
                $table->string('phone_number', 30)->nullable();
                $table->string('document_type', 100);
                $table->unsignedInteger('copies_count')->default(1);
                $table->string('purpose', 255)->nullable();
                $table->enum('status', ['diajukan', 'diverifikasi', 'selesai', 'ditolak'])->default('diajukan');
                $table->text('notes')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index('applicant_name');
                $table->index('nisn');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_legalizations');
    }
};
