@extends('layouts.app')

@section('title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">
                <span>Perpustakaan SMAN 1 Lengkong</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>📚 Katalog Buku Fisik & Digital</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Koleksi buku teks pelajaran, referensi, sastra fiksi, dan buku digital interaktif Flip HTML.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="openPanduanKatalogisasiModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 text-amber-800 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800 transition shadow-xs cursor-pointer" title="Pedoman Pembuatan Nomor Induk, Barcode & Call Number Standar Perpusnas & DDC">
                <span>📐 Panduan No. Induk & Call Number</span>
            </button>

            @if($canManage)
                <a href="{{ route('library.books.print_labels') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition cursor-pointer" title="Cetak Stiker Label Punggung Buku & Barcode Nomor Induk (A4 / F4)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Label & Barcode</span>
                </a>
                <a href="{{ route('library.books.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Buku</span>
                </a>
            @else
                <a href="{{ route('library.loans.my') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition">
                    <span>📖 Peminjaman Saya</span>
                </a>
            @endif
            <a href="{{ route('library.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form action="{{ route('library.books.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, penerbit, nomor ISBN/Barcode..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Filter Kategori DDC -->
            <select name="category_id" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                <option value="">Semua Kategori DDC</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        DDC {{ $cat->ddc_code }} - {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Tipe Buku -->
            <select name="book_type" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                <option value="">Semua Tipe Buku</option>
                <option value="digital_flip" {{ request('book_type') == 'digital_flip' ? 'selected' : '' }}>✨ E-Book Flip HTML</option>
                <option value="digital" {{ request('book_type') == 'digital' ? 'selected' : '' }}>Digital (E-Book)</option>
                <option value="fisik" {{ request('book_type') == 'fisik' ? 'selected' : '' }}>Buku Fisik</option>
                <option value="hybrid" {{ request('book_type') == 'hybrid' ? 'selected' : '' }}>Hybrid (Fisik & Digital)</option>
            </select>

            <!-- Filter Ketersediaan -->
            <select name="availability" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                <option value="">Semua Status Stok</option>
                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia untuk Dipinjam</option>
                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Sedang Habis Dipinjam</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'category_id', 'book_type', 'availability']))
                <a href="{{ route('library.books.index') }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Grid Koleksi Buku -->
    <div>
        @if($books->isEmpty())
            <div class="py-16 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
                <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-3xl mx-auto mb-3">📚</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Tidak ada buku ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau reset filter kategori buku di atas.</p>
                <div class="mt-4">
                    <a href="{{ route('library.books.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs">
                        + Tambah Buku Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($books as $b)
                    <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs hover:shadow-lg hover:border-indigo-500/50 transition-all duration-300 flex flex-col justify-between">
                        <!-- Top Cover Banner / Thumbnail -->
                        <div class="relative h-44 bg-slate-800 overflow-hidden flex items-center justify-center">
                            @if($b->cover_image && (Illuminate\Support\Facades\Storage::disk('public')->exists($b->cover_image) || file_exists(public_path('storage/' . $b->cover_image))))
                                <img src="{{ asset('storage/' . $b->cover_image) }}" alt="{{ $b->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <img src="{{ asset('images/default-book-cover.svg') }}" alt="Default Cover" class="w-full h-full object-cover">
                            @endif

                            <!-- Badges di pojok cover -->
                            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1">
                                @if($b->is_digital_flip || $b->book_type === 'digital')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 shadow-sm">
                                        ✨ Flip HTML
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-xs">
                                    {{ $b->book_type }}
                                </span>
                                @if(isset($userLoansMap[$b->id]))
                                    @if($userLoansMap[$b->id] === 'MENUNGGU')
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-400 text-slate-950 shadow-xs">
                                            ⏳ Diajukan
                                        </span>
                                    @elseif($userLoansMap[$b->id] === 'DIPINJAM')
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-indigo-500 text-white shadow-xs">
                                            📖 Anda Pinjam
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <div class="absolute top-2.5 right-2.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-900/90 text-cyan-300 border border-cyan-500/30">
                                    DDC {{ $b->ddc_code ?? '000' }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Content -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide truncate">
                                    {{ $b->category->name ?? 'Klasifikasi DDC ' . ($b->ddc_code ?? '000') }}
                                </div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white line-clamp-2 mt-1 group-hover:text-indigo-600 transition leading-snug">
                                    {{ $b->title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 truncate">✍️ {{ $b->author }}</p>
                                
                                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span>Lokasi Rak:</span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[130px]">{{ $b->rack_location ?? 'Digital' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>Stok Eksemplar:</span>
                                        @if($b->book_type === 'digital')
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Akses Digital</span>
                                        @else
                                            <span class="font-bold {{ $b->available_copies > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ $b->available_copies }} / {{ $b->total_copies }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('library.books.show', $b) }}" class="flex-1 text-center px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition">
                                        Detail
                                    </a>

                                    @if($b->is_digital_flip || $b->book_type === 'digital')
                                        <a href="{{ route('library.books.flipbook', $b) }}" class="flex-1 text-center px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs shadow-xs transition" title="Buka dengan E-Book Reader Flip HTML">
                                            📖 Baca Flip
                                        </a>
                                    @elseif(! $canManage && $b->available_copies > 0 && ! isset($userLoansMap[$b->id]))
                                        <a href="{{ route('library.books.show', $b) }}#ajukan-pinjam" class="flex-1 text-center px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition">
                                            Ajukan Pinjam
                                        </a>
                                    @endif
                                </div>

                                @if($canManage)
                                    <div class="flex items-center gap-1.5 pt-1.5 border-t border-dashed border-slate-200 dark:border-slate-800">
                                        <a href="{{ route('library.books.edit', $b) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 font-bold text-xs transition cursor-pointer" title="Edit Data Buku">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('library.books.destroy', $b) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku \'{{ addslashes($b->title) }}\' dari katalog perpustakaan?')" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 font-bold text-xs transition cursor-pointer" title="Hapus Buku dari Katalog">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>

@include('library.partials.guidelines_modal')
@endsection

