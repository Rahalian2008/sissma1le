@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    <!-- Student Hero Profile Card -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
            <div class="flex items-center gap-3.5 sm:gap-5 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-14 h-14 sm:w-20 sm:h-20 lg:w-22 lg:h-22 rounded-2xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-0.5 sm:p-1 shadow-lg shrink-0">
                        <div class="w-full h-full bg-slate-800 rounded-[14px] flex items-center justify-center font-black text-lg sm:text-2xl text-white">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </div>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center text-[10px]" title="Siswa Aktif">✓</span>
                </div>
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 px-2.5 sm:px-3 py-0.5 sm:py-1 bg-white/10 backdrop-blur-md rounded-full text-[11px] sm:text-xs font-semibold text-emerald-300 mb-1 sm:mb-1.5 border border-white/10 max-w-full truncate">
                        <span>Kelas {{ $student->schoolClass?->name ?? 'Siswa' }}</span>
                        <span>•</span>
                        <span>TP {{ $student->academicYear?->name ?? '2026/2027' }}</span>
                    </div>
                    <h2 class="text-base sm:text-2xl font-extrabold tracking-tight text-white truncate">{{ $student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 sm:gap-x-4 gap-y-0.5 text-xs text-slate-300 mt-0.5 sm:mt-1">
                        <span>NIS: <strong class="text-white font-mono">{{ $student->nis }}</strong></span>
                        <span>NISN: <strong class="text-white font-mono">{{ $student->nisn }}</strong></span>
                        <span class="truncate">Wali Kelas: <strong class="text-indigo-200">{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Smart Contextual Attendance Action -->
            <div class="shrink-0 w-full md:w-auto">
                @if($todayAttendance)
                    @if(!empty($todayAttendance->time_out))
                        <div class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center md:text-right">
                            <div class="text-[10px] sm:text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Presensi Hari Ini (Lengkap)</div>
                            <div class="text-xs sm:text-sm font-bold text-emerald-300 mt-0.5 flex items-center justify-center md:justify-end gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Masuk {{ substr($todayAttendance->time, 0, 5) }} • Pulang {{ substr($todayAttendance->time_out, 0, 5) }} WIB
                            </div>
                        </div>
                    @else
                        @php
                            $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');
                            $timeNowStr = $nowJakarta->format('H:i');
                            $outStartStr = substr($schedule['out_start'] ?? '15:00', 0, 5);
                            $outEndStr = substr($schedule['out_end'] ?? '17:30', 0, 5);
                            $isCheckoutOpen = $timeNowStr >= $outStartStr && $timeNowStr <= $outEndStr;
                            $isBeforeCheckout = $timeNowStr < $outStartStr;
                        @endphp
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <div class="px-3.5 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-center sm:text-right">
                                <div class="text-[10px] font-semibold text-slate-300 uppercase tracking-wider">Masuk: {{ substr($todayAttendance->time, 0, 5) }} WIB</div>
                                <div class="text-xs font-bold {{ $todayAttendance->status === 'HADIR' ? 'text-emerald-300' : 'text-amber-300' }}">
                                    {{ $todayAttendance->status }}
                                </div>
                            </div>
                            @if($isCheckoutOpen)
                                <a href="{{ route('attendance.selfie') }}" class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-400 hover:to-blue-400 text-white font-black rounded-xl shadow-lg shadow-sky-500/30 transition flex items-center justify-center gap-2 text-xs">
                                    <span>PRESENSI PULANG</span>
                                </a>
                            @elseif($isBeforeCheckout)
                                <div class="px-3.5 py-2 rounded-xl bg-sky-950/40 border border-sky-800/60 text-center sm:text-right text-xs">
                                    <div class="text-[10px] font-semibold text-sky-300 uppercase tracking-wider">Sesi Pulang</div>
                                    <div class="text-xs font-bold text-slate-200 flex items-center justify-center sm:justify-end gap-1">
                                        <span>🔒 Buka {{ $outStartStr }} WIB</span>
                                    </div>
                                </div>
                            @else
                                <div class="px-3.5 py-2 rounded-xl bg-slate-800/40 border border-slate-700 text-center sm:text-right text-xs">
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Sesi Pulang</div>
                                    <div class="text-xs font-bold text-slate-400">Tutup ({{ $outEndStr }} WIB)</div>
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    @php
                        $nowJakarta = $nowJakarta ?? \Carbon\Carbon::now('Asia/Jakarta');
                        $timeNowStr = $nowJakarta->format('H:i');
                        $inStartStr = substr($schedule['in_start'] ?? '06:00', 0, 5);
                    @endphp
                    @if($timeNowStr < $inStartStr)
                        <div class="px-4 py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center md:text-right">
                            <div class="text-[10px] font-semibold text-slate-300 uppercase tracking-wider">Sesi Presensi Masuk</div>
                            <div class="text-xs font-bold text-emerald-300 mt-0.5">
                                🔒 Buka Pukul {{ $inStartStr }} WIB
                            </div>
                        </div>
                    @else
                        <a href="{{ route('attendance.selfie') }}" class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black rounded-2xl shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2 text-xs sm:text-sm tracking-wide">
                            <svg class="w-4 h-4 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>PRESENSI MASUK SEKARANG</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Metric Cards (Grid 2 Kolom di Mobile, 4 di Desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <!-- 1. Kehadiran Semester -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Kehadiran</span>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-3xl font-black text-slate-900 dark:text-white tabular-num">{{ $attendanceRate }}%</span>
                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 hidden xs:inline">Semester Ini</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 sm:h-2 rounded-full mt-2 overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
                </div>
            </div>
        </div>

        <!-- 2. Total Poin Prestasi -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Poin Prestasi</span>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tabular-num">+{{ $totalAchievementPoints }}</span>
                    <span class="text-[10px] font-bold text-slate-400 hidden xs:inline">Poin</span>
                </div>
                <div class="mt-1 text-[10px] font-semibold text-amber-700 dark:text-amber-300 truncate">
                    {{ $rewardStatus['level'] }}
                </div>
            </div>
        </div>

        <!-- 3. Total Poin Pelanggaran -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Poin Tatib</span>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tabular-num">{{ $totalViolationPoints }}</span>
                    <span class="text-[10px] font-bold text-slate-400 hidden xs:inline">Poin</span>
                </div>
                <div class="mt-1 text-[10px] font-semibold {{ $guidanceStatus['action_needed'] ? 'text-rose-700 dark:text-rose-300' : 'text-emerald-700 dark:text-emerald-300' }} truncate">
                    {{ $guidanceStatus['level'] }}
                </div>
            </div>
        </div>

        <!-- 4. Progress 7 Kebiasaan Hari Ini -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">7 Kebiasaan</span>
                <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tabular-num">{{ $completedHabitsCount }}/{{ count($habits) }}</span>
                    <span class="text-[10px] font-semibold text-slate-400 hidden xs:inline">Checklist</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 sm:h-2 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitProgressPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pusat Menu & Layanan Siswa Terpadu (Unified Launcher Grid) -->
    @php
        $todayDhuhaRecord = $worshipTahfidzSummary['dhuha']['today_record'] ?? null;
        $todayDzuhurRecord = $worshipTahfidzSummary['dzuhur']['today_record'] ?? null;
        $todayKeputrianRecord = $worshipTahfidzSummary['keputrian']['today_record'] ?? null;
        $isFemaleStudent = $student && in_array(strtoupper($student->gender ?? ''), ['P', 'PEREMPUAN']);
        $hasExtraMember = isset($studentExtracurriculars) && $studentExtracurriculars->isNotEmpty();
        $secExtraQuick = $hasExtraMember ? $studentExtracurriculars->first(function($ex) {
            return in_array($ex->pivot->role ?? '', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true);
        }) : null;
        $primaryExtra = $secExtraQuick ?? ($hasExtraMember ? $studentExtracurriculars->first() : null);
        $primaryExtraAttendance = ($primaryExtra && isset($todayExtraAttendances)) ? $todayExtraAttendances->get($primaryExtra->id) : null;
    @endphp
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm">
                    ⚡
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight">
                        Layanan & Navigasi Terpadu Siswa
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Akses cepat presensi harian, modul belajar daring, perpustakaan, catatan hafalan, dan rapor kurnas.
                    </p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 self-start sm:self-center">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-6 gap-2.5 sm:gap-3.5">
            <!-- 1. Presensi Utama KBM -->
            <a href="{{ route('attendance.selfie') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-emerald-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📸</span>
                    @if($todayAttendance)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            ✓ {{ $todayAttendance->status }}
                        </span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 animate-pulse">
                            BELUM
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate">Presensi KBM</h4>
                    <p class="text-[10px] text-slate-400 truncate">Selfie Datang/Pulang</p>
                </div>
            </a>

            <!-- 2. Pengajuan Izin Siswa -->
            <a href="{{ route('attendance.leave') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-sky-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📝</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                        FORM
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 truncate">Pengajuan Izin</h4>
                    <p class="text-[10px] text-slate-400 truncate">Izin / Sakit / Dispen</p>
                </div>
            </a>

            <!-- 3. Shalat Dzuhur / Jum'at -->
            <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-indigo-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">🕌</span>
                    @if($todayDzuhurRecord)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            ✓ {{ $todayDzuhurRecord->status }}
                        </span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                            DZUHUR
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">Shalat Dzuhur</h4>
                    <p class="text-[10px] text-slate-400 truncate">Presensi Berjamaah</p>
                </div>
            </a>

            <!-- 4. Shalat Dhuha -->
            <a href="{{ route('worship.index', 'dhuha') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-amber-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">☀️</span>
                    @if($todayDhuhaRecord)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            ✓ {{ $todayDhuhaRecord->status }}
                        </span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                            DHUHA
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 truncate">Shalat Dhuha</h4>
                    <p class="text-[10px] text-slate-400 truncate">Pagi Berjamaah</p>
                </div>
            </a>

            <!-- 5. LMS Pembelajaran -->
            <a href="{{ route('lms.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-cyan-50/40 dark:bg-cyan-950/20 border-cyan-200/80 dark:border-cyan-800 hover:border-cyan-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">🎓</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300">
                        E-LEARNING
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 truncate">LMS Belajar</h4>
                    <p class="text-[10px] text-slate-400 truncate">Materi, Tugas & CBT</p>
                </div>
            </a>

            <!-- 6. Perpustakaan Digital -->
            <a href="{{ route('library.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-amber-50/40 dark:bg-amber-950/20 border-amber-200/80 dark:border-amber-800 hover:border-amber-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📚</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                        PUSTAKA
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 truncate">Perpustakaan</h4>
                    <p class="text-[10px] text-slate-400 truncate">Flip 3D & Sirkulasi</p>
                </div>
            </a>

            <!-- 7. Rapor Kurnas -->
            <a href="{{ route('rapor_kurnas.my_report') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-emerald-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📊</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        NILAI
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate">Rapor Kurnas</h4>
                    <p class="text-[10px] text-slate-400 truncate">e-Rapor Nasional 2025</p>
                </div>
            </a>

            <!-- 8. Raport Karakter AI -->
            <a href="{{ route('raport.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-indigo-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">🤖</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                        8 PILAR
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">Raport AI</h4>
                    <p class="text-[10px] text-slate-400 truncate">Analisis Karakter Siswa</p>
                </div>
            </a>

            <!-- 9. Hafalan Al-Qur'an (Tahfidz) -->
            <a href="{{ route('tahfidz.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-emerald-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📝</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        JUZ 30
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate">Tahfidz Quran</h4>
                    <p class="text-[10px] text-slate-400 truncate">Buku Mutaba'ah Setoran</p>
                </div>
            </a>

            <!-- Bimbingan Konseling (BK) -->
            <a href="{{ route('bk.curhat.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-gradient-to-br from-teal-500/10 via-emerald-500/5 to-slate-50 dark:from-teal-950/40 dark:via-emerald-950/20 dark:to-slate-800/60 border-teal-300/80 dark:border-teal-800/80 hover:border-teal-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">🤝</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                        BK ONLINE
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 truncate">Bimbingan Konseling</h4>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Ruang Curhat &amp; Konseling BK</p>
                </div>
            </a>

            <!-- AKPD Diagnostik (Khusus Siswa Kelas X / Fase E) -->
            @if($student?->isGradeX())
            <a href="{{ route('bk.akpd.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-gradient-to-br from-indigo-500/10 via-teal-500/5 to-slate-50 dark:from-indigo-950/40 dark:via-teal-950/20 dark:to-slate-800/60 border-indigo-300/80 dark:border-indigo-800/80 hover:border-indigo-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">📋</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                        FASE E (X)
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">AKPD Diagnostik</h4>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Asesmen Kebutuhan BK</p>
                </div>
            </a>
            @endif

            <!-- 10. Tata Tertib & Prestasi Siswa -->
            <a href="{{ route('violations.index') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-amber-400 hover:shadow-xs">
                <div class="flex items-start justify-between gap-1 mb-2">
                    <span class="text-xl">⚖️</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                        DISIPLIN
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 truncate">Tata Tertib</h4>
                    <p class="text-[10px] text-slate-400 truncate">Poin Pelanggaran & Prestasi</p>
                </div>
            </a>

            <!-- 11. Presensi Ekstrakurikuler (Jika Anggota) -->
            @if($hasExtraMember)
                @php
                    $isExtraSec = Auth::user()->isExtracurricularSecretary($primaryExtra?->id);
                @endphp
                <a href="{{ route('extracurricular.index', ['extra_id' => $primaryExtra->id, 'tab' => $isExtraSec ? 'manual' : 'selfie']) }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-cyan-50/40 dark:bg-cyan-950/20 border-cyan-200/80 dark:border-cyan-800 hover:border-cyan-400 hover:shadow-xs">
                    <div class="flex items-start justify-between gap-1 mb-2">
                        <span class="text-xl">🎯</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300 truncate max-w-[80px]">
                            {{ $isExtraSec ? 'SEKRETARIS' : 'EKSKUL' }}
                        </span>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 truncate">Ekstrakurikuler</h4>
                        <p class="text-[10px] text-slate-400 truncate">{{ $primaryExtra->name }}</p>
                    </div>
                </a>
            @endif

            <!-- 12. Presensi Keputrian (Khusus Siswi Perempuan) -->
            @if($isFemaleStudent)
                <a href="{{ route('worship.index', 'keputrian') }}" class="group p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-rose-50/40 dark:bg-rose-950/20 border-rose-200/80 dark:border-rose-800 hover:border-rose-400 hover:shadow-xs">
                    <div class="flex items-start justify-between gap-1 mb-2">
                        <span class="text-xl">🌸</span>
                        @if($todayKeputrianRecord)
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                ✓ {{ $todayKeputrianRecord->status }}
                            </span>
                        @else
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                KEPUTRIAN
                            </span>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 truncate">Keputrian</h4>
                        <p class="text-[10px] text-slate-400 truncate">Khusus Siswi Muslimah</p>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Active Workspace: 2-Column Section (Left: 7 Habits Checklist, Right: Notifications & Quick Agenda) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
        <!-- 7 Kebiasaan Quick Journal (Left 2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Gerakan 7 Kebiasaan Anak Hebat</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Centang pembiasaan positif yang telah Anda lakukan hari ini</p>
                </div>
                <a href="{{ route('habits.journal') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Jurnal Lengkap →
                </a>
            </div>

            <div class="space-y-2">
                @foreach($habits as $habit)
                    @php
                        $log = $todayHabitLogs->get($habit->id);
                        $isDone = $log && $log->is_completed;
                        $timeStatus = $habit->getTimeWindowStatus();
                        $canCheck = $timeStatus['is_active'];
                    @endphp
                    <div class="flex items-center justify-between p-2.5 sm:p-3 rounded-xl sm:rounded-2xl border {{ $isDone ? 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-800' }} transition gap-2">
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg font-bold text-xs flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $habit->order_number }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate {{ $isDone ? 'line-through text-slate-400 dark:text-slate-500' : '' }}">{{ $habit->name }}</h4>
                                <p class="text-[10px] sm:text-xs text-slate-400 truncate">
                                    <span class="font-semibold {{ $timeStatus['is_active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                        {{ $timeStatus['range'] }}
                                    </span>
                                    • {{ $habit->tagline }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($isDone)
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-600 text-white inline-flex items-center gap-1 shadow-xs">
                                    ✓ Selesai
                                </span>
                            @else
                                @if($canCheck)
                                    <form action="{{ route('habits.log.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="habit_id" value="{{ $habit->id }}">
                                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                                        <input type="hidden" name="is_completed" value="1">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition bg-slate-900 dark:bg-slate-100 dark:text-slate-900 hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white text-white shadow-xs cursor-pointer">
                                            Tandai
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled title="{{ $timeStatus['message'] }}" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed inline-flex items-center gap-1">
                                        🔒 Di Luar Jam
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Notifikasi Terbaru Siswa -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>🔔</span>
                        <span>Notifikasi Sekolah</span>
                    </h3>
                    <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Semua</a>
                </div>
                <div class="space-y-2.5">
                    @forelse($notifications as $notif)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 text-xs">
                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ $notif->title }}</div>
                            <div class="text-slate-500 dark:text-slate-300 mt-0.5 leading-normal text-[11px]">{{ $notif->message }}</div>
                            <div class="text-[10px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400 dark:text-slate-500">
                            Belum ada notifikasi baru untuk Anda.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Link Pustaka & LMS -->
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <a href="{{ route('lms.index') }}" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">
                    Buka LMS →
                </a>
                <a href="{{ route('library.index') }}" class="font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                    Buku Perpustakaan →
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan Ibadah Bersama & Tahfidz Al-Qur'an Siswa -->
    @include('dashboards.partials.worship_tahfidz_summary')

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital Siswa -->
    @include('dashboards.partials.lms_library_summary')

    <!-- Papan Peringkat Prestasi Keseluruhan Siswa SMAN 1 Lengkong -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-6 lg:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950/60 rounded-full text-[10px] sm:text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 mb-1">
                    <span>🏆 TATA TERTIB & PENGHARGAAN</span>
                    <span>•</span>
                    <span>SMAN 1 LENGKONG</span>
                </div>
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                    Papan Peringkat Siswa Berprestasi
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Akumulasi poin penghargaan prestasi akademik dan non-akademik siswa (Kategori R1 - R10).
                </p>
            </div>

            <!-- Posisi Siswa Yang Sedang Login -->
            <div class="p-2.5 sm:p-3 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 rounded-xl sm:rounded-2xl font-black text-xs shadow-sm shrink-0 flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-white/30 flex items-center justify-center text-sm">★</span>
                <div>
                    <div class="text-[9px] uppercase font-bold tracking-wider opacity-85">Posisi Prestasi Anda</div>
                    <div class="text-xs sm:text-sm font-black">
                        {{ $studentRank ? 'Peringkat #' . $studentRank : 'Belum Ada Peringkat' }}
                        <span class="text-[11px] font-semibold">({{ $totalAchievementPoints }} Poin)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="responsive-table-container">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-3">Rank</th>
                        <th class="py-2.5 px-3">Nama Siswa</th>
                        <th class="py-2.5 px-3">Kelas</th>
                        <th class="py-2.5 px-3 text-center">Jml Prestasi</th>
                        <th class="py-2.5 px-3 text-center">Total Poin</th>
                        <th class="py-2.5 px-3 text-right">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topAchievers as $index => $item)
                        @php
                            $isCurrentStudent = ($item['student']->id === $student->id);
                            $rank = $index + 1;
                        @endphp
                        <tr class="transition {{ $isCurrentStudent ? 'bg-amber-50/80 dark:bg-amber-950/40 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                            <td class="py-2.5 px-3">
                                @if($rank === 1)
                                    <span class="w-6 h-6 rounded-lg bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-xs">🥇</span>
                                @elseif($rank === 2)
                                    <span class="w-6 h-6 rounded-lg bg-slate-300 text-slate-800 font-black text-xs flex items-center justify-center shadow-xs">🥈</span>
                                @elseif($rank === 3)
                                    <span class="w-6 h-6 rounded-lg bg-amber-700 text-white font-black text-xs flex items-center justify-center shadow-xs">🥉</span>
                                @else
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-slate-900 dark:text-white {{ $isCurrentStudent ? 'text-amber-900 dark:text-amber-200' : '' }}">
                                        {{ $item['student']->name }}
                                    </span>
                                    @if($isCurrentStudent)
                                        <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-slate-950 font-black text-[9px]">
                                            ANDA
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $item['student']->nis }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 dark:text-slate-300 font-semibold">
                                {{ $item['student']->schoolClass?->name ?? 'Umum' }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300 text-xs tabular-num">
                                    {{ $item['count'] }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-black text-amber-600 dark:text-amber-400 text-xs sm:text-sm tabular-num">
                                +{{ $item['points'] }}
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <span class="px-2 py-0.5 rounded-lg font-bold text-[10px] {{ $item['points'] >= 200 ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300' : ($item['points'] >= 125 ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                                    {{ $item['reward_status']['level'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 dark:text-slate-500 italic">
                                Belum ada data capaian prestasi siswa terverifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
