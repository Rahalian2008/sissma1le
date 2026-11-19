@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 max-w-7xl mx-auto px-1 xs:px-2 sm:px-0 overflow-x-hidden">
    <!-- Header Guru -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-white shadow-xl border border-slate-800">
        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
            Pendidik & Pembina Kesiswaan
        </span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-2">Selamat Datang, {{ $teacher->name ?? Auth::user()->name }}</h2>
        <p class="text-xs text-slate-300 mt-1">NIP: {{ $teacher->nip ?? '-' }} • {{ $teacher->title ?? 'Guru Mata Pelajaran' }}</p>
    </div>

    <!-- Menu Cepat Guru & Pembina -->
    @include('dashboards.partials.staff_quick_actions')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6">
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Siswa Hadir Hari Ini</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $todayPresentCount }}</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Presensi di seluruh sistem SMAN 1 Lengkong</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Prestasi Menunggu Verifikasi</div>
            <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $pendingAchievements }}</div>
            <a href="{{ route('achievements.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-1 inline-block">Buka Antrean Verifikasi →</a>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Pelanggaran Menunggu Verifikasi</div>
            <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 mt-2">{{ $pendingViolations }}</div>
            <a href="{{ route('violations.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-1 inline-block">Buka Antrean Verifikasi →</a>
        </div>
    </div>

    <!-- Sesi Presensi QR Hari Ini & Shortcut Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Sesi Presensi QR Aktif Hari Ini</h3>
                <a href="{{ route('attendance.qr.session') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">+ Buat Sesi Baru</a>
            </div>

            <div class="space-y-3">
                @forelse($activeSessions as $session)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 gap-3">
                        <div class="min-w-0">
                            <span class="px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                                Kelas {{ $session->schoolClass?->name ?? 'Semua' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mt-1 truncate">{{ $session->subject_or_activity }}</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-mono">Waktu: {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
                        </div>
                        <a href="{{ route('attendance.qr.session', ['session_id' => $session->id]) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition">
                            Tampilkan Proyektor QR
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs">
                        Belum ada sesi presensi QR untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3 sm:space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Aksi Cepat Pendidik</h3>
            <a href="{{ route('achievements.create') }}" class="w-full min-h-[44px] py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-xs transition">
                <span>+</span> Input Capaian Prestasi Siswa
            </a>
            <a href="{{ route('violations.create') }}" class="w-full min-h-[44px] py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-xs transition">
                <span>+</span> Catat Pelanggaran Tata Tertib
            </a>
            <a href="{{ route('attendance.history') }}" class="w-full min-h-[44px] py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition">
                Lihat Rekapitulasi Presensi
            </a>
        </div>
    </div>

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital Guru -->
    @include('dashboards.partials.lms_library_summary')

    <!-- Ringkasan Ibadah Bersama & Tahfidz Al-Qur'an (Khusus Koordinator Keagamaan) -->
    @if(Auth::user()->hasRole('koordinator_keagamaan'))
        <!-- AGENDA & KEGIATAN SEKOLAH TERUPDATE -->
        @include('dashboards.partials.school_agendas_summary')

        @include('dashboards.partials.worship_tahfidz_summary')
    @endif
</div>
@endsection
