@extends('layouts.app')

@section('title', 'Laporan Presensi Real-Time - SISWA SMA1LE')

@section('content')
<div class="space-y-5 w-full max-w-full">
    @include('attendance.partials.nav')
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    MONITORING REAL-TIME
                </span>
                <span class="text-xs text-slate-400">Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Laporan Presensi Siswa Terpadu</h1>
            <p class="text-xs text-slate-300 mt-1">Rekapitulasi kehadiran terpadu dari Selfie Kamera, Barcode Scanner, Kartu RFID, Face Recognition, dan Presensi Manual.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if(!Auth::user()->isStudent())
            <a href="{{ route('reports.print.class_recap', ['start_date' => $date, 'end_date' => $date]) }}" target="_blank" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md shadow-sky-900/30 flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>📊 Cetak Rekap Rombel</span>
            </a>
            <button type="button" onclick="openPrintAttendanceModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-900/30 flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Cetak Laporan Resmi</span>
            </button>
            @endif
            <a href="{{ route('attendance.history') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition">
                Riwayat Lengkap
            </a>
        </div>
    </div>

    @if(!empty($isSecretary) || (Auth::user()->hasRole('sekretaris_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas'])))
        <div class="bg-indigo-50/90 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900/50 rounded-3xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                    📋
                </div>
                <div>
                    <div class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <span>Mode Monitoring Sekretaris Kelas</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-extrabold text-[10px]">Akses Hanya Lihat</span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Menampilkan laporan kehadiran harian khusus untuk <strong>Kelas {{ $classes->first()?->name ?? 'Anda' }}</strong>. Anda tidak dapat mengedit atau mengubah data presensi pada halaman ini.
                    </div>
                </div>
            </div>
            <div class="text-[11px] font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-indigo-100 dark:border-indigo-900/50 self-start sm:self-auto shrink-0 shadow-2xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Hanya Melihat (Read-Only)</span>
            </div>
        </div>
    @endif

    <!-- Real-time Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_students'] }}</div>
            <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Siswa Terdaftar</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-900/40 shadow-xs bg-emerald-50/20 dark:bg-emerald-950/20">
            <div class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Tepat Waktu</div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['hadir'] }}</div>
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $stats['percentage'] }}% Hadir</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-100 dark:border-amber-900/40 shadow-xs bg-amber-50/20 dark:bg-amber-950/20">
            <div class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Terlambat</div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['terlambat'] }}</div>
            <div class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5">Masuk &gt; 07:00</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-blue-100 dark:border-blue-900/40 shadow-xs bg-blue-50/20 dark:bg-blue-950/20">
            <div class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Izin</div>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $stats['izin'] }}</div>
            <div class="text-[10px] text-blue-500 dark:text-blue-400 mt-0.5">Dengan Surat</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-purple-100 dark:border-purple-900/40 shadow-xs bg-purple-50/20 dark:bg-purple-950/20">
            <div class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 uppercase tracking-wider">Sakit</div>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $stats['sakit'] }}</div>
            <div class="text-[10px] text-purple-500 dark:text-purple-400 mt-0.5">Surat Dokter</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-100 dark:border-rose-900/40 shadow-xs bg-rose-50/20 dark:bg-rose-950/20">
            <div class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 uppercase tracking-wider">Tanpa Keterangan</div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $stats['alpa'] }}</div>
            <div class="text-[10px] text-rose-500 dark:text-rose-400 mt-0.5">Perlu Tindakan</div>
        </div>
    </div>

    <!-- Filter & Live Typing Search Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('attendance.report') }}" method="GET" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Live Search by typing -->
                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Siswa (Ketik Nama / NIS / NISN)</label>
                    <div class="relative">
                        <input type="text" name="search" id="attendanceSearchInput" value="{{ request('search') }}"
                            placeholder="Ketik untuk mencari nama siswa atau nomor induk..."
                            class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Presensi</label>
                    <input type="date" name="date" value="{{ $date }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none focus:border-indigo-500 text-xs">
                </div>

                @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                @php
                    $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
                @endphp
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan Rombel Kelas</label>
                    @if(!empty($isSecretary) || (Auth::user()->hasRole('sekretaris_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas'])))
                        <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Kelas Anda' }}" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-700 dark:text-slate-300 text-xs cursor-not-allowed" readonly>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @elseif($isWaliKelasUser)
                        <select name="class_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none focus:border-indigo-500 text-xs font-bold">
                            @forelse($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @empty
                                <option value="">Belum ada kelas binaan</option>
                            @endforelse
                        </select>
                    @else
                        <select name="class_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none focus:border-indigo-500 text-xs">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                @endif

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl outline-none focus:border-indigo-500 text-xs">
                        <option value="">Semua Status</option>
                        <option value="HADIR" {{ request('status') == 'HADIR' ? 'selected' : '' }}>Hadir</option>
                        <option value="TERLAMBAT" {{ request('status') == 'TERLAMBAT' ? 'selected' : '' }}>Terlambat</option>
                        <option value="IZIN" {{ request('status') == 'IZIN' ? 'selected' : '' }}>Izin</option>
                        <option value="SAKIT" {{ request('status') == 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                        <option value="ALPA" {{ request('status') == 'ALPA' ? 'selected' : '' }}>Alpa</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-3 gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Metode Presensi:</label>
                    <div class="flex flex-wrap items-center gap-2 text-slate-700 dark:text-slate-300">
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ !request('method') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : '' }}">
                            <input type="radio" name="method" value="" {{ !request('method') ? 'checked' : '' }} onchange="this.form.submit()" class="text-indigo-600 focus:ring-indigo-500">
                            <span>Semua</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ request('method') == 'SELFIE' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold' : '' }}">
                            <input type="radio" name="method" value="SELFIE" {{ request('method') == 'SELFIE' ? 'checked' : '' }} onchange="this.form.submit()" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Selfie Kamera</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ in_array(request('method'), ['BARCODE', 'QR_CODE']) ? 'bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 font-bold' : '' }}">
                            <input type="radio" name="method" value="BARCODE" {{ in_array(request('method'), ['BARCODE', 'QR_CODE']) ? 'checked' : '' }} onchange="this.form.submit()" class="text-sky-600 focus:ring-sky-500">
                            <span>Barcode Scanner</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ request('method') == 'RFID' ? 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 font-bold' : '' }}">
                            <input type="radio" name="method" value="RFID" {{ request('method') == 'RFID' ? 'checked' : '' }} onchange="this.form.submit()" class="text-purple-600 focus:ring-purple-500">
                            <span>Kartu RFID</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ request('method') == 'FACE_RECOGNITION' ? 'bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300 font-bold' : '' }}">
                            <input type="radio" name="method" value="FACE_RECOGNITION" {{ request('method') == 'FACE_RECOGNITION' ? 'checked' : '' }} onchange="this.form.submit()" class="text-cyan-600 focus:ring-cyan-500">
                            <span>Face Recognition</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition {{ request('method') == 'MANUAL' ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-bold' : '' }}">
                            <input type="radio" name="method" value="MANUAL" {{ request('method') == 'MANUAL' ? 'checked' : '' }} onchange="this.form.submit()" class="text-amber-600 focus:ring-amber-500">
                            <span>Presensi Manual</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('attendance.report') }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl transition">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-xs transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Kehadiran Siswa Real-time</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $attendances->total() }} catatan presensi siswa</p>
            </div>
            <div class="text-xs text-slate-400 dark:text-slate-500">
                Pembaruan terakhir: {{ now()->translatedFormat('H:i:s') }} WIB
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="attendanceTable">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa / NIS</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Metode Presensi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Verifikasi / Lokasi</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($attendances as $index => $att)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition search-row">
                            <td class="py-3 px-4 text-center font-bold text-slate-400 dark:text-slate-500">
                                {{ $attendances->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white student-name">{{ $att->student_name ?? $att->student?->name }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 student-nis">NIS: {{ $att->nis ?? $att->student?->nis }} • NISN: {{ $att->nisn ?? $att->student?->nisn }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-md">
                                    {{ $att->student?->schoolClass?->name ?? 'XI-IPA-1' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ substr($att->time, 0, 5) }} WIB
                            </td>
                            <td class="py-3 px-4">
                                @if($att->method === 'SELFIE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                        📸 Selfie Kamera
                                    </span>
                                @elseif($att->method === 'BARCODE' || $att->method === 'QR_CODE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300">
                                        🏷️ Barcode Scanner
                                    </span>
                                @elseif($att->method === 'RFID')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300">
                                        💳 Kartu RFID
                                    </span>
                                @elseif($att->method === 'FACE_RECOGNITION')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300">
                                        🤖 Face Recognition
                                    </span>
                                @elseif($att->method === 'MANUAL' && $att->selfie_path)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                        📸 Manual + Selfie
                                    </span>
                                @elseif($att->method === 'MANUAL' || $att->method === 'IZIN_SAKIT')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                        📝 Presensi Manual
                                    </span>
                                @elseif($att->method === 'SISTEM')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                        ⚙️ Sistem
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300">
                                        📝 {{ $att->method ?? 'Presensi Manual' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($att->status === 'HADIR')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        HADIR
                                    </span>
                                @elseif($att->status === 'TERLAMBAT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        TERLAMBAT
                                    </span>
                                @elseif($att->status === 'IZIN')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        IZIN
                                    </span>
                                @elseif($att->status === 'SAKIT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        SAKIT
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        ALPA
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                <div class="text-[11px]">{{ $att->notes ?? 'Tervalidasi' }}</div>
                                @if($att->latitude && $att->longitude)
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">GPS: {{ round($att->latitude, 4) }}, {{ round($att->longitude, 4) }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($att->selfie_path)
                                    <a href="{{ asset('storage/' . $att->selfie_path) }}" target="_blank" class="inline-block w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xs hover:scale-110 transition">
                                        <img src="{{ asset('storage/' . $att->selfie_path) }}" class="w-full h-full object-cover" alt="Selfie">
                                    </a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400 dark:text-slate-500">
                                Tidak ada data presensi siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Cetak Laporan Presensi Resmi (A4) -->
<div id="printAttendanceModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-bold text-lg">
                    🖨️
                </div>
                <div>
                    <h3 class="font-bold text-base text-white">Cetak Laporan Presensi Resmi</h3>
                    <p class="text-xs text-slate-300">Format dokumen standar dinas ber-KOP resmi A4</p>
                </div>
            </div>
            <button type="button" onclick="closePrintAttendanceModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Filter Cetak -->
        <form action="{{ route('reports.print.attendance') }}" method="GET" target="_blank" class="p-6 space-y-4 text-xs">
            <!-- Pilihan Periode (Radio Tabs) -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pilih Jenis Periode</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/50 text-indigo-900 dark:text-indigo-300 font-bold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="daily" checked onchange="handlePeriodChange('daily')" class="hidden">
                        <span>📅 Harian</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Per Tanggal</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="weekly" onchange="handlePeriodChange('weekly')" class="hidden">
                        <span>🗓️ Mingguan</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">7 Hari / Minggu</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="monthly" onchange="handlePeriodChange('monthly')" class="hidden">
                        <span>📊 Bulanan</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Per Bulan</span>
                    </label>
                    <label class="period-option flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-xs cursor-pointer transition">
                        <input type="radio" name="period_type" value="custom" onchange="handlePeriodChange('custom')" class="hidden">
                        <span>⚙️ Kustom</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">Rentang Bebas</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Input Container based on Period Selection -->
            <!-- 1. Harian -->
            <div id="container-daily" class="period-container">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Tanggal Presensi</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
            </div>

            <!-- 2. Mingguan -->
            <div id="container-weekly" class="period-container hidden bg-slate-50 dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-600 dark:text-slate-300">Laporan mingguan akan mencetak rekapitulasi kehadiran siswa dalam 7 hari terakhir / minggu berjalan secara otomatis.</p>
            </div>

            <!-- 3. Bulanan -->
            <div id="container-monthly" class="period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-indigo-500 text-xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-indigo-500 text-xs">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- 4. Kustom -->
            <div id="container-custom" class="period-container hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white outline-none focus:border-indigo-500 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white outline-none focus:border-indigo-500 text-xs">
                </div>
            </div>

            <!-- Filter Kelas -->
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Target Kelas (Pilihan Rombel)</label>
                @if(!empty($isSecretary) || (Auth::user()->hasRole('sekretaris_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas'])))
                    <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Kelas Anda' }}" class="w-full px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed text-xs" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @elseif($isWaliKelasUser)
                    <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-800 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
                        @forelse($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                            </option>
                        @empty
                            <option value="">Belum ada kelas binaan</option>
                        @endforelse
                    </select>
                @else
                    <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-800 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs">
                        <option value="">Semua Kelas (Seluruh Siswa)</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            @endif

            <!-- Modal Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closePrintAttendanceModal()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Buka Dokumen Cetak (A4)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Client-side quick filter as user types in search input
    document.getElementById('attendanceSearchInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#attendanceTable .search-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
            const nis = row.querySelector('.student-nis')?.textContent.toLowerCase() || '';
            if (name.includes(query) || nis.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function openPrintAttendanceModal() {
        document.getElementById('printAttendanceModal')?.classList.remove('hidden');
    }

    function closePrintAttendanceModal() {
        document.getElementById('printAttendanceModal')?.classList.add('hidden');
    }

    function handlePeriodChange(type) {
        document.querySelectorAll('.period-container').forEach(c => c.classList.add('hidden'));
        const target = document.getElementById('container-' + type);
        if (target) target.classList.remove('hidden');

        document.querySelectorAll('.period-option').forEach(opt => {
            const input = opt.querySelector('input');
            if (input && input.value === type) {
                opt.classList.add('border-indigo-600', 'bg-indigo-50/50', 'text-indigo-900');
                opt.classList.remove('border-slate-200', 'text-slate-700');
            } else {
                opt.classList.remove('border-indigo-600', 'bg-indigo-50/50', 'text-indigo-900');
                opt.classList.add('border-slate-200', 'text-slate-700');
            }
        });
    }
</script>
@endsection
