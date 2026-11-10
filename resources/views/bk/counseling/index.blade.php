@extends('layouts.app')

@section('title', 'Layanan Bimbingan & Konseling - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Layanan Konseling</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Layanan Konseling Siswa</h1>
            <p class="text-xs text-slate-400 mt-0.5">Catatan konseling individual, kelompok, mediasi, dan alih tangan kasus berstandar POP-BK</p>
        </div>
        <a href="{{ route('bk.counseling.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Catat Sesi Konseling</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form action="{{ route('bk.counseling.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="field_of_service" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Bidang Layanan --</option>
                <option value="PRIBADI" {{ request('field_of_service') == 'PRIBADI' ? 'selected' : '' }}>Pribadi</option>
                <option value="SOSIAL" {{ request('field_of_service') == 'SOSIAL' ? 'selected' : '' }}>Sosial</option>
                <option value="BELAJAR" {{ request('field_of_service') == 'BELAJAR' ? 'selected' : '' }}>Belajar</option>
                <option value="KARIER" {{ request('field_of_service') == 'KARIER' ? 'selected' : '' }}>Karier</option>
            </select>
            <select name="session_type" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Jenis Layanan --</option>
                <option value="INDIVIDUAL" {{ request('session_type') == 'INDIVIDUAL' ? 'selected' : '' }}>Konseling Individual</option>
                <option value="KELOMPOK" {{ request('session_type') == 'KELOMPOK' ? 'selected' : '' }}>Konseling Kelompok</option>
                <option value="KLASIKAL" {{ request('session_type') == 'KLASIKAL' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                <option value="MEDIASI" {{ request('session_type') == 'MEDIASI' ? 'selected' : '' }}>Mediasi</option>
                <option value="REFERRAL" {{ request('session_type') == 'REFERRAL' ? 'selected' : '' }}>Alih Tangan Kasus</option>
            </select>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / topik..." class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 w-48">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-3 py-2 rounded-xl bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700 transition">Filter</button>
        </form>
    </div>

    <!-- Tabel Sesi Konseling -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Sesi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Siswa & Kelas</th>
                        <th class="py-3 px-4">Bidang Layanan</th>
                        <th class="py-3 px-4">Jenis Sesi</th>
                        <th class="py-3 px-4">Topik Konseling</th>
                        <th class="py-3 px-4">Konselor</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($sessions as $s)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white text-[11px]">
                            {{ $s->session_code }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($s->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('bk.student.profile360', $s->student) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600">
                                {{ $s->student?->name }}
                            </a>
                            <span class="block text-[10px] text-slate-400">{{ $s->student?->schoolClass?->name }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->field_badge['class'] }}">
                                {{ $s->field_badge['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-600 dark:text-slate-300">
                            {{ str_replace('_', ' ', $s->session_type) }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-white max-w-xs truncate">
                            {{ $s->topic }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                            {{ $s->counselor?->name ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $s->status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('bk.counseling.show', $s) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 font-bold hover:bg-teal-100 transition text-[11px]">
                                Detail
                            </a>
                            <a href="{{ route('bk.counseling.print', $s) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 transition text-[11px]">
                                Cetak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">Belum ada sesi konseling yang sesuai kriteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection

