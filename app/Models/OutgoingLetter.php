<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (OutgoingLetter $letter) {
            if (empty($letter->agenda_number)) {
                $count = static::whereYear('letter_date', date('Y'))->count() + 1;
                $letter->agenda_number = 'SK-'.date('Y').'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
            if (empty($letter->letter_number)) {
                $letter->letter_number = '421.3/'.rand(100, 999).'/SMAN1LKG/'.date('Y');
            }
            if (empty($letter->recipient)) {
                $letter->recipient = 'Tujuan Surat';
            }
            if (empty($letter->letter_date)) {
                $letter->letter_date = now()->toDateString();
            }
            if (empty($letter->subject)) {
                $letter->subject = 'Surat Keluar';
            }
            if (empty($letter->signer_name)) {
                $letter->signer_name = 'Kepala Sekolah';
            }
            if (empty($letter->status)) {
                $letter->status = 'Disetujui';
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
        ];
    }
}
