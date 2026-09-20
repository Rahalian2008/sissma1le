<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'nis',
        'nisn',
        'student_name',
        'class_id',
        'date',
        'time',
        'time_out',
        'method',
        'out_method',
        'selfie_path',
        'out_selfie_path',
        'latitude',
        'longitude',
        'out_latitude',
        'out_longitude',
        'gps_accuracy',
        'device_info',
        'out_device_info',
        'ip_address',
        'status',
        'is_early_leave',
        'early_leave_time',
        'early_leave_reason',
        'verification_status',
        'notes',
        'session_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_early_leave' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'out_latitude' => 'float',
            'out_longitude' => 'float',
            'gps_accuracy' => 'float',
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function selfie(): HasOne
    {
        return $this->hasOne(AttendanceSelfie::class);
    }

    public function qrLog(): HasOne
    {
        return $this->hasOne(AttendanceQrLog::class);
    }

    public function rfidLog(): HasOne
    {
        return $this->hasOne(AttendanceRfidLog::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isFlaggedForVerification(): bool
    {
        return $this->verification_status === 'PERLU_VERIFIKASI';
    }

    public function isLeaveRequest(): bool
    {
        return $this->method === 'IZIN_SAKIT' || in_array($this->status, ['IZIN', 'SAKIT']);
    }

    public function isManual(): bool
    {
        return $this->method === 'MANUAL';
    }
}
