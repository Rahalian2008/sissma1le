@extends('layouts.app')

@section('title', 'Konfigurasi Sistem & Geofence')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800">
        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-300 border border-white/10">
            KONTROL SISTEM ADMIN
        </span>
        <h2 class="text-2xl font-black text-white mt-2">Konfigurasi Aturan & Geofence Sekolah</h2>
        <p class="text-xs text-slate-300 mt-1">Kelola koordinat GPS presensi selfie, jam operasional presensi, dan ambang batas tata tertib sekolah.</p>
    </div>

    <!-- Main Settings Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('master.settings.update') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Identitas Sekolah -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">1. Identitas Sekolah</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Sekolah</label>
                        <input type="text" name="school_name" value="{{ \App\Models\SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong') }}"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Sekolah</label>
                        <input type="text" name="school_address" value="{{ \App\Models\SchoolSetting::get('school_address') }}"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- GPS Geofencing Settings -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center justify-between">
                    <span>2. Titik Koordinat GPS & Radius Presensi Selfie</span>
                    <span class="text-[11px] font-normal text-emerald-600 font-bold">✓ Geofence Aktif</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Latitude Sekolah</label>
                        <input type="text" name="school_lat" value="{{ \App\Models\SchoolSetting::get('school_lat', '-7.56845000') }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Longitude Sekolah</label>
                        <input type="text" name="school_lng" value="{{ \App\Models\SchoolSetting::get('school_lng', '112.04612000') }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Radius Toleransi (Meter)</label>
                        <input type="number" name="geofence_radius_meters" value="{{ \App\Models\SchoolSetting::get('geofence_radius_meters', 100) }}" required
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-bold text-indigo-600">
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    *Siswa di luar radius meter tersebut otomatis ditolak dengan pesan: "Anda berada di luar area presensi sekolah."
                </p>
            </div>

            <!-- Setting Pilihan Metode Presensi Aktif -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4 flex items-center justify-between">
                    <span>3. Pilihan Metode Presensi yang Diaktifkan</span>
                    <span class="text-[11px] text-slate-500 font-medium">Aktifkan atau nonaktifkan kanal presensi siswa sesuai kebijakan sekolah</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Toggle Selfie -->
                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                📷
                            </div>
                            <input type="checkbox" name="enable_attendance_selfie" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_selfie', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Presensi Selfie Kamera</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Kamera realtime siswa + validasi radius GPS geofence</div>
                        </div>
                    </label>

                    <!-- Toggle QR Code -->
                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                                📱
                            </div>
                            <input type="checkbox" name="enable_attendance_qr" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_qr', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-sky-600 rounded-lg focus:ring-sky-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Presensi QR Code Dinamis</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Scanner kamera HP siswa membaca QR sesi dari proyektor guru</div>
                        </div>
                    </label>

                    <!-- Toggle RFID -->
                    <label class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition cursor-pointer flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                                💳
                            </div>
                            <input type="checkbox" name="enable_attendance_rfid" value="1" {{ \App\Models\SchoolSetting::get('enable_attendance_rfid', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-purple-600 rounded-lg focus:ring-purple-500">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Presensi Kartu RFID</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Tap kartu pintar siswa di gerbang / simulator mesin kiosk</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Jam Operasional Presensi Harian (2 Sesi: Masuk & Pulang - Senin s.d. Sabtu) -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-2 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">4. Jam Operasional Presensi Harian (2 Sesi: Masuk & Pulang)</h3>
                        <p class="text-[11px] text-slate-500">Konfigurasi jadwal kehadiran dan kepulangan per hari kerja (Senin s.d. Sabtu)</p>
                    </div>
                    <button type="button" onclick="copyMondaySchedule()" class="text-[11px] px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg border border-indigo-200 transition cursor-pointer self-start sm:self-auto">
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
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="attendance_{{ $dayKey }}_is_active" value="1" {{ $isActive ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-slate-800">Hari {{ $dayData['name'] }}</span>
                                </label>
                                <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $isActive ? 'Hari Aktif' : 'Libur' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sesi Masuk -->
                                <div class="bg-white p-3 rounded-xl border border-emerald-100 space-y-2">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 mb-1">
                                        <span>🟢</span> Sesi Presensi Masuk
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Buka Masuk</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_start" id="in_start_{{ $dayKey }}" value="{{ $inStart }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Tepat Waktu</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_on_time" id="in_on_time_{{ $dayKey }}" value="{{ $inOnTime }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Batas Terlambat</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_in_late_cutoff" id="in_late_{{ $dayKey }}" value="{{ $inLate }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sesi Pulang -->
                                <div class="bg-white p-3 rounded-xl border border-indigo-100 space-y-2">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-700 mb-1">
                                        <span>🔵</span> Sesi Presensi Pulang
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Buka Pulang</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_out_start" id="out_start_{{ $dayKey }}" value="{{ $outStart }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Tutup Pulang</label>
                                            <input type="time" name="attendance_{{ $dayKey }}_out_end" id="out_end_{{ $dayKey }}" value="{{ $outEnd }}"
                                                class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:border-indigo-500 font-mono">
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

            <!-- 5. Konfigurasi Kecerdasan Buatan (Google Gemini AI) -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-2 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span>5. Konfigurasi Kecerdasan Buatan (Google Gemini AI)</span>
                            @php
                                $configuredApiKey = \App\Models\SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key');
                                $activeModel = \App\Models\SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash');
                            @endphp
                            @if(!empty($configuredApiKey))
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                                    ✓ API Aktif
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                                    Mode Offline / Lokal
                                </span>
                            @endif
                        </h3>
                        <p class="text-[11px] text-slate-500">Integrasi resmi Google Gemini API untuk analisis perkembangan holistik & sintesis psikopedagogis siswa.</p>
                    </div>
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer"
                       class="text-[11px] px-3 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto shadow-xs">
                        <span>✨ Dapatkan API Key Gratis di Google AI Studio</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Google Gemini API Key</label>
                        <div class="relative">
                            <input type="password" name="gemini_api_key" id="gemini_api_key_input"
                                value="{{ \App\Models\SchoolSetting::get('gemini_api_key', config('services.gemini.api_key', '')) }}"
                                placeholder="Masukkan Google Gemini API Key (AIzaSy...)"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-mono text-xs pr-10">
                            <button type="button" onclick="toggleGeminiKeyVisibility()"
                                class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 cursor-pointer p-0.5"
                                title="Lihat / Sembunyikan API Key">
                                <span id="gemini_eye_icon">👁️</span>
                            </button>
                        </div>
                        <p class="text-[10.5px] text-slate-400 mt-1">
                            *Dapat dikosongkan jika API key sudah dipasang pada file konfigurasi lingkungan <code class="text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded">.env</code>. Jika tidak diisi, sistem otomatis menggunakan synthesizer pedagogis aturan lokal.
                        </p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Model Gemini</label>
                        <select name="gemini_model" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-800">
                            <option value="gemini-2.5-flash" {{ $activeModel === 'gemini-2.5-flash' ? 'selected' : '' }}>
                                gemini-2.5-flash (Direkomendasikan)
                            </option>
                            <option value="gemini-1.5-flash" {{ $activeModel === 'gemini-1.5-flash' ? 'selected' : '' }}>
                                gemini-1.5-flash (Cepat & Hemat Kuota)
                            </option>
                            <option value="gemini-1.5-pro" {{ $activeModel === 'gemini-1.5-pro' ? 'selected' : '' }}>
                                gemini-1.5-pro (Analisis Mendalam)
                            </option>
                        </select>
                        <p class="text-[10.5px] text-slate-400 mt-1">
                            Model <code class="text-indigo-600">gemini-2.5-flash</code> memberikan respons tercepat dan analisis kepribadian siswa yang presisi.
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

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition">
                    Simpan Seluruh Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Pendaftaran Perangkat RFID Baru -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Pendaftaran Perangkat Scanner RFID / Kiosk Baru</h3>
        <form action="{{ route('master.devices.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Perangkat</label>
                <input type="text" name="device_code" required placeholder="Contoh: RFID-GATE-02" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Perangkat</label>
                <input type="text" name="device_name" required placeholder="Contoh: Scanner Gerbang Timur" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tipe Perangkat</label>
                <select name="device_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="RFID">RFID Scanner</option>
                    <option value="KIOSK">Kiosk Mandiri</option>
                    <option value="BARCODE">Barcode Reader</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-indigo-600 text-white font-bold rounded-xl transition">
                    + Daftarkan Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
