@extends('layouts.app')

@section('title', 'Perkembangan Nilai Siswa & SNBP Tracker - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>📉</span>
                <span>Perkembangan Nilai Siswa & SNBP Tracker</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Lacak tren riwayat nilai rapor semester 1 s.d. 6, analisis peningkatan konsistensi akademik, dan pantau kuota pemeringkatan SNBP (Standar e-Rapor 2025 Hal. 79-82).
            </p>
        </div>

        <!-- Filter Kelas & Siswa -->
        <form method="GET" action="{{ route('rapor_kurnas.growth') }}" class="flex flex-wrap items-center gap-2">
            <div>
                <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-bold">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }} ({{ $c->grade }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="student_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-bold">
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ $selectedStudentId == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} (NISN: {{ $st->nisn ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedStudent)
        @php
            $validAverages = array_filter($semesterAverages, fn($v) => !is_null($v));
            $cumulativeGpa = !empty($validAverages) ? round(array_sum($validAverages) / count($validAverages), 2) : 0;
            
            // Cek tren kenaikan
            $firstAvg = reset($validAverages) ?: null;
            $lastAvg = end($validAverages) ?: null;
            $isIncreasing = ($lastAvg && $firstAvg && $lastAvg >= $firstAvg);
            
            $tr = $selectedStudent->kurnasTranscript;
        @endphp

        <!-- KPI Cards Siswa -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-Rata Kumulatif (IPK)</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $cumulativeGpa > 0 ? number_format($cumulativeGpa, 2) : '-' }}</h3>
                    <span class="text-xs text-slate-400">/ 100</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Akumulasi seluruh semester aktif</p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tren Konsistensi Nilai</p>
                <div class="flex items-center gap-2 mt-1">
                    @if($isIncreasing)
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            ↗️ Meningkat
                        </span>
                    @else
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            ↔️ Dinamis
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Indikator penting SNBP PTN</p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Eligibilitas SNBP Siswa</p>
                <div class="mt-1">
                    @if($tr?->is_eligible_snbp)
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 inline-block">
                            ⭐ ELIGIBLE SNBP
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 inline-block">
                            Belum Ditetapkan
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-1">
                    @if($tr?->cohort_rank)
                        Peringkat {{ $tr->cohort_rank }} dari {{ $totalCohort }} siswa
                    @else
                        Kuota Angkatan: {{ $eligibleCount }} siswa (40%)
                    @endif
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cetak Dokumen Terkait</p>
                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('rapor_kurnas.print_buku_induk', $selectedStudent) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-xs flex items-center gap-1 transition">
                        <span>📜 Buku Induk</span>
                    </a>
                    <a href="{{ route('rapor_kurnas.print_academic', $selectedStudent) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center gap-1 transition">
                        <span>🖨️ Rapor</span>
                    </a>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Standar resmi cetak e-Rapor 2025</p>
            </div>
        </div>

        <!-- Rata-rata per Semester Grid -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Grafik Perkembangan Rata-Rata per Semester</h2>
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                @for($sem = 1; $sem <= 6; $sem++)
                    @php $avgVal = $semesterAverages[$sem] ?? null; @endphp
                    <div class="p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Semester {{ $sem }}</span>
                        <span class="text-xl font-black mt-1 block {{ $avgVal ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-300 dark:text-slate-600' }}">
                            {{ $avgVal ? number_format($avgVal, 1) : '-' }}
                        </span>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $avgVal ? min(100, $avgVal) : 0 }}%;"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Tabel Matriks Nilai Semester 1 s.d. 6 -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Matriks Riwayat Nilai Mata Pelajaran (Semester 1 s.d. 6)</h2>
                    <p class="text-xs text-slate-400">Rincian nilai setiap mata pelajaran dari awal masuk hingga jenjang kelulusan.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Mata Pelajaran</th>
                            <th class="p-3 text-center w-20">Sem 1</th>
                            <th class="p-3 text-center w-20">Sem 2</th>
                            <th class="p-3 text-center w-20">Sem 3</th>
                            <th class="p-3 text-center w-20">Sem 4</th>
                            <th class="p-3 text-center w-20">Sem 5</th>
                            <th class="p-3 text-center w-20">Sem 6</th>
                            <th class="p-3 text-center w-24">Rata-Rata</th>
                            <th class="p-3 text-center w-28">Tren</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($studentGrowthData as $idx => $row)
                            @php
                                $scores = $row['scores'];
                                $valid = array_filter($scores, fn($v) => !is_null($v));
                                $first = reset($valid);
                                $last = end($valid);
                                $subTrend = ($last && $first && $last >= $first);
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-white">
                                    {{ $row['subject']->name }}
                                </td>
                                @for($s = 1; $s <= 6; $s++)
                                    <td class="p-3 text-center font-bold {{ isset($scores[$s]) ? 'text-slate-800 dark:text-white' : 'text-slate-300 dark:text-slate-600' }}">
                                        {{ isset($scores[$s]) ? number_format($scores[$s], 0) : '-' }}
                                    </td>
                                @endfor
                                <td class="p-3 text-center font-black text-indigo-600 dark:text-indigo-400 bg-slate-50/50 dark:bg-slate-800/20">
                                    {{ number_format($row['average'], 1) }}
                                </td>
                                <td class="p-3 text-center">
                                    @if(count($valid) >= 2)
                                        @if($subTrend)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                                                ↗ Naik (+{{ number_format($last - $first, 1) }})
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                                                ↘ Turun ({{ number_format($last - $first, 1) }})
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-[10px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-slate-400">
                                    Belum ada catatan nilai rapor tersimpan untuk peserta didik ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Penetapan Eligibilitas SNBP (Admin / BK / Walas) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-3">
                <span>🎯</span>
                <span>Penetapan Kuota & Rekomendasi SNBP PTN</span>
            </h2>

            <form action="{{ route('rapor_kurnas.growth.update_snbp', $selectedStudent) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Eligibilitas SNBP</label>
                    <select name="is_eligible_snbp" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-bold">
                        <option value="1" {{ $tr?->is_eligible_snbp ? 'selected' : '' }}>Eligible (Rekomendasi Sekolah)</option>
                        <option value="0" {{ !($tr?->is_eligible_snbp) ? 'selected' : '' }}>Non-Eligible</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Peringkat Angkatan (Cohort Rank)</label>
                    <input type="number" name="cohort_rank" value="{{ $tr?->cohort_rank }}" min="1" max="{{ max(1, $totalCohort) }}" placeholder="Contoh: 1" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan BK / Walas</label>
                    <input type="text" name="notes" value="{{ $tr?->notes }}" placeholder="Rekomendasi prodi/kampus..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Status SNBP
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-12 text-center text-slate-400 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
            Pilih kelas dan siswa untuk melihat analisis perkembangan nilai dan SNBP tracker.
        </div>
    @endif
</div>
@endsection

