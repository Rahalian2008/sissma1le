<?php

namespace Database\Seeders;

use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class CurriculumMasterSeeder extends Seeder
{
    /**
     * Run the database seeds for curriculum master data.
     */
    public function run(): void
    {
        $admin = User::whereIn('role', ['super_admin', 'admin'])->first();
        $teacher = Teacher::first();

        // 1. Capaian Pembelajaran (CP) Contoh: Informatika Fase E (Kelas X)
        $inforSubject = Subject::where('name', 'like', '%Informatika%')->first();
        if (! $inforSubject) {
            $inforSubject = Subject::firstOrCreate(
                ['code' => 'INF-X'],
                [
                    'name' => 'Informatika',
                    'grade' => 'X',
                    'group' => 'Mata Pelajaran Wajib',
                    'kkm' => 75,
                    'color' => '#6366f1',
                    'icon' => '💻',
                    'is_active' => true,
                ]
            );
        }

        $cp1 = LearningOutcome::firstOrCreate(
            [
                'subject_id' => $inforSubject->id,
                'phase' => 'E',
                'grade_level' => 'X',
                'code' => 'CP-INF-E-BK',
            ],
            [
                'element' => 'Berpikir Komputasional (BK)',
                'description' => 'Pada akhir fase E, peserta didik mampu menerapkan strategi algoritmik standar pada kehidupan sehari-hari maupun implementasinya dalam sistem komputasi, untuk menghasilkan beberapa solusi persoalan dengan data diskrit bervolume besar.',
                'created_by' => $admin?->id,
                'is_active' => true,
            ]
        );

        $cp2 = LearningOutcome::firstOrCreate(
            [
                'subject_id' => $inforSubject->id,
                'phase' => 'E',
                'grade_level' => 'X',
                'code' => 'CP-INF-E-AD',
            ],
            [
                'element' => 'Analisis Data (AD)',
                'description' => 'Pada akhir fase E, peserta didik mampu mengakses, mengolah, mengelola, dan menganalisis data secara efisien, terstruktur, dan sistematis untuk menginterpretasi dan memprediksi sekumpulan data dari situasi konkret.',
                'created_by' => $admin?->id,
                'is_active' => true,
            ]
        );

        // 2. Tujuan Pembelajaran (TP)
        LearningObjective::firstOrCreate(
            [
                'subject_id' => $inforSubject->id,
                'tp_code' => '10.1',
            ],
            [
                'learning_outcome_id' => $cp1->id,
                'teacher_id' => $teacher?->id,
                'title' => 'Menerapkan Berpikir Komputasional untuk Pencarian (Searching) dan Pengurutan (Sorting)',
                'description' => 'Peserta didik mampu memahami dan menerapkan konsep algoritma pencarian (searching) dan pengurutan (sorting) data secara mandiri dan bernalar kritis.',
                'competency' => 'Memahami dan Menerapkan Algoritma Pengurutan & Pencarian',
                'scope_material' => 'Algoritma Sorting (Bubble, Selection, Insertion) dan Searching (Linear & Binary Search)',
                'hours_allocation' => 4,
                'target_semester' => 'GANJIL',
                'is_active' => true,
            ]
        );

        LearningObjective::firstOrCreate(
            [
                'subject_id' => $inforSubject->id,
                'tp_code' => '10.2',
            ],
            [
                'learning_outcome_id' => $cp2->id,
                'teacher_id' => $teacher?->id,
                'title' => 'Mengolah dan Memvisualisasikan Data dengan Aplikasi Lembar Kerja',
                'description' => 'Peserta didik mampu mengolah sekumpulan data statistik sederhana menggunakan fungsi logika dan memvisualisasikannya dalam bentuk diagram/grafik.',
                'competency' => 'Menganalisis dan Memvisualisasikan Data Diskrit',
                'scope_material' => 'Fungsi Logika IF, VLOOKUP/XLOOKUP, Pembuatan Chart Visual',
                'hours_allocation' => 4,
                'target_semester' => 'GANJIL',
                'is_active' => true,
            ]
        );
    }
}
