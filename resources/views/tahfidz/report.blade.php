@extends('layouts.app')

@section('title', "Dokumen Laporan Setoran Hafalan Al-Qur'an (Tahfidz Juz 30)")

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <a href="{{ route('tahfidz.index') }}" class="hover:underline flex items-center gap-1">
                    <span>← Kembali ke Buku Kontrol Tahfidz</span>
                </a>
            </div>
            <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Dokumen Laporan Setoran Hafalan Al-Qur'an Juz 30
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Rekapitulasi berkala progres setor hafalan Al-Qur'an siswa SMA Negeri 1 Lengkong.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tahfidz.custom_progress') }}"
                class="px-3.5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-md shadow-teal-600/30 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan Cicilan & Progres Siswa</span>
            </a>
            <a href="{{ route('tahfidz.print', request()->query()) }}" target="_blank"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Dokumen Resmi (A4 KOP)</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <form method="GET" action="{{ route('tahfidz.report') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @php
                $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
            @endphp
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan Rombel Kelas</label>
                <select name="class_id" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none font-bold">
                    @if(!$isWaliKelasUser)
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
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Hasil Kelulusan</label>
                <select name="grade" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
                    <option value="">Semua Hasil</option>
                    <option value="Lulus" {{ $gradeFilter == 'Lulus' ? 'selected' : '' }}>Lulus (Mutqin)</option>
                    <option value="Mengulang" {{ $gradeFilter == 'Mengulang' ? 'selected' : '' }}>Mengulang</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('tahfidz.report') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Setoran</span>
            <div class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-emerald-500/20 text-center">
            <span class="text-[10px] font-bold text-emerald-500 uppercase">Lulus</span>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ number_format($stats['lulus']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-500/20 text-center">
            <span class="text-[10px] font-bold text-amber-500 uppercase">Mengulang</span>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ number_format($stats['mengulang']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-indigo-500/20 text-center">
            <span class="text-[10px] font-bold text-indigo-500 uppercase">Siswa Terdata</span>
            <div class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5">{{ number_format($stats['students_count']) }}</div>
        </div>
    </div>

    <!-- Table of Records -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-400 font-bold uppercase text-[10px]">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal Setor</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Surat & Ayat</th>
                        <th class="py-3 px-4">Makhraj / Tajwid / Kelancaran</th>
                        <th class="py-3 px-4">Hasil</th>
                        <th class="py-3 px-4">Penguji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($records as $idx => $r)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $records->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($r->deposit_date)->locale('id')->translatedFormat('d M Y') }}
                                <div class="text-[10px] text-slate-400">{{ substr($r->deposit_time, 0, 5) }} WIB</div>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('tahfidz.student', $r->student_id) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                    {{ $r->student?->name }}
                                </a>
                                <div class="text-[10px] text-slate-400">NISN: {{ $r->student?->nisn }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $r->student?->schoolClass?->name ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="font-extrabold text-slate-800 dark:text-slate-200">📖 {{ $r->surah_name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $r->ayat_range }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-[10px] block">M: <strong>{{ $r->makhraj }}</strong></span>
                                <span class="text-[10px] block">T: <strong>{{ $r->tajwid }}</strong></span>
                                <span class="text-[10px] block">K: <strong>{{ $r->kelancaran }}</strong></span>
                            </td>
                            <td class="py-3 px-4">
                                @if($r->overall_grade === 'Lulus')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                        ✓ LULUS
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                        ↻ MENGULANG
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $r->examiner_name }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada catatan setoran hafalan yang sesuai filter.
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
@endsection
