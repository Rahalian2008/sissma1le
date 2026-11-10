<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KurnasSubjectCombine extends Model
{
    use HasFactory;

    protected $table = 'kurnas_subject_combines';

    protected $fillable = [
        'grade',
        'combined_name',
        'combined_code',
        'source_subject_ids',
        'order_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'source_subject_ids' => 'array',
            'order_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getSourceSubjectsAttribute()
    {
        return Subject::whereIn('id', $this->source_subject_ids ?? [])->get();
    }
}
