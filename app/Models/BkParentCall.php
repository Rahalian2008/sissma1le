<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkParentCall extends Model
{
    use HasFactory;

    protected $table = 'bk_parent_calls';

    protected $fillable = [
        'student_id',
        'counselor_id',
        'call_code',
        'letter_number',
        'call_date',
        'call_time',
        'place',
        'reason',
        'status',
        'meeting_notes',
        'parent_agreement',
    ];

    protected $casts = [
        'call_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (BkParentCall $call) {
            if (empty($call->call_code)) {
                $call->call_code = 'BK-PC-'.date('Ymd').'-'.rand(100, 999);
            }
            if (empty($call->call_date)) {
                $call->call_date = now()->toDateString();
            }
            if (empty($call->reason)) {
                $call->reason = 'Pemanggilan Orang Tua / Wali';
            }
            if (empty($call->status)) {
                $call->status = 'SELESAI';
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
