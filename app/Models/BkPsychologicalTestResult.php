<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkPsychologicalTestResult extends Model
{
    use HasFactory;

    protected $table = 'bk_psychological_test_results';

    protected $fillable = [
        'test_id',
        'student_id',
        'taken_at',
        'answers',
        'dimension_scores',
        'dominant_dimension',
        'secondary_dimension',
        'tertiary_dimension',
        'holland_code',
        'profile_label',
        'analysis_summary',
        'career_recommendations',
        'study_recommendations',
        'counselor_notes',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'verified_at' => 'datetime',
        'answers' => 'array',
        'dimension_scores' => 'array',
        'career_recommendations' => 'array',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(BkPsychologicalTest::class, 'test_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
