<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolExitPermit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (SchoolExitPermit $model) {
            if (empty($model->permit_number)) {
                $model->permit_number = static::generatePermitNumber($model->date ? Carbon::parse($model->date) : null);
            }
            if (empty($model->date)) {
                $model->date = Carbon::today('Asia/Jakarta')->toDateString();
            }
            if (empty($model->out_time)) {
                $model->out_time = Carbon::now('Asia/Jakarta')->format('H:i:s');
            }
            if (empty($model->purpose)) {
                $model->purpose = 'Izin Keluar Lingkungan Sekolah';
            }
            if (empty($model->reason)) {
                $model->reason = 'Keperluan Mendesak';
            }
            if (empty($model->status)) {
                $model->status = 'PENDING';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_returning' => 'boolean',
            'approved_at' => 'datetime',
            'security_checked_out_at' => 'datetime',
            'security_checked_in_at' => 'datetime',
        ];
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'school_exit_permit_students')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnRecorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStudentAttribute(): ?Student
    {
        return $this->students->first();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeActiveOutside($query)
    {
        return $query->where('status', 'APPROVED')
            ->whereNull('actual_back_time')
            ->where('is_returning', true);
    }

    /**
     * Generate Nomor Surat Resmi Surat Izin Keluar Lingkungan Sekolah
     * Format: 421.3/SIK-LS/{NOMOR}/{BULAN_ROMAWI}/{TAHUN}
     */
    public static function generatePermitNumber(?Carbon $date = null): string
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

        return "421.3/SIK-LS/{$padded}/{$romanMonth}/{$year}";
    }

    public function getStatusBadgeAttribute(): string
    {
        $badge = match ($this->status) {
            'APPROVED' => ['label' => 'Disetujui (Izin Berlaku)', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300'],
            'RETURNED' => ['label' => 'Sudah Kembali ke Sekolah', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border-blue-300'],
            'REJECTED' => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300'],
            default => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300'],
        };

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border '.$badge['class'].'">'.e($badge['label']).'</span>';
    }

    public function getStatusBadgeDataAttribute(): array
    {
        return match ($this->status) {
            'APPROVED' => ['label' => 'Disetujui (Izin Berlaku)', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300'],
            'RETURNED' => ['label' => 'Sudah Kembali ke Sekolah', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border-blue-300'],
            'REJECTED' => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300'],
            default => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300'],
        };
    }
}
