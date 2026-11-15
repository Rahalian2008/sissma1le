<?php

use App\Models\SchoolBell;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('school_bells')) {
            return;
        }

        // Jika database baru / belum di-seed lengkap, biarkan ensureDefaultSchedules yang men-generate
        if (SchoolBell::count() < 10) {
            return;
        }

        SchoolBell::fixFridayScheduleAndAnnouncements();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed
    }
};
