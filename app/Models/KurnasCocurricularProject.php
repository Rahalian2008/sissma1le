<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KurnasCocurricularProject extends Model
{
    use HasFactory;

    protected $table = 'kurnas_cocurricular_projects';

    protected $fillable = [
        'academic_year_id',
        'semester',
        'grade',
        'fase',
        'theme',
        'title',
        'description',
        'target_dimensions',
        'coordinator_teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'target_dimensions' => 'array',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'coordinator_teacher_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(KurnasCocurricularScore::class, 'project_id');
    }
}
