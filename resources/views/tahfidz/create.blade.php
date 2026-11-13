@extends('layouts.app')

@section('title', "Input Setoran Hafalan Al-Qur'an (Tahfidz Juz 30)")

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.single .ts-control, .ts-wrapper.single .ts-control input {
        cursor: pointer;
    }
    .ts-control {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        padding: 0.625rem 0.875rem;
        font-size: 0.75rem;
        background-color: #f8fafc;
        box-shadow: none;
    }
    .dark .ts-control {
        background-color: #1e293b;
        border-color: #334155;
        color: #f8fafc;
    }
    .dark .ts-dropdown {
        background-color: #1e293b;
        border-color: #334155;
        color: #f8fafc;
        z-index: 50 !important;
    }
    .ts-dropdown {
        z-index: 50 !important;
    }
    .dark .ts-dropdown .option {
        color: #f8fafc;
    }
    .dark .ts-dropdown .active {
        background-color: #334155;
        color: #f8fafc;
    }
</style>
<div class="max-w-4xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('tahfidz.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Form Input Setoran Hafalan Al-Qur'an
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Khusus akun <strong>Wali Kelas</strong> & <strong>Koordinator Keagamaan</strong> yang terotentikasi.
                </p>
            </div>
        </div>

        <span class="hidden sm:inline-flex px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
            Juz 30 (Amma)
        </span>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form action="{{ route('tahfidz.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Siswa & Penguji -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        1. Data Siswa & Penguji
                    </h3>
                    @if(!empty($isHomeroom) && !empty($homeroomClass))
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                            Wali Kelas: {{ $homeroomClass->name }} (Target Kelas {{ $homeroomGrade }})
                        </span>
                    @endif
                </div>

                @if(!empty($isHomeroom) && !empty($homeroomClass))
                    <div class="p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-500/30 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">🏫</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold">
                                Menginput setor hafalan siswa-siswi <strong>Kelas {{ $homeroomClass->name }}</strong>.
                            </span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                            Materi: {{ count($surahs) }} Surat Target Kelas {{ $homeroomGrade }}
                        </span>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="student_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih Siswa yang Menyetor <span class="text-rose-500">*</span>
                        </label>
                        <select name="student_id" id="student_id" required onchange="handleStudentChange(this)" class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            <option value="" disabled {{ !$selectedStudent ? 'selected' : '' }}>-- Cari / Pilih Siswa --</option>
                            @foreach($students as $st)
                                @php
                                    $initials = collect(explode(' ', $st->name))
                                        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                        ->join('');
                                @endphp
                                <option value="{{ $st->id }}"
                                    data-grade="{{ $st->getNormalizedGrade() }}"
                                    data-class="{{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}"
                                    data-initials="{{ $initials }}"
                                    {{ ($selectedStudent && $selectedStudent->id == $st->id) ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->schoolClass?->name ?? 'Tanpa Kelas' }} - NISN: {{ $st->nisn }} - Inisial: {{ $initials }})
                                </option>
                            @endforeach
                        </select>

                        <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                        <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="tahfidz-initials-bar">
                            <button type="button" onclick="filterTahfidzByInitial('ALL')"
                                class="tahfidz-initial-chip px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                                Semua
                            </button>
                            @foreach(range('A', 'Z') as $char)
                                <button type="button" onclick="filterTahfidzByInitial('{{ $char }}')"
                                    class="tahfidz-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                                    {{ $char }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="examiner_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Penguji / Koordinator / Wali Kelas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="examiner_name" id="examiner_name" value="{{ old('examiner_name', $defaultExaminerName) }}" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="deposit_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Setoran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="deposit_date" id="deposit_date" value="{{ old('deposit_date', \Carbon\Carbon::today()->toDateString()) }}" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label for="deposit_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Waktu Setoran <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="deposit_time" id="deposit_time" value="{{ old('deposit_time', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Surat & Rentang Ayat Sesuai Tingkat Kelas -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        2. Materi Setoran (Juz 30 Sesuai Tingkat Kelas)
                    </h3>

                    @if(!empty($isHomeroom) && !empty($homeroomGrade))
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                            🌟 Target Kurikulum Kelas {{ $homeroomGrade }} ({{ count($surahs) }} Surat)
                        </span>
                    @else
                        <!-- Filter Tingkatan Kelas -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mr-1">Filter Surat:</span>
                            <button type="button" onclick="filterSurahsByGrade('all')" id="btn-filter-all"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                Semua (37)
                            </button>
                            <button type="button" onclick="filterSurahsByGrade('X')" id="btn-filter-X"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition border border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                Kelas X (25)
                            </button>
                            <button type="button" onclick="filterSurahsByGrade('XI')" id="btn-filter-XI"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition border border-indigo-500/40 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                Kelas XI (7)
                            </button>
                            <button type="button" onclick="filterSurahsByGrade('XII')" id="btn-filter-XII"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition border border-purple-500/40 bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                Kelas XII (5)
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Info Alert Surat Sesuai Kelas -->
                <div id="grade-info-box" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
                        <span id="grade-info-badge" class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-emerald-500 text-white">Kelas {{ $homeroomGrade ?? 'X' }}</span>
                        <span id="grade-info-text">
                            @if(!empty($isHomeroom) && !empty($homeroomGrade))
                                Menampilkan {{ count($surahs) }} surat target kurikulum Kelas {{ $homeroomGrade }}.
                            @else
                                Menampilkan 25 surat target Kelas X (Al-Balad s.d. An-Nas).
                            @endif
                        </span>
                    </div>
                    @if(empty($isHomeroom))
                        <button type="button" onclick="filterSurahsByGrade('all')" class="text-[11px] font-bold text-indigo-500 hover:text-indigo-600 underline">Tampilkan Semua</button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Dropdown Surat -->
                    <div class="md:col-span-5">
                        <label for="surah_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            @if(!empty($isHomeroom) && !empty($homeroomGrade))
                                Pilih Surat Target Kelas {{ $homeroomGrade }} ({{ count($surahs) }} Surat) <span class="text-rose-500">*</span>
                            @else
                                Pilih Surat Juz 30 <span class="text-rose-500">*</span>
                            @endif
                        </label>
                        <select name="surah_number" id="surah_number" required onchange="handleSurahChange(this)" class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 font-medium">
                            <option value="" disabled selected>-- Pilih Surat {{ !empty($isHomeroom) && !empty($homeroomGrade) ? 'Kelas ' . $homeroomGrade : 'Juz 30' }} --</option>
                            @foreach($groupedSurahs as $gradeKey => $group)
                                <optgroup label="🌟 {{ $group['label'] ?? ('Target Kelas ' . $gradeKey) }} ({{ count($group['surahs']) }} Surat)" data-grade="{{ $gradeKey }}">
                                    @foreach($group['surahs'] as $s)
                                        <option value="{{ $s['number'] }}" data-grade="{{ $gradeKey }}" data-name="{{ $s['name'] }}" data-ayah="{{ $s['total_ayah'] }}" {{ old('surah_number') == $s['number'] ? 'selected' : '' }}>
                                            No. {{ $s['number'] }} — {{ $s['name'] }} ({{ $s['total_ayah'] }} Ayat / {{ $s['meaning'] }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sistem Cicilan Ayat: Mulai s.d. Sampai Ayat -->
                    <div class="md:col-span-7 p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-500/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                <span>⚡</span> <span>Sistem Setoran Cicilan (Angsuran Ayat)</span>
                            </span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                                Contoh: Ayat 10 s.d. 12
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-12 gap-2 items-center">
                            <div class="sm:col-span-4">
                                <label for="start_ayah" class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">
                                    Dari Ayat
                                </label>
                                <input type="number" name="start_ayah" id="start_ayah" value="{{ old('start_ayah') }}" min="1" max="286" placeholder="Misal: 10"
                                    oninput="updateInstallmentFields()"
                                    class="w-full text-xs px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            </div>
                            <div class="sm:col-span-4">
                                <label for="end_ayah" class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">
                                    Sampai Ayat
                                </label>
                                <input type="number" name="end_ayah" id="end_ayah" value="{{ old('end_ayah') }}" min="1" max="286" placeholder="Misal: 12"
                                    oninput="updateInstallmentFields()"
                                    class="w-full text-xs px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            </div>
                            <div class="col-span-2 sm:col-span-4 flex items-center gap-1.5 pt-1 sm:pt-3">
                                <button type="button" onclick="applyFullSurahInstallment()" class="w-full py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] transition shadow-xs text-center">
                                    1 Surat Penuh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Isian Jumlah Ayat yang Disetor -->
                    <div class="md:col-span-4">
                        <label for="ayat_count" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                            <span>Jumlah Ayat yang Disetor <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative">
                            <input type="number" name="ayat_count" id="ayat_count" value="{{ old('ayat_count') }}" min="1" max="100" required placeholder="Contoh: 20"
                                oninput="handleAyatCountChange(this.value)"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 font-black pr-14 text-emerald-600 dark:text-emerald-400">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">Ayat</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1" id="ayat_count_helper">
                            Dapat disesuaikan atau otomatis dari cicilan.
                        </p>
                    </div>

                    <!-- Rentang Ayat yang Disetor -->
                    <div class="md:col-span-8">
                        <label for="ayat_range" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Rentang Ayat yang Disetor <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="ayat_range" id="ayat_range" value="{{ old('ayat_range', 'Ayat 1 s.d. Selesai') }}" required placeholder="Contoh: Ayat 10 s.d. 12"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 font-bold">
                        <p class="text-[10px] text-slate-400 mt-1">
                            Format rentang ayat (misal: <em>Ayat 10 s.d. 12</em> atau <em>Ayat 1 - 20 (Selesai)</em>).
                        </p>
                    </div>
                </div>

                <!-- Pilihan Cepat Rentang Ayat -->
                <div class="flex items-center gap-1.5 flex-wrap text-[10px]">
                    <span class="text-slate-400 font-bold">Pilihan Cepat:</span>
                    <button type="button" onclick="setPreset('full')" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Surat Penuh</button>
                    <button type="button" onclick="setPreset('1-10')" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Ayat 1-10</button>
                    <button type="button" onclick="setPreset('11-20')" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Ayat 11-20</button>
                    <button type="button" onclick="setPreset('21-end')" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Ayat 21-Akhir</button>
                </div>
            </div>

            <!-- Section 3: Kategori Penilaian (Makhraj, Tajwid, Kelancaran) -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    3. Kategori Penilaian Penguji (Skala Standar)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Makhraj -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-850/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-100">
                            A. Makharijul Huruf <span class="text-rose-500">*</span>
                        </span>
                        <div class="space-y-1.5">
                            @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'] as $opt)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 text-xs font-semibold cursor-pointer transition">
                                    <input type="radio" name="makhraj" value="{{ $opt }}" {{ old('makhraj', 'Baik') === $opt ? 'checked' : '' }} class="text-emerald-600 focus:ring-0">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tajwid -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-850/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-100">
                            B. Kaidah Tajwid <span class="text-rose-500">*</span>
                        </span>
                        <div class="space-y-1.5">
                            @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'] as $opt)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 text-xs font-semibold cursor-pointer transition">
                                    <input type="radio" name="tajwid" value="{{ $opt }}" {{ old('tajwid', 'Baik') === $opt ? 'checked' : '' }} class="text-emerald-600 focus:ring-0">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kelancaran -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-850/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                        <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-100">
                            C. Kelancaran Hafalan <span class="text-rose-500">*</span>
                        </span>
                        <div class="space-y-1.5">
                            @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Mengulang'] as $opt)
                                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60 hover:bg-white dark:hover:bg-slate-800 text-xs font-semibold cursor-pointer transition">
                                    <input type="radio" name="kelancaran" value="{{ $opt }}" {{ old('kelancaran', 'Baik') === $opt ? 'checked' : '' }} class="text-emerald-600 focus:ring-0">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Hasil Akhir -->
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="block text-xs font-extrabold text-emerald-900 dark:text-emerald-200">Keputusan Hasil Setoran:</span>
                        <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Pilih 'Lulus' jika bacaan dan hafalan memenuhi syarat, atau 'Mengulang' jika perlu setor ulang.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-emerald-500 text-xs font-bold text-emerald-600 dark:text-emerald-400 cursor-pointer">
                            <input type="radio" name="overall_grade" value="Lulus" {{ old('overall_grade', 'Lulus') === 'Lulus' ? 'checked' : '' }} class="text-emerald-600 focus:ring-0">
                            <span>✓ Lulus (Mutqin)</span>
                        </label>
                        <label class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-amber-500 text-xs font-bold text-amber-600 dark:text-amber-400 cursor-pointer">
                            <input type="radio" name="overall_grade" value="Mengulang" {{ old('overall_grade') === 'Mengulang' ? 'checked' : '' }} class="text-amber-600 focus:ring-0">
                            <span>↻ Perlu Mengulang</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 4: Catatan Evaluasi Penguji -->
            <div class="space-y-2">
                <label for="evaluation_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                    Catatan Evaluasi / Rekomendasi Penguji (Teks Bebas)
                </label>
                <textarea name="evaluation_notes" id="evaluation_notes" rows="3" placeholder="Contoh: Perhatikan panjang mad wajib muttashil pada ayat 12, pengucapan huruf 'Ain sudah mantap."
                    class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">{{ old('evaluation_notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('tahfidz.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan & Validasi Setoran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentGradeFilter = '{{ $studentGrade ?? "all" }}';

    function setPreset(type) {
        const select = document.getElementById('surah_number');
        const opt = select.options[select.selectedIndex];
        const totalAyah = opt ? parseInt(opt.getAttribute('data-ayah') || '0', 10) : 0;
        const countInput = document.getElementById('ayat_count');
        const rangeInput = document.getElementById('ayat_range');

        if (type === 'full') {
            const count = totalAyah || 1;
            countInput.value = count;
            rangeInput.value = totalAyah ? `Ayat 1 s.d. ${totalAyah} (Selesai)` : 'Ayat 1 s.d. Selesai';
        } else if (type === '1-10') {
            const maxAyah = totalAyah ? Math.min(10, totalAyah) : 10;
            countInput.value = maxAyah;
            rangeInput.value = `Ayat 1 s.d. ${maxAyah}`;
        } else if (type === '11-20') {
            if (totalAyah && totalAyah < 11) {
                countInput.value = totalAyah;
                rangeInput.value = `Ayat 1 s.d. ${totalAyah} (Selesai)`;
            } else {
                const end = totalAyah ? Math.min(20, totalAyah) : 20;
                countInput.value = (end - 11 + 1);
                rangeInput.value = `Ayat 11 s.d. ${end}`;
            }
        } else if (type === '21-end') {
            if (totalAyah && totalAyah > 20) {
                countInput.value = (totalAyah - 20);
                rangeInput.value = `Ayat 21 s.d. ${totalAyah} (Selesai)`;
            } else {
                countInput.value = totalAyah || 1;
                rangeInput.value = totalAyah ? `Ayat 1 s.d. ${totalAyah} (Selesai)` : 'Ayat 1 s.d. Selesai';
            }
        }
    }

    function handleSurahChange(select) {
        const selected = select.options[select.selectedIndex];
        const ayahCount = selected.getAttribute('data-ayah');
        if (ayahCount) {
            document.getElementById('ayat_count').value = ayahCount;
            document.getElementById('ayat_range').value = `Ayat 1 s.d. ${ayahCount} (Selesai)`;
            const startInput = document.getElementById('start_ayah');
            const endInput = document.getElementById('end_ayah');
            if (startInput && endInput) {
                startInput.value = 1;
                endInput.value = ayahCount;
            }
        }
    }

    function updateInstallmentFields() {
        const startInput = document.getElementById('start_ayah');
        const endInput = document.getElementById('end_ayah');
        const rangeInput = document.getElementById('ayat_range');
        const countInput = document.getElementById('ayat_count');

        const start = parseInt(startInput.value, 10);
        const end = parseInt(endInput.value, 10);

        if (start && end) {
            const minA = Math.min(start, end);
            const maxA = Math.max(start, end);
            rangeInput.value = minA === maxA ? `Ayat ${minA}` : `Ayat ${minA} s.d. ${maxA}`;
            countInput.value = Math.abs(maxA - minA) + 1;
        } else if (start) {
            rangeInput.value = `Ayat ${start}`;
            countInput.value = 1;
        }
    }

    function applyFullSurahInstallment() {
        const select = document.getElementById('surah_number');
        const opt = select.options[select.selectedIndex];
        const totalAyah = opt ? parseInt(opt.getAttribute('data-ayah') || '0', 10) : 0;
        if (totalAyah) {
            document.getElementById('start_ayah').value = 1;
            document.getElementById('end_ayah').value = totalAyah;
            updateInstallmentFields();
        }
    }

    function clearInstallmentFields() {
        document.getElementById('start_ayah').value = '';
        document.getElementById('end_ayah').value = '';
    }

    function handleAyatCountChange(val) {
        const select = document.getElementById('surah_number');
        const opt = select.options[select.selectedIndex];
        const totalAyah = opt ? parseInt(opt.getAttribute('data-ayah') || '0', 10) : 0;
        const count = parseInt(val, 10);
        if (count && totalAyah && count === totalAyah) {
            document.getElementById('ayat_range').value = `Ayat 1 s.d. ${totalAyah} (Selesai)`;
        } else if (count && count > 0) {
            document.getElementById('ayat_range').value = `Ayat 1 s.d. ${count}`;
        }
    }

    function filterSurahsByGrade(grade) {
        currentGradeFilter = grade;
        const select = document.getElementById('surah_number');
        const optgroups = select.querySelectorAll('optgroup');
        const badge = document.getElementById('grade-info-badge');
        const text = document.getElementById('grade-info-text');

        // Reset button styles
        ['all', 'X', 'XI', 'XII'].forEach(g => {
            const btn = document.getElementById('btn-filter-' + g);
            if (btn) {
                if (g === grade) {
                    btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold transition shadow-xs ' + 
                        (g === 'X' ? 'bg-emerald-600 text-white' : (g === 'XI' ? 'bg-indigo-600 text-white' : (g === 'XII' ? 'bg-purple-600 text-white' : 'bg-slate-900 text-white dark:bg-white dark:text-slate-900')));
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold transition border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100';
                }
            }
        });

        if (grade === 'all') {
            optgroups.forEach(og => { og.style.display = ''; Array.from(og.options).forEach(o => o.style.display = ''); });
            if (badge) { badge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] bg-slate-700 text-white'; badge.textContent = 'Semua Kelas'; }
            if (text) text.textContent = 'Menampilkan seluruh 37 surat Juz 30 (Surat 78 s.d. 114).';
        } else {
            optgroups.forEach(og => {
                const ogGrade = og.getAttribute('data-grade');
                if (ogGrade === grade) {
                    og.style.display = '';
                    Array.from(og.options).forEach(o => o.style.display = '');
                } else {
                    og.style.display = 'none';
                    Array.from(og.options).forEach(o => o.style.display = 'none');
                }
            });

            if (badge) {
                badge.textContent = 'Kelas ' + grade;
                badge.className = 'px-2 py-0.5 rounded-md font-bold text-[10px] text-white ' + 
                    (grade === 'X' ? 'bg-emerald-600' : (grade === 'XI' ? 'bg-indigo-600' : 'bg-purple-600'));
            }

            if (text) {
                if (grade === 'X') text.textContent = 'Menampilkan 25 surat target Kelas X (No. 90 Al-Balad s.d. 114 An-Nas).';
                else if (grade === 'XI') text.textContent = 'Menampilkan 7 surat target Kelas XI (No. 83 Al-Mutaffifin s.d. 89 Al-Fajr).';
                else if (grade === 'XII') text.textContent = 'Menampilkan 5 surat target Kelas XII (No. 78 An-Naba\' s.d. 82 Al-Infitar).';
            }

            // If current selected option is hidden, reset select value to first visible
            const currentSelected = select.options[select.selectedIndex];
            if (currentSelected && currentSelected.style.display === 'none') {
                select.value = '';
            }
        }
    }

    const isHomeroomTeacher = {{ !empty($isHomeroom) && !empty($homeroomGrade) ? 'true' : 'false' }};
    const homeroomGradeLocked = "{{ $homeroomGrade ?? '' }}";

    function handleStudentChange(select) {
        if (isHomeroomTeacher && homeroomGradeLocked) {
            return; // Wali kelas terkunci ke tingkat kelas binaannya
        }
        const opt = select.options[select.selectedIndex];
        if (!opt) return;
        const grade = opt.getAttribute('data-grade');
        if (grade && ['X', 'XI', 'XII'].includes(grade)) {
            filterSurahsByGrade(grade);
        } else {
            filterSurahsByGrade('all');
        }
    }

    // Auto-init on page load based on selected student
    document.addEventListener('DOMContentLoaded', function () {
        if (isHomeroomTeacher && homeroomGradeLocked) {
            filterSurahsByGrade(homeroomGradeLocked);
            return;
        }
        const studentSelect = document.getElementById('student_id');
        if (studentSelect && studentSelect.selectedIndex > 0) {
            handleStudentChange(studentSelect);
        } else if (currentGradeFilter && currentGradeFilter !== 'all') {
            filterSurahsByGrade(currentGradeFilter);
        } else {
            filterSurahsByGrade('X'); // Default to Kelas X
        }

        // Initialize TomSelect for searchable dropdown
        if (studentSelect) {
            window.tahfidzTomSelect = new TomSelect(studentSelect, {
                create: false,
                searchField: ['text', 'initials'],
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "-- Cari / Pilih Siswa (Nama / Inisial) --",
                onChange: function(value) {
                    handleStudentChange(studentSelect);
                }
            });
        }
    });

    function filterTahfidzByInitial(char) {
        document.querySelectorAll('.tahfidz-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === char) {
                btn.className = 'tahfidz-initial-chip px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'tahfidz-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });

        if (window.tahfidzTomSelect) {
            if (char === 'ALL') {
                window.tahfidzTomSelect.setTextboxValue('');
                window.tahfidzTomSelect.clearFilter();
            } else {
                window.tahfidzTomSelect.setTextboxValue(char);
            }
            window.tahfidzTomSelect.open();
        }
    }
</script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endsection
