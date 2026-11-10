<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsAssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'submission_text',
        'file_path',
        'file_name',
        'submitted_at',
        'is_late',
        'score',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    public function getStatusAttribute(): string
    {
        return ($this->score !== null || $this->graded_at !== null) ? 'graded' : 'submitted';
    }

    public function scopePendingGrading($query)
    {
        return $query->whereNull('graded_at');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    public function getSubmissionTextAttribute(): ?string
    {
        return $this->attributes['content'] ?? null;
    }

    public function setSubmissionTextAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'is_late' => 'boolean',
            'score' => 'float',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(LmsAssignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function isGraded(): bool
    {
        return ! is_null($this->score);
    }
}
