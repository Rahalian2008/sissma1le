@extends('layouts.app')

@section('title', 'Jejak Audit Sistem (Audit Trail) - SIKATAS SMA1LE')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-slate-900 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </span>
                Jejak Audit Aktivitas (Audit Trail)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekaman jejak digital forensik seluruh operasi manipulasi data, autentikasi, dan pencetakan dokumen di lingkungan sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('settings.backups') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Cadangan Data Sistem &rarr;
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl shadow-xs border border-slate-200">
        <form action="{{ route('settings.audit-logs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cari Keterangan / Pengguna</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Modul Sistem</label>
                <select name="module" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ strtoupper($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Tindakan</label>
                <select name="action" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">Semua Tindakan</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create (Tambah)</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update (Ubah)</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete (Hapus)</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login (Masuk)</option>
                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout (Keluar)</option>
                    <option value="print" {{ request('action') === 'print' ? 'selected' : '' }}>Print (Cetak Dokumen)</option>
                    <option value="backup" {{ request('action') === 'backup' ? 'selected' : '' }}>Backup (Cadangan)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition cursor-pointer">
                    Saring Log
                </button>
                @if(request()->hasAny(['search', 'module', 'action']))
                    <a href="{{ route('settings.audit-logs') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition cursor-pointer">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Waktu & IP</th>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Modul</th>
                        <th class="px-5 py-3.5">Tindakan</th>
                        <th class="px-5 py-3.5">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-slate-900">{{ $log->user_name ?? $log->user?->name ?? 'Sistem Internal' }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 uppercase">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $actionColor = match(strtolower($log->action)) {
                                        'create' => 'bg-emerald-100 text-emerald-800',
                                        'update' => 'bg-amber-100 text-amber-800',
                                        'delete' => 'bg-rose-100 text-rose-800',
                                        'login' => 'bg-blue-100 text-blue-800',
                                        'logout' => 'bg-slate-200 text-slate-700',
                                        'print' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-indigo-100 text-indigo-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $actionColor }}">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 leading-relaxed text-slate-700 max-w-md">
                                <div>{{ $log->description }}</div>
                                @if(!empty($log->new_values) || !empty($log->old_values))
                                    <details class="mt-1.5 text-[11px] text-indigo-600 cursor-pointer">
                                        <summary class="font-semibold hover:underline">Lihat Payload Data</summary>
                                        <div class="mt-1 p-2.5 bg-slate-50 rounded-xl font-mono text-[10px] text-slate-700 border border-slate-200 overflow-x-auto">
                                            @if(!empty($log->old_values))
                                                <div class="text-rose-600 font-bold mb-0.5">Nilai Lama:</div>
                                                <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @endif
                                            @if(!empty($log->new_values))
                                                <div class="text-emerald-600 font-bold mt-1 mb-0.5">Nilai Baru:</div>
                                                <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @endif
                                        </div>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                Belum ada catatan aktivitas yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
