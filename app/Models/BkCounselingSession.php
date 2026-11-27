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
        'career_path',
        'study_admission_track',
        'campus_name',
        'study_program',
        'campus_choices',
        'work_field',
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
        'follow_up_date',
        'follow_up_status',
        'status',
        'is_confidential',
    ];

    protected $casts = [
        'date' => 'date',
        'follow_up_date' => 'date',
        'campus_choices' => 'array',
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

    public function getCareerSummaryAttribute(): ?string
    {
        if ($this->field_of_service !== 'KARIER' || empty($this->career_path)) {
            return null;
        }

        if ($this->career_path === 'KULIAH') {
            $parts = [];
            if ($this->study_admission_track) {
                $parts[] = str_starts_with(strtolower($this->study_admission_track), 'jalur')
                    ? $this->study_admission_track
                    : 'Jalur '.$this->study_admission_track;
            }
            if (! empty($this->campus_choices) && is_array($this->campus_choices)) {
                $choiceList = [];
                foreach ($this->campus_choices as $idx => $choice) {
                    $order = $choice['order'] ?? ($idx + 1);
                    $c = $choice['campus'] ?? '';
                    $m = $choice['major'] ?? '';
                    if ($c || $m) {
                        $choiceList[] = "Pilihan {$order}: {$c}".($m ? " ({$m})" : '');
                    }
                }
                if (! empty($choiceList)) {
                    $parts[] = implode('; ', $choiceList);
                }
            } else {
                if ($this->campus_name) {
                    $parts[] = $this->campus_name;
                }
                if ($this->study_program) {
                    $parts[] = 'Prodi '.$this->study_program;
                }
            }

            return 'Kuliah'.(! empty($parts) ? ' ('.implode(' - ', $parts).')' : '');
        }

        if ($this->career_path === 'BEKERJA') {
            return 'Bekerja'.($this->work_field ? ' ('.$this->work_field.')' : '');
        }

        return $this->career_path;
    }
}
