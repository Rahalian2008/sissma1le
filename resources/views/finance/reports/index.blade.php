@extends('layouts.app')

@section('title', 'Laporan Realisasi Anggaran Keuangan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Laporan Realisasi Anggaran Sekolah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Analisis capaian penyerapan anggaran BOSP dan pertanggungjawaban belanja tahun {{ $year }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.reconciliation.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Rekonsiliasi & Penutupan Kas
            </a>
            <a href="{{ route('finance.reports.print', ['year' => $year]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Laporan Realisasi
            </a>
        </div>
    </div>

    <!-- Year Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('finance.reports.index') }}" method="GET" class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 font-semibold">Tahun Anggaran:</span>
            <select name="year" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach(range(date('Y')-2, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Pagu Ditetapkan</span>
            <span class="text-xl font-black text-slate-900 font-mono mt-1 block">Rp {{ number_format($totalAllocated, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Realisasi Belanja (SPJ)</span>
            <span class="text-xl font-black text-blue-600 font-mono mt-1 block">Rp {{ number_format($totalRealized, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Sisa Alokasi Pagu</span>
            <span class="text-xl font-black text-emerald-700 font-mono mt-1 block">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Persentase Penyerapan</span>
            @php $percentage = $totalAllocated > 0 ? round(($totalRealized / $totalAllocated) * 100, 1) : 0; @endphp
            <span class="text-xl font-black text-indigo-700 font-mono mt-1 block">{{ $percentage }}%</span>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Realization Breakdown Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
        <h2 class="text-sm font-bold text-slate-900">Rincian Realisasi per Sumber Dana & Sub Kegiatan</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Sumber Dana / Kegiatan</th>
                        <th class="px-4 py-3 text-right">Pagu Anggaran</th>
                        <th class="px-4 py-3 text-right">Realisasi Belanja</th>
                        <th class="px-4 py-3 text-right">Sisa Anggaran</th>
                        <th class="px-4 py-3 text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($budgets as $b)
                        <tr class="bg-slate-50/60 font-bold text-slate-900">
                            <td class="px-4 py-3">
                                [{{ $b->funding_source }}] - Total Pagu Sumber
                            </td>
                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($b->total_allocation, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-blue-600">Rp {{ number_format($b->items->sum('realized_amount'), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-700">Rp {{ number_format($b->total_allocation - $b->items->sum('realized_amount'), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center font-mono">
                                {{ $b->total_allocation > 0 ? round(($b->items->sum('realized_amount') / $b->total_allocation) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @foreach($b->items as $item)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-4 py-2.5 pl-8 text-slate-700">
                                    &bull; {{ $item->activity_name }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono text-slate-700">Rp {{ number_format($item->allocated_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-slate-700">Rp {{ number_format($item->realized_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-slate-700">Rp {{ number_format($item->allocated_amount - $item->realized_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-center font-mono text-slate-500">
                                    {{ $item->allocated_amount > 0 ? round(($item->realized_amount / $item->allocated_amount) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data anggaran di tahun {{ $year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
