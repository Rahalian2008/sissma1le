@extends('layouts.app')

@section('title', 'Papan Peringkat & Pantauan Tata Tertib - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-bold">
                TATA TERTIB & PRESTASI
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Papan Peringkat Siswa Berprestasi</h1>
            <p class="text-xs text-slate-300 mt-1">Apresiasi pencapaian prestasi siswa dan pemantauan tindak lanjut tata tertib sekolah.</p>
            @if(in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <div class="mt-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-200 border border-amber-500/30 font-bold text-xs">
                        🏆 Semua Kelas (Tingkat Sekolah)
                    </span>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('discipline.report') }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Tata Tertib
            </a>
        </div>
    </div>

    <!-- Filter & Live Typing Search Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('discipline.ranking') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="{{ in_array(Auth::user()->role, ['siswa', 'orang_tua']) ? 'sm:col-span-3' : 'sm:col-span-2' }}">
                <label class="block font-bold text-slate-700 mb-1">Cari Siswa (Ketik Nama / NIS)</label>
                <div class="relative">
                    <input type="text" name="search" id="rankingSearchInput" value="{{ request('search') }}"
                        placeholder="Ketik nama siswa atau nomor induk untuk menyaring..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500 focus:bg-white text-xs transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block font-bold text-slate-700 mb-1">Filter Kelas</label>
                <div class="flex gap-2">
                    @if(Auth::user()->role === 'wali_kelas')
                        <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @else
                        <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500 text-xs">
                            <option value="">Semua Kelas (Tingkat Sekolah)</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @if(request('search') || (request('class_id') && Auth::user()->role !== 'wali_kelas'))
                        <a href="{{ route('discipline.ranking') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl flex items-center justify-center font-bold">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </form>
    </div>

    <!-- Top 3 Podium (If available) -->
    @if($achievers->count() >= 3 && !request('search'))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
            <!-- Rank 2 -->
            <div class="bg-gradient-to-b from-slate-100 to-white p-6 rounded-3xl border border-slate-200 text-center order-2 md:order-1 relative shadow-xs">
                <div class="w-12 h-12 mx-auto bg-slate-200 text-slate-700 rounded-full flex items-center justify-center font-black text-lg border-2 border-white shadow-md">
                    2
                </div>
                <div class="font-extrabold text-slate-900 text-base mt-3 flex items-center justify-center gap-2 flex-wrap">
                    <span>{{ $achievers[1]['student']->name }}</span>
                    @if($myChildStudentId && $achievers[1]['student']->id === $myChildStudentId)
                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] tracking-wide shadow-xs animate-pulse">ANAK ANDA</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500">Kelas {{ $achievers[1]['student']->schoolClass?->name }} • NIS {{ $achievers[1]['student']->nis }}</div>
                <div class="text-xl font-black text-indigo-600 mt-2">{{ $achievers[1]['points'] }} Poin</div>
                <span class="inline-block mt-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[11px] font-bold">
                    {{ $achievers[1]['reward_status']['level'] }}
                </span>
            </div>

            <!-- Rank 1 -->
            <div class="bg-gradient-to-b from-amber-50 to-white p-6 rounded-3xl border-2 border-amber-300 text-center order-1 md:order-2 relative shadow-lg transform -translate-y-2">
                <div class="w-14 h-14 mx-auto bg-amber-400 text-slate-950 rounded-full flex items-center justify-center font-black text-xl border-4 border-amber-200 shadow-md">
                    👑 1
                </div>
                <div class="font-black text-slate-900 text-lg mt-3 flex items-center justify-center gap-2 flex-wrap">
                    <span>{{ $achievers[0]['student']->name }}</span>
                    @if($myChildStudentId && $achievers[0]['student']->id === $myChildStudentId)
                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] tracking-wide shadow-xs animate-pulse">ANAK ANDA</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500">Kelas {{ $achievers[0]['student']->schoolClass?->name }} • NIS {{ $achievers[0]['student']->nis }}</div>
                <div class="text-2xl font-black text-amber-600 mt-2">{{ $achievers[0]['points'] }} Poin</div>
                <span class="inline-block mt-2 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[11px] font-black border border-amber-200">
                    🏆 {{ $achievers[0]['reward_status']['level'] }}
                </span>
            </div>

            <!-- Rank 3 -->
            <div class="bg-gradient-to-b from-amber-50/40 to-white p-6 rounded-3xl border border-slate-200 text-center order-3 md:order-3 relative shadow-xs">
                <div class="w-12 h-12 mx-auto bg-amber-700/20 text-amber-900 rounded-full flex items-center justify-center font-black text-lg border-2 border-white shadow-md">
                    3
                </div>
                <div class="font-extrabold text-slate-900 text-base mt-3 flex items-center justify-center gap-2 flex-wrap">
                    <span>{{ $achievers[2]['student']->name }}</span>
                    @if($myChildStudentId && $achievers[2]['student']->id === $myChildStudentId)
                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] tracking-wide shadow-xs animate-pulse">ANAK ANDA</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500">Kelas {{ $achievers[2]['student']->schoolClass?->name }} • NIS {{ $achievers[2]['student']->nis }}</div>
                <div class="text-xl font-black text-amber-700 mt-2">{{ $achievers[2]['points'] }} Poin</div>
                <span class="inline-block mt-2 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[11px] font-bold">
                    {{ $achievers[2]['reward_status']['level'] }}
                </span>
            </div>
        </div>
    @endif

    @php
        $isStaff = !in_array(Auth::user()->role, ['siswa', 'orang_tua']);
    @endphp

    <!-- Layout: Full Leaderboard & Optional Guidance Watchlist (Staff Only) -->
    <div class="grid grid-cols-1 {{ $isStaff ? 'lg:grid-cols-3' : '' }} gap-6">
        <!-- Main Leaderboard -->
        <div class="{{ $isStaff ? 'lg:col-span-2' : 'w-full' }} bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Peringkat Prestasi Siswa Teratas</h3>
                    <p class="text-xs text-slate-500">Diurutkan berdasarkan akumulasi poin prestasi terverifikasi (R1-R10)</p>
                </div>
                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                    {{ $achievers->count() }} Siswa
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="leaderboardTable">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">Rank</th>
                            <th class="py-3.5 px-4">Nama Siswa</th>
                            <th class="py-3.5 px-4">Kelas</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Capaian</th>
                            <th class="py-3.5 px-4 text-center">Poin Prestasi</th>
                            <th class="py-3.5 px-4">Status Penghargaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($achievers as $i => $item)
                            @php
                                $isMyChild = ($myChildStudentId && $item['student']->id === $myChildStudentId);
                            @endphp
                            <tr class="transition ranking-row {{ $isMyChild ? 'bg-amber-50/90 dark:bg-amber-950/40 border-l-4 border-amber-500' : 'hover:bg-slate-50/70' }}">
                                <td class="py-3.5 px-4 text-center font-black {{ $i < 3 ? 'text-amber-600 text-sm' : ($isMyChild ? 'text-amber-700 font-extrabold' : 'text-slate-400') }}">
                                    #{{ $i + 1 }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-slate-900 student-name {{ $isMyChild ? 'text-amber-950 font-black' : '' }}">{{ $item['student']->name }}</div>
                                        @if($isMyChild)
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] tracking-wide shadow-xs shrink-0 animate-pulse">
                                                ANAK ANDA
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400">NIS: {{ $item['student']->nis }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded-md">
                                        {{ $item['student']->schoolClass?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-600">
                                    {{ $item['count'] }} Prestasi
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-black text-sm text-emerald-600">+{{ $item['points'] }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item['points'] >= 200)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                            👑 Anugerah Waluya Utama
                                        </span>
                                    @elseif($item['points'] >= 176)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Siswa Berprestasi Utama
                                        </span>
                                    @elseif($item['points'] >= 125)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            Siswa Berprestasi
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">
                                            Menuju Ambang ({{ 125 - $item['points'] }} lagi)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">
                                    Belum ada data prestasi yang terverifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($isStaff)
        <!-- Guidance Watchlist (Col 1 - Strictly Separated & Staff Only) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-bold text-slate-900">Pantauan Pembinaan</h3>
                    <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 font-bold rounded-full text-[10px]">
                        {{ $guidanceWatchlist->count() }} Kasus
                    </span>
                </div>
                <p class="text-xs text-slate-500">Poin pelanggaran dipisah secara ketat untuk kebutuhan penanganan konseling.</p>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[600px]">
                @forelse($guidanceWatchlist as $gw)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">{{ $gw['student']->name }}</span>
                            <span class="font-black text-rose-600">{{ $gw['points'] }} Poin</span>
                        </div>
                        <div class="text-[11px] text-slate-500">Kelas {{ $gw['student']->schoolClass?->name }} • {{ $gw['count'] }} Pelanggaran</div>
                        
                        <div class="pt-1">
                            @if($gw['points'] > 200)
                                <span class="inline-block px-2.5 py-1 bg-red-600 text-white font-black rounded-lg text-[10px]">
                                    Sidang Pleno / Rapat Khusus
                                </span>
                            @elseif($gw['points'] == 200)
                                <span class="inline-block px-2.5 py-1 bg-rose-600 text-white font-bold rounded-lg text-[10px]">
                                    SP 3 - BK & Kesiswaan
                                </span>
                            @elseif($gw['points'] >= 176)
                                <span class="inline-block px-2.5 py-1 bg-orange-500 text-white font-bold rounded-lg text-[10px]">
                                    SP 2 - Panggilan Orang Tua
                                </span>
                            @elseif($gw['points'] >= 125)
                                <span class="inline-block px-2.5 py-1 bg-amber-500 text-white font-bold rounded-lg text-[10px]">
                                    SP 1 - Peringatan Tertulis
                                </span>
                            @elseif($gw['points'] >= 75)
                                <span class="inline-block px-2.5 py-1 bg-yellow-500 text-slate-900 font-bold rounded-lg text-[10px]">
                                    Peringatan Lisan & Bimbingan
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 bg-slate-200 text-slate-700 font-medium rounded text-[10px]">
                                    Pantauan Ringan (&lt;75 Poin)
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400 text-xs">
                        Tidak ada siswa dalam status ambang pembinaan khusus.
                    </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // Client-side quick filter as user types
    document.getElementById('rankingSearchInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#leaderboardTable .ranking-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
            if (name.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
