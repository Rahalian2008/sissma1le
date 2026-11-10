<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherReflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'teacher_id',
        'lesson_plan_id',
        'reflection_date',
        'success_notes',
        'challenges_notes',
        'follow_up_action',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reflection_date' => 'date',
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

    public function lessonPlan(): BelongsTo
    {
        return $this->belongsTo(LessonPlan::class);
    }
}
