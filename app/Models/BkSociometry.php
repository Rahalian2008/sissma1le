<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkSociometry extends Model
{
    use HasFactory;

    protected $table = 'bk_sociometries';

    protected $fillable = [
        'class_id',
        'counselor_id',
        'title',
        'date',
        'theme',
        'status',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(BkSociometryResponse::class, 'sociometry_id');
    }
}
