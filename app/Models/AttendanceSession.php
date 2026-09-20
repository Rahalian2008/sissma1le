<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_or_activity',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
        'qr_code_token',
        'qr_expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'qr_expires_at' => 'datetime',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function qrLogs(): HasMany
    {
        return $this->hasMany(AttendanceQrLog::class, 'session_id');
    }

    public function isQrValid(): bool
    {
        if ($this->status !== 'ACTIVE') {
            return false;
        }

        if ($this->qr_expires_at && now()->isAfter($this->qr_expires_at)) {
            return false;
        }

        return true;
    }

    public function regenerateQrToken(int $validMinutes = 15): void
    {
        $this->update([
            'qr_code_token' => Str::random(40),
            'qr_expires_at' => now()->addMinutes($validMinutes),
            'status' => 'ACTIVE',
        ]);
    }
}
