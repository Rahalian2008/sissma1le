<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuidanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'violation_id',
        'date',
        'follow_up_type',
        'agreement_letter_path',
        'recommendation',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }

    public function followUpLabel(): string
    {
        return match ($this->follow_up_type) {
            'BIMBINGAN_WALI_KELAS' => 'Bimbingan Wali Kelas',
            'SP1_BK' => 'Surat Peringatan 1 (SP 1 - BK)',
            'SP2_BK' => 'Surat Peringatan 2 (SP 2 - BK)',
            'SP3_BK_KESISWAAN' => 'Surat Peringatan 3 (SP 3 - BK & Kesiswaan)',
            'RAPAT_KHUSUS' => 'Rapat Khusus Dewan Guru & Komite',
            'KONSELING_RUTIN' => 'Konseling Rutin BK',
            default => $this->follow_up_type,
        };
    }
}
