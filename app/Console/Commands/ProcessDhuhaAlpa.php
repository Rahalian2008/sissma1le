<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:process-dhuha-alpa')]
#[Description('Proses pencatatan ALPA otomatis bagi siswa yang belum absen Shalat Dhuha hingga jam 09.00 WIB')]
class ProcessDhuhaAlpa extends Command
{
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Memulai pengecekan ALPA otomatis Shalat Dhuha cutoff 09:00 WIB...');
        $count = $attendanceService->processDhuhaCutoffAlpa();
        $this->info("Selesai. Total {$count} siswa dicatat ALPA untuk Shalat Dhuha.");

        return self::SUCCESS;
    }
}
