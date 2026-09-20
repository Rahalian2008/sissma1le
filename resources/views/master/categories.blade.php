@extends('layouts.app')

@section('title', 'Master Butir Tata Tertib - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-bold">
                MASTER DATA TATA TERTIB
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Katalog Master Butir Tata Tertib & Prestasi</h1>
            <p class="text-xs text-slate-300 mt-1">Daftar regulasi resmi butir penghargaan prestasi (R1&ndash;R10) dan sanksi pelanggaran (P1&ndash;P6) yang dapat diedit dan ditambahkan sewaktu-waktu.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('discipline.ranking') }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Papan Peringkat
            </a>
        </div>
    </div>

    <!-- 2 Columns: Prestasi & Pelanggaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Master Prestasi (R1 - R10) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">R</span>
                    <h3 class="text-base font-bold text-slate-800">Master Butir Prestasi (R1 &ndash; R10)</h3>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-full text-xs">
                    {{ $achievementCats->sum(fn($c) => $c->items->count()) }} Butir Aktif
                </span>
            </div>

            <!-- Form Tambah Prestasi Baru -->
            <form action="{{ route('master.achievement_item.store') }}" method="POST" class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-3 text-xs">
                @csrf
                <div class="flex items-center justify-between font-bold text-slate-800">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Butir Prestasi Baru
                    </div>
                    <button type="button" onclick="document.getElementById('importAchievementItemModal').classList.remove('hidden')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Impor CSV
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kategori Induk</label>
                        <select name="category_id" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                            @foreach($achievementCats as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->code }} - {{ $ac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kode Butir</label>
                        <input type="text" name="code" placeholder="Contoh: R1.4" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Prestasi / Bentuk Apresiasi</label>
                    <input type="text" name="name" placeholder="Uraian prestasi..." required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Poin Apresiasi</label>
                        <input type="number" name="default_points" value="25" min="1" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-black text-emerald-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tingkat</label>
                        <select name="level" class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                            <option value="SEKOLAH">SEKOLAH</option>
                            <option value="KECAMATAN">KECAMATAN</option>
                            <option value="KABUPATEN">KABUPATEN</option>
                            <option value="PROVINSI">PROVINSI</option>
                            <option value="NASIONAL">NASIONAL</option>
                            <option value="INTERNASIONAL">INTERNASIONAL</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition shadow-xs">
                    + Simpan Butir Prestasi Baru
                </button>
            </form>

            <!-- Daftar Kategori & Butir Prestasi -->
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                @foreach($achievementCats as $cat)
                    <div class="border border-slate-200 rounded-2xl p-4 bg-white hover:border-emerald-200 transition">
                        <div class="flex items-center justify-between font-bold text-xs text-slate-900 mb-2 border-b border-slate-100 pb-2">
                            <span class="text-emerald-700 font-black">{{ $cat->code }} &ndash; {{ $cat->name }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $cat->items->count() }} Butir</span>
                        </div>
                        <div class="space-y-1.5 text-[11px]">
                            @forelse($cat->items as $item)
                                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition group">
                                    <div class="flex-1 mr-2">
                                        <div class="font-bold text-slate-800">
                                            <span class="font-mono text-emerald-700 font-black">[{{ $item->code }}]</span> {{ $item->name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium">Tingkat: {{ $item->level }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-black rounded-md text-[10px]">
                                            +{{ $item->default_points }}
                                        </span>
                                        <button onclick="editAchievementItem({{ json_encode($item) }})" class="p-1 hover:bg-emerald-100 text-emerald-700 rounded-md transition" title="Edit Butir">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('master.achievement_item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus butir prestasi {{ $item->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 hover:bg-rose-100 text-rose-600 rounded-md transition" title="Hapus Butir">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-[11px] text-slate-400 italic py-1">Belum ada butir dalam kategori ini.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. Master Pelanggaran (P1 - P6) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-black text-xs">P</span>
                    <h3 class="text-base font-bold text-slate-800">Master Butir Pelanggaran (P1 &ndash; P6)</h3>
                </div>
                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold rounded-full text-xs">
                    {{ $violationCats->sum(fn($c) => $c->items->count()) }} Butir Sanksi
                </span>
            </div>

            <!-- Form Tambah Pelanggaran Baru -->
            <form action="{{ route('master.violation_item.store') }}" method="POST" class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100 space-y-3 text-xs">
                @csrf
                <div class="flex items-center justify-between font-bold text-slate-800">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Butir Pelanggaran Baru
                    </div>
                    <button type="button" onclick="document.getElementById('importViolationItemModal').classList.remove('hidden')" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Impor CSV
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kategori Induk</label>
                        <select name="category_id" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                            @foreach($violationCats as $vc)
                                <option value="{{ $vc->id }}">{{ $vc->code }} - {{ $vc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kode Butir</label>
                        <input type="text" name="code" placeholder="Contoh: P2.4" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bentuk Pelanggaran Tata Tertib</label>
                    <input type="text" name="name" placeholder="Uraian pelanggaran..." required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Poin Sanksi</label>
                        <input type="number" name="default_points" value="15" min="1" required class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-black text-rose-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Rekomendasi Pembinaan</label>
                        <input type="text" name="guidance_recommendation" placeholder="Tindakan awal BK..." class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl transition shadow-xs">
                    + Simpan Butir Pelanggaran Baru
                </button>
            </form>

            <!-- Daftar Kategori & Butir Pelanggaran -->
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                @foreach($violationCats as $vcat)
                    <div class="border border-slate-200 rounded-2xl p-4 bg-white hover:border-rose-200 transition">
                        <div class="flex items-center justify-between font-bold text-xs text-slate-900 mb-2 border-b border-slate-100 pb-2">
                            <span class="text-rose-700 font-black">{{ $vcat->code }} &ndash; {{ $vcat->name }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $vcat->items->count() }} Butir</span>
                        </div>
                        <div class="space-y-1.5 text-[11px]">
                            @forelse($vcat->items as $vitem)
                                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition group">
                                    <div class="flex-1 mr-2">
                                        <div class="font-bold text-slate-800">
                                            <span class="font-mono text-rose-700 font-black">[{{ $vitem->code }}]</span> {{ $vitem->name }}
                                        </div>
                                        @if($vitem->guidance_recommendation)
                                            <div class="text-[10px] text-slate-400 font-medium">Saran BK: {{ $vitem->guidance_recommendation }}</div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-black rounded-md text-[10px]">
                                            -{{ $vitem->default_points }}
                                        </span>
                                        <button onclick="editViolationItem({{ json_encode($vitem) }})" class="p-1 hover:bg-rose-100 text-rose-700 rounded-md transition" title="Edit Butir">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('master.violation_item.destroy', $vitem->id) }}" method="POST" onsubmit="return confirm('Hapus butir pelanggaran {{ $vitem->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 hover:bg-rose-100 text-rose-600 rounded-md transition" title="Hapus Butir">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-[11px] text-slate-400 italic py-1">Belum ada butir dalam kategori ini.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Prestasi -->
<div id="editAchievementModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Edit Butir Prestasi</h3>
            <button onclick="document.getElementById('editAchievementModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editAchievementForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Induk</label>
                <select name="category_id" id="edit_ach_cat_id" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none">
                    @foreach($achievementCats as $ac)
                        <option value="{{ $ac->id }}">{{ $ac->code }} - {{ $ac->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kode Butir</label>
                <input type="text" name="code" id="edit_ach_code" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Uraian Prestasi</label>
                <input type="text" name="name" id="edit_ach_name" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none font-bold">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Poin</label>
                    <input type="number" name="default_points" id="edit_ach_points" required min="1" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-emerald-700 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tingkat</label>
                    <select name="level" id="edit_ach_level" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none">
                        <option value="SEKOLAH">SEKOLAH</option>
                        <option value="KECAMATAN">KECAMATAN</option>
                        <option value="KABUPATEN">KABUPATEN</option>
                        <option value="PROVINSI">PROVINSI</option>
                        <option value="NASIONAL">NASIONAL</option>
                        <option value="INTERNASIONAL">INTERNASIONAL</option>
                    </select>
                </div>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editAchievementModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pelanggaran -->
<div id="editViolationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Edit Butir Pelanggaran</h3>
            <button onclick="document.getElementById('editViolationModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editViolationForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Induk</label>
                <select name="category_id" id="edit_vio_cat_id" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none">
                    @foreach($violationCats as $vc)
                        <option value="{{ $vc->id }}">{{ $vc->code }} - {{ $vc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kode Butir</label>
                <input type="text" name="code" id="edit_vio_code" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Bentuk Pelanggaran</label>
                <input type="text" name="name" id="edit_vio_name" required class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none font-bold">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Poin Sanksi</label>
                    <input type="number" name="default_points" id="edit_vio_points" required min="1" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-rose-600 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Rekomendasi Pembinaan</label>
                    <input type="text" name="guidance_recommendation" id="edit_vio_rec" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none">
                </div>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editViolationModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Impor Butir Prestasi -->
<div id="importAchievementItemModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Butir Prestasi (R1–R10)</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV katalog regulasi penghargaan prestasi</p>
                </div>
            </div>
            <button onclick="document.getElementById('importAchievementItemModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.achievement_item.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom: <code class="bg-white px-1.5 py-0.5 rounded text-emerald-700 font-mono">kode_kategori</code> (R1 s/d R10), <code class="bg-white px-1.5 py-0.5 rounded text-emerald-700 font-mono">kode_butir</code> (misal R1.1), <code class="bg-white px-1.5 py-0.5 rounded text-emerald-700 font-mono">nama_prestasi</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">poin_default</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tingkat</code> (SEKOLAH/KECAMATAN/KABUPATEN/PROVINSI/NASIONAL/INTERNASIONAL).
            </p>
        </div>

        <form action="{{ route('master.achievement_item.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importAchievementItemModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Butir Prestasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Impor Butir Pelanggaran -->
<div id="importViolationItemModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Butir Pelanggaran (P1–P6)</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV katalog regulasi tata tertib & sanksi</p>
                </div>
            </div>
            <button onclick="document.getElementById('importViolationItemModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.violation_item.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom: <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">kode_kategori</code> (P1 s/d P6), <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">kode_butir</code> (misal P1.1), <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">deskripsi_pelanggaran</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">poin_default</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tingkat</code> (RINGAN/SEDANG/BERAT/SANGAT_BERAT).
            </p>
        </div>

        <form action="{{ route('master.violation_item.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-600 file:text-white hover:file:bg-rose-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importViolationItemModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Butir Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editAchievementItem(item) {
        document.getElementById('editAchievementForm').action = '/master/achievement-item/' + item.id;
        document.getElementById('edit_ach_cat_id').value = item.category_id;
        document.getElementById('edit_ach_code').value = item.code;
        document.getElementById('edit_ach_name').value = item.name;
        document.getElementById('edit_ach_points').value = item.default_points;
        document.getElementById('edit_ach_level').value = item.level;
        document.getElementById('editAchievementModal').classList.remove('hidden');
    }

    function editViolationItem(item) {
        document.getElementById('editViolationForm').action = '/master/violation-item/' + item.id;
        document.getElementById('edit_vio_cat_id').value = item.category_id;
        document.getElementById('edit_vio_code').value = item.code;
        document.getElementById('edit_vio_name').value = item.name;
        document.getElementById('edit_vio_points').value = item.default_points;
        document.getElementById('edit_vio_rec').value = item.guidance_recommendation || '';
        document.getElementById('editViolationModal').classList.remove('hidden');
    }
</script>
@endsection
