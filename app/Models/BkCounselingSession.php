<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkCounselingSession extends Model
{
    use HasFactory;

    protected $table = 'bk_counseling_sessions';

    protected $fillable = [
        'student_id',
        'counselor_id',
        'academic_year_id',
        'session_code',
        'session_type',
        'field_of_service',
        'service_category',
        'date',
        'start_time',
        'end_time',
        'place',
        'topic',
        'problem_description',
        'counseling_approach',
        'agreement_result',
        'follow_up_plan',
        'status',
        'is_confidential',
    ];

    protected $casts = [
        'date' => 'date',
        'is_confidential' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (BkCounselingSession $session) {
            if (empty($session->session_code)) {
                $session->session_code = 'BK-CS-'.date('Ymd').'-'.rand(100, 999);
            }
            if (empty($session->date)) {
                $session->date = now()->toDateString();
            }
            if (empty($session->topic)) {
                $session->topic = 'Konseling Individual';
            }
            if (empty($session->problem_description)) {
                $session->problem_description = 'Deskripsi permasalahan konseling siswa';
            }
            if (empty($session->status)) {
                $session->status = 'SELESAI';
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

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getFieldBadgeAttribute(): array
    {
        return match ($this->field_of_service) {
            'PRIBADI' => ['label' => 'Bimbingan Pribadi', 'class' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20'],
            'SOSIAL' => ['label' => 'Bimbingan Sosial', 'class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'],
            'BELAJAR' => ['label' => 'Bimbingan Belajar', 'class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20'],
            'KARIER' => ['label' => 'Bimbingan Karier', 'class' => 'bg-sky-500/10 text-sky-400 border border-sky-500/20'],
            default => ['label' => $this->field_of_service, 'class' => 'bg-slate-500/10 text-slate-400 border border-slate-500/20'],
        };
    }
}
