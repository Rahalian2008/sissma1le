<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkHomeVisit extends Model
{
    use HasFactory;

    protected $table = 'bk_home_visits';

    protected $fillable = [
        'student_id',
        'counselor_id',
        'homeroom_id',
        'visit_code',
        'letter_number',
        'visit_date',
        'address_visited',
        'parent_name',
        'case_background',
        'family_condition',
        'parent_commitment',
        'visit_result',
        'status',
        'documentation_path',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function homeroom(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_id');
    }
}
