<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasCourseScore extends Model
{
    use HasFactory;

    protected $table = 'kurnas_course_scores';

    protected $fillable = [
        'course_class_id',
        'student_id',
        'academic_year_id',
        'semester',
        'score',
        'optimal_tp_ids',
        'improvement_tp_ids',
        'optimal_description',
        'improvement_description',
        'full_description',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'optimal_tp_ids' => 'array',
            'improvement_tp_ids' => 'array',
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

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
