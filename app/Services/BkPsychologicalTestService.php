<?php

namespace App\Services;

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestResult;
use App\Models\Student;

class BkPsychologicalTestService
{
    /**
     * Process student answers and generate comprehensive AI psychometric analysis,
     * item diagnostics, follow-up action plan (RTL BK), and authoritative scientific references.
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

        $itemAnalysis = $this->analyzeIndividualItems($test, $answers);

        $hollandCode = null;
        $profileLabel = '';
        $analysisSummary = '';
        $careerRecommendations = [];
        $studyRecommendations = '';
        $actionPlan = [];
        $scientificSources = [];

        if ($test->slug === 'vak') {
            $profileLabel = 'Gaya Belajar '.ucfirst(strtolower($dominant)).' Dominan';
            if ($secondary && abs($dimensionScores[$dominant] - $dimensionScores[$secondary]) <= 2) {
                $profileLabel = 'Gaya Belajar Multimodal ('.ucfirst(strtolower($dominant)).'-'.ucfirst(strtolower($secondary)).')';
            }

            $vakData = $this->generateVakAnalysis($dominant, $secondary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $vakData['summary'];
            $studyRecommendations = $vakData['study_tips'];
            $careerRecommendations = $vakData['careers'];
            $actionPlan = $vakData['action_plan'];
            $scientificSources = $vakData['scientific_sources'];
        } elseif ($test->slug === 'riasec') {
            $hollandCode = substr($dominant.$secondary.$tertiary, 0, 3);
            $profileLabel = "Tipe Karier Holland: {$hollandCode}";
            $riasecData = $this->generateRiasecAnalysis($hollandCode, $dominant, $secondary, $tertiary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $riasecData['summary'];
            $studyRecommendations = $riasecData['study_tips'];
            $careerRecommendations = $riasecData['careers'];
            $actionPlan = $riasecData['action_plan'];
            $scientificSources = $riasecData['scientific_sources'];
        } elseif ($test->slug === 'gardner') {
            $profileLabel = 'Kecerdasan Majemuk Dominan: '.str_replace('_', ' ', $dominant);
            $gardnerData = $this->generateGardnerAnalysis($dominant, $secondary, $tertiary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $gardnerData['summary'];
            $studyRecommendations = $gardnerData['study_tips'];
            $careerRecommendations = $gardnerData['careers'];
            $actionPlan = $gardnerData['action_plan'];
            $scientificSources = $gardnerData['scientific_sources'];
        } else {
            $profileLabel = 'Hasil Evaluasi Psikotes '.strtoupper($dominant);
            $analysisSummary = "Siswa menunjukkan dominansi pada aspek {$dominant} dengan capaian persentase {$dimensionPercentages[$dominant]}%.";
            $studyRecommendations = 'Dianjurkan untuk mengoptimalkan potensi dengan pendampingan guru BK dan wali kelas.';
            $actionPlan = $this->generateGenericActionPlan($dominant, $student);
            $scientificSources = $this->getGenericScientificSources();
        }

        // Simpan hasil tes lengkap dengan hasil analisis AI dan tindak lanjut
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
            'item_analysis' => $itemAnalysis,
            'career_recommendations' => $careerRecommendations,
            'study_recommendations' => $studyRecommendations,
            'action_plan' => $actionPlan,
            'scientific_sources' => $scientificSources,
            'status' => 'SUBMITTED',
            'ai_analyzed_at' => now(),
        ]);
    }

    /**
     * Re-generate or synthesize in-depth AI analysis and follow-up plan for an existing result.
     */
    public function generateAiAnalysisForResult(BkPsychologicalTestResult $result): BkPsychologicalTestResult
    {
        $result->load(['test.questions', 'student.schoolClass']);
        $test = $result->test;
        $student = $result->student;
        $answers = $result->answers ?: [];

        $dimensionScores = $result->dimension_scores['raw'] ?? [];
        $dimensionPercentages = $result->dimension_scores['percentages'] ?? [];

        // Jika skor belum terhitung, hitung ulang
        if (empty($dimensionScores) && $test->questions->isNotEmpty()) {
            $dimensionMax = [];
            foreach ($test->questions as $q) {
                $dim = $q->dimension;
                $dimensionScores[$dim] = ($dimensionScores[$dim] ?? 0) + (int) ($answers[$q->id] ?? 2);
                $dimensionMax[$dim] = ($dimensionMax[$dim] ?? 0) + 4;
            }
            foreach ($dimensionScores as $dim => $raw) {
                $max = $dimensionMax[$dim] ?: 1;
                $dimensionPercentages[$dim] = round(($raw / $max) * 100, 1);
            }
            arsort($dimensionScores);
        }

        $sortedDimensions = array_keys($dimensionScores);
        $dominant = $result->dominant_dimension ?: ($sortedDimensions[0] ?? '');
        $secondary = $result->secondary_dimension ?: ($sortedDimensions[1] ?? null);
        $tertiary = $result->tertiary_dimension ?: ($sortedDimensions[2] ?? null);

        $itemAnalysis = $this->analyzeIndividualItems($test, $answers);

        $hollandCode = $result->holland_code;
        $profileLabel = $result->profile_label;
        $analysisSummary = '';
        $careerRecommendations = [];
        $studyRecommendations = '';
        $actionPlan = [];
        $scientificSources = [];

        if ($test->slug === 'vak') {
            $profileLabel = 'Gaya Belajar '.ucfirst(strtolower($dominant)).' Dominan';
            if ($secondary && abs(($dimensionScores[$dominant] ?? 0) - ($dimensionScores[$secondary] ?? 0)) <= 2) {
                $profileLabel = 'Gaya Belajar Multimodal ('.ucfirst(strtolower($dominant)).'-'.ucfirst(strtolower($secondary)).')';
            }

            $vakData = $this->generateVakAnalysis($dominant, $secondary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $vakData['summary'];
            $studyRecommendations = $vakData['study_tips'];
            $careerRecommendations = $vakData['careers'];
            $actionPlan = $vakData['action_plan'];
            $scientificSources = $vakData['scientific_sources'];
        } elseif ($test->slug === 'riasec') {
            $hollandCode = substr($dominant.$secondary.$tertiary, 0, 3);
            $profileLabel = "Tipe Karier Holland: {$hollandCode}";
            $riasecData = $this->generateRiasecAnalysis($hollandCode, $dominant, $secondary, $tertiary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $riasecData['summary'];
            $studyRecommendations = $riasecData['study_tips'];
            $careerRecommendations = $riasecData['careers'];
            $actionPlan = $riasecData['action_plan'];
            $scientificSources = $riasecData['scientific_sources'];
        } elseif ($test->slug === 'gardner') {
            $profileLabel = 'Kecerdasan Majemuk Dominan: '.str_replace('_', ' ', $dominant);
            $gardnerData = $this->generateGardnerAnalysis($dominant, $secondary, $tertiary, $dimensionPercentages, $itemAnalysis, $student);
            $analysisSummary = $gardnerData['summary'];
            $studyRecommendations = $gardnerData['study_tips'];
            $careerRecommendations = $gardnerData['careers'];
            $actionPlan = $gardnerData['action_plan'];
            $scientificSources = $gardnerData['scientific_sources'];
        } else {
            $profileLabel = 'Hasil Evaluasi Psikotes '.strtoupper($dominant);
            $analysisSummary = "Siswa menunjukkan dominansi pada aspek {$dominant} dengan capaian persentase {$dimensionPercentages[$dominant]}%.";
            $studyRecommendations = 'Dianjurkan untuk mengoptimalkan potensi dengan pendampingan guru BK dan wali kelas.';
            $actionPlan = $this->generateGenericActionPlan($dominant, $student);
            $scientificSources = $this->getGenericScientificSources();
        }

        $result->update([
            'profile_label' => $profileLabel,
            'holland_code' => $hollandCode,
            'dominant_dimension' => $dominant,
            'secondary_dimension' => $secondary,
            'tertiary_dimension' => $tertiary,
            'dimension_scores' => [
                'raw' => $dimensionScores,
                'percentages' => $dimensionPercentages,
            ],
            'analysis_summary' => $analysisSummary,
            'item_analysis' => $itemAnalysis,
            'career_recommendations' => $careerRecommendations,
            'study_recommendations' => $studyRecommendations,
            'action_plan' => $actionPlan,
            'scientific_sources' => $scientificSources,
            'ai_analyzed_at' => now(),
        ]);

        return $result;
    }

    /**
     * Analisis Butir Soal Terperinci (Item-Level Diagnostic).
     * Mengidentifikasi respons dengan skor tertinggi (indikator kekuatan utama)
     * dan skor terendah (faktor yang kurang disukai / area distraksi).
     */
    protected function analyzeIndividualItems(BkPsychologicalTest $test, array $answers): array
    {
        $scores = [];
        foreach ($test->questions as $q) {
            $score = isset($answers[$q->id]) ? (int) $answers[$q->id] : 0;
            $scores[] = [
                'question_id' => $q->id,
                'question_number' => $q->question_number,
                'dimension' => $q->dimension,
                'dimension_name' => $q->dimension_name ?? $q->dimension,
                'statement' => $q->question_text,
                'text' => $q->question_text,
                'score' => $score,
            ];
        }

        // Urutkan skor tertinggi untuk mengidentifikasi indikator kekuatan utama
        $sortedDesc = $scores;
        usort($sortedDesc, fn ($a, $b) => $b['score'] <=> $a['score']);
        $strongestItems = array_map(function ($item) {
            $item['interpretation'] = 'Indikator perilaku / preferensi belajar yang sangat kuat dan konsisten.';
            $item['meaning'] = $item['interpretation'];

            return $item;
        }, array_slice($sortedDesc, 0, 5));

        // Urutkan skor terendah untuk mengidentifikasi area pertumbuhan / preferensi yang kurang diminati
        $sortedAsc = $scores;
        usort($sortedAsc, fn ($a, $b) => $a['score'] <=> $b['score']);
        $growthItems = array_map(function ($item) {
            $item['recommendation'] = 'Aktivitas atau stimulus yang kurang diminati; perlukan variasi metode atau pendampingan adaptif.';
            $item['meaning'] = $item['recommendation'];

            return $item;
        }, array_slice($sortedAsc, 0, 4));

        return [
            'total_items_answered' => count($scores),
            'strongest_count' => count(array_filter($scores, fn ($s) => $s['score'] >= 4)),
            'growth_count' => count(array_filter($scores, fn ($s) => $s['score'] <= 2)),
            'top_strengths' => $strongestItems,
            'growth_areas' => $growthItems,
            'least_preferred' => $growthItems,
        ];
    }

    /**
     * Evaluasi Lengkap Tes Gaya Belajar VAK (Visual, Auditori, Kinestetik)
     */
    protected function generateVakAnalysis(string $dominant, ?string $secondary, array $percentages, array $itemAnalysis, Student $student): array
    {
        $vPct = $percentages['VISUAL'] ?? 0;
        $aPct = $percentages['AUDITORI'] ?? 0;
        $kPct = $percentages['KINESTETIK'] ?? 0;

        $dominantName = match ($dominant) {
            'VISUAL' => 'Visual (Spatial-Visual Modality)',
            'AUDITORI' => 'Auditori (Auditory-Verbal Modality)',
            'KINESTETIK' => 'Kinestetik (Tactile-Kinesthetic Modality)',
            default => $dominant,
        };

        $secondaryName = match ($secondary) {
            'VISUAL' => 'Visual',
            'AUDITORI' => 'Auditori',
            'KINESTETIK' => 'Kinestetik',
            default => $secondary ?: '-',
        };

        $summary = "Berdasarkan pemrosesan instrumen Asesmen Modalitas Belajar VAK, peserta didik ananda **{$student->name}** menunjukkan profil modalitas sensorik primer **{$dominantName}** dengan skor **{$percentages[$dominant]}%**, didukung oleh modalitas sekunder **{$secondaryName}** (".($percentages[$secondary] ?? 0).'%) dan modalitas tertier ('.($percentages[array_diff(['VISUAL', 'AUDITORI', 'KINESTETIK'], [$dominant, $secondary])[0] ?? 'KINESTETIK'] ?? 0)."%).\n\n";

        if ($dominant === 'VISUAL') {
            $summary .= "Siswa memproses informasi paling optimal melalui stimulasi indra penglihatan. Siswa memiliki daya rekam spasial yang tajam terhadap bagan, pola grafik, warna stabilo, dan demonstrasi visual. Namun, konsentrasi siswa rentan terdistraksi oleh 'polusi visual' seperti ruangan/meja belajar yang berantakan atau materi yang disajikan berupa ceramah suara panjang tanpa bantuan proyektor/gambar.";
            $studyTips = "1. Gunakan teknik **Mind Mapping (Peta Konsep)** berwarna-warni dan diagram alur untuk merangkum bab pelajaran yang padat.\n2. Beri kode warna khusus (highlighter/stabilo) pada kata kunci, definisi hukum/rumus penting, dan kesimpulan.\n3. Ubah catatan teks panjang menjadi infografis sederhana, grafik perbandingan, atau kartu kilas visual (flashcards).\n4. Jaga kerapian meja belajar fisik dan layar laptop dari barang-barang berserakan untuk mempertahankan fokus visual.\n5. Tonton video pembelajaran beranimasi 3D atau simulasi grafis sebelum membaca naskah buku cetak yang tebal.";
            $careers = [
                ['major' => 'Desain Komunikasi Visual (DKV) & Animasi 3D', 'field' => 'Industri Kreatif & Digital', 'prospect' => 'Creative Director, Animator 3D, UI/UX Designer, Motion Graphic Artist'],
                ['major' => 'Arsitektur & Perencanaan Wilayah Kota (PWK)', 'field' => 'Rancang Bangun & Spasial', 'prospect' => 'Arsitek Bangunan, Urban Designer, Spatial Data Analyst, Interior Specialist'],
                ['major' => 'Kedokteran Gigi & Bedah Radiologi', 'field' => 'Ilmu Kesehatan Eksakta', 'prospect' => 'Dokter Gigi Spesialis, Radiografer Diagnostik, Spesialis Rekonstruksi'],
                ['major' => 'Geomatika & Penginderaan Jauh (Sistem Informasi Geografis / SIG)', 'field' => 'Sains Kebumian & Spasial', 'prospect' => 'GIS Specialist, Analis Citra Satelit Penginderaan Jauh, Surveyor Pemetaan'],
            ];
        } elseif ($dominant === 'AUDITORI') {
            $summary .= 'Siswa memproses dan mengingat materi pelajaran paling mudah melalui jalur pendengaran dan komunikasi lisan. Siswa sangat peka terhadap nada bicara, intonasi, ritme kata guru, dan mampu menyerap intisari konsep saat berdiskusi lisan. Sebaliknya, keheningan mutlak atau suasana gaduh berisik dapat mengurangi ketajaman berpikirnya; siswa lebih terbantu jika diizinkan membaca sambil bergumam lirih atau mengulang hafalan dengan suara.';
            $studyTips = "1. Rekam penjelasan materi esensial dari guru (dengan izin) untuk didengarkan ulang di rumah melalui earphone.\n2. Bacalah teks buku bacaan secara lisan dengan suara berbisik atau intonasi jelas untuk memperkuat retensi memori pendengaran.\n3. Bentuk kelompok belajar kecil untuk saling mengajukan pertanyaan lisan (peer-quizzing) dan bertukar argumen.\n4. Manfaatkan media podcast edukasi, rekaman diskusi ilmiah, atau fitur text-to-speech saat membaca artikel jurnal.\n5. Hindari belajar sambil mendengarkan musik berlirik bahasa yang sama dengan materi yang sedang dihafalkan karena menimbulkan interferensi fonologis.";
            $careers = [
                ['major' => 'Ilmu Komunikasi & Jurnalistik Penyiaran', 'field' => 'Media & Hubungan Publik', 'prospect' => 'News Anchor, Public Relations Officer, Juru Bicara Institusi, Podcast Producer'],
                ['major' => 'Ilmu Hukum (Advokasi Peradilan & Litigasi)', 'field' => 'Hukum & Tata Peradilan', 'prospect' => 'Advokat/Pengacara Persidangan, Hakim, Jaksa Penuntut, Mediator Hukum'],
                ['major' => 'Pendidikan Bahasa Asing & Linguistik Terapan', 'field' => 'Pendidikan & Bahasa', 'prospect' => 'Penerjemah Lisan Simultan (Conference Interpreter), Dosen Linguistik, Diplomat'],
                ['major' => 'Teknik Tata Suara & Audio Engineering', 'field' => 'Teknologi Akustik', 'prospect' => 'Sound Engineer Profesional, Akustik Ruang Studio, Produser Musik Audio'],
            ];
        } else { // KINESTETIK
            $summary .= 'Siswa memproses informasi paling efektif melalui keterlibatan fisik, gerakan motorik, sentuhan alat peraga konkret, dan uji coba langsung (*hands-on learning*). Siswa cepat merasa jenuh, lelah, atau gelisah jika dituntut duduk statis mendengarkan ceramah satu arah lebih dari 25-30 menit. Belajar sambil berdiri, berjalan santai, atau langsung mempraktikkan ke dalam latihan soal studi kasus adalah kunci lonjakan prestasinya.';
            $studyTips = "1. Terapkan **Metode Pomodoro Aktif**: Belajar fokus 25 menit diselingi 5 menit jeda peregangan fisik/jalan santai.\n2. Ubah konsep teori menjadi proyek fisik, manipulasi peraga 3D, atau simulasi peran (role-play).\n3. Belajarlah sambil berdiri di dekat papan tulis dinding kecil untuk mencoret-coret rumus dan alur logika berpikir.\n4. Saat menghafal definisi atau rumus, gunakan gerakan jari tangan atau berjalan perlahan mengitari ruangan.\n5. Utamakan metode *learning by doing*: kerjakan latihan soal secara langsung daripada hanya pasif membaca teori.";
            $careers = [
                ['major' => 'Teknik Mesin, Otomasi & Robotika Terapan', 'field' => 'Rekayasa Manufaktur', 'prospect' => 'Automation Engineer, Teknisi Robotika Industri, Maintenance Superintendent'],
                ['major' => 'Pendidikan Dokter Bedah & Fisioterapi Klinis', 'field' => 'Kesehatan Terapan Presisi', 'prospect' => 'Dokter Spesialis Bedah, Fisioterapis Rehabilitasi Medis, Paramedis Lapangan'],
                ['major' => 'Ilmu Keolahragaan & Manajemen Prestasi Atlet', 'field' => 'Sport Science & Kebugaran', 'prospect' => 'Pelatih Fisik Profesional, Sport Biomechanist, Manajer Kebugaran'],
                ['major' => 'Seni Kuliner Modern (Gastronomi) & Manajemen Perhotelan', 'field' => 'Hospitality & Culinary Arts', 'prospect' => 'Executive Chef Hotel Berbintang, Food Product Developer, Manajer F&B'],
            ];
        }

        $actionPlan = [
            'bidang_pribadi' => [
                'tujuan' => 'Peningkatan kesadaran diri (*self-awareness*) atas keunikan modalitas belajar tanpa rasa rendah diri.',
                'layanan' => 'Layanan Bimbingan Pribadi: Refleksi kekuatan diri dan adaptasi terhadap beragam metode mengajar guru.',
            ],
            'bidang_belajar' => [
                'tujuan' => "Implementasi strategi belajar multisensori berbasis preferensi dominan ({$dominant}).",
                'layanan' => "Bimbingan Kelompok Belajar: Praktik penyusunan jadwal belajar Pomodoro dan teknik pencatatan adaptif ({$dominant}).",
            ],
            'bidang_karier' => [
                'tujuan' => "Penyelarasan pemilihan kegiatan ekstrakurikuler dan program studi lanjut yang relevan dengan tipe {$dominant}.",
                'layanan' => 'Konseling Karier Individual: Eksplorasi program studi yang mengutamakan unjuk kerja aplikatif dan portofolio.',
            ],
            'kolaborasi_guru_mapel' => [
                'tujuan' => 'Pembelajaran Berdiferensiasi sesuai Kurikulum Merdeka (Kepmendikbudristek No. 009/H/KR/2022).',
                'rekomendasi' => "Guru Mata Pelajaran disarankan menyediakan diferensiasi konten/proses: memadukan tayangan visual grafis, penjelasan lisan interaktif, dan lembar kerja praktik mandiri agar siswa tipe {$dominant} dapat mencapai Capaian Pembelajaran secara optimal.",
            ],
            'peran_orang_tua' => [
                'tujuan' => 'Penyediaan ekosistem belajar yang kondusif di rumah sesuai kebutuhan sensorik anak.',
                'rekomendasi' => ($dominant === 'VISUAL')
                    ? 'Orang tua memfasilitasi meja belajar yang rapi, penerangan cukup, serta perlengkapan visual (stabilo, kertas mind-map, papan tulis dinding).'
                    : (($dominant === 'AUDITORI')
                        ? 'Orang tua memberikan ruang belajar yang bebas kebisingan suara televisi/gadget, serta mengajak anak menceritakan kembali apa yang telah dipelajarinya di sekolah.'
                        : 'Orang tua memahami kebutuhan anak yang aktif bergerak; hindari memarahi anak saat belajar sambil berjalan santai atau memegang benda di tangan.'),
            ],
            'indikator_evaluasi' => [
                'indikator' => 'Kenyamanan belajar meningkat, durasi retensi konsentrasi belajar di rumah bertambah minimal 40%, dan capaian nilai asesmen formatif stabil di atas Kriteria Ketercapaian Tujuan Pembelajaran (KKTP).',
                'waktu_evaluasi' => 'Pemantauan berkala setiap 4 minggu oleh Guru BK dan Wali Kelas.',
            ],
        ];

        $scientificSources = [
            'theory_name' => 'Teori Modalitas Belajar Visual, Auditori, Kinestetik (VAK) & Accelerated Learning',
            'pioneers' => 'Walter B. Barbe, Ph.D. & Raymond H. Swassing (Ohio State University, 1979); Bobbi DePorter (Quantum Learning Network, 1999); Neil D. Fleming (Lincoln University, 1992).',
            'primary_literature' => [
                'Barbe, W. B., Swassing, R. H., & Milone, M. N. (1979). Teaching Through Modality Strengths: Concepts and Practices. Zaner-Bloser.',
                'DePorter, B., Reardon, M., & Singer-Nourie, S. (1999). Quantum Learning: Unleashing the Genius in You. Dell Publishing.',
                'Fleming, N. D., & Mills, C. (1992). Not Another Inventory, Rather a Catalyst for Reflection. To Improve the Academy, 11(1), 137-155.',
            ],
            'regulatory_framework' => [
                'Badan Standar, Kurikulum, dan Asesmen Pendidikan (BSKAP) Kemendikbudristek RI No. 009/H/KR/2022 tentang Panduan Pembelajaran dan Asesmen (Prinsip Pembelajaran Berdiferensiasi).',
                'Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) Sekolah Menengah Atas (SMA), Dirjen GTK Kemendikbud RI.',
                'Standar Kompetensi Kemandirian Peserta Didik (SKKPD) Asosiasi Bimbingan dan Konseling Indonesia (ABKIN).',
            ],
        ];

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
            'action_plan' => $actionPlan,
            'scientific_sources' => $scientificSources,
        ];
    }

    /**
     * Evaluasi Lengkap Tes Minat Bakat Karier Holland RIASEC
     */
    protected function generateRiasecAnalysis(string $hollandCode, string $dominant, ?string $secondary, ?string $tertiary, array $percentages, array $itemAnalysis, Student $student): array
    {
        $codeNames = [
            'R' => 'Realistic (Praktikal, Teknik & Mekanikal)',
            'I' => 'Investigative (Analitis, Sains & Riset Ilmiah)',
            'A' => 'Artistic (Kreatif, Seni & Desain Ekspresif)',
            'S' => 'Social (Pemberdayaan, Edukasi & Pelayanan Sosial)',
            'E' => 'Enterprising (Kepemimpinan, Manajemen & Wirausaha)',
            'C' => 'Conventional (Keteraturan, Data & Administrasi Terstruktur)',
        ];

        $dominantFull = $codeNames[$dominant] ?? $dominant;
        $secondaryFull = $codeNames[$secondary] ?? ($secondary ?: '-');
        $tertiaryFull = $codeNames[$tertiary] ?? ($tertiary ?: '-');

        $summary = "Berdasarkan inventori minat vokasional dan tipologi kepribadian karier John L. Holland, profil kepribadian vokasional peserta didik ananda **{$student->name}** teridentifikasi ke dalam kode tiga huruf (**Holland Code**) **{$hollandCode}**.\n\n";
        $summary .= "Struktur dominansi minat siswa dipimpin oleh dimensi **{$dominantFull}** ({$percentages[$dominant]}%), disokong kuat oleh dimensi kedua **{$secondaryFull}** (".($percentages[$secondary] ?? 0)."%) dan dimensi ketiga **{$tertiaryFull}** (".($percentages[$tertiary] ?? 0).'%). Pola kombinasi kode ini mencerminkan lingkungan belajar, budaya kerja, dan atmosfer akademik yang paling kondusif untuk menumbuhkan kepuasan serta performa terbaik siswa di masa depan.';

        // Mapping terperinci jurusan kuliah PTN (SNBP / SNBT) dan prospek profesi masa depan
        $careers = $this->getDetailedRiasecCareers($hollandCode, $dominant, $secondary);

        $studyTips = match ($dominant) {
            'R' => "• Terapkan pembelajaran berbasis proyek fisik dan studi rekayasa terapan.\n• Rancang purwarupa nyata dari materi teori fisika/matematika untuk meningkatkan daya tangkap konsep.\n• Kembangkan keterampilan teknologi digital seperti CAD, perangkat IoT, atau simulasi mekanik.",
            'I' => "• Fasilitasi siswa dengan bacaan literatur sains mutakhir, jurnal riset, dan kompetisi karya ilmiah remaja (KIR).\n• Berikan kebebasan mengeksplorasi akar permasalahan melalui metodologi riset ilmiah empiris dan data statistik.\n• Dorong kebiasaan berpikir komputasional (coding algoritma dan analisis pemodelan data).",
            'A' => "• Berikan ruang terbuka bagi ekspresi ide kreatif orisinal tanpa penghakiman aturan baku yang kaku.\n• Integrasikan penugasan dalam bentuk portofolio karya seni, naskah narasi, film pendek, atau desain multimedia.\n• Ikutsertakan dalam kompetisi FLS2N, pameran karya visual, atau festival pertunjukan sekolah.",
            'S' => "• Libatkan siswa dalam kepanitiaan bakti sosial, tutor sebaya (*peer-tutoring*), atau kader kesehatan remaja/PMR.\n• Kembangkan kemampuan komunikasi empati dan penyelesaian konflik (*conflict resolution*).\n• Dorong kebiasaan membaca psikologi hubungan manusia dan dinamika sosial kemasyarakatan.",
            'E' => "• Berikan panggung kepemimpinan dalam OSIS, ekstrakurikuler, atau kegiatan simulasi wirausaha siswa.\n• Asah kemampuan persuasi verbal, retorika debat, dan *public speaking* di depan umum.\n• Pelajari dasar-dasar perencanaan bisnis (*business plan canvas*) dan analisis pasar digital.",
            'C' => "• Berikan lembar kerja yang terstruktur dengan instruksi, jadwal, dan kriteria penilaian (*rubrik*) yang jelas.\n• Libatkan siswa dalam manajemen kepengurusan administrasi organisasi kelas atau pengelolaan kas.\n• Latih kemampuan pengolahan data spreadsheet tingkat lanjut (Excel/Google Sheets) dan akuntansi dasar.",
            default => 'Kembangkan kompetensi akademik terarah dengan pendampingan intensif dari guru BK dan guru mata pelajaran.',
        };

        $actionPlan = [
            'bidang_karier' => [
                'tujuan' => "Perencanaan peminatan studi lanjut perguruan tinggi (PTN/Kedinasan) yang kongruen dengan kode {$hollandCode}.",
                'layanan' => 'Layanan Peminatan dan Perencanaan Individual: Eksplorasi program studi target SNBP/SNBT, portofolio prestasi, dan pemetaan mata pelajaran pendukung Fase F Kurikulum Merdeka.',
            ],
            'bidang_belajar' => [
                'tujuan' => 'Peningkatan nilai rapor semester 1-5 pada mata pelajaran esensial yang linier dengan program studi tujuan.',
                'layanan' => 'Bimbingan Belajar Terarah: Pendampingan target capaian belajar mata pelajaran pilihan (Fase F) bersama Wali Kelas.',
            ],
            'bidang_sosial' => [
                'tujuan' => 'Pengembangan kecakapan abad 21 (komunikasi, kolaborasi, dan kepemimpinan) di lingkungan sekolah.',
                'layanan' => 'Bimbingan Kelompok: Simulasi wawancara seleksi beasiswa / perguruan tinggi dan *focus group discussion* (FGD).',
            ],
            'kolaborasi_guru_mapel' => [
                'tujuan' => "Sinkronisasi proyek asesmen di kelas dengan orientasi minat karier siswa ({$hollandCode}).",
                'rekomendasi' => "Guru Mapel dapat mengarahkan topik tugas/proyek akhir semester siswa agar kontekstual dengan peminatan bidang {$dominantFull}, sehingga dapat dijadikan modal portofolio pendaftaran universitas.",
            ],
            'peran_orang_tua' => [
                'tujuan' => 'Penyelarasan ekspektasi keluarga dengan potensi minat dan bakat nyata peserta didik.',
                'rekomendasi' => 'Orang tua dianjurkan berdiskusi terbuka dengan anak dan Guru BK mengenai rencana kuliah tanpa memaksakan jurusan di luar kecenderungan bakat anak. Berikan dukungan moral dan fasilitasi eksplorasi kampus/profesi target.',
            ],
            'indikator_evaluasi' => [
                'indikator' => 'Siswa memiliki dokumen Rencana Studi dan Karier Masa Depan (Career Action Plan) yang terarah, mantap memilih mata pelajaran pilihan Fase F, serta siap menghadapi seleksi perguruan tinggi.',
                'waktu_evaluasi' => 'Sesi konseling karier individual terjadwal pada pertengahan semester berjalan.',
            ],
        ];

        $scientificSources = [
            'theory_name' => 'Teori Kepribadian Karier & Lingkungan Kerja Heksagonal Holland (RIASEC Theory of Vocational Personalities)',
            'pioneers' => 'Prof. John L. Holland, Ph.D. (Johns Hopkins University, 1959, 1997); Self-Directed Search (SDS) Assessment System.',
            'primary_literature' => [
                'Holland, J. L. (1997). Making Vocational Choices: A Theory of Vocational Personalities and Work Environments (3rd ed.). Psychological Assessment Resources.',
                'Gottfredson, G. D., & Holland, J. L. (1996). Dictionary of Holland Occupational Codes (3rd ed.). Psychological Assessment Resources.',
                'Nauta, M. M. (2010). The Development, Evolution, and Status of Holland’s Theory of Vocational Personalities: Reflection and Future Directions. Journal of Career Assessment, 18(1), 11-22.',
            ],
            'regulatory_framework' => [
                'Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) Sekolah Menengah Atas (SMA), Dirjen Guru dan Tenaga Kependidikan Kemendikbud RI (Bidang Bimbingan Karier).',
                'Standar Kompetensi Kemandirian Peserta Didik (SKKPD) Asosiasi Bimbingan dan Konseling Indonesia (ABKIN) - Aspek Kematangan Vokasional & Wawasan Karier.',
                'Keputusan Mendikbudristek No. 345/M/2022 tentang Petunjuk Teknis Pelaksanaan Kurikulum Merdeka (Mekanisme Pemilihan Mata Pelajaran Pilihan Fase F).',
            ],
        ];

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
            'action_plan' => $actionPlan,
            'scientific_sources' => $scientificSources,
        ];
    }

