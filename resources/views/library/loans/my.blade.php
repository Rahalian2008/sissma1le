@extends('layouts.app')

@section('title', 'Peminjaman Saya - Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-2xl shrink-0">
                📖
            </div>
            <div>
                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-0.5">Portal Pemustaka</div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Peminjaman Buku Saya</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Anggota: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $member->name }}</span> • 
                    No: <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $member->member_code }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('library.books.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Pinjam Buku</span>
            </a>
            <a href="{{ route('library.members.my-card') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition flex items-center gap-1.5">
                <span>🪪 Kartu Anggota</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('library.loans.my', ['status' => 'all']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ (!request('status') || request('status') === 'all') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600' }}">
            Semua ({{ $allCount }})
        </a>
        <a href="{{ route('library.loans.my', ['status' => 'MENUNGGU']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('status') === 'MENUNGGU' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20' }}">
            <span>⏳ Menunggu Persetujuan</span>
            @if($pendingCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'MENUNGGU' ? 'bg-slate-950 text-amber-300' : 'bg-amber-500 text-slate-950' }} font-black">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('library.loans.my', ['status' => 'DIPINJAM']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'DIPINJAM' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600' }}">
            📖 Sedang Dipinjam ({{ $activeCount }})
        </a>
        <a href="{{ route('library.loans.my', ['status' => 'TERLAMBAT']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('status') === 'TERLAMBAT' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' }}">
            <span>⚠️ Terlambat</span>
            @if($overdueCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'TERLAMBAT' ? 'bg-white text-rose-700' : 'bg-rose-500 text-white' }} font-black">{{ $overdueCount }}</span>
            @endif
        </a>
        <a href="{{ route('library.loans.my', ['status' => 'KEMBALI']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'KEMBALI' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-emerald-600' }}">
            ✅ Riwayat Selesai ({{ $returnedCount }})
        </a>
    </div>

    <!-- Petunjuk Pengambilan Buku -->
    <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 text-xs text-indigo-900 dark:text-indigo-200 flex items-start gap-3">
        <span class="text-base shrink-0 mt-0.5">ℹ️</span>
        <div class="leading-relaxed">
            <strong>Alur Peminjaman Buku:</strong> Setelah mengajukan peminjaman, silakan datang ke meja sirkulasi Perpustakaan Sekolah. Tunjukkan <strong>Kartu Anggota Digital</strong> Anda kepada petugas perpustakaan untuk memverifikasi dan mengambil buku fisik.
        </div>
    </div>

    <!-- Daftar Peminjaman -->
    <div class="space-y-4">
        @if($loans->isEmpty())
            <div class="p-12 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl text-slate-400 text-xs">
                <svg class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Belum ada data peminjaman buku untuk kategori ini.
                <div class="mt-4">
                    <a href="{{ route('library.books.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs inline-block">
                        Jelajahi Katalog Buku &rarr;
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($loans as $loan)
                    @php
                        $book = $loan->book;
                        $isOverdue = $loan->isOverdue();
                        $daysOverdue = $loan->days_overdue;
                        $daysLeft = now()->startOfDay()->diffInDays($loan->due_date, false);
                    @endphp
                    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:border-indigo-400/60 transition">
                        <div>
                            <!-- Header Card: Kode & Status -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-mono text-xs font-bold text-slate-400">{{ $loan->loan_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $loan->status_badge_classes }}">
                                    {{ $loan->status_label }}
                                </span>
                            </div>

                            <!-- Judul & Pengarang -->
                            <div class="flex gap-3.5">
                                <div class="w-16 h-22 rounded-xl overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                    @if($book && $book->cover_image)
                                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-2xl">📚</div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider truncate">
                                        {{ $book->category->name ?? 'DDC ' . ($book->ddc_code ?? '000') }}
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug mt-0.5">
                                        {{ $book->title ?? 'Buku Tidak Tersedia' }}
                                    </h3>
                                    <div class="text-xs text-slate-500 mt-1 truncate">
                                        Penulis: {{ $book->author ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Rak: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $book->rack_location ?? 'Meja Sirkulasi' }}</span> • No. Panggil: {{ $book->call_number ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal & Informasi Tenggat -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase">Tanggal Pinjam / Ajukan</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $loan->request_date ? $loan->request_date->translatedFormat('d M Y') : $loan->loan_date->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase">Batas Pengembalian</span>
                                    <span class="font-bold {{ $isOverdue && $loan->status !== 'KEMBALI' ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-800 dark:text-slate-200' }}">
                                        {{ $loan->due_date->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Sisa Hari / Denda -->
                            @if($loan->status === 'DIPINJAM')
                                <div class="mt-2.5 px-3 py-1.5 rounded-xl {{ $daysLeft <= 1 ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300' : 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300' }} text-xs font-semibold flex items-center justify-between">
                                    <span>⏱️ Sisa Waktu Pinjam:</span>
                                    <span class="font-bold">{{ $daysLeft > 0 ? $daysLeft . ' Hari Lagi' : 'Jatuh tempo hari ini!' }}</span>
                                </div>
                            @elseif($loan->status === 'TERLAMBAT' || ($loan->status === 'DIPINJAM' && $isOverdue))
                                <div class="mt-2.5 px-3 py-1.5 rounded-xl bg-rose-500/15 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-center justify-between">
                                    <span>⚠️ Terlambat {{ $daysOverdue }} Hari</span>
                                    <span class="font-bold">Denda: Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                </div>
                            @elseif($loan->status === 'MENUNGGU')
                                <div class="mt-2.5 px-3 py-1.5 rounded-xl bg-amber-500/15 text-amber-700 dark:text-amber-300 text-xs">
                                    ⏳ Menunggu verifikasi petugas perpustakaan sekolah.
                                </div>
                            @elseif($loan->status === 'DITOLAK')
                                <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-xs text-rose-700 dark:text-rose-300">
                                    <strong>Alasan Ditolak:</strong> {{ $loan->rejection_reason ?: 'Tidak memenuhi syarat peminjaman.' }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center gap-1.5">
                                @if($book && ($book->is_digital_flip || $book->book_type === 'digital'))
                                    <a href="{{ route('library.books.flipbook', $book) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                                        <span>📖 Baca Flip HTML</span>
                                    </a>
                                @endif
                                <a href="{{ route('library.books.show', $book) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                                    Detail Buku
                                </a>
                            </div>

                            <div class="flex items-center gap-1.5">
                                @if($loan->status === 'MENUNGGU')
                                    <!-- Batalkan Pengajuan -->
                                    <form action="{{ route('library.loans.cancel', $loan) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan pinjaman buku ini?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 text-xs font-bold transition cursor-pointer">
                                            ✕ Batalkan Pengajuan
                                        </button>
                                    </form>
                                @elseif($loan->status === 'DIPINJAM' && $loan->extension_count < 2 && ! $isOverdue)
                                    <!-- Ajukan Perpanjangan -->
                                    <form action="{{ route('library.loans.extend', $loan) }}" method="POST" onsubmit="return confirm('Perpanjang masa pinjam buku ini +7 hari?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                            + Perpanjang 7 Hari
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

