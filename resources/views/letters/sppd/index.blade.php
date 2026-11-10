@extends('layouts.app')

@section('title', 'Surat Perintah Perjalanan Dinas (SPPD)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Surat Perintah Perjalanan Dinas (SPPD)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penatausahaan naskah dinas perjalanan dinas pejabat, pendidik, dan tenaga kependidikan berbasis standar tata naskah dinas.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('letters.sppd.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Terbitkan SPPD Baru
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('letters.sppd.index') }}" method="GET" class="flex items-center gap-3 text-xs">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor SPPD, nama pegawai, maksud perjalanan, atau kota tujuan..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold transition">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('letters.sppd.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl">Reset</a>
            @endif
        </form>
    </div>

    <!-- SPPD Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nomor SPPD</th>
                        <th class="px-5 py-3.5">Pegawai Yang Ditugaskan</th>
                        <th class="px-5 py-3.5">Maksud Perjalanan Dinas</th>
                        <th class="px-5 py-3.5">Tempat Tujuan</th>
                        <th class="px-5 py-3.5">Tgl Berangkat & Kembali</th>
                        <th class="px-5 py-3.5">Sumber Dana</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sppds as $sppd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-700">
                                {{ $sppd->sppd_number }}
                                @if($sppd->letter_task_number)
                                    <div class="text-[10px] text-slate-400 font-sans">Dasar: {{ $sppd->letter_task_number }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $sppd->employee->formatted_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">NIP. {{ $sppd->employee->nip ?? '-' }} &bull; {{ $sppd->employee->position ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs truncate text-slate-800 font-medium" title="{{ $sppd->assignment_purpose }}">
                                {{ $sppd->assignment_purpose }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">
                                {{ $sppd->destination_location }}
                                <div class="text-[10px] text-slate-400 font-normal">({{ $sppd->duration_days }} Hari)</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <div>{{ $sppd->departure_date->format('d/m/Y') }} s.d.</div>
                                <div>{{ $sppd->return_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ $sppd->budget_source }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('letters.sppd.print', $sppd) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition mr-1" title="Cetak Format Resmi SPPD 2 Halaman">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak SPPD
                                </a>
                                <a href="{{ route('letters.sppd.edit', $sppd) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer inline-flex items-center mr-1" title="Edit SPPD">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('letters.sppd.destroy', $sppd) }}" method="POST" onsubmit="return confirm('Hapus berkas SPPD ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus SPPD">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada berkas Surat Perintah Perjalanan Dinas (SPPD) yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($sppds->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $sppds->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
