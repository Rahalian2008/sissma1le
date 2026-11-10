@extends('layouts.app')

@section('title', 'Katalog Kode Barang SIPD e-BMD')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Hierarchy Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-blue-500/10 blur-2xl"></div>
        <div class="absolute right-20 top-0 w-32 h-32 rounded-full bg-indigo-500/10 blur-xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-200 border border-blue-400/30 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Permendagri No. 108 Tahun 2016 &bull; SIPD e-BMD
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Katalog Kodefikasi Barang SIPD e-BMD</h1>
                <p class="text-blue-200/90 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Sistem klasifikasi, penggolongan, dan kodefikasi Barang Milik Daerah terintegrasi khusus untuk inventarisasi sarana prasarana SMA Negeri 1 Lengkong.
                </p>
                <!-- 5-Tier Instansi Hierarchy Badge -->
                <div class="pt-2 flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-slate-300">
                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white">Provinsi Jawa Barat</span>
                    <span>&rsaquo;</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white">Dinas Pendidikan</span>
                    <span>&rsaquo;</span>
                    <span class="px-2 py-0.5 rounded-md bg-amber-400/20 text-amber-300 border border-amber-400/30 font-semibold">{{ $school->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V' }}</span>
                    <span>&rsaquo;</span>
                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white">{{ $school->city ?? 'Kabupaten Sukabumi' }}</span>
                    <span>&rsaquo;</span>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 font-bold">{{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('sarpras.assets.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Entri Aset Baru</span>
                </a>
                <a href="{{ route('sarpras.bibi.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-2xl text-xs font-semibold backdrop-blur-md transition flex items-center gap-2 border border-white/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Buku Induk (BII)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards for KIB & BHP Types -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        @php
            $kibBadges = [
                'A' => ['color' => 'emerald', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'title' => 'KIB A: Tanah', 'prefix' => '1.3.1'],
                'B' => ['color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'title' => 'KIB B: Mesin & TIK', 'prefix' => '1.3.2'],
                'C' => ['color' => 'amber', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'title' => 'KIB C: Gedung', 'prefix' => '1.3.3'],
                'D' => ['color' => 'purple', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'title' => 'KIB D: Jaringan', 'prefix' => '1.3.4'],
                'E' => ['color' => 'pink', 'bg' => 'bg-pink-50', 'border' => 'border-pink-200', 'text' => 'text-pink-700', 'title' => 'KIB E: Buku & Aset', 'prefix' => '1.3.5'],
                'F' => ['color' => 'rose', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-700', 'title' => 'KIB F: KDP Fisik', 'prefix' => '1.3.6'],
                'BHP' => ['color' => 'teal', 'bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'text' => 'text-teal-700', 'title' => 'BHP: Persediaan', 'prefix' => '1.1.7'],
            ];
        @endphp

        @foreach($kibBadges as $key => $meta)
            <a href="{{ route('sarpras.sipd-bmd.index', ['kib' => $key]) }}" class="p-3.5 rounded-2xl border transition-all duration-200 {{ $kib === $key ? 'bg-slate-900 text-white border-slate-900 shadow-md ring-2 ring-blue-500/50' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-800' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold tracking-wider uppercase font-mono {{ $kib === $key ? 'text-blue-400' : 'text-slate-400' }}">{{ $meta['prefix'] }}</span>
                    <span class="w-2 h-2 rounded-full {{ $kib === $key ? 'bg-blue-400' : 'bg-slate-300' }}"></span>
                </div>
                <div class="mt-2 text-xs font-bold truncate">{{ $meta['title'] }}</div>
                <div class="mt-0.5 text-[11px] {{ $kib === $key ? 'text-slate-300' : 'text-slate-500' }}">Kodefikasi Resmi</div>
            </a>
        @endforeach
    </div>

    <!-- Main Content Card with Search & Filters -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Search and Filter Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 space-y-4">
            <form method="GET" action="{{ route('sarpras.sipd-bmd.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="kib" value="{{ $kib }}">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Ketik nama inventaris atau kode SIPD e-BMD (cth: laptop, proyektor, meja siswa, kertas HVS, server ANBK, mikroskop, gedung, buku)..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-2xs">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold shadow-2xs transition">
                        Cari Kode
                    </button>
                    @if($q || $kib !== 'ALL')
                        <a href="{{ route('sarpras.sipd-bmd.index') }}" class="px-3.5 py-2.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-600 rounded-2xl text-xs font-semibold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- KIB Tabs Selection -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-semibold">
                <a href="{{ route('sarpras.sipd-bmd.index', ['kib' => 'ALL', 'q' => $q]) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition {{ $kib === 'ALL' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    Semua Katalog ({{ $totalItems }})
                </a>
                @foreach(['B' => 'KIB B: Mesin & TIK', 'C' => 'KIB C: Gedung', 'E' => 'KIB E: Buku & Aset Lain', 'D' => 'KIB D: Jaringan', 'A' => 'KIB A: Tanah', 'F' => 'KIB F: KDP', 'BHP' => 'BHP: Bahan Habis Pakai (1.1.7)'] as $tabKey => $tabLabel)
                    <a href="{{ route('sarpras.sipd-bmd.index', ['kib' => $tabKey, 'q' => $q]) }}" class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition {{ $kib === $tabKey ? 'bg-blue-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Result Stats -->
        <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs text-slate-600">
            <div>
                Menampilkan <strong class="text-slate-900">{{ count($items) }}</strong> standar barang inventaris SMA dari total {{ $totalItems }} kode SIPD e-BMD
            </div>
            <div class="text-[11px] text-slate-400 hidden sm:block">
                Standar Permendagri No. 108/2016 &bull; Pemprov Jawa Barat
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-700 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No</th>
                        <th class="px-4 py-3.5 w-44">Kode SIPD e-BMD</th>
                        <th class="px-4 py-3.5 w-24">Klasifikasi</th>
                        <th class="px-4 py-3.5">Nama Barang Inventaris & Spesifikasi</th>
                        <th class="px-4 py-3.5 w-28">Kategori</th>
                        <th class="px-4 py-3.5 w-20 text-center">Satuan</th>
                        <th class="px-4 py-3.5 w-44 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $idx => $item)
                        @php
                            $kibColor = match($item['kib']) {
                                'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'B' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'C' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'D' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'E' => 'bg-pink-50 text-pink-700 border-pink-200',
                                'F' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'BHP' => 'bg-teal-50 text-teal-700 border-teal-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold text-blue-700 bg-blue-50/80 px-2.5 py-1 rounded-lg border border-blue-200 text-xs inline-block tracking-tight">
                                    {{ $item['code'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $kibColor }}">
                                    {{ $item['kib'] === 'BHP' ? 'BHP' : 'KIB-'.$item['kib'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item['name'] }}</div>
                                @if(!empty($item['specification']))
                                    <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic">{{ $item['specification'] }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                    {{ $item['category'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-slate-600">
                                {{ $item['unit'] }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="copyCodeToClipboard('{{ $item['code'] }}', '{{ addslashes($item['name']) }}')" title="Salin Kode Barang" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition cursor-pointer flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span>Salin</span>
                                    </button>
                                    @if(($item['kib'] ?? '') === 'BHP')
                                        <a href="{{ route('warehouse.goods.index', ['search' => $item['name']]) }}" title="Gunakan untuk persediaan gudang" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-2xs transition flex items-center gap-1">
                                            <span>Gunakan</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('sarpras.assets.create', ['sim_bmd_code' => $item['code'], 'name' => $item['name'], 'kib_type' => $item['kib'], 'unit' => $item['unit']]) }}" title="Gunakan untuk entri aset" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold shadow-2xs transition flex items-center gap-1">
                                            <span>Gunakan</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="font-bold text-slate-700 text-sm">Tidak ditemukan kode barang yang sesuai kata kunci</div>
                                <div class="text-xs text-slate-400 mt-1">Silakan coba dengan kata kunci lain atau pilih tab KIB yang berbeda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyCodeToClipboard(code, name) {
    navigator.clipboard.writeText(code).then(() => {
        showGlobalToast(`Kode [${code}] untuk ${name} berhasil disalin ke clipboard!`);
    }).catch(() => {
        const dummy = document.createElement('textarea');
        dummy.value = code;
        document.body.appendChild(dummy);
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        showGlobalToast(`Kode [${code}] berhasil disalin!`);
    });
}

function showGlobalToast(message) {
    let toast = document.getElementById('global-copy-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'global-copy-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-300';
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="global-copy-toast-msg"></span>
        `;
        document.body.appendChild(toast);
    }
    document.getElementById('global-copy-toast-msg').textContent = message;
    toast.classList.remove('translate-y-10', 'opacity-0');
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
    }, 3500);
}
</script>
@endsection
