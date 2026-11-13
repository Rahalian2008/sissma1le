<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'question_bank_id',
        'question_number',
        'question_type',
        'stimulus',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'score_weight',
        'learning_objective',
        'indicator',
        'cognitive_level',
        'difficulty_level',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'score_weight' => 'decimal:2',
            'question_number' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CbtAnswer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->question_type) {
            'complex_multiple_choice' => 'Pilihan Ganda Kompleks',
            'true_false' => 'Benar / Salah',
            'matching' => 'Menjodohkan',
            'short_answer' => 'Isian Singkat',
            'essay' => 'Uraian / Esai',
            default => 'Pilihan Ganda',
        };
    }

    public function getCognitiveLabelAttribute(): string
    {
        return match ($this->cognitive_level) {
            'L1' => 'L1 (Pemahaman / C1-C2)',
            'L3' => 'L3 (Penalaran / HOTS)',
            default => 'L2 (Aplikasi / C3)',
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match (strtolower($this->difficulty_level ?? 'sedang')) {
            'mudah' => 'Mudah',
            'sukar', 'sulit' => 'Sukar',
            default => 'Sedang',
        };
    }
}
