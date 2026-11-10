<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbRegistration extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'tka_exam_date' => 'date',
            'verified_at' => 'datetime',
            'passed_at' => 'datetime',
            'average_score' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'tka_score_literacy' => 'decimal:2',
            'tka_score_numeracy' => 'decimal:2',
            'tka_score_science' => 'decimal:2',
            'tka_score_total' => 'decimal:2',
            'tka_weighted_score' => 'decimal:2',
            'tka_rank' => 'integer',
            'report_score_sem1' => 'decimal:2',
            'report_score_sem2' => 'decimal:2',
            'report_score_sem3' => 'decimal:2',
            'report_score_sem4' => 'decimal:2',
            'report_score_sem5' => 'decimal:2',
            'report_score_sem6' => 'decimal:2',
        ];
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function spmbTrack(): BelongsTo
    {
        return $this->belongsTo(SpmbTrack::class, 'spmb_track_id');
    }

    public function spmbWave(): BelongsTo
    {
        return $this->belongsTo(SpmbWave::class, 'spmb_wave_id');
    }

    public function scopeTkaTrack(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('selection_track', 'Tes Kemampuan Akademik (TKA)')
                ->orWhere('selection_track', 'like', '%TKA%');
        });
    }

    public function scopePassed(Builder $query): Builder
    {
        return $query->whereIn('status', ['Lolos Seleksi', 'Diterima', 'Daftar Ulang']);
    }

    /**
     * Hitung Skor Rata-rata TKA & Skor Akhir Terbobot (60% TKA + 40% Nilai Rapor)
     */
    public function computeTkaScores(float $tkaWeight = 0.60, float $raporWeight = 0.40): void
    {
        $lit = (float) ($this->tka_score_literacy ?? 0);
        $num = (float) ($this->tka_score_numeracy ?? 0);
        $sci = (float) ($this->tka_score_science ?? 0);

        $tkaAverage = round(($lit + $num + $sci) / 3, 2);
        $rapor = (float) ($this->average_score ?? 0);

        $weighted = round(($tkaAverage * $tkaWeight) + ($rapor * $raporWeight), 2);

        $this->tka_score_total = $tkaAverage;
        $this->tka_weighted_score = $weighted;

        if ($this->tka_status === 'Belum Ujian' && ($lit > 0 || $num > 0 || $sci > 0)) {
            $this->tka_status = 'Selesai Ujian';
        }
    }

    /**
     * Generate Nomor Registrasi SPMB Resmi SMAN 1 Lengkong
     */
    public static function generateRegistrationNumber(string $track = 'Zonasi'): string
    {
        $year = date('Y');
        $code = match (true) {
            str_contains($track, 'TKA') => 'TKA',
            str_contains($track, 'Afirmasi') => 'AFR',
            str_contains($track, 'Prestasi') => 'PRS',
            str_contains($track, 'Perpindahan') => 'PTO',
            default => 'ZON',
        };

        $count = static::whereYear('created_at', $year)->count() + 1;

        return 'SPMB-'.$year.'-'.$code.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Peserta Ujian TKA
     */
    public static function generateTkaExamNumber(): string
    {
        $year = date('Y');
        $count = static::whereNotNull('tka_exam_number')->whereYear('created_at', $year)->count() + 1;

        return 'TKA-'.$year.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function getTrackBadgeClass(): string
    {
        return match (true) {
            str_contains((string) $this->selection_track, 'TKA') => 'bg-amber-100 text-amber-800 border-amber-200',
            str_contains((string) $this->selection_track, 'Afirmasi') => 'bg-purple-100 text-purple-800 border-purple-200',
            str_contains((string) $this->selection_track, 'Prestasi') => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            str_contains((string) $this->selection_track, 'Perpindahan') => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'Lolos Seleksi', 'Diterima' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Daftar Ulang' => 'bg-teal-100 text-teal-800 border-teal-300',
            'Terverifikasi' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Cadangan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Tidak Lolos', 'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    /**
     * Hitung Rata-rata Nilai Rapor SMP/MTs (Semester 1 - 5 Wajib, Semester 6 jika ada)
     */
    public function computeReportAverage(): float
    {
        $scores = array_filter([
            $this->report_score_sem1,
            $this->report_score_sem2,
            $this->report_score_sem3,
            $this->report_score_sem4,
            $this->report_score_sem5,
            $this->report_score_sem6,
        ], fn ($val) => $val !== null && $val > 0);

        if (empty($scores)) {
            return (float) ($this->average_score ?? 0);
        }

        return round(array_sum($scores) / count($scores), 2);
    }
}
