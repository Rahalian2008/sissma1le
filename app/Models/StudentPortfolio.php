<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_class_id',
        'learning_objective_id',
        'title',
        'description',
        'category',
        'file_path',
        'external_url',
        'artifact_date',
        'is_featured',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'artifact_date' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function objective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