    /**
     * Pemetaan Rekomendasi Jurusan Kuliah PTN & Profesi Holland RIASEC
     */
    protected function getDetailedRiasecCareers(string $hollandCode, string $dominant, ?string $secondary): array
    {
        $pair = substr($dominant.$secondary, 0, 2);

        $careerMap = [
            'IR' => [
                ['major' => 'Teknik Informatika & Ilmu Komputer (Informatics)', 'field' => 'Sains & Teknologi Data', 'prospect' => 'Software Architect, Machine Learning Engineer, Cyber Security Analyst'],
                ['major' => 'Teknik Elektro / Teknik Mekatronika', 'field' => 'Rekayasa Instrumentasi', 'prospect' => 'Robotics Engineer, IoT System Developer, Hardware Design Specialist'],
                ['major' => 'Pendidikan Dokter Umum & Spesialis Bedah', 'field' => 'Ilmu Kedokteran & Kesehatan', 'prospect' => 'Dokter Bedah, Peneliti Biomedis, Spesialis Patologi Klinik'],
                ['major' => 'Teknik Geologi & Eksplorasi Geofisika', 'field' => 'Energi & Sumber Daya Bumi', 'prospect' => 'Geolog Lapangan, Analis Seismik Eksplorasi, Reservoir Modeler'],
            ],
            'RI' => [
                ['major' => 'Teknik Mesin & Dirgantara (Aeronautika)', 'field' => 'Rekayasa Manufaktur Berat', 'prospect' => 'Aviation Engineer, Desainer Mekanik Presisi, Power Plant Engineer'],
                ['major' => 'Teknik Sipil & Infrastruktur Transportasi', 'field' => 'Rancang Bangun Konstruksi', 'prospect' => 'Structural Engineer, Project Highway Engineer, Analis Geoteknik'],
                ['major' => 'Teknologi Laboratorium Medik & Biomedik', 'field' => 'Sains Diagnostik Medis', 'prospect' => 'Pranata Laboratorium Medis, Ahli Bioteknologi Terapan, Formulator'],
                ['major' => 'Ilmu Kelautan & Oseanografi Terapan', 'field' => 'Sains Maritim Eksplorasi', 'prospect' => 'Oseanografer Lapangan, Analis Sumber Daya Pesisir, Peneliti Terumbu'],
            ],
            'IA' => [
                ['major' => 'Arsitektur Rancang Bangun Digital', 'field' => 'Perancangan Visual & Struktur', 'prospect' => 'BIM Architect, Spatial Concept Designer, Computational Architect'],
                ['major' => 'Desain Antarmuka Pengguna (UI/UX) & Interaksi Manusia Komputer', 'field' => 'Teknologi Desain Digital', 'prospect' => 'Lead UX Researcher, Product Designer, Usability Ergonomics Analyst'],
                ['major' => 'Psikologi Eksperimental & Sains Kognitif', 'field' => 'Psikologi & Neurosains', 'prospect' => 'Cognitive Psychologist, Behavioral Data Analyst, Peneliti Neuroedukasi'],
                ['major' => 'Biokimia & Teknologi Kosmetika Farmasi', 'field' => 'Sains R&D Formulasi', 'prospect' => 'Cosmetic Chemist, Analis Formulasi Herbal, R&D Product Specialist'],
            ],
            'IS' => [
                ['major' => 'Pendidikan Dokter & Kesehatan Masyarakat', 'field' => 'Pelayanan Kesehatan Holistik', 'prospect' => 'Dokter Puskesmas/RS, Epidemiolog Lapangan, Health Educator'],
                ['major' => 'Psikologi Klinis & Konseling Remaja', 'field' => 'Kesehatan Mental & Perilaku', 'prospect' => 'Psikolog Klinis Anak/Remaja, Terapis Perilaku, Konselor Rehabilitasi'],
                ['major' => 'Pendidikan Sains (Fisika / Kimia / Biologi / Matematika)', 'field' => 'Pendidikan Akademik', 'prospect' => 'Guru Sains Berprestasi, Dosen Akademisi, Penulis Naskah Soal Sains'],
                ['major' => 'Farmasi Klinis & Komunitas', 'field' => 'Pelayanan Farmakologi', 'prospect' => 'Apoteker Rumah Sakit, Edukator Farmasi, Konsultan Terapi Obat'],
            ],
            'IE' => [
                ['major' => 'Sains Data Bisnis (Business Analytics & Big Data)', 'field' => 'Teknologi Analisis Strategis', 'prospect' => 'Chief Data Officer, Strategic Business Analyst, Quantitative Strategist'],
                ['major' => 'Manajemen Rekayasa Industri & Rantai Pasok', 'field' => 'Optimasi Sistem & Operasional', 'prospect' => 'Supply Chain Manager, Quality Assurance Director, Lean Consultant'],
                ['major' => 'Aktuaria & Sains Risiko Finansial', 'field' => 'Keuangan Matematika Terapan', 'prospect' => 'Aktuaris Asuransi Jiwa, Financial Risk Manager, Investment Analyst'],
                ['major' => 'Bioteknologi Industri & Bioentrepreneurship', 'field' => 'Inovasi Biologi Komersial', 'prospect' => 'Biotech Venture Manager, Peneliti R&D Pangan, Bio-Patents Analyst'],
            ],
            'SE' => [
                ['major' => 'Manajemen Sumber Daya Manusia (SDM) & Psikologi Industri', 'field' => 'Tata Kelola Kepemimpinan', 'prospect' => 'Human Capital Director, Talent Acquisition Manager, Executive Trainer'],
                ['major' => 'Ilmu Komunikasi Korporat & Public Relations (PR)', 'field' => 'Relasi Media & Diplomasi', 'prospect' => 'Head of Corporate Communications, Brand Ambassador, Juru Bicara'],
                ['major' => 'Ilmu Hukum Bisnis & Tata Negara', 'field' => 'Hukum & Tata Kebijakan', 'prospect' => 'Corporate Legal Counsel, Konsultan Regulasi Pemerintah, Notaris Publik'],
                ['major' => 'Hubungan Internasional & Diplomasi Global', 'field' => 'Hubungan Antar Bangsa', 'prospect' => 'Diplomat Kementerian Luar Negeri, Policy Analyst PBB, Peace Negotiator'],
            ],
            'SI' => [
                ['major' => 'Psikologi Klinis & Konseling Pendidikan', 'field' => 'Layanan Kesehatan Jiwa', 'prospect' => 'Konselor Sekolah Profesional, Psikolog Perkembangan, Assessor Talenta'],
                ['major' => 'Kesejahteraan Sosial & Pengembangan Komunitas', 'field' => 'Pemberdayaan Kemasyarakatan', 'prospect' => 'Manajer Program LSM/NGO, Community Development Officer, Pekerja Sosial'],
                ['major' => 'Pendidikan Luar Biasa (Inklusi / PLB)', 'field' => 'Edukasi Khusus Terpadu', 'prospect' => 'Spesialis Pendidikan Inklusif, Terapis Okupasi Anak, Konsultan ABK'],
                ['major' => 'Sosiologi Terapan & Riset Kebijakan Publik', 'field' => 'Analisis Sosial Budaya', 'prospect' => 'Peneliti Dampak Sosial Bappenas, Analis Kebijakan Sosial, Sosiolog'],
            ],
            'SA' => [
                ['major' => 'Desain Komunikasi Visual untuk Edukasi & Sosial', 'field' => 'Kreatif Berdampak Sosial', 'prospect' => 'Social Campaign Creative, Animator Edukasi Anak, Creative Illustrator'],
                ['major' => 'Sastra & Pengajaran Bahasa Asing', 'field' => 'Bahasa & Kebudayaan', 'prospect' => 'Guru Bahasa Internasional, Editor Buku Sastra, Penerjemah Kebudayaan'],
                ['major' => 'Terapi Seni & Konseling Kreatif (Art Therapy)', 'field' => 'Kesehatan Mental Komplementer', 'prospect' => 'Art Therapist, Konselor Trauma Remaja, Pengembang Terapi Ekspresif'],
                ['major' => 'Seni Musik Terapan & Terapi Vokal', 'field' => 'Seni Terapi Pertunjukan', 'prospect' => 'Music Therapist, Guru Vokal Paduan Suara, Instruktur Harmoni Anak'],
            ],
            'AS' => [
                ['major' => 'Desain Produk Industri & Kriya Kreatif', 'field' => 'Industri Seni Terapan', 'prospect' => 'Product Designer, Art Director, Kurator Galeri Seni, Ceramist'],
                ['major' => 'Seni Rupa Murni & Pendidikan Seni', 'field' => 'Seni Rupa & Pembelajaran', 'prospect' => 'Pendidik Seni Rupa, Seniman Kontemporer, Visual Merchandiser'],
                ['major' => 'Jurnalistik Investigasi & Penulisan Kreatif', 'field' => 'Media Cetak & Digital', 'prospect' => 'Penulis Naskah Skenario, Jurnalis Human Interest, Editor Naskah'],
                ['major' => 'Perfilman, Televisi & Seni Pertunjukan', 'field' => 'Sinematografi & Media', 'prospect' => 'Sutradara Film Dokumenter, Produser Konten Kreatif, Pengarah Panggung'],
            ],
            'EC' => [
                ['major' => 'Akuntansi Manajerial & Perpajakan Korporat', 'field' => 'Keuangan Bisnis Strategis', 'prospect' => 'Financial Controller, Chief Financial Officer (CFO), Konsultan Pajak'],
                ['major' => 'Manajemen Bisnis & Kewirausahaan Digital', 'field' => 'Pengembangan Usaha', 'prospect' => 'Start-up Founder, Business Development Manager, Retail Operations Lead'],
                ['major' => 'Manajemen Perbankan & Pasar Modal', 'field' => 'Sektor Keuangan & Investasi', 'prospect' => 'Investment Banker, Branch Manager Bank, Wealth Portfolio Advisor'],
                ['major' => 'Administrasi Niaga & Perdagangan Internasional', 'field' => 'Perdagangan Global & Ekspor', 'prospect' => 'Export-Import Specialist, Trade Compliance Officer, Supply Analyst'],
            ],
            'CE' => [
                ['major' => 'Sistem Informasi Akuntansi & ERP Management', 'field' => 'Teknologi Informasi Keuangan', 'prospect' => 'ERP Consultant, Database Administrator, IT Auditor Financial System'],
                ['major' => 'Administrasi Publik & Kebijakan Tata Kelola', 'field' => 'Birokrasi & Pemerintahan', 'prospect' => 'Analis Kebijakan Aparatur Negara, Pengelola Data Statistik Daerah'],
                ['major' => 'Manajemen Logistik & Rantai Pasok', 'field' => 'Manajemen Distribusi Barang', 'prospect' => 'Logistics Coordinator, Warehouse Management Specialist, Cargo Analyst'],
                ['major' => 'Kearsipan Digital & Tata Kelola Informasi', 'field' => 'Administrasi Modern Terpadu', 'prospect' => 'Arsiparis Ahli Pertama, Information Governance Manager, Record Specialist'],
            ],
        ];

        if (isset($careerMap[$pair])) {
            return $careerMap[$pair];
        }

        // Fallback dinamis berdasarkan dimensi dominan jika pasangan belum terdaftar
        return match ($dominant) {
            'R' => [
                ['major' => 'Teknik Elektro / Teknik Mesin', 'field' => 'Rekayasa Teknik Terapan', 'prospect' => 'Insinyur Mekanik, Teknisi Instrumentasi, Perancang Mesin'],
                ['major' => 'Teknologi Informasi & Jaringan Komputer', 'field' => 'Infrastruktur TI', 'prospect' => 'Network Administrator, Cloud Infrastructure Engineer'],
                ['major' => 'Agroteknologi & Kehutanan Berkelanjutan', 'field' => 'Teknologi Pertanian Alam', 'prospect' => 'Agronomis Lapangan, Ahli Silvikultur, Manajer Kebun'],
                ['major' => 'Teknik Pertambangan / Geologi Terapan', 'field' => 'Eksplorasi Sumber Daya Alam', 'prospect' => 'Mining Engineer, Analis Kualitas Batuan, Safety Officer'],
            ],
            'I' => [
                ['major' => 'Teknik Informatika & Ilmu Komputer', 'field' => 'Sains & Kecerdasan Buatan (AI)', 'prospect' => 'AI Scientist, Machine Learning Engineer, Software Developer'],
                ['major' => 'Farmasi Klinis & Farmakologi Herbal', 'field' => 'Sains Kesehatan Terapan', 'prospect' => 'Peneliti Farmakologi, Apoteker Riset Obat, Formulator'],
                ['major' => 'Matematika Terapan & Statistika Sains Data', 'field' => 'Sains Data & Aktuaria', 'prospect' => 'Data Scientist, Aktuaris Pemodelan Risiko, Kriptografer'],
                ['major' => 'Fisika Murni / Astronomi & Sains Antariksa', 'field' => 'Sains Fundamental Murni', 'prospect' => 'Peneliti BRIN, Astrofisikawan, Analis Instrumentasi Optik'],
            ],
            'A' => [
                ['major' => 'Desain Komunikasi Visual (DKV) & Game Design', 'field' => 'Industri Hiburan Digital', 'prospect' => 'Game Artist, Character Designer, Creative Art Director'],
                ['major' => 'Arsitektur Rancang Bangun & Lanskap', 'field' => 'Perancangan Tata Ruang', 'prospect' => 'Arsitek Konseptual, Desainer Lansekap, Scenographer'],
                ['major' => 'Film, Televisi & Penulisan Kreatif Naskah', 'field' => 'Media Sinema Komunikasi', 'prospect' => 'Sutradara Film, Penulis Skenario Serial, Editor Visual'],
                ['major' => 'Seni Musik & Desain Tata Suara Akustik', 'field' => 'Seni Musik Pertunjukan', 'prospect' => 'Music Composer, Produser Musik Studio, Sound Designer'],
            ],
            'S' => [
                ['major' => 'Bimbingan dan Konseling (BK) & Psikologi Pendidikan', 'field' => 'Pendidikan Konseling', 'prospect' => 'Guru BK Profesional, Konselor Remaja, Terapis Tumbuh Kembang'],
                ['major' => 'Pendidikan Dokter & Keperawatan Spesialis', 'field' => 'Pelayanan Medis Kemanusiaan', 'prospect' => 'Dokter Umum, Kepala Perawat IGD, Edukator Kesehatan'],
                ['major' => 'Ilmu Kesejahteraan Sosial & Pengembangan Masyarakat', 'field' => 'Pengembangan Komunitas', 'prospect' => 'Community Development Lead, Koordinator Program Filantropi'],
                ['major' => 'Pendidikan Guru Bidang Studi / PGSD Unggulan', 'field' => 'Pendidikan Pengajaran', 'prospect' => 'Guru Teladan Berprestasi, Pengembang Modul Kurikulum'],
            ],
            'E' => [
                ['major' => 'Manajemen Bisnis & Kewirausahaan Eksekutif', 'field' => 'Ekonomi & Kepemimpinan Bisnis', 'prospect' => 'Start-up Founder, Chief Executive Officer (CEO), Bisnis Analis'],
                ['major' => 'Ilmu Komunikasi Korporat & Pemasaran Digital', 'field' => 'Komunikasi & Pemasaran', 'prospect' => 'Digital Marketing Director, Brand Strategist, PR Director'],
                ['major' => 'Ilmu Hukum Bisnis & Tata Kelola Perdagangan', 'field' => 'Hukum Korporasi Legal', 'prospect' => 'Legal Specialist Perusahaan, Pengacara Komersial, Notaris'],
                ['major' => 'Hubungan Internasional & Diplomasi Ekonomi', 'field' => 'Diplomasi & Perdagangan Global', 'prospect' => 'Diplomat, Konsultan Hubungan Pemerintah, Negosiator'],
            ],
            default => [
                ['major' => 'Akuntansi Keuangan & Perpajakan Terpadu', 'field' => 'Sistem Keuangan Akuntansi', 'prospect' => 'Akuntan Publik (CPA), Auditor Internal, Konsultan Pajak'],
                ['major' => 'Sains Data Bisnis & Administrasi Perbankan', 'field' => 'Teknologi Analisis Data Bisnis', 'prospect' => 'Business Analyst, Database Administrator, Compliance Manager'],
                ['major' => 'Manajemen Administrasi Publik & Arsip Digital', 'field' => 'Tata Kelola Pemerintahan', 'prospect' => 'Arsiparis Profesional, Analis Kebijakan Publik, Birokrat'],
                ['major' => 'Manajemen Logistik & Operasional Rantai Pasok', 'field' => 'Manajemen Distribusi & Pasokan', 'prospect' => 'Supply Chain Analyst, Inventory Controller, Quality Specialist'],
            ],
        };
    }

