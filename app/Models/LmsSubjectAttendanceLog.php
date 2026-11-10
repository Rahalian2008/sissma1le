<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsSubjectAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'method',
        'verified_method',
        'selfie_path',
        'recorded_at',
        'notes',
    ];

    public function getVerifiedMethodAttribute(): ?string
    {
        return $this->attributes['method'] ?? null;
    }

    public function setVerifiedMethodAttribute(?string $value): void
    {
        $this->attributes['method'] = $value;
    }

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(LmsSubjectAttendance::class, 'attendance_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
