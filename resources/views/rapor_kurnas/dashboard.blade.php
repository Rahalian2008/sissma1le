@extends('layouts.app')

@section('title', 'Rapor Kurikulum Nasional (Kurnas) SMA')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Top Hero Banner Rapor Kurnas -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 border border-indigo-900/50 p-6 sm:p-8 shadow-2xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        e-Rapor SMA Standar Resmi Kemendikdasmen 2025
                    </span>
                    <span class="px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700 text-slate-300 text-xs font-medium">
                        Tahun Ajaran: <strong class="text-white">{{ $academicYear->name ?? '2025/2026' }}</strong> (Semester {{ $setting->semester == '1' ? '1 - Ganjil' : '2 - Genap' }})
                    </span>
                    @if($setting->is_locked)
                        <span class="px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold flex items-center gap-1">
                            🔒 Input Nilai Dikunci
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center gap-1">
                            🔓 Input Nilai Dibuka
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Aplikasi Rapor Kurnas SMA</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Sistem Rapor Kurikulum Nasional & Merdeka Terpadu Standar Direktorat SMA. Penilaian berbasis Tujuan Pembelajaran (TP), otomatisasi Deskripsi Capaian Kompetensi, Kokurikuler (P5 / Profil Lulusan), Kelengkapan Wali Kelas, serta Cetak Rapor Resmi & Transkrip Ijazah mandiri tanpa ketergantungan Dapodik.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                @if($canManage)
                    <form action="{{ route('rapor_kurnas.toggle_lock') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg transition cursor-pointer {{ $setting->is_locked ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-rose-600 hover:bg-rose-500 text-white' }}">
                            @if($setting->is_locked)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <span>Buka Kunci Input Nilai</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Kunci / Tutup Input Nilai</span>
                            @endif
                        </button>
                    </form>

                    <a href="{{ route('rapor_kurnas.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold text-xs sm:text-sm shadow-md transition">
                        <span>⚙️ Pengaturan</span>
                    </a>
                @endif

                <a href="{{ route('rapor_kurnas.print_hub') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                    <span>🖨️ Cetak Rapor</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik Utama Sesuai Peran Pengguna -->
    @if($canManage)
        <!-- Matriks 4 Kartu Administrator / Manajemen Sekolah -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- 1. Total Siswa Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Siswa Aktif</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalStudents) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl">
                        👨‍🎓
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>Terdaftar di {{ $totalClasses }} Rombel</span>
                </p>
            </div>

            <!-- 2. Rombongan Belajar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rombongan Belajar</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $totalClasses }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl">
                        🏫
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>Fase E (Kelas X) & Fase F (XI-XII)</span>
                </p>
            </div>

            <!-- 3. Progres Nilai Mapel Masuk -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Progres Nilai Mapel</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $scoredPercentage }}%</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl">
                        📈
                    </div>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $scoredPercentage }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                    {{ $scoredCoursesCount }} dari {{ $totalCourseClasses }} kelas mapel terisi
                </p>
            </div>

            <!-- 4. Progres Wali Kelas -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelengkapan Walas</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $completedHomeroomsCount }} / {{ $totalClasses }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl">
                        📋
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>Rekap Absensi, Catatan & Kenaikan</span>
                </p>
            </div>
        </div>
    @elseif($isCounselor)
        <!-- Matriks 4 Kartu Guru BK / Konselor -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Siswa Terdaftar</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($totalStudents) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Target Pantauan BK</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rombongan Belajar</p>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalClasses }} Rombel</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Kelas X, XI, XII</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tracker SNBP Siswa</p>
                <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">Multi-Semester</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Grafik Nilai Semester 1-6</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Buku Induk & Transkrip</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">Siap Akses</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Riwayat Lengkap Belajar</p>
            </div>
        </div>
    @else
        <!-- Matriks 4 Kartu Guru Mata Pelajaran / Wali Kelas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- 1. Rombel yang Diampu -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas yang Anda Ampu</p>
                        <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $myTotalCourses }} Kelas</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl">
                        📚
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>Semester Ini</span>
                </p>
            </div>

            <!-- 2. Progres Nilai Mapel Anda -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Progres Nilai Anda</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $myScoredPercentage }}%</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl">
                        📝
                    </div>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $myScoredPercentage }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                    {{ $myScoredCount }} dari {{ $myTotalCourses }} kelas mapel terisi
                </p>
            </div>

            <!-- 3. Kelas Binaan Wali Kelas -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kelas Binaan</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white mt-1 truncate">
                            {{ $myHomeroomClass ? $myHomeroomClass->name : 'Bukan Walas' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl">
                        🌟
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>{{ $myHomeroomClass ? 'Wali Kelas Resmi' : 'Guru Mata Pelajaran' }}</span>
                </p>
            </div>

            <!-- 4. Status Akses Pengisian -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Periode</p>
                        <p class="text-lg font-black {{ $setting->is_locked ? 'text-rose-600' : 'text-emerald-600' }} mt-1">
                            {{ $setting->is_locked ? 'Terkunci' : 'Dibuka' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xl">
                        {{ $setting->is_locked ? '🔒' : '🔓' }}
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                    <span>{{ $setting->is_locked ? 'Menunggu Tim Kurikulum' : 'Bisa Mengisi Nilai Rapor' }}</span>
                </p>
            </div>
        </div>
    @endif

    <!-- Quick Access Hub Disesuaikan Kapasitas Peran -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @if($isCounselor)
            <!-- Kartu BK 1: Perkembangan Nilai & SNBP Tracker -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
                        📉
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Perkembangan Nilai & SNBP</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Lacak grafik nilai rata-rata siswa semester 1 hingga semester 6, peringkat paralel siswa di angkatan, serta penentuan kuota siswa eligible SNBP (40% Akreditasi A).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('rapor_kurnas.growth') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition">
                        <span>Buka Perkembangan SNBP</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Kartu BK 2: Buku Induk & Leger -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                        📜
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Buku Induk & Rekam Jejak</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Akses rekam jejak akademik, ketidakhadiran kumulatif, serta catatan ekstrakurikuler peserta didik untuk keperluan konseling dan rekomendasi penjurusan.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('rapor_kurnas.transcripts') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition">
                        <span>Buka Buku Induk & Leger</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Kartu BK 3: Status Penilaian Live -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                        📈
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Live Monitoring Penilaian</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pantau progres kelengkapan nilai mata pelajaran dari seluruh guru untuk memastikan tidak ada siswa binaan yang tertinggal dalam penilaian rapor.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('rapor_kurnas.monitoring') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                        <span>Pantau Status Penilaian</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Kartu 1: Input Nilai Mapel Saya -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                        📝
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Input Nilai Guru Mapel</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Input nilai akhir skala 0-100, pilih ketercapaian Tujuan Pembelajaran (TP optimal & TP perlu peningkatan), dan generate deskripsi capaian otomatis.
                    </p>
                    @if(!empty($myCourseClasses) && count($myCourseClasses) > 0)
                        <div class="pt-2">
                            <p class="text-[11px] font-semibold text-slate-400 mb-1">Mata Pelajaran Anda ({{ count($myCourseClasses) }}):</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($myCourseClasses->take(3) as $cc)
                                    <span class="text-[10px] bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800">
                                        {{ $cc->subject->name ?? 'Mapel' }} ({{ $cc->schoolClass->name ?? '' }})
                                    </span>
                                @endforeach
                                @if(count($myCourseClasses) > 3)
                                    <span class="text-[10px] text-slate-400">+{{ count($myCourseClasses) - 3 }} lainnya</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('rapor_kurnas.scores') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                        <span>Buka Input Nilai Mapel</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Kartu 2: Wali Kelas / TP Mapel -->
            @if($isHomeroom)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                            📋
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Kelengkapan Wali Kelas</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Rekap ketidakhadiran (bisa ditarik otomatis 1-klik dari Presensi Harian), input catatan wali kelas, dan tentukan status kenaikan kelas / kelulusan.
                        </p>
                        @if($myHomeroomClass)
                            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                                <span>🌟</span>
                                <span>Kelas Binaan: <strong>{{ $myHomeroomClass->name }}</strong></span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('rapor_kurnas.homeroom') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition">
                            <span>Buka Menu Wali Kelas</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl">
                            🎯
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Tujuan Pembelajaran (TP)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Kelola bank Tujuan Pembelajaran untuk mata pelajaran yang Anda ampu pada Fase E (Kelas X) atau Fase F (Kelas XI & XII) sesuai kaidah e-Rapor 2025.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('rapor_kurnas.learning_goals') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold transition">
                            <span>Kelola Tujuan Pembelajaran</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Kartu 3: Cetak Rapor Resmi & Leger -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                        🖨️
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Pusat Cetak & Leger Nilai</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Cetak Sampul, Data Peserta Didik, Rapor Akademik, Rapor Kokurikuler (P5), serta Unduh Leger Nilai Kelas format standar dinas Kemendikbudristek.
                    </p>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700">
                        Format dinas: Tanda tangan Kepala Sekolah di posisi <strong>KIRI</strong>, Wali Kelas di posisi <strong>KANAN</strong>.
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('rapor_kurnas.print_hub') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition">
                        <span>Buka Pusat Cetak Rapor</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Alur Kerja e-Rapor Kurnas SMA (Berdasarkan Buku Panduan Resmi 2025) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <span>📖</span>
            <span>Alur Pelaksanaan Rapor Kurikulum Nasional (Panduan Resmi 2025)</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-xs">
            <!-- Langkah 1 -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-[11px]">1</div>
                <p class="font-bold text-slate-800 dark:text-white">Tujuan Pembelajaran</p>
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">
                    Guru menyusun TP per fase (E/F) dengan format huruf kecil tanpa frasa "siswa dapat".
                </p>
            </div>

            <!-- Langkah 2 -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-[11px]">2</div>
                <p class="font-bold text-slate-800 dark:text-white">Penilaian & Deskripsi</p>
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">
                    Input nilai akhir angka (0-100), centang TP optimal & minimal, lalu generate deskripsi capaian.
                </p>
            </div>

            <!-- Langkah 3 -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-[11px]">3</div>
                <p class="font-bold text-slate-800 dark:text-white">Ekskul & Kokurikuler</p>
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">
                    Pembina input nilai ekskul; Koordinator projek menilai dimensi capaian P5 (MB, SB, BSH, SAB).
                </p>
            </div>

            <!-- Langkah 4 -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-[11px]">4</div>
                <p class="font-bold text-slate-800 dark:text-white">Rekapitulasi Walas</p>
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">
                    Wali kelas menarik absensi otomatis, memberi catatan motivasi, dan menentukan kenaikan kelas.
                </p>
            </div>

            <!-- Langkah 5 -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-[11px]">5</div>
                <p class="font-bold text-slate-800 dark:text-white">Cetak Rapor & Leger</p>
                <p class="text-slate-500 dark:text-slate-400 text-[11px]">
                    Unduh dan cetak buku rapor lengkap, transkrip ijazah, dan lembar leger nilai resmi sekolah.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
