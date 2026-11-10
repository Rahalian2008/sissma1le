@extends('layouts.app')

@section('title', 'Laporan Kustom Progres & Cicilan Hafalan Juz 30 Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 sm:p-8 rounded-3xl text-white shadow-xl border border-emerald-500/20">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    @if(!empty($targetGradeInfo))
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            📖 Target Kurikulum {{ $targetGradeInfo['label'] }} ({{ $targetGradeInfo['total_surahs'] }} Surat / {{ $targetGradeInfo['total_ayat'] }} Ayat)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            📖 Pengelompokan Surat Sesuai Tingkat Kelas (X: 25 Surat, XI: 7 Surat, XII: 5 Surat)
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        ⚡ Sistem Setoran Cicilan (Angsuran Ayat)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Laporan Kustom Progres & Cicilan Hafalan Siswa
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Pantau rincian perkembangan hafalan siswa ayat demi ayat secara transparan. Mengetahui surat yang telah tuntas 100%, surat yang sedang dicicil sebagian, ayat berapa saja yang sudah dan belum diselesaikan, serta sisa tanggungan hafalan di Juz 30.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                @if(Auth::user()->canManageTahfidz())
                    <a href="{{ route('tahfidz.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Setor Cicilan</span>
                    </a>
                @endif
                <a href="{{ route('tahfidz.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda Tahfidz</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <form method="GET" action="{{ route('tahfidz.custom_progress') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <label for="class_id" class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">
                    Filter Rombel / Kelas
                </label>
                <select name="class_id" id="class_id" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 font-semibold">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }} ({{ $c->academicYear?->name ?? 'Aktif' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-5">
                <label for="search" class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">
                    Cari Nama Siswa / NISN
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Ketik nama atau NISN siswa..."
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 pl-9">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="sm:col-span-2 flex items-end gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm">
                    Terapkan
                </button>
                @if($selectedClassId || $search)
                    <a href="{{ route('tahfidz.custom_progress') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold transition" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Siswa & Ringkasan Capaian Cicilan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">
                    Daftar Progres Cicilan Siswa {{ $selectedClass ? '— Kelas ' . $selectedClass->name : '' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Total {{ $students->total() }} siswa terdaftar. Klik salah satu siswa untuk melihat detail riwayat cicilan ayat dan penjelasan akhir.
                </p>
            </div>
            @if(!empty($targetGradeInfo))
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                    Target {{ $targetGradeInfo['label'] }}: {{ $targetGradeInfo['total_surahs'] }} Surat / {{ $targetGradeInfo['total_ayat'] }} Ayat
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    Target Disesuaikan Tingkat Kelas (X: 25 Surat, XI: 7 Surat, XII: 5 Surat)
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Identitas Siswa</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4 text-center">
                            {{ !empty($targetGradeInfo) ? 'Ketuntasan ' . $targetGradeInfo['label'] : 'Ketuntasan Target Kelas' }}
                        </th>
                        <th class="py-3.5 px-4">
                            {{ !empty($targetGradeInfo) ? 'Rincian Surat (' . $targetGradeInfo['total_surahs'] . ' Surat)' : 'Rincian Surat Sesuai Kelas' }}
                        </th>
                        <th class="py-3.5 px-4">
                            {{ !empty($targetGradeInfo) ? 'Rincian Ayat (' . $targetGradeInfo['total_ayat'] . ' Ayat)' : 'Rincian Ayat Sesuai Kelas' }}
                        </th>
                        <th class="py-3.5 px-4 text-right">Aksi Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($students as $index => $st)
                        @php
                            $analysis = $studentAnalyses[$st->id] ?? null;
                            $pct = $analysis['overall_percentage'] ?? 0;
                            $completedAyat = $analysis['total_completed_ayat'] ?? 0;
                            $pendingAyat = $analysis['total_pending_ayat'] ?? 0;
                            $completedSurahs = $analysis['total_completed_surahs'] ?? 0;
                            $inInstallment = $analysis['total_in_installment_surahs'] ?? 0;
                            $unstarted = $analysis['total_unstarted_surahs'] ?? 0;
                            $totalTargetSurahs = $analysis['total_surahs'] ?? 0;
                            $totalTargetAyat = $analysis['total_target_ayat'] ?? 0;
                            $stGrade = $analysis['student_grade'] ?? $st->getNormalizedGrade();
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-400">
                                {{ $students->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-black flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($st->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('tahfidz.student_custom_progress', $st->id) }}" class="font-extrabold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition block">
                                            {{ $st->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400">NISN: {{ $st->nisn }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold">
                                <span class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $st->schoolClass?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-mono font-black text-sm {{ $pct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($pct > 0 ? 'text-teal-600 dark:text-teal-400' : 'text-slate-400') }}">
                                        {{ $pct }}%
                                    </span>
                                    <div class="w-20 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $pct) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            {{ $completedSurahs }} Selesai
                                        </span>
                                        @if($inInstallment > 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                                {{ $inInstallment }} Dicicil
                                            </span>
                                        @endif
                                        @if($unstarted > 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                {{ $unstarted }} Belum
                                            </span>
                                        @elseif($completedSurahs >= $totalTargetSurahs && $totalTargetSurahs > 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500 text-white">
                                                ✓ Tuntas
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 block font-medium">
                                        Target: {{ $totalTargetSurahs }} Surat (Kelas {{ $stGrade }})
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-[11px] space-y-0.5">
                                    <div>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $completedAyat }} Selesai</span>
                                        <span class="text-slate-400 mx-1">/</span>
                                        @if($pendingAyat > 0)
                                            <span class="font-bold text-rose-500 dark:text-rose-400">{{ $pendingAyat }} Belum</span>
                                        @else
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">0 Belum (Tuntas)</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        Total Target: {{ $totalTargetAyat }} Ayat
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('tahfidz.student_custom_progress', $st->id) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                                        <span>Detail Cicilan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <a href="{{ route('tahfidz.print_custom_progress', $st->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition" title="Cetak Dokumen Resmi A4">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <span class="text-3xl block">📋</span>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Tidak ada data siswa ditemukan.</p>
                                    <p class="text-xs">Coba sesuaikan filter kelas atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

