@extends('layouts.app')

@section('title', 'Dashboard Manajemen Terpadu')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner & AI Trigger -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 rounded-2xl p-5 sm:p-6 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[11px] font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Aktif &bull; {{ $school->province ?? 'Provinsi Jawa Barat' }} &bull; {{ $school->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V' }} &bull; {{ $school->city ?? 'Kabupaten Sukabumi' }}
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ $user->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                Pusat kendali dan administrasi ketatausahaan digital {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}.
                <span class="text-slate-400 block sm:inline mt-0.5 sm:mt-0 sm:ml-2 font-medium">Kepala Sekolah: <strong class="text-white">{{ $school->headmaster_name ?? 'Pian Sopian, S.Pd' }}</strong> (NIP: {{ $school->headmaster_nip ?? '-' }})</span>
            </p>
        </div>

        <!-- Prominent AI Analytics Button -->
        <div class="flex items-center gap-3">
            <button onclick="triggerAiAnalysis('executive_headmaster')" type="button" class="inline-flex items-center px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-500/30 transition duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                <svg class="w-4 sm:w-5 h-4 sm:h-5 mr-2 text-purple-200 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>ANALISIS EKSEKUTIF DENGAN AI</span>
            </button>
        </div>
    </div>

    @include('tu.partials.nav')

    <!-- Quick Action Hub (Pusat Akses Satu Pintu Administrasi SMA) -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-4 bg-indigo-600 rounded-sm"></span>
                Pusat Akses Cepat Satu Pintu Administrasi SMA
            </h2>
            <span class="text-[11px] text-slate-400 hidden sm:inline">Pintasan Layanan Harian Tata Usaha</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
            <a href="{{ route('letters.incoming.create') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-blue-50/80 border border-slate-200/80 hover:border-blue-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-blue-100 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-blue-700 leading-tight">Surat Masuk</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Catat Agenda</span>
            </a>
            <a href="{{ route('letters.outgoing.create') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-sky-50/80 border border-slate-200/80 hover:border-sky-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-sky-100 group-hover:bg-sky-600 text-sky-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-sky-700 leading-tight">Surat Keluar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Buat Draft</span>
            </a>
            <a href="{{ route('finance.transactions.index') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-rose-50/80 border border-slate-200/80 hover:border-rose-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-rose-100 group-hover:bg-rose-600 text-rose-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-rose-700 leading-tight">Kas BKU</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Catat Transaksi</span>
            </a>
            <a href="{{ route('warehouse.receipts.index') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-amber-50/80 border border-slate-200/80 hover:border-amber-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-amber-100 group-hover:bg-amber-600 text-amber-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-amber-700 leading-tight">BAST Gudang</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Barang Masuk</span>
            </a>
            <a href="{{ route('guests.kiosk') }}" target="_blank" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/80 border border-slate-200/80 hover:border-emerald-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-700 leading-tight">Kiosk Tamu</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Buku Tamu</span>
            </a>
            <a href="{{ route('students.index') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-indigo-50/80 border border-slate-200/80 hover:border-indigo-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-indigo-700 leading-tight">Data Siswa</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Profil & Ringkasan</span>
            </a>
            <a href="{{ route('duk.index') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-teal-50/80 border border-slate-200/80 hover:border-teal-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-teal-100 group-hover:bg-teal-600 text-teal-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-teal-700 leading-tight">DUK Pegawai</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Pendidik & Tendik</span>
            </a>
            <a href="{{ route('sarpras.assets.index') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-purple-50/80 border border-slate-200/80 hover:border-purple-300 transition text-center shadow-2xs card-hover">
                <div class="w-8 h-8 rounded-lg bg-purple-100 group-hover:bg-purple-600 text-purple-600 group-hover:text-white flex items-center justify-center mb-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-purple-700 leading-tight">Aset KIB & KIR</span>
                <span class="text-[10px] text-slate-400 mt-0.5">SIPD e-BMD</span>
            </a>
        </div>
    </div>

    <!-- 1. Kesiswaan KPI Cards & Administrative Summary -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
            <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-4 bg-blue-600 rounded-sm"></span>
                Administrasi Kesiswaan & Peserta Didik
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.demographics') }}" class="inline-flex items-center px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-lg transition card-hover">
                    <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Demografi Siswa Lengkap &rarr;
                </a>
                <a href="{{ route('students.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                    <span>Data Siswa</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Total Siswa</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $totalStudents }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">100% Terdaftar</div>
            </div>
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Siswa Aktif</div>
                <div class="text-xl sm:text-2xl font-black text-blue-600 mt-1">{{ $activeStudents }}</div>
                <div class="text-[11px] text-slate-500 mt-1">{{ $totalStudents > 0 ? round(($activeStudents/$totalStudents)*100) : 0 }}% Status Aktif</div>
            </div>
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Laki-Laki</div>
                <div class="text-xl sm:text-2xl font-black text-slate-800 mt-1">{{ $maleStudents }}</div>
                <div class="text-[11px] text-blue-600 font-medium mt-1">{{ $activeStudents > 0 ? round(($maleStudents/$activeStudents)*100) : 0 }}% Laki-laki</div>
            </div>
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Perempuan</div>
                <div class="text-xl sm:text-2xl font-black text-slate-800 mt-1">{{ $femaleStudents }}</div>
                <div class="text-[11px] text-rose-500 font-medium mt-1">{{ $activeStudents > 0 ? round(($femaleStudents/$activeStudents)*100) : 0 }}% Perempuan</div>
            </div>
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Prestasi Tercatat</div>
                <div class="text-xl sm:text-2xl font-black text-amber-500 mt-1">{{ $totalAchievements }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-1">Akademik & Non</div>
            </div>
            <div class="bg-white rounded-xl p-3.5 sm:p-4 shadow-sm border border-slate-200 card-hover">
                <div class="text-[11px] font-medium text-slate-500">Pelanggaran / Poin</div>
                <div class="text-xl sm:text-2xl font-black text-rose-600 mt-1">{{ $totalViolations }}</div>
                <div class="text-[11px] text-rose-500 font-medium mt-1">Dalam Pembinaan</div>
            </div>
        </div>

        <!-- Realtime Demographics Quick Strip (Per Tingkat & Bantuan PIP) -->
        <div class="mt-3 p-3 bg-gradient-to-r from-blue-50/70 via-indigo-50/60 to-purple-50/70 border border-blue-100 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-700 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Komposisi Tingkat:
                </span>
                <span class="px-2 py-0.5 rounded-lg bg-white border border-blue-200 text-blue-900 font-semibold text-[11px] shadow-2xs">
                    Kelas X: <strong class="text-blue-700">{{ $classXCount ?? 0 }}</strong> Siswa
                </span>
                <span class="px-2 py-0.5 rounded-lg bg-white border border-indigo-200 text-indigo-900 font-semibold text-[11px] shadow-2xs">
                    Kelas XI: <strong class="text-indigo-700">{{ $classXICount ?? 0 }}</strong> Siswa
                </span>
                <span class="px-2 py-0.5 rounded-lg bg-white border border-purple-200 text-purple-900 font-semibold text-[11px] shadow-2xs">
                    Kelas XII: <strong class="text-purple-700">{{ $classXIICount ?? 0 }}</strong> Siswa
                </span>
                <span class="px-2 py-0.5 rounded-lg bg-white border border-amber-200 text-amber-900 font-semibold text-[11px] shadow-2xs">
                    Penerima PIP: <strong class="text-amber-700">{{ $pipBeneficiaries ?? 0 }}</strong> Siswa
                </span>
            </div>
            <a href="{{ route('students.demographics') }}" class="font-bold text-indigo-700 hover:text-indigo-900 flex items-center gap-1 text-[11px]">
                <span>Buka Analisis Demografi Lengkap (Peta Sebaran, Usia & Moda Transportasi)</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Sub-bar Kesiswaan: Mutasi, Ijazah, Beasiswa, Legalisir, Cetak Absen -->
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <a href="{{ route('mutations.index') }}" class="p-3 bg-white rounded-xl border border-slate-200 hover:border-blue-300 transition flex items-center justify-between shadow-2xs">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-lg bg-blue-50 text-blue-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </span>
                    <div>
                        <div class="text-[11px] text-slate-500 font-medium">Buku Mutasi Siswa</div>
                        <div class="text-xs font-bold text-slate-800">{{ $mutationsCount }} Berkas Terdata</div>
                    </div>
                </div>
                <span class="text-xs text-blue-600 font-bold">&rarr;</span>
            </a>
            <a href="{{ route('students.diplomas.index') }}" class="p-3 bg-white rounded-xl border border-slate-200 hover:border-emerald-300 transition flex items-center justify-between shadow-2xs">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    <div>
                        <div class="text-[11px] text-slate-500 font-medium">Penyerahan Ijazah</div>
                        <div class="text-xs font-bold text-slate-800">{{ $diplomasCount }} Terdistribusi</div>
                    </div>
                </div>
                <span class="text-xs text-emerald-600 font-bold">&rarr;</span>
            </a>
            <a href="{{ route('scholarships.index') }}" class="p-3 bg-white rounded-xl border border-slate-200 hover:border-amber-300 transition flex items-center justify-between shadow-2xs">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-lg bg-amber-50 text-amber-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <div>
                        <div class="text-[11px] text-slate-500 font-medium">Beasiswa & PIP</div>
                        <div class="text-xs font-bold text-slate-800">{{ $scholarshipsCount }} Penerima</div>
                    </div>
                </div>
                <span class="text-xs text-amber-600 font-bold">&rarr;</span>
            </a>
            <a href="{{ route('tu.kesiswaan.legalisir.index') }}" class="p-3 bg-gradient-to-r from-amber-50/70 to-orange-50/70 rounded-xl border-2 border-amber-300 hover:border-amber-500 transition flex items-center justify-between shadow-2xs">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-lg bg-amber-500 text-white shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <div>
                        <div class="text-[11px] text-amber-900 font-bold">Legalisir Dokumen</div>
                        <div class="text-xs font-extrabold text-amber-800">Buku & Resi Siswa</div>
                    </div>
                </div>
                <span class="text-xs text-amber-700 font-bold">&rarr;</span>
            </a>
            <a href="{{ route('tu.kesiswaan.absen_manual.index') }}" class="p-3 bg-gradient-to-r from-emerald-50/70 to-teal-50/70 rounded-xl border-2 border-emerald-300 hover:border-emerald-500 transition flex items-center justify-between shadow-2xs">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-lg bg-emerald-500 text-white shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </span>
                    <div>
                        <div class="text-[11px] text-emerald-900 font-bold">Cetak Absen Manual</div>
                        <div class="text-xs font-extrabold text-emerald-800">Format SIKATAS</div>
                    </div>
                </div>
                <span class="text-xs text-emerald-700 font-bold">&rarr;</span>
            </a>
        </div>
    </div>

    <!-- 2. Kepegawaian & Keuangan Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kepegawaian Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-4 bg-emerald-600 rounded-sm"></span>
                        Kepegawaian (Pendidik & Tendik)
                    </h2>
                    <a href="{{ route('duk.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">Lihat DUK &rarr;</a>
                </div>
                <div class="grid grid-cols-3 gap-3 my-4 text-center">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-xs text-slate-500">Total PTK</div>
                        <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $totalEmployees }}</div>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="text-xs text-emerald-700">Guru</div>
                        <div class="text-xl font-bold text-emerald-800 mt-0.5">{{ $totalTeachers }}</div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="text-xs text-blue-700">Tenaga Kependidikan</div>
                        <div class="text-xl font-bold text-blue-800 mt-0.5">{{ $totalStaff }}</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-600">Pegawai Negeri Sipil (PNS)</span>
                        <span class="font-bold text-slate-800">{{ $pnsEmployees }} Orang</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-600">Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)</span>
                        <span class="font-bold text-slate-800">{{ $pppkEmployees }} Orang</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-600">Guru Tidak Tetap / Pegawai Honorer (GTT/PTT)</span>
                        <span class="font-bold text-slate-800">{{ $honorerEmployees }} Orang</span>
                    </div>
                    <!-- Status Buku Kontrol Kepegawaian -->
                    <div class="pt-2">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Buku Kontrol & Monitoring PTK</div>
                        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                            <a href="{{ route('employees.kontrol-pangkat.index') }}" class="p-2 rounded-lg border flex items-center justify-between transition-colors {{ $kpDueCount > 0 ? 'bg-amber-50/70 border-amber-200 text-amber-900 hover:bg-amber-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                <span class="font-medium">Jatuh Tempo KP</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $kpDueCount > 0 ? 'bg-amber-200 text-amber-900' : 'bg-slate-200 text-slate-600' }}">{{ $kpDueCount }}</span>
                            </a>
                            <a href="{{ route('employees.kontrol-kgb.index') }}" class="p-2 rounded-lg border flex items-center justify-between transition-colors {{ $kgbDueCount > 0 ? 'bg-blue-50/70 border-blue-200 text-blue-900 hover:bg-blue-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                <span class="font-medium">Jatuh Tempo KGB</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $kgbDueCount > 0 ? 'bg-blue-200 text-blue-900' : 'bg-slate-200 text-slate-600' }}">{{ $kgbDueCount }}</span>
                            </a>
                            <a href="{{ route('employees.kontrol-cuti.index') }}" class="p-2 rounded-lg border flex items-center justify-between transition-colors {{ $activeLeavesCount > 0 ? 'bg-purple-50/70 border-purple-200 text-purple-900 hover:bg-purple-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                <span class="font-medium">Cuti Hari Ini</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $activeLeavesCount > 0 ? 'bg-purple-200 text-purple-900' : 'bg-slate-200 text-slate-600' }}">{{ $activeLeavesCount }}</span>
                            </a>
                            <a href="{{ route('employees.kontrol-pppk.index') }}" class="p-2 rounded-lg border flex items-center justify-between transition-colors {{ $pppkDueCount > 0 ? 'bg-rose-50/70 border-rose-200 text-rose-900 hover:bg-rose-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                <span class="font-medium">Evaluasi PPPK</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $pppkDueCount > 0 ? 'bg-rose-200 text-rose-900' : 'bg-slate-200 text-slate-600' }}">{{ $pppkDueCount }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <a href="{{ route('employees.buku-induk.index') }}" class="font-semibold text-emerald-700 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Buku Induk Pegawai
                    </a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('dso.index') }}" class="font-semibold text-slate-600 hover:text-blue-600">
                        Bagan DSO
                    </a>
                </div>
                <button onclick="triggerAiAnalysis('kepegawaian')" class="text-purple-600 hover:text-purple-700 font-bold text-xs flex items-center gap-1 cursor-pointer">
                    Analisis AI &rarr;
                </button>
            </div>
        </div>

        <!-- Keuangan Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-4 bg-amber-500 rounded-sm"></span>
                        Keuangan & Serapan Kas BKU
                    </h2>
                    <a href="{{ route('finance.bku.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">Buka BKU &rarr;</a>
                </div>
                
                <!-- Financial Progress Bar -->
                <div class="my-4">
                    <div class="flex justify-between text-xs mb-1.5 font-medium">
                        <span class="text-slate-600">Serapan Pagu Anggaran</span>
                        <span class="text-blue-600 font-bold">{{ $budgetAbsorptionPct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, $budgetAbsorptionPct) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                        <span>Realisasi: Rp {{ number_format($totalBudgetRealized, 0, ',', '.') }}</span>
                        <span>Pagu: Rp {{ number_format($totalBudgetAllocation, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- 3 Saldo Cards: Total, Tunai, Bank -->
                <div class="grid grid-cols-3 gap-2 text-center mb-3">
                    <div class="p-2.5 bg-blue-50/70 rounded-xl border border-blue-100">
                        <div class="text-[10px] text-blue-700 font-medium">Total Saldo Kas</div>
                        <div class="text-xs sm:text-sm font-bold text-blue-900 mt-0.5 truncate" title="Rp {{ number_format($cashBalance, 0, ',', '.') }}">Rp {{ number_format($cashBalance, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2.5 bg-emerald-50/70 rounded-xl border border-emerald-100">
                        <div class="text-[10px] text-emerald-700 font-medium">Kas Tunai (Bendahara)</div>
                        <div class="text-xs sm:text-sm font-bold text-emerald-900 mt-0.5 truncate" title="Rp {{ number_format($cashOnHand, 0, ',', '.') }}">Rp {{ number_format($cashOnHand, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2.5 bg-indigo-50/70 rounded-xl border border-indigo-100">
                        <div class="text-[10px] text-indigo-700 font-medium">Rekening Bank</div>
                        <div class="text-xs sm:text-sm font-bold text-indigo-900 mt-0.5 truncate" title="Rp {{ number_format($bankBalance, 0, ',', '.') }}">Rp {{ number_format($bankBalance, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    <div class="p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100">
                        <div class="text-slate-500 text-[11px]">Total Penerimaan Kas</div>
                        <div class="text-xs sm:text-sm font-bold text-emerald-700 mt-0.5">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2.5 bg-rose-50/60 rounded-xl border border-rose-100">
                        <div class="text-slate-500 text-[11px]">Total Pengeluaran Kas</div>
                        <div class="text-xs sm:text-sm font-bold text-rose-700 mt-0.5">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                    </div>
                </div>

                @if($pendingReceiptsCount > 0)
                <div class="mt-3 p-2.5 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2 text-amber-800">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-medium text-[11px]"><strong class="font-bold">{{ $pendingReceiptsCount }} BAST</strong> Penerimaan Gudang Menunggu SPJ Kasir</span>
                    </div>
                    <a href="{{ route('finance.transactions.index') }}" class="text-blue-600 font-bold hover:underline shrink-0 text-[11px]">SPJ Sekarang &rarr;</a>
                </div>
                @endif
            </div>

            <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="{{ route('finance.subsidiary.index') }}" class="font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Buku Pembantu Kas & Bank
                </a>
                <button onclick="triggerAiAnalysis('keuangan')" class="text-purple-600 hover:text-purple-700 font-bold text-xs flex items-center gap-1 cursor-pointer">
                    Analisis Realisasi AI &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Persuratan, Sarpras, Gudang & Arsip 4-Col Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Persuratan Card -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">Persuratan Dinas</span>
                    <span class="p-1 rounded bg-blue-50 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                </div>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Surat Masuk:</span> <span class="font-bold">{{ $incomingLettersCount }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Surat Keluar:</span> <span class="font-bold">{{ $outgoingLettersCount }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Disposisi Berjalan:</span> <span class="font-bold text-amber-600">{{ $pendingDispositionsCount }}</span></div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-[11px]">
                <a href="{{ route('letters.incoming.index') }}" class="text-blue-600 font-semibold hover:underline">Kelola Surat &rarr;</a>
                <button onclick="triggerAiAnalysis('persuratan')" class="text-purple-600 font-bold hover:underline cursor-pointer">AI Ringkas</button>
            </div>
        </div>

        <!-- Sarpras & KIB-KIR Card (SIPD e-BMD) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">Sarpras & Aset BMD</span>
                        <div class="text-[10px] text-emerald-600 font-bold">SIPD e-BMD Permendagri 108</div>
                    </div>
                    <span class="p-1 rounded bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                </div>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Total Aset KIB:</span> <span class="font-bold">{{ $totalAssets }} Item</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Nilai Aset:</span> <span class="font-bold text-emerald-700">Rp {{ number_format($totalAssetValuation, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ruang Ber-KIR:</span> <a href="{{ route('sarpras.rooms.index') }}" class="font-bold text-blue-600 hover:underline">{{ $totalRoomsWithKir }} Ruang (Kelola)</a></div>
                    <div class="flex justify-between"><span class="text-slate-500">Kondisi Baik:</span> <span class="font-bold text-emerald-600">{{ $assetsGood }} (RR:{{ $assetsMinorDamage }}, RB:{{ $assetsHeavyDamage }})</span></div>
                    <div class="pt-1.5 border-t border-slate-100 flex flex-wrap gap-1 text-[10px]">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">A:{{ $kibCounts['A'] ?? 0 }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-mono">B:{{ $kibCounts['B'] ?? 0 }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono">C:{{ $kibCounts['C'] ?? 0 }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-mono">E:{{ $kibCounts['E'] ?? 0 }}</span>
                    </div>
                    <!-- SIPD e-BMD Catalog Shortcut -->
                    <a href="{{ route('sarpras.sipd-bmd.index') }}" class="mt-2 p-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg flex items-center justify-between text-[11px] text-emerald-900 transition">
                        <span class="font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Katalog Kode SIPD e-BMD
                        </span>
                        <span class="px-1.5 py-0.2 bg-emerald-200 text-emerald-800 rounded font-bold text-[10px]">{{ $totalSipdCodes ?? 100 }}+ Kode</span>
                    </a>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-[11px]">
                <div class="flex items-center space-x-1.5 flex-wrap">
                    <a href="{{ route('sarpras.rooms.index') }}" class="text-blue-700 font-bold hover:underline" title="Kelola & Tambah Ruang Baru">+Ruang</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('sarpras.assets.index') }}" class="text-blue-600 font-semibold hover:underline">KIB</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('sarpras.kir.index') }}" class="text-emerald-600 font-semibold hover:underline">KIR</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('sarpras.bibi.index') }}" class="text-indigo-600 font-semibold hover:underline" title="Buku Induk Inventaris (BII)">BII</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('sarpras.disposals.index') }}" class="text-slate-600 font-semibold hover:underline" title="Usulan Afkir">Afkir ({{ $pendingDisposalsCount }})</a>
                </div>
                <button onclick="triggerAiAnalysis('sarpras')" class="text-purple-600 font-bold hover:underline cursor-pointer">AI Sarpras</button>
            </div>
        </div>

        <!-- Gudang Card -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">Gudang & Logistik</span>
                    <span class="p-1 rounded bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </span>
                </div>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Katalog Barang:</span> <span class="font-bold">{{ $totalGoods }} Item</span></div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Stok Kritis:</span> 
                        <span class="font-bold {{ $lowStockGoods > 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $lowStockGoods }} Item</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Peminjaman Aktif:</span> <span class="font-bold text-amber-600">{{ $activeBorrowingsCount }} Peminjaman</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">BAST Masuk:</span> <span class="font-bold text-emerald-600">Habis Pakai & BMD</span></div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-[11px]">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('warehouse.goods.index') }}" class="text-blue-600 font-semibold hover:underline">Stok</a>
                    <span class="text-slate-300">&bull;</span>
                    <a href="{{ route('warehouse.receipts.index') }}" class="text-emerald-600 font-semibold hover:underline">BAST Masuk</a>
                </div>
                <button onclick="triggerAiAnalysis('gudang')" class="text-purple-600 font-bold hover:underline cursor-pointer">AI Stok</button>
            </div>
        </div>

        <!-- Arsip Card -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col justify-between card-hover">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">Kearsipan Digital</span>
                    <span class="p-1 rounded bg-purple-50 text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </span>
                </div>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Arsip Aktif:</span> <span class="font-bold text-emerald-600">{{ $activeArchives }} Berkas</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Total Berkas:</span> <span class="font-bold">{{ $totalArchives }} Dokumen</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Retensi:</span> <span class="font-bold text-slate-600">Terkontrol Otomatis</span></div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-[11px]">
                <a href="{{ route('archives.index') }}" class="text-blue-600 font-semibold hover:underline">Buka Arsip &rarr;</a>
                <button onclick="triggerAiAnalysis('arsip')" class="text-purple-600 font-bold hover:underline cursor-pointer">AI Arsip</button>
            </div>
        </div>
    </div>

    <!-- 4. Realtime Operational Activity: Transaksi Kas & Logistik BAST -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi Kas Terbaru (SPJ BKU) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Transaksi Kas BKU Terkini (Realtime)</h3>
                </div>
                <a href="{{ route('finance.transactions.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua Transaksi</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentCashTransactions as $tx)
                    <div class="p-3.5 hover:bg-slate-50/80 transition flex items-center justify-between text-xs">
                        <div class="space-y-0.5 max-w-[70%]">
                            <div class="font-bold text-slate-900 truncate">{{ $tx->description }}</div>
                            <div class="text-[10px] text-slate-400">
                                No: <span class="font-mono text-slate-600">{{ $tx->proof_number }}</span> &bull; {{ $tx->transaction_date->format('d M Y') }} &bull; {{ ucfirst($tx->payment_method) }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-bold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold {{ $tx->type === 'income' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $tx->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">Belum ada transaksi kas tercatat.</div>
                @endforelse
            </div>
        </div>

        <!-- BAST Gudang Terbaru & Peringatan Stok Kritis -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Penerimaan Barang (BAST Gudang)</h3>
                    </div>
                    <a href="{{ route('warehouse.receipts.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua BAST</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentReceipts as $rec)
                        <div class="p-3.5 hover:bg-slate-50/80 transition flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="font-bold text-slate-900">{{ $rec->receipt_number }}</div>
                                <div class="text-[10px] text-slate-500">Penyedia: <span class="font-semibold text-slate-700">{{ $rec->supplier_name ?: '-' }}</span> &bull; {{ $rec->receipt_date->format('d M Y') }}</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-bold text-slate-800">Rp {{ number_format($rec->total_amount, 0, ',', '.') }}</div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $rec->is_spjed ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $rec->is_spjed ? 'SPJ Selesai' : 'Belum SPJ' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">Belum ada dokumen penerimaan barang.</div>
                    @endforelse
                </div>
            </div>

            <!-- Stok Kritis Alert List if exists -->
            @if($lowStockList->isNotEmpty())
            <div class="p-3.5 bg-rose-50/60 border-t border-rose-100">
                <div class="text-[11px] font-bold text-rose-800 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Peringatan Stok Menipis / Kritis:</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($lowStockList as $item)
                        <span class="px-2 py-0.5 rounded-lg bg-white border border-rose-200 text-rose-900 text-[10px] font-medium shadow-2xs">
                            {{ $item->name }} (Sisa: <strong class="text-rose-600">{{ $item->current_stock }} {{ $item->unit }}</strong>)
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- 5. Tables: Recent Incoming Letters & Pending Dispositions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Surat Masuk Terbaru -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800">Surat Masuk Terbaru</h3>
                <a href="{{ route('letters.incoming.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua Surat</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentIncomingLetters as $letter)
                    <div class="p-3.5 hover:bg-slate-50 transition flex items-start justify-between">
                        <div class="space-y-1">
                            <div class="font-bold text-xs text-slate-800">{{ $letter->subject }}</div>
                            <div class="text-[11px] text-slate-500">Dari: <span class="font-medium text-slate-700">{{ $letter->sender }}</span> &bull; No: {{ $letter->letter_number }}</div>
                            <div class="text-[10px] text-slate-400">Diterima: {{ $letter->received_date->format('d M Y') }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $letter->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $letter->status }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">Belum ada surat masuk tercatat.</div>
                @endforelse
            </div>
        </div>

        <!-- Disposisi Berjalan -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-800">Disposisi Menunggu Tindak Lanjut</h3>
                <a href="{{ route('dispositions.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Semua Disposisi</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($pendingDispositions as $disp)
                    <div class="p-3.5 hover:bg-slate-50 transition flex items-start justify-between">
                        <div class="space-y-1">
                            <div class="font-bold text-xs text-slate-800">{{ $disp->incomingLetter->subject ?? 'Surat Dinas' }}</div>
                            <div class="text-[11px] text-slate-600">Tujuan: <span class="font-semibold text-blue-600">{{ $disp->target_role_or_user }}</span></div>
                            <div class="text-[11px] text-slate-500 italic">"{{ Str::limit($disp->instructions, 70) }}"</div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 shrink-0">
                            {{ $disp->status }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-emerald-600 font-medium">Seluruh lembar disposisi telah ditindaklanjuti.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 6. Buku Tamu & Kunjungan Terpadu (Unified Digital Guestbook) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </span>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                        Buku Tamu Digital & Kunjungan Sekolah
                        @if($activeGuestsCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                {{ $activeGuestsCount }} Tamu Aktif
                            </span>
                        @endif
                    </h3>
                    <p class="text-[11px] text-slate-400">Monitoring kehadiran tamu, foto selfie webcam, tanda tangan digital, & tiket barcode check-out</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="triggerAiAnalysis('tamu')" type="button" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    AI Analisis Tamu
                </button>
                <a href="{{ route('guests.kiosk') }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    Buka Kiosk Tamu
                </a>
                <a href="{{ route('guests.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Semua Tamu &rarr;
                </a>
            </div>
        </div>

        <!-- Quick Summary Cards for Guestbook -->
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/50 border-b border-slate-100">
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="text-[11px] text-slate-500 font-medium">Tamu Hari Ini</div>
                    <div class="text-xl font-black text-slate-800 mt-0.5">{{ $totalGuestsToday }} Orang</div>
                </div>
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                    {{ now()->format('d M') }}
                </span>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="text-[11px] text-slate-500 font-medium">Sedang Bertamu (Di Sekolah)</div>
                    <div class="text-xl font-black text-emerald-600 mt-0.5">{{ $activeGuestsCount }} Orang</div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span>
                    Checked-In
                </span>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="text-[11px] text-slate-500 font-medium">Total Seluruh Kunjungan</div>
                    <div class="text-xl font-black text-slate-800 mt-0.5">{{ $totalGuestsAll }} Tamu</div>
                </div>
                <a href="{{ route('guests.report.print') }}" target="_blank" class="text-xs text-blue-600 hover:underline font-semibold flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Buku Tamu
                </a>
            </div>
        </div>

        <!-- Recent Visitors Mini Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[600px]">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Tamu & Tiket</th>
                        <th class="px-5 py-3">Instansi / Asal</th>
                        <th class="px-5 py-3">Tujuan / Keperluan</th>
                        <th class="px-5 py-3">Jam Masuk</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentGuests as $guest)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $guest->selfie_url }}" alt="{{ $guest->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'36\' height=\'36\' fill=\'%236366f1\'><rect width=\'36\' height=\'36\' rx=\'18\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ffffff\' font-family=\'sans-serif\' font-size=\'14\' font-weight=\'bold\'>{{ strtoupper(substr($guest->name, 0, 2)) }}</text></svg>';">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $guest->name }}</div>
                                        <div class="text-[10px] font-mono text-blue-600 font-semibold">{{ $guest->ticket_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ $guest->institution ?: 'Pribadi / Perorangan' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $guest->phone_number ?: '-' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-800">{{ $guest->destination_department ?: $guest->destination_person }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ Str::limit($guest->purpose, 40) }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ $guest->check_in_at?->format('H:i') }} WIB</div>
                                <div class="text-[10px] text-slate-400">{{ $guest->check_in_at?->format('d M Y') }}</div>
                            </td>
                            <td class="px-5 py-3">
                                @if($guest->status === 'checked_in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span>
                                        Sedang Bertamu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('guests.show', $guest) }}" class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-600 text-[11px] font-medium transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-400">
                                Belum ada riwayat kunjungan tamu. <a href="{{ route('guests.kiosk') }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Buka Kiosk Tamu</a> untuk melayani tamu pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal AI Analysis Result & Loading -->
<div id="aiModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-purple-900 to-indigo-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-purple-500/30 border border-purple-400/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">SIKATAS AI ASSISTANT &bull; Analisis Eksekutif</h3>
                    <p id="aiModalSubtitle" class="text-[11px] text-purple-200/80">Memproses data agregat live sistem...</p>
                </div>
            </div>
            <button onclick="closeAiModal()" class="text-purple-200 hover:text-white p-1 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="aiModalBody" class="p-6 overflow-y-auto space-y-5 text-xs text-slate-700 leading-relaxed">
            <!-- Loading Indicator -->
            <div id="aiLoading" class="py-16 text-center space-y-3">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-purple-600 border-t-transparent"></div>
                <div class="font-bold text-sm text-slate-800">Sedang Menganalisis Seluruh Data Sekolah...</div>
                <div class="text-xs text-slate-500">Mengkalkulasi rasio kesiswaan, serapan anggaran BKU, status disposisi, dan aset inventaris.</div>
            </div>

            <!-- Content Area (populated dynamically) -->
            <div id="aiContent" class="hidden space-y-4"></div>
        </div>

        <!-- Modal Footer -->
        <div id="aiModalFooter" class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0 hidden">
            <span class="text-[11px] text-slate-400">Data bersumber dari database operasional SIKATAS SMA1LE</span>
            <div class="flex items-center space-x-2">
                <button onclick="copyAiReport()" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    Salin Teks
                </button>
                <a id="aiPrintBtn" href="#" target="_blank" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    Cetak / Unduh PDF
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentReportText = '';

    function triggerAiAnalysis(moduleName = 'executive_headmaster') {
        const modal = document.getElementById('aiModal');
        const loading = document.getElementById('aiLoading');
        const content = document.getElementById('aiContent');
        const footer = document.getElementById('aiModalFooter');
        const subtitle = document.getElementById('aiModalSubtitle');

        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        footer.classList.add('hidden');
        subtitle.innerText = 'Menganalisis modul ' + moduleName + '...';

        fetch('{{ route('ai.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                module: moduleName,
                period: 'Semester Genap 2025/2026'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                const d = res.data;
                loading.classList.add('hidden');
                content.classList.remove('hidden');
                footer.classList.remove('hidden');
                subtitle.innerText = 'Laporan Selesai Dihasilkan &bull; ' + d.period;

                document.getElementById('aiPrintBtn').href = `/ai/reports/${d.id}/print`;

                currentReportText = `=== RINGKASAN EKSEKUTIF ===\n${d.executive_summary}\n\n=== TEMUAN UTAMA ===\n${d.key_findings}\n\n=== KESIMPULAN ===\n${d.conclusions}\n\n=== PERMASALAHAN ===\n${d.issues_identified}\n\n=== REKOMENDASI ===\n${d.recommendations}\n\n=== RENCANA TINDAK LANJUT ===\n${d.action_plan}`;

                content.innerHTML = `
                    <div class="p-4 bg-purple-50/70 border border-purple-200 rounded-xl">
                        <div class="font-bold text-xs uppercase tracking-wider text-purple-900 mb-1">1. Ringkasan Eksekutif</div>
                        <p class="text-purple-950 font-medium">${d.executive_summary}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="font-bold text-xs uppercase tracking-wider text-blue-900 mb-1.5">2. Temuan Utama</div>
                            <div class="whitespace-pre-line text-slate-700 font-medium">${d.key_findings}</div>
                        </div>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="font-bold text-xs uppercase tracking-wider text-emerald-900 mb-1.5">3. Kesimpulan</div>
                            <p class="text-slate-700 font-medium">${d.conclusions}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-rose-50/70 border border-rose-200 rounded-xl">
                        <div class="font-bold text-xs uppercase tracking-wider text-rose-900 mb-1.5">4. Permasalahan & Titik Perhatian</div>
                        <div class="whitespace-pre-line text-rose-950 font-medium">${d.issues_identified}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl">
                            <div class="font-bold text-xs uppercase tracking-wider text-amber-900 mb-1.5">5. Rekomendasi Tindakan</div>
                            <div class="whitespace-pre-line text-amber-950 font-medium">${d.recommendations}</div>
                        </div>
                        <div class="p-4 bg-indigo-50/70 border border-indigo-200 rounded-xl">
                            <div class="font-bold text-xs uppercase tracking-wider text-indigo-900 mb-1.5">6. Rencana Tindak Lanjut</div>
                            <div class="whitespace-pre-line text-indigo-950 font-medium">${d.action_plan}</div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => {
            loading.innerHTML = '<div class="text-rose-600 font-bold">Terjadi kesalahan saat memproses analisis data. Silakan coba kembali.</div>';
        });
    }

    function closeAiModal() {
        document.getElementById('aiModal').classList.add('hidden');
    }

    function copyAiReport() {
        navigator.clipboard.writeText(currentReportText).then(() => {
            alert('Laporan analisis AI berhasil disalin ke clipboard!');
        });
    }
</script>
@endpush
@endsection
