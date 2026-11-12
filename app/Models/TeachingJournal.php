<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'teacher_id',
        'lesson_plan_id',
        'learning_objective_id',
        'subject_attendance_id',
        'date',
        'meeting_number',
        'time_slot',
        'topic',
        'teaching_activities',
        'student_attendance_summary',
        'learning_achievement_status',
        'classroom_dynamics_notes',
        'follow_up_action',
        'is_verified_by_kurikulum',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'student_attendance_summary' => 'array',
            'is_verified_by_kurikulum' => 'boolean',
            'verified_at' => 'datetime',
            'meeting_number' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TeachingJournal $journal) {
            if (empty($journal->date)) {
                $journal->date = now()->toDateString();
            }
            if (empty($journal->topic)) {
                $journal->topic = 'Kegiatan Pembelajaran';
            }
            if (empty($journal->teaching_activities)) {
                $journal->teaching_activities = 'Pelaksanaan kegiatan belajar mengajar sesuai kurikulum.';
            }
            if ($journal->meeting_number === null) {
                $journal->meeting_number = 1;
            }
        });
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessonPlan(): BelongsTo
    {
        return $this->belongsTo(LessonPlan::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class);
    }

    public function subjectAttendance(): BelongsTo
    {
        return $this->belongsTo(LmsSubjectAttendance::class, 'subject_attendance_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Teks ringkasan kehadiran siswa (contoh: Hadir: 32, Sakit: 1, Izin: 1, Alfa: 0)
     */
    public function getAttendanceSummaryTextAttribute(): string
    {
        $summary = $this->student_attendance_summary;
        if (! is_array($summary) || empty($summary)) {
            return 'Lengkap';
        }

        $parts = [];
        if (isset($summary['hadir'])) {
            $parts[] = "Hadir: {$summary['hadir']}";
        }
        if (! empty($summary['sakit'])) {
            $parts[] = "S: {$summary['sakit']}";
        }
        if (! empty($summary['izin'])) {
            $parts[] = "I: {$summary['izin']}";
        }
        if (! empty($summary['alfa'])) {
            $parts[] = "A: {$summary['alfa']}";
        }

        return implode(', ', $parts);
    }
}
