<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'period_type',
        'period_key',
        'completed_count',
        'total_habits',
        'score_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'completed_count' => 'integer',
            'total_habits' => 'integer',
            'score_percentage' => 'float',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
