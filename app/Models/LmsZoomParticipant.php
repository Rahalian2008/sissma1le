<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsZoomParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_meeting_id',
        'student_id',
        'joined_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(LmsZoomMeeting::class, 'zoom_meeting_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
