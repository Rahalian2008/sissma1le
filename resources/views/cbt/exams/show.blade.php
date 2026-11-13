@extends('layouts.app')

@section('title', "{$exam->title} - Manajer Soal CBT")

@section('content')
<div class="space-y-6" x-data="{ 
    manualModal: false, 
    aiModal: false, 
    importModal: false,
    selectedType: 'multiple_choice' 
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
                    @if(!empty($exam->token))
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-mono font-black bg-amber-500/20 text-amber-300 border border-amber-400/30 tracking-widest">
                            🔑 TOKEN: {{ $exam->token }}
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
            <span class="font-bold text-slate-300">🖨️ Cetak Dokumen Resmi:</span>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('cbt.documents.kisi_kisi', $exam->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition flex items-center gap-1">
                    <span>📑 Kisi-Kisi Soal</span>
                </a>
                <a href="{{ route('cbt.documents.kartu_soal', $exam->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition flex items-center gap-1">
                    <span>🎴 Kartu Soal</span>
                </a>
                <a href="{{ route('cbt.documents.kartu_peserta', $exam->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30 font-bold transition flex items-center gap-1">
                    <span>🪪 Kartu Peserta (Akun & PIN)</span>
                </a>
                <a href="{{ route('cbt.documents.daftar_hadir', $exam->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition flex items-center gap-1">
                    <span>📋 Daftar Hadir</span>
                </a>
                <a href="{{ route('cbt.documents.berita_acara', $exam->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold transition flex items-center gap-1">
                    <span>📜 Berita Acara (BAP)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS: TAMBAH SOAL MANUAL, AI & IMPORT -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="text-xs text-slate-600 dark:text-slate-300 font-bold">
            Daftar Butir Soal (<span class="text-indigo-600 dark:text-indigo-400">{{ $exam->questions->count() }}</span> Butir Terdaftar)
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" @click="manualModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>➕ Tambah Soal Manual</span>
            </button>

            <button type="button" @click="aiModal = true" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>✨ Generate Soal AI</span>
            </button>

            <button type="button" @click="importModal = true" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1 cursor-pointer">
                <span>📥 Import JSON</span>
            </button>
        </div>
    </div>

    <!-- LIST OF QUESTIONS -->
    @if($exam->questions->isEmpty())
        <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <div class="text-5xl">📄</div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Belum Ada Soal di Paket Ini</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Silakan tambahkan butir soal secara manual, gunakan bantuan AI Gemini untuk generate otomatis berstandar Kemdikbud, atau import data soal.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($exam->questions as $q)
                <div class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <!-- Header Soal: Nomor, Tipe, Level Kognitif, Kesukaran, Bobot -->
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

                    <!-- Capaian Pembelajaran & Indikator Soal (Untuk Kisi-Kisi) -->
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

                    <!-- Teks Soal -->
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                        {!! nl2br(e($q->question_text)) !!}
                    </div>

                    <!-- TAMPILAN OPSI & KUNCI JAWABAN BERDASARKAN TIPE SOAL -->
                    <div class="pt-2">
                        @if($q->question_type === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($q->options ?? [] as $key => $optText)
                                    @php $isCorrect = (string)$key === (string)$q->correct_answer; @endphp
                                    <div class="flex items-start gap-2.5 p-3 rounded-xl border text-xs {{ $isCorrect ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500/40 text-emerald-900 dark:text-emerald-200 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }}">
                                        <span class="w-6 h-6 rounded-lg {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} font-black text-xs flex items-center justify-center shrink-0">
                                            {{ $key }}
                                        </span>
                                        <div class="flex-1 pt-0.5">{{ $optText }}</div>
                                        @if($isCorrect)
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase">
                                                Kunci Jawaban ✓
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($q->question_type === 'complex_multiple_choice')
                            <div class="space-y-2">
                                @php $correctArray = is_array($q->correct_answer) ? $q->correct_answer : []; @endphp
                                @foreach($q->options ?? [] as $key => $optText)
                                    @php $isCorrect = in_array((string)$key, array_map('strval', $correctArray)); @endphp
                                    <div class="flex items-start gap-2.5 p-3 rounded-xl border text-xs {{ $isCorrect ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500/40 text-emerald-900 dark:text-emerald-200 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }}">
                                        <span class="w-6 h-6 rounded-lg {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} font-black text-xs flex items-center justify-center shrink-0">
                                            ☑
                                        </span>
                                        <div class="flex-1 pt-0.5"><strong>[{{ $key }}]</strong> {{ $optText }}</div>
                                        @if($isCorrect)
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase">
                                                Benar ✓
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($q->question_type === 'true_false')
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            <th class="p-2.5 rounded-l-xl">No</th>
                                            <th class="p-2.5">Pernyataan</th>
                                            <th class="p-2.5 text-center rounded-r-xl">Kunci Kebenaran</th>
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
                                        @foreach($q->options ?? [] as $idx => $st)
                                            @php $isTrue = $keysMap[$st['id'] ?? ''] ?? false; @endphp
                                            <tr>
                                                <td class="p-2.5 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                                <td class="p-2.5 font-medium text-slate-800 dark:text-slate-200">{{ $st['statement'] ?? '' }}</td>
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
                                    @foreach($q->options['premises'] ?? [] as $p)
                                        <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-medium">
                                            <strong>{{ $p['id'] }}.</strong> {{ $p['text'] }}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-2">
                                    <div class="font-black text-amber-600 dark:text-amber-400">Respon (Kolom Kanan):</div>
                                    @foreach($q->options['responses'] ?? [] as $r)
                                        <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-medium">
                                            <strong>{{ $r['id'] }}.</strong> {{ $r['text'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-2 p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-500/30 text-xs text-emerald-800 dark:text-emerald-300">
                                <strong>Kunci Pasangan:</strong>
                                @if(is_array($q->correct_answer))
                                    @foreach($q->correct_answer as $pair)
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-emerald-500/20 font-bold ml-1">
                                            {{ $pair['premise_id'] ?? '' }} ↔ {{ $pair['response_id'] ?? '' }}
                                        </span>
                                    @endforeach
                                @endif
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
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- MODAL 1: TAMBAH SOAL MANUAL -->
    <!-- ===================================================================== -->
    <div x-show="manualModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-3xl rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>➕</span>
                    <span>Tambah Butir Soal Baru</span>
                </h3>
                <button type="button" @click="manualModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('cbt.exams.questions.store', $exam->id) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Pilihan Tipe Soal -->
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

                <!-- Pertanyaan Soal -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Teks Pertanyaan Soal <span class="text-rose-500">*</span></label>
                    <textarea name="question_text" rows="3" required placeholder="Tuliskan pertanyaan instruksional..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold"></textarea>
                </div>

                <!-- FORM INPUT DINAMIS SESUAI TIPE SOAL -->
                <!-- 1. Pilihan Ganda -->
                <div x-show="selectedType === 'multiple_choice'" class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400">Pilihan Jawaban (Pilihan Ganda A - E):</div>
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-1.5 shrink-0 text-xs font-black cursor-pointer">
                                <input type="radio" name="mc_correct_answer" value="{{ $opt }}" {{ $opt === 'A' ? 'checked' : '' }} class="text-emerald-600">
                                <span>{{ $opt }} (Kunci)</span>
                            </label>
                            <input type="text" name="option_{{ strtolower($opt) }}" placeholder="Teks pilihan {{ $opt }}..." class="flex-1 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
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
                    <input type="text" name="topic" required placeholder="Contoh: Trigonometri & Penerapannya Dalam Kehidupan Nyata" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
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
                    <textarea name="additional_notes" rows="2" placeholder="Contoh: Berikan studi kasus berbasis kearifan lokal Jawa Barat..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs"></textarea>
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
    <!-- MODAL 3: IMPORT SOAL (JSON) -->
    <!-- ===================================================================== -->
    <div x-show="importModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📥</span>
                    <span>Import Soal dari JSON</span>
                </h3>
                <button type="button" @click="importModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('cbt.exams.import', $exam->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Tempel Data JSON Soal:</label>
                    <textarea name="import_data" rows="10" required placeholder='[{"question_type": "multiple_choice", "question_text": "Pertanyaan...", "options": {"A": "opsi A", "B": "opsi B"}, "correct_answer": "A", "score_weight": 1.0}]' class="w-full p-3 font-mono text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="importModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-extrabold text-xs shadow-md">
                        Mulai Import Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
