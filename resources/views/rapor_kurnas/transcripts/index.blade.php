@extends('layouts.app')

@section('title', 'Transkrip Nilai & Buku Induk - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>📜</span>
                <span>Transkrip Nilai Rapor & Buku Induk</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Rekapitulasi riwayat nilai peserta didik untuk keperluan SNBP / SNMPTN, pendaftaran perguruan tinggi, seleksi kedinasan, dan Buku Induk Sekolah.
            </p>
        </div>

        <form method="GET" action="{{ route('rapor_kurnas.transcripts') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Kelas:</label>
            <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }} ({{ $cls->students()->count() }} Siswa)
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedClass)
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                    Daftar Siswa {{ $selectedClass->name }} ({{ count($students) }} Siswa)
                </h2>
                <span class="text-xs text-slate-400">Pilih jenis cetakan transkrip nilai</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5 w-32">NIS / NISN</th>
                            <th class="p-3.5">Nama Peserta Didik</th>
                            <th class="p-3.5 w-36 text-center">Rata-Rata Rapor</th>
                            <th class="p-3.5 w-64 text-center">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3.5 text-center text-slate-400 font-mono">
                                    {{ $index + 1 }}
                                </td>
                                <td class="p-3.5 font-mono text-slate-600 dark:text-slate-400">
                                    <div>{{ $student->nis ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $student->nisn ?? '-' }}</div>
                                </td>
                                <td class="p-3.5 font-bold text-slate-800 dark:text-white">
                                    {{ $student->name }}
                                </td>
                                <td class="p-3.5 text-center font-black text-indigo-600 dark:text-indigo-400">
                                    @php
                                        $avg = \App\Models\KurnasCourseScore::where('student_id', $student->id)->avg('score');
                                    @endphp
                                    {{ $avg ? number_format($avg, 1) : '-' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('rapor_kurnas.print_academic', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] transition">
                                            📊 Rapor Semester
                                        </a>
                                        <a href="{{ route('rapor_kurnas.print_full', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition">
                                            📖 Buku Induk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    Belum ada data siswa dalam kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

