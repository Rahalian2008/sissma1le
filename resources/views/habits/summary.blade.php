@extends('layouts.app')

@section('title', 'Ringkasan 7 Kebiasaan Anak Hebat - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-800 via-emerald-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
                    ANALITIK PEMBIASAAN SISWA
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-bold uppercase tracking-wider">
                    Mode {{ $mode === 'weekly' ? 'Mingguan' : 'Bulanan' }}
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">Ringkasan 7 Kebiasaan Anak Hebat</h2>
            <p class="text-xs text-slate-200 mt-1">Siswa: <strong class="text-white">{{ $student->name ?? '-' }}</strong> (Kelas {{ $student->schoolClass?->name ?? '-' }})</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('habits.journal', ['student_id' => $student?->id]) }}" class="px-4 py-2.5 bg-white text-slate-950 hover:bg-slate-100 font-bold rounded-xl text-xs shadow-xs transition">
                ← Jurnal Harian
            </a>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <a href="{{ route('habits.report', ['class_id' => $student?->class_id]) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs border border-white/20 transition">
                    Laporan Kelas →
                </a>
            @endif
        </div>
    </div>

    <!-- Mode Selector & Student Switcher (If Authorized) -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Tab Mode: Mingguan vs Bulanan -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 mr-1">Tinjauan:</span>
            <a href="{{ route('habits.summary', array_merge(request()->query(), ['mode' => 'weekly'])) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $mode === 'weekly' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <span>📅 Mingguan (7 Hari)</span>
            </a>
            <a href="{{ route('habits.summary', array_merge(request()->query(), ['mode' => 'monthly'])) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $mode === 'monthly' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <span>📊 Bulanan (30 Hari)</span>
            </a>
        </div>

        <!-- Student Selector: Hidden strictly for siswa and orang_tua -->
        @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']) && $allStudents->isNotEmpty())
            <div class="flex items-center gap-2">
                <button type="button" onclick="openSummaryStudentModal()"
                    class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 shadow-xs flex items-center gap-2 transition cursor-pointer">
                    <span>🔍 Siswa: <strong>{{ $student?->name ?? 'Pilih Siswa' }}</strong></span>
                    <span class="text-teal-600 font-extrabold text-[10px]">GANTI ▼</span>
                </button>
            </div>

            <!-- Modal Search & Initials Siswa untuk Summary -->
            <div id="summary-student-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-5 sm:p-6 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                        <div>
                            <h3 class="font-black text-sm sm:text-base text-slate-900">Pilih Siswa (Tren Kebiasaan)</h3>
                            <p class="text-[11px] text-slate-400">Ketik nama/NIS atau klik huruf inisial siswa</p>
                        </div>
                        <button type="button" onclick="closeSummaryStudentModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 text-sm font-bold flex items-center justify-center transition">
                            ✕
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative mb-2.5">
                        <input type="text" id="summary_student_search_input"
                            placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                            autocomplete="off"
                            oninput="filterSummaryStudents(this.value)"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 outline-none focus:border-teal-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Initials Filter Bar (A-Z) -->
                    <div class="flex items-center gap-1 overflow-x-auto pb-2 mb-2 no-scrollbar text-xs" id="summary-initials-bar">
                        <button type="button" onclick="filterSummaryByInitial('ALL')"
                            class="summary-initial-chip px-2.5 py-1 rounded-lg bg-teal-600 text-white font-bold text-[11px] shrink-0 transition" data-letter="ALL">
                            Semua
                        </button>
                        @foreach(range('A', 'Z') as $char)
                            <button type="button" onclick="filterSummaryByInitial('{{ $char }}')"
                                class="summary-initial-chip px-2 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold text-[11px] shrink-0 transition hover:bg-slate-200" data-letter="{{ $char }}">
                                {{ $char }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Student List -->
                    <div id="summary-student-list" class="max-h-72 overflow-y-auto rounded-2xl border border-slate-100 divide-y divide-slate-100">
                        @foreach($allStudents as $st)
                            @php
                                $words = explode(' ', trim($st->name));
                                $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                                $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                                $isSelected = ($student && $student->id === $st->id);
                            @endphp
                            <a href="{{ route('habits.summary', ['mode' => $mode, 'student_id' => $st->id]) }}"
                               class="summary-student-item px-3.5 py-2.5 hover:bg-teal-50 cursor-pointer flex items-center justify-between transition {{ $isSelected ? 'bg-teal-50/80 font-bold' : '' }}"
                               data-name="{{ $st->name }}"
                               data-nis="{{ $st->nis }}"
                               data-initials="{{ strtolower($initials) }}"
                               data-firstletter="{{ $firstChar }}">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <span class="w-8 h-8 rounded-lg {{ $isSelected ? 'bg-teal-600 text-white' : 'bg-teal-100 text-teal-700' }} font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($initials, 0, 2)) }}
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 truncate text-xs">{{ $st->name }}</div>
                                        <div class="text-[10px] text-slate-400">Kelas {{ $st->schoolClass?->name ?? '-' }} • NIS: {{ $st->nis }}</div>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono shrink-0 font-bold">
                                    [{{ strtoupper($initials) }}]
                                </span>
                            </a>
                        @endforeach
                        <div id="summary-student-empty" class="hidden p-6 text-center text-xs text-slate-400 italic">
                            Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                        </div>
                    </div>
                </div>
            </div>

            <script>
                let activeSummaryInitial = 'ALL';

                function openSummaryStudentModal() {
                    const modal = document.getElementById('summary-student-modal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        const input = document.getElementById('summary_student_search_input');
                        if (input) setTimeout(() => input.focus(), 50);
                    }
                }

                function closeSummaryStudentModal() {
                    const modal = document.getElementById('summary-student-modal');
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                }

                function filterSummaryByInitial(letter) {
                    activeSummaryInitial = letter;
                    document.querySelectorAll('.summary-initial-chip').forEach(btn => {
                        if (btn.getAttribute('data-letter') === letter) {
                            btn.className = 'summary-initial-chip px-2.5 py-1 rounded-lg bg-teal-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
                        } else {
                            btn.className = 'summary-initial-chip px-2 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold text-[11px] shrink-0 transition hover:bg-slate-200';
                        }
                    });
                    applySummaryFilters();
                }

                function filterSummaryStudents(keyword) {
                    applySummaryFilters();
                }

                function applySummaryFilters() {
                    const keyword = (document.getElementById('summary_student_search_input')?.value || '').toLowerCase().trim();
                    const items = document.querySelectorAll('.summary-student-item');
                    let visibleCount = 0;

                    items.forEach(item => {
                        const name = (item.getAttribute('data-name') || '').toLowerCase();
                        const nis = (item.getAttribute('data-nis') || '').toLowerCase();
                        const initials = (item.getAttribute('data-initials') || '').toLowerCase();
                        const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

                        const matchInitial = (activeSummaryInitial === 'ALL') || (firstLetter === activeSummaryInitial);
                        const matchKeyword = !keyword || name.includes(keyword) || nis.includes(keyword) || initials.includes(keyword);

                        if (matchInitial && matchKeyword) {
                            item.style.display = 'flex';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    const emptyNotice = document.getElementById('summary-student-empty');
                    if (emptyNotice) {
                        emptyNotice.classList.toggle('hidden', visibleCount > 0);
                    }
                }
            </script>
        @endif
    </div>

    <!-- Trend Chart Container -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-black text-slate-900">
                Tren Konsistensi Harian ({{ $mode === 'weekly' ? '7 Hari Terakhir' : '30 Hari Terakhir' }})
            </h3>
            <span class="text-xs font-bold text-teal-600">
                Target: 7 Pilar / Hari
            </span>
        </div>
        <p class="text-xs text-slate-500 mb-6">Persentase keberhasilan pelaksanaan 7 kebiasaan setiap harinya</p>

        <div class="grid {{ $mode === 'weekly' ? 'grid-cols-7' : 'grid-cols-10 sm:grid-cols-15 md:grid-cols-30' }} gap-1.5 sm:gap-2 items-end h-44 pt-4 border-b border-slate-100 pb-2">
            @foreach($trendDays as $day)
                <div class="flex flex-col items-center gap-1.5 h-full justify-end group">
                    <span class="text-[10px] font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        {{ $day['completed'] }}/7
                    </span>
                    <div class="w-full bg-emerald-50 rounded-t-lg transition-all duration-300 overflow-hidden" style="height: {{ max(10, $day['percentage']) }}%">
                        <div class="w-full h-full {{ $day['percentage'] >= 70 ? 'bg-emerald-500 hover:bg-emerald-400' : 'bg-teal-400 hover:bg-teal-300' }} rounded-t-lg"></div>
                    </div>
                    <span class="text-[9px] text-slate-400 truncate w-full text-center">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Per-Habit Consistency Status Cards -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-black text-slate-900">
                    Capaian 7 Pilar Karakter (Periode {{ $mode === 'weekly' ? 'Mingguan - 7 Hari' : 'Bulanan - 30 Hari' }})
                </h3>
                <p class="text-xs text-slate-500">Ketercapaian pembiasaan positif siswa pada masing-masing pilar</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">
                Target: 100% Tiap Hari
            </span>
        </div>

        <div class="space-y-3">
            @foreach($habitStats as $stat)
                @php
                    $h = $stat['habit'];
                    $color = match ($h->order_number) {
                        1 => 'bg-amber-500',
                        2 => 'bg-emerald-500',
                        3 => 'bg-blue-500',
                        4 => 'bg-rose-500',
                        5 => 'bg-purple-500',
                        6 => 'bg-indigo-500',
                        default => 'bg-cyan-500',
                    };
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-white hover:shadow-xs transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl {{ $color }} text-white font-black flex items-center justify-center shrink-0">
                            K{{ $h->order_number }}
                        </span>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm">{{ $h->name }}</h4>
                            <p class="text-slate-500 text-[11px]">{{ $h->target_time ? 'Target: '.$h->target_time.' • ' : '' }}{{ $h->tagline }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 sm:justify-end">
                        <div class="text-right">
                            <span class="font-bold text-slate-700">{{ $stat['completed_days'] }} dari {{ $stat['days_count'] }} Hari</span>
                            <div class="w-32 bg-slate-200 h-2 rounded-full mt-1 overflow-hidden">
                                <div class="{{ $color }} h-full rounded-full transition-all duration-500" style="width: {{ $stat['percentage'] }}%"></div>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $stat['status'] === 'KONSISTEN' ? 'bg-emerald-100 text-emerald-800' : ($stat['status'] === 'BERKEMBANG' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $stat['status'] }} ({{ $stat['percentage'] }}%)
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
