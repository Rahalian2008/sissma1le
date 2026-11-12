<?php

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\SchoolSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);
});

test('user dapat mengakses dashboard perpustakaan', function () {
    $response = $this->actingAs($this->user)->get(route('library.index'));

    $response->assertStatus(200);
    $response->assertSee('Perpustakaan Terpadu', false);
    $response->assertSee('Standar Nasional Perpustakaan', false);
});

test('user dapat melihat katalog buku perpustakaan', function () {
    $cat = LibraryCategory::firstOrCreate(
        ['ddc_code' => '500'],
        ['name' => 'Sains & MIPA', 'color' => 'sky', 'icon' => 'atom']
    );

    $book = LibraryBook::create([
        'book_code' => 'TEST-500-001',
        'title' => 'Fisika Kuantum Eksperimental',
        'author' => 'Dr. Richard',
        'total_copies' => 5,
        'available_copies' => 5,
        'book_type' => 'fisik',
        'category_id' => $cat->id,
        'ddc_code' => '500',
    ]);

    $response = $this->actingAs($this->user)->get(route('library.books.index'));

    $response->assertStatus(200);
    $response->assertSee('Fisika Kuantum Eksperimental');
});

test('user dapat menambah buku baru ke katalog perpustakaan', function () {
    $cat = LibraryCategory::firstOrCreate(
        ['ddc_code' => '000'],
        ['name' => 'Karya Umum & Komputer', 'color' => 'cyan', 'icon' => 'cpu']
    );

    $response = $this->actingAs($this->user)->post(route('library.books.store'), [
        'book_code' => 'LIB-NEW-999',
        'title' => 'Pemrograman Web Modern Laravel & Tailwind',
        'author' => 'Tim Dev',
        'publisher' => 'Pustaka Digital',
        'publish_year' => 2026,
        'category_id' => $cat->id,
        'total_copies' => 10,
        'book_type' => 'digital',
        'is_digital_flip' => 1,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('library_books', [
        'book_code' => 'LIB-NEW-999',
        'title' => 'Pemrograman Web Modern Laravel & Tailwind',
        'is_digital_flip' => 1,
    ]);
});

test('buku baru otomatis dibuatkan slug unik dan tidak menyebabkan query exception field slug', function () {
    $response = $this->actingAs($this->user)->post(route('library.books.store'), [
        'book_code' => 'LIB-2026-0AE9',
        'title' => 'putih',
        'author' => 'asep kuswara',
        'publish_year' => 2026,
        'total_copies' => 1,
        'book_type' => 'fisik',
        'language' => 'Indonesia',
        'source' => 'Pengadaan Sekolah',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('library_books', [
        'book_code' => 'LIB-2026-0AE9',
        'title' => 'putih',
        'slug' => 'putih',
    ]);

    // Tambah buku kedua dengan judul sama untuk verifikasi slug unik
    $response2 = $this->actingAs($this->user)->post(route('library.books.store'), [
        'book_code' => 'LIB-2026-0AE10',
        'title' => 'putih',
        'author' => 'penulis lain',
        'total_copies' => 1,
        'book_type' => 'fisik',
    ]);

    $response2->assertRedirect();
    $this->assertDatabaseHas('library_books', [
        'book_code' => 'LIB-2026-0AE10',
        'slug' => 'putih-1',
    ]);
});

test('user dapat membuka dan membaca e-book dengan reader Flip HTML', function () {
    $book = LibraryBook::create([
        'book_code' => 'EBOOK-FLIP-TEST',
        'title' => 'Buku Digital Flipbook Test',
        'author' => 'Pengarang Test',
        'total_copies' => 1,
        'available_copies' => 1,
        'book_type' => 'digital',
        'is_digital_flip' => true,
        'digital_content' => [
            [
                'page_num' => 1,
                'title' => 'Halaman Pertama Flipbook',
                'body_html' => '<p>Konten halaman pertama flipbook interaktif.</p>',
            ],
            [
                'page_num' => 2,
                'title' => 'Halaman Kedua Flipbook',
                'body_html' => '<p>Konten halaman kedua flipbook interaktif.</p>',
            ],
        ],
    ]);

    $response = $this->actingAs($this->user)->get(route('library.books.flipbook', $book));

    $response->assertStatus(200);
    $response->assertSee('Buku Digital Flipbook Test');
    $response->assertSee('Flip HTML 3D');
    $response->assertSee('Halaman Pertama Flipbook');
    $response->assertDontSee('Unduh / Buka Dokumen');
});

test('user dapat melakukan streaming pdf buku digital', function () {
    Storage::fake('public');
    $pdfContent = '%PDF-1.4 sample pdf content';
    Storage::disk('public')->put('library/ebooks/test_stream.pdf', $pdfContent);

    $book = LibraryBook::create([
        'book_code' => 'STREAM-PDF-001',
        'title' => 'Buku PDF Streaming Test',
        'author' => 'Author Stream',
        'total_copies' => 1,
        'available_copies' => 1,
        'book_type' => 'digital',
        'digital_file_path' => 'library/ebooks/test_stream.pdf',
    ]);

    $response = $this->actingAs($this->user)->get(route('library.books.stream_pdf', $book));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/octet-stream');

    // Cek juga dukungan fallback format base64
    $base64Response = $this->actingAs($this->user)->get(route('library.books.stream_pdf', [$book, 'format' => 'base64']));
    $base64Response->assertStatus(200);
    $base64Response->assertJson([
        'success' => true,
    ]);
});

test('user dapat mencatat peminjaman buku dan mengembalikannya dengan perhitungan denda', function () {
    $book = LibraryBook::create([
        'book_code' => 'LOAN-BOOK-001',
        'title' => 'Matematika Diskrit Lanjutan',
        'author' => 'Prof. Andi',
        'total_copies' => 2,
        'available_copies' => 2,
        'book_type' => 'fisik',
    ]);

    $member = LibraryMember::create([
        'member_code' => 'MBR-TEST-001',
        'member_type' => 'siswa',
        'name' => 'Siswa Penguji Perpustakaan',
        'joined_at' => now(),
    ]);

    // 1. Simpan Peminjaman
    $response = $this->actingAs($this->user)->post(route('library.loans.store'), [
        'book_id' => $book->id,
        'member_id' => $member->id,
        'loan_date' => Carbon::today()->toDateString(),
        'loan_days' => 7,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('library_loans', [
        'book_id' => $book->id,
        'member_id' => $member->id,
        'status' => 'DIPINJAM',
    ]);

    // Stok buku berkurang
    expect($book->fresh()->available_copies)->toBe(1);

    // 2. Kembalikan Buku
    $loan = LibraryLoan::where('book_id', $book->id)->first();
    $returnResponse = $this->actingAs($this->user)->post(route('library.loans.return', $loan));

    $returnResponse->assertRedirect();
    $this->assertDatabaseHas('library_loans', [
        'id' => $loan->id,
        'status' => 'KEMBALI',
    ]);

    // Stok buku kembali
    expect($book->fresh()->available_copies)->toBe(2);
});

test('user dapat mencatat presensi pengunjung perpustakaan', function () {
    $response = $this->actingAs($this->user)->post(route('library.visitors.store'), [
        'visitor_mode' => 'manual',
        'manual_name' => 'Budi Santoso (Tamu Pengawas)',
        'manual_class' => 'Dinas Pendidikan',
        'purpose' => 'Membaca Buku Digital (Flip HTML)',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('library_visitors', [
        'name' => 'Budi Santoso (Tamu Pengawas)',
        'class_or_institution' => 'Dinas Pendidikan',
        'purpose' => 'Membaca Buku Digital (Flip HTML)',
    ]);
});

test('user dapat menyinkronkan siswa dan guru dari master data ke anggota perpustakaan', function () {
    $response = $this->actingAs($this->user)->post(route('library.members.sync'));

    $response->assertRedirect();
    expect(LibraryMember::count())->toBeGreaterThan(0);
});

test('siswa dapat mengajukan peminjaman buku mandiri lewat akun dan melihat di peminjaman saya', function () {
    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $book = LibraryBook::create([
        'book_code' => 'REQ-TEST-001',
        'title' => 'Biologi Sel & Molekuler SMA',
        'author' => 'Dr. Campbell',
        'total_copies' => 3,
        'available_copies' => 3,
        'book_type' => 'fisik',
    ]);

    // 1. Siswa mengajukan peminjaman buku
    $response = $this->actingAs($studentUser)->post(route('library.books.request', $book), [
        'pickup_date' => Carbon::today()->toDateString(),
        'notes' => 'Untuk persiapan lomba OSN Biologi',
    ]);

    $response->assertRedirect(route('library.loans.my'));
    $this->assertDatabaseHas('library_loans', [
        'book_id' => $book->id,
        'status' => 'MENUNGGU',
        'request_notes' => 'Untuk persiapan lomba OSN Biologi',
    ]);

    // 2. Siswa membuka halaman Peminjaman Saya
    $myLoansResponse = $this->actingAs($studentUser)->get(route('library.loans.my'));
    $myLoansResponse->assertStatus(200);
    $myLoansResponse->assertSee('Biologi Sel & Molekuler SMA');
    $myLoansResponse->assertSee('Menunggu Persetujuan');

    // 3. Siswa membuka Kartu Anggota Digital
    $myCardResponse = $this->actingAs($studentUser)->get(route('library.members.my-card'));
    $myCardResponse->assertStatus(200);
    $myCardResponse->assertSee('KARTU ANGGOTA PERPUSTAKAAN');
});

test('pustakawan dapat menyetujui pengajuan peminjaman buku', function () {
    $librarian = User::factory()->create([
        'role' => 'pustakawan',
        'roles' => ['pustakawan'],
    ]);

    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);
    $studentMember = $studentUser->getOrCreateLibraryMember();

    $book = LibraryBook::create([
        'book_code' => 'APPROVE-TEST-001',
        'title' => 'Sejarah Indonesia Modern',
        'author' => 'M.C. Ricklefs',
        'total_copies' => 2,
        'available_copies' => 2,
        'book_type' => 'fisik',
    ]);

    $loan = LibraryLoan::create([
        'loan_code' => 'REQ-HIST-001',
        'book_id' => $book->id,
        'member_id' => $studentMember->id,
        'loan_date' => Carbon::today()->toDateString(),
        'due_date' => Carbon::today()->addDays(7)->toDateString(),
        'status' => 'MENUNGGU',
    ]);

    // Pustakawan menyetujui pengajuan
    $response = $this->actingAs($librarian)->post(route('library.loans.approve', $loan));

    $response->assertRedirect();
    $freshLoan = $loan->fresh();
    expect($freshLoan->status)->toBe('DIPINJAM');
    expect($freshLoan->approved_by)->toBe($librarian->id);
    expect($book->fresh()->available_copies)->toBe(1);
});

test('pustakawan dapat menolak pengajuan peminjaman buku dengan alasan', function () {
    $librarian = User::factory()->create([
        'role' => 'pustakawan',
        'roles' => ['pustakawan'],
    ]);

    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);
    $studentMember = $studentUser->getOrCreateLibraryMember();

    $book = LibraryBook::create([
        'book_code' => 'REJECT-TEST-001',
        'title' => 'Kimia Organik',
        'author' => 'Fessenden',
        'total_copies' => 1,
        'available_copies' => 1,
        'book_type' => 'fisik',
    ]);

    $loan = LibraryLoan::create([
        'loan_code' => 'REQ-KIM-001',
        'book_id' => $book->id,
        'member_id' => $studentMember->id,
        'loan_date' => Carbon::today()->toDateString(),
        'due_date' => Carbon::today()->addDays(7)->toDateString(),
        'status' => 'MENUNGGU',
    ]);

    // Pustakawan menolak pengajuan
    $response = $this->actingAs($librarian)->post(route('library.loans.reject', $loan), [
        'rejection_reason' => 'Buku fisik sedang dalam proses laminasi ulang.',
    ]);

    $response->assertRedirect();
    $freshLoan = $loan->fresh();
    expect($freshLoan->status)->toBe('DITOLAK');
    expect($freshLoan->rejection_reason)->toBe('Buku fisik sedang dalam proses laminasi ulang.');
});

test('siswa biasa tidak dapat mengakses aksi administratif pengelola perpustakaan', function () {
    $studentUser = User::factory()->create([
        'role' => 'siswa',
        'roles' => ['siswa'],
    ]);

    $response = $this->actingAs($studentUser)->get(route('library.books.create'));
    $response->assertStatus(403);
});

test('user dapat mengakses leaderboard rapor dan rangking pengunjung perpustakaan', function () {
    $response = $this->actingAs($this->user)->get(route('library.leaderboard'));

    $response->assertStatus(200);
    $response->assertSee('Rapor & Rangking Pengunjung Perpustakaan', false);
    $response->assertSee('Kunjungan Offline', false);
    $response->assertSee('Kunjungan Online', false);
});

test('user dapat melihat dan mencetak piagam penghargaan keren duta literasi', function () {
    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'TEST-PIAGAM-01'],
        [
            'member_type' => 'siswa',
            'name' => 'Bintang Literasi Pelajar',
            'identity_number' => '0098765432',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->user)->get(route('library.certificate', [
        'member' => $member->id,
        'rank' => 1,
        'predicate' => 'DUTA LITERASI UTAMA',
    ]));

    $response->assertStatus(200);
    $response->assertSee('PIAGAM PENGHARGAAN');
    $response->assertSee('Bintang Literasi Pelajar');
    $response->assertSee('DUTA LITERASI UTAMA');
});

test('user dapat mengakses laporan inventaris buku lengkap mengenai jumlah, ketersediaan, dan status buku', function () {
    $response = $this->actingAs($this->user)->get(route('library.reports.index'));

    $response->assertStatus(200);
    $response->assertSee('Laporan Inventaris, Sirkulasi & Status Buku', false);
    $response->assertSee('Total Judul Buku', false);
    $response->assertSee('Buku yang Ada', false);
    $response->assertSee('Buku Dipinjam', false);

    // Test print report
    $printResponse = $this->actingAs($this->user)->get(route('library.reports.print'));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('LAPORAN INVENTARIS', false);
});

test('user dapat mengunduh ekspor laporan inventaris buku dalam format csv', function () {
    $response = $this->actingAs($this->user)->get(route('library.reports.export'));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('text/csv');
});

test('admin dapat mendaftarkan tenaga administrasi dengan peran utama dan peran tambahan baru', function () {
    $nip = '199501012025011099';
    $response = $this->actingAs($this->user)->post(route('master.teachers.store'), [
        'nip' => $nip,
        'name' => 'Dewi Sartika, S.Kom.',
        'gender' => 'P',
        'title' => 'Tenaga Administrasi Kesiswaan',
        'phone' => '081234567899',
        'role' => 'tenaga_administrasi',
        'roles' => ['tenaga_administrasi', 'ta_kesiswaan', 'bendahara', 'operator'],
        'email' => 'dewi.ta@sman1lengkong.sch.id',
    ]);

    $response->assertRedirect();

    $user = User::where('email', 'dewi.ta@sman1lengkong.sch.id')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('tenaga_administrasi');
    expect($user->isTenagaAdministrasi())->toBeTrue();
    expect($user->isTaKesiswaan())->toBeTrue();
    expect($user->isBendahara())->toBeTrue();
    expect($user->isOperator())->toBeTrue();
});

test('role kepala perpustakaan dan pengelola perpustakaan memiliki hak akses pustakawan', function () {
    $kepala = User::factory()->create([
        'role' => 'kepala_perpustakaan',
        'roles' => ['kepala_perpustakaan'],
    ]);

    expect($kepala->isKepalaPerpustakaan())->toBeTrue();
    expect($kepala->isLibrarian())->toBeTrue();
    expect($kepala->canManageLibrary())->toBeTrue();

    $pengelola = User::factory()->create([
        'role' => 'pengelola_perpustakaan',
        'roles' => ['pengelola_perpustakaan'],
    ]);

    expect($pengelola->isPengelolaPerpustakaan())->toBeTrue();
    expect($pengelola->isLibrarian())->toBeTrue();
    expect($pengelola->canManageLibrary())->toBeTrue();

    $response = $this->actingAs($kepala)->get(route('library.members.index'));
    $response->assertStatus(200);
});

test('fitur cetak perpustakaan mengambil data kop surat dan logo dari master data SchoolSetting', function () {
    SchoolSetting::set('school_name', 'SMA Negeri 1 Lengkong Unggul');
    SchoolSetting::set('school_province', 'JAWA BARAT');
    SchoolSetting::set('school_npsn', '20202265');

    // 1. Cetak Laporan Inventaris
    $printResponse = $this->actingAs($this->user)->get(route('library.reports.print'));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('SMA Negeri 1 Lengkong Unggul');
    $printResponse->assertSee('PEMERINTAH PROVINSI JAWA BARAT');
    $printResponse->assertSee('20202265');

    // 2. Cetak Piagam Penghargaan
    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'MBR-S-TESTKOP'],
        [
            'member_type' => 'siswa',
            'name' => 'Siswa Penggemar Membaca',
            'identity_number' => '0091122334',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    $certResponse = $this->actingAs($this->user)->get(route('library.certificate', [
        'member' => $member->id,
        'rank' => 1,
    ]));

    $certResponse->assertStatus(200);
    $certResponse->assertSee('SMA Negeri 1 Lengkong Unggul');
    $certResponse->assertSee('PEMERINTAH PROVINSI JAWA BARAT');

    // 3. Cetak Kartu Anggota
    $cardResponse = $this->actingAs($this->user)->get(route('library.members.my-card'));
    $cardResponse->assertStatus(200);
    $cardResponse->assertSee('SMA NEGERI 1 LENGKONG UNGGUL');
});

test('user pengelola dapat mengedit dan menghapus buku langsung dari katalog buku', function () {
    $waliKelas = User::factory()->create([
        'name' => 'Guru Wali Kelas Test',
        'role' => 'wali_kelas',
        'roles' => ['wali_kelas', 'guru', 'pengelola_perpustakaan'],
    ]);

    expect($waliKelas->canManageLibrary())->toBeTrue();

    $cat = LibraryCategory::firstOrCreate(
        ['ddc_code' => '600'],
        ['name' => 'Teknologi & Terapan', 'color' => 'emerald', 'icon' => 'wrench']
    );

    $book = LibraryBook::create([
        'book_code' => 'BOOK-CATALOG-EDIT-DEL',
        'title' => 'Teknologi Budidaya Tanaman Modern',
        'author' => 'Prof. Ir. Hendra',
        'total_copies' => 3,
        'available_copies' => 3,
        'book_type' => 'fisik',
        'category_id' => $cat->id,
        'ddc_code' => '600',
    ]);

    // 1. Cek katalog buku menampilkan tombol Edit dan Hapus
    $indexResponse = $this->actingAs($waliKelas)->get(route('library.books.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Teknologi Budidaya Tanaman Modern');
    $indexResponse->assertSee(route('library.books.edit', $book));
    $indexResponse->assertSee(route('library.books.destroy', $book));
    $indexResponse->assertSee('Edit');
    $indexResponse->assertSee('Hapus');

    // 2. Akses halaman edit buku
    $editPageResponse = $this->actingAs($waliKelas)->get(route('library.books.edit', $book));
    $editPageResponse->assertStatus(200);
    $editPageResponse->assertSee('Teknologi Budidaya Tanaman Modern');

    // 3. Update buku
    $updateResponse = $this->actingAs($waliKelas)->put(route('library.books.update', $book), [
        'book_code' => 'BOOK-CATALOG-EDIT-DEL',
        'title' => 'Teknologi Budidaya Tanaman Modern Edisi 2',
        'author' => 'Prof. Ir. Hendra M.Sc',
        'total_copies' => 5,
        'book_type' => 'fisik',
        'category_id' => $cat->id,
    ]);

    $updateResponse->assertRedirect(route('library.books.show', $book));
    $this->assertDatabaseHas('library_books', [
        'id' => $book->id,
        'title' => 'Teknologi Budidaya Tanaman Modern Edisi 2',
        'author' => 'Prof. Ir. Hendra M.Sc',
        'total_copies' => 5,
    ]);

    // 4. Hapus buku dari katalog
    $destroyResponse = $this->actingAs($waliKelas)->delete(route('library.books.destroy', $book));
    $destroyResponse->assertRedirect(route('library.books.index'));
    $this->assertDatabaseMissing('library_books', [
        'id' => $book->id,
    ]);
});

test('buku yang memiliki peminjaman aktif tidak dapat dihapus', function () {
    $book = LibraryBook::create([
        'book_code' => 'BOOK-ACTIVE-LOAN',
        'title' => 'Buku Sedang Dipinjam Siswa',
        'author' => 'Penulis Populer',
        'total_copies' => 2,
        'available_copies' => 1,
        'book_type' => 'fisik',
    ]);

    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'MBR-LOAN-DEL-TEST'],
        [
            'member_type' => 'siswa',
            'name' => 'Siswa Peminjam Aktif',
            'identity_number' => '0099887766',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    LibraryLoan::create([
        'loan_code' => 'PINJAM-DEL-BLOCK',
        'book_id' => $book->id,
        'member_id' => $member->id,
        'loan_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'DIPINJAM',
    ]);

    $response = $this->actingAs($this->user)->delete(route('library.books.destroy', $book));
    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('library_books', [
        'id' => $book->id,
    ]);
});
