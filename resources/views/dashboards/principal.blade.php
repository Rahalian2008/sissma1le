@extends('layouts.app')

@section('title', 'Dashboard Kontroling & Pengawasan Eksekutif Kepala Sekolah')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 max-w-7xl mx-auto px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    <!-- Header Eksekutif Kepala Sekolah -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-white shadow-2xl border border-slate-800">
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold tracking-wide">
                Pusat Pengawasan Eksekutif
            </span>
            <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                MODE KONTROLING AKTIF
            </span>
        </div>
        <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Kepala SMAN 1 Lengkong</h1>
        <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
            Pusat supervisi dan kendali mutu sekolah: pemantauan terpadu 8 agenda inti (Presensi Utama, Tata Tertib, Shalat Dzuhur, Shalat Dhuha, Keputrian, Ekstrakurikuler, Tahfidz Juz 30, dan 7 KAIH) secara real-time.
        </p>
    </div>

    <!-- Notice Bar Fungsi Kontroling Khusus Kepala Sekolah -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-950/40 via-slate-900/60 to-cyan-950/40 border border-indigo-500/30 text-indigo-200 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center shrink-0 font-bold text-base border border-indigo-500/30">
                🛡️
            </div>
            <div>
                <span class="font-extrabold text-white uppercase tracking-wide">Fungsi Pengawasan & Kontroling Terpadu:</span>
                <span class="text-slate-300 block sm:inline"> Akses kendali eksekutif penuh ke seluruh lini operasional: Administrasi TU, Kurikulum/LMS, Kesiswaan, Keuangan BKU, Sarana BMD, dan Pos Keamanan.</span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 font-mono text-[11px]">
                Total Siswa: <strong class="text-white">{{ $totalStudents }}</strong>
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 font-mono text-[11px]">
                Kasus SP: <strong class="text-rose-200">{{ $criticalGuidanceStudents->count() }}</strong>
            </span>
        </div>
    </div>

    <!-- PUSAT KENDALI EKSEKUTIF UTAMA (EXECUTIVE COMMAND CENTER) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm sm:text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                <span>🎛️</span> Pusat Kendali Operasional Sekolah (Executive Command Center)
            </h2>
            <span class="text-xs text-slate-400 font-medium">Navigasi Terintegrasi 1-Klik</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Hub 1: Adm. Ketatausahaan (TU) & SIPD e-BMD -->
            <div class="bg-gradient-to-br from-emerald-950/40 via-slate-900 to-slate-900 p-4 sm:p-5 rounded-3xl border border-emerald-500/30 shadow-lg flex flex-col justify-between hover:border-emerald-500/60 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30">
                            🏛️
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-mono">
                            Tata Usaha
                        </span>
                    </div>
                    <h3 class="font-extrabold text-sm sm:text-base text-white group-hover:text-emerald-300 transition">
                        Adm. Ketatausahaan &amp; Aset BMD
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                        Pusat kendali BKU Kas BOS/BOPD, KIB-KIR Aset BMD Jawa Barat, DUK Kepegawaian &amp; Persuratan/Disposisi.
                    </p>
                </div>
                <div class="pt-3 mt-3 border-t border-white/5 space-y-1.5">
                    <a href="{{ route('tu.dashboard') }}" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl flex items-center justify-between transition shadow-xs">
                        <span>Pusat Kendali TU Terpadu</span>
                        <span>&rarr;</span>
                    </a>
                    <div class="grid grid-cols-2 gap-1.5 text-[10.5px]">
                        <a href="{{ route('tu.keuangan.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            💰 BKU Kas
                        </a>
                        <a href="{{ route('tu.sarpras.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            📦 Aset KIB-KIR
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hub 2: Supervisi Kurikulum & KBM Terpadu -->
            <div class="bg-gradient-to-br from-indigo-950/40 via-slate-900 to-slate-900 p-4 sm:p-5 rounded-3xl border border-indigo-500/30 shadow-lg flex flex-col justify-between hover:border-indigo-500/60 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-lg border border-indigo-500/30">
                            📚
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-mono">
                            Kurikulum
                        </span>
                    </div>
                    <h3 class="font-extrabold text-sm sm:text-base text-white group-hover:text-indigo-300 transition">
                        Supervisi Pembelajaran &amp; LMS
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                        Pantau realisasi Jurnal Mengajar Guru, materi &amp; tugas siswa di LMS, serta pelaksanaan Asesmen CBT.
                    </p>
                </div>
                <div class="pt-3 mt-3 border-t border-white/5 space-y-1.5">
                    <a href="{{ route('students.learning-agendas.index') }}" class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl flex items-center justify-between transition shadow-xs">
                        <span>Buku Jurnal KBM Kelas</span>
                        <span>&rarr;</span>
                    </a>
                    <div class="grid grid-cols-2 gap-1.5 text-[10.5px]">
                        <a href="{{ route('lms.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            💻 LMS Guru
                        </a>
                        <a href="{{ route('cbt.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            📝 Ujian CBT
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hub 3: Kesiswaan, Disiplin & Karakter -->
            <div class="bg-gradient-to-br from-amber-950/40 via-slate-900 to-slate-900 p-4 sm:p-5 rounded-3xl border border-amber-500/30 shadow-lg flex flex-col justify-between hover:border-amber-500/60 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-500/30">
                            👥
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono">
                            Kesiswaan
                        </span>
                    </div>
                    <h3 class="font-extrabold text-sm sm:text-base text-white group-hover:text-amber-300 transition">
                        Kesiswaan, Tatib &amp; Konseling
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                        Monitoring surat peringatan (SP), pembinaan konseling BP/BK, serta papan kehormatan prestasi siswa.
                    </p>
                </div>
                <div class="pt-3 mt-3 border-t border-white/5 space-y-1.5">
                    <a href="{{ route('discipline.report') }}" class="w-full py-2 px-3 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold text-xs rounded-xl flex items-center justify-between transition shadow-xs">
                        <span>Laporan Tata Tertib &amp; SP</span>
                        <span>&rarr;</span>
                    </a>
                    <div class="grid grid-cols-2 gap-1.5 text-[10.5px]">
                        <a href="{{ route('achievements.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            🏆 Prestasi Siswa
                        </a>
                        <a href="{{ route('bk.dashboard') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            🧭 Konseling BK
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hub 4: Sarana, Keamanan & Informasi Publik -->
            <div class="bg-gradient-to-br from-sky-950/40 via-slate-900 to-slate-900 p-4 sm:p-5 rounded-3xl border border-sky-500/30 shadow-lg flex flex-col justify-between hover:border-sky-500/60 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-lg border border-sky-500/30">
                            🛡️
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30 font-mono">
                            Keamanan
                        </span>
                    </div>
                    <h3 class="font-extrabold text-sm sm:text-base text-white group-hover:text-sky-300 transition">
                        Display KIOSK, Bel &amp; Keamanan
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                        Akses monitor TV Kiosk display informasi, otomatisasi bel sekolah, perpustakaan, dan buku tamu posko.
                    </p>
                </div>
                <div class="pt-3 mt-3 border-t border-white/5 space-y-1.5">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="w-full py-2 px-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl flex items-center justify-between transition shadow-xs">
                        <span>📺 Buka Monitor KIOSK TV</span>
                        <span>&rarr;</span>
                    </a>
                    <div class="grid grid-cols-2 gap-1.5 text-[10.5px]">
                        <a href="{{ route('school_bell.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            🔔 Bel Sekolah
                        </a>
                        <a href="{{ route('guests.index') }}" class="py-1 px-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-center truncate">
                            📖 Buku Tamu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 8 KARTU KONTROLING PILAR UTAMA (The 8 Pillars of SMAN 1 Lengkong) -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                <span>🎯</span> 8 Pilar Pengawasan & Rekapitulasi Sekolah
            </h2>
            <span class="text-xs text-slate-400">Sinkronisasi Realtime</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Presensi Utama -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">1. Presensi Utama</span>
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $attendanceStats['hadir_pct'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">({{ $attendanceStats['hadir'] + $attendanceStats['terlambat'] }}/{{ $totalStudents }})</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceStats['hadir_pct'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Izin: {{ $attendanceStats['izin'] }} | Sakit: {{ $attendanceStats['sakit'] }}</span>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">Alpa: {{ $attendanceStats['alpa'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('attendance.index') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center justify-between">
                        <span>Pantau Presensi Utama</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 2. Tata Tertib (Pelanggaran & Prestasi) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">2. Tata Tertib Sekolah</span>
                        <span class="p-2 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-rose-500 font-bold block">Poin Sanksi</span>
                            <span class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($totalViolationPoints) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-emerald-500 font-bold block">Poin Prestasi</span>
                            <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">+{{ number_format($totalAchievementPoints) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>{{ $totalVerifiedViolations }} Insiden Pelanggaran</span>
                        <span>{{ $totalVerifiedAchievements }} Prestasi</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('discipline.report') }}" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center justify-between">
                        <span>Laporan Tata Tertib</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 3. Presensi Shalat Dzuhur / Jum'at -->
            @php $dz = $worshipTahfidzSummary['dzuhur'] ?? ['rate' => 0, 'today_hadir' => 0, 'total_records' => 0]; @endphp
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">3. Shalat Dzuhur/Jum'at</span>
                        <span class="p-2 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-xl font-black text-xs">
                            🕌
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $dz['rate'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $dz['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Hadir Hari Ini: <strong class="text-slate-800 dark:text-white">{{ $dz['today_hadir'] ?? 0 }} Siswa</strong></span>
                        <span>Target: {{ $dz['today_total_target'] ?? $dz['total_records'] }} Siswa</span>
                    </div>
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800/60">
                        <span>H: <strong class="text-emerald-600">{{ $dz['today_hadir'] ?? 0 }}</strong></span>
                        <span>S: <strong class="text-amber-600">{{ $dz['today_sakit'] ?? 0 }}</strong></span>
                        <span>I: <strong class="text-sky-600">{{ $dz['today_izin'] ?? 0 }}</strong></span>
                        <span>A: <strong class="text-rose-600">{{ $dz['today_alpa'] ?? 0 }}</strong></span>
                        <span>U: <strong class="text-purple-600">{{ $dz['today_udzur'] ?? 0 }}</strong></span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center justify-between">
                        <span>Presensi Dzuhur & Jum'at</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 4. Presensi Shalat Dhuha -->
            @php $dh = $worshipTahfidzSummary['dhuha'] ?? ['rate' => 0, 'today_hadir' => 0, 'total_records' => 0]; @endphp
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">4. Shalat Dhuha</span>
                        <span class="p-2 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl font-black text-xs">
                            ☀️
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">{{ $dh['rate'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $dh['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Hadir Hari Ini: <strong class="text-slate-800 dark:text-white">{{ $dh['today_hadir'] ?? 0 }} Siswa</strong></span>
                        <span>Target: {{ $dh['today_total_target'] ?? $dh['total_records'] }} Siswa</span>
                    </div>
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800/60">
                        <span>H: <strong class="text-emerald-600">{{ $dh['today_hadir'] ?? 0 }}</strong></span>
                        <span>S: <strong class="text-amber-600">{{ $dh['today_sakit'] ?? 0 }}</strong></span>
                        <span>I: <strong class="text-sky-600">{{ $dh['today_izin'] ?? 0 }}</strong></span>
                        <span>A: <strong class="text-rose-600">{{ $dh['today_alpa'] ?? 0 }}</strong></span>
                        <span>U: <strong class="text-purple-600">{{ $dh['today_udzur'] ?? 0 }}</strong></span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'dhuha') }}" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center justify-between">
                        <span>Presensi Shalat Dhuha</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 5. Presensi Keputrian -->
            @php $kp = $worshipTahfidzSummary['keputrian'] ?? ['rate' => 0, 'today_hadir' => 0, 'total_records' => 0]; @endphp
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">5. Presensi Keputrian</span>
                        <span class="p-2 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl font-black text-xs">
                            🌸
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">{{ $kp['rate'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $kp['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Hadir Jum'at: <strong class="text-slate-800 dark:text-white">{{ $kp['today_hadir'] ?? 0 }} Siswi</strong></span>
                        <span>Target: {{ $kp['today_total_target'] ?? $kp['total_records'] }} Siswi</span>
                    </div>
                    <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800/60">
                        <span>H: <strong class="text-emerald-600">{{ $kp['today_hadir'] ?? 0 }}</strong></span>
                        <span>S: <strong class="text-amber-600">{{ $kp['today_sakit'] ?? 0 }}</strong></span>
                        <span>I: <strong class="text-sky-600">{{ $kp['today_izin'] ?? 0 }}</strong></span>
                        <span>A: <strong class="text-rose-600">{{ $kp['today_alpa'] ?? 0 }}</strong></span>
                        <span>U: <strong class="text-purple-600">{{ $kp['today_udzur'] ?? 0 }}</strong></span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'keputrian') }}" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center justify-between">
                        <span>Presensi Keputrian</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 6. Presensi Ekstrakurikuler -->
            @php $ex = $worshipTahfidzSummary['extracurricular'] ?? ['rate' => 0, 'today_hadir' => 0, 'total_records' => 0]; @endphp
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">6. Ekstrakurikuler</span>
                        <span class="p-2 bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 rounded-xl font-black text-xs">
                            🎯
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-cyan-600 dark:text-cyan-400">{{ $ex['rate'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Partisipasi</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-cyan-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $ex['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Hadir Hari Ini: <strong class="text-slate-800 dark:text-white">{{ $ex['today_hadir'] }}</strong></span>
                        <span>Total Sesi: {{ $ex['total_records'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('extracurricular.index') }}" class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center justify-between">
                        <span>Presensi Ekstrakurikuler</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 7. Hafalan Juz 30 (Tahfidz) -->
            @php $tf = $worshipTahfidzSummary['tahfidz'] ?? ['total_deposits' => 0, 'lulus_deposits' => 0, 'progress_pct' => 0, 'students_with_deposit' => 0, 'students_without_deposit' => 0, 'target_total_students' => 801, 'pass_rate_pct' => 0]; @endphp
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">7. Hafalan Juz 30</span>
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl font-black text-xs">
                            📖
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $tf['progress_pct'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Partisipasi Setor</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $tf['progress_pct']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Sudah Setor: <strong class="text-emerald-600 dark:text-emerald-400">{{ $tf['students_with_deposit'] ?? 0 }} Siswa</strong></span>
                        <span>Target: {{ $tf['target_total_students'] ?? 801 }} Siswa</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1 pt-1 border-t border-slate-100 dark:border-slate-800">
                        <span>Belum Setor: <strong class="text-rose-500 dark:text-rose-400">{{ $tf['students_without_deposit'] ?? 0 }} Siswa</strong></span>
                        <span>Lulus Sesi: {{ $tf['lulus_deposits'] ?? 0 }}/{{ $tf['total_deposits'] ?? 0 }} ({{ $tf['pass_rate_pct'] ?? 0 }}%)</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('tahfidz.index') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center justify-between">
                        <span>Buku Tahfidz Juz 30</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 8. 7 KAIH (Karakter Anak Hebat) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">8. 7 KAIH Karakter</span>
                        <span class="p-2 bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 rounded-xl font-black text-xs">
                            🌱
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-teal-600 dark:text-teal-400">{{ $habitCompletionRate }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Partisipasi Hari Ini</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-teal-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitCompletionRate }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>{{ $completedHabitsCount }} Terisi Hari Ini</span>
                        <span>7 Indikator</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('habits.summary') }}" class="text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center justify-between">
                        <span>Rekapitulasi Karakter</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION KONTROLING: DETEKSI DINI & ANOMALI KEDISIPLINAN -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Deteksi Siswa Alpa Kronis / Sering Bolos -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>🚨</span> Deteksi Dini: Siswa Sering Alpa (&ge; 3 Kali)
                        </h3>
                        <p class="text-[11px] text-slate-400">Peringatan preventif risiko membolos dan drop out sekolah</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-bold">
                        {{ $chronicAlpaStudents->count() }} Siswa Terdeteksi
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($chronicAlpaStudents as $sAlpa)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $sAlpa->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Kelas {{ $sAlpa->schoolClass?->name ?? '-' }} &bull; NISN: {{ $sAlpa->nisn }}
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                    {{ $sAlpa->total_alpa }}x Alpa
                                </span>
                                <a href="{{ route('reports.student.dossier', $sAlpa->id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 transition" title="Lihat Dossier Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Alhamdulillah, tidak ada siswa dengan akumulasi alpa mencurigakan. Presensi sekolah dalam kondisi tertib.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="{{ route('attendance.history') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Lihat Seluruh Riwayat Absensi &rarr;
                </a>
            </div>
        </div>

        <!-- Siswa Ambang Batas Tata Tertib (SP 1, SP 2, SP 3) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>⚠️</span> Siswa Ambang Batas Pembinaan (&ge; 75 Poin)
                        </h3>
                        <p class="text-[11px] text-slate-400">Monitoring eskalasi: Wali Kelas, SP 1, SP 2, SP 3, hingga Rapat Pleno</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-bold">
                        {{ $studentsNeedingAttention->count() }} Kasus
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($studentsNeedingAttention as $st)
                        @php $stage = $st->guidance_status; @endphp
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $st->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                    Kelas {{ $st->schoolClass?->name ?? '-' }} &bull; NISN: {{ $st->nisn }}
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex items-center gap-2">
                                <div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                        {{ $st->totalViolationPoints() }} Poin
                                    </span>
                                    <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300 mt-0.5">
                                        {{ $stage['level'] }}
                                    </div>
                                </div>
                                <a href="{{ route('reports.student.dossier', $st->id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 transition" title="Lihat Dossier Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Tidak ada siswa yang melampaui ambang sanksi 75 poin. Kedisiplinan sekolah terjaga dengan baik.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="{{ route('discipline.report') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">
                    Lihat Laporan Kedisiplinan Lengkap &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Kehadiran per Tingkat Kelas (Kelas X, XI, XII) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>📊</span> Pantauan Kehadiran Berdasarkan Jenjang Kelas Hari Ini
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Komparasi kedisiplinan absensi antar angkatan (Kelas X, XI, dan XII)</p>
            </div>
            <a href="{{ route('attendance.history') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Riwayat Absensi Harian &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($gradeStats as $gradeName => $gradeData)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                            Kelas {{ $gradeName }}
                        </span>
                        <span class="text-sm font-black text-slate-800 dark:text-white">{{ $gradeData['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $gradeData['percentage'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
                        <span>Hadir / Terlambat:</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200">{{ $gradeData['present'] }} dari {{ $gradeData['total'] }} siswa</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Papan Peringkat Prestasi Siswa Teratas (Top Achievers) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>🏆</span> Papan Peringkat Prestasi Siswa Teratas (Top Achievers)
                </h3>
                <p class="text-xs text-slate-400">Siswa dengan akumulasi apresiasi dan prestasi terbanyak di SMAN 1 Lengkong</p>
            </div>
            <a href="{{ route('discipline.ranking') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                Buka Seluruh Papan Peringkat &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($topAchievers as $idx => $item)
                @php $st = $item['student']; @endphp
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shrink-0
                            {{ $idx === 0 ? 'bg-amber-400 text-amber-950 shadow-xs' : ($idx === 1 ? 'bg-slate-300 text-slate-900' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200')) }}">
                            {{ $idx + 1 }}
                        </span>
                        <div class="min-w-0">
                            <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $st->name }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                Kelas {{ $st->schoolClass?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                            +{{ $item['points'] }} Poin
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-4 p-6 text-center text-slate-400 text-xs">
                    Belum ada data prestasi yang terverifikasi.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ringkasan Ibadah Bersama, Tahfidz Al-Qur'an & Ekstrakurikuler -->
    <!-- 8. AGENDA & KEGIATAN SEKOLAH TERUPDATE -->
    @include('dashboards.partials.school_agendas_summary')

    @include('dashboards.partials.worship_tahfidz_summary')

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital -->
    @include('dashboards.partials.lms_library_summary')

    <!-- AKSES CEPAT DOKUMEN & REKAP LAPORAN RESMI (SEMUA AGENDA TANPA CSV EXPORT) -->
    <div class="bg-gradient-to-r from-indigo-950/40 via-slate-900/60 to-indigo-950/40 border border-indigo-500/20 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-indigo-500/20 mb-4 gap-2">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                    <span>📑</span> Akses Cepat Dokumen & Rekap Laporan Resmi
                </h3>
                <p class="text-xs text-slate-300">Cetak dokumen pengawasan resmi siap arsip A4/F4 lengkap dengan KOP Surat untuk seluruh agenda sekolah:</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                Buka Pusat Laporan &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 text-center text-xs">
            <!-- 1. Presensi Utama -->
            <a href="{{ route('reports.print.attendance') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">📅</span>
                <span class="truncate w-full">Presensi Utama</span>
            </a>

            <!-- 2. Tata Tertib -->
            <a href="{{ route('reports.print.discipline') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">⚖️</span>
                <span class="truncate w-full">Tata Tertib</span>
            </a>

            <!-- 3. Shalat Dzuhur/Jum'at -->
            <a href="{{ route('worship.print', 'dzuhur-jumat') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">🕌</span>
                <span class="truncate w-full">Shalat Dzuhur</span>
            </a>

            <!-- 4. Shalat Dhuha -->
            <a href="{{ route('worship.print', 'dhuha') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">☀️</span>
                <span class="truncate w-full">Shalat Dhuha</span>
            </a>

            <!-- 5. Keputrian -->
            <a href="{{ route('worship.print', 'keputrian') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">🌸</span>
                <span class="truncate w-full">Presensi Keputrian</span>
            </a>

            <!-- 6. Ekstrakurikuler -->
            <a href="{{ route('extracurricular.print') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">🎯</span>
                <span class="truncate w-full">Ekstrakurikuler</span>
            </a>

            <!-- 7. Tahfidz Juz 30 -->
            <a href="{{ route('tahfidz.print') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">📖</span>
                <span class="truncate w-full">Tahfidz Juz 30</span>
            </a>

            <!-- 8. 7 KAIH -->
            <a href="{{ route('reports.print.habits') }}" target="_blank" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">🌱</span>
                <span class="truncate w-full">7 KAIH Karakter</span>
            </a>

            <!-- 9. Laporan Gabungan Terpadu (8 Pilar) -->
            <a href="{{ route('reports.print.combined') }}" target="_blank" class="p-3 bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-500/40 rounded-2xl text-indigo-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-indigo-900/30">
                <span class="text-lg">📑</span>
                <span class="truncate w-full">Laporan Gabungan</span>
            </a>

            <!-- 10. Raport Digital -->
            <a href="{{ route('raport.index') }}" class="p-3 bg-purple-950/80 hover:bg-purple-900 border border-purple-500/40 rounded-2xl text-purple-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-purple-900/30">
                <span class="text-lg">🤖</span>
                <span class="truncate w-full">Raport Digital</span>
            </a>

            <!-- 11. Rapor Kurnas SMA -->
            <a href="{{ route('rapor_kurnas.index') }}" class="p-3 bg-emerald-950/80 hover:bg-emerald-900 border border-emerald-500/40 rounded-2xl text-emerald-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-emerald-900/30">
                <span class="text-lg">📊</span>
                <span class="truncate w-full">Rapor Kurnas</span>
            </a>

            <!-- 12. LMS Pembelajaran -->
            <a href="{{ route('lms.index') }}" class="p-3 bg-cyan-950/80 hover:bg-cyan-900 border border-cyan-500/40 rounded-2xl text-cyan-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-cyan-900/30">
                <span class="text-lg">💻</span>
                <span class="truncate w-full">LMS Pembelajaran</span>
            </a>

            <!-- 13. Perpustakaan Digital -->
            <a href="{{ route('library.books.index') }}" class="p-3 bg-amber-950/80 hover:bg-amber-900 border border-amber-500/40 rounded-2xl text-amber-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-amber-900/30">
                <span class="text-lg">📚</span>
                <span class="truncate w-full">Perpustakaan</span>
            </a>

            <!-- 14. Adm. Ketatausahaan -->
            <a href="{{ route('tu.dashboard') }}" class="p-3 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-xs">
                <span class="text-lg">🗂️</span>
                <span class="truncate w-full">Adm. Ketatausahaan</span>
            </a>
        </div>
    </div>
</div>
@endsection
