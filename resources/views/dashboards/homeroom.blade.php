@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    <!-- Header Wali Kelas -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-white shadow-xl border border-slate-800">
        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-indigo-300 border border-white/10">
            Wali Kelas Binaan
        </span>
        <h2 class="text-lg sm:text-2xl font-extrabold text-white mt-2">Kelas {{ $class->name ?? 'XI-IPA-1' }}</h2>
        <p class="text-xs text-slate-300 mt-1">Wali Kelas: {{ $teacher->name ?? Auth::user()->name }} • Total {{ $students->count() }} Siswa Binaan</p>
    </div>

    <!-- Menu / Ikon Cepat Operasional Wali Kelas -->
    @include('dashboards.partials.staff_quick_actions')

    <!-- Attendance Summary for Class Today -->
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 sm:gap-3.5">
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block truncate">Total Siswa</span>
            <div class="text-base sm:text-2xl font-black text-slate-900 dark:text-white mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $totalStudents ?? $students->count() }}</span>
                <span class="text-[10px] font-semibold text-slate-400 hidden xs:inline">Siswa</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-emerald-600 uppercase tracking-wider block truncate">Hadir</span>
            <div class="text-base sm:text-2xl font-black text-emerald-600 mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $presentToday }}</span>
                <span class="text-[10px] font-semibold text-emerald-500 hidden xs:inline">Siswa</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-amber-600 uppercase tracking-wider block truncate">Terlambat</span>
            <div class="text-base sm:text-2xl font-black text-amber-600 mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $lateToday }}</span>
                <span class="text-[10px] font-semibold text-amber-500 hidden xs:inline">Siswa</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-sky-600 uppercase tracking-wider block truncate">Izin</span>
            <div class="text-base sm:text-2xl font-black text-sky-600 mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $permissionToday }}</span>
                <span class="text-[10px] font-semibold text-sky-500 hidden xs:inline">Siswa</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-purple-600 uppercase tracking-wider block truncate">Sakit</span>
            <div class="text-base sm:text-2xl font-black text-purple-600 mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $sickToday }}</span>
                <span class="text-[10px] font-semibold text-purple-500 hidden xs:inline">Siswa</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-bold text-rose-600 uppercase tracking-wider block truncate">Alpa</span>
            <div class="text-base sm:text-2xl font-black text-rose-600 mt-1 tabular-num flex items-baseline gap-1">
                <span>{{ $alphaToday }}</span>
                <span class="text-[10px] font-semibold text-rose-500 hidden xs:inline">Siswa</span>
            </div>
        </div>
    </div>

    <!-- Guidance Alert Notice if any students >= 75 points -->
    @if($needGuidanceStudents->isNotEmpty())
        <div class="p-5 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-950 flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold text-lg">!</div>
            <div>
                <h4 class="font-extrabold text-sm text-amber-900">NOTIFIKASI PERLU TINDAKAN: Siswa Memerlukan Pembinaan Wali Kelas</h4>
                <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                    Terdapat siswa yang telah mencapai akumulasi 75 poin pelanggaran. Sesuai dokumen tata tertib SMAN 1 Lengkong, segera lakukan sesi bimbingan konseling dan berikan catatan pembinaan.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($needGuidanceStudents as $ngs)
                        <a href="{{ route('guidance.create', ['student_id' => $ngs->id]) }}" class="px-3 py-1 bg-amber-500 text-white font-bold rounded-xl text-xs hover:bg-amber-600 transition">
                            Bimbing {{ $ngs->name }} ({{ $ngs->totalViolationPoints() }} Poin) →
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Papan Peringkat Presensi Kelas Binaan -->
    @include('dashboards.partials.attendance_rankings')

    <!-- Students Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Daftar Siswa Binaan Kelas {{ $class->name ?? '' }}</span>
                    </h3>
                    <span class="table-swipe-cue">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Geser Tabel →
                    </span>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring terpadu presensi multi-kegiatan, capaian Tahfidz Juz 30, serta tata tertib siswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Poin Prestasi & Tatib Terpisah</span>
            </div>
        </div>
        <div class="responsive-table-container">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/70 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider text-[10px] sm:text-[11px]">
                    <tr>
                        <th class="py-3 px-3.5">Nama Siswa / NIS</th>
                        <th class="py-3 px-3.5 min-w-[200px]">Presensi Hari Ini</th>
                        <th class="py-3 px-3.5 min-w-[170px]">Hafalan Juz 30</th>
                        <th class="py-3 px-3.5 text-center">Prestasi</th>
                        <th class="py-3 px-3.5 text-center">Pelanggaran</th>
                        <th class="py-3 px-3.5 text-center">Status Pembinaan</th>
                        <th class="py-3 px-3.5 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($students as $s)
                        @php
                            // 1. Presensi Hari Ini (Multi-Jenis)
                            $att = $s->attendances->first();
                            $dhuha = $s->worshipAttendances->firstWhere('activity_type', \App\Models\WorshipAttendance::ACTIVITY_DHUHA);
                            $dzuhur = $s->worshipAttendances->firstWhere('activity_type', \App\Models\WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
                            $keputrian = $s->worshipAttendances->firstWhere('activity_type', \App\Models\WorshipAttendance::ACTIVITY_KEPUTRIAN);
                            $extraAtt = $s->extracurricularAttendances->first();

                            // 2. Tahfidz Juz 30
                            $targetGrade = $s->getNormalizedGrade() ?: ($class?->grade ? \App\Models\QuranTahfidzRecord::normalizeGrade($class->grade) : 'XII');
                            $gradeSurahs = \App\Models\QuranTahfidzRecord::getSurahsByGrade($targetGrade);
                            $targetCount = count($gradeSurahs) ?: 37;

                            $lulusSurahs = $s->tahfidzRecords->where('overall_grade', 'Lulus')->pluck('surah_number')->unique();
                            $completedGradeCount = $lulusSurahs->intersect(array_keys($gradeSurahs))->count();
                            $totalLulusAll = $lulusSurahs->count();
                            $progressPct = $targetCount > 0 ? min(100, round(($completedGradeCount / $targetCount) * 100, 1)) : 0;
                            $latestTahfidz = $s->tahfidzRecords->sortByDesc('deposit_date')->sortByDesc('deposit_time')->first();

                            // 3. Prestasi & Pelanggaran
                            $achPoints = $s->totalAchievementPoints();
                            $violPoints = $s->totalViolationPoints();
                            $guidance = $s->guidance_status;
                            $reward = $s->reward_status;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <!-- 1. Nama Siswa & Identitas -->
                            <td class="py-3 px-3.5">
                                <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $s->name }}</span>
                                    @if($s->gender === 'P')
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-pink-50 text-pink-600 dark:bg-pink-950/50 dark:text-pink-300 font-bold" title="Perempuan">P</span>
                                    @else
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300 font-bold" title="Laki-laki">L</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">NIS: {{ $s->nis }}</div>
                            </td>

                            <!-- 2. Presensi Hari Ini (Lengkap Semua Jenis) -->
                            <td class="py-3 px-3.5">
                                <div class="space-y-1">
                                    <!-- A. Presensi Utama KBM (Masuk & Pulang) -->
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @if($att)
                                            @php
                                                $badgeStyle = match($att->status) {
                                                    'HADIR' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800',
                                                    'TERLAMBAT' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
                                                    'IZIN' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-300 dark:border-sky-800',
                                                    'SAKIT' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800',
                                                    'ALPA' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800',
                                                    default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300'
                                                };
                                            @endphp
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold {{ $badgeStyle }}">
                                                KBM: {{ $att->status }} {{ $att->time ? '('.substr($att->time, 0, 5).')' : '' }}
                                            </span>
                                            @if($att->time_out)
                                                <span class="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200 dark:border-teal-800" title="Jam Kepulangan">
                                                    Plg: {{ substr($att->time_out, 0, 5) }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                KBM: Belum Absen
                                            </span>
                                        @endif
                                    </div>

                                    <!-- B. Presensi Ibadah (Dhuha & Dzuhur) -->
                                    <div class="flex items-center gap-1 flex-wrap text-[9.5px]">
                                        @if($dhuha)
                                            <span class="px-1 py-0.2 rounded font-bold {{ $dhuha->status === 'HADIR' || $dhuha->status === 'TERLAMBAT' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">
                                                ☀️ Dhuha: {{ $dhuha->status }}
                                            </span>
                                        @endif
                                        @if($dzuhur)
                                            <span class="px-1 py-0.2 rounded font-bold {{ $dzuhur->status === 'HADIR' || $dzuhur->status === 'TERLAMBAT' ? 'bg-indigo-100 text-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">
                                                🕌 Dzuhur: {{ $dzuhur->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 3. Hafalan Al-Qur'an Juz 30 -->
                            <td class="py-3 px-3.5 min-w-[170px]">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-extrabold text-slate-800 dark:text-slate-200 tabular-num">
                                            {{ $completedGradeCount }}/{{ $targetCount }} <span class="text-[9.5px] font-normal text-slate-400">Surat</span>
                                        </span>
                                        <span class="font-black tabular-num {{ $progressPct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($progressPct >= 50 ? 'text-teal-600 dark:text-teal-400' : 'text-amber-600 dark:text-amber-400') }}">
                                            {{ $progressPct }}%
                                        </span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full" style="width: {{ $progressPct }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Prestasi Kumulatif -->
                            <td class="py-3 px-3.5 text-center">
                                <span class="font-black text-amber-600 dark:text-amber-400 text-xs sm:text-sm tabular-num">+{{ $achPoints }}</span>
                            </td>

                            <!-- 5. Pelanggaran Kumulatif -->
                            <td class="py-3 px-3.5 text-center">
                                <span class="font-black text-rose-600 dark:text-rose-400 text-xs sm:text-sm tabular-num">{{ $violPoints }}</span>
                            </td>

                            <!-- 6. Status Pembinaan -->
                            <td class="py-3 px-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold whitespace-nowrap {{ $guidance['action_needed'] ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' }}">
                                    {{ $guidance['level'] }}
                                </span>
                            </td>

                            <!-- 7. Aksi Cepat -->
                            <td class="py-3 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('reports.student.dossier', $s->id) }}" class="px-2 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold text-[10.5px] transition whitespace-nowrap" title="Raport Digital & Dossier">
                                        Raport
                                    </a>
                                    <a href="{{ route('tahfidz.student', $s->id) }}" class="px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold text-[10.5px] transition whitespace-nowrap" title="Mutaba'ah Tahfidz">
                                        Tahfidz
                                    </a>
                                    <a href="{{ route('habits.journal', ['student_id' => $s->id]) }}" class="px-2 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 font-bold text-[10.5px] transition whitespace-nowrap" title="Jurnal 7 Kebiasaan">
                                        Karakter
                                    </a>
                                    <a href="{{ route('guidance.create', ['student_id' => $s->id]) }}" class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-300 font-bold text-[10.5px] transition whitespace-nowrap" title="Bimbing Siswa">
                                        Bina
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                Belum ada siswa terdaftar di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analisis Tata Tertib Kelas Binaan -->
    @include('dashboards.partials.discipline_analytics')

    <!-- Capaian 7 Kebiasaan Anak Hebat Kelas Binaan -->
    @include('dashboards.partials.habits_analytics')

    <!-- Ringkasan Ibadah Bersama & Tahfidz Al-Qur'an Kelas Binaan -->
    <!-- AGENDA & KEGIATAN SEKOLAH TERUPDATE -->
    @include('dashboards.partials.school_agendas_summary')

    @include('dashboards.partials.worship_tahfidz_summary')

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital -->
    @include('dashboards.partials.lms_library_summary')
</div>
@endsection
