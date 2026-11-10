@extends('layouts.app')

@section('title', 'Dokumen Laporan - ' . $config['title'])

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-1">
                <a href="{{ route('worship.index', $activity) }}" class="hover:underline flex items-center gap-1">
                    <span>← Kembali ke Sesi {{ $config['title'] }}</span>
                </a>
            </div>
            <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Dokumen Laporan {{ $config['title'] }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Rekapitulasi berkala kehadiran siswa (Target: {{ $config['gender_label'] }}).
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openPrintWorshipModal()"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Dokumen Resmi (A4 KOP)</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <form method="GET" action="{{ route('worship.report', $activity) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @php
                $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
            @endphp
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan Rombel Kelas</label>
                <select name="class_id" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none font-bold">
                    @if(!$isWaliKelasUser && !Auth::user()->hasRole('sekretaris_kelas'))
                        <option value="">Semua Kelas</option>
                    @endif
                    @forelse($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                    @empty
                        <option value="">Belum ada kelas binaan</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Kehadiran</label>
                <select name="status" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
                    <option value="">Semua Status</option>
                    <option value="HADIR" {{ $statusFilter == 'HADIR' ? 'selected' : '' }}>Hadir</option>
                    <option value="IZIN" {{ $statusFilter == 'IZIN' ? 'selected' : '' }}>Izin</option>
                    <option value="SAKIT" {{ $statusFilter == 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                    <option value="UDZUR" {{ $statusFilter == 'UDZUR' ? 'selected' : '' }}>Udzur</option>
                    <option value="ALPA" {{ $statusFilter == 'ALPA' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('worship.report', $activity) }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Summary Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Log</span>
            <div class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-emerald-500/20 text-center">
            <span class="text-[10px] font-bold text-emerald-500 uppercase">Hadir</span>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ number_format($stats['hadir']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-500/20 text-center">
            <span class="text-[10px] font-bold text-amber-500 uppercase">Terlambat</span>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ number_format($stats['terlambat']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-sky-500/20 text-center">
            <span class="text-[10px] font-bold text-sky-500 uppercase">Izin / Udzur</span>
            <div class="text-xl font-black text-sky-600 dark:text-sky-400 mt-0.5">{{ number_format($stats['izin_udzur']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-rose-500/20 text-center">
            <span class="text-[10px] font-bold text-rose-500 uppercase">Alpa</span>
            <div class="text-xl font-black text-rose-600 dark:text-rose-400 mt-0.5">{{ number_format($stats['alpa']) }}</div>
        </div>
    </div>

    <!-- Table of Records -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-400 font-bold uppercase text-[10px]">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Hari & Tanggal</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jam Log</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Pencatat / Verifikator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($records as $idx => $r)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $records->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($r->date)->locale('id')->translatedFormat('l, d M Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $r->student?->name }}</div>
                                <div class="text-[10px] text-slate-400">NISN: {{ $r->student?->nisn }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $r->student?->schoolClass?->name ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono font-bold">{{ substr($r->time, 0, 5) }} WIB</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($r->method === 'SELFIE') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                    @elseif(in_array($r->method, ['BARCODE', 'QR_CODE'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                    @elseif($r->method === 'RFID') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                    @elseif($r->method === 'FACE_RECOGNITION') bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300
                                    @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 @endif">
                                    {{ $r->method }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold
                                    @if($r->status === 'HADIR') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30
                                    @elseif($r->status === 'TERLAMBAT') bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30
                                    @elseif(in_array($r->status, ['IZIN', 'SAKIT', 'UDZUR'])) bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30
                                    @else bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30 @endif">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $r->recorder?->name ?? 'Sistem Otomatis' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada catatan presensi yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>

@include('worship.partials.print_modal')
@endsection
