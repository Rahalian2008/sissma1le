<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Extracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'teacher_id',
        'schedule_day',
        'start_time',
        'end_time',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Pembina Utama Ekstrakurikuler
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Semua Guru Pembina (melalui pivot table extracurricular_teacher)
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'extracurricular_teacher')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Siswa Anggota Ekstrakurikuler
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'extracurricular_student')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Log Presensi Ekstrakurikuler
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(ExtracurricularAttendance::class);
    }

    /**
     * Daftar Semua Pembina (Penggabungan Pembina Utama & Pivot)
     */
    public function getAllTeachersAttribute()
    {
        $all = $this->teachers;
        if ($this->teacher && ! $all->contains('id', $this->teacher->id)) {
            $all->prepend($this->teacher);
        }

        return $all;
    }

    /**
     * Nama-nama Pembina sebagai teks
     */
    public function getTeacherNamesAttribute(): string
    {
        $names = $this->all_teachers->pluck('name')->filter()->unique();

        return $names->isNotEmpty() ? $names->implode(', ') : ($this->teacher?->name ?? 'Belum Ditentukan');
    }

    /**
     * Apakah ekstrakurikuler ini memiliki konfigurasi jadwal khusus
     */
    public function hasCustomSchedule(): bool
    {
        return ! empty($this->schedule_day) && ! empty($this->start_time) && ! empty($this->end_time);
    }

    /**
     * Hari aktif operasional (array lowercase)
     */
    public function getEffectiveDaysAttribute(): array
    {
        if ($this->hasCustomSchedule()) {
            return WorshipAttendance::parseDays($this->schedule_day, ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
        }

        return WorshipAttendance::parseDays(SchoolSetting::get('extra_attendance_days', 'senin,selasa,rabu,kamis,jumat,sabtu'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
    }

    /**
     * Label hari operasional terformat untuk tampilan UI
     */
    public function getEffectiveDaysLabelAttribute(): string
    {
        return WorshipAttendance::formatDaysLabel($this->effective_days);
    }

    /**
     * Jam mulai operasional efektif (H:i)
     */
    public function getEffectiveStartTimeAttribute(): string
    {
        if ($this->hasCustomSchedule() && ! empty($this->start_time)) {
            return substr($this->start_time, 0, 5);
        }

        return substr(SchoolSetting::get('extra_attendance_start', '14:00'), 0, 5);
    }

    /**
     * Jam selesai operasional efektif (H:i)
     */
    public function getEffectiveEndTimeAttribute(): string
    {
        if ($this->hasCustomSchedule() && ! empty($this->end_time)) {
            return substr($this->end_time, 0, 5);
        }

        return substr(SchoolSetting::get('extra_attendance_end', '17:30'), 0, 5);
    }

    /**
     * Cek apakah saat ini masuk jadwal sesi presensi (Time-Lock)
     */
    public function validateTimeLock(?Carbon $currentTime = null): array
    {
        $now = $currentTime ?? Carbon::now('Asia/Jakarta');
        $currentDayName = $now->locale('id')->isoFormat('dddd');
        $currentTimeStr = $now->format('H:i');

        $hasCustom = $this->hasCustomSchedule();
        $startTimeStr = $this->effective_start_time;
        $endTimeStr = $this->effective_end_time;
        $effectiveDays = $this->effective_days;
        $scheduleTypeLabel = $hasCustom ? "Jadwal Khusus {$this->name}" : 'Jadwal Standar Master Ekstrakurikuler';

        $isCorrectDay = in_array(strtolower($currentDayName), $effectiveDays, true);
        $isWithinTime = ($currentTimeStr >= $startTimeStr && $currentTimeStr <= $endTimeStr);

        if (! $isCorrectDay) {
            $daysLabel = WorshipAttendance::formatDaysLabel($effectiveDays);

            return [
                'allowed' => false,
                'status' => 'WRONG_DAY',
                'reason' => "Hari ini ({$currentDayName}) bukan hari operasional kegiatan {$this->name} [{$scheduleTypeLabel}: {$daysLabel}].",
                'current_day_name' => $currentDayName,
                'current_time' => $currentTimeStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'schedule_type' => $hasCustom ? 'custom' : 'default',
            ];
        }

        if (! $isWithinTime) {
            return [
                'allowed' => false,
                'status' => 'WRONG_TIME',
                'reason' => "Sesi presensi {$this->name} hanya dibuka pada jam operasional kegiatan [{$startTimeStr} - {$endTimeStr} WIB ({$scheduleTypeLabel})]. Saat ini pukul {$currentTimeStr} WIB.",
                'current_day_name' => $currentDayName,
                'current_time' => $currentTimeStr,
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'schedule_type' => $hasCustom ? 'custom' : 'default',
            ];
        }

        return [
            'allowed' => true,
            'status' => 'OPEN',
            'reason' => "Sesi presensi {$this->name} aktif berlangsung sesuai jam operasional kegiatan [{$startTimeStr} - {$endTimeStr} WIB ({$scheduleTypeLabel})].",
            'current_day_name' => $currentDayName,
            'current_time' => $currentTimeStr,
            'start_time' => $startTimeStr,
            'end_time' => $endTimeStr,
            'schedule_type' => $hasCustom ? 'custom' : 'default',
        ];
    }
}
