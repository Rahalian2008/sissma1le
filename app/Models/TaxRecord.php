<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tax_base' => 'decimal:2',
            'rate_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'deposit_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<CashTransaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class, 'cash_transaction_id');
    }
}
