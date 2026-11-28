<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentScholarship extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'proposal_date' => 'date',
            'sk_date' => 'date',
            'disbursement_date' => 'date',
        ];
    }

    public function getTanggalUsulanAttribute(): ?string
    {
        return $this->proposal_date?->translatedFormat('d/m/Y');
    }

    public function getTanggalSkAttribute(): ?string
    {
        return $this->sk_date?->translatedFormat('d/m/Y');
    }

    public function getTanggalCairAttribute(): ?string
    {
        return $this->disbursement_date?->translatedFormat('d/m/Y');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
