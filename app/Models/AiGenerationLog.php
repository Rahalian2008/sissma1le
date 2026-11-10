<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_type',
        'provider',
        'model',
        'prompt_summary',
        'is_fallback',
        'response_time_ms',
        'token_estimate',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_fallback' => 'boolean',
            'response_time_ms' => 'integer',
            'token_estimate' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
