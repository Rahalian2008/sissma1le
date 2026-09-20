<?php

namespace App\Services;

use App\Models\SchoolSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiStudentAnalysisService
{
    /**
     * Synthesize and generate comprehensive AI analysis for a student.
     * Integrates with Google Gemini API when configured, with seamless local fallback.
     */
    public function analyzeStudent(Student $student): array
    {
        // 1. Data Presensi
        $totalAttendance = $student->attendances()->count();
        $presentCount = $student->attendances()->where('status', 'HADIR')->count();
        $lateCount = $student->attendances()->where('status', 'TERLAMBAT')->count();
        $sickCount = $student->attendances()->where('status', 'SAKIT')->count();
        $permitCount = $student->attendances()->where('status', 'IZIN')->count();
        $alphaCount = $student->attendances()->where('status', 'ALPA')->count();

        $attendanceRate = $totalAttendance > 0 ? round((($presentCount + $lateCount) / $totalAttendance) * 100, 1) : 100.0;
        $punctualityRate = ($presentCount + $lateCount) > 0 ? round(($presentCount / ($presentCount + $lateCount)) * 100, 1) : 100.0;

        // 2. Data Prestasi (R1 - R10)
        $achievements = $student->achievements()->with('category')->where('status', 'DIVERIFIKASI')->get();
        $totalAchievementPoints = (int) $achievements->sum('points');
        $achievementCount = $achievements->count();
        $rewardStatus = $student->reward_status;

        $topAchievementCategories = $achievements->groupBy('category.name')->map->count()->sortDesc()->keys()->take(2)->all();
        $achievementList = $achievements->map(fn ($a) => "{$a->title} ({$a->level}, {$a->points} poin)")->take(5)->implode(', ');

        // 3. Data Pelanggaran (P1 - P6)
        $violations = $student->violations()->with('category')->where('status', 'DIVERIFIKASI')->get();
        $totalViolationPoints = (int) $violations->sum('points');
        $violationCount = $violations->count();
        $guidanceStatus = $student->guidance_status;

        $frequentViolations = $violations->groupBy('category.name')->map->count()->sortDesc()->keys()->take(2)->all();
        $violationList = $violations->map(fn ($v) => "{$v->category?->name} ({$v->points} poin)")->take(5)->implode(', ');

        // 4. Data Pembinaan
        $guidanceRecords = $student->guidanceRecords()->orderByDesc('date')->get();
        $activeGuidanceCount = $guidanceRecords->where('status', 'DALAM_PROSES')->count();

        // 5. Data 7 Kebiasaan Anak Indonesia Hebat
        $recentHabitLogs = $student->habitDailyLogs()
            ->where('date', '>=', Carbon::today()->subDays(14)->toDateString())
            ->get();
        $totalExpectedHabits = $recentHabitLogs->count();
        $completedHabits = $recentHabitLogs->where('is_completed', true)->count();
        $habitScore = $totalExpectedHabits > 0 ? round(($completedHabits / $totalExpectedHabits) * 100, 1) : 85.0;

        $habitStatus = match (true) {
            $habitScore >= 85 => 'KONSISTEN',
            $habitScore >= 70 => 'BERKEMBANG',
            $habitScore >= 50 => 'PERLU_PEMBIASAAN',
            default => 'BELUM_TERPANTAU',
        };

        // Synthesize Multi-Dimensional Score (0-100)
        $developmentIndex = round(
            ($attendanceRate * 0.25) +
            (min(100, $totalAchievementPoints * 0.5) * 0.35) +
            (max(0, 100 - ($totalViolationPoints * 1.5)) * 0.20) +
            ($habitScore * 0.20),
            1
        );

        $contextData = [
            'attendance_rate' => $attendanceRate,
            'punctuality_rate' => $punctualityRate,
            'late_count' => $lateCount,
            'sick_count' => $sickCount,
            'permit_count' => $permitCount,
            'alpha_count' => $alphaCount,
            'total_achievement_points' => $totalAchievementPoints,
            'achievement_count' => $achievementCount,
            'reward_tier' => $rewardStatus['level'],
            'achievement_list' => $achievementList ?: 'Belum ada prestasi terdata',
            'total_violation_points' => $totalViolationPoints,
            'violation_count' => $violationCount,
            'guidance_tier' => $guidanceStatus['level'],
            'guidance_action_needed' => $guidanceStatus['action_needed'],
            'violation_list' => $violationList ?: 'Tidak ada pelanggaran',
            'active_guidance_count' => $activeGuidanceCount,
            'habit_score' => $habitScore,
            'habit_status' => $habitStatus,
            'development_index' => $developmentIndex,
        ];

        // Attempt Gemini API if key is present
        $apiKey = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));
        $model = trim((string) (SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash')));

        $geminiResult = null;
        if (! empty($apiKey)) {
            $geminiResult = $this->callGeminiApi($student, $contextData, $apiKey, $model);
        }

        if ($geminiResult) {
            $ringkasan = $geminiResult['ringkasan'];
            $kesimpulan = $geminiResult['kesimpulan'];
            $rekomendasi = $geminiResult['rekomendasi'];
            $provider = 'Google Gemini AI';
            $isAiGenerated = true;
        } else {
            // Local fallback synthesis
            $ringkasan = $this->generateSummary(
                $student,
                $attendanceRate,
                $punctualityRate,
                $lateCount,
                $totalAchievementPoints,
                $achievementCount,
                $rewardStatus,
                $totalViolationPoints,
                $guidanceStatus,
                $habitScore,
                $habitStatus
            );

            $kesimpulan = $this->generateConclusions(
                $student,
                $attendanceRate,
                $punctualityRate,
                $totalAchievementPoints,
                $topAchievementCategories,
                $totalViolationPoints,
                $frequentViolations,
                $habitScore,
                $habitStatus
            );

            $rekomendasi = $this->generateRecommendations(
                $student,
                $lateCount,
                $totalAchievementPoints,
                $totalViolationPoints,
                $guidanceStatus,
                $habitScore
            );

            $provider = 'Synthesizer Pedagogis Lokal (Offline)';
            $isAiGenerated = false;
        }

        return [
            'analyzed_at' => now()->translatedFormat('d F Y H:i'),
            'provider' => $provider,
            'model' => $isAiGenerated ? $model : 'Heuristik Bawaan',
            'is_ai_generated' => $isAiGenerated,
            'has_api_key' => ! empty($apiKey),
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->schoolClass?->name ?? 'Belum terdata',
            ],
            'metrics' => [
                'attendance_rate' => $attendanceRate,
                'punctuality_rate' => $punctualityRate,
                'late_count' => $lateCount,
                'achievement_points' => $totalAchievementPoints,
                'achievement_count' => $achievementCount,
                'reward_tier' => $rewardStatus['level'],
                'violation_points' => $totalViolationPoints,
                'violation_count' => $violationCount,
                'guidance_tier' => $guidanceStatus['level'],
                'guidance_action_needed' => $guidanceStatus['action_needed'],
                'habit_score' => $habitScore,
                'habit_status' => $habitStatus,
                'development_index' => $developmentIndex,
            ],
            'ringkasan' => $ringkasan,
            'kesimpulan' => $kesimpulan,
            'rekomendasi' => $rekomendasi,
            'disclaimer' => $isAiGenerated
                ? "Analisis ini dihasilkan secara cerdas oleh Google Gemini AI ({$model}) berdasarkan 5 pilar data siswa. Digunakan sebagai panduan pendukung pembinaan."
                : 'Analisis ini dihasilkan secara objektif berdasarkan algoritma pedagogis sekolah (Mode Offline). Anda dapat mengaktifkan Google Gemini AI dengan memasukkan API Key di pengaturan.',
        ];
    }

    /**
     * Call Google Gemini REST API generateContent
     */
    protected function callGeminiApi(Student $student, array $ctx, string $apiKey, string $model): ?array
    {
        try {
            $prompt = $this->buildGeminiPrompt($student, $ctx);
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
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
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if ($response->successful()) {
                $candidates = $response->json('candidates');
                $rawText = $candidates[0]['content']['parts'][0]['text'] ?? null;

                if ($rawText) {
                    $parsed = json_decode($rawText, true);

                    if (is_array($parsed) && isset($parsed['ringkasan'], $parsed['kesimpulan'], $parsed['rekomendasi'])) {
                        return $parsed;
                    }
                }
            } else {
                Log::warning('Gemini API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Gemini API call exception: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Build pedagogical context prompt for Google Gemini
     */
    protected function buildGeminiPrompt(Student $student, array $ctx): string
    {
        $className = $student->schoolClass?->name ?? 'Umum';

        return <<<PROMPT
Anda adalah AI Konselor Pendidikan & Pakar Psikopedagogi Sekolah Menengah di Indonesia.
Tugas Anda adalah menganalisis rekam jejak multidimensi perkembangan siswa berikut ini secara objektif, berimbang, suportif, dan solutif.

[DATA SISWA]
- Nama: {$student->name}
- NIS: {$student->nis}
- Kelas: {$className}
- Tingkat Kehadiran: {$ctx['attendance_rate']}% (Tepat Waktu: {$ctx['punctuality_rate']}%, Terlambat: {$ctx['late_count']}x, Izin: {$ctx['permit_count']}x, Sakit: {$ctx['sick_count']}x, Alpa: {$ctx['alpha_count']}x)
- Prestasi Siswa: {$ctx['total_achievement_points']} Poin ({$ctx['achievement_count']} prestasi terverifikasi, Tier: {$ctx['reward_tier']}). Detail: {$ctx['achievement_list']}
- Pelanggaran Tata Tertib: {$ctx['total_violation_points']} Poin ({$ctx['violation_count']} pelanggaran terverifikasi, Status Pembinaan: {$ctx['guidance_tier']}). Detail: {$ctx['violation_list']}
- Konsistensi Gerakan 7 Kebiasaan Anak Indonesia Hebat: {$ctx['habit_score']}% (Predikat: {$ctx['habit_status']})
- Indeks Perkembangan Komposit: {$ctx['development_index']} / 100

Formatkan respons HANYA dalam JSON valid dengan struktur persis seperti berikut:
{
  "ringkasan": "Satu atau dua paragraf narasi evaluatif yang mendalam, mencakup aspek presensi, prestasi, kedisiplinan, dan kebiasaan karakter positif siswa.",
  "kesimpulan": [
    {
      "dimension": "Kedisiplinan & Waktu",
      "sentiment": "positive",
      "insight": "Pola analisis mengenai waktu dan kehadiran siswa..."
    },
    {
      "dimension": "Potensi Diri & Minat Bakat",
      "sentiment": "positive",
      "insight": "Pola analisis potensi prestasi dan minat siswa..."
    },
    {
      "dimension": "Karakter 7 Kebiasaan",
      "sentiment": "positive",
      "insight": "Pola analisis pelaksanaan 7 kebiasaan anak hebat..."
    }
  ],
  "rekomendasi": {
    "wali_kelas": {
      "role": "Wali Kelas",
      "actions": [
        "Aksi konkret 1 yang perlu dilakukan wali kelas...",
        "Aksi konkret 2..."
      ]
    },
    "bk": {
      "role": "Guru Bimbingan Konseling (BK)",
      "actions": [
        "Aksi konkret 1 untuk guru BK...",
        "Aksi konkret 2..."
      ]
    },
    "orang_tua": {
      "role": "Orang Tua / Wali",
      "actions": [
        "Aksi konkret 1 untuk orang tua di rumah...",
        "Aksi konkret 2..."
      ]
    },
    "siswa": {
      "role": "Siswa (Refleksi Diri)",
      "actions": [
        "Aksi konkret 1 bagi siswa...",
        "Aksi konkret 2..."
      ]
    }
  }
}
Catatan: Nilai sentiment harus salah satu dari: "positive", "neutral", atau "warning". Tulis dalam Bahasa Indonesia yang santun, profesional, dan edukatif.
PROMPT;
    }

    protected function generateSummary(
        Student $student,
        float $attRate,
        float $puncRate,
        int $lateCount,
        int $achPoints,
        int $achCount,
        array $rewardStatus,
        int $violPoints,
        array $guidanceStatus,
        float $habitScore,
        string $habitStatus
    ): string {
        $parts = [];

        if ($attRate >= 95) {
            $parts[] = "Siswa {$student->name} menunjukkan tingkat kehadiran yang sangat prima ({$attRate}%) pada tahun ajaran ini.";
        } elseif ($attRate >= 85) {
            $parts[] = "Tingkat kehadiran siswa {$student->name} berada pada kategori baik ({$attRate}%) dengan {$lateCount} kali catatan keterlambatan tercatat.";
        } else {
            $parts[] = "Perhatian diperlukan terkait kehadiran ({$attRate}%) dengan catatan keterlambatan dan izin yang memerlukan tindak lanjut.";
        }

        if ($achPoints > 0) {
            $parts[] = "Di bidang prestasi, siswa telah mengumpulkan total {$achPoints} poin dari {$achCount} kegiatan terverifikasi, saat ini berada pada status '{$rewardStatus['level']}' ({$rewardStatus['badge']}).";
        } else {
            $parts[] = 'Di bidang prestasi, siswa belum memiliki catatan capaian terverifikasi dalam master poin penghargaan tahun ajaran ini.';
        }

        if ($violPoints === 0) {
            $parts[] = 'Siswa memiliki rekam jejak kedisiplinan yang bersih tanpa poin pelanggaran tata tertib.';
        } elseif ($violPoints < 75) {
            $parts[] = "Catatan pelanggaran siswa berada dalam batas wajar ({$violPoints} poin, status {$guidanceStatus['level']}) dan belum memerlukan tindakan khusus.";
        } else {
            $parts[] = "PENTING: Siswa telah mencapai akumulasi {$violPoints} poin pelanggaran dan berada pada status '{$guidanceStatus['level']}' yang membutuhkan penanganan sesuai prosedur tata tertib.";
        }

        $parts[] = "Pada Gerakan 7 Kebiasaan Anak Indonesia Hebat, siswa mencatatkan tingkat konsistensi sebesar {$habitScore}% dengan predikat '{$habitStatus}'.";

        return implode(' ', $parts);
    }

    protected function generateConclusions(
        Student $student,
        float $attRate,
        float $puncRate,
        int $achPoints,
        array $topAch,
        int $violPoints,
        array $freqViol,
        float $habitScore,
        string $habitStatus
    ): array {
        $conclusions = [];

        if ($attRate >= 90 && $puncRate >= 90) {
            $conclusions[] = [
                'dimension' => 'Kedisiplinan & Waktu',
                'sentiment' => 'positive',
                'insight' => 'Siswa memiliki kebiasaan manajemen waktu yang stabil, mampu konsisten hadir tepat waktu sebelum bel berbunyi.',
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Kedisiplinan & Waktu',
                'sentiment' => 'neutral',
                'insight' => 'Terdapat fluktuasi kedisiplinan kedatangan di pagi hari yang berpotensi memengaruhi fokus belajar pada jam-jam awal.',
            ];
        }

        if ($achPoints >= 75) {
            $areas = ! empty($topAch) ? implode(' dan ', $topAch) : 'akademik/non-akademik';
            $conclusions[] = [
                'dimension' => 'Potensi Diri & Minat Bakat',
                'sentiment' => 'positive',
                'insight' => "Siswa memiliki inisiatif kompetitif yang kuat, khususnya menonjol pada kategori {$areas}.",
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Potensi Diri & Minat Bakat',
                'sentiment' => 'neutral',
                'insight' => 'Potensi siswa belum sepenuhnya terakselerasi ke dalam ajang perlombaan atau kegiatan ekstrakurikuler terstruktur.',
            ];
        }

        if ($habitScore >= 80) {
            $conclusions[] = [
                'dimension' => 'Karakter 7 Kebiasaan',
                'sentiment' => 'positive',
                'insight' => 'Jurnal harian menunjukkan rutinitas yang sehat, seimbang antara ibadah, belajar mandiri, dan istirahat.',
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Karakter 7 Kebiasaan',
                'sentiment' => 'warning',
                'insight' => 'Pengisian dan pelaksanaan pembiasaan harian memerlukan dorongan motivasi serta pemantauan berkala dari lingkungan rumah.',
            ];
        }

        return $conclusions;
    }

    protected function generateRecommendations(
        Student $student,
        int $lateCount,
        int $achPoints,
        int $violPoints,
        array $guidanceStatus,
        float $habitScore
    ): array {
        return [
            'wali_kelas' => [
                'role' => 'Wali Kelas',
                'actions' => [
                    $lateCount > 2 ? 'Lakukan dialog santai mengenai kendala transportasi atau kebiasaan pagi siswa.' : 'Berikan apresiasi atas konsistensi kehadiran dan peran aktif siswa di kelas.',
                    $achPoints >= 50 ? 'Dorong siswa untuk mempertahankan prestasi menuju target penghargaan Anugerah Waluya Utama.' : 'Ajak siswa mengeksplorasi ekstrakurikuler atau bidang kompetisi baru.',
                ],
            ],
            'bk' => [
                'role' => 'Guru Bimbingan Konseling (BK)',
                'actions' => [
                    $guidanceStatus['action_needed']
                        ? "Segera jadwalkan sesi '{$guidanceStatus['level']}' bersama siswa dan orang tua untuk menyusun rencana komitmen perbaikan."
                        : 'Lakukan sesi konseling suportif berkala untuk memetakan rencana studi lanjut dan minat karier.',
                    'Dukung siswa dalam mengembangkan strategi adaptasi belajar yang seimbang.',
                ],
            ],
            'orang_tua' => [
                'role' => 'Orang Tua / Wali',
                'actions' => [
                    $habitScore < 80 ? 'Dampingi rutinitas malam hari agar anak dapat tidur tepat waktu dan bangun pagi lebih bugar.' : 'Pertahankan suasana rumah yang kondusif bagi amalan ibadah dan belajar mandiri.',
                    'Luangkan waktu 10-15 menit setiap hari untuk mendengarkan refleksi aktivitas anak di sekolah.',
                ],
            ],
            'siswa' => [
                'role' => 'Siswa (Refleksi Diri)',
                'actions' => [
                    'Pertahankan kebiasaan baik yang sudah konsisten dan jadikan sebagai inspirasi bagi teman sebaya.',
                    'Manfaatkan jurnal 7 Kebiasaan sebagai sarana melatih kejujuran, tanggung jawab, dan kemandirian.',
                ],
            ],
        ];
    }
}
