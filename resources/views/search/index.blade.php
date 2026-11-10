@extends('layouts.app')

@section('title', 'Pencarian Global - SIKATAS SMA1LE')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Search Bar Hero -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
        <div class="max-w-2xl mx-auto text-center space-y-3 mb-6">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Pencarian Terpadu SIKATAS
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Cari Seluruh Data Administrasi Sekolah</h1>
            <p class="text-xs text-slate-500">Ketik nama siswa, NIS/NISN, nama guru/PTK, NIP, nomor surat dinas, kode inventaris aset, barang gudang, atau arsip digital.</p>
        </div>

        <form action="{{ route('global.search') }}" method="GET" class="max-w-3xl mx-auto">
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="q" value="{{ $query }}" placeholder="Ketik kata kunci pencarian..." autofocus required class="w-full pl-11 pr-32 py-3.5 bg-slate-50 hover:bg-slate-100/60 focus:bg-white border-2 border-slate-200 focus:border-indigo-600 rounded-2xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-hidden transition shadow-xs">
                <button type="submit" class="absolute right-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Cari Data
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results Section -->
    @if(!empty($query))
        <div class="flex items-center justify-between px-2">
            <div class="text-xs font-semibold text-slate-600">
                Menampilkan hasil pencarian untuk: <span class="font-bold text-slate-900">"{{ $query }}"</span>
            </div>
            <div class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                {{ count($results) }} Hasil Ditemukan
            </div>
        </div>

        @if(count($results) > 0)
            @php
                $grouped = collect($results)->groupBy('category');
            @endphp

            <div class="space-y-6">
                @foreach($grouped as $category => $items)
                    <div class="bg-white rounded-3xl shadow-xs border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ $category }}</h2>
                            </div>
                            <span class="text-[11px] font-bold text-slate-400">{{ count($items) }} entitas</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($items as $item)
                                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ $item['url'] }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition">
                                                {{ $item['title'] }}
                                            </a>
                                        </div>
                                        <p class="text-xs text-slate-500 font-mono">
                                            {{ $item['subtitle'] }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if(isset($item['buku_induk_url']))
                                            <a href="{{ $item['buku_induk_url'] }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl transition">
                                                Buku Induk
                                            </a>
                                        @endif
                                        <a href="{{ $item['url'] }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl transition">
                                            Buka Rincian &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center shadow-xs border border-slate-200 space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl font-bold">
                    🔍
                </div>
                <h2 class="text-base font-bold text-slate-800">Tidak ada data yang cocok</h2>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Kami tidak menemukan entitas yang sesuai dengan kata kunci <span class="font-bold text-slate-700">"{{ $query }}"</span>. Silakan periksa kembali ejaan atau coba kata kunci yang lebih spesifik.
                </p>
            </div>
        @endif
    @else
        <!-- Empty / Guide State -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>🎓</span> Peserta Didik
                </div>
                <p class="text-xs text-slate-500">Cari siswa berdasarkan nama lengkap, NIS, atau NISN nasional untuk mengakses buku induk digital.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>👔</span> Pendidik & Tenaga Kependidikan
                </div>
                <p class="text-xs text-slate-500">Cari guru dan staf tata usaha melalui nama lengkap atau NIP untuk mengakses DUK dan profil kepegawaian.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>✉️</span> Persuratan & Disposisi
                </div>
                <p class="text-xs text-slate-500">Cari surat dinas masuk dan keluar berdasarkan perihal, instansi pengirim, atau nomor registrasi surat.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>🏢</span> Sarpras & KIB-KIR (SIPD e-BMD)
                </div>
                <p class="text-xs text-slate-500">Cari aset inventaris berdasarkan kode barang standar SIPD e-BMD (Permendagri No. 108/2016), KIB A-F, dan lokasi KIR ruangan.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>📦</span> Gudang & Barang Masuk
                </div>
                <p class="text-xs text-slate-500">Cari barang habis pakai, penerimaan logistik, dan kartu kendali stok persediaan sekolah.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>📁</span> Kearsipan Digital
                </div>
                <p class="text-xs text-slate-500">Cari berkas dokumen arsip statis dan dinamis berdasarkan nomor berkas atau kategori.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="text-indigo-600 font-bold text-xs flex items-center gap-1.5">
                    <span>🤝</span> Buku Tamu Digital
                </div>
                <p class="text-xs text-slate-500">Cari data pengunjung dan tamu sekolah melalui nomor tiket, nama lengkap, atau instansi asal.</p>
            </div>
        </div>
    @endif
</div>
@endsection
