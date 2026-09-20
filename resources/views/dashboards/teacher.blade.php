@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Guru -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
                Pendidik & Pembina Kesiswaan
            </span>
            <h2 class="text-2xl font-extrabold text-white mt-2">Selamat Datang, {{ $teacher->name ?? Auth::user()->name }}</h2>
            <p class="text-xs text-slate-300 mt-1">NIP: {{ $teacher->nip ?? '-' }} • {{ $teacher->title ?? 'Guru Mata Pelajaran' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.qr.session') }}" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-2xl shadow-lg shadow-emerald-500/20 text-xs sm:text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Mulai Sesi Presensi QR
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase">Siswa Hadir Hari Ini</div>
            <div class="text-3xl font-black text-slate-900 mt-2">{{ $todayPresentCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Presensi di seluruh sistem SMAN 1 Lengkong</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase">Prestasi Menunggu Verifikasi</div>
            <div class="text-3xl font-black text-amber-600 mt-2">{{ $pendingAchievements }}</div>
            <a href="{{ route('achievements.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-xs font-bold text-indigo-600 mt-1 inline-block">Buka Antrean Verifikasi →</a>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-500 uppercase">Pelanggaran Menunggu Verifikasi</div>
            <div class="text-3xl font-black text-rose-600 mt-2">{{ $pendingViolations }}</div>
            <a href="{{ route('violations.index', ['status' => 'MENUNGGU_VERIFIKASI']) }}" class="text-xs font-bold text-indigo-600 mt-1 inline-block">Buka Antrean Verifikasi →</a>
        </div>
    </div>

    <!-- Sesi Presensi QR Hari Ini & Shortcut Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Sesi Presensi QR Aktif Hari Ini</h3>
                <a href="{{ route('attendance.qr.session') }}" class="text-xs font-bold text-indigo-600">+ Buat Sesi Baru</a>
            </div>

            <div class="space-y-3">
                @forelse($activeSessions as $session)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700 text-xs font-bold">
                                Kelas {{ $session->schoolClass?->name ?? 'Semua' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-800 mt-1">{{ $session->subject_or_activity }}</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Waktu: {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
                        </div>
                        <a href="{{ route('attendance.qr.session', ['session_id' => $session->id]) }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-indigo-600 transition">
                            Tampilkan Proyektor QR
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada sesi presensi QR untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900">Aksi Cepat Pendidik</h3>
            <a href="{{ route('achievements.create') }}" class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-xs transition">
                <span>+</span> Input Capaian Prestasi Siswa
            </a>
            <a href="{{ route('violations.create') }}" class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-xs transition">
                <span>+</span> Catat Pelanggaran Tata Tertib
            </a>
            <a href="{{ route('attendance.history') }}" class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition">
                Lihat Rekapitulasi Presensi
            </a>
        </div>
    </div>
</div>
@endsection
