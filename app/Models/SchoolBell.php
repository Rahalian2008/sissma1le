<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'order' => 'integer',
    ];

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
     * Pastikan jadwal default sekolah terpasang jika tabel masih kosong
     */
    public static function ensureDefaultSchedules(): void
    {
        if (! Schema::hasTable('school_bells') || self::count() > 0) {
            return;
        }

        $allWeekdays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $defaults = [
            [
                'title' => 'Murottal Al-Qur\'an Pra KBM (Pagi Hari)',
                'time' => '06:45',
                'days' => $allWeekdays,
                'sound_type' => 'murottal',
                'surah_number' => 55,
                'surah_name' => 'Ar-Rahman',
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Murottal Al-Qur\'an menyambut kedatangan peserta didik dan guru sebelum KBM dimulai',
                'voice_text' => null,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Bel Masuk Kelas & KBM Jam Ke-1',
                'time' => '07:00',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Tanda masuk kelas, seluruh siswa dan guru bersiap di ruang kelas',
                'voice_text' => 'Perhatian, saatnya jam pelajaran pertama dimulai. Selamat belajar.',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-2',
                'time' => '07:45',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-2',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-2.',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-3',
                'time' => '08:30',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-3',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-3.',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-4',
                'time' => '09:15',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-4',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-4.',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'title' => 'Bel Istirahat Pertama & Murottal Santai',
                'time' => '10:00',
                'days' => $allWeekdays,
                'sound_type' => 'murottal',
                'surah_number' => 56,
                'surah_name' => 'Al-Waqi\'ah',
                'volume' => 80,
                'duration_seconds' => 1200,
                'description' => 'Waktu istirahat pertama 20 menit disertai murottal Al-Qur\'an',
                'voice_text' => 'Saatnya istirahat pertama. Selamat beristirahat.',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-5 (Selesai Istirahat 1)',
                'time' => '10:20',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Istirahat pertama selesai, kembali masuk ke ruang kelas masing-masing',
                'voice_text' => 'Waktu istirahat telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-5.',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-6',
                'time' => '11:05',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-6',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-6.',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'title' => 'Bel Istirahat Kedua & Shalat Dzuhur Berjamaah',
                'time' => '11:50',
                'days' => $allWeekdays,
                'sound_type' => 'murottal',
                'surah_number' => 67,
                'surah_name' => 'Al-Mulk',
                'volume' => 85,
                'duration_seconds' => 1800,
                'description' => 'Istirahat siang, makan siang, dan ibadah Shalat Dzuhur berjamaah di Masjid Sekolah',
                'voice_text' => 'Saatnya istirahat kedua dan persiapan Shalat Dzuhur berjamaah.',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'title' => 'Bel Masuk KBM Jam Ke-7 (Selesai Dzuhur)',
                'time' => '12:45',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 15,
                'description' => 'Masuk kembali untuk pembelajaran sesi siang',
                'voice_text' => 'Waktu istirahat telah selesai. Seluruh siswa silakan masuk ke kelas untuk jam pelajaran ke-7.',
                'is_active' => true,
                'order' => 10,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-8',
                'time' => '13:30',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-8',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-8.',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'title' => 'Ganti Jam Pelajaran Ke-9',
                'time' => '14:15',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 95,
                'duration_seconds' => 10,
                'description' => 'Pergantian mata pelajaran jam ke-9',
                'voice_text' => 'Saatnya pergantian jam pelajaran ke-9.',
                'is_active' => true,
                'order' => 12,
            ],
            [
                'title' => 'Bel Pulang Sekolah',
                'time' => '15:00',
                'days' => $allWeekdays,
                'sound_type' => 'chime',
                'volume' => 100,
                'duration_seconds' => 20,
                'description' => 'KBM selesai, peserta didik berdoa dan pulang dengan tertib',
                'voice_text' => 'Kegiatan belajar mengajar hari ini telah selesai. Selamat jalan dan berhati-hati di jalan.',
                'is_active' => true,
                'order' => 13,
            ],
        ];

        foreach ($defaults as $d) {
            self::create($d);
        }
    }
}
