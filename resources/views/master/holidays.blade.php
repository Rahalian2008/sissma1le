@extends('layouts.app')

@section('title', 'Pengaturan Hari Libur & Tanggal Merah')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-rose-600 via-red-600 to-amber-600 text-white shadow-xl shadow-rose-600/20 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-white text-xs font-semibold mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Master Data Kalender & Tanggal Merah
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Hari Libur & Tanggal Merah</h1>
                <p class="text-sm text-rose-100 mt-1 max-w-2xl leading-relaxed">
                    Atur hari libur nasional, cuti bersama, dan kalender pendidikan sekolah. 
                    <span class="font-bold underline decoration-white/40">Sistem presensi (Selfie, QR, RFID) otomatis nonaktif</span> pada tanggal yang didaftarkan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openAddModal()"
                    class="px-4 py-2.5 bg-white text-rose-700 hover:bg-rose-50 font-bold rounded-2xl text-xs flex items-center gap-2 shadow-lg shadow-black/10 transition transform active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Hari Libur
                </button>
                <form action="{{ route('master.holidays.generate_common') }}" method="POST" onsubmit="return confirm('Muat otomatis daftar Hari Libur Nasional & Kalender Pendidikan tahun {{ $year }}?')">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit"
                        class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold rounded-2xl text-xs flex items-center gap-2 backdrop-blur-md transition transform active:scale-95 cursor-pointer"
                        title="Isi otomatis kalender libur nasional standar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Muat Libur Standar {{ $year }}
                    </button>
                </form>
                <button type="button" onclick="openImportModal()"
                    class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold rounded-2xl text-xs flex items-center gap-2 backdrop-blur-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Impor CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Terdaftar ({{ $year }})</span>
                <span class="w-7 h-7 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">📅</span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Hari / agenda libur</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Libur Nasional</span>
                <span class="w-7 h-7 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">🔴</span>
            </div>
            <div class="text-2xl font-black text-rose-600 mt-2">{{ $stats['nasional'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Tanggal merah resmi</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Cuti Bersama / Khusus</span>
                <span class="w-7 h-7 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">🟡</span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['khusus'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Keagamaan / khusus</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Libur Sekolah</span>
                <span class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">🔵</span>
            </div>
            <div class="text-2xl font-black text-indigo-600 mt-2">{{ $stats['sekolah'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Semester & KBM off</p>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('master.holidays') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-medium focus:bg-white focus:border-indigo-500 focus:outline-none" onchange="this.form.submit()">
                    @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Bulan</label>
                <select name="month" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-medium focus:bg-white focus:border-indigo-500 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Jenis Libur</label>
                <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-medium focus:bg-white focus:border-indigo-500 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="nasional" {{ $type === 'nasional' ? 'selected' : '' }}>Libur Nasional</option>
                    <option value="khusus" {{ $type === 'khusus' ? 'selected' : '' }}>Cuti Bersama / Khusus</option>
                    <option value="sekolah" {{ $type === 'sekolah' ? 'selected' : '' }}>Libur Sekolah / Semester</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Pencarian Nama</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama libur..." class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl flex items-center justify-center gap-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('master.holidays', ['year' => $year]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl flex items-center justify-center" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Holidays -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-bold text-sm text-slate-900">Daftar Tanggal Merah & Hari Libur</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">{{ $holidays->count() }} Data</span>
            </div>
            <a href="{{ route('master.holidays.template') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Template CSV
            </a>
        </div>

        @if($holidays->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-3xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-2xl mb-3 shadow-inner">
                    📅
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Hari Libur Terdaftar</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Belum ada data hari libur untuk filter yang dipilih. Anda dapat menambahkan hari libur manual atau klik tombol "Muat Libur Standar".
                </p>
                <div class="mt-4 flex items-center justify-center gap-3">
                    <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition cursor-pointer">
                        + Tambah Hari Libur
                    </button>
                    <form action="{{ route('master.holidays.generate_common') }}" method="POST">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs transition cursor-pointer">
                            Muat Libur Standar {{ $year }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4">Tanggal / Rentang</th>
                            <th class="py-3.5 px-4">Nama Hari Libur</th>
                            <th class="py-3.5 px-4">Jenis</th>
                            <th class="py-3.5 px-4">Keterangan</th>
                            <th class="py-3.5 px-4 text-center">Status Absensi</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($holidays as $index => $item)
                        @php
                            $isToday = \Carbon\Carbon::today()->between($item->start_date, $item->end_date);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition {{ $isToday ? 'bg-amber-50/40' : '' }}">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>{{ $item->formatted_range }}</span>
                                    @if($isToday)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white animate-pulse">HARI INI</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 font-medium">
                                    {{ $item->days_count }} Hari ({{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('l') }})
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900">{{ $item->name }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($item->type === 'nasional')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Libur Nasional
                                    </span>
                                @elseif($item->type === 'khusus')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Cuti Bersama
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span> Libur Sekolah
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600 truncate">
                                {{ $item->description ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        🔒 Absensi Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                                        Tidak Berlaku
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="openEditModal({{ json_encode($item) }})"
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('master.holidays.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus hari libur {{ $item->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Hari Libur -->
<div id="modal-add-holiday" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>📅</span> Tambah Hari Libur / Tanggal Merah
            </h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-1">
                ✕
            </button>
        </div>
        <form action="{{ route('master.holidays.store') }}" method="POST" class="space-y-4 mt-5">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Hari Libur <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Hari Raya Idul Fitri 1447 H" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="add_start_date" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none" onchange="syncEndDate('add')">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Selesai <span class="text-slate-400 text-[10px]">(Opsional)</span></label>
                    <input type="date" name="end_date" id="add_end_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori / Jenis Libur <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:outline-none">
                    <option value="nasional">🔴 Libur Nasional (Tanggal Merah Resmi Pemerintah)</option>
                    <option value="khusus">🟡 Cuti Bersama / Libur Khusus Keagamaan</option>
                    <option value="sekolah">🔵 Libur Sekolah / Semester (Kalender Pendidikan)</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                <textarea name="description" rows="2" placeholder="Catatan opsional mengenai agenda libur..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                <label for="add_is_active" class="font-semibold text-slate-700 text-xs">Aktifkan (Absensi otomatis nonaktif saat tanggal merah)</label>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Hari Libur
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Hari Libur -->
<div id="modal-edit-holiday" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>✏️</span> Edit Data Hari Libur
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
                ✕
            </button>
        </div>
        <form id="form-edit-holiday" method="POST" class="space-y-4 mt-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Hari Libur <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="edit_start_date" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="edit_end_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori / Jenis Libur <span class="text-rose-500">*</span></label>
                <select name="type" id="edit_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:outline-none">
                    <option value="nasional">🔴 Libur Nasional</option>
                    <option value="khusus">🟡 Cuti Bersama / Khusus</option>
                    <option value="sekolah">🔵 Libur Sekolah / Semester</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                <label for="edit_is_active" class="font-semibold text-slate-700 text-xs">Aktifkan (Absensi otomatis nonaktif)</label>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modal-import-holiday" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>📥</span> Impor Hari Libur dari CSV
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 p-1">
                ✕
            </button>
        </div>
        <form action="{{ route('master.holidays.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-5">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File CSV <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full p-2 border border-slate-200 rounded-xl">
                <p class="text-[11px] text-slate-400 mt-1">Gunakan template resmi agar format kolom terdeteksi otomatis.</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                <div class="font-bold text-slate-700">Format Kolom Header:</div>
                <div class="text-[11px] font-mono text-slate-600">nama_hari_libur, tanggal_mulai, tanggal_selesai, jenis, keterangan</div>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Unggah & Impor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('modal-add-holiday').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('modal-add-holiday').classList.add('hidden');
    }
    function syncEndDate(prefix) {
        const start = document.getElementById(prefix + '_start_date').value;
        const end = document.getElementById(prefix + '_end_date');
        if (start && !end.value) {
            end.value = start;
        }
    }
    function openEditModal(data) {
        const form = document.getElementById('form-edit-holiday');
        form.action = "{{ url('/master/holidays') }}/" + data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_start_date').value = data.start_date ? data.start_date.substring(0, 10) : '';
        document.getElementById('edit_end_date').value = data.end_date ? data.end_date.substring(0, 10) : '';
        document.getElementById('edit_type').value = data.type || 'nasional';
        document.getElementById('edit_description').value = data.description || '';
        document.getElementById('edit_is_active').checked = Boolean(data.is_active);
        document.getElementById('modal-edit-holiday').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('modal-edit-holiday').classList.add('hidden');
    }
    function openImportModal() {
        document.getElementById('modal-import-holiday').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('modal-import-holiday').classList.add('hidden');
    }
</script>
@endpush
