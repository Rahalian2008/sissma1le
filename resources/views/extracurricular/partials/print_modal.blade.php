<!-- Modal Cetak Laporan Presensi Resmi Ekstrakurikuler (A4 KOP Standar Dinas) -->
<div id="printExtracurricularModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-cyan-400 font-bold text-lg">
                    🖨️
                </div>
                <div>
                    <h3 class="font-bold text-base text-white">Cetak Laporan Presensi Resmi</h3>
                    <p class="text-xs text-slate-300">Format dokumen standar dinas ber-KOP resmi A4</p>
                </div>
            </div>
            <button type="button" onclick="closePrintExtracurricularModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Filter Cetak -->
        <form action="{{ route('extracurricular.print') }}" method="GET" target="_blank" class="p-6 space-y-4 text-xs">
            <!-- Pilihan Periode (Radio Tabs) -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pilih Jenis Periode</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-cyan-600 bg-cyan-50/50 dark:bg-cyan-950/50 text-cyan-900 dark:text-cyan-300 font-bold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="daily" checked onchange="handleExtraPeriodChange('daily')" class="hidden">
                        <span>📅 Harian</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Per Tanggal</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="weekly" onchange="handleExtraPeriodChange('weekly')" class="hidden">
                        <span>🗓️ Mingguan</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">7 Hari / Minggu</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="monthly" onchange="handleExtraPeriodChange('monthly')" class="hidden">
                        <span>📊 Bulanan</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Per Bulan</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="custom" onchange="handleExtraPeriodChange('custom')" class="hidden">
                        <span>⚙️ Kustom</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Rentang Bebas</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Input Container based on Period Selection -->
            <!-- 1. Harian -->
            <div id="container-extra-daily" class="extra-period-container">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Tanggal Presensi</label>
                <input type="date" name="date" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->toDateString() }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
            </div>

            <!-- 2. Mingguan -->
            <div id="container-extra-weekly" class="extra-period-container hidden bg-slate-50 dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-600 dark:text-slate-300">Laporan mingguan akan mencetak rekapitulasi kehadiran ekstrakurikuler dalam 7 hari terakhir / minggu berjalan secara otomatis.</p>
            </div>

            <!-- 3. Bulanan -->
            <div id="container-extra-monthly" class="extra-period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-cyan-500 text-xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-cyan-500 text-xs">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- 4. Kustom -->
            <div id="container-extra-custom" class="extra-period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString() }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white outline-none focus:border-cyan-500 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->toDateString() }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white outline-none focus:border-cyan-500 text-xs">
                </div>
            </div>

            <!-- Target Ekstrakurikuler -->
            @php
                $modalExtras = $extras ?? \App\Models\Extracurricular::where('is_active', true)->orderBy('name')->get();
                $modalExtraId = $selectedExtraId ?? ($selectedExtra?->id ?? ($modalExtras->first()?->id ?? ''));
                $modalClasses = $classes ?? \App\Models\SchoolClass::orderBy('name')->get();
                $isWaliKelasModal = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
            @endphp
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Target Ekstrakurikuler</label>
                <select name="extra_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
                    @foreach($modalExtras as $ex)
                        <option value="{{ $ex->id }}" {{ (string)$modalExtraId === (string)$ex->id ? 'selected' : '' }}>
                            {{ $ex->name }} ({{ $ex->category }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Target Kelas (Pilihan Rombel) -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Target Kelas (Pilihan Rombel)</label>
                @if($isWaliKelasModal)
                    <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
                        @forelse($modalClasses as $c)
                            <option value="{{ $c->id }}" {{ (string)($selectedClassId ?? '') === (string)$c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                            </option>
                        @empty
                            <option value="">Belum ada kelas binaan</option>
                        @endforelse
                    </select>
                @else
                    <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
                        <option value="">Semua Rombel (Seluruh Siswa)</option>
                        @foreach($modalClasses as $c)
                            <option value="{{ $c->id }}" {{ (string)($selectedClassId ?? '') === (string)$c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closePrintExtracurricularModal()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold shadow-md shadow-cyan-600/30 flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Buka Dokumen Cetak (A4)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPrintExtracurricularModal() {
        document.getElementById('printExtracurricularModal')?.classList.remove('hidden');
    }

    function closePrintExtracurricularModal() {
        document.getElementById('printExtracurricularModal')?.classList.add('hidden');
    }

    function handleExtraPeriodChange(type) {
        document.querySelectorAll('.extra-period-container').forEach(c => c.classList.add('hidden'));
        const target = document.getElementById('container-extra-' + type);
        if (target) target.classList.remove('hidden');

        document.querySelectorAll('#printExtracurricularModal .period-option').forEach(opt => {
            const input = opt.querySelector('input');
            if (input && input.value === type) {
                opt.classList.add('border-cyan-600', 'bg-cyan-50/50', 'text-cyan-900');
                opt.classList.remove('border-slate-200', 'text-slate-700');
            } else {
                opt.classList.remove('border-cyan-600', 'bg-cyan-50/50', 'text-cyan-900');
                opt.classList.add('border-slate-200', 'text-slate-700');
            }
        });
    }
</script>

