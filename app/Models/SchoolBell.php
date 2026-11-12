<?php

namespace App\Models;

use App\Services\NationalAnthemService;
use App\Services\QuranDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchoolBell extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'time',
        'days',
        'sound_type',
        'audio_url',
        'surah_number',
        'surah_name',
        'surah_numbers',
        'anthem_ids',
        'playlist',
        'volume',
        'duration_seconds',
        'description',
        'voice_text',
        'is_active',
        'order',
    ];

    protected $casts = [
        'days' => 'array',
        'is_active' => 'boolean',
        'volume' => 'integer',
        'duration_seconds' => 'integer',
        'surah_number' => 'integer',
        'surah_numbers' => 'array',
        'anthem_ids' => 'array',
        'playlist' => 'array',
        'order' => 'integer',
    ];

    protected $appends = [
        'playlist_items',
        'anthem_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (SchoolBell $bell) {
            if (empty($bell->title)) {
                $bell->title = 'Bel Sekolah';
            }
            if (empty($bell->time)) {
                $bell->time = '07:00';
            }
            if (empty($bell->sound_type)) {
                $bell->sound_type = 'chime';
            }
            if (empty($bell->days)) {
                $bell->days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            }
            if ($bell->volume === null) {
                $bell->volume = 100;
            }
            if ($bell->duration_seconds === null) {
                $bell->duration_seconds = 15;
            }
            if ($bell->is_active === null) {
                $bell->is_active = true;
            }
        });

        static::saving(function (SchoolBell $bell) {
            if ($bell->sound_type === 'murottal') {
                $numbers = ! empty($bell->surah_numbers) && is_array($bell->surah_numbers)
                    ? $bell->surah_numbers
                    : ($bell->surah_number ? [$bell->surah_number] : []);

                if (! empty($numbers)) {
                    $firstNum = (int) $numbers[0];
                    $bell->surah_number = $firstNum;
                    $surah = QuranDataService::getSurah($firstNum);
                    if ($surah) {
                        if (empty($bell->audio_url)) {
                            $bell->audio_url = $surah['audio_url'];
                        }
                        if (empty($bell->surah_name)) {
                            $names = [];
                            foreach ($numbers as $sn) {
                                $s = QuranDataService::getSurah((int) $sn);
                                if ($s) {
                                    $names[] = $s['name'];
                                }
                            }
                            if (count($names) > 3) {
                                $bell->surah_name = count($names).' Surat: '.$names[0].' s.d. '.$names[count($names) - 1];
                            } elseif (count($names) > 1) {
                                $bell->surah_name = implode(', ', $names);
                            } else {
                                $bell->surah_name = $surah['name'] ?? '';
                            }
                        }
                        $bell->surah_name = Str::limit($bell->surah_name, 250);
                    }
                }
            } elseif ($bell->sound_type === 'song') {
                if (! empty($bell->anthem_ids) && is_array($bell->anthem_ids)) {
                    $allAnthems = collect(NationalAnthemService::getNationalAnthems());
                    $firstAnthem = $allAnthems->firstWhere('id', $bell->anthem_ids[0]);
                    if ($firstAnthem && empty($bell->audio_url)) {
                        $bell->audio_url = $firstAnthem['audio_url'];
                    }
                }
            }
        });
    }

    /**
     * Dapatkan ID lagu nasional pertama (kompatibilitas backward)
     */
    public function getAnthemIdAttribute(): ?string
    {
        $ids = is_array($this->anthem_ids) ? $this->anthem_ids : (json_decode($this->anthem_ids ?? '[]', true) ?: []);

        return $ids[0] ?? null;
    }

    /**
     * Dapatkan daftar item playlist audio untuk bel ini
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPlaylistItemsAttribute(): array
    {
        return $this->getPlaylistItems();
    }

    /**
     * Dapatkan daftar item playlist audio (Surah/Lagu Nasional/Custom)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPlaylistItems(): array
    {
        if (! empty($this->playlist) && is_array($this->playlist)) {
            return $this->playlist;
        }

        if ($this->sound_type === 'murottal') {
            $numbers = ! empty($this->surah_numbers) && is_array($this->surah_numbers)
                ? $this->surah_numbers
                : ($this->surah_number ? [$this->surah_number] : []);

            $items = [];
            foreach ($numbers as $num) {
                $surah = QuranDataService::getSurah((int) $num);
                if ($surah) {
                    $items[] = [
                        'id' => $surah['number'],
                        'title' => 'Surah '.$surah['number'].'. '.$surah['name'],
                        'name' => $surah['name'],
                        'translation' => $surah['translation'] ?? '',
                        'audio_url' => $surah['audio_url'],
                        'type' => 'murottal',
                    ];
                }
            }

            if (! empty($items)) {
                return $items;
            }

            if ($this->audio_url) {
                return [[
                    'title' => $this->surah_name ? 'Surah '.$this->surah_name : $this->title,
                    'audio_url' => $this->audio_url,
                    'type' => 'murottal',
                ]];
            }
        }

        if ($this->sound_type === 'song') {
            $ids = ! empty($this->anthem_ids) && is_array($this->anthem_ids)
                ? $this->anthem_ids
                : [];

            if (! empty($ids)) {
                $allAnthems = collect(NationalAnthemService::getNationalAnthems());
                $items = [];
                foreach ($ids as $id) {
                    $anthem = $allAnthems->firstWhere('id', $id);
                    if ($anthem) {
                        $items[] = [
                            'id' => $anthem['id'],
                            'title' => $anthem['title'],
                            'composer' => $anthem['composer'] ?? '',
                            'audio_url' => $anthem['audio_url'],
                            'type' => 'song',
                        ];
                    }
                }
                if (! empty($items)) {
                    return $items;
                }
            }

            if ($this->audio_url) {
                return [[
                    'title' => $this->title,
                    'audio_url' => $this->audio_url,
                    'type' => 'song',
                ]];
            }
        }

        if ($this->audio_url) {
            return [[
                'title' => $this->title,
                'audio_url' => $this->audio_url,
                'type' => $this->sound_type,
            ]];
        }

        return [];
    }

    /**
     * Scope untuk jadwal bel aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk jadwal hari ini
     */
    public function scopeForDay(Builder $query, ?string $dayName = null): Builder
    {
        $dayName = $dayName ?: self::getCurrentDayName();

        return $query->where(function ($q) use ($dayName) {
            $q->whereNull('days')
                ->orWhereJsonContains('days', $dayName);
        });
    }

    /**
     * Mendapatkan nama hari dalam Bahasa Indonesia
     */
    public static function getCurrentDayName(): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $days[date('l')] ?? 'Senin';
    }

    /**
     * Pastikan jadwal default sekolah terpasang jika belum pernah diinisialisasi
     */
    public static function ensureDefaultSchedules(bool $force = false): void
    {
        if (! Schema::hasTable('school_bells')) {
            return;
        }

        $isInitialized = (bool) SchoolSetting::get('school_bells_initialized', false);
        if ($isInitialized && ! $force) {
            return;
        }

        if (! $force && self::count() > 0) {
            SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');

            return;
        }

        $allWeekdays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $defaults = [
            // ========================================================
            // 1. PRA-KBM (06.00 s.d 06.30) - MUROTTAL 20 MENIT
            // Setiap hari Senin s.d Jumat surat murottal berbeda
            // ========================================================
            [
                'title' => 'Murottal Pagi Pra-KBM (Senin)',
                'time' => '06:05',
                'days' => ['Senin'],
                'sound_type' => 'murottal',
                'surah_numbers' => [55, 56],
                'volume' => 85,
                'duration_seconds' => 1200,
                'description' => 'Murottal Al-Qur\'an 20 menit (Surah Ar-Rahman & Al-Waqi\'ah) mengawali awal pekan dengan penuh rasa syukur dan berkah',
                'voice_text' => null,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Murottal Pagi Pra-KBM (Selasa)',
                'time' => '06:05',
                'days' => ['Selasa'],
                'sound_type' => 'murottal',
                'surah_numbers' => [36],
                'volume' => 85,
                'duration_seconds' => 1200,
                'description' => 'Murottal Al-Qur\'an 20 menit Surah Yasin (Qalbul Qur\'an) menentramkan jiwa sebelum Senam Indonesia Hebat',
                'voice_text' => null,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Murottal Pagi Pra-KBM (Rabu)',
                'time' => '06:05',
                'days' => ['Rabu'],
                'sound_type' => 'murottal',
                'surah_numbers' => [67, 32, 76],
                'volume' => 85,
                'duration_seconds' => 1200,
                'description' => 'Murottal Al-Qur\'an 20 menit (Surah Al-Mulk, As-Sajdah & Al-Insan) mengiringi persiapan literasi hafalan Qur\'an',
                'voice_text' => null,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Murottal Pagi Pra-KBM (Kamis)',
                'time' => '06:05',
                'days' => ['Kamis'],
                'sound_type' => 'murottal',
                'surah_numbers' => [19, 76],
                'volume' => 85,
                'duration_seconds' => 1200,
                'description' => 'Murottal Al-Qur\'an 20 menit (Surah Maryam & Al-Insan) menyejukkan hati sebelum Shalat Dhuha bersama',
                'voice_text' => null,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'Murottal Pagi Pra-KBM (Jumat)',
                'time' => '06:05',
                'days' => ['Jumat'],
                'sound_type' => 'murottal',
                'surah_numbers' => [61, 62, 63],
                'volume' => 85,
                'duration_seconds' => 1200,
                'description' => 'Murottal Al-Qur\'an 20 menit (Surah As-Saff, Al-Jumu\'ah & Al-Munafiqun) menyambut hari Jumat yang agung',
                'voice_text' => null,
                'is_active' => true,
                'order' => 5,
            ],

            // ========================================================
            // 2. KEGIATAN PAGI PEMBUKA (06.30 WIB)
            // ========================================================
            [
                'title' => 'Bel Persiapan Upacara Bendera (Senin)',
                'time' => '06:30',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Persiapan dan pelaksanaan Upacara Bendera hari Senin (06.30 - 07.15 WIB)',
                'voice_text' => 'Perhatian, seluruh siswa, dewan guru, dan staf segera menuju lapangan untuk mengikuti Upacara Bendera.',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'title' => 'Bel Senam Indonesia Hebat / Selasa Bersih',
                'time' => '06:30',
                'days' => ['Selasa'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Kegiatan Senam Indonesia Hebat dan Gerakan Selasa Bersih (06.30 - 07.20 WIB)',
                'voice_text' => 'Perhatian, seluruh siswa berkumpul di lapangan untuk mengikuti kegiatan Senam Indonesia Hebat dan Selasa Bersih.',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'title' => 'Bel Kegiatan Bersama Wali Kelas (Rabu)',
                'time' => '06:30',
                'days' => ['Rabu'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Kegiatan bimbingan Wali Kelas, hafalan surat Al-Qur\'an dan literasi kelas (06.30 - 07.20 WIB)',
                'voice_text' => 'Perhatian, saatnya kegiatan bimbingan bersama Wali Kelas dan hafalan Al-Qur\'an di kelas masing-masing.',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'title' => 'Bel Shalat Dhuha Bersama (Kamis)',
                'time' => '06:30',
                'days' => ['Kamis'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Pelaksanaan ibadah Shalat Dhuha bersama seluruh civitas sekolah di masjid (06.30 - 07.20 WIB)',
                'voice_text' => 'Perhatian, seluruh siswa dan bapak ibu guru dipersilakan menuju masjid sekolah untuk melaksanakan Shalat Dhuha bersama.',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'title' => 'Bel Shalat Dhuha Bersama & Tadarus (Jumat)',
                'time' => '06:30',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Pelaksanaan Shalat Dhuha bersama dan Tadarus Al-Qur\'an pagi Jumat (06.30 - 07.15 WIB)',
                'voice_text' => 'Perhatian, mari bersama-sama menuju masjid sekolah untuk melaksanakan Shalat Dhuha dan Tadarus Al-Qur\'an.',
                'is_active' => true,
                'order' => 10,
            ],

            // ========================================================
            // 3. JADWAL KBM SENIN
            // ========================================================
            [
                'title' => 'Bel Masuk KBM Jam Ke-1 (Senin)',
                'time' => '07:15',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Tanda masuk ruang kelas jam pelajaran pertama hari Senin (07.15 - 08.00 WIB)',
                'voice_text' => 'Perhatian, saatnya jam pelajaran pertama dimulai. Selamat belajar.',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-2 (Senin)',
                'time' => '08:00',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-2 hari Senin (08.00 - 08.45 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-2.',
                'is_active' => true,
                'order' => 12,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-3 (Senin)',
                'time' => '08:45',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-3 hari Senin (08.45 - 09.30 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-3.',
                'is_active' => true,
                'order' => 13,
            ],
            [
                'title' => 'Bel Istirahat Ke-1 (Senin) + Lagu Nasional',
                'time' => '09:30',
                'days' => ['Senin'],
                'sound_type' => 'song',
                'anthem_ids' => ['indonesia-raya', 'bagimu-negeri'],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Istirahat pertama 15 menit hari Senin diiringi 2 Lagu Nasional: Indonesia Raya & Bagimu Negeri (09.30 - 09.45 WIB)',
                'voice_text' => 'Perhatian, saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 14,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-4 (Senin)',
                'time' => '09:45',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Istirahat pertama selesai, masuk kelas untuk jam ke-4 hari Senin (09.45 - 10.30 WIB)',
                'voice_text' => 'Waktu istirahat telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-4.',
                'is_active' => true,
                'order' => 15,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-5 (Senin)',
                'time' => '10:30',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-5 hari Senin (10.30 - 11.15 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-5.',
                'is_active' => true,
                'order' => 16,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-6 (Senin)',
                'time' => '11:15',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-6 hari Senin (11.15 - 12.00 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-6.',
                'is_active' => true,
                'order' => 17,
            ],
            [
                'title' => 'Bel Istirahat Ke-2 & Shalat Dzuhur (Senin) + Murottal',
                'time' => '12:00',
                'days' => ['Senin'],
                'sound_type' => 'murottal',
                'surah_numbers' => [48, 49],
                'volume' => 80,
                'duration_seconds' => 900,
                'description' => 'Istirahat kedua 45 menit (Ishoma Dzuhur) diiringi Murottal 15 menit Surah Al-Fath & Al-Hujurat (12.00 - 12.45 WIB)',
                'voice_text' => 'Saatnya istirahat kedua, makan siang, dan persiapan Shalat Dzuhur berjamaah di masjid sekolah.',
                'is_active' => true,
                'order' => 18,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-7 Selesai Ishoma (Senin)',
                'time' => '12:45',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Selesai istirahat kedua dan Shalat Dzuhur, masuk KBM jam ke-7 hari Senin (12.45 - 13.30 WIB)',
                'voice_text' => 'Waktu istirahat dan Shalat Dzuhur telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-7.',
                'is_active' => true,
                'order' => 19,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-8 (Senin)',
                'time' => '13:30',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-8 hari Senin (13.30 - 14.15 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-8.',
                'is_active' => true,
                'order' => 20,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-9 (Senin)',
                'time' => '14:15',
                'days' => ['Senin'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-9 hari Senin (14.15 - 15.00 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-9.',
                'is_active' => true,
                'order' => 21,
            ],
            [
                'title' => 'Bel Pulang Sekolah (Senin) + Murottal 15 Menit',
                'time' => '15:00',
                'days' => ['Senin'],
                'sound_type' => 'murottal',
                'surah_numbers' => [78, 79, 80],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM hari Senin diiringi Murottal 15 menit (Surah An-Naba\', An-Nazi\'at & \'Abasa)',
                'voice_text' => 'Kegiatan belajar mengajar hari ini telah selesai. Selamat pulang dan berhati-hati di jalan.',
                'is_active' => true,
                'order' => 22,
            ],

            // ========================================================
            // 4. JADWAL KBM SELASA, RABU, KAMIS
            // ========================================================
            [
                'title' => 'Bel Masuk KBM Jam Ke-1 (Selasa - Kamis)',
                'time' => '07:20',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Tanda masuk kelas jam pelajaran ke-1 hari Selasa, Rabu, dan Kamis (07.20 - 08.00 WIB)',
                'voice_text' => 'Perhatian, saatnya jam pelajaran pertama dimulai. Selamat belajar.',
                'is_active' => true,
                'order' => 23,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-2 (Selasa - Kamis)',
                'time' => '08:00',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-2 hari Selasa s.d Kamis (08.00 - 08.40 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-2.',
                'is_active' => true,
                'order' => 24,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-3 (Selasa - Kamis)',
                'time' => '08:40',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-3 hari Selasa s.d Kamis (08.40 - 09.20 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-3.',
                'is_active' => true,
                'order' => 25,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-4 (Selasa - Kamis)',
                'time' => '09:20',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-4 hari Selasa s.d Kamis (09.20 - 10.00 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-4.',
                'is_active' => true,
                'order' => 26,
            ],
            [
                'title' => 'Bel Istirahat Ke-1 (Selasa) + Lagu Nasional',
                'time' => '10:00',
                'days' => ['Selasa'],
                'sound_type' => 'song',
                'anthem_ids' => ['berkibarlah-benderaku', 'garuda-pancasila'],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Istirahat pertama 15 menit hari Selasa diiringi 2 Lagu Nasional: Berkibarlah Benderaku & Garuda Pancasila (10.00 - 10.15 WIB)',
                'voice_text' => 'Perhatian, saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 27,
            ],
            [
                'title' => 'Bel Istirahat Ke-1 (Rabu) + Lagu Nasional',
                'time' => '10:00',
                'days' => ['Rabu'],
                'sound_type' => 'song',
                'anthem_ids' => ['maju-indonesia', 'satu-nusa-satu-bangsa'],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Istirahat pertama 15 menit hari Rabu diiringi 2 Lagu Nasional: Maju Indonesia (Bangun Pemudi Pemuda) & Satu Nusa Satu Bangsa (10.00 - 10.15 WIB)',
                'voice_text' => 'Perhatian, saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 28,
            ],
            [
                'title' => 'Bel Istirahat Ke-1 (Kamis) + Lagu Nasional',
                'time' => '10:00',
                'days' => ['Kamis'],
                'sound_type' => 'song',
                'anthem_ids' => ['hari-merdeka', 'maju-tak-gentar'],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Istirahat pertama 15 menit hari Kamis diiringi 2 Lagu Nasional: Hari Merdeka & Maju Tak Gentar (10.00 - 10.15 WIB)',
                'voice_text' => 'Perhatian, saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 29,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-5 (Selasa - Kamis)',
                'time' => '10:15',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Istirahat pertama selesai, masuk kelas untuk jam ke-5 hari Selasa s.d Kamis (10.15 - 10.55 WIB)',
                'voice_text' => 'Waktu istirahat telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-5.',
                'is_active' => true,
                'order' => 30,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-6 (Selasa - Kamis)',
                'time' => '10:55',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-6 hari Selasa s.d Kamis (10.55 - 11.35 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-6.',
                'is_active' => true,
                'order' => 31,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-7 (Selasa - Kamis)',
                'time' => '11:35',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-7 hari Selasa s.d Kamis (11.35 - 12.15 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-7.',
                'is_active' => true,
                'order' => 32,
            ],
            [
                'title' => 'Bel Istirahat Ke-2 & Shalat Dzuhur (Selasa) + Murottal',
                'time' => '12:15',
                'days' => ['Selasa'],
                'sound_type' => 'murottal',
                'surah_numbers' => [50, 44, 53],
                'volume' => 80,
                'duration_seconds' => 900,
                'description' => 'Istirahat kedua 40 menit (Ishoma Dzuhur) hari Selasa diiringi Murottal 15 menit (Surah Qaf, Ad-Dukhan & An-Najm) (12.15 - 12.55 WIB)',
                'voice_text' => 'Saatnya istirahat kedua, makan siang, dan persiapan Shalat Dzuhur berjamaah di masjid sekolah.',
                'is_active' => true,
                'order' => 33,
            ],
            [
                'title' => 'Bel Istirahat Ke-2 & Shalat Dzuhur (Rabu) + Murottal',
                'time' => '12:15',
                'days' => ['Rabu'],
                'sound_type' => 'murottal',
                'surah_numbers' => [31, 57],
                'volume' => 80,
                'duration_seconds' => 900,
                'description' => 'Istirahat kedua 40 menit (Ishoma Dzuhur) hari Rabu diiringi Murottal 15 menit (Surah Luqman & Al-Hadid) (12.15 - 12.55 WIB)',
                'voice_text' => 'Saatnya istirahat kedua, makan siang, dan persiapan Shalat Dzuhur berjamaah di masjid sekolah.',
                'is_active' => true,
                'order' => 34,
            ],
            [
                'title' => 'Bel Istirahat Ke-2 & Shalat Dzuhur (Kamis) + Murottal',
                'time' => '12:15',
                'days' => ['Kamis'],
                'sound_type' => 'murottal',
                'surah_numbers' => [75, 73, 74],
                'volume' => 80,
                'duration_seconds' => 900,
                'description' => 'Istirahat kedua 40 menit (Ishoma Dzuhur) hari Kamis diiringi Murottal 15 menit (Surah Al-Qiyamah, Al-Muzzammil & Al-Muddassir) (12.15 - 12.55 WIB)',
                'voice_text' => 'Saatnya istirahat kedua, makan siang, dan persiapan Shalat Dzuhur berjamaah di masjid sekolah.',
                'is_active' => true,
                'order' => 35,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-8 Selesai Ishoma (Selasa - Kamis)',
                'time' => '12:55',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Selesai istirahat kedua dan Shalat Dzuhur, masuk KBM jam ke-8 hari Selasa s.d Kamis (12.55 - 13.40 WIB)',
                'voice_text' => 'Waktu istirahat dan Shalat Dzuhur telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-8.',
                'is_active' => true,
                'order' => 36,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-9 (Selasa - Kamis)',
                'time' => '13:40',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-9 hari Selasa s.d Kamis (13.40 - 14.20 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-9.',
                'is_active' => true,
                'order' => 37,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-10 (Selasa - Kamis)',
                'time' => '14:20',
                'days' => ['Selasa', 'Rabu', 'Kamis'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-10 hari Selasa s.d Kamis (14.20 - 15.00 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-10.',
                'is_active' => true,
                'order' => 38,
            ],
            [
                'title' => 'Bel Pulang Sekolah (Selasa) + Murottal 15 Menit',
                'time' => '15:00',
                'days' => ['Selasa'],
                'sound_type' => 'murottal',
                'surah_numbers' => [81, 82, 83, 84],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM hari Selasa diiringi Murottal 15 menit (Surah At-Takwir, Al-Infitar, Al-Mutaffifin & Al-Insyiqaq)',
                'voice_text' => 'Kegiatan belajar mengajar hari ini telah selesai. Selamat pulang dan berhati-hati di jalan.',
                'is_active' => true,
                'order' => 39,
            ],
            [
                'title' => 'Bel Pulang Sekolah (Rabu) + Murottal 15 Menit',
                'time' => '15:00',
                'days' => ['Rabu'],
                'sound_type' => 'murottal',
                'surah_numbers' => [85, 86, 87, 88],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM hari Rabu diiringi Murottal 15 menit (Surah Al-Buruj, At-Tariq, Al-A\'la & Al-Ghasyiyah)',
                'voice_text' => 'Kegiatan belajar mengajar hari ini telah selesai. Selamat pulang dan berhati-hati di jalan.',
                'is_active' => true,
                'order' => 40,
            ],
            [
                'title' => 'Bel Pulang Sekolah (Kamis) + Murottal 15 Menit',
                'time' => '15:00',
                'days' => ['Kamis'],
                'sound_type' => 'murottal',
                'surah_numbers' => [89, 90, 91, 92],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM hari Kamis diiringi Murottal 15 menit (Surah Al-Fajr, Al-Balad, Asy-Syams & Al-Lail)',
                'voice_text' => 'Kegiatan belajar mengajar hari ini telah selesai. Selamat pulang dan berhati-hati di jalan.',
                'is_active' => true,
                'order' => 41,
            ],

            // ========================================================
            // 5. JADWAL KBM JUM'AT (JADWAL KHUSUS)
            // ========================================================
            [
                'title' => 'Bel Masuk KBM Jam Ke-1 (Jumat)',
                'time' => '07:15',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Tanda masuk ruang kelas jam pelajaran pertama hari Jumat (07.15 - 07.55 WIB)',
                'voice_text' => 'Perhatian, saatnya jam pelajaran pertama dimulai. Selamat belajar.',
                'is_active' => true,
                'order' => 42,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-2 (Jumat)',
                'time' => '07:55',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-2 hari Jumat (07.55 - 08.35 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-2.',
                'is_active' => true,
                'order' => 43,
            ],
            [
                'title' => 'Bel Istirahat Ke-1 (Jumat) + Lagu Nasional',
                'time' => '08:35',
                'days' => ['Jumat'],
                'sound_type' => 'song',
                'anthem_ids' => ['indonesia-pusaka', 'tanah-airku'],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Istirahat pertama 15 menit hari Jumat diiringi 2 Lagu Nasional: Indonesia Pusaka & Tanah Airku (08.35 - 08.50 WIB)',
                'voice_text' => 'Perhatian, saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 44,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-3 (Jumat)',
                'time' => '08:50',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Istirahat pertama selesai, masuk kelas untuk jam ke-3 hari Jumat (08.50 - 09.30 WIB)',
                'voice_text' => 'Waktu istirahat telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-3.',
                'is_active' => true,
                'order' => 45,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-4 (Jumat)',
                'time' => '09:30',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-4 hari Jumat (09.30 - 10.10 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-4.',
                'is_active' => true,
                'order' => 46,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-5 (Jumat)',
                'time' => '10:10',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-5 hari Jumat (10.10 - 10.50 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-5.',
                'is_active' => true,
                'order' => 47,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-6 (Jumat)',
                'time' => '10:50',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-6 hari Jumat (10.50 - 11.30 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-6.',
                'is_active' => true,
                'order' => 48,
            ],
            [
                'title' => 'Bel Istirahat Ke-2 & Shalat Jumat + Murottal',
                'time' => '11:30',
                'days' => ['Jumat'],
                'sound_type' => 'murottal',
                'surah_numbers' => [18],
                'volume' => 80,
                'duration_seconds' => 900,
                'description' => 'Istirahat kedua 70 menit (Persiapan & Shalat Jumat) diiringi Murottal 15 menit Surah Al-Kahf (11.30 - 12.40 WIB)',
                'voice_text' => 'Saatnya istirahat kedua dan persiapan ibadah Shalat Jumat berjamaah di masjid sekolah.',
                'is_active' => true,
                'order' => 49,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-7 Selesai Shalat Jumat',
                'time' => '12:40',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Selesai ibadah Shalat Jumat, masuk KBM sesi siang jam ke-7 hari Jumat (12.40 - 13.20 WIB)',
                'voice_text' => 'Ibadah Shalat Jumat telah selesai. Seluruh siswa silakan kembali ke kelas untuk jam pelajaran ke-7.',
                'is_active' => true,
                'order' => 50,
            ],
            [
                'title' => 'Bel Ganti Jam Pelajaran Ke-8 (Jumat)',
                'time' => '13:20',
                'days' => ['Jumat'],
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-8 hari Jumat (13.20 - 14.00 WIB)',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-8.',
                'is_active' => true,
                'order' => 51,
            ],
            [
                'title' => 'Bel Pulang Sekolah (Jumat) + Murottal 15 Menit',
                'time' => '14:00',
                'days' => ['Jumat'],
                'sound_type' => 'murottal',
                'surah_numbers' => [93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM hari Jumat diiringi Murottal 15 menit 22 Surat Pendek Juz Amma (Ad-Duha s.d An-Nas / 3 Qul)',
                'voice_text' => 'Kegiatan pembelajaran hari Jumat telah selesai. Selamat berakhir pekan dan salam untuk keluarga di rumah.',
                'is_active' => true,
                'order' => 52,
            ],
        ];

        foreach ($defaults as $d) {
            self::create($d);
        }

        SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');
    }
}
