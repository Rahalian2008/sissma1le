<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasCocurricularScore extends Model
{
    use HasFactory;

    protected $table = 'kurnas_cocurricular_scores';

    protected $fillable = [
        'project_id',
        'student_id',
        'dimension_scores',
        'process_notes',
    ];

    protected function casts(): array
    {
        return [
            'dimension_scores' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(KurnasCocurricularProject::class, 'project_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
