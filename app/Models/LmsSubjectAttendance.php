<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsSubjectAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'title',
        'meeting_date',
        'start_time',
        'end_time',
        'is_open',
        'is_recurring',
        'recurring_days',
        'recurring_schedules',
        'parent_attendance_id',
        'attendance_method',
        'schedule_type',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'is_open' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_days' => 'array',
            'recurring_schedules' => 'array',
        ];
    }

    public function isAutoSchedule(): bool
    {
        return ($this->schedule_type ?? 'auto') === 'auto';
    }

    public function isManualSchedule(): bool
    {
        return ($this->schedule_type ?? 'auto') === 'manual';
    }

    public function parentAttendance(): BelongsTo
    {
        return $this->belongsTo(LmsSubjectAttendance::class, 'parent_attendance_id');
    }

    public function childrenAttendances(): HasMany
    {
        return $this->hasMany(LmsSubjectAttendance::class, 'parent_attendance_id');
    }

    /**
     * Mengambil daftar slot jadwal hari dan jam untuk sesi presensi berulang
     * Jika otomatis, mengambil langsung dari Jadwal Pembelajaran Kelas Belajar (Tugaskan Guru)
     *
     * @return array<int, array{day: string, start_time: string, end_time: string}>
     */
    public function getRecurringSchedulesList(): array
    {
        if ($this->isAutoSchedule() && $this->relationLoaded('courseClass') && $this->courseClass) {
            $classSchedules = $this->courseClass->getSchedulesList();
            if (! empty($classSchedules)) {
                return $classSchedules;
            }
        } elseif ($this->isAutoSchedule() && $this->course_class_id) {
            $cc = $this->courseClass ?? CourseClass::find($this->course_class_id);
            if ($cc) {
                $classSchedules = $cc->getSchedulesList();
                if (! empty($classSchedules)) {
                    return $classSchedules;
                }
            }
        }

        if (! empty($this->recurring_schedules) && is_array($this->recurring_schedules)) {
            return $this->recurring_schedules;
        }

        if (! empty($this->recurring_days) && is_array($this->recurring_days)) {
            return array_map(function ($d) {
                return [
                    'day' => $d,
                    'start_time' => $this->start_time ?: '07:30',
                    'end_time' => $this->end_time ?: '09:00',
                ];
            }, $this->recurring_days);
        }

        if ($this->course_class_id) {
            $cc = $this->courseClass ?? CourseClass::find($this->course_class_id);
            if ($cc) {
                return $cc->getSchedulesList();
            }
        }

        return [];
    }

    /**
     * Teks hari & jam berulang untuk ditampilkan di UI
     */
    public function getFormattedDaysAttribute(): string
    {
        $list = $this->getRecurringSchedulesList();
        if (empty($list)) {
            return $this->meeting_date ? $this->meeting_date->locale('id')->isoFormat('dddd, D MMMM Y') : '-';
        }

        $parts = [];
        foreach ($list as $s) {
            $parts[] = ($s['day'] ?? '-').' ('.($s['start_time'] ?? '-').' - '.($s['end_time'] ?? '-').' WIB)';
        }

        return implode(', ', $parts);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LmsSubjectAttendanceLog::class, 'attendance_id');
    }

    public function logFor(int $studentId): ?LmsSubjectAttendanceLog
    {
        return $this->logs()->where('student_id', $studentId)->first();
    }
}
