<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Cutoff Presensi Utama Jam 11:00 WIB: otomatis Alpa bagi yang belum absen
Schedule::command('attendance:process-alpa')->timezone('Asia/Jakarta')->dailyAt('11:00');

// 2. Cutoff Shalat Dhuha Jam 09:00 WIB: otomatis Alpa bagi yang belum absen Dhuha
Schedule::command('attendance:process-dhuha-alpa')->timezone('Asia/Jakarta')->dailyAt('09:00');

// 3. Otomasi Jam 11:00 WIB: Prestasi Shalat Dhuha Bersama HADIR (R1.2, 1 hari 1x, tanpa approve admin)
Schedule::command('discipline:process-automations --time=11:00')->timezone('Asia/Jakarta')->dailyAt('11:00');

// 4. Cutoff Shalat Dzuhur / Jum'at Jam 13:30 WIB: otomatis Alpa bagi yang belum absen Dzuhur/Jum'at
Schedule::command('attendance:process-dzuhur-alpa')->timezone('Asia/Jakarta')->dailyAt('13:30');

// 5. Cutoff Kegiatan Keputrian Jam 14:00 WIB: otomatis Alpa bagi siswi yang belum absen Keputrian
Schedule::command('attendance:process-keputrian-alpa')->timezone('Asia/Jakarta')->dailyAt('14:00');

// 6. Otomasi Jam 14:00 WIB:
//    - Pelanggaran Siswa ALPA Presensi Utama (P1.3, 1 hari 1x, tanpa approve admin)
//    - Pelanggaran ALPA 3 Hari Berturut-turut (P1.7, 1x seminggu, tanpa approve admin)
//    - Prestasi Shalat Dzuhur/Jum'at Berjamaah HADIR (R1.1, 1 hari 1x, tanpa approve admin)
Schedule::command('discipline:process-automations --time=14:00')->timezone('Asia/Jakarta')->dailyAt('14:00');

// 7. Otomasi Jam 14:30 WIB: Prestasi Kegiatan Keputrian HADIR (R1.6, 1 hari 1x, tanpa approve admin)
Schedule::command('discipline:process-automations --time=14:30')->timezone('Asia/Jakarta')->dailyAt('14:30');

// 8. Cutoff Presensi Ekstrakurikuler Jam 17:00 WIB: otomatis Alpa bagi anggota ekstra yang belum absen
Schedule::command('attendance:process-extra-alpa')->timezone('Asia/Jakarta')->dailyAt('17:00');

// 9. Generasi otomatis Raport Digital AI setiap hari Jum'at jam 23:30 WIB
Schedule::command('raport:generate-ai')->timezone('Asia/Jakarta')->fridays()->at('23:30');
