<?php

namespace App\Services;

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestResult;
use App\Models\Student;

class BkPsychologicalTestService
{
    /**
     * Process student answers and generate comprehensive analysis and recommendations.
     */
    public function evaluateTest(BkPsychologicalTest $test, Student $student, array $answers): BkPsychologicalTestResult
    {
        $test->load('questions');
        $questions = $test->questions->keyBy('id');

        $dimensionScores = [];
        $dimensionMaxPossible = [];

        foreach ($test->questions as $q) {
            $dim = $q->dimension;
            if (! isset($dimensionScores[$dim])) {
                $dimensionScores[$dim] = 0;
                $dimensionMaxPossible[$dim] = 0;
            }
            $dimensionMaxPossible[$dim] += 4; // Max score on scale 1-4

            $userScore = isset($answers[$q->id]) ? (int) $answers[$q->id] : 2;
            $dimensionScores[$dim] += $userScore;
        }

        // Hitung persentase per dimensi
        $dimensionPercentages = [];
        foreach ($dimensionScores as $dim => $raw) {
            $max = $dimensionMaxPossible[$dim] ?: 1;
            $dimensionPercentages[$dim] = round(($raw / $max) * 100, 1);
        }

        // Urutkan dimensi berdasarkan skor tertinggi
        arsort($dimensionScores);
        $sortedDimensions = array_keys($dimensionScores);
        $dominant = $sortedDimensions[0] ?? '';
        $secondary = $sortedDimensions[1] ?? null;
        $tertiary = $sortedDimensions[2] ?? null;

        $hollandCode = null;
        $profileLabel = '';
        $analysisSummary = '';
        $careerRecommendations = [];
        $studyRecommendations = '';

        if ($test->slug === 'vak') {
            $profileLabel = 'Gaya Belajar '.ucfirst(strtolower($dominant)).' Dominan';
            if ($secondary && abs($dimensionScores[$dominant] - $dimensionScores[$secondary]) <= 2) {
                $profileLabel = 'Gaya Belajar Multimodal ('.ucfirst(strtolower($dominant)).'-'.ucfirst(strtolower($secondary)).')';
            }

            $vakAnalysis = $this->generateVakAnalysis($dominant, $secondary, $dimensionPercentages);
            $analysisSummary = $vakAnalysis['summary'];
            $studyRecommendations = $vakAnalysis['study_tips'];
            $careerRecommendations = $vakAnalysis['careers'];
        } elseif ($test->slug === 'riasec') {
            $hollandCode = substr($dominant.$secondary.$tertiary, 0, 3);
            $profileLabel = "Tipe Karier Holland: {$hollandCode}";
            $riasecAnalysis = $this->generateRiasecAnalysis($hollandCode, $dominant, $secondary, $dimensionPercentages);
            $analysisSummary = $riasecAnalysis['summary'];
            $studyRecommendations = $riasecAnalysis['study_tips'];
            $careerRecommendations = $riasecAnalysis['careers'];
        } elseif ($test->slug === 'gardner') {
            $profileLabel = 'Kecerdasan Majemuk Dominan: '.str_replace('_', ' ', $dominant);
            $gardnerAnalysis = $this->generateGardnerAnalysis($dominant, $secondary, $tertiary, $dimensionPercentages);
            $analysisSummary = $gardnerAnalysis['summary'];
            $studyRecommendations = $gardnerAnalysis['study_tips'];
            $careerRecommendations = $gardnerAnalysis['careers'];
        } else {
            $profileLabel = 'Hasil Evaluasi Psikotes '.strtoupper($dominant);
            $analysisSummary = "Siswa menunjukkan dominansi pada aspek {$dominant} dengan skor {$dimensionScores[$dominant]}.";
            $studyRecommendations = 'Dianjurkan untuk mengoptimalkan potensi dengan pendampingan guru BK dan wali kelas.';
        }

        // Simpan / update hasil tes
        return BkPsychologicalTestResult::create([
            'test_id' => $test->id,
            'student_id' => $student->id,
            'taken_at' => now(),
            'answers' => $answers,
            'dimension_scores' => [
                'raw' => $dimensionScores,
                'percentages' => $dimensionPercentages,
            ],
            'dominant_dimension' => $dominant,
            'secondary_dimension' => $secondary,
            'tertiary_dimension' => $tertiary,
            'holland_code' => $hollandCode,
            'profile_label' => $profileLabel,
            'analysis_summary' => $analysisSummary,
            'career_recommendations' => $careerRecommendations,
            'study_recommendations' => $studyRecommendations,
            'status' => 'SUBMITTED',
        ]);
    }

