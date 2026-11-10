@extends('layouts.app')

@section('title', 'Dashboard Wakasek Bid. Sarpras - Infrastruktur & IoT Presensi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Wakasek Sarpras -->
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full text-xs font-bold tracking-wide">
                    BIDANG SARANA & PRASARANA (SARPRAS)
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[11px] font-mono flex items-center gap-1.5 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    INFRASTRUKTUR PRESENSI IOT AKTIF
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Wakasek Bid. Sarpras</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Supervisi sarana dan teknologi presensi: pemantauan mesin IoT RFID, Scanner QR Code gerbang, geofence radius sekolah, dan fasilitas pendukung kegiatan siswa.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('kiosk.index') }}" target="_blank" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-teal-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Layar Kiosk TV Display
            </a>
            <a href="{{ route('attendance.history') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 border border-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Log Scan Hari Ini
            </a>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Perangkat IoT Terpasang</span>
                <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                    📟
                </div>
            </div>
            <div class="text-3xl font-black text-teal-600 dark:text-teal-400 mt-2">{{ $devices->count() }} <span class="text-xs font-normal text-slate-400">Unit</span></div>
            <p class="text-xs text-slate-400 mt-1">{{ $rfidDevicesCount }} Mesin RFID • {{ $qrDevicesCount }} Scanner QR</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Perangkat Online / Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    🟢
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $activeDevicesCount }} <span class="text-xs font-normal text-slate-400">Siap Pakai</span></div>
            <p class="text-xs text-slate-400 mt-1">Status koneksi hardware normal</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Aktivitas Scan Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                    ⚡
                </div>
            </div>
            <div class="text-3xl font-black text-sky-600 dark:text-sky-400 mt-2">{{ $rfidScansToday + $qrScansToday + $selfieScansToday }} <span class="text-xs font-normal text-slate-400">Tap</span></div>
            <p class="text-xs text-slate-400 mt-1">{{ $rfidScansToday }} RFID • {{ $qrScansToday }} QR • {{ $selfieScansToday }} Selfie</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Fasilitas Ekstrakurikuler</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    ⚽
                </div>
            </div>
            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-2">{{ $extracurricularsCount }} <span class="text-xs font-normal text-slate-400">Unit Kegiatan</span></div>
            <p class="text-xs text-slate-400 mt-1">Pemanfaatan lapangan & ruang kegiatan</p>
        </div>
    </div>

    <!-- Status Daftar Perangkat Scanner IoT Sekolah -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>📡 Status & Lokasi Mesin Presensi IoT</span>
                </h2>
                <p class="text-xs text-slate-400">Daftar mesin tap kartu RFID dan pembaca QR barcode di lingkungan sekolah</p>
            </div>
            <span class="px-3 py-1 bg-teal-100 dark:bg-teal-950/80 text-teal-800 dark:text-teal-300 rounded-full text-xs font-bold">
                {{ $devices->count() }} Perangkat Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3">Nama Perangkat</th>
                        <th class="p-3">Kode / ID</th>
                        <th class="p-3">Tipe Scanner</th>
                        <th class="p-3">Titik Lokasi</th>
                        <th class="p-3">IP Address</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($devices as $dev)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3">
                                <div class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $dev->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $dev->device_name }}
                                </div>
                            </td>
                            <td class="p-3 font-mono font-semibold text-slate-600 dark:text-slate-400">
                                {{ $dev->device_code ?? '-' }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ str_contains(strtoupper($dev->device_type ?? ''), 'RFID') ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : 'bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300' }}">
                                    {{ $dev->device_type }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">
                                {{ $dev->location_name ?? $dev->location ?? 'Gerbang Sekolah' }}
                            </td>
                            <td class="p-3 font-mono text-slate-500 text-[11px]">
                                {{ $dev->ip_address ?? '192.168.1.x' }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $dev->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                    {{ $dev->is_active ? 'Online / Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada perangkat scanner terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 Columns: Geofence Info & Fasilitas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Geofence & Lokasi Presensi -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span>📍 Titik Koordinat & Geofence Sekolah</span>
            </h3>
            <p class="text-xs text-slate-400">Batas radius GPS untuk presensi mobile & verifikasi kehadiran guru/siswa</p>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Nama Lokasi Utama:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $location->name ?? 'SMAN 1 Lengkong - Kampus Utama' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Koordinat Latitude:</span>
                    <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $location->latitude ?? '-7.032150' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Koordinat Longitude:</span>
                    <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $location->longitude ?? '106.912400' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Radius Kehadiran:</span>
                    <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-bold dark:bg-teal-950 dark:text-teal-300">{{ $location->radius_meters ?? '150' }} Meter</span>
                </div>
            </div>
        </div>

        <!-- Pemeliharaan & Integrasi Sarpras -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Supervisi Prasarana Sekolah</h3>
            <p class="text-xs text-slate-400">Pemanfaatan ruang & fasilitas sarana pendukung</p>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs">
                    <div class="text-[11px] text-slate-400 font-medium">Masjid / Musholla</div>
                    <div class="font-bold text-slate-800 dark:text-slate-100 mt-1">Kegiatan Ibadah Harian</div>
                    <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">Dzuhur, Dhuha & Keputrian</div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs">
                    <div class="text-[11px] text-slate-400 font-medium">Lobby & Hall Utama</div>
                    <div class="font-bold text-slate-800 dark:text-slate-100 mt-1">Layar Display TV Kiosk</div>
                    <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Live Monitoring Kehadiran</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
