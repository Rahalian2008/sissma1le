<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CbtSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'student_id',
        'session_token',
        'cbt_username',
        'cbt_password',
        'room_name',
        'is_allowed',
        'started_at',
        'finished_at',
        'remaining_seconds',
        'status',
        'violation_count',
        'is_blocked',
        'blocked_reason',
        'blocked_at',
        'unblocked_at',
        'unblocked_by',
        'student_latitude',
        'student_longitude',
        'student_distance_meters',
        'student_ip',
        'student_user_agent',
        'total_score',
        'max_possible_score',
        'final_grade',
        'is_graded',
        'graded_at',
        'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'is_allowed' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'blocked_at' => 'datetime',
            'unblocked_at' => 'datetime',
            'graded_at' => 'datetime',
            'is_blocked' => 'boolean',
            'is_graded' => 'boolean',
            'violation_count' => 'integer',
            'remaining_seconds' => 'integer',
            'total_score' => 'decimal:2',
            'max_possible_score' => 'decimal:2',
            'final_grade' => 'decimal:2',
            'student_distance_meters' => 'decimal:2',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function unblocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unblocked_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CbtAnswer::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(CbtViolation::class)->latest('violation_time');
    }

    public function isOngoing(): bool
    {
        return $this->status === 'in_progress' && ! $this->is_blocked;
    }

    public function isPassed(): bool
    {
        $kkm = $this->exam?->pass_percentage ?? 75.00;

        return (float) $this->final_grade >= (float) $kkm;
    }

    /**
     * Ensure CBT credentials exist for participant exam card.
     */
    public function ensureCredentials(): void
    {
        if (empty($this->cbt_username)) {
            $student = $this->student;
            $nis = $student?->nis ?? $student?->nisn ?? sprintf('%05d', $this->student_id);
            $this->cbt_username = 'CBT-'.$nis;
        }

        if (empty($this->cbt_password)) {
            $this->cbt_password = strtoupper(Str::random(6));
        }

        $this->save();
    }
}
