@extends('layouts.app')

@section('title', 'Input Nilai Akhir Rapor - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Top Action & Selector Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>📝</span>
                <span>Penilaian Mata Pelajaran & Capaian TP</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Input Nilai Akhir Rapor (0-100), tandai TP optimal & TP perlu bimbingan, lalu generate deskripsi capaian otomatis standar e-Rapor 2025.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('rapor_kurnas.scores') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Kelas Mapel:</label>
                <select name="course_class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                    @forelse($courseClasses as $cc)
                        <option value="{{ $cc->id }}" {{ $selectedCourseClassId == $cc->id ? 'selected' : '' }}>
                            {{ $cc->subject->name ?? 'Mapel' }} — {{ $cc->schoolClass->name ?? 'Kelas' }} ({{ $cc->teacher->name ?? 'Guru' }})
                        </option>
                    @empty
                        <option value="">-- Belum ada rombel mapel --</option>
                    @endforelse
                </select>
            </form>

            @if($isCurriculum)
                <form method="POST" action="{{ route('rapor_kurnas.sync_course_classes') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center gap-1.5 transition cursor-pointer" title="Sinkronisasi otomatis semua rombel mapel dari Kurikulum Sekolah">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Sinkron Mapel Kelas</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Info Kurikulum Merdeka: Asal Sumber Mapel per Kelas -->
    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 text-indigo-950 dark:text-indigo-200 text-xs flex items-start gap-3">
        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white shrink-0 flex items-center justify-center font-bold text-xs">i</div>
        <div class="space-y-0.5 leading-relaxed">
            <div class="font-bold">Asal Sumber Mata Pelajaran Per Kelas (Otomatisasi Kurikulum Merdeka):</div>
            <p class="text-indigo-900/80 dark:text-indigo-300/80">
                Mata pelajaran per kelas diambil secara otomatis dari <strong>Kurikulum Satuan Pendidikan (KSP)</strong> yang memetakan setiap Rombongan Belajar (Fase E: Kelas X-1 s/d X-7; Fase F: Kelas XI & XII) dengan Mata Pelajaran Kurikulum dan Guru Pengampu masing-masing. Guru mapel dapat langsung memilih rombel yang diampunya untuk menginput nilai dan tujuan pembelajaran (TP).
            </p>
        </div>
    </div>

    @if($selectedCourse)
        <!-- Course Info Card -->
        <div class="bg-gradient-to-r from-indigo-900/10 via-indigo-600/5 to-transparent border border-indigo-200/60 dark:border-indigo-800/60 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Mata Pelajaran:</span>
                    <strong class="text-indigo-900 dark:text-indigo-300 font-extrabold text-sm">{{ $selectedCourse->subject->name ?? '-' }}</strong>
                </div>
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Rombongan Belajar:</span>
                    <strong class="text-slate-800 dark:text-white font-bold text-sm">{{ $selectedCourse->schoolClass->name ?? '-' }} (Tingkat {{ $selectedCourse->schoolClass->grade ?? '-' }})</strong>
                </div>
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Guru Pengampu:</span>
                    <strong class="text-slate-800 dark:text-white font-bold text-sm">{{ $selectedCourse->teacher->name ?? '-' }}</strong>
                </div>
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Jumlah Siswa:</span>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-xs">{{ count($students) }} Siswa</span>
                </div>
            </div>

            <!-- Import / Export Actions -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('rapor_kurnas.scores.template', $selectedCourse->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition inline-flex items-center gap-1.5">
                    <span>📥</span>
                    <span>Format CSV</span>
                </a>

                <button type="button" onclick="document.getElementById('modalImportCsv').classList.remove('hidden')" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <span>📤</span>
                    <span>Import CSV</span>
                </button>

                <button type="button" onclick="autoGenerateAllDescriptions()" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <span>⚡</span>
                    <span>Generate Otomatis Semua</span>
                </button>
            </div>
        </div>

        @if($learningGoals->isEmpty())
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-300 text-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚠️</span>
                    <span>Belum ada Tujuan Pembelajaran (TP) untuk mapel <strong>{{ $selectedCourse->subject->name }}</strong> di semester ini. Anda tetap dapat menginput nilai angka, namun disarankan menambahkan TP agar deskripsi rapor terisi otomatis.</span>
                </div>
                <a href="{{ route('rapor_kurnas.learning_goals', ['subject_id' => $selectedCourse->subject_id]) }}" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold whitespace-nowrap">
                    + Tambah TP Sekarang
                </a>
            </div>
        @endif

        <!-- Form Input Nilai Masal -->
        <form action="{{ route('rapor_kurnas.scores.store') }}" method="POST">
            @csrf
            <input type="hidden" name="course_class_id" value="{{ $selectedCourse->id }}">

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="p-3 w-12 text-center">No</th>
                                <th class="p-3 w-48">Nama Siswa / NISN</th>
                                <th class="p-3 w-28 text-center">Nilai Akhir (0-100)</th>
                                <th class="p-3 w-56">
                                    <span class="text-emerald-700 dark:text-emerald-400">✨ Capaian Tertinggi (Optimal)</span>
                                </th>
                                <th class="p-3 w-56">
                                    <span class="text-rose-700 dark:text-rose-400">⚠️ Perlu Peningkatan</span>
                                </th>
                                <th class="p-3 min-w-[280px]">Deskripsi Capaian Rapor (Otomatis / Edit Manual)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $index => $student)
                                @php
                                    $scoreData = $existingScores->get($student->id);
                                    $optimalIds = $scoreData?->optimal_tp_ids ?? [];
                                    $improvementIds = $scoreData?->improvement_tp_ids ?? [];
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition student-row" data-student-id="{{ $student->id }}">
                                    <td class="p-3 text-center text-slate-400 font-mono">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $student->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $student->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="100" 
                                               name="scores[{{ $index }}][score]" 
                                               value="{{ $scoreData?->score }}" 
                                               placeholder="0-100" 
                                               class="w-20 text-center font-black rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-2 py-1.5 focus:border-indigo-500 focus:bg-white">
                                    </td>
                                    <td class="p-3">
                                        <div class="space-y-1 max-h-24 overflow-y-auto scrollbar-thin">
                                            @foreach($learningGoals as $goal)
                                                <label class="flex items-center gap-1.5 text-[11px] text-slate-700 dark:text-slate-300 cursor-pointer hover:text-emerald-600">
                                                    <input type="checkbox" 
                                                           name="scores[{{ $index }}][optimal_tp][]" 
                                                           value="{{ $goal->id }}" 
                                                           data-desc="{{ $goal->description }}" 
                                                           class="rounded text-emerald-600 tp-optimal-check" 
                                                           onchange="updateRowDescription({{ $index }})"
                                                           {{ in_array($goal->id, $optimalIds) ? 'checked' : '' }}>
                                                    <span class="font-semibold">{{ $goal->code }}</span>
                                                    <span class="truncate text-[10px] text-slate-400" title="{{ $goal->description }}">{{ Str::limit($goal->description, 28) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="space-y-1 max-h-24 overflow-y-auto scrollbar-thin">
                                            @foreach($learningGoals as $goal)
                                                <label class="flex items-center gap-1.5 text-[11px] text-slate-700 dark:text-slate-300 cursor-pointer hover:text-rose-600">
                                                    <input type="checkbox" 
                                                           name="scores[{{ $index }}][improvement_tp][]" 
                                                           value="{{ $goal->id }}" 
                                                           data-desc="{{ $goal->description }}" 
                                                           class="rounded text-rose-600 tp-improvement-check" 
                                                           onchange="updateRowDescription({{ $index }})"
                                                           {{ in_array($goal->id, $improvementIds) ? 'checked' : '' }}>
                                                    <span class="font-semibold">{{ $goal->code }}</span>
                                                    <span class="truncate text-[10px] text-slate-400" title="{{ $goal->description }}">{{ Str::limit($goal->description, 28) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <textarea name="scores[{{ $index }}][full_description]" 
                                                  id="desc_{{ $index }}" 
                                                  rows="2" 
                                                  placeholder="Deskripsi capaian rapor akan otomatis terisi saat TP dicentang..." 
                                                  class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-[11px] p-2 leading-relaxed font-sans focus:border-indigo-500">{{ $scoreData?->full_description }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-500">
                        Pastikan memeriksa seluruh nilai dan deskripsi sebelum menekan tombol simpan.
                    </p>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition cursor-pointer flex items-center gap-2">
                        <span>💾</span>
                        <span>Simpan Seluruh Nilai Rapor</span>
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="p-12 text-center bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl text-slate-400">
            <span class="text-4xl block mb-3">📚</span>
            <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Belum ada kelas mapel yang dipilih</h3>
            <p class="text-xs text-slate-400 mt-1">Silakan pilih kelas mata pelajaran pada menu di atas untuk mulai menginput nilai.</p>
        </div>
    @endif
</div>

<!-- Modal Import CSV -->
@if($selectedCourse)
<div id="modalImportCsv" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl text-slate-800 dark:text-white space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold flex items-center gap-2">
                <span>📤</span>
                <span>Import Nilai dari CSV</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalImportCsv').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form action="{{ route('rapor_kurnas.scores.import', $selectedCourse->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <p class="text-slate-500 dark:text-slate-400 mb-2">
                    Unggah file CSV yang telah diisi sesuai format template e-Rapor 2025.
                </p>
                <input type="file" name="csv_file" accept=".csv" required class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modalImportCsv').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs text-white shadow-md">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function updateRowDescription(rowIndex) {
    const row = document.querySelectorAll('.student-row')[rowIndex];
    if (!row) return;

    const optimalChecks = row.querySelectorAll('.tp-optimal-check:checked');
    const improveChecks = row.querySelectorAll('.tp-improvement-check:checked');

    const optimalDescs = Array.from(optimalChecks).map(c => c.getAttribute('data-desc')).filter(Boolean);
    const improveDescs = Array.from(improveChecks).map(c => c.getAttribute('data-desc')).filter(Boolean);

    let parts = [];
    if (optimalDescs.length > 0) {
        parts.push(`Menunjukkan penguasaan yang sangat baik dalam ${optimalDescs.join(', ')}.`);
    }
    if (improveDescs.length > 0) {
        parts.push(`Perlu peningkatan dan pendampingan dalam ${improveDescs.join(', ')}.`);
    }

    const descField = document.getElementById(`desc_${rowIndex}`);
    if (descField) {
        descField.value = parts.join(' ');
    }
}

function autoGenerateAllDescriptions() {
    const rows = document.querySelectorAll('.student-row');
    rows.forEach((row, idx) => {
        updateRowDescription(idx);
    });
    alert('Deskripsi capaian kompetensi seluruh siswa berhasil diperbarui sesuai pilihan TP!');
}
</script>
@endsection

