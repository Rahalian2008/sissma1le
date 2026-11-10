@php
    $attPeriod = $attPeriod ?? 'monthly';
    $stats = $attStats ?? [
        'total' => 0,
        'hadir' => 0,
        'terlambat' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alpa' => 0,
        'pulang_cepat' => 0,
        'rate' => 0.0,
    ];
    $attendances = $periodAttendances ?? collect();
    $rateColor = $stats['rate'] >= 90 ? 'text-emerald-600 dark:text-emerald-400' : ($stats['rate'] >= 75 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400');
    $rateBg = $stats['rate'] >= 90 ? 'bg-emerald-500' : ($stats['rate'] >= 75 ? 'bg-amber-500' : 'bg-rose-500');

    $wStats = $worshipStats ?? [
        'dhuha' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
        'dzuhur' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
        'keputrian' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
        'tahfidz' => [
            'period_count' => 0,
            'completed_surahs_count' => 0,
            'target_surahs_count' => 37,
            'progress_pct' => 0.0,
            'student_grade' => 'X',
            'records' => collect(),
        ],
        'all_worship_records' => collect(),
    ];
    $worshipRecords = $wStats['all_worship_records'] ?? collect();
    $tahfidzRecords = $wStats['tahfidz']['records'] ?? collect();

    $isFemale = $isFemale ?? (isset($student) && in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true));
    $hasExtracurriculars = $hasExtracurriculars ?? (isset($student) && !empty($student->extracurriculars) && $student->extracurriculars->isNotEmpty());
    $extraStats = $extraStats ?? [
        'total' => 0,
        'hadir' => 0,
        'terlambat' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alpa' => 0,
        'rate' => 0.0,
    ];
    $periodExtraAttendances = $periodExtraAttendances ?? collect();

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $currentYear = (int) date('Y');
@endphp