    protected function generateVakAnalysis(string $dominant, ?string $secondary, array $percentages): array
    {
        $vPct = $percentages['VISUAL'] ?? 0;
        $aPct = $percentages['AUDITORI'] ?? 0;
        $kPct = $percentages['KINESTETIK'] ?? 0;

        $summary = "Berdasarkan evaluasi modalitas sensorik VAK, siswa memiliki preferensi utama pada gaya belajar **{$dominant}** ({$percentages[$dominant]}%), diikuti oleh modalitas **{$secondary}** (".($percentages[$secondary] ?? 0).'%). Siswa paling mudah menangkap, memproses, dan merekonstruksi materi pelajaran ketika stimulus disajikan selaras dengan gaya belajar dominannya.';

        if ($dominant === 'VISUAL') {
            $studyTips = "• Gunakan mind-mapping, diagram alur, dan stabilo aneka warna saat membuat ringkasan materi.\n• Ubah teks panjang menjadi tabel perbandingan, grafik konsep, atau flashcard bergambar.\n• Hindari belajar di meja atau ruangan yang berantakan karena polusi visual dapat mengaburkan konsentrasi.\n• Tonton animasi pembelajaran atau video edukasi visual sebelum membaca bab teks yang rumit.";
            $careers = [
                ['major' => 'Desain Komunikasi Visual / Animasi', 'field' => 'Kreatif Digital', 'prospect' => 'Desainer Grafis, Animator 3D, UI/UX Designer'],
                ['major' => 'Arsitektur & Perencanaan Wilayah Kota', 'field' => 'Teknik & Desain', 'prospect' => 'Arsitek Bangunan, Urban Planner, Interior Designer'],
                ['major' => 'Teknologi Radiologi / Kedokteran Gigi', 'field' => 'Kesehatan', 'prospect' => 'Radiografer, Dokter Spesialis Gigi, Ahli Bedah'],
                ['major' => 'Kartografi & Penginderaan Jauh (SIG)', 'field' => 'Sains Kebumian', 'prospect' => 'Spesialis GIS, Analis Citra Satelit, Surveyor'],
            ];
        } elseif ($dominant === 'AUDITORI') {
            $studyTips = "• Rekam suara penjelasan guru saat diizinkan dan dengarkan ulang di rumah sebagai pengulangan materi.\n• Bacalah teks bacaan dengan suara lirih/berbisik atau ajak teman berdiskusi dan saling menguji hafalan.\n• Manfaatkan media audio seperti podcast edukasi atau buku audio (audiobook).\n• Belajarlah di lingkungan yang tenang tanpa kebisingan latar belakang yang mengganggu.";
            $careers = [
                ['major' => 'Ilmu Komunikasi & Penyiaran', 'field' => 'Sosial Humaniora', 'prospect' => 'Penyiar Radio/TV, Podcaster, Public Relations Officer, MC'],
                ['major' => 'Ilmu Hukum (Advokasi & Peradilan)', 'field' => 'Hukum', 'prospect' => 'Pengacara/Advokat, Hakim, Jaksa Penuntut, Legal Mediator'],
                ['major' => 'Pendidikan Bahasa & Sastra', 'field' => 'Pendidikan', 'prospect' => 'Guru Bahasa Asing, Dosen Linguistik, Penerjemah Lisan (Interpreter)'],
                ['major' => 'Seni Musik & Audio Engineering', 'field' => 'Seni Pertunjukan', 'prospect' => 'Komposer Musik, Produser Musik, Teknisi Tata Suara'],
            ];
        } else { // KINESTETIK
            $studyTips = "• Selipkan jeda peregangan (active breaks) 3-5 menit setiap 25 menit sesi belajar fokus (Metode Pomodoro Aktif).\n• Praktikkan materi ke dalam simulasi nyata, buat proyek kecil atau kerjakan latihan soal langsung.\n• Gunakan alat peraga fisik, model 3D, atau belajar sambil berdiri/berjalan santai saat menghafal rumus.\n• Libatkan interaksi tangan seperti menulis rangkuman di papan tulis kecil.";
            $careers = [
                ['major' => 'Teknik Mesin / Teknik Elektro / Otomotif', 'field' => 'Teknik Terapan', 'prospect' => 'Insinyur Mekanik, Teknisi Robotika, Maintenance Specialist'],
                ['major' => 'Kedokteran & Keperawatan / Bedah', 'field' => 'Kesehatan Terapan', 'prospect' => 'Dokter Bedah, Fisioterapis, Perawat Kritis, Paramedis'],
                ['major' => 'Ilmu Keolahragaan & Fisioterapi', 'field' => 'Olahraga & Kesehatan', 'prospect' => 'Pelatih Atlet Profesional, Fisioterapis Olahraga, Guru PJOK'],
                ['major' => 'Pariwisata & Seni Kuliner (Gastronomi)', 'field' => 'Hospitality', 'prospect' => 'Executive Chef, Food Stylist, Manajer Operasional Hotel'],
            ];
        }

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
        ];
    }

    protected function generateRiasecAnalysis(string $hollandCode, string $dominant, ?string $secondary, array $percentages): array
    {
        $codeNames = [
            'R' => 'Realistic (Praktikal)',
            'I' => 'Investigative (Analitis)',
            'A' => 'Artistic (Kreatif)',
            'S' => 'Social (Pemberdayaan)',
            'E' => 'Enterprising (Wirausaha/Pemimpin)',
            'C' => 'Conventional (Terstruktur)',
        ];

        $summary = "Tipe kepribadian karier siswa berdasarkan teori John Holland terklasifikasi ke dalam kode **{$hollandCode}** ({$codeNames[$dominant]}). Siswa memiliki orientasi kuat terhadap lingkungan belajar dan kerja yang sesuai dengan kombinasi dimensi teratasnya.";

        $careers = [];
        $studyTips = '';

        // Mapping kombinasi kode Holland RIASEC ke rekomendasi jurusan & profesi
        if (str_contains($hollandCode, 'I') && str_contains($hollandCode, 'R')) {
            $careers = [
                ['major' => 'Teknik Informatika & Ilmu Komputer', 'field' => 'Sains & Teknologi', 'prospect' => 'Software Engineer, AI Developer, Data Scientist'],
                ['major' => 'Teknik Elektro / Mekatronika', 'field' => 'Rekayasa Teknik', 'prospect' => 'Robotika Engineer, Hardware Designer, Automation Specialist'],
                ['major' => 'Kedokteran Umum & Spesialis Bedah', 'field' => 'Ilmu Kesehatan', 'prospect' => 'Dokter Umum, Dokter Spesialis, Peneliti Biomedis'],
                ['major' => 'Teknik Perminyakan / Geologi', 'field' => 'Energi & Mineral', 'prospect' => 'Geolog Lapangan, Analis Eksplorasi, Reservoir Engineer'],
            ];
            $studyTips = 'Siswa unggul dalam memecahkan masalah praktikal berlandaskan teori ilmiah yang kokoh. Berikan tantangan problem-solving nyata dan proyek riset sains mandiri.';
        } elseif (str_contains($hollandCode, 'S') && str_contains($hollandCode, 'E')) {
            $careers = [
                ['major' => 'Manajemen Bisnis & Kewirausahaan', 'field' => 'Ekonomi Bisnis', 'prospect' => 'Business Development Manager, Start-up Founder, CEO'],
                ['major' => 'Ilmu Komunikasi & Hubungan Internasional', 'field' => 'Sosial Politik', 'prospect' => 'Diplomat, Corporate PR, Juru Bicara Pemerintah'],
                ['major' => 'Psikologi Industri & Organisasi', 'field' => 'Psikologi', 'prospect' => 'HRD Director, Talent Acquisition Head, Trainer Eksekutif'],
                ['major' => 'Ilmu Hukum Bisnis & Advokasi', 'field' => 'Hukum & Tata Kelola', 'prospect' => 'Konsultan Hukum Perusahaan, Notaris, Pengacara Bisnis'],
            ];
            $studyTips = 'Siswa memiliki jiwa kepemimpinan, kepiawaian interpersonal, dan persuasi verbal. Dorong keterlibatan dalam OSIS, debat bahasa, dan kepanitiaan sekolah.';
        } elseif (str_contains($hollandCode, 'A')) {
            $careers = [
                ['major' => 'Desain Komunikasi Visual (DKV) & Multimedia', 'field' => 'Industri Kreatif', 'prospect' => 'Art Director, Creative Director, Brand Identity Designer'],
                ['major' => 'Sastra & Penulisan Kreatif / Jurnalistik', 'field' => 'Humaniora', 'prospect' => 'Penulis Skenario, Jurnalis Investigasi, Editor Buku'],
                ['major' => 'Arsitektur & Desain Interior', 'field' => 'Rancang Bangun Seni', 'prospect' => 'Arsitek Konseptual, Scenographer, Spatial Designer'],
                ['major' => 'Film, Televisi & Seni Pertunjukan', 'field' => 'Media & Hiburan', 'prospect' => 'Sutradara Film, Produser Kreatif, Content Creator Profesional'],
            ];
            $studyTips = 'Beri ruang bagi ekspresi orisinalitas ide siswa. Siswa dapat kurang nyaman dengan aturan hafalan kaku; padukan pembelajaran dengan karya kreatif dan proyek multimedia.';
        } elseif (str_contains($hollandCode, 'C')) {
            $careers = [
                ['major' => 'Akuntansi Keuangan & Perpajakan', 'field' => 'Ekonomi & Keuangan', 'prospect' => 'Akuntan Publik (CPA), Auditor Finansial, Konsultan Pajak'],
                ['major' => 'Statistika Terapan & Sains Data', 'field' => 'Matematika Terapan', 'prospect' => 'Aktuaris Asuransi, Business Intelligence Analyst, Risk Assessor'],
                ['major' => 'Administrasi Publik & Tata Kelola Negara', 'field' => 'Ilmu Pemerintahan', 'prospect' => 'Analis Kebijakan Publik, Pejabat Arsiparis, Birokrat Madya'],
                ['major' => 'Sistem Informasi Manajemen & Keuangan', 'field' => 'Teknologi Bisnis', 'prospect' => 'Database Administrator, ERP Specialist, Compliance Officer'],
            ];
            $studyTips = 'Siswa menyukai ketelitian, jadwal yang pasti, dan data terstruktur. Berikan lembar kerja yang rapi dengan rubrik penilaian transparan dan target jelas.';
        } else {
            $careers = [
                ['major' => 'Psikologi Pendidikan & Bimbingan Konseling', 'field' => 'Pendidikan', 'prospect' => 'Konselor Sekolah, Praktisi Tumbuh Kembang, Terapis'],
                ['major' => 'Ilmu Kesehatan Masyarakat & Gizi Klinis', 'field' => 'Kesehatan Masyarakat', 'prospect' => 'Nutrisionis Rumah Sakit, Edukator Kesehatan, Epidemiolog'],
                ['major' => 'Sosiologi & Antropologi Terapan', 'field' => 'Ilmu Sosial', 'prospect' => 'Peneliti Sosial LSM, Manajer Pemberdayaan Desa, Sosiolog'],
                ['major' => 'Pendidikan Guru (PGSD / Guru Bidang Studi)', 'field' => 'Pendidikan Guru', 'prospect' => 'Pendidik Profesional, Pengembang Kurikulum, Fasilitator'],
            ];
            $studyTips = 'Fasilitasi siswa dengan kegiatan sosial, pendampingan teman sebaya (peer counseling), dan proyek bakti kemasyarakatan.';
        }

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
        ];
    }

    protected function generateGardnerAnalysis(string $dominant, ?string $secondary, ?string $tertiary, array $percentages): array
    {
        $names = [
            'LINGUISTIK' => 'Linguistik-Verbal',
            'LOGIS_MATEMATIS' => 'Logis-Matematis',
            'VISUAL_SPASIAL' => 'Visual-Spasial',
            'KINESTETIK' => 'Kinestetik-Jasmani',
            'MUSIKAL' => 'Musikal-Ritmik',
            'INTERPERSONAL' => 'Interpersonal (Sosial)',
            'INTRAPERSONAL' => 'Intrapersonal (Reflektif Diri)',
            'NATURALIS' => 'Naturalis (Lingkungan Alam)',
        ];

        $summary = "Berdasarkan kerangka Teori Kecerdasan Majemuk (Multiple Intelligences) Howard Gardner, siswa menunjukkan potensi istimewa pada dimensi **{$names[$dominant]}** ({$percentages[$dominant]}%), didukung oleh potensi **".($names[$secondary] ?? $secondary).'** ('.($percentages[$secondary] ?? 0).'%) dan **'.($names[$tertiary] ?? $tertiary).'** ('.($percentages[$tertiary] ?? 0).'%). Profil ini mengindikasikan bahwa setiap siswa memiliki kombinasi kecerdasan unik yang dapat diasah melalui pendekatan pembelajaran berdiferensiasi.';

        $careers = [
            ['major' => 'Program Studi Unggulan Berbasis '.$names[$dominant], 'field' => 'Akademik & Terapan', 'prospect' => 'Profesi Spesialis Bidang '.$names[$dominant]],
            ['major' => 'Program Studi Berbasis '.($names[$secondary] ?? 'Interdisipliner'), 'field' => 'Pengembangan Talenta', 'prospect' => 'Karier Sinergis Bidang '.($names[$secondary] ?? 'Terkait')],
        ];

        $studyTips = "Kembangkan kecerdasan dominan siswa sebagai 'pintu masuk' untuk memahami mata pelajaran yang dianggap sulit. Contoh: jika dominan Musikal, gunakan rima lagu untuk mengingat konsep; jika dominan Visual-Spasial, gunakan sketsa grafik konsep.";

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
        ];
    }
}
