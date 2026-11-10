<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\AiStudentAnalysisService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('raport:generate-ai {--student= : ID siswa spesifik}')]
#[Description('Generate raport digital AI terbaru secara otomatis dari pembacaan seluruh laporan kegiatan terbaru (Presensi Utama, Dzuhur, Dhuha, Keputrian, Ekstrakurikuler, Prestasi, Pelanggaran, Juz 30, dan 7 KAIH)')]
class GenerateAiRaport extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(AiStudentAnalysisService $aiService): int
    {
        $studentId = $this->option('student');

        if ($studentId) {
            $student = Student::with('schoolClass')->find($studentId);
            if (! $student) {
                $this->error("Siswa dengan ID {$studentId} tidak ditemukan.");

                return self::FAILURE;
            }

            $this->info("Men-generate Raport Digital AI terbaru untuk {$student->name} ({$student->nis})...");
            try {
                $aiService->generateAndSaveStudentReport(
                    student: $student,
                    generatedById: null,
                    generationType: 'SCHEDULED'
                );
                $this->info("✓ Sukses men-generate Raport Digital AI untuk {$student->name}!");

                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error("Gagal men-generate raport: {$e->getMessage()}");

                return self::FAILURE;
            }
        }

        $this->info('Memulai generasi Raport Digital AI terpadu dari pembacaan seluruh laporan kegiatan terbaru untuk semua siswa aktif...');

        $students = Student::where('is_active', true)->with('schoolClass')->get();
        $total = $students->count();

        if ($total === 0) {
            $this->warn('Tidak ada siswa aktif yang ditemukan.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successCount = 0;
        $failedCount = 0;

        foreach ($students as $student) {
            try {
                $aiService->generateAndSaveStudentReport(
                    student: $student,
                    generatedById: null,
                    generationType: 'SCHEDULED'
                );
                $successCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("Gagal generate untuk siswa ID {$student->id} ({$student->name}): {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generasi Raport AI selesai! Sukses: {$successCount}, Gagal: {$failedCount} dari total {$total} siswa.");

        return self::SUCCESS;
    }
}
