@php
    $navUser = Auth::user();
    $roles = $navUser ? $navUser->all_roles : [];
    $isStudent = $navUser && ($navUser->role === 'siswa' || in_array('siswa', $roles)) && ! $navUser->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'kepala_sekolah', 'koordinator_bk', 'guru_bk', 'bk']);
    $isWaliKelas = $navUser && ($navUser->role === 'wali_kelas' || in_array('wali_kelas', $roles)) && ! $navUser->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
@endphp

<nav aria-label="Navigasi Bimbingan Konseling" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        @if(!$isStudent)
        <!-- 1. Dashboard BK -->
        <a href="{{ route('bk.dashboard') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.dashboard') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📊</span>
            <span>{{ $isWaliKelas ? 'Dashboard BK Kelas Binaan' : 'Dashboard BK' }}</span>
        </a>
        @endif

        @if(!$isWaliKelas)
        <!-- 2. Ruang Curhat & Konseling Online (Chat Interaktif WhatsApp Style) -->
        <a href="{{ route('bk.curhat.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.curhat.*') ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/30' : 'text-emerald-700 dark:text-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 font-extrabold' }}">
            <span>💬</span>
            <span>Ruang Curhat BK</span>
        </a>
        @endif

        <!-- 3. Tes Psikotes Siswa (VAK, Holland RIASEC, Gardner Multiple Intelligences) -->
        <a href="{{ route('bk.psychological.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('bk.psychological.index') || request()->routeIs('bk.psychological.result') || request()->routeIs('bk.psychological.take')) ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🧠</span>
            <span>Tes Psikotes Siswa</span>
        </a>

        @if(!$isWaliKelas)
        <!-- 4. AKPD Diagnostik (Siswa & Tim BK) -->
        <a href="{{ route('bk.akpd.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.akpd.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📋</span>
            <span>AKPD Diagnostik</span>
        </a>
        @endif

        <!-- 5. Sosiometri Kelas -->
        <a href="{{ route('bk.sociometry.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.sociometry.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🌐</span>
            <span>Sosiometri Kelas</span>
        </a>

        @if(!$isStudent)
            <!-- 6. Layanan Konseling (Khusus Tim BK & Wali Kelas Siswa) -->
            <a href="{{ route('bk.counseling.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.counseling.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>💬</span>
                <span>Layanan Konseling</span>
            </a>

            <!-- 7. Panggilan Orang Tua (Didampingi Wali Kelas & Koordinator BK) -->
            <a href="{{ route('bk.parent-call.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.parent-call.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📞</span>
                <span>Panggilan Orang Tua</span>
            </a>

            <!-- 8. Home Visit (Kunjungan Rumah) -->
            <a href="{{ route('bk.home-visit.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.home-visit.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏠</span>
                <span>Home Visit</span>
            </a>

            <!-- 9. Peta Profil Kelas -->
            <a href="{{ route('bk.psychological.class-report') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.psychological.class-report') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📈</span>
                <span>Peta Profil Kelas</span>
            </a>
        @endif

        @if(!$isStudent && !$isWaliKelas)
            <!-- 10. RPL BK 1 Lembar (Khusus Guru BK/Koordinator BK) -->
            <a href="{{ route('bk.rpl.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.rpl.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📄</span>
                <span>RPL BK 1 Lembar</span>
            </a>

            <!-- 11. Konferensi Kasus (Khusus Tim BK Internal) -->
            <a href="{{ route('bk.case-conference.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.case-conference.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>👥</span>
                <span>Konferensi Kasus</span>
            </a>

            <!-- 12. Alih Tangan Kasus (Referral Spesialis) -->
            <a href="{{ route('bk.referral.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('bk.referral.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🔄</span>
                <span>Alih Tangan (Referral)</span>
            </a>
        @endif
    </div>
</nav>

