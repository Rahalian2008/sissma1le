@extends('layouts.app')

@section('title', 'Cadangan Basis Data (Database Backup) - SIKATAS SMA1LE')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('settings.audit-logs') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                    &larr; Jejak Audit Sistem
                </a>
            </div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-slate-900 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                </span>
                Cadangan & Pemulihan Basis Data (Backup & Recovery)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Amankan snapshot seluruh tabel kesiswaan, kepegawaian, keuangan, persuratan, dan inventaris secara teratur.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('settings.backups.create') }}" method="POST" onsubmit="return confirm('Buat snapshot cadangan basis data sistem sekarang?')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Snapshot Cadangan Baru
                </button>
            </form>
        </div>
    </div>

    <!-- Storage Info Card -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6 text-xs text-indigo-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="font-bold text-slate-900 text-sm">Direktori Penyimpanan Snapshot:</h2>
            <p class="font-mono text-[11px] text-slate-600 bg-white/60 px-3 py-1.5 rounded-lg border border-indigo-100 inline-block">storage/app/backups/</p>
            <p class="text-slate-600">Seluruh file cadangan terisolasi secara aman di server lokal dan siap diunduh ke media penyimpanan eksternal.</p>
        </div>
        <div class="shrink-0 text-right">
            <div class="text-2xl font-black text-indigo-700">{{ count($backups) }}</div>
            <div class="text-[11px] font-bold text-slate-500">Berkas Tersimpan</div>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-900">Daftar Arsip Cadangan Sistem</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Berkas Cadangan</th>
                        <th class="px-5 py-3.5">Ukuran Berkas</th>
                        <th class="px-5 py-3.5">Waktu Penciptaan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-mono font-bold text-slate-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16m-8 4v6m-3-3l3 3 3-3"></path></svg>
                                    {{ $backup['filename'] }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-600">
                                {{ $backup['size'] }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $backup['created_at'] }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('settings.backups.download', $backup['filename']) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold rounded-xl transition cursor-pointer text-xs">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Unduh File
                                </a>
                                <form action="{{ route('settings.backups.destroy', $backup['filename']) }}" method="POST" onsubmit="return confirm('Hapus berkas cadangan ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl transition cursor-pointer text-xs" title="Hapus Berkas Cadangan">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                Belum ada berkas cadangan database. Klik tombol "Buat Snapshot Cadangan Baru" di atas untuk membuat cadangan pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
