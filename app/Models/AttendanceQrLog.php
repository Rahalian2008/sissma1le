<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceQrLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'session_id',
        'student_id',
        'scanned_at',
        'qr_token',
        'is_valid',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'is_valid' => 'boolean',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
