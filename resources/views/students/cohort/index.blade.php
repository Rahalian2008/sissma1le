@extends('layouts.app')

@section('title', 'Analisis Kohort Siswa - SIKATAS SMA1LE')

@section('content')
<div class="space-y-6">
    <!-- Header Page & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wider">
                    Kesiswaan &bull; Statistik
                </span>
                <span class="text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Standar Dapodik & Manajemen Pendidikan</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Analisis Kohort Peserta Didik (Student Cohort Flow)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan aliran perkembangan angkatan masuk dari Tingkat X, XI, XII hingga kelulusan dan angka efisiensi internal sekolah.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('students.cohort.print', ['cohort' => $selectedYear]) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Kohort Resmi
            </a>
        </div>
    </div>

    <!-- Filter Angkatan Kohort -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('students.cohort.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs w-full sm:w-auto">
            <label class="font-bold text-slate-700">Pilih Angkatan / Tahun Masuk:</label>
            <select name="cohort" onchange="this.form.submit()" class="px-3 py-1.5 border border-slate-300 rounded-lg font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($cohortYears as $yr)
                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                        Angkatan {{ $yr }}/{{ $yr + 1 }} (Masuk Tahun {{ $yr }})
                    </option>
                @endforeach
            </select>
            <span class="text-slate-400 hidden sm:inline">|</span>
            <span class="text-slate-600 font-medium">Terpilih: <strong>Angkatan {{ $cohortLabel }}</strong></span>
        </form>

        <div class="text-[11px] text-slate-500 italic">
            *Metodologi: Model rekonstruksi kohort murni (True Cohort Flow Method)
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Masuk -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase text-slate-500 tracking-wider">Total Masuk Awal (X)</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $initialTotal }} <span class="text-xs font-normal text-slate-500">Siswa</span></h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Laki-laki: <strong>{{ $initialMale }}</strong> &bull; Perempuan: <strong>{{ $initialFemale }}</strong></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <!-- Angka Bertahan (Survival Rate) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase text-slate-500 tracking-wider">Angka Bertahan (Survival)</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $survivalRate }}%</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Sebanyak <strong>{{ $surviving }}</strong> siswa tetap aktif/lulus</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Tingkat Kelulusan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase text-slate-500 tracking-wider">Tingkat Kelulusan</p>
                <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ $graduationRate }}%</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Lulus: <strong>{{ $graduated }}</strong> (L: {{ $graduatedMale }}, P: {{ $graduatedFemale }})</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <!-- Dropout & Mutasi -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase text-slate-500 tracking-wider">Putus Sekolah / Mutasi</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $dropout + $mutatedOut }} <span class="text-xs font-normal text-slate-500">Siswa</span></h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Mutasi Keluar: <strong>{{ $mutatedOut }}</strong> &bull; DO/Keluar: <strong>{{ $dropout }}</strong></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
        </div>
    </div>

    <!-- Matriks Perkembangan Kohort (Tabel Utama) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-sm">Matriks Alur Perkembangan Angkatan {{ $cohortLabel }} (Jenjang SMA 3 Tahun)</h2>
            <span class="text-xs text-slate-500">Satuan: Orang / Persentase (%)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200 text-center">
                    <tr>
                        <th class="px-4 py-3 text-left" rowspan="2">Fase Perkembangan Pendidikan</th>
                        <th class="px-3 py-3" colspan="3">Jumlah Siswa</th>
                        <th class="px-4 py-3 text-right" rowspan="2">Rasio / Persentase</th>
                        <th class="px-4 py-3 text-left" rowspan="2">Keterangan / Status Administrasi</th>
                    </tr>
                    <tr class="bg-slate-200/60 text-[11px]">
                        <th class="px-3 py-1.5 w-20">Laki-Laki</th>
                        <th class="px-3 py-1.5 w-20">Perempuan</th>
                        <th class="px-3 py-1.5 w-24">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="bg-blue-50/40 font-semibold">
                        <td class="px-4 py-3">1. Siswa Masuk Awal (Tahun I - Tingkat X)</td>
                        <td class="px-3 py-3 text-center">{{ $initialMale }}</td>
                        <td class="px-3 py-3 text-center">{{ $initialFemale }}</td>
                        <td class="px-3 py-3 text-center font-bold text-blue-700">{{ $initialTotal }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold">100.0%</td>
                        <td class="px-4 py-3 text-slate-500 text-[11px]">Pendaftar baru & diterima resmi pada Kelas X</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 pl-8 text-slate-600">&bull; Masih aktif belajar di Tingkat X</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->where('current_class_level', 'X')->where('gender', 'L')->where('status', 'Aktif')->count() }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->where('current_class_level', 'X')->where('gender', 'P')->where('status', 'Aktif')->count() }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold">{{ $inClassX }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-slate-500">{{ round(($inClassX / max($initialTotal, 1)) * 100, 1) }}%</td>
                        <td class="px-4 py-2.5 text-slate-500 text-[11px]">Siswa baru tahun pertama berjalan</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 pl-8 text-slate-600">&bull; Berhasil naik ke Tingkat XI (Tahun II)</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->whereIn('current_class_level', ['XI', 'XII'])->where('gender', 'L')->count() + $graduatedMale }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->whereIn('current_class_level', ['XI', 'XII'])->where('gender', 'P')->count() + $graduatedFemale }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold text-emerald-700">{{ $inClassXI + $inClassXII + $graduated }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-emerald-700 font-bold">{{ round((($inClassXI + $inClassXII + $graduated) / max($initialTotal, 1)) * 100, 1) }}%</td>
                        <td class="px-4 py-2.5 text-slate-500 text-[11px]">Naik tingkat ke kelas XI secara reguler</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 pl-8 text-slate-600">&bull; Berhasil naik ke Tingkat XII (Tahun III)</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->where('current_class_level', 'XII')->where('gender', 'L')->count() + $graduatedMale }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $students->where('current_class_level', 'XII')->where('gender', 'P')->count() + $graduatedFemale }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold text-indigo-700">{{ $inClassXII + $graduated }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-indigo-700 font-bold">{{ round((($inClassXII + $graduated) / max($initialTotal, 1)) * 100, 1) }}%</td>
                        <td class="px-4 py-2.5 text-slate-500 text-[11px]">Mencapai tingkat akhir pendidikan menengah</td>
                    </tr>
                    <tr class="bg-emerald-50/50 font-semibold">
                        <td class="px-4 py-3">2. Lulus Tepat Waktu (3 Tahun Pendidikan)</td>
                        <td class="px-3 py-3 text-center">{{ $graduatedMale }}</td>
                        <td class="px-3 py-3 text-center">{{ $graduatedFemale }}</td>
                        <td class="px-3 py-3 text-center font-bold text-emerald-700">{{ $graduated }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">{{ $graduationRate }}%</td>
                        <td class="px-4 py-3 text-slate-500 text-[11px]">Menyelesaikan seluruh kurikulum & menerima Ijazah</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 pl-8 text-slate-600">3. Mutasi Keluar Sekolah (Pindah Sekolah)</td>
                        <td class="px-3 py-2.5 text-center">{{ $mutatedOutMale }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $mutatedOutFemale }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold text-amber-700">{{ $mutatedOut }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-amber-700">{{ $mutationRate }}%</td>
                        <td class="px-4 py-2.5 text-slate-500 text-[11px]">Mutasi ke SMA/SMK lain dengan surat resmi</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 pl-8 text-slate-600">4. Putus Sekolah / Mengundurkan Diri (Drop Out)</td>
                        <td class="px-3 py-2.5 text-center">{{ $dropoutMale }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $dropoutFemale }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold text-rose-700">{{ $dropout }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rose-700">{{ $dropoutRate }}%</td>
                        <td class="px-4 py-2.5 text-slate-500 text-[11px]">Keluar permanen sebelum menuntaskan jenjang SMA</td>
                    </tr>
                    <tr class="bg-slate-50 font-bold border-t-2 border-slate-300">
                        <td class="px-4 py-3">Indikator Efisiensi Internal: Angka Bertahan (Survival Rate)</td>
                        <td class="px-3 py-3 text-center">{{ $students->whereIn('status', ['Aktif', 'Lulus'])->where('gender', 'L')->count() }}</td>
                        <td class="px-3 py-3 text-center">{{ $students->whereIn('status', ['Aktif', 'Lulus'])->where('gender', 'P')->count() }}</td>
                        <td class="px-3 py-3 text-center text-blue-700 font-extrabold">{{ $surviving }}</td>
                        <td class="px-4 py-3 text-right font-mono text-blue-700 font-extrabold">{{ $survivalRate }}%</td>
                        <td class="px-4 py-3 text-slate-600 text-[11px]">Rasio peserta didik yang berhasil dipertahankan sistem</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan Komparasi Lintas Angkatan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-sm">Tabel Komparasi Lintas Angkatan (Multi-Year Cohort Comparison)</h2>
            <span class="text-xs text-slate-500">Riwayat 4 Tahun Ajaran</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200 text-center">
                    <tr>
                        <th class="px-4 py-3 text-left">Angkatan / Tahun Masuk</th>
                        <th class="px-3 py-3">Total Masuk (X)</th>
                        <th class="px-3 py-3">Masih Aktif</th>
                        <th class="px-3 py-3">Telah Lulus</th>
                        <th class="px-3 py-3">Mutasi Keluar</th>
                        <th class="px-3 py-3">Drop Out (DO)</th>
                        <th class="px-4 py-3 text-right">Survival Rate</th>
                        <th class="px-4 py-3 text-right">Graduation Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($allCohortsSummary as $row)
                        <tr class="hover:bg-slate-50 transition {{ $row['year'] == $selectedYear ? 'bg-blue-50/50 font-semibold' : '' }}">
                            <td class="px-4 py-3 text-slate-900 font-medium">
                                Angkatan {{ $row['label'] }} (Masuk {{ $row['year'] }})
                                @if($row['year'] == $selectedYear)
                                    <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">Terpilih</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center font-bold">{{ $row['total_entry'] }}</td>
                            <td class="px-3 py-3 text-center text-blue-600">{{ $row['active'] }}</td>
                            <td class="px-3 py-3 text-center text-emerald-600 font-bold">{{ $row['graduated'] }}</td>
                            <td class="px-3 py-3 text-center text-amber-600">{{ $row['mutated_out'] }}</td>
                            <td class="px-3 py-3 text-center text-rose-600">{{ $row['dropout'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">{{ $row['survival_rate'] }}%</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-indigo-700">{{ $row['graduation_rate'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rincian Peserta Didik dalam Angkatan Terpilih -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-800 text-sm">Daftar Peserta Didik Angkatan {{ $cohortLabel }}</h2>
                <p class="text-[11px] text-slate-500">Total data: {{ $students->count() }} peserta didik terdaftar pada tahun masuk {{ $selectedYear }}.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NIS / NISN</th>
                        <th class="px-4 py-3">Nama Lengkap Siswa</th>
                        <th class="px-3 py-3 text-center">L/P</th>
                        <th class="px-4 py-3">Rombel Awal</th>
                        <th class="px-4 py-3">Tingkat & Rombel Saat Ini</th>
                        <th class="px-4 py-3">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-center">Status Terkini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $index => $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">
                                {{ $s->nis }} <span class="text-slate-400 font-normal">/ {{ $s->nisn ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $s->name }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $s->gender === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $s->gender }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $s->admission_class ?? 'Kelas X' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $s->current_rombel ?? ('Tingkat '.$s->current_class_level) }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->translatedFormat('d M Y') : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($s->status === 'Aktif')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Aktif ({{ $s->current_class_level }})
                                    </span>
                                @elseif($s->status === 'Lulus')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        Lulus ({{ $s->graduation_year ?? 'Alumni' }})
                                    </span>
                                @elseif($s->status === 'Mutasi Keluar')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Mutasi Keluar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        {{ $s->status }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data peserta didik pada angkatan tahun {{ $selectedYear }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
