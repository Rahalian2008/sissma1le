@php
    $canManageSubjects = Auth::check() && Auth::user()->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum']);
@endphp

<nav aria-label="Navigasi Menu LMS & Kurikulum Merdeka" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1.5 sm:p-2 shadow-xs mb-6 overflow-x-auto scrollbar-none touch-pan-x select-none">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- Ruang Kelas LMS -->
        <a href="{{ route('lms.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.index') || request()->routeIs('lms.course*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.index') || request()->routeIs('lms.course*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
            <span>Ruang Kelas & Mapel Saya</span>
        </a>

        @if(Auth::check() && (Auth::user()->isTeacher() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])))
        <!-- Perencanaan Pembelajaran (CP, TP, ATP, Modul Ajar) -->
        <a href="{{ route('lms.planning.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.planning.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.planning.*') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Perencanaan & Perangkat Ajar</span>
        </a>

        <!-- Jurnal Mengajar Harian Guru -->
        <a href="{{ route('lms.journals.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.journals.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.journals.*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Jurnal Mengajar Guru</span>
        </a>

        <!-- Bank Soal & Kisi-Kisi Asesmen -->
        <a href="{{ route('lms.assessment.bank') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.assessment.bank*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.assessment.bank*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Bank Soal & Kisi-Kisi</span>
        </a>

        <!-- Remedial & Pengayaan -->
        <a href="{{ route('lms.assessment.remediation') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.assessment.remediation*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.assessment.remediation*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Remedial & Pengayaan</span>
        </a>

        <!-- Refleksi Pembelajaran Guru -->
        <a href="{{ route('lms.reflections.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.reflections.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.reflections.*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span>Refleksi Mengajar</span>
        </a>

        <!-- Supervisi Akademik & Kurikulum -->
        <a href="{{ route('lms.supervision.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.supervision.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.supervision.*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Supervisi Akademik</span>
        </a>

        <!-- Pusat Layanan AI Kurikulum & Pedagogi -->
        <a href="{{ route('lms.ai.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.ai.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.ai.*') ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Pusat Layanan AI</span>
        </a>

        <!-- Pusat Cetak & Arsip Dokumen Kurikulum -->
        <a href="{{ route('lms.documents.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.documents.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.documents.*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
            <span>Arsip & Cetak Dokumen</span>
        </a>
        @endif

        <!-- Portofolio Digital Siswa (Siswa, Guru, Pimpinan) -->
        <a href="{{ route('lms.portfolios.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.portfolios.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.portfolios.*') ? 'text-white' : 'text-pink-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>Portofolio Siswa</span>
        </a>

        @if($canManageSubjects)
        <!-- Kelola Mapel & Guru -->
        <a href="{{ route('lms.master.subjects') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.master.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.master.*') ? 'text-white' : 'text-cyan-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Kelola Mapel & Guru</span>
        </a>

        <!-- Kesiapan & Health LMS -->
        <a href="{{ route('lms.health') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.health') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('lms.health') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <span>Kesiapan Sistem & Health</span>
        </a>
        @endif
    </div>
</nav>

