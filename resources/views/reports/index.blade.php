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
            <a href="{{ route('reports.print.combined', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-white font-black rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition">
                📑 Cetak 1 Dokumen Terpadu
            </a>
            <a href="{{ route('reports.print.attendance', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-indigo-500 hover:bg-indigo-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak Presensi
            </a>
            <a href="{{ route('reports.print.discipline', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-purple-500 hover:bg-purple-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak Tata Tertib
            </a>
            <a href="{{ route('reports.print.habits', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="px-3.5 py-2 bg-teal-500 hover:bg-teal-400 text-white font-bold rounded-xl text-xs shadow-md flex items-center gap-1.5 transition">
                🖨️ Cetak 7 Kebiasaan
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

    <!-- Type Switcher Tabs (Touch-friendly & Horizontal Scrolling) -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 text-xs font-bold overflow-x-auto no-scrollbar whitespace-nowrap">
        <a href="{{ route('reports.index', ['type' => 'attendance']) }}"
            class="px-4 py-2 rounded-xl transition shrink-0 {{ $type === 'attendance' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Laporan Presensi
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
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $classes->first()?->name ?? 'Kelas Binaan' }}" class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl outline-none font-bold text-slate-700 cursor-not-allowed" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
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
                <span>1 DOKUMEN KOMPREHENSIF</span>
            </div>
            <h3 class="text-xl font-black text-white">Cetak 1 Dokumen (Presensi, Tata Tertib & 7 Kebiasaan)</h3>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                Menghasilkan 1 berkas rekapitulasi utuh siap cetak (Format A4 Landscape) yang menggabungkan seluruh metrik kehadiran siswa, akumulasi poin prestasi & pelanggaran tata tertib, serta persentase ketercapaian 7 Kebiasaan Anak Indonesia Hebat per kelas.
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-1">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Presensi Lengkap (H, T, I, S, A, %)</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Prestasi & Pelanggaran Terverifikasi</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-400"></span> Ketercapaian K1-K7 & Predikat Karakter</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
            <a href="{{ route('reports.print.combined', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black rounded-2xl shadow-lg shadow-emerald-500/20 text-xs sm:text-sm flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5">
                <span>🖨️ Buka & Cetak 1 Dokumen Terpadu (A4)</span>
            </a>
        </div>
    </div>

    <!-- Dossier Direct Access Table (Quick Print/View Dossier for any student) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Berkas Dossier Terpadu Siswa (Cetak Lengkap)</h3>
                <p class="text-xs text-slate-500">Kombinasi Presensi + Prestasi + Pelanggaran + 7 Kebiasaan + Pembinaan + AI Insight</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($students->take(6) as $st)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                    <div>
                        <div class="font-bold text-slate-900">{{ $st->name }}</div>
                        <div class="text-[11px] text-slate-400">Kelas {{ $st->schoolClass?->name }} • NIS: {{ $st->nis }}</div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('reports.student.dossier', $st->id) }}" class="px-2.5 py-1.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold rounded-lg text-[11px]">
                            Lihat
                        </a>
                        <a href="{{ route('reports.student.print', $st->id) }}" target="_blank" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-[11px]">
                            Cetak ↗
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
