<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'class_id',
        'academic_year_id',
        'day_of_week',
        'start_time',
        'end_time',
        'schedules',
        'weight_material',
        'weight_assignment',
        'weight_quiz',
        'weight_attendance',
        'description',
        'kktp',
        'is_active',
        'is_security_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_security_enabled' => 'boolean',
            'kktp' => 'integer',
            'schedules' => 'array',
            'weight_material' => 'float',
            'weight_assignment' => 'float',
            'weight_quiz' => 'float',
            'weight_attendance' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (CourseClass $courseClass) {
            if (! Schema::hasColumn('course_classes', 'is_security_enabled')) {
                Schema::table('course_classes', function (Blueprint $table) {
                    $table->boolean('is_security_enabled')->default(true)->after('is_active');
                });
            }
            if (! Schema::hasColumn('course_classes', 'kktp')) {
                Schema::table('course_classes', function (Blueprint $table) {
                    $table->unsignedInteger('kktp')->nullable()->after('description');
                });
            }
        });
    }

    /**
     * Cek apakah proteksi keamanan anti-curang (kunci tab & blokir otomatis) aktif pada kelas ini
     */
    public function isSecurityEnabled(): bool
    {
        if (! array_key_exists('is_security_enabled', $this->attributes)) {
            return true;
        }

        return $this->attributes['is_security_enabled'] !== false && $this->attributes['is_security_enabled'] !== 0 && $this->attributes['is_security_enabled'] !== '0';
    }

    /**
     * Dapatkan nilai Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) efektif
     */
    public function getEffectiveKktp(): int
    {
        return (int) ($this->kktp ?? $this->subject?->kkm ?? 75);
    }

    public function getWeightMaterial(): float
    {
        return (float) ($this->weight_material ?? 20.0);
    }

    public function getWeightAssignment(): float
    {
        return (float) ($this->weight_assignment ?? 30.0);
    }

    public function getWeightQuiz(): float
    {
        return (float) ($this->weight_quiz ?? 30.0);
    }

    public function getWeightAttendance(): float
    {
        return (float) ($this->weight_attendance ?? 20.0);
    }

    /**
     * Mengambil daftar slot jadwal hari dan jam untuk kelas mapel ini.
     *
     * @return array<int, array{day: string, start_time: string, end_time: string}>
     */
    public function getSchedulesList(): array
    {
        if (! empty($this->schedules) && is_array($this->schedules)) {
            return $this->schedules;
        }

        $days = array_filter(array_map('trim', explode(',', (string) ($this->day_of_week ?? ''))));
        if (empty($days)) {
            return [
                [
                    'day' => 'Senin',
                    'start_time' => $this->start_time ?: '07:30',
                    'end_time' => $this->end_time ?: '09:00',
                ],
            ];
        }

        return array_values(array_map(function ($d) {
            return [
                'day' => $d,
                'start_time' => $this->start_time ?: '07:30',
                'end_time' => $this->end_time ?: '09:00',
            ];
        }, $days));
    }

    /**
     * Format string jadwal untuk tampilan UI
     */
    public function getScheduleTextAttribute(): string
    {
        $list = $this->getSchedulesList();
        if (empty($list)) {
            return ($this->day_of_week ?: 'Senin').' ('.($this->start_time ?: '07:30').' - '.($this->end_time ?: '09:00').' WIB)';
        }

        $parts = [];
        foreach ($list as $s) {
            $day = $s['day'] ?? '-';
            $start = $s['start_time'] ?? '-';
            $end = $s['end_time'] ?? '-';
            $parts[] = "{$day} ({$start} - {$end} WIB)";
        }

        return implode(', ', $parts);
    }

    /**
     * Periksa apakah mata pelajaran ini sedang aktif dan boleh dikerjakan oleh siswa
     * berdasarkan hari dan jam saat ini (WIB).
     */
    public function isCurrentlyActiveForStudent(): bool
    {
        $now = Carbon::now('Asia/Jakarta');
        $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $currentDay = $indonesianDays[$now->dayOfWeek];
        $currentTime = $now->format('H:i');

        $schedules = $this->getSchedulesList();
        if (empty($schedules)) {
            return false;
        }

        foreach ($schedules as $slot) {
            $day = trim($slot['day'] ?? '');
            $start = trim($slot['start_time'] ?? '');
            $end = trim($slot['end_time'] ?? '');

            if (empty($day) || empty($start) || empty($end)) {
                continue;
            }

            // Samakan pengecekan hari (case-insensitive)
            if (strcasecmp($day, $currentDay) === 0) {
                // Periksa apakah waktu sekarang berada dalam rentang jam mulai - jam selesai
                if ($currentTime >= $start && $currentTime <= $end) {
                    return true;
                }
            }
        }

        return false;
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LmsMaterial::class)->orderByDesc('id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LmsAssignment::class)->orderByDesc('due_date');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(LmsQuiz::class)->orderByDesc('id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LmsSubjectAttendance::class)->orderByDesc('meeting_date');
    }

    public function securityBlocks(): HasMany
    {
        return $this->hasMany(LmsStudentSecurityBlock::class)->orderByDesc('blocked_at');
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(LmsDiscussion::class)->whereNull('parent_id')->orderByDesc('is_pinned')->orderByDesc('created_at');
    }

    public function zoomMeetings(): HasMany
    {
        return $this->hasMany(LmsZoomMeeting::class)->orderByDesc('start_time');
    }

    /**
     * Hitung progres penyelesaian materi & tugas untuk siswa tertentu
     *
     * @return array{materials_count: int, materials_read: int, assignments_count: int, assignments_submitted: int, percentage: float}
     */
    public function progressForStudent(int $studentId): array
    {
        $materialsCount = $this->materials()->where('is_published', true)->count();
        $materialsRead = LmsMaterialRead::whereIn('material_id', $this->materials()->where('is_published', true)->pluck('id'))
            ->where('student_id', $studentId)
            ->count();

        $assignmentsCount = $this->assignments()->where('is_published', true)->count();
        $assignmentsSubmitted = LmsAssignmentSubmission::whereIn('assignment_id', $this->assignments()->where('is_published', true)->pluck('id'))
            ->where('student_id', $studentId)
            ->count();

        $totalItems = $materialsCount + $assignmentsCount;
        $completedItems = $materialsRead + $assignmentsSubmitted;

        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0;

        return [
            'materials_count' => $materialsCount,
            'materials_read' => $materialsRead,
            'assignments_count' => $assignmentsCount,
            'assignments_submitted' => $assignmentsSubmitted,
            'percentage' => $percentage,
        ];
    }
}
