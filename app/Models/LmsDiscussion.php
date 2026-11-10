<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'user_id',
        'parent_id',
        'title',
        'content',
        'message',
        'is_pinned',
    ];

    public function getMessageAttribute(): ?string
    {
        return $this->attributes['content'] ?? null;
    }

    public function setMessageAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }
}
