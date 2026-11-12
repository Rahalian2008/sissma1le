<?php

namespace App\Models;

use App\Services\DisciplineAutomationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranTahfidzRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'juz',
        'surah_number',
        'surah_name',
        'ayat_range',
        'ayat_count',
        'makhraj',
        'tajwid',
        'kelancaran',
        'overall_grade',
        'evaluation_notes',
        'deposit_date',
        'deposit_time',
        'examiner_id',
        'examiner_name',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'juz' => 'integer',
            'surah_number' => 'integer',
            'ayat_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (QuranTahfidzRecord $record) {
            if (empty($record->class_id) && $record->student_id) {
                $record->class_id = $record->student?->class_id;
            }
            if (empty($record->deposit_date)) {
                $record->deposit_date = now()->toDateString();
            }
            if (empty($record->deposit_time)) {
                $record->deposit_time = now()->format('H:i:s');
            }
            if (empty($record->examiner_name)) {
                $record->examiner_name = $record->examiner?->name ?? 'Penguji Tahfidz';
            }
            if (empty($record->surah_name)) {
                $record->surah_name = 'Al-Fatihah';
            }
            if (empty($record->ayat_range)) {
                $record->ayat_range = '1-7';
            }
            if (empty($record->makhraj)) {
                $record->makhraj = 'Baik';
            }
            if (empty($record->tajwid)) {
                $record->tajwid = 'Baik';
            }
            if (empty($record->kelancaran)) {
                $record->kelancaran = 'Baik';
            }
            if (empty($record->overall_grade)) {
                $record->overall_grade = 'Lulus';
            }
        });

        static::saved(function (QuranTahfidzRecord $record) {
            if (($record->ayat_count ?? 0) >= 20) {
                app(DisciplineAutomationService::class)->recordTahfidzAchievement($record);
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    /**
     * Normalisasi tingkatan kelas (X, XI, XII)
     */
    public static function normalizeGrade(string|int|null $grade): ?string
    {
        if (empty($grade)) {
            return null;
        }

        $grade = strtoupper(trim((string) $grade));

        if (in_array($grade, ['X', '10', 'KELAS X', 'KELAS 10'])) {
            return 'X';
        }

        if (in_array($grade, ['XI', '11', 'KELAS XI', 'KELAS 11'])) {
            return 'XI';
        }

        if (in_array($grade, ['XII', '12', 'KELAS XII', 'KELAS 12'])) {
            return 'XII';
        }

        if (str_starts_with($grade, 'XII') || str_starts_with($grade, '12') || str_contains($grade, 'XII')) {
            return 'XII';
        }

        if (str_starts_with($grade, 'XI') || str_starts_with($grade, '11') || str_contains($grade, 'XI')) {
            return 'XI';
        }

        if (str_starts_with($grade, 'X') || str_starts_with($grade, '10') || str_contains($grade, 'KELAS X')) {
            return 'X';
        }

        return null;
    }

    /**
     * Daftar 37 Surat pada Juz 30 (An-Naba s.d. An-Nas) dengan pemetaan target kelas
     * - Kelas X   : 25 Surat (No. 90 s.d. 114)
     * - Kelas XI  : 7 Surat  (No. 83 s.d. 89)
     * - Kelas XII : 5 Surat  (No. 78 s.d. 82)
     */
    public static function getJuz30Surahs(): array
    {
        return [
            // Kelas XII (5 Surat)
            78 => ['number' => 78, 'name' => "An-Naba'", 'total_ayah' => 40, 'meaning' => 'Berita Besar', 'grade' => 'XII', 'grade_label' => 'Kelas XII'],
            79 => ['number' => 79, 'name' => "An-Nazi'at", 'total_ayah' => 46, 'meaning' => 'Malaikat yang Mencabut', 'grade' => 'XII', 'grade_label' => 'Kelas XII'],
            80 => ['number' => 80, 'name' => "'Abasa", 'total_ayah' => 42, 'meaning' => 'Ia Bermuka Masam', 'grade' => 'XII', 'grade_label' => 'Kelas XII'],
            81 => ['number' => 81, 'name' => 'At-Takwir', 'total_ayah' => 29, 'meaning' => 'Menggulung', 'grade' => 'XII', 'grade_label' => 'Kelas XII'],
            82 => ['number' => 82, 'name' => 'Al-Infitar', 'total_ayah' => 19, 'meaning' => 'Terbelah', 'grade' => 'XII', 'grade_label' => 'Kelas XII'],

            // Kelas XI (7 Surat)
            83 => ['number' => 83, 'name' => 'Al-Mutaffifin', 'total_ayah' => 36, 'meaning' => 'Orang-Orang Curang', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            84 => ['number' => 84, 'name' => 'Al-Inshiqaq', 'total_ayah' => 25, 'meaning' => 'Terbelah', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            85 => ['number' => 85, 'name' => 'Al-Buruj', 'total_ayah' => 22, 'meaning' => 'Gugusan Bintang', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            86 => ['number' => 86, 'name' => 'At-Tariq', 'total_ayah' => 17, 'meaning' => 'Yang Datang di Malam Hari', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            87 => ['number' => 87, 'name' => "Al-A'la", 'total_ayah' => 19, 'meaning' => 'Yang Paling Tinggi', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            88 => ['number' => 88, 'name' => 'Al-Ghasyiyah', 'total_ayah' => 26, 'meaning' => 'Hari Kiamat/Menghuru-harakan', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],
            89 => ['number' => 89, 'name' => 'Al-Fajr', 'total_ayah' => 30, 'meaning' => 'Fajar', 'grade' => 'XI', 'grade_label' => 'Kelas XI'],

            // Kelas X (25 Surat)
            90 => ['number' => 90, 'name' => 'Al-Balad', 'total_ayah' => 20, 'meaning' => 'Negeri', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            91 => ['number' => 91, 'name' => 'Asy-Syams', 'total_ayah' => 15, 'meaning' => 'Matahari', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            92 => ['number' => 92, 'name' => 'Al-Lail', 'total_ayah' => 21, 'meaning' => 'Malam', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            93 => ['number' => 93, 'name' => 'Ad-Duha', 'total_ayah' => 11, 'meaning' => 'Waktu Dhuha', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            94 => ['number' => 94, 'name' => 'Asy-Syarh / Al-Insyirah', 'total_ayah' => 8, 'meaning' => 'Kelapangan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            95 => ['number' => 95, 'name' => 'At-Tin', 'total_ayah' => 8, 'meaning' => 'Buah Tin', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            96 => ['number' => 96, 'name' => "Al-'Alaq", 'total_ayah' => 19, 'meaning' => 'Segumpal Darah', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            97 => ['number' => 97, 'name' => 'Al-Qadr', 'total_ayah' => 5, 'meaning' => 'Kemuliaan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            98 => ['number' => 98, 'name' => 'Al-Bayyinah', 'total_ayah' => 8, 'meaning' => 'Bukti Nyata', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            99 => ['number' => 99, 'name' => 'Az-Zalzalah', 'total_ayah' => 8, 'meaning' => 'Kegoncangan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            100 => ['number' => 100, 'name' => "Al-'Adiyat", 'total_ayah' => 11, 'meaning' => 'Kuda Perang yang Berlari Kencang', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            101 => ['number' => 101, 'name' => "Al-Qari'ah", 'total_ayah' => 11, 'meaning' => 'Hari Kiamat/Ketukan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            102 => ['number' => 102, 'name' => 'At-Takasur', 'total_ayah' => 8, 'meaning' => 'Bermegah-megahan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            103 => ['number' => 103, 'name' => "Al-'Asr", 'total_ayah' => 3, 'meaning' => 'Masa/Waktu', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            104 => ['number' => 104, 'name' => 'Al-Humazah', 'total_ayah' => 9, 'meaning' => 'Pengumpat', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            105 => ['number' => 105, 'name' => 'Al-Fil', 'total_ayah' => 5, 'meaning' => 'Gajah', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            106 => ['number' => 106, 'name' => 'Quraisy', 'total_ayah' => 4, 'meaning' => 'Suku Quraisy', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            107 => ['number' => 107, 'name' => "Al-Ma'un", 'total_ayah' => 7, 'meaning' => 'Barang-Barang yang Berguna', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            108 => ['number' => 108, 'name' => 'Al-Kautsar', 'total_ayah' => 3, 'meaning' => 'Nikmat yang Berlimpah', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            109 => ['number' => 109, 'name' => 'Al-Kafirun', 'total_ayah' => 6, 'meaning' => 'Orang-Orang Kafir', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            110 => ['number' => 110, 'name' => 'An-Nasr', 'total_ayah' => 3, 'meaning' => 'Pertolongan', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            111 => ['number' => 111, 'name' => 'Al-Lahab / Al-Masad', 'total_ayah' => 5, 'meaning' => 'Gejolak Api/Sabut', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            112 => ['number' => 112, 'name' => 'Al-Ikhlas', 'total_ayah' => 4, 'meaning' => 'Keesaan Allah', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            113 => ['number' => 113, 'name' => 'Al-Falaq', 'total_ayah' => 5, 'meaning' => 'Waktu Subuh', 'grade' => 'X', 'grade_label' => 'Kelas X'],
            114 => ['number' => 114, 'name' => 'An-Nas', 'total_ayah' => 6, 'meaning' => 'Manusia', 'grade' => 'X', 'grade_label' => 'Kelas X'],
        ];
    }

    /**
     * Dapatkan daftar surat Juz 30 terfilter berdasarkan kelas (X, XI, atau XII)
     */
    public static function getSurahsByGrade(?string $grade = null): array
    {
        $surahs = static::getJuz30Surahs();
        $norm = static::normalizeGrade($grade);

        if (! $norm) {
            return $surahs;
        }

        return array_filter($surahs, fn ($s) => ($s['grade'] ?? null) === $norm);
    }

    /**
     * Dapatkan daftar surat Juz 30 dikelompokkan berdasarkan tingkatan kelas
     */
    public static function getSurahsGroupedByGrade(): array
    {
        $surahs = static::getJuz30Surahs();
        $grouped = [
            'X' => ['label' => 'Kelas X (25 Surat)', 'surahs' => []],
            'XI' => ['label' => 'Kelas XI (7 Surat)', 'surahs' => []],
            'XII' => ['label' => 'Kelas XII (5 Surat)', 'surahs' => []],
        ];

        foreach ($surahs as $number => $surah) {
            $g = $surah['grade'] ?? 'X';
            $grouped[$g]['surahs'][$number] = $surah;
        }

        return $grouped;
    }

    /**
     * Parsing rentang teks ayat menjadi array nomor ayat
     */
    public static function parseAyatRange(?string $range, int $totalAyah = 40): array
    {
        if (empty($range)) {
            return [];
        }

        $range = trim($range);

        // Pola rentang: "10-12", "10 s.d. 12", "10 sampai 12", "10 s/d 12", "Ayat 10 - 12"
        if (preg_match('/(\d+)\s*(?:-|s\.d\.|s\/d|sampai|\.\.)\s*(\d+)/i', $range, $m)) {
            $start = min((int) $m[1], (int) $m[2]);
            $end = max((int) $m[1], (int) $m[2]);
            $start = max(1, $start);
            $end = min($totalAyah, $end);

            return range($start, $end);
        }

        // Pola kata kunci: Semua / Penuh / Selesai / Khatam
        if (preg_match('/(?:semua|penuh|selesai|khatam|akhir)/i', $range)) {
            return range(1, $totalAyah);
        }

        // Pola angka tunggal: "Ayat 5" atau "5"
        if (preg_match('/^\s*(?:ayat\s*)?(\d+)\s*$/i', $range, $m)) {
            $single = max(1, min($totalAyah, (int) $m[1]));

            return [$single];
        }

        return [];
    }

    /**
     * Memformat array nomor-nomor ayat menjadi rentang teks yang mudah dibaca
     * Contoh: [1,2,3, 10,11,12] -> "Ayat 1 s.d. 3, 10 s.d. 12"
     */
    public static function formatAyatRanges(array $numbers): string
    {
        if (empty($numbers)) {
            return '-';
        }

        $numbers = array_values(array_unique($numbers));
        sort($numbers);

        $ranges = [];
        $start = $numbers[0];
        $prev = $start;

        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] == $prev + 1) {
                $prev = $numbers[$i];
            } else {
                $ranges[] = ($start == $prev) ? (string) $start : "{$start} s.d. {$prev}";
                $start = $numbers[$i];
                $prev = $start;
            }
        }
        $ranges[] = ($start == $prev) ? (string) $start : "{$start} s.d. {$prev}";

        return 'Ayat '.implode(', ', $ranges);
    }

    /**
     * Analisis komprehensif progres cicilan hafalan per siswa berdasarkan pengelompokan tingkat kelas
     * (Kelas X: 25 Surat / 219 Ayat, Kelas XI: 7 Surat / 175 Ayat, Kelas XII: 5 Surat / 176 Ayat)
     */
    public static function getStudentJuz30Analysis(Student $student, ?string $targetGrade = null): array
    {
        $studentGrade = static::normalizeGrade($targetGrade ?? $student->getNormalizedGrade()) ?: 'X';
        $isAllRequested = strtolower((string) $targetGrade) === 'all';

        // Tampilkan surat-surat sesuai target tingkat kelas siswa (jangan menampilkan semua surat)
        $targetSurahs = $isAllRequested ? static::getJuz30Surahs() : static::getSurahsByGrade($studentGrade);

        $records = static::with('examiner')
            ->where('student_id', $student->id)
            ->orderBy('deposit_date')
            ->orderBy('deposit_time')
            ->get();

        $surahDetails = [];
        $completedSurahsList = [];
        $pendingSurahsList = [];

        $totalTargetAyat = 0;
        $totalCompletedAyat = 0;

        foreach ($targetSurahs as $num => $surah) {
            $totalAyah = (int) $surah['total_ayah'];
            $totalTargetAyat += $totalAyah;

            $surahRecords = $records->where('surah_number', $num);
            $passedRecords = $surahRecords->where('overall_grade', 'Lulus');

            // Himpunan ayat yang telah lulus disetorkan
            $completedVersesSet = [];
            foreach ($passedRecords as $rec) {
                $parsed = static::parseAyatRange($rec->ayat_range, $totalAyah);
                if (! empty($parsed)) {
                    foreach ($parsed as $v) {
                        $completedVersesSet[$v] = true;
                    }
                } elseif (($rec->ayat_count ?? 0) > 0) {
                    $count = min($totalAyah, (int) $rec->ayat_count);
                    for ($v = 1; $v <= $count; $v++) {
                        $completedVersesSet[$v] = true;
                    }
                }
            }

            $completedVerses = array_keys($completedVersesSet);
            sort($completedVerses);
            $completedCount = count($completedVerses);
            $totalCompletedAyat += $completedCount;

            $allVerses = range(1, $totalAyah);
            $pendingVerses = array_values(array_diff($allVerses, $completedVerses));
            $pendingCount = count($pendingVerses);

            $status = 'BELUM_DISETOR';
            if ($pendingCount === 0) {
                $status = 'SELESAI';
            } elseif ($completedCount > 0) {
                $status = 'SEDANG_DICICIL';
            }

            $detail = [
                'number' => $num,
                'name' => $surah['name'],
                'meaning' => $surah['meaning'] ?? '',
                'grade' => $surah['grade'] ?? $studentGrade,
                'total_ayah' => $totalAyah,
                'status' => $status,
                'completed_count' => $completedCount,
                'pending_count' => $pendingCount,
                'progress_percent' => $totalAyah > 0 ? round(($completedCount / $totalAyah) * 100, 1) : 0,
                'completed_verses_text' => $completedCount > 0 ? static::formatAyatRanges($completedVerses) : '-',
                'pending_verses_text' => $pendingCount > 0 ? static::formatAyatRanges($pendingVerses) : 'Tuntas (0 Ayat)',
                'deposit_count' => $surahRecords->count(),
                'latest_deposit' => $surahRecords->last(),
                'records' => $surahRecords,
            ];

            $surahDetails[$num] = $detail;

            if ($status === 'SELESAI') {
                $completedSurahsList[] = $detail;
            } else {
                $pendingSurahsList[] = $detail;
            }
        }

        $totalSurahs = count($targetSurahs);
        $totalCompletedSurahs = count($completedSurahsList);
        $totalPendingSurahs = count($pendingSurahsList);
        $totalInInstallmentSurahs = collect($pendingSurahsList)->where('status', 'SEDANG_DICICIL')->count();
        $totalUnstartedSurahs = collect($pendingSurahsList)->where('status', 'BELUM_DISETOR')->count();
        $totalPendingAyat = max(0, $totalTargetAyat - $totalCompletedAyat);
        $overallPercentage = $totalTargetAyat > 0 ? round(($totalCompletedAyat / $totalTargetAyat) * 100, 1) : 0;

        return [
            'student' => $student,
            'student_grade' => $studentGrade,
            'target_grade' => $isAllRequested ? 'ALL' : $studentGrade,
            'target_grade_label' => $isAllRequested ? 'Seluruh Juz 30' : 'Kelas '.$studentGrade,
            'is_all_requested' => $isAllRequested,
            'all_surahs' => $targetSurahs,
            'surah_details' => $surahDetails,
            'records' => $records,
            'total_surahs' => $totalSurahs,
            'total_completed_surahs' => $totalCompletedSurahs,
            'total_pending_surahs' => $totalPendingSurahs,
            'total_in_installment_surahs' => $totalInInstallmentSurahs,
            'total_unstarted_surahs' => $totalUnstartedSurahs,
            'total_juz30_ayat' => $totalTargetAyat,
            'total_target_ayat' => $totalTargetAyat,
            'total_completed_ayat' => $totalCompletedAyat,
            'total_pending_ayat' => $totalPendingAyat,
            'overall_percentage' => $overallPercentage,
            'completed_surahs_list' => $completedSurahsList,
            'pending_surahs_list' => $pendingSurahsList,
        ];
    }
}
