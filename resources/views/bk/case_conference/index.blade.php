@extends('layouts.app')

@section('title', 'Konferensi Kasus (Case Conference) BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 border border-purple-200 text-xs font-semibold text-purple-700 mb-2">
                <span>Musyawarah Kasus Multi-Pihak Terpadu</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Konferensi Kasus (Case Conference)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Forum musyawarah resmi penanganan masalah peserta didik yang melibatkan Kepala Sekolah, Guru BK, Wali Kelas, Orang Tua, dan pihak terkait.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                &larr; Dashboard BK
            </a>
            <a href="{{ route('bk.case-conference.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Konferensi Kasus
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Total Kasus</span>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }} <span class="text-xs font-normal text-slate-400">Kasus</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Kasus Terlaksana</span>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['terlaksana'] }} <span class="text-xs font-normal text-slate-400">Sidang</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Kasus Selesai</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['selesai'] }} <span class="text-xs font-normal text-slate-400">Tuntas</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Terjadwal</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['terjadwal'] }} <span class="text-xs font-normal text-slate-400">Agenda</span></div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Notula & Berita Acara Konferensi Kasus</h3>
                <p class="text-xs text-slate-400">Total: {{ $conferences->total() }} Konferensi Kasus Tercatat</p>
            </div>
            <form action="{{ route('bk.case-conference.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul kasus atau siswa..." class="px-3 py-2 border border-slate-300 rounded-xl">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Kode & Tanggal</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Judul Kasus Musyawarah</th>
                        <th class="py-3 px-4">Pimpinan Sidang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($conferences as $index => $c)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">{{ $conferences->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900">{{ $c->conference_code }}</div>
                                <div class="text-[11px] text-slate-400">{{ $c->conference_date?->translatedFormat('d F Y') ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $c->student?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $c->student?->schoolClass?->name ?? '-' }} - NISN: {{ $c->student?->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $c->case_title }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $c->leader_name }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $c->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : ($c->status === 'TERLAKSANA' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('bk.case-conference.print', $c) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Cetak Berita Acara">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('bk.case-conference.show', $c) }}" class="p-1.5 text-purple-600 hover:text-purple-800 rounded-lg hover:bg-purple-50 transition" title="Detail Kasus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada berkas konferensi kasus yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($conferences->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $conferences->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

