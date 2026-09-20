<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'class_id',
        'academic_year_id',
        'rfid_uid',
        'photo',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function parents(): HasMany
    {
        return $this->hasMany(ParentGuardian::class, 'student_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function guidanceRecords(): HasMany
    {
        return $this->hasMany(GuidanceRecord::class);
    }

    public function habitDailyLogs(): HasMany
    {
        return $this->hasMany(HabitDailyLog::class);
    }

    public function habitSummaries(): HasMany
    {
        return $this->hasMany(HabitSummary::class);
    }

    // Points calculation - strictly kept separate!
    public function totalAchievementPoints(): int
    {
        return (int) $this->achievements()
            ->where('status', 'DIVERIFIKASI')
            ->sum('points');
    }

    public function totalViolationPoints(): int
    {
        return (int) $this->violations()
            ->where('status', 'DIVERIFIKASI')
            ->sum('points');
    }

    // Reward tier strictly based on Tata Tertib SMAN 1 Lengkong
    public function getRewardStatusAttribute(): array
    {
        $points = $this->totalAchievementPoints();

        if ($points >= 200) {
            return [
                'level' => 'Anugerah Waluya Utama',
                'badge' => 'Sertifikat, Hadiah & Gelar Kehormatan',
                'color' => 'amber',
                'points' => $points,
                'is_eligible' => true,
            ];
        }

        if ($points >= 176) {
            return [
                'level' => 'Siswa Berprestasi Utama',
                'badge' => 'Sertifikat dan Hadiah',
                'color' => 'emerald',
                'points' => $points,
                'is_eligible' => true,
            ];
        }

        if ($points >= 125) {
            return [
                'level' => 'Siswa Berprestasi',
                'badge' => 'Sertifikat Penghargaan',
                'color' => 'blue',
                'points' => $points,
                'is_eligible' => true,
            ];
        }

        return [
            'level' => 'Menuju Ambang Penghargaan',
            'badge' => 'Butuh '.(125 - $points).' poin lagi',
            'color' => 'slate',
            'points' => $points,
            'is_eligible' => false,
        ];
    }

    // Guidance alert tier strictly based on Tata Tertib SMAN 1 Lengkong
    public function getGuidanceStatusAttribute(): array
    {
        $points = $this->totalViolationPoints();

        if ($points > 200) {
            return [
                'level' => 'Rapat Khusus Sekolah',
                'badge' => 'NOTIFIKASI PERLU TINDAKAN (Sidang Pleno)',
                'action_needed' => true,
                'color' => 'rose',
                'stage' => 'RAPAT_KHUSUS',
                'points' => $points,
            ];
        }

        if ($points === 200) {
            return [
                'level' => 'SP 3 - BK & Kesiswaan',
                'badge' => 'NOTIFIKASI PERLU TINDAKAN (Surat Peringatan 3)',
                'action_needed' => true,
                'color' => 'red',
                'stage' => 'SP3_BK_KESISWAAN',
                'points' => $points,
            ];
        }

        if ($points >= 176) {
            return [
                'level' => 'SP 2 - BK',
                'badge' => 'NOTIFIKASI PERLU TINDAKAN (Surat Peringatan 2)',
                'action_needed' => true,
                'color' => 'orange',
                'stage' => 'SP2_BK',
                'points' => $points,
            ];
        }

        if ($points >= 125) {
            return [
                'level' => 'SP 1 - BK',
                'badge' => 'NOTIFIKASI PERLU TINDAKAN (Surat Peringatan 1)',
                'action_needed' => true,
                'color' => 'amber',
                'stage' => 'SP1_BK',
                'points' => $points,
            ];
        }

        if ($points >= 75) {
            return [
                'level' => 'Bimbingan Wali Kelas',
                'badge' => 'NOTIFIKASI PERLU TINDAKAN (Bimbingan Konseling)',
                'action_needed' => true,
                'color' => 'yellow',
                'stage' => 'BIMBINGAN_WALI_KELAS',
                'points' => $points,
            ];
        }

        return [
            'level' => 'Disiplin Baik',
            'badge' => 'Dalam batas normal',
            'action_needed' => false,
            'color' => 'emerald',
            'stage' => 'AMAN',
            'points' => $points,
        ];
    }

    public function attendancePercentage(): float
    {
        $total = $this->attendances()->whereIn('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA'])->count();
        if ($total === 0) {
            return 100.0;
        }

        $present = $this->attendances()->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();

        return round(($present / $total) * 100, 1);
    }
}
