@extends('layouts.app')

@section('title', 'Dashboard Monitoring Kepala Sekolah - SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header Eksekutif Kepala Sekolah -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-5 sm:p-8 text-white shadow-2xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold">
                    Pusat Pengawasan Eksekutif
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    MODE MONITORING
                </span>
            </div>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Kepala SMAN 1 Lengkong</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                Pantauan komprehensif kehadiran seluruh siswa, tata tertib & kedisiplinan, perkembangan karakter 7 Kebiasaan Anak Indonesia Hebat, serta laporan eksekutif sekolah secara realtime.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.print.combined') }}" target="_blank" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan Gabungan
            </a>
            <a href="{{ route('reports.index') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Semua Laporan & Rekap
            </a>
        </div>
    </div>

    <!-- Notice Bar Khusus Monitoring Read-Only -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-cyan-950/40 via-slate-900/60 to-cyan-950/40 border border-cyan-500/30 text-cyan-200 text-xs flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center shrink-0 font-bold text-sm">
            👁️
        </div>
        <div>
            <span class="font-extrabold text-cyan-100 uppercase tracking-wide">Fungsi Pengawasan Eksekutif (Monitoring Only):</span>
            <span class="text-slate-300"> Akun Kepala Sekolah memiliki kewenangan penuh untuk meninjau seluruh rekap kehadiran, catatan tata tertib, dan capaian karakter seluruh siswa tanpa beban input maupun mutasi data operasional.</span>
        </div>
    </div>

    <!-- 4 Kartu KPI Utama Eksekutif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Tingkat Kehadiran Sekolah -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Kehadiran Hari Ini</span>
                <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $attendanceStats['hadir_pct'] }}%</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">({{ $attendanceStats['hadir'] + $attendanceStats['terlambat'] }}/{{ $totalStudents }} siswa)</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-3">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceStats['hadir_pct'] }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                <span>Izin: {{ $attendanceStats['izin'] }} | Sakit: {{ $attendanceStats['sakit'] }}</span>
                <span class="text-rose-600 dark:text-rose-400 font-semibold">Alpa: {{ $attendanceStats['alpa'] }}</span>
            </div>
        </div>

        <!-- 2. Poin & Prestasi Siswa -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Prestasi Terverifikasi</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">+{{ number_format($totalAchievementPoints) }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Total Poin</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                <span>Total Berkas:</span>
                <span class="font-bold text-slate-800 dark:text-slate-100">{{ $totalVerifiedAchievements }} Kegiatan</span>
            </div>
            <a href="{{ route('achievements.index') }}" class="mt-2 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                Tinjau Buku Prestasi Siswa &rarr;
            </a>
        </div>

        <!-- 3. Pelanggaran & Pembinaan -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Pelanggaran Tata Tertib</span>
                <span class="p-2 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">{{ number_format($totalViolationPoints) }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Total Poin Sanksi</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                <span>Kasus Dicatat:</span>
                <span class="font-bold text-slate-800 dark:text-slate-100">{{ $totalVerifiedViolations }} Insiden</span>
            </div>
            <a href="{{ route('violations.index') }}" class="mt-2 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1">
                Tinjau Buku Pelanggaran &rarr;
            </a>
        </div>

        <!-- 4. Karakter 7 Kebiasaan -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">7 Kebiasaan Anak Hebat</span>
                <span class="p-2 bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-teal-600 dark:text-teal-400">{{ $habitCompletionRate }}%</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Partisipasi Hari Ini</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-3">
                <div class="bg-teal-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitCompletionRate }}%"></div>
            </div>
            <a href="{{ route('habits.summary') }}" class="mt-2 text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1">
                Tinjau Rekapitulasi Karakter &rarr;
            </a>
        </div>
    </div>

    <!-- Kehadiran per Tingkat Kelas (Kelas X, XI, XII) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>📊</span> Pantauan Kehadiran Berdasarkan Tingkat Kelas Hari Ini
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan kedisiplinan absensi tiap jenjang di SMAN 1 Lengkong</p>
            </div>
            <a href="{{ route('attendance.history') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Riwayat Lengkap &rarr;
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

    <!-- 2 Kolom: Siswa Perlu Bimbingan (Kiri) & Peringkat Siswa Berprestasi (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Siswa Memerlukan Perhatian Khusus -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>⚠️</span> Siswa Memerlukan Perhatian Khusus (Poin &ge; 75)
                        </h3>
                        <p class="text-[11px] text-slate-400">Ambang bimbingan wali kelas, SP 1, SP 2, hingga SP 3</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-bold">
                        {{ $studentsNeedingAttention->count() }} Siswa
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
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                    {{ $st->totalViolationPoints() }} Poin
                                </span>
                                <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300 mt-1">
                                    {{ $stage['level'] }}
                                </div>
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

        <!-- Top Achievers (Siswa Berprestasi) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>🏆</span> Papan Peringkat Prestasi Siswa Teratas
                        </h3>
                        <p class="text-[11px] text-slate-400">Siswa dengan perolehan poin penghargaan tertinggi</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-bold">
                        Top Achievers
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($topAchievers as $idx => $item)
                        @php $st = $item['student']; @endphp
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shrink-0
                                    {{ $idx === 0 ? 'bg-amber-400 text-amber-950 shadow-xs' : ($idx === 1 ? 'bg-slate-300 text-slate-900' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $st->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                        Kelas {{ $st->schoolClass?->name ?? '-' }} &bull; {{ $item['count'] }} Penghargaan
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                    +{{ $item['points'] }} Poin
                                </span>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-1">
                                    {{ $item['reward_status']['level'] }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Belum ada data prestasi yang terverifikasi.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="{{ route('discipline.ranking') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                    Buka Seluruh Papan Peringkat Sekolah &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Tautan Cepat Peninjauan Laporan Sekolah (Pusat Cetak & Rekap) -->
    <div class="bg-gradient-to-r from-indigo-900/30 via-slate-900/50 to-indigo-900/30 border border-indigo-500/20 rounded-3xl p-6 shadow-sm">
        <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
            <span>📑</span> Akses Cepat Dokumen & Rekap Laporan Resmi
        </h3>
        <p class="text-xs text-slate-300 mb-4">Akses instan cetak dan ekspor seluruh kegiatan pembelajaran dan kesiswaan:</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center text-xs">
            <a href="{{ route('reports.print.attendance') }}" target="_blank" class="p-3 bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">📅</span>
                <span>Rekap Presensi</span>
            </a>
            <a href="{{ route('reports.print.discipline') }}" target="_blank" class="p-3 bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">⚖️</span>
                <span>Rekap Tata Tertib</span>
            </a>
            <a href="{{ route('reports.print.habits') }}" target="_blank" class="p-3 bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-slate-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">🌱</span>
                <span>Rekap Karakter</span>
            </a>
            <a href="{{ route('reports.print.combined') }}" target="_blank" class="p-3 bg-indigo-950/60 hover:bg-indigo-900/60 border border-indigo-500/30 rounded-2xl text-indigo-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">📑</span>
                <span>Laporan Gabungan</span>
            </a>
            <a href="{{ route('reports.export.csv') }}" class="p-3 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-500/30 rounded-2xl text-emerald-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">📊</span>
                <span>Ekspor CSV</span>
            </a>
            <a href="{{ route('ai.analysis') }}" class="p-3 bg-purple-950/60 hover:bg-purple-900/60 border border-purple-500/30 rounded-2xl text-purple-200 font-bold transition flex flex-col items-center justify-center gap-1.5">
                <span class="text-lg">🤖</span>
                <span>Analisis AI</span>
            </a>
        </div>
    </div>
</div>
@endsection
