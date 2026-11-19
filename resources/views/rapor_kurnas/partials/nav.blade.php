@php
    $user = Auth::user();
    $canManageKurnas = $user && ($user->isSuperAdmin() || $user->isAdmin() || $user->isWakasekKurikulum() || $user->isKepalaSekolah());
    $myHomeroomClass = $user?->teacher?->homeroomClass;
    $isHomeroom = $user && ($user->isHomeroom() || (bool) $myHomeroomClass);
    $isGrade12Homeroom = $myHomeroomClass && $myHomeroomClass->grade === 'XII';
    $isCounselor = $user && $user->isCounselor();
    $isStudentOrParent = $user && ($user->isStudent() || $user->isParent());
    $activeSetting = \App\Models\KurnasSetting::where('is_active', true)->first();
    $isLocked = $activeSetting ? $activeSetting->is_locked : false;
@endphp

<!-- Baris Identitas Role & Status Pengguna Rapor -->
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl px-4 py-3 shadow-xs mb-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2.5 flex-wrap">
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Kapasitas Akses:</span>
        @if($canManageKurnas)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                <span>👑</span>
                <span>Administrator Kurikulum & Pimpinan</span>
            </span>
        @elseif($isHomeroom)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                <span>🌟</span>
                <span>Wali Kelas {{ $myHomeroomClass ? $myHomeroomClass->name : '' }}</span>
            </span>
        @elseif($isCounselor)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                <span>🎯</span>
                <span>Guru Bimbingan Konseling (BK)</span>
            </span>
        @elseif($user && $user->isTeacher())
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                <span>👨‍🏫</span>
                <span>Guru Mata Pelajaran</span>
            </span>
        @elseif($isStudentOrParent)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                <span>👨‍🎓</span>
                <span>{{ $user->isStudent() ? 'Siswa' : 'Orang Tua Siswa' }}</span>
            </span>
        @endif
    </div>

    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
        <span class="hidden sm:inline">TA: <strong class="text-slate-800 dark:text-white">{{ $activeSetting?->academicYear?->name ?? '2025/2026' }}</strong> (Sem {{ $activeSetting?->semester == '2' ? 'Genap' : 'Ganjil' }})</span>
        <span class="h-3.5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:inline"></span>
        @if($isLocked)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                🔒 Input Terkunci
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                🔓 Input Terbuka
            </span>
        @endif
    </div>
</div>

<!-- Navigasi Menu Horizontal Sesuai Kapasitas Role -->
<nav aria-label="Navigasi Menu Rapor Kurikulum Nasional" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1.5 sm:p-2 shadow-xs mb-6 overflow-x-auto scrollbar-none touch-pan-x select-none">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        @if($isStudentOrParent)
            <!-- Siswa & Orang Tua HANYA melihat Rapor Saya -->
            <a href="{{ route('rapor_kurnas.my_report') }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                <span>Rapor Saya (Hasil Capaian Belajar)</span>
            </a>
        @else
            <!-- 1. Dashboard -->
            <a href="{{ route('rapor_kurnas.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.index') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Status Penilaian (Live Monitoring) -->
            <a href="{{ route('rapor_kurnas.monitoring') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.monitoring*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.monitoring*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Status Penilaian</span>
                <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">Live</span>
            </a>

            <!-- 3. Tujuan Pembelajaran (TP) - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.learning_goals') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.learning_goals*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.learning_goals*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Tujuan Pembelajaran (TP)</span>
                </a>
            @endif

            <!-- 4. Input Nilai Rapor - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.scores') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.scores*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.scores*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Input Nilai Rapor</span>
                    @if($isLocked)
                        <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30">Kunci</span>
                    @else
                        <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">Buka</span>
                    @endif
                </a>
            @endif

            <!-- 5. Mapel Gabungan IPA & IPS - Khusus Admin Kurikulum -->
            @if($canManageKurnas)
                <a href="{{ route('rapor_kurnas.combined_subjects') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.combined_subjects*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.combined_subjects*') ? 'text-white' : 'text-cyan-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <span>Mapel Gabungan</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-cyan-500/20 text-cyan-700 dark:text-cyan-400 border border-cyan-500/30">IPA / IPS</span>
                </a>
            @endif

            <!-- 6. Nilai Ekstrakurikuler - Untuk Guru, Pembina & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.extracurriculars') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.extracurriculars*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.extracurriculars*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>Ekstrakurikuler</span>
                </a>
            @endif

            <!-- 7. Kokurikuler / P5 - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.cocurricular') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.cocurricular*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.cocurricular*') ? 'text-white' : 'text-yellow-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span>Kokurikuler (P5)</span>
                </a>
            @endif

            <!-- 8. Wali Kelas - Hanya untuk Wali Kelas & Tim Kurikulum -->
            @if($isHomeroom || $canManageKurnas)
                <a href="{{ route('rapor_kurnas.homeroom') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.homeroom*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.homeroom*') ? 'text-white' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Wali Kelas</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30">Absensi & Catatan</span>
                </a>
            @endif

            <!-- 9. Perkembangan Siswa & SNBP Tracker - Untuk BK, Walas, Kurikulum -->
            @if($isCounselor || $isHomeroom || $canManageKurnas)
                <a href="{{ route('rapor_kurnas.growth') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.growth*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.growth*') ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Perkembangan SNBP</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-purple-500/20 text-purple-700 dark:text-purple-400 border border-purple-500/30">Sem 1-6</span>
                </a>
            @endif

            <!-- 10. Cetak Rapor - Untuk Semua Pengajar & Admin -->
            <a href="{{ route('rapor_kurnas.print_hub') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.print_*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.print_*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rapor</span>
                <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/30">e-Rapor 2025</span>
            </a>

            <!-- 11. Ijazah & Transkrip Kelulusan - Admin Kurikulum & Walas Kelas XII -->
            @if($canManageKurnas || $isGrade12Homeroom)
                <a href="{{ route('rapor_kurnas.diploma') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.diploma*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.diploma*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    <span>Ijazah & Transkrip</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-teal-500/20 text-teal-700 dark:text-teal-400 border border-teal-500/30">Permen 13/2025</span>
                </a>
            @endif

            <!-- 12. Buku Induk & Leger - Untuk Admin, BK, dan Walas -->
            @if($canManageKurnas || $isCounselor || $isHomeroom)
                <a href="{{ route('rapor_kurnas.transcripts') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.transcripts*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.transcripts*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Buku Induk & Leger</span>
                </a>
            @endif

            <!-- 13. Portal Siswa Preview -->
            <a href="{{ route('rapor_kurnas.my_report') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.my_report*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.my_report*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Portal Siswa</span>
            </a>

            <!-- 14. Pengaturan & Referensi - Eksklusif Admin Kurikulum -->
            @if($canManageKurnas)
                <a href="{{ route('rapor_kurnas.settings') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.settings*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('rapor_kurnas.settings*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan & Referensi</span>
                </a>
            @endif
        @endif
    </div>
</nav>
