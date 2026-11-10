<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarlyLeaveRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Generate Nomor Surat Resmi Surat Keterangan Pulang Cepat
     * Format: 421.3/SKPC/{NOMOR}/{BULAN_ROMAWI}/{TAHUN}
     */
    public static function generateLetterNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        $year = $date->format('Y');
        $month = $date->format('n');

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $romanMonth = $romanMonths[$month] ?? 'XI';

        $count = static::whereYear('date', $year)->count() + 1;
        $padded = str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        return "421.3/SKPC/{$padded}/{$romanMonth}/{$year}";
    }

    public function getStatusBadgeAttribute(): string
    {
        $badge = match ($this->status) {
            'APPROVED' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300'],
            'REJECTED' => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300'],
            default => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300'],
        };

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border '.$badge['class'].'">'.e($badge['label']).'</span>';
    }

    public function getStatusBadgeDataAttribute(): array
    {
        return match ($this->status) {
            'APPROVED' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300'],
            'REJECTED' => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300'],
            default => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300'],
        };
    }
}
