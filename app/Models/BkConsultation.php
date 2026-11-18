<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkConsultation extends Model
{
    use HasFactory;

    protected $table = 'bk_consultations';

    protected $fillable = [
        'consultation_code',
        'student_id',
        'user_id',
        'assigned_counselor_id',
        'topic',
        'category',
        'status',
        'is_anonymous',
        'last_message_at',
        'last_message_preview',
        'unread_counselor_count',
        'unread_student_count',
        'conclusion_notes',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'last_message_at' => 'datetime',
        'unread_counselor_count' => 'integer',
        'unread_student_count' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedCounselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BkConsultationMessage::class, 'consultation_id')->oldest();
    }

    public function latestMessages(): HasMany
    {
        return $this->hasMany(BkConsultationMessage::class, 'consultation_id')->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['OPEN', 'ACTIVE']);
    }

    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'PRIBADI' => 'Pribadi & Emosional',
            'BELAJAR' => 'Akademik & Belajar',
            'SOSIAL' => 'Sosial & Pertemanan',
            'KARIER' => 'Karier & Studi Lanjut',
            default => 'Curhat & Konseling',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'PRIBADI' => 'from-purple-500 to-indigo-500 text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800',
            'BELAJAR' => 'from-amber-500 to-orange-500 text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800',
            'SOSIAL' => 'from-pink-500 to-rose-500 text-pink-700 dark:text-pink-300 bg-pink-50 dark:bg-pink-950/40 border-pink-200 dark:border-pink-800',
            'KARIER' => 'from-teal-500 to-emerald-500 text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 border-teal-200 dark:border-teal-800',
            default => 'from-sky-500 to-blue-500 text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/40 border-sky-200 dark:border-sky-800',
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            $class = $this->student?->schoolClass?->name ? " (Kelas {$this->student->schoolClass->name})" : '';

            return "Siswa Rahasia / Anonim{$class}";
        }

        return $this->student?->name ?? 'Peserta Didik';
    }

    public function markAsReadFor(User $user, bool $isCounselor): void
    {
        if ($isCounselor) {
            $this->messages()
                ->where('sender_role', 'STUDENT')
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            $this->update(['unread_counselor_count' => 0]);
        } else {
            $this->messages()
                ->where('sender_role', 'COUNSELOR')
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            $this->update(['unread_student_count' => 0]);
        }
    }
}
