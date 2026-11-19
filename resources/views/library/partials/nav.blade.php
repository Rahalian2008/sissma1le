@php
    $canManage = Auth::check() && Auth::user()->canManageLibrary();
    $pendingCount = \App\Models\LibraryLoan::where('status', 'MENUNGGU')->count();
    $userPending = Auth::check() ? \App\Models\LibraryLoan::whereHas('member', function($q) {
        $q->where('user_id', Auth::id());
    })->where('status', 'MENUNGGU')->count() : 0;
@endphp

<nav aria-label="Navigasi Menu Perpustakaan Digital" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1.5 sm:p-2 shadow-xs mb-4 sm:mb-6 overflow-x-auto scrollbar-none touch-pan-x select-none">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- Dashboard -->
        <a href="{{ route('library.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('library.index') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Dashboard</span>
        </a>

        <!-- Katalog Buku -->
        <a href="{{ route('library.books.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('library.books.*') && request('book_type') !== 'digital_flip') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ (request()->routeIs('library.books.*') && request('book_type') !== 'digital_flip') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Katalog Buku</span>
        </a>

        <!-- Koleksi E-Book Flip HTML -->
        <a href="{{ route('library.books.index', ['book_type' => 'digital_flip']) }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('library.books.*') && request('book_type') === 'digital_flip') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ (request()->routeIs('library.books.*') && request('book_type') === 'digital_flip') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            <span>E-Book Flip 3D</span>
        </a>

        @if($canManage)
            <!-- Sirkulasi & Antrean (Pengelola) -->
            <a href="{{ route('library.loans.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.loans.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.loans.index') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Sirkulasi & Antrean</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-bold animate-pulse">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Anggota Perpustakaan -->
            <a href="{{ route('library.members.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.members.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.members.index') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Anggota Pustaka</span>
            </a>

            <!-- Buku Tamu & Presensi -->
            <a href="{{ route('library.visitors.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.visitors.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.visitors.index') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Presensi Pengunjung</span>
            </a>

            <!-- Kiosk Buku Tamu Mandiri (Layar Sentuh / Publik) -->
            <a href="{{ route('library.guest_book.index') }}" target="_blank"
               title="Buka Layar Kiosk Buku Tamu Mandiri Pengunjung"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 hover:bg-emerald-100">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Kiosk Buku Tamu</span>
            </a>

            <!-- Rangking & Rapor Literasi (NEW) -->
            <a href="{{ route('library.leaderboard') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4m0 0l3 3m-3-3L9 7m10 5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Rapor & Rangking</span>
            </a>

            <!-- Laporan Buku Tamu Pengunjung -->
            <a href="{{ route('library.reports.visitors') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.visitors*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.reports.visitors*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan Pengunjung</span>
            </a>

            <!-- Laporan Sirkulasi Buku -->
            <a href="{{ route('library.reports.loans') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.loans*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.reports.loans*') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Laporan Sirkulasi</span>
            </a>

            <!-- Laporan Lengkap Inventaris Buku -->
            <a href="{{ route('library.reports.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.index') || request()->routeIs('library.reports.print') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.reports.index') || request()->routeIs('library.reports.print') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan Inventaris</span>
            </a>

            @if(Auth::user()->isLibraryAdmin())
                <!-- Pengaturan (Admin / Kepala Perpustakaan) -->
                <a href="{{ route('library.settings') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.settings') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('library.settings') ? 'text-white' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan</span>
                </a>
            @endif

        @else
            <!-- Peminjaman Saya (Pemustaka) -->
            <a href="{{ route('library.loans.my') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.loans.my') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.loans.my') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Peminjaman Saya</span>
                @if($userPending > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-bold">{{ $userPending }}</span>
                @endif
            </a>

            <!-- Kartu Anggota Digital -->
            <a href="{{ route('library.members.my-card') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.members.my-card') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.members.my-card') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Kartu Anggota</span>
            </a>

            <!-- Rapor & Peringkat Literasi Saya -->
            <a href="{{ route('library.leaderboard') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'text-white' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4m0 0l3 3m-3-3L9 7m10 5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Rangking & Piagam</span>
            </a>
        @endif
    </div>
</nav>

