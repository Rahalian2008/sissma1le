<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope for active holidays.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Find if a given date falls on a registered active holiday.
     */
    public static function getHoliday(Carbon|string $date): ?self
    {
        $dateStr = is_string($date) ? Carbon::parse($date)->toDateString() : $date->toDateString();

        return static::active()
            ->where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->first();
    }

    /**
     * Check if a given date is a holiday.
     */
    public static function isHoliday(Carbon|string $date): bool
    {
        return static::getHoliday($date) !== null;
    }

    /**
     * Get formatted range string.
     */
    public function getFormattedRangeAttribute(): string
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        if ($start->isSameDay($end)) {
            return $start->translatedFormat('d F Y');
        }

        if ($start->isSameMonth($end)) {
            return $start->format('d').' - '.$end->translatedFormat('d F Y');
        }

        return $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y');
    }

    /**
     * Calculate number of days.
     */
    public function getDaysCountAttribute(): int
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->startOfDay();

        return (int) $start->diffInDays($end) + 1;
    }
}
