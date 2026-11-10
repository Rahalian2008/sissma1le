<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'rank_tmt' => 'date',
            'position_tmt' => 'date',
            'last_kp_tmt' => 'date',
            'next_kp_tmt' => 'date',
            'last_kgb_tmt' => 'date',
            'next_kgb_tmt' => 'date',
            'pension_tmt' => 'date',
            'pppk_contract_start' => 'date',
            'pppk_contract_end' => 'date',
            'last_kgb_salary' => 'decimal:2',
            'is_certified' => 'boolean',
            'is_active' => 'boolean',
            'years_of_service_years' => 'integer',
            'years_of_service_months' => 'integer',
            'leave_balance' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<EmployeeHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class);
    }

    /**
     * @return HasMany<EmployeeHistory, $this>
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class)
            ->where('history_type', 'Diklat/Pelatihan')
            ->orderBy('start_date', 'desc');
    }

    /**
     * @return HasMany<EmployeeLeave, $this>
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    /**
     * @return HasMany<OrganizationStructure, $this>
     */
    public function organizationStructures(): HasMany
    {
        return $this->hasMany(OrganizationStructure::class);
    }

    public function getNameAttribute(?string $value): ?string
    {
        return $value ? mb_strtoupper($value) : null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['name'] = $value ? mb_strtoupper(trim($value)) : null;
    }

    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix).' ' : '';
        $suffix = $this->title_suffix ? ', '.trim($this->title_suffix) : '';

        return $prefix.mb_strtoupper($this->name).$suffix;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            if (str_starts_with($this->photo_path, 'data:image/') || str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
                return $this->photo_path;
            }
            if (Storage::disk('public')->exists($this->photo_path)) {
                return asset('storage/'.$this->photo_path);
            }
            if (file_exists(public_path($this->photo_path))) {
                return asset($this->photo_path);
            }
        }

        // SVG Avatar Fallback with initials
        $initials = strtoupper(substr($this->name, 0, 2));

        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'><rect width='100' height='100' rx='50' fill='%230284c7'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' fill='%23ffffff' font-family='sans-serif' font-size='38' font-weight='bold'>{$initials}</text></svg>";
    }

    public function isPns(): bool
    {
        return strtoupper($this->employment_status) === 'PNS';
    }

    public function isPppk(): bool
    {
        return strtoupper($this->employment_status) === 'PPPK';
    }

    public function isHonorer(): bool
    {
        return in_array(strtoupper($this->employment_status), ['GTT', 'PTT', 'HONORER']);
    }

    public function isKpDue(int $monthsThreshold = 6): bool
    {
        if (! $this->next_kp_tmt) {
            return false;
        }

        return $this->next_kp_tmt->isPast() || $this->next_kp_tmt->diffInMonths(now()) <= $monthsThreshold;
    }

    public function isKgbDue(int $monthsThreshold = 3): bool
    {
        if (! $this->next_kgb_tmt) {
            return false;
        }

        return $this->next_kgb_tmt->isPast() || $this->next_kgb_tmt->diffInMonths(now()) <= $monthsThreshold;
    }

    public function isPppkContractExpiring(int $monthsThreshold = 6): bool
    {
        if (! $this->pppk_contract_end) {
            return false;
        }

        return $this->pppk_contract_end->isPast() || $this->pppk_contract_end->diffInMonths(now()) <= $monthsThreshold;
    }

    public function usedLeaveDays(?int $year = null): int
    {
        $year = $year ?? (int) date('Y');

        return (int) $this->leaves()
            ->where('status', 'Disetujui')
            ->whereYear('start_date', $year)
            ->sum('days_count');
    }

    public function remainingLeaveBalance(?int $year = null): int
    {
        $maxQuota = $this->leave_balance > 0 ? $this->leave_balance : 12;
        $remaining = $maxQuota - $this->usedLeaveDays($year);

        return max(0, $remaining);
    }

    public function getRetirementAgeAttribute(): int
    {
        return $this->employment_type === 'Guru' ? 60 : 58;
    }

    public function getCalculatedPensionTmtAttribute(): ?Carbon
    {
        if ($this->pension_tmt) {
            return $this->pension_tmt;
        }

        if (! $this->birth_date) {
            return null;
        }

        return $this->birth_date->copy()->addYears($this->retirement_age)->addMonth()->startOfMonth();
    }

    public function getRemainingPensionMonthsAttribute(): ?int
    {
        if (! $this->calculated_pension_tmt) {
            return null;
        }

        return (int) now()->diffInMonths($this->calculated_pension_tmt, false);
    }

    public function isPensionDue(int $monthsThreshold = 12): bool
    {
        $months = $this->remaining_pension_months;

        return $months !== null && $months <= $monthsThreshold && $months > 0;
    }

    public function isPensionMpp(int $monthsThreshold = 24): bool
    {
        $months = $this->remaining_pension_months;

        return $months !== null && $months <= $monthsThreshold && $months > 12;
    }

    /**
     * @param  Builder<Employee>  $query
     * @return Builder<Employee>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Employee>  $query
     * @return Builder<Employee>
     */
    public function scopeTeachers(Builder $query): Builder
    {
        return $query->where('employment_type', 'Guru');
    }

    /**
     * @param  Builder<Employee>  $query
     * @return Builder<Employee>
     */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('employment_type', 'Tenaga Kependidikan');
    }

    /**
     * Cari Wali Kelas untuk suatu rombongan belajar
     */
    public static function getWaliKelasForRombel(?string $rombel): ?self
    {
        if (empty($rombel) || ! Schema::hasTable('employees')) {
            return null;
        }

        $trimmed = trim($rombel);

        return static::where('homeroom_rombel', $trimmed)
            ->orWhere('position', 'like', "%Wali Kelas {$trimmed}%")
            ->orWhere('position', 'like', "%{$trimmed}%")
            ->orWhere('additional_tasks', 'like', "%{$trimmed}%")
            ->first();
    }
}
