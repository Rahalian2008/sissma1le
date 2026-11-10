<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsIssuanceItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<GoodsIssuance, $this>
     */
    public function issuance(): BelongsTo
    {
        return $this->belongsTo(GoodsIssuance::class, 'goods_issuance_id');
    }

    /**
     * @return BelongsTo<Good, $this>
     */
    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }
}
