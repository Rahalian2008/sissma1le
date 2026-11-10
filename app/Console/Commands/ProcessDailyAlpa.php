<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Student;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:process-alpa')]
#[Description('Proses pencatatan data presensi ALPA siswa yang belum absen berdasarkan Jam Operasional Presensi Harian')]
class ProcessDailyAlpa extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService): int
    {
        $today = Carbon::today('Asia/Jakarta');
        $dateStr = $today->toDateString();

        $schedule = AttendanceService::getScheduleForDay($today);
        if (! $schedule['is_active'] || ! empty($schedule['is_holiday'])) {
            $holidayName = $schedule['holiday_name'] ?? 'Bukan hari aktif';
            $this->info("Hari ini libur/non-aktif ({$holidayName}). Proses ALPA dilewati.");

            return self::SUCCESS;
        }

        $cutoff = $schedule['in_late_cutoff'] ?? '11:00';
        $dayName = $schedule['day_name'] ?? ucfirst($schedule['day']);
        $this->info("Memulai pengecekan ALPA otomatis cutoff {$cutoff} WIB ({$dayName}) untuk tanggal: {$dateStr}");

        $students = Student::where('is_active', true)->get();
        $processedCount = 0;

        foreach ($students as $student) {
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', $dateStr)
                ->first();

            if (! $attendance) {
                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'student_name' => $student->name,
                    'class_id' => $student->class_id,
                    'date' => $dateStr,
                    'time' => null,
                    'method' => 'SISTEM',
                    'status' => 'ALPA',
                    'verification_status' => 'DIVERIFIKASI',
                    'notes' => "Otomatis ALPA oleh sistem (belum presensi pada jam operasional hari {$dayName})",
                    'approved_by' => 1,
                ]);

                $processedCount++;
            }
        }

        $this->info("Selesai memproses ALPA Presensi Utama. Total {$processedCount} siswa diproses.");

        return self::SUCCESS;
    }
}
