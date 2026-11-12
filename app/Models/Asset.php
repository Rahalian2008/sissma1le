<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->code)) {
                $asset->code = 'AST-'.date('Y').'-'.strtoupper(substr(uniqid(), -5));
            }
            if (empty($asset->name)) {
                $asset->name = 'Aset Sarpras';
            }
            if (empty($asset->procurement_year)) {
                $asset->procurement_year = (int) date('Y');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'procurement_year' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsTo<GoodsReceipt, $this>
     */
    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    /**
     * @return HasMany<AssetMaintenance, $this>
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    /**
     * @return HasMany<AssetDisposal, $this>
     */
    public function disposals(): HasMany
    {
        return $this->hasMany(AssetDisposal::class);
    }

    /**
     * @return HasMany<CashTransaction, $this>
     */
    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function getKibNameAttribute(): string
    {
        $kibs = [
            'A' => 'KIB A (Tanah)',
            'B' => 'KIB B (Peralatan & Mesin)',
            'C' => 'KIB C (Gedung & Bangunan)',
            'D' => 'KIB D (Jalan & Jaringan)',
            'E' => 'KIB E (Aset Tetap Lainnya)',
            'F' => 'KIB F (KDP)',
        ];

        return $kibs[$this->kib_type ?? 'B'] ?? "KIB {$this->kib_type}";
    }

    public function getFullBmdCodeAttribute(): string
    {
        if (! empty($this->sim_bmd_code)) {
            $reg = $this->register_number ?? '0001';

            return "{$this->sim_bmd_code}.{$this->procurement_year}.{$reg}";
        }

        return $this->code;
    }
}
