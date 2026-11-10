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
<nav aria-label="Navigasi Menu Rapor Kurikulum Nasional" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        @if($isStudentOrParent)
            <!-- Siswa & Orang Tua HANYA melihat Rapor Saya -->
            <a href="{{ route('rapor_kurnas.my_report') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs">
                <span>👨‍🎓</span>
                <span>Rapor Saya (Hasil Capaian Belajar)</span>
            </a>
        @else
            <!-- 1. Dashboard -->
            <a href="{{ route('rapor_kurnas.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>

            <!-- 2. Status Penilaian (Live Monitoring) -->
            <a href="{{ route('rapor_kurnas.monitoring') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.monitoring*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📈</span>
                <span>Status Penilaian</span>
                <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">Live</span>
            </a>

            <!-- 3. Tujuan Pembelajaran (TP) - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.learning_goals') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.learning_goals*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>🎯</span>
                    <span>Tujuan Pembelajaran (TP)</span>
                </a>
            @endif

            <!-- 4. Input Nilai Rapor - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.scores') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.scores*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>📝</span>
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
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.combined_subjects*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>🧬</span>
                    <span>Mapel Gabungan</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-cyan-500/20 text-cyan-700 dark:text-cyan-400 border border-cyan-500/30">IPA / IPS</span>
                </a>
            @endif

            <!-- 6. Nilai Ekstrakurikuler - Untuk Guru, Pembina & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.extracurriculars') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.extracurriculars*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>🎨</span>
                    <span>Ekstrakurikuler</span>
                </a>
            @endif

            <!-- 7. Kokurikuler / P5 - Untuk Guru & Admin -->
            @if(! $isCounselor)
                <a href="{{ route('rapor_kurnas.cocurricular') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.cocurricular*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>💡</span>
                    <span>Kokurikuler (P5)</span>
                </a>
            @endif

            <!-- 8. Wali Kelas - Hanya untuk Wali Kelas & Tim Kurikulum -->
            @if($isHomeroom || $canManageKurnas)
                <a href="{{ route('rapor_kurnas.homeroom') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.homeroom*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>📋</span>
                    <span>Wali Kelas</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30">Absensi & Catatan</span>
                </a>
            @endif

            <!-- 9. Perkembangan Siswa & SNBP Tracker - Untuk BK, Walas, Kurikulum -->
            @if($isCounselor || $isHomeroom || $canManageKurnas)
                <a href="{{ route('rapor_kurnas.growth') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.growth*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>📉</span>
                    <span>Perkembangan SNBP</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-purple-500/20 text-purple-700 dark:text-purple-400 border border-purple-500/30">Sem 1-6</span>
                </a>
            @endif

            <!-- 10. Cetak Rapor - Untuk Semua Pengajar & Admin -->
            <a href="{{ route('rapor_kurnas.print_hub') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.print_*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🖨️</span>
                <span>Cetak Rapor</span>
                <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/30">e-Rapor 2025</span>
            </a>

            <!-- 11. Ijazah & Transkrip Kelulusan - Admin Kurikulum & Walas Kelas XII -->
            @if($canManageKurnas || $isGrade12Homeroom)
                <a href="{{ route('rapor_kurnas.diploma') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.diploma*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>🎓</span>
                    <span>Ijazah & Transkrip</span>
                    <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-teal-500/20 text-teal-700 dark:text-teal-400 border border-teal-500/30">Permen 13/2025</span>
                </a>
            @endif

            <!-- 12. Buku Induk & Leger - Untuk Admin, BK, dan Walas -->
            @if($canManageKurnas || $isCounselor || $isHomeroom)
                <a href="{{ route('rapor_kurnas.transcripts') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.transcripts*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>📜</span>
                    <span>Buku Induk & Leger</span>
                </a>
            @endif

            <!-- 13. Portal Siswa Preview -->
            <a href="{{ route('rapor_kurnas.my_report') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.my_report*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>👨‍🎓</span>
                <span>Portal Siswa</span>
            </a>

            <!-- 14. Pengaturan & Referensi - Eksklusif Admin Kurikulum -->
            @if($canManageKurnas)
                <a href="{{ route('rapor_kurnas.settings') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('rapor_kurnas.settings*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>⚙️</span>
                    <span>Pengaturan & Referensi</span>
                </a>
            @endif
        @endif
    </div>
</nav>
