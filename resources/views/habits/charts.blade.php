@extends('layouts.app')

@section('title', 'Diagram & Persentase Karakter - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full text-xs font-bold">
                    KARAKTER & 7 KEBIASAAN
                </span>
                <span class="px-3 py-1 bg-white/10 text-slate-300 rounded-full text-xs">
                    Evaluasi {{ $days }} Hari Terakhir
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Diagram & Persentase Karakter Hebat</h1>
            <p class="text-xs text-slate-300 mt-1">Pemantauan visual ketercapaian Gerakan 7 Kebiasaan Anak Indonesia Hebat siswa SMA Negeri 1 Lengkong.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('habits.journal') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Jurnal Harian
            </a>
            <a href="{{ route('habits.summary') }}" class="px-4 py-2.5 bg-white text-slate-800 hover:bg-slate-100 font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ringkasan
            </a>
        </div>
    </div>

    <!-- Filter & Live Typing Search -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('habits.charts') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Rentang Waktu</label>
                <select name="days" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-teal-500 text-xs font-medium">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir (1 Minggu)</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>14 Hari Terakhir (2 Minggu)</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir (1 Bulan)</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Hari Terakhir (2 Bulan)</option>
                </select>
            </div>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block font-bold text-slate-700 mb-1">Filter Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $classes->first()?->name ?? 'Kelas Binaan' }}" class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-teal-500 text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            @endif
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Cari Siswa (Ketik Nama / NIS)</label>
                <div class="relative">
                    <input type="text" name="search" id="habitSearchInput" value="{{ $search }}"
                        placeholder="Ketik nama siswa atau NIS untuk menyaring grafik & tabel..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-teal-500 text-xs transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    <!-- Overall Index Card + 7 Pillars Diagram Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Overall Index Donut Gauge -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col items-center text-center justify-between">
            <div class="w-full">
                <div class="flex items-center justify-between w-full border-b border-slate-100 pb-3">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Indeks Rata-Rata</span>
                    <span class="px-2.5 py-0.5 bg-teal-100 text-teal-800 text-[10px] font-black rounded-full">
                        {{ $days }} HARI
                    </span>
                </div>
                <h3 class="text-lg font-black text-slate-800 mt-4">Tingkat Ketercapaian Keseluruhan</h3>
                <p class="text-xs text-slate-500 mt-1">Rata-rata kedisiplinan pembiasaan karakter seluruh siswa yang terpilih.</p>
            </div>

            <!-- Visual Circle Progress -->
            <div class="relative w-44 h-44 my-6 flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="12" fill="transparent"/>
                    <circle cx="50" cy="50" r="40" stroke="#0d9488" stroke-width="12" stroke-linecap="round" fill="transparent"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="{{ 251.2 - (251.2 * min(100, $avgConsistency) / 100) }}"/>
                </svg>
                <div class="absolute flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-800">{{ $avgConsistency }}%</span>
                    <span class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">KONSISTENSI</span>
                </div>
            </div>

            <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                    <span class="text-slate-500">Kategori Mutu</span>
                    <span class="font-bold text-slate-800">
                        @if($avgConsistency >= 80)
                            <span class="text-emerald-600">Sangat Tinggi (A)</span>
                        @elseif($avgConsistency >= 65)
                            <span class="text-teal-600">Baik / Berkembang (B)</span>
                        @elseif($avgConsistency >= 50)
                            <span class="text-amber-600">Cukup (C)</span>
                        @else
                            <span class="text-rose-600">Perlu Pembinaan Khusus</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-500">Siswa Terpantau</span>
                    <span class="font-black text-slate-800">{{ $studentPerformances->count() }} Siswa</span>
                </div>
            </div>
        </div>

        <!-- Right 2 Columns: 7 Kebiasaan Breakdown Bars -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                <div>
                    <h3 class="text-base font-black text-slate-800">Ketercapaian per Pilar Kebiasaan</h3>
                    <p class="text-xs text-slate-500">Evaluasi persentase keterlaksanaan masing-masing pilar 7 Kebiasaan.</p>
                </div>
                <span class="text-xs text-slate-400 font-medium">Target: 100%</span>
            </div>

            <div class="space-y-4">
                @foreach($habitBreakdowns as $item)
                    @php
                        $h = $item['habit'];
                        $pct = $item['percentage'];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-lg bg-teal-100 text-teal-800 font-black text-[10px] flex items-center justify-center">
                                    {{ $h->order_number }}
                                </span>
                                <span class="font-bold text-slate-800">{{ $h->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold 
                                    {{ $pct >= 80 ? 'bg-emerald-100 text-emerald-800' : ($pct >= 65 ? 'bg-teal-100 text-teal-800' : ($pct >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                    {{ $item['status'] }}
                                </span>
                                <span class="font-black text-slate-800 w-12 text-right">{{ $pct }}%</span>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct >= 80 ? 'bg-emerald-500' : ($pct >= 65 ? 'bg-teal-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-rose-500')) }}"
                                style="width: {{ min(100, $pct) }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                            <span>{{ $h->description }}</span>
                            <span>{{ $item['completed_count'] }} kali dilakukan</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Class Comparison Diagram (If multiple classes exist) -->
    @if(count($classComparisons) > 0)
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                <div>
                    <h3 class="text-base font-black text-slate-800">Diagram Komparasi Ketercapaian Antar Kelas</h3>
                    <p class="text-xs text-slate-500">Persentase konsistensi 7 Kebiasaan Anak Hebat berdasarkan perbandingan antar rombel kelas.</p>
                </div>
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                    {{ count($classComparisons) }} Kelas Terpantau
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($classComparisons as $comp)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 hover:bg-white hover:shadow-xs transition">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-black text-slate-800 text-sm">Kelas {{ $comp['class_name'] }}</span>
                            <span class="text-[11px] font-bold text-slate-500">{{ $comp['student_count'] }} Siswa</span>
                        </div>
                        <div class="text-2xl font-black {{ $comp['percentage'] >= 75 ? 'text-teal-700' : ($comp['percentage'] >= 50 ? 'text-amber-600' : 'text-rose-600') }} mb-2">
                            {{ $comp['percentage'] }}%
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full {{ $comp['percentage'] >= 75 ? 'bg-teal-600' : ($comp['percentage'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                style="width: {{ min(100, $comp['percentage']) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Leaderboard Siswa Karakter Hebat -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-teal-50 to-white border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-800">Peringkat Konsistensi Siswa dalam 7 Kebiasaan</h3>
                <p class="text-xs text-slate-500">Urutan siswa berdasarkan persentase pelaksanaan kebiasaan pada periode ini.</p>
            </div>
            <span class="px-3 py-1 bg-teal-100 text-teal-800 font-bold text-xs rounded-full">
                {{ $studentPerformances->count() }} Siswa
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="habitPerformanceTable">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3 text-center">Peringkat</th>
                        <th class="p-3">Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3 text-center">Jurnal Tercatat</th>
                        <th class="p-3">Persentase Konsistensi</th>
                        <th class="p-3 text-center">Predikat Karakter</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentPerformances as $idx => $item)
                        @php
                            $s = $item['student'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition habit-student-row" data-search="{{ strtolower($s->name . ' ' . $s->nis . ' ' . ($s->schoolClass->name ?? '')) }}">
                            <td class="p-3 text-center">
                                @if($idx == 0)
                                    <span class="w-7 h-7 mx-auto rounded-full bg-amber-400 text-slate-900 font-black flex items-center justify-center text-xs shadow-xs">1</span>
                                @elseif($idx == 1)
                                    <span class="w-7 h-7 mx-auto rounded-full bg-slate-300 text-slate-800 font-black flex items-center justify-center text-xs shadow-xs">2</span>
                                @elseif($idx == 2)
                                    <span class="w-7 h-7 mx-auto rounded-full bg-amber-700 text-white font-black flex items-center justify-center text-xs shadow-xs">3</span>
                                @else
                                    <span class="text-slate-400 font-bold">#{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $s->nis }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded-md">
                                    {{ $s->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-semibold text-slate-700">
                                {{ $item['completed'] }} Checklist
                            </td>
                            <td class="p-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-28 bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-full rounded-full {{ $item['percentage'] >= 80 ? 'bg-emerald-500' : ($item['percentage'] >= 65 ? 'bg-teal-500' : 'bg-amber-500') }}"
                                            style="width: {{ min(100, $item['percentage']) }}%"></div>
                                    </div>
                                    <span class="font-black text-slate-800">{{ $item['percentage'] }}%</span>
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                    {{ $item['percentage'] >= 85 ? 'bg-emerald-100 text-emerald-800' : ($item['percentage'] >= 70 ? 'bg-teal-100 text-teal-800' : ($item['percentage'] >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                    {{ $item['badge'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tidak ada data siswa untuk kriteria yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live typing instant filter on table rows
    document.getElementById('habitSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.habit-student-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
</script>
@endsection
