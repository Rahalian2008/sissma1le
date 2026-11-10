@extends('layouts.app')

@section('title', 'Demografi Peserta Didik')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Banner -->
    <div class="bg-gradient-to-r from-indigo-900 via-blue-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-blue-500/10 blur-2xl"></div>
        <div class="absolute right-24 top-0 w-32 h-32 rounded-full bg-indigo-500/15 blur-xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-200 border border-blue-400/30 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Profil & Statistik Real-Time
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Demografi Peserta Didik</h1>
                <p class="text-blue-200/90 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Ringkasan analitik komprehensif profil siswa {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} mencakup komposisi rombel, sebaran gender, kelompok usia, jalur PPDB, afirmasi PIP, domisili wilayah dan mobilitas.
                </p>
                <div class="pt-1 flex flex-wrap items-center gap-2 text-xs text-slate-300">
                    <span class="font-semibold text-white">{{ $school->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V' }}</span>
                    <span>&bull;</span>
                    <span>{{ $school->city ?? 'Kabupaten Sukabumi' }}</span>
                    <span>&bull;</span>
                    <span class="font-mono text-emerald-300 font-bold">{{ $totalActive }} Siswa Aktif Terdaftar</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('students.demographics.print') }}" target="_blank" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Laporan Demografi</span>
                </a>
                <a href="{{ route('students.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-2xl text-xs font-semibold backdrop-blur-md transition flex items-center gap-2 border border-white/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Data Induk Siswa</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Primary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Active -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa Aktif</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalActive }}</div>
                <div class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                    <span>100% Terverifikasi Dapodik</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Gender Composition -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Komposisi Gender</div>
                <div class="text-xs font-bold text-indigo-600">{{ $malePercentage }}% L / {{ $femalePercentage }}% P</div>
            </div>
            <div class="flex items-baseline gap-3 mt-1">
                <span class="text-2xl font-extrabold text-blue-600">{{ $maleActive }} <span class="text-xs font-semibold text-slate-500">Laki</span></span>
                <span class="text-slate-300 font-bold">&bull;</span>
                <span class="text-2xl font-extrabold text-rose-500">{{ $femaleActive }} <span class="text-xs font-semibold text-slate-500">Perempuan</span></span>
            </div>
            <div class="w-full bg-rose-100 h-2 rounded-full mt-3 overflow-hidden flex">
                <div class="bg-blue-600 h-full rounded-l-full" style="width: {{ $malePercentage }}%"></div>
                <div class="bg-rose-500 h-full rounded-r-full" style="width: {{ $femalePercentage }}%"></div>
            </div>
        </div>

        <!-- PIP Assistance -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Afirmasi PIP / KIP</div>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ $pipActive }} <span class="text-xs font-medium text-slate-500">Siswa</span></div>
                <div class="text-xs text-slate-500 font-medium mt-1">
                    {{ $totalActive > 0 ? round(($pipActive / $totalActive) * 100, 1) : 0 }}% dari total siswa aktif
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Total Rombel -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rombongan Belajar</div>
                <div class="text-3xl font-extrabold text-indigo-600 mt-1">{{ count($rombelStats) }} <span class="text-xs font-medium text-slate-500">Rombel</span></div>
                <div class="text-xs text-slate-500 font-medium mt-1">
                    Kelas X, XI, dan XII
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
    </div>

    <!-- Row 1: Tingkat Kelas & Rombel Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Rekapitulasi Tingkat Kelas -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Distribusi Tingkat Kelas</h2>
                    <p class="text-xs text-slate-500">Komposisi jumlah siswa berdasarkan jenjang kelas X, XI, XII</p>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold font-mono">
                    {{ count($classStats) }} Tingkat
                </span>
            </div>
            <div class="p-5 space-y-4">
                @foreach($classStats as $cs)
                    @php
                        $pct = $totalActive > 0 ? round(($cs->total / $totalActive) * 100, 1) : 0;
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shadow-xs">
                                    {{ $cs->current_class_level }}
                                </span>
                                <div>
                                    <span class="font-bold text-slate-900 text-sm">Kelas {{ $cs->current_class_level }}</span>
                                    <span class="text-xs text-slate-400 ml-1">({{ $pct }}% dari total)</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-extrabold text-slate-900">{{ $cs->total }}</span>
                                <span class="text-xs text-slate-500 font-medium">Siswa</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600 mb-1.5">
                            <span class="text-blue-600 font-semibold">Laki-laki: {{ $cs->male }}</span>
                            <span class="text-rose-500 font-semibold">Perempuan: {{ $cs->female }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden flex">
                            <div class="bg-blue-600 h-full" style="width: {{ $cs->total > 0 ? ($cs->male / $cs->total) * 100 : 0 }}%"></div>
                            <div class="bg-rose-500 h-full" style="width: {{ $cs->total > 0 ? ($cs->female / $cs->total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rekapitulasi Rombongan Belajar (Rombel) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rombongan Belajar (Rombel)</h2>
                    <p class="text-xs text-slate-500">Daftar kelas aktif beserta perbandingan siswa L/P</p>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold font-mono">
                    {{ count($rombelStats) }} Kelas
                </span>
            </div>
            <div class="p-5 overflow-y-auto max-h-[380px]">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2.5">Nama Rombel</th>
                            <th class="px-3 py-2.5 text-center">Tingkat</th>
                            <th class="px-3 py-2.5 text-center text-blue-600">L</th>
                            <th class="px-3 py-2.5 text-center text-rose-500">P</th>
                            <th class="px-3 py-2.5 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rombelStats as $rs)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5 font-bold text-slate-900">{{ $rs->current_rombel }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold">{{ $rs->current_class_level }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-blue-600">{{ $rs->male }}</td>
                                <td class="px-3 py-2.5 text-center font-bold text-rose-500">{{ $rs->female }}</td>
                                <td class="px-3 py-2.5 text-right font-extrabold text-slate-900">{{ $rs->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Row 2: Kelompok Usia & Jalur Masuk PPDB -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Distribusi Kelompok Usia -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Distribusi Kelompok Usia Siswa</h2>
                    <p class="text-xs text-slate-500">Kalkulasi akurat usia peserta didik dari tanggal lahir Dapodik</p>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold">
                    Rentang Usia SMA
                </span>
            </div>
            <div class="p-5 space-y-3">
                @foreach($ageBrackets as $label => $data)
                    @php
                        $agePct = $totalActive > 0 ? round(($data['total'] / $totalActive) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-800">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-600 font-semibold">{{ $data['L'] }} L</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-rose-500 font-semibold">{{ $data['P'] }} P</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-extrabold text-slate-900">{{ $data['total'] }} Siswa ({{ $agePct }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden flex">
                            @if($data['total'] > 0)
                                <div class="bg-blue-500 h-full" style="width: {{ ($data['L'] / $data['total']) * 100 }}%"></div>
                                <div class="bg-rose-400 h-full" style="width: {{ ($data['P'] / $data['total']) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Jalur Masuk PPDB -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Jalur Penerimaan (PPDB Jawa Barat)</h2>
                    <p class="text-xs text-slate-500">Statistik penerimaan peserta didik baru berdasarkan jalur masuk</p>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold">
                    PPDB Disdik Jabar
                </span>
            </div>
            <div class="p-5 space-y-3">
                @forelse($entryPathStats as $ep)
                    @php
                        $epPct = $totalActive > 0 ? round(($ep->total / $totalActive) * 100, 1) : 0;
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                                {{ substr($ep->entry_path ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $ep->entry_path ?? 'Belum Terdata' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $epPct }}% dari total populasi siswa</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-extrabold text-slate-900">{{ $ep->total }}</span>
                            <span class="text-xs text-slate-500">Siswa</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">Belum ada data jalur penerimaan PPDB.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Row 3: Domisili Wilayah & Transportasi / Jarak -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sebaran Domisili Kecamatan -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Sebaran Domisili / Tempat Tinggal</h2>
                <p class="text-xs text-slate-500">Kecamatan domisili siswa di wilayah Kabupaten Sukabumi</p>
            </div>
            <div class="p-5 space-y-2.5 overflow-y-auto max-h-[320px]">
                @forelse($districtStats as $ds)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100 text-xs">
                        <span class="font-semibold text-slate-800">{{ $ds->residence_district }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-blue-600">{{ $ds->total }}</span>
                            <span class="text-slate-400 text-[11px]">Siswa</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Data domisili belum terinput.</div>
                @endforelse
            </div>
        </div>

        <!-- Moda Transportasi Siswa -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Moda Transportasi ke Sekolah</h2>
                <p class="text-xs text-slate-500">Sarana transportasi yang digunakan siswa setiap hari</p>
            </div>
            <div class="p-5 space-y-2.5 overflow-y-auto max-h-[320px]">
                @forelse($transportStats as $ts)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100 text-xs">
                        <span class="font-semibold text-slate-800">{{ $ts->transportation }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-indigo-600">{{ $ts->total }}</span>
                            <span class="text-slate-400 text-[11px]">Siswa</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Data transportasi belum terinput.</div>
                @endforelse
            </div>
        </div>

        <!-- Jarak Tempat Tinggal ke Sekolah -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Jarak Tempuh ke Sekolah</h2>
                <p class="text-xs text-slate-500">Estimasi radius jarak rumah siswa ke SMAN 1 Lengkong</p>
            </div>
            <div class="p-5 space-y-2.5 overflow-y-auto max-h-[320px]">
                @forelse($distanceStats as $dist)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100 text-xs">
                        <span class="font-semibold text-slate-800">{{ $dist->distance_to_school }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-emerald-600">{{ $dist->total }}</span>
                            <span class="text-slate-400 text-[11px]">Siswa</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Data jarak tempuh belum terinput.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
