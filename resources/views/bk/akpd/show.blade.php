@extends('layouts.app')

@section('title', 'Analisis Kebutuhan AKPD - ' . $akpd->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700 mb-2">
                <span>Hasil Asesmen Kebutuhan Peserta Didik</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $akpd->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Matriks analisis kebutuhan peserta didik, rekapitulasi prioritas layanan, dan butir masalah paling dominan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.akpd.print-analysis', [$akpd, 'class_id' => $selectedClassId]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Analisis Format POP-BK
            </a>
            <a href="{{ route('bk.akpd.take', $akpd) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                + Input Respon Siswa
            </a>
        </div>
    </div>

    <!-- Top 5 Masalah Paling Dominan Di Sekolah -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            Top 10 Butir Kebutuhan Paling Banyak Dipilih Siswa (Prioritas Tinggi)
        </h2>

        <div class="space-y-3">
            @forelse ($topQuestions as $q)
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $q->field_of_service === 'PRIBADI' ? 'bg-rose-100 text-rose-800' : ($q->field_of_service === 'SOSIAL' ? 'bg-amber-100 text-amber-800' : ($q->field_of_service === 'BELAJAR' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                {{ $q->field_of_service }} ({{ $q->code }})
                            </span>
                            <span class="text-xs font-bold text-slate-700">{{ $q->profile_dimension }}</span>
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
                <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-2 px-3">
                    <option value="">-- Semua Rombel / Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa & NISN</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4 text-center">Pribadi</th>
                        <th class="py-3 px-4 text-center">Sosial</th>
                        <th class="py-3 px-4 text-center">Belajar</th>
                        <th class="py-3 px-4 text-center">Karier</th>
                        <th class="py-3 px-4 text-center">Kebutuhan Dominan</th>
                        <th class="py-3 px-4 text-center">Tanggal Isi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($responses as $index => $resp)
                        @php
                            $scores = $resp->score_per_field ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">{{ $responses->firstItem() + $index }}</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
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

