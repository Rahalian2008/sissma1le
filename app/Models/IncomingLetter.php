<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'received_date' => 'date',
        ];
    }

    /**
     * @return HasMany<Disposition, $this>
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }
}
