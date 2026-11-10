@php
    $canManage = Auth::check() && Auth::user()->canManageLibrary();
    $pendingCount = \App\Models\LibraryLoan::where('status', 'MENUNGGU')->count();
    $userPending = Auth::check() ? \App\Models\LibraryLoan::whereHas('member', function($q) {
        $q->where('user_id', Auth::id());
    })->where('status', 'MENUNGGU')->count() : 0;
@endphp

<nav aria-label="Navigasi Menu Perpustakaan Digital" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- Dashboard -->
        <a href="{{ route('library.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📊</span>
            <span>Dashboard</span>
        </a>

        <!-- Katalog Buku -->
        <a href="{{ route('library.books.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('library.books.*') && request('book_type') !== 'digital_flip') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📚</span>
            <span>Katalog Buku</span>
        </a>

        <!-- Koleksi E-Book Flip HTML -->
        <a href="{{ route('library.books.index', ['book_type' => 'digital_flip']) }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('library.books.*') && request('book_type') === 'digital_flip') ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-amber-600 dark:text-amber-400 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 hover:bg-amber-100' }}">
            <span>✨</span>
            <span>E-Book Flip 3D</span>
        </a>

        @if($canManage)
            <!-- Sirkulasi & Antrean (Pengelola) -->
            <a href="{{ route('library.loans.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.loans.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🔄</span>
                <span>Sirkulasi & Antrean</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-400 text-slate-950 font-black animate-pulse">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Anggota Perpustakaan -->
            <a href="{{ route('library.members.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.members.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>👥</span>
                <span>Anggota Pustaka</span>
            </a>

            <!-- Buku Tamu & Presensi -->
            <a href="{{ route('library.visitors.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.visitors.index') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📝</span>
                <span>Presensi Pengunjung</span>
            </a>

            <!-- Kiosk Buku Tamu Mandiri (Layar Sentuh / Publik) -->
            <a href="{{ route('library.guest_book.index') }}" target="_blank"
               title="Buka Layar Kiosk Buku Tamu Mandiri Pengunjung"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 hover:bg-emerald-100">
                <span>🖥️</span>
                <span>Kiosk Buku Tamu</span>
            </a>

            <!-- Rangking & Rapor Literasi (NEW) -->
            <a href="{{ route('library.leaderboard') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏆</span>
                <span>Rapor & Rangking</span>
            </a>

            <!-- Laporan Buku Tamu Pengunjung -->
            <a href="{{ route('library.reports.visitors') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.visitors*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📊</span>
                <span>Laporan Pengunjung</span>
            </a>

            <!-- Laporan Sirkulasi Buku -->
            <a href="{{ route('library.reports.loans') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.loans*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🔄</span>
                <span>Laporan Sirkulasi</span>
            </a>

            <!-- Laporan Lengkap Inventaris Buku -->
            <a href="{{ route('library.reports.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.reports.index') || request()->routeIs('library.reports.print') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📑</span>
                <span>Laporan Inventaris</span>
            </a>

            @if(Auth::user()->isLibraryAdmin())
                <!-- Pengaturan (Admin / Kepala Perpustakaan) -->
                <a href="{{ route('library.settings') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.settings') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <span>⚙️</span>
                    <span>Pengaturan</span>
                </a>
            @endif

        @else
            <!-- Peminjaman Saya (Pemustaka) -->
            <a href="{{ route('library.loans.my') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.loans.my') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📖</span>
                <span>Peminjaman Saya</span>
                @if($userPending > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-400 text-slate-950 font-black">{{ $userPending }}</span>
                @endif
            </a>

            <!-- Kartu Anggota Digital -->
            <a href="{{ route('library.members.my-card') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.members.my-card') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🪪</span>
                <span>Kartu Anggota</span>
            </a>

            <!-- Rapor & Peringkat Literasi Saya -->
            <a href="{{ route('library.leaderboard') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('library.leaderboard') || request()->routeIs('library.certificate*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏆</span>
                <span>Rangking & Piagam</span>
            </a>
        @endif
    </div>
</nav>

