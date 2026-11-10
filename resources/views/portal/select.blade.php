<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk Terpadu - {{ $appName }} | {{ $schoolName }}</title>
    @php
        $portalOgImage = !empty($appLogoUrl) ? $appLogoUrl : url(asset('images/website/hero_school.jpg'));
    @endphp
    <!-- Open Graph / Meta -->
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="Portal Masuk Terpadu - {{ $appName }} | {{ $schoolName }}">
    <meta property="og:description" content="Pusat Akses Tunggal Ekosistem Pendidikan Digital {{ $schoolName }}. Terintegrasi Akademik, Kesiswaan, Bimbingan Konseling, dan Administrasi Ketatausahaan.">
    <meta property="og:image" content="{{ $portalOgImage }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-between p-4 sm:p-8 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 relative overflow-x-hidden selection:bg-indigo-600 selection:text-white">

    <!-- Ambient Glowing Background Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 0.8s;"></div>
    </div>

    <!-- Top Header -->
    <header class="w-full max-w-6xl mx-auto flex items-center justify-between relative z-10 py-2">
        <a href="{{ route('website.home') }}" class="inline-flex items-center gap-3 text-slate-300 hover:text-white transition group">
            <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/10 p-1 flex items-center justify-center shrink-0 group-hover:bg-white/15 transition">
                @if(!empty($appLogoUrl))
                    <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain">
                @else
                    <span class="text-sm font-black text-indigo-400">1LE</span>
                @endif
            </div>
            <div>
                <h1 class="font-extrabold text-sm sm:text-base text-white tracking-tight leading-tight">{{ $schoolName }}</h1>
                <p class="text-[11px] text-slate-400">Portal Ekosistem Digital Terpadu {{ $appName }}</p>
            </div>
        </a>

        <a href="{{ route('website.home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span class="hidden sm:inline">Kembali ke Website</span>
        </a>
    </header>

    <!-- Main Container: Unified SIS Portal -->
    <main class="w-full max-w-5xl mx-auto my-auto py-8 sm:py-12 relative z-10">
        <!-- Title & Badges -->
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-bold tracking-wide uppercase mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Pintu Masuk Tunggal</span>
            </div>
            <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                Portal Ekosistem {{ $appName }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-2.5 leading-relaxed">
                Pusat layanan digital mandiri terpadu di lingkungan <strong class="text-slate-200">{{ $schoolName }}</strong>. Seluruh proses akademik, kesiswaan, bimbingan konseling, dan administrasi ketatausahaan terintegrasi dalam 1 sistem terpusat.
            </p>
        </div>

        <!-- Featured Primary Card: SIS-SMA1LE Login Hub -->
        <div class="group relative rounded-3xl bg-slate-900/90 border border-indigo-500/40 p-6 sm:p-10 backdrop-blur-xl shadow-2xl shadow-indigo-950/60 transition-all duration-300 mb-6">
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left Details -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            Akses Masuk Utama Sistem
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Online & Real-Time
                        </span>
                    </div>

                    <h3 class="text-2xl sm:text-3xl font-black text-white group-hover:text-indigo-300 transition-colors">
                        {{ $appName }}
                    </h3>
                    <p class="text-xs sm:text-sm font-semibold text-indigo-400">
                        Sistem Informasi Sekolah Terpadu & Administrasi Mandiri
                    </p>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Gerbang operasional harian bagi seluruh sivitas akademika: Presensi Kehadiran multi-metode, LMS Pembelajaran, Rapor Kurikulum Nasional 2025, Layanan Bimbingan Konseling & Psikotes, serta 5 Modul Administrasi Ketatausahaan (Kesiswaan, Kepegawaian, Persuratan, Sarpras BMD, Keuangan).
                    </p>

                    <!-- User Roles Allowed -->
                    <div class="pt-3 border-t border-slate-800">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Dukungan Multi-Peran:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">👨‍🎓 Siswa</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">👨‍🏫 Guru Pengajar</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">📋 Wali Kelas</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">🏛️ Kepala Sekolah</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">🤝 Guru BK</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">💼 Staf Tata Usaha</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">💰 Bendahara</span>
                        </div>
                    </div>
                </div>

                <!-- Right Action CTA Box -->
                <div class="lg:col-span-5 flex flex-col justify-center items-center text-center p-6 rounded-2xl bg-slate-950/70 border border-slate-800/80 shadow-inner space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-blue-500 text-white flex items-center justify-center shadow-xl shadow-indigo-600/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <h4 class="font-extrabold text-base text-white">Masuk ke Akun Anda</h4>
                        <p class="text-xs text-slate-400">Gunakan NISN / NIP / Username dan Password</p>
                    </div>

                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-extrabold text-sm sm:text-base shadow-xl shadow-indigo-600/30 transition-all hover:scale-[1.02]">
                        <span>Masuk Portal {{ $appName }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>

                    <p class="text-[11px] text-slate-500">
                        Sistem mendeteksi role aktif dan mengalihkan ke dashboard sesuai kewenangan.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3 Secondary Public Fast-Access Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            
            <!-- Quick Link 1: Portal Orang Tua (Tanpa Login) -->
            <a href="{{ route('parent.portal') }}" class="group p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-900 border border-emerald-500/30 hover:border-emerald-400/60 backdrop-blur-md transition-all duration-300 hover:-translate-y-0.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl">
                            👨‍👩‍👧
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Tanpa Login
                        </span>
                    </div>
                    <h4 class="font-extrabold text-sm sm:text-base text-white group-hover:text-emerald-300 transition-colors">
                        Portal Orang Tua Siswa
                    </h4>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                        Pantau riwayat presensi hari ini (foto masuk/pulang), pembiasaan 7 KAIH, tahfidz, dan prestasi cukup dengan <strong>NISN</strong> anak.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 text-xs font-bold text-emerald-400 flex items-center justify-between">
                    <span>Buka Portal Ortu</span>
                    <span>→</span>
                </div>
            </a>

            <!-- Quick Link 2: Buku Tamu Perpustakaan Kiosk -->
            <a href="{{ route('library.guest_book.index') }}" class="group p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-900 border border-amber-500/30 hover:border-amber-400/60 backdrop-blur-md transition-all duration-300 hover:-translate-y-0.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl">
                            📖
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Kios Mandiri
                        </span>
                    </div>
                    <h4 class="font-extrabold text-sm sm:text-base text-white group-hover:text-amber-300 transition-colors">
                        Buku Tamu Perpustakaan
                    </h4>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                        Layanan mandiri presensi kunjungan perpustakaan bagi siswa, dewan guru, staf pendidik, serta tamu sekolah.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 text-xs font-bold text-amber-400 flex items-center justify-between">
                    <span>Isi Buku Tamu</span>
                    <span>→</span>
                </div>
            </a>

            <!-- Quick Link 3: Layar Display TV Kiosk -->
            <a href="{{ route('kiosk.index') }}" target="_blank" class="group p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-900 border border-sky-500/30 hover:border-sky-400/60 backdrop-blur-md transition-all duration-300 hover:-translate-y-0.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-xl">
                            📺
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30">
                            Lobby TV
                        </span>
                    </div>
                    <h4 class="font-extrabold text-sm sm:text-base text-white group-hover:text-sky-300 transition-colors">
                        Layar Display Kiosk TV
                    </h4>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                        Monitor informasi publik real-time untuk display hall utama sekolah: pengumuman dinas, agenda, dan statistik harian.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 text-xs font-bold text-sky-400 flex items-center justify-between">
                    <span>Buka Display TV</span>
                    <span>↗</span>
                </div>
            </a>

        </div>

        <!-- Information Footer -->
        <div class="mt-10 pt-6 border-t border-slate-800 text-center flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div class="flex items-center gap-3">
                <a href="{{ route('website.home') }}" class="hover:text-white transition">
                    <span>🏠 Website Resmi</span>
                </a>
                <span>•</span>
                <a href="{{ route('parent.portal') }}" class="hover:text-white transition">
                    <span>👨‍👩‍👧 Portal Ortu</span>
                </a>
                <span>•</span>
                <a href="{{ route('website.home') }}#kontak" class="hover:text-white transition">
                    <span>📞 Pusat Bantuan IT</span>
                </a>
            </div>
            <div>
                &copy; {{ date('Y') }} {{ $schoolName }}. Semua Hak Cipta Dilindungi.
            </div>
        </div>
    </main>

</body>
</html>
