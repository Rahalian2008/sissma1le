<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkPsychologicalTestQuestion extends Model
{
    use HasFactory;

    protected $table = 'bk_psychological_test_questions';

    protected $fillable = [
        'test_id',
        'question_number',
        'question_text',
        'dimension',
        'dimension_name',
        'scale_type',
        'options',
    ];

    protected $casts = [
        'question_number' => 'integer',
        'options' => 'array',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(BkPsychologicalTest::class, 'test_id');
    }
}
