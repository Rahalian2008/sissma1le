@extends('layouts.app')

@section('title', 'AKPD - Angket Kebutuhan Peserta Didik (POP-BK)')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 mb-2">
                <span>Asesmen Diagnostik Non-Tes POP-BK Kemdikbudristek</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Angket Kebutuhan Peserta Didik (AKPD)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Instrumen asesmen kebutuhan berbasis 4 Bidang Layanan (Pribadi, Sosial, Belajar, Karier) untuk dasar penyusunan program tahunan BK Kurikulum Merdeka.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                &larr; Dashboard BK
            </a>
        </div>
    </div>

    <!-- Stats by Field -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang Pribadi</span>
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $fieldsCount['PRIBADI'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Siswa Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Emosi, Percaya Diri, Karakter</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang Sosial</span>
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $fieldsCount['SOSIAL'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Siswa Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Komunikasi, Teman Sebaya, Adaptasi</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang Belajar</span>
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $fieldsCount['BELAJAR'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Siswa Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Waktu Belajar, Kesulitan, Motivasi</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang Karier</span>
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $fieldsCount['KARIER'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Siswa Prioritas</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Studi Lanjut PTN, Minat, Profesi</div>
        </div>
    </div>

    <!-- Daftar Instrumen AKPD -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Instrumen AKPD Aktif</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($akpds as $akpd)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:border-indigo-300 transition-all space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Tingkat {{ $akpd->grade_level }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 mt-1.5">{{ $akpd->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $akpd->description }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-lg text-[10px] font-extrabold {{ $akpd->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $akpd->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 py-3 border-y border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400">Jumlah Butir Kebutuhan:</span>
                            <div class="font-bold text-slate-800">{{ $akpd->questions_count }} Butir Asesmen</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Total Responden Siswa:</span>
                            <div class="font-bold text-indigo-600">{{ $akpd->responses_count }} Respon Terkumpul</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 gap-2">
                        <a href="{{ route('bk.akpd.take', $akpd) }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Isi / Simulasi Angket
                        </a>
                        <a href="{{ route('bk.akpd.show', $akpd) }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Lihat Matriks & Analisis &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-200">
                    Belum ada instrumen AKPD yang diaktifkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

