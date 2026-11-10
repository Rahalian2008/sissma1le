@extends('layouts.app')

@section('title', 'Laporan Inventaris & Sirkulasi Buku Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header & Action Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 rounded-3xl shadow-xl text-white">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                <span>📑</span>
                <span>Standar Nasional Perpustakaan (SNP) • Inventaris Koleksi</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white flex items-center gap-3">
                <span>Laporan Inventaris, Sirkulasi & Status Buku</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                Rekapitulasi resmi mengenai jumlah total judul buku, ketersediaan eksemplar di rak (*buku yang ada*), buku yang sedang dipinjam, serta status fisik dan digital koleksi perpustakaan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start md:self-auto shrink-0">
            <!-- Cetak Laporan Resmi -->
            <a href="{{ route('library.reports.print', request()->query()) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm transition flex items-center gap-2 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Resmi</span>
            </a>

            <!-- Ekspor CSV/Excel -->
            <a href="{{ route('library.reports.export', request()->query()) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm transition flex items-center gap-2 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Ekspor CSV / Excel</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI MATRIKS REKAPITULASI BUKU -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- 1. Total Judul Buku -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Judul Buku</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1.5">{{ number_format($totalTitles) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Koleksi Terdaftar</div>
        </div>

        <!-- 2. Total Eksemplar -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Eksemplar</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1.5">{{ number_format($totalCopies) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Fisik & Digital</div>
        </div>

        <!-- 3. Buku yang Ada (Tersedia di Rak) -->
        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider flex items-center justify-between">
                <span>Buku yang Ada</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1.5">{{ number_format($availableCopies) }}</div>
            <div class="text-[11px] text-emerald-800 dark:text-emerald-300 font-medium mt-0.5">
                {{ round(($availableCopies / max(1, $totalCopies)) * 100) }}% Tersedia di Rak
            </div>
        </div>

        <!-- 4. Buku yang Sedang Dipinjam -->
        <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider flex items-center justify-between">
                <span>Buku Dipinjam</span>
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            </div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1.5">{{ number_format($borrowedCopies) }}</div>
            <div class="text-[11px] text-indigo-800 dark:text-indigo-300 font-medium mt-0.5">
                {{ round(($borrowedCopies / max(1, $totalCopies)) * 100) }}% Beredar di Pemustaka
            </div>
        </div>

        <!-- 5. E-Book Digital Flip HTML -->
        <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 shadow-xs">
            <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center justify-between">
                <span>E-Book Flip 3D</span>
                <span class="text-xs">✨</span>
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1.5">{{ number_format($digitalBooksCount) }}</div>
            <div class="text-[11px] text-amber-800 dark:text-amber-300 font-medium mt-0.5">Akses Digital Mandiri</div>
        </div>

        <!-- 6. Stok Habis / Perlu Pengadaan -->
        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 shadow-xs">
            <div class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider flex items-center justify-between">
                <span>Stok 0 (Habis)</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1.5">{{ number_format($outOfStockCount) }}</div>
            <div class="text-[11px] text-rose-800 dark:text-rose-300 font-medium mt-0.5">Semua Eksemplar Keluar</div>
        </div>
    </div>

    <!-- Filter & Pencarian Laporan -->
    <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('library.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Filter Status Ketersediaan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Ketersediaan</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="available" {{ $status === 'available' ? 'selected' : '' }}>📗 Tersedia di Rak (Ada Eksemplar)</option>
                    <option value="borrowed" {{ $status === 'borrowed' ? 'selected' : '' }}>📘 Sedang Dipinjam</option>
                    <option value="out_of_stock" {{ $status === 'out_of_stock' ? 'selected' : '' }}>📕 Habis Dipinjam (Stok 0)</option>
                    <option value="digital" {{ $status === 'digital' ? 'selected' : '' }}>💻 E-Book Digital Flip</option>
                </select>
            </div>

            <!-- Filter Kategori / DDC -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori / DDC</label>
                <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->code ?? '' }} - {{ $cat->name }} ({{ $cat->books_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tipe Koleksi -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Format Koleksi</label>
                <select name="book_type" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <option value="all" {{ $bookType === 'all' ? 'selected' : '' }}>Semua Format</option>
                    <option value="physical" {{ $bookType === 'physical' ? 'selected' : '' }}>Buku Fisik</option>
                    <option value="digital_flip" {{ $bookType === 'digital_flip' ? 'selected' : '' }}>E-Book Flip HTML 3D</option>
                    <option value="digital" {{ $bookType === 'digital' ? 'selected' : '' }}>Digital Umum</option>
                </select>
            </div>

            <!-- Search Input -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Judul / ISBN / Rak</label>
                <div class="flex items-center gap-1.5">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                        Cari
                    </button>
                    @if($search || $status !== 'all' || $categoryId || $bookType)
                        <a href="{{ route('library.reports.index') }}" class="p-2 text-xs text-slate-400 hover:text-white" title="Reset Filter">✕</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- MAIN REPORT TABLE (TABEL INVENTARIS & STATUS BUKU) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📚 Daftar Koleksi, Jumlah Eksemplar & Status Buku</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Rincian per judul: total eksemplar, stok ada di rak, jumlah sedang dipinjam, dan status ketersediaan.</p>
            </div>
            <span class="text-xs font-mono font-semibold text-slate-400 self-start sm:self-auto">
                Menampilkan {{ $books->total() }} Judul Buku
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Kode & ISBN</th>
                        <th class="py-3.5 px-4">Judul Buku & Penulis</th>
                        <th class="py-3.5 px-4">Kategori / Rak</th>
                        <th class="py-3.5 px-4 text-center">Total Eks</th>
                        <th class="py-3.5 px-4 text-center">Buku Ada (Rak)</th>
                        <th class="py-3.5 px-4 text-center">Dipinjam</th>
                        <th class="py-3.5 px-4 text-center">Status Ketersediaan</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($books as $idx => $book)
                        @php
                            $borrowed = max(0, $book->total_copies - $book->available_copies);
                            $pctAvailable = $book->total_copies > 0 ? round(($book->available_copies / $book->total_copies) * 100) : 100;
                            $isDigital = $book->book_type === 'digital' || $book->is_digital_flip;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $books->firstItem() + $idx }}
                            </td>

                            <!-- Kode & ISBN -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $book->book_code }}</span>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    ISBN: {{ $book->isbn ?? 'Katalog' }}
                                </div>
                            </td>

                            <!-- Judul & Penulis -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white max-w-sm truncate">
                                    <a href="{{ route('library.books.show', $book) }}" class="hover:text-indigo-600 transition">
                                        {{ $book->title }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate mt-0.5">
                                    ✍️ {{ $book->author }} • {{ $book->publisher ?? 'SMAN 1 Lengkong' }} ({{ $book->publish_year ?? '-' }})
                                </div>
                            </td>

                            <!-- Kategori & Lokasi Rak -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $book->category->name ?? 'Umum' }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-mono">
                                    <span>📍</span>
                                    <span>{{ $book->rack_location ?? 'Koleksi Cloud' }}</span>
                                </div>
                            </td>

                            <!-- Total Eksemplar -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-extrabold text-slate-800 dark:text-slate-200 font-mono text-sm">
                                    {{ $book->total_copies }}
                                </span>
                            </td>

                            <!-- Buku Ada (Tersedia di Rak) -->
                            <td class="py-3.5 px-4 text-center">
                                @if($isDigital)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-500 border border-amber-400/30">
                                        Digital Cloud
                                    </span>
                                @elseif($book->available_copies > 0)
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-black font-mono border border-emerald-200 dark:border-emerald-800/60 text-xs">
                                        {{ $book->available_copies }} Eks
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 font-black font-mono border border-rose-200 dark:border-rose-800/60 text-xs">
                                        0 Eks
                                    </span>
                                @endif
                            </td>

                            <!-- Buku Dipinjam -->
                            <td class="py-3.5 px-4 text-center">
                                @if($borrowed > 0)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-black font-mono border border-indigo-200 dark:border-indigo-800/60 text-xs">
                                        {{ $borrowed }} Eks
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono text-xs">-</span>
                                @endif
                            </td>

                            <!-- Status Ketersediaan (Visual Indicator) -->
                            <td class="py-3.5 px-4 text-center">
                                @if($isDigital)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">
                                        <span>✨</span>
                                        <span>Akses E-Book 3D</span>
                                    </span>
                                @elseif($book->available_copies >= $book->total_copies)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Tersedia Lengkap</span>
                                    </span>
                                @elseif($book->available_copies > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Tersedia Sebagian</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Habis Dipinjam</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('library.books.show', $book) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold transition" title="Lihat Detail Buku">
                                        👁️ Detail
                                    </a>
                                    @if($book->is_digital_flip)
                                        <a href="{{ route('library.books.flipbook', $book) }}" class="p-1.5 rounded-lg bg-amber-500/20 text-amber-500 hover:bg-amber-500 hover:text-slate-950 text-xs font-bold transition" title="Buka Flip Reader 3D">
                                            📖 Baca
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                Tidak ada data buku yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $books->links() }}
            </div>
        @endif
    </div>

    <!-- SECTION BUKU YANG SEDANG DIPINJAM SAAT INI (ACTIVE LOANS BREAKDOWN) -->
    @if($activeLoans->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🔄 Transaksi Sirkulasi: Buku yang Sedang Beredar / Dipinjam</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar buku fisik yang saat ini berada di tangan pemustaka beserta tenggat waktu pengembalian.</p>
                </div>
                <a href="{{ route('library.loans.index') }}" class="text-xs text-indigo-600 hover:underline font-bold">
                    Kelola Semua Sirkulasi &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($activeLoans as $loan)
                    @php
                        $isOverdue = $loan->due_date < \Carbon\Carbon::today();
                        $diffDays = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border {{ $isOverdue ? 'border-rose-300 dark:border-rose-800/60 bg-rose-50/30' : 'border-slate-200 dark:border-slate-700/60' }} flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-mono font-bold text-slate-500">{{ $loan->loan_code }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isOverdue ? 'bg-rose-500 text-white' : 'bg-indigo-600 text-white' }}">
                                    {{ $loan->status }}
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" title="{{ $loan->book->title ?? '-' }}">
                                📚 {{ $loan->book->title ?? 'Buku Perpustakaan' }}
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                Peminjam: <strong>{{ $loan->member->name ?? $loan->borrower_name }}</strong> ({{ $loan->member->member_class_or_title ?? '-' }})
                            </p>
                        </div>

                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-[10px] font-mono">
                            <span class="text-slate-400">Jatuh Tempo: {{ $loan->due_date->format('d/m/Y') }}</span>
                            @if($isOverdue)
                                <span class="font-bold text-rose-600 dark:text-rose-400">Terlambat {{ abs($diffDays) }} Hari</span>
                            @else
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Sisa {{ $diffDays }} Hari</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SECTION REKAPITULASI DDC & KATEGORI -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
            📊 Rekapitulasi Koleksi Berdasarkan Klasifikasi Dewey Decimal (DDC)
        </h3>
        <p class="text-xs text-slate-500 mb-4">Sebaran jumlah judul dan eksemplar buku per kategori bidang keilmuan.</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($categoryBreakdown as $c)
                @php
                    $catTotal = $c->books_sum_total_copies ?? 0;
                    $catAvail = $c->books_sum_available_copies ?? 0;
                    $catBorrow = max(0, $catTotal - $catAvail);
                @endphp
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-center">
                    <div class="font-mono text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $c->code ?? 'DDC' }}</div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate mt-0.5">{{ $c->name }}</div>
                    <div class="mt-2 text-base font-black text-slate-800 dark:text-slate-100 font-mono">{{ $c->books_count }} <span class="text-[10px] font-normal text-slate-400">Judul</span></div>
                    <div class="mt-1 text-[10px] text-slate-400 flex items-center justify-around border-t border-slate-200 dark:border-slate-700 pt-1.5">
                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $catAvail }} Ada</span>
                        <span>•</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $catBorrow }} Pinjam</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

