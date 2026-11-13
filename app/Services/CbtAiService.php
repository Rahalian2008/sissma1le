<?php

namespace App\Services;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CbtAiService
{
    /**
     * Generate soal CBT berbasis standar Kemdikbudristek / Kurikulum Merdeka & AKM.
     *
     * @param array{
     *     subject: string,
     *     grade: string,
     *     topic: string,
     *     question_types: array<string>,
     *     count: int,
     *     cognitive_focus: string,
     *     additional_notes?: string
     * } $params
     * @return array<int, array>
     */
    public function generateQuestions(array $params): array
    {
        $apiKey = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));
        $model = trim((string) (SchoolSetting::get('gemini_model') ?: 'gemini-2.5-flash'));

        $subject = $params['subject'] ?? 'Umum';
        $grade = $params['grade'] ?? 'Fase E (Kelas X)';
        $topic = $params['topic'] ?? 'Materi Pembelajaran';
        $types = ! empty($params['question_types']) ? $params['question_types'] : ['multiple_choice'];
        $count = max(1, min(20, (int) ($params['count'] ?? 5)));
        $cogFocus = $params['cognitive_focus'] ?? 'Campuran L1, L2, L3 (HOTS)';
        $notes = $params['additional_notes'] ?? '';

        $prompt = <<<PROMPT
Anda adalah Pakar Asesmen Pendidikan Nasional Kemdikbudristek RI dan Pembuat Soal Ujian Online Berbasis Komputer (CBT / ANBK).
Tugas Anda adalah menyusun paket soal ujian berkualitas tinggi, berkarakter Kurikulum Merdeka (Deep Learning & Penalaran HOTS), kontekstual, dan valid.

MATA PELAJARAN: {$subject}
FASE / KELAS: {$grade}
MATERI POKOK / CAPAIAN PEMBELAJARAN (CP): {$topic}
TIPE SOAL YANG HARUS DIBUAT: {$this->formatTypesList($types)}
JUMLAH SOAL: {$count} butir soal
FOKUS TINGKAT KOGNITIF: {$cogFocus}
CATATAN TAMBAHAN: {$notes}

ATURAN STRUKTUR SETIAP TIPE SOAL:
1. "multiple_choice" (Pilihan Ganda Biasa):
   - options: objek key {"A": "teks A", "B": "teks B", "C": "teks C", "D": "teks D", "E": "teks E"}
   - correct_answer: string key huruf, misal "B"
2. "complex_multiple_choice" (Pilihan Ganda Kompleks):
   - options: objek key {"A": "pernyataan A", "B": "pernyataan B", "C": "pernyataan C", "D": "pernyataan D"}
   - correct_answer: array opsi benar, misal ["A", "C"]
3. "true_false" (Benar / Salah):
   - options: array objek pernyataan [{"id": "S1", "statement": "Pernyataan 1"}, {"id": "S2", "statement": "Pernyataan 2"}]
   - correct_answer: objek key-value boolean [{"id": "S1", "is_true": true}, {"id": "S2", "is_true": false}]
4. "matching" (Menjodohkan):
   - options: objek berisi "premises" (daftar sisi kiri) dan "responses" (daftar sisi kanan pilihan)
     contoh: {"premises": [{"id": 1, "text": "Istilah 1"}, {"id": 2, "text": "Istilah 2"}], "responses": [{"id": "A", "text": "Definisi A"}, {"id": "B", "text": "Definisi B"}]}
   - correct_answer: array pasangan menjodohkan benar [{"premise_id": 1, "response_id": "B"}, {"premise_id": 2, "response_id": "A"}]
5. "short_answer" (Isian Singkat):
   - options: null
   - correct_answer: string jawaban singkat atau kata kunci utama yang tepat
6. "essay" (Uraian / Esai):
   - options: null
   - correct_answer: pedoman penskoran atau rubrik jawaban ideal guru

