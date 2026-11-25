@php
    $navTuUser = Auth::user();
    $canKesiswaan = $navTuUser?->canAccessTuSubmodule('kesiswaan') ?? false;
    $canKepegawaian = $navTuUser?->canAccessTuSubmodule('kepegawaian') ?? false;
    $canPersuratan = $navTuUser?->canAccessTuSubmodule('persuratan') ?? false;
    $canSarpras = $navTuUser?->canAccessTuSubmodule('sarpras') ?? false;
    $canKeuangan = $navTuUser?->canAccessTuSubmodule('keuangan') ?? false;
@endphp

<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1.5 sm:p-2 shadow-xs mb-6 overflow-x-auto scrollbar-none touch-pan-x select-none">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max">
        <!-- 1. Pusat TU -->
        <a href="{{ route('adm_tu.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('adm_tu.*') || request()->routeIs('tu.dashboard')) && !request()->routeIs('tu.*.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ (request()->routeIs('adm_tu.*') || request()->routeIs('tu.dashboard')) && !request()->routeIs('tu.*.*') ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Pusat Kendali TU</span>
        </a>

        <!-- 2. Kesiswaan -->
        @if($canKesiswaan)
        <a href="{{ route('tu.kesiswaan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.kesiswaan.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('tu.kesiswaan.*') ? 'text-white' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
            </svg>
            <span>Adm. Kesiswaan</span>
        </a>
        @endif

        <!-- 3. Kepegawaian -->
        @if($canKepegawaian)
        <a href="{{ route('tu.kepegawaian.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.kepegawaian.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('tu.kepegawaian.*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>Adm. Kepegawaian</span>
        </a>
        @endif

        <!-- 4. Persuratan -->
        @if($canPersuratan)
        <a href="{{ route('tu.persuratan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.persuratan.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('tu.persuratan.*') ? 'text-white' : 'text-sky-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>Persuratan & Arsip</span>
        </a>
        @endif

        <!-- 5. Sarpras -->
        @if($canSarpras)
        <a href="{{ route('tu.sarpras.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.sarpras.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('tu.sarpras.*') ? 'text-white' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Sarpras & BMD</span>
        </a>
        @endif

        <!-- 6. Keuangan -->
        @if($canKeuangan)
        <a href="{{ route('tu.keuangan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.keuangan.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('tu.keuangan.*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Keuangan & BKU</span>
        </a>
        @endif

        <!-- 7. Buku Tamu Digital -->
        <a href="{{ route('guests.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold sm:font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('guests.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('guests.*') ? 'text-white' : 'text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Buku Tamu Digital</span>
        </a>
    </div>
</div>

