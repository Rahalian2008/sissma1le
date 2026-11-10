<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationStructure extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<OrganizationStructure, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationStructure::class, 'parent_id');
    }

    /**
     * @return HasMany<OrganizationStructure, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(OrganizationStructure::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
