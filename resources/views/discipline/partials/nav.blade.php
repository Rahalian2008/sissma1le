<nav aria-label="Navigasi Menu Tata Tertib & Prestasi" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- 1. Prestasi Siswa (R1-R10) -->
        <a href="{{ route('achievements.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('achievements.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏆</span>
            <span>Prestasi Siswa (R1-R10)</span>
        </a>

        <!-- 2. Pelanggaran Siswa (P1-P6) -->
        <a href="{{ route('violations.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('violations.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>⚠️</span>
            <span>Pelanggaran Siswa (P1-P6)</span>
        </a>

        <!-- 3. Catatan Pembinaan -->
        <a href="{{ route('guidance.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('guidance.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🤝</span>
            <span>Catatan Pembinaan</span>
        </a>

        <!-- 4. Papan Peringkat (Rangking) -->
        <a href="{{ route('discipline.ranking') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('discipline.ranking*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📊</span>
            <span>Papan Peringkat (Rangking)</span>
        </a>

        <!-- 5. Laporan Tata Tertib -->
        <a href="{{ route('discipline.report') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('discipline.report*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📑</span>
            <span>Laporan Tata Tertib</span>
        </a>
    </div>
</nav>
