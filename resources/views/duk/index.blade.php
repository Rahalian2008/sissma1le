@extends('layouts.app')

@section('title', 'Daftar Urut Kepangkatan (DUK) Pegawai')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Daftar Urut Kepangkatan (DUK)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemeringkatan hierarki kepegawaian otomatis berdasar Golongan Ruang, TMT Pangkat, Jabatan, Masa Kerja, dan Tingkat Pendidikan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('duk.export-csv', ['type' => $filterType]) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor CSV
            </a>
            <a href="{{ route('duk.print', ['type' => $filterType]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak DUK Resmi
            </a>
        </div>
    </div>

    <!-- Filter Tab Bar -->
    <div class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200 inline-flex flex-wrap gap-1">
        <a href="{{ route('duk.index', ['type' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterType === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Pegawai
        </a>
        <a href="{{ route('duk.index', ['type' => 'pns']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterType === 'pns' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Aparatur Sipil Negara (PNS / PPPK)
        </a>
        <a href="{{ route('duk.index', ['type' => 'guru']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterType === 'guru' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Tenaga Pendidik (Guru)
        </a>
        <a href="{{ route('duk.index', ['type' => 'tendik']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterType === 'tendik' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Tenaga Kependidikan (TU)
        </a>
    </div>

    <!-- DUK Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No. Urut</th>
                        <th class="px-5 py-3.5">Nama Pegawai & NIP</th>
                        <th class="px-4 py-3.5 text-center">Golongan / Pangkat</th>
                        <th class="px-4 py-3.5">TMT Pangkat</th>
                        <th class="px-5 py-3.5">Jabatan Sekarang</th>
                        <th class="px-4 py-3.5 text-center">Masa Kerja</th>
                        <th class="px-5 py-3.5">Pendidikan Terakhir</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rankedEmployees as $index => $emp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 text-center font-bold text-slate-900 font-mono">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('employees.show', $emp) }}" class="font-bold text-slate-900 hover:text-blue-600 transition">
                                    {{ $emp->formatted_name }}
                                </a>
                                <div class="text-[11px] text-slate-400 font-mono">NIP: {{ $emp->nip ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200 font-mono">
                                    {{ $emp->rank_group ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 font-mono">
                                {{ $emp->rank_tmt?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $emp->position ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-semibold text-slate-700">
                                {{ $emp->years_of_service_years ?? 0 }} Th {{ $emp->years_of_service_months ?? 0 }} Bl
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $emp->education_level ?? 'S1' }} {{ $emp->education_major }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($emp->employment_status === 'PNS') bg-blue-100 text-blue-800
                                    @elseif($emp->employment_status === 'PPPK') bg-emerald-100 text-emerald-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $emp->employment_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada data pegawai untuk kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
