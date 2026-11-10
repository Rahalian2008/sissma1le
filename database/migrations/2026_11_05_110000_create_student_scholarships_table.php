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
        if (! Schema::hasTable('student_scholarships')) {
            Schema::create('student_scholarships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('scholarship_name', 100);
                $table->string('source', 100)->default('Pemerintah Pusat');
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('period', 50)->default('2024/2025');
                $table->string('status', 50)->default('Usulan');
                $table->string('document_path', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_scholarships');
    }
};
