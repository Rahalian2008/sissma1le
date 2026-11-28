@extends('layouts.app')

@section('title', 'Pelanggaran & Disiplin Siswa - Adm. Kesiswaan')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('tu.kesiswaan.index') }}" class="hover:text-blue-600 font-medium">Adm. Kesiswaan</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Pelanggaran Tata Tertib</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Catatan Kedisiplinan &amp; Pelanggaran Tata Tertib</h1>
            <p class="text-xs text-slate-400 mt-0.5">Monitoring otomatis dari aplikasi Kesiswaan - Tata Tertib Siswa (Akumulasi Poin, Tindakan Disiplin, dan Status Pembinaan)</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('tu.kesiswaan.violations.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekap Pelanggaran</span>
            </a>
        </div>
    </div>

    <!-- Statistik Pelanggaran Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400">Total Pelanggaran</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">Semua Kejadian</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-rose-200/80 dark:border-rose-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">Total Akumulasi Poin</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['total_points']) }}</div>
            <span class="text-[10px] text-rose-500 font-semibold">Poin Pelanggaran</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Dalam Proses Pembinaan</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['proses']) }}</div>
            <span class="text-[10px] text-amber-500 font-semibold">Bimbingan / SP</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Selesai / Tuntas</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['selesai']) }}</div>
            <span class="text-[10px] text-emerald-500 font-semibold">Konseling Selesai</span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form action="{{ route('tu.kesiswaan.violations.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Siswa / Pelanggaran</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / Kasus..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Penanganan</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Status --</option>
                    <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Tuntas</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas / Rombel</label>
                <select name="class_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">Filter</button>
                <a href="{{ route('tu.kesiswaan.violations.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggaran Siswa -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Daftar Kejadian Pelanggaran Tata Tertib</h3>
                <p class="text-xs text-slate-400 mt-0.5">Basis data resmi dari Tim Ketertiban Kesiswaan &amp; Guru Piket</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                Mode: Tampil &amp; Cetak Rekapitulasi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa &amp; NISN</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jenis Pelanggaran</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Poin</th>
                        <th class="py-3 px-4">Tindakan / Pembinaan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($violations as $index => $v)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">{{ $violations->firstItem() + $index }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $v->student?->name ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $v->student?->nisn ?: '-' }}</div>
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $v->student?->schoolClass?->name ?? ($v->schoolClass?->name ?? '-') }}
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ $v->title }}
                            @if($v->chronology)
                                <div class="text-[11px] text-slate-400 font-normal line-clamp-1">{{ $v->chronology }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $v->category?->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center font-black text-rose-600">
                            {{ $v->points }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $v->guidance_notes ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $v->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $v->status === 'selesai' ? 'Tuntas' : 'Dalam Proses' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $v->date?->translatedFormat('d F Y') ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            Belum ada catatan pelanggaran tata tertib yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($violations->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $violations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
