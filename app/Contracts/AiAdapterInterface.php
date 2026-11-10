<?php

namespace App\Contracts;

interface AiAdapterInterface
{
    /**
     * Nama identifikasi penyedia (e.g. 'gemini', 'mock', 'openai').
     */
    public function getProviderName(): string;

    /**
     * Memeriksa apakah adapter siap digunakan (kunci API / endpoint valid).
     */
    public function isAvailable(): bool;

    /**
     * Mengirim prompt dan konteks ke provider AI untuk menghasilkan draf dokumen pembelajaran.
     *
     * @param  array<string, mixed>  $context
     * @return array{
     *     success: bool,
     *     content: string,
     *     provider: string,
     *     model: string,
     *     is_draft: bool,
     *     metadata: array<string, mixed>,
     *     error: ?string
     * }
     */
    public function generate(string $prompt, array $context = []): array;
}
