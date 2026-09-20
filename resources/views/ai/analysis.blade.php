@extends('layouts.app')

@section('title', 'AI Analisis Perkembangan Siswa')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header AI -->
    <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-800/40 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-pink-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-bold border border-indigo-500/30 mb-2">
                    <span class="w-2 h-2 rounded-full bg-pink-400 animate-pulse"></span>
                    SINTESIS KECERDASAN BUATAN
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white">AI Analisis Perkembangan Siswa</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-xl leading-relaxed">
                    Sistem menganalisis 5 pilar data siswa (Presensi, Prestasi, Pelanggaran, Pembinaan, dan Jurnal 7 Kebiasaan) untuk menghasilkan kesimpulan pola objektif dan saran pendampingan terarah.
                </p>
            </div>

            <!-- Student Switcher for Staff with Search & Initials -->
            @if(in_array(Auth::user()->role, ['guru', 'wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin']) && $allStudents->isNotEmpty())
                <div class="bg-slate-900/80 p-3 rounded-2xl border border-slate-800 shrink-0 flex flex-col items-start gap-1.5">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Siswa yang Dianalisis:</label>
                    <button type="button" onclick="openAiStudentModal()"
                        class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold border border-slate-700 outline-none cursor-pointer flex items-center gap-2 transition">
                        <span>🔍 {{ $student->name ?? 'Pilih Siswa' }}</span>
                        <span class="text-indigo-400 font-extrabold text-[10px]">GANTI ▼</span>
                    </button>
                </div>

                <!-- Modal Search & Initials Siswa untuk AI Analysis -->
                <div id="ai-student-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl relative text-left">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
                            <div>
                                <h3 class="font-black text-sm sm:text-base text-white">Pilih Siswa untuk Analisis AI</h3>
                                <p class="text-[11px] text-slate-400">Ketik nama/NIS atau klik inisial siswa</p>
                            </div>
                            <button type="button" onclick="closeAiStudentModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-sm font-bold flex items-center justify-center transition">
                                ✕
                            </button>
                        </div>

                        <!-- Search Input -->
                        <div class="relative mb-2.5">
                            <input type="text" id="ai_student_search_input"
                                placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                                autocomplete="off"
                                oninput="filterAiStudents(this.value)"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 outline-none focus:border-indigo-500 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Initials Filter Bar (A-Z) -->
                        <div class="flex items-center gap-1 overflow-x-auto pb-2 mb-2 no-scrollbar text-xs" id="ai-initials-bar">
                            <button type="button" onclick="filterAiByInitial('ALL')"
                                class="ai-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition" data-letter="ALL">
                                Semua
                            </button>
                            @foreach(range('A', 'Z') as $char)
                                <button type="button" onclick="filterAiByInitial('{{ $char }}')"
                                    class="ai-initial-chip px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-700" data-letter="{{ $char }}">
                                    {{ $char }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Student List -->
                        <div id="ai-student-list" class="max-h-72 overflow-y-auto rounded-2xl border border-slate-800 divide-y divide-slate-800">
                            @foreach($allStudents as $st)
                                @php
                                    $words = explode(' ', trim($st->name));
                                    $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                                    $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                                    $isSelected = ($student && $student->id === $st->id);
                                @endphp
                                <a href="{{ route('ai.analysis', ['student_id' => $st->id]) }}"
                                   class="ai-student-item px-3.5 py-2.5 hover:bg-slate-800 cursor-pointer flex items-center justify-between transition {{ $isSelected ? 'bg-indigo-950/60 font-bold' : '' }}"
                                   data-name="{{ $st->name }}"
                                   data-nis="{{ $st->nis }}"
                                   data-initials="{{ strtolower($initials) }}"
                                   data-firstletter="{{ $firstChar }}">
                                    <div class="flex items-center gap-2.5 truncate mr-2">
                                        <span class="w-8 h-8 rounded-lg {{ $isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300' }} font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($initials, 0, 2)) }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-white truncate text-xs">{{ $st->name }}</div>
                                            <div class="text-[10px] text-slate-400">Kelas {{ $st->schoolClass?->name ?? 'Umum' }} • NIS: {{ $st->nis }}</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 font-mono shrink-0 font-bold">
                                        [{{ strtoupper($initials) }}]
                                    </span>
                                </a>
                            @endforeach
                            <div id="ai-student-empty" class="hidden p-6 text-center text-xs text-slate-400 italic">
                                Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    let activeAiInitial = 'ALL';

                    function openAiStudentModal() {
                        const modal = document.getElementById('ai-student-modal');
                        if (modal) {
                            modal.classList.remove('hidden');
                            const input = document.getElementById('ai_student_search_input');
                            if (input) setTimeout(() => input.focus(), 50);
                        }
                    }

                    function closeAiStudentModal() {
                        const modal = document.getElementById('ai-student-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                        }
                    }

                    function filterAiByInitial(letter) {
                        activeAiInitial = letter;
                        document.querySelectorAll('.ai-initial-chip').forEach(btn => {
                            if (btn.getAttribute('data-letter') === letter) {
                                btn.className = 'ai-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
                            } else {
                                btn.className = 'ai-initial-chip px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-700';
                            }
                        });
                        applyAiFilters();
                    }

                    function filterAiStudents(keyword) {
                        applyAiFilters();
                    }

                    function applyAiFilters() {
                        const keyword = (document.getElementById('ai_student_search_input')?.value || '').toLowerCase().trim();
                        const items = document.querySelectorAll('.ai-student-item');
                        let visibleCount = 0;

                        items.forEach(item => {
                            const name = (item.getAttribute('data-name') || '').toLowerCase();
                            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
                            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
                            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

                            const matchInitial = (activeAiInitial === 'ALL') || (firstLetter === activeAiInitial);
                            const matchKeyword = !keyword || name.includes(keyword) || nis.includes(keyword) || initials.includes(keyword);

                            if (matchInitial && matchKeyword) {
                                item.style.display = 'flex';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        const emptyNotice = document.getElementById('ai-student-empty');
                        if (emptyNotice) {
                            emptyNotice.classList.toggle('hidden', visibleCount > 0);
                        }
                    }
                </script>
            @endif
        </div>
    </div>

    @if($analysis)
    <!-- Status Engine AI (Google Gemini / Offline Heuristic) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl {{ $analysis['is_ai_generated'] ? 'bg-gradient-to-r from-indigo-950 via-slate-900 to-purple-950 text-white border border-indigo-500/40 shadow-md' : 'bg-amber-50 text-amber-900 border border-amber-200' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0 {{ $analysis['is_ai_generated'] ? 'bg-indigo-500/30 border border-indigo-400/40 text-indigo-300' : 'bg-amber-100 text-amber-700' }}">
                {{ $analysis['is_ai_generated'] ? '✨' : '⚙️' }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-extrabold text-xs sm:text-sm">
                        {{ $analysis['provider'] }}
                    </span>
                    @if($analysis['is_ai_generated'])
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-400/30">
                            ● Live & Terhubung
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/30 text-indigo-200 text-[10px] font-mono">
                            {{ $analysis['model'] }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-800 text-[10px] font-bold">
                            Aturan Pedagogis Bawaan
                        </span>
                    @endif
                </div>
                <p class="text-[11px] {{ $analysis['is_ai_generated'] ? 'text-indigo-200' : 'text-amber-700' }} mt-0.5">
                    Waktu Analisis: {{ $analysis['analyzed_at'] }} • Subjek: <span class="font-bold">{{ $analysis['student']['name'] }}</span> ({{ $analysis['student']['class'] }})
                </p>
            </div>
        </div>
        @if(!$analysis['is_ai_generated'] && in_array(Auth::user()->role, ['admin', 'super_admin']))
            <a href="{{ route('master.settings') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition shrink-0 self-start sm:self-auto">
                <span>🔑 Aktifkan Google Gemini API</span>
                <span>→</span>
            </a>
        @endif
    </div>

    <!-- Key Analytical Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Tingkat Kehadiran</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $analysis['metrics']['attendance_rate'] }}%</div>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $analysis['metrics']['late_count'] }} kali terlambat</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Prestasi Terverifikasi</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $analysis['metrics']['achievement_points'] }} Poin</div>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $analysis['metrics']['reward_tier'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Pelanggaran Terpisah</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $analysis['metrics']['violation_points'] }} Poin</div>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $analysis['metrics']['guidance_tier'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Skor 7 Kebiasaan</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $analysis['metrics']['habit_score'] }}%</div>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $analysis['metrics']['habit_status'] }}</p>
        </div>
    </div>

    <!-- Section 1: Ringkasan Perkembangan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs">1</div>
            <h3 class="text-base font-extrabold text-slate-900">Ringkasan Perkembangan Siswa</h3>
        </div>
        <div class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-100 text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
            {{ $analysis['ringkasan'] }}
        </div>
    </div>

    <!-- Section 2: Kesimpulan Pola Perkembangan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-xl bg-purple-50 text-purple-600 font-bold flex items-center justify-center text-xs">2</div>
            <h3 class="text-base font-extrabold text-slate-900">Kesimpulan Pola Berdasarkan Data</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($analysis['kesimpulan'] as $kes)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">{{ $kes['dimension'] }}</span>
                        <span class="w-2 h-2 rounded-full {{ $kes['sentiment'] === 'positive' ? 'bg-emerald-500' : ($kes['sentiment'] === 'warning' ? 'bg-rose-500' : 'bg-blue-500') }}"></span>
                    </div>
                    <p class="text-slate-600 leading-relaxed">{{ $kes['insight'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section 3: Rekomendasi Pendampingan Terarah -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-xs">3</div>
            <h3 class="text-base font-extrabold text-slate-900">Rekomendasi Saran Pendampingan Terarah</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Rekomendasi Wali Kelas -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                <span class="font-extrabold text-indigo-700 block text-sm">Untuk Wali Kelas:</span>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600 leading-relaxed">
                    @foreach($analysis['rekomendasi']['wali_kelas']['actions'] as $act)
                        <li>{{ $act }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Guru BK -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                <span class="font-extrabold text-purple-700 block text-sm">Untuk Guru BK:</span>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600 leading-relaxed">
                    @foreach($analysis['rekomendasi']['bk']['actions'] as $act)
                        <li>{{ $act }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Orang Tua -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                <span class="font-extrabold text-blue-700 block text-sm">Untuk Orang Tua / Wali:</span>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600 leading-relaxed">
                    @foreach($analysis['rekomendasi']['orang_tua']['actions'] as $act)
                        <li>{{ $act }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Rekomendasi Siswa -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                <span class="font-extrabold text-emerald-700 block text-sm">Untuk Refleksi Mandiri Siswa:</span>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600 leading-relaxed">
                    @foreach($analysis['rekomendasi']['siswa']['actions'] as $act)
                        <li>{{ $act }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Ethical Guardrail Notice -->
    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-[11px] text-slate-500 leading-relaxed flex items-start gap-3">
        <span class="font-bold text-slate-700">Catatan Etis AI:</span>
        <span>{{ $analysis['disclaimer'] }}</span>
    </div>
    @else
        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-400 text-xs">
            Pilih siswa untuk menghasilkan analisis perkembangan AI.
        </div>
    @endif
</div>
@endsection
