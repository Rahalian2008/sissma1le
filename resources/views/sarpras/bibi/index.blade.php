@extends('layouts.app')

@section('title', 'Buku Induk Inventaris (BII)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Standar TAS & BMD</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Permendiknas No. 24/2008</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Induk Inventaris (BII)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi komprehensif seluruh barang inventaris milik satuan pendidikan (KIB A s.d. KIB F) dan neraca kekayaan aset sekolah.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Cetak Berdasarkan Filter & Sort Aktif -->
            <a href="{{ route('sarpras.bibi.print', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen BII
            </a>

            <!-- Tombol Cetak Cepat: Keseluruhan vs Berdasarkan Kondisi -->
            <div class="relative inline-block text-left" id="printMenuWrapper">
                <button type="button" 
                        id="printMenuButton"
                        onclick="togglePrintMenu(event)" 
                        class="inline-flex items-center px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 text-blue-700 font-semibold text-xs rounded-xl border border-blue-200 transition shadow-2xs">
                    <span>Opsi Cetak BII</span>
                    <svg id="printMenuChevron" class="w-3.5 h-3.5 ml-1.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="printMenuDropdown" 
                     class="hidden origin-top-right absolute right-0 mt-2 w-72 max-h-[60vh] overflow-y-auto rounded-xl shadow-xl bg-white border border-slate-200 divide-y divide-slate-100 z-50 transition-all custom-scroll">
                    <div class="p-2">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1">Pilihan Urutan Cetak</div>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['sort_by' => 'default'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center px-2.5 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition">
                            <span class="w-2 h-2 rounded-full bg-slate-400 mr-2.5 shrink-0"></span>
                            <div class="flex-1">
                                <div class="font-medium">Cetak Keseluruhan (Standar KIB)</div>
                                <div class="text-[10px] text-slate-400">Urut berdasar nomor register KIB</div>
                            </div>
                        </a>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['sort_by' => 'condition'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center px-2.5 py-2 text-xs text-blue-700 hover:bg-blue-50 rounded-lg transition">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2.5 shrink-0"></span>
                            <div class="flex-1">
                                <div class="font-bold">Cetak Urut Berdasarkan Kondisi Aset</div>
                                <div class="text-[10px] text-blue-500">Kelompok: Baik, Rusak, s.d. Hilang</div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1">Cetak Khusus Per Kondisi</div>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Baik'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Kondisi Baik</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">{{ $conditionSummary['Baik']['count'] }}</span>
                        </a>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Ringan'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-sky-700 hover:bg-sky-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-sky-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Rusak Ringan</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-bold">{{ $conditionSummary['Rusak Ringan']['count'] }}</span>
                        </a>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Sedang'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-amber-700 hover:bg-amber-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Rusak Sedang</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">{{ $conditionSummary['Rusak Sedang']['count'] }}</span>
                        </a>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Rusak Berat'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-rose-700 hover:bg-rose-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-rose-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Rusak Berat</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">{{ $conditionSummary['Rusak Berat']['count'] }}</span>
                        </a>
                        <a href="{{ route('sarpras.bibi.print', array_merge(request()->query(), ['condition' => 'Hilang'])) }}" target="_blank" onclick="closePrintMenu()" class="flex items-center justify-between px-2.5 py-1.5 text-xs text-purple-700 hover:bg-purple-50 rounded-lg transition">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-purple-500 mr-2.5 shrink-0"></span>
                                <span class="font-medium">Hanya Barang Hilang</span>
                            </div>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 font-bold">{{ $conditionSummary['Hilang']['count'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REKAPITULASI KONDISI ASET INVENTARIS (BII) -->
    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Rekapitulasi Kondisi Aset Inventaris
            </h2>
            @if(request('condition'))
                <a href="{{ route('sarpras.bibi.index', request()->except(['condition', 'page'])) }}" class="text-[11px] font-semibold text-blue-600 hover:underline flex items-center gap-1">
                    <span>Tampilkan Keseluruhan (Reset Filter Kondisi)</span>
                    <span>&times;</span>
                </a>
            @else
                <span class="text-[11px] text-slate-400">Klik kartu untuk filter cepat kondisi</span>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <!-- 1. BAIK -->
            <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['condition' => request('condition') === 'Baik' ? null : 'Baik'])) }}"
               class="p-4 rounded-2xl border transition-all relative overflow-hidden group {{ request('condition') === 'Baik' ? 'bg-emerald-600 text-white border-emerald-600 ring-2 ring-emerald-500 shadow-md' : 'bg-white hover:bg-emerald-50/50 text-slate-800 border-slate-200 hover:border-emerald-300 shadow-xs' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase {{ request('condition') === 'Baik' ? 'text-emerald-100' : 'text-emerald-700' }}">1. Kondisi Baik</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold {{ request('condition') === 'Baik' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $conditionSummary['Baik']['percentage'] }}%
                    </span>
                </div>
                <div class="text-2xl font-black mt-2 {{ request('condition') === 'Baik' ? 'text-white' : 'text-emerald-700' }}">
                    {{ number_format($conditionSummary['Baik']['count']) }}
                    <span class="text-xs font-normal opacity-80">Unit</span>
                </div>
                <div class="text-[11px] font-mono mt-1 font-semibold {{ request('condition') === 'Baik' ? 'text-emerald-100' : 'text-slate-600' }}">
                    Rp {{ number_format($conditionSummary['Baik']['value'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] mt-2 pt-2 border-t {{ request('condition') === 'Baik' ? 'border-white/20 text-emerald-100' : 'border-slate-100 text-slate-400' }}">
                    Layak pakai & operasional
                </div>
            </a>

            <!-- 2. RUSAK RINGAN -->
            <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['condition' => request('condition') === 'Rusak Ringan' ? null : 'Rusak Ringan'])) }}"
               class="p-4 rounded-2xl border transition-all relative overflow-hidden group {{ request('condition') === 'Rusak Ringan' ? 'bg-sky-600 text-white border-sky-600 ring-2 ring-sky-500 shadow-md' : 'bg-white hover:bg-sky-50/50 text-slate-800 border-slate-200 hover:border-sky-300 shadow-xs' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase {{ request('condition') === 'Rusak Ringan' ? 'text-sky-100' : 'text-sky-700' }}">2. Rusak Ringan</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold {{ request('condition') === 'Rusak Ringan' ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-800' }}">
                        {{ $conditionSummary['Rusak Ringan']['percentage'] }}%
                    </span>
                </div>
                <div class="text-2xl font-black mt-2 {{ request('condition') === 'Rusak Ringan' ? 'text-white' : 'text-sky-700' }}">
                    {{ number_format($conditionSummary['Rusak Ringan']['count']) }}
                    <span class="text-xs font-normal opacity-80">Unit</span>
                </div>
                <div class="text-[11px] font-mono mt-1 font-semibold {{ request('condition') === 'Rusak Ringan' ? 'text-sky-100' : 'text-slate-600' }}">
                    Rp {{ number_format($conditionSummary['Rusak Ringan']['value'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] mt-2 pt-2 border-t {{ request('condition') === 'Rusak Ringan' ? 'border-white/20 text-sky-100' : 'border-slate-100 text-slate-400' }}">
                    Dapat difungsikan / servis ringan
                </div>
            </a>

            <!-- 3. RUSAK SEDANG -->
            <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['condition' => request('condition') === 'Rusak Sedang' ? null : 'Rusak Sedang'])) }}"
               class="p-4 rounded-2xl border transition-all relative overflow-hidden group {{ request('condition') === 'Rusak Sedang' ? 'bg-amber-600 text-white border-amber-600 ring-2 ring-amber-500 shadow-md' : 'bg-white hover:bg-amber-50/50 text-slate-800 border-slate-200 hover:border-amber-300 shadow-xs' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase {{ request('condition') === 'Rusak Sedang' ? 'text-amber-100' : 'text-amber-700' }}">3. Rusak Sedang</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold {{ request('condition') === 'Rusak Sedang' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $conditionSummary['Rusak Sedang']['percentage'] }}%
                    </span>
                </div>
                <div class="text-2xl font-black mt-2 {{ request('condition') === 'Rusak Sedang' ? 'text-white' : 'text-amber-700' }}">
                    {{ number_format($conditionSummary['Rusak Sedang']['count']) }}
                    <span class="text-xs font-normal opacity-80">Unit</span>
                </div>
                <div class="text-[11px] font-mono mt-1 font-semibold {{ request('condition') === 'Rusak Sedang' ? 'text-amber-100' : 'text-slate-600' }}">
                    Rp {{ number_format($conditionSummary['Rusak Sedang']['value'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] mt-2 pt-2 border-t {{ request('condition') === 'Rusak Sedang' ? 'border-white/20 text-amber-100' : 'border-slate-100 text-slate-400' }}">
                    Perlu perbaikan komponen utama
                </div>
            </a>

            <!-- 4. RUSAK BERAT -->
            <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['condition' => request('condition') === 'Rusak Berat' ? null : 'Rusak Berat'])) }}"
               class="p-4 rounded-2xl border transition-all relative overflow-hidden group {{ request('condition') === 'Rusak Berat' ? 'bg-rose-600 text-white border-rose-600 ring-2 ring-rose-500 shadow-md' : 'bg-white hover:bg-rose-50/50 text-slate-800 border-slate-200 hover:border-rose-300 shadow-xs' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase {{ request('condition') === 'Rusak Berat' ? 'text-rose-100' : 'text-rose-700' }}">4. Rusak Berat</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold {{ request('condition') === 'Rusak Berat' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $conditionSummary['Rusak Berat']['percentage'] }}%
                    </span>
                </div>
                <div class="text-2xl font-black mt-2 {{ request('condition') === 'Rusak Berat' ? 'text-white' : 'text-rose-700' }}">
                    {{ number_format($conditionSummary['Rusak Berat']['count']) }}
                    <span class="text-xs font-normal opacity-80">Unit</span>
                </div>
                <div class="text-[11px] font-mono mt-1 font-semibold {{ request('condition') === 'Rusak Berat' ? 'text-rose-100' : 'text-slate-600' }}">
                    Rp {{ number_format($conditionSummary['Rusak Berat']['value'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] mt-2 pt-2 border-t {{ request('condition') === 'Rusak Berat' ? 'border-white/20 text-rose-100' : 'border-slate-100 text-slate-400' }}">
                    Tidak dapat dipakai / usul afkir
                </div>
            </a>

            <!-- 5. HILANG -->
            <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['condition' => request('condition') === 'Hilang' ? null : 'Hilang'])) }}"
               class="p-4 rounded-2xl border transition-all relative overflow-hidden group {{ request('condition') === 'Hilang' ? 'bg-purple-600 text-white border-purple-600 ring-2 ring-purple-500 shadow-md' : 'bg-white hover:bg-purple-50/50 text-slate-800 border-slate-200 hover:border-purple-300 shadow-xs' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase {{ request('condition') === 'Hilang' ? 'text-purple-100' : 'text-purple-700' }}">5. Hilang</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold {{ request('condition') === 'Hilang' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800' }}">
                        {{ $conditionSummary['Hilang']['percentage'] }}%
                    </span>
                </div>
                <div class="text-2xl font-black mt-2 {{ request('condition') === 'Hilang' ? 'text-white' : 'text-purple-700' }}">
                    {{ number_format($conditionSummary['Hilang']['count']) }}
                    <span class="text-xs font-normal opacity-80">Unit</span>
                </div>
                <div class="text-[11px] font-mono mt-1 font-semibold {{ request('condition') === 'Hilang' ? 'text-purple-100' : 'text-slate-600' }}">
                    Rp {{ number_format($conditionSummary['Hilang']['value'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] mt-2 pt-2 border-t {{ request('condition') === 'Hilang' ? 'border-white/20 text-purple-100' : 'border-slate-100 text-slate-400' }}">
                    Laporan kepolisian / TGR
                </div>
            </a>
        </div>
    </div>

    <!-- Executive Asset Balance Sheet Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-900 to-indigo-900 text-white p-4 rounded-2xl shadow-sm">
            <div class="text-[11px] font-medium text-blue-200 uppercase tracking-wider">Total Nilai Aset Sekolah</div>
            <div class="text-2xl font-black mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
            <div class="text-[11px] text-blue-200 mt-1">{{ number_format($totalItems) }} Unit / Bidang Barang (Semua KIB)</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">Aset Aktif / Operasional</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($activeValue, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">{{ number_format($activeItems) }} Unit tercatat siap operasional</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Status Pencatatan BII</div>
            <div class="text-lg font-bold text-slate-800 mt-1">
                @if(request('condition'))
                    <span class="text-blue-600">Filter: {{ request('condition') }}</span>
                @else
                    <span>Keseluruhan Kondisi</span>
                @endif
            </div>
            <div class="text-[11px] text-slate-500 mt-1">
                Urutan: {{ request('sort_by') === 'condition' ? 'Berdasarkan Kondisi Aset' : 'Standar KIB & Register' }}
            </div>
        </div>
    </div>

    <!-- KIB Breakdown Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($kibSummary as $code => $data)
        <a href="{{ route('sarpras.bibi.index', array_merge(request()->query(), ['kib_type' => request('kib_type') === $code ? null : $code])) }}" class="p-3 rounded-xl border transition {{ request('kib_type') === $code ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 shadow-xs' }}">
            <div class="flex items-center justify-between text-xs font-bold">
                <span>KIB {{ $code }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded {{ request('kib_type') === $code ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $data['count'] }}</span>
            </div>
            <div class="text-[10px] truncate mt-1 opacity-80">{{ $data['title'] }}</div>
            <div class="text-xs font-semibold mt-1">Rp {{ number_format($data['value'], 0, ',', '.') }}</div>
        </a>
        @endforeach
    </div>

    <!-- Filter and Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('sarpras.bibi.index') }}" class="flex flex-wrap items-center gap-2.5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aset, kode, SIPD e-BMD..." class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700 w-52">

                <!-- Filter Kondisi Aset -->
                <select name="condition" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700">
                    <option value="">-- Semua Kondisi (Keseluruhan) --</option>
                    <option value="Baik" {{ request('condition') === 'Baik' ? 'selected' : '' }}>Baik ({{ $conditionSummary['Baik']['count'] }})</option>
                    <option value="Rusak Ringan" {{ request('condition') === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan ({{ $conditionSummary['Rusak Ringan']['count'] }})</option>
                    <option value="Rusak Sedang" {{ request('condition') === 'Rusak Sedang' ? 'selected' : '' }}>Rusak Sedang ({{ $conditionSummary['Rusak Sedang']['count'] }})</option>
                    <option value="Rusak Berat" {{ request('condition') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat ({{ $conditionSummary['Rusak Berat']['count'] }})</option>
                    <option value="Hilang" {{ request('condition') === 'Hilang' ? 'selected' : '' }}>Hilang ({{ $conditionSummary['Hilang']['count'] }})</option>
                </select>

                <!-- Sort By -->
                <select name="sort_by" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700 font-medium">
                    <option value="default" {{ request('sort_by', 'default') === 'default' ? 'selected' : '' }}>Urutan: Keseluruhan (Standar KIB)</option>
                    <option value="condition" {{ request('sort_by') === 'condition' ? 'selected' : '' }}>Urutan: Berdasarkan Kondisi Aset</option>
                </select>

                <select name="kib_type" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700">
                    <option value="">-- Semua KIB (A s.d. F) --</option>
                    @foreach($kibCategories as $kCode => $kInfo)
                    <option value="{{ $kCode }}" {{ request('kib_type') === $kCode ? 'selected' : '' }}>KIB {{ $kCode }} - {{ $kInfo['title'] }}</option>
                    @endforeach
                </select>

                <select name="year" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>

                <select name="funding_source" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 text-slate-700">
                    <option value="">-- Semua Sumber Dana --</option>
                    @foreach(['BOSP Reguler', 'BOSP Kinerja', 'BOPD Pemprov Jabar', 'DAK Fisik', 'Hibah', 'Komite Sekolah'] as $src)
                    <option value="{{ $src }}" {{ request('funding_source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition">Filter</button>
                @if(request()->hasAny(['search', 'condition', 'sort_by', 'kib_type', 'year', 'funding_source']))
                <a href="{{ route('sarpras.bibi.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">No. Register & KIB</th>
                        <th class="px-4 py-3">Nama Barang / Spesifikasi</th>
                        <th class="px-4 py-3">Merk / Tipe / No. Seri</th>
                        <th class="px-4 py-3">Tahun & Sumber Dana</th>
                        <th class="px-4 py-3 text-right">Harga Perolehan</th>
                        <th class="px-4 py-3 text-center">Kondisi Aset</th>
                        <th class="px-4 py-3">Ruangan (KIR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-100 text-blue-800 border border-blue-200">KIB {{ $asset->kib_type }}</span>
                            <div class="font-mono font-bold text-slate-900 mt-1">{{ $asset->code }}</div>
                            <div class="text-[10px] text-slate-500">Reg: <strong class="text-slate-800">{{ $asset->register_range ?? $asset->register_number }}</strong></div>
                            @if($asset->sim_bmd_code)
                            <div class="text-[10px] font-mono text-slate-400">BMD: {{ $asset->sim_bmd_code }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('sarpras.assets.show', $asset->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition">{{ $asset->name }}</a>
                                @if(($asset->quantity ?? 1) > 1)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        {{ $asset->quantity }} {{ $asset->unit }}
                                    </span>
                                @endif
                            </div>
                            @if($asset->specification)
                            <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $asset->specification }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $asset->brand_model ?? '-' }}</div>
                            @if($asset->serial_number)
                            <div class="text-[10px] text-slate-400 font-mono">SN: {{ $asset->serial_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-slate-800">{{ $asset->procurement_year }}</span>
                            <div class="text-[10px] text-slate-500">{{ $asset->funding_source }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                            Rp {{ number_format($asset->price, 0, ',', '.') }}
                            @if(($asset->quantity ?? 1) > 1)
                                <div class="text-[10px] text-slate-400 font-normal">Total: Rp {{ number_format($asset->total_price, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10.5px] font-bold 
                                @if($asset->condition === 'Baik') bg-emerald-100 text-emerald-800 border border-emerald-300
                                @elseif($asset->condition === 'Rusak Ringan') bg-sky-100 text-sky-800 border border-sky-300
                                @elseif($asset->condition === 'Rusak Sedang') bg-amber-100 text-amber-800 border border-amber-300
                                @elseif($asset->condition === 'Rusak Berat') bg-rose-100 text-rose-800 border border-rose-300
                                @elseif($asset->condition === 'Hilang') bg-purple-100 text-purple-800 border border-purple-300
                                @elseif($asset->condition === 'Dihapus/Afkir') bg-slate-800 text-white
                                @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($asset->room)
                            <a href="{{ route('sarpras.kir.index', ['room_id' => $asset->room_id]) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $asset->room->name }}
                            </a>
                            @else
                            <span class="text-slate-400 italic">Belum Ditempatkan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                            Tidak ada data aset inventaris yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $assets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePrintMenu(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const menu = document.getElementById('printMenuDropdown');
        const chevron = document.getElementById('printMenuChevron');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    function closePrintMenu() {
        const menu = document.getElementById('printMenuDropdown');
        const chevron = document.getElementById('printMenuChevron');
        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }

    // Dismiss when clicking outside
    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('printMenuWrapper');
        if (wrapper && !wrapper.contains(event.target)) {
            closePrintMenu();
        }
    });

    // Dismiss on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePrintMenu();
        }
    });
</script>
@endpush
