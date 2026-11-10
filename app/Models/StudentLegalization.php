<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLegalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'student_id',
        'applicant_name',
        'nisn',
        'nis',
        'graduation_year',
        'phone_number',
        'document_type',
        'copies_count',
        'purpose',
        'status',
        'notes',
        'verified_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'copies_count' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateReceiptNumber(): string
    {
        $yearMonth = now()->format('Ym');
        $prefix = "LEG-{$yearMonth}-";

        $lastNumber = static::where('receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('receipt_number');

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $nextSequence = (int) $matches[1] + 1;
        } else {
            $nextSequence = 1;
        }

        return $prefix.str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
