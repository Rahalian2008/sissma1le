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
            ['name' => 'adm_web'],
            [
                'display_name' => 'Admin Web (Siswa)',
                'description' => 'Akses kelola konten website sekolah terbatas pada Berita & Kabar',
                'level' => 8,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where('name', 'adm_web')->delete();
    }
};
