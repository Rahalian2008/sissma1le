@extends('layouts.app')

@section('title', 'Adm. Keuangan Sekolah (BOSP) - Tata Usaha')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-600 via-pink-600 to-red-700 p-6 sm:p-8 text-white shadow-xl shadow-rose-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-rose-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Sub Menu Administrasi Ketatausahaan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Administrasi Keuangan Sekolah</h1>
                <p class="text-rose-100 text-sm mt-1 max-w-xl">
                    Pengelolaan Rencana Kegiatan dan Anggaran Sekolah (RKAS), Buku Kas Umum (BKU), Buku Pembantu Kas Tunai & Bank, administrasi kuitansi tanda terima, dan pelaporan pajak BOSP.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('finance.bku.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-rose-700 font-bold text-xs shadow-lg shadow-black/10 hover:bg-rose-50 transition-all">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Buku Kas Umum (BKU)</span>
                </a>
                <a href="{{ route('finance.transactions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Transaksi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik Keuangan Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pagu Anggaran RKAS</span>
            <div class="text-xl font-black text-slate-800 dark:text-white mt-1 truncate">Rp {{ number_format($totalBudgetAllocated, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Tahun Anggaran Berjalan</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Realisasi Belanja</span>
            <div class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1 truncate">Rp {{ number_format($totalBudgetRealized, 0, ',', '.') }}</div>
            <div class="text-[11px] text-rose-500 mt-0.5 font-medium">Serapan: {{ $absorptionPct }}%</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Saldo BKU</span>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1 truncate">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-500 mt-0.5 font-medium">Sisa Kas Kumulatif</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kas Tunai di Brankas</span>
            <div class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1 truncate">Rp {{ number_format($cashOnHand, 0, ',', '.') }}</div>
            <div class="text-[11px] text-blue-500 mt-0.5 font-medium">Buku Pembantu Kas</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Saldo Rekening Bank</span>
            <div class="text-xl font-black text-teal-600 dark:text-teal-400 mt-1 truncate">Rp {{ number_format($bankBalance, 0, ',', '.') }}</div>
            <div class="text-[11px] text-teal-500 mt-0.5 font-medium">Buku Pembantu Bank</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pajak Belum Disetor</span>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1 truncate">Rp {{ number_format($unpaidTaxesAmount, 0, ',', '.') }}</div>
            <div class="text-[11px] text-amber-500 mt-0.5 font-medium">{{ $unpaidTaxesCount }} transaksi pajak</div>
        </div>
    </div>

    <!-- Quick Access Feature Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Modul Layanan Administrasi Keuangan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('finance.budgets.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-rose-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">RKAS Anggaran</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Pagu & Belanja</span>
            </a>
            <a href="{{ route('finance.bku.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-pink-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-pink-500/10 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku BKU</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Buku Kas Umum</span>
            </a>
            <a href="{{ route('finance.subsidiary.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kas & Bank</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Buku Pembantu</span>
            </a>
            <a href="{{ route('finance.transactions.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Transaksi & Kuitansi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">SPJ Bukti Riil</span>
            </a>
            <a href="{{ route('finance.taxes.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku Pajak</span>
                <span class="text-[10px] text-slate-400 mt-0.5">PPN & PPh BOSP</span>
            </a>
            <a href="{{ route('finance.reports.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Laporan Keuangan</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Rekapitulasi Kas</span>
            </a>
        </div>
    </div>

    <!-- Tabel Transaksi Kas Terkini -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white">Transaksi Pembukuan Kas Terkini</h3>
                <p class="text-xs text-slate-400 mt-0.5">Buku catatan transaksi penerimaan dan pengeluaran kas sekolah</p>
            </div>
            <a href="{{ route('finance.transactions.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">Kelola Semua Transaksi &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nomor Bukti / Kuitansi</th>
                        <th class="py-3 px-4">Uraian Transaksi</th>
                        <th class="py-3 px-4">Metode Kas</th>
                        <th class="py-3 px-4 text-right">Penerimaan (Debit)</th>
                        <th class="py-3 px-4 text-right">Pengeluaran (Kredit)</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($recentTransactions as $t)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($t->transaction_date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white text-[11px]">
                            {{ $t->proof_number ?: '-' }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-white max-w-xs truncate">
                            {{ $t->description }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t->payment_method === 'Bank' ? 'bg-teal-500/10 text-teal-600 border border-teal-500/20' : 'bg-blue-500/10 text-blue-600 border border-blue-500/20' }}">
                                {{ $t->payment_method ?: 'Kas Tunai' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                            {{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if(Route::has('finance.transactions.receipt.print'))
                                <a href="{{ route('finance.transactions.receipt.print', $t) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold hover:bg-rose-100 transition text-[11px]">
                                    Kuitansi
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada transaksi kas yang dicatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

