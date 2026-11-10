@extends('layouts.app')

@section('title', 'Tambah Aset Inventaris (KIB - SIPD e-BMD)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sarpras.assets.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Tambah Aset Inventaris</h1>
                <p class="text-xs text-slate-500">Pencatatan aset dengan Katalog Kodefikasi Barang SIPD e-BMD (Permendagri No. 108/2016) dan penempatan KIR ruangan.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800">
            <p class="font-bold mb-1">Terdapat kesalahan formulir:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('sarpras.assets.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- Preset SIPD e-BMD Selector -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="font-semibold text-slate-700 flex items-center gap-1.5">
                        <span>Katalog Kodefikasi Barang SIPD e-BMD</span>
                        <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold border border-emerald-200">Permendagri 108/2016</span>
                    </label>
                    <button type="button" onclick="openSimBmdModal()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg text-[11px] font-bold shadow-2xs hover:shadow transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Kode SIPD e-BMD</span>
                    </button>
                </div>
                <select id="preset-sim-bmd" onchange="applySimBmdPreset(this.value)" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white font-medium">
                    <option value="">-- Pilih dari Katalog Kodefikasi Barang SIPD e-BMD (Atau isi manual) --</option>
                    @foreach(($sipdBmdCodes ?? $simBmdCodes) as $code => $info)
                        <option value="{{ $code }}" data-name="{{ $info['name'] }}" data-kib="{{ $info['kib'] }}" data-unit="{{ $info['unit'] }}">
                            [{{ $code }}] {{ $info['name'] }} (KIB-{{ $info['kib'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Barang / Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="field-name" value="{{ old('name') }}" placeholder="Laptop Asus ExpertBook Core i5" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kodefikasi Barang SIPD e-BMD <span class="text-slate-400 font-normal">(Permendagri No. 108/2016)</span></label>
                    <input type="text" name="sim_bmd_code" id="field-sim-bmd-code" value="{{ old('sim_bmd_code', '1.3.2.10.01.02.002') }}" placeholder="1.3.2.10.01.02.002" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">💡 Jika tidak ditemukan di katalog pencarian SIPD e-BMD, Anda dapat mengetikkan Kode Barang dan Nama Aset secara manual.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Klasifikasi KIB <span class="text-rose-500">*</span></label>
                    <select name="kib_type" id="field-kib-type" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($kibCategories as $kKey => $kVal)
                            <option value="{{ $kKey }}" {{ old('kib_type', 'B') === $kKey ? 'selected' : '' }}>
                                {{ $kVal['name'] }} - {{ $kVal['title'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700">Penempatan Ruangan (KIR)</label>
                        <span class="text-[10px] text-slate-400 font-medium">Boleh Transit</span>
                    </div>
                    <select name="room_id" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Belum Ditempatkan (Gudang Transit / Nanti Dialokasikan) --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}" {{ old('room_id') == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->code }}) @if($r->person_in_charge)— PJ: {{ $r->person_in_charge }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kondisi Aset <span class="text-rose-500">*</span></label>
                    <select name="condition" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($conditions as $cond)
                            <option value="{{ $cond->name }}" {{ old('condition', 'Baik') === $cond->name ? 'selected' : '' }}>
                                {{ $cond->name }} ({{ $cond->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Merek & Tipe</label>
                    <input type="text" name="brand_model" value="{{ old('brand_model') }}" placeholder="Asus B1400CEAE" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Seri / Pabrik</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="SN: 8947238914" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Satuan</label>
                    <input type="text" name="unit" id="field-unit" list="unit-list" value="{{ old('unit', 'Unit') }}" placeholder="Pilih / ketik satuan..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <datalist id="unit-list">
                        @foreach($units as $u)
                            <option value="{{ $u->name }}">{{ $u->code }} - {{ $u->name }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Perolehan <span class="text-rose-500">*</span></label>
                    <input type="number" name="procurement_year" value="{{ old('procurement_year', date('Y')) }}" min="2000" max="{{ date('Y')+1 }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Harga Perolehan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 12500000) }}" min="0" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Pendanaan <span class="text-rose-500">*</span></label>
                    <select name="funding_source" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($fundingSources as $fs)
                            <option value="{{ $fs->name }}" {{ old('funding_source', 'BOSP Reguler') === $fs->name ? 'selected' : '' }}>
                                {{ $fs->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Rincian Spesifikasi Teknis / Ukuran / Bahan</label>
                <textarea name="specification" rows="2" placeholder="Processor Intel Core i5, RAM 16GB, SSD 512GB, Display 14 inch Full HD..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('specification') }}</textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Pengadaan Semester Genap TP 2025/2026" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('sarpras.assets.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Simpan & Daftarkan KIB-KIR
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function applySimBmdPreset(code) {
    if (!code) return;
    const select = document.getElementById('preset-sim-bmd');
    const option = select.options[select.selectedIndex];
    if (!option) return;

    const name = option.getAttribute('data-name');
    const kib = option.getAttribute('data-kib');
    const unit = option.getAttribute('data-unit');

    document.getElementById('field-name').value = name || '';
    document.getElementById('field-sim-bmd-code').value = code || '';
    if (kib) {
        document.getElementById('field-kib-type').value = kib;
    }
    if (unit) {
        document.getElementById('field-unit').value = unit;
    }
}
</script>

<x-sim-bmd-search-modal 
    targetNameId="field-name" 
    targetCodeId="field-sim-bmd-code" 
    targetKibId="field-kib-type" 
    targetUnitId="field-unit" 
    presetSelectId="preset-sim-bmd" 
/>
@endsection
