<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterExpedition extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'delivery_date' => 'date',
            'received_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<OutgoingLetter, $this>
     */
    public function outgoingLetter(): BelongsTo
    {
        return $this->belongsTo(OutgoingLetter::class);
    }

    /**
     * Generate unique expedition number (e.g. EXP/2026/0001)
     */
    public static function generateExpeditionNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('delivery_date', $year)->count() + 1;

        return "EXP/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
