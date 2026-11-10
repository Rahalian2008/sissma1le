<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:process-dzuhur-alpa')]
#[Description('Proses pencatatan ALPA otomatis bagi siswa yang belum absen Shalat Dzuhur/Jum\'at hingga jam 13.30 WIB')]
class ProcessDzuhurAlpa extends Command
{
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Memulai pengecekan ALPA otomatis Shalat Dzuhur/Jum\'at cutoff 13:30 WIB...');
        $count = $attendanceService->processDzuhurCutoffAlpa();
        $this->info("Selesai. Total {$count} siswa dicatat ALPA untuk Shalat Dzuhur/Jum'at.");

        return self::SUCCESS;
    }
}
