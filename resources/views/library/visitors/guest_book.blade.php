<!DOCTYPE html>
<html lang="id" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kiosk Buku Tamu Mandiri - {{ $schoolName ?? 'SMAN 1 Lengkong' }}</title>
    <link rel="icon" type="image/png" href="{{ $schoolFaviconUrl ?? ($schoolLogo ?? asset('images/logo.png')) }}">
    <link rel="shortcut icon" href="{{ $schoolFaviconUrl ?? ($schoolLogo ?? asset('images/logo.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; 
            background-color: #050811;
            color: #f1f5f9;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom subtle scrollbar */
        .kiosk-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .kiosk-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 9999px;
        }
        .kiosk-scroll::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.35);
            border-radius: 9999px;
        }
        .kiosk-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.7);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-screen max-h-screen overflow-hidden flex flex-col justify-between bg-[#050811] text-slate-100 selection:bg-indigo-500 selection:text-white">

    <!-- Ambient Glow Background -->
    <div class="fixed top-0 left-1/4 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-1/4 w-80 h-80 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none z-0"></div>

    <!-- =========================================================
         TOP KIOSK HEADER BAR (SLEEK COMPACT ~54px)
         ========================================================= -->
    <header class="relative z-30 shrink-0 h-14 bg-gradient-to-r from-slate-900 via-indigo-950/70 to-slate-900 border-b border-slate-800/80 px-3 sm:px-6 shadow-xl backdrop-blur-xl flex items-center justify-between">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between gap-3">
            
            <!-- Branding Identitas Perpustakaan & Sekolah -->
            <div class="flex items-center gap-3 shrink-0 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 p-0.5 shadow-md shadow-indigo-500/20 shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center overflow-hidden">
                        @if(!empty($schoolLogo))
                            <img src="{{ $schoolLogo }}" alt="Logo Sekolah" class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
                        @else
                            <span class="text-lg">📚</span>
                        @endif
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm sm:text-base font-black tracking-tight text-white truncate">
                            Buku Tamu Perpustakaan
                        </h1>
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[10px] font-extrabold uppercase tracking-wider shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>MANDIRI</span>
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-indigo-200/80 font-medium truncate">
                        {{ $schoolName ?? 'SMA Negeri 1 Lengkong' }} &bull; Presensi Kehadiran Pemustaka &amp; Tamu
                    </p>
                </div>
            </div>

            <!-- Jam Realtime, Counter & Unified Ecosystem Switcher -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Unified Kiosk Ecosystem Switcher -->
                <div class="hidden md:flex items-center gap-1 bg-slate-950/80 border border-slate-800 rounded-xl p-1 text-[11px] font-bold overflow-x-auto no-scrollbar">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios TV Display">
                        <span>📺 Kios TV</span>
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" class="px-2 py-1 rounded-lg bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 flex items-center gap-1 whitespace-nowrap">
                        <span>📚 Kios Perpus</span>
                    </a>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Resepsionis Tamu">
                        <span>👥 Kios Tamu</span>
                    </a>
                    <a href="{{ route('attendance.kiosk-satpam') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Pos Satpam">
                        <span>🛡️ Kios Satpam</span>
                    </a>
                    <a href="{{ route('school_bell.index') }}" target="_blank" class="hidden lg:flex px-2 py-1 rounded-lg text-amber-400 hover:text-amber-300 hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Buka Bel Sekolah">
                        <span>🔔 Bel</span>
                    </a>
                    <a href="{{ route('website.home') }}" target="_blank" class="hidden xl:flex px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Kembali ke Portal Sekolah">
                        <span>🌐 Web</span>
                    </a>
                </div>

                <!-- Digital Clock Card -->
                <div class="text-right px-2.5 sm:px-3 py-1 rounded-xl bg-slate-950/70 border border-slate-800 shadow-inner shrink-0">
                    <div id="live-clock" class="font-mono text-xs sm:text-sm font-black text-amber-400 tracking-wider leading-tight">00:00:00</div>
                    <div id="live-date" class="text-[9px] text-slate-400 font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
                </div>

                <!-- Pengunjung Counter Badge -->
                <div class="text-center px-2.5 sm:px-3 py-1 rounded-xl bg-indigo-600/25 border border-indigo-500/30 shadow-inner shrink-0">
                    <div id="badge-total-today" class="text-xs sm:text-sm font-black text-white font-mono leading-tight">{{ $todayCount }}</div>
                    <div class="text-[8.5px] text-indigo-200 uppercase font-bold tracking-wider">Pengunjung</div>
                </div>

                <!-- Tombol Fullscreen & Admin Nav -->
                <button type="button" onclick="toggleKioskFullscreen()" title="Layar Penuh (Fullscreen)" 
                        class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center text-xs cursor-pointer shadow-sm shrink-0">
                    <span id="fullscreen-icon">⛶</span>
                </button>
                <a href="{{ route('library.index') }}" title="Masuk ke Panel Pengelola Perpustakaan" 
                   class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center text-xs cursor-pointer shadow-sm shrink-0">
                    <span>⚙️</span>
                </a>
            </div>
        </div>
    </header>

    <!-- =========================================================
         MAIN DUAL GRID (STRICTLY 1-SCREEN VIEWPORT FIT)
         ========================================================= -->
    <main class="flex-1 min-h-0 max-w-7xl w-full mx-auto p-2.5 sm:p-4 grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 overflow-hidden relative z-10">
        
        <!-- =====================================================
             KOLOM KIRI: FORM ENTRI BUKU TAMU MANDIRI
             ===================================================== -->
        <div class="lg:col-span-7 flex flex-col h-full min-h-0 bg-slate-900/85 border border-slate-800 rounded-2xl p-3 sm:p-4 shadow-xl backdrop-blur-xl overflow-hidden">
            
            <!-- Mini Form Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-800/80 shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="p-1.5 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs">✍️</span>
                    <h2 class="text-xs sm:text-sm font-black text-white truncate">Isi Presensi Kunjungan Anda</h2>
                </div>
                <button type="button" onclick="resetGuestForm()" 
                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] font-semibold text-slate-300 hover:text-white transition flex items-center gap-1 cursor-pointer border border-slate-700 shrink-0">
                    <span>🔄 Bersihkan</span>
                </button>
            </div>

            <!-- Form Content (Flex-1 Min-H-0 dengan internal scroll aman) -->
            <form id="kiosk-guest-form" onsubmit="submitGuestBook(event)" class="flex-1 min-h-0 flex flex-col justify-between overflow-y-auto kiosk-scroll pr-1 space-y-2.5 mt-2">
                @csrf
                <input type="hidden" name="visitor_type" id="input-visitor-type" value="siswa">

                <!-- 1. Pilihan Kategori Tamu (Compact Sentuh Mudah) -->
                <div class="shrink-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                            Langkah 1 &bull; Pilih Kategori <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400">Pilih salah satu</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" id="btn-type-siswa" onclick="selectVisitorType('siswa')" 
                                class="p-2 sm:p-2.5 rounded-xl border text-center transition cursor-pointer border-indigo-500 bg-indigo-950/60 text-indigo-300 font-bold shadow-md shadow-indigo-950/40">
                            <div class="text-lg sm:text-xl">🎓</div>
                            <div class="text-xs font-black text-white">Siswa/i</div>
                            <div class="text-[9px] text-indigo-300/80 font-medium">Gunakan NISN</div>
                        </button>

                        <button type="button" id="btn-type-guru" onclick="selectVisitorType('guru')" 
                                class="p-2 sm:p-2.5 rounded-xl border border-slate-800 text-center transition cursor-pointer hover:border-slate-700 bg-slate-950/60 text-slate-400 font-bold">
                            <div class="text-lg sm:text-xl">👨‍🏫</div>
                            <div class="text-xs font-black text-white">Guru/Tendik</div>
                            <div class="text-[9px] text-slate-500 font-medium">Gunakan NIP</div>
                        </button>

                        <button type="button" id="btn-type-umum" onclick="selectVisitorType('umum')" 
                                class="p-2 sm:p-2.5 rounded-xl border border-slate-800 text-center transition cursor-pointer hover:border-slate-700 bg-slate-950/60 text-slate-400 font-bold">
                            <div class="text-lg sm:text-xl">🏛️</div>
                            <div class="text-xs font-black text-white">Tamu Umum</div>
                            <div class="text-[9px] text-slate-500 font-medium">Wali / Tamu Luar</div>
                        </button>
                    </div>
                </div>

                <!-- 2. Kotak Input Identitas (NISN / NIP) dengan Live Instant Lookup -->
                <div id="box-identity-number" class="shrink-0 space-y-1">
                    <div class="flex items-center justify-between">
                        <label id="label-identity-number" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                            Langkah 2 &bull; Nomor Induk Siswa Nasional (NISN) <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[10px] text-indigo-400 font-medium hidden sm:inline">Scan kartu atau ketik langsung</span>
                    </div>
                    <div class="relative">
                        <input type="text" name="identity_number" id="input-identity-number" oninput="debounceLookup(this.value)" autofocus
                               placeholder="Masukkan 10 digit NISN Anda..." 
                               class="w-full px-3.5 py-2 sm:py-2.5 text-sm sm:text-base font-mono font-bold bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition text-white placeholder-slate-600">
                        <div id="lookup-spinner" class="absolute right-3 top-2.5 hidden">
                            <span class="inline-block w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>
                    <div id="lookup-result-status" class="text-[11px] hidden"></div>
                </div>

                <!-- 3. Detail Nama Lengkap & Kelas/Instansi -->
                <div class="shrink-0 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                            Nama Lengkap Pengunjung <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="name" id="input-name" required placeholder="Nama lengkap..." 
                               class="w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 text-white font-semibold placeholder-slate-600">
                    </div>
                    <div class="space-y-1">
                        <label id="label-class-or-inst" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                            Kelas / Rombel Siswa
                        </label>
                        <input type="text" name="class_or_institution" id="input-class-or-inst" placeholder="Contoh: XII Merdeka 7" 
                               class="w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 text-white placeholder-slate-600">
                    </div>
                </div>

                <!-- 4. Keperluan Kunjungan (Visual Pills Selection) -->
                <div class="shrink-0 space-y-1.5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                        Langkah 3 &bull; Keperluan Kunjungan <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                        @php
                            $purposes = [
                                ['label' => 'Membaca Buku', 'icon' => '📖'],
                                ['label' => 'Pinjam / Balik Buku', 'icon' => '🔄'],
                                ['label' => 'Mengerjakan Tugas', 'icon' => '📝'],
                                ['label' => 'Diskusi Kelompok', 'icon' => '👥'],
                                ['label' => 'Komputer & E-Book', 'icon' => '💻'],
                                ['label' => 'Riset / Penelitian', 'icon' => '🔍'],
                            ];
                        @endphp
                        @foreach($purposes as $idx => $p)
                            <button type="button" onclick="selectPurpose('{{ $p['label'] }}')" data-purpose="{{ $p['label'] }}"
                                    class="btn-purpose p-2 rounded-xl border text-left transition cursor-pointer flex items-center gap-2 {{ $idx === 0 ? 'bg-indigo-600 text-white font-bold border-indigo-500 shadow-sm' : 'text-slate-300 bg-slate-950/60 border-slate-800 hover:border-slate-700' }}">
                                <span class="text-sm shrink-0">{{ $p['icon'] }}</span>
                                <span class="text-[11px] font-semibold truncate">{{ $p['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="purpose" id="input-purpose" value="Membaca Buku" required>
                </div>

                <!-- 5. Kepuasan Layanan & Catatan Tambahan (Opsional) -->
                <div class="shrink-0 p-2.5 bg-slate-950/60 rounded-xl border border-slate-800/80 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[10.5px] font-bold text-slate-300">Suasana Perpustakaan Hari Ini (Opsional)</label>
                        <span class="text-[9px] text-slate-500">Pilih salah satu</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectRating('Sangat Nyaman')" data-rating="Sangat Nyaman" id="btn-rating-sangat-nyaman"
                                class="btn-rating py-1.5 px-2 rounded-lg border text-center transition cursor-pointer flex items-center justify-center gap-1.5 bg-emerald-500/20 text-emerald-300 border-emerald-500 shadow-xs font-bold text-[11px]">
                            <span>😍</span>
                            <span class="truncate">Sangat Nyaman</span>
                        </button>
                        <button type="button" onclick="selectRating('Nyaman')" data-rating="Nyaman" id="btn-rating-nyaman"
                                class="btn-rating py-1.5 px-2 rounded-lg border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-[11px] text-center transition cursor-pointer flex items-center justify-center gap-1.5">
                            <span>😊</span>
                            <span class="truncate">Nyaman</span>
                        </button>
                        <button type="button" onclick="selectRating('Cukup')" data-rating="Cukup" id="btn-rating-cukup"
                                class="btn-rating py-1.5 px-2 rounded-lg border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-[11px] text-center transition cursor-pointer flex items-center justify-center gap-1.5">
                            <span>🙂</span>
                            <span class="truncate">Cukup</span>
                        </button>
                    </div>
                    <input type="hidden" name="satisfaction_rating" id="input-satisfaction-rating" value="Sangat Nyaman">

                    <input type="text" name="notes" id="input-notes" placeholder="Pesan, saran, atau judul buku yang dicari (opsional)..." 
                           class="w-full px-3 py-1.5 text-[11px] bg-slate-900 border border-slate-800 rounded-lg outline-none focus:border-indigo-500 text-white placeholder-slate-600">
                </div>

                <!-- Tombol Submit Presensi (Always prominent & visible) -->
                <div class="shrink-0 pt-1">
                    <button type="submit" id="btn-submit-guest" 
                            class="w-full py-2.5 sm:py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 hover:from-indigo-500 hover:to-sky-400 text-white font-extrabold text-xs sm:text-sm tracking-wide shadow-lg shadow-indigo-600/30 transition transform active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
                        <span>🚀 Kirim Presensi Kunjungan Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- =====================================================
             KOLOM KANAN: LOG PENGUNJUNG HARI INI & STATISTIK
             ===================================================== -->
        <div class="lg:col-span-5 flex flex-col h-full min-h-0 bg-slate-900/85 border border-slate-800 rounded-2xl p-3 sm:p-4 shadow-xl backdrop-blur-xl overflow-hidden">
            
            <!-- Log Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-800/80 shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-xs">📋</span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-white truncate">Log Pengunjung Hari Ini</h3>
                        <p class="text-[10px] text-slate-400">Presensi kehadiran pemustaka terkini</p>
                    </div>
                </div>
                <span id="log-count-pill" class="px-2.5 py-0.5 rounded-full text-[11px] bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 font-extrabold font-mono shrink-0">
                    {{ $todayVisitors->count() }} Orang
                </span>
            </div>

            <!-- Breakdown Statistik Kategori Hari Ini -->
            <div class="grid grid-cols-3 gap-2 text-center shrink-0 my-2">
                <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800/80">
                    <div class="text-base sm:text-lg font-black text-cyan-400 font-mono leading-tight" id="stat-siswa-count">{{ $studentCount }}</div>
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">🎓 Siswa</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800/80">
                    <div class="text-base sm:text-lg font-black text-emerald-400 font-mono leading-tight" id="stat-guru-count">{{ $teacherCount }}</div>
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">👨‍🏫 Guru/Staf</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800/80">
                    <div class="text-base sm:text-lg font-black text-purple-400 font-mono leading-tight" id="stat-umum-count">{{ $generalCount }}</div>
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">🏛️ Umum</div>
                </div>
            </div>

            <!-- Mini Pencarian Cepat di Log -->
            <div class="shrink-0 mb-2">
                <input type="text" id="log-filter-search" oninput="filterLog(this.value)" placeholder="🔍 Cari nama atau kelas di log..." 
                       class="w-full px-3 py-1.5 text-xs bg-slate-950 border border-slate-800 rounded-xl outline-none focus:border-indigo-500 text-white placeholder-slate-600">
            </div>

            <!-- Daftar Scrollable Pengunjung Hari Ini (Flex-1 Min-H-0) -->
            <div id="today-visitors-container" class="flex-1 min-h-0 overflow-y-auto pr-1 kiosk-scroll space-y-2">
                @forelse($todayVisitors as $v)
                    <div class="visitor-log-item p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-indigo-500/40 transition">
                        <!-- Baris Atas: Avatar, Nama, Kelas, dan Jam -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0 {{ $v->visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/20' : ($v->visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-purple-500/15 text-purple-400 border border-purple-500/20') }}">
                                    {{ $v->visitor_type === 'siswa' ? '🎓' : ($v->visitor_type === 'guru' ? '👨‍🏫' : '🏛️') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-extrabold text-xs text-white truncate visitor-name" title="{{ $v->name }}">{{ $v->name }}</div>
                                    <div class="text-[10px] text-slate-400 truncate" title="{{ $v->class_or_institution ?: ($v->identity_number ?: '-') }}">{{ $v->class_or_institution ?: ($v->identity_number ?: '-') }}</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-mono font-bold text-slate-300 bg-slate-900 px-2 py-0.5 rounded border border-slate-800 inline-block">
                                    {{ $v->visited_at ? $v->visited_at->format('H:i') : '-' }} WIB
                                </span>
                            </div>
                        </div>

                        <!-- Baris Bawah: Keperluan Kunjungan -->
                        <div class="mt-2 pt-1.5 border-t border-slate-800/60 flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/20 max-w-[200px] truncate" title="{{ $v->purpose }}">
                                <span class="w-1 h-1 rounded-full bg-indigo-400 shrink-0"></span>
                                <span class="truncate">{{ $v->purpose }}</span>
                            </span>
                            @if(!empty($v->satisfaction_rating))
                                <span class="text-[9.5px] text-slate-400 shrink-0 font-medium truncate">
                                    {{ $v->satisfaction_rating }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div id="log-empty-state" class="h-full flex flex-col items-center justify-center text-center py-8 text-slate-500 text-xs space-y-1">
                        <div class="text-2xl opacity-40">📖</div>
                        <p class="font-semibold text-slate-400">Belum ada pengunjung tercatat hari ini.</p>
                        <p class="text-[10px] text-slate-500">Silakan isi formulir di samping untuk menjadi yang pertama!</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer Kolom Kanan: Tujuan Terbanyak -->
            @if($purposeStats->count() > 0)
                <div class="shrink-0 pt-2 border-t border-slate-800 space-y-1 mt-2">
                    <div class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400">Tujuan Terbanyak Hari Ini:</div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($purposeStats->take(3) as $ps)
                            <span class="px-2 py-0.5 rounded text-[9.5px] font-semibold bg-slate-950 border border-slate-800 text-slate-300 truncate inline-flex items-center gap-1">
                                <span class="truncate">{{ \Illuminate\Support\Str::limit($ps->purpose, 22) }}</span>: <strong class="text-indigo-400 font-mono">{{ $ps->total }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- =========================================================
         BOTTOM FOOTER BAR (COMPACT ~30px)
         ========================================================= -->
    <footer class="h-7 shrink-0 bg-slate-950/95 border-t border-slate-800/80 px-4 flex items-center justify-between text-[10px] text-slate-500 relative z-20">
        <span>&copy; {{ date('Y') }} {{ $schoolName ?? 'SMAN 1 Lengkong' }} &bull; Perpustakaan Digital SIS-SMA1LE</span>
        <span class="text-slate-400 hidden sm:inline">Presensi mandiri instan dengan memasukkan NISN bagi siswa atau NIP bagi guru/tendik.</span>
    </footer>

    <!-- =========================================================
         POPUP MODAL SUKSES DENGAN HITUNG MUNDUR RESET OTOMATIS
         ========================================================= -->
    <div id="modal-guest-success" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-gradient-to-b from-slate-900 to-indigo-950 border-2 border-emerald-500/50 rounded-3xl p-6 sm:p-8 max-w-sm sm:max-w-md w-full text-center shadow-2xl relative animate-in fade-in zoom-in duration-200">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-500/20 border-2 border-emerald-400/40 text-emerald-400 text-3xl sm:text-4xl flex items-center justify-center mx-auto mb-3 animate-bounce">
                🎉
            </div>
            
            <h3 class="text-xl sm:text-2xl font-black text-white mb-1">Presensi Berhasil Dicatat!</h3>
            <p id="modal-welcome-name" class="text-sm sm:text-base font-extrabold text-emerald-400 mb-2">Selamat Datang!</p>
            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                Terima kasih telah berkunjung ke Perpustakaan {{ $schoolName ?? 'SMA Negeri 1 Lengkong' }}. Selamat membaca dan belajar!
            </p>

            <!-- Countdown Timer Card -->
            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 mb-4">
                <div class="text-[11px] text-slate-400 mb-0.5">Formulir otomatis kembali ke awal dalam:</div>
                <div class="text-2xl sm:text-3xl font-black text-amber-400 font-mono tracking-wider">
                    <span id="countdown-number">5</span> Detik
                </div>
            </div>

            <button type="button" onclick="closeSuccessAndReset()" 
                    class="w-full py-2.5 sm:py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition border border-slate-700 cursor-pointer">
                Selesai (Tamu Selanjutnya)
            </button>
        </div>
    </div>

<!-- =============================================================
     JAVASCRIPT LOGIC KIOSK MANDIRI
     ============================================================= -->
<script>
    // 1. Digital Realtime Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('live-clock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Fullscreen Toggle
    function toggleKioskFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
            const icon = document.getElementById('fullscreen-icon');
            if (icon) icon.textContent = '✕';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                const icon = document.getElementById('fullscreen-icon');
                if (icon) icon.textContent = '⛶';
            }
        }
    }

    // 3. Visitor Type Switcher (Siswa, Guru, Umum)
    let currentVisitorType = 'siswa';
    function selectVisitorType(type) {
        currentVisitorType = type;
        document.getElementById('input-visitor-type').value = type;

        const btnSiswa = document.getElementById('btn-type-siswa');
        const btnGuru = document.getElementById('btn-type-guru');
        const btnUmum = document.getElementById('btn-type-umum');

        const activeClass = 'border-indigo-500 bg-indigo-950/60 text-indigo-300 font-bold shadow-md shadow-indigo-950/40';
        const inactiveClass = 'border-slate-800 bg-slate-950/60 text-slate-400 font-bold hover:border-slate-700';

        btnSiswa.className = `p-2 sm:p-2.5 rounded-xl border text-center transition cursor-pointer ${type === 'siswa' ? activeClass : inactiveClass}`;
        btnGuru.className = `p-2 sm:p-2.5 rounded-xl border text-center transition cursor-pointer ${type === 'guru' ? activeClass : inactiveClass}`;
        btnUmum.className = `p-2 sm:p-2.5 rounded-xl border text-center transition cursor-pointer ${type === 'umum' ? activeClass : inactiveClass}`;

        const boxId = document.getElementById('box-identity-number');
        const labelId = document.getElementById('label-identity-number');
        const inputId = document.getElementById('input-identity-number');
        const labelClass = document.getElementById('label-class-or-inst');
        const inputClass = document.getElementById('input-class-or-inst');

        if (type === 'siswa') {
            boxId.classList.remove('hidden');
            labelId.innerHTML = 'Langkah 2 &bull; Nomor Induk Siswa Nasional (NISN) <span class="text-rose-400">*</span>';
            inputId.placeholder = 'Masukkan 10 digit NISN Siswa...';
            labelClass.textContent = 'Kelas / Rombel Siswa';
            inputClass.placeholder = 'Contoh: XII Merdeka 7';
        } else if (type === 'guru') {
            boxId.classList.remove('hidden');
            labelId.innerHTML = 'Langkah 2 &bull; Nomor Induk Pegawai (NIP) / NUPTK <span class="text-rose-400">*</span>';
            inputId.placeholder = 'Masukkan NIP / NUPTK Guru atau Tendik...';
            labelClass.textContent = 'Mata Pelajaran / Jabatan';
            inputClass.placeholder = 'Contoh: Guru Matematika / Staf TU';
        } else {
            // Tamu Umum: Opsional Nomor Telepon / KTP
            boxId.classList.remove('hidden');
            labelId.innerHTML = 'Langkah 2 &bull; Nomor Kontak / HP (Opsional)';
            inputId.placeholder = 'Nomor WhatsApp / HP tamu...';
            labelClass.textContent = 'Asal Lembaga / Instansi / Alamat';
            inputClass.placeholder = 'Contoh: Komite Sekolah / Orang Tua Siswa';
        }

        inputId.focus();
        document.getElementById('lookup-result-status').classList.add('hidden');
    }

    // 4. Instant Realtime AJAX Lookup via NISN / NIP
    let lookupTimer = null;
    function debounceLookup(val) {
        clearTimeout(lookupTimer);
        const code = val.trim();
        const statusEl = document.getElementById('lookup-result-status');
        const spinner = document.getElementById('lookup-spinner');

        if (code.length < 4) {
            statusEl.classList.add('hidden');
            spinner.classList.add('hidden');
            return;
        }

        spinner.classList.remove('hidden');
        lookupTimer = setTimeout(() => {
            fetch(`{{ route('library.guest_book.lookup') }}?code=${encodeURIComponent(code)}&type=${currentVisitorType}`)
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    statusEl.classList.remove('hidden');

                    if (data.found) {
                        statusEl.className = 'mt-1 text-xs p-2 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 font-bold flex items-center gap-1.5';
                        statusEl.innerHTML = `<span>✅ Terdaftar:</span> <strong>${data.name}</strong> <span>(${data.class_or_institution || '-'})</span>`;

                        document.getElementById('input-name').value = data.name;
                        if (data.class_or_institution) {
                            document.getElementById('input-class-or-inst').value = data.class_or_institution;
                        }
                    } else {
                        statusEl.className = 'mt-1 text-xs p-2 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-300 font-medium flex items-center gap-1.5';
                        statusEl.innerHTML = `<span>ℹ️ Identitas belum terdaftar. Silakan lengkapi nama secara mandiri.</span>`;
                    }
                })
                .catch(() => {
                    spinner.classList.add('hidden');
                });
        }, 350);
    }

    // 5. Purpose Selector Buttons
    function selectPurpose(purpose) {
        document.getElementById('input-purpose').value = purpose;
        document.querySelectorAll('.btn-purpose').forEach(btn => {
            if (btn.getAttribute('data-purpose') === purpose) {
                btn.className = 'btn-purpose p-2 rounded-xl border text-left transition cursor-pointer flex items-center gap-2 bg-indigo-600 text-white font-bold border-indigo-500 shadow-sm';
            } else {
                btn.className = 'btn-purpose p-2 rounded-xl border text-left transition cursor-pointer flex items-center gap-2 text-slate-300 bg-slate-950/60 border-slate-800 hover:border-slate-700';
            }
        });
    }

    // 6. Satisfaction Rating Selector Buttons (Pilihan Suasana)
    function selectRating(rating) {
        const input = document.getElementById('input-satisfaction-rating');
        if (input) input.value = rating;

        document.querySelectorAll('.btn-rating').forEach(btn => {
            const r = btn.getAttribute('data-rating');
            if (r === rating) {
                let activeColor = 'bg-emerald-500/20 text-emerald-300 border-emerald-500 shadow-xs';
                if (rating === 'Nyaman') {
                    activeColor = 'bg-sky-500/20 text-sky-300 border-sky-500 shadow-xs';
                } else if (rating === 'Cukup') {
                    activeColor = 'bg-amber-500/20 text-amber-300 border-amber-500 shadow-xs';
                }
                btn.className = `btn-rating py-1.5 px-2 rounded-lg border text-center transition cursor-pointer flex items-center justify-center gap-1.5 font-bold text-[11px] ${activeColor}`;
            } else {
                btn.className = 'btn-rating py-1.5 px-2 rounded-lg border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-[11px] text-center transition cursor-pointer flex items-center justify-center gap-1.5';
            }
        });
    }

    // 7. Form Submission via AJAX dengan Modal Auto-Reset
    let countdownInterval = null;
    async function submitGuestBook(e) {
        e.preventDefault();
        const form = document.getElementById('kiosk-guest-form');
        const submitBtn = document.getElementById('btn-submit-guest');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> <span>Menyimpan Presensi...</span>';

        const formData = new FormData(form);

        try {
            const response = await fetch("{{ route('library.guest_book.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const res = await response.json();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>🚀 Kirim Presensi Kunjungan Sekarang</span>';

            if (res.status === 'success') {
                // Tampilkan Modal Sukses
                document.getElementById('modal-welcome-name').textContent = `Selamat Datang, ${res.visitor.name}!`;
                document.getElementById('modal-guest-success').classList.remove('hidden');

                // Update counter pengunjung hari ini
                if (res.today_count) {
                    document.getElementById('badge-total-today').textContent = res.today_count;
                }

                // Tambahkan langsung item ke log atas
                prependVisitorToLog(res.visitor);

                // Mulai hitung mundur 5 detik untuk reset form otomatis ke tamu selanjutnya
                startCountdownReset(5);
            } else {
                alert(res.message || 'Gagal menyimpan presensi kunjungan. Cek kembali data Anda.');
            }
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>🚀 Kirim Presensi Kunjungan Sekarang</span>';
            // Fallback submit biasa jika AJAX terhalang
            form.submit();
        }
    }

    function prependVisitorToLog(visitor) {
        const container = document.getElementById('today-visitors-container');
        const emptyState = document.getElementById('log-empty-state');
        if (emptyState) emptyState.remove();

        const icon = visitor.visitor_type === 'siswa' ? '🎓' : (visitor.visitor_type === 'guru' ? '👨‍🏫' : '🏛️');
        const badgeColor = visitor.visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/20' : (visitor.visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-purple-500/15 text-purple-400 border border-purple-500/20');

        const card = document.createElement('div');
        card.className = 'visitor-log-item p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-indigo-500/40 transition animate-in fade-in slide-in-from-top-2 duration-300';
        card.innerHTML = `
            <!-- Baris Atas: Avatar, Nama, Kelas, dan Jam -->
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0 ${badgeColor}">
                        ${icon}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-xs text-white truncate visitor-name" title="${visitor.name}">${visitor.name}</div>
                        <div class="text-[10px] text-slate-400 truncate" title="${visitor.class_or_institution || '-'}">${visitor.class_or_institution || '-'}</div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-mono font-bold text-slate-300 bg-slate-900 px-2 py-0.5 rounded border border-slate-800 inline-block">
                        ${visitor.time} WIB
                    </span>
                </div>
            </div>

            <!-- Baris Bawah: Keperluan Kunjungan -->
            <div class="mt-2 pt-1.5 border-t border-slate-800/60 flex items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/20 max-w-[200px] truncate" title="${visitor.purpose}">
                    <span class="w-1 h-1 rounded-full bg-indigo-400 shrink-0"></span>
                    <span class="truncate">${visitor.purpose}</span>
                </span>
                ${visitor.satisfaction_rating ? `<span class="text-[9.5px] text-slate-400 shrink-0 font-medium truncate">${visitor.satisfaction_rating}</span>` : ''}
            </div>
        `;

        container.insertBefore(card, container.firstChild);
    }

    function startCountdownReset(seconds) {
        let left = seconds;
        const el = document.getElementById('countdown-number');
        if (el) el.textContent = left;

        clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
            left--;
            if (el) el.textContent = left;
            if (left <= 0) {
                clearInterval(countdownInterval);
                closeSuccessAndReset();
            }
        }, 1000);
    }

    function closeSuccessAndReset() {
        clearInterval(countdownInterval);
        document.getElementById('modal-guest-success').classList.add('hidden');
        resetGuestForm();
    }

    function resetGuestForm() {
        const form = document.getElementById('kiosk-guest-form');
        form.reset();
        selectVisitorType('siswa');
        selectPurpose('Membaca Buku');
        selectRating('Sangat Nyaman');
        document.getElementById('lookup-result-status').classList.add('hidden');
        document.getElementById('input-identity-number').focus();
    }

    // 8. Mini Filter di Log Pengunjung
    function filterLog(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.visitor-log-item').forEach(item => {
            const nameEl = item.querySelector('.visitor-name');
            const text = nameEl ? nameEl.textContent.toLowerCase() : '';
            item.style.display = text.includes(q) ? 'block' : 'none';
        });
    }
</script>
</body>
</html>
