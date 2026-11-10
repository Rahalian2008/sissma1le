@php
    $w = $worshipTahfidzSummary ?? [
        'dzuhur' => ['today_hadir' => 0, 'today_total' => 0, 'today_record' => null, 'total_records' => 0, 'hadir_records' => 0, 'rate' => 0.0],
        'dhuha' => ['today_hadir' => 0, 'today_total' => 0, 'today_record' => null, 'total_records' => 0, 'hadir_records' => 0, 'rate' => 0.0],
        'keputrian' => ['today_hadir' => 0, 'today_total' => 0, 'today_record' => null, 'total_records' => 0, 'hadir_records' => 0, 'rate' => 0.0],
        'extracurricular' => ['today_hadir' => 0, 'today_total' => 0, 'today_record' => null, 'total_records' => 0, 'hadir_records' => 0, 'rate' => 0.0],
        'tahfidz' => ['total_deposits' => 0, 'lulus_deposits' => 0, 'completed_surahs_count' => 0, 'progress_pct' => 0.0, 'recent' => collect(), 'latest_deposit' => null],
    ];
    $isStudentOrParent = isset($student) && $student;
    $todayDayOfWeek = Carbon\Carbon::today('Asia/Jakarta')->dayOfWeek;
    // Jika anaknya jenis kelamin laki-laki, keputrian disembunyikan
    $hideKeputrian = $isStudentOrParent && ($student->gender === 'L');

    $dhuhaCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DHUHA);
    $dzuhurCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
    $keputrianCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_KEPUTRIAN);
    $extraDays = \App\Models\WorshipAttendance::parseDays(\App\Models\SchoolSetting::get('extra_attendance_days', 'senin,selasa,rabu,kamis,jumat,sabtu'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
    $extraDaysLabel = \App\Models\WorshipAttendance::formatDaysLabel($extraDays);
    $extraCarbonDays = \App\Models\WorshipAttendance::dayKeysToCarbonDays($extraDays);
    $extraStart = \App\Models\SchoolSetting::get('extra_attendance_start', \App\Models\SchoolSetting::get('extracurricular_start_time', '14:00'));
    $extraEnd = \App\Models\SchoolSetting::get('extra_attendance_end', \App\Models\SchoolSetting::get('extracurricular_end_time', '17:30'));
@endphp

<div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 sm:p-7 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-xs shadow-indigo-500/50"></span>
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📊 Ringkasan Presensi Ibadah & Progres Tahfidz Al-Qur'an</span>
                </h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                @if($isStudentOrParent)
                    Rekam jejak kehadiran Shalat Dzuhur/Jum'at, Dhuha, Keputrian, Ekstrakurikuler, dan capaian Juz 30 ananda <strong>{{ $student->name }}</strong>.
                @else
                    Monitoring terpadu kehadiran Shalat Dzuhur/Jum'at, Shalat Dhuha, Keputrian, Ekstrakurikuler, serta perkembangan hafalan Al-Qur'an Juz 30.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300 font-extrabold rounded-xl text-xs transition border border-indigo-200 dark:border-indigo-800 flex items-center gap-1">
                <span>🕌 Dzuhur</span>
            </a>
            <a href="{{ route('worship.index', 'dhuha') }}" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-extrabold rounded-xl text-xs transition border border-amber-200 dark:border-amber-800 flex items-center gap-1">
                <span>☀️ Dhuha</span>
            </a>
            @if(!$hideKeputrian)
            <a href="{{ route('worship.index', 'keputrian') }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-800 dark:text-rose-300 font-extrabold rounded-xl text-xs transition border border-rose-200 dark:border-rose-800 flex items-center gap-1">
                <span>🌸 Keputrian</span>
            </a>
            @endif
            <a href="{{ route('extracurricular.index') }}" class="px-2.5 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/60 dark:hover:bg-purple-900/60 text-purple-800 dark:text-purple-300 font-extrabold rounded-xl text-xs transition border border-purple-200 dark:border-purple-800 flex items-center gap-1">
                <span>🎯 Ekskul</span>
            </a>
            <a href="{{ route('tahfidz.index') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center gap-1">
                <span>📖 Tahfidz Juz 30 →</span>
            </a>
        </div>
    </div>

    <!-- 5 Cards Responsive Grid: Dzuhur, Dhuha, Keputrian, Ekstrakurikuler, Tahfidz Juz 30 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 {{ $hideKeputrian ? 'lg:grid-cols-2 xl:grid-cols-4' : 'lg:grid-cols-3 xl:grid-cols-5' }} gap-4 sm:gap-5">

        <!-- 1. Ringkasan Laporan Presensi Shalat Dzuhur / Jum'at -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-indigo-50/70 to-indigo-100/30 dark:from-indigo-950/30 dark:to-indigo-900/20 border border-indigo-200/80 dark:border-indigo-800/60 flex flex-col justify-between space-y-4 transition hover:shadow-md">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-xs shadow-indigo-600/40">
                        🕌
                    </span>
                    <div class="text-right">
                        <span class="px-2 py-0.5 bg-indigo-200/80 dark:bg-indigo-900/80 text-indigo-900 dark:text-indigo-200 text-[10px] font-black rounded-md tracking-wider uppercase block">
                            {{ $dzuhurCfg['days_label'] }}
                        </span>
                        <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 block mt-0.5">
                            Cutoff {{ $dzuhurCfg['end_time'] }} WIB
                        </span>
                    </div>
                </div>
                <div>
                    <h4 class="font-black text-sm text-slate-900 dark:text-white leading-snug">Presensi Shalat Dzuhur/Jum'at</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        @if($isStudentOrParent && $student->gender === 'P')
                            Senin - Kamis (Putri di Kelas/Masjid)
                        @else
                            {{ $dzuhurCfg['days_label'] }} di Masjid Sekolah
                        @endif
                    </p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-indigo-200/60 dark:border-indigo-800/40">
                @if($isStudentOrParent)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Status Hari Ini:</span>
                        @if($w['dzuhur']['today_record'])
                            <span class="px-2 py-0.5 rounded text-[10px] font-black
                                @if($w['dzuhur']['today_record']->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                @elseif($w['dzuhur']['today_record']->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                @elseif(in_array($w['dzuhur']['today_record']->status, ['IZIN', 'SAKIT', 'UDZUR'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                {{ $w['dzuhur']['today_record']->status }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                                {{ in_array($todayDayOfWeek, $dzuhurCfg['allowed_days']) ? 'Belum Tercatat' : 'Bukan Jadwal' }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Hadir Hari Ini:</span>
                        @if(!in_array($todayDayOfWeek, $dzuhurCfg['allowed_days']))
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">- (Bukan Jadwal)</span>
                        @elseif(($w['dzuhur']['today_total'] ?? 0) === 0)
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">0 Siswa <span class="text-[10px] text-slate-400 font-normal">(Belum Ada)</span></span>
                        @else
                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $w['dzuhur']['today_hadir'] }} Siswa</span>
                        @endif
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ $w['dzuhur']['rate'] }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-indigo-200/50 dark:bg-indigo-950 overflow-hidden">
                        <div class="h-full rounded-full bg-indigo-600 transition-all duration-500" style="width: {{ min(100, $w['dzuhur']['rate']) }}%"></div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center justify-between pt-1">
                    <span>Total Sesi: {{ $w['dzuhur']['total_records'] }}</span>
                    <span>Hadir: {{ $w['dzuhur']['hadir_records'] }}</span>
                </div>

                <div class="pt-1">
                    <a href="{{ route('worship.index', 'dzuhur-jumat') }}" class="w-full py-1.5 px-3 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                        <span>Laporan Dzuhur →</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Presensi Shalat Dhuha -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-amber-50/70 to-amber-100/30 dark:from-amber-950/30 dark:to-amber-900/20 border border-amber-200/80 dark:border-amber-800/60 flex flex-col justify-between space-y-4 transition hover:shadow-md">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-xs shadow-amber-500/40">
                        ☀️
                    </span>
                    <div class="text-right">
                        <span class="px-2 py-0.5 bg-amber-200/80 dark:bg-amber-900/80 text-amber-900 dark:text-amber-200 text-[10px] font-black rounded-md tracking-wider uppercase block">
                            {{ $dhuhaCfg['days_label'] }}
                        </span>
                        <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 block mt-0.5">
                            Cutoff {{ $dhuhaCfg['end_time'] }} WIB
                        </span>
                    </div>
                </div>
                <div>
                    <h4 class="font-black text-sm text-slate-900 dark:text-white leading-snug">Presensi Shalat Dhuha</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Semua Siswa (Putra & Putri)</p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-amber-200/60 dark:border-amber-800/40">
                @if($isStudentOrParent)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Status Hari Ini:</span>
                        @if($w['dhuha']['today_record'])
                            <span class="px-2 py-0.5 rounded text-[10px] font-black
                                @if($w['dhuha']['today_record']->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                @elseif($w['dhuha']['today_record']->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                @elseif(in_array($w['dhuha']['today_record']->status, ['IZIN', 'SAKIT', 'UDZUR'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                {{ $w['dhuha']['today_record']->status }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                                {{ in_array($todayDayOfWeek, $dhuhaCfg['allowed_days']) ? 'Belum Tercatat' : 'Bukan Jadwal' }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Hadir Hari Ini:</span>
                        @if(!in_array($todayDayOfWeek, $dhuhaCfg['allowed_days']))
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">- (Bukan Jadwal)</span>
                        @elseif(($w['dhuha']['today_total'] ?? 0) === 0)
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">0 Siswa <span class="text-[10px] text-slate-400 font-normal">(Belum Ada)</span></span>
                        @else
                            <span class="text-sm font-black text-amber-600 dark:text-amber-400">{{ $w['dhuha']['today_hadir'] }} Siswa</span>
                        @endif
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                        <span class="text-amber-600 dark:text-amber-400 font-black">{{ $w['dhuha']['rate'] }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-amber-200/50 dark:bg-amber-950 overflow-hidden">
                        <div class="h-full rounded-full bg-amber-500 transition-all duration-500" style="width: {{ min(100, $w['dhuha']['rate']) }}%"></div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center justify-between pt-1">
                    <span>Total Sesi: {{ $w['dhuha']['total_records'] }}</span>
                    <span>Hadir: {{ $w['dhuha']['hadir_records'] }}</span>
                </div>

                <div class="pt-1">
                    <a href="{{ route('worship.index', 'dhuha') }}" class="w-full py-1.5 px-3 bg-amber-600 hover:bg-amber-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                        <span>Laporan Dhuha →</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 3. Presensi Keputrian --}}
        @if(!$hideKeputrian)
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-rose-50/70 to-rose-100/30 dark:from-rose-950/30 dark:to-rose-900/20 border border-rose-200/80 dark:border-rose-800/60 flex flex-col justify-between space-y-4 transition hover:shadow-md">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center font-black text-lg shadow-xs shadow-rose-500/40">
                        🌸
                    </span>
                    <div class="text-right">
                        <span class="px-2 py-0.5 bg-rose-200/80 dark:bg-rose-900/80 text-rose-900 dark:text-rose-200 text-[10px] font-black rounded-md tracking-wider uppercase block">
                            {{ $keputrianCfg['days_label'] }}
                        </span>
                        <span class="text-[9px] font-bold text-rose-600 dark:text-rose-400 block mt-0.5">
                            Cutoff {{ $keputrianCfg['end_time'] }} WIB
                        </span>
                    </div>
                </div>
                <div>
                    <h4 class="font-black text-sm text-slate-900 dark:text-white leading-snug">Presensi Keputrian</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Khusus Siswi Putri ({{ $keputrianCfg['days_label'] }})</p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-rose-200/60 dark:border-rose-800/40">
                @if($isStudentOrParent)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Status Hari Ini:</span>
                        @if($student->gender === 'L')
                            <span class="text-[10px] font-bold text-slate-400">Khusus Putri</span>
                        @elseif($w['keputrian']['today_record'])
                            <span class="px-2 py-0.5 rounded text-[10px] font-black
                                @if($w['keputrian']['today_record']->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                @elseif($w['keputrian']['today_record']->status === 'HALANGAN' || $w['keputrian']['today_record']->status === 'UDZUR') bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300
                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                {{ $w['keputrian']['today_record']->status }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                                {{ in_array($todayDayOfWeek, $keputrianCfg['allowed_days']) ? 'Belum Tercatat' : 'Bukan Jadwal' }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Hadir Hari Ini:</span>
                        @if(!in_array($todayDayOfWeek, $keputrianCfg['allowed_days']))
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">- (Bukan Jadwal)</span>
                        @elseif(($w['keputrian']['today_total'] ?? 0) === 0)
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">0 Siswi <span class="text-[10px] text-slate-400 font-normal">(Belum Ada)</span></span>
                        @else
                            <span class="text-sm font-black text-rose-600 dark:text-rose-400">{{ $w['keputrian']['today_hadir'] }} Siswi</span>
                        @endif
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                        <span class="text-rose-600 dark:text-rose-400 font-black">{{ $w['keputrian']['rate'] }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-rose-200/50 dark:bg-rose-950 overflow-hidden">
                        <div class="h-full rounded-full bg-rose-500 transition-all duration-500" style="width: {{ min(100, $w['keputrian']['rate']) }}%"></div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center justify-between pt-1">
                    <span>Total Sesi: {{ $w['keputrian']['total_records'] }}</span>
                    <span>Hadir: {{ $w['keputrian']['hadir_records'] }}</span>
                </div>

                <div class="pt-1">
                    <a href="{{ route('worship.index', 'keputrian') }}" class="w-full py-1.5 px-3 bg-rose-600 hover:bg-rose-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                        <span>Laporan Keputrian →</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- 4. Presensi Ekstrakurikuler -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-purple-50/70 to-purple-100/30 dark:from-purple-950/30 dark:to-purple-900/20 border border-purple-200/80 dark:border-purple-800/60 flex flex-col justify-between space-y-4 transition hover:shadow-md">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-black text-lg shadow-xs shadow-purple-600/40">
                        🎯
                    </span>
                    <div class="text-right">
                        <span class="px-2 py-0.5 bg-purple-200/80 dark:bg-purple-900/80 text-purple-900 dark:text-purple-200 text-[10px] font-black rounded-md tracking-wider uppercase block">
                            {{ $extraDaysLabel }}
                        </span>
                        <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400 block mt-0.5">
                            Cutoff {{ $extraEnd }} WIB
                        </span>
                    </div>
                </div>
                <div>
                    <h4 class="font-black text-sm text-slate-900 dark:text-white leading-snug">Presensi Ekstrakurikuler</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Unit Minat & Bakat Siswa ({{ $extraStart }} - {{ $extraEnd }} WIB)</p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-purple-200/60 dark:border-purple-800/40">
                @if($isStudentOrParent)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Status Hari Ini:</span>
                        @if($w['extracurricular']['today_record'] ?? null)
                            <span class="px-2 py-0.5 rounded text-[10px] font-black
                                @if($w['extracurricular']['today_record']->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                @elseif($w['extracurricular']['today_record']->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                @elseif(in_array($w['extracurricular']['today_record']->status, ['IZIN', 'SAKIT'])) bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                {{ $w['extracurricular']['today_record']->status }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                                Belum Tercatat
                            </span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Hadir Hari Ini:</span>
                        @if(($w['extracurricular']['today_total'] ?? 0) === 0)
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400">0 Siswa <span class="text-[10px] text-slate-400 font-normal">(Belum Ada)</span></span>
                        @else
                            <span class="text-sm font-black text-purple-600 dark:text-purple-400">{{ $w['extracurricular']['today_hadir'] }} Siswa</span>
                        @endif
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500 dark:text-slate-400">Tingkat Hadir</span>
                        <span class="text-purple-600 dark:text-purple-400 font-black">{{ $w['extracurricular']['rate'] }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-purple-200/50 dark:bg-purple-950 overflow-hidden">
                        <div class="h-full rounded-full bg-purple-600 transition-all duration-500" style="width: {{ min(100, $w['extracurricular']['rate']) }}%"></div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center justify-between pt-1">
                    <span>Total Sesi: {{ $w['extracurricular']['total_records'] }}</span>
                    <span>Hadir: {{ $w['extracurricular']['hadir_records'] }}</span>
                </div>

                <div class="pt-1">
                    <a href="{{ route('extracurricular.index') }}" class="w-full py-1.5 px-3 bg-purple-600 hover:bg-purple-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                        <span>Laporan Ekskul →</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. Laporan & Progres Hafalan Al-Qur'an Juz 30 -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-50/70 to-emerald-100/30 dark:from-emerald-950/30 dark:to-emerald-900/20 border border-emerald-200/80 dark:border-emerald-800/60 flex flex-col justify-between space-y-4 transition hover:shadow-md">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-xs shadow-emerald-600/40">
                        📖
                    </span>
                    <span class="px-2 py-0.5 bg-emerald-200/80 dark:bg-emerald-900/80 text-emerald-900 dark:text-emerald-200 text-[10px] font-black rounded-md tracking-wider uppercase">
                        {{ !empty($w['tahfidz']['student_grade']) ? 'Kelas ' . $w['tahfidz']['student_grade'] . ' (' . ($w['tahfidz']['target_surahs_count'] ?? 25) . ' Surat)' : 'Juz 30 Target' }}
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-sm text-slate-900 dark:text-white leading-snug">Progres Hafalan Juz 30</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ !empty($w['tahfidz']['student_grade']) ? 'Target Mutu Kelas ' . $w['tahfidz']['student_grade'] : 'Buku Kontrol Setoran Digital' }}
                    </p>
                </div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-emerald-200/60 dark:border-emerald-800/40">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Surat Lulus:</span>
                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">
                        {{ $w['tahfidz']['completed_surahs_count'] }} <span class="text-xs font-normal text-slate-500">/ {{ $w['tahfidz']['target_surahs_count'] ?? 37 }} Surat</span>
                    </span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500 dark:text-slate-400">
                            {{ !empty($w['tahfidz']['student_grade']) ? 'Capaian Kelas ' . $w['tahfidz']['student_grade'] : 'Khatam Juz 30' }}
                        </span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-black">{{ $w['tahfidz']['progress_pct'] }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-emerald-200/50 dark:bg-emerald-950 overflow-hidden">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ min(100, $w['tahfidz']['progress_pct']) }}%"></div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center justify-between pt-1">
                    <span>Total Setor: {{ $w['tahfidz']['total_deposits'] }}x</span>
                    <span>Lulus: {{ $w['tahfidz']['lulus_deposits'] }}x</span>
                </div>

                <div class="pt-1">
                    <a href="{{ route('tahfidz.index') }}" class="w-full py-1.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-1.5">
                        <span>Buku Kontrol Tahfidz →</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
