<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemediationEnrichmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'teacher_id',
        'learning_objective_id',
        'type',
        'topic',
        'description',
        'remedial_method',
        'student_ids',
        'scheduled_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'student_ids' => 'array',
            'scheduled_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RemediationEnrichmentPlan $plan) {
            if (empty($plan->topic)) {
                $plan->topic = 'Program Pembelajaran';
            }
            if (empty($plan->description)) {
                $plan->description = 'Rencana pelaksanaan remedial / pengayaan';
            }
            if (empty($plan->type)) {
                $plan->type = 'remedial';
            }
            if (empty($plan->status)) {
                $plan->status = 'terjadwal';
            }
        });
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function objective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }
}
