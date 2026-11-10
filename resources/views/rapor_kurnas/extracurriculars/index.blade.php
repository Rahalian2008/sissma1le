@extends('layouts.app')

@section('title', 'Penilaian Ekstrakurikuler - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>🎨</span>
                <span>Penilaian Kegiatan Ekstrakurikuler</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Input predikat dan deskripsi keaktifan anggota ekstrakurikuler untuk dicetak pada lembar rapor resmi.
            </p>
        </div>

        <form method="GET" action="{{ route('rapor_kurnas.extracurriculars') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Ekstrakurikuler:</label>
            <select name="extra_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                @foreach($extracurriculars as $extra)
                    <option value="{{ $extra->id }}" {{ $selectedExtraId == $extra->id ? 'selected' : '' }}>
                        {{ $extra->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedExtra)
        <form action="{{ route('rapor_kurnas.extracurriculars.store') }}" method="POST">
            @csrf
            <input type="hidden" name="extracurricular_id" value="{{ $selectedExtra->id }}">

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                            Daftar Anggota: {{ $selectedExtra->name }} ({{ count($students) }} Siswa)
                        </h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="p-3 w-12 text-center">No</th>
                                <th class="p-3 w-56">Nama Siswa / Kelas</th>
                                <th class="p-3 w-40">Predikat Nilai</th>
                                <th class="p-3">Catatan / Deskripsi Kegiatan Ekstrakurikuler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($students as $index => $student)
                                @php
                                    $scoreData = $existingScores->get($student->id);
                                    $currentPred = $scoreData?->predicate ?? 'Baik';
                                    $currentDesc = $scoreData?->description ?? "Aktif dan berpartisipasi dengan antusias dalam seluruh kegiatan {$selectedExtra->name}.";
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-3 text-center text-slate-400 font-mono">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $student->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $student->schoolClass->name ?? '-' }}</div>
                                    </td>
                                    <td class="p-3">
                                        <select name="scores[{{ $index }}][predicate]" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1.5 font-bold text-indigo-700 dark:text-indigo-400">
                                            <option value="Sangat Baik" {{ $currentPred == 'Sangat Baik' ? 'selected' : '' }}>🌟 Sangat Baik</option>
                                            <option value="Baik" {{ $currentPred == 'Baik' ? 'selected' : '' }}>👍 Baik</option>
                                            <option value="Cukup" {{ $currentPred == 'Cukup' ? 'selected' : '' }}>👌 Cukup</option>
                                            <option value="Kurang" {{ $currentPred == 'Kurang' ? 'selected' : '' }}>⚠️ Kurang</option>
                                        </select>
                                    </td>
                                    <td class="p-3">
                                        <input type="text" 
                                               name="scores[{{ $index }}][description]" 
                                               value="{{ $currentDesc }}" 
                                               class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-1.5 focus:border-indigo-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400">
                                        Belum ada data anggota siswa yang terdaftar pada ekstrakurikuler ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($students) > 0)
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
                            <span>💾</span>
                            <span>Simpan Nilai Ekstrakurikuler</span>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @else
        <div class="p-12 text-center bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl text-slate-400">
            <span class="text-4xl block mb-3">🎨</span>
            <p>Silakan pilih kegiatan ekstrakurikuler di atas.</p>
        </div>
    @endif
</div>
@endsection

