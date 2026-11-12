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

    protected static function booted(): void
    {
        static::creating(function (TeacherReflection $refl) {
            if (empty($refl->reflection_date)) {
                $refl->reflection_date = now()->toDateString();
            }
            if (empty($refl->success_notes)) {
                $refl->success_notes = '-';
            }
            if (empty($refl->challenges_notes)) {
                $refl->challenges_notes = '-';
            }
            if (empty($refl->follow_up_action)) {
                $refl->follow_up_action = '-';
            }
            if (empty($refl->status)) {
                $refl->status = 'selesai';
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
}
