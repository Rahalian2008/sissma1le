<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectPermendikdasmenSeeder extends Seeder
{
    public function run(): void
    {
        $count = Subject::seedPermendikdasmenSubjects();
        $this->command?->info("Berhasil meng-generate {$count} mata pelajaran Permendikdasmen No. 13 Tahun 2025.");
    }
}
