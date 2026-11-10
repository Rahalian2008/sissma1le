@extends('layouts.app')

@section('title', 'Data Ekstrakurikuler - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    @include('master.partials.nav')
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 rounded-full text-xs font-bold">
                    MASTER DATA SEKOLAH
                </span>
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                    PENGEMBANGAN DIRI & BAKAT
                </span>
            </div>
            <h1 class="text-xl sm:text-3xl font-black text-white">Data Master Ekstrakurikuler</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola jenis-jenis kegiatan ekstrakurikuler, penugasan guru pembina, lokasi kegiatan, serta daftar anggota siswa.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <a href="{{ route('extracurricular.index') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 shadow-xs transition">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Buka Presensi Ekstra
            </a>
            <button onclick="document.getElementById('addExtraModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Ekstrakurikuler
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex flex-col md:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('master.extracurriculars') }}" class="flex flex-1 flex-col sm:flex-row gap-2.5 w-full">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" id="extraSearchInput" placeholder="Cari nama ekstra, kode, pembina, atau lokasi..." class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </div>
            <select name="category" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-semibold text-slate-700 dark:text-slate-200">
                <option value="">Semua Kategori / Jenis</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ ($category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition">Filter</button>
            @if(!empty($search) || !empty($category))
                <a href="{{ route('master.extracurriculars') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold text-center transition">Reset</a>
            @endif
        </form>
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl whitespace-nowrap">
            Total: {{ $extracurriculars->count() }} Ekstrakurikuler
        </span>
    </div>

    <!-- Grid Cards of Extracurriculars -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($extracurriculars as $extra)
            <div class="extra-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between space-y-4"
                 data-search="{{ strtolower($extra->code . ' ' . $extra->name . ' ' . $extra->category . ' ' . $extra->teacher_names . ' ' . ($extra->location ?? '')) }}">
                
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-sm shrink-0">
                                {{ strtoupper(substr($extra->code ?? $extra->name, 0, 3)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm leading-tight hover:text-cyan-700 dark:hover:text-cyan-400 transition truncate" title="{{ $extra->name }}">
                                    {{ $extra->name }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-1 min-w-0">
                                    <span class="px-2 py-0.5 bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800 font-bold rounded-md text-[10px] shrink-0">
                                        {{ $extra->category }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 truncate">#{{ $extra->code }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 whitespace-nowrap {{ $extra->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                            {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($extra->description)
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-3 line-clamp-2 leading-relaxed">
                            {{ $extra->description }}
                        </p>
                    @endif

                    <!-- Details Box -->
                    <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                        <!-- Pembina -->
                        <div class="flex items-start gap-2">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">👤 Pembina:</span>
                            <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                {{ $extra->teacher_names }}
                                @if($extra->all_teachers->isNotEmpty())
                                    <span class="block text-[10px] text-cyan-600 dark:text-cyan-400 font-normal">({{ $extra->all_teachers->count() }} Guru Pembina)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300 pt-1 border-t border-slate-200/60 dark:border-slate-700/60 min-w-0 gap-2">
                            <div class="truncate min-w-0 flex-1" title="{{ $extra->location ?? '-' }}">
                                <span class="text-slate-400 dark:text-slate-500">📍 Lokasi:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $extra->location ?? 'Lingkungan Sekolah' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Card / Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <!-- Member count trigger modal -->
                    <button type="button" onclick="openMembersModal({{ $extra->id }})" class="px-3 py-1.5 bg-cyan-50 dark:bg-cyan-950/40 hover:bg-cyan-100 dark:hover:bg-cyan-900/60 text-cyan-800 dark:text-cyan-300 font-bold rounded-xl text-xs flex items-center gap-1.5 transition cursor-pointer" title="Kelola Anggota Ekstrakurikuler">
                        <svg class="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>+ Kelola Anggota ({{ $extra->students_count }})</span>
                    </button>

                    <div class="flex items-center gap-1">
                        <!-- Link to Direct Attendance for this extra -->
                        <a href="{{ route('extracurricular.index', ['extra_id' => $extra->id]) }}" class="p-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-xl transition" title="Buka Presensi Hari Ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </a>

                        <!-- Edit -->
                        <button type="button" onclick="editExtra({{ $extra->id }})" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-cyan-50 dark:hover:bg-cyan-950/50 text-slate-700 dark:text-slate-300 hover:text-cyan-700 dark:hover:text-cyan-400 rounded-xl transition cursor-pointer" title="Edit Data Ekstra">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>

                        <!-- Delete -->
                        <form action="{{ route('master.extracurriculars.destroy', $extra->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ekstrakurikuler {{ $extra->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl transition" title="Hapus Ekstrakurikuler">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center text-slate-400 dark:text-slate-500 space-y-3">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto text-2xl font-black">
                    🎪
                </div>
                <h3 class="font-black text-slate-700 dark:text-slate-200 text-base">Belum Ada Ekstrakurikuler</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto">Silakan tambahkan data ekstrakurikuler baru untuk memulai manajemen kegiatan bakat & minat siswa.</p>
                <button onclick="document.getElementById('addExtraModal').classList.remove('hidden')" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs transition">
                    + Tambah Ekstrakurikuler Baru
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Ekstrakurikuler -->
<div id="addExtraModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Tambah Ekstrakurikuler Baru</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Lengkapi informasi nama, jenis, pembina, dan jadwal latihan.</p>
            </div>
            <button onclick="document.getElementById('addExtraModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>
        <form action="{{ route('master.extracurriculars.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Cth: PRA" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono uppercase font-bold text-slate-800 dark:text-slate-100">
                </div>
                <div class="col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Cth: Pramuka Ambalan" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis / Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" list="category_suggestions" required placeholder="Cth: Olahraga & Bela Diri" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                    <datalist id="category_suggestions">
                        <option value="Kepanduan & Bela Negara">
                        <option value="Olahraga & Bela Diri">
                        <option value="Seni, Musik & Budaya">
                        <option value="Keagamaan & Rohani">
                        <option value="Akademik & Sains">
                        <option value="Bahasa & Komunikasi">
                        <option value="Teknologi & Multimedia">
                    </datalist>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan <span class="text-rose-500">*</span></label>
                    <select name="is_active" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Pilih Guru Pembina <span class="text-[11px] font-normal text-cyan-700 dark:text-cyan-400 font-semibold">(Bisa pilih lebih dari 1 guru pembina)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-36 overflow-y-auto">
                    @foreach($teachers as $teacher)
                    <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-700 cursor-pointer transition">
                        <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" class="add-teacher-cb w-4 h-4 rounded text-cyan-600 focus:ring-0">
                        <div class="min-w-0">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $teacher->name }}</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block truncate">{{ $teacher->title ?? 'Guru' }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Latihan / Kegiatan</label>
                <input type="text" name="location" placeholder="Cth: Lapangan Utama / Aula / Laboratorium" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat / Visi</label>
                <textarea name="description" rows="2" placeholder="Keterangan singkat tentang program ekstrakurikuler..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addExtraModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-xs transition">Simpan Ekstrakurikuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Ekstrakurikuler -->
<div id="editExtraModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm">Edit Data Ekstrakurikuler</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Perbarui informasi nama, kategori, jadwal, atau pembina.</p>
            </div>
            <button onclick="document.getElementById('editExtraModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>
        <form id="editExtraForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="edit_extra_code" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-mono uppercase font-bold text-slate-800 dark:text-slate-100">
                </div>
                <div class="col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_extra_name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis / Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" id="edit_extra_category" list="category_suggestions" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan <span class="text-rose-500">*</span></label>
                    <select name="is_active" id="edit_extra_is_active" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Pilih Guru Pembina <span class="text-[11px] font-normal text-cyan-700 dark:text-cyan-400 font-semibold">(Bisa pilih lebih dari 1 guru pembina)</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] max-h-36 overflow-y-auto">
                    @foreach($teachers as $teacher)
                    <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-700 cursor-pointer transition">
                        <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" class="edit-teacher-cb w-4 h-4 rounded text-cyan-600 focus:ring-0">
                        <div class="min-w-0">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $teacher->name }}</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block truncate">{{ $teacher->title ?? 'Guru' }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Latihan / Kegiatan</label>
                <input type="text" name="location" id="edit_extra_location" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                <textarea name="description" id="edit_extra_description" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('editExtraModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Anggota Ekstrakurikuler -->
<div id="membersExtraModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-sm flex items-center gap-2">
                    <span>Kelola Anggota Ekstra:</span>
                    <span id="membersModalTitle" class="text-cyan-700 dark:text-cyan-400 font-black"></span>
                </h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Tambah anggota siswa baru atau hapus keanggotaan dari kegiatan ini.</p>
            </div>
            <button onclick="document.getElementById('membersExtraModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
        </div>

        <!-- Form Tambah Anggota Baru -->
        <form id="addMemberForm" method="POST" class="p-3.5 bg-cyan-50/60 dark:bg-cyan-950/20 border border-cyan-100 dark:border-cyan-900/50 rounded-2xl flex flex-col gap-2.5 text-xs">
            @csrf
            <div class="relative" id="master-extra-member-combobox">
                <label class="block font-bold text-cyan-900 dark:text-cyan-300 mb-1">
                    Pilih Siswa untuk Ditambahkan *
                    <span class="text-[10px] font-normal text-slate-500 dark:text-slate-400 ml-1">
                        (Cari dengan ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="master_extra_student_id" required>

                <!-- Selected Preview Badge -->
                <div id="master-extra-student-selected-preview" class="hidden mb-2 p-2.5 bg-white dark:bg-slate-800 border border-cyan-300 dark:border-cyan-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div id="master-extra-student-preview-avatar" class="w-7 h-7 rounded-lg bg-cyan-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                            -
                        </div>
                        <div class="min-w-0">
                            <div id="master-extra-student-preview-name" class="font-bold text-slate-900 dark:text-slate-100 truncate text-xs"></div>
                            <div id="master-extra-student-preview-meta" class="text-[10px] text-cyan-700 dark:text-cyan-400 truncate"></div>
                        </div>
                    </div>
                    <button type="button" onclick="clearMasterExtraMemberSelection()" class="px-2 py-0.5 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative" id="master-extra-student-search-wrapper">
                    <input type="text" id="master_extra_member_search_input"
                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa..."
                        autocomplete="off"
                        onfocus="openMasterExtraMemberDropdown()"
                        oninput="filterMasterExtraMemberStudents(this.value)"
                        class="w-full pl-9 pr-9 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-cyan-500 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="master-extra-student-search-clear" onclick="resetMasterExtraMemberSearch()" class="hidden absolute right-3 top-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-1.5 flex items-center gap-1 overflow-x-auto pb-1 text-xs no-scrollbar" id="master-extra-initials-bar">
                    <button type="button" onclick="filterMasterExtraMemberByInitial('ALL')"
                        class="master-extra-initial-chip px-2 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterMasterExtraMemberByInitial('{{ $char }}')"
                            class="master-extra-initial-chip px-1.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-100 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Result List -->
                <div id="master-extra-member-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 max-h-56 overflow-y-auto rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($allStudents as $st)
                        @php
                            $words = explode(' ', trim($st->name));
                            $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                        @endphp
                        <div class="master-extra-student-item px-3 py-2 hover:bg-cyan-50 dark:hover:bg-slate-700/60 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $st->id }}"
                             data-name="{{ $st->name }}"
                             data-nis="{{ $st->nis }}"
                             data-class="{{ $st->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($initials) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectMasterExtraMemberStudent('{{ $st->id }}', '{{ addslashes($st->name) }}', 'Kelas {{ $st->schoolClass?->name ?? 'Umum' }} • NIS: {{ $st->nis }}', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                            <div class="flex items-center gap-2 truncate mr-2">
                                <span class="w-6 h-6 rounded-md bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($initials, 0, 2)) }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $st->name }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500">Kelas {{ $st->schoolClass?->name ?? 'Umum' }} • NIS: {{ $st->nis }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono shrink-0 font-bold">
                                {{ strtoupper($initials) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-center justify-between pt-1">
                <div class="w-full sm:w-56">
                    <label class="block font-bold text-cyan-900 dark:text-cyan-300 mb-0.5">Peran / Jabatan Siswa:</label>
                    <select name="role" class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-xs font-semibold text-slate-800 dark:text-slate-100">
                        <option value="Anggota" selected>Anggota Biasa</option>
                        <option value="Ketua">Ketua Ekstrakurikuler</option>
                        <option value="Wakil Ketua">Wakil Ketua</option>
                        <option value="Sekretaris">Sekretaris (Bisa Absen Manual)</option>
                        <option value="Bendahara">Bendahara</option>
                        <option value="Koordinator Latihan">Koordinator Latihan</option>
                    </select>
                </div>
                <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl whitespace-nowrap shadow-xs text-xs cursor-pointer transition">
                    + Tambahkan ke Ekstra
                </button>
            </div>
        </form>

        <!-- Daftar Anggota Terdaftar -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-bold text-slate-700 dark:text-slate-300 text-xs">Daftar Anggota Saat Ini (<span id="memberCountBadge">0</span> Siswa)</h4>
                <input type="text" id="memberFilterInput" placeholder="Cari nama siswa..." class="px-2.5 py-1 text-[11px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </div>
            <div class="max-h-64 overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-2xl divide-y divide-slate-100 dark:divide-slate-800 text-xs" id="membersListContainer">
                <!-- Javascript will render student items here -->
            </div>
        </div>

        <div class="pt-2 flex justify-end border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="document.getElementById('membersExtraModal').classList.add('hidden')" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl font-bold text-xs text-slate-700 dark:text-slate-300 transition">Tutup</button>
        </div>
    </div>
</div>

<script>
    const allExtras = @json($extracurriculars->keyBy('id'));

    // Live filter search for extracurricular cards
    document.getElementById('extraSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.extra-card').forEach(card => {
            const text = card.getAttribute('data-search') || '';
            card.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function editExtra(extraOrId) {
        const e = typeof extraOrId === 'object' ? extraOrId : allExtras[extraOrId];
        if (!e) return;
        document.getElementById('editExtraForm').action = '/master/extracurriculars/' + e.id;
        document.getElementById('edit_extra_code').value = e.code || '';
        document.getElementById('edit_extra_name').value = e.name || '';
        document.getElementById('edit_extra_category').value = e.category || '';
        
        // Multi-teacher selection
        const enrolledTeacherIds = [];
        if (e.teacher_id) {
            enrolledTeacherIds.push(parseInt(e.teacher_id));
        }
        if (e.teachers && Array.isArray(e.teachers)) {
            e.teachers.forEach(t => {
                const tid = parseInt(t.id);
                if (!enrolledTeacherIds.includes(tid)) {
                    enrolledTeacherIds.push(tid);
                }
            });
        }
        document.querySelectorAll('.edit-teacher-cb').forEach(cb => {
            cb.checked = enrolledTeacherIds.includes(parseInt(cb.value));
        });

        document.getElementById('edit_extra_location').value = e.location || '';
        document.getElementById('edit_extra_is_active').value = e.is_active ? '1' : '0';
        document.getElementById('edit_extra_description').value = e.description || '';
        document.getElementById('editExtraModal').classList.remove('hidden');
    }

    let currentExtraStudents = [];
    let currentExtraId = null;

    function openMembersModal(extraOrId) {
        const extra = typeof extraOrId === 'object' ? extraOrId : allExtras[extraOrId];
        if (!extra) return;
        currentExtraId = extra.id;
        currentExtraStudents = extra.students || [];
        document.getElementById('membersModalTitle').textContent = extra.name;
        document.getElementById('addMemberForm').action = '/master/extracurriculars/' + extra.id + '/members';
        clearMasterExtraMemberSelection();
        renderMembersList(currentExtraStudents);
        document.getElementById('membersExtraModal').classList.remove('hidden');
    }

    function renderMembersList(students) {
        const container = document.getElementById('membersListContainer');
        document.getElementById('memberCountBadge').textContent = students.length;
        if (!students || students.length === 0) {
            container.innerHTML = `<div class="p-6 text-center text-slate-400 dark:text-slate-500 text-xs">Belum ada siswa yang terdaftar dalam kegiatan ekstra ini. Silakan tambahkan anggota di atas.</div>`;
            return;
        }

        let html = '';
        students.forEach(st => {
            const role = (st.pivot && st.pivot.role) ? st.pivot.role : 'Anggota';
            const isSecretary = role.toLowerCase().includes('sekretaris');
            const className = st.school_class ? st.school_class.name : (st.schoolClass ? st.schoolClass.name : 'Umum');
            html += `
                <div class="member-item p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition" data-name="${(st.name || '').toLowerCase()}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full ${isSecretary ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : 'bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300'} font-bold flex items-center justify-center text-xs shrink-0">
                            ${st.name.substring(0, 1)}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span>${st.name}</span>
                                ${isSecretary ? '<span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-extrabold border border-amber-300 dark:border-amber-700">Sekretaris</span>' : ''}
                            </div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500">
                                NISN: ${st.nisn || st.nis || '-'} • Kelas: ${className}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <form action="/master/extracurriculars/${currentExtraId}/members/${st.id}/role" method="POST" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <select name="role" onchange="this.form.submit()" title="Ganti Jabatan Anggota" class="text-[11px] font-semibold px-2 py-1 rounded-lg border cursor-pointer transition ${isSecretary ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 font-bold' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700'}">
                                <option value="Anggota" ${role === 'Anggota' ? 'selected' : ''}>Anggota Biasa</option>
                                <option value="Ketua" ${role === 'Ketua' ? 'selected' : ''}>Ketua</option>
                                <option value="Wakil Ketua" ${role === 'Wakil Ketua' ? 'selected' : ''}>Wakil Ketua</option>
                                <option value="Sekretaris" ${role === 'Sekretaris' ? 'selected' : ''}>Sekretaris</option>
                                <option value="Bendahara" ${role === 'Bendahara' ? 'selected' : ''}>Bendahara</option>
                                <option value="Koordinator Latihan" ${role === 'Koordinator Latihan' ? 'selected' : ''}>Koord. Latihan</option>
                            </select>
                        </form>
                        <form action="/master/extracurriculars/${currentExtraId}/members/${st.id}" method="POST" onsubmit="return confirm('Keluarkan siswa ${st.name} dari ekstra ini?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition cursor-pointer" title="Keluarkan Anggota">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // Filter member search inside modal
    document.getElementById('memberFilterInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.member-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            item.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Helper functions for Master Extra Member Combobox
    let activeMasterExtraMemberInitial = 'ALL';

    function openMasterExtraMemberDropdown() {
        const dd = document.getElementById('master-extra-member-dropdown');
        if (dd) dd.classList.remove('hidden');
    }

    function closeMasterExtraMemberDropdown() {
        const dd = document.getElementById('master-extra-member-dropdown');
        if (dd) dd.classList.add('hidden');
    }

    function resetMasterExtraMemberSearch() {
        const input = document.getElementById('master_extra_member_search_input');
        if (input) input.value = '';
        document.getElementById('master-extra-student-search-clear')?.classList.add('hidden');
        filterMasterExtraMemberStudents('');
    }

    function filterMasterExtraMemberStudents(query) {
        const q = (query || '').toLowerCase().trim();
        const clearBtn = document.getElementById('master-extra-student-search-clear');
        if (clearBtn) {
            if (q.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        const items = document.querySelectorAll('.master-extra-student-item');
        items.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const nis = (item.dataset.nis || '').toLowerCase();
            const cls = (item.dataset.class || '').toLowerCase();
            const initials = (item.dataset.initials || '').toLowerCase();
            const firstLetter = (item.dataset.firstletter || '').toUpperCase();

            const matchesQuery = q === '' || name.includes(q) || nis.includes(q) || cls.includes(q) || initials.includes(q);
            const matchesInitial = activeMasterExtraMemberInitial === 'ALL' || firstLetter === activeMasterExtraMemberInitial;

            if (matchesQuery && matchesInitial) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
        openMasterExtraMemberDropdown();
    }

    function filterMasterExtraMemberByInitial(char) {
        activeMasterExtraMemberInitial = char;
        document.querySelectorAll('.master-extra-initial-chip').forEach(btn => {
            if (btn.dataset.letter === char) {
                btn.className = 'master-extra-initial-chip px-2 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition';
            } else {
                btn.className = 'master-extra-initial-chip px-1.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-100 dark:hover:bg-slate-700';
            }
        });
        const currentQuery = document.getElementById('master_extra_member_search_input')?.value || '';
        filterMasterExtraMemberStudents(currentQuery);
    }

    function selectMasterExtraMemberStudent(id, name, meta, initials) {
        document.getElementById('master_extra_student_id').value = id;
        document.getElementById('master-extra-student-preview-name').innerText = name;
        document.getElementById('master-extra-student-preview-meta').innerText = meta;
        document.getElementById('master-extra-student-preview-avatar').innerText = initials;

        document.getElementById('master-extra-student-selected-preview').classList.remove('hidden');
        document.getElementById('master-extra-student-search-wrapper').classList.add('hidden');
        document.getElementById('master-extra-initials-bar').classList.add('hidden');
        closeMasterExtraMemberDropdown();
    }

    function clearMasterExtraMemberSelection() {
        document.getElementById('master_extra_student_id').value = '';
        document.getElementById('master-extra-student-selected-preview').classList.add('hidden');
        document.getElementById('master-extra-student-search-wrapper').classList.remove('hidden');
        document.getElementById('master-extra-initials-bar').classList.remove('hidden');
        resetMasterExtraMemberSearch();
    }

    document.addEventListener('click', function(e) {
        const masterCombobox = document.getElementById('master-extra-member-combobox');
        if (masterCombobox && !masterCombobox.contains(e.target)) {
            closeMasterExtraMemberDropdown();
        }
    });

    @if(request()->query('open_members'))
        document.addEventListener('DOMContentLoaded', function() {
            openMembersModal({{ (int) request()->query('open_members') }});
        });
    @endif
</script>
@endsection
