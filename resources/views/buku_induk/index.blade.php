@extends('layouts.app')

@section('title', 'Buku Induk Siswa Digital')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buku Induk Siswa Digital</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catatan rekam jejak resmi peserta didik dengan QR Code verifikasi keaslian dokumen.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('klaper.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                Buku Klaper (A-Z)
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('buku-induk.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, NIS, atau NISN..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="class_level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Tingkat Kelas</option>
                    <option value="X" {{ request('class_level') === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('class_level') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('class_level') === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Buku Induk Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Lembar Induk</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">NIS / NISN</th>
                        <th class="px-5 py-3.5">Kelas Saat Ini</th>
                        <th class="px-5 py-3.5">Tgl Masuk</th>
                        <th class="px-5 py-3.5 text-right">Aksi Lembar Induk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $index => $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900">
                                #{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">{{ $s->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $s->birth_place }}, {{ $s->birth_date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-800">{{ $s->nis }}</span>
                                <div class="text-[11px] text-slate-400">NISN: {{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded-lg border border-blue-200">
                                    Kelas {{ $s->current_class_level }} {{ $s->current_rombel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right space-x-1.5">
                                <a href="{{ route('buku-induk.show', $s) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition border border-indigo-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Buka Lembar
                                </a>
                                <a href="{{ route('buku-induk.print', $s) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition border border-slate-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data lembar buku induk siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
