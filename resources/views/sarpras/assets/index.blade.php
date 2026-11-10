@extends('layouts.app')

@section('title', 'Inventaris Sarana & Prasarana (KIB - KIR - SIPD e-BMD)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Inventaris Sarana & Prasarana</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan Barang Milik Daerah (BMD) sesuai standar SIPD e-BMD (Permendagri No. 108/2016), Kartu Inventaris Barang (KIB A-F), dan penempatan KIR.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('sarpras.assets.labels.custom') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition shadow-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Custom Cetak Label
            </a>
            <a href="{{ route('sarpras.kir.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition shadow-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Kartu Inventaris Ruangan (KIR)
            </a>
            <a href="{{ route('sarpras.kib.print', ['kib_type' => request('kib_type', 'B')]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs rounded-xl border border-purple-200 transition shadow-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen KIB {{ request('kib_type', 'B') }}
            </a>
            <x-import-modal type="assets" title="Aset & Sarpras" />
            <a href="{{ route('sarpras.assets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Aset
            </a>
        </div>
    </div>

    <!-- KIB Segmented Navigation Tabs -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('sarpras.assets.index', array_merge(request()->except('kib_type', 'page'))) }}" 
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ !request('kib_type') ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua Aset
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ !request('kib_type') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $kibCounts['all'] }}</span>
        </a>
        @foreach($kibCategories as $kKey => $kVal)
            <a href="{{ route('sarpras.assets.index', array_merge(request()->except('page'), ['kib_type' => $kKey])) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('kib_type') === $kKey ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                {{ $kVal['name'] }}: {{ $kVal['title'] }}
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('kib_type') === $kKey ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $kibCounts[$kKey] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('sarpras.assets.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            @if(request('kib_type'))
                <input type="hidden" name="kib_type" value="{{ request('kib_type') }}">
            @endif
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang, kode SIPD e-BMD, register, atau merek..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="room_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Lokasi Ruang (KIR)</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="condition" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Kondisi</option>
                    @foreach($conditions as $cond)
                        <option value="{{ $cond }}" {{ request('condition') === $cond ? 'selected' : '' }}>{{ $cond }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Bulk Action Toolbar (appears when items are checked) -->
    <div id="bulk-toolbar" class="hidden bg-slate-900 text-white rounded-2xl px-4 py-3 flex items-center justify-between text-xs shadow-lg transition">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span id="bulk-selected-count" class="font-bold">0 item dipilih</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="printBulkLabels()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Label Terpilih dalam 1 Dokumen
            </button>
            <button type="button" onclick="deselectAll()" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg">Batal</button>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-3.5 w-10 text-center">
                            <input type="checkbox" id="check-all-assets" onchange="toggleSelectAllIndex(this)" class="w-4 h-4 text-indigo-600 rounded border-slate-300 cursor-pointer">
                        </th>
                        <th class="px-4 py-3.5">Kode Barang SIPD e-BMD / Label</th>
                        <th class="px-4 py-3.5">Nama Barang & Spesifikasi</th>
                        <th class="px-4 py-3.5">Klasifikasi KIB</th>
                        <th class="px-4 py-3.5">Lokasi Ruang (KIR)</th>
                        <th class="px-4 py-3.5">Tahun / Sumber</th>
                        <th class="px-4 py-3.5 text-right">Nilai Perolehan</th>
                        <th class="px-4 py-3.5">Kondisi</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($assets as $ast)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-3 py-3.5 text-center">
                                <input type="checkbox" value="{{ implode(',', $ast->asset_ids ?? [$ast->id]) }}" onchange="updateIndexBulkCount()" class="row-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 cursor-pointer">
                            </td>
                            <td class="px-4 py-3.5 font-mono">
                                <div class="font-bold text-indigo-700">{{ $ast->sim_bmd_code ?? $ast->code }}</div>
                                <div class="text-[10px] text-slate-500">Reg: <span class="font-bold text-slate-800">{{ $ast->register_range ?? $ast->register_number }}</span></div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-900">{{ $ast->name }}</span>
                                    @if(($ast->quantity ?? 1) > 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                            Jumlah: {{ $ast->quantity }} {{ $ast->unit ?? 'Unit' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $ast->brand_model ?? '-' }}
                                    @if($ast->serial_number) &bull; <span class="font-mono">SN: {{ $ast->serial_number }}</span> @endif
                                </div>
                                @if($ast->specification)
                                    <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">{{ $ast->specification }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    KIB {{ $ast->kib_type ?? 'B' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-800">
                                @if($ast->room)
                                    <a href="{{ route('sarpras.kir.index', ['room_id' => $ast->room_id]) }}" class="text-blue-600 hover:underline">
                                        📍 {{ $ast->room->name }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Belum Ditempatkan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $ast->procurement_year }}</div>
                                <div class="text-[10px] text-slate-400">{{ $ast->funding_source }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($ast->price, 0, ',', '.') }}
                                @if(($ast->quantity ?? 1) > 1)
                                    <div class="text-[10px] text-slate-400 font-normal">Total: Rp {{ number_format($ast->total_price, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($ast->condition === 'Baik') bg-emerald-100 text-emerald-800
                                    @elseif($ast->condition === 'Rusak Ringan') bg-blue-100 text-blue-800
                                    @elseif($ast->condition === 'Rusak Sedang') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $ast->condition }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('sarpras.assets.show', $ast->id) }}" class="inline-flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition" title="Lihat Detail">
                                    Lihat
                                </a>
                                @if(!empty($ast->asset_ids) && count($ast->asset_ids) > 1)
                                    <a href="{{ route('sarpras.assets.labels.print', ['asset_ids' => implode(',', $ast->asset_ids)]) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition" title="Cetak Semua Label Unit ({{ count($ast->asset_ids) }} Stiker)">
                                        Label ({{ count($ast->asset_ids) }})
                                    </a>
                                @else
                                    <a href="{{ route('sarpras.assets.label', $ast->id) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition" title="Cetak Label QR">
                                        Label QR
                                    </a>
                                @endif
                                <a href="{{ route('sarpras.assets.edit', $ast->id) }}" class="inline-flex items-center px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg transition" title="Edit Aset">
                                    Edit
                                </a>
                                <form action="{{ route('sarpras.assets.destroy', $ast->id) }}" method="POST" onsubmit="return confirm('Hapus data aset inventaris ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg transition cursor-pointer" title="Hapus Aset">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-slate-400">
                                Belum ada aset inventaris yang sesuai kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($assets->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleSelectAllIndex(master) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = master.checked;
    });
    updateIndexBulkCount();
}

