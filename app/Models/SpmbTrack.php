<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'quota_percentage',
        'quota_seats',
        'description',
        'requirements',
        'is_tka',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quota_percentage' => 'float',
            'quota_seats' => 'integer',
            'is_tka' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PpdbRegistration::class, 'spmb_track_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTka($query)
    {
        return $query->where('is_tka', true);
    }

    public function getCalculatedSeats(int $totalCapacity = 360): int
    {
        if ($this->quota_seats > 0) {
            return $this->quota_seats;
        }

        if ($this->quota_percentage > 0) {
            return (int) round(($totalCapacity * $this->quota_percentage) / 100);
        }

        return 0;
    }

    public function getBadgeColorAttribute(): string
    {
        return match ($this->code) {
            'zonasi' => 'sky',
            'tka' => 'indigo',
            'afirmasi' => 'amber',
            'prestasi' => 'emerald',
            'mutasi' => 'purple',
            default => 'slate',
        };
    }
}
