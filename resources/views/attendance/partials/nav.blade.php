<div class="w-full max-w-full min-w-0 overflow-hidden mb-3.5 sm:mb-6">
    <nav aria-label="Navigasi Menu Presensi" class="w-full max-w-full min-w-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl sm:rounded-2xl p-1.5 sm:p-2 shadow-xs overflow-x-auto scrollbar-none touch-pan-x select-none">
        <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
            <!-- 1. Presensi Utama -->
            <a href="{{ route('attendance.index') }}" 
               class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 shrink-0 {{ request()->routeIs('attendance.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('attendance.index') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>Presensi Utama</span>
            </a>

            <!-- 2. Presensi Ekstrakurikuler -->
            <a href="{{ route('extracurricular.index') }}" 
               class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 shrink-0 {{ request()->routeIs('extracurricular.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('extracurricular.*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span>Presensi Ekstrakurikuler</span>
            </a>

            <!-- 5. Presensi Shalat Dhuha -->
            <a href="{{ route('worship.index', ['activity' => 'dhuha']) }}" 
               class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 shrink-0 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dhuha') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dhuha') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Presensi Shalat Dhuha</span>
            </a>

            <!-- 6. Presensi Shalat Dzuhur/Jum'at -->
            <a href="{{ route('worship.index', ['activity' => 'dzuhur-jumat']) }}" 
               class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 shrink-0 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dzuhur-jumat') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dzuhur-jumat') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Presensi Shalat Dzuhur/Jum'at</span>
            </a>

            <!-- 7. Presensi Keputrian -->
            <a href="{{ route('worship.index', ['activity' => 'keputrian']) }}" 
               class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 shrink-0 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'keputrian') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'keputrian') ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Presensi Keputrian</span>
            </a>
        </div>
    </nav>
</div>
