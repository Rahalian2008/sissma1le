@extends('layouts.app')

@section('title', 'Laporan Terpadu Tata Tertib - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold">
                    LAPORAN TATA TERTIB
                </span>
                <span class="px-3 py-1 bg-white/10 text-slate-300 rounded-full text-xs">
                    {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Rekapitulasi Prestasi & Pelanggaran Siswa</h1>
            <p class="text-xs text-slate-300 mt-1">Laporan resmi terpadu kedisiplinan dan penghargaan siswa SMA Negeri 1 Lengkong.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('discipline.ranking') }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Papan Peringkat
            </a>
            <button type="button" onclick="openPrintDisciplineModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md shadow-emerald-900/30 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Cetak Laporan Resmi</span>
            </button>
        </div>
    </div>

    <!-- Filter & Live Typing Search -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs print:hidden">
        <form method="GET" action="{{ route('discipline.report') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                </div>
                @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kelas</label>
                    @if(Auth::user()->role === 'wali_kelas')
                        <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @else
                        <select name="class_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                @endif
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Cari Ketik Cepat</label>
                    <div class="relative">
                        <input type="text" name="search" id="disciplineSearchInput" value="{{ $search }}"
                            placeholder="Ketik nama / NIS / judul..."
                            class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 text-xs">Filter cepat:</span>
                    <a href="{{ route('discipline.report', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">Hari Ini</a>
                    <a href="{{ route('discipline.report', ['start_date' => now()->startOfWeek()->toDateString(), 'end_date' => now()->endOfWeek()->toDateString()]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">Minggu Ini</a>
                    <a href="{{ route('discipline.report', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->endOfMonth()->toDateString()]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">Bulan Ini</a>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('discipline.report') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">Reset</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-xs transition">Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Prestasi</span>
            <div class="text-2xl font-black text-emerald-800 mt-1">{{ $stats['total_achievements'] }}</div>
            <div class="text-[11px] text-emerald-600 mt-0.5">Catatan diverifikasi</div>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Poin Prestasi</span>
            <div class="text-2xl font-black text-emerald-800 mt-1">+{{ $stats['total_achievement_points'] }}</div>
            <div class="text-[11px] text-emerald-600 mt-0.5">Akumulasi positif</div>
        </div>

        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Pelanggaran</span>
            <div class="text-2xl font-black text-rose-800 mt-1">{{ $stats['total_violations'] }}</div>
            <div class="text-[11px] text-rose-600 mt-0.5">Pelanggaran dicatat</div>
        </div>

        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Poin Pelanggaran</span>
            <div class="text-2xl font-black text-rose-800 mt-1">-{{ $stats['total_violation_points'] }}</div>
            <div class="text-[11px] text-rose-600 mt-0.5">Akumulasi sanksi</div>
        </div>

        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Siswa Berprestasi</span>
            <div class="text-2xl font-black text-amber-800 mt-1">{{ $stats['top_achievers_count'] }}</div>
            <div class="text-[11px] text-amber-600 mt-0.5">Siswa berpenghargaan</div>
        </div>

        <div class="bg-purple-50 border border-purple-200 p-4 rounded-2xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700">Perlu Pembinaan</span>
            <div class="text-2xl font-black text-purple-800 mt-1">{{ $stats['students_with_violations'] }}</div>
            <div class="text-[11px] text-purple-600 mt-0.5">Tindak lanjut BK</div>
        </div>
    </div>

    <!-- Section 1: Prestasi Siswa -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-emerald-50 to-white border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Daftar Rekapitulasi Prestasi Siswa (R1 &ndash; R10)</h2>
                    <p class="text-xs text-slate-500">Pencatatan prestasi akademik, minat bakat, dan kejuaraan terverifikasi.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                {{ $achievements->count() }} Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="achievementTable">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Judul Prestasi</th>
                        <th class="p-3">Kategori & Butir</th>
                        <th class="p-3 text-center">Poin</th>
                        <th class="p-3 text-center">Tingkat</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($achievements as $ach)
                        <tr class="hover:bg-slate-50 transition achievement-row" data-search="{{ strtolower($ach->student->name . ' ' . $ach->student->nis . ' ' . $ach->title . ' ' . ($ach->student->schoolClass->name ?? '')) }}">
                            <td class="p-3 font-medium text-slate-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($ach->date)->format('d/m/Y') }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $ach->student->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $ach->student->nis }}</div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded-md">
                                    {{ $ach->student->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $ach->title }}</div>
                                @if($ach->description)
                                    <div class="text-[11px] text-slate-500 line-clamp-1">{{ $ach->description }}</div>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-md text-[10px]">
                                    {{ $ach->category->code ?? 'PRESTASI' }}
                                </span>
                                <span class="text-slate-600 text-[11px] ml-1">{{ $ach->category->name ?? '-' }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-black rounded-lg">
                                    +{{ $ach->points }}
                                </span>
                            </td>
                            <td class="p-3 text-center text-slate-600 font-medium whitespace-nowrap">
                                {{ $ach->level ?? 'Sekolah' }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px]">
                                    Diverifikasi
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Tidak ada data prestasi pada periode atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Pelanggaran Siswa (STRICTLY SEPARATED) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-rose-50 to-white border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Daftar Rekapitulasi Pelanggaran Siswa (P1 &ndash; P6)</h2>
                    <p class="text-xs text-slate-500">Pencatatan pelanggaran tata tertib dan tindakan pembinaan terverifikasi.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-rose-100 text-rose-800 font-bold text-xs rounded-full">
                {{ $violations->count() }} Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="violationTable">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Tindakan Pelanggaran</th>
                        <th class="p-3">Kategori Butir</th>
                        <th class="p-3 text-center">Poin Sanksi</th>
                        <th class="p-3">Tindak Lanjut</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($violations as $v)
                        <tr class="hover:bg-slate-50 transition violation-row" data-search="{{ strtolower($v->student->name . ' ' . $v->student->nis . ' ' . $v->title . ' ' . ($v->student->schoolClass->name ?? '')) }}">
                            <td class="p-3 font-medium text-slate-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($v->date)->format('d/m/Y') }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $v->student->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $v->student->nis }}</div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded-md">
                                    {{ $v->student->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $v->title }}</div>
                                @if($v->description)
                                    <div class="text-[11px] text-slate-500 line-clamp-1">{{ $v->description }}</div>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold rounded-md text-[10px]">
                                    {{ $v->category->code ?? 'PELANGGARAN' }}
                                </span>
                                <span class="text-slate-600 text-[11px] ml-1">{{ $v->category->name ?? '-' }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-black rounded-lg">
                                    -{{ $v->points }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600 text-[11px]">
                                {{ $v->follow_up ?? 'Teguran Lisan' }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 font-bold rounded-full text-[10px]">
                                    Diverifikasi
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Tidak ada data pelanggaran pada periode atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Cetak Laporan Tata Tertib Resmi (A4) -->
<div id="printDisciplineModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-bold text-lg">
                    🖨️
                </div>
                <div>
                    <h3 class="font-bold text-base text-white">Cetak Laporan Tata Tertib Resmi</h3>
                    <p class="text-xs text-slate-300">Prestasi & Pelanggaran standar dinas ber-KOP resmi A4</p>
                </div>
            </div>
            <button type="button" onclick="closePrintDisciplineModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Filter Cetak -->
        <form action="{{ route('reports.print.discipline') }}" method="GET" target="_blank" class="p-6 space-y-4 text-xs">
            <!-- Pilihan Periode (Radio Tabs) -->
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Jenis Periode</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="discipline-period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-indigo-600 bg-indigo-50/50 text-indigo-900 font-bold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="daily" checked onchange="handleDisciplinePeriodChange('daily')" class="hidden">
                        <span>📅 Harian</span>
                        <span class="text-[10px] text-slate-500 font-normal">Per Tanggal</span>
                    </label>
                    <label class="discipline-period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="weekly" onchange="handleDisciplinePeriodChange('weekly')" class="hidden">
                        <span>🗓️ Mingguan</span>
                        <span class="text-[10px] text-slate-500 font-normal">7 Hari / Minggu</span>
                    </label>
                    <label class="discipline-period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="monthly" onchange="handleDisciplinePeriodChange('monthly')" class="hidden">
                        <span>📊 Bulanan</span>
                        <span class="text-[10px] text-slate-500 font-normal">Per Bulan</span>
                    </label>
                    <label class="discipline-period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="custom" onchange="handleDisciplinePeriodChange('custom')" class="hidden">
                        <span>⚙️ Kustom</span>
                        <span class="text-[10px] text-slate-500 font-normal">Rentang Bebas</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Input Container based on Period Selection -->
            <!-- 1. Harian -->
            <div id="container-disc-daily" class="disc-period-container">
                <label class="block font-semibold text-slate-700 mb-1">Pilih Tanggal</label>
                <input type="date" name="date" value="{{ $endDate }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none focus:border-indigo-500 focus:bg-white text-xs">
            </div>

            <!-- 2. Mingguan -->
            <div id="container-disc-weekly" class="disc-period-container hidden bg-slate-50 p-3 rounded-xl border border-slate-200">
                <p class="text-slate-600">Laporan mingguan akan mencetak catatan prestasi dan pelanggaran siswa selama 7 hari terakhir / minggu berjalan secara otomatis.</p>
            </div>

            <!-- 3. Bulanan -->
            <div id="container-disc-monthly" class="disc-period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none focus:border-indigo-500 text-xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none focus:border-indigo-500 text-xs">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- 4. Kustom -->
            <div id="container-disc-custom" class="disc-period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                </div>
            </div>

            <!-- Filter Kelas -->
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Target Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-700 cursor-not-allowed text-xs" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 outline-none focus:border-indigo-500 focus:bg-white text-xs">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            @endif

            <!-- Modal Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closePrintDisciplineModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Buka Dokumen Cetak (A4)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live typing instant filter for both tables
    document.getElementById('disciplineSearchInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        
        // Filter Achievement Rows
        document.querySelectorAll('.achievement-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });

        // Filter Violation Rows
        document.querySelectorAll('.violation-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    function openPrintDisciplineModal() {
        document.getElementById('printDisciplineModal')?.classList.remove('hidden');
    }

    function closePrintDisciplineModal() {
        document.getElementById('printDisciplineModal')?.classList.add('hidden');
    }

    function handleDisciplinePeriodChange(type) {
        document.querySelectorAll('.disc-period-container').forEach(c => c.classList.add('hidden'));
        const target = document.getElementById('container-disc-' + type);
        if (target) target.classList.remove('hidden');

        document.querySelectorAll('.discipline-period-option').forEach(opt => {
            const input = opt.querySelector('input');
            if (input && input.value === type) {
                opt.classList.add('border-indigo-600', 'bg-indigo-50/50', 'text-indigo-900');
                opt.classList.remove('border-slate-200', 'text-slate-700');
            } else {
                opt.classList.remove('border-indigo-600', 'bg-indigo-50/50', 'text-indigo-900');
                opt.classList.add('border-slate-200', 'text-slate-700');
            }
        });
    }
</script>
@endsection
