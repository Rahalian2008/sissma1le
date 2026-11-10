<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class SpmbWave extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'wave_number',
        'academic_year',
        'start_date',
        'end_date',
        'announcement_date',
        're_registration_start',
        're_registration_end',
        'quota_seats',
        'allowed_tracks',
        'is_active',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wave_number' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'announcement_date' => 'date',
            're_registration_start' => 'date',
            're_registration_end' => 'date',
            'quota_seats' => 'integer',
            'allowed_tracks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PpdbRegistration::class, 'spmb_wave_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentlyOpen(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = Carbon::today();

        return $today->between($this->start_date, $this->end_date);
    }

    public function isUpcoming(): bool
    {
        return Carbon::today()->lt($this->start_date);
    }

    public function isPast(): bool
    {
        return Carbon::today()->gt($this->end_date);
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Nonaktif';
        }

        if ($this->isOpen()) {
            return 'Pendaftaran Dibuka';
        }

        if ($this->isUpcoming()) {
            return 'Segera Dibuka';
        }

        return 'Pendaftaran Ditutup';
    }

    public function getStatusColorAttribute(): string
    {
        if (! $this->is_active) {
            return 'slate';
        }

        if ($this->isOpen()) {
            return 'emerald';
        }

        if ($this->isUpcoming()) {
            return 'amber';
        }

        return 'rose';
    }

    /**
     * Ambil Jalur SPMB yang diizinkan untuk gelombang ini
     *
     * @return Collection<int, SpmbTrack>
     */
    public function allowedTracksCollection(): Collection
    {
        $ids = is_array($this->allowed_tracks) ? $this->allowed_tracks : [];

        if (empty($ids)) {
            return SpmbTrack::active()->orderBy('sort_order')->orderBy('id')->get();
        }

        return SpmbTrack::whereIn('id', $ids)->active()->orderBy('sort_order')->orderBy('id')->get();
    }
}
