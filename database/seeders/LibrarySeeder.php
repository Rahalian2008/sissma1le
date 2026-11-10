<?php

namespace Database\Seeders;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\LibraryVisitor;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori DDC (Dewey Decimal Classification) Standar Perpusnas
        $categories = [
            ['ddc_code' => '000', 'name' => 'Karya Umum, Komputer & Informasi', 'description' => 'Ilmu komputer, sistem informasi, ensiklopedia, dan pustaka umum.', 'color' => 'cyan', 'icon' => 'cpu'],
            ['ddc_code' => '100', 'name' => 'Filsafat & Psikologi', 'description' => 'Filsafat hidup, psikologi perkembangan remaja, etika, dan logika.', 'color' => 'purple', 'icon' => 'brain'],
            ['ddc_code' => '200', 'name' => 'Agama & Budi Pekerti', 'description' => 'Pendidikan agama Islam, akhlak terpuji, fiqih, dan teologi.', 'color' => 'emerald', 'icon' => 'book-heart'],
            ['ddc_code' => '300', 'name' => 'Ilmu Sosial, Politik & Hukum', 'description' => 'Sosiologi, ekonomi, pendidikan, kewarganegaraan, dan hukum.', 'color' => 'blue', 'icon' => 'users'],
            ['ddc_code' => '400', 'name' => 'Bahasa & Linguistik', 'description' => 'Bahasa Indonesia, Bahasa Inggris, Bahasa Sunda, kamus, dan tata bahasa.', 'color' => 'indigo', 'icon' => 'message-square'],
            ['ddc_code' => '500', 'name' => 'Sains, MIPA & Ilmu Murni', 'description' => 'Matematika, fisika, kimia, biologi, dan astronomi.', 'color' => 'sky', 'icon' => 'atom'],
            ['ddc_code' => '600', 'name' => 'Teknologi & Ilmu Terapan', 'description' => 'Rekayasa teknologi, kesehatan jasmani, prakarya, dan pertanian.', 'color' => 'teal', 'icon' => 'wrench'],
            ['ddc_code' => '700', 'name' => 'Kesenian, Rekreasi & Olahraga', 'description' => 'Seni rupa, musik tradisional, fotografi, dan olahraga.', 'color' => 'amber', 'icon' => 'palette'],
            ['ddc_code' => '800', 'name' => 'Kesusastraan & Novel', 'description' => 'Novel fiksi, antologi puisi, naskah drama, dan sastra modern.', 'color' => 'rose', 'icon' => 'feather'],
            ['ddc_code' => '900', 'name' => 'Sejarah, Geografi & Biografi', 'description' => 'Sejarah peradaban Nusantara, atlas bumi, dan biografi tokoh inspiratif.', 'color' => 'orange', 'icon' => 'compass'],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $created = LibraryCategory::updateOrCreate(['ddc_code' => $cat['ddc_code']], $cat);
            $categoryMap[$cat['ddc_code']] = $created->id;
        }

        // 2. Sample Koleksi Buku (Buku Fisik & E-Book Digital Flip HTML)
        $books = [
            // E-BOOK 1 (Flip HTML Interaktif)
            [
                'book_code' => 'EBOOK-2026-001',
                'title' => 'Modul Ajar Informatika Tingkat Lanjut: Algoritma & Pemrograman Python Modern',
                'author' => 'Tim Pengembang Kurikulum Informatika SMAN 1 Lengkong',
                'publisher' => 'Pusat Kurikulum dan Perbukuan Kemendikdasmen',
                'publish_year' => 2025,
                'isbn' => '978-623-111-001-4',
                'category_id' => $categoryMap['000'],
                'ddc_code' => '005.1',
                'call_number' => '005.1 TIM m',
                'rack_location' => 'Digital Server / Cloud E-Library',
                'total_copies' => 100,
                'available_copies' => 100,
                'book_type' => 'digital',
                'description' => 'Buku teks elektronik resmi Fase F Kurikulum Merdeka yang membahas konsep komputasi berpikir, struktur data lanjutan, paradigma pemrograman modular Python, dan integrasi API AI untuk siswa SMA.',
                'pages_count' => 8,
                'language' => 'Indonesia',
                'source' => 'Kemendikbudristek RI',
                'is_digital_flip' => true,
                'digital_content' => [
                    [
                        'page_num' => 1,
                        'title' => 'Cover & Halaman Judul',
                        'subtitle' => 'E-Book Interaktif Flip HTML',
                        'body_html' => '<div class="text-center py-12 px-6 flex flex-col items-center justify-center min-h-[460px] bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white rounded-2xl shadow-inner border border-indigo-500/20">
                            <span class="px-3 py-1 rounded-full bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 text-xs font-bold uppercase tracking-wider mb-4">Kurikulum Merdeka • Fase F</span>
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2 leading-tight">Informatika Tingkat Lanjut</h1>
                            <p class="text-lg text-indigo-200 font-medium mb-6">Algoritma, Struktur Data & Pemrograman Python Modern</p>
                            <div class="w-16 h-1 bg-cyan-400 rounded-full mb-6"></div>
                            <p class="text-sm text-slate-300">Penyusun: Tim MGMP Informatika SMA Negeri 1 Lengkong</p>
                            <p class="text-xs text-slate-400 mt-1">Edisi Digital Interaktif Terakreditasi 2026</p>
                        </div>',
                    ],
                    [
                        'page_num' => 2,
                        'title' => 'Kata Pengantar & Capaian Pembelajaran',
                        'subtitle' => 'Permendikdasmen No. 13 Tahun 2025',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">Prakata Kepala Sekolah</h2>
                            <p>Puji syukur ke hadirat Tuhan Yang Maha Esa atas hadirnya platform Buku Digital Interaktif Flipbook SMAN 1 Lengkong. Modul ini disusun untuk menuntun peserta didik menguasai keterampilan abad ke-21 melalui computational thinking dan rekayasa perangkat lunak.</p>
                            <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/60 rounded-xl border border-indigo-200 dark:border-indigo-800/80">
                                <h3 class="font-bold text-xs text-indigo-900 dark:text-indigo-200 uppercase mb-1">Capaian Pembelajaran Elemen AP:</h3>
                                <p class="text-xs text-slate-700 dark:text-slate-300">Peserta didik mampu merancang solusi algoritma berparadigma modular, menganalisis kompleksitas waktu, mengimplementasikan struktur data dinamis, dan membangun program aplikatif menggunakan bahasa Python.</p>
                            </div>
                            <p class="text-xs text-slate-500 italic mt-4 text-right">— Kepala Perpustakaan & Laboratorium Komputer</p>
                        </div>',
                    ],
                    [
                        'page_num' => 3,
                        'title' => 'Bab 1: Dasar Pemrograman Berorientasi Objek',
                        'subtitle' => 'Konsep Kelas, Objek, dan Enkapsulasi',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">1.1 Paradigma OOP di Python</h2>
                            <p>Pemrograman Berorientasi Objek (Object-Oriented Programming / OOP) adalah paradigma rekayasa kode yang membungkus data (atribut) dan perilaku (metode) dalam cetak biru bernama <strong>Class</strong>.</p>
                            <div class="bg-slate-900 text-slate-100 rounded-xl p-3.5 font-mono text-xs overflow-x-auto shadow-md">
                                <span class="text-slate-400"># Contoh Definisi Class Buku Perpustakaan</span><br>
                                <span class="text-purple-400">class</span> <span class="text-amber-300">BukuPerpustakaan</span>:<br>
                                &nbsp;&nbsp;<span class="text-purple-400">def</span> <span class="text-blue-400">__init__</span>(<span class="text-cyan-300">self</span>, judul, penulis):<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-cyan-300">self</span>.judul = judul<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-cyan-300">self</span>.penulis = penulis<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-cyan-300">self</span>.dipinjam = <span class="text-rose-400">False</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Dengan enkapsulasi, integritas data inventaris buku perpustakaan sekolah terlindungi dari modifikasi langsung di luar sistem.</p>
                        </div>',
                    ],
                    [
                        'page_num' => 4,
                        'title' => 'Bab 2: Struktur Data Dinamis & Pencarian',
                        'subtitle' => 'Binary Search & Hash Indexing Koleksi Pustaka',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">2.1 Efisiensi Temu Balik Informasi</h2>
                            <p>Katalog buku perpustakaan yang memiliki ribuan judul memerlukan algoritma temu kembali data (Information Retrieval) berkecepatan tinggi dengan kompleksitas \(O(\log n)\).</p>
                            <div class="grid grid-cols-2 gap-3 mt-2">
                                <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                    <div class="font-bold text-xs text-rose-500">Pencarian Linier</div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1">Kompleksitas \(O(n)\). Memeriksa satu per satu nomor buku dari rak pertama hingga terakhir.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800">
                                    <div class="font-bold text-xs text-indigo-600 dark:text-indigo-400">Binary Search</div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1">Kompleksitas \(O(\log n)\). Membagi dua rentang kode DDC yang sudah terurut rapi.</p>
                                </div>
                            </div>
                        </div>',
                    ],
                    [
                        'page_num' => 5,
                        'title' => 'Bab 3: Pemrograman Web & Interaktif Digital',
                        'subtitle' => 'Arsitektur E-Reader Modern',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">3.1 Teknologi Buku Digital Flip HTML</h2>
                            <p>E-Book interaktif mengadopsi transformasi 3D CSS perspective untuk meniru kelenturan lembaran kertas fisik saat disentuh layar ponsel atau diklik mouse di laptop.</p>
                            <ul class="list-disc pl-5 text-xs space-y-1.5 text-slate-700 dark:text-slate-300">
                                <li><strong>3D Perspective:</strong> Menghasilkan kedalaman bayangan lekukan buku saat membuka halaman baru.</li>
                                <li><strong>Responsive Dual-Page:</strong> Menampilkan dua halaman sejajar pada laptop/tablet, dan satu halaman otomatis pada ponsel.</li>
                                <li><strong>Audio Feedback:</strong> Meniru bunyi desau kertas saat dibolak-balik guna memicu pengalaman kognitif membaca yang riil.</li>
                            </ul>
                        </div>',
                    ],
                    [
                        'page_num' => 6,
                        'title' => 'Rangkuman & Proyek Mandiri Siswa',
                        'subtitle' => 'Tugas Praktik Portofolio',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">Uji Pemahaman Portofolio</h2>
                            <p class="text-xs">Selesaikan proyek pemrograman mini berikut pada ruang lab komputer atau komputer mandiri Anda:</p>
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-2">
                                <p class="font-bold">🎯 Instruksi Proyek:</p>
                                <p>1. Buat program Python yang membaca file CSV berisi daftar 20 buku perpustakaan.</p>
                                <p>2. Urutkan buku berdasarkan nomor klasifikasi DDC.</p>
                                <p>3. Buat fitur pencarian buku menggunakan nomor ISBN secara instan.</p>
                            </div>
                            <p class="text-[11px] text-slate-500 text-center mt-6">Koleksi Digital Perpustakaan SMA Negeri 1 Lengkong • Hak Cipta Dilindungi Undang-Undang</p>
                        </div>',
                    ],
                ],
            ],

            // E-BOOK 2 (Flip HTML Interaktif)
            [
                'book_code' => 'EBOOK-2026-002',
                'title' => 'Panduan Karakter Pelajar Pancasila & Budaya Literasi Sekolah Abad 21',
                'author' => 'Tim Penggerak Literasi SMA Negeri 1 Lengkong',
                'publisher' => 'Pustaka Inspirasi Edukasi Jawa Barat',
                'publish_year' => 2026,
                'isbn' => '978-623-222-002-8',
                'category_id' => $categoryMap['300'],
                'ddc_code' => '370.11',
                'call_number' => '370.11 TIM p',
                'rack_location' => 'Digital Server / Cloud E-Library',
                'total_copies' => 100,
                'available_copies' => 100,
                'book_type' => 'digital',
                'description' => 'Buku panduan digital pembiasaan karakter luhur, 7 kebiasaan anak Indonesia hebat, serta strategi gemar membaca dan berpikir kritis di era kecerdasan buatan.',
                'pages_count' => 6,
                'language' => 'Indonesia',
                'source' => 'Pengadaan Mandiri Perpustakaan',
                'is_digital_flip' => true,
                'digital_content' => [
                    [
                        'page_num' => 1,
                        'title' => 'Sampul Buku',
                        'subtitle' => 'Literasi Karakter Abad 21',
                        'body_html' => '<div class="text-center py-12 px-6 flex flex-col items-center justify-center min-h-[460px] bg-gradient-to-br from-emerald-950 via-slate-900 to-teal-900 text-white rounded-2xl shadow-inner border border-emerald-500/20">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-4">Gerakan Literasi Sekolah (GLS)</span>
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2 leading-tight">Panduan Karakter Pelajar Pancasila & Budaya Literasi</h1>
                            <p class="text-sm text-emerald-200 font-medium mb-6">Membentuk Insan Beriman, Bernalar Kritis & Berakhlak Mulia</p>
                            <div class="w-16 h-1 bg-emerald-400 rounded-full mb-6"></div>
                            <p class="text-xs text-slate-300">SMAN 1 Lengkong • Sukabumi</p>
                        </div>',
                    ],
                    [
                        'page_num' => 2,
                        'title' => '6 Dimensi Profil Pelajar Pancasila',
                        'subtitle' => 'Landasan Karakter Unggul',
                        'body_html' => '<div class="space-y-3.5 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 border-b border-emerald-200 dark:border-emerald-800 pb-2">Enam Pilar Utama</h2>
                            <ul class="space-y-2 text-xs">
                                <li class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 font-semibold text-emerald-900 dark:text-emerald-200">1. Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia</li>
                                <li class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">2. Berkebinekaan Global</li>
                                <li class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">3. Bergotong Royong</li>
                                <li class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">4. Mandiri</li>
                                <li class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">5. Bernalar Kritis</li>
                                <li class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">6. Kreatif</li>
                            </ul>
                        </div>',
                    ],
                    [
                        'page_num' => 3,
                        'title' => 'Aktivitas Membaca 15 Menit Setiap Hari',
                        'subtitle' => 'Habit Membaca Berkelanjutan',
                        'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                            <h2 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 border-b border-emerald-200 dark:border-emerald-800 pb-2">Membangun Disiplin Membaca</h2>
                            <p>Membaca selama minimal 15 menit setiap hari di perpustakaan sekolah maupun melalui buku digital terbukti memperkaya perbendaharaan kosakata, menajamkan empati sosial, dan meningkatkan fokus belajar.</p>
                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-800 dark:text-amber-200">
                                💡 <strong>Tips Literasi:</strong> Buat ringkasan atau kutipan inspiratif dari setiap bab yang Anda baca di perpustakaan untuk dimasukkan ke dalam Jurnal Kebiasaan Siswa!
                            </div>
                        </div>',
                    ],
                    [
                        'page_num' => 4,
                        'title' => 'Penutup & Salam Pustaka',
                        'subtitle' => 'Perpustakaan sebagai Jendela Dunia',
                        'body_html' => '<div class="text-center py-10 px-6 space-y-4">
                            <div class="w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-500 mx-auto flex items-center justify-center font-bold text-2xl">📚</div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">"Buku Adalah Sahabat Paling Setia"</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 max-w-sm mx-auto">Manfaatkan koleksi fisik dan digital perpustakaan untuk terus bertumbuh menjadi pembelajar sepanjang hayat.</p>
                        </div>',
                    ],
                ],
            ],

            // BUKU FISIK 1 (Sains MIPA)
            [
                'book_code' => 'B-530-2026-001',
                'title' => 'Fisika SMA/MA Kelas XI: Dinamika Gerak, Termodinamika & Gelombang Mekanik',
                'author' => 'Prof. Dr. Bambang Sudarsono, M.Sc.',
                'publisher' => 'Penerbit Erlangga Mahameru',
                'publish_year' => 2024,
                'isbn' => '978-602-434-118-2',
                'category_id' => $categoryMap['500'],
                'ddc_code' => '530',
                'call_number' => '530 SUD f',
                'rack_location' => 'Rak MIPA 01 - Baris 2',
                'total_copies' => 12,
                'available_copies' => 10,
                'book_type' => 'hybrid',
                'description' => 'Buku pegangan siswa yang mengulas hukum-hukum mekanika Newton, impuls momentum, gerak harmonik sederhana, fluida statis-dinamis, dan gelombang dengan eksperimen laboratorium.',
                'pages_count' => 342,
                'language' => 'Indonesia',
                'source' => 'Pengadaan BOS Kinerja 2025',
                'is_digital_flip' => false,
            ],

            // BUKU FISIK 2 (Sastra & Novel)
            [
                'book_code' => 'B-813-2026-002',
                'title' => 'Laskar Pelangi: Menembus Batas Harapan Belitung',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publish_year' => 2023,
                'isbn' => '978-602-291-002-3',
                'category_id' => $categoryMap['800'],
                'ddc_code' => '813',
                'call_number' => '813 HIR l',
                'rack_location' => 'Rak Sastra & Fiksi 02 - Baris 1',
                'total_copies' => 8,
                'available_copies' => 6,
                'book_type' => 'fisik',
                'description' => 'Kisah inspiratif tentang perjuangan sepuluh anak di Belitung dalam menuntut ilmu di tengah segala keterbatasan fasilitas sekolah desa.',
                'pages_count' => 529,
                'language' => 'Indonesia',
                'source' => 'Hibah Alumni SMAN 1 Lengkong',
                'is_digital_flip' => false,
            ],

            // BUKU FISIK 3 (Sejarah & Geografi)
            [
                'book_code' => 'B-959-2026-003',
                'title' => 'Sejarah Nasional Indonesia: Dari Zaman Prasejarah Hingga Era Reformasi',
                'author' => 'Dr. Sartono Kartodirdjo',
                'publisher' => 'Balai Pustaka',
                'publish_year' => 2022,
                'isbn' => '978-979-407-123-4',
                'category_id' => $categoryMap['900'],
                'ddc_code' => '959.8',
                'call_number' => '959.8 KAR s',
                'rack_location' => 'Rak Sejarah & Sosial 03 - Baris 2',
                'total_copies' => 15,
                'available_copies' => 13,
                'book_type' => 'fisik',
                'description' => 'Rekonstruksi komprehensif peristiwa sejarah pergerakan kebangsaan Indonesia, perang kemerdekaan, proklamasi 17 Agustus 1945, dan dinamika ketatanegaraan.',
                'pages_count' => 488,
                'language' => 'Indonesia',
                'source' => 'Pengadaan Perpustakaan Sekolah',
                'is_digital_flip' => false,
            ],

            // BUKU FISIK 4 (Bahasa & Budaya Sunda)
            [
                'book_code' => 'B-499-2026-004',
                'title' => 'Kamus Undak Usuk Basa Sunda & Paribasa Pasundan Lengkap',
                'author' => 'Drs. Budi Rahayu Tamsyah',
                'publisher' => 'Geger Sunten Bandung',
                'publish_year' => 2024,
                'isbn' => '978-979-708-456-7',
                'category_id' => $categoryMap['400'],
                'ddc_code' => '499.221',
                'call_number' => '499.221 TAM k',
                'rack_location' => 'Rak Muatan Lokal 01 - Baris 1',
                'total_copies' => 10,
                'available_copies' => 9,
                'book_type' => 'fisik',
                'description' => 'Kamus rujukan tata krama basa Sunda (Loma, Lemes keur sorangan, Lemes keur batur), babasan, paribasa, sarta pupuh tradisional Jawa Barat.',
                'pages_count' => 264,
                'language' => 'Sunda & Indonesia',
                'source' => 'Dinas Pendidikan Provinsi Jawa Barat',
                'is_digital_flip' => false,
            ],
        ];

        $bookEntities = [];
        foreach ($books as $b) {
            $bookEntities[] = LibraryBook::updateOrCreate(['book_code' => $b['book_code']], $b);
        }

        // 3. Sinkronisasi Anggota Perpustakaan dari Master Data Siswa & Guru
        $students = Student::with('schoolClass')->take(10)->get();
        $teachers = Teacher::take(5)->get();

        $memberIndex = 1;
        $createdMembers = [];

        foreach ($students as $stu) {
            $identifier = $stu->nisn ?: $stu->nis;
            $member = LibraryMember::where('student_id', $stu->id)->first();
            if (! $member && $identifier) {
                $member = LibraryMember::where('identity_number', $identifier)->first();
            }

            if ($member) {
                $member->update([
                    'student_id' => $stu->id,
                    'name' => $stu->name,
                    'identity_number' => $identifier ?: $member->identity_number,
                    'phone' => $stu->phone ?: $member->phone,
                    'address' => $stu->address ?: $member->address,
                    'is_active' => true,
                ]);
            } else {
                $memberCode = LibraryMember::generateUniqueCode('siswa', $stu->id);
                $member = LibraryMember::create([
                    'member_code' => $memberCode,
                    'member_type' => 'siswa',
                    'student_id' => $stu->id,
                    'name' => $stu->name,
                    'identity_number' => $identifier ?: ('SIS-'.$stu->id),
                    'phone' => $stu->phone,
                    'address' => $stu->address,
                    'joined_at' => Carbon::now()->subMonths(rand(1, 12)),
                    'is_active' => true,
                    'notes' => 'Anggota Siswa Kelas '.($stu->schoolClass->name ?? '-'),
                ]);
            }
            $createdMembers[] = $member;
        }

        foreach ($teachers as $tch) {
            $identifier = $tch->nip ?: ('GURU-'.$tch->id);
            $member = LibraryMember::where('teacher_id', $tch->id)->first();
            if (! $member && $identifier) {
                $member = LibraryMember::where('identity_number', $identifier)->first();
            }

            if ($member) {
                $member->update([
                    'teacher_id' => $tch->id,
                    'name' => $tch->name,
                    'identity_number' => $identifier ?: $member->identity_number,
                    'phone' => $tch->phone ?: $member->phone,
                    'is_active' => true,
                ]);
            } else {
                $memberCode = LibraryMember::generateUniqueCode('guru', $tch->id);
                $member = LibraryMember::create([
                    'member_code' => $memberCode,
                    'member_type' => 'guru',
                    'teacher_id' => $tch->id,
                    'name' => $tch->name,
                    'identity_number' => $identifier,
                    'phone' => $tch->phone,
                    'joined_at' => Carbon::now()->subYears(1),
                    'is_active' => true,
                    'notes' => 'Anggota Guru / Tenaga Pendidik',
                ]);
            }
            $createdMembers[] = $member;
        }

        // Tambah Anggota Tamu Manual (Sesuai Syarat User: Bisa dientri manual)
        $manualGuest = LibraryMember::updateOrCreate(
            ['member_code' => 'MBR-U-0001'],
            [
                'member_code' => 'MBR-U-0001',
                'member_type' => 'umum',
                'name' => 'Dra. Hj. Nunung Nurjanah (Pengawas Pustaka)',
                'identity_number' => '3202115509740003',
                'phone' => '081234567890',
                'address' => 'Lengkong, Sukabumi',
                'joined_at' => Carbon::now()->subMonths(2),
                'is_active' => true,
                'notes' => 'Anggota Kehormatan / Peneliti Luar',
            ]
        );
        $createdMembers[] = $manualGuest;

        // Buat Akun Pustakawan Resmi Perpustakaan Sekolah
        $librarianUser = User::firstOrCreate(
            ['email' => 'pustakawan@sma1le.sch.id'],
            [
                'name' => 'Hj. Siti Nurhaliza, S.Pust.',
                'username' => 'PUSTAKA',
                'role' => 'pustakawan',
                'roles' => ['pustakawan'],
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        $librarianMember = LibraryMember::updateOrCreate(
            ['member_code' => 'LIB-PST-00001'],
            [
                'member_type' => 'guru',
                'user_id' => $librarianUser->id,
                'name' => 'Hj. Siti Nurhaliza, S.Pust.',
                'identity_number' => '198205122008012015',
                'phone' => '081398765432',
                'email' => 'pustakawan@sma1le.sch.id',
                'joined_at' => Carbon::now()->subYears(2),
                'is_active' => true,
                'notes' => 'Kepala Pengelola Perpustakaan Sekolah',
            ]
        );
        $createdMembers[] = $librarianMember;

        // 4. Sample Transaksi Sirkulasi Peminjaman & Pengembalian
        $adminUser = User::where('role', 'admin')->orWhere('role', 'super_admin')->first();
        $adminId = $librarianUser->id ?? $adminUser?->id;

        if (count($bookEntities) >= 4 && count($createdMembers) >= 3) {
            // Pinjaman 1: Masih Aktif (Belum jatuh tempo)
            LibraryLoan::updateOrCreate(
                ['loan_code' => 'LOAN-202610-0001'],
                [
                    'loan_code' => 'LOAN-202610-0001',
                    'book_id' => $bookEntities[2]->id, // Buku Fisik 1
                    'member_id' => $createdMembers[0]->id,
                    'user_id' => $adminId,
                    'loan_date' => Carbon::now()->subDays(3)->toDateString(),
                    'due_date' => Carbon::now()->addDays(4)->toDateString(),
                    'return_date' => null,
                    'status' => 'DIPINJAM',
                    'fine_amount' => 0,
                    'is_fine_paid' => false,
                    'extension_count' => 0,
                    'notes' => 'Peminjaman untuk tugas kelompok Fisika.',
                ]
            );

            // Pinjaman 2: Sudah Dikembalikan Tepat Waktu
            LibraryLoan::updateOrCreate(
                ['loan_code' => 'LOAN-202610-0002'],
                [
                    'loan_code' => 'LOAN-202610-0002',
                    'book_id' => $bookEntities[3]->id, // Buku Fisik 2 (Laskar Pelangi)
                    'member_id' => $createdMembers[1]->id,
                    'user_id' => $adminId,
                    'loan_date' => Carbon::now()->subDays(10)->toDateString(),
                    'due_date' => Carbon::now()->subDays(3)->toDateString(),
                    'return_date' => Carbon::now()->subDays(4)->toDateString(),
                    'status' => 'KEMBALI',
                    'fine_amount' => 0,
                    'is_fine_paid' => true,
                    'extension_count' => 0,
                    'notes' => 'Buku dikembalikan dalam kondisi sangat baik.',
                ]
            );

            // Pinjaman 3: Terlambat & Kena Denda
            LibraryLoan::updateOrCreate(
                ['loan_code' => 'LOAN-202610-0003'],
                [
                    'loan_code' => 'LOAN-202610-0003',
                    'book_id' => $bookEntities[4]->id, // Buku Sejarah
                    'member_id' => $createdMembers[2]->id,
                    'user_id' => $adminId,
                    'loan_date' => Carbon::now()->subDays(12)->toDateString(),
                    'due_date' => Carbon::now()->subDays(5)->toDateString(),
                    'return_date' => null,
                    'status' => 'TERLAMBAT',
                    'fine_amount' => 5000, // 5 hari x Rp 1.000
                    'is_fine_paid' => false,
                    'extension_count' => 1,
                    'notes' => 'Keterlambatan 5 hari. Notifikasi WhatsApp peringatan telah dikirimkan.',
                ]
            );

            // Pinjaman 4: PENGAJUAN ONLINE BARU (Status: MENUNGGU / Antrean Pengajuan)
            LibraryLoan::updateOrCreate(
                ['loan_code' => 'LOAN-REQ-202610-0004'],
                [
                    'loan_code' => 'LOAN-REQ-202610-0004',
                    'book_id' => $bookEntities[2]->id,
                    'member_id' => $createdMembers[1]->id,
                    'user_id' => null,
                    'request_date' => Carbon::now()->subHours(2),
                    'loan_date' => Carbon::today()->toDateString(),
                    'due_date' => Carbon::today()->addDays(7)->toDateString(),
                    'return_date' => null,
                    'status' => 'MENUNGGU',
                    'fine_amount' => 0,
                    'is_fine_paid' => false,
                    'extension_count' => 0,
                    'request_notes' => 'Diajukan mandiri oleh siswa untuk persiapan ujian tengah semester.',
                ]
            );
        }

        // 5. Sample Buku Tamu / Presensi Pengunjung Perpustakaan Hari Ini
        $purposes = [
            'Membaca Buku Digital (Flip HTML)',
            'Meminjam & Mengembalikan Buku Fisik',
            'Mengerjakan Tugas & Riset Portofolio',
            'Membaca Koran & Majalah Perpustakaan',
            'Diskusi Kelompok Literasi',
        ];

        foreach (array_slice($createdMembers, 0, 5) as $i => $m) {
            LibraryVisitor::create([
                'visitor_type' => $m->member_type,
                'member_id' => $m->id,
                'student_id' => $m->student_id,
                'teacher_id' => $m->teacher_id,
                'name' => $m->name,
                'identity_number' => $m->identity_number,
                'class_or_institution' => $m->member_class_or_title,
                'purpose' => $purposes[$i % count($purposes)],
                'visited_at' => Carbon::now()->subHours(rand(1, 5))->subMinutes(rand(5, 50)),
                'notes' => 'Pengunjung aktif terdata di presensi barcode / manual.',
            ]);
        }
    }
}
