<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (IncomingLetter $letter) {
            if (empty($letter->agenda_number)) {
                $count = static::whereYear('received_date', date('Y'))->count() + 1;
                $letter->agenda_number = 'SM-'.date('Y').'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
            if (empty($letter->letter_number)) {
                $letter->letter_number = '000/SM/'.date('Y');
            }
            if (empty($letter->sender)) {
                $letter->sender = 'Instansi Pengirim';
            }
            if (empty($letter->letter_date)) {
                $letter->letter_date = now()->toDateString();
            }
            if (empty($letter->received_date)) {
                $letter->received_date = now()->toDateString();
            }
            if (empty($letter->subject)) {
                $letter->subject = 'Surat Masuk';
            }
            if (empty($letter->character)) {
                $letter->character = 'Biasa';
            }
            if (empty($letter->status)) {
                $letter->status = 'Menunggu Disposisi';
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
            'received_date' => 'date',
        ];
    }

    /**
     * @return HasMany<Disposition, $this>
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }
}
