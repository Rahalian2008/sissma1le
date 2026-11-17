@extends('layouts.app')

@section('title', 'Rekapitulasi Kehadiran Siswa - Adm. Kesiswaan')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-blue-700 to-sky-700 p-5 sm:p-7 text-white shadow-xl shadow-indigo-600/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-200">
                    <a href="{{ route('tu.dashboard') }}" class="hover:underline">Adm. Tata Usaha</a>
                    <span>/</span>
                    <a href="{{ route('tu.kesiswaan.index') }}" class="hover:underline">Kesiswaan</a>
                    <span>/</span>
                    <span>Rekap Presensi Siswa</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight flex items-center gap-2.5">
                    <span>📊 Rekapitulasi Kehadiran Siswa</span>
                </h1>
                <p class="text-xs sm:text-sm text-sky-100 max-w-2xl leading-relaxed">
                    Laporan komprehensif kehadiran & presensi harian peserta didik {{ $school?->name ?? 'SMA Negeri 1 Lengkong' }}. Mendukung filter rentang tanggal kustom, rekapitulasi semua rombel tanpa nama, dan rincian per rombel.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tu.kesiswaan.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Kesiswaan</span>
                </a>
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 sm:px-4.5 sm:py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-lg shadow-black/10 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan / PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mode Switcher Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-2 sm:p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none w-full sm:w-auto">
            <a href="{{ route('tu.kesiswaan.rekap_presensi.index', array_merge(request()->query(), ['mode' => 'all_rombels'])) }}" 
               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition whitespace-nowrap {{ $mode === 'all_rombels' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏫 Semua Rombel (Tanpa Nama)</span>
                @if($mode === 'all_rombels')
                    <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                @endif
            </a>
            <a href="{{ route('tu.kesiswaan.rekap_presensi.index', array_merge(request()->query(), ['mode' => 'per_rombel', 'class_id' => $selectedClassId])) }}" 
               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition whitespace-nowrap {{ $mode === 'per_rombel' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>👥 Per Rombel (Detail Siswa)</span>
                @if($mode === 'per_rombel')
                    <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                @endif
            </a>
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400 px-2 font-medium flex items-center gap-2">
            <span>Periode Data:</span>
            <strong class="text-slate-800 dark:text-slate-200">{{ $startDate->translatedFormat('d M Y') }} - {{ $endDate->translatedFormat('d M Y') }}</strong>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-4 sm:p-5">
        <form action="{{ route('tu.kesiswaan.rekap_presensi.index') }}" method="GET" class="space-y-4">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Tanggal Mulai -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                </div>

                <!-- Tanggal Akhir -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Tanggal Akhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                </div>

                <!-- Filter Tingkat Kelas -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Tingkat / Fase
                    </label>
                    <select name="grade" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                        <option value="ALL" {{ $grade === 'ALL' ? 'selected' : '' }}>Semua Tingkat (X, XI, XII)</option>
                        <option value="X" {{ $grade === 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
                        <option value="XI" {{ $grade === 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
                        <option value="XII" {{ $grade === 'XII' ? 'selected' : '' }}>Kelas XII (Fase F Tingkat Akhir)</option>
                    </select>
                </div>

                <!-- Dropdown Pilih Rombel (Mode Per Rombel) -->
                <div class="space-y-1 {{ $mode === 'per_rombel' ? 'block' : 'opacity-70' }}">
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Rombongan Belajar (Kelas)
                    </label>
                    <select name="class_id" {{ $mode === 'per_rombel' ? 'required' : '' }} onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }} (Tingkat {{ $c->grade }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tombol Preset Waktu & Submit -->
            <div class="flex flex-wrap items-center justify-between gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-slate-400 font-bold text-[11px] mr-1">Preset Cepat:</span>
                    <button type="button" onclick="setPreset('today')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setPreset('7days')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setPreset('month')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition">
                        Bulan Ini
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs flex items-center gap-1.5 shadow-md shadow-indigo-600/20 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistik Ringkasan Cards -->
    @php
        $stats = $mode === 'all_rombels' ? $schoolTotal : $rombelSummary;
        $labelSubtitle = $mode === 'all_rombels' ? 'Semua Rombongan Belajar' : ($selectedClass ? $selectedClass->name : 'Rombel');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- 1. Total Siswa -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Total Siswa</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['total_students']) }}</div>
            <div class="text-[10.5px] text-indigo-600 dark:text-indigo-400 mt-0.5 font-bold truncate">{{ $labelSubtitle }}</div>
        </div>

        <!-- 2. Hadir -->
        <div class="bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/50 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">Hadir Total</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['hadir']) }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium truncate">
                Tepat: {{ $stats['tepat_waktu'] }} • Telat: {{ $stats['terlambat'] }}
            </div>
        </div>

        <!-- 3. Sakit -->
        <div class="bg-white dark:bg-slate-900 border border-sky-200 dark:border-sky-900/50 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-sky-700 dark:text-sky-400">Sakit (S)</span>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ number_format($stats['sakit']) }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Surat Dokter/Ortu</div>
        </div>

        <!-- 4. Izin -->
        <div class="bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-900/50 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400">Izin (I)</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['izin']) }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Pengajuan Terverifikasi</div>
        </div>

        <!-- 5. Alpa -->
        <div class="bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-900/50 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">Alpa (A)</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['alpa']) }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Tanpa Keterangan</div>
        </div>

        <!-- 6. Persentase Kehadiran -->
        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 text-white p-4 rounded-2xl shadow-md">
            <span class="text-[11px] font-bold text-indigo-100">% Kehadiran</span>
            <div class="text-2xl font-black mt-1">{{ $stats['percentage'] }}%</div>
            <div class="text-[10px] text-indigo-200 mt-0.5 font-bold">
                {{ $stats['percentage'] >= 90 ? '🌟 Sangat Baik' : ($stats['percentage'] >= 80 ? '👍 Baik' : ($stats['percentage'] >= 70 ? '⚠️ Cukup' : '🔴 Perlu Evaluasi')) }}
            </div>
        </div>
    </div>

    @if($mode === 'all_rombels')
    <!-- MODE A: TABEL REKAPITULASI SEMUA ROMBEL TANPA NAMA -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-sm sm:text-base text-slate-800 dark:text-white flex items-center gap-2">
                    <span>Rekapitulasi Kehadiran Seluruh Rombel</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300">
                        {{ count($rombelsRecap) }} Rombel Terdata
                    </span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Data agregat presensi harian per kelas tanpa mencantumkan nama siswa individu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['mode' => 'all_rombels'])) }}" target="_blank"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap Rombel</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3 px-3.5 text-center w-12">No</th>
                        <th class="py-3 px-4">Kelas / Rombel</th>
                        <th class="py-3 px-3 text-center">Tingkat</th>
                        <th class="py-3 px-4">Wali Kelas</th>
                        <th class="py-3 px-3 text-center">Jml Siswa</th>
                        <th class="py-3 px-3 text-center bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300">Hadir (H)</th>
                        <th class="py-3 px-3 text-center bg-sky-50/50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-300">Sakit (S)</th>
                        <th class="py-3 px-3 text-center bg-amber-50/50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300">Izin (I)</th>
                        <th class="py-3 px-3 text-center bg-rose-50/50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300">Alpa (A)</th>
                        <th class="py-3 px-3 text-center text-amber-600 dark:text-amber-400">Terlambat</th>
                        <th class="py-3 px-3 text-center">Total Catatan</th>
                        <th class="py-3 px-3 text-center bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300">% Kehadiran</th>
                        <th class="py-3 px-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($rombelsRecap as $idx => $row)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-3.5 text-center font-bold text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-black text-slate-900 dark:text-white">
                                {{ $row['class']->name }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $row['class']->grade }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 dark:text-slate-300 font-medium">
                                {{ $row['class']->homeroomTeacher?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-800 dark:text-slate-200">
                                {{ $row['student_count'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-950/10">
                                {{ $row['hadir'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-sky-600 dark:text-sky-400 bg-sky-50/30 dark:bg-sky-950/10">
                                {{ $row['sakit'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-amber-600 dark:text-amber-400 bg-amber-50/30 dark:bg-amber-950/10">
                                {{ $row['izin'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-rose-600 dark:text-rose-400 bg-rose-50/30 dark:bg-rose-950/10">
                                {{ $row['alpa'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-medium text-amber-600 dark:text-amber-400">
                                {{ $row['terlambat'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-600 dark:text-slate-400">
                                {{ $row['total_records'] }}
                            </td>
                            <td class="py-3 px-3 text-center bg-indigo-50/30 dark:bg-indigo-950/10">
                                <span class="px-2 py-0.5 rounded-full font-black text-[11px] {{ $row['percentage'] >= 90 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : ($row['percentage'] >= 80 ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : ($row['percentage'] >= 70 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300')) }}">
                                    {{ $row['percentage'] }}%
                                </span>
                            </td>
                            <td class="py-3 px-3.5 text-center">
                                <a href="{{ route('tu.kesiswaan.rekap_presensi.index', array_merge(request()->query(), ['mode' => 'per_rombel', 'class_id' => $row['class']->id])) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 font-bold text-[10.5px] transition" title="Lihat detail nama siswa rombel ini">
                                    <span>Rincian</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-8 text-center text-slate-400">
                                Tidak ada data rombel untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 dark:bg-slate-800 font-black text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700">
                        <td colspan="4" class="py-3.5 px-4 text-right uppercase text-[11px] tracking-wider">
                            GRAND TOTAL SELURUH ROMBEL:
                        </td>
                        <td class="py-3.5 px-3 text-center">{{ number_format($schoolTotal['total_students']) }}</td>
                        <td class="py-3.5 px-3 text-center text-emerald-600 dark:text-emerald-400 bg-emerald-100/50 dark:bg-emerald-950/40">{{ number_format($schoolTotal['hadir']) }}</td>
                        <td class="py-3.5 px-3 text-center text-sky-600 dark:text-sky-400 bg-sky-100/50 dark:bg-sky-950/40">{{ number_format($schoolTotal['sakit']) }}</td>
                        <td class="py-3.5 px-3 text-center text-amber-600 dark:text-amber-400 bg-amber-100/50 dark:bg-amber-950/40">{{ number_format($schoolTotal['izin']) }}</td>
                        <td class="py-3.5 px-3 text-center text-rose-600 dark:text-rose-400 bg-rose-100/50 dark:bg-rose-950/40">{{ number_format($schoolTotal['alpa']) }}</td>
                        <td class="py-3.5 px-3 text-center text-amber-600 dark:text-amber-400">{{ number_format($schoolTotal['terlambat']) }}</td>
                        <td class="py-3.5 px-3 text-center">{{ number_format($schoolTotal['total_records']) }}</td>
                        <td class="py-3.5 px-3 text-center bg-indigo-100/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">
                            {{ $schoolTotal['percentage'] }}%
                        </td>
                        <td class="py-3.5 px-3.5 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <!-- MODE B: TABEL REKAPITULASI PER ROMBEL DENGAN DETAIL SISWA -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Rombel Detail Header Banner -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/30">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300">
                        Laporan Per Rombel
                    </span>
                    <h3 class="font-black text-base sm:text-lg text-slate-800 dark:text-white">
                        {{ $selectedClass?->name ?? 'Pilih Rombel' }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Wali Kelas: <strong class="text-slate-700 dark:text-slate-200">{{ $selectedClass?->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</strong>
                    @if($selectedClass?->homeroomTeacher?->nip)
                        (NIP: {{ $selectedClass->homeroomTeacher->nip }})
                    @endif
                    • Total Siswa: <strong class="text-slate-700 dark:text-slate-200">{{ count($studentsRecap) }} Siswa</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tu.kesiswaan.rekap_presensi.print', array_merge(request()->query(), ['mode' => 'per_rombel', 'class_id' => $selectedClassId])) }}" target="_blank"
                    class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Kelas Ini</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-3.5">NIS / NISN</th>
                        <th class="py-3 px-4">Nama Lengkap Siswa</th>
                        <th class="py-3 px-2.5 text-center">L/P</th>
                        <th class="py-3 px-3 text-center bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300">Hadir (H)</th>
                        <th class="py-3 px-3 text-center bg-sky-50/50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-300">Sakit (S)</th>
                        <th class="py-3 px-3 text-center bg-amber-50/50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300">Izin (I)</th>
                        <th class="py-3 px-3 text-center bg-rose-50/50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300">Alpa (A)</th>
                        <th class="py-3 px-3 text-center text-amber-600 dark:text-amber-400">Terlambat</th>
                        <th class="py-3 px-3 text-center">Total Terdata</th>
                        <th class="py-3 px-3 text-center bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300">% Hadir</th>
                        <th class="py-3 px-3.5 text-center">Predikat / Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($studentsRecap as $idx => $sRow)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-2.5 px-3 text-center font-bold text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $sRow['student']->nis ?? '-' }} / {{ $sRow['student']->nisn ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $sRow['student']->name }}
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-semibold text-slate-600 dark:text-slate-300">
                                {{ $sRow['student']->gender ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-950/10">
                                {{ $sRow['hadir'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-sky-600 dark:text-sky-400 bg-sky-50/30 dark:bg-sky-950/10">
                                {{ $sRow['sakit'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-amber-600 dark:text-amber-400 bg-amber-50/30 dark:bg-amber-950/10">
                                {{ $sRow['izin'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-rose-600 dark:text-rose-400 bg-rose-50/30 dark:bg-rose-950/10">
                                {{ $sRow['alpa'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-medium text-amber-600 dark:text-amber-400">
                                {{ $sRow['terlambat'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-slate-600 dark:text-slate-400">
                                {{ $sRow['total_records'] }}
                            </td>
                            <td class="py-2.5 px-3 text-center bg-indigo-50/30 dark:bg-indigo-950/10 font-black">
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] {{ $sRow['percentage'] >= 90 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : ($sRow['percentage'] >= 80 ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : ($sRow['percentage'] >= 70 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300')) }}">
                                    {{ $sRow['percentage'] }}%
                                </span>
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                @if($sRow['total_records'] === 0)
                                    <span class="text-[10px] text-slate-400">Belum Ada Presensi</span>
                                @elseif($sRow['percentage'] >= 90)
                                    <span class="text-[10.5px] font-bold text-emerald-600 dark:text-emerald-400">Sangat Baik (A)</span>
                                @elseif($sRow['percentage'] >= 80)
                                    <span class="text-[10.5px] font-bold text-blue-600 dark:text-blue-400">Baik (B)</span>
                                @elseif($sRow['percentage'] >= 70)
                                    <span class="text-[10.5px] font-bold text-amber-600 dark:text-amber-400">Cukup (C)</span>
                                @else
                                    <span class="text-[10.5px] font-bold text-rose-600 dark:text-rose-400">Perlu Pembinaan (D)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-8 text-center text-slate-400">
                                Tidak ada siswa terdaftar pada rombel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 dark:bg-slate-800 font-black text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700">
                        <td colspan="4" class="py-3.5 px-4 text-right uppercase text-[11px] tracking-wider">
                            TOTAL REKAPITULASI KELAS {{ $selectedClass?->name }}:
                        </td>
                        <td class="py-3.5 px-3 text-center text-emerald-600 dark:text-emerald-400 bg-emerald-100/50 dark:bg-emerald-950/40">{{ number_format($rombelSummary['hadir']) }}</td>
                        <td class="py-3.5 px-3 text-center text-sky-600 dark:text-sky-400 bg-sky-100/50 dark:bg-sky-950/40">{{ number_format($rombelSummary['sakit']) }}</td>
                        <td class="py-3.5 px-3 text-center text-amber-600 dark:text-amber-400 bg-amber-100/50 dark:bg-amber-950/40">{{ number_format($rombelSummary['izin']) }}</td>
                        <td class="py-3.5 px-3 text-center text-rose-600 dark:text-rose-400 bg-rose-100/50 dark:bg-rose-950/40">{{ number_format($rombelSummary['alpa']) }}</td>
                        <td class="py-3.5 px-3 text-center text-amber-600 dark:text-amber-400">{{ number_format($rombelSummary['terlambat']) }}</td>
                        <td class="py-3.5 px-3 text-center">{{ number_format($rombelSummary['total_records']) }}</td>
                        <td class="py-3.5 px-3 text-center bg-indigo-100/50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">
                            {{ $rombelSummary['percentage'] }}%
                        </td>
                        <td class="py-3.5 px-3.5 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function setPreset(type) {
        const today = new Date();
        const formatDate = (d) => {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        };

        const startInput = document.querySelector('input[name="start_date"]');
        const endInput = document.querySelector('input[name="end_date"]');

        if (type === 'today') {
            const tStr = formatDate(today);
            startInput.value = tStr;
            endInput.value = tStr;
        } else if (type === '7days') {
            const past7 = new Date();
            past7.setDate(today.getDate() - 6);
            startInput.value = formatDate(past7);
            endInput.value = formatDate(today);
        } else if (type === 'month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(today);
        }
    }
</script>
@endpush
@endsection
