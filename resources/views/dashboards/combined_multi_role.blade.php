@extends('layouts.app')

@section('title', 'Dasbor Terpadu Multi-Peran')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Multi-Peran Terpadu -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="space-y-2 relative z-10">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 rounded-full text-xs font-bold border border-amber-500/30 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Mode Terpadu Multi-Peran
                </span>
                @foreach($activeRoleNames as $roleName)
                    <span class="px-2.5 py-0.5 bg-white/10 text-slate-200 rounded-full text-[11px] font-semibold border border-white/10">
                        {{ $roleName }}
                    </span>
                @endforeach
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Selamat Datang, {{ $teacher->name ?? $user->name }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 max-w-3xl leading-relaxed">
                NIP: {{ $teacher->nip ?? ($user->nip ?? '-') }} • Anda sedang dalam mode <strong>Semua Peran (Akses Lengkap)</strong>. Seluruh tanggung jawab, pemantauan presensi, kelas binaan, dan tugas pengajaran disajikan terpadu sesuai peran aktif Anda.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0">
            @if(in_array('guru', $allUserRoles, true))
                <a href="{{ route('attendance.qr.session') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    + Presensi QR
                </a>
            @endif

            @if(in_array('wali_kelas', $allUserRoles, true) && $class)
                <a href="{{ route('reports.index', ['class_id' => $class->id]) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs flex items-center gap-2 border border-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Rekap {{ $class->name }}
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Role Pill Jump Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <span class="text-slate-400 font-bold shrink-0">Lompat Cepat:</span>
        <a href="#ringkasan-terpadu" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 font-bold rounded-xl border border-indigo-200 dark:border-indigo-800 shrink-0 hover:bg-indigo-100 transition">
            ⚡ Ringkasan Terpadu
        </a>
        @if(in_array('wali_kelas', $allUserRoles, true) && $class)
            <a href="#panel-wali-kelas" class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl border border-emerald-200 dark:border-emerald-800 shrink-0 hover:bg-emerald-100 transition">
                🏫 Kelas Binaan ({{ $class->name }})
            </a>
        @endif
        @if(in_array('guru', $allUserRoles, true))
            <a href="#panel-guru-mapel" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 font-bold rounded-xl border border-amber-200 dark:border-amber-800 shrink-0 hover:bg-amber-100 transition">
                👨‍🏫 Tugas Guru Mapel
            </a>
        @endif
        @if(in_array('wakasek_kesiswaan', $allUserRoles, true) || in_array('kesiswaan', $allUserRoles, true))
            <a href="#panel-kesiswaan" class="px-3 py-1.5 bg-purple-50 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 font-bold rounded-xl border border-purple-200 dark:border-purple-800 shrink-0 hover:bg-purple-100 transition">
                🛡️ Kesiswaan & Izin
            </a>
        @endif
        <a href="#panel-lms-perpustakaan" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl border border-slate-200 dark:border-slate-700 shrink-0 hover:bg-slate-200 transition">
            📚 LMS & Perpustakaan
        </a>
    </div>

    <!-- Menu / Ikon Cepat Operasional -->
    @include('dashboards.partials.staff_quick_actions')

    <!-- Section 1: Ringkasan Metrik Terpadu Hari Ini -->
    <div id="ringkasan-terpadu" class="space-y-3">
        <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            Ringkasan Indikator Kinerja Hari Ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }})
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(in_array('guru', $allUserRoles, true))
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hadir Hari Ini (Sekolah)</div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $todayPresentCount }}</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Siswa hadir / terlambat di sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Verifikasi Prestasi</div>
                        <div class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $pendingAchievements }}</div>
                        <a href="{{ route('achievements.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-0.5 inline-block">Lihat antrean →</a>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Verifikasi Pelanggaran</div>
                        <div class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $pendingViolations }}</div>
                        <a href="{{ route('violations.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-0.5 inline-block">Lihat antrean →</a>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            @endif

            @if(in_array('wali_kelas', $allUserRoles, true) && $class)
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kehadiran Kelas {{ $class->name }}</div>
                        <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $presentToday + $lateToday }}/{{ $totalStudents }}</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $permissionToday + $sickToday }} Izin/Sakit • {{ $alphaToday }} Alpa</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            @endif

            @if(in_array('bk', $allUserRoles, true) || in_array('koordinator_bk', $allUserRoles, true) || in_array('guru_bk', $allUserRoles, true))
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Layanan Konseling BK</div>
                        <div class="text-3xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ $bkCasesCount }}</div>
                        <a href="{{ route('bk.dashboard') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-0.5 inline-block">Buka Dasbor BK →</a>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            @endif

            @if(in_array('wakasek_kesiswaan', $allUserRoles, true) || in_array('kesiswaan', $allUserRoles, true))
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Izin Pulang & Keluar</div>
                        <div class="text-3xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $totalEarlyLeavesToday + $totalSchoolPassesToday }}</div>
                        <a href="{{ route('attendance.early-leave.index') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-0.5 inline-block">Surat Izin Resmi →</a>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Section 2: Panel Wali Kelas (Jika pengguna memiliki peran Wali Kelas) -->
    @if(in_array('wali_kelas', $allUserRoles, true) && $class)
        <div id="panel-wali-kelas" class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        Tugas Wali Kelas: Kelas Binaan {{ $class->name }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan kehadiran harian, ketertiban poin siswa, dan pendampingan siswa kelas binaan.</p>
                </div>
                <a href="{{ route('reports.index', ['class_id' => $class->id]) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Buka Laporan Kelas Lengkap →</a>
            </div>

            <!-- Breakdown Kehadiran Kelas Binaan -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa</span>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalStudents }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Hadir</span>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $presentToday }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Terlambat</span>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $lateToday }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Izin</span>
                    <div class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ $permissionToday }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Sakit</span>
                    <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $sickToday }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Alpa</span>
                    <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $alphaToday }}</div>
                </div>
            </div>

            <!-- Warning Peringatan Pembinaan Siswa >= 75 Poin -->
            @if($needGuidanceStudents->isNotEmpty())
                <div class="p-4 sm:p-5 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-950 dark:text-amber-200 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-black text-lg">!</div>
                    <div>
                        <h4 class="font-extrabold text-sm text-amber-900 dark:text-amber-200">PERHATIAN WALI KELAS: Siswa Memerlukan Pembinaan & Koordinasi BK</h4>
                        <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                            Terdapat {{ $needGuidanceStudents->count() }} siswa dengan akumulasi pelanggaran ≥ 75 poin:
                            @foreach($needGuidanceStudents as $ngs)
                                <span class="font-bold underline">{{ $ngs->name }} ({{ $ngs->totalViolationPoints() }} Poin)</span>{{ ! $loop->last ? ', ' : '' }}
                            @endforeach. Segera lakukan tindak lanjut atau jadwalkan panggilan orang tua bersama Guru BK.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Tabel Siswa Kelas Binaan -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h4 class="font-bold text-sm text-slate-800 dark:text-slate-100">Daftar Siswa Kelas Binaan ({{ $students->count() }} Siswa)</h4>
                    <span class="text-xs text-slate-400">Status Hari Ini & Poin Tata Tertib</span>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-3 py-3">NIS</th>
                                <th class="px-3 py-3 text-center">Presensi Hari Ini</th>
                                <th class="px-3 py-3 text-center">Poin Prestasi</th>
                                <th class="px-3 py-3 text-center">Poin Pelanggaran</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($students as $s)
                                @php
                                    $todayAtt = $s->attendances->first();
                                    $achPoints = $s->totalAchievementPoints();
                                    $violPoints = $s->totalViolationPoints();
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                    <td class="px-4 py-2.5 font-bold text-slate-800 dark:text-slate-100">
                                        {{ $s->name }}
                                    </td>
                                    <td class="px-3 py-2.5 font-mono text-slate-500 dark:text-slate-400">
                                        {{ $s->nis }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        @if($todayAtt)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $todayAtt->status === 'HADIR' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($todayAtt->status === 'TERLAMBAT' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($todayAtt->status === 'IZIN' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300')) }}">
                                                {{ $todayAtt->status }} {{ $todayAtt->time ? '('.substr($todayAtt->time, 0, 5).')' : '' }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-400">
                                                Belum Presensi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-amber-600 dark:text-amber-400">
                                        +{{ $achPoints }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold {{ $violPoints >= 75 ? 'text-rose-600 dark:text-rose-400 animate-pulse' : 'text-slate-600 dark:text-slate-300' }}">
                                        {{ $violPoints }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                        <a href="{{ route('reports.student.dossier', $s->id) }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            Buku Induk / Profil →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada data siswa di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Section 3: Panel Tenaga Pendidik (Guru Mapel) -->
    @if(in_array('guru', $allUserRoles, true))
        <div id="panel-guru-mapel" class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        Tugas Guru Mata Pelajaran
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sesi presensi QR kelas yang sedang berlangsung hari ini dan alat bantu mengajar.</p>
                </div>
                <a href="{{ route('attendance.qr.session') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">+ Buat Sesi Presensi QR Baru</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Sesi Presensi QR Aktif Hari Ini</h4>
                        <span class="text-xs text-slate-400">{{ $activeSessions->count() }} Sesi Terjadwal</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($activeSessions as $session)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div>
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                                        Kelas {{ $session->schoolClass?->name ?? 'Semua' }}
                                    </span>
                                    <h5 class="text-xs font-bold text-slate-800 dark:text-white mt-1">{{ $session->name }}</h5>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('attendance.qr.display', $session->id) }}" target="_blank" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition">
                                        Tampilkan QR Code
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs">
                                <p>Belum ada sesi presensi QR aktif yang Anda buat hari ini.</p>
                                <a href="{{ route('attendance.qr.session') }}" class="mt-2 inline-block font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    + Buat Sesi Presensi Sekarang
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Fast Actions Box -->
                <div class="bg-gradient-to-br from-indigo-50 to-slate-50 dark:from-slate-800/80 dark:to-slate-900 p-6 rounded-3xl border border-indigo-100 dark:border-slate-700 space-y-3">
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">Alat Bantu Pendidik Cepat</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Akses cepat formulir pencatatan prestasi dan pelanggaran siswa dari kelas yang Anda ajar.</p>
                    <div class="space-y-2 pt-2">
                        <a href="{{ route('achievements.create') }}" class="w-full py-2.5 px-3 bg-white dark:bg-slate-800 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 rounded-xl text-xs font-bold transition flex items-center justify-between shadow-2xs">
                            <span>🏆 + Catat Prestasi Siswa</span>
                            <span>→</span>
                        </a>
                        <a href="{{ route('violations.create') }}" class="w-full py-2.5 px-3 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 rounded-xl text-xs font-bold transition flex items-center justify-between shadow-2xs">
                            <span>⚠️ + Catat Pelanggaran Siswa</span>
                            <span>→</span>
                        </a>
                        <a href="{{ route('lms.index') }}" class="w-full py-2.5 px-3 bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 rounded-xl text-xs font-bold transition flex items-center justify-between shadow-2xs">
                            <span>💻 Modul Pembelajaran LMS</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Section 4: LMS & Perpustakaan Terpadu -->
    <div id="panel-lms-perpustakaan" class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
        @include('dashboards.partials.lms_library_summary')
    </div>

    <!-- Section 5: Ibadah & Tahfidz Terpadu -->
    <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
        <!-- AGENDA & KEGIATAN SEKOLAH TERUPDATE -->
        @include('dashboards.partials.school_agendas_summary')

        @include('dashboards.partials.worship_tahfidz_summary')
    </div>
</div>
@endsection

