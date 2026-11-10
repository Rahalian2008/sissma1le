@extends('layouts.app')

@section('title', 'Mengerjakan Asesmen: ' . $test->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">BK</a>
            <span>/</span>
            <a href="{{ route('bk.psychological.index') }}" class="hover:text-emerald-600 font-medium">Tes Psikotes</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $test->name }}</span>
        </div>
        <a href="{{ route('bk.psychological.index') }}" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" onclick="return confirm('Apakah Anda yakin ingin membatalkan pengisian tes ini?')">
            Batalkan Tes
        </a>
    </div>

    <!-- Header Card -->
    <div class="p-6 rounded-3xl bg-linear-to-r from-emerald-600 via-teal-600 to-cyan-700 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                    {{ $test->category }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/20 text-xs font-semibold backdrop-blur-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Estimasi: ± {{ $test->duration_minutes }} Menit
                </span>
            </div>

            <h1 class="text-2xl font-black">{{ $test->name }}</h1>
            <p class="text-xs text-emerald-100 max-w-2xl leading-relaxed">
                {{ $test->short_desc }}
            </p>

            <div class="pt-3 border-t border-white/20 flex flex-wrap items-center justify-between gap-4 text-xs text-emerald-50">
                <div class="flex flex-wrap items-center gap-4">
                    <div>Nama Siswa: <b class="text-white">{{ $student->name }}</b></div>
                    <div>NISN: <b class="text-white">{{ $student->nisn }}</b></div>
                    <div>Kelas: <b class="text-white">{{ $student->schoolClass->name ?? '-' }}</b></div>
                    <div>Total Soal: <b class="text-white">{{ $test->questions->count() }} Butir</b></div>
                </div>

                @if(!empty($isAdminOrCounselor) && $availableStudents->isNotEmpty())
                    <form method="GET" action="{{ route('bk.psychological.take', $test->slug) }}" class="flex items-center gap-2">
                        <span class="text-[11px] text-emerald-200">Ganti Siswa:</span>
                        <select name="student_id" onchange="this.form.submit()" class="px-2.5 py-1 text-xs rounded-xl bg-white/20 text-white border border-white/30 backdrop-blur-md focus:bg-slate-900 focus:text-white">
                            @foreach($availableStudents as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $student->id ? 'selected' : '' }} class="text-slate-900 dark:text-white dark:bg-slate-900">
                                    {{ $st->name }} ({{ $st->schoolClass->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    @if(!empty($isAdminOrCounselor))
        <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-900 dark:text-teal-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 font-medium">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-pulse"></span>
                <span>Mode Konselor BK / Admin: Asesmen langsung atas nama <strong>{{ $student->name }}</strong></span>
            </div>
            <button type="button" onclick="autoFillSimulation()" class="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition cursor-pointer self-start sm:self-auto">
                ⚡ Simulasi Isi Cepat (Testing)
            </button>
        </div>
    @endif

    <!-- Petunjuk Pengerjaan -->
    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs space-y-1.5">
        <div class="flex items-center gap-2 font-bold">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Petunjuk Pengisian:</span>
        </div>
        <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-300">
            {{ $test->instructions ?? 'Bacalah setiap pernyataan dengan seksama. Pilihlah salah satu opsi jawaban yang paling mencerminkan kondisi diri Anda yang sebenarnya. Tidak ada jawaban yang dianggap salah atau keliru, kejujuran Anda sangat menentukan ketepatan hasil analisis psikologis.' }}
        </p>
    </div>

    <!-- Sticky Progress Tracker -->
    <div class="sticky top-20 z-20 p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 shadow-md">
        <div class="flex items-center justify-between text-xs font-bold mb-2">
            <span class="text-slate-700 dark:text-slate-200">Progress Pengerjaan:</span>
            <span id="progress-text" class="text-teal-600 dark:text-teal-400 font-mono">0 / {{ $test->questions->count() }} Terjawab (0%)</span>
        </div>
        <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
            <div id="progress-bar" class="h-full bg-linear-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Form Kuesioner Soal -->
    <form id="psikotes-form" action="{{ route('bk.psychological.submit', $test->slug) }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">

        @php
            $scaleLabels = match($test->slug) {
                'riasec' => [
                    1 => 'Sangat Tidak Suka',
                    2 => 'Kurang Suka',
                    3 => 'Suka / Tertarik',
                    4 => 'Sangat Suka',
                ],
                'gardner' => [
                    1 => 'Sangat Tidak Sesuai',
                    2 => 'Kurang Sesuai',
                    3 => 'Cukup Sesuai',
                    4 => 'Sangat Menggambarkan Diri',
                ],
                default => [ // VAK
                    1 => 'Sangat Jarang / Tidak Pernah',
                    2 => 'Kadang-kadang',
                    3 => 'Sering Mengalami',
                    4 => 'Selalu / Sangat Sesuai',
                ]
            };
        @endphp

        <div class="space-y-4">
            @foreach($test->questions as $index => $q)
                <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs question-card transition hover:border-teal-300 dark:hover:border-teal-700" id="card-q-{{ $q->id }}">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="shrink-0 w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                            {{ $q->question_number }}
                        </span>
                        <div class="grow">
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 leading-relaxed">
                                {{ $q->question_text }}
                            </p>
                        </div>
                    </div>

                    <!-- Skala Opsi Jawaban Radio 1-4 -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                        @foreach([1, 2, 3, 4] as $val)
                            <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-teal-50 dark:hover:bg-teal-950/30 hover:border-teal-400 cursor-pointer transition select-none text-center group">
                                <input type="radio" 
                                       name="answers[{{ $q->id }}]" 
                                       value="{{ $val }}" 
                                       required 
                                       class="sr-only test-radio" 
                                       data-question-id="{{ $q->id }}"
                                       onchange="updateProgress(); highlightSelected(this);">
                                <span class="w-6 h-6 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center font-bold text-xs text-slate-600 dark:text-slate-300 group-hover:border-teal-500 mb-1.5 radio-badge transition">
                                    {{ $val }}
                                </span>
                                <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300 radio-text">
                                    {{ $scaleLabels[$val] ?? "Skor $val" }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Button Box -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg text-center space-y-3">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pemeriksaan Akhir & Evaluasi Otomatis</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Pastikan semua pernyataan telah dijawab sebelum mengirimkan tes. Sistem akan memproses algoritma penskoran secara langsung.
            </p>
            <div class="pt-2">
                <button type="submit" id="btn-submit" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-600/30 transition transform active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim & Selesaikan Asesmen</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const totalQuestions = {{ $test->questions->count() }};

    function updateProgress() {
        const answeredQuestions = new Set();
        document.querySelectorAll('.test-radio:checked').forEach(r => {
            answeredQuestions.add(r.getAttribute('data-question-id'));
        });

        const count = answeredQuestions.size;
        const percentage = Math.round((count / totalQuestions) * 100);

        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');

        if (progressBar) progressBar.style.width = percentage + '%';
        if (progressText) progressText.innerText = count + ' / ' + totalQuestions + ' Terjawab (' + percentage + '%)';
    }

    function highlightSelected(input) {
        const parentCard = document.getElementById('card-q-' + input.getAttribute('data-question-id'));
        if (parentCard) {
            parentCard.classList.remove('border-slate-200', 'dark:border-slate-800');
            parentCard.classList.add('border-emerald-400', 'dark:border-emerald-600', 'bg-emerald-50/20');
        }

        // Reset sibling radio labels
        const labels = parentCard.querySelectorAll('label');
        labels.forEach(lbl => {
            lbl.classList.remove('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-sm');
            lbl.classList.add('bg-slate-50/50', 'dark:bg-slate-800/40', 'border-slate-200', 'dark:border-slate-800');
            const badge = lbl.querySelector('.radio-badge');
            if (badge) {
                badge.classList.remove('bg-white', 'text-teal-700', 'border-white');
                badge.classList.add('border-slate-300', 'dark:border-slate-600', 'text-slate-600', 'dark:text-slate-300');
            }
            const text = lbl.querySelector('.radio-text');
            if (text) {
                text.classList.remove('text-white', 'font-bold');
                text.classList.add('text-slate-600', 'dark:text-slate-300');
            }
        });

        // Highlight selected
        const chosenLabel = input.closest('label');
        if (chosenLabel) {
            chosenLabel.classList.remove('bg-slate-50/50', 'dark:bg-slate-800/40', 'border-slate-200', 'dark:border-slate-800');
            chosenLabel.classList.add('bg-teal-600', 'text-white', 'border-teal-600', 'shadow-sm');
            const badge = chosenLabel.querySelector('.radio-badge');
            if (badge) {
                badge.classList.add('bg-white', 'text-teal-700', 'border-white');
                badge.classList.remove('border-slate-300', 'dark:border-slate-600', 'text-slate-600', 'dark:text-slate-300');
            }
            const text = chosenLabel.querySelector('.radio-text');
            if (text) {
                text.classList.add('text-white', 'font-bold');
                text.classList.remove('text-slate-600', 'dark:text-slate-300');
            }
        }
    }

    function autoFillSimulation() {
        const questionCards = document.querySelectorAll('.question-card');
        questionCards.forEach(card => {
            const radios = card.querySelectorAll('.test-radio');
            if (radios.length > 0) {
                // Beri bobot acak realistis antara 2, 3, dan 4
                const options = [2, 3, 3, 4, 4];
                const targetScore = options[Math.floor(Math.random() * options.length)];
                let matchedRadio = Array.from(radios).find(r => parseInt(r.value) === targetScore) || radios[radios.length - 1];
                matchedRadio.checked = true;
                highlightSelected(matchedRadio);
            }
        });
        updateProgress();
    }

    document.getElementById('psikotes-form')?.addEventListener('submit', function(e) {
        const answeredCount = new Set(Array.from(document.querySelectorAll('.test-radio:checked')).map(r => r.getAttribute('data-question-id'))).size;
        if (answeredCount < totalQuestions) {
            e.preventDefault();
            alert('Perhatian: Anda baru menjawab ' + answeredCount + ' dari ' + totalQuestions + ' soal. Mohon lengkapi semua soal sebelum mengirimkan jawaban.');
            return false;
        }
        if (!confirm('Apakah Anda sudah yakin dengan semua jawaban Anda? Hasil evaluasi akan langsung disimpan.')) {
            e.preventDefault();
            return false;
        }
    });

    // Inisialisasi progress awal jika ada nilai terpilih
    updateProgress();
</script>
@endsection

