@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6 max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto">
    <!-- Student Hero Profile Card -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-2xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-1 shadow-lg shrink-0">
                        <div class="w-full h-full bg-slate-800 rounded-[14px] flex items-center justify-center font-black text-xl sm:text-2xl text-white">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </div>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center text-[10px]" title="Siswa Aktif">✓</span>
                </div>
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[11px] sm:text-xs font-semibold text-emerald-300 mb-1.5 sm:mb-2 border border-white/10 max-w-full truncate">
                        <span>Kelas {{ $student->schoolClass?->name ?? 'XI-IPA-1' }}</span>
                        <span>•</span>
                        <span>TP {{ $student->academicYear?->name ?? '2026/2027' }}</span>
                    </div>
                    <h2 class="text-lg sm:text-2xl font-extrabold tracking-tight text-white truncate">{{ $student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 sm:gap-x-4 gap-y-1 text-xs text-slate-300 mt-1">
                        <span>NIS: <strong class="text-white font-mono">{{ $student->nis }}</strong></span>
                        <span>NISN: <strong class="text-white font-mono">{{ $student->nisn }}</strong></span>
                        <span class="truncate">Wali: <strong class="text-indigo-200">{{ $student->schoolClass?->homeroomTeacher?->name ?? 'Siti Rahmawati, M.Pd' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Presensi Action Button -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                @if($todayAttendance)
                    <div class="px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center sm:text-right">
                        <div class="text-[10px] sm:text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Presensi Hari Ini</div>
                        <div class="text-xs sm:text-sm font-bold {{ $todayAttendance->status === 'HADIR' ? 'text-emerald-300' : 'text-amber-300' }} mt-0.5 flex items-center justify-center sm:justify-end gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $todayAttendance->status === 'HADIR' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                            {{ $todayAttendance->status }} ({{ substr($todayAttendance->time, 0, 5) }} WIB)
                        </div>
                    </div>
                @else
                    <a href="{{ route('attendance.selfie') }}" class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black rounded-2xl shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2.5 text-xs sm:text-sm tracking-wide">
                        <svg class="w-5 h-5 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>PRESENSI SELFIE SEKARANG</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 4 Key Stat Cards (Presence Rate, Prestasi, Pelanggaran, 7 Kebiasaan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 3xl:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Kehadiran -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tingkat Kehadiran</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $attendanceRate }}%</span>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Semester Ini</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
            </div>
        </div>

        <!-- 2. TOTAL PRESTASI (R1 - R10) -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Poin Prestasi</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $totalAchievementPoints }}</span>
                <span class="text-xs font-bold text-slate-400">Poin Akumulasi</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-bold text-amber-700 dark:text-amber-300">{{ $rewardStatus['level'] }}</span>
            </div>
        </div>

        <!-- 3. TOTAL PELANGGARAN (P1 - P6) - DIPISAH TEGAS! -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Poin Pelanggaran</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-rose-600 dark:text-rose-400">{{ $totalViolationPoints }}</span>
                <span class="text-xs font-bold text-slate-400">Poin Akumulasi</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full {{ $guidanceStatus['action_needed'] ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                <span class="font-bold {{ $guidanceStatus['action_needed'] ? 'text-rose-700 dark:text-rose-300' : 'text-emerald-700 dark:text-emerald-300' }}">{{ $guidanceStatus['level'] }}</span>
            </div>
        </div>

        <!-- 4. Progress 7 Kebiasaan Hari Ini -->
        <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover-lift transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">7 Kebiasaan Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $completedHabitsCount }}/{{ count($habits) }}</span>
                <span class="text-xs font-semibold text-slate-400">Terlaksana</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $habitProgressPercentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Section: 7 Habits Checklist & AI / Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 7 Kebiasaan Quick Journal (Left 2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Gerakan 7 Kebiasaan Anak Indonesia Hebat</h3>
                    <p class="text-xs text-slate-500">Centang aktivitas pembiasaan positif yang telah Anda lakukan hari ini</p>
                </div>
                <a href="{{ route('habits.journal') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    Buka Jurnal Penuh →
                </a>
            </div>

            <div class="space-y-2.5">
                @foreach($habits as $habit)
                    @php
                        $log = $todayHabitLogs->get($habit->id);
                        $isDone = $log && $log->is_completed;
                        $timeStatus = $habit->getTimeWindowStatus();
                        $canCheck = $timeStatus['is_active'];
                    @endphp
                    <div class="flex items-center justify-between p-3.5 rounded-2xl border {{ $isDone ? 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-800' }} transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-7 h-7 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $habit->order_number }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate {{ $isDone ? 'line-through text-slate-500 dark:text-slate-400' : '' }}">{{ $habit->name }}</h4>
                                <p class="text-xs text-slate-400 truncate">
                                    <span class="font-semibold {{ $timeStatus['is_active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                        {{ $timeStatus['range'] }}
                                    </span>
                                    • {{ $habit->tagline }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            @if($isDone)
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white inline-flex items-center gap-1 shadow-xs">
                                    ✓ Selesai
                                </span>
                            @else
                                @if($canCheck)
                                    <form action="{{ route('habits.log.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="habit_id" value="{{ $habit->id }}">
                                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                                        <input type="hidden" name="is_completed" value="1">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-900 dark:bg-slate-100 dark:text-slate-900 hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white text-white shadow-xs cursor-pointer">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled title="{{ $timeStatus['message'] }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed inline-flex items-center gap-1 shadow-none">
                                        🔒 Di Luar Jam
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: AI Insight Shortcut & Notifications -->
        <div class="space-y-6">
            <!-- AI Analysis Teaser Card -->
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-3xl p-6 text-white shadow-md border border-indigo-800/40">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-[11px] font-bold border border-indigo-500/30">
                        AI SMART INSIGHT
                    </span>
                    <span class="text-xs text-slate-400">Analisis Perkembangan</span>
                </div>
                <h4 class="text-base font-bold text-white mb-2">Potensi & Rekomendasi Karakter Anda</h4>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Sistem menganalisis data presensi, prestasi, catatan tata tertib, dan konsistensi 7 kebiasaan Anda secara objektif.
                </p>
                <a href="{{ route('ai.analysis') }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-md transition">
                    Lihat Analisis AI Saya
                    <span>→</span>
                </a>
            </div>

            <!-- Notifications Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi Terbaru</h3>
                    <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700">Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($notifications as $notif)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-xs">
                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ $notif->title }}</div>
                            <div class="text-slate-500 dark:text-slate-300 mt-1 leading-normal">{{ $notif->message }}</div>
                            <div class="text-[10px] text-slate-400 mt-1.5">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400">
                            Belum ada notifikasi baru untuk Anda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Papan Peringkat Prestasi Keseluruhan Siswa SMAN 1 Lengkong -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-950/60 rounded-full text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 mb-1.5">
                    <span>🏆 TATA TERTIB & PENGHARGAAN</span>
                    <span>•</span>
                    <span>SMAN 1 LENGKONG</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                    Papan Peringkat Siswa Berprestasi
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Akumulasi poin penghargaan prestasi akademik dan non-akademik seluruh siswa (Kategori R1 - R10).
                </p>
            </div>

            <!-- Posisi Siswa Yang Sedang Login -->
            <div class="p-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 rounded-2xl font-black text-xs shadow-md shrink-0 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-white/30 flex items-center justify-center text-sm">★</span>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-85">Posisi Prestasi Anda</div>
                    <div class="text-sm sm:text-base font-black">
                        {{ $studentRank ? 'Peringkat #' . $studentRank : 'Belum Ada Peringkat' }}
                        <span class="text-xs font-semibold">({{ $totalAchievementPoints }} Poin)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="responsive-table-container">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3 px-3">Rank</th>
                        <th class="py-3 px-3">Nama Siswa</th>
                        <th class="py-3 px-3">Kelas</th>
                        <th class="py-3 px-3 text-center">Jml Prestasi</th>
                        <th class="py-3 px-3 text-center">Total Poin</th>
                        <th class="py-3 px-3 text-right">Predikat Penghargaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topAchievers as $index => $item)
                        @php
                            $isCurrentStudent = ($item['student']->id === $student->id);
                            $rank = $index + 1;
                        @endphp
                        <tr class="transition {{ $isCurrentStudent ? 'bg-amber-50/80 dark:bg-amber-950/40 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                            <td class="py-3 px-3">
                                @if($rank === 1)
                                    <span class="w-7 h-7 rounded-xl bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-xs">🥇</span>
                                @elseif($rank === 2)
                                    <span class="w-7 h-7 rounded-xl bg-slate-300 text-slate-800 font-black text-xs flex items-center justify-center shadow-xs">🥈</span>
                                @elseif($rank === 3)
                                    <span class="w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs flex items-center justify-center shadow-xs">🥉</span>
                                @else
                                    <span class="w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-slate-900 dark:text-white {{ $isCurrentStudent ? 'text-amber-900 dark:text-amber-200' : '' }}">
                                        {{ $item['student']->name }}
                                    </span>
                                    @if($isCurrentStudent)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px]">
                                            ANDA
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400">NIS: {{ $item['student']->nis }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-300 font-semibold">
                                {{ $item['student']->schoolClass?->name ?? 'Umum' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300 text-xs">
                                    {{ $item['count'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-black text-amber-600 dark:text-amber-400 text-sm">
                                +{{ $item['points'] }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2.5 py-1 rounded-xl font-bold text-[10px] {{ $item['points'] >= 200 ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300' : ($item['points'] >= 125 ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                                    {{ $item['reward_status']['level'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 italic">
                                Belum ada data capaian prestasi siswa terverifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
