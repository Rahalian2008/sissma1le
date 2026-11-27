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
        'high_school_major',
        'study_funding_source',
        'phone',
        'email',
        'current_address',
        'current_city',
        'activity_status',
        'work_type',
        'college_level',
        'campus_name',
        'study_program',
        'college_start_date',
        'college_funding_source',
        'admission_track',
        'major_alignment',
        'company_name',
        'company_type',
        'company_location',
        'work_field',
        'job_position',
        'monthly_income_range',
        'job_waiting_time',
        'job_search_start',
        'job_search_method',
        'business_name',
        'business_field',
        'business_revenue_range',
        'employee_count',
        'job_major_relevance',
        'job_education_level_match',
        'job_mismatch_reason',
        'competencies_evaluation',
        'learning_methods_evaluation',
        'bk_service_rating',
        'bk_career_guidance_impact',
        'school_feedback',
        'improvement_suggestions',
        'is_verified',
        'ip_address',
    ];

    protected $casts = [
        'bk_service_rating' => 'integer',
        'employee_count' => 'integer',
        'is_verified' => 'boolean',
        'competencies_evaluation' => 'array',
        'learning_methods_evaluation' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getActivityStatusBadgeAttribute(): array
    {
        return match ($this->activity_status) {
            'KULIAH' => ['label' => 'Kuliah / Studi Lanjut', 'color' => 'bg-sky-500/10 text-sky-400 border border-sky-500/30', 'icon' => '🎓'],
            'BEKERJA' => ['label' => 'Bekerja' . ($this->work_type ? ' (' . $this->work_type . ')' : ''), 'color' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30', 'icon' => '💼'],
            'WIRAUSAHA' => ['label' => 'Wiraswasta / Wirausaha', 'color' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30', 'icon' => '🚀'],
            'BELUM_MEMUNGKINKAN' => ['label' => 'Belum Memungkinkan Bekerja', 'color' => 'bg-rose-500/10 text-rose-400 border border-rose-500/30', 'icon' => '🏡'],
            'MENCARI_KERJA' => ['label' => 'Mencari Kerja / Persiapan', 'color' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30', 'icon' => '🔍'],
            'KURSUS_PELATIHAN' => ['label' => 'Kursus / Pelatihan', 'color' => 'bg-purple-500/10 text-purple-400 border border-purple-500/30', 'icon' => '📚'],
            default => ['label' => $this->activity_status, 'color' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30', 'icon' => '📌'],
        };
    }

    public function getActivityBadgeClassAttribute(): string
    {
        return $this->activity_status_badge['color'] ?? 'bg-slate-500/10 text-slate-400 border border-slate-500/30';
    }

    public function getActivityStatusLabelAttribute(): string
    {
        return $this->activity_status_badge['label'] ?? $this->activity_status;
    }

    public static function standardCompetencies(): array
    {
        return [
            'etika_integritas' => 'Etika dan Integritas Moral',
            'keahlian_bidang' => 'Keahlian Berdasarkan Bidang Ilmu',
            'bahasa_asing' => 'Kemampuan Bahasa Asing (Bahasa Inggris)',
            'teknologi_informasi' => 'Penggunaan Teknologi Informasi (IT)',
            'komunikasi_kerjasama' => 'Komunikasi & Kerja Sama Tim',
            'pengembangan_diri' => 'Pengembangan Diri & Kemandirian Belajar',
        ];
    }

    public static function standardLearningMethods(): array
    {
        return [
            'teori_kelas' => 'Pembelajaran & Teori di Kelas',
            'praktikum_lab' => 'Praktikum di Laboratorium / Bengkel',
            'magang_pkl' => 'Magang / PKL / Studi Lapangan',
            'diskusi' => 'Diskusi Kelompok & Presentasi',
            'proyek_riset' => 'Proyek / Riset / Penugasan Kolaboratif',
        ];
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
