@php
    $canManageSubjects = Auth::check() && Auth::user()->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum']);
@endphp

<nav aria-label="Navigasi Menu LMS & Kurikulum Merdeka" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- Ruang Kelas LMS -->
        <a href="{{ route('lms.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.index') || request()->routeIs('lms.course*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🎓</span>
            <span>Ruang Kelas & Mapel Saya</span>
        </a>

        @if(Auth::check() && (Auth::user()->isTeacher() || Auth::user()->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])))
        <!-- Perencanaan Pembelajaran (CP, TP, ATP, Modul Ajar) -->
        <a href="{{ route('lms.planning.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.planning.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📝</span>
            <span>Perencanaan & Perangkat Ajar</span>
        </a>

        <!-- Jurnal Mengajar Harian Guru -->
        <a href="{{ route('lms.journals.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.journals.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📖</span>
            <span>Jurnal Mengajar Guru</span>
        </a>

        <!-- Bank Soal & Kisi-Kisi Asesmen -->
        <a href="{{ route('lms.assessment.bank') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.assessment.bank*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🎯</span>
            <span>Bank Soal & Kisi-Kisi</span>
        </a>

        <!-- Remedial & Pengayaan -->
        <a href="{{ route('lms.assessment.remediation') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.assessment.remediation*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🌱</span>
            <span>Remedial & Pengayaan</span>
        </a>

        <!-- Refleksi Pembelajaran Guru -->
        <a href="{{ route('lms.reflections.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.reflections.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>💡</span>
            <span>Refleksi Mengajar</span>
        </a>

        <!-- Supervisi Akademik & Kurikulum -->
        <a href="{{ route('lms.supervision.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.supervision.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🛡️</span>
            <span>Supervisi Akademik</span>
        </a>

        <!-- Pusat Layanan AI Kurikulum & Pedagogi -->
        <a href="{{ route('lms.ai.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.ai.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🤖</span>
            <span>Pusat Layanan AI</span>
        </a>

        <!-- Pusat Cetak & Arsip Dokumen Kurikulum -->
        <a href="{{ route('lms.documents.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.documents.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📂</span>
            <span>Arsip & Cetak Dokumen</span>
        </a>
        @endif

        <!-- Portofolio Digital Siswa (Siswa, Guru, Pimpinan) -->
        <a href="{{ route('lms.portfolios.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.portfolios.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🎨</span>
            <span>Portofolio Siswa</span>
        </a>

        @if($canManageSubjects)
        <!-- Kelola Mapel & Guru -->
        <a href="{{ route('lms.master.subjects') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.master.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📚</span>
            <span>Kelola Mapel & Guru Pengampu</span>
        </a>

        <!-- Kesiapan & Health LMS -->
        <a href="{{ route('lms.health') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.health') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🩺</span>
            <span>Kesiapan Sistem & Health</span>
        </a>
        @endif
    </div>
</nav>

