<nav aria-label="Navigasi Menu Presensi" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- 1. Presensi Utama -->
        <a href="{{ route('attendance.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('attendance.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📋</span>
            <span>Presensi Utama</span>
        </a>

        <!-- 2. Presensi Ekstrakurikuler -->
        <a href="{{ route('extracurricular.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('extracurricular.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>⚽</span>
            <span>Presensi Ekstrakurikuler</span>
        </a>

        <!-- 5. Presensi Shalat Dhuha -->
        <a href="{{ route('worship.index', ['activity' => 'dhuha']) }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dhuha') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>☀️</span>
            <span>Presensi Shalat Dhuha</span>
        </a>

        <!-- 6. Presensi Shalat Dzuhur/Jum'at -->
        <a href="{{ route('worship.index', ['activity' => 'dzuhur-jumat']) }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'dzuhur-jumat') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🕌</span>
            <span>Presensi Shalat Dzuhur/Jum'at</span>
        </a>

        <!-- 7. Presensi Keputrian -->
        <a href="{{ route('worship.index', ['activity' => 'keputrian']) }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('worship.*') && request()->route('activity') === 'keputrian') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🧕</span>
            <span>Presensi Keputrian</span>
        </a>
    </div>
</nav>
