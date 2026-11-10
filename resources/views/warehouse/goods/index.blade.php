@extends('layouts.app')

@section('title', 'Katalog Persediaan Barang Gudang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Persediaan Barang Gudang</h1>
            <p class="text-xs text-slate-500 mt-0.5">Inventarisasi ATK, alat kebersihan, sarana lab habis pakai, dan peringatan batas minimum stok (Reorder Point).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warehouse.receipts.index') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-xl border border-emerald-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                Barang Masuk
            </a>
            <a href="{{ route('warehouse.issuances.index') }}" class="inline-flex items-center px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-xl border border-rose-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                Barang Keluar
            </a>
            <button type="button" onclick="openConsumableModal()" class="inline-flex items-center px-3.5 py-2 bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold text-xs rounded-xl border border-teal-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Katalog Kodefikasi BHP
            </button>
            <x-import-modal type="goods" title="Barang Persediaan" />
            <button onclick="openCreateGoodModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Item Barang
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('warehouse.goods.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau kode item (cth: HVS, Spidol, 1.1.7)..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center">
                <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-slate-700">
                    <input type="checkbox" name="low_stock" value="1" onchange="this.form.submit()" {{ request('low_stock') ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded">
                    <span>Hanya Stok Menipis (Kritis)</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Goods Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Kode Barang / Kodefikasi</th>
                        <th class="px-5 py-3.5">Nama Item Persediaan</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Stok Saat Ini</th>
                        <th class="px-5 py-3.5 text-center">Batas Minimum</th>
                        <th class="px-5 py-3.5">Lokasi Rak Simpan</th>
                        <th class="px-5 py-3.5 text-right">Kartu Stok & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($goods as $g)
                        <tr class="hover:bg-slate-50/80 transition {{ $g->isLowStock() ? 'bg-rose-50/30' : '' }}">
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-xs">
                                    {{ $g->code }}
                                </span>
                                @if(str_starts_with($g->code, '1.1.7'))
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 ml-1">
                                        BHP BAS
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $g->name }}</div>
                                @if($g->isLowStock())
                                    <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-bold bg-rose-100 text-rose-800 mt-0.5">
                                        ⚠️ Stok Kritis (Segera Beli)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $g->category }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold font-mono text-sm {{ $g->isLowStock() ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $g->current_stock }} {{ $g->unit }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-slate-500">
                                {{ $g->minimum_stock }} {{ $g->unit }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-medium">
                                {{ $g->storage_location ?? 'Gudang Utama' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('warehouse.goods.card', $g) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition border border-blue-200" title="Kartu Stok">
                                    Kartu
                                </a>
                                <button type="button" onclick="openEditGood({{ json_encode([
                                    'id' => $g->id,
                                    'code' => $g->code,
                                    'name' => $g->name,
                                    'category' => $g->category,
                                    'unit' => $g->unit,
                                    'current_stock' => $g->current_stock,
                                    'minimum_stock' => $g->minimum_stock,
                                    'storage_location' => $g->storage_location,
                                ]) }})" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Barang">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('warehouse.goods.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus barang persediaan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Barang">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data persediaan barang gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($goods instanceof \Illuminate\Pagination\LengthAwarePaginator && $goods->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $goods->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Tambah Barang Persediaan -->
<div id="modal-good" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[100] p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden my-auto">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Tambah Barang Persediaan (BHP)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Input katalog barang habis pakai / logistik operasional sekolah.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-good').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('warehouse.goods.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden m-0">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1">
                <!-- Banner Pencarian Cepat Kodefikasi BHP -->
                <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-emerald-950 dark:text-emerald-200 text-xs">Pilih dari Katalog Resmi Kodefikasi BHP</div>
                            <div class="text-[11px] text-emerald-700 dark:text-emerald-300">Tersedia 70+ kodefikasi standar ATK, Cetakan, Lab & Medis (Akun 1.1.7).</div>
                        </div>
                    </div>
                    <button type="button" onclick="openConsumableModal({ targetCodeId: 'create-good-code', targetNameId: 'create-good-name', targetCategoryId: 'create-good-category', targetUnitId: 'create-good-unit', targetLocationId: 'create-good-location' })" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-2xs transition shrink-0 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Katalog BHP</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="create-good-code" placeholder="1.1.7.01.01.01.002 atau BRG-ATK-001" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">💡 Gunakan tombol Cari Katalog BHP di atas untuk kode resmi.</span>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                        <select name="category" id="create-good-category" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Item Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="create-good-name" placeholder="Contoh: Kertas HVS A4 80gr Sinar Dunia / Spidol Whiteboard" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan <span class="text-rose-500">*</span></label>
                        <select name="unit" id="create-good-unit" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                            @foreach($units as $u)
                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Awal <span class="text-rose-500">*</span></label>
                        <input type="number" name="current_stock" value="10" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Batas Minimum <span class="text-rose-500">*</span></label>
                        <input type="number" name="minimum_stock" value="5" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Penyimpanan (Rak / Lemari)</label>
                    <input type="text" name="storage_location" id="create-good-location" placeholder="Gudang Logistik & ATK / Rak A-2" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-good').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Barang Persediaan -->
<div id="modal-edit-good" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[100] p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden my-auto">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Edit Item Barang Persediaan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui kode, nama, kategori, stok, dan lokasi penyimpanan barang.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-good').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-edit-good" method="POST" class="flex flex-col flex-1 overflow-hidden m-0">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1">
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-700">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Sesuaikan dengan katalog resmi:</span>
                    <button type="button" onclick="openConsumableModal({ targetCodeId: 'edit-good-code', targetNameId: 'edit-good-name', targetCategoryId: 'edit-good-category', targetUnitId: 'edit-good-unit', targetLocationId: 'edit-good-location' })" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900 text-emerald-800 dark:text-emerald-300 font-bold text-xs rounded-xl border border-emerald-200 dark:border-emerald-800 transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Pilih dari Katalog BHP</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="edit-good-code" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">💡 Kode barang dapat disesuaikan secara manual atau dari katalog.</span>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                        <select name="category" id="edit-good-category" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Item Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit-good-name" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan <span class="text-rose-500">*</span></label>
                        <select name="unit" id="edit-good-unit" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                            @foreach($units as $u)
                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Saat Ini <span class="text-rose-500">*</span></label>
                        <input type="number" name="current_stock" id="edit-good-stock" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Batas Minimum <span class="text-rose-500">*</span></label>
                        <input type="number" name="minimum_stock" id="edit-good-min" min="0" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Penyimpanan (Rak / Lemari)</label>
                    <input type="text" name="storage_location" id="edit-good-location" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-edit-good').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pencarian Katalog Kodefikasi BHP -->
<x-consumable-search-modal
    targetNameId="create-good-name"
    targetCodeId="create-good-code"
    targetCategoryId="create-good-category"
    targetUnitId="create-good-unit"
    targetLocationId="create-good-location"
/>

<script>
    function openCreateGoodModal() {
        document.getElementById('modal-good').classList.remove('hidden');
    }

    function openEditGood(data) {
        document.getElementById('form-edit-good').action = '/warehouse/goods/' + data.id;
        document.getElementById('edit-good-code').value = data.code;
        document.getElementById('edit-good-category').value = data.category;
        document.getElementById('edit-good-name').value = data.name;
        document.getElementById('edit-good-unit').value = data.unit;
        document.getElementById('edit-good-stock').value = data.current_stock;
        document.getElementById('edit-good-min').value = data.minimum_stock;
        document.getElementById('edit-good-location').value = data.storage_location || '';
        document.getElementById('modal-edit-good').classList.remove('hidden');
    }
</script>
@endpush
