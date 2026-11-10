<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth overflow-x-clip bg-indigo-700 dark:bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4338ca" id="meta-theme-color">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-navbutton-color" content="#4338ca">
    <title>{{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }} - Unggul, Berkarakter & Berprestasi di Era Digital</title>
    <meta name="description" content="Website Resmi SMAN 1 Lengkong Kabupaten Sukabumi. Portal akademik SIAKAD terpadu, LMS Pembelajaran Permendikdasmen No. 13 Tahun 2025, Presensi 5 Metode, dan Portal Orang Tua.">
    <meta name="keywords" content="SMAN 1 Lengkong, SMA Negeri 1 Lengkong, Sukabumi, Sekolah Digital, SIAKAD SMAN 1 Lengkong, LMS SMAN 1 Lengkong, Portal Siswa, Kurikulum Nasional 2025">
    
    <!-- Open Graph / Meta Sosial (Rich Preview WhatsApp, Facebook, Telegram, Twitter, LinkedIn) -->
    <meta property="og:site_name" content="{{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}">
    <meta property="og:title" content="{{ $metaTitle ?? (($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' - Sekolah Unggul Berwawasan Digital') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Official Web Portal & SIAKAD Terpadu SMAN 1 Lengkong Sukabumi. Sistem Pembelajaran Modern & Karakter Unggul.' }}">
    <meta property="og:type" content="{{ $metaType ?? 'website' }}">
    <meta property="og:url" content="{{ $metaUrl ?? url('/') }}">
    <meta property="og:image" content="{{ $metaImage ?? asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}">
    <meta property="og:image:url" content="{{ $metaImage ?? asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}">
    <meta property="og:image:secure_url" content="{{ $metaImage ?? asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}">
    <meta property="og:image:type" content="{{ $metaImageMime ?? 'image/jpeg' }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $metaTitle ?? ($settings['school_name'] ?? 'SMAN 1 Lengkong') }}">

    <!-- Link image_src for WhatsApp & legacy crawlers -->
    <link rel="image_src" href="{{ $metaImage ?? asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle ?? (($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' - Sekolah Unggul Berwawasan Digital') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Official Web Portal & SIAKAD Terpadu SMAN 1 Lengkong Sukabumi.' }}">
    <meta name="twitter:image" content="{{ $metaImage ?? asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}">

    <!-- Google Fonts & Tailwind CSS v4 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif

    <!-- Anti-flash Dark Mode Script -->
    <script>
        function updateMetaThemeColor(isDark) {
            const meta = document.getElementById('meta-theme-color');
            if (meta) {
                meta.setAttribute('content', isDark ? '#0f172a' : '#4338ca');
            }
        }
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            updateMetaThemeColor(true);
        } else {
            document.documentElement.classList.remove('dark');
            updateMetaThemeColor(false);
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .dark .glass-nav {
            background: rgba(10, 15, 30, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .news-rich-article img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin: 1rem auto;
            display: block;
        }
        .news-rich-article table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
        }
        .news-rich-article th {
            background-color: #f1f5f9;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
        }
        .news-rich-article td {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
        }
        .news-rich-article blockquote {
            border-left: 4px solid #6366f1;
            padding-left: 1rem;
            font-style: italic;
            color: #64748b;
            margin: 1rem 0;
        }
        .news-rich-article h2 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        .news-rich-article h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.4rem;
            color: #1e293b;
        }
        .dark .news-rich-article th {
            background-color: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .dark .news-rich-article td {
            border-color: #334155;
            color: #cbd5e1;
        }
        .dark .news-rich-article h2, .dark .news-rich-article h3 {
            color: #ffffff;
        }

        .text-glow-indigo {
            text-shadow: 0 0 30px rgba(99, 102, 241, 0.6);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .bottom-dock-transition {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        }
        .bottom-dock-hidden {
            transform: translateY(120%);
            opacity: 0;
            pointer-events: none;
        }

        /* Header Running Text Marquee Animation */
        @keyframes headerRunningMarquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .header-running-track {
            display: flex;
            width: max-content;
            animation: headerRunningMarquee var(--header-marquee-duration, 32s) linear infinite;
            will-change: transform;
        }
        .header-running-track:hover {
            animation-play-state: paused !important;
        }
        html {
            scroll-padding-top: 135px;
            overflow-x: clip;
        }
        body {
            overflow-x: clip;
        }
        #sticky-header-wrapper {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 50;
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-600 selection:text-white transition-colors duration-300 overflow-x-clip">

    <!-- ==================== 1 & 2. STICKY TOP HEADER & NAVIGATION WRAPPER ==================== -->
    <div id="sticky-header-wrapper" class="sticky top-0 z-50 w-full transition-all duration-300 shadow-md bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-700 dark:from-slate-950 dark:via-indigo-950 dark:to-slate-950">
        <!-- 1. TOP ANNOUNCEMENT TICKER -->
        <aside aria-label="Pengumuman Resmi Sekolah" class="bg-gradient-to-r from-indigo-700/95 via-indigo-600/95 to-blue-700/95 dark:from-indigo-950/95 dark:via-slate-900/95 dark:to-indigo-950/95 backdrop-blur-md text-white py-1.5 sm:py-2 px-3 sm:px-4 shadow-xs border-b border-indigo-500/30 dark:border-indigo-900/50 pt-[env(safe-area-inset-top,0px)]">
            <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[9px] sm:text-[10px] font-black uppercase tracking-wider shrink-0 shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-950 animate-ping"></span>
                        Resmi
                    </span>
                    <!-- Mobile View: Ringkas & rapi tanpa truncate ellipsis '...' -->
                    <p class="sm:hidden text-indigo-100 text-[10.5px] font-semibold whitespace-nowrap">
                        Terakreditasi A • NPSN: {{ $settings['school_npsn'] ?? '20202265' }}
                    </p>
                    <!-- Tablet & Desktop View: Lengkap -->
                    <p class="hidden sm:block truncate text-indigo-100 text-xs font-medium">
                        Portal Resmi <span class="font-bold text-white">{{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}</span> • Terakreditasi A • NPSN: {{ $settings['school_npsn'] ?? '20202265' }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Mobile Quick SPMB Pill -->
                    <a href="{{ route('spmb.public.register') }}" class="sm:hidden px-2.5 py-0.5 rounded-full bg-gradient-to-r from-emerald-400 to-teal-300 text-slate-950 font-black text-[10px] shadow-xs flex items-center gap-1">
                        <span>🎓 SPMB</span>
                    </a>
                    <div class="hidden md:flex items-center gap-3 text-[11px] text-indigo-100 shrink-0">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Sistem Akademik Online
                        </span>
                        <span class="text-indigo-400">|</span>
                        <a href="{{ route('kiosk.index') }}" target="_blank" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>📺 Kios TV</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="{{ route('guests.kiosk') }}" target="_blank" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>👥 Kios Tamu</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="{{ route('library.guest_book.index') }}" target="_blank" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>📚 Kios Perpus</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="#ekosistem-sis" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>💻 Ekosistem SIS</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="#layanan-peran" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>⚡ Pusat Layanan</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="{{ route('parent.portal') }}" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold text-white">
                            <span>👨‍👩‍👧‍👦 Portal Orang Tua</span>
                        </a>
                        <span class="text-indigo-400">|</span>
                        <a href="{{ route('spmb.public.register') }}" class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-[11px] shadow-sm transition flex items-center gap-1">
                            <span>🎓 Daftar SPMB</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 2. FLOATING HEADER / NAVBAR -->
        <header id="main-header" class="transition-all duration-300 glass-nav border-b border-slate-200/70 dark:border-slate-800/80 shadow-xs">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2 sm:gap-3">
            
            <!-- School Brand Identity -->
            <a href="#hero" class="flex items-center gap-2 sm:gap-3 group shrink-0 min-w-0">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-indigo-600 via-blue-600 to-emerald-400 p-0.5 shadow-md shadow-indigo-600/25 group-hover:scale-105 transition-transform duration-300 shrink-0">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[10px] sm:rounded-[14px] flex items-center justify-center overflow-hidden">
                        @if(!empty($appLogoUrl))
                            <img src="{{ $appLogoUrl }}" alt="Logo {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}" class="w-full h-full object-contain p-0.5 sm:p-1">
                        @else
                            <span class="font-heading font-black text-indigo-600 dark:text-indigo-400 text-xs sm:text-lg">1LE</span>
                        @endif
                    </div>
                </div>
                <div class="min-w-0">
                    <h1 class="font-heading font-black text-slate-900 dark:text-white text-xs sm:text-base lg:text-lg tracking-tight leading-none group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                        {{ $settings['school_name'] ?? 'SMAN 1 LENGKONG' }}
                    </h1>
                    <p class="text-[9px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 flex items-center gap-1 sm:gap-1.5 truncate">
                        <span class="inline-block w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        Kab. Sukabumi • Jawa Barat
                    </p>
                </div>
            </a>

            <!-- Header Running Text -->
            @php
                $runningText = $settings['website_running_text'] ?? ('Selamat Datang di Official Portal ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' Sukabumi • Pusat Ekosistem Pendidikan Digital, Presensi Terpadu & Layanan Ramah Siswa • Unggul dalam Mutu, Berkarakter, dan Berdaya Saing Global.');
                $runningBadge = $settings['website_running_text_badge'] ?? 'INFO SEKOLAH';
                $runningSpeed = $settings['website_running_text_speed'] ?? 'normal';
                $speedDuration = match($runningSpeed) {
                    'slow' => '48s',
                    'fast' => '22s',
                    default => '32s',
                };
            @endphp
            <div class="hidden lg:flex flex-1 items-center mx-3 xl:mx-6 min-w-0 max-w-2xl xl:max-w-4xl py-1.5 px-3 rounded-2xl bg-slate-100/90 dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-inner group overflow-hidden" 
                 title="Arahkan kursor mouse untuk berhenti dan membaca informasi">
                
                <div class="shrink-0 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 text-white font-black text-[10px] xl:text-[11px] shadow-sm uppercase tracking-wider mr-2.5 select-none">
                    <span class="inline-block w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span>{{ $runningBadge }}</span>
                </div>

                <div class="relative flex-1 overflow-hidden h-7 flex items-center">
                    <div class="header-running-track flex items-center whitespace-nowrap text-xs xl:text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:[animation-play-state:paused] cursor-default select-none"
                         style="--header-marquee-duration: {{ $speedDuration }};">
                        <span class="inline-block px-4">{{ $runningText }}</span>
                        <span class="inline-block px-4 text-emerald-500 font-black">•</span>
                        <span class="inline-block px-4">{{ $runningText }}</span>
                        <span class="inline-block px-4 text-emerald-500 font-black">•</span>
                    </div>
                </div>
            </div>

            <!-- Actions: Dark Mode & Gateways -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <button type="button" id="theme-toggle" class="p-1.5 sm:p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/70 border border-slate-200/60 dark:border-slate-800 transition" aria-label="Ganti Tema">
                    <svg id="theme-toggle-light-icon" class="hidden w-4 h-4 sm:w-5 sm:h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4 sm:w-5 sm:h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                <!-- Kiosk Ecosystem Quick Links -->
                <div class="hidden xl:flex items-center gap-1.5">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800/80 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition" title="Layar Display TV Lobby Sekolah">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Display TV</span>
                    </a>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800/80 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition" title="Kios Buku Tamu Resepsionis Mandiri">
                        <span>👥 Kios Tamu</span>
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800/80 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition" title="Kios Buku Tamu Perpustakaan">
                        <span>📚 Kios Perpus</span>
                    </a>
                </div>

                <!-- SPMB Registration CTA Link -->
                <a href="{{ route('spmb.public.register') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs sm:text-sm shadow-md shadow-emerald-600/25 transition-all hover:scale-[1.02] border border-emerald-400/30">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>🎓 Daftar SPMB</span>
                </a>

                <!-- Primary Portal Auth Button -->
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-1.5 sm:py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/30 transition-all hover:scale-[1.02]">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="hidden xs:inline truncate max-w-[100px] sm:max-w-none">Dashboard</span>
                        <span class="xs:hidden">Masuk</span>
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-1.5 sm:py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/30 transition-all hover:scale-[1.02]">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Masuk Portal SIS</span>
                        <span class="sm:hidden">Portal SIS</span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button type="button" id="mobile-menu-btn" class="lg:hidden p-1.5 sm:p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Buka Menu">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>


        <!-- Mobile Drawer Navigation (12 Ordered Menu Items) -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 dark:border-slate-800 px-4 pt-3 pb-6 space-y-2 bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl max-h-[80vh] overflow-y-auto">
            <nav class="flex flex-col space-y-1 font-medium text-xs sm:text-sm text-slate-700 dark:text-slate-200">
                <a href="{{ route('spmb.public.register') }}" class="mobile-nav-link px-3 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-black transition flex items-center justify-between shadow-sm">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span>🎓 Pendaftaran SPMB Siswa Baru</span>
                    </span>
                    <span class="text-[10px] bg-white/20 text-white px-2 py-0.5 rounded-md font-bold">Buka</span>
                </a>
                <a href="#sambutan" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>💬 Sambutan Kepala Sekolah</span>
                    <span class="text-[10px] text-slate-400">Amanat</span>
                </a>
                <a href="#profil" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>🏛️ Profil Lembaga</span>
                    <span class="text-[10px] text-slate-400">Visi & Misi</span>
                </a>
                <a href="#berita" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-amber-50 dark:hover:bg-slate-800 hover:text-amber-600 transition flex items-center justify-between font-bold text-amber-600 dark:text-amber-400">
                    <span>📰 Kabar & Warta Sekolah</span>
                    <span class="text-[10px] bg-amber-100 dark:bg-amber-950/80 px-2 py-0.5 rounded-md">Update</span>
                </a>
                <a href="#guru-staf" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>👨‍🏫 Tenaga Pendidik & Kependidikan</span>
                    <span class="text-[10px] text-slate-400">{{ count($staffMembers ?? []) }} Pegawai</span>
                </a>
                <a href="#fasilitas" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>🏫 Sarana & Lingkungan Belajar</span>
                    <span class="text-[10px] text-slate-400">Sarpras</span>
                </a>
                <a href="#agenda" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>📅 Kalender Akademik</span>
                    <span class="text-[10px] text-slate-400">Kegiatan</span>
                </a>
                <a href="#ekstrakurikuler" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>🎯 Minat, Bakat & Kepemimpinan</span>
                    <span class="text-[10px] text-slate-400">{{ $extracurricularCount }} Ekskul</span>
                </a>
                <a href="#pembiasaan" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950 hover:text-emerald-600 transition flex items-center justify-between font-bold text-emerald-600 dark:text-emerald-400">
                    <span>🌱 Pembiasaan Positif Harian Siswa</span>
                    <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950/80 px-2 py-0.5 rounded-md">7 Kebiasaan</span>
                </a>
                <a href="#prestasi" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-amber-50 dark:hover:bg-slate-800 hover:text-amber-600 transition flex items-center justify-between">
                    <span>🏆 Klasemen & Torehan Prestasi Siswa</span>
                    <span class="text-[10px] text-amber-500 font-bold">Top 10</span>
                </a>
                <a href="#ekosistem-sis" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between font-bold text-indigo-600 dark:text-indigo-400">
                    <span>💻 Transformasi Digital Terpadu</span>
                    <span class="text-[10px] bg-indigo-100 dark:bg-indigo-950/80 px-2 py-0.5 rounded-md">SIS Terpadu</span>
                </a>
                <a href="#layanan-peran" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>⚡ Akses Fleksibel Berdasarkan Kebutuhan</span>
                    <span class="text-[10px] text-indigo-500 font-bold">Hub Peran</span>
                </a>
                <a href="#kontak" class="mobile-nav-link px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 transition flex items-center justify-between">
                    <span>📍 Informasi & Layanan Publik</span>
                    <span class="text-[10px] text-slate-400">Kontak</span>
                </a>
            </nav>
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                @guest
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 text-white shadow-md flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Masuk Portal SIS-SMA1LE</span>
                    </a>
                @endguest
                <a href="{{ route('parent.portal') }}" class="w-full text-center py-2.5 px-4 rounded-xl font-bold text-xs bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                    👨‍👩‍👧‍👦 Buka Portal Orang Tua Siswa (Tanpa Login)
                </a>
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="text-center py-2 px-1.5 rounded-xl font-bold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                        📺 Kios TV
                    </a>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="text-center py-2 px-1.5 rounded-xl font-bold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                        👥 Kios Tamu
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" target="_blank" class="text-center py-2 px-1.5 rounded-xl font-bold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                        📚 Kios Perpus
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Running Text Bar (Layar Ponsel & Tablet) -->
        <div class="lg:hidden bg-slate-100/95 dark:bg-slate-900/95 border-t border-slate-200/80 dark:border-slate-800 px-3 py-1.5 flex items-center gap-2 overflow-hidden select-none">
            <span class="shrink-0 px-2 py-0.5 rounded-lg bg-emerald-600 text-white font-black text-[9px] uppercase tracking-wider">
                {{ $runningBadge }}
            </span>
            <div class="relative flex-1 overflow-hidden h-5 flex items-center">
                <div class="header-running-track flex items-center whitespace-nowrap text-[11px] font-semibold text-slate-700 dark:text-slate-200"
                     style="--header-marquee-duration: {{ $speedDuration }};">
                    <span class="inline-block px-3">{{ $runningText }}</span>
                    <span class="inline-block px-3 text-emerald-500 font-bold">•</span>
                    <span class="inline-block px-3">{{ $runningText }}</span>
                    <span class="inline-block px-3 text-emerald-500 font-bold">•</span>
                </div>
            </div>
        </div>
    </header>
</div>

    <!-- ==================== HERO SECTION ==================== -->
    <section id="hero" class="relative overflow-hidden pt-8 pb-12 sm:pt-14 sm:pb-20 md:pt-16 md:pb-24 bg-slate-950 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}" alt="Gedung {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}" class="w-full h-full object-cover object-center filter brightness-[0.38] scale-105 transition-transform duration-1000 ease-out">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-indigo-950/40"></div>
            <div class="absolute inset-0 bg-radial-[at_top] from-indigo-600/25 via-transparent to-slate-950/90"></div>
        </div>

        <div class="relative z-10 max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center space-y-4 sm:space-y-6">
                <!-- Badges -->
                <div class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                    @if(!empty($settings['website_hero_badge']))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 backdrop-blur-md">
                            ⭐ {{ $settings['website_hero_badge'] }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400"></span>
                        Akreditasi A Unggul
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 backdrop-blur-md">
                        ⭐ Kurikulum Nasional 2025
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 backdrop-blur-md">
                        🌱 Sekolah Digital Berkarakter
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/40 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Data SIS-SMA1LE
                    </span>
                </div>

                <!-- Main Headline -->
                <h2 class="font-heading font-black text-2xl sm:text-4xl md:text-5xl lg:text-6xl text-white tracking-tight leading-tight sm:leading-[1.1] text-glow-indigo">
                    @if(!empty($settings['website_hero_headline']))
                        {{ $settings['website_hero_headline'] }}
                    @else
                        Mencetak Generasi <span class="bg-gradient-to-r from-indigo-300 via-sky-300 to-emerald-300 bg-clip-text text-transparent">Cerdas, Berakhlak,</span> & Berdaya Saing Digital.
                    @endif
                </h2>

                <!-- Subtitle -->
                <p class="text-xs sm:text-base lg:text-lg text-slate-300 leading-relaxed font-normal max-w-2xl mx-auto px-2">
                    {{ $settings['website_hero_subheadline'] ?? ('Selamat datang di Official Portal ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' Sukabumi. Pusat ekosistem pendidikan cerdas terpadu yang memadukan sistem kehadiran multi-metode, LMS berbasis AI, dan transparansi portal orang tua tanpa batas.') }}
                </p>

                <!-- Live Dynamic Counters Grid (Sync to SIS-SMA1LE Database) -->
                <div class="pt-4 sm:pt-6 border-t border-slate-800/80 max-w-3xl mx-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                        <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xs">
                            <div class="text-xl sm:text-3xl font-heading font-black text-indigo-400 counter-value transition-all duration-300" id="live-stat-student" data-target="{{ $studentCount }}">
                                {{ $studentCount }}
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5">Siswa Terdaftar</div>
                        </div>
                        <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xs">
                            <div class="text-xl sm:text-3xl font-heading font-black text-emerald-400 counter-value transition-all duration-300" id="live-stat-teacher" data-target="{{ $teacherCount }}">
                                {{ $teacherCount }}
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5">Guru & Tendik</div>
                        </div>
                        <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xs">
                            <div class="text-xl sm:text-3xl font-heading font-black text-sky-400 counter-value transition-all duration-300" id="live-stat-class" data-target="{{ $classCount }}">
                                {{ $classCount }}
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5">Rombel Kelas</div>
                        </div>
                        <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xs">
                            <div class="text-xl sm:text-3xl font-heading font-black text-amber-400 counter-value transition-all duration-300" id="live-stat-ekskul" data-target="{{ $extracurricularCount }}">
                                {{ $extracurricularCount }}
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5">Ekstrakurikuler</div>
                        </div>
                    </div>
                    <div class="mt-2.5 sm:mt-3 flex items-center justify-center gap-1.5 sm:gap-2 text-[10px] sm:text-[11px] font-medium text-emerald-400/90 select-none">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>Tersinkronisasi Otomatis & Real-Time dengan Database SIS-SMA1LE</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 1. SAMBUTAN KEPALA SEKOLAH ==================== -->
    @php
        $speechToShow = $activeSpeech ?? ($speeches[0] ?? null);
        $speakerName = $speechToShow['speaker_name'] ?? ($settings['website_headmaster_name'] ?? ($settings['headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd.'));
        $speakerTitle = $speechToShow['speaker_title'] ?? ($settings['website_headmaster_title'] ?? ('Kepala ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong')));
        $speechTitle = $speechToShow['title'] ?? ($settings['website_headmaster_speech_title'] ?? 'Menghidupkan Potensi, Meneguhkan Budi Pekerti di Era Transformasi Digital');
        $speechQuote = $speechToShow['quote'] ?? ($settings['website_headmaster_quote'] ?? 'Pendidikan sejati bukan sekadar mengisi wadah dengan informasi, melainkan menyalakan lentera budi pekerti, nalar kritis, dan integritas yang abadi.');
        $speechP1 = $speechToShow['speech_p1'] ?? ($settings['website_headmaster_speech_p1'] ?? ('Puji dan syukur senantiasa kita panjatkan ke hadirat Allah SWT. Selamat datang di portal resmi ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong') . ', wadah silaturahmi informasi, transparansi akademik, serta pusat inovasi pembelajaran bagi seluruh warga sekolah, orang tua, dan masyarakat luas.'));
        $speechP2 = $speechToShow['speech_p2'] ?? ($settings['website_headmaster_speech_p2'] ?? 'Di era revolusi digital dan kurikulum nasional terkini berlandaskan Permendikdasmen No. 13 Tahun 2025, kami meyakini bahwa teknologi harus berpihak pada kemanusiaan dan kearifan budi pekerti. Sistem pembelajaran yang kami bangun dirancang untuk memfasilitasi Deep Learning, melatih nalar kritis, serta membentengi generasi muda dengan nilai keimanan yang kokoh.');
        $speechExt = $speechToShow['speech_extended'] ?? ($settings['website_headmaster_speech_extended'] ?? 'Melalui aplikasi sekolah mandiri ini—dari presensi cerdas 5 metode hingga materi interaktif berbasis AI—kami ingin memastikan setiap siswa mendapatkan ruang bertumbuh secara adil dan optimal. Kerjasama harmonis antara sekolah dan orang tua adalah kunci keberhasilan putra-putri kita.');
        $speechPhoto = !empty($speechToShow['photo']) ? $speechToShow['photo'] : ($settings['website_headmaster_photo'] ?? 'images/website/headmaster.jpg');
    @endphp
    <section id="sambutan" class="py-12 sm:py-16 md:py-24 relative bg-white dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card rounded-3xl p-6 sm:p-10 lg:p-12 border border-slate-200/80 dark:border-slate-800/80 shadow-xl overflow-hidden relative">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Headmaster Photo & Title -->
                    <div class="lg:col-span-5 text-center">
                        <div class="relative inline-block mx-auto">
                            <div class="w-52 sm:w-60 aspect-[3/4] rounded-3xl bg-gradient-to-tr from-indigo-600 via-blue-500 to-emerald-400 p-1 shadow-2xl shadow-indigo-600/30 overflow-hidden mx-auto">
                                <img src="{{ asset($speechPhoto) }}" alt="Kepala Sekolah - {{ $speakerName }}" class="w-full h-full object-cover object-top rounded-[22px] hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3.5 py-1 rounded-full shadow-lg flex items-center gap-2 whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Kepala Sekolah Berdedikasi</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white">
                                {{ $speakerName }}
                            </h3>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1">
                                {{ $speakerTitle }}
                            </p>
                        </div>
                    </div>

                    <!-- Speech Content -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                <span>💬 Sambutan Kepala Sekolah</span>
                            </span>
                            @if(!empty($speechToShow['speech_date']))
                                <span class="text-[11px] text-slate-400 font-semibold">
                                    📅 {{ \Carbon\Carbon::parse($speechToShow['speech_date'])->translatedFormat('d F Y') }}
                                </span>
                            @endif
                        </div>
                        
                        <h2 class="font-heading font-black text-2xl sm:text-3xl xl:text-4xl text-slate-900 dark:text-white tracking-tight leading-tight">
                            {{ $speechTitle }}
                        </h2>

                        <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            <p>
                                <em>Assalamu’alaikum Warahmatullahi Wabarakatuh, Salam Sejahtera, Sampurasun.</em>
                            </p>
                            <p>
                                {{ $speechP1 }}
                            </p>
                            @if(!empty($speechP2))
                                <p>
                                    {{ $speechP2 }}
                                </p>
                            @endif
                            <div id="speech-extended" class="hidden space-y-3">
                                <p>
                                    {{ $speechExt }}
                                </p>
                            </div>
                        </div>

                        <!-- Read More Toggle Button -->
                        <div>
                            <button type="button" id="toggle-speech-btn" onclick="toggleHeadmasterSpeech()" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 cursor-pointer">
                                <span id="toggle-speech-text">Baca Selengkapnya</span>
                                <span id="toggle-speech-icon">↓</span>
                            </button>
                        </div>

                        <!-- Quote Box -->
                        <div class="p-3.5 sm:p-4 rounded-2xl bg-indigo-50/70 dark:bg-slate-900/80 border-l-4 border-indigo-600 dark:border-indigo-500 text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-medium italic">
                            "{{ $speechQuote }}"
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 2. PROFIL LEMBAGA ==================== -->
    <section id="profil" class="py-12 sm:py-16 md:py-24 bg-slate-100/60 dark:bg-slate-900/40 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 mb-3">
                    🏛️ Profil Lembaga
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                    Visi & Misi Unggulan Sekolah
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Landasan filosofis dan arah gerak langkah {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }} dalam mendidik generasi penerus bangsa yang unggul, santun, dan mandiri.
                </p>
            </div>

            <!-- Visi Card -->
            <div class="mb-10 glass-card p-6 sm:p-10 rounded-3xl border border-indigo-200 dark:border-indigo-900/50 shadow-xl relative overflow-hidden text-center bg-radial-[at_top] from-indigo-500/10 via-transparent to-transparent">
                <span class="text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Visi Resmi Sekolah</span>
                <h3 class="font-heading font-black text-lg sm:text-2xl xl:text-3xl text-slate-900 dark:text-white mt-2 max-w-3xl mx-auto leading-snug">
                    "{{ $settings['website_visi'] ?? 'Terwujudnya Peserta Didik yang Beriman dan Bertaqwa, Berakhlak Mulia, Cerdas, Terampil, Mandiri, dan Berwawasan Lingkungan Global.' }}"
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 font-medium">
                    {{ $settings['website_visi_sub'] ?? '(Unggul dalam IPTEK, Kokoh dalam IMTAQ, Harmonis dengan Alam Semesta)' }}
                </p>
            </div>

            <!-- 4 Misi Utama Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($missions as $misi)
                    <div class="glass-card p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center mb-3">
                            {{ $misi['number'] ?? '01' }}
                        </div>
                        <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base mb-1.5">{{ $misi['title'] }}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $misi['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 3. KABAR & WARTA SEKOLAH ==================== -->
    <section id="berita" class="py-12 sm:py-16 md:py-24 relative bg-white dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <span>📰 Kabar & Warta Sekolah</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                            {{ count($newsArticles) }} Artikel Tayang
                        </span>
                    </div>
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Berita, Informasi & Prestasi Terkini
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-2xl">
                        Dokumentasi kegiatan akademik, torehan prestasi membanggakan, kurikulum deep learning, serta pengumuman resmi civitas akademika {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}.
                    </p>
                </div>

                @auth
                    @if(auth()->user()->canAccessWebsiteContentTab('news'))
                        <div class="shrink-0">
                            <a href="{{ route('master.website_content', ['tab' => 'news']) }}" class="px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 text-xs font-bold inline-flex items-center gap-1.5 transition">
                                <span>✏️</span>
                                <span>Kelola Berita</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- News Filter Controls & Real-Time Search -->
            <div class="mb-8 p-4 sm:p-5 rounded-3xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 md:pb-0 text-xs font-bold">
                    <button type="button" onclick="filterNewsCategory('all', this)" class="news-cat-btn px-4 py-2 rounded-xl bg-indigo-600 text-white shadow-xs transition whitespace-nowrap cursor-pointer">
                        Semua Kategori
                    </button>
                    <button type="button" onclick="filterNewsCategory('Prestasi', this)" class="news-cat-btn px-4 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition whitespace-nowrap cursor-pointer">
                        🏆 Prestasi
                    </button>
                    <button type="button" onclick="filterNewsCategory('Akademik', this)" class="news-cat-btn px-4 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition whitespace-nowrap cursor-pointer">
                        📚 Akademik
                    </button>
                    <button type="button" onclick="filterNewsCategory('Kegiatan', this)" class="news-cat-btn px-4 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition whitespace-nowrap cursor-pointer">
                        🎪 Kegiatan
                    </button>
                    <button type="button" onclick="filterNewsCategory('Pengumuman', this)" class="news-cat-btn px-4 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition whitespace-nowrap cursor-pointer">
                        📢 Pengumuman
                    </button>
                </div>

                <div class="relative w-full md:w-80">
                    <input type="text" id="news-search-input" oninput="filterNewsLive()" placeholder="Cari artikel berita..." class="w-full pl-9 pr-8 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                    <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                    <button type="button" id="news-search-clear" onclick="clearNewsSearch()" class="hidden absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs">✕</button>
                </div>
            </div>

            <!-- News Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="news-grid">
                @forelse($newsArticles as $article)
                    <article class="news-card glass-card rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between"
                             data-category="{{ $article['category'] ?? 'Berita' }}"
                             data-title="{{ strtolower($article['title'] ?? '') }}"
                             data-excerpt="{{ strtolower($article['excerpt'] ?? '') }}">
                        <div>
                            <div class="relative aspect-video overflow-hidden bg-slate-100 dark:bg-slate-800">
                                <img src="{{ asset($article['image'] ?? 'images/website/classroom.jpg') }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-600/90 text-white backdrop-blur-md shadow-xs">
                                    {{ $article['category'] ?? 'Berita' }}
                                </span>
                            </div>

                            <div class="p-6 space-y-2.5">
                                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                    <span>🗓️ {{ \Carbon\Carbon::parse($article['date'])->translatedFormat('d M Y') }}</span>
                                    <span>•</span>
                                    <span>✍️ {{ $article['author'] ?? 'Humas' }}</span>
                                    @if(!empty($article['created_by_name']))
                                        <span>•</span>
                                        <span title="Diterbitkan oleh {{ $article['created_by_name'] }}">👤 {{ $article['created_by_name'] }}</span>
                                    @endif
                                </div>
                                <h3 class="font-heading font-bold text-slate-900 dark:text-white text-base sm:text-lg leading-snug line-clamp-2">
                                    {{ $article['title'] }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                    {{ $article['excerpt'] }}
                                </p>
                            </div>
                        </div>

                        <div class="p-6 pt-0 flex items-center gap-2">
                            <button type="button" onclick="openNewsModal({{ json_encode($article) }})" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-indigo-600 dark:bg-slate-800 dark:hover:bg-indigo-600 text-slate-800 dark:text-slate-200 hover:text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Baca Selengkapnya</span>
                                <span>→</span>
                            </button>
                            <button type="button" onclick='shareNewsWhatsApp({{ json_encode($article) }})' title="Bagikan Berita & Gambar ke WhatsApp" class="px-3 py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-600 hover:text-white border border-emerald-500/20 transition flex items-center justify-center cursor-pointer">
                                <span class="text-sm">💬</span>
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 text-xs rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        Belum ada artikel berita yang diterbitkan.
                    </div>
                @endforelse
            </div>

            <div id="news-empty-msg" class="hidden text-center py-12 text-slate-500 text-xs">
                Tidak ada berita yang cocok dengan pencarian Anda.
            </div>

        </div>
    </section>

    <!-- ==================== 4. TENAGA PENDIDIK & KEPENDIDIKAN ==================== -->
    <section id="guru-staf" class="py-12 sm:py-16 md:py-24 relative bg-slate-100/60 dark:bg-slate-900/40 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            @php
                $staffList = $staffMembers ?? [];
                $totalStaffCount = count($staffList);
                $guruCount = collect($staffList)->where('category', 'guru')->count();
                $tuCount = collect($staffList)->where('category', 'staf_tu')->count();
            @endphp

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span>👨‍🏫 Tenaga Pendidik & Kependidikan</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                            {{ $totalStaffCount }} Pegawai Terdaftar
                        </span>
                    </div>
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Dewan Guru & Staf Tata Usaha
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                        Mengenal lebih dekat para pendidik berdedikasi dan tenaga kependidikan profesional yang membimbing, mengajar, serta mengawal tata kelola {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}.
                    </p>
                </div>

                @auth
                    @if(auth()->user()->canAccessWebsiteContentTab('staff'))
                        <div class="shrink-0">
                            <a href="{{ route('master.website_content', ['tab' => 'staff']) }}" class="px-3.5 py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-xs font-bold inline-flex items-center gap-1.5 transition">
                                <span>✏️</span>
                                <span>Kelola Guru & Staf</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Filter Controls & Real-Time Search Bar -->
            <div class="mb-8 p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800/80 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 md:pb-0">
                    <button type="button" onclick="filterStaffWebsite('all', this)" class="staff-cat-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-600 text-white shadow-md shadow-emerald-600/30 transition cursor-pointer whitespace-nowrap">
                        Semua Pegawai ({{ $totalStaffCount }})
                    </button>
                    <button type="button" onclick="filterStaffWebsite('guru', this)" class="staff-cat-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                        👨‍🏫 Dewan Guru ({{ $guruCount }})
                    </button>
                    <button type="button" onclick="filterStaffWebsite('staf_tu', this)" class="staff-cat-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                        💼 Staf Tata Usaha ({{ $tuCount }})
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-80 lg:w-96">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">🔍</span>
                        <input type="text" id="staff-search-input" oninput="filterStaffLive()" placeholder="Cari nama, NIP, mapel, atau tugas..." class="w-full pl-10 pr-9 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        <button type="button" onclick="clearStaffSearch()" id="staff-clear-search-btn" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer hidden">✕</button>
                    </div>
                    <span id="staff-live-count" class="shrink-0 hidden sm:inline-block px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                        {{ $totalStaffCount }} pegawai
                    </span>
                </div>
            </div>

            <!-- Running Ticker Controls Bar -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-white/70 dark:bg-slate-950/70 border border-slate-200/70 dark:border-slate-800/70 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <button type="button" id="staff-marquee-toggle-btn" onclick="toggleStaffMarquee()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 dark:bg-emerald-600 text-white font-bold text-xs shadow-md hover:bg-slate-800 dark:hover:bg-emerald-500 transition cursor-pointer">
                        <span id="staff-marquee-toggle-icon">⏸</span>
                        <span id="staff-marquee-toggle-text">Jeda Gerakan</span>
                    </button>
                    <button type="button" onclick="nudgeStaffMarquee(-340)" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 transition cursor-pointer text-xs font-bold inline-flex items-center gap-1">
                        <span>◀</span>
                        <span class="hidden sm:inline">Mundur</span>
                    </button>
                    <button type="button" onclick="nudgeStaffMarquee(340)" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 transition cursor-pointer text-xs font-bold inline-flex items-center gap-1">
                        <span class="hidden sm:inline">Maju</span>
                        <span>▶</span>
                    </button>
                </div>

                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Arahkan kursor ke kartu untuk menjeda & membaca informasi guru secara detail.</span>
                </div>
            </div>

            <style>
                @keyframes staffRunningTicker {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                .staff-running-track {
                    display: flex;
                    width: max-content;
                    animation: staffRunningTicker 60s linear infinite;
                    will-change: transform;
                }
                .staff-marquee-viewport:hover .staff-running-track,
                .staff-running-track.is-paused {
                    animation-play-state: paused !important;
                }
                .staff-marquee-viewport { scrollbar-width: none; -ms-overflow-style: none; }
                .staff-marquee-viewport::-webkit-scrollbar { display: none; }
            </style>

            <!-- Running Marquee Viewport with Proportionate Teacher Cards (Foto tidak raksasa) -->
            <div class="relative w-full overflow-x-auto staff-marquee-viewport py-4" id="staff-marquee-viewport">
                <div class="hidden sm:block absolute left-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-r from-slate-100/95 dark:from-slate-900/95 to-transparent z-20 pointer-events-none"></div>
                <div class="hidden sm:block absolute right-0 top-0 bottom-0 w-12 sm:w-20 bg-gradient-to-l from-slate-100/95 dark:from-slate-900/95 to-transparent z-20 pointer-events-none"></div>

                <div id="staff-running-track" class="staff-running-track flex gap-5 select-none">
                    @foreach([1, 2] as $loopPass)
                        <div class="staff-track-group flex gap-5 shrink-0">
                            @forelse($staffList as $member)
                                @php
                                    $cleanPhoto = !empty($member['photo']) ? explode('?', $member['photo'])[0] : null;
                                    $photoExists = $cleanPhoto && file_exists(public_path($cleanPhoto));
                                    $photoUrl = $photoExists ? (asset($cleanPhoto) . '?v=' . filemtime(public_path($cleanPhoto))) : null;
                                    $isTu = ($member['category'] ?? 'guru') === 'staf_tu';
                                    $tuDuty = $member['tu_duty'] ?? ($member['duty'] ?? 'Pelaksana Administrasi');
                                    $status = $member['employment_status'] ?? 'PNS';
                                    $statusClass = match(strtoupper($status)) {
                                        'PNS' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                        'PPPK' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                        default => 'bg-slate-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                                    };
                                    $cleanName = preg_replace('/[,\.]/', ' ', $member['name'] ?? 'Pegawai');
                                    $words = array_values(array_filter(explode(' ', trim($cleanName))));
                                    $initials = count($words) >= 2 ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($words[0] ?? 'P', 0, 2));
                                @endphp
                                <div class="staff-card w-64 sm:w-72 shrink-0 group relative flex flex-col rounded-3xl bg-white dark:bg-slate-950 border border-slate-200/90 dark:border-slate-800/90 shadow-md hover:shadow-2xl hover:border-emerald-500/50 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
                                     data-category="{{ $member['category'] ?? 'guru' }}"
                                     data-name="{{ strtolower($member['name'] ?? '') }}"
                                     data-nip="{{ strtolower($member['nip'] ?? '') }}"
                                     data-mapel="{{ strtolower($member['mapel'] ?? '') }}"
                                     data-duty="{{ strtolower($isTu ? $tuDuty : ($member['duty'] ?? '')) }}">
                                    
                                    <!-- Photo Block (Besar, Jelas, Proporsional & Hero Element) -->
                                    <div class="h-52 sm:h-56 w-full relative overflow-hidden bg-slate-100 dark:bg-slate-900 shrink-0">
                                        @if($photoExists)
                                            <img src="{{ $photoUrl }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                        @else
                                            <div class="w-full h-full {{ $isTu ? 'bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950' : 'bg-gradient-to-tr from-emerald-700 via-emerald-600 to-teal-400 text-white' }} flex flex-col items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-500 p-4">
                                                <span class="font-heading font-black text-5xl sm:text-6xl tracking-wider opacity-90 drop-shadow-sm">{{ $initials }}</span>
                                                <span class="text-[10px] font-bold tracking-widest uppercase mt-2 opacity-80">{{ $isTu ? 'Staf Tata Usaha' : 'Dewan Guru' }}</span>
                                            </div>
                                        @endif

                                        <!-- Gradient overlay on photo bottom for smooth blend -->
                                        <div class="absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/60 via-black/20 to-transparent pointer-events-none"></div>

                                        <!-- Floating Top Badges -->
                                        <div class="absolute top-3 inset-x-3 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                            @if($isTu)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wide bg-amber-500 text-slate-950 shadow-md backdrop-blur-md">
                                                    <span>💼</span>
                                                    <span>Staf TU</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wide bg-emerald-600 text-white shadow-md backdrop-blur-md">
                                                    <span>👨‍🏫</span>
                                                    <span>Dewan Guru</span>
                                                </span>
                                            @endif

                                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-black/60 text-white backdrop-blur-md border border-white/20 shadow-md">
                                                {{ $status }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Compact Text Area (Rapi & Tidak Terlalu Dominan) -->
                                    <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between text-center space-y-2">
                                        <div>
                                            <h3 class="font-heading font-black text-slate-900 dark:text-white text-xs sm:text-sm leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-1" title="{{ $member['name'] }}">
                                                {{ $member['name'] }}
                                            </h3>

                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                NIP: {{ !empty($member['nip']) && $member['nip'] !== '-' ? $member['nip'] : 'Identitas Terdaftar' }}
                                            </div>

                                            <div class="mt-2">
                                                @if($isTu)
                                                    <div class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 dark:bg-amber-950/40 border border-amber-500/20 text-center">
                                                        <span class="sr-only">Tugas / Unit Kerja Tata Usaha:</span>
                                                        <span class="font-bold text-[11px] text-amber-700 dark:text-amber-300 line-clamp-1" title="{{ $tuDuty }}">{{ $tuDuty }}</span>
                                                    </div>
                                                @else
                                                    <div class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 dark:bg-emerald-950/40 border border-emerald-500/20 text-center">
                                                        <span class="sr-only">Mata Pelajaran yang Dipegang:</span>
                                                        <span class="font-black text-[11px] text-emerald-700 dark:text-emerald-300 line-clamp-1" title="{{ $member['mapel'] ?? 'Mata Pelajaran Umum' }}">
                                                            {{ $member['mapel'] ?? 'Mata Pelajaran Umum' }}
                                                        </span>
                                                    </div>
                                                    @if(!empty($member['duty']) && $member['duty'] !== '-')
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-1">
                                                            {{ $member['duty'] }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
                                            <span>{{ ($member['gender'] ?? 'L') === 'P' ? '👩 Guru Putri' : '👨 Guru Putra' }}</span>
                                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">● Aktif Mengajar</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada data guru atau staf tata usaha yang ditambahkan.
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="staff-empty-msg" class="hidden text-center py-10 text-slate-500 text-xs">
                Tidak ditemukan pegawai dengan kata kunci tersebut.
            </div>

        </div>
    </section>

    <!-- ==================== 5. SARANA & LINGKUNGAN BELAJAR ==================== -->
    <section id="fasilitas" class="py-12 sm:py-16 md:py-24 bg-white dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800 mb-2">
                        🏫 Sarana & Lingkungan Belajar
                    </span>
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Fasilitas Unggulan Kampus SMAN 1 Lengkong
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-2xl">
                        Sarana fisik modern, laboratorium berteknologi, perpustakaan digital, serta ruang hijau asri demi kelancaran pembelajaran, riset, dan kreativitas siswa.
                    </p>
                </div>

                @auth
                    @if(auth()->user()->canAccessWebsiteContentTab('facilities'))
                        <div class="shrink-0">
                            <a href="{{ route('master.website_content', ['tab' => 'facilities']) }}" class="px-3.5 py-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/30 text-xs font-bold inline-flex items-center gap-1.5 transition">
                                <span>✏️</span>
                                <span>Kelola Fasilitas</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            @php
                $getFacilityIcon = function($iconKey, $name = '') {
                    $key = strtolower(($iconKey ?? '') . ' ' . ($name ?? ''));
                    if (str_contains($key, 'komputer') || str_contains($key, 'desktop') || str_contains($key, 'multimedia') || str_contains($key, 'cbt')) {
                        return '<svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>';
                    } elseif (str_contains($key, 'beaker') || str_contains($key, 'sains') || str_contains($key, 'bioteknologi') || str_contains($key, 'lab') || str_contains($key, 'kimia') || str_contains($key, 'biologi')) {
                        return '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>';
                    } elseif (str_contains($key, 'book') || str_contains($key, 'perpustakaan') || str_contains($key, 'literasi') || str_contains($key, 'baca')) {
                        return '<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
                    } elseif (str_contains($key, 'masjid') || str_contains($key, 'library') || str_contains($key, 'musholla') || str_contains($key, 'tahfidz') || str_contains($key, 'ibadah') || str_contains($key, 'agama')) {
                        return '<svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>';
                    } elseif (str_contains($key, 'trophy') || str_contains($key, 'olahraga') || str_contains($key, 'lapangan') || str_contains($key, 'gelanggang') || str_contains($key, 'futsal') || str_contains($key, 'basket') || str_contains($key, 'voli')) {
                        return '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-6.496C16.14 7.234 17.5 5.768 17.5 4H6.5c0 1.768 1.36 3.234 3.004 3.754m5.992 0a6.002 6.002 0 01-5.992 0m5.992 0v2.621a3 3 0 01-3 3h-.008a3 3 0 01-3-3V7.754"/></svg>';
                    } elseif (str_contains($key, 'sparkles') || str_contains($key, 'smart') || str_contains($key, 'kelas') || str_contains($key, 'kios') || str_contains($key, 'tv')) {
                        return '<svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>';
                    } else {
                        return '<svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>';
                    }
                };
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($facilities as $item)
                    <div class="glass-card rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        <div>
                            @if(!empty($item['image']))
                                <div class="relative w-full h-36 sm:h-40 md:h-44 overflow-hidden bg-slate-100 dark:bg-slate-800 border-b border-slate-200/80 dark:border-slate-800 shrink-0">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-black/20 pointer-events-none"></div>
                                    <div class="absolute top-2.5 left-2.5 w-8 h-8 rounded-xl bg-white/95 dark:bg-slate-900/90 backdrop-blur-md shadow-md border border-white/40 dark:border-slate-700/60 flex items-center justify-center">
                                        {!! $getFacilityIcon($item['icon'] ?? '', $item['name'] ?? '') !!}
                                    </div>
                                    <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-900/80 backdrop-blur-md text-sky-300 border border-white/20 shadow-xs">
                                        Fasilitas Kampus
                                    </span>
                                </div>
                            @else
                                <div class="p-5 pb-2 flex items-center justify-between">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500/10 to-indigo-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-2xs">
                                        {!! $getFacilityIcon($item['icon'] ?? '', $item['name'] ?? '') !!}
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                        Fasilitas Kampus
                                    </span>
                                </div>
                            @endif

                            <div class="p-4 sm:p-5 space-y-1.5">
                                <h3 class="font-heading font-bold text-slate-900 dark:text-white text-sm sm:text-base leading-snug">
                                    {{ $item['name'] }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>

                        <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
                            <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                <span>✓</span>
                                <span>Standar Nasional Pendidikan</span>
                            </span>
                            <span>SMAN 1 Lengkong</span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 6. KALENDER AKADEMIK ==================== -->
    <section id="agenda" class="py-12 sm:py-16 md:py-24 relative bg-slate-100/60 dark:bg-slate-900/40 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 mb-2">
                        📅 Kalender Akademik
                    </span>
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Agenda & Kegiatan Penting Sekolah
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-2xl">
                        Jadwal kegiatan kurikuler, asesmen sumatif, peringatan hari besar nasional, dan kalender pendidikan semester berjalan.
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 transition flex items-center gap-1.5">
                        <span>📺 Display TV Kiosk</span>
                    </a>
                    @auth
                        @if(auth()->user()->canAccessWebsiteContentTab('agendas'))
                            <a href="{{ route('master.website_content', ['tab' => 'agendas']) }}" class="px-3.5 py-2 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30 text-xs font-bold inline-flex items-center gap-1.5 transition">
                                <span>✏️</span>
                                <span>Kelola Agenda</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($agendas as $agenda)
                    @php
                        $day = '01';
                        $month = 'AGENDA';
                        $fullDate = $agenda['date'] ?? '';
                        try {
                            if (!empty($agenda['date'])) {
                                $cd = \Carbon\Carbon::parse($agenda['date']);
                                $day = $cd->format('d');
                                $month = $cd->translatedFormat('M');
                                
                                if (!empty($agenda['end_date']) && $agenda['end_date'] !== $agenda['date']) {
                                    $cdEnd = \Carbon\Carbon::parse($agenda['end_date']);
                                    if ($cd->format('Y-m') === $cdEnd->format('Y-m')) {
                                        $day = $cd->format('d') . '-' . $cdEnd->format('d');
                                        $fullDate = $cd->format('d') . ' - ' . $cdEnd->translatedFormat('d F Y');
                                    } else {
                                        $fullDate = $cd->translatedFormat('d M') . ' - ' . $cdEnd->translatedFormat('d M Y');
                                    }
                                } else {
                                    $fullDate = $cd->translatedFormat('d F Y');
                                }
                            }
                        } catch (\Exception $e) {
                            $day = '--';
                            $month = 'TBA';
                            $fullDate = $agenda['date'] ?? 'TBA';
                        }
                    @endphp
                    <div class="glass-card rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div>
                            @if(!empty($agenda['image']))
                                <div class="w-full aspect-video overflow-hidden bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-800">
                                    <img src="{{ asset($agenda['image']) }}" alt="{{ $agenda['title'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                                </div>
                            @endif

                            <div class="p-5 sm:p-6 space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        {{ $agenda['tag'] ?? 'Akademik' }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                        📅 {{ $fullDate }}
                                    </span>
                                </div>

                                <h3 class="font-heading font-bold text-slate-900 dark:text-white text-base leading-snug">
                                    {{ $agenda['title'] }}
                                </h3>

                                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {{ $agenda['description'] }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 pt-0 border-t border-slate-100 dark:border-slate-800/80 mt-2 flex items-center justify-between text-[11px] text-slate-400">
                            <span>📍 Kampus SMAN 1 Lengkong</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="shareAgendaWhatsApp({{ json_encode($agenda) }})" class="p-1 rounded-lg text-emerald-600 hover:bg-emerald-500/10 transition cursor-pointer" title="Bagikan Agenda ke WhatsApp">
                                    <span>💬</span>
                                </button>
                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">Terjadwal</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 7. MINAT, BAKAT & KEPEMIMPINAN ==================== -->
    <section id="ekstrakurikuler" class="py-12 sm:py-16 md:py-24 bg-white dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 mb-2">
                        🎯 Minat, Bakat & Kepemimpinan
                    </span>
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Wadah Ekstrakurikuler & Organisasi Siswa
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-2xl">
                        Mengasah potensi non-akademik, kepemimpinan (OSIS & MPK), kepanduan, olahraga prestasi, serta seni budaya lokal Sunda secara seimbang.
                    </p>
                </div>

                <div class="w-full md:w-72">
                    <div class="relative">
                        <input type="text" id="ekskul-search-input" oninput="filterEkskulLive()" placeholder="Cari nama ekstrakurikuler..." class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                        <span class="absolute left-3 top-3 text-slate-400 text-xs">🔍</span>
                    </div>
                </div>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 mb-8 no-scrollbar overflow-x-auto pb-1" id="ekskul-filters">
                <button type="button" onclick="filterEkskulCategory('all', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-600 text-white transition">
                    Semua Cabang
                </button>
                <button type="button" onclick="filterEkskulCategory('kepanduan', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Kepanduan & Paskibra
                </button>
                <button type="button" onclick="filterEkskulCategory('olahraga', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Olahraga
                </button>
                <button type="button" onclick="filterEkskulCategory('seni', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Seni & Budaya
                </button>
                <button type="button" onclick="filterEkskulCategory('sains', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Sains & Teknologi
                </button>
                <button type="button" onclick="filterEkskulCategory('religi', this)" class="ekskul-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Keagamaan
                </button>
            </div>

            <!-- Extracurricular Featured Banner & Documentation -->
            <div class="mb-8 rounded-3xl overflow-hidden glass-card border border-slate-200/80 dark:border-slate-800/80 shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 items-center">
                    <div class="lg:col-span-5 h-48 sm:h-56 lg:h-full min-h-[180px] relative overflow-hidden bg-slate-900">
                        <img src="{{ asset($settings['website_extracurriculars_photo'] ?? 'images/website/extracurriculars.jpg') }}" alt="Dokumentasi Ekstrakurikuler" class="w-full h-full object-cover filter brightness-95 hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-r from-slate-950/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-3 px-3 py-1 rounded-full bg-emerald-600/90 text-white font-black text-[10px] uppercase tracking-wider backdrop-blur-md">
                            Dokumentasi Ekskul & Olahraga
                        </div>
                    </div>
                    <div class="lg:col-span-7 p-5 sm:p-6 lg:p-7 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Pengembangan Minat, Bakat & Kepemimpinan</span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white">
                            Panggung Kreasi & Prestasi Pelajar Unggul
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            Dari kejuaraan olahraga, pentas seni budaya Sunda, kepanduan Pramuka & Paskibra, hingga olimpiade sains dan robotika digital—setiap bakat dibina secara intensif oleh guru pembina berdedikasi tinggi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Extracurricular Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="ekskul-grid">
                @forelse($extracurriculars as $ekskul)
                    @php
                        $nameLower = strtolower($ekskul->name);
                        $category = 'umum';
                        $icon = '⭐';
                        if (str_contains($nameLower, 'pramuka') || str_contains($nameLower, 'scout')) {
                            $category = 'kepanduan'; $icon = '🏕️';
                        } elseif (str_contains($nameLower, 'paskibra')) {
                            $category = 'kepanduan'; $icon = '🇮🇩';
                        } elseif (str_contains($nameLower, 'pmr') || str_contains($nameLower, 'palang merah')) {
                            $category = 'kepanduan'; $icon = '🩺';
                        } elseif (str_contains($nameLower, 'futsal') || str_contains($nameLower, 'sepak bola') || str_contains($nameLower, 'voli') || str_contains($nameLower, 'basket') || str_contains($nameLower, 'badminton') || str_contains($nameLower, 'silat') || str_contains($nameLower, 'taekwondo')) {
                            $category = 'olahraga'; $icon = '⚽';
                        } elseif (str_contains($nameLower, 'seni') || str_contains($nameLower, 'tari') || str_contains($nameLower, 'musik') || str_contains($nameLower, 'gamelan') || str_contains($nameLower, 'paduan suara') || str_contains($nameLower, 'teater')) {
                            $category = 'seni'; $icon = '🎭';
                        } elseif (str_contains($nameLower, 'rohis') || str_contains($nameLower, 'irma') || str_contains($nameLower, 'tahfidz') || str_contains($nameLower, 'islam')) {
                            $category = 'religi'; $icon = '🕌';
                        } elseif (str_contains($nameLower, 'komputer') || str_contains($nameLower, 'it') || str_contains($nameLower, 'kir') || str_contains($nameLower, 'robotik') || str_contains($nameLower, 'english')) {
                            $category = 'sains'; $icon = '🔬';
                        }
                        $pembinaGuru = !empty($ekskul->teacher_names) && $ekskul->teacher_names !== 'Belum Ditentukan'
                            ? $ekskul->teacher_names
                            : ($ekskul->teacher?->name ?? 'Dewan Guru Pembina');
                    @endphp
                    <div class="ekskul-card glass-card p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-xl hover:border-indigo-500/60 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between cursor-pointer group" 
                         data-category="{{ $category }}" 
                         data-name="{{ strtolower($ekskul->name) }}"
                         data-ekskul-title="{{ $ekskul->name }}"
                         data-ekskul-category="{{ ucfirst($category) }}"
                         data-ekskul-icon="{{ $icon }}"
                         data-ekskul-desc="{{ $ekskul->description ?? 'Kegiatan pembinaan minat, bakat, dan pembentukan karakter kepemimpinan siswa.' }}"
                         data-pembina-name="{{ $ekskul->pembina_name ?? $pembinaGuru }}"
                         data-pembina-nip="{{ $ekskul->pembina_nip ?? '-' }}"
                         data-pembina-photo="{{ $ekskul->pembina_photo_url ?? '' }}"
                         data-members='@json($ekskul->members_data ?? [])'
                         onclick="openEkskulDetailModal(this)">
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <span class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                                    {{ $icon }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                    {{ ucfirst($category) }}
                                </span>
                            </div>
                            <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $ekskul->name }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                {{ $ekskul->description ?? 'Kegiatan pembinaan minat, bakat, dan pembentukan karakter kepemimpinan siswa.' }}
                            </p>
                        </div>
                        <div>
                            <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                <span class="truncate flex-1" title="Guru Pembina: {{ $pembinaGuru }}">
                                    Pembina: <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ $pembinaGuru }}</strong>
                                </span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 shrink-0">{{ $ekskul->students_count ?? 0 }} Siswa</span>
                            </div>
                            <div class="mt-2.5 pt-2 border-t border-dashed border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between text-[10px] font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform">
                                <span>Lihat Pembina & Anggota Siswa</span>
                                <span>→</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-slate-500 text-sm">
                        Data ekstrakurikuler sedang disiapkan.
                    </div>
                @endforelse
            </div>

            <div id="ekskul-empty-msg" class="hidden text-center py-12 text-slate-500 text-xs">
                Tidak ditemukan ekstrakurikuler dengan kata kunci tersebut.
            </div>

        </div>
    </section>

    <!-- Modal Detail Ekstrakurikuler: Pembina & Anggota Siswa -->
    <div id="ekskul-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md hidden transition-all duration-300" onclick="if(event.target === this) closeEkskulDetailModal()">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="p-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4 bg-gradient-to-r from-indigo-50/50 dark:from-slate-800/40 to-transparent">
                <div class="flex items-center gap-3.5">
                    <span id="ekskul-modal-icon" class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl shrink-0 border border-indigo-500/20">
                        ⭐
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span id="ekskul-modal-category" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                                Umum
                            </span>
                            <span class="text-[11px] text-slate-400">SMAN 1 Lengkong</span>
                        </div>
                        <h3 id="ekskul-modal-title" class="font-heading font-black text-xl text-slate-900 dark:text-white mt-0.5">
                            Nama Ekstrakurikuler
                        </h3>
                    </div>
                </div>
                <button type="button" onclick="closeEkskulDetailModal()" class="w-9 h-9 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition shrink-0" aria-label="Tutup Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6">
                <!-- Deskripsi Ekstrakurikuler -->
                <p id="ekskul-modal-desc" class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/60">
                    Deskripsi ekstrakurikuler...
                </p>

                <!-- Box Pembina Ekstrakurikuler -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                        <span>👔</span>
                        <span>Pembina Ekstrakurikuler</span>
                    </h4>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-50/60 via-white to-sky-50/40 dark:from-slate-800/80 dark:via-slate-900/80 dark:to-slate-800/50 border border-indigo-200/60 dark:border-slate-700/60 flex items-center gap-4 shadow-xs">
                        <div id="ekskul-modal-pembina-avatar-box" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden shrink-0 border-2 border-white dark:border-slate-700 shadow-md bg-indigo-600 text-white flex items-center justify-center font-heading font-black text-xl">
                            <!-- Image or Initials injected via JS -->
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 uppercase tracking-wider mb-1">
                                Guru Pembina Utama
                            </span>
                            <h5 id="ekskul-modal-pembina-name" class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white truncate">
                                Nama Guru Pembina
                            </h5>
                            <p id="ekskul-modal-pembina-nip" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                NIP: -
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Box Daftar Siswa Anggota -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <span>👥</span>
                            <span>Daftar Siswa Anggota</span>
                        </h4>
                        <span id="ekskul-modal-member-count" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            0 Siswa Terdaftar
                        </span>
                    </div>

                    <div id="ekskul-modal-students-container" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden bg-white dark:bg-slate-900/50 shadow-2xs">
                        <!-- Table or Empty state injected via JS -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-[11px] text-slate-400">Terintegrasi dengan SIAKAD & Sistem Presensi Ekskul</span>
                <button type="button" onclick="closeEkskulDetailModal()" class="px-5 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== 8. PEMBIASAAN POSITIF HARIAN SISWA ==================== -->
    <section id="pembiasaan" class="py-12 sm:py-16 md:py-24 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white relative overflow-hidden border-b border-indigo-900/60">
        <!-- Background Glow Orbs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 backdrop-blur-md mb-3">
                    🌱 Pembiasaan Positif Harian Siswa
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-4xl text-white tracking-tight">
                    7 Kebiasaan Anak Indonesia Hebat & Karakter Islami
                </h2>
                <p class="text-xs sm:text-sm text-indigo-200 mt-2 leading-relaxed">
                    Membina kedisiplinan hidup, keteguhan ibadah, kebiasaan literasi, dan kepedulian sosial yang dipantau setiap hari secara terpadu melalui aplikasi mandiri SIS-SMA1LE.
                </p>
            </div>

            <!-- Live Stats Row From Database -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10 max-w-4xl mx-auto">
                <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-2xl shrink-0">
                        📖
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-300" id="live-stat-tahfidz">
                            {{ number_format($habitStats['total_tahfidz_records'] ?? $habitStats['tahfidz_records'] ?? 0) }}+
                        </div>
                        <div class="text-xs text-indigo-200 font-medium">Catatan Tahfidz Qur'an Siswa</div>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-2xl shrink-0">
                        🕌
                    </div>
                    <div>
                        <div class="text-2xl font-black text-indigo-300" id="live-stat-worship">
                            {{ number_format($habitStats['total_worship_records'] ?? $habitStats['worship_attendance'] ?? 0) }}+
                        </div>
                        <div class="text-xs text-indigo-200 font-medium">Presensi Shalat Berjamaah</div>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-2xl shrink-0">
                        🛡️
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-300" id="live-stat-dailylogs">
                            {{ number_format($habitStats['daily_logs'] ?? 0) }}+
                        </div>
                        <div class="text-xs text-indigo-200 font-medium">Jurnal Harian Karakter Pelajar</div>
                    </div>
                </div>
            </div>

            <!-- 7 Kebiasaan Anak Indonesia Hebat Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-10">
                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🌅</div>
                    <h4 class="text-xs font-black text-white">Bangun Pagi</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Disiplin waktu sebelum fajar</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🤲</div>
                    <h4 class="text-xs font-black text-white">Beribadah</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Shalat 5 waktu & berjamaah</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🏃‍♂️</div>
                    <h4 class="text-xs font-black text-white">Berolahraga</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Jasmani bugar & energik</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">📖</div>
                    <h4 class="text-xs font-black text-white">Gemar Belajar</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Literasi 15 menit & Deep Learning</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🥗</div>
                    <h4 class="text-xs font-black text-white">Makan Sehat</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Gizi seimbang & higienis</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🤝</div>
                    <h4 class="text-xs font-black text-white">Bermasyarakat</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">5S, empati & gotong royong</p>
                </div>

                <div class="p-4 rounded-3xl bg-white/5 hover:bg-white/10 border border-white/10 text-center transition-all hover:-translate-y-1">
                    <div class="text-2xl mb-1.5">🌙</div>
                    <h4 class="text-xs font-black text-white">Tidur Cepat</h4>
                    <p class="text-[10px] text-indigo-200 mt-1">Manajemen gawai & pulih</p>
                </div>
            </div>

            <!-- Transparency & Parental Integration Note -->
            <div class="p-5 rounded-2xl bg-white/5 border border-white/10 text-center max-w-2xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-left text-xs text-indigo-200">
                    <strong class="text-white block font-bold">Terhubung ke Portal Orang Tua:</strong>
                    Setiap pencapaian tahfidz, kehadiran shalat, dan kendali kebiasaan harian dapat dipantau langsung oleh orang tua tanpa login.
                </div>
                <a href="{{ route('parent.portal') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shrink-0 transition">
                    Cek Portal Ortu →
                </a>
            </div>

        </div>
    </section>

    <!-- ==================== 9. KLASEMEN & TOREHAN PRESTASI SISWA ==================== -->
    <section id="prestasi" class="py-12 sm:py-16 md:py-24 relative bg-slate-100/60 dark:bg-slate-900/40 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800 mb-2">
                    🏆 Klasemen & Torehan Prestasi Siswa
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                    Rangking Prestasi Siswa (Peringkat 1 - 10)
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                    Apresiasi dedikasi dan keaktifan peserta didik terbaik SMAN 1 Lengkong berdasarkan akumulasi perolehan poin penghargaan dan torehan prestasi terverifikasi.
                </p>

                <div class="inline-flex p-1.5 rounded-2xl bg-slate-200/80 dark:bg-slate-800/80 border border-slate-300/60 dark:border-slate-700/60 mt-6 shadow-inner gap-1">
                    <button type="button" onclick="switchPrestasiTab('leaderboard')" id="tab-btn-leaderboard" class="px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-950 shadow-amber-500/25">
                        <span>🏆 Klasemen Top 10</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-950/15 text-slate-950 text-[10px] font-black">10 Terbaik</span>
                    </button>
                    <button type="button" onclick="switchPrestasiTab('recent')" id="tab-btn-recent" class="px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-2">
                        <span>📜 Riwayat Prestasi Terbaru</span>
                    </button>
                </div>
            </div>

            <!-- VIEW 1: LEADERBOARD TOP 10 -->
            <div id="prestasi-leaderboard-view" class="space-y-10">
                @php
                    $student1 = $topStudents->get(0);
                    $student2 = $topStudents->get(1);
                    $student3 = $topStudents->get(2);
                    $remainingStudents = $topStudents->slice(3);
                    $maxPoints = $topStudents->max('achievements_sum_points') ?: 100;
                @endphp

                <!-- Podium Top 3 Highlight -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <!-- JUARA 2 (PERAK 🥈) -->
                    <div class="order-2 md:order-1 glass-card p-6 rounded-3xl border border-slate-300/80 dark:border-slate-700/80 bg-gradient-to-b from-slate-100/70 via-white/50 to-white/90 dark:from-slate-800/60 dark:via-slate-900/60 dark:to-slate-950/90 shadow-lg hover:shadow-2xl transition-all duration-300 text-center relative overflow-hidden group">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 mb-4 shadow-sm">
                            🥈 JUARA 2 • MEDALI PERAK
                        </div>
                        <div class="relative mx-auto w-20 h-20 rounded-full ring-4 ring-slate-300 dark:ring-slate-600 shadow-lg flex items-center justify-center font-heading font-black text-2xl text-slate-800 dark:text-white bg-gradient-to-tr from-slate-200 via-slate-100 to-slate-300 dark:from-slate-700 dark:via-slate-800 dark:to-slate-700">
                            <span>{{ $student2 ? ($student2->initials ?: substr($student2->name, 0, 2)) : '2' }}</span>
                            <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white flex items-center justify-center text-xs font-black ring-2 ring-white dark:ring-slate-900 shadow">
                                🥈
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white mt-4 tracking-tight line-clamp-1">
                            {{ $student2?->name ?? 'Siswa Berprestasi' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $student2?->schoolClass?->name ?? 'Kelas Unggulan' }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 mt-5 pt-4 border-t border-slate-200/60 dark:border-slate-800/60">
                            <div class="p-2.5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Poin</span>
                                <strong class="font-heading font-black text-lg text-slate-800 dark:text-slate-100">
                                    {{ (int)($student2?->achievements_sum_points ?? 0) }}
                                </strong>
                            </div>
                            <div class="p-2.5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prestasi</span>
                                <strong class="font-heading font-black text-lg text-slate-800 dark:text-slate-100">
                                    {{ $student2?->achievements_count ?? 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- JUARA 1 (EMAS 🥇) - CENTER PODIUM -->
                    <div class="order-1 md:order-2 glass-card p-6 sm:p-7 rounded-3xl border-2 border-amber-400 dark:border-amber-500 bg-gradient-to-b from-amber-500/15 via-amber-500/5 to-white/95 dark:from-amber-950/50 dark:via-slate-900/90 dark:to-slate-950 shadow-2xl shadow-amber-500/20 hover:shadow-amber-500/30 transition-all duration-300 text-center relative overflow-hidden md:-translate-y-3 group">
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 shadow-md shadow-amber-500/30 mb-4 animate-pulse">
                            👑 JUARA 1 • MEDALI EMAS
                        </div>
                        <div class="relative mx-auto w-24 h-24 rounded-full ring-4 ring-amber-400 dark:ring-amber-400 shadow-xl shadow-amber-400/30 flex items-center justify-center font-heading font-black text-3xl text-slate-950 bg-gradient-to-tr from-amber-300 via-yellow-200 to-amber-400">
                            <span>{{ $student1 ? ($student1->initials ?: substr($student1->name, 0, 2)) : '1' }}</span>
                            <span class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-black ring-2 ring-white dark:ring-slate-900 shadow">
                                🥇
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white mt-4 tracking-tight line-clamp-1">
                            {{ $student1?->name ?? 'Siswa Berprestasi Terbaik' }}
                        </h3>
                        <p class="text-xs sm:text-sm font-semibold text-amber-700 dark:text-amber-400 mt-0.5">
                            {{ $student1?->schoolClass?->name ?? 'Kelas Unggulan' }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 mt-5 pt-4 border-t border-amber-300/40 dark:border-amber-500/30">
                            <div class="p-3 rounded-2xl bg-amber-400/20 dark:bg-amber-950/40 border border-amber-400/40 dark:border-amber-600/40 text-center">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Total Poin</span>
                                <strong class="font-heading font-black text-2xl text-amber-600 dark:text-amber-400">
                                    {{ (int)($student1?->achievements_sum_points ?? 0) }}
                                </strong>
                            </div>
                            <div class="p-3 rounded-2xl bg-amber-400/20 dark:bg-amber-950/40 border border-amber-400/40 dark:border-amber-600/40 text-center">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Prestasi</span>
                                <strong class="font-heading font-black text-2xl text-amber-600 dark:text-amber-400">
                                    {{ $student1?->achievements_count ?? 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- JUARA 3 (PERUNGGU 🥉) -->
                    <div class="order-3 glass-card p-6 rounded-3xl border border-amber-600/40 dark:border-amber-700/40 bg-gradient-to-b from-amber-700/10 via-white/50 to-white/90 dark:from-amber-950/30 dark:via-slate-900/60 dark:to-slate-950/90 shadow-lg hover:shadow-2xl transition-all duration-300 text-center relative overflow-hidden group">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800 mb-4 shadow-sm">
                            🥉 JUARA 3 • MEDALI PERUNGGU
                        </div>
                        <div class="relative mx-auto w-20 h-20 rounded-full ring-4 ring-amber-600/50 dark:ring-amber-600/50 shadow-lg flex items-center justify-center font-heading font-black text-2xl text-amber-900 dark:text-amber-100 bg-gradient-to-tr from-amber-200 via-amber-100 to-amber-300 dark:from-amber-900 dark:via-amber-800 dark:to-amber-900">
                            <span>{{ $student3 ? ($student3->initials ?: substr($student3->name, 0, 2)) : '3' }}</span>
                            <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-amber-700 text-white flex items-center justify-center text-xs font-black ring-2 ring-white dark:ring-slate-900 shadow">
                                🥉
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white mt-4 tracking-tight line-clamp-1">
                            {{ $student3?->name ?? 'Siswa Berprestasi' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $student3?->schoolClass?->name ?? 'Kelas Unggulan' }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 mt-5 pt-4 border-t border-slate-200/60 dark:border-slate-800/60">
                            <div class="p-2.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/60">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Total Poin</span>
                                <strong class="font-heading font-black text-lg text-amber-700 dark:text-amber-300">
                                    {{ (int)($student3?->achievements_sum_points ?? 0) }}
                                </strong>
                            </div>
                            <div class="p-2.5 rounded-2xl bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prestasi</span>
                                <strong class="font-heading font-black text-lg text-slate-800 dark:text-slate-100">
                                    {{ $student3?->achievements_count ?? 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Peringkat 4 s/d 10 -->
                <div class="glass-card rounded-3xl border border-slate-200/80 dark:border-slate-800/80 overflow-hidden shadow-xl">
                    <div class="p-5 sm:p-6 bg-slate-50/70 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white flex items-center gap-2">
                                <span>🎖️ Klasemen Lanjutan (Peringkat 4 s/d 10)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Daftar peserta didik berprestasi urutan ke-4 hingga ke-10 berdasarkan total akumulasi poin prestasi.
                            </p>
                        </div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            Kuota: <span class="text-amber-600 dark:text-amber-400 font-black">Top 10 Terbaik</span>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-200/60 dark:divide-slate-800/60">
                        @forelse($remainingStudents as $index => $student)
                            @php
                                $rankNumber = $index + 4;
                                $points = (int)($student->achievements_sum_points ?? 0);
                                $progressPct = $maxPoints > 0 ? min(100, round(($points / $maxPoints) * 100)) : 50;
                            @endphp
                            <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-heading font-black text-sm text-slate-700 dark:text-slate-200 shrink-0">
                                        #{{ $rankNumber }}
                                    </div>
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-heading font-bold text-sm flex items-center justify-center shrink-0 shadow-sm">
                                        {{ $student->initials ?: substr($student->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-heading font-bold text-sm sm:text-base text-slate-900 dark:text-white truncate">
                                                {{ $student->name }}
                                            </h4>
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 shrink-0">
                                                {{ $student->schoolClass?->name ?? 'Kelas Aktif' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            NISN: {{ $student->nisn ?? $student->nis ?? '-' }} • {{ $student->achievements_count ?? 0 }} Catatan Prestasi Terdaftar
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 self-end md:self-auto shrink-0 w-full md:w-auto justify-between md:justify-end pt-2 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800/40">
                                    <div class="hidden lg:block w-32">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                            <span>Akumulasi Poin</span>
                                            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $progressPct }}%</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-yellow-500 transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                                        </div>
                                    </div>

                                    <div class="px-3.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200/60 dark:border-amber-800/60 text-center shrink-0">
                                        <span class="block text-[10px] uppercase font-bold text-amber-700 dark:text-amber-400">Total</span>
                                        <strong class="font-heading font-black text-sm sm:text-base text-amber-600 dark:text-amber-400">
                                            ⭐ {{ $points }} Pts
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-500 text-xs">
                                Belum ada data perolehan poin tambahan untuk peringkat 4-10.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-4 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-200/60 dark:border-slate-800/60 text-center text-xs text-slate-500">
                        💡 Data klasemen diperbarui real-time mengikuti verifikasi pembina kesiswaan di aplikasi SIS-SMA1LE.
                    </div>
                </div>
            </div>

            <!-- VIEW 2: RIWAYAT PRESTASI TERBARU -->
            <div id="prestasi-recent-view" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($achievements as $achieve)
                        <div class="glass-card p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-amber-400/20 text-amber-600 dark:text-amber-400 border border-amber-400/40">
                                        🥇 {{ $achieve->rank ?? 'Juara Unggulan' }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        {{ $achieve->date ? \Carbon\Carbon::parse($achieve->date)->translatedFormat('d M Y') : '2026' }}
                                    </span>
                                </div>

                                <h3 class="font-heading font-bold text-slate-900 dark:text-white text-base leading-snug mb-2">
                                    {{ $achieve->name }}
                                </h3>

                                <p class="text-xs text-slate-600 dark:text-slate-400 mb-4 leading-relaxed line-clamp-2">
                                    {{ $achieve->description ?? 'Kompetisi dan keikutsertaan kegiatan yang diikuti siswa berprestasi SMAN 1 Lengkong.' }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between text-xs">
                                <div class="truncate">
                                    <span class="text-slate-400 block text-[10px]">Peraih Prestasi</span>
                                    <strong class="text-slate-800 dark:text-slate-200 truncate block">
                                        {{ $achieve->student?->name ?? 'Siswa Berprestasi' }}
                                    </strong>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[11px] font-bold border border-indigo-200 dark:border-indigo-800 shrink-0">
                                    {{ $achieve->level ?? 'Tingkat Sekolah' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                            Belum ada riwayat prestasi baru yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </section>

    <!-- ==================== 10. TRANSFORMASI DIGITAL TERPADU ==================== -->
    <section id="ekosistem-sis" class="py-12 sm:py-16 md:py-24 bg-gradient-to-b from-slate-900 via-indigo-950/70 to-slate-950 text-white relative overflow-hidden border-b border-indigo-900/60">
        <div class="absolute inset-0 bg-radial-[at_top] from-indigo-500/15 via-transparent to-slate-950/80 pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-16 space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 backdrop-blur-md uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Transformasi Digital Terpadu
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-4xl lg:text-5xl tracking-tight text-white leading-tight">
                    Ekosistem Aplikasi Mandiri <span class="bg-gradient-to-r from-indigo-300 via-sky-300 to-emerald-300 bg-clip-text text-transparent">SIS-SMA1LE</span>
                </h2>
                <p class="text-xs sm:text-sm md:text-base text-slate-300 leading-relaxed font-normal">
                    Platform tata kelola sekolah mandiri berstandar nasional yang mengintegrasikan Kurikulum Nasional, Kesiswaan Berkarakter, Perpustakaan Interaktif, dan Administrasi Ketatausahaan tanpa ketergantungan sistem eksternal.
                </p>
            </div>

            <!-- 7 Grand Pillars of SIS-SMA1LE Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6 items-stretch mb-12">
                
                <!-- Pilar 1: KURIKULUM & LMS TERPADU -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-indigo-500/30 hover:border-indigo-400 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                📘
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 uppercase tracking-wider">
                                KURIKULUM
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            Kurikulum LMS: Pembelajaran & Rapor Kurnas
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Implementasi standar resmi <strong>Permendikdasmen No. 13 Tahun 2025</strong> & Panduan e-Rapor SMA Versi 2025 untuk pembelajaran bermakna (Deep Learning).
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">LMS & Modul Ajar:</strong>
                                    <p class="text-[11px] text-slate-400">Modul ajar interaktif, bank soal CBT anti-cheat, jurnal mengajar, dan supervisi akademik.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Rapor Kurnas 2025:</strong>
                                    <p class="text-[11px] text-slate-400">Penilaian KKTP, Formatif, Sumatif Fase E & F, Cetak Cover, Identitas, Rapor, Buku Induk, & Transkrip.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('lms.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-md shadow-indigo-600/30">
                            Buka LMS →
                        </a>
                        <a href="{{ route('rapor_kurnas.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-300 border border-indigo-500/30 font-bold text-xs transition">
                            Rapor Kurnas
                        </a>
                    </div>
                </div>

                <!-- Pilar 2: KESISWAAN & PRESENSI MULTI-METODE -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-emerald-500/30 hover:border-emerald-400 transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                ⭐
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 uppercase tracking-wider">
                                KESISWAAN
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            Presensi & Ketertiban Siswa
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Presensi harian multi-metode real-time, izin keluar/pulang cepat terpadu, penugasan petugas piket, dan pembinaan karakter disiplin.
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Presensi 5 Metode:</strong>
                                    <p class="text-[11px] text-slate-400">RFID IoT Kartu, QR Scanner, Selfie GPS, Manual Rombel, & Izin Sakit.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Izin Pulang & Keluar Sekolah:</strong>
                                    <p class="text-[11px] text-slate-400">Surat izin pulang cepat, surat izin keluar sementara, jadwal piket harian, dan cetak dokumen resmi ber-Kop.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Tata Tertib & Ekstrakurikuler:</strong>
                                    <p class="text-[11px] text-slate-400">Poin pembinaan, verifikasi prestasi, presensi ekskul & pembina.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('attendance.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/30">
                            Presensi Online →
                        </a>
                        <a href="{{ route('extracurricular.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition">
                            Ekstrakurikuler
                        </a>
                    </div>
                </div>

                <!-- Pilar 3: SPMB BARU & SELEKSI TKA -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-amber-500/30 hover:border-amber-400 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                🎓
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase tracking-wider">
                                SPMB & TKA
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            SPMB Baru & Seleksi TKA
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Sistem Penerimaan Murid Baru digital terpadu per gelombang dengan seleksi Tes Kemampuan Akademik (TKA) dan verifikasi berkas lengkap.
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Gelombang & Kuota Fleksibel:</strong>
                                    <p class="text-[11px] text-slate-400">Jadwal gelombang buka/tutup, jalur Zonasi, Prestasi, Afirmasi, Mutasi, dan TKA.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Upload Berkas Lengkap:</strong>
                                    <p class="text-[11px] text-slate-400">KTP Ortu, KK, Akte Kelahiran, PIP/KIP/PKH, Rapor SMP Semester 1-5, dan Sertifikat TKA.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('spmb.public.register') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition shadow-md shadow-amber-600/30">
                            Daftar Online →
                        </a>
                        <a href="{{ route('spmb.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 border border-amber-500/30 font-bold text-xs transition">
                            Info Gelombang
                        </a>
                    </div>
                </div>

                <!-- Pilar 4: BIMBINGAN KONSELING 360° & PSIKOTES -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-pink-500/30 hover:border-pink-400 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-pink-500/20 text-pink-400 border border-pink-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                🧠
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-pink-500/20 text-pink-300 border border-pink-500/40 uppercase tracking-wider">
                                BK 360°
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            Bimbingan Konseling 360°
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Layanan holistik pendampingan siswa, asesmen kebutuhan (AKPD/AUM), psikotes minat bakat, karir kuliah, dan penanganan kasus terpadu.
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Konseling & Sosiometri:</strong>
                                    <p class="text-[11px] text-slate-400">Sesi konseling individu/kelompok, dinamika relasi kelas, dan deteksi dini masalah.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Home Visit & Panggilan Ortu:</strong>
                                    <p class="text-[11px] text-slate-400">Arsip resmi berita acara kunjungan rumah, pemanggilan orang tua, dan konferensi kasus.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('bk.dashboard') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs transition shadow-md shadow-pink-600/30">
                            Dashboard BK →
                        </a>
                        <a href="{{ route('bk.psychological.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-pink-300 border border-pink-500/30 font-bold text-xs transition">
                            Psikotes & Karir
                        </a>
                    </div>
                </div>

                <!-- Pilar 5: PERPUSTAKAAN DIGITAL & E-BOOK 3D FLIP -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-cyan-500/30 hover:border-cyan-400 transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                📚
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 uppercase tracking-wider">
                                PERPUSTAKAAN
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            Perpustakaan: Literasi & E-Book 3D Flip
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Koleksi buku teks dan referensi lengkap dengan E-Reader 3D Flip realistis ramah kuota, barcode peminjaman, serta kios buku tamu pemustaka mandiri.
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Koleksi Cetak & Digital:</strong>
                                    <p class="text-[11px] text-slate-400">Katalog terpadu, stok buku riil, dan pembaca flip per lembar tanpa unduh paksa.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Kios Buku Tamu Pemustaka:</strong>
                                    <p class="text-[11px] text-slate-400">Terminal presensi pengunjung perpustakaan mandiri tanpa antrean.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('library.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition shadow-md shadow-cyan-600/30">
                            Jelajah Buku →
                        </a>
                        <a href="{{ route('library.guest_book.index') }}" target="_blank" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition">
                            Kios Perpus ↗
                        </a>
                    </div>
                </div>

                <!-- Pilar 6: PEMBIASAAN 7 KARAKTER & TAHFIDZ AL-QUR'AN -->
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-teal-500/30 hover:border-teal-400 transition-all duration-300 hover:shadow-2xl hover:shadow-teal-500/20 flex flex-col justify-between group backdrop-blur-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                🕌
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-teal-500/20 text-teal-300 border border-teal-500/40 uppercase tracking-wider">
                                KARAKTER & RELIGI
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            7 Karakter & Tahfidz Qur'an
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Penguatan nilai spiritual dan budi pekerti: setoran tahfidz Juz 30, mutaba'ah yaumiyah, presensi shalat dhuha/dzuhur berjamaah, dan keputrian.
                        </p>
                        
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <li class="flex items-start gap-2">
                                <span class="text-teal-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Tahfidz Juz 30 & Mutaba'ah:</strong>
                                    <p class="text-[11px] text-slate-400">Monitoring progres per surat/ayat Al-Qur'an dan sertifikasi hafalan.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-teal-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Presensi Ibadah & Keputrian:</strong>
                                    <p class="text-[11px] text-slate-400">Shalat dhuha pagi, dzuhur/jum'at berjamaah, dan materi keputrian mingguan.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('habits.journal') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition shadow-md shadow-teal-600/30">
                            Jurnal Karakter →
                        </a>
                        <a href="{{ route('tahfidz.index') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-300 border border-teal-500/30 font-bold text-xs transition">
                            Hafalan Tahfidz
                        </a>
                    </div>
                </div>

                {{-- Pilar 7: LAYANAN PUBLIK TATA USAHA & EKOSISTEM KIOS (Col-span 2 on wide screens) --}}
                <div class="p-6 rounded-3xl bg-slate-900/80 border border-sky-500/30 hover:border-sky-400 transition-all duration-300 hover:shadow-2xl hover:shadow-sky-500/20 flex flex-col justify-between group backdrop-blur-md md:col-span-2">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-400 border border-sky-500/30 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                🏢
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-sky-500/20 text-sky-300 border border-sky-500/40 uppercase tracking-wider">
                                TATA USAHA & KIOS PUBLIK
                            </span>
                        </div>
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white mb-2">
                            Ketatausahaan & SIP-Ortu: Layanan Tata Usaha & Kios
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Layanan administrasi ketatausahaan terpadu (TU Terpadu), persuratan kedinasan, arsip dossier, display TV lobby, kios resepsionis tamu selfie & TTD digital, serta portal orang tua tanpa login.
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-300 mb-6 border-t border-slate-800 pt-4">
                            <div class="flex items-start gap-2">
                                <span class="text-sky-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Ekosistem Kios Mandiri:</strong>
                                    <p class="text-[11px] text-slate-400">Display TV Lobby real-time, Kios Buku Tamu selfie webcam & tanda tangan digital, dan Kios Perpus.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-sky-400 mt-0.5 font-bold">✓</span>
                                <div>
                                    <strong class="text-white">Portal Publik Orang Tua (SIP-Ortu):</strong>
                                    <p class="text-[11px] text-slate-400">Akses instan tanpa login hanya dengan memasukkan NISN anak untuk cek kehadiran, izin, dan poin karakter.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-2">
                        <a href="{{ route('kiosk.index') }}" target="_blank" class="flex-1 min-w-[130px] text-center py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-md shadow-indigo-600/30">
                            📺 Kios TV Lobby ↗
                        </a>
                        <a href="{{ route('guests.kiosk') }}" target="_blank" class="flex-1 min-w-[130px] text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/30">
                            👥 Kios Buku Tamu ↗
                        </a>
                        <a href="{{ route('parent.portal') }}" class="flex-1 min-w-[140px] text-center py-2 px-3 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition shadow-md shadow-sky-600/30">
                            👨‍👩‍👧 Portal Orang Tua →
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ==================== 11. AKSES FLEKSIBEL BERDASARKAN KEBUTUHAN ==================== -->
    <section id="layanan-peran" class="py-12 sm:py-16 md:py-24 bg-white dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 mb-3">
                    ⚡ Akses Fleksibel Berdasarkan Kebutuhan
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                    Pusat Layanan Terpadu Berbasis Peran
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Pilih kategori Anda untuk mengakses layanan akademik, panduan, dan instrumen yang tepat dengan cepat.
                </p>
            </div>

            <!-- Role Selector Tabs -->
            <div class="flex items-center justify-center gap-2 mb-8 no-scrollbar overflow-x-auto pb-1" id="role-hub-tabs">
                <button type="button" onclick="switchRoleHub('siswa', this)" class="role-tab-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-indigo-600 text-white shadow-md shadow-indigo-600/25 transition whitespace-nowrap">
                    🎓 Siswa
                </button>
                <button type="button" onclick="switchRoleHub('ortu', this)" class="role-tab-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition whitespace-nowrap">
                    👨‍👩‍👧 Orang Tua / Wali
                </button>
                <button type="button" onclick="switchRoleHub('guru', this)" class="role-tab-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition whitespace-nowrap">
                    👨‍🏫 Guru & Tenaga Kependidikan
                </button>
                <button type="button" onclick="switchRoleHub('tamu', this)" class="role-tab-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition whitespace-nowrap">
                    🌐 Alumni & Masyarakat
                </button>
            </div>

            <!-- Role Pane: SISWA -->
            <div id="role-pane-siswa" class="role-pane grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800">
                    <span class="text-2xl block mb-2">📚</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">LMS & Modul Ajar</h4>
                    <p class="text-xs text-slate-500 mb-3">Unduh materi pelajaran, rangkuman, dan diskusi interaktif.</p>
                    <a href="{{ route('lms.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka LMS →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800">
                    <span class="text-2xl block mb-2">🎯</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">CBT Ujian & Kuis</h4>
                    <p class="text-xs text-slate-500 mb-3">Kerjakan asesmen sumatif dan kuis formatif berbatas waktu.</p>
                    <a href="{{ route('lms.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Masuk Ujian →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800">
                    <span class="text-2xl block mb-2">📸</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Presensi Mandiri</h4>
                    <p class="text-xs text-slate-500 mb-3">Presensi selfie kamera langsung dengan validasi radius GPS sekolah.</p>
                    <a href="{{ route('attendance.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Ambil Presensi →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800">
                    <span class="text-2xl block mb-2">📖</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Tahfidz Qur'an</h4>
                    <p class="text-xs text-slate-500 mb-3">Pantau hafalan Juz 30 dan rekap pembiasaan ibadah harian.</p>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buku Tahfidz →</a>
                </div>
            </div>

            <!-- Role Pane: ORTU -->
            <div id="role-pane-ortu" class="role-pane hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl glass-card border border-emerald-500/30">
                    <span class="text-2xl block mb-2">🔍</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Cek Absensi Harian</h4>
                    <p class="text-xs text-slate-500 mb-3">Pantau jam kedatangan dan jam pulang putra-putri Anda secara live.</p>
                    <a href="{{ route('parent.portal') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Buka Portal Ortu →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-emerald-500/30">
                    <span class="text-2xl block mb-2">📜</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Cetak e-Raport</h4>
                    <p class="text-xs text-slate-500 mb-3">Unduh dan cetak rekap nilai capaian kompetensi semester siswa.</p>
                    <a href="{{ route('parent.portal') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Cek Nilai →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-emerald-500/30">
                    <span class="text-2xl block mb-2">⚖️</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Buku Disiplin & Prestasi</h4>
                    <p class="text-xs text-slate-500 mb-3">Pantau poin penghargaan prestasi dan catatan kedisiplinan siswa.</p>
                    <a href="{{ route('parent.portal') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Lihat Catatan →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-emerald-500/30">
                    <span class="text-2xl block mb-2">💬</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Konseling & Wali Kelas</h4>
                    <p class="text-xs text-slate-500 mb-3">Hubungi wali kelas dan guru BK untuk konsultasi perkembangan anak.</p>
                    <a href="#kontak" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Kontak Sekolah →</a>
                </div>
            </div>

            <!-- Role Pane: GURU -->
            <div id="role-pane-guru" class="role-pane hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl glass-card border border-indigo-500/30">
                    <span class="text-2xl block mb-2">🤖</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">LMS & CBT Ujian</h4>
                    <p class="text-xs text-slate-500 mb-3">Kelola materi kurikulum, bank soal CBT anti-cheat, dan asisten AI pembelajaran.</p>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Masuk LMS Guru →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-indigo-500/30">
                    <span class="text-2xl block mb-2">📋</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Presensi Kelas & Mapel</h4>
                    <p class="text-xs text-slate-500 mb-3">Input absensi jam KBM terpadu dan monitoring kehadiran siswa 5 metode.</p>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Input Presensi →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-indigo-500/30">
                    <span class="text-2xl block mb-2">📊</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Rapor Kurnas 2025</h4>
                    <p class="text-xs text-slate-500 mb-3">Input KKTP, asesmen formatif/sumatif Fase E/F, dan cetak rapor terstandar.</p>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Kelola Rapor Kurnas →</a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-indigo-500/30">
                    <span class="text-2xl block mb-2">📖</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-sm mb-1">Perpustakaan & Jurnal</h4>
                    <p class="text-xs text-slate-500 mb-3">Akses buku teks digital 3D Flip, referensi ajar, dan catat jurnal harian mengajar.</p>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka Pustaka & Jurnal →</a>
                </div>
            </div>

            <!-- Role Pane: TAMU / ALUMNI -->
            <div id="role-pane-tamu" class="role-pane hidden grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto">
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800 text-center hover:border-indigo-500/50 hover:shadow-xl transition-all duration-300">
                    <span class="text-3xl block mb-2">📺</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base mb-1">Display Informasi Kios TV</h4>
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">Siaran display monitor interaktif lobby sekolah secara real-time.</p>
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-md shadow-indigo-600/20">
                        <span>Buka Kios TV</span>
                        <span>↗</span>
                    </a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800 text-center hover:border-emerald-500/50 hover:shadow-xl transition-all duration-300">
                    <span class="text-3xl block mb-2">👨‍👩‍👧</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base mb-1">Portal Publik Orang Tua</h4>
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">Pantau jam presensi, riwayat nilai, dan catatan siswa cukup dengan nomor NISN.</p>
                    <a href="{{ route('parent.portal') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-md shadow-emerald-600/20">
                        <span>Cek Portal Ortu</span>
                        <span>→</span>
                    </a>
                </div>
                <div class="p-5 rounded-3xl glass-card border border-slate-200 dark:border-slate-800 text-center hover:border-amber-500/50 hover:shadow-xl transition-all duration-300">
                    <span class="text-3xl block mb-2">📖</span>
                    <h4 class="font-heading font-bold text-slate-900 dark:text-white text-base mb-1">Katalog Pustaka 3D</h4>
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">Eksplorasi koleksi literasi, modul ajar, dan e-book interaktif flip HTML.</p>
                    <a href="{{ route('library.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition shadow-md shadow-amber-600/20">
                        <span>Buka Katalog</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ==================== 12. INFORMASI & LAYANAN PUBLIK ==================== -->
    <section id="kontak" class="py-12 sm:py-16 md:py-24 bg-slate-100/60 dark:bg-slate-900/40 border-t border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Information & Contacts -->
                <div class="lg:col-span-5 space-y-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                        📍 Informasi & Layanan Publik
                    </span>

                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">
                        Hubungi Kampus Kami
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Kami senantiasa terbuka untuk silaturahmi, konsultasi bimbingan, kemitraan pendidikan, dan layanan administrasi sekolah ramah masyarakat.
                    </p>

                    <div class="space-y-3.5 text-xs sm:text-sm">
                        <!-- Address -->
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl glass-card border border-slate-200/80 dark:border-slate-800/80">
                            <span class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 text-base">
                                🏢
                            </span>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-heading font-bold">Alamat Resmi</strong>
                                <p class="text-slate-600 dark:text-slate-400 mt-0.5 text-xs">
                                    {{ $settings['school_address'] ?? 'Jl. Raya Lengkong No. 01, Desa Lengkong, Kec. Lengkong, Kabupaten Sukabumi, Jawa Barat 43174' }}
                                </p>
                            </div>
                        </div>

                        <!-- Phone / WA -->
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl glass-card border border-slate-200/80 dark:border-slate-800/80">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-base">
                                📞
                            </span>
                            <div class="flex-1 flex items-center justify-between gap-2">
                                <div>
                                    <strong class="text-slate-900 dark:text-white block font-heading font-bold">Telepon & WhatsApp</strong>
                                    <p class="text-slate-600 dark:text-slate-400 mt-0.5 text-xs">
                                        {{ $settings['school_phone'] ?? '(0266) 123456' }}
                                    </p>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['website_humas_phone'] ?? '6281234567890') }}?text={{ urlencode($settings['website_humas_message'] ?? 'Halo Admin SMAN 1 Lengkong, saya ingin bertanya informasi sekolah.') }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shrink-0 transition">
                                    Chat WA ↗
                                </a>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl glass-card border border-slate-200/80 dark:border-slate-800/80">
                            <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 text-base">
                                ✉️
                            </span>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-heading font-bold">Email Informasi</strong>
                                <p class="text-slate-600 dark:text-slate-400 mt-0.5 text-xs">
                                    {{ $settings['school_email'] ?? 'info@sman1lengkong.sch.id' }}
                                </p>
                            </div>
                        </div>

                        <!-- Jam Layanan -->
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl glass-card border border-slate-200/80 dark:border-slate-800/80">
                            <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 text-base">
                                ⏰
                            </span>
                            <div>
                                <strong class="text-slate-900 dark:text-white block font-heading font-bold">Jam Layanan Pelajar</strong>
                                <p class="text-slate-600 dark:text-slate-400 mt-0.5 text-xs">
                                    {{ $settings['website_service_hours'] ?? 'Senin - Jumat: 06.30 - 15.00 WIB' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Map Embed -->
                <div class="lg:col-span-7">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-800/80 h-96 relative">
                        <iframe 
                            src="https://www.openstreetmap.org/export/embed.html?bbox=106.6988%2C-7.1350%2C106.7088%2C-7.1250&amp;layer=mapnik&amp;marker=-7.13013342752238%2C106.7038263897621" 
                            class="w-full h-full border-0" 
                            title="Peta Lokasi SMAN 1 Lengkong Sukabumi"
                            loading="lazy">
                        </iframe>
                        <div class="absolute bottom-3 left-3 right-3 p-3 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 shadow-md flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📍</span>
                                <div>
                                    <strong class="text-slate-900 dark:text-white block font-bold text-xs">Titik Kampus SMAN 1 Lengkong</strong>
                                    <span class="text-[10px] text-slate-500">-7.130133, 106.703826 (Kecamatan Lengkong)</span>
                                </div>
                            </div>
                            <a href="https://maps.google.com/?q=-7.13013342752238,106.7038263897621" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shrink-0 transition">
                                Buka di Maps ↗
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-800">
        <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 mb-12">
                
                <!-- School Info Brand -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-0.5 flex items-center justify-center">
                            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                                @if(!empty($appLogoUrl))
                                    <img src="{{ $appLogoUrl }}" alt="Logo" class="w-6 h-6 object-contain">
                                @else
                                    <span class="font-heading font-black text-white text-sm">1LE</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-white text-base tracking-tight leading-none">
                                {{ $settings['school_name'] ?? 'SMAN 1 LENGKONG' }}
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-1">Kab. Sukabumi • Jawa Barat</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pusat ekosistem pendidikan cerdas terpadu berlandaskan Kurikulum Nasional 2025. Memadukan presensi cerdas multi-metode, pembelajaran Deep Learning, dan transparansi portal orang tua tanpa batas.
                    </p>
                </div>

                <!-- Navigation Links (Exact 12 Items Order) -->
                <div class="lg:col-span-3 space-y-2.5">
                    <h5 class="font-heading font-bold text-white text-sm">Susunan Menu Website</h5>
                    <ul class="space-y-1.5 text-xs">
                        <li><a href="#sambutan" class="hover:text-white transition">Sambutan Kepala Sekolah</a></li>
                        <li><a href="#profil" class="hover:text-white transition">Profil Lembaga</a></li>
                        <li><a href="#berita" class="hover:text-white transition">Kabar & Warta Sekolah</a></li>
                        <li><a href="#guru-staf" class="hover:text-white transition">Tenaga Pendidik & Kependidikan</a></li>
                        <li><a href="#fasilitas" class="hover:text-white transition">Sarana & Lingkungan Belajar</a></li>
                        <li><a href="#agenda" class="hover:text-white transition">Kalender Akademik</a></li>
                        <li><a href="#ekstrakurikuler" class="hover:text-white transition">Minat, Bakat & Kepemimpinan</a></li>
                        <li><a href="#pembiasaan" class="hover:text-white transition text-emerald-400 font-semibold">Pembiasaan Positif Harian Siswa</a></li>
                        <li><a href="#prestasi" class="hover:text-white transition text-amber-400 font-semibold">Klasemen & Torehan Prestasi Siswa</a></li>
                        <li><a href="#ekosistem-sis" class="hover:text-white transition text-indigo-400 font-semibold">Transformasi Digital Terpadu</a></li>
                        <li><a href="#layanan-peran" class="hover:text-white transition">Akses Fleksibel Berdasarkan Kebutuhan</a></li>
                        <li><a href="#kontak" class="hover:text-white transition">Informasi & Layanan Publik</a></li>
                    </ul>
                </div>

                <!-- Integrated Portals -->
                <div class="lg:col-span-3 space-y-2.5">
                    <h5 class="font-heading font-bold text-white text-sm">Ekosistem Aplikasi SIS-SMA1LE</h5>
                    <ul class="space-y-1.5 text-xs">
                        <li>
                            <a href="#ekosistem-sis" class="hover:text-white transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                <span>Kurikulum: LMS & Rapor Kurnas 2025</span>
                            </a>
                        </li>
                        <li>
                            <a href="#ekosistem-sis" class="hover:text-white transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Kesiswaan: Presensi 5 Metode & Tatib</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('library.index') }}" class="hover:text-white transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Perpustakaan: E-Book & 3D Flip HTML</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('parent.portal') }}" class="hover:text-white transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                <span>Adm. Ketatausahaan & Portal Ortu (SIP)</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kiosk.index') }}" target="_blank" class="hover:text-white transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                <span>Display Kios TV Informasi Lobby ↗</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- External Links -->
                <div class="lg:col-span-2 space-y-2.5">
                    <h5 class="font-heading font-bold text-white text-sm">Tautan Terkait</h5>
                    <ul class="space-y-1.5 text-[11px]">
                        <li><a href="https://kemdikbud.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">Kemdikbudristek RI ↗</a></li>
                        <li><a href="https://disdik.jabarprov.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">Disdik Jawa Barat ↗</a></li>
                        <li><a href="https://sukabumikab.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">Pemkab Sukabumi ↗</a></li>
                        <li><a href="https://siap-ppdb.com" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">PPDB Jawa Barat ↗</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright & Back to Top -->
            <div class="pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-slate-500">
                <p>
                    &copy; {{ date('Y') }} <strong>{{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}</strong>. Seluruh Hak Cipta Dilindungi. Terintegrasi SIAKAD SIS-SMA1LE.
                </p>
                <div class="flex items-center gap-4 text-[11px]">
                    <a href="#hero" class="text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                        <span>Kembali ke Atas</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== NEWS READER MODAL ==================== -->
    <div id="news-modal" class="hidden fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-news-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" onclick="closeNewsModal()"></div>

        <div class="min-h-full flex items-center justify-center p-3 sm:p-6 text-center">
            <div class="relative w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left align-middle shadow-2xl transition-all my-8 flex flex-col max-h-[90vh]">
                <div class="p-4 sm:p-6 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span id="modal-news-category" class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-indigo-600 text-white shadow-xs">
                            Kategori
                        </span>
                        <span id="modal-news-date" class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Tanggal
                        </span>
                    </div>

                    <button type="button" onclick="closeNewsModal()" class="w-8 h-8 rounded-full bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm font-bold transition cursor-pointer" aria-label="Tutup">
                        ✕
                    </button>
                </div>

                <div class="p-5 sm:p-8 overflow-y-auto space-y-6">
                    <h2 id="modal-news-title" class="font-heading font-black text-xl sm:text-3xl text-slate-900 dark:text-white leading-tight">
                        Judul Berita
                    </h2>

                    <div class="flex items-center justify-between gap-3 py-3 border-y border-slate-100 dark:border-slate-800 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">
                                ✍️
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200" id="modal-news-author">Penulis</span>
                                <span class="block text-[10px] text-slate-400">Sivitas Akademika {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">Terverifikasi</span>
                        </div>
                    </div>

                    <div class="rounded-2xl overflow-hidden aspect-video bg-slate-100 dark:bg-slate-800 shadow-md">
                        <img id="modal-news-image" src="" alt="Cover Berita" class="w-full h-full object-cover">
                    </div>

                    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-slate-800/60 border-l-4 border-indigo-600 text-slate-700 dark:text-slate-300 text-xs sm:text-sm font-medium italic leading-relaxed" id="modal-news-excerpt">
                        Ringkasan berita...
                    </div>

                    <div id="modal-news-content" class="news-rich-article text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed space-y-4 font-normal">
                        Konten lengkap...
                    </div>

                    <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Bagikan Berita Ini:</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="shareNewsWhatsApp()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer">
                                <span>💬</span>
                                <span>WhatsApp</span>
                            </button>
                            <button type="button" onclick="copyNewsLink()" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 transition cursor-pointer">
                                <span>🔗</span>
                                <span id="copy-link-text">Salin Tautan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex items-center justify-between shrink-0">
                    <span class="text-[11px] text-slate-400">{{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }} Official News Portal</span>
                    <button type="button" onclick="closeNewsModal()" class="px-5 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white text-xs font-bold transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MOBILE BOTTOM FLOATING DOCK ==================== -->
    <div id="mobile-dock" class="md:hidden fixed bottom-3 left-3 right-3 z-40 bg-slate-900/90 dark:bg-slate-950/90 backdrop-blur-xl border border-slate-800 rounded-2xl p-2 shadow-2xl flex items-center justify-around text-slate-300 bottom-dock-transition">
        <a href="#hero" class="flex flex-col items-center py-1 px-1.5 hover:text-white text-[10px] font-bold">
            <span class="text-base mb-0.5">🏠</span>
            <span>Home</span>
        </a>
        <a href="#ekosistem-sis" class="flex flex-col items-center py-1 px-1.5 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold">
            <span class="text-base mb-0.5">💻</span>
            <span>Apps</span>
        </a>
        <a href="#berita" class="flex flex-col items-center py-1 px-1.5 text-amber-400 hover:text-amber-300 text-[10px] font-bold">
            <span class="text-base mb-0.5">📰</span>
            <span>Berita</span>
        </a>
        <a href="{{ route('parent.portal') }}" class="flex flex-col items-center py-1 px-1.5 text-emerald-400 hover:text-emerald-300 text-[10px] font-bold">
            <span class="text-base mb-0.5">👨‍👩‍👧</span>
            <span>Ortu</span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-1.5 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold">
                <span class="text-base mb-0.5">🚀</span>
                <span>Portal</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center py-1 px-1.5 text-sky-400 hover:text-sky-300 text-[10px] font-bold">
                <span class="text-base mb-0.5">🔑</span>
                <span>Login</span>
            </a>
        @endauth
    </div>

    <!-- ==================== INTERACTIVE JAVASCRIPT ==================== -->
    <script>
        // 1. Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                });
            });
        }



        // 2. Dark / Light Mode Toggle
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        function updateThemeIcons() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon.classList.remove('hidden');
                themeToggleDarkIcon.classList.add('hidden');
            } else {
                themeToggleLightIcon.classList.add('hidden');
                themeToggleDarkIcon.classList.remove('hidden');
            }
        }
        updateThemeIcons();

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                    if (typeof updateMetaThemeColor === 'function') {
                        updateMetaThemeColor(false);
                    }
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                    if (typeof updateMetaThemeColor === 'function') {
                        updateMetaThemeColor(true);
                    }
                }
                updateThemeIcons();
            });
        }

        // 3. Role-Based Service Hub Switcher
        function switchRoleHub(role, btnElement) {
            const panes = document.querySelectorAll('.role-pane');
            const buttons = document.querySelectorAll('.role-tab-btn');

            buttons.forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/25');
                btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
            });

            if (btnElement) {
                btnElement.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/25');
                btnElement.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border');
            }

            panes.forEach(pane => {
                pane.classList.add('hidden');
            });

            const activePane = document.getElementById(`role-pane-${role}`);
            if (activePane) {
                activePane.classList.remove('hidden');
            }
        }

        // 4. Extracurricular Category & Live Search Filters
        let currentEkskulCategory = 'all';

        function filterEkskulCategory(category, btnElement) {
            currentEkskulCategory = category;
            const buttons = document.querySelectorAll('.ekskul-filter-btn');

            buttons.forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
            });

            if (btnElement) {
                btnElement.classList.add('bg-indigo-600', 'text-white');
                btnElement.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border');
            }

            filterEkskulCards();
        }

        function filterEkskulLive() {
            filterEkskulCards();
        }

        function filterEkskulCards() {
            const searchInput = document.getElementById('ekskul-search-input');
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const cards = document.querySelectorAll('.ekskul-card');
            const emptyMsg = document.getElementById('ekskul-empty-msg');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                const cardName = card.getAttribute('data-name') || '';

                const categoryMatches = (currentEkskulCategory === 'all' || cardCat === currentEkskulCategory);
                const queryMatches = (query === '' || cardName.includes(query));

                if (categoryMatches && queryMatches) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyMsg) {
                emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        // 4.1 Ekstrakurikuler Detail Modal (Pembina & Siswa Anggota)
        function openEkskulDetailModal(cardEl) {
            if (!cardEl) return;
            const modal = document.getElementById('ekskul-detail-modal');
            if (!modal) return;

            const title = cardEl.getAttribute('data-ekskul-title') || 'Detail Ekstrakurikuler';
            const category = cardEl.getAttribute('data-ekskul-category') || 'Umum';
            const icon = cardEl.getAttribute('data-ekskul-icon') || '⭐';
            const desc = cardEl.getAttribute('data-ekskul-desc') || '';
            const pembinaName = cardEl.getAttribute('data-pembina-name') || 'Dewan Guru Pembina';
            const pembinaNip = cardEl.getAttribute('data-pembina-nip') || '-';
            const pembinaPhoto = cardEl.getAttribute('data-pembina-photo') || '';
            
            let members = [];
            try {
                members = JSON.parse(cardEl.getAttribute('data-members') || '[]');
            } catch (e) {
                members = [];
            }

            document.getElementById('ekskul-modal-title').textContent = title;
            document.getElementById('ekskul-modal-category').textContent = category;
            document.getElementById('ekskul-modal-icon').textContent = icon;
            document.getElementById('ekskul-modal-desc').textContent = desc;

            // Pembina info
            document.getElementById('ekskul-modal-pembina-name').textContent = pembinaName;
            document.getElementById('ekskul-modal-pembina-nip').textContent = pembinaNip !== '-' ? `NIP: ${pembinaNip}` : 'Status: Guru Pembina Ekstrakurikuler';

            // Pembina avatar/photo
            const avatarBox = document.getElementById('ekskul-modal-pembina-avatar-box');
            if (pembinaPhoto) {
                avatarBox.innerHTML = `<img src="${pembinaPhoto}" alt="${pembinaName}" class="w-full h-full object-cover">`;
            } else {
                const words = pembinaName.trim().split(/\s+/);
                const initials = ((words[0] ? words[0][0] : '') + (words[1] ? words[1][0] : '')).toUpperCase();
                avatarBox.innerHTML = `<span class="tracking-wider">${initials || 'PB'}</span>`;
            }

            // Members info
            const countBadge = document.getElementById('ekskul-modal-member-count');
            countBadge.textContent = `${members.length} Siswa Terdaftar`;

            const studentsContainer = document.getElementById('ekskul-modal-students-container');
            if (members.length === 0) {
                studentsContainer.innerHTML = `
                    <div class="p-8 text-center">
                        <span class="text-3xl block mb-2 opacity-50">📋</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Siswa Anggota Terdaftar</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">Data keanggotaan aktif untuk tahun ajaran berjalan sedang dihimpun oleh koordinator pembina atau loket kesiswaan.</p>
                    </div>
                `;
            } else {
                let rowsHtml = '';
                members.forEach((s, idx) => {
                    const initials = s.name.trim().split(/\s+/).map(w => w[0]).slice(0, 2).join('').toUpperCase();
                    const isEven = idx % 2 === 0;
                    rowsHtml += `
                        <tr class="${isEven ? 'bg-white dark:bg-slate-900/60' : 'bg-slate-50/70 dark:bg-slate-800/40'} border-b border-slate-100 dark:border-slate-800/60 hover:bg-indigo-50/40 dark:hover:bg-slate-800/70 transition-colors">
                            <td class="px-4 py-3 text-center text-slate-400 font-bold text-[11px] w-12">${idx + 1}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-[10px] flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-800">
                                        ${initials || 'SW'}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 dark:text-white text-xs truncate">${s.name}</div>
                                        <div class="text-[10px] text-slate-400">NISN: ${s.nisn}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/70 text-sky-700 dark:text-sky-300 font-bold text-[10px] border border-sky-200 dark:border-sky-800">
                                    ${s.class_name}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    ${s.role}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                studentsContainer.innerHTML = `
                    <div class="overflow-x-auto max-h-64 no-scrollbar">
                        <table class="w-full text-left text-xs">
                            <thead class="sticky top-0 bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-black tracking-wider border-b border-slate-200 dark:border-slate-700 z-10">
                                <tr>
                                    <th class="px-4 py-2.5 text-center w-12">No</th>
                                    <th class="px-4 py-2.5">Nama Siswa</th>
                                    <th class="px-4 py-2.5">Kelas</th>
                                    <th class="px-4 py-2.5 text-right">Peran</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEkskulDetailModal() {
            const modal = document.getElementById('ekskul-detail-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeEkskulDetailModal();
            }
        });

        // 5. Expandable Speech for Headmaster
        function toggleHeadmasterSpeech() {
            const extended = document.getElementById('speech-extended');
            const text = document.getElementById('toggle-speech-text');
            const icon = document.getElementById('toggle-speech-icon');
            if (!extended) return;

            if (extended.classList.contains('hidden')) {
                extended.classList.remove('hidden');
                text.innerText = 'Tampilkan Lebih Sedikit';
                icon.innerText = '↑';
            } else {
                extended.classList.add('hidden');
                text.innerText = 'Baca Selengkapnya';
                icon.innerText = '↓';
            }
        }

        // 6. Auto-hide Mobile Bottom Dock on Scroll Down, Show on Scroll Up
        let lastScrollY = window.scrollY;
        const mobileDock = document.getElementById('mobile-dock');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            if (mobileDock) {
                if (currentScrollY > lastScrollY && currentScrollY > 120) {
                    mobileDock.classList.add('bottom-dock-hidden');
                } else {
                    mobileDock.classList.remove('bottom-dock-hidden');
                }
            }
            lastScrollY = currentScrollY;
        }, { passive: true });

        // 7. Smooth Animated Counters on Scroll
        let countersAnimated = false;
        function animateCounters() {
            if (countersAnimated) return;
            const counters = document.querySelectorAll('.counter-value');
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target') || 0;
                const duration = 1200;
                const stepTime = 30;
                const steps = duration / stepTime;
                const increment = target / steps;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.innerText = target.toLocaleString('id-ID');
                        clearInterval(timer);
                    } else {
                        counter.innerText = Math.floor(current).toLocaleString('id-ID');
                    }
                }, stepTime);
            });
            countersAnimated = true;
        }

        window.addEventListener('scroll', () => {
            const heroStats = document.getElementById('hero');
            if (heroStats) {
                const rect = heroStats.getBoundingClientRect();
                if (rect.top <= window.innerHeight && rect.bottom >= 0) {
                    animateCounters();
                }
            }
        }, { passive: true });
        animateCounters();

        // 8. Interactive News Reader Modal & Filtering
        let currentNewsArticle = null;
        let activeNewsCategory = 'all';

        function openNewsModal(article) {
            currentNewsArticle = article;
            const modal = document.getElementById('news-modal');
            if (!modal || !article) return;

            document.getElementById('modal-news-title').textContent = article.title || 'Kabar Sekolah';
            document.getElementById('modal-news-category').textContent = article.category || 'Berita';
            document.getElementById('modal-news-date').textContent = '🗓️ ' + (article.date || '');
            const publisherInfo = article.created_by_name ? ` (Diterbitkan oleh: ${article.created_by_name}${article.created_by_role ? ' - ' + article.created_by_role : ''})` : '';
            document.getElementById('modal-news-author').textContent = (article.author || 'Humas {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}') + publisherInfo;
            document.getElementById('modal-news-excerpt').textContent = article.excerpt || '';
            
            const contentContainer = document.getElementById('modal-news-content');
            if (contentContainer) {
                const rawContent = article.content || '';
                if (/<[a-z][\s\S]*>/i.test(rawContent)) {
                    contentContainer.innerHTML = rawContent;
                } else {
                    const paragraphs = rawContent
                        .split(/\n\n+/)
                        .map(p => `<p class="leading-relaxed mb-3">${p.replace(/\n/g, '<br>')}</p>`);
                    contentContainer.innerHTML = paragraphs.join('');
                }
            }

            const img = document.getElementById('modal-news-image');
            if (img) {
                const rawImg = article.image || 'images/website/classroom.jpg';
                const imgUrl = rawImg.startsWith('http') ? rawImg : ('/' + rawImg.replace(/^\/+/, ''));
                img.src = imgUrl;
                img.alt = article.title || 'Cover Berita';
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeNewsModal() {
            const modal = document.getElementById('news-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeNewsModal();
            }
        });

        function filterNewsCategory(category, btn) {
            activeNewsCategory = category;
            const buttons = document.querySelectorAll('.news-cat-btn');
            buttons.forEach(b => {
                b.className = 'news-cat-btn px-4 py-2 rounded-xl bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition whitespace-nowrap cursor-pointer';
            });
            if (btn) {
                btn.className = 'news-cat-btn px-4 py-2 rounded-xl bg-indigo-600 text-white shadow-xs transition whitespace-nowrap cursor-pointer';
            }
            filterNewsLive();
        }

        function filterNewsLive() {
            const query = (document.getElementById('news-search-input')?.value || '').trim().toLowerCase();
            const clearBtn = document.getElementById('news-search-clear');
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', query.length === 0);
            }

            const cards = document.querySelectorAll('.news-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category') || '';
                const cardTitle = card.getAttribute('data-title') || '';
                const cardExcerpt = card.getAttribute('data-excerpt') || '';

                const matchesCat = (activeNewsCategory === 'all') || (cardCat.toLowerCase() === activeNewsCategory.toLowerCase());
                const matchesQuery = !query || cardTitle.includes(query) || cardExcerpt.includes(query);

                if (matchesCat && matchesQuery) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const emptyMsg = document.getElementById('news-empty-msg');
            if (emptyMsg) {
                emptyMsg.classList.toggle('hidden', visibleCount > 0);
            }
        }

        function clearNewsSearch() {
            const input = document.getElementById('news-search-input');
            if (input) {
                input.value = '';
            }
            filterNewsLive();
        }

        function shareNewsWhatsApp(article) {
            const item = article || currentNewsArticle;
            if (!item) return;
            const baseUrl = window.location.origin + window.location.pathname;
            const articleParam = item.slug || item.id || encodeURIComponent(item.title);
            const shareUrl = `${baseUrl}?berita=${encodeURIComponent(articleParam)}`;
            const text = encodeURIComponent(`*${item.title}*\n\n${item.excerpt || ''}\n\nBaca selengkapnya di Portal Resmi {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}:\n${shareUrl}`);
            window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
        }

        function copyNewsLink(article) {
            const item = article || currentNewsArticle;
            if (!item) return;
            const baseUrl = window.location.origin + window.location.pathname;
            const articleParam = item.slug || item.id || encodeURIComponent(item.title);
            const shareUrl = `${baseUrl}?berita=${encodeURIComponent(articleParam)}`;
            navigator.clipboard.writeText(shareUrl).then(() => {
                const text = document.getElementById('copy-link-text');
                if (text) {
                    text.innerText = 'Tersalin! ✅';
                    setTimeout(() => { text.innerText = 'Salin Tautan'; }, 2000);
                }
            });
        }

        function shareAgendaWhatsApp(agenda) {
            if (!agenda) return;
            const baseUrl = window.location.origin + window.location.pathname;
            const agendaId = agenda.id || '';
            const shareUrl = `${baseUrl}?agenda=${encodeURIComponent(agendaId)}`;
            const text = encodeURIComponent(`*Agenda Sekolah: ${agenda.title}*\n📅 ${agenda.date || ''}\n\n${agenda.description || ''}\n\nInfo selengkapnya di Website Resmi {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}:\n${shareUrl}`);
            window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
        }

        // 9. Switch Prestasi Tab (Top 10 vs Recent Achievements)
        function switchPrestasiTab(tab) {
            const leaderboardView = document.getElementById('prestasi-leaderboard-view');
            const recentView = document.getElementById('prestasi-recent-view');
            const tabBtnLeaderboard = document.getElementById('tab-btn-leaderboard');
            const tabBtnRecent = document.getElementById('tab-btn-recent');

            if (!leaderboardView || !recentView) return;

            if (tab === 'leaderboard') {
                leaderboardView.classList.remove('hidden');
                recentView.classList.add('hidden');

                if (tabBtnLeaderboard && tabBtnRecent) {
                    tabBtnLeaderboard.className = 'px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-950 shadow-amber-500/25';
                    tabBtnRecent.className = 'px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-2';
                }
            } else {
                leaderboardView.classList.add('hidden');
                recentView.classList.remove('hidden');

                if (tabBtnLeaderboard && tabBtnRecent) {
                    tabBtnRecent.className = 'px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-indigo-500/25';
                    tabBtnLeaderboard.className = 'px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-2';
                }
            }
        }

        // 10. Staff Running Marquee Controls & Live Filtering
        let currentStaffCategory = 'all';
        let isStaffMarqueePaused = false;

        function toggleStaffMarquee() {
            const track = document.getElementById('staff-running-track');
            const icon = document.getElementById('staff-marquee-toggle-icon');
            const text = document.getElementById('staff-marquee-toggle-text');
            if (!track) return;

            isStaffMarqueePaused = !isStaffMarqueePaused;
            if (isStaffMarqueePaused) {
                track.classList.add('is-paused');
                if (icon) icon.innerText = '▶';
                if (text) text.innerText = 'Lanjutkan Gerakan';
            } else {
                track.classList.remove('is-paused');
                if (icon) icon.innerText = '⏸';
                if (text) text.innerText = 'Jeda Gerakan';
            }
        }

        function nudgeStaffMarquee(amount) {
            const viewport = document.getElementById('staff-marquee-viewport');
            const track = document.getElementById('staff-running-track');
            if (!track) return;

            if (!isStaffMarqueePaused) {
                track.classList.add('is-paused');
                setTimeout(() => {
                    if (!isStaffMarqueePaused) {
                        track.classList.remove('is-paused');
                    }
                }, 4000);
            }

            if (viewport) {
                viewport.scrollBy({ left: amount, behavior: 'smooth' });
            }
        }

        function filterStaffWebsite(cat, btn) {
            currentStaffCategory = cat;
            document.querySelectorAll('.staff-cat-btn').forEach(b => {
                b.className = 'staff-cat-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer whitespace-nowrap';
            });
            if (btn) {
                btn.className = 'staff-cat-btn px-4 sm:px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-600 text-white shadow-md shadow-emerald-600/30 transition cursor-pointer whitespace-nowrap';
            }
            filterStaffLive();
        }

        function filterStaffLive() {
            const searchInput = document.getElementById('staff-search-input');
            const clearBtn = document.getElementById('staff-clear-search-btn');
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const cards = document.querySelectorAll('.staff-card');
            const emptyMsg = document.getElementById('staff-empty-msg');
            const liveCountBadge = document.getElementById('staff-live-count');
            const track = document.getElementById('staff-running-track');
            let visible = 0;

            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            if (track) {
                if (query.length > 0) {
                    track.classList.add('is-paused');
                } else if (!isStaffMarqueePaused) {
                    track.classList.remove('is-paused');
                }
            }

            cards.forEach(card => {
                const cat = card.getAttribute('data-category') || 'guru';
                const name = card.getAttribute('data-name') || '';
                const nip = card.getAttribute('data-nip') || '';
                const mapel = card.getAttribute('data-mapel') || '';
                const duty = card.getAttribute('data-duty') || '';

                const matchesCat = (currentStaffCategory === 'all' || cat === currentStaffCategory);
                const matchesQuery = (query === '' || name.includes(query) || nip.includes(query) || mapel.includes(query) || duty.includes(query));

                if (matchesCat && matchesQuery) {
                    card.style.display = 'flex';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            const uniqueVisible = Math.ceil(visible / 2);

            if (liveCountBadge) {
                liveCountBadge.innerText = `${uniqueVisible} pegawai`;
            }

            if (emptyMsg) {
                emptyMsg.style.display = visible === 0 ? 'block' : 'none';
            }
            if (track) {
                track.style.display = visible === 0 ? 'none' : 'flex';
            }
        }

        function clearStaffSearch() {
            const searchInput = document.getElementById('staff-search-input');
            if (searchInput) {
                searchInput.value = '';
            }
            filterStaffLive();
        }

        // 9. Real-Time SIS-SMA1LE Dynamic Live Sync
        function pollLiveStats() {
            fetch('{{ route('website.live_stats') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    // Update hero live counters
                    updateLiveCounter('live-stat-student', data.student_count);
                    updateLiveCounter('live-stat-teacher', data.teacher_count);
                    updateLiveCounter('live-stat-class', data.class_count);
                    updateLiveCounter('live-stat-ekskul', data.extracurricular_count);

                    // Update habit live counters
                    const tahfidzEl = document.getElementById('live-stat-tahfidz');
                    if (tahfidzEl && data.tahfidz_count !== undefined) {
                        tahfidzEl.textContent = Number(data.tahfidz_count).toLocaleString('id-ID') + '+';
                    }
                    const worshipEl = document.getElementById('live-stat-worship');
                    if (worshipEl && data.worship_count !== undefined) {
                        worshipEl.textContent = Number(data.worship_count).toLocaleString('id-ID') + '+';
                    }
                    const dailyLogEl = document.getElementById('live-stat-dailylogs');
                    if (dailyLogEl && data.daily_logs_count !== undefined) {
                        dailyLogEl.textContent = Number(data.daily_logs_count).toLocaleString('id-ID') + '+';
                    }
                }
            })
            .catch(() => {});
        }

        function updateLiveCounter(id, newVal) {
            const el = document.getElementById(id);
            if (!el || newVal === undefined) return;
            const current = parseInt(el.getAttribute('data-target') || '0', 10);
            if (current !== newVal) {
                el.setAttribute('data-target', newVal);
                el.textContent = Number(newVal).toLocaleString('id-ID');
                el.classList.add('scale-110', 'text-white');
                setTimeout(() => {
                    el.classList.remove('scale-110', 'text-white');
                }, 800);
            }
        }

        // Jalankan polling ringan berkala setiap 30 detik saat tab aktif
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                pollLiveStats();
            }
        }, 30000);

        @if(!empty($targetArticle))
            document.addEventListener('DOMContentLoaded', function () {
                openNewsModal(@json($targetArticle));
            });
        @endif

        @if(!empty($targetAgenda))
            document.addEventListener('DOMContentLoaded', function () {
                const agendaSec = document.getElementById('agenda');
                if (agendaSec) {
                    agendaSec.scrollIntoView({ behavior: 'smooth' });
                }
            });
        @endif

        // Modal Portal Selection (SIS-SMA1LE)
        function openPortalModal() {
            const modal = document.getElementById('portal-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closePortalModal() {
            const modal = document.getElementById('portal-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePortalModal();
            }
        });
    </script>

    <!-- Modal Pilihan Portal Masuk (SIS-SMA1LE) -->
    <div id="portal-modal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md hidden transition-opacity duration-200" role="dialog" aria-modal="true">
        <!-- Backdrop Close Area -->
        <div class="fixed inset-0" onclick="closePortalModal()"></div>

        <!-- Modal Box -->
        <div class="relative w-full max-w-3xl max-h-[92vh] overflow-y-auto bg-slate-900 border border-slate-700/80 rounded-3xl p-5 sm:p-8 shadow-2xl shadow-black/80 z-10 text-slate-100">
            <!-- Close Button -->
            <button type="button" onclick="closePortalModal()" class="absolute top-4 right-4 sm:top-6 sm:right-6 w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition cursor-pointer" aria-label="Tutup Modal">
                ✕
            </button>

            <!-- Header -->
            <div class="text-center max-w-xl mx-auto mb-6 sm:mb-8 pr-6 sm:pr-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[11px] font-bold tracking-wide uppercase mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Pusat Layanan Digital Mandiri</span>
                </div>
                <h3 class="text-xl sm:text-3xl font-black text-white tracking-tight">
                    Portal Ekosistem SIS-SMA1LE
                </h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Silakan tentukan layanan yang ingin Anda akses di lingkungan {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}.
                </p>
            </div>

            <!-- Portal Options Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">
                <!-- 1. Login Utama Sistem SIS -->
                <div class="rounded-2xl sm:rounded-3xl bg-slate-950/70 border border-indigo-500/30 p-5 sm:p-6 flex flex-col justify-between hover:border-indigo-400/60 transition group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                Akses Masuk Sistem
                            </span>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white text-lg">
                                🎓
                            </div>
                        </div>
                        <h4 class="text-lg sm:text-xl font-black text-white group-hover:text-indigo-300 transition">
                            Login SIS-SMA1LE
                        </h4>
                        <p class="text-xs font-semibold text-indigo-400 mt-0.5">Sistem Informasi Sekolah Terpadu</p>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Akses terpadu untuk Siswa, Guru, Wali Kelas, Kepala Sekolah, Staf TU, Bendahara, dan Guru BK: Presensi, LMS, Rapor Kurnas, Konseling, serta Administrasi Sekolah.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-1">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Siswa</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Guru / Wali Kelas</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Kepala Sekolah</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Guru BK</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Tata Usaha</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800/80">
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition hover:scale-[1.02]">
                            <span>Masuk ke Akun SIS</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- 2. Portal Orang Tua (Tanpa Login) -->
                <div class="rounded-2xl sm:rounded-3xl bg-slate-950/70 border border-emerald-500/30 p-5 sm:p-6 flex flex-col justify-between hover:border-emerald-400/60 transition group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Bebas Akses (Tanpa Login)
                            </span>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-lg">
                                👨‍👩‍👧
                            </div>
                        </div>
                        <h4 class="text-lg sm:text-xl font-black text-white group-hover:text-emerald-300 transition">
                            Portal Orang Tua Siswa
                        </h4>
                        <p class="text-xs font-semibold text-emerald-400 mt-0.5">Monitoring Perkembangan Anak</p>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Cukup masukkan nomor <strong>NISN</strong> anak Anda untuk memantau kehadiran hari ini (foto masuk/pulang), pembiasaan 7 KAIH, hafalan Al-Qur'an, dan poin disiplin.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-1">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Presensi Harian</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Foto Selfie</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">7 KAIH</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Tahfidz Quran</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Nilai Rapor</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800/80">
                        <a href="{{ route('parent.portal') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-600/30 transition hover:scale-[1.02]">
                            <span>Buka Portal Ortu (Cek NISN)</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Kiosk Links -->
            <div class="mt-5 p-3.5 rounded-2xl bg-slate-950/50 border border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
                <span class="font-semibold text-slate-300">Layanan Kiosk Mandiri:</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('library.guest_book.index') }}" class="text-amber-400 hover:underline flex items-center gap-1">
                        <span>📖 Buku Tamu Perpustakaan</span>
                    </a>
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="text-sky-400 hover:underline flex items-center gap-1">
                        <span>📺 Display Kios TV</span>
                    </a>
                </div>
            </div>

            <!-- Footer in modal -->
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <a href="{{ route('portal.select') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold hover:underline">
                    Buka Halaman Portal Penuh →
                </a>
                <button type="button" onclick="closePortalModal()" class="text-slate-400 hover:text-white cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    @auth
        @if(auth()->user()->canManageWebsiteContent())
            <div class="fixed bottom-24 right-4 sm:bottom-6 sm:right-6 z-50">
                <a href="{{ route('master.website_content') }}" class="px-4 py-2.5 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-black text-xs shadow-2xl shadow-amber-500/40 border border-amber-300/60 flex items-center gap-2 hover:scale-105 transition-all">
                    <span class="text-base">✏️</span>
                    <span>Edit Konten Website</span>
                </a>
            </div>
        @endif
    @endauth
</body>
</html>
