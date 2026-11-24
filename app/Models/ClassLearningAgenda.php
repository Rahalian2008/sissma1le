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

    /**
     * Sinkronisasi data satu catatan Jurnal Mengajar Guru (LMS) ke Agenda Pembelajaran Kelas
     */
    public static function syncFromTeachingJournal(TeachingJournal $journal): ?self
    {
        $journal->loadMissing(['courseClass.schoolClass', 'courseClass.subject', 'teacher']);
        $rombel = $journal->courseClass?->schoolClass?->name;
        if (! $rombel) {
            return null;
        }

        $subjectName = $journal->courseClass?->subject?->name ?? 'Mata Pelajaran';
        $timeSlot = $journal->time_slot ?: ('Jam Ke-' . ($journal->meeting_number ?: '1'));
        $teacherName = $journal->teacher?->name ?? 'Guru Mata Pelajaran';
        $employee = Employee::where('nip', $journal->teacher?->nip)->first()
            ?? Employee::where('name', 'like', '%' . $journal->teacher?->name . '%')->first();

        $summary = $journal->student_attendance_summary ?? [];
        $present = (int) ($summary['hadir'] ?? 0);
        $sick = (int) ($summary['sakit'] ?? 0);
        $permitted = (int) ($summary['izin'] ?? 0);
        $absent = (int) ($summary['alfa'] ?? 0);

        return static::updateOrCreate(
            [
                'rombel' => $rombel,
                'date' => $journal->date ? $journal->date->toDateString() : now()->toDateString(),
                'subject' => $subjectName,
                'time_slot' => $timeSlot,
            ],
            [
                'academic_year_id' => $journal->courseClass?->academic_year_id,
                'teacher_employee_id' => $employee?->id,
                'teacher_name' => $teacherName,
                'learning_topic' => $journal->topic ?: 'Kegiatan Pembelajaran',
                'activities_summary' => $journal->teaching_activities,
                'students_present' => $present,
                'students_sick' => $sick,
                'students_permitted' => $permitted,
                'students_absent' => $absent,
                'class_notes' => $journal->classroom_dynamics_notes,
                'teacher_signed' => true,
            ]
        );
    }

    /**
     * Sinkronisasi seluruh Jurnal Mengajar Guru (LMS) ke Agenda Pembelajaran Kelas (berdasarkan rombel tertentu atau seluruhnya)
     */
    public static function syncFromTeachingJournals(?string $rombel = null): int
    {
        if (! class_exists(TeachingJournal::class) || ! \Illuminate\Support\Facades\Schema::hasTable('teaching_journals')) {
            return 0;
        }

        $query = TeachingJournal::with(['courseClass.schoolClass', 'courseClass.subject', 'teacher']);

        if ($rombel) {
            $query->whereHas('courseClass.schoolClass', function ($q) use ($rombel) {
                $q->where('name', $rombel);
            });
        }

        $synced = 0;
        foreach ($query->get() as $journal) {
            if (static::syncFromTeachingJournal($journal)) {
                $synced++;
            }
        }

        return $synced;
    }
}
