@extends('layouts.app')

@section('title', 'Pemanggilan Orang Tua / Wali - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Panggilan Orang Tua</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Pemanggilan Orang Tua / Wali</h1>
            <p class="text-xs text-slate-400 mt-0.5">Penerbitan surat pemanggilan resmi dan berita acara musyawarah pembinaan peserta didik</p>
        </div>
        <a href="{{ route('bk.parent-call.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 text-white font-bold text-xs shadow-md shadow-amber-600/20 hover:bg-amber-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Buat Surat Panggilan</span>
        </a>
    </div>

    <!-- Tabel Panggilan Ortu -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode / No. Surat</th>
                        <th class="py-3 px-4">Tanggal Hadir</th>
                        <th class="py-3 px-4">Nama Siswa & Kelas</th>
                        <th class="py-3 px-4">Alasan Pemanggilan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($calls as $c)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <span class="font-mono font-bold text-slate-900 dark:text-white text-[11px] block">{{ $c->call_code }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $c->letter_number ?: '-' }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <span class="font-bold text-slate-800 dark:text-white block">{{ \Carbon\Carbon::parse($c->call_date)->translatedFormat('d M Y') }}</span>
                            <span class="text-[10px] text-slate-400">Pukul: {{ $c->call_time ?: '08:30 WIB' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $c->student?->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $c->student?->schoolClass?->name }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300 max-w-xs truncate">
                            {{ $c->reason }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $c->status === 'SELESAI' || $c->status === 'HADIR' ? 'bg-emerald-500/10 text-emerald-600' : ($c->status === 'SURAT_DIKIRIM' ? 'bg-blue-500/10 text-blue-600' : 'bg-rose-500/10 text-rose-600') }}">
                                {{ $c->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('bk.parent-call.print-letter', $c) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-bold hover:bg-amber-100 transition text-[11px]">
                                Cetak Surat
                            </a>
                            <a href="{{ route('bk.parent-call.show', $c) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 transition text-[11px]">
                                Berita Acara
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada surat pemanggilan orang tua yang diterbitkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $calls->links() }}
        </div>
    </div>
</div>
@endsection

