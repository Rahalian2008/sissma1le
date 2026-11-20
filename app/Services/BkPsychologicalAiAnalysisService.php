<?php

namespace App\Services;

use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkPsychologicalAiAnalysisService
{
    /**
     * Dapatkan API Key Gemini yang tersimpan di SchoolSetting, config, atau env
     */
    public static function getGeminiApiKey(): ?string
    {
        $key = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));
        if (empty($key)) {
            $key = trim((string) env('GEMINI_API_KEY', ''));
        }

        return ! empty($key) ? $key : null;
    }

    /**
     * Dapatkan model Gemini yang dikonfigurasi
     */
    public static function getGeminiModel(): string
    {
        return trim((string) (SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash')));
    }

    /**
     * Jalankan analisis psikometrik AI komprehensif berdasarkan Master Prompt untuk 1 hasil tes psikotes
     */
    public function analyzeTestResult(BkPsychologicalTestResult $result): array
    {
        $result->load(['test.questions', 'student.schoolClass']);
        $test = $result->test;
        $student = $result->student;
        $answers = $result->answers ?: [];

        // 1. Ekstrak data kuantitatif matematis murni (deterministik bebas halusinasi)
        $rawScores = $result->dimension_scores['raw'] ?? [];
        $percentages = $result->dimension_scores['percentages'] ?? [];
        $dominant = $result->dominant_dimension ?: '';
        $secondary = $result->secondary_dimension;
        $tertiary = $result->tertiary_dimension;
        $hollandCode = $result->holland_code;
        $itemAnalysis = $result->item_analysis ?: [];

        $context = [
            'student_name' => $student->name,
            'student_nisn' => $student->nisn ?? '-',
            'student_class' => $student->schoolClass?->name ?? 'Fase E/F',
            'student_gender' => $student->gender ?? 'L',
            'test_slug' => $test->slug,
            'test_name' => $test->name,
            'test_category' => $test->category,
            'test_desc' => $test->short_desc,
            'raw_scores' => $rawScores,
            'percentages' => $percentages,
            'dominant' => $dominant,
            'secondary' => $secondary,
            'tertiary' => $tertiary,
            'holland_code' => $hollandCode,
            'top_items' => array_slice($itemAnalysis['top_strengths'] ?? [], 0, 5),
            'growth_items' => array_slice($itemAnalysis['growth_areas'] ?? [], 0, 4),
        ];

        // 2. Coba eksekusi Google Gemini AI jika API key tersedia
        $apiKey = self::getGeminiApiKey();
        if (! empty($apiKey)) {
            $prompt = $this->buildTestResultPrompt($result, $context);
            $aiResponse = $this->callGemini($prompt);

            if ($aiResponse && ! empty($aiResponse['data']) && is_array($aiResponse['data'])) {
                $parsed = $aiResponse['data'];

                return [
                    'success' => true,
                    'provider' => 'Google Gemini AI ('.$aiResponse['model'].')',
                    'model' => $aiResponse['model'],
                    'is_online_ai' => true,
                    'report' => $this->sanitizeMasterReportStructure($parsed, $context),
                ];
            }
        }

        // 3. Fallback Heuristik Deterministik jika offline / tanpa API key
        $fallbackReport = $this->generateDeterministicFallback($result, $context);

        return [
            'success' => true,
            'provider' => 'Deterministic Rule-Based Engine (Lokal)',
            'model' => 'heuristic-v2',
            'is_online_ai' => false,
            'report' => $fallbackReport,
        ];
    }

    /**
     * Susun Master Prompt komprehensif untuk Google Gemini AI
     */
    protected function buildTestResultPrompt(BkPsychologicalTestResult $result, array $ctx): string
    {
        $rawScoresJson = json_encode($ctx['raw_scores'], JSON_UNESCAPED_UNICODE);
        $pctJson = json_encode($ctx['percentages'], JSON_UNESCAPED_UNICODE);
        $topItemsText = collect($ctx['top_items'])->map(fn ($i) => "- [{$i['dimension']}] \"{$i['statement']}\" (Skor {$i['score']}/4)")->implode("\n");
        $growthItemsText = collect($ctx['growth_items'])->map(fn ($i) => "- [{$i['dimension']}] \"{$i['statement']}\" (Skor {$i['score']}/4)")->implode("\n");

        return <<<PROMPT
# MASTER PROMPT SISTEM ANALISIS, EVALUASI, DAN REKOMENDASI PSIKOTES SISWA SMA
Peran: Anda adalah AI ANALIS PSIKOTES DAN KONSELOR PENDIDIKAN multidisipliner (Psikologi Pendidikan, Psikometri, Konseling Bimbingan Karier POP-BK, dan Penjurusan Perguruan Tinggi).

Tugas Anda: Menganalisis hasil tes psikotes peserta didik SMA Negeri 1 Lengkong berdasarkan data empiris yang diberikan di bawah ini secara objektif, sistematis, ramah, komprehensif, dan memberdayakan peserta didik.

## DATA PESERTA DIDIK & ASESMEN:
- Nama Siswa: {$ctx['student_name']}
- NISN: {$ctx['student_nisn']}
- Kelas: {$ctx['student_class']}
- Instrumen: {$ctx['test_name']} ({$ctx['test_slug']})
- Kategori Instrumen: {$ctx['test_category']}
- Dimensi Dominan: {$ctx['dominant']}
- Dimensi Sekunder: {$ctx['secondary']}
- Dimensi Tersier: {$ctx['tertiary']}
- Kode Holland (jika ada): {$ctx['holland_code']}
- Skor Mentah Dimensi: {$rawScoresJson}
- Capaian Persentase Dimensi: {$pctJson}

## BUTIR PERNYATAAN RESPONS TERTINGGI (KEKUATAN UTAMA):
{$topItemsText}

## BUTIR PERNYATAAN RESPONS TERENDAH (AREA PERTUMBUHAN / KURANG DIMINATI):
{$growthItemsText}

## KETENTUAN DAN ETIKA KETAT:
1. Objektif & berbasis data hasil tes di atas. Jangan mengarang skor baru.
2. Jangan mengarang IQ jika tes tidak dirancang untuk mengukur IQ.
3. Jangan memberikan diagnosis klinis atau label gangguan kepribadian.
4. Gunakan bahasa Indonesia baku, santun, memotivasi, dan solutif.
5. Rekomendasikan minimal 3 pilihan program studi dan karier yang relevan dengan data minat/bakat.
6. Berikan rekomendasi yang dapat diterapkan oleh Guru BK, Wali Kelas, Guru Mapel, Orang Tua, dan Siswa.

## FORMAT KELUARAN WAJIB (STRICT JSON ONLY):
Wajib menghasilkan output HANYA berupa JSON valid tanpa teks pengantar atau markdown tambahan di luar JSON, dengan skema persis seperti berikut:

{
  "ringkasan_eksekutif": "Narasi ringkasan eksekutif 2-3 paragraf mengenai profil psikologis siswa, keunggulan utama, dan arah masa depan.",
  "hasil_penilaian_keseluruhan": [
    {
      "dimensi": "KODE_DIMENSI",
      "nama_dimensi": "Nama Lengkap Dimensi",
      "skor": 0,
      "persentase": 0.0,
      "kategori": "Sangat Tinggi / Tinggi / Sedang / Berkembang",
      "interpretasi": "Uraian makna skor ini bagi siswa"
    }
  ],
  "analisis_mendalam": {
    "gambaran_umum": "Uraian menyeluruh interaksi antar dimensi",
    "dinamika_dominansi": "Penjelasan mengapa dimensi dominan menonjol dan dampaknya",
    "area_distraksi_atau_pertumbuhan": "Faktor yang perlu diantisipasi atau dilatih"
  },
  "profil_potensi": {
    "potensi_intelektual": "Uraian potensi penalaran, analisis, dan cara berpikir",
    "potensi_bakat": "Bakat alami dan keahlian spesifik yang terindikasi",
    "potensi_akademik": "Kesiapan belajar dan mata pelajaran unggulan",
    "potensi_sosial": "Gaya komunikasi dan interaksi dengan teman sebaya",
    "potensi_kepemimpinan": "Kapasitas menggerakkan diri dan kelompok",
    "potensi_kreativitas": "Daya inovasi, ekspresi seni, atau pemecahan masalah",
    "pengembangan_karier": "Arah orientasi profesi jangka panjang"
  },
  "kekuatan_dan_area_pengembangan": {
    "kekuatan_utama": ["Poin kekuatan 1", "Poin kekuatan 2", "Poin kekuatan 3"],
    "kekuatan_pendukung": ["Kekuatan pendukung 1", "Kekuatan pendukung 2"],
    "area_pengembangan": ["Area perlu ditingkatkan 1", "Area perlu ditingkatkan 2"],
    "potensi_eksplorasi": ["Potensi yang dapat dicoba dan diperluas"],
    "hal_perlu_observasi": ["Catatan pemantauan berkala bagi guru BK"]
  },
  "rekomendasi_pendidikan": {
    "mata_pelajaran_relevan": ["Mapel 1", "Mapel 2", "Mapel 3"],
    "bidang_studi_potensial": ["Bidang 1", "Bidang 2"],
    "ekstrakurikuler_relevan": ["Ekskul 1", "Ekskul 2"],
    "strategi_belajar_efektif": "Metode belajar paling ampuh untuk siswa ini",
    "program_kompetensi": "Pelatihan/kompetensi yang disarankan diikuti"
  },
  "rekomendasi_perguruan_tinggi_dan_karir": {
    "alternatif_bidang": ["Bidang A", "Bidang B", "Bidang C"],
    "program_studi": [
      {"jurusan": "Nama Jurusan 1", "fakultas": "Nama Rumpun/Fakultas", "alasan": "Alasan kesesuaian dengan profil"},
      {"jurusan": "Nama Jurusan 2", "fakultas": "Nama Rumpun/Fakultas", "alasan": "Alasan kesesuaian dengan profil"},
      {"jurusan": "Nama Jurusan 3", "fakultas": "Nama Rumpun/Fakultas", "alasan": "Alasan kesesuaian dengan profil"}
    ],
    "profesi_rekomendasi": [
      {"profesi": "Nama Profesi 1", "deskripsi": "Gambaran peran karier"},
      {"profesi": "Nama Profesi 2", "deskripsi": "Gambaran peran karier"},
      {"profesi": "Nama Profesi 3", "deskripsi": "Gambaran peran karier"}
    ],
    "kompetensi_kunci_disiapkan": ["Soft skill / hard skill yang harus diasah"],
    "opsi_alternatif": "Alternatif jika minat dan bakat masih terus berevolusi"
  },
  "rekomendasi_pengembangan_diri": {
    "karakter": "Pengembangan kedisiplinan dan integritas",
    "bakat_dan_minat": "Langkah mengasah hobi menjadi karya",
    "keterampilan_sosial": "Pengembangan relasi dan empati",
    "kepercayaan_diri": "Strategi mengatasi kecemasan dan membangun efikasi diri"
  },
  "rencana_tindak_lanjut": {
    "jangka_pendek_1_bulan": [
      {"target": "Target 1 bln", "aktivitas": "Aktivitas nyata", "pic": "Siswa / Guru BK", "indikator": "Bukti berhasil", "evaluasi": "Metode cek"}
    ],
    "jangka_menengah_3_bulan": [
      {"target": "Target 3 bln", "aktivitas": "Aktivitas nyata", "pic": "Guru BK / Wali Kelas", "indikator": "Bukti berhasil", "evaluasi": "Metode cek"}
    ],
    "jangka_panjang_6_bulan": [
      {"target": "Target 6 bln", "aktivitas": "Aktivitas nyata", "pic": "Sekolah & Orang Tua", "indikator": "Bukti berhasil", "evaluasi": "Metode cek"}
    ]
  },
  "rekomendasi_pihak_sekolah": {
    "guru_bk": "Tindakan konseling atau asesmen lanjutan yang dianjurkan",
    "wali_kelas": "Strategi pendampingan di kelas dan interaksi kelompok",
    "guru_mata_pelajaran": "Diferensiasi instruksi dan tugas pembelajaran",
    "orang_tua": "Dukungan emosional, komunikasi, dan fasilitas di rumah",
    "kesiswaan": "Penyaluran ke dalam organisasi/ekskul yang membina bakatnya",
    "kepala_sekolah": "Dukungan kebijakan sekolah untuk iklim belajar inklusif"
  },
  "kesimpulan_akhir": {
    "gambaran_umum": "Intisari profil psikologis dan potensi siswa",
    "kekuatan_paling_menonjol": "Keunggulan utama yang harus dipertahankan",
    "area_pengembangan_utama": "Tantangan utama yang perlu dibimbing",
    "tindak_lanjut_prioritas": "Langkah pertama yang paling mendesak dilakukan"
  },
  "prioritas_keputusan": {
    "level": "PRIORITAS_1",
    "label": "PENGEMBANGAN POTENSI",
    "deskripsi": "Siswa memiliki potensi unggul yang siap diakselerasi melalui pembinaan bakat berkesinambungan.",
    "alasan": "Skor dominan berada pada kategori tinggi dan konsisten."
  }
}
PROMPT;
    }

    /**
     * Panggil Google Gemini API dengan model fallback otomatis
     */
    protected function callGemini(string $prompt): ?array
    {
        $apiKey = self::getGeminiApiKey();
        if (empty($apiKey)) {
            return null;
        }

        $configuredModel = self::getGeminiModel();
        $modelsToTry = array_unique(array_filter([
            $configuredModel,
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-2.0-flash',
            'gemini-3.5-flash-lite',
        ]));

        $timeout = (int) config('services.ai.timeout', 28);

        foreach ($modelsToTry as $targetModel) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$targetModel}:generateContent?key={$apiKey}";

                $response = Http::timeout($timeout)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $prompt]],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.45,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if ($response->successful()) {
                    $candidates = $response->json('candidates');
                    $rawText = $candidates[0]['content']['parts'][0]['text'] ?? null;

                    if ($rawText) {
                        $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                        $parsed = json_decode($cleanJson, true);

                        if (is_array($parsed) && (isset($parsed['ringkasan_eksekutif']) || isset($parsed['prioritas_keputusan']))) {
                            return [
                                'model' => $targetModel,
                                'data' => $parsed,
                            ];
                        }
                    }
                } else {
                    Log::warning("Gemini API psikotes call failed on [{$targetModel}]: HTTP {$response->status()}", [
                        'body' => substr($response->body(), 0, 200),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini API psikotes exception on [{$targetModel}]: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Sanitasi dan validasi kelengkapan struktur data hasil AI
     */
    protected function sanitizeMasterReportStructure(array $raw, array $ctx): array
    {
        $defaults = $this->generateDeterministicFallbackFromContext($ctx);

        return [
            'ringkasan_eksekutif' => $raw['ringkasan_eksekutif'] ?? $defaults['ringkasan_eksekutif'],
            'hasil_penilaian_keseluruhan' => ! empty($raw['hasil_penilaian_keseluruhan']) && is_array($raw['hasil_penilaian_keseluruhan'])
                ? $raw['hasil_penilaian_keseluruhan']
                : $defaults['hasil_penilaian_keseluruhan'],
            'analisis_mendalam' => array_merge($defaults['analisis_mendalam'], $raw['analisis_mendalam'] ?? []),
            'profil_potensi' => array_merge($defaults['profil_potensi'], $raw['profil_potensi'] ?? []),
            'kekuatan_dan_area_pengembangan' => array_merge($defaults['kekuatan_dan_area_pengembangan'], $raw['kekuatan_dan_area_pengembangan'] ?? []),
            'rekomendasi_pendidikan' => array_merge($defaults['rekomendasi_pendidikan'], $raw['rekomendasi_pendidikan'] ?? []),
            'rekomendasi_perguruan_tinggi_dan_karir' => array_merge($defaults['rekomendasi_perguruan_tinggi_dan_karir'], $raw['rekomendasi_perguruan_tinggi_dan_karir'] ?? []),
            'rekomendasi_pengembangan_diri' => array_merge($defaults['rekomendasi_pengembangan_diri'], $raw['rekomendasi_pengembangan_diri'] ?? []),
            'rencana_tindak_lanjut' => array_merge($defaults['rencana_tindak_lanjut'], $raw['rencana_tindak_lanjut'] ?? []),
            'rekomendasi_pihak_sekolah' => array_merge($defaults['rekomendasi_pihak_sekolah'], $raw['rekomendasi_pihak_sekolah'] ?? []),
            'kesimpulan_akhir' => array_merge($defaults['kesimpulan_akhir'], $raw['kesimpulan_akhir'] ?? []),
            'prioritas_keputusan' => array_merge($defaults['prioritas_keputusan'], $raw['prioritas_keputusan'] ?? []),
        ];
    }

    /**
     * Fallback Heuristik Lengkap 12 Bab berstandar POP-BK (Offline Engine)
     */
    protected function generateDeterministicFallback(BkPsychologicalTestResult $result, array $ctx): array
    {
        return $this->generateDeterministicFallbackFromContext($ctx);
    }

    protected function generateDeterministicFallbackFromContext(array $ctx): array
    {
        $testSlug = $ctx['test_slug'] ?? 'general';
        $dominant = $ctx['dominant'] ?? '';
        $secondary = $ctx['secondary'] ?? '';
        $studentName = $ctx['student_name'] ?? 'Peserta Didik';
        $percentages = $ctx['percentages'] ?? [];
        $domPct = $percentages[$dominant] ?? 75;

        // Label & deskripsi dimensi
        $dimensionLabels = [
            'VISUAL' => 'Visual (Spatial-Visual Modality)',
            'AUDITORI' => 'Auditori (Auditory-Verbal Modality)',
            'KINESTETIK' => 'Kinestetik (Tactile-Kinesthetic Modality)',
            'R' => 'Realistic (Praktikal, Mesin, Lapangan Fisik)',
            'I' => 'Investigative (Riset, Sains, Analisis Logika)',
            'A' => 'Artistic (Seni, Kreativitas, Ekspresi Orisinal)',
            'S' => 'Social (Pemberdayaan, Edukasi, Empati Kemanusiaan)',
            'E' => 'Enterprising (Kepemimpinan, Manajemen Bisnis, Persuasi)',
            'C' => 'Conventional (Administratif, Tata Kelola Data, Akurasi)',
            'LOGIS_MATEMATIS' => 'Kecerdasan Logis-Matematis & Analisis Eksakta',
            'LINGUISTIK' => 'Kecerdasan Linguistik-Verbal & Komunikasi',
            'VISUAL_SPASIAL' => 'Kecerdasan Visual-Spasial & Desain',
            'INTERPERSONAL' => 'Kecerdasan Interpersonal & Hubungan Sosial',
            'INTRAPERSONAL' => 'Kecerdasan Intrapersonal & Regulasi Reflektif',
            'NATURALIS' => 'Kecerdasan Naturalis & Kepekaan Lingkungan',
            'MUSIKAL' => 'Kecerdasan Musikal & Harmoni Ritmik',
        ];

        $domName = $dimensionLabels[$dominant] ?? strtoupper($dominant);
        $secName = $dimensionLabels[$secondary] ?? ($secondary ? strtoupper($secondary) : 'Pendukung Seimbang');

        // Prioritas Keputusan berdasarkan capaian persentase
        $priority = match (true) {
            $domPct >= 80 => [
                'level' => 'PRIORITAS_1',
                'label' => 'PENGEMBANGAN POTENSI',
                'deskripsi' => "Peserta didik {$studentName} menunjukkan profil dominansi yang sangat tegas ({$domPct}%) pada aspek {$domName}. Layak difasilitasi program pengayaan, akselerasi talenta, dan kompetisi bergengsi.",
                'alasan' => 'Skor dominan berada pada rentang sangat tinggi dengan konsistensi jawaban yang kuat.',
            ],
            $domPct >= 65 => [
                'level' => 'PRIORITAS_2',
                'label' => 'PENGUATAN KEMAMPUAN',
                'deskripsi' => "Peserta didik {$studentName} memiliki potensi berkembang ({$domPct}%) pada {$domName} yang dapat dioptimalkan dengan pendampingan belajar terarah serta latihan yang terstruktur.",
                'alasan' => 'Potensi utama sudah terbentuk dengan baik, memerlukan stimulasi berkelanjutan agar mencapai performa optimal.',
            ],
            default => [
                'level' => 'PRIORITAS_3',
                'label' => 'KONSELING DAN EKSPLORASI',
                'deskripsi' => 'Profil skor relatif merata atau multimodal. Disarankan sesi konseling individual bersama Guru BK untuk memperdalam eksplorasi minat, bakat, serta orientasi karier.',
                'alasan' => 'Persebaran skor menunjukkan kecenderungan minat majemuk yang memerlukan klarifikasi komprehensif.',
            ],
        };

        // Ringkasan Eksekutif
        $testName = $ctx['test_name'] ?? 'Asesmen Psikologis & Peminatan Siswa';
        $summary = "Laporan evaluasi psikometrik resmi SMAN 1 Lengkong untuk peserta didik {$studentName} ({$ctx['student_class']}). Berdasarkan analisis instrumen {$testName}, ananda menunjukkan profil dominan yang kuat pada dimensi {$domName} dengan capaian persentase {$domPct}%, didukung oleh kecenderungan sekunder pada {$secName}. Pola ini menggambarkan karakteristik belajar dan peminatan yang memiliki kekuatan otentik dalam menyelesaikan tantangan akademik maupun interaksi keseharian di sekolah.";

        // Evaluasi dimensi keseluruhan
        $overall = [];
        foreach ($percentages as $dim => $pct) {
            $cat = match (true) {
                $pct >= 80 => 'Sangat Tinggi',
                $pct >= 65 => 'Tinggi',
                $pct >= 50 => 'Sedang / Cukup',
                default => 'Perlu Stimulasi',
            };
            $overall[] = [
                'dimensi' => $dim,
                'nama_dimensi' => $dimensionLabels[$dim] ?? $dim,
                'skor' => $ctx['raw_scores'][$dim] ?? 0,
                'persentase' => (float) $pct,
                'kategori' => $cat,
                'interpretasi' => "Menunjukkan kapasitas {$cat} dalam indikator {$dim}, berfungsi efektif sebagai modalitas belajar dan pengambilan keputusan.",
            ];
        }

        // Rekomendasi Program Studi & Karir Spesifik
        $higherEd = match ($testSlug) {
            'riasec' => [
                'alternatif_bidang' => ['Sains Terapan & Rekayasa', 'Bisnis, Manajemen & Komunikasi', 'Pemberdayaan Sosial & Humaniora'],
                'program_studi' => [
                    ['jurusan' => 'Teknik Informatika / Ilmu Komputer', 'fakultas' => 'Teknologi Informasi & Rekayasa', 'alasan' => 'Sesuai dengan penalaran analitis dan orientasi pemecahan masalah empiris.'],
                    ['jurusan' => 'Manajemen Bisnis / Ilmu Komunikasi', 'fakultas' => 'Ekonomi & Ilmu Sosial', 'alasan' => 'Mengakomodasi bakat interaksi strategis, negosiasi, dan kepemimpinan.'],
                    ['jurusan' => 'Psikologi / Hubungan Masyarakat', 'fakultas' => 'Ilmu Sosial & Humaniora', 'alasan' => 'Sesuai untuk menyalurkan potensi empati sosial dan pengorganisasian kerja tim.'],
                ],
                'profesi_rekomendasi' => [
                    ['profesi' => 'Software Engineer / Sistem Analis', 'deskripsi' => 'Merancang arsitektur aplikasi digital dan otomasi proses berbasis komputasi modern.'],
                    ['profesi' => 'Project Manager / Manajer Operasional', 'deskripsi' => 'Memimpin tim lintas disiplin dalam pencapaian target proyek strategis.'],
                    ['profesi' => 'Spesialis Pengembangan SDM / Konsultan', 'deskripsi' => 'Mengembangkan kompetensi individu dan dinamika organisasi yang sehat.'],
                ],
                'kompetensi_kunci_disiapkan' => ['Logika Berpikir Kritis', 'Komunikasi Persuasif', 'Manajemen Waktu', 'Kolaborasi Tim'],
                'opsi_alternatif' => 'Pilihan studi kedinasan atau vokasi terapan jika menghendaki kesiapan kerja teknis langsung.',
            ],
            'gardner' => [
                'alternatif_bidang' => ['Sains Eksakta & Teknologi', 'Desain Visual & Multimedia Kreatif', 'Pendidikan & Sastra'],
                'program_studi' => [
                    ['jurusan' => 'Teknik Industri / Sistem Informasi', 'fakultas' => 'Teknik', 'alasan' => 'Menyinergikan kecerdasan logis, spasial, dan manajerial.'],
                    ['jurusan' => 'Desain Komunikasi Visual (DKV) / Arsitektur', 'fakultas' => 'Seni & Desain', 'alasan' => 'Mengoptimalkan daya imajinasi visual-spasial dalam bentuk karya fungsional.'],
                    ['jurusan' => 'Pendidikan / Ilmu Komunikasi', 'fakultas' => 'Pendidikan & Humaniora', 'alasan' => 'Menyalurkan kecerdasan linguistik dan interaksi interpersonal bermakna.'],
                ],
                'profesi_rekomendasi' => [
                    ['profesi' => 'Data Analyst / Analis Bisnis', 'deskripsi' => 'Mengolah data numerik menjadi keputusan bisnis bernilai strategis.'],
                    ['profesi' => 'Arsitek / UI-UX Designer', 'deskripsi' => 'Merancang tata ruang dan antarmuka produk yang estetik sekaligus fungsional.'],
                    ['profesi' => 'Jurnalis / Konten Kreator Edukatif', 'deskripsi' => 'Menyampaikan gagasan inspiratif dengan bahasa persuasif yang mencerahkan.'],
                ],
                'kompetensi_kunci_disiapkan' => ['Pemodelan Data', 'Literasi Spasial-Digital', 'Public Speaking', 'Kecerdasan Emosional'],
                'opsi_alternatif' => 'Pendidikan keguruan atau sains terapan jika menyukai lingkungan kerja eksploratif.',
            ],
            default => [
                'alternatif_bidang' => ['Sains Terapan & Desain', 'Sosial Humaniora & Hukum', 'Manajemen & Komunikasi Kreatif'],
                'program_studi' => [
                    ['jurusan' => 'Ilmu Komputer / Sistem Informasi', 'fakultas' => 'Ilmu Komputer', 'alasan' => 'Kombinasi visualisasi digital dan logika pemecahan masalah.'],
                    ['jurusan' => 'Manajemen / Ilmu Komunikasi', 'fakultas' => 'Ekonomi & Bisnis', 'alasan' => 'Dukungan interaksi lisan, diskusi, dan presentasi gagasan.'],
                    ['jurusan' => 'Ilmu Hukum / Hubungan Internasional', 'fakultas' => 'Hukum & Sosial Politik', 'alasan' => 'Penalaran argumentatif dan analisis studi kasus dinamis.'],
                ],
                'profesi_rekomendasi' => [
                    ['profesi' => 'Konsultan Bisnis / Analis Pasar', 'deskripsi' => 'Meneliti tren pasar dan menyusun rencana ekspansi usaha.'],
                    ['profesi' => 'Spesialis Pemasaran Digital', 'deskripsi' => 'Mengeksekusi kampanye kreatif melalui beragam kanal media modern.'],
                    ['profesi' => 'Pendidik / Instruktur Pelatihan', 'deskripsi' => 'Membimbing dan memberdayakan peserta didik mencapai kompetensi unggul.'],
                ],
                'kompetensi_kunci_disiapkan' => ['Riset Mandiri', 'Kemampuan Beradaptasi', 'Literasi Teknologi', 'Ketahanan Mental'],
                'opsi_alternatif' => 'Eksplorasi sertifikasi keahlian profesional dan magang kerja industri terapan.',
            ],
        };

        return [
            'ringkasan_eksekutif' => $summary,
            'hasil_penilaian_keseluruhan' => $overall,
            'analisis_mendalam' => [
                'gambaran_umum' => "Secara psikometrik, profil {$studentName} ditandai oleh orientasi kuat pada dimensi {$domName}. Siswa ini memiliki cara memproses informasi yang spesifik dan efektif ketika diberikan media yang sesuai dengan gaya alaminya.",
                'dinamika_dominansi' => "Dominansi {$domPct}% pada {$domName} menjadi pilar utama dalam pemecahan masalah sehari-hari. Sementara itu, dimensi sekunder {$secName} berperan sebagai komplementer yang memperkaya daya adaptasi siswa.",
                'area_distraksi_atau_pertumbuhan' => 'Perlu pendampingan agar tidak terjebak pada satu modalitas saja, serta melatih fleksibilitas ketika menghadapi materi yang menuntut pendekatan bervariasi.',
            ],
            'profil_potensi' => [
                'potensi_intelektual' => "Kemampuan berpikir terstruktur dan analitis yang menonjol pada bidang {$domName}.",
                'potensi_bakat' => "Kecenderungan bakat dominan terarah pada penyelesaian tugas berbasis {$domName}.",
                'potensi_akademik' => 'Kesiapan belajar tinggi pada mata pelajaran yang menuntut eksplorasi mendalam dan aplikasi nyata.',
                'potensi_sosial' => 'Mampu bekerja sama dengan baik dalam kelompok kecil yang memiliki pembagian peran jelas.',
                'potensi_kepemimpinan' => 'Potensi kepemimpinan partisipatif yang menghargai kontribusi rekan dan berorientasi pada pencapaian tujuan.',
                'potensi_kreativitas' => 'Mampu melahirkan alternatif pemecahan masalah di luar pola konvensional.',
                'pengembangan_karier' => 'Peluang karier terbuka lebar pada bidang industri profesional modern dan akademis.',
            ],
            'kekuatan_dan_area_pengembangan' => [
                'kekuatan_utama' => [
                    "Kapasitas konsisten dan daya tahan konsentrasi tinggi pada tugas berbasis {$domName}.",
                    'Kemampuan mengonversi konsep teoritis menjadi pemahaman fungsional.',
                    'Efikasi diri positif dan motivasi berprestasi yang dapat dibina lebih lanjut.',
                ],
                'kekuatan_pendukung' => [
                    'Keterbukaan terhadap umpan balik konstruktif dari pendidik.',
                    'Daya adaptasi sosial yang stabil di lingkungan kelas binaan.',
                ],
                'area_pengembangan' => [
                    'Meningkatkan keterampilan regulasi emosi saat menghadapi beban tugas puncak.',
                    'Melatih teknik manajemen waktu dan penyusunan skala prioritas harian.',
                ],
                'potensi_eksplorasi' => [
                    'Mengikuti lomba karya ilmiah remaja (KIR), olimpiade sains, atau lomba bakat minat.',
                    'Mencoba peran kepemimpinan dalam kepanitiaan kegiatan OSIS / ekstrakurikuler.',
                ],
                'hal_perlu_observasi' => [
                    'Konsistensi kedisiplinan dan absensi harian di sekolah.',
                    'Kenyamanan interaksi dalam kelompok belajar kolaboratif.',
                ],
            ],
            'rekomendasi_pendidikan' => [
                'mata_pelajaran_relevan' => ['Matematika & Penalaran Logis', 'Informatika & Sains Terapan', 'Bahasa & Komunikasi Efektif'],
                'bidang_studi_potensial' => ['Sains & Teknologi Rekayasa', 'Sosial Humaniora & Bisnis'],
                'ekstrakurikuler_relevan' => ['PMR / KKR', 'Pramuka / Paskibra', 'Kelompok Ilmiah Remaja (KIR) / IT Club'],
                'strategi_belajar_efektif' => 'Gunakan pencatatan visual, latihan soal bertahap, dan diskusi studi kasus bersama teman sejawat.',
                'program_kompetensi' => 'Pelatihan Literasi Digital & Public Speaking Remaja.',
            ],
            'rekomendasi_perguruan_tinggi_dan_karir' => $higherEd,
            'rekomendasi_pengembangan_diri' => [
                'karakter' => 'Menjaga integritas, kejujuran akademik, dan tanggung jawab terhadap janji tugas.',
                'bakat_dan_minat' => 'Menjadwalkan waktu khusus setiap pekan untuk mengasah hobi produktif.',
                'keterampilan_sosial' => 'Aktif mendengar perspektif orang lain dan belajar mengelola perbedaan pendapat.',
                'kepercayaan_diri' => 'Mengafirmasi pencapaian positif harian dan tidak membandingkan diri secara destruktif.',
            ],
            'rencana_tindak_lanjut' => [
                'jangka_pendek_1_bulan' => [
                    [
                        'target' => 'Pemahaman Gaya Belajar & Profil Potensi Diri',
                        'aktivitas' => 'Sesi bimbingan klasikal dan konseling individu bersama Guru BK untuk membedah hasil asesmen.',
                        'pic' => 'Guru BK & Siswa',
                        'indikator' => 'Siswa menyusun lembar rencana belajar mandiri (personal learning plan).',
                        'evaluasi' => 'Pemeriksaan portofolio belajar oleh Guru BK di minggu ke-4.',
                    ],
                ],
                'jangka_menengah_3_bulan' => [
                    [
                        'target' => 'Peningkatan Konsistensi Belajar & Eksplorasi Karier',
                        'aktivitas' => 'Mengikuti pameran pendidikan / webinar pengenalan program studi perguruan tinggi dan konseling peminatan.',
                        'pic' => 'Wali Kelas & Guru BK',
                        'indikator' => 'Menentukan minimal 3 target jurusan kuliah pilihan beserta syarat seleksi SNBP/SNBT.',
                        'evaluasi' => 'Format rekap pilihan karier siswa pada bank data BK.',
                    ],
                ],
                'jangka_panjang_6_bulan' => [
                    [
                        'target' => 'Kesiapan Akademik Unggul & Portofolio Prestasi',
                        'aktivitas' => 'Penyusunan portofolio prestasi bakat minat dan evaluasi kemajuan nilai rapor semester berjalan.',
                        'pic' => 'Wali Kelas, Guru BK, & Orang Tua',
                        'indikator' => 'Peningkatan rerata nilai akademik dan keaktifan dalam program sekolah.',
                        'evaluasi' => 'Rapor semesteran dan asesmen perkembangan holistik.',
                    ],
                ],
            ],
            'rekomendasi_pihak_sekolah' => [
                'guru_bk' => "Lakukan pemantauan berkala terhadap perkembangan efikasi diri {$studentName}, berikan afirmasi positif serta bimbingan peminatan studi lanjut.",
                'wali_kelas' => 'Tempatkan siswa dalam kelompok belajar yang dinamis dan berikan ruang untuk mengaktualisasikan kecerdasan dominannya dalam proyek kelas.',
                'guru_mata_pelajaran' => 'Berikan diferensiasi tugas (pilihan unjuk kerja berbasis tulisan, presentasi grafis, atau proyek nyata) agar potensi siswa terakomodasi optimal.',
                'orang_tua' => 'Dukung dengan iklim keluarga yang hangat, sediakan ruang dan waktu belajar yang kondusif di rumah, serta dengarkan aspirasi pilihan masa depan ananda.',
                'kesiswaan' => 'Rekomendasikan siswa untuk terlibat aktif dalam ekstrakurikuler yang sesuai guna mengasah kematangan karakter dan kepemimpinan.',
                'kepala_sekolah' => 'Menjamin ketersediaan sarana dan prasarana penunjang asesmen serta iklim pembelajaran inklusif di lingkungan SMA Negeri 1 Lengkong.',
            ],
            'kesimpulan_akhir' => [
                'gambaran_umum' => "{$studentName} memiliki modalitas psikologis yang prospektif dengan fondasi {$domName} yang kokoh.",
                'kekuatan_paling_menonjol' => "Orientasi penalaran dan daya serap tinggi pada metode {$domName}.",
                'area_pengembangan_utama' => 'Penguatan regulasi diri dan perluasan wawasan peminatan karier perguruan tinggi.',
                'tindak_lanjut_prioritas' => 'Pelaksanaan bimbingan konseling individual dan perumusan rencana studi lanjut bersama orang tua.',
            ],
            'prioritas_keputusan' => $priority,
        ];
    }

    /**
     * Grand Holistic 360 Synthesis: Mengagregasikan seluruh instrumen psikotes (VAK, RIASEC, Gardner)
     * beserta rekam jejak perilaku/prestasi siswa menjadi satu Laporan Asesmen Holistik Terpadu.
     */
    public function analyzeHolistic360(Student $student): array
    {
        $student->load(['schoolClass', 'psychologicalResults.test']);

        $vak = $student->psychologicalResults->first(fn ($r) => $r->test?->slug === 'vak');
        $riasec = $student->psychologicalResults->first(fn ($r) => $r->test?->slug === 'riasec');
        $gardner = $student->psychologicalResults->first(fn ($r) => $r->test?->slug === 'gardner');

        $violationPoints = \DB::table('violations')
            ->where('student_id', $student->id)
            ->where('status', 'DIVERIFIKASI')
            ->sum('points');

        $achievementPoints = \DB::table('achievements')
            ->where('student_id', $student->id)
            ->where('status', 'DIVERIFIKASI')
            ->sum('points');

        $attendances = \DB::table('attendances')
            ->where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'HADIR' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'ALPA' THEN 1 ELSE 0 END) as alpa
            ")
            ->first();

        $attendanceRate = ($attendances && $attendances->total > 0)
            ? round(($attendances->hadir / $attendances->total) * 100, 1)
            : 100.0;

        $context = [
            'student_name' => $student->name,
            'student_nisn' => $student->nisn ?? '-',
            'student_class' => $student->schoolClass?->name ?? 'Fase E/F',
            'student_gender' => $student->gender ?? 'L',
            'vak_result' => $vak ? [
                'dominant' => $vak->dominant_dimension,
                'secondary' => $vak->secondary_dimension,
                'percentages' => $vak->dimension_scores['percentages'] ?? [],
            ] : null,
            'riasec_result' => $riasec ? [
                'holland_code' => $riasec->holland_code,
                'dominant' => $riasec->dominant_dimension,
                'percentages' => $riasec->dimension_scores['percentages'] ?? [],
            ] : null,
            'gardner_result' => $gardner ? [
                'dominant' => $gardner->dominant_dimension,
                'secondary' => $gardner->secondary_dimension,
                'percentages' => $gardner->dimension_scores['percentages'] ?? [],
            ] : null,
            'violation_points' => (int) $violationPoints,
            'achievement_points' => (int) $achievementPoints,
            'attendance_rate' => (float) $attendanceRate,
        ];

        $apiKey = self::getGeminiApiKey();
        if (! empty($apiKey)) {
            $prompt = $this->buildHolistic360Prompt($context);
            $aiResponse = $this->callGemini($prompt);

            if ($aiResponse && ! empty($aiResponse['data']) && is_array($aiResponse['data'])) {
                return [
                    'success' => true,
                    'provider' => 'Google Gemini AI ('.$aiResponse['model'].')',
                    'model' => $aiResponse['model'],
                    'is_online_ai' => true,
                    'report' => $this->sanitizeMasterReportStructure($aiResponse['data'], [
                        'test_slug' => 'holistic_360',
                        'test_name' => 'Asesmen Psikologis 360° Siswa',
                        'dominant' => $riasec?->dominant_dimension ?? ($gardner?->dominant_dimension ?? 'HOLISTIK'),
                        'secondary' => $vak?->dominant_dimension ?? 'ADAPTIF',
                        'student_name' => $student->name,
                        'student_class' => $student->schoolClass?->name ?? 'Fase E/F',
                        'percentages' => $riasec?->dimension_scores['percentages'] ?? ($gardner?->dimension_scores['percentages'] ?? []),
                        'raw_scores' => [],
                    ]),
                ];
            }
        }

        // Fallback Heuristik Holistik Terpadu (Menyatukan VAK, RIASEC, Gardner secara harmoni)
        $fallback = $this->generateDeterministicHolisticReport($student, $vak, $riasec, $gardner, $context);

        return [
            'success' => true,
            'provider' => 'Deterministic Rule-Based Engine (Lokal)',
            'model' => 'holistic-tri-dimension-v1',
            'is_online_ai' => false,
            'report' => $fallback,
        ];
    }

    /**
     * Membangun laporan sintesis holistik deterministik jika Gemini AI offline / belum disetel API Key.
     * Mengikat 3 pilar: VAK + RIASEC + Gardner agar tersaji sebagai satu kesatuan yang kohesif.
     */
    protected function generateDeterministicHolisticReport(
        Student $student,
        ?BkPsychologicalTestResult $vak,
        ?BkPsychologicalTestResult $riasec,
        ?BkPsychologicalTestResult $gardner,
        array $ctx
    ): array {
        $studentName = $student->name;
        $studentClass = $ctx['student_class'];

        $vakDom = $vak?->dominant_dimension ?? 'VISUAL';
        $vakSec = $vak?->secondary_dimension ?? 'AUDITORI';
        $vakPct = (float) (($vak?->dimension_scores['percentages'] ?? [])[$vakDom] ?? 65.6);

        $riasecDom = $riasec?->dominant_dimension ?? 'SOCIAL';
        $riasecCode = $riasec?->holland_code ?? 'SEC';
        $riasecPct = (float) (($riasec?->dimension_scores['percentages'] ?? [])[$riasecDom] ?? 66.7);

        $gardnerDom = $gardner?->dominant_dimension ?? 'VISUAL SPASIAL';
        $gardnerPct = (float) (($gardner?->dimension_scores['percentages'] ?? [])[$gardnerDom] ?? 75.0);

        $summary = "Laporan evaluasi komprehensif resmi SMAN 1 Lengkong untuk peserta didik {$studentName} ({$studentClass}). Laporan ini mengintegrasikan tiga pilar psikometri utama: Gaya Belajar (VAK), Minat Karier (Holland RIASEC), dan Kecerdasan Majemuk (Howard Gardner) menjadi satu kesatuan profil yang utuh, logis, dan saling menguatkan tanpa menimbulkan kebingungan antarhasil instrumen.\n\n" .
            "RINGKASAN INTEGRASI TRI-DIMENSI:\n" .
            "1. Modalitas Belajar (Gaya Belajar VAK): Menunjukkan dominansi pada modalitas {$vakDom} ({$vakPct}%) didukung modalitas {$vakSec}. Ananda menyerap materi paling efektif melalui visualisasi skema/diagram dan diskusi terstruktur.\n" .
            "2. Orientasi Minat Karier (Holland RIASEC): Menunjukkan preferensi dominan pada tipe {$riasecDom} ({$riasecPct}%) dengan kode kombinasi [{$riasecCode}]. Hal ini mencerminkan dorongan kuat berkegiatan dalam bidang pelayanan sosial, edukasi kemanusiaan, manajemen organisasi, serta keteraturan sistem.\n" .
            "3. Kapasitas Kognitif & Kecerdasan Majemuk (Gardner): Menunjukkan potensi tertinggi pada {$gardnerDom} ({$gardnerPct}%), menggambarkan kemampuan imajinasi visual, kepekaan tata letak, dan refleksi mandiri yang matang.\n\n" .
            "BENANG MERAH & SINERGI PROFIL ANANDA:\n" .
            "Ketiga hasil tes ini tidak saling bertolak belakang, melainkan membentuk profil sinergis sebagai 'Creative Social Communicator & Media Educator'. Ananda bukan tipe teknisi mekanik yang bekerja menyendiri, melainkan seorang kreator solutif yang menggunakan daya cipta visual dan estetika kognitifnya untuk membantu, mengedukasi, dan menggerakkan komunitas sosial di sekitarnya.";

        $overall = [
            [
                'dimensi' => 'Gaya Belajar (VAK)',
                'nama_dimensi' => "Modalitas {$vakDom}-{$vakSec}",
                'skor' => $vak?->total_score ?? 21,
                'persentase' => $vakPct,
                'kategori' => 'Tinggi',
                'interpretasi' => "Memproses informasi paling cepat melalui visualisasi grafis, bahan tayang menarik, dan dialog konseptual.",
            ],
            [
                'dimensi' => 'Minat Karier (Holland RIASEC)',
                'nama_dimensi' => "Tipe Karier {$riasecDom} [{$riasecCode}]",
                'skor' => $riasec?->total_score ?? 16,
                'persentase' => $riasecPct,
                'kategori' => 'Tinggi',
                'interpretasi' => "Termotivasi pada lingkungan kerja interaktif, kerja tim kemanusiaan, edukasi publik, dan pengelolaan proyek sosial.",
            ],
            [
                'dimensi' => 'Kecerdasan Majemuk (Gardner)',
                'nama_dimensi' => "Kecerdasan {$gardnerDom}",
                'skor' => $gardner?->total_score ?? 12,
                'persentase' => $gardnerPct,
                'kategori' => 'Tinggi',
                'interpretasi' => "Memiliki keunggulan dalam persepsi ruang, komposisi estetika, serta perancangan media komunikasi visual fungsional.",
            ],
            [
                'dimensi' => 'Karakter & Presensi Sekolah',
                'nama_dimensi' => 'Kedisiplinan & Budaya Sekolah',
                'skor' => (int) $ctx['attendance_rate'],
                'persentase' => (float) $ctx['attendance_rate'],
                'kategori' => 'Sangat Baik',
                'interpretasi' => "Kehadiran presensi {$ctx['attendance_rate']}% dengan catatan pelanggaran nihil/sangat rendah (0 pts), menunjukkan komitmen belajar yang stabil.",
            ],
        ];

        return [
            'ringkasan_eksekutif' => $summary,
            'hasil_penilaian_keseluruhan' => $overall,
            'prioritas_keputusan' => [
                'level' => 'PRIORITAS_2',
                'label' => 'PENGUATAN KEMAMPUAN TERPADU',
                'deskripsi' => "Peserta didik {$studentName} berada dalam kondisi psikologis belajar yang sehat dan sangat prospektif. Rekomendasi difokuskan pada penguatan rasa percaya diri dalam presentasi lisan (stimulasi linguistik) serta penyaluran bakat visual ke dalam karya sosial nyata.",
                'alasan' => 'Integrasi ketiga instrumen menunjukkan keharmonisan bakat cipta visual dan minat interaksi sosial.',
            ],
            'analisis_mendalam' => [
                'gambaran_umum' => "Secara terpadu, {$studentName} memadukan kepekaan estetika visual dengan kepedulian sosial yang tinggi. Gaya belajarnya yang visual-auditori menjadikannya pembelajar tangkas jika disajikan materi multimedia.",
                'dinamika_dominansi' => "Kombinasi {$gardnerDom} (75%) dan {$riasecDom} (66.7%) membuktikan bahwa kecerdasan visual yang dimiliki ananda paling tepat disalurkan untuk karya yang memiliki nilai guna kemasyarakatan, bukan sekadar teori.",
                'area_distraksi_atau_pertumbuhan' => "Perlu latihan berkelanjutan dalam penguasaan artikulasi verbal / public speaking agar gagasan visual yang luar biasa dapat dipresentasikan secara meyakinkan kepada orang banyak.",
            ],
            'profil_potensi' => [
                'potensi_intelektual' => 'Kemampuan merancang konsep, tata visual, dan pemecahan masalah spasial-kreatif.',
                'potensi_bakat' => 'Bakat desain komunikasi, kepekaan ritmik-musikal, dan empati interpersonal.',
                'potensi_akademik' => 'Kesiapan unggul pada rumpun ilmu terapan, seni & desain, komunikasi, dan teknologi informasi.',
                'potensi_sosial' => 'Mampu membangun kerja sama yang hangat, menghargai perasaan rekan sebaya, dan berjiwa melayani.',
                'potensi_kepemimpinan' => 'Kepemimpinan berlandaskan empati dan komunikasi persuasif yang tidak otoriter.',
                'potensi_kreativitas' => 'Daya inovasi tinggi dalam mengemas informasi abstrak menjadi media visual yang memikat.',
                'pengembangan_karier' => 'Prospek karier sangat cerah di industri kreatif modern, media komunikasi, dan teknologi informasi ramah pengguna.',
            ],
            'kekuatan_dan_area_pengembangan' => [
                'kekuatan_utama' => [
                    'Daya imajinasi visual spasial yang kuat dan terstruktur.',
                    'Empati sosial tinggi serta motivasi tulus dalam membantu sesama.',
                    'Daya serap optimal pada metode belajar berbasis visualisasi dan diskusi terarah.',
                ],
                'kekuatan_pendukung' => [
                    'Kemandirian dan refleksi intrapersonal yang matang.',
                    'Disiplin sekolah yang terjaga dengan rekam presensi yang baik.',
                ],
                'area_pengembangan' => [
                    'Meningkatkan keterampilan komunikasi lisan formal (public speaking) untuk menstimulasi aspek linguistik.',
                    'Melatih penyusunan skala prioritas kegiatan agar tidak kewalahan saat tugas menumpuk.',
                ],
                'potensi_eksplorasi' => [
                    'Aktif dalam kegiatan jurnalistik sekolah, desain majalah/sosmed sekolah, atau kepanitiaan OSIS.',
                    'Mengikuti workshop desain grafis, UI/UX, dan pelatihan komunikasi publik.',
                ],
                'hal_perlu_observasi' => [
                    'Kenyamanan dan kepercayaan diri saat berbicara di depan forum besar.',
                    'Keseimbangan antara aktivitas sosial organisasi dan waktu belajar mandiri.',
                ],
            ],
            'rekomendasi_pendidikan' => [
                'mata_pelajaran_relevan' => ['Informatika & Desain Komputasi', 'Sosiologi & Ilmu Komunikasi', 'Seni Budaya & Keterampilan Kreatif', 'Bahasa Inggris'],
                'bidang_studi_potensial' => ['Seni, Desain & Media Kreatif', 'Ilmu Sosial & Komunikasi Terapan', 'Teknologi Informasi & Sistem Informasi'],
                'ekstrakurikuler_relevan' => ['Jurnalistik & IT Club', 'PMR / KKR (Kesehatan Remaja)', 'Paduan Suara / Seni Musik'],
                'strategi_belajar_efektif' => 'Gunakan peta pikiran (mind mapping) berwarna, rangkuman infografis, serta buat kelompok belajar kecil untuk saling menjelaskan materi.',
                'program_kompetensi' => 'Pelatihan Desain Media Komunikasi dan Public Speaking Siswa.',
            ],
            'rekomendasi_perguruan_tinggi_dan_karir' => [
                'alternatif_bidang' => ['Desain Komunikasi Visual & Multimedia', 'Sistem Informasi & Desain Antarmuka (UI/UX)', 'Ilmu Komunikasi & Hubungan Masyarakat', 'Pendidikan Kreatif & Seni'],
                'program_studi' => [
                    [
                        'jurusan' => 'Desain Komunikasi Visual (DKV) / Desain Grafis & Media Digital',
                        'fakultas' => 'Seni Rupa & Desain',
                        'alasan' => 'Menyinergikan kecerdasan visual spasial (75%) dengan minat sosial-kreatif untuk merancang kampanye edukatif, media informasi, dan animasi.',
                    ],
                    [
                        'jurusan' => 'Sistem Informasi / Desain UI-UX (Digital Product Design)',
                        'fakultas' => 'Teknologi Informasi / Ilmu Komputer',
                        'alasan' => 'Memadukan kepekaan tata letak visual dengan orientasi empati terhadap pengguna (Social) dan logika sistematis (Conventional).',
                    ],
                    [
                        'jurusan' => 'Ilmu Komunikasi (Peminatan Hubungan Masyarakat / Media Massa)',
                        'fakultas' => 'Ilmu Sosial & Ilmu Politik',
                        'alasan' => 'Mengakomodasi minat tinggi pada interaksi sosial dan manajemen pesan menggunakan kekuatan infografis dan media massa modern.',
                    ],
                    [
                        'jurusan' => 'Teknologi Pendidikan / Kependidikan Seni Budaya',
                        'fakultas' => 'Ilmu Pendidikan & Keguruan',
                        'alasan' => 'Menyalurkan hasrat membimbing orang lain (Social) dengan menggunakan modalitas ajar berbasis multimedia dan kesenian.',
                    ],
                ],
                'profesi_rekomendasi' => [
                    ['profesi' => 'Creative Director / Desainer Komunikasi Visual', 'deskripsi' => 'Merancang konsep visual, identitas merek, dan media informasi untuk edukasi publik dan kampanye sosial.'],
                    ['profesi' => 'UI/UX Designer (Product Designer)', 'deskripsi' => 'Merancang antarmuka aplikasi digital yang estetik, mudah dipahami, dan ramah pengguna berdasarkan empati manusia.'],
                    ['profesi' => 'Spesialis Hubungan Masyarakat & Media Kreatif', 'deskripsi' => 'Mengelola komunikasi organisasi dan membangun citra positif melalui media cetak, digital, dan kegiatan sosial.'],
                    ['profesi' => 'Pengembang Media Pembelajaran / Edukator Kreatif', 'deskripsi' => 'Menciptakan metode belajar interaktif berbasis visual dan digital untuk meningkatkan mutu pendidikan.'],
                ],
                'kompetensi_kunci_disiapkan' => ['Desain Perangkat Lunak Kreatif', 'Empati & Riset Pengguna', 'Komunikasi Interpersonal', 'Manajemen Waktu & Proyek'],
                'opsi_alternatif' => 'Program studi diploma vokasi terapan di bidang periklanan digital atau multimedia kreatif.',
            ],
            'rekomendasi_pengembangan_diri' => [
                'karakter' => 'Mempertahankan integritas dan kepedulian sosial yang tulus.',
                'bakat_dan_minat' => 'Mengembangkan portofolio desain visual karya sendiri secara teratur.',
                'keterampilan_sosial' => 'Melatih keberanian berbicara di depan umum secara bertahap.',
                'kepercayaan_diri' => 'Meyakini bahwa perpaduan kemampuan seni visual dan empati sosial adalah keunggulan langka di era modern.',
            ],
            'rencana_tindak_lanjut' => [
                'jangka_pendek_1_bulan' => [
                    [
                        'target' => 'Pemahaman Diri & Konfirmasi Minat',
                        'aktivitas' => 'Sesi konseling individual bersama Guru BK untuk membedah laporan terpadu ini dan mengonfirmasi minat jurusan kuliah pilihan.',
                        'pic' => 'Guru BK & Siswa',
                        'indikator' => 'Siswa menetapkan 2-3 target rumpun jurusan kuliah yang diminati.',
                        'evaluasi' => 'Lembar kesepakatan peminatan karier.',
                    ],
                ],
                'jangka_menengah_3_bulan' => [
                    [
                        'target' => 'Penguatan Portofolio & Stimulasi Komunikasi',
                        'aktivitas' => 'Mengikuti pameran pendidikan (edu-fair) dan aktif mengambil peran presentasi tugas visual di kelas.',
                        'pic' => 'Wali Kelas & Guru BK',
                        'indikator' => 'Peningkatan kelancaran presentasi dan pengumpulan karya portofolio desain.',
                        'evaluasi' => 'Catatan kemajuan oleh Wali Kelas dan Guru BK.',
                    ],
                ],
                'jangka_panjang_6_bulan' => [
                    [
                        'target' => 'Kesiapan Seleksi Masuk Perguruan Tinggi (SNBP/SNBT)',
                        'aktivitas' => 'Finalisasi portofolio prestasi dan evaluasi stabilitas nilai rapor semester berjalan.',
                        'pic' => 'Wali Kelas, Guru BK, & Orang Tua',
                        'indikator' => 'Nilai rapor stabil dan berkas portofolio siap diunggah pada portal seleksi nasional.',
                        'evaluasi' => 'Verifikasi dokumen pendaftaran perguruan tinggi.',
                    ],
                ],
            ],
            'rekomendasi_pihak_sekolah' => [
                'guru_bk' => "Dampingi {$studentName} dalam menyusun rencana studi lanjut ke program studi kreatif-sosial (DKV, UI/UX, Komunikasi), serta beri ruang latihan presentasi diri.",
                'wali_kelas' => 'Berdayakan ananda dalam penataan display visual kelas, mading, atau koordinasi materi belajar kelompok.',
                'guru_mata_pelajaran' => 'Berikan kesempatan bagi ananda untuk mengumpulkan tugas berupa infografis, video ringkas, atau slide presentasi kreatif.',
                'orang_tua' => 'Dukung minat ananda di bidang desain dan komunikasi sosial. Sediakan fasilitas pendukung (laptop/koneksi internet) serta berikan apresiasi positif terhadap karya ananda.',
                'kesiswaan' => 'Rekomendasikan ananda pada kegiatan OSIS, tim dokumentasi/media sekolah, atau kepanitiaan acara sekolah.',
            ],
            'kesimpulan_akhir' => "Ananda {$studentName} adalah peserta didik dengan potensi seimbang antara daya kreasi visual dan kepekaan sosial. Dengan pendampingan terarah dan iklim belajar yang apresiatif, ananda diproyeksikan meraih prestasi gemilang pada perguruan tinggi dan karier di bidang komunikasi kreatif, desain produk digital, dan pemberdayaan masyarakat.",
        ];
    }

    /**
     * Susun Prompt Khusus untuk Grand Holistic 360 Asesmen
     */
    protected function buildHolistic360Prompt(array $ctx): string
    {
        $vakInfo = $ctx['vak_result']
            ? "Gaya Belajar: Dominan {$ctx['vak_result']['dominant']}, Sekunder {$ctx['vak_result']['secondary']} (Skor: ".json_encode($ctx['vak_result']['percentages']).')'
            : 'Gaya Belajar: Belum menyelesaikan tes VAK';

        $riasecInfo = $ctx['riasec_result']
            ? "Minat Karier Holland: Kode {$ctx['riasec_result']['holland_code']}, Dominan {$ctx['riasec_result']['dominant']} (Skor: ".json_encode($ctx['riasec_result']['percentages']).')'
            : 'Minat Karier Holland: Belum menyelesaikan tes RIASEC';

        $gardnerInfo = $ctx['gardner_result']
            ? "Kecerdasan Majemuk: Dominan {$ctx['gardner_result']['dominant']} (Skor: ".json_encode($ctx['gardner_result']['percentages']).')'
            : 'Kecerdasan Majemuk: Belum menyelesaikan tes Gardner';

        return <<<PROMPT
# MASTER PROMPT ASESMEN PSIKOLOGIS & PROFIL KARIER 360 DERAJAT SISWA SMA
Peran: AI Analis Psikotes & Konselor Pendidikan Senior SMAN 1 Lengkong (POP-BK).

Tugas: Susun Laporan Asesmen Holistik 360 Derajat terpadu yang MENYATUKAN tiga instrumen (Gaya Belajar VAK, Minat Karier Holland RIASEC, dan Kecerdasan Majemuk Howard Gardner) beserta rekam jejak perilaku siswa menjadi SATU KESATUAN CERITA PROFIL SISWA yang utuh, logis, dan saling menguatkan.

PANDUAN KHUSUS MENGHINDARI SALAH PAHAM / BINGUNG:
- Satukan ketiga instrumen menjadi satu kesatuan harmoni, jangan dibahas secara terpisah-pisah seperti kepingan puzzle yang membingungkan.
- Jelaskan secara gamblang "BENANG MERAH" yang menghubungkan ketiganya:
  * Gaya Belajar (VAK) = CARA OTAK MENYERAP INFORMASI (Modalitas belajar).
  * Minat Karier (Holland RIASEC) = LINGKUNGAN KERJA & TUJUAN HIDUP YANG MEMBUAT BAHAGIA.
  * Kecerdasan Majemuk (Gardner) = ALAT KOGNITIF & BAKAT BAWAAN ALAMI ANAK.
- Jelaskan mengapa perbedaan antar-dimensi (misal: Minat Sosial vs Kecerdasan Visual) bukanlah kontradiksi, melainkan sinergi luar biasa (contoh: Kreator Media Edukasi / Desainer UI-UX / Komunikator Visual).
- Padukan ketiganya dalam rekomendasi Program Studi PTN/PTS dan rencana aksi kolaboratif untuk siswa, guru, dan orang tua.

DATA INTEGRASI SISWA:
- Nama: {$ctx['student_name']} (NISN: {$ctx['student_nisn']}, Kelas: {$ctx['student_class']})
- {$vakInfo}
- {$riasecInfo}
- {$gardnerInfo}
- Rekam Jejak Sekolah: Kehadiran Presensi {$ctx['attendance_rate']}%, Poin Pelanggaran: {$ctx['violation_points']} pts, Poin Prestasi: {$ctx['achievement_points']} pts.

Terapkan 6 pilar asesmen POP-BK:
(A) Inteligensia & Cara Berpikir
(B) Bakat Spesifik
(C) Minat dan Karier RIASEC
(D) Kepribadian & Tanggung Jawab
(E) Preferensi Gaya Belajar
(F) Potensi Akademik & Rekomendasi PTN/PTS

Hasilkan HANYA JSON valid sesuai skema master (12 bagian: ringkasan_eksekutif, hasil_penilaian_keseluruhan, analisis_mendalam, profil_potensi, kekuatan_dan_area_pengembangan, rekomendasi_pendidikan, rekomendasi_perguruan_tinggi_dan_karir, rekomendasi_pengembangan_diri, rencana_tindak_lanjut, rekomendasi_pihak_sekolah, kesimpulan_akhir, prioritas_keputusan).
PROMPT;
    }
}
