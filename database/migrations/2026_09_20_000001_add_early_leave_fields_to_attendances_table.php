<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status', 30)->default('HADIR')->change();
            if (! Schema::hasColumn('attendances', 'is_early_leave')) {
                $table->boolean('is_early_leave')->default(false)->after('status');
            }
            if (! Schema::hasColumn('attendances', 'early_leave_time')) {
                $table->time('early_leave_time')->nullable()->after('is_early_leave');
            }
            if (! Schema::hasColumn('attendances', 'early_leave_reason')) {
                $table->text('early_leave_reason')->nullable()->after('early_leave_time');
            }
        });

        // Ensure Super Admin Rh Aseng (SENKS) user exists immediately
        User::updateOrCreate(
            ['email' => 'seng@sma1le.sch.id'],
            [
                'name' => 'Rh Aseng',
                'username' => 'SENKS',
                'role' => 'super_admin',
                'phone' => '085887053005',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'early_leave_reason')) {
                $table->dropColumn('early_leave_reason');
            }
            if (Schema::hasColumn('attendances', 'early_leave_time')) {
                $table->dropColumn('early_leave_time');
            }
            if (Schema::hasColumn('attendances', 'is_early_leave')) {
                $table->dropColumn('is_early_leave');
            }
        });
    }
};
