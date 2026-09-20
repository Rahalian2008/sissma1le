<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'rfid_uid',
        'device_id',
        'student_id',
        'scanned_at',
        'is_valid',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'is_valid' => 'boolean',
            'raw_payload' => 'array',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(AttendanceDevice::class, 'device_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
