<!DOCTYPE html>
<html lang="id" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kios TV Display Layar &bull; {{ $data['profile']['name'] ?? ($school['name'] ?? 'SMAN 1 Lengkong') }}</title>
    
    <!-- Fonts & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#040711',
                        cardbg: '#0c1222',
                        bordercolor: 'rgba(255, 255, 255, 0.08)',
                    }
                }
            }
        }
    </script>
    <style>
        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            background-color: #040711;
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #f1f5f9;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .glass-panel {
            background: rgba(12, 18, 34, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .glow-teal { box-shadow: 0 0 30px -5px rgba(20, 184, 166, 0.25); }
        .glow-indigo { box-shadow: 0 0 30px -5px rgba(99, 102, 241, 0.25); }
        .glow-emerald { box-shadow: 0 0 30px -5px rgba(16, 185, 129, 0.25); }
        .glow-amber { box-shadow: 0 0 30px -5px rgba(245, 158, 11, 0.25); }
        .glow-gold { box-shadow: 0 0 35px -3px rgba(234, 179, 8, 0.35); }

        /* Slim Scrollbar for Internal Feeds */
        .kiosk-scroll::-webkit-scrollbar { width: 4px; }
        .kiosk-scroll::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.4); }
        .kiosk-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 9999px; }

        /* Smooth Marquee Ticker (Slowed down for comfortable reading) */
        @keyframes tickerMove {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-ticker-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: tickerMove 85s linear infinite;
        }
        .animate-ticker-marquee:hover {
            animation-play-state: paused;
        }

        /* Clean Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="h-screen max-h-screen overflow-hidden flex flex-col justify-between text-slate-100 antialiased selection:bg-teal-500 selection:text-white">

    <!-- AMBIENT BACKGROUND GLOW LIGHTS -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>

    <!-- 1. TOP HEADER BAR (Compact ~56px) -->
    <header class="relative z-30 h-14 sm:h-16 shrink-0 glass-panel border-b border-white/10 px-3 sm:px-5 flex items-center justify-between gap-2.5 sm:gap-4 shadow-2xl overflow-hidden">
        <!-- Brand Identity (Fixed, never shrinks or wraps into sibling) -->
        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-teal-500 via-indigo-500 to-sky-400 p-0.5 shadow-lg shadow-teal-500/20 shrink-0">
                <div id="kioskLogoContainer" class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center overflow-hidden">
                    @if(!empty($data['school']['logo']))
                        <img src="{{ asset('storage/' . $data['school']['logo']) }}" alt="Logo" class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
                    @else
                        <span class="text-xs sm:text-sm font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-sky-300">S1L</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="flex items-center gap-2">
                    <h1 id="kioskSchoolName" class="text-xs sm:text-sm md:text-base font-black tracking-tight text-white uppercase whitespace-nowrap">
                        {{ $data['profile']['name'] ?? ($school['name'] ?? 'SMA NEGERI 1 LENGKONG') }}
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> LIVE KIOSK
                    </span>
                </div>
                <p class="text-[9.5px] sm:text-[10px] text-slate-400 font-medium truncate max-w-[200px] sm:max-w-[260px] leading-tight">
                    Layar Informasi Digital Terpadu &bull; TV Display
                </p>
            </div>
        </div>

        <!-- Slide Switcher Pills (Desktop & Tablet) -->
        <div class="hidden md:flex items-center gap-1 bg-slate-950/80 border border-white/10 rounded-xl p-1 text-xs shrink min-w-0 max-w-[50vw] xl:max-w-none overflow-x-auto no-scrollbar mx-1">
            <button type="button" onclick="goToSlide(0)" id="pill-slide-0" class="slide-nav-pill px-2 py-1 rounded-lg font-bold transition flex items-center gap-1 bg-teal-500 text-slate-950 shadow-sm whitespace-nowrap text-[11px]">
                <span>1. Presensi</span>
            </button>
            <button type="button" onclick="goToSlide(1)" id="pill-slide-1" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>2. Ibadah</span>
            </button>
            <button type="button" onclick="goToSlide(2)" id="pill-slide-2" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>3. Tahfidz</span>
            </button>
            <button type="button" onclick="goToSlide(3)" id="pill-slide-3" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>4. Prestasi</span>
            </button>
            <button type="button" onclick="goToSlide(4)" id="pill-slide-4" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>5. Piket</span>
            </button>
            <button type="button" onclick="goToSlide(5)" id="pill-slide-5" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>6. Profil</span>
            </button>
            <button type="button" onclick="goToSlide(6)" id="pill-slide-6" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>7. Berita</span>
            </button>
            <button type="button" onclick="goToSlide(7)" id="pill-slide-7" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>8. Agenda</span>
            </button>
            <button type="button" onclick="goToSlide(8)" id="pill-slide-8" class="slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]">
                <span>9. Guru &amp; TU</span>
            </button>
        </div>

        <!-- Controls: Play/Pause, Prev/Next, Fullscreen, Clock -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <!-- Ekosistem Kios Quick Launch (Layar Besar) -->
            <div class="hidden 2xl:flex items-center gap-1 bg-slate-950/80 border border-white/10 rounded-xl p-1 text-xs">
                <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition flex items-center gap-1" title="Buka Kios Buku Tamu">
                    <span>👥 Tamu</span>
                </a>
                <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition flex items-center gap-1" title="Buka Kios Perpustakaan">
                    <span>📚 Perpus</span>
                </a>
                <a href="{{ route('attendance.kiosk-satpam') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition flex items-center gap-1" title="Buka Kios Satpam">
                    <span>🛡️ Satpam</span>
                </a>
                <a href="{{ route('website.home') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition flex items-center gap-1" title="Portal Web Utama">
                    <span>🌐 Web</span>
                </a>
            </div>

            <!-- Slide Control Buttons -->
            <div class="flex items-center bg-slate-950/80 border border-white/10 rounded-xl p-1 text-xs">
                <button type="button" onclick="prevSlide()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Slide Sebelumnya (Panah Kiri)">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" id="btnPlayPause" onclick="togglePlayPause()" class="px-2 py-1 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 font-bold transition flex items-center gap-1" title="Jeda / Lanjutkan Auto Slide">
                    <span id="playPauseIcon">⏸️</span>
                    <span id="slideCountdownLabel" class="font-mono text-[11px] text-teal-300">16s</span>
                </button>
                <button type="button" onclick="nextSlide()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Slide Berikutnya (Panah Kanan)">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Fullscreen Button -->
            <button type="button" onclick="toggleFullscreen()" class="p-2 rounded-xl bg-slate-900/90 border border-white/10 hover:border-teal-500/50 text-slate-300 hover:text-white transition" title="Layar Penuh (F11)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>

            <!-- Live Digital Clock -->
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950/90 border border-indigo-500/30 rounded-xl px-2.5 sm:px-3 py-1 text-right shadow-inner shrink-0">
                <div id="liveClock" class="text-xs sm:text-sm font-mono font-black text-teal-300 tracking-wider">--:--:-- WIB</div>
                <div id="liveDate" class="text-[9px] text-slate-400 font-semibold tracking-wide capitalize truncate max-w-[110px] sm:max-w-[140px]">--</div>
            </div>
        </div>
    </header>

    <!-- 2. TOP COUNTDOWN PROGRESS BAR (15 seconds slide duration) -->
    <div class="relative z-20 w-full bg-slate-950 h-1 shrink-0 overflow-hidden">
        <div id="slideProgressBar" class="h-full bg-gradient-to-r from-teal-400 via-indigo-500 to-emerald-400 transition-all duration-200 ease-linear" style="width: 0%;"></div>
    </div>

    <!-- 3. MAIN SLIDE DECK VIEWPORT (Strictly fits 1 screen, flex-1 min-h-0) -->
    <main class="relative z-10 flex-1 min-h-0 w-full overflow-hidden p-2.5 sm:p-4 lg:p-5">

        <!-- ========================================================================= -->
        <!-- SLIDE 0: PRESENSI KEHADIRAN SISWA TERPADU & JADWAL HARI INI              -->
        <!-- ========================================================================= -->
        <section id="slide-0" class="kiosk-slide h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-sm">01</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Presensi Kehadiran Harian &amp; Jadwal Sekolah</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Ringkasan kehadiran siswa real-time dan jadwal bel masuk-pulang hari ini</p>
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 font-mono hidden sm:flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                    <span>Sinkronisasi Otomatis 15s</span>
                </div>
            </div>

            <!-- Slide Main Grid: Left Statistics & Schedule, Right Live Tap Feed -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left Column: KPI Gauge & Status Breakdown & Bell Schedule (7 Cols) -->
                <div class="lg:col-span-7 flex flex-col justify-between gap-3 min-h-0 overflow-hidden">
                    <!-- Top Metric Cards Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 shrink-0">
                        <!-- Percentage Big Card -->
                        <div class="col-span-2 sm:col-span-1 p-3 rounded-2xl glass-card border border-teal-500/30 flex flex-col justify-between glow-teal">
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider">Persentase Kehadiran</span>
                            <div class="my-1 flex items-baseline gap-2">
                                <span id="dailyPercentage" class="text-3xl sm:text-4xl font-black text-white font-mono">{{ $data['daily']['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-slate-900/80 rounded-full h-2 overflow-hidden border border-white/5">
                                <div id="dailyPercentageBar" class="bg-gradient-to-r from-teal-400 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $data['daily']['percentage'] }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1">Total <strong id="dailyTotalStudents" class="text-white">{{ $data['daily']['total_students'] }}</strong> Siswa Aktif</span>
                        </div>

                        <!-- Hadir & Terlambat -->
                        <div class="p-3 rounded-2xl glass-card border border-emerald-500/20 flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Hadir Tepat</span>
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="my-1">
                                <span id="countHadir" class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $data['daily']['hadir'] }}</span>
                                <span class="text-xs text-slate-400 font-sans">Siswa</span>
                            </div>
                            <span class="text-[10px] text-emerald-300/80 font-medium">Tepat Waktu</span>
                        </div>

                        <div class="p-3 rounded-2xl glass-card border border-amber-500/20 flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Terlambat</span>
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            </div>
                            <div class="my-1">
                                <span id="countTerlambat" class="text-2xl sm:text-3xl font-black text-amber-300 font-mono">{{ $data['daily']['terlambat'] }}</span>
                                <span class="text-xs text-slate-400 font-sans">Siswa</span>
                            </div>
                            <span class="text-[10px] text-amber-300/80 font-medium">Dalam Batas Toleransi</span>
                        </div>

                        <!-- Izin, Sakit, Alpa -->
                        <div class="p-2.5 rounded-xl glass-card border border-sky-500/20 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-sky-400 uppercase block">Izin</span>
                                <span id="countIzin" class="text-lg font-black text-white font-mono">{{ $data['daily']['izin'] }}</span>
                            </div>
                            <span class="text-lg">📄</span>
                        </div>

                        <div class="p-2.5 rounded-xl glass-card border border-indigo-500/20 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-indigo-400 uppercase block">Sakit</span>
                                <span id="countSakit" class="text-lg font-black text-white font-mono">{{ $data['daily']['sakit'] }}</span>
                            </div>
                            <span class="text-lg">💊</span>
                        </div>

                        <div class="p-2.5 rounded-xl glass-card border border-rose-500/20 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-rose-400 uppercase block">Tanpa Keterangan (Alpa)</span>
                                <span id="countAlpa" class="text-lg font-black text-rose-400 font-mono">{{ $data['daily']['alpa'] }}</span>
                            </div>
                            <span class="text-lg">⚠️</span>
                        </div>
                    </div>

                    <!-- Bottom Schedule Information Card -->
                    <div class="flex-1 min-h-0 p-3 sm:p-4 rounded-2xl glass-card border border-white/10 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-white/5">
                            <div class="flex items-center gap-2">
                                <span class="text-base">⏰</span>
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Jadwal Presensi &amp; Jam Bel Hari Ini</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ ($data['schedules']['presensi_utama']['is_holiday'] ?? false) ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                {{ ($data['schedules']['presensi_utama']['is_holiday'] ?? false) ? 'HARI LIBUR' : 'HARI AKTIF SEKOLAH' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                            <div class="p-2 rounded-xl bg-slate-900/60 border border-white/5">
                                <span class="text-[10px] text-slate-400 block font-bold">WAKTU MASUK</span>
                                <span class="font-mono font-bold text-teal-300 text-sm">s.d. 06:30 WIB</span>
                                <span class="text-[9px] text-slate-500 block mt-0.5">Tepat waktu</span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900/60 border border-white/5">
                                <span class="text-[10px] text-slate-400 block font-bold">BATAS TERLAMBAT</span>
                                <span class="font-mono font-bold text-amber-300 text-sm">11:00 WIB</span>
                                <span class="text-[9px] text-slate-500 block mt-0.5">Melewati batas = Alpa</span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900/60 border border-white/5">
                                <span class="text-[10px] text-slate-400 block font-bold">WAKTU PULANG</span>
                                <span class="font-mono font-bold text-indigo-300 text-sm">15:00 - 21:00 WIB</span>
                                <span class="text-[9px] text-slate-500 block mt-0.5">Presensi kepulangan siswa</span>
                            </div>
                        </div>

                        <div class="mt-2 p-2 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-between text-[11px] text-teal-200">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span>💡 Siswa belum tap presensi hingga batas waktu otomatis tersinkronisasi sebagai Alpa.</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Live Tap Feed (5 Cols) -->
                <div class="lg:col-span-5 flex flex-col h-full min-h-0 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span class="text-xs font-black text-white uppercase tracking-wider">Aktivitas Presensi Terkini</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">Live Tap Log</span>
                    </div>

                    <div id="dailyRecentLogs" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pr-1">
                        @forelse($data['daily']['recent'] as $rec)
                            <div class="bg-slate-900/70 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between hover:border-teal-500/30 transition">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($rec['name'], 0, 1) }}
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-xs text-white truncate">{{ $rec['name'] }}</div>
                                        <div class="text-[10px] text-slate-400 truncate">{{ $rec['class'] }} &bull; {{ $rec['method'] }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $rec['status'] === 'HADIR' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                        {{ $rec['status'] }}
                                    </span>
                                    <span class="text-[11px] font-mono font-bold text-slate-300">{{ $rec['time'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic">
                                Belum ada aktivitas presensi masuk hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 1: PRESENSI IBADAH BERJAMAAH & KARAKTER 7 KAIH                       -->
        <!-- ========================================================================= -->
        <section id="slide-1" class="kiosk-slide hidden h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-black text-sm">02</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Presensi Ibadah Berjamaah &amp; 7 Kebiasaan Anak Indonesia Hebat</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Monitoring Shalat Dhuha, Shalat Dzuhur, Presensi Keputrian, dan pembiasaan karakter 7 KAIH</p>
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 font-mono hidden sm:flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
                    <span>Sesi Ibadah Terjadwal</span>
                </div>
            </div>

            <!-- Top Grid: 3 Worship Panels (Dhuha, Dzuhur, Keputrian) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 shrink-0">
                <!-- Card 1: Shalat Dhuha Bersama -->
                <div class="p-3 sm:p-3.5 rounded-2xl glass-card border border-teal-500/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🕌</span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Shalat Dhuha Bersama</h3>
                        </div>
                        <span id="dhuhaStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black {{ $data['dhuha']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                            {{ $data['dhuha']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between my-1">
                        <span class="text-[10px] text-slate-400">{{ $data['dhuha']['schedule'] }}</span>
                        <div class="text-right">
                            <span id="dhuhaTotalToday" class="text-xl sm:text-2xl font-black text-teal-300 font-mono">{{ $data['dhuha']['total_today'] }}</span>
                            <span class="text-[10px] text-slate-400">Jamaah</span>
                        </div>
                    </div>
                    <div id="dhuhaRecentLogs" class="max-h-24 overflow-y-auto kiosk-scroll space-y-1.5 mt-1 border-t border-white/5 pt-1.5">
                        @forelse(collect($data['dhuha']['recent'])->take(3) as $dw)
                            <div class="text-[11px] flex items-center justify-between text-slate-300 bg-slate-900/50 px-2 py-1 rounded-lg">
                                <span class="truncate mr-2 font-medium">{{ $dw['name'] }} <span class="text-slate-500">({{ $dw['class'] }})</span></span>
                                <span class="font-mono text-teal-400 shrink-0">{{ $dw['time'] }}</span>
                            </div>
                        @empty
                            <div class="text-slate-500 text-[10px] italic py-1">Belum ada jamaah dhuha yang tercatat.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Card 2: Shalat Dzuhur / Jum'at Berjamaah -->
                <div class="p-3 sm:p-3.5 rounded-2xl glass-card border border-indigo-500/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🕌</span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Shalat Dzuhur / Jum'at</h3>
                        </div>
                        <span id="dzuhurStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black {{ $data['dzuhur']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                            {{ $data['dzuhur']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between my-1">
                        <span class="text-[10px] text-slate-400">{{ $data['dzuhur']['schedule'] }}</span>
                        <div class="text-right">
                            <span id="dzuhurTotalToday" class="text-xl sm:text-2xl font-black text-indigo-300 font-mono">{{ $data['dzuhur']['total_today'] }}</span>
                            <span class="text-[10px] text-slate-400">Jamaah</span>
                        </div>
                    </div>
                    <div id="dzuhurRecentLogs" class="max-h-24 overflow-y-auto kiosk-scroll space-y-1.5 mt-1 border-t border-white/5 pt-1.5">
                        @forelse(collect($data['dzuhur']['recent'])->take(3) as $dz)
                            <div class="text-[11px] flex items-center justify-between text-slate-300 bg-slate-900/50 px-2 py-1 rounded-lg">
                                <span class="truncate mr-2 font-medium">{{ $dz['name'] }} <span class="text-slate-500">({{ $dz['class'] }})</span></span>
                                <span class="font-mono text-indigo-400 shrink-0">{{ $dz['time'] }}</span>
                            </div>
                        @empty
                            <div class="text-slate-500 text-[10px] italic py-1">Belum ada jamaah dzuhur yang tercatat.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Card 3: Presensi Keputrian -->
                <div class="p-3 sm:p-3.5 rounded-2xl glass-card border border-rose-500/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-base">✨</span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Presensi Keputrian</h3>
                        </div>
                        <span id="keputrianStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black {{ $data['keputrian']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                            {{ $data['keputrian']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between my-1">
                        <span class="text-[10px] text-slate-400">{{ $data['keputrian']['schedule'] }}</span>
                        <div class="text-right">
                            <span id="keputrianTotalToday" class="text-xl sm:text-2xl font-black text-rose-300 font-mono">{{ $data['keputrian']['total_today'] }}</span>
                            <span class="text-[10px] text-slate-400">Siswi</span>
                        </div>
                    </div>
                    <div id="keputrianRecentLogs" class="max-h-24 overflow-y-auto kiosk-scroll space-y-1.5 mt-1 border-t border-white/5 pt-1.5">
                        @forelse(collect($data['keputrian']['recent'])->take(3) as $kp)
                            <div class="text-[11px] flex items-center justify-between text-slate-300 bg-slate-900/50 px-2 py-1 rounded-lg">
                                <span class="truncate mr-2 font-medium">{{ $kp['name'] }} <span class="text-slate-500">({{ $kp['class'] }})</span></span>
                                <span class="font-mono text-rose-400 shrink-0">{{ $kp['time'] }}</span>
                            </div>
                        @empty
                            <div class="text-slate-500 text-[10px] italic py-1">Belum ada kehadiran keputrian yang tercatat.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Bottom Panel: 7 Kebiasaan Anak Indonesia Hebat (7 KAIH) -->
            <div class="flex-1 min-h-0 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🌟</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                                Pembiasaan Karakter &bull; 7 Kebiasaan Anak Indonesia Hebat (7 KAIH)
                            </h3>
                            <p class="text-[10px] text-slate-400">Pencatatan pembiasaan harian siswa untuk membangun profil pelajar berintegritas</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span id="habitsCompletedToday" class="text-sm font-black text-emerald-400 font-mono">{{ $data['habits']['completed_today'] ?? 0 }}</span>
                        <span class="text-[10px] text-slate-400 block">Aktivitas Terisi Hari Ini</span>
                    </div>
                </div>

                <!-- 7 Habits Horizontal Cards Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5 flex-1 min-h-0 overflow-y-auto kiosk-scroll">
                    @foreach($data['habits']['list'] as $hb)
                        <div class="p-2.5 rounded-xl bg-slate-900/70 border border-white/5 flex flex-col justify-between hover:border-teal-500/40 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-lg">{{ $hb['icon'] }}</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-white/5 text-slate-400">#{{ $hb['order'] }}</span>
                            </div>
                            <div class="my-1">
                                <h4 class="text-xs font-bold text-white truncate">{{ $hb['name'] }}</h4>
                                <p class="text-[9px] text-slate-400 line-clamp-1">{{ $hb['tagline'] }}</p>
                            </div>
                            <div class="pt-1.5 border-t border-white/5 flex items-center justify-between text-[10px]">
                                <span class="text-slate-500">Hari ini:</span>
                                <span class="font-bold text-emerald-400 font-mono">+{{ $hb['completed'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 2: BUKU KONTROL DIGITAL TAHFIDZ AL-QUR'AN JUZ 30                    -->
        <!-- ========================================================================= -->
        <section id="slide-2" class="kiosk-slide hidden h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-black text-sm">03</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Buku Kontrol Digital: Setor Hafalan Juz 30 Al-Qur'an</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Target kelulusan surat per jenjang kelas X, XI, XII dan santri dengan capaian terbaik</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs font-mono">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-white/10 text-slate-300">
                        Setoran Hari Ini: <strong id="tahfidzTotalToday" class="text-emerald-400">{{ $data['tahfidz']['total_today'] }}</strong>
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
                        Lulus Hari Ini: <strong id="tahfidzPassedToday" class="text-white">{{ $data['tahfidz']['passed_today'] }}</strong>
                    </span>
                </div>
            </div>

            <!-- Main Content: Left 3 Grade Target & Top Students Columns, Right Recent Feed -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left 3 Grade Cards (8 Cols) -->
                <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-3 min-h-0 overflow-hidden">
                    @foreach(['X' => ['target' => 'An-Nas s.d At-Takatsur (13 Surat)', 'color' => 'teal'], 'XI' => ['target' => 'Al-Qari\'ah s.d An-Nazi\'at (23 Surat)', 'color' => 'indigo'], 'XII' => ['target' => 'Surat An-Naba\' (Target Utama)', 'color' => 'emerald']] as $grd => $gcfg)
                        <div class="rounded-2xl glass-panel border border-{{ $gcfg['color'] }}-500/30 p-3 sm:p-3.5 flex flex-col justify-between overflow-hidden shadow-lg">
                            <div class="pb-2 border-b border-white/10 shrink-0">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-{{ $gcfg['color'] }}-500/20 text-{{ $gcfg['color'] }}-300 border border-{{ $gcfg['color'] }}-500/40">
                                        KELAS {{ $grd }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">Target {{ $data['tahfidz']['rankings'][$grd]['total_target'] ?? 0 }} Surat</span>
                                </div>
                                <div class="text-[11px] text-slate-300 font-semibold mt-1 truncate">{{ $gcfg['target'] }}</div>
                            </div>

                            <!-- Top Students List per Grade -->
                            <div id="tahfidzRankList{{ $grd }}" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 my-2 pr-1">
                                @forelse($data['tahfidz']['rankings'][$grd]['students'] ?? [] as $st)
                                    <div class="rounded-xl px-2.5 py-2 flex items-center justify-between border {{ $st['rank'] === 1 ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200' : 'bg-slate-900/60 border-white/5 text-slate-200' }}">
                                        <div class="flex items-center gap-2 truncate mr-2">
                                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black {{ $st['rank'] === 1 ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                                {{ $st['rank'] }}
                                            </span>
                                            <div class="truncate">
                                                <div class="font-bold text-xs text-white truncate">{{ $st['name'] }}</div>
                                                <div class="text-[9px] text-slate-400 truncate">{{ $st['class'] }} &bull; {{ $st['badge'] }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-black text-emerald-300 font-mono">{{ $st['lulus_count'] }} <span class="text-[9px] font-sans text-slate-400">/ {{ $st['total_target'] }}</span></span>
                                            <div class="text-[9px] text-emerald-400 font-bold">{{ $st['pct'] }}%</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="h-full flex items-center justify-center text-center text-slate-500 text-xs italic py-4">
                                        Belum ada data capaian hafalan kelas {{ $grd }}.
                                    </div>
                                @endforelse
                            </div>

                            <div class="text-[10px] text-slate-400 text-center pt-1 border-t border-white/5 shrink-0">
                                Evaluasi Tajwid, Makhraj &amp; Kelancaran
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Right Recent Setoran Tahfidz Feed (4 Cols) -->
                <div class="lg:col-span-4 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span class="text-xs font-black text-white uppercase tracking-wider">Setoran Hafalan Terkini</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">Buku Kontrol</span>
                    </div>

                    <div id="tahfidzRecentLogs" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pr-1">
                        @forelse($data['tahfidz']['recent'] as $rec)
                            <div class="bg-slate-900/70 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between hover:border-emerald-500/30 transition">
                                <div class="truncate mr-2">
                                    <div class="font-bold text-xs text-white truncate">{{ $rec['name'] }} <span class="text-slate-400 font-normal">({{ $rec['class'] }})</span></div>
                                    <div class="text-[10px] text-emerald-300 font-semibold">{{ $rec['surah'] }}</div>
                                    <div class="text-[9px] text-slate-400">Tajwid: {{ $rec['tajwid'] }} &bull; Kelancaran: {{ $rec['kelancaran'] }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $rec['grade'] === 'Lulus' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                        {{ $rec['grade'] }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono">{{ $rec['time'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic">
                                Belum ada riwayat setoran hafalan hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 3: HALL OF FAME — TOP 10 RANKING POIN PRESTASI POSITIF SISWA       -->
        <!-- ========================================================================= -->
        <section id="slide-3" class="kiosk-slide hidden h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-yellow-500/20 text-yellow-300 flex items-center justify-center font-black text-sm">04</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Hall of Fame: Ranking Poin Prestasi Positif Siswa</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Papan apresiasi dan kehormatan siswa dengan akumulasi poin prestasi tertinggi</p>
                    </div>
                </div>
                <div class="text-[11px] text-yellow-300 font-mono hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-yellow-500/10 border border-yellow-500/30">
                    <span>🏆 Reward &amp; Prestasi Terverifikasi</span>
                </div>
            </div>

            <!-- Main Podium & Leaderboard View -->
            <div class="flex-1 min-h-0 flex flex-col justify-between gap-3 overflow-hidden">
                <!-- Top 3 Grand Stage Podium -->
                <div class="grid grid-cols-3 gap-3 sm:gap-4 shrink-0">
                    @php
                        $top1 = $data['ranking'][0] ?? null;
                        $top2 = $data['ranking'][1] ?? null;
                        $top3 = $data['ranking'][2] ?? null;
                    @endphp

                    <!-- Rank 2 (Silver) -->
                    <div class="p-3 sm:p-4 rounded-2xl glass-card border border-slate-300/30 flex flex-col items-center text-center justify-between order-1 shadow-lg">
                        <div class="w-7 h-7 rounded-full bg-slate-300 text-slate-950 font-black text-xs flex items-center justify-center shadow-md">2</div>
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-slate-400 to-slate-200 p-0.5 my-1.5 shadow-md">
                            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-lg text-slate-200">
                                {{ $top2 ? substr($top2['name'], 0, 2) : '--' }}
                            </div>
                        </div>
                        <h4 class="text-xs sm:text-sm font-bold text-white truncate max-w-full">{{ $top2['name'] ?? 'Belum Ada' }}</h4>
                        <span class="text-[10px] text-slate-400">{{ $top2['class'] ?? '-' }}</span>
                        <div class="mt-1 px-3 py-0.5 rounded-full bg-slate-300/20 text-slate-200 font-mono font-black text-xs sm:text-sm">
                            +{{ $top2['points'] ?? 0 }} Poin
                        </div>
                    </div>

                    <!-- Rank 1 (Gold) - Higher & Glowing -->
                    <div class="p-3 sm:p-4 rounded-2xl glass-panel border border-yellow-500/60 flex flex-col items-center text-center justify-between order-2 glow-gold scale-102">
                        <span class="text-xl sm:text-2xl animate-bounce">👑</span>
                        <div class="w-14 h-14 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-tr from-yellow-400 via-amber-300 to-yellow-500 p-1 my-1 shadow-xl shadow-yellow-500/30">
                            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-xl sm:text-2xl text-yellow-300">
                                {{ $top1 ? substr($top1['name'], 0, 2) : '--' }}
                            </div>
                        </div>
                        <h4 class="text-sm sm:text-base font-black text-white truncate max-w-full">{{ $top1['name'] ?? 'Belum Ada' }}</h4>
                        <span class="text-[10px] sm:text-xs text-yellow-200/90 font-semibold">{{ $top1['class'] ?? '-' }} &bull; {{ $top1['achievements_count'] ?? 0 }} Prestasi</span>
                        <div class="mt-1 px-4 py-1 rounded-full bg-gradient-to-r from-yellow-500 to-amber-500 text-slate-950 font-mono font-black text-sm sm:text-base shadow-lg">
                            +{{ $top1['points'] ?? 0 }} POIN
                        </div>
                    </div>

                    <!-- Rank 3 (Bronze) -->
                    <div class="p-3 sm:p-4 rounded-2xl glass-card border border-amber-600/30 flex flex-col items-center text-center justify-between order-3 shadow-lg">
                        <div class="w-7 h-7 rounded-full bg-amber-600 text-white font-black text-xs flex items-center justify-center shadow-md">3</div>
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 p-0.5 my-1.5 shadow-md">
                            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-lg text-amber-300">
                                {{ $top3 ? substr($top3['name'], 0, 2) : '--' }}
                            </div>
                        </div>
                        <h4 class="text-xs sm:text-sm font-bold text-white truncate max-w-full">{{ $top3['name'] ?? 'Belum Ada' }}</h4>
                        <span class="text-[10px] text-slate-400">{{ $top3['class'] ?? '-' }}</span>
                        <div class="mt-1 px-3 py-0.5 rounded-full bg-amber-600/20 text-amber-300 font-mono font-black text-xs sm:text-sm">
                            +{{ $top3['points'] ?? 0 }} Poin
                        </div>
                    </div>
                </div>

                <!-- Ranks 4 to 10 Horizontal Grid -->
                <div class="flex-1 min-h-0 rounded-2xl glass-card border border-white/10 p-3 flex flex-col justify-between overflow-hidden">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block shrink-0">Peringkat 4 s.d. 10 Poin Prestasi Sekolah</span>
                    <div id="rankingList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 flex-1 min-h-0 overflow-y-auto kiosk-scroll pr-1">
                        @forelse(collect($data['ranking'])->slice(3, 8) as $rk)
                            <div class="rounded-xl px-3 py-2 bg-slate-900/60 border border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-2 truncate mr-2">
                                    <span class="w-5 h-5 rounded-md bg-slate-800 text-slate-400 flex items-center justify-center text-[10px] font-bold">
                                        {{ $rk['rank'] }}
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-xs text-white truncate">{{ $rk['name'] }}</div>
                                        <div class="text-[9px] text-slate-400 truncate">{{ $rk['class'] }}</div>
                                    </div>
                                </div>
                                <span class="font-mono font-black text-xs text-yellow-400 shrink-0">+{{ $rk['points'] }}</span>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-slate-500 text-xs italic py-2">
                                Belum ada data prestasi lainnya.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 4: KETERTIBAN SEKOLAH, PETUGAS PIKET & MONITORING POS SATPAM       -->
        <!-- ========================================================================= -->
        <section id="slide-4" class="kiosk-slide hidden h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center font-black text-sm">05</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Ketertiban Sekolah, Petugas Piket &amp; Monitoring Pos Satpam</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Jadwal guru/staf piket hari ini dan monitoring perizinan siswa di pos gerbang sekolah</p>
                    </div>
                </div>
                <div class="text-[11px] text-sky-300 font-mono hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-500/10 border border-sky-500/30">
                    <span>🛡️ Keamanan &amp; Ketertiban Terpadu</span>
                </div>
            </div>

            <!-- Main Grid: Left Duty Pickets, Right Security & Exit Permits -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left Column: Picket Schedule (6 Cols) -->
                <div class="lg:col-span-6 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-base">📋</span>
                            <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                                Petugas Piket Hari Ini ({{ $data['picket']['day_name'] ?? 'Aktif' }})
                            </h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/20 text-teal-300">
                            {{ $data['picket']['total'] ?? 0 }} Petugas Bertugas
                        </span>
                    </div>

                    <div id="picketOfficersList" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pr-1">
                        @forelse($data['picket']['officers'] as $pk)
                            <div class="p-2.5 rounded-xl bg-slate-900/70 border border-white/5 flex items-center justify-between hover:border-teal-500/30 transition">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($pk['name'], 0, 2) }}
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-xs text-white truncate">{{ $pk['name'] }}</div>
                                        <div class="text-[10px] text-slate-400 truncate">NIP: {{ $pk['nip'] }} &bull; {{ $pk['location'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-teal-300 border border-teal-500/20">
                                        {{ $pk['role_type'] }}
                                    </span>
                                    <div class="text-[9px] text-slate-400 mt-1 font-mono">{{ $pk['time'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-6">
                                Belum ada petugas piket yang terjadwal untuk hari ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-2 pt-2 border-t border-white/5 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                        <span>Standby di Pos Piket Lobi Utama &amp; Pos Gerbang</span>
                        <span class="font-mono text-teal-400">SMAN 1 Lengkong</span>
                    </div>
                </div>

                <!-- Right Column: Security Gate & Permits Monitoring (6 Cols) -->
                <div class="lg:col-span-6 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🛡️</span>
                            <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                                Monitoring Pos Satpam &amp; Perizinan Siswa
                            </h3>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">Gerbang Sekolah</span>
                    </div>

                    <!-- 3 Security Metric Badges -->
                    <div class="grid grid-cols-3 gap-2 shrink-0 mb-2">
                        <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center">
                            <span class="text-[9px] text-amber-300 font-bold block uppercase">Di Luar Kampus</span>
                            <span id="securityOutsideCount" class="text-lg font-black text-white font-mono">{{ $data['security']['currently_outside'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">Siswa</span>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                            <span class="text-[9px] text-emerald-300 font-bold block uppercase">Sudah Kembali</span>
                            <span id="securityReturnedCount" class="text-lg font-black text-white font-mono">{{ $data['security']['returned'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">Siswa</span>
                        </div>
                        <div class="p-2 rounded-xl bg-sky-500/10 border border-sky-500/20 text-center">
                            <span class="text-[9px] text-sky-300 font-bold block uppercase">Pulang Cepat</span>
                            <span id="securityEarlyLeavesCount" class="text-lg font-black text-white font-mono">{{ $data['security']['early_leaves_count'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">Disetujui</span>
                        </div>
                    </div>

                    <!-- Recent Permits Feed -->
                    <div id="securityRecentPermits" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pr-1">
                        @forelse($data['security']['recent'] as $pm)
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center justify-between text-xs">
                                <div class="truncate mr-2">
                                    <div class="font-bold text-white truncate">{{ $pm['students'] }}</div>
                                    <div class="text-[10px] text-slate-400 truncate">{{ $pm['purpose'] }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black {{ $pm['status'] === 'SUDAH KEMBALI' ? 'bg-emerald-500/20 text-emerald-300' : ($pm['status'] === 'DI LUAR SEKOLAH' ? 'bg-amber-500/20 text-amber-300' : 'bg-sky-500/20 text-sky-300') }}">
                                        {{ $pm['status'] }}
                                    </span>
                                    <div class="text-[9px] text-slate-400 mt-0.5 font-mono">{{ $pm['time'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-4">
                                Tidak ada siswa berizin keluar atau pulang cepat hari ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-2 pt-2 border-t border-white/5 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                        <span>Pemeriksaan ketat surat izin bermaterai/QR oleh satpam</span>
                        <a href="{{ route('attendance.kiosk-satpam') }}" target="_blank" class="text-teal-400 hover:underline">Buka Kios Satpam &rarr;</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 5: EKSTRAKURIKULER, PROFIL & VISI MISI SMAN 1 LENGKONG             -->
        <!-- ========================================================================= -->
        <section id="slide-5" class="kiosk-slide hidden h-full w-full flex flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center font-black text-sm">06</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Presensi Ekstrakurikuler &amp; Profil SMA Negeri 1 Lengkong</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Pengembangan bakat minat siswa, identitas resmi, serta visi-misi sekolah</p>
                    </div>
                </div>
                <div class="text-[11px] text-purple-300 font-mono hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-500/10 border border-purple-500/30">
                    <span>🎯 Minat &amp; Bakat Siswa</span>
                </div>
            </div>

            <!-- Main Grid: Left Extracurricular Feed, Right School Profile & Vision -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left Column: Ekstrakurikuler (5 Cols) -->
                <div class="lg:col-span-5 rounded-2xl glass-panel border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🎯</span>
                            <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                                Presensi Ekstrakurikuler
                            </h3>
                        </div>
                        <div class="text-right">
                            <span id="extraTotalToday" class="text-base font-black text-purple-300 font-mono">{{ $data['extracurricular']['total_today'] }}</span>
                            <span class="text-[10px] text-slate-400">Hadir</span>
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-400 mb-2 shrink-0">
                        Jadwal Latihan: <strong class="text-slate-300">{{ $data['extracurricular']['schedule'] ?? '-' }}</strong>
                    </div>

                    <div id="extraRecentLogs" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pr-1">
                        @forelse($data['extracurricular']['recent'] as $ex)
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center justify-between text-xs">
                                <div class="truncate mr-2">
                                    <div class="font-bold text-white truncate">{{ $ex['name'] }} <span class="text-slate-400 font-normal">({{ $ex['class'] }})</span></div>
                                    <div class="text-[10px] text-purple-300 font-semibold truncate">{{ $ex['extracurricular'] }}</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-purple-500/20 text-purple-300">
                                        {{ $ex['status'] }}
                                    </span>
                                    <span class="font-mono text-slate-400 text-[10px]">{{ $ex['time'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-6">
                                Belum ada presensi ekstrakurikuler tercatat hari ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-2 pt-2 border-t border-white/5 text-[10px] text-slate-400 shrink-0">
                        Pramuka, Paskibra, PMR, Rohis, Seni Tari, Olahraga Prestasi &bull; S1L
                    </div>
                </div>

                <!-- Right Column: Profil Resmi & Visi Misi Sekolah (7 Cols) -->
                <div class="lg:col-span-7 rounded-2xl glass-panel border border-white/10 p-3 sm:p-5 flex flex-col justify-between overflow-hidden shadow-xl">
                    <!-- School Header Profile -->
                    <div class="flex items-center justify-between pb-3 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-indigo-600 p-0.5 shadow-lg">
                                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-white text-lg">
                                    1LE
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-white">{{ $data['profile']['name'] }}</h3>
                                <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                    <span>NPSN: <strong class="text-white font-mono">{{ $data['profile']['npsn'] }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Akreditasi: <strong class="text-teal-300">{{ $data['profile']['accreditation'] }}</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-white">{{ $data['profile']['principal_name'] }}</div>
                            <div class="text-[10px] text-slate-400">Kepala Sekolah &bull; NIP {{ $data['profile']['principal_nip'] }}</div>
                        </div>
                    </div>

                    <!-- Visi & Misi Resmi (Diambil dari Kelola Konten Web di Visi & Misi) -->
                    <div class="flex-1 min-h-0 my-2 flex flex-col gap-2.5 overflow-hidden">
                        <!-- Visi Sekolah -->
                        <div class="p-2.5 sm:p-3 rounded-xl bg-slate-900/80 border border-teal-500/20 shrink-0 shadow-sm">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-black text-teal-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🌟</span> VISI SEKOLAH
                                </span>
                                <span class="text-[9.5px] text-slate-400 font-medium">SIS-SMA1LE &bull; Profil Resmi</span>
                            </div>
                            <p class="text-xs sm:text-sm text-white font-medium italic leading-snug">
                                "{{ $data['profile']['vision'] }}"
                            </p>
                            @if(!empty($data['profile']['vision_sub']))
                                <p class="text-[10.5px] text-teal-300 font-semibold mt-1">
                                    {{ $data['profile']['vision_sub'] }}
                                </p>
                            @endif
                        </div>

                        <!-- Misi Sekolah (Cards / Grid) -->
                        <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                            <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-white/10 shrink-0">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🎯</span> MISI SEKOLAH
                                </span>
                                <span class="text-[9.5px] text-slate-400 font-mono">{{ count($data['profile']['missions'] ?? []) }} Misi Utama</span>
                            </div>

                            <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll grid grid-cols-1 sm:grid-cols-2 gap-2 pr-1">
                                @forelse($data['profile']['missions'] ?? [] as $misi)
                                    <div class="p-2.5 rounded-xl bg-slate-900/70 border border-white/5 hover:border-indigo-500/30 transition flex items-start gap-2.5">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-black text-[10px] shrink-0 font-mono mt-0.5">
                                            {{ $misi['number'] ?? '01' }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-bold text-white truncate">{{ $misi['title'] ?? '' }}</h4>
                                            <p class="text-[10px] text-slate-300 leading-snug mt-0.5 line-clamp-3">
                                                {{ $misi['desc'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center text-slate-500 text-xs italic py-4">
                                        Data misi sekolah belum dikonfigurasi.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- School Address & Location Footer -->
                    <div class="p-2.5 rounded-xl bg-teal-500/10 border border-teal-500/20 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-300 shrink-0 gap-1">
                        <span class="truncate">📍 {{ $data['profile']['address'] }}</span>
                        <span class="font-mono text-teal-300 shrink-0">sman1lengkong.school</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 6: BERITA & WARTA SEKOLAH RESMI                                      -->
        <!-- ========================================================================= -->
        <section id="slide-6" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-sm">07</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Warta &amp; Publikasi Resmi Sekolah</span>
                            <span class="text-xs font-normal text-slate-400 hidden md:inline">&bull; Kabar Terkini, Prestasi &amp; Dokumentasi Kegiatan Siswa</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Liputan berita resmi SMAN 1 Lengkong yang terintegrasi langsung dengan portal website</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1">
                        <span>📰</span> PORTAL BERITA
                    </span>
                    <a href="{{ route('website.home') }}#berita" target="_blank" class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition">
                        Buka di Web &rarr;
                    </a>
                </div>
            </div>

            <!-- Content Area: Two-Column TV Layout -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3.5 overflow-hidden">
                @php
                    $newsList = $data['news'] ?? [];
                    $featuredNews = $newsList[0] ?? null;
                    $otherNews = array_slice($newsList, 1);
                @endphp

                <!-- Left Column: Featured News (7 of 12 cols) -->
                <div class="lg:col-span-7 h-full flex flex-col glass-card rounded-2xl border border-white/10 p-3 sm:p-4 overflow-hidden glow-teal">
                    @if($featuredNews)
                        @php
                            $imgSrc = null;
                            if (!empty($featuredNews['image'])) {
                                $imgSrc = (str_starts_with($featuredNews['image'], 'http') || str_starts_with($featuredNews['image'], 'images/') || str_starts_with($featuredNews['image'], 'storage/'))
                                    ? asset($featuredNews['image'])
                                    : asset('storage/' . $featuredNews['image']);
                            }
                        @endphp
                        <div class="relative w-full h-44 sm:h-52 md:h-60 rounded-xl overflow-hidden shrink-0 border border-white/10 bg-slate-900 shadow-lg">
                            @if($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $featuredNews['title'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-950 via-slate-900 to-teal-950">
                                    <span class="text-4xl">📰</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                            
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-teal-500 text-slate-950 shadow-md">
                                    ⭐ UTAMA &bull; {{ $featuredNews['category'] ?? 'Berita' }}
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-[11px] text-slate-300">
                                <span class="flex items-center gap-1.5 font-medium bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                                    <span>🗓️</span> {{ $featuredNews['date'] ?? '' }}
                                </span>
                                <span class="flex items-center gap-1.5 font-medium bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                                    <span>✍️</span> {{ $featuredNews['author'] ?? 'Humas SMAN 1 Lengkong' }}
                                </span>
                            </div>
                        </div>

                        <!-- Featured Body -->
                        <div class="flex-1 min-h-0 flex flex-col justify-between mt-3 overflow-hidden">
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-white leading-snug line-clamp-2 hover:text-teal-300 transition">
                                    {{ $featuredNews['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed line-clamp-3 sm:line-clamp-4 font-normal">
                                    {{ $featuredNews['excerpt'] ?? Str::limit(strip_tags($featuredNews['content'] ?? ''), 220) }}
                                </p>
                            </div>
                            
                            <div class="pt-2 border-t border-white/5 flex items-center justify-between text-xs text-slate-400 mt-2 shrink-0">
                                <span class="text-[11px] flex items-center gap-1 text-teal-400 font-semibold">
                                    <span>🌐</span> Terpublikasi di Portal Resmi SMAN 1 Lengkong
                                </span>
                                <span class="font-mono text-[10px] text-slate-500">sman1lengkong.school</span>
                            </div>
                        </div>
                    @else
                        <div class="h-full flex items-center justify-center text-slate-500 text-xs italic">
                            Belum ada artikel berita tersimpan.
                        </div>
                    @endif
                </div>

                <!-- Right Column: Other Recent News Cards (5 of 12 cols) -->
                <div class="lg:col-span-5 h-full flex flex-col glass-card rounded-2xl border border-white/10 p-3 sm:p-4 overflow-hidden">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Koleksi Warta Terbaru</h4>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400">{{ count($newsList) }} Berita Aktif</span>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll flex flex-col gap-2.5 pr-1">
                        @forelse($otherNews as $item)
                            @php
                                $subImg = null;
                                if (!empty($item['image'])) {
                                    $subImg = (str_starts_with($item['image'], 'http') || str_starts_with($item['image'], 'images/') || str_starts_with($item['image'], 'storage/'))
                                        ? asset($item['image'])
                                        : asset('storage/' . $item['image']);
                                }
                            @endphp
                            <div class="p-2.5 rounded-xl bg-slate-900/70 border border-white/5 hover:border-teal-500/30 transition flex gap-3 items-center group">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden shrink-0 border border-white/10 bg-slate-950 relative">
                                    @if($subImg)
                                        <img src="{{ $subImg }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-slate-400 text-lg">
                                            📰
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                            {{ $item['category'] ?? 'Berita' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $item['date'] ?? '' }}</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white leading-snug line-clamp-2 group-hover:text-teal-300 transition">
                                        {{ $item['title'] }}
                                    </h5>
                                    <p class="text-[10px] text-slate-400 line-clamp-2 mt-1 leading-snug">
                                        {{ $item['excerpt'] ?? Str::limit(strip_tags($item['content'] ?? ''), 90) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-6">
                                Belum ada artikel warta tambahan.
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 mt-2 border-t border-white/5 flex items-center justify-between text-[11px] text-slate-400 shrink-0">
                        <span>Menampilkan warta rujukan website</span>
                        <span class="text-teal-400 font-mono text-[10px]">Pembaruan Otomatis</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 7: AGENDA & KALENDER KEGIATAN AKADEMIK                              -->
        <!-- ========================================================================= -->
        <section id="slide-7" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-black text-sm">08</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Agenda &amp; Kalender Kegiatan Akademik</span>
                            <span class="text-xs font-normal text-slate-400 hidden md:inline">&bull; Jadwal Asesmen, Projek P5, Peringatan &amp; Kegiatan Resmi</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Jadwal kegiatan akademik dan kesiswaan SMAN 1 Lengkong terkoneksi langsung dari website</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center gap-1">
                        <span>📅</span> KALENDER AGENDA
                    </span>
                    <a href="{{ route('website.home') }}#agenda" target="_blank" class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition">
                        Buka di Web &rarr;
                    </a>
                </div>
            </div>

            <!-- Content Area: Agenda Grid & Info Cards -->
            <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden">
                <!-- Top Summary Ribbon -->
                <div class="p-2.5 sm:p-3 rounded-xl bg-gradient-to-r from-indigo-950/70 via-slate-900/80 to-teal-950/70 border border-indigo-500/20 flex flex-wrap items-center justify-between gap-3 shrink-0 shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-lg font-black shrink-0">
                            🗓️
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-white">Kalender Aktivitas Terpadu SMAN 1 Lengkong</div>
                            <div class="text-[10.5px] text-slate-300">Seluruh agenda resmi mengacu pada kalender pendidikan &amp; agenda semester berjalan</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 font-mono text-xs">
                        <span class="px-3 py-1 rounded-lg bg-indigo-500/30 text-indigo-300 font-bold border border-indigo-500/40">
                            {{ count($data['agendas'] ?? []) }} Agenda Terdaftar
                        </span>
                        <span class="px-3 py-1 rounded-lg bg-teal-500/30 text-teal-300 font-bold border border-teal-500/40 hidden sm:inline-block">
                            Semester Ganjil 2026/2027
                        </span>
                    </div>
                </div>

                <!-- Grid of Agenda Cards -->
                <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-3 pr-1">
                    @forelse($data['agendas'] ?? [] as $idx => $agenda)
                        @php
                            $dateObj = null;
                            try {
                                $dateObj = \Carbon\Carbon::parse($agenda['date']);
                            } catch (\Throwable $e) {}
                            $dayNum = $dateObj ? $dateObj->format('d') : '25';
                            $monthStr = $dateObj ? $dateObj->translatedFormat('M') : 'NOV';
                            $dayName = $dateObj ? $dateObj->translatedFormat('l') : 'Rabu';
                        @endphp
                        <div class="glass-card rounded-2xl border border-white/10 p-3.5 flex flex-col justify-between hover:border-indigo-500/40 transition group glow-indigo relative overflow-hidden">
                            <!-- Top Ribbon: Date Badge + Tag -->
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-12 h-13 rounded-xl bg-gradient-to-b from-indigo-500 to-indigo-700 text-white flex flex-col items-center justify-center shadow-lg shrink-0 border border-indigo-400/30">
                                        <span class="text-[9px] font-black tracking-widest uppercase">{{ $monthStr }}</span>
                                        <span class="text-base font-extrabold leading-tight">{{ $dayNum }}</span>
                                    </div>
                                    <div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            {{ $agenda['tag'] ?? 'Akademik' }}
                                        </span>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ $dayName }}, {{ $agenda['date'] ?? '' }}
                                            @if(!empty($agenda['end_date']))
                                                s.d. {{ $agenda['end_date'] }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <span class="w-7 h-7 rounded-lg bg-slate-900 border border-white/10 flex items-center justify-center text-xs text-indigo-300 font-mono">
                                    #{{ $idx + 1 }}
                                </span>
                            </div>

                            <!-- Title & Description -->
                            <div class="flex-1 min-h-0 my-1">
                                <h4 class="text-sm font-extrabold text-white leading-snug group-hover:text-indigo-300 transition line-clamp-2">
                                    {{ $agenda['title'] }}
                                </h4>
                                <p class="text-xs text-slate-300 leading-relaxed mt-1.5 line-clamp-3">
                                    {{ $agenda['description'] ?? 'Agenda resmi sekolah yang wajib diikuti oleh warga sekolah yang bersangkutan.' }}
                                </p>
                            </div>

                            <!-- Footer Card Status -->
                            <div class="pt-2 mt-2 border-t border-white/5 flex items-center justify-between text-[10.5px] text-slate-400 shrink-0">
                                <span class="flex items-center gap-1 text-teal-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span> Terjadwal Resmi
                                </span>
                                <span class="font-mono text-slate-500 text-[10px]">SMAN 1 Lengkong</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full h-full flex items-center justify-center text-slate-500 text-xs italic py-12">
                            Belum ada jadwal agenda yang dipublikasikan.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 8: DEWAN GURU & TENAGA KEPENDIDIKAN (NAMA & FOTO RESMI)             -->
        <!-- ========================================================================= -->
        <section id="slide-8" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-black text-sm">09</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Direktori Dewan Guru &amp; Tenaga Kependidikan</span>
                            <span class="text-xs font-normal text-slate-400 hidden md:inline">&bull; Profil Pendidik &amp; Tenaga Administrasi Resmi</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Data resmi nama lengkap, gelar, NIP, mata pelajaran/tugas, dan foto pendidik SMAN 1 Lengkong</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                        <span>👥</span> {{ count($data['staff'] ?? []) }} PENDIDIK &amp; STAF
                    </span>

                    <!-- Sub-page Prev/Next Navigation Controls -->
                    <div class="flex items-center bg-slate-900/90 border border-white/10 rounded-xl p-0.5 text-xs">
                        <button type="button" onclick="prevStaffPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Guru Sebelumnya">
                            &larr;
                        </button>
                        <span id="staffPageIndicator" class="px-2 font-mono text-[11px] font-bold text-teal-300">
                            Hal 1 / {{ max(1, count(array_chunk($data['staff'] ?? [], 8))) }}
                        </span>
                        <button type="button" onclick="nextStaffPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Guru Berikutnya">
                            &rarr;
                        </button>
                    </div>

                    <a href="{{ route('website.home') }}#guru-staf" target="_blank" class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition">
                        Buka di Web &rarr;
                    </a>
                </div>
            </div>

            <!-- Content Area: Chunked Grid of Teachers & Staff with Enlarged Photos -->
            <div class="flex-1 min-h-0 relative overflow-hidden">
                @php
                    $allStaff = $data['staff'] ?? [];
                    $chunkedStaff = array_chunk($allStaff, 8);
                @endphp

                @forelse($chunkedStaff as $pageIdx => $staffPage)
                    <div class="staff-page h-full w-full {{ $pageIdx === 0 ? 'grid' : 'hidden' }} grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4 overflow-y-auto kiosk-scroll content-start" id="staff-page-{{ $pageIdx }}">
                        @foreach($staffPage as $st)
                            @php
                                $photoUrl = null;
                                if (!empty($st['photo'])) {
                                    if (str_starts_with($st['photo'], 'http')) {
                                        $photoUrl = $st['photo'];
                                    } elseif (str_starts_with($st['photo'], 'images/') || str_starts_with($st['photo'], 'storage/')) {
                                        $photoUrl = asset($st['photo']);
                                    } else {
                                        $photoUrl = asset('storage/' . $st['photo']);
                                    }
                                }
                                $isHeadmaster = ($st['type'] ?? '') === 'kepala_sekolah' || str_contains(strtolower($st['duty'] ?? ''), 'kepala sekolah');
                                $isGuru = ($st['type'] ?? '') === 'guru' || !empty($st['mapel']);
                            @endphp
                            <div class="glass-card rounded-2xl border border-white/10 p-2.5 sm:p-3 flex flex-col items-center text-center justify-between hover:border-teal-500/40 transition duration-300 group relative overflow-hidden {{ $isHeadmaster ? 'glow-gold border-amber-500/30 bg-amber-950/20' : 'glow-teal' }}">
                                <!-- Top Status Pill -->
                                <div class="w-full flex items-center justify-between gap-1 mb-1 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase tracking-wider truncate {{ $isHeadmaster ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : ($isGuru ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30') }}">
                                        {{ $isHeadmaster ? '👑 Kepala Sekolah' : ($isGuru ? '🎓 Dewan Guru' : '🏢 Staf TU') }}
                                    </span>
                                    <span class="text-[8px] font-mono text-slate-400 truncate">
                                        {{ $st['employment_status'] ?? 'ASN' }}
                                    </span>
                                </div>

                                <!-- Photo Container with Elegant Ring (Portrait Ratio, No Head Cutoff) -->
                                <div class="w-24 h-28 sm:w-28 sm:h-32 md:w-30 md:h-36 rounded-2xl overflow-hidden p-1 bg-gradient-to-tr {{ $isHeadmaster ? 'from-amber-400 via-yellow-500 to-amber-600' : 'from-teal-400 via-indigo-500 to-sky-400' }} shadow-lg my-1 shrink-0 group-hover:scale-105 transition duration-300">
                                    <div class="w-full h-full bg-slate-950 rounded-[12px] overflow-hidden flex items-center justify-center">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="{{ $st['name'] }}" class="w-full h-full object-cover object-top" style="object-position: top center;">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-900 via-slate-800 to-teal-950 text-teal-300 font-extrabold text-xl sm:text-2xl">
                                                <span>{{ strtoupper(substr($st['name'], 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Staff Name & Academic Degrees -->
                                <div class="w-full min-w-0 mt-0.5 shrink-0">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-white leading-tight truncate group-hover:text-teal-300 transition" title="{{ $st['name'] }}">
                                        {{ $st['name'] }}
                                    </h4>
                                    <div class="text-[10px] sm:text-[10.5px] font-semibold text-teal-300 truncate mt-0.5" title="{{ $st['duty'] ?? 'Pendidik' }}">
                                        {{ $st['duty'] ?? 'Pendidik' }}
                                    </div>
                                    @if(!empty($st['mapel']) && $st['mapel'] !== ($st['duty'] ?? ''))
                                        <div class="text-[9.5px] text-slate-400 truncate mt-0.5" title="{{ $st['mapel'] }}">
                                            {{ $st['mapel'] }}
                                        </div>
                                    @endif
                                </div>

                                <!-- NIP Badge Footer -->
                                <div class="w-full pt-1 mt-0.5 border-t border-white/5 shrink-0">
                                    <span class="inline-block text-[9px] font-mono text-slate-400 bg-slate-900/90 px-2 py-0.5 rounded-full border border-white/5 truncate max-w-full">
                                        {{ !empty($st['nip']) && $st['nip'] !== '-' ? 'NIP ' . $st['nip'] : 'Pendidik Resmi' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-12">
                        Data dewan guru dan tenaga kependidikan belum dimuat.
                    </div>
                @endforelse
            </div>
        </section>

    </main>

    <!-- 4. BOTTOM TICKER FOOTER BAR (Compact ~34px) -->
    <footer class="relative z-30 h-8 sm:h-9 shrink-0 bg-slate-950/95 border-t border-white/10 px-3 sm:px-4 flex items-center justify-between gap-3 overflow-hidden shadow-2xl">
        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/40 text-[10px] font-black uppercase tracking-wider shrink-0">
            <span>📢 PENGUMUMAN</span>
        </div>
        <div class="flex-1 min-w-0 overflow-hidden relative">
            <div id="kioskTicker" class="animate-ticker-marquee text-xs font-semibold text-slate-300 tracking-wide">
                {{ $data['ticker'] }}
            </div>
        </div>
        <div class="text-[10px] text-slate-500 font-mono shrink-0 hidden sm:block">
            SIS-SMA1LE &bull; TV KIOSK
        </div>
    </footer>

    <!-- JAVASCRIPT SLIDE CONTROLLER & BACKGROUND REALTIME POLLER -->
    <script>
        // Slide Deck State
        const TOTAL_SLIDES = 9;
        const SLIDE_DURATION = 16; // 16 seconds per slide
        let currentSlideIdx = 0;
        let slideRemainingTime = SLIDE_DURATION;
        let isPaused = false;
        let slideTimerInterval = null;

        // Sub-page Controller for Slide 8 (Guru & Staf)
        let currentStaffPageIdx = 0;
        const totalStaffPages = {{ max(1, count(array_chunk($data['staff'] ?? [], 8))) }};
        let staffCycleInterval = null;

        function showStaffPage(page) {
            if (totalStaffPages <= 1) return;
            currentStaffPageIdx = (page + totalStaffPages) % totalStaffPages;
            for (let i = 0; i < totalStaffPages; i++) {
                const pageEl = document.getElementById(`staff-page-${i}`);
                if (pageEl) {
                    if (i === currentStaffPageIdx) {
                        pageEl.classList.remove('hidden');
                        pageEl.classList.add('grid');
                    } else {
                        pageEl.classList.add('hidden');
                        pageEl.classList.remove('grid');
                    }
                }
            }
            const indEl = document.getElementById('staffPageIndicator');
            if (indEl) {
                indEl.textContent = `Hal ${currentStaffPageIdx + 1} / ${totalStaffPages}`;
            }
        }

        function nextStaffPage() {
            showStaffPage(currentStaffPageIdx + 1);
        }

        function prevStaffPage() {
            showStaffPage(currentStaffPageIdx - 1);
        }

        function checkStaffSlideAutoCycle() {
            if (staffCycleInterval) {
                clearInterval(staffCycleInterval);
                staffCycleInterval = null;
            }
            if (currentSlideIdx === 8 && totalStaffPages > 1) {
                staffCycleInterval = setInterval(() => {
                    if (!isPaused) {
                        nextStaffPage();
                    }
                }, 3000); // cycle sub-page briskly every 3s while on slide 8
            }
        }

        // Initialize Slide Engine
        function initSlideDeck() {
            showSlide(currentSlideIdx);
            startSlideTimer();
            updateLiveClock();
            setInterval(updateLiveClock, 1000);
        }

        // Show Slide Function
        function showSlide(index) {
            currentSlideIdx = (index + TOTAL_SLIDES) % TOTAL_SLIDES;

            // Update slide visibility
            for (let i = 0; i < TOTAL_SLIDES; i++) {
                const slideEl = document.getElementById(`slide-${i}`);
                const pillEl = document.getElementById(`pill-slide-${i}`);

                if (slideEl) {
                    if (i === currentSlideIdx) {
                        slideEl.classList.remove('hidden');
                        slideEl.classList.add('flex');
                    } else {
                        slideEl.classList.add('hidden');
                        slideEl.classList.remove('flex');
                    }
                }

                if (pillEl) {
                    if (i === currentSlideIdx) {
                        pillEl.className = 'slide-nav-pill px-2 py-1 rounded-lg font-bold transition flex items-center gap-1 bg-teal-500 text-slate-950 shadow-sm whitespace-nowrap text-[11px]';
                    } else {
                        pillEl.className = 'slide-nav-pill px-2 py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[11px]';
                    }
                }
            }

            // Check staff slide sub-page cycle
            checkStaffSlideAutoCycle();

            // Reset countdown for this slide
            slideRemainingTime = SLIDE_DURATION;
            updateProgressBar();
        }

        function nextSlide() {
            showSlide(currentSlideIdx + 1);
        }

        function prevSlide() {
            showSlide(currentSlideIdx - 1);
        }

        function goToSlide(idx) {
            showSlide(idx);
        }

        // Auto Advance Timer & Progress Bar
        function startSlideTimer() {
            if (slideTimerInterval) clearInterval(slideTimerInterval);

            slideTimerInterval = setInterval(() => {
                if (!isPaused) {
                    slideRemainingTime--;
                    updateProgressBar();

                    if (slideRemainingTime <= 0) {
                        nextSlide();
                    }
                }
            }, 1000);
        }

        function updateProgressBar() {
            const bar = document.getElementById('slideProgressBar');
            const countdownEl = document.getElementById('slideCountdownLabel');

            if (bar) {
                const pct = ((SLIDE_DURATION - slideRemainingTime) / SLIDE_DURATION) * 100;
                bar.style.width = `${pct}%`;
            }

            if (countdownEl) {
                countdownEl.textContent = `${slideRemainingTime}s`;
            }
        }

        function togglePlayPause() {
            isPaused = !isPaused;
            const icon = document.getElementById('playPauseIcon');
            if (icon) {
                icon.textContent = isPaused ? '▶️' : '⏸️';
            }
        }

        // Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
            }
        }

        // Keyboard Navigation (ArrowLeft, ArrowRight, Space)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') {
                nextSlide();
            } else if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === ' ' || e.code === 'Space') {
                togglePlayPause();
            }
        });

        // Realtime Clock Updater
        function updateLiveClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }

            const dateEl = document.getElementById('liveDate');
            if (dateEl) {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            }
        }

        // Background Realtime Data Poller (Runs silently every 15s without interrupting user)
        async function fetchKioskData() {
            try {
                const res = await fetch("{{ route('kiosk.data') }}");
                if (!res.ok) return;
                const d = await res.json();

                // 1. Update Daily Attendance
                if (d.daily) {
                    const pctEl = document.getElementById('dailyPercentage');
                    const barEl = document.getElementById('dailyPercentageBar');
                    const totEl = document.getElementById('dailyTotalStudents');
                    if (pctEl) pctEl.textContent = `${d.daily.percentage}%`;
                    if (barEl) barEl.style.width = `${d.daily.percentage}%`;
                    if (totEl) totEl.textContent = d.daily.total_students;

                    const countMap = {
                        countHadir: d.daily.hadir,
                        countTerlambat: d.daily.terlambat,
                        countIzin: d.daily.izin,
                        countSakit: d.daily.sakit,
                        countAlpa: d.daily.alpa,
                    };
                    Object.keys(countMap).forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = countMap[id];
                    });

                    renderList('dailyRecentLogs', d.daily.recent, item => `
                        <div class="bg-slate-900/70 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between hover:border-teal-500/30 transition">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-xs shrink-0">
                                    ${item.name.charAt(0)}
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-xs text-white truncate">${item.name}</div>
                                    <div class="text-[10px] text-slate-400 truncate">${item.class} &bull; ${item.method}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black ${item.status === 'HADIR' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'}">
                                    ${item.status}
                                </span>
                                <span class="text-[11px] font-mono font-bold text-slate-300">${item.time}</span>
                            </div>
                        </div>
                    `, 'Belum ada aktivitas presensi masuk hari ini.');
                }

                // 2. Update Worship Stats
                if (d.dhuha) {
                    const dhTot = document.getElementById('dhuhaTotalToday');
                    if (dhTot) dhTot.textContent = d.dhuha.total_today;
                    updateStatusBadge('dhuhaStatusBadge', d.dhuha.is_open);
                }
                if (d.dzuhur) {
                    const dzTot = document.getElementById('dzuhurTotalToday');
                    if (dzTot) dzTot.textContent = d.dzuhur.total_today;
                    updateStatusBadge('dzuhurStatusBadge', d.dzuhur.is_open);
                }
                if (d.keputrian) {
                    const kpTot = document.getElementById('keputrianTotalToday');
                    if (kpTot) kpTot.textContent = d.keputrian.total_today;
                    updateStatusBadge('keputrianStatusBadge', d.keputrian.is_open);
                }

                // 3. Update 7 KAIH Habits
                if (d.habits) {
                    const hbTot = document.getElementById('habitsCompletedToday');
                    if (hbTot) hbTot.textContent = d.habits.completed_today;
                }

                // 4. Update Tahfidz Juz 30
                if (d.tahfidz) {
                    const thTot = document.getElementById('tahfidzTotalToday');
                    const thPass = document.getElementById('tahfidzPassedToday');
                    if (thTot) thTot.textContent = d.tahfidz.total_today;
                    if (thPass) thPass.textContent = d.tahfidz.passed_today;
                }

                // 5. Update Security & Picket
                if (d.security) {
                    const outEl = document.getElementById('securityOutsideCount');
                    const retEl = document.getElementById('securityReturnedCount');
                    const elvEl = document.getElementById('securityEarlyLeavesCount');
                    if (outEl) outEl.textContent = d.security.currently_outside;
                    if (retEl) retEl.textContent = d.security.returned;
                    if (elvEl) elvEl.textContent = d.security.early_leaves_count;
                }

                // 6. Update Ekstrakurikuler
                if (d.extracurricular) {
                    const exTot = document.getElementById('extraTotalToday');
                    if (exTot) exTot.textContent = d.extracurricular.total_today;
                }

                // 7. Update Ticker Text
                if (d.ticker) {
                    const tickerEl = document.getElementById('kioskTicker');
                    if (tickerEl) tickerEl.textContent = d.ticker;
                }

            } catch (e) {
                console.error("Kiosk background poller error:", e);
            }
        }

        function updateStatusBadge(id, isOpen) {
            const el = document.getElementById(id);
            if (!el) return;
            if (isOpen) {
                el.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse';
                el.textContent = 'SESI BUKA';
            } else {
                el.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-800 text-slate-400 border border-white/10';
                el.textContent = 'SESI TUTUP';
            }
        }

        function renderList(containerId, items, templateFn, emptyMsg) {
            const el = document.getElementById(containerId);
            if (!el) return;
            if (!items || items.length === 0) {
                el.innerHTML = `<div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-4">${emptyMsg}</div>`;
                return;
            }
            el.innerHTML = items.map(templateFn).join('');
        }

        // Auto Poll every 15s
        setInterval(fetchKioskData, 15000);

        // Run on load
        document.addEventListener('DOMContentLoaded', initSlideDeck);
    </script>
</body>
</html>
