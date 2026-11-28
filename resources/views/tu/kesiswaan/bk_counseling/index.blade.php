@extends('layouts.app')

@section('title', 'Layanan Konseling & Peminatan Siswa - Adm. Kesiswaan')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tu.kesiswaan.index') }}" class="hover:text-blue-600 font-medium">Adm. Kesiswaan</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Layanan BK &amp; Konseling</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Rekapitulasi Layanan Konseling &amp; Peminatan Siswa</h1>
            <p class="text-xs text-slate-400 mt-0.5">Integrasi informasi operasional konseling pribadi, sosial, belajar, dan penelusuran karier &amp; studi lanjut siswa (Sumber: Bimbingan Konseling)</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('tu.kesiswaan.bk_counseling.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekap Konseling</span>
            </a>
        </div>
    </div>

    <!-- Statistik Layanan BK Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400">Total Sesi Terlaksana</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">Sesi Konseling</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Bimbingan Karier</span>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($stats['karier']) }}</div>
            <span class="text-[10px] text-indigo-500 font-semibold">Kuliah / Kerja</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Bimbingan Belajar</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['belajar']) }}</div>
            <span class="text-[10px] text-emerald-500 font-semibold">Akademik &amp; KBM</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-purple-200/80 dark:border-purple-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-purple-600 dark:text-purple-400">Konseling Pribadi</span>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['pribadi']) }}</div>
            <span class="text-[10px] text-purple-500 font-semibold">Karakter &amp; Diri</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Bimbingan Sosial</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['sosial']) }}</div>
            <span class="text-[10px] text-amber-500 font-semibold">Relasi Teman Sebaya</span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form action="{{ route('tu.kesiswaan.bk_counseling.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Siswa / Topik Konseling</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / Topik / Kampus..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan</label>
                <select name="field_of_service" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Bidang --</option>
                    <option value="Bimbingan Karier" {{ request('field_of_service') === 'Bimbingan Karier' ? 'selected' : '' }}>Bimbingan Karier</option>
                    <option value="Bimbingan Belajar" {{ request('field_of_service') === 'Bimbingan Belajar' ? 'selected' : '' }}>Bimbingan Belajar</option>
                    <option value="Bimbingan Pribadi" {{ request('field_of_service') === 'Bimbingan Pribadi' ? 'selected' : '' }}>Bimbingan Pribadi</option>
                    <option value="Bimbingan Sosial" {{ request('field_of_service') === 'Bimbingan Sosial' ? 'selected' : '' }}>Bimbingan Sosial</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Sesi</label>
                <select name="session_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Jenis --</option>
                    <option value="INDIVIDUAL" {{ request('session_type') === 'INDIVIDUAL' ? 'selected' : '' }}>Individual</option>
                    <option value="KELOMPOK" {{ request('session_type') === 'KELOMPOK' ? 'selected' : '' }}>Kelompok</option>
                    <option value="KLASIKAL" {{ request('session_type') === 'KLASIKAL' ? 'selected' : '' }}>Klasikal</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">Filter</button>
                <a href="{{ route('tu.kesiswaan.bk_counseling.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Konseling BK -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Daftar Sesi Layanan Bimbingan &amp; Konseling Terlaksana</h3>
                <p class="text-xs text-slate-400 mt-0.5">Dokumentasi operasional konseling siswa terverifikasi</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                Mode: Tampil &amp; Cetak Resmi (Read-Only)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa &amp; Kelas</th>
                        <th class="py-3 px-4">Bidang Layanan</th>
                        <th class="py-3 px-4">Peminatan Karier / Studi</th>
                        <th class="py-3 px-4">Topik Sesi</th>
                        <th class="py-3 px-4">Konselor (Guru BK)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($sessions as $index => $sess)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">{{ $sessions->firstItem() + $index }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $sess->student?->name ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400 font-semibold">{{ $sess->student?->schoolClass?->name ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ $sess->field_of_service }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $sess->session_type }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @if($sess->career_path)
                                <div class="font-bold text-emerald-600 dark:text-emerald-400">{{ $sess->career_path }}</div>
                                @if($sess->campus_name)<div class="text-[11px] text-slate-500">{{ $sess->campus_name }} ({{ $sess->study_program ?: '-' }})</div>@endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            {{ $sess->topic }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $sess->counselor?->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $sess->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $sess->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $sess->date?->translatedFormat('d F Y') ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            Belum ada rekap sesi konseling BK yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
