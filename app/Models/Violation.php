<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'category_id',
        'item_id',
        'violation_code',
        'title',
        'points',
        'date',
        'time',
        'location',
        'chronology',
        'reporter_id',
        'evidence_path',
        'guidance_notes',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'points' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ViolationCategory::class, 'category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ViolationItem::class, 'item_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function guidanceRecords(): HasMany
    {
        return $this->hasMany(GuidanceRecord::class);
    }

    public function isVerified(): bool
    {
        return $this->status === 'DIVERIFIKASI';
    }

    /**
     * Dapatkan identitas resmi verifikator (Wakasek Kesiswaan)
     */
    public function getVerifierDisplayAttribute(): string
    {
        if (! $this->isVerified()) {
            return 'Menunggu Verifikasi';
        }

        $wakasek = Teacher::getWakasekKesiswaan();
        $name = $wakasek['name'] ?? ($this->verifier?->name ?? 'Winda Dwi Atyani, S.Pd., M.Pd.');
        $nip = $wakasek['nip'] ?? ($this->verifier?->nip ?? '197911162008012007');

        return "{$name} (Wakasek Kesiswaan • NIP. {$nip})";
    }
}
