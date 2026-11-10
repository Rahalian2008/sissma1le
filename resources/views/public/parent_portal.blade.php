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
    <!-- Alpine.js (Local & Offline Capable with CDN Fallback) -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
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
                <!-- Tab Headers (Toolbar Terstruktur & Rapi: Fitur Utama + Layanan Terpadu) -->
                <div class="border-b border-slate-800 pb-5 space-y-3.5">
                    <!-- Kelompok 1: Fitur Utama (Presensi, 7 Kebiasaan, Prestasi & Tata Tertib) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                Menu Utama Siswa
                            </span>
                            <span class="text-[10px] text-slate-500 font-medium hidden sm:inline">Presensi, Karakter, Prestasi & Kedisiplinan</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Tab 1: Presensi (Fitur Awal) -->
                            <button type="button" @click="tab = 'attendance'" onclick="switchPortalTab('attendance')" data-portal-tab-btn="attendance"
                                :class="tab === 'attendance' ? 'bg-indigo-600 text-white font-extrabold shadow-md shadow-indigo-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>📅 Riwayat Presensi</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $recentAttendances->count() }}</span>
                            </button>

                            <!-- Tab 2: 7 Kebiasaan (Fitur Awal) -->
                            <button type="button" @click="tab = 'habits'" onclick="switchPortalTab('habits')" data-portal-tab-btn="habits"
                                :class="tab === 'habits' ? 'bg-emerald-600 text-white font-extrabold shadow-md shadow-emerald-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>🌱 7 Kebiasaan Anak Hebat</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $completedHabitsCount }}/{{ count($habits) }}</span>
                            </button>

                            <!-- Tab 3: Prestasi (Fitur Awal) -->
                            <button type="button" @click="tab = 'achievements'" onclick="switchPortalTab('achievements')" data-portal-tab-btn="achievements"
                                :class="tab === 'achievements' ? 'bg-amber-600 text-white font-extrabold shadow-md shadow-amber-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>🏆 Prestasi & Penghargaan</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $achievements->count() }}</span>
                            </button>

                            <!-- Tab 4: Tata Tertib (Fitur Awal) -->
                            <button type="button" @click="tab = 'violations'" onclick="switchPortalTab('violations')" data-portal-tab-btn="violations"
                                :class="tab === 'violations' ? 'bg-rose-600 text-white font-extrabold shadow-md shadow-rose-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>📋 Tata Tertib & Pembinaan</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $violations->count() }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kelompok 2: Layanan Akademik & Pengembangan Diri -->
                    <div class="pt-2.5 border-t border-slate-800/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                Layanan Akademik & Pengembangan Diri
                            </span>
                            <span class="text-[10px] text-slate-500 font-medium hidden sm:inline">Tahfidz, Rapor Kurnas, Psikotes, Pustaka & KBM</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Tab 5: Ibadah & Tahfidz (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'worship'" onclick="switchPortalTab('worship')" data-portal-tab-btn="worship"
                                :class="tab === 'worship' ? 'bg-teal-600 text-white font-extrabold shadow-md shadow-teal-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>🕌 Ibadah & Tahfidz</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $completedSurahsCount ?? 0 }}/{{ $targetSurahsCount ?? 37 }}</span>
                            </button>

                            <!-- Tab 6: Rapor Kurnas (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'kurnas'" onclick="switchPortalTab('kurnas')" data-portal-tab-btn="kurnas"
                                :class="tab === 'kurnas' ? 'bg-violet-600 text-white font-extrabold shadow-md shadow-violet-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>📝 Rapor Kurnas</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $kurnasScores->count() }} Mapel</span>
                            </button>

                            <!-- Tab 7: Bimbingan & Psikotes (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'counseling'" onclick="switchPortalTab('counseling')" data-portal-tab-btn="counseling"
                                :class="tab === 'counseling' ? 'bg-fuchsia-600 text-white font-extrabold shadow-md shadow-fuchsia-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>🧠 Bimbingan & Psikotes</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $bkPsychologicalResults->isNotEmpty() ? 'Hasil Ada' : 'Layanan BK' }}</span>
                            </button>

                            <!-- Tab 8: Perpustakaan (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'library'" onclick="switchPortalTab('library')" data-portal-tab-btn="library"
                                :class="tab === 'library' ? 'bg-yellow-600 text-white font-extrabold shadow-md shadow-yellow-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>📚 Pustaka & Literasi</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $libraryLoans->count() }}</span>
                            </button>

                            <!-- Tab 9: Jadwal & KBM (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'schedule'" onclick="switchPortalTab('schedule')" data-portal-tab-btn="schedule"
                                :class="tab === 'schedule' ? 'bg-cyan-600 text-white font-extrabold shadow-md shadow-cyan-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>⏰ Jadwal & Agenda KBM</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $classSchedules->count() }}</span>
                            </button>

                            @if(!empty($hasExtracurriculars) && $hasExtracurriculars)
                            <!-- Tab 10: Ekstrakurikuler (Fitur Tambahan Baru) -->
                            <button type="button" @click="tab = 'extracurricular'" onclick="switchPortalTab('extracurricular')" data-portal-tab-btn="extracurricular"
                                :class="tab === 'extracurricular' ? 'bg-sky-600 text-white font-extrabold shadow-md shadow-sky-600/30 border-transparent' : 'bg-slate-800/80 text-slate-300 hover:text-white font-medium border-slate-700/60 hover:bg-slate-800'"
                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 border cursor-pointer">
                                <span>⚽ Ekstrakurikuler</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">{{ $student->extracurriculars->count() }}</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab 1: Attendance History -->
                <div x-show="tab === 'attendance'" data-portal-tab-content="attendance" class="mt-5 space-y-4">
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
                            <div class="text-xl font-black text-rose-400 mt-0.5">{{ $attendanceStats['alpa'] ?? ($attendanceStats['alfa'] ?? 0) }}</div>
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
                <div x-show="tab === 'habits'" data-portal-tab-content="habits" x-cloak class="mt-5 space-y-3">
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
                <div x-show="tab === 'achievements'" data-portal-tab-content="achievements" x-cloak class="mt-5 space-y-3">
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
                <div x-show="tab === 'violations'" data-portal-tab-content="violations" x-cloak class="mt-5 space-y-4">
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

                <!-- Tab 5: Ibadah & Tahfidz Quran -->
                <div x-show="tab === 'worship'" data-portal-tab-content="worship" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-teal-950/20 border border-teal-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 uppercase tracking-wider">
                                Pembiasaan Keagamaan
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Presensi Ibadah Berjamaah & Tahfidz Al-Qur'an</h4>
                            <p class="text-xs text-slate-400">Pemantauan shalat dhuha, shalat dzuhur berjamaah, dan capaian hafalan Juz 30.</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs text-slate-400 block">Progres Hafalan Kelas {{ $targetGrade ?? 'X' }}</span>
                            <span class="text-xl font-black text-teal-400">{{ $completedSurahsCount ?? 0 }}/{{ $targetSurahsCount ?? 37 }} Surat Tuntas</span>
                        </div>
                    </div>

                    <!-- Ringkasan Ibadah -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 bg-slate-950/60 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] font-bold text-teal-400 flex items-center justify-between">
                                <span>🌅 Shalat Dhuha</span>
                                <span>{{ $worshipStats['dhuha']['rate'] ?? 0 }}%</span>
                            </div>
                            <div class="text-lg font-black text-white mt-1">{{ $worshipStats['dhuha']['hadir'] ?? 0 }} Sesi Hadir</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Dari total {{ $worshipStats['dhuha']['total'] ?? 0 }} jadwal</div>
                        </div>

                        <div class="p-3.5 bg-slate-950/60 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] font-bold text-emerald-400 flex items-center justify-between">
                                <span>🕌 Shalat Dzuhur / Jumat</span>
                                <span>{{ $worshipStats['dzuhur']['rate'] ?? 0 }}%</span>
                            </div>
                            <div class="text-lg font-black text-white mt-1">{{ $worshipStats['dzuhur']['hadir'] ?? 0 }} Sesi Hadir</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Berjamaah di Masjid Sekolah</div>
                        </div>

                        <div class="p-3.5 bg-slate-950/60 border border-slate-800 rounded-2xl">
                            <div class="text-[11px] font-bold text-cyan-400 flex items-center justify-between">
                                <span>📖 Tahfidz Juz 30</span>
                                <span>{{ $tahfidzProgressPct ?? 0 }}%</span>
                            </div>
                            <div class="text-lg font-black text-white mt-1">{{ $completedSurahsCount ?? 0 }} Surat Lulus</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Target {{ $targetSurahsCount ?? 37 }} Surat</div>
                        </div>
                    </div>

                    <!-- Riwayat Setoran Tahfidz -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Riwayat Setoran Hafalan Qur'an Terbaru</h4>
                        @if(!empty($allStudentTahfidzRecords) && $allStudentTahfidzRecords->isNotEmpty())
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                @foreach($allStudentTahfidzRecords->take(10) as $tahfidz)
                                    <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-bold text-white">{{ $tahfidz->surah_name }} (Surat ke-{{ $tahfidz->surah_number }})</div>
                                            <div class="text-[11px] text-slate-400">Ayat {{ $tahfidz->start_ayah }} - {{ $tahfidz->end_ayah }} • Penguji: {{ $tahfidz->examiner?->name ?? 'Ustadz/Guru PAI' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $tahfidz->overall_grade === 'Lulus' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                                {{ $tahfidz->overall_grade }}
                                            </span>
                                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $tahfidz->deposit_date ? \Carbon\Carbon::parse($tahfidz->deposit_date)->format('d M Y') : '-' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400 text-xs bg-slate-950/40 rounded-2xl border border-slate-800">
                                Belum ada catatan setoran tahfidz hafalan Al-Qur'an ananda.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 6: Rapor Kurnas & Akademik -->
                <div x-show="tab === 'kurnas'" data-portal-tab-content="kurnas" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-violet-950/20 border border-violet-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-violet-500/20 text-violet-300 border border-violet-500/30 uppercase tracking-wider">
                                Kurikulum Nasional
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Capaian Hasil Belajar & Rapor Akademik</h4>
                            <p class="text-xs text-slate-400">Nilai sumatif akhir mata pelajaran, capaian TP optimal, dan projek P5.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($kurnasAverage)
                                <div class="px-3.5 py-1.5 rounded-xl bg-violet-600/30 border border-violet-500/40 text-center">
                                    <span class="text-[10px] text-violet-300 block uppercase font-bold">Rata-Rata</span>
                                    <span class="text-lg font-black text-white">{{ $kurnasAverage }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Nilai Mapel -->
                    @if($kurnasScores->isNotEmpty())
                        <div class="overflow-x-auto rounded-2xl border border-slate-800">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                    <tr>
                                        <th class="p-3">No</th>
                                        <th class="p-3">Mata Pelajaran & Guru</th>
                                        <th class="p-3 text-center">Nilai</th>
                                        <th class="p-3">Capaian Kompetensi Optimal</th>
                                        <th class="p-3">Perlu Peningkatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                    @foreach($kurnasScores as $idx => $sc)
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="p-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                            <td class="p-3">
                                                <div class="font-bold text-white">{{ $sc->courseClass?->subject?->name ?? 'Mata Pelajaran' }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $sc->courseClass?->teacher?->name ?? 'Guru Pengampu' }}</div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ (float)$sc->score >= 75 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                                    {{ number_format((float)$sc->score, 1) }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-xs text-slate-200 leading-relaxed max-w-xs">
                                                {{ $sc->optimal_description ?: 'Menunjukkan penguasaan kompetensi pembelajaran dengan baik.' }}
                                            </td>
                                            <td class="p-3 text-xs text-slate-400 leading-relaxed max-w-xs">
                                                {{ $sc->improvement_description ?: 'Perlu pendampingan untuk penguatan materi berkelanjutan.' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-950/40 rounded-2xl border border-slate-800">
                            Data nilai rapor kurnas sedang diproses oleh dewan guru untuk periode semester ini.
                        </div>
                    @endif

                    <!-- Catatan Wali Kelas & Projek P5 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                <span>✍️</span> Catatan Wali Kelas:
                            </span>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                {{ $kurnasHomeroom?->note ?? 'Ananda menunjukkan keaktifan dan semangat belajar yang sangat baik. Tingkatkan terus prestasi dan pertahankan budi pekerti luhur.' }}
                            </p>
                            <div class="pt-2 border-t border-slate-800/80 text-[11px] text-slate-400 flex items-center justify-between">
                                <span>Status: <strong class="text-emerald-400">{{ $kurnasHomeroom?->status_promotion ?? 'Aktif KBM' }}</strong></span>
                                <span>Wali Kelas: <strong class="text-white">{{ $homeroomTeacher?->name ?? '-' }}</strong></span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                <span>🎨</span> Projek Penguatan Profil Pelajar Pancasila (P5)
                            </span>
                            @if($kurnasP5Projects->isNotEmpty())
                                @foreach($kurnasP5Projects->take(2) as $p5)
                                    <div class="text-xs">
                                        <span class="font-bold text-indigo-300">{{ $p5->project?->title ?? 'Projek P5' }}</span>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $p5->process_notes ?: 'Aktif berpartisipasi dalam perencanaan dan pembuatan karya kelompok secara mandiri dan gotong royong.' }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-slate-400">Ananda aktif mengikuti seluruh rangkaian projek profil pelajar Pancasila di sekolah.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab 7: Bimbingan & Psikotes -->
                <div x-show="tab === 'counseling'" data-portal-tab-content="counseling" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-fuchsia-950/20 border border-fuchsia-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-fuchsia-500/20 text-fuchsia-300 border border-fuchsia-500/30 uppercase tracking-wider">
                                Bimbingan Konseling & Karir
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Pemetaan Potensi Diri & Hasil Tes Psikotes</h4>
                            <p class="text-xs text-slate-400">Membantu orang tua memahami kecenderungan bakat, minat karir, dan arahan studi ananda.</p>
                        </div>
                        @if($bkCounselor && $bkCounselor->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $bkCounselor->phone) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-500 text-white text-xs font-bold flex items-center gap-1.5 shrink-0 transition">
                                <span>💬 Konsultasi Guru BK</span>
                            </a>
                        @endif
                    </div>

                    @if($bkPsychologicalResults->isNotEmpty())
                        @php $psi = $bkPsychologicalResults->first(); @endphp
                        <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-fuchsia-400">Hasil Asesmen Minat Bakat (Holland RIASEC)</span>
                                    <h4 class="text-sm font-black text-white">{{ $psi->profile_label ?? 'Profil Kepribadian Siswa' }}</h4>
                                </div>
                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-fuchsia-500/20 text-fuchsia-300 border border-fuchsia-500/30">
                                    {{ $psi->holland_code ?? ($psi->dominant_dimension ?? 'RIASEC') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="space-y-1">
                                    <span class="font-bold text-slate-400">Ringkasan Analisis Karakter:</span>
                                    <p class="text-slate-200 leading-relaxed font-medium">
                                        {{ $psi->analysis_summary ?: 'Ananda memiliki daya nalar logis yang baik dan potensi eksplorasi kreatif dalam bidang terapan.' }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-bold text-slate-400">Rekomendasi Jurusan Kuliah / Studi Lanjut:</span>
                                    <p class="text-slate-200 leading-relaxed font-medium">
                                        {{ $psi->study_recommendations ?: 'Sains Terapan, Teknik Informatika, Komunikasi Bisnis, atau Kependidikan.' }}
                                    </p>
                                </div>
                            </div>

                            @if(!empty($psi->career_recommendations) && is_array($psi->career_recommendations))
                                <div class="pt-2 border-t border-slate-800">
                                    <span class="text-[11px] font-bold text-fuchsia-300 block mb-1.5">Pilihan Karir & Profesi Masa Depan:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($psi->career_recommendations as $cr)
                                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 text-fuchsia-200 border border-fuchsia-800/40 text-[11px] font-bold">
                                                ✓ {{ $cr }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-950/40 rounded-2xl border border-slate-800">
                            Asesmen psikotes dan minat bakat ananda sedang diagendakan oleh guru BK sekolah.
                        </div>
                    @endif
                </div>

                <!-- Tab 8: Literasi & Pustaka -->
                <div x-show="tab === 'library'" data-portal-tab-content="library" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-yellow-950/20 border border-yellow-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 uppercase tracking-wider">
                                Perpustakaan Sekolah
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Aktivitas Peminjaman Buku & Gerakan Literasi</h4>
                            <p class="text-xs text-slate-400">Pantau buku bacaan yang sedang dipinjam dan riwayat literasi ananda.</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs text-slate-400 block">Total Peminjaman</span>
                            <span class="text-xl font-black text-yellow-400">{{ $libraryLoans->count() }} Buku</span>
                        </div>
                    </div>

                    @if($libraryLoans->isNotEmpty())
                        <div class="overflow-x-auto rounded-2xl border border-slate-800">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                    <tr>
                                        <th class="p-3">Judul Buku & Penulis</th>
                                        <th class="p-3">Tgl Pinjam</th>
                                        <th class="p-3">Jatuh Tempo</th>
                                        <th class="p-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                    @foreach($libraryLoans as $loan)
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="p-3">
                                                <div class="font-bold text-white">{{ $loan->book?->title ?? 'Buku Perpustakaan' }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $loan->book?->author ?? '-' }}</div>
                                            </td>
                                            <td class="p-3 whitespace-nowrap">{{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</td>
                                            <td class="p-3 whitespace-nowrap">{{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</td>
                                            <td class="p-3 text-center whitespace-nowrap">
                                                @if(in_array(strtoupper($loan->status), ['RETURNED', 'DIKEMBALIKAN']))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                        ✓ Dikembalikan
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                        Sedang Dipinjam
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-950/40 rounded-2xl border border-slate-800">
                            Belum ada riwayat peminjaman buku perpustakaan yang tercatat.
                        </div>
                    @endif
                </div>

                <!-- Tab 9: Jadwal & Agenda KBM -->
                <div x-show="tab === 'schedule'" data-portal-tab-content="schedule" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-cyan-950/20 border border-cyan-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 uppercase tracking-wider">
                                Pembelajaran Kelas {{ $student->schoolClass?->name ?? '' }}
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Jadwal Pelajaran & Agenda Belajar Harian</h4>
                            <p class="text-xs text-slate-400">Informasi mata pelajaran dan topik KBM yang diajarkan bapak/ibu guru di kelas.</p>
                        </div>
                        @if($homeroomTeacher && $homeroomTeacher->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $homeroomTeacher->phone) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-1.5 shrink-0 transition">
                                <span>💬 WA Wali Kelas</span>
                            </a>
                        @endif
                    </div>

                    <!-- Jadwal Pelajaran Mingguan -->
                    @if($classSchedules->isNotEmpty())
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Jadwal Pelajaran Rombel</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                @foreach($classSchedules as $cs)
                                    <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-indigo-500/20 text-indigo-300">
                                                {{ $cs->day_of_week }}
                                            </span>
                                            <span class="text-[10px] font-mono text-slate-400">{{ substr($cs->start_time, 0, 5) }} - {{ substr($cs->end_time, 0, 5) }}</span>
                                        </div>
                                        <div class="font-bold text-xs text-white">{{ $cs->subject?->name ?? 'Mapel' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $cs->teacher?->name ?? 'Guru Pengampu' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Agenda Pembelajaran Terkini -->
                    @if($classAgendas->isNotEmpty())
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Agenda KBM Kelas Terkini</h4>
                            <div class="space-y-2">
                                @foreach($classAgendas->take(5) as $agenda)
                                    <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-white">{{ $agenda->subject }}: {{ $agenda->learning_topic }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $agenda->date ? $agenda->date->format('d M Y') : '-' }}</span>
                                        </div>
                                        <p class="text-slate-300 leading-snug">{{ $agenda->activities_summary }}</p>
                                        <div class="text-[10px] text-slate-400">Guru: {{ $agenda->teacher_name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Tab 10: Ekstrakurikuler -->
                @if(!empty($hasExtracurriculars) && $hasExtracurriculars)
                <div x-show="tab === 'extracurricular'" data-portal-tab-content="extracurricular" x-cloak class="mt-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-sky-950/20 border border-sky-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30 uppercase tracking-wider">
                                Pengembangan Minat & Bakat
                            </span>
                            <h4 class="text-sm font-extrabold text-white mt-1">Kegiatan Ekstrakurikuler Ananda</h4>
                            <p class="text-xs text-slate-400">Daftar keanggotaan dan presensi latihan kegiatan ekstrakurikuler sekolah.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($student->extracurriculars as $ex)
                            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-white text-sm">{{ $ex->name }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        {{ $ex->pivot->role ?? 'Anggota' }}
                                    </span>
                                </div>
                                <div class="text-slate-400">Kategori: {{ $ex->category ?? 'Ekstrakurikuler' }}</div>
                                <div class="text-[11px] text-slate-400 pt-1 border-t border-slate-800/80">
                                    Pembina: <strong class="text-slate-200">{{ $ex->teacher?->name ?? 'Pembina Ekskul' }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
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

    <!-- Script Pengendali Tab Responsif & Bulletproof (Vanilla JS + Alpine Fallback) -->
    <script>
        window.switchPortalTab = function(targetTab) {
            window.activePortalTab = targetTab;

            // 1. Sync with Alpine if available
            try {
                const rootEl = document.querySelector('[x-data]');
                if (rootEl && window.Alpine) {
                    const data = Alpine.$data(rootEl);
                    if (data) {
                        data.tab = targetTab;
                    }
                }
            } catch (e) {}

            // 2. Direct DOM show/hide for 100% guarantee in all browsers
            const panels = document.querySelectorAll('[data-portal-tab-content]');
            panels.forEach(function(panel) {
                const pTab = panel.getAttribute('data-portal-tab-content');
                if (pTab === targetTab) {
                    panel.classList.remove('hidden');
                    panel.style.display = 'block';
                    panel.removeAttribute('x-cloak');
                } else {
                    panel.classList.add('hidden');
                    panel.style.display = 'none';
                }
            });

            // 3. Tab buttons style update
            const tabThemeColors = {
                'attendance': 'bg-indigo-600',
                'habits': 'bg-emerald-600',
                'achievements': 'bg-amber-600',
                'violations': 'bg-rose-600',
                'worship': 'bg-teal-600',
                'kurnas': 'bg-violet-600',
                'counseling': 'bg-fuchsia-600',
                'library': 'bg-yellow-600',
                'schedule': 'bg-cyan-600',
                'extracurricular': 'bg-sky-600'
            };

            const allColorClasses = ['bg-indigo-600', 'bg-emerald-600', 'bg-amber-600', 'bg-rose-600', 'bg-teal-600', 'bg-violet-600', 'bg-fuchsia-600', 'bg-yellow-600', 'bg-cyan-600', 'bg-sky-600'];

            const btns = document.querySelectorAll('[data-portal-tab-btn]');
            btns.forEach(function(btn) {
                const bTab = btn.getAttribute('data-portal-tab-btn');
                allColorClasses.forEach(function(cls) { btn.classList.remove(cls); });
                if (bTab === targetTab) {
                    btn.classList.remove('bg-slate-800/80', 'text-slate-300', 'text-slate-400', 'border-slate-700/60');
                    const activeColor = tabThemeColors[targetTab] || 'bg-indigo-600';
                    btn.classList.add(activeColor, 'text-white', 'font-extrabold', 'shadow-md', 'border-transparent');
                } else {
                    btn.classList.remove('text-white', 'font-extrabold', 'shadow-md', 'border-transparent');
                    btn.classList.add('bg-slate-800/80', 'text-slate-300', 'font-medium', 'border-slate-700/60');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.switchPortalTab('attendance');
        });
    </script>
</body>
</html>
