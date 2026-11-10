@extends('layouts.app')

@section('title', 'Kelengkapan Rapor Wali Kelas - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Top Header & Class Selector -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>📋</span>
                <span>Kelengkapan Rapor Wali Kelas</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola rekapitulasi ketidakhadiran (sinkron otomatis dari presensi harian), catatan wali kelas, dan keputusan kenaikan kelas / kelulusan.
            </p>
        </div>

        <form method="GET" action="{{ route('rapor_kurnas.homeroom') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Kelas Perwalian:</label>
            <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }} (Wali: {{ $cls->homeroomTeacher->name ?? 'Belum Ditentukan' }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedClass)
        <!-- Action bar -->
        <div class="bg-gradient-to-r from-blue-900/10 via-blue-600/5 to-transparent border border-blue-200/60 dark:border-blue-800/60 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Kelas Perwalian:</span>
                    <strong class="text-blue-950 dark:text-blue-300 font-extrabold text-sm">{{ $selectedClass->name }} (Tingkat {{ $selectedClass->grade }})</strong>
                </div>
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Wali Kelas:</span>
                    <strong class="text-slate-800 dark:text-white font-bold text-sm">{{ $selectedClass->homeroomTeacher->name ?? '-' }}</strong>
                </div>
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Jumlah Siswa:</span>
                    <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-black text-xs">{{ count($students) }} Siswa</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <!-- Tombol Tarik Presensi Otomatis -->
                <form action="{{ route('rapor_kurnas.homeroom.sync_attendance', $selectedClass->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-bold text-xs transition inline-flex items-center gap-2 cursor-pointer shadow-xs">
                        <span>🔄</span>
                        <span>Tarik Presensi Harian Otomatis</span>
                    </button>
                </form>

                <a href="{{ route('rapor_kurnas.print_leger', $selectedClass->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs transition inline-flex items-center gap-2 shadow-xs">
                    <span>📑</span>
                    <span>Cetak Leger Nilai</span>
                </a>
            </div>
        </div>

        <!-- Form Kelengkapan Walas -->
        <form action="{{ route('rapor_kurnas.homeroom.store', $selectedClass->id) }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="p-3 w-12 text-center">No</th>
                                <th class="p-3 w-52">Nama Siswa / NISN</th>
                                <th class="p-3 w-44 text-center">
                                    <span>Ketidakhadiran</span>
                                    <div class="grid grid-cols-3 gap-1 text-[9px] text-slate-400 mt-1 font-normal">
                                        <span>Sakit</span>
                                        <span>Izin</span>
                                        <span>Alpa</span>
                                    </div>
                                </th>
                                <th class="p-3 min-w-[280px]">Catatan Wali Kelas</th>
                                <th class="p-3 w-48 text-center">Kenaikan / Kelulusan</th>
                                <th class="p-3 w-24 text-center">Cetak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $index => $student)
                                @php
                                    $record = $existingRecords->get($student->id);
                                    $grade = $selectedClass->grade;
                                    $defaultPromotion = ($grade === 'XII') ? 'LULUS' : 'NAIK';
                                    $currentPromo = $record?->status_promotion ?? $defaultPromotion;
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-3 text-center text-slate-400 font-mono">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="records[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $student->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $student->nisn ?? '-' }}</div>
                                    </td>
                                    <!-- Ketidakhadiran S/I/A -->
                                    <td class="p-3">
                                        <div class="grid grid-cols-3 gap-1.5">
                                            <input type="number" min="0" name="records[{{ $index }}][sick]" value="{{ $record?->sick ?? 0 }}" title="Sakit" class="w-full text-center rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-1 font-bold">
                                            <input type="number" min="0" name="records[{{ $index }}][permission]" value="{{ $record?->permission ?? 0 }}" title="Izin" class="w-full text-center rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-1 font-bold">
                                            <input type="number" min="0" name="records[{{ $index }}][absent]" value="{{ $record?->absent ?? 0 }}" title="Tanpa Keterangan" class="w-full text-center rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-1 font-bold text-rose-600">
                                        </div>
                                    </td>
                                    <!-- Catatan Wali Kelas -->
                                    <td class="p-3">
                                        <textarea name="records[{{ $index }}][note]" rows="2" placeholder="Catatan motivasi atau evaluasi wali kelas..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-[11px] p-2 leading-relaxed focus:border-blue-500">{{ $record?->note ?? 'Pertahankan semangat belajarmu, tingkatkan kedisiplinan dan keaktifan dalam kegiatan akademik maupun sosial.' }}</textarea>
                                    </td>
                                    <!-- Kenaikan / Kelulusan -->
                                    <td class="p-3 text-center">
                                        @if($grade === 'XII')
                                            <select name="records[{{ $index }}][status_promotion]" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1.5 font-bold text-emerald-700 dark:text-emerald-400">
                                                <option value="LULUS" {{ $currentPromo === 'LULUS' ? 'selected' : '' }}>🎓 LULUS</option>
                                                <option value="TIDAK_LULUS" {{ $currentPromo === 'TIDAK_LULUS' ? 'selected' : '' }}>❌ TIDAK LULUS</option>
                                            </select>
                                        @elseif($grade === 'XI')
                                            <select name="records[{{ $index }}][status_promotion]" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1.5 font-bold text-indigo-700 dark:text-indigo-400">
                                                <option value="NAIK" {{ $currentPromo === 'NAIK' ? 'selected' : '' }}>⬆️ Naik ke Kelas XII</option>
                                                <option value="TINGGAL" {{ $currentPromo === 'TINGGAL' ? 'selected' : '' }}>⏹️ Tinggal di Kelas XI</option>
                                            </select>
                                            <input type="hidden" name="records[{{ $index }}][promotion_target_grade]" value="XII">
                                        @else
                                            <select name="records[{{ $index }}][status_promotion]" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1.5 font-bold text-indigo-700 dark:text-indigo-400">
                                                <option value="NAIK" {{ $currentPromo === 'NAIK' ? 'selected' : '' }}>⬆️ Naik ke Kelas XI</option>
                                                <option value="TINGGAL" {{ $currentPromo === 'TINGGAL' ? 'selected' : '' }}>⏹️ Tinggal di Kelas X</option>
                                            </select>
                                            <input type="hidden" name="records[{{ $index }}][promotion_target_grade]" value="XI">
                                        @endif
                                    </td>
                                    <!-- Aksi Cetak Per Siswa -->
                                    <td class="p-3 text-center">
                                        <a href="{{ route('rapor_kurnas.print_academic', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] inline-flex items-center gap-1 transition">
                                            <span>🖨️</span>
                                            <span>Rapor</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-500">
                        Data kehadiran dan catatan akan langsung muncul di lembar cetak rapor resmi.
                    </p>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
                        <span>💾</span>
                        <span>Simpan Kelengkapan Rapor</span>
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

