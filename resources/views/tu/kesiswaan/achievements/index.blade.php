@extends('layouts.app')

@section('title', 'Siswa Berprestasi - Adm. Kesiswaan')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tu.kesiswaan.index') }}" class="hover:text-blue-600 font-medium">Adm. Kesiswaan</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Siswa Berprestasi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Direktori Siswa Berprestasi &amp; Penghargaan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Sinkron otomatis dari aplikasi Kesiswaan - Tata Tertib Siswa (Poin Apresiasi, Akademik &amp; Non-Akademik)</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('tu.kesiswaan.achievements.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekap Prestasi</span>
            </a>
        </div>
    </div>

    <!-- Statistik Prestasi Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400">Total Prestasi</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">{{ number_format($stats['total_points']) }} Total Poin</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-purple-200/80 dark:border-purple-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-purple-600 dark:text-purple-400">Internasional</span>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['internasional']) }}</div>
            <span class="text-[10px] text-purple-500 font-semibold">Tingkat Dunia</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-blue-200/80 dark:border-blue-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">Nasional</span>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['nasional']) }}</div>
            <span class="text-[10px] text-blue-500 font-semibold">Tingkat Nasional</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Provinsi</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['provinsi']) }}</div>
            <span class="text-[10px] text-emerald-500 font-semibold">Jawa Barat</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Kabupaten/Kota</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['kabupaten']) }}</div>
            <span class="text-[10px] text-amber-500 font-semibold">Sukabumi</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500">Sekolah</span>
            <div class="text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ number_format($stats['sekolah']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">Internal SMAN 1</span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form action="{{ route('tu.kesiswaan.achievements.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Siswa / Judul Prestasi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / Prestasi..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Prestasi</label>
                <select name="level" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Tingkat --</option>
                    <option value="internasional" {{ request('level') === 'internasional' ? 'selected' : '' }}>Internasional</option>
                    <option value="nasional" {{ request('level') === 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="provinsi" {{ request('level') === 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                    <option value="kabupaten" {{ request('level') === 'kabupaten' ? 'selected' : '' }}>Kabupaten / Kota</option>
                    <option value="sekolah" {{ request('level') === 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas / Rombel</label>
                <select name="class_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">Filter</button>
                <a href="{{ route('tu.kesiswaan.achievements.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Siswa Berprestasi -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Daftar Prestasi &amp; Penghargaan Siswa</h3>
                <p class="text-xs text-slate-400 mt-0.5">Data terverifikasi resmi oleh Pembina Kesiswaan &amp; Guru</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                Mode: Tampil &amp; Cetak Resmi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa &amp; NISN</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Nama Prestasi / Kegiatan</th>
                        <th class="py-3 px-4">Bidang / Kategori</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4 text-center">Poin</th>
                        <th class="py-3 px-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($achievements as $index => $ach)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">{{ $achievements->firstItem() + $index }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $ach->student?->name ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $ach->student?->nisn ?: '-' }}</div>
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $ach->student?->schoolClass?->name ?? ($ach->schoolClass?->name ?? '-') }}
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $ach->title }}
                            @if($ach->description)
                                <div class="text-[11px] text-slate-400 font-normal">{{ $ach->description }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $ach->category?->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($ach->level === 'internasional') bg-purple-100 text-purple-800
                                @elseif($ach->level === 'nasional') bg-blue-100 text-blue-800
                                @elseif($ach->level === 'provinsi') bg-emerald-100 text-emerald-800
                                @elseif($ach->level === 'kabupaten') bg-amber-100 text-amber-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $ach->level }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-emerald-600">
                            +{{ $ach->points }}
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $ach->date?->translatedFormat('d F Y') ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            Belum ada catatan siswa berprestasi yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($achievements->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $achievements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
