<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'name',
        'tagline',
        'description',
        'target_time',
        'start_time',
        'end_time',
        'default_activity',
        'reflection_prompt',
        'is_time_restricted',
        'icon',
    ];

    protected $casts = [
        'is_time_restricted' => 'boolean',
    ];

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(HabitDailyLog::class);
    }

    /**
     * Memeriksa apakah waktu saat ini berada dalam rentang waktu pelaksanaan kebiasaan
     */
    public function isWithinTimeWindow(?Carbon $time = null): bool
    {
        if (! $this->is_time_restricted || empty($this->start_time) || empty($this->end_time)) {
            return true;
        }

        $time = $time ?? Carbon::now('Asia/Jakarta');
        $current = $time->format('H:i');
        $start = substr($this->start_time, 0, 5);
        $end = substr($this->end_time, 0, 5);

        if ($start <= $end) {
            return $current >= $start && $current <= $end;
        }

        // Overnight range (e.g. 21:30 - 04:00)
        return $current >= $start || $current <= $end;
    }

    /**
     * Mendapatkan deskripsi status waktu pelaksanaan saat ini
     *
     * @return array{is_active: bool, state: string, message: string, range: string}
     */
    public function getTimeWindowStatus(?Carbon $time = null): array
    {
        $range = ($this->start_time && $this->end_time)
            ? substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5).' WIB'
            : ($this->target_time ?? 'Bebas');

        if (! $this->is_time_restricted || empty($this->start_time) || empty($this->end_time)) {
            return [
                'is_active' => true,
                'state' => 'open',
                'message' => 'Dapat dilaksanakan kapan saja',
                'range' => $range,
            ];
        }

        $time = $time ?? Carbon::now('Asia/Jakarta');
        $current = $time->format('H:i');
        $start = substr($this->start_time, 0, 5);
        $end = substr($this->end_time, 0, 5);

        $isActive = $this->isWithinTimeWindow($time);

        if ($isActive) {
            return [
                'is_active' => true,
                'state' => 'active',
                'message' => "Jadwal sedang aktif ({$range})",
                'range' => $range,
            ];
        }

        $state = ($start <= $end && $current < $start) ? 'early' : 'expired';
        $msg = $state === 'early'
            ? "Belum masuk jadwal (Aktif pukul {$range})"
            : "Waktu pelaksanaan telah lewat ({$range})";

        return [
            'is_active' => false,
            'state' => $state,
            'message' => $msg,
            'range' => $range,
        ];
    }
}
