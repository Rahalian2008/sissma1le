@extends('layouts.app')

@section('title', 'Presensi Ekstrakurikuler - SISSMA1LE')

@section('content')
<div class="space-y-5">
    @include('attendance.partials.nav')
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-md bg-gradient-to-tr from-cyan-600 to-blue-500 shadow-cyan-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Presensi Ekstrakurikuler {{ $extra ? ': ' . $extra->name : '' }}
                    </h1>
                    @if($extra)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-500/30">
                            {{ $extra->category }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                    <span>Pembina: <strong class="text-slate-700 dark:text-slate-200">{{ $extra?->teacher_names ?? 'Belum Ditentukan' }}</strong></span>
                    <span>•</span>
                    <span>Jam Operasional: <strong class="text-slate-700 dark:text-slate-200">[{{ substr(\App\Models\SchoolSetting::get('extra_attendance_start', '14:00'), 0, 5) }} - {{ substr(\App\Models\SchoolSetting::get('extra_attendance_end', '17:30'), 0, 5) }} WIB]</strong></span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-cyan-50 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300 font-bold border border-cyan-200 dark:border-cyan-800">
                        <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $selectedDateFormatted }}</span>
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Ekstrakurikuler Switcher Dropdown -->
            <form method="GET" action="{{ route('extracurricular.index') }}" class="flex items-center gap-2">
                @if($selectedDate)
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                @endif
                <select name="extra_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 outline-none cursor-pointer">
                    @foreach($availableExtras as $ex)
                        <option value="{{ $ex->id }}" {{ $selectedExtraId == $ex->id ? 'selected' : '' }}>
                            {{ $ex->name }} ({{ $ex->category }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if(!Auth::user()->isStudent() || ($canManageSchedule ?? false))
            @if($canManageSchedule ?? false)
            <button type="button" onclick="openScheduleModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-white transition flex items-center gap-1.5 shadow-xs shadow-amber-500/30 cursor-pointer" title="Atur Jadwal & Waktu Latihan Ekstrakurikuler">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Atur Jadwal Latihan</span>
            </button>
            @endif
            @if(Auth::user()->canManageExtracurricular() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'pembina_ekstra']) || in_array(Auth::user()->role, ['admin', 'super_admin'], true))
            <button type="button" onclick="switchTab('tab-members')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-500 text-white transition flex items-center gap-1.5 shadow-xs shadow-cyan-600/30 cursor-pointer" title="Kelola Anggota Ekstrakurikuler">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Kelola Anggota</span>
            </button>
            @endif
            <a href="{{ route('extracurricular.report', ['extra_id' => $selectedExtraId]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Dokumen Laporan</span>
            </a>
            <button type="button" onclick="openPrintExtracurricularModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition flex items-center gap-1.5 shadow-xs cursor-pointer" title="Cetak Rekap Presensi Resmi Ekstrakurikuler">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekap</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Time-Lock Status Alert Banner -->
    <div class="p-4 sm:p-5 rounded-2xl sm:rounded-3xl border transition-all duration-200 
        @if($timeValidation['allowed']) bg-gradient-to-r from-emerald-500/10 via-cyan-500/10 to-emerald-500/10 border-emerald-500/30 text-emerald-950 dark:text-emerald-200
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
                            {{ $timeValidation['allowed'] ? 'SESI LATIHAN TERBUKA' : 'SESI TERKUNCI (TIME-LOCK)' }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Waktu Sistem: <strong>{{ $timeValidation['current_day_name'] ?? date('l') }} • {{ $timeValidation['current_time'] ?? date('H:i') }} WIB</strong>
                        </span>
                    </div>
                    <p class="text-xs mt-1 leading-relaxed">
                        @if($timeValidation['allowed'])
                            Sesi kegiatan <strong>{{ $extra?->name }}</strong> saat ini <strong>sedang berlangsung aktif</strong> sesuai {{ $extra?->hasCustomSchedule() ? 'Jadwal Khusus' : 'Jadwal Standar Ekstrakurikuler' }} [{{ $extra?->effective_days_label ?? 'Fleksibel' }} • {{ $extra?->effective_start_time ?? '14:00' }} - {{ $extra?->effective_end_time ?? '17:30' }} WIB]. Siswa/anggota dapat memindai kartu RFID / barcode NISN atau mengambil foto selfie WebRTC.
                        @else
                            <strong>Sesi Kegiatan Belum Dimulai / Di Luar Jadwal.</strong> Jadwal resmi: <strong>{{ $extra?->effective_days_label ?? 'Fleksibel' }}</strong> pukul <strong>[{{ $extra?->effective_start_time ?? '14:00' }} - {{ $extra?->effective_end_time ?? '17:30' }} WIB]</strong> ({{ $extra?->hasCustomSchedule() ? 'Jadwal Khusus' : 'Jadwal Standar Umum' }}). Di luar jam tersebut presensi mandiri terkunci.
                            @if(Auth::user()->canManageExtracurricular())
                                <span class="font-bold text-cyan-700 dark:text-cyan-400">Gunakan centang Bypass Waktu di samping jika mengadakan latihan tambahan.</span>
                            @endif
                        @endif
                    </p>
                </div>
            </div>

            @if(Auth::user()->canManageExtracurricular() || Auth::user()->hasAnyRole(['admin', 'super_admin']))
            <div class="flex items-center gap-2 self-end md:self-center shrink-0 bg-white/80 dark:bg-slate-900/80 p-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 dark:text-slate-300">
                    <input type="checkbox" id="bypass-time-toggle" class="w-4 h-4 rounded text-cyan-600 focus:ring-0">
                    <span>Bypass Jadwal Latihan (Pembina / Admin)</span>
                </label>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Statistics Cards -->
    @if(!Auth::user()->isStudent() || $isExtraSecretary)
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Anggota</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalTarget) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">{{ $extra?->name ?? 'Ekstrakurikuler' }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 dark:border-emerald-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalHadir) }}</div>
            <span class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium">{{ $percentHadir }}% dari anggota</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 dark:border-amber-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Izin / Sakit</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalIzin) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Berhalangan hadir</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-500/20 dark:border-rose-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Alpa</span>
            <div class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($totalAlpa) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Tanpa keterangan</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Belum Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ number_format($totalBelum) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Belum presensi</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-cyan-500/20 dark:border-cyan-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-cyan-600 uppercase tracking-wider block">Kehadiran</span>
            <div class="text-xl sm:text-2xl font-black text-cyan-700 dark:text-cyan-400 mt-1">{{ $percentHadir }}%</div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-1.5">
                <div class="bg-cyan-600 h-full rounded-full transition-all duration-500" style="width: {{ $percentHadir }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Interactive Tabs Navigation -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 dark:border-slate-800 p-2 sm:p-2.5 bg-slate-50/50 dark:bg-slate-950/40">
            @if(!$isStudent)
            <button onclick="switchTab('tab-scanner')" id="btn-tab-scanner" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'scanner' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Terminal Gerbang Ekstra</span>
            </button>
            @endif
            <button onclick="switchTab('tab-selfie')" id="btn-tab-selfie" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'selfie' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Presensi Kamera Selfie</span>
            </button>
            @if(Auth::user()->canRecordExtracurricularManual($selectedExtraId ?? $extra?->id))
            <button onclick="switchTab('tab-manual')" id="btn-tab-manual" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'manual' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Absen Manual Anggota</span>
            </button>
            @endif
            <button onclick="switchTab('tab-history')" id="btn-tab-history" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'history' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Presensi ({{ $attendances->count() }})</span>
            </button>
            @if(!$isStudent && (Auth::user()->canManageExtracurricular() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'pembina_ekstra']) || in_array(Auth::user()->role, ['admin', 'super_admin'], true)))
            <button onclick="switchTab('tab-members')" id="btn-tab-members" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'members' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Kelola Anggota ({{ $extra?->students?->count() ?? 0 }})</span>
            </button>
            @endif
        </div>

        @if(!$isStudent)
        <!-- TAB 1: SCANNER GERBANG EKSTRA (BARCODE, RFID & FACE RECOGNITION) -->
        <div id="tab-scanner" class="{{ $activeTab === 'scanner' ? '' : 'hidden' }} p-4 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Scanner Input Card -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-950 to-cyan-950 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                        <div class="relative z-10 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    Terminal Siap untuk: {{ $extra?->name }}
                                </span>
                                <!-- Status 5 Metode Presensi -->
                                <div class="flex items-center gap-1 flex-wrap text-[10px]">
                                    <span class="px-1.5 py-0.5 rounded {{ $enableSelfie ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-400' }}">Selfie: {{ $enableSelfie ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableBarcode ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-700 text-slate-400' }}">Barcode: {{ $enableBarcode ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableRfid ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-700 text-slate-400' }}">RFID: {{ $enableRfid ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableFaceRecognition ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400' }}">Face AI: {{ $enableFaceRecognition ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableManual ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-400' }}">Manual: {{ $enableManual ? 'Aktif' : 'Off' }}</span>
                                </div>
                            </div>

                            <!-- Pilihan Gerbang Sensor Terminal Ekstrakurikuler -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Pilih Gerbang Sensor / Mode Pemindaian Terminal:
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" onclick="setExtraGate('barcode')" id="btn-gate-barcode" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableBarcode ? 'border-cyan-500/60 bg-cyan-950/40 text-white ring-2 ring-cyan-500/50' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">1. Barcode</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableBarcode ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Scan Barcode NISN</span>
                                    </button>
                                    <button type="button" onclick="setExtraGate('rfid')" id="btn-gate-rfid" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableRfid ? 'border-cyan-500/60 bg-cyan-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">2. Kartu RFID</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableRfid ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Tap Kartu Anggota</span>
                                    </button>
                                    <button type="button" onclick="setExtraGate('face')" id="btn-gate-face" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableFaceRecognition ? 'border-cyan-500/60 bg-cyan-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">3. Face AI</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableFaceRecognition ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Deteksi Wajah AI</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Shared Device Selector -->
                            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 space-y-1.5">
                                <label for="device-select" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Terminal Gerbang / Perangkat Scanner:
                                </label>
                                <select id="device-select" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white outline-none focus:border-cyan-400">
                                    @forelse($devices as $dev)
                                        <option value="{{ $dev->device_code }}">
                                            {{ $dev->device_name }} ({{ $dev->device_code }}) - [{{ $dev->location }}]
                                        </option>
                                    @empty
                                        <option value="GATE-EKSTRA-01">Terminal Ekstrakurikuler (GATE-EKSTRA-01)</option>
                                    @endforelse
                                </select>
                            </div>

                            <form id="scanner-form" onsubmit="handleScannerSubmit(event)" class="space-y-3">
                                <label for="scan-input" id="scan-input-label" class="block text-xs text-slate-300 font-medium">Scan Barcode Siswa atau Masukkan NISN Anggota:</label>
                                <div class="relative">
                                    <input type="text" id="scan-input" autofocus autocomplete="off" placeholder="Arahkan barcode scanner atau ketik NISN..."
                                        class="w-full px-4 py-3.5 pl-12 bg-slate-900/90 border-2 border-cyan-500/70 focus:border-cyan-400 rounded-2xl text-base font-mono text-white placeholder-slate-500 outline-none shadow-inner">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-cyan-400">
                                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Tekan <strong>ENTER</strong> jika menggunakan barcode scanner USB</span>
                                    <button type="submit" id="btn-submit-scanner" class="px-4 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-bold transition cursor-pointer">Kirim Log Barcode</button>
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
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Aktivitas Scan Realtime
                        </h3>
                        <span class="text-[11px] text-slate-400" id="live-scan-count">{{ $recentLogs->count() }} Terdata</span>
                    </div>

                    <div id="live-scan-list" class="space-y-2 max-h-96 overflow-y-auto pr-1 divide-y divide-slate-200/60 dark:divide-slate-800/60">
                        @forelse($recentLogs as $log)
                            <div class="pt-2 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950 text-cyan-600 dark:text-cyan-400 font-black flex items-center justify-center text-[10px] shrink-0">
                                        {{ strtoupper(substr($log->student?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $log->student?->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->student?->schoolClass?->name ?? '-' }} • Method: {{ $log->method }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
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
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi Selfie Kamera Langsung (WebRTC)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pastikan wajah terlihat jelas dan sistem GPS mendeteksi Anda di lingkungan sekolah.</p>
                </div>

                <!-- Geofencing Info -->
                <div class="bg-cyan-50/70 dark:bg-cyan-950/40 p-3.5 rounded-2xl border border-cyan-200/70 dark:border-cyan-800/70 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-cyan-900 dark:text-cyan-200 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Radius & GPS Geofence Sekolah
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $enableSelfie ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                            Selfie: {{ $enableSelfie ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                        Maksimal radius <strong>{{ $geofenceRadius }} meter</strong> dari lokasi sekolah.
                    </p>
                </div>

                <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden bg-slate-950 aspect-4/3 border-2 border-cyan-500/40 shadow-xl flex items-center justify-center">
                    <video id="selfie-video" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="selfie-canvas" class="hidden"></canvas>
                    <div id="camera-loading" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center text-white gap-2">
                        <svg class="w-8 h-8 animate-spin text-cyan-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span class="text-xs font-semibold">Menginisialisasi Kamera...</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="button" onclick="startCamera()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-300 dark:hover:bg-slate-700 transition">
                        Nyalakan Kamera
                    </button>
                    <button type="button" onclick="captureAndSubmitSelfie()" id="btn-take-selfie" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 hover:from-cyan-500 hover:to-emerald-400 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ambil Foto Selfie & Simpan</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 3: ABSEN MANUAL ANGGOTA --}}
        @if(Auth::user()->canRecordExtracurricularManual($selectedExtraId ?? $extra?->id))
        <div id="tab-manual" class="{{ $activeTab === 'manual' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Presensi Manual Anggota {{ $extra?->name }}</span>
                        @if($isExtraSecretary)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-700">Akses Sekretaris Ekskul</span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500">Tentukan status kehadiran anggota secara massal per sesi latihan.</p>
                </div>

                <form method="GET" action="{{ route('extracurricular.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="extra_id" value="{{ $selectedExtraId }}">
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-[11px] font-bold text-slate-500">Tanggal:</span>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                    </div>

                    <select name="class_id" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                        <option value="">Semua Rombel Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Quick Filter Bar with Search and A-Z Initials for Tab Manual -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-2">
                <div class="relative">
                    <input type="text" id="manualStudentSearchInput" oninput="filterManualAttendanceStudents(this.value)"
                        placeholder="🔍 Cari anggota (Ketik nama, NIS, rombel, atau inisial misal: AD, MRP)..."
                        class="w-full pl-9 pr-9 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-cyan-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="manualStudentSearchClear" onclick="resetManualStudentSearch()" class="hidden absolute right-3 top-2 text-slate-400 hover:text-slate-600 dark:hover:text-white">✕</button>
                </div>
                <!-- A-Z Initial Chips -->
                <div class="flex items-center gap-1 overflow-x-auto pb-1 text-xs no-scrollbar" id="manual-initials-bar">
                    <button type="button" onclick="filterManualStudentByInitial('ALL')" class="manual-initial-chip px-2.5 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition" data-letter="ALL">Semua</button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterManualStudentByInitial('{{ $char }}')" class="manual-initial-chip px-1.5 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-100 dark:hover:bg-slate-800" data-letter="{{ $char }}">{{ $char }}</button>
                    @endforeach
                </div>
            </div>

            @if($targetStudents->isNotEmpty())
            <form id="manual-attendance-form" action="{{ route('extracurricular.manual') }}" method="POST">
                @csrf
                <input type="hidden" name="extracurricular_id" value="{{ $extra->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="flex items-center justify-between py-2 border-b border-slate-200/80 dark:border-slate-800 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500">Menampilkan <strong>{{ $targetStudents->count() }}</strong> Anggota Terdaftar</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800/60">
                            📅 {{ $selectedDateFormatted }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-1 rounded-lg border border-amber-200 dark:border-amber-800/60" title="Bypass pembatasan jam operasional jika absen dilakukan di luar jadwal rutin">
                            <input type="checkbox" name="bypass_time_lock" value="1" checked class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span>Bypass Jam Sesi</span>
                        </label>
                        <button type="button" onclick="setAllManualStatus('HADIR')" class="text-xs px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 transition">
                            ✓ Semua Hadir
                        </button>
                        <button type="button" onclick="resetAllManualStatus()" class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold transition">
                            Kosongkan
                        </button>
                        <button type="submit" class="text-xs px-4 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi</span>
                        </button>
                    </div>
                </div>

                <!-- Bilah Aksi Massal Pilihan Siswa Ekstrakurikuler (Multi-Select Checkboxes Toolbar) -->
                <div class="p-3 bg-gradient-to-r from-cyan-50/90 via-slate-50 to-cyan-50/90 dark:from-cyan-950/40 dark:via-slate-900/40 dark:to-cyan-950/40 rounded-2xl border border-cyan-200/70 dark:border-cyan-800/60 flex flex-wrap items-center justify-between gap-3 text-xs my-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer font-extrabold text-cyan-950 dark:text-cyan-200 select-none">
                            <input type="checkbox" id="extra-check-all-students" onchange="toggleSelectAllExtraStudents(this.checked)" class="w-4 h-4 rounded text-cyan-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700">
                            <span>Pilih Semua Siswa</span>
                        </label>
                        <span id="extra-selected-students-count" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-cyan-100 dark:bg-cyan-900/60 text-cyan-800 dark:text-cyan-300 transition">
                            0 Terpilih
                        </span>
                        <button type="button" onclick="selectUnmarkedExtraStudents()" class="text-[10.5px] px-2 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            ⚡ Pilih Belum Absen
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mr-0.5">Terapkan ke Siswa Terpilih:</span>
                        <button type="button" onclick="setExtraStatusesForSelected('HADIR')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>✓</span><span>HADIR</span>
                        </button>
                        <button type="button" onclick="setExtraStatusesForSelected('IZIN')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>📝</span><span>IZIN</span>
                        </button>
                        <button type="button" onclick="setExtraStatusesForSelected('SAKIT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>🏥</span><span>SAKIT</span>
                        </button>
                        <button type="button" onclick="setExtraStatusesForSelected('ALPA')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>❌</span><span>ALPA</span>
                        </button>
                        <button type="button" onclick="clearExtraStatusesForSelected()" class="text-[11px] font-bold px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition cursor-pointer" title="Batalkan status pilihan siswa tercentang">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80" id="manualStudentsList">
                    @foreach($targetStudents as $idx => $st)
                        @php
                            $att = $attendanceByStudentId->get($st->id);
                            $currentStatus = $att ? $att->status : null;
                            $stWords = explode(' ', trim($st->name));
                            $stInitials = collect($stWords)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstLetter = strtoupper(substr(trim($st->name), 0, 1));
                        @endphp
                        <div class="manual-student-item extra-student-row py-2.5 px-2 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40"
                             data-id="{{ $st->id }}"
                             data-name="{{ strtolower($st->name) }}"
                             data-nis="{{ $st->nis }}"
                             data-class="{{ strtolower($st->schoolClass?->name ?? '') }}"
                             data-initials="{{ strtolower($stInitials) }}"
                             data-firstletter="{{ $firstLetter }}">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" class="extra-manual-student-checkbox w-4 h-4 rounded text-cyan-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700" data-student-id="{{ $st->id }}" data-has-status="{{ $att ? '1' : '0' }}" onchange="updateExtraSelectedStudentsCounter()">
                                    <span class="text-xs text-slate-400 font-mono w-6 text-right">{{ $idx + 1 }}.</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $st->name }}</div>
                                        <span class="text-[10px] px-1.5 py-0.2 bg-cyan-100 dark:bg-cyan-950 text-cyan-800 dark:text-cyan-300 font-mono font-bold rounded">
                                            {{ strtoupper($stInitials) }}
                                        </span>
                                        @if($att)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black
                                                @if($att->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                @elseif($att->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                                @elseif(in_array($att->status, ['IZIN', 'SAKIT'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                                Tercatat: {{ $att->status }}
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                Belum Terdata
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $st->nisn ?? $st->nis }} • Rombel: {{ $st->schoolClass?->name ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach(['HADIR' => 'bg-emerald-500', 'IZIN' => 'bg-sky-500', 'SAKIT' => 'bg-yellow-500', 'ALPA' => 'bg-rose-500'] as $stKey => $stBg)
                                    <label class="text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer flex items-center gap-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <input type="radio" name="statuses[{{ $st->id }}]" value="{{ $stKey }}" {{ $currentStatus === $stKey ? 'checked' : '' }} class="manual-status-radio text-cyan-600 focus:ring-0">
                                        <span>{{ $stKey }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center flex-wrap gap-2">
                    <div class="text-xs text-slate-500">
                        Pastikan data presensi anggota sudah sesuai sebelum menekan tombol simpan.
                    </div>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Presensi Manual</span>
                    </button>
                </div>
            </form>
            @else
            <div class="py-12 text-center text-xs text-slate-400">
                Belum ada anggota terdaftar untuk kegiatan ekstra ini. Silakan tambahkan anggota di Menu Master Ekstrakurikuler.
            </div>
            @endif
        </div>
        @endif

        <!-- TAB 4: REKAP RIWAYAT PRESENSI -->
        <div id="tab-history" class="{{ $activeTab === 'history' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Presensi Terdata</h3>
                    <p class="text-xs text-slate-500">Sesi {{ $selectedDateFormatted }} ({{ $attendances->count() }} Catatan)</p>
                </div>

                <form method="GET" action="{{ route('extracurricular.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="extra_id" value="{{ $selectedExtraId }}">
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-500">Filter Tanggal:</span>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase text-[10px]">
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Metode</th>
                            <th class="py-2.5 px-3">Pencatat</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($attendances as $idx => $row)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $row->student?->name }}</div>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $row->student?->nisn ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300 font-semibold">
                                    {{ $row->schoolClass?->name ?? ($row->student?->schoolClass?->name ?? '-') }}
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                    {{ substr($row->time, 0, 5) }} WIB
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black
                                        @if($row->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif($row->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                        @elseif(in_array($row->status, ['IZIN', 'SAKIT'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                        @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        @if($row->method === 'SELFIE') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif(in_array($row->method, ['BARCODE', 'QR_CODE'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                        @elseif($row->method === 'RFID') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                        @elseif($row->method === 'FACE_RECOGNITION') bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300
                                        @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 @endif">
                                        {{ $row->method }}
                                    </span>
                                    @if($row->selfie_path)
                                        <a href="{{ asset('storage/' . $row->selfie_path) }}" target="_blank" class="ml-1 text-[10px] text-cyan-600 hover:underline">Foto</a>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-500 text-[11px]">
                                    {{ $row->recorder?->name ?? 'Mandiri' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    @if(Auth::user()->canRecordAttendanceManual() || $isExtraSecretary || Auth::user()->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas']))
                                    <form action="{{ route('extracurricular.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data presensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Belum ada data presensi pada sesi tanggal {{ $selectedDateFormatted }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!$isStudent && (Auth::user()->canManageExtracurricular() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'pembina_ekstra']) || in_array(Auth::user()->role, ['admin', 'super_admin'], true)))
        <!-- TAB 5: KELOLA ANGGOTA EKSTRAKURIKULER -->
        <div id="tab-members" class="{{ $activeTab === 'members' ? '' : 'hidden' }} p-4 sm:p-6 space-y-6">
            <!-- Header & Form Tambah Anggota -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-cyan-950 text-white p-5 sm:p-6 rounded-3xl border border-slate-800 shadow-xl space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                PENDAFTARAN ANGGOTA
                            </span>
                            <span class="text-xs text-slate-400">
                                Ekstrakurikuler: <strong class="text-white">{{ $extra?->name }}</strong>
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-white mt-1">
                            Tambah Anggota Siswa ke Ekstrakurikuler
                        </h3>
                        <p class="text-xs text-slate-400">
                            Pilih siswa dari database sekolah menggunakan pencarian nama atau inisial cepat (A-Z).
                        </p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[11px] text-slate-400 block font-semibold">Total Anggota Terdaftar</span>
                        <span class="text-2xl font-black text-cyan-400">{{ $extra?->students?->count() ?? 0 }}</span>
                        <span class="text-xs text-slate-400">Siswa</span>
                    </div>
                </div>

                <form action="{{ route('extracurricular.members.add', $extra?->id ?? 0) }}" method="POST" id="formAddExtraMembers" class="space-y-4 text-xs" onsubmit="return validateExtraMemberSubmit()">
                    @csrf
                    @php
                        $existingStudentIds = $extra?->students?->pluck('id')->flip()->toArray() ?? [];
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                        <!-- Kolom Kiri: Filter & Daftar Siswa Calon Anggota (Multi-Select) -->
                        <div class="lg:col-span-8 space-y-3">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
                                <label class="font-extrabold text-slate-200 text-xs flex items-center gap-1.5">
                                    <span>Pilih Siswa Calon Anggota:</span>
                                    <span class="text-[11px] text-cyan-300 font-normal">(Centang siswa yang ingin dimasukkan)</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="selectAllFilteredMembers()" class="px-2.5 py-1 text-[11px] font-bold bg-cyan-600/30 hover:bg-cyan-600/50 text-cyan-200 border border-cyan-500/40 rounded-lg transition cursor-pointer">
                                        ✓ Pilih Semua yang Tampil
                                    </button>
                                    <button type="button" onclick="deselectAllMembers()" class="px-2.5 py-1 text-[11px] font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition cursor-pointer">
                                        ✗ Batal Pilih Semua
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Bar: Dropdown Kelas & Pencarian Cepat -->
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                                <div class="sm:col-span-4">
                                    <select id="extra_member_class_filter" onchange="filterExtraMemberStudents()"
                                        class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl outline-none focus:border-cyan-500 text-xs text-white">
                                        <option value="ALL">Semua Rombel / Kelas</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-8 relative">
                                    <input type="text" id="extra_member_search_input"
                                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa..."
                                        autocomplete="off"
                                        oninput="filterExtraMemberStudents()"
                                        class="w-full pl-9 pr-8 py-2 bg-slate-900/90 border border-slate-700 rounded-xl outline-none focus:border-cyan-500 text-xs text-white placeholder-slate-400 transition">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <button type="button" id="extra-student-search-clear" onclick="resetExtraMemberSearch()" class="hidden absolute right-2.5 top-2 text-slate-400 hover:text-white">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <!-- Alphabet Inisial Quick Bar -->
                            <div class="flex items-center gap-1 overflow-x-auto pb-1 pt-0.5 text-xs no-scrollbar" id="extra-initials-bar">
                                <button type="button" onclick="filterExtraMemberByInitial('ALL')"
                                    class="extra-initial-chip px-2.5 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition" data-letter="ALL">
                                    Semua
                                </button>
                                @foreach(range('A', 'Z') as $char)
                                    <button type="button" onclick="filterExtraMemberByInitial('{{ $char }}')"
                                        class="extra-initial-chip px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-700" data-letter="{{ $char }}">
                                        {{ $char }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- List Siswa Calon Anggota (Checkbox Container) -->
                            <div class="max-h-64 overflow-y-auto rounded-2xl bg-slate-900/90 border border-slate-700 divide-y divide-slate-800/80 p-1" id="extraMemberStudentsList">
                                @foreach($allStudents ?? [] as $s)
                                    @php
                                        $isAlreadyMember = isset($existingStudentIds[$s->id]);
                                        $words = explode(' ', trim($s->name));
                                        $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                                        $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                                    @endphp
                                    <div class="extra-student-item px-3 py-2 rounded-xl transition flex items-center justify-between gap-3 {{ $isAlreadyMember ? 'opacity-50 bg-slate-900/40 cursor-not-allowed' : 'hover:bg-cyan-950/50 cursor-pointer' }}"
                                         data-id="{{ $s->id }}"
                                         data-name="{{ $s->name }}"
                                         data-nis="{{ $s->nis }}"
                                         data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                                         data-initials="{{ strtolower($initials) }}"
                                         data-firstletter="{{ $firstChar }}"
                                         data-is-member="{{ $isAlreadyMember ? '1' : '0' }}"
                                         onclick="toggleExtraStudentRow(event, '{{ $s->id }}')">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <input type="checkbox"
                                                   id="extra_chk_{{ $s->id }}"
                                                   class="extra-student-checkbox rounded border-slate-700 text-cyan-600 focus:ring-0 shrink-0 w-4 h-4 cursor-pointer"
                                                   value="{{ $s->id }}"
                                                   {{ $isAlreadyMember ? 'disabled' : '' }}
                                                   onchange="handleStudentCheckboxChange(this, '{{ $s->id }}', '{{ addslashes($s->name) }}', '{{ $s->schoolClass?->name ?? 'Umum' }}', '{{ $s->nis }}')">
                                            <span class="w-6 h-6 rounded-md bg-cyan-950 text-cyan-300 border border-cyan-800 font-bold text-[10px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($initials, 0, 2)) }}
                                            </span>
                                            <div class="min-w-0 truncate">
                                                <div class="font-bold text-slate-100 text-xs truncate">{{ $s->name }}</div>
                                                <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-1.5">
                                            @if($isAlreadyMember)
                                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-950 text-emerald-300 border border-emerald-800 font-bold">
                                                    ✓ Sudah Anggota
                                                </span>
                                            @else
                                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 font-mono font-bold">
                                                    {{ strtoupper($initials) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kolom Kanan: Ringkasan Siswa Terpilih & Submit Form -->
                        <div class="lg:col-span-4 bg-slate-900/80 border border-slate-700/80 rounded-2xl p-4 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                <div>
                                    <h4 class="font-black text-white text-xs uppercase tracking-wider">Siswa Terpilih</h4>
                                    <p class="text-[11px] text-cyan-300">
                                        <span id="selectedExtraCount">0</span> siswa siap dimasukkan
                                    </p>
                                </div>
                                <button type="button" onclick="deselectAllMembers()" class="text-[10px] font-bold text-rose-400 hover:text-rose-300 underline cursor-pointer">
                                    Kosongkan
                                </button>
                            </div>

                            <!-- Tray Chips Siswa Terpilih -->
                            <div id="selectedExtraMembersTray" class="max-h-36 overflow-y-auto space-y-1.5 pr-1">
                                <div id="emptyExtraSelectionNotice" class="text-slate-500 text-center py-6 text-[11px]">
                                    Belum ada siswa yang dipilih.<br>Silakan centang siswa di kolom sebelah kiri.
                                </div>
                            </div>

                            <!-- Pilihan Posisi / Peran -->
                            <div class="space-y-1 pt-2 border-t border-slate-800">
                                <label class="block font-bold text-slate-200 text-xs">Peran / Posisi dalam Ekstra:</label>
                                <select name="role" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-cyan-500 font-semibold text-white text-xs">
                                    <option value="Anggota">Anggota Biasa</option>
                                    <option value="Ketua">Ketua Ekstrakurikuler</option>
                                    <option value="Wakil Ketua">Wakil Ketua</option>
                                    <option value="Sekretaris">Sekretaris (Akses Absen Manual)</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Koordinator Latihan">Koordinator Latihan</option>
                                </select>
                            </div>

                            <!-- Container Hidden Input student_ids[] -->
                            <div id="extraSelectedInputsContainer"></div>

                            <!-- Tombol Submit -->
                            <button type="submit" id="btnSubmitExtraMembers" disabled
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-teal-600 opacity-50 cursor-not-allowed hover:from-cyan-500 hover:to-teal-500 text-white font-extrabold rounded-xl shadow-md transition flex items-center justify-center gap-2 text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambahkan <span id="btnExtraCount">0</span> Siswa Terpilih</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Anggota Terdaftar Saat Ini -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3.5">
                    <div>
                        <h4 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">
                            Daftar Anggota Terdaftar ({{ $extra?->students?->count() ?? 0 }} Siswa)
                        </h4>
                        <p class="text-[11px] text-slate-400">
                            Siswa terdaftar aktif sebagai anggota ekstrakurikuler {{ $extra?->name }}
                        </p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <input type="text" id="memberTableFilterInput" oninput="filterExtraMemberTable(this.value)" placeholder="🔍 Saring nama, NIS, atau inisial..." class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-cyan-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left" id="extraMembersTable">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center">No</th>
                                <th class="py-3 px-3">Siswa & Inisial</th>
                                <th class="py-3 px-3">NIS / NISN</th>
                                <th class="py-3 px-3">Kelas</th>
                                <th class="py-3 px-3">Peran / Jabatan</th>
                                <th class="py-3 px-3">Terdaftar Sejak</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="extraMembersTableBody">
                            @forelse($extra?->students ?? [] as $idx => $st)
                                @php
                                    $stWords = explode(' ', trim($st->name));
                                    $stInitials = collect($stWords)->map(fn($w) => substr($w, 0, 1))->join('');
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition extra-member-row"
                                    data-search="{{ strtolower($st->name . ' ' . $st->nis . ' ' . $st->nisn . ' ' . $stInitials . ' ' . ($st->schoolClass?->name ?? '')) }}">
                                    <td class="py-3 px-3 text-center font-bold text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300 font-black text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($stInitials, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-slate-900 dark:text-white">{{ $st->name }}</div>
                                                <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-bold font-mono">Inisial: {{ strtoupper($stInitials) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $st->nis ?? '-' }} / {{ $st->nisn ?? '-' }}
                                    </td>
                                    <td class="py-3 px-3 font-bold text-slate-700 dark:text-slate-200">
                                        {{ $st->schoolClass?->name ?? 'Umum' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <form action="{{ route('extracurricular.members.role', [$extra?->id ?? 0, $st->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" onchange="this.form.submit()" title="Ganti Jabatan Anggota" class="text-[11px] font-bold px-2.5 py-1 rounded-lg border cursor-pointer transition {{ in_array($st->pivot->role ?? 'Anggota', ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara']) ? 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700' : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                                                <option value="Anggota" {{ ($st->pivot->role ?? 'Anggota') === 'Anggota' ? 'selected' : '' }}>Anggota Biasa</option>
                                                <option value="Sekretaris" {{ ($st->pivot->role ?? '') === 'Sekretaris' ? 'selected' : '' }}>Sekretaris (Absen Manual)</option>
                                                <option value="Ketua" {{ ($st->pivot->role ?? '') === 'Ketua' ? 'selected' : '' }}>Ketua</option>
                                                <option value="Wakil Ketua" {{ ($st->pivot->role ?? '') === 'Wakil Ketua' ? 'selected' : '' }}>Wakil Ketua</option>
                                                <option value="Bendahara" {{ ($st->pivot->role ?? '') === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-3 px-3 text-slate-400 text-[11px]">
                                        {{ $st->pivot->joined_at ? \Carbon\Carbon::parse($st->pivot->joined_at)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <form action="{{ route('extracurricular.members.remove', [$extra?->id ?? 0, $st->id]) }}" method="POST" class="inline" onsubmit="return confirm('Keluarkan siswa {{ addslashes($st->name) }} dari ekstrakurikuler {{ addslashes($extra?->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-bold rounded-lg transition inline-flex items-center gap-1 cursor-pointer" title="Keluarkan Anggota">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Keluarkan</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        Belum ada anggota terdaftar untuk ekstrakurikuler ini. Silakan tambahkan anggota di form atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
</div>

@if($canManageSchedule ?? false)
<!-- Modal Konfigurasi Jadwal Latihan Ekstrakurikuler (Akses Pembina & Admin) -->
<div id="schedule-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden transition-all">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Atur Jadwal & Waktu Latihan</h3>
                    <p class="text-[11px] text-slate-500">{{ $extra?->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg p-1 cursor-pointer">✕</button>
        </div>

        <form action="{{ route('extracurricular.schedule.update', $extra?->id ?? 0) }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @php
                $configuredDays = $extra ? $extra->effective_days : ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
            @endphp

            <!-- Pilihan Hari Latihan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Hari Latihan Rutin:
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach(['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $dayKey => $dayLabel)
                        <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-slate-700 dark:text-slate-300 font-semibold select-none">
                            <input type="checkbox" name="schedule_days[]" value="{{ $dayKey }}" {{ in_array($dayKey, $configuredDays, true) ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-500 focus:ring-0">
                            <span>{{ $dayLabel }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-400 mt-1">*Pilih hari di mana kegiatan ekstrakurikuler ini dilaksanakan.</p>
            </div>

            <!-- Jam Latihan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Mulai Sesi:</label>
                    <input type="time" name="start_time" value="{{ $extra?->start_time ? substr($extra->start_time, 0, 5) : '14:00' }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Selesai Sesi:</label>
                    <input type="time" name="end_time" value="{{ $extra?->end_time ? substr($extra->end_time, 0, 5) : '17:30' }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Lokasi Latihan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Latihan / Ruangan:</label>
                <input type="text" name="location" value="{{ $extra?->location }}" placeholder="Misal: Lapangan Basket, Aula Utama, Lab Komputer 1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:border-amber-500">
            </div>

            <!-- Deskripsi / Keterangan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi & Catatan Kegiatan:</label>
                <textarea name="description" rows="2" placeholder="Catatan atau petunjuk kegiatan ekstra..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:border-amber-500">{{ $extra?->description }}</textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold shadow-md shadow-amber-500/20 transition cursor-pointer">
                    Simpan Jadwal Latihan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // Tab Switching
    function switchTab(tabId) {
        ['tab-scanner', 'tab-selfie', 'tab-manual', 'tab-history', 'tab-members'].forEach(id => {
            const el = document.getElementById(id);
            const btn = document.getElementById('btn-' + id);
            if (el) el.classList.add('hidden');
            if (btn) {
                btn.classList.remove('bg-cyan-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-600', 'dark:text-slate-400');
            }
        });
        const target = document.getElementById(tabId);
        const targetBtn = document.getElementById('btn-' + tabId);
        if (target) target.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.add('bg-cyan-600', 'text-white', 'shadow-xs');
            targetBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
        }
        if (tabId === 'tab-scanner') {
            document.getElementById('scan-input')?.focus();
        }
    }

    // Helper functions for Extra Member Multi-Selection
    let activeExtraMemberInitial = 'ALL';
    const selectedExtraMembers = new Map();

    function resetExtraMemberSearch() {
        const input = document.getElementById('extra_member_search_input');
        if (input) input.value = '';
        document.getElementById('extra-student-search-clear')?.classList.add('hidden');
        filterExtraMemberStudents();
    }

    function filterExtraMemberStudents() {
        const q = (document.getElementById('extra_member_search_input')?.value || '').toLowerCase().trim();
        const selectedClass = document.getElementById('extra_member_class_filter')?.value || 'ALL';
        const clearBtn = document.getElementById('extra-student-search-clear');
        if (clearBtn) {
            if (q.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        const items = document.querySelectorAll('.extra-student-item');
        items.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const nis = (item.dataset.nis || '').toLowerCase();
            const cls = (item.dataset.class || '');
            const initials = (item.dataset.initials || '').toLowerCase();
            const firstLetter = (item.dataset.firstletter || '').toUpperCase();

            const matchesQuery = q === '' || name.includes(q) || nis.includes(q) || cls.toLowerCase().includes(q) || initials.includes(q);
            const matchesClass = selectedClass === 'ALL' || cls === selectedClass;
            const matchesInitial = activeExtraMemberInitial === 'ALL' || firstLetter === activeExtraMemberInitial;

            if (matchesQuery && matchesClass && matchesInitial) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function filterExtraMemberByInitial(char) {
        activeExtraMemberInitial = char;
        document.querySelectorAll('.extra-initial-chip').forEach(btn => {
            if (btn.dataset.letter === char) {
                btn.className = 'extra-initial-chip px-2.5 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'extra-initial-chip px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-700';
            }
        });
        filterExtraMemberStudents();
    }

    function toggleExtraStudentRow(event, id) {
        if (event.target.tagName === 'INPUT') return;
        const chk = document.getElementById('extra_chk_' + id);
        if (chk && !chk.disabled) {
            chk.checked = !chk.checked;
            chk.dispatchEvent(new Event('change'));
        }
    }

    function handleStudentCheckboxChange(chk, id, name, cls, nis) {
        const strId = String(id);
        if (chk.checked) {
            selectedExtraMembers.set(strId, { id: strId, name, cls, nis });
        } else {
            selectedExtraMembers.delete(strId);
        }
        renderSelectedExtraMembers();
    }

    function selectAllFilteredMembers() {
        const items = document.querySelectorAll('.extra-student-item');
        items.forEach(item => {
            if (!item.classList.contains('hidden') && item.dataset.isMember !== '1') {
                const id = String(item.dataset.id);
                const chk = document.getElementById('extra_chk_' + id);
                if (chk && !chk.disabled) {
                    chk.checked = true;
                    selectedExtraMembers.set(id, {
                        id: id,
                        name: item.dataset.name,
                        cls: item.dataset.class,
                        nis: item.dataset.nis
                    });
                }
            }
        });
        renderSelectedExtraMembers();
    }

    function deselectAllMembers() {
        selectedExtraMembers.clear();
        document.querySelectorAll('.extra-student-checkbox').forEach(chk => {
            if (!chk.disabled) chk.checked = false;
        });
        renderSelectedExtraMembers();
    }

    function removeSelectedMember(id) {
        const strId = String(id);
        selectedExtraMembers.delete(strId);
        const chk = document.getElementById('extra_chk_' + strId);
        if (chk) chk.checked = false;
        renderSelectedExtraMembers();
    }

    function renderSelectedExtraMembers() {
        const count = selectedExtraMembers.size;
        const countEl = document.getElementById('selectedExtraCount');
        const btnCountEl = document.getElementById('btnExtraCount');
        const btnSubmit = document.getElementById('btnSubmitExtraMembers');
        const tray = document.getElementById('selectedExtraMembersTray');
        const inputsContainer = document.getElementById('extraSelectedInputsContainer');

        if (countEl) countEl.innerText = count;
        if (btnCountEl) btnCountEl.innerText = count;

        if (btnSubmit) {
            if (count > 0) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        if (tray) {
            if (count === 0) {
                tray.innerHTML = '<div id="emptyExtraSelectionNotice" class="text-slate-500 text-center py-6 text-[11px]">Belum ada siswa yang dipilih.<br>Silakan centang siswa di kolom sebelah kiri.</div>';
            } else {
                let html = '';
                selectedExtraMembers.forEach(st => {
                    html += `
                        <div class="p-2 bg-slate-950/80 border border-slate-700/80 rounded-xl flex items-center justify-between gap-2 text-xs">
                            <div class="min-w-0 truncate">
                                <span class="font-bold text-white truncate block text-[11px]">${escapeHtml(st.name)}</span>
                                <span class="text-[10px] text-cyan-300">Kelas ${escapeHtml(st.cls)} • NIS: ${escapeHtml(st.nis)}</span>
                            </div>
                            <button type="button" onclick="removeSelectedMember('${st.id}')" class="text-rose-400 hover:text-rose-300 font-bold px-1.5 py-0.5 rounded-md hover:bg-rose-950/40 transition shrink-0" title="Batal Pilih">✕</button>
                        </div>
                    `;
                });
                tray.innerHTML = html;
            }
        }

        if (inputsContainer) {
            let inputsHtml = '';
            selectedExtraMembers.forEach(st => {
                inputsHtml += `<input type="hidden" name="student_ids[]" value="${st.id}">`;
            });
            inputsContainer.innerHTML = inputsHtml;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function validateExtraMemberSubmit() {
        if (selectedExtraMembers.size === 0) {
            alert('Silakan pilih minimal satu siswa calon anggota terlebih dahulu.');
            return false;
        }
        return true;
    }

    function filterExtraMemberTable(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.extra-member-row').forEach(row => {
            const searchData = (row.dataset.search || '').toLowerCase();
            if (q === '' || searchData.includes(q)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    // Helper functions for Tab Manual Filter with Initials and Search
    let activeManualStudentInitial = 'ALL';

    function resetManualStudentSearch() {
        const input = document.getElementById('manualStudentSearchInput');
        if (input) input.value = '';
        document.getElementById('manualStudentSearchClear')?.classList.add('hidden');
        filterManualAttendanceStudents('');
    }

    function filterManualStudentByInitial(letter) {
        activeManualStudentInitial = letter;
        document.querySelectorAll('.manual-initial-chip').forEach(btn => {
            if (btn.dataset.letter === letter) {
                btn.className = 'manual-initial-chip px-2.5 py-0.5 rounded-lg bg-cyan-600 text-white font-bold text-[10px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'manual-initial-chip px-1.5 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[10px] shrink-0 transition hover:bg-slate-100 dark:hover:bg-slate-800';
            }
        });
        const currentQuery = document.getElementById('manualStudentSearchInput')?.value || '';
        filterManualAttendanceStudents(currentQuery);
    }

    function filterManualAttendanceStudents(query) {
        const q = (query || '').toLowerCase().trim();
        const clearBtn = document.getElementById('manualStudentSearchClear');
        if (clearBtn) {
            if (q.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        document.querySelectorAll('.manual-student-item').forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const nis = (item.dataset.nis || '').toLowerCase();
            const cls = (item.dataset.class || '').toLowerCase();
            const initials = (item.dataset.initials || '').toLowerCase();
            const firstLetter = (item.dataset.firstletter || '').toUpperCase();

            const matchesText = q === '' || name.includes(q) || nis.includes(q) || cls.includes(q) || initials.includes(q);
            const matchesInitial = activeManualStudentInitial === 'ALL' || firstLetter === activeManualStudentInitial;

            if (matchesText && matchesInitial) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const combobox = document.getElementById('extra-member-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeExtraMemberDropdown();
        }
    });

    // Gate Controller for Ekstrakurikuler Terminal
    let currentExtraGate = '{{ $enableBarcode ? 'barcode' : ($enableRfid ? 'rfid' : ($enableFaceRecognition ? 'face' : 'barcode')) }}';

    function setExtraGate(gate) {
        currentExtraGate = gate;
        const inputLabel = document.getElementById('scan-input-label');
        const input = document.getElementById('scan-input');
        const submitBtn = document.getElementById('btn-submit-scanner');

        document.querySelectorAll('.gate-btn').forEach(b => {
            b.classList.remove('border-cyan-500', 'bg-cyan-950/60', 'ring-2', 'ring-cyan-500/50');
        });

        const activeBtn = document.getElementById('btn-gate-' + gate);
        if (activeBtn) {
            activeBtn.classList.add('border-cyan-500', 'bg-cyan-950/60', 'ring-2', 'ring-cyan-500/50');
        }

        if (gate === 'barcode') {
            if (inputLabel) inputLabel.innerText = "Scan Barcode Anggota atau Masukkan NISN Siswa:";
            if (input) {
                input.placeholder = "Arahkan barcode scanner ke kartu anggota atau ketik NISN...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log Barcode";
        } else if (gate === 'rfid') {
            if (inputLabel) inputLabel.innerText = "Tempelkan Kartu RFID Anggota ke Reader Sensor / UID:";
            if (input) {
                input.placeholder = "Dekatkan kartu RFID anggota ke reader...";
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
        const deviceCode = deviceSelect ? deviceSelect.value : 'GATE-EKSTRA-01';
        const scanMethod = currentExtraGate === 'rfid' ? 'RFID' : (currentExtraGate === 'barcode' ? 'BARCODE' : 'FACE_RECOGNITION');

        try {
            const res = await fetch("{{ route('extracurricular.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    extracurricular_id: {{ $extra?->id ?? 'null' }},
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
                        <div class="text-xs mt-0.5">Anggota: <strong>${data.student.name}</strong> (${data.student.class_name}) • Pukul: <strong>${data.formatted_time}</strong></div>
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

        const bypassToggle = document.getElementById('bypass-time-toggle');
        const bypass = bypassToggle ? bypassToggle.checked : false;

        try {
            const res = await fetch("{{ route('extracurricular.selfie') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    extracurricular_id: {{ $extra?->id ?? 'null' }},
                    selfie_image: dataUrl,
                    latitude: lat,
                    longitude: lng,
                    bypass_time_lock: bypass
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
    }

    // Modal Jadwal Latihan Ekstrakurikuler
    function openScheduleModal() {
        const modal = document.getElementById('schedule-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeScheduleModal() {
        const modal = document.getElementById('schedule-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeScheduleModal();
        }
    });

    // Extracurricular Multi-Select Students Checkboxes & Bulk Batch Actions
    function toggleSelectAllExtraStudents(checked) {
        document.querySelectorAll('.extra-manual-student-checkbox').forEach(cb => {
            const item = cb.closest('.manual-student-item');
            if (!item || !item.classList.contains('hidden')) {
                cb.checked = checked;
            }
        });
        updateExtraSelectedStudentsCounter();
    }

    function selectUnmarkedExtraStudents() {
        document.querySelectorAll('.extra-manual-student-checkbox').forEach(cb => {
            const item = cb.closest('.manual-student-item');
            if (!item || !item.classList.contains('hidden')) {
                cb.checked = (cb.getAttribute('data-has-status') === '0');
            }
        });
        updateExtraSelectedStudentsCounter();
    }

    function updateExtraSelectedStudentsCounter() {
        const allBoxes = document.querySelectorAll('.extra-manual-student-checkbox');
        const checkedBoxes = document.querySelectorAll('.extra-manual-student-checkbox:checked');
        const counterEl = document.getElementById('extra-selected-students-count');
        if (counterEl) {
            counterEl.textContent = `${checkedBoxes.length} Terpilih`;
            if (checkedBoxes.length > 0) {
                counterEl.classList.remove('bg-cyan-100', 'text-cyan-800', 'dark:bg-cyan-900/60', 'dark:text-cyan-300');
                counterEl.classList.add('bg-cyan-600', 'text-white');
            } else {
                counterEl.classList.remove('bg-cyan-600', 'text-white');
                counterEl.classList.add('bg-cyan-100', 'text-cyan-800', 'dark:bg-cyan-900/60', 'dark:text-cyan-300');
            }
        }
        const checkAll = document.getElementById('extra-check-all-students');
        if (checkAll) {
            checkAll.checked = (allBoxes.length > 0 && checkedBoxes.length === allBoxes.length);
            checkAll.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        }
    }

    function setExtraStatusesForSelected(status) {
        const checkedBoxes = document.querySelectorAll('.extra-manual-student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu menggunakan kotak centang (checkbox).');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            const radio = document.querySelector(`#manual-attendance-form input[name="statuses[${studentId}]"][value="${status}"]`);
            if (radio) {
                radio.checked = true;
                radio.dataset.wasChecked = 'true';
                const row = cb.closest('.extra-student-row');
                if (row) {
                    row.classList.add('bg-cyan-50/80', 'dark:bg-cyan-950/40');
                    setTimeout(() => row.classList.remove('bg-cyan-50/80', 'dark:bg-cyan-950/40'), 800);
                }
            }
        });
    }

    function clearExtraStatusesForSelected() {
        const checkedBoxes = document.querySelectorAll('.extra-manual-student-checkbox:checked');
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

    // Allow clicking an already-checked radio to deselect/uncheck
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#manual-attendance-form input.manual-status-radio').forEach(radio => {
            radio.dataset.wasChecked = radio.checked ? 'true' : 'false';
            radio.addEventListener('click', function() {
                if (this.dataset.wasChecked === 'true') {
                    this.checked = false;
                    this.dataset.wasChecked = 'false';
                } else {
                    const group = document.querySelectorAll(`input.manual-status-radio[name="${this.name}"]`);
                    group.forEach(r => r.dataset.wasChecked = 'false');
                    this.dataset.wasChecked = 'true';
                }
            });
        });
    });
</script>

@include('extracurricular.partials.print_modal')
@endsection
