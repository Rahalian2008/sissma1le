@extends('layouts.app')

@section('title', 'Adm. Persuratan & Tata Naskah - Tata Usaha')

@section('content')
<div class="space-y-6">
    @include('tu.partials.nav')

    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 sm:p-8 text-white shadow-xl shadow-indigo-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-indigo-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Sub Menu Administrasi Ketatausahaan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Persuratan & Tata Naskah Dinas</h1>
                <p class="text-indigo-100 text-sm mt-1 max-w-xl">
                    Pengelolaan agenda surat masuk, surat keluar, lembar disposisi digital berjenjang, penomoran kode klasifikasi dinas, SPPD perjalanan dinas, notula rapat dinas, dan buku tamu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('letters.incoming.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 font-bold text-xs shadow-lg shadow-black/10 hover:bg-indigo-50 transition-all">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Surat Masuk</span>
                </a>
                <a href="{{ route('letters.outgoing.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Surat Keluar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik Persuratan Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Surat Masuk</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($incomingCount) }}</div>
            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-0.5 font-medium">Buku Agenda Masuk</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Surat Keluar</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($outgoingCount) }}</div>
            <div class="text-[11px] text-purple-600 dark:text-purple-400 mt-0.5 font-medium">Buku Agenda Keluar</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Disposisi Berjalan</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($pendingDispositionsCount) }}</div>
            <div class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 font-medium">Belum Selesai</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">SPPD Dinas</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($sppdCount) }}</div>
            <div class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5 font-medium">Perjalanan Dinas</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Arsip Digital</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($activeArchivesCount) }}</div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">Dokumen Aktif</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tamu Hari Ini</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($guestsTodayCount) }}</div>
            <div class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5 font-medium">Buku Tamu Resepsionis</div>
        </div>
    </div>

    <!-- Quick Access Feature Grid -->
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Modul Layanan Persuratan & Tata Naskah Terpadu</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
            <a href="{{ route('letters.incoming.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Surat Masuk</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Agenda & Scan</span>
            </a>
            <a href="{{ route('letters.outgoing.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Surat Keluar</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Penomoran Otomatis</span>
            </a>
            <a href="{{ route('dispositions.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Disposisi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Digital Berjenjang</span>
            </a>
            <a href="{{ route('letters.expeditions.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Ekspedisi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Tanda Terima</span>
            </a>
            <a href="{{ route('letters.sppds.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">SPPD Dinas</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Perjalanan Dinas</span>
            </a>
            <a href="{{ route('letters.generator.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-pink-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-pink-500/10 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Generator</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Template Naskah</span>
            </a>
            <a href="{{ route('letters.meeting-notes.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Notula Rapat</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Daftar Hadir</span>
            </a>
            <a href="{{ route('archives.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Arsip Dinamis</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Digital & QR</span>
            </a>
            <a href="{{ route('letters.classifications.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Kode Surat</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Klasifikasi Naskah</span>
            </a>
            <a href="{{ route('guests.index') }}" class="group p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-cyan-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-800 dark:text-white">Buku Tamu</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kiosk Mandiri</span>
            </a>
        </div>
    </div>

    <!-- Dua Kolom: Surat Masuk & Surat Keluar Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Surat Masuk Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    Agenda Surat Masuk Terkini
                </h3>
                <a href="{{ route('letters.incoming.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentIncoming as $in)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-[11px] font-mono text-indigo-600 dark:text-indigo-400 font-bold">{{ $in->letter_number }}</div>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-white truncate mt-0.5">{{ $in->subject }}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Dari: {{ $in->sender }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::parse($in->received_date)->translatedFormat('d M Y') }}</span>
                        <a href="{{ route('letters.incoming.show', $in) }}" class="inline-block mt-1 text-[11px] font-bold text-blue-600 hover:underline">Detail</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada surat masuk yang tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Surat Keluar Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    Agenda Surat Keluar Terkini
                </h3>
                <a href="{{ route('letters.outgoing.index') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($recentOutgoing as $out)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-[11px] font-mono text-purple-600 dark:text-purple-400 font-bold">{{ $out->letter_number }}</div>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-white truncate mt-0.5">{{ $out->subject }}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Tujuan: {{ $out->recipient }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::parse($out->letter_date)->translatedFormat('d M Y') }}</span>
                        <a href="{{ route('letters.outgoing.show', $out) }}" class="inline-block mt-1 text-[11px] font-bold text-purple-600 hover:underline">Detail</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada surat keluar yang tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

