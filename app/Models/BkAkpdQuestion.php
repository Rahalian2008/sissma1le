<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkAkpdQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function akpd(): BelongsTo
    {
        return $this->belongsTo(BkAkpd::class, 'akpd_id');
    }
}
