@extends('layouts.app')

@section('title', 'Presensi Utama')

@section('content')
<div class="space-y-5">
    @include('attendance.partials.nav')

    {{-- Banner Notifikasi Cetak Surat Resmi (Pulang Cepat / Izin Keluar Sekolah) --}}
    @if(session('print_url'))
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-2xl shrink-0">
                    📄
                </div>
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white text-amber-900 mb-1">
                        Dokumen Resmi Diterbitkan
                    </span>
                    <h3 class="font-extrabold text-sm sm:text-base leading-snug">{{ session('success') }}</h3>
                    <p class="text-xs text-amber-100 mt-0.5">Surat telah dibuat menggunakan format resmi sekolah dan Kop Surat dari Master Data.</p>
                </div>
            </div>
            <a href="{{ session('print_url') }}" target="_blank"
               class="px-5 py-2.5 bg-white hover:bg-amber-50 text-amber-900 font-extrabold rounded-xl text-xs sm:text-sm shadow-md flex items-center gap-2 whitespace-nowrap active:scale-95 transition">
                <span>🖨️ Cetak Dokumen Sekarang</span>
                <span class="text-base">&nearr;</span>
            </a>
        </div>
    @elseif(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-medium flex items-center gap-3">
            <span class="text-lg">✅</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-medium flex items-center gap-3">
            <span class="text-lg">⚠️</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-md bg-gradient-to-tr from-emerald-600 to-teal-500 shadow-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Presensi Utama Siswa</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>Target: <strong class="text-slate-700 dark:text-slate-200">Semua Siswa</strong></span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px]">
                        🟢 Sesi Datang: {{ $schedule['in_start'] }} - {{ $schedule['in_late_cutoff'] }} WIB (Tepat Waktu s.d. {{ $schedule['in_on_time'] }})
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px]">
                        🔵 Sesi Pulang: {{ $schedule['out_start'] }} - {{ $schedule['out_end'] }} WIB
                    </span>
                    @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                        <a href="{{ route('master.settings') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-[10px] bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800 transition shadow-xs inline-flex items-center gap-1 font-bold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Pengaturan & Geofence
                        </a>
                    @endif
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px]">
                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $selectedDateFormatted }}</span>
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- 1. Izin Keluar Sekolah -->
            <button type="button" onclick="openSchoolPassModal()" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-500 text-white transition flex items-center gap-1.5 shadow-xs shadow-teal-600/20 active:scale-95 cursor-pointer"
               title="Buka Form Izin Keluar Lingkungan Sekolah (Lengkap sampai Cetak Dokumen)">
                <span>🚪</span>
                <span>Izin Keluar Sekolah</span>
                @if(($pendingExitPermitsCount ?? 0) > 0)
                    <span class="w-4 h-4 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center">{{ $pendingExitPermitsCount }}</span>
                @endif
            </button>

            <!-- 2. Izin Pulang Cepat -->
            <button type="button" onclick="openEarlyLeaveModal()" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-amber-950 font-black transition flex items-center gap-1.5 shadow-xs shadow-amber-500/20 active:scale-95 cursor-pointer"
               title="Buka Form Izin Pulang Cepat Siswa (Lengkap sampai Cetak Dokumen)">
                <span>🏃</span>
                <span>Izin Pulang Cepat</span>
                @if(($pendingEarlyLeavesCount ?? 0) > 0)
                    <span class="w-4 h-4 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center">{{ $pendingEarlyLeavesCount }}</span>
                @endif
            </button>

            <!-- 3. Jadwal Petugas Piket -->
            <button type="button" onclick="openPicketScheduleModal()" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center gap-1.5 shadow-xs shadow-indigo-600/20 active:scale-95 cursor-pointer"
               title="Lihat & Kelola Jadwal Petugas Piket (Guru & TU Piket Harian)">
                <span>📋</span>
                <span>Jadwal Petugas Piket</span>
                @if(isset($todayPickets) && $todayPickets->isNotEmpty())
                    <span class="px-1.5 py-0.2 rounded-md bg-indigo-900/60 text-indigo-200 text-[10px] font-mono font-bold">{{ $todayPickets->count() }}</span>
                @endif
            </button>

            <!-- 4. Laporan & Persetujuan -->
            <button type="button" onclick="scrollToPermitsSection()" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition flex items-center gap-1.5 shadow-xs active:scale-95 cursor-pointer"
               title="Lihat Laporan, Persetujuan & Cetak Dokumen Surat Resmi">
                <span>📑</span>
                <span>Laporan & Persetujuan</span>
            </button>

            @if(!Auth::user()->isStudent())
            <!-- 5. Cetak Rekap Kelas (Tanpa Nama) -->
            <a href="{{ route('reports.print.class_recap', ['start_date' => $selectedDate, 'end_date' => $selectedDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-sky-500 hover:bg-sky-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                <span>📊 Cetak Rekap Kelas (Tanpa Nama)</span>
            </a>

            <!-- 6. Dokumen Laporan -->
            <a href="{{ route('attendance.report') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Dokumen Laporan</span>
            </a>

            <!-- 7. Cetak Rekap & Kartu -->
            <a href="{{ route('attendance.cards.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white transition flex items-center gap-1.5 shadow-xs shadow-purple-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Cetak Rekap & Kartu</span>
            </a>
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
                            Waktu Sistem: <strong>{{ $timeValidation['current_day_date'] }}</strong>
                        </span>
                    </div>
                    <p class="text-xs mt-1 leading-relaxed">
                        @if($timeValidation['allowed'])
                            Sesi saat ini <strong>sedang berlangsung</strong>: 
                            <strong class="uppercase text-emerald-700 dark:text-emerald-300">
                                @if($timeValidation['session_phase'] === 'MASUK_TEPAT_WAKTU') SESI MASUK TEPAT WAKTU ({{ $schedule['in_start'] }} - {{ $schedule['in_on_time'] }} WIB)
                                @elseif($timeValidation['session_phase'] === 'MASUK_TERLAMBAT') SESI MASUK TERLAMBAT ({{ $schedule['in_on_time'] }} - {{ $schedule['in_late_cutoff'] }} WIB)
                                @elseif($timeValidation['session_phase'] === 'PULANG') SESI PULANG SEKOLAH ({{ $schedule['out_start'] }} - {{ $schedule['out_end'] }} WIB)
                                @else SESI AKTIF @endif
                            </strong>. Siswa dapat memindai kartu RFID / QR Code atau mengambil foto selfie kamera langsung.
                        @else
                            @if(!empty($timeValidation['is_holiday']))
                                <strong>Hari ini sekolah libur ({{ $timeValidation['holiday_name'] ?? 'Libur Nasional' }}).</strong> Presensi otomatis ditutup.
                            @elseif(empty($timeValidation['is_active_day']))
                                <strong>Hari ini bukan merupakan hari aktif sekolah.</strong> Presensi otomatis ditutup.
                            @else
                                <strong>Sesi Presensi Belum Dibuka / Telah Berakhir.</strong> Presensi diizinkan pada jam masuk [{{ $schedule['in_start'] }} - {{ $schedule['in_late_cutoff'] }} WIB] dan jam pulang [{{ $schedule['out_start'] }} - {{ $schedule['out_end'] }} WIB]. Di luar jam tersebut sistem otomatis menolak log.
                            @endif
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
    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-indigo-100 dark:border-indigo-800/50 shadow-xs relative overflow-hidden">
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
                    <h2 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                        Presensi Siswa Kelas {{ $homeroomClass->name }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Pemantauan khusus untuk kelas binaan Anda. Anda juga dapat menyetujui ajuan izin atau mengisi absen manual rombel.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Statistics Cards (Horizontal Layout Sesuai Shalat Dzuhur) -->
    @if(!Auth::user()->isStudent())
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Siswa</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalTarget) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">{{ $selectedClassId ? 'Siswa Terpilih' : 'Semua Siswa Aktif' }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 dark:border-emerald-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalHadir) }}</div>
            <span class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium">Tepat: {{ $totalTepatWaktu }} • Terlambat: {{ $totalTerlambat }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 dark:border-amber-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Izin / Sakit / Pulang</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalIzinSakit + $totalPulangCepat) }}</div>
            <span class="text-[10px] text-amber-600/80 dark:text-amber-400/80 font-medium">Izin: {{ $totalIzinSakit }} • Pulang: {{ $totalPulangCepat }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-500/20 dark:border-rose-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Alpa</span>
            <div class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($totalAlpa) }}</div>
            <span class="text-[10px] text-rose-600/80 dark:text-rose-400/80 font-medium">Tanpa Keterangan</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Belum Hadir</span>
            <div class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ number_format($totalBelum) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Belum scan / nihil</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-indigo-500/20 dark:border-indigo-500/30 shadow-2xs">
            <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Tingkat Kehadiran</span>
            <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $percentHadir }}%</div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $percentHadir) }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Interactive Tabs Navigation (Persis Seperti Shalat Dzuhur, Mempertahankan Setting Awal) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 dark:border-slate-800 p-2 sm:p-2.5 bg-slate-50/50 dark:bg-slate-950/40">
            @if(!$isStudent)
            <button onclick="switchTab('tab-scanner')" id="btn-tab-scanner" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'scanner' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Terminal Gerbang Sekolah</span>
            </button>
            @endif

            <button onclick="switchTab('tab-selfie')" id="btn-tab-selfie" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'selfie' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Presensi Kamera Selfie</span>
            </button>

            @if($canRecordManual)
            <button onclick="switchTab('tab-manual')" id="btn-tab-manual" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'manual' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Absen Manual</span>
            </button>
            @endif

            <button onclick="switchTab('tab-leave')" id="btn-tab-leave" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'leave' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Pengajuan Izin & Sakit</span>
            </button>

            @if($canApproveLeaves)
            <button onclick="switchTab('tab-approvals')" id="btn-tab-approvals" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'approvals' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Persetujuan Izin & Sakit</span>
                @if($pendingLeaves->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">{{ $pendingLeaves->count() }}</span>
                @endif
            </button>
            @endif

            <button onclick="switchTab('tab-history')" id="btn-tab-history" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 {{ $activeTab === 'history' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Presensi ({{ $selectedDate === $today ? 'Hari Ini' : \Carbon\Carbon::parse($selectedDate)->locale('id')->translatedFormat('d M Y') }}) ({{ $dayAttendances->count() }})</span>
            </button>
        </div>

        @if(!$isStudent)
        <!-- ========================================== -->
        <!-- TAB 1: TERMINAL SCANNER GERBANG SEKOLAH    -->
        <!-- (Barcode Scanner, RFID & Face Recognition) -->
        <!-- ========================================== -->
        <div id="tab-scanner" class="{{ $activeTab === 'scanner' ? '' : 'hidden' }} p-4 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Scanner Input Card -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                        <div class="relative z-10 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                        Terminal Gerbang Siap (Datang & Pulang)
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10.5px] font-bold">
                                        2 Sesi: Masuk [{{ $schedule['in_start'] }}] • Pulang [{{ $schedule['out_start'] }}]
                                    </span>
                                </div>
                                <!-- Status 5 Metode Presensi -->
                                <div class="flex items-center gap-1 flex-wrap text-[10px]">
                                    <span class="px-1.5 py-0.5 rounded {{ $enableSelfie ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-400' }}">Selfie: {{ $enableSelfie ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableBarcode ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400' }}">Barcode: {{ $enableBarcode ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableRfid ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400' }}">RFID: {{ $enableRfid ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableFaceRecognition ? 'bg-cyan-500/20 text-cyan-300' : 'bg-slate-700 text-slate-400' }}">Face AI: {{ $enableFaceRecognition ? 'Aktif' : 'Off' }}</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $enableManual ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-400' }}">Manual: {{ $enableManual ? 'Aktif' : 'Off' }}</span>
                                </div>
                            </div>

                            <!-- Pilihan Gerbang Metode Terminal Sekolah -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Pilih Gerbang Sensor / Mode Pemindaian Terminal:
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" onclick="setScannerGate('barcode')" id="btn-gate-barcode" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableBarcode ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">1. Barcode Scanner</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableBarcode ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Scan Barcode Kartu Siswa / NISN</span>
                                    </button>
                                    <button type="button" onclick="setScannerGate('rfid')" id="btn-gate-rfid" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableRfid ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">2. Kartu RFID</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableRfid ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Tap Kartu RFID / Smart Card</span>
                                    </button>
                                    <button type="button" onclick="setScannerGate('face')" id="btn-gate-face" class="gate-btn p-2.5 rounded-xl border text-left transition flex flex-col gap-0.5 {{ $enableFaceRecognition ? 'border-indigo-500/60 bg-indigo-950/40 text-white' : 'border-slate-800 bg-slate-900/40 text-slate-500 opacity-60' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold">3. Face Recognition</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ $enableFaceRecognition ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Terminal AI Deteksi Wajah</span>
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
                                        <option value="GATE-01">Gerbang Utama Sekolah (Default Terpadu GATE-01)</option>
                                    @endforelse
                                </select>
                                <p class="text-[10px] text-slate-400">Terminal hardware gerbang mencatat ke laporan presensi terpadu tanpa tumpang tindih.</p>
                            </div>

                            <form id="scanner-form" onsubmit="handleScannerSubmit(event)" class="space-y-3">
                                <label for="scan-input" id="scan-input-label" class="block text-xs text-slate-300 font-medium">Scan Barcode Kartu Pelajar atau Masukkan NISN:</label>
                                <div class="relative">
                                    <input type="text" id="scan-input" autofocus autocomplete="off" placeholder="Arahkan scanner ke barcode atau tap kartu RFID..."
                                        class="w-full px-4 py-3.5 pl-12 bg-slate-900/90 border-2 border-indigo-500/70 focus:border-indigo-400 rounded-2xl text-base font-mono text-white placeholder-slate-500 outline-none shadow-inner">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400" id="scan-icon-container">
                                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Tekan <strong>ENTER</strong> jika menggunakan barcode scanner hardware / reader</span>
                                    <button type="submit" id="btn-submit-scanner" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition cursor-pointer">Kirim Log Scan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Scan Feedback Box -->
                    <div id="scan-feedback-box" class="hidden p-5 rounded-2xl border transition-all duration-300"></div>
                </div>

                <!-- Live Scanned Stream (Aktivitas Scan Realtime) -->
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
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold 
                                        @if($log->status === 'HADIR') bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400
                                        @elseif($log->status === 'TERLAMBAT') bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400
                                        @else bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 @endif">
                                        {{ substr($log->time ?? '00:00', 0, 5) }} WIB ({{ $log->status }})
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

        <!-- ========================================== -->
        <!-- TAB 2: PRESENSI KAMERA SELFIE & GPS        -->
        <!-- ========================================== -->
        <div id="tab-selfie" class="{{ $activeTab === 'selfie' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="max-w-xl mx-auto space-y-4">
                <div class="text-center">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi Selfie Kamera Langsung</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Wajib menggunakan kamera aktif perangkat dan berada di dalam radius geofence sekolah.</p>
                </div>

                <!-- Geofencing & Method Synchronization Info -->
                <div class="bg-indigo-50/70 dark:bg-indigo-950/40 p-3.5 rounded-2xl border border-indigo-200/70 dark:border-indigo-800/70 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Radius & GPS Geofence Presensi Harian
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $enableSelfie ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                            Selfie: {{ $enableSelfie ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                        Radius Maksimal: <strong>{{ $geofenceRadius }} meter</strong> dari titik sekolah ({{ $schoolLat }}, {{ $schoolLng }}).
                    </p>
                    <div id="selfie-gps-status" class="text-[10px] text-slate-500 font-mono">
                        GPS: Menunggu koordinat perangkat...
                    </div>
                </div>

                <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden bg-slate-950 aspect-4/3 border-2 border-indigo-500/40 shadow-xl flex items-center justify-center">
                    <video id="selfie-video" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="selfie-canvas" class="hidden"></canvas>
                    <div id="camera-loading" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center text-white gap-2">
                        <svg class="w-8 h-8 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span class="text-xs font-semibold">Kamera Belum Aktif</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="button" onclick="startCamera()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-300 dark:hover:bg-slate-700 transition">
                        Nyalakan Kamera
                    </button>
                    <button type="button" onclick="captureAndSubmitSelfie()" id="btn-take-selfie" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ambil Foto & Simpan Presensi Harian</span>
                    </button>
                </div>

                <div id="selfie-feedback-box" class="hidden p-4 rounded-2xl border text-xs"></div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: ABSEN MANUAL PER KELAS              -->
        <!-- ========================================== -->
        @if($canRecordManual)
        <div id="tab-manual" class="{{ $activeTab === 'manual' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi Manual Kelas</h3>
                    <p class="text-xs text-slate-500">Tentukan status kehadiran harian siswa rombel secara massal.</p>
                </div>

                <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="tab" value="manual">
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
            <form id="manual-attendance-form" action="{{ route('attendance.manual.bulk') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClassId ?: $targetStudents->first()->class_id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="flex items-center justify-between py-2 border-b border-slate-200/80 dark:border-slate-800 flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-slate-500">Menampilkan <strong>{{ $targetStudents->count() }}</strong> Siswa</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                            📅 {{ $selectedDateFormatted }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="relative inline-block text-left" id="bulk-dropdown-container">
                            <button type="button" onclick="toggleBulkDropdown()" class="text-xs px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                                <span>⚡ Tandai Semua...</span>
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="bulk-dropdown-menu" class="hidden absolute right-0 mt-1.5 w-44 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl z-30 py-1 text-xs">
                                <button type="button" onclick="setAllManualStatus('HADIR'); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-2">
                                    <span>✓</span><span>Semua HADIR</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('TERLAMBAT'); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-amber-50 dark:hover:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-bold flex items-center gap-2">
                                    <span>⏰</span><span>Semua TERLAMBAT</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('IZIN'); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-sky-50 dark:hover:bg-sky-950/50 text-sky-700 dark:text-sky-300 font-bold flex items-center gap-2">
                                    <span>📝</span><span>Semua IZIN</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('SAKIT'); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-purple-50 dark:hover:bg-purple-950/50 text-purple-700 dark:text-purple-300 font-bold flex items-center gap-2">
                                    <span>🏥</span><span>Semua SAKIT</span>
                                </button>
                                <button type="button" onclick="setAllManualStatus('ALPA'); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-700 dark:text-rose-300 font-bold flex items-center gap-2">
                                    <span>❌</span><span>Semua ALPA</span>
                                </button>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <button type="button" onclick="resetAllManualStatus(); closeBulkDropdown();" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-bold flex items-center gap-2">
                                    <span>🗑️</span><span>Kosongkan Pilihan</span>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="setAllManualStatus('HADIR')" class="text-xs px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 transition">
                            ✓ Semua Hadir
                        </button>
                        <button type="button" onclick="resetAllManualStatus()" class="text-xs px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold transition">
                            Kosongkan
                        </button>
                        <button type="submit" class="text-xs px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi</span>
                        </button>
                    </div>
                </div>

                <!-- Bilah Aksi Massal Pilihan Siswa (Multi-Select Checkboxes Toolbar) -->
                <div class="p-3 bg-gradient-to-r from-indigo-50/90 via-slate-50 to-indigo-50/90 dark:from-indigo-950/40 dark:via-slate-900/40 dark:to-indigo-950/40 rounded-2xl border border-indigo-200/70 dark:border-indigo-800/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer font-extrabold text-indigo-950 dark:text-indigo-200 select-none">
                            <input type="checkbox" id="check-all-students" onchange="toggleSelectAllStudents(this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700">
                            <span>Pilih Semua Siswa</span>
                        </label>
                        <span id="selected-students-count" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300 transition">
                            0 Terpilih
                        </span>
                        <button type="button" onclick="selectUnmarkedStudents()" class="text-[10.5px] px-2 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            ⚡ Pilih Belum Absen
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mr-0.5">Terapkan ke Siswa Terpilih:</span>
                        <button type="button" onclick="setStatusesForSelected('HADIR')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>✓</span><span>HADIR</span>
                        </button>
                        <button type="button" onclick="setStatusesForSelected('TERLAMBAT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>⏰</span><span>TERLAMBAT</span>
                        </button>
                        <button type="button" onclick="setStatusesForSelected('IZIN')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>📝</span><span>IZIN</span>
                        </button>
                        <button type="button" onclick="setStatusesForSelected('SAKIT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>🏥</span><span>SAKIT</span>
                        </button>
                        <button type="button" onclick="setStatusesForSelected('PULANG_CEPAT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>🏠</span><span>PULANG</span>
                        </button>
                        <button type="button" onclick="setStatusesForSelected('ALPA')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>❌</span><span>ALPA</span>
                        </button>
                        <button type="button" onclick="clearStatusesForSelected()" class="text-[11px] font-bold px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition cursor-pointer" title="Batalkan pilihan siswa tercentang">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($targetStudents as $idx => $st)
                        @php
                            $att = $attendanceByStudentId->get($st->id);
                            $currentStatus = $att ? $att->status : null;
                        @endphp
                        <div class="student-row py-2.5 px-2 rounded-xl transition flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" class="student-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700" data-student-id="{{ $st->id }}" data-has-status="{{ $att ? '1' : '0' }}" onchange="updateSelectedStudentsCounter()">
                                    <span class="text-xs text-slate-400 font-mono w-6 text-right">{{ $idx + 1 }}.</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $st->name }}</div>
                                        @if($att)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black
                                                @if($att->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                @elseif($att->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                                @elseif(in_array($att->status, ['IZIN', 'SAKIT', 'UDZUR', 'PULANG_CEPAT'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                                @if($att->status === 'ALPA' && $att->requested_status && $att->verification_status === 'PERLU_VERIFIKASI')
                                                    ALPA (Ajuan {{ $att->requested_status }} Belum Diapprove)
                                                @elseif($att->status === 'ALPA')
                                                    Tercatat: ALPA (Otomatis)
                                                @else
                                                    Tercatat: {{ $att->status }}
                                                @endif
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
                                @foreach(['HADIR' => 'bg-emerald-500', 'TERLAMBAT' => 'bg-amber-500', 'IZIN' => 'bg-sky-500', 'SAKIT' => 'bg-yellow-500', 'PULANG_CEPAT' => 'bg-indigo-500', 'ALPA' => 'bg-rose-500'] as $stKey => $stBg)
                                    <label class="text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer flex items-center gap-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <input type="radio" name="attendances[{{ $st->id }}][status]" value="{{ $stKey }}" {{ $currentStatus === $stKey ? 'checked' : '' }} class="manual-status-radio text-indigo-600 focus:ring-0">
                                        <span>{{ $stKey === 'PULANG_CEPAT' ? 'PULANG' : $stKey }}</span>
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

        <!-- ========================================== -->
        <!-- TAB 4: PENGAJUAN IZIN, SAKIT & PULANG      -->
        <!-- ========================================== -->
        <div id="tab-leave" class="{{ $activeTab === 'leave' ? '' : 'hidden' }} p-4 sm:p-6 space-y-6">
            <!-- Header Notice -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                        FORM AJUAN RESMI
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Ajuan Izin & Sakit Siswa (Tidak Masuk Sekolah)</h2>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                    Pengajuan izin atau sakit bagi siswa yang <strong>tidak dapat hadir/masuk sekolah</strong>. Siswa/orang tua <strong>wajib mengambil foto selfie langsung melalui kamera perangkat</strong> dan mengaktifkan GPS (Bebas radius sekolah). Untuk siswa yang hadir di sekolah dan perlu izin keluar / pulang mendahului jadwal KBM, silakan gunakan menu <strong>🚪 Izin Keluar Sekolah</strong> di bagian atas.
                </p>
            </div>

            <!-- Information Box: Aturan Jam Pengajuan Izin & Sakit (Hanya Izin & Sakit Tidak Masuk Sekolah) -->
            <div class="p-4 sm:p-5 rounded-3xl bg-indigo-950/40 border border-indigo-500/20 shadow-sm text-xs text-slate-300 space-y-2.5">
                <div class="flex items-center gap-2 font-bold text-sm text-white">
                    <span class="text-amber-400 text-base">⏰</span>
                    <span>Ketentuan Jam Pengajuan Izin & Sakit:</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="font-bold text-emerald-400 mb-1 flex items-center gap-1.5">
                            <span>📅</span> Jauh Hari (Malam / H-1)
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Dapat diajukan kapan saja untuk tanggal yang akan datang (bebas tanpa batasan jam).
                        </p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="font-bold text-amber-400 mb-1 flex items-center gap-1.5">
                            <span>☀️</span> Hari H (Maks. 06:30 WIB)
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Pengajuan izin/sakit hari H ditutup pukul <strong>06:30 WIB</strong> pagi sebelum jam presensi masuk dibuka.
                        </p>
                    </div>
                </div>
            </div>

            @if(!empty($todayHoliday))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-900 dark:text-rose-200 flex items-center gap-3.5 shadow-xs">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                    📅
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-sm text-rose-700 dark:text-rose-300">Hari Libur Resmi: {{ $todayHoliday->name }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 text-rose-800 uppercase">{{ $todayHoliday->type }}</span>
                    </div>
                    <p class="text-xs text-rose-800 dark:text-rose-300 mt-0.5">Hari ini sekolah diliburkan. Siswa tidak perlu mengajukan izin atau sakit untuk hari libur resmi.</p>
                </div>
            </div>
            @endif

            <!-- Main Leave Form Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <form id="leave-form" action="{{ route('attendance.leave.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Hidden Inputs for Camera Stream & GPS Coordinates -->
                    <input type="hidden" name="selfie_image" id="tab_leave_selfie_image" required>
                    <input type="hidden" name="latitude" id="tab_leave_latitude">
                    <input type="hidden" name="longitude" id="tab_leave_longitude">
                    <input type="hidden" name="accuracy" id="tab_leave_accuracy">

                    @if(!Auth::user()->isStudent())
                    <div class="relative" id="leave-student-combobox">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Pilih Siswa yang Mengajukan <span class="text-rose-500">*</span>
                            <span class="text-[10px] lowercase font-normal text-slate-400 dark:text-slate-500 ml-1">
                                (ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                            </span>
                        </label>
                        <input type="hidden" name="student_id" id="leave_student_id" required value="{{ old('student_id', $student?->id ?? '') }}">

                        @php
                            $defaultSelectedStudent = null;
                            if (old('student_id')) {
                                $defaultSelectedStudent = $targetStudents->firstWhere('id', old('student_id'));
                            } elseif ($student) {
                                $defaultSelectedStudent = $targetStudents->firstWhere('id', $student->id);
                            }
                        @endphp
                        <!-- Selected Preview -->
                        <div id="leave-student-selected-preview" class="{{ $defaultSelectedStudent ? '' : 'hidden' }} mb-2 p-3 bg-indigo-50/90 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-2xl flex items-center justify-between transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div id="leave-student-preview-avatar" class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ $defaultSelectedStudent ? strtoupper(substr($defaultSelectedStudent->initials ?: $defaultSelectedStudent->name, 0, 2)) : '-' }}
                                </div>
                                <div class="min-w-0">
                                    <div id="leave-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm">
                                        {{ $defaultSelectedStudent?->name ?? '' }}
                                    </div>
                                    <div id="leave-student-preview-meta" class="text-[11px] text-indigo-700 dark:text-indigo-300 truncate">
                                        Kelas {{ $defaultSelectedStudent?->schoolClass?->name ?? 'Umum' }} • NIS: {{ $defaultSelectedStudent?->nis ?? '-' }} • [{{ strtoupper($defaultSelectedStudent?->initials ?? '') }}]
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="clearLeaveStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-950/50 rounded-lg transition shrink-0 cursor-pointer">
                                Ganti Siswa ✕
                            </button>
                        </div>

                        <!-- Search Input with Icon -->
                        <div class="relative {{ $defaultSelectedStudent ? 'hidden' : '' }}" id="leave-student-search-wrapper">
                            <input type="text" id="leave_student_search_input"
                                placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa (misal: MR, AF, Dani, XI-IPA)..."
                                autocomplete="off"
                                onfocus="openLeaveStudentDropdown()"
                                oninput="filterLeaveStudents(this.value)"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 text-xs text-slate-800 dark:text-slate-100 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <button type="button" id="leave-student-search-clear" onclick="resetLeaveStudentSearch()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                ✕
                            </button>
                        </div>

                        <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                        <div class="mt-2 {{ $defaultSelectedStudent ? 'hidden' : '' }} flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="leave-initials-bar">
                            <button type="button" onclick="filterLeaveByInitial('ALL')"
                                class="leave-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                                Semua
                            </button>
                            @foreach(range('A', 'Z') as $char)
                                <button type="button" onclick="filterLeaveByInitial('{{ $char }}')"
                                    class="leave-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                                    {{ $char }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Dropdown Result List -->
                        <div id="leave-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($targetStudents as $st)
                                @php
                                    $inits = $st->initials;
                                    $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                                @endphp
                                <div class="leave-student-item px-3.5 py-2.5 hover:bg-indigo-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                                     data-id="{{ $st->id }}"
                                     data-name="{{ $st->name }}"
                                     data-nis="{{ $st->nis }}"
                                     data-class="{{ $st->schoolClass?->name ?? 'Umum' }}"
                                     data-initials="{{ strtolower($inits) }}"
                                     data-avatar="{{ strtoupper(substr($inits ?: $st->name, 0, 2)) }}"
                                     data-firstletter="{{ $firstChar }}"
                                     onclick="selectLeaveStudent(this)">
                                    <div class="flex items-center gap-2.5 truncate mr-2">
                                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($inits ?: $st->name, 0, 2)) }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $st->name }}</div>
                                            <div class="text-[10px] text-slate-400">Kelas {{ $st->schoolClass?->name ?? 'Umum' }} • NIS: {{ $st->nis }}</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono shrink-0 font-bold">
                                        [{{ strtoupper($inits) }}]
                                    </span>
                                </div>
                            @endforeach
                            <div id="leave-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                                Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="student_id" value="{{ Auth::user()->student?->id }}">
                    @endif

                    <!-- Jenis Ajuan & Tanggal -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Jenis Pengajuan <span class="text-rose-500">* (Izin / Sakit Tidak Masuk Sekolah)</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="relative flex items-center justify-center p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-indigo-600 has-checked:bg-indigo-50/60 dark:has-checked:bg-indigo-950/40 has-checked:text-indigo-900 dark:has-checked:text-indigo-200 border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300">
                                    <input type="radio" name="status" value="IZIN" class="sr-only leave-status-radio" checked onchange="checkTabLeaveFormReady()">
                                    <div class="flex items-center gap-2 font-bold text-sm">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span>Izin</span>
                                    </div>
                                </label>
                                <label class="relative flex items-center justify-center p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-indigo-600 has-checked:bg-indigo-50/60 dark:has-checked:bg-indigo-950/40 has-checked:text-indigo-900 dark:has-checked:text-indigo-200 border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300">
                                    <input type="radio" name="status" value="SAKIT" class="sr-only leave-status-radio" onchange="checkTabLeaveFormReady()">
                                    <div class="flex items-center gap-2 font-bold text-sm">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                        <span>Sakit</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="tab_leave_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Tanggal Pengajuan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="date" id="tab_leave_date" value="{{ date('Y-m-d') }}" required onchange="checkTabLeaveFormReady()"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition text-slate-900 dark:text-white">
                        </div>

                        <!-- Alert Validasi Jam & Tanggal Cutoff 06:30 -->
                        <div id="tab-leave-cutoff-warning-alert" class="hidden p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-xs text-rose-900 dark:text-rose-200 flex items-start gap-3">
                            <span class="text-rose-500 font-bold text-base shrink-0">⚠️</span>
                            <div>
                                <strong class="font-bold text-rose-950 dark:text-rose-200">Batas Waktu Pengajuan Hari H Ditutup:</strong>
                                <p class="mt-0.5 leading-relaxed text-rose-800 dark:text-rose-300">
                                    Pengajuan izin atau sakit untuk hari ini maksimal diajukan sebelum pukul <strong>06:30 WIB</strong> (sebelum KBM dimulai).
                                    Jika siswa sudah berada di sekolah dan perlu izin keluar lingkungan sekolah atau pulang mendahului jadwal KBM, silakan gunakan menu <strong>🚪 Izin Keluar Sekolah</strong> di bagian atas.
                                    Untuk izin hari besok atau seterusnya (jauh hari), silakan tentukan tanggal ke tanggal yang akan datang.
                                </p>
                            </div>
                        </div>

                        <div id="tab-leave-advance-notice" class="hidden p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 flex items-center gap-2.5">
                            <span class="text-emerald-600 font-bold text-base">✨</span>
                            <div>
                                <strong>Pengajuan Jauh Hari Aktif:</strong> Anda mengajukan izin/sakit untuk tanggal mendatang. Pengajuan ini akan diteruskan ke Wali Kelas untuk ditinjau.
                            </div>
                        </div>

                        <!-- Info Banner jika sudah Hadir pagi ini -->
                        @if($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']))
                            <div id="tab-leave-morning-checkin-alert" class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 text-xs text-emerald-900 dark:text-emerald-200 {{ ($defaultLeaveType ?? '') === 'PULANG_CEPAT' ? '' : 'hidden' }}">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <strong>Presensi Masuk Pagi Terdeteksi:</strong> Anda sudah tercatat {{ $existingAttendance->status }} pukul {{ substr($existingAttendance->time, 0, 5) }} WIB. Data masuk pagi Anda akan tetap aman dan kepulangan cepat Anda akan diverifikasi Wali Kelas.
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Keterangan / Alasan -->
                    <div>
                        <label for="tab_leave_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Keterangan / Alasan Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="notes" id="tab_leave_notes" rows="3" required placeholder="Contoh: Mengalami demam tinggi sejak semalam dan berobat ke puskesmas / Ada keperluan keluarga mendesak..."
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition text-slate-900 dark:text-white"></textarea>
                    </div>

                    <!-- Upload Dokumen / Surat Dokter Opsional -->
                    <div>
                        <label for="tab_leave_attachment" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Lampiran Surat Dokter / Surat Izin Orang Tua (Opsional)
                        </label>
                        <div class="relative flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl">
                            <input type="file" name="attachment" id="tab_leave_attachment" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Format diperbolehkan: PDF, JPG, PNG (Maks. 5MB). Lampiran ini membantu percepatan verifikasi oleh wali kelas.</p>
                    </div>

                    <!-- Live GPS Status Bar -->
                    <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-slate-800/60 border border-indigo-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div id="tab-leave-gps-indicator" class="w-3.5 h-3.5 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200" id="tab-leave-gps-status-text">Mendeteksi Posisi GPS Realtime...</span>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400" id="tab-leave-gps-coords-text">Mohon izinkan akses lokasi pada browser</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-bold text-center text-[11px]">
                            ✓ Bebas Batas Radius
                        </span>
                    </div>

                    <!-- Live Camera Section (Strict Rule: Direct Camera Only) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Foto Selfie Langsung Kamera <span class="text-rose-500">* (Dilarang Dari Galeri)</span>
                        </label>

                        <div class="relative w-full max-w-md mx-auto aspect-4/3 bg-slate-950 rounded-3xl overflow-hidden shadow-xl border-4 border-slate-900 flex items-center justify-center">
                            <!-- Live Video Element -->
                            <video id="tab-leave-video" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                            
                            <!-- Captured Image Preview -->
                            <img id="tab-leave-preview" class="hidden w-full h-full object-cover" alt="Preview Foto Selfie Langsung">

                            <!-- Frame Canvas (hidden) -->
                            <canvas id="tab-leave-canvas" class="hidden"></canvas>

                            <!-- Face Oval Guide -->
                            <div id="tab-leave-face-guide" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-4">
                                <div class="w-40 h-52 border-2 border-dashed border-emerald-400/80 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/10">
                                    <span class="text-[10px] font-bold text-emerald-300 bg-slate-950/70 px-2.5 py-1 rounded-full backdrop-blur-xs">
                                        Posisikan Wajah Siswa
                                    </span>
                                </div>
                            </div>

                            <!-- Camera Starter Overlay -->
                            <div id="tab-leave-prompt" class="absolute inset-0 bg-slate-950/90 backdrop-blur-xs flex flex-col items-center justify-center p-6 text-center text-white">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-sm mb-1">Kamera Selfie Langsung</h3>
                                <p class="text-xs text-slate-400 mb-4 max-w-xs">
                                    Sistem memerlukan foto wajah terkini sebagai bukti kehadiran/kondisi hari ini.
                                </p>
                                <button type="button" onclick="startTabLeaveCamera()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg transition cursor-pointer">
                                    Nyalakan Kamera
                                </button>
                            </div>
                        </div>

                        <!-- Camera Controls -->
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                            <button type="button" id="btn-tab-leave-snap" onclick="takeTabLeaveSnapshot()" disabled
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl text-xs shadow-md transition inline-flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                Jepret Foto Selfie
                            </button>

                            <button type="button" id="btn-tab-leave-retake" onclick="retakeTabLeaveSnapshot()" class="hidden px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition cursor-pointer">
                                Ambil Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" id="btn-tab-leave-submit" disabled
                            class="w-full py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            KIRIM PENGAJUAN SEKARANG
                        </button>
                        <p class="text-center text-[11px] text-slate-400 mt-2">
                            Ajuan akan diteruskan ke akun Wali Kelas dan Administrator untuk ditinjau dan disetujui.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Riwayat Pengajuan Izin / Sakit / Pulang Cepat Terakhir -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-4 flex items-center justify-between">
                    <span>Riwayat Ajuan Izin, Sakit & Pulang Cepat Saya</span>
                    <span class="text-xs font-semibold text-slate-500">{{ count($historyLeaves) }} Data Terakhir</span>
                </h3>

                @if(count($historyLeaves) === 0)
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat pengajuan izin, sakit, atau pulang cepat yang dicatat.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($historyLeaves as $item)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    @if($item->selfie_path)
                                        <img src="{{ asset('storage/'.$item->selfie_path) }}" alt="Foto Selfie" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0 cursor-pointer" onclick="window.open('{{ asset('storage/'.$item->selfie_path) }}', '_blank')">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-xs font-bold shrink-0">
                                            {{ $item->is_early_leave || $item->status === 'PULANG_CEPAT' ? 'P.CEPAT' : $item->status }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @php
                                                $isPc = $item->is_early_leave || $item->status === 'PULANG_CEPAT';
                                                $reqStatus = $item->requested_status ?? $item->status;
                                                $badgeColor = $reqStatus === 'SAKIT' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($isPc ? 'bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300');
                                                $badgeLabel = $isPc ? 'PULANG CEPAT' : $reqStatus;
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $badgeColor }}">
                                                AJUAN {{ $badgeLabel }}
                                            </span>
                                            @if($item->status === 'ALPA' && $item->verification_status === 'PERLU_VERIFIKASI')
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                    Status Sementara: ALPA
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                {{ \Carbon\Carbon::parse($item->date)->locale('id')->translatedFormat('l, d M Y') }} • {{ substr($item->time, 0, 5) }} WIB
                                            </span>
                                        </div>
                                        @if($isPc)
                                            <p class="text-[11px] text-orange-700 dark:text-orange-400 font-bold mt-0.5">
                                                Rencana / Jam Pulang: {{ substr($item->early_leave_time ?? $item->time_out ?? $item->time, 0, 5) }} WIB
                                                @if($item->time_out)
                                                    <span class="text-emerald-600 font-semibold">• Check-out: {{ substr($item->time_out, 0, 5) }} WIB</span>
                                                @endif
                                            </p>
                                        @endif
                                        <p class="text-xs text-slate-700 dark:text-slate-300 mt-1 font-medium">{{ $item->notes }}</p>
                                        @if($item->attachment_path)
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    <span>📎</span> Lihat Dokumen/Surat Lampiran
                                                </a>
                                            </div>
                                        @endif
                                        @if($item->rejection_reason)
                                            <p class="text-xs text-rose-600 mt-1 font-semibold">Alasan Penolakan: {{ $item->rejection_reason }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    @if($item->verification_status === 'PERLU_VERIFIKASI')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($item->verification_status === 'DIVERIFIKASI')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($item->verification_status === 'DITOLAK')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $item->verification_status }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: PERSETUJUAN IZIN & PULANG           -->
        <!-- ========================================== -->
        @if($canApproveLeaves)
        <div id="tab-approvals" class="{{ $activeTab === 'approvals' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Persetujuan Ajuan Izin & Pulang Cepat</h3>
                    <p class="text-xs text-slate-500">Verifikasi pengajuan izin dari siswa atau orang tua dengan menekan tombol setujui atau tolak.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold">
                        Menunggu Verifikasi: {{ $pendingLeaves->count() }}
                    </span>
                </div>
            </div>

            <!-- List Menunggu Persetujuan -->
            <div class="space-y-3">
                @forelse($pendingLeaves as $pl)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            @if($pl->selfie_path)
                                <img src="{{ asset('storage/' . $pl->selfie_path) }}" alt="Foto Selfie Siswa" class="w-12 h-12 rounded-xl object-cover border-2 border-indigo-200 dark:border-indigo-800 shrink-0 shadow-xs cursor-pointer" onclick="window.open('{{ asset('storage/' . $pl->selfie_path) }}', '_blank')">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center font-black text-xs shrink-0">
                                    {{ strtoupper(substr($pl->student?->name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $pl->student?->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                                        Kelas: {{ $pl->student?->schoolClass?->name ?? '-' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                        {{ $pl->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pl->date)->locale('id')->translatedFormat('l, d F Y') }} • <strong>Keterangan:</strong> {{ $pl->notes ?: 'Tidak ada catatan' }}
                                </p>
                                @if($pl->attachment_path)
                                    <div class="mt-1.5">
                                        <a href="{{ asset('storage/' . $pl->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span>Lihat Berkas Lampiran / Surat Dokter</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                            <form action="{{ route('attendance.leaves.approve', $pl->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui</span>
                                </button>
                            </form>

                            <form action="{{ route('attendance.leaves.reject', $pl->id) }}" method="POST" onsubmit="return confirm('Tolak permohonan izin ini?');" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tolak</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-xs text-slate-400 bg-slate-50/50 dark:bg-slate-950/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        Tidak ada permohonan izin atau pulang cepat yang menunggu verifikasi saat ini.
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 6: RIWAYAT PRESENSI HARI INI           -->
        <!-- ========================================== -->
        <div id="tab-history" class="{{ $activeTab === 'history' ? '' : 'hidden' }} p-4 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Presensi Terdata</h3>
                    <p class="text-xs text-slate-500">Hari & Tanggal: <strong class="text-slate-700 dark:text-slate-300">{{ $selectedDateFormatted }}</strong> ({{ $dayAttendances->count() }} Catatan)</p>
                </div>

                <form method="GET" action="{{ route('attendance.index') }}" class="flex items-center gap-2 flex-wrap">
                    <input type="hidden" name="tab" value="history">
                    @if($selectedClassId)
                        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    @endif
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-[11px] font-bold text-slate-500">Filter Tanggal:</span>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-500">Metode:</span>
                        <select name="method" onchange="this.form.submit()" class="text-xs bg-transparent text-slate-800 dark:text-slate-200 font-semibold outline-none cursor-pointer">
                            <option value="">Semua Metode</option>
                            <option value="SELFIE" {{ request('method') === 'SELFIE' ? 'selected' : '' }}>Selfie Kamera</option>
                            <option value="BARCODE" {{ in_array(request('method'), ['BARCODE', 'QR_CODE']) ? 'selected' : '' }}>Barcode Scanner</option>
                            <option value="RFID" {{ request('method') === 'RFID' ? 'selected' : '' }}>Kartu RFID</option>
                            <option value="FACE_RECOGNITION" {{ request('method') === 'FACE_RECOGNITION' ? 'selected' : '' }}>Face Recognition</option>
                            <option value="MANUAL" {{ request('method') === 'MANUAL' ? 'selected' : '' }}>Presensi Manual</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800 uppercase text-[10px] font-extrabold">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Siswa</th>
                            <th class="p-3">Kelas</th>
                            <th class="p-3">Jam Masuk</th>
                            <th class="p-3">Jam Pulang</th>
                            <th class="p-3">Metode</th>
                            <th class="p-3">Status</th>
                            @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                                <th class="p-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($dayAttendances as $i => $row)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3 text-center text-slate-400 font-mono">{{ $i + 1 }}</td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $row->student?->name ?? 'Siswa' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $row->student?->nisn ?? '-' }}</div>
                                </td>
                                <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">{{ $row->student?->schoolClass?->name ?? '-' }}</td>
                                <td class="p-3 font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $row->time ? substr($row->time, 0, 5) . ' WIB' : '-' }}
                                </td>
                                <td class="p-3 font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $row->time_out ? substr($row->time_out, 0, 5) . ' WIB' : '-' }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        @if(($row->method ?? '') === 'SELFIE') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif(in_array($row->method ?? '', ['BARCODE', 'QR_CODE'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                        @elseif(($row->method ?? '') === 'RFID') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                        @elseif(($row->method ?? '') === 'FACE_RECOGNITION') bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300
                                        @elseif(($row->method ?? '') === 'MANUAL' || ($row->method ?? '') === 'IZIN_SAKIT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                        @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif">
                                        @if(($row->method ?? '') === 'SELFIE') 📸 Selfie Kamera
                                        @elseif(in_array($row->method ?? '', ['BARCODE', 'QR_CODE'])) 🏷️ Barcode Scanner
                                        @elseif(($row->method ?? '') === 'RFID') 💳 Kartu RFID
                                        @elseif(($row->method ?? '') === 'FACE_RECOGNITION') 🤖 Face Recognition
                                        @elseif(($row->method ?? '') === 'MANUAL' || ($row->method ?? '') === 'IZIN_SAKIT') 📝 Presensi Manual
                                        @else {{ $row->method ?? 'Presensi' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black
                                        @if($row->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif($row->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                        @elseif(in_array($row->status, ['IZIN', 'SAKIT', 'UDZUR', 'PULANG_CEPAT'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                        @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                        @if($row->status === 'ALPA' && $row->requested_status && $row->verification_status === 'PERLU_VERIFIKASI')
                                            ALPA (Ajuan {{ $row->requested_status }} Belum Diapprove)
                                        @else
                                            {{ $row->status }}
                                        @endif
                                    </span>
                                </td>
                                @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="openEditModal('{{ $row->id }}', '{{ addslashes($row->student?->name ?? $row->student_name) }}', '{{ $row->status }}', '{{ $row->time ? \Carbon\Carbon::parse($row->time)->format('H:i') : '' }}', '{{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('H:i') : '' }}', '{{ addslashes($row->notes ?? '') }}')" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition inline-flex items-center gap-1 font-bold text-[10px] shadow-2xs cursor-pointer" title="Edit Hasil Presensi">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ in_array(Auth::user()->role, ['admin', 'super_admin']) ? 8 : 7 }}" class="p-8 text-center text-xs text-slate-400">
                                    Tidak ada data presensi siswa tercatat untuk tanggal {{ $selectedDateFormatted }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- PANEL TERPADU: LAPORAN & PERSETUJUAN PERIZINAN KHUSUS SISWA     -->
    <!-- (Ajuan Pulang Cepat & Izin Keluar Lingkungan Sekolah)          -->
    <!-- ============================================================== -->
    <div id="section-special-permits" class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <!-- Header Panel -->
        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-gradient-to-r from-slate-50 via-white to-amber-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-amber-950/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                        Pusat Perizinan & Surat Siswa
                    </span>
                    @if(($pendingEarlyLeavesCount ?? 0) + ($pendingExitPermitsCount ?? 0) > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white animate-pulse">
                            {{ ($pendingEarlyLeavesCount ?? 0) + ($pendingExitPermitsCount ?? 0) }} Ajuan Menunggu Persetujuan
                        </span>
                    @endif
                </div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📑 Laporan, Persetujuan & Cetak Dokumen Surat Perizinan</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Monitoring riwayat ajuan pulang cepat dan izin keluar sekolah, persetujuan (Wali Kelas / Guru Piket), serta cetak dokumen resmi ber-kop sekolah.
                </p>
            </div>

            <!-- Segmented Switcher & Quick Create Action -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center gap-1 text-xs font-bold">
                    <button type="button" onclick="switchPermitTab('early-leave')" id="btn-permit-tab-early-leave"
                        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-300 shadow-xs">
                        <span>🏃 Pulang Cepat</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ ($pendingEarlyLeavesCount ?? 0) > 0 ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200' }}">
                            {{ isset($todayEarlyLeaves) ? $todayEarlyLeaves->count() : 0 }}
                        </span>
                    </button>
                    <button type="button" onclick="switchPermitTab('school-pass')" id="btn-permit-tab-school-pass"
                        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <span>🚪 Izin Keluar Sekolah</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ ($pendingExitPermitsCount ?? 0) > 0 ? 'bg-teal-500 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200' }}">
                            {{ isset($todayExitPermits) ? $todayExitPermits->count() : 0 }}
                        </span>
                    </button>
                </div>
                <button type="button" onclick="openCurrentPermitCreateModal()"
                    class="px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer">
                    <span id="label-permit-create-btn">+ Buat Ajuan Pulang Cepat</span>
                </button>
            </div>
        </div>

        <!-- Tab Content 1: Laporan Ajuan Pulang Cepat -->
        <div id="view-permit-early-leave" class="p-5 sm:p-6 space-y-4 text-xs">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 dark:text-slate-200">Daftar Ajuan Pulang Cepat:</span>
                    <span class="text-slate-500 dark:text-slate-400">Total: <strong>{{ isset($todayEarlyLeaves) ? $todayEarlyLeaves->count() : 0 }} siswa</strong></span>
                </div>
                <a href="{{ route('attendance.early-leave.index') }}" class="font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 text-xs flex items-center gap-1 hover:underline">
                    <span>📂 Buka Manajemen & Arsip Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-3.5">No. Surat & Tanggal</th>
                            <th class="py-3 px-3.5">Identitas Siswa</th>
                            <th class="py-3 px-3.5">Jam & Alasan Pulang</th>
                            <th class="py-3 px-3.5">Penjemput</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-right">Persetujuan & Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($todayEarlyLeaves ?? [] as $el)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $el->letter_number }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $el->date ? $el->date->format('d/m/Y') : '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $el->student?->name ?? 'Siswa' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                        NIS: {{ $el->student?->nis ?? '-' }} • Kelas {{ $el->student?->schoolClass?->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[11px] border border-amber-200 dark:border-amber-800">
                                        ⏰ {{ $el->leave_time }} WIB
                                    </span>
                                    <div class="font-semibold text-slate-700 dark:text-slate-300 mt-1">{{ $el->reason_category }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $el->reason }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    @if($el->pickup_name)
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $el->pickup_name }}</div>
                                        <div class="text-[11px] text-slate-400">({{ $el->pickup_relationship ?? 'Keluarga' }}) {{ $el->pickup_phone ? '• '.$el->pickup_phone : '' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">Pulang Sendiri / Mandiri</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($el->status === 'APPROVED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($el->status === 'REJECTED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-800 animate-pulse">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        @if($el->status === 'PENDING' && ($canManagePermits ?? false))
                                            <form action="{{ route('attendance.early-leave.approve', $el->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Setujui ajuan pulang cepat dan terbitkan Surat Keterangan Resmi?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-2xs cursor-pointer flex items-center gap-1">
                                                    <span>✓</span>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('attendance.early-leave.reject', $el->id) }}" method="POST" class="inline m-0" onsubmit="return confirmRejectEarlyLeave(this)">
                                                @csrf
                                                <input type="hidden" name="reason" value="Tidak memenuhi kriteria perizinan pulang cepat">
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-[11px] transition cursor-pointer">
                                                    Tolak
                                                </button>
                                            </form>
                                        @endif

                                        @if($el->status === 'APPROVED')
                                            <a href="{{ route('attendance.early-leave.print', $el->id) }}" target="_blank"
                                                class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1"
                                                title="Cetak Surat Keterangan Pulang Cepat Ber-Kop Sekolah Resmi">
                                                <span>🖨️ Cetak Surat</span>
                                                <span class="text-xs">&nearr;</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    <div class="text-2xl mb-1">🏃</div>
                                    <div class="font-semibold">Belum ada ajuan pulang cepat yang tercatat.</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Siswa, Wali Kelas, atau Guru Piket dapat membuat ajuan pulang cepat melalui tombol di atas.</p>
                                    <button type="button" onclick="openEarlyLeaveModal('form')" class="mt-3 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs transition cursor-pointer">
                                        + Ajukan Pulang Cepat Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content 2: Laporan Izin Keluar Lingkungan Sekolah -->
        <div id="view-permit-school-pass" class="hidden p-5 sm:p-6 space-y-4 text-xs">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 dark:text-slate-200">Daftar Izin Keluar Lingkungan Sekolah:</span>
                    <span class="text-slate-500 dark:text-slate-400">Total: <strong>{{ isset($todayExitPermits) ? $todayExitPermits->count() : 0 }} perizinan</strong></span>
                </div>
                <a href="{{ route('attendance.school-pass.index') }}" class="font-bold text-teal-600 hover:text-teal-700 dark:text-teal-400 text-xs flex items-center gap-1 hover:underline">
                    <span>📂 Buka Manajemen & Arsip Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-3.5">No. Izin & Tanggal</th>
                            <th class="py-3 px-3.5">Rombongan Siswa</th>
                            <th class="py-3 px-3.5">Waktu Izin</th>
                            <th class="py-3 px-3.5">Keperluan & Pendamping</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-right">Persetujuan & Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($todayExitPermits ?? [] as $sp)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $sp->permit_number }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $sp->date ? $sp->date->format('d/m/Y') : '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 mb-1">
                                        {{ $sp->students->count() }} Siswa Diberi Izin:
                                    </div>
                                    <div class="flex flex-wrap gap-1 max-w-sm">
                                        @foreach($sp->students as $st)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-semibold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                                <span>{{ $st->name }}</span>
                                                <span class="text-[9px] text-slate-400">({{ $st->schoolClass?->name ?? '-' }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-700 dark:text-teal-300">
                                        <span>🚪 Keluar: {{ $sp->exit_time }} WIB</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        @if($sp->is_returning)
                                            Estimasi Kembali: <strong>{{ $sp->estimated_back_time }} WIB</strong>
                                        @else
                                            <span class="text-amber-600 font-semibold">Tidak Kembali (Langsung Pulang)</span>
                                        @endif
                                    </div>
                                    @if($sp->actual_back_time)
                                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">
                                            ✓ Kembali Pukul: {{ $sp->actual_back_time }} WIB
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $sp->purpose }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Pendamping: <strong>{{ $sp->companion_type ?? 'Mandiri' }}</strong>
                                        {{ $sp->companion_name ? '('.$sp->companion_name.')' : '' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($sp->status === 'APPROVED')
                                        @if($sp->is_returning && !$sp->actual_back_time)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-800 animate-pulse">
                                                🔵 Di Luar Sekolah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                                ✓ Disetujui
                                            </span>
                                        @endif
                                    @elseif($sp->status === 'RETURNED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                            ✓ Sudah Kembali
                                        </span>
                                    @elseif($sp->status === 'REJECTED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-800 animate-pulse">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        @if($sp->status === 'PENDING' && ($canManagePermits ?? false))
                                            <form action="{{ route('attendance.school-pass.approve', $sp->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Setujui izin keluar sekolah dan terbitkan Surat Izin Resmi?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] transition shadow-2xs cursor-pointer flex items-center gap-1">
                                                    <span>✓</span>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('attendance.school-pass.reject', $sp->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                <input type="hidden" name="reason" value="Izin keluar lingkungan sekolah tidak disetujui">
                                                <button type="submit" onclick="return confirm('Tolak perizinan keluar lingkungan sekolah ini?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-[11px] transition cursor-pointer">
                                                    Tolak
                                                </button>
                                            </form>
                                        @endif

                                        @if(in_array($sp->status, ['APPROVED', 'RETURNED']))
                                            <a href="{{ route('attendance.school-pass.print', $sp->id) }}" target="_blank"
                                                class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1"
                                                title="Cetak Surat Izin Keluar Lingkungan Sekolah Resmi Ber-Kop Sekolah">
                                                <span>🖨️ Cetak Surat</span>
                                                <span class="text-xs">&nearr;</span>
                                            </a>
                                        @endif

                                        @if($sp->status === 'APPROVED' && $sp->is_returning && !$sp->actual_back_time && ($canManagePermits ?? false))
                                            <form action="{{ route('attendance.school-pass.returned', $sp->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Catat konfirmasi bahwa rombongan siswa telah kembali ke lingkungan sekolah?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[11px] transition shadow-2xs cursor-pointer flex items-center gap-1"
                                                    title="Konfirmasi Siswa Sudah Kembali ke Sekolah">
                                                    <span>↩️ Kembali</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    <div class="text-2xl mb-1">🚪</div>
                                    <div class="font-semibold">Belum ada izin keluar lingkungan sekolah yang tercatat.</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Wali Kelas, Guru Piket, atau Siswa dapat membuat surat izin keluar melalui tombol di atas.</p>
                                    <button type="button" onclick="openSchoolPassModal('form')" class="mt-3 px-3.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition cursor-pointer">
                                        + Buat Izin Keluar Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Admin & Super Admin) -->
    @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
    <div id="edit-attendance-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Presensi Utama</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">Admin Mode</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="edit-modal-student-name">Nama Siswa</p>

            <form id="edit-attendance-form" method="POST" action="" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran:</label>
                    <select name="status" id="edit-modal-status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none font-bold">
                        <option value="HADIR">HADIR (Tepat Waktu)</option>
                        <option value="TERLAMBAT">TERLAMBAT</option>
                        <option value="IZIN">IZIN</option>
                        <option value="SAKIT">SAKIT</option>
                        <option value="ALPA">ALPA</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Masuk:</label>
                        <input type="time" name="time" id="edit-modal-time-in" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Pulang:</label>
                        <input type="time" name="time_out" id="edit-modal-time-out" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan Perubahan:</label>
                    <textarea name="notes" id="edit-modal-notes" rows="2" placeholder="Catatan admin..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    <!-- Modal Ajuan Pulang Cepat Siswa (Vanilla JS) -->
    <div id="quick-early-leave-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🏃</span>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base leading-tight">Ajuan Pulang Cepat Siswa</h3>
                        <p class="text-[11px] text-amber-100">Formulir ajuan, monitoring laporan, persetujuan & cetak surat resmi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="p-1 bg-black/20 rounded-xl flex items-center gap-1">
                        <button type="button" id="quick-el-tab-form-btn" onclick="switchQuickEarlyLeaveTab('form')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-amber-700 shadow-xs transition cursor-pointer">
                            ✍️ Ajuan Baru
                        </button>
                        <button type="button" id="quick-el-tab-report-btn" onclick="switchQuickEarlyLeaveTab('report')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer flex items-center gap-1">
                            <span>📋 Laporan & Persetujuan</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ ($pendingEarlyLeavesCount ?? 0) > 0 ? 'bg-rose-500 text-white' : 'bg-black/30 text-white' }}">
                                {{ isset($todayEarlyLeaves) ? $todayEarlyLeaves->count() : 0 }}
                            </span>
                        </button>
                    </div>
                    <button type="button" onclick="closeEarlyLeaveModal()" class="text-white/80 hover:text-white text-2xl font-bold leading-none cursor-pointer ml-1">&times;</button>
                </div>
            </div>

            <!-- View 1: Formulir Ajuan Baru -->
            <div id="quick-el-view-form" class="overflow-y-auto flex-1 min-h-0">
                <form action="{{ route('attendance.early-leave.store') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4 text-xs">
                    @csrf

                    @if(!Auth::user()->isStudent())
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Pilih Siswa <span class="text-rose-500">*</span>
                            </label>
                            <select name="student_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-medium">
                                <option value="">-- Pilih Siswa yang Mengajukan Pulang Cepat --</option>
                                @foreach($allStudents ?? [] as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->name }} (NIS: {{ $st->nis }}) - Kelas {{ $st->schoolClass?->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] text-amber-700 dark:text-amber-400 font-semibold">Identitas Siswa Pemohon:</p>
                                <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $student?->name ?? Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">NIS: {{ $student?->nis ?? '-' }} • Kelas {{ $student?->schoolClass?->name ?? '-' }}</p>
                            </div>
                            <span class="text-2xl">🎓</span>
                            <input type="hidden" name="student_id" value="{{ $student?->id }}">
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kepulangan <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Pulang Cepat <span class="text-rose-500">*</span></label>
                            <input type="time" name="leave_time" required value="{{ now('Asia/Jakarta')->format('H:i') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-semibold font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Alasan <span class="text-rose-500">*</span></label>
                        <select name="reason_category" required
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="Sakit di Sekolah (Rujukan UKS)">Sakit di Sekolah (Rujukan UKS)</option>
                            <option value="Urusan Keluarga Mendesak">Urusan Keluarga Mendesak / Takziyah / Berduka</option>
                            <option value="Keperluan Medis / Berobat / Terapi">Keperluan Medis / Berobat ke Rumah Sakit</option>
                            <option value="Perjalanan / Jadwal Keberangkatan Luar Kota">Perjalanan / Tiket Luar Kota</option>
                            <option value="Dispensasi Lomba / Tugas Khusus">Dispensasi Lomba / Tugas Resmi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Keterangan & Alasan <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="2" required placeholder="Jelaskan alasan detail kepulangan siswa mendahului jadwal KBM..."
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800 space-y-2.5">
                        <p class="font-bold text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                            <span>🚗</span>
                            <span>Identitas Penjemput (Orang Tua / Wali)</span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Nama Penjemput</label>
                                <input type="text" name="pickup_name" placeholder="Nama penjemput"
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Hubungan</label>
                                <select name="pickup_relationship" class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                                    <option value="Orang Tua (Ayah/Ibu)">Orang Tua (Ayah/Ibu)</option>
                                    <option value="Wali Siswa">Wali Siswa</option>
                                    <option value="Kakak / Saudara Kandung">Kakak / Saudara</option>
                                    <option value="Pulang Mandiri">Pulang Mandiri (Izin Ortu)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. HP / WA</label>
                                <input type="text" name="pickup_phone" placeholder="08xxxxxxxxxx"
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lampiran / Bukti Surat (Opsional)</label>
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                    </div>

                    @if(!Auth::user()->isStudent())
                        <div class="pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="direct_approve" value="1" checked
                                    class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Langsung Setujui & Terbitkan Surat Keterangan Resmi (Wali Kelas / Guru Piket)
                                </span>
                            </label>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="switchQuickEarlyLeaveTab('report')" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 underline flex items-center gap-1 cursor-pointer">
                            <span>📋 Lihat Laporan & Persetujuan ({{ isset($todayEarlyLeaves) ? $todayEarlyLeaves->count() : 0 }})</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="closeEarlyLeaveModal()"
                                class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-xs font-bold bg-amber-500 hover:bg-amber-600 active:scale-95 text-white rounded-xl transition shadow-sm shadow-amber-500/30 cursor-pointer">
                                Simpan & Terbitkan Surat
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- View 2: Laporan & Persetujuan -->
            <div id="quick-el-view-report" class="hidden overflow-y-auto flex-1 min-h-0 p-5 sm:p-6 space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Data Ajuan Pulang Cepat Hari Ini</span>
                        <p class="text-[11px] text-slate-400">Total: <strong>{{ isset($todayEarlyLeaves) ? $todayEarlyLeaves->count() : 0 }} ajuan</strong> tercatat</p>
                    </div>
                    <button type="button" onclick="switchQuickEarlyLeaveTab('form')" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition shadow-xs cursor-pointer">
                        + Tambah Ajuan Baru
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto space-y-2.5 pr-1">
                    @forelse($todayEarlyLeaves ?? [] as $el)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $el->student?->name ?? 'Siswa' }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $el->student?->schoolClass?->name ?? '-' }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400">({{ $el->letter_number }})</span>
                                </div>
                                <div class="text-[11px] text-amber-700 dark:text-amber-400 font-bold flex items-center gap-1.5">
                                    <span>⏰ Pulang: {{ $el->leave_time }} WIB</span>
                                    <span>•</span>
                                    <span>{{ $el->reason_category }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 italic">"{{ $el->reason }}"</p>
                                @if($el->pickup_name)
                                    <p class="text-[10px] text-slate-500">
                                        Penjemput: <strong>{{ $el->pickup_name }}</strong> ({{ $el->pickup_relationship ?? 'Keluarga' }}) {{ $el->pickup_phone ? '• '.$el->pickup_phone : '' }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0">
                                <div>
                                    @if($el->status === 'APPROVED')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($el->status === 'REJECTED')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-800 animate-pulse">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5">
                                    @if($el->status === 'PENDING' && ($canManagePermits ?? false))
                                        <form action="{{ route('attendance.early-leave.approve', $el->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Setujui ajuan pulang cepat dan terbitkan Surat Keterangan Resmi?')"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition cursor-pointer">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('attendance.early-leave.reject', $el->id) }}" method="POST" class="inline m-0" onsubmit="return confirmRejectEarlyLeave(this)">
                                            @csrf
                                            <input type="hidden" name="reason" value="Tidak memenuhi kriteria perizinan pulang cepat">
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-[11px] transition cursor-pointer">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if($el->status === 'APPROVED')
                                        <a href="{{ route('attendance.early-leave.print', $el->id) }}" target="_blank"
                                            class="px-3 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1"
                                            title="Cetak Surat Keterangan Pulang Cepat Ber-Kop Sekolah Resmi">
                                            <span>🖨️ Cetak Surat</span>
                                            <span class="text-xs">&nearr;</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <div class="text-3xl mb-1">🏃</div>
                            <p class="font-bold">Belum ada ajuan pulang cepat hari ini.</p>
                            <button type="button" onclick="switchQuickEarlyLeaveTab('form')" class="mt-2 text-amber-600 hover:underline font-bold text-xs">
                                + Buat Ajuan Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('attendance.early-leave.index') }}" class="text-[11px] font-bold text-amber-600 hover:underline flex items-center gap-1">
                        <span>📂 Buka Manajemen & Riwayat Arsip Lengkap</span>
                        <span>&rarr;</span>
                    </a>
                    <button type="button" onclick="closeEarlyLeaveModal()" class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Jadwal Petugas Piket (Guru & TU Piket Harian) -->
    <div id="modal-picket-schedule" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-2 sm:p-4 py-4 sm:py-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col max-h-[88vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200 my-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-700 via-indigo-600 to-purple-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">📋</span>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base leading-tight">Jadwal Petugas Piket Sekolah</h3>
                        <p class="text-[11px] text-indigo-100">Guru Piket Ketertiban & Petugas TU Piket Harian</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="p-1 bg-black/20 rounded-xl flex items-center gap-1">
                        <button type="button" id="picket-tab-today-btn" onclick="switchPicketTab('today')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-indigo-900 shadow-xs transition cursor-pointer">
                            ☀️ Hari Ini ({{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l') }})
                        </button>
                        <button type="button" id="picket-tab-weekly-btn" onclick="switchPicketTab('weekly')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer">
                            🗓️ Mingguan
                        </button>
                        @if($canManagePermits ?? true)
                            <button type="button" id="picket-tab-create-btn" onclick="switchPicketTab('create')"
                                class="px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer">
                                ➕ Tambah
                            </button>
                        @endif
                    </div>
                    <a href="{{ route('attendance.picket-schedules.print') }}" target="_blank"
                       class="px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center gap-1 ml-1"
                       title="Cetak Jadwal Piket Resmi Ber-Kop Sekolah">
                        <span>🖨️</span>
                        <span class="hidden sm:inline">Cetak</span>
                    </a>
                    <button type="button" onclick="closePicketScheduleModal()" class="text-white/80 hover:text-white text-2xl font-bold leading-none cursor-pointer ml-1">&times;</button>
                </div>
            </div>

            <!-- Modal Body Content -->
            <div class="overflow-y-auto flex-1 min-h-0 p-5 sm:p-6 text-xs space-y-4">
                <!-- Tab 1: Petugas Piket Hari Ini -->
                <div id="picket-view-today" class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm">
                                Petugas Piket Hari Ini: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                        <span class="text-slate-400 font-semibold text-[11px]">{{ count($todayPickets ?? []) }} Petugas Bertugas</span>
                    </div>

                    @forelse(($todayPickets ?? []) as $p)
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($p->person_name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-900 dark:text-white text-sm">{{ $p->person_name }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $p->role_type === 'GURU_PIKET' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-200' : 'bg-purple-100 text-purple-900 dark:bg-purple-950 dark:text-purple-200' }}">
                                            {{ $p->role_type === 'GURU_PIKET' ? '👨‍🏫 Guru Piket' : ($p->role_type === 'TU_PIKET' ? '🏢 TU Piket' : '⭐ Koordinator Piket') }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 space-y-0.5">
                                        <div>📍 Pos Tugas: <strong>{{ $p->location }}</strong></div>
                                        <div>⏰ Jam Operasional: <strong class="font-mono text-indigo-600 dark:text-indigo-400">{{ substr($p->start_time, 0, 5) }} - {{ substr($p->end_time, 0, 5) }} WIB</strong></div>
                                        @if($p->notes)
                                            <div class="text-slate-400 italic">"{{ $p->notes }}"</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                @if($p->teacher?->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->teacher->phone) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[11px] flex items-center gap-1 transition">
                                        <span>💬 Hubungi WA</span>
                                    </a>
                                @endif
                                @if(($canManagePermits ?? true) && $p->id && $p->id < 9000)
                                    <form action="{{ route('attendance.picket-schedules.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus petugas ini dari jadwal piket?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition" title="Hapus dari jadwal">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400">
                            <span class="text-4xl">📋</span>
                            <p class="font-bold mt-2">Belum ada penugasan petugas piket khusus untuk hari {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l') }}.</p>
                            <button type="button" onclick="switchPicketTab('create')" class="mt-2 text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                                + Tambahkan Petugas Piket Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>

                <!-- Tab 2: Jadwal Piket Mingguan (Senin - Sabtu) -->
                <div id="picket-view-weekly" class="hidden space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="font-bold text-slate-900 dark:text-white text-sm">Daftar Jadwal Piket Mingguan (Senin - Sabtu)</span>
                        <a href="{{ route('attendance.picket-schedules.print') }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                            🖨️ Cetak Jadwal Resmi
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $dName)
                            @php
                                $dList = ($weeklyPickets ?? collect())->get($dName, collect());
                            @endphp
                            <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 shadow-2xs space-y-2">
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-100 dark:border-slate-700">
                                    <span class="font-black text-indigo-700 dark:text-indigo-300 text-xs uppercase tracking-wider flex items-center gap-1.5">
                                        <span>🗓️</span> {{ $dName }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        {{ $dList->count() }} Petugas
                                    </span>
                                </div>

                                <div class="space-y-1.5">
                                    @forelse($dList as $it)
                                        <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-between text-[11px]">
                                            <div>
                                                <div class="font-bold text-slate-800 dark:text-slate-100">{{ $it->person_name }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $it->role_type === 'GURU_PIKET' ? 'Guru Piket' : 'TU Piket' }} &bull; {{ substr($it->start_time, 0, 5) }}-{{ substr($it->end_time, 0, 5) }}</div>
                                            </div>
                                            @if(($canManagePermits ?? true) && $it->id && $it->id < 9000)
                                                <form action="{{ route('attendance.picket-schedules.destroy', $it->id) }}" method="POST" onsubmit="return confirm('Hapus petugas ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-400 hover:text-rose-600 p-1">✕</button>
                                                </form>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="p-2 text-center text-slate-400 text-[10px] italic">
                                            Belum ada petugas di hari ini
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tab 3: Tambah Petugas Piket Baru -->
                <div id="picket-view-create" class="hidden">
                    <form action="{{ route('attendance.picket-schedules.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 text-indigo-950 dark:text-indigo-200 text-xs font-medium">
                            Tetapkan guru atau staf tata usaha untuk bertugas menjaga ketertiban, pelayanan tamu, dan perizinan siswa.
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Hari Bertugas <span class="text-rose-500">*</span></label>
                                <select name="day_name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat" selected>Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Peran / Tugas Piket <span class="text-rose-500">*</span></label>
                                <select name="role_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                                    <option value="GURU_PIKET">Guru Piket Ketertiban</option>
                                    <option value="TU_PIKET">Petugas TU Piket & Pelayanan</option>
                                    <option value="KOORDINATOR_PIKET">Koordinator Tim Piket</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Guru / Pegawai Piket <span class="text-rose-500">*</span></label>
                                <select id="picket-select-teacher" onchange="syncPicketPersonName(this)" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white mb-2">
                                    <option value="">-- Pilih dari Daftar Guru / Pegawai --</option>
                                    <optgroup label="Dewan Guru">
                                        @foreach(($allPicketTeachers ?? []) as $tch)
                                            <option value="teacher_{{ $tch->id }}" data-name="{{ $tch->name }}">{{ $tch->name }} ({{ $tch->nip ?: 'Guru Mapel' }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Staf Tata Usaha">
                                        @foreach(($allPicketStaff ?? []) as $stf)
                                            <option value="user_{{ $stf->id }}" data-name="{{ $stf->name }}">{{ $stf->name }} ({{ strtoupper($stf->role) }})</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <input type="hidden" name="teacher_id" id="picket_teacher_id">
                                <input type="hidden" name="user_id" id="picket_user_id">
                                <input type="text" name="person_name" id="picket_person_name" required placeholder="Atau ketik nama petugas piket..."
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pos / Lokasi Tugas <span class="text-rose-500">*</span></label>
                                <input type="text" name="location" value="Pos Piket Gerbang Utama & Lobi" required
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="start_time" value="06:30" required
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="end_time" value="15:30" required
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tugas Pokok & Catatan Khusus</label>
                                <textarea name="notes" rows="2" placeholder="Contoh: Pemeriksaan atribut gerbang, buku tamu, persetujuan izin pulang cepat..."
                                          class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"></textarea>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                            <button type="button" onclick="switchPicketTab('today')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-md transition cursor-pointer">
                                Simpan Jadwal Piket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="quick-school-pass-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 bg-gradient-to-r from-teal-600 to-emerald-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🚪</span>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base leading-tight">Surat Izin Keluar Lingkungan Sekolah</h3>
                        <p class="text-[11px] text-teal-100">Bisa memilih lebih dari 1 siswa sekaligus (rombongan/delegasi)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="p-1 bg-black/20 rounded-xl flex items-center gap-1">
                        <button type="button" id="quick-sp-tab-form-btn" onclick="switchQuickSchoolPassTab('form')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-teal-800 shadow-xs transition cursor-pointer">
                            ✍️ Izin Baru
                        </button>
                        <button type="button" id="quick-sp-tab-report-btn" onclick="switchQuickSchoolPassTab('report')"
                            class="px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer flex items-center gap-1">
                            <span>📋 Laporan & Persetujuan</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ ($pendingExitPermitsCount ?? 0) > 0 ? 'bg-rose-500 text-white' : 'bg-black/30 text-white' }}">
                                {{ isset($todayExitPermits) ? $todayExitPermits->count() : 0 }}
                            </span>
                        </button>
                    </div>
                    <button type="button" onclick="closeSchoolPassModal()" class="text-white/80 hover:text-white text-2xl font-bold leading-none cursor-pointer ml-1">&times;</button>
                </div>
            </div>

            <!-- View 1: Formulir Izin Baru -->
            <div id="quick-sp-view-form" class="overflow-y-auto flex-1 min-h-0">
                <form action="{{ route('attendance.school-pass.store') }}" method="POST" class="p-5 sm:p-6 space-y-4 text-xs">
                    @csrf

                    <!-- Pemilihan Siswa (Multi-Select) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">
                                Pilih Siswa yang Diberi Izin <span class="text-rose-500">* (Bisa > 1 Siswa)</span>
                            </label>
                            <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400">
                                <span id="quick-sp-selected-counter">0</span> Siswa Dipilih
                            </span>
                        </div>

                        <!-- Filter Pencarian Siswa -->
                        <div class="relative mb-2">
                            <input type="text" id="quick-sp-search-input" oninput="filterQuickSpStudents(this.value)" placeholder="Cari nama, NIS, atau kelas siswa..."
                                class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <span class="absolute left-2.5 top-2 text-slate-400">🔍</span>
                        </div>

                        <!-- Container Chip Siswa Terpilih -->
                        <div id="quick-sp-chips-container" class="flex flex-wrap gap-1 mb-2 max-h-24 overflow-y-auto"></div>

                        <!-- List Scrollable Checkbox Siswa -->
                        <div class="max-h-40 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-xl p-2 space-y-1 bg-slate-50/50 dark:bg-slate-800/50 scrollbar-thin">
                            @foreach($allStudents ?? [] as $st)
                                <label class="quick-sp-student-row flex items-center justify-between p-2 rounded-lg hover:bg-teal-50 dark:hover:bg-slate-700 cursor-pointer transition text-xs"
                                       data-search="{{ strtolower($st->name . ' ' . $st->nis . ' ' . ($st->schoolClass?->name ?? '')) }}">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="student_ids[]" value="{{ $st->id }}"
                                            data-name="{{ $st->name }}" data-class="{{ $st->schoolClass?->name ?? '-' }}"
                                            onchange="updateQuickSpChips()"
                                            class="quick-sp-checkbox rounded text-teal-600 focus:ring-teal-500 w-4 h-4 cursor-pointer">
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-white">{{ $st->name }}</p>
                                            <p class="text-[10px] text-slate-400">NIS: {{ $st->nis }} • Kelas: {{ $st->schoolClass?->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold">
                                        {{ $st->schoolClass?->name ?? '-' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Izin <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Keluar <span class="text-rose-500">*</span></label>
                            <input type="time" name="out_time" required value="{{ now('Asia/Jakarta')->format('H:i') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-semibold font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Jam Kembali</label>
                            <input type="time" name="expected_back_time"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-semibold font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Tujuan <span class="text-rose-500">*</span></label>
                            <select name="purpose" required
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option value="Membeli Perlengkapan KBM / Praktikum">Membeli Alat / Bahan KBM / Praktikum</option>
                                <option value="Tugas Dinas / OSIS / MPK / Ekstrakurikuler">Tugas Dinas / OSIS / MPK / Ekstrakurikuler</option>
                                <option value="Mengikuti Lomba / Festival / Kompetisi">Mengikuti Lomba / Pertandingan Resmi</option>
                                <option value="Pemeriksaan Medis / Rujukan Puskesmas">Pemeriksaan Medis / Puskesmas</option>
                                <option value="Keperluan Administrasi / Cetak Dokumen">Cetak / Fotokopi Dokumen Sekolah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat / Lokasi Tujuan</label>
                            <input type="text" name="destination" placeholder="Contoh: Puskesmas Lengkong / Toko ATK..."
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pendamping (Jika Ada)</label>
                            <input type="text" name="accompanied_by" placeholder="Contoh: Guru Pembina, Satpam, Mandiri"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_returning" value="1" checked
                                    class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Siswa Akan Kembali Lagi ke Sekolah Hari Ini
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Alasan Lengkap <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="2" required placeholder="Jelaskan secara spesifik urgensi dan alasan izin keluar lingkungan sekolah..."
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                    </div>

                    @if(!Auth::user()->isStudent())
                        <div class="pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="direct_approve" value="1" checked
                                    class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Langsung Setujui & Terbitkan Surat Izin Resmi (Guru Piket / TU Piket / Wali Kelas)
                                </span>
                            </label>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="switchQuickSchoolPassTab('report')" class="text-[11px] font-bold text-teal-600 hover:text-teal-700 dark:text-teal-400 underline flex items-center gap-1 cursor-pointer">
                            <span>📋 Lihat Laporan & Persetujuan ({{ isset($todayExitPermits) ? $todayExitPermits->count() : 0 }})</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="closeSchoolPassModal()"
                                class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-xs font-bold bg-teal-600 hover:bg-teal-700 active:scale-95 text-white rounded-xl transition shadow-sm shadow-teal-600/30 cursor-pointer">
                                Simpan & Terbitkan Surat Izin
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- View 2: Laporan & Persetujuan -->
            <div id="quick-sp-view-report" class="hidden overflow-y-auto flex-1 min-h-0 p-5 sm:p-6 space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Data Izin Keluar Sekolah Hari Ini</span>
                        <p class="text-[11px] text-slate-400">Total: <strong>{{ isset($todayExitPermits) ? $todayExitPermits->count() : 0 }} perizinan</strong></p>
                    </div>
                    <button type="button" onclick="switchQuickSchoolPassTab('form')" class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] transition shadow-xs cursor-pointer">
                        + Tambah Izin Keluar Baru
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto space-y-2.5 pr-1">
                    @forelse($todayExitPermits ?? [] as $sp)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white text-xs">
                                        {{ $sp->students->count() }} Siswa Diberi Izin
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400">({{ $sp->permit_number }})</span>
                                </div>
                                <div class="flex flex-wrap gap-1 max-w-sm">
                                    @foreach($sp->students as $st)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold">
                                            {{ $st->name }} ({{ $st->schoolClass?->name ?? '-' }})
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-[11px] text-teal-700 dark:text-teal-300 font-bold flex items-center gap-2 flex-wrap">
                                    <span>🚪 Keluar: {{ $sp->exit_time }} WIB</span>
                                    <span>•</span>
                                    <span>Kembali: {{ $sp->is_returning ? ($sp->actual_back_time ? 'Sudah Kembali ('.$sp->actual_back_time.')' : 'Est. '.$sp->estimated_back_time.' WIB') : 'Langsung Pulang' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 italic">"{{ $sp->purpose }}" - {{ $sp->destination ?? 'Lingkungan Sekolah' }}</p>
                            </div>

                            <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0">
                                <div>
                                    @if($sp->status === 'APPROVED')
                                        @if($sp->is_returning && !$sp->actual_back_time)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-800 animate-pulse">
                                                🔵 Di Luar Sekolah
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                                ✓ Disetujui
                                            </span>
                                        @endif
                                    @elseif($sp->status === 'RETURNED')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                                            ✓ Sudah Kembali
                                        </span>
                                    @elseif($sp->status === 'REJECTED')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-800 animate-pulse">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                    @if($sp->status === 'PENDING' && ($canManagePermits ?? false))
                                        <form action="{{ route('attendance.school-pass.approve', $sp->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Setujui izin keluar sekolah dan terbitkan Surat Izin Resmi?')"
                                                class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] shadow-2xs transition cursor-pointer">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('attendance.school-pass.reject', $sp->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <input type="hidden" name="reason" value="Izin keluar lingkungan sekolah tidak disetujui">
                                            <button type="submit" onclick="return confirm('Tolak perizinan keluar lingkungan sekolah ini?')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-[11px] transition cursor-pointer">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($sp->status, ['APPROVED', 'RETURNED']))
                                        <a href="{{ route('attendance.school-pass.print', $sp->id) }}" target="_blank"
                                            class="px-3 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1"
                                            title="Cetak Surat Izin Keluar Lingkungan Sekolah Resmi Ber-Kop Sekolah">
                                            <span>🖨️ Cetak Surat</span>
                                            <span class="text-xs">&nearr;</span>
                                        </a>
                                    @endif

                                    @if($sp->status === 'APPROVED' && $sp->is_returning && !$sp->actual_back_time && ($canManagePermits ?? false))
                                        <form action="{{ route('attendance.school-pass.returned', $sp->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Catat konfirmasi bahwa rombongan siswa telah kembali ke lingkungan sekolah?')"
                                                class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[11px] transition shadow-2xs cursor-pointer flex items-center gap-1"
                                                title="Konfirmasi Siswa Sudah Kembali ke Sekolah">
                                                <span>↩️ Kembali</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <div class="text-3xl mb-1">🚪</div>
                            <p class="font-bold">Belum ada izin keluar lingkungan sekolah hari ini.</p>
                            <button type="button" onclick="switchQuickSchoolPassTab('form')" class="mt-2 text-teal-600 hover:underline font-bold text-xs">
                                + Buat Izin Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('attendance.school-pass.index') }}" class="text-[11px] font-bold text-teal-600 hover:underline flex items-center gap-1">
                        <span>📂 Buka Manajemen & Riwayat Arsip Lengkap</span>
                        <span>&rarr;</span>
                    </a>
                    <button type="button" onclick="closeSchoolPassModal()" class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab Controller
    function switchTab(tabId) {
        document.querySelectorAll('[id^="tab-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.add('text-slate-600', 'dark:text-slate-400');
        });

        const activeTabEl = document.getElementById(tabId);
        if (activeTabEl) {
            activeTabEl.classList.remove('hidden');
        }

        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            activeBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
        }

        if (tabId === 'tab-scanner') {
            const scanInput = document.getElementById('scan-input');
            if (scanInput) scanInput.focus();
        }

        if (tabId === 'tab-leave') {
            initTabLeaveGPS();
            checkTabLeaveFormReady();
        }
    }

    // Bulk Dropdown Controller
    function toggleBulkDropdown() {
        const menu = document.getElementById('bulk-dropdown-menu');
        if (menu) menu.classList.toggle('hidden');
    }

    function closeBulkDropdown() {
        const menu = document.getElementById('bulk-dropdown-menu');
        if (menu) menu.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('bulk-dropdown-container');
        if (container && !container.contains(e.target)) {
            closeBulkDropdown();
        }
    });

    // Multi-Select Students Checkboxes & Bulk Batch Actions
    function toggleSelectAllStudents(checked) {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
        updateSelectedStudentsCounter();
    }

    function selectUnmarkedStudents() {
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.checked = (cb.getAttribute('data-has-status') === '0');
        });
        updateSelectedStudentsCounter();
    }

    function updateSelectedStudentsCounter() {
        const allBoxes = document.querySelectorAll('.student-checkbox');
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const counterEl = document.getElementById('selected-students-count');
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
        const checkAll = document.getElementById('check-all-students');
        if (checkAll) {
            checkAll.checked = (allBoxes.length > 0 && checkedBoxes.length === allBoxes.length);
            checkAll.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        }
    }

    function setStatusesForSelected(status) {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu menggunakan kotak pilihan (checkbox).');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            const radio = document.querySelector(`input[name="attendances[${studentId}][status]"][value="${status}"]`);
            if (radio) {
                radio.checked = true;
                const row = cb.closest('.student-row');
                if (row) {
                    row.classList.add('bg-indigo-50/80', 'dark:bg-indigo-950/40');
                    setTimeout(() => row.classList.remove('bg-indigo-50/80', 'dark:bg-indigo-950/40'), 800);
                }
            }
        });
    }

    function clearStatusesForSelected() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu.');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            document.querySelectorAll(`input[name="attendances[${studentId}][status]"]`).forEach(r => r.checked = false);
        });
    }

    // Set All Manual Status Helper
    function setAllManualStatus(status) {
        document.querySelectorAll(`.manual-status-radio[value="${status}"]`).forEach(r => r.checked = true);
    }

    function resetAllManualStatus() {
        document.querySelectorAll('.manual-status-radio').forEach(r => r.checked = false);
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        updateSelectedStudentsCounter();
    }

    // Scanner Gate Controller (Barcode, RFID, Face Recognition)
    let currentScannerGate = '{{ $enableBarcode ? 'barcode' : ($enableRfid ? 'rfid' : ($enableFaceRecognition ? 'face' : 'barcode')) }}';

    function setScannerGate(gate) {
        currentScannerGate = gate;
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
                input.placeholder = "Arahkan scanner ke Barcode Kartu Siswa atau ketik NISN...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log Barcode";
        } else if (gate === 'rfid') {
            if (inputLabel) inputLabel.innerText = "Tempelkan Kartu RFID Siswa ke Reader Sensor / Masukkan UID:";
            if (input) {
                input.placeholder = "Dekatkan kartu RFID siswa ke reader scanner...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log RFID";
        } else if (gate === 'face') {
            if (inputLabel) inputLabel.innerText = "Terminal AI Face Recognition (Masukkan UID Wajah / NISN Siswa Terdeteksi):";
            if (input) {
                input.placeholder = "ID Wajah / NISN hasil verifikasi kamera terminal AI...";
                input.focus();
            }
            if (submitBtn) submitBtn.innerText = "Kirim Log Face Recognition";
        }
    }

    // Scanner Submit Handler (AJAX)
    async function handleScannerSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('scan-input');
        const deviceSelect = document.getElementById('device-select');
        const feedback = document.getElementById('scan-feedback-box');
        const bypassToggle = document.getElementById('bypass-time-toggle');

        const val = input ? input.value.trim() : '';
        if (!val) return;

        const deviceCode = deviceSelect ? deviceSelect.value : 'GATE-01';
        const isBypass = bypassToggle && bypassToggle.checked;

        feedback.className = 'p-4 rounded-2xl border text-xs bg-slate-100 text-slate-700 flex items-center gap-2';
        feedback.innerHTML = '<span class="animate-spin">⏳</span> Memproses pemindaian gerbang presensi...';
        feedback.classList.remove('hidden');

        let targetUrl = "{{ route('attendance.barcode.scan') }}";
        let bodyPayload = {
            device_code: deviceCode,
            bypass_time: isBypass
        };

        if (currentScannerGate === 'rfid') {
            targetUrl = "{{ route('attendance.rfid.scan') }}";
            bodyPayload.rfid_uid = val;
        } else if (currentScannerGate === 'barcode') {
            targetUrl = "{{ route('attendance.barcode.scan') }}";
            bodyPayload.barcode = val;
        } else if (currentScannerGate === 'face') {
            targetUrl = "{{ route('attendance.face.scan') }}";
            bodyPayload.identifier = val;
        }

        try {
            const resp = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyPayload)
            });

            const data = await resp.json();

            if (data.success) {
                feedback.className = 'p-4 rounded-2xl border text-xs bg-emerald-50 dark:bg-emerald-950/60 border-emerald-500/40 text-emerald-900 dark:text-emerald-200';
                feedback.innerHTML = `<strong>✓ BERHASIL:</strong> ${data.message}`;

                // Add to realtime scan feed
                const list = document.getElementById('live-scan-list');
                if (list) {
                    const newItem = document.createElement('div');
                    newItem.className = 'pt-2 flex items-center justify-between gap-3 text-xs bg-emerald-50/50 p-2 rounded-xl';
                    const methodLabel = currentScannerGate.toUpperCase();
                    newItem.innerHTML = `
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-black flex items-center justify-center text-[10px] shrink-0">✓</div>
                            <div class="truncate">
                                <div class="font-bold text-slate-800 truncate">${data.student_name || val}</div>
                                <div class="text-[10px] text-slate-400">Method: ${methodLabel} • ${deviceCode}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-700">Tercatat</span>
                    `;
                    list.prepend(newItem);
                }
            } else {
                feedback.className = 'p-4 rounded-2xl border text-xs bg-rose-50 dark:bg-rose-950/60 border-rose-500/40 text-rose-900 dark:text-rose-200';
                feedback.innerHTML = `<strong>✕ GAGAL:</strong> ${data.message}`;
            }
        } catch (err) {
            feedback.className = 'p-4 rounded-2xl border text-xs bg-rose-50 text-rose-800';
            feedback.innerHTML = '<strong>✕ Kesalahan Jaringan:</strong> Gagal menghubungi server presensi.';
        }

        input.value = '';
        input.focus();
    }

    // Camera Selfie Stream & Geolocation
    let videoStream = null;
    let userLat = null;
    let userLng = null;

    function initGeolocation() {
        const gpsStatus = document.getElementById('selfie-gps-status');
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (pos) => {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    if (gpsStatus) {
                        gpsStatus.innerHTML = `GPS Terkunci: ${userLat.toFixed(5)}, ${userLng.toFixed(5)} (Akurasi: ±${Math.round(pos.coords.accuracy)}m)`;
                    }
                },
                (err) => {
                    if (gpsStatus) {
                        gpsStatus.innerHTML = `⚠️ Akses GPS Ditolak / Tidak Tersedia (${err.message})`;
                    }
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }

    async function startCamera() {
        initGeolocation();
        const video = document.getElementById('selfie-video');
        const loading = document.getElementById('camera-loading');

        try {
            if (videoStream) {
                videoStream.getTracks().forEach(t => t.stop());
            }

            videoStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
            });

            if (video) {
                video.srcObject = videoStream;
                if (loading) loading.classList.add('hidden');
            }
        } catch (err) {
            alert('Tidak dapat mengakses kamera perangkat: ' + err.message);
        }
    }

    async function captureAndSubmitSelfie() {
        const video = document.getElementById('selfie-video');
        const canvas = document.getElementById('selfie-canvas');
        const feedback = document.getElementById('selfie-feedback-box');
        const bypassToggle = document.getElementById('bypass-time-toggle');

        if (!videoStream || !video) {
            alert('Silakan nyalakan kamera terlebih dahulu!');
            return;
        }

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

        feedback.className = 'p-4 rounded-2xl border text-xs bg-slate-100 text-slate-700 flex items-center gap-2';
        feedback.innerHTML = '<span class="animate-spin">⏳</span> Mengunggah foto dan memvalidasi geofence...';
        feedback.classList.remove('hidden');

        try {
            const resp = await fetch("{{ route('attendance.selfie.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    selfie_image: dataUrl,
                    latitude: userLat,
                    longitude: userLng,
                    bypass_time: bypassToggle && bypassToggle.checked
                })
            });

            const data = await resp.json();

            if (data.success) {
                feedback.className = 'p-4 rounded-2xl border text-xs bg-emerald-50 text-emerald-900 border-emerald-500/40';
                feedback.innerHTML = `<strong>✓ SUKSES:</strong> ${data.message}`;
            } else {
                feedback.className = 'p-4 rounded-2xl border text-xs bg-rose-50 text-rose-900 border-rose-500/40';
                feedback.innerHTML = `<strong>✕ GAGAL:</strong> ${data.message}`;
            }
        } catch (e) {
            feedback.className = 'p-4 rounded-2xl border text-xs bg-rose-50 text-rose-800';
            feedback.innerHTML = '<strong>✕ Kesalahan Jaringan:</strong> Gagal menyimpan selfie presensi.';
        }
    }

    // ==========================================
    // TAB 4: KAMERA SELFIE WAJIB PENGAJUAN IZIN
    // ==========================================
    let tabLeaveStream = null;
    let tabLeaveLat = null;
    let tabLeaveLng = null;
    let tabLeaveAcc = null;
    let tabLeaveHasSnapshot = false;

    function toggleTabLeaveEarlyField() {
        checkTabLeaveFormReady();
    }

    function checkTabLeaveCutoffTime() {
        const dateInput = document.getElementById('tab_leave_date');
        const todayStr = '{{ date('Y-m-d') }}';
        const nowTimeStr = '{{ date('H:i') }}';
        const cutoffAlert = document.getElementById('tab-leave-cutoff-warning-alert');
        const advanceNotice = document.getElementById('tab-leave-advance-notice');

        if (!dateInput) return true;

        const targetDate = dateInput.value;

        if (targetDate === todayStr && nowTimeStr > '06:30') {
            if (cutoffAlert) cutoffAlert.classList.remove('hidden');
            if (advanceNotice) advanceNotice.classList.add('hidden');
            return false;
        } else if (targetDate > todayStr) {
            if (cutoffAlert) cutoffAlert.classList.add('hidden');
            if (advanceNotice) advanceNotice.classList.remove('hidden');
            return true;
        } else {
            if (cutoffAlert) cutoffAlert.classList.add('hidden');
            if (advanceNotice) advanceNotice.classList.add('hidden');
            return true;
        }
    }

    function checkTabLeaveFormReady() {
        const isCutoffValid = checkTabLeaveCutoffTime();
        const submitBtn = document.getElementById('btn-tab-leave-submit');
        if (submitBtn) {
            if (tabLeaveHasSnapshot && tabLeaveLat !== null && tabLeaveLng !== null && isCutoffValid) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }
    }

    function initTabLeaveGPS() {
        const indicator = document.getElementById('tab-leave-gps-indicator');
        const statusText = document.getElementById('tab-leave-gps-status-text');
        const coordsText = document.getElementById('tab-leave-gps-coords-text');
        const hiddenLat = document.getElementById('tab_leave_latitude');
        const hiddenLng = document.getElementById('tab_leave_longitude');
        const hiddenAcc = document.getElementById('tab_leave_accuracy');

        if (!navigator.geolocation) {
            if (statusText) statusText.innerText = "Perangkat tidak mendukung geolokasi GPS (menggunakan lokasi sekolah).";
            tabLeaveLat = {{ (float) ($settings['school_lat'] ?? -7.56845) }};
            tabLeaveLng = {{ (float) ($settings['school_lng'] ?? 112.04612) }};
            if (hiddenLat) hiddenLat.value = tabLeaveLat;
            if (hiddenLng) hiddenLng.value = tabLeaveLng;
            checkTabLeaveFormReady();
            return;
        }

        navigator.geolocation.watchPosition(
            (pos) => {
                tabLeaveLat = pos.coords.latitude;
                tabLeaveLng = pos.coords.longitude;
                tabLeaveAcc = pos.coords.accuracy;

                if (hiddenLat) hiddenLat.value = tabLeaveLat;
                if (hiddenLng) hiddenLng.value = tabLeaveLng;
                if (hiddenAcc) hiddenAcc.value = tabLeaveAcc;

                if (indicator) indicator.className = "w-3.5 h-3.5 rounded-full bg-emerald-500 shrink-0";
                if (statusText) statusText.innerText = "Lokasi Realtime Terekam (" + tabLeaveLat.toFixed(5) + ", " + tabLeaveLng.toFixed(5) + ")";
                if (coordsText) coordsText.innerText = "Akurasi GPS: ±" + Math.round(tabLeaveAcc) + " meter";

                checkTabLeaveFormReady();
            },
            (err) => {
                if (indicator) indicator.className = "w-3.5 h-3.5 rounded-full bg-amber-500 shrink-0";
                if (statusText) statusText.innerText = "GPS Perangkat Tidak Aktif (" + err.message + ")";
                if (coordsText) coordsText.innerText = "Menggunakan koordinat standar sekolah (Bebas Batas Radius).";
                tabLeaveLat = {{ (float) ($settings['school_lat'] ?? -7.56845) }};
                tabLeaveLng = {{ (float) ($settings['school_lng'] ?? 112.04612) }};
                tabLeaveAcc = 50;
                if (hiddenLat) hiddenLat.value = tabLeaveLat;
                if (hiddenLng) hiddenLng.value = tabLeaveLng;
                if (hiddenAcc) hiddenAcc.value = tabLeaveAcc;
                checkTabLeaveFormReady();
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 }
        );
    }

    async function startTabLeaveCamera() {
        initTabLeaveGPS();
        const video = document.getElementById('tab-leave-video');
        const prompt = document.getElementById('tab-leave-prompt');
        const guide = document.getElementById('tab-leave-face-guide');
        const snapBtn = document.getElementById('btn-tab-leave-snap');

        try {
            if (tabLeaveStream) {
                tabLeaveStream.getTracks().forEach(t => t.stop());
            }

            tabLeaveStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            });

            if (video) {
                video.srcObject = tabLeaveStream;
                if (prompt) prompt.classList.add('hidden');
                if (guide) guide.classList.remove('hidden');
                if (snapBtn) snapBtn.disabled = false;
            }
        } catch (err) {
            alert('Tidak dapat mengakses kamera: ' + err.message + '. Pastikan izin kamera telah diberikan pada peramban.');
        }
    }

    function takeTabLeaveSnapshot() {
        const video = document.getElementById('tab-leave-video');
        const preview = document.getElementById('tab-leave-preview');
        const canvas = document.getElementById('tab-leave-canvas');
        const guide = document.getElementById('tab-leave-face-guide');
        const snapBtn = document.getElementById('btn-tab-leave-snap');
        const retakeBtn = document.getElementById('btn-tab-leave-retake');
        const hiddenInput = document.getElementById('tab_leave_selfie_image');

        if (!tabLeaveStream || !video) {
            alert('Silakan nyalakan kamera terlebih dahulu!');
            return;
        }

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');

        // Mirror snapshot
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

        if (hiddenInput) hiddenInput.value = dataUrl;
        if (preview) {
            preview.src = dataUrl;
            preview.classList.remove('hidden');
        }
        if (video) video.classList.add('hidden');
        if (guide) guide.classList.add('hidden');
        if (snapBtn) snapBtn.classList.add('hidden');
        if (retakeBtn) retakeBtn.classList.remove('hidden');
        tabLeaveHasSnapshot = true;

        checkTabLeaveFormReady();
    }

    function retakeTabLeaveSnapshot() {
        const video = document.getElementById('tab-leave-video');
        const preview = document.getElementById('tab-leave-preview');
        const guide = document.getElementById('tab-leave-face-guide');
        const snapBtn = document.getElementById('btn-tab-leave-snap');
        const retakeBtn = document.getElementById('btn-tab-leave-retake');
        const hiddenInput = document.getElementById('tab_leave_selfie_image');

        if (hiddenInput) hiddenInput.value = '';
        if (preview) preview.classList.add('hidden');
        if (video) video.classList.remove('hidden');
        if (guide) guide.classList.remove('hidden');
        if (snapBtn) snapBtn.classList.remove('hidden');
        if (retakeBtn) retakeBtn.classList.add('hidden');
        tabLeaveHasSnapshot = false;

        checkTabLeaveFormReady();
    }

    let activeLeaveInitial = 'ALL';

    function openLeaveStudentDropdown() {
        const dd = document.getElementById('leave-student-dropdown');
        if (dd) dd.classList.remove('hidden');
    }

    function closeLeaveStudentDropdown() {
        const dd = document.getElementById('leave-student-dropdown');
        if (dd) dd.classList.add('hidden');
    }

    function filterLeaveByInitial(char) {
        activeLeaveInitial = char;
        document.querySelectorAll('.leave-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === char) {
                btn.className = 'leave-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'leave-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openLeaveStudentDropdown();
        filterLeaveStudents(document.getElementById('leave_student_search_input')?.value || '');
    }

    function filterLeaveStudents(query) {
        const q = query.toLowerCase().trim();
        const clearBtn = document.getElementById('leave-student-search-clear');
        if (clearBtn) clearBtn.classList.toggle('hidden', q === '');

        const items = document.querySelectorAll('.leave-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const sClass = (item.getAttribute('data-class') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchesInitial = (activeLeaveInitial === 'ALL' || firstLetter === activeLeaveInitial);
            const matchesQuery = !q || name.includes(q) || nis.includes(q) || sClass.includes(q) || initials.includes(q);

            if (matchesInitial && matchesQuery) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = document.getElementById('leave-student-empty');
        if (empty) empty.classList.toggle('hidden', visibleCount > 0);
        openLeaveStudentDropdown();
    }

    function selectLeaveStudent(el) {
        const id = el.getAttribute('data-id');
        const name = el.getAttribute('data-name');
        const nis = el.getAttribute('data-nis');
        const sClass = el.getAttribute('data-class');
        const avatar = el.getAttribute('data-avatar');
        const initials = el.getAttribute('data-initials');

        const hidden = document.getElementById('leave_student_id');
        if (hidden) hidden.value = id;

        const preview = document.getElementById('leave-student-selected-preview');
        const previewName = document.getElementById('leave-student-preview-name');
        const previewMeta = document.getElementById('leave-student-preview-meta');
        const previewAvatar = document.getElementById('leave-student-preview-avatar');
        const searchWrapper = document.getElementById('leave-student-search-wrapper');
        const initialsBar = document.getElementById('leave-initials-bar');

        if (previewName) previewName.textContent = name;
        if (previewMeta) previewMeta.textContent = `Kelas ${sClass} • NIS: ${nis} • [${initials.toUpperCase()}]`;
        if (previewAvatar) previewAvatar.textContent = avatar;

        if (preview) preview.classList.remove('hidden');
        if (searchWrapper) searchWrapper.classList.add('hidden');
        if (initialsBar) initialsBar.classList.add('hidden');

        closeLeaveStudentDropdown();
        checkTabLeaveFormReady();
    }

    function clearLeaveStudentSelection() {
        const hidden = document.getElementById('leave_student_id');
        if (hidden) hidden.value = '';

        const preview = document.getElementById('leave-student-selected-preview');
        const searchWrapper = document.getElementById('leave-student-search-wrapper');
        const initialsBar = document.getElementById('leave-initials-bar');
        const input = document.getElementById('leave_student_search_input');

        if (preview) preview.classList.add('hidden');
        if (searchWrapper) searchWrapper.classList.remove('hidden');
        if (initialsBar) initialsBar.classList.remove('hidden');
        if (input) {
            input.value = '';
            input.focus();
        }

        filterLeaveByInitial('ALL');
        checkTabLeaveFormReady();
    }

    function resetLeaveStudentSearch() {
        const input = document.getElementById('leave_student_search_input');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterLeaveStudents('');
    }

    document.addEventListener('click', function(e) {
        const combobox = document.getElementById('leave-student-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeLeaveStudentDropdown();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        initTabLeaveGPS();
        toggleTabLeaveEarlyField();
        const tabLeaveDate = document.getElementById('tab_leave_date');
        if (tabLeaveDate) {
            tabLeaveDate.addEventListener('change', checkTabLeaveFormReady);
        }
    });

    function openEditModal(id, studentName, currentStatus, timeIn, timeOut, notes) {
        const modal = document.getElementById('edit-attendance-modal');
        if (!modal) return;
        document.getElementById('edit-modal-student-name').innerText = "Siswa: " + studentName;
        document.getElementById('edit-modal-status').value = currentStatus;
        document.getElementById('edit-modal-time-in').value = timeIn;
        document.getElementById('edit-modal-time-out').value = timeOut;
        document.getElementById('edit-modal-notes').value = notes;
        document.getElementById('edit-attendance-form').action = "/attendance/" + id;
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('edit-attendance-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Modal Jadwal Petugas Piket Controller (Vanilla JS)
    function openPicketScheduleModal(tab = 'today') {
        const modal = document.getElementById('modal-picket-schedule');
        if (modal) {
            modal.classList.remove('hidden');
            switchPicketTab(tab);
        }
    }

    function closePicketScheduleModal() {
        const modal = document.getElementById('modal-picket-schedule');
        if (modal) modal.classList.add('hidden');
    }

    function switchPicketTab(tab) {
        const views = {
            today: document.getElementById('picket-view-today'),
            weekly: document.getElementById('picket-view-weekly'),
            create: document.getElementById('picket-view-create'),
        };
        const btns = {
            today: document.getElementById('picket-tab-today-btn'),
            weekly: document.getElementById('picket-tab-weekly-btn'),
            create: document.getElementById('picket-tab-create-btn'),
        };

        Object.keys(views).forEach(key => {
            if (views[key]) {
                if (key === tab) {
                    views[key].classList.remove('hidden');
                } else {
                    views[key].classList.add('hidden');
                }
            }
            if (btns[key]) {
                if (key === tab) {
                    btns[key].className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-indigo-900 shadow-xs transition cursor-pointer';
                } else {
                    btns[key].className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer';
                }
            }
        });
    }

    function syncPicketPersonName(selectEl) {
        if (!selectEl) return;
        const val = selectEl.value;
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const name = selectedOption ? selectedOption.getAttribute('data-name') : '';

        const teacherInput = document.getElementById('picket_teacher_id');
        const userInput = document.getElementById('picket_user_id');
        const nameInput = document.getElementById('picket_person_name');

        if (teacherInput) teacherInput.value = '';
        if (userInput) userInput.value = '';

        if (val.startsWith('teacher_')) {
            const id = val.replace('teacher_', '');
            if (teacherInput) teacherInput.value = id;
            if (nameInput && name) nameInput.value = name;
        } else if (val.startsWith('user_')) {
            const id = val.replace('user_', '');
            if (userInput) userInput.value = id;
            if (nameInput && name) nameInput.value = name;
        }
    }

    // Modal Ajuan Pulang Cepat Controller (Vanilla JS)
    function openEarlyLeaveModal(tab = 'form') {
        const modal = document.getElementById('quick-early-leave-modal');
        if (modal) {
            modal.classList.remove('hidden');
            switchQuickEarlyLeaveTab(tab);
        }
    }

    function closeEarlyLeaveModal() {
        const modal = document.getElementById('quick-early-leave-modal');
        if (modal) modal.classList.add('hidden');
    }

    function switchQuickEarlyLeaveTab(tab) {
        const formView = document.getElementById('quick-el-view-form');
        const reportView = document.getElementById('quick-el-view-report');
        const formBtn = document.getElementById('quick-el-tab-form-btn');
        const reportBtn = document.getElementById('quick-el-tab-report-btn');

        if (tab === 'form') {
            if (formView) formView.classList.remove('hidden');
            if (reportView) reportView.classList.add('hidden');
            if (formBtn) {
                formBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-amber-700 shadow-xs transition cursor-pointer';
            }
            if (reportBtn) {
                reportBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer flex items-center gap-1';
            }
        } else {
            if (formView) formView.classList.add('hidden');
            if (reportView) reportView.classList.remove('hidden');
            if (reportBtn) {
                reportBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-amber-700 shadow-xs transition cursor-pointer flex items-center gap-1';
            }
            if (formBtn) {
                formBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer';
            }
        }
    }

    // Modal Surat Izin Keluar Lingkungan Sekolah Controller (Vanilla JS)
    function openSchoolPassModal(tab = 'form') {
        const modal = document.getElementById('quick-school-pass-modal');
        if (modal) {
            modal.classList.remove('hidden');
            updateQuickSpChips();
            switchQuickSchoolPassTab(tab);
        }
    }

    function closeSchoolPassModal() {
        const modal = document.getElementById('quick-school-pass-modal');
        if (modal) modal.classList.add('hidden');
    }

    function switchQuickSchoolPassTab(tab) {
        const formView = document.getElementById('quick-sp-view-form');
        const reportView = document.getElementById('quick-sp-view-report');
        const formBtn = document.getElementById('quick-sp-tab-form-btn');
        const reportBtn = document.getElementById('quick-sp-tab-report-btn');

        if (tab === 'form') {
            if (formView) formView.classList.remove('hidden');
            if (reportView) reportView.classList.add('hidden');
            if (formBtn) {
                formBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-teal-800 shadow-xs transition cursor-pointer';
            }
            if (reportBtn) {
                reportBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer flex items-center gap-1';
            }
        } else {
            if (formView) formView.classList.add('hidden');
            if (reportView) reportView.classList.remove('hidden');
            if (reportBtn) {
                reportBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-white text-teal-800 shadow-xs transition cursor-pointer flex items-center gap-1';
            }
            if (formBtn) {
                formBtn.className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-white/90 hover:text-white transition cursor-pointer';
            }
        }
    }

    // Dashboard Section Permitted Tabs & Actions
    let currentPermitType = 'early-leave';

    function switchPermitTab(type) {
        currentPermitType = type;
        const elTabBtn = document.getElementById('btn-permit-tab-early-leave');
        const spTabBtn = document.getElementById('btn-permit-tab-school-pass');
        const elView = document.getElementById('view-permit-early-leave');
        const spView = document.getElementById('view-permit-school-pass');
        const createBtnLabel = document.getElementById('label-permit-create-btn');

        if (type === 'early-leave') {
            if (elTabBtn) {
                elTabBtn.className = 'px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-300 shadow-xs';
            }
            if (spTabBtn) {
                spTabBtn.className = 'px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            }
            if (elView) elView.classList.remove('hidden');
            if (spView) spView.classList.add('hidden');
            if (createBtnLabel) createBtnLabel.textContent = '+ Buat Ajuan Pulang Cepat';
        } else {
            if (spTabBtn) {
                spTabBtn.className = 'px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-300 shadow-xs';
            }
            if (elTabBtn) {
                elTabBtn.className = 'px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            }
            if (spView) spView.classList.remove('hidden');
            if (elView) elView.classList.add('hidden');
            if (createBtnLabel) createBtnLabel.textContent = '+ Buat Izin Keluar Sekolah';
        }
    }

    function openCurrentPermitCreateModal() {
        if (currentPermitType === 'early-leave') {
            openEarlyLeaveModal('form');
        } else {
            openSchoolPassModal('form');
        }
    }

    function scrollToPermitsSection() {
        const el = document.getElementById('section-special-permits');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function confirmRejectEarlyLeave(form) {
        const reason = prompt('Masukkan alasan penolakan ajuan pulang cepat:', 'Tidak memenuhi kriteria perizinan');
        if (reason === null) return false;
        const input = form.querySelector('input[name="reason"]');
        if (input) input.value = reason.trim() || 'Tidak memenuhi kriteria perizinan';
        return true;
    }

    function filterQuickSpStudents(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.quick-sp-student-row').forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = (!q || searchData.includes(q)) ? 'flex' : 'none';
        });
    }

    function updateQuickSpChips() {
        const checkboxes = document.querySelectorAll('.quick-sp-checkbox:checked');
        const counter = document.getElementById('quick-sp-selected-counter');
        const container = document.getElementById('quick-sp-chips-container');
        if (counter) counter.textContent = checkboxes.length;

        if (!container) return;
        container.innerHTML = '';

        checkboxes.forEach(cb => {
            const id = cb.value;
            const name = cb.getAttribute('data-name');
            const sClass = cb.getAttribute('data-class');

            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-100 dark:bg-teal-950/70 text-teal-800 dark:text-teal-200 border border-teal-300 dark:border-teal-700';
            chip.innerHTML = `
                <span>${name} (${sClass})</span>
                <button type="button" onclick="removeQuickSpStudent('${id}')" class="text-teal-600 hover:text-teal-900 dark:text-teal-300 dark:hover:text-white font-bold ml-1 cursor-pointer">&times;</button>
            `;
            container.appendChild(chip);
        });
    }

    function removeQuickSpStudent(id) {
        const cb = document.querySelector(`.quick-sp-checkbox[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            updateQuickSpChips();
        }
    }

    // Keyboard ESC to close active modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEarlyLeaveModal();
            closeSchoolPassModal();
        }
    });
</script>
@endpush
@endsection
