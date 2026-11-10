<nav aria-label="Navigasi Bimbingan Konseling" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- 1. Dashboard BK -->
        <a href="{{ route('bk.dashboard') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.dashboard') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📊</span>
            <span>Dashboard BK</span>
        </a>

        <!-- 2. AKPD Diagnostik -->
        <a href="{{ route('bk.akpd.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.akpd.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📋</span>
            <span>AKPD Diagnostik</span>
        </a>

        <!-- 3. RPL BK 1 Lembar -->
        <a href="{{ route('bk.rpl.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.rpl.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📄</span>
            <span>RPL BK 1 Lembar</span>
        </a>

        <!-- 4. Layanan Konseling -->
        <a href="{{ route('bk.counseling.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.counseling.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>💬</span>
            <span>Layanan Konseling</span>
        </a>

        <!-- 5. Konferensi Kasus -->
        <a href="{{ route('bk.case-conference.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.case-conference.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>👥</span>
            <span>Konferensi Kasus</span>
        </a>

        <!-- 6. Alih Tangan Kasus -->
        <a href="{{ route('bk.referral.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.referral.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🔄</span>
            <span>Alih Tangan (Referral)</span>
        </a>

        <!-- 7. Home Visit -->
        <a href="{{ route('bk.home-visit.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.home-visit.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏠</span>
            <span>Home Visit</span>
        </a>

        <!-- 8. Panggilan Orang Tua -->
        <a href="{{ route('bk.parent-call.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.parent-call.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📞</span>
            <span>Panggilan Orang Tua</span>
        </a>

        <!-- 9. Sosiometri Kelas -->
        <a href="{{ route('bk.sociometry.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.sociometry.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🌐</span>
            <span>Sosiometri Kelas</span>
        </a>

        <!-- 10. Tes Psikotes Siswa -->
        <a href="{{ route('bk.psychological.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('bk.psychological.index') || request()->routeIs('bk.psychological.result') || request()->routeIs('bk.psychological.take')) ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🧠</span>
            <span>Tes Psikotes Siswa</span>
        </a>

        <!-- 11. Peta Profil Kelas -->
        <a href="{{ route('bk.psychological.class-report') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.psychological.class-report') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📈</span>
            <span>Peta Profil Kelas</span>
        </a>
    </div>
</nav>

