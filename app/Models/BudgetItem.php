<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'realized_amount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Budget, $this>
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * @return HasMany<CashTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->allocated_amount - $this->realized_amount);
    }

    public function getRealizationPercentageAttribute(): float
    {
        if ($this->allocated_amount <= 0) {
            return 0.0;
        }

        return round(($this->realized_amount / $this->allocated_amount) * 100, 2);
    }
}
