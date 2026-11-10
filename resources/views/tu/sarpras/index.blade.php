@extends('layouts.app')

@section('title', 'Adm. Sarpras & Logistik BMD - Tata Usaha')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-orange-600 to-rose-700 p-6 sm:p-8 text-white shadow-xl shadow-orange-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-amber-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Sub Menu Administrasi Ketatausahaan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Sarana Prasarana & Logistik BMD</h1>
                <p class="text-amber-100 text-sm mt-1 max-w-xl">
                    Sistem inventarisasi Barang Milik Daerah (BMD) standar Permendagri 108/2016 & SIPD e-BMD, Kartu Inventaris Ruangan (KIR), Buku Induk BII & BIBHP, pemeliharaan aset, dan gudang ATK.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('sarpras.rooms.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-black/10 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>+ Kelola & Buat Ruangan</span>
                </a>
                <a href="{{ route('sarpras.assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-orange-700 font-bold text-xs shadow-lg shadow-black/10 hover:bg-amber-50 transition-all">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Katalog KIB Aset</span>
                </a>
                <a href="{{ route('sarpras.kir.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>KIR Ruangan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik Sarpras Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Nilai Aset BMD</span>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1 truncate">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ $totalAssets }} item terdaftar</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kondisi Baik</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($assetsGood) }}</div>
            <div class="text-[11px] text-emerald-500 mt-0.5 font-medium">Siap Digunakan</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rusak Ringan</span>
            <div class="text-2xl font-black text-amber-500 mt-1">{{ number_format($assetsMinorDamage) }}</div>
            <div class="text-[11px] text-amber-500 mt-0.5 font-medium">Perlu Perbaikan</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rusak Berat</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($assetsHeavyDamage) }}</div>
            <div class="text-[11px] text-rose-500 mt-0.5 font-medium">Usulan Afkir: {{ $pendingDisposals }}</div>
        </div>
        <a href="{{ route('sarpras.rooms.index') }}" class="block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs hover:border-blue-500 hover:shadow-md transition-all group">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-blue-600 transition-colors">Ruangan & KIR</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $roomsWithKir }} / {{ $totalRooms }}</div>
            <div class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5 font-medium flex items-center justify-between">
                <span>Ruang Ber-KIR</span>
                <span class="text-[10px] font-bold underline">Kelola &rarr;</span>
            </div>
        </a>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Logistik Gudang</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $totalGoods }}</div>
            <div class="text-[11px] text-purple-600 dark:text-purple-400 mt-0.5 font-medium">{{ $activeBorrowings }} alat dipinjam</div>
        </div>
    </div>

    <!-- Quick Access Feature Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Modul Layanan Sarpras & Logistik BMD Terpadu</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <a href="{{ route('sarpras.rooms.index') }}" class="group p-3.5 bg-blue-50/70 dark:bg-blue-950/30 rounded-2xl border-2 border-blue-400 dark:border-blue-600 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-700 dark:text-blue-300 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-black text-blue-900 dark:text-blue-200">Data Ruangan</span>
                <span class="text-[10px] text-blue-700 dark:text-blue-400 mt-0.5 font-bold">Buat Ruang KIR</span>
            </a>
            <a href="{{ route('sarpras.assets.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">KIB A-F</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Inventaris Aset</span>
            </a>
            <a href="{{ route('sarpras.kir.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-orange-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">KIR Ruang</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kartu Ruangan</span>
            </a>
            <a href="{{ route('sarpras.bibi.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku BII</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Buku Induk Aset</span>
            </a>
            <a href="{{ route('sarpras.bibhp.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">BIBHP</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Habis Pakai</span>
            </a>
            <a href="{{ route('warehouse.receipts.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Penerimaan ATK</span>
                <span class="text-[10px] text-slate-400 mt-0.5">BAST Barang</span>
            </a>
            <a href="{{ route('warehouse.issuances.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Pengeluaran ATK</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Bon Distribusi</span>
            </a>
            <a href="{{ route('warehouse.borrowings.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-yellow-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Peminjaman</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Alat & Sarana</span>
            </a>
            <a href="{{ route('warehouse.stock-opnames.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-violet-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Stock Opname</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Fisik Gudang</span>
            </a>
            <a href="{{ route('sarpras.sipd-bmd.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">SIPD e-BMD</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kode Rekening</span>
            </a>
            <a href="{{ route('sarpras.maintenance.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-cyan-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Perawatan</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Servis & Usulan</span>
            </a>
            <a href="{{ route('warehouse.goods.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Katalog Gudang</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Stok ATK</span>
            </a>
            <a href="{{ route('sarpras.disposals.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-rose-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Afkir Aset</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Penghapusan BMD</span>
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Aset Terkini -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white">Daftar Inventaris Aset Terkini</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pencatatan aset barang milik daerah beserta lokasi dan nomor register</p>
            </div>
            <a href="{{ route('sarpras.assets.index') }}" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">Kelola Semua Aset &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Register</th>
                        <th class="py-3 px-4">Nama Barang & Merk/Tipe</th>
                        <th class="py-3 px-4">KIB</th>
                        <th class="py-3 px-4">Lokasi Ruangan</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4 text-right">Harga Perolehan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($recentAssets as $a)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white text-[11px]">
                            {{ $a->code ?: ($a->register_number ?: '-') }}
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-white">
                            {{ $a->name }}
                            @if($a->brand)
                                <span class="text-slate-400 font-normal">({{ $a->brand }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                KIB {{ $a->kib_type ?: 'B' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                            {{ $a->room?->name ?: 'Gudang / Belum Ditata' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $a->condition === 'Baik' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : ($a->condition === 'Rusak Ringan' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20') }}">
                                {{ $a->condition }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($a->price ?: 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada aset BMD yang terdata.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

