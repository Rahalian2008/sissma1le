<!DOCTYPE html>
<html lang="id" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kios Informasi Pos Satpam - Perizinan Siswa | {{ $school?->name ?? 'SMAN 1 Lengkong' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @keyframes pulseSlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
        .pulse-slow {
            animation: pulseSlow 2.5s infinite ease-in-out;
        }
        .kiosk-scroll::-webkit-scrollbar { width: 4px; }
        .kiosk-scroll::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.4); }
        .kiosk-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 9999px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 h-screen max-h-screen overflow-hidden flex flex-col justify-between selection:bg-teal-500 selection:text-white">

    <!-- Top Header Bar Kios Satpam -->
    <header class="bg-slate-900/90 border-b border-slate-800 shrink-0 z-40 backdrop-blur-md px-3 sm:px-6 py-2.5 shadow-xl">
        <div class="max-w-7xl mx-auto flex flex-wrap sm:flex-nowrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 shrink-0 min-w-0">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/25 shrink-0">
                    <span class="text-xl">🛡️</span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm sm:text-base font-black tracking-wide text-white uppercase flex items-center gap-2 truncate">
                            <span>Kios Pos Satpam &bull; Perizinan Siswa</span>
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>LIVE MONITOR</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 truncate">
                        {{ $school?->name ?? 'SMAN 1 Lengkong' }} &bull; Khusus Petugas Keamanan &amp; Guru Piket
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 flex-wrap justify-end">
                <!-- Unified Kiosk Ecosystem Switcher -->
                <div class="flex items-center gap-1 bg-slate-950/80 border border-slate-800 rounded-xl p-1 text-[11px] font-bold overflow-x-auto no-scrollbar">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios TV Display">
                        <span>📺 Kios TV</span>
                    </a>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Buku Tamu">
                        <span>👥 Kios Tamu</span>
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Perpustakaan">
                        <span>📚 Kios Perpus</span>
                    </a>
                    <a href="{{ route('attendance.kiosk-satpam') }}" class="px-2 py-1 rounded-lg bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1 whitespace-nowrap">
                        <span>🛡️ Kios Satpam</span>
                    </a>
                    <a href="{{ route('school_bell.index') }}" target="_blank" class="hidden md:flex px-2 py-1 rounded-lg text-amber-400 hover:text-amber-300 hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Buka Bel Sekolah">
                        <span>🔔 Bel</span>
                    </a>
                    <a href="{{ route('website.home') }}" target="_blank" class="hidden lg:flex px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Kembali ke Portal Sekolah">
                        <span>🌐 Web</span>
                    </a>
                </div>

                <!-- Live Jam Digital Realtime -->
                <div class="px-2.5 sm:px-3 py-1 rounded-xl bg-slate-800/80 border border-slate-700/80 text-right shrink-0">
                    <div id="live-clock" class="text-xs sm:text-sm font-black font-mono text-teal-400 tracking-wider">--:--:-- WIB</div>
                    <div id="live-date" class="text-[9px] text-slate-400 font-semibold">{{ $today->translatedFormat('l, d F Y') }}</div>
                </div>

                <!-- Tombol Fullscreen & Navigasi -->
                <button type="button" onclick="toggleFullScreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition" title="Layar Penuh (F11)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button type="button" onclick="window.location.reload()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-400 hover:text-teal-300 border border-slate-700 transition" title="Segarkan Data Sekarang">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <a href="{{ route('attendance.index') }}" class="px-2 sm:px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition flex items-center gap-1 shrink-0">
                    <span>&larr; Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Top KPI & Search Bar Container (Shrink-0) -->
    <div class="shrink-0 max-w-7xl w-full mx-auto px-4 sm:px-6 pt-2.5 pb-1 space-y-2">
        <!-- Quick Search & Status Indicators Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center text-lg shrink-0 font-bold">
                    🏃
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block">Pulang Cepat</span>
                    <span class="text-base font-black text-white font-mono leading-none">{{ $stats['total_approved_early_leaves'] }}</span>
                    <span class="text-[9px] text-slate-500 block">Disetujui</span>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-500/15 text-teal-400 flex items-center justify-center text-lg shrink-0 font-bold">
                    🚪
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block">Izin Keluar</span>
                    <span class="text-base font-black text-white font-mono leading-none">{{ $stats['total_approved_exit_permits'] }}</span>
                    <span class="text-[9px] text-slate-500 block">Surat Rombongan</span>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-900 border border-amber-500/30 shadow-sm flex items-center gap-2.5 relative overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-400 flex items-center justify-center text-lg shrink-0 font-bold">
                    🔵
                </div>
                <div>
                    <span class="text-[10px] text-amber-400 font-bold block">Sedang di Luar</span>
                    <span class="text-base font-black text-white font-mono leading-none">{{ $stats['students_currently_outside'] }}</span>
                    <span class="text-[9px] text-slate-500 block">Belum Kembali</span>
                </div>
                @if($stats['students_currently_outside'] > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                @endif
            </div>

            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-lg shrink-0 font-bold">
                    ✓
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block">Sudah Kembali</span>
                    <span class="text-base font-black text-white font-mono leading-none">{{ $stats['students_returned'] }}</span>
                    <span class="text-[9px] text-slate-500 block">Siswa Selesai</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Satpam -->
        <div class="p-2 bg-slate-900/90 rounded-xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="kiosk-search" oninput="filterKioskCards(this.value)" placeholder="Cari nama siswa, kelas, NIS, atau no surat..." class="w-full bg-slate-950 border border-slate-700/80 rounded-lg pl-9 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
            </div>

            <div class="flex items-center gap-2 font-semibold self-stretch sm:self-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1 p-0.5 bg-slate-950 rounded-lg border border-slate-800 text-[11px]">
                    <button type="button" onclick="switchKioskSection('all')" id="btn-kiosk-all" class="px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition">Semua</button>
                    <button type="button" onclick="switchKioskSection('exit-permit')" id="btn-kiosk-exit" class="px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition">🚪 Izin Keluar ({{ $exitPermits->count() }})</button>
                    <button type="button" onclick="switchKioskSection('early-leave')" id="btn-kiosk-early" class="px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition">🏃 Pulang Cepat ({{ $earlyLeaves->count() }})</button>
                </div>
                <div class="text-[10px] text-slate-500 flex items-center gap-1 font-mono">
                    <span>Auto:</span>
                    <span id="refresh-timer" class="text-teal-400 font-bold">30s</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container (Strictly 1-Screen Viewport Fit) -->
    <main class="flex-1 min-h-0 max-w-7xl w-full mx-auto px-4 sm:px-6 py-2 overflow-hidden flex flex-col">

        <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-2 gap-3.5 overflow-hidden" id="kiosk-sections-grid">
            <!-- Section 1: Izin Keluar Sekolah Sementara (School Pass) -->
            <section id="section-exit-permit" class="h-full flex flex-col min-h-0 rounded-2xl bg-slate-900/70 border border-slate-800 p-3 sm:p-4 overflow-hidden">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg">🚪</span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-white">Izin Keluar Lingkungan Sekolah (Sementara)</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-500/20 text-teal-300 font-mono">
                            {{ $exitPermits->count() }} Surat
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-500">Hari ini: {{ $today->format('d/m/Y') }}</span>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2.5 pr-1 mt-2" id="exit-permits-container">
                    @forelse($exitPermits as $sp)
                        @php
                            $isOutside = ($sp->status === 'APPROVED' && !$sp->actual_back_time && $sp->is_returning);
                            $studentNames = $sp->students->pluck('name')->join(', ');
                            $classes = $sp->students->map(fn($st) => $st->schoolClass?->name)->filter()->unique()->join(', ');
                        @endphp
                        <div class="kiosk-card p-3 sm:p-3.5 rounded-xl bg-slate-950/70 border {{ $isOutside ? 'border-amber-500/60 shadow-lg shadow-amber-500/10' : 'border-slate-800' }} space-y-2.5 transition"
                             data-search="{{ strtolower($studentNames . ' ' . $classes . ' ' . $sp->permit_number . ' ' . $sp->purpose . ' ' . ($sp->destination ?? '')) }}">
                            
                            <!-- Header Card -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono font-bold text-xs text-teal-400 block">{{ $sp->permit_number }}</span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Penyetuju: <strong class="text-slate-300">{{ $sp->approver?->name ?? 'Guru Piket' }}</strong>
                                    </div>
                                </div>
                                <div>
                                    @if($isOutside)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span>DI LUAR SEKOLAH</span>
                                        </span>
                                    @elseif($sp->status === 'RETURNED' || $sp->actual_back_time)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1">
                                            <span>✓ SUDAH KEMBALI</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                            ✓ DISETUJUI
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Waktu & Tujuan -->
                            <div class="p-2 rounded-lg bg-slate-900/90 border border-slate-800/80 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-500 text-[9px] block font-semibold">WAKTU KELUAR</span>
                                    <span class="font-mono font-bold text-teal-300 text-xs">🚪 {{ $sp->out_time }} WIB</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[9px] block font-semibold">ESTIMASI KEMBALI</span>
                                    @if($sp->is_returning)
                                        <span class="font-mono font-bold text-amber-300 text-xs">⏰ {{ $sp->expected_back_time ?? '-' }} WIB</span>
                                    @else
                                        <span class="text-slate-400 font-semibold text-xs">Langsung Pulang</span>
                                    @endif
                                </div>
                                @if($sp->actual_back_time)
                                    <div class="col-span-2 pt-1 border-t border-slate-800 text-emerald-400 font-bold flex items-center gap-1 text-[10px]">
                                        <span>↩️ Kembali:</span>
                                        <span class="font-mono">{{ $sp->actual_back_time }} WIB</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Keperluan & Lokasi Tujuan -->
                            <div class="text-xs">
                                <span class="text-slate-500 text-[9px] font-bold uppercase block">Keperluan:</span>
                                <p class="font-bold text-slate-200 text-xs mt-0.5">{{ $sp->purpose }}</p>
                                @if($sp->destination)
                                    <p class="text-slate-400 text-[10px] mt-0.5">📍 Lokasi: {{ $sp->destination }}</p>
                                @endif
                                <p class="text-slate-400 text-[10px] mt-0.5">
                                    Pendamping: <strong>{{ $sp->accompanied_by ?: 'Mandiri / Tanpa Pendamping' }}</strong>
                                </p>
                            </div>

                            <!-- Daftar Siswa Rombongan -->
                            <div>
                                <span class="text-slate-500 text-[9px] font-bold uppercase block mb-1">
                                    Siswa Izin ({{ $sp->students->count() }} Orang):
                                </span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sp->students as $st)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-slate-900 text-slate-200 border border-slate-800 font-semibold">
                                            <span class="w-1 h-1 rounded-full bg-teal-400"></span>
                                            <span>{{ $st->name }}</span>
                                            <span class="text-slate-400 font-mono text-[9px]">({{ $st->schoolClass?->name ?? '-' }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tombol Konfirmasi Satpam -->
                            @if($isOutside)
                                <div class="pt-1.5 border-t border-slate-800 flex items-center justify-between gap-2">
                                    <span class="text-[10px] text-amber-400 font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                        <span>Menunggu kembali</span>
                                    </span>
                                    <form action="{{ route('attendance.kiosk-satpam.return', $sp->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Konfirmasi bahwa rombongan siswa No. {{ $sp->permit_number }} telah kembali ke lingkungan sekolah?');">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-[11px] shadow-sm transition cursor-pointer flex items-center gap-1">
                                            <span>↩️ Konfirmasi Kembali</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center bg-slate-950/60 rounded-xl border border-slate-800/80 text-slate-500">
                            <div class="text-2xl mb-1">🚪</div>
                            <p class="font-bold text-slate-400 text-xs">Tidak ada izin keluar lingkungan sekolah yang aktif hari ini.</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Semua perizinan resmi yang disetujui guru piket akan otomatis tampil di sini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Section 2: Izin Pulang Cepat (Early Leave) -->
            <section id="section-early-leave" class="h-full flex flex-col min-h-0 rounded-2xl bg-slate-900/70 border border-slate-800 p-3 sm:p-4 overflow-hidden">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg">🏃</span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-white">Izin Pulang Cepat Hari Ini (Disetujui)</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 font-mono">
                            {{ $earlyLeaves->count() }} Siswa
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-500">Pemberitahuan Pos Gerbang</span>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2.5 pr-1 mt-2" id="early-leaves-container">
                    @forelse($earlyLeaves as $el)
                        @php
                            $st = $el->student;
                        @endphp
                        <div class="kiosk-card p-3 sm:p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2 hover:border-slate-700 transition"
                             data-search="{{ strtolower(($st?->name ?? '') . ' ' . ($st?->schoolClass?->name ?? '') . ' ' . ($st?->nis ?? '') . ' ' . $el->letter_number . ' ' . $el->reason_category . ' ' . ($el->pickup_name ?? '')) }}">
                            
                            <!-- Identitas Siswa -->
                            <div class="flex items-start gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                                    {{ substr($st?->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-white text-xs truncate leading-tight">{{ $st?->name ?? 'Siswa' }}</h3>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Kelas: <strong class="text-slate-200">{{ $st?->schoolClass?->name ?? '-' }}</strong> &bull; NIS: <span class="font-mono">{{ $st?->nis ?? '-' }}</span>
                                    </div>
                                    <span class="font-mono text-[9px] text-teal-400 block mt-0.5">{{ $el->letter_number }}</span>
                                </div>
                            </div>

                            <!-- Jam Pulang & Alasan -->
                            <div class="p-2 rounded-lg bg-slate-900/90 border border-slate-800/80 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-[9px] font-bold">JAM PULANG RESMI</span>
                                    <span class="font-mono font-black text-amber-400 text-xs">⏰ {{ $el->leave_time }} WIB</span>
                                </div>
                                <div class="text-[10px] font-bold text-slate-300">
                                    {{ $el->reason_category }}
                                </div>
                                <p class="text-[10px] text-slate-400 italic line-clamp-2">"{{ $el->reason }}"</p>
                            </div>

                            <!-- Info Penjemput & Petugas Penyetuju -->
                            <div class="text-xs space-y-0.5 pt-0.5">
                                @if($el->pickup_name)
                                    <div class="text-[10px] text-slate-300">
                                        🚗 Dijemput: <strong>{{ $el->pickup_name }}</strong> ({{ $el->pickup_relationship ?? 'Keluarga' }})
                                        @if($el->pickup_phone)
                                            <span class="text-[9px] text-slate-400 font-mono">Telp: {{ $el->pickup_phone }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-[10px] text-slate-400 italic">
                                        🚶 Pulang Mandiri (Diizinkan)
                                    </div>
                                @endif
                                <div class="text-[9px] text-slate-500 pt-0.5 border-t border-slate-800">
                                    Disetujui: <strong class="text-slate-400">{{ $el->approver?->name ?? 'Guru Piket' }}</strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center bg-slate-950/60 rounded-xl border border-slate-800/80 text-slate-500">
                            <div class="text-2xl mb-1">🏃</div>
                            <p class="font-bold text-slate-400 text-xs">Belum ada siswa yang diizinkan pulang cepat hari ini.</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Surat keterangan pulang cepat resmi akan tampil otomatis saat disetujui guru piket.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>

    <!-- Bottom Footer Bar (Compact ~32px) -->
    <footer class="h-8 shrink-0 bg-slate-900/90 border-t border-slate-800/80 px-4 flex items-center justify-between text-[11px] text-slate-500">
        <span>&copy; {{ date('Y') }} {{ $school?->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Terpadu Keamanan Gerbang Sekolah</span>
        <span class="text-slate-400 hidden sm:inline">Petugas Satpam memeriksa izin siswa dengan memverifikasi kartu/surat izin resmi.</span>
    </footer>

    <!-- Scripts: Jam Digital, Search & Auto-Refresh -->
    <script>
        // Jam Digital Realtime
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const elem = document.getElementById('live-clock');
            if (elem) {
                elem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto Refresh Countdown (30 Detik)
        let countdown = 30;
        const timerElem = document.getElementById('refresh-timer');
        setInterval(() => {
            countdown--;
            if (timerElem) {
                timerElem.textContent = `${countdown}s`;
            }
            if (countdown <= 0) {
                window.location.reload();
            }
        }, 1000);

        // Filter Pencarian Kios Realtime
        function filterKioskCards(query) {
            query = query.toLowerCase().trim();
            const cards = document.querySelectorAll('.kiosk-card');
            cards.forEach(card => {
                const searchText = card.getAttribute('data-search') || '';
                if (!query || searchText.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Switch Sections (Semua, Izin Keluar, Pulang Cepat)
        function switchKioskSection(type) {
            const secExit = document.getElementById('section-exit-permit');
            const secEarly = document.getElementById('section-early-leave');
            const grid = document.getElementById('kiosk-sections-grid');
            const btnAll = document.getElementById('btn-kiosk-all');
            const btnExit = document.getElementById('btn-kiosk-exit');
            const btnEarly = document.getElementById('btn-kiosk-early');

            // Reset buttons
            [btnAll, btnExit, btnEarly].forEach(btn => {
                if (btn) {
                    btn.className = 'px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition';
                }
            });

            if (type === 'all') {
                if (secExit) { secExit.classList.remove('hidden'); }
                if (secEarly) { secEarly.classList.remove('hidden'); }
                if (grid) { grid.className = 'flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-2 gap-3.5 overflow-hidden'; }
                if (btnAll) btnAll.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition';
            } else if (type === 'exit-permit') {
                if (secExit) { secExit.classList.remove('hidden'); }
                if (secEarly) { secEarly.classList.add('hidden'); }
                if (grid) { grid.className = 'flex-1 min-h-0 grid grid-cols-1 gap-3.5 overflow-hidden'; }
                if (btnExit) btnExit.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition';
            } else if (type === 'early-leave') {
                if (secExit) { secExit.classList.add('hidden'); }
                if (secEarly) { secEarly.classList.remove('hidden'); }
                if (grid) { grid.className = 'flex-1 min-h-0 grid grid-cols-1 gap-3.5 overflow-hidden'; }
                if (btnEarly) btnEarly.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition';
            }
        }

        // Fullscreen Mode
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</body>
</html>
