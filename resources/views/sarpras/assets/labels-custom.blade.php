@extends('layouts.app')

@section('title', 'Kustomisasi Cetak Label Stiker Inventaris')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sarpras.assets.index') }}" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-slate-900">Kustomisasi Cetak Label Stiker Inventaris</h1>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Cetak stiker barcode / QR code barang secara fleksibel: per-barang terpilih, per-ruangan (KIR), atau satu klasifikasi KIB dalam satu dokumen cetak.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="submitPrintForm()" id="btn-print-selected" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span id="btn-print-text">Cetak Label Terpilih (0)</span>
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('sarpras.assets.labels.custom') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">Cari Barang / Kode / Merk</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama aset, kode SIPD e-BMD, nomor seri..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Ruangan (KIR)</label>
                <select name="room_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->assets_count ?? $r->assets()->count() }} item)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Klasifikasi KIB</label>
                <select name="kib_type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">-- Semua KIB (A - F) --</option>
                    @foreach($kibCategories as $kKey => $kVal)
                        <option value="{{ $kKey }}" {{ request('kib_type') === $kKey ? 'selected' : '' }}>KIB {{ $kKey }}: {{ $kVal['title'] }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('room_id'))
            @php $currentRoom = $rooms->firstWhere('id', request('room_id')); @endphp
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-600">Filter aktif untuk ruangan: <strong class="text-slate-900">{{ $currentRoom?->name }}</strong></span>
                <a href="{{ route('sarpras.assets.labels.print', ['room_id' => request('room_id')]) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-2xs transition">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Semua Label di Ruangan Ini ({{ $assets->count() }} Label)
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Selection & Layout Bar -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" id="check-all" onchange="toggleSelectAll(this)" class="w-4 h-4 text-indigo-600 rounded border-slate-300">
                <span>Pilih Semua Barang yang Ditampilkan ({{ $assets->count() }} Item)</span>
            </label>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-slate-600 font-medium">Format Tata Letak:</span>
            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="layout_option" value="grid_2col" checked class="text-indigo-600">
                <span class="text-slate-700">2 Kolom (Standar A4)</span>
            </label>
            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="layout_option" value="grid_3col" class="text-indigo-600">
                <span class="text-slate-700">3 Kolom (Compact)</span>
            </label>
        </div>
    </div>

    <!-- Assets Selection Table -->
    <form id="print-form" action="{{ route('sarpras.assets.labels.print') }}" method="GET" target="_blank">
        <input type="hidden" name="layout" id="form-layout" value="grid_2col">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 w-10 text-center">Pilih</th>
                            <th class="px-4 py-3.5">Kode Barang SIPD e-BMD</th>
                            <th class="px-4 py-3.5">No. Register</th>
                            <th class="px-4 py-3.5">Nama Barang & Merek</th>
                            <th class="px-4 py-3.5">KIB</th>
                            <th class="px-4 py-3.5">Lokasi Ruang (KIR)</th>
                            <th class="px-4 py-3.5">Tahun</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($assets as $ast)
                            <tr class="hover:bg-slate-50/80 transition cursor-pointer" onclick="toggleRowCheckbox(event, {{ $ast->id }})">
                                <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" name="asset_ids[]" value="{{ $ast->id }}" onchange="updateSelectedCount()" class="asset-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300">
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-indigo-700">
                                    {{ $ast->sim_bmd_code ?? $ast->code }}
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-800">
                                    {{ $ast->register_number ?? $ast->code }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $ast->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $ast->brand_model ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                        KIB {{ $ast->kib_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    📍 {{ $ast->room?->name ?? 'Belum Ditentukan' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">
                                    {{ $ast->procurement_year }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                    <a href="{{ route('sarpras.assets.label', $ast) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition" title="Pratinjau Stiker Tunggal">
                                        Stiker
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                    Tidak ada data barang yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = master.checked;
    });
    updateSelectedCount();
}

function toggleRowCheckbox(event, id) {
    const cb = document.querySelector(`.asset-checkbox[value="${id}"]`);
    if (cb) {
        cb.checked = !cb.checked;
        updateSelectedCount();
    }
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.asset-checkbox:checked');
    const count = checked.length;
    const btn = document.getElementById('btn-print-selected');
    const text = document.getElementById('btn-print-text');
    
    text.innerText = `Cetak Label Terpilih (${count})`;
    btn.disabled = count === 0;

    const all = document.querySelectorAll('.asset-checkbox');
    const master = document.getElementById('check-all');
    if (master && all.length > 0) {
        master.checked = (checked.length === all.length);
    }
}

function submitPrintForm() {
    const checked = document.querySelectorAll('.asset-checkbox:checked');
    if (checked.length === 0) {
        alert('Silakan pilih minimal satu barang untuk dicetak labelnya.');
        return;
    }

    const layoutRadio = document.querySelector('input[name="layout_option"]:checked');
    document.getElementById('form-layout').value = layoutRadio ? layoutRadio.value : 'grid_2col';

    document.getElementById('print-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endsection
