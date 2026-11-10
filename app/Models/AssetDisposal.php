<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disposal_date' => 'date',
            'approved_at' => 'datetime',
            'estimated_scrap_value' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public static function generateDisposalNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('disposal_date', $year)->count() + 1;

        return "BA-AFKIR/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
