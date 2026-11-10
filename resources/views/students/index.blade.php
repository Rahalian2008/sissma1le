@extends('layouts.app')

@section('title', 'Data Peserta Didik')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Peserta Didik</h1>
            <p class="text-xs text-slate-500 mt-0.5">Basis data terpadu seluruh siswa {{ $school->name ?? 'Sekolah' }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.demographics') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Analisis Demografi
            </a>
            <a href="{{ route('students.export-csv') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel / CSV
            </a>
            <a href="{{ Route::has('master.students') ? route('master.students') : route('sis.index') }}" class="inline-flex items-center px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Sinkron Dapodik / SIS
            </a>
            <x-import-modal type="students" title="Data Peserta Didik" />
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Siswa
            </a>
        </div>
    </div>

    @if(($summaryStats['totalActive'] ?? 0) === 0)
    <div class="p-6 bg-blue-50/80 border-2 border-dashed border-blue-200 rounded-2xl text-center space-y-3">
        <h3 class="text-base font-bold text-slate-800">Data Siswa di Administrasi Kesiswaan Sedang Kosong</h3>
        <p class="text-xs text-slate-500 max-w-lg mx-auto">
            Tersedia <strong>{{ $masterAvailableCount ?? 798 }} Siswa & Kartu RFID</strong> yang siap ditarik dari Data Master ke Administrasi Kesiswaan.
        </p>
        <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs inline-flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Tarik Data dari Master Siswa & RFID</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Ringkasan Statistik Demografi Siswa (Permintaan: Jumlah Total, Perkelas, Jenis Kelamin & Usia) -->
    <div x-data="{ showDetail: true, activeTab: 'kelas' }" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Ringkasan Demografi Peserta Didik Aktif</h2>
                    <p class="text-[11px] text-slate-500">Statistik real-time jumlah total siswa, per tingkat kelas, rombel, rasio gender, dan sebaran usia.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showDetail = !showDetail" type="button" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                    <span x-text="showDetail ? 'Sembunyikan Rincian' : 'Tampilkan Rincian'"></span>
                    <svg class="w-3.5 h-3.5 ml-1 transform transition-transform" :class="showDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>

        <!-- 3 Highlight Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mt-4">
            <!-- Total Siswa -->
            <div class="p-3.5 bg-gradient-to-br from-blue-50/60 to-indigo-50/60 border border-blue-100 rounded-xl">
                <div class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">Total Siswa Aktif</div>
                <div class="text-2xl font-black text-blue-950 mt-0.5">{{ number_format($summaryStats['totalActive'], 0, ',', '.') }} <span class="text-xs font-semibold text-blue-600">Siswa</span></div>
                <div class="text-[11px] text-blue-600 mt-1 flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span> Terdaftar di Dapodik & Buku Induk
                </div>
            </div>

            <!-- Rasio Gender (L & P) -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Komposisi Jenis Kelamin</div>
                <div class="flex items-center justify-between mt-1 text-xs font-bold">
                    <span class="text-blue-700">👦 L: {{ $summaryStats['maleActive'] }} ({{ $summaryStats['totalActive'] > 0 ? round(($summaryStats['maleActive'] / $summaryStats['totalActive']) * 100, 1) : 0 }}%)</span>
                    <span class="text-rose-600">👧 P: {{ $summaryStats['femaleActive'] }} ({{ $summaryStats['totalActive'] > 0 ? round(($summaryStats['femaleActive'] / $summaryStats['totalActive']) * 100, 1) : 0 }}%)</span>
                </div>
                <!-- Mini Bar -->
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden flex mt-2">
                    <div class="bg-blue-500 h-full" style="width: {{ $summaryStats['totalActive'] > 0 ? ($summaryStats['maleActive'] / $summaryStats['totalActive']) * 100 : 50 }}%"></div>
                    <div class="bg-rose-400 h-full" style="width: {{ $summaryStats['totalActive'] > 0 ? ($summaryStats['femaleActive'] / $summaryStats['totalActive']) * 100 : 50 }}%"></div>
                </div>
            </div>

            <!-- Total Rombongan Belajar -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Rombongan Belajar</div>
                <div class="text-2xl font-black text-slate-800 mt-0.5">{{ $summaryStats['rombelStats']->count() }} <span class="text-xs font-semibold text-slate-500">Rombel</span></div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Terbagi pada {{ $summaryStats['classStats']->count() }} tingkat kelas (X, XI, XII)
                </div>
            </div>

            <!-- Rentang Usia Dominan -->
            @php
                $topAgeBracket = collect($summaryStats['ageBrackets'])->sortDesc()->keys()->first() ?? '-';
            @endphp
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Usia Siswa Terbanyak</div>
                <div class="text-2xl font-black text-slate-800 mt-0.5">{{ $topAgeBracket }}</div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $summaryStats['ageBrackets'][$topAgeBracket] ?? 0 }} siswa dari data tanggal lahir
                </div>
            </div>
        </div>

        <!-- Detail Breakdown (Tabs: Per Tingkat Kelas, Per Rombel, Sebaran Usia) -->
        <div x-show="showDetail" x-collapse class="mt-4 pt-4 border-t border-slate-100">
            <div class="flex items-center space-x-2 border-b border-slate-200 pb-2 mb-3">
                <button type="button" @click="activeTab = 'kelas'" 
                        :class="activeTab === 'kelas' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Rekap Per Tingkat Kelas ({{ $summaryStats['classStats']->count() }})
                </button>
                <button type="button" @click="activeTab = 'rombel'" 
                        :class="activeTab === 'rombel' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Rekap Per Rombel ({{ $summaryStats['rombelStats']->count() }})
                </button>
                <button type="button" @click="activeTab = 'usia'" 
                        :class="activeTab === 'usia' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Sebaran Usia Siswa (6 Kategori)
                </button>
            </div>

            <!-- Tab 1: Per Tingkat Kelas -->
            <div x-show="activeTab === 'kelas'">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @forelse($summaryStats['classStats'] as $cs)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Kelas {{ $cs->current_class_level }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                <span class="text-blue-600 font-semibold">L: {{ $cs->male }}</span> &bull; 
                                <span class="text-rose-500 font-semibold">P: {{ $cs->female }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-slate-900">{{ $cs->total }}</span>
                            <div class="text-[10px] text-slate-400 font-medium">Siswa</div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-3 text-center py-4 text-xs text-slate-400">Belum ada data tingkat kelas.</div>
                    @endforelse
                </div>
            </div>

            <!-- Tab 2: Per Rombel -->
            <div x-show="activeTab === 'rombel'">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5">
                    @forelse($summaryStats['rombelStats'] as $rs)
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg hover:border-blue-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-800">{{ $rs->current_rombel }}</span>
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 font-bold text-[10px] rounded">{{ $rs->total }}</span>
                        </div>
                        <div class="text-[10.5px] text-slate-500 mt-1 flex justify-between">
                            <span class="text-blue-600 font-medium">L: {{ $rs->male }}</span>
                            <span class="text-rose-500 font-medium">P: {{ $rs->female }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-6 text-center py-4 text-xs text-slate-400">Belum ada data rombel aktif.</div>
                    @endforelse
                </div>
            </div>

            <!-- Tab 3: Sebaran Usia -->
            <div x-show="activeTab === 'usia'">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                    @foreach($summaryStats['ageBrackets'] as $bracket => $count)
                    @php
                        $genderDetail = $summaryStats['ageGenderBrackets'][$bracket] ?? ['L' => 0, 'P' => 0];
                        $percent = $summaryStats['totalActive'] > 0 ? round(($count / $summaryStats['totalActive']) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                        <div class="text-[11px] font-bold text-slate-600">{{ $bracket }}</div>
                        <div class="text-lg font-black text-slate-900 mt-0.5">{{ $count }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $percent }}% dari total</div>
                        <div class="text-[10.5px] mt-1 pt-1 border-t border-slate-200 flex justify-around">
                            <span class="text-blue-600 font-semibold">L: {{ $genderDetail['L'] }}</span>
                            <span class="text-rose-500 font-semibold">P: {{ $genderDetail['P'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('students.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, NIS, atau NISN..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="class_level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Tingkat Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->name }}" {{ request('class_level') === $c->name ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Identitas Siswa</th>
                        <th class="px-5 py-3.5">NIS / NISN</th>
                        <th class="px-5 py-3.5">L/P</th>
                        <th class="px-5 py-3.5">Kelas & Rombel</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('students.show', $s) }}" class="font-bold text-slate-900 text-sm hover:text-blue-600">
                                    {{ $s->name }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $s->school_origin ?? 'SMP' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-700">
                                <div>{{ $s->nis }}</div>
                                <div class="text-[10px] text-slate-400">{{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold">
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $s->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $s->gender }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $s->schoolClass?->name ?? $s->current_class_level }}</span>
                                @if($s->schoolClass && $s->schoolClass->grade)
                                    <span class="text-slate-400 text-[10px]">Tingkat {{ $s->schoolClass->grade }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('buku-induk.show', $s) }}" class="text-indigo-600 hover:underline font-semibold" title="Buku Induk">Buku Induk</a>
                                <a href="{{ route('students.show', $s) }}" class="text-blue-600 hover:underline font-semibold">Detail</a>
                                <a href="{{ route('students.edit', $s) }}" class="text-amber-600 hover:underline font-semibold">Edit</a>
                                <form action="{{ route('students.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline font-semibold cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="max-w-md mx-auto space-y-2">
                                    <div class="text-sm font-bold text-slate-700">Data Siswa Masih Kosong</div>
                                    <p class="text-xs text-slate-400">Data belum ditarik dari Master Data Siswa & RFID ke Administrasi Kesiswaan.</p>
                                    <form action="{{ route('tu.kesiswaan.pull_from_master') }}" method="POST" class="pt-2">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                            <span>Tarik Data dari Master Siswa & RFID</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3.5 border-t border-slate-100">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
