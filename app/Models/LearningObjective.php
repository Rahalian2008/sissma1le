<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_outcome_id',
        'subject_id',
        'teacher_id',
        'tp_code',
        'title',
        'description',
        'competency',
        'scope_material',
        'hours_allocation',
        'target_semester',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hours_allocation' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(LearningOutcome::class, 'learning_outcome_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessonPlans(): BelongsToMany
    {
        return $this->belongsToMany(LessonPlan::class, 'lesson_plan_tps', 'learning_objective_id', 'lesson_plan_id')->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LmsMaterial::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LmsAssignment::class);
    }
}
