<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'learning_objective_id',
        'grade_level',
        'topic',
        'question_text',
        'stimulus',
        'question_type',
        'cognitive_level',
        'indicator',
        'options',
        'correct_answer',
        'explanation',
        'score_weight',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_active' => 'boolean',
            'score_weight' => 'integer',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->question_type) {
            'essay', 'uraian' => 'Uraian / Esai',
            default => 'Pilihan Ganda',
        };
    }

    public function getCognitiveLabelAttribute(): string
    {
        return match ($this->cognitive_level) {
            'L1' => 'L1 (Pemahaman / C1-C2)',
            'L3' => 'L3 (Penalaran / HOTS / C4-C6)',
            default => 'L2 (Aplikasi / C3)',
        };
    }
}
