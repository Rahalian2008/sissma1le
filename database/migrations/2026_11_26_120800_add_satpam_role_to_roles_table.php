<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::firstOrCreate(
            ['name' => 'satpam'],
            [
                'display_name' => 'Satpam / Petugas Keamanan',
                'description' => 'Petugas keamanan pos gerbang, izin keluar siswa (School Pass), dan tamu sekolah',
                'level' => 7,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where('name', 'satpam')->delete();
    }
};
