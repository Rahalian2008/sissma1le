@extends('layouts.app')

@section('title', 'Dashboard Wakasek Bid. Humas - Kemitraan & Prestasi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Wakasek Humas -->
    <div class="bg-gradient-to-r from-slate-900 via-pink-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-pink-500/20 text-pink-300 border border-pink-500/30 rounded-full text-xs font-bold tracking-wide">
                    BIDANG HUBUNGAN MASYARAKAT (HUMAS)
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    KEMITRAAN & CITRA SEKOLAH
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Wakasek Bid. Humas</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Pusat komunikasi eksternal sekolah: monitoring komunikasi orang tua, publikasi capaian prestasi siswa (R1-R10), serta penguatan citra positif SMAN 1 Lengkong.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('parent.portal') }}" target="_blank" class="px-4 py-2.5 bg-pink-600 hover:bg-pink-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-pink-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Portal Orang Tua Publik
            </a>
            <a href="{{ route('achievements.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Kelola Prestasi Siswa
            </a>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Prestasi Terverifikasi</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    🏆
                </div>
            </div>
            <div class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $totalVerifiedAchievements }} <span class="text-xs font-normal text-slate-400">Prestasi</span></div>
            <p class="text-xs text-slate-400 mt-1">Total Poin: <span class="font-bold text-amber-600 dark:text-amber-400">+{{ $totalAchievementPoints }}</span> poin penghargaan</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Jangkauan Orang Tua</span>
                <div class="w-8 h-8 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center font-bold">
                    👨‍👩‍👧
                </div>
            </div>
            <div class="text-3xl font-black text-pink-600 dark:text-pink-400 mt-2">{{ $totalParents }} <span class="text-xs font-normal text-slate-400">Wali Murid</span></div>
            <p class="text-xs text-slate-400 mt-1">Terhubung dengan database sekolah</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Kehadiran Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    📈
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $attendanceRateToday }}%</div>
            <p class="text-xs text-slate-400 mt-1">{{ $presentToday }} Hadir • {{ $lateToday }} Terlambat</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Total Siswa Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    👥
                </div>
            </div>
            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-2">{{ $totalStudents }}</div>
            <p class="text-xs text-slate-400 mt-1">Peserta didik SMAN 1 Lengkong</p>
        </div>
    </div>

    <!-- Papan Peringkat Bintang Prestasi Sekolah (Top 10 Achievers) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>🌟 Top 10 Bintang Prestasi Siswa (Siap Dipublikasikan)</span>
                </h2>
                <p class="text-xs text-slate-400">Daftar siswa berprestasi tertinggi untuk konten media sosial, website sekolah, dan papan mading</p>
            </div>
            <a href="{{ route('discipline.ranking') }}" class="text-xs font-bold text-pink-600 dark:text-pink-400 hover:underline">
                Peringkat Lengkap →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3 text-center w-12">No</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3 text-center">Jumlah Prestasi</th>
                        <th class="p-3 text-center">Total Poin</th>
                        <th class="p-3 text-center">Status Penghargaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topAchievers as $idx => $ach)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3 text-center font-bold">
                                @if($idx === 0)
                                    <span class="w-6 h-6 rounded-full bg-amber-400 text-slate-950 font-black inline-flex items-center justify-center text-xs shadow-xs">1</span>
                                @elseif($idx === 1)
                                    <span class="w-6 h-6 rounded-full bg-slate-300 text-slate-950 font-black inline-flex items-center justify-center text-xs shadow-xs">2</span>
                                @elseif($idx === 2)
                                    <span class="w-6 h-6 rounded-full bg-amber-700 text-white font-black inline-flex items-center justify-center text-xs shadow-xs">3</span>
                                @else
                                    <span class="text-slate-500 font-bold">{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $ach['student']->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $ach['student']->nisn }}</div>
                            </td>
                            <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $ach['student']->schoolClass->name ?? '-' }}
                            </td>
                            <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-200">
                                {{ $ach['count'] }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                    +{{ $ach['points'] }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-pink-100 dark:bg-pink-950/80 text-pink-700 dark:text-pink-300">
                                    {{ is_array($ach['reward_status']) ? ($ach['reward_status']['level'] ?? 'Penghargaan Siswa') : ($ach['reward_status'] ?? 'Penghargaan Siswa') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data prestasi siswa terverifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Prestasi Terkini Siap Warta -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Dokumentasi Prestasi Terkini</h3>
                <p class="text-xs text-slate-400">Rekap piagam dan capaian kompetisi yang diverifikasi</p>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-xs font-bold text-pink-600 dark:text-pink-400 hover:underline">
                Kelola Arsip Prestasi →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentAchievements as $rc)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg shrink-0">
                        🏅
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate">{{ $rc->student->name ?? 'Siswa' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">+{{ $rc->points }} Poin</span>
                        </div>
                        <div class="text-[11px] text-slate-600 dark:text-slate-300 font-semibold mt-0.5">{{ $rc->item->name ?? $rc->description ?? 'Prestasi Sekolah' }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                            <span>{{ $rc->student->schoolClass->name ?? '-' }}</span>
                            <span>•</span>
                            <span>{{ \Carbon\Carbon::parse($rc->date)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-6 text-slate-400 text-xs">Belum ada prestasi baru terdaftar.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
