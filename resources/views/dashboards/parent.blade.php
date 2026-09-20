@extends('layouts.app')

@section('title', 'Dashboard Orang Tua / Wali')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Orang Tua & Identitas Anak -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 min-w-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-500 p-1 shrink-0">
                <div class="w-full h-full bg-slate-900 rounded-[12px] flex items-center justify-center font-bold text-lg sm:text-xl text-white">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
            </div>
            <div class="min-w-0">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[11px] sm:text-xs font-semibold border border-blue-500/30">
                    Pemantauan Perkembangan Anak
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-1 truncate">{{ $student->name }}</h2>
                <p class="text-xs text-slate-300 truncate">Kelas {{ $student->schoolClass?->name ?? '-' }} • NIS: {{ $student->nis }} • Wali: {{ $parentProfile->name ?? Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Real-time Presence Status Badge for Parent -->
        <div class="px-5 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center md:text-right">
            <div class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Status Kehadiran Hari Ini</div>
            @if($todayAttendance)
                <div class="text-sm font-bold text-emerald-300 mt-0.5 flex items-center justify-center md:justify-end gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    {{ $todayAttendance->status }} ({{ substr($todayAttendance->time, 0, 5) }} WIB)
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">Metode: {{ $todayAttendance->method }}</div>
            @else
                <div class="text-sm font-bold text-amber-400 mt-0.5 flex items-center justify-center md:justify-end gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Belum Melakukan Presensi
                </div>
            @endif
        </div>
    </div>

    <!-- Poin Summary Cards (Separated strictly!) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- 1. Prestasi Anak -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prestasi Terverifikasi</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">★</span>
            </div>
            <div class="text-3xl font-black text-amber-600 mt-3">{{ $student->totalAchievementPoints() }} Poin</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $student->reward_status['level'] }}</p>
        </div>

        <!-- 2. Pelanggaran Anak -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan Pelanggaran</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">!</span>
            </div>
            <div class="text-3xl font-black text-rose-600 mt-3">{{ $student->totalViolationPoints() }} Poin</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $student->guidance_status['level'] }}</p>
        </div>

        <!-- 3. 7 Kebiasaan Anak -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">7 Kebiasaan Hari Ini</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="text-3xl font-black text-emerald-600 mt-3">{{ $todayHabitLogs->where('is_completed', true)->count() }}/7</div>
            <p class="text-xs text-slate-500 mt-1">Kebiasaan positif terlaksana hari ini</p>
        </div>
    </div>

    <!-- 2-Columns: History Achievements & Violations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Capaian Prestasi Anak -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-3">Daftar Prestasi & Penghargaan Anak</h3>
            <div class="space-y-3">
                @forelse($achievements as $ach)
                    <div class="p-3.5 rounded-2xl bg-amber-50/50 border border-amber-200/60 text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900">{{ $ach->title }}</span>
                            <span class="font-black text-amber-600 text-sm">+{{ $ach->points }} Poin</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $ach->description }}</p>
                        <div class="text-[10px] text-slate-400 mt-1">Tanggal: {{ $ach->date->format('d M Y') }} • Tingkat: {{ $ach->level }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada catatan prestasi terverifikasi.</div>
                @endforelse
            </div>
        </div>

        <!-- Catatan Tata Tertib / Pelanggaran Anak -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-3">Riwayat Pelanggaran & Pembinaan Tata Tertib</h3>
            <div class="space-y-3">
                @forelse($violations as $viol)
                    <div class="p-3.5 rounded-2xl bg-rose-50/50 border border-rose-200/60 text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900">{{ $viol->title }}</span>
                            <span class="font-black text-rose-600 text-sm">{{ $viol->points }} Poin</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $viol->chronology ?? $viol->guidance_notes }}</p>
                        <div class="text-[10px] text-slate-400 mt-1">Tanggal: {{ $viol->date->format('d M Y') }} • Lokasi: {{ $viol->location ?? '-' }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Alhamdulillah! Tidak ada catatan pelanggaran tata tertib.</div>
                @endforelse
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

            <!-- Posisi Putra/Putri -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="p-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 rounded-2xl font-black text-xs shadow-md shrink-0 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-white/30 flex items-center justify-center text-sm">★</span>
                    <div>
                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-85">Peringkat Prestasi Keseluruhan</div>
                        <div class="text-sm sm:text-base font-black">
                            {{ $studentRank ? 'Peringkat #' . $studentRank : 'Belum Ada Peringkat' }}
                            @if(isset($totalSchoolStudents) && $totalSchoolStudents > 0)
                                <span class="text-xs font-semibold">dari {{ $totalSchoolStudents }} Siswa se-Sekolah</span>
                            @endif
                        </div>
                        <div class="text-[11px] font-bold opacity-90">Akumulasi: +{{ $student->totalAchievementPoints() }} Poin</div>
                    </div>
                </div>
                <a href="{{ route('discipline.ranking') }}" class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2 transition text-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Papan Peringkat Lengkap
                </a>
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
                            $isChild = ($item['student']->id === $student->id);
                            $rank = $index + 1;
                        @endphp
                        <tr class="transition {{ $isChild ? 'bg-amber-50/80 dark:bg-amber-950/40 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
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
                                    <span class="font-extrabold text-slate-900 dark:text-white {{ $isChild ? 'text-amber-900 dark:text-amber-200' : '' }}">
                                        {{ $item['student']->name }}
                                    </span>
                                    @if($isChild)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px]">
                                            ANAK ANDA
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

        <div class="p-4 mt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="text-slate-500 dark:text-slate-400">
                Menampilkan 10 peringkat prestasi teratas sekolah. Peringkat keseluruhan putra/putri Anda: <strong class="text-slate-800 dark:text-slate-200">#{{ $studentRank ?? '-' }}</strong> dari total <strong class="text-slate-800 dark:text-slate-200">{{ $totalSchoolStudents ?? count($topAchievers) }} siswa</strong>.
            </div>
            <a href="{{ route('discipline.ranking') }}" class="inline-flex items-center gap-1.5 font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                <span>Lihat Seluruh Papan Peringkat Siswa</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
