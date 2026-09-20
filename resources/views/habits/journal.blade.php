@extends('layouts.app')

@section('title', 'Jurnal 7 Kebiasaan Anak Indonesia Hebat')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-700 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-bold text-emerald-200 backdrop-blur-md mb-2">
                <span>GERAKAN KARAKTER POSITIF</span>
                <span>•</span>
                <span>SMAN 1 LENGKONG</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white">7 Kebiasaan Anak Indonesia Hebat</h2>
            <p class="text-xs text-emerald-100 mt-1 max-w-xl leading-relaxed">
                Jurnal refleksi pembiasaan harian untuk melatih kemandirian, kesehatan, dan integritas siswa. Modul ini bersifat pendampingan positif, bukan sanksi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if(Auth::user()->role === 'super_admin')
                <button type="button" onclick="document.getElementById('modal-target-times').classList.remove('hidden')" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-2xl shadow-md text-xs sm:text-sm flex items-center gap-1.5 transition cursor-pointer">
                    ⚙️ Atur Waktu & Aktivitas
                </button>
            @endif
            <a href="{{ route('habits.summary', ['student_id' => $student?->id]) }}" class="px-4 py-2.5 bg-white text-slate-950 hover:bg-slate-100 font-bold rounded-2xl shadow-md text-xs sm:text-sm flex items-center gap-1.5 transition shrink-0">
                Ringkasan →
            </a>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <a href="{{ route('habits.report', ['class_id' => $student?->class_id]) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl border border-white/20 text-xs sm:text-sm flex items-center gap-1.5 transition shrink-0">
                    Laporan Kelas
                </a>
            @endif
        </div>
    </div>

    <!-- Date Picker & Progress Card -->
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('habits.journal') }}" class="flex items-center gap-3 text-xs">
            @if(request('student_id'))
                <input type="hidden" name="student_id" value="{{ request('student_id') }}">
            @endif
            <span class="font-bold text-slate-700 dark:text-slate-200">Pilih Tanggal Jurnal:</span>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-indigo-600 dark:text-indigo-400 text-xs">
        </form>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $completedCount }} dari {{ count($habits) }} Terlaksana</div>
                <div class="text-[11px] text-slate-400">Progres Pembiasaan Hari Ini</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                {{ $progressPct }}%
            </div>
        </div>
    </div>

    <!-- Student Switcher for Staff / Parents with Search & Initials -->
    @if(in_array(Auth::user()->role, ['guru', 'wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin']) && $allStudents->isNotEmpty())
        <div class="p-4 bg-slate-100 dark:bg-slate-800/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">👤</span>
                <span class="text-slate-600 dark:text-slate-300">
                    Jurnal Siswa: <strong class="text-slate-900 dark:text-white font-bold">{{ $student->name ?? '-' }}</strong>
                    <span class="text-slate-400">({{ $student->schoolClass?->name ?? 'Umum' }})</span>
                </span>
            </div>

            <!-- Trigger Button for Student Modal -->
            <button type="button" onclick="openHabitStudentModal()"
                class="px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 shadow-xs flex items-center gap-1.5 transition self-start sm:self-auto cursor-pointer">
                <span>🔍 Cari Siswa & Inisial ({{ $allStudents->count() }})</span>
                <span class="text-teal-600 font-extrabold">▼</span>
            </button>
        </div>

        <!-- Modal Search & Initials Siswa -->
        <div id="habit-student-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl relative">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <div>
                        <h3 class="font-black text-sm sm:text-base text-slate-900 dark:text-white">Pilih Siswa Jurnal</h3>
                        <p class="text-[11px] text-slate-400">Ketik nama/NIS atau klik huruf inisial siswa</p>
                    </div>
                    <button type="button" onclick="closeHabitStudentModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-500 text-sm font-bold flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative mb-2.5">
                    <input type="text" id="habit_student_search_input"
                        placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                        autocomplete="off"
                        oninput="filterHabitStudents(this.value)"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-teal-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Initials Filter Bar (A-Z) -->
                <div class="flex items-center gap-1 overflow-x-auto pb-2 mb-2 no-scrollbar text-xs" id="habit-initials-bar">
                    <button type="button" onclick="filterHabitByInitial('ALL')"
                        class="habit-initial-chip px-2.5 py-1 rounded-lg bg-teal-600 text-white font-bold text-[11px] shrink-0 transition" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterHabitByInitial('{{ $char }}')"
                            class="habit-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Student List -->
                <div id="habit-student-list" class="max-h-72 overflow-y-auto rounded-2xl border border-slate-100 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($allStudents as $st)
                        @php
                            $words = explode(' ', trim($st->name));
                            $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstChar = strtoupper(substr(trim($st->name), 0, 1));
                            $isSelected = ($student && $student->id === $st->id);
                        @endphp
                        <a href="{{ route('habits.journal', ['date' => $date, 'student_id' => $st->id]) }}"
                           class="habit-student-item px-3.5 py-2.5 hover:bg-teal-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition {{ $isSelected ? 'bg-teal-50/80 dark:bg-teal-950/40' : '' }}"
                           data-name="{{ $st->name }}"
                           data-nis="{{ $st->nis }}"
                           data-initials="{{ strtolower($initials) }}"
                           data-firstletter="{{ $firstChar }}">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-8 h-8 rounded-lg {{ $isSelected ? 'bg-teal-600 text-white' : 'bg-teal-100 dark:bg-teal-950 text-teal-700 dark:text-teal-300' }} font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($initials, 0, 2)) }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $st->name }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas {{ $st->schoolClass?->name ?? '-' }} • NIS: {{ $st->nis }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono shrink-0 font-bold">
                                [{{ strtoupper($initials) }}]
                            </span>
                        </a>
                    @endforeach
                    <div id="habit-student-empty" class="hidden p-6 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>
        </div>

        <script>
            let activeHabitInitial = 'ALL';

            function openHabitStudentModal() {
                const modal = document.getElementById('habit-student-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                    const input = document.getElementById('habit_student_search_input');
                    if (input) setTimeout(() => input.focus(), 50);
                }
            }

            function closeHabitStudentModal() {
                const modal = document.getElementById('habit-student-modal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            function filterHabitByInitial(letter) {
                activeHabitInitial = letter;
                document.querySelectorAll('.habit-initial-chip').forEach(btn => {
                    if (btn.getAttribute('data-letter') === letter) {
                        btn.className = 'habit-initial-chip px-2.5 py-1 rounded-lg bg-teal-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
                    } else {
                        btn.className = 'habit-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200';
                    }
                });
                applyHabitFilters();
            }

            function filterHabitStudents(keyword) {
                applyHabitFilters();
            }

            function applyHabitFilters() {
                const keyword = (document.getElementById('habit_student_search_input')?.value || '').toLowerCase().trim();
                const items = document.querySelectorAll('.habit-student-item');
                let visibleCount = 0;

                items.forEach(item => {
                    const name = (item.getAttribute('data-name') || '').toLowerCase();
                    const nis = (item.getAttribute('data-nis') || '').toLowerCase();
                    const initials = (item.getAttribute('data-initials') || '').toLowerCase();
                    const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

                    const matchInitial = (activeHabitInitial === 'ALL') || (firstLetter === activeHabitInitial);
                    const matchKeyword = !keyword || name.includes(keyword) || nis.includes(keyword) || initials.includes(keyword);

                    if (matchInitial && matchKeyword) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const emptyNotice = document.getElementById('habit-student-empty');
                if (emptyNotice) {
                    emptyNotice.classList.toggle('hidden', visibleCount > 0);
                }
            }
        </script>
    @endif

    <!-- 7 Habits Daily Checklist Cards -->
    <div class="space-y-4">
        @php
            $todayDate = \Carbon\Carbon::today('Asia/Jakarta')->toDateString();
            $isToday = ($date === $todayDate);
        @endphp

        @foreach($habits as $habit)
            @php
                $log = $dailyLogs->get($habit->id);
                $isDone = $log && $log->is_completed;
                $timeStatus = $habit->getTimeWindowStatus();
                $canCheck = $isToday && $timeStatus['is_active'];
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-3xl border {{ $isDone ? 'border-emerald-300 dark:border-emerald-700 shadow-sm' : 'border-slate-200 dark:border-slate-800' }} p-5 sm:p-6 transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <span class="w-10 h-10 rounded-2xl font-black text-sm flex items-center justify-center shrink-0 {{ $isDone ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                            {{ $habit->order_number }}
                        </span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white {{ $isDone ? 'line-through text-slate-500 dark:text-slate-400' : '' }}">
                                    {{ $habit->name }}
                                </h3>
                                @if($isDone)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-bold text-[10px]">
                                        ✓ Selesai {{ substr($log->check_time ?? '', 0, 5) }} WIB
                                    </span>
                                @elseif($isToday && $habit->is_time_restricted && !empty($habit->start_time))
                                    @if($timeStatus['is_active'])
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-bold text-[10px] flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Jadwal Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-bold text-[10px] flex items-center gap-1">
                                            🔒 Waktu Terkunci
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $habit->tagline }} • 
                                <span class="font-bold {{ $timeStatus['is_active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500' }}">
                                    Target: {{ $timeStatus['range'] }}
                                </span>
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-400 mt-1 leading-relaxed">{{ $habit->description }}</p>

                            <!-- Default Activity Hint -->
                            @if($habit->default_activity)
                                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <span class="font-bold text-slate-600 dark:text-slate-300">Rekomendasi Kegiatan:</span>
                                    <span>{{ $habit->default_activity }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button / Time Lock -->
                    <div class="sm:shrink-0 text-right">
                        @if(Auth::user()->role === 'siswa')
                            @if($isDone)
                                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2">
                                    <button type="button" onclick="openStudentEntryModal('{{ $habit->id }}', '{{ addslashes($habit->name) }}', '{{ $habit->order_number }}', '{{ addslashes($log->activity_name ?? $habit->default_activity) }}', '{{ addslashes($log->reflection ?? '') }}', '{{ addslashes($habit->reflection_prompt ?? '') }}', '{{ $timeStatus['range'] }}')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition cursor-pointer">
                                        ✎ Edit Refleksi
                                    </button>
                                    @if($canCheck)
                                        <form action="{{ route('habits.log.update') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="habit_id" value="{{ $habit->id }}">
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="is_completed" value="0">
                                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer">
                                                Batalkan Ceklis
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                @if($canCheck)
                                    <button type="button" onclick="openStudentEntryModal('{{ $habit->id }}', '{{ addslashes($habit->name) }}', '{{ $habit->order_number }}', '{{ addslashes($habit->default_activity ?? '') }}', '', '{{ addslashes($habit->reflection_prompt ?? '') }}', '{{ $timeStatus['range'] }}')" class="w-full sm:w-auto px-5 py-2.5 rounded-2xl text-xs font-bold bg-slate-900 hover:bg-indigo-600 text-white transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                        <span>✓</span> Laksanakan
                                    </button>
                                @else
                                    <div class="space-y-1 text-right">
                                        <button type="button" disabled class="w-full sm:w-auto px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed flex items-center justify-center gap-1.5 shadow-none">
                                            <span>🔒</span> Di Luar Jam
                                        </button>
                                        <div class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                                            {{ $isToday ? $timeStatus['message'] : 'Hanya untuk hari ini' }}
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @else
                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $isDone ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                {{ $isDone ? 'Terlaksana' : 'Belum Terlaksana' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Activity Details & Reflection if Done -->
                @if($isDone && ($log->activity_name || $log->reflection))
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs bg-slate-50/70 dark:bg-slate-800/40 p-3.5 rounded-2xl space-y-1.5">
                        @if($log->activity_name)
                            <div><strong class="text-slate-700 dark:text-slate-200">Aktivitas:</strong> <span class="text-slate-600 dark:text-slate-300">{{ $log->activity_name }}</span></div>
                        @endif
                        @if($log->reflection)
                            <div><strong class="text-slate-700 dark:text-slate-200">Refleksi Diri:</strong> <span class="text-slate-600 dark:text-slate-300 italic">"{{ $log->reflection }}"</span></div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Mentoring & Reflection Notes from Staff / Parents -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Catatan Pendampingan & Komunikasi Keluarga</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Wali Kelas, Guru, atau Orang Tua dapat memberikan catatan apresiasi untuk mendukung pembiasaan positif anak.</p>

        <!-- Existing Notes -->
        <div class="space-y-3 mb-5">
            @forelse($notes as $note)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $note->author->name }} ({{ strtoupper(str_replace('_', ' ', $note->author_role)) }})</span>
                        <span class="text-slate-400 text-[10px]">{{ $note->created_at->format('H:i') }} WIB</span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $note->comment }}</p>
                </div>
            @empty
                <div class="text-center py-4 text-xs text-slate-400">Belum ada catatan pendampingan pada tanggal ini.</div>
            @endforelse
        </div>

        @if(in_array(Auth::user()->role, ['wali_kelas', 'guru', 'bk', 'orang_tua', 'admin', 'super_admin']) && $student)
            <form action="{{ route('habits.note.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <textarea name="comment" required rows="2" placeholder="Tuliskan kata-kata penyemangat, observasi, atau refleksi pembiasaan untuk siswa..."
                    class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100"></textarea>
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                    Kirim Catatan Pendampingan
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Modal 1: Student Entry (Laksanakan / Isi Refleksi) -->
<div id="modal-student-entry" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5 mb-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300" id="entry-order-badge">
                    Pilar 1
                </span>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-1" id="entry-habit-name">
                    Bangun Pagi
                </h3>
            </div>
            <button type="button" onclick="closeStudentEntryModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('habits.log.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="habit_id" id="entry-habit-id">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="is_completed" value="1">

            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300">Status Waktu Pelaksanaan:</span>
                <span class="text-[11px] font-extrabold text-emerald-900 dark:text-emerald-200" id="entry-time-range">-</span>
            </div>

            <!-- Aktivitas -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Aktivitas yang Dilakukan *</label>
                <input type="text" name="activity_name" id="entry-activity-name" required placeholder="Contoh: Bangun jam 04.45, wudhu, dan sholat..."
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-800 dark:text-slate-100">
            </div>

            <!-- Refleksi Diri -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Refleksi / Kesan Anda</label>
                <p class="text-[11px] text-indigo-600 dark:text-indigo-400 italic mb-1.5" id="entry-reflection-prompt"></p>
                <textarea name="reflection" id="entry-reflection" rows="3" placeholder="Tuliskan refleksi singkat Anda..."
                    class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeStudentEntryModal()" class="px-4 py-2 text-slate-500 font-bold hover:text-slate-700 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    ✓ Simpan & Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Super Admin Settings (Waktu, Aktivitas, & Refleksi) -->
@if(Auth::user()->role === 'super_admin')
<div id="modal-target-times" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Pengaturan 7 Kebiasaan Anak Hebat</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur rentang jam aktif, kunci waktu pelaksanaan, rekomendasi aktivitas, dan panduan refleksi.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-target-times').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('habits.target_times.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-1.5">
                @foreach($habits as $idx => $h)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] font-black">
                                    {{ $h->order_number }}
                                </span>
                                {{ $h->name }}
                            </span>
                            <input type="hidden" name="targets[{{ $idx }}][id]" value="{{ $h->id }}">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="targets[{{ $idx }}][is_time_restricted]" value="1" {{ $h->is_time_restricted ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">Kunci Waktu Otomatis</span>
                            </label>
                        </div>

                        <!-- Rentang Jam -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Jam Mulai</label>
                                <input type="time" name="targets[{{ $idx }}][start_time]" value="{{ substr($h->start_time ?? '', 0, 5) }}"
                                    class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Jam Selesai</label>
                                <input type="time" name="targets[{{ $idx }}][end_time]" value="{{ substr($h->end_time ?? '', 0, 5) }}"
                                    class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                            </div>
                        </div>

                        <!-- Rekomendasi Aktivitas -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Rekomendasi / Default Aktivitas</label>
                            <input type="text" name="targets[{{ $idx }}][default_activity]" value="{{ $h->default_activity }}" placeholder="Contoh: Sholat Subuh berjamaah dan merapikan tempat tidur"
                                class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100">
                        </div>

                        <!-- Panduan Refleksi -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Panduan / Pertanyaan Refleksi Siswa</label>
                            <textarea name="targets[{{ $idx }}][reflection_prompt]" rows="2" placeholder="Pertanyaan panduan refleksi saat siswa mencentang..."
                                class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">{{ $h->reflection_prompt }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-target-times').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs transition shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openStudentEntryModal(id, name, order, defaultAct, currentRef, prompt, timeRange) {
        document.getElementById('entry-habit-id').value = id;
        document.getElementById('entry-habit-name').textContent = name;
        document.getElementById('entry-order-badge').textContent = 'Pilar ' + order;
        document.getElementById('entry-activity-name').value = defaultAct || '';
        document.getElementById('entry-reflection').value = currentRef || '';
        document.getElementById('entry-reflection-prompt').textContent = prompt ? ('Panduan: ' + prompt) : '';
        document.getElementById('entry-time-range').textContent = timeRange;
        document.getElementById('modal-student-entry').classList.remove('hidden');
    }

    function closeStudentEntryModal() {
        document.getElementById('modal-student-entry').classList.add('hidden');
    }
</script>
@endpush
@endsection
