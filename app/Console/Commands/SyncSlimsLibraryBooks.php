<?php

namespace App\Console\Commands;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncSlimsLibraryBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:sync-slims
                            {--database=u432977099_perpustakaan : Nama database SLiMS}
                            {--username= : Username database SLiMS}
                            {--password= : Password database SLiMS}
                            {--host= : Host database SLiMS}
                            {--covers-dir= : Lokasi folder cover buku SLiMS}
                            {--force : Paksa update data buku yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi dan migrasi katalog buku beserta gambar cover dari database SLiMS ke katalog perpustakaan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $slimsDb = $this->option('database') ?: 'u432977099_perpustakaan';
        $this->info("=== Memulai Sinkronisasi Katalog Perpustakaan dari SLiMS ({$slimsDb}) ===");

        // 1. Deteksi kredensial database SLiMS (dari parameter, file config SLiMS, atau fallback)
        $defaultMysql = config('database.connections.mysql');
        $slimsConfig = $this->resolveSlimsDbConfig();

        $customUser = $this->option('username') ?: env('SLIMS_DB_USERNAME');
        $customPass = $this->option('password') ?: env('SLIMS_DB_PASSWORD');
        $customHost = $this->option('host') ?: env('SLIMS_DB_HOST');

        $credentialsToTry = [];

        // Opsi 1: Kredensial khusus dari CLI / ENV
        if ($customUser) {
            $credentialsToTry[] = [
                'user' => $customUser,
                'pass' => $customPass ?? '',
                'host' => $customHost ?: ($slimsConfig['host'] ?? '127.0.0.1'),
            ];
        }

        // Opsi 2: Kredensial dari file config SLiMS (config/database.php)
        if (! empty($slimsConfig['username'])) {
            $credentialsToTry[] = [
                'user' => $slimsConfig['username'],
                'pass' => $slimsConfig['password'] ?? '',
                'host' => $slimsConfig['host'] ?? '127.0.0.1',
            ];
        }

        // Opsi 3: Kredensial bawaan default MySQL aplikasi
        $credentialsToTry[] = [
            'user' => $defaultMysql['username'],
            'pass' => $defaultMysql['password'],
            'host' => $defaultMysql['host'],
        ];

        // Opsi 4: Kredensial default SLiMS Hostinger
        $credentialsToTry[] = [
            'user' => 'u432977099_perpustakaan',
            'pass' => '=WqNTqqW6Ut',
            'host' => '127.0.0.1',
        ];

        $connected = false;
        $lastError = null;

        foreach ($credentialsToTry as $cred) {
            config(['database.connections.slims' => array_merge($defaultMysql, [
                'host' => $cred['host'],
                'database' => $slimsDb,
                'username' => $cred['user'],
                'password' => $cred['pass'],
            ])]);

            try {
                DB::purge('slims');
                $testCount = DB::connection('slims')->table('biblio')->count();
                $this->info("✓ Berhasil terhubung ke database SLiMS ({$cred['user']}@{$cred['host']}). Ditemukan {$testCount} judul buku.");
                $connected = true;
                break;
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        if (! $connected) {
            $this->error('Gagal terhubung ke database SLiMS: '.$lastError);
            $this->warn("Pastikan database '{$slimsDb}' sudah diimport ke MySQL.");
            $this->warn('Gunakan: php artisan library:sync-slims --username=... --password=...');

            return Command::FAILURE;
        }

        // 2. Deteksi lokasi gambar cover SLiMS
        $coversDir = $this->resolveCoversDirectory($this->option('covers-dir'));
        if ($coversDir && File::isDirectory($coversDir)) {
            $coverFileCount = count(File::files($coversDir));
            $this->info("✓ Direktori cover SLiMS ditemukan: {$coversDir} ({$coverFileCount} file cover).");
        } else {
            $this->warn('! Direktori cover SLiMS tidak ditemukan otomatis. Cover gambar akan dilewati jika file tidak ada.');
        }

        // 3. Siapkan direktori penyimpanan cover di aplikasi
        $storageCoverDir = storage_path('app/public/library/covers');
        $publicCoverDir = public_path('storage/library/covers');
        File::ensureDirectoryExists($storageCoverDir);
        File::ensureDirectoryExists($publicCoverDir);

        // 4. Muat kategori perpustakaan
        $categories = LibraryCategory::all();
        if ($categories->isEmpty()) {
            $this->warn('Kategori perpustakaan belum ada, menjalankan seeder kategori...');
            $this->call('db:seed', ['--class' => 'LibrarySeeder', '--force' => true]);
            $categories = LibraryCategory::all();
        }

        // 5. Query seluruh biblio, authors, publisher, dan item copies dari SLiMS
        $this->info('Mengambil metadata buku dari SLiMS...');
        $biblios = DB::connection('slims')
            ->table('biblio as b')
            ->leftJoin('mst_publisher as p', 'b.publisher_id', '=', 'p.publisher_id')
            ->select(
                'b.biblio_id',
                'b.title',
                'b.sor',
                'b.edition',
                'b.isbn_issn',
                'b.publish_year',
                'b.collation',
                'b.call_number as biblio_call_number',
                'b.classification',
                'b.notes',
                'b.image',
                'b.input_date',
                'p.publisher_name'
            )
            ->orderBy('b.biblio_id')
            ->get();

        // Ambil pemetaan author
        $authorsRaw = DB::connection('slims')
            ->table('biblio_author as ba')
            ->join('mst_author as a', 'ba.author_id', '=', 'a.author_id')
            ->select('ba.biblio_id', 'a.author_name')
            ->get()
            ->groupBy('biblio_id');

        // Ambil pemetaan item (eksemplar fisik)
        $itemsRaw = DB::connection('slims')
            ->table('item')
            ->select('biblio_id', 'item_code', 'call_number', 'location_id')
            ->get()
            ->groupBy('biblio_id');

        $bar = $this->output->createProgressBar($biblios->count());
        $bar->start();

        $syncedCount = 0;
        $coverCopiedCount = 0;
        $totalItemsCount = 0;

        foreach ($biblios as $biblio) {
            $biblioId = $biblio->biblio_id;
            $items = $itemsRaw->get($biblioId, collect());
            $authorList = $authorsRaw->get($biblioId, collect())->pluck('author_name')->filter()->values();

            $author = $authorList->isNotEmpty()
                ? $authorList->implode(', ')
                : (! empty($biblio->sor) ? $biblio->sor : 'Penulis SLiMS / Anonim');

            $publisher = ! empty($biblio->publisher_name) ? $biblio->publisher_name : 'Penerbit Perpustakaan';
            $totalCopies = max(1, $items->count());
            $totalItemsCount += $items->count();

            // Kode buku / barcode
            $firstItem = $items->first();
            $bookCode = ($firstItem && ! empty($firstItem->item_code))
                ? $firstItem->item_code
                : ('SLIMS-'.str_pad($biblioId, 5, '0', STR_PAD_LEFT));

            // Call number
            $callNumber = ! empty($biblio->biblio_call_number)
                ? $biblio->biblio_call_number
                : ($firstItem->call_number ?? ($biblio->classification !== 'NONE' ? $biblio->classification : ''));

            // Tentukan kategori buku berdasarkan DDC / Judul
            $category = $this->determineCategory($biblio, $categories);

            // Pengolahan Cover Gambar
            $coverImagePath = null;
            if (! empty($biblio->image) && $coversDir) {
                $sourceImage = $coversDir.DIRECTORY_SEPARATOR.$biblio->image;
                if (File::exists($sourceImage)) {
                    $targetFileName = $biblio->image;
                    $destStorage = $storageCoverDir.DIRECTORY_SEPARATOR.$targetFileName;
                    $destPublic = $publicCoverDir.DIRECTORY_SEPARATOR.$targetFileName;

                    if (! File::exists($destStorage) || $this->option('force')) {
                        File::copy($sourceImage, $destStorage);
                    }
                    if (! File::exists($destPublic) || $this->option('force')) {
                        File::copy($sourceImage, $destPublic);
                    }

                    $coverImagePath = 'library/covers/'.$targetFileName;
                    $coverCopiedCount++;
                }
            }

            // Ekstrak perkiraan halaman dari collation (misal: "x, 250 hlm.; 25 cm")
            $pagesCount = 100;
            if (! empty($biblio->collation) && preg_match('/(\d+)\s*(hlm|hal|p)/i', $biblio->collation, $m)) {
                $pagesCount = (int) $m[1];
            }

            $synopsis = ! empty($biblio->notes) ? strip_tags($biblio->notes) : 'Koleksi resmi Perpustakaan SMA Negeri 1 Lengkong.';

            // Cari apakah buku dengan judul sama atau ISBN atau kode sama sudah ada
            $existing = LibraryBook::where('book_code', $bookCode)
                ->orWhere(function ($q) use ($biblio) {
                    if (! empty($biblio->isbn_issn) && strlen(trim($biblio->isbn_issn)) > 5) {
                        $q->where('isbn', trim($biblio->isbn_issn));
                    }
                })
                ->orWhere('title', trim($biblio->title))
                ->first();

            // Tentukan slug yang aman dan unik
            if ($existing && ! empty($existing->slug)) {
                $slug = $existing->slug;
            } else {
                $baseSlug = Str::slug($biblio->title ?: ('buku-'.$biblioId));
                $slug = $baseSlug.'-'.$biblioId;
                if (LibraryBook::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$biblioId.'-'.strtolower(Str::random(4));
                }
            }

            // Simpan / update ke database library_books aplikasi
            $attributes = [
                'book_code' => $bookCode,
                'title' => trim($biblio->title),
                'slug' => $slug,
                'book_type' => 'PHYSICAL',
                'author' => $author,
                'publisher' => $publisher,
                'publish_year' => ! empty($biblio->publish_year) ? (int) $biblio->publish_year : (int) date('Y'),
                'isbn' => ! empty($biblio->isbn_issn) ? trim($biblio->isbn_issn) : null,
                'call_number' => $callNumber,
                'category_id' => $category?->id,
                'category_name' => $category?->name,
                'ddc_code' => ($biblio->classification !== 'NONE' && ! empty($biblio->classification)) ? $biblio->classification : $category?->ddc_code,
                'collection_type' => 'Koleksi Umum & Teks',
                'total_copies' => $totalCopies,
                'available_copies' => $totalCopies,
                'location_shelf' => 'Rak Koleksi Utama',
                'rack_location' => 'Lantai 1',
                'synopsis' => $synopsis,
                'description' => $synopsis,
                'page_count' => $pagesCount,
                'pages_count' => $pagesCount,
                'language' => 'Bahasa Indonesia',
                'is_ebook' => false,
                'is_available' => true,
                'is_active' => true,
            ];

            if ($coverImagePath) {
                $attributes['cover_image'] = $coverImagePath;
            }

            if ($existing) {
                if ($this->option('force') || empty($existing->cover_image)) {
                    $existing->update($attributes);
                }
            } else {
                LibraryBook::create($attributes);
            }

            $syncedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('================ SINKRONISASI SELESAI ================');
        $this->info("✓ Total judul buku berhasil diproses : {$syncedCount}");
        $this->info("✓ Total eksemplar fisik tercatat     : {$totalItemsCount}");
        $this->info("✓ Total cover buku berhasil disalin  : {$coverCopiedCount}");
        $this->info('Katalog perpustakaan siap digunakan di menu Perpustakaan.');

        return Command::SUCCESS;
    }

    /**
     * Mencari file config database SLiMS untuk membaca kredensialnya otomatis
     */
    protected function resolveSlimsDbConfig(): array
    {
        $candidates = [
            'C:\\laragon\\www\\slims\\config\\database.php',
            'C:/laragon/www/slims/config/database.php',
            base_path('../slims/config/database.php'),
            base_path('slims/config/database.php'),
            public_path('../slims/config/database.php'),
            dirname(base_path()).'/slims/config/database.php',
            '/home/u432977099/domains/sman1lengkong.school/slims/config/database.php',
            '/home/u432977099/public_html/slims/config/database.php',
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                try {
                    $config = include $path;
                    if (isset($config['nodes']['SLiMS'])) {
                        return $config['nodes']['SLiMS'];
                    }
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
        }

        return [];
    }

    /**
     * Cari folder cover gambar SLiMS di berbagai path yang umum
     */
    protected function resolveCoversDirectory(?string $customPath): ?string
    {
        if ($customPath && File::isDirectory($customPath)) {
            return realpath($customPath);
        }

        $candidates = [
            'C:\\laragon\\www\\slims\\images\\docs',
            'C:/laragon/www/slims/images/docs',
            base_path('../slims/images/docs'),
            base_path('slims/images/docs'),
            public_path('../slims/images/docs'),
            public_path('slims/images/docs'),
            '/home/u432977099/domains/sman1lengkong.school/slims/images/docs',
            '/home/u432977099/public_html/slims/images/docs',
            $_SERVER['HOME'] ?? ''.'/domains/sman1lengkong.school/public_html/slims/images/docs',
        ];

        foreach ($candidates as $cand) {
            if (! empty($cand) && File::isDirectory($cand)) {
                return realpath($cand);
            }
        }

        return null;
    }

    /**
     * Menentukan kategori buku berdasarkan Dewey Decimal Classification (DDC) atau kata kunci judul
     */
    protected function determineCategory(object $biblio, Collection $categories): ?object
    {
        $title = strtoupper($biblio->title ?? '');
        $class = trim($biblio->classification ?? '');

        // 1. Buku Teks Kurikulum Merdeka
        if (str_contains($title, 'KURIKULUM MERDEKA') || str_contains($title, 'MERDEKA BELAJAR')) {
            return $categories->firstWhere('id', 11) ?? $categories->firstWhere('name', 'Buku Teks Kurikulum Merdeka');
        }

        // 2. Berdasarkan DDC (Dewey Decimal Classification)
        if ($class !== '' && $class !== 'NONE' && is_numeric(substr($class, 0, 1))) {
            $firstDigit = substr($class, 0, 1);
            $ddcMapping = [
                '0' => 1,  // Informatika & Literasi Digital
                '1' => 2,  // Filsafat & Pengembangan Diri
                '2' => 3,  // Agama & Budi Pekerti
                '3' => 4,  // Ilmu Sosial & Kewarganegaraan
                '4' => 5,  // Bahasa & Linguistik
                '5' => 6,  // Sains & Matematika Murni
                '6' => 7,  // Teknologi & Ilmu Terapan
                '7' => 8,  // Seni, Budaya & Olahraga
                '8' => 9,  // Kesusastraan & Novel Fiksi
                '9' => 10, // Sejarah & Biografi Tokoh
            ];

            $catId = $ddcMapping[$firstDigit] ?? null;
            if ($catId && ($found = $categories->firstWhere('id', $catId))) {
                return $found;
            }
        }

        // 3. Fallback cerdas berdasarkan kata kunci judul
        if (str_contains($title, 'AGAMA') || str_contains($title, 'ISLAM') || str_contains($title, 'QURAN') || str_contains($title, 'FIQIH') || str_contains($title, 'PABP')) {
            return $categories->firstWhere('id', 3);
        }
        if (str_contains($title, 'INFORMATIKA') || str_contains($title, 'KOMPUTER') || str_contains($title, 'DIGITAL') || str_contains($title, 'TIK')) {
            return $categories->firstWhere('id', 1);
        }
        if (str_contains($title, 'MATEMATIKA') || str_contains($title, 'FISIKA') || str_contains($title, 'KIMIA') || str_contains($title, 'BIOLOGI') || str_contains($title, 'IPA') || str_contains($title, 'SAINS')) {
            return $categories->firstWhere('id', 6);
        }
        if (str_contains($title, 'BAHASA') || str_contains($title, 'INDONESIA') || str_contains($title, 'INGGRIS') || str_contains($title, 'SUNDA') || str_contains($title, 'KAMUS')) {
            return $categories->firstWhere('id', 5);
        }
        if (str_contains($title, 'EKONOMI') || str_contains($title, 'SOSIOLOGI') || str_contains($title, 'GEOGRAFI') || str_contains($title, 'PPKN') || str_contains($title, 'PANCASILA') || str_contains($title, 'IPS') || str_contains($title, 'HUKUM')) {
            return $categories->firstWhere('id', 4);
        }
        if (str_contains($title, 'SENI') || str_contains($title, 'MUSIK') || str_contains($title, 'OLAHRAGA') || str_contains($title, 'PENJAS') || str_contains($title, 'PJOK') || str_contains($title, 'KESEHATAN')) {
            return $categories->firstWhere('id', 8);
        }
        if (str_contains($title, 'NOVEL') || str_contains($title, 'CERITA') || str_contains($title, 'DONGENG') || str_contains($title, 'KOMIK') || str_contains($title, 'PUISI') || str_contains($title, 'SASTRA') || str_contains($title, 'FIKSI')) {
            return $categories->firstWhere('id', 9);
        }
        if (str_contains($title, 'SEJARAH') || str_contains($title, 'BIOGRAFI') || str_contains($title, 'TOKOH') || str_contains($title, 'PAHLAWAN') || str_contains($title, 'ATLAS')) {
            return $categories->firstWhere('id', 10);
        }
        if (str_contains($title, 'BUKU PENDIDIKAN') || str_contains($title, 'KELAS 10') || str_contains($title, 'KELAS 11') || str_contains($title, 'KELAS 12')) {
            return $categories->firstWhere('id', 11);
        }

        return $categories->firstWhere('id', 4) ?? $categories->first();
    }
}
