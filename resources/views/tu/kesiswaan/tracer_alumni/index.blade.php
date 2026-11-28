@extends('layouts.app')

@section('title', 'Tracer Study Alumni - Adm. Kesiswaan')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tu.kesiswaan.index') }}" class="hover:text-blue-600 font-medium">Adm. Kesiswaan</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Tracer Alumni</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Tracer Study &amp; Penelusuran Tamatan Alumni</h1>
            <p class="text-xs text-slate-400 mt-0.5">Basis data terpadu sebaran alumni ke jenjang Perguruan Tinggi (PTN/PTS/Kedinasan), Dunia Usaha/Industri (DUDI), dan Wirausaha (Sumber: Bimbingan Konseling)</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('bk.tracer.public_form') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Formulir Alumni</span>
            </a>
            <a href="{{ route('tu.kesiswaan.tracer_alumni.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekapitulasi Tracer</span>
            </a>
        </div>
    </div>

    <!-- Kartu Statistik Distribusi Alumni -->
    @php
        $tot = $stats['total'] > 0 ? $stats['total'] : 1;
        $pctKuliah = round(($stats['kuliah'] / $tot) * 100, 1);
        $pctKerja = round(($stats['bekerja'] / $tot) * 100, 1);
        $pctWirausaha = round(($stats['wirausaha'] / $tot) * 100, 1);
        $pctKursus = round(($stats['kursus'] / $tot) * 100, 1);
        $pctMencari = round(($stats['mencari_kerja'] / $tot) * 100, 1);
        $pctBelum = round(($stats['belum'] / $tot) * 100, 1);
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400">Total Terdata</span>
                <span class="text-base">📋</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">Responden Alumni</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Kuliah (PT)</span>
                <span class="text-base">🎓</span>
            </div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($stats['kuliah']) }}</div>
            <span class="text-[10px] text-indigo-500 font-bold">{{ $pctKuliah }}% dari total</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Bekerja</span>
                <span class="text-base">💼</span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($stats['bekerja']) }}</div>
            <span class="text-[10px] text-emerald-500 font-bold">{{ $pctKerja }}% dari total</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Wirausaha</span>
                <span class="text-base">🚀</span>
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($stats['wirausaha']) }}</div>
            <span class="text-[10px] text-amber-500 font-bold">{{ $pctWirausaha }}% dari total</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-cyan-200/80 dark:border-cyan-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400">Kursus/Pelatihan</span>
                <span class="text-base">📜</span>
            </div>
            <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400">{{ number_format($stats['kursus']) }}</div>
            <span class="text-[10px] text-cyan-500 font-bold">{{ $pctKursus }}% dari total</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500">Mencari Kerja</span>
                <span class="text-base">🔍</span>
            </div>
            <div class="text-2xl font-black text-slate-700 dark:text-slate-300">{{ number_format($stats['mencari_kerja']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">{{ $pctMencari }}% dari total</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-rose-200/80 dark:border-rose-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">Belum Bekerja</span>
                <span class="text-base">🏡</span>
            </div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($stats['belum']) }}</div>
            <span class="text-[10px] text-rose-500 font-semibold">{{ $pctBelum }}% dari total</span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form action="{{ route('tu.kesiswaan.tracer_alumni.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Alumni</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / Kampus / Kantor..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Kelulusan</label>
                <select name="graduation_year" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Angkatan (2005 - {{ date('Y') }}) --</option>
                    @foreach($graduationYears as $year)
                        <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kegiatan</label>
                <select name="activity_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Status --</option>
                    <option value="KULIAH" {{ request('activity_status') == 'KULIAH' ? 'selected' : '' }}>Kuliah / Studi Lanjut</option>
                    <option value="BEKERJA" {{ request('activity_status') == 'BEKERJA' ? 'selected' : '' }}>Bekerja</option>
                    <option value="WIRAUSAHA" {{ request('activity_status') == 'WIRAUSAHA' ? 'selected' : '' }}>Wirausaha / Bisnis Mandiri</option>
                    <option value="KURSUS_PELATIHAN" {{ request('activity_status') == 'KURSUS_PELATIHAN' ? 'selected' : '' }}>Kursus / Pelatihan</option>
                    <option value="MENCARI_KERJA" {{ request('activity_status') == 'MENCARI_KERJA' ? 'selected' : '' }}>Mencari Kerja</option>
                    <option value="BELUM_MEMUNGKINKAN" {{ request('activity_status') == 'BELUM_MEMUNGKINKAN' ? 'selected' : '' }}>Belum Memungkinkan Bekerja</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">Filter Data</button>
                <a href="{{ route('tu.kesiswaan.tracer_alumni.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Tracer Alumni -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Daftar Responden Tracer Alumni</h3>
                <p class="text-xs text-slate-400 mt-0.5">Sinkron otomatis dengan entri tracer kuesioner Bimbingan Konseling</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                Mode: Tampil &amp; Cetak Resmi (Read-Only)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Lengkap &amp; NISN</th>
                        <th class="py-3 px-4">L/P</th>
                        <th class="py-3 px-4">Tahun Lulus</th>
                        <th class="py-3 px-4">Status Kegiatan</th>
                        <th class="py-3 px-4">Rincian Institusi / Kampus / Kantor</th>
                        <th class="py-3 px-4">Domisili &amp; Kontak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($alumni as $index => $a)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">{{ $alumni->firstItem() + $index }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $a->full_name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $a->nisn ?: '-' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $a->gender === 'L' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                {{ $a->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">
                            {{ $a->graduation_year }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $a->activity_status_badge['color'] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ $a->activity_status_badge['icon'] ?? '•' }} {{ $a->activity_status_badge['label'] ?? $a->activity_status }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($a->activity_status === 'KULIAH')
                                <div class="font-bold text-indigo-600 dark:text-indigo-400">{{ $a->campus_name ?: '-' }}</div>
                                <div class="text-[11px] text-slate-500">Prodi: {{ $a->study_program ?: '-' }} ({{ $a->college_level ?: 'S1' }})</div>
                                @if($a->admission_track)<div class="text-[10px] text-slate-400">Jalur: {{ $a->admission_track }}</div>@endif
                            @elseif($a->activity_status === 'BEKERJA')
                                <div class="font-bold text-emerald-600 dark:text-emerald-400">{{ $a->company_name ?: '-' }}</div>
                                <div class="text-[11px] text-slate-500">Posisi: {{ $a->job_position ?: '-' }}</div>
                                @if($a->work_field)<div class="text-[10px] text-slate-400">Bidang: {{ $a->work_field }}</div>@endif
                            @elseif($a->activity_status === 'WIRAUSAHA')
                                <div class="font-bold text-amber-600 dark:text-amber-400">{{ $a->business_name ?: '-' }}</div>
                                <div class="text-[11px] text-slate-500">Usaha: {{ $a->business_field ?: '-' }}</div>
                            @elseif($a->activity_status === 'KURSUS_PELATIHAN')
                                <div class="font-bold text-cyan-600 dark:text-cyan-400">{{ $a->training_institution ?: 'Lembaga Kursus' }}</div>
                                <div class="text-[11px] text-slate-500">Bidang: {{ $a->training_field ?: '-' }}</div>
                            @else
                                <span class="text-slate-400 italic">{{ $a->activity_status_badge['label'] ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-800 dark:text-slate-200">{{ $a->current_city ?: ($a->current_address ?: '-') }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $a->phone ?: '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            Belum ada data tracer alumni yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alumni->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $alumni->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
