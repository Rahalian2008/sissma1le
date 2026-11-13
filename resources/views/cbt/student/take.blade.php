<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CBT SMAN 1 Lengkong - {{ $exam->title }}</title>
    
    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Disable text selection and context menu during exam */
        body {
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-slate-800 dark:text-slate-100 antialiased font-sans flex flex-col justify-between overflow-x-hidden"
      x-data="cbtPlayer({
          examId: {{ $exam->id }},
          durationSeconds: {{ max(0, $session->remaining_seconds ?? ($exam->duration_minutes * 60)) }},
          isBlockedInitial: {{ $session->is_blocked ? 'true' : 'false' }},
          enableSecurity: {{ $exam->enable_security_block ? 'true' : 'false' }},
          maxViolations: {{ $exam->max_violations ?? 3 }},
          violationCount: {{ $session->violation_count ?? 0 }},
          questions: {{ Js::from($questions) }},
          initialAnswers: {{ Js::from($answers) }},
          saveUrl: '{{ route('cbt.student.save-answer', $exam->id) }}',
          violationUrl: '{{ route('cbt.student.violation', $exam->id) }}',
          finishUrl: '{{ route('cbt.student.finish', $exam->id) }}',
          csrfToken: '{{ csrf_token() }}'
      })"
      x-init="initPlayer()">

    <!-- HEADER CBT PLAYER -->
    <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm sticky top-0 z-30 px-4 sm:px-6 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <!-- INFO PESERTA & UJIAN -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center flex-shrink-0 text-sm shadow-md shadow-indigo-600/20">
                    CBT
                </div>
                <div>
                    <h1 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white leading-tight truncate max-w-[200px] sm:max-w-md">
                        {{ $exam->title }}
                    </h1>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $student->name }}</span>
                        <span>•</span>
                        <span>{{ $student->schoolClass?->name ?? 'Kelas' }}</span>
                    </div>
                </div>
            </div>

            <!-- COUNTDOWN TIMER & STATUS -->
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Status Simpan Auto-Save -->
                <div class="hidden sm:flex items-center gap-1.5 text-xs font-medium" :class="saving ? 'text-amber-500' : 'text-emerald-500'">
                    <span class="w-2 h-2 rounded-full" :class="saving ? 'bg-amber-500 animate-ping' : 'bg-emerald-500'"></span>
                    <span x-text="saving ? 'Menyimpan...' : 'Tersimpan'"></span>
                </div>

                <!-- Timer -->
                <div class="px-3.5 py-1.5 rounded-xl border font-mono font-black text-sm sm:text-base flex items-center gap-1.5 shadow-sm transition"
                     :class="remainingSeconds < 300 ? 'bg-rose-50 text-rose-600 border-rose-300 dark:bg-rose-950/40 dark:border-rose-800 animate-pulse' : 'bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-white border-slate-200 dark:border-slate-700'">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="formatTimer(remainingSeconds)"></span>
                </div>

                <!-- Tombol Fullscreen & Daftar Soal Mobile -->
                <button type="button" @click="togglePalette = !togglePalette" class="lg:hidden p-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <span class="hidden sm:inline">Daftar Soal</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN EXAM CONTAINER -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 py-6 flex-1 flex flex-col lg:flex-row gap-6">
        <!-- LEMBAR SOAL UTAMA -->
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
            <template x-if="currentQuestion">
                <div class="space-y-6">
                    <!-- HEADER NOMOR & JENIS SOAL -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-xl bg-indigo-600 text-white font-black text-sm">
                                Soal No. <span x-text="currentIndex + 1"></span>
                            </span>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400" x-text="getQuestionTypeName(currentQuestion.question_type)"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold select-none px-3 py-1.5 rounded-xl border transition"
                                   :class="isCurrentDoubtful ? 'bg-amber-50 text-amber-700 border-amber-300 dark:bg-amber-950/40 dark:border-amber-700' : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-700/60 dark:text-slate-300 dark:border-slate-600'">
                                <input type="checkbox" x-model="isCurrentDoubtful" @change="saveCurrentAnswer()" class="rounded text-amber-500 focus:ring-amber-400">
                                <span>Ragu-Ragu</span>
                            </label>
                        </div>
                    </div>

                    <!-- TEKS SOAL & MEDIA GAMBAR -->
                    <div class="space-y-4">
                        <template x-if="currentQuestion.media_path">
                            <div class="rounded-2xl overflow-hidden max-w-lg border border-slate-200 dark:border-slate-700">
                                <img :src="'/storage/' + currentQuestion.media_path" alt="Ilustrasi Soal" class="w-full h-auto object-cover max-h-96">
                            </div>
                        </template>

                        <div class="text-sm sm:text-base leading-relaxed text-slate-800 dark:text-slate-100 font-medium whitespace-pre-wrap" x-text="currentQuestion.question_text"></div>
                    </div>

                    <!-- AREA OPSI JAWABAN BERDASARKAN TIPE SOAL -->
                    <div class="pt-2">
                        <!-- 1. PILIHAN GANDA (SINGLE CHOICE) -->
                        <template x-if="currentQuestion.question_type === 'multiple_choice'">
                            <div class="space-y-2.5">
                                <template x-for="(opt, idx) in (currentQuestion.options || [])" :key="idx">
                                    <label class="flex items-start gap-3.5 p-3.5 rounded-2xl border cursor-pointer transition"
                                           :class="currentAnswer === opt.key ? 'bg-indigo-50/70 border-indigo-500 text-indigo-950 dark:bg-indigo-950/40 dark:border-indigo-500 dark:text-white shadow-sm' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70 dark:bg-slate-900/40 dark:border-slate-700 dark:hover:bg-slate-700/50 text-slate-800 dark:text-slate-200'">
                                        <input type="radio" :name="'mc_' + currentQuestion.id" :value="opt.key" x-model="currentAnswer" @change="saveCurrentAnswer()" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex items-start gap-2.5 text-xs sm:text-sm">
                                            <span class="font-black text-indigo-600 dark:text-indigo-400 w-5 flex-shrink-0" x-text="opt.key + '.'"></span>
                                            <span class="font-medium" x-text="opt.text"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- 2. PILIHAN GANDA KOMPLEKS (MULTI CHOICE CHECKBOX) -->
                        <template x-if="currentQuestion.question_type === 'complex_multiple_choice'">
                            <div class="space-y-2.5">
                                <div class="text-xs text-slate-500 italic mb-1">*Pilih satu atau lebih jawaban yang benar.</div>
                                <template x-for="(opt, idx) in (currentQuestion.options || [])" :key="idx">
                                    <label class="flex items-start gap-3.5 p-3.5 rounded-2xl border cursor-pointer transition"
                                           :class="(Array.isArray(currentAnswer) && currentAnswer.includes(opt.key)) ? 'bg-indigo-50/70 border-indigo-500 text-indigo-950 dark:bg-indigo-950/40 dark:border-indigo-500 dark:text-white shadow-sm' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70 dark:bg-slate-900/40 dark:border-slate-700 dark:hover:bg-slate-700/50 text-slate-800 dark:text-slate-200'">
                                        <input type="checkbox" :value="opt.key" :checked="Array.isArray(currentAnswer) && currentAnswer.includes(opt.key)" @change="toggleComplexOption(opt.key)" class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex items-start gap-2.5 text-xs sm:text-sm">
                                            <span class="font-black text-indigo-600 dark:text-indigo-400 w-5 flex-shrink-0" x-text="opt.key + '.'"></span>
                                            <span class="font-medium" x-text="opt.text"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- 3. BENAR / SALAH (TRUE / FALSE PER STATEMENT) -->
                        <template x-if="currentQuestion.question_type === 'true_false'">
                            <div class="space-y-3">
                                <div class="text-xs text-slate-500 italic mb-1">*Tentukan Benar atau Salah untuk setiap pernyataan di bawah ini.</div>
                                <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-2xl">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-slate-50 dark:bg-slate-900/60 font-bold border-b border-slate-200 dark:border-slate-700">
                                            <tr>
                                                <th class="py-3 px-4">Pernyataan</th>
                                                <th class="py-3 px-4 text-center w-24">Benar</th>
                                                <th class="py-3 px-4 text-center w-24">Salah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                            <template x-for="(stmt, idx) in (currentQuestion.options || [])" :key="idx">
                                                <tr>
                                                    <td class="py-3 px-4 font-medium text-slate-700 dark:text-slate-200" x-text="stmt.statement"></td>
                                                    <td class="py-3 px-4 text-center">
                                                        <input type="radio" :name="'tf_' + currentQuestion.id + '_' + idx" value="true" :checked="currentAnswer && currentAnswer[idx] === 'true'" @change="setTrueFalse(idx, 'true')" class="text-indigo-600 focus:ring-indigo-500">
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        <input type="radio" :name="'tf_' + currentQuestion.id + '_' + idx" value="false" :checked="currentAnswer && currentAnswer[idx] === 'false'" @change="setTrueFalse(idx, 'false')" class="text-indigo-600 focus:ring-indigo-500">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <!-- 4. MENJODOHKAN (MATCHING) -->
                        <template x-if="currentQuestion.question_type === 'matching'">
                            <div class="space-y-3">
                                <div class="text-xs text-slate-500 italic mb-1">*Pilih pasangan yang tepat untuk setiap premis di kolom kiri.</div>
                                <div class="space-y-2.5">
                                    <template x-for="(pair, idx) in (currentQuestion.options || [])" :key="idx">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                                            <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 sm:w-1/2" x-text="pair.premise"></span>
                                            <select :value="currentAnswer ? currentAnswer[idx] : ''" @change="setMatching(idx, $event.target.value)" class="text-xs rounded-xl bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 py-1.5 px-3 text-slate-800 dark:text-white sm:w-1/2 focus:ring-2 focus:ring-indigo-500">
                                                <option value="">-- Pilih Jawaban --</option>
                                                <template x-for="(target, tIdx) in (currentQuestion.options || [])" :key="tIdx">
                                                    <option :value="target.response" x-text="target.response"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- 5. ISIAN SINGKAT (SHORT ANSWER) -->
                        <template x-if="currentQuestion.question_type === 'short_answer'">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Ketikkan jawaban singkat Anda di bawah ini:</label>
                                <input type="text" x-model="currentAnswer" @input.debounce.500ms="saveCurrentAnswer()" placeholder="Tulis jawaban..." class="w-full text-sm rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-4 font-semibold text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </template>

                        <!-- 6. URAIAN / ESAI (ESSAY) -->
                        <template x-if="currentQuestion.question_type === 'essay'">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Tuliskan penjelasan atau uraian lengkap jawaban Anda:</label>
                                <textarea rows="6" x-model="currentAnswer" @input.debounce.600ms="saveCurrentAnswer()" placeholder="Tuliskan uraian jawaban secara jelas dan terperinci..." class="w-full text-sm rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 p-4 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 leading-relaxed"></textarea>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- FOOTER NAVIGATION TOMBOL -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3 mt-6">
                <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0" class="px-4 sm:px-5 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Sebelumnya</span>
                </button>

                <div class="flex items-center gap-2">
                    <template x-if="currentIndex < questions.length - 1">
                        <button type="button" @click="nextQuestion()" class="px-5 sm:px-6 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-md shadow-indigo-600/20">
                            <span>Selanjutnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </template>

                    <template x-if="currentIndex === questions.length - 1">
                        <button type="button" @click="showFinishModal = true" class="px-5 sm:px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Selesaikan Ujian</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- PALET NOMOR SOAL (SIDEBAR) -->
        <div class="lg:w-80 flex-shrink-0" :class="togglePalette ? 'fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 lg:static lg:bg-transparent lg:p-0' : 'hidden lg:block'">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 w-full max-w-sm lg:max-w-none space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Navigasi Nomor Soal</h3>
                    <button type="button" @click="togglePalette = false" class="lg:hidden text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <!-- INDIKATOR WARNA PALET -->
                <div class="grid grid-cols-3 gap-2 text-[10px] font-bold text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-lg bg-indigo-600"></span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-lg bg-amber-500"></span>
                        <span>Ragu-Ragu</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600"></span>
                        <span>Kosong</span>
                    </div>
                </div>

                <!-- GRID NOMOR SOAL -->
                <div class="grid grid-cols-5 gap-2.5 max-h-80 overflow-y-auto pr-1">
                    <template x-for="(q, idx) in questions" :key="q.id">
                        <button type="button" @click="goToQuestion(idx); togglePalette = false;"
                                class="h-10 rounded-xl font-bold text-xs flex items-center justify-center border transition relative"
                                :class="getPaletteClass(q, idx)">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="showFinishModal = true" class="w-full py-2.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Lembar Ujian</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL PERINGATAN PELANGGARAN KEAMANAN (ANTI-CHEAT) -->
    <div x-show="showViolationAlert" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-rose-300 dark:border-rose-800 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Peringatan Keamanan CBT!</h3>
                <p class="text-xs text-rose-600 dark:text-rose-400 font-bold mt-1" x-text="violationMessage"></p>
                <p class="text-xs text-slate-500 mt-2">
                    Pelanggaran Ke-<span class="font-bold text-slate-800 dark:text-white" x-text="violationCount"></span> dari batas toleransi <span class="font-bold text-slate-800 dark:text-white" x-text="maxViolations"></span>x. Jika melampaui batas, ujian akan otomatis terkunci!
                </p>
            </div>
            <button type="button" @click="dismissViolationAlert()" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold text-xs transition">
                Saya Mengerti & Lanjutkan Ujian
            </button>
        </div>
    </div>

    <!-- OVERLAY TERBLOKIR OTOMATIS (LOCKED STATE) -->
    <div x-show="isBlocked" x-cloak class="fixed inset-0 z-50 bg-rose-950/95 backdrop-blur-lg flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-rose-500 text-center space-y-5">
            <div class="w-20 h-20 rounded-3xl bg-rose-100 dark:bg-rose-950 text-rose-600 flex items-center justify-center mx-auto animate-bounce">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-black uppercase tracking-wider">Akses Terkunci Otomatis</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-2">Ujian Anda Telah Terblokir</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                    Sistem mendeteksi bahwa Anda telah berpindah tab, meminimalkan aplikasi, atau keluar dari layar ujian sebanyak <span class="font-bold text-rose-600" x-text="violationCount"></span> kali.
                </p>
                <div class="bg-rose-50 dark:bg-rose-950/40 p-3.5 rounded-2xl border border-rose-200 dark:border-rose-900 text-xs text-rose-800 dark:text-rose-200 font-semibold mt-3">
                    Silakan lapor kepada Pengawas Ruang Ujian untuk verifikasi dan pembukaan blokir secara langsung dari menu monitoring.
                </div>
            </div>
            <div class="pt-2">
                <button type="button" onclick="window.location.reload()" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold text-xs transition">
                    Cek Status Pembukaan Blokir
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI SELESAI UJIAN -->
    <div x-show="showFinishModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 text-left space-y-4" @click.away="showFinishModal = false">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Konfirmasi Selesaikan Ujian</h3>
                    <p class="text-xs text-slate-500">Apakah Anda yakin ingin mengakhiri sesi ujian ini?</p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Butir Soal:</span>
                    <strong class="font-bold text-slate-800 dark:text-white" x-text="questions.length"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-600 font-semibold">Sudah Dijawab:</span>
                    <strong class="font-bold text-emerald-600" x-text="countAnswered()"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-amber-600 font-semibold">Ditandai Ragu-Ragu:</span>
                    <strong class="font-bold text-amber-600" x-text="countDoubtful()"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-rose-500 font-semibold">Belum Dijawab:</span>
                    <strong class="font-bold text-rose-500" x-text="questions.length - countAnswered()"></strong>
                </div>
            </div>

            <form method="POST" :action="finishUrl" class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                @csrf
                <button type="button" @click="showFinishModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                    Periksa Kembali
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition">
                    Ya, Selesaikan & Kirim
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIKA CBT INTERAKTIF & ANTI-CHEAT -->
    <script>
        function cbtPlayer(config) {
            return {
                examId: config.examId,
                remainingSeconds: config.durationSeconds,
                isBlocked: config.isBlockedInitial,
                enableSecurity: config.enableSecurity,
                maxViolations: config.maxViolations,
                violationCount: config.violationCount,
                questions: config.questions || [],
                answersMap: config.initialAnswers || {},
                saveUrl: config.saveUrl,
                violationUrl: config.violationUrl,
                finishUrl: config.finishUrl,
                csrfToken: config.csrfToken,

                currentIndex: 0,
                saving: false,
                togglePalette: false,
                showFinishModal: false,
                showViolationAlert: false,
                violationMessage: '',
                timerInterval: null,

                get currentQuestion() {
                    return this.questions[this.currentIndex] || null;
                },

                get currentAnswer() {
                    if (!this.currentQuestion) return null;
                    const ansObj = this.answersMap[this.currentQuestion.id];
                    return ansObj ? ansObj.answer : null;
                },

                set currentAnswer(val) {
                    if (!this.currentQuestion) return;
                    if (!this.answersMap[this.currentQuestion.id]) {
                        this.answersMap[this.currentQuestion.id] = { answer: null, is_doubtful: false };
                    }
                    this.answersMap[this.currentQuestion.id].answer = val;
                },

                get isCurrentDoubtful() {
                    if (!this.currentQuestion) return false;
                    const ansObj = this.answersMap[this.currentQuestion.id];
                    return ansObj ? !!ansObj.is_doubtful : false;
                },

                set isCurrentDoubtful(val) {
                    if (!this.currentQuestion) return;
                    if (!this.answersMap[this.currentQuestion.id]) {
                        this.answersMap[this.currentQuestion.id] = { answer: null, is_doubtful: false };
                    }
                    this.answersMap[this.currentQuestion.id].is_doubtful = val;
                },

                initPlayer() {
                    this.startTimer();
                    this.setupAntiCheat();
                    this.requestFullscreen();
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                            // Sync setiap 60 detik
                            if (this.remainingSeconds % 60 === 0) {
                                this.saveCurrentAnswer(true);
                            }
                        } else {
                            clearInterval(this.timerInterval);
                            alert('Waktu ujian telah habis! Sistem akan mengirimkan jawaban Anda secara otomatis.');
                            document.querySelector('form[action="' + this.finishUrl + '"]').submit();
                        }
                    }, 1000);
                },

                formatTimer(sec) {
                    const h = Math.floor(sec / 3600);
                    const m = Math.floor((sec % 3600) / 60);
                    const s = sec % 60;
                    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },

                goToQuestion(index) {
                    if (index >= 0 && index < this.questions.length) {
                        this.currentIndex = index;
                    }
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                getQuestionTypeName(type) {
                    const names = {
                        'multiple_choice': 'Pilihan Ganda',
                        'complex_multiple_choice': 'Pilihan Ganda Kompleks',
                        'true_false': 'Benar / Salah',
                        'matching': 'Menjodohkan',
                        'short_answer': 'Isian Singkat',
                        'essay': 'Uraian / Esai'
                    };
                    return names[type] || 'Soal';
                },

                toggleComplexOption(key) {
                    let cur = Array.isArray(this.currentAnswer) ? [...this.currentAnswer] : [];
                    const pos = cur.indexOf(key);
                    if (pos > -1) {
                        cur.splice(pos, 1);
                    } else {
                        cur.push(key);
                    }
                    this.currentAnswer = cur;
                    this.saveCurrentAnswer();
                },

                setTrueFalse(statementIdx, val) {
                    let cur = (this.currentAnswer && typeof this.currentAnswer === 'object') ? {...this.currentAnswer} : {};
                    cur[statementIdx] = val;
                    this.currentAnswer = cur;
                    this.saveCurrentAnswer();
                },

                setMatching(premiseIdx, responseVal) {
                    let cur = (this.currentAnswer && typeof this.currentAnswer === 'object') ? {...this.currentAnswer} : {};
                    cur[premiseIdx] = responseVal;
                    this.currentAnswer = cur;
                    this.saveCurrentAnswer();
                },

                saveCurrentAnswer(silent = false) {
                    if (!this.currentQuestion) return;
                    if (!silent) this.saving = true;

                    fetch(this.saveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: this.currentQuestion.id,
                            answer: this.currentAnswer,
                            is_doubtful: this.isCurrentDoubtful,
                            remaining_seconds: this.remainingSeconds
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.saving = false;
                        if (data.is_blocked) {
                            this.isBlocked = true;
                        }
                    })
                    .catch(() => {
                        this.saving = false;
                    });
                },

                countAnswered() {
                    let count = 0;
                    for (const q of this.questions) {
                        const ansObj = this.answersMap[q.id];
                        if (ansObj && ansObj.answer !== null && ansObj.answer !== '' && (!Array.isArray(ansObj.answer) || ansObj.answer.length > 0)) {
                            count++;
                        }
                    }
                    return count;
                },

                countDoubtful() {
                    let count = 0;
                    for (const q of this.questions) {
                        const ansObj = this.answersMap[q.id];
                        if (ansObj && ansObj.is_doubtful) {
                            count++;
                        }
                    }
                    return count;
                },

                getPaletteClass(q, idx) {
                    const isCurrent = this.currentIndex === idx;
                    const ansObj = this.answersMap[q.id];
                    const hasAnswer = ansObj && ansObj.answer !== null && ansObj.answer !== '' && (!Array.isArray(ansObj.answer) || ansObj.answer.length > 0);
                    const isDoubtful = ansObj && ansObj.is_doubtful;

                    let base = '';
                    if (isDoubtful) {
                        base = 'bg-amber-500 text-white border-amber-600';
                    } else if (hasAnswer) {
                        base = 'bg-indigo-600 text-white border-indigo-700';
                    } else {
                        base = 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
                    }

                    if (isCurrent) {
                        base += ' ring-2 ring-offset-2 ring-indigo-500 dark:ring-offset-slate-900 font-black';
                    }

                    return base;
                },

                setupAntiCheat() {
                    if (!this.enableSecurity) return;

                    // 1. Deteksi Window Blur / Tab Switch
                    window.addEventListener('blur', () => {
                        this.handleViolation('tab_switch', 'Meninggalkan jendela atau berpindah tab');
                    });

                    // 2. Deteksi Document Visibility Change
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.handleViolation('tab_switch', 'Membuka tab lain atau meminimalkan browser');
                        }
                    });

                    // 3. Cegah Shortcut keyboard (F11, F12, Ctrl+W, Ctrl+R, Alt+Tab, etc.)
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.key === 'u')) {
                            e.preventDefault();
                            this.handleViolation('devtools', 'Mencoba membuka inspect element / developer tools');
                        }
                        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                            e.preventDefault();
                        }
                    });

                    // 4. Disable Right Click Context Menu
                    window.addEventListener('contextmenu', (e) => e.preventDefault());
                },

                handleViolation(type, description) {
                    if (this.isBlocked) return;

                    fetch(this.violationUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            type: type,
                            description: description
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.violationCount = data.violation_count;
                            this.violationMessage = data.message;
                            if (data.is_blocked) {
                                this.isBlocked = true;
                                this.showViolationAlert = false;
                            } else {
                                this.showViolationAlert = true;
                            }
                        }
                    })
                    .catch(() => {});
                },

                dismissViolationAlert() {
                    this.showViolationAlert = false;
                    this.requestFullscreen();
                },

                requestFullscreen() {
                    const docEl = document.documentElement;
                    if (docEl.requestFullscreen && !document.fullscreenElement) {
                        docEl.requestFullscreen().catch(() => {});
                    }
                }
            };
        }
    </script>
</body>
</html>
