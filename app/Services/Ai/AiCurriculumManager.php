<?php

namespace App\Services\Ai;

use App\Contracts\AiAdapterInterface;
use App\Models\AiGenerationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiCurriculumManager
{
    protected AiAdapterInterface $primaryAdapter;

    protected AiAdapterInterface $fallbackAdapter;

    public function __construct(?AiAdapterInterface $primaryAdapter = null, ?AiAdapterInterface $fallbackAdapter = null)
    {
        $this->fallbackAdapter = $fallbackAdapter ?? new MockAiAdapter;

        $configuredProvider = config('services.ai.provider', 'gemini');
        if ($primaryAdapter !== null) {
            $this->primaryAdapter = $primaryAdapter;
        } elseif ($configuredProvider === 'mock') {
            $this->primaryAdapter = $this->fallbackAdapter;
        } else {
            $this->primaryAdapter = new GeminiAiAdapter;
        }
    }

    /**
     * Dapatkan adapter utama yang sedang aktif.
     */
    public function getActiveAdapter(): AiAdapterInterface
    {
        return $this->primaryAdapter->isAvailable() ? $this->primaryAdapter : $this->fallbackAdapter;
    }

    /**
     * Hasilkan draf konten pembelajaran dengan mekanisme fallback aman dan jaminan etika AI.
     *
     * @param  array<string, mixed>  $context
     * @return array{
     *     success: bool,
     *     content: string,
     *     provider: string,
     *     model: string,
     *     is_draft: bool,
     *     used_fallback: bool,
     *     metadata: array<string, mixed>,
     *     error: ?string
     * }
     */
    public function generateDraft(string $prompt, array $context = []): array
    {
        // 1. Perlindungan Privasi: Cegah kebocoran PII siswa pada konteks
        $sanitizedPrompt = $this->sanitizePromptForPrivacy($prompt);
        $taskType = $context['task'] ?? 'umum';

        $startTime = microtime(true);
        $usedFallback = false;

        // 2. Coba eksekusi melalui adapter utama bila tersedia
        if ($this->primaryAdapter->isAvailable()) {
            $result = $this->primaryAdapter->generate($sanitizedPrompt, $context);
            if ($result['success'] && ! empty($result['content'])) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                $this->logUsage($this->primaryAdapter->getProviderName(), $result['model'], true, $duration, $taskType, $sanitizedPrompt, false);

                return array_merge($result, [
                    'is_draft' => true,
                    'used_fallback' => false,
                ]);
            }
        }

        // 3. Fallback cerdas ke MockAiAdapter jika provider utama tidak tersedia atau gagal
        $usedFallback = true;
        $result = $this->fallbackAdapter->generate($sanitizedPrompt, $context);
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->logUsage('mock', 'mock-engine', true, $duration, $taskType, $sanitizedPrompt, true);

        return array_merge($result, [
            'is_draft' => true,
            'used_fallback' => true,
            'metadata' => array_merge($result['metadata'] ?? [], [
                'fallback_notice' => 'Menggunakan mesin draf lokal karena koneksi ke AI provider eksternal belum tersedia.',
            ]),
        ]);
    }

    /**
     * Generator butir soal HOTS (Penalaran L3) terintegrasi kearifan lokal & stimulus nyata.
     *
     * @return array<string, mixed>
     */
    public function generateHotsQuestionsWithLocalContext(string $topic, string $grade = 'X', string $localContext = 'Sukabumi / Jawa Barat'): array
    {
        $prompt = "Buatkan rekomendasi butir soal HOTS (Level Kognitif L3 - Penalaran/Analisis) untuk topik '{$topic}' "
            ."pada tingkat Kelas {$grade}. "
            ."Gunakan stimulus kontekstual berbasis fenomena lingkungan, potensi daerah, atau kearifan lokal {$localContext}. "
            .'Sertakan kunci jawaban dan rubrik penilaian analitis yang jelas.';

        return $this->generateDraft($prompt, [
            'task' => 'hots_question',
            'topic' => $topic,
            'grade' => $grade,
            'local_context' => $localContext,
        ]);
    }

    /**
     * Generator draf Modul Ajar berdiferensiasi (Konten, Proses, Produk).
     *
     * @return array<string, mixed>
     */
    public function generateDifferentiatedLessonPlan(string $topic, string $grade = 'X', string $learningModel = 'PBL'): array
    {
        $prompt = "Susun draf Modul Ajar Kurikulum Merdeka berdiferensiasi untuk topik '{$topic}' pada tingkat Kelas {$grade} "
            ."dengan model pembelajaran {$learningModel}. "
            .'Uraikan strategi: 1) Diferensiasi Konten (Visual/Auditori/Kinestetik), 2) Diferensiasi Proses (Tiering aktivitas bertingkat), '
            .'dan 3) Diferensiasi Produk penyajian hasil belajar siswa.';

        return $this->generateDraft($prompt, [
            'task' => 'diferensiasi',
            'topic' => $topic,
            'grade' => $grade,
            'learning_model' => $learningModel,
        ]);
    }

    /**
     * Analisis diagnostik pola kesulitan belajar siswa & saran tindak lanjut remedial.
     *
     * @param  array<string>  $failedAspects
     * @return array<string, mixed>
     */
    public function analyzeDiagnosticRemediation(string $tpTitle, array $failedAspects = []): array
    {
        $aspectsStr = ! empty($failedAspects) ? implode(', ', $failedAspects) : 'konsep dasar dan studi kasus pemecahan masalah';
        $prompt = "Lakukan analisis diagnostik pedagogis untuk Tujuan Pembelajaran: '{$tpTitle}'. "
            ."Aspek yang belum dikuasai siswa: {$aspectsStr}. "
            .'Berikan: 1) Akar penyebab kesulitan kognitif, 2) Metode intervensi remedial terfokus (tutor sebaya / scaffolding), '
            .'dan 3) Format kuis asesmen diagnostik ulang 3-5 pertanyaan kunci.';

        return $this->generateDraft($prompt, [
            'task' => 'diagnostik',
            'topic' => $tpTitle,
            'failed_aspects' => $failedAspects,
        ]);
    }

    /**
     * Dapatkan ringkasan statistik utilisasi AI untuk panel dashboard monitoring.
     *
     * @return array<string, mixed>
     */
    public function getUsageStatistics(): array
    {
        $totalLogs = 0;
        $fallbackCount = 0;
        $avgDuration = 0;
        $taskCounts = [];

        if (class_exists(AiGenerationLog::class)) {
            $totalLogs = AiGenerationLog::count();
            $fallbackCount = AiGenerationLog::where('is_fallback', true)->count();
            $avgDuration = $totalLogs > 0 ? round((float) AiGenerationLog::avg('response_time_ms'), 1) : 0;
            $taskCounts = AiGenerationLog::selectRaw('task_type, count(*) as count')
                ->groupBy('task_type')
                ->pluck('count', 'task_type')
                ->toArray();
        }

        return [
            'total_requests' => $totalLogs,
            'fallback_count' => $fallbackCount,
            'fallback_rate' => $totalLogs > 0 ? round(($fallbackCount / $totalLogs) * 100, 1) : 0,
            'avg_response_time' => $avgDuration,
            'by_task_type' => $taskCounts,
            'primary_provider' => $this->primaryAdapter->getProviderName(),
            'is_primary_available' => $this->primaryAdapter->isAvailable(),
            'fallback_provider' => $this->fallbackAdapter->getProviderName(),
        ];
    }

    /**
     * Sanitasi prompt untuk mematuhi prinsip privasi data siswa (tanpa NIK, NISN, atau no HP).
     */
    protected function sanitizePromptForPrivacy(string $prompt): string
    {
        // Ganti potensi nomor telepon / NIK 16 digit dengan [DATA_TERLINDUNG]
        $sanitized = preg_replace('/\b\d{16}\b/', '[NIK_TERLINDUNG]', $prompt);
        $sanitized = preg_replace('/\b08\d{8,11}\b/', '[NOMOR_TERLINDUNG]', (string) $sanitized);

        return (string) $sanitized;
    }

    /**
     * Catat log audit penggunaan AI tanpa menyimpan rahasia/kunci API.
     */
    protected function logUsage(string $provider, string $model, bool $success, float $durationMs, string $taskType = 'umum', string $promptSummary = '', bool $isFallback = false): void
    {
        Log::info("AI Service Request: Provider={$provider}, Model={$model}, Task={$taskType}, Success=".($success ? '1' : '0').", Duration={$durationMs}ms");

        try {
            if (class_exists(AiGenerationLog::class)) {
                AiGenerationLog::create([
                    'user_id' => Auth::id(),
                    'task_type' => $taskType,
                    'provider' => $provider,
                    'model' => $model,
                    'prompt_summary' => Str::limit($promptSummary, 250),
                    'is_fallback' => $isFallback,
                    'response_time_ms' => (int) round($durationMs),
                    'token_estimate' => (int) ceil(strlen($promptSummary) / 4) + 150,
                    'status' => $success ? 'SUCCESS' : 'FAILED',
                    'metadata' => [
                        'ip' => request()?->ip(),
                        'route' => request()?->route()?->getName(),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Logging failure should never break the request
        }
    }
}
