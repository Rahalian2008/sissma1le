<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:process-keputrian-alpa')]
#[Description('Proses pencatatan ALPA otomatis bagi siswi yang belum absen Keputrian hingga jam 14.00 WIB')]
class ProcessKeputrianAlpa extends Command
{
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Memulai pengecekan ALPA otomatis Keputrian cutoff 14:00 WIB...');
        $count = $attendanceService->processKeputrianCutoffAlpa();
        $this->info("Selesai. Total {$count} siswi dicatat ALPA untuk Keputrian.");

        return self::SUCCESS;
    }
}
