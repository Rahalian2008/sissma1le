@extends('layouts.app')

@section('title', 'Edit Aset Inventaris - ' . $asset->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sarpras.assets.show', $asset) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit Aset Inventaris</h1>
                <p class="text-xs text-slate-500 font-mono">Kode Barang SIPD e-BMD: {{ $asset->sim_bmd_code ?? $asset->code }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('sarpras.assets.update', $asset) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Barang / Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="field-edit-name" value="{{ old('name', $asset->name) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-semibold text-slate-700">Kodefikasi Barang SIPD e-BMD</label>
                        <button type="button" onclick="openSimBmdModal()" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span>Cari Kode SIPD e-BMD</span>
                        </button>
                    </div>
                    <input type="text" name="sim_bmd_code" id="field-edit-sim-bmd" value="{{ old('sim_bmd_code', $asset->sim_bmd_code) }}" placeholder="1.3.2.10.01.02.002" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">💡 Jika tidak ditemukan di katalog pencarian SIPD e-BMD, Anda dapat mengetikkan Kode Barang dan Nama Aset secara manual.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Klasifikasi KIB <span class="text-rose-500">*</span></label>
                    <select name="kib_type" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($kibCategories as $kKey => $kVal)
                            <option value="{{ $kKey }}" {{ old('kib_type', $asset->kib_type ?? 'B') === $kKey ? 'selected' : '' }}>
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
                            <option value="{{ $r->id }}" {{ old('room_id', $asset->room_id) == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->code }}) @if($r->person_in_charge)— PJ: {{ $r->person_in_charge }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kondisi Aset <span class="text-rose-500">*</span></label>
                    <select name="condition" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($conditions as $cond)
                            <option value="{{ $cond->name }}" {{ old('condition', $asset->condition) === $cond->name ? 'selected' : '' }}>
                                {{ $cond->name }} ({{ $cond->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Merek & Tipe</label>
                    <input type="text" name="brand_model" value="{{ old('brand_model', $asset->brand_model) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Seri / Pabrik</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Harga Perolehan (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', (int)$asset->price) }}" min="0" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Pendanaan</label>
                    <select name="funding_source" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($fundingSources as $fs)
                            <option value="{{ $fs->name }}" {{ old('funding_source', $asset->funding_source) === $fs->name ? 'selected' : '' }}>
                                {{ $fs->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Rincian Spesifikasi / Ukuran / Bahan</label>
                <textarea name="specification" rows="2" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('specification', $asset->specification) }}</textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <input type="text" name="notes" value="{{ old('notes', $asset->notes) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('sarpras.assets.show', $asset) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<x-sim-bmd-search-modal 
    targetNameId="field-edit-name" 
    targetCodeId="field-edit-sim-bmd" 
    targetKibId="field-edit-kib" 
    targetUnitId="field-edit-unit" 
/>
@endsection
