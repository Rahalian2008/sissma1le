<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CashTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (CashTransaction $tx) {
            if (empty($tx->evidence_number)) {
                $count = static::whereYear('transaction_date', date('Y'))->count() + 1;
                $tx->evidence_number = 'BKK-'.date('Ymd').'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
            if (empty($tx->transaction_date)) {
                $tx->transaction_date = now()->toDateString();
            }
            if (empty($tx->description)) {
                $tx->description = 'Transaksi Kas';
            }
            if (empty($tx->recipient_or_payer)) {
                $tx->recipient_or_payer = 'Bendahara Sekolah';
            }
            if ($tx->debit === null) {
                $tx->debit = 0;
            }
            if ($tx->credit === null) {
                $tx->credit = 0;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'running_balance' => 'decimal:2',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<BudgetItem, $this>
     */
    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(BudgetItem::class);
    }

    public function budget(): HasOneThrough
    {
        return $this->hasOneThrough(Budget::class, BudgetItem::class, 'id', 'id', 'budget_item_id', 'budget_id');
    }

    /**
     * @return BelongsTo<GoodsReceipt, $this>
     */
    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * @return HasMany<TaxRecord, $this>
     */
    public function taxes(): HasMany
    {
        return $this->hasMany(TaxRecord::class);
    }
}
