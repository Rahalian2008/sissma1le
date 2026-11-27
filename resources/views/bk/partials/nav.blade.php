@php
    $navUser = Auth::user();
    $roles = $navUser ? $navUser->all_roles : [];
    $isStudent = $navUser && ($navUser->role === 'siswa' || in_array('siswa', $roles)) && ! $navUser->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'kepala_sekolah', 'koordinator_bk', 'guru_bk', 'bk']);
    $isWaliKelas = $navUser && ($navUser->role === 'wali_kelas' || in_array('wali_kelas', $roles)) && ! $navUser->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
@endphp

<nav aria-label="Navigasi Bimbingan Konseling" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1.5 sm:p-2 shadow-xs mb-6 overflow-x-auto scrollbar-none touch-pan-x select-none">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        @if(!$isStudent)
        <!-- 1. Dashboard BK -->
        <a href="{{ route('bk.dashboard') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.dashboard') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('bk.dashboard') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>{{ $isWaliKelas ? 'Dashboard BK Binaan' : 'Dashboard BK' }}</span>
        </a>

        <!-- Data Siswa & Buku Induk -->
        <a href="{{ route('bk.students.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.students.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('bk.students.*') ? 'text-white' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Data Siswa &amp; Buku Induk</span>
        </a>
        @endif

        @if(!$isWaliKelas)
        <!-- 2. Ruang Curhat & Konseling Online -->
        <a href="{{ route('bk.curhat.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.curhat.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('bk.curhat.*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Ruang Curhat BK</span>
        </a>
        @endif

        <!-- 3. Tes Psikotes Siswa -->
        <a href="{{ route('bk.psychological.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('bk.psychological.index') || request()->routeIs('bk.psychological.result') || request()->routeIs('bk.psychological.take')) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ (request()->routeIs('bk.psychological.index') || request()->routeIs('bk.psychological.result') || request()->routeIs('bk.psychological.take')) ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span>Tes Psikotes Siswa</span>
        </a>

        @php
            $canSeeAkpd = ! $isWaliKelas && (! $isStudent || ($navUser && $navUser->isGradeXStudent()));
        @endphp
        @if($canSeeAkpd)
        <!-- 4. AKPD Diagnostik -->
        <a href="{{ route('bk.akpd.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.akpd.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('bk.akpd.*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>AKPD Diagnostik</span>
        </a>
        @endif

        <!-- 5. Sosiometri Kelas -->
        <a href="{{ route('bk.sociometry.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.sociometry.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('bk.sociometry.*') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-3-9-3m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            <span>Sosiometri Kelas</span>
        </a>

        @if(!$isStudent)
            <!-- 6. Layanan Konseling -->
            <a href="{{ route('bk.counseling.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.counseling.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.counseling.*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                <span>Layanan Konseling</span>
            </a>

            <!-- 7. Panggilan Orang Tua -->
            <a href="{{ route('bk.parent-call.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.parent-call.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.parent-call.*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span>Panggilan Orang Tua</span>
            </a>

            <!-- 8. Home Visit (Kunjungan Rumah) -->
            <a href="{{ route('bk.home-visit.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.home-visit.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.home-visit.*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Home Visit</span>
            </a>

            <!-- 9. Peta Profil Kelas -->
            <a href="{{ route('bk.psychological.class-report') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.psychological.class-report') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.psychological.class-report') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Peta Profil Kelas</span>
            </a>
        @endif

        @if(!$isStudent && !$isWaliKelas)
            <!-- 10. RPL BK 1 Lembar -->
            <a href="{{ route('bk.rpl.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.rpl.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.rpl.*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>RPL BK 1 Lembar</span>
            </a>

            <!-- 11. Konferensi Kasus -->
            <a href="{{ route('bk.case-conference.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.case-conference.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.case-conference.*') ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Konferensi Kasus</span>
            </a>

            <!-- 12. Alih Tangan Kasus -->
            <a href="{{ route('bk.referral.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.referral.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.referral.*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Alih Tangan Kasus</span>
            </a>

            <!-- 13. Tracer Alumni -->
            <a href="{{ route('bk.tracer.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.tracer.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('bk.tracer.*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                <span>Tracer Alumni</span>
            </a>
        @endif
    </div>
</nav>

