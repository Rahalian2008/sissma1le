@extends('layouts.app')

@section('title', 'Sirkulasi Peminjaman & Pengembalian - Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Manajemen Sirkulasi</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>🔄 Sirkulasi Peminjaman & Pengembalian</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pencatatan peminjaman, perpanjangan, pengembalian buku, dan perhitungan denda keterlambatan otomatis.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openNewLoanModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Catat Peminjaman Baru</span>
            </button>
            <a href="{{ route('library.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('library.loans.index', ['status' => 'all', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ (!request('status') || request('status') === 'all') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600' }}">
            Semua Transaksi ({{ $allCount }})
        </a>
        <a href="{{ route('library.loans.index', ['status' => 'MENUNGGU', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('status') === 'MENUNGGU' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20' }}">
            <span>⏳ Antrean Pengajuan</span>
            @if($pendingCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'MENUNGGU' ? 'bg-slate-950 text-amber-300' : 'bg-amber-500 text-slate-950' }} font-black">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('library.loans.index', ['status' => 'DIPINJAM', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'DIPINJAM' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600' }}">
            📖 Sedang Dipinjam ({{ $borrowedCount }})
        </a>
        <a href="{{ route('library.loans.index', ['status' => 'TERLAMBAT', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('status') === 'TERLAMBAT' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' }}">
            <span>⚠️ Terlambat / Denda</span>
            @if($overdueCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'TERLAMBAT' ? 'bg-white text-rose-700' : 'bg-rose-500 text-white' }} font-black">{{ $overdueCount }}</span>
            @endif
        </a>
        <a href="{{ route('library.loans.index', ['status' => 'KEMBALI', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'KEMBALI' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-emerald-600' }}">
            ✅ Dikembalikan ({{ $returnedCount }})
        </a>
        <a href="{{ route('library.loans.index', ['status' => 'DITOLAK', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'DITOLAK' ? 'bg-slate-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-slate-700' }}">
            ✕ Ditolak / Batal ({{ $rejectedCount }})
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form action="{{ route('library.loans.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode pinjam, judul buku, nama siswa/guru, atau NISN/NIP..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                Cari Data
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('library.loans.index', ['status' => request('status', 'all')]) }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Transaksi Sirkulasi -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        @if($loans->isEmpty())
            <div class="py-16 text-center text-slate-400 text-xs">
                Tidak ada data peminjaman buku yang sesuai dengan filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5">Kode Pinjam</th>
                            <th class="px-4 py-3.5">Koleksi Buku</th>
                            <th class="px-4 py-3.5">Peminjam</th>
                            <th class="px-4 py-3.5">Tgl Pinjam</th>
                            <th class="px-4 py-3.5">Jatuh Tempo</th>
                            <th class="px-4 py-3.5">Denda Keterlambatan</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($loans as $loan)
                            @php
                                $isOverdue = $loan->isOverdue();
                                $daysOverdue = $loan->days_overdue;
                                $statusBadge = match($loan->status) {
                                    'KEMBALI' => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                    'TERLAMBAT' => 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                    default => ($isOverdue ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/20' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/20'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $loan->loan_code }}
                                </td>
                                <td class="px-4 py-3.5 min-w-[200px]">
                                    <div class="font-bold text-slate-900 dark:text-white line-clamp-1">
                                        {{ $loan->book->title ?? 'Buku Dihapus' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $loan->book->book_code ?? '-' }} • Rak: {{ $loan->book->rack_location ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $loan->member->name ?? 'Anggota' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $loan->member->member_class_or_title ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    {{ $loan->loan_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold {{ $isOverdue && $loan->status !== 'KEMBALI' ? 'text-rose-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $loan->due_date->format('d/m/Y') }}
                                    </span>
                                    @if($isOverdue && $loan->status !== 'KEMBALI')
                                        <div class="text-[10px] text-rose-500 font-bold">Telat {{ $daysOverdue }} hari</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-mono">
                                    @if($loan->fine_amount > 0)
                                        <span class="font-bold text-rose-600">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                        <span class="text-[10px] {{ $loan->is_fine_paid ? 'text-emerald-600' : 'text-rose-500' }}">
                                            ({{ $loan->is_fine_paid ? 'Lunas' : 'Belum Bayar' }})
                                        </span>
                                    @else
                                        <span class="text-slate-400">Rp 0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        @if($loan->status === 'MENUNGGU')
                                            <!-- Tombol Setujui & Serahkan -->
                                            <form action="{{ route('library.loans.approve', $loan) }}" method="POST" onsubmit="return confirm('Setujui pengajuan peminjaman dan serahkan buku fisik ke {{ addslashes($loan->member->name ?? 'peminjam') }}?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-xs transition cursor-pointer flex items-center gap-1" title="Setujui dan serahkan buku">
                                                    <span>✓ Setujui</span>
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button" onclick="openRejectModal({{ $loan->id }}, '{{ addslashes($loan->book->title ?? '') }}', '{{ addslashes($loan->member->name ?? '') }}')" class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 text-[11px] font-bold transition cursor-pointer">
                                                ✕ Tolak
                                            </button>
                                        @elseif($loan->status === 'DIPINJAM' || $loan->status === 'TERLAMBAT')
                                            <!-- Tombol Kembalikan Buku -->
                                            <form action="{{ route('library.loans.return', $loan) }}" method="POST" onsubmit="return confirm('Proses pengembalian buku ini?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-xs transition cursor-pointer">
                                                    ✓ Kembalikan
                                                </button>
                                            </form>

                                            <!-- Tombol Perpanjang -->
                                            @if($loan->extension_count < 2)
                                                <form action="{{ route('library.loans.extend', $loan) }}" method="POST" onsubmit="return confirm('Perpanjang masa pinjam buku ini +7 hari?')">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 text-[11px] font-semibold transition cursor-pointer" title="Perpanjang 7 Hari">
                                                        +7 Hari
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif($loan->status === 'DITOLAK')
                                            <span class="text-[10px] text-rose-500 font-semibold" title="{{ $loan->rejection_reason }}">Ditolak: {{ Str::limit($loan->rejection_reason ?? 'Tidak memenuhi syarat', 20) }}</span>
                                        @else
                                            <span class="text-[10px] text-emerald-600 font-semibold">Tuntas ({{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '' }})</span>
                                        @endif

                                        <a href="{{ route('library.loans.print_receipt', $loan) }}" target="_blank"
                                           class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] border border-slate-300 dark:border-slate-700 transition"
                                           title="Cetak Struk Bukti Peminjaman">
                                            🖨️ Struk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Catat Peminjaman Baru -->
<div id="new-loan-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>📖 Catat Transaksi Peminjaman Baru</span>
            </h3>
            <button type="button" onclick="closeNewLoanModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
        </div>

        <form action="{{ route('library.loans.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. Pilih Buku (Dengan Fitur Live Search) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Koleksi Buku <span class="text-rose-500">*</span></label>
                    <span id="book-match-count" class="text-[10px] text-slate-400 font-semibold">{{ count($availableBooks) }} buku tersedia</span>
                </div>
                <div class="relative mb-1.5">
                    <input type="text" id="loan-book-search" oninput="filterBookSelect(this.value)" placeholder="🔍 Ketik judul, kode buku, atau penulis untuk memfilter..." class="w-full px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
                <select name="book_id" id="loan-book-select" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                    <option value="">-- Pilih Buku yang Tersedia --</option>
                    @foreach($availableBooks as $b)
                        <option value="{{ $b->id }}" data-text="{{ strtolower($b->title . ' ' . $b->book_code . ' ' . $b->author . ' ' . ($b->category->name ?? '')) }}">
                            [{{ $b->book_code }}] {{ $b->title }} - {{ $b->author }} (Tersedia: {{ $b->available_copies }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Pilih Anggota Terdaftar (Semua Tipe: Siswa, Guru, Tendik, Umum - Dengan Fitur Live Search) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Anggota Terdaftar (Semua Tipe)</label>
                    <span id="member-match-count" class="text-[10px] text-slate-400 font-semibold">{{ count($activeMembers) }} anggota</span>
                </div>
                <div class="relative mb-1.5">
                    <input type="text" id="loan-member-search" oninput="filterMemberSelect(this.value)" placeholder="🔍 Ketik nama, kode anggota, NISN, NIP, atau kelas/jabatan..." class="w-full px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
                <select name="member_id" id="loan-member-select" onchange="toggleManualMemberInput(this.value)" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                    <option value="">-- Pilih Anggota Terdaftar --</option>
                    @foreach($activeMembers as $m)
                        <option value="{{ $m->id }}" data-text="{{ strtolower($m->name . ' ' . $m->member_code . ' ' . ($m->identity_number ?? '') . ' ' . $m->member_type . ' ' . ($m->member_class_or_title ?? '')) }}">
                            [{{ strtoupper($m->member_type) }}] {{ $m->name }} ({{ $m->member_code }} - {{ $m->identity_number ?: '-' }} - {{ $m->member_class_or_title }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Gunakan kotak cari di atas atau kosongkan pilihan ini jika ingin mengentri peminjam manual di bawah.</p>
            </div>

            <!-- Input Manual Jika Bukan Anggota Terdaftar -->
            <div id="manual-member-box" class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/60 space-y-2.5">
                <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase">Entri Peminjam Manual (Tamu / Pengunjung)</div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <input type="text" name="manual_name" placeholder="Nama Lengkap Peminjam..." class="w-full px-3 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <input type="text" name="manual_identity" placeholder="NISN / NIP / NIK KTP..." class="w-full px-3 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Tanggal & Durasi -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                    <input type="date" name="loan_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Pinjam (Hari) <span class="text-rose-500">*</span></label>
                    <select name="loan_days" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <option value="3">3 Hari</option>
                        <option value="7" selected>7 Hari (Standar Siswa)</option>
                        <option value="14">14 Hari (Standar Guru)</option>
                        <option value="21">21 Hari</option>
                        <option value="30">30 Hari</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                <input type="text" name="notes" placeholder="Keperluan peminjaman / catatan buku..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeNewLoanModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNewLoanModal() {
        document.getElementById('new-loan-modal').classList.remove('hidden');
    }

    function closeNewLoanModal() {
        document.getElementById('new-loan-modal').classList.add('hidden');
    }

    function toggleManualMemberInput(selectedId) {
        const box = document.getElementById('manual-member-box');
        if (selectedId) {
            box.classList.add('opacity-40', 'pointer-events-none');
        } else {
            box.classList.remove('opacity-40', 'pointer-events-none');
        }
    }

    function filterBookSelect(val) {
        const q = val.toLowerCase().trim();
        const select = document.getElementById('loan-book-select');
        let matched = 0;
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (!opt.value) continue;
            const text = (opt.getAttribute('data-text') || opt.text).toLowerCase();
            if (!q || text.includes(q)) {
                opt.style.display = '';
                matched++;
            } else {
                opt.style.display = 'none';
            }
        }
        const countEl = document.getElementById('book-match-count');
        if (countEl) countEl.innerText = q ? (matched + ' buku cocok') : ('{{ count($availableBooks) }} buku tersedia');
    }

    function filterMemberSelect(val) {
        const q = val.toLowerCase().trim();
        const select = document.getElementById('loan-member-select');
        let matched = 0;
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (!opt.value) continue;
            const text = (opt.getAttribute('data-text') || opt.text).toLowerCase();
            if (!q || text.includes(q)) {
                opt.style.display = '';
                matched++;
            } else {
                opt.style.display = 'none';
            }
        }
        const countEl = document.getElementById('member-match-count');
        if (countEl) countEl.innerText = q ? (matched + ' anggota cocok') : ('{{ count($activeMembers) }} anggota');
    }

    function openRejectModal(loanId, bookTitle, memberName) {
        const modal = document.getElementById('reject-loan-modal');
        const form = document.getElementById('reject-loan-form');
        const desc = document.getElementById('reject-loan-desc');

        form.action = `/library/loans/${loanId}/reject`;
        desc.textContent = `Tolak pengajuan pinjam buku "${bookTitle}" oleh ${memberName}.`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-loan-modal').classList.add('hidden');
    }
</script>

<!-- Modal Tolak Pengajuan Pinjam -->
<div id="reject-loan-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-base font-bold text-rose-600 dark:text-rose-400 flex items-center gap-2">
                <span>✕ Tolak Pengajuan Peminjaman</span>
            </h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
        </div>

        <form id="reject-loan-form" method="POST" class="space-y-4">
            @csrf
            <p id="reject-loan-desc" class="text-xs text-slate-600 dark:text-slate-300"></p>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="rejection_reason" required rows="3" placeholder="Contoh: Stok fisik buku sedang dalam perbaikan, atau kuota pinjaman Anda melebihi batas..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

