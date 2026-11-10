<?php

namespace App\Console\Commands;

use App\Services\DisciplineAutomationService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('discipline:process-automations {--time= : Waktu eksekusi spesifik (11:00, 14:00, 14:30, tahfidz, all)} {--date= : Tanggal target (YYYY-MM-DD)}')]
#[Description('Otomasi input pelanggaran siswa (P1.3 Alpa, P1.7 Alpa 3 hari) dan prestasi siswa (R1.1 Shalat Dzuhur, R1.2 Shalat Dhuha, R1.6 Keputrian, R1.3 Tahfidz >= 20 ayat)')]
class ProcessDisciplineAndAchievementAutomations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DisciplineAutomationService $service): int
    {
        $targetDate = $this->option('date') ?: Carbon::today('Asia/Jakarta')->toDateString();
        $targetTime = $this->option('time');

        $this->info("Menjalankan otomasi tata tertib & prestasi untuk tanggal {$targetDate} (Filter Waktu: ".($targetTime ?: 'ALL').')...');

        // 1. Jam 11.00 WIB: Prestasi Shalat Dhuha Bersama HADIR (R1.2, 1 hari 1x, tanpa approve admin)
        // Mendukung alias '10:00' untuk backward compatibility
        if (in_array($targetTime, ['11:00', '11', '10:00', '10']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- [11:00 WIB] Memproses R1.2: Prestasi Shalat Dhuha Bersama HADIR ---');
            $res11 = $service->processDhuhaPresenceAchievement($targetDate);
            $this->info($res11['message']);
        }

        // 2. Jam 14.00 WIB: Pelanggaran ALPA Presensi Utama (P1.3, 1 hari 1x, tanpa approve admin)
        // Mendukung alias '11:30' untuk backward compatibility
        if (in_array($targetTime, ['14:00', '14', '11:30', '11.30']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- [14:00 WIB] Memproses P1.3: Pelanggaran ALPA Presensi Utama (Terlambat/Alpa tanpa ada keterangan) ---');
            $resAlpa = $service->processDailyAlpaViolation($targetDate);
            $this->info($resAlpa['message']);
        }

        // 3. Jam 14.00 WIB: Pelanggaran ALPA Berturut-turut 3 Hari (P1.7, 1x seminggu, tanpa approve admin)
        // Mendukung alias '08:00' untuk backward compatibility
        if (in_array($targetTime, ['14:00', '14', '08:00', '08', '8']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- [14:00 WIB] Memproses P1.7: Pelanggaran Tidak Masuk Selama 3 Hari Berturut-turut Tanpa Keterangan ---');
            $resAlpa3 = $service->processThreeDaysConsecutiveAlpaViolation($targetDate);
            $this->info($resAlpa3['message']);
        }

        // 4. Jam 14.00 WIB: Prestasi Shalat Dzuhur/Jum'at Berjamaah HADIR (R1.1, 1 hari 1x, tanpa approve admin)
        if (in_array($targetTime, ['14:00', '14']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- [14:00 WIB] Memproses R1.1: Prestasi Shalat Dzuhur/Jum\'at Berjamaah HADIR ---');
            $resDzuhur = $service->processDzuhurPresenceAchievement($targetDate);
            $this->info($resDzuhur['message']);
        }

        // 5. Jam 14.30 WIB: Prestasi Kegiatan Keputrian HADIR (R1.6, 1 hari 1x, tanpa approve admin)
        if (in_array($targetTime, ['14:30', '14.30']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- [14:30 WIB] Memproses R1.6: Prestasi Melaksanakan Kegiatan Keputrian HADIR ---');
            $resKeputrian = $service->processKeputrianPresenceAchievement($targetDate);
            $this->info($resKeputrian['message']);
        }

        // 6. Setoran Hafalan Al-Qur'an Juz 30 (>= 20 Ayat) -> Prestasi R1.3 (tanpa approve admin)
        if (in_array($targetTime, ['tahfidz']) || empty($targetTime) || $targetTime === 'all') {
            $this->info('--- Memproses R1.3: Prestasi Setoran Hafalan Al-Qur\'an Juz 30 (>= 20 Ayat Sekali Duduk) ---');
            $resTahfidz = $service->processTahfidzAchievements($targetDate);
            $this->info($resTahfidz['message']);
        }

        $this->info('Otomasi tata tertib & prestasi selesai dijalankan.');

        return self::SUCCESS;
    }
}
