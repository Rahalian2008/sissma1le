<?php

namespace App\Console\Commands;

use App\Models\SchoolBell;
use App\Models\SchoolSetting;
use Illuminate\Console\Command;

class GenerateSchoolBellScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bell:generate-schedule {--force : Timpa dan hapus seluruh jadwal bel yang ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate jadwal bel sekolah terpadu SMAN 1 Lengkong TP 2026/2027 lengkap dengan Murottal Al-Qur\'an dan Lagu Nasional';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $count = SchoolBell::count();
        if ($count > 0 && ! $force) {
            if (! $this->confirm("Terdapat {$count} jadwal bel sekolah yang sudah terpasang. Apakah Anda yakin ingin menimpa seluruh jadwal?", false)) {
                $this->info('Operasi dibatalkan. Tidak ada jadwal yang diubah.');

                return self::SUCCESS;
            }
        }

        $this->info('Menginisialisasi jadwal bel resmi SMAN 1 Lengkong TP 2026/2027...');

        SchoolBell::query()->delete();
        SchoolBell::ensureDefaultSchedules(force: true);

        $newCount = SchoolBell::count();
        SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');

        $this->info("Berhasil men-generate {$newCount} jadwal bel sekolah terpadu:");
        $this->line('- Murottal Pra-KBM (06.05 - 06.25 WIB) 20 menit: Senin s.d Jumat berbeda surat');
        $this->line('- Bel Pembuka Kegiatan Pagi (06.30 WIB): Upacara, Senam, Literasi Wali Kelas, Shalat Dhuha');
        $this->line('- Istirahat Ke-1: Bel + 2 Lagu Nasional berbeda setiap hari (Total 10 lagu nasional terdistribusi tanpa kembar)');
        $this->line('- Istirahat Ke-2: Bel + Murottal 15 menit (Ishoma Dzuhur & Persiapan Shalat Jumat)');
        $this->line('- Bel Pulang Sekolah: Bel + Murottal 15 menit penutup pembelajaran');
        $this->line('- Seluruh jam bel pergantian jam pelajaran (KBM) sesuai dokumen resmi jadwal sekolah.');

        return self::SUCCESS;
    }
}
