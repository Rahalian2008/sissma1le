<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'exam_code',
        'exam_type',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'duration_minutes',
        'token',
        'start_time',
        'end_time',
        'randomize_questions',
        'randomize_options',
        'show_result_to_student',
        'enable_security_block',
        'max_violations',
        'enable_geofence',
        'geofence_latitude',
        'geofence_longitude',
        'geofence_radius',
        'pass_percentage',
        'status',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_result_to_student' => 'boolean',
            'enable_security_block' => 'boolean',
            'max_violations' => 'integer',
            'enable_geofence' => 'boolean',
            'geofence_latitude' => 'decimal:8',
            'geofence_longitude' => 'decimal:8',
            'geofence_radius' => 'integer',
            'pass_percentage' => 'decimal:2',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examClasses(): HasMany
    {
        return $this->hasMany(CbtExamClass::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'cbt_exam_classes', 'cbt_exam_id', 'school_class_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CbtExamQuestion::class)->orderBy('question_number');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CbtSession::class);
    }

    public function isActiveNow(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        $now = now();
        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->exam_type) {
            'UH' => 'Ulangan Harian',
            'PTS' => 'Penilaian Tengah Semester',
            'PAS' => 'Penilaian Akhir Semester',
            'PAT' => 'Penilaian Akhir Tahun',
            'US' => 'Ujian Sekolah',
            'ANBK' => 'Simulasi Asesmen Nasional',
            'DIAGNOSTIK' => 'Asesmen Diagnostik',
            default => $this->exam_type ?? 'Ujian CBT',
        };
    }
}
