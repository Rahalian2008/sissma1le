@extends('layouts.app')

@section('title', 'Konfigurasi & Integrasi Mesin RFID')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
            <span class="text-xl">✅</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
            <span class="text-xl">⚠️</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-500/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        MODUL HARDWARE & IOT
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $isEnabled ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                        Status Layanan: {{ $isEnabled ? 'AKTIF (Menerima Tap)' : 'NONAKTIF' }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Konfigurasi & Integrasi Mesin RFID
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Pusat tata kelola mesin tap RFID, pendaftaran kartu siswa, monitoring koneksi IoT gerbang sekolah, dan panduan firmware mikrokontroler (ESP32/ESP8266/PC USB).
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Toggle Aktif/Nonaktif Sakelar Utama -->
                <form action="{{ route('attendance.rfid.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-3.5 py-2 rounded-xl font-semibold text-xs transition flex items-center gap-1.5 shadow-xs cursor-pointer {{ $isEnabled ? 'bg-rose-600/90 hover:bg-rose-600 text-white' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}"
                        title="Klik untuk menyalakan/mematikan penerimaan presensi kartu RFID">
                        @if($isEnabled)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Nonaktifkan RFID</span>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Aktifkan RFID</span>
                        @endif
                    </button>
                </form>

                <a href="{{ route('attendance.rfid.simulator') }}" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span>Simulator Tap</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Scanner Gerbang</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mesin Terdaftar</span>
                <span class="text-xl">📟</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                {{ $devices->count() }} <span class="text-xs font-bold text-slate-400">Unit</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-1">
                Gate gerbang, lobby & koridor
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Mesin</span>
                <span class="text-xl">🟢</span>
            </div>
            @php
                $onlineCount = $devices->filter(fn($d) => $d->isOnline())->count();
                $offlineCount = $devices->count() - $onlineCount;
            @endphp
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">
                {{ $onlineCount }} <span class="text-xs font-bold text-slate-400">Online</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-1">
                {{ $offlineCount }} unit offline / belum terhubung
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Siswa Berkartu RFID</span>
                <span class="text-xl">🪪</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">
                {{ $studentsWithRfid }} <span class="text-xs font-bold text-slate-400">/ {{ $totalStudents }}</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-1">
                {{ $studentsWithoutRfid }} siswa belum ada UID
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tap RFID Hari Ini</span>
                <span class="text-xl">📊</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400">
                {{ $todayRfidScans }} <span class="text-xs font-bold text-slate-400">Transaksi</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-1">
                Tercatat di log presensi harian
            </div>
        </div>
    </div>

    <!-- Main 2-Column Content: Devices Management & Quick Pairing -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Device Management Table (2 Cols Wide on Large) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📟</span>
                            <span>Daftar Mesin Presensi RFID Sekolah</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola terminal gate RFID yang terpasang di gerbang dan lorong sekolah.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openAddDeviceModal()" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Mesin</span>
                        </button>
                        
                        <form action="{{ route('master.devices.generate_default') }}" method="POST" onsubmit="return confirm('Atur ulang seluruh konfigurasi mesin ke standar resmi SMAN 1 Lengkong?');">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer" title="Inisialisasi 6 Mesin RFID Bawaan">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Reset Default</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table of Devices -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-black tracking-wider">
                                <th class="p-3 rounded-l-xl">Kode & Nama Mesin</th>
                                <th class="p-3">Lokasi Fisik</th>
                                <th class="p-3">Status IoT</th>
                                <th class="p-3">Terakhir Ping</th>
                                <th class="p-3 text-right rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($devices as $dev)
                                @php
                                    $isOnline = $dev->isOnline();
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $dev->device_name }}</div>
                                        <div class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400 font-black mt-0.5">
                                            ID: {{ $dev->device_code }}
                                        </div>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">
                                        <div>{{ $dev->location_name ?: ($dev->location ?: 'Belum diatur') }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">IP: {{ $dev->ip_address ?: 'DHCP' }}</div>
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $isOnline ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                            {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-500 dark:text-slate-400 text-[11px]">
                                        {{ $dev->last_ping_at ? $dev->last_ping_at->diffForHumans() : 'Belum pernah' }}
                                    </td>
                                    <td class="p-3 text-right space-x-1 whitespace-nowrap">
                                        <button type="button" 
                                            onclick="testPingDevice('{{ $dev->device_code }}')" 
                                            class="px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-300 font-bold text-[10px] transition cursor-pointer"
                                            title="Uji kirim sinyal ping ke mesin">
                                            📡 Ping
                                        </button>
                                        <button type="button" 
                                            onclick='openEditDeviceModal(@json($dev))'
                                            class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[10px] transition cursor-pointer">
                                            ✏️ Edit
                                        </button>
                                        <form action="{{ route('master.devices.destroy', $dev->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mesin {{ $dev->device_code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 text-rose-600 font-bold text-[10px] transition cursor-pointer">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        Belum ada mesin RFID terdaftar. Klik <strong>Reset Default</strong> untuk memasang 6 mesin gate standar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📋</span>
                        <span>Log Transaksi Tap Kartu Hari Ini</span>
                    </h3>
                    <span class="text-xs text-slate-400">{{ count($recentLogs) }} transaksi terbaru</span>
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto">
                    @forelse($recentLogs as $log)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $log->student_name }}</div>
                                <div class="text-[11px] text-slate-400 truncate">
                                    NIS: {{ $log->nis }} • {{ $log->notes }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $log->status === 'HADIR' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
                                    {{ $log->status }}
                                </span>
                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                    {{ substr($log->time, 0, 5) }} WIB
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada riwayat tap kartu RFID hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Quick Pairing Card & Hardware Specs (1 Col) -->
        <div class="space-y-6">
            
            <!-- Quick Pairing Card (Pasangkan Kartu Baru) -->
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-3xl p-6 text-white shadow-lg border border-indigo-800 space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-lg">
                        💳
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white">Pasangkan Kartu ke Siswa</h3>
                        <p class="text-[11px] text-indigo-200">Tap kartu ke reader atau ketik UID fisik.</p>
                    </div>
                </div>

                <form action="{{ route('attendance.rfid.pair_card') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    
                    <div>
                        <label class="block font-bold text-indigo-100 mb-1">UID Kartu RFID:</label>
                        <input type="text" 
                               name="rfid_uid" 
                               id="pair_rfid_uid" 
                               required 
                               placeholder="Contoh: A4B5C6D7 (8 Hex)" 
                               class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-indigo-400/40 rounded-xl font-mono font-bold text-indigo-300 placeholder-indigo-400/50 outline-none focus:border-indigo-400 uppercase tracking-wider text-sm">
                        <span class="text-[10px] text-indigo-300/80 mt-1 block">UID otomatis terbaca jika menggunakan USB reader.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-indigo-100 mb-1">Pilih Siswa Penerima:</label>
                        <select name="student_id" required class="w-full px-3 py-2.5 bg-slate-950/80 border border-indigo-400/40 rounded-xl text-slate-100 text-xs outline-none focus:border-indigo-400">
                            <option value="">-- Cari / Pilih Siswa ({{ count($students) }} Siswa) --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->name }} ({{ $st->schoolClass?->name ?? 'Umum' }}) — UID: {{ $st->rfid_uid ?: 'Belum Ada' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-500 hover:bg-indigo-400 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>💾 Pasangkan Kartu RFID</span>
                    </button>
                </form>

                <div class="pt-3 border-t border-indigo-800/80 flex items-center justify-between text-[11px]">
                    <a href="{{ route('master.students') }}" class="text-indigo-300 hover:text-white font-bold inline-flex items-center gap-1">
                        <span>Buka Master Siswa</span> &rarr;
                    </a>
                    <a href="{{ route('attendance.cards.index') }}" class="text-indigo-300 hover:text-white font-bold inline-flex items-center gap-1">
                        <span>Cetak Kartu Siswa</span> 🪪
                    </a>
                </div>
            </div>

            <!-- API Endpoint Info Box -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 space-y-3 text-xs">
                <span class="px-2.5 py-0.5 rounded-md bg-purple-500/10 text-purple-700 dark:text-purple-300 font-mono text-[10px] font-black uppercase">
                    API Endpoint Perangkat Fisik
                </span>
                
                <div>
                    <span class="text-[11px] text-slate-400 block font-bold">URL Endpoint Target:</span>
                    <div class="p-2.5 mt-1 rounded-xl bg-slate-100 dark:bg-slate-800 font-mono text-[11px] text-indigo-600 dark:text-indigo-400 select-all break-all font-bold">
                        {{ url('/api/attendance/rfid') }}
                    </div>
                </div>

                <div>
                    <span class="text-[11px] text-slate-400 block font-bold">Method & Format:</span>
                    <div class="text-[11px] text-slate-700 dark:text-slate-300 mt-0.5">
                        <strong class="text-emerald-600">POST</strong> • <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800">application/json</code>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] text-slate-400 block font-bold">Format Payload JSON:</span>
                    <pre class="p-2.5 mt-1 rounded-xl bg-slate-950 font-mono text-[11px] text-emerald-400 overflow-x-auto">{
  "rfid_uid": "A4B5C6D7",
  "device_code": "RFID-01"
}</pre>
                </div>
            </div>

        </div>
    </div>

    <!-- Complete Guide & Firmware Accordion Section -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 sm:p-8 space-y-6" x-data="{ activeTab: 'hardware' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-[10px] uppercase tracking-wider">
                    DOKUMENTASI LENGKAP TEKNISI
                </span>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1">
                    Panduan Integrasi Perangkat & Source Code Siap Flash
                </h3>
                <p class="text-xs text-slate-500">Pilih panduan hardware mikrokontroler atau script PC di bawah ini:</p>
            </div>

            <!-- Tab Buttons -->
            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-2xl text-xs font-bold">
                <button type="button" @click="activeTab = 'hardware'" :class="{ 'bg-white dark:bg-slate-900 text-indigo-600 shadow-xs': activeTab === 'hardware', 'text-slate-500 hover:text-slate-900': activeTab !== 'hardware' }" class="px-3 py-1.5 rounded-xl transition cursor-pointer">
                    🔌 1. Skema Kabel (Wiring)
                </button>
                <button type="button" @click="activeTab = 'arduino'" :class="{ 'bg-white dark:bg-slate-900 text-indigo-600 shadow-xs': activeTab === 'arduino', 'text-slate-500 hover:text-slate-900': activeTab !== 'arduino' }" class="px-3 py-1.5 rounded-xl transition cursor-pointer">
                    ⚡ 2. Firmware ESP32/ESP8266
                </button>
                <button type="button" @click="activeTab = 'python'" :class="{ 'bg-white dark:bg-slate-900 text-indigo-600 shadow-xs': activeTab === 'python', 'text-slate-500 hover:text-slate-900': activeTab !== 'python' }" class="px-3 py-1.5 rounded-xl transition cursor-pointer">
                    🐍 3. Skrip USB Reader (PC)
                </button>
            </div>
        </div>

        <!-- TAB 1: Skema Hardware & Pinout RC522 -->
        <div x-show="activeTab === 'hardware'" class="space-y-4">
            <h4 class="text-sm font-black text-slate-900 dark:text-white">A. Skema Sambungan Modul RFID-RC522 ke Mikrokontroler</h4>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Gunakan modul <strong>MFRC522 (13.56 MHz)</strong> yang dihubungkan ke ESP32 atau NodeMCU ESP8266 melalui jalur SPI standar:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Pinout ESP32 -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 block">📌 Pinout Modul ESP32 DevKit:</span>
                    <table class="w-full text-[11px]">
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold w-32">VCC (3.3V)</td><td>Pin 3V3 (Jangan hubungkan ke 5V!)</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">RST</td><td>GPIO 22</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">GND</td><td>GND</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">MISO</td><td>GPIO 19</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">MOSI</td><td>GPIO 23</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">SCK</td><td>GPIO 18</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">SDA (SS)</td><td>GPIO 21 (atau GPIO 5)</td></tr>
                        <tr><td class="font-mono font-bold">Buzzer (+)</td><td>GPIO 4 (GND ke Buzzer -)</td></tr>
                    </table>
                </div>

                <!-- Pinout ESP8266 -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                    <span class="font-bold text-purple-600 dark:text-purple-400 block">📌 Pinout Modul NodeMCU ESP8266:</span>
                    <table class="w-full text-[11px]">
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold w-32">VCC (3.3V)</td><td>Pin 3V3</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">RST</td><td>Pin D3 (GPIO 0)</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">GND</td><td>GND</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">MISO</td><td>Pin D6 (GPIO 12)</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">MOSI</td><td>Pin D7 (GPIO 13)</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">SCK</td><td>Pin D5 (GPIO 14)</td></tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700 py-1"><td class="font-mono font-bold">SDA (SS)</td><td>Pin D4 (GPIO 2)</td></tr>
                        <tr><td class="font-mono font-bold">Buzzer (+)</td><td>Pin D1 (GPIO 5)</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Source Code Arduino IDE (ESP32 / ESP8266) -->
        <div x-show="activeTab === 'arduino'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white">B. Kode Firmware Arduino IDE (ESP32 / ESP8266)</h4>
                    <p class="text-xs text-slate-500">Salin kode ini langsung ke Arduino IDE. Dilengkapi auto-reconnect WiFi dan respon buzzer.</p>
                </div>
                <button type="button" onclick="copyArduinoCode()" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer">
                    <span>📋 Salin Kode Arduino</span>
                </button>
            </div>

            <pre id="arduino-code-block" class="p-4 rounded-2xl bg-slate-950 font-mono text-[11.5px] text-emerald-400 overflow-x-auto leading-relaxed border border-slate-800">
#include &lt;SPI.h&gt;
#include &lt;MFRC522.h&gt;
#if defined(ESP8266)
  #include &lt;ESP8266WiFi.h&gt;
  #include &lt;ESP8266HTTPClient.h&gt;
  #define SS_PIN 2   // D4
  #define RST_PIN 0  // D3
  #define BUZZER_PIN 5 // D1
#elif defined(ESP32)
  #include &lt;WiFi.h&gt;
  #include &lt;HTTPClient.h&gt;
  #define SS_PIN 21
  #define RST_PIN 22
  #define BUZZER_PIN 4
#endif

// ================= KONFIGURASI WIFI & SERVER =================
const char* WIFI_SSID     = "WiFi-Sekolah-Hotspot"; // Nama WiFi
const char* WIFI_PASSWORD = "PasswordWifi";          // Sandi WiFi
const char* SERVER_URL    = "{{ url('/api/attendance/rfid') }}"; // URL SISSMA1LE
const char* DEVICE_CODE   = "RFID-01";              // Kode Mesin Sesuai Database

MFRC522 mfrc522(SS_PIN, RST_PIN);

void beep(int times, int durationMs) {
  for (int i = 0; i &lt; times; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(durationMs);
    digitalWrite(BUZZER_PIN, LOW);
    delay(80);
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  SPI.begin();
  mfrc522.PCD_Init();

  Serial.println("\nMenghubungkan ke WiFi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung! IP: " + WiFi.localIP().toString());
  beep(2, 100); // Tanda berhasil boot & konek WiFi
}

void loop() {
  // Cek apakah ada kartu RFID didekatkan
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) {
    delay(50);
    return;
  }

  // Baca UID Kartu dan konversi ke Format Heksadesimal (Contoh: A4B5C6D7)
  String uidStr = "";
  for (byte i = 0; i &lt; mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] &lt; 0x10) uidStr += "0";
    uidStr += String(mfrc522.uid.uidByte[i], HEX);
  }
  uidStr.toUpperCase();
  Serial.println("\n[TAP KARTU] UID: " + uidStr);

  // Kirim HTTP POST ke Server SISSMA1LE
  if (WiFi.status() == WL_CONNECTED) {
    #if defined(ESP8266)
      WiFiClient client;
      HTTPClient http;
      http.begin(client, SERVER_URL);
    #else
      HTTPClient http;
      http.begin(SERVER_URL);
    #endif

    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    String jsonPayload = "{\"rfid_uid\":\"" + uidStr + "\",\"device_code\":\"" + String(DEVICE_CODE) + "\"}";
    int httpCode = http.POST(jsonPayload);
    String response = http.getString();

    Serial.println("Response Code: " + String(httpCode));
    Serial.println("Response: " + response);

    if (httpCode == 200) {
      // HADIR TEPAT WAKTU: Beep 1x Pendek Mantap
      beep(1, 200);
    } else if (httpCode == 422) {
      // TERLAMBAT / SUDAH SELESAI / PULANG: Beep 2x Cepat
      beep(2, 120);
    } else {
      // KARTU TIDAK TERDAFTAR / DITOLAK: Beep Panjang 3x
      beep(3, 400);
    }
    http.end();
  }

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  delay(1200); // Cegah double tap seketika
}</pre>
        </div>

        <!-- TAB 3: Skrip Python USB RFID Reader -->
        <div x-show="activeTab === 'python'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white">C. Skrip Python untuk USB RFID Reader (Colok ke Laptop/PC Gerbang)</h4>
                    <p class="text-xs text-slate-500">Cocok jika sekolah memakai USB RFID Card Reader tipe Plug & Play yang dicolokkan ke laptop petugas piket.</p>
                </div>
                <button type="button" onclick="copyPythonCode()" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer">
                    <span>📋 Salin Skrip Python</span>
                </button>
            </div>

            <pre id="python-code-block" class="p-4 rounded-2xl bg-slate-950 font-mono text-[11.5px] text-purple-300 overflow-x-auto leading-relaxed border border-slate-800">
# SISSMA1LE - USB RFID Reader Auto Attendance Script (Python)
# Install dependencies: pip install requests
import requests
import time

SERVER_URL = "{{ url('/api/attendance/rfid') }}"
DEVICE_CODE = "RFID-01"

print("=" * 60)
print(" SISSMA1LE RFID USB SCANNER GATE CLIENT")
print(f" Target Server: {SERVER_URL}")
print(f" Device ID: {DEVICE_CODE}")
print(" Siap menerima tap kartu siswa...")
print("=" * 60)

while True:
    try:
        # USB RFID Reader bertindak seperti keyboard mengetik UID + ENTER
        uid = input("\n[STANDBY] Silakan Tempelkan Kartu RFID: ").strip().upper()
        if not uid:
            continue

        print(f"-> Memproses UID: {uid} ke server...")
        payload = {
            "rfid_uid": uid,
            "device_code": DEVICE_CODE
        }

        res = requests.post(SERVER_URL, json=payload, headers={"Accept": "application/json"}, timeout=5)
        data = res.json()

        if res.status_code == 200:
            student = data.get("student", {})
            print(f"✅ BERHASIL: {student.get('name')} ({student.get('class')}) - {data.get('message')}")
        else:
            print(f"⚠️ PERHATIAN: {data.get('message')}")

    except KeyboardInterrupt:
        print("\nProgram dihentikan.")
        break
    except Exception as e:
        print(f"❌ Error Koneksi: {e}")
        time.sleep(1)</pre>
        </div>

    </div>
</div>

<!-- Modal Tambah / Edit Device Mesin RFID -->
<div id="deviceModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 id="modalTitle" class="text-base font-bold text-slate-900 dark:text-white">Tambah Mesin RFID Baru</h3>
            <button type="button" onclick="closeDeviceModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form id="deviceForm" action="{{ route('master.devices.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="device_type" value="RFID">

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Mesin (Unik):</label>
                <input type="text" name="device_code" id="dev_code" required placeholder="Contoh: RFID-07 atau GATE-02" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono font-bold uppercase text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mesin:</label>
                <input type="text" name="device_name" id="dev_name" required placeholder="Contoh: Gate RFID 07 - Pos Satpam Depan" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Pemasangan Fisik:</label>
                <input type="text" name="location_name" id="dev_location" placeholder="Contoh: Gerbang Utama A / Lobby Barat" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat IP Mesin (Opsional):</label>
                <input type="text" name="ip_address" id="dev_ip" placeholder="Contoh: 192.168.1.107 atau DHCP" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-slate-800 dark:text-slate-200">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-xs transition">
                    Simpan Mesin RFID
                </button>
                <button type="button" onclick="closeDeviceModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddDeviceModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Mesin RFID Baru';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('deviceForm').action = "{{ route('master.devices.store') }}";
        document.getElementById('dev_code').value = '';
        document.getElementById('dev_code').readOnly = false;
        document.getElementById('dev_name').value = '';
        document.getElementById('dev_location').value = '';
        document.getElementById('dev_ip').value = '';
        document.getElementById('deviceModal').classList.remove('hidden');
    }

    function openEditDeviceModal(device) {
        document.getElementById('modalTitle').innerText = 'Edit Mesin: ' + device.device_code;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('deviceForm').action = "{{ url('master/devices') }}/" + device.id;
        document.getElementById('dev_code').value = device.device_code;
        document.getElementById('dev_code').readOnly = true;
        document.getElementById('dev_name').value = device.device_name;
        document.getElementById('dev_location').value = device.location_name || device.location || '';
        document.getElementById('dev_ip').value = device.ip_address || '';
        document.getElementById('deviceModal').classList.remove('hidden');
    }

    function closeDeviceModal() {
        document.getElementById('deviceModal').classList.add('hidden');
    }

    async function testPingDevice(deviceCode) {
        try {
            const res = await fetch("{{ route('api.attendance.device_ping') }}?device_code=" + encodeURIComponent(deviceCode));
            const data = await res.json();
            if (data.success) {
                alert("✅ Sinyal Ping Diterima!\nMesin: " + deviceCode + "\nWaktu Server: " + data.server_time + "\nStatus: ONLINE");
                location.reload();
            } else {
                alert("⚠️ Gagal Ping: " + (data.message || 'Unknown error'));
            }
        } catch (e) {
            alert("❌ Terjadi kesalahan saat mengirim ping.");
        }
    }

    function copyArduinoCode() {
        const text = document.getElementById('arduino-code-block').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("✨ Kode Firmware Arduino IDE berhasil disalin ke clipboard!");
        });
    }

    function copyPythonCode() {
        const text = document.getElementById('python-code-block').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("✨ Skrip Python USB RFID Reader berhasil disalin ke clipboard!");
        });
    }
</script>
@endpush
@endsection
