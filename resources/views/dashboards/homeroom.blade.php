@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Wali Kelas -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-indigo-300 border border-white/10">
                Wali Kelas Binaan
            </span>
            <h2 class="text-2xl font-extrabold text-white mt-2">Kelas {{ $class->name ?? 'XI-IPA-1' }}</h2>
            <p class="text-xs text-slate-300 mt-1">Wali Kelas: {{ $teacher->name ?? Auth::user()->name }} • Total {{ $students->count() }} Siswa Binaan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index', ['class_id' => $class->id ?? null]) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs flex items-center gap-2 border border-white/20 transition">
                Cetak Rekap Kelas
            </a>
        </div>
    </div>

    <!-- Menu / Ikon Cepat Operasional Wali Kelas -->
    @include('dashboards.partials.staff_quick_actions')

    <!-- Attendance Summary for Class Today -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jumlah Siswa</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalStudents ?? $students->count() }} <span class="text-xs font-semibold text-slate-400">Siswa</span></div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Hadir</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $presentToday }} <span class="text-xs font-semibold text-emerald-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Terlambat</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $lateToday }} <span class="text-xs font-semibold text-amber-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Izin</span>
            <div class="text-2xl font-black text-sky-600 mt-1">{{ $permissionToday }} <span class="text-xs font-semibold text-sky-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sakit</span>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $sickToday }} <span class="text-xs font-semibold text-purple-500">Siswa</span></div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Alpa</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $alphaToday }} <span class="text-xs font-semibold text-rose-500">Siswa</span></div>
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
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Daftar Siswa Binaan Kelas {{ $class->name ?? '' }}</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring terpadu presensi multi-kegiatan, capaian Tahfidz Juz 30, serta tata tertib siswa.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 dark:text-slate-500">Poin Prestasi & Pelanggaran Dihitung Terpisah</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap lg:whitespace-normal">
                <thead class="bg-slate-50 dark:bg-slate-800/70 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Nama Siswa / NIS</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Presensi Hari Ini (Semua Jenis)</th>
                        <th class="py-3.5 px-4 min-w-[190px]">Hafalan Al-Qur'an (Juz 30)</th>
                        <th class="py-3.5 px-4">Prestasi Kumulatif</th>
                        <th class="py-3.5 px-4">Pelanggaran Kumulatif</th>
                        <th class="py-3.5 px-4">Status Pembinaan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
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
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $s->name }}</span>
                                    @if($s->gender === 'P')
                                        <span class="text-[9.5px] px-1 py-0.2 rounded bg-pink-50 text-pink-600 dark:bg-pink-950/50 dark:text-pink-300 font-bold" title="Perempuan">P</span>
                                    @else
                                        <span class="text-[9.5px] px-1 py-0.2 rounded bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300 font-bold" title="Laki-laki">L</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">NIS: {{ $s->nis }} • NISN: {{ $s->nisn }}</div>
                            </td>

                            <!-- 2. Presensi Hari Ini (Lengkap Semua Jenis) -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1.5 py-0.5">
                                    <!-- A. Presensi Utama KBM (Masuk & Pulang) -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 shrink-0" title="Presensi Masuk & Pulang KBM">
                                            🏫
                                        </span>
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
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold {{ $badgeStyle }}">
                                                KBM: {{ $att->status }} {{ $att->time ? '('.substr($att->time, 0, 5).')' : '' }}
                                            </span>
                                            @if($att->time_out)
                                                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200 dark:border-teal-800" title="Jam Kepulangan">
                                                    Plg: {{ substr($att->time_out, 0, 5) }}
                                                </span>
                                            @elseif($att->status === 'HADIR' || $att->status === 'TERLAMBAT')
                                                <span class="px-1.5 py-0.5 rounded-md text-[9.5px] font-semibold text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-dashed border-slate-200 dark:border-slate-700">
                                                    Belum Pulang
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                KBM: Belum Presensi
                                            </span>
                                        @endif
                                    </div>

                                    <!-- B. Presensi Ibadah (Dhuha, Dzuhur/Jum'at, Keputrian) & Ekskul -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <!-- Dhuha -->
                                        @if($dhuha)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $dhuha->status === 'HADIR' || $dhuha->status === 'TERLAMBAT' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}" title="Presensi Shalat Dhuha ({{ $dhuha->time ?? '-' }})">
                                                <span>☀️ Dhuha:</span>
                                                <span>{{ $dhuha->status }} {{ $dhuha->time ? '('.substr($dhuha->time, 0, 5).')' : '' }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-700" title="Belum / Tidak Shalat Dhuha">
                                                <span>☀️ Dhuha: -</span>
                                            </span>
                                        @endif

                                        <!-- Dzuhur / Jum'at -->
                                        @php
                                            $isFriday = (isset($today) ? date('N', strtotime($today)) : date('N')) == 5;
                                            $prayerName = $isFriday ? "Jum'at" : "Dzuhur";
                                        @endphp
                                        @if($dzuhur)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $dzuhur->status === 'HADIR' || $dzuhur->status === 'TERLAMBAT' ? 'bg-indigo-100 text-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}" title="Presensi Shalat {{ $prayerName }} ({{ $dzuhur->time ?? '-' }})">
                                                <span>🕌 {{ $prayerName }}:</span>
                                                <span>{{ $dzuhur->status }} {{ $dzuhur->time ? '('.substr($dzuhur->time, 0, 5).')' : '' }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-700" title="Belum / Tidak Shalat {{ $prayerName }}">
                                                <span>🕌 {{ $prayerName }}: -</span>
                                            </span>
                                        @endif

                                        <!-- Keputrian (khusus siswi perempuan jika ada sesi) -->
                                        @if($s->gender === 'P' && $keputrian)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-900 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800" title="Presensi Kegiatan Keputrian">
                                                <span>🌸 Keputrian: {{ $keputrian->status }}</span>
                                            </span>
                                        @endif

                                        <!-- Ekstrakurikuler (jika ada sesi hari ini) -->
                                        @if($extraAtt)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-900 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800" title="Presensi Ekstrakurikuler">
                                                <span>🎯 {{ $extraAtt->extracurricular->name ?? 'Ekskul' }}: {{ $extraAtt->status }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 3. Hafalan Al-Qur'an Juz 30 (Baru & Lengkap) -->
                            <td class="py-3.5 px-4 min-w-[190px]">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black text-slate-800 dark:text-slate-100">
                                            {{ $completedGradeCount }} / {{ $targetCount }} <span class="text-[10px] font-semibold text-slate-400">Surat (Target {{ $targetGrade }})</span>
                                        </span>
                                        <span class="text-[11px] font-black {{ $progressPct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($progressPct >= 50 ? 'text-teal-600 dark:text-teal-400' : 'text-amber-600 dark:text-amber-400') }}">
                                            {{ $progressPct }}%
                                        </span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5 border border-slate-200/60 dark:border-slate-700/60">
                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full transition-all duration-300" style="width: {{ $progressPct }}%"></div>
                                    </div>

                                    <!-- Setoran Terakhir atau Total Surat -->
                                    <div class="flex items-center justify-between text-[10.5px] pt-0.5">
                                        @if($latestTahfidz)
                                            <div class="truncate max-w-[130px]" title="Setoran Terakhir: Surat {{ $latestTahfidz->surah_name }} ({{ $latestTahfidz->ayat_range ?? 'Lengkap' }})">
                                                <span class="text-slate-400 font-medium">Terakhir:</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $latestTahfidz->surah_name }}</span>
                                            </div>
                                            <span class="px-1.5 py-0.2 rounded text-[9.5px] font-extrabold {{ $latestTahfidz->overall_grade === 'Lulus' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                                {{ $latestTahfidz->overall_grade }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[10px]">Belum ada setoran</span>
                                            <span class="text-[9.5px] text-slate-400 font-semibold">Total {{ $totalLulusAll }}/37</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Prestasi Kumulatif -->
                            <td class="py-3.5 px-4">
                                <span class="font-black text-amber-600 dark:text-amber-400 text-sm">{{ $achPoints }} Poin</span>
                                @if($reward['is_eligible'])
                                    <div class="text-[10px] text-amber-700 dark:text-amber-300 font-semibold">{{ $reward['level'] }}</div>
                                @endif
                            </td>

                            <!-- 5. Pelanggaran Kumulatif -->
                            <td class="py-3.5 px-4">
                                <span class="font-black text-rose-600 dark:text-rose-400 text-sm">{{ $violPoints }} Poin</span>
                            </td>

                            <!-- 6. Status Pembinaan -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold whitespace-nowrap {{ $guidance['action_needed'] ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' }}">
                                    {{ $guidance['level'] }}
                                </span>
                            </td>

                            <!-- 7. Aksi Cepat -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('reports.student.dossier', $s->id) }}" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold transition" title="Lihat Raport Digital AI & Berkas Dossier Siswa">
                                        Raport & Dossier
                                    </a>
                                    <a href="{{ route('tahfidz.student', $s->id) }}" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-lg text-xs font-semibold transition flex items-center gap-1" title="Buku Mutaba'ah & Setor Tahfidz Juz 30">
                                        Tahfidz
                                    </a>
                                    <a href="{{ route('habits.journal', ['student_id' => $s->id]) }}" class="p-1.5 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/50 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 rounded-lg text-xs font-semibold transition" title="Jurnal 7 Kebiasaan Anak Hebat">
                                        7 Kebiasaan
                                    </a>
                                    <a href="{{ route('guidance.create', ['student_id' => $s->id]) }}" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold transition" title="Input Catatan Pembinaan">
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
