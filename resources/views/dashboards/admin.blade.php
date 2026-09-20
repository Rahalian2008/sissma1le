@extends('layouts.app')

@section('title', 'Dashboard Administrator - SISWA SMA1LE')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header Admin -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
                    Pusat Kendali Sistem Realtime
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    LIVE MONITOR
                </span>
            </div>
            <h2 class="text-xl sm:text-3xl font-black text-white mt-2">Administrator SMAN 1 Lengkong</h2>
            <p class="text-xs text-slate-300 mt-1">Pantauan presensi terpadu, papan peringkat prestasi, kedisiplinan tata tertib, dan kendali master data.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('attendance.report') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Presensi
            </a>
            <a href="{{ route('master.settings') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Sistem
            </a>
        </div>
    </div>

    @if(Auth::user()->role === 'super_admin')
        <!-- Pusat Kendali Peralihan Peran (Khusus Akun Super Admin) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white border border-indigo-500/30 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-black text-lg">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-white tracking-tight">Peralihan Peran Pengguna (Khusus Super Admin)</h3>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[10px] font-bold border border-emerald-500/30">
                                AKTIF
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">Beralih langsung ke akun peran lain untuk mengecek antarmuka, hak akses, dan alur kerja masing-masing pengguna secara realtime.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-2.5 text-xs">
                @php
                    $roleList = [
                        ['key' => 'super_admin', 'name' => 'Super Admin', 'desc' => 'Rh Aseng (SENKS)'],
                        ['key' => 'admin', 'name' => 'Admin Sekolah', 'desc' => 'Tata Usaha & Ops'],
                        ['key' => 'kepala_sekolah', 'name' => 'Kepala Sekolah', 'desc' => 'Monitoring & Kebijakan'],
                        ['key' => 'guru', 'name' => 'Guru Mapel', 'desc' => 'Presensi KBM & QR'],
                        ['key' => 'wali_kelas', 'name' => 'Wali Kelas', 'desc' => 'Verifikasi & Approval'],
                        ['key' => 'siswa', 'name' => 'Siswa', 'desc' => 'Presensi & Karakter'],
                        ['key' => 'orang_tua', 'name' => 'Orang Tua', 'desc' => 'Pantauan Anak'],
                    ];
                @endphp

                @foreach($roleList as $r)
                    <form action="{{ route('quick.switch') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="role" value="{{ $r['key'] }}">
                        <button type="submit"
                            class="w-full h-full p-3 rounded-2xl text-left transition transform active:scale-95 cursor-pointer border {{ Auth::user()->role === $r['key'] ? 'bg-indigo-600 border-indigo-400 text-white shadow-lg shadow-indigo-600/30 ring-2 ring-indigo-400/40' : 'bg-slate-900/90 hover:bg-slate-800 border-slate-800 hover:border-indigo-500/50 text-slate-200' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-black text-[11px] truncate">{{ $r['name'] }}</span>
                                @if(Auth::user()->role === $r['key'])
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $r['desc'] }}</div>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Master Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 3xl:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs hover-lift transition">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Siswa Terdaftar</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_students'] }}</div>
            <a href="{{ route('master.students') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-1 block">Kelola Data Siswa →</a>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs hover-lift transition">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Pendidik & Role</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_teachers'] }}</div>
            <a href="{{ route('master.teachers') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-1 block">Kelola Guru & Akun →</a>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs hover-lift transition">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Rombongan Belajar</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_classes'] }} Kelas</div>
            <a href="{{ route('master.classes') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline mt-1 block">Kelola Rombel →</a>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs hover-lift transition">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Menunggu Verifikasi</span>
            <div class="text-2xl sm:text-3xl font-black {{ $stats['pending_verifications'] > 0 ? 'text-amber-500' : 'text-slate-900 dark:text-white' }} mt-1">
                {{ $stats['pending_verifications'] }} Berkas
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Presensi & Tata Tertib</span>
        </div>
    </div>

    <!-- 1. REALTIME PRESENSI & DIAGRAM REKAPITULASI (Permintaan User #5) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 mb-6 gap-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <h3 class="text-lg font-black text-slate-800">Realtime Presensi Siswa Hari Ini</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi lengkap kehadiran siswa SMA Negeri 1 Lengkong per tanggal {{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM Y') }}.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('attendance.history') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Riwayat
                </a>
                <a href="{{ route('attendance.report') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition shadow-xs">
                    Laporan Real-Time →
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <!-- Diagram Donut Visual Lingkaran Persentase -->
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle -->
                        <circle cx="50" cy="50" r="40" stroke="#e2e8f0" stroke-width="12" fill="transparent"/>
                        <!-- Hadir Segment -->
                        <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="12" stroke-linecap="round" fill="transparent"
                            stroke-dasharray="251.2"
                            stroke-dashoffset="{{ 251.2 - (251.2 * min(100, $attendanceStats['hadir_pct']) / 100) }}"/>
                    </svg>
                    <div class="absolute flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-slate-800">{{ $attendanceStats['hadir_pct'] }}%</span>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">KEHADIRAN</span>
                    </div>
                </div>
                <div class="text-xs text-slate-500 mt-4">
                    Total Partisipasi: <strong class="text-slate-800">{{ $attendanceStats['hadir'] + $attendanceStats['terlambat'] }} / {{ $attendanceStats['total_students'] }} Siswa</strong>
                </div>
            </div>

            <!-- Kartu 4 Kategori: Hadir, Izin, Sakit, Alpa -->
            <div class="lg:col-span-2 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <!-- Hadir -->
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase text-emerald-800">Hadir</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-2xl font-black text-emerald-900 mt-2">{{ $attendanceStats['hadir'] }}</div>
                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">{{ $attendanceStats['hadir_pct'] }}% dari siswa</div>
                    </div>

                    <!-- Izin -->
                    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase text-sky-800">Izin</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        </div>
                        <div class="text-2xl font-black text-sky-900 mt-2">{{ $attendanceStats['izin'] }}</div>
                        <div class="text-[10px] text-sky-700 font-semibold mt-0.5">{{ $attendanceStats['izin_pct'] }}% dari siswa</div>
                    </div>

                    <!-- Sakit -->
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase text-amber-800">Sakit</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        </div>
                        <div class="text-2xl font-black text-amber-900 mt-2">{{ $attendanceStats['sakit'] }}</div>
                        <div class="text-[10px] text-amber-700 font-semibold mt-0.5">{{ $attendanceStats['sakit_pct'] }}% dari siswa</div>
                    </div>

                    <!-- Alpa / Tanpa Keterangan -->
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase text-rose-800">Alpa</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        </div>
                        <div class="text-2xl font-black text-rose-900 mt-2">{{ $attendanceStats['alpa'] }}</div>
                        <div class="text-[10px] text-rose-700 font-semibold mt-0.5">{{ $attendanceStats['alpa_pct'] }}% belum absen</div>
                    </div>
                </div>

                <!-- Segmented Distribution Bar Diagram -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                    <div class="flex justify-between text-xs font-bold text-slate-700">
                        <span>Diagram Proporsi Kehadiran Hari Ini</span>
                        <span class="text-slate-500 font-mono">{{ $attendanceStats['total_students'] }} Siswa Aktif</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-4 flex overflow-hidden">
                        <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $attendanceStats['hadir_pct'] }}%" title="Hadir: {{ $attendanceStats['hadir_pct'] }}%"></div>
                        <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $attendanceStats['izin_pct'] }}%" title="Izin: {{ $attendanceStats['izin_pct'] }}%"></div>
                        <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $attendanceStats['sakit_pct'] }}%" title="Sakit: {{ $attendanceStats['sakit_pct'] }}%"></div>
                        <div class="bg-rose-500 h-full transition-all duration-500" style="width: {{ $attendanceStats['alpa_pct'] }}%" title="Alpa: {{ $attendanceStats['alpa_pct'] }}%"></div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-[10px] text-slate-500 pt-1">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> Hadir ({{ $attendanceStats['hadir_pct'] }}%)</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-sky-500 inline-block"></span> Izin ({{ $attendanceStats['izin_pct'] }}%)</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-amber-500 inline-block"></span> Sakit ({{ $attendanceStats['sakit_pct'] }}%)</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-rose-500 inline-block"></span> Tanpa Keterangan ({{ $attendanceStats['alpa_pct'] }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1b. Papan Peringkat Presensi: Siswa Paling Sering Alpa, Izin, Sakit -->
    @include('dashboards.partials.attendance_rankings')

    <!-- 2. ANALISIS TATA TERTIB TERPADU (Rangking, Sering Terjadi, Ambang Batas) -->
    @include('dashboards.partials.discipline_analytics')

    <!-- 3. CAPAIAN 7 KEBIASAAN ANAK INDONESIA HEBAT (Diagram & Persentase) -->
    @include('dashboards.partials.habits_analytics')

    <!-- Geofence & Perangkat Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Geofence Status -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Lokasi Geofencing Presensi Selfie</h3>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">Aktif</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Lokasi:</span>
                    <strong class="text-slate-800">{{ $location->name ?? 'SMA Negeri 1 Lengkong' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Koordinat Latitude:</span>
                    <strong class="text-slate-800 font-mono">{{ $location->latitude ?? -7.56845 }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Koordinat Longitude:</span>
                    <strong class="text-slate-800 font-mono">{{ $location->longitude ?? 112.04612 }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Radius Maksimal:</span>
                    <strong class="text-indigo-600 font-bold">{{ $location->radius_meters ?? 100 }} Meter</strong>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">
                *Siswa hanya dapat melakukan presensi selfie jika berada di dalam radius tersebut melalui koordinat GPS realtime.
            </p>
        </div>

        <!-- Perangkat RFID & Kiosk -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Status Perangkat Presensi Fisik</h3>
                <a href="{{ route('attendance.rfid.simulator') }}" class="text-xs font-bold text-indigo-600 hover:underline">Buka Simulator →</a>
            </div>
            <div class="space-y-3">
                @foreach($devices as $device)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                <span>{{ $device->device_name }}</span>
                                <span class="px-1.5 py-0.5 bg-slate-200 text-slate-700 rounded text-[10px] font-mono">{{ $device->device_code }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $device->location_name }} • IP: {{ $device->ip_address }}</div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-full text-[10px]">
                            {{ $device->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Audit Log Keamanan -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Audit Log Aktivitas Sistem Terkini</h3>
                <span class="table-swipe-cue">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Geser Tabel →
                </span>
            </div>
            <span class="text-xs text-slate-400 font-mono self-start sm:self-auto">Realtime Security Logger</span>
        </div>
        <div class="responsive-table-container">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Pengguna</th>
                        <th class="p-3">Aksi</th>
                        <th class="p-3">Objek</th>
                        <th class="p-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentAudits as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3 text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ $log->user->name ?? 'Sistem/IoT' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-md font-mono text-[10px] font-bold whitespace-nowrap">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $log->auditable_type ?? '-' }}</td>
                            <td class="p-3 text-slate-400 font-mono whitespace-nowrap">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
