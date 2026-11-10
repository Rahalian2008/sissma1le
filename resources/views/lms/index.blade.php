@extends('layouts.app')

@section('title', 'LMS - Learning Management System')

@php
    $currentTeacher = $currentTeacher ?? ($teacher ?? (auth()->user()?->teacher ?? \App\Models\Teacher::where('user_id', auth()->id())->first()));
@endphp

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')
    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <!-- Glow Orbs (Desktop only to prevent mobile GPU compositor crash) -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    E-Learning SMA Negeri 1 Lengkong • Permendikdasmen No. 13 Tahun 2025
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Learning Management System (LMS)
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Portal pembelajaran daring terpadu jenjang SMA: Modul & Video Interaktif, Pengumpulan Tugas, CBT Ujian Daring, Presensi Mapel Otomatis (Selfie/Manual), serta Pemantauan Capaian Nilai Siswa.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($isAdminOrKurikulum || $isTeacher)
                    <a href="{{ route('lms.master.subjects') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-white font-semibold text-xs sm:text-sm border border-slate-700 shadow-sm transition">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>{{ $isAdminOrKurikulum ? 'Kelola Mapel & Guru' : '+ Buat Penugasan Kelas' }}</span>
                    </a>
                @endif
                <div class="px-4 py-2.5 rounded-xl bg-indigo-600/30 border border-indigo-500/40 text-indigo-200 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60 sm:backdrop-blur-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran Aktif</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-1">{{ $totalCourses }} <span class="text-xs font-normal text-slate-400">Kelas Mapel</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60 sm:backdrop-blur-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Modul & Video</div>
                <div class="text-xl sm:text-2xl font-black text-cyan-400 mt-1">{{ $totalMaterials }} <span class="text-xs font-normal text-slate-400">Materi</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60 sm:backdrop-blur-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tugas Daring</div>
                <div class="text-xl sm:text-2xl font-black text-amber-400 mt-1">{{ $totalAssignments }} <span class="text-xs font-normal text-slate-400">Penugasan</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/80 sm:bg-slate-800/50 border border-slate-700/60 sm:backdrop-blur-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">CBT Kuis & Ujian</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-400 mt-1">{{ $totalQuizzes }} <span class="text-xs font-normal text-slate-400">Online</span></div>
            </div>
        </div>
    </div>

    <!-- Active Presensi Mapel Hari Ini & Upcoming Deadlines -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Presensi Mapel Hari Ini -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Presensi Pertemuan Mapel Hari Ini</h2>
                        <p class="text-xs text-slate-500">Sesi kehadiran mata pelajaran tatap muka / daring per pertemuan</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    {{ $todayAttendances->count() }} Pertemuan
                </span>
            </div>

            @if($todayAttendances->isEmpty())
                <div class="py-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada sesi presensi pertemuan mapel dibuka hari ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Guru pengampu dapat membuka sesi presensi pertemuan langsung dari menu kelas mata pelajaran.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($todayAttendances as $att)
                        <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-400/50 bg-slate-50/70 dark:bg-slate-800/40 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">
                                        {{ $att->courseClass->schoolClass->name }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $att->courseClass->subject->name }}</h4>
                                    <p class="text-xs text-slate-500">{{ $att->title }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $att->is_open ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-500/20 text-slate-500' }}">
                                    {{ $att->is_open ? 'Aktif' : 'Tutup' }}
                                </span>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-xs">
                                <span class="text-slate-400">⏰ {{ $att->start_time }} - {{ $att->end_time }} WIB</span>
                                <a href="{{ route('lms.attendances.show', ['courseClass' => $att->course_class_id, 'attendance' => $att->id]) }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Buka Presensi &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tugas Mendatang / Butuh Penilaian -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            @if($isStudent)
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Deadline Tugas Siswa</h2>
                            <p class="text-xs text-slate-500">Tugas yang perlu segera dikumpulkan</p>
                        </div>
                    </div>
                </div>

                @if($upcomingAssignments->isEmpty())
                    <div class="py-8 text-center text-slate-400 text-xs">
                        🎉 Tidak ada tenggat tugas dalam waktu dekat. Kerja bagus!
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach($upcomingAssignments as $asg)
                            @php
                                $sub = $asg->submissions->first();
                            @endphp
                            <a href="{{ route('lms.assignments.show', ['courseClass' => $asg->course_class_id, 'assignment' => $asg->id]) }}" class="block p-3 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-400/50 bg-slate-50/50 dark:bg-slate-800/40 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase truncate">{{ $asg->courseClass->subject->name }}</div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $asg->title }}</div>
                                        <div class="text-[11px] text-rose-500 font-semibold mt-0.5">Batas: {{ $asg->due_date->translatedFormat('d M, H:i') }} WIB</div>
                                    </div>
                                    <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold {{ $sub ? 'bg-emerald-500/15 text-emerald-600' : 'bg-amber-500/15 text-amber-600' }}">
                                        {{ $sub ? 'Sudah Dikumpul' : 'Belum' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Panel Guru & Penilaian</h2>
                            <p class="text-xs text-slate-500">Antrean koreksi tugas siswa</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 text-center">
                    <div class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $pendingGradingCount }}</div>
                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">Tugas Menunggu Dinilai</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Siswa yang telah mengirimkan berkas jawaban.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar Kelas Mapel -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form action="{{ route('lms.index') }}" method="GET" class="w-full flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mata pelajaran, guru, atau kelas..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            @if(!$isStudent)
                <select name="class_id" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="subject_group" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                <option value="">Semua Kelompok Mapel</option>
                <option value="Mata Pelajaran Wajib" {{ request('subject_group') == 'Mata Pelajaran Wajib' ? 'selected' : '' }}>Mata Pelajaran Wajib</option>
                <option value="Mata Pelajaran Pilihan" {{ request('subject_group') == 'Mata Pelajaran Pilihan' ? 'selected' : '' }}>Mata Pelajaran Pilihan</option>
                <option value="Muatan Lokal" {{ request('subject_group') == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'class_id', 'subject_group']))
                <a href="{{ route('lms.index') }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Course Classes Grid -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2 flex-wrap">
                    <span>Daftar Kelas & Mata Pelajaran</span>
                    @if($isStudent)
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            Aktif Sesuai Jadwal
                        </span>
                    @endif
                </h2>
                <span class="text-xs text-slate-500">
                    @if($isStudent)
                        Menampilkan {{ $courseClasses->count() }} mata pelajaran yang sedang aktif pada hari dan jam sekarang
                    @else
                        Menampilkan {{ $courseClasses->count() }} kursus pembelajaran
                    @endif
                </span>
            </div>
            @if($isAdminOrKurikulum || $isTeacher)
                <a href="{{ route('lms.master.subjects') }}" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ $isAdminOrKurikulum ? 'Atur Alokasi Guru ke Kelas' : '+ Tugaskan Diri ke Kelas Belajar' }}</span>
                </a>
            @endif
        </div>

        @if($courseClasses->isEmpty())
            @if($isStudent && $student && $allStudentCourses->isNotEmpty())
                <!-- Tampilan Khusus Siswa: Sedang di luar jam pelajaran aktif -->
                <div class="p-8 sm:p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="max-w-md mx-auto space-y-1.5">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tidak Ada Mata Pelajaran yang Sedang Berlangsung</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Mata pelajaran hanya akan tampil dan dapat dikerjakan saat jam pelajaran berlangsung (Hari & Jam). Jika waktu pelajaran telah selesai, mata pelajaran otomatis ditutup dari dashboard Anda.
                        </p>
                    </div>

                    <div class="pt-4 max-w-2xl mx-auto text-left">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 text-center sm:text-left">
                            📅 Jadwal Mingguan Kelas Anda ({{ $student->schoolClass->name ?? 'Kelas' }}):
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($allStudentCourses as $asc)
                                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $asc->subject->name }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400">{{ $asc->subject->code }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">{{ $asc->teacher->name ?? 'Guru Mapel' }}</p>
                                    <div class="pt-1 text-[11px] font-medium text-cyan-700 dark:text-cyan-300 flex items-center gap-1.5">
                                        <span>🗓️ {{ $asc->schedule_text }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Tidak ada mata pelajaran ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Mata pelajaran yang Anda cari belum tersedia atau belum ditugaskan ke kelas Anda.</p>
                    @if($isTeacher)
                        <div class="mt-4">
                            <a href="{{ route('lms.master.subjects') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tugaskan Diri ke Kelas Belajar Sekarang</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($courseClasses as $course)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-500/50 rounded-3xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300">
                        <!-- Card Banner Header with Dynamic Color -->
                        <div class="p-5 bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 text-white relative overflow-hidden">
                            <!-- Background pattern -->
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-indigo-500/10 group-hover:scale-125 transition-transform duration-500"></div>

                            <div class="flex items-start justify-between gap-2 relative z-10">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-white/20 text-white backdrop-blur-xs uppercase tracking-wide">
                                    {{ $course->schoolClass->name }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @if($course->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/30 text-emerald-200 border border-emerald-400/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/30 text-rose-200 border border-rose-400/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Non-Aktif
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/30 text-indigo-200">
                                        KKTP: {{ $course->getEffectiveKktp() }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 relative z-10">
                                <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">{{ $course->subject->group }}</span>
                                <h3 class="text-base font-black tracking-tight text-white line-clamp-1 group-hover:text-indigo-300 transition-colors">
                                    {{ $course->subject->name }}
                                </h3>
                                <p class="text-[11px] text-slate-300 mt-0.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="truncate">{{ $course->teacher->name }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Card Body Stats -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-4">
                            <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-800 text-center">
                                <div class="p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                    <div class="text-[10px] text-slate-400 font-semibold">Materi</div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $course->materials->count() }}</div>
                                </div>
                                <div class="p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                    <div class="text-[10px] text-slate-400 font-semibold">Tugas</div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $course->assignments->count() }}</div>
                                </div>
                                <div class="p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                    <div class="text-[10px] text-slate-400 font-semibold">Kuis</div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $course->quizzes->count() }}</div>
                                </div>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                                <div class="flex items-start gap-2">
                                    <svg class="w-3.5 h-3.5 text-indigo-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div class="text-[11px] leading-snug">
                                        <span class="font-bold text-slate-700 dark:text-slate-300">Jadwal:</span> 
                                        <span>{{ $course->schedule_text }}</span>
                                    </div>
                                </div>
                                @if($isStudent && $course->isCurrentlyActiveForStudent())
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>SEDANG BERLANGSUNG</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button Group (Buka/Masuk LMS, Edit, Toggle Aktif, Hapus) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <a href="{{ route('lms.course', $course->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs group-hover:bg-indigo-500 transition">
                                    <span>Buka / Masuk LMS</span>
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>

                                @php
                                    $activeTeacherModel = $currentTeacher ?? ($teacher ?? (auth()->user()?->teacher ?? \App\Models\Teacher::where('user_id', auth()->id())->first()));
                                    $canManageCourse = $isAdminOrKurikulum || ($activeTeacherModel && (int)$activeTeacherModel->id === (int)$course->teacher_id);
                                @endphp

                                @if($canManageCourse)
                                    <div class="grid grid-cols-3 gap-1.5 pt-1">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" onclick="openEditCourseModal({{ json_encode([
                                            'id' => $course->id,
                                            'subject_name' => $course->subject->name,
                                            'class_name' => $course->schoolClass->name,
                                            'day_of_week' => $course->day_of_week,
                                            'start_time' => $course->start_time,
                                            'end_time' => $course->end_time,
                                            'schedules' => $course->getSchedulesList(),
                                            'description' => $course->description,
                                            'kktp' => $course->getEffectiveKktp(),
                                            'is_active' => (bool)$course->is_active,
                                            'is_security_enabled' => (bool)$course->isSecurityEnabled(),
                                            'update_url' => route('lms.master.course_classes.update', $course->id),
                                        ]) }})" class="py-1.5 px-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 text-slate-700 dark:text-slate-300 hover:text-indigo-600 font-bold text-[11px] flex items-center justify-center gap-1 border border-slate-200 dark:border-slate-700 transition" title="Edit Jadwal & KKTP">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Toggle Status Aktif / Non Aktif -->
                                        <form action="{{ route('lms.master.course_classes.toggle_status', $course->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full py-1.5 px-2 rounded-xl {{ $course->is_active ? 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20' }} font-bold text-[11px] flex items-center justify-center gap-1 transition" title="{{ $course->is_active ? 'Nonaktifkan Kelas' : 'Aktifkan Kelas' }}">
                                                <span>{{ $course->is_active ? 'Nonaktif' : 'Aktifkan' }}</span>
                                            </button>
                                        </form>

                                        <!-- Hapus Kelas -->
                                        <form action="{{ route('lms.master.course_classes.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas mapel {{ $course->subject->name }} ({{ $course->schoolClass->name }})?');" class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full py-1.5 px-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 font-bold text-[11px] flex items-center justify-center gap-1 transition" title="Hapus Kelas">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Quick Toggle Fitur Keamanan Anti-Kecurangan Siswa -->
                                    <form action="{{ route('lms.security.toggle', $course->id) }}" method="POST" class="w-full pt-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 px-2 rounded-xl {{ $course->isSecurityEnabled() ? 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }} font-bold text-[11px] flex items-center justify-center gap-1.5 transition" title="{{ $course->isSecurityEnabled() ? 'Keamanan Siswa AKTIF. Klik untuk Nonaktifkan' : 'Keamanan Siswa NONAKTIF. Klik untuk Aktifkan' }}">
                                            <span>🛡️ Anti-Curang: {{ $course->isSecurityEnabled() ? 'Aktif' : 'Nonaktif' }}</span>
                                            <span class="text-[9px] px-1 rounded font-black {{ $course->isSecurityEnabled() ? 'bg-emerald-500 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $course->isSecurityEnabled() ? 'ON' : 'OFF' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT KELAS MAPEL & KKTP (LMS)        -->
<!-- ========================================== -->
<div id="modal-edit-course" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white" id="edit-course-title">Edit Kelas & Jadwal Belajar</h3>
                <p class="text-xs text-slate-500" id="edit-course-subtitle">Sesuaikan target KKTP, status aktif, dan waktu pembelajaran</p>
            </div>
            <button type="button" onclick="closeEditCourseModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">✕</button>
        </div>

        <form id="form-edit-course" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- KKTP & Status Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target KKTP (50 - 100) *</label>
                    <input type="number" name="kktp" id="edit_course_kktp" min="50" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <span class="text-[10px] text-slate-500">Nilai standar ketuntasan belajar siswa</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Aktivitas Kelas</label>
                    <select name="is_active" id="edit_course_is_active" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="1">Aktif (Dapat diakses)</option>
                        <option value="0">Non-Aktif (Diarsipkan)</option>
                    </select>
                    <span class="text-[10px] text-slate-500">Siswa hanya melihat kelas aktif</span>
                </div>
            </div>

            <!-- Jadwal Pembelajaran Multi-Hari -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Jadwal Hari & Jam Pembelajaran</label>
                    <button type="button" onclick="addEditScheduleRow()" class="text-[11px] font-bold text-indigo-600 hover:underline">+ Tambah Hari</button>
                </div>
                <div id="edit-schedule-rows-container" class="space-y-2">
                    <!-- Dinamis diisi JS -->
                </div>
            </div>

            <!-- Deskripsi / Pengumuman -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi / Pengantar Kelas</label>
                <textarea name="description" id="edit_course_description" rows="2" placeholder="Catatan pengantar atau silabus materi singkat..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <!-- Keamanan Anti-Curang Siswa -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Fitur Keamanan Siswa (Anti-Minimize & Kunci Tab)</label>
                <select name="is_security_enabled" id="edit_course_is_security_enabled" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <option value="1">Aktif (Blokir otomatis saat siswa berpindah tab/jendela)</option>
                    <option value="0">Non-Aktif (Siswa bebas membuka tab tanpa pembatasan)</option>
                </select>
                <span class="text-[10px] text-slate-500">Berlaku untuk sesi Materi Pelajaran, Tugas & PR, serta CBT</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeEditCourseModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditCourseModal(course) {
        const modal = document.getElementById('modal-edit-course');
        const form = document.getElementById('form-edit-course');
        form.action = course.update_url;

        document.getElementById('edit-course-title').innerText = 'Edit ' + course.subject_name + ' (' + course.class_name + ')';
        document.getElementById('edit_course_kktp').value = course.kktp || 75;
        document.getElementById('edit_course_is_active').value = course.is_active ? '1' : '0';
        document.getElementById('edit_course_is_security_enabled').value = course.is_security_enabled ? '1' : '0';
        document.getElementById('edit_course_description').value = course.description || '';

        const container = document.getElementById('edit-schedule-rows-container');
        container.innerHTML = '';

        const schedules = (course.schedules && course.schedules.length > 0) ? course.schedules : [{
            day: course.day_of_week || 'Senin',
            start_time: course.start_time || '07:30',
            end_time: course.end_time || '09:00'
        }];

        schedules.forEach(s => {
            addEditScheduleRow(s.day, s.start_time, s.end_time);
        });

        modal.classList.remove('hidden');
    }

    function closeEditCourseModal() {
        document.getElementById('modal-edit-course').classList.add('hidden');
    }

    function addEditScheduleRow(day = 'Senin', start = '07:30', end = '09:00') {
        const container = document.getElementById('edit-schedule-rows-container');
        const div = document.createElement('div');
        div.className = 'edit-schedule-row grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl border border-slate-200 dark:border-slate-700';
        
        const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        let optionsHtml = '';
        days.forEach(d => {
            optionsHtml += `<option value="${d}" ${d === day ? 'selected' : ''}>${d}</option>`;
        });

        div.innerHTML = `
            <div class="col-span-5">
                <select name="schedule_days[]" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1.5 font-bold text-slate-800 dark:text-slate-200">
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-span-3">
                <input type="time" name="schedule_starts[]" value="${start}" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1.5 text-slate-800 dark:text-slate-200">
            </div>
            <div class="col-span-3">
                <input type="time" name="schedule_ends[]" value="${end}" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1.5 text-slate-800 dark:text-slate-200">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="this.closest('.edit-schedule-row').remove()" class="text-slate-400 hover:text-rose-500 font-bold text-xs" title="Hapus jadwal">✕</button>
            </div>
        `;
        container.appendChild(div);
    }
</script>
@endsection
