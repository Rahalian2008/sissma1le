@extends('layouts.app')

@section('title', 'Sosiometri Relasi Siswa - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Sosiometri Kelas</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Sosiometri Dinamika Hubungan Kelas</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemetaan relasi sosial, popularitas, siswa bintang kelas, dan deteksi siswa terisolir di rombel</p>
        </div>
        <a href="{{ route('bk.sociometry.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 text-white font-bold text-xs shadow-md shadow-purple-600/20 hover:bg-purple-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Buat Sosiometri Baru</span>
        </a>
    </div>

    <!-- Tabel Sosiometri -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Judul Sosiometri</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Tema Relasi</th>
                        <th class="py-3 px-4">Tanggal Pelaksanaan</th>
                        <th class="py-3 px-4 text-center">Responden Masuk</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($sociometries as $soc)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $soc->title }}
                        </td>
                        <td class="py-3 px-4 font-bold text-purple-600 dark:text-purple-400">
                            {{ $soc->schoolClass?->name }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $soc->theme }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($soc->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/10 text-purple-600">
                                {{ $soc->responses_count }} Respon
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $soc->status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                {{ $soc->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('bk.sociometry.show', $soc) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-600 text-white font-bold hover:bg-purple-700 transition text-[11px] shadow-xs">
                                Matriks Sosiogram &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada instrumen sosiometri yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $sociometries->links() }}
        </div>
    </div>
</div>
@endsection

