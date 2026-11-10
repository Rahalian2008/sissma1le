@extends('layouts.app')

@section('title', 'Dashboard Wakasek Bid. Kurikulum - Akademik & KBM')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Wakasek Kurikulum -->
    <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-full text-xs font-bold tracking-wide">
                    BIDANG KURIKULUM & AKADEMIK
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    TA {{ $activeYear->name ?? '2026/2027' }} • SEMESTER AKTIF
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Wakasek Bid. Kurikulum</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Supervisi kegiatan belajar mengajar (KBM), ketercapaian kehadiran siswa per jenjang, integrasi Raport Digital AI, dan monitoring hafalan Al-Qur'an Juz 30.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('rapor_kurnas.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-emerald-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Rapor Kurnas
            </a>
            <a href="{{ route('lms.index') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-cyan-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                LMS Pembelajaran
            </a>
            <a href="{{ route('raport.index') }}" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-sky-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Raport Digital
            </a>
            <a href="{{ route('reports.print.class_recap') }}" target="_blank" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Rekap Kehadiran Kelas
            </a>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Tingkat Kehadiran KBM</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    ✓
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $attendanceRateToday }}%</div>
            <p class="text-xs text-slate-400 mt-1">{{ $presentToday }} Hadir • {{ $lateToday }} Terlambat hari ini</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Cakupan Raport AI</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                    🤖
                </div>
            </div>
            <div class="text-3xl font-black text-sky-600 dark:text-sky-400 mt-2">{{ $totalAiRaport }} <span class="text-xs font-normal text-slate-400">/ {{ $totalStudents }} Siswa</span></div>
            <p class="text-xs text-slate-400 mt-1">{{ $aiCompletionRate }}% telah dibuatkan analisis AI</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Total Rombel / Kelas</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    🏫
                </div>
            </div>
            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-2">{{ $totalClasses }} <span class="text-xs font-normal text-slate-400">Kelas</span></div>
            <p class="text-xs text-slate-400 mt-1">Tingkat X, XI, dan XII Terpadu</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Setoran Tahfidz Juz 30</span>
                <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                    📖
                </div>
            </div>
            <div class="text-3xl font-black text-teal-600 dark:text-teal-400 mt-2">{{ $totalTahfidzRecords }}</div>
            <p class="text-xs text-slate-400 mt-1">Total riwayat hafalan tersertifikasi</p>
        </div>
    </div>

    <!-- Attendance Breakdown per Jenjang (X, XI, XII) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Monitoring Presensi KBM per Tingkatan Jenjang</h2>
                <p class="text-xs text-slate-400">Tingkat kehadiran siswa pada jam belajar aktif hari ini</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-xs font-semibold">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(['X' => 'Kelas X (Fase E)', 'XI' => 'Kelas XI (Fase F)', 'XII' => 'Kelas XII (Fase F Tingkat Akhir)'] as $gKey => $gLabel)
                @php
                    $stat = $gradeStats[$gKey] ?? ['total' => 0, 'present' => 0, 'rate' => 0];
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-slate-800 dark:text-slate-100">{{ $gLabel }}</span>
                        <span class="font-black text-xs {{ $stat['rate'] >= 90 ? 'text-emerald-600' : ($stat['rate'] >= 75 ? 'text-amber-600' : 'text-rose-600') }}">{{ $stat['rate'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-sky-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $stat['rate']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                        <span>Hadir: <strong>{{ $stat['present'] }}</strong> siswa</span>
                        <span>Total: <strong>{{ $stat['total'] }}</strong> siswa</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Columns: Tahfidz Monitoring & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monitoring Hafalan Al-Qur'an Juz 30 -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Setoran Hafalan Tahfidz Terkini</h3>
                    <p class="text-xs text-slate-400">Catatan perkembangan surah & tajwid siswa</p>
                </div>
                <a href="{{ route('tahfidz.index') }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline">
                    Lihat Semua →
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($recentTahfidz as $th)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $th->student->name ?? 'Siswa' }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ $th->student->schoolClass->name ?? '-' }} • Surah: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $th->surah_name ?? 'Juz 30' }}</span> (Ayat {{ $th->verse_range ?? '-' }})
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ match($th->predicate ?? 'LANCAR') {
                            'MUMTAZ', 'SANGAT_BAIK' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300',
                            'JAYYID', 'BAIK' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                        } }}">
                            {{ $th->predicate ?? 'Tuntas' }}
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">Belum ada catatan setoran tahfidz hari ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Fasilitas Supervisi Kurikulum -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Pusat Layanan Supervisi Akademik</h3>
            <p class="text-xs text-slate-400">Tautan cepat evaluasi kurikulum, generate raport AI, dan dokumen resmi</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <a href="{{ route('rapor_kurnas.index') }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        📊
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Rapor Kurnas</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Penilaian e-Rapor SMA resmi</div>
                </a>

                <a href="{{ route('lms.index') }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-cyan-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-cyan-100 dark:bg-cyan-950 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        💻
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">LMS Pembelajaran</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Modul, tugas daring & ujian CBT</div>
                </a>

                <a href="{{ route('raport.index') }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-sky-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        🤖
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Raport Digital</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Analisis holistik capaian 8 pilar siswa</div>
                </a>

                <a href="{{ route('reports.print.combined') }}" target="_blank" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-sky-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        📑
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Laporan Terpadu 8 Pilar</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Rekap komprehensif sekolah</div>
                </a>

                <a href="{{ route('library.books.index') }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-amber-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        📚
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Perpustakaan</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Katalog buku fisik & E-Book Flip 3D</div>
                </a>

                <a href="{{ route('reports.index') }}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:border-teal-500 transition group">
                    <div class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                        🗂️
                    </div>
                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Adm. Ketatausahaan</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Pusat dokumen & arsip dossier</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital Kurikulum -->
    @include('dashboards.partials.lms_library_summary')
</div>
@endsection
