@extends('layouts.app')

@section('title', 'Detail Aktivitas: ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('reports.user_activity') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Laporan Aktivitas Pengguna</span>
        </a>
    </div>

    <!-- User Profile Header Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30">
                        {{ strtoupper(str_replace('_', ' ', $user->role)) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Username: <strong>{{ $user->username }}</strong>
                    @if($user->email) • Email: {{ $user->email }} @endif
                    @if($user->teacher) • NIP: {{ $user->teacher->nip ?: '-' }} @endif
                    @if($user->student) • NISN: {{ $user->student->nisn }} (Kelas {{ $user->student->schoolClass?->name }}) @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-center">
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl min-w-28">
                <div class="text-[10px] font-bold text-emerald-600 uppercase">Frekuensi Masuk</div>
                <div class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ $loginCount }} Kali</div>
            </div>
            <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl min-w-28">
                <div class="text-[10px] font-bold text-indigo-600 uppercase">Total Aktivitas</div>
                <div class="text-xl font-black text-indigo-700 dark:text-indigo-300">{{ $totalActivities }} Aksi</div>
            </div>
        </div>
    </div>

    <!-- Timeline of Audit Logs -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Linimasa Riwayat Aktivitas & Sesi Masuk</h3>
                <p class="text-xs text-slate-500">Catatan kronologis interaksi pengguna pada sistem aplikasi</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                    <tr>
                        <th class="p-3.5 w-12 text-center">No</th>
                        <th class="p-3.5">Waktu Akses</th>
                        <th class="p-3.5">Jenis Tindakan (Action)</th>
                        <th class="p-3.5">Modul / Entitas Terkait</th>
                        <th class="p-3.5">IP Address & Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($logs as $idx => $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3.5 text-center font-bold text-slate-400">
                                {{ $logs->firstItem() + $idx }}
                            </td>
                            <td class="p-3.5 font-medium text-slate-700 dark:text-slate-300">
                                <div>{{ $log->created_at->translatedFormat('d F Y, H:i:s') }} WIB</div>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($log->action === 'LOGIN')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                        MASUK (LOGIN)
                                    </span>
                                @elseif($log->action === 'LOGOUT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-500/20 text-slate-600 dark:text-slate-400">
                                        KELUAR (LOGOUT)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20 font-mono">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-400">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ class_basename($log->auditable_type ?: 'System') }}</div>
                                @if($log->auditable_id)
                                    <span class="text-[10px] text-slate-400">ID: #{{ $log->auditable_id }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-400">
                                <div class="font-mono text-[11px]">{{ $log->ip_address ?: '127.0.0.1' }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-sm" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 45) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-500">
                                Belum ada riwayat aktivitas tercatat untuk pengguna ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
