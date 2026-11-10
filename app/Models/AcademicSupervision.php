<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicSupervision extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_class_id',
        'supervisor_id',
        'academic_year',
        'semester',
        'supervision_date',
        'instrument_type',
        'planning_score',
        'execution_score',
        'assessment_score',
        'reflection_score',
        'final_score',
        'predicate',
        'notes',
        'recommendation',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'supervision_date' => 'date',
            'planning_score' => 'float',
            'execution_score' => 'float',
            'assessment_score' => 'float',
            'reflection_score' => 'float',
            'final_score' => 'float',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
