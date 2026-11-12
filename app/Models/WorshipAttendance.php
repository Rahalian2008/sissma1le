<?php

namespace App\Models;

use App\Services\DisciplineAutomationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorshipAttendance extends Model
{
    use HasFactory;

    public const ACTIVITY_DHUHA = 'DHUHA';

    public const ACTIVITY_DZUHUR_JUMAT = 'DZUHUR_JUMAT';

    public const ACTIVITY_KEPUTRIAN = 'KEPUTRIAN';

    protected $fillable = [
        'student_id',
        'class_id',
        'activity_type',
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
        static::creating(function (WorshipAttendance $worship) {
            if (empty($worship->class_id) && $worship->student_id) {
                $student = Student::find($worship->student_id);
                if ($student) {
                    $worship->class_id = $student->class_id;
                }
            }
            if (empty($worship->time)) {
                $worship->time = now()->format('H:i:s');
            }
            if (empty($worship->status)) {
                $worship->status = 'HADIR';
            }
            if (empty($worship->method)) {
                $worship->method = 'MANUAL';
            }
        });

        static::saved(function (WorshipAttendance $worship) {
            if ($worship->status === 'HADIR') {
                app(DisciplineAutomationService::class)->recordWorshipAchievement($worship);
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

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Label resmi kegiatan
     */
    public static function getActivityTitle(string $type): string
    {
        return match ($type) {
            self::ACTIVITY_DHUHA => 'Presensi Shalat Dhuha Bersama',
            self::ACTIVITY_DZUHUR_JUMAT => "Presensi Shalat Dzuhur/Jum'at Berjamaah",
            self::ACTIVITY_KEPUTRIAN => 'Presensi Kegiatan Keputrian',
            default => 'Presensi Keagamaan',
        };
    }

    /**
     * Gender yang diperbolehkan ('L', 'P', atau 'ALL')
     *
     * Aturan Bisnis:
     * - Dhuha: Semua Siswa (ALL)
     * - Keputrian: Siswi Perempuan (P) khusus hari Jum'at
     * - Dzuhur/Jum'at:
     *   * Shalat Dzuhur/Jum'at: Berlaku untuk Putra & Putri (Laki-Laki & Perempuan)
     *   * Shalat Dhuha: Berlaku untuk Putra & Putri (Laki-Laki & Perempuan)
     *   * Keputrian: Khusus Putri (Perempuan)
     */
    public static function getAllowedGender(string $type, \DateTimeInterface|string|null $date = null): string
    {
        return match ($type) {
            self::ACTIVITY_DHUHA => 'ALL',
            self::ACTIVITY_DZUHUR_JUMAT => 'ALL',
            self::ACTIVITY_KEPUTRIAN => 'P',
            default => 'ALL',
        };
    }

    /**
     * Parse hari aktif dari SchoolSetting (bisa comma-separated, array, atau JSON)
     *
     * @return array<string>
     */
    public static function parseDays(mixed $value, array $default = []): array
    {
        if (empty($value)) {
            return $default;
        }

        if (is_array($value)) {
            $days = array_values(array_filter(array_map('trim', $value)));

            return ! empty($days) ? array_map('strtolower', $days) : $default;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $days = array_values(array_filter(array_map('trim', $decoded)));

                return ! empty($days) ? array_map('strtolower', $days) : $default;
            }

            $exploded = array_values(array_filter(array_map('trim', explode(',', $value))));

            return ! empty($exploded) ? array_map('strtolower', $exploded) : $default;
        }

        return $default;
    }

    /**
     * Konversi daftar string nama hari ke integer Carbon DayOfWeek (0: Sunday, 1: Monday, s.d. 6: Saturday)
     *
     * @param  array<string>  $dayKeys
     * @return array<int>
     */
    public static function dayKeysToCarbonDays(array $dayKeys): array
    {
        $map = [
            'minggu' => Carbon::SUNDAY,
            'ahad' => Carbon::SUNDAY,
            'senin' => Carbon::MONDAY,
            'selasa' => Carbon::TUESDAY,
            'rabu' => Carbon::WEDNESDAY,
            'kamis' => Carbon::THURSDAY,
            'jumat' => Carbon::FRIDAY,
            "jum'at" => Carbon::FRIDAY,
            'sabtu' => Carbon::SATURDAY,
        ];

        $carbonDays = [];
        foreach ($dayKeys as $k) {
            $clean = strtolower(trim($k));
            if (isset($map[$clean])) {
                $carbonDays[] = $map[$clean];
            }
        }

        return array_values(array_unique($carbonDays));
    }

    /**
     * Format label hari ramah pengguna (Indonesian)
     *
     * @param  array<string>  $dayKeys
     */
    public static function formatDaysLabel(array $dayKeys): string
    {
        $labels = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => "Jum'at",
            "jum'at" => "Jum'at",
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
        ];

        $order = [
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            "jum'at" => 5,
            'sabtu' => 6,
            'minggu' => 7,
        ];

        $cleaned = [];
        foreach ($dayKeys as $dk) {
            $clean = strtolower(trim($dk));
            if (isset($labels[$clean])) {
                $cleaned[$clean] = $labels[$clean];
            }
        }

        if (empty($cleaned)) {
            return 'Tidak Ada Hari Aktif';
        }

        uksort($cleaned, fn ($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));
        $formatted = array_values($cleaned);

        if (count($formatted) === 1) {
            return $formatted[0];
        }

        if (count($formatted) === 2) {
            return $formatted[0].' & '.$formatted[1];
        }

        if ($formatted === ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at"]) {
            return "Senin - Jum'at";
        }

        if ($formatted === ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu']) {
            return 'Senin - Sabtu';
        }

        if (count($formatted) === 7) {
            return 'Setiap Hari (Senin - Minggu)';
        }

        $last = array_pop($formatted);

        return implode(', ', $formatted).' & '.$last;
    }

    /**
     * Konfigurasi jadwal dan aturan hari (Wajib mengambil dari Master Data / SchoolSetting)
     */
    public static function getScheduleConfig(string $type, \DateTimeInterface|string|null $date = null): array
    {
        $allowedGender = self::getAllowedGender($type, $date);

        return match ($type) {
            self::ACTIVITY_DHUHA => (function () {
                $dayKeys = self::parseDays(SchoolSetting::get('worship_dhuha_days', 'kamis,jumat'), ['kamis', 'jumat']);
                $allowedDays = self::dayKeysToCarbonDays($dayKeys);
                if (empty($allowedDays)) {
                    $allowedDays = [Carbon::THURSDAY, Carbon::FRIDAY];
                }

                return [
                    'type' => self::ACTIVITY_DHUHA,
                    'title' => 'Shalat Dhuha Bersama',
                    'day_keys' => $dayKeys,
                    'allowed_days' => $allowedDays,
                    'days_label' => self::formatDaysLabel($dayKeys),
                    'start_time' => SchoolSetting::get('worship_dhuha_start', '06:30'),
                    'end_time' => SchoolSetting::get('worship_dhuha_end', '07:30'),
                    'gender' => 'ALL',
                    'gender_label' => 'Semua Siswa (Laki-Laki & Perempuan)',
                    'icon' => 'sun',
                    'color' => 'amber',
                ];
            })(),
            self::ACTIVITY_DZUHUR_JUMAT => (function () {
                $dayKeys = self::parseDays(SchoolSetting::get('worship_dzuhur_jumat_days', 'senin,selasa,rabu,kamis,jumat'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
                $allowedDays = self::dayKeysToCarbonDays($dayKeys);
                if (empty($allowedDays)) {
                    $allowedDays = [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY];
                }

                return [
                    'type' => self::ACTIVITY_DZUHUR_JUMAT,
                    'title' => "Shalat Dzuhur/Jum'at Berjamaah",
                    'day_keys' => $dayKeys,
                    'allowed_days' => $allowedDays,
                    'days_label' => self::formatDaysLabel($dayKeys),
                    'start_time' => SchoolSetting::get('worship_dzuhur_jumat_start', '11:30'),
                    'end_time' => SchoolSetting::get('worship_dzuhur_jumat_end', '13:15'),
                    'gender' => 'ALL',
                    'gender_label' => 'Semua Siswa (Laki-Laki & Perempuan)',
                    'icon' => 'mosque',
                    'color' => 'indigo',
                ];
            })(),
            self::ACTIVITY_KEPUTRIAN => (function () {
                $dayKeys = self::parseDays(SchoolSetting::get('worship_keputrian_days', 'jumat'), ['jumat']);
                $allowedDays = self::dayKeysToCarbonDays($dayKeys);
                if (empty($allowedDays)) {
                    $allowedDays = [Carbon::FRIDAY];
                }

                return [
                    'type' => self::ACTIVITY_KEPUTRIAN,
                    'title' => 'Kegiatan Keputrian',
                    'day_keys' => $dayKeys,
                    'allowed_days' => $allowedDays,
                    'days_label' => self::formatDaysLabel($dayKeys),
                    'start_time' => SchoolSetting::get('worship_keputrian_start', '11:30'),
                    'end_time' => SchoolSetting::get('worship_keputrian_end', '13:30'),
                    'gender' => 'P',
                    'gender_label' => 'Siswi Perempuan',
                    'icon' => 'sparkles',
                    'color' => 'rose',
                ];
            })(),
            default => [
                'type' => $type,
                'title' => 'Kegiatan Keagamaan',
                'day_keys' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
                'allowed_days' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY],
                'days_label' => "Senin - Jum'at",
                'start_time' => '06:00',
                'end_time' => '17:00',
                'gender' => $allowedGender,
                'gender_label' => 'Semua Siswa',
                'icon' => 'book',
                'color' => 'slate',
            ],
        };
    }

    /**
     * Validasi Time-Lock Ketat (Business Rule Strict Validation Mengikuti Master Data Pengaturan)
     */
    public static function validateTimeLock(string $type, ?Carbon $now = null, bool $allowAdminBypass = false): array
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $config = self::getScheduleConfig($type, $now);
        $currentDay = $now->dayOfWeek;
        $currentTime = $now->format('H:i');
        $dayName = $now->locale('id')->isoFormat('dddd');

        $isCorrectDay = in_array($currentDay, $config['allowed_days'], true);
        $startTime = substr($config['start_time'], 0, 5);
        $endTime = substr($config['end_time'], 0, 5);
        $isWithinHours = ($currentTime >= $startTime && $currentTime <= $endTime);

        if (! $isCorrectDay) {
            return [
                'allowed' => false,
                'is_open' => false,
                'reason' => "Hari ini ({$dayName}) bukan hari pelaksanaan {$config['title']}. Jadwal resmi: {$config['days_label']}.",
                'config' => $config,
                'current_time' => $currentTime,
                'current_day_name' => $dayName,
                'current_date' => $now->toDateString(),
                'current_date_formatted' => $now->locale('id')->translatedFormat('d F Y'),
                'current_day_date' => $now->locale('id')->translatedFormat('l, d F Y'),
            ];
        }

        if ($currentTime < $startTime) {
            return [
                'allowed' => false,
                'is_open' => false,
                'reason' => "Sesi presensi {$config['title']} belum dibuka. Presensi dibuka mulai pukul {$startTime} WIB.",
                'config' => $config,
                'current_time' => $currentTime,
                'current_day_name' => $dayName,
                'current_date' => $now->toDateString(),
                'current_date_formatted' => $now->locale('id')->translatedFormat('d F Y'),
                'current_day_date' => $now->locale('id')->translatedFormat('l, d F Y'),
            ];
        }

        if ($currentTime > $endTime) {
            return [
                'allowed' => false,
                'is_open' => false,
                'reason' => "Sesi presensi {$config['title']} telah berakhir pada pukul {$endTime} WIB (Batas Akhir).",
                'config' => $config,
                'current_time' => $currentTime,
                'current_day_name' => $dayName,
                'current_date' => $now->toDateString(),
                'current_date_formatted' => $now->locale('id')->translatedFormat('d F Y'),
                'current_day_date' => $now->locale('id')->translatedFormat('l, d F Y'),
            ];
        }

        return [
            'allowed' => true,
            'is_open' => true,
            'reason' => "Sesi presensi {$config['title']} sedang dibuka ({$startTime} - {$endTime} WIB).",
            'config' => $config,
            'current_time' => $currentTime,
            'current_day_name' => $dayName,
            'current_date' => $now->toDateString(),
            'current_date_formatted' => $now->locale('id')->translatedFormat('d F Y'),
            'current_day_date' => $now->locale('id')->translatedFormat('l, d F Y'),
        ];
    }
}
