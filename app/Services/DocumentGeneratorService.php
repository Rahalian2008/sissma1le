<?php

namespace App\Services;

use App\Models\SchoolIdentity;

class DocumentGeneratorService
{
    /**
     * Parse template text and replace variables.
     *
     * @param  array<string, string|int|null>  $variables
     */
    public static function parse(string $templateContent, array $variables = []): string
    {
        $school = SchoolIdentity::current();

        $defaultVariables = [
            'nama_sekolah' => $school->name,
            'npsn' => $school->npsn,
            'alamat_sekolah' => $school->address,
            'kota_sekolah' => $school->city ?? 'Bandung',
            'kabupaten_sekolah' => $school->regency ?? $school->city ?? 'Bandung',
            'provinsi_sekolah' => $school->province ?? 'Jawa Barat',
            'telepon_sekolah' => $school->phone ?? '-',
            'email_sekolah' => $school->email ?? '-',
            'website_sekolah' => $school->website ?? '-',
            'kepala_sekolah' => $school->headmaster_name,
            'nip_kepala_sekolah' => $school->headmaster_nip,
            'tanggal_hari_ini' => now()->translatedFormat('d F Y'),
            'tahun_sekarang' => date('Y'),
        ];

        $merged = array_merge($defaultVariables, $variables);

        $parsed = $templateContent;
        foreach ($merged as $key => $value) {
            $parsed = str_replace([
                '{{'.$key.'}}',
                '{{ '.$key.' }}',
                '{{'.strtoupper($key).'}}',
                '{{ '.strtoupper($key).' }}',
            ], (string) ($value ?? '-'), $parsed);
        }

        return $parsed;
    }
}
