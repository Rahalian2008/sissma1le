<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

#[Signature('app:import-sissma-activities {--path= : Path to u432977099_sissma1le (4).sql}')]
#[Description('Import dan gantikan data presensi, tatatertib, tahfidz Al-Quran, dan 7 KAIH dari dump sissma1le')]
class ImportSissmaActivities extends Command
{
    /**
     * Target tables to replace.
     *
     * @var list<string>
     */
    protected array $targetTables = [
        // Presensi
        'attendance_locations',
        'attendance_devices',
        'attendances',
        'attendance_selfies',
        'worship_attendances',
        'lms_subject_attendances',
        'lms_subject_attendance_logs',

        // Tatatertib (Pelanggaran & Prestasi)
        'violation_categories',
        'violation_items',
        'violations',
        'achievement_categories',
        'achievement_items',
        'achievements',

        // Hafalan Al-Qur'an
        'quran_tahfidz_records',

        // 7 Kebiasaan Anak Indonesia Hebat (7 KAIH)
        'habits',
        'habit_daily_logs',
        'habit_summaries',
    ];

    public function handle(): int
    {
        $defaultPath = 'C:\\Users\\SERVER 1 SAMN1LE\\Downloads\\u432977099_sissma1le (4).sql';
        $path = $this->option('path') ?: $defaultPath;

        if (! file_exists($path)) {
            $this->error("File dump tidak ditemukan di: {$path}");

            return self::FAILURE;
        }

        $this->info("Memulai proses penggantian data dari: {$path}");

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::statement("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");

            $this->info('Mengosongkan data lama pada tabel target...');
            foreach ($this->targetTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line(" - Truncated table: <comment>{$table}</comment>");
                }
            }

            $this->info('Membaca dan mengeksekusi INSERT statements...');
            $handle = fopen($path, 'r');
            if (! $handle) {
                throw new \RuntimeException("Gagal membuka file {$path}");
            }

            $currentStatement = '';
            $isCollecting = false;
            $executedStatements = 0;
            $currentTable = null;

            while (! feof($handle)) {
                $line = fgets($handle);
                if ($line === false) {
                    break;
                }

                if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $matches)) {
                    $table = $matches[1];
                    if (in_array($table, $this->targetTables, true)) {
                        $isCollecting = true;
                        $currentTable = $table;
                        $currentStatement = $line;
                    } else {
                        $isCollecting = false;
                        $currentTable = null;
                        $currentStatement = '';
                    }
                } elseif ($isCollecting) {
                    $currentStatement .= $line;
                }

                if ($isCollecting && str_ends_with(trim($line), ';')) {
                    DB::unprepared($currentStatement);
                    $executedStatements++;
                    if ($executedStatements % 20 === 0) {
                        $this->line(" -> Dieksekusi {$executedStatements} statements (terakhir: {$currentTable})...");
                    }
                    $isCollecting = false;
                    $currentStatement = '';
                    $currentTable = null;
                }
            }

            fclose($handle);

            $this->info("Berhasil mengeksekusi {$executedStatements} statements.");
        } catch (Throwable $e) {
            $this->error('Terjadi kesalahan saat import data: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info('Verifikasi jumlah baris data baru:');
        $rows = [];
        foreach ($this->targetTables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $rows[] = [$table, number_format($count, 0, ',', '.')];
            }
        }
        $this->table(['Tabel', 'Jumlah Baris Baru'], $rows);

        $this->info('Penggantian data Presensi, Tatatertib, Tahfidz Al-Quran, dan 7 KAIH selesai dengan sukses!');

        return self::SUCCESS;
    }
}
