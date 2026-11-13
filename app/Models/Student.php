<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
        'religion',
        'nik',
        'address',
        'father_name',
        'mother_name',
        'previous_school',
        'phone',
        'is_active',
        'current_class_level',
        'current_rombel',
        'status',
        'entry_path',
        'school_origin',
        'admission_date',
        'admission_class',
        'junior_diploma_number',
        'junior_exam_number',
        'diploma_number',
        'graduation_year',
        'qr_code_token',
        'notes',
        'residence_district',
        'transportation',
        'distance_to_school',
        'special_needs',
        'living_with',
        'child_order',
        'siblings_count',
        'blood_type',
        'height',
        'weight',
        'rt_rw',
        'postal_code',
        'family_card_number',
        'is_pip',
        'pip_number',
        'face_photo_path',
        'face_descriptor',
        'face_registered_at',
        'is_tu_registered',
        'tu_registered_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'admission_date' => 'date',
            'is_active' => 'boolean',
            'is_tu_registered' => 'boolean',
            'face_registered_at' => 'datetime',
            'tu_registered_at' => 'datetime',
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

    public function family(): HasOne
    {
        return $this->hasOne(StudentFamily::class, 'student_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StudentMutation::class, 'student_id');
    }

    public function scholarships(): HasMany
    {
        return $this->hasMany(StudentScholarship::class, 'student_id');
    }

    public function alumnus(): HasOne
    {
        return $this->hasOne(StudentAlumnus::class, 'student_id');
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

    public function worshipAttendances(): HasMany
    {
        return $this->hasMany(WorshipAttendance::class, 'student_id');
    }

    public function tahfidzRecords(): HasMany
    {
        return $this->hasMany(QuranTahfidzRecord::class, 'student_id');
    }

    public function cbtSessions(): HasMany
    {
        return $this->hasMany(CbtSession::class, 'student_id');
    }

    public function extracurriculars(): BelongsToMany
    {
        return $this->belongsToMany(Extracurricular::class, 'extracurricular_student')
            ->withPivot('role', 'joined_at');
    }

    public function extracurricularAttendances(): HasMany
    {
        return $this->hasMany(ExtracurricularAttendance::class, 'student_id');
    }

    public function aiRaport(): HasOne
    {
        return $this->hasOne(StudentAiRaport::class, 'student_id')->latestOfMany();
    }

    public function aiRaports(): HasMany
    {
        return $this->hasMany(StudentAiRaport::class, 'student_id');
    }

    public function counselingSessions(): HasMany
    {
        return $this->hasMany(BkCounselingSession::class, 'student_id');
    }

    public function homeVisits(): HasMany
    {
        return $this->hasMany(BkHomeVisit::class, 'student_id');
    }

    public function parentCalls(): HasMany
    {
        return $this->hasMany(BkParentCall::class, 'student_id');
    }

    public function psychologicalResults(): HasMany
    {
        return $this->hasMany(BkPsychologicalTestResult::class, 'student_id');
    }

    public function latestVakResult(): HasOne
    {
        return $this->hasOne(BkPsychologicalTestResult::class, 'student_id')
            ->whereHas('test', fn ($q) => $q->where('slug', 'vak'))
            ->latestOfMany();
    }

    public function latestRiasecResult(): HasOne
    {
        return $this->hasOne(BkPsychologicalTestResult::class, 'student_id')
            ->whereHas('test', fn ($q) => $q->where('slug', 'riasec'))
            ->latestOfMany();
    }

    public function getNormalizedGrade(): ?string
    {
        $grade = $this->schoolClass?->grade ?? null;
        if (! $grade && $this->class_id) {
            $schoolClass = SchoolClass::find($this->class_id);
            $grade = $schoolClass?->grade;
        }

        return QuranTahfidzRecord::normalizeGrade($grade) ?: 'X';
    }

    public function hasFaceRegistered(): bool
    {
        return ! empty($this->face_descriptor) || ! empty($this->face_photo_path);
    }

    public function getFacePhotoUrlAttribute(): ?string
    {
        if (! $this->face_photo_path) {
            return null;
        }

        return Storage::url($this->face_photo_path);
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTuRegistered($query)
    {
        if (Schema::hasColumn('students', 'is_tu_registered')) {
            return $query->where('is_tu_registered', true);
        }

        return $query;
    }
}
