<?php

namespace App\Services;

use App\Models\SchoolIdentity;
use Carbon\Carbon;
use DateTimeInterface;

class LetterNumberService
{
    /**
     * Dapatkan Kode Pokok Urusan berdasarkan kode klasifikasi kearsipan SIKD Jabar / Pergub 14/2018.
     */
    public static function getPrefixCode(string $classificationCode): string
    {
        $code = strtoupper(trim($classificationCode));

        if (str_starts_with($code, 'KPG.14')) {
            return '822.2'; // Kenaikan Gaji Berkala (KGB)
        }

        if (str_starts_with($code, 'KPG')) {
            return '800'; // Kepegawaian GTK
        }

        if (str_starts_with($code, 'PK') || str_starts_with($code, 'PS') || str_starts_with($code, 'KO') || str_starts_with($code, 'KS')) {
            return '421.3'; // Pendidikan Menengah / Siswa / Kurikulum / Sekolah
        }

        if (str_starts_with($code, 'KU')) {
            return '900'; // Keuangan, BOS & Realisasi Anggaran
        }

        if (str_starts_with($code, 'PL') || str_starts_with($code, 'RT') || str_starts_with($code, 'TU') || str_starts_with($code, 'HM')) {
            return '000'; // Umum, Tata Usaha, Sarpras & Rumah Tangga
        }

        if (str_starts_with($code, '800') || str_starts_with($code, '822')) {
            return '800';
        }

        if (str_starts_with($code, '400') || str_starts_with($code, '421')) {
            return '421.3';
        }

        if (str_starts_with($code, '900')) {
            return '900';
        }

        return '421.3';
    }

    /**
     * Konversi bulan angka (1-12) ke angka Romawi.
     */
    public static function toRomanMonth(int|string|null $month = null): string
    {
        $m = (int) ($month ?: date('n'));
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$m] ?? 'I';
    }

    /**
     * Konversi bilangan bulat (termasuk bulan 1-12) ke angka Romawi.
     */
    public static function toRoman(int|string|null $number = null): string
    {
        return static::toRomanMonth($number);
    }

    /**
     * Format nomor surat dinas resmi SMA sesuai standar Pergub Jabar No. 14 Th 2018 & SIKD Jabar:
     * Rumus: [Kode Pokok] / [Nomor Urut Verbal] / [Kode Klasifikasi] - [Nama Sekolah] / [Bulan Romawi] / [Tahun]
     */
    public static function formatNumber(
        int|string $verbalNumber,
        string $classificationCode = 'PK.03.03.01',
        ?string $schoolName = null,
        ?DateTimeInterface $date = null
    ): string {
        $school = $schoolName ?: (SchoolIdentity::current()?->name ?? 'SMAN 1 Lengkong');
        $dateObj = $date ? Carbon::instance($date) : now();

        $prefix = self::getPrefixCode($classificationCode);
        $paddedVerbal = str_pad((string) $verbalNumber, 3, '0', STR_PAD_LEFT);
        $romanMonth = self::toRomanMonth($dateObj->month);
        $year = $dateObj->year;

        return "{$prefix} / {$paddedVerbal} / {$classificationCode} - {$school} / {$romanMonth} / {$year}";
    }

    /**
     * Contoh standar resmi yang tercantum pada dokumen panduan.
     *
     * @return array<int, array<string, string>>
     */
    public static function getOfficialExamples(?string $schoolName = null): array
    {
        $school = $schoolName ?: (SchoolIdentity::current()?->name ?? 'SMAN 1 Lengkong');
        $year = date('Y');

        return [
            [
                'title' => 'Surat Penugasan Guru (Bintek / MGMP / Pengawasan)',
                'code' => 'KPG.03.01.01',
                'number' => "800 / 084 / KPG.03.01.01 - {$school} / IX / {$year}",
                'category' => 'Kepegawaian (KPG)',
            ],
            [
                'title' => 'Surat Keterangan Aktif Siswa / Mutasi Siswa Keluar',
                'code' => 'PK.11.01.01',
                'number' => "421.3 / 195 / PK.11.01.01 - {$school} / X / {$year}",
                'category' => 'Pendidikan (PK)',
            ],
            [
                'title' => 'Surat Pemberitahuan Asesmen Sumatif Akhir Semester (SAS)',
                'code' => 'PK.07.01',
                'number' => "421.3 / 210 / PK.07.01 - {$school} / XI / {$year}",
                'category' => 'Asesmen & Mutu (PK)',
            ],
            [
                'title' => 'Surat Usulan Kenaikan Gaji Berkala (KGB) Guru/Tendik',
                'code' => 'KPG.14',
                'number' => "822.2 / 045 / KPG.14 - {$school} / XII / {$year}",
                'category' => 'Kepegawaian (KPG)',
            ],
            [
                'title' => 'Laporan Pertanggungjawaban (SPJ) Dana BOS Sekolah',
                'code' => 'KU.06.09',
                'number' => "900 / 312 / KU.06.09 - {$school} / XII / {$year}",
                'category' => 'Keuangan BOS (KU)',
            ],
        ];
    }
}
