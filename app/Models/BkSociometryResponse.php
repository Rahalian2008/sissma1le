<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkSociometryResponse extends Model
{
    use HasFactory;

    protected $table = 'bk_sociometry_responses';

    protected $fillable = [
        'sociometry_id',
        'student_id',
        'first_choice_id',
        'second_choice_id',
        'third_choice_id',
        'rejection_choice_id',
        'reasons',
    ];

    public function sociometry(): BelongsTo
    {
        return $this->belongsTo(BkSociometry::class, 'sociometry_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function firstChoice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'first_choice_id');
    }

    public function secondChoice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'second_choice_id');
    }

    public function thirdChoice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'third_choice_id');
    }

    public function rejectionChoice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'rejection_choice_id');
    }
}
