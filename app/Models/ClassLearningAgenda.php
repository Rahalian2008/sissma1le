<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassLearningAgenda extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (ClassLearningAgenda $agenda) {
            if (empty($agenda->rombel)) {
                $agenda->rombel = 'Kelas';
            }
            if (empty($agenda->date)) {
                $agenda->date = now()->toDateString();
            }
            if (empty($agenda->time_slot)) {
                $agenda->time_slot = 'Jam Ke-1';
            }
            if (empty($agenda->subject)) {
                $agenda->subject = 'Mata Pelajaran';
            }
            if (empty($agenda->teacher_name)) {
                $agenda->teacher_name = $agenda->teacher?->name ?? 'Guru Mata Pelajaran';
            }
            if (empty($agenda->learning_topic)) {
                $agenda->learning_topic = 'Kegiatan Pembelajaran';
            }
        });
    }

    protected $casts = [
        'date' => 'date',
        'teacher_signed' => 'boolean',
        'students_present' => 'integer',
        'students_sick' => 'integer',
        'students_permitted' => 'integer',
        'students_absent' => 'integer',
    ];

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_employee_id');
    }
}