KEMBALIKAN OUTPUT HANYA DALAM FORMAT JSON MURNI TANPA MARKDOWN BACKTICKS, dengan struktur:
{
  "questions": [
    {
      "question_type": "multiple_choice|complex_multiple_choice|true_false|matching|short_answer|essay",
      "stimulus": "Teks bacaan, tabel, studi kasus, data, atau narasi kontekstual (bisa string kosong jika tanpa stimulus)",
      "question_text": "Teks pertanyaan instruksional yang jelas",
      "options": {...},
      "correct_answer": ...,
      "explanation": "Penjelasan ilmiah dan pembahasan rinci mengapa jawaban tersebut benar",
      "score_weight": 2.0,
      "learning_objective": "Tujuan Pembelajaran atau Capaian Pembelajaran terkait",
      "indicator": "Disajikan ..., peserta didik dapat ...",
      "cognitive_level": "L1|L2|L3",
      "difficulty_level": "mudah|sedang|sukar"
    }
  ]
}
PROMPT;

        if (! empty($apiKey)) {
            $result = $this->callGemini($apiKey, $model, $prompt);
            if (! empty($result['questions']) && is_array($result['questions'])) {
                return $result['questions'];
            }
        }

        // Fallback generator cerdas jika AI API offline/limit
        return $this->generateFallbackQuestions($params);
    }

    private function callGemini(string $apiKey, string $model, string $prompt): ?array
    {
        $modelsToTry = array_unique([$model, 'gemini-2.5-flash', 'gemini-1.5-flash', 'gemini-flash-latest']);

        foreach ($modelsToTry as $m) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key={$apiKey}";
                $response = Http::withoutVerifying()->timeout(35)->post($endpoint, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'topP' => 0.9,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $cleanJson = trim($rawText);
                    $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
                    $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);

                    $decoded = json_decode($cleanJson, true);
                    if (is_array($decoded) && isset($decoded['questions'])) {
                        return $decoded;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini AI error on model {$m} for CBT: ".$e->getMessage());
            }
        }

        return null;
    }

    private function formatTypesList(array $types): string
    {
        $map = [
            'multiple_choice' => 'Pilihan Ganda Biasa (1 Benar)',
            'complex_multiple_choice' => 'Pilihan Ganda Kompleks (>1 Benar)',
            'true_false' => 'Benar / Salah (Pernyataan)',
            'matching' => 'Menjodohkan (Matching)',
            'short_answer' => 'Uraian Singkat / Isian',
            'essay' => 'Uraian / Esai',
        ];

        return implode(', ', array_map(fn ($t) => $map[$t] ?? $t, $types));
    }

    /**
     * Generator template cadangan jika API offline
     */
    private function generateFallbackQuestions(array $params): array
    {
        $subject = $params['subject'] ?? 'Mata Pelajaran';
        $topic = $params['topic'] ?? 'Konsep Dasar';
        $count = max(1, min(10, (int) ($params['count'] ?? 3)));
        $types = ! empty($params['question_types']) ? $params['question_types'] : ['multiple_choice'];

        $questions = [];
        for ($i = 1; $i <= $count; $i++) {
            $type = $types[($i - 1) % count($types)];
            $questions[] = match ($type) {
                'complex_multiple_choice' => [
                    'question_type' => 'complex_multiple_choice',
                    'stimulus' => "Perhatikan stimulus wacana tentang penerapan {$topic} dalam kehidupan sehari-hari.",
                    'question_text' => "Berdasarkan materi {$topic} pada mata pelajaran {$subject}, manakah pernyataan berikut yang bernilai benar? (Pilih lebih dari satu)",
                    'options' => [
                        'A' => "Pernyataan prinsip pertama yang mendukung efektivitas {$topic}.",
                        'B' => 'Pernyataan kedua yang bertentangan dengan kaidah dasar.',
                        'C' => "Pernyataan ketiga yang membuktikan korelasi positif penerapan {$topic}.",
                        'D' => 'Pernyataan keempat yang kurang relevan terhadap kasus.',
                    ],
                    'correct_answer' => ['A', 'C'],
                    'explanation' => "Pilihan A dan C benar karena sesuai dengan kaidah teoritis dan implementasi nyata {$topic}.",
                    'score_weight' => 2.0,
                    'learning_objective' => "Memahami dan menganalisis prinsip komprehensif {$topic}.",
                    'indicator' => 'Disajikan wacana kontekstual, peserta didik dapat mengidentifikasi multi-pernyataan yang valid.',
                    'cognitive_level' => 'L3',
                    'difficulty_level' => 'sedang',
                ],
                'true_false' => [
                    'question_type' => 'true_false',
                    'stimulus' => "Kajian komparatif terkait kaidah dan implementasi {$topic}.",
                    'question_text' => "Tentukan kebenaran dari setiap pernyataan mengenai {$topic} di bawah ini:",
                    'options' => [
                        ['id' => 'S1', 'statement' => "Karakteristik fundamental {$topic} berfokus pada efisiensi proses."],
                        ['id' => 'S2', 'statement' => "Penerapan konsep {$topic} tidak memerlukan validasi data empiris."],
                        ['id' => 'S3', 'statement' => "Dampak langsung pemahaman {$topic} meningkatkan daya nalar kritis siswa."],
                    ],
                    'correct_answer' => [
                        ['id' => 'S1', 'is_true' => true],
                        ['id' => 'S2', 'is_true' => false],
                        ['id' => 'S3', 'is_true' => true],
                    ],
                    'explanation' => 'Pernyataan 1 dan 3 bernilai BENAR sesuai teori, sedangkan pernyataan 2 SALAH karena validasi data selalu diperlukan.',
                    'score_weight' => 2.0,
                    'learning_objective' => "Mengevaluasi kebenaran pernyataan berdasarkan konsep {$topic}.",
                    'indicator' => 'Disajikan tabel pernyataan, peserta didik dapat menentukan status Benar/Salah secara kritis.',
                    'cognitive_level' => 'L2',
                    'difficulty_level' => 'sedang',
                ],
                'matching' => [
                    'question_type' => 'matching',
                    'stimulus' => "Daftar istilah penting dan peran fungsional dalam materi {$topic}.",
                    'question_text' => 'Jodohkan istilah di sebelah kiri dengan deskripsi atau pasangan yang tepat di sebelah kanan:',
                    'options' => [
                        'premises' => [
                            ['id' => 1, 'text' => "Komponen Utama {$topic}"],
                            ['id' => 2, 'text' => "Tujuan Pembelajaran {$topic}"],
                            ['id' => 3, 'text' => "Tantangan Implementasi {$topic}"],
                        ],
                        'responses' => [
                            ['id' => 'A', 'text' => 'Mengembangkan pemahaman mendalam dan kecakapan praktis'],
                            ['id' => 'B', 'text' => 'Fondasi struktural yang menopang seluruh alur kerja'],
                            ['id' => 'C', 'text' => 'Kebutuhan adaptasi teknologi dan penguatan konsistensi'],
                        ],
                    ],
                    'correct_answer' => [
                        ['premise_id' => 1, 'response_id' => 'B'],
                        ['premise_id' => 2, 'response_id' => 'A'],
                        ['premise_id' => 3, 'response_id' => 'C'],
                    ],
                    'explanation' => 'Pasangan 1-B, 2-A, dan 3-C merupakan relasi paling tepat antara konsep dan fungsi.',
                    'score_weight' => 3.0,
                    'learning_objective' => "Menghubungkan relasi antarkonsep dalam materi {$topic}.",
                    'indicator' => 'Disajikan premis dan respon, peserta didik dapat menjodohkan pasangan konsep secara tepat.',
                    'cognitive_level' => 'L2',
                    'difficulty_level' => 'sedang',
                ],
                'short_answer' => [
                    'question_type' => 'short_answer',
                    'stimulus' => '',
                    'question_text' => "Sebutkan istilah kunci yang merujuk pada prinsip inti pembelajaran {$topic} dalam konteks {$subject}!",
                    'options' => null,
                    'correct_answer' => $topic,
                    'explanation' => "Jawaban tepat merujuk pada konsep {$topic}.",
                    'score_weight' => 1.5,
                    'learning_objective' => "Mengingat dan menuliskan istilah baku dalam materi {$topic}.",
                    'indicator' => 'Peserta didik dapat mengisikan kata kunci konsep yang tepat.',
                    'cognitive_level' => 'L1',
                    'difficulty_level' => 'mudah',
                ],
                'essay' => [
                    'question_type' => 'essay',
                    'stimulus' => "Perkembangan paradigma kurikulum menuntut pemahaman mendalam pada materi {$topic}.",
                    'question_text' => "Jelaskan secara komprehensif bagaimana implementasi {$topic} dapat mengatasi permasalahan nyata pada bidang {$subject}! Sertakan contoh konkret.",
                    'options' => null,
                    'correct_answer' => "Rubrik Penilaian:\n1. Kesesuaian konsep (Bobot 40%)\n2. Logika analisis dan kedalaman argumen (Bobot 30%)\n3. Contoh konkret dan solusi kontekstual (Bobot 30%)",
                    'explanation' => 'Jawaban esai dinilai dari kedalaman pemahaman konsep, sistematika berpikir analitis, dan relevansi contoh nyata yang diberikan siswa.',
                    'score_weight' => 5.0,
                    'learning_objective' => "Mengelaborasi dan mengevaluasi solusi permasalahan berbasis {$topic}.",
                    'indicator' => 'Peserta didik mampu menguraikan solusi terstruktur dan argumentatif atas studi kasus.',
                    'cognitive_level' => 'L3',
                    'difficulty_level' => 'sukar',
                ],
                default => [
                    'question_type' => 'multiple_choice',
                    'stimulus' => "Di era modern, penguasaan {$topic} memegang peranan krusial dalam pembentukan kompetensi {$subject}.",
                    'question_text' => "Berdasarkan materi {$topic}, manakah kesimpulan yang paling tepat mengenai fungsi utamanya?",
                    'options' => [
                        'A' => "Memfasilitasi pemahaman terintegrasi dan penalaran logis dalam {$subject}.",
                        'B' => 'Menghindari adanya evaluasi berkala demi kenyamanan proses.',
                        'C' => 'Menggantikan seluruh proses konvensional tanpa adanya adaptasi bertahap.',
                        'D' => 'Mempersempit ruang eksplorasi peserta didik pada satu sumber saja.',
                        'E' => 'Meniadakan peran kolaborasi antarsiswa dalam kegiatan belajar.',
                    ],
                    'correct_answer' => 'A',
                    'explanation' => "Opsi A adalah jawaban paling tepat karena tujuan utama {$topic} adalah membangun penalaran logis dan pemahaman yang utuh.",
                    'score_weight' => 1.0,
                    'learning_objective' => "Mengidentifikasi esensi fungsi utama {$topic}.",
                    'indicator' => 'Disajikan deskripsi materi, peserta didik dapat menentukan kesimpulan fungsi dengan tepat.',
                    'cognitive_level' => 'L2',
                    'difficulty_level' => 'sedang',
                ],
            };
        }

        return $questions;
    }
}
