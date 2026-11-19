@extends('layouts.app')

@section('title', 'Presensi Selfie (Kamera Langsung & GPS)')

@section('content')
<div class="w-full max-w-full sm:max-w-3xl min-w-0 mx-auto space-y-4 sm:space-y-6 px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    @include('attendance.partials.nav')
    <!-- Header Notice: Strict Business Rule -->
    <div class="w-full max-w-full min-w-0 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl sm:rounded-3xl p-3.5 xs:p-4 sm:p-6 text-white shadow-xl border border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 mb-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10.5px] sm:text-xs font-bold border border-emerald-500/30">
                    PRESENSI DUA SESI (MASUK & PULANG)
                </span>
            </div>
            <div class="text-[11px] sm:text-xs text-slate-300 font-medium">
                Hari ini: <strong class="text-white">{{ $settings['day_name'] ?? Carbon\Carbon::now()->isoFormat('dddd') }}</strong>, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
        <h2 class="text-lg sm:text-2xl font-black text-white tracking-tight leading-snug">Presensi Selfie Kamera & Geolokasi GPS</h2>
        <p class="text-[11.5px] sm:text-xs text-slate-300 mt-1.5 leading-relaxed">
            Presensi selfie <strong>wajib diambil melalui kamera aktif perangkat secara realtime</strong> dan berada di dalam radius area <strong>{{ $settings['school_name'] }}</strong> (maksimal {{ $settings['radius_meters'] }} meter). Dilarang mengunggah berkas foto dari galeri.
        </p>

        <!-- Session Schedule Badges -->
        <div class="mt-3.5 sm:mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-2.5 pt-3 border-t border-slate-800/80 text-xs">
            <div class="p-2.5 rounded-xl sm:rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    <div>
                        <span class="text-slate-300 font-semibold block text-[11px] sm:text-xs">Sesi Masuk:</span>
                        <span class="text-[10px] text-slate-400">Tepat Waktu s.d. {{ $settings['checkin_time'] }} WIB</span>
                    </div>
                </div>
                <span class="font-bold text-emerald-300 text-[11px] sm:text-xs">{{ $settings['start_time'] }} - {{ $settings['late_cutoff_time'] }} WIB</span>
            </div>
            <div class="p-2.5 rounded-xl sm:rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400 shrink-0"></span>
                    <div>
                        <span class="text-slate-300 font-semibold block text-[11px] sm:text-xs">Sesi Pulang:</span>
                        <span class="text-[10px] text-slate-400">Buka Pulang: {{ $settings['out_start'] }} WIB</span>
                    </div>
                </div>
                <span class="font-bold text-sky-300 text-[11px] sm:text-xs">{{ $settings['out_start'] }} - {{ $settings['out_end'] }} WIB</span>
            </div>
        </div>
    </div>

    <!-- Attendance Card -->
    <div class="w-full max-w-full min-w-0 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-3.5 xs:p-4 sm:p-6 md:p-8 overflow-hidden">
        
        @if(!$settings['is_enabled'])
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-200 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="min-w-0">
                    <span class="font-bold">Metode Presensi Selfie Kamera Sedang Dinonaktifkan</span>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5">Pihak sekolah saat ini menonaktifkan presensi selfie kamera. Silakan gunakan metode presensi lain (QR Code atau Kartu RFID) yang tersedia.</p>
                </div>
            </div>
        @endif

        @if(!empty($settings['is_holiday']))
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-rose-500/10 via-red-500/10 to-rose-500/10 border border-rose-300 dark:border-rose-900/50 text-rose-900 dark:text-rose-200 text-xs flex items-center gap-3 sm:gap-3.5 shadow-xs">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-extrabold text-xs sm:text-sm text-rose-700 dark:text-rose-300">🔴 Hari Libur: {{ $settings['holiday_name'] }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 uppercase">{{ $settings['holiday_type'] ?? 'Libur' }}</span>
                    </div>
                    <p class="text-[11px] sm:text-[11.5px] text-rose-800 dark:text-rose-300 mt-0.5">Hari ini adalah tanggal merah / hari libur sekolah resmi. Seluruh sesi presensi masuk dan pulang dinonaktifkan otomatis.</p>
                </div>
            </div>
        @elseif(isset($settings['is_active_day']) && !$settings['is_active_day'])
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-200 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="min-w-0">
                    <span class="font-bold">Hari Libur / Non-Aktif</span>
                    <p class="text-[11px] text-rose-700 dark:text-rose-300 mt-0.5">Hari ini sekolah dinyatakan libur atau bukan merupakan jadwal aktif presensi sekolah.</p>
                </div>
            </div>
        @endif

        <!-- Active Status Banner (Checkin vs Checkout) -->
        @php
            $hasCheckin = $existingAttendance 
                && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']) 
                && !empty($existingAttendance->time);
            $hasCheckout = $existingAttendance && !empty($existingAttendance->time_out);
            $isComplete = $hasCheckin && $hasCheckout;

            $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');
            $currentTimeStr = $nowJakarta->format('H:i');
            $outStartStr = substr($settings['out_start'] ?? ($schedule['out_start'] ?? '15:00'), 0, 5);
            $outEndStr = substr($settings['out_end'] ?? ($schedule['out_end'] ?? '17:30'), 0, 5);
            $inStartStr = substr($settings['start_time'] ?? ($schedule['in_start'] ?? '06:00'), 0, 5);
            $inLateStr = substr($settings['late_cutoff_time'] ?? ($schedule['in_late_cutoff'] ?? '07:30'), 0, 5);

            $isPrivileged = auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;

            $isCheckoutWindow = $currentTimeStr >= $outStartStr && $currentTimeStr <= $outEndStr;
            $isBeforeCheckout = $currentTimeStr < $outStartStr;
            $isAfterCheckout = $currentTimeStr > $outEndStr;

            $isCheckinWindow = $currentTimeStr >= $inStartStr && $currentTimeStr <= $inLateStr;
            $isBeforeCheckin = $currentTimeStr < $inStartStr;
            $isAfterCheckin = $currentTimeStr > $inLateStr;

            if ($isComplete) {
                $sessionMode = 'complete';
            } elseif ($hasCheckin) {
                if ($isBeforeCheckout && !$isPrivileged) {
                    $sessionMode = 'waiting_checkout';
                } elseif ($isCheckoutWindow || $isPrivileged) {
                    $sessionMode = 'checkout';
                } else {
                    $sessionMode = 'checkout_closed';
                }
            } else {
                if ($isBeforeCheckin && !$isPrivileged) {
                    $sessionMode = 'waiting_checkin';
                } elseif ($isCheckinWindow || $isPrivileged) {
                    $sessionMode = 'checkin';
                } else {
                    $sessionMode = 'checkin_late';
                }
            }
        @endphp

        @if($sessionMode === 'complete')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-950 dark:text-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 w-full min-w-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-500 text-white font-black flex items-center justify-center text-base sm:text-lg shadow-md shadow-emerald-500/20 shrink-0">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-xs sm:text-sm text-emerald-900 dark:text-emerald-100">Presensi Hari Ini Telah Lengkap!</h3>
                        <p class="text-[11px] sm:text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                            Masuk: <strong>{{ substr($existingAttendance->time, 0, 5) }} WIB</strong> • Pulang: <strong>{{ substr($existingAttendance->time_out, 0, 5) }} WIB</strong>
                        </p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold self-start sm:self-auto shrink-0">
                    Status: VALID ✓
                </div>
            </div>
        @elseif($sessionMode === 'waiting_checkout')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-950 dark:text-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-500 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        🔒
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-extrabold text-xs text-amber-950 dark:text-amber-100">SESI PRESENSI PULANG BELUM DIBUKA</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">
                                Masuk Selesai ({{ substr($existingAttendance->time, 0, 5) }} WIB)
                            </span>
                        </div>
                        <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-snug mt-1">
                            Anda telah presensi masuk pada pukul <strong>{{ substr($existingAttendance->time, 0, 5) }} WIB</strong>. Sesi presensi pulang hari <strong>{{ $settings['day_name'] }}</strong> baru dibuka pukul <strong>{{ $outStartStr }} s.d. {{ $outEndStr }} WIB</strong>.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-extrabold text-[11px] shrink-0 border border-amber-200 dark:border-amber-800 self-start sm:self-auto">
                    Buka {{ $outStartStr }} WIB
                </span>
            </div>
        @elseif($sessionMode === 'checkout')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-900/50 text-sky-950 dark:text-sky-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-sky-500 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        2
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-sky-950 dark:text-sky-100">SESI PRESENSI PULANG DIAKTIFKAN</h4>
                        <p class="text-[11px] text-sky-800 dark:text-sky-300 leading-snug">
                            Jam kepulangan sekolah telah tiba ({{ $outStartStr }} - {{ $outEndStr }} WIB). Silakan ambil foto selfie untuk merekam jam kepulangan Anda.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 font-extrabold text-[11px] shrink-0 border border-sky-200 dark:border-sky-800 self-start sm:self-auto">
                    Sesi Pulang Aktif
                </span>
            </div>
        @elseif($sessionMode === 'checkout_closed')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-950 dark:text-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-500 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        !
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-rose-950 dark:text-rose-100">SESI PRESENSI PULANG TELAH BERAKHIR</h4>
                        <p class="text-[11px] text-rose-800 dark:text-rose-300 leading-snug">
                            Batas akhir kepulangan pukul <strong>{{ $outEndStr }} WIB</strong> untuk hari ini telah berakhir.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold text-[11px] shrink-0 border border-rose-200 dark:border-rose-800 self-start sm:self-auto">
                    Tutup {{ $outEndStr }} WIB
                </span>
            </div>
        @elseif($sessionMode === 'waiting_checkin')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 text-indigo-950 dark:text-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        ⏰
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-indigo-950 dark:text-indigo-100">SESI PRESENSI MASUK BELUM DIBUKA</h4>
                        <p class="text-[11px] text-indigo-800 dark:text-indigo-300 leading-snug">
                            Jam presensi masuk hari <strong>{{ $settings['day_name'] }}</strong> baru dibuka pukul <strong>{{ $inStartStr }} WIB</strong>.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 font-extrabold text-[11px] shrink-0 border border-indigo-200 dark:border-indigo-800 self-start sm:self-auto">
                    Buka {{ $inStartStr }} WIB
                </span>
            </div>
        @elseif($sessionMode === 'checkin_late')
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-950 dark:text-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-600 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        ✕
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-rose-950 dark:text-rose-100">BATAS AKHIR PRESENSI MASUK TELAH LEWAT</h4>
                        <p class="text-[11px] text-rose-800 dark:text-rose-300 leading-snug">
                            Batas akhir presensi masuk pukul <strong>{{ $inLateStr }} WIB</strong> telah berakhir. Siswa yang belum hadir tercatat Alpa.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold text-[11px] shrink-0 border border-rose-200 dark:border-rose-800 self-start sm:self-auto">
                    Batas {{ $inLateStr }} WIB
                </span>
            </div>
        @else
            <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 text-indigo-950 dark:text-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 w-full min-w-0">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                        1
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-indigo-950 dark:text-indigo-100">SESI PRESENSI MASUK</h4>
                        <p class="text-[11px] text-indigo-800 dark:text-indigo-300 leading-snug">
                            Posisikan wajah Anda di dalam lingkaran panduan di bawah dan pastikan GPS mendeteksi Anda di area sekolah.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 font-extrabold text-[11px] shrink-0 border border-indigo-200 dark:border-indigo-800 self-start sm:self-auto">
                    Sesi Masuk Aktif
                </span>
            </div>
        @endif

        <!-- Geofence & GPS Status Bar -->
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 text-xs w-full min-w-0 max-w-full overflow-hidden">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 w-full sm:w-auto overflow-hidden">
                <div id="gps-indicator" class="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
                <div class="min-w-0 flex-1 overflow-hidden">
                    <span class="font-bold text-slate-700 dark:text-slate-200 text-xs block truncate" id="gps-status-text">Memindai koordinat GPS...</span>
                    <div class="text-[10.5px] sm:text-[11px] text-slate-400 dark:text-slate-500 truncate" id="gps-coords-text">Mencari sinyal lokasi perangkat</div>
                </div>
            </div>
            @if(empty($settings['is_geofence_enabled']))
                <div id="distance-badge" class="w-full sm:w-auto px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold text-center border border-emerald-200 dark:border-emerald-800 text-[11px] sm:text-xs shrink-0 truncate max-w-full">
                    ✓ Radius Bebas (Fitur Radius Nonaktif)
                </div>
            @else
                <div id="distance-badge" class="w-full sm:w-auto px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-center text-[11px] sm:text-xs shrink-0 truncate max-w-full">
                    Maks. Radius: {{ $settings['radius_meters'] }} meter
                </div>
            @endif
        </div>

        <!-- Video Stream Container with Face Overlay Guide -->
        <div class="relative w-full max-w-[280px] xs:max-w-[320px] sm:max-w-md mx-auto aspect-4/3 bg-slate-950 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-2 sm:border-4 border-slate-900 flex items-center justify-center shrink-0">
            <!-- Video Element (WebRTC Live Stream) -->
            <video id="webcam-video" autoplay playsinline muted class="w-full h-full max-w-full max-h-full object-cover transform -scale-x-100 block min-w-0"></video>
            
            <!-- Hidden Canvas for frame grab -->
            <canvas id="canvas-capture" class="hidden"></canvas>

            <!-- Face Oval Guide Overlay -->
            <div class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-3 xs:p-4 sm:p-6 max-w-full overflow-hidden">
                <div class="w-28 h-40 xs:w-36 xs:h-48 sm:w-44 sm:h-56 border-2 border-dashed border-emerald-400/80 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/10">
                    <span class="text-[9px] xs:text-[9.5px] sm:text-[10px] font-bold text-emerald-300 bg-slate-950/70 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full backdrop-blur-xs">
                        Posisikan Wajah Di Sini
                    </span>
                </div>
            </div>

            <!-- Permission / Loading Banner -->
            <div id="camera-prompt" class="absolute inset-0 max-w-full max-h-full overflow-hidden bg-slate-950/90 backdrop-blur-xs flex flex-col items-center justify-center p-3 xs:p-4 sm:p-6 text-center text-white">
                <div class="w-10 h-10 xs:w-12 xs:h-12 sm:w-14 sm:h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mb-2 xs:mb-2.5 sm:mb-3 shrink-0">
                    <svg class="w-5 h-5 xs:w-6 xs:h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-xs sm:text-sm mb-1 truncate max-w-full">Izin Kamera Diperlukan</h3>
                <p class="text-[10.5px] xs:text-[11px] sm:text-xs text-slate-400 mb-2.5 sm:mb-4 px-1 max-w-xs leading-relaxed">
                    Sistem akan mengaktifkan kamera depan perangkat Anda untuk pengambilan selfie realtime.
                </p>
                <button type="button" onclick="startCamera()" class="px-3.5 py-2 sm:px-5 sm:py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg transition active:scale-95 cursor-pointer shrink-0">
                    Aktifkan Kamera Sekarang
                </button>
            </div>
        </div>

        <!-- Action Button: AMBIL SELFIE -->
        <div class="mt-4 sm:mt-6 text-center w-full min-w-0 max-w-full flex flex-col items-center px-1">
            @if(!$settings['is_enabled'])
                <button type="button" disabled
                    class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>METODE SELFIE DINONAKTIFKAN</span>
                </button>
                <p class="text-xs text-amber-700 dark:text-amber-400 mt-2 font-medium">
                    Metode presensi selfie kamera sedang dinonaktifkan oleh administrator sekolah. Silakan gunakan metode lain.
                </p>
            @elseif(!$settings['is_active_day'])
                <button type="button" disabled
                    class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>PRESENSI TUTUP ({{ !empty($settings['is_holiday']) ? 'HARI LIBUR' : 'NON-AKTIF' }})</span>
                </button>
                <p class="text-xs text-rose-600 dark:text-rose-400 mt-2 font-medium">
                    {{ !empty($settings['is_holiday']) ? 'Hari ini adalah hari libur resmi: ' . $settings['holiday_name'] : 'Hari ini bukan merupakan hari aktif presensi sekolah.' }}
                </p>
            @elseif($sessionMode === 'complete')
                <button type="button" disabled
                    class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 font-black rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-base tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>PRESENSI HARI INI SELESAI</span>
                </button>
                <p class="text-xs text-slate-400 mt-2">
                    Anda telah melakukan presensi masuk dan pulang untuk hari ini.
                </p>
            @elseif($sessionMode === 'waiting_checkout')
                <div class="w-full flex flex-col items-center gap-2">
                    <button type="button" disabled
                        class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>🔒 SESI PULANG DIBUKA PUKUL {{ $outStartStr }} WIB</span>
                    </button>
                    @if($isPrivileged)
                        <button type="button" onclick="takeSelfie('checkout', true)" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-bold cursor-pointer">
                            ⚡ Mode Admin: Buka Paksa Sesi Presensi Pulang
                        </button>
                        <button type="button" onclick="takeSelfie('checkin', true)" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold cursor-pointer">
                            ⚡ Mode Admin: Paksa Sesi Presensi Masuk (Hadir)
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    Jam kepulangan diatur sesuai aturan sekolah: pukul {{ $outStartStr }} s.d. {{ $outEndStr }} WIB.
                </p>
            @elseif($sessionMode === 'checkout')
                <div class="w-full flex flex-col items-center gap-2">
                    <button type="button" id="btn-capture" onclick="takeSelfie('checkout')" disabled
                        class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black rounded-xl sm:rounded-2xl shadow-xl shadow-sky-600/25 text-xs sm:text-base tracking-wide transition transform active:scale-95 flex items-center justify-center gap-2 sm:gap-3 cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>AMBIL SELFIE & PRESENSI PULANG</span>
                    </button>
                    @if($isPrivileged)
                        <button type="button" onclick="takeSelfie('checkin', true)" class="text-xs text-indigo-500 dark:text-indigo-400 hover:underline font-semibold cursor-pointer">
                            Mode Admin: Paksa Sesi Presensi Masuk (Hadir)
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    Merekam waktu kepulangan dan foto selfie pulang tervalidasi otomatis.
                </p>
            @elseif($sessionMode === 'checkout_closed')
                <button type="button" disabled
                    class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>SESI PULANG TELAH DITUTUP ({{ $outEndStr }} WIB)</span>
                </button>
                <p class="text-xs text-slate-400 mt-2">
                    Batas akhir kepulangan hari ini telah berakhir pada pukul {{ $outEndStr }} WIB.
                </p>
            @elseif($sessionMode === 'waiting_checkin')
                <div class="w-full flex flex-col items-center gap-2">
                    <button type="button" disabled
                        class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>🔒 SESI MASUK DIBUKA PUKUL {{ $inStartStr }} WIB</span>
                    </button>
                    @if($isPrivileged)
                        <button type="button" onclick="takeSelfie('checkin', true)" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-bold cursor-pointer">
                            ⚡ Mode Admin: Buka Paksa Sesi Presensi Masuk
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    Sesi presensi masuk dibuka pukul {{ $inStartStr }} s.d. {{ $inLateStr }} WIB.
                </p>
            @elseif($sessionMode === 'checkin_late')
                <div class="w-full flex flex-col items-center gap-2">
                    <button type="button" disabled
                        class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold rounded-xl sm:rounded-2xl cursor-not-allowed text-xs sm:text-sm tracking-wide flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>BATAS PRESENSI MASUK BERAKHIR ({{ $inLateStr }} WIB)</span>
                    </button>
                    @if($isPrivileged)
                        <button type="button" onclick="takeSelfie('checkin', true)" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-bold cursor-pointer">
                            ⚡ Mode Admin: Buka Paksa Sesi Presensi Masuk
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    Batas akhir presensi masuk untuk sesi hari ini telah berakhir pada pukul {{ $inLateStr }} WIB.
                </p>
            @else
                <div class="w-full flex flex-col items-center gap-2">
                    <button type="button" id="btn-capture" onclick="takeSelfie('checkin')" disabled
                        class="w-full max-w-xs xs:max-w-sm sm:max-w-md px-4 sm:px-10 py-3 sm:py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 disabled:opacity-50 disabled:cursor-not-allowed text-slate-950 font-black rounded-xl sm:rounded-2xl shadow-xl shadow-emerald-500/20 text-xs sm:text-base tracking-wide transition transform active:scale-95 flex items-center justify-center gap-2 sm:gap-3 cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        <span>AMBIL SELFIE & PRESENSI MASUK</span>
                    </button>
                    @if($isPrivileged)
                        <button type="button" onclick="takeSelfie('checkout', true)" class="text-xs text-indigo-500 dark:text-indigo-400 hover:underline font-semibold cursor-pointer">
                            Mode Admin: Paksa Sesi Presensi Pulang
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    Kehadiran siswa di dalam area sekolah berstatus otomatis <strong>VALID</strong>.
                </p>
            @endif
        </div>

        <!-- Result Modal / Banner -->
        <div id="result-box" class="hidden mt-6 p-4 rounded-2xl text-xs leading-relaxed"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let videoStream = null;
    let currentLat = null;
    let currentLng = null;
    let currentAccuracy = null;
    let isWithinRadius = false;

    const schoolLat = {{ $settings['school_lat'] }};
    const schoolLng = {{ $settings['school_lng'] }};
    const allowedRadius = {{ $settings['radius_meters'] }};
    const isGeofenceEnabled = {{ !empty($settings['is_geofence_enabled']) ? 'true' : 'false' }};

    const video = document.getElementById('webcam-video');
    const canvas = document.getElementById('canvas-capture');
    const btnCapture = document.getElementById('btn-capture');
    const promptBox = document.getElementById('camera-prompt');
    const gpsIndicator = document.getElementById('gps-indicator');
    const gpsStatusText = document.getElementById('gps-status-text');
    const gpsCoordsText = document.getElementById('gps-coords-text');
    const distanceBadge = document.getElementById('distance-badge');
    const resultBox = document.getElementById('result-box');

    // Haversine Distance Formula in Meters
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c);
    }

    // Initialize GPS Geolocation
    function initGeolocation() {
        if (!navigator.geolocation) {
            if (gpsStatusText) gpsStatusText.innerText = "Perangkat tidak mendukung geolokasi GPS.";
            return;
        }

        navigator.geolocation.watchPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                currentAccuracy = pos.coords.accuracy;

                const distance = calculateDistance(schoolLat, schoolLng, currentLat, currentLng);
                if (gpsCoordsText) {
                    gpsCoordsText.innerText = `Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)} (Akurasi: ±${Math.round(currentAccuracy)}m)`;
                }

                if (!isGeofenceEnabled) {
                    isWithinRadius = true;
                    if (gpsIndicator) gpsIndicator.className = "w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-emerald-500 shrink-0";
                    if (gpsStatusText) gpsStatusText.innerText = `Lokasi GPS Terdeteksi (Jarak: ${distance}m • Fitur Radius Nonaktif)`;
                    if (distanceBadge) {
                        distanceBadge.className = "w-full sm:w-auto px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold text-center text-[11px] sm:text-xs shrink-0 border border-emerald-200 dark:border-emerald-800 truncate max-w-full";
                        distanceBadge.innerText = `Radius Bebas (${distance}m)`;
                    }
                } else if (distance <= allowedRadius) {
                    isWithinRadius = true;
                    if (gpsIndicator) gpsIndicator.className = "w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-emerald-500 shrink-0";
                    if (gpsStatusText) gpsStatusText.innerText = `Lokasi Tervalidasi: Berada di area sekolah (Jarak: ${distance}m)`;
                    if (distanceBadge) {
                        distanceBadge.className = "w-full sm:w-auto px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold text-center text-[11px] sm:text-xs shrink-0 border border-emerald-200 dark:border-emerald-800 truncate max-w-full";
                        distanceBadge.innerText = `Dalam Radius (${distance}m / ${allowedRadius}m)`;
                    }
                } else {
                    isWithinRadius = false;
                    if (gpsIndicator) gpsIndicator.className = "w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-rose-500 shrink-0";
                    if (gpsStatusText) gpsStatusText.innerText = `Di Luar Area Sekolah: Jarak Anda ${distance}m dari sekolah`;
                    if (distanceBadge) {
                        distanceBadge.className = "w-full sm:w-auto px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold text-center text-[11px] sm:text-xs shrink-0 border border-rose-200 dark:border-rose-900/60 truncate max-w-full";
                        distanceBadge.innerText = `Di Luar Radius (${distance}m / ${allowedRadius}m)`;
                    }
                }

                checkReadyState();
            },
            (err) => {
                if (gpsIndicator) gpsIndicator.className = "w-4 h-4 rounded-full bg-rose-500 shrink-0";
                if (gpsStatusText) gpsStatusText.innerText = "Gagal mengambil lokasi GPS: " + err.message;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // Start Realtime Camera
    async function startCamera() {
        if (!video) return;
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            video.srcObject = videoStream;
            if (promptBox) promptBox.classList.add('hidden');
            checkReadyState();
        } catch (err) {
            alert("Gagal membuka kamera perangkat: " + err.message + ". Pastikan izin kamera telah diberikan.");
        }
    }

    function checkReadyState() {
        if (btnCapture && videoStream && videoStream.active) {
            btnCapture.disabled = false;
        }
    }

    // Capture Frame Directly from Live Stream to Canvas
    async function takeSelfie(sessionType = 'checkin', bypass = false) {
        if (!videoStream) {
            alert("Kamera belum aktif!");
            return;
        }

        if (isGeofenceEnabled && (!currentLat || !currentLng)) {
            alert("Koordinat GPS belum terdeteksi. Pastikan GPS aktif dan berikan izin lokasi.");
            return;
        }

        // Draw current video frame to canvas
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        
        // Mirror the image horizontally for natural selfie
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64Image = canvas.toDataURL('image/jpeg', 0.85);

        if (btnCapture) {
            btnCapture.disabled = true;
            btnCapture.innerHTML = `
                <svg class="animate-spin w-5 h-5 ${sessionType === 'checkout' ? 'text-white' : 'text-slate-950'}" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                MEMVERIFIKASI PRESENSI...
            `;
        }

        try {
            const response = await fetch("{{ route('attendance.selfie.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    selfie_image: base64Image,
                    latitude: currentLat,
                    longitude: currentLng,
                    accuracy: currentAccuracy,
                    device_info: navigator.userAgent,
                    liveness_score: 1.00,
                    session_type: sessionType,
                    bypass_time: bypass
                })
            });

            const res = await response.json();

            resultBox.classList.remove('hidden');
            if (response.ok && res.success) {
                resultBox.className = "mt-5 sm:mt-6 p-4 sm:p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200";
                resultBox.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center shrink-0">✓</div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-sm text-emerald-950 dark:text-emerald-100">PRESENSI ${res.session === 'checkout' ? 'PULANG' : 'MASUK'} BERHASIL!</h4>
                            <p class="text-xs text-emerald-800 dark:text-emerald-300">${res.message}</p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-1">Status: <strong>${res.status} (VALID)</strong> • Waktu: ${res.time_out || res.time} WIB • Jarak: ${res.distance}m</p>
                        </div>
                    </div>
                `;
                btnCapture.innerHTML = "PRESENSI BERHASIL ✓";
                setTimeout(() => {
                    window.location.reload();
                }, 1800);
            } else {
                resultBox.className = "mt-5 sm:mt-6 p-4 sm:p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200";
                resultBox.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-500 text-white font-bold flex items-center justify-center shrink-0">✕</div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-sm text-rose-950 dark:text-rose-100">PRESENSI DITOLAK</h4>
                            <p class="text-xs text-rose-800 dark:text-rose-300 font-medium">${res.message || "Validasi presensi gagal."}</p>
                        </div>
                    </div>
                `;
                btnCapture.disabled = false;
                btnCapture.innerHTML = "COBA LAGI";
            }
        } catch (err) {
            resultBox.classList.remove('hidden');
            resultBox.className = "mt-5 sm:mt-6 p-4 sm:p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200";
            resultBox.innerHTML = `<strong>Error:</strong> Gagal terhubung ke server. ${err.message}`;
            btnCapture.disabled = false;
            btnCapture.innerHTML = "COBA LAGI";
        }
    }

    // Auto-init on load
    window.addEventListener('DOMContentLoaded', () => {
        initGeolocation();
        @if(!empty($settings['is_enabled']))
            startCamera();
        @endif
    });
</script>
@endpush
