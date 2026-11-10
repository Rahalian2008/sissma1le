<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasSetting extends Model
{
    use HasFactory;

    protected $table = 'kurnas_settings';

    protected $fillable = [
        'academic_year_id',
        'semester',
        'is_active',
        'is_locked',
        'report_date',
        'report_place',
        'headmaster_name',
        'headmaster_nip',
        'headmaster_signature',
        'school_stamp',
        'school_logo',
        'provincial_logo',
        'combine_science_social',
        'is_published_to_students',
        'show_signature_image',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'combine_science_social' => 'boolean',
            'is_published_to_students' => 'boolean',
            'show_signature_image' => 'boolean',
            'report_date' => 'date',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