    /**
     * Evaluasi Lengkap Tes Kecerdasan Majemuk (Howard Gardner Multiple Intelligences)
     */
    protected function generateGardnerAnalysis(string $dominant, ?string $secondary, ?string $tertiary, array $percentages, array $itemAnalysis, Student $student): array
    {
        $names = [
            'LINGUISTIK' => 'Linguistik-Verbal (Kecakapan Kata & Bahasa)',
            'LOGIS_MATEMATIS' => 'Logis-Matematis (Logika Angka & Penalaran Sains)',
            'VISUAL_SPASIAL' => 'Visual-Spasial (Imajinasi Bentuk, Pola & Ruang)',
            'KINESTETIK' => 'Kinestetik-Jasmani (Ketangkasan Gerak Motorik & Fisik)',
            'MUSIKAL' => 'Musikal-Ritmik (Peka Irama, Nada, Ritme & Melodi)',
            'INTERPERSONAL' => 'Interpersonal (Sosial, Empati & Kepemimpinan Kelompok)',
            'INTRAPERSONAL' => 'Intrapersonal (Refleksi Diri, Kemandirian & Pengendalian Emosi)',
            'NATURALIS' => 'Naturalis (Kepekaan Flora-Fauna & Ekosistem Alam)',
        ];

        $domName = $names[$dominant] ?? $dominant;
        $secName = $names[$secondary] ?? ($secondary ?: '-');
        $terName = $names[$tertiary] ?? ($tertiary ?: '-');

        $summary = "Berdasarkan kerangka Teori Kecerdasan Majemuk (*Multiple Intelligences*) Howard Gardner, peserta didik ananda **{$student->name}** menunjukkan profil spektrum kecerdasan primer pada dimensi **{$domName}** ({$percentages[$dominant]}%), bersinergi kuat dengan potensi sekunder **{$secName}** (".($percentages[$secondary] ?? 0)."%) dan potensi tersier **{$terName}** (".($percentages[$tertiary] ?? 0)."%).\n\n";
        $summary .= 'Sesuai hakikat teori Gardner, setiap anak memiliki modalitas kecerdasan majemuk yang unik dan tidak dapat disederhanakan hanya melalui skor IQ numerik tunggal. Potensi dominan siswa ini merupakan *leverage point* (daya ungkit) terbaik untuk mengakselerasi prestasi akademik, pengembangan karakter Pelajar Pancasila, dan arah karier masa depannya.';

        $studyTips = match ($dominant) {
            'LINGUISTIK' => "• Jelaskan materi pelajaran dalam bentuk narasi cerita, artikel ringkas, atau analogi metafora kata.\n• Biasakan menulis jurnal refleksi harian dan aktif dalam sesi debat atau presentasi di depan kelas.\n• Gunakan teknik membaca cepat (*skimming-scanning*) dan kuis kosa kata untuk memperkuat retensi.",
            'LOGIS_MATEMATIS' => "• Cari hubungan sebab-akibat (kausalitas) dan pola keteraturan logis dari setiap rumus/teori baru.\n• Pecah konsep permasalahan kompleks menjadi langkah-langkah terstruktur seperti bagan algoritma (*flowchart*).\n• Latih daya nalar dengan permainan strategi logika (catur, teka-teki silang angka, problem solving sains).",
            'VISUAL_SPASIAL' => "• Konversikan teks naratif panjang menjadi sketsa peta pikiran (*mind map*), infografis, dan grafik perbandingan.\n• Manfaatkan representasi diagram geometris dan peragaan visual 3 dimensi dalam memahami konsep abstrak.\n• Buat ringkasan menggunakan susunan tata letak yang rapi dan estetis dengan stabilo aneka warna.",
            'KINESTETIK' => "• Pelajari materi sambil melakukan simulasi fisik, membuat maket peraga, atau eksperimen langsung di laboratorium.\n• Selipkan jeda peregangan aktif setiap 25 menit sesi belajar untuk mengembalikan energi konsentrasi otak.\n• Asosiasikan rumus dan definisi penting dengan gerakan isyarat tubuh atau peragaan teatrikal.",
            'MUSIKAL' => "• Ubah barisan fakta hafalan atau rumus pelajaran menjadi rima lagu, jingle melodis, atau irama ketukan ritmis.\n• Dengarkan musik instrumental lembut tanpa lirik (gelombang alfa / musik barok) sebagai latar pemicu fokus belajar.\n• Manfaatkan variasi tempo dan intonasi ritmis suara saat mengulang hafalan materi.",
            'INTERPERSONAL' => "• Maksimalkan hasil belajar melalui diskusi kelompok (*cooperative learning*), saling bertukar peran tanya-jawab.\n• Ajarkan materi yang telah dipahami kepada teman sebaya (*peer-teaching*) untuk memperkokoh pemahaman diri.\n• Ambil peran koordinator atau moderator dalam penugasan kelompok sekolah.",
            'INTRAPERSONAL' => "• Tentukan target belajar mandiri harian secara realistis dan evaluasi pencapaiannya secara berkala.\n• Sediakan ruang belajar yang tenang untuk kontemplasi mendalam tanpa gangguan interupsi pihak luar.\n• Hubungkan materi pelajaran dengan nilai-nilai prinsip hidup pribadi dan cita-cita masa depanmu.",
            'NATURALIS' => "• Kaitkan materi teori biologi, kimia, fisika, dan geografi dengan fenomena nyata yang terjadi di lingkungan alam sekitar.\n• Lakukan sesi belajar sesekali di luar ruangan (*outdoor study* / taman sekolah) untuk menyegarkan suasana pikiran.\n• Libatkan diri dalam proyek pelestarian lingkungan hidup, penanaman pohon, atau studi lapangan konservasi.",
            default => 'Kembangkan pembelajaran terpadu dengan pendampingan bimbingan konseling berkala.',
        };

        $careers = [
            ['major' => 'Program Studi Berbasis '.$domName, 'field' => 'Bidang Studi Utama', 'prospect' => 'Karier Profesional Spesialis di Bidang '.$domName],
            ['major' => 'Program Studi Sinergis '.($names[$secondary] ?? 'Interdisipliner'), 'field' => 'Bidang Studi Pendukung', 'prospect' => 'Profesi Multidisiplin Menggabungkan '.$domName.' & '.($names[$secondary] ?? 'Terkait')],
        ];

        $actionPlan = [
            'bidang_pribadi' => [
                'tujuan' => "Afirmasi konsep diri positif bahwa siswa memiliki kecerdasan otentik pada dimensi {$domName}.",
                'layanan' => 'Layanan Bimbingan Pribadi: Penguatan rasa percaya diri, pengembangan kebiasaan reflektif, dan optimalisasi potensi unggul.',
            ],
            'bidang_belajar' => [
                'tujuan' => "Pemanfaatan kecerdasan dominan ({$dominant}) sebagai jembatan memahami mata pelajaran yang dianggap sulit.",
                'layanan' => 'Bimbingan Belajar Diferensiatif: Penyusunan teknik belajar pribadi sesuai gaya pemecahan masalah alami siswa.',
            ],
            'bidang_sosial' => [
                'tujuan' => 'Penyaluran bakat melalui interaksi bermakna dalam Projek Penguatan Profil Pelajar Pancasila (P5) dan ekstrakurikuler.',
                'layanan' => 'Bimbingan Kelompok & Penugasan Berdiferensiasi: Penempatan peran proyek sesuai kekuatan kecerdasan siswa.',
            ],
            'kolaborasi_guru_mapel' => [
                'tujuan' => 'Diferensiasi Produk dalam Asesmen Pembelajaran (Kurikulum Merdeka).',
                'rekomendasi' => "Guru Mata Pelajaran disarankan memberi keleluasaan bentuk tugas/produk akhir: siswa tipe {$domName} dapat menghasilkan unjuk kerja berupa tulisan esai, karya seni grafis, video peragaan fisik, atau presentasi proyek sosial sesuai minat kecerdasannya.",
            ],
            'peran_orang_tua' => [
                'tujuan' => 'Dukungan apresiatif orang tua terhadap keunikan talenta anak di luar nilai rapor akademis semata.',
                'rekomendasi' => 'Orang tua disarankan mengapresiasi dan memfasilitasi sarana pengembangan kecerdasan dominan anak (misal: buku bacaan bermutu, perlengkapan eksperimen, alat musik, atau keikutsertaan pelatihan kepemimpinan), serta tidak membanding-bandingkan anak dengan saudaranya.',
            ],
            'indikator_evaluasi' => [
                'indikator' => 'Keterlibatan aktif siswa dalam kegiatan kelas meningkat, kepuasan belajar dan efikasi diri tinggi, serta portofolio karya mencerminkan orisinalitas potensi dirinya.',
                'waktu_evaluasi' => 'Evaluasi portofolio talenta siswa setiap akhir semester.',
            ],
        ];

        $scientificSources = [
            'theory_name' => 'Teori Kecerdasan Majemuk (Multiple Intelligences Theory)',
            'pioneers' => 'Prof. Howard Gardner, Ph.D. (John H. and Elisabeth A. Hobbs Research Professor of Cognition and Education, Harvard Graduate School of Education, Harvard University, 1983, 1999).',
            'primary_literature' => [
                'Gardner, H. (1983). Frames of Mind: The Theory of Multiple Intelligences. Basic Books.',
                'Gardner, H. (1999). Intelligence Reframed: Multiple Intelligences for the 21st Century. Basic Books.',
                'Armstrong, T. (2009). Multiple Intelligences in the Classroom (3rd ed.). Association for Supervision and Curriculum Development (ASCD).',
            ],
            'regulatory_framework' => [
                'Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) Sekolah Menengah Atas (SMA), Dirjen GTK Kemendikbud RI (Bidang Bimbingan Pribadi & Belajar).',
                'Standar Kompetensi Kemandirian Peserta Didik (SKKPD) Asosiasi Bimbingan dan Konseling Indonesia (ABKIN) - Aspek Kesadaran Tanggung Jawab Sosial & Pengembangan Diri Holistik.',
                'Keputusan Kepala BSKAP Kemendikbudristek No. 009/H/KR/2022 tentang Dimensi, Elemen, dan Subelemen Profil Pelajar Pancasila.',
            ],
        ];

        return [
            'summary' => $summary,
            'study_tips' => $studyTips,
            'careers' => $careers,
            'action_plan' => $actionPlan,
            'scientific_sources' => $scientificSources,
        ];
    }

