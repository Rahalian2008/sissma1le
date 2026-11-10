<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'book_id',
        'member_id',
        'user_id',
        'request_date',
        'loan_date',
        'due_date',
        'pickup_deadline',
        'return_date',
        'status',
        'fine_amount',
        'is_fine_paid',
        'extension_count',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'request_notes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
            'loan_date' => 'date',
            'due_date' => 'date',
            'pickup_deadline' => 'date',
            'return_date' => 'date',
            'approved_at' => 'datetime',
            'fine_amount' => 'decimal:2',
            'is_fine_paid' => 'boolean',
            'extension_count' => 'integer',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'MENUNGGU';
    }

    public function isApproved(): bool
    {
        return $this->status === 'DISETUJUI';
    }

    public function isBorrowed(): bool
    {
        return in_array($this->status, ['DIPINJAM', 'TERLAMBAT'], true);
    }

    public function isReturned(): bool
    {
        return $this->status === 'KEMBALI';
    }

    public function isRejected(): bool
    {
        return $this->status === 'DITOLAK';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'DIBATALKAN';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'MENUNGGU' => 'Menunggu Persetujuan',
            'DISETUJUI' => 'Disetujui (Siap Diambil)',
            'DIPINJAM' => 'Sedang Dipinjam',
            'KEMBALI' => 'Telah Dikembalikan',
            'TERLAMBAT' => 'Terlambat',
            'DITOLAK' => 'Pengajuan Ditolak',
            'DIBATALKAN' => 'Pengajuan Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'MENUNGGU' => 'bg-amber-500/10 text-amber-500 dark:text-amber-400 border border-amber-500/30',
            'DISETUJUI' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30',
            'DIPINJAM' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30',
            'KEMBALI' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30',
            'TERLAMBAT' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30 animate-pulse',
            'DITOLAK', 'DIBATALKAN' => 'bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/30',
            default => 'bg-slate-500/10 text-slate-500 border border-slate-500/30',
        };
    }

    public function isOverdue(): bool
    {
        if ($this->status === 'KEMBALI' && $this->return_date) {
            return Carbon::parse($this->return_date)->startOfDay()->gt($this->due_date);
        }

        return now()->startOfDay()->gt($this->due_date);
    }

    public function getDaysOverdueAttribute(): int
    {
        $targetDate = ($this->status === 'KEMBALI' && $this->return_date)
            ? Carbon::parse($this->return_date)->startOfDay()
            : now()->startOfDay();

        if ($targetDate->gt($this->due_date)) {
            return (int) $this->due_date->diffInDays($targetDate);
        }

        return 0;
    }
}
