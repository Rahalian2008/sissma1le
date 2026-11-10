@extends('layouts.app')

@section('title', 'Detail Buku: ' . $book->title)

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Top Action Nav -->
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('library.books.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-50 transition">
            &larr; Kembali ke Katalog
        </a>

        <div class="flex items-center gap-2">
            @if($book->is_digital_flip || $book->book_type === 'digital')
                <a href="{{ route('library.books.flipbook', $book) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs shadow-md transition transform active:scale-95">
                    <span>📖 Buka E-Book Flip HTML</span>
                </a>
            @endif

            <button type="button" onclick="openPanduanKatalogisasiModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 text-amber-800 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800 transition shadow-xs cursor-pointer" title="Pedoman Pembuatan Nomor Induk, Barcode & Call Number Standar Perpusnas & DDC">
                <span>📐 Panduan No. Induk & Call Number</span>
            </button>

            @if($canManage)
                <a href="{{ route('library.books.labels', $book) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>🏷️ Cetak Label & Barcode</span>
                </a>
                <a href="{{ route('library.books.edit', $book) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                </a>
                <form action="{{ route('library.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini dari katalog perpustakaan?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 text-xs font-bold transition cursor-pointer">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row gap-6 lg:gap-8">
            <!-- Cover Image Preview -->
            <div class="w-full md:w-64 shrink-0 flex flex-col items-center">
                <div class="w-full max-w-[240px] aspect-[2/3] rounded-2xl overflow-hidden shadow-xl border-2 border-slate-700/60 relative group bg-slate-800">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    
                    @if($book->is_digital_flip || $book->book_type === 'digital')
                        <a href="{{ route('library.books.flipbook', $book) }}" class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 text-white font-bold text-xs p-4 text-center">
                            <span class="w-12 h-12 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-xl shadow-lg">📖</span>
                            <span>Buka Flipbook Sekarang</span>
                        </a>
                    @endif
                </div>

                <div class="w-full max-w-[240px] mt-4 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 text-center space-y-2">
                    <div class="text-[10px] font-bold text-slate-400 uppercase">Nomor Induk & Barcode</div>
                    <div class="p-1.5 bg-white rounded-lg border border-slate-200 dark:border-slate-700 flex justify-center overflow-hidden">
                        {!! $book->barcode_svg !!}
                    </div>
                    <div class="font-mono text-xs font-black text-slate-900 dark:text-white">{{ $book->book_code }}</div>
                    <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">Call Number: {{ $book->call_number ?? '-' }}</div>

                    @if($canManage)
                        <div class="pt-1 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('library.books.labels', $book) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                <span>🏷️ Buka Lembar Cetak Stiker &rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Information -->
            <div class="flex-1 space-y-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                            {{ $book->category->name ?? 'Klasifikasi DDC ' . ($book->ddc_code ?? '000') }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            Tipe: {{ $book->book_type }}
                        </span>
                        @if($book->is_digital_flip)
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-500 border border-amber-400/30">
                                ✨ E-Book Flip HTML
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white leading-tight">
                        {{ $book->title }}
                    </h1>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mt-1">
                        Penulis: <span class="text-slate-900 dark:text-white">{{ $book->author }}</span>
                    </p>
                </div>

                <!-- Quick Status Banner -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Ketersediaan</div>
                        <div class="text-sm font-black mt-0.5 {{ $book->is_available ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }}">
                            {{ $book->book_type === 'digital' ? 'Akses Terbuka' : ($book->available_copies > 0 ? $book->available_copies . ' Tersedia' : 'Sedang Dipinjam') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Total Eksemplar</div>
                        <div class="text-sm font-black text-slate-900 dark:text-white mt-0.5">
                            {{ $book->total_copies }} Eksemplar
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Nomor Panggil</div>
                        <div class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">
                            {{ $book->call_number ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Lokasi Rak Fisik</div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                            {{ $book->rack_location ?? 'Digital Server' }}
                        </div>
                    </div>
                </div>

                <!-- Sinopsis -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sinopsis & Ringkasan</h3>
                    <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $book->description ?: 'Belum ada sinopsis untuk buku ini.' }}
                    </p>
                </div>

                <!-- Metadata Lengkap -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Metadata Bibliografis</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Penerbit:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->publisher ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tahun Terbit:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->publish_year ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">ISBN / ISSN:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $book->isbn ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Bahasa:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->language ?? 'Indonesia' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Sumber Pengadaan:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->source ?? 'Pengadaan Sekolah' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Statistik Sirkulasi:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->loans_count }}x dipinjam • {{ $book->views_count }}x dibaca digital</span>
                        </div>
                    </div>
                </div>

                <!-- Box Aksi Pengajuan Peminjaman Buku (Khusus Buku Fisik / Hybrid) -->
                @if($book->book_type !== 'digital')
                    <div id="ajukan-pinjam" class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                        @if($userExistingLoan)
                            <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                        <span>Status Peminjaman Anda:</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $userExistingLoan->status_badge_classes }}">
                                            {{ $userExistingLoan->status_label }}
                                        </span>
                                    </div>
                                    <div class="text-slate-600 dark:text-slate-400 mt-0.5">
                                        Kode Transaksi: <span class="font-mono font-bold">{{ $userExistingLoan->loan_code }}</span> • Batas Waktu: {{ $userExistingLoan->due_date->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                                <a href="{{ route('library.loans.my') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shrink-0 transition">
                                    Lihat di Peminjaman Saya &rarr;
                                </a>
                            </div>
                        @elseif($book->available_copies <= 0)
                            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-xs text-rose-800 dark:text-rose-300 flex items-center gap-2.5">
                                <span class="text-lg">⚠️</span>
                                <div>
                                    <strong>Stok Eksemplar Sedang Habis:</strong> Seluruh buku fisik saat ini sedang dipinjam oleh pemustaka lain. Anda dapat memeriksa kembali secara berkala setelah ada pengembalian.
                                </div>
                            </div>
                        @else
                            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Layanan Peminjaman Online</h4>
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Ajukan Peminjaman Buku Ini</h3>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                        {{ $book->available_copies }} Eksemplar Siap Pinjam
                                    </span>
                                </div>

                                <form action="{{ route('library.books.request', $book) }}" method="POST" class="space-y-3.5">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <label class="block text-slate-500 mb-1 font-medium">Nama Pemustaka / Akun:</label>
                                            <input type="text" value="{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})" readonly class="w-full px-3 py-2 bg-slate-200/70 dark:bg-slate-700/60 rounded-xl text-slate-700 dark:text-slate-300 font-semibold cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 mb-1 font-medium">Rencana Tanggal Pengambilan:</label>
                                            <input type="date" name="pickup_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white font-semibold">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-slate-500 mb-1 text-xs font-medium">Catatan / Keperluan (Opsional):</label>
                                        <input type="text" name="notes" placeholder="Contoh: Untuk referensi tugas Biologi atau persiapan portofolio..." class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <div class="text-[11px] text-slate-400">
                                            Durasi pinjaman standar: <strong class="text-slate-600 dark:text-slate-300">7 Hari</strong> (bisa diperpanjang)
                                        </div>
                                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition transform active:scale-95 cursor-pointer">
                                            Kirim Pengajuan Pinjam &rarr;
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Peminjaman Buku Ini -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span>🔄 Riwayat Sirkulasi Peminjaman Buku Ini</span>
        </h3>

        @if($recentLoans->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">
                Belum ada riwayat peminjaman untuk judul buku ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Kode Pinjam</th>
                            <th class="px-4 py-3">Nama Peminjam</th>
                            <th class="px-4 py-3">Tgl Pinjam</th>
                            <th class="px-4 py-3">Jatuh Tempo</th>
                            <th class="px-4 py-3">Tgl Kembali</th>
                            <th class="px-4 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentLoans as $loan)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $loan->loan_code }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                                    {{ $loan->member->name ?? 'Anggota' }}
                                    <div class="text-[10px] text-slate-400">{{ $loan->member->member_class_or_title ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    @php
                                        $badgeColor = match($loan->status) {
                                            'KEMBALI' => 'bg-emerald-500/15 text-emerald-600',
                                            'TERLAMBAT' => 'bg-rose-500/15 text-rose-600',
                                            default => 'bg-amber-500/15 text-amber-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badgeColor }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@include('library.partials.guidelines_modal')
@endsection

