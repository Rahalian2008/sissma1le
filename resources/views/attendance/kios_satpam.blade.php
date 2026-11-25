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
        .kiosk-scroll::-webkit-scrollbar-thumb { background: rgba(20, 184, 166, 0.35); border-radius: 9999px; }
        .kiosk-scroll::-webkit-scrollbar-thumb:hover { background: rgba(20, 184, 166, 0.65); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 h-screen max-h-screen overflow-hidden flex flex-col justify-between selection:bg-teal-500 selection:text-white">

    <!-- Top Header Bar Kios Satpam (Clean, unified single-row layout) -->
    <header class="h-14 shrink-0 bg-slate-900/95 border-b border-slate-800 z-40 backdrop-blur-md px-3 sm:px-6 shadow-xl flex items-center">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between gap-3">
            
            <!-- Left: Brand Identity -->
            <div class="flex items-center gap-3 shrink-0 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/25 shrink-0">
                    <span class="text-lg">🛡️</span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xs sm:text-sm md:text-base font-black tracking-wide text-white uppercase flex items-center gap-1.5 truncate">
                            <span>KIOS POS SATPAM</span>
                            <span class="text-slate-500 hidden sm:inline">&bull;</span>
                            <span class="text-teal-300 hidden sm:inline">PERIZINAN SISWA</span>
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>LIVE MONITOR</span>
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 truncate">
                        {{ $school?->name ?? 'SMAN 1 Lengkong' }} &bull; Pengawasan Gerbang &amp; Perizinan Siswa
                    </p>
                </div>
            </div>

            <!-- Middle: Unified Ecosystem Switcher (Desktop & Tablet) -->
            <div class="hidden md:flex items-center gap-1 bg-slate-950/80 border border-slate-800 rounded-xl p-1 text-[11px] font-bold overflow-x-auto no-scrollbar shrink-0">
                <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios TV Display">
                    <span>📺 Kios TV</span>
                </a>
                <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Perpustakaan">
                    <span>📚 Kios Perpus</span>
                </a>
                <a href="{{ route('guests.kiosk') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Buku Tamu">
                    <span>👥 Kios Tamu</span>
                </a>
                <a href="{{ route('attendance.kiosk-satpam') }}" class="px-2 py-1 rounded-lg bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1 whitespace-nowrap">
                    <span>🛡️ Kios Satpam</span>
                </a>
                <a href="{{ route('school_bell.index') }}" target="_blank" class="hidden lg:flex px-2 py-1 rounded-lg text-amber-400 hover:text-amber-300 hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Buka Bel Sekolah">
                    <span>🔔 Bel</span>
                </a>
                <a href="{{ route('website.home') }}" target="_blank" class="hidden xl:flex px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Kembali ke Portal Sekolah">
                    <span>🌐 Web</span>
                </a>
            </div>

            <!-- Right: Jam Digital & Controls -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Live Jam Digital Realtime -->
                <div class="px-2.5 sm:px-3 py-1 rounded-xl bg-slate-800/80 border border-slate-700/80 text-right shrink-0">
                    <div id="live-clock" class="text-xs sm:text-sm font-black font-mono text-teal-400 tracking-wider leading-tight">--:--:-- WIB</div>
                    <div id="live-date" class="text-[9px] text-slate-400 font-semibold">{{ $today->translatedFormat('d M Y') }}</div>
                </div>

                <!-- Tombol Fullscreen & Navigasi -->
                <button type="button" onclick="toggleFullScreen()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition flex items-center justify-center text-xs" title="Layar Penuh (F11)">
                    <span id="fullscreen-icon">⛶</span>
                </button>
                <button type="button" onclick="window.location.reload()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-400 hover:text-teal-300 border border-slate-700 transition flex items-center justify-center text-xs" title="Segarkan Data Sekarang">
                    <span>🔄</span>
                </button>
                <a href="{{ route('attendance.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition flex items-center gap-1 shrink-0">
                    <span>&larr; Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Top KPI & Search Bar Container (Shrink-0) -->
    <div class="shrink-0 max-w-7xl w-full mx-auto px-3 sm:px-6 pt-2 pb-1 space-y-2">
        <!-- Quick Search & Status Indicators Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
            <div class="p-2 sm:p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center text-base shrink-0 font-bold">
                    🏃
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] text-slate-400 font-bold block">Pulang Cepat</span>
                    <span class="text-sm sm:text-base font-black text-white font-mono leading-tight">{{ $stats['total_approved_early_leaves'] }}</span>
                    <span class="text-[8.5px] sm:text-[9px] text-slate-500 block">Disetujui</span>
                </div>
            </div>

            <div class="p-2 sm:p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-500/15 text-teal-400 flex items-center justify-center text-base shrink-0 font-bold">
                    🚪
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] text-slate-400 font-bold block">Izin Keluar</span>
                    <span class="text-sm sm:text-base font-black text-white font-mono leading-tight">{{ $stats['total_approved_exit_permits'] }}</span>
                    <span class="text-[8.5px] sm:text-[9px] text-slate-500 block">Surat Rombongan</span>
                </div>
            </div>

            <div class="p-2 sm:p-2.5 rounded-xl bg-slate-900 border border-amber-500/30 shadow-sm flex items-center gap-2.5 relative overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-400 flex items-center justify-center text-base shrink-0 font-bold">
                    🔵
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] text-amber-400 font-bold block">Sedang di Luar</span>
                    <span class="text-sm sm:text-base font-black text-white font-mono leading-tight">{{ $stats['students_currently_outside'] }}</span>
                    <span class="text-[8.5px] sm:text-[9px] text-slate-500 block">Belum Kembali</span>
                </div>
                @if($stats['students_currently_outside'] > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                @endif
            </div>

            <div class="p-2 sm:p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-base shrink-0 font-bold">
                    ✓
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] text-slate-400 font-bold block">Sudah Kembali</span>
                    <span class="text-sm sm:text-base font-black text-white font-mono leading-tight">{{ $stats['students_returned'] }}</span>
                    <span class="text-[8.5px] sm:text-[9px] text-slate-500 block">Siswa Selesai</span>
                </div>
            </div>

            <div class="p-2 sm:p-2.5 rounded-xl bg-slate-900 border border-indigo-500/30 shadow-sm flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center text-base shrink-0 font-bold">
                    👥
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] text-indigo-300 font-bold block">Buku Tamu</span>
                    <span class="text-sm sm:text-base font-black text-white font-mono leading-tight">{{ $stats['today_guests'] ?? 0 }}</span>
                    <span class="text-[8.5px] sm:text-[9px] text-slate-400 block">{{ $stats['guests_in_school'] ?? 0 }} di Sekolah</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Satpam -->
        <div class="p-2 bg-slate-900/90 rounded-xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="kiosk-search" oninput="filterKioskCards(this.value)" placeholder="Cari nama siswa, tamu, instansi, tiket, plat nomor..." class="w-full bg-slate-950 border border-slate-700/80 rounded-lg pl-9 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
            </div>

            <div class="flex items-center gap-2 font-semibold self-stretch sm:self-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1 p-0.5 bg-slate-950 rounded-lg border border-slate-800 text-[11px] overflow-x-auto">
                    <button type="button" onclick="switchKioskSection('all')" id="btn-kiosk-all" class="px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition">Semua</button>
                    <button type="button" onclick="switchKioskSection('exit-permit')" id="btn-kiosk-exit" class="px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition whitespace-nowrap">🚪 Izin Keluar ({{ $exitPermits->count() }})</button>
                    <button type="button" onclick="switchKioskSection('early-leave')" id="btn-kiosk-early" class="px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition whitespace-nowrap">🏃 Pulang Cepat ({{ $earlyLeaves->count() }})</button>
                    <button type="button" onclick="switchKioskSection('guests')" id="btn-kiosk-guests" class="px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition whitespace-nowrap">👥 Buku Tamu ({{ $todayGuests->count() }})</button>
                </div>
                <div class="text-[10px] text-slate-500 flex items-center gap-1 font-mono">
                    <span>Auto:</span>
                    <span id="refresh-timer" class="text-teal-400 font-bold">30s</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container (Strictly 1-Screen Viewport Fit) -->
    <main class="flex-1 min-h-0 max-w-7xl w-full mx-auto px-3 sm:px-6 py-2 overflow-hidden flex flex-col">

        <div class="flex-1 min-h-0 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5 overflow-hidden" id="kiosk-sections-grid">
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
                                        <span>✓ Kembali Pukul:</span>
                                        <span class="font-mono">{{ $sp->actual_back_time }} WIB</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Siswa yang Izin -->
                            <div>
                                <span class="text-slate-500 text-[9px] block font-semibold uppercase tracking-wider mb-1">
                                    Daftar Siswa ({{ $sp->students->count() }} Orang):
                                </span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sp->students as $st)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10.5px] text-slate-200">
                                            <span class="font-semibold text-white">{{ $st->name }}</span>
                                            <span class="text-teal-400 text-[9.5px]">({{ $st->schoolClass?->name ?? '-' }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Keperluan & Tujuan -->
                            <div class="text-[11px] text-slate-300">
                                <span class="text-slate-500 text-[9px] block font-semibold">KEPERLUAN:</span>
                                <p class="text-slate-200 font-medium">{{ $sp->purpose }}</p>
                                @if(!empty($sp->destination))
                                    <p class="text-[10px] text-slate-400 mt-0.5">Tujuan: {{ $sp->destination }}</p>
                                @endif
                            </div>

                            @if($sp->is_returning && $sp->status === 'APPROVED' && empty($sp->actual_back_time))
                                <form action="{{ route('attendance.kiosk-satpam.return', $sp->id) }}" method="POST" class="pt-2 border-t border-slate-800/80">
                                    @csrf
                                    <input type="hidden" name="actual_back_time" value="{{ now()->format('H:i') }}">
                                    <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm shadow-teal-600/30 cursor-pointer">
                                        <span>✓</span>
                                        <span>Konfirmasi Kembali</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-10 text-slate-500 text-xs space-y-1">
                            <span class="text-3xl">🚪</span>
                            <p class="font-bold text-slate-400 text-xs">Tidak ada izin keluar lingkungan sekolah yang aktif hari ini.</p>
                            <p class="text-[10px] text-slate-500">Semua perizinan resmi yang disetujui guru piket akan otomatis tampil di sini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Section 2: Izin Pulang Cepat Hari Ini -->
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
                        <div class="kiosk-card p-3 sm:p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2 transition"
                             data-search="{{ strtolower(($el->student?->name ?? '') . ' ' . ($el->student?->schoolClass?->name ?? '') . ' ' . ($el->student?->nis ?? '') . ' ' . $el->reason_category . ' ' . ($el->reason ?? '')) }}">
                            
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($el->student?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-extrabold text-xs sm:text-sm text-white truncate">{{ $el->student?->name ?? 'Nama Siswa' }}</h3>
                                        <p class="text-[10px] text-teal-300 truncate">{{ $el->student?->schoolClass?->name ?? '-' }} &bull; NIS: {{ $el->student?->nis ?? '-' }}</p>
                                        <div class="font-mono text-[9.5px] text-slate-400 mt-0.5 truncate">
                                            No. Surat: <span class="text-teal-300 font-bold">{{ $el->letter_number }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 shrink-0">
                                    ✓ PULANG
                                </span>
                            </div>

                            <div class="p-2 rounded-lg bg-slate-900/90 border border-slate-800/80 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-500 text-[9px] block font-semibold">WAKTU PULANG</span>
                                    <span class="font-mono font-bold text-amber-300 text-xs">🏃 {{ substr($el->leave_time ?? '', 0, 5) }} WIB</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[9px] block font-semibold">PENJEMPUT</span>
                                    <span class="font-semibold text-slate-300 text-xs">{{ $el->pickup_name ?? $el->picked_up_by ?? 'Mandiri / Keluarga' }}</span>
                                </div>
                            </div>

                            <div class="text-[11px] text-slate-300">
                                <span class="text-slate-500 text-[9px] block font-semibold">ALASAN PULANG CEPAT:</span>
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[10px] font-semibold border border-slate-800 inline-block mr-1">
                                    {{ $el->reason_category ?? 'Izin Khusus' }}
                                </span>
                                <p class="text-slate-300 text-xs mt-1">{{ $el->reason }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-10 text-slate-500 text-xs space-y-1">
                            <span class="text-3xl">🏃</span>
                            <p class="font-bold text-slate-400 text-xs">Belum ada siswa yang diizinkan pulang cepat hari ini.</p>
                            <p class="text-[10px] text-slate-500">Surat keterangan pulang cepat resmi akan tampil otomatis saat disetujui guru piket.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Section 3: Buku Tamu Digital Terpadu di Lingkungan Sekolah -->
            <section id="section-guests" class="h-full flex flex-col min-h-0 rounded-2xl bg-slate-900/70 border border-slate-800 p-3 sm:p-4 overflow-hidden">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg">👥</span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-white">Daftar Kunjungan Tamu Sekolah</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/20 text-indigo-300 font-mono">
                            {{ $todayGuests->count() }} Tamu
                        </span>
                    </div>
                    <a href="{{ route('guests.kiosk') }}" target="_blank" class="text-[10px] text-indigo-400 hover:text-indigo-300 hover:underline flex items-center gap-1 font-semibold">
                        <span>+ Kiosk Tamu</span> &rarr;
                    </a>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto kiosk-scroll space-y-2.5 pr-1 mt-2" id="guests-container">
                    @forelse($todayGuests as $guest)
                        @php
                            $inSchool = is_null($guest->check_out_at) && $guest->status !== 'dibatalkan';
                        @endphp
                        <div class="kiosk-card p-3 sm:p-3.5 rounded-xl bg-slate-950/70 border {{ $inSchool ? 'border-indigo-500/60 shadow-lg shadow-indigo-500/10' : 'border-slate-800' }} space-y-2.5 transition"
                             data-search="{{ strtolower($guest->name . ' ' . $guest->ticket_number . ' ' . $guest->institution . ' ' . $guest->destination_department . ' ' . ($guest->vehicle_license_plate ?? '')) }}">
                            
                            <!-- Header Card Tamu -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    @if($guest->selfie_path)
                                        <img src="{{ $guest->selfie_path }}" alt="{{ $guest->name }}" class="w-9 h-9 rounded-lg object-cover border border-slate-700 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-indigo-900/60 text-indigo-300 font-bold flex items-center justify-center shrink-0 text-xs">
                                            {{ strtoupper(substr($guest->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <span class="font-mono font-bold text-xs text-indigo-400 block">{{ $guest->ticket_number }}</span>
                                        <h3 class="text-xs sm:text-sm font-black text-white truncate">{{ $guest->name }}</h3>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($inSchool)
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            Di Sekolah
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-black bg-slate-800 text-slate-400 border border-slate-700">
                                            Selesai ({{ $guest->check_out_at?->format('H:i') }} WIB)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Detail Kunjungan -->
                            <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-400 bg-slate-900/60 p-2 rounded-lg border border-slate-800/80">
                                <div>
                                    <span class="text-slate-500 block">Asal Instansi:</span>
                                    <span class="font-semibold text-slate-200 block truncate">{{ $guest->institution }}</span>
                                    <span class="text-[9px] text-slate-500 block truncate">{{ $guest->institution_type }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Tujuan &amp; Keperluan:</span>
                                    <span class="font-semibold text-indigo-300 block truncate">{{ $guest->destination_department }}</span>
                                    <span class="text-[9px] text-slate-300 block truncate">{{ $guest->purpose }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Waktu Masuk:</span>
                                    <span class="font-mono font-bold text-slate-300">{{ $guest->check_in_at?->format('H:i') }} WIB</span>
                                    <span class="text-[9px] text-slate-500 ml-1">({{ $guest->number_of_guests }} Org)</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Kendaraan &amp; Plat:</span>
                                    <span class="font-semibold text-slate-300">{{ $guest->vehicle_type ?? '-' }}</span>
                                    @if($guest->vehicle_license_plate)
                                        <span class="font-mono text-[9px] px-1 py-0.2 bg-slate-800 text-amber-300 rounded ml-1">{{ $guest->vehicle_license_plate }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Satpam Check-out -->
                            @if($inSchool)
                                <div class="pt-1 flex items-center justify-between border-t border-slate-800/60">
                                    <span class="text-[10px] text-slate-500 font-mono">Status: Sedang Berkunjung</span>
                                    <form action="{{ route('attendance.kiosk-satpam.guest-checkout', $guest) }}" method="POST" onsubmit="return confirm('Konfirmasi tamu {{ $guest->name }} ({{ $guest->ticket_number }}) telah keluar dari gerbang sekolah?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] transition shadow-xs flex items-center gap-1 cursor-pointer">
                                            <span>🚪 Checkout Tamu</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-10 text-slate-500 text-xs space-y-1">
                            <span class="text-3xl">👥</span>
                            <p class="font-bold text-slate-400 text-xs">Belum ada kunjungan tamu tercatat hari ini.</p>
                            <p class="text-[10px] text-slate-500">Tamu yang mendaftar melalui Kiosk Tamu akan muncul otomatis di sini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>

    <!-- Bottom Footer Bar (Compact ~30px) -->
    <footer class="h-7 shrink-0 bg-slate-900/95 border-t border-slate-800/80 px-4 flex items-center justify-between text-[10.5px] text-slate-500">
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

        // Switch Sections (Semua, Izin Keluar, Pulang Cepat, Buku Tamu)
        function switchKioskSection(type) {
            const secExit = document.getElementById('section-exit-permit');
            const secEarly = document.getElementById('section-early-leave');
            const secGuests = document.getElementById('section-guests');
            const grid = document.getElementById('kiosk-sections-grid');
            const btnAll = document.getElementById('btn-kiosk-all');
            const btnExit = document.getElementById('btn-kiosk-exit');
            const btnEarly = document.getElementById('btn-kiosk-early');
            const btnGuests = document.getElementById('btn-kiosk-guests');

            // Reset buttons
            [btnAll, btnExit, btnEarly, btnGuests].forEach(btn => {
                if (btn) {
                    btn.className = 'px-2.5 py-1 rounded-md text-slate-400 hover:text-white transition whitespace-nowrap';
                }
            });

            if (type === 'all') {
                if (secExit) secExit.classList.remove('hidden');
                if (secEarly) secEarly.classList.remove('hidden');
                if (secGuests) secGuests.classList.remove('hidden');
                if (grid) grid.className = 'flex-1 min-h-0 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5 overflow-hidden';
                if (btnAll) btnAll.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition whitespace-nowrap';
            } else if (type === 'exit-permit') {
                if (secExit) secExit.classList.remove('hidden');
                if (secEarly) secEarly.classList.add('hidden');
                if (secGuests) secGuests.classList.add('hidden');
                if (grid) grid.className = 'flex-1 min-h-0 grid grid-cols-1 gap-3.5 overflow-hidden';
                if (btnExit) btnExit.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition whitespace-nowrap';
            } else if (type === 'early-leave') {
                if (secExit) secExit.classList.add('hidden');
                if (secEarly) secEarly.classList.remove('hidden');
                if (secGuests) secGuests.classList.add('hidden');
                if (grid) grid.className = 'flex-1 min-h-0 grid grid-cols-1 gap-3.5 overflow-hidden';
                if (btnEarly) btnEarly.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition whitespace-nowrap';
            } else if (type === 'guests') {
                if (secExit) secExit.classList.add('hidden');
                if (secEarly) secEarly.classList.add('hidden');
                if (secGuests) secGuests.classList.remove('hidden');
                if (grid) grid.className = 'flex-1 min-h-0 grid grid-cols-1 gap-3.5 overflow-hidden';
                if (btnGuests) btnGuests.className = 'px-2.5 py-1 rounded-md bg-teal-600 text-white font-bold transition whitespace-nowrap';
            }
        }

        // Fullscreen Mode
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
                const icon = document.getElementById('fullscreen-icon');
                if (icon) icon.textContent = '✕';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(err => {});
                    const icon = document.getElementById('fullscreen-icon');
                    if (icon) icon.textContent = '⛶';
                }
            }
        }
    </script>
</body>
</html>
