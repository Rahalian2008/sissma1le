<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasTranscript extends Model
{
    use HasFactory;

    protected $table = 'kurnas_transcripts';

    protected $fillable = [
        'student_id',
        'diploma_number',
        'transcript_number',
        'graduation_date',
        'subject_final_scores',
        'school_exam_scores',
        'semester_1_6_scores',
        'average_score',
        'is_eligible_snbp',
        'cohort_rank',
        'status_graduation',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'graduation_date' => 'date',
            'subject_final_scores' => 'array',
            'school_exam_scores' => 'array',
            'is_eligible_snbp' => 'boolean',
            'cohort_rank' => 'integer',
            'average_score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
