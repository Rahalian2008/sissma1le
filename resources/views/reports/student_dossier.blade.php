@extends('layouts.app')

@section('title', 'Berkas Dossier & Raport Digital Siswa: ' . $student->name)

@section('content')
<div class="space-y-6 max-w-7xl 2xl:max-w-[1600px] mx-auto pb-12">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-950 dark:text-emerald-200 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shrink-0">✓</span>
                <span class="text-xs font-bold leading-relaxed">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold px-2 py-1">✕</button>
        </div>
    @endif

    <!-- Header with Actions -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-800/40 relative overflow-hidden flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-bold border border-indigo-500/30 mb-2.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                DOSSIER PERKEMBANGAN SISWA TERPADU • RAPORT DIGITAL AI
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $student->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                NIS: <strong class="text-white">{{ $student->nis }}</strong> • NISN: <strong class="text-white">{{ $student->nisn }}</strong> • Kelas: <strong class="text-indigo-300">{{ $student->schoolClass?->name }}</strong> • Wali Kelas: {{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }} • {{ $schoolName ?? 'SMA Negeri 1 Lengkong' }}
            </p>
            @php
                $latestAi = $dossier['latest_ai_raport'] ?? $student->aiRaport;
            @endphp
            @if($latestAi)
                <div class="mt-3 flex items-center gap-2 flex-wrap text-[11px] text-indigo-200">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-900/60 border border-indigo-700/50 font-medium">
                        <span>🕒 Data Raport AI:</span>
                        <strong class="text-white">{{ $latestAi->generated_at?->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }} WIB</strong>
                    </span>
                    <span class="text-slate-400 font-medium">• 8 Pilar Standar Penilaian Lengkap</span>
                </div>
            @endif
        </div>

        <div class="relative z-10 flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('raport.show', $student->id) }}"
                class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold rounded-xl shadow-md text-xs flex items-center gap-1.5 transition">
                <span>📊 Tampilan Raport Digital Interaktif</span>
            </a>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']) && !Auth::user()->hasRole('siswa') && !Auth::user()->hasRole('orang_tua'))
                <form action="{{ route('raport.generate_single', $student->id) }}" method="POST" onsubmit="return confirm('Generate ulang data Raport AI & Dossier untuk siswa ini?');" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold rounded-xl border border-slate-700 text-xs flex items-center gap-1.5 transition cursor-pointer">
                        <span>⚡ Generate AI</span>
                    </button>
                </form>
            @endif
            <div class="flex items-center gap-1 bg-slate-900/80 p-1 rounded-xl border border-emerald-500/30">
                <a href="{{ route('raport.print', ['student' => $student->id, 'paper' => 'a4']) }}" target="_blank"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs flex items-center gap-1 transition">
                    📄 Cetak A4
                </a>
                <a href="{{ route('raport.print', ['student' => $student->id, 'paper' => 'f4']) }}" target="_blank"
                    class="px-3 py-1.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-lg text-xs flex items-center gap-1 transition">
                    📜 Cetak F4
                </a>
            </div>
        </div>
    </div>

    @php
        $latestAiRaport = $dossier['latest_ai_raport'] ?? $student->aiRaport;
        $analysis = $dossier['ai_analysis'];
        $worshipSummary = $dossier['worship_summary'] ?? [
            'total' => 0, 'hadir' => 0, 'dhuha' => 0, 'dzuhur' => 0, 'keputrian' => 0, 'rate' => 0.0
        ];
        $tahfidzSummary = $dossier['tahfidz_summary'] ?? [
            'completed_surahs_count' => 0, 'total_juz30' => 37, 'student_grade' => 'X', 'progress_pct' => 0.0, 'status' => 'Pemula'
        ];
    @endphp

    <!-- 8 PILAR PERKEMBANGAN TERPADU SISWA & EKSTRAKURIKULER -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                8 PILAR STANDAR PENILAIAN PERKEMBANGAN TERPADU
            </h3>
            <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400">Indeks Perkembangan AI: <strong>{{ $latestAiRaport?->development_index ?? ($analysis['metrics']['development_index'] ?? 0) }}/100</strong></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Presensi Harian -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">1. Presensi Harian</span>
                        <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">🕒</span>
                    </div>
                    @php
                        $attTotal = $latestAiRaport?->attendance_total ?? $student->attendances()->count();
                        $attRate = ($attTotal === 0) ? 0.0 : ($latestAiRaport?->attendance_rate ?? $analysis['metrics']['attendance_rate']);
                        $attHadir = $latestAiRaport?->attendance_present ?? $analysis['metrics']['present_count'];
                        $attLate = $latestAiRaport?->attendance_late ?? $analysis['metrics']['late_count'];
                        $attPermit = $latestAiRaport?->attendance_permit ?? $analysis['metrics']['permit_count'];
                        $attSick = $latestAiRaport?->attendance_sick ?? $analysis['metrics']['sick_count'];
                        $attAlpha = $latestAiRaport?->attendance_alpha ?? $analysis['metrics']['alpha_count'];
                    @endphp
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($attRate, 1) }}%</div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Total {{ $attTotal }} Hari Efektif</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px]">
                    <span class="text-emerald-600 font-bold">H: {{ $attHadir }}</span>
                    <span class="text-amber-600 font-semibold">T: {{ $attLate }}</span>
                    <span class="text-sky-600 font-semibold">I: {{ $attPermit }}</span>
                    <span class="text-pink-600 font-semibold">S: {{ $attSick }}</span>
                    <span class="text-rose-600 font-bold">A: {{ $attAlpha }}</span>
                </div>
            </div>

            <!-- 2. Poin Prestasi -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">2. Poin Prestasi</span>
                        <span class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">🏆</span>
                    </div>
                    @php
                        $achPts = $latestAiRaport?->achievement_points ?? $student->totalAchievementPoints();
                        $rewardTier = $latestAiRaport?->reward_tier ?? ($dossier['reward_status']['level'] ?? 'Dasar');
                    @endphp
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">+{{ $achPts }} <span class="text-xs font-normal text-slate-400">Pts</span></div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Tier: <strong class="text-slate-700 dark:text-slate-200">{{ $rewardTier }}</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Total {{ $dossier['achievements']->count() }} Prestasi</span>
                    <span class="text-amber-600 font-bold">R1 - R10</span>
                </div>
            </div>

            <!-- 3. Pelanggaran Tata Tertib -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">3. Pelanggaran</span>
                        <span class="w-7 h-7 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold">⚠️</span>
                    </div>
                    @php
                        $vioPts = $latestAiRaport?->violation_points ?? $student->totalViolationPoints();
                        $guidanceTier = $latestAiRaport?->guidance_tier ?? ($dossier['guidance_status']['level'] ?? 'Aman');
                    @endphp
                    <div class="text-2xl font-black {{ $vioPts > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $vioPts }} <span class="text-xs font-normal text-slate-400">Pts</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Status: <strong class="text-slate-700 dark:text-slate-200">{{ $guidanceTier }}</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Total {{ $dossier['violations']->count() }} Kasus</span>
                    <span class="{{ $vioPts > 0 ? 'text-rose-600' : 'text-emerald-600' }} font-bold">P1 - P6</span>
                </div>
            </div>

            <!-- 4. 7 Karakter Hebat -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">4. 7 Karakter Hebat</span>
                        <span class="w-7 h-7 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm font-bold">🌟</span>
                    </div>
                    @php
                        $habitScore = $latestAiRaport?->habit_score ?? $analysis['metrics']['habit_score'];
                        $habitStatus = $latestAiRaport?->habit_status ?? $analysis['metrics']['habit_status'];
                    @endphp
                    <div class="text-2xl font-black text-teal-600 dark:text-teal-400">{{ number_format($habitScore, 1) }}%</div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Predikat: <strong class="text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', $habitStatus) }}</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>KAIH 7 Kebiasaan</span>
                    <span class="text-teal-600 font-bold">Karakter Unggul</span>
                </div>
            </div>

            <!-- 5. Shalat Dzuhur / Jum'at -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">5. Shalat Dzuhur / Jum'at</span>
                        <span class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">🕌</span>
                    </div>
                    @php
                        $dzTotal = $latestAiRaport?->worship_dzuhur_total ?? $analysis['metrics']['worship_dzuhur_total'];
                        $dzHadir = $latestAiRaport?->worship_dzuhur_hadir ?? $analysis['metrics']['worship_dzuhur_hadir'];
                        $dzRate = $latestAiRaport?->worship_dzuhur_rate ?? $analysis['metrics']['worship_dzuhur_rate'];
                    @endphp
                    <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($dzRate, 1) }}%</div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $dzHadir }}/{{ $dzTotal }} Sesi</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Putra & Putri (Semua Siswa)</span>
                    <span class="text-indigo-600 font-bold">{{ $dzHadir }}x Hadir</span>
                </div>
            </div>

            <!-- 6. Shalat Dhuha -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">6. Shalat Dhuha</span>
                        <span class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">☀️</span>
                    </div>
                    @php
                        $dhTotal = $latestAiRaport?->worship_dhuha_total ?? $analysis['metrics']['worship_dhuha_total'];
                        $dhHadir = $latestAiRaport?->worship_dhuha_hadir ?? $analysis['metrics']['worship_dhuha_hadir'];
                        $dhRate = $latestAiRaport?->worship_dhuha_rate ?? $analysis['metrics']['worship_dhuha_rate'];
                    @endphp
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($dhRate, 1) }}%</div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $dhHadir }}/{{ $dhTotal }} Sesi</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Pembiasaan Pagi</span>
                    <span class="text-amber-600 font-bold">{{ $dhHadir }}x Hadir</span>
                </div>
            </div>

            <!-- 7. Presensi Keputrian -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">7. Keputrian</span>
                        <span class="w-7 h-7 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center text-xs font-bold">🧕</span>
                    </div>
                    @php
                        $isFemale = in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true);
                        $kepTotal = $latestAiRaport?->worship_keputrian_total ?? $analysis['metrics']['worship_keputrian_total'];
                        $kepHadir = $latestAiRaport?->worship_keputrian_hadir ?? $analysis['metrics']['worship_keputrian_hadir'];
                        $kepRate = $latestAiRaport?->worship_keputrian_rate ?? $analysis['metrics']['worship_keputrian_rate'];
                    @endphp
                    @if(!$isFemale)
                        <div class="text-base font-black text-slate-400 mt-1">Khusus Siswi</div>
                        <p class="text-[10px] text-slate-400 mt-1 leading-snug">Tidak dibebankan bagi siswa laki-laki.</p>
                    @else
                        <div class="text-2xl font-black text-pink-600 dark:text-pink-400">{{ number_format($kepRate, 1) }}%</div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Hadir: <strong class="text-slate-700 dark:text-slate-200">{{ $kepHadir }}/{{ $kepTotal }} Sesi</strong></p>
                    @endif
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Siswa Perempuan</span>
                    <span class="text-pink-600 font-bold">{{ $isFemale ? "{$kepHadir}x Hadir" : 'N/A' }}</span>
                </div>
            </div>

            <!-- 8. Tahfidz Juz 30 -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">8. Tahfidz Juz 30</span>
                        <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">📖</span>
                    </div>
                    @php
                        $tahfidzCompleted = $latestAiRaport?->tahfidz_completed_count ?? $analysis['metrics']['completed_surahs_count'];
                        $tahfidzTarget = $latestAiRaport?->tahfidz_target_count ?? $analysis['metrics']['total_target_surahs'];
                        $tahfidzPct = $latestAiRaport?->tahfidz_progress_pct ?? $analysis['metrics']['tahfidz_progress_pct'];
                        $tahfidzStatus = $latestAiRaport?->tahfidz_status ?? $analysis['metrics']['tahfidz_status'];
                    @endphp
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $tahfidzCompleted }} <span class="text-xs font-normal text-slate-400">/ {{ $tahfidzTarget }} Surat</span></div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Progres: <strong class="text-slate-700 dark:text-slate-200">{{ number_format($tahfidzPct, 1) }}%</strong></p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-500">
                    <span>Target Kelas {{ $student->getNormalizedGrade() ?: 'X' }}</span>
                    <span class="text-emerald-600 font-bold">{{ $tahfidzStatus }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Multi-Dimensional Synthesis Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-2">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                Ringkasan Analisis Evaluasi Perkembangan Siswa (AI System)
            </h3>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                Sintesis Multidimensi Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Ringkasan & P5 -->
            <div class="md:col-span-2 bg-sky-50/70 dark:bg-slate-800/60 p-5 rounded-2xl border border-sky-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-sky-800 dark:text-sky-300 mb-1.5 flex items-center gap-1.5">
                    <span>📊</span> Ringkasan Komprehensif Eksekutif
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->ringkasan_komprehensif ?? ($analysis['ringkasan_komprehensif'] ?? '-') }}</p>
            </div>

            <div class="md:col-span-2 bg-fuchsia-50/70 dark:bg-slate-800/60 p-5 rounded-2xl border border-fuchsia-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-fuchsia-800 dark:text-fuchsia-300 mb-1.5 flex items-center gap-1.5">
                    <span>🌟</span> Karakter Profil Pelajar Pancasila (Kurikulum Merdeka)
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->catatan_profil_pelajar ?? ($analysis['catatan_profil_pelajar'] ?? '-') }}</p>
            </div>

            <div class="bg-indigo-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-indigo-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-indigo-800 dark:text-indigo-300 mb-1 flex items-center gap-1">
                    <span>📝</span> Penilaian Terpadu Siswa
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->penilaian_siswa ?? ($analysis['penilaian_siswa'] ?? '-') }}</p>
            </div>

            <div class="bg-emerald-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-emerald-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-300 mb-1 flex items-center gap-1">
                    <span>📈</span> Tren Perkembangan & Potensi
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->perkembangan ?? ($analysis['perkembangan'] ?? '-') }}</p>
            </div>

            <div class="bg-amber-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-amber-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-amber-800 dark:text-amber-300 mb-1 flex items-center gap-1">
                    <span>🔍</span> Analisis Kendala & Masalah
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->analisis_masalah ?? ($analysis['analisis_masalah'] ?? '-') }}</p>
            </div>

            <div class="bg-teal-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-teal-100 dark:border-slate-700">
                <h4 class="text-xs font-black text-teal-800 dark:text-teal-300 mb-1 flex items-center gap-1">
                    <span>💡</span> Strategi Pemecahan & Solusi
                </h4>
                <p class="text-xs sm:text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $latestAiRaport?->pemecahan_masalah ?? ($analysis['pemecahan_masalah'] ?? '-') }}</p>
            </div>
        </div>
    </div>

    <!-- Prestasi & Pelanggaran Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Prestasi -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                <span>1. Catatan Prestasi Terverifikasi</span>
                <span class="text-amber-600 font-black text-xs">+{{ $student->totalAchievementPoints() }} Poin</span>
            </h3>
            <div class="space-y-2.5 text-xs">
                @forelse($dossier['achievements'] as $ach)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ach->title }}</span>
                            <span class="font-black text-amber-600">+{{ $ach->points }}</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $ach->date->format('d/m/Y') }} • Kategori: {{ $ach->category?->code ?? '-' }} • {{ $ach->level }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada data prestasi.</div>
                @endforelse
            </div>
        </div>

        <!-- Pelanggaran -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                <span>2. Catatan Pelanggaran Tata Tertib</span>
                <span class="text-rose-600 font-black text-xs">{{ $student->totalViolationPoints() }} Poin</span>
            </h3>
            <div class="space-y-2.5 text-xs">
                @forelse($dossier['violations'] as $viol)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $viol->title }}</span>
                            <span class="font-black text-rose-600">{{ $viol->points }} Poin</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $viol->date->format('d/m/Y') }} • Kategori: {{ $viol->category?->code ?? '-' }} • Lokasi: {{ $viol->location ?? '-' }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Alhamdulillah! Tidak ada catatan pelanggaran tata tertib.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Catatan Pembinaan BK & Konseling -->
    @if(isset($dossier['guidances']) && $dossier['guidances']->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <span>🧭</span> 3. Riwayat Pembinaan & Konseling BK
            </h3>
            <div class="space-y-2.5 text-xs">
                @foreach($dossier['guidances'] as $g)
                    <div class="p-4 bg-purple-50/60 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-800/50 rounded-2xl">
                        <div class="flex items-center justify-between font-bold text-purple-950 dark:text-purple-200 mb-1">
                            <span>{{ $g->followUpLabel() }} ({{ $g->date->format('d M Y') }})</span>
                            <span class="px-2 py-0.5 bg-purple-200 dark:bg-purple-800 rounded text-[10px]">{{ $g->status }}</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $g->recommendation }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
