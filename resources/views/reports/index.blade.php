@extends('layouts.app')

@section('title', 'Pusat Laporan & Rekapitulasi')

@section('content')
<div class="space-y-6 max-w-7xl 2xl:max-w-[1600px] 3xl:max-w-[1880px] mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-800 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-teal-300 border border-white/10">
                PUSAT DATA & LAPORAN RESMI
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">Laporan Perkembangan Siswa</h2>
            <p class="text-xs text-slate-300 mt-1 max-w-xl leading-relaxed">
                Rekapitulasi terpusat untuk presensi, prestasi, pelanggaran tata tertib, dan berkas perkembangan siswa SMAN 1 Lengkong (Format Print, CSV/Excel).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('raport.index') }}"
                class="px-3.5 py-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-500 hover:to-pink-500 text-white font-black rounded-xl text-xs shadow-lg shadow-indigo-600/30 flex items-center gap-1.5 transition">
                ⚡ Raport Digital AI (8 Pilar)
            </a>
            <a href="{{ route('attendance.cards.index', ['class_id' => $classId]) }}"
                class="px-3.5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🪪 Cetak Kartu Siswa (RFID/QR)
            </a>
            <a href="{{ route('reports.print.combined', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-white font-black rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition">
                📑 Cetak 1 Dokumen (Presensi, Tata Tertib & 7 Kebiasaan)
            </a>
            <a href="{{ route('reports.print.attendance', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-indigo-500 hover:bg-indigo-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak Presensi Siswa
            </a>
            <a href="{{ route('reports.print.discipline', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-purple-500 hover:bg-purple-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak Tata Tertib
            </a>
            <a href="{{ route('reports.print.habits', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-teal-500 hover:bg-teal-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak 7 Kebiasaan
            </a>
            <a href="{{ route('worship.report', 'dhuha') }}"
                class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🕌 Laporan Dhuha
            </a>
            <a href="{{ route('worship.report', 'dzuhur-jumat') }}"
                class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🕌 Laporan Dzuhur/Jum'at
            </a>
            <a href="{{ route('worship.report', 'keputrian') }}"
                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                ✨ Laporan Keputrian
            </a>
            <a href="{{ route('tahfidz.report') }}"
                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                📖 Laporan Tahfidz Juz 30
            </a>
            <a href="{{ route('reports.user_activity') }}"
                class="px-3.5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                📊 Laporan Frekuensi Masuk & Aktivitas Sistem
            </a>
            <a href="{{ route('reports.export.csv', ['type' => $type, 'class_id' => $classId]) }}"
                class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh CSV
            </a>
        </div>
    </div>

    <!-- Type Switcher Tabs (Responsive & Multi-row Wrapping) -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 text-xs font-bold">
        <a href="{{ route('reports.index', ['type' => 'attendance']) }}"
            class="px-4 py-2 rounded-xl transition shrink-0 {{ $type === 'attendance' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Laporan Presensi Harian
        </a>
        <a href="{{ route('worship.report', 'dhuha') }}"
            class="px-4 py-2 rounded-xl transition shrink-0 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
            Laporan Shalat Dhuha
        </a>
        <a href="{{ route('worship.report', 'dzuhur-jumat') }}"
            class="px-4 py-2 rounded-xl transition shrink-0 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
            Laporan Shalat Dzuhur/Jum'at
        </a>
        <a href="{{ route('worship.report', 'keputrian') }}"
            class="px-4 py-2 rounded-xl transition shrink-0 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
            Laporan Keputrian
        </a>
        <a href="{{ route('tahfidz.report') }}"
            class="px-4 py-2 rounded-xl transition shrink-0 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
            Laporan Tahfidz (Juz 30)
        </a>
        <a href="{{ route('reports.index', ['type' => 'achievements']) }}"
            class="px-4 py-2 rounded-xl transition shrink-0 {{ $type === 'achievements' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Laporan Prestasi (R1-R10)
        </a>
        <a href="{{ route('reports.index', ['type' => 'violations']) }}"
            class="px-4 py-2 rounded-xl transition shrink-0 {{ $type === 'violations' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Laporan Pelanggaran (P1-P6)
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="type" value="{{ $type }}">
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            @php
                $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
            @endphp
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Pilihan Rombel Kelas</label>
                @if($isWaliKelasUser)
                    <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold text-slate-700">
                        @forelse($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @empty
                            <option value="">Belum ada kelas binaan</option>
                        @endforelse
                    </select>
                @else
                    <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            @endif
            @if($type === 'attendance')
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
            </div>
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
            </div>
            @endif
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition">Tampilkan</button>
            </div>
        </form>
    </div>

    <!-- Pilihan Cetak 1 Dokumen Terpadu (Presensi, Tata Tertib & 7 Kebiasaan) -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-teal-950 rounded-3xl p-6 sm:p-7 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                <span>📑 OPSI CETAK RESMI TERPADU</span>
                <span>•</span>
                <span>PILIHAN UKURAN KERTAS A4 & F4</span>
            </div>
            <h3 class="text-xl font-black text-white">Cetak 1 Dokumen Terpadu (Presensi, Tatib, Karakter, Ibadah & Tahfidz)</h3>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                Menghasilkan 1 berkas rekapitulasi utuh siap cetak yang menggabungkan seluruh metrik kehadiran siswa, akumulasi poin prestasi & pelanggaran tata tertib, persentase ketercapaian 7 Kebiasaan Anak Hebat, serta laporan presensi ibadah (Dzuhur, Dhuha, Keputrian) dan setoran hafalan Al-Qur'an per kelas. Diformat presisi untuk pilihan kertas <strong>A4</strong> atau <strong>F4 / Folio</strong>.
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-1">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Presensi Lengkap (H, T, I, S, A, %)</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Prestasi & Pelanggaran Terverifikasi</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-400"></span> Ketercapaian K1-K7 & Predikat Karakter</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-cyan-400"></span> Presensi Ibadah & Tahfidz Juz 30</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
            <a href="{{ route('reports.print.combined', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'a4']) }}" target="_blank"
                class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black rounded-2xl shadow-lg shadow-emerald-500/20 text-xs sm:text-sm flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5">
                <span>📄 Cetak A4 (297×210mm)</span>
            </a>
            <a href="{{ route('reports.print.combined', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate, 'paper' => 'f4']) }}" target="_blank"
                class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/20 text-xs sm:text-sm flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5">
                <span>📜 Cetak F4 / Folio (330×215mm)</span>
            </a>
        </div>
    </div>

    <!-- PUSAT CETAK KUSTOM SEMUA MODUL (CUSTOM PRINT GENERATOR) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-7">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-full text-[11px] font-bold uppercase tracking-wider">
                    🛠️ Generator Cetak Kustom
                </span>
                <h3 class="text-lg font-black text-slate-900 dark:text-white mt-1">Sesuaikan Dokumen & Ukuran Kertas Cetak</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih modul, kelas, rentang tanggal, serta ukuran kertas (A4 vs F4) sesuai kebutuhan administrasi sekolah.</p>
            </div>
        </div>

        <form id="customPrintForm" target="_blank" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">1. Pilih Dokumen / Modul</label>
                <select id="docModuleSelect" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                    <option value="combined">📑 Dokumen Terpadu (Presensi + Tatib + Karakter)</option>
                    <option value="attendance">📅 Presensi Harian Siswa</option>
                    <option value="dhuha">🕌 Presensi Shalat Dhuha Bersama</option>
                    <option value="dzuhur">🕌 Presensi Shalat Dzuhur / Jum'at</option>
                    <option value="keputrian">✨ Presensi Kegiatan Keputrian</option>
                    <option value="tahfidz">📖 Rekapitulasi Tahfidz Juz 30</option>
                    <option value="discipline">⚖️ Rekap Tata Tertib & Prestasi</option>
                    <option value="habits">🌟 Rekap 7 Kebiasaan Anak Indonesia</option>
                </select>
            </div>

            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">2. Target Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $classes->first()?->name ?? 'Kelas Binaan' }}" class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed" readonly>
                    <input type="hidden" id="customClassId" value="{{ $classId }}">
                @else
                    <select id="customClassId" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            @else
                <input type="hidden" id="customClassId" value="{{ $classId }}">
            @endif

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">3. Tanggal Mulai</label>
                <input type="date" id="customStartDate" value="{{ $startDate }}" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium outline-none text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">4. Tanggal Selesai</label>
                <input type="date" id="customEndDate" value="{{ $endDate }}" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium outline-none text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">5. Ukuran Kertas</label>
                <select id="customPaperSize" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-black outline-none text-indigo-600 dark:text-indigo-400">
                    <option value="a4">📄 A4 (210 × 297 mm)</option>
                    <option value="f4" selected>📜 F4 / Folio (215 × 330 mm)</option>
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="executeCustomPrint()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️ Buka & Cetak Dokumen Kustom (New Tab)</span>
                </button>
            </div>
        </form>

        <script>
            function executeCustomPrint() {
                const mod = document.getElementById('docModuleSelect').value;
                const classId = document.getElementById('customClassId') ? document.getElementById('customClassId').value : '';
                const start = document.getElementById('customStartDate').value;
                const end = document.getElementById('customEndDate').value;
                const paper = document.getElementById('customPaperSize').value;

                let url = '';
                if (mod === 'combined') {
                    url = `{{ route('reports.print.combined') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'attendance') {
                    url = `{{ route('reports.print.attendance') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'dhuha') {
                    url = `{{ route('worship.report.print', 'dhuha') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'dzuhur') {
                    url = `{{ route('worship.report.print', 'dzuhur-jumat') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'keputrian') {
                    url = `{{ route('worship.report.print', 'keputrian') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'tahfidz') {
                    url = `{{ route('tahfidz.report.print') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'discipline') {
                    url = `{{ route('reports.print.discipline') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                } else if (mod === 'habits') {
                    url = `{{ route('reports.print.habits') }}?class_id=${classId}&start_date=${start}&end_date=${end}&paper=${paper}`;
                }

                if (url) {
                    window.open(url, '_blank');
                }
            }
        </script>
    </div>

    <!-- Quick Cards Grid for New Modules (Worship & Tahfidz & Raport) -->
    @php
        $dhuhaCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DHUHA);
        $dzuhurCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
        $keputrianCfg = \App\Models\WorshipAttendance::getScheduleConfig(\App\Models\WorshipAttendance::ACTIVITY_KEPUTRIAN);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Shalat Dhuha -->
        <div class="bg-gradient-to-br from-amber-900/30 to-slate-900 border border-amber-500/30 rounded-3xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase text-amber-400">Presensi Ibadah</span>
                    <span class="text-xs bg-amber-500/20 text-amber-300 px-2.5 py-0.5 rounded-full font-bold">{{ $dhuhaCfg['days_label'] }}</span>
                </div>
                <h4 class="text-base font-black text-white">Shalat Dhuha Bersama</h4>
                <p class="text-xs text-slate-300 mt-1">Presensi bersama seluruh siswa pukul {{ $dhuhaCfg['start_time'] }} - {{ $dhuhaCfg['end_time'] }} WIB.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center gap-2">
                <a href="{{ route('worship.print', ['activity' => 'dhuha', 'class_id' => $classId, 'paper' => 'a4']) }}" target="_blank" class="flex-1 text-center py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition">
                    📄 Cetak A4
                </a>
                <a href="{{ route('worship.print', ['activity' => 'dhuha', 'class_id' => $classId, 'paper' => 'f4']) }}" target="_blank" class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 border border-amber-500/40 font-bold rounded-xl text-xs transition">
                    📜 Cetak F4
                </a>
            </div>
        </div>

        <!-- Shalat Dzuhur / Jum'at -->
        <div class="bg-gradient-to-br from-indigo-900/30 to-slate-900 border border-indigo-500/30 rounded-3xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase text-indigo-400">Presensi Ibadah</span>
                    <span class="text-xs bg-indigo-500/20 text-indigo-300 px-2.5 py-0.5 rounded-full font-bold">{{ $dzuhurCfg['days_label'] }}</span>
                </div>
                <h4 class="text-base font-black text-white">Shalat Dzuhur / Jum'at</h4>
                <p class="text-xs text-slate-300 mt-1">Siswa & siswi muslim pukul {{ $dzuhurCfg['start_time'] }} - {{ $dzuhurCfg['end_time'] }} WIB.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center gap-2">
                <a href="{{ route('worship.print', ['activity' => 'dzuhur-jumat', 'class_id' => $classId, 'paper' => 'a4']) }}" target="_blank" class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition">
                    📄 Cetak A4
                </a>
                <a href="{{ route('worship.print', ['activity' => 'dzuhur-jumat', 'class_id' => $classId, 'paper' => 'f4']) }}" target="_blank" class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-indigo-300 border border-indigo-500/40 font-bold rounded-xl text-xs transition">
                    📜 Cetak F4
                </a>
            </div>
        </div>

        <!-- Keputrian -->
        <div class="bg-gradient-to-br from-rose-900/30 to-slate-900 border border-rose-500/30 rounded-3xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase text-rose-400">Presensi Ibadah</span>
                    <span class="text-xs bg-rose-500/20 text-rose-300 px-2.5 py-0.5 rounded-full font-bold">{{ $keputrianCfg['days_label'] }}</span>
                </div>
                <h4 class="text-base font-black text-white">Kegiatan Keputrian</h4>
                <p class="text-xs text-slate-300 mt-1">Khusus siswi putri pukul {{ $keputrianCfg['start_time'] }} - {{ $keputrianCfg['end_time'] }} WIB.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center gap-2">
                <a href="{{ route('worship.print', ['activity' => 'keputrian', 'class_id' => $classId, 'paper' => 'a4']) }}" target="_blank" class="flex-1 text-center py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs transition">
                    📄 Cetak A4
                </a>
                <a href="{{ route('worship.print', ['activity' => 'keputrian', 'class_id' => $classId, 'paper' => 'f4']) }}" target="_blank" class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-rose-300 border border-rose-500/40 font-bold rounded-xl text-xs transition">
                    📜 Cetak F4
                </a>
            </div>
        </div>

        <!-- Tahfidz Juz 30 -->
        <div class="bg-gradient-to-br from-emerald-900/30 to-slate-900 border border-emerald-500/30 rounded-3xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase text-emerald-400">Tahfidz Digital</span>
                    <span class="text-xs bg-emerald-500/20 text-emerald-300 px-2.5 py-0.5 rounded-full font-bold">37 Surat</span>
                </div>
                <h4 class="text-base font-black text-white">Buku Kontrol Tahfidz Juz 30</h4>
                <p class="text-xs text-slate-300 mt-1">Rekapitulasi setoran hafalan, makhraj, tajwid, dan kelancaran.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center gap-2">
                <a href="{{ route('tahfidz.print', ['class_id' => $classId, 'paper' => 'a4']) }}" target="_blank" class="flex-1 text-center py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition">
                    📄 Cetak A4
                </a>
                <a href="{{ route('tahfidz.print', ['class_id' => $classId, 'paper' => 'f4']) }}" target="_blank" class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-emerald-300 border border-emerald-500/40 font-bold rounded-xl text-xs transition">
            </div>
        </div>
    </div>

    <!-- Dossier & Raport Digital Direct Access Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Raport Digital & Berkas Dossier Terpadu Siswa</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Satu kesatuan berkas dossier perkembangan dan raport komprehensif 8 pilar standar penilaian (Presensi Harian, Prestasi, Tatib, 7 Karakter Hebat KAIH, Shalat Dzuhur, Shalat Dhuha, Keputrian, Tahfidz Juz 30) + Evaluasi AI Multidimensi.</p>
            </div>
            <a href="{{ route('raport.index') }}" class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-bold rounded-xl text-xs hover:bg-indigo-100 transition inline-flex items-center gap-1.5 self-start">
                <span>📊 Buka Raport Digital AI & Dossier Siswa →</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($students->take(6) as $st)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between gap-3 text-xs">
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $st->name }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Kelas {{ $st->schoolClass?->name }} • NIS: {{ $st->nis }}</div>
                    </div>
                    <div class="flex items-center gap-1.5 pt-2 border-t border-slate-200 dark:border-slate-700 flex-wrap">
                        <a href="{{ route('raport.show', $st->id) }}" class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-[11px] transition">
                            Raport & Dossier AI
                        </a>
                        <a href="{{ route('raport.print', ['student' => $st->id, 'paper' => 'a4']) }}" target="_blank" class="px-2 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold rounded-lg text-[11px] transition">
                            🖨️ A4
                        </a>
                        <a href="{{ route('raport.print', ['student' => $st->id, 'paper' => 'f4']) }}" target="_blank" class="px-2 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold rounded-lg text-[11px] transition">
                            📜 F4
                        </a>
                        <a href="{{ route('tahfidz.student.print', ['student' => $st->id, 'paper' => 'f4']) }}" target="_blank" class="px-2 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 font-bold rounded-lg text-[11px] transition">
                            📖 Tahfidz
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
