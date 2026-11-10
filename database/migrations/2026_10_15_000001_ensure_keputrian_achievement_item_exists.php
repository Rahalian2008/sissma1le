<?php

use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = AchievementCategory::where('code', 'R1')->first()
            ?? AchievementCategory::where('id', 1)->first()
            ?? AchievementCategory::firstOrCreate(
                ['name' => 'Pengembangan Keagamaan'],
                ['code' => 'R1', 'description' => 'Prestasi bidang keagamaan dan ibadah']
            );

        $exists = AchievementItem::where('code', 'R1.6')
            ->orWhere('name', 'Melaksanakan kegiatan keputrian')
            ->first();

        if (! $exists) {
            AchievementItem::create([
                'category_id' => $category->id,
                'code' => 'R1.6',
                'name' => 'Melaksanakan kegiatan keputrian',
                'default_points' => 5,
                'level' => 'SEKOLAH',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        AchievementItem::where('code', 'R1.6')
            ->where('name', 'Melaksanakan kegiatan keputrian')
            ->delete();
    }
};
