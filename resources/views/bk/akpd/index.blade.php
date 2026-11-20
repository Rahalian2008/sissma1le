@extends('layouts.app')

@section('title', 'AKPD - Angket Kebutuhan Peserta Didik (POP-BK)')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-xs font-semibold text-teal-700 mb-2">
                <span>Asesmen Diagnostik Non-Tes POP-BK Kemdikbudristek (Fase E)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Angket Kebutuhan Peserta Didik (AKPD)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Instrumen asesmen kebutuhan berbasis 4 Bidang Layanan (Pribadi, Sosial, Belajar, Karier) khusus untuk peserta didik Kelas X (Fase E).</p>
        </div>
        <div class="flex items-center gap-2">
            @if(!auth()->user()->isStudent())
                <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    &larr; Dashboard BK
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    &larr; Beranda Siswa
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Khusus Siswa Kelas X -->
    @if(auth()->user()->isStudent() || auth()->user()->student)
        <div class="p-4 rounded-2xl bg-gradient-to-r from-teal-500/10 via-indigo-500/10 to-transparent border border-teal-200 dark:border-teal-800 flex items-start gap-3.5">
            <span class="text-2xl shrink-0">🎓</span>
            <div class="space-y-1 text-xs">
                <h4 class="font-bold text-teal-900 dark:text-teal-200">
                    Halo Ananda {{ $currentStudent?->name ?? 'Peserta Didik' }} ({{ $currentStudent?->schoolClass?->name ?? 'Kelas X' }})
                </h4>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                    Asesmen Diagnostik AKPD ini dirancang khusus untuk memetakan kebutuhan bimbingan dan minat pengembangan potensi Ananda di jenjang SMA. Jawaban bersifat rahasia dan akan menjadi dasar pendampingan oleh Guru Bimbingan Konseling.
                </p>
            </div>
        </div>
    @endif

    <!-- Stats by Field -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bidang Pribadi</span>
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $fieldsCount['PRIBADI'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Emosi, Percaya Diri, Karakter</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bidang Sosial</span>
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $fieldsCount['SOSIAL'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Komunikasi, Teman Sebaya, Adaptasi</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bidang Belajar</span>
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $fieldsCount['BELAJAR'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Waktu Belajar, Kesulitan, Motivasi</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bidang Karier</span>
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $fieldsCount['KARIER'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Studi Lanjut PTN, Minat, Profesi</div>
        </div>
    </div>

    <!-- Daftar Instrumen AKPD -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Daftar Instrumen AKPD Aktif (Fase E)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($akpds as $akpd)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-teal-400 transition-all space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                Tingkat {{ $akpd->grade_level }} (Fase E)
                            </span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1.5">{{ $akpd->title }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $akpd->description }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-lg text-[10px] font-extrabold {{ $akpd->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                            {{ $akpd->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 py-3 border-y border-slate-100 dark:border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-400">Jumlah Butir Asesmen:</span>
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $akpd->questions_count }} Butir Pernyataan</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Total Responden:</span>
                            <div class="font-bold text-indigo-600 dark:text-indigo-400">{{ $akpd->responses_count }} Respon Siswa</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 gap-2 flex-wrap">
                        @if(auth()->user()->isStudent() || auth()->user()->student)
                            @if(in_array($akpd->id, $answeredAkpdIds ?? []))
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($currentStudent)
                                        <a href="{{ route('bk.akpd.individual', [$akpd, $currentStudent]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Lihat Hasil AKPD Saya</span>
                                        </a>
                                        <a href="{{ route('bk.akpd.print-individual', [$akpd, $currentStudent]) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition" title="Cetak Laporan A4">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak A4</span>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <a href="{{ route('bk.akpd.take', $akpd) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Mulai Isi Angket AKPD</span>
                                </a>
                            @endif
                        @else
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('bk.akpd.class-report', $akpd) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <span>Peta Profil Kelas</span>
                                </a>
                                <a href="{{ route('bk.akpd.show', $akpd) }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                    <span>Matriks Respon &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white dark:bg-slate-900 rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-200 dark:border-slate-800">
                    Belum ada instrumen AKPD yang diaktifkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
