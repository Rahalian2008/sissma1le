<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposition extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Disposition $disp) {
            if (empty($disp->target_role_or_user)) {
                $disp->target_role_or_user = 'Waka / Staf Terkait';
            }
            if (empty($disp->disposition_date)) {
                $disp->disposition_date = now()->toDateString();
            }
            if (empty($disp->instructions)) {
                $disp->instructions = 'Untuk ditindaklanjuti';
            }
            if (empty($disp->status)) {
                $disp->status = 'Menunggu';
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disposition_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<IncomingLetter, $this>
     */
    public function incomingLetter(): BelongsTo
    {
        return $this->belongsTo(IncomingLetter::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by_user_id');
    }
}
