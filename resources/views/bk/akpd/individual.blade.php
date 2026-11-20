@extends('layouts.app')

@section('title', 'Laporan Profil Kebutuhan AKPD - ' . $student->name)

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Breadcrumb & Navigasi -->
    @include('bk.partials.nav')

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-teal-700 via-indigo-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-teal-200 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Laporan Diagnostik Individual POP-BK Kurikulum Merdeka</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Profil Kebutuhan Peserta Didik (AKPD)</h1>
                <p class="text-xs sm:text-sm text-teal-100 max-w-2xl leading-relaxed">
                    Hasil asesmen diagnostik non-tes pemetaan kebutuhan dan butir masalah 4 Bidang Layanan Bimbingan &amp; Konseling untuk dasar program pengembangan potensi ananda.
                </p>
            </div>

            <!-- Tombol Aksi Cetak & Navigasi -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('bk.akpd.print-individual', [$akpd, $student]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-900/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Resmi A4</span>
                </a>
                @if(!auth()->user()->isStudent())
                    <a href="{{ route('bk.akpd.show', $akpd) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs hover:bg-white/30 transition-all">
                        <span>&larr; Rekapitulasi AKPD</span>
                    </a>
                @else
                    <a href="{{ route('bk.akpd.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs hover:bg-white/30 transition-all">
                        <span>&larr; Menu AKPD</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Identitas Siswa Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-teal-500 text-white flex items-center justify-center font-black text-xl shadow-md">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $student->name }}</h2>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                            {{ $student->schoolClass?->name ?? 'Kelas X' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                            Fase E (Kelas X)
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3 font-mono">
                        <span>NISN: <strong>{{ $student->nisn ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>NIS: <strong>{{ $student->nis ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>Gender: <strong>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 w-full sm:w-auto">
                <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Tanggal Pengisian Angket</div>
                <div class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $response->submitted_at ? $response->submitted_at->translatedFormat('d F Y - H:i') : '-' }} WIB
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold mt-1.5 bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Prioritas Utama: Bidang {{ $response->highest_need_field }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Bidang Layanan Matriks Skor & Derajat Kebutuhan -->
    <div>
        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
            Matriks Persentase Kebutuhan 4 Bidang Layanan (Standar POP-BK)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach(['PRIBADI' => 'Pribadi', 'SOSIAL' => 'Sosial', 'BELAJAR' => 'Belajar', 'KARIER' => 'Karier'] as $fieldKey => $fieldLabel)
                @php
                    $d = $domainAnalysis[$fieldKey] ?? [
                        'count' => 0,
                        'total_field' => 10,
                        'percentage' => 0,
                        'degree' => 'Rendah',
                        'priority' => 'Prioritas Rendah',
                        'color' => 'emerald',
                        'badge' => 'bg-emerald-100 text-emerald-800',
                        'description' => '-'
                    ];
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4 hover:border-indigo-400 transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Bidang {{ $fieldLabel }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black border {{ $d['badge'] }}">
                                {{ $d['degree'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $d['percentage'] }}%</span>
                            <span class="text-xs text-slate-400 font-semibold">({{ $d['count'] }}/{{ $d['total_field'] }} butir)</span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden mt-3">
                            <div class="h-full rounded-full transition-all duration-500 {{ $d['percentage'] >= 50 ? 'bg-rose-500' : ($d['percentage'] >= 26 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($d['percentage'], 100) }}%"></div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-2.5 leading-relaxed">
                        {{ $d['description'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Rincian Butir Kebutuhan yang Dipilih -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Rincian Butir Kebutuhan / Masalah yang Dialami Siswa
                </h3>
                <p class="text-xs text-slate-400">
                    Total {{ count($selectedQuestions) }} butir pertanyaan dipilih dari total {{ $akpd->questions->count() }} butir instrumen.
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Data Rahasia Bimbingan Konseling
            </span>
        </div>

        <div class="p-5 sm:p-6 space-y-6">
            @foreach(['PRIBADI' => 'Bidang Layanan Pribadi', 'SOSIAL' => 'Bidang Layanan Sosial', 'BELAJAR' => 'Bidang Layanan Belajar', 'KARIER' => 'Bidang Layanan Karier'] as $fieldKey => $fieldTitle)
                @php
                    $items = $groupedQuestions[$fieldKey] ?? collect();
                @endphp
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $fieldKey === 'PRIBADI' ? 'bg-rose-500' : ($fieldKey === 'SOSIAL' ? 'bg-amber-500' : ($fieldKey === 'BELAJAR' ? 'bg-blue-500' : 'bg-emerald-500')) }}"></span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            {{ $fieldTitle }} ({{ $items->count() }} Butir Dialami)
                        </h4>
                    </div>

                    @if($items->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            @foreach($items as $q)
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700 flex items-start gap-3">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black font-mono shrink-0 {{ $fieldKey === 'PRIBADI' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($fieldKey === 'SOSIAL' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($fieldKey === 'BELAJAR' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300')) }}">
                                        {{ $q->code }}
                                    </span>
                                    <div class="space-y-0.5">
                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-snug">
                                            {{ $q->statement }}
                                        </p>
                                        @if($q->profile_dimension)
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 block">
                                                Aspek: {{ $q->profile_dimension }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3.5 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30 border border-dashed border-slate-200 dark:border-slate-800 text-xs text-slate-400 italic">
                            Tidak ada butir kebutuhan/masalah yang dipilih pada bidang ini (Kondisi stabil/mandiri).
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Rekomendasi Program Layanan Bimbingan & Konseling (POP-BK) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-black">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Rekomendasi Tindak Lanjut Program Layanan Bimbingan &amp; Konseling
                </h3>
                <p class="text-xs text-slate-400">
                    Disusun berdasarkan norma operasional POP-BK Kurikulum Merdeka untuk tindak lanjut semester aktif.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- 1. Layanan Dasar -->
            <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/20 border border-indigo-200/80 dark:border-indigo-900/50 space-y-2">
                <div class="flex items-center gap-2 font-bold text-xs text-indigo-900 dark:text-indigo-300">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    <span>1. Layanan Dasar (Bimbingan Klasikal / Kelompok)</span>
                </div>
                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc list-inside">
                    @foreach($recommendations['layanan_dasar'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- 2. Layanan Responsif -->
            <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-900/50 space-y-2">
                <div class="flex items-center gap-2 font-bold text-xs text-rose-900 dark:text-rose-300">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>2. Layanan Responsif (Konseling Individual / Mediasi)</span>
                </div>
                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc list-inside">
                    @foreach($recommendations['layanan_responsif'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- 3. Peminatan & Perencanaan Individual -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/50 space-y-2">
                <div class="flex items-center gap-2 font-bold text-xs text-emerald-900 dark:text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>3. Peminatan &amp; Perencanaan Individual (Fase F)</span>
                </div>
                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc list-inside">
                    @foreach($recommendations['peminatan_perencanaan'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- 4. Dukungan Sistem -->
            <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/50 space-y-2">
                <div class="flex items-center gap-2 font-bold text-xs text-amber-900 dark:text-amber-300">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    <span>4. Dukungan Sistem (Kolaborasi Wali Kelas &amp; Orang Tua)</span>
                </div>
                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc list-inside">
                    @foreach($recommendations['dukungan_sistem'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
