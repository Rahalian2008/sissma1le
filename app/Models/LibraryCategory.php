<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ddc_code',
        'name',
        'description',
        'color',
        'icon',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'category_id');
    }
}
