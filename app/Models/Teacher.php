<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'nik',
        'name',
        'title_prefix',
        'title_suffix',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'address',
        'title',
        'position',
        'employment_type',
        'employment_status',
        'rank_group',
        'rank_tmt',
        'position_tmt',
        'years_of_service_years',
        'years_of_service_months',
        'education_level',
        'education_institution',
        'education_major',
        'education_grad_year',
        'teaching_subject',
        'additional_tasks',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'birth_date' => 'date',
            'rank_tmt' => 'date',
            'position_tmt' => 'date',
            'years_of_service_years' => 'integer',
            'years_of_service_months' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTeachers($query)
    {
        return $query->where(function ($q) {
            $q->where('employment_type', 'Guru')
                ->orWhereNull('employment_type');
        });
    }

    public function scopeStaff($query)
    {
        return $query->where('employment_type', 'Tenaga Kependidikan');
    }

    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix).' ' : '';
        $suffix = $this->title_suffix ? ', '.trim($this->title_suffix) : '';
        $clean = trim($this->name);

        return $prefix.$clean.$suffix;
    }

    public function homeroomClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function extracurriculars(): BelongsToMany
    {
        return $this->belongsToMany(Extracurricular::class, 'extracurricular_teacher', 'teacher_id', 'extracurricular_id');
    }

    public function ledExtracurriculars(): HasMany
    {
        return $this->hasMany(Extracurricular::class, 'teacher_id');
    }

    /**
     * Get all extracurriculars where teacher is primary leader or co-pembina.
     */
    public function getAllExtracurricularsAttribute(): Collection
    {
        $led = $this->ledExtracurriculars ?? collect();
        $assigned = $this->extracurriculars ?? collect();

        return $led->merge($assigned)->unique('id')->values();
    }

    /**
     * @return array{name: string, nip: string, user: ?User}
     */
    public static function getHeadmaster(): array
    {
        $settingName = SchoolSetting::where('key', 'headmaster_name')->value('value');
        $settingNip = SchoolSetting::where('key', 'headmaster_nip')->value('value');

        $user = User::where('role', 'kepala_sekolah')
            ->orWhereJsonContains('roles', 'kepala_sekolah')
            ->first();

        return [
            'name' => $settingName ?: ($user?->name ?? 'Drs. H. Ahmad Sudrajat, M.Pd.'),
            'nip' => $settingNip ?: ($user?->username ?? ($user?->teacher?->nip ?? '196803151994031004')),
            'user' => $user,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User}
     */
    public static function getWakasekKesiswaan(): array
    {
        $user = User::where('role', 'wakasek_kesiswaan')
            ->orWhereJsonContains('roles', 'wakasek_kesiswaan')
            ->orWhere('role', 'kesiswaan')
            ->orWhereJsonContains('roles', 'kesiswaan')
            ->first();

        return [
            'name' => $user?->name ?? 'Winda Dwi Atyani, S.Pd., M.Pd.',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '197911162008012007')),
            'user' => $user,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User}
     */
    public static function getWakasekKurikulum(): array
    {
        $user = User::where('role', 'wakasek_kurikulum')
            ->orWhereJsonContains('roles', 'wakasek_kurikulum')
            ->first();

        return [
            'name' => $user?->name ?? 'Anne Listiyani, S.Si., M.Pd.',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '198004172010012014')),
            'user' => $user,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User}
     */
    public static function getWakasekHumas(): array
    {
        $user = User::where('role', 'wakasek_humas')
            ->orWhereJsonContains('roles', 'wakasek_humas')
            ->first();

        return [
            'name' => $user?->name ?? 'Wakasek Humas SMAN 1 Lengkong',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '-')),
            'user' => $user,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User}
     */
    public static function getWakasekSarpras(): array
    {
        $user = User::where('role', 'wakasek_sarpras')
            ->orWhereJsonContains('roles', 'wakasek_sarpras')
            ->first();

        return [
            'name' => $user?->name ?? 'Undang Mulyana, S.Pd.I., M.Pd.',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '196806122000101001')),
            'user' => $user,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User, teacher: ?self}
     */
    public static function getBkCoordinator(): array
    {
        $user = User::where('role', 'koordinator_bk')
            ->orWhereJsonContains('roles', 'koordinator_bk')
            ->first();

        return [
            'name' => $user?->name ?? 'Koordinator BP/BK SMAN 1 Lengkong',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '-')),
            'user' => $user,
            'teacher' => $user?->teacher,
        ];
    }

    /**
     * @return array{name: string, nip: string, user: ?User, teacher: ?self}
     */
    public static function getReligionCoordinator(): array
    {
        $user = User::where('role', 'koordinator_keagamaan')
            ->orWhereJsonContains('roles', 'koordinator_keagamaan')
            ->first();

        return [
            'name' => $user?->name ?? 'Koordinator Keagamaan SMAN 1 Lengkong',
            'nip' => $user?->nip ?? ($user?->username ?? ($user?->teacher?->nip ?? '-')),
            'user' => $user,
            'teacher' => $user?->teacher,
        ];
    }
}
