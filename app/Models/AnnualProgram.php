<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'teacher_id',
        'academic_year_id',
        'type',
        'semester',
        'distribution_data',
        'total_hours',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'distribution_data' => 'array',
            'total_hours' => 'integer',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