    protected function generateGenericActionPlan(string $dominant, Student $student): array
    {
        return [
            'bidang_pribadi' => [
                'tujuan' => 'Peningkatan pemahaman potensi diri dan regulasi emosi positif.',
                'layanan' => 'Layanan Bimbingan Konseling Pribadi terjadwal.',
            ],
            'bidang_belajar' => [
                'tujuan' => 'Optimalisasi pencapaian tujuan pembelajaran dan peningkatan ketahanan belajar.',
                'layanan' => 'Bimbingan Belajar dan Pemantauan Hasil Asesmen Formatif.',
            ],
            'bidang_karier' => [
                'tujuan' => 'Eksplorasi wawasan karier dan perencanaan masa depan terarah.',
                'layanan' => 'Layanan Informasi dan Peminatan Studi Lanjut.',
            ],
            'kolaborasi_guru_mapel' => [
                'tujuan' => 'Pendampingan belajar kolaboratif di kelas.',
                'rekomendasi' => 'Guru mapel menerapkan variasi metode pembelajaran aktif yang memotivasi siswa.',
            ],
            'peran_orang_tua' => [
                'tujuan' => 'Pendampingan positif di lingkungan keluarga.',
                'rekomendasi' => 'Orang tua memberikan perhatian dan apresiasi terhadap proses perkembangan belajar anak.',
            ],
            'indikator_evaluasi' => [
                'indikator' => 'Keterlibatan belajar meningkat dan target akademik tercapai.',
                'waktu_evaluasi' => 'Evaluasi berkala setiap akhir bulan.',
            ],
        ];
    }

    protected function getGenericScientificSources(): array
    {
        return [
            'theory_name' => 'Prinsip-Prinsip Asesmen Psikologis & Standar Bimbingan Konseling Indonesia',
            'pioneers' => 'Asosiasi Bimbingan dan Konseling Indonesia (ABKIN) & Himpunan Psikologi Indonesia (HIMPSI).',
            'primary_literature' => [
                'Anastasi, A., & Urbina, S. (1997). Psychological Testing (7th ed.). Prentice Hall.',
                'Prayitno, & Amti, E. (2004). Dasar-dasar Bimbingan dan Konseling. Rineka Cipta.',
            ],
            'regulatory_framework' => [
                'Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) SMA, Kementerian Pendidikan dan Kebudayaan RI.',
                'Kode Etik Bimbingan dan Konseling Indonesia, Pengurus Besar ABKIN.',
            ],
        ];
    }
}
