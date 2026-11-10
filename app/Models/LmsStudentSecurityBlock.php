<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsStudentSecurityBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'student_id',
        'blockable_type',
        'blockable_id',
        'violation_type',
        'reason',
        'is_blocked',
        'blocked_at',
        'unblocked_at',
        'unblocked_by',
    ];

    protected function casts(): array
    {
        return [
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
            'unblocked_at' => 'datetime',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function unblockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unblocked_by');
    }

    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->blockable_type) {
            'material' => 'Materi Pelajaran',
            'assignment' => 'Tugas & PR',
            'quiz' => 'Asesmen & CBT',
            default => ucfirst($this->blockable_type),
        };
    }

    public function getViolationLabelAttribute(): string
    {
        return match ($this->violation_type) {
            'minimize' => 'Meminimalkan Jendela Browser',
            'tab_switch' => 'Membuka Tab Baru / Pindah Tab Browser',
            'window_blur' => 'Beralih ke Aplikasi Lain',
            'app_close' => 'Menutup / Keluar dari Aplikasi Saat Belajar',
            default => 'Pelanggaran Integritas Aplikasi',
        };
    }

    public function getItemTitleAttribute(): string
    {
        if ($this->blockable_type === 'material') {
            return LmsMaterial::find($this->blockable_id)?->title ?? 'Materi Pelajaran';
        }
        if ($this->blockable_type === 'assignment') {
            return LmsAssignment::find($this->blockable_id)?->title ?? 'Tugas & PR';
        }
        if ($this->blockable_type === 'quiz') {
            return LmsQuiz::find($this->blockable_id)?->title ?? 'Asesmen & CBT';
        }

        return 'Item Pembelajaran';
    }

    /**
     * Cek apakah siswa sedang berstatus diblokir pada materi / tugas / asesmen tertentu
     */
    public static function isStudentBlocked(int $studentId, string $type, int $id): bool
    {
        return static::where('student_id', $studentId)
            ->where('blockable_type', $type)
            ->where('blockable_id', $id)
            ->where('is_blocked', true)
            ->exists();
    }

    /**
     * Dapatkan data blokir aktif siswa pada materi / tugas / asesmen tertentu
     */
    public static function getActiveBlock(int $studentId, string $type, int $id): ?self
    {
        return static::where('student_id', $studentId)
            ->where('blockable_type', $type)
            ->where('blockable_id', $id)
            ->where('is_blocked', true)
            ->latest('blocked_at')
            ->first();
    }

    /**
     * Catat pelanggaran keamanan dan aktifkan blokir untuk siswa
     */
    public static function recordBlock(
        CourseClass|int $courseClass,
        Student|int $student,
        string $type,
        int $id,
        string $violationType = 'tab_switch',
        ?string $reason = null
    ): self {
        $courseClassId = $courseClass instanceof CourseClass ? $courseClass->id : (int) $courseClass;
        $studentId = $student instanceof Student ? $student->id : (int) $student;

        $defaultReason = match ($violationType) {
            'minimize' => 'Terdeteksi meminimalkan jendela aplikasi browser saat sedang mengakses pembelajaran.',
            'tab_switch' => 'Terdeteksi membuka tab baru atau berpindah tab browser saat sedang mengakses pembelajaran.',
            'window_blur' => 'Terdeteksi beralih fokus ke jendela aplikasi lain di komputer/HP.',
            default => 'Terdeteksi meninggalkan halaman atau menutup aplikasi pembelajaran.',
        };

        return static::create([
            'course_class_id' => $courseClassId,
            'student_id' => $studentId,
            'blockable_type' => $type,
            'blockable_id' => $id,
            'violation_type' => $violationType,
            'reason' => $reason ?: $defaultReason,
            'is_blocked' => true,
            'blocked_at' => now(),
        ]);
    }
}
