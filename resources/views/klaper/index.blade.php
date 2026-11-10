@extends('layouts.app')

@section('title', 'Buku Klaper Siswa (A-Z)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buku Klaper Siswa (A - Z)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar abjad induk siswa terstruktur untuk pencarian data arsip sekolah.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('klaper.export-csv', ['letter' => request('letter', 'all')]) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor CSV
            </a>
            <a href="{{ route('klaper.print', request()->all()) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Klaper
            </a>
        </div>
    </div>

    <!-- Alphabet Navigation Bar -->
    <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200 overflow-x-auto">
        <div class="flex items-center gap-1 min-w-max">
            <a href="{{ route('klaper.index', array_merge(request()->except('letter'), ['letter' => 'all'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('letter', 'all') === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                SEMUA
            </a>
            @foreach (range('A', 'Z') as $char)
                <a href="{{ route('klaper.index', array_merge(request()->except('letter'), ['letter' => $char])) }}" 
                   class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-bold transition {{ request('letter') === $char ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-200' }}">
                    {{ $char }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Klaper Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Abjad</th>
                        <th class="px-5 py-3.5">Nama Lengkap Siswa</th>
                        <th class="px-5 py-3.5">NIS</th>
                        <th class="px-5 py-3.5">NISN</th>
                        <th class="px-5 py-3.5">L/P</th>
                        <th class="px-5 py-3.5">Kelas / Rombel</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-blue-600 font-mono">
                                {{ strtoupper(substr($s->name, 0, 1)) }}
                            </td>
                            <td class="px-5 py-3 font-bold text-slate-900">
                                {{ $s->name }}
                            </td>
                            <td class="px-5 py-3 font-mono font-semibold text-slate-700">
                                {{ $s->nis }}
                            </td>
                            <td class="px-5 py-3 font-mono text-slate-500">
                                {{ $s->nisn ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-1.5 py-0.5 rounded text-[11px] font-bold {{ $s->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $s->gender }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                Kelas {{ $s->current_class_level }} {{ $s->current_rombel }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($s->status === 'Aktif') bg-emerald-100 text-emerald-800
                                    @elseif($s->status === 'Lulus') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('buku-induk.show', $s) }}" class="px-2.5 py-1 text-blue-600 hover:bg-blue-50 rounded-lg font-semibold transition">
                                    Lihat
                                </a>
                                <a href="{{ route('students.edit', $s) }}" class="px-2.5 py-1 text-amber-600 hover:bg-amber-50 rounded-lg font-semibold transition">
                                    Edit
                                </a>
                                <form action="{{ route('students.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-rose-600 hover:bg-rose-50 rounded-lg font-semibold transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada siswa dengan awalan abjad ini.
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
