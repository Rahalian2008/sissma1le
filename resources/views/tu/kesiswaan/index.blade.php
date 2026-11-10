@extends('layouts.app')

@section('title', 'Adm. Kesiswaan - Tata Usaha')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-sky-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                    <span>Sub Menu Administrasi Ketatausahaan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Administrasi Kesiswaan</h1>
                <p class="text-sky-100 text-sm mt-1 max-w-xl">
                    Pengelolaan terpadu Buku Induk Siswa, data nomor induk, mutasi peserta didik, rekapitulasi rombel, arsip ijazah, dan demografi kesiswaan {{ $school?->name ?? 'SMA Negeri 1 Lengkong' }}.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($totalStudents === 0)
                    <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST" onsubmit="return confirm('Tarik seluruh data siswa & RFID dari Master Data ke Administrasi Kesiswaan?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-lg shadow-black/10 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>📥 Tarik Data dari Master ({{ $masterAvailableCount }} Siswa)</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST" class="inline" onsubmit="return confirm('Perbarui / tarik ulang data siswa & RFID dari Master Data?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold text-xs border border-white/30 transition cursor-pointer" title="Sinkronkan ulang jika ada siswa baru di Data Master">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Tarik Ulang</span>
                        </button>
                    </form>
                    <form action="{{ route('tu.kesiswaan.clear') }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Kosongkan data siswa di Administrasi Kesiswaan? (Data Master Siswa & RFID tetap aman dan bisa ditarik kembali kapan saja)')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-500/80 hover:bg-rose-600 text-white font-bold text-xs border border-rose-300/30 transition cursor-pointer" title="Kosongkan data siswa dari meja kerja TU">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Kosongkan Adm Kesiswaan</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('tu.kesiswaan.absen_manual.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-black text-xs shadow-lg shadow-black/10 transition-all">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Cetak Absen Manual</span>
                </a>
                <a href="{{ route('tu.kesiswaan.legalisir.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Legalisir Dokumen</span>
                </a>
                <a href="{{ route('buku-induk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-blue-700 font-bold text-xs shadow-lg shadow-black/10 hover:bg-sky-50 transition-all">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Buku Induk</span>
                </a>
            </div>
        </div>
    </div>

    @if($totalStudents === 0)
    <!-- Panel Banner Status: Data Adm Kesiswaan Masih Kosong -->
    <div class="p-6 sm:p-8 bg-blue-50/80 dark:bg-slate-800/80 border-2 border-dashed border-blue-300 dark:border-blue-700/60 rounded-3xl text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-600/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <h3 class="text-base font-black text-slate-800 dark:text-white">Data di Administrasi Kesiswaan Saat Ini Sedang Dikosongkan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg mx-auto mt-1">
                Sesuai alur tata kelola data sekolah, seluruh basis data siswa tersimpan aman di <strong>Data Master Siswa & RFID ({{ $masterAvailableCount }} Siswa)</strong>. Anda dapat menarik datanya ke Administrasi Kesiswaan kapan saja.
            </p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>📥 Tarik Data dari Master Siswa & RFID ({{ $masterAvailableCount }} Siswa)</span>
                </button>
            </form>
            <a href="{{ route('master.students') }}" class="px-4 py-2.5 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 font-bold rounded-xl text-xs transition">
                Buka Master Data Siswa & RFID &rarr;
            </a>
        </div>
    </div>
    @endif

    <!-- Statistik Kesiswaan Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Siswa Aktif</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($activeStudents) }}</div>
            <div class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5 font-medium">L: {{ $maleStudents }} | P: {{ $femaleStudents }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas X (Fase E)</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($classXCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Siswa Baru</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas XI (Fase F)</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($classXICount) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Tingkat Madya</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas XII (Fase F)</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($classXIICount) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Tingkat Akhir</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Mutasi Siswa</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $mutationsIn + $mutationsOut }}</div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">+{{ $mutationsIn }} Masuk / -{{ $mutationsOut }} Keluar</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ijazah & SKL</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalDiplomas) }}</div>
            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-0.5 font-medium">PIP: {{ $pipCount }} siswa</div>
        </div>
    </div>

    <!-- Quick Access Feature Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Modul Layanan Administrasi Kesiswaan Terpadu</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <a href="{{ route('students.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Data Siswa</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Semua Rombel</span>
            </a>
            <a href="{{ route('buku-induk.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-sky-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku Induk</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Format Standar</span>
            </a>
            <a href="{{ route('klaper.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Klaper Siswa</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Urutan Abjad</span>
            </a>
            <a href="{{ route('mutations.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku Mutasi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Masuk & Keluar</span>
            </a>
            <a href="{{ route('students.rombel.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Penataan Rombel</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kenaikan Kelas</span>
            </a>
            <a href="{{ route('students.scholarships.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Beasiswa (PIP/KETM)</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Bantuan Siswa</span>
            </a>
            <a href="{{ route('students.cohort.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-cyan-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Analisis Kohort</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Angka Bertahan</span>
            </a>
            <a href="{{ route('students.diplomas.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-rose-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Ijazah & SKL</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Tanda Terima</span>
            </a>
            <a href="{{ route('alumni.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-violet-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Tracer Alumni</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Pelacakan Karir</span>
            </a>
            <a href="{{ route('students.learning-agendas.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Agenda Belajar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Jurnal Harian</span>
            </a>
            <a href="{{ route('students.demographics') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Demografi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Statistik Wilayah</span>
            </a>
            <a href="{{ route('tu.kesiswaan.legalisir.index') }}" class="group p-3.5 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/20 rounded-2xl border-2 border-amber-400 dark:border-amber-600/70 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-300 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-black text-amber-900 dark:text-amber-200">Legalisir Dokumen</span>
                <span class="text-[10px] text-amber-700 dark:text-amber-400 mt-0.5 font-semibold">Agenda & Tanda Terima</span>
            </a>
            <a href="{{ route('spmb.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">SPMB SMA</span>
                <span class="text-[10px] text-slate-400 mt-0.5">PPDB Baru</span>
            </a>
            <a href="{{ route('spmb.tka.index') }}" class="group p-3.5 bg-amber-50/60 dark:bg-amber-950/20 rounded-2xl border-2 border-amber-300 dark:border-amber-700/60 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-300 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform font-black">
                    🎯
                </div>
                <span class="text-xs font-black text-amber-900 dark:text-amber-200">Jalur Nilai TKA</span>
                <span class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5 font-semibold">Skor & Ranking</span>
            </a>
            <a href="{{ route('tu.kesiswaan.absen_manual.index') }}" class="group p-3.5 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 rounded-2xl border-2 border-emerald-400 dark:border-emerald-600/70 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform font-black">
                    📋
                </div>
                <span class="text-xs font-black text-emerald-900 dark:text-emerald-200">Cetak Absen Manual</span>
                <span class="text-[10px] text-emerald-700 dark:text-emerald-400 mt-0.5 font-semibold">Model SIKATAS</span>
            </a>
        </div>
    </div>

    <!-- Tabel Master Data Siswa SISSMA1LE -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 dark:text-white">Direktori Siswa Aktif</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        Sumber: Data Master Siswa
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Basis data terintegrasi langsung dengan DAPODIK & SIS-SMA1LE (Total {{ number_format($totalStudents) }} Siswa)</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('master.students') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5" title="Buka Master Data Siswa untuk mengedit biodata lengkap">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Kelola di Master Data</span>
                </a>
                <form action="{{ route('tu.kesiswaan.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN / NIK..." class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 w-48">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">Filter</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NISN / NIK</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">L/P</th>
                        <th class="py-3 px-4">TTL & Asal Sekolah</th>
                        <th class="py-3 px-4">Alamat / Domisili</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($students as $s)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $s->name }}
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                            <div>NISN: {{ $s->nisn ?: '-' }}</div>
                            <div class="text-[10px] text-slate-400">NIK: {{ $s->nik ?: '-' }}</div>
                        </td>
                        <td class="py-3 px-4 font-semibold text-blue-600 dark:text-blue-400">
                            {{ $s->schoolClass?->name ?: 'Belum Ada' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->gender === 'L' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' : 'bg-pink-500/10 text-pink-600 dark:text-pink-400' }}">
                                {{ $s->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                            <div>{{ $s->birth_place ?: '-' }}, {{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->translatedFormat('d M Y') : '-' }}</div>
                            @if($s->school_origin)
                                <div class="text-[10px] text-slate-400 font-medium">Asal: {{ $s->school_origin }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            <div>{{ $s->residence_district ?: ($s->address ?: '-') }}</div>
                            @if($s->father_name || $s->mother_name)
                                <div class="text-[10px] text-slate-400">Ortu: {{ $s->father_name ?: ($s->mother_name ?: '-') }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $s->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400' }}">
                                {{ $s->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <a href="{{ route('buku-induk.show', $s) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 font-bold hover:bg-sky-100 transition text-[11px]">
                                Buku Induk
                            </a>
                            <a href="{{ route('reports.student.dossier', $s) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold hover:bg-slate-200 transition text-[11px]">
                                Dossier
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="max-w-md mx-auto space-y-2">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Data Administrasi Kesiswaan Masih Kosong</div>
                                <p class="text-xs text-slate-400">Belum ada siswa yang didaftarkan ke meja kerja Administrasi Kesiswaan atau data baru saja dikosongkan.</p>
                                <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST" class="pt-2">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Tarik Data dari Master Siswa & RFID ({{ $masterAvailableCount }} Siswa)</span>
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
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection

