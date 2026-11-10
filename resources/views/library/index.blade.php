@extends('layouts.app')

@section('title', 'Perpustakaan Terpadu & E-Book Digital')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Top Hero Banner Perpustakaan -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-amber-500/15 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Standar Nasional Perpustakaan (SNP) • Klasifikasi DDC 000-900
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Perpustakaan Terpadu & Digital</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Pusat sumber belajar modern SMA: Katalog Buku Fisik & Digital (E-Book Interaktif Flip HTML), Sirkulasi Peminjaman Mandiri, Presensi Pengunjung Barcode, serta Integrasi Master Data Siswa & Guru.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if($canManage)
                    <a href="{{ route('library.books.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Entri Buku Baru</span>
                    </a>
                    <a href="{{ route('library.visitors.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs sm:text-sm border border-slate-700 shadow-xs transition">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Buku Tamu Pengunjung</span>
                    </a>
                @else
                    <a href="{{ route('library.loans.my') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Peminjaman Saya</span>
                        @if($userPendingLoansCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-400 text-slate-950 font-black">{{ $userPendingLoansCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('library.members.my-card') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs sm:text-sm border border-slate-700 shadow-xs transition">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span>Kartu Anggota Digital</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Quick Stats Matrix Perpustakaan -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Judul Buku</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-1">{{ $totalBooks }} <span class="text-xs font-normal text-slate-400">Judul</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Eksemplar</div>
                <div class="text-xl sm:text-2xl font-black text-cyan-400 mt-1">{{ $totalCopies }} <span class="text-xs font-normal text-slate-400">Buku</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">E-Book Flip HTML</div>
                <div class="text-xl sm:text-2xl font-black text-amber-300 mt-1">{{ $digitalBooksCount }} <span class="text-xs font-normal text-slate-400">Digital</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sedang Dipinjam</div>
                <div class="text-xl sm:text-2xl font-black text-indigo-400 mt-1">{{ $activeLoansCount }} <span class="text-xs font-normal text-slate-400">Transaksi</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-rose-400 uppercase tracking-wider">Jatuh Tempo</div>
                <div class="text-xl sm:text-2xl font-black text-rose-400 mt-1">{{ $overdueLoansCount }} <span class="text-xs font-normal text-slate-400">Denda</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60">
                <div class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Pengunjung Hari Ini</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-400 mt-1">{{ $todayVisitorsCount }} <span class="text-xs font-normal text-slate-400">Orang</span></div>
            </div>
        </div>
    </div>


    @if($canManage && $pendingLoansCount > 0)
        <!-- Alert Banner Antrean Pengajuan Masuk untuk Pengelola -->
        <div class="p-4 sm:p-5 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-950 dark:text-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-xl shrink-0 shadow-md">
                    🔔
                </div>
                <div>
                    <div class="font-bold text-sm sm:text-base text-amber-900 dark:text-amber-100 flex items-center gap-2">
                        <span>Ada {{ $pendingLoansCount }} Pengajuan Peminjaman Buku Menunggu Persetujuan!</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500 text-slate-950 font-black uppercase">Perlu Diproses</span>
                    </div>
                    <div class="text-xs text-amber-800/80 dark:text-amber-300/80 mt-0.5">Siswa dan Guru telah mengajukan permohonan pinjam buku fisik secara mandiri melalui akun aplikasi.</div>
                </div>
            </div>
            <a href="{{ route('library.loans.index', ['status' => 'MENUNGGU']) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shrink-0 transition shadow-md">
                <span>Periksa Antrean Pengajuan &rarr;</span>
            </a>
        </div>
    @endif

    @if(! $canManage)
        <!-- Box Pemustaka Mandiri (Siswa & Guru) -->
        <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-900 border border-indigo-700/40 text-white flex flex-col md:flex-row md:items-center justify-between gap-5 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center text-2xl font-black shrink-0 shadow-lg">
                    📖
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-300 uppercase tracking-wider mb-0.5">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                        Portal Pemustaka Mandiri
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-white">Halo, {{ Auth::user()->name }}!</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        No. Anggota: <span class="font-mono font-bold text-amber-300">{{ $userMember->member_code ?? '-' }}</span> • 
                        Status: <span class="text-emerald-400 font-semibold">{{ $userMember->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-slate-800/90 border border-slate-700 text-center min-w-[90px]">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Aktif Dipinjam</div>
                    <div class="text-base font-black text-indigo-400">{{ $userActiveLoansCount }} <span class="text-[10px] font-normal text-slate-400">Buku</span></div>
                </div>
                <div class="px-4 py-2 rounded-2xl bg-slate-800/90 border border-slate-700 text-center min-w-[90px]">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Menunggu</div>
                    <div class="text-base font-black text-amber-400">{{ $userPendingLoansCount }} <span class="text-[10px] font-normal text-slate-400">Pengajuan</span></div>
                </div>
                <a href="{{ route('library.loans.my') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-md">
                    <span>Peminjaman Saya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- Section 1: E-Book Digital Flip HTML Unggulan -->
    <div class="bg-gradient-to-br from-indigo-950/80 via-slate-900 to-indigo-900/80 border border-indigo-800/40 rounded-3xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 border-b border-indigo-800/40 pb-4">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>E-Book Digital Reader Flip HTML</span>
                </div>
                <h2 class="text-xl font-black text-white">Buku Digital Interaktif (Efek 3D Flipbook)</h2>
                <p class="text-xs text-slate-300 mt-0.5">Buku dapat dibolak-balik halamannya secara nyata seperti membaca buku fisik di ponsel maupun laptop.</p>
            </div>
            <a href="{{ route('library.books.index', ['book_type' => 'digital_flip']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                <span>Lihat Seluruh E-Book</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($digitalBooks->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                Belum ada buku digital yang ditambahkan. Silakan tambahkan e-book melalui tombol Entri Buku Baru.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($digitalBooks as $ebook)
                    <div class="group relative bg-slate-900/90 rounded-2xl border border-indigo-700/40 p-4 hover:border-amber-400/80 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/10 flex flex-col justify-between">
                        <div>
                            <!-- Badge & Kategori -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-400/30 flex items-center gap-1">
                                    <span>📖 Flip HTML</span>
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">DDC {{ $ebook->ddc_code ?? '000' }}</span>
                            </div>

                            <!-- Judul & Pengarang -->
                            <h3 class="text-sm font-bold text-white group-hover:text-amber-300 transition line-clamp-2 leading-snug">
                                {{ $ebook->title }}
                            </h3>
                            <p class="text-xs text-indigo-300 font-medium mt-1 truncate">
                                ✍️ {{ $ebook->author }}
                            </p>

                            <!-- Sinopsis Singkat -->
                            <p class="text-xs text-slate-300 mt-2.5 line-clamp-3 leading-relaxed">
                                {{ $ebook->description ?? 'Buku digital teks interaktif Kurikulum Merdeka resmi Perpustakaan SMA Negeri 1 Lengkong.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>{{ $ebook->views_count }}x dibaca</span>
                            </span>

                            <a href="{{ route('library.books.flipbook', $ebook) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs shadow-md transition transform active:scale-95">
                                <span>Buka Flipbook</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 2: Buku Populer & Transaksi Sirkulasi Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Buku Terpopuler / Paling Banyak Dipinjam -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        🔥
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Buku Populer & Favorit</h2>
                        <p class="text-xs text-slate-500">Paling banyak dipinjam dan dibaca siswa</p>
                    </div>
                </div>
                <a href="{{ route('library.books.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Semua Buku &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($popularBooks as $pb)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800">
                        <div class="min-w-0">
                            <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide truncate">
                                {{ $pb->category->name ?? 'Klasifikasi DDC ' . ($pb->ddc_code ?? '000') }}
                            </div>
                            <a href="{{ route('library.books.show', $pb) }}" class="text-xs font-bold text-slate-900 dark:text-white hover:text-indigo-600 truncate block">
                                {{ $pb->title }}
                            </a>
                            <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                Penulis: {{ $pb->author }} • Rak: {{ $pb->rack_location ?? '-' }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                {{ $pb->loans_count }}x dipinjam
                            </span>
                            <div class="mt-1">
                                <a href="{{ route('library.books.show', $pb) }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Detail &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat peminjaman buku.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sirkulasi Peminjaman & Pengembalian Terkini -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                        🔄
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Sirkulasi Peminjaman Terbaru</h2>
                        <p class="text-xs text-slate-500">Status transaksi peminjaman & pengembalian</p>
                    </div>
                </div>
                <a href="{{ route('library.loans.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Kelola Sirkulasi &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentLoans as $loan)
                    @php
                        $statusColor = match($loan->status) {
                            'KEMBALI' => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'TERLAMBAT' => 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/20',
                            default => 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/20',
                        };
                    @endphp
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800">
                        <div class="min-w-0">
                            <div class="text-[10px] font-mono text-slate-400">{{ $loan->loan_code }}</div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                {{ $loan->book->title ?? 'Buku' }}
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 truncate">
                                👤 {{ $loan->member->name ?? 'Anggota' }} • {{ $loan->member->member_class_or_title ?? '-' }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                {{ $loan->status }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1">
                                Jatuh Tempo: {{ $loan->due_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada transaksi peminjaman aktif.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section 3: Klasifikasi DDC (Dewey Decimal Classification) Standar Perpusnas -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Klasifikasi DDC Koleksi Pustaka (000 - 900)</h2>
                <p class="text-xs text-slate-500">Standar Klasifikasi Internasional Perpustakaan Nasional RI</p>
            </div>
            <span class="text-xs font-bold text-slate-400">10 Kelas Utama DDC</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($categories as $cat)
                <a href="{{ route('library.books.index', ['category_id' => $cat->id]) }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-sm transition group">
                    <div class="text-xs font-black font-mono text-indigo-600 dark:text-indigo-400">DDC {{ $cat->ddc_code }}</div>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 line-clamp-2 group-hover:text-indigo-600 transition">{{ $cat->name }}</div>
                    <div class="text-[11px] text-slate-400 mt-2 font-medium">{{ $cat->books_count }} Judul Buku</div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

