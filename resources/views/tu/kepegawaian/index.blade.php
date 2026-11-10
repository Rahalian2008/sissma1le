@extends('layouts.app')

@section('title', 'Adm. Kepegawaian (PTK) - Tata Usaha')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 p-6 sm:p-8 text-white shadow-xl shadow-teal-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-emerald-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Sub Menu Administrasi Ketatausahaan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Administrasi Kepegawaian (PTK)</h1>
                <p class="text-emerald-100 text-sm mt-1 max-w-xl">
                    Sistem informasi data Guru dan Tenaga Kependidikan, Daftar Urut Kepangkatan (DUK), kontrol kenaikan pangkat & gaji berkala, buku cuti, dan struktur organisasi sekolah.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($totalEmployees === 0)
                    <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST" onsubmit="return confirm('Tarik seluruh data Guru & Tenaga Kependidikan dari Master Data ke Administrasi Kepegawaian?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-lg shadow-black/10 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>📥 Tarik Data dari Master ({{ $masterAvailableCount }} PTK)</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST" class="inline" onsubmit="return confirm('Perbarui / tarik ulang data Guru & PTK dari Master Data?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold text-xs border border-white/30 transition cursor-pointer" title="Sinkronkan ulang jika ada PTK baru di Data Master">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Tarik Ulang</span>
                        </button>
                    </form>
                    <form action="{{ route('tu.kepegawaian.clear') }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Kosongkan data di Administrasi Kepegawaian? (Data Master Guru & PTK tetap aman dan bisa ditarik kembali kapan saja)')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-500/80 hover:bg-rose-600 text-white font-bold text-xs border border-rose-300/30 transition cursor-pointer" title="Kosongkan data kepegawaian dari meja kerja TU">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Kosongkan Adm Kepegawaian</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('duk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-teal-700 font-bold text-xs shadow-lg shadow-black/10 hover:bg-emerald-50 transition-all">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Daftar DUK Resmi</span>
                </a>
                <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Buku Induk PTK</span>
                </a>
            </div>
        </div>
    </div>

    @if($totalEmployees === 0)
    <!-- Panel Banner Status: Data Adm Kepegawaian Masih Kosong -->
    <div class="p-6 sm:p-8 bg-teal-50/80 dark:bg-slate-800/80 border-2 border-dashed border-teal-300 dark:border-teal-700/60 rounded-3xl text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-teal-600/10 text-teal-600 dark:text-teal-400 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <h3 class="text-base font-black text-slate-800 dark:text-white">Data di Administrasi Kepegawaian Saat Ini Sedang Dikosongkan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto mt-1">
                Sesuai alur tata kelola data sekolah, seluruh basis data Guru & Tenaga Kependidikan tersimpan aman di <strong>Data Master Guru & PTK ({{ $masterAvailableCount }} PTK)</strong>. Anda dapat menarik datanya ke Administrasi Kepegawaian kapan saja.
            </p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>📥 Tarik Data dari Master Guru & PTK ({{ $masterAvailableCount }} PTK)</span>
                </button>
            </form>
            <a href="{{ route('master.teachers') }}" class="px-4 py-2.5 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-bold rounded-xl text-xs transition">
                Buka Master Data Guru & PTK &rarr;
            </a>
        </div>
    </div>
    @endif

    <!-- Statistik Kepegawaian Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total PTK</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalEmployees) }}</div>
            <div class="text-[11px] text-teal-600 dark:text-teal-400 mt-0.5 font-medium">Guru & Tenaga Kependidikan</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Guru Pendidik</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalTeachers) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Tenaga Pendidik</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tenaga Kependidikan</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalStaff) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Staf Administrasi & Laboran</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">PNS</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($pnsCount) }}</div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">Pegawai Negeri Sipil</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">PPPK</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($pppkCount) }}</div>
            <div class="text-[11px] text-cyan-600 dark:text-cyan-400 mt-0.5 font-medium">Perjanjian Kerja</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">GTT / PTT / Honorer</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($honorerCount) }}</div>
            <div class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 font-medium">Non-ASN Sekolah</div>
        </div>
    </div>

    <!-- Alert Kontrol Kepegawaian (KP & KGB & PPPK) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-900 dark:text-amber-200 flex items-start gap-3">
            <div class="p-2 rounded-xl bg-amber-500 text-white shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-amber-700 dark:text-amber-300">Kontrol Kenaikan Pangkat (KP)</h4>
                <p class="text-xs mt-0.5">Ada <span class="font-black text-sm">{{ $kpDueEmployees->count() }} pegawai</span> yang memenuhi periode usulan kenaikan pangkat dalam 3 bulan ke depan.</p>
                <a href="{{ route('employees.kontrol-pangkat.index') }}" class="inline-block mt-2 text-[11px] font-bold text-amber-700 dark:text-amber-300 hover:underline">Buka Buku Kontrol KP &rarr;</a>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-900 dark:text-teal-200 flex items-start gap-3">
            <div class="p-2 rounded-xl bg-teal-500 text-white shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-teal-700 dark:text-teal-300">Kontrol Kenaikan Gaji Berkala (KGB)</h4>
                <p class="text-xs mt-0.5">Terdapat <span class="font-black text-sm">{{ $kgbDueEmployees->count() }} pegawai</span> yang mendekati jatuh tempo KGB (PNS & PPPK).</p>
                <a href="{{ route('employees.kontrol-kgb.index') }}" class="inline-block mt-2 text-[11px] font-bold text-teal-700 dark:text-teal-300 hover:underline">Buka Buku Kontrol KGB &rarr;</a>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-900 dark:text-indigo-200 flex items-start gap-3">
            <div class="p-2 rounded-xl bg-indigo-500 text-white shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Cuti Pegawai Aktif</h4>
                <p class="text-xs mt-0.5">Saat ini <span class="font-black text-sm">{{ $activeLeaves->count() }} pegawai</span> sedang dalam masa cuti kerja yang telah disetujui pimpinan.</p>
                <a href="{{ route('employees.kontrol-cuti.index') }}" class="inline-block mt-2 text-[11px] font-bold text-indigo-700 dark:text-indigo-300 hover:underline">Buka Buku Kontrol Cuti &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Access Feature Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Modul Layanan Administrasi Kepegawaian</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-3">
            <a href="{{ route('employees.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Data PTK</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Guru & Tendik</span>
            </a>
            <a href="{{ route('employees.buku-induk.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku Induk</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Pegawai Digital</span>
            </a>
            <a href="{{ route('duk.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-cyan-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">DUK Resmi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Urut Kepangkatan</span>
            </a>
            <a href="{{ route('employees.kontrol-pangkat.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kontrol KP</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kenaikan Pangkat</span>
            </a>
            <a href="{{ route('employees.kontrol-kgb.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kontrol KGB</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Gaji Berkala</span>
            </a>
            <a href="{{ route('employees.kontrol-pppk.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-cyan-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kontrak PPPK</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Masa Perjanjian</span>
            </a>
            <a href="{{ route('employees.kontrol-cuti.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kontrol Cuti</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Izin & Cuti</span>
            </a>
            <a href="{{ route('employees.kontrol-pensiun.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-rose-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">BUP Pensiun</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Batas Usia</span>
            </a>
            <a href="{{ route('dso.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-slate-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-slate-500/10 text-slate-600 dark:text-slate-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Struktur DSO</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Susunan Organisasi</span>
            </a>
        </div>
    </div>

    <!-- Tabel Master Data Pegawai PTK -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 dark:text-white">Direktori Guru & Tendik (PTK)</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950/80 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                        Sumber: Data Master PTK
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Basis data PTK terpadu dengan sinkronisasi DAPODIK & SK Pengangkatan</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('master.teachers') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5" title="Buka Master Data Guru & PTK untuk mengedit biodata atau peran akun">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                    <span>Kelola di Master Data</span>
                </a>
                <form action="{{ route('tu.kepegawaian.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="type" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Guru" {{ request('type') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Tendik" {{ request('type') == 'Tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Semua Status --</option>
                        <option value="PNS" {{ request('status') == 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ request('status') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Honorer" {{ request('status') == 'Honorer' ? 'selected' : '' }}>Honorer / GTT</option>
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIP / NUPTK..." class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 w-48">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700 transition">Filter</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai / PTK</th>
                        <th class="py-3 px-4">NIP / NUPTK</th>
                        <th class="py-3 px-4">Jabatan & Tugas</th>
                        <th class="py-3 px-4">Status & Gol/Ruang</th>
                        <th class="py-3 px-4">Pendidikan</th>
                        <th class="py-3 px-4 text-center">Jenis</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($employees as $e)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $e->name }}
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                            {{ $e->nip ?: ($e->nuptk ?: '-') }}
                        </td>
                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300 font-medium">
                            {{ $e->position ?: ($e->employment_type ?: 'Staf') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $e->employment_status === 'PNS' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : ($e->employment_status === 'PPPK' ? 'bg-cyan-500/10 text-cyan-600 border border-cyan-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20') }}">
                                {{ $e->employment_status }} {{ $e->rank_group ? "({$e->rank_group})" : '' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                            {{ $e->education_level ?: '-' }} {{ $e->education_major ? "- {$e->education_major}" : '' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $e->employment_type === 'Guru' ? 'bg-blue-500/10 text-blue-600' : 'bg-slate-500/10 text-slate-400' }}">
                                {{ $e->employment_type ?: 'Tendik' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <a href="{{ route('employees.buku-induk.show', $e) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 font-bold hover:bg-teal-100 transition text-[11px]">
                                Buku Induk
                            </a>
                            <a href="{{ route('employees.show', $e) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold hover:bg-slate-200 transition text-[11px]">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <div class="max-w-md mx-auto space-y-2">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Data Administrasi Kepegawaian Masih Kosong</div>
                                <p class="text-xs text-slate-400">Belum ada pegawai yang didaftarkan ke meja kerja Administrasi Kepegawaian atau data baru saja dikosongkan.</p>
                                <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST" class="pt-2">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Tarik Data dari Master Guru & PTK ({{ $masterAvailableCount }} PTK)</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection

