<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'member_type',
        'user_id',
        'student_id',
        'teacher_id',
        'name',
        'identity_number',
        'phone',
        'email',
        'address',
        'joined_at',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Generate unique member code guaranteed not to collide with any existing record.
     */
    public static function generateUniqueCode(string $type, int|string|null $preferredId = null): string
    {
        $prefix = match ($type) {
            'siswa' => 'MBR-S-',
            'guru' => 'MBR-G-',
            default => 'MBR-U-',
        };

        $digits = ($type === 'siswa') ? 5 : 4;

        if ($preferredId && is_numeric($preferredId) && (int) $preferredId > 0) {
            $candidate = $prefix.str_pad((string) $preferredId, $digits, '0', STR_PAD_LEFT);
            if (! static::where('member_code', $candidate)->exists()) {
                return $candidate;
            }
        }

        $maxId = (int) (static::max('id') ?? 0);
        $counter = max($maxId + 1, (int) $preferredId + 1);

        do {
            $candidate = $prefix.str_pad((string) $counter, $digits, '0', STR_PAD_LEFT);
            $counter++;
        } while (static::where('member_code', $candidate)->exists());

        return $candidate;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'member_id');
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'member_id')->whereIn('status', ['DIPINJAM', 'TERLAMBAT']);
    }

    public function getMemberIdentityAttribute(): string
    {
        if ($this->student && $this->student->nisn) {
            return 'NISN: '.$this->student->nisn;
        }

        if ($this->teacher && $this->teacher->nip) {
            return 'NIP: '.$this->teacher->nip;
        }

        return $this->identity_number ? 'Identitas: '.$this->identity_number : 'Pengunjung Umum';
    }

    public function getMemberClassOrTitleAttribute(): string
    {
        if ($this->student && $this->student->schoolClass) {
            return 'Kelas '.$this->student->schoolClass->name;
        }

        if ($this->teacher) {
            return $this->teacher->title ?: 'Guru / Pegawai';
        }

        return 'Umum / Tamu Luar';
    }
}
