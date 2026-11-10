@extends('layouts.app')

@section('title', 'Dashboard Bimbingan & Konseling (BK) Terpadu - SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full max-w-7xl mx-auto">
    <!-- Header Layanan Bimbingan & Konseling -->
    <div class="bg-gradient-to-r from-slate-950 via-purple-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-2xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/30 rounded-full text-xs font-bold tracking-wide">
                    Pusat Layanan Bimbingan & Konseling (BK)
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    MONITORING HOLISTIK
                </span>
            </div>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-2">Bimbingan Konseling SMAN 1 Lengkong</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Pusat pendampingan holistik siswa: pemantauan terpadu kedisiplinan tata tertib, deteksi dini presensi bermasalah, pembiasaan karakter 7 KAIH, ibadah, keputrian, ekstrakurikuler, dan tahfidz Al-Qur'an.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('guidance.create') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-purple-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Buka Catatan Pembinaan Baru
            </a>
            <a href="{{ route('discipline.ranking') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Papan Peringkat
            </a>
        </div>
    </div>

    <!-- Menu Cepat Staf & Koordinator BK -->
    @include('dashboards.partials.staff_quick_actions')

    <!-- 8 KARTU REKAPITULASI & INDIKATOR PERKEMBANGAN SISWA (THE 8 PILLARS FOR BK) -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm sm:text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                <span>🔍</span> 8 Pilar Pemantauan & Rekapitulasi Siswa Terpadu
            </h2>
            <span class="text-xs text-slate-400">Dasar Diagnosis & Konseling</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Presensi Utama (Deteksi Kehadiran & Membolos) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">1. Presensi Utama</span>
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $attendanceStats['hadir_pct'] }}%</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Kehadiran Sekolah</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceStats['hadir_pct'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span class="text-rose-600 dark:text-rose-400 font-bold">Alpa Hari Ini: {{ $attendanceStats['alpa'] }}</span>
                        <span>Terlambat: {{ $attendanceStats['terlambat'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('attendance.index') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center justify-between">
                        <span>Pantau Presensi Harian</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 2. Tata Tertib & Kasus Konseling -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">2. Tata Tertib & Kasus</span>
                        <span class="p-2 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">{{ $studentsNeedingAction->count() }}</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Siswa Ambang SP (&ge;75)</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Kasus Aktif: <strong class="text-purple-600 dark:text-purple-400">{{ $activeGuidanceCases->count() }}</strong></span>
                        <span>Total Sanksi: {{ number_format($totalViolationPoints) }} Poin</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('discipline.report') }}" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center justify-between">
                        <span>Rekap Kasus Tata Tertib</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 3. Shalat Dzuhur / Jum'at -->
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
                        <span>Hadir Hari Ini: {{ $dz['today_hadir'] }}</span>
                        <span>Total Sesi: {{ $dz['total_records'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center justify-between">
                        <span>Pantau Shalat Dzuhur</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 4. Shalat Dhuha -->
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
                        <span>Hadir Hari Ini: {{ $dh['today_hadir'] }}</span>
                        <span>Total Sesi: {{ $dh['total_records'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'dhuha') }}" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center justify-between">
                        <span>Pantau Shalat Dhuha</span>
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
                        <span>Hadir Jum'at: {{ $kp['today_hadir'] }}</span>
                        <span>Khusus Siswi Putri</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('worship.index', 'keputrian') }}" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center justify-between">
                        <span>Pantau Keputrian</span>
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
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Partisipasi Siswa</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-cyan-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $ex['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Hadir Hari Ini: {{ $ex['today_hadir'] }}</span>
                        <span>Bakat & Minat</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('extracurricular.index') }}" class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center justify-between">
                        <span>Pantau Ekstrakurikuler</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 7. Hafalan Juz 30 (Tahfidz) -->
            @php $tf = $worshipTahfidzSummary['tahfidz'] ?? ['total_deposits' => 0, 'lulus_deposits' => 0, 'progress_pct' => 0]; @endphp
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
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Kelulusan</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $tf['progress_pct']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                        <span>Lulus: {{ $tf['lulus_deposits'] }}</span>
                        <span>Total Setoran: {{ $tf['total_deposits'] }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('tahfidz.index') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center justify-between">
                        <span>Buku Tahfidz Juz 30</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 8. 7 KAIH Karakter Anak Hebat -->
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
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Partisipasi Karakter</span>
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

    <!-- Ambang Batas Tindakan Pembinaan (Tata Tertib SMAN 1 Lengkong) -->
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Ambang Batas Tindakan Pembinaan (Tata Tertib SMAN 1 Lengkong)</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Sistem memberikan <strong>NOTIFIKASI PERLU TINDAKAN</strong> saat siswa mencapai ambang batas poin berikut:</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div class="p-3 bg-yellow-50 dark:bg-yellow-950/40 border border-yellow-200 dark:border-yellow-800/60 rounded-2xl">
                <span class="font-bold text-yellow-800 dark:text-yellow-300">75 - 124 Poin</span>
                <p class="text-[11px] text-yellow-700 dark:text-yellow-400 mt-1">Bimbingan Wali Kelas</p>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl">
                <span class="font-bold text-amber-800 dark:text-amber-300">125 - 175 Poin</span>
                <p class="text-[11px] text-amber-700 dark:text-amber-400 mt-1">SP 1 - Guru BK</p>
            </div>
            <div class="p-3 bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/60 rounded-2xl">
                <span class="font-bold text-orange-800 dark:text-orange-300">176 - 199 Poin</span>
                <p class="text-[11px] text-orange-700 dark:text-orange-400 mt-1">SP 2 - BK & Panggilan Ortu</p>
            </div>
            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-2xl">
                <span class="font-bold text-rose-800 dark:text-rose-300">200 Poin</span>
                <p class="text-[11px] text-rose-700 dark:text-rose-400 mt-1">SP 3 - BK & Kesiswaan</p>
            </div>
            <div class="p-3 bg-red-100 dark:bg-red-950/60 border border-red-300 dark:border-red-800 rounded-2xl">
                <span class="font-bold text-red-900 dark:text-red-200">&gt; 200 Poin</span>
                <p class="text-[11px] text-red-800 dark:text-red-300 mt-1">Rapat Pleno Dewan Guru</p>
            </div>
        </div>
    </div>

    <!-- DAFTAR SISWA MENCAPAI AMBANG PEMBINAAN (ACTION REQUIRED) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Siswa Mencapai Ambang Pembinaan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar siswa dengan akumulasi poin pelanggaran &ge; 75 poin yang memerlukan intervensi BK</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold text-xs">
                {{ $studentsNeedingAction->count() }} Kasus Perlu Tindakan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Nama Siswa</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4">Total Poin Pelanggaran</th>
                        <th class="py-3.5 px-4">Tahap Tindakan</th>
                        <th class="py-3.5 px-4">Riwayat Kasus</th>
                        <th class="py-3.5 px-4 text-center">Tindakan BK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($studentsNeedingAction as $student)
                        @php $gStatus = $student->guidance_status; @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $student->name }}</div>
                                <div class="text-[11px] text-slate-400">NIS: {{ $student->nis }} &bull; NISN: {{ $student->nisn }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold text-slate-700 dark:text-slate-300">{{ $student->schoolClass?->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-black text-rose-600 dark:text-rose-400 text-sm">{{ $student->totalViolationPoints() }} Poin</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                    {{ $gStatus['level'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-600 dark:text-slate-300">{{ $student->violations->count() }} Catatan Kejadian</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('guidance.create', ['student_id' => $student->id]) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition shadow-xs">
                                        Proses Bimbingan
                                    </a>
                                    <a href="{{ route('reports.student.dossier', $student->id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold transition" title="Lihat Raport Digital AI & Dossier Siswa">
                                        Raport & Dossier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">
                                Alhamdulillah! Tidak ada siswa yang melampaui batas ambang pembinaan (&ge;75 poin). Kedisiplinan siswa terjaga sangat baik.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETEKSI DINI MASALAH SISWA: ALPA KRONIS & SERING TERLAMBAT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Deteksi Siswa Alpa Kronis / Risiko Membolos -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>🚨</span> Deteksi Dini: Siswa Sering Alpa (&ge; 3 Kali)
                        </h3>
                        <p class="text-[11px] text-slate-400">Target konseling preventif agar tidak terjadi drop out atau penumpukan sanksi</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-bold">
                        {{ $chronicAlpaStudents->count() }} Siswa
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
                                <a href="{{ route('guidance.create', ['student_id' => $sAlpa->id]) }}" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[11px] font-bold transition">
                                    Konseling
                                </a>
                                <a href="{{ route('reports.student.dossier', $sAlpa->id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 transition" title="Lihat Dossier Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Alhamdulillah, tidak ada siswa dengan akumulasi alpa mencurigakan.
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

        <!-- Deteksi Siswa Sering Terlambat -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>⏰</span> Deteksi Dini: Siswa Sering Terlambat (&ge; 3 Kali)
                        </h3>
                        <p class="text-[11px] text-slate-400">Pembinaan manajemen waktu dan kedisiplinan pagi hari</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-bold">
                        {{ $chronicLateStudents->count() }} Siswa
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($chronicLateStudents as $sLate)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $sLate->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Kelas {{ $sLate->schoolClass?->name ?? '-' }} &bull; NISN: {{ $sLate->nisn }}
                                </div>
                            </div>
                            <div class="text-right shrink-0 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                    {{ $sLate->total_terlambat }}x Terlambat
                                </span>
                                <a href="{{ route('guidance.create', ['student_id' => $sLate->id]) }}" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[11px] font-bold transition">
                                    Konseling
                                </a>
                                <a href="{{ route('reports.student.dossier', $sLate->id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 transition" title="Lihat Dossier Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Alhamdulillah, tidak ada siswa dengan keterlambatan berulang.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="{{ route('attendance.history') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                    Pantau Jam Kehadiran &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Active Guidance Cases & Agreements (Kasus Sedang Berjalan) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Catatan Pembinaan yang Sedang Berjalan (Dalam Proses)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Monitoring komitmen dan tindak lanjut surat perjanjian siswa</p>
            </div>
            <a href="{{ route('guidance.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Semua Catatan Pembinaan →</a>
        </div>

        <div class="space-y-3">
            @forelse($activeGuidanceCases as $case)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $case->student->name }}</span>
                            <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold text-[10px]">{{ $case->followUpLabel() }}</span>
                            <span class="text-slate-400">• {{ $case->date->format('d M Y') }}</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $case->recommendation }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('guidance.show', $case->id) }}" class="px-3 py-1.5 bg-slate-900 dark:bg-slate-700 text-white rounded-xl font-bold hover:bg-indigo-600 transition">
                            Detail Kasus
                        </a>
                        <a href="{{ route('reports.student.dossier', $case->student_id) }}" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 transition" title="Lihat Dossier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    Tidak ada kasus pembinaan aktif yang sedang dalam proses.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ringkasan Ibadah Bersama, Tahfidz Al-Qur'an & Ekstrakurikuler -->
    @include('dashboards.partials.worship_tahfidz_summary')

    <!-- AKSES CEPAT DOKUMEN & LAPORAN RESMI BK -->
    <div class="bg-gradient-to-r from-purple-950/40 via-slate-900/60 to-purple-950/40 border border-purple-500/20 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-purple-500/20 mb-4 gap-2">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                    <span>📑</span> Akses Cepat Dokumen & Rekap Laporan Resmi BK
                </h3>
                <p class="text-xs text-slate-300">Akses langsung rekapitulasi data 8 pilar untuk pertimbangan konseling, berkas rapat kasus, dan laporan resmi:</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 transition">
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
            <a href="{{ route('reports.print.combined') }}" target="_blank" class="p-3 bg-purple-950/80 hover:bg-purple-900 border border-purple-500/40 rounded-2xl text-purple-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-purple-900/30">
                <span class="text-lg">📑</span>
                <span class="truncate w-full">Laporan Gabungan</span>
            </a>

            <!-- 10. Raport Digital AI -->
            <a href="{{ route('raport.index') }}" class="p-3 bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-500/40 rounded-2xl text-indigo-200 font-black transition flex flex-col items-center justify-center gap-1.5 hover:scale-[1.02] shadow-md shadow-indigo-900/30">
                <span class="text-lg">🤖</span>
                <span class="truncate w-full">Raport Digital AI</span>
            </a>
        </div>
    </div>
</div>
@endsection
