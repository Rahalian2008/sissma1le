@extends('layouts.app')

@section('title', 'Live Monitor Pengawas - ' . $exam->title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('cbt.exams.show', $exam->id) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Detail Ujian
                </a>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </span>
                Live Monitor Pengawas: {{ $exam->title }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pantau aktivitas peserta ujian, durasi waktu pengerjaan, dan deteksi pelanggaran anti-cheat secara real-time.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh Data
            </button>
            <div class="px-3 py-2 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-xl text-xs font-mono font-bold text-indigo-700 dark:text-indigo-300">
                TOKEN: <span class="tracking-widest">{{ $exam->token ?? '-' }}</span>
            </div>
        </div>
    </div>
        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Sesi Terdaftar</div>
                <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $stats['total'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Siswa dalam rombel</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">Sedang Mengerjakan</div>
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['in_progress'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Aktif di dalam tes</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Telah Selesai</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['submitted'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Jawaban terkirim</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-rose-300 dark:border-rose-900/60 bg-rose-50/30 dark:bg-rose-950/10 shadow-sm">
                <div class="text-xs font-semibold text-rose-600 dark:text-rose-400">Terblokir Otomatis</div>
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $stats['blocked'] }}</div>
                <div class="text-[11px] text-rose-500/80 mt-0.5">Melanggar anti-cheat</div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum Memulai</div>
                <div class="text-2xl font-black text-slate-600 dark:text-slate-300 mt-1">{{ $stats['not_started'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Belum input token</div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        <!-- TABEL MONITORING -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden" x-data="{ search: '', filterStatus: '' }">
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="font-bold text-slate-800 dark:text-white text-base">Aktivitas Siswa Saat Ini</h2>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Cari nama / NIS..." class="w-48 sm:w-64 text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 pl-8 pr-3 py-2 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <select x-model="filterStatus" class="text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 py-2 px-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="in_progress">Sedang Mengerjakan</option>
                        <option value="submitted">Selesai</option>
                        <option value="blocked">Terblokir</option>
                        <option value="not_started">Belum Mulai</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="py-3 px-4">Peserta & Kelas</th>
                            <th class="py-3 px-4">Akun CBT</th>
                            <th class="py-3 px-4">Lokasi & IP</th>
                            <th class="py-3 px-4">Mulai / Sisa Waktu</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Pelanggaran</th>
                            <th class="py-3 px-4 text-right">Aksi Pengawas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($sessions as $session)
                            @php
                                $student = $session->student;
                                $searchKey = strtolower(($student?->name ?? '').' '.($student?->nis ?? ''));
                                $statusKey = $session->is_blocked ? 'blocked' : $session->status;
                            @endphp
                            <tr x-show="(search === '' || '{{ $searchKey }}'.includes(search.toLowerCase())) && (filterStatus === '' || '{{ $statusKey }}' === filterStatus)" class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $student?->name ?? 'Siswa #'.$session->student_id }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span>NIS: {{ $student?->nis ?? '-' }}</span>
                                        <span>•</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-semibold">{{ $student?->schoolClass?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    <div class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $session->cbt_username ?? '-' }}</div>
                                    <div class="text-slate-400">PIN: {{ $session->cbt_password ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] text-slate-600 dark:text-slate-300">
                                    <div>IP: <span class="font-mono">{{ $session->student_ip ?? 'Belum login' }}</span></div>
                                    @if($session->student_distance_meters !== null)
                                        <div class="mt-0.5 {{ $session->student_distance_meters > ($exam->geofence_radius_meters ?? 100) ? 'text-rose-600 font-bold' : 'text-emerald-600' }}">
                                            📍 Radius: {{ round($session->student_distance_meters) }} m
                                        </div>
                                    @else
                                        <div class="text-slate-400 mt-0.5">-</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[11px]">
                                    @if($session->started_at)
                                        <div class="text-slate-700 dark:text-slate-300">{{ $session->started_at->format('H:i:s') }}</div>
                                        <div class="font-mono text-xs font-bold {{ $session->remaining_seconds < 300 ? 'text-rose-600' : 'text-indigo-600 dark:text-indigo-400' }}">
                                            Sisa: {{ gmdate('H:i:s', max(0, $session->remaining_seconds)) }}
                                        </div>
                                    @else
                                        <span class="text-slate-400">Belum masuk</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($session->is_blocked)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3.5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Terblokir ({{ $session->violation_count }}x)
                                        </span>
                                    @elseif($session->status === 'submitted')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                                            ✓ Selesai
                                        </span>
                                    @elseif($session->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            Mengerjakan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                            Belum Mulai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($session->violation_count > 0)
                                        <span class="px-2 py-0.5 rounded-md font-bold text-xs bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                                            {{ $session->violation_count }}x
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($session->is_blocked)
                                            <form method="POST" action="{{ route('cbt.exams.unblock', [$exam->id, $session->id]) }}" onsubmit="return confirm('Buka blokir ujian untuk siswa {{ addslashes($student?->name) }}?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-sm">
                                                    Buka Blokir
                                                </button>
                                            </form>
                                        @endif

                                        @if($session->violations->count() > 0)
                                            <button type="button" onclick="alert('LOG PELANGGARAN SISWA: {{ addslashes($student?->name) }}\n\n@foreach($session->violations as $v)[{{ $v->violation_time?->format("H:i:s") }}] {{ $v->violation_type }}: {{ addslashes($v->description) }}\n@endforeach')" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Lihat detail log pelanggaran">
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada siswa yang terdaftar dalam sesi ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
