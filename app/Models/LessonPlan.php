<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'teacher_id',
        'title',
        'meeting_number',
        'time_allocation_minutes',
        'grade_level',
        'semester',
        'target_profile',
        'learning_model',
        'learning_methods',
        'media_and_resources',
        'learning_steps',
        'assessment_plan',
        'lkpd_content',
        'file_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'is_published',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'meeting_number' => 'integer',
            'time_allocation_minutes' => 'integer',
            'target_profile' => 'array',
            'learning_methods' => 'array',
            'learning_steps' => 'array',
            'assessment_plan' => 'array',
            'reviewed_at' => 'datetime',
            'is_published' => 'boolean',
            'version' => 'integer',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function objectives(): BelongsToMany
    {
        return $this->belongsToMany(LearningObjective::class, 'lesson_plan_tps', 'lesson_plan_id', 'learning_objective_id')->withTimestamps();
    }

    public function reflections(): HasMany
    {
        return $this->hasMany(TeacherReflection::class);
    }
}
