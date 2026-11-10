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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js (Local & Offline Capable with CDN Fallback) -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif

    <!-- Anti-flash Dark Mode Script with Safe Storage Fallback for Older Chrome/WebView -->
    <script>
        try {
            var savedTheme = null;
            if (typeof localStorage !== 'undefined') {
                savedTheme = localStorage.getItem('color-theme');
            }
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {
            // Safe fallback if localStorage or matchMedia is restricted in Chrome/WebView
        }
    </script>

    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200">
    <!-- Accessible Skip Link for Keyboard & Screen Reader Navigation -->
    <a href="#main-scroll-area" class="skip-link">Loncat ke konten utama</a>

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

        @if(session('error') || (isset($errors) && $errors->any()))
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 rounded-2xl shadow-xl transition-all duration-300">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium">
                    {{ session('error') ?? (isset($errors) ? $errors->first() : '') }}
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 p-1">✕</button>
            </div>
        @endif
    </div>

    <!-- Main Container -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar (Desktop lg+) -->
        <aside id="desktop-sidebar" class="hidden lg:flex lg:flex-col w-60 xl:w-64 2xl:w-72 bg-slate-900 dark:bg-slate-950 text-slate-200 border-r border-slate-800 shrink-0 transition-all duration-300 ease-in-out">
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
                @php
                    $currUser = Auth::user();
                    $activeRoles = $currUser ? $currUser->all_roles : ['guest'];
                    $currentActiveSlug = $currUser ? $currUser->active_role : 'guest';
                    $currentActiveTitle = $currUser ? $currUser->getRoleTitleFor($currentActiveSlug) : 'Tamu';
                @endphp
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Peran Aktif</span>
                    <span class="flex items-center gap-1 text-emerald-400 text-[10px] font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Online
                    </span>
                </div>
                <div class="mb-2">
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-500/20 to-emerald-500/15 border border-indigo-500/40 text-indigo-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <div class="truncate text-xs font-extrabold tracking-wide text-white" title="{{ $currentActiveTitle }}">
                            {{ $currentActiveTitle }}
                        </div>
                    </div>
                    @if(count($activeRoles) > 1)
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            @foreach($activeRoles as $actRole)
                                @if($actRole !== $currentActiveSlug)
                                    <a href="{{ route('user.switch_role_get', $actRole) }}" class="px-2 py-0.5 text-[9.5px] font-bold rounded-md bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white border border-slate-700/80 transition truncate max-w-[130px]" title="Klik untuk beralih ke {{ $currUser->getRoleTitleFor($actRole) }}">
                                        + {{ $currUser->getRoleTitleFor($actRole) }}
                                    </a>
                                @endif
                            @endforeach
                            @if($currentActiveSlug !== 'all_roles')
                                <a href="{{ route('user.switch_role_get', 'all_roles') }}" class="px-2 py-0.5 text-[9.5px] font-bold rounded-md bg-purple-950/80 hover:bg-purple-600 text-purple-200 hover:text-white border border-purple-500/40 transition">
                                    ⚡ Semua Peran
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between gap-1.5">
                    @if(Auth::user()->role === 'super_admin' || session('switched_from_super_admin'))
                        <form action="{{ route('quick.switch') }}" method="POST" class="w-full">
                            @csrf
                            <select name="role" onchange="this.form.submit()" title="Ganti Role (Khusus Super Admin)" class="w-full text-xs bg-slate-800 border border-slate-700 text-slate-200 rounded-lg px-2 py-1.5 outline-none focus:border-indigo-500 cursor-pointer hover:bg-slate-700 transition">
                                <option value="" disabled selected>⚡ Simulasi Role Super Admin</option>
                                <option value="super_admin" {{ Auth::user()->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ Auth::user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="kepala_sekolah" {{ Auth::user()->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="wakasek_kurikulum" {{ Auth::user()->role === 'wakasek_kurikulum' ? 'selected' : '' }}>Wakasek Kurikulum</option>
                                <option value="wakasek_kesiswaan" {{ Auth::user()->role === 'wakasek_kesiswaan' ? 'selected' : '' }}>Wakasek Kesiswaan</option>
                                <option value="wakasek_humas" {{ Auth::user()->role === 'wakasek_humas' ? 'selected' : '' }}>Wakasek Humas</option>
                                <option value="wakasek_sarpras" {{ Auth::user()->role === 'wakasek_sarpras' ? 'selected' : '' }}>Wakasek Sarpras</option>
                                <option value="kepala_laboratorium" {{ Auth::user()->role === 'kepala_laboratorium' ? 'selected' : '' }}>Kepala Laboratorium</option>
                                <option value="kepala_perpustakaan" {{ Auth::user()->role === 'kepala_perpustakaan' ? 'selected' : '' }}>Kepala Perpustakaan</option>
                                <option value="pengelola_perpustakaan" {{ Auth::user()->role === 'pengelola_perpustakaan' ? 'selected' : '' }}>Pengelola Perpustakaan</option>
                                <option value="koordinator_bk" {{ Auth::user()->role === 'koordinator_bk' ? 'selected' : '' }}>Koordinator BP/BK</option>
                                <option value="koordinator_keagamaan" {{ Auth::user()->role === 'koordinator_keagamaan' ? 'selected' : '' }}>Koordinator Keagamaan</option>
                                <option value="wali_kelas" {{ Auth::user()->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                <option value="bk" {{ Auth::user()->role === 'bk' ? 'selected' : '' }}>Guru BK</option>
                                <option value="guru_piket" {{ Auth::user()->role === 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
                                <option value="pembina_ekstra" {{ Auth::user()->role === 'pembina_ekstra' ? 'selected' : '' }}>Pembina Ekstrakurikuler</option>
                                <option value="kepala_administrasi" {{ Auth::user()->role === 'kepala_administrasi' ? 'selected' : '' }}>Kepala Urusan Tata Usaha</option>
                                <option value="bendahara_bosp" {{ Auth::user()->role === 'bendahara_bosp' ? 'selected' : '' }}>Bendahara BOSP</option>
                                <option value="bendahara_bopd" {{ Auth::user()->role === 'bendahara_bopd' ? 'selected' : '' }}>Bendahara BOPD</option>
                                <option value="bendahara_gaji" {{ Auth::user()->role === 'bendahara_gaji' ? 'selected' : '' }}>Bendahara Gaji</option>
                                <option value="operator_dapodik" {{ Auth::user()->role === 'operator_dapodik' ? 'selected' : '' }}>Operator Dapodik</option>
                                <option value="ta_kesiswaan" {{ Auth::user()->role === 'ta_kesiswaan' ? 'selected' : '' }}>Adm. Urusan Kesiswaan</option>
                                <option value="ta_kepegawaian" {{ Auth::user()->role === 'ta_kepegawaian' ? 'selected' : '' }}>Adm. Urusan Kepegawaian</option>
                                <option value="ta_sarpras" {{ Auth::user()->role === 'ta_sarpras' ? 'selected' : '' }}>Adm. Urusan Sarpras</option>
                                <option value="ta_persuratan" {{ Auth::user()->role === 'ta_persuratan' ? 'selected' : '' }}>Adm. Urusan Persuratan</option>
                                <option value="tu_piket" {{ Auth::user()->role === 'tu_piket' ? 'selected' : '' }}>Petugas TU Piket</option>
                                <option value="guru" {{ Auth::user()->role === 'guru' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                                <option value="staff" {{ Auth::user()->role === 'staff' ? 'selected' : '' }}>Tenaga Kependidikan (Staf TU)</option>
                                <option value="siswa" {{ Auth::user()->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="orang_tua" {{ Auth::user()->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                            </select>
                        </form>
                    @elseif(count($activeRoles) > 1)
                        <form action="{{ route('user.switch_active_role') }}" method="POST" class="w-full">
                            @csrf
                            <label class="block text-[10px] text-indigo-300 font-semibold mb-1">Beralih Mode Tampilan:</label>
                            <select name="role" onchange="this.form.submit()" title="Beralih Mode Peran Tambahan" class="w-full text-xs bg-indigo-950/90 border border-indigo-500/50 text-indigo-200 rounded-xl px-2.5 py-1.5 outline-none focus:border-indigo-400 cursor-pointer hover:bg-indigo-900 transition font-medium">
                                <option value="all_roles" {{ ($currentActiveSlug === 'all_roles') ? 'selected' : '' }}>
                                    ⚡ Semua Peran (Akses Lengkap)
                                </option>
                                @foreach($activeRoles as $r)
                                    <option value="{{ $r }}" {{ ($currentActiveSlug === $r) ? 'selected' : '' }}>
                                        Mode: {{ $currUser->getRoleTitleFor($r) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-1">
                @php
                    $navUser = Auth::user();
                    $roles = $navUser ? $navUser->all_roles : ['guest'];
                    $hasRole = fn (...$check) => !empty(array_intersect($check, $roles));
                    $canNav = fn (string $key) => $navUser ? $navUser->canAccessInActiveRole($key) : false;
                    $canRecordManual = $navUser ? $navUser->canRecordAttendanceManual() : false;
                    $canManageTahfidz = $navUser ? $navUser->canManageTahfidz() : false;
                    $isStudentOnly = count($roles) === 1 && in_array('siswa', $roles);
                    $studentGender = $navUser?->student?->gender ?? $navUser?->parentProfile?->student?->gender;
                    $canSeeKiosk = $navUser && ! ($hasRole('siswa', 'sekretaris_kelas', 'wali_kelas', 'koordinator_keagamaan') && ! $hasRole('admin', 'super_admin'));
                    $isGuruOnly = $navUser && ($navUser->role === 'guru' && ! $navUser->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'koordinator_keagamaan', 'kepala_sekolah', 'pembina_ekstra', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'kesiswaan']));
                    $isStudentUser = $navUser && ($navUser->role === 'siswa' || in_array('siswa', $roles)) && ! $hasRole('pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'kepala_sekolah', 'bk');
                    $canSeeExtraAttendance = ! $isStudentUser || ($navUser && $navUser->isExtracurricularMember());
                    $isKesiswaanActive = request()->routeIs('attendance.*')
                        || request()->routeIs('worship.*')
                        || request()->routeIs('extracurricular.*')
                        || request()->routeIs('violations.*')
                        || request()->routeIs('achievements.*')
                        || request()->routeIs('guidance.*')
                        || request()->routeIs('discipline.*')
                        || request()->routeIs('tahfidz.*')
                        || request()->routeIs('habits.*')
                        || request()->routeIs('ai.*')
                        || request()->routeIs('raport.*')
                        || request()->routeIs('spmb.*')
                        || request()->routeIs('kiosk.*');
                    $isKurikulumActive = request()->routeIs('lms.*')
                        || request()->routeIs('rapor_kurnas.*');
                    $isBkActive = request()->routeIs('bk.*');
                    $isTuActive = request()->routeIs('tu.*')
                        || request()->routeIs('adm_tu.*')
                        || request()->routeIs('letters.*')
                        || request()->routeIs('dispositions.*')
                        || request()->routeIs('sppds.*')
                        || request()->routeIs('archives.*')
                        || request()->routeIs('sarpras.*')
                        || request()->routeIs('warehouse.*')
                        || request()->routeIs('employees.*')
                        || request()->routeIs('duk.*')
                        || request()->routeIs('dso.*')
                        || request()->routeIs('finance.*')
                        || request()->routeIs('guests.*')
                        || request()->routeIs('buku-induk.*')
                        || request()->routeIs('klaper.*')
                        || request()->routeIs('mutations.*')
                        || request()->routeIs('students.diplomas.*')
                        || request()->routeIs('alumni.*')
                        || request()->routeIs('ppdb.*')
                        || request()->routeIs('spmb.*')
                        || request()->routeIs('students.learning-agendas.*');
                @endphp

                <div class="sidebar-heading text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3 mb-1.5">Menu Utama</div>

                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" data-tooltip="Dashboard" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                <!-- 2. Master Data -->
                @if($canNav('master_data'))
                <a href="{{ route('master.school_profile') }}" data-tooltip="Master Data" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ (request()->routeIs('master.*') && ! request()->routeIs('master.website_content*')) ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    <span class="sidebar-label">Master Data</span>
                </a>
                @endif

                <!-- Presensi Harian Siswa (Akses Langsung Akun Siswa) -->
                @if(($navUser?->isStudent() || $isStudentUser) && $canNav('attendance'))
                <a href="{{ route('attendance.index') }}" data-tooltip="Presensi Siswa" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('attendance.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="sidebar-label">Presensi Siswa</span>
                </a>
                @endif

                <!-- 3. Kesiswaan (Parent Menu) -->
                @if($canNav('kesiswaan'))
                <div>
                    <button type="button" onclick="toggleSidebarMenu('kesiswaan-menu-desktop', 'kesiswaan-chevron-desktop')" data-tooltip="Kesiswaan" class="sidebar-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isKesiswaanActive ? 'text-indigo-400 bg-indigo-500/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4.5 h-4.5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="sidebar-label font-bold">Kesiswaan</span>
                            @if(in_array($currentActiveSlug, ['wakasek_kesiswaan', 'wali_kelas', 'pembina_ekstra', 'guru_piket', 'koordinator_keagamaan']))
                                <span class="sidebar-label px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-indigo-500/25 text-indigo-300 border border-indigo-500/30">Aktif</span>
                            @endif
                        </div>
                        <svg id="kesiswaan-chevron-desktop" class="sidebar-chevron w-4 h-4 text-slate-400 transition-transform duration-200 {{ $isKesiswaanActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="kesiswaan-menu-desktop" class="sidebar-accordion-menu {{ $isKesiswaanActive ? '' : 'hidden' }} pl-3 pr-1 py-1 space-y-1 border-l-2 border-indigo-500/30 ml-4 mt-0.5">
                        <!-- Presensi -->
                        @if($canNav('attendance'))
                        <a href="{{ route('attendance.index') }}" data-tooltip="Presensi Harian" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('attendance.*') || request()->routeIs('worship.*') || request()->routeIs('extracurricular.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="sidebar-label">Presensi Harian</span>
                        </a>
                        @endif

                        <!-- SPMB Siswa Baru -->
                        @if($canNav('spmb'))
                        <a href="{{ route('spmb.index') }}" data-tooltip="SPMB" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('spmb.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <span class="sidebar-label">SPMB</span>
                            </div>
                        </a>
                        @endif

                        <!-- Tata Tertib Siswa -->
                        @if($canNav('discipline'))
                        <a href="{{ route('violations.index') }}" data-tooltip="Tata Tertib Siswa" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('achievements.*') || request()->routeIs('violations.*') || request()->routeIs('guidance.*') || request()->routeIs('discipline.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="sidebar-label">Tata Tertib Siswa</span>
                        </a>
                        @endif

                        <!-- Hafalan Al-Qur'an -->
                        @if($canNav('tahfidz'))
                        <a href="{{ route('tahfidz.index') }}" data-tooltip="Hafalan Al-Qur'an" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('tahfidz.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="sidebar-label">Hafalan Al-Qur'an</span>
                        </a>
                        @endif

                        <!-- 7 KAIH -->
                        @if($canNav('habits'))
                        <a href="{{ route('habits.journal') }}" data-tooltip="7 KAIH" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('habits.*') || request()->routeIs('ai.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <span class="sidebar-label">7 KAIH</span>
                        </a>
                        @endif

                        <!-- Raport Digital -->
                        @if($canNav('raport_digital'))
                        <a href="{{ route('raport.index') }}" data-tooltip="Raport Digital" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('raport.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="sidebar-label">Raport Digital</span>
                        </a>
                        @endif

                        <!-- Display Kios TV -->
                        @if($canNav('kiosk') && $canSeeKiosk)
                        <a href="{{ route('kiosk.index') }}" target="_blank" data-tooltip="Display Kios TV" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition text-amber-300 hover:bg-slate-800/70 hover:text-white">
                            <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="sidebar-label">Display Kios TV</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 4. Kurikulum (Parent Menu) -->
                @if($canNav('kurikulum'))
                <div>
                    <button type="button" onclick="toggleSidebarMenu('kurikulum-menu-desktop', 'kurikulum-chevron-desktop')" data-tooltip="Kurikulum" class="sidebar-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isKurikulumActive ? 'text-indigo-400 bg-indigo-500/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4.5 h-4.5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                            </svg>
                            <span class="sidebar-label font-bold">Kurikulum</span>
                            @if(in_array($currentActiveSlug, ['wakasek_kurikulum', 'guru']))
                                <span class="sidebar-label px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-sky-500/25 text-sky-300 border border-sky-500/30">Aktif</span>
                            @endif
                        </div>
                        <svg id="kurikulum-chevron-desktop" class="sidebar-chevron w-4 h-4 text-slate-400 transition-transform duration-200 {{ $isKurikulumActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="kurikulum-menu-desktop" class="sidebar-accordion-menu {{ $isKurikulumActive ? '' : 'hidden' }} pl-3 pr-1 py-1 space-y-1 border-l-2 border-indigo-500/30 ml-4 mt-0.5">
                        <!-- LMS Pembelajaran -->
                        @if($canNav('lms'))
                        <a href="{{ route('lms.index') }}" data-tooltip="LMS Pembelajaran" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('lms.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="sidebar-label">LMS Pembelajaran</span>
                        </a>
                        @endif

                        <!-- Rapor Kurnas -->
                        @if($canNav('rapor_kurnas'))
                        <a href="{{ route('rapor_kurnas.index') }}" data-tooltip="Rapor Kurnas" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('rapor_kurnas.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="sidebar-label">{{ ($navUser && $navUser->isStudent()) ? 'Rapor Siswa (Kurnas)' : 'Rapor Kurnas' }}</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 5. Bimbingan Konseling (BK) -->
                @if($canNav('bk'))
                <a href="{{ route('bk.dashboard') }}" data-tooltip="Bimbingan Konseling" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('bk.*') ? 'bg-teal-600 text-white shadow-xs shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="sidebar-label font-bold">Bimbingan Konseling</span>
                    </div>
                    @if(in_array($currentActiveSlug, ['koordinator_bk', 'guru_bk', 'bk']))
                        <span class="sidebar-label px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-teal-500/25 text-teal-300 border border-teal-500/30">Aktif</span>
                    @endif
                </a>
                @endif

                <!-- 6. Perpustakaan -->
                @if($canNav('library'))
                <a href="{{ route('library.index') }}" data-tooltip="Perpustakaan" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('library.*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        <span class="sidebar-label">Perpustakaan</span>
                    </div>
                    @if(in_array($currentActiveSlug, ['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan']))
                        <span class="sidebar-label px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-amber-500/25 text-amber-300 border border-amber-500/30">Aktif</span>
                    @endif
                </a>
                @endif

                @if($canNav('tu') && $navUser?->role !== 'guru')
                {{-- 7. Adm. Ketatausahaan --}}
                <a href="{{ route('tu.dashboard') }}" data-tooltip="Adm. Ketatausahaan" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isTuActive ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="sidebar-label font-bold">Adm. Ketatausahaan</span>
                    </div>
                    @if(in_array($currentActiveSlug, ['kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'tu_piket', 'operator_dapodik']))
                        <span class="sidebar-label px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-emerald-500/25 text-emerald-300 border border-emerald-500/30">Aktif</span>
                    @endif
                </a>
                @endif

                @if($canNav('website_content'))
                <!-- Kelola Konten Web (Standalone Menu) -->
                <a href="{{ route('master.website_content') }}" data-tooltip="Kelola Konten Web" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('master.website_content*') ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span class="sidebar-label font-bold">Kelola Konten Web</span>
                </a>
                @endif

                <!-- Bel Sekolah (Posisi di Bawah Kelola Konten Web) -->
                @if($canNav('school_bell'))
                <a href="{{ route('school_bell.index') }}" data-tooltip="Bel Sekolah" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('school_bell.*') ? 'bg-amber-600 text-white shadow-xs shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="sidebar-label font-bold">Bel Sekolah</span>
                    </div>
                </a>
                @endif

                <!-- 7. Website Resmi Sekolah -->
                <a href="{{ route('website.home') }}" target="_blank" data-tooltip="Website Sekolah" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13.5px] font-semibold leading-snug transition text-amber-300 hover:bg-amber-500/10 hover:text-amber-200 border border-amber-500/20">
                    <svg class="w-4.5 h-4.5 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="sidebar-label">Website Sekolah</span>
                </a>
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

        <!-- Content Area (Refined, Smooth & Atmospheric) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar (Sleek Glassmorphism & Modern Dashboard Header) -->
            <header class="h-14 sm:h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between px-3.5 sm:px-6 shrink-0 shadow-xs z-30 transition-colors">
                <!-- Left: Mobile Trigger, Brand Logo, Sidebar Toggle & Breadcrumbs -->
                <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                    <!-- Hamburger button for mobile/tablet -->
                    <button id="mobile-drawer-trigger" aria-controls="mobile-drawer" aria-expanded="false" onclick="toggleMobileDrawer(true)" aria-label="Buka Menu Navigasi" class="lg:!hidden inline-flex items-center justify-center w-9 h-9 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl cursor-pointer active:scale-95 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Desktop Sidebar Toggle Icon Button -->
                    <button onclick="toggleDesktopSidebar()" type="button" aria-label="Toggle Sidebar" title="Ciutkan / Buka Menu (Ctrl+B)" class="hidden lg:flex items-center justify-center w-8.5 h-8.5 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer active:scale-95">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </button>

                    <!-- Mobile Brand Logo -->
                    <div class="flex items-center gap-2 lg:hidden min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-emerald-400 p-0.5 flex items-center justify-center shrink-0 shadow-xs">
                            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center font-extrabold text-white text-[11px] overflow-hidden">
                                @if(!empty($appLogoUrl))
                                    <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                                @else
                                    1LE
                                @endif
                            </div>
                        </div>
                        <span class="font-extrabold text-slate-900 dark:text-white tracking-tight text-xs truncate max-w-[120px] xs:max-w-[180px] sm:max-w-none">
                            {{ $appName }}
                        </span>
                    </div>

                    <!-- Desktop Breadcrumb & Realtime Indicator -->
                    <nav aria-label="Breadcrumb" class="hidden lg:flex items-center gap-2 text-xs min-w-0">
                        <span class="font-bold text-slate-400 dark:text-slate-500 hidden 2xl:flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shrink-0">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span class="truncate max-w-[130px]">{{ $schoolName }}</span>
                        </span>
                        <svg class="w-3 h-3 text-slate-300 dark:text-slate-600 hidden 2xl:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-extrabold text-slate-800 dark:text-slate-100 bg-slate-100/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-nowrap min-w-0 max-w-[160px] sm:max-w-[220px] xl:max-w-xs 2xl:max-w-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                            <span class="truncate">@yield('title', 'Sistem Informasi Siswa')</span>
                        </span>
                    </nav>

                    <!-- Realtime Live Clock & Date Widget (Desktop 2XL+) -->
                    <div class="hidden 2xl:flex items-center gap-2 px-3 py-1 bg-slate-100/80 dark:bg-slate-800/80 rounded-full border border-slate-200/80 dark:border-slate-700/80 text-[11px] font-mono text-slate-600 dark:text-slate-300 shadow-2xs tabular-num whitespace-nowrap shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        <span id="live-header-clock" class="font-bold text-slate-800 dark:text-slate-100 whitespace-nowrap">--:--:-- WIB</span>
                        <span class="text-slate-300 dark:text-slate-600 shrink-0">•</span>
                        <span id="live-header-date" class="text-slate-500 dark:text-slate-400 font-sans font-medium whitespace-nowrap">--</span>
                    </div>
                </div>

                <!-- Right Actions: System Status, Kiosk, Theme Toggle, Quick Role, Notifications, Profile -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Live Online Status Pill -->
                    <div class="hidden 2xl:inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-full text-[11px] font-bold text-emerald-700 dark:text-emerald-300 whitespace-nowrap shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-emerald shrink-0"></span>
                        <span>Online</span>
                    </div>

                    {{-- Website Resmi Sekolah Link --}}
                    <a href="{{ route('website.home') }}" target="_blank" aria-label="Buka Website Resmi Sekolah" title="Buka Website Resmi SMAN 1 Lengkong"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 transition text-xs font-bold active:scale-95 shrink-0">
                        <span class="text-sm">🌐</span>
                        <span class="hidden sm:inline">Website Sekolah</span>
                    </a>

                    @if($canSeeKiosk)
                    {{-- Display Kios TV Quick Launch --}}
                    <a href="{{ route('kiosk.index') }}" target="_blank" aria-label="Buka Display Kios TV" title="Buka Display Kios TV (Layar Publik Lobby/Kelas)"
                        class="touch-target relative text-slate-500 dark:text-slate-400 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer p-2 flex items-center justify-center active:scale-95 shrink-0">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-500 rounded-full animate-ping"></span>
                    </a>
                    @endif

                    <!-- Fullscreen Toggle Button for Classroom/Kiosk/Laptop Displays -->
                    <button onclick="toggleAppFullscreen()" type="button" aria-label="Layar Penuh" title="Layar Penuh / Maksimalkan Luas Tampilan"
                        class="hidden md:inline-flex text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer active:scale-95 shrink-0 w-8.5 h-8.5 items-center justify-center">
                        <svg id="fullscreen-icon-expand" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        <svg id="fullscreen-icon-compress" class="w-4.5 h-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h6m0 0v6m0-6l-7 7m17-7h-6m0 0v6m0-6l7 7M10 4v6m0 0H4m6 0L3 3m14 7h6m-6 0V4m0 6l7-7"/>
                        </svg>
                    </button>

                    <!-- Dark Mode Toggle Button -->
                    <button onclick="toggleTheme()" type="button" aria-label="Ganti Tema Gelap / Terang" title="Ganti Tema (Dark / Light)"
                        class="touch-target text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer active:scale-95 shrink-0">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="theme-icon-sun" class="w-4.5 h-4.5 hidden text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="theme-icon-moon" class="w-4.5 h-4.5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <!-- Quick Switcher Compact (Khusus Super Admin & Multi Role) -->
                    @php
                        $topUser = Auth::user();
                        $topRoles = $topUser ? $topUser->all_roles : [];
                        $activeRoleCurrent = $topUser ? ($topUser->active_role) : null;
                    @endphp
                    @if($topUser && ($topUser->role === 'super_admin' || session('switched_from_super_admin')))
                        <div class="hidden sm:flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs whitespace-nowrap shrink-0">
                            <span class="text-slate-500 dark:text-slate-400 font-medium text-[10px] shrink-0">Super:</span>
                            <form action="{{ route('quick.switch') }}" method="POST" class="inline">
                                @csrf
                                <select name="role" onchange="this.form.submit()" title="Ganti Role (Khusus Super Admin)" class="bg-transparent font-bold text-indigo-600 dark:text-indigo-400 outline-none cursor-pointer text-xs">
                                    <option value="super_admin" {{ $topUser->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ $topUser->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="kepala_sekolah" {{ $topUser->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="wakasek_kurikulum" {{ $topUser->role === 'wakasek_kurikulum' ? 'selected' : '' }}>Wakasek Kurikulum</option>
                                    <option value="wakasek_kesiswaan" {{ $topUser->role === 'wakasek_kesiswaan' ? 'selected' : '' }}>Wakasek Kesiswaan</option>
                                    <option value="wakasek_humas" {{ $topUser->role === 'wakasek_humas' ? 'selected' : '' }}>Wakasek Humas</option>
                                    <option value="wakasek_sarpras" {{ $topUser->role === 'wakasek_sarpras' ? 'selected' : '' }}>Wakasek Sarpras</option>
                                    <option value="kepala_laboratorium" {{ $topUser->role === 'kepala_laboratorium' ? 'selected' : '' }}>Kepala Laboratorium</option>
                                    <option value="kepala_perpustakaan" {{ $topUser->role === 'kepala_perpustakaan' ? 'selected' : '' }}>Kepala Perpustakaan</option>
                                    <option value="pengelola_perpustakaan" {{ $topUser->role === 'pengelola_perpustakaan' ? 'selected' : '' }}>Pengelola Perpustakaan</option>
                                    <option value="koordinator_bk" {{ $topUser->role === 'koordinator_bk' ? 'selected' : '' }}>Koordinator BP/BK</option>
                                    <option value="koordinator_keagamaan" {{ $topUser->role === 'koordinator_keagamaan' ? 'selected' : '' }}>Koord. Keagamaan</option>
                                    <option value="wali_kelas" {{ $topUser->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                    <option value="bk" {{ $topUser->role === 'bk' ? 'selected' : '' }}>Guru BK</option>
                                    <option value="guru_piket" {{ $topUser->role === 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
                                    <option value="pembina_ekstra" {{ $topUser->role === 'pembina_ekstra' ? 'selected' : '' }}>Pembina Ekstra</option>
                                    <option value="kepala_administrasi" {{ $topUser->role === 'kepala_administrasi' ? 'selected' : '' }}>Kepala TU</option>
                                    <option value="bendahara_bosp" {{ $topUser->role === 'bendahara_bosp' ? 'selected' : '' }}>Bendahara BOSP</option>
                                    <option value="bendahara_bopd" {{ $topUser->role === 'bendahara_bopd' ? 'selected' : '' }}>Bendahara BOPD</option>
                                    <option value="bendahara_gaji" {{ $topUser->role === 'bendahara_gaji' ? 'selected' : '' }}>Bendahara Gaji</option>
                                    <option value="operator_dapodik" {{ $topUser->role === 'operator_dapodik' ? 'selected' : '' }}>Operator Dapodik</option>
                                    <option value="ta_kesiswaan" {{ $topUser->role === 'ta_kesiswaan' ? 'selected' : '' }}>Adm. Kesiswaan</option>
                                    <option value="ta_kepegawaian" {{ $topUser->role === 'ta_kepegawaian' ? 'selected' : '' }}>Adm. Kepegawaian</option>
                                    <option value="ta_sarpras" {{ $topUser->role === 'ta_sarpras' ? 'selected' : '' }}>Adm. Sarpras</option>
                                    <option value="ta_persuratan" {{ $topUser->role === 'ta_persuratan' ? 'selected' : '' }}>Adm. Persuratan</option>
                                    <option value="tu_piket" {{ $topUser->role === 'tu_piket' ? 'selected' : '' }}>TU Piket</option>
                                    <option value="guru" {{ $topUser->role === 'guru' ? 'selected' : '' }}>Guru Mapel</option>
                                    <option value="staff" {{ $topUser->role === 'staff' ? 'selected' : '' }}>Staf TU</option>
                                    <option value="siswa" {{ $topUser->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="orang_tua" {{ $topUser->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                                </select>
                            </form>
                        </div>
                    @elseif($topUser && count($topRoles) > 1)
                        <div class="hidden sm:flex items-center gap-1.5 bg-indigo-50/90 dark:bg-indigo-950/60 px-2.5 py-1 rounded-xl border border-indigo-200 dark:border-indigo-800/80 text-xs whitespace-nowrap shrink-0 shadow-2xs">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-700 dark:text-indigo-300">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Mode:
                            </span>
                            <form action="{{ route('user.switch_active_role') }}" method="POST" class="inline">
                                @csrf
                                <select name="role" onchange="this.form.submit()" title="Beralih Mode Peran Tambahan" class="bg-transparent font-extrabold text-indigo-700 dark:text-indigo-300 outline-none cursor-pointer text-xs pr-1">
                                    <option value="all_roles" {{ ($activeRoleCurrent === 'all_roles') ? 'selected' : '' }} class="text-indigo-700 dark:text-indigo-300 dark:bg-slate-900 font-extrabold">
                                        ⚡ Semua Peran (Akses Lengkap)
                                    </option>
                                    @foreach($topRoles as $r)
                                        <option value="{{ $r }}" {{ ($activeRoleCurrent === $r) ? 'selected' : '' }} class="text-slate-800 dark:text-slate-100 dark:bg-slate-900 font-semibold">
                                            {{ $topUser->getRoleTitleFor($r) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif

                    <!-- Notification Bell with Interactive Dropdown -->
                    <div class="relative" id="notification-bell-container">
                        <button type="button" onclick="toggleNotificationDropdown(event)" aria-label="Notifikasi" class="touch-target relative text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer p-2 flex items-center justify-center active:scale-95">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-rose-500 text-white rounded-full text-[9px] font-extrabold flex items-center justify-center shadow-xs">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Popover -->
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-3 z-50 text-left">
                            <div class="px-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="font-bold text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>Notifikasi Sistem</span>
                                    @if($unreadCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-extrabold">{{ $unreadCount }} Baru</span>
                                    @endif
                                </div>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read_all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline font-semibold cursor-pointer">Tandai Semua Dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                                @php
                                    $recentNotifs = Auth::user()->systemNotifications()->orderByDesc('created_at')->limit(5)->get();
                                @endphp
                                @forelse($recentNotifs as $rn)
                                    <a href="{{ $rn->link_url ?: route('notifications.index') }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition {{ !$rn->is_read ? 'bg-indigo-50/40 dark:bg-indigo-950/20' : '' }}">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ !$rn->is_read ? 'bg-indigo-600 animate-pulse' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $rn->title }}</div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">{{ $rn->message }}</div>
                                                <div class="text-[9.5px] text-slate-400 mt-1">{{ $rn->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-8 text-center text-xs text-slate-400">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        Tidak ada notifikasi saat ini
                                    </div>
                                @endforelse
                            </div>
                            <div class="pt-2 px-4 border-t border-slate-100 dark:border-slate-800 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 block py-1">
                                    Buka Halaman Notifikasi Lengkap &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Pill & Role Badge -->
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 pl-2 sm:pl-3 border-l border-slate-200/80 dark:border-slate-800/80 group">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-extrabold text-xs flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate max-w-[120px] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider leading-tight truncate max-w-[140px]" title="{{ Auth::user()->getRoleTitleFor(Auth::user()->active_role) }}">{{ Auth::user()->getRoleTitleFor(Auth::user()->active_role) }}</span>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Sleek Top Micro Progress Bar for Page Transitions -->
            <div id="page-progress-bar" class="h-0.5 bg-gradient-to-r from-indigo-500 via-teal-400 to-emerald-500 w-0 opacity-0 transition-all duration-300 pointer-events-none z-40"></div>

            <!-- Main Page Scrollable Content (Responsive padding & safe margin) -->
            <main id="main-scroll-area" tabindex="-1" class="flex-1 min-w-0 w-full overflow-y-auto overflow-x-hidden safe-pb-nav pb-24 xs:pb-28 lg:pb-10 p-2 sm:p-3 md:p-3.5 lg:p-4 xl:p-5 2xl:p-6 transition-colors relative focus:outline-hidden">
                <div class="w-full max-w-full min-w-0 mx-auto content-area min-h-[calc(100vh-140px)] flex flex-col">
                    @if(session('switched_from_super_admin'))
                        <div class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/40 text-amber-900 dark:text-amber-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shadow-sm">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                <div>
                                    <span class="font-extrabold uppercase tracking-wide">Mode Simulasi Peran (Super Admin):</span>
                                    <span> Saat ini Anda sedang meninjau sistem sebagai <strong>{{ Auth::user()->name }}</strong> (Role: <span class="uppercase font-bold text-amber-600 dark:text-amber-300">{{ str_replace('_', ' ', Auth::user()->role) }}</span>).</span>
                                </div>
                            </div>
                            <form action="{{ route('quick.switch') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="role" value="super_admin">
                                <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                    Kembali ke Super Admin
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(Auth::check() && count(Auth::user()->getActiveRoleOptions()) > 1)
                        @php
                            $userActiveRole = Auth::user()->active_role;
                            $userAllRoles = Auth::user()->getActiveRoleOptions();
                        @endphp
                        <div class="mb-5 p-3 sm:p-3.5 rounded-2xl bg-gradient-to-r from-indigo-50/90 via-purple-50/60 to-emerald-50/70 dark:from-slate-800/90 dark:via-indigo-950/40 dark:to-emerald-950/30 border border-indigo-200/70 dark:border-indigo-800/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                                <div class="truncate text-xs">
                                    <div class="text-[10.5px] uppercase tracking-wider font-extrabold text-indigo-700 dark:text-indigo-400">Mode Peran Aktif:</div>
                                    <div class="font-black text-slate-800 dark:text-white truncate">
                                        {{ Auth::user()->getRoleTitleFor($userActiveRole) }}
                                        @if($userActiveRole === 'all_roles')
                                            <span class="ml-1 px-1.5 py-0.2 rounded-md bg-amber-500/20 text-amber-600 dark:text-amber-300 font-bold text-[10px]">Semua Aplikasi Terbuka</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 mr-1 hidden sm:inline">Pindah Role:</span>
                                <a href="{{ route('user.switch_role_get', 'all_roles') }}" class="px-2.5 py-1 rounded-xl text-xs font-bold transition flex items-center gap-1 {{ $userActiveRole === 'all_roles' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                                    <span>⚡ Semua Akses</span>
                                </a>
                                @foreach($userAllRoles as $rSlug)
                                    <a href="{{ route('user.switch_role_get', $rSlug) }}" class="px-2.5 py-1 rounded-xl text-xs font-semibold transition flex items-center gap-1 {{ $userActiveRole === $rSlug ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}" title="Beralih ke {{ Auth::user()->getRoleTitleFor($rSlug) }}">
                                        @if($userActiveRole === $rSlug)
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        @endif
                                        <span>{{ Auth::user()->getRoleTitleFor($rSlug) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Floating Back to Top Button -->
            <button id="back-to-top" onclick="scrollToTop()" aria-label="Kembali ke atas" title="Kembali ke atas" class="fixed bottom-4 right-3 sm:right-4 z-20 w-8 h-8 sm:w-9 sm:h-9 bg-indigo-600/90 hover:bg-indigo-600 text-white rounded-xl shadow-lg hover:shadow-indigo-500/25 transition-all duration-300 opacity-0 pointer-events-none translate-y-3 flex items-center justify-center cursor-pointer">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>Peran Aktif</span>
                <span class="flex items-center gap-1 text-emerald-400 text-[10px] font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Online
                </span>
            </div>
            <div class="mb-2">
                <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-500/20 to-emerald-500/15 border border-indigo-500/40 text-indigo-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    <div class="truncate text-xs font-extrabold tracking-wide text-white">
                        {{ $currentActiveTitle }}
                    </div>
                </div>
                @if(count($activeRoles) > 1)
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        <a href="{{ route('user.switch_role_get', 'all_roles') }}" class="px-2 py-0.5 text-[9.5px] font-bold rounded-md {{ $currentActiveSlug === 'all_roles' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-amber-300 hover:bg-slate-700' }} border border-amber-500/40">
                            ⚡ Semua Akses
                        </a>
                        @foreach($activeRoles as $actRole)
                            @if($actRole !== $currentActiveSlug)
                                <a href="{{ route('user.switch_role_get', $actRole) }}" class="px-1.5 py-0.5 text-[9.5px] font-semibold rounded-md bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white border border-slate-700/80 transition truncate max-w-[130px]" title="{{ $currUser->getRoleTitleFor($actRole) }}">
                                    + {{ $currUser->getRoleTitleFor($actRole) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if(Auth::user()->role === 'super_admin' || session('switched_from_super_admin'))
                <form action="{{ route('quick.switch') }}" method="POST" class="mt-2">
                    @csrf
                    <select name="role" onchange="this.form.submit()" class="w-full text-xs bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-1.5 outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="" disabled selected>⚡ Simulasi Role Super Admin</option>
                        <option value="super_admin" {{ Auth::user()->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ Auth::user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kepala_sekolah" {{ Auth::user()->role === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="wakasek_kurikulum" {{ Auth::user()->role === 'wakasek_kurikulum' ? 'selected' : '' }}>Wakasek Kurikulum</option>
                        <option value="wakasek_kesiswaan" {{ Auth::user()->role === 'wakasek_kesiswaan' ? 'selected' : '' }}>Wakasek Kesiswaan</option>
                        <option value="wakasek_humas" {{ Auth::user()->role === 'wakasek_humas' ? 'selected' : '' }}>Wakasek Humas</option>
                        <option value="wakasek_sarpras" {{ Auth::user()->role === 'wakasek_sarpras' ? 'selected' : '' }}>Wakasek Sarpras</option>
                        <option value="kepala_laboratorium" {{ Auth::user()->role === 'kepala_laboratorium' ? 'selected' : '' }}>Kepala Laboratorium</option>
                        <option value="kepala_perpustakaan" {{ Auth::user()->role === 'kepala_perpustakaan' ? 'selected' : '' }}>Kepala Perpustakaan</option>
                        <option value="pengelola_perpustakaan" {{ Auth::user()->role === 'pengelola_perpustakaan' ? 'selected' : '' }}>Pengelola Perpustakaan</option>
                        <option value="koordinator_bk" {{ Auth::user()->role === 'koordinator_bk' ? 'selected' : '' }}>Koordinator BP/BK</option>
                        <option value="koordinator_keagamaan" {{ Auth::user()->role === 'koordinator_keagamaan' ? 'selected' : '' }}>Koordinator Keagamaan</option>
                        <option value="wali_kelas" {{ Auth::user()->role === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="bk" {{ Auth::user()->role === 'bk' ? 'selected' : '' }}>Guru BK</option>
                        <option value="guru_piket" {{ Auth::user()->role === 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
                        <option value="pembina_ekstra" {{ Auth::user()->role === 'pembina_ekstra' ? 'selected' : '' }}>Pembina Ekstrakurikuler</option>
                        <option value="kepala_administrasi" {{ Auth::user()->role === 'kepala_administrasi' ? 'selected' : '' }}>Kepala Urusan Tata Usaha</option>
                        <option value="bendahara_bosp" {{ Auth::user()->role === 'bendahara_bosp' ? 'selected' : '' }}>Bendahara BOSP</option>
                        <option value="bendahara_bopd" {{ Auth::user()->role === 'bendahara_bopd' ? 'selected' : '' }}>Bendahara BOPD</option>
                        <option value="bendahara_gaji" {{ Auth::user()->role === 'bendahara_gaji' ? 'selected' : '' }}>Bendahara Gaji</option>
                        <option value="operator_dapodik" {{ Auth::user()->role === 'operator_dapodik' ? 'selected' : '' }}>Operator Dapodik</option>
                        <option value="ta_kesiswaan" {{ Auth::user()->role === 'ta_kesiswaan' ? 'selected' : '' }}>Adm. Urusan Kesiswaan</option>
                        <option value="ta_kepegawaian" {{ Auth::user()->role === 'ta_kepegawaian' ? 'selected' : '' }}>Adm. Urusan Kepegawaian</option>
                        <option value="ta_sarpras" {{ Auth::user()->role === 'ta_sarpras' ? 'selected' : '' }}>Adm. Urusan Sarpras</option>
                        <option value="ta_persuratan" {{ Auth::user()->role === 'ta_persuratan' ? 'selected' : '' }}>Adm. Urusan Persuratan</option>
                        <option value="tu_piket" {{ Auth::user()->role === 'tu_piket' ? 'selected' : '' }}>Petugas TU Piket</option>
                        <option value="guru" {{ Auth::user()->role === 'guru' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                        <option value="staff" {{ Auth::user()->role === 'staff' ? 'selected' : '' }}>Tenaga Kependidikan (Staf TU)</option>
                        <option value="siswa" {{ Auth::user()->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="orang_tua" {{ Auth::user()->role === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                    </select>
                </form>
            @elseif(count($activeRoles) > 1)
                <form action="{{ route('user.switch_active_role') }}" method="POST" class="mt-2">
                    @csrf
                    <label class="block text-[10px] text-indigo-300 font-semibold mb-1">Beralih Mode Tampilan:</label>
                    <select name="role" onchange="this.form.submit()" class="w-full text-xs bg-indigo-950/90 border border-indigo-500/50 text-indigo-200 rounded-xl px-3 py-1.5 outline-none focus:border-indigo-400 cursor-pointer">
                        <option value="all_roles" {{ ($currentActiveSlug === 'all_roles') ? 'selected' : '' }}>
                            ⚡ Semua Peran (Akses Lengkap)
                        </option>
                        @foreach($activeRoles as $r)
                            <option value="{{ $r }}" {{ ($currentActiveSlug === $r) ? 'selected' : '' }}>
                                Mode: {{ $currUser->getRoleTitleFor($r) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <!-- Navigation items list -->
        <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-1">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('dashboard') || request()->is('/') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Master Data -->
            @if($canNav('master_data'))
            <a href="{{ route('master.school_profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ (request()->routeIs('master.*') && ! request()->routeIs('master.website_content*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
                <span>Master Data</span>
            </a>
            @endif

            <!-- Presensi Harian Siswa (Menu Langsung Siswa) -->
            @if(($navUser?->isStudent() || $isStudentUser) && $canNav('attendance'))
            <a href="{{ route('attendance.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('attendance.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>Presensi Siswa</span>
            </a>
            @endif

            <!-- 3. Kesiswaan (Parent Menu) -->
            @if($canNav('kesiswaan'))
            <div>
                <button type="button" onclick="toggleSidebarMenu('kesiswaan-menu-mobile', 'kesiswaan-chevron-mobile')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isKesiswaanActive ? 'text-indigo-400 bg-indigo-500/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="font-bold">Kesiswaan</span>
                        @if(in_array($currentActiveSlug, ['wakasek_kesiswaan', 'wali_kelas', 'pembina_ekstra', 'guru_piket', 'koordinator_keagamaan']))
                            <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-indigo-500/25 text-indigo-300 border border-indigo-500/30">Aktif</span>
                        @endif
                    </div>
                    <svg id="kesiswaan-chevron-mobile" class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $isKesiswaanActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="kesiswaan-menu-mobile" class="{{ $isKesiswaanActive ? '' : 'hidden' }} pl-3 pr-1 py-1 space-y-1 border-l-2 border-indigo-500/30 ml-4 mt-0.5">
                    @if($canNav('attendance'))
                    <!-- Presensi -->
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('attendance.*') || request()->routeIs('worship.*') || request()->routeIs('extracurricular.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Presensi Harian</span>
                    </a>
                    @endif

                    @if($canNav('spmb'))
                    <!-- SPMB Siswa Baru -->
                    <a href="{{ route('spmb.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('spmb.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            <span>SPMB</span>
                        </div>
                    </a>
                    @endif

                    @if($canNav('discipline'))
                    <!-- Tata Tertib Siswa -->
                    <a href="{{ route('violations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('achievements.*') || request()->routeIs('violations.*') || request()->routeIs('guidance.*') || request()->routeIs('discipline.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Tata Tertib Siswa</span>
                    </a>
                    @endif

                    @if($canNav('tahfidz'))
                    <!-- Hafalan Al-Qur'an -->
                    <a href="{{ route('tahfidz.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('tahfidz.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Hafalan Al-Qur'an</span>
                    </a>
                    @endif

                    @if($canNav('habits'))
                    <!-- 7 KAIH -->
                    <a href="{{ route('habits.journal') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ (request()->routeIs('habits.*') || request()->routeIs('ai.*')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span>7 KAIH</span>
                    </a>
                    @endif

                    @if($canNav('raport_digital'))
                    <!-- Raport Digital -->
                    <a href="{{ route('raport.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('raport.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Raport Digital</span>
                    </a>
                    @endif

                    @if($canNav('kiosk') && $canSeeKiosk)
                    <!-- Display Kios TV -->
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition text-amber-300 hover:bg-slate-800/70 hover:text-white">
                        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Display Kios TV</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- 4. Kurikulum (Parent Menu) -->
            @if($canNav('kurikulum'))
            <div>
                <button type="button" onclick="toggleSidebarMenu('kurikulum-menu-mobile', 'kurikulum-chevron-mobile')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isKurikulumActive ? 'text-indigo-400 bg-indigo-500/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4.5 h-4.5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                        <span class="font-bold">Kurikulum</span>
                        @if(in_array($currentActiveSlug, ['wakasek_kurikulum', 'guru']))
                            <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-sky-500/25 text-sky-300 border border-sky-500/30">Aktif</span>
                        @endif
                    </div>
                    <svg id="kurikulum-chevron-mobile" class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $isKurikulumActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="kurikulum-menu-mobile" class="{{ $isKurikulumActive ? '' : 'hidden' }} pl-3 pr-1 py-1 space-y-1 border-l-2 border-indigo-500/30 ml-4 mt-0.5">
                    @if($canNav('lms'))
                    <!-- LMS Pembelajaran -->
                    <a href="{{ route('lms.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('lms.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>LMS Pembelajaran</span>
                    </a>
                    @endif

                    @if($canNav('rapor_kurnas'))
                    <!-- Rapor Kurnas -->
                    <a href="{{ route('rapor_kurnas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium leading-snug transition {{ request()->routeIs('rapor_kurnas.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>{{ ($navUser && $navUser->isStudent()) ? 'Rapor Siswa (Kurnas)' : 'Rapor Kurnas' }}</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- 5. Bimbingan Konseling (BK) Mobile -->
            @if($canNav('bk'))
            <a href="{{ route('bk.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('bk.*') ? 'bg-teal-600 text-white shadow-xs shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4.5 h-4.5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-bold">Bimbingan Konseling</span>
                </div>
                @if(in_array($currentActiveSlug, ['koordinator_bk', 'guru_bk', 'bk']))
                    <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-teal-500/25 text-teal-300 border border-teal-500/30">Aktif</span>
                @endif
            </a>
            @endif

            <!-- 6. Perpustakaan -->
            @if($canNav('library'))
            <a href="{{ route('library.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('library.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4.5 h-4.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    <span>Perpustakaan</span>
                </div>
                @if(in_array($currentActiveSlug, ['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan']))
                    <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-amber-500/25 text-amber-300 border border-amber-500/30">Aktif</span>
                @endif
            </a>
            @endif

            @if($canNav('tu') && $navUser?->role !== 'guru')
            {{-- 7. Adm. Ketatausahaan Mobile --}}
            <a href="{{ route('tu.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ $isTuActive ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4.5 h-4.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="font-bold">Adm. Ketatausahaan</span>
                </div>
                @if(in_array($currentActiveSlug, ['kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'tu_piket', 'operator_dapodik']))
                    <span class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md bg-emerald-500/25 text-emerald-300 border border-emerald-500/30">Aktif</span>
                @endif
            </a>
            @endif

            @if($canNav('website_content'))
            <!-- Kelola Konten Web (Standalone Mobile) -->
            <a href="{{ route('master.website_content') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('master.website_content*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span class="font-bold">Kelola Konten Web</span>
            </a>
            @endif

            <!-- Bel Sekolah (Mobile - Di Bawah Kelola Konten Web) -->
            @if($canNav('school_bell'))
            <a href="{{ route('school_bell.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-semibold leading-snug transition {{ request()->routeIs('school_bell.*') ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4.5 h-4.5 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="font-bold">Bel Sekolah</span>
                </div>
            </a>
            @endif

            <!-- 7. Website Resmi Sekolah -->
            <a href="{{ route('website.home') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13.5px] font-semibold leading-snug transition text-amber-300 hover:bg-amber-500/10 hover:text-amber-200 border border-amber-500/20">
                <svg class="w-4.5 h-4.5 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <span>Website Sekolah</span>
            </a>
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

    <!-- Mobile Floating Island Bottom Navigation (Role-Tailored, Touch-Friendly Ergonomics) -->
    <nav class="lg:hidden fixed bottom-3 inset-x-2.5 sm:inset-x-8 floating-island-nav rounded-2xl px-2 py-1.5 flex items-center justify-around z-40 shadow-2xl safe-pb transition-all">
        @php
            $botUser = Auth::user();
            $bRoles = $botUser ? $botUser->all_roles : ['guest'];
            $bHasRole = fn (...$check) => !empty(array_intersect($check, $bRoles));
            $bCanManual = $botUser ? $botUser->canRecordAttendanceManual() : false;
        @endphp
        
        <!-- 1. Home / Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('dashboard') || request()->is('/') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="truncate max-w-[56px] xs:max-w-none">Home</span>
        </a>

        @if($bHasRole('sekretaris_kelas'))
            <!-- Sekretaris: Ajuan Izin -->
            <a href="{{ route('attendance.leave') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.leave*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Izin/Sakit</span>
            </a>

            <!-- CENTER FAB: Presensi Selfie -->
            <a href="{{ route('attendance.selfie') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Presensi Selfie Siswa">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center shadow-lg shadow-emerald-500/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <circle cx="12" cy="13" r="3" stroke-width="2.2"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 -mt-1 truncate">Selfie</span>
            </a>

            <!-- Presensi Manual -->
            <a href="{{ route('attendance.manual') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.manual*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Manual</span>
            </a>
        @elseif($bHasRole('siswa'))
            <!-- Siswa: Ajuan Izin/Sakit -->
            <a href="{{ route('attendance.leave') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.leave*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Izin/Sakit</span>
            </a>

            <!-- CENTER FAB: Presensi Selfie Siswa -->
            <a href="{{ route('attendance.selfie') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Presensi Selfie Kamera">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center shadow-lg shadow-emerald-500/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <circle cx="12" cy="13" r="3" stroke-width="2.2"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 -mt-1 truncate">Selfie</span>
            </a>

            <!-- 7 Karakter Hebat -->
            <a href="{{ route('habits.journal') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('habits.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Karakter</span>
            </a>
        @elseif($bHasRole('koordinator_keagamaan'))
            <!-- Keagamaan: Shalat Dhuha/Dzuhur -->
            <a href="{{ route('worship.index', 'dhuha') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->is('worship*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Ibadah</span>
            </a>

            <!-- CENTER FAB: Tahfidz Al-Qur'an -->
            <a href="{{ route('tahfidz.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Setoran Tahfidz">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-600/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 -mt-1 truncate">Tahfidz</span>
            </a>

            <!-- Karakter / 7 KAIH Pembiasaan -->
            <a href="{{ route('habits.journal') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('habits.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Karakter</span>
            </a>
        @elseif($bHasRole('pembina_ekstra', 'sekretaris_ekstra'))
            <!-- Ekstrakurikuler -->
            <a href="{{ route('extracurricular.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('extracurricular.*') ? 'text-purple-600 dark:text-purple-400 font-extrabold bg-purple-50/80 dark:bg-purple-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-purple-500' }}">
                <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Ekstra</span>
            </a>

            <!-- CENTER FAB: Presensi Ekstrakurikuler -->
            <a href="{{ route('extracurricular.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Presensi Ekstrakurikuler">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-purple-600/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-purple-600 dark:text-purple-400 -mt-1 truncate">Presensi</span>
            </a>

            <!-- Prestasi Ekstra / Siswa -->
            <a href="{{ route('achievements.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('achievements.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Prestasi</span>
            </a>
        @elseif($bHasRole('wali_kelas', 'admin', 'super_admin', 'kesiswaan', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'kepala_sekolah', 'guru_bk', 'bk'))
            <!-- Ajuan Izin & Sakit Siswa -->
            <a href="{{ route('attendance.leaves.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.leaves*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Ajuan</span>
            </a>

            <!-- CENTER FAB: Presensi Manual Cepat -->
            <a href="{{ route('attendance.manual') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Input Presensi Manual">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-cyan-500/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-cyan-600 dark:text-cyan-400 -mt-1 truncate">Manual</span>
            </a>

            <!-- Pelanggaran Tata Tertib Siswa -->
            <a href="{{ route('violations.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('violations.*') ? 'text-rose-600 dark:text-rose-400 font-extrabold bg-rose-50/80 dark:bg-rose-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-rose-500' }}">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Pelanggaran</span>
            </a>
        @elseif($bHasRole('guru') || $bCanManual)
            <!-- Guru: Scanner QR Siswa -->
            <a href="{{ route('attendance.qr.scanner') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.qr.scanner*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Scan</span>
            </a>

            <!-- CENTER FAB: Buka Sesi QR KBM -->
            <a href="{{ route('attendance.qr.session') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Buka Sesi QR Kelas">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 flex items-center justify-center shadow-lg shadow-amber-500/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 -mt-1 truncate">QR Sesi</span>
            </a>

            <!-- Pelanggaran Tata Tertib -->
            <a href="{{ route('violations.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('violations.*') ? 'text-rose-600 dark:text-rose-400 font-extrabold bg-rose-50/80 dark:bg-rose-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-rose-500' }}">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Pelanggaran</span>
            </a>
        @else
            <!-- Default / Fallback Role (Orang Tua / Guest): Riwayat Presensi -->
            <a href="{{ route('attendance.history') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('attendance.history*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Riwayat</span>
            </a>

            <!-- CENTER FAB: Presensi Utama -->
            <a href="{{ route('attendance.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center -mt-5 group" title="Pusat Presensi">
                <div class="nav-fab-btn w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-600 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-indigo-600/35 border-3 border-white dark:border-slate-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 -mt-1 truncate">Presensi</span>
            </a>

            <!-- Prestasi / Pencapaian Siswa -->
            <a href="{{ route('achievements.index') }}" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight transition active:scale-95 {{ request()->routeIs('achievements.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60 rounded-xl' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="truncate max-w-[56px] xs:max-w-none">Prestasi</span>
            </a>
        @endif

        <!-- 5. Buka Menu Drawer Lengkap (One-Thumb Reach) -->
        <button type="button" onclick="toggleMobileDrawer(true)" aria-label="Buka Menu Lengkap" class="flex-1 min-w-0 flex flex-col items-center justify-center gap-0.5 py-1 px-1 touch-target text-[11px] xs:text-[12px] font-semibold tracking-tight text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition active:scale-95 cursor-pointer">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span class="truncate max-w-[56px] xs:max-w-none">Menu</span>
        </button>
    </nav>

    <!-- Global Scripts: Theme Toggle, Desktop Sidebar Toggle, Mobile Drawer, Keyboard navigation, Scroll to Top -->
    <script>
        // Sidebar & Mobile Drawer Accordion Menu Toggle Controller
        function toggleSidebarMenu(menuId, chevronId) {
            const menu = document.getElementById(menuId);
            const chevron = document.getElementById(chevronId);
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

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
                try { if (typeof localStorage !== 'undefined') localStorage.setItem('sidebar-collapsed', 'true'); } catch (e) {}
                if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            } else {
                sidebar.classList.remove('is-compact');
                try { if (typeof localStorage !== 'undefined') localStorage.setItem('sidebar-collapsed', 'false'); } catch (e) {}
                if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            }
        }

        // Keyboard Shortcut Ctrl+B or Cmd+B to toggle sidebar
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleDesktopSidebar();
            }
        });

        // Restore desktop sidebar state on page load
        try {
            if (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === 'true') {
                toggleDesktopSidebar(true);
            }
        } catch (e) {}

        // Mobile Drawer Controller (Multi-Engine & Older Chrome / WebView Compatible)
        function toggleMobileDrawer(open) {
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            const trigger = document.getElementById('mobile-drawer-trigger');
            if (!drawer) return;

            const shouldOpen = typeof open === 'boolean' ? open : drawer.classList.contains('-translate-x-full');

            if (shouldOpen) {
                drawer.classList.remove('-translate-x-full');
                drawer.classList.add('is-open');
                drawer.style.transform = 'translateX(0)';
                drawer.style.webkitTransform = 'translateX(0)';
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                    backdrop.style.display = 'block';
                }
                if (trigger) trigger.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            } else {
                drawer.classList.add('-translate-x-full');
                drawer.classList.remove('is-open');
                drawer.style.transform = 'translateX(-100%)';
                drawer.style.webkitTransform = 'translateX(-100%)';
                if (backdrop) {
                    backdrop.classList.add('hidden');
                    backdrop.style.display = 'none';
                }
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                    if (document.activeElement && drawer.contains(document.activeElement)) {
                        trigger.focus();
                    }
                }
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Close drawer with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                toggleMobileDrawer(false);
            }
        });

        // Touch swipe-to-close gesture on mobile drawer (Ergonomic & Scroll-Aware)
        (function() {
            let touchStartX = 0;
            let touchStartY = 0;
            let touchCurrentX = 0;
            let touchCurrentY = 0;
            const drawerEl = document.getElementById('mobile-drawer');

            if (drawerEl) {
                drawerEl.addEventListener('touchstart', function(e) {
                    if (e.touches && e.touches.length > 0) {
                        touchStartX = e.touches[0].clientX;
                        touchStartY = e.touches[0].clientY;
                        touchCurrentX = touchStartX;
                        touchCurrentY = touchStartY;
                    }
                }, { passive: true });

                drawerEl.addEventListener('touchmove', function(e) {
                    if (e.touches && e.touches.length > 0) {
                        touchCurrentX = e.touches[0].clientX;
                        touchCurrentY = e.touches[0].clientY;
                    }
                }, { passive: true });

                drawerEl.addEventListener('touchend', function() {
                    const diffX = touchStartX - touchCurrentX;
                    const diffY = Math.abs(touchStartY - touchCurrentY);
                    // Only close on intentional horizontal left swipe (> 80px) and NOT during vertical menu scrolling
                    if (diffX > 80 && diffX > diffY * 1.8 && touchCurrentX !== 0) {
                        toggleMobileDrawer(false);
                    }
                    touchStartX = 0;
                    touchStartY = 0;
                    touchCurrentX = 0;
                    touchCurrentY = 0;
                });
            }
        })();

        // Notification Dropdown Controller
        function toggleNotificationDropdown(e) {
            if (e) e.stopPropagation();
            const dd = document.getElementById('notification-dropdown');
            if (dd) {
                dd.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            const bellContainer = document.getElementById('notification-bell-container');
            const dd = document.getElementById('notification-dropdown');
            if (dd && !dd.classList.contains('hidden')) {
                if (bellContainer && !bellContainer.contains(e.target)) {
                    dd.classList.add('hidden');
                }
            }
        });

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
            try {
                if (typeof localStorage !== 'undefined') {
                    localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
                }
            } catch (e) {}
            updateThemeIcons(isDark);
        }

        // Fullscreen Toggle Controller for Widescreen/Kiosk/Laptop Displays
        function toggleAppFullscreen() {
            const expandIcon = document.getElementById('fullscreen-icon-expand');
            const compressIcon = document.getElementById('fullscreen-icon-compress');
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                if (expandIcon && compressIcon) {
                    expandIcon.classList.add('hidden');
                    compressIcon.classList.remove('hidden');
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
                if (expandIcon && compressIcon) {
                    expandIcon.classList.remove('hidden');
                    compressIcon.classList.add('hidden');
                }
            }
        }

        document.addEventListener('fullscreenchange', function() {
            const expandIcon = document.getElementById('fullscreen-icon-expand');
            const compressIcon = document.getElementById('fullscreen-icon-compress');
            if (!expandIcon || !compressIcon) return;
            if (document.fullscreenElement) {
                expandIcon.classList.add('hidden');
                compressIcon.classList.remove('hidden');
            } else {
                expandIcon.classList.remove('hidden');
                compressIcon.classList.add('hidden');
            }
        });

        // Initialize theme icons and live clock on load
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            updateThemeIcons(isDark);

            // Live Digital Clock & Indonesian Date Updater
            function updateLiveHeaderClock() {
                const clockEl = document.getElementById('live-header-clock');
                const dateEl = document.getElementById('live-header-date');
                if (!clockEl && !dateEl) return;
                const now = new Date();
                if (clockEl) {
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
                }
                if (dateEl) {
                    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                }
            }
            updateLiveHeaderClock();
            setInterval(updateLiveHeaderClock, 1000);
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

        // Smooth Page Navigation Transition Handler (Optimized to Prevent Mobile Chromium GPU Crash)
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            const isMobile = window.innerWidth < 1024;
            // Close mobile drawer immediately if a link inside drawer is clicked
            if (isMobile && link.closest('#mobile-drawer')) {
                toggleMobileDrawer(false);
            }

            try {
                const targetUrl = new URL(link.href, window.location.origin);
                if (targetUrl.origin === window.location.origin && targetUrl.pathname !== window.location.pathname) {
                    const progressBar = document.getElementById('page-progress-bar');
                    if (progressBar) {
                        progressBar.style.width = '35%';
                        progressBar.classList.remove('opacity-0');
                        setTimeout(function () {
                            progressBar.style.width = '80%';
                        }, 120);
                    }
                    // NEVER trigger 2D transform on .content-area on mobile to avoid GPU texture memory overflow / white blank crash
                    if (!isMobile) {
                        const contentArea = document.querySelector('.content-area');
                        if (contentArea) {
                            contentArea.style.transition = 'opacity 0.16s ease-out';
                            contentArea.style.opacity = '0.85';
                        }
                    }
                }
            } catch (err) {}
        });

        // Restore DOM state when restored from BFCache (Back/Forward in Chrome Mobile)
        window.addEventListener('pageshow', function () {
            const progressBar = document.getElementById('page-progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.classList.add('opacity-0');
            }
            const contentArea = document.querySelector('.content-area');
            if (contentArea) {
                contentArea.style.opacity = '1';
                contentArea.style.transform = 'none';
            }
            toggleMobileDrawer(false);
        });
    </script>

    @stack('modals')
    @stack('scripts')
</body>
</html>
