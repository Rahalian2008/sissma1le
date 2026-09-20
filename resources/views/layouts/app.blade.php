<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $appName }} | {{ $schoolName }}</title>

    <!-- Google Fonts & Tailwind CSS v4 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif

    <!-- Anti-flash Dark Mode Script -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200">

    <!-- Top Notification / Alert Toasts -->
    <div id="toast-container" class="fixed top-4 right-4 sm:top-5 sm:right-5 z-50 flex flex-col gap-2 max-w-sm w-[calc(100%-2rem)] sm:w-full pointer-events-none">
        @if(session('success'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 rounded-2xl shadow-xl transition-all duration-300">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300 p-1">✕</button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 rounded-2xl shadow-xl transition-all duration-300">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium">
                    {{ session('error') ?? $errors->first() }}
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 p-1">✕</button>
            </div>
        @endif
    </div>

    <!-- Main Container -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar (Desktop lg+) -->
        <aside id="desktop-sidebar" class="hidden lg:flex lg:flex-col w-72 bg-slate-900 dark:bg-slate-950 text-slate-200 border-r border-slate-800 shrink-0 transition-all duration-300 ease-in-out">
            <!-- Brand Header -->
            <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3 min-w-0 brand-container">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-400 p-0.5 flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                        <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-extrabold text-white text-base tracking-wider overflow-hidden">
                            @if(!empty($appLogoUrl))
                                <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-1 rounded-[14px]">
                            @else
                                1LE
                            @endif
                        </div>
                    </div>
                    <div class="truncate sidebar-brand-text">
                        <h1 class="font-extrabold text-white text-sm sm:text-base tracking-tight leading-none truncate">{{ $appName }}</h1>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium truncate">{{ $schoolName }}</p>
                    </div>
                </div>
                <!-- Toggle Collapse Button for Desktop Sidebar -->
                <button type="button" onclick="toggleDesktopSidebar()" id="sidebar-toggle-btn" title="Ciutkan / Lebarkan Sidebar (Ctrl+B)" class="hidden lg:flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg id="sidebar-toggle-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Role Badge & Quick Switcher -->
            <div class="sidebar-role-panel px-4 py-3 border-b border-slate-800/80 bg-slate-950/20">
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Role Aktif</span>
                    <span class="flex items-center gap-1 text-emerald-400 text-[10px] font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Online
                    </span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wide truncate {{ (Auth::user()->role === 'super_admin' || session('switched_from_super_admin')) ? 'max-w-[100px]' : '' }}">
                        {{ str_replace('_', ' ', Auth::user()->role ?? 'Guest') }}
                    </span>
                    @if(Auth::user()->role === 'super_admin' || session('switched_from_super_admin'))
                        <form action="{{ route('quick.switch') }}" method="POST" class="inline">
                            @csrf
                            <select name="role" onchange="this.form.submit()" title="Ganti Role (Khusus Super Admin)" class="text-xs bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-2 py-1 outline-none focus:border-indigo-500 cursor-pointer hover:bg-slate-700 transition">
                                <option value="" disabled selected>Ganti</option>
                                <option value="super_admin" {{ Auth::user()->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ Auth::user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="kepala_sekolah" {{ Auth::user()->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="guru" {{ Auth::user()->role === 'guru' ? 'selected' : '' }}>Guru Mapel</option>
                                <option value="wali_kelas" {{ Auth::user()->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                <option value="siswa" {{ Auth::user()->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="orang_tua" {{ Auth::user()->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                            </select>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-1">
                @php $role = Auth::user()->role ?? 'guest'; @endphp

                <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Menu Utama</div>

                <a href="{{ route('dashboard') }}" data-tooltip="Dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') || request()->is('/') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                <!-- 1. Presensi Terpadu -->
                <div class="pt-2">
                    <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">Presensi Terpadu</div>
                    
                    @if(in_array($role, ['siswa', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.selfie') }}" data-tooltip="Presensi Selfie" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.selfie') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="sidebar-label">Presensi Selfie (Kamera)</span>
                    </a>
                    @endif

                    @if(in_array($role, ['siswa', 'guru', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.qr.scanner') }}" data-tooltip="Scan QR Code" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.qr.scanner') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span class="sidebar-label">Scan QR Code</span>
                    </a>
                    @endif

                    @if($role === 'siswa')
                    <a href="{{ route('attendance.leave') }}" data-tooltip="Izin, Sakit & Pulang Cepat" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.leave*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Izin / Sakit / Pulang Cepat</span>
                    </a>
                    @endif

                    @if(in_array($role, ['guru', 'kesiswaan', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.qr.session') }}" data-tooltip="Proyektor QR Sesi" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.qr.session*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        <span class="sidebar-label">Proyektor QR Sesi</span>
                    </a>
                    @endif

                    @if(in_array($role, ['wali_kelas', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.manual') }}" data-tooltip="Absen Manual" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.manual*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="sidebar-label">Absen Manual</span>
                    </a>

                    <a href="{{ route('attendance.leaves.index') }}" data-tooltip="Persetujuan Izin & Pulang Cepat" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.leaves*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span class="sidebar-label">Persetujuan Izin & Pulang Cepat</span>
                    </a>
                    @endif

                    @if(in_array($role, ['admin', 'super_admin']))
                    <a href="{{ route('attendance.rfid.simulator') }}" data-tooltip="Integrasi RFID / Kiosk" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.rfid.simulator') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        <span class="sidebar-label">Integrasi RFID / Kiosk</span>
                    </a>
                    @endif

                    @if(in_array($role, ['siswa', 'guru', 'orang_tua', 'kepala_sekolah', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.history') }}" data-tooltip="Riwayat Presensi" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.history') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="sidebar-label">Riwayat Presensi</span>
                    </a>
                    @endif

                    @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
                    <a href="{{ route('attendance.report') }}" data-tooltip="Laporan Presensi Real-Time" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendance.report') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Laporan Presensi Real-Time</span>
                    </a>
                    @endif
                </div>

                <!-- 2. Tata Tertib -->
                <div class="pt-2">
                    <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">Tata Tertib</div>

                    <a href="{{ route('achievements.index') }}" data-tooltip="Prestasi Siswa (R1-R10)" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('achievements.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <span class="sidebar-label">Prestasi Siswa (R1-R10)</span>
                    </a>

                    <a href="{{ route('violations.index') }}" data-tooltip="Pelanggaran Siswa (P1-P6)" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('violations.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="sidebar-label">Pelanggaran Siswa (P1-P6)</span>
                    </a>

                    @if(in_array($role, ['guru', 'wali_kelas', 'kepala_sekolah', 'admin', 'super_admin']))
                    <a href="{{ route('guidance.index') }}" data-tooltip="Catatan Pembinaan" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('guidance.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Catatan Pembinaan</span>
                    </a>
                    @endif

                    @if($role !== 'siswa')
                    <a href="{{ route('discipline.ranking') }}" data-tooltip="Papan Peringkat (Rangking)" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('discipline.ranking') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span class="sidebar-label">Papan Peringkat (Rangking)</span>
                    </a>
                    @endif

                    @if(in_array($role, ['guru', 'wali_kelas', 'kepala_sekolah', 'admin', 'super_admin']))
                    <a href="{{ route('discipline.report') }}" data-tooltip="Laporan Tata Tertib" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('discipline.report') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Laporan Tata Tertib</span>
                    </a>
                    @endif
                </div>

                <!-- 3. Karakter -->
                <div class="pt-2">
                    <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">Karakter</div>

                    <a href="{{ route('habits.journal') }}" data-tooltip="7 Kebiasaan Anak Hebat" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('habits.journal') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span class="sidebar-label">7 Kebiasaan Anak Hebat</span>
                    </a>

                    <a href="{{ route('habits.summary') }}" data-tooltip="Ringkasan Karakter" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('habits.summary') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="sidebar-label">Ringkasan Karakter</span>
                    </a>

                    @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
                    <a href="{{ route('habits.charts') }}" data-tooltip="Diagram & Persentase" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('habits.charts') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <span class="sidebar-label">Diagram & Persentase</span>
                    </a>

                    <a href="{{ route('habits.report') }}" data-tooltip="Laporan Karakter" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('habits.report') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Laporan Karakter</span>
                    </a>
                    @endif
                </div>

                <!-- 4. Laporan & Analisis -->
                @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
                <div class="pt-2">
                    <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">Laporan & Cetak</div>

                    <a href="{{ route('ai.analysis') }}" data-tooltip="AI Analisis Perkembangan" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('ai.analysis') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="sidebar-label">AI Analisis Perkembangan</span>
                    </a>

                    <a href="{{ route('reports.index') }}" data-tooltip="Rekap & Laporan Cetak" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-label">Rekap & Laporan Cetak</span>
                    </a>
                </div>
                @endif

                @if(in_array($role, ['admin', 'super_admin']))
                <!-- 5. Master Data Terpadu -->
                <div class="pt-2">
                    <div class="sidebar-heading text-[11px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">Master Data</div>

                    <a href="{{ route('master.school_profile') }}" data-tooltip="Identitas Sekolah" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.school_profile') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="sidebar-label">Identitas Sekolah Lengkap</span>
                    </a>

                    <a href="{{ route('master.branding') }}" data-tooltip="Logo & KOP Surat" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.branding*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="sidebar-label">Logo & KOP Surat</span>
                    </a>

                    <a href="{{ route('master.classes') }}" data-tooltip="Data Kelas" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.classes*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="sidebar-label">Data Kelas</span>
                    </a>

                    <a href="{{ route('master.students') }}" data-tooltip="Data Siswa & RFID" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.students*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="sidebar-label">Data Siswa & RFID</span>
                    </a>

                    <a href="{{ route('master.teachers') }}" data-tooltip="Data Guru & Akun" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.teachers*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="sidebar-label">Data Guru & Role Akun</span>
                    </a>

                    <a href="{{ route('master.academic_years') }}" data-tooltip="Tahun Pelajaran" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.academic_years*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="sidebar-label">Tahun Pelajaran</span>
                    </a>

                    <a href="{{ route('master.settings') }}" data-tooltip="Pengaturan Sistem" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.settings') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="sidebar-label">Pengaturan Sistem & Geofence</span>
                    </a>

                    <a href="{{ route('master.categories') }}" data-tooltip="Butir Tata Tertib" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('master.categories') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="sidebar-label">Master Butir Tata Tertib</span>
                    </a>
                </div>
                @endif
            </nav>

            <!-- User Footer with Logout -->
            <div class="p-3.5 border-t border-slate-800 flex items-center justify-between bg-slate-950/40">
                <a href="{{ route('profile') }}" data-tooltip="Profil Akun" class="flex items-center gap-3 truncate hover:opacity-85 transition">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-md">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="sidebar-label truncate">
                        <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="sidebar-label">
                    @csrf
                    <button type="submit" title="Keluar Akun" class="touch-target text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar (Mobile, Tablet, Desktop, Ultra-Wide) -->
            <header class="h-16 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-3 sm:px-6 shrink-0 shadow-xs z-30 transition-colors">
                <!-- Left: Mobile Trigger & Brand / Desktop Breadcrumbs -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <!-- Hamburger button for mobile/tablet -->
                    <button onclick="toggleMobileDrawer(true)" aria-label="Buka Menu Navigasi" class="touch-target text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl lg:hidden cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Desktop Sidebar Toggle Icon Button -->
                    <button onclick="toggleDesktopSidebar()" type="button" aria-label="Toggle Sidebar" title="Ciutkan / Buka Menu (Ctrl+B)" class="hidden lg:flex items-center justify-center w-9 h-9 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </button>

                    <!-- Mobile Brand Logo -->
                    <div class="flex items-center gap-2 lg:hidden min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-xs shrink-0 overflow-hidden shadow-sm">
                            @if(!empty($appLogoUrl))
                                <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                            @else
                                1LE
                            @endif
                        </div>
                        <span class="font-extrabold text-slate-900 dark:text-white tracking-tight text-xs sm:text-sm truncate max-w-[120px] xs:max-w-[180px] sm:max-w-none">
                            {{ $appName }}
                        </span>
                    </div>

                    <!-- Desktop Breadcrumb -->
                    <div class="hidden lg:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-semibold text-slate-400 dark:text-slate-500">SMAN 1 Lengkong</span>
                        <span>/</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100 truncate max-w-md">@yield('title', 'Sistem Informasi Perkembangan Siswa')</span>
                    </div>
                </div>

                <!-- Right Actions: Theme Toggle, Quick Role, Notifications, Profile -->
                <div class="flex items-center gap-1 sm:gap-2.5 shrink-0">
                    <!-- Dark Mode Toggle Button -->
                    <button onclick="toggleTheme()" type="button" aria-label="Ganti Tema Gelap / Terang" title="Ganti Tema (Dark / Light)"
                        class="touch-target text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="theme-icon-sun" class="w-5 h-5 hidden text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="theme-icon-moon" class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <!-- Quick Switcher Compact (Khusus Super Admin) -->
                    @if(Auth::user()->role === 'super_admin' || session('switched_from_super_admin'))
                        <div class="hidden sm:flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-medium text-[11px]">Role:</span>
                            <form action="{{ route('quick.switch') }}" method="POST" class="inline">
                                @csrf
                                <select name="role" onchange="this.form.submit()" title="Ganti Role (Khusus Super Admin)" class="bg-transparent font-bold text-indigo-600 dark:text-indigo-400 outline-none cursor-pointer text-xs">
                                    <option value="super_admin" {{ Auth::user()->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ Auth::user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="kepala_sekolah" {{ Auth::user()->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="guru" {{ Auth::user()->role === 'guru' ? 'selected' : '' }}>Guru Mapel</option>
                                    <option value="wali_kelas" {{ Auth::user()->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                    <option value="siswa" {{ Auth::user()->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="orang_tua" {{ Auth::user()->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                                </select>
                            </form>
                        </div>
                    @endif

                    <!-- Notification Bell -->
                    <a href="{{ route('notifications.index') }}" aria-label="Notifikasi" class="touch-target relative text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-2 right-2 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-extrabold flex items-center justify-center shadow-xs">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Pill Mini -->
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 pl-1 sm:pl-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden md:inline text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[110px] lg:max-w-[140px]">{{ Auth::user()->name }}</span>
                    </a>
                </div>
            </header>

            <!-- Main Page Scrollable Content (Responsive padding & safe margin) -->
            <main id="main-scroll-area" class="flex-1 overflow-y-auto pb-24 lg:pb-12 p-3.5 sm:p-5 md:p-6 lg:p-8 3xl:p-10 transition-colors relative">
                <div class="max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto w-full">
                    @if(session('switched_from_super_admin'))
                        <div class="mb-5 p-3 sm:p-4 rounded-2xl bg-amber-500/15 border border-amber-500/40 text-amber-900 dark:text-amber-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                <div>
                                    <span class="font-extrabold uppercase tracking-wide">Mode Simulasi Peran (Super Admin):</span>
                                    <span> Saat ini Anda sedang meninjau sistem sebagai <strong>{{ Auth::user()->name }}</strong> (Role: <span class="uppercase font-bold text-amber-600 dark:text-amber-300">{{ str_replace('_', ' ', Auth::user()->role) }}</span>).</span>
                                </div>
                            </div>
                            <form action="{{ route('quick.switch') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="role" value="super_admin">
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                    Kembali ke Super Admin
                                </button>
                            </form>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Floating Back to Top Button -->
            <button id="back-to-top" onclick="scrollToTop()" aria-label="Kembali ke atas" title="Kembali ke atas" class="fixed bottom-20 lg:bottom-8 right-4 sm:right-6 z-30 p-2.5 sm:p-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 opacity-0 pointer-events-none translate-y-4 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Backdrop Overlay -->
    <div id="mobile-drawer-backdrop" onclick="toggleMobileDrawer(false)" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden transition-opacity duration-300 ease-in-out lg:hidden"></div>

    <!-- Mobile Navigation Drawer -->
    <aside id="mobile-drawer" class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 max-w-[85vw] bg-slate-900 dark:bg-slate-950 text-slate-200 flex flex-col h-full shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
        <!-- Header -->
        <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-sm shrink-0 overflow-hidden">
                    @if(!empty($appLogoUrl))
                        <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-1">
                    @else
                        1LE
                    @endif
                </div>
                <div class="truncate">
                    <h2 class="font-extrabold text-white text-sm tracking-tight truncate">{{ $appName }}</h2>
                    <p class="text-[11px] text-slate-400 truncate">{{ $schoolName }}</p>
                </div>
            </div>
            <button onclick="toggleMobileDrawer(false)" aria-label="Tutup Menu" class="touch-target text-slate-400 hover:text-white p-1 rounded-lg">
                ✕
            </button>
        </div>

        <!-- Role Badge for Mobile -->
        <div class="px-4 py-3 border-b border-slate-800/80 bg-slate-950/30">
            <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                <span>Role Aktif</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase">
                    {{ str_replace('_', ' ', Auth::user()->role ?? 'Guest') }}
                </span>
            </div>
            @if(Auth::user()->role === 'super_admin' || session('switched_from_super_admin'))
                <form action="{{ route('quick.switch') }}" method="POST" class="mt-2">
                    @csrf
                    <select name="role" onchange="this.form.submit()" class="w-full text-xs bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-1.5 outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="" disabled>Pilih Role Pengujian</option>
                        <option value="super_admin" {{ Auth::user()->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ Auth::user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kepala_sekolah" {{ Auth::user()->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="guru" {{ Auth::user()->role === 'guru' ? 'selected' : '' }}>Guru Mapel</option>
                        <option value="wali_kelas" {{ Auth::user()->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="siswa" {{ Auth::user()->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="orang_tua" {{ Auth::user()->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                    </select>
                </form>
            @endif
        </div>

        <!-- Navigation items list -->
        <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-1">
            @php $role = Auth::user()->role ?? 'guest'; @endphp
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') || request()->is('/') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- 1. Presensi Terpadu -->
            <div class="text-[10px] font-bold text-slate-500 uppercase px-3 pt-3">Presensi Terpadu</div>
            @if(in_array($role, ['siswa', 'admin', 'super_admin']))
            <a href="{{ route('attendance.selfie') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-emerald-400 hover:bg-slate-800">
                Presensi Selfie (Kamera)
            </a>
            @endif

            @if(in_array($role, ['siswa', 'guru', 'admin', 'super_admin']))
            <a href="{{ route('attendance.qr.scanner') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-sky-400 hover:bg-slate-800">
                Scan QR Code
            </a>
            @endif

            @if($role === 'siswa')
            <a href="{{ route('attendance.leave') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-amber-400 hover:bg-slate-800">
                Izin / Sakit / Pulang Cepat
            </a>
            @endif

            @if(in_array($role, ['guru', 'kesiswaan', 'admin', 'super_admin']))
            <a href="{{ route('attendance.qr.session') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-amber-400 hover:bg-slate-800">
                Proyektor QR Sesi
            </a>
            @endif

            @if(in_array($role, ['wali_kelas', 'admin', 'super_admin']))
            <a href="{{ route('attendance.manual') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-cyan-400 hover:bg-slate-800">
                Absen Manual
            </a>
            <a href="{{ route('attendance.leaves.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-yellow-400 hover:bg-slate-800">
                Persetujuan Izin & Pulang Cepat
            </a>
            @endif

            @if(in_array($role, ['admin', 'super_admin']))
            <a href="{{ route('attendance.rfid.simulator') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-purple-400 hover:bg-slate-800">
                Simulator RFID / Kiosk
            </a>
            @endif

            @if(in_array($role, ['siswa', 'guru', 'orang_tua', 'kepala_sekolah', 'admin', 'super_admin']))
            <a href="{{ route('attendance.history') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:bg-slate-800">
                Riwayat Presensi
            </a>
            @endif

            @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
            <a href="{{ route('attendance.report') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-emerald-400 hover:bg-slate-800">
                Laporan Presensi Real-Time
            </a>
            @endif

            <!-- 2. Tata Tertib -->
            <div class="text-[10px] font-bold text-slate-500 uppercase px-3 pt-3">Tata Tertib</div>
            <a href="{{ route('achievements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-amber-400 hover:bg-slate-800">
                Prestasi Siswa (R1-R10)
            </a>
            <a href="{{ route('violations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-rose-400 hover:bg-slate-800">
                Pelanggaran Siswa (P1-P6)
            </a>
            @if(in_array($role, ['guru', 'wali_kelas', 'kepala_sekolah', 'admin', 'super_admin']))
            <a href="{{ route('guidance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-indigo-400 hover:bg-slate-800">
                Catatan Pembinaan
            </a>
            @endif
            @if($role !== 'siswa')
            <a href="{{ route('discipline.ranking') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-amber-300 hover:bg-slate-800">
                Papan Peringkat (Rangking)
            </a>
            @endif
            @if(in_array($role, ['guru', 'wali_kelas', 'kepala_sekolah', 'admin', 'super_admin']))
            <a href="{{ route('discipline.report') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-indigo-300 hover:bg-slate-800">
                Laporan Tata Tertib
            </a>
            @endif

            <!-- 3. Karakter -->
            <div class="text-[10px] font-bold text-slate-500 uppercase px-3 pt-3">Karakter</div>
            <a href="{{ route('habits.journal') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-teal-400 hover:bg-slate-800">
                7 Kebiasaan Anak Hebat
            </a>
            <a href="{{ route('habits.summary') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-teal-300 hover:bg-slate-800">
                Ringkasan Karakter
            </a>
            @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
            <a href="{{ route('habits.charts') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-emerald-400 hover:bg-slate-800">
                Diagram & Persentase
            </a>
            <a href="{{ route('habits.report') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-teal-400 hover:bg-slate-800">
                Laporan Karakter
            </a>
            @endif

            <!-- 4. Laporan & Analisis -->
            @if(in_array($role, ['wali_kelas', 'guru', 'kepala_sekolah', 'admin', 'super_admin']))
            <div class="text-[10px] font-bold text-slate-500 uppercase px-3 pt-3">Laporan & Cetak</div>
            <a href="{{ route('ai.analysis') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-pink-400 hover:bg-slate-800">
                AI Analisis Perkembangan
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-teal-400 hover:bg-slate-800">
                Rekap & Laporan Cetak
            </a>
            @endif

            @if(in_array($role, ['admin', 'super_admin']))
            <div class="text-[10px] font-bold text-slate-500 uppercase px-3 pt-3">Master Data</div>
            <a href="{{ route('master.school_profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-blue-400 hover:bg-slate-800">
                Identitas Sekolah
            </a>
            <a href="{{ route('master.branding') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-pink-400 hover:bg-slate-800">
                Logo & KOP Surat
            </a>
            <a href="{{ route('master.classes') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-indigo-400 hover:bg-slate-800">
                Data Kelas
            </a>
            <a href="{{ route('master.students') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-sky-400 hover:bg-slate-800">
                Data Siswa & RFID
            </a>
            <a href="{{ route('master.teachers') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-emerald-400 hover:bg-slate-800">
                Data Guru & Role Akun
            </a>
            <a href="{{ route('master.academic_years') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-amber-400 hover:bg-slate-800">
                Tahun Pelajaran
            </a>
            <a href="{{ route('master.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800">
                Pengaturan Sistem
            </a>
            <a href="{{ route('master.categories') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800">
                Butir Tata Tertib
            </a>
            @endif
        </nav>

        <!-- Drawer Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-3 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-600/30 rounded-xl text-sm font-semibold transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation Bar (Role-Tailored, Clean Touch-Friendly) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 glass-nav border-t border-slate-200 dark:border-slate-800 px-1 py-1 flex items-center justify-around z-40 shadow-xl pb-[calc(0.4rem+env(safe-area-inset-bottom,0px))] transition-colors">
        @php $role = Auth::user()->role ?? 'guest'; @endphp
        
        <a href="{{ route('dashboard') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('dashboard') || request()->is('/') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="truncate max-w-[56px] xs:max-w-none">Home</span>
        </a>

        @if($role === 'siswa')
            <a href="{{ route('attendance.selfie') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.selfie') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <div class="w-7 h-7 -mt-2 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-md shadow-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    </svg>
                </div>
                <span class="truncate max-w-[56px] xs:max-w-none">Selfie</span>
            </a>
            <a href="{{ route('attendance.leave') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.leave*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Izin/Sakit</span>
            </a>
            <a href="{{ route('habits.journal') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('habits.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Karakter</span>
            </a>
        @elseif(in_array($role, ['wali_kelas', 'admin', 'super_admin']))
            <a href="{{ route('attendance.manual') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.manual*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <div class="w-7 h-7 -mt-2 bg-cyan-600 text-white rounded-full flex items-center justify-center shadow-md shadow-cyan-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="truncate max-w-[56px] xs:max-w-none">Manual</span>
            </a>
            <a href="{{ route('attendance.leaves.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.leaves*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Ajuan</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('reports.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Laporan</span>
            </a>
        @elseif($role === 'guru')
            <a href="{{ route('attendance.qr.session') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.qr.session*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <div class="w-7 h-7 -mt-2 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md shadow-amber-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <span class="truncate max-w-[56px] xs:max-w-none">QR Sesi</span>
            </a>
            <a href="{{ route('attendance.qr.scanner') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.qr.scanner*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Scan</span>
            </a>
            <a href="{{ route('achievements.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('achievements.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Prestasi</span>
            </a>
        @elseif($role === 'bk')
            <a href="{{ route('guidance.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('guidance.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <div class="w-7 h-7 -mt-2 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-md shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="truncate max-w-[56px] xs:max-w-none">BK</span>
            </a>
            <a href="{{ route('violations.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('violations.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Pelanggaran</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('reports.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Laporan</span>
            </a>
        @elseif($role === 'kesiswaan')
            <a href="{{ route('attendance.report') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.report*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <div class="w-7 h-7 -mt-2 bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-md shadow-emerald-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="truncate max-w-[56px] xs:max-w-none">Presensi</span>
            </a>
            <a href="{{ route('achievements.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('achievements.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Prestasi</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('reports.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Laporan</span>
            </a>
        @else
            <a href="{{ route('attendance.history') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('attendance.history*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Absen</span>
            </a>
            <a href="{{ route('habits.journal') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('habits.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Karakter</span>
            </a>
            <a href="{{ route('achievements.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('achievements.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Prestasi</span>
            </a>
        @endif

        <a href="{{ route('profile') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[9px] xs:text-[10px] sm:text-[11px] font-medium transition active:scale-95 {{ request()->routeIs('profile') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-indigo-950/50 rounded-xl' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="truncate max-w-[56px] xs:max-w-none">Akun</span>
        </a>
    </nav>

    <!-- Global Scripts: Theme Toggle, Desktop Sidebar Toggle, Mobile Drawer, Keyboard navigation, Scroll to Top -->
    <script>
        // Desktop Sidebar Collapse Controller (Responsive compact icon mode)
        function toggleDesktopSidebar(forceCollapse) {
            const sidebar = document.getElementById('desktop-sidebar');
            const toggleIcon = document.getElementById('sidebar-toggle-icon');
            if (!sidebar) return;

            let isCompact;
            if (typeof forceCollapse === 'boolean') {
                isCompact = forceCollapse;
            } else {
                isCompact = !sidebar.classList.contains('is-compact');
            }

            if (isCompact) {
                sidebar.classList.add('is-compact');
                localStorage.setItem('sidebar-collapsed', 'true');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            } else {
                sidebar.classList.remove('is-compact');
                localStorage.setItem('sidebar-collapsed', 'false');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            }
        }

        // Keyboard Shortcut Ctrl+B or Cmd+B to toggle sidebar
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleDesktopSidebar();
            }
        });

        // Restore desktop sidebar state on page load
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            toggleDesktopSidebar(true);
        }

        // Mobile Drawer Controller
        function toggleMobileDrawer(open) {
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            if (!drawer || !backdrop) return;

            if (open) {
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                drawer.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Close drawer with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                toggleMobileDrawer(false);
            }
        });

        // Touch swipe-to-close gesture on mobile drawer
        (function() {
            let touchStartX = 0;
            let touchCurrentX = 0;
            const drawerEl = document.getElementById('mobile-drawer');

            if (drawerEl) {
                drawerEl.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].clientX;
                }, { passive: true });

                drawerEl.addEventListener('touchmove', function(e) {
                    touchCurrentX = e.touches[0].clientX;
                }, { passive: true });

                drawerEl.addEventListener('touchend', function() {
                    if (touchStartX - touchCurrentX > 60 && touchCurrentX !== 0) {
                        toggleMobileDrawer(false);
                    }
                    touchStartX = 0;
                    touchCurrentX = 0;
                });
            }
        })();

        // Theme Toggle Controller
        function updateThemeIcons(isDark) {
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            if (!sunIcon || !moonIcon) return;

            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            updateThemeIcons(isDark);
        }

        // Initialize theme icons on load
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            updateThemeIcons(isDark);
        });

        // Floating Back to Top Button Controller
        const backToTopBtn = document.getElementById('back-to-top');
        const mainScrollArea = document.getElementById('main-scroll-area');

        function handleScroll() {
            if (!backToTopBtn) return;
            const scrollPos = mainScrollArea ? mainScrollArea.scrollTop : (window.scrollY || document.documentElement.scrollTop);
            if (scrollPos > 260) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                backToTopBtn.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                backToTopBtn.classList.remove('opacity-100', 'translate-y-0');
            }
        }

        if (mainScrollArea) {
            mainScrollArea.addEventListener('scroll', handleScroll, { passive: true });
        }
        window.addEventListener('scroll', handleScroll, { passive: true });

        function scrollToTop() {
            if (mainScrollArea) {
                mainScrollArea.scrollTo({ top: 0, behavior: 'smooth' });
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>

    @stack('scripts')
</body>
</html>
