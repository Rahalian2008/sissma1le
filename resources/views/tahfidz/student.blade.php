@extends('layouts.app')

@section('title', 'Buku Kontrol Capaian Hafalan Al-Qur\'an Siswa - Kelas ' . ($studentGrade ?? 'X') . ' - ' . $student->name)

@section('content')
<div class="space-y-6">
    <!-- Header Siswa & Informasi Akun -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-indigo-600 text-white font-black flex items-center justify-center text-xl shrink-0 shadow-lg shadow-emerald-600/20">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $student->name }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $student->schoolClass?->name ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                        Kelas {{ $studentGrade }}
                    </span>
                    @if($student->user && ($student->user->hasRole('sekretaris_kelas') || in_array('sekretaris_kelas', (array)($student->user->roles ?? []))))
                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 uppercase tracking-wide">
                            Sekretaris Kelas
                        </span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-x-3 gap-y-1 flex-wrap">
                    <span>NISN: <strong class="text-slate-700 dark:text-slate-300">{{ $student->nisn }}</strong></span>
                    <span>NIS: <strong class="text-slate-700 dark:text-slate-300">{{ $student->nis ?? '-' }}</strong></span>
                    <span>Wali Kelas: <strong class="text-slate-700 dark:text-slate-300">{{ $student->schoolClass?->homeroomTeacher?->name ?? 'Belum ditentukan' }}</strong></span>
                    <span>Target: <strong class="text-emerald-600 dark:text-emerald-400">Kelas {{ $studentGrade }} ({{ $targetGradeTotal }} Surat / {{ $targetGradeTotalAyat }} Ayat)</strong></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if(Auth::user()->canManageTahfidz())
                <a href="{{ route('tahfidz.create', ['student_id' => $student->id, 'class_id' => $student->class_id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Setoran</span>
                </a>
            @endif
            <a href="{{ route('tahfidz.student_custom_progress', $student->id) }}" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 shadow-teal-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan Cicilan & Progres</span>
            </a>
            <a href="{{ route('tahfidz.print_student', $student->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 shadow-indigo-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu Siswa</span>
            </a>
            @if(Auth::user()->canManageTahfidz() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah']))
                <a href="{{ route('tahfidz.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Kembali
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Dashboard
                </a>
            @endif
        </div>
    </div>

    <!-- Hero Card: Informasi & Progres Komplit Capaian Hafalan Sesuai Surat Kelas -->
    <div class="bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 p-5 sm:p-7 rounded-2xl sm:rounded-3xl text-white border border-emerald-500/30 shadow-xl space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        📖 Kurikulum Tahfidz Kelas {{ $studentGrade }}
                    </span>
                    @if($targetGradePercentage >= 100)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-400 text-slate-950 shadow-sm flex items-center gap-1">
                            <span>✓</span> <span>TUNTAS & MUTQIN KELAS {{ $studentGrade }}</span>
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1">
                            <span>⏳</span> <span>Dalam Proses (Sisa {{ $targetGradeTotal - $targetGradeCompleted }} Surat)</span>
                        </span>
                    @endif
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Capaian Hafalan Surat Kelas {{ $studentGrade }}
                </h2>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Informasi dan progres komplit capaian hafalan Al-Qur'an ananda <strong>{{ $student->name }}</strong> untuk seluruh materi <strong>{{ $targetGradeTotal }} surat target kurikulum Kelas {{ $studentGrade }}</strong>
                    @if($targetGradeTotal > 0)
                        (Surat No. {{ array_key_first($targetGradeSurahs) }} {{ $targetGradeSurahs[array_key_first($targetGradeSurahs)]['name'] }} s.d. No. {{ array_key_last($targetGradeSurahs) }} {{ $targetGradeSurahs[array_key_last($targetGradeSurahs)]['name'] }}).
                    @endif
                </p>
            </div>

            <!-- Metrik Ringkas Kanan -->
            <div class="flex items-center gap-4 bg-slate-900/60 p-3.5 rounded-2xl border border-slate-800 shrink-0">
                <div class="text-center px-2">
                    <div class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">{{ $targetGradePercentage }}%</div>
                    <span class="text-[9.5px] text-slate-400 font-bold uppercase tracking-wider block">Ketuntasan Surat</span>
                </div>
                <div class="w-px h-10 bg-slate-800"></div>
                <div class="text-center px-2">
                    <div class="text-2xl sm:text-3xl font-black text-teal-300 font-mono">{{ $targetGradeAyatPercentage }}%</div>
                    <span class="text-[9.5px] text-slate-400 font-bold uppercase tracking-wider block">Ketuntasan Ayat</span>
                </div>
            </div>
        </div>

        <!-- Dua Progress Bar Berdampingan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-800/80">
            <!-- Progress Bar 1: Ketuntasan Surat -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Ketuntasan Surat Target Kelas {{ $studentGrade }}
                    </span>
                    <span class="font-mono font-extrabold text-emerald-400">
                        {{ $targetGradeCompleted }} / {{ $targetGradeTotal }} Surat ({{ $targetGradePercentage }}%)
                    </span>
                </div>
                <div class="w-full bg-slate-800/90 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700/80">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-700" style="width: {{ $targetGradePercentage }}%"></div>
                </div>
            </div>

            <!-- Progress Bar 2: Ketuntasan Ayat -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        Ketuntasan Ayat Target Kelas {{ $studentGrade }}
                    </span>
                    <span class="font-mono font-extrabold text-teal-300">
                        {{ $targetGradeCompletedAyat }} / {{ $targetGradeTotalAyat }} Ayat ({{ $targetGradeAyatPercentage }}%)
                    </span>
                </div>
                <div class="w-full bg-slate-800/90 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700/80">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-400 h-full rounded-full transition-all duration-700" style="width: {{ $targetGradeAyatPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6 Kartu Metrik Detail Capaian Hafalan Kelas Siswa -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Target Surat Kelas {{ $studentGrade }}</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $targetGradeTotal }} <span class="text-xs font-semibold text-slate-400">Surat</span></div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Kurikulum resmi Kelas {{ $studentGrade }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 shadow-2xs">
            <span class="text-[10.5px] font-bold text-emerald-500 uppercase tracking-wider block">Tuntas / Mutqin</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $targetGradeCompleted }} <span class="text-xs font-semibold text-slate-400">/ {{ $targetGradeTotal }}</span></div>
            <span class="text-[10px] text-emerald-600/80 font-medium">Hafalan lancar & diuji lulus</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 shadow-2xs">
            <span class="text-[10.5px] font-bold text-amber-500 uppercase tracking-wider block">Perlu Mengulang</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $targetGradeMengulang }} <span class="text-xs font-semibold text-slate-400">Surat</span></div>
            <span class="text-[10px] text-amber-600/80 font-medium">Perlu muraja'ah & setor ulang</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-indigo-500/20 shadow-2xs">
            <span class="text-[10.5px] font-bold text-indigo-500 uppercase tracking-wider block">Belum Disetor</span>
            <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $targetGradeBelumSetor }} <span class="text-xs font-semibold text-slate-400">Surat</span></div>
            <span class="text-[10px] text-indigo-600/80 font-medium">Sisa materi target kelas</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Target Total Ayat</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $targetGradeTotalAyat }} <span class="text-xs font-semibold text-slate-400">Ayat</span></div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Akumulasi ayat di Kelas {{ $studentGrade }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-teal-500/20 shadow-2xs">
            <span class="text-[10.5px] font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">Ayat Disetor</span>
            <div class="text-xl sm:text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">{{ $targetGradeCompletedAyat }} <span class="text-xs font-semibold text-slate-400">({{ $targetGradeAyatPercentage }}%)</span></div>
            <span class="text-[10px] text-teal-600/80 font-medium">Total ayat telah dihafal</span>
        </div>
    </div>

    <!-- Section Utama: Capaian Hafalan Surat Sesuai Kelas Siswa -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight">
                        Capaian Hafalan Surat Sesuai Kelas {{ $studentGrade }} ({{ $targetGradeTotal }} Surat)
                    </h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Informasi dan progres komplit evaluasi untuk setiap surat yang menjadi target kurikulum Kelas {{ $studentGrade }}.
                </p>
            </div>

            <!-- Controls: Filter Status & Toggle Mode Tampilan -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Filter Status Surat -->
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-bold gap-1">
                    <button type="button" onclick="filterClassSurahs('all')" id="filter-btn-all"
                        class="px-2.5 py-1.5 rounded-lg transition bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs">
                        Semua ({{ $targetGradeTotal }})
                    </button>
                    <button type="button" onclick="filterClassSurahs('lulus')" id="filter-btn-lulus"
                        class="px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-emerald-600">
                        ✓ Lulus ({{ $targetGradeCompleted }})
                    </button>
                    <button type="button" onclick="filterClassSurahs('mengulang')" id="filter-btn-mengulang"
                        class="px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-amber-600">
                        ↻ Mengulang ({{ $targetGradeMengulang }})
                    </button>
                    <button type="button" onclick="filterClassSurahs('belum')" id="filter-btn-belum"
                        class="px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-indigo-600">
                        Belum ({{ $targetGradeBelumSetor }})
                    </button>
                </div>

                <!-- Toggle Tampilan: Tabel vs Kartu -->
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-bold gap-1">
                    <button type="button" onclick="switchDisplayMode('table')" id="mode-btn-table"
                        class="px-2.5 py-1.5 rounded-lg transition bg-emerald-600 text-white shadow-xs flex items-center gap-1.5" title="Tampilan Tabel Buku Kontrol">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Tabel Buku Kontrol</span>
                    </button>
                    <button type="button" onclick="switchDisplayMode('cards')" id="mode-btn-cards"
                        class="px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5" title="Tampilan Kartu Visual">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Kartu Visual</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAMPILAN 1: TABEL BUKU KONTROL LENGKAP (DEFAULT) -->
        <div id="container-table" class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50 text-slate-400 font-bold uppercase text-[10px]">
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3">Surat Target Kelas {{ $studentGrade }}</th>
                        <th class="py-3 px-3 text-center">Jml Ayat</th>
                        <th class="py-3 px-3 text-center">Status Capaian</th>
                        <th class="py-3 px-3">Progres Ayat Disetor</th>
                        <th class="py-3 px-3">Rubrik Evaluasi</th>
                        <th class="py-3 px-3">Tgl & Waktu Setor</th>
                        <th class="py-3 px-3">Penguji</th>
                        <th class="py-3 px-4">Catatan Penguji</th>
                        @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                        <th class="py-3 px-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="table-body-surahs">
                    @php $seq = 1; @endphp
                    @foreach($targetGradeSurahs as $num => $s)
                        @php
                            $status = $surahStatus[$num] ?? ['has_deposited' => false, 'is_lulus' => false, 'latest' => null, 'deposit_count' => 0];
                            $latestRecord = $status['latest'] ?? null;
                            $statusCategory = $status['is_lulus'] ? 'lulus' : ($status['has_deposited'] ? 'mengulang' : 'belum');
                        @endphp
                        <tr data-status="{{ $statusCategory }}" class="surah-row hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-400">
                                {{ $seq++ }}
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-extrabold text-slate-900 dark:text-slate-100 text-sm flex items-center gap-1.5">
                                    <span>📖 {{ $s['name'] }}</span>
                                    <span class="text-[9.5px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">
                                        No. {{ $num }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $s['meaning'] }}
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px]">
                                    {{ $s['total_ayah'] }} Ayat
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                @if($status['is_lulus'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-black bg-emerald-600 text-white shadow-2xs">
                                        <span>✓</span> <span>LULUS / MUTQIN</span>
                                    </span>
                                @elseif($status['has_deposited'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-black bg-amber-500 text-white shadow-2xs">
                                        <span>↻</span> <span>MENGULANG</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        Belum Disetor
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if($latestRecord)
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $latestRecord->ayat_range }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400">
                                            {{ $latestRecord->ayat_count ?? $s['total_ayah'] }} / {{ $s['total_ayah'] }} Ayat
                                        </span>
                                        @if($status['deposit_count'] > 1)
                                            <span class="px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 text-[9px] font-bold">
                                                Setor {{ $status['deposit_count'] }}x
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if($latestRecord)
                                    <div class="grid grid-cols-3 gap-1 text-[9px] bg-slate-50 dark:bg-slate-800/80 p-1 rounded-md border border-slate-200/50 dark:border-slate-700/50 text-center font-bold">
                                        <div title="Makhrajul Huruf">
                                            <span class="block text-[8px] text-slate-400 font-normal">Makhraj</span>
                                            <span class="{{ $latestRecord->makhraj === 'Sangat Baik' ? 'text-emerald-600' : ($latestRecord->makhraj === 'Baik' ? 'text-teal-600' : 'text-amber-600') }}">
                                                {{ $latestRecord->makhraj }}
                                            </span>
                                        </div>
                                        <div class="border-x border-slate-200 dark:border-slate-700" title="Ilmu Tajwid">
                                            <span class="block text-[8px] text-slate-400 font-normal">Tajwid</span>
                                            <span class="{{ $latestRecord->tajwid === 'Sangat Baik' ? 'text-emerald-600' : ($latestRecord->tajwid === 'Baik' ? 'text-teal-600' : 'text-amber-600') }}">
                                                {{ $latestRecord->tajwid }}
                                            </span>
                                        </div>
                                        <div title="Kelancaran">
                                            <span class="block text-[8px] text-slate-400 font-normal">Lancar</span>
                                            <span class="{{ $latestRecord->kelancaran === 'Sangat Baik' ? 'text-emerald-600' : ($latestRecord->kelancaran === 'Baik' ? 'text-teal-600' : 'text-amber-600') }}">
                                                {{ $latestRecord->kelancaran }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($latestRecord)
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($latestRecord->deposit_date)->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ substr($latestRecord->deposit_time, 0, 5) }} WIB
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if($latestRecord)
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $latestRecord->examiner_name }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($latestRecord && $latestRecord->evaluation_notes)
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 italic line-clamp-2" title="{{ $latestRecord->evaluation_notes }}">
                                        "{{ $latestRecord->evaluation_notes }}"
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- TAMPILAN 2: KARTU VISUAL (GRID CARDS) -->
        <div id="container-cards" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
            @php $cardSeq = 1; @endphp
            @foreach($targetGradeSurahs as $num => $s)
                @php
                    $status = $surahStatus[$num] ?? ['has_deposited' => false, 'is_lulus' => false, 'latest' => null, 'deposit_count' => 0];
                    $latestRecord = $status['latest'] ?? null;
                    $statusCategory = $status['is_lulus'] ? 'lulus' : ($status['has_deposited'] ? 'mengulang' : 'belum');
                @endphp
                <div data-status="{{ $statusCategory }}"
                     class="surah-card p-4 rounded-2xl border transition text-left flex flex-col justify-between space-y-3 overflow-hidden
                     @if($status['is_lulus']) bg-emerald-50/60 dark:bg-emerald-950/25 border-emerald-300/80 dark:border-emerald-800/70 shadow-2xs
                     @elseif($status['has_deposited']) bg-amber-50/60 dark:bg-amber-950/25 border-amber-300/80 dark:border-amber-800/70 shadow-2xs
                     @else bg-slate-50/60 dark:bg-slate-850/30 border-slate-200 dark:border-slate-800 @endif">

                    <!-- Baris Atas: Nomor Target & Status Badge -->
                    <div class="flex items-start justify-between gap-1.5 min-w-0">
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="px-2 py-0.5 rounded-md font-black text-xs bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                #{{ $cardSeq++ }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">
                                No. {{ $num }}
                            </span>
                        </div>

                        <div class="shrink-0 whitespace-nowrap">
                            @if($status['is_lulus'])
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-600 text-white flex items-center gap-1 shadow-2xs">
                                    <span>✓</span> <span>LULUS</span>
                                </span>
                            @elseif($status['has_deposited'])
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white flex items-center gap-1 shadow-2xs">
                                    <span>↻</span> <span>MENGULANG</span>
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                    Belum Setor
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Surat -->
                    <div>
                        <div class="font-black text-base text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                            <span>📖 {{ $s['name'] }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $s['total_ayah'] }} Ayat • {{ $s['meaning'] }}
                        </div>
                    </div>

                    <!-- Detail Evaluasi Setoran -->
                    <div class="pt-2.5 border-t border-slate-200/60 dark:border-slate-800/60 text-xs space-y-2">
                        @if($latestRecord)
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10.5px]">
                                <span>📅 {{ \Carbon\Carbon::parse($latestRecord->deposit_date)->format('d M Y') }}</span>
                                <span class="font-extrabold text-emerald-600 dark:text-emerald-400">
                                    {{ $latestRecord->ayat_count ?? $s['total_ayah'] }} / {{ $s['total_ayah'] }} Ayat
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-1 text-[9.5px] bg-white/70 dark:bg-slate-900/70 p-1.5 rounded-lg border border-slate-200/50 dark:border-slate-700/50">
                                <div class="text-center">
                                    <span class="block text-slate-400 text-[8.5px]">Makhraj</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestRecord->makhraj }}</span>
                                </div>
                                <div class="text-center border-x border-slate-200/60 dark:border-slate-800">
                                    <span class="block text-slate-400 text-[8.5px]">Tajwid</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestRecord->tajwid }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-slate-400 text-[8.5px]">Kelancaran</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestRecord->kelancaran }}</span>
                                </div>
                            </div>

                            @if($latestRecord->evaluation_notes)
                                <div class="text-[10px] text-slate-600 dark:text-slate-300 italic line-clamp-2" title="{{ $latestRecord->evaluation_notes }}">
                                    "{{ $latestRecord->evaluation_notes }}"
                                </div>
                            @endif

                            <div class="text-[9.5px] text-slate-400 flex items-center justify-between pt-1">
                                <span>Penguji: <strong>{{ $latestRecord->examiner_name }}</strong></span>
                                @if($status['deposit_count'] > 1)
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">Setor {{ $status['deposit_count'] }}x</span>
                                @endif
                            </div>
                        @else
                            <div class="text-slate-400 italic text-[11px] py-3 text-center">
                                Belum ada catatan setoran untuk surat ini.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Riwayat Lengkap Setoran Siswa (Log Kronologis) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Riwayat Lengkap Setoran Siswa ({{ $records->count() }} Kali Setor)
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Seluruh rekam jejak log setoran hafalan Al-Qur'an ananda {{ $student->name }}.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-400 font-bold uppercase text-[10px]">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal & Waktu</th>
                        <th class="py-3 px-4">Surat & Rentang Ayat</th>
                        <th class="py-3 px-4">Rubrik Penilaian</th>
                        <th class="py-3 px-4">Hasil</th>
                        <th class="py-3 px-4">Penguji & Catatan</th>
                        @if(Auth::user()->canManageTahfidz())
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($records as $idx => $r)
                        @php
                            $surahMeta = $surahs[$r->surah_number] ?? null;
                            $surahGrade = $surahMeta['grade'] ?? null;
                            $isClassSurah = ($surahGrade === $studentGrade);
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($r->deposit_date)->locale('id')->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                    {{ substr($r->deposit_time, 0, 5) }} WIB
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 flex-wrap">
                                    <span>📖 {{ $r->surah_name }}</span>
                                    @if($r->surah_number)
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">No. {{ $r->surah_number }}</span>
                                    @endif
                                    @if($isClassSurah)
                                        <span class="text-[8.5px] px-1.5 py-0.2 rounded font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                            Target Kelas {{ $studentGrade }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $r->ayat_range }}</span>
                                    @if($r->ayat_count)
                                        <span class="px-1.5 py-0.2 rounded bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-[10px]">
                                            {{ $r->ayat_count }} Ayat
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col gap-0.5 text-[10px]">
                                    <span>Makhraj: <strong>{{ $r->makhraj }}</strong></span>
                                    <span>Tajwid: <strong>{{ $r->tajwid }}</strong></span>
                                    <span>Kelancaran: <strong>{{ $r->kelancaran }}</strong></span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($r->overall_grade === 'Lulus')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                        ✓ LULUS
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                        ↻ MENGULANG
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[11px]">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $r->examiner_name }}</div>
                                @if($r->evaluation_notes)
                                    <div class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1" title="{{ $r->evaluation_notes }}">
                                        "{{ $r->evaluation_notes }}"
                                    </div>
                                @endif
                            </td>
                            @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('tahfidz.edit', $r->id) }}" class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition" title="Edit Catatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('tahfidz.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus catatan setoran ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition cursor-pointer" title="Hapus Catatan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->hasAnyRole(['admin', 'super_admin']) ? 7 : 6 }}" class="py-10 text-center text-slate-400 text-xs">
                                Belum ada data setoran hafalan Al-Qur'an tercatat untuk ananda {{ $student->name }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accordion Rekap Global Seluruh 37 Surat Juz 30 (Hanya untuk Guru / Admin / Bukan Akun Orang Tua) -->
    @if(!Auth::user()->isParent())
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <button type="button" onclick="toggleJuz30GlobalRecap()" class="w-full p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition cursor-pointer">
            <div class="flex items-center gap-3 text-left">
                <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-sm shrink-0">
                    🌐
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200">
                        Rekapitulasi Global Seluruh 37 Surat Juz 30 (Akumulasi Lintas Jenjang)
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Total Juz 30: <strong>{{ $completedSurahs }} dari {{ $totalSurahs }} Surat Selesai ({{ $completionPercentage }}%)</strong>. Klik untuk melihat rincian seluruh tingkatan kelas.
                    </p>
                </div>
            </div>
            <svg id="chevron-juz30" class="w-5 h-5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div id="content-juz30-recap" class="hidden p-4 sm:p-6 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/20 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($groupedSurahs as $gradeKey => $g)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-800 dark:text-slate-200">
                                {{ $g['label'] }}
                            </span>
                            @if($gradeKey === $studentGrade)
                                <span class="px-2 py-0.2 rounded text-[9px] font-black bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 uppercase">
                                    Kelas Sekarang
                                </span>
                            @endif
                        </div>
                        <div class="space-y-1 max-h-48 overflow-y-auto pr-1 text-[11px]">
                            @foreach($g['surahs'] as $n => $item)
                                @php
                                    $st = $surahStatus[$n] ?? ['is_lulus' => false, 'has_deposited' => false];
                                @endphp
                                <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-slate-800/60 last:border-0">
                                    <span class="text-slate-700 dark:text-slate-300">
                                        #{{ $n }} {{ $item['name'] }}
                                    </span>
                                    @if($st['is_lulus'])
                                        <span class="text-emerald-600 font-bold text-[10px]">✓ Lulus</span>
                                    @elseif($st['has_deposited'])
                                        <span class="text-amber-500 font-bold text-[10px]">↻ Mengulang</span>
                                    @else
                                        <span class="text-slate-400 text-[10px]">-</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function filterClassSurahs(status) {
        const rows = document.querySelectorAll('.surah-row');
        const cards = document.querySelectorAll('.surah-card');
        const filterBtns = ['all', 'lulus', 'mengulang', 'belum'];

        filterBtns.forEach(b => {
            const btn = document.getElementById('filter-btn-' + b);
            if (!btn) return;
            if (b === status) {
                btn.className = 'px-2.5 py-1.5 rounded-lg transition bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs font-extrabold';
            } else {
                btn.className = 'px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white';
            }
        });

        rows.forEach(r => {
            const rStatus = r.getAttribute('data-status');
            if (status === 'all' || rStatus === status) {
                r.classList.remove('hidden');
            } else {
                r.classList.add('hidden');
            }
        });

        cards.forEach(c => {
            const cStatus = c.getAttribute('data-status');
            if (status === 'all' || cStatus === status) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });
    }

    function switchDisplayMode(mode) {
        const containerTable = document.getElementById('container-table');
        const containerCards = document.getElementById('container-cards');
        const btnTable = document.getElementById('mode-btn-table');
        const btnCards = document.getElementById('mode-btn-cards');

        if (mode === 'table') {
            containerTable.classList.remove('hidden');
            containerCards.classList.add('hidden');

            btnTable.className = 'px-2.5 py-1.5 rounded-lg transition bg-emerald-600 text-white shadow-xs flex items-center gap-1.5 font-bold';
            btnCards.className = 'px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 font-bold';
        } else {
            containerTable.classList.add('hidden');
            containerCards.classList.remove('hidden');

            btnCards.className = 'px-2.5 py-1.5 rounded-lg transition bg-emerald-600 text-white shadow-xs flex items-center gap-1.5 font-bold';
            btnTable.className = 'px-2.5 py-1.5 rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 font-bold';
        }
    }

    function toggleJuz30GlobalRecap() {
        const content = document.getElementById('content-juz30-recap');
        const chevron = document.getElementById('chevron-juz30');
        if (!content) return;

        const isHidden = content.classList.contains('hidden');
        if (isHidden) {
            content.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }
</script>
@endsection
