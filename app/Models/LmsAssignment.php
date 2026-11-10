<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'learning_objective_id',
        'title',
        'topic',
        'instructions',
        'attachment_path',
        'attachment_name',
        'start_time',
        'due_date',
        'max_score',
        'allow_late',
        'submission_type',
        'allowed_file_types',
        // Aliases for form compatibility
        'description',
        'allow_late_submission',
        'file_attachment',
    ];

    public function getDescriptionAttribute(): ?string
    {
        return $this->attributes['instructions'] ?? null;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['instructions'] = $value;
    }

    public function getAllowLateSubmissionAttribute(): bool
    {
        return (bool) ($this->attributes['allow_late'] ?? true);
    }

    public function setAllowLateSubmissionAttribute(bool|int|string|null $value): void
    {
        $this->attributes['allow_late'] = (bool) $value;
    }

    public function getFileAttachmentAttribute(): ?string
    {
        return $this->attributes['attachment_path'] ?? null;
    }

    public function setFileAttachmentAttribute(?string $value): void
    {
        $this->attributes['attachment_path'] = $value;
    }

    public function getSubmissionTypeLabelAttribute(): string
    {
        return match ($this->submission_type) {
            'text' => 'Hanya Teks Langsung',
            'file' => 'Unggah Berkas / Dokumen',
            default => 'Teks & Unggah Berkas',
        };
    }

    public function allowsText(): bool
    {
        return in_array($this->submission_type ?? 'both', ['text', 'both']);
    }

    public function allowsFile(): bool
    {
        return in_array($this->submission_type ?? 'both', ['file', 'both']);
    }

    /**
     * @return array<string>
     */
    public function getAllowedFileTypesListAttribute(): array
    {
        $types = $this->allowed_file_types;
        if (empty($types) || ! is_array($types)) {
            return ['pdf', 'ppt', 'image'];
        }

        return $types;
    }

    public function getAllowedMimesRuleAttribute(): string
    {
        $types = $this->allowed_file_types_list;
        $mimes = [];

        if (in_array('pdf', $types)) {
            $mimes[] = 'pdf';
        }
        if (in_array('ppt', $types)) {
            $mimes[] = 'ppt';
            $mimes[] = 'pptx';
        }
        if (in_array('image', $types)) {
            $mimes[] = 'jpg';
            $mimes[] = 'jpeg';
            $mimes[] = 'png';
            $mimes[] = 'webp';
        }

        if (empty($mimes)) {
            $mimes = ['pdf', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp'];
        }

        return implode(',', array_unique($mimes));
    }

    public function getAcceptMimesAttribute(): string
    {
        $types = $this->allowed_file_types_list;
        $accept = [];

        if (in_array('pdf', $types)) {
            $accept[] = '.pdf,application/pdf';
        }
        if (in_array('ppt', $types)) {
            $accept[] = '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation';
        }
        if (in_array('image', $types)) {
            $accept[] = 'image/*,.jpg,.jpeg,.png,.webp';
        }

        if (empty($accept)) {
            $accept = ['.pdf', '.ppt', '.pptx', 'image/*'];
        }

        return implode(',', $accept);
    }

    public function getAllowedFileTypesFormattedAttribute(): string
    {
        $types = $this->allowed_file_types_list;
        $labels = [];

        if (in_array('pdf', $types)) {
            $labels[] = 'PDF (.pdf)';
        }
        if (in_array('ppt', $types)) {
            $labels[] = 'Presentasi PPT (.ppt, .pptx)';
        }
        if (in_array('image', $types)) {
            $labels[] = 'Foto / Gambar (.jpg, .jpeg, .png, .webp)';
        }

        return ! empty($labels) ? implode(', ', $labels) : 'PDF, PPT, atau Gambar';
    }

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'due_date' => 'datetime',
            'max_score' => 'integer',
            'allow_late' => 'boolean',
            'is_published' => 'boolean',
            'allowed_file_types' => 'array',
        ];
    }

    /**
     * Cek apakah penugasan sudah memasuki waktu mulai untuk diakses siswa.
     */
    public function isAvailable(): bool
    {
        if (! $this->start_time) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->start_time);
    }

    /**
     * Format teks waktu mulai untuk tampilan.
     */
    public function formattedStartTime(): string
    {
        if (! $this->start_time) {
            return 'Langsung Aktif';
        }

        return $this->start_time->translatedFormat('d M Y, H:i').' WIB';
    }

    /**
     * Format teks tenggat waktu berakhir untuk tampilan.
     */
    public function formattedDueDate(): string
    {
        if (! $this->due_date) {
            return 'Tanpa Batas';
        }

        return $this->due_date->translatedFormat('d M Y, H:i').' WIB';
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(LmsAssignmentSubmission::class, 'assignment_id');
    }

    public function submissionFor(int $studentId): ?LmsAssignmentSubmission
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function isOverdue(): bool
    {
        return now()->isAfter($this->due_date);
    }
}
