<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_session_id',
        'violation_type',
        'description',
        'violation_time',
    ];

    protected function casts(): array
    {
        return [
            'violation_time' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CbtSession::class, 'cbt_session_id');
    }

    public function getLabelAttribute(): string
    {
        return match ($this->violation_type) {
            'tab_switch' => 'Beralih Tab Browser',
            'window_blur' => 'Fokus Layar Hilang / Buka Aplikasi Lain',
            'exit_fullscreen' => 'Keluar dari Mode Layar Penuh',
            'app_minimize' => 'Aplikasi Diminimalkan',
            'location_out_of_range' => 'Di Luar Radius Sekolah',
            default => ucwords(str_replace('_', ' ', $this->violation_type ?? 'Pelanggaran Keamanan')),
        };
    }
}
