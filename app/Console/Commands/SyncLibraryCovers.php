<?php

namespace App\Console\Commands;

use App\Models\LibraryBook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncLibraryCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:sync-covers
                            {--database=u432977099_perpustakaan : Nama database SLiMS}
                            {--force : Paksa timpa file cover yang sudah ada di folder publik}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi berkas cover buku dari storage/app/public ke public/storage dan update field cover_image di database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Memulai Sinkronisasi Berkas Cover Buku Perpustakaan ===');

        $storageCoverDir = storage_path('app/public/library/covers');
        $publicCoverDir = public_path('storage/library/covers');
        $altPublicDir = base_path('public_html/storage/library/covers');

        File::ensureDirectoryExists($storageCoverDir);
        File::ensureDirectoryExists($publicCoverDir);
        if (File::isDirectory(base_path('public_html'))) {
            File::ensureDirectoryExists($altPublicDir);
        }

        // 1. Kumpulkan seluruh file cover yang ada
        $storageFiles = File::isDirectory($storageCoverDir) ? File::files($storageCoverDir) : [];
        $this->info('✓ Jumlah berkas cover di storage/app/public/library/covers : '.count($storageFiles));

        // 2. Salin seluruh file cover ke public/storage/library/covers
        $rootStorageCoverDir = base_path('storage/library/covers');
        File::ensureDirectoryExists($rootStorageCoverDir);

        $copiedCount = 0;
        foreach ($storageFiles as $file) {
            $dest = $publicCoverDir.DIRECTORY_SEPARATOR.$file->getFilename();
            if (! File::exists($dest) || $this->option('force')) {
                @File::copy($file->getPathname(), $dest);
                $copiedCount++;
            }
            $rootDest = $rootStorageCoverDir.DIRECTORY_SEPARATOR.$file->getFilename();
            if (! File::exists($rootDest) || $this->option('force')) {
                @File::copy($file->getPathname(), $rootDest);
            }
            if (File::isDirectory(base_path('public_html'))) {
                $altDest = $altPublicDir.DIRECTORY_SEPARATOR.$file->getFilename();
                if (! File::exists($altDest) || $this->option('force')) {
                    @File::copy($file->getPathname(), $altDest);
                }
            }
        }
        $this->info("✓ Berkas cover disalin/diverifikasi ke folder publik : {$copiedCount}");

        // 3. Pastikan symlink storage:link aktif
        try {
            $this->callSilent('storage:link');
            $this->info('✓ Symlink storage:link berhasil diperiksa/dibuat.');
        } catch (\Throwable $e) {
            $this->warn('! storage:link dilewati: '.$e->getMessage());
        }

        // 4. Update field cover_image pada tabel library_books
        $slimsDb = $this->option('database') ?: 'u432977099_perpustakaan';
        $updatedBooksCount = 0;

        // Coba muat peta cover dari arsip JSON bawaan terlebih dahulu
        $jsonCatalogPath = database_path('data/slims_library_catalog.json');
        $catalogMapById = collect();
        $catalogMapByCode = collect();
        $catalogMapByTitle = collect();

        if (File::exists($jsonCatalogPath)) {
            $catalogData = json_decode(file_get_contents($jsonCatalogPath), true) ?: [];
            foreach ($catalogData as $bData) {
                if (! empty($bData['cover_image'])) {
                    $catalogMapById->put($bData['id'], $bData['cover_image']);
                    $catalogMapByCode->put($bData['book_code'], $bData['cover_image']);
                    $catalogMapByTitle->put(strtoupper(trim($bData['title'])), $bData['cover_image']);
                }
            }
            $this->info('✓ Peta cover dari arsip JSON slims_library_catalog.json dimuat: '.$catalogMapById->count().' cover.');
        }

        // Coba baca dari SLiMS jika koneksi tersedia
        $slimsBiblios = collect();
        try {
            $defaultMysql = config('database.connections.mysql');
            $credentials = [
                ['user' => 'u432977099_perpustakaan', 'pass' => '=WqNTqqW6Ut', 'host' => '127.0.0.1'],
                ['user' => $defaultMysql['username'], 'pass' => $defaultMysql['password'], 'host' => $defaultMysql['host']],
            ];

            foreach ($credentials as $cred) {
                config(['database.connections.slims' => array_merge($defaultMysql, [
                    'host' => $cred['host'],
                    'database' => $slimsDb,
                    'username' => $cred['user'],
                    'password' => $cred['pass'],
                ])]);

                try {
                    DB::purge('slims');
                    $slimsBiblios = DB::connection('slims')
                        ->table('biblio')
                        ->whereNotNull('image')
                        ->where('image', '!=', '')
                        ->select('biblio_id', 'title', 'image')
                        ->get()
                        ->keyBy('biblio_id');
                    break;
                } catch (\Throwable) {
                    // Coba kredensial berikutnya
                }
            }
        } catch (\Throwable) {
            // Abaikan jika SLiMS offline
        }

        $allBooks = LibraryBook::all();
        foreach ($allBooks as $book) {
            $expectedCover = null;

            // Prioritas 1: Dari database SLiMS langsung jika online
            if ($slimsBiblios->has($book->id)) {
                $expectedCover = 'library/covers/'.$slimsBiblios->get($book->id)->image;
            } elseif (preg_match('/SLIMS-(\d+)/', (string) $book->book_code, $m) && $slimsBiblios->has((int) $m[1])) {
                $expectedCover = 'library/covers/'.$slimsBiblios->get((int) $m[1])->image;
            }

            // Prioritas 2: Dari arsip JSON katalog bawaan
            if (! $expectedCover) {
                $expectedCover = $catalogMapById->get($book->id)
                    ?? $catalogMapByCode->get($book->book_code)
                    ?? $catalogMapByTitle->get(strtoupper(trim($book->title)));
            }

            // Normalisasi prefix
            if ($expectedCover) {
                $filename = basename($expectedCover);
                $expectedCover = 'library/covers/'.$filename;

                if ($book->cover_image !== $expectedCover) {
                    $book->update(['cover_image' => $expectedCover]);
                    $updatedBooksCount++;
                }
            }
        }

        $this->info("✓ Total data buku yang diperbarui cover_image-nya di database : {$updatedBooksCount}");
        $totalWithCover = LibraryBook::whereNotNull('cover_image')->where('cover_image', '!=', '')->count();
        $this->info("✓ Total buku yang kini memiliki cover di katalog : {$totalWithCover} dari {$allBooks->count()} judul.");

        $this->info('Sinkronisasi cover selesai. Silakan refresh halaman katalog perpustakaan.');

        return Command::SUCCESS;
    }
}
