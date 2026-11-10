<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:process-extra-alpa')]
#[Description('Proses pencatatan ALPA otomatis bagi anggota ekstrakurikuler yang belum absen hingga jam 17.00 WIB')]
class ProcessExtracurricularAlpa extends Command
{
    public function handle(AttendanceService $attendanceService): int
    {
        $this->info('Memulai pengecekan ALPA otomatis Ekstrakurikuler cutoff 17:00 WIB...');
        $count = $attendanceService->processExtracurricularCutoffAlpa();
        $this->info("Selesai. Total {$count} presensi anggota ekstrakurikuler dicatat ALPA.");

        return self::SUCCESS;
    }
}
