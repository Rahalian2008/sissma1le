@extends('layouts.app')

@section('title', 'Notifikasi Terpadu - SISWA SMA1LE')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Pusat Notifikasi</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pemberitahuan presensi, prestasi, pembinaan tata tertib, dan sistem</p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>

    <!-- Notification List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
        @forelse($notifications as $notif)
            @php
                $bgClass = $notif->is_read ? '' : 'bg-blue-50/40 dark:bg-blue-950/20';
                $iconColor = match($notif->type) {
                    'ACHIEVEMENT' => 'text-amber-500 bg-amber-50 dark:bg-amber-950/40',
                    'VIOLATION' => 'text-rose-500 bg-rose-50 dark:bg-rose-950/40',
                    'GUIDANCE' => 'text-purple-500 bg-purple-50 dark:bg-purple-950/40',
                    'ATTENDANCE' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/40',
                    'HABIT' => 'text-teal-500 bg-teal-50 dark:bg-teal-950/40',
                    default => 'text-blue-500 bg-blue-50 dark:bg-blue-950/40',
                };
            @endphp
            <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-slate-50/70 dark:hover:bg-slate-800/50 {{ $bgClass }}">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $iconColor }}">
                        @if($notif->type === 'ACHIEVEMENT')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        @elseif($notif->type === 'VIOLATION')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @elseif($notif->type === 'GUIDANCE')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @elseif($notif->type === 'ATTENDANCE')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($notif->type === 'HABIT')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-white">{{ $notif->title }}</h2>
                            @if(!$notif->is_read)
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $notif->message }}</p>
                        <div class="flex items-center space-x-3 text-[11px] text-slate-400 dark:text-slate-500 pt-1">
                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span class="uppercase tracking-wider font-medium text-slate-500">{{ $notif->type }}</span>
                            @if($notif->channel)
                                <span>•</span>
                                <span class="text-slate-400">Via {{ $notif->channel }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="shrink-0 flex items-center space-x-2">
                    <form action="{{ route('notifications.read', $notif) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-xs font-medium rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Buka tautan atau tandai dibaca">
                            @if($notif->link_url)
                                <span class="inline-flex items-center text-blue-600 dark:text-blue-400 font-semibold">
                                    Lihat
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada notifikasi</h3>
                <p class="text-xs text-slate-400 mt-1">Anda sudah melihat semua pembaruan penting saat ini.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
