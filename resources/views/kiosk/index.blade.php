<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display KIOSK & Respons Layar - {{ $school['name'] }}</title>
    <!-- Tailwind CSS (via Vite or CDN Fallback) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#090d16',
                        cardbg: '#0f172a',
                        bordercolor: '#1e293b',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #060913;
            color: #f1f5f9;
            overflow-x: hidden;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glow-teal {
            box-shadow: 0 0 25px -5px rgba(20, 184, 166, 0.3);
        }
        .glow-amber {
            box-shadow: 0 0 25px -5px rgba(245, 158, 11, 0.3);
        }
        .glow-indigo {
            box-shadow: 0 0 25px -5px rgba(99, 102, 241, 0.3);
        }
        .glow-rose {
            box-shadow: 0 0 25px -5px rgba(244, 63, 94, 0.3);
        }
        .glow-emerald {
            box-shadow: 0 0 25px -5px rgba(16, 185, 129, 0.3);
        }
        /* Custom Scrollbar for live lists */
        .kiosk-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .kiosk-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
        }
        .kiosk-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }
        /* Marquee / Ticker Animation */
        @keyframes tickerAnimation {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-ticker {
            display: inline-block;
            white-space: nowrap;
            animation: tickerAnimation 40s linear infinite;
        }
        .animate-ticker:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-slate-100 antialiased selection:bg-teal-500 selection:text-white">

    <!-- TOP HEADER BAR (Logo, School Name, Mode Switcher, Digital Clock) -->
    <header class="sticky top-0 z-50 glass-panel border-b border-white/10 px-4 sm:px-8 py-3.5 flex flex-wrap items-center justify-between gap-4 shadow-2xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-teal-500 via-indigo-500 to-sky-400 p-0.5 shadow-lg shadow-teal-500/20 shrink-0">
                <div id="kioskLogoContainer" class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center overflow-hidden">
                    @if(!empty($school['logo']))
                        <img src="{{ asset('storage/' . $school['logo']) }}" alt="Logo" class="w-8 h-8 object-contain">
                    @else
                        <span class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-sky-300">S1L</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 id="kioskSchoolName" class="text-base sm:text-lg font-black tracking-tight text-white uppercase">{{ $school['name'] }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> LIVE KIOSK
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium hidden sm:block">Monitor Display Presensi, Ibadah Berjamaah, Tahfidz & Leaderboard Prestasi</p>
            </div>
        </div>

        <!-- Mode Buttons & Action Controls -->
        <div class="flex items-center gap-2.5">
            <!-- Kiosk Ecosystem Switcher -->
            <div class="hidden lg:flex items-center gap-1.5 bg-slate-900/90 border border-white/10 rounded-xl p-1 text-xs">
                <a href="{{ route('kiosk.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1">
                    <span>📺 Kios TV</span>
                </a>
                <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2.5 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white hover:bg-white/5 transition flex items-center gap-1">
                    <span>📚 Kios Perpus</span>
                </a>
                <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2.5 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white hover:bg-white/5 transition flex items-center gap-1">
                    <span>👥 Kios Tamu</span>
                </a>
            </div>

            <!-- Mode Switcher -->
            <div class="bg-slate-900/90 border border-white/10 rounded-xl p-1 flex items-center gap-1 text-xs">
                <button type="button" id="btnModeGrid" onclick="setKioskMode('grid')" class="px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-sm">
                    <span>🔲 Grid</span>
                </button>
                <button type="button" id="btnModeCarousel" onclick="setKioskMode('carousel')" class="px-3 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5">
                    <span>🔄 Carousel (<span id="carouselTimer">12s</span>)</span>
                </button>
            </div>

            <!-- Fullscreen Toggle -->
            <button type="button" onclick="toggleFullscreen()" title="Layar Penuh (F11)" class="p-2 rounded-xl bg-slate-900/90 border border-white/10 hover:border-teal-500/50 text-slate-300 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>

            <!-- Back to App Link -->
            @auth
            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 text-xs font-semibold transition hidden md:inline-flex items-center gap-1">
                <span>← Dashboard</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 text-xs font-semibold transition hidden md:inline-flex items-center gap-1">
                <span>Masuk Petugas</span>
            </a>
            @endauth

            <!-- Live Digital Clock -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950/80 border border-indigo-500/30 rounded-2xl px-4 py-1.5 text-right shadow-inner">
                <div id="liveClock" class="text-base sm:text-lg font-mono font-black text-teal-300 tracking-wider">--:--:-- WIB</div>
                <div id="liveDate" class="text-[10px] text-slate-400 font-semibold tracking-wide capitalize">--</div>
            </div>
        </div>
    </header>

    <!-- CAROUSEL INDICATOR BAR (Active in Carousel Mode) -->
    <div id="carouselProgressBarContainer" class="hidden w-full bg-slate-900/60 h-1 overflow-hidden">
        <div id="carouselProgressBar" class="bg-gradient-to-r from-teal-400 via-indigo-500 to-rose-500 h-full w-0 transition-all duration-300 ease-linear"></div>
    </div>

    <!-- CAROUSEL TAB CONTROLS (Quick Jump to Panel) -->
    <nav class="bg-slate-950/70 border-b border-white/5 px-4 sm:px-8 py-2 overflow-x-auto no-scrollbar flex items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1 hidden sm:inline">Pintas Layar:</span>
            <button onclick="focusWidget(0)" class="tab-btn px-3 py-1 rounded-lg font-bold transition bg-white/10 text-teal-300 hover:bg-white/15" data-idx="0">
                📅 1. Presensi Utama
            </button>
            <button onclick="focusWidget(1)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="1">
                🕌 2. Shalat Dhuha
            </button>
            <button onclick="focusWidget(2)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="2">
                🕌 3. Shalat Dzuhur
            </button>
            <button onclick="focusWidget(3)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="3">
                ✨ 4. Keputrian
            </button>
            <button onclick="focusWidget(4)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="4">
                🎯 5. Ekstrakurikuler
            </button>
            <button onclick="focusWidget(5)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="5">
                📖 6. Tahfidz Juz 30 (X,XI,XII)
            </button>
            <button onclick="focusWidget(6)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="6">
                🏆 7. Top 10 Prestasi
            </button>
            <button onclick="focusWidget(7)" class="tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5" data-idx="7">
                ⏱️ 8. Jadwal Aktivitas
            </button>
        </div>

        <div class="shrink-0 text-[11px] text-slate-400 font-mono flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
            <span>Update data: <strong id="lastUpdatedTime">Auto 15s</strong></span>
        </div>
    </nav>

    <!-- MAIN DASHBOARD CONTENT AREA -->
    <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-[1920px] w-full mx-auto">
        <!-- 6 WIDGET PANELS GRID -->
        <div id="widgetsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6">

            <!-- WIDGET 1: PRESENSI HARIAN -->
            <section id="widget-0" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-teal-500 glow-teal transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-teal-500/20 text-teal-300 flex items-center justify-center text-xl font-bold border border-teal-500/30 shrink-0">
                                📅
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Presensi Kehadiran Harian</h2>
                                <p class="text-xs text-slate-400 truncate">Total Siswa Terdaftar: <strong id="dailyTotalStudents" class="text-teal-300">{{ $data['daily']['total_students'] }}</strong></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span id="dailyPercentage" class="text-xl sm:text-2xl font-black text-teal-400">{{ $data['daily']['percentage'] }}%</span>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Tingkat Hadir</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-800 rounded-full h-2 mb-4 overflow-hidden">
                        <div id="dailyProgressBar" class="bg-gradient-to-r from-teal-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ $data['daily']['percentage'] }}%;"></div>
                    </div>

                    <!-- Stat Metric Badges -->
                    <div class="grid grid-cols-5 gap-2 text-center mb-4">
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-2">
                            <div class="text-[10px] font-bold text-emerald-400 uppercase">Hadir</div>
                            <div id="dailyHadir" class="text-lg font-black text-emerald-300">{{ $data['daily']['hadir'] }}</div>
                        </div>
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-2">
                            <div class="text-[10px] font-bold text-amber-400 uppercase">Telat</div>
                            <div id="dailyTerlambat" class="text-lg font-black text-amber-300">{{ $data['daily']['terlambat'] }}</div>
                        </div>
                        <div class="bg-sky-500/10 border border-sky-500/20 rounded-xl p-2">
                            <div class="text-[10px] font-bold text-sky-400 uppercase">Izin</div>
                            <div id="dailyIzin" class="text-lg font-black text-sky-300">{{ $data['daily']['izin'] }}</div>
                        </div>
                        <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-2">
                            <div class="text-[10px] font-bold text-indigo-400 uppercase">Sakit</div>
                            <div id="dailySakit" class="text-lg font-black text-indigo-300">{{ $data['daily']['sakit'] }}</div>
                        </div>
                        <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-2">
                            <div class="text-[10px] font-bold text-rose-400 uppercase">Alpa</div>
                            <div id="dailyAlpa" class="text-lg font-black text-rose-300">{{ $data['daily']['alpa'] }}</div>
                        </div>
                    </div>

                    <!-- Recent Check-ins List -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-2">
                            <span>⏱️ Log Presensi Terakhir</span>
                            <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded text-teal-300">Live Update</span>
                        </div>
                        <div id="dailyRecentLogs" class="space-y-1.5 max-h-56 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['daily']['recent'] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log['class'] }} • {{ $log['method'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $log['status'] === 'HADIR' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $log['status'] }}
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $log['time'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada log presensi masuk hari ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <!-- WIDGET 2: SHALAT DHUHA BERSAMA -->
            <section id="widget-1" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-amber-500 glow-amber transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-xl font-bold border border-amber-500/30 shrink-0">
                                🕌
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Shalat Dhuha Bersama</h2>
                                <p class="text-xs text-slate-400 truncate">{{ $data['dhuha']['schedule'] }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span id="dhuhaStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black {{ $data['dhuha']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                                <span class="w-2 h-2 rounded-full {{ $data['dhuha']['is_open'] ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                {{ $data['dhuha']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Total Card -->
                    <div class="bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-900 border border-amber-500/20 rounded-2xl p-4 mb-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-amber-300/80 font-bold uppercase">Jamaah Dhuha Hari Ini</div>
                            <div class="text-xs text-slate-400 mt-0.5">Seluruh Siswa (Putra & Putri)</div>
                        </div>
                        <div class="text-right">
                            <span id="dhuhaTotalToday" class="text-3xl font-black text-amber-400 font-mono">{{ $data['dhuha']['total_today'] }}</span>
                            <span class="text-xs text-slate-400 ml-1">Siswa</span>
                        </div>
                    </div>

                    <!-- Recent Check-ins List -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-2">
                            <span>⏱️ Log Presensi Dhuha</span>
                            <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded text-amber-300">Siswa & Siswi</span>
                        </div>
                        <div id="dhuhaRecentLogs" class="space-y-1.5 max-h-56 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['dhuha']['recent'] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log['class'] }} • {{ $log['method'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-500/20 text-amber-300">
                                            HADIR
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $log['time'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada jamaah dhuha yang tercatat hari ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <!-- WIDGET 3: SHALAT DZUHUR / JUM'AT BERJAMAAH -->
            <section id="widget-2" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-indigo-500 glow-indigo transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-xl font-bold border border-indigo-500/30 shrink-0">
                                🕌
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Shalat Dzuhur / Jum'at</h2>
                                <p class="text-xs text-slate-400 truncate">{{ $data['dzuhur']['schedule'] }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span id="dzuhurStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black {{ $data['dzuhur']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                                <span class="w-2 h-2 rounded-full {{ $data['dzuhur']['is_open'] ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                {{ $data['dzuhur']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Total Card -->
                    <div class="bg-gradient-to-r from-indigo-950/40 via-slate-900 to-slate-900 border border-indigo-500/20 rounded-2xl p-4 mb-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-indigo-300/80 font-bold uppercase">Jamaah Dzuhur/Jum'at</div>
                            <div class="text-xs text-slate-400 mt-0.5">Senin–Kamis (Putra & Putri), Jum'at (Putra)</div>
                        </div>
                        <div class="text-right">
                            <span id="dzuhurTotalToday" class="text-3xl font-black text-indigo-400 font-mono">{{ $data['dzuhur']['total_today'] }}</span>
                            <span class="text-xs text-slate-400 ml-1">Siswa</span>
                        </div>
                    </div>

                    <!-- Recent Check-ins List -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-2">
                            <span>⏱️ Log Presensi Dzuhur/Jum'at</span>
                            <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded text-indigo-300">{{ Carbon\Carbon::today('Asia/Jakarta')->dayOfWeek === 5 ? 'Khusus Putra (Jum\'at)' : 'Putra & Putri' }}</span>
                        </div>
                        <div id="dzuhurRecentLogs" class="space-y-1.5 max-h-56 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['dzuhur']['recent'] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log['class'] }} • {{ $log['method'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-500/20 text-indigo-300">
                                            HADIR
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $log['time'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada jamaah shalat yang tercatat hari ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <!-- WIDGET 4: PRESENSI KEGIATAN KEPUTRIAN -->
            <section id="widget-3" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-rose-500 glow-rose transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-rose-500/20 text-rose-300 flex items-center justify-center text-xl font-bold border border-rose-500/30 shrink-0">
                                ✨
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Presensi Keputrian</h2>
                                <p class="text-xs text-slate-400 truncate">{{ $data['keputrian']['schedule'] }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span id="keputrianStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black {{ $data['keputrian']['is_open'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-slate-800 text-slate-400 border border-white/10' }}">
                                <span class="w-2 h-2 rounded-full {{ $data['keputrian']['is_open'] ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                {{ $data['keputrian']['is_open'] ? 'SESI BUKA' : 'SESI TUTUP' }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Total Card -->
                    <div class="bg-gradient-to-r from-rose-950/40 via-slate-900 to-slate-900 border border-rose-500/20 rounded-2xl p-4 mb-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-rose-300/80 font-bold uppercase">Peserta Keputrian Hari Ini</div>
                            <div class="text-xs text-slate-400 mt-0.5">Khusus Siswi Perempuan (Putri)</div>
                        </div>
                        <div class="text-right">
                            <span id="keputrianTotalToday" class="text-3xl font-black text-rose-400 font-mono">{{ $data['keputrian']['total_today'] }}</span>
                            <span class="text-xs text-slate-400 ml-1">Siswi</span>
                        </div>
                    </div>

                    <!-- Recent Check-ins List -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-2">
                            <span>⏱️ Log Presensi Keputrian</span>
                            <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded text-rose-300">Khusus Putri</span>
                        </div>
                        <div id="keputrianRecentLogs" class="space-y-1.5 max-h-56 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['keputrian']['recent'] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log['class'] }} • {{ $log['method'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-500/20 text-rose-300">
                                            HADIR
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $log['time'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada siswi keputrian yang tercatat hari ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <!-- WIDGET 5: PRESENSI EKSTRAKURIKULER -->
            <section id="widget-4" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-purple-500 glow-indigo transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-purple-500/20 text-purple-300 flex items-center justify-center text-xl font-bold border border-purple-500/30 shrink-0">
                                🎯
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Presensi Ekstrakurikuler</h2>
                                <p class="text-xs text-slate-400 truncate">{{ $data['extracurricular']['schedule'] ?? 'Sore Hari (15:00 - 17:30 WIB)' }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                                AKTIF
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Total Card -->
                    <div class="bg-gradient-to-r from-purple-950/40 via-slate-900 to-slate-900 border border-purple-500/20 rounded-2xl p-4 mb-4 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-purple-300/80 font-bold uppercase">Hadir Ekstrakurikuler Hari Ini</div>
                            <div class="text-xs text-slate-400 mt-0.5">Seluruh Unit Minat & Bakat</div>
                        </div>
                        <div class="text-right">
                            <span id="extraTotalToday" class="text-3xl font-black text-purple-400 font-mono">{{ $data['extracurricular']['total_today'] ?? 0 }}</span>
                            <span class="text-xs text-slate-400 ml-1">Siswa</span>
                        </div>
                    </div>

                    <!-- Recent Check-ins List -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 mb-2">
                            <span>⏱️ Log Presensi Ekskul</span>
                            <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded text-purple-300">Live Update</span>
                        </div>
                        <div id="extraRecentLogs" class="space-y-1.5 max-h-56 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['extracurricular']['recent'] ?? [] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }} <span class="text-slate-400 font-normal">({{ $log['class'] }})</span></div>
                                        <div class="text-[10px] text-purple-300 font-semibold truncate">{{ $log['extracurricular'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $log['status'] === 'HADIR' ? 'bg-purple-500/20 text-purple-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $log['status'] }}
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $log['time'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada kegiatan presensi ekstrakurikuler hari ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <!-- WIDGET 6: SETOR HAFALAN & RANKING TAHFIDZ JUZ 30 PER KELAS -->
            <section id="widget-5" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-emerald-500 glow-emerald transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xl font-bold border border-emerald-500/30 shrink-0">
                                📖
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Setor Hafalan Juz 30 & Peringkat</h2>
                                <p class="text-xs text-slate-400 truncate">Target Mutu Kelas X (25), XI (7), XII (5 Surat)</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span class="text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2.5 py-1 rounded-full font-bold">
                                Tahfidz Digital
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Total Card -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="bg-gradient-to-r from-emerald-950/40 to-slate-900 border border-emerald-500/20 rounded-2xl p-2.5">
                            <div class="text-[10px] text-emerald-300 font-bold uppercase">Setoran Hari Ini</div>
                            <div id="tahfidzTotalToday" class="text-xl font-black text-emerald-400 font-mono mt-0.5">{{ $data['tahfidz']['total_today'] }}</div>
                        </div>
                        <div class="bg-gradient-to-r from-teal-950/40 to-slate-900 border border-teal-500/20 rounded-2xl p-2.5">
                            <div class="text-[10px] text-teal-300 font-bold uppercase">Lulus Verifikasi</div>
                            <div id="tahfidzPassedToday" class="text-xl font-black text-teal-400 font-mono mt-0.5">{{ $data['tahfidz']['passed_today'] }}</div>
                        </div>
                    </div>

                    <!-- Sub-Navigation Tabs for Tahfidz Widget -->
                    <div class="flex items-center gap-1 bg-slate-900/80 p-1 rounded-xl mb-3 border border-white/5 text-[11px]">
                        <button type="button" onclick="switchTahfidzView('live')" id="btnTahfidzLive" class="flex-1 py-1 px-2 rounded-lg font-bold bg-emerald-600 text-white transition text-center">
                            ⏱️ Log Terkini
                        </button>
                        <button type="button" onclick="switchTahfidzView('X')" id="btnTahfidzX" class="flex-1 py-1 px-2 rounded-lg font-semibold text-slate-400 hover:text-white transition text-center">
                            👑 Kelas X
                        </button>
                        <button type="button" onclick="switchTahfidzView('XI')" id="btnTahfidzXI" class="flex-1 py-1 px-2 rounded-lg font-semibold text-slate-400 hover:text-white transition text-center">
                            ⭐ Kelas XI
                        </button>
                        <button type="button" onclick="switchTahfidzView('XII')" id="btnTahfidzXII" class="flex-1 py-1 px-2 rounded-lg font-semibold text-slate-400 hover:text-white transition text-center">
                            ✨ Kelas XII
                        </button>
                    </div>

                    <!-- Tab Content: Live Recent Deposits -->
                    <div id="tahfidzTabLive">
                        <div id="tahfidzRecentLogs" class="space-y-1.5 max-h-48 overflow-y-auto kiosk-scroll pr-1 text-xs">
                            @forelse($data['tahfidz']['recent'] as $log)
                                <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                                    <div class="truncate mr-2">
                                        <div class="font-bold text-white truncate">{{ $log['name'] }} <span class="text-slate-400 font-normal">({{ $log['class'] }})</span></div>
                                        <div class="text-[10px] text-emerald-300 font-semibold">{{ $log['surah'] }}</div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $log['grade'] === 'Lulus' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $log['grade'] }}
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $log['date'] }} • {{ $log['time'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada riwayat setoran tahfidz yang dicatat.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab Content: Rankings per Class X, XI, XII -->
                    @foreach(['X', 'XI', 'XII'] as $grd)
                        <div id="tahfidzTab{{ $grd }}" class="hidden">
                            <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2 font-semibold">
                                <span>Peringkat Terbanyak Lulus Target Kelas {{ $grd }} ({{ $data['tahfidz']['rankings'][$grd]['total_target'] ?? '-' }} Surat):</span>
                            </div>
                            <div id="tahfidzRankList{{ $grd }}" class="space-y-1.5 max-h-48 overflow-y-auto kiosk-scroll pr-1 text-xs">
                                @forelse($data['tahfidz']['rankings'][$grd]['students'] ?? [] as $st)
                                    <div class="rounded-xl px-3 py-2 flex items-center justify-between border {{ $st['rank'] === 1 ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200' : 'bg-slate-900/60 border-white/5 text-slate-200' }}">
                                        <div class="flex items-center gap-2 truncate mr-2">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black {{ $st['rank'] === 1 ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400' }}">
                                                {{ $st['rank'] }}
                                            </span>
                                            <div class="truncate">
                                                <div class="font-bold text-white truncate">{{ $st['name'] }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $st['class'] }} • {{ $st['badge'] }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-sm font-black text-emerald-300 font-mono">{{ $st['lulus_count'] }} <span class="text-[10px] font-sans text-slate-400">/ {{ $st['total_target'] }}</span></span>
                                            <div class="text-[10px] text-emerald-400 font-bold">{{ $st['pct'] }}%</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-slate-500 text-xs italic">Belum ada siswa kelas {{ $grd }} yang menyetorkan surat target.</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- WIDGET 7: RANKING POIN PRESTASI SISWA (LEADERBOARD TOP 10) -->
            <section id="widget-6" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-yellow-500 glow-amber transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-yellow-500/20 text-yellow-300 flex items-center justify-center text-xl font-bold border border-yellow-500/30 shrink-0">
                                🏆
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Ranking Poin Prestasi</h2>
                                <p class="text-xs text-slate-400 truncate">Top Siswa Berprestasi (Kategori R1 - R10)</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span class="text-xs bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 px-2.5 py-1 rounded-full font-bold">
                                TOP 10 SEKOLAH
                            </span>
                        </div>
                    </div>

                    <!-- Leaderboard Top 10 List -->
                    <div id="rankingList" class="space-y-1.5 max-h-[330px] overflow-y-auto kiosk-scroll pr-1 text-xs">
                        @forelse($data['ranking'] as $item)
                            <div class="rounded-xl px-3 py-2 flex items-center justify-between border {{ $item['rank'] === 1 ? 'bg-yellow-500/10 border-yellow-500/40 text-yellow-200' : ($item['rank'] === 2 ? 'bg-slate-300/10 border-slate-300/30 text-slate-200' : ($item['rank'] === 3 ? 'bg-amber-600/10 border-amber-600/30 text-amber-200' : 'bg-slate-900/60 border-white/5 text-slate-200')) }}">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-black {{ $item['rank'] <= 3 ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $item['rank'] }}
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-white truncate">{{ $item['name'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $item['class'] }} • {{ $item['achievements_count'] }} Prestasi</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-base font-black text-yellow-400 font-mono">+{{ $item['points'] }} <span class="text-[10px] font-sans text-slate-400 font-normal">Poin</span></div>
                                    <div class="text-[9px] text-slate-400">{{ $item['badge'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-500 text-xs italic">Belum ada catatan prestasi yang diverifikasi.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- WIDGET 8: JADWAL AKTIVITAS & MASTER AGENDA SEKOLAH -->
            <section id="widget-7" class="kiosk-widget glass-panel rounded-3xl p-5 sm:p-6 flex flex-col justify-between border-t-4 border-t-sky-500 glow-indigo transition-all duration-300">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-white/10 min-w-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-sky-500/20 text-sky-300 flex items-center justify-center text-xl font-bold border border-sky-500/30 shrink-0">
                                ⏱️
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight truncate">Jadwal Agenda Sekolah</h2>
                                <p class="text-xs text-slate-400 truncate">Pengaturan Jam Presensi & Ibadah Master</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 whitespace-nowrap">
                            <span class="text-xs bg-sky-500/20 text-sky-300 border border-sky-500/30 px-2.5 py-1 rounded-full font-bold">
                                MASTER JADWAL
                            </span>
                        </div>
                    </div>

                    <!-- Master Schedule Cards List -->
                    <div id="schedulesList" class="space-y-2 max-h-[330px] overflow-y-auto kiosk-scroll pr-1 text-xs">
                        @foreach($data['schedules'] ?? [] as $key => $sch)
                            <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-3 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-white text-xs">{{ $sch['name'] }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        {{ $sch['time'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-0.5">
                                    <span>📅 {{ $sch['days'] }}</span>
                                    <span class="text-teal-400 font-semibold">{{ $sch['target'] }}</span>
                                </div>
                                @if(!empty($sch['cutoff']))
                                    <div class="mt-1 text-[10px] text-amber-400/90 font-semibold flex items-center gap-1">
                                        <span>⚠️</span> <span>{{ $sch['cutoff'] }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- FOOTER RUNNING TICKER (School Updates & Worship Reminders) -->
    <footer class="glass-panel border-t border-white/10 px-4 py-2.5 flex items-center gap-3 overflow-hidden shadow-2xl">
        <div class="flex items-center gap-1.5 shrink-0 px-2.5 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-lg text-xs font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
            <span>INFORMASI</span>
        </div>
        <div class="flex-1 overflow-hidden whitespace-nowrap text-xs text-slate-300 font-medium">
            <span id="kioskTicker" class="animate-ticker">
                {{ $data['ticker'] }}
            </span>
        </div>
        <div class="shrink-0 text-[11px] text-slate-500 hidden sm:block font-mono">
            SMAN 1 LENGKONG • TAHUN PELAJARAN 2026/2027
        </div>
    </footer>

    <!-- REAL-TIME CLOCK, POLLING & CAROUSEL SCRIPTS -->
    <script>
        // State
        let currentMode = 'grid'; // 'grid' or 'carousel'
        let currentWidgetIdx = 0;
        let carouselInterval = null;
        let carouselCountdown = 12;
        let countdownTimer = null;
        const totalWidgets = 8;

        // Switch Tahfidz Sub-views (Live vs Grade Rankings)
        function switchTahfidzView(view) {
            const tabs = ['live', 'X', 'XI', 'XII'];
            tabs.forEach(t => {
                const btn = document.getElementById(`btnTahfidz${t === 'live' ? 'Live' : t}`);
                const panel = document.getElementById(`tahfidzTab${t === 'live' ? 'Live' : t}`);
                if (btn && panel) {
                    if (t === view) {
                        btn.className = 'flex-1 py-1 px-2 rounded-lg font-bold bg-emerald-600 text-white transition text-center shadow-sm';
                        panel.classList.remove('hidden');
                    } else {
                        btn.className = 'flex-1 py-1 px-2 rounded-lg font-semibold text-slate-400 hover:text-white transition text-center';
                        panel.classList.add('hidden');
                    }
                }
            });
        }

        // 1. Digital Clock (Updated every second)
        function updateLiveClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds} WIB`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('liveDate').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // 2. Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // 3. Mode Toggle (Grid vs Carousel)
        function setKioskMode(mode) {
            currentMode = mode;
            const btnGrid = document.getElementById('btnModeGrid');
            const btnCarousel = document.getElementById('btnModeCarousel');
            const progContainer = document.getElementById('carouselProgressBarContainer');

            if (mode === 'grid') {
                btnGrid.className = 'px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-sm';
                btnCarousel.className = 'px-3 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5';
                progContainer.classList.add('hidden');
                clearInterval(carouselInterval);
                clearInterval(countdownTimer);

                // Show all widgets
                for (let i = 0; i < totalWidgets; i++) {
                    const el = document.getElementById(`widget-${i}`);
                    if (el) {
                        el.classList.remove('hidden', 'col-span-full', 'scale-100');
                    }
                }
            } else {
                btnCarousel.className = 'px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-sm';
                btnGrid.className = 'px-3 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5';
                progContainer.classList.remove('hidden');

                startCarousel();
            }
        }

        // 4. Focus Specific Widget (In Carousel Mode or On Demand)
        function focusWidget(idx) {
            currentWidgetIdx = idx;
            // Update tab button styles
            document.querySelectorAll('.tab-btn').forEach((btn, i) => {
                if (i === idx) {
                    btn.className = 'tab-btn px-3 py-1 rounded-lg font-bold transition bg-white/10 text-teal-300 hover:bg-white/15';
                } else {
                    btn.className = 'tab-btn px-3 py-1 rounded-lg font-bold transition text-slate-400 hover:text-white hover:bg-white/5';
                }
            });

            if (currentMode === 'carousel') {
                for (let i = 0; i < totalWidgets; i++) {
                    const el = document.getElementById(`widget-${i}`);
                    if (el) {
                        if (i === idx) {
                            el.classList.remove('hidden');
                            el.classList.add('col-span-full');
                        } else {
                            el.classList.add('hidden');
                            el.classList.remove('col-span-full');
                        }
                    }
                }
                resetCarouselTimer();
            } else {
                // In grid mode, smoothly scroll to that widget
                const el = document.getElementById(`widget-${idx}`);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('ring-2', 'ring-teal-400');
                    setTimeout(() => el.classList.remove('ring-2', 'ring-teal-400'), 1500);
                }
            }
        }

        function resetCarouselTimer() {
            carouselCountdown = 12;
            const bar = document.getElementById('carouselProgressBar');
            bar.style.width = '0%';
        }

        function startCarousel() {
            focusWidget(currentWidgetIdx);
            clearInterval(carouselInterval);
            clearInterval(countdownTimer);

            carouselCountdown = 12;
            const bar = document.getElementById('carouselProgressBar');
            
            countdownTimer = setInterval(() => {
                carouselCountdown--;
                if (carouselCountdown < 0) {
                    carouselCountdown = 12;
                    currentWidgetIdx = (currentWidgetIdx + 1) % totalWidgets;
                    focusWidget(currentWidgetIdx);
                }
                document.getElementById('carouselTimer').textContent = `${carouselCountdown}s`;
                const pct = ((12 - carouselCountdown) / 12) * 100;
                bar.style.width = `${pct}%`;
            }, 1000);
        }

        // 5. AJAX Polling (Fetch fresh data every 15 seconds)
        async function fetchKioskData() {
            try {
                const res = await fetch('{{ route("kiosk.data") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const d = await res.json();
                
                // Update School Data & Ticker
                if (d.school) {
                    document.getElementById('kioskSchoolName').textContent = d.school.name;
                    const logoContainer = document.getElementById('kioskLogoContainer');
                    if (d.school.logo) {
                        logoContainer.innerHTML = `<img src="/storage/${d.school.logo}" alt="Logo" class="w-8 h-8 object-contain">`;
                    } else {
                        logoContainer.innerHTML = `<span class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-sky-300">S1L</span>`;
                    }
                }
                
                if (d.ticker) {
                    document.getElementById('kioskTicker').textContent = d.ticker;
                }

                // Update Presensi Harian
                document.getElementById('dailyTotalStudents').textContent = d.daily.total_students;
                document.getElementById('dailyPercentage').textContent = d.daily.percentage + '%';
                document.getElementById('dailyProgressBar').style.width = d.daily.percentage + '%';
                document.getElementById('dailyHadir').textContent = d.daily.hadir;
                document.getElementById('dailyTerlambat').textContent = d.daily.terlambat;
                document.getElementById('dailyIzin').textContent = d.daily.izin;
                document.getElementById('dailySakit').textContent = d.daily.sakit;
                document.getElementById('dailyAlpa').textContent = d.daily.alpa;

                renderList('dailyRecentLogs', d.daily.recent, item => `
                    <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <div class="font-bold text-white truncate">${item.name}</div>
                            <div class="text-[10px] text-slate-400">${item.class} • ${item.method}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black ${item.status === 'HADIR' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'}">
                                ${item.status}
                            </span>
                            <span class="text-[11px] font-mono text-slate-400">${item.time}</span>
                        </div>
                    </div>
                `, 'Belum ada log presensi masuk hari ini.');

                // Update Dhuha
                document.getElementById('dhuhaTotalToday').textContent = d.dhuha.total_today;
                updateStatusBadge('dhuhaStatusBadge', d.dhuha.is_open);
                renderList('dhuhaRecentLogs', d.dhuha.recent, item => `
                    <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <div class="font-bold text-white truncate">${item.name}</div>
                            <div class="text-[10px] text-slate-400">${item.class} • ${item.method}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-500/20 text-amber-300">HADIR</span>
                            <span class="text-[11px] font-mono text-slate-400">${item.time}</span>
                        </div>
                    </div>
                `, 'Belum ada jamaah dhuha yang tercatat hari ini.');

                // Update Dzuhur
                document.getElementById('dzuhurTotalToday').textContent = d.dzuhur.total_today;
                updateStatusBadge('dzuhurStatusBadge', d.dzuhur.is_open);
                renderList('dzuhurRecentLogs', d.dzuhur.recent, item => `
                    <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <div class="font-bold text-white truncate">${item.name}</div>
                            <div class="text-[10px] text-slate-400">${item.class} • ${item.method}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-500/20 text-indigo-300">HADIR</span>
                            <span class="text-[11px] font-mono text-slate-400">${item.time}</span>
                        </div>
                    </div>
                `, 'Belum ada jamaah shalat yang tercatat hari ini.');

                // Update Keputrian
                document.getElementById('keputrianTotalToday').textContent = d.keputrian.total_today;
                updateStatusBadge('keputrianStatusBadge', d.keputrian.is_open);
                renderList('keputrianRecentLogs', d.keputrian.recent, item => `
                    <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <div class="font-bold text-white truncate">${item.name}</div>
                            <div class="text-[10px] text-slate-400">${item.class} • ${item.method}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-500/20 text-rose-300">HADIR</span>
                            <span class="text-[11px] font-mono text-slate-400">${item.time}</span>
                        </div>
                    </div>
                `, 'Belum ada siswi keputrian yang tercatat hari ini.');

                // Update Ekstrakurikuler
                if (d.extracurricular) {
                    const extraTotalEl = document.getElementById('extraTotalToday');
                    if (extraTotalEl) extraTotalEl.textContent = d.extracurricular.total_today || 0;
                    renderList('extraRecentLogs', d.extracurricular.recent, item => `
                        <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                            <div class="truncate mr-2">
                                <div class="font-bold text-white truncate">${item.name} <span class="text-slate-400 font-normal">(${item.class})</span></div>
                                <div class="text-[10px] text-purple-300 font-semibold truncate">${item.extracurricular}</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black ${item.status === 'HADIR' ? 'bg-purple-500/20 text-purple-300' : 'bg-amber-500/20 text-amber-300'}">
                                    ${item.status}
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">${item.time}</span>
                            </div>
                        </div>
                    `, 'Belum ada kegiatan presensi ekstrakurikuler hari ini.');
                }

                // Update Tahfidz
                document.getElementById('tahfidzTotalToday').textContent = d.tahfidz.total_today;
                document.getElementById('tahfidzPassedToday').textContent = d.tahfidz.passed_today;
                renderList('tahfidzRecentLogs', d.tahfidz.recent, item => `
                    <div class="bg-slate-900/60 border border-white/5 rounded-xl px-3 py-2 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <div class="font-bold text-white truncate">${item.name} <span class="text-slate-400 font-normal">(${item.class})</span></div>
                            <div class="text-[10px] text-emerald-300 font-semibold">${item.surah}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black ${item.grade === 'Lulus' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'}">
                                ${item.grade}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">${item.date} • ${item.time}</div>
                        </div>
                    </div>
                `, 'Belum ada riwayat setoran tahfidz yang dicatat.');

                // Update Tahfidz Class Rankings
                if (d.tahfidz && d.tahfidz.rankings) {
                    ['X', 'XI', 'XII'].forEach(grd => {
                        if (d.tahfidz.rankings[grd]) {
                            const rData = d.tahfidz.rankings[grd];
                            renderList(`tahfidzRankList${grd}`, rData.students, st => `
                                <div class="rounded-xl px-3 py-2 flex items-center justify-between border ${st.rank === 1 ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200' : 'bg-slate-900/60 border-white/5 text-slate-200'}">
                                    <div class="flex items-center gap-2 truncate mr-2">
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black ${st.rank === 1 ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400'}">
                                            ${st.rank}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-white truncate">${st.name}</div>
                                            <div class="text-[10px] text-slate-400">${st.class} • ${st.badge}</div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-sm font-black text-emerald-300 font-mono">${st.lulus_count} <span class="text-[10px] font-sans text-slate-400">/ ${st.total_target}</span></span>
                                        <div class="text-[10px] text-emerald-400 font-bold">${st.pct}%</div>
                                    </div>
                                </div>
                            `, `Belum ada siswa kelas ${grd} yang menyetorkan surat target.`);
                        }
                    });
                }

                // Update Ranking
                renderList('rankingList', d.ranking, item => {
                    const isTop1 = item.rank === 1;
                    const isTop2 = item.rank === 2;
                    const isTop3 = item.rank === 3;
                    const bgClass = isTop1 ? 'bg-yellow-500/10 border-yellow-500/40 text-yellow-200' : (isTop2 ? 'bg-slate-300/10 border-slate-300/30 text-slate-200' : (isTop3 ? 'bg-amber-600/10 border-amber-600/30 text-amber-200' : 'bg-slate-900/60 border-white/5 text-slate-200'));
                    const badgeClass = item.rank <= 3 ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400';
                    return `
                        <div class="rounded-xl px-3 py-2 flex items-center justify-between border ${bgClass}">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-black ${badgeClass}">
                                    ${item.rank}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-white truncate">${item.name}</div>
                                    <div class="text-[10px] text-slate-400">${item.class} • ${item.achievements_count} Prestasi</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-base font-black text-yellow-400 font-mono">+${item.points} <span class="text-[10px] font-sans text-slate-400 font-normal">Poin</span></div>
                                <div class="text-[9px] text-slate-400">${item.badge}</div>
                            </div>
                        </div>
                    `;
                }, 'Belum ada catatan prestasi yang diverifikasi.');

                // Update Ticker
                if (d.ticker) {
                    document.getElementById('kioskTicker').textContent = d.ticker;
                }

                // Update Last Refreshed
                const n = new Date();
                const timeStr = `${String(n.getHours()).padStart(2, '0')}:${String(n.getMinutes()).padStart(2, '0')}:${String(n.getSeconds()).padStart(2, '0')}`;
                document.getElementById('lastUpdatedTime').textContent = timeStr;
            } catch (err) {
                console.error("Kiosk polling error:", err);
            }
        }

        function updateStatusBadge(id, isOpen) {
            const el = document.getElementById(id);
            if (!el) return;
            if (isOpen) {
                el.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse';
                el.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400"></span> SESI BUKA';
            } else {
                el.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-slate-800 text-slate-400 border border-white/10';
                el.innerHTML = '<span class="w-2 h-2 rounded-full bg-slate-500"></span> SESI TUTUP';
            }
        }

        function renderList(targetId, items, templateFn, emptyText) {
            const container = document.getElementById(targetId);
            if (!container) return;
            if (!items || items.length === 0) {
                container.innerHTML = `<div class="text-center py-6 text-slate-500 text-xs italic">${emptyText}</div>`;
                return;
            }
            container.innerHTML = items.map(templateFn).join('');
        }

        // Auto poll every 15 seconds
        setInterval(fetchKioskData, 15000);

        // Auto reload page entirely every 15 minutes to reflect application setting or layout changes
        setTimeout(() => window.location.reload(), 15 * 60 * 1000);
    </script>
</body>
</html>
