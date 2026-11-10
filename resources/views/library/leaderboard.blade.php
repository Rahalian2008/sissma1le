@extends('layouts.app')

@section('title', 'Rapor & Rangking Pengunjung Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-900 border border-indigo-800/50 rounded-3xl shadow-xl text-white">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-xs font-bold uppercase tracking-wider">
                <span>🏆</span>
                <span>Gerakan Literasi Sekolah (GLS) • Indeks Pemustaka</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white flex items-center gap-3">
                <span>Rapor & Rangking Pengunjung Perpustakaan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-3xl leading-relaxed">
                Peringkat keaktifan siswa dan guru dalam mengunjungi perpustakaan, baik <strong>kunjungan langsung (offline)</strong> maupun <strong>kunjungan digital membaca e-book (online)</strong>, serta apresiasi piagam penghargaan resmi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start md:self-auto shrink-0">
            @if($currentUserRank)
                <div class="px-4 py-2.5 rounded-2xl bg-slate-800/90 border border-amber-500/40 text-center shadow-lg">
                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Peringkat Anda</div>
                    <div class="text-lg font-black text-white flex items-center justify-center gap-1">
                        <span>#{{ $currentUserRank['rank'] }}</span>
                        <span class="text-xs font-medium text-slate-300">({{ $currentUserRank['total_visits'] }}x Kunjungan)</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kunjungan</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ number_format($totalAllVisits) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Gabungan Offline & Online</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Kunjungan Offline</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">🏛️</span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ number_format($totalOfflineVisits) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Presensi Fisik & Barcode</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">Kunjungan Online</span>
                <span class="w-8 h-8 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-sm font-bold">✨</span>
            </div>
            <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-2">{{ number_format($totalOnlineVisits) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Pembaca E-Book Flip 3D</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pemustaka Teraktif</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">🥇</span>
            </div>
            <div class="text-base font-extrabold text-slate-900 dark:text-white mt-2 truncate">
                {{ $topVisitor ? $topVisitor['member']->name : 'Belum Ada Data' }}
            </div>
            <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">
                {{ $topVisitor ? $topVisitor['total_visits'] . 'x Kunjungan (' . $topVisitor['literacy_score'] . ' Poin)' : '-' }}
            </div>
        </div>
    </div>

    <!-- PODIUM TOP 3 PEMUSTAKA TELADAN -->
    @if($leaderboard->count() >= 3)
        <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border border-indigo-800/40 shadow-xl text-white">
            <div class="text-center max-w-md mx-auto mb-6">
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-400/30">Podium Kehormatan Literasi</span>
                <h2 class="text-lg sm:text-xl font-black mt-2">Duta Literasi Perpustakaan Terpilih</h2>
                <p class="text-xs text-slate-300 mt-1">Pemustaka dengan intensitas kunjungan dan budaya baca tertinggi periode {{ $periodLabel }}.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end max-w-4xl mx-auto pt-4">
                <!-- Juara 2 (Perak) -->
                @php $rank2 = $leaderboard->get(1); @endphp
                @if($rank2)
                    <div class="order-2 md:order-1 p-5 rounded-2xl bg-slate-800/80 border border-slate-700/80 text-center flex flex-col items-center justify-between relative shadow-lg">
                        <div class="w-14 h-14 rounded-full bg-slate-300 text-slate-900 flex items-center justify-center text-2xl font-black mb-2 shadow-md">
                            🥈
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-600 text-slate-100 mb-2">Peringkat 2</span>
                        <h3 class="text-sm font-bold text-white truncate max-w-[200px]">{{ $rank2['member']->name }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $rank2['member']->member_class_or_title }}</p>
                        
                        <div class="my-3 py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-700 w-full grid grid-cols-2 gap-1 text-[11px]">
                            <div>
                                <span class="text-slate-400 block text-[9px]">Offline / Online</span>
                                <span class="font-bold text-slate-200">{{ $rank2['offline_visits'] }} / {{ $rank2['online_visits'] }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px]">Skor Literasi</span>
                                <span class="font-extrabold text-amber-400">{{ $rank2['literacy_score'] }} Pts</span>
                            </div>
                        </div>

                        <a href="{{ route('library.certificate', ['member' => $rank2['member']->id, 'rank' => 2, 'period' => $periodLabel]) }}" target="_blank" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                            <span>🎓</span>
                            <span>Cetak Piagam</span>
                        </a>
                    </div>
                @endif

                <!-- Juara 1 (Emas) - Lebih Tinggi & Menonjol -->
                @php $rank1 = $leaderboard->get(0); @endphp
                @if($rank1)
                    <div class="order-1 md:order-2 p-6 rounded-3xl bg-gradient-to-b from-amber-500/20 via-slate-800 to-slate-900 border-2 border-amber-400/60 text-center flex flex-col items-center justify-between relative shadow-2xl md:-translate-y-3">
                        <div class="absolute -top-3.5 px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] uppercase tracking-wider shadow-lg">
                            👑 DUTA LITERASI UTAMA
                        </div>
                        <div class="w-18 h-18 rounded-full bg-amber-400 text-slate-950 flex items-center justify-center text-3xl font-black mb-2 shadow-xl border-2 border-white/40">
                            🥇
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase bg-amber-500/30 text-amber-300 border border-amber-400/40 mb-2">Peringkat 1 Terbaik</span>
                        <h3 class="text-base font-extrabold text-white truncate max-w-[220px]">{{ $rank1['member']->name }}</h3>
                        <p class="text-xs text-amber-200/90 font-medium mt-0.5">{{ $rank1['member']->member_class_or_title }}</p>
                        
                        <div class="my-3 py-2.5 px-4 rounded-xl bg-slate-950/70 border border-amber-500/30 w-full grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Total Kunjungan</span>
                                <span class="font-extrabold text-emerald-400">{{ $rank1['total_visits'] }}x ({{ $rank1['offline_visits'] }} Off / {{ $rank1['online_visits'] }} On)</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Skor Literasi</span>
                                <span class="font-black text-amber-400 text-sm">{{ $rank1['literacy_score'] }} Pts</span>
                            </div>
                        </div>

                        <a href="{{ route('library.certificate', ['member' => $rank1['member']->id, 'rank' => 1, 'period' => $periodLabel]) }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-amber-500/30">
                            <span>✨</span>
                            <span>Cetak Piagam Duta Literasi</span>
                        </a>
                    </div>
                @endif

                <!-- Juara 3 (Perunggu) -->
                @php $rank3 = $leaderboard->get(2); @endphp
                @if($rank3)
                    <div class="order-3 p-5 rounded-2xl bg-slate-800/80 border border-slate-700/80 text-center flex flex-col items-center justify-between relative shadow-lg">
                        <div class="w-14 h-14 rounded-full bg-amber-700 text-amber-100 flex items-center justify-center text-2xl font-black mb-2 shadow-md">
                            🥉
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-700/40 text-amber-300 mb-2">Peringkat 3</span>
                        <h3 class="text-sm font-bold text-white truncate max-w-[200px]">{{ $rank3['member']->name }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $rank3['member']->member_class_or_title }}</p>
                        
                        <div class="my-3 py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-700 w-full grid grid-cols-2 gap-1 text-[11px]">
                            <div>
                                <span class="text-slate-400 block text-[9px]">Offline / Online</span>
                                <span class="font-bold text-slate-200">{{ $rank3['offline_visits'] }} / {{ $rank3['online_visits'] }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px]">Skor Literasi</span>
                                <span class="font-extrabold text-amber-400">{{ $rank3['literacy_score'] }} Pts</span>
                            </div>
                        </div>

                        <a href="{{ route('library.certificate', ['member' => $rank3['member']->id, 'rank' => 3, 'period' => $periodLabel]) }}" target="_blank" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                            <span>🎓</span>
                            <span>Cetak Piagam</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('library.leaderboard') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Filter Kategori & Periode -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tipe Pemustaka -->
                <div class="flex items-center rounded-xl bg-slate-100 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700/60 text-xs">
                    <a href="{{ route('library.leaderboard', array_merge(request()->query(), ['type' => 'all'])) }}" class="px-3 py-1.5 rounded-lg font-bold transition {{ $type === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-white' }}">
                        Semua
                    </a>
                    <a href="{{ route('library.leaderboard', array_merge(request()->query(), ['type' => 'siswa'])) }}" class="px-3 py-1.5 rounded-lg font-bold transition {{ $type === 'siswa' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-white' }}">
                        🎓 Siswa
                    </a>
                    <a href="{{ route('library.leaderboard', array_merge(request()->query(), ['type' => 'guru'])) }}" class="px-3 py-1.5 rounded-lg font-bold transition {{ $type === 'guru' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-white' }}">
                        👨‍🏫 Guru / PTK
                    </a>
                </div>

                <!-- Periode -->
                <select name="period" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200 font-medium">
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>📅 Bulan Ini</option>
                    <option value="semester" {{ $period === 'semester' ? 'selected' : '' }}>📆 Semester Ini</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>🗓️ Tahun Ini</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>🌐 Sepanjang Waktu</option>
                </select>
            </div>

            <!-- Pencarian Nama -->
            <div class="flex items-center gap-2">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemustaka / NISN..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shrink-0">
                    Filter
                </button>
                @if($search || $period !== 'month' || $type !== 'all')
                    <a href="{{ route('library.leaderboard') }}" class="p-2 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tabel Peringkat Literasi & Keaktifan Pemustaka</h3>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan seluruh siswa & guru dengan akumulasi kunjungan offline, pembacaan e-book online, dan skor literasi.</p>
            </div>
            <span class="text-xs font-mono font-semibold text-slate-400">{{ $paginatedItems->total() }} Anggota Terdata</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4 w-16 text-center">Rangking</th>
                        <th class="py-3.5 px-4">Nama Pemustaka</th>
                        <th class="py-3.5 px-4">Kategori / Kelas</th>
                        <th class="py-3.5 px-4 text-center">Kunjungan Offline</th>
                        <th class="py-3.5 px-4 text-center">Kunjungan Online</th>
                        <th class="py-3.5 px-4 text-center">Buku Dipinjam</th>
                        <th class="py-3.5 px-4 text-center">Total Kunjungan</th>
                        <th class="py-3.5 px-4 text-center">Skor Literasi</th>
                        <th class="py-3.5 px-4 text-center">Predikat</th>
                        <th class="py-3.5 px-4 text-center w-32">Aksi Piagam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($paginatedItems as $item)
                        @php
                            $m = $item['member'];
                            $isTop3 = $item['rank'] <= 3;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition {{ $isTop3 ? 'bg-amber-500/5' : '' }}">
                            <!-- Rangking -->
                            <td class="py-3.5 px-4 text-center">
                                @if($item['rank'] === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-md">1</span>
                                @elseif($item['rank'] === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-300 text-slate-900 font-black text-xs shadow-md">2</span>
                                @elseif($item['rank'] === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700 text-amber-100 font-black text-xs shadow-md">3</span>
                                @else
                                    <span class="font-bold text-slate-500 font-mono">#{{ $item['rank'] }}</span>
                                @endif
                            </td>

                            <!-- Nama -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ $m->name }}</span>
                                    @if(Auth::check() && Auth::id() === $m->user_id)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] bg-indigo-500 text-white font-extrabold uppercase">Anda</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    {{ $m->member_code }} • {{ $m->member_identity }}
                                </div>
                            </td>

                            <!-- Kelas / Jabatan -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $m->member_type === 'guru' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/40' : 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800/40' }}">
                                    {{ $m->member_class_or_title }}
                                </span>
                            </td>

                            <!-- Offline Visits -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $item['offline_visits'] }}x</span>
                            </td>

                            <!-- Online Visits -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ $item['online_visits'] }}x</span>
                            </td>

                            <!-- Loans -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $item['total_loans'] }} Buku</span>
                            </td>

                            <!-- Total Visits -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $item['total_visits'] }}</span>
                            </td>

                            <!-- Skor Literasi -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-1 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black font-mono border border-amber-400/30">
                                    {{ $item['literacy_score'] }} Pts
                                </span>
                            </td>

                            <!-- Predikat Badge -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold whitespace-nowrap {{ $item['rank'] === 1 ? 'bg-amber-400 text-slate-950' : ($item['rank'] === 2 ? 'bg-slate-300 text-slate-900' : ($item['rank'] === 3 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    {{ $item['badge'] }}
                                </span>
                            </td>

                            <!-- Aksi Cetak Piagam -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('library.certificate', ['member' => $m->id, 'rank' => $item['rank'], 'period' => $periodLabel, 'predicate' => $item['predicate']]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-xs cursor-pointer" title="Cetak Piagam Penghargaan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Piagam</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                Belum ada data kunjungan yang tercatat untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedItems->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $paginatedItems->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

