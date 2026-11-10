@extends('layouts.app')

@section('title', 'Raport Digital Analisis Perkembangan (AI)')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper { width: 100%; flex: 1 1 auto; }
    .ts-wrapper.single .ts-control {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        padding: 0 0.875rem;
        height: 2.75rem;
        background-color: #f8fafc;
        cursor: pointer;
        display: flex;
        align-items: center;
        box-shadow: none;
        box-sizing: border-box;
    }
    .dark .ts-wrapper.single .ts-control {
        background-color: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }
    .ts-wrapper.single .ts-control input {
        color: inherit;
        font-size: 0.75rem;
    }
    .ts-dropdown {
        font-size: 0.75rem;
        border-radius: 0.75rem;
        overflow: hidden;
        z-index: 99999 !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25) !important;
        max-height: 280px !important;
        background: #ffffff !important;
    }
    .dark .ts-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .ts-dropdown .option {
        padding: 8px 12px !important;
        cursor: pointer !important;
        color: #1e293b !important;
    }
    .dark .ts-dropdown .option {
        color: #f1f5f9 !important;
    }
    .ts-dropdown .option:hover, .ts-dropdown .option.active {
        background-color: #e0e7ff !important;
        color: #3730a3 !important;
    }
    .dark .ts-dropdown .option:hover, .dark .ts-dropdown .option.active {
        background-color: #334155 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')

<div class="space-y-6 max-w-7xl 2xl:max-w-[1600px] mx-auto pb-12">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shrink-0">✓</span>
                <span class="text-xs font-bold leading-relaxed">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold px-2 py-1">✕</button>
        </div>
    @endif

    <!-- Header Raport Digital -->
    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-800/40 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-bold border border-indigo-500/30 mb-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    8 PILAR STANDAR PENILAIAN • RAPORT DIGITAL AI & DOSSIER SISWA
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Raport Digital & Berkas Dossier Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                    Satu kesatuan data dan berkas laporan perkembangan siswa terpadu dengan seluruh fitur standar penilaian: Presensi Harian, Poin Prestasi, Tata Tertib, 7 Karakter Hebat (KAIH), Shalat Dzuhur, Shalat Dhuha, Keputrian Siswi, Tahfidz Juz 30 target kelas, serta Evaluasi & Rekomendasi Berbasis AI.
                </p>
                @if(isset($latestAiRaport) && $latestAiRaport)
                    <div class="mt-3 flex items-center gap-2 flex-wrap text-[11px] text-indigo-200">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-900/60 border border-indigo-700/50 font-medium">
                            <span>🕒 Terakhir Digenerate:</span>
                            <strong class="text-white">{{ $latestAiRaport->generated_at?->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }} WIB</strong>
                        </span>
                        <span class="text-slate-400 font-medium">• Jadwal otomatis: Setiap Sabtu jam 10:00 WIB</span>
                    </div>
                @endif
            </div>

            <!-- Action Hub / Toolbar -->
            <div class="flex flex-col sm:flex-row lg:flex-row items-stretch sm:items-center gap-3 shrink-0">
                @if(!in_array($user->role, ['siswa', 'orang_tua']) && !$user->hasRole('siswa') && !$user->hasRole('orang_tua'))
                    <!-- Pod Aksi AI -->
                    <div class="flex items-center gap-1.5 p-1.5 bg-slate-900/70 backdrop-blur-md border border-indigo-500/20 rounded-2xl shadow-inner">
                        @if($student)
                            <form action="{{ route('raport.generate_single', $student->id) }}" method="POST" onsubmit="return confirm('Generate ulang Raport Digital AI untuk siswa {{ $student->name }} sekarang?');" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-600/30 text-xs flex items-center gap-1.5 transition cursor-pointer"
                                    title="Generate / Perbarui data AI khusus siswa ini">
                                    <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Generate Siswa</span>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('raport.generate_ai') }}" method="POST" onsubmit="return confirm('Mulai proses generate Raport AI terbaru untuk seluruh siswa aktif sekarang? Proses ini merangkum seluruh aspek penilaian.');" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-3.5 py-2 bg-slate-800/80 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white font-bold rounded-xl border border-slate-700/80 text-xs flex items-center gap-1.5 transition cursor-pointer"
                                title="Generate massal seluruh siswa aktif">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                </svg>
                                <span>Generate Semua</span>
                            </button>
                        </form>
                    </div>
                @endif

                @if($student)
                    <!-- Pod Cetak Resmi -->
                    <div class="flex items-center gap-1.5 p-1.5 bg-slate-900/70 backdrop-blur-md border border-emerald-500/20 rounded-2xl shadow-inner">
                        <a href="{{ route('raport.print', ['student' => $student->id, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'a4']) }}" target="_blank"
                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl shadow-md shadow-emerald-600/30 text-xs flex items-center gap-1.5 transition cursor-pointer"
                            title="Buka Dokumen Raport Format A4 Standar">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak A4</span>
                        </a>
                        <a href="{{ route('raport.print', ['student' => $student->id, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'f4']) }}" target="_blank"
                            class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white font-bold rounded-xl shadow-md shadow-sky-600/30 text-xs flex items-center gap-1.5 transition cursor-pointer"
                            title="Buka Dokumen Raport Format F4 / Folio Resmi">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Cetak F4 (Folio)</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian (Nama Siswa, Kelas, dan Rentang Tanggal) -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('raport.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-3.5 items-end text-xs">
            @if(!in_array($user->role, ['siswa', 'orang_tua']))
                <!-- 1. Filter Kelas (2 Cols) -->
                <div class="lg:col-span-2">
                    <label class="block text-slate-600 dark:text-slate-400 font-bold mb-1.5 text-xs">Pilih Kelas</label>
                    @if($user->role === 'wali_kelas')
                        <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full h-11 px-3.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 cursor-not-allowed text-xs flex items-center" readonly>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @else
                        <select name="class_id" onchange="this.form.submit()" class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-800 dark:text-slate-100 text-xs cursor-pointer transition">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- 2. Pencarian Siswa (4 Cols) -->
                <div class="lg:col-span-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-slate-600 dark:text-slate-400 font-bold text-xs">Pilih Siswa</label>
                        <button type="button" onclick="openStudentSearchModal()" title="Pencarian Cepat Siswa (A-Z)" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold flex items-center gap-1 transition cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cari Cepat (A-Z)</span>
                        </button>
                    </div>
                    <select id="student_id_select" name="student_id" onchange="this.form.submit()" class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-medium text-xs">
                        <option value="">-- Cari / Pilih Siswa --</option>
                        @forelse($studentsList as $st)
                            <option value="{{ $st->id }}" {{ ($student && $student->id == $st->id) ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->nis }}) - {{ $st->schoolClass?->name }}
                            </option>
                        @empty
                        @endforelse
                    </select>
                </div>
            @else
                <!-- Siswa / Orang Tua (Terkunci) -->
                <div class="lg:col-span-6">
                    <label class="block text-slate-600 dark:text-slate-400 font-bold mb-1.5 text-xs">Siswa Terdata</label>
                    <div class="w-full h-11 px-3.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-slate-100 text-xs flex items-center justify-between">
                        <span>👤 {{ $student->name ?? 'Siswa' }}</span>
                        <span class="text-indigo-500 font-semibold">Kelas {{ $student->schoolClass?->name ?? '-' }} • NIS: {{ $student->nis ?? '-' }}</span>
                    </div>
                    <input type="hidden" name="student_id" value="{{ $student->id ?? '' }}">
                </div>
            @endif

            <!-- 3. Rentang Tanggal Mulai (2 Cols) -->
            <div class="lg:col-span-2">
                <label class="block text-slate-600 dark:text-slate-400 font-bold mb-1.5 text-xs">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-medium text-slate-800 dark:text-slate-100 text-xs transition">
            </div>

            <!-- 4. Rentang Tanggal Sampai (2 Cols) -->
            <div class="lg:col-span-2">
                <label class="block text-slate-600 dark:text-slate-400 font-bold mb-1.5 text-xs">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-medium text-slate-800 dark:text-slate-100 text-xs transition">
            </div>

            <!-- 5. Tombol Terapkan (2 Cols) -->
            <div class="lg:col-span-2">
                <span class="hidden lg:block text-transparent select-none mb-1.5 text-xs font-bold leading-normal">&nbsp;</span>
                <button type="submit" class="w-full h-11 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-bold rounded-xl transition flex items-center justify-center gap-2 shadow-sm shadow-indigo-600/25 text-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    @if(!$student)
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-slate-800 text-indigo-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                🔍
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Data Siswa Belum Dipilih</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                Silakan pilih kelas dan siswa melalui filter di atas untuk menghasilkan Raport Digital Analisis Perkembangan.
            </p>
        </div>
    @else
        <!-- Profil Siswa & Periode Aktif -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-400 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">{{ $student->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[11px] font-bold border border-indigo-200 dark:border-indigo-800">
                            Kelas {{ $student->schoolClass?->name ?? 'Umum' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[11px] font-medium">
                            NIS: {{ $student->nis }} • NISN: {{ $student->nisn }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex-wrap">
                        <span>👨‍🏫 Wali Kelas: <strong class="text-slate-700 dark:text-slate-300">{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>📅 Periode Analisis: <strong class="text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Composite Score Badge -->
            <div class="bg-gradient-to-br from-indigo-50 to-slate-100 dark:from-slate-800 dark:to-slate-800/60 p-4 rounded-2xl border border-indigo-100 dark:border-slate-700 text-center shrink-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Indeks Perkembangan AI</div>
                <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5">
                    {{ $analysis['metrics']['development_index'] ?? 0 }} <span class="text-xs text-slate-400 font-semibold">/ 100</span>
                </div>
                <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $analysis['model'] ?? 'AI Sintesis' }}
                </div>
            </div>
        </div>

        <!-- 8 PILAR PERKEMBANGAN TERPADU SISWA & EKSTRAKURIKULER -->
        <div class="mb-3">
            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                8 PILAR PERKEMBANGAN TERPADU SISWA & EKSTRAKURIKULER
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- 1. Presensi Harian -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">1. Presensi Harian</span>
                        <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                            🕒
                        </span>
                    </div>
                    @php
                        $attTotal = $latestAiRaport?->attendance_total ?? $student->attendances()->count();
                        $attRate = ($attTotal === 0) ? 0.0 : ($latestAiRaport?->attendance_rate ?? $analysis['metrics']['attendance_rate']);
                        $punctualityRate = ($attTotal === 0) ? 0.0 : ($analysis['metrics']['punctuality_rate'] ?? 0.0);
                        $attPresent = $latestAiRaport?->attendance_present ?? $analysis['metrics']['present_count'];
                        $attLate = $latestAiRaport?->attendance_late ?? $analysis['metrics']['late_count'];
                        $attPermit = $latestAiRaport?->attendance_permit ?? $analysis['metrics']['permit_count'];
                        $attSick = $latestAiRaport?->attendance_sick ?? $analysis['metrics']['sick_count'];
                        $attAlpha = $latestAiRaport?->attendance_alpha ?? $analysis['metrics']['alpha_count'];
                    @endphp
                    @if($attTotal === 0)
                        <div class="text-2xl font-black text-slate-400">
                            0.0%
                        </div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Terlaksana
                        </span>
                    @else
                        <div class="text-2xl font-black {{ $attRate >= 85 ? 'text-emerald-600 dark:text-emerald-400' : ($attRate >= 70 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                            {{ number_format($attRate, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Tepat Waktu: <strong class="text-slate-700 dark:text-slate-200">{{ number_format($punctualityRate, 1) }}%</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px]">
                    <span class="text-emerald-600 font-bold" title="Hadir">H: {{ $attPresent }}</span>
                    <span class="text-amber-600 font-semibold" title="Terlambat">T: {{ $attLate }}</span>
                    <span class="text-sky-600 font-semibold" title="Izin">I: {{ $attPermit }}</span>
                    <span class="text-pink-600 font-semibold" title="Sakit">S: {{ $attSick }}</span>
                    <span class="text-rose-600 font-bold" title="Alpa">A: {{ $attAlpha }}</span>
                </div>
            </div>

            <!-- 2. Poin Prestasi -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">2. Poin Prestasi</span>
                        <span class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">
                            🏆
                        </span>
                    </div>
                    @php
                        $achPts = $latestAiRaport?->achievement_points ?? $student->totalAchievementPoints();
                        $achCount = $latestAiRaport?->achievement_count ?? $analysis['metrics']['achievement_count'];
                        $rewardTier = $latestAiRaport?->reward_tier ?? $analysis['metrics']['reward_tier'];
                    @endphp
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        +{{ $achPts }} <span class="text-xs font-normal text-slate-400">Pts</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Tier: <strong class="text-slate-700 dark:text-slate-200">{{ $rewardTier }}</strong>
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Total: <strong>{{ $achCount }} Prestasi</strong></span>
                    <span class="text-amber-600 font-bold">+{{ $achPts }} Kumulatif</span>
                </div>
            </div>

            <!-- 3. Poin Pelanggaran -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">3. Pelanggaran</span>
                        <span class="w-7 h-7 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold">
                            ⚠️
                        </span>
                    </div>
                    @php
                        $vioPts = $latestAiRaport?->violation_points ?? $student->totalViolationPoints();
                        $vioCount = $latestAiRaport?->violation_count ?? $analysis['metrics']['violation_count'];
                        $guidanceTier = $latestAiRaport?->guidance_tier ?? $analysis['metrics']['guidance_tier'];
                    @endphp
                    <div class="text-2xl font-black {{ $vioPts > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $vioPts }} <span class="text-xs font-normal text-slate-400">Pts</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Status: <strong class="text-slate-700 dark:text-slate-200">{{ $guidanceTier }}</strong>
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Total: <strong>{{ $vioCount }} Catatan</strong></span>
                    <span class="text-rose-600 font-bold">{{ $vioPts }} Kumulatif</span>
                </div>
            </div>

            <!-- 4. 7 Kegiatan Anak Hebat -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">4. 7 Anak Hebat</span>
                        <span class="w-7 h-7 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm font-bold">
                            🌟
                        </span>
                    </div>
                    @php
                        $habitScore = $latestAiRaport?->habit_score ?? $analysis['metrics']['habit_score'];
                        $habitStatus = $latestAiRaport?->habit_status ?? $analysis['metrics']['habit_status'];
                        $habitTotal = $latestAiRaport?->habit_total_logs ?? $analysis['metrics']['total_expected_habits'];
                        $habitDone = $latestAiRaport?->habit_completed_logs ?? $analysis['metrics']['completed_habits'];
                    @endphp
                    @if($habitTotal === 0)
                        <div class="text-2xl font-black text-slate-400">0.0%</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Terpantau
                        </span>
                    @else
                        <div class="text-2xl font-black text-teal-600 dark:text-teal-400">
                            {{ number_format($habitScore, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Predikat: <strong class="text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', $habitStatus) }}</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Terisi: <strong>{{ $habitDone }}</strong> / {{ $habitTotal }}</span>
                    <span class="text-teal-600 font-bold">7 Karakter</span>
                </div>
            </div>

            <!-- 5. Presensi Shalat Dzuhur / Jum'at -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">5. Shalat Dzuhur (Putra & Putri)</span>
                        <span class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">
                            🕌
                        </span>
                    </div>
                    @php
                        $dzuhurTotal = $latestAiRaport?->worship_dzuhur_total ?? $analysis['metrics']['worship_dzuhur_total'];
                        $dzuhurHadir = $latestAiRaport?->worship_dzuhur_hadir ?? $analysis['metrics']['worship_dzuhur_hadir'];
                        $dzuhurRate = $latestAiRaport?->worship_dzuhur_rate ?? $analysis['metrics']['worship_dzuhur_rate'];
                    @endphp
                    @if($dzuhurTotal === 0)
                        <div class="text-2xl font-black text-slate-400">0.0%</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Ada Data
                        </span>
                    @else
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                            {{ number_format($dzuhurRate, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $dzuhurHadir }}/{{ $dzuhurTotal }} Sesi</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Putra & Putri (Semua Siswa)</span>
                    <span class="text-indigo-600 font-bold">{{ $dzuhurHadir }}x Hadir</span>
                </div>
            </div>

            <!-- 6. Presensi Shalat Dhuha -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">6. Shalat Dhuha</span>
                        <span class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">
                            ☀️
                        </span>
                    </div>
                    @php
                        $dhuhaTotal = $latestAiRaport?->worship_dhuha_total ?? $analysis['metrics']['worship_dhuha_total'];
                        $dhuhaHadir = $latestAiRaport?->worship_dhuha_hadir ?? $analysis['metrics']['worship_dhuha_hadir'];
                        $dhuhaRate = $latestAiRaport?->worship_dhuha_rate ?? $analysis['metrics']['worship_dhuha_rate'];
                    @endphp
                    @if($dhuhaTotal === 0)
                        <div class="text-2xl font-black text-slate-400">0.0%</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Ada Data
                        </span>
                    @else
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                            {{ number_format($dhuhaRate, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $dhuhaHadir }}/{{ $dhuhaTotal }} Sesi</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Pembiasaan Pagi</span>
                    <span class="text-amber-600 font-bold">{{ $dhuhaHadir }}x Hadir</span>
                </div>
            </div>

            <!-- 7. Presensi Keputrian (Bagi Siswa Perempuan) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">7. Keputrian</span>
                        <span class="w-7 h-7 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center text-xs font-bold">
                            🧕
                        </span>
                    </div>
                    @php
                        $isFemale = in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true);
                        $kepTotal = $latestAiRaport?->worship_keputrian_total ?? $analysis['metrics']['worship_keputrian_total'];
                        $kepHadir = $latestAiRaport?->worship_keputrian_hadir ?? $analysis['metrics']['worship_keputrian_hadir'];
                        $kepRate = $latestAiRaport?->worship_keputrian_rate ?? $analysis['metrics']['worship_keputrian_rate'];
                    @endphp
                    @if(!$isFemale)
                        <div class="text-base font-black text-slate-400 mt-1">
                            Khusus Siswi
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
                            Tidak dibebankan bagi siswa laki-laki.
                        </p>
                    @elseif($kepTotal === 0)
                        <div class="text-2xl font-black text-slate-400">0.0%</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Ada Sesi
                        </span>
                    @else
                        <div class="text-2xl font-black text-pink-600 dark:text-pink-400">
                            {{ number_format($kepRate, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $kepHadir }}/{{ $kepTotal }} Sesi</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Siswa Perempuan</span>
                    <span class="text-pink-600 font-bold">{{ $isFemale ? "{$kepHadir}x Hadir" : 'N/A' }}</span>
                </div>
            </div>

            <!-- 8. Tahfidz Juz 30 -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">8. Tahfidz Juz 30</span>
                        <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                            📖
                        </span>
                    </div>
                    @php
                        $tahfidzCompleted = $latestAiRaport?->tahfidz_completed_count ?? $analysis['metrics']['completed_surahs_count'];
                        $tahfidzTarget = $latestAiRaport?->tahfidz_target_count ?? $analysis['metrics']['total_target_surahs'];
                        $tahfidzPct = $latestAiRaport?->tahfidz_progress_pct ?? $analysis['metrics']['tahfidz_progress_pct'];
                        $tahfidzStatus = $latestAiRaport?->tahfidz_status ?? $analysis['metrics']['tahfidz_status'];
                    @endphp
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ $tahfidzCompleted }} <span class="text-xs font-normal text-slate-400">/ {{ $tahfidzTarget }} Surat</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate" title="{{ $tahfidzStatus }}">
                        Progres: <strong class="text-slate-700 dark:text-slate-200">{{ number_format($tahfidzPct, 1) }}%</strong>
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Target Kelas</span>
                    <span class="text-emerald-600 font-bold">{{ $tahfidzStatus }}</span>
                </div>
            </div>

            <!-- 9. Presensi Ekstrakurikuler -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">9. Ekstrakurikuler</span>
                        <span class="w-7 h-7 rounded-xl bg-violet-50 dark:bg-violet-950/60 text-violet-600 dark:text-violet-400 flex items-center justify-center text-xs font-bold">
                            🎯
                        </span>
                    </div>
                    @php
                        $hasExtra = $analysis['metrics']['has_extracurricular'] ?? ($student->extracurriculars()->exists());
                        $extraTotal = $analysis['metrics']['extracurricular_total'] ?? $student->extracurricularAttendances()->count();
                        $extraHadir = $analysis['metrics']['extracurricular_hadir'] ?? $student->extracurricularAttendances()->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
                        $extraRate = $latestAiRaport?->extracurricular_rate ?? ($analysis['metrics']['extracurricular_rate'] ?? 0.0);
                        $extraNotes = $latestAiRaport?->extracurricular_notes ?? ($analysis['metrics']['extracurricular_notes'] ?? '');
                        $extraNames = $analysis['metrics']['joined_extracurricular_names'] ?? $student->extracurriculars->pluck('name')->all();
                    @endphp
                    @if(!$hasExtra)
                        <div class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">
                            Tidak Mengikuti Ekskul
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
                            Siswa tidak mengikuti ekstrakurikuler.
                        </p>
                    @elseif($extraTotal === 0)
                        <div class="text-2xl font-black text-slate-400">0.0%</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            Belum Ada Sesi
                        </span>
                    @else
                        <div class="text-2xl font-black text-violet-600 dark:text-violet-400">
                            {{ number_format($extraRate, 1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate" title="{{ implode(', ', $extraNames) }}">
                            Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $extraHadir }}/{{ $extraTotal }} Sesi</strong>
                        </p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span class="truncate max-w-[120px]" title="{{ implode(', ', $extraNames) }}">{{ !empty($extraNames) ? implode(', ', $extraNames) : 'Ekstrakurikuler' }}</span>
                    <span class="text-violet-600 font-bold">{{ $hasExtra ? ($extraTotal > 0 ? "{$extraHadir}x Hadir" : 'Terdaftar') : 'Tidak Ada' }}</span>
                </div>
            </div>
        </div>

        <!-- SINTESIS EVALUASI PERKEMBANGAN DARI AI -->
        <div class="bg-gradient-to-br from-slate-50 to-indigo-50/30 dark:from-slate-900 dark:to-indigo-950/20 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-sm mt-5">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Evaluasi Perkembangan Siswa (AI Summary)</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Sintesis multidimensi berdasarkan 9 pilar kegiatan siswa</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5 border border-emerald-200 dark:border-emerald-800/50 shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $analysis['provider'] ?? 'AI Engine (Online)' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <!-- 1. Ringkasan Komprehensif -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-sky-50/60 to-white dark:from-slate-800/60 dark:to-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-sky-300 dark:hover:border-sky-700/60 transition duration-300 flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-sky-600 dark:text-sky-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-sky-100 dark:bg-sky-950/80 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shadow-xs font-black">📊</span>
                                Ringkasan Komprehensif
                            </h4>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-900/50">Eksekutif</span>
                        </div>
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $analysis['ringkasan_komprehensif'] ?? 'Data ringkasan perkembangan sedang diproses.' }}
                        </p>
                    </div>
                </div>

                <!-- 2. Profil Pelajar Pancasila -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-fuchsia-50/60 to-white dark:from-slate-800/60 dark:to-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-fuchsia-300 dark:hover:border-fuchsia-700/60 transition duration-300 flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-fuchsia-600 dark:text-fuchsia-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-950/80 text-fuchsia-600 dark:text-fuchsia-400 flex items-center justify-center text-xs shadow-xs font-black">🌟</span>
                                Karakter Profil Pelajar Pancasila
                            </h4>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-fuchsia-100 dark:bg-fuchsia-950 text-fuchsia-700 dark:text-fuchsia-300 border border-fuchsia-200 dark:border-fuchsia-900/50">Kurikulum Merdeka</span>
                        </div>
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $analysis['catatan_profil_pelajar'] ?? 'Catatan pembiasaan karakter profil pelajar sedang diproses.' }}
                        </p>
                    </div>
                </div>

                <!-- 3. Penilaian Terpadu -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-indigo-50/60 to-white dark:from-slate-800/60 dark:to-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700/60 transition duration-300 flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs shadow-xs font-black">📝</span>
                                Penilaian Terpadu Siswa
                            </h4>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900/50">8 Pilar Terpadu</span>
                        </div>
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $analysis['penilaian_siswa'] ?? 'Data sintesis penilaian sedang diproses.' }}
                        </p>
                    </div>
                </div>

                <!-- 4. Tren Perkembangan -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-emerald-50/60 to-white dark:from-slate-800/60 dark:to-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700/60 transition duration-300 flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs shadow-xs font-black">📈</span>
                                Tren Perkembangan & Potensi
                            </h4>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">Progresif</span>
                        </div>
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $analysis['perkembangan'] ?? 'Data tren perkembangan sedang disintesis.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- REKOMENDASI SARAN PENDAMPINGAN TERARAH SESUAI ROLE (FEATURE UTAMA) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-xs">
            @php
                $activeRoleTab = match(true) {
                    $user->hasRole('orang_tua') => 'orang_tua',
                    $user->hasRole('siswa') => 'siswa',
                    $user->hasRole('wali_kelas') => 'wali_kelas',
                    $user->hasRole('koordinator_keagamaan') => 'koordinator_keagamaan',
                    $user->hasRole('bk') => 'bk',
                    default => 'manajemen',
                };
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20 mb-1">
                        <span>🎯 REKOMENDASI PENDAMPINGAN TERARAH</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        Saran Tindakan Berdasarkan Peran ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                    </h3>
                </div>

                <!-- Role Selector Tabs for Educators/Admin -->
                @if(in_array($user->role, ['guru', 'wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin', 'kepala_sekolah', 'koordinator_keagamaan']) || $user->hasRole('koordinator_keagamaan'))
                    <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl text-xs font-bold" id="role-recom-tabs">
                        <button type="button" onclick="switchRecomTab('orang_tua')" id="tab-btn-orang_tua" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'orang_tua' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            👨‍👩‍👧 Orang Tua
                        </button>
                        <button type="button" onclick="switchRecomTab('siswa')" id="tab-btn-siswa" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'siswa' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            🎒 Siswa
                        </button>
                        <button type="button" onclick="switchRecomTab('wali_kelas')" id="tab-btn-wali_kelas" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'wali_kelas' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            👨‍🏫 Wali Kelas
                        </button>
                        <button type="button" onclick="switchRecomTab('koordinator_keagamaan')" id="tab-btn-koordinator_keagamaan" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'koordinator_keagamaan' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            🕌 Pembina Keagamaan
                        </button>
                        <button type="button" onclick="switchRecomTab('bk')" id="tab-btn-bk" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'bk' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            🧭 Guru BK
                        </button>
                        <button type="button" onclick="switchRecomTab('manajemen')" id="tab-btn-manajemen" class="tab-btn px-3 py-1.5 rounded-xl transition text-[11px] {{ $activeRoleTab === 'manajemen' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                            🏛️ Manajemen
                        </button>
                    </div>
                @endif
            </div>

            <!-- Rekomendasi Khusus Orang Tua -->
            <div id="recom-content-orang_tua" class="recom-panel {{ $activeRoleTab === 'orang_tua' ? '' : 'hidden' }} p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">👨‍👩‍👧</span>
                    <h4 class="font-extrabold text-amber-900 dark:text-amber-200 text-sm">Saran Khusus Pendampingan Orang Tua / Wali di Rumah:</h4>
                </div>
                <ul class="space-y-2 text-xs text-amber-900/90 dark:text-amber-300">
                    @foreach($analysis['rekomendasi']['orang_tua']['actions'] ?? [
                        'Dampingi rutinitas malam hari agar anak dapat tidur tepat waktu dan bangun pagi lebih bugar.',
                        'Luangkan waktu 10-15 menit setiap hari untuk mendengarkan refleksi aktivitas anak di sekolah.'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-amber-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Khusus Siswa -->
            <div id="recom-content-siswa" class="recom-panel {{ $activeRoleTab === 'siswa' ? '' : 'hidden' }} p-5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-800/40">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🎒</span>
                    <h4 class="font-extrabold text-emerald-900 dark:text-emerald-200 text-sm">Target Refleksi & Pengembangan Mandiri Siswa:</h4>
                </div>
                <ul class="space-y-2 text-xs text-emerald-900/90 dark:text-emerald-300">
                    @foreach($analysis['rekomendasi']['siswa']['actions'] ?? [
                        'Pertahankan kebiasaan baik yang sudah konsisten dan jadikan sebagai inspirasi bagi teman sebaya.',
                        'Manfaatkan jurnal 7 Kebiasaan sebagai sarana melatih kejujuran, tanggung jawab, dan kemandirian.'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Khusus Wali Kelas -->
            <div id="recom-content-wali_kelas" class="recom-panel {{ $activeRoleTab === 'wali_kelas' ? '' : 'hidden' }} p-5 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/20 border border-indigo-200/80 dark:border-indigo-800/40">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">👨‍🏫</span>
                    <h4 class="font-extrabold text-indigo-900 dark:text-indigo-200 text-sm">Panduan Tindakan Pedagogis Wali Kelas:</h4>
                </div>
                <ul class="space-y-2 text-xs text-indigo-900/90 dark:text-indigo-300">
                    @foreach($analysis['rekomendasi']['wali_kelas']['actions'] ?? [
                        'Lakukan dialog berkala mengenai perkembangan belajar dan peran aktif siswa di kelas.',
                        'Dorong siswa untuk aktif mengeksplorasi minat bakat dan mencatatkan prestasi.'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-indigo-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Khusus Koordinator Keagamaan & Pembina Tahfidz -->
            <div id="recom-content-koordinator_keagamaan" class="recom-panel {{ $activeRoleTab === 'koordinator_keagamaan' ? '' : 'hidden' }} p-5 rounded-2xl bg-teal-50/60 dark:bg-teal-950/20 border border-teal-200/80 dark:border-teal-800/40">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🕌</span>
                    <h4 class="font-extrabold text-teal-900 dark:text-teal-200 text-sm">Panduan Tindakan Pembina Keagamaan & Tahfidz:</h4>
                </div>
                <ul class="space-y-2 text-xs text-teal-900/90 dark:text-teal-300">
                    @foreach($analysis['rekomendasi']['koordinator_keagamaan']['actions'] ?? [
                        'Jadwalkan pendampingan setoran hafalan Juz 30 secara rutin minimal 2 kali seminggu untuk memantau tajwid dan makhraj.',
                        'Pantau kehadiran shalat dhuha dan shalat berjamaah sebagai bagian dari pembentukan akhlak mulia.'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-teal-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Khusus Guru BK -->
            <div id="recom-content-bk" class="recom-panel {{ $activeRoleTab === 'bk' ? '' : 'hidden' }} p-5 rounded-2xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-200/80 dark:border-purple-800/40">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🧭</span>
                    <h4 class="font-extrabold text-purple-900 dark:text-purple-200 text-sm">Rencana Intervensi & Konseling Guru BK:</h4>
                </div>
                <ul class="space-y-2 text-xs text-purple-900/90 dark:text-purple-300">
                    @foreach($analysis['rekomendasi']['bk']['actions'] ?? [
                        'Lakukan sesi konseling suportif berkala untuk memetakan rencana studi lanjut dan minat karier.',
                        'Dukung siswa dalam mengembangkan strategi adaptasi belajar yang seimbang.'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-purple-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Khusus Manajemen / Kepala Sekolah / Admin -->
            <div id="recom-content-manajemen" class="recom-panel {{ $activeRoleTab === 'manajemen' ? '' : 'hidden' }} p-5 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-300 dark:border-slate-700">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">🏛️</span>
                    <h4 class="font-extrabold text-slate-900 dark:text-slate-200 text-sm">Tinjauan Kebijakan & Manajemen Sekolah:</h4>
                </div>
                <ul class="space-y-2 text-xs text-slate-700 dark:text-slate-300">
                    @foreach($analysis['rekomendasi']['manajemen']['actions'] ?? [
                        'Pertimbangkan rekomendasi delegasi lomba dan beasiswa apresiasi siswa berprestasi pada forum pembinaan sekolah.',
                        'Dukung penguatan budaya positif sekolah dan sinergi kemitraan tri sentra pendidikan (Sekolah, Keluarga, Masyarakat).'
                    ] as $action)
                        <li class="flex items-start gap-2.5">
                            <span class="text-indigo-500 font-bold shrink-0 mt-0.5">✔</span>
                            <span>{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- DETAIL CATATAN PRESTASI & PELANGGARAN PERIODE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Capaian Prestasi Periode -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🏆</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Catatan Prestasi Periode Ini</h4>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-full">
                        {{ $periodAchievements->count() }} Kegiatan
                    </span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-56 overflow-y-auto pr-1">
                    @forelse($periodAchievements as $ach)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $ach->title }}</div>
                                <div class="text-[11px] text-slate-400">{{ $ach->date->format('d/m/Y') }} • Tingkat {{ $ach->level }} • {{ $ach->category?->name }}</div>
                            </div>
                            <span class="font-bold text-amber-600 shrink-0 ml-2">+{{ $ach->points }} Pts</span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400 italic">
                            Belum ada catatan prestasi yang terverifikasi pada rentang tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Catatan Pelanggaran Periode -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Catatan Pelanggaran Periode Ini</h4>
                    </div>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/60 px-2.5 py-1 rounded-full">
                        {{ $periodViolations->count() }} Catatan
                    </span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-56 overflow-y-auto pr-1">
                    @forelse($periodViolations as $viol)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $viol->title }}</div>
                                <div class="text-[11px] text-slate-400">{{ $viol->date->format('d/m/Y') }} • {{ $viol->category?->name }} • Lokasi: {{ $viol->location ?? '-' }}</div>
                            </div>
                            <span class="font-bold text-rose-600 shrink-0 ml-2">{{ $viol->points }} Pts</span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                            ✨ Tidak ada catatan pelanggaran tata tertib pada rentang tanggal ini (Disiplin Bersih).
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- DETAIL CATATAN IBADAH BERSAMA & TAHFIDZ PERIODE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Rekapitulasi Ibadah Bersama (Dhuha, Dzuhur, Keputrian) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🕌</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Catatan Presensi Ibadah Bersama</h4>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-1 rounded-full">
                        {{ $worshipRecords->count() }} Sesi Terdata ({{ $worshipSummary['rate'] }}% Kehadiran)
                    </span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-56 overflow-y-auto pr-1">
                    @forelse($worshipRecords as $wRec)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ \App\Models\WorshipAttendance::getActivityTitle($wRec->activity_type) }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($wRec->date)->locale('id')->translatedFormat('l, d M Y') }} • Pukul {{ substr($wRec->time, 0, 5) }} WIB • Metode: {{ $wRec->method ?? 'Sistem' }}
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ in_array($wRec->status, ['HADIR', 'TERLAMBAT']) ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-400' }}">
                                {{ $wRec->status }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400 italic">
                            Belum ada catatan presensi ibadah bersama pada rentang tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Setoran Tahfidz Al-Qur'an Juz 30 -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📖</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Riwayat Setoran Tahfidz Juz 30</h4>
                        @if(!empty($tahfidzSummary['student_grade']))
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full
                                {{ $tahfidzSummary['student_grade'] === 'X' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : ($tahfidzSummary['student_grade'] === 'XI' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400') }}">
                                Target Kelas {{ $tahfidzSummary['student_grade'] }}
                            </span>
                        @endif
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-full">
                        {{ $tahfidzSummary['completed_surahs_count'] }} / {{ $tahfidzSummary['total_juz30'] }} Surat Lulus ({{ $tahfidzSummary['progress_pct'] }}%)
                    </span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-56 overflow-y-auto pr-1">
                    @forelse($tahfidzRecords as $tRec)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    QS. {{ $tRec->surah_name }} (Ayat {{ $tRec->ayat_range }})
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $tRec->deposit_date->format('d/m/Y') }} • Penguji: {{ $tRec->examiner_name }} • Makhraj: {{ $tRec->makhraj }}, Tajwid: {{ $tRec->tajwid }}, Kelancaran: {{ $tRec->kelancaran }}
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ $tRec->overall_grade === 'Lulus' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400' }}">
                                {{ $tRec->overall_grade }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400 italic">
                            Belum ada catatan setoran hafalan pada rentang tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tombol Cetak Dokumen Lengkap di Bawah (Pilihan A4 & F4) -->
        <div class="flex flex-wrap justify-center items-center gap-3 pt-3">
            <a href="{{ route('raport.print', ['student' => $student->id, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'a4']) }}" target="_blank"
                class="px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold rounded-2xl shadow-xl shadow-emerald-600/20 text-xs sm:text-sm flex items-center gap-2 transition transform hover:-translate-y-0.5">
                <span>🖨️ Cetak Raport Digital (Format A4)</span>
                <span>↗</span>
            </a>
            <a href="{{ route('raport.print', ['student' => $student->id, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'f4']) }}" target="_blank"
                class="px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-500 hover:to-sky-500 text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-600/20 text-xs sm:text-sm flex items-center gap-2 transition transform hover:-translate-y-0.5">
                <span>📜 Cetak Raport Digital (Format F4 / Folio)</span>
                <span>↗</span>
            </a>
        </div>
    @endif
</div>

<!-- Modal Pencarian Cepat Nama Siswa (A-Z) untuk Staff / Guru -->
@if(!in_array($user->role, ['siswa', 'orang_tua']) && $studentsList->isNotEmpty())
<div id="student-search-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl relative text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
            <div>
                <h3 class="font-black text-sm sm:text-base text-white">Cari Siswa untuk Raport Digital</h3>
                <p class="text-[11px] text-slate-400">Ketik nama/NIS atau pilih inisial A-Z</p>
            </div>
            <button type="button" onclick="closeStudentSearchModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-sm font-bold flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <!-- Input Pencarian -->
        <div class="relative mb-2.5">
            <input type="text" id="modal_student_search_input"
                placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                autocomplete="off"
                oninput="filterModalStudents(this.value)"
                class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 outline-none focus:border-indigo-500 transition">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Inisial Bar A-Z -->
        <div class="flex items-center gap-1 overflow-x-auto pb-2 mb-2 no-scrollbar text-xs">
            <button type="button" onclick="filterModalByInitial('ALL')"
                class="modal-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition" data-letter="ALL">
                Semua
            </button>
            @foreach(range('A', 'Z') as $char)
                <button type="button" onclick="filterModalByInitial('{{ $char }}')"
                    class="modal-initial-chip px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-700" data-letter="{{ $char }}">
                    {{ $char }}
                </button>
            @endforeach
        </div>

        <!-- Daftar Siswa -->
        <div id="modal-student-list" class="max-h-72 overflow-y-auto rounded-2xl border border-slate-800 divide-y divide-slate-800">
            @foreach($studentsList as $st)
                @php
                    $words = explode(' ', trim($st->name));
                    $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                    $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                    $isSelected = ($student && $student->id === $st->id);
                @endphp
                <a href="{{ route('raport.index', array_merge(request()->query(), ['student_id' => $st->id, 'class_id' => $st->class_id])) }}"
                    class="modal-student-item px-3.5 py-2.5 hover:bg-slate-800 cursor-pointer flex items-center justify-between transition {{ $isSelected ? 'bg-indigo-950/60 font-bold' : '' }}"
                    data-name="{{ $st->name }}"
                    data-nis="{{ $st->nis }}"
                    data-initials="{{ strtolower($initials) }}"
                    data-firstletter="{{ $firstChar }}">
                    <div class="flex items-center gap-2.5 truncate mr-2">
                        <span class="w-8 h-8 rounded-lg {{ $isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300' }} font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($initials, 0, 2)) }}
                        </span>
                        <div class="truncate">
                            <div class="font-bold text-white truncate text-xs">{{ $st->name }}</div>
                            <div class="text-[10px] text-slate-400">Kelas {{ $st->schoolClass?->name ?? 'Umum' }} • NIS: {{ $st->nis }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 font-mono shrink-0 font-bold">
                        [{{ strtoupper($initials) }}]
                    </span>
                </a>
            @endforeach
            <div id="modal-student-empty" class="hidden p-6 text-center text-xs text-slate-400 italic">
                Tidak ada siswa yang cocok dengan kata kunci tersebut.
            </div>
        </div>
    </div>
</div>

<script>
    function openStudentSearchModal() {
        const modal = document.getElementById('student-search-modal');
        if (modal) {
            modal.classList.remove('hidden');
            const input = document.getElementById('modal_student_search_input');
            if (input) setTimeout(() => input.focus(), 50);
        }
    }

    function closeStudentSearchModal() {
        const modal = document.getElementById('student-search-modal');
        if (modal) modal.classList.add('hidden');
    }

    let activeModalInitial = 'ALL';

    function filterModalByInitial(char) {
        activeModalInitial = char;
        document.querySelectorAll('.modal-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === char) {
                btn.className = 'modal-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition';
            } else {
                btn.className = 'modal-initial-chip px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-700';
            }
        });
        const query = document.getElementById('modal_student_search_input')?.value || '';
        filterModalStudents(query);
    }

    function filterModalStudents(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.modal-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchesInitial = (activeModalInitial === 'ALL' || firstLetter === activeModalInitial);
            const matchesQuery = !q || name.includes(q) || nis.includes(q) || initials.includes(q);

            if (matchesInitial && matchesQuery) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = document.getElementById('modal-student-empty');
        if (empty) {
            empty.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function switchRecomTab(role) {
        document.querySelectorAll('.recom-panel').forEach(panel => panel.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.add('text-slate-600', 'dark:text-slate-300');
        });

        const targetPanel = document.getElementById('recom-content-' + role);
        const targetBtn = document.getElementById('tab-btn-' + role);

        if (targetPanel) targetPanel.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            targetBtn.classList.remove('text-slate-600', 'dark:text-slate-300');
        }
    }
</script>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    (function() {
        function initTomSelect() {
            const studentSelect = document.getElementById('student_id_select');
            if (studentSelect && !studentSelect.tomselect) {
                new TomSelect(studentSelect, {
                    create: false,
                    maxOptions: null,
                    maxItems: 1,
                    searchField: ['text'],
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: "-- Cari / Pilih Siswa --",
                    render: {
                        no_results: function(data, escape) {
                            return '<div class="no-results p-2.5 text-xs text-slate-400 italic">Siswa tidak ditemukan...</div>';
                        }
                    },
                    onChange: function(value) {
                        if (value && value !== "") {
                            studentSelect.form.submit();
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTomSelect);
        } else {
            initTomSelect();
        }
    })();
</script>
@endpush

@endsection
