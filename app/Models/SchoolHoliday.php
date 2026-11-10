<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SchoolHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'description',
        'target_classes',
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
            'target_classes' => 'array',
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
    public static function getHoliday(Carbon|string $date, $classId = null): ?self
    {
        $dateStr = is_string($date) ? Carbon::parse($date)->toDateString() : $date->toDateString();

        $holiday = static::active()
            ->where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->first();

        if ($holiday && $classId && ! $holiday->appliesToClass($classId)) {
            return null;
        }

        return $holiday;
    }

    /**
     * Check if holiday applies to a specific class.
     */
    public function appliesToClass($classId = null): bool
    {
        if (empty($classId) || ! isset($this->attributes['target_classes']) || empty($this->target_classes) || $this->target_classes === 'all') {
            return true;
        }

        $targets = is_array($this->target_classes) ? $this->target_classes : json_decode($this->target_classes, true);

        return is_array($targets) ? in_array($classId, $targets) : true;
    }

    /**
     * Check if a given date is a holiday.
     */
    public static function isHoliday(Carbon|string $date, $classId = null): bool
    {
        return static::getHoliday($date, $classId) !== null;
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

    /**
     * Get names of classes targeted by this holiday.
     *
     * @return array<string>
     */
    public function getTargetClassNamesAttribute(): array
    {
        if (empty($this->target_classes)) {
            return [];
        }

        $ids = is_array($this->target_classes)
            ? $this->target_classes
            : (json_decode((string) $this->target_classes, true) ?? []);

        if (empty($ids) || ! is_array($ids)) {
            return [];
        }

        if (! Schema::hasTable('school_classes')) {
            return [];
        }

        return SchoolClass::whereIn('id', $ids)->orderBy('name')->pluck('name')->toArray() ?? [];
    }

    /**
     * Get summary text of target classes.
     */
    public function getTargetSummaryAttribute(): string
    {
        $names = $this->target_class_names;

        if (empty($names)) {
            return 'Seluruh Sekolah';
        }

        if (count($names) <= 3) {
            return 'Kelas: '.implode(', ', $names);
        }

        return count($names).' Kelas Terpilih';
    }
}
