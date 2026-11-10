<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkAkpdResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'selected_question_ids' => 'array',
            'score_per_field' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function akpd(): BelongsTo
    {
        return $this->belongsTo(BkAkpd::class, 'akpd_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
