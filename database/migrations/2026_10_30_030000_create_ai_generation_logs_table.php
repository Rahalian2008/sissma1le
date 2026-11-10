<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_generation_logs')) {
            Schema::create('ai_generation_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('task_type', 50)->index();
                $table->string('provider', 50)->default('gemini');
                $table->string('model', 100)->nullable();
                $table->text('prompt_summary')->nullable();
                $table->boolean('is_fallback')->default(false);
                $table->unsignedInteger('response_time_ms')->default(0);
                $table->unsignedInteger('token_estimate')->default(0);
                $table->string('status', 20)->default('SUCCESS');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['created_at', 'task_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_logs');
    }
};
