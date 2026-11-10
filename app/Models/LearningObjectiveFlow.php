<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningObjectiveFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'academic_year_id',
        'teacher_id',
        'title',
        'phase',
        'grade_level',
        'flow_order',
        'rationale',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'flow_order' => 'array',
            'is_approved' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
