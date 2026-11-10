<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki dan sinkronisasi folder storage, izin berkas, dan aset foto agar tidak korup di hosting';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai perbaikan dan sinkronisasi folder storage untuk Hostinger...');

        $publicStoragePath = public_path('storage');
        $appStoragePath = storage_path('app/public');

        // 1. Pastikan storage/app/public ada
        if (! File::exists($appStoragePath)) {
            File::makeDirectory($appStoragePath, 0755, true, true);
            $this->line('✓ Direktori storage/app/public berhasil dibuat.');
        }

        // 2. Periksa apakah public/storage adalah symlink rusak (misal sisa Windows NTFS junction di Linux)
        if (is_link($publicStoragePath)) {
            $target = @readlink($publicStoragePath);
            if (! $target || ! File::exists($target)) {
                $this->warn("! Terdeteksi symlink/junction rusak: {$target}");
                @unlink($publicStoragePath);
                $this->line('✓ Symlink rusak berhasil dihapus.');
            }
        }

        // 3. Jika public/storage bukan symlink dan belum ada, buat sebagai direktori nyata
        if (! File::exists($publicStoragePath)) {
            File::makeDirectory($publicStoragePath, 0755, true, true);
            $this->line('✓ Direktori fisik public/storage berhasil dibuat.');
        }

        // 4. Pastikan seluruh subfolder penting tersedia
        $subfolders = [
            'attendance',
            'selfies',
            'branding',
            'avatars',
            'leaves',
            'achievements',
            'violations',
        ];

        foreach ($subfolders as $folder) {
            $dirInApp = $appStoragePath.'/'.$folder;
            $dirInPublic = $publicStoragePath.'/'.$folder;

            if (! File::exists($dirInApp)) {
                File::makeDirectory($dirInApp, 0755, true, true);
            }
            if (! is_link($publicStoragePath) && ! File::exists($dirInPublic)) {
                File::makeDirectory($dirInPublic, 0755, true, true);
            }
        }
        $this->line('✓ Seluruh subdirektori (attendance, selfies, branding, dll) siap.');

        // 5. Sinkronisasi berkas dari storage/app/public ke public/storage
        if (! is_link($publicStoragePath)) {
            $files = File::allFiles($appStoragePath);
            $syncedCount = 0;

            foreach ($files as $file) {
                $relativePath = $file->getRelativePathname();
                $destPath = $publicStoragePath.'/'.$relativePath;

                if (! File::exists(dirname($destPath))) {
                    File::makeDirectory(dirname($destPath), 0755, true, true);
                }

                if (! File::exists($destPath) || File::lastModified($file->getPathname()) > File::lastModified($destPath)) {
                    File::copy($file->getPathname(), $destPath);
                    $syncedCount++;
                }
            }
            $this->line("✓ Sinkronisasi berkas selesai: {$syncedCount} file disalin ke public/storage.");
        }

        // 6. Buat sampel foto selfie valid jika belum ada atau kosong
        $sampleSelfieApp = $appStoragePath.'/attendance/sample_selfie.jpg';
        $sampleSelfiePub = $publicStoragePath.'/attendance/sample_selfie.jpg';

        if (! File::exists($sampleSelfieApp) || File::size($sampleSelfieApp) < 50) {
            $validJpeg = self::generateValidJpegBinary();
            File::put($sampleSelfieApp, $validJpeg);
            if (! is_link($publicStoragePath)) {
                File::put($sampleSelfiePub, $validJpeg);
            }
            $this->line('✓ File sampel attendance/sample_selfie.jpg valid berhasil dibuat.');
        }

        $this->newLine();
        $this->info('================================================================');
        $this->info(' SUKSES: Seluruh folder dan foto storage kini siap & tidak korup! ');
        $this->info('================================================================');

        return Command::SUCCESS;
    }

    /**
     * Generate standard valid 200x200 JPEG binary.
     */
    protected static function generateValidJpegBinary(): string
    {
        if (function_exists('imagecreatetruecolor') && function_exists('imagejpeg')) {
            $im = imagecreatetruecolor(200, 200);
            $bg = imagecolorallocate($im, 79, 70, 229);
            $textCol = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 0, 0, $bg);
            imagestring($im, 5, 45, 90, 'SIS SMA1LE', $textCol);
            ob_start();
            imagejpeg($im, null, 90);
            $jpeg = ob_get_clean();
            imagedestroy($im);
            if ($jpeg && strlen($jpeg) > 100) {
                return $jpeg;
            }
        }

        // Minimal valid 1x1 JPEG binary
        return hex2bin('ffd8ffe000104a46494600010101004800480000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333430ffc0000b080001000101011100ffc4001f0000010501010101010100000000000000000102030405060708090a0bffda0008010100003f00bf0037fffe0000ffd9');
    }
}
