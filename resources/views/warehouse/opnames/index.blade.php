@extends('layouts.app')

@section('title', 'Stok Opname & Penyelarasan Fisik')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Stok Opname Persediaan Gudang</h1>
            <p class="text-xs text-slate-500 mt-0.5">Audit rekonsiliasi berkala antara catatan sistem dan perhitungan fisik riil beserta Berita Acara.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-opname').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Lakukan Stok Opname
            </button>
        </div>
    </div>

    <!-- Opnames Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal Audit</th>
                        <th class="px-5 py-3.5">No. Berita Acara</th>
                        <th class="px-5 py-3.5">Nama Item Barang</th>
                        <th class="px-5 py-3.5 text-center">Stok Sistem</th>
                        <th class="px-5 py-3.5 text-center">Stok Fisik Riil</th>
                        <th class="px-5 py-3.5 text-center">Selisih</th>
                        <th class="px-5 py-3.5">Petugas Pemeriksa</th>
                        <th class="px-5 py-3.5">Keterangan / Berita Acara</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($opnames as $op)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $op->opname_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                                {{ $op->official_report_number ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $op->good?->name }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">{{ $op->good?->code }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono font-semibold text-slate-800">
                                {{ $op->system_stock }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono font-bold text-blue-700">
                                {{ $op->physical_stock }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono
                                    @if($op->difference == 0) bg-emerald-100 text-emerald-800
                                    @elseif($op->difference > 0) bg-blue-100 text-blue-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $op->difference > 0 ? '+' : '' }}{{ $op->difference }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $op->inspector_name }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-xs">
                                <span class="line-clamp-2">{{ $op->explanation ?? 'Stok fisik sesuai' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat audit stok opname.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($opnames->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $opnames->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Rekonsiliasi Stok Opname -->
<div id="modal-opname" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] my-auto overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Audit Stok Opname Barang</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Penyelarasan stok fisik aktual dengan database inventaris.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-opname').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('warehouse.opnames.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Item Barang yang Diaudit <span class="text-rose-500">*</span></label>
                    <select name="good_id" id="opname-good-select" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl" onchange="updateSystemStock(this)">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($goods as $g)
                            <option value="{{ $g->id }}" data-stock="{{ $g->current_stock }}">{{ $g->name }} (Stok Sistem Saat Ini: {{ $g->current_stock }} {{ $g->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Hasil Hitungan Fisik Riil <span class="text-rose-500">*</span></label>
                        <input type="number" name="physical_stock" value="10" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pemeriksaan <span class="text-rose-500">*</span></label>
                        <input type="date" name="opname_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Petugas Pemeriksa (Auditor) <span class="text-rose-500">*</span></label>
                    <input type="text" name="inspector_name" value="{{ auth()->user()->name }}" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Selisih / Keterangan Berita Acara</label>
                    <textarea name="explanation" rows="2" placeholder="Barang rusak / sobek kemasan / tercecer..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-opname').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan & Selaraskan Stok</button>
            </div>
        </form>
    </div>
</div>
@endpush