<div id="rekap-presensi" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-8 space-y-6 scroll-mt-6">
    <!-- Header Rekapitulasi Presensi -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-xs shadow-indigo-500/50"></span>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📊 Rekapitulasi Presensi Kehadiran Anak</span>
                </h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pantau rekapitulasi kehadiran ananda <strong class="text-slate-800 dark:text-slate-200">{{ $student?->name ?? 'Siswa' }}</strong> secara harian, mingguan, bulanan, atau rentang tanggal kustom pilihan Anda.
            </p>
        </div>

        <!-- Active Period Pill -->
        <div class="flex items-center gap-2 shrink-0">
            <div class="px-3.5 py-1.5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/80 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 text-xs font-black flex items-center gap-2 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span>{{ $periodLabel ?? 'Periode Aktif' }}</span>
            </div>
        </div>
    </div>

    <!-- Tab Filter Options & Form -->
    <div class="bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 space-y-4">
        <!-- Tab Buttons (Harian, Mingguan, Bulanan, Custom) -->
        <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mr-1 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Pilihan Periode:
            </span>

            <button type="button" onclick="switchAttPeriodTab('daily')" id="btn-tab-daily"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer {{ $attPeriod === 'daily' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                <span>☀️ Harian</span>
            </button>

            <button type="button" onclick="switchAttPeriodTab('weekly')" id="btn-tab-weekly"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer {{ $attPeriod === 'weekly' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                <span>📆 Mingguan</span>
            </button>

            <button type="button" onclick="switchAttPeriodTab('monthly')" id="btn-tab-monthly"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer {{ $attPeriod === 'monthly' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                <span>🗓️ Bulanan</span>
            </button>

            <button type="button" onclick="switchAttPeriodTab('custom')" id="btn-tab-custom"
                class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer {{ $attPeriod === 'custom' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                <span>⚙️ Costume / Kustom</span>
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ $formAction ?? route('dashboard') }}#rekap-presensi" id="form-att-period">
            @if(!empty($student?->nisn) || !empty($nisn))
                <input type="hidden" name="nisn" value="{{ $nisn ?? $student?->nisn }}">
            @endif
            <input type="hidden" name="att_period" id="input-att-period" value="{{ $attPeriod }}">

            <!-- Panel 1: Filter Harian -->
            <div id="panel-daily" class="tab-panel {{ $attPeriod === 'daily' ? '' : 'hidden' }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 max-w-xs">
                        <label for="att_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih Tanggal Presensi:
                        </label>
                        <input type="date" name="att_date" id="att_date" value="{{ $attDate ?? date('Y-m-d') }}"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('att_date').value='{{ date('Y-m-d') }}'; document.getElementById('form-att-period').submit();"
                            class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                            Hari Ini
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Tampilkan Harian
                        </button>
                    </div>
                </div>
            </div>

            <!-- Panel 2: Filter Mingguan -->
            <div id="panel-weekly" class="tab-panel {{ $attPeriod === 'weekly' ? '' : 'hidden' }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 max-w-xs">
                        <label for="att_week_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Pilih Tanggal dalam Minggu Tersebut:
                        </label>
                        <input type="date" name="att_week_date" id="att_week_date" value="{{ $attWeekDate ?? date('Y-m-d') }}"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('att_week_date').value='{{ date('Y-m-d') }}'; document.getElementById('form-att-period').submit();"
                            class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                            Minggu Ini
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Tampilkan Mingguan
                        </button>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                    💡 Rekap mingguan otomatis mengelompokkan catatan presensi dari hari <strong>Senin s.d. Minggu</strong> pada minggu yang dipilih.
                </p>
            </div>

            <!-- Panel 3: Filter Bulanan -->
            <div id="panel-monthly" class="tab-panel {{ $attPeriod === 'monthly' ? '' : 'hidden' }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="w-40">
                        <label for="att_month" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Bulan:
                        </label>
                        <select name="att_month" id="att_month"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @foreach($months as $num => $mName)
                                <option value="{{ $num }}" {{ (int)($attMonth ?? date('n')) === $num ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-28">
                        <label for="att_year" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tahun:
                        </label>
                        <select name="att_year" id="att_year"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @for($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                                <option value="{{ $y }}" {{ (int)($attYear ?? $currentYear) === $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('att_month').value='{{ date('n') }}'; document.getElementById('att_year').value='{{ date('Y') }}'; document.getElementById('form-att-period').submit();"
                            class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                            Bulan Ini
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Tampilkan Bulanan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Panel 4: Filter Costume / Kustom -->
            <div id="panel-custom" class="tab-panel {{ $attPeriod === 'custom' ? '' : 'hidden' }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 max-w-xs">
                        <label for="att_start_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Dari Tanggal (Mulai):
                        </label>
                        <input type="date" name="att_start_date" id="att_start_date" value="{{ $attStartDate ?? date('Y-m-d', strtotime('-14 days')) }}"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    </div>

                    <div class="flex-1 max-w-xs">
                        <label for="att_end_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Sampai Tanggal (Selesai):
                        </label>
                        <input type="date" name="att_end_date" id="att_end_date" value="{{ $attEndDate ?? date('Y-m-d') }}"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Terapkan Rentang Kustom
                        </button>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                    ⚙️ Tentukan rentang tanggal bebas untuk merekapitulasi riwayat kehadiran ananda sesuai kebutuhan evaluasi Anda.
                </p>
            </div>
        </form>
    </div>

    <!-- Bagian 1: 6 Metric Cards Presensi Kehadiran Sekolah -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🏫</span> Kehadiran Sekolah
            </span>
            <span class="text-[11px] text-slate-400">Total: {{ $stats['total'] }} Hari Tercatat</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
            <!-- 1. Persentase Kehadiran -->
            <div class="col-span-2 sm:col-span-3 lg:col-span-1 p-4 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-950/40 dark:to-indigo-900/30 border border-indigo-200/80 dark:border-indigo-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">% Kehadiran</span>
                    <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black">📈</span>
                </div>
                <div class="my-2">
                    <div class="text-2xl sm:text-3xl font-black {{ $rateColor }}">{{ $stats['rate'] }}%</div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 mt-1.5 overflow-hidden">
                        <div class="{{ $rateBg }} h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $stats['rate']) }}%"></div>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">
                    {{ $stats['hadir'] + $stats['terlambat'] }} dari {{ $stats['total'] }} hari hadir
                </div>
            </div>

            <!-- 2. Hadir Tepat Waktu (H) -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">Hadir (H)</span>
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">✓</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 my-1">
                    {{ $stats['hadir'] }}
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Tepat waktu</div>
            </div>

            <!-- 3. Terlambat (T) -->
            <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Terlambat (T)</span>
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">⏰</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 my-1">
                    {{ $stats['terlambat'] }}
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Presensi lewat batas</div>
            </div>

            <!-- 4. Izin (I) -->
            <div class="p-4 rounded-2xl bg-sky-50/60 dark:bg-sky-950/30 border border-sky-200/80 dark:border-sky-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-sky-700 dark:text-sky-300 uppercase tracking-wider">Izin (I)</span>
                    <span class="w-6 h-6 rounded-lg bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs font-bold">✉️</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 my-1">
                    {{ $stats['izin'] }}
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Izin terkonfirmasi</div>
            </div>

            <!-- 5. Sakit (S) -->
            <div class="p-4 rounded-2xl bg-violet-50/60 dark:bg-violet-950/30 border border-violet-200/80 dark:border-violet-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-violet-700 dark:text-violet-300 uppercase tracking-wider">Sakit (S)</span>
                    <span class="w-6 h-6 rounded-lg bg-violet-500/20 text-violet-600 dark:text-violet-400 flex items-center justify-center text-xs font-bold">🩺</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-violet-600 dark:text-violet-400 my-1">
                    {{ $stats['sakit'] }}
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Keterangan medis</div>
            </div>

            <!-- 6. Alpa (A) -->
            <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider">Alpa (A)</span>
                    <span class="w-6 h-6 rounded-lg bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold">⚠️</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 my-1">
                    {{ $stats['alpa'] }}
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Tanpa keterangan</div>
            </div>
        </div>
    </div>

    @if(($stats['pulang_cepat'] ?? 0) > 0)
        <div class="px-4 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-center gap-2">
            <span>⚠️</span>
            <span>Tercatat <strong>{{ $stats['pulang_cepat'] }} kali izin pulang cepat</strong> pada periode ini.</span>
        </div>
    @endif

    <!-- Bagian 2: 4 Metric Cards Presensi Ibadah Bersama & Tahfidz Al-Qur'an -->
    @php
        $dhuhaCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DHUHA);
        $dzuhurCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
        $keputrianCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_KEPUTRIAN);
    @endphp
    <div class="space-y-2 pt-4 border-t border-slate-200/80 dark:border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <span class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🕌</span> Presensi Ibadah Bersama & Tahfidz Al-Qur'an (Periode: {{ $periodLabel ?? 'Terpilih' }})
            </span>
            <span class="text-[11px] text-slate-400">Pembiasaan Karakter & Ibadah Siswa</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4">
            <!-- 1. Shalat Dhuha Bersama -->
            <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Shalat Dhuha</span>
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">☀️</span>
                </div>
                <div>
                    <div class="flex items-baseline justify-between">
                        <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">
                            {{ $wStats['dhuha']['rate'] }}%
                        </div>
                        <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/60 px-2 py-0.5 rounded-md uppercase">
                            {{ $dhuhaCfg['days_label'] }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $wStats['dhuha']['rate']) }}%"></div>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center justify-between">
                    <span>{{ $wStats['dhuha']['hadir'] }} dari {{ $wStats['dhuha']['total'] }} sesi hadir</span>
                    <span>Batas {{ $dhuhaCfg['end_time'] }} WIB</span>
                </div>
            </div>

            <!-- 2. Shalat Dzuhur / Jum'at Berjamaah -->
            <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200/80 dark:border-indigo-800/60 flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">Dzuhur / Jum'at</span>
                    <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">🕌</span>
                </div>
                <div>
                    <div class="flex items-baseline justify-between">
                        <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">
                            {{ $wStats['dzuhur']['rate'] }}%
                        </div>
                        <span class="text-[10px] font-bold text-indigo-800 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/60 px-2 py-0.5 rounded-md uppercase">
                            {{ $dzuhurCfg['days_label'] }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $wStats['dzuhur']['rate']) }}%"></div>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center justify-between">
                    <span>{{ $wStats['dzuhur']['hadir'] }} dari {{ $wStats['dzuhur']['total'] }} sesi hadir</span>
                    <span>Batas {{ $dzuhurCfg['end_time'] }} WIB</span>
                </div>
            </div>

            @if(!empty($isFemale) && $isFemale)
                <!-- 3. Presensi Kegiatan Keputrian (Hanya Tampil Jika Siswi Perempuan) -->
                <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/60 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider">Keputrian</span>
                        <span class="w-6 h-6 rounded-lg bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold">🌸</span>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">
                                {{ $wStats['keputrian']['rate'] }}%
                            </div>
                            <span class="text-[10px] font-bold text-rose-800 dark:text-rose-300 bg-rose-100 dark:bg-rose-900/60 px-2 py-0.5 rounded-md uppercase">
                                {{ $keputrianCfg['days_label'] }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-rose-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $wStats['keputrian']['rate']) }}%"></div>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center justify-between">
                        <span>{{ $wStats['keputrian']['hadir'] }} dari {{ $wStats['keputrian']['total'] }} sesi hadir</span>
                        <span>Batas {{ $keputrianCfg['end_time'] }} WIB</span>
                    </div>
                </div>
            @endif

            @if(!empty($hasExtracurriculars) && $hasExtracurriculars)
                {{-- 4. Presensi Ekstrakurikuler (Hanya Tampil Jika Mengikuti Ekskul) --}}
                <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/60 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Ekstrakurikuler</span>
                        <span class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">⚽</span>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <div class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400">
                                {{ $extraStats['rate'] }}%
                            </div>
                            <span class="text-[10px] font-bold text-blue-800 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/60 px-2 py-0.5 rounded-md uppercase">
                                {{ isset($student) ? $student->extracurriculars->count() : 0 }} Kegiatan Aktif
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $extraStats['rate']) }}%"></div>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center justify-between">
                        <span>{{ $extraStats['hadir'] + $extraStats['terlambat'] }} dari {{ $extraStats['total'] }} sesi hadir</span>
                        <span class="truncate max-w-[120px]" title="{{ isset($student) ? $student->extracurriculars->pluck('name')->join(', ') : '' }}">
                            {{ isset($student) ? $student->extracurriculars->pluck('name')->join(', ') : 'Ekskul' }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- 5. Tahfidz Al-Qur'an Juz 30 (Sesuai Target Kelas) -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/60 flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">Tahfidz Juz 30</span>
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">📖</span>
                </div>
                <div>
                    <div class="flex items-baseline justify-between">
                        <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ $wStats['tahfidz']['progress_pct'] }}%
                        </div>
                        <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/60 px-2 py-0.5 rounded-md uppercase">
                            Target Kelas {{ $wStats['tahfidz']['student_grade'] ?? 'X' }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $wStats['tahfidz']['progress_pct']) }}%"></div>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center justify-between">
                    <span>{{ $wStats['tahfidz']['completed_surahs_count'] }}/{{ $wStats['tahfidz']['target_surahs_count'] }} Surat Lulus</span>
                    <span>{{ $wStats['tahfidz']['period_count'] }} Setoran</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian 3: Tabel Catatan & Riwayat Kehadiran (Sekolah, Ibadah, Tahfidz) -->
    <div class="space-y-3 pt-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-200/60 dark:border-slate-800 pb-3">
            <div>
                <h4 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋 Catatan & Riwayat Kehadiran ({{ $attendances->count() }} Rekaman Sekolah)</span>
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Riwayat presensi sekolah, ibadah bersama, dan setoran tahfidz pada rentang {{ $periodLabel ?? 'terpilih' }}.
                </p>
            </div>

            <!-- Sub-Tabs Switcher for Logs -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="switchRecapLogTab('school')" id="btn-recap-log-school"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer bg-indigo-600 text-white shadow-xs">
                    <span>🏫 Presensi Sekolah</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-white/20 text-[10px] font-black">{{ $attendances->count() }}</span>
                </button>

                <button type="button" onclick="switchRecapLogTab('worship')" id="btn-recap-log-worship"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700">
                    <span>🕌 Presensi Ibadah</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-[10px] font-black">{{ $worshipRecords->count() }}</span>
                </button>

                @if(!empty($hasExtracurriculars) && $hasExtracurriculars)
                    <button type="button" onclick="switchRecapLogTab('extra')" id="btn-recap-log-extra"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700">
                        <span>⚽ Ekstrakurikuler</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-[10px] font-black">{{ $periodExtraAttendances->count() }}</span>
                    </button>
                @endif

                <button type="button" onclick="switchRecapLogTab('tahfidz')" id="btn-recap-log-tahfidz"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700">
                    <span>📖 Setoran Tahfidz</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-[10px] font-black">{{ $tahfidzRecords->count() }}</span>
                </button>
            </div>

            @if(Route::has('attendance.history') && Auth::check())
                <a href="{{ route('attendance.history') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition inline-flex items-center gap-1">
                    <span>Buka Riwayat Presensi Lengkap</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            @endif
        </div>

        <!-- Panel 1: Presensi Sekolah -->
        <div id="panel-recap-log-school" class="recap-log-panel">
            <div class="responsive-table-container border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-3.5">Tanggal & Hari</th>
                            <th class="py-3 px-3.5">Jam Masuk</th>
                            <th class="py-3 px-3.5">Jam Pulang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5">Metode</th>
                            <th class="py-3 px-3.5">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($attendances as $att)
                            @php
                                $attStatus = strtoupper($att->status);
                                $badgeClass = match($attStatus) {
                                    'HADIR' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300',
                                    'TERLAMBAT' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300',
                                    'IZIN' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300 border-sky-300',
                                    'SAKIT' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/80 dark:text-violet-300 border-violet-300',
                                    default => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300',
                                };
                                $dateCarbon = \Carbon\Carbon::parse($att->date, 'Asia/Jakarta');
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-extrabold text-slate-900 dark:text-white">
                                        {{ $dateCarbon->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $dateCarbon->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($att->time)
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                            {{ substr($att->time, 0, 5) }} WIB
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($att->time_out)
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                            {{ substr($att->time_out, 0, 5) }} WIB
                                        </span>
                                    @elseif($att->is_early_leave && $att->early_leave_time)
                                        <span class="font-mono font-bold text-amber-600 dark:text-amber-400">
                                            {{ substr($att->early_leave_time, 0, 5) }} (Pulang Cepat)
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $badgeClass }}">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-[10px]">
                                        {{ $att->method ?? 'MANUAL' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs text-slate-600 dark:text-slate-300">
                                    @if($att->notes)
                                        <p class="truncate" title="{{ $att->notes }}">{{ $att->notes }}</p>
                                    @elseif($att->early_leave_reason)
                                        <p class="truncate text-amber-600" title="{{ $att->early_leave_reason }}">Pulang cepat: {{ $att->early_leave_reason }}</p>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                        📅
                                    </div>
                                    <div class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum Ada Rekaman Presensi Sekolah</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Tidak ditemukan catatan kehadiran siswa pada rentang {{ $periodLabel ?? 'terpilih' }}.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel 2: Presensi Ibadah Bersama -->
        <div id="panel-recap-log-worship" class="recap-log-panel hidden">
            <div class="responsive-table-container border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-3.5">Tanggal & Hari</th>
                            <th class="py-3 px-3.5">Jenis Ibadah</th>
                            <th class="py-3 px-3.5">Waktu</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5">Petugas / Pencatat</th>
                            <th class="py-3 px-3.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($worshipRecords as $wRec)
                            @php
                                $wStatus = strtoupper($wRec->status);
                                $wBadgeClass = match($wStatus) {
                                    'HADIR' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300',
                                    'TERLAMBAT' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300',
                                    'HALANGAN' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300 border-purple-300',
                                    default => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300',
                                };
                                $actLabel = match($wRec->activity_type) {
                                    'DHUHA' => ['label' => 'Shalat Dhuha', 'icon' => '☀️', 'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800'],
                                    'DZUHUR_JUMAT' => ['label' => 'Dzuhur / Jum\'at', 'icon' => '🕌', 'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800'],
                                    'KEPUTRIAN' => ['label' => 'Keputrian', 'icon' => '🌸', 'color' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800'],
                                    default => ['label' => $wRec->activity_type, 'icon' => '✨', 'color' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'],
                                };
                                $dateCarbon = \Carbon\Carbon::parse($wRec->date, 'Asia/Jakarta');
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-extrabold text-slate-900 dark:text-white">
                                        {{ $dateCarbon->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $dateCarbon->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $actLabel['color'] }}">
                                        <span>{{ $actLabel['icon'] }}</span>
                                        <span>{{ $actLabel['label'] }}</span>
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($wRec->time)
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                            {{ substr($wRec->time, 0, 5) }} WIB
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $wBadgeClass }}">
                                        {{ $wRec->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $wRec->recorder?->name ?? 'Petugas / Guru' }}
                                </td>
                                <td class="py-3 px-3.5 max-w-xs text-slate-600 dark:text-slate-300">
                                    @if($wRec->notes)
                                        <p class="truncate" title="{{ $wRec->notes }}">{{ $wRec->notes }}</p>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                        🕌
                                    </div>
                                    <div class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum Ada Rekaman Presensi Ibadah</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Tidak ditemukan catatan shalat dhuha, dzuhur/jum'at, atau keputrian pada rentang {{ $periodLabel ?? 'terpilih' }}.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel 3: Setoran Tahfidz Qur'an -->
        <div id="panel-recap-log-tahfidz" class="recap-log-panel hidden">
            <div class="responsive-table-container border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-3.5">Tanggal Setoran</th>
                            <th class="py-3 px-3.5">Surat & Ayat</th>
                            <th class="py-3 px-3.5">Nilai Kriteria</th>
                            <th class="py-3 px-3.5 text-center">Hasil / Status</th>
                            <th class="py-3 px-3.5">Guru Penguji</th>
                            <th class="py-3 px-3.5">Catatan Penguji</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($tahfidzRecords as $tRec)
                            @php
                                $dateCarbon = \Carbon\Carbon::parse($tRec->deposit_date, 'Asia/Jakarta');
                                $isLulus = strtolower($tRec->overall_grade) === 'lulus';
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-extrabold text-slate-900 dark:text-white">
                                        {{ $dateCarbon->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ substr($tRec->deposit_time, 0, 5) }} WIB
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold">
                                            #{{ $tRec->surah_number }}
                                        </span>
                                        <span>{{ $tRec->surah_name }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        Ayat {{ $tRec->start_verse }} - {{ $tRec->end_verse }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-[10px]">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="Tajwid">
                                            Tajwid: <strong>{{ $tRec->tajweed_grade ?? '-' }}</strong>
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="Makhraj">
                                            Makhraj: <strong>{{ $tRec->makhraj_grade ?? '-' }}</strong>
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="Kelancaran">
                                            Lancar: <strong>{{ $tRec->fluency_grade ?? '-' }}</strong>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($isLulus)
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300">
                                            ✓ LULUS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300">
                                            {{ strtoupper($tRec->overall_grade ?? 'MENGULANG') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $tRec->examiner_name ?? $tRec->examiner?->name ?? 'Guru Penguji' }}
                                </td>
                                <td class="py-3 px-3.5 max-w-xs text-slate-600 dark:text-slate-300">
                                    @if($tRec->notes)
                                        <p class="truncate" title="{{ $tRec->notes }}">{{ $tRec->notes }}</p>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                        📖
                                    </div>
                                    <div class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum Ada Setoran Tahfidz</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Tidak ditemukan catatan setoran hafalan Al-Qur'an Juz 30 pada rentang {{ $periodLabel ?? 'terpilih' }}.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($hasExtracurriculars) && $hasExtracurriculars)
            {{-- Panel 4: Presensi Ekstrakurikuler --}}
            <div id="panel-recap-log-extra" class="recap-log-panel hidden">
                <div class="responsive-table-container border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-3.5">Tanggal & Hari</th>
                                <th class="py-3 px-3.5">Kegiatan Ekstrakurikuler</th>
                                <th class="py-3 px-3.5">Waktu</th>
                                <th class="py-3 px-3.5 text-center">Status</th>
                                <th class="py-3 px-3.5">Pembina / Pelatih</th>
                                <th class="py-3 px-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($periodExtraAttendances as $eRec)
                                @php
                                    $eStatus = strtoupper($eRec->status);
                                    $eBadgeClass = match($eStatus) {
                                        'HADIR' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300',
                                        'TERLAMBAT' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300',
                                        'IZIN' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300 border-sky-300',
                                        'SAKIT' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/80 dark:text-violet-300 border-violet-300',
                                        default => 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-300',
                                    };
                                    $dateCarbon = \Carbon\Carbon::parse($eRec->date, 'Asia/Jakarta');
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $dateCarbon->translatedFormat('l, d M Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $dateCarbon->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <span>⚽</span>
                                            <span>{{ $eRec->extracurricular?->name ?? 'Ekstrakurikuler' }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        @if($eRec->time)
                                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                                {{ substr($eRec->time, 0, 5) }} WIB
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $eBadgeClass }}">
                                            {{ $eRec->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap text-slate-700 dark:text-slate-300 font-medium">
                                        {{ $eRec->recorder?->name ?? ($eRec->extracurricular?->teacher?->name ?? 'Pembina / Pelatih') }}
                                    </td>
                                    <td class="py-3 px-3.5 max-w-xs text-slate-600 dark:text-slate-300">
                                        @if($eRec->notes)
                                            <p class="truncate" title="{{ $eRec->notes }}">{{ $eRec->notes }}</p>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                            ⚽
                                        </div>
                                        <div class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum Ada Rekaman Presensi Ekstrakurikuler</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Tidak ditemukan catatan kehadiran kegiatan ekstrakurikuler pada rentang {{ $periodLabel ?? 'terpilih' }}.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function switchAttPeriodTab(period) {
    const input = document.getElementById('input-att-period');
    if (input) input.value = period;

    const tabs = ['daily', 'weekly', 'monthly', 'custom'];
    tabs.forEach(t => {
        const btn = document.getElementById('btn-tab-' + t);
        const panel = document.getElementById('panel-' + t);
        if (t === period) {
            if (btn) {
                btn.className = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer bg-indigo-600 text-white shadow-xs';
            }
            if (panel) panel.classList.remove('hidden');
        } else {
            if (btn) {
                btn.className = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700';
            }
            if (panel) panel.classList.add('hidden');
        }
    });
}

function switchRecapLogTab(logTab) {
    const tabs = ['school', 'worship', 'extra', 'tahfidz'];
    tabs.forEach(t => {
        const btn = document.getElementById('btn-recap-log-' + t);
        const panel = document.getElementById('panel-recap-log-' + t);
        if (t === logTab) {
            if (btn) {
                btn.className = 'px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer bg-indigo-600 text-white shadow-xs';
            }
            if (panel) panel.classList.remove('hidden');
        } else {
            if (btn) {
                btn.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700';
            }
            if (panel) panel.classList.add('hidden');
        }
    });
}
</script>
