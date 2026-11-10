@extends('layouts.app')

@section('title', $quiz->title . ' - LMS')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Asesmen {{ $courseClass->subject->name }}</span>
        </a>
        @php
            $typeLabel = match($quiz->quiz_type) {
                'formatif' => 'Asesmen Formatif',
                'sumatif_materi' => 'Asesmen Sumatif (Lingkup Materi)',
                'sts' => 'Sumatif Tengah Semester (STS)',
                'sas' => 'Sumatif Akhir Semester (SAS)',
                'saj' => 'Sumatif Akhir Jenjang (SAJ)',
                'ulangan_harian' => 'Asesmen Sumatif (Lingkup Materi)',
                'uts' => 'Sumatif Tengah Semester (STS)',
                'uas' => 'Sumatif Akhir Semester (SAS)',
                'kuis' => 'Asesmen Formatif',
                default => ucwords(str_replace('_', ' ', $quiz->quiz_type)),
            };
        @endphp
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 uppercase">
                {{ $typeLabel }}
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                KKTP: {{ $quiz->passing_score }}
            </span>
        </div>
    </div>

    @if($canManage)
        @include('lms.security.unblock_panel', [
            'courseClass' => $courseClass,
            'scopedType' => 'quiz',
            'scopedId' => $quiz->id,
        ])
    @endif

    <!-- Hero Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 uppercase">
                    {{ $courseClass->schoolClass->name }} • {{ $courseClass->subject->name }}
                </span>
                <span class="text-xs text-slate-400">⏱️ Durasi: {{ $quiz->duration_minutes }} Menit</span>
                <span class="text-xs text-slate-400">• Standar Permendikdasmen 13/2025</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $quiz->title }}</h1>
            @if($quiz->instructions)
                <div class="text-xs text-slate-500 leading-relaxed whitespace-pre-line">{{ $quiz->instructions }}</div>
            @endif

            @if($quiz->topic)
                @php
                    $rawTopics = preg_split('/[\r\n,;]+/', $quiz->topic);
                    $topics = array_filter(array_map('trim', $rawTopics));
                @endphp
                @if(count($topics) > 0)
                    <div class="flex flex-wrap items-center gap-1.5 pt-1.5">
                        <span class="text-[11px] font-bold text-slate-400">Topik / Lingkup Materi:</span>
                        @foreach($topics as $tp)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                <span>{{ $tp }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        @if($student)
            <div class="shrink-0">
                <a href="{{ route('lms.quizzes.take', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-sm rounded-2xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    <span>{{ $latestAttempt ? 'Kerjakan Ulang / Lanjut Ujian' : 'Mulai Pengerjaan Ujian' }}</span>
                </a>
            </div>
        @elseif($canManage)
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="openEditQuizModal()" class="px-3.5 py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700 font-bold text-xs flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Asesmen</span>
                </button>
                <a href="{{ route('lms.quizzes.kisi_kisi', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 transition" title="Buka Dokumen Kisi-Kisi Soal Siap Cetak">
                    <span>📋 Kisi-Kisi Soal</span>
                </a>
                <a href="{{ route('lms.quizzes.kartu_soal', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 transition" title="Buka Dokumen Kartu Soal Per Butir Siap Cetak">
                    <span>🗂️ Kartu Soal</span>
                </a>
                <button type="button" onclick="document.getElementById('modal-ai-generate-questions').classList.remove('hidden')" class="px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                    <span>✨ Generate Soal HOTS AI</span>
                </button>
                <button type="button" onclick="document.getElementById('modal-add-question-manual').classList.remove('hidden')" class="px-3.5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Soal Manual</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Student Previous Attempts -->
    @if($student && $latestAttempt)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Hasil Pengerjaan Terakhir</h2>
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-500">Waktu Selesai: {{ $latestAttempt->completed_at ? $latestAttempt->completed_at->translatedFormat('d F Y, H:i') : 'Sedang Berlangsung' }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Status Kelulusan: <strong class="{{ $latestAttempt->is_passed ? 'text-emerald-500' : 'text-rose-500' }}">{{ $latestAttempt->is_passed ? 'TUNTAS (Mencapai KKTP)' : 'REMEDIAL (Belum Mencapai KKTP)' }}</strong></div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Skor Akhir</div>
                    <div class="text-3xl font-black {{ $latestAttempt->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">{{ $latestAttempt->score }}</div>
                </div>
            </div>

            @if(isset($nextQuiz) && $nextQuiz && $latestAttempt->completed_at)
                <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-800/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">🔓</span>
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 dark:text-white">Asesmen Berikutnya Telah Terbuka:</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $nextQuiz->title }}</span>
                        </div>
                    </div>
                    <a href="{{ route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $nextQuiz->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shrink-0">
                        <span>Buka Asesmen Berikutnya &rarr;</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Question Bank & Teacher Management -->
    @if($canManage)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Butir Soal CBT Terdaftar</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-600">{{ $quiz->questions->count() }} Butir Soal</span>
                    </h2>
                    <p class="text-xs text-slate-500">Standar Permendikdasmen No. 13 Tahun 2025 & AKM (PG, PG Kompleks, B/S, Menjodohkan, Isian Singkat)</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('lms.quizzes.kisi_kisi', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-1">
                        <span>📋 Dokumen Kisi-Kisi</span>
                    </a>
                    <a href="{{ route('lms.quizzes.kartu_soal', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-1">
                        <span>🗂️ Dokumen Kartu Soal</span>
                    </a>
                    <button type="button" onclick="document.getElementById('modal-ai-generate-questions').classList.remove('hidden')" class="px-3 py-1.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-xl text-xs font-bold hover:bg-purple-100 transition">
                        ✨ Tambah Soal AI
                    </button>
                    <button type="button" onclick="document.getElementById('modal-add-question-manual').classList.remove('hidden')" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs font-bold hover:bg-indigo-100 transition">
                        + Soal Manual
                    </button>
                </div>
            </div>

            @if($quiz->questions->isEmpty())
                <div class="py-12 text-center bg-slate-50/50 dark:bg-slate-800/30 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold mb-3">
                        📝
                    </div>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada butir soal pada asesmen ini.</p>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Gunakan fitur <strong>Generate Soal HOTS AI</strong> untuk membuat butir soal otomatis berstandar Permendikdasmen No. 13 Tahun 2025 atau input manual.</p>
                    <div class="flex items-center justify-center gap-3 mt-4">
                        <button type="button" onclick="document.getElementById('modal-ai-generate-questions').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition">
                            ✨ Generate Soal AI Sekarang
                        </button>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($quiz->questions as $idx => $q)
                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shadow-xs">
                                        {{ $idx + 1 }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 uppercase">
                                        {{ $q->type_label }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">
                                        Bobot: {{ $q->score_weight }} Poin
                                    </span>
                                    @if($q->level_kognitif)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                                            {{ $q->level_kognitif }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-black bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                        Kunci:
                                        @if($q->question_type === 'complex_multiple_choice')
                                            @php
                                                $cList = json_decode($q->correct_answer, true);
                                                if (!is_array($cList)) $cList = explode(',', (string)$q->correct_answer);
                                            @endphp
                                            {{ implode(', ', (array)$cList) }}
                                        @elseif($q->question_type === 'matching')
                                            Pasangan Benar
                                        @else
                                            {{ $q->correct_answer }}
                                        @endif
                                    </span>

                                    <!-- Tombol Edit Soal & Kunci Jawaban -->
                                    <button type="button" onclick="openEditQuestionModal({{ json_encode([
                                        'id' => $q->id,
                                        'order_number' => $q->order_number,
                                        'question_type' => $q->question_type,
                                        'stimulus' => $q->stimulus,
                                        'question_text' => $q->question_text,
                                        'options' => $q->options,
                                        'correct_answer' => $q->correct_answer,
                                        'score_weight' => $q->score_weight,
                                        'level_kognitif' => $q->level_kognitif,
                                        'explanation' => $q->pembahasan,
                                        'update_url' => route('lms.quizzes.questions.update', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id, 'question' => $q->id]),
                                    ]) }})" class="px-2 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 font-bold text-xs flex items-center gap-1 transition" title="Edit Butir Soal & Kunci Jawaban">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit Soal</span>
                                    </button>

                                    <form action="{{ route('lms.quizzes.questions.destroy', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id, 'question' => $q->id]) }}" method="POST" onsubmit="return confirm('Hapus butir soal nomor {{ $idx + 1 }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 p-1.5 rounded-lg transition" title="Hapus Soal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($q->stimulus)
                                <div class="p-3 bg-purple-50/60 dark:bg-purple-950/20 border-l-4 border-purple-500 rounded-r-xl text-xs text-purple-900 dark:text-purple-200 italic leading-relaxed">
                                    <span class="font-bold text-[10px] uppercase not-italic block text-purple-700 dark:text-purple-300 mb-0.5">📌 Stimulus Kontekstual:</span>
                                    {!! \App\Models\LmsQuizQuestion::renderRichText($q->stimulus) !!}
                                </div>
                            @endif

                            <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white leading-relaxed whitespace-pre-line">
                                {!! $q->formatted_question_text !!}
                            </div>

                            <!-- OPSI / MODEL PENJAWABAN BERDASARKAN TIPE -->
                            @if($q->question_type === 'multiple_choice' && $q->options && is_array($q->options))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                                    @foreach($q->options as $optKey => $optVal)
                                        <div class="p-2.5 rounded-xl {{ $optKey === $q->correct_answer ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-200 font-bold' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }} flex items-start gap-2">
                                            <span class="font-black px-1.5 py-0.5 rounded {{ $optKey === $q->correct_answer ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $optKey }}</span>
                                            <span class="leading-relaxed">{!! $optVal !!}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'complex_multiple_choice' && $q->options && is_array($q->options))
                                @php
                                    $correctList = json_decode($q->correct_answer, true);
                                    if(!is_array($correctList)) $correctList = array_map('trim', explode(',', (string)$q->correct_answer));
                                    $correctList = array_map('strtoupper', (array)$correctList);
                                @endphp
                                <div class="space-y-1.5 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                                    <span class="text-[11px] text-slate-400 font-semibold">Pernyataan Pilihan Ganda Kompleks:</span>
                                    @foreach($q->options as $optKey => $optVal)
                                        @php $isCorrect = in_array(strtoupper($optKey), $correctList); @endphp
                                        <div class="p-2 rounded-xl {{ $isCorrect ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-200 font-bold' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }} flex items-start gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[11px] font-black {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $optKey }}</span>
                                            <span class="leading-relaxed">{!! $optVal !!}</span>
                                            @if($isCorrect)
                                                <span class="ml-auto text-emerald-600 text-[10px] font-extrabold uppercase">✓ Benar</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'true_false')
                                <div class="flex items-center gap-3 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                                    <div class="px-3 py-1.5 rounded-xl border {{ strtolower($q->correct_answer) === 'benar' || strtoupper($q->correct_answer) === 'A' ? 'bg-emerald-500/15 border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}">
                                        [A] Pernyataan Benar
                                    </div>
                                    <div class="px-3 py-1.5 rounded-xl border {{ strtolower($q->correct_answer) === 'salah' || strtoupper($q->correct_answer) === 'B' ? 'bg-emerald-500/15 border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}">
                                        [B] Pernyataan Salah
                                    </div>
                                </div>
                            @elseif($q->question_type === 'matching')
                                @php
                                    $premises = $q->options['premises'] ?? [];
                                    $choices = $q->options['choices'] ?? [];
                                    $correctPairs = json_decode($q->correct_answer, true) ?: [];
                                @endphp
                                <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="space-y-1.5">
                                            <span class="text-[11px] font-bold text-slate-500">Premis Pertanyaan:</span>
                                            @foreach($premises as $pk => $pv)
                                                <div class="p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-start gap-2">
                                                    <span class="font-bold text-indigo-600">{{ $pk }}.</span>
                                                    <span>{!! \App\Models\LmsQuizQuestion::renderRichText($pv) !!}</span>
                                                    @if(isset($correctPairs[$pk]))
                                                        <span class="ml-auto text-[10px] bg-indigo-50 dark:bg-indigo-950 text-indigo-600 px-1.5 py-0.5 rounded font-bold">→ {{ $correctPairs[$pk] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="space-y-1.5">
                                            <span class="text-[11px] font-bold text-slate-500">Pilihan Pasangan Jawaban:</span>
                                            @foreach($choices as $ck => $cv)
                                                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-start gap-2">
                                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $ck }}.</span>
                                                    <span>{!! \App\Models\LmsQuizQuestion::renderRichText($cv) !!}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @elseif($q->question_type === 'short_answer')
                                <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs space-y-1">
                                    <span class="text-[11px] text-slate-400 font-semibold">Kunci Jawaban Uraian / Isian Singkat:</span>
                                    <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 font-bold">
                                        {!! \App\Models\LmsQuizQuestion::renderRichText($q->correct_answer) !!}
                                    </div>
                                </div>
                            @elseif($q->question_type === 'essay' || $q->question_type === 'uraian')
                                <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs space-y-1.5">
                                    <span class="text-[11px] text-purple-600 dark:text-purple-400 font-bold flex items-center gap-1">
                                        <span>📝</span> Pedoman Penskoran & Kunci Jawaban Uraian:
                                    </span>
                                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800/60 text-purple-900 dark:text-purple-200 font-medium whitespace-pre-line leading-relaxed">
                                        {!! \App\Models\LmsQuizQuestion::renderRichText($q->correct_answer) !!}
                                    </div>
                                </div>
                            @endif

                            @if($q->pembahasan)
                                <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                    <div class="font-bold flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400">
                                        <span>💡 Pembahasan Ilmiah & Analisis HOTS:</span>
                                    </div>
                                    <div class="leading-relaxed whitespace-pre-line text-[11px]">
                                        {!! \App\Models\LmsQuizQuestion::renderRichText($q->pembahasan) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Rekapitulasi Nilai Pengerjaan Siswa Kelas -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Rekapitulasi Nilai Siswa ({{ $quiz->attempts->count() }} Pengerjaan)</h3>
                        <p class="text-xs text-slate-500">Hasil pengerjaan asesmen CBT seluruh siswa di kelas</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('lms.quizzes.export_excel', [$courseClass->id, $quiz->id]) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Excel</span>
                        </a>
                        <a href="{{ route('lms.quizzes.print_pdf', [$courseClass->id, $quiz->id]) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak / PDF (KOP)</span>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                            <tr>
                                <th class="p-3">Nama Siswa</th>
                                <th class="p-3">Waktu Mulai</th>
                                <th class="p-3">Waktu Selesai</th>
                                <th class="p-3 text-center">Skor Akhir</th>
                                <th class="p-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($quiz->attempts as $att)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $att->student->name }}</td>
                                    <td class="p-3 text-slate-500">{{ $att->started_at ? $att->started_at->format('H:i:s') : '-' }}</td>
                                    <td class="p-3 text-slate-500">{{ $att->completed_at ? $att->completed_at->format('H:i:s') : 'Mengerjakan' }}</td>
                                    <td class="p-3 text-center font-black text-sm {{ $att->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">{{ $att->score }}</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $att->is_passed ? 'bg-emerald-500/15 text-emerald-600' : 'bg-rose-500/15 text-rose-600' }}">
                                            {{ $att->is_passed ? 'Tuntas' : 'Remedial' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-xs text-slate-400">Belum ada siswa yang mengerjakan asesmen ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 1: GENERATE SOAL AI (PERMENDIKDASMEN NO. 13 TAHUN 2025) -->
        <!-- ======================================================== -->
        <div id="modal-ai-generate-questions" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-purple-600 text-white flex items-center justify-center text-xs font-black">✨</span>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Generate Soal AI (Permendikdasmen 13/2025)</h3>
                    </div>
                    <button onclick="document.getElementById('modal-ai-generate-questions').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="mt-3 p-3.5 rounded-2xl bg-purple-50/60 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/60 text-xs text-purple-900 dark:text-purple-200 space-y-1">
                    <div class="font-black flex items-center gap-1.5">
                        <span>📜 Standar Asesmen Kurikulum Nasional:</span>
                    </div>
                    <p class="leading-relaxed text-[11px] text-purple-800 dark:text-purple-300">
                        Soal dirumuskan berstandar Asesmen Nasional (AKM/HOTS Level C3-C5), mencakup stimulus fenomena kontekstual, opsi pilihan ganda A s/d E dengan pengecoh logis, serta kunci jawaban dan pembahasan ilmiah.
                    </p>
                </div>

                <form action="{{ route('lms.quizzes.questions.ai_generate', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" method="POST" class="mt-4 space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Fase Kurikulum Nasional *</label>
                            <select name="target_fase" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                                <option value="Fase E (Kelas X SMA)" {{ str_contains($courseClass->schoolClass->name, 'X-') || str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase E (Kelas X SMA)</option>
                                <option value="Fase F (Kelas XI - XII SMA)" {{ !str_contains($courseClass->schoolClass->name, 'X-') && !str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase F (Kelas XI - XII SMA)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Elemen *</label>
                            <input type="text" name="elemen" required placeholder="Contoh: Pemahaman Fisika / Keterampilan Proses" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Materi Asesmen & CBT *</label>
                        <input type="text" name="materi" id="cbt_add_q_materi" value="{{ $quiz->topic ?: $quiz->title }}" required oninput="document.getElementById('cbt_add_q_topic_alias').value = this.value" placeholder="Contoh: Kinematika Gerak Lurus & Hukum Newton" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <input type="hidden" name="topic" id="cbt_add_q_topic_alias" value="{{ $quiz->topic ?: $quiz->title }}">
                        <p class="text-[10px] text-slate-400 mt-1">AI akan merumuskan proporsi butir soal HOTS yang mencakup materi di atas.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Capaian Pembelajaran (CP) *</label>
                        <textarea name="capaian_pembelajaran" rows="2" required placeholder="Tuliskan Capaian Pembelajaran (CP) yang diujikan dalam butir soal ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) *</label>
                        <textarea name="tujuan_pembelajaran" rows="2" required placeholder="Tuliskan Tujuan Pembelajaran (TP) yang menjadi sasaran indikator butir soal..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alur Tujuan Pembelajaran (ATP) *</label>
                        <textarea name="alur_tujuan_pembelajaran" rows="2" required placeholder="Tuliskan tahapan Alur Tujuan Pembelajaran (ATP) yang diukur..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bentuk Soal *</label>
                            <select name="question_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                                <option value="all" selected>Semua Tipe (Campuran)</option>
                                <option value="multiple_choice">Pilihan Ganda (PG)</option>
                                <option value="complex_multiple_choice">Pilihan Ganda Kompleks (PGK)</option>
                                <option value="true_false">Benar / Salah (B-S)</option>
                                <option value="matching">Menjodohkan (Matching)</option>
                                <option value="short_answer">Isian Singkat</option>
                                <option value="essay">Uraian / Esai (HOTS)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Butir Soal (s/d 50) *</label>
                            <select name="count" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                                <option value="3">3 Butir Soal</option>
                                <option value="5" selected>5 Butir Soal (Rekomendasi)</option>
                                <option value="10">10 Butir Soal</option>
                                <option value="15">15 Butir Soal</option>
                                <option value="20">20 Butir Soal</option>
                                <option value="25">25 Butir Soal</option>
                                <option value="30">30 Butir Soal</option>
                                <option value="40">40 Butir Soal</option>
                                <option value="50">50 Butir Soal</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Level Karakteristik Soal *</label>
                        <select name="difficulty" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="HOTS" selected>HOTS (Penalaran & Analisis C3-C5)</option>
                            <option value="MOTS">MOTS (Penerapan Konsep C2-C3)</option>
                            <option value="LOTS">LOTS (Pemahaman Konsep C1-C2)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('modal-ai-generate-questions').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                            🚀 Generate Soal Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 2: TAMBAH BUTIR SOAL SECARA MANUAL (MULTI-TIPE)   -->
        <!-- ======================================================== -->
        <div id="modal-add-question-manual" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tambah Butir Soal Manual</h3>
                        <p class="text-xs text-slate-500">Mendukung PG, PG Kompleks, Benar/Salah, Menjodohkan, Isian Singkat, & Uraian HOTS</p>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-add-question-manual').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
                </div>
                <form action="{{ route('lms.quizzes.questions.store', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bentuk Soal *</label>
                        <select name="question_type" id="add_question_type" onchange="toggleQuestionTypeFields('add', this.value)" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <option value="multiple_choice" selected>Pilihan Ganda Tunggal (PG) - 1 Kunci Benar (A–E)</option>
                            <option value="complex_multiple_choice">Pilihan Ganda Kompleks (PGK) - Lebih dari 1 Pernyataan Benar</option>
                            <option value="true_false">Benar / Salah (B-S) - Analisis Validitas Konsep</option>
                            <option value="matching">Menjodohkan (Matching) - Pasangan Premis & Respon</option>
                            <option value="short_answer">Isian Singkat - Jawaban Presisi / Terminologi</option>
                            <option value="essay">Uraian / Esai (HOTS) - Penalaran & Rubrik Berjenjang</option>
                        </select>
                    </div>

                    <!-- Stimulus Soal (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stimulus / Wacana / Data Kontekstual (Opsional)</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'add_question_stimulus'])
                            <textarea id="add_question_stimulus" name="stimulus" rows="2" placeholder="Teks wacana, studi kasus, atau data fenomena (dapat memuat gambar, simbol matematika/kimia/fisika, teks Arab, atau ayat Al-Quran)..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal (Wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pertanyaan / Instruksi Soal *</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'add_question_text'])
                            <textarea id="add_question_text" name="question_text" rows="4" required placeholder="Tuliskan pertanyaan soal secara komprehensif. Anda dapat menyisipkan gambar, rumus matematika, reaksi kimia, besaran fisika, huruf Arab, maupun ayat Al-Qur'an..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <!-- 1. Pilihan Ganda Biasa (Opsi A–E, 1 Kunci) -->
                    <div id="add_wrapper_multiple_choice" class="space-y-3">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilihan Jawaban (A s/d E) *</label>
                                <span class="text-[11px] text-slate-400">Gunakan tombol alat bantu untuk menyisipkan rumus, simbol, huruf Arab, ayat, atau gambar ke tiap opsi</span>
                            </div>

                            <!-- Opsi A -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi A</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_opt_a'])
                                </div>
                                <input type="text" id="add_opt_a" name="option_a" onfocus="window.RichEditor.setTarget('add_opt_a')" placeholder="Teks / rumus / simbol / gambar opsi A..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi B -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi B</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_opt_b'])
                                </div>
                                <input type="text" id="add_opt_b" name="option_b" onfocus="window.RichEditor.setTarget('add_opt_b')" placeholder="Teks / rumus / simbol / gambar opsi B..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi C -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi C</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_opt_c'])
                                </div>
                                <input type="text" id="add_opt_c" name="option_c" onfocus="window.RichEditor.setTarget('add_opt_c')" placeholder="Teks / rumus / simbol / gambar opsi C..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi D -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi D</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_opt_d'])
                                </div>
                                <input type="text" id="add_opt_d" name="option_d" onfocus="window.RichEditor.setTarget('add_opt_d')" placeholder="Teks / rumus / simbol / gambar opsi D..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi E -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi E (Opsional SMA)</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_opt_e'])
                                </div>
                                <input type="text" id="add_opt_e" name="option_e" onfocus="window.RichEditor.setTarget('add_opt_e')" placeholder="Teks / rumus / simbol / gambar opsi E..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kunci Jawaban Benar (PG) *</label>
                            <select name="correct_answer" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                                <option value="A">Opsi A</option>
                                <option value="B">Opsi B</option>
                                <option value="C">Opsi C</option>
                                <option value="D">Opsi D</option>
                                <option value="E">Opsi E</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Pilihan Ganda Kompleks (PGK: Pernyataan 1–4, Multi Kunci) -->
                    <div id="add_wrapper_complex_multiple_choice" class="hidden space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pernyataan & Centang Jawaban yang Benar (Bisa Lebih dari 1):</label>
                        <div class="space-y-2 text-xs">
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="A" id="add_cmc_a" class="rounded text-indigo-600">
                                        <label for="add_cmc_a" class="font-bold text-xs">Pernyataan A (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_cmc_opt_a'])
                                </div>
                                <input type="text" id="add_cmc_opt_a" name="option_a" onfocus="window.RichEditor.setTarget('add_cmc_opt_a')" placeholder="Pernyataan 1 (dapat memuat rumus/simbol/Arab)..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="B" id="add_cmc_b" class="rounded text-indigo-600">
                                        <label for="add_cmc_b" class="font-bold text-xs">Pernyataan B (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_cmc_opt_b'])
                                </div>
                                <input type="text" id="add_cmc_opt_b" name="option_b" onfocus="window.RichEditor.setTarget('add_cmc_opt_b')" placeholder="Pernyataan 2 (dapat memuat rumus/simbol/Arab)..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="C" id="add_cmc_c" class="rounded text-indigo-600">
                                        <label for="add_cmc_c" class="font-bold text-xs">Pernyataan C (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_cmc_opt_c'])
                                </div>
                                <input type="text" id="add_cmc_opt_c" name="option_c" onfocus="window.RichEditor.setTarget('add_cmc_opt_c')" placeholder="Pernyataan 3 (dapat memuat rumus/simbol/Arab)..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="D" id="add_cmc_d" class="rounded text-indigo-600">
                                        <label for="add_cmc_d" class="font-bold text-xs">Pernyataan D (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_cmc_opt_d'])
                                </div>
                                <input type="text" id="add_cmc_opt_d" name="option_d" onfocus="window.RichEditor.setTarget('add_cmc_opt_d')" placeholder="Pernyataan 4 (dapat memuat rumus/simbol/Arab)..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Benar / Salah (B-S) -->
                    <div id="add_wrapper_true_false" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kunci Jawaban Pernyataan Ini *</label>
                        <select name="correct_answer_tf" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <option value="benar">Benar (Pernyataan Sesuai Fakta / Konsep Ilmiah)</option>
                            <option value="salah">Salah (Pernyataan Keliru / Bertentangan)</option>
                        </select>
                    </div>

                    <!-- 4. Menjodohkan (Matching) -->
                    <div id="add_wrapper_matching" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pasangan Premis & Jawaban (Korelasi):</label>
                        <div class="space-y-2 text-xs">
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 1</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_match_p1'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add_match_p1" name="matching_premis[]" onfocus="window.RichEditor.setTarget('add_match_p1')" placeholder="Premis 1 (Misal: Lambang Natrium)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" id="add_match_r1" name="matching_respon[]" onfocus="window.RichEditor.setTarget('add_match_r1')" placeholder="Pasangan 1 (Misal: Na)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 2</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_match_p2'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add_match_p2" name="matching_premis[]" onfocus="window.RichEditor.setTarget('add_match_p2')" placeholder="Premis 2 (Misal: Lambang Klorin)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" id="add_match_r2" name="matching_respon[]" onfocus="window.RichEditor.setTarget('add_match_r2')" placeholder="Pasangan 2 (Misal: Cl)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 3 (Opsional)</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_match_p3'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add_match_p3" name="matching_premis[]" onfocus="window.RichEditor.setTarget('add_match_p3')" placeholder="Premis 3 (Opsional)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" id="add_match_r3" name="matching_respon[]" onfocus="window.RichEditor.setTarget('add_match_r3')" placeholder="Pasangan 3 (Opsional)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Isian Singkat -->
                    <div id="add_wrapper_short_answer" class="hidden space-y-2">
                        <div class="flex items-center justify-between flex-wrap gap-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Kunci Jawaban Singkat / Istilah *</label>
                            @include('lms.partials.rich_editor_option_tools', ['targetId' => 'add_correct_answer_sa'])
                        </div>
                        <input type="text" id="add_correct_answer_sa" name="correct_answer_sa" onfocus="window.RichEditor.setTarget('add_correct_answer_sa')" placeholder="Contoh: Fotosintesis / Mitokondria / 42 Joule / ½..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <p class="text-[11px] text-slate-400">Pemeriksaan jawaban siswa bersifat case-insensitive (huruf besar/kecil tidak dibedakan).</p>
                    </div>

                    <!-- 6. Uraian / Esai (HOTS) -->
                    <div id="add_wrapper_essay" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pedoman Penskoran / Kunci Jawaban Uraian</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'add_correct_answer_essay'])
                            <textarea id="add_correct_answer_essay" name="correct_answer_essay" rows="3" placeholder="Tuliskan kata kunci konsep, poin rubrik penilaian, atau alternatif jawaban yang diharapkan..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bobot Nilai Soal *</label>
                            <input type="number" name="score_weight" value="10" min="1" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Level Kognitif</label>
                            <select name="level_kognitif" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-semibold">
                                <option value="L1 / C1-C2 (Pemahaman & Mengingat)">L1 / C1-C2 (Pemahaman)</option>
                                <option value="L2 / C3 (Aplikasi & Penerapan)" selected>L2 / C3 (Aplikasi)</option>
                                <option value="L3 / C4-C6 (Penalaran & Analisis HOTS)">L3 / C4-C6 (Penalaran HOTS)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pembahasan Ilmiah (Opsional)</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'add_explanation'])
                            <textarea id="add_explanation" name="explanation" rows="2" placeholder="Uraikan dasar teori pembahasan butir soal ini, rumus turunan, atau dalil ilmiah..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('modal-add-question-manual').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Butir Soal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 3: EDIT BUTIR SOAL & KUNCI JAWABAN (MULTI-TIPE)     -->
        <!-- ======================================================== -->
        <div id="modal-edit-question-manual" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Edit Butir Soal & Kunci Jawaban</span>
                            <span id="edit_question_badge" class="px-2 py-0.5 rounded-full text-xs font-black bg-amber-500/20 text-amber-700 dark:text-amber-300">No. 1</span>
                        </h3>
                        <p class="text-xs text-slate-500">Perbarui redaksi soal, opsi, kunci jawaban, dan pembahasan</p>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-edit-question-manual').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
                </div>
                <form id="form-edit-question-manual" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bentuk Soal *</label>
                        <select name="question_type" id="edit_question_type" onchange="toggleQuestionTypeFields('edit', this.value)" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <option value="multiple_choice">Pilihan Ganda Tunggal (PG) - 1 Kunci Benar (A–E)</option>
                            <option value="complex_multiple_choice">Pilihan Ganda Kompleks (PGK) - Multi-Pernyataan Benar</option>
                            <option value="true_false">Benar / Salah (B-S) - Analisis Validitas Konsep</option>
                            <option value="matching">Menjodohkan (Matching) - Pasangan Premis & Respon</option>
                            <option value="short_answer">Isian Singkat - Jawaban Presisi / Terminologi</option>
                            <option value="essay">Uraian / Esai (HOTS) - Penalaran & Rubrik Berjenjang</option>
                        </select>
                    </div>

                    <!-- Stimulus Soal (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stimulus / Wacana / Data Kontekstual (Opsional)</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_question_stimulus'])
                            <textarea id="edit_question_stimulus" name="stimulus" rows="2" placeholder="Teks wacana, gambar, rumus matematika/kimia/fisika, atau ayat Al-Qur'an..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal (Wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pertanyaan / Instruksi Soal *</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_question_text'])
                            <textarea id="edit_question_text" name="question_text" rows="4" required placeholder="Tuliskan pertanyaan soal secara komprehensif..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <!-- 1. Pilihan Ganda Tunggal -->
                    <div id="edit_wrapper_multiple_choice" class="space-y-3">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilihan Jawaban (A s/d E) *</label>
                                <span class="text-[11px] text-slate-400">Gunakan tombol alat bantu untuk menyisipkan rumus, simbol, huruf Arab, ayat, atau gambar ke tiap opsi</span>
                            </div>

                            <!-- Opsi A -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi A</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_opt_a'])
                                </div>
                                <input type="text" id="edit_opt_a" name="option_a" onfocus="window.RichEditor.setTarget('edit_opt_a')" placeholder="Pilihan A..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi B -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi B</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_opt_b'])
                                </div>
                                <input type="text" id="edit_opt_b" name="option_b" onfocus="window.RichEditor.setTarget('edit_opt_b')" placeholder="Pilihan B..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi C -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi C</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_opt_c'])
                                </div>
                                <input type="text" id="edit_opt_c" name="option_c" onfocus="window.RichEditor.setTarget('edit_opt_c')" placeholder="Pilihan C..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi D -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi D</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_opt_d'])
                                </div>
                                <input type="text" id="edit_opt_d" name="option_d" onfocus="window.RichEditor.setTarget('edit_opt_d')" placeholder="Pilihan D..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <!-- Opsi E -->
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <span class="font-extrabold text-xs text-indigo-600 dark:text-indigo-400">Opsi E (Opsional SMA)</span>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_opt_e'])
                                </div>
                                <input type="text" id="edit_opt_e" name="option_e" onfocus="window.RichEditor.setTarget('edit_opt_e')" placeholder="Pilihan E (Opsional SMA)..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kunci Jawaban Benar (PG) *</label>
                            <select id="edit_mc_correct_answer" name="correct_answer" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                                <option value="A">Opsi A</option>
                                <option value="B">Opsi B</option>
                                <option value="C">Opsi C</option>
                                <option value="D">Opsi D</option>
                                <option value="E">Opsi E</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Pilihan Ganda Kompleks -->
                    <div id="edit_wrapper_complex_multiple_choice" class="hidden space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pernyataan & Centang Jawaban yang Benar:</label>
                        <div class="space-y-2 text-xs">
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="A" id="edit_cmc_a" class="rounded text-indigo-600">
                                        <label for="edit_cmc_a" class="font-bold text-xs">Pernyataan A (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_cmc_opt_a'])
                                </div>
                                <input type="text" id="edit_cmc_opt_a" name="option_a" onfocus="window.RichEditor.setTarget('edit_cmc_opt_a')" placeholder="Pernyataan 1..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="B" id="edit_cmc_b" class="rounded text-indigo-600">
                                        <label for="edit_cmc_b" class="font-bold text-xs">Pernyataan B (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_cmc_opt_b'])
                                </div>
                                <input type="text" id="edit_cmc_opt_b" name="option_b" onfocus="window.RichEditor.setTarget('edit_cmc_opt_b')" placeholder="Pernyataan 2..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="C" id="edit_cmc_c" class="rounded text-indigo-600">
                                        <label for="edit_cmc_c" class="font-bold text-xs">Pernyataan C (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_cmc_opt_c'])
                                </div>
                                <input type="text" id="edit_cmc_opt_c" name="option_c" onfocus="window.RichEditor.setTarget('edit_cmc_opt_c')" placeholder="Pernyataan 3..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-slate-900 dark:text-white">
                            </div>
                            <div class="space-y-1 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between flex-wrap gap-1">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="correct_answer_complex[]" value="D" id="edit_cmc_d" class="rounded text-indigo-600">
                                        <label for="edit_cmc_d" class="font-bold text-xs">Pernyataan D (Centang jika Benar)</label>
                                    </div>
                                    @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_cmc_opt_d'])
                                </div>
                                <input type="text" id="edit_cmc_opt_d" name="option_d" onfocus="window.RichEditor.setTarget('edit_cmc_opt_d')" placeholder="Pernyataan 4..." class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-slate-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Benar / Salah -->
                    <div id="edit_wrapper_true_false" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kunci Jawaban Pernyataan Ini *</label>
                        <select id="edit_tf_correct_answer" name="correct_answer_tf" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <option value="benar">Benar (Pernyataan Sesuai Fakta / Konsep Ilmiah)</option>
                            <option value="salah">Salah (Pernyataan Keliru / Bertentangan)</option>
                        </select>
                    </div>

                    <!-- 4. Menjodohkan -->
                    <div id="edit_wrapper_matching" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pasangan Premis & Jawaban (Korelasi):</label>
                        <div id="edit_matching_pairs_container" class="space-y-2 text-xs">
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 1</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_match_p1'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="matching_premis[]" id="edit_match_p1" onfocus="window.RichEditor.setTarget('edit_match_p1')" placeholder="Premis 1..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" name="matching_respon[]" id="edit_match_r1" onfocus="window.RichEditor.setTarget('edit_match_r1')" placeholder="Pasangan 1..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 2</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_match_p2'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="matching_premis[]" id="edit_match_p2" onfocus="window.RichEditor.setTarget('edit_match_p2')" placeholder="Premis 2..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" name="matching_respon[]" id="edit_match_r2" onfocus="window.RichEditor.setTarget('edit_match_r2')" placeholder="Pasangan 2..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-indigo-600 dark:text-indigo-400">Pasangan 3 (Opsional)</span>
                                    <div class="flex items-center gap-1">
                                        @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_match_p3'])
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="matching_premis[]" id="edit_match_p3" onfocus="window.RichEditor.setTarget('edit_match_p3')" placeholder="Premis 3 (Opsional)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                    <input type="text" name="matching_respon[]" id="edit_match_r3" onfocus="window.RichEditor.setTarget('edit_match_r3')" placeholder="Pasangan 3 (Opsional)..." class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Isian Singkat -->
                    <div id="edit_wrapper_short_answer" class="hidden space-y-2">
                        <div class="flex items-center justify-between flex-wrap gap-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Kunci Jawaban Singkat / Istilah *</label>
                            @include('lms.partials.rich_editor_option_tools', ['targetId' => 'edit_sa_correct_answer'])
                        </div>
                        <input type="text" id="edit_sa_correct_answer" name="correct_answer_sa" onfocus="window.RichEditor.setTarget('edit_sa_correct_answer')" placeholder="Contoh: Fotosintesis / 42..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <!-- 6. Uraian / Esai -->
                    <div id="edit_wrapper_essay" class="hidden space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pedoman Penskoran / Kunci Jawaban Uraian</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_essay_rubric'])
                            <textarea id="edit_essay_rubric" name="correct_answer_essay" rows="3" placeholder="Tuliskan kata kunci konsep atau poin rubrik penilaian..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bobot Nilai Soal *</label>
                            <input type="number" id="edit_score_weight" name="score_weight" min="1" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Level Kognitif</label>
                            <select id="edit_level_kognitif" name="level_kognitif" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-semibold">
                                <option value="L1 / C1-C2 (Pemahaman & Mengingat)">L1 / C1-C2 (Pemahaman)</option>
                                <option value="L2 / C3 (Aplikasi & Penerapan)">L2 / C3 (Aplikasi)</option>
                                <option value="L3 / C4-C6 (Penalaran & Analisis HOTS)">L3 / C4-C6 (Penalaran HOTS)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pembahasan Ilmiah (Opsional)</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_explanation'])
                            <textarea id="edit_explanation" name="explanation" rows="2" placeholder="Uraikan dasar teori pembahasan butir soal ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('modal-edit-question-manual').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Soal</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function toggleQuestionTypeFields(prefix, type) {
                const types = ['multiple_choice', 'complex_multiple_choice', 'true_false', 'matching', 'short_answer', 'essay'];
                types.forEach(t => {
                    const wrap = document.getElementById(prefix + '_wrapper_' + t);
                    if (wrap) {
                        if (t === type || (t === 'complex_multiple_choice' && type === 'complex_mc') || (t === 'essay' && type === 'uraian')) {
                            wrap.classList.remove('hidden');
                        } else {
                            wrap.classList.add('hidden');
                        }
                    }
                });
            }

            function openEditQuestionModal(data) {
                const modal = document.getElementById('modal-edit-question-manual');
                const form = document.getElementById('form-edit-question-manual');
                if (!modal || !form) return;

                form.action = data.update_url;
                document.getElementById('edit_question_badge').innerText = 'No. ' + (data.order_number || 1);

                // Set type
                let normType = data.question_type;
                if (normType === 'complex_mc') normType = 'complex_multiple_choice';
                if (normType === 'uraian') normType = 'essay';
                document.getElementById('edit_question_type').value = normType;
                toggleQuestionTypeFields('edit', normType);

                document.getElementById('edit_question_stimulus').value = data.stimulus || '';
                document.getElementById('edit_question_text').value = data.question_text || '';
                document.getElementById('edit_score_weight').value = data.score_weight || 10;
                document.getElementById('edit_level_kognitif').value = data.level_kognitif || 'L2 / C3 (Aplikasi & Penerapan)';
                document.getElementById('edit_explanation').value = data.explanation || '';

                // Populate options & correct answer per type
                const opts = data.options || {};
                const cor = data.correct_answer || '';

                if (normType === 'multiple_choice') {
                    document.getElementById('edit_opt_a').value = opts['A'] || '';
                    document.getElementById('edit_opt_b').value = opts['B'] || '';
                    document.getElementById('edit_opt_c').value = opts['C'] || '';
                    document.getElementById('edit_opt_d').value = opts['D'] || '';
                    document.getElementById('edit_opt_e').value = opts['E'] || '';
                    document.getElementById('edit_mc_correct_answer').value = (cor || 'A').toUpperCase();
                } else if (normType === 'complex_multiple_choice') {
                    document.getElementById('edit_cmc_opt_a').value = opts['A'] || '';
                    document.getElementById('edit_cmc_opt_b').value = opts['B'] || '';
                    document.getElementById('edit_cmc_opt_c').value = opts['C'] || '';
                    document.getElementById('edit_cmc_opt_d').value = opts['D'] || '';

                    let corList = [];
                    try {
                        corList = typeof cor === 'string' ? (cor.startsWith('[') ? JSON.parse(cor) : cor.split(',')) : cor;
                    } catch(e) {
                        corList = [cor];
                    }
                    corList = (corList || []).map(v => String(v).trim().toUpperCase());
                    document.getElementById('edit_cmc_a').checked = corList.includes('A');
                    document.getElementById('edit_cmc_b').checked = corList.includes('B');
                    document.getElementById('edit_cmc_c').checked = corList.includes('C');
                    document.getElementById('edit_cmc_d').checked = corList.includes('D');
                } else if (normType === 'true_false') {
                    document.getElementById('edit_tf_correct_answer').value = String(cor).toLowerCase() === 'salah' || String(cor).toUpperCase() === 'B' ? 'salah' : 'benar';
                } else if (normType === 'matching') {
                    const keys = Object.keys(opts);
                    document.getElementById('edit_match_p1').value = keys[0] || '';
                    document.getElementById('edit_match_r1').value = keys[0] ? opts[keys[0]] : '';
                    document.getElementById('edit_match_p2').value = keys[1] || '';
                    document.getElementById('edit_match_r2').value = keys[1] ? opts[keys[1]] : '';
                    document.getElementById('edit_match_p3').value = keys[2] || '';
                    document.getElementById('edit_match_r3').value = keys[2] ? opts[keys[2]] : '';
                } else if (normType === 'short_answer') {
                    document.getElementById('edit_sa_correct_answer').value = cor;
                } else if (normType === 'essay') {
                    document.getElementById('edit_essay_rubric').value = cor;
                }

                // Sinkronisasikan konten ke visual canvas Word-like
                ['edit_question_stimulus', 'edit_question_text', 'edit_explanation', 'edit_essay_rubric'].forEach(id => {
                    if (window.RichEditor && typeof window.RichEditor.initVisualCanvas === 'function') {
                        window.RichEditor.initVisualCanvas(id);
                        const slug = id.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                        const visual = document.getElementById('rich-visual-' + slug);
                        const ta = document.getElementById(id);
                        if (visual && ta) {
                            visual.innerHTML = window.RichEditor.markdownToVisualHtml(ta.value) || '<p><br></p>';
                        }
                    }
                });

                modal.classList.remove('hidden');
            }

            @if(session('open_add_question') || request('open_input'))
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modal-add-question-manual');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });
            @endif
        </script>

        <!-- ======================================================== -->
        <!-- MODAL: EDIT ASESMEN & CBT -->
        <!-- ======================================================== -->
        <div id="modal-edit-quiz" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </span>
                            <span>Edit Asesmen & CBT</span>
                        </h3>
                        <p class="text-xs text-slate-500">Perbarui pengaturan jadwal, durasi, topik, dan status ujian</p>
                    </div>
                    <button type="button" onclick="closeEditQuizModal()" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form action="{{ route('lms.quizzes.update', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" method="POST" class="mt-4 space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Asesmen / Ujian *</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Topik / Ruang Lingkup Materi Asesmen</label>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md">Bisa Lebih dari 1 Topik</span>
                        </div>
                        <textarea name="topic" rows="2" placeholder="Tuliskan topik / ruang lingkup materi (bisa beberapa, pisahkan baris baru / koma)..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white">{{ old('topic', $quiz->topic) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Asesmen *</label>
                            <select name="quiz_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                                <option value="formatif" {{ old('quiz_type', $quiz->quiz_type) === 'formatif' ? 'selected' : '' }}>Asesmen Formatif (Kuis Cek Penguasaan)</option>
                                <option value="sumatif_materi" {{ old('quiz_type', $quiz->quiz_type) === 'sumatif_materi' ? 'selected' : '' }}>Asesmen Sumatif (Lingkup Materi / UH)</option>
                                <option value="sts" {{ old('quiz_type', $quiz->quiz_type) === 'sts' ? 'selected' : '' }}>Sumatif Tengah Semester (STS)</option>
                                <option value="sas" {{ old('quiz_type', $quiz->quiz_type) === 'sas' ? 'selected' : '' }}>Sumatif Akhir Semester (SAS)</option>
                                <option value="saj" {{ old('quiz_type', $quiz->quiz_type) === 'saj' ? 'selected' : '' }}>Sumatif Akhir Jenjang (SAJ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Pengerjaan (Menit) *</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="5" max="300" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="10" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jadwal Mulai Ujian (Opsional)</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time', $quiz->start_time ? $quiz->start_time->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Selesai Ujian (Opsional)</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time', $quiz->end_time ? $quiz->end_time->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tata Tertib / Petunjuk Asesmen</label>
                        <textarea name="instructions" rows="2" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white">{{ old('instructions', $quiz->instructions) }}</textarea>
                    </div>

                    <div class="space-y-2 pt-1">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_quiz_is_randomized" name="is_randomized" value="1" {{ old('is_randomized', $quiz->is_randomized) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_quiz_is_randomized" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Acak urutan butir soal saat dikerjakan siswa</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_quiz_is_published" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_quiz_is_published" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Publikasikan asesmen ke siswa kelas</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" onclick="closeEditQuizModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Asesmen</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditQuizModal() {
                const modal = document.getElementById('modal-edit-quiz');
                if (modal) modal.classList.remove('hidden');
            }
            function closeEditQuizModal() {
                const modal = document.getElementById('modal-edit-quiz');
                if (modal) modal.classList.add('hidden');
            }
        </script>
    @endif
</div>

@include('lms.partials.rich_editor_modals')
@endsection
