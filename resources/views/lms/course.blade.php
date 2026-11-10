@extends('layouts.app')

@section('title', $courseClass->subject->name . ' - ' . $courseClass->schoolClass->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lms.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Dashboard LMS</span>
        </a>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
            {{ $courseClass->academicYear->name ?? 'Tahun Pelajaran Berjalan' }}
        </span>
    </div>

    <!-- Course Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-10 -right-10 w-52 h-52 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-sm uppercase tracking-wider">
                        {{ $courseClass->schoolClass->name }}
                    </span>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/10 text-cyan-300 backdrop-blur-xs">
                        {{ $courseClass->subject->group }}
                    </span>
                    <div class="inline-flex items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            KKTP: {{ $courseClass->getEffectiveKktp() }}
                        </span>
                        @if($canManage)
                            <button type="button" onclick="document.getElementById('modal-edit-kktp').classList.remove('hidden')" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-500/30 hover:bg-indigo-500/50 text-indigo-200 border border-indigo-400/30 inline-flex items-center gap-1 transition shadow-xs" title="Edit Kriteria Ketercapaian Tujuan Pembelajaran">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit KKTP</span>
                            </button>
                        @endif
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    {{ $courseClass->subject->name }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-300">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                            {{ strtoupper(substr($courseClass->teacher->name, 0, 1)) }}
                        </div>
                        <span>Guru Pengampu: <strong>{{ $courseClass->teacher->name }}</strong></span>
                    </div>
                    <span class="text-slate-500">•</span>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Jadwal: {{ $courseClass->day_of_week }} ({{ $courseClass->start_time }} - {{ $courseClass->end_time }} WIB)</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons for Teacher / Admin -->
            @if($canManage)
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="openModalImportFromClass()" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition" title="Ambil materi, tugas, dan butir asesmen dari kelas lain yang Anda ampu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        <span>📥 Ambil dari Kelas Lain</span>
                    </button>
                    <div class="relative inline-block text-left" id="dropdown-lms-package-wrapper">
                        <button type="button" onclick="togglePackageDropdown()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center gap-1.5 transition border border-slate-200 dark:border-slate-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Paket Kelas</span>
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dropdown-lms-package-menu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl z-30 py-1.5 text-xs">
                            <a href="{{ route('lms.export_package', $courseClass->id) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 transition">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Ekspor Paket (.JSON)</span>
                            </a>
                            <button type="button" onclick="closePackageDropdown(); document.getElementById('modal-import-package-json').classList.remove('hidden')" class="w-full text-left flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 transition">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Impor Paket (.JSON)</span>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-add-material').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Materi</span>
                    </button>
                    <button type="button" onclick="document.getElementById('modal-add-assignment').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Tugas</span>
                    </button>
                    <button type="button" onclick="document.getElementById('modal-add-attendance').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buka Presensi</span>
                    </button>
                    <button type="button" onclick="document.getElementById('modal-add-zoom-meeting').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Buat Zoom Meet</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Live Banner Zoom Meet (Jika Ada Sesi Berlangsung) -->
    @if($activeZoomMeeting)
        <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 border border-blue-400/30">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="relative flex h-4 w-4 mt-0.5 sm:mt-0 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white"></span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black tracking-wider bg-red-500/90 text-white uppercase animate-pulse">LIVE SEKARANG</span>
                        <h3 class="text-sm sm:text-base font-extrabold text-white">{{ $activeZoomMeeting->title }}</h3>
                    </div>
                    <p class="text-xs text-blue-100 mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                        <span>{{ $activeZoomMeeting->topic ?: 'Tatap Muka Online Kelas' }}</span>
                        <span>&bull; Mulai: {{ $activeZoomMeeting->start_time->format('H:i') }} WIB</span>
                        @if($activeZoomMeeting->meeting_id)
                            <span>&bull; ID: <span class="font-mono font-bold bg-white/20 px-1.5 py-0.2 rounded">{{ $activeZoomMeeting->meeting_id }}</span></span>
                        @endif
                        @if($activeZoomMeeting->passcode)
                            <span>&bull; Passcode: <span class="font-mono font-bold bg-white/20 px-1.5 py-0.2 rounded">{{ $activeZoomMeeting->passcode }}</span></span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                <a href="{{ route('lms.zoom.join', ['courseClass' => $courseClass->id, 'meeting' => $activeZoomMeeting->id]) }}" target="_blank" class="px-5 py-2.5 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 font-black text-xs sm:text-sm flex items-center gap-2 shadow-md hover:scale-105 transition transform">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Masuk ke Ruang Zoom</span>
                </a>
                @if($canManage)
                    <form action="{{ route('lms.zoom.toggle_status', ['courseClass' => $courseClass->id, 'meeting' => $activeZoomMeeting->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri sesi tatap muka ini?')">
                        @csrf
                        <button type="submit" class="px-3.5 py-2.5 rounded-2xl bg-red-600/80 hover:bg-red-700 text-white font-bold text-xs flex items-center gap-1.5 transition" title="Akhiri Sesi Zoom">
                            <span>Akhiri Sesi</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    @if($canManage)
        @include('lms.security.unblock_panel', ['courseClass' => $courseClass])
    @endif

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'materi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Materi Pelajaran</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'materi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $materials->flatten()->count() }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'tugas' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Tugas & PR</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'tugas' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $assignments->count() }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'kuis' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Asesmen & CBT</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'kuis' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $quizzes->count() }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'presensi']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'presensi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Presensi Mapel</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'presensi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $attendances->count() }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'nilai']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'nilai' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Pemantauan Nilai</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'nilai' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ count($gradebook) }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'diskusi']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'diskusi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Forum Diskusi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'diskusi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $discussions->count() }}</span>
        </a>

        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'zoom']) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 {{ $activeTab === 'zoom' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span>Tatap Muka / Zoom</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'zoom' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">{{ $zoomMeetings->count() }}</span>
        </a>

        @if($canManage)
        <a href="{{ route('lms.journals.index', ['course_class_id' => $courseClass->id]) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
            <span>📖</span>
            <span>Jurnal Mengajar</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">{{ $teachingJournals->count() }}</span>
        </a>
        @endif
    </div>

    <!-- TAB 1: MATERI PELAJARAN -->
    @if($activeTab === 'materi')
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Modul & Bahan Belajar Siswa</h2>
                    <p class="text-xs text-slate-500">Materi tersusun per bab/topik: dokumen PDF, modul presentasi, video interaktif & rangkuman</p>
                </div>
                @if($canManage)
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModalTab('modal-add-material', 'ai')" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                            <span>✨ Generate Modul Interaktif AI</span>
                            <span class="text-[9.5px] bg-white/20 px-1.5 py-0.5 rounded-full font-black uppercase tracking-wider">Permendikdasmen 13/2025</span>
                        </button>
                        <button type="button" onclick="openModalTab('modal-add-material', 'manual')" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Unggah Manual</span>
                        </button>
                    </div>
                @endif
            </div>

            @if($student)
                <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/70 dark:border-indigo-800/70 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 text-sm font-black shadow-xs">
                        🔄
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-indigo-950 dark:text-indigo-200">Alur Belajar Materi Berurutan Aktif</h4>
                        <p class="text-[11px] text-indigo-700 dark:text-indigo-300">Materi pelajaran disusun secara bertahap. Siswa wajib menyelesaikan materi sebelumnya terlebih dahulu untuk membuka materi selanjutnya.</p>
                    </div>
                </div>
            @endif

            @if($materials->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada materi pelajaran yang diunggah untuk kelas ini.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($materials as $topic => $topicMaterials)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-xs">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $topic }}</h3>
                                </div>
                                <span class="text-xs text-slate-400 font-semibold">{{ $topicMaterials->count() }} Bahan Belajar</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                @foreach($topicMaterials as $mat)
                                    @php
                                        $read = $mat->reads->first();
                                        $isUnlocked = $mat->is_unlocked ?? true;
                                        $isCompleted = $mat->is_completed ?? ($read !== null);
                                    @endphp
                                    <div class="p-4 rounded-2xl border {{ $isUnlocked ? 'border-slate-200 dark:border-slate-800 hover:border-indigo-500/40 bg-slate-50/50 dark:bg-slate-800/40' : 'border-dashed border-slate-300 dark:border-slate-700 bg-slate-100/50 dark:bg-slate-900/50 opacity-80' }} transition flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    @if(isset($mat->sequence_num))
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                            #{{ $mat->sequence_num }}
                                                        </span>
                                                    @endif
                                                    @php
                                                        $badgeColor = match($mat->content_type) {
                                                            'video' => 'bg-rose-500/20 text-rose-600 dark:text-rose-400',
                                                            'document' => 'bg-blue-500/20 text-blue-600 dark:text-blue-400',
                                                            'link' => 'bg-purple-500/20 text-purple-600 dark:text-purple-400',
                                                            default => 'bg-cyan-500/20 text-cyan-600 dark:text-cyan-400',
                                                        };
                                                        $isInteractive = str_starts_with(trim($mat->content ?? ''), '{') && str_contains($mat->content ?? '', 'kartu_konsep');
                                                    @endphp
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $badgeColor }}">
                                                        {{ $mat->content_type }}
                                                    </span>
                                                    @if($isInteractive)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-400/30">
                                                            ✨ 13/2025
                                                        </span>
                                                    @endif
                                                    @if($mat->learningObjective)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-400/30" title="{{ $mat->learningObjective->title }}">
                                                            🎯 TP {{ $mat->learningObjective->tp_code }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($student)
                                                    @if(! $isUnlocked)
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                            🔒 Terkunci
                                                        </span>
                                                    @elseif($isCompleted)
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-600">
                                                            ✓ Selesai Dipelajari
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-600 animate-pulse">
                                                            ▶ Siap Dipelajari
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <h4 class="text-sm font-bold {{ $isUnlocked ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400' }} mt-2 line-clamp-2">
                                                {{ $mat->title }}
                                            </h4>

                                            @if($mat->start_time)
                                                <div class="mt-1 flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold">
                                                    <span>⏰ Mulai: {{ $mat->start_time->translatedFormat('d M Y H:i') }} WIB</span>
                                                </div>
                                            @endif

                                            @if($mat->description)
                                                <p class="text-xs text-slate-500 line-clamp-2 mt-1">{{ $mat->description }}</p>
                                            @endif

                                            @if($student && ! $isUnlocked)
                                                @if($mat->is_time_locked ?? false)
                                                    <div class="mt-2.5 p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-[11px] text-indigo-700 dark:text-indigo-300 font-semibold flex items-center gap-1.5">
                                                        <span class="shrink-0">⏳</span>
                                                        <span>Materi baru dapat dibuka pada <strong>{{ $mat->formattedStartTime() }} WIB</strong></span>
                                                    </div>
                                                @elseif($mat->prerequisite_title)
                                                    <div class="mt-2.5 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-700 dark:text-amber-300 font-semibold flex items-center gap-1.5">
                                                        <span class="shrink-0">⚠️</span>
                                                        <span class="truncate">Selesaikan materi <strong>{{ $mat->prerequisite_title }}</strong> terlebih dahulu.</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                                            @if(! $student || $isUnlocked)
                                                <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $mat->id]) }}" class="px-3.5 py-1.5 rounded-xl {{ $isCompleted ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white font-bold text-xs inline-flex items-center gap-1.5 transition">
                                                    <span>{{ $isCompleted ? 'Buka Ulang Pelajaran' : 'Buka Pelajaran' }}</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </a>
                                            @else
                                                <button type="button" disabled class="px-3.5 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 font-bold text-xs inline-flex items-center gap-1.5 cursor-not-allowed" title="{{ ($mat->is_time_locked ?? false) ? 'Materi baru dapat dibuka pada ' . $mat->formattedStartTime() : 'Selesaikan materi sebelumnya terlebih dahulu' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Terkunci</span>
                                                </button>
                                            @endif

                                            @if($canManage)
                                                <div class="flex items-center gap-2">
                                                    <button type="button" onclick="openEditMaterialModal({{ json_encode([
                                                        'id' => $mat->id,
                                                        'title' => $mat->title,
                                                        'topic' => $mat->topic,
                                                        'content_type' => $mat->content_type,
                                                        'description' => $mat->description,
                                                        'external_url' => $mat->external_url,
                                                        'start_time' => $mat->start_time ? $mat->start_time->format('Y-m-d\TH:i') : '',
                                                        'file_name' => $mat->file_name ?: basename($mat->file_path),
                                                        'is_published' => (bool) $mat->is_published,
                                                        'update_url' => route('lms.materials.update', ['courseClass' => $courseClass->id, 'material' => $mat->id])
                                                    ]) }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold inline-flex items-center gap-1 transition" title="Edit materi ini">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button type="button" onclick="openCopyModal('material', {{ $mat->id }}, '{{ addslashes($mat->title) }}')" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-300 font-bold inline-flex items-center gap-1 transition" title="Salin materi ini ke kelas lain">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                        <span>Salin</span>
                                                    </button>
                                                    <form action="{{ route('lms.materials.destroy', ['courseClass' => $courseClass->id, 'material' => $mat->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1">Hapus</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: TUGAS & PR -->
    @if($activeTab === 'tugas')
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Penugasan & Pengumpulan PR Daring</h2>
                    <p class="text-xs text-slate-500">Tempat pengumpulan berkas jawaban tugas dengan batas waktu terstandar</p>
                </div>
                @if($canManage)
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModalTab('modal-add-assignment', 'ai')" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                            <span>✨ Generate Tugas & PR AI</span>
                            <span class="text-[9.5px] bg-white/20 px-1.5 py-0.5 rounded-full font-black uppercase tracking-wider">Permendikdasmen 13/2025</span>
                        </button>
                        <button type="button" onclick="openModalTab('modal-add-assignment', 'manual')" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Buat Manual</span>
                        </button>
                    </div>
                @endif
            </div>

            @if($student)
                <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/70 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0 text-sm font-black shadow-xs">
                        📋
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-amber-950 dark:text-amber-200">Alur Pengerjaan Tugas Berurutan Aktif</h4>
                        <p class="text-[11px] text-amber-700 dark:text-amber-300">Tugas & PR harus diselesaikan dan dikumpulkan secara bertahap. Siswa wajib mengumpulkan tugas sebelumnya untuk membuka akses penugasan selanjutnya.</p>
                    </div>
                </div>
            @endif

            @if($assignments->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada tugas atau PR untuk mata pelajaran ini.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($assignments as $asg)
                        @php
                            $mySub = $asg->submissions->first();
                            $isLate = now()->greaterThan($asg->due_date);
                            $isUnlocked = $asg->is_unlocked ?? true;
                            $isCompleted = $asg->is_completed ?? ($mySub !== null);
                        @endphp
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border {{ $isUnlocked ? 'border-slate-200 dark:border-slate-800 hover:border-indigo-500/40' : 'border-dashed border-slate-300 dark:border-slate-700 bg-slate-100/50 dark:bg-slate-900/50 opacity-80' }} shadow-xs transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5 max-w-2xl">
                                <div class="flex items-center gap-2">
                                    @if(isset($asg->sequence_num))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                            #{{ $asg->sequence_num }}
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-500/20 text-amber-700 dark:text-amber-300 uppercase">
                                        Maks Skor: {{ $asg->max_score }}
                                    </span>
                                    @if($asg->start_time)
                                        <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                            ⏳ Mulai: {{ $asg->start_time->translatedFormat('d M Y H:i') }} WIB
                                        </span>
                                    @endif
                                    <span class="text-xs {{ $isLate ? 'text-rose-500 font-bold' : 'text-slate-500' }}">
                                        Batas Waktu: {{ $asg->due_date->translatedFormat('l, d F Y - H:i') }} WIB
                                    </span>
                                    @if($asg->learningObjective)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-400/30" title="{{ $asg->learningObjective->title }}">
                                            🎯 TP {{ $asg->learningObjective->tp_code }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-bold {{ $isUnlocked ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">{{ $asg->title }}</h3>
                                <p class="text-xs text-slate-500 line-clamp-2">{{ $asg->description }}</p>

                                @if($student && ! $isUnlocked)
                                    @if($asg->is_time_locked ?? false)
                                        <div class="mt-2 p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-[11px] text-indigo-700 dark:text-indigo-300 font-semibold flex items-center gap-1.5">
                                            <span class="shrink-0">⏳</span>
                                            <span>Tugas baru dapat dibuka pada <strong>{{ $asg->formattedStartTime() }} WIB</strong></span>
                                        </div>
                                    @elseif($asg->prerequisite_title)
                                        <div class="mt-2 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-700 dark:text-amber-300 font-semibold flex items-center gap-1.5">
                                            <span class="shrink-0">⚠️</span>
                                            <span>Kumpulkan tugas <strong>{{ $asg->prerequisite_title }}</strong> terlebih dahulu untuk membuka tugas ini.</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if($student)
                                    <div class="text-right">
                                        @if(! $isUnlocked)
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-400 flex items-center gap-1">
                                                🔒 Terkunci
                                            </span>
                                        @elseif($mySub)
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $mySub->score !== null ? 'bg-emerald-500/20 text-emerald-600' : 'bg-cyan-500/20 text-cyan-600' }}">
                                                {{ $mySub->score !== null ? 'Nilai: ' . $mySub->score . '/' . $asg->max_score : 'Terkumpul (Menunggu Koreksi)' }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isLate ? 'bg-rose-500/20 text-rose-600' : 'bg-amber-500/20 text-amber-600' }}">
                                                {{ $isLate ? 'Belum Mengumpulkan (Lewat Waktu)' : 'Belum Mengumpulkan' }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($canManage)
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $asg->submissions->count() }} Pengumpulan</div>
                                        <div class="text-[11px] text-amber-500 font-semibold">{{ $asg->submissions->where('status', 'submitted')->count() }} Belum Dinilai</div>
                                    </div>
                                @endif

                                @if(! $student || $isUnlocked)
                                    <a href="{{ route('lms.assignments.show', ['courseClass' => $courseClass->id, 'assignment' => $asg->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                                        {{ $student ? ($mySub ? 'Lihat Tugas' : 'Kumpulkan Tugas') : 'Periksa Jawaban' }} &rarr;
                                    </a>
                                @else
                                    <button type="button" disabled class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 font-bold text-xs inline-flex items-center gap-1.5 cursor-not-allowed" title="{{ ($asg->is_time_locked ?? false) ? 'Tugas baru dapat dibuka pada ' . $asg->formattedStartTime() : 'Selesaikan tugas sebelumnya terlebih dahulu' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </button>
                                @endif

                                @if($canManage)
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditAssignmentModal({{ json_encode([
                                            'id' => $asg->id,
                                            'title' => $asg->title,
                                            'topic' => $asg->topic,
                                            'instructions' => $asg->instructions,
                                            'start_time' => $asg->start_time ? $asg->start_time->format('Y-m-d\TH:i') : '',
                                            'due_date' => $asg->due_date ? $asg->due_date->format('Y-m-d\TH:i') : '',
                                            'max_score' => $asg->max_score,
                                            'submission_type' => $asg->submission_type ?? 'both',
                                            'allowed_file_types' => $asg->allowed_file_types ?: ['pdf', 'ppt', 'image'],
                                            'allow_late' => (bool) $asg->allow_late,
                                            'is_published' => (bool) $asg->is_published,
                                            'update_url' => route('lms.assignments.update', ['courseClass' => $courseClass->id, 'assignment' => $asg->id])
                                        ]) }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold inline-flex items-center gap-1 transition" title="Edit tugas ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" onclick="openCopyModal('assignment', {{ $asg->id }}, '{{ addslashes($asg->title) }}')" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-300 font-bold inline-flex items-center gap-1 transition" title="Salin tugas ini ke kelas lain">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            <span>Salin</span>
                                        </button>
                                        <form action="{{ route('lms.assignments.destroy', ['courseClass' => $courseClass->id, 'assignment' => $asg->id]) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 3: ASESMEN PEMBELAJARAN (CBT) -->
    @if($activeTab === 'kuis')
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Asesmen Pembelajaran (CBT) & Kuis Daring</h2>
                    <p class="text-xs text-slate-500">Asesmen formatif & sumatif (lingkup materi, STS, SAS) digital sesuai Permendikdasmen No. 13 Tahun 2025</p>
                </div>
                @if($canManage)
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModalTab('modal-add-quiz', 'ai')" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                            <span>✨ Generate Paket CBT AI</span>
                            <span class="text-[9.5px] bg-white/20 px-1.5 py-0.5 rounded-full font-black uppercase tracking-wider">Permendikdasmen 13/2025</span>
                        </button>
                        <button type="button" onclick="openModalTab('modal-add-quiz', 'manual')" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Buat Manual</span>
                        </button>
                    </div>
                @endif
            </div>

            @if($student)
                <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/70 dark:border-purple-800/70 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 text-sm font-black shadow-xs">
                        🎯
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-purple-950 dark:text-purple-200">Alur Asesmen & CBT Berurutan Aktif</h4>
                        <p class="text-[11px] text-purple-700 dark:text-purple-300">Agenda asesmen digital harus diselesaikan secara bertahap. Siswa wajib menyelesaikan ujian sebelumnya untuk membuka agenda asesmen berikutnya.</p>
                    </div>
                </div>
            @endif

            @if($quizzes->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada agenda asesmen atau ujian daring untuk kelas ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($quizzes as $quiz)
                        @php
                            $attempt = $quiz->attempts->first();
                            $isUnlocked = $quiz->is_unlocked ?? true;
                            $isCompleted = $quiz->is_completed ?? ($attempt && $attempt->completed_at);
                            $typeLabel = match($quiz->quiz_type) {
                                'formatif' => 'Asesmen Formatif',
                                'sumatif_materi' => 'Asesmen Sumatif (Lingkup Materi)',
                                'sts' => 'Sumatif Tengah Semester (STS)',
                                'sas' => 'Sumatif Akhir Semester (SAS)',
                                'saj' => 'Sumatif Akhir Jenjang (SAJ)',
                                'ulangan_harian' => 'Asesmen Sumatif (Lingkup Materi)',
                                'uts' => 'Sumatif Tengah Semester (STS)',
                                'uas' => 'Sumatif Akhir Semester (SAS)',
                                'kuis' => 'Asesmen Formatif',
                                default => ucwords(str_replace('_', ' ', $quiz->quiz_type)),
                            };
                            $badgeColor = match($quiz->quiz_type) {
                                'formatif', 'kuis' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30',
                                'sumatif_materi', 'ulangan_harian' => 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30',
                                'sts', 'uts' => 'bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-500/30',
                                'sas', 'uas' => 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30',
                                'saj' => 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30',
                                default => 'bg-slate-500/15 text-slate-700 dark:text-slate-300 border border-slate-500/30',
                            };
                        @endphp
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border {{ $isUnlocked ? 'border-slate-200 dark:border-slate-800 hover:border-indigo-500/40' : 'border-dashed border-slate-300 dark:border-slate-700 bg-slate-100/50 dark:bg-slate-900/50 opacity-80' }} shadow-xs transition flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5">
                                        @if(isset($quiz->sequence_num))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                #{{ $quiz->sequence_num }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $badgeColor }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-400">⏱️ {{ $quiz->duration_minutes }} Menit • KKTP: {{ $quiz->passing_score }}</span>
                                </div>
                                <h3 class="text-base font-bold {{ $isUnlocked ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">{{ $quiz->title }}</h3>
                                <p class="text-xs text-slate-500">{{ $quiz->questions->count() }} Butir Soal Terdaftar</p>

                                @if($student && ! $isUnlocked && $quiz->prerequisite_title)
                                    <div class="mt-2 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-700 dark:text-amber-300 font-semibold flex items-center gap-1.5">
                                        <span class="shrink-0">⚠️</span>
                                        <span class="truncate">Selesaikan asesmen <strong>{{ $quiz->prerequisite_title }}</strong> terlebih dahulu.</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                @if($student)
                                    @if(! $isUnlocked)
                                        <button type="button" disabled class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 font-bold text-xs inline-flex items-center gap-1.5 cursor-not-allowed" title="Selesaikan asesmen sebelumnya terlebih dahulu">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Terkunci</span>
                                        </button>
                                    @elseif($attempt && $attempt->completed_at)
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $attempt->is_passed ? 'bg-emerald-500/20 text-emerald-600' : 'bg-rose-500/20 text-rose-600' }}">
                                            Skor: {{ $attempt->score }} ({{ $attempt->is_passed ? 'TUNTAS (Mencapai KKTP)' : 'REMEDIAL (Belum Mencapai KKTP)' }})
                                        </span>
                                    @else
                                        <a href="{{ route('lms.quizzes.take', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs inline-flex items-center gap-1.5 transition">
                                            <span>Mulai Ujian</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Detail Soal & Nilai Siswa ({{ $quiz->attempts->count() }} Pengerjaan) &rarr;
                                    </a>
                                @endif

                                @if($canManage)
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditQuizModal({{ json_encode([
                                            'id' => $quiz->id,
                                            'title' => $quiz->title,
                                            'topic' => $quiz->topic,
                                            'quiz_type' => $quiz->quiz_type,
                                            'duration_minutes' => $quiz->duration_minutes,
                                            'passing_score' => $quiz->passing_score,
                                            'start_time' => $quiz->start_time ? $quiz->start_time->format('Y-m-d\TH:i') : '',
                                            'end_time' => $quiz->end_time ? $quiz->end_time->format('Y-m-d\TH:i') : '',
                                            'instructions' => $quiz->instructions ?? $quiz->description ?? '',
                                            'is_randomized' => (bool) $quiz->is_randomized,
                                            'is_published' => (bool) $quiz->is_published,
                                            'update_url' => route('lms.quizzes.update', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id])
                                        ]) }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold inline-flex items-center gap-1 transition" title="Edit asesmen ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" onclick="openCopyModal('quiz', {{ $quiz->id }}, '{{ addslashes($quiz->title) }}')" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-300 font-bold inline-flex items-center gap-1 transition" title="Salin kuis ini ke kelas lain">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            <span>Salin</span>
                                        </button>
                                        <form action="{{ route('lms.quizzes.destroy', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" method="POST" onsubmit="return confirm('Hapus asesmen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 4: PRESENSI MAPEL -->
    @if($activeTab === 'presensi')
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Presensi Pertemuan Mata Pelajaran</h2>
                    <p class="text-xs text-slate-500">Mencatat kehadiran per sesi tatap muka / daring dengan verifikasi manual guru atau selfie siswa</p>
                </div>
                @if($canManage)
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="openAddAttendanceModal('auto')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs" title="Buka Sesi Presensi yang otomatis mengambil jadwal dari Tugaskan Guru/Diri ke Kelas">
                            <span>⚡ Presensi Otomatis (Jadwal Kelas)</span>
                        </button>
                        <button type="button" onclick="openAddAttendanceModal('manual')" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs" title="Buka Sesi Presensi dengan jadwal hari & jam kustom manual">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Buka Presensi Baru</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Banner Info Jadwal Pembelajaran Kelas Belajar (Tugaskan Guru) -->
            <div class="p-4 rounded-3xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                        🗓️
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-black text-slate-900 dark:text-white">Jadwal Pembelajaran Kelas Belajar:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ $courseClass->schedule_text }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Data jadwal pembelajaran diatur melalui menu <strong>Tugaskan Diri / Tugaskan Guru Pengampu ke Kelas</strong>. Mode presensi <em>Otomatis</em> akan selalu tersinkronisasi dengan jadwal ini.
                        </p>
                    </div>
                </div>
                @if($canManage)
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('lms.attendances.print_recap', $courseClass->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-300 dark:border-slate-700 shadow-xs" title="Cetak Dokumen Rekapitulasi Presensi Lengkap">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak Rekapitulasi Presensi</span>
                        </a>
                        @if($attendances->isEmpty())
                            <button type="button" onclick="openAddAttendanceModal('auto')" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                <span>Aktifkan Presensi Kelas Sekarang &rarr;</span>
                            </button>
                        @else
                            <button type="button" onclick="openAddAttendanceModal('manual')" class="shrink-0 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                <span>+ Buat Sesi Presensi</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            @if($attendances->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada sesi presensi pertemuan untuk kelas ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($attendances as $att)
                        @php
                            $myLog = $att->logs->first();
                        @endphp
                        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-500/40 shadow-xs transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if(($att->schedule_type ?? 'auto') === 'auto')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 flex items-center gap-1" title="Jadwal presensi otomatis mengambil dari Tugaskan Guru ke Kelas Belajar">
                                            <span>🤖</span>
                                            <span>Jadwal Otomatis Kelas</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 flex items-center gap-1" title="Jadwal presensi diatur secara manual kustom">
                                            <span>✍️</span>
                                            <span>Jadwal Manual Kustom</span>
                                        </span>
                                    @endif

                                    @if($att->is_recurring && $att->parent_attendance_id === null)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-500/30">
                                            🔄 Presensi Rutin Berulang
                                        </span>
                                        <span class="text-xs text-slate-500 font-medium">
                                            🗓️ Setiap: <strong class="text-slate-700 dark:text-slate-300">{{ $att->formatted_days }}</strong>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $att->is_open ? 'bg-emerald-500/20 text-emerald-600' : 'bg-slate-500/20 text-slate-500' }}">
                                            {{ $att->is_open ? 'Sesi Terbuka' : 'Sesi Ditutup' }}
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            📅 {{ Carbon\Carbon::parse($att->meeting_date)->translatedFormat('l, d F Y') }} ({{ $att->start_time }} - {{ $att->end_time }} WIB)
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $att->title }}</h3>
                                <p class="text-xs text-slate-500">
                                    Metode: <strong class="text-slate-700 dark:text-slate-300">
                                        @if($att->attendance_method === 'both')
                                            Selfie & Manual (Guru & Sekretaris Kelas)
                                        @elseif($att->attendance_method === 'manual')
                                            Manual (Guru & Sekretaris Kelas)
                                        @else
                                            Selfie Siswa
                                        @endif
                                    </strong> 
                                    • {{ $att->notes ?: 'Pertemuan tatap muka / daring' }}
                                    @if($att->parent_attendance_id)
                                        <span class="ml-1 text-[11px] text-cyan-600 dark:text-cyan-400 font-semibold">(Otomatis dari Presensi Rutin)</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($student)
                                    @if($att->is_recurring && $att->parent_attendance_id === null)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                            Terjadwal Rutin
                                        </span>
                                    @elseif($myLog)
                                        @if($myLog->status === 'hadir')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-600">
                                                ✓ Hadir ({{ strtoupper($myLog->verified_method) }})
                                            </span>
                                        @elseif($myLog->status === 'izin')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-600">
                                                ℹ Izin
                                            </span>
                                        @elseif($myLog->status === 'sakit')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-600">
                                                🏥 Sakit
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-600">
                                                ✕ Alpa
                                            </span>
                                        @endif
                                    @else
                                        @if($att->is_open)
                                            <a href="{{ route('lms.attendances.show', ['courseClass' => $courseClass->id, 'attendance' => $att->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs inline-flex items-center gap-1.5 transition shadow-xs">
                                                <span>Presensi Hadir Sekarang</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                            </a>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-600">Alpa (Sesi Berakhir)</span>
                                        @endif
                                    @endif

                                    @if(auth()->user()->hasRole('sekretaris_kelas') && (int)$student->class_id === (int)$courseClass->class_id && (!$att->is_recurring || $att->parent_attendance_id !== null))
                                        <a href="{{ route('lms.attendances.show', ['courseClass' => $courseClass->id, 'attendance' => $att->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition shadow-xs" title="Kelola Presensi Manual Kelas">
                                            <span>Presensi Manual Kelas</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        </a>
                                    @endif
                                @else
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($att->is_recurring && $att->parent_attendance_id === null)
                                            <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">
                                                {{ $att->childrenAttendances->count() }} Sesi Terlaksana
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $att->logs->where('status', 'hadir')->count() }} Siswa Hadir
                                            </span>
                                        @endif
                                        <a href="{{ route('lms.attendances.show', ['courseClass' => $courseClass->id, 'attendance' => $att->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                                            Buka Rekap & Manual &rarr;
                                        </a>

                                        @if($canManage)
                                            <a href="{{ route('lms.attendances.print', ['courseClass' => $courseClass->id, 'attendance' => $att->id]) }}" target="_blank" class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold transition border border-slate-200 dark:border-slate-700" title="Cetak Rekap Presensi Sesi Ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>
                                            <button type="button" onclick="openEditAttendanceModal({{ json_encode([
                                                'id' => $att->id,
                                                'title' => $att->title,
                                                'meeting_date' => $att->meeting_date ? Carbon\Carbon::parse($att->meeting_date)->format('Y-m-d') : '',
                                                'start_time' => $att->start_time,
                                                'end_time' => $att->end_time,
                                                'attendance_method' => $att->attendance_method,
                                                'notes' => $att->notes,
                                                'is_open' => (bool)$att->is_open,
                                                'update_url' => route('lms.attendances.update', ['courseClass' => $courseClass->id, 'attendance' => $att->id]),
                                            ]) }})" class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-slate-700 dark:text-slate-300 hover:text-indigo-600 text-xs font-bold transition border border-slate-200 dark:border-slate-700" title="Edit Sesi Presensi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form action="{{ route('lms.attendances.destroy', ['courseClass' => $courseClass->id, 'attendance' => $att->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi presensi {{ $att->title }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition border border-rose-500/20" title="Hapus Sesi Presensi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 5: PEMANTAUAN NILAI & BUKU NILAI (GRADEBOOK) -->
    @if($activeTab === 'nilai')
        @php
            $wm = $courseClass->getWeightMaterial();
            $wa = $courseClass->getWeightAssignment();
            $wq = $courseClass->getWeightQuiz();
            $wt = $courseClass->getWeightAttendance();
            $canEditWeights = auth()->check() && auth()->user()->canEditLmsWeights($courseClass);
        @endphp
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Buku Nilai & Pemantauan Ketuntasan Siswa</h2>
                    <p class="text-xs text-slate-500">Bobot Penilaian Aktif: Materi ({{ $wm }}%), Tugas ({{ $wa }}%), Asesmen CBT ({{ $wq }}%), dan Presensi Mapel ({{ $wt }}%)</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('lms.grades.export_excel', $courseClass->id) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 transition shadow-xs" title="Unduh Buku Nilai format Microsoft Excel (.xls)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Excel</span>
                    </a>
                    <a href="{{ route('lms.grades.print_pdf', $courseClass->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 transition shadow-xs" title="Cetak / Simpan PDF Lembar Nilai Resmi dengan KOP dan TTD Kepala Sekolah">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak / PDF (KOP)</span>
                    </a>
                    <a href="{{ route('lms.grades.export', $courseClass->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh CSV</span>
                    </a>
                </div>
            </div>

            <!-- FORM MANUAL: PENGATURAN PERSENTASE BOBOT PENILAIAN -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>⚙️ Pengaturan Bobot Persentase Penilaian Akhir</span>
                        </h3>
                        <p class="text-xs text-slate-500">Tentukan persentase kontribusi Materi Pelajaran, Tugas & PR, Asesmen & CBT, dan Presensi Kehadiran. Total harus tepat 100%.</p>
                    </div>
                    <div id="course-grade-weight-badge" class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700">
                        Total Bobot: <span id="course-weight-total-display">100</span>% (Valid)
                    </div>
                </div>

                @if($canEditWeights)
                    <form action="{{ route('lms.grades.weights', $courseClass->id) }}" method="POST" onsubmit="return validateCourseGradeWeights(event)" class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2 items-end">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📖 Materi Selesai (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="weight_material" id="cw_mat" value="{{ $wm }}" oninput="recalcCourseWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <span class="text-[10px] text-slate-400">Tuntas = 100, Rerata</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📝 Tugas & PR (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="weight_assignment" id="cw_asg" value="{{ $wa }}" oninput="recalcCourseWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <span class="text-[10px] text-slate-400">Rata-rata tugas</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🎯 Asesmen CBT (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="weight_quiz" id="cw_qiz" value="{{ $wq }}" oninput="recalcCourseWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <span class="text-[10px] text-slate-400">Rata-rata ujian CBT</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📅 Presensi Mapel (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="weight_attendance" id="cw_att" value="{{ $wt }}" oninput="recalcCourseWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                            <span class="text-[10px] text-slate-400">H=100, I/S=50, A=0</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <button type="submit" id="btn-course-save-weights" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Simpan Bobot
                            </button>
                        </div>
                    </form>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Materi Pelajaran</span>
                            <strong class="text-sm text-slate-900 dark:text-white">{{ $wm }}%</strong>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Tugas & PR</span>
                            <strong class="text-sm text-slate-900 dark:text-white">{{ $wa }}%</strong>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Asesmen & CBT</span>
                            <strong class="text-sm text-slate-900 dark:text-white">{{ $wq }}%</strong>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Presensi Mapel</span>
                            <strong class="text-sm text-slate-900 dark:text-white">{{ $wt }}%</strong>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 4 Indikator Pemantauan Pembelajaran Aktif (Selalu Update Mengikuti Jumlah Konten) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span class="font-bold">Materi Pelajaran</span>
                        <span class="p-1 rounded-lg bg-blue-500/10 text-blue-600">📖</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ $totalMaterialsCount ?? 0 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wm }}%</p>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span class="font-bold">Tugas & PR</span>
                        <span class="p-1 rounded-lg bg-indigo-500/10 text-indigo-600">📝</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ $totalAssignmentsCount ?? 0 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wa }}%</p>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span class="font-bold">Asesmen & CBT</span>
                        <span class="p-1 rounded-lg bg-purple-500/10 text-purple-600">🎯</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ $totalQuizzesCount ?? 0 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wq }}%</p>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span class="font-bold">Presensi Mapel</span>
                        <span class="p-1 rounded-lg bg-emerald-500/10 text-emerald-600">📅</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ $totalAttendancesCount ?? 0 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bobot kehadiran {{ $wt }}%</p>
                </div>
            </div>

            @if($student && isset($gradebook[$student->id]))
                @php $myGrade = $gradebook[$student->id]; @endphp
                <!-- Ringkasan Nilai & Progres Mandiri Siswa -->
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-tr from-indigo-950 via-slate-900 to-slate-900 border border-indigo-500/30 text-white shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shrink-0">
                                🎓
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-white">Ringkasan Ketuntasan Belajar Anda</h3>
                                <p class="text-xs text-indigo-300">{{ $student->name }} • NISN: {{ $student->nisn }} • Kelas {{ $courseClass->schoolClass->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl text-xs font-black uppercase {{ $myGrade['is_mastered'] ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                                Status: {{ $myGrade['is_mastered'] ? 'TUNTAS (Mencapai KKTP)' : 'REMEDIAL' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <!-- 1. Materi -->
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-1.5">
                            <div class="flex items-center justify-between text-slate-400">
                                <span class="font-bold">Materi Selesai ({{ $wm }}%)</span>
                                <span>📖</span>
                            </div>
                            <div class="text-xl font-black text-white">
                                {{ $myGrade['materials_completed'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $myGrade['materials_count'] }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $myGrade['materials_pct'] }}%"></div>
                            </div>
                            <span class="text-[10px] text-blue-400 font-bold">Rerata: {{ $myGrade['material_avg'] ?? 0 }}</span>
                        </div>

                        <!-- 2. Tugas -->
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-1.5">
                            <div class="flex items-center justify-between text-slate-400">
                                <span class="font-bold">Tugas & PR ({{ $wa }}%)</span>
                                <span>📝</span>
                            </div>
                            <div class="text-xl font-black text-white">
                                {{ $myGrade['assignments_submitted'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $myGrade['assignments_count'] }}</span>
                            </div>
                            <span class="text-[11px] text-indigo-300 font-bold">Rerata: {{ $myGrade['assignment_avg'] }}</span>
                        </div>

                        <!-- 3. Asesmen CBT -->
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-1.5">
                            <div class="flex items-center justify-between text-slate-400">
                                <span class="font-bold">Asesmen CBT ({{ $wq }}%)</span>
                                <span>🎯</span>
                            </div>
                            <div class="text-xl font-black text-white">
                                {{ $myGrade['quizzes_completed'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $myGrade['quizzes_count'] }}</span>
                            </div>
                            <span class="text-[11px] text-purple-300 font-bold">Rerata: {{ $myGrade['quiz_avg'] }}</span>
                        </div>

                        <!-- 4. Presensi -->
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-1.5">
                            <div class="flex items-center justify-between text-slate-400">
                                <span class="font-bold">Presensi Mapel ({{ $wt }}%)</span>
                                <span>📅</span>
                            </div>
                            <div class="text-xl font-black text-white">
                                {{ $myGrade['attendances_attended'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $myGrade['attendances_count'] }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $myGrade['attendance_pct'] }}%"></div>
                            </div>
                            <span class="text-[10px] text-emerald-400 font-bold">Rerata: {{ $myGrade['attendance_avg'] ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Nilai Akhir Card Strip -->
                    <div class="p-3.5 rounded-2xl bg-indigo-900/40 border border-indigo-500/30 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-indigo-200">Nilai Akhir Berdasarkan Bobot:</span>
                            <p class="text-[11px] text-indigo-300/80">{{ $wm }}% Materi + {{ $wa }}% Tugas + {{ $wq }}% CBT + {{ $wt }}% Presensi (KKTP: {{ $courseClass->subject->kkm ?? 75 }})</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <div class="text-2xl font-black text-white">{{ $myGrade['final_score'] ?? '-' }}</div>
                                <span class="text-[10px] font-bold text-indigo-300">Predikat: {{ $myGrade['predicate'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Gradebook Table -->
            <div class="overflow-x-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500 tracking-wider">
                        <tr>
                            <th class="p-3.5 text-center">No</th>
                            <th class="p-3.5">NISN & Nama Siswa</th>
                            <th class="p-3.5 text-center">Materi ({{ $wm }}%)</th>
                            <th class="p-3.5 text-center">Tugas & PR ({{ $wa }}%)</th>
                            <th class="p-3.5 text-center">Asesmen CBT ({{ $wq }}%)</th>
                            <th class="p-3.5 text-center">Presensi ({{ $wt }}%)</th>
                            <th class="p-3.5 text-center">Nilai Akhir</th>
                            <th class="p-3.5 text-center">Predikat</th>
                            <th class="p-3.5 text-center">Status KKTP ({{ $courseClass->subject->kkm ?? 75 }})</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = 1; @endphp
                        @foreach($classStudents as $st)
                            @php
                                $grade = $gradebook[$st->id] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3.5 text-center text-slate-400 font-semibold">{{ $no++ }}</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $st->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $st->nisn }}</div>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $grade['materials_completed'] ?? 0 }}/{{ $grade['materials_count'] ?? 0 }}</span>
                                    <span class="block text-[10px] text-blue-600 dark:text-blue-400 font-bold">Rerata: {{ isset($grade['material_avg']) && $grade['material_avg'] !== null ? $grade['material_avg'] : '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $grade['assignments_submitted'] ?? 0 }}/{{ $grade['assignments_count'] ?? 0 }}</span>
                                    <span class="block text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">Rerata: {{ isset($grade['assignment_avg']) && $grade['assignment_avg'] !== null ? $grade['assignment_avg'] : '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $grade['quizzes_completed'] ?? 0 }}/{{ $grade['quizzes_count'] ?? 0 }}</span>
                                    <span class="block text-[10px] text-purple-600 dark:text-purple-400 font-bold">Rerata: {{ isset($grade['quiz_avg']) && $grade['quiz_avg'] !== null ? $grade['quiz_avg'] : '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $grade['attendances_attended'] ?? 0 }}/{{ $grade['attendances_count'] ?? 0 }}</span>
                                    <span class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Rerata: {{ isset($grade['attendance_avg']) && $grade['attendance_avg'] !== null ? $grade['attendance_avg'] : '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center font-black text-sm text-indigo-600 dark:text-indigo-400">
                                    {{ $grade['final_score'] ?? '-' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded font-black text-xs bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                        {{ $grade['predicate'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if(isset($grade['is_mastered']) && $grade['is_mastered'] !== null)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $grade['is_mastered'] ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                                            {{ $grade['is_mastered'] ? 'TUNTAS' : 'REMEDIAL' }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        function recalcCourseWeightTotal() {
            const mat = parseFloat(document.getElementById('cw_mat')?.value) || 0;
            const asg = parseFloat(document.getElementById('cw_asg')?.value) || 0;
            const qiz = parseFloat(document.getElementById('cw_qiz')?.value) || 0;
            const att = parseFloat(document.getElementById('cw_att')?.value) || 0;
            const total = Math.round((mat + asg + qiz + att) * 10) / 10;
            
            const display = document.getElementById('course-weight-total-display');
            const badge = document.getElementById('course-grade-weight-badge');
            const btn = document.getElementById('btn-course-save-weights');
            
            if (display) display.textContent = total;
            
            if (Math.abs(total - 100) < 0.01) {
                if (badge) {
                    badge.className = "px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700";
                    badge.innerHTML = `Total Bobot: <span id="course-weight-total-display">${total}</span>% (Valid)`;
                }
                if (btn) btn.disabled = false;
            } else {
                if (badge) {
                    badge.className = "px-3 py-1 rounded-xl text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-700";
                    badge.innerHTML = `Total Bobot: <span id="course-weight-total-display">${total}</span>% (Harus 100%)`;
                }
            }
        }

        function validateCourseGradeWeights(e) {
            const mat = parseFloat(document.getElementById('cw_mat')?.value) || 0;
            const asg = parseFloat(document.getElementById('cw_asg')?.value) || 0;
            const qiz = parseFloat(document.getElementById('cw_qiz')?.value) || 0;
            const att = parseFloat(document.getElementById('cw_att')?.value) || 0;
            const total = Math.round((mat + asg + qiz + att) * 10) / 10;
            
            if (Math.abs(total - 100) > 0.01) {
                alert('Total persentase bobot harus pas 100%. Saat ini: ' + total + '%. Silakan sesuaikan.');
                e.preventDefault();
                return false;
            }
            return true;
        }
        </script>
    @endif

    <!-- TAB 6: FORUM DISKUSI -->
    @if($activeTab === 'diskusi')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ruang Diskusi & Tanya Jawab Kelas</h2>
                    <p class="text-xs text-slate-500">Forum interaktif antara guru pengampu dan seluruh siswa kelas</p>
                </div>
            </div>

            <!-- New Discussion Box -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <form action="{{ route('lms.discussions.store', $courseClass->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" placeholder="Judul Topik Diskusi (Opsional)" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <textarea name="message" rows="3" required placeholder="Tuliskan pertanyaan, tanggapan materi, atau pengumuman kelas di sini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 outline-none focus:border-indigo-500 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        @if($canManage)
                            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                <input type="checkbox" name="is_pinned" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Sematkan / Pin Pengumuman ini di atas</span>
                            </label>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                            Kirim Diskusi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Discussions List -->
            @if($discussions->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada topik diskusi di kelas ini. Jadilah yang pertama memulai diskusi!
                </div>
            @else
                <div class="space-y-4">
                    @foreach($discussions as $disc)
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border {{ $disc->is_pinned ? 'border-amber-400/60 dark:border-amber-500/40 bg-amber-50/20' : 'border-slate-200 dark:border-slate-800' }} shadow-xs space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-extrabold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($disc->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <span>{{ $disc->user->name }}</span>
                                            <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold uppercase bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                {{ str_replace('_', ' ', $disc->user->role) }}
                                            </span>
                                            @if($disc->is_pinned)
                                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-extrabold uppercase bg-amber-500 text-slate-950">
                                                    PINNED
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400">{{ $disc->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>

                                @if($canManage)
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('lms.discussions.pin', ['courseClass' => $courseClass->id, 'discussion' => $disc->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-amber-500 hover:underline">
                                                {{ $disc->is_pinned ? 'Unpin' : 'Pin' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('lms.discussions.destroy', ['courseClass' => $courseClass->id, 'discussion' => $disc->id]) }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            @if($disc->title)
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $disc->title }}</h4>
                            @endif

                            <div class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                {{ $disc->message }}
                            </div>

                            <!-- Replies Stream -->
                            @if($disc->replies->isNotEmpty())
                                <div class="mt-3 pl-4 border-l-2 border-indigo-500/30 space-y-2.5">
                                    @foreach($disc->replies as $reply)
                                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $reply->user->name }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-slate-700 dark:text-slate-300">{{ $reply->message }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Reply Form -->
                            <form action="{{ route('lms.discussions.reply', ['courseClass' => $courseClass->id, 'discussion' => $disc->id]) }}" method="POST" class="pt-2 flex items-center gap-2">
                                @csrf
                                <input type="text" name="message" required placeholder="Tulis balasan..." class="flex-1 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-1.5 outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold">Balas</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <!-- TAB 7: TATAP MUKA VIRTUAL / ZOOM MEET -->
    @elseif($activeTab === 'zoom')
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Tatap Muka Virtual (Zoom Meet)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-500/15 text-blue-600 dark:text-blue-400">SMA1LE LIVE</span>
                    </h2>
                    <p class="text-xs text-slate-500">Pertemuan video tatap muka pembelajaran langsung secara daring terpadu</p>
                </div>
                @if($canManage)
                    <button type="button" onclick="document.getElementById('modal-add-zoom-meeting').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Jadwalkan Zoom Baru</span>
                    </button>
                @endif
            </div>

            <!-- Security & Restriction Notice Card -->
            <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 flex items-start gap-3 text-xs text-blue-900 dark:text-blue-200">
                <div class="p-2 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div class="space-y-1">
                    <div class="font-extrabold flex items-center gap-2">
                        <span>Akses Terbatas & Terproteksi (Siswa Kelas & Guru Pengampu)</span>
                        <span class="px-2 py-0.2 rounded-full text-[9px] font-black uppercase bg-blue-600 text-white">Privat</span>
                    </div>
                    <p class="text-blue-800 dark:text-blue-300 text-[11px] leading-relaxed">
                        Sesi tatap muka daring ini hanya dapat diakses oleh <strong>Siswa terdaftar di kelas {{ $courseClass->schoolClass->name }}</strong> dan <strong>Guru Pengampu ({{ $courseClass->teacher->name ?? '-' }})</strong>. Siswa dari kelas lain atau pengguna luar yang mencoba membuka tautan akan otomatis ditolak oleh sistem keamanan LMS.
                    </p>
                </div>
            </div>

            <!-- Zoom Meetings List -->
            @if($zoomMeetings->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-800 space-y-3">
                    <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Sesi Zoom Meet</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Guru mata pelajaran belum menjadwalkan sesi tatap muka online untuk kelas ini.</p>
                    @if($canManage)
                        <div class="pt-2">
                            <button type="button" onclick="document.getElementById('modal-add-zoom-meeting').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition">
                                + Buat Sesi Zoom Meet Pertama
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($zoomMeetings as $zoom)
                        @php
                            $isLive = $zoom->isLiveNow();
                            $hasAttended = $student ? $zoom->isAttendedBy($student->id) : false;
                        @endphp
                        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border {{ $isLive ? 'border-blue-400 ring-2 ring-blue-500/20 shadow-md' : 'border-slate-200 dark:border-slate-800 shadow-xs' }} space-y-4">
                            <!-- Card Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 {{ $isLive ? 'bg-blue-600 text-white animate-pulse' : 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($isLive)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-500 text-white flex items-center gap-1 animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    LIVE SEKARANG
                                                </span>
                                            @elseif($zoom->status === 'scheduled')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/15 text-amber-700 dark:text-amber-400">
                                                    📅 TERJADWAL
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                    SELESAI
                                                </span>
                                            @endif
                                            <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">{{ $zoom->title }}</h3>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2">
                                            @if($zoom->topic)
                                                <span class="font-medium text-slate-700 dark:text-slate-300">Topik: {{ $zoom->topic }}</span>
                                                <span>&bull;</span>
                                            @endif
                                            <span>Oleh: {{ $zoom->user->name ?? $courseClass->teacher->name ?? 'Guru Pengampu' }}</span>
                                            <span>&bull;</span>
                                            <span>Mulai: {{ $zoom->start_time->format('d M Y, H:i') }} WIB</span>
                                            @if($zoom->end_time)
                                                <span>- {{ $zoom->end_time->format('H:i') }} WIB</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Header Quick Actions -->
                                <div class="flex items-center gap-2 self-start sm:self-center">
                                    <a href="{{ route('lms.zoom.join', ['courseClass' => $courseClass->id, 'meeting' => $zoom->id]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        <span>Masuk Zoom</span>
                                    </a>
                                    @if($canManage)
                                        <form action="{{ route('lms.zoom.toggle_status', ['courseClass' => $courseClass->id, 'meeting' => $zoom->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold transition" title="{{ $zoom->status === 'live' ? 'Akhiri Sesi' : 'Aktifkan (Live)' }}">
                                                @if($zoom->status === 'live')
                                                    <span class="text-red-500">Akhiri</span>
                                                @else
                                                    <span class="text-emerald-500">Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('lms.zoom.destroy', ['courseClass' => $courseClass->id, 'meeting' => $zoom->id]) }}" method="POST" onsubmit="return confirm('Hapus jadwal sesi Zoom ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition" title="Hapus Jadwal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Body: Details & Credentials -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2 space-y-2">
                                    @if($zoom->agenda)
                                        <div class="text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-800 leading-relaxed">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block mb-1">Agenda & Petunjuk Belajar:</span>
                                            {!! nl2br(e($zoom->agenda)) !!}
                                        </div>
                                    @endif

                                    <!-- Credentials Box -->
                                    <div class="flex flex-wrap items-center gap-2 p-3 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-500">Meeting ID:</span>
                                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">{{ $zoom->meeting_id ?: 'Tercantum di Link' }}</span>
                                        </div>
                                        @if($zoom->passcode)
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-slate-500">Passcode:</span>
                                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200 bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">{{ $zoom->passcode }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1 text-right">
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $zoom->meeting_url }}'); alert('Tautan Zoom berhasil disalin!')" class="text-blue-600 dark:text-blue-400 hover:underline font-bold text-[11px] inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <span>Salin Link</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance Log Box -->
                                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 flex flex-col justify-between gap-3 text-xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">Presensi Tatap Muka:</span>
                                            <span class="font-black text-blue-600 dark:text-blue-400 text-xs">{{ $zoom->participants->count() }} / {{ $courseClass->schoolClass->students->count() }}</span>
                                        </div>
                                        @if($isStudent && $student)
                                            @if($hasAttended)
                                                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-1.5 text-[11px]">
                                                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Kehadiran Anda Tercatat</span>
                                                </div>
                                            @else
                                                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 font-medium text-[11px]">
                                                    Klik <strong>Masuk Zoom</strong> untuk otomatis mencatat kehadiran Anda.
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Preview Participant Badges -->
                                        @if($zoom->participants->isNotEmpty())
                                            <div class="mt-2.5 space-y-1">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Siswa Bergabung:</span>
                                                <div class="max-h-24 overflow-y-auto space-y-1 pr-1 text-[11px]">
                                                    @foreach($zoom->participants->take(10) as $pt)
                                                        <div class="flex items-center justify-between py-0.5 text-slate-700 dark:text-slate-300">
                                                            <span class="truncate">{{ $pt->student->name ?? 'Siswa' }}</span>
                                                            <span class="text-[10px] text-slate-400 shrink-0">{{ $pt->joined_at->format('H:i') }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if($zoom->participants->count() > 10)
                                                        <p class="text-[10px] text-slate-400 italic text-center">+ {{ $zoom->participants->count() - 10 }} siswa lainnya</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-[11px] text-slate-400 italic mt-1">Belum ada siswa yang masuk ke ruang Zoom.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<!-- ======================================================== -->
<!-- MODAL: UNGGAH / GENERATE MATERI PEMBELAJARAN (GURU / ADMIN) -->
<!-- ======================================================== -->
@if($canManage)
<div id="modal-add-material" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kelola Materi Pembelajaran</h3>
                <p class="text-xs text-slate-500">Pilih generate modul interaktif AI atau unggah berkas manual</p>
            </div>
            <button onclick="document.getElementById('modal-add-material').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex items-center gap-2 mt-4 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80">
            <button type="button" onclick="switchModalMode('modal-add-material', 'ai')" class="btn-tab-ai flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-indigo-600 text-white shadow-xs">
                <span>✨ Generate AI Interaktif</span>
                <span class="text-[9px] bg-white/20 px-1.5 py-0.2 rounded-full font-black">13/2025</span>
            </button>
            <button type="button" onclick="switchModalMode('modal-add-material', 'manual')" class="btn-tab-manual flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                <span>Unggah Manual (File/Video)</span>
            </button>
        </div>

        <!-- SUB-TAB 1: AI GENERATOR (PERMENDIKDASMEN NO. 13 TAHUN 2025) -->
        <div class="modal-mode-ai mt-4 space-y-4">
            <div class="p-3.5 rounded-2xl bg-cyan-50/60 dark:bg-cyan-950/30 border border-cyan-200/60 dark:border-cyan-800/60 text-xs text-cyan-900 dark:text-cyan-200 space-y-1">
                <div class="font-black flex items-center gap-1.5">
                    <span>📜 Standar Permendikdasmen No. 13 Tahun 2025:</span>
                </div>
                <p class="leading-relaxed text-[11px] text-cyan-800 dark:text-cyan-300">
                    Modul disusun otomatis dengan <strong>Penjelasan Terperinci, akurat, sesuai & Lengkap</strong> (bukan sekadar rangkuman), <strong>Analogi Dunia Nyata</strong> yang mudah dipahami, Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), Kartu Konsep Interaktif (Flashcards), Cek Pemahaman Instan, Studi Kasus, dan Glosarium.
                </p>
            </div>

            <form action="{{ route('lms.materials.ai_generate', $courseClass->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Fase Kurikulum Nasional *</label>
                        <select name="target_fase" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="Fase E (Kelas X SMA)" {{ str_contains($courseClass->schoolClass->name, 'X-') || str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase E (Kelas X SMA)</option>
                            <option value="Fase F (Kelas XI - XII SMA)" {{ !str_contains($courseClass->schoolClass->name, 'X-') && !str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase F (Kelas XI - XII SMA)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Elemen *</label>
                        <input type="text" name="elemen" required placeholder="Contoh: Pemahaman Fisika / Menyimak / Geometri" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Materi Pembelajaran *</label>
                    <input type="text" name="materi" id="mat_ai_materi" required oninput="document.getElementById('mat_ai_topic_alias').value = this.value" placeholder="Contoh: Dinamika Gerak Lurus dan Penerapan Hukum Newton" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <input type="hidden" name="topic" id="mat_ai_topic_alias">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Capaian Pembelajaran (CP) *</label>
                    <textarea name="capaian_pembelajaran" rows="2" required placeholder="Tuliskan Capaian Pembelajaran (CP) yang menjadi acuan modul ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) *</label>
                    <textarea name="tujuan_pembelajaran" rows="2" required placeholder="Tuliskan Tujuan Pembelajaran (TP) terukur yang ingin dicapai peserta didik..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alur Tujuan Pembelajaran (ATP) *</label>
                    <textarea name="alur_tujuan_pembelajaran" rows="2" required placeholder="Tuliskan tahapan atau Alur Tujuan Pembelajaran (ATP) modul ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <!-- TIPE KONTEN: CUKUP RANGKUMAN & ARTIKEL TEKS INTERAKTIF -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Konten Pembelajaran</label>
                    <input type="hidden" name="content_type" value="text">
                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-cyan-50/80 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800/70 text-cyan-950 dark:text-cyan-200 text-xs">
                        <span class="text-base">📰</span>
                        <div class="flex-1">
                            <div class="font-bold flex items-center gap-2">
                                <span>Rangkuman & Artikel Teks Interaktif</span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-200/70 dark:bg-cyan-800/60 font-black text-cyan-900 dark:text-cyan-100">Baku Kurikulum Nasional</span>
                            </div>
                            <p class="text-[10px] text-cyan-800 dark:text-cyan-300 mt-0.5">AI wajib mengenerate modul lengkap berbasis teks interaktif, analogi kontekstual, flashcards konsep, serta uji pemahaman yang berakar langsung pada form kurikulum di atas.</p>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi / Petunjuk Singkat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat / Arahan Guru (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Tuliskan petunjuk belajar atau instruksi khusus untuk siswa..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Mulai Dapat Diakses Siswa)</label>
                    <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika ingin langsung dapat dibuka saat ini juga. Sekalipun waktu telah lewat, materi tetap tampil dan dapat diakses siswa.</p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="mat_auto_publish" name="auto_publish" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="mat_auto_publish" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Langsung publikasikan ke siswa kelas ini</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-material').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        🚀 Generate Modul Interaktif Terperinci
                    </button>
                </div>
            </form>
        </div>

        <!-- SUB-TAB 2: UNGGAH MANUAL (MODEL LAMA DIPERTAHANKAN) -->
        <div class="modal-mode-manual mt-4 space-y-3.5 hidden">
            <form action="{{ route('lms.materials.store', $courseClass->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Materi *</label>
                    <input type="text" name="title" required placeholder="Contoh: Modul 1: Hukum Newton & Gravitasi" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                @if(isset($objectives) && $objectives->isNotEmpty())
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">🎯 Keterkaitan Tujuan Pembelajaran (TP)</label>
                    <select name="learning_objective_id" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">-- Tanpa Kaitan TP Spesifik --</option>
                        @foreach($objectives as $tp)
                            <option value="{{ $tp->id }}">[TP {{ $tp->tp_code }}] {{ $tp->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Bab</label>
                    <input type="text" name="topic" placeholder="Contoh: Bab 1: Mekanika Klasik" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Konten *</label>
                    <select name="content_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="document">Dokumen / Modul (PDF, PPT, DOC, ZIP)</option>
                        <option value="video">Video Pembelajaran (YouTube / Embed / MP4)</option>
                        <option value="text">Rangkuman / Artikel Teks</option>
                        <option value="link">Tautan Luar / Website Edukasi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">URL Video (Opsional, YouTube / Google Drive)</label>
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Unggah Berkas Lampiran (Max 50MB)</label>
                    <input type="file" name="attachment" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Tuliskan petunjuk belajar materi..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Mulai Dapat Diakses Siswa)</label>
                    <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika materi dapat langsung dibuka kapan saja. Sekalipun waktu telah lewat, materi tetap tampil dan dapat diakses siswa.</p>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-material').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: BUAT / GENERATE TUGAS & PR (GURU / ADMIN) -->
<!-- ======================================================== -->
<div id="modal-add-assignment" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kelola Penugasan & PR</h3>
                <p class="text-xs text-slate-500">Generate tugas & rubrik KKTP via AI atau susun tugas manual</p>
            </div>
            <button onclick="document.getElementById('modal-add-assignment').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex items-center gap-2 mt-4 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80">
            <button type="button" onclick="switchModalMode('modal-add-assignment', 'ai')" class="btn-tab-ai flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-indigo-600 text-white shadow-xs">
                <span>✨ Generate AI Tugas & PR</span>
                <span class="text-[9px] bg-white/20 px-1.5 py-0.2 rounded-full font-black">13/2025</span>
            </button>
            <button type="button" onclick="switchModalMode('modal-add-assignment', 'manual')" class="btn-tab-manual flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                <span>Buat Tugas Manual</span>
            </button>
        </div>

        <!-- SUB-TAB 1: AI GENERATOR TUGAS & PR (PERMENDIKDASMEN NO. 13 TAHUN 2025) -->
        <div class="modal-mode-ai mt-4 space-y-4">
            <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-200 space-y-1">
                <div class="font-black flex items-center gap-1.5">
                    <span>📜 Rubrik KKTP Kurikulum Nasional:</span>
                </div>
                <p class="leading-relaxed text-[11px] text-amber-800 dark:text-amber-300">
                    Instruksi tugas dirancang dengan kriteria ketercapaian tujuan pembelajaran (KKTP) dalam 4 tingkatan standar: Baru Berkembang (<70), Layak (70-79), Cakap (80-89), dan Mahir (90-100).
                </p>
            </div>

            <form action="{{ route('lms.assignments.ai_generate', $courseClass->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Fase Kurikulum Nasional *</label>
                        <select name="target_fase" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="Fase E (Kelas X SMA)" {{ str_contains($courseClass->schoolClass->name, 'X-') || str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase E (Kelas X SMA)</option>
                            <option value="Fase F (Kelas XI - XII SMA)" {{ !str_contains($courseClass->schoolClass->name, 'X-') && !str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase F (Kelas XI - XII SMA)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Elemen *</label>
                        <input type="text" name="elemen" required placeholder="Contoh: Pemahaman Fisika / Menulis Teks Rekon / Geometri" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Materi Penugasan *</label>
                    <input type="text" name="materi" id="assign_ai_materi" required oninput="document.getElementById('assign_ai_topic_alias').value = this.value" placeholder="Contoh: Analisis Kinematika Gerak Lurus & Penerapan Hukum Gravitasi" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <input type="hidden" name="topic" id="assign_ai_topic_alias">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Capaian Pembelajaran (CP) *</label>
                    <textarea name="capaian_pembelajaran" rows="2" required placeholder="Tuliskan Capaian Pembelajaran (CP) penugasan / PR ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) *</label>
                    <textarea name="tujuan_pembelajaran" rows="2" required placeholder="Tuliskan Tujuan Pembelajaran (TP) yang diukur pada penugasan / PR ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alur Tujuan Pembelajaran (ATP) *</label>
                    <textarea name="alur_tujuan_pembelajaran" rows="2" required placeholder="Tuliskan tahapan atau Alur Tujuan Pembelajaran (ATP) terkait..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bentuk / Jenis Penugasan *</label>
                    <select name="task_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="tugas_autentik">Tugas Pembelajaran Autentik Bermakna</option>
                        <option value="pr_mandiri" selected>Tugas & Pekerjaan Rumah (PR) Mandiri Terstruktur</option>
                        <option value="studi_kasus">Penugasan Analisis Studi Kasus Kontekstual</option>
                        <option value="proyek_mini">Penugasan Proyek Mini Berbasis Masalah (PBL)</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Dapat Dibuka Siswa)</label>
                        <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika langsung dapat dibuka.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Waktu Pengumpulan (Deadline) *</label>
                        <input type="datetime-local" name="due_date" value="{{ now()->addDays(7)->format('Y-m-d\T23:59') }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Tugas tetap tampil & dapat diakses walau waktu lewat.</p>
                    </div>
                </div>

                <!-- Ketentuan Format Jawaban Siswa (AI) -->
                <div class="space-y-2.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Format Pengumpulan Jawaban Siswa *
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="text" onchange="toggleAssignmentFileTypeSettings('ai', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📝 Teks Langsung</span>
                            <span class="text-[9px] text-slate-400">Ketik jawaban esai</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="file" onchange="toggleAssignmentFileTypeSettings('ai', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📁 Unggah Berkas</span>
                            <span class="text-[9px] text-slate-400">Wajib upload file</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="both" checked onchange="toggleAssignmentFileTypeSettings('ai', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📑 Teks & Berkas</span>
                            <span class="text-[9px] text-slate-400">Fleksibel / Keduanya</span>
                        </label>
                    </div>

                    <!-- Ketentuan Format Berkas (PDF, PPT, Gambar) -->
                    <div id="ai_file_types_wrapper" class="pt-2 border-t border-slate-200 dark:border-slate-700 space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                            Ketentuan Bentuk Berkas yang Boleh Diunggah Siswa:
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="pdf" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Dokumen PDF</div>
                                    <div class="text-[9px] text-slate-400">.pdf</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="ppt" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Presentasi PPT</div>
                                    <div class="text-[9px] text-slate-400">.ppt, .pptx</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="image" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Foto / Gambar</div>
                                    <div class="text-[9px] text-slate-400">.jpg, .png, .webp</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="asg_auto_publish" name="auto_publish" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="asg_auto_publish" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Langsung publikasikan penugasan ke siswa</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-assignment').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        🚀 Generate Penugasan & KKTP
                    </button>
                </div>
            </form>
        </div>

        <!-- SUB-TAB 2: BUAT MANUAL (MODEL LAMA DIPERTAHANKAN) -->
        <div class="modal-mode-manual mt-4 space-y-3.5 hidden">
            <form action="{{ route('lms.assignments.store', $courseClass->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Tugas *</label>
                    <input type="text" name="title" required placeholder="Contoh: Tugas Mandiri: Analisis GLBB & Gerak Parabola" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                @if(isset($objectives) && $objectives->isNotEmpty())
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">🎯 Keterkaitan Tujuan Pembelajaran (TP)</label>
                    <select name="learning_objective_id" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">-- Tanpa Kaitan TP Spesifik --</option>
                        @foreach($objectives as $tp)
                            <option value="{{ $tp->id }}">[TP {{ $tp->tp_code }}] {{ $tp->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instruksi & Soal Tugas *</label>
                    <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                        @include('lms.partials.rich_editor_toolbar', ['targetId' => 'manual_assignment_description'])
                        <textarea id="manual_assignment_description" name="description" rows="4" required placeholder="Tuliskan detail instruksi tugas yang harus dikerjakan siswa. Anda bisa menyisipkan gambar, rumus matematika, rumus kimia, simbol fisika, teks Arab, maupun ayat Al-Qur'an..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai</label>
                        <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <p class="text-[10px] text-slate-400 mt-0.5">Dapat dibuka siswa.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Waktu (Deadline) *</label>
                        <input type="datetime-local" name="due_date" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <p class="text-[10px] text-slate-400 mt-0.5">Tetap tampil setelah lewat.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Maksimal *</label>
                        <input type="number" name="max_score" value="100" min="10" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>

                <!-- Ketentuan Format Jawaban Siswa (Manual) -->
                <div class="space-y-2.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Format Pengumpulan Jawaban Siswa *
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="text" onchange="toggleAssignmentFileTypeSettings('manual', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📝 Teks Langsung</span>
                            <span class="text-[9px] text-slate-400">Ketik jawaban esai</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="file" onchange="toggleAssignmentFileTypeSettings('manual', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📁 Unggah Berkas</span>
                            <span class="text-[9px] text-slate-400">Wajib upload file</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-2.5 flex flex-col items-center text-center gap-1 transition bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 hover:border-indigo-400">
                            <input type="radio" name="submission_type" value="both" checked onchange="toggleAssignmentFileTypeSettings('manual', this.value)" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">📑 Teks & Berkas</span>
                            <span class="text-[9px] text-slate-400">Fleksibel / Keduanya</span>
                        </label>
                    </div>

                    <!-- Ketentuan Format Berkas (PDF, PPT, Gambar) -->
                    <div id="manual_file_types_wrapper" class="pt-2 border-t border-slate-200 dark:border-slate-700 space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                            Ketentuan Bentuk Berkas yang Boleh Diunggah Siswa:
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="pdf" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Dokumen PDF</div>
                                    <div class="text-[9px] text-slate-400">.pdf</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="ppt" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Presentasi PPT</div>
                                    <div class="text-[9px] text-slate-400">.ppt, .pptx</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="allowed_file_types[]" value="image" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="text-[11px]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Foto / Gambar</div>
                                    <div class="text-[9px] text-slate-400">.jpg, .png, .webp</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lampiran Berkas Soal Guru (Opsional, PDF/Word/Gambar)</label>
                    <input type="file" name="attachment" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-assignment').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold">Terbitkan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: BUKA SESI PRESENSI BARU (GURU / ADMIN) -->
<!-- ======================================================== -->
<div id="modal-add-attendance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Buka Sesi Presensi Pertemuan Mapel</h3>
                <p class="text-[11px] text-slate-500">Otomatis terbuka & berulang setiap nama hari & jam (tanpa input tanggal manual)</p>
            </div>
            <button onclick="document.getElementById('modal-add-attendance').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>
        <form action="{{ route('lms.attendances.store', $courseClass->id) }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Keterangan Pertemuan *</label>
                <input type="text" name="title" required value="Presensi Rutin Mapel {{ $courseClass->subject->name }}" placeholder="Contoh: Presensi Pertemuan Rutin Mapel" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
            </div>

            <!-- Pilihan Mode Jadwal Presensi: Otomatis vs Manual -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilihan Pengaturan Jadwal Presensi *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="relative flex flex-col p-3 rounded-2xl border-2 cursor-pointer transition border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800" id="label-schedule-auto">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="schedule_type" value="auto" checked onchange="toggleAttendanceScheduleMode('auto')" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>🤖 Otomatis</span>
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-extrabold uppercase">Rekomendasi</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 pl-5 leading-relaxed">
                            Mengambil dari <strong>Tugaskan Guru / Jadwal Pembelajaran Kelas</strong>.
                        </p>
                    </label>

                    <label class="relative flex flex-col p-3 rounded-2xl border-2 cursor-pointer transition border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800" id="label-schedule-manual">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="schedule_type" value="manual" onchange="toggleAttendanceScheduleMode('manual')" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>✍️ Manual</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 pl-5 leading-relaxed">
                            Atur hari, jam mulai, dan jam selesai secara manual / kustom.
                        </p>
                    </label>
                </div>
            </div>

            <!-- Preview Box Jadwal Otomatis (Saat Mode Otomatis Aktif) -->
            <div id="attendance-auto-info-box" class="p-3.5 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 space-y-2">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 text-xs font-black shadow-xs">
                        🗓️
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Jadwal Pembelajaran Kelas Belajar (Tersinkron Otomatis):</h4>
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Sinkron</span>
                        </div>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @php
                                $classSlots = $courseClass->getSchedulesList();
                            @endphp
                            @foreach($classSlots as $cs)
                                <span class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-emerald-300 dark:border-emerald-700 text-xs font-bold text-emerald-800 dark:text-emerald-300 shadow-2xs">
                                    {{ $cs['day'] }} ({{ $cs['start_time'] }} - {{ $cs['end_time'] }} WIB)
                                </span>
                            @endforeach
                        </div>
                        <p class="text-[10.5px] text-emerald-700/90 dark:text-emerald-300/80 mt-1.5">
                            ✓ Diambil otomatis dari menu <em>"Tugaskan Diri/Tugaskan Guru Pengampu ke Kelas Belajar &rarr; Jadwal Pembelajaran"</em>. Sesi presensi akan otomatis dibuka dan berulang mengikuti jadwal ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dynamic Recurring Days & Times Container (Untuk Mode Manual) -->
            <div id="attendance-manual-container" class="hidden space-y-2">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Jadwal Hari & Jam Presensi Manual (Bisa Lebih dari 1 Hari / Minggu) *
                    </label>
                    <button type="button" onclick="addAttendanceScheduleRow()" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                        <span>+ Tambah Hari Lain</span>
                    </button>
                </div>
                <div id="attendance-schedule-rows-container" class="space-y-2">
                    @php
                        $defaultSchedules = $courseClass->getSchedulesList();
                        if (empty($defaultSchedules)) {
                            $defaultSchedules = [['day' => 'Senin', 'start_time' => '07:30', 'end_time' => '09:00']];
                        }
                    @endphp
                    @foreach($defaultSchedules as $idx => $ds)
                        <div class="attendance-schedule-row grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="col-span-5">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Hari</label>
                                <select name="attendance_days[]" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-medium">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                                        <option value="{{ $h }}" {{ strcasecmp($ds['day'] ?? '', $h) === 0 ? 'selected' : '' }}>{{ $h }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Mulai</label>
                                <input type="time" name="attendance_starts[]" value="{{ $ds['start_time'] ?? '07:30' }}" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Selesai</label>
                                <input type="time" name="attendance_ends[]" value="{{ $ds['end_time'] ?? '09:00' }}" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
                            </div>
                            <div class="col-span-1 pt-3.5 text-center">
                                <button type="button" onclick="removeAttendanceScheduleRow(this)" class="text-slate-400 hover:text-rose-500 text-xs font-bold transition" title="Hapus hari">✕</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Tentukan hari dan jam presensi secara mandiri di luar jadwal pembelajaran kelas.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Presensi Kehadiran *</label>
                <select name="attendance_method" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="both">Selfie & Manual (Guru & Sekretaris Kelas)</option>
                    <option value="selfie">Hanya Selfie Siswa</option>
                    <option value="manual">Manual (Guru & Sekretaris Kelas)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Pertemuan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Pembelajaran Lab Komputer / Daring Google Meet" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-add-attendance').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs">
                    Aktifkan Sesi Presensi Rutin
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: BUAT SESI TATAP MUKA ZOOM MEET BARU (GURU / ADMIN) -->
<!-- ======================================================== -->
<div id="modal-add-zoom-meeting" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Jadwalkan Sesi Zoom Meet</h3>
                    <p class="text-xs text-slate-500">Khusus siswa rombel {{ $courseClass->schoolClass->name }}</p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-add-zoom-meeting').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.zoom.store', $courseClass->id) }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Sesi Pertemuan *</label>
                <input type="text" name="title" required placeholder="Contoh: Tatap Muka Bab 4: Konsep Algoritma & Pemrograman" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Materi Terkait (Opsional)</label>
                <input type="text" name="topic" placeholder="Contoh: Algoritma Pencarian & Pengurutan" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tautan / Link Zoom Meeting *</label>
                <input type="url" name="meeting_url" required placeholder="https://us05web.zoom.us/j/1234567890?pwd=..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                <span class="text-[10px] text-slate-400 mt-0.5 block">Tempel link invitation langsung dari aplikasi Zoom.</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Meeting ID (Opsional)</label>
                    <input type="text" name="meeting_id" placeholder="892 1234 5678" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Passcode (Opsional)</label>
                    <input type="text" name="passcode" placeholder="123456" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Mulai *</label>
                    <input type="datetime-local" name="start_time" value="{{ date('Y-m-d\TH:i') }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Selesai (Opsional)</label>
                    <input type="datetime-local" name="end_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sesi *</label>
                <select name="status" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="live">Langsung Aktif (Live Sekarang)</option>
                    <option value="scheduled">Jadwalkan untuk Nanti</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Agenda Pertemuan / Petunjuk Belajar (Opsional)</label>
                <textarea name="agenda" rows="2" placeholder="Tuliskan tujuan tatap muka, instruksi persiapan modul, dll." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <label class="flex items-center gap-2.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" name="auto_create_attendance" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-bold">Otomatis Buka Presensi Mapel</span>
                        <p class="text-[10px] text-slate-500">Membuat rekapan sesi presensi pertemuan mapel otomatis untuk sesi ini.</p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-add-zoom-meeting').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-sm transition">Jadwalkan Sesi Zoom</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: BUAT / GENERATE ASESMEN CBT (GURU / ADMIN) -->
<!-- ======================================================== -->
<div id="modal-add-quiz" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kelola Asesmen CBT Daring</h3>
                <p class="text-xs text-slate-500">Generate paket ujian & butir soal HOTS otomatis atau susun manual</p>
            </div>
            <button onclick="document.getElementById('modal-add-quiz').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex items-center gap-2 mt-4 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80">
            <button type="button" onclick="switchModalMode('modal-add-quiz', 'ai')" class="btn-tab-ai flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-indigo-600 text-white shadow-xs">
                <span>✨ Generate Paket CBT AI</span>
                <span class="text-[9px] bg-white/20 px-1.5 py-0.2 rounded-full font-black">13/2025</span>
            </button>
            <button type="button" onclick="switchModalMode('modal-add-quiz', 'manual')" class="btn-tab-manual flex-1 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                <span>Buat Asesmen Manual</span>
            </button>
        </div>

        <!-- SUB-TAB 1: AI GENERATOR ASESMEN CBT (PERMENDIKDASMEN NO. 13 TAHUN 2025) -->
        <div class="modal-mode-ai mt-4 space-y-4">
            <div class="p-3.5 rounded-2xl bg-purple-50/60 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/60 text-xs text-purple-900 dark:text-purple-200 space-y-1">
                <div class="font-black flex items-center gap-1.5">
                    <span>📜 Standar Asesmen Digital Permendikdasmen No. 13 Tahun 2025:</span>
                </div>
                <p class="leading-relaxed text-[11px] text-purple-800 dark:text-purple-300">
                    AI langsung menyusun paket CBT beserta butir soal standar AKM / HOTS level C3–C5 hingga 50 butir soal. Anda dapat mengombinasikan berbagai bentuk soal (Pilihan Ganda, PG Kompleks, Benar/Salah, Menjodohkan, dan Isian Singkat) yang otomatis dapat diturunkan ke dokumen <strong>Kisi-Kisi</strong> dan <strong>Kartu Soal</strong> resmi.
                </p>
            </div>

            <form action="{{ route('lms.quizzes.ai_generate', $courseClass->id) }}" method="POST" id="form-ai-generate-cbt" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Fase Kurikulum Nasional *</label>
                        <select name="target_fase" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="Fase E (Kelas X SMA)" {{ str_contains($courseClass->schoolClass->name, 'X-') || str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase E (Kelas X SMA)</option>
                            <option value="Fase F (Kelas XI - XII SMA)" {{ !str_contains($courseClass->schoolClass->name, 'X-') && !str_contains($courseClass->schoolClass->name, '10') ? 'selected' : '' }}>Fase F (Kelas XI - XII SMA)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Elemen *</label>
                        <input type="text" name="elemen" required placeholder="Contoh: Pemahaman Fisika / Keterampilan Proses" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Materi Asesmen & CBT *</label>
                    <input type="text" name="materi" id="cbt_ai_materi" required oninput="document.getElementById('cbt_ai_topic_alias').value = this.value" placeholder="Contoh: Kinematika Gerak Lurus & Penerapan Hukum Gravitasi Newton" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <input type="hidden" name="topic" id="cbt_ai_topic_alias">
                    <p class="mt-1 text-[11px] text-slate-500">Soal HOTS akan dikonstruksi secara proporsional menguji materi pokok di atas.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Capaian Pembelajaran (CP) *</label>
                    <textarea name="capaian_pembelajaran" rows="2" required placeholder="Tuliskan Capaian Pembelajaran (CP) yang diujikan dalam CBT ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) *</label>
                    <textarea name="tujuan_pembelajaran" rows="2" required placeholder="Tuliskan Tujuan Pembelajaran (TP) yang menjadi sasaran indikator butir soal..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alur Tujuan Pembelajaran (ATP) *</label>
                    <textarea name="alur_tujuan_pembelajaran" rows="2" required placeholder="Tuliskan Alur Tujuan Pembelajaran (ATP) asesmen ini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Asesmen *</label>
                        <select name="quiz_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="formatif">Asesmen Formatif (Kuis Cek Penguasaan)</option>
                            <option value="sumatif_materi" selected>Asesmen Sumatif (Lingkup Materi / UH)</option>
                            <option value="sts">Sumatif Tengah Semester (STS)</option>
                            <option value="sas">Sumatif Akhir Semester (SAS)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Total Butir Soal (Maks. 50) *</label>
                        <div class="flex items-center gap-2">
                            <select id="cbt_preset_count" onchange="applyCbtPresetCount(this.value)" class="w-2/3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                                <option value="5" selected>5 Butir Soal</option>
                                <option value="10">10 Butir Soal</option>
                                <option value="15">15 Butir Soal</option>
                                <option value="20">20 Butir Soal</option>
                                <option value="25">25 Butir Soal</option>
                                <option value="30">30 Butir Soal</option>
                                <option value="40">40 Butir Soal</option>
                                <option value="50">50 Butir Soal</option>
                                <option value="custom">Kustom Angka...</option>
                            </select>
                            <input type="number" id="cbt_target_total" name="question_count" value="5" min="1" max="50" oninput="onCustomCbtTargetChange(this.value)" class="w-1/3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-center font-bold text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- KOMPOSISI 5 BENTUK SOAL (MULTIPLE TYPES SELECTION) -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <span>🎯 Komposisi Bentuk Soal (Multi-Tipe)</span>
                            </span>
                            <p class="text-[11px] text-slate-500">Centang bentuk soal yang diinginkan & atur jumlah butir masing-masing</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="distributeEvenlyCbt()" class="px-2 py-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 rounded-lg border border-indigo-200 dark:border-indigo-700/60 hover:bg-indigo-100 transition">
                                Bagi Rata
                            </button>
                            <button type="button" onclick="resetToSingleChoiceCbt()" class="px-2 py-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 transition">
                                Hanya PG
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <!-- 1. Pilihan Ganda -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_mc" onchange="toggleCbtType('mc')" checked class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_mc" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Pilihan Ganda (PG)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Opsi A–E, 1 Kunci</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_mc" name="count_multiple_choice" value="5" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- 2. Pilihan Ganda Kompleks -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_cmc" onchange="toggleCbtType('cmc')" class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_cmc" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Pilihan Ganda Kompleks (PGK)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Multi-Pernyataan Benar</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_cmc" name="count_complex_multiple_choice" value="0" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 3. Benar / Salah -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_tf" onchange="toggleCbtType('tf')" class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_tf" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Benar / Salah (B-S)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Analisis Validitas Konsep</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_tf" name="count_true_false" value="0" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 4. Menjodohkan -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_match" onchange="toggleCbtType('match')" class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_match" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Menjodohkan (Matching)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Premis vs Pasangan Opsi</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_match" name="count_matching" value="0" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 5. Isian Singkat -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_sa" onchange="toggleCbtType('sa')" class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_sa" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Isian Singkat
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Istilah / Definisi Kunci</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_sa" name="count_short_answer" value="0" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 6. Uraian / Esai -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="chk_essay" onchange="toggleCbtType('essay')" class="cbt-type-chk rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="chk_essay" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Uraian / Esai (HOTS)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Penalaran & Sintesis Analitis</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="count_essay" name="count_essay" value="0" min="0" max="50" oninput="recalcCbtTotal()" class="cbt-type-input w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Total Realtime Indicator -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-xs">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Akumulasi Butir Soal Terpilih:</span>
                        <div class="flex items-center gap-1.5">
                            <span id="cbt_live_total" class="px-2.5 py-0.5 rounded-full font-black text-xs bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                5 / 50 Butir
                            </span>
                        </div>
                    </div>
                    <div id="cbt_warning_limit" class="text-[11px] text-rose-500 font-semibold hidden">
                        ⚠️ Total butir soal melebihi batas maksimal 50 butir! Silakan kurangi jumlah butir soal.
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Pengerjaan (Menit) *</label>
                        <input type="number" name="duration_minutes" value="60" min="10" max="180" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">KKTP Kelulusan</label>
                        <input type="number" value="{{ $courseClass->subject->kkm ?? 75 }}" disabled class="w-full text-xs bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-500">
                    </div>
                </div>

                <!-- Jadwal Mulai & Batas Selesai Ujian -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jadwal Mulai Ujian (Opsional)</label>
                        <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Selesai Ujian (Opsional)</label>
                        <input type="datetime-local" name="end_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="ai_quiz_random" name="is_randomized" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="ai_quiz_random" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Acak urutan soal otomatis saat dikerjakan siswa</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="ai_quiz_publish" name="auto_publish" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="ai_quiz_publish" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Langsung publikasikan asesmen ini ke siswa kelas</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-quiz').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" id="btn-submit-cbt-ai" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        🚀 Generate Paket CBT & Soal HOTS
                    </button>
                </div>
            </form>
        </div>

        <!-- SUB-TAB 2: BUAT MANUAL (MODEL LAMA DIPERTAHANKAN) -->
        <div class="modal-mode-manual mt-4 space-y-3.5 hidden">
            <form action="{{ route('lms.quizzes.store', $courseClass->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Asesmen / Ujian *</label>
                    <input type="text" name="title" required placeholder="Contoh: Asesmen Sumatif 1: Konsep Dasar Dinamika Gerak" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Topik / Ruang Lingkup Materi Asesmen</label>
                        <span class="text-[10px] text-slate-500 font-semibold">Bisa Lebih dari 1 Topik</span>
                    </div>
                    <textarea name="topic" rows="2" placeholder="Contoh:&#10;1. Kinematika Gerak Lurus&#10;2. Dinamika Hukum Newton" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
                    <p class="mt-1 text-[11px] text-slate-500">Pisahkan dengan baris baru, koma, atau titik koma jika mencakup lebih dari satu ruang lingkup materi.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Asesmen *</label>
                        <select name="quiz_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="formatif">Asesmen Formatif (Kuis / Latihan)</option>
                            <option value="sumatif_materi">Asesmen Sumatif (Lingkup Materi / UH)</option>
                            <option value="sts">Sumatif Tengah Semester (STS)</option>
                            <option value="sas">Sumatif Akhir Semester (SAS)</option>
                            <option value="saj">Sumatif Akhir Jenjang (SAJ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Pengerjaan (Menit) *</label>
                        <input type="number" name="duration_minutes" value="60" min="5" max="180" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                    <input type="number" name="passing_score" value="{{ $courseClass->getEffectiveKktp() }}" min="50" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>

                <!-- KOMPOSISI BENTUK SOAL MULTI-TIPE (MANUAL) -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <span>🎯 Komposisi Bentuk Soal (Multi-Tipe)</span>
                            </span>
                            <p class="text-[11px] text-slate-500">Tentukan jumlah butir per bentuk soal yang ingin Anda susun</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" onclick="manualSelectAllCbtTypes()" class="px-2.5 py-1 text-[10px] font-bold bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow-2xs transition flex items-center gap-1" title="Pilih dan aktifkan semua 6 bentuk soal sekaligus">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Pilih Semua Tipe</span>
                            </button>
                            <button type="button" onclick="manualDistributeEvenlyCbt()" class="px-2 py-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 rounded-lg border border-indigo-200 dark:border-indigo-700/60 hover:bg-indigo-100 transition">
                                Bagi Rata
                            </button>
                            <button type="button" onclick="manualResetToSingleChoiceCbt()" class="px-2 py-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 transition">
                                Hanya PG
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <!-- 1. Pilihan Ganda -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_mc" onchange="manualToggleCbtType('mc')" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_mc" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Pilihan Ganda (PG)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Opsi A–E, 1 Kunci</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_mc" name="count_multiple_choice" value="5" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- 2. Pilihan Ganda Kompleks -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_cmc" onchange="manualToggleCbtType('cmc')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_cmc" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Pilihan Ganda Kompleks (PGK)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Multi-Pernyataan Benar</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_cmc" name="count_complex_mc" value="0" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 3. Benar / Salah -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_tf" onchange="manualToggleCbtType('tf')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_tf" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Benar / Salah (B-S)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Analisis Validitas Konsep</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_tf" name="count_true_false" value="0" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 4. Menjodohkan -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_match" onchange="manualToggleCbtType('match')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_match" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Menjodohkan (Matching)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Korelasi Premis & Respon</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_match" name="count_matching" value="0" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 5. Isian Singkat -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_sa" onchange="manualToggleCbtType('sa')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_sa" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Isian Singkat (Short Answer)
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Jawaban Presisi / Terminologi</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_sa" name="count_short_answer" value="0" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>

                        <!-- 6. Uraian / Esai HOTS -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual_chk_essay" onchange="manualToggleCbtType('essay')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="manual_chk_essay" class="cursor-pointer font-semibold text-slate-700 dark:text-slate-200">
                                    Uraian / Esai Penalaran HOTS
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">Penilaian Rubrik Berjenjang</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400">Butir:</span>
                                <input type="number" id="manual_count_essay" name="count_essay" value="0" min="0" max="50" oninput="manualRecalcCbtTotal()" class="w-16 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-center font-bold text-slate-900 dark:text-white" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Live Total Indicator -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-xs">
                        <span class="text-slate-500">Total Butir Soal Terkonfigurasi:</span>
                        <span id="manual_cbt_total_indicator" class="font-extrabold text-indigo-600 dark:text-indigo-400">5 Butir Soal</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jadwal Mulai Ujian (Opsional)</label>
                        <input type="datetime-local" name="start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Selesai Ujian (Opsional)</label>
                        <input type="datetime-local" name="end_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tata Tertib / Petunjuk Asesmen</label>
                    <textarea name="instructions" rows="2" placeholder="Dilarang membuka tab lain, kerjakan dengan mandiri dan teliti..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="quiz_is_randomized" name="is_randomized" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="quiz_is_randomized" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Acak urutan butir soal saat dikerjakan siswa</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="quiz_is_published" name="is_published" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="quiz_is_published" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Langsung publikasikan asesmen ini ke siswa kelas</label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-quiz').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <span>Simpan & Lanjutkan ke Input Soal & Jawaban</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: AMBIL / SALIN DARI KELAS LAIN (IMPORT LINTAS KELAS) -->
<!-- ======================================================== -->
<div id="modal-import-from-class" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-teal-500/15 text-teal-600 dark:text-teal-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    </span>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ambil / Salin Konten dari Kelas Lain</h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Salin Materi Pelajaran, Tugas & PR, atau Paket Asesmen & CBT beserta butir soalnya dari kelas yang Anda ampu ke kelas ini.</p>
            </div>
            <button onclick="document.getElementById('modal-import-from-class').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.import_from_class', $courseClass->id) }}" method="POST" class="mt-4 space-y-4" id="form-import-from-class">
            @csrf

            <!-- 1. Pilihan Kelas Sumber -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Pilih Kelas Sumber <span class="text-rose-500">*</span>
                </label>
                <select name="source_class_id" id="source_class_select" onchange="loadSourceClassContents(this.value)" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Pilih Kelas Sumber Konten --</option>
                    @foreach($availableSourceClasses as $src)
                        @php
                            $isSameSub = ($src->subject_id === $courseClass->subject_id);
                        @endphp
                        <option value="{{ $src->id }}" data-same="{{ $isSameSub ? '1' : '0' }}">
                            {{ $isSameSub ? '⭐ [Mapel Sama] ' : '' }}{{ $src->schoolClass->name ?? 'Kelas' }} - {{ $src->subject->name ?? 'Mapel' }} {{ $src->teacher ? '('.$src->teacher->name.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @if($availableSourceClasses->isEmpty())
                    <p class="text-[11px] text-amber-500 mt-1">Belum ada kelas lain yang terdaftar atau diampu untuk disalin.</p>
                @else
                    <p class="text-[11px] text-slate-400 mt-1">💡 Kelas dengan mata pelajaran yang sama ditandai bintang ⭐ di daftar teratas.</p>
                @endif
            </div>

            <!-- Loading State -->
            <div id="source_class_loading" class="hidden py-8 text-center text-xs text-slate-500">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-teal-500 border-t-transparent mb-2"></div>
                <p>Memuat daftar materi, tugas, dan butir asesmen kelas sumber...</p>
            </div>

            <!-- Kontainer Konten Kelas Sumber -->
            <div id="source_class_contents_container" class="hidden space-y-4">
                <!-- Tab Kategori Konten -->
                <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <button type="button" onclick="switchSourceTab('materials')" id="btn-src-tab-materials" class="flex-1 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-900 text-teal-600 dark:text-teal-400 shadow-xs">
                        <span>📚 Materi</span>
                        <span id="badge-count-materials" class="px-1.5 py-0.2 rounded-full text-[10px] bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300">0</span>
                    </button>
                    <button type="button" onclick="switchSourceTab('assignments')" id="btn-src-tab-assignments" class="flex-1 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400">
                        <span>📝 Tugas & PR</span>
                        <span id="badge-count-assignments" class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">0</span>
                    </button>
                    <button type="button" onclick="switchSourceTab('quizzes')" id="btn-src-tab-quizzes" class="flex-1 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400">
                        <span>🎯 Asesmen & CBT</span>
                        <span id="badge-count-quizzes" class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">0</span>
                    </button>
                </div>

                <!-- Pane 1: Materi -->
                <div id="source-pane-materials" class="space-y-2">
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Materi Pelajaran:</span>
                        <button type="button" onclick="toggleSelectAllInContainer('source-materials-list', this)" class="text-[11px] text-teal-600 dark:text-teal-400 font-bold hover:underline">Pilih Semua</button>
                    </div>
                    <div id="source-materials-list" class="max-h-52 overflow-y-auto space-y-1.5 pr-1"></div>
                </div>

                <!-- Pane 2: Tugas -->
                <div id="source-pane-assignments" class="space-y-2 hidden">
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Tugas & PR:</span>
                        <button type="button" onclick="toggleSelectAllInContainer('source-assignments-list', this)" class="text-[11px] text-teal-600 dark:text-teal-400 font-bold hover:underline">Pilih Semua</button>
                    </div>
                    <div id="source-assignments-list" class="max-h-52 overflow-y-auto space-y-1.5 pr-1"></div>
                </div>

                <!-- Pane 3: Kuis & CBT -->
                <div id="source-pane-quizzes" class="space-y-2 hidden">
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Asesmen / CBT (beserta seluruh butir soal):</span>
                        <button type="button" onclick="toggleSelectAllInContainer('source-quizzes-list', this)" class="text-[11px] text-teal-600 dark:text-teal-400 font-bold hover:underline">Pilih Semua</button>
                    </div>
                    <div id="source-quizzes-list" class="max-h-52 overflow-y-auto space-y-1.5 pr-1"></div>
                </div>

                <!-- Pengaturan Tambahan Saat Salin -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 space-y-3">
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan Penyesuaian Salinan:</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Status Publikasi ke Siswa:</label>
                            <select name="publish_status" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                                <option value="keep">Pertahankan Status Asal</option>
                                <option value="published">Langsung Terbitkan (Aktif)</option>
                                <option value="draft">Simpan Sebagai Draf Dulu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Tenggat Waktu Tugas Baru:</label>
                            <select name="assignment_due_mode" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                                <option value="add_7_days">+7 Hari dari Sekarang (Rekomendasi)</option>
                                <option value="add_14_days">+14 Hari dari Sekarang</option>
                                <option value="keep">Gunakan Tanggal Asal Kelas Sumber</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty / Instructions state before selecting source class -->
            <div id="source_class_empty_hint" class="p-6 text-center rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-xs text-slate-500">
                Pilih salah satu kelas sumber di atas untuk melihat dan memilih Materi, Tugas, atau Asesmen yang ingin disalin.
            </div>

            <!-- Footer Action -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-import-from-class').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" id="btn-submit-import-from-class" disabled class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-extrabold flex items-center gap-1.5 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Salin Konten Terpilih ke Kelas Ini</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: SALIN SATU ITEM KE BEBERAPA KELAS LAIN -->
<!-- ======================================================== -->
<div id="modal-copy-item-to-classes" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span class="p-1 rounded-md bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    </span>
                    <span>Salin ke Kelas Lain</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">Duplikasi konten ini ke satu atau beberapa kelas lain yang Anda ampu.</p>
            </div>
            <button onclick="document.getElementById('modal-copy-item-to-classes').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.copy_to_classes', $courseClass->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="item_type" id="copy_item_type" value="material">
            <input type="hidden" name="item_id" id="copy_item_id" value="">

            <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60">
                <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400" id="copy_item_type_label">Materi Pelajaran</span>
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white mt-0.5 line-clamp-2" id="copy_item_title"></h4>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Kelas Tujuan:</label>
                    <button type="button" onclick="selectAllSameSubjectTargets()" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Pilih Semua Mapel Sama</button>
                </div>

                <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
                    @forelse($availableSourceClasses as $tc)
                        @php
                            $isSame = ($tc->subject_id === $courseClass->subject_id);
                        @endphp
                        <label class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs">
                            <input type="checkbox" name="target_class_ids[]" value="{{ $tc->id }}" data-same="{{ $isSame ? '1' : '0' }}" class="target-class-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $tc->schoolClass->name ?? 'Kelas' }}</span>
                                    @if($isSame)
                                        <span class="text-[9.5px] px-1.5 py-0.2 rounded font-black bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-400/30">⭐ Mapel Sama</span>
                                    @endif
                                </div>
                                <div class="text-[10.5px] text-slate-500 truncate">{{ $tc->subject->name ?? 'Mata Pelajaran' }}</div>
                            </div>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Tidak ada kelas lain yang tersedia.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Status Publikasi:</label>
                    <select name="publish_status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <option value="keep">Pertahankan Status</option>
                        <option value="published">Langsung Terbitkan</option>
                        <option value="draft">Simpan Sebagai Draf</option>
                    </select>
                </div>
                <div id="copy_due_mode_wrapper">
                    <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Tenggat (Jika Tugas):</label>
                    <select name="assignment_due_mode" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <option value="add_7_days">+7 Hari ke Depan</option>
                        <option value="add_14_days">+14 Hari ke Depan</option>
                        <option value="keep">Gunakan Tanggal Asal</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-copy-item-to-classes').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold">Salin Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: IMPOR PAKET LMS JSON -->
<!-- ======================================================== -->
<div id="modal-import-package-json" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span class="p-1.5 rounded-lg bg-blue-500/15 text-blue-600 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                    </span>
                    <span>Impor Paket Berkas (.JSON)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">Impor paket pembelajaran yang telah diekspor dari kelas atau guru lain.</p>
            </div>
            <button onclick="document.getElementById('modal-import-package-json').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.import_package', $courseClass->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Pilih Berkas Paket (.JSON) <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="package_file" required accept=".json,application/json,text/plain" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 file:cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2 bg-slate-50 dark:bg-slate-800">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Status Publikasi:</label>
                    <select name="publish_status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <option value="keep">Pertahankan Berkas</option>
                        <option value="published">Langsung Terbitkan</option>
                        <option value="draft">Sebagai Draf Dulu</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Tenggat Waktu Tugas:</label>
                    <select name="assignment_due_days" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <option value="7">+7 Hari dari Sekarang</option>
                        <option value="14">+14 Hari dari Sekarang</option>
                        <option value="30">+30 Hari dari Sekarang</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-import-package-json').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold">Unggah & Impor Paket</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: EDIT MATERI PELAJARAN -->
<!-- ======================================================== -->
<div id="modal-edit-material" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </span>
                    <span>Edit Materi Pelajaran</span>
                </h3>
                <p class="text-xs text-slate-500">Perbarui informasi, konten, tautan, atau berkas materi</p>
            </div>
            <button onclick="document.getElementById('modal-edit-material').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form id="form-edit-material" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Materi *</label>
                <input type="text" name="title" id="edit_material_title" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Bab *</label>
                    <input type="text" name="topic" id="edit_material_topic" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Konten *</label>
                    <select name="content_type" id="edit_material_content_type" onchange="handleEditMaterialTypeChange(this.value)" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="text">Teks / Artikel Digital</option>
                        <option value="document">Dokumen Berkas (PDF, DOC, PPT)</option>
                        <option value="video">Video Pembelajaran (YouTube / MP4)</option>
                        <option value="link">Tautan Web Edukasi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Ringkas</label>
                <textarea name="description" id="edit_material_description" rows="2" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div id="edit_material_wrapper_url">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tautan URL Eksternal / Video</label>
                <input type="url" name="external_url" id="edit_material_external_url" placeholder="https://..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div id="edit_material_wrapper_file">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas Materi (Opsional)</label>
                <div id="edit_material_current_file_badge" class="mb-2 text-[11px] text-slate-500 bg-slate-100 dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 hidden">
                    <span>📄 Berkas saat ini:</span>
                    <strong id="edit_material_current_file_name" class="text-indigo-600 dark:text-indigo-400"></strong>
                </div>
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.zip" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 file:cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2 bg-slate-50 dark:bg-slate-800">
                <p class="mt-1 text-[11px] text-slate-500">Biarkan kosong jika tidak ingin mengganti berkas materi yang sudah ada.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Mulai Dapat Diakses Siswa)</label>
                <input type="datetime-local" name="start_time" id="edit_material_start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika materi dapat langsung dibuka kapan saja. Sekalipun waktu telah lewat, materi tetap tampil dan dapat diakses siswa.</p>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="edit_material_is_published" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="edit_material_is_published" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Publikasikan materi ke siswa kelas</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-material').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Materi</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: EDIT TUGAS & PR -->
<!-- ======================================================== -->
<div id="modal-edit-assignment" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </span>
                    <span>Edit Tugas & PR</span>
                </h3>
                <p class="text-xs text-slate-500">Perbarui judul, instruksi, tenggat pengumpulan, dan berkas lampiran</p>
            </div>
            <button onclick="document.getElementById('modal-edit-assignment').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form id="form-edit-assignment" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Tugas *</label>
                <input type="text" name="title" id="edit_assignment_title" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Ruang Lingkup Materi *</label>
                <input type="text" name="topic" id="edit_assignment_topic" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instruksi & Soal Tugas *</label>
                <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                    @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_assignment_instructions'])
                    <textarea name="instructions" id="edit_assignment_instructions" rows="4" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none"></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Dapat Dibuka Siswa)</label>
                    <input type="datetime-local" name="start_time" id="edit_assignment_start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <p class="text-[10px] text-slate-400 mt-0.5">Kosongkan jika langsung dapat dibuka.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tenggat Waktu Pengumpulan (Deadline) *</label>
                    <input type="datetime-local" name="due_date" id="edit_assignment_due_date" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <p class="text-[10px] text-slate-400 mt-0.5">Tugas tetap tampil & dapat diakses walau waktu lewat.</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nilai Maksimal (Bobot) *</label>
                <input type="number" name="max_score" id="edit_assignment_max_score" min="10" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Pengumpulan Jawaban</label>
                <select name="submission_type" id="edit_assignment_submission_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="both">Unggah Berkas atau Teks Bebas (Keduanya)</option>
                    <option value="file">Wajib Unggah Berkas (PDF, DOC, Gambar)</option>
                    <option value="text">Hanya Teks Online Langsung</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas Soal / Rubrik Lampiran (Opsional)</label>
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 file:cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2 bg-slate-50 dark:bg-slate-800">
                <p class="mt-1 text-[11px] text-slate-500">Biarkan kosong jika tidak ingin mengubah berkas lampiran soal sebelumnya.</p>
            </div>

            <div class="space-y-2 pt-1">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_assignment_allow_late" name="allow_late" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_assignment_allow_late" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Izinkan pengumpulan terlambat melewati tenggat</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_assignment_is_published" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_assignment_is_published" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Publikasikan tugas ke siswa kelas</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-assignment').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Tugas</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: EDIT ASESMEN / CBT -->
<!-- ======================================================== -->
<div id="modal-edit-quiz" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </span>
                    <span>Edit Asesmen & CBT</span>
                </h3>
                <p class="text-xs text-slate-500">Perbarui pengaturan jadwal, durasi, topik, dan status ujian</p>
            </div>
            <button onclick="document.getElementById('modal-edit-quiz').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form id="form-edit-quiz" method="POST" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Asesmen / Ujian *</label>
                <input type="text" name="title" id="edit_quiz_title" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Topik / Ruang Lingkup Materi Asesmen</label>
                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md">Bisa Lebih dari 1 Topik</span>
                </div>
                <textarea name="topic" id="edit_quiz_topic" rows="2" placeholder="Tuliskan topik / ruang lingkup materi (bisa beberapa, pisahkan baris baru)..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Asesmen *</label>
                    <select name="quiz_type" id="edit_quiz_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="formatif">Asesmen Formatif (Kuis Cek Penguasaan)</option>
                        <option value="sumatif_materi">Asesmen Sumatif (Lingkup Materi / UH)</option>
                        <option value="sts">Sumatif Tengah Semester (STS)</option>
                        <option value="sas">Sumatif Akhir Semester (SAS)</option>
                        <option value="saj">Sumatif Akhir Jenjang (SAJ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Pengerjaan (Menit) *</label>
                    <input type="number" name="duration_minutes" id="edit_quiz_duration" min="5" max="300" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                <input type="number" name="passing_score" id="edit_quiz_passing_score" min="10" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jadwal Mulai Ujian (Opsional)</label>
                    <input type="datetime-local" name="start_time" id="edit_quiz_start_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Selesai Ujian (Opsional)</label>
                    <input type="datetime-local" name="end_time" id="edit_quiz_end_time" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tata Tertib / Petunjuk Asesmen</label>
                <textarea name="instructions" id="edit_quiz_instructions" rows="2" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="space-y-2 pt-1">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_quiz_is_randomized" name="is_randomized" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_quiz_is_randomized" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Acak urutan butir soal saat dikerjakan siswa</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_quiz_is_published" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_quiz_is_published" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Publikasikan asesmen ke siswa kelas</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-quiz').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Asesmen</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMaterialModal(data) {
    const modal = document.getElementById('modal-edit-material');
    if (!modal) return;
    const form = document.getElementById('form-edit-material');
    if (form) form.action = data.update_url;

    document.getElementById('edit_material_title').value = data.title || '';
    document.getElementById('edit_material_topic').value = data.topic || '';
    document.getElementById('edit_material_content_type').value = data.content_type || 'text';
    document.getElementById('edit_material_description').value = data.description || '';
    document.getElementById('edit_material_external_url').value = data.external_url || '';
    document.getElementById('edit_material_start_time').value = data.start_time || '';
    document.getElementById('edit_material_is_published').checked = !!data.is_published;

    const badge = document.getElementById('edit_material_current_file_badge');
    const nameEl = document.getElementById('edit_material_current_file_name');
    if (data.file_name) {
        if (badge) badge.classList.remove('hidden');
        if (nameEl) nameEl.textContent = data.file_name;
    } else {
        if (badge) badge.classList.add('hidden');
    }

    handleEditMaterialTypeChange(data.content_type || 'text');
    modal.classList.remove('hidden');
}

function handleEditMaterialTypeChange(type) {
    const wrapperUrl = document.getElementById('edit_material_wrapper_url');
    const wrapperFile = document.getElementById('edit_material_wrapper_file');
    if (type === 'video' || type === 'link') {
        if (wrapperUrl) wrapperUrl.classList.remove('hidden');
        if (wrapperFile) wrapperFile.classList.add('hidden');
    } else if (type === 'document') {
        if (wrapperUrl) wrapperUrl.classList.add('hidden');
        if (wrapperFile) wrapperFile.classList.remove('hidden');
    } else {
        if (wrapperUrl) wrapperUrl.classList.add('hidden');
        if (wrapperFile) wrapperFile.classList.add('hidden');
    }
}

function openEditAssignmentModal(data) {
    const modal = document.getElementById('modal-edit-assignment');
    if (!modal) return;
    const form = document.getElementById('form-edit-assignment');
    if (form) form.action = data.update_url;

    document.getElementById('edit_assignment_title').value = data.title || '';
    document.getElementById('edit_assignment_topic').value = data.topic || '';
    document.getElementById('edit_assignment_instructions').value = data.instructions || '';
    document.getElementById('edit_assignment_start_time').value = data.start_time || '';
    document.getElementById('edit_assignment_due_date').value = data.due_date || '';
    document.getElementById('edit_assignment_max_score').value = data.max_score || 100;
    document.getElementById('edit_assignment_submission_type').value = data.submission_type || 'both';
    document.getElementById('edit_assignment_allow_late').checked = !!data.allow_late;
    document.getElementById('edit_assignment_is_published').checked = !!data.is_published;

    if (window.RichEditor && typeof window.RichEditor.initVisualCanvas === 'function') {
        window.RichEditor.initVisualCanvas('edit_assignment_instructions');
        const visual = document.getElementById('rich-visual-edit-assignment-instructions');
        const ta = document.getElementById('edit_assignment_instructions');
        if (visual && ta) {
            visual.innerHTML = window.RichEditor.markdownToVisualHtml(ta.value) || '<p><br></p>';
        }
    }

    modal.classList.remove('hidden');
}

function openEditQuizModal(data) {
    const modal = document.getElementById('modal-edit-quiz');
    if (!modal) return;
    const form = document.getElementById('form-edit-quiz');
    if (form) form.action = data.update_url;

    document.getElementById('edit_quiz_title').value = data.title || '';
    document.getElementById('edit_quiz_topic').value = data.topic || '';
    document.getElementById('edit_quiz_type').value = data.quiz_type || 'formatif';
    document.getElementById('edit_quiz_duration').value = data.duration_minutes || 60;
    document.getElementById('edit_quiz_passing_score').value = data.passing_score || 75;
    document.getElementById('edit_quiz_start_time').value = data.start_time || '';
    document.getElementById('edit_quiz_end_time').value = data.end_time || '';
    document.getElementById('edit_quiz_instructions').value = data.instructions || '';
    document.getElementById('edit_quiz_is_randomized').checked = !!data.is_randomized;
    document.getElementById('edit_quiz_is_published').checked = !!data.is_published;

    modal.classList.remove('hidden');
}

function openModalTab(modalId, mode) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    switchModalMode(modalId, mode);
}

function switchModalMode(modalId, mode) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const aiSection = modal.querySelector('.modal-mode-ai');
    const manualSection = modal.querySelector('.modal-mode-manual');
    const btnAi = modal.querySelector('.btn-tab-ai');
    const btnManual = modal.querySelector('.btn-tab-manual');

    if (mode === 'ai') {
        if (aiSection) aiSection.classList.remove('hidden');
        if (manualSection) manualSection.classList.add('hidden');
        if (btnAi) {
            btnAi.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            btnAi.classList.remove('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
        }
        if (btnManual) {
            btnManual.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btnManual.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
        }
    } else {
        if (aiSection) aiSection.classList.add('hidden');
        if (manualSection) manualSection.classList.remove('hidden');
        if (btnManual) {
            btnManual.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            btnManual.classList.remove('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
        }
        if (btnAi) {
            btnAi.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btnAi.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
        }
    }
}

function handleAiContentTypeChange(val) {
    const urlLabel = document.getElementById('ai_url_label');
    const urlInput = document.getElementById('ai_video_url');
    const urlHelp = document.getElementById('ai_url_help');
    if (!urlLabel || !urlInput) return;

    if (val === 'video') {
        urlLabel.textContent = 'URL Video Pembelajaran (YouTube / Google Drive / MP4) *';
        urlInput.placeholder = 'https://www.youtube.com/watch?v=...';
        if (urlHelp) urlHelp.textContent = 'Video akan otomatis di-embed dan disematkan di dalam modul interaktif siswa.';
    } else if (val === 'link') {
        urlLabel.textContent = 'URL Tautan Luar / Website Edukasi *';
        urlInput.placeholder = 'https://sumberbelajar.belajar.kemdikbud.go.id/...';
        if (urlHelp) urlHelp.textContent = 'Tautan edukasi luar akan disajikan bersama panduan telaah kritis interaktif.';
    } else if (val === 'document') {
        urlLabel.textContent = 'URL Sumber Modul / E-Book Daring (Opsional)';
        urlInput.placeholder = 'https://drive.google.com/... atau tautan modul daring';
        if (urlHelp) urlHelp.textContent = 'Anda dapat mengisi URL dokumen daring atau mengunggah berkas PDF/PPT langsung di bawah.';
    } else {
        urlLabel.textContent = 'URL Video / Tautan Pelengkap (Opsional)';
        urlInput.placeholder = 'https://...';
        if (urlHelp) urlHelp.textContent = 'Jika diisi, tautan atau video ini akan disematkan di dalam modul interaktif siswa.';
    }
}

function applyCbtPresetCount(val) {
    const targetInput = document.getElementById('cbt_target_total');
    if (!targetInput) return;
    if (val !== 'custom') {
        targetInput.value = val;
        distributeEvenlyCbt();
    } else {
        targetInput.focus();
    }
}

function onCustomCbtTargetChange(val) {
    const presetSelect = document.getElementById('cbt_preset_count');
    if (presetSelect) {
        const matchingOpt = Array.from(presetSelect.options).find(o => o.value === String(val));
        presetSelect.value = matchingOpt ? String(val) : 'custom';
    }
    distributeEvenlyCbt();
}

function toggleCbtType(key) {
    const chk = document.getElementById('chk_' + key);
    const input = document.getElementById('count_' + key);
    if (!chk || !input) return;

    if (chk.checked) {
        input.disabled = false;
        if (parseInt(input.value || 0) <= 0) {
            input.value = 1;
        }
    } else {
        input.disabled = true;
        input.value = 0;
    }
    recalcCbtTotal();
}

function recalcCbtTotal() {
    const keys = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];
    let sum = 0;
    keys.forEach(k => {
        const inp = document.getElementById('count_' + k);
        const chk = document.getElementById('chk_' + k);
        if (inp && chk && chk.checked) {
            const v = Math.max(0, parseInt(inp.value || 0));
            sum += v;
        }
    });

    const badge = document.getElementById('cbt_live_total');
    const warn = document.getElementById('cbt_warning_limit');
    const submitBtn = document.getElementById('btn-submit-cbt-ai');
    const targetInput = document.getElementById('cbt_target_total');

    if (badge) {
        badge.textContent = sum + ' / 50 Butir';
        if (sum > 50) {
            badge.className = 'px-2.5 py-0.5 rounded-full font-black text-xs bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
            if (warn) warn.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = true;
        } else if (sum === 0) {
            badge.className = 'px-2.5 py-0.5 rounded-full font-black text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
            if (warn) warn.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = true;
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full font-black text-xs bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
            if (warn) warn.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    if (targetInput && sum > 0 && sum <= 50) {
        targetInput.value = sum;
    }
}

function distributeEvenlyCbt() {
    const targetInput = document.getElementById('cbt_target_total');
    const target = Math.min(50, Math.max(1, parseInt(targetInput?.value || 5)));
    const keys = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];
    const activeKeys = keys.filter(k => {
        const chk = document.getElementById('chk_' + k);
        return chk && chk.checked;
    });

    if (activeKeys.length === 0) {
        const chkMc = document.getElementById('chk_mc');
        if (chkMc) { chkMc.checked = true; toggleCbtType('mc'); activeKeys.push('mc'); }
    }

    const each = Math.floor(target / activeKeys.length);
    const rem = target % activeKeys.length;

    keys.forEach(k => {
        const inp = document.getElementById('count_' + k);
        const chk = document.getElementById('chk_' + k);
        if (inp && chk) {
            if (chk.checked) {
                const idx = activeKeys.indexOf(k);
                inp.disabled = false;
                inp.value = each + (idx < rem ? 1 : 0);
            } else {
                inp.disabled = true;
                inp.value = 0;
            }
        }
    });

    recalcCbtTotal();
}

function resetToSingleChoiceCbt() {
    const targetInput = document.getElementById('cbt_target_total');
    const target = Math.min(50, Math.max(1, parseInt(targetInput?.value || 5)));
    const keys = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];

    keys.forEach(k => {
        const chk = document.getElementById('chk_' + k);
        const inp = document.getElementById('count_' + k);
        if (chk && inp) {
            if (k === 'mc') {
                chk.checked = true;
                inp.disabled = false;
                inp.value = target;
            } else {
                chk.checked = false;
                inp.disabled = true;
                inp.value = 0;
            }
        }
    });

    recalcCbtTotal();
}

function toggleAssignmentFileTypeSettings(prefix, type) {
    const wrapper = document.getElementById(prefix + '_file_types_wrapper');
    if (!wrapper) return;
    if (type === 'text') {
        wrapper.classList.add('hidden');
    } else {
        wrapper.classList.remove('hidden');
    }
}

// Dropdown Paket Kelas
function togglePackageDropdown() {
    const menu = document.getElementById('dropdown-lms-package-menu');
    if (menu) menu.classList.toggle('hidden');
}

function closePackageDropdown() {
    const menu = document.getElementById('dropdown-lms-package-menu');
    if (menu) menu.classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('dropdown-lms-package-wrapper');
    const menu = document.getElementById('dropdown-lms-package-menu');
    if (wrapper && menu && !wrapper.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// Modal Import Dari Kelas Lain
let currentSourceData = null;

function openModalImportFromClass() {
    const modal = document.getElementById('modal-import-from-class');
    if (!modal) return;
    modal.classList.remove('hidden');
    const select = document.getElementById('source_class_select');
    if (select && select.value) {
        loadSourceClassContents(select.value);
    }
}

function switchSourceTab(tabName) {
    const panes = ['materials', 'assignments', 'quizzes'];
    panes.forEach(p => {
        const pane = document.getElementById('source-pane-' + p);
        const btn = document.getElementById('btn-src-tab-' + p);
        if (pane && btn) {
            if (p === tabName) {
                pane.classList.remove('hidden');
                btn.className = 'flex-1 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-900 text-teal-600 dark:text-teal-400 shadow-xs';
            } else {
                pane.classList.add('hidden');
                btn.className = 'flex-1 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400';
            }
        }
    });
}

function loadSourceClassContents(sourceClassId) {
    const loading = document.getElementById('source_class_loading');
    const container = document.getElementById('source_class_contents_container');
    const emptyHint = document.getElementById('source_class_empty_hint');
    const submitBtn = document.getElementById('btn-submit-import-from-class');

    if (!sourceClassId) {
        if (container) container.classList.add('hidden');
        if (loading) loading.classList.add('hidden');
        if (emptyHint) emptyHint.classList.remove('hidden');
        if (submitBtn) submitBtn.disabled = true;
        return;
    }

    if (emptyHint) emptyHint.classList.add('hidden');
    if (loading) loading.classList.remove('hidden');
    if (container) container.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    const url = '{{ route("lms.source_contents", ["courseClass" => $courseClass->id, "sourceClass" => "__SRC__"]) }}'.replace('__SRC__', sourceClassId);

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Gagal memuat konten kelas sumber.');
        return res.json();
    })
    .then(data => {
        currentSourceData = data;
        if (loading) loading.classList.add('hidden');
        if (container) container.classList.remove('hidden');

        // Render Materials
        const matList = document.getElementById('source-materials-list');
        const badgeMat = document.getElementById('badge-count-materials');
        if (badgeMat) badgeMat.textContent = data.materials.length;
        if (matList) {
            if (data.materials.length === 0) {
                matList.innerHTML = '<p class="text-xs text-slate-400 py-3 text-center italic">Tidak ada materi pelajaran di kelas sumber ini.</p>';
            } else {
                matList.innerHTML = data.materials.map(m => `
                    <label class="flex items-start gap-2.5 p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs transition">
                        <input type="checkbox" name="material_ids[]" value="${m.id}" onchange="checkSelectedSourceContents()" class="src-content-chk mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1">${escapeHtml(m.title)}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-500">
                                <span class="px-1.5 py-0.2 rounded font-semibold bg-slate-200 dark:bg-slate-700 uppercase">${m.type}</span>
                                <span>Topik: ${escapeHtml(m.topic)}</span>
                                ${m.has_file ? '<span class="text-indigo-500">📎 Berkas</span>' : ''}
                            </div>
                        </div>
                    </label>
                `).join('');
            }
        }

        // Render Assignments
        const asgList = document.getElementById('source-assignments-list');
        const badgeAsg = document.getElementById('badge-count-assignments');
        if (badgeAsg) badgeAsg.textContent = data.assignments.length;
        if (asgList) {
            if (data.assignments.length === 0) {
                asgList.innerHTML = '<p class="text-xs text-slate-400 py-3 text-center italic">Tidak ada tugas atau PR di kelas sumber ini.</p>';
            } else {
                asgList.innerHTML = data.assignments.map(a => `
                    <label class="flex items-start gap-2.5 p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs transition">
                        <input type="checkbox" name="assignment_ids[]" value="${a.id}" onchange="checkSelectedSourceContents()" class="src-content-chk mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1">${escapeHtml(a.title)}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-500">
                                <span>Topik: ${escapeHtml(a.topic)}</span>
                                <span>Tipe: ${escapeHtml(a.submission_type_label)}</span>
                                <span class="text-amber-600 dark:text-amber-400">📅 Tenggat: ${a.due_date_formatted}</span>
                            </div>
                        </div>
                    </label>
                `).join('');
            }
        }

        // Render Quizzes
        const qzList = document.getElementById('source-quizzes-list');
        const badgeQz = document.getElementById('badge-count-quizzes');
        if (badgeQz) badgeQz.textContent = data.quizzes.length;
        if (qzList) {
            if (data.quizzes.length === 0) {
                qzList.innerHTML = '<p class="text-xs text-slate-400 py-3 text-center italic">Tidak ada asesmen atau CBT di kelas sumber ini.</p>';
            } else {
                qzList.innerHTML = data.quizzes.map(q => `
                    <label class="flex items-start gap-2.5 p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer text-xs transition">
                        <input type="checkbox" name="quiz_ids[]" value="${q.id}" onchange="checkSelectedSourceContents()" class="src-content-chk mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1">${escapeHtml(q.title)}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-500">
                                <span class="px-1.5 py-0.2 rounded font-black bg-purple-500/15 text-purple-700 dark:text-purple-300 uppercase">${escapeHtml(q.quiz_type)}</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">🎯 ${q.questions_count} Butir Soal Lengkap</span>
                                <span>⏱ ${q.duration_minutes} Menit</span>
                            </div>
                        </div>
                    </label>
                `).join('');
            }
        }

        checkSelectedSourceContents();
    })
    .catch(err => {
        if (loading) loading.classList.add('hidden');
        alert(err.message || 'Terjadi kesalahan saat memuat konten kelas sumber.');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function toggleSelectAllInContainer(containerId, btn) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const checkboxes = container.querySelectorAll('input[type="checkbox"]');
    if (checkboxes.length === 0) return;

    const allChecked = Array.from(checkboxes).every(c => c.checked);
    checkboxes.forEach(c => c.checked = !allChecked);

    if (btn) {
        btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
    }
    checkSelectedSourceContents();
}

function checkSelectedSourceContents() {
    const checkboxes = document.querySelectorAll('.src-content-chk:checked');
    const submitBtn = document.getElementById('btn-submit-import-from-class');
    if (submitBtn) {
        submitBtn.disabled = (checkboxes.length === 0);
        if (checkboxes.length > 0) {
            submitBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Salin ${checkboxes.length} Konten Terpilih ke Kelas Ini</span>
            `;
        } else {
            submitBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Salin Konten Terpilih ke Kelas Ini</span>
            `;
        }
    }
}

// Modal Salin Satu Item ke Beberapa Kelas
function openCopyModal(itemType, itemId, itemTitle) {
    const modal = document.getElementById('modal-copy-item-to-classes');
    if (!modal) return;

    document.getElementById('copy_item_type').value = itemType;
    document.getElementById('copy_item_id').value = itemId;
    document.getElementById('copy_item_title').textContent = itemTitle;

    const labelMap = {
        'material': 'Materi Pelajaran',
        'assignment': 'Tugas & PR',
        'quiz': 'Asesmen & CBT'
    };
    document.getElementById('copy_item_type_label').textContent = labelMap[itemType] || 'Konten';

    const dueWrapper = document.getElementById('copy_due_mode_wrapper');
    if (dueWrapper) {
        if (itemType === 'assignment') {
            dueWrapper.classList.remove('hidden');
        } else {
            dueWrapper.classList.add('hidden');
        }
    }

    // Reset checkboxes
    document.querySelectorAll('.target-class-checkbox').forEach(c => c.checked = false);

    modal.classList.remove('hidden');
}

function selectAllSameSubjectTargets() {
    const checkboxes = document.querySelectorAll('.target-class-checkbox');
    checkboxes.forEach(c => {
        if (c.getAttribute('data-same') === '1') {
            c.checked = true;
        }
    });
}

function openAddAttendanceModal(defaultMode = 'auto') {
    const modal = document.getElementById('modal-add-attendance');
    if (!modal) return;
    modal.classList.remove('hidden');
    toggleAttendanceScheduleMode(defaultMode);
}

function toggleAttendanceScheduleMode(mode) {
    const autoRadio = document.querySelector('input[name="schedule_type"][value="auto"]');
    const manualRadio = document.querySelector('input[name="schedule_type"][value="manual"]');
    const autoBox = document.getElementById('attendance-auto-info-box');
    const manualContainer = document.getElementById('attendance-manual-container');
    const labelAuto = document.getElementById('label-schedule-auto');
    const labelManual = document.getElementById('label-schedule-manual');

    if (mode === 'auto') {
        if (autoRadio) autoRadio.checked = true;
        if (autoBox) autoBox.classList.remove('hidden');
        if (manualContainer) manualContainer.classList.add('hidden');
        if (labelAuto) {
            labelAuto.classList.add('border-emerald-500', 'bg-emerald-50/50', 'dark:bg-emerald-950/30');
            labelAuto.classList.remove('border-slate-200', 'dark:border-slate-700');
        }
        if (labelManual) {
            labelManual.classList.remove('border-indigo-500', 'bg-indigo-50/50', 'dark:bg-indigo-950/30');
            labelManual.classList.add('border-slate-200', 'dark:border-slate-700');
        }
        if (manualContainer) {
            const inputs = manualContainer.querySelectorAll('select, input');
            inputs.forEach(i => i.disabled = true);
        }
    } else {
        if (manualRadio) manualRadio.checked = true;
        if (autoBox) autoBox.classList.add('hidden');
        if (manualContainer) manualContainer.classList.remove('hidden');
        if (labelManual) {
            labelManual.classList.add('border-indigo-500', 'bg-indigo-50/50', 'dark:bg-indigo-950/30');
            labelManual.classList.remove('border-slate-200', 'dark:border-slate-700');
        }
        if (labelAuto) {
            labelAuto.classList.remove('border-emerald-500', 'bg-emerald-50/50', 'dark:bg-emerald-950/30');
            labelAuto.classList.add('border-slate-200', 'dark:border-slate-700');
        }
        if (manualContainer) {
            const inputs = manualContainer.querySelectorAll('select, input');
            inputs.forEach(i => i.disabled = false);
        }
    }
}

function addAttendanceScheduleRow() {
    const container = document.getElementById('attendance-schedule-rows-container');
    const newRow = document.createElement('div');
    newRow.className = 'attendance-schedule-row grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700';
    newRow.innerHTML = `
        <div class="col-span-5">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Hari</label>
            <select name="attendance_days[]" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-medium">
                <option value="Senin">Senin</option>
                <option value="Selasa" selected>Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Mulai</label>
            <input type="time" name="attendance_starts[]" value="09:45" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Selesai</label>
            <input type="time" name="attendance_ends[]" value="10:20" class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
        </div>
        <div class="col-span-1 pt-3.5 text-center">
            <button type="button" onclick="removeAttendanceScheduleRow(this)" class="text-slate-400 hover:text-rose-500 text-xs font-bold transition" title="Hapus hari">✕</button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeAttendanceScheduleRow(btn) {
    const rows = document.querySelectorAll('.attendance-schedule-row');
    if (rows.length > 1) {
        btn.closest('.attendance-schedule-row').remove();
    } else {
        alert('Minimal harus ada 1 jadwal hari presensi.');
    }
}

// ==========================================
// CBT MANUAL MULTI-TYPE JAVASCRIPT HELPERS
// ==========================================
function manualToggleCbtType(type) {
    const chk = document.getElementById('manual_chk_' + type);
    const input = document.getElementById('manual_count_' + type);
    if (chk && input) {
        input.disabled = !chk.checked;
        if (!chk.checked) {
            input.value = 0;
        } else if (parseInt(input.value) === 0) {
            input.value = 2;
        }
    }
    manualRecalcCbtTotal();
}

function manualRecalcCbtTotal() {
    const types = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];
    let total = 0;
    types.forEach(t => {
        const inp = document.getElementById('manual_count_' + t);
        if (inp && !inp.disabled) {
            total += parseInt(inp.value) || 0;
        }
    });
    const indicator = document.getElementById('manual_cbt_total_indicator');
    if (indicator) {
        indicator.innerText = total + ' Butir Soal';
    }
}

function manualSelectAllCbtTypes(defaultCount = 5) {
    const types = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];
    types.forEach(t => {
        const chk = document.getElementById('manual_chk_' + t);
        const inp = document.getElementById('manual_count_' + t);
        if (chk && inp) {
            chk.checked = true;
            inp.disabled = false;
            if (parseInt(inp.value) <= 0) {
                inp.value = defaultCount;
            }
        }
    });
    manualRecalcCbtTotal();
}

function manualDistributeEvenlyCbt() {
    const types = ['mc', 'cmc', 'tf', 'match', 'sa', 'essay'];
    types.forEach(t => {
        const chk = document.getElementById('manual_chk_' + t);
        const inp = document.getElementById('manual_count_' + t);
        if (chk && inp) {
            chk.checked = true;
            inp.disabled = false;
            inp.value = 2;
        }
    });
    manualRecalcCbtTotal();
}

function manualResetToSingleChoiceCbt() {
    const types = ['cmc', 'tf', 'match', 'sa', 'essay'];
    types.forEach(t => {
        const chk = document.getElementById('manual_chk_' + t);
        const inp = document.getElementById('manual_count_' + t);
        if (chk && inp) {
            chk.checked = false;
            inp.disabled = true;
            inp.value = 0;
        }
    });
    const chkMc = document.getElementById('manual_chk_mc');
    const inpMc = document.getElementById('manual_count_mc');
    if (chkMc && inpMc) {
        chkMc.checked = true;
        inpMc.disabled = false;
        inpMc.value = 5;
    }
    manualRecalcCbtTotal();
}

function openEditAttendanceModal(att) {
    const modal = document.getElementById('modal-edit-attendance');
    const form = document.getElementById('form-edit-attendance');
    if (!modal || !form) return;

    form.action = att.update_url;
    document.getElementById('edit_attendance_title').value = att.title || '';
    document.getElementById('edit_attendance_date').value = att.meeting_date || '';
    document.getElementById('edit_attendance_start').value = att.start_time || '07:30';
    document.getElementById('edit_attendance_end').value = att.end_time || '09:00';
    document.getElementById('edit_attendance_method').value = att.attendance_method || 'both';
    document.getElementById('edit_attendance_notes').value = att.notes || '';
    document.getElementById('edit_attendance_is_open').value = att.is_open ? '1' : '0';

    modal.classList.remove('hidden');
}

function closeEditAttendanceModal() {
    const modal = document.getElementById('modal-edit-attendance');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleAttendanceScheduleMode('auto');
});
</script>

<!-- ========================================== -->
<!-- MODAL: EDIT KKTP GURU MAPEL               -->
<!-- ========================================== -->
<div id="modal-edit-kktp" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Target KKTP Mapel</h3>
                <p class="text-xs text-slate-500">{{ $courseClass->subject->name }} — {{ $courseClass->schoolClass->name }}</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-kktp').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.kktp.update', $courseClass->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                <div class="relative">
                    <input type="number" name="kktp" value="{{ $courseClass->getEffectiveKktp() }}" min="50" max="100" required class="w-full text-lg font-black bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white">
                    <span class="absolute right-3 top-3 text-xs font-bold text-slate-400">Skala 100</span>
                </div>
                <p class="mt-1.5 text-[11px] text-slate-500 leading-relaxed">
                    KKTP ini akan digunakan secara langsung untuk menentukan status <strong>Tuntas / Remedial</strong> pada Buku Nilai, Tugas, dan Kuis/CBT kelas ini.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-kktp').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">Simpan Target KKTP</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT SESI PRESENSI                 -->
<!-- ========================================== -->
<div id="modal-edit-attendance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Sesi Presensi Pertemuan</h3>
                <p class="text-xs text-slate-500">Sesuaikan judul, waktu, dan metode presensi pertemuan</p>
            </div>
            <button type="button" onclick="closeEditAttendanceModal()" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form id="form-edit-attendance" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Pertemuan Ke- *</label>
                <input type="text" name="title" id="edit_attendance_title" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Sesi</label>
                    <input type="date" name="meeting_date" id="edit_attendance_date" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" id="edit_attendance_start" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" id="edit_attendance_end" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Verifikasi Kehadiran</label>
                    <select name="attendance_method" id="edit_attendance_method" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="both">Selfie Siswa & Manual Guru/Sekretaris</option>
                        <option value="manual">Hanya Manual Guru & Sekretaris</option>
                        <option value="selfie">Hanya Selfie Kamera Siswa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sesi Presensi</label>
                    <select name="is_open" id="edit_attendance_is_open" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="1">Terbuka (Siswa dapat absen)</option>
                        <option value="0">Ditutup</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Pertemuan / Agenda Belajar</label>
                <textarea name="notes" id="edit_attendance_notes" rows="2" placeholder="Catatan pertemuan tatap muka atau topik pembelajaran..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeEditAttendanceModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition">Simpan Perubahan Sesi</button>
            </div>
        </form>
    </div>
</div>
@endif

@include('lms.partials.rich_editor_modals')

@endsection
