@extends('layouts.app')

@section('title', 'Konfigurasi Sistem & Geofence')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    @include('master.partials.nav')
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800">
        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
            KONTROL SISTEM ADMIN
        </span>
        <h2 class="text-2xl font-black text-white mt-2">Konfigurasi Aturan & Geofence Sekolah</h2>
        <p class="text-xs text-slate-300 mt-1">Kelola koordinat GPS presensi selfie, jam operasional presensi, dan ambang batas tata tertib sekolah.</p>
    </div>

    <!-- Main Settings Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        <form action="{{ route('master.settings.update') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Identitas Sekolah -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2 mb-4">1. Identitas Sekolah</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Sekolah</label>
                        <input type="text" name="school_name" value="{{ \App\Models\SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong') }}"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Sekolah</label>
                        <input type="text" name="school_address" value="{{ \App\Models\SchoolSetting::get('school_address') }}"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- GPS Geofencing Settings -->
            @php
                $isGeofenceActive = \App\Models\SchoolSetting::get('enable_geofence_radius', '1') == '1';
            @endphp
            <div>
                <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>2. Titik Koordinat GPS & Radius Presensi Selfie</span>
                            <span id="badge-geofence-status" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold transition-colors {{ $isGeofenceActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800' }}">
                                {{ $isGeofenceActive ? '✓ Geofence Aktif' : '✕ Fitur Radius Nonaktif' }}
                            </span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Atur apakah pembatasan jarak GPS (radius meter) wajib berlaku atau dibebaskan untuk siswa.
                        </p>
                    </div>

                    <!-- Tombol / Switch Pilihan Fitur Radius Aktif atau Tidak -->
                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700 shrink-0">
                        <label class="cursor-pointer">
                            <input type="radio" name="enable_geofence_radius" value="1" {{ $isGeofenceActive ? 'checked' : '' }}
                                class="sr-only peer" onchange="updateGeofenceUI(true)">
                            <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition peer-checked:bg-emerald-600 peer-checked:text-white text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Radius Aktif</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="enable_geofence_radius" value="0" {{ ! $isGeofenceActive ? 'checked' : '' }}
                                class="sr-only peer" onchange="updateGeofenceUI(false)">
                            <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition peer-checked:bg-rose-600 peer-checked:text-white text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Radius Nonaktif</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Alert Penjelasan Status Fitur -->
                <div id="geofence-alert-box" class="p-3.5 rounded-2xl mb-4 border transition-colors flex items-start gap-3 {{ $isGeofenceActive ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-200' : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-200' }}">
                    <div id="geofence-alert-icon" class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $isGeofenceActive ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white' }}">
                        <span id="geofence-alert-symbol" class="text-xs font-bold">{{ $isGeofenceActive ? '✓' : '!' }}</span>
                    </div>
                    <div class="text-xs leading-relaxed">
                        <span id="geofence-alert-title" class="font-bold block">{{ $isGeofenceActive ? 'Fitur Radius Berlaku (Geofence Ketat):' : 'Fitur Radius Tidak Berlaku / Tidak Digunakan (Bebas Jarak):' }}</span>
                        <span id="geofence-alert-desc" class="text-[11.5px] opacity-90">{{ $isGeofenceActive ? 'Siswa wajib berada dalam radius meter yang ditentukan dari koordinat sekolah untuk dapat melakukan presensi selfie. Siswa di luar radius akan ditolak otomatis.' : 'Siswa dapat melakukan presensi selfie dari lokasi mana pun tanpa pembatasan jarak/radius GPS. Fitur radius dinonaktifkan sehingga siswa tidak akan ditolak.' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Latitude Sekolah</label>
                        <input type="text" name="school_lat" value="{{ \App\Models\SchoolSetting::get('school_lat', '-7.13013342752238') }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Longitude Sekolah</label>
                        <input type="text" name="school_lng" value="{{ \App\Models\SchoolSetting::get('school_lng', '106.7038263897621') }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                            <span>Radius Toleransi (Meter)</span>
                            <span id="radius-field-status" class="text-[10px] font-bold {{ $isGeofenceActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $isGeofenceActive ? '(Diterapkan)' : '(Tidak Digunakan)' }}
                            </span>
                        </label>
                        <input type="number" name="geofence_radius_meters" id="input_geofence_radius_meters" value="{{ \App\Models\SchoolSetting::get('geofence_radius_meters', 100) }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold {{ $isGeofenceActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 opacity-60' }}">
                    </div>
                </div>
                <p id="geofence-footer-note" class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">
                    {{ $isGeofenceActive ? '*Siswa di luar radius meter tersebut otomatis ditolak dengan pesan: "Anda berada di luar area presensi sekolah."' : '*Fitur radius sedang dinonaktifkan: Siswa dari jarak berapa pun dapat melakukan presensi selfie tanpa penolakan.' }}
                </p>
            </div>

            <!-- Setting Pilihan Metode Presensi Aktif (5 Gerbang Pilihan Terpadu) -->
            <div>
                <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>3. Pilihan Metode Presensi yang Diaktifkan</span>
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-[10px] border border-indigo-200 dark:border-indigo-800">5 Gerbang Terpadu</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Kelima metode berfungsi sebagai gerbang pilihan (opsi input) yang terintegrasi utuh ke satu rekapan laporan & riwayat tanpa data ganda.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                    <!-- 1. Toggle Selfie -->
                    <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-sm">
                                📷
                            </div>
                            <input type="checkbox" name="enable_attendance_selfie" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_selfie', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Selfie Kamera</div>
                            <div class="text-[10.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Kamera realtime siswa + validasi radius GPS geofence (Tampil di web siswa)</div>
                        </div>
                    </label>

                    <!-- 2. Toggle Barcode Scanner -->
                    <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold text-sm">
                                📟
                            </div>
                            <input type="checkbox" name="enable_attendance_barcode" value="1" {{ (\App\Models\SchoolSetting::get('enable_attendance_barcode', '1') == '1' || \App\Models\SchoolSetting::get('enable_attendance_qr', '1') == '1') ? 'checked' : '' }}
                                class="w-5 h-5 text-sky-600 rounded-lg focus:ring-sky-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Barcode Scanner</div>
                            <div class="text-[10.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Scan barcode kartu siswa menggunakan alat/mesin scanner sekolah</div>
                        </div>
                    </label>

                    <!-- 3. Toggle RFID -->
                    <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-sm">
                                💳
                            </div>
                            <input type="checkbox" name="enable_attendance_rfid" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_rfid', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-purple-600 rounded-lg focus:ring-purple-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Kartu RFID</div>
                            <div class="text-[10.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Tap kartu pintar siswa di mesin reader gerbang / kiosk sekolah</div>
                        </div>
                    </label>

                    <!-- 4. Toggle Face Recognition -->
                    <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-violet-100 dark:bg-violet-950/80 text-violet-700 dark:text-violet-300 flex items-center justify-center font-bold text-sm">
                                👤
                            </div>
                            <input type="checkbox" name="enable_attendance_face_recognition" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_face_recognition', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-violet-600 rounded-lg focus:ring-violet-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Face Recognition</div>
                            <div class="text-[10.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Mesin / terminal AI pengenal biometrik wajah siswa yang disediakan sekolah</div>
                        </div>
                    </label>

                    <!-- 5. Toggle Manual -->
                    <label class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-sm">
                                📝
                            </div>
                            <input type="checkbox" name="enable_attendance_manual" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_manual', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600 rounded-lg focus:ring-amber-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">Presensi Manual</div>
                            <div class="text-[10.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Input kehadiran manual rombel oleh Admin, Wali Kelas, & Sekretaris Kelas</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Jam Operasional Presensi Harian (2 Sesi: Masuk & Pulang - Senin s.d. Sabtu) -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">4. Jam Operasional Presensi Harian (2 Sesi: Masuk & Pulang)</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Konfigurasi jadwal kehadiran dan kepulangan per hari kerja (Senin s.d. Sabtu)</p>
                    </div>
                    <button type="button" onclick="copyMondaySchedule()" class="text-[11px] px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold rounded-lg border border-indigo-200 dark:border-indigo-800 transition cursor-pointer self-start sm:self-auto">
                        ⚡ Salin Jam Senin ke Hari Kerja Lain
                    </button>
                </div>

                @php
                    $daysList = [
                        'senin' => ['name' => 'Senin', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '15:00', 'default_out_end' => '17:30'],
                        'selasa' => ['name' => 'Selasa', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '15:00', 'default_out_end' => '17:30'],
                        'rabu' => ['name' => 'Rabu', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '15:00', 'default_out_end' => '17:30'],
                        'kamis' => ['name' => 'Kamis', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '15:00', 'default_out_end' => '17:30'],
                        'jumat' => ['name' => 'Jumat', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '11:45', 'default_out_end' => '17:00'],
                        'sabtu' => ['name' => 'Sabtu', 'default_in_start' => '06:00', 'default_in_on_time' => '07:00', 'default_in_late' => '07:30', 'default_out_start' => '13:00', 'default_out_end' => '16:00'],
                    ];
                    $legacyStart = \App\Models\SchoolSetting::get('attendance_start_time', '06:00');
                    $legacyCheckin = \App\Models\SchoolSetting::get('attendance_checkin_time', '07:00');
                    $legacyLate = \App\Models\SchoolSetting::get('attendance_late_cutoff_time', '07:30');
                    $legacyEnd = \App\Models\SchoolSetting::get('attendance_end_time', '17:30');
                @endphp

                <div class="space-y-3">
                    @foreach($daysList as $dayKey => $dayData)
                        @php
                            $isActive = \App\Models\SchoolSetting::get("attendance_{$dayKey}_is_active", '1') === '1';
                            $inStart = \App\Models\SchoolSetting::get("attendance_{$dayKey}_in_start", $dayKey === 'senin' ? $legacyStart : $dayData['default_in_start']);
                            $inOnTime = \App\Models\SchoolSetting::get("attendance_{$dayKey}_in_on_time", $dayKey === 'senin' ? $legacyCheckin : $dayData['default_in_on_time']);
                            $inLate = \App\Models\SchoolSetting::get("attendance_{$dayKey}_in_late_cutoff", $dayKey === 'senin' ? $legacyLate : $dayData['default_in_late']);
                            $outStart = \App\Models\SchoolSetting::get("attendance_{$dayKey}_out_start", $dayData['default_out_start']);
                            $outEnd = \App\Models\SchoolSetting::get("attendance_{$dayKey}_out_end", $dayKey === 'senin' ? $legacyEnd : $dayData['default_out_end']);
                        @endphp
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-200/70 dark:border-slate-800 pb-2">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="attendance_{{ $dayKey }}_is_active" value="1" {{ $isActive ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-slate-800 dark:text-slate-100">Hari {{ $dayData['name'] }}</span>
                                </label>
                                <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    {{ $isActive ? 'Hari Aktif' : 'Libur' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sesi Masuk -->
                                <div class="bg-white dark:bg-slate-900 p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/50 space-y-2">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 mb-1">
                                        <span>🟢</span> Sesi Presensi Masuk
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Buka Masuk</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_start" id="in_start_{{ $dayKey }}" value="{{ $inStart }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Tepat Waktu</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_on_time" id="in_on_time_{{ $dayKey }}" value="{{ $inOnTime }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Batas Terlambat</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_late_cutoff" id="in_late_{{ $dayKey }}" value="{{ $inLate }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sesi Pulang -->
                                <div class="bg-white dark:bg-slate-900 p-3 rounded-xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-300 mb-1">
                                        <span>🔵</span> Sesi Presensi Pulang
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Buka Pulang</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_out_start" id="out_start_{{ $dayKey }}" value="{{ $outStart }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Tutup Pulang</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_out_end" id="out_end_{{ $dayKey }}" value="{{ $outEnd }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                function copyMondaySchedule() {
                    const inStart = document.getElementById('in_start_senin').value;
                    const inOnTime = document.getElementById('in_on_time_senin').value;
                    const inLate = document.getElementById('in_late_senin').value;
                    const outStart = document.getElementById('out_start_senin').value;
                    const outEnd = document.getElementById('out_end_senin').value;

                    ['selasa', 'rabu', 'kamis'].forEach(day => {
                        if (document.getElementById('in_start_' + day)) document.getElementById('in_start_' + day).value = inStart;
                        if (document.getElementById('in_on_time_' + day)) document.getElementById('in_on_time_' + day).value = inOnTime;
                        if (document.getElementById('in_late_' + day)) document.getElementById('in_late_' + day).value = inLate;
                        if (document.getElementById('out_start_' + day)) document.getElementById('out_start_' + day).value = outStart;
                        if (document.getElementById('out_end_' + day)) document.getElementById('out_end_' + day).value = outEnd;
                    });
                    alert('Jadwal hari Senin berhasil disalin ke Selasa, Rabu, dan Kamis!');
                }
            </script>

            <!-- 5. Jam Operasional Presensi Keagamaan & Ekstrakurikuler (Barometer Waktu Sistem) -->
            <div>
                <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>5. Jam Operasional Presensi Keagamaan & Ekstrakurikuler</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] border border-emerald-200 dark:border-emerald-800">Barometer Jadwal Sistem</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Semua presensi mengambil barometer jadwal resmi dari pengaturan ini untuk membatasi time-lock sesi masuk.</p>
                </div>

                <div class="space-y-4">
                    @php
                        $activitySchedules = [
                            'dhuha' => [
                                'name' => 'Presensi Shalat Dhuha',
                                'desc' => 'Shalat Dhuha bersama untuk seluruh siswa & siswi muslim',
                                'days_key' => 'worship_dhuha_days',
                                'default_days' => ['kamis', 'jumat'],
                                'start_key' => 'worship_dhuha_start',
                                'end_key' => 'worship_dhuha_end',
                                'default_start' => '06:30',
                                'default_end' => '07:30',
                                'badge' => 'Keagamaan',
                                'icon' => '🌅',
                            ],
                            'dzuhur_jumat' => [
                                'name' => "Presensi Shalat Dzuhur/Jum'at",
                                'desc' => "Shalat Dzuhur berjamaah (Senin-Kamis) & Shalat Jum'at untuk siswa putra",
                                'days_key' => 'worship_dzuhur_jumat_days',
                                'default_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
                                'start_key' => 'worship_dzuhur_jumat_start',
                                'end_key' => 'worship_dzuhur_jumat_end',
                                'default_start' => '11:30',
                                'default_end' => '13:15',
                                'badge' => 'Keagamaan',
                                'icon' => '🕌',
                            ],
                            'keputrian' => [
                                'name' => 'Presensi Kegiatan Keputrian',
                                'desc' => "Kajian keputrian khusus siswi putri saat Shalat Jum'at berlangsung",
                                'days_key' => 'worship_keputrian_days',
                                'default_days' => ['jumat'],
                                'start_key' => 'worship_keputrian_start',
                                'end_key' => 'worship_keputrian_end',
                                'default_start' => '11:30',
                                'default_end' => '13:30',
                                'badge' => 'Keagamaan',
                                'icon' => '🧕',
                            ],
                            'ekstrakurikuler' => [
                                'name' => 'Presensi Ekstrakurikuler',
                                'desc' => 'Kegiatan ekstrakurikuler sore hari (Pramuka, PMR, Paskibra, Olahraga, Kesenian, Rohis, dll)',
                                'days_key' => 'extra_attendance_days',
                                'default_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'],
                                'start_key' => 'extra_attendance_start',
                                'end_key' => 'extra_attendance_end',
                                'default_start' => '14:00',
                                'default_end' => '17:30',
                                'badge' => 'Ekstrakurikuler',
                                'icon' => '⚽',
                            ],
                        ];

                        $allWeekDays = [
                            'senin' => 'Senin',
                            'selasa' => 'Selasa',
                            'rabu' => 'Rabu',
                            'kamis' => 'Kamis',
                            'jumat' => "Jum'at",
                            'sabtu' => 'Sabtu',
                            'minggu' => 'Minggu',
                        ];

                        $extracurriculars = $extracurriculars ?? \App\Models\Extracurricular::with(['teacher', 'teachers'])->orderBy('name')->get();
                    @endphp

                    @foreach($activitySchedules as $actKey => $act)
                        @php
                            $actStart = \App\Models\SchoolSetting::get($act['start_key'], $act['default_start']);
                            $actEnd = \App\Models\SchoolSetting::get($act['end_key'], $act['default_end']);
                            $rawDays = \App\Models\SchoolSetting::get($act['days_key'], implode(',', $act['default_days']));
                            $selectedDays = \App\Models\WorshipAttendance::parseDays($rawDays, $act['default_days']);
                            $daysLabel = \App\Models\WorshipAttendance::formatDaysLabel($selectedDays);
                        @endphp

                        @if($actKey !== 'ekstrakurikuler')
                            {{-- Card Standar: Presensi Keagamaan (Dhuha, Dzuhur/Jumat, Keputrian) --}}
                            <div class="p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition space-y-4 shadow-2xs">
                                <!-- Top bar: Nama, Deskripsi, Badge & Live Schedule Summary Badge -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200/70 dark:border-slate-800 pb-3">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs shrink-0">{{ $act['icon'] }}</span>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-extrabold text-sm text-slate-800 dark:text-slate-100">{{ $act['name'] }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shrink-0">
                                                    {{ $act['badge'] }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $act['desc'] }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                                        <span class="px-3 py-1 rounded-xl text-[11px] font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 shadow-2xs">
                                            <span>📅 {{ $daysLabel }}</span>
                                            <span>•</span>
                                            <span>⏰ {{ $actStart }} - {{ $actEnd }} WIB</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Hidden input untuk mendeteksi submit form jika user uncheck semua hari -->
                                <input type="hidden" name="_present_{{ $act['days_key'] }}" value="1">

                                <!-- Pemilih Hari Aktif -->
                                <div>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 mb-2">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Pilih Hari Aktif Operasional Presensi:
                                        </label>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', ['senin','selasa','rabu','kamis','jumat'])" class="px-2 py-0.5 rounded-md bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition">
                                                Senin - Jum'at
                                            </button>
                                            <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'])" class="px-2 py-0.5 rounded-md bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition">
                                                Semua Hari
                                            </button>
                                            <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', [])" class="px-2 py-0.5 rounded-md bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 font-semibold transition">
                                                Kosongkan
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                                        @foreach($allWeekDays as $dayKey => $dayName)
                                            @php $isChecked = in_array($dayKey, $selectedDays); @endphp
                                            <label class="cursor-pointer select-none">
                                                <input type="checkbox" name="{{ $act['days_key'] }}[]" value="{{ $dayKey }}"
                                                    {{ $isChecked ? 'checked' : '' }}
                                                    onchange="toggleDayPill(this)"
                                                    class="sr-only day-checkbox-{{ $act['days_key'] }}">
                                                <div class="day-pill-box px-2.5 py-2 rounded-xl text-xs font-bold border transition-all text-center flex items-center justify-center gap-1.5 {{ $isChecked ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                                                    <span class="day-pill-dot w-2 h-2 rounded-full shrink-0 {{ $isChecked ? 'bg-white' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                                    <span class="day-pill-text">{{ $dayName }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Rentang Jam Mulai & Selesai -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 border-t border-slate-200/50 dark:border-slate-800">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Waktu Mulai Buka Presensi
                                        </label>
                                        <input type="time" name="{{ $act['start_key'] }}" value="{{ $actStart }}"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Sesi dibuka dan siap menerima log presensi siswa.</p>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Waktu Selesai Tutup Presensi (Batas Akhir)
                                        </label>
                                        <input type="time" name="{{ $act['end_key'] }}" value="{{ $actEnd }}"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Batas akhir presensi sebelum time-lock otomatis mengunci sistem.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Card Khusus: Presensi Ekstrakurikuler (Multi-Jadwal Berdasarkan Jenis Ekstrakurikuler + Default Master Fallback) --}}
                            <div class="p-4 sm:p-6 rounded-3xl border-2 border-indigo-200 dark:border-indigo-900/60 bg-gradient-to-b from-indigo-50/40 via-white to-slate-50 dark:from-slate-900 dark:via-slate-900/90 dark:to-slate-900 space-y-6 shadow-sm">
                                <!-- Top Bar Ekstrakurikuler -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-indigo-100 dark:border-indigo-950 pb-4">
                                    <div class="flex items-start gap-3">
                                        <span class="text-3xl p-2.5 rounded-2xl bg-indigo-600 text-white shadow-md shadow-indigo-600/20 shrink-0">⚽</span>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-black text-base text-slate-900 dark:text-white">Presensi Ekstrakurikuler</span>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                    Sistem Jadwal Per Jenis Kegiatan
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                                Setiap jenis kegiatan ekstrakurikuler memiliki jadwal hari dan jam latihan yang berbeda-beda. Sistem presensi semua ekstrakurikuler akan secara otomatis mengikuti konfigurasi jadwal yang disetting di sini.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs">
                                            Terdaftar: <strong class="text-indigo-600 dark:text-indigo-400">{{ $extracurriculars->count() }} Kegiatan</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- SUB-BAGIAN A: Jadwal Standar / Umum Default Acuan -->
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 space-y-4 shadow-2xs">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 font-black text-xs flex items-center justify-center">A</span>
                                                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Jadwal Standar / Umum Ekstrakurikuler (Default Acuan Utama)</h4>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Jadwal ini digunakan secara otomatis untuk kegiatan ekstrakurikuler yang mengikuti jadwal umum, serta menjadi <strong>default otomatis bagi setiap jenis ekstrakurikuler baru yang nantinya baru dientri</strong> di Master Data.
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-xl text-[11px] font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 shrink-0 self-start sm:self-center">
                                            <span>📅 {{ $daysLabel }}</span>
                                            <span>•</span>
                                            <span>⏰ {{ $actStart }} - {{ $actEnd }} WIB</span>
                                        </span>
                                    </div>

                                    <!-- Hidden input untuk mendeteksi submit form jika user uncheck semua hari -->
                                    <input type="hidden" name="_present_{{ $act['days_key'] }}" value="1">

                                    <!-- Pemilih Hari Aktif Standar Umum -->
                                    <div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 mb-2">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Pilih Hari Standar / Umum Ekstrakurikuler:
                                            </label>
                                            <div class="flex items-center gap-1.5 text-[10px]">
                                                <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', ['senin','selasa','rabu','kamis','jumat'])" class="px-2 py-0.5 rounded-md bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition">
                                                    Senin - Jum'at
                                                </button>
                                                <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', ['senin','selasa','rabu','kamis','jumat','sabtu'])" class="px-2 py-0.5 rounded-md bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition">
                                                    Senin - Sabtu
                                                </button>
                                                <button type="button" onclick="setQuickDays('{{ $act['days_key'] }}', [])" class="px-2 py-0.5 rounded-md bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 font-semibold transition">
                                                    Kosongkan
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                                            @foreach($allWeekDays as $dayKey => $dayName)
                                                @php $isChecked = in_array($dayKey, $selectedDays); @endphp
                                                <label class="cursor-pointer select-none">
                                                    <input type="checkbox" name="{{ $act['days_key'] }}[]" value="{{ $dayKey }}"
                                                        {{ $isChecked ? 'checked' : '' }}
                                                        onchange="toggleDayPill(this)"
                                                        class="sr-only day-checkbox-{{ $act['days_key'] }}">
                                                    <div class="day-pill-box px-2.5 py-2 rounded-xl text-xs font-bold border transition-all text-center flex items-center justify-center gap-1.5 {{ $isChecked ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                                                        <span class="day-pill-dot w-2 h-2 rounded-full shrink-0 {{ $isChecked ? 'bg-white' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                                        <span class="day-pill-text">{{ $dayName }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Jam Mulai & Selesai Standar Umum -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 border-t border-slate-100 dark:border-slate-700/80">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Jam Mulai Standar Umum (Buka Sesi)
                                            </label>
                                            <input type="time" name="{{ $act['start_key'] }}" value="{{ $actStart }}"
                                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Sesi dibuka dan siap menerima log presensi ekstra yang mengikuti jadwal umum.</p>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Jam Selesai Standar Umum (Batas Akhir / Time-Lock)
                                            </label>
                                            <input type="time" name="{{ $act['end_key'] }}" value="{{ $actEnd }}"
                                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Batas akhir sebelum time-lock otomatis mengunci presensi ekstra yang mengikuti jadwal umum.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SUB-BAGIAN B: Jadwal Khusus Per Jenis Ekstrakurikuler -->
                                <div class="space-y-3.5 pt-2">
                                    <div class="flex items-center justify-between gap-2 border-b border-indigo-100 dark:border-indigo-950 pb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center">B</span>
                                            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Jadwal Khusus Per Jenis Ekstrakurikuler</h4>
                                        </div>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold">
                                            Disesuaikan per ekstrakurikuler
                                        </span>
                                    </div>

                                    <!-- Informasi Khusus: Pengingat Entri Ekstrakurikuler Belum Lengkap -->
                                    <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50/70 dark:from-blue-950/40 dark:to-indigo-950/40 border border-blue-200/80 dark:border-blue-900/60 text-blue-900 dark:text-blue-200 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs">
                                        <div class="flex items-start gap-3">
                                            <span class="text-xl p-1.5 rounded-xl bg-white dark:bg-slate-800 border border-blue-200 dark:border-blue-800 shadow-2xs shrink-0">💡</span>
                                            <div class="text-[11.5px] leading-relaxed">
                                                <span class="font-extrabold text-blue-950 dark:text-blue-100 block mb-0.5">Catatan Penyesuaian Jadwal & Ekstrakurikuler Baru:</span>
                                                Data/jenis ekstrakurikuler belum dientri semua pada Master Data? Anda dapat menambahkan data ekstrakurikuler baru kapan saja melalui menu <strong>Master Data &gt; Data Ekstrakurikuler</strong>. Setiap jenis ekstrakurikuler baru yang dientri akan otomatis menggunakan <strong>Jadwal Standar / Umum</strong> di atas sampai Anda menyesuaikan jadwal khususnya di halaman ini.
                                            </div>
                                        </div>
                                        <a href="{{ route('master.extracurriculars') }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-black bg-indigo-600 hover:bg-indigo-700 text-white transition shrink-0 self-start md:self-center shadow-xs">
                                            <span>+ Kelola / Tambah Ekstrakurikuler</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>

                                    <!-- Daftar Kegiatan Ekstrakurikuler -->
                                    <div class="space-y-3">
                                        @forelse($extracurriculars as $extra)
                                            @php
                                                $hasCustom = $extra->hasCustomSchedule();
                                                $customDays = $hasCustom ? $extra->effective_days : [];
                                                $extraStart = $extra->start_time ? substr($extra->start_time, 0, 5) : '14:00';
                                                $extraEnd = $extra->end_time ? substr($extra->end_time, 0, 5) : '17:30';
                                            @endphp
                                            <div class="p-4 rounded-2xl border {{ $hasCustom ? 'border-indigo-300 dark:border-indigo-800 bg-white dark:bg-slate-800/80 ring-1 ring-indigo-500/10' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50' }} shadow-2xs space-y-3 transition">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                                                    <div>
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $extra->name }}</span>
                                                            @if($extra->category)
                                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                                                    {{ $extra->category }}
                                                                </span>
                                                            @endif
                                                            <span id="extra_status_badge_{{ $extra->id }}" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $hasCustom ? 'bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                                                {{ $hasCustom ? '✨ Jadwal Khusus Aktif' : '🔄 Mengikuti Jadwal Umum' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                                            <span>Kode: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ $extra->code }}</strong></span>
                                                            <span>•</span>
                                                            <span>Pembina: <strong class="text-slate-700 dark:text-slate-300">{{ $extra->teacher_names }}</strong></span>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 self-start sm:self-center">
                                                        <label class="inline-flex items-center gap-2 cursor-pointer select-none px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:border-indigo-300 transition shadow-2xs">
                                                            <input type="checkbox" name="extra_schedules[{{ $extra->id }}][use_custom]" value="1"
                                                                id="extra_use_custom_{{ $extra->id }}"
                                                                {{ $hasCustom ? 'checked' : '' }}
                                                                onchange="toggleExtraCustomSchedule({{ $extra->id }}, this.checked)"
                                                                class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 cursor-pointer">
                                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Atur Jadwal Khusus</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Notice bila mengikuti Jadwal Standar Umum Default -->
                                                <div id="extra_default_notice_{{ $extra->id }}" class="{{ $hasCustom ? 'hidden' : '' }} p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-dashed border-slate-200 dark:border-slate-700 text-[11px] text-slate-600 dark:text-slate-400 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-emerald-500 font-bold">✓</span>
                                                        <span>Ekstrakurikuler ini aktif menggunakan <strong>Jadwal Standar / Umum Ekstrakurikuler</strong> (Buka presensi sesuai jadwal umum di atas).</span>
                                                    </div>
                                                    <button type="button" onclick="enableExtraCustomSchedule({{ $extra->id }})" class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-[11px] self-start sm:self-auto cursor-pointer">
                                                        + Sesuaikan Hari & Jam Khusus
                                                    </button>
                                                </div>

                                                <!-- Editor Jadwal Khusus (Tampil jika centang Atur Jadwal Khusus diaktifkan) -->
                                                <div id="extra_custom_container_{{ $extra->id }}" class="{{ $hasCustom ? '' : 'hidden' }} space-y-3 pt-1">
                                                    <div>
                                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 mb-2">
                                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                Pilih Hari Latihan Khusus {{ $extra->name }}:
                                                            </label>
                                                            <div class="flex items-center gap-1 text-[10px] flex-wrap">
                                                                <button type="button" onclick="setQuickExtraDays({{ $extra->id }}, ['jumat'])" class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-semibold transition">Jum'at Saja</button>
                                                                <button type="button" onclick="setQuickExtraDays({{ $extra->id }}, ['sabtu'])" class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-semibold transition">Sabtu Saja</button>
                                                                <button type="button" onclick="setQuickExtraDays({{ $extra->id }}, ['selasa','kamis'])" class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-semibold transition">Selasa & Kamis</button>
                                                                <button type="button" onclick="setQuickExtraDays({{ $extra->id }}, ['senin','selasa','rabu','kamis','jumat','sabtu'])" class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-semibold transition">Senin-Sabtu</button>
                                                                <button type="button" onclick="setQuickExtraDays({{ $extra->id }}, [])" class="px-2 py-0.5 rounded bg-rose-100 hover:bg-rose-200 text-rose-700 font-semibold transition">Kosongkan</button>
                                                            </div>
                                                        </div>

                                                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-1.5">
                                                            @foreach($allWeekDays as $dayKey => $dayName)
                                                                @php $isExtraDayChecked = in_array($dayKey, $customDays); @endphp
                                                                <label class="cursor-pointer select-none">
                                                                    <input type="checkbox" name="extra_schedules[{{ $extra->id }}][days][]" value="{{ $dayKey }}"
                                                                        {{ $isExtraDayChecked ? 'checked' : '' }}
                                                                        onchange="toggleDayPill(this)"
                                                                        class="sr-only extra-day-checkbox-{{ $extra->id }}">
                                                                    <div class="day-pill-box px-2 py-1.5 rounded-xl text-xs font-bold border transition-all text-center flex items-center justify-center gap-1 {{ $isExtraDayChecked ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                                                                        <span class="day-pill-dot w-1.5 h-1.5 rounded-full shrink-0 {{ $isExtraDayChecked ? 'bg-white' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                                                        <span class="day-pill-text text-[11px]">{{ $dayName }}</span>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                                                        <div>
                                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                                                <span class="text-emerald-500">⏰</span>
                                                                <span>Jam Mulai Latihan (Buka Presensi)</span>
                                                            </label>
                                                            <input type="time" name="extra_schedules[{{ $extra->id }}][start_time]" value="{{ $extraStart }}"
                                                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 font-mono transition">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                                                <span class="text-rose-500">⏰</span>
                                                                <span>Jam Selesai Latihan (Batas Akhir / Time-Lock)</span>
                                                            </label>
                                                            <input type="time" name="extra_schedules[{{ $extra->id }}][end_time]" value="{{ $extraEnd }}"
                                                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 font-mono transition">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-6 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center space-y-2 bg-white dark:bg-slate-800/40">
                                                <span class="text-3xl block">📋</span>
                                                <div class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Data Jenis Ekstrakurikuler yang Dientri</div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                                                    Silakan tambahkan data ekstrakurikuler melalui menu Master Data &gt; Data Ekstrakurikuler terlebih dahulu. Setiap kegiatan yang ditambahkan akan otomatis mengikuti Jadwal Standar / Umum di atas.
                                                </p>
                                                <div class="pt-2">
                                                    <a href="{{ route('master.extracurriculars') }}" target="_blank"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-xs">
                                                        + Tambah Data Ekstrakurikuler Baru
                                                    </a>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <script>
                function toggleDayPill(checkbox) {
                    const label = checkbox.closest('label');
                    if (!label) return;
                    const box = label.querySelector('.day-pill-box');
                    const dot = label.querySelector('.day-pill-dot');
                    if (!box || !dot) return;

                    if (checkbox.checked) {
                        box.classList.remove('bg-white', 'dark:bg-slate-900', 'bg-slate-50', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-200', 'hover:border-indigo-300', 'dark:hover:border-indigo-600');
                        box.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs');
                        dot.classList.remove('bg-slate-300', 'dark:bg-slate-600');
                        dot.classList.add('bg-white');
                    } else {
                        box.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs');
                        box.classList.add('bg-white', 'dark:bg-slate-900', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-200', 'hover:border-indigo-300', 'dark:hover:border-indigo-600');
                        dot.classList.remove('bg-white');
                        dot.classList.add('bg-slate-300', 'dark:bg-slate-600');
                    }
                }

                function setQuickDays(daysKey, targetDays) {
                    const checkboxes = document.querySelectorAll('.day-checkbox-' + daysKey);
                    checkboxes.forEach(cb => {
                        cb.checked = targetDays.includes(cb.value);
                        toggleDayPill(cb);
                    });
                }

                function toggleExtraCustomSchedule(extraId, isCustom) {
                    const notice = document.getElementById('extra_default_notice_' + extraId);
                    const container = document.getElementById('extra_custom_container_' + extraId);
                    const badge = document.getElementById('extra_status_badge_' + extraId);

                    if (isCustom) {
                        if (notice) notice.classList.add('hidden');
                        if (container) container.classList.remove('hidden');
                        if (badge) {
                            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800';
                            badge.innerText = '✨ Jadwal Khusus Aktif';
                        }
                    } else {
                        if (notice) notice.classList.remove('hidden');
                        if (container) container.classList.add('hidden');
                        if (badge) {
                            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700';
                            badge.innerText = '🔄 Mengikuti Jadwal Umum';
                        }
                    }
                }

                function enableExtraCustomSchedule(extraId) {
                    const cb = document.getElementById('extra_use_custom_' + extraId);
                    if (cb) {
                        cb.checked = true;
                        toggleExtraCustomSchedule(extraId, true);
                    }
                }

                function setQuickExtraDays(extraId, targetDays) {
                    const checkboxes = document.querySelectorAll('.extra-day-checkbox-' + extraId);
                    checkboxes.forEach(cb => {
                        cb.checked = targetDays.includes(cb.value);
                        toggleDayPill(cb);
                    });
                }
            </script>

            <!-- 6. Konfigurasi Kecerdasan Buatan (Google Gemini AI) -->
            <div class="mt-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>6. Konfigurasi Kecerdasan Buatan (Google Gemini AI)</span>
                            @php
                                $configuredApiKey = \App\Models\SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key');
                                $activeModel = \App\Models\SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-1.5-flash');
                            @endphp
                            @if(!empty($configuredApiKey))
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center gap-1 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> ✓ Live Online Terhubung
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 flex items-center gap-1 border border-indigo-200 dark:border-indigo-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Siap Dihubungkan ke Gemini Live
                                </span>
                            @endif
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Integrasi resmi Google Gemini API untuk analisis perkembangan holistik & sintesis psikopedagogis siswa secara realtime online.</p>
                    </div>
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer"
                       class="text-[11px] px-3 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-indigo-950/40 dark:to-blue-950/40 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-indigo-900/40 dark:hover:to-blue-900/40 text-indigo-700 dark:text-indigo-300 font-bold rounded-xl border border-indigo-200 dark:border-indigo-800 transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto shadow-xs">
                        <span>✨ Dapatkan API Key Gratis di Google AI Studio</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Google Gemini API Key</label>
                        <div class="relative">
                            <input type="password" name="gemini_api_key" id="gemini_api_key_input"
                                value="{{ \App\Models\SchoolSetting::get('gemini_api_key', config('services.gemini.api_key', '')) }}"
                                placeholder="Masukkan Google Gemini API Key (AIzaSy...)"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500 font-mono text-xs pr-10">
                            <button type="button" onclick="toggleGeminiKeyVisibility()"
                                class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer p-0.5"
                                title="Lihat / Sembunyikan API Key">
                                <span id="gemini_eye_icon">👁️</span>
                            </button>
                        </div>
                        <p class="text-[10.5px] text-slate-400 dark:text-slate-500 mt-1">
                            *Dapat dikosongkan jika API key sudah dipasang pada file konfigurasi lingkungan <code class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-1 py-0.5 rounded">.env</code>.
                        </p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Model Gemini</label>
                        <select name="gemini_model" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-800 dark:text-slate-100">
                            <option value="gemini-1.5-flash" {{ $activeModel === 'gemini-1.5-flash' ? 'selected' : '' }}>
                                gemini-1.5-flash (Direkomendasikan - Stabil & Cepat)
                            </option>
                            <option value="gemini-2.0-flash" {{ $activeModel === 'gemini-2.0-flash' ? 'selected' : '' }}>
                                gemini-2.0-flash (Generasi Terbaru)
                            </option>
                            <option value="gemini-1.5-pro" {{ $activeModel === 'gemini-1.5-pro' ? 'selected' : '' }}>
                                gemini-1.5-pro (Analisis Mendalam)
                            </option>
                        </select>
                        <p class="text-[10.5px] text-slate-400 dark:text-slate-500 mt-1">
                            Model <code class="text-indigo-600 dark:text-indigo-400">gemini-1.5-flash</code> memberikan respon online tercepat, gratis, dan penalaran psikopedagogis presisi.
                        </p>
                    </div>
                </div>
            </div>

            <script>
                function toggleGeminiKeyVisibility() {
                    const input = document.getElementById('gemini_api_key_input');
                    const icon = document.getElementById('gemini_eye_icon');
                    if (input && icon) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.textContent = '🙈';
                        } else {
                            input.type = 'password';
                            icon.textContent = '👁️';
                        }
                    }
                }
            </script>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Seluruh Pengaturan
                </button>
            </div>
        </form>
    </div>


    <!-- Pengaturan 6 Mesin RFID, 6 Barcode Scanner & 6 Mesin Face Recognition AI -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <span class="px-2.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 rounded-full text-[11px] font-bold tracking-wide uppercase border border-indigo-200 dark:border-indigo-800">
                    Manajemen Perangkat IoT & Biometrik
                </span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1">Konfigurasi Mesin RFID, Barcode Scanner & Face Recognition AI</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola status aktif, lokasi, IP address, dan penempatan 6 unit RFID, 6 unit Barcode Scanner, dan 6 unit Face Recognition AI, serta tambah perangkat baru sesuai kebutuhan.</p>
            </div>
            <form action="{{ route('master.devices.generate_default') }}" method="POST" onsubmit="return confirm('Inisialisasi ulang akan mengatur 6 Mesin RFID, 6 Barcode Scanner, dan 6 Face Recognition AI (Total 18 Unit) sesuai standar resmi. Lanjutkan?');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                    <span>⚡</span>
                    <span>Reset / Generate Standar 6 RFID, 6 Barcode & 6 Face AI</span>
                </button>
            </form>
        </div>

        <!-- Tab Navigasi Perangkat -->
        <div class="flex flex-wrap items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3" id="deviceTabs">
            <button type="button" onclick="switchDeviceTab('rfid')" id="tabBtnRfid"
                class="px-4 py-2 text-xs font-bold rounded-xl transition bg-purple-600 text-white shadow-sm flex items-center gap-2 cursor-pointer">
                <span>💳</span>
                <span>Mesin RFID ({{ $rfidDevices->count() }})</span>
            </button>
            <button type="button" onclick="switchDeviceTab('barcode')" id="tabBtnBarcode"
                class="px-4 py-2 text-xs font-bold rounded-xl transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-2 cursor-pointer">
                <span>📟</span>
                <span>Barcode Scanner ({{ $barcodeDevices->count() }})</span>
            </button>
            <button type="button" onclick="switchDeviceTab('face')" id="tabBtnFace"
                class="px-4 py-2 text-xs font-bold rounded-xl transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-2 cursor-pointer">
                <span>👤</span>
                <span>Face Recognition AI ({{ $faceDevices->count() }})</span>
            </button>
        </div>

        <!-- Daftar Mesin RFID -->
        <div id="panelRfidDevices" class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Daftar Mesin RFID Presensi Mandiri:</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Mendukung tap kartu RFID siswa di gerbang & lorong sekolah</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @forelse($rfidDevices as $dev)
                    <div class="p-4 rounded-2xl border {{ $dev->is_active ? 'border-purple-200 dark:border-purple-900/60 bg-purple-50/30 dark:bg-purple-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 opacity-75' }} flex flex-col justify-between space-y-3 hover:shadow-xs transition relative overflow-hidden">
                        <div class="flex items-start justify-between gap-2.5 min-w-0">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-xl {{ $dev->is_active ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center font-bold text-sm shrink-0">
                                    💳
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 min-w-0">
                                        <span class="px-1.5 py-0.5 bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 font-mono text-[10px] rounded font-bold shrink-0">{{ $dev->device_code }}</span>
                                        <span class="truncate text-xs" title="{{ $dev->device_name }}">{{ $dev->device_name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 min-w-0 truncate" title="{{ $dev->location }}">
                                        <span class="shrink-0">📍</span>
                                        <span class="truncate">{{ $dev->location ?: 'Lokasi belum disetel' }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 whitespace-nowrap {{ $dev->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50' }}">
                                {{ $dev->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 min-w-0">
                            <div class="truncate mr-2 font-mono text-slate-600 dark:text-slate-300">
                                IP: {{ $dev->ip_address ?: '-' }}
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" onclick="openEditDeviceModal({{ json_encode($dev) }})" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-indigo-700 dark:text-indigo-300 font-bold border border-slate-200 dark:border-slate-700 rounded-lg transition cursor-pointer">
                                    Edit
                                </button>
                                <form action="{{ route('master.devices.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat {{ $dev->device_code }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 font-bold hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl text-center text-slate-500 dark:text-slate-400 text-xs">
                        Belum ada mesin RFID terdaftar. Klik tombol <strong>"Reset / Generate Standar 6 RFID, 6 Barcode & 6 Face AI"</strong> di atas.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Daftar Mesin Barcode Scanner -->
        <div id="panelBarcodeDevices" class="space-y-4 hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Daftar Mesin Barcode Scanner / QR Code:</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Pemindai barcode fisik & pembaca QR NISN di gerbang / kiosk</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @forelse($barcodeDevices as $dev)
                    <div class="p-4 rounded-2xl border {{ $dev->is_active ? 'border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/30 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 opacity-75' }} flex flex-col justify-between space-y-3 hover:shadow-xs transition relative overflow-hidden">
                        <div class="flex items-start justify-between gap-2.5 min-w-0">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-xl {{ $dev->is_active ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center font-bold text-sm shrink-0">
                                    📟
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 min-w-0">
                                        <span class="px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-mono text-[10px] rounded font-bold shrink-0">{{ $dev->device_code }}</span>
                                        <span class="truncate text-xs" title="{{ $dev->device_name }}">{{ $dev->device_name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 min-w-0 truncate" title="{{ $dev->location }}">
                                        <span class="shrink-0">📍</span>
                                        <span class="truncate">{{ $dev->location ?: 'Lokasi belum disetel' }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 whitespace-nowrap {{ $dev->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50' }}">
                                {{ $dev->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 min-w-0">
                            <div class="truncate mr-2 font-mono text-slate-600 dark:text-slate-300">
                                IP: {{ $dev->ip_address ?: '-' }}
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" onclick="openEditDeviceModal({{ json_encode($dev) }})" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-indigo-700 dark:text-indigo-300 font-bold border border-slate-200 dark:border-slate-700 rounded-lg transition cursor-pointer">
                                    Edit
                                </button>
                                <form action="{{ route('master.devices.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat {{ $dev->device_code }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 font-bold hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl text-center text-slate-500 dark:text-slate-400 text-xs">
                        Belum ada mesin Barcode Scanner terdaftar. Klik tombol <strong>"Reset / Generate Standar 6 RFID, 6 Barcode & 6 Face AI"</strong> di atas.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Daftar Mesin Face Recognition AI -->
        <div id="panelFaceDevices" class="space-y-4 hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Daftar Mesin Face Recognition AI:</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Kamera cerdas & AI Terminal pendeteksi wajah siswa untuk presensi otomatis</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @forelse($faceDevices as $dev)
                    <div class="p-4 rounded-2xl border {{ $dev->is_active ? 'border-blue-200 dark:border-blue-900/60 bg-blue-50/30 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 opacity-75' }} flex flex-col justify-between space-y-3 hover:shadow-xs transition relative overflow-hidden">
                        <div class="flex items-start justify-between gap-2.5 min-w-0">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-xl {{ $dev->is_active ? 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }} flex items-center justify-center font-bold text-sm shrink-0">
                                    👤
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 min-w-0">
                                        <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 font-mono text-[10px] rounded font-bold shrink-0">{{ $dev->device_code }}</span>
                                        <span class="truncate text-xs" title="{{ $dev->device_name }}">{{ $dev->device_name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 min-w-0 truncate" title="{{ $dev->location }}">
                                        <span class="shrink-0">📍</span>
                                        <span class="truncate">{{ $dev->location ?: 'Lokasi belum disetel' }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 whitespace-nowrap {{ $dev->is_active ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50' }}">
                                {{ $dev->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 min-w-0">
                            <div class="truncate mr-2 font-mono text-slate-600 dark:text-slate-300">
                                IP: {{ $dev->ip_address ?: '-' }}
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" onclick="openEditDeviceModal({{ json_encode($dev) }})" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-indigo-700 dark:text-indigo-300 font-bold border border-slate-200 dark:border-slate-700 rounded-lg transition cursor-pointer">
                                    Edit
                                </button>
                                <form action="{{ route('master.devices.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat {{ $dev->device_code }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 font-bold hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl text-center text-slate-500 dark:text-slate-400 text-xs">
                        Belum ada mesin Face Recognition terdaftar. Klik tombol <strong>"Reset / Generate Standar 6 RFID, 6 Barcode & 6 Face AI"</strong> di atas.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Form Tambah Perangkat Kustom -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-3">+ Daftarkan / Tambah Perangkat Presensi Baru</h4>
            <form action="{{ route('master.devices.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Perangkat</label>
                    <input type="text" name="device_code" required placeholder="Contoh: FACE-07" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl uppercase font-mono font-bold focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perangkat</label>
                    <input type="text" name="device_name" required placeholder="Contoh: Face Cam Gate Sayap Selatan" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Perangkat</label>
                    <select name="device_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-bold focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500">
                        <option value="RFID">💳 RFID Scanner</option>
                        <option value="BARCODE">📟 Barcode Scanner</option>
                        <option value="FACE_RECOGNITION">👤 Face Recognition AI</option>
                        <option value="KIOSK">🖥️ Kiosk Mandiri</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Penempatan</label>
                    <input type="text" name="location" placeholder="Contoh: Gate Selatan" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 bg-slate-900 dark:bg-indigo-600 hover:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold rounded-xl transition cursor-pointer shadow-sm">
                        + Tambah Perangkat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Perangkat -->
    <div id="editDeviceModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <span>⚙️</span>
                    <span>Edit Konfigurasi Perangkat Presensi</span>
                </h3>
                <button type="button" onclick="closeEditDeviceModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold text-lg cursor-pointer">&times;</button>
            </div>
            <form id="editDeviceForm" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Perangkat</label>
                        <input type="text" id="edit_device_code" name="device_code" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono font-bold uppercase">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Perangkat</label>
                        <select id="edit_device_type" name="device_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-bold">
                            <option value="RFID">💳 RFID Scanner</option>
                            <option value="BARCODE">📟 Barcode Scanner</option>
                            <option value="FACE_RECOGNITION">👤 Face Recognition AI</option>
                            <option value="KIOSK">🖥️ Kiosk Mandiri</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perangkat</label>
                    <input type="text" id="edit_device_name" name="device_name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-bold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Penempatan</label>
                        <input type="text" id="edit_device_location" name="location" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">IP Address</label>
                        <input type="text" id="edit_device_ip" name="ip_address" placeholder="192.168.1.xxx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Deskripsi</label>
                    <input type="text" id="edit_device_notes" name="notes" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl">
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_device_is_active" name="is_active" value="1" class="w-4 h-4 text-indigo-600 rounded">
                    <label for="edit_device_is_active" class="font-bold text-slate-800 dark:text-slate-200">Status Perangkat Aktif & Menerima Presensi</label>
                </div>
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="closeEditDeviceModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchDeviceTab(tab) {
            const panelRfid = document.getElementById('panelRfidDevices');
            const panelBarcode = document.getElementById('panelBarcodeDevices');
            const panelFace = document.getElementById('panelFaceDevices');
            const btnRfid = document.getElementById('tabBtnRfid');
            const btnBarcode = document.getElementById('tabBtnBarcode');
            const btnFace = document.getElementById('tabBtnFace');

            // Hide all panels
            panelRfid.classList.add('hidden');
            panelBarcode.classList.add('hidden');
            panelFace.classList.add('hidden');

            // Reset buttons to inactive style
            const inactiveClass = 'px-4 py-2 text-xs font-bold rounded-xl transition bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-2 cursor-pointer';
            btnRfid.className = inactiveClass;
            btnBarcode.className = inactiveClass;
            btnFace.className = inactiveClass;

            if (tab === 'rfid') {
                panelRfid.classList.remove('hidden');
                btnRfid.className = 'px-4 py-2 text-xs font-bold rounded-xl transition bg-purple-600 text-white shadow-sm flex items-center gap-2 cursor-pointer';
            } else if (tab === 'barcode') {
                panelBarcode.classList.remove('hidden');
                btnBarcode.className = 'px-4 py-2 text-xs font-bold rounded-xl transition bg-emerald-600 text-white shadow-sm flex items-center gap-2 cursor-pointer';
            } else if (tab === 'face') {
                panelFace.classList.remove('hidden');
                btnFace.className = 'px-4 py-2 text-xs font-bold rounded-xl transition bg-blue-600 text-white shadow-sm flex items-center gap-2 cursor-pointer';
            }
        }

        function openEditDeviceModal(device) {
            const form = document.getElementById('editDeviceForm');
            form.action = "{{ url('master/devices') }}/" + device.id;
            document.getElementById('edit_device_code').value = device.device_code || '';
            document.getElementById('edit_device_name').value = device.device_name || '';
            document.getElementById('edit_device_type').value = device.device_type || 'RFID';
            document.getElementById('edit_device_location').value = device.location || '';
            document.getElementById('edit_device_ip').value = device.ip_address || '';
            document.getElementById('edit_device_notes').value = device.notes || '';
            document.getElementById('edit_device_is_active').checked = Boolean(device.is_active);

            document.getElementById('editDeviceModal').classList.remove('hidden');
        }

        function closeEditDeviceModal() {
            document.getElementById('editDeviceModal').classList.add('hidden');
        }

        function updateGeofenceUI(isActive) {
            const badge = document.getElementById('badge-geofence-status');
            const alertBox = document.getElementById('geofence-alert-box');
            const alertIcon = document.getElementById('geofence-alert-icon');
            const alertSymbol = document.getElementById('geofence-alert-symbol');
            const alertTitle = document.getElementById('geofence-alert-title');
            const alertDesc = document.getElementById('geofence-alert-desc');
            const radiusStatus = document.getElementById('radius-field-status');
            const radiusInput = document.getElementById('input_geofence_radius_meters');
            const footerNote = document.getElementById('geofence-footer-note');

            if (isActive) {
                if (badge) {
                    badge.className = "px-2.5 py-0.5 rounded-full text-[11px] font-extrabold transition-colors bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800";
                    badge.innerText = "✓ Geofence Aktif";
                }
                if (alertBox) {
                    alertBox.className = "p-3.5 rounded-2xl mb-4 border transition-colors flex items-start gap-3 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-200";
                }
                if (alertIcon) alertIcon.className = "w-6 h-6 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-emerald-600 text-white";
                if (alertSymbol) alertSymbol.innerText = "✓";
                if (alertTitle) alertTitle.innerText = "Fitur Radius Berlaku (Geofence Ketat):";
                if (alertDesc) alertDesc.innerText = "Siswa wajib berada dalam radius meter yang ditentukan dari koordinat sekolah untuk dapat melakukan presensi selfie. Siswa di luar radius akan ditolak otomatis.";
                if (radiusStatus) {
                    radiusStatus.className = "text-[10px] font-bold text-indigo-600 dark:text-indigo-400";
                    radiusStatus.innerText = "(Diterapkan)";
                }
                if (radiusInput) {
                    radiusInput.classList.remove('text-slate-400', 'dark:text-slate-500', 'opacity-60');
                    radiusInput.classList.add('text-indigo-600', 'dark:text-indigo-400');
                }
                if (footerNote) {
                    footerNote.innerText = '*Siswa di luar radius meter tersebut otomatis ditolak dengan pesan: "Anda berada di luar area presensi sekolah."';
                }
            } else {
                if (badge) {
                    badge.className = "px-2.5 py-0.5 rounded-full text-[11px] font-extrabold transition-colors bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800";
                    badge.innerText = "✕ Fitur Radius Nonaktif";
                }
                if (alertBox) {
                    alertBox.className = "p-3.5 rounded-2xl mb-4 border transition-colors flex items-start gap-3 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-200";
                }
                if (alertIcon) alertIcon.className = "w-6 h-6 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-amber-600 text-white";
                if (alertSymbol) alertSymbol.innerText = "!";
                if (alertTitle) alertTitle.innerText = "Fitur Radius Tidak Berlaku / Tidak Digunakan (Bebas Jarak):";
                if (alertDesc) alertDesc.innerText = "Siswa dapat melakukan presensi selfie dari lokasi mana pun tanpa pembatasan jarak/radius GPS. Fitur radius dinonaktifkan sehingga siswa tidak akan ditolak.";
                if (radiusStatus) {
                    radiusStatus.className = "text-[10px] font-bold text-slate-400 dark:text-slate-500";
                    radiusStatus.innerText = "(Tidak Digunakan)";
                }
                if (radiusInput) {
                    radiusInput.classList.add('text-slate-400', 'dark:text-slate-500', 'opacity-60');
                    radiusInput.classList.remove('text-indigo-600', 'dark:text-indigo-400');
                }
                if (footerNote) {
                    footerNote.innerText = '*Fitur radius sedang dinonaktifkan: Siswa dari jarak berapa pun dapat melakukan presensi selfie tanpa penolakan.';
                }
            }
        }
    </script>
</div>
@endsection
