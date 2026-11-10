<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasSubjectGroup extends Model
{
    use HasFactory;

    protected $table = 'kurnas_subject_groups';

    protected $fillable = [
        'subject_id',
        'group_type',
        'display_name',
        'order_number',
        'is_combined',
        'combine_parent_code',
    ];

    protected function casts(): array
    {
        return [
            'order_number' => 'integer',
            'is_combined' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
