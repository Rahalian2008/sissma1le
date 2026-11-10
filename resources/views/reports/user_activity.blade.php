@extends('layouts.app')

@section('title', 'Laporan Aktivitas & Frekuensi Akses Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Laporan Aktivitas & Frekuensi Masuk Sistem</h1>
                    <p class="text-xs text-slate-500">Pemantauan intensitas penggunaan aplikasi untuk Guru, Tenaga Administrasi, dan Siswa</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('reports.print_user_activity', request()->query()) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Cetak Laporan Resmi</span>
            </a>
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                Kembali ke Menu Laporan
            </a>
        </div>
    </div>

    <!-- 4 Summary Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Login -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Masuk (Login)</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_logins'], 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Kali</span></div>
            <p class="text-[11px] text-slate-500">Kumulatif sesi masuk oleh seluruh pengguna</p>
        </div>

        <!-- Total Aktivitas Sistem -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aktivitas Sistem</span>
                <span class="p-2 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_activities'], 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Aksi</span></div>
            <p class="text-[11px] text-slate-500">Interaksi data & penggunaan fitur aplikasi</p>
        </div>

        <!-- Pengguna Pernah Masuk -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pengguna Aktif</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_users_logged_in'], 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">User</span></div>
            <p class="text-[11px] text-slate-500">Akun yang telah memiliki riwayat login</p>
        </div>

        <!-- Distribusi Akun Terdaftar -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Distribusi Akun</span>
                <span class="p-2 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="text-emerald-600 dark:text-emerald-400">{{ $stats['guru_count'] }} Guru</span> •
                <span class="text-blue-600 dark:text-blue-400">{{ $stats['staff_count'] }} Staff</span> •
                <span class="text-purple-600 dark:text-purple-400">{{ $stats['siswa_count'] }} Siswa</span>
            </div>
            <p class="text-[11px] text-slate-500">Total entitas pengguna dalam database</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
        <!-- Sub-Tabs Filter Peran -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-100 dark:border-slate-800">
            <a href="{{ route('reports.user_activity', array_merge(request()->query(), ['role' => 'all'])) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 {{ $roleCategory === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                👥 Semua Pengguna
            </a>
            <a href="{{ route('reports.user_activity', array_merge(request()->query(), ['role' => 'guru'])) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 {{ $roleCategory === 'guru' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                👨‍🏫 Guru (Pendidik)
            </a>
            <a href="{{ route('reports.user_activity', array_merge(request()->query(), ['role' => 'administrasi'])) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 {{ ($roleCategory === 'administrasi' || $roleCategory === 'staff') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                🏢 Tenaga Administrasi / Staff
            </a>
            <a href="{{ route('reports.user_activity', array_merge(request()->query(), ['role' => 'siswa'])) }}" class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 {{ $roleCategory === 'siswa' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                🎓 Siswa (Peserta Didik)
            </a>
        </div>

        <form action="{{ route('reports.user_activity') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <input type="hidden" name="role" value="{{ $roleCategory }}">

            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Nama / Username / NIP / NISN</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Urutkan Berdasarkan</label>
                <select name="sort" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="login_desc" {{ $sort === 'login_desc' ? 'selected' : '' }}>Frekuensi Masuk Terbanyak</option>
                    <option value="activity_desc" {{ $sort === 'activity_desc' ? 'selected' : '' }}>Total Aktivitas Terbanyak</option>
                    <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama Pengguna (A - Z)</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition text-center">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Daftar Frekuensi Akses & Penggunaan Aplikasi</h3>
                <p class="text-xs text-slate-500">Menampilkan {{ $users->total() }} entitas pengguna berdasarkan filter yang aktif</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                    <tr>
                        <th class="p-3.5 text-center w-12">No</th>
                        <th class="p-3.5">Nama & Identitas Pengguna</th>
                        <th class="p-3.5">Peran / Kategori</th>
                        <th class="p-3.5 text-center">Frekuensi Masuk (Login)</th>
                        <th class="p-3.5 text-center">Total Aktivitas Sistem</th>
                        <th class="p-3.5">Login Terakhir</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $idx => $u)
                        @php
                            $isGuru = $u->teacher || in_array($u->role, ['guru', 'wali_kelas', 'pembina_ekstra', 'koordinator_keagamaan', 'bk']);
                            $isSiswa = $u->student || in_array($u->role, ['siswa', 'sekretaris_kelas', 'sekretaris_ekstra']);
                            $isStaff = ! $isGuru && ! $isSiswa;
                            $lastLog = $u->lastLoginAudit;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3.5 text-center font-bold text-slate-400">
                                {{ $users->firstItem() + $idx }}
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    Username: {{ $u->username }} @if($u->email) • {{ $u->email }} @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if($isGuru)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                        👨‍🏫 Guru ({{ strtoupper(str_replace('_', ' ', $u->role)) }})
                                    </span>
                                @elseif($isSiswa)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-500/20">
                                        🎓 Siswa {{ $u->student?->schoolClass ? '• ' . $u->student->schoolClass->name : '' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-500/20">
                                        🏢 Staff ({{ strtoupper(str_replace('_', ' ', $u->role)) }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="px-3 py-1 rounded-xl text-xs font-black {{ $u->login_count > 0 ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                    {{ $u->login_count }} Kali
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="px-3 py-1 rounded-xl text-xs font-bold {{ $u->total_activities_count > 0 ? 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                    {{ $u->total_activities_count }} Aksi
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-400">
                                @if($lastLog)
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $lastLog->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs" title="{{ $lastLog->user_agent }}">
                                        IP: {{ $lastLog->ip_address ?: '-' }}
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum Pernah Masuk</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('reports.user_activity_detail', $u->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 text-slate-700 dark:text-slate-300 hover:text-indigo-600 text-xs font-bold transition inline-flex items-center gap-1 border border-slate-200 dark:border-slate-700" title="Lihat Detail Riwayat Aktivitas">
                                    <span>Detail Linimasa</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500">
                                Tidak ada data aktivitas pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
