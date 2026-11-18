<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BkConsultationMessage extends Model
{
    use HasFactory;

    protected $table = 'bk_consultation_messages';

    protected $fillable = [
        'consultation_id',
        'sender_id',
        'sender_role',
        'message',
        'attachment_type',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(BkConsultation::class, 'consultation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function getFormattedSizeAttribute(): ?string
    {
        if (! $this->attachment_size) {
            return null;
        }

        $bytes = $this->attachment_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->attachment_type === 'IMAGE' || (
            $this->attachment_mime && str_starts_with($this->attachment_mime, 'image/')
        );
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->attachment_type === 'VIDEO' || (
            $this->attachment_mime && str_starts_with($this->attachment_mime, 'video/')
        );
    }

    public function getIsAudioAttribute(): bool
    {
        return $this->attachment_type === 'AUDIO' || (
            $this->attachment_mime && str_starts_with($this->attachment_mime, 'audio/')
        );
    }

    public function getIsDocumentAttribute(): bool
    {
        return $this->attachment_type === 'DOCUMENT' || (! $this->is_image && ! $this->is_video && ! $this->is_audio && ! empty($this->attachment_path));
    }

    public function getRenderedMessageHtmlAttribute(): string
    {
        if (empty($this->message)) {
            return '';
        }

        $escaped = e($this->message);

        // Convert URLs to clickable hyperlinks
        $pattern = '/(https?:\/\/[^\s<]+)/i';
        $linkified = preg_replace_callback($pattern, function ($matches) {
            $url = $matches[1];
            $display = strlen($url) > 45 ? substr($url, 0, 42).'...' : $url;

            return '<a href="'.$url.'" target="_blank" rel="noopener noreferrer" class="underline text-sky-400 hover:text-sky-300 break-all font-semibold inline-flex items-center gap-1"><span>🔗</span> '.$display.'</a>';
        }, $escaped);

        return nl2br($linkified);
    }
}
