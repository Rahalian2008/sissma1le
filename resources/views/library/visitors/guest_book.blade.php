<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Buku Tamu Mandiri - {{ $schoolName ?? 'SMAN 1 Lengkong' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
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
            width: 5px;
        }
        .kiosk-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 9999px;
        }
        .kiosk-scroll::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 9999px;
        }
        .kiosk-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.7);
        }
    </style>
</head>
<body class="min-h-screen bg-[#050811] text-slate-100 p-3 sm:p-5 lg:p-7 selection:bg-indigo-500 selection:text-white">
<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         TOP KIOSK HEADER BAR (DESAIN MODERN TANPA KOP SEKOLAH)
         ========================================================= -->
    <header class="bg-gradient-to-r from-slate-900 via-indigo-950/70 to-slate-900 border border-slate-800/80 rounded-3xl p-5 sm:p-6 text-white shadow-2xl relative overflow-hidden backdrop-blur-xl">
        <div class="absolute -right-12 -bottom-12 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 w-80 h-80 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-5">
            
            <!-- Branding Identitas Perpustakaan & Sekolah (Tanpa Kop Surat) -->
            <div class="flex items-center gap-4 text-left w-full lg:w-auto">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 p-0.5 shadow-lg shadow-indigo-500/20 shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center overflow-hidden">
                        @if(!empty($schoolLogo))
                            <img src="{{ $schoolLogo }}" alt="Logo Sekolah" class="w-10 h-10 sm:w-11 sm:h-11 object-contain">
                        @else
                            <span class="text-2xl">📚</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[11px] font-extrabold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Kiosk Buku Tamu Mandiri</span>
                        </span>
                        <span class="text-[11px] font-semibold text-slate-400 hidden sm:inline">Presensi Mandiri Tanpa Login</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-white truncate">
                        Buku Tamu Perpustakaan
                    </h1>
                    <p class="text-xs sm:text-sm text-indigo-200/80 font-medium truncate">
                        {{ $schoolName ?? 'SMA Negeri 1 Lengkong' }} &bull; Presensi Kehadiran Pemustaka & Tamu
                    </p>
                </div>
            </div>

            <!-- Jam Realtime, Total Pengunjung & Kontrol Kiosk -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 justify-between lg:justify-end w-full lg:w-auto pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-800/80">
                
                <!-- Digital Clock Card -->
                <div class="text-center sm:text-right px-4 sm:px-5 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-800 shadow-inner">
                    <div id="live-clock" class="font-mono text-2xl sm:text-3xl font-black text-amber-400 tracking-wider">00:00:00</div>
                    <div id="live-date" class="text-[11px] text-slate-400 font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                </div>

                <!-- Pengunjung Counter Badge -->
                <div class="text-center px-4 sm:px-5 py-2.5 rounded-2xl bg-indigo-600/25 border border-indigo-500/30 shadow-inner min-w-[125px]">
                    <div id="badge-total-today" class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $todayCount }}</div>
                    <div class="text-[10px] text-indigo-200 uppercase font-bold tracking-wider">Pengunjung Hari Ini</div>
                </div>

                <!-- Kiosk Ecosystem Switcher -->
                <div class="hidden xl:flex items-center gap-1.5 bg-slate-950/80 border border-slate-800 rounded-2xl p-1.5 text-xs">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2.5 py-1.5 rounded-xl font-bold text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1">
                        <span>📺 Kios TV</span>
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" class="px-2.5 py-1.5 rounded-xl font-bold bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 flex items-center gap-1">
                        <span>📚 Kios Perpus</span>
                    </a>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2.5 py-1.5 rounded-xl font-bold text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1">
                        <span>👥 Kios Tamu</span>
                    </a>
                </div>

                <!-- Tombol Fullscreen & Admin Nav -->
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="toggleKioskFullscreen()" title="Layar Penuh (Fullscreen)" 
                            class="w-11 h-11 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center text-sm cursor-pointer shadow-sm">
                        <span id="fullscreen-icon">⛶</span>
                    </button>
                    <a href="{{ route('library.index') }}" title="Masuk ke Panel Pengelola Perpustakaan" 
                       class="w-11 h-11 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center text-sm cursor-pointer shadow-sm">
                        <span>⚙️</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- =========================================================
         MAIN DUAL GRID (KIRI: FORM MANDIRI | KANAN: LOG PENGUNJUNG)
         ========================================================= -->
    <main class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- =====================================================
             KOLOM KIRI: FORM ENTRI BUKU TAMU MANDIRI
             ===================================================== -->
        <div class="lg:col-span-7 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-white flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-indigo-500/20 text-indigo-400 text-base">✍️</span>
                        <span>Isi Presensi Kunjungan Anda</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Gunakan NISN/NIP untuk pengisian otomatis instan atau isi secara mandiri.</p>
                </div>
                <button type="button" onclick="resetGuestForm()" 
                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5 cursor-pointer border border-slate-700">
                    <span>🔄 Bersihkan Form</span>
                </button>
            </div>

            <form id="kiosk-guest-form" onsubmit="submitGuestBook(event)" class="space-y-6">
                @csrf
                <input type="hidden" name="visitor_type" id="input-visitor-type" value="siswa">

                <!-- 1. Pilihan Kategori Tamu (Tab Besar Sentuh Mudah) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2.5">
                        Langkah 1 &bull; Pilih Kategori Anda <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" id="btn-type-siswa" onclick="selectVisitorType('siswa')" 
                                class="p-4 rounded-2xl border-2 text-center transition cursor-pointer border-indigo-500 bg-indigo-950/50 text-indigo-300 font-bold shadow-lg shadow-indigo-950/50">
                            <div class="text-2xl sm:text-3xl mb-1.5">🎓</div>
                            <div class="text-xs sm:text-sm font-extrabold text-white">Siswa / Siswi</div>
                            <div class="text-[10px] text-indigo-300/70 font-medium mt-0.5">Gunakan NISN</div>
                        </button>

                        <button type="button" id="btn-type-guru" onclick="selectVisitorType('guru')" 
                                class="p-4 rounded-2xl border-2 border-slate-800 text-center transition cursor-pointer hover:border-slate-700 bg-slate-950/50 text-slate-400 font-bold">
                            <div class="text-2xl sm:text-3xl mb-1.5">👨‍🏫</div>
                            <div class="text-xs sm:text-sm font-extrabold text-white">Guru / Tendik</div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">Gunakan NIP</div>
                        </button>

                        <button type="button" id="btn-type-umum" onclick="selectVisitorType('umum')" 
                                class="p-4 rounded-2xl border-2 border-slate-800 text-center transition cursor-pointer hover:border-slate-700 bg-slate-950/50 text-slate-400 font-bold">
                            <div class="text-2xl sm:text-3xl mb-1.5">🏛️</div>
                            <div class="text-xs sm:text-sm font-extrabold text-white">Tamu Umum</div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">Wali / Pengunjung Luar</div>
                        </button>
                    </div>
                </div>

                <!-- 2. Kotak Input Identitas (NISN / NIP) dengan Live Instant Lookup -->
                <div id="box-identity-number" class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label id="label-identity-number" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Langkah 2 &bull; Nomor Induk Siswa Nasional (NISN) <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[11px] text-indigo-400 font-medium">Bisa scan kartu atau ketik langsung</span>
                    </div>
                    <div class="relative">
                        <input type="text" name="identity_number" id="input-identity-number" oninput="debounceLookup(this.value)" autofocus
                               placeholder="Masukkan 10 digit NISN Anda..." 
                               class="w-full px-5 py-4 text-base sm:text-lg font-mono font-bold bg-slate-950 border-2 border-slate-700 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition text-white placeholder-slate-600">
                        <div id="lookup-spinner" class="absolute right-4 top-4 hidden">
                            <span class="inline-block w-6 h-6 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>
                    <div id="lookup-result-status" class="mt-2 text-xs hidden"></div>
                </div>

                <!-- 3. Detail Nama Lengkap & Kelas/Instansi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Nama Lengkap Pengunjung <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="name" id="input-name" required placeholder="Nama lengkap..." 
                               class="w-full px-4 py-3 text-sm bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white font-semibold placeholder-slate-600">
                    </div>
                    <div class="space-y-1.5">
                        <label id="label-class-or-inst" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Kelas / Rombel Siswa
                        </label>
                        <input type="text" name="class_or_institution" id="input-class-or-inst" placeholder="Contoh: XII Merdeka 7" 
                               class="w-full px-4 py-3 text-sm bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-600">
                    </div>
                </div>

                <!-- 4. Keperluan Kunjungan (Visual Pills Selection) -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Langkah 3 &bull; Tujuan / Keperluan Kunjungan <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
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
                                    class="btn-purpose p-3 rounded-2xl border text-left transition cursor-pointer flex items-center gap-2.5 {{ $idx === 0 ? 'bg-indigo-600 text-white font-bold border-indigo-500 shadow-md shadow-indigo-600/30' : 'text-slate-300 bg-slate-950/60 border-slate-800 hover:border-slate-700' }}">
                                <span class="text-xl shrink-0">{{ $p['icon'] }}</span>
                                <span class="text-xs font-semibold truncate">{{ $p['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="purpose" id="input-purpose" value="Membaca Buku" required>
                </div>

                <!-- 5. Kepuasan Layanan & Catatan Tambahan (Opsional) -->
                <div class="p-4 bg-slate-950/60 rounded-2xl border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-300">Suasana Perpustakaan Hari Ini (Opsional)</label>
                        <span class="text-[10px] text-slate-500">Pilih salah satu</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5">
                        <button type="button" onclick="selectRating('Sangat Nyaman')" data-rating="Sangat Nyaman" id="btn-rating-sangat-nyaman"
                                class="btn-rating p-3 rounded-2xl border text-center transition cursor-pointer flex items-center justify-center gap-2 bg-emerald-500/20 text-emerald-300 border-emerald-500 shadow-md font-bold text-xs">
                            <span class="text-base">😍</span>
                            <span>Sangat Nyaman</span>
                        </button>
                        <button type="button" onclick="selectRating('Nyaman')" data-rating="Nyaman" id="btn-rating-nyaman"
                                class="btn-rating p-3 rounded-2xl border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-xs text-center transition cursor-pointer flex items-center justify-center gap-2">
                            <span class="text-base">😊</span>
                            <span>Nyaman</span>
                        </button>
                        <button type="button" onclick="selectRating('Cukup')" data-rating="Cukup" id="btn-rating-cukup"
                                class="btn-rating p-3 rounded-2xl border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-xs text-center transition cursor-pointer flex items-center justify-center gap-2">
                            <span class="text-base">🙂</span>
                            <span>Cukup</span>
                        </button>
                    </div>
                    <input type="hidden" name="satisfaction_rating" id="input-satisfaction-rating" value="Sangat Nyaman">

                    <input type="text" name="notes" id="input-notes" placeholder="Pesan, kritik, saran, atau judul buku yang Anda cari (opsional)..." 
                           class="w-full px-4 py-2.5 text-xs bg-slate-900 border border-slate-800 rounded-xl outline-none focus:border-indigo-500 text-white placeholder-slate-600">
                </div>

                <!-- Tombol Submit Presensi -->
                <div class="pt-1">
                    <button type="submit" id="btn-submit-guest" 
                            class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 hover:from-indigo-500 hover:to-sky-400 text-white font-black text-sm tracking-wide shadow-xl shadow-indigo-600/30 transition transform active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
                        <span>🚀 Kirim Presensi Kunjungan Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- =====================================================
             KOLOM KANAN: LOG PENGUNJUNG HARI INI & STATISTIK
             ===================================================== -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card Log Pengunjung Realtime -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl space-y-4">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm">📋</span>
                        <div>
                            <h3 class="text-sm font-black text-white">Log Pengunjung Hari Ini</h3>
                            <p class="text-[11px] text-slate-400">Daftar kehadiran pemustaka terkini</p>
                        </div>
                    </div>
                    <span id="log-count-pill" class="px-3 py-1 rounded-full text-xs bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 font-extrabold font-mono">
                        {{ $todayVisitors->count() }} Orang
                    </span>
                </div>

                <!-- Breakdown Statistik Kategori Hari Ini -->
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="text-lg font-black text-cyan-400 font-mono" id="stat-siswa-count">{{ $studentCount }}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">🎓 Siswa</div>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="text-lg font-black text-emerald-400 font-mono" id="stat-guru-count">{{ $teacherCount }}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">👨‍🏫 Guru/Staf</div>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="text-lg font-black text-purple-400 font-mono" id="stat-umum-count">{{ $generalCount }}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">🏛️ Umum</div>
                    </div>
                </div>

                <!-- Mini Pencarian Cepat di Log -->
                <div class="relative">
                    <input type="text" id="log-filter-search" oninput="filterLog(this.value)" placeholder="🔍 Cari nama atau kelas di daftar log..." 
                           class="w-full px-4 py-2.5 text-xs bg-slate-950 border border-slate-800 rounded-xl outline-none focus:border-indigo-500 text-white placeholder-slate-600">
                </div>

                <!-- Daftar Scrollable Pengunjung Hari Ini -->
                <div id="today-visitors-container" class="space-y-3 max-h-[460px] overflow-y-auto pr-1 kiosk-scroll">
                    @forelse($todayVisitors as $v)
                        <div class="visitor-log-item p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 hover:border-indigo-500/40 transition">
                            <!-- Baris Atas: Avatar, Nama, Kelas, dan Jam -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold shrink-0 {{ $v->visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/20' : ($v->visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-purple-500/15 text-purple-400 border border-purple-500/20') }}">
                                        {{ $v->visitor_type === 'siswa' ? '🎓' : ($v->visitor_type === 'guru' ? '👨‍🏫' : '🏛️') }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-extrabold text-xs sm:text-sm text-white truncate visitor-name" title="{{ $v->name }}">{{ $v->name }}</div>
                                        <div class="text-[11px] text-slate-400 truncate" title="{{ $v->class_or_institution ?: ($v->identity_number ?: '-') }}">{{ $v->class_or_institution ?: ($v->identity_number ?: '-') }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[11px] font-mono font-bold text-slate-300 bg-slate-900/90 px-2.5 py-1 rounded-lg border border-slate-800 inline-block shadow-xs">
                                        {{ $v->visited_at ? $v->visited_at->format('H:i') : '-' }} WIB
                                    </span>
                                </div>
                            </div>

                            <!-- Baris Bawah: Keperluan Kunjungan (Lega, Rapi, Truncated Bersih) -->
                            <div class="mt-2.5 pt-2 border-t border-slate-800/60 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/20 max-w-[260px] sm:max-w-xs truncate" title="{{ $v->purpose }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 shrink-0"></span>
                                    <span class="truncate">{{ $v->purpose }}</span>
                                </span>
                                @if(!empty($v->satisfaction_rating))
                                    <span class="text-[10px] text-slate-400 shrink-0 font-medium">
                                        {{ $v->satisfaction_rating }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div id="log-empty-state" class="text-center py-12 text-slate-500 text-xs space-y-2">
                            <div class="text-3xl opacity-40">📖</div>
                            <p>Belum ada pengunjung tercatat hari ini.<br>Silakan isi formulir di samping untuk menjadi yang pertama!</p>
                        </div>
                    @endforelse
                </div>

                @if($purposeStats->count() > 0)
                    <div class="pt-3 border-t border-slate-800 space-y-1.5">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tujuan Terbanyak Hari Ini:</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($purposeStats->take(4) as $ps)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-950 border border-slate-800 text-slate-300 max-w-full truncate inline-flex items-center gap-1">
                                    <span class="truncate">{{ \Illuminate\Support\Str::limit($ps->purpose, 28) }}</span>: <strong class="text-indigo-400 font-mono">{{ $ps->total }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- =========================================================
         POPUP MODAL SUKSES DENGAN HITUNG MUNDUR RESET OTOMATIS
         ========================================================= -->
    <div id="modal-guest-success" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-gradient-to-b from-slate-900 to-indigo-950 border-2 border-emerald-500/50 rounded-3xl p-8 max-w-md w-full text-center shadow-2xl relative animate-in fade-in zoom-in duration-200">
            <div class="w-20 h-20 rounded-full bg-emerald-500/20 border-2 border-emerald-400/40 text-emerald-400 text-4xl flex items-center justify-center mx-auto mb-4 animate-bounce">
                🎉
            </div>
            
            <h3 class="text-2xl font-black text-white mb-1">Presensi Berhasil Dicatat!</h3>
            <p id="modal-welcome-name" class="text-base font-extrabold text-emerald-400 mb-2">Selamat Datang!</p>
            <p class="text-xs text-slate-300 leading-relaxed mb-6">
                Terima kasih telah berkunjung ke Perpustakaan {{ $schoolName ?? 'SMA Negeri 1 Lengkong' }}. Selamat membaca dan berkarya!
            </p>

            <!-- Countdown Timer Card -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 mb-6">
                <div class="text-xs text-slate-400 mb-1">Formulir otomatis kembali ke awal dalam:</div>
                <div class="text-3xl font-black text-amber-400 font-mono tracking-wider">
                    <span id="countdown-number">5</span> Detik
                </div>
            </div>

            <button type="button" onclick="closeSuccessAndReset()" 
                    class="w-full py-3.5 px-6 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition border border-slate-700 cursor-pointer">
                Selesai (Tamu Selanjutnya)
            </button>
        </div>
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
            document.getElementById('fullscreen-icon').textContent = '✕';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                document.getElementById('fullscreen-icon').textContent = '⛶';
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

        const activeClass = 'border-indigo-500 bg-indigo-950/50 text-indigo-300 font-bold shadow-lg shadow-indigo-950/50';
        const inactiveClass = 'border-slate-800 bg-slate-950/50 text-slate-400 font-bold';

        btnSiswa.className = `p-4 rounded-2xl border-2 text-center transition cursor-pointer ${type === 'siswa' ? activeClass : inactiveClass}`;
        btnGuru.className = `p-4 rounded-2xl border-2 text-center transition cursor-pointer ${type === 'guru' ? activeClass : inactiveClass}`;
        btnUmum.className = `p-4 rounded-2xl border-2 text-center transition cursor-pointer ${type === 'umum' ? activeClass : inactiveClass}`;

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
                        statusEl.className = 'mt-2 text-xs p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 font-bold flex items-center gap-2';
                        statusEl.innerHTML = `<span>✅ Data Ditemukan:</span> <strong>${data.name}</strong> <span>(${data.class_or_institution || '-'})</span>`;

                        document.getElementById('input-name').value = data.name;
                        if (data.class_or_institution) {
                            document.getElementById('input-class-or-inst').value = data.class_or_institution;
                        }
                    } else {
                        statusEl.className = 'mt-2 text-xs p-3 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 font-medium flex items-center gap-2';
                        statusEl.innerHTML = `<span>ℹ️ Identitas belum terdaftar di database. Silakan isi nama secara mandiri.</span>`;
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
                btn.className = 'btn-purpose p-3 rounded-2xl border text-left transition cursor-pointer flex items-center gap-2.5 bg-indigo-600 text-white font-bold border-indigo-500 shadow-md shadow-indigo-600/30';
            } else {
                btn.className = 'btn-purpose p-3 rounded-2xl border text-left transition cursor-pointer flex items-center gap-2.5 text-slate-300 bg-slate-950/60 border-slate-800 hover:border-slate-700';
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
                let activeColor = 'bg-emerald-500/20 text-emerald-300 border-emerald-500 shadow-md';
                if (rating === 'Nyaman') {
                    activeColor = 'bg-sky-500/20 text-sky-300 border-sky-500 shadow-md';
                } else if (rating === 'Cukup') {
                    activeColor = 'bg-amber-500/20 text-amber-300 border-amber-500 shadow-md';
                }
                btn.className = `btn-rating p-3 rounded-2xl border text-center transition cursor-pointer flex items-center justify-center gap-2 font-bold text-xs ${activeColor}`;
            } else {
                btn.className = 'btn-rating p-3 rounded-2xl border border-slate-800 bg-slate-900 text-slate-300 hover:border-slate-700 font-semibold text-xs text-center transition cursor-pointer flex items-center justify-center gap-2';
            }
        });
    }

    // 6. Form Submission via AJAX dengan Modal Auto-Reset
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
        card.className = 'visitor-log-item p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 hover:border-indigo-500/40 transition animate-in fade-in slide-in-from-top-2 duration-300';
        card.innerHTML = `
            <!-- Baris Atas: Avatar, Nama, Kelas, dan Jam -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold shrink-0 ${badgeColor}">
                        ${icon}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-xs sm:text-sm text-white truncate visitor-name" title="${visitor.name}">${visitor.name}</div>
                        <div class="text-[11px] text-slate-400 truncate" title="${visitor.class_or_institution || '-'}">${visitor.class_or_institution || '-'}</div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[11px] font-mono font-bold text-slate-300 bg-slate-900/90 px-2.5 py-1 rounded-lg border border-slate-800 inline-block shadow-xs">
                        ${visitor.time} WIB
                    </span>
                </div>
            </div>

            <!-- Baris Bawah: Keperluan Kunjungan (Lega, Rapi, Truncated Bersih) -->
            <div class="mt-2.5 pt-2 border-t border-slate-800/60 flex items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/20 max-w-[260px] sm:max-w-xs truncate" title="${visitor.purpose}">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 shrink-0"></span>
                    <span class="truncate">${visitor.purpose}</span>
                </span>
                ${visitor.satisfaction_rating ? `<span class="text-[10px] text-slate-400 shrink-0 font-medium">${visitor.satisfaction_rating}</span>` : ''}
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

    // 7. Mini Filter di Log Pengunjung
    function filterLog(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.visitor-log-item').forEach(item => {
            const nameEl = item.querySelector('.visitor-name');
            const text = nameEl ? nameEl.textContent.toLowerCase() : '';
            item.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }
</script>
</body>
</html>
