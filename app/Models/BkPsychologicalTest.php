<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkPsychologicalTest extends Model
{
    use HasFactory;

    protected $table = 'bk_psychological_tests';

    protected $fillable = [
        'slug',
        'name',
        'category',
        'short_desc',
        'instructions',
        'duration_minutes',
        'total_questions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(BkPsychologicalTestQuestion::class, 'test_id')->orderBy('question_number');
    }

    public function results(): HasMany
    {
        return $this->hasMany(BkPsychologicalTestResult::class, 'test_id');
    }
}
