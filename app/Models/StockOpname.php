<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
            'system_stock' => 'integer',
            'physical_stock' => 'integer',
            'difference' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Good, $this>
     */
    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }
}
