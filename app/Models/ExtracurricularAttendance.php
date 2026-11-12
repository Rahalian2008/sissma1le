<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'extracurricular_id',
        'student_id',
        'class_id',
        'date',
        'time',
        'status',
        'method',
        'device_info',
        'latitude',
        'longitude',
        'selfie_path',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ExtracurricularAttendance $model) {
            if (empty($model->date)) {
                $model->date = now()->toDateString();
            }
            if (empty($model->time)) {
                $model->time = now()->format('H:i:s');
            }
            if (empty($model->class_id) && $model->student_id) {
                $model->class_id = $model->student?->class_id;
            }
            if (empty($model->status)) {
                $model->status = 'HADIR';
            }
            if (empty($model->method)) {
                $model->method = 'MANUAL';
            }
        });
    }

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
