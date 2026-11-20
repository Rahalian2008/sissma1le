@extends('layouts.app')

@section('title', 'Analisis Kebutuhan AKPD - ' . $akpd->title)

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Breadcrumb & Navigasi BK -->
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-xs font-semibold text-teal-700 mb-2">
                <span>Asesmen Diagnostik Kebutuhan Peserta Didik (Fase E)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $akpd->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Matriks analisis kebutuhan peserta didik, rekapitulasi prioritas layanan, dan butir masalah paling dominan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bk.akpd.class-report', [$akpd, 'class_id' => $selectedClassId]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Peta Profil Kelas &amp; Grafik</span>
            </a>
            <a href="{{ route('bk.akpd.print-class', [$akpd, 'class_id' => $selectedClassId]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Profil Kelas A4</span>
            </a>
            <a href="{{ route('bk.akpd.take', $akpd) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Input Respon Siswa</span>
            </a>
        </div>
    </div>

    <!-- Top 10 Masalah Paling Dominan Di Sekolah -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                Top 10 Butir Kebutuhan Paling Banyak Dipilih Siswa (Prioritas Tinggi)
            </h2>
            <span class="text-xs font-bold text-slate-400">Total: {{ $totalRespondentsFiltered }} Responden</span>
        </div>

        <div class="space-y-3">
            @forelse ($topQuestions as $q)
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $q->field_of_service === 'PRIBADI' ? 'bg-rose-100 text-rose-800' : ($q->field_of_service === 'SOSIAL' ? 'bg-amber-100 text-amber-800' : ($q->field_of_service === 'BELAJAR' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                {{ $q->field_of_service }} ({{ $q->code }})
                            </span>
                            @if($q->profile_dimension)
                                <span class="text-xs font-bold text-slate-700">{{ $q->profile_dimension }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-800 font-medium">{{ $q->statement }}</p>
                    </div>
                    <div class="text-right sm:shrink-0">
                        <div class="text-base font-black text-rose-600">{{ $q->percentage }}%</div>
                        <div class="text-[10px] text-slate-400 font-semibold">{{ $q->chosen_count }} Siswa</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs">Belum ada respon yang diinputkan.</div>
            @endforelse
        </div>
    </div>

    <!-- Filter Kelas & Daftar Respon Siswa -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rekapitulasi Respon Kebutuhan Siswa</h3>
                <p class="text-xs text-slate-400">Total: {{ $responses->total() }} Responden Terdaftar</p>
            </div>
            <form action="{{ route('bk.akpd.show', $akpd) }}" method="GET" class="flex items-center gap-2">
                <select name="class_id" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border-slate-200 py-2 px-3 bg-slate-50 text-slate-800 focus:ring-indigo-500">
                    <option value="">-- Semua Rombel / Kelas --</option>
                    <optgroup label="Kelas X (Sasaran Utama AKPD Fase E)">
                        @foreach ($gradeXClasses as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </optgroup>
                    @if($otherClasses->isNotEmpty())
                        <optgroup label="Kelas Lainnya">
                            @foreach ($otherClasses as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa &amp; NISN</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4 text-center">Pribadi</th>
                        <th class="py-3 px-4 text-center">Sosial</th>
                        <th class="py-3 px-4 text-center">Belajar</th>
                        <th class="py-3 px-4 text-center">Karier</th>
                        <th class="py-3 px-4 text-center">Kebutuhan Dominan</th>
                        <th class="py-3 px-4 text-center">Tanggal Isi</th>
                        <th class="py-3 px-4 text-center">Aksi / Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($responses as $index => $resp)
                        @php
                            $scores = $resp->score_per_field ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono">{{ $responses->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $resp->student?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $resp->student?->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700">
                                {{ $resp->student?->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-rose-600">
                                {{ $scores['PRIBADI']['percentage'] ?? 0 }}%
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-amber-600">
                                {{ $scores['SOSIAL']['percentage'] ?? 0 }}%
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-600">
                                {{ $scores['BELAJAR']['percentage'] ?? 0 }}%
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">
                                {{ $scores['KARIER']['percentage'] ?? 0 }}%
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $resp->highest_need_field === 'PRIBADI' ? 'bg-rose-100 text-rose-800' : ($resp->highest_need_field === 'SOSIAL' ? 'bg-amber-100 text-amber-800' : ($resp->highest_need_field === 'BELAJAR' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                    {{ $resp->highest_need_field }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-400 font-mono">
                                {{ $resp->submitted_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($resp->student)
                                        <a href="{{ route('bk.akpd.individual', [$akpd, $resp->student]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition" title="Lihat Laporan Individu">
                                            Laporan
                                        </a>
                                        <a href="{{ route('bk.akpd.print-individual', [$akpd, $resp->student]) }}" target="_blank" class="p-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Cetak Format A4">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    @endif
                                    @if(auth()->user()->isCounselor() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
                                        <form action="{{ url('/bk/akpd/' . $akpd->id . '/response/' . $resp->id) }}" method="POST" class="inline" onsubmit="return confirm('Peringatan: Reset respons AKPD untuk {{ addslashes($resp->student?->name ?? 'siswa ini') }}? Data angket akan dihapus dan siswa bersangkutan dapat mengisi kembali instrumen ini.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer" title="Reset Respons Siswa (Izinkan Mengulang)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                Belum ada siswa yang mengisi instrumen ini pada kelas yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($responses->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $responses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
