<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'learning_objective_id',
        'title',
        'quiz_type',
        'topic',
        'description',
        'instructions',
        'duration_minutes',
        'start_time',
        'end_time',
        'passing_score',
        'is_randomized',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'passing_score' => 'integer',
            'is_randomized' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LmsQuizQuestion::class, 'quiz_id')->orderBy('order_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LmsQuizAttempt::class, 'quiz_id')->orderByDesc('id');
    }

    public function attemptFor(int $studentId): ?LmsQuizAttempt
    {
        return $this->attempts()->where('student_id', $studentId)->first();
    }

    public function totalScoreWeight(): int
    {
        return (int) $this->questions()->sum('score_weight');
    }
}
