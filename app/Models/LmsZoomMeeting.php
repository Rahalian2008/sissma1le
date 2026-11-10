<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsZoomMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'title',
        'topic',
        'agenda',
        'meeting_url',
        'meeting_id',
        'passcode',
        'start_time',
        'end_time',
        'status',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LmsZoomParticipant::class, 'zoom_meeting_id');
    }

    public function isLiveNow(): bool
    {
        if (! $this->is_active || $this->status === 'ended') {
            return false;
        }

        if ($this->status === 'live') {
            return true;
        }

        if ($this->start_time && $this->start_time->isPast()) {
            if (! $this->end_time || $this->end_time->isFuture()) {
                return true;
            }
        }

        return false;
    }

    public function isAttendedBy(int $studentId): bool
    {
        return $this->participants()->where('student_id', $studentId)->exists();
    }
}
