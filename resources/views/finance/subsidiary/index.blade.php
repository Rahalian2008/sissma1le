@extends('layouts.app')

@section('title', 'Buku Pembantu Kas & Bank')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Pelaporan BOSP & BOPD</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Permendikbudristek No. 63/2023</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Pembantu Kas & Bank</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penatausahaan pembukuan pembantu kas tunai brankas dan rekening koran bank sekolah untuk rekonsiliasi BKU.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.bku.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z"></path></svg>
                Kembali ke BKU
            </a>
            <a href="{{ route('finance.subsidiary.print', array_merge(request()->all(), ['type' => $tab])) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Pembantu
            </a>
        </div>
    </div>

    <!-- Tabs & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Tabs Switcher -->
        <div class="flex items-center p-1 bg-slate-100 rounded-xl">
            <a href="{{ route('finance.subsidiary.index', array_merge(request()->all(), ['tab' => 'bank'])) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $tab === 'bank' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ $tab === 'bank' ? 'text-blue-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                Buku Pembantu Bank
                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $tab === 'bank' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700' }}">
                    Rp {{ number_format($bankBalance, 0, ',', '.') }}
                </span>
            </a>
            <a href="{{ route('finance.subsidiary.index', array_merge(request()->all(), ['tab' => 'cash'])) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $tab === 'cash' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ $tab === 'cash' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Buku Pembantu Kas (Tunai)
                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $tab === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                    Rp {{ number_format($cashBalance, 0, ',', '.') }}
                </span>
            </a>
        </div>

        <!-- Filter Month / Year / Source -->
        <form action="{{ route('finance.subsidiary.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="month" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach(range(1, 12) as $m)
                    @php $mStr = str_pad((string)$m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mStr }}" {{ (int)$month === $m ? 'selected' : '' }}>
                        Bulan {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach(range(date('Y')-2, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
            <select name="source" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="all">Semua Sumber Dana</option>
                <option value="BOSP Reguler" {{ $source === 'BOSP Reguler' ? 'selected' : '' }}>BOSP Reguler</option>
                <option value="BOSP Kinerja" {{ $source === 'BOSP Kinerja' ? 'selected' : '' }}>BOSP Kinerja</option>
                <option value="BOPD Pemprov" {{ $source === 'BOPD Pemprov' ? 'selected' : '' }}>BOPD Pemprov</option>
                <option value="Komite Sekolah" {{ $source === 'Komite Sekolah' ? 'selected' : '' }}>Komite Sekolah</option>
            </select>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Penerimaan ({{ $tab === 'cash' ? 'Tunai' : 'Bank' }})</span>
            <span class="text-xl font-black text-emerald-700 font-mono mt-1 block">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Pengeluaran ({{ $tab === 'cash' ? 'Tunai' : 'Bank' }})</span>
            <span class="text-xl font-black text-rose-700 font-mono mt-1 block">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Saldo Akhir {{ $tab === 'cash' ? 'Kas Tunai' : 'Kas Bank' }}</span>
            <span class="text-xl font-black font-mono mt-1 block {{ $netBalance >= 0 ? 'text-blue-700' : 'text-rose-700' }}">Rp {{ number_format($netBalance, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Ledger Table -->
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
                        <th class="px-5 py-3.5 text-right">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $running = 0; @endphp
                    @forelse ($transactions as $index => $t)
                        @php $running += ($t->debit - $t->credit); @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 font-mono text-slate-600">{{ $t->transaction_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-900">{{ $t->evidence_number }}</td>
                            <td class="px-5 py-3.5 max-w-sm">
                                <div class="font-bold text-slate-900">{{ $t->description }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Kepada/Dari: {{ $t->recipient_or_payer }} &bull; {{ $t->funding_source }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                {{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-700">
                                {{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($running, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                Tidak ada transaksi {{ $tab === 'cash' ? 'kas tunai' : 'bank' }} pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($transactions->isNotEmpty())
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                    <tr>
                        <td colspan="4" class="px-5 py-3 text-right text-slate-600 uppercase">Jumlah Total Transaksi:</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-700">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-rose-700">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-mono text-blue-700">Rp {{ number_format($netBalance, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
