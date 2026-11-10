<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LmsMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'learning_objective_id',
        'title',
        'topic',
        'type',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'video_questions',
        'is_published',
        'start_time',
        'views_count',
        // Aliases for form compatibility
        'content_type',
        'description',
        'content_text',
        'video_url',
    ];

    public function getContentTypeAttribute(): string
    {
        return $this->attributes['type'] ?? 'dokumen';
    }

    public function setContentTypeAttribute(?string $value): void
    {
        $this->attributes['type'] = $value ?? 'dokumen';
    }

    public function getDescriptionAttribute(): ?string
    {
        $content = $this->attributes['content'] ?? null;
        if (! $content) {
            return null;
        }

        if (str_starts_with(trim($content), '{') && str_ends_with(trim($content), '}')) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded['catatan_guru'] ?? ($decoded['ringkasan_materi'] ? Str::limit(strip_tags($decoded['ringkasan_materi']), 180) : null);
            }
        }

        return $content;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    public function getContentTextAttribute(): ?string
    {
        return $this->attributes['content'] ?? null;
    }

    public function setContentTextAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->attributes['external_url'] ?? null;
    }

    public function setVideoUrlAttribute(?string $value): void
    {
        $this->attributes['external_url'] = $value;
    }

    protected function casts(): array
    {
        return [
            'video_questions' => 'array',
            'is_published' => 'boolean',
            'start_time' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    /**
     * Cek apakah materi sudah memasuki waktu dimulai untuk diakses siswa.
     */
    public function isAvailable(): bool
    {
        if (! $this->start_time) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->start_time);
    }

    /**
     * Format teks waktu dimulai untuk tampilan.
     */
    public function formattedStartTime(): string
    {
        if (! $this->start_time) {
            return 'Langsung Aktif';
        }

        return $this->start_time->translatedFormat('d M Y, H:i').' WIB';
    }

    /**
     * Cek apakah materi ini berbasis video (URL atau berkas MP4).
     */
    public function isVideoContent(): bool
    {
        if ($this->content_type === 'video') {
            return true;
        }

        if (! empty($this->external_url) || ! empty($this->video_url)) {
            return true;
        }

        if (! empty($this->file_path)) {
            $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

            return in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
        }

        return false;
    }

    /**
     * Cek apakah berkas lampiran adalah dokumen (PDF, PPT, DOC, dsb).
     */
    public function getDocumentExtension(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function isEmbeddedDocument(): bool
    {
        $ext = $this->getDocumentExtension();

        return in_array($ext, ['pdf', 'ppt', 'pptx', 'doc', 'docx']);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(LmsMaterialRead::class, 'material_id');
    }

    public function isReadBy(int $studentId): bool
    {
        return $this->reads()->where('student_id', $studentId)->exists();
    }

    public function formattedFileSize(): string
    {
        if (! $this->file_size) {
            return '-';
        }
        if ($this->file_size >= 1048576) {
            return round($this->file_size / 1048576, 2).' MB';
        }

        return round($this->file_size / 1024, 1).' KB';
    }
}
