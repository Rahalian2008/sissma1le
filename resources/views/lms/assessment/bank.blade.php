@extends('layouts.app')

@section('title', 'Bank Soal & Kisi-Kisi Asesmen - LMS SMAN 1 Lengkong')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')

    <!-- Flash Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Bank Soal & Kisi-Kisi Terpetakan TP • SMAN 1 Lengkong
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Bank Soal & Asesmen Terpadu
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Pengelolaan butir soal standar Kurikulum Merdeka (Pilihan Ganda & Uraian HOTS), pemetaan level kognitif (L1–L3), cetak kisi-kisi asesmen resmi, dan asisten generator AI.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($isTeacher || $isKurikulum)
                    <button onclick="openModal('modalCreateQuestion')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Tambah Soal</span>
                    </button>
                    <button onclick="openModal('modalAiQuestion')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs sm:text-sm shadow-md transition border border-purple-400/30">
                        <span>✨</span>
                        <span>AI Asisten Soal HOTS</span>
                    </button>
                    @if($subjectFilter)
                        <a href="{{ route('lms.assessment.kisi_kisi.print', ['subject_id' => $subjectFilter]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs sm:text-sm shadow-md transition border border-slate-700">
                            <span>🖨️</span>
                            <span>Cetak Kisi-Kisi</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <!-- Matrix Statistik Bank Soal -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Total Butir Soal</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-blue-400 uppercase">Pilihan Ganda</div>
                <div class="text-xl sm:text-2xl font-black text-blue-300 mt-1">{{ $stats['pg'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-emerald-400 uppercase">Uraian / Esai</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-300 mt-1">{{ $stats['essay'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-purple-600/40">
                <div class="text-[11px] font-bold text-purple-400 uppercase">Soal HOTS (L3)</div>
                <div class="text-xl sm:text-2xl font-black text-purple-300 mt-1">{{ $stats['hots'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('lms.assessment.bank') }}" class="flex flex-wrap items-center gap-3">
            <select name="subject_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Mata Pelajaran --</option>
                @foreach($subjects as $sb)
                    <option value="{{ $sb->id }}" {{ $subjectFilter == $sb->id ? 'selected' : '' }}>{{ $sb->name }}</option>
                @endforeach
            </select>

            <select name="grade_level" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Tingkat Kelas --</option>
                <option value="X" {{ $gradeFilter === 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
                <option value="XI" {{ $gradeFilter === 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
                <option value="XII" {{ $gradeFilter === 'XII' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
            </select>

            <select name="cognitive_level" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Level Kognitif --</option>
                <option value="L1" {{ $cognitiveFilter === 'L1' ? 'selected' : '' }}>Level 1: Pemahaman (C1-C2)</option>
                <option value="L2" {{ $cognitiveFilter === 'L2' ? 'selected' : '' }}>Level 2: Aplikasi (C3)</option>
                <option value="L3" {{ $cognitiveFilter === 'L3' ? 'selected' : '' }}>Level 3: Penalaran / HOTS (C4-C6)</option>
            </select>

            <select name="question_type" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Bentuk Soal --</option>
                <option value="multiple_choice" {{ $typeFilter === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="essay" {{ $typeFilter === 'essay' ? 'selected' : '' }}>Uraian</option>
            </select>
        </form>

        <span class="text-xs text-slate-500 font-semibold">
            Menampilkan {{ $questions->total() }} butir soal
        </span>
    </div>

    <!-- Daftar Butir Soal -->
    @if($questions->isEmpty())
        <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <span class="text-4xl">🎯</span>
            <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Bank Soal Masih Kosong</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                Tambahkan butir soal terpetakan Tujuan Pembelajaran secara manual atau gunakan AI Asisten Soal HOTS untuk merumuskan stimulus soal kontekstual.
            </p>
            <button onclick="openModal('modalCreateQuestion')" class="mt-4 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                + Tambah Soal Pertama
            </button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($questions as $index => $q)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-3 hover:border-indigo-400 dark:hover:border-indigo-600 transition">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $q->type_label }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider
                                {{ $q->cognitive_level === 'L3' ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800' : 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200' }}">
                                {{ $q->cognitive_label }}
                            </span>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $q->subject->name ?? 'Mapel' }} • Kelas {{ $q->grade_level }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold text-slate-500">Bobot Skor: {{ $q->score_weight }}</span>
                            <form method="POST" action="{{ route('lms.assessment.bank.questions.destroy', $q) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus butir soal ini?')" class="text-rose-600 hover:underline text-xs font-semibold ml-2">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Keterkaitan TP & Indikator -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($q->learningObjective)
                            <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-bold border border-purple-200 dark:border-purple-800 text-[11px]">
                                🎯 TP {{ $q->learningObjective->tp_code }}: {{ $q->learningObjective->title }}
                            </span>
                        @endif
                        @if($q->indicator)
                            <span class="text-slate-500 italic text-[11px]">Indikator: {{ $q->indicator }}</span>
                        @endif
                    </div>

                    <!-- Stimulus Soal jika ada -->
                    @if($q->stimulus)
                        <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 text-xs text-amber-900 dark:text-amber-200 leading-relaxed whitespace-pre-line">
                            <div class="font-bold mb-1">Stimulus / Wacana:</div>
                            {{ $q->stimulus }}
                        </div>
                    @endif

                    <!-- Teks Pertanyaan -->
                    <div class="text-xs font-semibold text-slate-900 dark:text-white leading-relaxed">
                        {{ $q->question_text }}
                    </div>

                    <!-- Opsi Jawaban (Jika Pilihan Ganda) -->
                    @if($q->question_type === 'multiple_choice' && !empty($q->options))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-2">
                            @foreach($q->options as $key => $opt)
                                @php
                                    $isCorrect = ($q->correct_answer === $key || $q->correct_answer === (string)$opt);
                                @endphp
                                <div class="p-2 rounded-xl border {{ $isCorrect ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300' }}">
                                    <span class="mr-1.5 font-black">{{ $key }}.</span> {{ is_array($opt) ? ($opt['text'] ?? json_encode($opt)) : $opt }}
                                    @if($isCorrect)
                                        <span class="ml-1 text-[10px] text-emerald-600 font-extrabold">(Kunci)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($q->correct_answer)
                        <!-- Pedoman Penskoran Esai -->
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Pedoman Penskoran / Kunci:</span>
                            <p class="mt-0.5 text-slate-600 dark:text-slate-400">{{ $q->correct_answer }}</p>
                        </div>
                    @endif

                    @if($q->explanation)
                        <div class="text-[11px] text-slate-500 italic pt-1">
                            <strong>Pembahasan:</strong> {{ $q->explanation }}
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="mt-4">
                {{ $questions->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH BUTIR SOAL MANUAL           -->
<!-- ========================================== -->
<div id="modalCreateQuestion" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-8">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Butir Soal Baru</h3>
                <p class="text-xs text-slate-500">Petakan butir soal ke Tujuan Pembelajaran (TP) & Level Kognitif.</p>
            </div>
            <button onclick="closeModal('modalCreateQuestion')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.assessment.bank.questions.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran *</label>
                    <select name="subject_id" id="formSubjectId" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas *</label>
                    <select name="grade_level" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="X">Kelas X (Fase E)</option>
                        <option value="XI">Kelas XI (Fase F)</option>
                        <option value="XII">Kelas XII (Fase F)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP)</label>
                    <select name="learning_objective_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">-- Tanpa Kaitan TP Spesifik --</option>
                        @foreach($objectives as $tp)
                            <option value="{{ $tp->id }}">[TP {{ $tp->tp_code }}] {{ $tp->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Lingkup Materi *</label>
                    <input type="text" name="topic" id="formTopic" required placeholder="Contoh: Logika Algoritma & Flowchart" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bentuk Soal *</label>
                    <select name="question_type" id="formQType" onchange="toggleOptionFields(this.value)" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="multiple_choice">Pilihan Ganda (A-E)</option>
                        <option value="essay">Uraian / Esai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Level Kognitif *</label>
                    <select name="cognitive_level" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="L2">Level 2: Aplikasi (C3)</option>
                        <option value="L1">Level 1: Pemahaman (C1-C2)</option>
                        <option value="L3">Level 3: Penalaran / HOTS (C4-C6)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bobot Skor *</label>
                    <input type="number" name="score_weight" value="1" min="1" max="100" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Indikator Soal (Untuk Kisi-Kisi)</label>
                <input type="text" name="indicator" id="formIndicator" placeholder="Disajikan ilustrasi, peserta didik dapat menganalisis..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stimulus / Wacana Bacaan (Opsional)</label>
                <textarea name="stimulus" id="formStimulus" rows="2" placeholder="Teks studi kasus, data tabel, atau ilustrasi..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Pertanyaan *</label>
                <textarea name="question_text" id="formQuestionText" rows="3" required placeholder="Tuliskan butir soal pertanyaan secara jelas..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <!-- Opsi Pilihan Ganda -->
            <div id="optionFieldsGroup" class="space-y-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Opsi Jawaban & Kunci (Pilihan Ganda)</label>
                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                    <div class="flex items-center gap-2">
                        <span class="w-6 text-xs font-bold text-slate-600 dark:text-slate-400">{{ $opt }}.</span>
                        <input type="text" name="options[{{ $opt }}]" placeholder="Pilihan {{ $opt }}" class="flex-1 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-slate-900 dark:text-white">
                        <label class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                            <input type="radio" name="correct_answer" value="{{ $opt }}" {{ $opt === 'A' ? 'checked' : '' }}>
                            <span>Kunci</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div id="essayAnswerGroup" class="hidden">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pedoman Penskoran / Kunci Esai</label>
                <textarea name="correct_answer_essay" rows="2" placeholder="Kriteria jawaban yang diharapkan..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateQuestion')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Butir Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: AI ASISTEN SOAL HOTS & KISI-KISI    -->
<!-- ========================================== -->
<div id="modalAiQuestion" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-purple-500/30 max-w-xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl">✨</span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">AI Generator Soal HOTS</h3>
                    <p class="text-xs text-purple-600 font-medium">Buat butir soal kontekstual terpetakan TP & Level Kognitif.</p>
                </div>
            </div>
            <button onclick="closeModal('modalAiQuestion')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran</label>
                <input type="text" id="aiSubName" value="{{ $subjects->first()->name ?? 'Informatika' }}" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                    <select id="aiGradeLevel" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="X">Kelas X (Fase E)</option>
                        <option value="XI">Kelas XI (Fase F)</option>
                        <option value="XII">Kelas XII (Fase F)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Kognitif</label>
                    <select id="aiCognitive" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="L3">Level 3: Penalaran / HOTS (C4-C6)</option>
                        <option value="L2">Level 2: Aplikasi (C3)</option>
                        <option value="L1">Level 1: Pemahaman (C1-C2)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Lingkup Materi *</label>
                <input type="text" id="aiQuestionTopic" placeholder="Misal: Keamanan Siber & Enkripsi Data" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <button type="button" id="btnRunAiQuestion" onclick="runAiQuestionGenerator()" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                <span id="aiQuestionSpinner" class="hidden animate-spin">⏳</span>
                <span>✨ Susun Draf Soal dengan AI</span>
            </button>

            <!-- Preview Hasil AI -->
            <div id="aiQuestionResultContainer" class="hidden space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <span class="font-bold text-xs text-purple-700 dark:text-purple-300">💡 Draf Soal Hasil Rekomendasi AI:</span>
                <div id="aiQuestionResultText" class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap max-h-56 overflow-y-auto leading-relaxed">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    function toggleOptionFields(type) {
        const optGroup = document.getElementById('optionFieldsGroup');
        const essayGroup = document.getElementById('essayAnswerGroup');
        if (type === 'essay') {
            optGroup.classList.add('hidden');
            essayGroup.classList.remove('hidden');
        } else {
            optGroup.classList.remove('hidden');
            essayGroup.classList.add('hidden');
        }
    }

    async function runAiQuestionGenerator() {
        const topic = document.getElementById('aiQuestionTopic').value.trim();
        const subject = document.getElementById('aiSubName').value.trim();
        const grade = document.getElementById('aiGradeLevel').value;
        const cognitive = document.getElementById('aiCognitive').value;

        if (!topic) {
            alert('Silakan masukkan topik materi terlebih dahulu.');
            return;
        }

        const spinner = document.getElementById('aiQuestionSpinner');
        const btn = document.getElementById('btnRunAiQuestion');
        spinner.classList.remove('hidden');
        btn.disabled = true;

        try {
            const resp = await fetch("{{ route('lms.assessment.bank.ai_generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject_name: subject,
                    topic: topic,
                    grade: grade,
                    cognitive_level: cognitive
                })
            });

            const data = await resp.json();
            if (data.status === 'ok') {
                document.getElementById('aiQuestionResultText').textContent = data.content;
                document.getElementById('aiQuestionResultContainer').classList.remove('hidden');
            } else {
                alert('Gagal membuat draf soal AI.');
            }
        } catch (e) {
            console.error(e);
            alert('Kendala koneksi AI.');
        } finally {
            spinner.classList.add('hidden');
            btn.disabled = false;
        }
    }
</script>
@endsection

