<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurnasHomeroomRecord extends Model
{
    use HasFactory;

    protected $table = 'kurnas_homeroom_records';

    protected $fillable = [
        'school_class_id',
        'student_id',
        'academic_year_id',
        'semester',
        'sick',
        'permission',
        'absent',
        'note',
        'status_promotion',
        'promotion_target_grade',
    ];

    protected function casts(): array
    {
        return [
            'sick' => 'integer',
            'permission' => 'integer',
            'absent' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
