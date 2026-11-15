<?php

use App\Models\SchoolBell;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('school_bells')) {
            return;
        }

        // Jika database baru / belum di-seed lengkap, biarkan ensureDefaultSchedules yang men-generate
        if (SchoolBell::count() < 10) {
            return;
        }

        // 1. Update Bel Jumat Jam 14.00 (Mulai Kegiatan Ekstrakurikuler)
        $friday14 = SchoolBell::where('time', '14:00')
            ->where(function ($q) {
                $q->whereJsonContains('days', 'Jumat')
                    ->orWhere('title', 'like', '%Jumat%');
            })
            ->first();

        if ($friday14) {
            $friday14->update([
                'title' => 'Bel Mulai Kegiatan Ekstrakurikuler (Jumat)',
                'sound_type' => 'chime',
                'surah_numbers' => null,
                'surah_number' => null,
                'surah_name' => null,
                'audio_url' => null,
                'duration_seconds' => 15,
                'description' => 'Kegiatan pembelajaran reguler selesai, saatnya memulai kegiatan Ekstrakurikuler hari Jumat (14.00 - 15.00 WIB)',
                'voice_text' => 'Perhatian, kegiatan belajar mengajar reguler hari Jumat telah selesai. Kepada seluruh siswa yang mengikuti kegiatan ekstrakurikuler, dipersilakan menuju ke lokasi kegiatan masing-masing.',
            ]);
        }

        // 2. Tambah / Pastikan Bel Pulang Jumat Jam 15.00 (Pulang Sekolah & Selesai Ekstrakurikuler)
        $friday15 = SchoolBell::where('time', '15:00')
            ->where(function ($q) {
                $q->whereJsonContains('days', 'Jumat')
                    ->orWhere('title', 'like', '%Jumat%');
            })
            ->first();

        if (! $friday15) {
            SchoolBell::create([
                'title' => 'Bel Pulang Sekolah & Selesai Ekstrakurikuler (Jumat) + Murottal 15 Menit',
                'time' => '15:00',
                'days' => ['Jumat'],
                'sound_type' => 'murottal',
                'surah_numbers' => [93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114],
                'volume' => 85,
                'duration_seconds' => 900,
                'description' => 'Bel kepulangan KBM dan selesai ekstrakurikuler hari Jumat diiringi Murottal 15 menit 22 Surat Pendek Juz Amma (Ad-Duha s.d An-Nas / 3 Qul)',
                'voice_text' => 'Seluruh rangkaian kegiatan pembelajaran dan ekstrakurikuler hari Jumat telah selesai. Selamat pulang ke rumah masing-masing, berhati-hati di jalan, selamat berakhir pekan dan salam hangat untuk keluarga di rumah.',
                'is_active' => true,
                'order' => 53,
            ]);
        }

        // 3. Update Voice Text Murottal Pra-KBM Pagi 06:05 jika masih kosong
        $praKbmTexts = [
            'Senin' => 'Selamat pagi civitas akademika SMAN 1 Lengkong. Mengawali pagi awal pekan dengan penuh rasa syukur dan berkah, mari bersama kita dengarkan lantunan Murottal Al-Qur\'an.',
            'Selasa' => 'Selamat pagi civitas akademika SMAN 1 Lengkong. Mengawali pagi hari Selasa yang penuh semangat, mari bersama kita dengarkan lantunan Murottal Al-Qur\'an Surah Yasin.',
            'Rabu' => 'Selamat pagi civitas akademika SMAN 1 Lengkong. Mengawali pagi hari Rabu bersama bimbingan Wali Kelas dan literasi Al-Qur\'an, mari bersama kita dengarkan lantunan Murottal Al-Qur\'an.',
            'Kamis' => 'Selamat pagi civitas akademika SMAN 1 Lengkong. Mengawali pagi hari Kamis sebelum pelaksanaan Shalat Dhuha bersama, mari bersama kita dengarkan lantunan Murottal Al-Qur\'an.',
            'Jumat' => 'Selamat pagi civitas akademika SMAN 1 Lengkong di hari Jumat yang mulia dan penuh berkah. Mari bersama kita dengarkan lantunan Murottal Al-Qur\'an.',
        ];

        foreach ($praKbmTexts as $day => $text) {
            $morningBell = SchoolBell::where('time', '06:05')
                ->whereJsonContains('days', $day)
                ->where('sound_type', 'murottal')
                ->first();

            if ($morningBell && empty($morningBell->voice_text)) {
                $morningBell->update(['voice_text' => $text]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed
    }
};
