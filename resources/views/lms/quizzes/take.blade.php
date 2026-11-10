@extends('layouts.app')

@section('title', 'CBT Ujian: ' . $quiz->title)

@section('content')
<div class="space-y-6">
    <!-- CBT Header with Live Countdown Timer -->
    <div class="p-5 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 rounded-3xl text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-4 z-30">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-indigo-500/30 text-indigo-300">
                    CBT ONLINE • {{ $courseClass->subject->name }}
                </span>
                <span class="text-xs text-slate-300">{{ $student->name }} ({{ $student->nisn }})</span>
            </div>
            <h1 class="text-base sm:text-lg font-black text-white truncate max-w-xl">{{ $quiz->title }}</h1>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <!-- Countdown Timer Display -->
            <div class="px-4 py-2 rounded-2xl bg-slate-800/90 border border-slate-700 text-center shadow-inner">
                <div class="text-[10px] uppercase font-bold text-slate-400">Sisa Waktu</div>
                <div id="cbt-timer" class="text-xl sm:text-2xl font-black text-amber-400 font-mono tracking-wider">
                    --:--
                </div>
            </div>

            <button type="button" onclick="confirmSubmitCbt()" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition cursor-pointer">
                Selesai & Kumpulkan
            </button>
        </div>
    </div>

    <!-- CBT Main Layout: Question Flow & Quick Navigation Palette -->
    <form id="cbt-form" action="{{ route('lms.quizzes.submit', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Left: Question Flow (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                @foreach($questions as $index => $q)
                    <div id="q-card-{{ $index + 1 }}" class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                                    Soal {{ $index + 1 }} dari {{ $questions->count() }} • (Bobot {{ $q->score_weight }} Poin)
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 uppercase font-bold">{{ str_replace('_', ' ', $q->question_type) }}</span>
                        </div>

                        @if($q->stimulus)
                            <div class="p-3.5 bg-purple-50/70 dark:bg-purple-950/20 border-l-4 border-purple-600 rounded-r-2xl text-xs sm:text-sm text-purple-950 dark:text-purple-200 italic leading-relaxed">
                                <span class="font-bold text-[10px] uppercase not-italic block text-purple-700 dark:text-purple-300 mb-1">📌 Stimulus / Fenomena Kontekstual:</span>
                                {!! \App\Models\LmsQuizQuestion::renderRichText($q->stimulus) !!}
                            </div>
                        @endif

                        <!-- Question Text -->
                        <div class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white leading-relaxed">
                            {!! $q->formatted_question_text !!}
                        </div>

                        <!-- 1. PILIHAN GANDA (SINGLE CHOICE) -->
                        @if($q->question_type === 'multiple_choice' && $q->options && is_array($q->options))
                            <div class="space-y-2.5 pt-2">
                                @foreach($q->options as $optKey => $optVal)
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500/50 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer transition select-none group has-checked:border-indigo-600 has-checked:bg-indigo-50/60 dark:has-checked:bg-indigo-950/40">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optKey }}" onchange="updatePaletteStatus({{ $index + 1 }})" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                            {{ $optKey }}
                                        </span>
                                        <span class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-medium leading-snug">
                                            {!! $optVal !!}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                        <!-- 2. PILIHAN GANDA KOMPLEKS (MULTI CHOICE) -->
                        @elseif($q->question_type === 'complex_multiple_choice' && $q->options && is_array($q->options))
                            <div class="space-y-2.5 pt-2">
                                <div class="p-2 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 text-[11px] font-bold">
                                    💡 Petunjuk: Anda dapat memilih lebih dari satu pernyataan yang benar (centang seluruh opsi yang tepat).
                                </div>
                                @foreach($q->options as $optKey => $optVal)
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500/50 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer transition select-none group has-checked:border-indigo-600 has-checked:bg-indigo-50/60 dark:has-checked:bg-indigo-950/40">
                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $optKey }}" onchange="updatePaletteStatus({{ $index + 1 }})" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <span class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                            {{ $optKey }}
                                        </span>
                                        <span class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-medium leading-snug">
                                            {!! $optVal !!}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                        <!-- 3. BENAR / SALAH (TRUE / FALSE) -->
                        @elseif($q->question_type === 'true_false')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500/50 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer transition select-none group has-checked:border-emerald-600 has-checked:bg-emerald-50/60 dark:has-checked:bg-emerald-950/40">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="Benar" onchange="updatePaletteStatus({{ $index + 1 }})" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                        <span>[A]</span>
                                        <span>Pernyataan BENAR</span>
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500/50 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer transition select-none group has-checked:border-rose-600 has-checked:bg-rose-50/60 dark:has-checked:bg-rose-950/40">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="Salah" onchange="updatePaletteStatus({{ $index + 1 }})" class="w-4 h-4 text-rose-600 focus:ring-rose-500 border-slate-300">
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                        <span>[B]</span>
                                        <span>Pernyataan SALAH</span>
                                    </span>
                                </label>
                            </div>

                        <!-- 4. MENJODOHKAN (MATCHING) -->
                        @elseif($q->question_type === 'matching')
                            @php
                                $premises = $q->options['premises'] ?? [];
                                $choices = $q->options['choices'] ?? [];
                            @endphp
                            <div class="pt-2 space-y-3">
                                <div class="p-2 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300 text-[11px] font-bold">
                                    💡 Petunjuk: Pasangkan setiap premis di sebelah kiri dengan pilihan yang tepat di dropdown sebelah kanan.
                                </div>
                                <div class="space-y-3">
                                    @foreach($premises as $pKey => $pVal)
                                        <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-start gap-2 max-w-md">
                                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ $pKey }}
                                                </span>
                                                <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200">
                                                    {!! \App\Models\LmsQuizQuestion::renderRichText($pVal) !!}
                                                </span>
                                            </div>
                                            <div class="shrink-0 w-full sm:w-64">
                                                <select name="answers[{{ $q->id }}][{{ $pKey }}]" onchange="updatePaletteStatus({{ $index + 1 }})" class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white font-medium focus:border-indigo-500 outline-none">
                                                    <option value="">-- Pilih Pasangan --</option>
                                                    @foreach($choices as $cKey => $cVal)
                                                        <option value="{{ $cKey }}">({{ $cKey }}) {{ Str::limit(strip_tags($cVal), 40) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        <!-- 5. URAIAN / ISIAN SINGKAT -->
                        @elseif($q->question_type === 'short_answer')
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tuliskan Jawaban Isian Singkat Anda:</label>
                                <input type="text" name="answers[{{ $q->id }}]" oninput="updatePaletteStatus({{ $index + 1 }})" placeholder="Ketik kata kunci / istilah jawaban Anda di sini..." class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                        <!-- FALLBACK ESSAY -->
                        @else
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tuliskan Jawaban Uraian Anda:</label>
                                <textarea name="answers[{{ $q->id }}]" rows="4" oninput="updatePaletteStatus({{ $index + 1 }})" placeholder="Ketik jawaban lengkap di sini..." class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl p-3 text-slate-900 dark:text-white outline-none focus:border-indigo-500"></textarea>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Right: Question Palette / Navigator (1 Col) -->
            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4 lg:sticky lg:top-32">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-black uppercase text-slate-900 dark:text-white tracking-wider">Nomor Soal</h3>
                    <span id="answered-counter" class="text-xs font-bold text-indigo-600 dark:text-indigo-400">0 / {{ $questions->count() }} Terjawab</span>
                </div>

                <div class="grid grid-cols-5 gap-2">
                    @foreach($questions as $index => $q)
                        <button type="button" onclick="scrollToQuestion({{ $index + 1 }})" id="palette-btn-{{ $index + 1 }}" class="h-9 rounded-xl font-black text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-indigo-500 transition cursor-pointer">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2 text-[11px] text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-emerald-500 shrink-0"></span>
                        <span>Sudah Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-slate-200 dark:bg-slate-700 border border-slate-300 shrink-0"></span>
                        <span>Belum Dijawab</span>
                    </div>
                </div>

                <button type="button" onclick="confirmSubmitCbt()" class="w-full mt-2 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Selesai & Kumpulkan Jawaban
                </button>
            </div>
        </div>
    </form>
</div>

<!-- CBT Timer & Navigation Script -->
<script>
    const totalMinutes = {{ $quiz->duration_minutes }};
    let remainingSeconds = totalMinutes * 60;
    const timerDisplay = document.getElementById('cbt-timer');
    const cbtForm = document.getElementById('cbt-form');
    const totalQuestions = {{ $questions->count() }};
    const answeredMap = {};

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    const timerInterval = setInterval(() => {
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.innerText = "00:00";
            alert("Waktu ujian telah habis! Jawaban Anda akan otomatis dikumpulkan ke server.");
            cbtForm.submit();
        } else {
            timerDisplay.innerText = formatTime(remainingSeconds);
            if (remainingSeconds < 300) { // kurang dari 5 menit
                timerDisplay.classList.remove('text-amber-400');
                timerDisplay.classList.add('text-rose-500', 'animate-pulse');
            }
        }
    }, 1000);

    function scrollToQuestion(number) {
        const target = document.getElementById('q-card-' + number);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function updatePaletteStatus(number) {
        answeredMap[number] = true;
        const btn = document.getElementById('palette-btn-' + number);
        if (btn) {
            btn.classList.remove('bg-slate-50', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
            btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
        }
        const count = Object.keys(answeredMap).length;
        document.getElementById('answered-counter').innerText = count + ' / ' + totalQuestions + ' Terjawab';
    }

    function confirmSubmitCbt() {
        const count = Object.keys(answeredMap).length;
        const unanswered = totalQuestions - count;
        let msg = "Apakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian?";
        if (unanswered > 0) {
            msg += "\n\nPeringatan: Masih ada " + unanswered + " butir soal yang belum Anda jawab!";
        }
        if (confirm(msg)) {
            window.__isNormalSubmission = true;
            clearInterval(timerInterval);
            cbtForm.submit();
        }
    }
</script>

@include('lms.security.tracker_script', [
    'courseClass' => $courseClass,
    'blockableType' => 'quiz',
    'blockableId' => $quiz->id,
    'itemTitle' => $quiz->title,
])
@endsection
