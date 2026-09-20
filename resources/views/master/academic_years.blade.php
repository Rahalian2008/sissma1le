@extends('layouts.app')

@section('title', 'Tahun Pelajaran - Master Data SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-bold">
                MASTER DATA SEKOLAH
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Data Tahun Pelajaran & Semester</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola kalender akademik periode aktif, pergantian semester ganjil/genap, dan alokasi data tahunan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button onclick="document.getElementById('importAcademicYearModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Periode
            </button>
            <button onclick="document.getElementById('addAcademicYearModal').classList.remove('hidden')" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Tahun Pelajaran
            </button>
        </div>
    </div>

    <!-- Table of Academic Years -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Periode Tahun Pelajaran</h2>
                <span class="table-swipe-cue">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Geser Tabel →
                </span>
            </div>
            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold text-xs rounded-full self-start sm:self-auto">
                {{ $years->count() }} Periode Terdaftar
            </span>
        </div>

        <div class="responsive-table-container">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-4">Tahun Pelajaran</th>
                        <th class="p-4">Semester</th>
                        <th class="p-4">Tanggal Mulai</th>
                        <th class="p-4">Tanggal Berakhir</th>
                        <th class="p-4 text-center">Rombel Terhubung</th>
                        <th class="p-4 text-center">Status Periode</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($years as $y)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-black text-slate-800 text-sm">
                                {{ $y->name }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg font-bold {{ $y->semester === 'Ganjil' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    Semester {{ $y->semester }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($y->start_date)->format('d M Y') }}
                            </td>
                            <td class="p-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($y->end_date)->format('d M Y') }}
                            </td>
                            <td class="p-4 text-center font-bold text-slate-700">
                                {{ $y->classes_count }} Rombel
                            </td>
                            <td class="p-4 text-center">
                                @if($y->is_active)
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-black rounded-full text-xs flex items-center justify-center gap-1.5 mx-auto w-max">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        SEDANG AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-medium rounded-full text-[10px]">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$y->is_active)
                                        <form action="{{ route('master.academic_years.set_active', $y->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition">
                                                Jadikan Aktif
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('master.academic_years.destroy', $y->id) }}" method="POST" onsubmit="return confirm('Hapus periode tahun pelajaran {{ $y->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-rose-600 rounded-lg transition" title="Hapus Periode">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Belum ada tahun pelajaran yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Impor Tahun Pelajaran -->
<div id="importAcademicYearModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Tahun Pelajaran</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV kalender tahun pelajaran sekolah</p>
                </div>
            </div>
            <button onclick="document.getElementById('importAcademicYearModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('master.academic_years.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-600 hover:bg-orange-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom wajib: <code class="bg-white px-1.5 py-0.5 rounded text-orange-700 font-mono">nama_tahun</code> (contoh: 2026/2027), <code class="bg-white px-1.5 py-0.5 rounded text-orange-700 font-mono">semester</code> (GANJIL/GENAP).
                Kolom opsional: <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tanggal_mulai</code> (YYYY-MM-DD), <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tanggal_selesai</code> (YYYY-MM-DD), <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">is_active</code> (1/0).
            </p>
        </div>

        <form action="{{ route('master.academic_years.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-600 file:text-white hover:file:bg-orange-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importAcademicYearModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Periode
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Tahun Pelajaran -->
<div id="addAcademicYearModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-800 text-sm">Tambah Periode Tahun Pelajaran</h3>
            <button onclick="document.getElementById('addAcademicYearModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('master.academic_years.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Tahun Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: 2026/2027" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                <select name="semester" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold">
                    <option value="Ganjil">Semester Ganjil</option>
                    <option value="Genap">Semester Genap</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="is_active_check" value="1" class="w-4 h-4 text-amber-600 rounded">
                <label for="is_active_check" class="font-bold text-slate-700 cursor-pointer">Langsung jadikan Tahun Pelajaran Aktif</label>
            </div>
            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addAcademicYearModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-xs">Simpan Periode</button>
            </div>
        </form>
    </div>
</div>
@endsection
