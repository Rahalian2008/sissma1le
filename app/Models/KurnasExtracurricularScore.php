<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasExtracurricularScore extends Model
{
    use HasFactory;

    protected $table = 'kurnas_extracurricular_scores';

    protected $fillable = [
        'student_id',
        'extracurricular_id',
        'academic_year_id',
        'semester',
        'predicate',
        'description',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
