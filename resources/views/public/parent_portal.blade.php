<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Orang Tua Siswa - {{ $student ? $student->name . ' | ' : '' }}{{ $schoolName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @if(!empty($schoolSetting?->logo_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $schoolSetting->logo_path) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-100">

    <!-- Top Navigation Bar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-0.5 shadow-md shadow-indigo-500/20 shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-white font-black text-sm">
                        @if(!empty($schoolSetting?->logo_path))
                            <img src="{{ asset('storage/' . $schoolSetting->logo_path) }}" alt="Logo" class="w-full h-full object-contain p-1 rounded-[10px]">
                        @else
                            1LE
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold text-white tracking-tight flex items-center gap-1.5">
                        Portal Orang Tua
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Tanpa Login
                        </span>
                    </h1>
                    <p class="text-[11px] text-slate-400 truncate max-w-xs sm:max-w-md">{{ $schoolName }}</p>
                </div>
            </div>

            <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 text-xs font-bold border border-slate-700 flex items-center gap-1.5 transition">
                <span>Login Petugas / Guru</span>
                <span class="text-slate-400">→</span>
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">

        <!-- Search Bar Section (Prominent) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-7 shadow-2xl backdrop-blur-xl">
            <div class="max-w-2xl mx-auto text-center mb-5">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 inline-flex items-center gap-1.5 mb-2">
                    <span>👨‍👩‍👧</span> Layanan Informasi & Pemantauan Siswa Terpadu
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-white">Pantau Kehadiran & Perkembangan Anak</h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Cukup masukkan nomor <strong>NISN</strong> (Nomor Induk Siswa Nasional) untuk melihat data presensi harian, foto masuk/pulang, 7 kebiasaan anak hebat, dan tata tertib.
                </p>
            </div>

            <form action="{{ route('parent.portal') }}" method="GET" class="max-w-xl mx-auto">
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <input type="text" name="nisn" value="{{ $nisn }}" required autofocus
                            placeholder="Ketik 10 Digit NISN (Contoh: 0089234811)"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-950/80 border border-slate-700 rounded-2xl text-sm sm:text-base font-bold text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition tracking-wide">
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition shrink-0 flex items-center justify-center gap-2 text-sm">
                        <span>🔍 Cek Data Anak</span>
                    </button>
                </div>
                <div class="flex items-center justify-center gap-4 mt-2.5 text-[11px] text-slate-400">
                    <span>💡 Tidak tahu NISN? Lihat pada Raport / Ijazah SMP anak</span>
                </div>
            </form>
        </div>

        @if($errorMessage)
            <!-- Error Notification -->
            <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs sm:text-sm flex items-start gap-3">
                <span class="text-lg">⚠️</span>
                <div class="flex-1">
                    <div class="font-bold">Data Siswa Tidak Ditemukan</div>
                    <div class="mt-0.5 text-rose-300/90">{{ $errorMessage }}</div>
                </div>
            </div>
        @endif

        @if($student)
            <!-- Student Header Profile Card -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-7 border border-indigo-900/50 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-4 min-w-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 p-1 shrink-0 shadow-lg shadow-indigo-500/30">
                        @if(!empty($student->photo))
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover rounded-[12px]">
                        @else
                            <div class="w-full h-full bg-slate-900 rounded-[12px] flex items-center justify-center font-black text-xl sm:text-2xl text-white">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[11px] font-bold border border-indigo-500/30">
                                Kelas {{ $student->schoolClass?->name ?? 'Belum ada kelas' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-semibold border border-emerald-500/30">
                                ● Siswa Aktif
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white mt-1 truncate">{{ $student->name }}</h2>
                        <div class="flex items-center gap-3 text-xs text-slate-300 mt-1 flex-wrap">
                            <span>NISN: <strong>{{ $student->nisn }}</strong></span>
                            <span>•</span>
                            <span>NIS: <strong>{{ $student->nis }}</strong></span>
                            @if($student->schoolClass?->homeroomTeacher)
                                <span>•</span>
                                <span>Wali Kelas: <strong>{{ $student->schoolClass->homeroomTeacher->name }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto">
                    <a href="{{ route('parent.portal') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold border border-slate-700 transition">
                        ✕ Ganti Siswa
                    </a>
                    <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-indigo-600/30">
                        <span>🖨️ Cetak Ringkasan</span>
                    </button>
                </div>
            </div>

            <!-- Real-time Presence Card (2 Sessions: Masuk & Pulang) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Presensi Hari Ini ({{ \Carbon\Carbon::today('Asia/Jakarta')->translatedFormat('l, d F Y') }})
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pemantauan kedatangan dan kepulangan anak secara langsung</p>
                    </div>
                    @if($todayAttendance)
                        <span class="px-3 py-1 rounded-xl text-xs font-extrabold self-start sm:self-auto {{ $todayAttendance->status === 'HADIR' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($todayAttendance->status === 'TERLAMBAT' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30') }}">
                            STATUS: {{ $todayAttendance->status }}
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 self-start sm:self-auto">
                            BELUM ADA REKAMAN HARI INI
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sesi 1: Presensi Masuk -->
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🌅</span> SESI 1: PRESENSI MASUK
                                </span>
                                @if($todayAttendance)
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-800 text-slate-300">
                                        {{ $todayAttendance->method }}
                                    </span>
                                @endif
                            </div>

                            @if($todayAttendance)
                                <div class="flex items-center gap-4">
                                    <div class="text-3xl font-black text-white">
                                        {{ substr($todayAttendance->time, 0, 5) }} <span class="text-xs font-normal text-slate-400">WIB</span>
                                    </div>
                                    <div>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-extrabold {{ $todayAttendance->status === 'HADIR' ? 'bg-emerald-500 text-white' : ($todayAttendance->status === 'TERLAMBAT' ? 'bg-amber-500 text-white' : 'bg-blue-500 text-white') }}">
                                            {{ $todayAttendance->status }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Selfie & Location Details -->
                                <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                    <div class="text-slate-400 flex items-center gap-2">
                                        @if($todayAttendance->selfie_path)
                                            <a href="{{ asset('storage/' . $todayAttendance->selfie_path) }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 rounded-lg font-semibold flex items-center gap-1 transition">
                                                <span>📷</span> Lihat Foto Masuk
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-500">Tanpa Foto Masuk</span>
                                        @endif

                                        @if($todayAttendance->latitude && $todayAttendance->longitude)
                                            <a href="https://www.google.com/maps?q={{ $todayAttendance->latitude }},{{ $todayAttendance->longitude }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-emerald-300 rounded-lg font-semibold flex items-center gap-1 transition">
                                                <span>📍</span> Peta Lokasi
                                            </a>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400">Tercatat di sistem</span>
                                </div>
                            @else
                                <div class="py-4 text-center text-slate-400 text-xs">
                                    <p class="font-semibold text-slate-300">Belum ada rekaman presensi masuk</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Anak Anda belum melakukan scan kartu RFID, selfie, atau diabsen guru hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sesi 2: Presensi Pulang -->
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🌇</span> SESI 2: PRESENSI PULANG
                                </span>
                                @if($todayAttendance && $todayAttendance->out_method)
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-800 text-slate-300">
                                        {{ $todayAttendance->out_method }}
                                    </span>
                                @endif
                            </div>

                            @if($todayAttendance && $todayAttendance->time_out)
                                <div class="flex items-center gap-4">
                                    <div class="text-3xl font-black text-white">
                                        {{ substr($todayAttendance->time_out, 0, 5) }} <span class="text-xs font-normal text-slate-400">WIB</span>
                                    </div>
                                    <div>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-extrabold bg-blue-500 text-white">
                                            SUDAH PULANG
                                        </span>
                                    </div>
                                </div>

                                <!-- Selfie & Location Details -->
                                <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                    <div class="text-slate-400 flex items-center gap-2">
                                        @if($todayAttendance->out_selfie_path)
                                            <a href="{{ asset('storage/' . $todayAttendance->out_selfie_path) }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 rounded-lg font-semibold flex items-center gap-1 transition">
                                                <span>📷</span> Lihat Foto Pulang
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-500">Tanpa Foto Pulang</span>
                                        @endif

                                        @if($todayAttendance->out_latitude && $todayAttendance->out_longitude)
                                            <a href="https://www.google.com/maps?q={{ $todayAttendance->out_latitude }},{{ $todayAttendance->out_longitude }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-emerald-300 rounded-lg font-semibold flex items-center gap-1 transition">
                                                <span>📍</span> Peta Pulang
                                            </a>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400">Tercatat di sistem</span>
                                </div>
                            @else
                                <div class="py-4 text-center text-slate-400 text-xs">
                                    <p class="font-semibold text-slate-300">Belum ada rekaman presensi pulang</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Siswa belum melakukan presensi kepulangan sekolah hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3 Summary Indicator Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- 1. Prestasi Anak -->
                <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Prestasi Terverifikasi</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm font-bold border border-amber-500/30">★</span>
                    </div>
                    <div class="text-3xl font-black text-amber-400 mt-3">{{ $student->totalAchievementPoints() }} Poin</div>
                    <p class="text-xs text-slate-400 mt-1 font-medium">{{ $student->reward_status['level'] }}</p>
                    <div class="text-[11px] text-slate-400 mt-2">{{ $achievements->count() }} Piagam/Penghargaan Terdata</div>
                </div>

                <!-- 2. Catatan Pelanggaran -->
                <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kedisiplinan & Tata Tertib</span>
                        <span class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm font-bold border border-rose-500/30">!</span>
                    </div>
                    <div class="text-3xl font-black text-rose-400 mt-3">{{ $student->totalViolationPoints() }} Poin</div>
                    <p class="text-xs text-slate-400 mt-1 font-medium">{{ $student->guidance_status['level'] }}</p>
                    <div class="text-[11px] text-slate-400 mt-2">{{ $violations->count() }} Catatan Pelanggaran</div>
                </div>

                <!-- 3. Jurnal 7 Kebiasaan Anak Hebat -->
                @php
                    $completedHabitsCount = $todayHabitLogs->where('is_completed', true)->count();
                    $habitPct = count($habits) > 0 ? round(($completedHabitsCount / count($habits)) * 100) : 0;
                @endphp
                <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">7 Kebiasaan Hari Ini</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm font-bold border border-emerald-500/30">✓</span>
                    </div>
                    <div class="text-3xl font-black text-emerald-400 mt-3">{{ $completedHabitsCount }}/{{ count($habits) }}</div>
                    <p class="text-xs text-slate-400 mt-1 font-medium">{{ $habitPct }}% terlaksana hari ini</p>
                    <div class="w-full bg-slate-800 h-2 rounded-full mt-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Detail Container (Presensi, 7 Kebiasaan, Prestasi, Pelanggaran) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-7 shadow-xl" x-data="{ tab: 'attendance' }">
                <!-- Tab Headers -->
                <div class="flex items-center gap-2 border-b border-slate-800 pb-4 overflow-x-auto">
                    <button type="button" @click="tab = 'attendance'"
                        :class="tab === 'attendance' ? 'bg-indigo-600 text-white font-extrabold shadow-md shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-white font-semibold'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2">
                        <span>📅 Riwayat Presensi (30 Hari)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $recentAttendances->count() }}</span>
                    </button>

                    <button type="button" @click="tab = 'habits'"
                        :class="tab === 'habits' ? 'bg-emerald-600 text-white font-extrabold shadow-md shadow-emerald-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-white font-semibold'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2">
                        <span>🌱 7 Kebiasaan Anak Hebat</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $completedHabitsCount }}/{{ count($habits) }}</span>
                    </button>

                    <button type="button" @click="tab = 'achievements'"
                        :class="tab === 'achievements' ? 'bg-amber-600 text-white font-extrabold shadow-md shadow-amber-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-white font-semibold'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2">
                        <span>🏆 Prestasi & Penghargaan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $achievements->count() }}</span>
                    </button>

                    <button type="button" @click="tab = 'violations'"
                        :class="tab === 'violations' ? 'bg-rose-600 text-white font-extrabold shadow-md shadow-rose-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-white font-semibold'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2">
                        <span>📋 Tata Tertib & Pembinaan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $violations->count() }}</span>
                    </button>
                </div>

                <!-- Tab 1: Attendance History -->
                <div x-show="tab === 'attendance'" class="mt-5 space-y-4">
                    <!-- Attendance Stats Pills -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-center">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Hadir</div>
                            <div class="text-xl font-black text-emerald-400 mt-0.5">{{ $attendanceStats['hadir'] }}</div>
                        </div>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-center">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Terlambat</div>
                            <div class="text-xl font-black text-amber-400 mt-0.5">{{ $attendanceStats['terlambat'] }}</div>
                        </div>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-center">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Izin</div>
                            <div class="text-xl font-black text-blue-400 mt-0.5">{{ $attendanceStats['izin'] }}</div>
                        </div>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-center">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Sakit</div>
                            <div class="text-xl font-black text-purple-400 mt-0.5">{{ $attendanceStats['sakit'] }}</div>
                        </div>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-center">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Tanpa Keterangan</div>
                            <div class="text-xl font-black text-rose-400 mt-0.5">{{ $attendanceStats['alfa'] }}</div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-800">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="p-3.5">Tanggal</th>
                                    <th class="p-3.5">Jam Masuk</th>
                                    <th class="p-3.5">Jam Pulang</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5">Metode</th>
                                    <th class="p-3.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                @forelse($recentAttendances as $att)
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="p-3.5 font-bold text-white whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            {{ $att->time ? substr($att->time, 0, 5) . ' WIB' : '-' }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            {{ $att->time_out ? substr($att->time_out, 0, 5) . ' WIB' : '-' }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $att->status === 'HADIR' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($att->status === 'TERLAMBAT' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30') }}">
                                                {{ $att->status }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap text-slate-400">
                                            {{ $att->method }}
                                        </td>
                                        <td class="p-3.5 text-slate-400">
                                            {{ $att->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                            Belum ada rekaman riwayat presensi dalam 30 hari terakhir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: 7 Habits Checklist -->
                <div x-show="tab === 'habits'" x-cloak class="mt-5 space-y-3">
                    <p class="text-xs text-slate-400">
                        Program pembiasaan karakter positif anak hebat setiap hari:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @forelse($habits as $habit)
                            @php
                                $log = $todayHabitLogs->get($habit->id);
                                $isDone = $log && $log->is_completed;
                            @endphp
                            <div class="p-4 rounded-2xl border transition {{ $isDone ? 'bg-emerald-950/30 border-emerald-800/80 text-emerald-100' : 'bg-slate-950/60 border-slate-800 text-slate-300' }}">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl font-black text-xs flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $habit->order_number }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="font-extrabold text-sm text-white truncate">{{ $habit->title }}</h4>
                                            @if($isDone)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0">
                                                    ✓ Terlaksana
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 shrink-0">
                                                    Belum Diceklis
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $habit->description }}</p>
                                        <div class="text-[10px] text-slate-400 mt-2">
                                            Waktu: {{ substr($habit->start_time, 0, 5) }} - {{ substr($habit->end_time, 0, 5) }} WIB
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-6 text-center text-slate-400 text-xs italic">
                                Belum ada data 7 kebiasaan yang dikonfigurasi.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab 3: Achievements -->
                <div x-show="tab === 'achievements'" x-cloak class="mt-5 space-y-3">
                    <div class="space-y-3">
                        @forelse($achievements as $ach)
                            <div class="p-4 rounded-2xl bg-amber-950/20 border border-amber-900/50 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-black text-sm text-amber-300">{{ $ach->title }}</span>
                                    <span class="font-black text-amber-400 text-sm px-2.5 py-0.5 rounded-lg bg-amber-500/20 border border-amber-500/30">
                                        +{{ $ach->points }} Poin
                                    </span>
                                </div>
                                <p class="text-slate-300 leading-relaxed">{{ $ach->description }}</p>
                                <div class="flex items-center gap-3 text-[10px] text-slate-400 mt-2">
                                    <span>Tanggal: {{ $ach->date ? $ach->date->format('d M Y') : '-' }}</span>
                                    <span>•</span>
                                    <span>Tingkat: {{ $ach->level ?? 'Sekolah' }}</span>
                                    <span>•</span>
                                    <span class="text-emerald-400 font-bold">Terverifikasi</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">
                                Belum ada catatan prestasi atau penghargaan yang terdata untuk siswa ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab 4: Violations & Guidance -->
                <div x-show="tab === 'violations'" x-cloak class="mt-5 space-y-4">
                    <!-- Violations List -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Pelanggaran Tata Tertib</h4>
                        <div class="space-y-2.5">
                            @forelse($violations as $viol)
                                <div class="p-3.5 rounded-2xl bg-rose-950/20 border border-rose-900/50 text-xs">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-rose-300">{{ $viol->category?->name ?? 'Pelanggaran Disiplin' }}</span>
                                        <span class="font-black text-rose-400 text-sm px-2 py-0.5 rounded-lg bg-rose-500/20 border border-rose-500/30">
                                            {{ $viol->points }} Poin
                                        </span>
                                    </div>
                                    <p class="text-slate-300 leading-relaxed">{{ $viol->description }}</p>
                                    <div class="text-[10px] text-slate-400 mt-2">
                                        Tanggal: {{ $viol->date ? $viol->date->format('d M Y') : '-' }} • Penanganan: {{ $viol->action_taken ?? 'Pembinaan edukatif' }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs bg-slate-950/40 rounded-2xl border border-slate-800">
                                    <span class="text-emerald-400 font-bold">Alhamdulillah, tidak ada catatan pelanggaran tata tertib.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Guidance Records -->
                    @if($guidanceRecords->isNotEmpty())
                        <div class="pt-3 border-t border-slate-800">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Konseling & Pendampingan Siswa</h4>
                            <div class="space-y-2.5">
                                @foreach($guidanceRecords as $rec)
                                    <div class="p-3.5 rounded-2xl bg-indigo-950/20 border border-indigo-900/50 text-xs">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-bold text-indigo-300">{{ $rec->title }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30">
                                                {{ $rec->status }}
                                            </span>
                                        </div>
                                        <p class="text-slate-300">{{ $rec->notes }}</p>
                                        <div class="text-[10px] text-slate-400 mt-2">
                                            Konselor: {{ $rec->counselor?->name ?? 'Guru BK / Wali Kelas' }} • {{ $rec->date ? $rec->date->format('d M Y') : '-' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-900/40 mt-12 py-6 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ $schoolName }}. Sistem Informasi Manajemen Sekolah Terpadu.</p>
            <p class="text-[11px] text-slate-400 mt-1">Portal Pemantauan Orang Tua Tanpa Login Berbasis NISN Siswa</p>
        </div>
    </footer>

</body>
</html>
