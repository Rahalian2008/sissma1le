<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'max_tokens' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): self
    {
        $setting = static::first();

        $defaultModel = config('services.gemini.model', 'gemini-3.5-flash-lite');

        if (! $setting) {
            return static::create([
                'provider' => 'gemini',
                'model' => $defaultModel,
                'temperature' => 0.70,
                'max_tokens' => 2048,
                'system_prompt' => 'Anda adalah SIKATAS AI Assistant, asisten kecerdasan buatan terpadu untuk ketatausahaan SMA1LE. Berikan analisis berbasis data aktual, obyektif, akurat, dan dapat ditindaklanjuti secara online.',
                'is_active' => true,
            ]);
        }

        // Jika sebelumnya masih default local_heuristic, otomatis switch ke Google Gemini sesuai preferensi sistem
        if ($setting->provider === 'local_heuristic') {
            $setting->update([
                'provider' => 'gemini',
                'model' => $defaultModel,
            ]);
        }

        // Migrasi model lama yang telah pensiun (1.5 / 2.0 / 2.5) ke 3.5-flash-lite
        $obsoleteModels = [
            'gemini-1.5-flash',
            'gemini-1.5-pro',
            'gemini-2.0-flash',
            'gemini-2.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-2.5-pro',
        ];
        if (in_array($setting->model, $obsoleteModels, true)) {
            $setting->update([
                'model' => $defaultModel,
            ]);
        }

        if (empty($setting->api_key) && ! empty(env('GEMINI_API_KEY'))) {
            $setting->update([
                'api_key' => env('GEMINI_API_KEY'),
            ]);
        }

        return $setting;
    }

    /**
     * Dapatkan API key efektif (dari database atau fallback dari .env)
     */
    public function getEffectiveApiKey(): ?string
    {
        if (! empty($this->api_key)) {
            return $this->api_key;
        }

        return config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
    }

    /**
     * Cek apakah engine online aktif dan siap terhubung
     */
    public function getIsOnlineReadyAttribute(): bool
    {
        return $this->is_active && $this->provider === 'gemini' && ! empty($this->getEffectiveApiKey());
    }
}
