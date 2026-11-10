@extends('layouts.app')

@section('title', 'Bimbingan Konseling (BK) - ' . ($school?->name ?? 'SMA Negeri 1 Lengkong'))

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-600 via-emerald-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl shadow-emerald-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-teal-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Standar Permendikbud & POP-BK Kurikulum Merdeka</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Bimbingan & Konseling (BK)</h1>
                <p class="text-teal-100 text-sm mt-1 max-w-xl">
                    Pusat layanan bimbingan perkembangan holistik peserta didik: 4 Bidang Layanan (Pribadi, Sosial, Belajar, Karier), responsif, home visit, pemanggilan orang tua, dan asesmen psikotes minat bakat.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('bk.akpd.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>AKPD Diagnostik</span>
                </a>
                <a href="{{ route('bk.rpl.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>RPL 1 Lembar</span>
                </a>
                <a href="{{ route('bk.counseling.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-emerald-800 font-bold text-xs shadow-lg shadow-black/10 hover:bg-teal-50 transition-all">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Konseling</span>
                </a>
                <a href="{{ route('bk.psychological.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span>Tes Psikotes</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Bidang Layanan BK Standar POP-BK -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">4 Bidang Layanan Bimbingan & Konseling</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Bimbingan Pribadi -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-indigo-500 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        {{ $pribadiCount }} Sesi
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Bimbingan Pribadi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Pengembangan karakter, kematangan emosi, kedisiplinan, spiritual, dan resolusi konflik diri.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('bk.counseling.index', ['field_of_service' => 'PRIBADI']) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Sesi &rarr;</a>
                </div>
            </div>

            <!-- 2. Bimbingan Sosial -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-emerald-500 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        {{ $sosialCount }} Sesi
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Bimbingan Sosial</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Hubungan teman sebaya, adaptasi lingkungan sekolah, sosiometri kelas, empati, dan anti-bullying.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('bk.counseling.index', ['field_of_service' => 'SOSIAL']) }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Lihat Sesi &rarr;</a>
                </div>
            </div>

            <!-- 3. Bimbingan Belajar -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-amber-500 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                        {{ $belajarCount }} Sesi
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Bimbingan Belajar</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Gaya belajar VAK, kesulitan konsentrasi, motivasi akademik, strategi menghadapi ujian, dan literasi.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('bk.counseling.index', ['field_of_service' => 'BELAJAR']) }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">Lihat Sesi &rarr;</a>
                </div>
            </div>

            <!-- 4. Bimbingan Karier -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-sky-500 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                        {{ $karierCount }} Sesi
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Bimbingan Karier</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Peminatan jurusan, Holland RIASEC, seleksi SNBP/SNBT perguruan tinggi, prospek profesi, dan dunia kerja.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('bk.counseling.index', ['field_of_service' => 'KARIER']) }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline">Lihat Sesi &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Regulasi & Standar POP-BK Kurikulum Merdeka (Permendikbud 111/2014) -->
    <div class="bg-gradient-to-r from-teal-900/40 via-emerald-900/30 to-indigo-900/40 border border-teal-500/30 rounded-3xl p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Modul Regulasi Standar Permendikbudristek & POP-BK</h2>
                </div>
                <p class="text-xs text-teal-200/80 mt-0.5">Asesmen diagnostik non-tes, perencanaan RPL merdeka belajar, serta penanganan kasus kolaboratif berjenjang.</p>
            </div>
            <span class="px-2.5 py-1 rounded-xl bg-teal-500/20 text-teal-300 text-[11px] font-bold border border-teal-500/30 self-start sm:self-auto">
                Kurikulum Merdeka Ready
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. AKPD -->
            <a href="{{ route('bk.akpd.index') }}" class="group p-4 bg-slate-900/80 hover:bg-slate-800 rounded-2xl border border-teal-500/20 hover:border-teal-400 transition-all shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-black group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $totalAkpd }} Periode</span>
                </div>
                <h3 class="font-bold text-white text-sm group-hover:text-teal-300 transition-colors">AKPD Diagnostik</h3>
                <p class="text-[11px] text-slate-300 mt-1 line-clamp-2">Angket Kebutuhan Peserta Didik 4 bidang untuk analisis butir masalah & penyusunan program tahunan.</p>
                <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-teal-400 font-semibold">
                    <span>Buka Instrumen</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 2. RPL BK 1 Lembar -->
            <a href="{{ route('bk.rpl.index') }}" class="group p-4 bg-slate-900/80 hover:bg-slate-800 rounded-2xl border border-indigo-500/20 hover:border-indigo-400 transition-all shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-black group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">{{ $totalRpl }} RPL</span>
                </div>
                <h3 class="font-bold text-white text-sm group-hover:text-indigo-300 transition-colors">RPL BK 1 Lembar</h3>
                <p class="text-[11px] text-slate-300 mt-1 line-clamp-2">Rencana Pelaksanaan Layanan BK: Layanan Dasar, Responsif, Peminatan Individual, dan Dukungan Sistem.</p>
                <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-indigo-400 font-semibold">
                    <span>Kelola RPL</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 3. Konferensi Kasus -->
            <a href="{{ route('bk.case-conference.index') }}" class="group p-4 bg-slate-900/80 hover:bg-slate-800 rounded-2xl border border-amber-500/20 hover:border-amber-400 transition-all shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center font-black group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300">{{ $totalCaseConferences }} Kasus</span>
                </div>
                <h3 class="font-bold text-white text-sm group-hover:text-amber-300 transition-colors">Konferensi Kasus</h3>
                <p class="text-[11px] text-slate-300 mt-1 line-clamp-2">Pembahasan multi-pihak (Kepsek, Guru BK, Wali Kelas, Ortu) dengan Berita Acara & komitmen penanganan terpadu.</p>
                <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-amber-400 font-semibold">
                    <span>Lihat Sidang Kasus</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 4. Alih Tangan Kasus -->
            <a href="{{ route('bk.referral.index') }}" class="group p-4 bg-slate-900/80 hover:bg-slate-800 rounded-2xl border border-rose-500/20 hover:border-rose-400 transition-all shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center font-black group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300">{{ $totalReferrals }} Rujukan</span>
                </div>
                <h3 class="font-bold text-white text-sm group-hover:text-rose-300 transition-colors">Alih Tangan Kasus</h3>
                <p class="text-[11px] text-slate-300 mt-1 line-clamp-2">Surat rujukan (Referral) resmi ke Psikolog Klinis, Psikiater, Puskesmas/RS, DP3A/UPTD PPA, atau Bapas.</p>
                <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-rose-400 font-semibold">
                    <span>Kelola Rujukan</span>
                    <span>&rarr;</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Modul Layanan BK Shortcut Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Instrumen & Fasilitas Layanan BK</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('bk.counseling.index') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Layanan Konseling</span>
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $totalSessions }} Tercatat</span>
            </a>
            <a href="{{ route('bk.home-visit.index') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Home Visit</span>
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $homeVisitsCount }} Kunjungan</span>
            </a>
            <a href="{{ route('bk.parent-call.index') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Panggilan Ortu</span>
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $parentCallsCount }} Surat Resmi</span>
            </a>
            <a href="{{ route('bk.sociometry.index') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Sosiometri</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Dinamika Kelas</span>
            </a>
            <a href="{{ route('bk.psychological.index') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-pink-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-pink-500/10 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Tes Psikotes</span>
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $totalPsychResults }} Siswa Teruji</span>
            </a>
            <a href="{{ route('bk.psychological.class-report') }}" class="group p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Peta Gaya Belajar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Rekap Kelas VAK</span>
            </a>
        </div>
    </div>

    <!-- Dua Kolom: Peta Hasil Psikotes (VAK & RIASEC) & Siswa Prioritas Bimbingan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Visualisasi Distribusi Psikotes -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white">Peta Modalitas Belajar Siswa (VAK)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Berdasarkan hasil asesmen psikotes yang telah diselesaikan siswa</p>
                </div>
                <a href="{{ route('bk.psychological.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Kelola &rarr;</a>
            </div>

            <!-- Gaya Belajar VAK Bars -->
            @php
                $vakTotal = array_sum($vakDistribution) ?: 1;
                $vPct = round(($vakDistribution['VISUAL'] / $vakTotal) * 100);
                $aPct = round(($vakDistribution['AUDITORI'] / $vakTotal) * 100);
                $kPct = round(($vakDistribution['KINESTETIK'] / $vakTotal) * 100);
            @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-sky-600 dark:text-sky-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            Visual (Daya Serap Penglihatan & Gambar)
                        </span>
                        <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $vakDistribution['VISUAL'] }} siswa ({{ $vPct }}%)</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-sky-500 to-blue-600 rounded-full" style="width: {{ $vPct }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Auditori (Daya Serap Pendengaran & Diskusi)
                        </span>
                        <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $vakDistribution['AUDITORI'] }} siswa ({{ $aPct }}%)</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full" style="width: {{ $aPct }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Kinestetik (Daya Serap Praktik, Gerak, & Eksperimen)
                        </span>
                        <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $vakDistribution['KINESTETIK'] }} siswa ({{ $kPct }}%)</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-orange-600 rounded-full" style="width: {{ $kPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Distribusi Karier Holland RIASEC Mini Badges -->
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Sebaran Minat Karier Holland (RIASEC)</span>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-center">
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">R - Realistic</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['R'] }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">I - Investig.</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['I'] }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">A - Artistic</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['A'] }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">S - Social</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['S'] }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">E - Enterpr.</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['E'] }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-bold">C - Convent.</span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $riasecDistribution['C'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Siswa Prioritas Bimbingan (Berdasarkan Akumulasi Poin Pelanggaran / Peringatan) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        Siswa Prioritas Pendampingan BK
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Siswa dengan akumulasi poin pelanggaran dan butuh bimbingan intensif</p>
                </div>
                <a href="{{ route('violations.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">Pelanggaran &rarr;</a>
            </div>

            <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                @forelse($priorityStudents as $ps)
                @php
                    $points = $ps->verified_violations_sum_points ?? 0;
                    $severityBadge = $points >= 50
                        ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20'
                        : ($points >= 25 ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-slate-500/10 text-slate-600');
                @endphp
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 hover:bg-slate-100/60 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $ps->initials ?: substr($ps->name, 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $ps->name }}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $ps->schoolClass?->name ?: 'Kelas -' }} &bull; NISN: {{ $ps->nisn ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $severityBadge }}">
                            {{ $points }} Poin
                        </span>
                        <a href="{{ route('bk.student.profile360', $ps) }}" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition shadow-xs">
                            Profil 360°
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-8">Alhamdulillah, tidak ada siswa dengan akumulasi pelanggaran tinggi saat ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sesi Konseling & Hasil Psikotes Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sesi Konseling Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Sesi Bimbingan & Konseling Terkini</h3>
                <a href="{{ route('bk.counseling.index') }}" class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline">Semua Sesi &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentSessions as $session)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $session->field_badge['class'] }}">
                                {{ $session->field_badge['label'] }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">{{ $session->session_code }}</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate mt-1">{{ $session->topic }}</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Siswa: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $session->student?->name }}</span> ({{ $session->student?->schoolClass?->name }})</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::parse($session->date)->translatedFormat('d M Y') }}</span>
                        <a href="{{ route('bk.counseling.show', $session) }}" class="inline-block mt-1 text-[11px] font-bold text-teal-600 hover:underline">Detail</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada sesi konseling yang tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Hasil Tes Psikotes Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Hasil Asesmen Psikotes Terkini</h3>
                <a href="{{ route('bk.psychological.index') }}" class="text-xs font-bold text-pink-600 dark:text-pink-400 hover:underline">Semua Hasil &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentPsychResults as $res)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-pink-500/10 text-pink-600 dark:text-pink-400 border border-pink-500/20">
                            {{ $res->test?->name }}
                        </span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate mt-1">{{ $res->student?->name }} ({{ $res->student?->schoolClass?->name }})</h4>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">{{ $res->profile_label }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::parse($res->taken_at)->translatedFormat('d M Y') }}</span>
                        <a href="{{ route('bk.psychological.result', $res) }}" class="inline-block mt-1 text-[11px] font-bold text-pink-600 hover:underline">Hasil Laporan</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada hasil asesmen psikotes yang masuk.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

