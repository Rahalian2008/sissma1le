@php
    $navTuUser = Auth::user();
    $canKesiswaan = $navTuUser?->canAccessTuSubmodule('kesiswaan') ?? false;
    $canKepegawaian = $navTuUser?->canAccessTuSubmodule('kepegawaian') ?? false;
    $canPersuratan = $navTuUser?->canAccessTuSubmodule('persuratan') ?? false;
    $canSarpras = $navTuUser?->canAccessTuSubmodule('sarpras') ?? false;
    $canKeuangan = $navTuUser?->canAccessTuSubmodule('keuangan') ?? false;
@endphp

<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max">
        <!-- 1. Pusat TU -->
        <a href="{{ route('adm_tu.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ (request()->routeIs('adm_tu.*') || request()->routeIs('tu.dashboard')) && !request()->routeIs('tu.*.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏛️</span>
            <span>Pusat Kendali TU</span>
        </a>

        <!-- 2. Kesiswaan -->
        @if($canKesiswaan)
        <a href="{{ route('tu.kesiswaan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.kesiswaan.*') ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>👨‍🎓</span>
            <span>Adm. Kesiswaan</span>
        </a>
        @endif

        <!-- 3. Kepegawaian -->
        @if($canKepegawaian)
        <a href="{{ route('tu.kepegawaian.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.kepegawaian.*') ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>💼</span>
            <span>Adm. Kepegawaian</span>
        </a>
        @endif

        <!-- 4. Persuratan -->
        @if($canPersuratan)
        <a href="{{ route('tu.persuratan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.persuratan.*') ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-sky-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📬</span>
            <span>Persuratan & Arsip</span>
        </a>
        @endif

        <!-- 5. Sarpras -->
        @if($canSarpras)
        <a href="{{ route('tu.sarpras.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.sarpras.*') ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-purple-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏢</span>
            <span>Sarpras & BMD</span>
        </a>
        @endif

        <!-- 6. Keuangan -->
        @if($canKeuangan)
        <a href="{{ route('tu.keuangan.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('tu.keuangan.*') ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>💰</span>
            <span>Keuangan & BKU</span>
        </a>
        @endif
    </div>
</div>

