@extends('layouts.app')

@section('title', 'Kunjungan Rumah (Home Visit) - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Home Visit</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Kunjungan Rumah (Home Visit)</h1>
            <p class="text-xs text-slate-400 mt-0.5">Instrumen pendukung BK untuk konfirmasi langsung kondisi lingkungan keluarga dan komitmen orang tua</p>
        </div>
        <a href="{{ route('bk.home-visit.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-600/20 hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Rencana Kunjungan Baru</span>
        </a>
    </div>

    <!-- Tabel Home Visit -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Kunjungan</th>
                        <th class="py-3 px-4">Tanggal Kunjungan</th>
                        <th class="py-3 px-4">Nama Siswa & Kelas</th>
                        <th class="py-3 px-4">Nama Orang Tua</th>
                        <th class="py-3 px-4">Latar Belakang Kasus</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($visits as $v)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white text-[11px]">
                            {{ $v->visit_code }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($v->visit_date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $v->student?->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $v->student?->schoolClass?->name }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $v->parent_name ?: ($v->student?->father_name ?: 'Orang Tua') }}
                        </td>
                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300 max-w-xs truncate">
                            {{ $v->case_background }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $v->status === 'TERLAKSANA' ? 'bg-emerald-500/10 text-emerald-600' : ($v->status === 'RENCANA' ? 'bg-blue-500/10 text-blue-600' : 'bg-rose-500/10 text-rose-600') }}">
                                {{ $v->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('bk.home-visit.show', $v) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-bold hover:bg-blue-100 transition text-[11px]">
                                Detail
                            </a>
                            <a href="{{ route('bk.home-visit.print', $v) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 transition text-[11px]">
                                Cetak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data kunjungan rumah (home visit) yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $visits->links() }}
        </div>
    </div>
</div>
@endsection

