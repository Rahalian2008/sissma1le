@extends('layouts.app')

@section('title', 'Laporan Sirkulasi Peminjaman & Pengembalian Buku')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Laporan & Rekapitulasi Sirkulasi</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>🔄 Laporan Sirkulasi Buku</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Rekapitulasi buku yang dipinjam, dikembalikan, dan terlambat dengan filter kustom periode (harian, mingguan, bulanan, rentang tanggal).
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('library.loans.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5">
                <span>&larr; Meja Sirkulasi</span>
            </a>
            
            <a href="{{ route('library.reports.loans_print', request()->query()) }}" target="_blank" 
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <span>🖨️ Cetak Dokumen Resmi (A4)</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
        <form action="{{ route('library.reports.loans') }}" method="GET" class="space-y-4">
            
            <!-- 1. Pilihan Jenis Periode -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Pilih Jenis Periode Transaksi</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="daily" {{ $period === 'daily' ? 'checked' : '' }} onchange="switchLoanPeriodMode('daily')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📅</div>
                            <div>Harian</div>
                            <div class="text-[10px] text-slate-400 font-normal">Per Tanggal</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="weekly" {{ $period === 'weekly' ? 'checked' : '' }} onchange="switchLoanPeriodMode('weekly')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📆</div>
                            <div>Mingguan</div>
                            <div class="text-[10px] text-slate-400 font-normal">7 Hari / Pekan</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="monthly" {{ $period === 'monthly' ? 'checked' : '' }} onchange="switchLoanPeriodMode('monthly')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📊</div>
                            <div>Bulanan</div>
                            <div class="text-[10px] text-slate-400 font-normal">Per Bulan & Tahun</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="custom" {{ $period === 'custom' ? 'checked' : '' }} onchange="switchLoanPeriodMode('custom')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">⚙️</div>
                            <div>Rentang Bebas</div>
                            <div class="text-[10px] text-slate-400 font-normal">Mulai s/d Selesai</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Kotak Filter Tanggal Sesuai Periode -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                <div id="loan-period-box-daily" class="{{ $period === 'daily' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tanggal Pinjam</label>
                    <input type="date" name="date" value="{{ $date }}" class="w-full sm:w-64 px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                </div>

                <div id="loan-period-box-weekly" class="{{ $period === 'weekly' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tanggal dalam Pekan Tersebut</label>
                    <input type="date" name="week_date" value="{{ $weekDate }}" class="w-full sm:w-64 px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                </div>

                <div id="loan-period-box-monthly" class="{{ $period === 'monthly' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Bulan</label>
                        <select name="month" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tahun</label>
                        <select name="year" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                            @for($y = date('Y') + 1; $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div id="loan-period-box-custom" class="{{ $period === 'custom' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Pinjam</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai Pinjam</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                    </div>
                </div>
            </div>

            <!-- 3. Filter Status Sirkulasi & Pencarian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sirkulasi Buku</label>
                    <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-bold">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status Sirkulasi</option>
                        <option value="DIPINJAM" {{ $status === 'DIPINJAM' ? 'selected' : '' }}>📖 Sedang Dipinjam (Aktif)</option>
                        <option value="KEMBALI" {{ $status === 'KEMBALI' ? 'selected' : '' }}>✅ Sudah Dikembalikan</option>
                        <option value="TERLAMBAT" {{ $status === 'TERLAMBAT' ? 'selected' : '' }}>⚠️ Terlambat (Lewat Jatuh Tempo)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Kode pinjam, judul buku, nama peminjam..." 
                           class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                    Filter Aktif: {{ $periodLabel }} {{ $status !== 'all' ? "• Status: {$status}" : '' }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('library.reports.loans') }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                        🔍 Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan Statistik Sirkulasi -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalLoans) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sirkulasi Tercatat</div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sedang Dipinjam</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ number_format($borrowedLoans) }}</div>
            <div class="text-[10px] text-blue-500 font-semibold mt-0.5">Buku di pemustaka</div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sudah Dikembalikan</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($returnedLoans) }}</div>
            <div class="text-[10px] text-emerald-500 font-semibold mt-0.5">Sirkulasi selesai</div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terlambat</div>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ number_format($overdueLoans) }}</div>
            <div class="text-[10px] text-rose-500 font-semibold mt-0.5">Lewat jatuh tempo</div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs col-span-2 sm:col-span-1">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Denda</div>
            <div class="text-xl font-black text-amber-600 mt-1 font-mono">Rp {{ number_format($totalFines, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Akumulasi Denda</div>
        </div>
    </div>

    <!-- Tabel Data Sirkulasi -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Rincian Transaksi Sirkulasi Buku
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Periode: {{ $periodLabel }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Total: {{ $loans->total() }} Transaksi
            </span>
        </div>

        @if($loans->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <div class="text-4xl mb-2">📭</div>
                <div class="text-sm font-semibold">Tidak ada data sirkulasi pada periode ini.</div>
                <div class="text-xs mt-1 text-slate-500">Coba sesuaikan filter periode atau status sirkulasi.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-slate-500 font-extrabold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5">Kode Transaksi</th>
                            <th class="px-4 py-3.5">Judul Buku</th>
                            <th class="px-4 py-3.5">Peminjam</th>
                            <th class="px-4 py-3.5 text-center">Tgl Pinjam</th>
                            <th class="px-4 py-3.5 text-center">Jatuh Tempo</th>
                            <th class="px-4 py-3.5 text-center">Tgl Kembali</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = $loans->firstItem(); @endphp
                        @foreach($loans as $l)
                            @php
                                $isOverdue = ($l->status === 'TERLAMBAT') || ($l->status === 'DIPINJAM' && $l->due_date < \Carbon\Carbon::today());
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-bold">{{ $no++ }}</td>
                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $l->loan_code }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $l->book->title ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">Kode: {{ $l->book->book_code ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $l->member->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">
                                        <span class="uppercase font-semibold text-[10px] text-indigo-500">[{{ $l->member->member_type ?? '-' }}]</span>
                                        {{ $l->member->member_class_or_title ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-slate-600 dark:text-slate-300">
                                    {{ $l->loan_date ? \Carbon\Carbon::parse($l->loan_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-slate-600 dark:text-slate-300">
                                    {{ $l->due_date ? \Carbon\Carbon::parse($l->due_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-slate-600 dark:text-slate-300">
                                    {{ $l->return_date ? \Carbon\Carbon::parse($l->return_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($l->status === 'KEMBALI')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/15 text-emerald-600 border border-emerald-500/20">
                                            KEMBALI
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/15 text-rose-600 border border-rose-500/20">
                                            TERLAMBAT
                                        </span>
                                    @elseif($l->status === 'DIPINJAM')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-500/15 text-blue-600 border border-blue-500/20">
                                            DIPINJAM
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                            {{ $l->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold {{ $l->fine_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    {{ $l->fine_amount > 0 ? 'Rp ' . number_format($l->fine_amount, 0, ',', '.') : '-' }}
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

<script>
    function switchLoanPeriodMode(mode) {
        ['daily', 'weekly', 'monthly', 'custom'].forEach(m => {
            const el = document.getElementById(`loan-period-box-${m}`);
            if (el) {
                if (m === mode) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });
    }
</script>
@endsection

