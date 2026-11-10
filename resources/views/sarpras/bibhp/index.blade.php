@extends('layouts.app')

@section('title', 'Buku Induk Barang Habis Pakai (Non-Inventaris)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">Aset Lancar & Persediaan</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Permendagri No. 108/2016 (Akun 1.1.7)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Induk Barang Habis Pakai / Non-Inventaris (BIBHP)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan resmi dan neraca persediaan bahan habis pakai (ATK, Tinta/Komputer, Cetakan, Alat Kebersihan, Praktik Lab IPA/Seni, Obat UKS).</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Ekspor CSV -->
            <a href="{{ route('sarpras.bibhp.export-csv') }}" class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel / CSV
            </a>

            <!-- Dropdown Cetak Dokumen -->
            <div class="relative inline-block text-left" id="printBibhpWrapper">
                <button type="button" 
                        id="printBibhpBtn"
                        onclick="toggleBibhpPrintMenu(event)" 
                        class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Dokumen BIBHP</span>
                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="printBibhpDropdown" 
                     class="hidden origin-top-right absolute right-0 mt-2 w-72 max-h-[60vh] overflow-y-auto rounded-xl shadow-xl bg-white border border-slate-200 divide-y divide-slate-100 z-50 transition-all custom-scroll">
                    <div class="p-2">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1">Pilihan Format Cetak</div>
                        <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['sort_by' => 'default'])) }}" target="_blank" onclick="closeBibhpPrintMenu()" class="flex items-center px-2.5 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition">
                            <span class="w-2 h-2 rounded-full bg-slate-400 mr-2.5 shrink-0"></span>
                            <div class="flex-1">
                                <div class="font-medium">Cetak Standar Sesuai Kategori</div>
                                <div class="text-[10px] text-slate-400">Urutan ATK, Tinta, Medis, Lab, dll.</div>
                            </div>
                        </a>
                        <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['sort_by' => 'value_desc'])) }}" target="_blank" onclick="closeBibhpPrintMenu()" class="flex items-center px-2.5 py-2 text-xs text-blue-700 hover:bg-blue-50 rounded-lg transition">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2.5 shrink-0"></span>
                            <div class="flex-1">
                                <div class="font-bold">Cetak Urut Nilai Persediaan Tertinggi</div>
                                <div class="text-[10px] text-blue-500">Membantu rekonsiliasi SPJ & BPKAD</div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1">Filter Khusus Status Fisik</div>
                        <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['status' => 'menipis'])) }}" target="_blank" onclick="closeBibhpPrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-amber-700 hover:bg-amber-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Barang Stok Menipis</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">{{ $stockStats['menipis'] }}</span>
                        </a>
                        <a href="{{ route('sarpras.bibhp.print', array_merge(request()->query(), ['status' => 'habis'])) }}" target="_blank" onclick="closeBibhpPrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-rose-700 hover:bg-rose-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-rose-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Barang Stok Habis (0)</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">{{ $stockStats['habis'] }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah BAST & Cari BHP -->
            <a href="{{ route('warehouse.receipts.index') }}" class="inline-flex items-center px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Penerimaan BAST
            </a>
        </div>
    </div>

    <!-- REKAPITULASI STATISTIK BUKU INDUK BARANG HABIS PAKAI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Jenis Barang -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Item Persediaan</span>
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalItemsCount, 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-500">jenis barang</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Terdaftar dalam katalog bahan habis pakai</p>
        </div>

        <!-- Card 2: Total Fisik Stok Tersedia -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Stok Fisik</span>
                <span class="p-2 rounded-xl bg-teal-50 text-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-teal-700">{{ number_format($totalStockQty, 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-500">unit akumulatif</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Stok riil di gudang utama & lab</p>
        </div>

        <!-- Card 3: Total Nilai Nominal Persediaan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nilai Buku Persediaan</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-xl font-black text-emerald-700">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Sesuai standar Bagan Akun 1.1.7</p>
        </div>

        <!-- Card 4: Status Ketersediaan Stok -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Ketersediaan</span>
                <span class="text-[10px] font-mono font-bold text-slate-400">GUDANG</span>
            </div>
            <div class="grid grid-cols-3 gap-1.5 pt-1 text-center">
                <a href="{{ route('sarpras.bibhp.index', array_merge(request()->query(), ['status' => request('status') === 'aman' ? null : 'aman'])) }}" class="p-2 rounded-xl border transition {{ request('status') === 'aman' ? 'bg-emerald-600 text-white border-emerald-600 font-bold' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
                    <div class="text-base font-black">{{ $stockStats['aman'] }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-semibold">Aman</div>
                </a>
                <a href="{{ route('sarpras.bibhp.index', array_merge(request()->query(), ['status' => request('status') === 'menipis' ? null : 'menipis'])) }}" class="p-2 rounded-xl border transition {{ request('status') === 'menipis' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
                    <div class="text-base font-black">{{ $stockStats['menipis'] }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-semibold">Menipis</div>
                </a>
                <a href="{{ route('sarpras.bibhp.index', array_merge(request()->query(), ['status' => request('status') === 'habis' ? null : 'habis'])) }}" class="p-2 rounded-xl border transition {{ request('status') === 'habis' ? 'bg-rose-600 text-white border-rose-600 font-bold' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
                    <div class="text-base font-black">{{ $stockStats['habis'] }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-semibold">Habis</div>
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
        <form method="GET" action="{{ route('sarpras.bibhp.index') }}" class="flex flex-wrap items-center gap-2.5">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari kode 1.1.7, nama barang habis pakai, lokasi..." 
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filter Kategori -->
            <select name="category" class="text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <!-- Filter Lokasi Gudang -->
            @if($locations->isNotEmpty())
            <select name="storage_location" class="text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="all">Semua Lokasi Gudang</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc }}" {{ $selectedLocation === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
            @endif

            <!-- Filter Status -->
            <select name="status" class="text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status Stok</option>
                <option value="aman" {{ $selectedStatus === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                <option value="menipis" {{ $selectedStatus === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="habis" {{ $selectedStatus === 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>

            <!-- Sorting -->
            <select name="sort_by" class="text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="default" {{ $sortBy === 'default' ? 'selected' : '' }}>Urut: Standar Kategori</option>
                <option value="code" {{ $sortBy === 'code' ? 'selected' : '' }}>Urut: Kodefikasi (1.1.7)</option>
                <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Urut: Nama Barang (A-Z)</option>
                <option value="value_desc" {{ $sortBy === 'value_desc' ? 'selected' : '' }}>Urut: Nilai Persediaan Tertinggi</option>
                <option value="stock_desc" {{ $sortBy === 'stock_desc' ? 'selected' : '' }}>Urut: Sisa Stok Terbanyak</option>
                <option value="stock_asc" {{ $sortBy === 'stock_asc' ? 'selected' : '' }}>Urut: Sisa Stok Paling Sedikit</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                Terapkan
            </button>
            <a href="{{ route('sarpras.bibhp.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition">
                Reset
            </a>
        </form>
    </div>

    <!-- TABEL DATA BUKU INDUK BARANG HABIS PAKAI -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Daftar Buku Induk Bahan Habis Pakai / Non-Inventaris</h3>
                <p class="text-[11px] text-slate-500">Menampilkan {{ $paginatedGoods->firstItem() ?? 0 }} - {{ $paginatedGoods->lastItem() ?? 0 }} dari total {{ $paginatedGoods->total() }} barang persediaan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-mono bg-white border border-slate-200 text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-teal-500 mr-2"></span>
                    BAS Akun 1.1.7
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/75 text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-3">Kodefikasi (Permendagri 1.1.7)</th>
                        <th class="py-3 px-3">Nama Barang Habis Pakai</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3 text-center">Satuan</th>
                        <th class="py-3 px-3">Lokasi Gudang</th>
                        <th class="py-3 px-3 text-right">Masuk</th>
                        <th class="py-3 px-3 text-right">Keluar</th>
                        <th class="py-3 px-3 text-right font-black text-slate-900 bg-blue-50/50">Sisa Stok</th>
                        <th class="py-3 px-3 text-right">Harga Satuan</th>
                        <th class="py-3 px-3 text-right">Total Nilai Persediaan</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($paginatedGoods as $index => $good)
                    <tr class="hover:bg-blue-50/40 transition">
                        <td class="py-3 px-3 text-center font-mono text-slate-400">
                            {{ $paginatedGoods->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-3 font-mono font-medium whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-md bg-teal-50 border border-teal-200 text-teal-800 text-[11px] font-bold">
                                {{ $good->code }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-900">{{ $good->name }}</div>
                            @if($good->sim_bmd_code && $good->sim_bmd_code !== $good->code)
                                <div class="text-[10px] text-slate-400 font-mono">Kode e-BMD: {{ $good->sim_bmd_code }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $good->category }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center whitespace-nowrap font-medium text-slate-600">
                            {{ $good->unit }}
                        </td>
                        <td class="py-3 px-3 text-slate-600 whitespace-nowrap">
                            <span class="flex items-center gap-1 text-[11px]">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $good->storage_location ?: 'Gudang Utama' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-600 font-semibold">
                            +{{ number_format($good->total_received, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-rose-600 font-semibold">
                            -{{ number_format($good->total_issued, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-black text-sm text-slate-900 bg-blue-50/50">
                            {{ number_format($good->current_stock, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-700 whitespace-nowrap">
                            Rp {{ number_format($good->latest_unit_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700 whitespace-nowrap">
                            Rp {{ number_format($good->inventory_value, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            @if($good->stock_status === 'Aman')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Aman
                                </span>
                            @elseif($good->stock_status === 'Menipis')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                    Menipis (≤{{ $good->minimum_stock }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Habis (0)
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <a href="{{ route('warehouse.goods.card', $good->id) }}" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition inline-flex items-center" title="Kartu Stok Persediaan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-12 text-slate-400">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p class="font-semibold text-slate-600">Belum ada data barang habis pakai yang sesuai kriteria pencarian.</p>
                            <p class="text-xs text-slate-400 mt-1">Gunakan tombol Penerimaan BAST atau sesuaikan kata kunci filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedGoods->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $paginatedGoods->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function toggleBibhpPrintMenu(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('printBibhpDropdown');
    dropdown.classList.toggle('hidden');
}

function closeBibhpPrintMenu() {
    const dropdown = document.getElementById('printBibhpDropdown');
    if (dropdown) dropdown.classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('printBibhpWrapper');
    const dropdown = document.getElementById('printBibhpDropdown');
    if (wrapper && !wrapper.contains(e.target)) {
        if (dropdown) dropdown.classList.add('hidden');
    }
});
</script>
@endsection
