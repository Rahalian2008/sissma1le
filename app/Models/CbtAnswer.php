<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_session_id',
        'cbt_exam_question_id',
        'answer',
        'is_doubtful',
        'is_correct',
        'score_earned',
        'teacher_feedback',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answer' => 'array',
            'is_doubtful' => 'boolean',
            'is_correct' => 'boolean',
            'score_earned' => 'decimal:2',
            'answered_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CbtSession::class, 'cbt_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(CbtExamQuestion::class, 'cbt_exam_question_id');
    }
}
