<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Informasi Pos Satpam - Perizinan Siswa | {{ $school?->name ?? 'SMAN 1 Lengkong' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-teal-500 selection:text-white">

    <!-- Top Header Bar Kios Satpam -->
    <header class="bg-slate-900/90 border-b border-slate-800 sticky top-0 z-40 backdrop-blur-md px-4 sm:px-6 py-3.5 shadow-xl">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/25 shrink-0">
                    <span class="text-2xl">🛡️</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-black tracking-wide text-white uppercase flex items-center gap-2">
                            <span>Kios Pos Satpam &bull; Perizinan Siswa</span>
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>LIVE MONITOR</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ $school?->name ?? 'SMAN 1 Lengkong' }} &bull; Khusus Petugas Keamanan &amp; Guru Piket
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-end md:self-center flex-wrap">
                <!-- Live Jam Digital Realtime -->
                <div class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-right">
                    <div id="live-clock" class="text-sm sm:text-base font-black font-mono text-teal-400 tracking-wider">--:--:-- WIB</div>
                    <div id="live-date" class="text-[10px] text-slate-400 font-semibold">{{ $today->translatedFormat('l, d F Y') }}</div>
                </div>

                <!-- Tombol Fullscreen & Navigasi -->
                <button type="button" onclick="toggleFullScreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition" title="Layar Penuh (F11)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button type="button" onclick="window.location.reload()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-400 hover:text-teal-300 border border-slate-700 transition" title="Segarkan Data Sekarang">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <span>&larr; Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 space-y-5">

        <!-- Flash Message Notification -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 text-xs sm:text-sm font-bold flex items-center justify-between gap-3 shadow-lg animate-fade-in">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        <!-- Quick Search & Status Indicators Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center text-xl shrink-0 font-bold">
                    🏃
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 font-bold block">Pulang Cepat</span>
                    <span class="text-lg font-black text-white font-mono">{{ $stats['total_approved_early_leaves'] }}</span>
                    <span class="text-[10px] text-slate-500 block">Siswa Disetujui</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/15 text-teal-400 flex items-center justify-center text-xl shrink-0 font-bold">
                    🚪
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 font-bold block">Izin Keluar</span>
                    <span class="text-lg font-black text-white font-mono">{{ $stats['total_approved_exit_permits'] }}</span>
                    <span class="text-[10px] text-slate-500 block">Perizinan Rombongan</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-amber-500/30 shadow-sm flex items-center gap-3 relative overflow-hidden">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-xl shrink-0 font-bold">
                    🔵
                </div>
                <div>
                    <span class="text-[11px] text-amber-400 font-bold block">Sedang di Luar</span>
                    <span class="text-lg font-black text-white font-mono">{{ $stats['students_currently_outside'] }}</span>
                    <span class="text-[10px] text-slate-500 block">Siswa Belum Kembali</span>
                </div>
                @if($stats['students_currently_outside'] > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                @endif
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-xl shrink-0 font-bold">
                    ✓
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 font-bold block">Sudah Kembali</span>
                    <span class="text-lg font-black text-white font-mono">{{ $stats['students_returned'] }}</span>
                    <span class="text-[10px] text-slate-500 block">Siswa Selesai Izin</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Satpam -->
        <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="kiosk-search" oninput="filterKioskCards(this.value)" placeholder="Cari nama siswa, kelas, NIS, atau nomor surat..." class="w-full bg-slate-950 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold self-stretch sm:self-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1.5 p-1 bg-slate-950 rounded-xl border border-slate-800">
                    <button type="button" onclick="switchKioskSection('all')" id="btn-kiosk-all" class="px-3 py-1.5 rounded-lg bg-teal-600 text-white font-bold transition">Semua</button>
                    <button type="button" onclick="switchKioskSection('exit-permit')" id="btn-kiosk-exit" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition">🚪 Izin Keluar ({{ $exitPermits->count() }})</button>
                    <button type="button" onclick="switchKioskSection('early-leave')" id="btn-kiosk-early" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition">🏃 Pulang Cepat ({{ $earlyLeaves->count() }})</button>
                </div>
                <div class="text-[11px] text-slate-500 flex items-center gap-1 font-mono">
                    <span>Auto-refresh:</span>
                    <span id="refresh-timer" class="text-teal-400 font-bold">30s</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Izin Keluar Sekolah Sementara (School Pass) -->
        <section id="section-exit-permit" class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🚪</span>
                    <h2 class="text-sm sm:text-base font-extrabold text-white">Izin Keluar Lingkungan Sekolah (Sementara)</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-500/20 text-teal-300 font-mono">
                        {{ $exitPermits->count() }} Surat
                    </span>
                </div>
                <span class="text-xs text-slate-500">Hari ini: {{ $today->format('d/m/Y') }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="exit-permits-container">
                @forelse($exitPermits as $sp)
                    @php
                        $isOutside = ($sp->status === 'APPROVED' && !$sp->actual_back_time && $sp->is_returning);
                        $studentNames = $sp->students->pluck('name')->join(', ');
                        $classes = $sp->students->map(fn($st) => $st->schoolClass?->name)->filter()->unique()->join(', ');
                    @endphp
                    <div class="kiosk-card p-4 sm:p-5 rounded-2xl bg-slate-900 border {{ $isOutside ? 'border-amber-500/60 shadow-lg shadow-amber-500/10' : 'border-slate-800' }} space-y-3 transition"
                         data-search="{{ strtolower($studentNames . ' ' . $classes . ' ' . $sp->permit_number . ' ' . $sp->purpose . ' ' . ($sp->destination ?? '')) }}">
                        
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-mono font-bold text-xs sm:text-sm text-teal-400 block">{{ $sp->permit_number }}</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Disetujui oleh: <strong class="text-slate-300">{{ $sp->approver?->name ?? 'Guru Piket' }}</strong>
                                </div>
                            </div>
                            <div>
                                @if($isOutside)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1.5 animate-pulse">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        <span>DI LUAR SEKOLAH</span>
                                    </span>
                                @elseif($sp->status === 'RETURNED' || $sp->actual_back_time)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5">
                                        <span>✓ SUDAH KEMBALI</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                        ✓ DISETUJUI
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Waktu & Tujuan -->
                        <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">WAKTU KELUAR</span>
                                <span class="font-mono font-bold text-teal-300 text-sm">🚪 {{ $sp->out_time }} WIB</span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">ESTIMASI KEMBALI</span>
                                @if($sp->is_returning)
                                    <span class="font-mono font-bold text-amber-300 text-sm">⏰ {{ $sp->expected_back_time ?? '-' }} WIB</span>
                                @else
                                    <span class="text-slate-400 font-semibold text-xs">Langsung Pulang</span>
                                @endif
                            </div>
                            @if($sp->actual_back_time)
                                <div class="col-span-2 pt-1.5 border-t border-slate-800 text-emerald-400 font-bold flex items-center gap-1 text-[11px]">
                                    <span>↩️ Kembali ke Sekolah:</span>
                                    <span class="font-mono">{{ $sp->actual_back_time }} WIB</span>
                                </div>
                            @endif
                        </div>

                        <!-- Keperluan & Lokasi Tujuan -->
                        <div class="text-xs">
                            <span class="text-slate-500 text-[10px] font-bold uppercase block">Keperluan &amp; Tujuan</span>
                            <p class="font-bold text-slate-200 mt-0.5">{{ $sp->purpose }}</p>
                            @if($sp->destination)
                                <p class="text-slate-400 text-[11px] mt-0.5">📍 Lokasi: {{ $sp->destination }}</p>
                            @endif
                            <p class="text-slate-400 text-[10px] mt-0.5">
                                Pendamping: <strong>{{ $sp->accompanied_by ?: 'Mandiri / Tanpa Pendamping' }}</strong>
                            </p>
                        </div>

                        <!-- Daftar Siswa Rombongan -->
                        <div>
                            <span class="text-slate-500 text-[10px] font-bold uppercase block mb-1.5">
                                Siswa Diberi Izin ({{ $sp->students->count() }} Orang):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($sp->students as $st)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs bg-slate-800 text-slate-200 border border-slate-700/80 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                                        <span>{{ $st->name }}</span>
                                        <span class="text-slate-400 font-mono text-[10px]">({{ $st->schoolClass?->name ?? '-' }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tombol Konfirmasi Satpam -->
                        @if($isOutside)
                            <div class="pt-2 border-t border-slate-800 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-amber-400 font-semibold flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                                    <span>Tunggu konfirmasi kedatangan</span>
                                </span>
                                <form action="{{ route('attendance.kiosk-satpam.return', $sp->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Konfirmasi bahwa rombongan siswa No. {{ $sp->permit_number }} telah kembali ke lingkungan sekolah?');">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-md shadow-emerald-600/30 transition cursor-pointer flex items-center gap-1.5">
                                        <span>↩️ Konfirmasi Kembali</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-slate-900/60 rounded-2xl border border-slate-800/80 text-slate-500">
                        <div class="text-3xl mb-1">🚪</div>
                        <p class="font-bold text-slate-400 text-sm">Tidak ada izin keluar lingkungan sekolah yang aktif hari ini.</p>
                        <p class="text-xs text-slate-500 mt-0.5">Semua perizinan resmi yang disetujui guru piket akan otomatis tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Section 2: Izin Pulang Cepat (Early Leave) -->
        <section id="section-early-leave" class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏃</span>
                    <h2 class="text-sm sm:text-base font-extrabold text-white">Izin Pulang Cepat Hari Ini (Disetujui)</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 font-mono">
                        {{ $earlyLeaves->count() }} Siswa
                    </span>
                </div>
                <span class="text-xs text-slate-500">Pemberitahuan Pos Gerbang</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="early-leaves-container">
                @forelse($earlyLeaves as $el)
                    @php
                        $st = $el->student;
                    @endphp
                    <div class="kiosk-card p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 hover:border-slate-700 transition"
                         data-search="{{ strtolower(($st?->name ?? '') . ' ' . ($st?->schoolClass?->name ?? '') . ' ' . ($st?->nis ?? '') . ' ' . $el->letter_number . ' ' . $el->reason_category . ' ' . ($el->pickup_name ?? '')) }}">
                        
                        <!-- Identitas Siswa -->
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-600 to-orange-500 text-white font-black text-lg flex items-center justify-center shrink-0 shadow-md">
                                {{ substr($st?->name ?? 'S', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-white text-sm truncate leading-tight">{{ $st?->name ?? 'Siswa' }}</h3>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Kelas: <strong class="text-slate-200">{{ $st?->schoolClass?->name ?? '-' }}</strong> &bull; NIS: <span class="font-mono">{{ $st?->nis ?? '-' }}</span>
                                </div>
                                <span class="font-mono text-[10px] text-teal-400 block mt-0.5">{{ $el->letter_number }}</span>
                            </div>
                        </div>

                        <!-- Jam Pulang & Alasan -->
                        <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800/80 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 text-[10px] font-bold">JAM PULANG RESMI</span>
                                <span class="font-mono font-black text-amber-400 text-sm">⏰ {{ $el->leave_time }} WIB</span>
                            </div>
                            <div class="text-[11px] font-bold text-slate-300">
                                {{ $el->reason_category }}
                            </div>
                            <p class="text-[11px] text-slate-400 italic line-clamp-2">"{{ $el->reason }}"</p>
                        </div>

                        <!-- Info Penjemput & Petugas Penyetuju -->
                        <div class="text-xs space-y-1 pt-1">
                            @if($el->pickup_name)
                                <div class="text-[11px] text-slate-300">
                                    🚗 Dijemput: <strong>{{ $el->pickup_name }}</strong> ({{ $el->pickup_relationship ?? 'Keluarga' }})
                                    @if($el->pickup_phone)
                                        <div class="text-[10px] text-slate-400 font-mono">Telp/WA: {{ $el->pickup_phone }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="text-[11px] text-slate-400 italic">
                                    🚶 Pulang Mandiri (Diizinkan)
                                </div>
                            @endif
                            <div class="text-[10px] text-slate-500 pt-1 border-t border-slate-800">
                                Disetujui: <strong class="text-slate-400">{{ $el->approver?->name ?? 'Guru Piket' }}</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-slate-900/60 rounded-2xl border border-slate-800/80 text-slate-500">
                        <div class="text-3xl mb-1">🏃</div>
                        <p class="font-bold text-slate-400 text-sm">Belum ada siswa yang diizinkan pulang cepat hari ini.</p>
                        <p class="text-xs text-slate-500 mt-0.5">Surat keterangan pulang cepat resmi akan tampil otomatis saat disetujui guru piket.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>

    <!-- Bottom Footer Bar -->
    <footer class="bg-slate-900/80 border-t border-slate-800/80 py-3 px-4 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $school?->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Terpadu Keamanan Gerbang Sekolah</span>
            <span class="text-[11px] text-slate-400">Petugas Satpam dapat memeriksa izin siswa dengan memverifikasi kartu/surat izin resmi.</span>
        </div>
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
            const btnAll = document.getElementById('btn-kiosk-all');
            const btnExit = document.getElementById('btn-kiosk-exit');
            const btnEarly = document.getElementById('btn-kiosk-early');

            // Reset buttons
            [btnAll, btnExit, btnEarly].forEach(btn => {
                if (btn) {
                    btn.className = 'px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition';
                }
            });

            if (type === 'all') {
                if (secExit) secExit.style.display = '';
                if (secEarly) secEarly.style.display = '';
                if (btnAll) btnAll.className = 'px-3 py-1.5 rounded-lg bg-teal-600 text-white font-bold transition';
            } else if (type === 'exit-permit') {
                if (secExit) secExit.style.display = '';
                if (secEarly) secEarly.style.display = 'none';
                if (btnExit) btnExit.className = 'px-3 py-1.5 rounded-lg bg-teal-600 text-white font-bold transition';
            } else if (type === 'early-leave') {
                if (secExit) secExit.style.display = 'none';
                if (secEarly) secEarly.style.display = '';
                if (btnEarly) btnEarly.className = 'px-3 py-1.5 rounded-lg bg-teal-600 text-white font-bold transition';
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
