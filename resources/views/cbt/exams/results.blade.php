@extends('layouts.app')

@section('title', 'Rekap Hasil & Koreksi Nilai - ' . $exam->title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('cbt.exams.show', $exam->id) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Detail Ujian
                </a>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                Rekap Hasil & Koreksi Nilai: {{ $exam->title }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Mata Pelajaran: <strong>{{ $exam->subject?->name ?? 'Umum' }}</strong> • KKM: <span class="font-bold text-indigo-600">{{ $exam->kkm }}</span> • Total Soal: {{ $exam->questions->count() }} Butir
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Rekap
            </button>
        </div>
    </div>
        <!-- STATISTIK KELULUSAN -->
        @php
            $submittedSessions = $sessions->where('status', 'submitted');
            $submittedCount = $submittedSessions->count();
            $percentagePass = $submittedCount > 0 ? round(($passCount / $submittedCount) * 100, 1) : 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rata-rata Nilai</div>
                <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($avgGrade, 1) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Dari {{ $submittedCount }} siswa selesai</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Nilai Tertinggi</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($highest, 1) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Skor maksimal ujian</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">Nilai Terendah</div>
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($lowest, 1) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Skor minimal ujian</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tuntas KKM (&ge; {{ $exam->kkm }})</div>
                <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $passCount }} <span class="text-xs font-medium text-slate-400">/ {{ $submittedCount }} ({{ $percentagePass }}%)</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Tingkat ketuntasan belajar</div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        <!-- TABEL REKAP NILAI -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden" x-data="{ search: '', filterClass: '', activeEssaySession: null }">
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="font-bold text-slate-800 dark:text-white text-base">Daftar Hasil Nilai Siswa</h2>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="text" x-model="search" placeholder="Cari nama / NIS..." class="text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 py-2 px-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                    <select x-model="filterClass" class="text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 py-2 px-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Rombel</option>
                        @foreach($exam->classes as $cls)
                            <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">Peringkat</th>
                            <th class="py-3 px-4">Nama Siswa & Rombel</th>
                            <th class="py-3 px-4 text-center">Jawaban Terisi</th>
                            <th class="py-3 px-4 text-center">Soal Objektif</th>
                            <th class="py-3 px-4 text-center">Nilai Akhir</th>
                            <th class="py-3 px-4 text-center">Status Ketuntasan</th>
                            <th class="py-3 px-4 text-right">Koreksi Esai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @php $rank = 1; @endphp
                        @forelse($sessions as $session)
                            @php
                                $student = $session->student;
                                $searchKey = strtolower(($student?->name ?? '').' '.($student?->nis ?? ''));
                                $className = $student?->schoolClass?->name ?? '';
                                $essayAnswers = $session->answers->filter(fn($a) => in_array($a->question?->question_type, ['essay', 'short_answer']));
                            @endphp
                            <tr x-show="(search === '' || '{{ $searchKey }}'.includes(search.toLowerCase())) && (filterClass === '' || '{{ $className }}' === filterClass)" class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500">
                                    @if($session->status === 'submitted')
                                        {{ $rank++ }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $student?->name ?? 'Siswa #'.$session->student_id }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span>NIS: {{ $student?->nis ?? '-' }}</span>
                                        <span>•</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-semibold">{{ $className ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center text-[11px]">
                                    <span class="font-bold text-slate-700 dark:text-slate-200">{{ $session->answers->whereNotNull('answer')->count() }}</span>
                                    <span class="text-slate-400">/ {{ $exam->questions->count() }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-[11px]">
                                    <span class="text-emerald-600 font-bold">{{ $session->total_correct }} Benar</span>
                                    <span class="text-slate-400">•</span>
                                    <span class="text-rose-500 font-bold">{{ $session->total_incorrect }} Salah</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($session->status === 'submitted')
                                        <div class="text-base font-black {{ $session->isPassed() ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ number_format($session->final_grade, 1) }}
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum Selesai</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($session->status === 'submitted')
                                        @if($session->isPassed())
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                                                ✓ Tuntas
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                                                Remedial
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500">
                                            {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($essayAnswers->count() > 0 && $session->status === 'submitted')
                                        <button type="button" @click="activeEssaySession = {{ $session->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-bold text-xs transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Koreksi Esai ({{ $essayAnswers->count() }})
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- MODAL KOREKSI ESAI PER SISWA -->
                            @if($essayAnswers->count() > 0 && $session->status === 'submitted')
                                <div x-show="activeEssaySession === {{ $session->id }}" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                                    <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 text-left space-y-5" @click.away="activeEssaySession = null">
                                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                                            <div>
                                                <h3 class="text-base font-bold text-slate-800 dark:text-white">Koreksi Jawaban Esai: {{ $student?->name }}</h3>
                                                <p class="text-xs text-slate-500">Kelas: {{ $className }} • Nilai Saat Ini: {{ number_format($session->final_grade, 1) }}</p>
                                            </div>
                                            <button type="button" @click="activeEssaySession = null" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                                        </div>

                                        <form method="POST" action="{{ route('cbt.exams.grade_essay', [$exam->id, $session->id]) }}" class="space-y-4">
                                            @csrf
                                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                                                @foreach($essayAnswers as $ans)
                                                    @php $q = $ans->question; @endphp
                                                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 space-y-2.5">
                                                        <div class="flex items-center justify-between text-xs">
                                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Soal No. {{ $q->question_number }} (Bobot Maks: {{ $q->score_weight }})</span>
                                                            <span class="text-[11px] px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 font-medium">{{ $q->question_type === 'short_answer' ? 'Isian Singkat' : 'Uraian / Esai' }}</span>
                                                        </div>
                                                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">{!! nl2br(e($q->question_text)) !!}</div>
                                                        
                                                        <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jawaban Siswa:</div>
                                                            <div class="text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ is_array($ans->answer) ? json_encode($ans->answer) : ($ans->answer ?? '(Tidak dijawab)') }}</div>
                                                        </div>

                                                        @if(!empty($q->explanation) || !empty($q->correct_answer))
                                                            <div class="bg-amber-50 dark:bg-amber-950/30 p-2.5 rounded-xl border border-amber-200 dark:border-amber-900 text-[11px] text-amber-800 dark:text-amber-300">
                                                                <span class="font-bold">Pedoman Penskoran:</span> {{ $q->explanation ?? $q->correct_answer }}
                                                            </div>
                                                        @endif

                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Nilai Guru (0 - {{ $q->score_weight }}):</label>
                                                                <input type="number" step="0.1" min="0" max="{{ $q->score_weight }}" name="essay_scores[{{ $ans->id }}]" value="{{ $ans->score_earned ?? 0 }}" required class="w-full text-xs rounded-xl bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 py-1.5 px-3 font-bold text-slate-800 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Catatan Koreksi (Opsional):</label>
                                                                <input type="text" name="essay_feedbacks[{{ $ans->id }}]" value="{{ $ans->teacher_feedback }}" placeholder="Catatan untuk siswa..." class="w-full text-xs rounded-xl bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 py-1.5 px-3 text-slate-800 dark:text-white">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                                                <button type="button" @click="activeEssaySession = null" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">Simpan Koreksi Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada data pengerjaan ujian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
