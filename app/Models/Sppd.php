<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sppd extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Sppd $sppd) {
            if (empty($sppd->sppd_number)) {
                $count = static::whereYear('departure_date', date('Y'))->count() + 1;
                $sppd->sppd_number = '090/SPPD/'.str_pad((string) $count, 3, '0', STR_PAD_LEFT).'/'.date('Y');
            }
            if (empty($sppd->assignment_purpose)) {
                $sppd->assignment_purpose = 'Tugas Kedinasan';
            }
            if (empty($sppd->departure_location)) {
                $sppd->departure_location = 'SMAN 1 Lengkong';
            }
            if (empty($sppd->destination_location)) {
                $sppd->destination_location = 'Dinas Pendidikan';
            }
            if (empty($sppd->departure_date)) {
                $sppd->departure_date = now()->toDateString();
            }
            if (empty($sppd->return_date)) {
                $sppd->return_date = now()->toDateString();
            }
            if (empty($sppd->ppk_name)) {
                $sppd->ppk_name = 'Kepala Sekolah';
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'duration_days' => 'integer',
        ];
    }

    public function getDurationSpellAttribute(): string
    {
        $numbers = [
            1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
            6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh',
            11 => 'sebelas', 12 => 'dua belas', 13 => 'tiga belas', 14 => 'empat belas', 15 => 'lima belas',
            16 => 'enam belas', 17 => 'tujuh belas', 18 => 'delapan belas', 19 => 'sembilan belas', 20 => 'dua puluh',
            21 => 'dua puluh satu', 22 => 'dua puluh dua', 25 => 'dua puluh lima', 30 => 'tiga puluh',
        ];

        return $numbers[$this->duration_days] ?? (string) $this->duration_days;
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return BelongsTo<OutgoingLetter, $this>
     */
    public function outgoingLetter(): BelongsTo
    {
        return $this->belongsTo(OutgoingLetter::class);
    }
}
