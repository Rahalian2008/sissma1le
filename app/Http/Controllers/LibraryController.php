<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\LibraryVisitor;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\BarcodeService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryController extends Controller
{
    /**
     * Memastikan 10 Kelas Utama DDC (000 - 900) Standar SNP selalu tersedia
     */
    public static function ensureDefaultCategories(): void
    {
        if (LibraryCategory::count() === 0) {
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

            foreach ($categories as $cat) {
                LibraryCategory::updateOrCreate(['ddc_code' => $cat['ddc_code']], $cat);
            }
        }
    }

    /**
     * Dashboard Utama Perpustakaan Modern
     */
    public function index(Request $request): View
    {
        self::ensureDefaultCategories();
        $today = Carbon::today();
        $user = Auth::user();
        $canManage = $user ? $user->canManageLibrary() : false;

        // 1. Matriks Statistik Utama
        $totalBooks = LibraryBook::where('is_active', true)->count();
        $totalCopies = LibraryBook::where('is_active', true)->sum('total_copies');
        $digitalBooksCount = 0;
        if (Schema::hasColumn('library_books', 'book_type')) {
            $digitalBooksCount = LibraryBook::where('is_active', true)
                ->where(function ($q) {
                    $q->where('book_type', 'digital');
                    if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                        $q->orWhere('is_digital_flip', true);
                    }
                    if (Schema::hasColumn('library_books', 'digital_file_path')) {
                        $q->orWhereNotNull('digital_file_path');
                    }
                })->count();
        } elseif (Schema::hasColumn('library_books', 'is_digital_flip')) {
            $digitalBooksCount = LibraryBook::where('is_active', true)
                ->where('is_digital_flip', true)
                ->count();
        }

        $activeLoansCount = LibraryLoan::whereIn('status', ['DIPINJAM', 'TERLAMBAT'])->count();
        $pendingLoansCount = LibraryLoan::where('status', 'MENUNGGU')->count();
        $overdueLoansCount = LibraryLoan::where(function ($q) {
            $q->where('status', 'TERLAMBAT')
                ->orWhere(function ($sub) {
                    $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                });
        })->count();

        $todayVisitorsCount = LibraryVisitor::whereDate('visited_at', $today)->count();
        $totalMembersCount = LibraryMember::where('is_active', true)->count();

        // 2. Data Khusus Pengguna Login (Guru / Siswa)
        $userMember = $user ? $user->getOrCreateLibraryMember() : null;
        $userActiveLoansCount = 0;
        $userPendingLoansCount = 0;
        $userOverdueLoansCount = 0;
        $userRecentLoans = collect();

        if ($userMember) {
            $userActiveLoansCount = LibraryLoan::where('member_id', $userMember->id)
                ->whereIn('status', ['DIPINJAM', 'TERLAMBAT'])
                ->count();
            $userPendingLoansCount = LibraryLoan::where('member_id', $userMember->id)
                ->where('status', 'MENUNGGU')
                ->count();
            $userOverdueLoansCount = LibraryLoan::where('member_id', $userMember->id)
                ->where(function ($q) {
                    $q->where('status', 'TERLAMBAT')
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                        });
                })->count();
            $userRecentLoans = LibraryLoan::with(['book.category'])
                ->where('member_id', $userMember->id)
                ->latest('updated_at')
                ->take(4)
                ->get();
        }

        // 3. Koleksi E-Book Digital Flip HTML Pilihan
        $digitalBooksQuery = LibraryBook::with('category')->where('is_active', true);
        if (Schema::hasColumn('library_books', 'is_digital_flip') || Schema::hasColumn('library_books', 'book_type')) {
            $digitalBooksQuery->where(function ($q) {
                if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $q->where('is_digital_flip', true);
                }
                if (Schema::hasColumn('library_books', 'book_type')) {
                    $q->orWhere('book_type', 'digital');
                }
            });
        }
        $digitalOrderCol = Schema::hasColumn('library_books', 'views_count') ? 'views_count' : 'id';
        $digitalBooks = $digitalBooksQuery->orderByDesc($digitalOrderCol)->take(6)->get();

        // 4. Buku Populer / Sering Dipinjam
        $popularBooks = LibraryBook::with('category')
            ->where('is_active', true)
            ->orderByDesc('loans_count')
            ->take(5)
            ->get();

        // 5. Sirkulasi Terbaru
        $recentLoans = LibraryLoan::with(['book', 'member'])
            ->latest('loan_date')
            ->take(6)
            ->get();

        // 6. Pengunjung Hari Ini
        $recentVisitors = LibraryVisitor::whereDate('visited_at', $today)
            ->latest('visited_at')
            ->take(8)
            ->get();

        // 7. Distribusi Koleksi per Kategori DDC
        $categories = LibraryCategory::withCount(['books' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('ddc_code')
            ->get();

        return view('library.index', compact(
            'totalBooks',
            'totalCopies',
            'digitalBooksCount',
            'activeLoansCount',
            'pendingLoansCount',
            'overdueLoansCount',
            'todayVisitorsCount',
            'totalMembersCount',
            'userMember',
            'userActiveLoansCount',
            'userPendingLoansCount',
            'userOverdueLoansCount',
            'userRecentLoans',
            'digitalBooks',
            'popularBooks',
            'recentLoans',
            'recentVisitors',
            'categories',
            'canManage'
        ));
    }

    /**
     * Katalog Lengkap Koleksi Buku (Fisik, Digital Flip HTML & Hybrid)
     */
    public function books(Request $request): View
    {
        self::ensureDefaultCategories();
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $bookType = $request->input('book_type'); // fisik, digital, hybrid
        $availability = $request->input('availability'); // available, unavailable

        $query = LibraryBook::with('category')->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%")
                    ->orWhere('call_number', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($bookType && Schema::hasColumn('library_books', 'book_type')) {
            if ($bookType === 'digital_flip') {
                if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $query->where('is_digital_flip', true);
                }
            } else {
                $query->where('book_type', $bookType);
            }
        }

        if ($availability === 'available') {
            $query->where(function ($q) {
                if (Schema::hasColumn('library_books', 'book_type')) {
                    $q->where('book_type', 'digital')
                        ->orWhere('available_copies', '>', 0);
                } else {
                    $q->where('available_copies', '>', 0);
                }
            });
        } elseif ($availability === 'unavailable') {
            if (Schema::hasColumn('library_books', 'book_type')) {
                $query->where('book_type', '!=', 'digital')->where('available_copies', '<=', 0);
            } else {
                $query->where('available_copies', '<=', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(12)->withQueryString();
        $categories = LibraryCategory::orderBy('ddc_code')->get();

        $user = Auth::user();
        $canManage = $user ? $user->canManageLibrary() : false;
        $userLoansMap = [];

        if ($user) {
            $member = $user->getOrCreateLibraryMember();
            if ($member) {
                $userLoansMap = LibraryLoan::where('member_id', $member->id)
                    ->whereIn('status', ['MENUNGGU', 'DISETUJUI', 'DIPINJAM'])
                    ->pluck('status', 'book_id')
                    ->toArray();
            }
        }

        return view('library.books.index', compact('books', 'categories', 'search', 'categoryId', 'bookType', 'availability', 'canManage', 'userLoansMap'));
    }

    /**
     * Form Tambah Buku Baru
     */
    public function createBook(): View
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        self::ensureDefaultCategories();
        $categories = LibraryCategory::orderBy('ddc_code')->get();

        return view('library.books.create', compact('categories'));
    }

    /**
     * Simpan Buku Baru ke Database
     */
    public function storeBook(Request $request): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }
        $validated = $request->validate([
            'book_code' => ['required', 'string', 'max:50', 'unique:library_books,book_code'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publish_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'isbn' => ['nullable', 'string', 'max:50'],
            'category_id' => ['nullable', 'exists:library_categories,id'],
            'call_number' => ['nullable', 'string', 'max:50'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'book_type' => ['required', 'in:fisik,digital,hybrid'],
            'description' => ['nullable', 'string'],
            'pages_count' => ['nullable', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'digital_file' => ['nullable', 'file', 'mimes:pdf,epub', 'max:51200'],
            'is_digital_flip' => ['nullable', 'boolean'],
            'flip_pages' => ['nullable', 'array'],
            'flip_pages.*.title' => ['nullable', 'string', 'max:255'],
            'flip_pages.*.subtitle' => ['nullable', 'string', 'max:255'],
            'flip_pages.*.body_html' => ['nullable', 'string'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('library/covers', 'public');
        }

        $digitalFilePath = null;
        if ($request->hasFile('digital_file')) {
            $digitalFilePath = $request->file('digital_file')->store('library/ebooks', 'public');
        }

        $category = ! empty($validated['category_id']) ? LibraryCategory::find($validated['category_id']) : null;
        $ddcCode = $category ? $category->ddc_code : null;

        // Susun halaman digital Flip HTML jika ada entri halaman
        $digitalContent = null;
        if (! empty($validated['flip_pages']) && is_array($validated['flip_pages'])) {
            $digitalContent = [];
            $pageNum = 1;
            foreach ($validated['flip_pages'] as $p) {
                if (! empty($p['title']) || ! empty($p['body_html'])) {
                    $digitalContent[] = [
                        'page_num' => $pageNum++,
                        'title' => $p['title'] ?? ('Halaman '.$pageNum),
                        'subtitle' => $p['subtitle'] ?? null,
                        'body_html' => $p['body_html'] ?? '',
                    ];
                }
            }
        }

        $isDigitalFlip = ! empty($validated['is_digital_flip']) || $validated['book_type'] === 'digital' || ! empty($digitalContent);

        $book = LibraryBook::create([
            'book_code' => $validated['book_code'],
            'title' => $validated['title'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'publish_year' => $validated['publish_year'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'ddc_code' => $ddcCode,
            'call_number' => $validated['call_number'] ?? null,
            'rack_location' => $validated['rack_location'] ?? null,
            'total_copies' => $validated['total_copies'],
            'available_copies' => $validated['total_copies'],
            'book_type' => $validated['book_type'],
            'cover_image' => $coverPath,
            'description' => $validated['description'] ?? null,
            'pages_count' => $validated['pages_count'] ?? (is_array($digitalContent) ? count($digitalContent) : null),
            'language' => $validated['language'] ?? 'Indonesia',
            'source' => $validated['source'] ?? 'Pengadaan Sekolah',
            'digital_file_path' => $digitalFilePath,
            'is_digital_flip' => $isDigitalFlip,
            'digital_content' => $digitalContent,
            'is_active' => true,
        ]);

        AuditLog::log('CREATE_LIBRARY_BOOK', 'LibraryBook', $book->id, null, ['title' => $book->title, 'code' => $book->book_code]);

        return redirect()->route('library.books.show', $book)->with('success', "Buku '{$book->title}' berhasil ditambahkan ke katalog perpustakaan.");
    }

    /**
     * Tampilan Detail Buku & Riwayat Peminjaman
     */
    public function showBook(LibraryBook $book): View
    {
        $book->load(['category', 'loans.member']);
        $recentLoans = $book->loans()->latest('loan_date')->take(10)->get();

        $user = Auth::user();
        $canManage = $user ? $user->canManageLibrary() : false;
        $member = $user ? $user->getOrCreateLibraryMember() : null;

        $userExistingLoan = null;
        if ($member) {
            $userExistingLoan = LibraryLoan::where('book_id', $book->id)
                ->where('member_id', $member->id)
                ->whereIn('status', ['MENUNGGU', 'DISETUJUI', 'DIPINJAM'])
                ->first();
        }

        return view('library.books.show', compact('book', 'recentLoans', 'canManage', 'userExistingLoan', 'member'));
    }

    /**
     * Form Edit Buku
     */
    public function editBook(LibraryBook $book): View
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        self::ensureDefaultCategories();
        $categories = LibraryCategory::orderBy('ddc_code')->get();

        return view('library.books.edit', compact('book', 'categories'));
    }

    /**
     * Update Buku
     */
    public function updateBook(Request $request, LibraryBook $book): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }
        $validated = $request->validate([
            'book_code' => ['required', 'string', 'max:50', 'unique:library_books,book_code,'.$book->id],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publish_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'isbn' => ['nullable', 'string', 'max:50'],
            'category_id' => ['nullable', 'exists:library_categories,id'],
            'call_number' => ['nullable', 'string', 'max:50'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'book_type' => ['required', 'in:fisik,digital,hybrid'],
            'description' => ['nullable', 'string'],
            'pages_count' => ['nullable', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'digital_file' => ['nullable', 'file', 'mimes:pdf,epub', 'max:51200'],
            'is_digital_flip' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'flip_pages' => ['nullable', 'array'],
            'flip_pages.*.title' => ['nullable', 'string', 'max:255'],
            'flip_pages.*.subtitle' => ['nullable', 'string', 'max:255'],
            'flip_pages.*.body_html' => ['nullable', 'string'],
        ]);

        $coverPath = $book->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover_image')->store('library/covers', 'public');
        }

        $digitalFilePath = $book->digital_file_path;
        if ($request->hasFile('digital_file')) {
            if ($digitalFilePath && Storage::disk('public')->exists($digitalFilePath)) {
                Storage::disk('public')->delete($digitalFilePath);
            }
            $digitalFilePath = $request->file('digital_file')->store('library/ebooks', 'public');
        }

        $category = ! empty($validated['category_id']) ? LibraryCategory::find($validated['category_id']) : null;
        $ddcCode = $category ? $category->ddc_code : $book->ddc_code;

        // Hitung ulang ketersediaan stok
        $borrowedCopies = $book->activeLoans()->count();
        $newAvailableCopies = max(0, $validated['total_copies'] - $borrowedCopies);

        // Update konten digital Flip HTML jika diinput
        $digitalContent = $book->digital_content;
        if ($request->has('flip_pages')) {
            $rawPages = $validated['flip_pages'] ?? [];
            $digitalContent = [];
            $pageNum = 1;
            foreach ($rawPages as $p) {
                if (! empty($p['title']) || ! empty($p['body_html'])) {
                    $digitalContent[] = [
                        'page_num' => $pageNum++,
                        'title' => $p['title'] ?? ('Halaman '.$pageNum),
                        'subtitle' => $p['subtitle'] ?? null,
                        'body_html' => $p['body_html'] ?? '',
                    ];
                }
            }
        }

        $isDigitalFlip = ! empty($validated['is_digital_flip']) || $validated['book_type'] === 'digital' || ! empty($digitalContent);

        $book->update([
            'book_code' => $validated['book_code'],
            'title' => $validated['title'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'publish_year' => $validated['publish_year'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'ddc_code' => $ddcCode,
            'call_number' => $validated['call_number'] ?? null,
            'rack_location' => $validated['rack_location'] ?? null,
            'total_copies' => $validated['total_copies'],
            'available_copies' => $newAvailableCopies,
            'book_type' => $validated['book_type'],
            'cover_image' => $coverPath,
            'description' => $validated['description'] ?? null,
            'pages_count' => $validated['pages_count'] ?? (is_array($digitalContent) ? count($digitalContent) : $book->pages_count),
            'language' => $validated['language'] ?? 'Indonesia',
            'source' => $validated['source'] ?? 'Pengadaan Sekolah',
            'digital_file_path' => $digitalFilePath,
            'is_digital_flip' => $isDigitalFlip,
            'digital_content' => $digitalContent,
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : true,
        ]);

        AuditLog::log('UPDATE_LIBRARY_BOOK', 'LibraryBook', $book->id, null, ['title' => $book->title]);

        return redirect()->route('library.books.show', $book)->with('success', "Data buku '{$book->title}' berhasil diperbarui.");
    }

    /**
     * Hapus Buku dari Katalog
     */
    public function destroyBook(LibraryBook $book): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        if ($book->activeLoans()->exists()) {
            return back()->with('error', "Buku '{$book->title}' tidak dapat dihapus karena masih ada eksemplar yang sedang dipinjam.");
        }

        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->digital_file_path && Storage::disk('public')->exists($book->digital_file_path)) {
            Storage::disk('public')->delete($book->digital_file_path);
        }

        $title = $book->title;
        $book->delete();

        AuditLog::log('DELETE_LIBRARY_BOOK', 'LibraryBook', null, null, ['title' => $title]);

        return redirect()->route('library.books.index')->with('success', "Buku '{$title}' berhasil dihapus dari katalog perpustakaan.");
    }

    /**
     * Cetak Label Stiker Punggung Buku (Call Number) & Barcode Nomor Induk (Per-Buku)
     */
    public function printBookLabels(Request $request, LibraryBook $book): View
    {
        $includeCopies = $request->boolean('include_copies', true);
        $labelType = $request->input('label_type', 'both'); // both, spine, barcode
        $paper = strtolower((string) $request->input('paper', 'a4'));

        $labelItems = [];
        $totalCopies = $includeCopies ? max(1, min(50, (int) $book->total_copies)) : 1;

        $parts = $book->call_number_parts;

        for ($i = 1; $i <= $totalCopies; $i++) {
            $copyLabel = 'c.'.$i;
            $code = $book->book_code ?: 'LIB-'.str_pad((string) $book->id, 5, '0', STR_PAD_LEFT);
            if ($totalCopies > 1) {
                $codeWithCopy = $code.'-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            } else {
                $codeWithCopy = $code;
            }

            $labelItems[] = [
                'book' => $book,
                'copy_number' => $copyLabel,
                'ddc' => $parts['ddc'],
                'author' => $parts['author'],
                'title_letter' => $parts['title_letter'],
                'call_number' => $parts['ddc'].' '.$parts['author'].' '.$parts['title_letter'].' '.$copyLabel,
                'book_code' => $codeWithCopy,
                'barcode_svg' => BarcodeService::generateCode128Svg($codeWithCopy, 34, 1.25),
            ];
        }

        $schoolName = SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG');
        $libraryName = SchoolSetting::get('library_name', 'PERPUSTAKAAN SMAN 1 LENGKONG');
        $schoolLogo = SchoolSetting::get('school_logo');

        return view('library.books.print_labels', compact(
            'book',
            'labelItems',
            'labelType',
            'paper',
            'includeCopies',
            'schoolName',
            'libraryName',
            'schoolLogo'
        ));
    }

    /**
     * Cetak Stiker & Barcode Buku Massal (Batch Label Print)
     */
    public function printBatchLabels(Request $request): View
    {
        $bookIds = $request->input('book_ids');
        if (is_string($bookIds)) {
            $bookIds = explode(',', $bookIds);
        }

        $categoryId = $request->input('category_id');
        $includeCopies = $request->boolean('include_copies', false);
        $labelType = $request->input('label_type', 'both');
        $paper = strtolower((string) $request->input('paper', 'a4'));
        $search = $request->input('search');

        $query = LibraryBook::with('category')->where('is_active', true);

        if (! empty($bookIds)) {
            $query->whereIn('id', array_filter(array_map('intval', (array) $bookIds)));
        }

        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('book_code')->limit(150)->get();

        $labelItems = [];
        foreach ($books as $b) {
            $parts = $b->call_number_parts;
            $copiesCount = $includeCopies ? max(1, min(20, (int) $b->total_copies)) : 1;

            for ($i = 1; $i <= $copiesCount; $i++) {
                $copyLabel = 'c.'.$i;
                $code = $b->book_code ?: 'LIB-'.str_pad((string) $b->id, 5, '0', STR_PAD_LEFT);
                $codeWithCopy = ($copiesCount > 1) ? $code.'-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT) : $code;

                $labelItems[] = [
                    'book' => $b,
                    'copy_number' => $copyLabel,
                    'ddc' => $parts['ddc'],
                    'author' => $parts['author'],
                    'title_letter' => $parts['title_letter'],
                    'call_number' => $parts['ddc'].' '.$parts['author'].' '.$parts['title_letter'].' '.$copyLabel,
                    'book_code' => $codeWithCopy,
                    'barcode_svg' => BarcodeService::generateCode128Svg($codeWithCopy, 34, 1.25),
                ];
            }
        }

        $schoolName = SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG');
        $libraryName = SchoolSetting::get('library_name', 'PERPUSTAKAAN SMAN 1 LENGKONG');
        $schoolLogo = SchoolSetting::get('school_logo');
        $book = null;

        return view('library.books.print_labels', compact(
            'book',
            'books',
            'labelItems',
            'labelType',
            'paper',
            'includeCopies',
            'schoolName',
            'libraryName',
            'schoolLogo'
        ));
    }

    /**
     * TAMPILAN BUKU DIGITAL FLIP HTML (Interaktif 3D Page Turn Flipbook)
     */
    public function flipbook(LibraryBook $book): View
    {
        $book->increment('views_count');

        // Catat kunjungan online pembaca digital
        if ($user = Auth::user()) {
            $member = $user->getOrCreateLibraryMember();
            $recent = LibraryVisitor::where('member_id', $member->id)
                ->where('channel', 'online')
                ->where('visited_at', '>=', now()->subMinutes(15))
                ->exists();

            if (! $recent) {
                LibraryVisitor::create([
                    'visitor_type' => $member->member_type,
                    'channel' => 'online',
                    'member_id' => $member->id,
                    'student_id' => $member->student_id,
                    'teacher_id' => $member->teacher_id,
                    'name' => $member->name,
                    'identity_number' => $member->identity_number,
                    'class_or_institution' => $member->member_class_or_title,
                    'purpose' => 'Membaca E-Book Digital: '.substr($book->title, 0, 70),
                    'visited_at' => now(),
                    'notes' => 'Akses Flip HTML 3D',
                ]);
            }
        }

        // Jika buku belum memiliki halaman digital terstruktur tetapi memiliki deskripsi/bab, buat halaman default
        $pages = $book->digital_content;
        if (empty($pages) || ! is_array($pages)) {
            $pages = [
                [
                    'page_num' => 1,
                    'title' => $book->title,
                    'subtitle' => 'Koleksi Perpustakaan Digital SMA Negeri 1 Lengkong',
                    'body_html' => '<div class="text-center py-12 px-6 flex flex-col items-center justify-center min-h-[460px] bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white rounded-2xl shadow-inner border border-indigo-500/20">
                        <span class="px-3 py-1 rounded-full bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 text-xs font-bold uppercase tracking-wider mb-4">'.e($book->category->name ?? 'Pustaka Digital').'</span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2 leading-tight">'.e($book->title).'</h1>
                        <p class="text-base text-indigo-200 font-medium mb-4">Penulis: '.e($book->author).'</p>
                        <div class="w-16 h-1 bg-cyan-400 rounded-full mb-6"></div>
                        <p class="text-xs text-slate-300">Penerbit: '.e($book->publisher ?? 'SMA Negeri 1 Lengkong').' ('.($book->publish_year ?? date('Y')).')</p>
                        <p class="text-xs text-slate-400 mt-1">ISBN: '.e($book->isbn ?? 'Katalog Pustaka Terakreditasi').'</p>
                    </div>',
                ],
                [
                    'page_num' => 2,
                    'title' => 'Sinopsis & Informasi Pustaka',
                    'subtitle' => 'Data Katalog Klasifikasi DDC '.($book->ddc_code ?? '000'),
                    'body_html' => '<div class="space-y-4 text-slate-800 dark:text-slate-100 text-sm leading-relaxed p-6">
                        <h2 class="text-lg font-bold text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-800 pb-2">Ringkasan Buku</h2>
                        <p>'.nl2br(e($book->description ?? 'Buku digital interaktif resmi perpustakaan sekolah.')).'</p>
                        <div class="mt-6 p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-xs space-y-1">
                            <p><strong>Nomor Panggil:</strong> '.e($book->call_number ?? '-').'</p>
                            <p><strong>Lokasi Rak Fisik:</strong> '.e($book->rack_location ?? 'Koleksi Digital Cloud').'</p>
                            <p><strong>Bahasa:</strong> '.e($book->language ?? 'Indonesia').'</p>
                        </div>
                    </div>',
                ],
            ];
        }

        $digitalFileUrl = $book->digital_file_url ? route('library.books.stream_pdf', $book) : null;

        return view('library.books.flipbook', compact('book', 'pages', 'digitalFileUrl'));
    }

    /**
     * Stream Berkas PDF Dokumen Digital secara aman untuk Reader Interaktif (Tanpa opsi download langsung & anti-intersep IDM)
     */
    public function streamPdf(Request $request, LibraryBook $book): BinaryFileResponse|JsonResponse
    {
        if (! $book->digital_file_path) {
            abort(404, 'Dokumen digital tidak ditemukan.');
        }

        $disk = Storage::disk('public');
        $fullPath = $disk->exists($book->digital_file_path) ? $disk->path($book->digital_file_path) : storage_path('app/public/'.$book->digital_file_path);
        if (! file_exists($fullPath)) {
            $fullPath = public_path('storage/'.$book->digital_file_path);
        }

        if (! file_exists($fullPath)) {
            abort(404, 'Berkas PDF fisik tidak ditemukan pada server.');
        }

        // Mode JSON Base64 untuk lingkungan dengan download manager agresif (IDM, FDM)
        if ($request->query('format') === 'base64') {
            return response()->json([
                'success' => true,
                'data' => base64_encode(file_get_contents($fullPath)),
                'mime' => 'application/octet-stream',
            ]);
        }

        // Header biner aman tanpa ekstensi .pdf agar tidak dicegat oleh IDM / Download Manager
        return response()->file($fullPath, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-transform, max-age=3600',
        ]);
    }

    /**
     * Sirkulasi Peminjaman & Pengembalian (Khusus Admin & Pengelola Perpustakaan)
     */
    public function loans(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageLibrary()) {
            return redirect()->route('library.loans.my');
        }

        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = LibraryLoan::with(['book', 'member.student.schoolClass', 'member.teacher', 'user', 'approver']);

        if ($status && $status !== 'all') {
            if ($status === 'TERLAMBAT') {
                $query->where(function ($q) {
                    $q->where('status', 'TERLAMBAT')
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                        });
                });
            } elseif ($status === 'DIPINJAM') {
                $query->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today());
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('book', fn ($bq) => $bq->where('title', 'like', "%{$search}%")->orWhere('book_code', 'like', "%{$search}%"))
                    ->orWhereHas('member', fn ($mq) => $mq->where('name', 'like', "%{$search}%")->orWhere('member_code', 'like', "%{$search}%")->orWhere('identity_number', 'like', "%{$search}%"));
            });
        }

        $loans = $query->latest('updated_at')->paginate(15)->withQueryString();

        // Hitung badge per kategori tab
        $pendingCount = LibraryLoan::where('status', 'MENUNGGU')->count();
        $borrowedCount = LibraryLoan::where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today())->count();
        $overdueCount = LibraryLoan::where('status', 'TERLAMBAT')
            ->orWhere(function ($q) {
                $q->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
            })->count();
        $returnedCount = LibraryLoan::where('status', 'KEMBALI')->count();
        $rejectedCount = LibraryLoan::whereIn('status', ['DITOLAK', 'DIBATALKAN'])->count();
        $allCount = LibraryLoan::count();

        // Data bantuan untuk form modal peminjaman baru di meja perpustakaan
        $availableBooks = LibraryBook::where('is_active', true)
            ->where(function ($q) {
                $q->where('book_type', 'digital')
                    ->orWhere('available_copies', '>', 0);
            })
            ->orderBy('title')
            ->get();

        $activeMembers = LibraryMember::where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('library.loans.index', compact(
            'loans',
            'availableBooks',
            'activeMembers',
            'classes',
            'status',
            'search',
            'pendingCount',
            'borrowedCount',
            'overdueCount',
            'returnedCount',
            'rejectedCount',
            'allCount'
        ));
    }

    /**
     * Pengajuan Peminjaman Buku Mandiri oleh Akun Pengguna (Guru / Siswa)
     */
    public function requestLoan(Request $request, LibraryBook $book): RedirectResponse
    {
        $user = Auth::user();

        if (! $book->is_active) {
            return back()->with('error', 'Buku ini sedang tidak aktif dalam katalog perpustakaan.');
        }

        if ($book->book_type === 'digital') {
            return redirect()->route('library.books.flipbook', $book)
                ->with('info', 'Buku ini adalah buku digital yang dapat Anda baca langsung melalui Flip HTML 3D tanpa perlu meminjam fisik.');
        }

        if ($book->available_copies <= 0) {
            return back()->with('error', "Mohon maaf, saat ini seluruh eksemplar fisik buku '{$book->title}' sedang dipinjam.");
        }

        $member = $user->getOrCreateLibraryMember();

        // 1. Cek apakah ada pinjaman yang terlambat
        $hasOverdue = LibraryLoan::where('member_id', $member->id)
            ->where(function ($q) {
                $q->where('status', 'TERLAMBAT')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                    });
            })->exists();

        if ($hasOverdue) {
            return back()->with('error', 'Anda masih memiliki peminjaman buku yang melewati batas waktu (terlambat). Silakan selesaikan pengembalian buku sebelumnya ke perpustakaan terlebih dahulu.');
        }

        // 2. Cek apakah sudah pernah mengajukan atau sedang meminjam buku ini
        $alreadyRequested = LibraryLoan::where('book_id', $book->id)
            ->where('member_id', $member->id)
            ->whereIn('status', ['MENUNGGU', 'DISETUJUI', 'DIPINJAM'])
            ->exists();

        if ($alreadyRequested) {
            return back()->with('error', "Anda sudah memiliki pengajuan aktif atau sedang meminjam buku '{$book->title}'.");
        }

        // 3. Cek batas maksimal buku aktif
        $maxAllowed = $user->isTeacher() ? (int) SchoolSetting::get('library_max_teacher_loans', 5) : (int) SchoolSetting::get('library_max_student_loans', 3);
        $activeLoansCount = LibraryLoan::where('member_id', $member->id)
            ->whereIn('status', ['MENUNGGU', 'DISETUJUI', 'DIPINJAM'])
            ->count();

        if ($activeLoansCount >= $maxAllowed) {
            return back()->with('error', "Anda telah mencapai batas maksimal peminjaman aktif ({$maxAllowed} buku). Silakan kembalikan buku yang sedang dipinjam terlebih dahulu.");
        }

        $loanDate = Carbon::today();
        $defaultLoanDays = (int) SchoolSetting::get('library_loan_days', 7);
        $dueDate = $loanDate->copy()->addDays($defaultLoanDays);
        $loanCode = 'REQ-'.date('Ym').'-'.strtoupper(substr(uniqid(), -5));

        $loan = LibraryLoan::create([
            'loan_code' => $loanCode,
            'book_id' => $book->id,
            'member_id' => $member->id,
            'user_id' => null,
            'request_date' => now(),
            'loan_date' => $loanDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'MENUNGGU',
            'fine_amount' => 0,
            'is_fine_paid' => false,
            'extension_count' => 0,
            'request_notes' => $request->input('notes') ?: $request->input('request_notes'),
            'notes' => 'Diajukan mandiri oleh '.$user->name.' ('.strtoupper($member->member_type).') via akun.',
        ]);

        AuditLog::log('REQUEST_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, [
            'book' => $book->title,
            'member' => $member->name,
        ]);

        return redirect()->route('library.loans.my')
            ->with('success', "Pengajuan peminjaman buku '{$book->title}' berhasil diajukan! Silakan temui petugas di perpustakaan untuk pengambilan buku.");
    }

    /**
     * Halaman "Peminjaman Saya" untuk Pengguna (Guru / Siswa)
     */
    public function myLoans(Request $request): View
    {
        $user = Auth::user();
        $member = $user->getOrCreateLibraryMember();
        $status = $request->input('status', 'all');

        $query = LibraryLoan::with(['book.category', 'approver'])
            ->where('member_id', $member->id);

        if ($status && $status !== 'all') {
            if ($status === 'TERLAMBAT') {
                $query->where(function ($q) {
                    $q->where('status', 'TERLAMBAT')
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                        });
                });
            } elseif ($status === 'DIPINJAM') {
                $query->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today());
            } else {
                $query->where('status', $status);
            }
        }

        $loans = $query->latest('updated_at')->paginate(10)->withQueryString();

        // Hitung statistik user
        $pendingCount = LibraryLoan::where('member_id', $member->id)->where('status', 'MENUNGGU')->count();
        $activeCount = LibraryLoan::where('member_id', $member->id)->where('status', 'DIPINJAM')->count();
        $overdueCount = LibraryLoan::where('member_id', $member->id)
            ->where(function ($q) {
                $q->where('status', 'TERLAMBAT')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                    });
            })->count();
        $returnedCount = LibraryLoan::where('member_id', $member->id)->where('status', 'KEMBALI')->count();
        $allCount = LibraryLoan::where('member_id', $member->id)->count();

        return view('library.loans.my', compact(
            'loans',
            'member',
            'status',
            'pendingCount',
            'activeCount',
            'overdueCount',
            'returnedCount',
            'allCount'
        ));
    }

    /**
     * Pengguna membatalkan pengajuan peminjaman yang masih berstatus MENUNGGU
     */
    public function cancelMyLoan(LibraryLoan $loan): RedirectResponse
    {
        $user = Auth::user();
        $member = $user->libraryMember ?? $user->getOrCreateLibraryMember();

        if ($loan->member_id !== $member->id && ! $user->canManageLibrary()) {
            return back()->with('error', 'Anda tidak memiliki hak untuk membatalkan pengajuan ini.');
        }

        if ($loan->status !== 'MENUNGGU') {
            return back()->with('error', 'Hanya pengajuan dengan status menunggu persetujuan yang dapat dibatalkan.');
        }

        $loan->update([
            'status' => 'DIBATALKAN',
            'notes' => ($loan->notes ? $loan->notes."\n" : '').'Dibatalkan mandiri oleh peminjam pada '.now()->translatedFormat('d F Y H:i').'.',
        ]);

        AuditLog::log('CANCEL_LIBRARY_LOAN', 'LibraryLoan', $loan->id);

        return back()->with('success', "Pengajuan peminjaman buku '{$loan->book->title}' berhasil dibatalkan.");
    }

    /**
     * Petugas / Pustakawan menyetujui pengajuan peminjaman buku
     */
    public function approveLoan(Request $request, LibraryLoan $loan): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        if ($loan->status !== 'MENUNGGU') {
            return back()->with('error', 'Status peminjaman ini sudah diproses sebelumnya.');
        }

        $book = $loan->book;
        if ($book->book_type !== 'digital' && $book->available_copies <= 0) {
            return back()->with('error', "Stok eksemplar fisik buku '{$book->title}' sedang kosong. Tidak dapat disetujui saat ini.");
        }

        $loanDate = Carbon::today();
        $defaultLoanDays = (int) SchoolSetting::get('library_loan_days', 7);
        $dueDate = $loanDate->copy()->addDays($defaultLoanDays);

        $loan->update([
            'status' => 'DIPINJAM',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'user_id' => Auth::id(),
            'loan_date' => $loanDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'notes' => ($loan->notes ? $loan->notes."\n" : '').'Disetujui dan diserahkan ke peminjam oleh '.Auth::user()->name.' pada '.now()->translatedFormat('d F Y H:i').'.',
        ]);

        if ($book->book_type !== 'digital') {
            $book->decrement('available_copies');
        }
        $book->increment('loans_count');

        AuditLog::log('APPROVE_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, [
            'book' => $book->title,
            'member' => $loan->member->name,
            'approver' => Auth::user()->name,
        ]);

        return back()->with('success', "Pengajuan peminjaman disetujui. Buku '{$book->title}' telah diserahkan kepada {$loan->member->name}.");
    }

    /**
     * Petugas / Pustakawan menolak pengajuan peminjaman buku
     */
    public function rejectLoan(Request $request, LibraryLoan $loan): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        if ($loan->status !== 'MENUNGGU') {
            return back()->with('error', 'Pengajuan ini sudah tidak berstatus menunggu persetujuan.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $reason = $request->input('rejection_reason');

        $loan->update([
            'status' => 'DITOLAK',
            'rejection_reason' => $reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => ($loan->notes ? $loan->notes."\n" : '').'Ditolak oleh '.Auth::user()->name.': '.$reason,
        ]);

        AuditLog::log('REJECT_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, [
            'book' => $loan->book->title,
            'reason' => $reason,
        ]);

        return back()->with('success', "Pengajuan peminjaman buku '{$loan->book->title}' oleh {$loan->member->name} telah ditolak.");
    }

    /**
     * Simpan Transaksi Peminjaman Buku Baru (Langsung di Meja Sirkulasi)
     */
    public function storeLoan(Request $request): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $validated = $request->validate([
            'book_id' => ['required', 'exists:library_books,id'],
            'member_id' => ['nullable', 'exists:library_members,id'],
            'member_type' => ['nullable', 'in:siswa,guru,umum'],
            'manual_name' => ['nullable', 'string', 'max:255'],
            'manual_identity' => ['nullable', 'string', 'max:50'],
            'manual_phone' => ['nullable', 'string', 'max:30'],
            'loan_date' => ['required', 'date'],
            'loan_days' => ['required', 'integer', 'min:1', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $book = LibraryBook::findOrFail($validated['book_id']);

        if ($book->book_type !== 'digital' && $book->available_copies <= 0) {
            return back()->with('error', "Buku '{$book->title}' saat ini sedang habis (seluruh eksemplar sedang dipinjam).");
        }

        // Cari atau buat Anggota Perpustakaan
        $memberId = $validated['member_id'] ?? null;
        if (! $memberId) {
            if (empty($validated['manual_name'])) {
                return back()->with('error', 'Silakan pilih anggota terdaftar atau isi nama peminjam secara manual.');
            }

            $memberCode = 'MBR-M-'.time();
            $newMember = LibraryMember::create([
                'member_code' => $memberCode,
                'member_type' => $validated['member_type'] ?? 'umum',
                'name' => trim($validated['manual_name']),
                'identity_number' => $validated['manual_identity'] ?? null,
                'phone' => $validated['manual_phone'] ?? null,
                'joined_at' => now(),
                'is_active' => true,
                'notes' => 'Peminjam manual tercatat di sistem sirkulasi.',
            ]);
            $memberId = $newMember->id;
        }

        $loanDate = Carbon::parse($validated['loan_date']);
        $dueDate = $loanDate->copy()->addDays((int) $validated['loan_days']);
        $loanCode = 'LOAN-'.date('Ym').'-'.strtoupper(substr(uniqid(), -5));

        $loan = LibraryLoan::create([
            'loan_code' => $loanCode,
            'book_id' => $book->id,
            'member_id' => $memberId,
            'user_id' => Auth::id(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'loan_date' => $loanDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'DIPINJAM',
            'fine_amount' => 0,
            'is_fine_paid' => false,
            'extension_count' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Kurangi stok jika buku fisik/hybrid
        if ($book->book_type !== 'digital') {
            $book->decrement('available_copies');
        }
        $book->increment('loans_count');

        AuditLog::log('CREATE_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, [
            'book' => $book->title,
            'member_id' => $memberId,
            'due_date' => $dueDate->toDateString(),
        ]);

        return back()->with('success', "Peminjaman buku '{$book->title}' berhasil dicatat (Jatuh tempo: {$dueDate->translatedFormat('d F Y')}).");
    }

    /**
     * Proses Pengembalian Buku & Hitung Denda Keterlambatan
     */
    public function returnLoan(Request $request, LibraryLoan $loan): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        if ($loan->status === 'KEMBALI') {
            return back()->with('error', 'Transaksi peminjaman ini sudah dikembalikan sebelumnya.');
        }

        $returnDate = Carbon::today();
        $daysOverdue = 0;
        $fineAmount = 0;

        // Denda keterlambatan per hari (default Rp 1.000/hari sesuai standar SNP)
        $dailyFineRate = (int) SchoolSetting::get('library_fine_per_day', 1000);

        if ($returnDate->gt($loan->due_date)) {
            $daysOverdue = (int) $loan->due_date->diffInDays($returnDate);
            $fineAmount = $daysOverdue * $dailyFineRate;
        }

        $newStatus = $daysOverdue > 0 ? 'TERLAMBAT' : 'KEMBALI';

        $loan->update([
            'return_date' => $returnDate->toDateString(),
            'status' => $newStatus,
            'fine_amount' => $fineAmount,
            'is_fine_paid' => $request->boolean('is_fine_paid') || $fineAmount == 0,
            'notes' => $request->input('notes') ?: ($daysOverdue > 0 ? "Dikembalikan terlambat {$daysOverdue} hari. Denda: Rp ".number_format($fineAmount, 0, ',', '.') : 'Dikembalikan tepat waktu.'),
        ]);

        // Kembalikan stok buku
        if ($loan->book && $loan->book->book_type !== 'digital') {
            $loan->book->increment('available_copies');
        }

        AuditLog::log('RETURN_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, [
            'days_overdue' => $daysOverdue,
            'fine_amount' => $fineAmount,
        ]);

        $msg = "Buku '{$loan->book->title}' berhasil dikembalikan.";
        if ($fineAmount > 0) {
            $msg .= " Terlambat {$daysOverdue} hari (Denda: Rp ".number_format($fineAmount, 0, ',', '.').').';
        }

        return back()->with('success', $msg);
    }

    /**
     * Perpanjangan Masa Pinjam Buku (+7 Hari)
     */
    public function extendLoan(Request $request, LibraryLoan $loan): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageLibrary()) {
            $member = $user->libraryMember ?? $user->getOrCreateLibraryMember();
            if ($loan->member_id !== $member->id) {
                return back()->with('error', 'Anda tidak berwenang memperpanjang pinjaman ini.');
            }
        }

        if ($loan->status === 'KEMBALI') {
            return back()->with('error', 'Peminjaman yang sudah selesai tidak dapat diperpanjang.');
        }

        if ($loan->extension_count >= 2) {
            return back()->with('error', 'Buku ini sudah mencapai batas maksimal perpanjangan (2 kali).');
        }

        $extensionDays = (int) $request->input('extension_days', 7);
        $newDueDate = Carbon::parse($loan->due_date)->addDays($extensionDays);

        $loan->update([
            'due_date' => $newDueDate->toDateString(),
            'extension_count' => $loan->extension_count + 1,
            'status' => 'DIPINJAM',
            'notes' => ($loan->notes ? $loan->notes."\n" : '').'Diperpanjang ke-'.($loan->extension_count + 1).' hingga '.$newDueDate->translatedFormat('d F Y').'.',
        ]);

        AuditLog::log('EXTEND_LIBRARY_LOAN', 'LibraryLoan', $loan->id, null, ['new_due_date' => $newDueDate->toDateString()]);

        return back()->with('success', "Masa pinjam buku '{$loan->book->title}' berhasil diperpanjang hingga {$newDueDate->translatedFormat('d F Y')}.");
    }

    /**
     * Tampilan Kartu Anggota Perpustakaan Digital Pengguna
     */
    public function myCard(): View
    {
        $user = Auth::user();
        $member = $user->getOrCreateLibraryMember();
        $activeLoans = LibraryLoan::with('book')
            ->where('member_id', $member->id)
            ->whereIn('status', ['DIPINJAM', 'TERLAMBAT'])
            ->get();

        $branding = $this->getSchoolBranding();

        return view('library.members.my_card', array_merge(compact('user', 'member', 'activeLoans'), $branding));
    }

    /**
     * Kelola Data Anggota Perpustakaan (Khusus Admin & Pustakawan)
     */
    public function members(Request $request): View
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $search = $request->input('search');
        $memberType = $request->input('member_type'); // siswa, guru, umum

        $query = LibraryMember::with(['student.schoolClass', 'teacher']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($memberType) {
            $query->where('member_type', $memberType);
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();

        return view('library.members.index', compact('members', 'classes', 'search', 'memberType'));
    }

    /**
     * Tambah Anggota Manual Baru (Pengunjung Luar / Tamu / Staf)
     */
    public function storeMember(Request $request): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $validated = $request->validate([
            'member_type' => ['required', 'in:siswa,guru,umum'],
            'student_id' => ['nullable', 'exists:students,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'name' => ['required', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $prefix = match ($validated['member_type']) {
            'siswa' => 'MBR-S-',
            'guru' => 'MBR-G-',
            default => 'MBR-U-',
        };
        $memberCode = $prefix.strtoupper(substr(uniqid(), -6));

        $member = LibraryMember::create([
            'member_code' => $memberCode,
            'member_type' => $validated['member_type'],
            'student_id' => $validated['student_id'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
            'name' => trim($validated['name']),
            'identity_number' => $validated['identity_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'joined_at' => now(),
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::log('CREATE_LIBRARY_MEMBER', 'LibraryMember', $member->id, null, ['name' => $member->name]);

        return back()->with('success', "Anggota perpustakaan '{$member->name}' ({$member->member_code}) berhasil didaftarkan.");
    }

    /**
     * Perbarui Data Anggota Perpustakaan
     */
    public function updateMember(Request $request, LibraryMember $member): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $validated = $request->validate([
            'member_type' => ['required', 'in:siswa,guru,umum'],
            'name' => ['required', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'notes' => ['nullable', 'string'],
        ]);

        $member->update([
            'member_type' => $validated['member_type'],
            'name' => trim($validated['name']),
            'identity_number' => $validated['identity_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::log('UPDATE_LIBRARY_MEMBER', 'LibraryMember', $member->id, null, ['name' => $member->name]);

        return back()->with('success', "Data anggota perpustakaan '{$member->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Data Anggota Perpustakaan
     */
    public function destroyMember(LibraryMember $member): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        // Cek apakah anggota masih memiliki pinjaman buku yang belum dikembalikan
        $activeLoansCount = $member->loans()->whereIn('status', ['DIPINJAM', 'TERLAMBAT'])->count();
        if ($activeLoansCount > 0) {
            return back()->with('error', "Gagal menghapus! Anggota '{$member->name}' masih memiliki {$activeLoansCount} buku pinjaman yang belum dikembalikan. Harap proses pengembalian buku terlebih dahulu.");
        }

        $name = $member->name;
        $member->delete();

        AuditLog::log('DELETE_LIBRARY_MEMBER', 'LibraryMember', null, ['name' => $name], null);

        return back()->with('success', "Anggota perpustakaan '{$name}' berhasil dihapus.");
    }

    /**
     * Sinkronisasi Massal Seluruh Siswa & Guru dari Master Data ke Anggota Perpustakaan
     */
    public function syncMembersFromMaster(): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $students = Student::with('schoolClass')->get();
        $teachers = Teacher::all();

        $syncedCount = 0;

        foreach ($students as $stu) {
            $identifier = $stu->nisn ?: $stu->nis;
            $existing = LibraryMember::where('student_id', $stu->id)->first();
            if (! $existing && ! empty($identifier)) {
                $existing = LibraryMember::where('identity_number', $identifier)->first();
            }

            if ($existing) {
                $existing->update([
                    'student_id' => $stu->id,
                    'name' => $stu->name,
                    'identity_number' => $identifier ?: $existing->identity_number,
                    'phone' => $stu->phone ?: $existing->phone,
                    'address' => $stu->address ?: $existing->address,
                    'is_active' => $stu->is_active ?? true,
                ]);
            } else {
                $saved = false;
                $attempts = 0;
                while (! $saved && $attempts < 5) {
                    $attempts++;
                    $preferred = ($attempts === 1) ? $stu->id : ($stu->id + random_int(1000, 99999));
                    $code = LibraryMember::generateUniqueCode('siswa', $preferred);
                    try {
                        LibraryMember::create([
                            'member_code' => $code,
                            'member_type' => 'siswa',
                            'student_id' => $stu->id,
                            'name' => $stu->name,
                            'identity_number' => $identifier ?: ('SIS-'.$stu->id),
                            'phone' => $stu->phone,
                            'address' => $stu->address,
                            'joined_at' => now(),
                            'is_active' => $stu->is_active ?? true,
                            'notes' => 'Otomatis tersinkron dari Master Data Siswa '.($stu->schoolClass->name ?? ''),
                        ]);
                        $saved = true;
                        $syncedCount++;
                    } catch (UniqueConstraintViolationException|QueryException $e) {
                        if (str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == 23000) {
                            continue;
                        }
                        throw $e;
                    }
                }
            }
        }

        foreach ($teachers as $tch) {
            $identifier = $tch->nip ?: ('GURU-'.$tch->id);
            $existing = LibraryMember::where('teacher_id', $tch->id)->first();
            if (! $existing && ! empty($identifier)) {
                $existing = LibraryMember::where('identity_number', $identifier)->first();
            }

            if ($existing) {
                $existing->update([
                    'teacher_id' => $tch->id,
                    'name' => $tch->name,
                    'identity_number' => $identifier ?: $existing->identity_number,
                    'phone' => $tch->phone ?: $existing->phone,
                    'is_active' => $tch->is_active ?? true,
                ]);
            } else {
                $saved = false;
                $attempts = 0;
                while (! $saved && $attempts < 5) {
                    $attempts++;
                    $preferred = ($attempts === 1) ? $tch->id : ($tch->id + random_int(100, 9999));
                    $code = LibraryMember::generateUniqueCode('guru', $preferred);
                    try {
                        LibraryMember::create([
                            'member_code' => $code,
                            'member_type' => 'guru',
                            'teacher_id' => $tch->id,
                            'name' => $tch->name,
                            'identity_number' => $identifier,
                            'phone' => $tch->phone,
                            'joined_at' => now(),
                            'is_active' => $tch->is_active ?? true,
                            'notes' => 'Otomatis tersinkron dari Master Data Guru / Pegawai',
                        ]);
                        $saved = true;
                        $syncedCount++;
                    } catch (UniqueConstraintViolationException|QueryException $e) {
                        if (str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == 23000) {
                            continue;
                        }
                        throw $e;
                    }
                }
            }
        }

        AuditLog::log('SYNC_LIBRARY_MEMBERS', 'LibraryMember', null, null, ['synced' => $syncedCount]);

        return back()->with('success', "Berhasil menyinkronkan {$syncedCount} siswa & guru dari Master Data ke Anggota Perpustakaan!");
    }

    /**
     * Buku Tamu / Presensi Pengunjung Perpustakaan
     */
    public function visitors(Request $request): View
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $search = $request->input('search');
        $date = $request->input('date', Carbon::today()->toDateString());

        $query = LibraryVisitor::with(['student.schoolClass', 'teacher']);

        if ($date) {
            $query->whereDate('visited_at', $date);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('class_or_institution', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        $visitors = $query->latest('visited_at')->paginate(20)->withQueryString();

        // Rekap Kunjungan Hari Ini per Keperluan
        $purposeStats = LibraryVisitor::whereDate('visited_at', $date)
            ->select('purpose', DB::raw('count(*) as total'))
            ->groupBy('purpose')
            ->get();

        $students = Student::with('schoolClass')->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();

        return view('library.visitors.index', compact('visitors', 'purposeStats', 'students', 'teachers', 'date', 'search'));
    }

    /**
     * Catat Kunjungan Baru di Buku Tamu Perpustakaan
     */
    public function storeVisitor(Request $request): RedirectResponse
    {
        if (! Auth::user()->canManageLibrary()) {
            abort(403, 'Akses terbatas untuk Pengelola Perpustakaan.');
        }

        $validated = $request->validate([
            'visitor_mode' => ['required', 'in:student,teacher,manual,scan'],
            'student_id' => ['nullable', 'exists:students,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'manual_name' => ['nullable', 'string', 'max:255'],
            'manual_class' => ['nullable', 'string', 'max:100'],
            'scan_code' => ['nullable', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $visitorType = 'umum';
        $studentId = null;
        $teacherId = null;
        $memberId = null;
        $name = '';
        $identityNumber = null;
        $classOrInstitution = null;

        // Mode 1: Scan Barcode (NISN / NIP / Member Code)
        if ($validated['visitor_mode'] === 'scan' && ! empty($validated['scan_code'])) {
            $code = trim($validated['scan_code']);
            // Cek member
            $member = LibraryMember::where('member_code', $code)->orWhere('identity_number', $code)->first();
            if ($member) {
                $memberId = $member->id;
                $visitorType = $member->member_type;
                $studentId = $member->student_id;
                $teacherId = $member->teacher_id;
                $name = $member->name;
                $identityNumber = $member->identity_number;
                $classOrInstitution = $member->member_class_or_title;
            } else {
                // Cek Siswa
                $stu = Student::with('schoolClass')->where('nisn', $code)->orWhere('nis', $code)->orWhere('rfid_uid', $code)->first();
                if ($stu) {
                    $visitorType = 'siswa';
                    $studentId = $stu->id;
                    $name = $stu->name;
                    $identityNumber = $stu->nisn ?: $stu->nis;
                    $classOrInstitution = 'Kelas '.($stu->schoolClass->name ?? '-');
                } else {
                    // Cek Guru
                    $tch = Teacher::where('nip', $code)->first();
                    if ($tch) {
                        $visitorType = 'guru';
                        $teacherId = $tch->id;
                        $name = $tch->name;
                        $identityNumber = $tch->nip;
                        $classOrInstitution = $tch->title ?: 'Guru / Pegawai';
                    } else {
                        $name = 'Pengunjung Barcode ('.$code.')';
                        $identityNumber = $code;
                        $classOrInstitution = 'Tamu Umum';
                    }
                }
            }
        } elseif ($validated['visitor_mode'] === 'student' && ! empty($validated['student_id'])) {
            $stu = Student::with('schoolClass')->findOrFail($validated['student_id']);
            $visitorType = 'siswa';
            $studentId = $stu->id;
            $name = $stu->name;
            $identityNumber = $stu->nisn ?: $stu->nis;
            $classOrInstitution = 'Kelas '.($stu->schoolClass->name ?? '-');
        } elseif ($validated['visitor_mode'] === 'teacher' && ! empty($validated['teacher_id'])) {
            $tch = Teacher::findOrFail($validated['teacher_id']);
            $visitorType = 'guru';
            $teacherId = $tch->id;
            $name = $tch->name;
            $identityNumber = $tch->nip;
            $classOrInstitution = $tch->title ?: 'Guru / Pegawai';
        } else {
            $name = trim($validated['manual_name'] ?? 'Tamu Perpustakaan');
            $classOrInstitution = trim($validated['manual_class'] ?? 'Pengunjung Umum');
            $identityNumber = null;
        }

        $visitor = LibraryVisitor::create([
            'visitor_type' => $visitorType,
            'member_id' => $memberId,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'name' => $name,
            'identity_number' => $identityNumber,
            'class_or_institution' => $classOrInstitution,
            'purpose' => $validated['purpose'],
            'visited_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "Presensi kunjungan atas nama '{$visitor->name}' ({$visitor->class_or_institution}) berhasil dicatat!");
    }

    /**
     * Tampilan Kiosk Pengisian Buku Tamu Mandiri (Publik Tanpa Perlu Login)
     */
    public function guestBook(Request $request): View
    {
        $today = Carbon::today()->toDateString();

        // Log Pengunjung Hari Ini
        $todayVisitors = LibraryVisitor::whereDate('visited_at', $today)
            ->latest('visited_at')
            ->take(25)
            ->get();

        $todayCount = LibraryVisitor::whereDate('visited_at', $today)->count();
        $studentCount = LibraryVisitor::whereDate('visited_at', $today)->where('visitor_type', 'siswa')->count();
        $teacherCount = LibraryVisitor::whereDate('visited_at', $today)->where('visitor_type', 'guru')->count();
        $generalCount = LibraryVisitor::whereDate('visited_at', $today)->where('visitor_type', 'umum')->count();

        // Rekap Keperluan Hari Ini
        $purposeStats = LibraryVisitor::whereDate('visited_at', $today)
            ->select('purpose', DB::raw('count(*) as total'))
            ->groupBy('purpose')
            ->orderByDesc('total')
            ->get();

        $branding = $this->getSchoolBranding();

        return view('library.visitors.guest_book', array_merge(compact(
            'todayVisitors',
            'todayCount',
            'studentCount',
            'teacherCount',
            'generalCount',
            'purposeStats',
            'today'
        ), $branding));
    }

    /**
     * Lookup Pengunjung Otomatis via NISN / NIP / Kode Anggota (AJAX)
     */
    public function lookupGuest(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));
        $type = $request->input('type', 'any'); // siswa, guru, any

        if (empty($code)) {
            return response()->json(['found' => false, 'message' => 'Identitas tidak boleh kosong.']);
        }

        // 1. Cek dari Anggota Perpustakaan
        $member = LibraryMember::with(['student.schoolClass', 'teacher'])
            ->where(function ($q) use ($code) {
                $q->where('member_code', $code)
                    ->orWhere('identity_number', $code);
            })
            ->first();

        if ($member) {
            return response()->json([
                'found' => true,
                'source' => 'member',
                'member_id' => $member->id,
                'student_id' => $member->student_id,
                'teacher_id' => $member->teacher_id,
                'visitor_type' => $member->member_type,
                'name' => $member->name,
                'identity_number' => $member->identity_number ?: $code,
                'class_or_institution' => $member->member_class_or_title,
            ]);
        }

        // 2. Jika Siswa / Any -> Cari di Master Data Siswa
        if ($type === 'siswa' || $type === 'any') {
            $student = Student::with('schoolClass')
                ->where('is_active', true)
                ->where(function ($q) use ($code) {
                    $q->where('nisn', $code)
                        ->orWhere('nis', $code)
                        ->orWhere('rfid_uid', $code);
                })
                ->first();

            if ($student) {
                return response()->json([
                    'found' => true,
                    'source' => 'student',
                    'student_id' => $student->id,
                    'visitor_type' => 'siswa',
                    'name' => $student->name,
                    'identity_number' => $student->nisn ?: $student->nis,
                    'class_or_institution' => 'Kelas '.($student->schoolClass->name ?? '-'),
                ]);
            }
        }

        // 3. Jika Guru / Tendik / Any -> Cari di Master Data Guru
        if ($type === 'guru' || $type === 'any') {
            $teacher = Teacher::where('is_active', true)
                ->where(function ($q) use ($code) {
                    $q->where('nip', $code)
                        ->orWhere('nuptk', $code)
                        ->orWhere('rfid_uid', $code);
                })
                ->first();

            if ($teacher) {
                return response()->json([
                    'found' => true,
                    'source' => 'teacher',
                    'teacher_id' => $teacher->id,
                    'visitor_type' => 'guru',
                    'name' => $teacher->name,
                    'identity_number' => $teacher->nip ?: '-',
                    'class_or_institution' => $teacher->title ?: 'Guru / Tenaga Pendidik',
                ]);
            }
        }

        return response()->json([
            'found' => false,
            'message' => 'Identitas tidak ditemukan dalam database sekolah. Silakan pilih tab Tamu Umum untuk entri manual.',
        ]);
    }

    /**
     * Simpan Kunjungan dari Layar Kiosk Mandiri (Mendukung AJAX & Standar Form)
     */
    public function storeGuestBook(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'visitor_type' => ['required', 'in:siswa,guru,umum'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'class_or_institution' => ['nullable', 'string', 'max:150'],
            'purpose' => ['required', 'string', 'max:100'],
            'satisfaction_rating' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $visitorType = $validated['visitor_type'];
        $identityNumber = trim($validated['identity_number'] ?? '');
        $name = trim($validated['name']);
        $classOrInstitution = trim($validated['class_or_institution'] ?? '');
        $studentId = null;
        $teacherId = null;
        $memberId = null;

        if (! empty($identityNumber)) {
            $member = LibraryMember::where('identity_number', $identityNumber)->orWhere('member_code', $identityNumber)->first();
            if ($member) {
                $memberId = $member->id;
                $studentId = $member->student_id;
                $teacherId = $member->teacher_id;
            } else {
                if ($visitorType === 'siswa') {
                    $stu = Student::where('nisn', $identityNumber)->orWhere('nis', $identityNumber)->first();
                    if ($stu) {
                        $studentId = $stu->id;
                    }
                } elseif ($visitorType === 'guru') {
                    $tch = Teacher::where('nip', $identityNumber)->first();
                    if ($tch) {
                        $teacherId = $tch->id;
                    }
                }
            }
        }

        $noteContent = $validated['notes'] ?? '';
        if (! empty($validated['satisfaction_rating'])) {
            $noteContent = "[Rating: {$validated['satisfaction_rating']}] ".($noteContent ?: 'Pelayanan memuaskan');
        }

        $visitor = LibraryVisitor::create([
            'visitor_type' => $visitorType,
            'channel' => 'kiosk',
            'member_id' => $memberId,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'name' => $name,
            'identity_number' => $identityNumber ?: null,
            'class_or_institution' => $classOrInstitution ?: ($visitorType === 'umum' ? 'Tamu Umum' : '-'),
            'purpose' => $validated['purpose'],
            'visited_at' => now(),
            'notes' => $noteContent ?: null,
        ]);

        $successMsg = "Selamat datang, {$visitor->name}! Kunjungan Anda berhasil dicatat di Buku Tamu Perpustakaan.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMsg,
                'visitor' => [
                    'id' => $visitor->id,
                    'name' => $visitor->name,
                    'visitor_type' => $visitor->visitor_type,
                    'class_or_institution' => $visitor->class_or_institution,
                    'purpose' => $visitor->purpose,
                    'time' => $visitor->visited_at->format('H:i:s'),
                ],
                'today_count' => LibraryVisitor::whereDate('visited_at', Carbon::today())->count(),
            ]);
        }

        return redirect()->route('library.guest_book.index')->with('success', $successMsg);
    }

    /**
     * Pengaturan Perpustakaan & Penugasan Pengelola / Pustakawan (Admin)
     */
    public function settings(): View
    {
        if (! Auth::user()->isLibraryAdmin()) {
            abort(403, 'Hanya Administrator atau Kepala Perpustakaan yang berwenang mengatur sistem perpustakaan.');
        }

        $finePerDay = (int) SchoolSetting::get('library_fine_per_day', 1000);
        $loanDays = (int) SchoolSetting::get('library_loan_days', 7);
        $maxStudentLoans = (int) SchoolSetting::get('library_max_student_loans', 3);
        $maxTeacherLoans = (int) SchoolSetting::get('library_max_teacher_loans', 5);

        // Daftar Pustakawan Aktif
        $librarians = User::where(function ($q) {
            $q->where('role', 'pustakawan')
                ->orWhereJsonContains('roles', 'pustakawan')
                ->orWhere('role', 'pengelola_perpustakaan')
                ->orWhereJsonContains('roles', 'pengelola_perpustakaan');
        })->get();

        // Calon staf/guru untuk ditugaskan jadi pustakawan
        $staffUsers = User::whereNotIn('role', ['siswa', 'orang_tua'])
            ->orderBy('name')
            ->get();

        return view('library.settings', compact(
            'finePerDay',
            'loanDays',
            'maxStudentLoans',
            'maxTeacherLoans',
            'librarians',
            'staffUsers'
        ));
    }

    /**
     * Simpan Pengaturan & Penugasan Pengelola Perpustakaan
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        if (! Auth::user()->isLibraryAdmin()) {
            abort(403, 'Hanya Administrator atau Kepala Perpustakaan yang berwenang mengatur sistem perpustakaan.');
        }

        $validated = $request->validate([
            'fine_per_day' => ['required', 'integer', 'min:0', 'max:50000'],
            'loan_days' => ['required', 'integer', 'min:1', 'max:30'],
            'max_student_loans' => ['required', 'integer', 'min:1', 'max:10'],
            'max_teacher_loans' => ['required', 'integer', 'min:1', 'max:20'],
            'assign_user_id' => ['nullable', 'exists:users,id'],
            'remove_user_id' => ['nullable', 'exists:users,id'],
        ]);

        SchoolSetting::set('library_fine_per_day', $validated['fine_per_day'], 'library', 'Denda keterlambatan per hari (Rupiah)');
        SchoolSetting::set('library_loan_days', $validated['loan_days'], 'library', 'Durasi standar peminjaman buku (Hari)');
        SchoolSetting::set('library_max_student_loans', $validated['max_student_loans'], 'library', 'Maksimal buku dipinjam siswa');
        SchoolSetting::set('library_max_teacher_loans', $validated['max_teacher_loans'], 'library', 'Maksimal buku dipinjam guru');

        // Jika ada penugasan pengelola perpustakaan baru
        if (! empty($validated['assign_user_id'])) {
            $assignedUser = User::find($validated['assign_user_id']);
            if ($assignedUser) {
                $roles = is_array($assignedUser->roles) ? $assignedUser->roles : [];
                if (! in_array('pustakawan', $roles, true)) {
                    $roles[] = 'pustakawan';
                }
                $assignedUser->update([
                    'roles' => array_values(array_unique($roles)),
                ]);

                // Buat atau sinkronkan anggota perpustakaan
                $assignedUser->getOrCreateLibraryMember();
            }
        }

        // Jika ada pencabutan pengelola perpustakaan
        if (! empty($validated['remove_user_id'])) {
            $removeUser = User::find($validated['remove_user_id']);
            if ($removeUser) {
                $roles = is_array($removeUser->roles) ? $removeUser->roles : [];
                $roles = array_diff($roles, ['pustakawan', 'pengelola_perpustakaan']);
                $newMainRole = $removeUser->role === 'pustakawan' ? 'guru' : $removeUser->role;
                $removeUser->update([
                    'role' => $newMainRole,
                    'roles' => array_values(array_unique($roles)),
                ]);
            }
        }

        AuditLog::log('UPDATE_LIBRARY_SETTINGS', 'SchoolSetting', null, null, $validated);

        return back()->with('success', 'Pengaturan perpustakaan dan penugasan pengelola berhasil diperbarui.');
    }

    /**
     * Rapor & Rangking Pengunjung Perpustakaan (Offline & Online)
     */
    public function leaderboard(Request $request): View
    {
        $period = $request->input('period', 'month'); // month, semester, year, all
        $type = $request->input('type', 'all'); // all, siswa, guru
        $search = $request->input('search');

        $dateFilter = match ($period) {
            'month' => Carbon::now()->startOfMonth(),
            'semester' => Carbon::now()->month >= 7 ? Carbon::create(Carbon::now()->year, 7, 1) : Carbon::create(Carbon::now()->year, 1, 1),
            'year' => Carbon::now()->startOfYear(),
            default => null,
        };

        // Query visitors
        $visitorQuery = LibraryVisitor::query();
        if ($dateFilter) {
            $visitorQuery->where('visited_at', '>=', $dateFilter);
        }

        // Ambil rekap kunjungan per member_id
        $visitsByMember = (clone $visitorQuery)
            ->whereNotNull('member_id')
            ->selectRaw('member_id, 
                COUNT(*) as total_visits, 
                SUM(CASE WHEN channel = "online" OR notes LIKE "%online%" OR notes LIKE "%Flip%" THEN 1 ELSE 0 END) as online_visits,
                SUM(CASE WHEN channel != "online" AND (notes NOT LIKE "%online%" OR notes IS NULL) THEN 1 ELSE 0 END) as offline_visits')
            ->groupBy('member_id')
            ->get()
            ->keyBy('member_id');

        // Ambil rekap peminjaman per member_id
        $loanQuery = LibraryLoan::query();
        if ($dateFilter) {
            $loanQuery->where('loan_date', '>=', $dateFilter);
        }
        $loansByMember = $loanQuery->selectRaw('member_id, COUNT(*) as total_loans')
            ->groupBy('member_id')
            ->get()
            ->keyBy('member_id');

        // Query Members
        $membersQuery = LibraryMember::with(['student.schoolClass', 'teacher', 'user'])
            ->where('is_active', true);

        if ($type === 'siswa') {
            $membersQuery->where('member_type', 'siswa');
        } elseif ($type === 'guru') {
            $membersQuery->where('member_type', 'guru');
        }

        if ($search) {
            $membersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        $allMembers = $membersQuery->get();

        // Map statistics and calculate Literacy Index Score
        $leaderboardData = $allMembers->map(function ($mbr) use ($visitsByMember, $loansByMember) {
            $visitStat = $visitsByMember->get($mbr->id);
            $loanStat = $loansByMember->get($mbr->id);

            $offlineVisits = $visitStat ? (int) $visitStat->offline_visits : 0;
            $onlineVisits = $visitStat ? (int) $visitStat->online_visits : 0;
            $totalVisits = $offlineVisits + $onlineVisits;
            $totalLoans = $loanStat ? (int) $loanStat->total_loans : 0;

            // Indeks Skor Literasi = (Kunjungan Offline * 10) + (Kunjungan Online * 8) + (Pinjaman Buku * 15)
            $literacyScore = ($offlineVisits * 10) + ($onlineVisits * 8) + ($totalLoans * 15);

            return [
                'member' => $mbr,
                'offline_visits' => $offlineVisits,
                'online_visits' => $onlineVisits,
                'total_visits' => $totalVisits,
                'total_loans' => $totalLoans,
                'literacy_score' => $literacyScore,
            ];
        })
            ->sortByDesc('literacy_score')
            ->values();

        // Beri ranking & predikat
        $leaderboard = $leaderboardData->map(function ($item, $index) {
            $rank = $index + 1;
            $item['rank'] = $rank;

            if ($rank === 1) {
                $item['badge'] = '🥇 Duta Literasi Utama';
                $item['predicate'] = 'Peringkat I - Duta Literasi Terbaik';
            } elseif ($rank === 2) {
                $item['badge'] = '🥈 Pemustaka Teladan';
                $item['predicate'] = 'Peringkat II - Pemustaka Teladan';
            } elseif ($rank === 3) {
                $item['badge'] = '🥉 Sahabat Literasi Utama';
                $item['predicate'] = 'Peringkat III - Sahabat Literasi';
            } elseif ($rank <= 10) {
                $item['badge'] = '⭐ Top 10 Pembaca Berprestasi';
                $item['predicate'] = 'Top 10 Pemustaka Teraktif';
            } else {
                $item['badge'] = '📖 Pemustaka Aktif';
                $item['predicate'] = 'Pemustaka Aktif';
            }

            return $item;
        });

        // Metrik Top Stats
        $topVisitor = $leaderboard->first();
        $totalOnlineVisits = $leaderboard->sum('online_visits');
        $totalOfflineVisits = $leaderboard->sum('offline_visits');
        $totalAllVisits = $totalOnlineVisits + $totalOfflineVisits;

        // Current logged-in user position
        $currentUserRank = null;
        if ($user = Auth::user()) {
            $userMember = $user->libraryMember;
            if ($userMember) {
                $currentUserRank = $leaderboard->firstWhere('member.id', $userMember->id);
            }
        }

        // Paginate for view table
        $perPage = 25;
        $page = (int) $request->input('page', 1);
        $paginatedItems = new LengthAwarePaginator(
            $leaderboard->forPage($page, $perPage),
            $leaderboard->count(),
            $perPage,
            $page,
            ['path' => route('library.leaderboard'), 'query' => $request->query()]
        );

        $periodLabel = match ($period) {
            'month' => 'Bulan Ini ('.Carbon::now()->translatedFormat('F Y').')',
            'semester' => 'Semester '.(Carbon::now()->month >= 7 ? 'Ganjil' : 'Genap').' '.Carbon::now()->year,
            'year' => 'Tahun '.Carbon::now()->year,
            default => 'Sepanjang Waktu',
        };

        return view('library.leaderboard', compact(
            'paginatedItems',
            'leaderboard',
            'topVisitor',
            'totalOnlineVisits',
            'totalOfflineVisits',
            'totalAllVisits',
            'currentUserRank',
            'period',
            'periodLabel',
            'type',
            'search'
        ));
    }

    /**
     * Dapatkan data branding resmi sekolah (Kop Surat, Logo, Identitas) dari SchoolSetting
     *
     * @return array<string, mixed>
     */
    protected function getSchoolBranding(): array
    {
        $kopPath = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));
        $actualKopUrl = $kopPath && (Storage::disk('public')->exists($kopPath) || file_exists(public_path('storage/'.$kopPath)))
            ? asset('storage/'.$kopPath)
            : null;

        $logoPath = SchoolSetting::get('school_logo');
        $schoolLogo = $logoPath && (Storage::disk('public')->exists($logoPath) || file_exists(public_path('storage/'.$logoPath)))
            ? asset('storage/'.$logoPath)
            : null;

        $kopDarkModeUrl = file_exists(public_path('storage/branding/kop_dark_compact.png'))
            ? asset('storage/branding/kop_dark_compact.png')
            : (file_exists(public_path('storage/branding/kop_dark_mode.png'))
                ? asset('storage/branding/kop_dark_mode.png')
                : $actualKopUrl);

        $headOfLibUser = User::where(function ($q) {
            $q->where('role', 'kepala_perpustakaan')->orWhereJsonContains('roles', 'kepala_perpustakaan');
        })->with('teacher')->first();

        $librarianName = $headOfLibUser?->teacher?->name ?: ($headOfLibUser?->name ?: SchoolSetting::get('library_head_name', 'Anne Listiyani, S.Si., M.Pd.'));
        $librarianNip = $headOfLibUser?->teacher?->nip ?: ($headOfLibUser?->username ?: SchoolSetting::get('library_head_nip', '19800417 201001 2 014'));

        $headmaster = Teacher::getHeadmaster();
        $headmasterName = SchoolSetting::get('headmaster_name', $headmaster['name'] ?? 'Drs. H. Ahmad Sudrajat, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip', $headmaster['nip'] ?? '19680512 199303 1 004');

        return [
            'actualKopUrl' => $actualKopUrl,
            'kopDarkModeUrl' => $kopDarkModeUrl,
            'schoolLogo' => $schoolLogo,
            'schoolProvince' => SchoolSetting::get('school_province', 'JAWA BARAT'),
            'schoolBranch' => SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V'),
            'schoolName' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'schoolAddress' => SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat'),
            'schoolPhone' => SchoolSetting::get('school_phone', '(0266) 123456'),
            'schoolEmail' => SchoolSetting::get('school_email', 'sman1lengkong@gmail.com'),
            'schoolWebsite' => SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id'),
            'schoolNpsn' => SchoolSetting::get('school_npsn', '20202265'),
            'schoolNss' => SchoolSetting::get('school_nss', '301051408001'),
            'schoolAccreditation' => SchoolSetting::get('school_accreditation', 'A (Unggul)'),
            'headmasterName' => $headmasterName,
            'headmasterNip' => $headmasterNip,
            'librarianName' => $librarianName,
            'librarianNip' => $librarianNip,
        ];
    }

    /**
     * Piagam Penghargaan Keren Pemustaka & Duta Literasi Sekolah
     */
    public function certificate(LibraryMember $member, Request $request): View
    {
        $member->load(['student.schoolClass', 'teacher', 'user']);

        $period = $request->input('period', 'Tahun Ajaran '.date('Y').'/'.(date('Y') + 1));
        $rank = (int) $request->input('rank', 1);
        $predicate = $request->input('predicate');

        if (! $predicate) {
            $predicate = match ($rank) {
                1 => 'DUTA LITERASI UTAMA (PERINGKAT I)',
                2 => 'PEMUSTAKA TELADAN (PERINGKAT II)',
                3 => 'SAHABAT LITERASI BERPRESTASI (PERINGKAT III)',
                default => 'PEMUSTAKA TERAKTIF & GEMAR MEMBACA',
            };
        }

        $certNumber = '421.3/PERPUS-LIT/'.date('Y').'/'.str_pad((string) $member->id, 4, '0', STR_PAD_LEFT);

        $offlineCount = LibraryVisitor::where('member_id', $member->id)->where('channel', '!=', 'online')->count();
        $onlineCount = LibraryVisitor::where('member_id', $member->id)->where(function ($q) {
            $q->where('channel', 'online')->orWhere('notes', 'LIKE', '%online%');
        })->count();
        $loanCount = LibraryLoan::where('member_id', $member->id)->count();

        $branding = $this->getSchoolBranding();

        return view('library.certificate', array_merge(compact(
            'member',
            'period',
            'rank',
            'predicate',
            'certNumber',
            'offlineCount',
            'onlineCount',
            'loanCount'
        ), $branding));
    }

    /**
     * Laporan Lengkap Inventaris, Sirkulasi & Status Buku
     */
    public function reports(Request $request): View
    {
        $status = $request->input('status', 'all'); // all, available, borrowed, out_of_stock, digital
        $categoryId = $request->input('category_id');
        $bookType = $request->input('book_type'); // all, physical, digital, digital_flip
        $search = $request->input('search');

        // Ringkasan KPI Utama
        $totalTitles = LibraryBook::count();
        $totalCopies = (int) LibraryBook::sum('total_copies');
        $availableCopies = (int) LibraryBook::sum('available_copies');
        $borrowedCopies = max(0, $totalCopies - $availableCopies);
        $digitalBooksCount = LibraryBook::where('is_digital_flip', true)->orWhere('book_type', 'digital')->count();
        $physicalBooksCount = LibraryBook::where('book_type', '!=', 'digital')->count();
        $outOfStockCount = LibraryBook::where('book_type', '!=', 'digital')->where('available_copies', '<=', 0)->count();

        // Query Buku
        $query = LibraryBook::with(['category'])->withCount(['loans']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($bookType && $bookType !== 'all') {
            if ($bookType === 'digital_flip') {
                $query->where('is_digital_flip', true);
            } else {
                $query->where('book_type', $bookType);
            }
        }

        if ($status === 'available') {
            $query->where('available_copies', '>', 0);
        } elseif ($status === 'borrowed') {
            $query->whereRaw('total_copies > available_copies');
        } elseif ($status === 'out_of_stock') {
            $query->where('book_type', '!=', 'digital')->where('available_copies', '<=', 0);
        } elseif ($status === 'digital') {
            $query->where(function ($q) {
                $q->where('book_type', 'digital')->orWhere('is_digital_flip', true);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('rack_location', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('title')->paginate(20)->withQueryString();

        // Kategori untuk dropdown filter
        $categories = LibraryCategory::withCount('books')->orderBy('name')->get();

        // Data Buku yang Sedang Dipinjam Saat Ini (Active Loans)
        $activeLoans = LibraryLoan::with(['book', 'member'])
            ->whereIn('status', ['DIPINJAM', 'TERLAMBAT'])
            ->orderBy('due_date')
            ->take(15)
            ->get();

        // Rekapitulasi per Kategori DDC
        $categoryBreakdown = LibraryCategory::withCount('books')
            ->withSum('books', 'total_copies')
            ->withSum('books', 'available_copies')
            ->orderBy('name')
            ->get();

        return view('library.reports.index', compact(
            'books',
            'categories',
            'activeLoans',
            'categoryBreakdown',
            'totalTitles',
            'totalCopies',
            'availableCopies',
            'borrowedCopies',
            'digitalBooksCount',
            'physicalBooksCount',
            'outOfStockCount',
            'status',
            'categoryId',
            'bookType',
            'search'
        ));
    }

    /**
     * Cetak Laporan Inventaris Buku Resmi (Print-Ready A4)
     */
    public function printReport(Request $request): View
    {
        $categoryId = $request->input('category_id');
        $status = $request->input('status', 'all');

        $query = LibraryBook::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status === 'available') {
            $query->where('available_copies', '>', 0);
        } elseif ($status === 'borrowed') {
            $query->whereRaw('total_copies > available_copies');
        }

        $books = $query->orderBy('book_code')->get();

        $totalTitles = $books->count();
        $totalCopies = (int) $books->sum('total_copies');
        $availableCopies = (int) $books->sum('available_copies');
        $borrowedCopies = max(0, $totalCopies - $availableCopies);

        $branding = $this->getSchoolBranding();

        return view('library.reports.print', array_merge(compact(
            'books',
            'totalTitles',
            'totalCopies',
            'availableCopies',
            'borrowedCopies'
        ), $branding));
    }

    /**
     * Ekspor Laporan Inventaris Buku ke CSV
     */
    public function exportReport(Request $request): StreamedResponse
    {
        $books = LibraryBook::with('category')->orderBy('book_code')->get();
        $filename = 'Laporan_Inventaris_Buku_SMAN1Lengkong_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($books) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Buku',
                'ISBN',
                'Judul Buku',
                'Penulis',
                'Penerbit',
                'Tahun Terbit',
                'Kategori / DDC',
                'Lokasi Rak',
                'Total Eksemplar',
                'Eksemplar Ada (Tersedia)',
                'Eksemplar Dipinjam',
                'Tipe Buku',
                'Status Ketersediaan',
            ], ';');

            foreach ($books as $idx => $b) {
                $borrowed = max(0, $b->total_copies - $b->available_copies);
                $statusText = $b->book_type === 'digital' ? 'Buku Digital (Akses Online)' : ($b->available_copies > 0 ? ($borrowed > 0 ? "Tersedia Sebagian ({$b->available_copies})" : 'Tersedia Lengkap') : 'Habis Dipinjam');

                fputcsv($handle, [
                    $idx + 1,
                    $b->book_code,
                    $b->isbn ?? '-',
                    $b->title,
                    $b->author,
                    $b->publisher ?? '-',
                    $b->publish_year ?? '-',
                    $b->category->name ?? '-',
                    $b->rack_location ?? '-',
                    $b->total_copies,
                    $b->available_copies,
                    $borrowed,
                    $b->is_digital_flip ? 'E-Book Flip HTML 3D' : ($b->book_type === 'digital' ? 'E-Book Digital' : 'Buku Fisik'),
                    $statusText,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Laporan Buku Tamu Pengunjung Kustom (Harian, Mingguan, Bulanan, Rentang Tanggal)
     */
    public function visitorReports(Request $request): View
    {
        $period = $request->input('period', 'daily');
        $date = $request->input('date', Carbon::today()->toDateString());
        $weekDate = $request->input('week_date', Carbon::today()->toDateString());
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $visitorType = $request->input('visitor_type', 'all');
        $purpose = $request->input('purpose', 'all');
        $search = $request->input('search');

        $query = LibraryVisitor::with(['student.schoolClass', 'teacher', 'member']);

        // Periode Filter
        if ($period === 'weekly') {
            $start = Carbon::parse($weekDate)->startOfWeek();
            $end = Carbon::parse($weekDate)->endOfWeek();
            $query->whereBetween('visited_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
            $periodLabel = 'Mingguan ('.$start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y').')';
        } elseif ($period === 'monthly') {
            $query->whereMonth('visited_at', $month)->whereYear('visited_at', $year);
            $periodLabel = 'Bulan '.Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        } elseif ($period === 'custom') {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('visited_at', [$start, $end]);
            $periodLabel = 'Periode '.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y');
        } else {
            // Default Harian
            $query->whereDate('visited_at', $date);
            $periodLabel = 'Tanggal '.Carbon::parse($date)->translatedFormat('d F Y');
        }

        if ($visitorType && $visitorType !== 'all') {
            $query->where('visitor_type', $visitorType);
        }

        if ($purpose && $purpose !== 'all') {
            $query->where('purpose', $purpose);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('class_or_institution', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Clone query untuk statistik periode
        $statsQuery = clone $query;
        $totalVisitors = $statsQuery->count();
        $studentVisitors = (clone $query)->where('visitor_type', 'siswa')->count();
        $teacherVisitors = (clone $query)->where('visitor_type', 'guru')->count();
        $generalVisitors = (clone $query)->where('visitor_type', 'umum')->count();

        $purposeStats = (clone $query)
            ->select('purpose', DB::raw('count(*) as total'))
            ->groupBy('purpose')
            ->orderByDesc('total')
            ->get();

        $visitors = $query->latest('visited_at')->paginate(20)->withQueryString();

        return view('library.visitors.report', compact(
            'visitors',
            'totalVisitors',
            'studentVisitors',
            'teacherVisitors',
            'generalVisitors',
            'purposeStats',
            'period',
            'periodLabel',
            'date',
            'weekDate',
            'month',
            'year',
            'startDate',
            'endDate',
            'visitorType',
            'purpose',
            'search'
        ));
    }

    /**
     * Cetak Dokumen Resmi Laporan Buku Tamu Pengunjung (A4 / F4)
     */
    public function printVisitorReports(Request $request): View
    {
        $period = $request->input('period', 'daily');
        $date = $request->input('date', Carbon::today()->toDateString());
        $weekDate = $request->input('week_date', Carbon::today()->toDateString());
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $visitorType = $request->input('visitor_type', 'all');
        $purpose = $request->input('purpose', 'all');

        $query = LibraryVisitor::with(['student.schoolClass', 'teacher', 'member']);

        if ($period === 'weekly') {
            $start = Carbon::parse($weekDate)->startOfWeek();
            $end = Carbon::parse($weekDate)->endOfWeek();
            $query->whereBetween('visited_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
            $periodLabel = 'Mingguan ('.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y').')';
        } elseif ($period === 'monthly') {
            $query->whereMonth('visited_at', $month)->whereYear('visited_at', $year);
            $periodLabel = 'Bulan '.Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        } elseif ($period === 'custom') {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('visited_at', [$start, $end]);
            $periodLabel = 'Periode '.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y');
        } else {
            $query->whereDate('visited_at', $date);
            $periodLabel = 'Hari/Tanggal '.Carbon::parse($date)->translatedFormat('l, d F Y');
        }

        if ($visitorType && $visitorType !== 'all') {
            $query->where('visitor_type', $visitorType);
        }

        if ($purpose && $purpose !== 'all') {
            $query->where('purpose', $purpose);
        }

        $visitors = $query->orderBy('visited_at', 'asc')->get();

        $totalVisitors = $visitors->count();
        $studentVisitors = $visitors->where('visitor_type', 'siswa')->count();
        $teacherVisitors = $visitors->where('visitor_type', 'guru')->count();
        $generalVisitors = $visitors->where('visitor_type', 'umum')->count();

        $branding = $this->getSchoolBranding();

        return view('library.visitors.print_report', array_merge(compact(
            'visitors',
            'totalVisitors',
            'studentVisitors',
            'teacherVisitors',
            'generalVisitors',
            'periodLabel'
        ), $branding));
    }

    /**
     * Laporan Sirkulasi Buku Kustom (Dipinjam, Dikembalikan, Terlambat - Harian, Mingguan, Bulanan, Rentang Bebas)
     */
    public function loansReports(Request $request): View
    {
        $period = $request->input('period', 'monthly');
        $date = $request->input('date', Carbon::today()->toDateString());
        $weekDate = $request->input('week_date', Carbon::today()->toDateString());
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $status = $request->input('status', 'all'); // all, DIPINJAM, KEMBALI, TERLAMBAT
        $search = $request->input('search');

        $query = LibraryLoan::with(['book.category', 'member.student.schoolClass', 'member.teacher', 'user']);

        // Filter Periode berdasarkan loan_date
        if ($period === 'daily') {
            $query->whereDate('loan_date', $date);
            $periodLabel = 'Tanggal '.Carbon::parse($date)->translatedFormat('d F Y');
        } elseif ($period === 'weekly') {
            $start = Carbon::parse($weekDate)->startOfWeek();
            $end = Carbon::parse($weekDate)->endOfWeek();
            $query->whereBetween('loan_date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
            $periodLabel = 'Mingguan ('.$start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y').')';
        } elseif ($period === 'custom') {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('loan_date', [$start, $end]);
            $periodLabel = 'Periode '.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y');
        } else {
            // Default Bulanan
            $query->whereMonth('loan_date', $month)->whereYear('loan_date', $year);
            $periodLabel = 'Bulan '.Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        }

        // Filter Status
        if ($status && $status !== 'all') {
            if ($status === 'TERLAMBAT') {
                $query->where(function ($q) {
                    $q->where('status', 'TERLAMBAT')
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                        });
                });
            } elseif ($status === 'DIPINJAM') {
                $query->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today());
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('book', fn ($bq) => $bq->where('title', 'like', "%{$search}%")->orWhere('book_code', 'like', "%{$search}%"))
                    ->orWhereHas('member', fn ($mq) => $mq->where('name', 'like', "%{$search}%")->orWhere('identity_number', 'like', "%{$search}%"));
            });
        }

        // Summary counts
        $totalLoans = (clone $query)->count();
        $borrowedLoans = (clone $query)->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today())->count();
        $returnedLoans = (clone $query)->where('status', 'KEMBALI')->count();
        $overdueLoans = (clone $query)->where(function ($q) {
            $q->where('status', 'TERLAMBAT')->orWhere(function ($sub) {
                $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
            });
        })->count();
        $totalFines = (clone $query)->sum('fine_amount');

        $loans = $query->latest('loan_date')->paginate(20)->withQueryString();

        return view('library.loans.report', compact(
            'loans',
            'totalLoans',
            'borrowedLoans',
            'returnedLoans',
            'overdueLoans',
            'totalFines',
            'period',
            'periodLabel',
            'date',
            'weekDate',
            'month',
            'year',
            'startDate',
            'endDate',
            'status',
            'search'
        ));
    }

    /**
     * Cetak Dokumen Resmi Laporan Sirkulasi Buku (A4 / F4)
     */
    public function printLoansReports(Request $request): View
    {
        $period = $request->input('period', 'monthly');
        $date = $request->input('date', Carbon::today()->toDateString());
        $weekDate = $request->input('week_date', Carbon::today()->toDateString());
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $status = $request->input('status', 'all');

        $query = LibraryLoan::with(['book.category', 'member.student.schoolClass', 'member.teacher', 'user']);

        if ($period === 'daily') {
            $query->whereDate('loan_date', $date);
            $periodLabel = 'Hari/Tanggal '.Carbon::parse($date)->translatedFormat('l, d F Y');
        } elseif ($period === 'weekly') {
            $start = Carbon::parse($weekDate)->startOfWeek();
            $end = Carbon::parse($weekDate)->endOfWeek();
            $query->whereBetween('loan_date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
            $periodLabel = 'Mingguan ('.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y').')';
        } elseif ($period === 'custom') {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('loan_date', [$start, $end]);
            $periodLabel = 'Periode '.$start->translatedFormat('d M Y').' s/d '.$end->translatedFormat('d M Y');
        } else {
            $query->whereMonth('loan_date', $month)->whereYear('loan_date', $year);
            $periodLabel = 'Bulan '.Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        }

        if ($status && $status !== 'all') {
            if ($status === 'TERLAMBAT') {
                $query->where(function ($q) {
                    $q->where('status', 'TERLAMBAT')
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'DIPINJAM')->where('due_date', '<', Carbon::today());
                        });
                });
            } elseif ($status === 'DIPINJAM') {
                $query->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today());
            } else {
                $query->where('status', $status);
            }
        }

        $loans = $query->orderBy('loan_date', 'asc')->get();

        $totalLoans = $loans->count();
        $borrowedLoans = $loans->where('status', 'DIPINJAM')->where('due_date', '>=', Carbon::today())->count();
        $returnedLoans = $loans->where('status', 'KEMBALI')->count();
        $overdueLoans = $loans->filter(function ($item) {
            return $item->status === 'TERLAMBAT' || ($item->status === 'DIPINJAM' && $item->due_date < Carbon::today());
        })->count();
        $totalFines = $loans->sum('fine_amount');

        $branding = $this->getSchoolBranding();

        return view('library.loans.print_report', array_merge(compact(
            'loans',
            'totalLoans',
            'borrowedLoans',
            'returnedLoans',
            'overdueLoans',
            'totalFines',
            'periodLabel'
        ), $branding));
    }

    /**
     * Cetak Kartu Anggota Perpustakaan Resmi (Print-Ready)
     */
    public function printMemberCard(LibraryMember $member): View
    {
        $member->load(['student.schoolClass', 'teacher', 'user']);
        $branding = $this->getSchoolBranding();
        $user = $member->user ?: auth()->user();

        return view('library.members.print_card', array_merge(compact('member', 'user'), $branding));
    }

    /**
     * Cetak Struk / Bukti Transaksi Peminjaman Buku Resmi
     */
    public function printLoanReceipt(LibraryLoan $loan): View
    {
        $loan->load(['book.category', 'member.student.schoolClass', 'member.teacher', 'approver', 'user']);
        $branding = $this->getSchoolBranding();

        return view('library.loans.print_receipt', array_merge(compact('loan'), $branding));
    }

    /**
     * Cetak Surat Keterangan Bebas Pustaka (Clearance Letter)
     */
    public function printClearanceCertificate(LibraryMember $member): View
    {
        $member->load(['student.schoolClass', 'teacher', 'user']);
        $branding = $this->getSchoolBranding();

        $activeLoansCount = LibraryLoan::where('member_id', $member->id)
            ->whereIn('status', ['DIPINJAM', 'TERLAMBAT'])
            ->count();
        $unpaidFines = (float) LibraryLoan::where('member_id', $member->id)
            ->where('fine_paid', false)
            ->sum('fine_amount');

        $isCleared = ($activeLoansCount === 0 && $unpaidFines <= 0);

        return view('library.members.clearance', array_merge(compact('member', 'activeLoansCount', 'unpaidFines', 'isCleared'), $branding));
    }
}