function updateIndexBulkCount() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const toolbar = document.getElementById('bulk-toolbar');
    const countSpan = document.getElementById('bulk-selected-count');

    let totalUnits = 0;
    checked.forEach(cb => {
        const ids = cb.value.split(',').filter(Boolean);
        totalUnits += ids.length;
    });

    if (checked.length > 0) {
        toolbar.classList.remove('hidden');
        countSpan.innerText = `${checked.length} kelompok baris (${totalUnits} unit aset) dipilih`;
    } else {
        toolbar.classList.add('hidden');
    }

    const all = document.querySelectorAll('.row-checkbox');
    const master = document.getElementById('check-all-assets');
    if (master && all.length > 0) {
        master.checked = (checked.length === all.length);
    }
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => { cb.checked = false; });
    const master = document.getElementById('check-all-assets');
    if (master) master.checked = false;
    updateIndexBulkCount();
}

function printBulkLabels() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) return;

    let allIds = [];
    checked.forEach(cb => {
        const parts = cb.value.split(',').filter(Boolean);
        allIds = allIds.concat(parts);
    });

    const uniqueIds = [...new Set(allIds)];
    const url = '{{ route('sarpras.assets.labels.print') }}?asset_ids=' + uniqueIds.join(',');
    window.open(url, '_blank');
}
</script>
@endsection
