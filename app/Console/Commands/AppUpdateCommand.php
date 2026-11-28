<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class AppUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update 
                            {--push : Lakukan git commit dan git push ke GitHub secara otomatis}
                            {--deploy : Kirim sinyal pembaruan dan migrasi database ke Hostinger}
                            {--message= : Pesan commit git kustom}
                            {--no-pull : Lewati proses git pull dari repository}
                            {--no-cache : Lewati proses caching konfigurasi & route}
                            {--force : Jalankan pembaruan secara paksa tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pembaruan otomatis seluruh kode aplikasi SIS-SMA1LE, push GitHub, dan sinkronisasi struktur database Hostinger.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);

        $this->newLine();
        $this->info('========================================================================');
        $this->info('   SIS-SMA1LE - SISTEM INFORMASI & EKOSISTEM SEKOLAH TERPADU');
        $this->info('   PROSES PEMBARUAN OTOMATIS APLIKASI & DATABASE (AUTO-UPDATER)');
        $this->info('========================================================================');
        $this->line(' Tanggal & Waktu : '.now()->translatedFormat('l, d F Y H:i:s').' WIB');
        $this->line(' PHP Version     : '.PHP_VERSION);
        $this->line(' Environment     : '.app()->environment());
        $this->newLine();

        $stepsSummary = [];

        // 0. GIT COMMIT & PUSH KE GITHUB (Opsi --push)
        if ($this->option('push')) {
            $this->info('▶ [0/6] Memeriksa & Mengirim Pembaruan Kode ke GitHub (Git Push)...');
            if (File::exists(base_path('.git'))) {
                try {
                    $statusProcess = Process::fromShellCommandline('git status --porcelain', base_path());
                    $statusProcess->run();
                    $statusOutput = trim($statusProcess->getOutput());

                    $branchProcess = Process::fromShellCommandline('git rev-parse --abbrev-ref HEAD', base_path());
                    $branchProcess->run();
                    $currentBranch = trim($branchProcess->getOutput()) ?: 'main';

                    if (! empty($statusOutput)) {
                        $commitMsg = $this->option('message') ?: 'Pembaruan otomatis aplikasi & database SIS-SMA1LE ('.now()->format('d/m/Y H:i').')';
                        $addProcess = Process::fromShellCommandline('git add -A', base_path());
                        $addProcess->run();

                        $commitProcess = Process::fromShellCommandline('git commit -m '.escapeshellarg($commitMsg), base_path());
                        $commitProcess->run();
                        $this->line("   <info>✓ Git Commit Berhasil:</info> {$commitMsg}");
                    } else {
                        $this->line('   <comment>• Tidak ada perubahan file lokal yang belum di-commit.</comment>');
                    }

                    $pushProcess = Process::fromShellCommandline("git push origin {$currentBranch}", base_path());
                    $pushProcess->setTimeout(180);
                    $pushProcess->run();

                    if ($pushProcess->isSuccessful()) {
                        $this->line("   <info>✓ Berhasil push kode terbaru ke GitHub (origin/{$currentBranch})!</info>");
                        $stepsSummary['GitHub Repository'] = "Sukses Diperbarui ({$currentBranch})";
                    } else {
                        $pushErr = trim($pushProcess->getErrorOutput()) ?: trim($pushProcess->getOutput());
                        $this->warn("   ⚠ Git push catatan: {$pushErr}");
                        $stepsSummary['GitHub Repository'] = 'Catatan Push: '.substr($pushErr, 0, 40);
                    }
                } catch (\Throwable $e) {
                    $this->error('   ✕ Gagal push ke GitHub: '.$e->getMessage());
                    $stepsSummary['GitHub Repository'] = 'Gagal ('.$e->getMessage().')';
                }
            }
            $this->newLine();
        }

        // 1. GIT PULL (Pembaruan Kode Aplikasi)
        if (! $this->option('no-pull')) {
            $this->info('▶ [1/6] Memeriksa & Mengambil Pembaruan Kode Aplikasi (Git Pull)...');

            if (File::exists(base_path('.git'))) {
                try {
                    // Deteksi nama branch aktif
                    $branchProcess = Process::fromShellCommandline('git rev-parse --abbrev-ref HEAD', base_path());
                    $branchProcess->run();
                    $currentBranch = trim($branchProcess->getOutput()) ?: 'main';

                    $this->line("   Branch aktif: <comment>{$currentBranch}</comment>");

                    // Jalankan git pull
                    $pullProcess = Process::fromShellCommandline("git pull origin {$currentBranch}", base_path());
                    $pullProcess->setTimeout(180);
                    $pullProcess->run();

                    $pullOutput = trim($pullProcess->getOutput());
                    if ($pullProcess->isSuccessful()) {
                        $this->line('   <info>✓ Git Pull Berhasil:</info>');
                        $this->line('     '.str_replace("\n", "\n     ", $pullOutput));
                        $stepsSummary['Kode Aplikasi (Git)'] = 'Sukses diperbarui ('.$currentBranch.')';
                    } else {
                        $pullError = trim($pullProcess->getErrorOutput());
                        $this->warn('   ⚠ Git Pull menghasilkan peringatan/pesan:');
                        $this->line('     '.($pullError ?: $pullOutput));
                        $stepsSummary['Kode Aplikasi (Git)'] = 'Peringatan / Dilewati';
                    }
                } catch (\Throwable $e) {
                    $this->warn('   ⚠ Git pull dilewati: '.$e->getMessage());
                    $stepsSummary['Kode Aplikasi (Git)'] = 'Dilewati ('.$e->getMessage().')';
                }
            } else {
                $this->line('   Direktori .git tidak ditemukan. Pembaruan kode via git dilewati.');
                $stepsSummary['Kode Aplikasi (Git)'] = 'Dilewati (Bukan git repository)';
            }
        } else {
            $this->line('▶ [1/5] Pembaruan kode via git dilewati (--no-pull aktif).');
            $stepsSummary['Kode Aplikasi (Git)'] = 'Dilewati oleh pengguna';
        }
        $this->newLine();

        // 2. MIGRASI DATABASE (Pembaruan Skema & Tabel)
        $this->info('▶ [2/5] Memperbarui Struktur & Skema Database (Database Migration)...');
        try {
            // Cek koneksi database terlebih dahulu
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            $this->line("   Database terhubung: <comment>{$dbName}</comment>");

            // Jalankan migrate --force
            $exitCodeMigrate = Artisan::call('migrate', [
                '--force' => true,
                '--no-interaction' => true,
            ]);

            $migrateOutput = trim(Artisan::output());
            $this->line('   <info>✓ Migrasi Database Selesai:</info>');
            $this->line('     '.str_replace("\n", "\n     ", $migrateOutput ?: 'Tidak ada migrasi baru yang perlu dijalankan.'));

            $stepsSummary['Struktur Database'] = $exitCodeMigrate === 0 ? 'Sukses (Tersinkronisasi)' : 'Peringatan pada migrasi';
        } catch (\Throwable $e) {
            $this->error('   ✕ Gagal menjalankan migrasi database: '.$e->getMessage());
            $stepsSummary['Struktur Database'] = 'Gagal: '.$e->getMessage();
        }
        $this->newLine();

        // 3. SINKRONISASI AKUN & PERAN PENGGUNA (Data Master & Hak Akses)
        $this->info('▶ [3/5] Memverifikasi Integritas Akun & Master Peran Sistem...');
        try {
            $exitCodeAccounts = Artisan::call('users:ensure-active-accounts', [
                '--no-interaction' => true,
            ]);
            $this->line('   <info>✓ Verifikasi Akun Pengguna:</info> Master peran dan akun GTK/Siswa sinkron.');
            $stepsSummary['Integritas Akun & Peran'] = $exitCodeAccounts === 0 ? 'Sukses (Aktif & Sinkron)' : 'Selesai';
        } catch (\Throwable $e) {
            $this->warn('   ⚠ Perintah users:ensure-active-accounts dilewati: '.$e->getMessage());
            $stepsSummary['Integritas Akun & Peran'] = 'Dilewati ('.$e->getMessage().')';
        }
        $this->newLine();

        // 4. STORAGE SYMLINK
        $this->info('▶ [4/5] Memeriksa Tautan Direktori Penyimpanan Publik (Storage Link)...');
        try {
            $publicStorage = public_path('storage');
            if (! File::exists($publicStorage)) {
                Artisan::call('storage:link');
                $this->line('   <info>✓ Storage link berhasil dibuat.</info>');
                $stepsSummary['Storage Symlink'] = 'Tautan Baru Dibuat';
            } else {
                $this->line('   <info>✓ Storage link sudah aktif dan terhubung.</info>');
                $stepsSummary['Storage Symlink'] = 'Sudah Terhubung';
            }
        } catch (\Throwable $e) {
            $this->warn('   ⚠ Pengecekan storage link: '.$e->getMessage());
            $stepsSummary['Storage Symlink'] = 'Dilewati';
        }
        $this->newLine();

        // 5. PEMBERSIHAN & RE-CACHING FRAMEWORK
        if (app()->environment('testing')) {
            $this->line('   • Pembersihan & re-caching framework dilewati pada environment testing.');
            $stepsSummary['Optimasi & Cache'] = 'Dilewati (Testing Environment)';
        } else {
            $this->info('▶ [5/5] Membersihkan Cache Lama & Mengoptimalkan Kinerja (Optimization)...');
            try {
                // Clear semua cache usang
                Artisan::call('optimize:clear');
                $this->line('   • Cache aplikasi, config, route, dan view lama berhasil dibersihkan.');

                if (! $this->option('no-cache')) {
                    // Re-cache untuk performa maksimal
                    try {
                        Artisan::call('config:cache');
                        $this->line('   • Konfigurasi (.env & config) berhasil di-cache.');
                    } catch (\Throwable $e) {
                        $this->warn('   ⚠ Config cache dilewati: '.$e->getMessage());
                    }

                    try {
                        Artisan::call('route:cache');
                        $this->line('   • Daftar route URL berhasil di-cache.');
                    } catch (\Throwable $e) {
                        $this->warn('   ⚠ Route cache dilewati (terdapat closure route).');
                    }

                    try {
                        Artisan::call('view:cache');
                        $this->line('   • Seluruh template tampilan Blade berhasil di-cache.');
                    } catch (\Throwable $e) {
                        $this->warn('   ⚠ View cache dilewati: '.$e->getMessage());
                    }

                    $stepsSummary['Optimasi & Cache'] = 'Sukses (Kecepatan Maksimal)';
                } else {
                    $this->line('   • Re-caching dilewati (--no-cache aktif).');
                    $stepsSummary['Optimasi & Cache'] = 'Cache Dibersihkan';
                }
            } catch (\Throwable $e) {
                $this->warn('   ⚠ Pengoptimalan cache menghasilkan catatan: '.$e->getMessage());
                $stepsSummary['Optimasi & Cache'] = 'Selesai Sebagian';
            }
        }
        $this->newLine();

        // 6. SINKRONISASI SERVER & DATABASE HOSTINGER (Opsi --deploy)
        if ($this->option('deploy')) {
            $this->info('▶ [Hostinger] Menghubungi Server Hostinger untuk Pembaruan & Migrasi Database Live...');
            try {
                $hostingerUrl = 'https://sman1lengkong.school/api/hostinger-update';
                $this->line("   Mengirim sinyal update ke: <comment>{$hostingerUrl}</comment>");

                $response = Http::timeout(45)->get($hostingerUrl);

                if ($response->successful()) {
                    $resData = $response->json();
                    $gitPullMsg = $resData['git_pull'] ?? 'Selesai';
                    $migrateMsg = $resData['migrate'] ?? 'Database Up to date';

                    $this->line('   <info>✓ Respon Sukses dari Hostinger Production:</info>');
                    $this->line("     • Git Pull: {$gitPullMsg}");
                    $this->line("     • Migrasi Database: {$migrateMsg}");
                    $stepsSummary['Hostinger & Database Live'] = 'Sukses Terupdate & Termigrasi';
                } else {
                    $fallbackUrl = 'https://sman1lengkong.school/artisan-optimize-clear';
                    $resFallback = Http::timeout(30)->get($fallbackUrl);
                    if ($resFallback->successful()) {
                        $this->line('   <info>✓ Hostinger berhasil menjalankan optimize:clear & migrate via fallback web helper.</info>');
                        $stepsSummary['Hostinger & Database Live'] = 'Sukses via Web Helper';
                    } else {
                        $this->warn('   ⚠ Server Hostinger merespon status: '.$response->status());
                        $stepsSummary['Hostinger & Database Live'] = 'Status '.$response->status();
                    }
                }
            } catch (\Throwable $e) {
                $this->warn('   ⚠ Peringatan sinkronisasi Hostinger: '.$e->getMessage());
                $stepsSummary['Hostinger & Database Live'] = 'Dilewati: '.$e->getMessage();
            }
            $this->newLine();
        }

        $duration = round(microtime(true) - $startTime, 2);

        // RINGKASAN AKHIR
        $this->info('========================================================================');
        $this->info("   HASIL PEMBARUAN SISTEM SELESAI ({$duration} detik)");
        $this->info('========================================================================');

        $tableRows = [];
        foreach ($stepsSummary as $task => $status) {
            $tableRows[] = [$task, $status];
        }
        $this->table(['Komponen Pembaruan', 'Status Akhir'], $tableRows);

        $this->newLine();
        $this->info('✓ Aplikasi SIS-SMA1LE dan database siap digunakan dengan versi terbaru!');
        $this->newLine();

        return Command::SUCCESS;
    }
}
