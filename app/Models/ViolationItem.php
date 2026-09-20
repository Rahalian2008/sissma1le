<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'default_points',
        'guidance_recommendation',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ViolationCategory::class, 'category_id');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class, 'item_id');
    }
}
