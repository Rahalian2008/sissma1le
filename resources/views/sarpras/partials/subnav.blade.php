<div class="space-y-2 mb-6">
    <!-- Top Level TU Navigation (Pusat TU, Kesiswaan, Kepegawaian, Persuratan, Sarpras, Keuangan) -->
    @include('tu.partials.nav')

    <!-- Sub-Navigasi Terpadu Sarpras & Logistik BMD (Fleksibel & Lengkap) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3 sm:p-4 shadow-xs space-y-2.5">
        <div class="flex items-center justify-between gap-2 flex-wrap pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-1.5">
                    <span>🏢</span> Menu Terpadu Sarpras & Logistik BMD
                </span>
            </div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                Akses cepat antar-modul inventaris & pergudangan sekolah
            </div>
        </div>

        <!-- Baris 1: Aset BMD, Ruangan & Buku Induk -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-900/60 shrink-0">
                Aset & Ruang:
            </span>

            <a href="{{ route('tu.sarpras.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('tu.sarpras.*') ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📊</span> Dashboard
            </a>

            <a href="{{ route('sarpras.rooms.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.rooms.*') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🏛️</span> Data Ruangan
            </a>

            <a href="{{ route('sarpras.assets.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.assets.*') && !request()->routeIs('sarpras.assets.labels.*') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📦</span> Katalog KIB A-F
            </a>

            <a href="{{ route('sarpras.kir.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.kir.*') ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📋</span> KIR Ruangan
            </a>

            <a href="{{ route('sarpras.bibi.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.bibi.*') || request()->routeIs('sarpras.bii.*') ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📖</span> Buku BII Aset
            </a>

            <a href="{{ route('sarpras.bibhp.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.bibhp.*') ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📑</span> BIBHP Habis Pakai
            </a>
        </div>

        <!-- Baris 2: Logistik Gudang & Sirkulasi Barang -->
        <div class="flex items-center gap-1.5 flex-wrap pt-1 border-t border-slate-100 dark:border-slate-800/80">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-900/60 shrink-0">
                Gudang & Logistik:
            </span>

            <a href="{{ route('warehouse.receipts.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('warehouse.receipts.*') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📥</span> Penerimaan (BM)
            </a>

            <a href="{{ route('warehouse.issuances.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('warehouse.issuances.*') ? 'bg-sky-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📤</span> Pengeluaran / Bon
            </a>

            <a href="{{ route('warehouse.borrowings.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('warehouse.borrowings.*') ? 'bg-yellow-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🤝</span> Peminjaman Alat
            </a>

            <a href="{{ route('warehouse.goods.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('warehouse.goods.*') ? 'bg-violet-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🏷️</span> Stok Gudang ATK
            </a>

            <a href="{{ route('warehouse.stock-opnames.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('warehouse.stock-opnames.*') ? 'bg-cyan-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>📊</span> Stock Opname
            </a>

            <a href="{{ route('sarpras.maintenance.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.maintenance.*') ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🔧</span> Pemeliharaan
            </a>

            <a href="{{ route('sarpras.disposals.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.disposals.*') ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🗑️</span> Usulan Afkir
            </a>

            <a href="{{ route('sarpras.sipd-bmd.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('sarpras.sipd-bmd.*') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                <span>🏛️</span> Standar SIPD e-BMD
            </a>
        </div>
    </div>
</div>
