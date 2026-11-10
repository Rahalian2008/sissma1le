<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\KurnasCocurricularProject;
use App\Models\KurnasLearningGoal;
use App\Models\KurnasSetting;
use App\Models\KurnasSubjectCombine;
use App\Models\KurnasSubjectGroup;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RaporKurnasSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest('id')->first();
        $headmasterName = SchoolSetting::where('key', 'headmaster_name')->value('value') ?? 'H. Jajang Sutisna, M.Pd.';
        $headmasterNip = SchoolSetting::where('key', 'headmaster_nip')->value('value') ?? '196805141994121001';
        $schoolLogo = SchoolSetting::where('key', 'school_logo')->value('value') ?? 'images/logo-sma1.png';

        // 1. Setting Rapor Kurnas
        $setting = KurnasSetting::firstOrCreate(
            ['is_active' => true],
            [
                'academic_year_id' => $activeYear?->id,
                'semester' => $activeYear?->semester ?? '1',
                'is_locked' => false,
                'report_date' => Carbon::now()->addWeeks(2)->toDateString(),
                'report_place' => 'Lengkong',
                'headmaster_name' => $headmasterName,
                'headmaster_nip' => $headmasterNip,
                'school_logo' => $schoolLogo,
                'provincial_logo' => 'images/logo-jabar.png',
                'notes' => 'Rapor Kurikulum Merdeka / Kurnas SMA Negeri 1 Lengkong Versi 2025.',
            ]
        );

        // 2. Mapping Kelompok Mapel
        $subjects = Subject::all();
        $orderA = 1;
        $orderB = 1;
        $orderC = 1;

        foreach ($subjects as $subject) {
            $name = strtolower($subject->name);
            $groupType = 'A'; // default umum

            if (str_contains($name, 'sunda') || str_contains($name, 'muatan lokal') || str_contains($name, 'mulok')) {
                $groupType = 'C';
                $order = $orderC++;
            } elseif (
                str_contains($name, 'pilihan') ||
                str_contains($name, 'biologi') ||
                str_contains($name, 'kimia') ||
                str_contains($name, 'fisika') ||
                str_contains($name, 'ekonomi') ||
                str_contains($name, 'geografi') ||
                str_contains($name, 'sosiologi') ||
                str_contains($name, 'antropologi') ||
                str_contains($name, 'informatika lanjut') ||
                str_contains($name, 'matematika tingkat lanjut')
            ) {
                $groupType = 'B';
                $order = $orderB++;
            } else {
                $groupType = 'A';
                $order = $orderA++;
            }

            KurnasSubjectGroup::updateOrCreate(
                ['subject_id' => $subject->id],
                [
                    'group_type' => $groupType,
                    'display_name' => $subject->name,
                    'order_number' => $order,
                    'is_combined' => false,
                ]
            );
        }

        // 3. Sample Tujuan Pembelajaran (TP) Kurikulum Merdeka
        $teacher = Teacher::first();
        foreach ($subjects as $subject) {
            $name = strtolower($subject->name);

            if (str_contains($name, 'matematika')) {
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 1', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'menerapkan konsep eksponen dan logaritma dalam memecahkan masalah kontekstual',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 2', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'menganalisis barisan dan deret aritmetika serta geometri dalam pemodelan finansial',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 3', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'menyelesaikan perbandingan trigonometri siku-siku dalam pengukuran lapangan',
                        'is_active' => true,
                    ]
                );
            } elseif (str_contains($name, 'informatika')) {
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 1', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'memahami strategi algoritmik standar dan berpikir komputasional dalam penyelesaian masalah',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 2', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'mengimplementasikan struktur data dan algoritma dasar pada bahasa pemrograman',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 3', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'menganalisis aspek privasi, keamanan data, dan etika kecerdasan artifisial',
                        'is_active' => true,
                    ]
                );
            } elseif (str_contains($name, 'indonesia')) {
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 1', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'mengevaluasi informasi akurat dan kritis dari teks laporan hasil observasi',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 2', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'mengonstruksi teks eksposisi dan teks biografi dengan struktur serta kaidah bahasa yang tepat',
                        'is_active' => true,
                    ]
                );
            } else {
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 1', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'memahami konsep dasar dan esensi materi pembelajaran secara komprehensif',
                        'is_active' => true,
                    ]
                );
                KurnasLearningGoal::firstOrCreate(
                    ['subject_id' => $subject->id, 'code' => 'TP 2', 'fase' => 'E'],
                    [
                        'teacher_id' => $teacher?->id,
                        'grade' => 'X',
                        'semester' => '1',
                        'description' => 'menerapkan prinsip analisis dan pemecahan masalah kontekstual dalam kehidupan sehari-hari',
                        'is_active' => true,
                    ]
                );
            }
        }

        // 4. Sample Projek Kokurikuler / P5
        KurnasCocurricularProject::firstOrCreate(
            ['title' => 'Pengolahan Sampah Organik dan Audit Jejak Karbon Sekolah'],
            [
                'academic_year_id' => $activeYear?->id,
                'semester' => '1',
                'grade' => 'X',
                'fase' => 'E',
                'theme' => 'Gaya Hidup Berkelanjutan',
                'description' => 'Peserta didik melakukan investigasi sistem pembuangan sampah sekolah, menghitung jejak karbon harian warga sekolah, dan merancang komposting terpadu.',
                'target_dimensions' => [
                    'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
                    'Gotong Royong',
                    'Bernalar Kritis',
                ],
                'coordinator_teacher_id' => $teacher?->id,
            ]
        );

        KurnasCocurricularProject::firstOrCreate(
            ['title' => 'E-Voting Demokrasi Santun Pemilihan Ketua OSIS'],
            [
                'academic_year_id' => $activeYear?->id,
                'semester' => '1',
                'grade' => 'XI',
                'fase' => 'F',
                'theme' => 'Suara Demokrasi',
                'description' => 'Membangun simulasi pemilu terbuka, menyusun visi misi calon, debat kandidat, serta menyalurkan hak suara secara jujur dan berkeadaban.',
                'target_dimensions' => [
                    'Berkebinekaan Global',
                    'Mandiri',
                    'Bernalar Kritis',
                ],
                'coordinator_teacher_id' => $teacher?->id,
            ]
        );

        // 5. Default Mata Pelajaran Gabungan (IPA & IPS) untuk Fase E (Kelas X) Sesuai e-Rapor 2025
        $ipaSubjectIds = Subject::where(function ($q) {
            $q->where('name', 'like', '%fisika%')
                ->orWhere('name', 'like', '%kimia%')
                ->orWhere('name', 'like', '%biologi%');
        })->pluck('id')->toArray();

        if (! empty($ipaSubjectIds)) {
            KurnasSubjectCombine::updateOrCreate(
                ['combined_code' => 'IPA', 'grade' => 'X'],
                [
                    'combined_name' => 'Ilmu Pengetahuan Alam (IPA)',
                    'source_subject_ids' => $ipaSubjectIds,
                    'order_number' => 7,
                    'is_active' => true,
                ]
            );
        }

        $ipsSubjectIds = Subject::where(function ($q) {
            $q->where('name', 'like', '%sosiologi%')
                ->orWhere('name', 'like', '%ekonomi%')
                ->orWhere('name', 'like', '%geografi%')
                ->orWhere('name', 'like', '%sejarah%');
        })->pluck('id')->toArray();

        if (! empty($ipsSubjectIds)) {
            KurnasSubjectCombine::updateOrCreate(
                ['combined_code' => 'IPS', 'grade' => 'X'],
                [
                    'combined_name' => 'Ilmu Pengetahuan Sosial (IPS)',
                    'source_subject_ids' => $ipsSubjectIds,
                    'order_number' => 8,
                    'is_active' => true,
                ]
            );
        }
    }
}
