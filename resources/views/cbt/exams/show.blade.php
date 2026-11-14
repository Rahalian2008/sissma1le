@extends('layouts.app')

@section('title', "{$exam->title} - Manajer Soal & Ruang CBT")

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
<style>
    .katex-display { margin: 0.5em 0 !important; }
</style>
@endpush

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/mhchem.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof renderMathInElement === 'function') {
            renderMathInElement(document.body, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ],
                throwOnError: false
            });
        }
    });
</script>
@endpush

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: 'questions', 
    manualModal: false, 
    aiModal: false, 
    importModal: false,
    distributeModal: false,
    printModal: false,
    selectedType: 'multiple_choice',
    questionText: '',
    stimulusText: '',
    formulaCategory: 'math',
    imagePreview: null,
    printDoc: 'daftar_hadir',
    printRoom: '',
    printClass: '',
    printManual: '0',

    previewMath() {
        this.$nextTick(() => {
            const el = document.getElementById('formula-live-preview');
            if (el && typeof renderMathInElement === 'function') {
                renderMathInElement(el, {
                    delimiters: [
                        {left: '$$', right: '$$', display: true},
                        {left: '$', right: '$', display: false},
                        {left: '\\(', right: '\\)', display: false},
                        {left: '\\[', right: '\\]', display: true}
                    ],
                    throwOnError: false
                });
            }
        });
    },

    insertFormula(sym) {
        const textarea = document.getElementById('manual_question_text');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + sym + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + sym.length;
        textarea.focus();
        this.questionText = textarea.value;
        this.previewMath();
    },

    handleImageSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.imagePreview = URL.createObjectURL(file);
        }
    },

    getPrintUrl() {
        let base = '';
        if (this.printDoc === 'daftar_hadir') base = '{{ route('cbt.documents.daftar_hadir', $exam->id) }}';
        else if (this.printDoc === 'berita_acara') base = '{{ route('cbt.documents.berita_acara', $exam->id) }}';
        else if (this.printDoc === 'kartu_peserta') base = '{{ route('cbt.documents.kartu_peserta', $exam->id) }}';
        else if (this.printDoc === 'kartu_soal') base = '{{ route('cbt.documents.kartu_soal', $exam->id) }}';
        else if (this.printDoc === 'kisi_kisi') base = '{{ route('cbt.documents.kisi_kisi', $exam->id) }}';

        let params = new URLSearchParams();
        if (this.printRoom) params.append('room', this.printRoom);
        if (this.printClass) params.append('class_id', this.printClass);
        if (this.printManual === '1') params.append('is_manual', '1');

        let q = params.toString();
        return base + (q ? ('?' + q) : '');
    }
}">

    <!-- TOP HEADER & EXAM METADATA -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 text-white border border-indigo-500/20 shadow-xl space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('cbt.index') }}" class="text-xs font-bold text-indigo-300 hover:text-white transition flex items-center gap-1">
                        <span>⬅</span>
                        <span>Daftar Ujian</span>
                    </a>
                    <span class="text-slate-500">•</span>
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                        {{ $exam->exam_type }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $exam->status === 'published' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-slate-700 text-slate-300' }}">
                        {{ ucfirst($exam->status) }}
                    </span>
                    @if($exam->require_token && !empty($exam->token))
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-mono font-black bg-amber-500/20 text-amber-300 border border-amber-400/30 tracking-widest">
                            🔑 TOKEN WAJIB: {{ $exam->token }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            ⚡ BEBAS TOKEN (Langsung Masuk)
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    {{ $exam->title }}
                </h1>

                <div class="flex items-center gap-4 flex-wrap text-xs text-slate-300 pt-1">
                    <div>📚 Mapel: <span class="font-bold text-white">{{ $exam->subject?->name ?? 'Umum' }}</span></div>
                    <div>👨‍🏫 Guru: <span class="font-bold text-white">{{ $exam->teacher?->name ?? 'Belum Ditentukan' }}</span></div>
                    <div>⏱️ Durasi: <span class="font-bold text-white">{{ $exam->duration_minutes }} Menit</span></div>
                    <div>🎯 KKM: <span class="font-bold text-white">{{ $exam->pass_percentage }}</span></div>
                    <div>🏛️ Ruang: <span class="font-bold text-amber-300">{{ count($availableRooms) }} Ruang ({{ implode(', ', $availableRooms) }})</span></div>
                </div>
            </div>

            <!-- Action Buttons Header -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <a href="{{ route('cbt.exams.monitor', $exam->id) }}" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span>📡 Live Monitor</span>
                </a>
                <a href="{{ route('cbt.exams.results', $exam->id) }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span>📊 Rekap Nilai</span>
                </a>
                <a href="{{ route('cbt.exams.edit', $exam->id) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                    ⚙️ Edit
                </a>
            </div>
        </div>

        <!-- QUICK DOKUMEN CETAK BAR -->
        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-300">🖨️ Cetak Dokumen Ujian:</span>
                <span class="text-[11px] text-slate-400">(Tersedia format otomatis & format manual / blanko)</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="printModal = true" class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                    <span>⚙️ Opsi Cetak Per Ruang & Manual Blanko</span>
                </button>
                <a href="{{ route('cbt.documents.kisi_kisi', $exam->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition">
                    📑 Kisi-Kisi
                </a>
                <a href="{{ route('cbt.documents.kartu_soal', $exam->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition">
                    🎴 Kartu Soal
                </a>
                <a href="{{ route('cbt.documents.kartu_peserta', $exam->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition">
                    🪪 Kartu Peserta
                </a>
                <a href="{{ route('cbt.documents.daftar_hadir', $exam->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition">
                    📋 Daftar Hadir
                </a>
                <a href="{{ route('cbt.documents.berita_acara', $exam->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition">
                    📜 Berita Acara
                </a>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION: BUTIR SOAL VS RUANG & AKSES SISWA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-2">
            <button type="button" @click="activeTab = 'questions'" class="px-4 py-2 rounded-xl font-black text-xs transition cursor-pointer flex items-center gap-1.5"
                    :class="activeTab === 'questions' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                <span>📝 Butir Soal Ujian ({{ $exam->questions->count() }})</span>
            </button>
            <button type="button" @click="activeTab = 'students'" class="px-4 py-2 rounded-xl font-black text-xs transition cursor-pointer flex items-center gap-1.5"
                    :class="activeTab === 'students' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'">
                <span>🏛️ Ruang Ujian & Akses Peserta ({{ $sessions->count() }})</span>
            </button>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <template x-if="activeTab === 'questions'">
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="manualModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>➕ Tambah Soal Manual</span>
                    </button>
                    <button type="button" @click="aiModal = true" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>✨ Generate Soal AI</span>
                    </button>
                    <button type="button" @click="importModal = true" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1 cursor-pointer">
                        <span>📥 Import CSV / JSON</span>
                    </button>
                </div>
            </template>

            <template x-if="activeTab === 'students'">
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="distributeModal = true" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>⚡ Distribusi Ruang Otomatis (Zig-Zag)</span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: DAFTAR BUTIR SOAL UJIAN -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'questions'" class="space-y-4">
        @if($exam->questions->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-3">
                <div class="text-5xl">📄</div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Belum Ada Soal di Paket Ini</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Silakan tambahkan butir soal secara manual dengan editor rumus lengkap (Matematika, Kimia, Fisika, Biologi), generate dengan AI Gemini, atau import via CSV template.
                </p>
            </div>
        @else
            @foreach($exam->questions as $q)
                <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <!-- Header Soal -->
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30 font-black text-xs flex items-center justify-center">
                                #{{ $q->question_number }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $q->type_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                                {{ $q->cognitive_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300">
                                Tingkat: {{ $q->difficulty_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                                Bobot: {{ $q->score_weight }} Poin
                            </span>
                        </div>

                        <!-- Tombol Hapus Soal -->
                        <form action="{{ route('cbt.exams.questions.destroy', [$exam->id, $q->id]) }}" method="POST" onsubmit="return confirm('Hapus soal nomor {{ $q->question_number }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-xs font-bold cursor-pointer" title="Hapus Soal">
                                🗑️
                            </button>
                        </form>
                    </div>

                    <!-- Capaian Pembelajaran & Indikator Soal -->
                    @if(!empty($q->learning_objective) || !empty($q->indicator))
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-850 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                            @if(!empty($q->learning_objective))
                                <div><strong class="text-slate-400">Capaian Pembelajaran (CP):</strong> <span class="text-slate-700 dark:text-slate-300">{{ $q->learning_objective }}</span></div>
                            @endif
                            @if(!empty($q->indicator))
                                <div><strong class="text-slate-400">Indikator Soal:</strong> <span class="text-slate-700 dark:text-slate-300">{{ $q->indicator }}</span></div>
                            @endif
                        </div>
                    @endif

                    <!-- Stimulus / Wacana Bacaan -->
                    @if(!empty($q->stimulus))
                        <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 text-xs text-slate-800 dark:text-amber-200 leading-relaxed font-serif">
                            <div class="text-[10px] font-sans font-black uppercase text-amber-700 dark:text-amber-400 mb-1">Stimulus / Wacana:</div>
                            {!! nl2br(e($q->stimulus)) !!}
                        </div>
                    @endif

                    <!-- Ilustrasi Gambar jika ada -->
                    @if(!empty($q->media_path))
                        <div class="max-w-md rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset('storage/'.$q->media_path) }}" alt="Gambar Soal #{{ $q->question_number }}" class="w-full h-auto object-contain max-h-80">
                        </div>
                    @endif

                    <!-- Teks Soal -->
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-relaxed whitespace-pre-wrap">
                        {{ $q->question_text }}
                    </div>

                    <!-- Tampilan Opsi & Kunci Jawaban -->
                    <div class="pt-2">
                        @php
                            $normOpts = $q->normalized_options;
                        @endphp

                        @if($q->question_type === 'multiple_choice' || $q->question_type === 'complex_multiple_choice')
                            <div class="space-y-2">
                                @foreach($normOpts as $opt)
                                    @php
                                        $k = $opt['key'] ?? '';
                                        $isCorrect = false;
                                        if ($q->question_type === 'multiple_choice') {
                                            $isCorrect = (string)$k === (string)$q->correct_answer;
                                        } else {
                                            $cArr = is_array($q->correct_answer) ? $q->correct_answer : [];
                                            $isCorrect = in_array((string)$k, array_map('strval', $cArr));
                                        }
                                    @endphp
                                    <div class="flex items-start gap-2.5 p-3 rounded-xl border text-xs {{ $isCorrect ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500/40 text-emerald-900 dark:text-emerald-200 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }}">
                                        <span class="w-6 h-6 rounded-lg {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} font-black text-xs flex items-center justify-center shrink-0">
                                            {{ $k }}
                                        </span>
                                        <div class="flex-1 pt-0.5">{{ $opt['text'] ?? '' }}</div>
                                        @if($isCorrect)
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase">
                                                Kunci Jawaban ✓
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($q->question_type === 'true_false')
                            <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-2xl">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">
                                            <th class="p-2.5 w-10">No</th>
                                            <th class="p-2.5">Pernyataan</th>
                                            <th class="p-2.5 text-center w-28">Kunci Kebenaran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @php 
                                            $keysMap = [];
                                            if (is_array($q->correct_answer)) {
                                                foreach ($q->correct_answer as $ka) {
                                                    if (isset($ka['id'])) $keysMap[$ka['id']] = $ka['is_true'] ?? false;
                                                }
                                            }
                                        @endphp
                                        @foreach($normOpts as $idx => $st)
                                            @php $isTrue = $keysMap[$st['id'] ?? ''] ?? false; @endphp
                                            <tr>
                                                <td class="p-2.5 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                                <td class="p-2.5 font-medium text-slate-800 dark:text-slate-200">{{ $st['statement'] ?? '-' }}</td>
                                                <td class="p-2.5 text-center">
                                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $isTrue ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                                                        {{ $isTrue ? 'BENAR' : 'SALAH' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @elseif($q->question_type === 'matching')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-2">
                                    <div class="font-black text-indigo-600 dark:text-indigo-400">Premis (Kolom Kiri):</div>
                                    @foreach($normOpts as $pair)
                                        <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-medium">
                                            <strong>{{ $pair['id'] }}.</strong> {{ $pair['premise'] }}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-2">
                                    <div class="font-black text-amber-600 dark:text-amber-400">Respon (Kolom Kanan):</div>
                                    @foreach($normOpts as $pair)
                                        <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-medium">
                                            <strong>{{ $pair['response_id'] ?? '-' }}.</strong> {{ $pair['response'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        @elseif($q->question_type === 'short_answer')
                            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-500/30 text-xs text-emerald-800 dark:text-emerald-200">
                                <strong>Kunci Jawaban Isian Singkat:</strong> <span class="font-mono font-bold text-sm ml-1">{{ is_array($q->correct_answer) ? implode(', ', $q->correct_answer) : $q->correct_answer }}</span>
                            </div>

                        @elseif($q->question_type === 'essay')
                            <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-500/30 text-xs text-indigo-900 dark:text-indigo-200 space-y-1">
                                <div class="font-black text-indigo-700 dark:text-indigo-300">Pedoman Penskoran / Rubrik Esai:</div>
                                <div class="whitespace-pre-line text-slate-700 dark:text-slate-300">{{ $q->correct_answer }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Pembahasan -->
                    @if(!empty($q->explanation))
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                            <strong class="text-indigo-600 dark:text-indigo-400">💡 Pembahasan:</strong> {{ $q->explanation }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: MANAJEMEN RUANG UJIAN & AKSES SISWA -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'students'" class="space-y-4">
        <!-- STATS & KONTROL MASAL -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[11px] text-slate-400 block font-bold">Total Siswa Terdaftar</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ $sessions->count() }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[11px] text-slate-400 block font-bold">Ruang Ujian Tersedia</span>
                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1 block">{{ count($availableRooms) }} Ruang</span>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[11px] text-slate-400 block font-bold">Akses Aktif (Boleh Ujian)</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">{{ $sessions->where('is_allowed', true)->count() }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[11px] text-slate-400 block font-bold">Akses Terkunci (Nonaktif)</span>
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 block">{{ $sessions->where('is_allowed', false)->count() }}</span>
            </div>
        </div>

        <!-- ACTION BAR AKSES SISWA -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="text-xs text-slate-600 dark:text-slate-300">
                <strong>Kontrol Akses Ujian:</strong> Guru / Pengawas dapat mengaktifkan atau menonaktifkan akses masuk siswa tanpa repot membagikan token.
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <form action="{{ route('cbt.exams.bulk_access', $exam->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="allow_all">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                        <span>🔓 Aktifkan Semua Siswa</span>
                    </button>
                </form>

                <form action="{{ route('cbt.exams.bulk_access', $exam->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="block_all">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                        <span>🔒 Kunci Semua Siswa</span>
                    </button>
                </form>

                <button type="button" @click="distributeModal = true" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                    <span>⚡ Atur / Bagi Ruang Ujian</span>
                </button>
            </div>
        </div>

        <!-- TABEL DAFTAR SISWA & RUANG -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Nama Lengkap Siswa</th>
                            <th class="p-3 w-28 text-center">NIS</th>
                            <th class="p-3 w-32">Kelas / Rombel</th>
                            <th class="p-3 w-40 text-center">Ruang Ujian</th>
                            <th class="p-3 w-36 text-center">Status Akses Masuk</th>
                            <th class="p-3 w-32 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($sessions as $index => $sess)
                            @php $student = $sess->student; @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="p-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $student?->name ?? 'Siswa' }}
                                    @if($sess->is_blocked)
                                        <span class="px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-600 text-[10px] font-bold ml-1">Terblokir Cheat</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-mono text-slate-600 dark:text-slate-300">{{ $student?->nis ?? '-' }}</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">{{ $student?->schoolClass?->name ?? '-' }}</td>
                                
                                <!-- RUANG UJIAN (INLINE EDITABLE / DROPDOWN) -->
                                <td class="p-3 text-center">
                                    <form action="{{ route('cbt.exams.update_room', [$exam->id, $sess->id]) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        <select name="room_name" onchange="this.form.submit()" class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                                            @foreach($availableRooms as $r)
                                                <option value="{{ $r }}" {{ $sess->room_name === $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <!-- STATUS AKSES (TOGGLE SWITCH BUTTON) -->
                                <td class="p-3 text-center">
                                    @if($sess->is_allowed)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                            ✓ Boleh Akses
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                                            ✕ Dinonaktifkan
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI TOGGLE AKSES -->
                                <td class="p-3 text-center">
                                    <form action="{{ route('cbt.exams.toggle_access', [$exam->id, $sess->id]) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer {{ $sess->is_allowed ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' }}">
                                            {{ $sess->is_allowed ? 'Kunci' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 italic">
                                    Belum ada siswa yang didaftarkan ke sesi ujian. Jalankan tombol "Distribusi Ruang Otomatis" untuk memasukkan siswa dari kelas yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 1: TAMBAH SOAL MANUAL DENGAN FORMULA TOOLBAR LENGKAP & LIVE PREVIEW -->
    <!-- ===================================================================== -->
    <div x-show="manualModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-4xl rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 max-h-[92vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📐</span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Tambah Butir Soal Baru (Editor Rumus Lengkap)</h3>
                        <p class="text-[11px] text-slate-400">Mendukung formula Matematika, Kimia (mhchem), Fisika & Biologi dengan Live KaTeX Preview</p>
                    </div>
                </div>
                <button type="button" @click="manualModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('cbt.exams.questions.store', $exam->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Pilihan Tipe Soal & Bobot -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Bentuk / Tipe Soal <span class="text-rose-500">*</span></label>
                        <select name="question_type" x-model="selectedType" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white">
                            <option value="multiple_choice">Pilihan Ganda (Single Choice)</option>
                            <option value="complex_multiple_choice">Pilihan Ganda Kompleks (Multi-Select)</option>
                            <option value="true_false">Benar / Salah (Pernyataan)</option>
                            <option value="matching">Menjodohkan (Matching)</option>
                            <option value="short_answer">Isian Singkat (Short Answer)</option>
                            <option value="essay">Uraian / Esai (Essay)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Bobot Skor <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.5" name="score_weight" value="1.0" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                </div>

                <!-- Capaian Pembelajaran & Indikator -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Capaian Pembelajaran (CP) / KD</label>
                        <input type="text" name="learning_objective" placeholder="Tujuan / Capaian Pembelajaran..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Indikator Soal</label>
                        <input type="text" name="indicator" placeholder="Disajikan ..., peserta didik dapat..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                    </div>
                </div>

                <!-- Level Kognitif & Tingkat Kesukaran -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Level Kognitif</label>
                        <select name="cognitive_level" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <option value="L1">L1 (Pemahaman / C1-C2)</option>
                            <option value="L2" selected>L2 (Aplikasi / C3)</option>
                            <option value="L3">L3 (Penalaran / HOTS / C4-C6)</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Tingkat Kesukaran</label>
                        <select name="difficulty_level" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <option value="mudah">Mudah</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="sukar">Sukar</option>
                        </select>
                    </div>
                </div>

                <!-- Stimulus Soal (Opsional) -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Stimulus / Wacana / Narasi Kontekstual (Opsional)</label>
                    <textarea name="stimulus" rows="2" placeholder="Teks wacana, studi kasus, atau data pendukung..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs"></textarea>
                </div>

                <!-- UPLOAD GAMBAR SOAL -->
                <div class="space-y-1 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-850 border border-slate-200/80 dark:border-slate-700">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">🖼️ Sisipkan Gambar / Bagan / Diagram Soal (Opsional)</label>
                    <input type="file" name="question_image" accept="image/*" @change="handleImageSelect($event)" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <template x-if="imagePreview">
                        <div class="mt-2 max-w-xs rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700">
                            <img :src="imagePreview" alt="Preview Gambar" class="w-full h-auto object-cover max-h-48">
                        </div>
                    </template>
                </div>

                <!-- TOOLBAR FORMULA & RUMUS SAINS LENGKAP -->
                <div class="space-y-2 p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-slate-850 border border-indigo-200/60 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2 flex-wrap pb-2 border-b border-indigo-100 dark:border-slate-700">
                        <span class="text-xs font-black text-indigo-950 dark:text-indigo-300 flex items-center gap-1.5">
                            <span>⚡ Toolbar Rumus Sains & Simbol:</span>
                        </span>
                        <div class="flex items-center gap-1 text-[11px] font-bold">
                            <button type="button" @click="formulaCategory = 'math'" :class="formulaCategory === 'math' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg border border-indigo-200/60 transition">Matematika</button>
                            <button type="button" @click="formulaCategory = 'chemistry'" :class="formulaCategory === 'chemistry' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg border border-indigo-200/60 transition">Kimia</button>
                            <button type="button" @click="formulaCategory = 'physics'" :class="formulaCategory === 'physics' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg border border-indigo-200/60 transition">Fisika</button>
                            <button type="button" @click="formulaCategory = 'biology'" :class="formulaCategory === 'biology' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg border border-indigo-200/60 transition">Biologi</button>
                        </div>
                    </div>

                    <!-- Kategori Matematika -->
                    <div x-show="formulaCategory === 'math'" class="flex items-center gap-1.5 flex-wrap pt-1 text-xs">
                        <button type="button" @click="insertFormula('$\\frac{a}{b}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\frac{a}{b}$ (Pecahan)</button>
                        <button type="button" @click="insertFormula('$\\sqrt{x}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\sqrt{x}$ (Akar)</button>
                        <button type="button" @click="insertFormula('$x^2$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$x^2$ (Pangkat)</button>
                        <button type="button" @click="insertFormula('$\\int_a^b f(x) dx$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\int$ (Integral)</button>
                        <button type="button" @click="insertFormula('$\\sum_{i=1}^n$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\sum$ (Sigma)</button>
                        <button type="button" @click="insertFormula('$\\lim_{x \\to 0}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\lim$ (Limit)</button>
                        <button type="button" @click="insertFormula('$\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">Matriks $2 \times 2$</button>
                        <button type="button" @click="insertFormula('^\\circ')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$^\circ$ (Derajat)</button>
                    </div>

                    <!-- Kategori Kimia (mhchem) -->
                    <div x-show="formulaCategory === 'chemistry'" class="flex items-center gap-1.5 flex-wrap pt-1 text-xs">
                        <button type="button" @click="insertFormula('$\\ce{2H2 + O2 -> 2H2O}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\ce{2H2 + O2 -> 2H2O}$</button>
                        <button type="button" @click="insertFormula('$\\ce{H2SO4}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\ce{H2SO4}$</button>
                        <button type="button" @click="insertFormula('$\\ce{SO4^2-}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\ce{SO4^2-}$ (Ion)</button>
                        <button type="button" @click="insertFormula('$\\ce{^{14}_{6}C}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\ce{^{14}_{6}C}$ (Isotop)</button>
                        <button type="button" @click="insertFormula('$\\Delta H = -393.5\\text{ kJ/mol}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\Delta H$ (Entalpi)</button>
                        <button type="button" @click="insertFormula('$\\ce{<=>}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\ce{<=>}$ (Kesetimbangan)</button>
                    </div>

                    <!-- Kategori Fisika -->
                    <div x-show="formulaCategory === 'physics'" class="flex items-center gap-1.5 flex-wrap pt-1 text-xs">
                        <button type="button" @click="insertFormula('$\\lambda$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\lambda$ (Lambda)</button>
                        <button type="button" @click="insertFormula('$\\Omega$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\Omega$ (Ohm)</button>
                        <button type="button" @click="insertFormula('$\\mu$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\mu$ (Mu)</button>
                        <button type="button" @click="insertFormula('$\\vec{F} = m \\vec{a}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\vec{F} = m\vec{a}$ (Vektor)</button>
                        <button type="button" @click="insertFormula('$\\rho$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\rho$ (Massa Jenis)</button>
                        <button type="button" @click="insertFormula('$\\Delta t$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\Delta t$ (Delta)</button>
                        <button type="button" @click="insertFormula('$\\theta$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\theta$ (Sudut Teta)</button>
                    </div>

                    <!-- Kategori Biologi -->
                    <div x-show="formulaCategory === 'biology'" class="flex items-center gap-1.5 flex-wrap pt-1 text-xs">
                        <button type="button" @click="insertFormula('$\\male$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\male$ (Jantan)</button>
                        <button type="button" @click="insertFormula('$\\female$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\female$ (Betina)</button>
                        <button type="button" @click="insertFormula('$\\text{P}_1: \\text{BbKk} \\times \\text{BbKk}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">Persilangan Dihibrid</button>
                        <button type="button" @click="insertFormula('$\\text{F}_1$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">$\text{F}_1$ (Keturunan)</button>
                        <button type="button" @click="insertFormula('$\\text{DNA} \\rightarrow \\text{mRNA} \\rightarrow \\text{Protein}$')" class="px-2 py-1 rounded-md bg-white dark:bg-slate-800 border text-slate-700 dark:text-slate-200 font-mono hover:bg-indigo-50">Sintesis Protein</button>
                    </div>
                </div>

                <!-- Pertanyaan Soal -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Teks Pertanyaan Soal <span class="text-rose-500">*</span></label>
                    <textarea id="manual_question_text" name="question_text" x-model="questionText" @input="previewMath()" rows="3" required placeholder="Ketik teks pertanyaan atau sisipkan formula rumus di atas..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white leading-relaxed"></textarea>
                </div>

                <!-- LIVE PREVIEW RUMUS (KATEX) -->
                <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-slate-850 border border-amber-200/80 dark:border-slate-700">
                    <div class="text-[10px] font-black uppercase text-amber-800 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                        <span>👁️ Live KaTeX Formula Preview:</span>
                        <span class="text-slate-400 font-normal">(Tampilan persis yang dilihat peserta saat ujian)</span>
                    </div>
                    <div id="formula-live-preview" class="text-xs sm:text-sm font-medium text-slate-900 dark:text-white min-h-[32px] p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800" x-text="questionText || 'Preview formula rumus akan muncul di sini secara real-time saat Anda mengetik...'"></div>
                </div>

                <!-- FORM INPUT OPSI DINAMIS SESUAI TIPE SOAL -->
                <!-- 1. Pilihan Ganda -->
                <div x-show="selectedType === 'multiple_choice'" class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Pilihan Jawaban (A - E):</div>
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-1.5 shrink-0 text-xs font-black cursor-pointer">
                                <input type="radio" name="mc_correct_answer" value="{{ $opt }}" {{ $opt === 'A' ? 'checked' : '' }} class="text-emerald-600">
                                <span>{{ $opt }} (Kunci)</span>
                            </label>
                            <input type="text" name="option_{{ strtolower($opt) }}" placeholder="Teks pilihan {{ $opt }} (bisa rumus: $\frac{a}{b}$)..." class="flex-1 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                        </div>
                    @endforeach
                </div>

                <!-- 2. Pilihan Ganda Kompleks -->
                <div x-show="selectedType === 'complex_multiple_choice'" class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Pernyataan Pilihan Ganda Kompleks (Centang yang benar):</div>
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-1.5 shrink-0 text-xs font-black cursor-pointer">
                                <input type="checkbox" name="cmc_correct_answer[]" value="{{ $opt }}" class="text-emerald-600 rounded">
                                <span>{{ $opt }} (Benar)</span>
                            </label>
                            <input type="text" name="cmc_options[{{ $opt }}]" placeholder="Teks pernyataan {{ $opt }}..." class="flex-1 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                        </div>
                    @endforeach
                </div>

                <!-- 3. Benar / Salah -->
                <div x-show="selectedType === 'true_false'" class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Daftar Pernyataan Benar / Salah:</div>
                    @for($i = 0; $i < 3; $i++)
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-400">{{ $i + 1 }}.</span>
                            <input type="text" name="tf_statements[]" placeholder="Teks pernyataan {{ $i + 1 }}..." class="flex-1 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                            <select name="tf_keys[]" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                                <option value="1">BENAR</option>
                                <option value="0">SALAH</option>
                            </select>
                        </div>
                    @endfor
                </div>

                <!-- 4. Menjodohkan -->
                <div x-show="selectedType === 'matching'" class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Pasangan Menjodohkan (Premis ↔ Respon):</div>
                    @for($i = 0; $i < 3; $i++)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                            <input type="text" name="match_premises[]" placeholder="Premis kiri {{ $i + 1 }}..." class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                            <input type="text" name="match_responses[]" placeholder="Respon kanan {{ chr(65 + $i) }}..." class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                            <div class="flex items-center gap-1 text-xs font-bold">
                                <span>Pasangan:</span>
                                <input type="text" name="match_pairs[{{ $i + 1 }}]" value="{{ chr(65 + $i) }}" class="w-16 px-2 py-1 rounded bg-white dark:bg-slate-800 border text-center uppercase font-mono">
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- 5. Isian Singkat -->
                <div x-show="selectedType === 'short_answer'" class="space-y-2 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Kunci Jawaban Isian Singkat:</div>
                    <input type="text" name="short_correct_answer" placeholder="Tuliskan kata kunci jawaban yang tepat..." class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                </div>

                <!-- 6. Esai / Uraian -->
                <div x-show="selectedType === 'essay'" class="space-y-2 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Pedoman Penskoran / Rubrik Jawaban Esai:</div>
                    <textarea name="essay_rubric" rows="3" placeholder="Tuliskan rubrik penilaian dan poin kunci..." class="w-full p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs"></textarea>
                </div>

                <!-- Pembahasan -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pembahasan / Penjelasan Jawaban</label>
                    <textarea name="explanation" rows="2" placeholder="Penjelasan mengapa jawaban tersebut benar..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="manualModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30">
                        Simpan Butir Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 2: GENERATE SOAL DENGAN AI (GEMINI) -->
    <!-- ===================================================================== -->
    <div x-show="aiModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✨</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">AI Question Generator</h3>
                        <p class="text-[11px] text-slate-400">Generate paket soal otomatis berkarakter AKM & Kurikulum Merdeka</p>
                    </div>
                </div>
                <button type="button" @click="aiModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('cbt.exams.ai_generate', $exam->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Topik / Materi Pokok / Capaian Pembelajaran (CP) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="topic" required placeholder="Contoh: Stoikiometri Larutan & Reaksi Redoks" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Jumlah Soal</label>
                        <input type="number" name="count" value="5" min="1" max="20" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Fokus Kognitif</label>
                        <select name="cognitive_focus" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <option value="Campuran L1, L2, L3 (HOTS)" selected>Campuran L1, L2 & L3</option>
                            <option value="Dominan L3 (HOTS / Penalaran)">Dominan L3 (HOTS)</option>
                            <option value="Dominan L2 (Aplikasi & Perhitungan)">Dominan L2 (Aplikasi)</option>
                            <option value="L1 (Pemahaman Dasar Konsep)">L1 (Pemahaman Dasar)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Tipe Soal yang Ingin Digenerate:</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="multiple_choice" checked class="rounded text-amber-600">
                            <span>Pilihan Ganda</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="complex_multiple_choice" checked class="rounded text-amber-600">
                            <span>PG Kompleks</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="true_false" checked class="rounded text-amber-600">
                            <span>Benar / Salah</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="matching" checked class="rounded text-amber-600">
                            <span>Menjodohkan</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="short_answer" class="rounded text-amber-600">
                            <span>Isian Singkat</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="question_types[]" value="essay" class="rounded text-amber-600">
                            <span>Uraian / Esai</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Catatan Tambahan untuk AI (Opsional)</label>
                    <textarea name="additional_notes" rows="2" placeholder="Contoh: Sertakan formula reaksi kimia berformat LaTeX \\ce{...}..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="aiModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-md shadow-amber-500/30 transition flex items-center gap-1.5 cursor-pointer">
                        <span>✨ Generate Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 3: IMPORT SOAL (DENGAN DOWNLOAD CSV TEMPLATE & FILE UPLOAD) -->
    <!-- ===================================================================== -->
    <div x-show="importModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📥</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Import Butir Soal (CSV / JSON)</h3>
                        <p class="text-[11px] text-slate-400">Mendukung file CSV template Excel & formula rumus LaTeX</p>
                    </div>
                </div>
                <button type="button" @click="importModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <!-- DOWNLOAD TEMPLATE BANNER -->
            <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-3 text-xs">
                <div>
                    <span class="font-bold text-indigo-950 dark:text-indigo-200 block">Belum punya format file import?</span>
                    <span class="text-[11px] text-indigo-700 dark:text-indigo-400">Unduh template CSV yang sudah dilengkapi contoh formula rumus lengkap.</span>
                </div>
                <a href="{{ route('cbt.exams.download_template') }}" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shrink-0 shadow-xs flex items-center gap-1">
                    <span>📥 Unduh Template CSV</span>
                </a>
            </div>

            <form action="{{ route('cbt.exams.import', $exam->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilihan 1: Upload File CSV Template</label>
                    <input type="file" name="import_file" accept=".csv,text/csv,text/plain" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200">
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    <span class="flex-shrink mx-4 text-slate-400 text-[10px] font-bold uppercase">Atau Tempel JSON</span>
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilihan 2: Tempel Data JSON Format Soal</label>
                    <textarea name="import_data" rows="6" placeholder='[{"question_type": "multiple_choice", "question_text": "Hitung $\\int_0^1 x dx$", "options": {"A": "1/2", "B": "1"}, "correct_answer": "A"}]' class="w-full p-3 font-mono text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="importModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20">
                        Mulai Import Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 4: DISTRIBUSI RUANG UJIAN OTOMATIS (ZIG-ZAG / SEQUENTIAL) -->
    <!-- ===================================================================== -->
    <div x-show="distributeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🏛️</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Distribusi Peserta ke Ruang Ujian</h3>
                        <p class="text-[11px] text-slate-400">Pembagian peserta ujian antar ruang secara selang-seling rombel</p>
                    </div>
                </div>
                <button type="button" @click="distributeModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('cbt.exams.distribute_rooms', $exam->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Daftar Ruang Ujian (Pisahkan dengan koma) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="rooms" value="{{ implode(', ', $availableRooms) ?: 'Ruang 01, Ruang 02, Ruang 03' }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white">
                    <p class="text-[11px] text-slate-400">Contoh: Ruang 01, Ruang 02, Ruang 03, Lab Komputer A</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Kapasitas Maks per Ruang</label>
                        <input type="number" name="capacity" value="20" min="5" max="100" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pola Pembagian Siswa</label>
                        <select name="mode" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white">
                            <option value="zigzag" selected>Selang-Seling Rombel (Zig-Zag)</option>
                            <option value="sequential">Per Kelas / Rombel Berurutan</option>
                        </select>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-xs text-amber-900 dark:text-amber-300 space-y-1">
                    <strong class="block font-bold">💡 Apa itu Mode Zig-Zag?</strong>
                    <p class="text-[11px] leading-relaxed">
                        Jika ujian ini diikuti oleh beberapa kelas (misal Kelas 10A dan Kelas 11A), sistem akan menempatkan siswa 10A dan 11A secara selang-seling dalam satu ruang yang sama untuk meminimalkan potensi kerja sama saat ujian.
                    </p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="distributeModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-500/20">
                        ⚡ Jalankan Distribusi Ruang Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 5: CETAK DOKUMEN PER RUANG & FORMAT MANUAL (BLANKO) -->
    <!-- ===================================================================== -->
    <div x-show="printModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🖨️</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Opsi Cetak Dokumen Ujian</h3>
                        <p class="text-[11px] text-slate-400">Pilih jenis dokumen, filter ruang, serta format otomatis / manual</p>
                    </div>
                </div>
                <button type="button" @click="printModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Jenis Dokumen</label>
                    <select x-model="printDoc" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                        <option value="daftar_hadir">Daftar Hadir Ujian</option>
                        <option value="berita_acara">Berita Acara Pelaksanaan (BAP)</option>
                        <option value="kartu_peserta">Kartu Peserta Ujian (Akun & PIN)</option>
                        <option value="kartu_soal">Kartu Soal Kurikulum</option>
                        <option value="kisi_kisi">Kisi-Kisi Ujian Lengkap</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Ruang Ujian</label>
                        <select x-model="printRoom" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <option value="">-- Semua Ruang --</option>
                            @foreach($availableRooms as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Filter Rombel / Kelas</label>
                        <select x-model="printClass" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($exam->classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1" x-show="printDoc === 'daftar_hadir' || printDoc === 'berita_acara'">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Sifat Format Dokumen</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer" :class="printManual === '0' ? 'bg-indigo-50 border-indigo-500 font-bold text-indigo-900' : 'bg-slate-50 border-slate-200 text-slate-700'">
                            <input type="radio" x-model="printManual" value="0" class="text-indigo-600">
                            <span>Otomatis (Isian Data Siswa Lengkap)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer" :class="printManual === '1' ? 'bg-amber-50 border-amber-500 font-bold text-amber-900' : 'bg-slate-50 border-slate-200 text-slate-700'">
                            <input type="radio" x-model="printManual" value="1" class="text-amber-600">
                            <span>Format Manual (Blanko Kosong Tulis Tangan)</span>
                        </label>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="printModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Tutup</button>
                    <a :href="getPrintUrl()" target="_blank" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-md shadow-indigo-600/20 flex items-center gap-1.5">
                        <span>🖨️ Buka & Cetak Dokumen</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
