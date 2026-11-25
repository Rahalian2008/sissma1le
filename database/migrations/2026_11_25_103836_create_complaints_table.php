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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->boolean('is_anonymous')->default(false);
            $table->string('reporter_name', 150);
            $table->string('reporter_phone', 30);
            $table->string('reporter_email', 150)->nullable();
            $table->string('reporter_type', 50)->default('masyarakat');
            $table->string('category', 50)->default('aspirasi_kemajuan');
            $table->string('title', 255);
            $table->text('description');
            $table->date('incident_date')->nullable();
            $table->string('incident_location', 150)->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->string('status', 30)->default('MENUNGGU'); // MENUNGGU, DITELAAH, DIPROSES, SELESAI, DITOLAK
            $table->text('disposition_notes')->nullable(); // Tanggapan resmi publik
            $table->text('internal_notes')->nullable(); // Catatan internal kepsek & wakasek
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            $table->index('ticket_number');
            $table->index('status');
            $table->index('category');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
