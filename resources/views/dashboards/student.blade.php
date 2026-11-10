@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6 max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto">
    <!-- Student Hero Profile Card -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-2xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-1 shadow-lg shrink-0">
                        <div class="w-full h-full bg-slate-800 rounded-[14px] flex items-center justify-center font-black text-xl sm:text-2xl text-white">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </div>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center text-[10px]" title="Siswa Aktif">✓</span>
                </div>
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[11px] sm:text-xs font-semibold text-emerald-300 mb-1.5 sm:mb-2 border border-white/10 max-w-full truncate">
                        <span>Kelas {{ $student->schoolClass?->name ?? 'XI-IPA-1' }}</span>
                        <span>•</span>
                        <span>TP {{ $student->academicYear?->name ?? '2026/2027' }}</span>
                    </div>
                    <h2 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white truncate">{{ $student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 sm:gap-x-4 gap-y-1 text-xs text-slate-300 mt-1">
                        <span>NIS: <strong class="text-white font-mono">{{ $student->nis }}</strong></span>
                        <span>NISN: <strong class="text-white font-mono">{{ $student->nisn }}</strong></span>
                        <span class="truncate">Wali: <strong class="text-indigo-200">{{ $student->schoolClass?->homeroomTeacher?->name ?? 'Siti Rahmawati, M.Pd' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Presensi Action Button -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                @if($todayAttendance)
                    @if(!empty($todayAttendance->time_out))
                        <div class="px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center sm:text-right">
                            <div class="text-[10px] sm:text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Presensi Hari Ini (Lengkap)</div>
                            <div class="text-xs sm:text-sm font-bold text-emerald-300 mt-0.5 flex items-center justify-center sm:justify-end gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Masuk {{ substr($todayAttendance->time, 0, 5) }} • Pulang {{ substr($todayAttendance->time_out, 0, 5) }} WIB
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center sm:text-right">
                                <div class="text-[10px] font-semibold text-slate-300 uppercase tracking-wider">Masuk: {{ substr($todayAttendance->time, 0, 5) }} WIB</div>
                                <div class="text-xs font-bold {{ $todayAttendance->status === 'HADIR' ? 'text-emerald-300' : 'text-amber-300' }}">
                                    {{ $todayAttendance->status }}
                                </div>
                            </div>
                            <a href="{{ route('attendance.selfie') }}" class="px-4 py-2.5 sm:py-3 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-400 hover:to-blue-400 text-white font-black rounded-2xl shadow-lg shadow-sky-500/30 transition flex items-center justify-center gap-2 text-xs">
                                <span>PRESENSI PULANG</span>
                            </a>
                        </div>
                    @endif
                @else
                    <a href="{{ route('attendance.selfie') }}" class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black rounded-2xl shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2.5 text-xs sm:text-sm tracking-wide">
                        <svg class="w-5 h-5 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>PRESENSI SELFIE SEKARANG</span>
                    </a>
                @endif

                <!-- Presensi Shalat Dzuhur Mandiri (Putra & Putri) -->
                @php
                    $todayDzuhurRecord = $worshipTahfidzSummary['dzuhur']['today_record'] ?? null;
                    $isDzuhurPresent = $todayDzuhurRecord && in_array($todayDzuhurRecord->status, ['HADIR', 'TERLAMBAT', 'DISPENSASI', 'IZIN', 'SAKIT', 'UDZUR_SYARI'], true);
                @endphp
                @if($isDzuhurPresent)
                    <div class="px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-indigo-950/60 backdrop-blur-md border border-indigo-500/30 text-center sm:text-right">
                        <div class="text-[10px] sm:text-[11px] font-semibold text-indigo-300 uppercase tracking-wider">Shalat Dzuhur</div>
                        <div class="text-xs sm:text-sm font-bold text-emerald-300 mt-0.5 flex items-center justify-center sm:justify-end gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            {{ $todayDzuhurRecord->status }} ({{ substr($todayDzuhurRecord->time, 0, 5) }} WIB)
                        </div>
                    </div>
                @else
                    <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 hover:from-indigo-500 hover:to-sky-400 text-white font-black rounded-2xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2.5 text-xs sm:text-sm tracking-wide">
                        <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>ABSEN DZUHUR MANDIRI</span>
                    </a>
                @endif

                <!-- Presensi Ekstrakurikuler (Aktif Jika Siswa Terdaftar Anggota) -->
                @if(isset($studentExtracurriculars) && $studentExtracurriculars->isNotEmpty())
                    @php
                        $heroSecExtra = $studentExtracurriculars->first(function($ex) {
                            return in_array($ex->pivot->role ?? '', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true);
                        });
                        $primaryExtra = $heroSecExtra ?? $studentExtracurriculars->first();
                        $isHeroSec = (bool) $heroSecExtra || auth()->user()->hasRole('sekretaris_ekstra');
                        $extraAttendanceToday = isset($todayExtraAttendances) ? $todayExtraAttendances->get($primaryExtra->id) : null;
                    @endphp
                    @if($extraAttendanceToday && !$isHeroSec)
                        <div class="px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-cyan-950/60 backdrop-blur-md border border-cyan-500/30 text-center sm:text-right">
                            <div class="text-[10px] sm:text-[11px] font-semibold text-cyan-300 uppercase tracking-wider truncate max-w-[170px]">{{ $primaryExtra->name }}</div>
                            <div class="text-xs sm:text-sm font-bold text-emerald-300 mt-0.5 flex items-center justify-center sm:justify-end gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                {{ $extraAttendanceToday->status }} ({{ substr($extraAttendanceToday->time, 0, 5) }} WIB)
                            </div>
                        </div>
                    @else
                        <a href="{{ route('extracurricular.index', ['extra_id' => $primaryExtra->id, 'tab' => $isHeroSec ? 'manual' : 'selfie']) }}" class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-3.5 bg-gradient-to-r from-cyan-600 via-teal-600 to-emerald-600 hover:from-cyan-500 hover:to-emerald-500 text-white font-black rounded-2xl shadow-lg shadow-cyan-600/30 transition flex items-center justify-center gap-2.5 text-xs sm:text-sm tracking-wide">
                            <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                            </svg>
                            <span>{{ $isHeroSec ? 'ABSENKAN ANGGOTA EKSKUL' : 'PRESENSI EKSKUL' }}</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Menu / Ikon Cepat Presensi Siswa -->
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
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm">
                    ⚡
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight">
                        Menu Cepat Presensi Siswa
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Akses instan seluruh jenis presensi harian: Presensi Utama{{ $hasExtraMember ? ', Ekskul' : '' }}, Shalat Dzuhur, Shalat Dhuha{{ $isFemaleStudent ? ', & Keputrian' : '' }}.
                    </p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 self-start sm:self-center">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-{{ 5 + ($hasExtraMember ? 1 : 0) + ($isFemaleStudent ? 1 : 0) }} gap-3 sm:gap-4">
            <!-- LMS Pembelajaran (Ruang Kelas & CBT) -->
            <a href="{{ route('lms.index') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-cyan-50/40 dark:bg-cyan-950/20 border-cyan-200/80 dark:border-cyan-800 hover:border-cyan-400 hover:shadow-md">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300 shrink-0 whitespace-nowrap">
                        KELAS SAYA
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition truncate">
                        LMS Pembelajaran
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Materi, Tugas, CBT & Nilai
                    </p>
                </div>
            </a>

            <!-- 1. Presensi Utama -->
            <a href="{{ route('attendance.selfie') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden {{ $todayAttendance ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-emerald-400 hover:shadow-md' }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    @if($todayAttendance)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            ✓ {{ $todayAttendance->status }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 animate-pulse shrink-0 whitespace-nowrap">
                            BELUM
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                        Presensi Utama
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $todayAttendance ? (!empty($todayAttendance->time_out) ? substr($todayAttendance->time, 0, 5).' - '.substr($todayAttendance->time_out, 0, 5).' WIB' : substr($todayAttendance->time, 0, 5).' WIB (Belum Pulang)') : (isset($schedule) ? $schedule['in_start'].' - '.$schedule['in_late_cutoff'].' WIB' : 'Selfie / Jam Masuk Pagi') }}
                    </p>
                </div>
            </a>

            <!-- 2. Pengajuan Izin Siswa -->
            <a href="{{ route('attendance.leave') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-sky-400 hover:shadow-md">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300 shrink-0 whitespace-nowrap">
                        FORM IZIN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition truncate">
                        Pengajuan Izin
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Form Izin, Sakit & Dispensasi
                    </p>
                </div>
            </a>

            <!-- 2. Presensi Ekstrakurikuler (Tampil bagi Siswa Anggota Ekstrakurikuler) -->
            @if($hasExtraMember)
            @php
                $isExtraSec = Auth::user()->isExtracurricularSecretary($primaryExtra?->id);
            @endphp
            <a href="{{ route('extracurricular.index', ['extra_id' => $primaryExtra->id, 'tab' => $isExtraSec ? 'manual' : 'selfie']) }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden {{ $primaryExtraAttendance ? 'bg-cyan-50/40 dark:bg-cyan-950/20 border-cyan-200 dark:border-cyan-800 hover:border-cyan-300' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-cyan-400 hover:shadow-md' }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    @if($isExtraSec)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 border border-amber-300 shrink-0 whitespace-nowrap">
                            SEKRETARIS EKSKUL
                        </span>
                    @elseif($primaryExtraAttendance)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            ✓ {{ $primaryExtraAttendance->status }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300 shrink-0 whitespace-nowrap">
                            ANGGOTA EKSKUL
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition truncate">
                        Presensi Ekstrakurikuler
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $primaryExtra->name }} {{ $isExtraSec ? '• Absenkan Rekan' : '' }}
                    </p>
                </div>
            </a>
            @endif

            <!-- 3. Presensi Shalat Dzuhur / Jum'at -->
            <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden {{ $isDzuhurPresent ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-800 hover:border-indigo-300' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-indigo-400 hover:shadow-md' }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    @if($isDzuhurPresent)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            ✓ {{ $todayDzuhurRecord->status }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 shrink-0 whitespace-nowrap">
                            DZUHUR
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                        Presensi Shalat Dzuhur
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $isDzuhurPresent ? substr($todayDzuhurRecord->time, 0, 5).' WIB' : 'Dzuhur / Jum\'at Berjamaah' }}
                    </p>
                    @if(!$isDzuhurPresent)
                        <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 mt-1 inline-flex items-center gap-1">Absen Dzuhur Mandiri →</span>
                    @endif
                </div>
            </a>

            <!-- 4. Presensi Shalat Dhuha -->
            <a href="{{ route('worship.index', 'dhuha') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden {{ $todayDhuhaRecord ? 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800 hover:border-amber-300' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-amber-400 hover:shadow-md' }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    @if($todayDhuhaRecord)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            ✓ {{ $todayDhuhaRecord->status }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 shrink-0 whitespace-nowrap">
                            DHUHA
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">
                        Presensi Shalat Dhuha
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $todayDhuhaRecord ? substr($todayDhuhaRecord->time, 0, 5).' WIB' : 'Pagi Berjamaah' }}
                    </p>
                </div>
            </a>

            <!-- 5. Presensi Keputrian (Tampilkan Khusus Siswa Perempuan) -->
            @if($isFemaleStudent)
            <a href="{{ route('worship.index', 'keputrian') }}" class="group p-4 rounded-2xl border transition duration-200 flex flex-col justify-between overflow-hidden {{ $todayKeputrianRecord ? 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800 hover:border-rose-300' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 hover:border-rose-400 hover:shadow-md' }}">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    @if($todayKeputrianRecord)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            ✓ {{ $todayKeputrianRecord->status }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300 shrink-0 whitespace-nowrap">
                            KEPUTRIAN
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition truncate">
                        Presensi Keputrian
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $todayKeputrianRecord ? substr($todayKeputrianRecord->time, 0, 5).' WIB' : 'Khusus Siswi Muslimah' }}
                    </p>
                </div>
            </a>
            @endif
        </div>
    </div>

    <!-- Pusat Navigasi Menu Siswa Terpadu -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm">
                    🧭
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight">
                        Layanan & Menu Siswa Terpadu
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Akses seluruh fitur akademik dan kesiswaan: Kurikulum, Rapor Kurnas, Perpustakaan, dan Arsip Dokumen.
                    </p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 self-start sm:self-center">
                Aplikasi Siswa
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- 1. Rapor Kurnas -->
            <a href="{{ route('rapor_kurnas.my_report') }}" class="group p-3.5 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    📊
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">Rapor Kurnas</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Capaian nilai & e-Rapor 2025</div>
                </div>
            </a>

            <!-- 2. Raport Digital -->
            <a href="{{ route('raport.index') }}" class="group p-3.5 rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-200/80 dark:border-indigo-900/60 hover:border-indigo-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    📑
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">Raport Digital</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Analisis 8 pilar kepribadian</div>
                </div>
            </a>

            <!-- 3. Tata Tertib Siswa -->
            <a href="{{ route('violations.index') }}" class="group p-3.5 rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    ⚖️
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">Tata Tertib Siswa</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Skor poin disiplin & prestasi</div>
                </div>
            </a>

            <!-- 4. Hafalan Al-Qur'an -->
            <a href="{{ route('tahfidz.index') }}" class="group p-3.5 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    📖
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">Hafalan Al-Qur'an</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Catatan setoran Juz 30</div>
                </div>
            </a>

            <!-- 5. Perpustakaan Digital -->
            <a href="{{ route('library.index') }}" class="group p-3.5 rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    📚
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">Perpustakaan</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">E-reader 3D & katalog buku</div>
                </div>
            </a>

            <!-- 6. Adm. Ketatausahaan -->
            <a href="{{ route('reports.index') }}" class="group p-3.5 rounded-2xl bg-purple-50/40 dark:bg-purple-950/20 border border-purple-200/80 dark:border-purple-900/60 hover:border-purple-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-base mb-2 group-hover:scale-110 transition">
                    🗂️
                </div>
                <div>
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition truncate">Adm. Dokumen</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Arsip & rekap data pribadi</div>
                </div>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards (Presence Rate, Prestasi, Pelanggaran, 7 Kebiasaan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 3xl:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Kehadiran -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tingkat Kehadiran</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $attendanceRate }}%</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Semester Ini</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
            </div>
        </div>

        <!-- 2. TOTAL PRESTASI (R1 - R10) -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Poin Prestasi</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $totalAchievementPoints }}</span>
                <span class="text-xs font-bold text-slate-400">Poin Akumulasi</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-bold text-amber-700 dark:text-amber-300">{{ $rewardStatus['level'] }}</span>
            </div>
        </div>

        <!-- 3. TOTAL PELANGGARAN (P1 - P6) - DIPISAH TEGAS! -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Poin Pelanggaran</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-rose-600 dark:text-rose-400">{{ $totalViolationPoints }}</span>
                <span class="text-xs font-bold text-slate-400">Poin Akumulasi</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full {{ $guidanceStatus['action_needed'] ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                <span class="font-bold {{ $guidanceStatus['action_needed'] ? 'text-rose-700 dark:text-rose-300' : 'text-emerald-700 dark:text-emerald-300' }}">{{ $guidanceStatus['level'] }}</span>
            </div>
        </div>

        <!-- 4. Progress 7 Kebiasaan Hari Ini -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">7 Kebiasaan Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $completedHabitsCount }}/{{ count($habits) }}</span>
                <span class="text-xs font-semibold text-slate-400">Terlaksana</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitProgressPercentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Section: 7 Habits Checklist & AI / Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 7 Kebiasaan Quick Journal (Left 2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Gerakan 7 Kebiasaan Anak Indonesia Hebat</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Centang aktivitas pembiasaan positif yang telah Anda lakukan hari ini</p>
                </div>
                <a href="{{ route('habits.journal') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    Buka Jurnal Penuh →
                </a>
            </div>

            <div class="space-y-2.5">
                @foreach($habits as $habit)
                    @php
                        $log = $todayHabitLogs->get($habit->id);
                        $isDone = $log && $log->is_completed;
                        $timeStatus = $habit->getTimeWindowStatus();
                        $canCheck = $timeStatus['is_active'];
                    @endphp
                    <div class="flex items-center justify-between p-3.5 rounded-2xl border {{ $isDone ? 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-800' }} transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-7 h-7 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $habit->order_number }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate {{ $isDone ? 'line-through text-slate-500 dark:text-slate-400' : '' }}">{{ $habit->name }}</h4>
                                <p class="text-xs text-slate-400 truncate">
                                    <span class="font-semibold {{ $timeStatus['is_active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                        {{ $timeStatus['range'] }}
                                    </span>
                                    • {{ $habit->tagline }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            @if($isDone)
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white inline-flex items-center gap-1 shadow-xs">
                                    ✓ Selesai
                                </span>
                            @else
                                @if($canCheck)
                                    <form action="{{ route('habits.log.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="habit_id" value="{{ $habit->id }}">
                                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                                        <input type="hidden" name="is_completed" value="1">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-900 dark:bg-slate-100 dark:text-slate-900 hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white text-white shadow-xs cursor-pointer">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled title="{{ $timeStatus['message'] }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed inline-flex items-center gap-1 shadow-none">
                                        🔒 Di Luar Jam
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: AI Insight Shortcut & Notifications -->
        <div class="space-y-6">

            <!-- Notifications Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi Terbaru</h3>
                    <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700">Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($notifications as $notif)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-xs">
                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ $notif->title }}</div>
                            <div class="text-slate-500 dark:text-slate-300 mt-1 leading-normal">{{ $notif->message }}</div>
                            <div class="text-[10px] text-slate-400 mt-1.5">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400 dark:text-slate-500">
                            Belum ada notifikasi baru untuk Anda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Kegiatan & Presensi Ekstrakurikuler (Hanya Aktif & Tampil Jika Siswa Terdaftar Anggota) -->
    @if(isset($studentExtracurriculars) && $studentExtracurriculars->isNotEmpty())
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-cyan-50 dark:bg-cyan-950/60 rounded-full text-xs font-bold text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 mb-1.5">
                    <span>🎯 KEANGGOTAAN EKSTRAKURIKULER</span>
                    <span>•</span>
                    <span>{{ $studentExtracurriculars->count() }} Ekstrakurikuler Diikuti</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                    Kegiatan & Presensi Ekstrakurikuler
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Jadwal latihan, status presensi kegiatan hari ini, dan akses kehadiran mandiri ekstra.
                </p>
            </div>

            <a href="{{ route('extracurricular.index') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-xs shadow-cyan-600/30 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                </svg>
                <span>Buka Presensi Ekskul</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($studentExtracurriculars as $extraItem)
                @php
                    $itemAttendance = isset($todayExtraAttendances) ? $todayExtraAttendances->get($extraItem->id) : null;
                    $timeLockStatus = $extraItem->validateTimeLock();
                @endphp
                <div class="p-5 sm:p-6 rounded-2xl border {{ $itemAttendance ? 'bg-emerald-50/40 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700/80' }} flex flex-col justify-between transition hover:shadow-md">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 dark:bg-cyan-950 text-cyan-800 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 truncate inline-block max-w-full">
                                    {{ $extraItem->category ?? 'Ekstrakurikuler' }}
                                </span>
                                <h4 class="text-base font-extrabold text-slate-900 dark:text-white mt-1.5 truncate">{{ $extraItem->name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                    Peran: <strong class="text-slate-700 dark:text-slate-200">{{ $extraItem->pivot->role ?? 'Anggota' }}</strong>
                                </p>
                            </div>
                            <span class="w-10 h-10 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-black text-xs shrink-0 border border-cyan-500/20">
                                {{ strtoupper(substr($extraItem->code ?? $extraItem->name, 0, 3)) }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 py-3 border-y border-slate-200/60 dark:border-slate-700/60 mb-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400 shrink-0">Jadwal:</span>
                                <span class="font-semibold text-right truncate">{{ $extraItem->schedule_day ?? '-' }}, {{ !empty($extraItem->start_time) ? substr($extraItem->start_time, 0, 5) . ' - ' . substr($extraItem->end_time, 0, 5) . ' WIB' : 'Waktu Fleksibel' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400 shrink-0">Lokasi:</span>
                                <span class="font-semibold text-right truncate">{{ $extraItem->location ?? 'Area Sekolah' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-400 shrink-0">Pembina:</span>
                                <span class="font-semibold text-right truncate">{{ $extraItem->teacher_names }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Presensi Hari Ini</div>
                            @if($itemAttendance)
                                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>{{ $itemAttendance->status }} ({{ substr($itemAttendance->time, 0, 5) }} WIB)</span>
                                </div>
                            @else
                                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Belum Presensi</span>
                                </div>
                            @endif
                        </div>

                        @php
                            $isItemSec = in_array($extraItem->pivot->role ?? '', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true) || (Auth::user()->hasRole('sekretaris_ekstra') && Auth::user()->isExtracurricularSecretary($extraItem->id));
                        @endphp
                        <a href="{{ route('extracurricular.index', ['extra_id' => $extraItem->id, 'tab' => $isItemSec ? 'manual' : ($itemAttendance ? 'history' : 'selfie')]) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0 {{ $itemAttendance && !$isItemSec ? 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300' : 'bg-cyan-600 hover:bg-cyan-700 text-white shadow-xs shadow-cyan-600/30' }}">
                            <span>{{ $isItemSec ? 'Absenkan Anggota' : ($itemAttendance ? 'Lihat' : 'Presensi') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Ringkasan Ibadah Bersama & Tahfidz Al-Qur'an Siswa -->
    @include('dashboards.partials.worship_tahfidz_summary')

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital Siswa -->
    @include('dashboards.partials.lms_library_summary')

    <!-- Papan Peringkat Prestasi Keseluruhan Siswa SMAN 1 Lengkong -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-950/60 rounded-full text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 mb-1.5">
                    <span>🏆 TATA TERTIB & PENGHARGAAN</span>
                    <span>•</span>
                    <span>SMAN 1 LENGKONG</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                    Papan Peringkat Siswa Berprestasi
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Akumulasi poin penghargaan prestasi akademik dan non-akademik seluruh siswa (Kategori R1 - R10).
                </p>
            </div>

            <!-- Posisi Siswa Yang Sedang Login -->
            <div class="p-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 rounded-2xl font-black text-xs shadow-md shrink-0 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-white/30 flex items-center justify-center text-sm">★</span>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-85">Posisi Prestasi Anda</div>
                    <div class="text-sm sm:text-base font-black">
                        {{ $studentRank ? 'Peringkat #' . $studentRank : 'Belum Ada Peringkat' }}
                        <span class="text-xs font-semibold">({{ $totalAchievementPoints }} Poin)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="responsive-table-container">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3 px-3">Rank</th>
                        <th class="py-3 px-3">Nama Siswa</th>
                        <th class="py-3 px-3">Kelas</th>
                        <th class="py-3 px-3 text-center">Jml Prestasi</th>
                        <th class="py-3 px-3 text-center">Total Poin</th>
                        <th class="py-3 px-3 text-right">Predikat Penghargaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topAchievers as $index => $item)
                        @php
                            $isCurrentStudent = ($item['student']->id === $student->id);
                            $rank = $index + 1;
                        @endphp
                        <tr class="transition {{ $isCurrentStudent ? 'bg-amber-50/80 dark:bg-amber-950/40 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                            <td class="py-3 px-3">
                                @if($rank === 1)
                                    <span class="w-7 h-7 rounded-xl bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-xs">🥇</span>
                                @elseif($rank === 2)
                                    <span class="w-7 h-7 rounded-xl bg-slate-300 text-slate-800 font-black text-xs flex items-center justify-center shadow-xs">🥈</span>
                                @elseif($rank === 3)
                                    <span class="w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs flex items-center justify-center shadow-xs">🥉</span>
                                @else
                                    <span class="w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-slate-900 dark:text-white {{ $isCurrentStudent ? 'text-amber-900 dark:text-amber-200' : '' }}">
                                        {{ $item['student']->name }}
                                    </span>
                                    @if($isCurrentStudent)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px]">
                                            ANDA
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400">NIS: {{ $item['student']->nis }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-300 font-semibold">
                                {{ $item['student']->schoolClass?->name ?? 'Umum' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300 text-xs">
                                    {{ $item['count'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-black text-amber-600 dark:text-amber-400 text-sm">
                                +{{ $item['points'] }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2.5 py-1 rounded-xl font-bold text-[10px] {{ $item['points'] >= 200 ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300' : ($item['points'] >= 125 ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
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
