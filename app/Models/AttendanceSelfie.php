<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSelfie extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'student_id',
        'image_path',
        'face_detected',
        'liveness_score',
        'client_metadata',
    ];

    protected function casts(): array
    {
        return [
            'face_detected' => 'boolean',
            'liveness_score' => 'float',
            'client_metadata' => 'array',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
