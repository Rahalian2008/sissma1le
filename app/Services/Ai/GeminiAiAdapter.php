<?php

namespace App\Services\Ai;

use App\Contracts\AiAdapterInterface;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiAdapter implements AiAdapterInterface
{
    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function isAvailable(): bool
    {
        $apiKey = $this->getApiKey();

        return ! empty($apiKey);
    }

    public function generate(string $prompt, array $context = []): array
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            return [
                'success' => false,
                'content' => '',
                'provider' => 'gemini',
                'model' => 'none',
                'is_draft' => true,
                'metadata' => ['reason' => 'API Key Gemini belum dikonfigurasi di sistem'],
                'error' => 'API Key Gemini belum disetel. Masukkan API key di Pengaturan Sistem.',
            ];
        }

        $configuredModel = trim((string) (SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash')));
        $timeout = (int) config('services.ai.timeout', 25);

        $modelsToTry = array_unique(array_filter([
            $configuredModel,
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-3.5-flash-lite',
        ]));

        $lastError = null;
        foreach ($modelsToTry as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
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
                            'temperature' => 0.6,
                        ],
                    ]);

                if ($response->successful()) {
                    $candidates = $response->json('candidates');
                    $rawText = $candidates[0]['content']['parts'][0]['text'] ?? '';
                    if (! empty($rawText)) {
                        return [
                            'success' => true,
                            'content' => $rawText,
                            'provider' => 'gemini',
                            'model' => $model,
                            'is_draft' => true,
                            'metadata' => [
                                'task' => $context['task'] ?? 'umum',
                                'model' => $model,
                                'disclaimer' => 'Keluaran AI berstatus draf dan wajib ditinjau/diverifikasi oleh guru.',
                            ],
                            'error' => null,
                        ];
                    }
                }

                $lastError = 'HTTP '.$response->status().': '.substr($response->body(), 0, 150);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning("GeminiAiAdapter [{$model}] exception: ".$e->getMessage());
            }
        }

        return [
            'success' => false,
            'content' => '',
            'provider' => 'gemini',
            'model' => $configuredModel,
            'is_draft' => true,
            'metadata' => ['last_error' => $lastError],
            'error' => 'Gagal menghubungi Gemini AI: '.($lastError ?: 'Koneksi timeout'),
        ];
    }

    protected function getApiKey(): ?string
    {
        $key = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));

        return ! empty($key) ? $key : null;
    }
}
