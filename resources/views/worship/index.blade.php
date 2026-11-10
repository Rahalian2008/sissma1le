@extends('layouts.app')

@section('title', $config['title'])

@section('content')
<div class="space-y-5">
    @include('attendance.partials.nav')
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-md 
                @if($activityType === 'DHUHA') bg-gradient-to-tr from-amber-500 to-orange-400 shadow-amber-500/20
                @elseif($activityType === 'DZUHUR_JUMAT') bg-gradient-to-tr from-indigo-600 to-sky-500 shadow-indigo-500/20
                @else bg-gradient-to-tr from-rose-500 to-pink-400 shadow-rose-500/20 @endif">
                @if($activityType === 'DHUHA')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                @elseif($activityType === 'DZUHUR_JUMAT')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                @endif
            </div>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $config['title'] }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                    <span>Target: <strong class="text-slate-700 dark:text-slate-200">{{ $config['gender_label'] }}</strong></span>
                    <span>•</span>
                    <span>Jadwal: <strong class="text-slate-700 dark:text-slate-200">{{ $config['days_label'] }} [{{ $config['start_time'] }} - {{ $config['end_time'] }} WIB]</strong></span>
                    @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                        <button type="button" onclick="openWorshipTimeModal()" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 ml-1 text-[10px] bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded border border-indigo-200 dark:border-indigo-800 transition shadow-sm inline-flex items-center gap-1 shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit Waktu
                        </button>
                    @endif
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Tanggal Presensi: {{ $selectedDateFormatted }}</span>
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(!Auth::user()->isStudent())
            <a href="{{ route('worship.report', $activity) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Dokumen Laporan</span>
            </a>
            <button type="button" onclick="openPrintWorshipModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center gap-1.5 shadow-xs shadow-indigo-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Resmi</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Time-Lock Status Alert Banner -->
    <div class="p-4 sm:p-5 rounded-2xl sm:rounded-3xl border transition-all duration-200 
        @if($timeValidation['allowed']) bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-emerald-500/10 border-emerald-500/30 text-emerald-950 dark:text-emerald-200
        @else bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-rose-500/10 border-rose-500/30 text-rose-950 dark:text-rose-200 @endif">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 
                    @if($timeValidation['allowed']) bg-emerald-500 text-white shadow-md shadow-emerald-500/30
                    @else bg-rose-500 text-white shadow-md shadow-rose-500/30 @endif">
                    @if($timeValidation['allowed'])
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold tracking-wide uppercase
                            @if($timeValidation['allowed']) bg-emerald-500 text-white
                            @else bg-rose-500 text-white @endif">
                            {{ $timeValidation['allowed'] ? 'SESI TERBUKA (ACTIVE)' : 'SESI TERKUNCI (TIME-LOCK)' }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Waktu Sistem: <strong>{{ $timeValidation['current_day_date'] ?? ($timeValidation['current_day_name'] . ', ' . ($timeValidation['current_date'] ?? date('Y-m-d'))) }} • {{ $timeValidation['current_time'] }} WIB</strong>
                        </span>
                    </div>
                    <p class="text-xs mt-1 leading-relaxed">
                        @if($timeValidation['allowed'])
                            Sesi saat ini <strong>sedang berlangsung</strong> sesuai rentang waktu aktif pada hari <strong>{{ $timeValidation['current_day_name'] }}</strong>, tanggal <strong>{{ $timeValidation['current_date_formatted'] ?? ($timeValidation['current_date'] ?? date('d M Y')) }}</strong>. Siswa dapat memindai kartu RFID / QR Code atau mengambil foto selfie.
                        @else
                            <strong>Sesi Shalat Belum Dibuka / Telah Berakhir.</strong> Presensi hanya diizinkan pada hari <strong>{{ $config['days_label'] }}</strong> pukul <strong>[{{ $config['start_time'] }} - {{ $config['end_time'] }} WIB]</strong>. Hari ini: <strong>{{ $timeValidation['current_day_date'] ?? ($timeValidation['current_day_name'] . ', ' . ($timeValidation['current_date'] ?? date('d M Y'))) }}</strong>. Di luar jam tersebut sistem otomatis menolak log.
                            @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                                <a href="{{ route('master.settings') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-bold underline ml-1">Ubah Konfigurasi Waktu</a>
                            @endif
                        @endif
                    </p>
                </div>
            </div>

            @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
            <div class="flex items-center gap-2 self-end md:self-center shrink-0 bg-white/80 dark:bg-slate-900/80 p-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 dark:text-slate-300">
                    <input type="checkbox" id="bypass-time-toggle" class="w-4 h-4 rounded text-indigo-600 focus:ring-0">
                    <span>Simulasi / Bypass Waktu (Admin)</span>
                </label>
            </div>
            @endif
        </div>
    </div>

    <!-- Tampilan Khusus Wali Kelas -->
    @if(isset($isHomeroom) && $isHomeroom && isset($homeroomClass))
    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-indigo-100 dark:border-indigo-800/50 shadow-sm relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30 dark:opacity-10 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%234f46e5\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-800/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-700/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30">
                            Wali Kelas Binaan
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/30">
                            Kelas {{ $homeroomClass->name }}
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                        Presensi Siswa Kelas {{ $homeroomClass->name }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Pemantauan khusus untuk kelas binaan Anda. Anda tetap dapat melihat kelas lain melalui dropdown filter di bawah.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Statistics Cards -->
    @if(!Auth::user()->isStudent())
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Siswa</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalTarget) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">{{ $config['gender_label'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 dark:border-emerald-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalHadir) }}</div>
            <span class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium">{{ $percentHadir }}% dari target</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 dark:border-amber-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Izin / Udzur</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalIzin) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Sakit, Izin, Udzur</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-500/20 dark:border-rose-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Alpa</span>
            <div class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($totalAlpa) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Tanpa keterangan</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Belum Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ number_format($totalBelum) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Belum melakukan scan</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-indigo-500/20 dark:border-indigo-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Tingkat Kehadiran</span>
            <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $percentHadir }}%</div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-1.5">
                <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $percentHadir }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Interactive Tabs Navigation -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 dark:border-slate-800 p-2 sm:p-2.5 bg-slate-50/50 dark:bg-slate-950/40">
            @if(!$isStudent)
            <button onclick="switchTab('tab-scanner')" id="btn-tab-scanner" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'scanner' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Terminal Gerbang Ibadah</span>
            </button>
            @endif
            <button onclick="switchTab('tab-selfie')" id="btn-tab-selfie" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'selfie' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Presensi Kamera Selfie</span>
            </button>
            @if(Auth::user()->canRecordAttendanceManual())
            <button onclick="switchTab('tab-manual')" id="btn-tab-manual" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'manual' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Absen Manual</span>
            </button>
            @endif
            <button onclick="switchTab('tab-history')" id="btn-tab-history" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'history' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Presensi ({{ $selectedDate === $today ? 'Hari Ini' : \Carbon\Carbon::parse($selectedDate)->locale('id')->translatedFormat('d M Y') }}) ({{ $attendances->count() }})</span>
            </button>
        </div>

        @if(!$isStudent)
        <!-- TAB 1: SCANNER GERBANG IBADAH (RFID, BARCODE & FACE RECOGNITION) -->
        <div id="tab-scanner" class="{{ $activeTab === 'scanner' ? '' : 'hidden' }} p-4 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Scanner Input Card -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                        <div class="relative z-10 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    Terminal Ibadah Siap Menerima Input
                                </span>
                                <!-- Status 5 Metode Presensi -->
                                <div class="flex items-center gap-1 flex-wrap text-[10px]">
                                    <span class="px-1.5 py-0.5 rounded {{ $enableSelfie ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-400' }}">Selfie: {{ $enableSelfie ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableBarcode ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400' }}">Barcode: {{ $enableBarcode ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableRfid ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400' }}">RFID: {{ $enableRfid ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableFaceRecognition ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-700 text-slate-400' }}">Face AI: {{ $enableFaceRecognition ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableManual ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-400' }}">Manual: {{ $enableManual ? 'Aktif' : 'Off' }}</span>
                                </div>
                            </div>

                            <!-- Pilihan Gerbang Metode Terminal Ibadah -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Pilih Gerbang Sensor / Mode Pemindaian Terminal:
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" onclick="setWorshipGate('barcode')" id="btn-gate-barcode" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableBarcode ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">1. Barcode</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableBarcode ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Scan Barcode Siswa</span>
                                    </button>
                                    <button type="button" onclick="setWorshipGate('rfid')" id="btn-gate-rfid" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableRfid ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">2. Kartu RFID</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableRfid ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Tap Kartu Siswa</span>
                                    </button>
                                    <button type="button" onclick="setWorshipGate('face')" id="btn-gate-face" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableFaceRecognition ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">3. Face AI</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableFaceRecognition ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Deteksi Wajah AI</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Shared Device Hardware Selector -->
                            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 space-y-1.5">
                                <label for="device-select" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Perangkat Gerbang / Terminal Presensi Terpadu:
                                </label>
                                <select id="device-select" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white outline-none focus:border-indigo-400">
                                    @forelse($devices as $dev)
                                        <option value="{{ $dev->device_code }}">
                                            {{ $dev->device_name }} ({{ $dev->device_code }}) - [Tipe: {{ $dev->device_type }} | Lokasi: {{ $dev->location }}]
                                        </option>
                                    @empty
                                        <option value="GATE-01">Gerbang Utama RFID/QR (Default Terpadu GATE-01)</option>
                                    @endforelse
                                </select>
                                <p class="text-[10px] text-slate-400">Konfigurasi perangkat dan sensor scan terhubung langsung dengan sistem Presensi Harian.</p>
                            </div>

                            <form id="scanner-form" onsubmit="handleScannerSubmit(event)" class="space-y-3">
                                <label for="scan-input" id="scan-input-label" class="block text-xs text-slate-300 font-medium">Scan Barcode Kartu Pelajar atau Masukkan NISN:</label>
                                <div class="relative">
                                    <input type="text" id="scan-input" autofocus autocomplete="off" placeholder="Dekatkan kartu RFID atau scan barcode NISN..."
                                        class="w-full px-4 py-3.5 pl-12 bg-slate-900/90 border-2 border-indigo-500/70 focus:border-indigo-400 rounded-2xl text-base font-mono text-white placeholder-slate-500 outline-none shadow-inner">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400">
                                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Tekan <strong>ENTER</strong> jika menggunakan barcode scanner hardware</span>
                                    <button type="submit" id="btn-submit-scanner" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition cursor-pointer">Kirim Log Scan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Scan Feedback Box -->
                    <div id="scan-feedback-box" class="hidden p-5 rounded-2xl border transition-all duration-300"></div>
                </div>

                <!-- Live Scanned Stream -->
                <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-950/60 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Aktivitas Scan Realtime
                        </h3>
                        <span class="text-[11px] text-slate-400" id="live-scan-count">{{ $recentLogs->count() }} Terdata</span>
                    </div>

                    <div id="live-scan-list" class="space-y-2 max-h-96 overflow-y-auto pr-1 divide-y divide-slate-200/60 dark:divide-slate-800/60">
                        @forelse($recentLogs as $log)
                            <div class="pt-2 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-[10px] shrink-0">
                                        {{ strtoupper(substr($log->student?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $log->student?->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->student?->schoolClass?->name ?? '-' }} • Method: {{ $log->method }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[10px] text-slate-400 font-semibold">{{ \Carbon\Carbon::parse($log->date)->locale('id')->translatedFormat('d M Y') }}</div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                        {{ substr($log->time, 0, 5) }} WIB
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-xs text-slate-400">
                                Belum ada presensi yang tercatat untuk sesi hari ini ({{ $todayFormatted }}).
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 2: KAMERA SELFIE & GPS -->
        <div id="tab-selfie" class="{{ $activeTab === 'selfie' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="max-w-xl mx-auto space-y-4">
                <div class="text-center">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi Selfie Kamera Langsung</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pastikan wajah terlihat jelas di area kamera dan izinkan akses lokasi GPS.</p>
                </div>

                <!-- Geofencing & Method Synchronization Info -->
                <div class="bg-indigo-50/70 dark:bg-indigo-950/40 p-3.5 rounded-2xl border border-indigo-200/70 dark:border-indigo-800/70 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Radius & GPS Geofence Terpadu
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $enableSelfie ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                            Selfie: {{ $enableSelfie ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                        Sinkron dengan Presensi Harian: Radius <strong>{{ $geofenceRadius }} meter</strong> dari titik koordinat sekolah ({{ $schoolLat }}, {{ $schoolLng }}).
                    </p>
                </div>

                <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden bg-slate-950 aspect-4/3 border-2 border-indigo-500/40 shadow-xl flex items-center justify-center">
                    <video id="selfie-video" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="selfie-canvas" class="hidden"></canvas>
                    <div id="camera-loading" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center text-white gap-2">
                        <svg class="w-8 h-8 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span class="text-xs font-semibold">Menginisialisasi Kamera...</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="button" onclick="startCamera()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-300 dark:hover:bg-slate-700 transition">
                        Nyalakan Kamera
                    </button>
                    <button type="button" onclick="captureAndSubmitSelfie()" id="btn-take-selfie" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-emerald-500 hover:from-indigo-500 hover:to-emerald-400 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ambil Foto & Simpan Presensi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: ABSEN MANUAL PER KELAS -->
        @if(Auth::user()->canRecordAttendanceManual())
        <div id="tab-manual" class="{{ $activeTab === 'manual' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi Manual Kelas</h3>
                    <p class="text-xs text-slate-500">Tentukan status kehadiran ibadah siswa rombel secara massal.</p>
                </div>

                <form method="GET" action="{{ route('worship.index', $activity) }}" class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-[11px] font-bold text-slate-500">Tanggal:</span>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                    </div>

                    @if($classes->count() <= 1)
                        <div class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-900 rounded-xl text-xs font-extrabold text-indigo-700 dark:text-indigo-300 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Kelas: {{ $classes->first()?->name ?? 'Kelas Anda' }}</span>
                        </div>
                        @if($classes->first())
                            <input type="hidden" name="class_id" value="{{ $classes->first()->id }}">
                        @endif
                    @else
                        <select name="class_id" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </form>
            </div>

            @if($targetStudents->isNotEmpty())
            <form id="manual-attendance-form" action="{{ route('worship.manual', $activity) }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClassId ?: $targetStudents->first()->class_id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="flex items-center justify-between py-2 border-b border-slate-200/80 dark:border-slate-800 flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-slate-500">Menampilkan <strong>{{ $targetStudents->count() }}</strong> {{ $config['gender_label'] }}</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                            📅 {{ $selectedDateFormatted }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="relative inline-block text-left" id="worship-bulk-dropdown-container">
                            <button type="button" onclick="toggleWorshipBulkDropdown()" class="text-xs px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                                <span>⚡ Tandai Semua...</span>
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="worship-bulk-dropdown-menu" class="hidden absolute right-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl z-30 py-1 text-xs">
                                <button type="button" onclick="setAllManualStatus('HADIR'); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-2">
                                    <span>✓</span><span>Semua HADIR</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('IZIN'); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-sky-50 dark:hover:bg-sky-950/50 text-sky-700 dark:text-sky-300 font-bold flex items-center gap-2">
                                    <span>📝</span><span>Semua IZIN</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('SAKIT'); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-purple-50 dark:hover:bg-purple-950/50 text-purple-700 dark:text-purple-300 font-bold flex items-center gap-2">
                                    <span>🏥</span><span>Semua SAKIT</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('UDZUR'); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-purple-50 dark:hover:bg-purple-950/50 text-purple-700 dark:text-purple-300 font-bold flex items-center gap-2">
                                    <span>💜</span><span>Semua UDZUR</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('ALPA'); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-700 dark:text-rose-300 font-bold flex items-center gap-2">
                                    <span>❌</span><span>Semua ALPA</span>
                                </button>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <button type="button" onclick="resetAllManualStatus(); closeWorshipBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-bold flex items-center gap-2">
                                    <span>🗑️</span><span>Kosongkan Pilihan</span>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="setAllManualStatus('HADIR')" title="Tandai Semua Hadir" class="text-xs px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 transition">
                            ✓ Tandai Semua Hadir
                        </button>
                        <button type="button" onclick="resetAllManualStatus()" class="text-xs px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold transition">
                            Kosongkan
                        </button>
                        <button type="submit" class="text-xs px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi</span>
                        </button>
                    </div>
                </div>

                <!-- Bilah Aksi Massal Pilihan Siswa Ibadah (Multi-Select Checkboxes Toolbar) -->
                <div class="p-3 bg-gradient-to-r from-indigo-50/90 via-slate-50 to-indigo-50/90 dark:from-indigo-950/40 dark:via-slate-900/40 dark:to-indigo-950/40 rounded-2xl border border-indigo-200/70 dark:border-indigo-800/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer font-extrabold text-indigo-950 dark:text-indigo-200 select-none">
                            <input type="checkbox" id="worship-check-all-students" onchange="toggleSelectAllWorshipStudents(this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700">
                            <span>Pilih Semua Siswa</span>
                        </label>
                        <span id="worship-selected-students-count" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300 transition">
                            0 Terpilih
                        </span>
                        <button type="button" onclick="selectUnmarkedWorshipStudents()" class="text-[10.5px] px-2 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            ⚡ Pilih Belum Absen
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mr-0.5">Terapkan ke Siswa Terpilih:</span>
                        <button type="button" onclick="setWorshipStatusesForSelected('HADIR')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>✓</span><span>HADIR</span>
                        </button>
                        <button type="button" onclick="setWorshipStatusesForSelected('IZIN')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>📝</span><span>IZIN</span>
                        </button>
                        <button type="button" onclick="setWorshipStatusesForSelected('SAKIT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>🏥</span><span>SAKIT</span>
                        </button>
                        <button type="button" onclick="setWorshipStatusesForSelected('UDZUR')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>💜</span><span>UDZUR</span>
                        </button>
                        <button type="button" onclick="setWorshipStatusesForSelected('ALPA')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>❌</span><span>ALPA</span>
                        </button>
                        <button type="button" onclick="clearWorshipStatusesForSelected()" class="text-[11px] font-bold px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition cursor-pointer" title="Batalkan pilihan siswa tercentang">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($targetStudents as $idx => $st)
                        @php
                            $att = $attendanceByStudentId->get($st->id);
                            // Jangan langsung aktif hadir jika siswa belum tercatat presensinya
                            $currentStatus = $att ? $att->status : null;
                        @endphp
                        <div class="worship-student-row py-2.5 px-2 rounded-xl transition flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" class="worship-student-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700" data-student-id="{{ $st->id }}" data-has-status="{{ $att ? '1' : '0' }}" onchange="updateWorshipSelectedStudentsCounter()">
                                    <span class="text-xs text-slate-400 font-mono w-6 text-right">{{ $idx + 1 }}.</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $st->name }}</div>
                                        @if($att)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black
                                                @if($att->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                @elseif($att->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                                @elseif(in_array($att->status, ['IZIN', 'SAKIT', 'UDZUR'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                                Tercatat: {{ $att->status }}
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                Belum Terdata
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $st->nisn }} • Kelas: {{ $st->schoolClass?->name ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach(['HADIR' => 'bg-emerald-500', 'IZIN' => 'bg-sky-500', 'SAKIT' => 'bg-yellow-500', 'UDZUR' => 'bg-purple-500', 'ALPA' => 'bg-rose-500'] as $stKey => $stBg)
                                    <label class="text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer flex items-center gap-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <input type="radio" name="statuses[{{ $st->id }}]" value="{{ $stKey }}" {{ $currentStatus === $stKey ? 'checked' : '' }} class="manual-status-radio text-indigo-600 focus:ring-0">
                                        <span>{{ $stKey }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                        Simpan Presensi Manual
                    </button>
                </div>
            </form>
            @else
            <div class="py-12 text-center text-xs text-slate-400">
                Pilih kelas terlebih dahulu untuk mengisi presensi manual.
            </div>
            @endif
        </div>
        @endif

        <!-- TAB 4: REKAP RIWAYAT PRESENSI -->
        <div id="tab-history" class="hidden p-4 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Presensi Terdata</h3>
                    <p class="text-xs text-slate-500">Hari & Tanggal: <strong class="text-slate-700 dark:text-slate-300">{{ $selectedDateFormatted }}</strong> ({{ $attendances->count() }} Catatan)</p>
                </div>

                <form method="GET" action="{{ route('worship.index', $activity) }}" class="flex items-center gap-2">
                    @if($selectedClassId)
                        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    @endif
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-500">Filter Tanggal:</span>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                    </div>
                    @if($selectedDate !== $today)
                        <a href="{{ route('worship.index', array_filter(['activity' => $activity, 'class_id' => $selectedClassId])) }}" class="text-xs px-2.5 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-300 transition">
                            Hari Ini
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase text-[10px]">
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3">Hari & Tanggal</th>
                            <th class="py-2.5 px-3">Jam Masuk</th>
                            <th class="py-2.5 px-3">Metode</th>
                            <th class="py-2.5 px-3">Status</th>
                            @if(Auth::user()->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'sekretaris_kelas', 'koordinator_keagamaan']))
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($attendances as $idx => $att)
                            <tr>
                                <td class="py-2.5 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $att->student?->name }}</td>
                                <td class="py-2.5 px-3 text-slate-500">{{ $att->student?->schoolClass?->name ?? '-' }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('l, d M Y') }}
                                </td>
                                <td class="py-2.5 px-3 font-mono font-bold">{{ substr($att->time, 0, 5) }} WIB</td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        @if($att->method === 'SELFIE') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif(in_array($att->method, ['BARCODE', 'QR_CODE'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                        @elseif($att->method === 'RFID') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                        @elseif($att->method === 'FACE_RECOGNITION') bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300
                                        @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 @endif">
                                        {{ $att->method }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold
                                        @if($att->status === 'HADIR') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30
                                        @elseif($att->status === 'TERLAMBAT') bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30
                                        @elseif(in_array($att->status, ['IZIN', 'SAKIT', 'UDZUR'])) bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30
                                        @else bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30 @endif">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                @if(Auth::user()->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'sekretaris_kelas', 'koordinator_keagamaan']))
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="openEditModal('{{ $att->id }}', '{{ $att->student?->name }}', '{{ $att->status }}', '{{ $att->time ? \Carbon\Carbon::parse($att->time)->format('H:i') : '' }}', '{{ addslashes($att->notes) }}')" class="px-2 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-[11px] transition" title="Edit Data">
                                            Edit
                                        </button>
                                        @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                                        <form action="{{ route('worship.destroy', ['activity' => $activity, 'id' => $att->id]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data presensi ibadah ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-[11px] transition" title="Hapus Data">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">Belum ada siswa yang tercatat presensi pada {{ $selectedDateFormatted }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Time Settings Modal -->
@if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
<div id="worship-time-modal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 transform scale-95 opacity-0 transition-all duration-300" id="worship-time-modal-content">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pengaturan Waktu Pelaksanaan
                </h3>
                <button type="button" onclick="closeWorshipTimeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 p-1.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('master.settings.update') }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs rounded-xl border border-indigo-200 dark:border-indigo-800/50 flex items-start gap-2">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Atur rentang waktu dimana sistem menerima presensi untuk kegiatan <strong>{{ $config['title'] }}</strong>. Di luar jam ini, presensi akan ditolak otomatis.</span>
                    </div>

                    @php
                        $startKey = 'worship_' . strtolower(str_replace('-', '_', $activity)) . '_start';
                        $endKey = 'worship_' . strtolower(str_replace('-', '_', $activity)) . '_end';
                        $daysKey = 'worship_' . strtolower(str_replace('-', '_', $activity)) . '_days';
                        $allWeekDays = [
                            'senin' => 'Senin',
                            'selasa' => 'Selasa',
                            'rabu' => 'Rabu',
                            'kamis' => 'Kamis',
                            'jumat' => "Jum'at",
                            'sabtu' => 'Sabtu',
                            'minggu' => 'Minggu',
                        ];
                        $currentSelectedDays = $config['day_keys'] ?? [];
                    @endphp

                    <!-- Hidden input to detect empty selection -->
                    <input type="hidden" name="_present_{{ $daysKey }}" value="1">

                    <!-- Pilihan Hari Aktif -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Pilih Hari Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-800">
                                {{ $config['days_label'] }}
                            </span>
                        </div>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-1.5">
                            @foreach($allWeekDays as $dk => $dn)
                                @php $isDkChecked = in_array($dk, $currentSelectedDays); @endphp
                                <label class="cursor-pointer select-none">
                                    <input type="checkbox" name="{{ $daysKey }}[]" value="{{ $dk }}"
                                        {{ $isDkChecked ? 'checked' : '' }}
                                        onchange="this.nextElementSibling.classList.toggle('bg-indigo-600', this.checked); this.nextElementSibling.classList.toggle('text-white', this.checked); this.nextElementSibling.classList.toggle('border-indigo-600', this.checked); this.nextElementSibling.classList.toggle('bg-slate-50', !this.checked); this.nextElementSibling.classList.toggle('dark:bg-slate-800', !this.checked); this.nextElementSibling.classList.toggle('text-slate-700', !this.checked); this.nextElementSibling.classList.toggle('dark:text-slate-200', !this.checked);"
                                        class="sr-only">
                                    <div class="px-2 py-1.5 rounded-lg text-xs font-bold border transition-all text-center {{ $isDkChecked ? 'bg-indigo-600 text-white border-indigo-600 shadow-2xs' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                                        {{ $dn }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Waktu Mulai <span class="text-rose-500">*</span></label>
                            <input type="time" name="{{ $startKey }}" value="{{ $config['start_time'] }}" required class="w-full text-sm px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Waktu Berakhir <span class="text-rose-500">*</span></label>
                            <input type="time" name="{{ $endKey }}" value="{{ $config['end_time'] }}" required class="w-full text-sm px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-mono">
                        </div>
                    </div>
                </div>
                
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 rounded-b-2xl flex justify-end gap-2">
                    <button type="button" onclick="closeWorshipTimeModal()" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-500/30 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

    <!-- Edit Modal (Admin, Wali Kelas, Sekretaris Kelas) -->
    @if(Auth::user()->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan', 'wali_kelas', 'sekretaris_kelas']))
    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Edit Presensi Ibadah</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="edit-modal-student-name">Nama Siswa</p>

            <form id="edit-form" method="POST" action="" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran:</label>
                    <select name="status" id="edit-modal-status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <option value="HADIR">HADIR</option>
                        <option value="IZIN">IZIN</option>
                        <option value="SAKIT">SAKIT</option>
                        <option value="UDZUR">UDZUR</option>
                        <option value="ALPA">ALPA</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Waktu Presensi:</label>
                    <input type="time" name="time" id="edit-modal-time" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan:</label>
                    <textarea name="notes" id="edit-modal-notes" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

<script>
    // Tab Switching
    function switchTab(tabId) {
        ['tab-scanner', 'tab-selfie', 'tab-manual', 'tab-history'].forEach(id => {
            const el = document.getElementById(id);
            const btn = document.getElementById('btn-' + id);
            if (el) el.classList.add('hidden');
            if (btn) {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-600', 'dark:text-slate-400');
            }
        });
        const target = document.getElementById(tabId);
        const targetBtn = document.getElementById('btn-' + tabId);
        if (target) target.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            targetBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
        }
        if (tabId === 'tab-scanner') {
            document.getElementById('scan-input')?.focus();
        }
    }

    // Set All Manual Status
    function setAllManualStatus(status) {
        document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
    }

    // Gate Controller for Worship Terminal
    let currentWorshipGate = '{{ $enableBarcode ? 'barcode' : ($enableRfid ? 'rfid' : ($enableFaceRecognition ? 'face' : 'barcode')) }}';

    function setWorshipGate(gate) {
        currentWorshipGate = gate;
        const inputLabel = document.getElementById('scan-input-label');
        const input = document.getElementById('scan-input');
        const submitBtn = document.getElementById('btn-submit-scanner');

        document.querySelectorAll('.gate-btn').forEach(b => {
            b.classList.remove('border-indigo-500', 'bg-indigo-950/60', 'ring-2', 'ring-indigo-500/50');
        });

        const activeBtn = document.getElementById('btn-gate-' + gate);
        if (activeBtn) {
            activeBtn.classList.add('border-indigo-500', 'bg-indigo-950/60', 'ring-2', 'ring-indigo-500/50');
        }

        if (gate === 'barcode') {
            if (inputLabel) inputLabel.innerText = "Scan Barcode Kartu Pelajar atau Masukkan NISN Siswa:";
            if (input) {
                input.placeholder = "Arahkan barcode scanner ke kartu siswa atau ketik NISN...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log Barcode";
        } else if (gate === 'rfid') {
            if (inputLabel) inputLabel.innerText = "Tempelkan Kartu RFID Siswa ke Reader Sensor / UID:";
            if (input) {
                input.placeholder = "Dekatkan kartu RFID siswa ke reader...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log RFID";
        } else if (gate === 'face') {
            if (inputLabel) inputLabel.innerText = "Terminal AI Face Recognition (Masukkan UID Wajah / NISN):";
            if (input) {
                input.placeholder = "ID Wajah / NISN hasil verifikasi terminal AI...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log Face Recognition";
        }
    }

    // Scanner AJAX Handler
    async function handleScannerSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('scan-input');
        const feedback = document.getElementById('scan-feedback-box');
        const val = input.value.trim();
        if (!val) return;

        const bypassToggle = document.getElementById('bypass-time-toggle');
        const bypass = bypassToggle ? bypassToggle.checked : false;
        const deviceSelect = document.getElementById('device-select');
        const deviceCode = deviceSelect ? deviceSelect.value : 'GATE-01';
        const scanMethod = currentWorshipGate === 'rfid' ? 'RFID' : (currentWorshipGate === 'barcode' ? 'BARCODE' : 'FACE_RECOGNITION');

        try {
            const res = await fetch("{{ route('worship.scan', $activity) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    identifier: val,
                    method: scanMethod,
                    device_code: deviceCode,
                    bypass_time_lock: bypass
                })
            });

            const data = await res.json();
            feedback.classList.remove('hidden');

            if (res.ok && data.success) {
                feedback.className = 'p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100 flex items-center gap-3 animate-bounce-once';
                feedback.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shrink-0">✓</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-emerald-700 dark:text-emerald-300">${data.message}</div>
                        <div class="text-xs mt-0.5">Siswa: <strong>${data.student.name}</strong> (${data.student.class_name}) • Pukul: <strong>${data.formatted_time}</strong></div>
                    </div>
                `;

                // Prepend to live stream
                const streamList = document.getElementById('live-scan-list');
                const item = document.createElement('div');
                item.className = 'pt-2 flex items-center justify-between gap-3 text-xs bg-emerald-500/10 p-2 rounded-xl border border-emerald-500/20';
                item.innerHTML = `
                    <div class="truncate">
                        <div class="font-bold text-emerald-800 dark:text-emerald-200 truncate">${data.student.name}</div>
                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400">${data.student.class_name} • Baru Saja</div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500 text-white">${data.formatted_time}</span>
                `;
                streamList.prepend(item);
            } else {
                feedback.className = 'p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-100 flex items-center gap-3';
                feedback.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-sm shrink-0">✕</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-rose-700 dark:text-rose-300">Presensi Ditolak!</div>
                        <div class="text-xs mt-0.5">${data.message}</div>
                    </div>
                `;
            }
        } catch (err) {
            feedback.classList.remove('hidden');
            feedback.className = 'p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-100 text-xs';
            feedback.innerText = 'Gagal memproses presensi: Terjadi gangguan jaringan.';
        } finally {
            input.value = '';
            input.focus();
        }
    }

    // Camera Stream Management
    let cameraStream = null;
    async function startCamera() {
        const video = document.getElementById('selfie-video');
        const loader = document.getElementById('camera-loading');
        try {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = cameraStream;
            if (loader) loader.classList.add('hidden');
        } catch (err) {
            alert('Tidak dapat mengakses kamera perangkat. Pastikan izin kamera telah diberikan.');
            if (loader) loader.classList.add('hidden');
        }
    }

    async function captureAndSubmitSelfie() {
        const video = document.getElementById('selfie-video');
        const canvas = document.getElementById('selfie-canvas');
        if (!video.videoWidth) {
            alert('Silakan nyalakan kamera terlebih dahulu.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);

        // Get GPS
        let lat = null, lng = null;
        if (navigator.geolocation) {
            try {
                const pos = await new Promise((res, rej) => navigator.geolocation.getCurrentPosition(res, rej, { timeout: 5000 }));
                lat = pos.coords.latitude;
                lng = pos.coords.longitude;
            } catch (e) {
                console.warn('GPS timed out or blocked');
            }
        }

        const deviceSelect = document.getElementById('device-select');
        const deviceCode = deviceSelect ? deviceSelect.value : 'SELFIE-01';

        try {
            const res = await fetch("{{ route('worship.selfie', $activity) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    selfie_image: dataUrl,
                    latitude: lat,
                    longitude: lng,
                    device_code: deviceCode
                })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menyimpan presensi selfie.');
            }
        } catch (e) {
            alert('Terjadi kesalahan saat mengunggah foto selfie.');
        }
    }

    // Worship Bulk Dropdown Controller
    function toggleWorshipBulkDropdown() {
        const menu = document.getElementById('worship-bulk-dropdown-menu');
        if (menu) menu.classList.toggle('hidden');
    }

    function closeWorshipBulkDropdown() {
        const menu = document.getElementById('worship-bulk-dropdown-menu');
        if (menu) menu.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('worship-bulk-dropdown-container');
        if (container && !container.contains(e.target)) {
            closeWorshipBulkDropdown();
        }
    });

    // Worship Multi-Select Students Checkboxes & Bulk Batch Actions
    function toggleSelectAllWorshipStudents(checked) {
        document.querySelectorAll('.worship-student-checkbox').forEach(cb => cb.checked = checked);
        updateWorshipSelectedStudentsCounter();
    }

    function selectUnmarkedWorshipStudents() {
        document.querySelectorAll('.worship-student-checkbox').forEach(cb => {
            cb.checked = (cb.getAttribute('data-has-status') === '0');
        });
        updateWorshipSelectedStudentsCounter();
    }

    function updateWorshipSelectedStudentsCounter() {
        const allBoxes = document.querySelectorAll('.worship-student-checkbox');
        const checkedBoxes = document.querySelectorAll('.worship-student-checkbox:checked');
        const counterEl = document.getElementById('worship-selected-students-count');
        if (counterEl) {
            counterEl.textContent = `${checkedBoxes.length} Terpilih`;
            if (checkedBoxes.length > 0) {
                counterEl.classList.remove('bg-indigo-100', 'text-indigo-800', 'dark:bg-indigo-900/60', 'dark:text-indigo-300');
                counterEl.classList.add('bg-indigo-600', 'text-white');
            } else {
                counterEl.classList.remove('bg-indigo-600', 'text-white');
                counterEl.classList.add('bg-indigo-100', 'text-indigo-800', 'dark:bg-indigo-900/60', 'dark:text-indigo-300');
            }
        }
        const checkAll = document.getElementById('worship-check-all-students');
        if (checkAll) {
            checkAll.checked = (allBoxes.length > 0 && checkedBoxes.length === allBoxes.length);
            checkAll.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        }
    }

    function setWorshipStatusesForSelected(status) {
        const checkedBoxes = document.querySelectorAll('.worship-student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu menggunakan kotak pilihan (checkbox).');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            const radio = document.querySelector(`#manual-attendance-form input[name="statuses[${studentId}]"][value="${status}"]`);
            if (radio) {
                radio.checked = true;
                radio.dataset.wasChecked = 'true';
                const row = cb.closest('.worship-student-row');
                if (row) {
                    row.classList.add('bg-indigo-50/80', 'dark:bg-indigo-950/40');
                    setTimeout(() => row.classList.remove('bg-indigo-50/80', 'dark:bg-indigo-950/40'), 800);
                }
            }
        });
    }

    function clearWorshipStatusesForSelected() {
        const checkedBoxes = document.querySelectorAll('.worship-student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu.');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            document.querySelectorAll(`#manual-attendance-form input[name="statuses[${studentId}]"]`).forEach(r => {
                r.checked = false;
                r.dataset.wasChecked = 'false';
            });
        });
    }

    // Manual Attendance Bulk Actions & Deselect
    function setAllManualStatus(status) {
        document.querySelectorAll('#manual-attendance-form input.manual-status-radio').forEach(radio => {
            if (radio.value === status) {
                radio.checked = true;
                radio.dataset.wasChecked = 'true';
            } else {
                radio.dataset.wasChecked = 'false';
            }
        });
    }

    function resetAllManualStatus() {
        document.querySelectorAll('#manual-attendance-form input.manual-status-radio').forEach(radio => {
            radio.checked = false;
            radio.dataset.wasChecked = 'false';
        });
        document.querySelectorAll('.worship-student-checkbox').forEach(cb => cb.checked = false);
        updateWorshipSelectedStudentsCounter();
    }

    // Allow clicking an already-checked radio to deselect/uncheck
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#manual-attendance-form input.manual-status-radio').forEach(radio => {
            radio.dataset.wasChecked = radio.checked ? 'true' : 'false';
            radio.addEventListener('click', function() {
                if (this.dataset.wasChecked === 'true') {
                    this.checked = false;
                    this.dataset.wasChecked = 'false';
                } else {
                    document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => r.dataset.wasChecked = 'false');
                    this.dataset.wasChecked = 'true';
                }
            });
        });
    });
    // Edit Modal function
    function openEditModal(id, studentName, status, time, notes) {
        document.getElementById('edit-modal-student-name').innerText = studentName;
        document.getElementById('edit-modal-status').value = status;
        document.getElementById('edit-modal-time').value = time;
        document.getElementById('edit-modal-notes').value = notes;
        
        // Use a generic URL structure for resource update, replace dynamically
        const form = document.getElementById('edit-form');
        form.action = `{{ url('/worship') }}/{{ $activity }}/${id}`;
        
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    // Edit Worship Time Modal
    function openWorshipTimeModal() {
        const modal = document.getElementById('worship-time-modal');
        const content = document.getElementById('worship-time-modal-content');
        if(!modal) return;
        
        modal.classList.remove('hidden');
        // trigger animation
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeWorshipTimeModal() {
        const modal = document.getElementById('worship-time-modal');
        const content = document.getElementById('worship-time-modal-content');
        if(!modal) return;
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

@include('worship.partials.print_modal')
@endsection
