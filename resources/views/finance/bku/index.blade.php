@extends('layouts.app')

@section('title', 'Buku Kas Umum (BKU) Digital')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buku Kas Umum (BKU) Digital</h1>
            <p class="text-xs text-slate-500 mt-0.5">Buku jurnal kas umum sekolah dengan kalkulasi saldo kas berjalan otomatis sesuai standar pelaporan BOSP.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.bku.export-csv', request()->all()) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor CSV
            </a>
            <a href="{{ route('finance.bku.print', request()->all()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak BKU Resmi
            </a>
        </div>
    </div>

    <!-- Filter Month / Year / Source -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('finance.bku.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <select name="month" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                    @foreach(range(1, 12) as $m)
                        @php $mStr = str_pad((string)$m, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $mStr }}" {{ (int)$month === $m ? 'selected' : '' }}>
                            Bulan {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="year" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="source" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="all">Semua Sumber Dana</option>
                    <option value="BOSP Reguler" {{ $source === 'BOSP Reguler' ? 'selected' : '' }}>BOSP Reguler</option>
                    <option value="BOSP Kinerja" {{ $source === 'BOSP Kinerja' ? 'selected' : '' }}>BOSP Kinerja</option>
                    <option value="BOPD Pemprov" {{ $source === 'BOPD Pemprov' ? 'selected' : '' }}>BOPD Pemprov</option>
                    <option value="Komite Sekolah" {{ $source === 'Komite Sekolah' ? 'selected' : '' }}>Komite Sekolah</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Penerimaan (Bulan Ini)</span>
            <span class="text-xl font-black text-emerald-700 font-mono mt-1 block">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Pengeluaran (Bulan Ini)</span>
            <span class="text-xl font-black text-rose-700 font-mono mt-1 block">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Sisa Saldo Kas Bersih</span>
            <span class="text-xl font-black text-blue-700 font-mono mt-1 block">Rp {{ number_format($netBalance, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- BKU Ledger Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No.</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">No. Bukti Kas</th>
                        <th class="px-5 py-3.5">Uraian Transaksi</th>
                        <th class="px-4 py-3.5 text-right">Penerimaan (Debit)</th>
                        <th class="px-4 py-3.5 text-right">Pengeluaran (Kredit)</th>
                        <th class="px-5 py-3.5 text-right">Saldo Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $runningBalance = 0; @endphp
                    @forelse ($transactions as $index => $t)
                        @php $runningBalance += ($t->debit - $t->credit); @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 text-center font-mono text-slate-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 font-mono">
                                {{ $t->transaction_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-900">
                                {{ $t->evidence_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $t->description }}</div>
                                <div class="text-[11px] text-slate-500">Pihak: {{ $t->recipient_or_payer }} &bull; {{ $t->payment_method }} ({{ $t->funding_source }})</div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-semibold text-emerald-700">
                                {{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-semibold text-rose-700">
                                {{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($runningBalance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada transaksi kas pada periode bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
