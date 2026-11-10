<nav aria-label="Navigasi Menu 7 Kebiasaan Anak Hebat" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- 1. 7 Kebiasaan Anak Hebat -->
        <a href="{{ route('habits.journal') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('habits.journal') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🌟</span>
            <span>7 Kebiasaan Anak Hebat</span>
        </a>

        <!-- 2. Ringkasan Karakter -->
        <a href="{{ route('habits.summary') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('habits.summary') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📑</span>
            <span>Ringkasan Karakter</span>
        </a>

        <!-- 3. Diagram & Persentase -->
        <a href="{{ route('habits.charts') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('habits.charts') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📊</span>
            <span>Diagram & Persentase</span>
        </a>

        <!-- 4. Laporan Karakter -->
        <a href="{{ route('habits.report') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('habits.report') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🖨️</span>
            <span>Laporan Karakter</span>
        </a>
    </div>
</nav>
