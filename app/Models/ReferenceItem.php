<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<ReferenceItem>  $query
     * @return Builder<ReferenceItem>
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category)->where('is_active', true)->orderBy('sort_order');
    }
}
