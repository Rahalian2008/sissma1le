<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAiRaport extends Model
{
    use HasFactory;

    protected $table = 'student_ai_raports';

    protected $guarded = ['id'];

    protected $casts = [
        'generated_at' => 'datetime',
        'attendance_rate' => 'float',
        'habit_score' => 'float',
        'worship_dzuhur_rate' => 'float',
        'worship_dhuha_rate' => 'float',
        'worship_keputrian_rate' => 'float',
        'extracurricular_rate' => 'float',
        'tahfidz_progress_pct' => 'float',
        'development_index' => 'float',
        'avg_makhraj' => 'float',
        'avg_tajwid' => 'float',
        'avg_kelancaran' => 'float',
        'is_female' => 'boolean',
        'is_ai_generated' => 'boolean',
        'rekomendasi' => 'array',
        'context_data' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
