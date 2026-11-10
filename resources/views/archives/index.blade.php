@extends('layouts.app')

@section('title', 'Kearsipan Digital Sekolah')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kearsipan Digital Sekolah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan Jadwal Retensi Arsip (JRA), pemilahan berkas aktif/inaktif, dan lokasi rak fisik.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-import-modal type="archives" title="Berkas Arsip Digital" />
            <button onclick="document.getElementById('modal-archive').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Arsip Digital
            </button>
        </div>
    </div>

    <!-- Dasar Regulasi Kearsipan SIKD Jabar Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-blue-950 text-white p-4 rounded-2xl shadow-xs border border-blue-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-0.5">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/30 text-blue-200 border border-blue-400/30">Dasar Regulasi Kearsipan Resmi</span>
                <span class="text-xs text-blue-200/60">• Pergub Jabar No. 14/2018 & SIKD Jabar</span>
            </div>
            <p class="text-xs text-slate-300">Pemberkasan arsip aktif, inaktif, dan permanen terintegrasi dengan klasifikasi urusan Substantif Pendidikan (PK), GTK (KPG), Perpustakaan (PS), Kesiswaan (KO/KS), TU/Humas (TU/HM), Sarpras (PL/RT), dan Keuangan (KU).</p>
        </div>
        <a href="{{ route('letters.classifications.index') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shrink-0 inline-flex items-center gap-1.5 shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Buku Panduan Kode Kearsipan
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('archives.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul arsip, kode klasifikasi, atau nomor arsip..." 
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
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status Arsip</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inaktif" {{ request('status') === 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                    <option value="Permanen" {{ request('status') === 'Permanen' ? 'selected' : '' }}>Permanen (Abadi)</option>
                    <option value="Usul Musnah" {{ request('status') === 'Usul Musnah' ? 'selected' : '' }}>Usul Musnah</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Archives Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Arsip & Tahun</th>
                        <th class="px-5 py-3.5">Judul Berkas Arsip</th>
                        <th class="px-5 py-3.5">Kategori & Unit Asal</th>
                        <th class="px-5 py-3.5">Lokasi Rak / Lemari</th>
                        <th class="px-5 py-3.5">Jadwal Retensi</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($archives as $arc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 font-mono">{{ $arc->archive_number }}</div>
                                <div class="text-[11px] text-slate-400">Tahun: {{ $arc->year }}</div>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $arc->title }}</div>
                                <span class="text-[11px] text-slate-400 font-mono">Kode: {{ $arc->classification_code ?? 'ARS' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $arc->category }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $arc->origin_unit ?? 'Tata Usaha' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-medium">
                                {{ $arc->location_rack ?? 'Lemari Arsip Utama' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $arc->retention_period_years }} Tahun</div>
                                <div class="text-[11px] text-slate-500">Exp: {{ $arc->retention_expire_date?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($arc->archive_status === 'Aktif') bg-emerald-100 text-emerald-800
                                    @elseif($arc->archive_status === 'Inaktif') bg-blue-100 text-blue-800
                                    @elseif($arc->archive_status === 'Permanen') bg-purple-100 text-purple-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $arc->archive_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('archives.show', $arc) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition border border-blue-200" title="Verifikasi QR">
                                    Verifikasi QR
                                </a>
                                <button type="button" 
                                        onclick="openEditArchive({{ json_encode($arc) }})" 
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" 
                                        title="Edit Berkas Arsip">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('archives.destroy', $arc) }}" method="POST" onsubmit="return confirm('Hapus berkas arsip ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Berkas Arsip">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada berkas arsip yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($archives->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $archives->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Arsip -->
<div id="modal-archive" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Berkas Arsip Digital</h2>
        <p class="text-xs text-slate-500 mb-4">Input data pengarsipan surat, dokumen kesiswaan, atau kepegawaian.</p>

        <form action="{{ route('archives.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul Berkas Arsip <span class="text-rose-500">*</span></label>
                <input type="text" name="title" placeholder="Contoh: Berkas Kelulusan dan Ijazah Siswa TA 2024/2025" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori Arsip <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Dokumen <span class="text-rose-500">*</span></label>
                    <input type="number" name="year" value="{{ date('Y') }}" min="2000" max="{{ date('Y')+1 }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Kearsipan (Pergub Jabar 14/2018 & SIKD)</label>
                <select name="classification_code" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                    <option value="">-- Pilih Kode Klasifikasi Arsip (Opsional) --</option>
                    @foreach($classifications as $cl)
                        <option value="{{ $cl->code }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Arsip <span class="text-rose-500">*</span></label>
                    <select name="archive_status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Aktif">Aktif (Masih Sering Digunakan)</option>
                        <option value="Inaktif">Inaktif (Jarang Digunakan)</option>
                        <option value="Permanen">Permanen (Abadi / Bernilai Guna Tinggi)</option>
                        <option value="Usul Musnah">Usul Musnah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Masa Retensi (Tahun) <span class="text-rose-500">*</span></label>
                    <input type="number" name="retention_period_years" value="5" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit Pengolah Asal</label>
                    <input type="text" name="origin_unit" placeholder="Subbag Tata Usaha / Kurikulum" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Lokasi Fisik (Rak / Boks)</label>
                    <input type="text" name="location_rack" placeholder="Lemari A / Rak 03 / Boks 12" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan / Deskripsi Isi</label>
                <textarea name="notes" rows="2" placeholder="Uraian isi berkas arsip..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-archive').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Arsip -->
<div id="modal-edit-archive" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Berkas Arsip Digital</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui informasi dan rincian berkas arsip.</p>

        <form id="form-edit-archive" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul Berkas Arsip <span class="text-rose-500">*</span></label>
                <input type="text" id="edit-arc-title" name="title" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori Arsip <span class="text-rose-500">*</span></label>
                    <select id="edit-arc-category" name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Dokumen <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit-arc-year" name="year" min="2000" max="{{ date('Y')+1 }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Kearsipan (Pergub Jabar 14/2018 & SIKD)</label>
                <select id="edit-arc-classification_code" name="classification_code" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                    <option value="">-- Pilih Kode Klasifikasi Arsip (Opsional) --</option>
                    @foreach($classifications as $cl)
                        <option value="{{ $cl->code }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Arsip <span class="text-rose-500">*</span></label>
                    <select id="edit-arc-status" name="archive_status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Aktif">Aktif (Masih Sering Digunakan)</option>
                        <option value="Inaktif">Inaktif (Jarang Digunakan)</option>
                        <option value="Permanen">Permanen (Abadi / Bernilai Guna Tinggi)</option>
                        <option value="Usul Musnah">Usul Musnah</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Masa Retensi (Tahun) <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit-arc-retention" name="retention_period_years" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit Pengolah Asal</label>
                    <input type="text" id="edit-arc-origin-unit" name="origin_unit" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Lokasi Fisik (Rak / Boks)</label>
                    <input type="text" id="edit-arc-location" name="location_rack" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan / Deskripsi Isi</label>
                <textarea id="edit-arc-notes" name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-archive').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer">Perbarui Arsip</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditArchive(archive) {
        document.getElementById('form-edit-archive').action = `/archives/${archive.id}`;
        document.getElementById('edit-arc-title').value = archive.title || '';
        document.getElementById('edit-arc-category').value = archive.category || 'Kesiswaan';
        document.getElementById('edit-arc-classification_code').value = archive.classification_code || '';
        document.getElementById('edit-arc-year').value = archive.year || '{{ date('Y') }}';
        document.getElementById('edit-arc-status').value = archive.archive_status || 'Aktif';
        document.getElementById('edit-arc-retention').value = archive.retention_period_years || 5;
        document.getElementById('edit-arc-origin-unit').value = archive.origin_unit || '';
        document.getElementById('edit-arc-location').value = archive.location_rack || '';
        document.getElementById('edit-arc-notes').value = archive.notes || '';
        document.getElementById('modal-edit-archive').classList.remove('hidden');
    }
</script>
@endsection
