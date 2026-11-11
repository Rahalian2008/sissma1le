@php
    $agendas = $schoolAgendasData ?? [
        'today_date' => \Carbon\Carbon::today('Asia/Jakarta')->toDateString(),
        'today_formatted' => \Carbon\Carbon::today('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y'),
        'today_day_name' => 'Hari Ini',
        'learning_agendas' => collect(),
        'active_holidays' => collect(),
        'upcoming_events' => collect(),
        'picket_officers' => collect(),
        'counseling_sessions' => collect(),
        'is_holiday_today' => false,
        'total_learning_agendas' => 0,
        'total_counseling' => 0,
    ];
@endphp

<div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 sm:p-7 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-blue-500 shadow-xs shadow-blue-500/50"></span>
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📅 Agenda & Kegiatan Sekolah Terupdate</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold text-[10px] border border-blue-200 dark:border-blue-800">
                    {{ $agendas['today_formatted'] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pantauan agenda pembelajaran harian kelas, kegiatan kalender sekolah, petugas piket, serta agenda terjadwal per hari ini.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $agendas['is_holiday_today'] ? 'Kegiatan Khusus / Libur' : 'KBM Efektif Aktif' }}</span>
            </span>
            @if(Route::has('class_learning_agendas.index'))
                <a href="{{ route('class_learning_agendas.index') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center gap-1">
                    <span>Semua Agenda KBM &rarr;</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 2 Column Responsive Layout: Left (Kalender & Piket), Right (Agenda Pembelajaran & Layanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Kolom Kiri: Kalender Akademik, Status Hari Ini & Petugas Piket -->
        <div class="space-y-4">
            <!-- Status Agenda Hari Ini -->
            <div class="p-4 rounded-2xl {{ $agendas['is_holiday_today'] ? 'bg-amber-50/70 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200/80 dark:border-slate-800' }} border space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider {{ $agendas['is_holiday_today'] ? 'text-amber-800 dark:text-amber-300' : 'text-slate-600 dark:text-slate-300' }}">
                        📌 Status Kalender Akademik Hari Ini
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 font-mono">
                        {{ $agendas['today_date'] }}
                    </span>
                </div>

                @if($agendas['active_holidays']->isNotEmpty())
                    @foreach($agendas['active_holidays'] as $h)
                        <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-amber-300/80 dark:border-amber-800/60 shadow-2xs">
                            <div class="font-extrabold text-sm text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                                <span>🎉</span>
                                <span>{{ $h->name }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                {{ $h->description ?: 'Kegiatan resmi dalam kalender pendidikan sekolah.' }}
                            </p>
                            <div class="text-[10px] text-amber-700 dark:text-amber-400 font-bold mt-1.5">
                                Berlaku: {{ \Carbon\Carbon::parse($h->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($h->end_date)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div class="font-bold text-xs text-slate-800 dark:text-slate-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Hari Pembelajaran KBM Efektif Reguler</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                            Seluruh rombongan belajar melaksanakan Kegiatan Belajar Mengajar (KBM) sesuai jadwal pelajaran dan presensi harian.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Petugas Piket Sekolah Hari Ini -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">🛡️</span>
                        <h4 class="font-bold text-xs text-indigo-900 dark:text-indigo-200">Petugas Piket Sekolah Hari Ini ({{ $agendas['today_day_name'] }})</h4>
                    </div>
                    @if(Route::has('picket.schedule.index'))
                        <a href="{{ route('picket.schedule.index') }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            Jadwal Lengkap &rarr;
                        </a>
                    @endif
                </div>

                @if($agendas['picket_officers']->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($agendas['picket_officers'] as $p)
                            <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-indigo-200/60 dark:border-indigo-800/40 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($p->person_name ?: ($p->teacher?->name ?: ($p->user?->name ?: 'P')), 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $p->person_name ?: ($p->teacher?->name ?: ($p->user?->name ?: 'Petugas Piket')) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate">
                                        NIP: {{ $p->nip ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                        Belum ada data petugas piket terdaftar khusus hari ini.
                    </p>
                @endif
            </div>

            <!-- Agenda Mendatang (7 - 14 Hari) -->
            @if($agendas['upcoming_events']->isNotEmpty())
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>🗓️ Agenda & Kegiatan Mendatang (14 Hari ke Depan)</span>
                        <span class="text-[10px] text-slate-400">{{ $agendas['upcoming_events']->count() }} Agenda</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($agendas['upcoming_events'] as $up)
                            <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $up->name }}</div>
                                    <div class="text-[10px] text-slate-400 truncate">{{ $up->description ?: 'Kegiatan kalender akademik sekolah' }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-[10px] font-bold shrink-0">
                                    {{ \Carbon\Carbon::parse($up->start_date)->translatedFormat('d M') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Agenda KBM Pembelajaran & Sesi Layanan Hari Ini -->
        <div class="space-y-4">
            <!-- Agenda KBM Pembelajaran Kelas Hari Ini -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">📚</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white">Agenda KBM Pembelajaran Hari Ini</h4>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                        {{ $agendas['total_learning_agendas'] }} Terdata Hari Ini
                    </span>
                </div>

                @if($agendas['learning_agendas']->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($agendas['learning_agendas']->take(5) as $agenda)
                            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-2xs">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 rounded font-black text-[10px]">
                                            {{ $agenda->rombel }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                            {{ $agenda->subject }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 truncate">
                                        <strong>Topik:</strong> {{ $agenda->learning_topic }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-3">
                                        <span>Guru: {{ $agenda->teacher_name ?: ($agenda->teacher?->name ?: '-') }}</span>
                                        <span>Hadir: <strong class="text-emerald-600 font-bold">{{ $agenda->students_present }}</strong> | S:{{ $agenda->students_sick }} | I:{{ $agenda->students_permitted }} | A:{{ $agenda->students_absent }}</span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-mono font-bold block">
                                        {{ $agenda->time_slot }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 text-center">
                        <span class="text-2xl mb-1 block">📝</span>
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Agenda KBM Tercatat Hari Ini</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Guru mapel akan mencatat topik pembelajaran dan absensi kelas melalui jurnal KBM digital.</p>
                    </div>
                @endif
            </div>

            <!-- Sesi Layanan Bimbingan Konseling (BK) Terjadwal Hari Ini -->
            @if($agendas['counseling_sessions']->isNotEmpty())
                <div class="p-4 rounded-2xl bg-teal-50/50 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🤝</span>
                            <h4 class="font-bold text-xs text-teal-900 dark:text-teal-200">Layanan Bimbingan Konseling (BK) Hari Ini</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-300 text-[10px] font-bold">
                            {{ $agendas['total_counseling'] }} Sesi
                        </span>
                    </div>

                    <div class="space-y-2">
                        @foreach($agendas['counseling_sessions'] as $cs)
                            <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-teal-200/60 dark:border-teal-800/40 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                        {{ $cs->student?->name }} ({{ $cs->student?->schoolClass?->name ?? 'Siswa' }})
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                        Layanan: {{ $cs->service_type ?? 'Konseling Individu' }} • Konselor: {{ $cs->counselor?->name ?? 'Guru BK' }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cs->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $cs->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

