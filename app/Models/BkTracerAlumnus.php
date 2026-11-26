<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkTracerAlumnus extends Model
{
    use HasFactory;

    protected $table = 'bk_tracer_alumni';

    protected $fillable = [
        'student_id',
        'full_name',
        'nisn',
        'nis',
        'gender',
        'graduation_year',
        'last_class',
        'phone',
        'email',
        'current_address',
        'current_city',
        'activity_status',
        'college_level',
        'campus_name',
        'study_program',
        'admission_track',
        'major_alignment',
        'company_name',
        'work_field',
        'job_position',
        'monthly_income_range',
        'job_waiting_time',
        'business_name',
        'business_field',
        'business_revenue_range',
        'employee_count',
        'bk_service_rating',
        'bk_career_guidance_impact',
        'school_feedback',
        'is_verified',
        'ip_address',
    ];

    protected $casts = [
        'bk_service_rating' => 'integer',
        'employee_count' => 'integer',
        'is_verified' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getActivityStatusBadgeAttribute(): array
    {
        return match ($this->activity_status) {
            'KULIAH' => ['label' => 'Kuliah (Perguruan Tinggi)', 'color' => 'bg-sky-500/10 text-sky-400 border border-sky-500/30', 'icon' => '🎓'],
            'BEKERJA' => ['label' => 'Bekerja (Karyawan/Pegawai)', 'color' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30', 'icon' => '💼'],
            'WIRAUSAHA' => ['label' => 'Wirausaha / Usaha Mandiri', 'color' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30', 'icon' => '🚀'],
            'KURSUS_PELATIHAN' => ['label' => 'Kursus / Pelatihan / Magang', 'color' => 'bg-purple-500/10 text-purple-400 border border-purple-500/30', 'icon' => '📚'],
            'MENCARI_KERJA' => ['label' => 'Mencari Kerja / Gap Year', 'color' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30', 'icon' => '⏳'],
            default => ['label' => $this->activity_status, 'color' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30', 'icon' => '📌'],
        };
    }

    public function getActivityDetailSummaryAttribute(): string
    {
        return match ($this->activity_status) {
            'KULIAH' => trim(($this->campus_name ?: 'Perguruan Tinggi').($this->study_program ? ' - Prodi '.$this->study_program : '').($this->admission_track ? ' (Jalur '.$this->admission_track.')' : '')),
            'BEKERJA' => trim(($this->company_name ?: 'Perusahaan/Instansi').($this->job_position ? ' - '.$this->job_position : '').($this->work_field ? ' ('.$this->work_field.')' : '')),
            'WIRAUSAHA' => trim(($this->business_name ?: 'Usaha Mandiri').($this->business_field ? ' - '.$this->business_field : '')),
            'KURSUS_PELATIHAN' => 'Pelatihan / Kursus Keterampilan',
            'MENCARI_KERJA' => 'Masa Tunggu / Persiapan Karier',
            default => '-',
        };
    }
}
