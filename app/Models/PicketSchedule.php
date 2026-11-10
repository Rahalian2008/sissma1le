<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicketSchedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'day_number' => 'integer',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolve NIP or NUPTK of the picket officer from teacher, user, or direct name matching.
     */
    public function getNipAttribute(): ?string
    {
        if (! empty($this->attributes['nip'])) {
            return $this->attributes['nip'];
        }

        if ($this->teacher && (! empty($this->teacher->nip) || ! empty($this->teacher->nuptk))) {
            return $this->teacher->nip ?: $this->teacher->nuptk;
        }

        if ($this->user) {
            if ($this->user->teacher && (! empty($this->user->teacher->nip) || ! empty($this->user->teacher->nuptk))) {
                return $this->user->teacher->nip ?: $this->user->teacher->nuptk;
            }
            if (! empty($this->user->nip)) {
                return $this->user->nip;
            }
        }

        if (! empty($this->person_name)) {
            $name = trim($this->person_name);
            $teacher = Teacher::where('name', $name)
                ->orWhere('name', 'like', "%{$name}%")
                ->first();
            if ($teacher && (! empty($teacher->nip) || ! empty($teacher->nuptk))) {
                return $teacher->nip ?: $teacher->nuptk;
            }

            $user = User::where('name', $name)
                ->orWhere('name', 'like', "%{$name}%")
                ->first();
            if ($user) {
                if ($user->teacher && (! empty($user->teacher->nip) || ! empty($user->teacher->nuptk))) {
                    return $user->teacher->nip ?: $user->teacher->nuptk;
                }
                if (! empty($user->nip)) {
                    return $user->nip;
                }
            }
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, string $dayName)
    {
        return $query->where('day_name', $dayName);
    }

    /**
     * Map English day name or dayOfWeek to Indonesian
     */
    public static function indonesianDayName(int|string|Carbon $day): string
    {
        if ($day instanceof Carbon) {
            $num = (int) $day->format('N'); // 1 = Monday ... 7 = Sunday
        } elseif (is_numeric($day)) {
            $num = (int) $day;
        } else {
            $map = [
                'monday' => 1, 'senin' => 1,
                'tuesday' => 2, 'selasa' => 2,
                'wednesday' => 3, 'rabu' => 3,
                'thursday' => 4, 'kamis' => 4,
                'friday' => 5, 'jumat' => 5,
                'saturday' => 6, 'sabtu' => 6,
                'sunday' => 7, 'minggu' => 7,
            ];
            $num = $map[strtolower(trim($day))] ?? 1;
        }

        return match ($num) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
    }

    /**
     * Get active picket officers scheduled for a specific date (defaults to today)
     */
    public static function getTodayPickets(?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        $dayName = static::indonesianDayName($date);

        $pickets = static::active()
            ->with(['teacher', 'user.teacher'])
            ->where('day_name', $dayName)
            ->orderBy('role_type')
            ->orderBy('id')
            ->get();

        if ($pickets->isNotEmpty()) {
            return $pickets;
        }

        // Fallback: search users with role guru_piket or tu_piket
        $fallbackUsers = User::with('teacher')
            ->whereIn('role', ['guru_piket', 'tu_piket', 'petugas_piket'])
            ->take(3)
            ->get();

        if ($fallbackUsers->isNotEmpty()) {
            return $fallbackUsers->map(function ($u, $idx) use ($dayName) {
                return new static([
                    'id' => 9000 + $idx,
                    'day_name' => $dayName,
                    'day_number' => 1,
                    'user_id' => $u->id,
                    'teacher_id' => $u->teacher?->id,
                    'person_name' => $u->name,
                    'role_type' => str_contains($u->role, 'tu') ? 'TU_PIKET' : 'GURU_PIKET',
                    'location' => 'Pos Piket Gerbang Utama',
                    'start_time' => '06:30:00',
                    'end_time' => '15:30:00',
                    'is_active' => true,
                ]);
            });
        }

        return new Collection;
    }
}
