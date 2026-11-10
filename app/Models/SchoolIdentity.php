<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SchoolIdentity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function current(): self
    {
        $school = static::first();

        if (! $school) {
            $data = [
                'name' => 'SMA Negeri 1 Lengkong',
                'npsn' => '20202265',
                'nss' => '301020304050',
                'status' => 'Negeri',
                'accreditation' => 'A',
                'address' => 'Jl. Raya Lengkong No. 1, Desa Lengkong',
                'district' => 'Kecamatan Lengkong',
                'city' => 'Kabupaten Sukabumi',
                'province' => 'Jawa Barat',
                'postal_code' => '43174',
                'phone' => '(0266) 6491001',
                'email' => 'info@sman1lengkong.sch.id',
                'website' => 'https://sman1lengkong.sch.id',
                'headmaster_name' => 'PIAN SOPIAN, S.PD',
                'headmaster_nip' => '198009132008011005',
                'committee_head' => 'ADE',
                'operator_name' => 'AHMAD RIFA\'I, S.KOM.',
                'treasurer_name' => 'RIYAN',
            ];

            if (Schema::hasColumn('school_identities', 'branch_office')) {
                $data['branch_office'] = 'Cabang Dinas Pendidikan Wilayah V';
            }

            $school = static::create($data);
        }

        return $school;
    }

    public function getHeadmasterNameAttribute(?string $value): ?string
    {
        return $value ? mb_strtoupper($value) : null;
    }

    public function setHeadmasterNameAttribute(?string $value): void
    {
        $this->attributes['headmaster_name'] = $value ? mb_strtoupper(trim($value)) : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->has_logo) {
            return asset('storage/'.ltrim($this->logo_path, '/'));
        }

        return null;
    }

    public function getHeaderImageUrlAttribute(): ?string
    {
        if ($this->has_header_image) {
            return asset('storage/'.ltrim($this->header_image_path, '/'));
        }

        return null;
    }

    public function getHasLogoAttribute(): bool
    {
        if (! empty($this->logo_path)) {
            return Storage::disk('public')->exists($this->logo_path) || file_exists(public_path('storage/'.ltrim($this->logo_path, '/')));
        }

        return false;
    }

    public function getHasHeaderImageAttribute(): bool
    {
        if (! empty($this->header_image_path)) {
            return Storage::disk('public')->exists($this->header_image_path) || file_exists(public_path('storage/'.ltrim($this->header_image_path, '/')));
        }

        return false;
    }

    public function getPrincipalNameAttribute(): string
    {
        return $this->headmaster_name ?? 'Pian Sopian, S.Pd';
    }

    public function getPrincipalNipAttribute(): string
    {
        return $this->headmaster_nip ?? '198009132008011005';
    }

    public function getCityNameAttribute(): string
    {
        return $this->city ?? 'Kabupaten Sukabumi';
    }

    public function getBranchOfficeNameAttribute(): string
    {
        return $this->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V';
    }
}
