<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Good extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Good $good) {
            if (empty($good->code)) {
                $good->code = 'BRG-'.strtoupper(substr(uniqid(), -6));
            }
            if (empty($good->name)) {
                $good->name = 'Barang Inventaris';
            }
            if (empty($good->unit)) {
                $good->unit = 'Unit';
            }
            if ($good->current_stock === null) {
                $good->current_stock = 0;
            }
            if ($good->minimum_stock === null) {
                $good->minimum_stock = 0;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * @return HasMany<GoodsReceiptItem, $this>
     */
    public function receiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    /**
     * @return HasMany<GoodsIssuanceItem, $this>
     */
    public function issuanceItems(): HasMany
    {
        return $this->hasMany(GoodsIssuanceItem::class);
    }

    /**
     * @return HasMany<StockOpname, $this>
     */
    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
