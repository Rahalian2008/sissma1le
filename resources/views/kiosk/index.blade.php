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
        <script>
        if (localStorage.getItem('kiosk_theme') === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
    </script>
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

        /* Adaptive sizing for compact screens / laptop heights */
        @media (max-height: 720px) {
            .staff-card-photo {
                height: 4.25rem !important; /* ~68px */
                width: 3.5rem !important;   /* ~56px */
            }
        }
        /* Light Theme Styling for Kiosk */
        html.light, html.light body {
            background-color: #f8fafc;
            background: radial-gradient(circle at top right, #e2e8f0, #f8fafc 40%, #f1f5f9 100%);
            color: #0f172a;
        }
        html.light .glass-panel {
            background: rgba(255, 255, 255, 0.90) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            color: #0f172a !important;
        }
        html.light .glass-card {
            background: rgba(255, 255, 255, 0.82) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            color: #1e293b !important;
        }
        html.light .text-white {
            color: #0f172a !important;
        }
        html.light .text-slate-100 {
            color: #0f172a !important;
        }
        html.light .text-slate-200 {
            color: #1e293b !important;
        }
        html.light .text-slate-300 {
            color: #334155 !important;
        }
        html.light .text-slate-400 {
            color: #64748b !important;
        }
        html.light .text-slate-500 {
            color: #64748b !important;
        }
        html.light .bg-slate-900\/50,
        html.light .bg-slate-900\/60,
        html.light .bg-slate-900\/70,
        html.light .bg-slate-900\/80,
        html.light .bg-slate-900\/90,
        html.light .bg-slate-900,
        html.light .bg-slate-950\/50,
        html.light .bg-slate-950\/60,
        html.light .bg-slate-950\/70,
        html.light .bg-slate-950\/80,
        html.light .bg-slate-950\/90,
        html.light .bg-slate-950 {
            background-color: rgba(241, 245, 249, 0.90) !important;
        }
        html.light .border-white\/10,
        html.light .border-white\/5 {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }
        html.light .kiosk-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        html.light .kiosk-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
        }
        html.light #slidePillsContainer {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
        }
        html.light .slide-nav-pill:not(.bg-teal-500) {
            color: #475569 !important;
        }
        html.light .slide-nav-pill:not(.bg-teal-500):hover {
            color: #0f172a !important;
            background-color: rgba(0, 0, 0, 0.05) !important;
        }
        html.light footer {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
        }
        html.light #kioskTicker {
            color: #1e293b !important;
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
                <p class="text-[9.5px] sm:text-[10px] text-slate-400 font-medium truncate max-w-[200px] sm:max-w-[260px] hidden 2xl:block leading-tight">
                    Layar Informasi Digital Terpadu &bull; TV Display
                </p>
            </div>
        </div>

                <!-- Slide Switcher Pills (Desktop & Tablet) - Responsif tanpa terpotong -->
        <div id="slidePillsContainer" class="hidden md:flex items-center gap-0.5 lg:gap-1 bg-slate-950/80 border border-white/10 rounded-xl p-1 text-xs shrink min-w-0 flex-1 max-w-fit overflow-x-auto no-scrollbar mx-1 scroll-smooth">
            <button type="button" onclick="goToSlide(0)" id="pill-slide-0" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold transition flex items-center gap-1 bg-teal-500 text-slate-950 shadow-sm whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>1. Profil</span>
            </button>
            <button type="button" onclick="goToSlide(1)" id="pill-slide-1" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>2. Guru &amp; TU</span>
            </button>
            <button type="button" onclick="goToSlide(2)" id="pill-slide-2" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>3. Rombel</span>
            </button>
            <button type="button" onclick="goToSlide(3)" id="pill-slide-3" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>4. Perpus</span>
            </button>
            <button type="button" onclick="goToSlide(4)" id="pill-slide-4" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>5. BK</span>
            </button>
            <button type="button" onclick="goToSlide(5)" id="pill-slide-5" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>6. Presensi</span>
            </button>
            <button type="button" onclick="goToSlide(6)" id="pill-slide-6" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>7. Ibadah</span>
            </button>
            <button type="button" onclick="goToSlide(7)" id="pill-slide-7" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>8. Ekskul</span>
            </button>
            <button type="button" onclick="goToSlide(8)" id="pill-slide-8" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>9. Tahfidz</span>
            </button>
            <button type="button" onclick="goToSlide(9)" id="pill-slide-9" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>10. Prestasi</span>
            </button>
            <button type="button" onclick="goToSlide(10)" id="pill-slide-10" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>11. Piket</span>
            </button>
            <button type="button" onclick="goToSlide(11)" id="pill-slide-11" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>12. Berita</span>
            </button>
            <button type="button" onclick="goToSlide(12)" id="pill-slide-12" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>13. Agenda</span>
            </button>
            <button type="button" onclick="goToSlide(13)" id="pill-slide-13" class="slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]">
                <span>14. Tamu</span>
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
                    <span id="slideCountdownLabel" class="font-mono text-[11px] text-teal-300">18s</span>
                </button>
                <button type="button" onclick="nextSlide()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Slide Berikutnya (Panah Kanan)">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

                        <!-- Theme Toggle Button (Dark / Light) -->
            <button type="button" id="themeToggleBtn" onclick="toggleKioskTheme()" class="p-2 rounded-xl bg-slate-900/90 border border-white/10 hover:border-teal-500/50 text-slate-300 hover:text-white transition" title="Ganti Tema (Gelap / Terang)">
                <span id="themeToggleIcon" class="text-sm">🌙</span>
            </button>

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
        <!-- SLIDE 0: PROFIL RESMI KELEMBAGAAN & KEPEMIMPINAN SMAN 1 LENGKONG -->
        <!-- ========================================================================= -->
        <section id="slide-0" class="kiosk-slide  h-full w-full flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-sm">01</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Profil Resmi Kelembagaan &amp; Kepemimpinan Sekolah</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Identitas lengkap, legalitas institusi, akreditasi, pimpinan kepala sekolah, serta visi &amp; misi resmi</p>
                    </div>
                </div>
                <div class="text-[11px] text-teal-300 font-mono hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-500/10 border border-teal-500/30">
                    <span>🏛️ Profil Lembaga &amp; Pimpinan</span>
                </div>
            </div>

            <!-- Main Grid: Left 5 Cols (Identitas Lengkap & Kepala Sekolah), Right 7 Cols (Visi & Misi Terbaca Jelas) -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left Column (5 Cols): Identitas Sekolah & Kepala Sekolah -->
                <div class="lg:col-span-5 flex flex-col h-full min-h-0 gap-3 justify-between overflow-hidden">
                    <!-- School Identity Specs Card (Data dari Master Data - Identitas Sekolah) -->
                    <div class="glass-panel rounded-2xl border border-white/10 p-3 sm:p-3.5 shadow-lg flex-1 min-h-0 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-indigo-600 p-0.5 shadow-md shrink-0">
                                        <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center font-black text-white text-xs">
                                            1LE
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-black text-white leading-tight">{{ $data['profile']['name'] }}</h3>
                                        <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                                            {{ $data['profile']['province_dept'] }} &bull; {{ $data['profile']['branch_office'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                    {{ str_starts_with($data['profile']['accreditation'], 'Akreditasi') ? $data['profile']['accreditation'] : 'Akreditasi ' . $data['profile']['accreditation'] }}
                                </span>
                            </div>

                            <!-- 6-Grid School Meta Details -->
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">NPSN</div>
                                    <div class="font-mono font-bold text-white text-[11px] mt-0.5">{{ $data['profile']['npsn'] }}</div>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">NSS</div>
                                    <div class="font-mono font-bold text-white text-[11px] mt-0.5">{{ $data['profile']['nss'] }}</div>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Status / Kurikulum</div>
                                    <div class="font-bold text-teal-300 text-[10.5px] truncate mt-0.5">{{ $data['profile']['status'] }} &bull; Merdeka</div>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Telepon / Kontak</div>
                                    <div class="font-mono text-slate-200 text-[10.5px] mt-0.5 truncate">{{ $data['profile']['phone'] }}</div>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Email Resmi</div>
                                    <div class="font-mono text-slate-200 text-[10.5px] mt-0.5 truncate">{{ $data['profile']['email'] }}</div>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Kepala Tata Usaha</div>
                                    <div class="font-bold text-indigo-300 text-[10.5px] mt-0.5 truncate">{{ $data['profile']['head_of_admin'] }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Address bar -->
                        <div class="mt-2 pt-2 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-300">
                            <span class="truncate">📍 {{ $data['profile']['address'] }}</span>
                            <span class="font-mono text-teal-300 shrink-0 ml-2">{{ $data['profile']['website'] }}</span>
                        </div>
                    </div>

                    <!-- Kepala Sekolah Card (Cukup Photo, Nama, NIP dari Master Data - Identitas Sekolah) -->
                    <div class="glass-panel rounded-2xl border border-amber-500/30 p-3 sm:p-3.5 xl:p-4 shadow-xl bg-gradient-to-r from-slate-900/90 via-slate-900/95 to-amber-950/20 glow-gold shrink-0">
                        <div class="flex items-center gap-3.5 xl:gap-4">
                            <!-- Photo Portrait (object-position: top center, head NOT cut off) -->
                            <div class="w-20 h-24 sm:w-24 sm:h-28 xl:w-26 xl:h-32 rounded-2xl overflow-hidden p-1 bg-gradient-to-tr from-amber-400 via-yellow-500 to-amber-600 shadow-xl shrink-0">
                                <div class="w-full h-full bg-slate-950 rounded-[12px] overflow-hidden flex items-center justify-center">
                                    @if(!empty($data['profile']['principal_photo']))
                                        <img src="{{ $data['profile']['principal_photo'] }}" alt="{{ $data['profile']['principal_name'] }}" class="w-full h-full object-cover" style="object-position: top center;">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-900 to-amber-950 text-amber-300 font-extrabold text-xl xl:text-2xl">
                                            PS
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <span class="px-2 py-0.5 rounded-full text-[8.5px] xl:text-[9.5px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1 mb-1">
                                    <span>👑</span> PIMPINAN SEKOLAH
                                </span>
                                <h3 class="text-sm sm:text-base xl:text-lg font-black text-white leading-tight truncate" title="{{ $data['profile']['principal_name'] }}">
                                    {{ $data['profile']['principal_name'] }}
                                </h3>
                                <div class="text-[11px] xl:text-xs font-bold text-amber-300 mt-0.5">
                                    {{ $data['profile']['principal_title'] ?? 'Kepala SMAN 1 Lengkong' }}
                                </div>
                                <div class="mt-1.5 inline-block px-2.5 py-0.5 rounded-md bg-slate-900/90 border border-white/10 text-[10px] xl:text-[11px] font-mono font-bold text-slate-200">
                                    NIP {{ $data['profile']['principal_nip'] }}
                                </div>
                                <div class="mt-2 text-[10px] xl:text-[11px] text-slate-300 italic flex items-center gap-1.5 border-t border-amber-500/20 pt-1.5 truncate">
                                    <span>💬</span> "Maju Bersama, Hebat Semua! Menuju S1L Berkarakter & Unggul"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (7 Cols): VISI & MISI SEKOLAH (Harus Terbaca Jelas & Terbuka) -->
                <div class="lg:col-span-7 flex flex-col h-full min-h-0 glass-panel rounded-2xl border border-white/10 p-3.5 sm:p-4 xl:p-5 justify-between overflow-hidden shadow-lg">
                    <!-- Visi Section (Prominent Banner) -->
                    <div class="p-3 sm:p-3.5 xl:p-4 rounded-xl bg-gradient-to-r from-teal-950/60 via-slate-900/80 to-indigo-950/60 border border-teal-500/30 shrink-0 shadow-md">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] xl:text-xs font-black text-teal-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🌟</span> VISI SEKOLAH RESMI
                            </span>
                            <span class="text-[9.5px] xl:text-[11px] text-slate-400 font-mono">SMAN 1 Lengkong</span>
                        </div>
                        <p class="text-xs sm:text-sm md:text-base xl:text-lg text-white font-bold italic leading-relaxed">
                            "{{ $data['profile']['vision'] }}"
                        </p>
                        @if(!empty($data['profile']['vision_sub']))
                            <p class="text-[11px] sm:text-xs xl:text-sm text-teal-300 font-semibold mt-1">
                                {{ $data['profile']['vision_sub'] }}
                            </p>
                        @endif
                    </div>

                    <!-- Misi Section (2x2 Grid, Legible, No Cutoff, Fills Height) -->
                    <div class="flex-1 min-h-0 flex flex-col justify-between my-2 overflow-hidden">
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-white/10 shrink-0">
                            <span class="text-[10px] xl:text-xs font-black text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🎯</span> MISI SEKOLAH
                            </span>
                            <span class="text-[9.5px] xl:text-[11px] text-slate-400 font-mono">{{ count($data['profile']['missions'] ?? []) }} Misi Unggulan</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 grid-rows-2 gap-2.5 xl:gap-3.5 flex-1 min-h-0 overflow-hidden">
                            @forelse($data['profile']['missions'] ?? [] as $misi)
                                <div class="p-3 xl:p-3.5 rounded-xl bg-slate-900/80 border border-white/10 hover:border-indigo-500/30 transition flex flex-col justify-center h-full">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="w-6 h-6 xl:w-7 xl:h-7 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center font-black text-[10px] xl:text-xs shrink-0 font-mono">
                                            {{ $misi['number'] ?? '01' }}
                                        </div>
                                        <h4 class="text-xs sm:text-sm xl:text-base font-extrabold text-white leading-tight truncate">{{ $misi['title'] ?? '' }}</h4>
                                    </div>
                                    <p class="text-[11px] sm:text-xs xl:text-sm text-slate-300 leading-relaxed">
                                        {{ $misi['desc'] ?? '' }}
                                    </p>
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-slate-500 text-xs italic py-4">
                                    Data misi sekolah belum dikonfigurasi.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Bottom Tagline -->
                    <div class="pt-2 border-t border-white/5 flex items-center justify-between text-[10px] xl:text-xs text-slate-400 shrink-0">
                        <span>Mewujudkan Generasi Berakhlak Mulia, Cerdas, Terampil &amp; Berwawasan Global</span>
                        <span class="font-mono text-teal-400 font-bold hidden sm:inline">SIS-SMA1LE &bull; TV KIOSK</span>
                    </div>
                        <span class="text-teal-400 font-semibold font-mono">SIS-SMA1LE</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 1: DIREKTORI DEWAN GURU & TENAGA KEPENDIDIKAN -->
        <!-- ========================================================================= -->
        <section id="slide-1" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-black text-sm">02</div>
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
                    <div class="staff-page h-full w-full {{ $pageIdx === 0 ? 'grid' : 'hidden' }} grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 grid-rows-2 gap-2.5 sm:gap-3 xl:gap-3.5 overflow-y-auto kiosk-scroll" id="staff-page-{{ $pageIdx }}">
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
                                $isHeadmaster = ($st['type'] ?? ($st['category'] ?? '')) === 'kepala_sekolah' || str_contains(strtolower($st['duty'] ?? ''), 'kepala sekolah');
                                $isGuru = ($st['type'] ?? ($st['category'] ?? '')) === 'guru' || !empty($st['mapel']);
                            @endphp
                            <div class="glass-card rounded-2xl border border-white/10 p-2 sm:p-2.5 xl:p-3 flex flex-col items-center text-center justify-between hover:border-teal-500/40 transition duration-300 group relative min-h-0 h-full {{ $isHeadmaster ? 'glow-gold border-amber-500/30 bg-amber-950/20' : 'glow-teal' }}">
                                <!-- Top Status Pill -->
                                <div class="w-full flex items-center justify-between gap-1 mb-1 shrink-0">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[8.5px] xl:text-[9.5px] font-black uppercase tracking-wider truncate {{ $isHeadmaster ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : ($isGuru ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30') }}">
                                        {{ $isHeadmaster ? '👑 Kepala Sekolah' : ($isGuru ? '🎓 Dewan Guru' : '🏢 Staf TU') }}
                                    </span>
                                    <span class="text-[7.5px] sm:text-[8px] xl:text-[9px] font-mono text-slate-400 truncate">
                                        {{ $st['employment_status'] ?? 'ASN' }}
                                    </span>
                                </div>

                                <!-- Photo Container with Elegant Ring (Portrait Ratio, No Head Cutoff) -->
                                <div class="staff-card-photo w-14 h-16 sm:w-16 sm:h-20 md:w-18 md:h-22 lg:w-20 lg:h-24 xl:w-24 xl:h-28 2xl:w-26 2xl:h-30 rounded-xl overflow-hidden p-0.5 sm:p-1 bg-gradient-to-tr {{ $isHeadmaster ? 'from-amber-400 via-yellow-500 to-amber-600' : 'from-teal-400 via-indigo-500 to-sky-400' }} shadow-md my-0.5 sm:my-1 shrink-0 group-hover:scale-105 transition duration-300">
                                    <div class="w-full h-full bg-slate-950 rounded-[9px] overflow-hidden flex items-center justify-center">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="{{ $st['name'] }}" class="w-full h-full object-cover object-top" style="object-position: top center;">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-900 via-slate-800 to-teal-950 text-teal-300 font-extrabold text-sm sm:text-base">
                                                <span>{{ strtoupper(substr($st['name'], 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Staff Name & Details -->
                                <div class="w-full min-w-0 mt-0.5 flex-1 flex flex-col justify-between">
                                    <h4 class="text-[11px] sm:text-xs md:text-[13px] font-black text-white leading-tight truncate group-hover:text-teal-300 transition" title="{{ $st['name'] }}">
                                        {{ $st['name'] }}
                                    </h4>
                                    <!-- NIP -->
                                    <div class="my-0.5">
                                        <span class="inline-block text-[8px] sm:text-[8.5px] md:text-[9px] font-mono text-slate-300 bg-slate-900/90 px-1.5 py-0.5 rounded border border-white/10 truncate max-w-full">
                                            NIP: {{ !empty($st['nip']) && $st['nip'] !== '-' ? $st['nip'] : '-' }}
                                        </span>
                                    </div>
                                    <!-- Tugas Tambahan & Mata Pelajaran -->
                                    <div class="w-full pt-1 border-t border-white/5 space-y-0.5 text-left text-[8px] sm:text-[8.5px] md:text-[9px] leading-tight">
                                        @if($isGuru)
                                            <div class="truncate text-slate-300 flex items-center gap-1" title="Mata Pelajaran: {{ $st['mapel'] ?? '-' }}">
                                                <span class="text-teal-400 font-bold shrink-0">📚 Mapel:</span>
                                                <span class="truncate font-medium text-white">{{ !empty($st['mapel']) ? $st['mapel'] : 'Mata Pelajaran Umum' }}</span>
                                            </div>
                                            <div class="truncate text-slate-300 flex items-center gap-1" title="Tugas Tambahan: {{ $st['duty'] ?? '-' }}">
                                                <span class="text-indigo-400 font-bold shrink-0">⭐ Tugas:</span>
                                                <span class="truncate font-medium text-teal-200">{{ !empty($st['duty']) && $st['duty'] !== 'Guru Pengajar' ? $st['duty'] : ($isHeadmaster ? 'Kepala Sekolah' : 'Guru Pengajar') }}</span>
                                            </div>
                                        @else
                                            <div class="truncate text-slate-300 flex items-center gap-1" title="Tugas: {{ $st['duty'] ?? 'Staf Administrasi' }}">
                                                <span class="text-indigo-400 font-bold shrink-0">🏢 Tugas:</span>
                                                <span class="truncate font-medium text-white">{{ $st['duty'] ?? 'Staf Administrasi' }}</span>
                                            </div>
                                            <div class="truncate text-slate-400 flex items-center gap-1" title="Bidang: {{ !empty($st['tu_duty']) ? $st['tu_duty'] : '-' }}">
                                                <span class="text-teal-400 font-bold shrink-0">📋 Bidang:</span>
                                                <span class="truncate font-medium text-slate-300">{{ !empty($st['tu_duty']) && $st['tu_duty'] !== ($st['duty'] ?? '') ? $st['tu_duty'] : 'Layanan Ketatausahaan' }}</span>
                                            </div>
                                        @endif
                                    </div>
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

        <!-- ========================================================================= -->
        <!-- SLIDE 2: DATA ROMBONGAN BELAJAR (ROMBEL) & DIREKTORI WALI KELAS -->
        <!-- ========================================================================= -->
        <section id="slide-2" class="kiosk-slide hidden h-full w-full flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center font-black text-sm">03</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Data Rombongan Belajar (Rombel) &amp; Direktori Wali Kelas</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Komposisi siswa Laki-laki (L) / Perempuan (P), rasio gender, serta profil &amp; foto wali kelas resmi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Sub-page Navigation for Rombel (1-4 Layar Sistem Slide) -->
                    <div class="flex items-center gap-1 bg-sky-950/60 border border-sky-500/30 rounded-xl p-0.5 text-xs">
                        <button type="button" onclick="prevRombelPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Rombel Sebelumnya">
                            &larr;
                        </button>
                        <span id="rombelPageIndicator" class="px-2 font-mono text-[11px] font-bold text-sky-300">
                            Hal 1 / {{ max(1, count(array_chunk(collect($data['rombels']['list'] ?? [])->all(), 6))) }}
                        </span>
                        <button type="button" onclick="nextRombelPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Rombel Berikutnya">
                            &rarr;
                        </button>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-sky-500/20 text-sky-300 border border-sky-500/30 flex items-center gap-1">
                        <span>🏫</span> {{ $data['rombels']['total_rombels'] ?? 0 }} ROMBEL BELAJAR
                    </span>
                </div>
            </div>

            <!-- Content Area: Top KPI Ribbon + Paginated 1-4 Screen Rombel Cards Grid -->
            <div class="flex-1 min-h-0 flex flex-col gap-3 justify-between overflow-hidden">
                <!-- Top Summary Ribbon: Total Siswa, L, P, Rombel, Avg -->
                <div class="p-2 sm:p-2.5 rounded-2xl bg-gradient-to-r from-sky-950/70 via-slate-900/80 to-indigo-950/70 border border-sky-500/20 grid grid-cols-2 sm:grid-cols-5 gap-2 shrink-0 shadow-lg">
                    <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center text-sm font-bold shrink-0">
                            👥
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-400 uppercase font-bold">Total Seluruh Siswa</div>
                            <div class="text-sm sm:text-base font-black text-white font-mono leading-none mt-0.5">{{ $data['rombels']['total_students'] ?? 0 }} <span class="text-[9.5px] font-normal text-slate-400">Orang</span></div>
                        </div>
                    </div>

                    <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center text-sm font-bold shrink-0">
                            👦
                        </div>
                        <div>
                            <div class="text-[9px] text-teal-400 uppercase font-bold">Laki-Laki (L)</div>
                            <div class="text-sm sm:text-base font-black text-teal-300 font-mono leading-none mt-0.5">{{ $data['rombels']['total_male'] ?? 0 }} <span class="text-[9.5px] font-normal text-slate-400">Siswa</span></div>
                        </div>
                    </div>

                    <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center text-sm font-bold shrink-0">
                            👧
                        </div>
                        <div>
                            <div class="text-[9px] text-rose-400 uppercase font-bold">Perempuan (P)</div>
                            <div class="text-sm sm:text-base font-black text-rose-300 font-mono leading-none mt-0.5">{{ $data['rombels']['total_female'] ?? 0 }} <span class="text-[9.5px] font-normal text-slate-400">Siswi</span></div>
                        </div>
                    </div>

                    <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-sm font-bold shrink-0">
                            🏫
                        </div>
                        <div>
                            <div class="text-[9px] text-indigo-400 uppercase font-bold">Total Rombel</div>
                            <div class="text-sm sm:text-base font-black text-indigo-300 font-mono leading-none mt-0.5">{{ $data['rombels']['total_rombels'] ?? 0 }} <span class="text-[9.5px] font-normal text-slate-400">Kelas</span></div>
                        </div>
                    </div>

                    <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5 flex items-center gap-2.5 col-span-2 sm:col-span-1">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-sm font-bold shrink-0">
                            📊
                        </div>
                        <div>
                            <div class="text-[9px] text-amber-400 uppercase font-bold">Rata-Rata Kelas</div>
                            <div class="text-sm sm:text-base font-black text-amber-300 font-mono leading-none mt-0.5">{{ $data['rombels']['avg_per_rombel'] ?? 0 }} <span class="text-[9.5px] font-normal text-slate-400">Siswa/Kls</span></div>
                        </div>
                    </div>
                </div>

                <!-- Paginated Sliding Grid: 6 Rombels Per Screen (3 Cols x 2 Rows) with Homeroom Teacher Photo -->
                <div class="flex-1 min-h-0 relative overflow-hidden">
                    @php
                        $allRombels = collect($data['rombels']['list'] ?? [])->all();
                        $chunkedRombels = array_chunk($allRombels, 6);
                    @endphp

                    @forelse($chunkedRombels as $rIdx => $rombelPage)
                        <div class="rombel-page h-full w-full {{ $rIdx === 0 ? 'grid' : 'hidden' }} grid-cols-1 md:grid-cols-2 lg:grid-cols-3 grid-rows-2 gap-3 xl:gap-4 overflow-y-auto kiosk-scroll" id="rombel-page-{{ $rIdx }}">
                            @foreach($rombelPage as $r)
                                <div class="glass-card rounded-2xl border border-white/10 p-3 sm:p-3.5 xl:p-4 flex flex-col justify-between hover:border-sky-500/40 transition duration-300 group glow-teal relative overflow-hidden shadow-lg h-full">
                                    <!-- Class Title & Grade Badge -->
                                    <div>
                                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center font-black text-xs font-mono">
                                                    {{ $r['grade'] ?? 'X' }}
                                                </div>
                                                <div>
                                                    <h4 class="text-xs sm:text-sm font-black text-white group-hover:text-sky-300 transition truncate">{{ $r['name'] }}</h4>
                                                    <span class="text-[9px] text-slate-400 block">{{ $r['major'] ?? 'Kurikulum Merdeka' }}</span>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-black font-mono bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                                {{ $r['total_count'] }} Siswa
                                            </span>
                                        </div>

                                        <!-- Gender L / P Metrics & Split Bar -->
                                        <div class="space-y-1 my-1.5">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <div class="flex items-center gap-1 text-teal-300 font-bold">
                                                    <span>👦 L:</span>
                                                    <span class="font-mono text-white">{{ $r['male_count'] }}</span>
                                                    <span class="text-[9.5px] text-slate-400">({{ $r['male_pct'] }}%)</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-rose-300 font-bold">
                                                    <span>👧 P:</span>
                                                    <span class="font-mono text-white">{{ $r['female_count'] }}</span>
                                                    <span class="text-[9.5px] text-slate-400">({{ $r['female_pct'] }}%)</span>
                                                </div>
                                            </div>

                                            <!-- Bi-color split progress bar -->
                                            <div class="w-full h-1.5 rounded-full bg-slate-900 overflow-hidden flex border border-white/5">
                                                <div class="h-full bg-teal-400 transition-all duration-500" style="width: {{ $r['male_pct'] }}%"></div>
                                                <div class="h-full bg-rose-400 transition-all duration-500" style="width: {{ $r['female_pct'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Homeroom Teacher Profile Box with Photo from Master Data - Data Guru & PTK -->
                                    <div class="mt-1.5 pt-1.5 border-t border-white/10 flex items-center gap-2.5 bg-slate-950/60 p-2 rounded-xl">
                                        <!-- Photo Container (Portrait ratio, head not cut off) -->
                                        <div class="w-11 h-14 rounded-lg overflow-hidden p-0.5 bg-gradient-to-tr from-sky-400 to-indigo-500 shadow-md shrink-0">
                                            <div class="w-full h-full bg-slate-900 rounded-[6px] overflow-hidden flex items-center justify-center">
                                                @if(!empty($r['homeroom_teacher']['photo']))
                                                    <img src="{{ $r['homeroom_teacher']['photo'] }}" alt="{{ $r['homeroom_teacher']['name'] }}" class="w-full h-full object-cover" style="object-position: top center;">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-slate-900 text-sky-300 font-extrabold text-xs">
                                                        {{ strtoupper(substr($r['homeroom_teacher']['name'] ?? 'WK', 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="text-[9px] font-bold text-sky-400 uppercase tracking-wider">Wali Kelas</div>
                                            <div class="text-xs font-extrabold text-white truncate group-hover:text-sky-300 transition" title="{{ $r['homeroom_teacher']['name'] }}">
                                                {{ $r['homeroom_teacher']['name'] }}
                                            </div>
                                            <div class="text-[9.5px] font-mono text-slate-400 truncate">
                                                {{ !empty($r['homeroom_teacher']['nip']) && $r['homeroom_teacher']['nip'] !== '-' ? 'NIP ' . $r['homeroom_teacher']['nip'] : 'Pendidik Resmi S1L' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="col-span-full h-full flex items-center justify-center text-slate-500 text-xs italic py-10">
                            Data rombel belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 3: PERPUSTAKAAN & LITERASI DIGITAL -->
        <!-- ========================================================================= -->
        <section id="slide-3" class="hidden flex-col h-full min-h-0 overflow-hidden">
            <!-- Header Slide -->
            <div class="flex items-center justify-between pb-2 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-400 font-mono font-bold text-xs">04</span>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                            <span>Perpustakaan &amp; Literasi Digital</span>
                            <span class="text-slate-500">&bull;</span>
                            <span class="text-xs font-semibold text-indigo-300">Pusat Sumber Belajar &amp; Membaca Siswa</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Statistik pengunjung harian, klasifikasi DDC, duta literasi, pengurus perpustakaan &amp; peminjaman buku aktif</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        📚 E-Library SMA1LE
                    </span>
                    <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 text-[11px] font-bold transition flex items-center gap-1 border border-indigo-500/30">
                        <span>Buka Kios Perpus &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 overflow-hidden pt-1">
                <!-- Left (7 Cols): KPI + DDC Dewey Classes + Popular Books -->
                <div class="lg:col-span-7 flex flex-col h-full min-h-0 gap-2.5 overflow-hidden">
                    <!-- KPI Cards (4 Cards) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 shrink-0">
                        <div class="glass-panel p-2.5 rounded-2xl border border-white/5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-base shrink-0 font-bold">
                                👥
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9.5px] text-slate-400 font-bold block truncate">Pengunjung Hari Ini</span>
                                <span id="libKioskVisitorsToday" class="text-base font-black text-white font-mono leading-none">{{ $data['library']['total_today'] ?? 0 }}</span>
                                <span class="text-[8.5px] text-cyan-300 block truncate">S: {{ $data['library']['students_today'] ?? 0 }} | G: {{ $data['library']['teachers_today'] ?? 0 }} | U: {{ $data['library']['general_today'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="glass-panel p-2.5 rounded-2xl border border-white/5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base shrink-0 font-bold">
                                📚
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9.5px] text-slate-400 font-bold block truncate">Total Judul Buku</span>
                                <span class="text-base font-black text-white font-mono leading-none">{{ number_format($data['library']['total_books'] ?? 0) }}</span>
                                <span class="text-[8.5px] text-indigo-300 block truncate">10 Kelas DDC</span>
                            </div>
                        </div>

                        <div class="glass-panel p-2.5 rounded-2xl border border-white/5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base shrink-0 font-bold">
                                📖
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9.5px] text-slate-400 font-bold block truncate">Eksemplar Siap Baca</span>
                                <span class="text-base font-black text-white font-mono leading-none">{{ number_format($data['library']['available_copies'] ?? 0) }}</span>
                                <span class="text-[8.5px] text-emerald-300 block truncate">Buku Fisik &amp; Digital</span>
                            </div>
                        </div>

                        <div class="glass-panel p-2.5 rounded-2xl border border-white/5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base shrink-0 font-bold">
                                🔄
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9.5px] text-slate-400 font-bold block truncate">Peminjaman Aktif</span>
                                <span class="text-base font-black text-white font-mono leading-none">{{ $data['library']['active_loans'] ?? 0 }}</span>
                                <span class="text-[8.5px] text-amber-300 block truncate">Sedang Dipinjam</span>
                            </div>
                        </div>
                    </div>

                    <!-- 10 Kelas Klasifikasi DDC (Dewey Decimal Classification) -->
                    <div class="glass-panel rounded-2xl p-2.5 sm:p-3 border border-white/5 flex flex-col shrink-0">
                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 shrink-0">
                            <h3 class="text-xs sm:text-sm font-black text-white flex items-center gap-2">
                                <span>📚</span>
                                <span>Koleksi Berdasarkan Klasifikasi DDC (Dewey Decimal Classification)</span>
                            </h3>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-semibold">
                                10 Kelas Standar Perpusnas
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-1.5 pt-2">
                            @foreach($data['library']['ddc'] ?? [] as $ddc)
                                <div class="bg-slate-900/90 rounded-xl p-1.5 sm:p-2 border border-white/5 flex flex-col justify-between hover:border-indigo-500/40 transition">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="text-xs">{{ $ddc['icon'] }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[8.5px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            {{ $ddc['code'] }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <div class="text-[9.5px] font-bold text-white truncate" title="{{ $ddc['name'] }}">
                                            {{ $ddc['name'] }}
                                        </div>
                                        <div class="text-[8.5px] font-mono text-emerald-400 font-semibold mt-0.5">
                                            {{ number_format($ddc['count']) }} Judul
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Koleksi Buku Terpopuler & Paling Banyak Dibaca -->
                    <div class="flex-1 min-h-0 glass-panel rounded-2xl p-2.5 sm:p-3 border border-white/5 flex flex-col overflow-hidden">
                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 shrink-0">
                            <h3 class="text-xs sm:text-sm font-black text-white flex items-center gap-2">
                                <span>⭐</span>
                                <span>Koleksi Rekomendasi &amp; Paling Banyak Dibaca</span>
                            </h3>
                            <span class="text-[10px] text-slate-400">Favorit Pemustaka S1L</span>
                        </div>
                        <div class="flex-1 min-h-0 grid grid-cols-2 sm:grid-cols-3 gap-2 pt-2 overflow-y-auto kiosk-scroll">
                            @forelse(collect($data['library']['popular_books'] ?? [])->take(3) as $book)
                                <div class="bg-slate-900/80 rounded-xl p-2 border border-white/5 flex flex-col justify-between hover:border-indigo-500/40 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1">
                                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 truncate">
                                                {{ $book['category'] }}
                                            </span>
                                            <span class="text-[8.5px] font-mono text-emerald-400 shrink-0 font-bold">
                                                Sisa: {{ $book['available'] }}
                                            </span>
                                        </div>
                                        <h4 class="text-[11px] font-bold text-white line-clamp-2 leading-tight" title="{{ $book['title'] }}">
                                            {{ $book['title'] }}
                                        </h4>
                                        <p class="text-[9.5px] text-slate-400 truncate">{{ $book['author'] }}</p>
                                    </div>
                                    <div class="pt-1 mt-1 border-t border-white/5 flex items-center justify-between text-[8.5px] text-slate-500">
                                        <span>Total: {{ $book['total'] }} Eks</span>
                                        <span class="text-indigo-400 font-semibold">Tersedia</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-4 text-slate-500 text-xs italic">
                                    Belum ada data buku populer.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right (5 Cols): Pengurus Perpustakaan + Duta Literasi + Log Kunjungan Pemustaka -->
                <div class="lg:col-span-5 flex flex-col h-full min-h-0 gap-2.5 overflow-hidden">
                    <!-- Pengurus Perpustakaan (Master Data Guru & PTK) -->
                    <div class="glass-panel rounded-2xl p-2.5 sm:p-3 border border-white/5 flex flex-col shrink-0">
                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs">👥</span>
                                <h3 class="text-xs sm:text-sm font-black text-white">Pengurus &amp; Pengelola Perpustakaan</h3>
                            </div>
                            <span class="text-[9.5px] text-slate-400">Master Data PTK S1L</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
                            @forelse($data['library']['managers'] ?? [] as $mgr)
                                <div class="p-2 rounded-xl border transition flex items-center gap-2.5 {{ !empty($mgr['is_head']) ? 'bg-gradient-to-r from-indigo-950/70 via-slate-900/95 to-indigo-900/50 border-indigo-500/40 shadow-sm' : 'bg-slate-900/85 border-white/10 hover:border-indigo-500/40' }}">
                                    <div class="w-12 h-14 sm:w-13 sm:h-16 rounded-xl overflow-hidden shrink-0 border-2 {{ !empty($mgr['is_head']) ? 'border-amber-400/80 ring-2 ring-indigo-500/40' : 'border-indigo-400/40' }} bg-slate-800 shadow-md flex items-center justify-center">
                                        @if(!empty($mgr['photo']))
                                            <img src="{{ $mgr['photo'] }}" alt="{{ $mgr['name'] }}" class="w-full h-full object-cover" style="object-position: top center;">
                                        @else
                                            <span class="text-xl">{{ !empty($mgr['is_head']) ? '👑' : '📚' }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-0.5">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase tracking-wider {{ !empty($mgr['is_head']) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' }}">
                                                {{ !empty($mgr['is_head']) ? '👑 Kepala Perpus' : '📖 Pengelola' }}
                                            </span>
                                            @if(!empty($mgr['status']))
                                                <span class="px-1 py-0.5 rounded text-[8px] font-mono font-bold bg-white/10 text-slate-300">
                                                    {{ $mgr['status'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-extrabold text-[11px] sm:text-xs text-white leading-snug truncate" title="{{ $mgr['name'] }}">
                                            {{ $mgr['name'] }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-mono truncate">
                                            NIP: {{ $mgr['nip'] }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-2 text-slate-500 text-xs italic">
                                    Data pengurus perpustakaan belum dimuat.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Rapor & Ranking (Duta Literasi Terpilih) -->
                    <div class="glass-panel rounded-2xl p-2.5 sm:p-3 border border-white/5 flex flex-col shrink-0">
                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs">🏆</span>
                                <h3 class="text-xs sm:text-sm font-black text-white">Rapor &amp; Ranking: Duta Literasi Terpilih</h3>
                            </div>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold">
                                Siswa Teraktif
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-2">
                            @forelse($data['library']['duta_literasi'] ?? [] as $duta)
                                <div class="p-2 rounded-xl border flex flex-col justify-between text-center relative overflow-hidden {{ $duta['rank'] === 1 ? 'bg-gradient-to-b from-amber-500/20 to-slate-900 border-amber-500/40 shadow-sm' : ($duta['rank'] === 2 ? 'bg-gradient-to-b from-slate-300/20 to-slate-900 border-slate-400/40' : 'bg-gradient-to-b from-amber-700/20 to-slate-900 border-amber-600/40') }}">
                                    <div class="text-base sm:text-lg">{{ $duta['badge'] }}</div>
                                    <div class="mt-0.5 min-w-0">
                                        <span class="text-[8px] font-black uppercase tracking-wider block {{ $duta['rank'] === 1 ? 'text-amber-300' : ($duta['rank'] === 2 ? 'text-slate-300' : 'text-amber-400') }}">
                                            {{ $duta['rank'] === 1 ? 'Juara 1 Emas' : ($duta['rank'] === 2 ? 'Juara 2 Perak' : 'Juara 3 Perunggu') }}
                                        </span>
                                        <div class="font-bold text-[10.5px] sm:text-[11px] text-white truncate mt-0.5" title="{{ $duta['name'] }}">
                                            {{ $duta['name'] }}
                                        </div>
                                        <div class="text-[8.5px] text-slate-400 truncate">
                                            {{ $duta['class'] }}
                                        </div>
                                    </div>
                                    <div class="mt-1 pt-1 border-t border-white/10 text-[8.5px] font-mono font-bold text-emerald-400">
                                        {{ $duta['books_read'] }} Buku Dibaca
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-3 px-2 rounded-xl bg-slate-900/60 border border-dashed border-white/10 text-slate-400 text-xs flex flex-col items-center justify-center gap-1">
                                    <span class="text-base">📚</span>
                                    <span>Data Rapor &amp; Rangking Duta Literasi baru saja di-reset / belum ada peminjaman baru.</span>
                                    <span class="text-[10px] text-slate-500">Peringkat akan otomatis terbit dari akumulasi pembaca &amp; peminjaman buku perpustakaan.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Log Kunjungan Pemustaka Terkini (Laporan Buku Tamu) -->
                    <div class="flex-1 min-h-0 glass-panel rounded-2xl p-2.5 sm:p-3 border border-white/5 flex flex-col overflow-hidden">
                        <div class="flex items-center justify-between pb-1.5 border-b border-white/5 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs">📋</span>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-black text-white">Laporan Buku Tamu Pengunjung</h3>
                                    <p class="text-[9.5px] text-slate-400">Presensi mandiri pemustaka terkini</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Hari Ini: {{ $data['library']['total_today'] ?? 0 }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-mono text-slate-300 bg-slate-800/80 border border-white/5">
                                    Bln Ini: {{ $data['library']['total_month'] ?? 0 }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-1.5 pt-2 pr-1">
                            @forelse($data['library']['recent_visitors'] ?? [] as $lv)
                                <div class="p-2 rounded-xl bg-slate-900/80 border border-white/5 flex items-center justify-between gap-2 hover:border-indigo-500/30 transition">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ $lv['type'] === 'Siswa' ? '🎓' : ($lv['type'] === 'Guru/Tendik' ? '👨‍🏫' : '🏛️') }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-extrabold text-[11px] text-white truncate">{{ $lv['name'] }}</div>
                                            <div class="text-[9.5px] text-slate-400 truncate">{{ $lv['class'] }} &bull; <span class="text-indigo-300">{{ $lv['purpose'] }}</span></div>
                                        </div>
                                    </div>
                                    <span class="font-mono text-[9px] font-bold text-slate-300 bg-slate-950 px-1.5 py-0.5 rounded border border-white/5 shrink-0">
                                        {{ $lv['time'] }} WIB
                                    </span>
                                </div>
                            @empty
                                <div class="h-full flex flex-col items-center justify-center text-center py-4 text-slate-500 text-xs space-y-1">
                                    <span class="text-xl">📖</span>
                                    <span class="font-semibold text-slate-400 text-[11px]">Belum ada pengunjung tercatat hari ini.</span>
                                </div>
                            @endforelse
                        </div>

                        <!-- Mini Footer: Info Pojok Baca Digital -->
                        <div class="pt-1.5 mt-1 border-t border-white/5 flex items-center justify-between text-[9.5px] text-slate-400 shrink-0">
                            <span>💻 Layanan E-Book &amp; OPAC Mandiri</span>
                            <span class="text-indigo-400 font-semibold">Tersedia di Lab &amp; Kios Perpus</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 4: BIMBINGAN & KONSELING (BK) -->
        <!-- ========================================================================= -->
        <section id="slide-4" class="hidden flex-col h-full min-h-0 overflow-hidden">
            <!-- Header Slide -->
            <div class="flex items-center justify-between pb-2 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-400 font-mono font-bold text-xs">05</span>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                            <span>Bimbingan &amp; Konseling (BK)</span>
                            <span class="text-slate-500">&bull;</span>
                            <span class="text-xs font-semibold text-purple-300">Layanan Terpadu &amp; Pengembangan Karir Siswa</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Pusat bimbingan pribadi, sosial, belajar, peminatan karir, dan persiapan masuk perguruan tinggi (SNBP/SNBT)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        🤝 Ramah &bull; Aman &bull; Rahasia
                    </span>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3.5 overflow-hidden pt-1">
                <!-- Left (7 Cols): 4 Bidang Layanan BK + Alur Layanan Konseling -->
                <div class="lg:col-span-7 flex flex-col h-full min-h-0 gap-3">
                    <!-- 4 Bidang Layanan Utama BK Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 shrink-0">
                        @foreach($data['bk']['fields'] ?? [] as $bf)
                            <div class="glass-panel p-3 rounded-2xl border border-white/5 space-y-1.5 hover:border-purple-500/40 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">{{ $bf['icon'] }}</span>
                                        <h3 class="text-xs font-black text-white">{{ $bf['field'] }}</h3>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                        {{ $bf['count'] }} Sesi
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 leading-relaxed">{{ $bf['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Program Sukses Karir & Kuliah (SNBP, SNBT, Kedinasan) -->
                    <div class="flex-1 min-h-0 glass-panel rounded-2xl p-3 sm:p-3.5 border border-white/5 flex flex-col justify-between overflow-hidden">
                        <div class="flex items-center justify-between pb-2 border-b border-white/5 shrink-0">
                            <h3 class="text-xs sm:text-sm font-black text-white flex items-center gap-2">
                                <span>🎯</span>
                                <span>Pendampingan Sukses Studi Lanjut &amp; Karir Masa Depan</span>
                            </h3>
                            <span class="text-[10px] font-mono text-purple-300 font-bold">Kelas X, XI, XII</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5 py-2">
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 text-center">
                                <span class="text-lg">🏛️</span>
                                <h4 class="text-xs font-extrabold text-white mt-1">Jalur SNBP</h4>
                                <p class="text-[9.5px] text-slate-400 mt-0.5">Pemetaan nilai rapor &amp; portofolio prestasi unggulan</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 text-center">
                                <span class="text-lg">📝</span>
                                <h4 class="text-xs font-extrabold text-white mt-1">Jalur SNBT-UTBK</h4>
                                <p class="text-[9.5px] text-slate-400 mt-0.5">Simulasi tes skolastik, penalaran &amp; literasi</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 text-center">
                                <span class="text-lg">👮‍♂️</span>
                                <h4 class="text-xs font-extrabold text-white mt-1">Kedinasan &amp; Karir</h4>
                                <p class="text-[9.5px] text-slate-400 mt-0.5">Bimbingan tes fisik, psikotes &amp; peminatan kerja</p>
                            </div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/20 text-purple-200 text-[10px] flex items-center justify-between shrink-0">
                            <span>💬 Butuh konsultasi pribadi? Siswa dapat mengajukan sesi konsultasi tatap muka atau online melalui SIS-SMA1LE.</span>
                            <span class="font-bold text-white shrink-0 ml-2">GRATIS &amp; RAHASIA</span>
                        </div>
                    </div>
                </div>

                <!-- Right (5 Cols): Tim Guru BK / Konselor & Layanan Curhat Siswa -->
                <div class="lg:col-span-5 glass-panel rounded-2xl p-3 sm:p-3.5 xl:p-4 border border-white/5 flex flex-col h-full min-h-0 justify-between overflow-hidden gap-2">
                    <div class="flex items-center justify-between pb-2 border-b border-white/5 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-400 text-xs">👨‍🏫</span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-black text-white">Konselor / Guru BK Sekolah</h3>
                                <p class="text-[10px] text-slate-400">Siap mendengarkan &amp; mendampingi setiap siswa</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            Ruang BK S1L
                        </span>
                    </div>

                    <div class="flex-1 min-h-0 flex flex-col justify-between space-y-2 py-0.5 overflow-hidden">
                        <!-- Counselor Cards Stack -->
                        <div class="space-y-2">
                            @foreach($data['bk']['counselors'] ?? [] as $gc)
                                <div class="p-2 sm:p-2.5 xl:p-3 rounded-xl border transition flex items-center gap-3 {{ !empty($gc['is_coordinator']) ? 'bg-gradient-to-r from-purple-950/70 via-slate-900/95 to-purple-900/50 border-purple-500/40 shadow-md shadow-purple-950/40' : 'bg-slate-900/85 border-white/10 hover:border-purple-500/40' }}">
                                    <div class="w-12 sm:w-14 xl:w-16 h-14 sm:h-16 xl:h-20 rounded-xl overflow-hidden shrink-0 border-2 {{ !empty($gc['is_coordinator']) ? 'border-amber-400/70 ring-2 ring-purple-500/40' : 'border-purple-400/40' }} bg-slate-800 shadow-md flex items-center justify-center">
                                        @if(!empty($gc['photo']))
                                            <img src="{{ $gc['photo'] }}" alt="{{ $gc['name'] }}" class="w-full h-full object-cover" style="object-position: top center;">
                                        @else
                                            <span class="text-xl">{{ !empty($gc['is_coordinator']) ? '👑' : '🧑‍🏫' }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-0.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if(!empty($gc['is_coordinator']))
                                                <span class="px-1.5 py-0.5 rounded-md text-[8.5px] font-black uppercase tracking-wider bg-gradient-to-r from-amber-500/25 to-purple-500/25 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                                    <span>👑</span> KOORDINATOR BK
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded-md text-[8.5px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center gap-1">
                                                    <span>👨‍🏫</span> GURU BK
                                                </span>
                                            @endif
                                            @if(!empty($gc['status']))
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-white/10 text-slate-300 border border-white/5 font-mono">
                                                    {{ $gc['status'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-extrabold text-xs sm:text-[13px] xl:text-sm text-white leading-tight truncate" title="{{ $gc['name'] }}">
                                            {{ $gc['name'] }}
                                        </div>
                                        <div class="text-[10px] xl:text-[11px] font-medium text-purple-300 truncate">
                                            {{ $gc['role'] }}
                                        </div>
                                        <div class="text-[9px] xl:text-[9.5px] text-slate-400 font-mono truncate flex items-center gap-1">
                                            <span class="text-slate-500">NIP:</span>
                                            <span class="text-slate-300 font-semibold">{{ $gc['nip'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- 4 Pilar Layanan Konseling Unggulan (Fills space gracefully in Fullscreen) -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-900/90 border border-white/5 shrink-0">
                            <div class="flex items-start gap-2">
                                <span class="text-sm shrink-0">🤝</span>
                                <div>
                                    <div class="text-[10.5px] font-bold text-white">Konseling Pribadi</div>
                                    <div class="text-[8.5px] text-slate-400 leading-tight">Solusi masalah emosional &amp; pertemanan</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-sm shrink-0">🎓</span>
                                <div>
                                    <div class="text-[10.5px] font-bold text-white">Bimbingan Karir</div>
                                    <div class="text-[8.5px] text-slate-400 leading-tight">PTN, Kedinasan &amp; Minat Profesi</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-sm shrink-0">📖</span>
                                <div>
                                    <div class="text-[10.5px] font-bold text-white">Bimbingan Belajar</div>
                                    <div class="text-[8.5px] text-slate-400 leading-tight">Gaya belajar &amp; motivasi prestasi</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-sm shrink-0">👨‍👩‍👧</span>
                                <div>
                                    <div class="text-[10.5px] font-bold text-white">Konsultasi Wali</div>
                                    <div class="text-[8.5px] text-slate-400 leading-tight">Sinergi bersama orang tua siswa</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prinsip Layanan BK Bar (Clean, Slim & Non-overlapping) -->
                    <div class="py-1.5 px-3 rounded-lg bg-slate-950/80 border border-white/5 flex items-center justify-between text-[9px] text-slate-400 shrink-0">
                        <span class="flex items-center gap-1.5 text-purple-300 font-semibold truncate">
                            <span>🛡️</span> Asas Kerahasiaan BK Terjamin
                        </span>
                        <span class="text-slate-400 text-[8.5px] font-medium shrink-0">Konseling Aman, Ramah &amp; Bebas Biaya</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 5: PRESENSI KEHADIRAN SISWA TERPADU & JADWAL HARI INI -->
        <!-- ========================================================================= -->
        <section id="slide-5" class="kiosk-slide h-full w-full  flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-sm">06</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Presensi Kehadiran Harian &amp; Jadwal Sekolah</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Ringkasan kehadiran siswa real-time dan jadwal bel masuk-pulang hari ini</p>
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 font-mono hidden sm:flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                    <span>Sinkronisasi Otomatis 20s</span>
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
        <!-- SLIDE 6: PRESENSI IBADAH BERJAMAAH & KARAKTER 7 KAIH -->
        <!-- ========================================================================= -->
        <section id="slide-6" class="kiosk-slide hidden h-full w-full  flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-black text-sm">07</div>
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
        <!-- SLIDE 7: PRESENSI & PEMBINAAN EKSTRAKURIKULER SISWA -->
        <!-- ========================================================================= -->
        <section id="slide-7" class="kiosk-slide hidden h-full w-full flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center font-black text-sm">08</div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                            <span>Presensi &amp; Pembinaan Ekstrakurikuler Siswa</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Pengembangan minat, bakat, kepemimpinan, dan pembinaan karakter siswa SMAN 1 Lengkong</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Sub-page Navigation for Ekskul (1/2 Layar Slide) -->
                    <div class="flex items-center gap-1 bg-purple-950/60 border border-purple-500/30 rounded-xl p-0.5 text-xs">
                        <button type="button" onclick="prevExtraPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Ekskul Sebelumnya">
                            &larr;
                        </button>
                        <span id="extraPageIndicator" class="px-2 font-mono text-[11px] font-bold text-purple-300">
                            Hal 1 / {{ max(1, count(array_chunk(collect($data['extracurricular']['clubs'] ?? [])->all(), 4))) }}
                        </span>
                        <button type="button" onclick="nextExtraPage()" class="p-1 px-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition" title="Halaman Ekskul Berikutnya">
                            &rarr;
                        </button>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center gap-1">
                        <span>🎯</span> {{ $data['extracurricular']['total_clubs'] ?? count($data['extracurricular']['clubs'] ?? []) }} CABANG EKSKUL
                    </span>
                </div>
            </div>

            <!-- Content Area: Left 7 Cols (1/2 Layar Sistem Slide Cabang & Pembina Foto), Right 5 Cols (Presensi Hari Ini & Pilar Karakter) -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4 overflow-hidden">
                <!-- Left Column (7 Cols): 1/2 Layar Sistem Slide Ekskul & Foto Pembina Guru & PTK -->
                <div class="lg:col-span-7 flex flex-col h-full min-h-0 gap-3 justify-between overflow-hidden">
                    <!-- Ribbon: Jadwal Latihan & Keterangan -->
                    <div class="p-2 sm:p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/20 flex flex-wrap items-center justify-between gap-2 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🏆</span>
                            <div>
                                <div class="text-xs sm:text-sm font-bold text-white">Cabang Peminatan Minat &amp; Bakat Aktif</div>
                                <div class="text-[10px] text-purple-300">Wadah aktualisasi diri, sportivitas, kreativitas, dan kepanduan</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] text-slate-400">Jadwal Latihan Mingguan</div>
                            <div class="text-xs font-mono font-bold text-white">{{ $data['extracurricular']['schedule'] ?? 'Senin - Sabtu' }}</div>
                        </div>
                    </div>

                    <!-- Paginated Sliding Grid: 4 Clubs Per Screen (2x2) with Teacher Photo -->
                    <div class="flex-1 min-h-0 relative overflow-hidden">
                        @php
                            $allClubs = collect($data['extracurricular']['clubs'] ?? [])->all();
                            $chunkedClubs = array_chunk($allClubs, 4);
                        @endphp

                        @forelse($chunkedClubs as $pIdx => $clubPage)
                            <div class="extra-page h-full w-full {{ $pIdx === 0 ? 'grid' : 'hidden' }} grid-cols-1 sm:grid-cols-2 grid-rows-2 gap-2.5 xl:gap-3.5 overflow-hidden" id="extra-page-{{ $pIdx }}">
                                @foreach($clubPage as $club)
                                    <div class="p-2.5 sm:p-3 xl:p-3.5 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-purple-500/40 transition flex flex-col justify-between group shadow-md h-full">
                                        <!-- Club Name & Category Badge -->
                                        <div>
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center text-lg font-bold shrink-0 border border-purple-500/30">
                                                        {{ $club['icon'] ?? '🎯' }}
                                                    </div>
                                                    <div>
                                                        <h4 class="text-xs sm:text-sm font-black text-white group-hover:text-purple-300 transition truncate">{{ $club['name'] }}</h4>
                                                        <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-300 border border-purple-500/20 inline-block mt-0.5">
                                                            {{ $club['category'] ?? 'Ekstrakurikuler' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-2 text-[10px] text-slate-400">
                                                <span class="font-mono text-purple-300">{{ $club['schedule'] ?? 'Sore Hari' }}</span> &bull; <span>{{ $club['location'] ?? 'Kampus S1L' }}</span>
                                            </div>
                                        </div>

                                        <!-- Pembina Box with Photo from Master Data - Data Guru & PTK -->
                                        <div class="mt-2 pt-2 border-t border-white/10 flex items-center gap-2.5 bg-slate-950/70 p-2 rounded-xl">
                                            <div class="w-11 sm:w-12 xl:w-14 h-14 sm:h-16 xl:h-18 rounded-lg overflow-hidden p-0.5 bg-gradient-to-tr from-purple-400 to-indigo-500 shadow-md shrink-0">
                                                <div class="w-full h-full bg-slate-900 rounded-[6px] overflow-hidden flex items-center justify-center">
                                                    @if(!empty($club['pembina']['photo']))
                                                        <img src="{{ $club['pembina']['photo'] }}" alt="{{ $club['pembina']['name'] }}" class="w-full h-full object-cover" style="object-position: top center;">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-900 text-purple-300 font-extrabold text-xs">
                                                            {{ strtoupper(substr($club['pembina']['name'] ?? 'PB', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-[9px] font-bold text-purple-400 uppercase tracking-wider">Pembina Ekskul</div>
                                                <div class="text-xs font-extrabold text-white truncate group-hover:text-purple-300 transition" title="{{ $club['pembina']['name'] ?? $club['teacher'] ?? 'Dewan Guru' }}">
                                                    {{ $club['pembina']['name'] ?? $club['teacher'] ?? 'Dewan Guru' }}
                                                </div>
                                                <div class="text-[9.5px] font-mono text-slate-400 truncate">
                                                    {{ !empty($club['pembina']['nip']) && $club['pembina']['nip'] !== '-' ? 'NIP ' . $club['pembina']['nip'] : 'Guru Pembina Terverifikasi' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-slate-500 text-xs italic py-6">
                                Belum ada data cabang ekstrakurikuler terdaftar.
                            </div>
                        @endforelse
                    </div>

                    <!-- Bottom Bar -->
                    <div class="p-2 rounded-xl bg-slate-950/80 border border-white/5 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                        <span>Pramuka (Wajib), Paskibra, PMR Wira, Rohis, Seni Musik &amp; Tari, Futsal, Basket, Voli, Silat, KIR, English Club</span>
                        <span class="text-purple-300 font-bold font-mono">Ekosistem S1L</span>
                    </div>
                </div>

                <!-- Right Column (5 Cols): Log Presensi Ekskul Hari Ini & 4 Pilar Karakter -->
                <div class="lg:col-span-5 flex flex-col h-full min-h-0 gap-3 justify-between overflow-hidden">
                    <!-- Presensi Ekstrakurikuler Card -->
                    <div class="flex-1 min-h-0 glass-panel rounded-2xl border border-white/10 p-3 sm:p-4 flex flex-col justify-between overflow-hidden shadow-xl">
                        <div class="flex items-center justify-between pb-2 border-b border-white/10 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="p-1 rounded-lg bg-purple-500/20 text-purple-300 text-xs">📝</span>
                                <h3 class="text-xs sm:text-sm font-black text-white">Log Presensi Ekskul Hari Ini</h3>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span id="extraTotalToday" class="text-base font-black text-purple-300 font-mono">{{ $data['extracurricular']['total_today'] }}</span>
                                <span class="text-[10px] text-slate-400">Siswa Hadir</span>
                            </div>
                        </div>

                        <div id="extraRecentLogs" class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 py-2 pr-1">
                            @forelse($data['extracurricular']['recent'] ?? [] as $ex)
                                <div class="p-2.5 rounded-xl bg-slate-900/70 border border-white/5 flex items-center justify-between text-xs">
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
                                <div class="h-full flex flex-col items-center justify-center text-center py-6 text-slate-500 text-xs space-y-1">
                                    <span class="text-2xl">🎯</span>
                                    <span class="font-semibold text-slate-400">Belum ada presensi ekstrakurikuler tercatat hari ini.</span>
                                    <span class="text-[10px] text-slate-500">Presensi dibuka saat jadwal latihan ekskul berlangsung.</span>
                                </div>
                            @endforelse
                        </div>

                        <div class="pt-2 border-t border-white/5 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                            <span>Presensi berbasis RFID / Barcode Mandiri</span>
                            <span class="text-purple-300 font-mono font-bold">Terverifikasi</span>
                        </div>
                    </div>

                    <!-- 4 Pilar Karakter Ekstrakurikuler -->
                    <div class="glass-panel rounded-2xl border border-white/10 p-3 shrink-0 shadow-lg">
                        <div class="text-[10px] font-black uppercase text-purple-300 tracking-wider mb-2 flex items-center gap-1.5">
                            <span>🛡️</span> 4 PILAR PEMBINAAN KARAKTER EKSKUL
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                <div class="font-bold text-white flex items-center gap-1">
                                    <span>🧭</span> Kepemimpinan
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-0.5 leading-snug">Menempa jiwa mandiri, tangguh, dan berintegritas.</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                <div class="font-bold text-white flex items-center gap-1">
                                    <span>⏱️</span> Kedisiplinan
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-0.5 leading-snug">Menghargai waktu, aturan, dan komitmen tim.</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                <div class="font-bold text-white flex items-center gap-1">
                                    <span>🎨</span> Kreativitas
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-0.5 leading-snug">Eksplorasi bakat seni, riset, dan olahraga.</div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-900/70 border border-white/5">
                                <div class="font-bold text-white flex items-center gap-1">
                                    <span>🤝</span> Solidaritas
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-0.5 leading-snug">Kebersamaan, gotong royong, dan empati sosial.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- SLIDE 8: BUKU KONTROL DIGITAL TAHFIDZ AL-QUR'AN JUZ 30 -->
        <!-- ========================================================================= -->
        <section id="slide-8" class="kiosk-slide hidden h-full w-full  flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-black text-sm">09</div>
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
                    @foreach(['X' => ['target' => 'Al-Balad s.d. An-Nas (25 Surat)', 'color' => 'teal'], 'XI' => ['target' => 'Al-Mutaffifin s.d. Al-Fajr (7 Surat)', 'color' => 'indigo'], 'XII' => ['target' => 'An-Naba\' s.d. Al-Infitar (5 Surat)', 'color' => 'emerald']] as $grd => $gcfg)
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
        <!-- SLIDE 9: HALL OF FAME — TOP 10 RANKING POIN PRESTASI POSITIF SISWA -->
        <!-- ========================================================================= -->
        <section id="slide-9" class="kiosk-slide hidden h-full w-full  flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-yellow-500/20 text-yellow-300 flex items-center justify-center font-black text-sm">10</div>
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
        <!-- SLIDE 10: KETERTIBAN SEKOLAH, PETUGAS PIKET & MONITORING POS SATPAM -->
        <!-- ========================================================================= -->
        <section id="slide-10" class="kiosk-slide hidden h-full w-full  flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center font-black text-sm">11</div>
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
        <!-- SLIDE 11: BERITA & WARTA SEKOLAH RESMI -->
        <!-- ========================================================================= -->
        <section id="slide-11" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-sm">12</div>
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
                        <div class="relative w-full h-48 sm:h-56 md:h-64 lg:h-72 xl:h-80 2xl:h-96 rounded-xl overflow-hidden shrink-0 border border-white/10 bg-slate-900 shadow-lg">
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
                                <h3 class="text-base sm:text-lg xl:text-xl font-black text-white leading-snug line-clamp-2 hover:text-teal-300 transition">
                                    {{ $featuredNews['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed line-clamp-3 sm:line-clamp-4 xl:line-clamp-6 font-normal">
                                    {{ $featuredNews['excerpt'] ?? Str::limit(strip_tags($featuredNews['content'] ?? ''), 360) }}
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
        <!-- SLIDE 12: AGENDA & KALENDER KEGIATAN AKADEMIK RESMI -->
        <!-- ========================================================================= -->
        <section id="slide-12" class="kiosk-slide h-full w-full hidden flex-col justify-between overflow-hidden gap-3">
            <!-- Slide Title Header -->
            <div class="flex items-center justify-between shrink-0 pb-1 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-black text-sm">13</div>
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
        <!-- SLIDE 13: BUKU TAMU KEDINASAN & KUNJUNGAN SEKOLAH (SIKATAS) -->
        <!-- ========================================================================= -->
        <section id="slide-13" class="hidden flex-col h-full min-h-0 overflow-hidden">
            <!-- Header Slide -->
            <div class="flex items-center justify-between pb-2 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-400 font-mono font-bold text-xs">14</span>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                            <span>Buku Tamu Kedinasan &amp; Kunjungan Sekolah</span>
                            <span class="text-slate-500">&bull;</span>
                            <span class="text-xs font-semibold text-amber-300">SIKATAS Resepsionis</span>
                        </h2>
                        <p class="text-[10px] text-slate-400">Monitoring kehadiran tamu kedinasan, dinas pendidikan, wali murid, dan mitra institusi resmi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        👥 Meja Pelayanan Tamu
                    </span>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-amber-600/30 hover:bg-amber-600 text-amber-200 text-[11px] font-bold transition flex items-center gap-1 border border-amber-500/30">
                        <span>Buka Kios Tamu &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3.5 overflow-hidden pt-1">
                <!-- Left (7 Cols): KPI Cards + SOP Pelayanan Tamu -->
                <div class="lg:col-span-7 flex flex-col h-full min-h-0 gap-3">
                    <!-- KPI Cards (3 Cards) -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 shrink-0">
                        <div class="glass-panel p-3 rounded-2xl border border-white/5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg shrink-0 font-bold">
                                👥
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">Tamu Hari Ini</span>
                                <span class="text-lg font-black text-white font-mono leading-none">{{ $data['guests']['total_today'] ?? 0 }}</span>
                                <span class="text-[9.5px] text-amber-300 block">Kunjungan</span>
                            </div>
                        </div>

                        <div class="glass-panel p-3 rounded-2xl border border-white/5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg shrink-0 font-bold">
                                📅
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">Total Bulan Ini</span>
                                <span class="text-lg font-black text-white font-mono leading-none">{{ $data['guests']['total_month'] ?? 0 }}</span>
                                <span class="text-[9.5px] text-teal-300 block">Kunjungan Tamu</span>
                            </div>
                        </div>

                        <div class="glass-panel p-3 rounded-2xl border border-white/5 flex items-center gap-3 col-span-2 sm:col-span-1">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg shrink-0 font-bold">
                                🏛️
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">Pelayanan</span>
                                <span class="text-sm font-black text-white leading-none">Terintegrasi</span>
                                <span class="text-[9.5px] text-blue-300 block">SOP Gerbang &amp; TU</span>
                            </div>
                        </div>
                    </div>

                    <!-- SOP Alur Penerimaan Tamu Sekolah -->
                    <div class="flex-1 min-h-0 glass-panel rounded-2xl p-3 sm:p-3.5 border border-white/5 flex flex-col justify-between overflow-hidden">
                        <div class="flex items-center justify-between pb-2 border-b border-white/5 shrink-0">
                            <h3 class="text-xs sm:text-sm font-black text-white flex items-center gap-2">
                                <span>📋</span>
                                <span>Alur &amp; Prosedur Operasional Standar (SOP) Tamu Sekolah</span>
                            </h3>
                            <span class="text-[10px] text-slate-400">Tertib &bull; Aman &bull; Nyaman</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 py-2">
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 space-y-1">
                                <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-xs flex items-center justify-center">1</span>
                                <h4 class="text-xs font-bold text-white">Lapor Gerbang</h4>
                                <p class="text-[9.5px] text-slate-400 leading-snug">Melapor ke pos satpam dan menukarkan identitas dengan kartu tamu.</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 space-y-1">
                                <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-xs flex items-center justify-center">2</span>
                                <h4 class="text-xs font-bold text-white">Presensi Kios</h4>
                                <p class="text-[9.5px] text-slate-400 leading-snug">Mengisi presensi mandiri di layar Kios Buku Tamu SIKATAS.</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 space-y-1">
                                <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-xs flex items-center justify-center">3</span>
                                <h4 class="text-xs font-bold text-white">Konfirmasi TU</h4>
                                <p class="text-[9.5px] text-slate-400 leading-snug">Petugas menghubungkan tamu dengan pihak / pimpinan yang dituju.</p>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 space-y-1">
                                <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-xs flex items-center justify-center">4</span>
                                <h4 class="text-xs font-bold text-white">Selesai / Pulang</h4>
                                <p class="text-[9.5px] text-slate-400 leading-snug">Menyerahkan kembali kartu tanda pengenal tamu ke pos keamanan.</p>
                            </div>
                        </div>

                        <div class="p-2 rounded-xl bg-slate-950/80 border border-white/5 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                            <span>🕒 Jam Pelayanan Tamu Kedinasan: <strong>Senin - Jum'at (07:30 - 15:30 WIB)</strong></span>
                            <span class="text-amber-300 font-bold">Resepsionis S1L</span>
                        </div>
                    </div>
                </div>

                <!-- Right (5 Cols): Daftar Log Kunjungan Tamu Terkini -->
                <div class="lg:col-span-5 glass-panel rounded-2xl p-3 sm:p-3.5 border border-white/5 flex flex-col h-full min-h-0 overflow-hidden">
                    <div class="flex items-center justify-between pb-2 border-b border-white/5 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-amber-500/20 text-amber-400 text-xs">👥</span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-black text-white">Daftar Kunjungan Tamu Terkini</h3>
                                <p class="text-[10px] text-slate-400">Log kehadiran tamu resepsionis sekolah</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Resmi &amp; Tercatat
                        </span>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2 pt-2.5 pr-1">
                        @forelse($data['guests']['recent'] ?? [] as $gst)
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-white/5 space-y-1.5 hover:border-amber-500/30 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-extrabold text-xs text-white truncate">{{ $gst['name'] }}</div>
                                        <div class="text-[10px] text-amber-300 truncate font-semibold">{{ $gst['institution'] }}</div>
                                    </div>
                                    <span class="font-mono text-[10px] font-bold text-slate-300 bg-slate-950 px-2 py-0.5 rounded border border-white/5 shrink-0">
                                        {{ $gst['time'] }} WIB
                                    </span>
                                </div>
                                <div class="pt-1.5 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-400">
                                    <span class="truncate">Bertemu: <strong class="text-slate-200">{{ $gst['destination'] }}</strong></span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-800 text-slate-300 shrink-0">{{ $gst['status'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center py-8 text-slate-500 text-xs space-y-1">
                                <span class="text-2xl">👥</span>
                                <span class="font-semibold text-slate-400">Belum ada catatan tamu kedinasan baru.</span>
                                <span class="text-[10px] text-slate-500">Tamu yang mengisi formulir resepsionis akan langsung tercatat di sini.</span>
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 mt-2 border-t border-white/5 text-[10px] text-slate-500 text-center shrink-0">
                        Sistem Informasi Kunjungan &amp; Buku Tamu Sekolah (SIKATAS) &bull; SIS-SMA1LE
                    </div>
                </div>
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
        const TOTAL_SLIDES = 14;
        const SLIDE_DURATION = 18; // 18 seconds per slide
        let currentSlideIdx = 0;
        let slideRemainingTime = SLIDE_DURATION;
        let isPaused = false;
        let slideTimerInterval = null;

                // Theme Controller (Dark / Light Theme)
        function initKioskTheme() {
            const saved = localStorage.getItem('kiosk_theme') || 'dark';
            applyKioskTheme(saved);
        }

        function applyKioskTheme(theme) {
            const icon = document.getElementById('themeToggleIcon');
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                if (icon) icon.textContent = '☀️';
                localStorage.setItem('kiosk_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                if (icon) icon.textContent = '🌙';
                localStorage.setItem('kiosk_theme', 'dark');
            }
        }

        function toggleKioskTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            applyKioskTheme(isDark ? 'light' : 'dark');
        }

        // Sub-page Controller for Direktori Guru & Staf
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
            const activeSlideEl = document.getElementById(`slide-${currentSlideIdx}`);
            const hasStaffPages = activeSlideEl && activeSlideEl.querySelector('.staff-page');
            if (hasStaffPages && totalStaffPages > 1) {
                staffCycleInterval = setInterval(() => {
                    if (!isPaused) {
                        nextStaffPage();
                    }
                }, 3600); // cycle sub-page briskly every 3.6s while on staff slide
            }
        }

        // Sub-page Controller for Ekstrakurikuler (1/2 Layar Slide)
        let currentExtraPageIdx = 0;
        const totalExtraPages = {{ max(1, count(array_chunk(collect($data['extracurricular']['clubs'] ?? [])->all(), 4))) }};
        let extraCycleInterval = null;

        function showExtraPage(page) {
            if (totalExtraPages <= 1) return;
            currentExtraPageIdx = (page + totalExtraPages) % totalExtraPages;
            for (let i = 0; i < totalExtraPages; i++) {
                const pageEl = document.getElementById(`extra-page-${i}`);
                if (pageEl) {
                    if (i === currentExtraPageIdx) {
                        pageEl.classList.remove('hidden');
                        pageEl.classList.add('grid');
                    } else {
                        pageEl.classList.add('hidden');
                        pageEl.classList.remove('grid');
                    }
                }
            }
            const indEl = document.getElementById('extraPageIndicator');
            if (indEl) {
                indEl.textContent = `Hal ${currentExtraPageIdx + 1} / ${totalExtraPages}`;
            }
        }

        function nextExtraPage() {
            showExtraPage(currentExtraPageIdx + 1);
        }

        function prevExtraPage() {
            showExtraPage(currentExtraPageIdx - 1);
        }

        function checkExtraSlideAutoCycle() {
            if (extraCycleInterval) {
                clearInterval(extraCycleInterval);
                extraCycleInterval = null;
            }
            const activeSlideEl = document.getElementById(`slide-${currentSlideIdx}`);
            const hasExtraPages = activeSlideEl && activeSlideEl.querySelector('.extra-page');
            if (hasExtraPages && totalExtraPages > 1) {
                extraCycleInterval = setInterval(() => {
                    if (!isPaused) {
                        nextExtraPage();
                    }
                }, 4500); // cycle ekskul page every 4.5s while on ekskul slide
            }
        }

        // Sub-page Controller for Rombel (1-4 Layar Slide)
        let currentRombelPageIdx = 0;
        const totalRombelPages = {{ max(1, count(array_chunk(collect($data['rombels']['list'] ?? [])->all(), 6))) }};
        let rombelCycleInterval = null;

        function showRombelPage(page) {
            if (totalRombelPages <= 1) return;
            currentRombelPageIdx = (page + totalRombelPages) % totalRombelPages;
            for (let i = 0; i < totalRombelPages; i++) {
                const pageEl = document.getElementById(`rombel-page-${i}`);
                if (pageEl) {
                    if (i === currentRombelPageIdx) {
                        pageEl.classList.remove('hidden');
                        pageEl.classList.add('grid');
                    } else {
                        pageEl.classList.add('hidden');
                        pageEl.classList.remove('grid');
                    }
                }
            }
            const indEl = document.getElementById('rombelPageIndicator');
            if (indEl) {
                indEl.textContent = `Hal ${currentRombelPageIdx + 1} / ${totalRombelPages}`;
            }
        }

        function nextRombelPage() {
            showRombelPage(currentRombelPageIdx + 1);
        }

        function prevRombelPage() {
            showRombelPage(currentRombelPageIdx - 1);
        }

        function checkRombelSlideAutoCycle() {
            if (rombelCycleInterval) {
                clearInterval(rombelCycleInterval);
                rombelCycleInterval = null;
            }
            const activeSlideEl = document.getElementById(`slide-${currentSlideIdx}`);
            const hasRombelPages = activeSlideEl && activeSlideEl.querySelector('.rombel-page');
            if (hasRombelPages && totalRombelPages > 1) {
                rombelCycleInterval = setInterval(() => {
                    if (!isPaused) {
                        nextRombelPage();
                    }
                }, 4500); // cycle rombel page every 4.5s while on rombel slide
            }
        }

        // Initialize Slide Engine
        function initSlideDeck() {
            initKioskTheme();
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
                        pillEl.className = 'slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold transition flex items-center gap-1 bg-teal-500 text-slate-950 shadow-sm whitespace-nowrap text-[10.5px] lg:text-[11px]';
                        if (typeof pillEl.scrollIntoView === 'function') {
                            pillEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                        }
                    } else {
                        pillEl.className = 'slide-nav-pill px-1.5 lg:px-2 py-0.5 sm:py-1 rounded-lg font-bold text-slate-400 hover:text-white transition flex items-center gap-1 whitespace-nowrap text-[10.5px] lg:text-[11px]';
                    }
                }
            }

            // Check sub-page auto cycles
            checkStaffSlideAutoCycle();
            checkExtraSlideAutoCycle();
            checkRombelSlideAutoCycle();

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

                // 8. Update Perpustakaan
                if (d.library) {
                    const libTot = document.getElementById('libKioskVisitorsToday');
                    if (libTot) libTot.textContent = d.library.total_today;
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

        // Auto Poll every 20s
        setInterval(fetchKioskData, 20000);

        // Run on load
        document.addEventListener('DOMContentLoaded', initSlideDeck);
    </script>
</body>
</html>
