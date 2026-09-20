@extends('layouts.app')

@section('title', 'Ajuan Izin, Sakit & Pulang Cepat Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Notice -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                FORM AJUAN RESMI
            </span>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Ajuan Izin, Sakit & Pulang Cepat Siswa</h2>
        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
            Pengajuan izin, sakit, atau pulang cepat <strong>wajib mengambil foto selfie langsung melalui kamera perangkat</strong> dan mengaktifkan GPS. <strong>Tidak ada batasan radius sekolah</strong>, namun posisi realtime Anda akan dicatat otomatis untuk diverifikasi oleh Wali Kelas dan Administrator.
        </p>
    </div>

    <!-- Information Box: Aturan Jam Pengajuan -->
    <div class="p-4 sm:p-5 rounded-3xl bg-indigo-950/40 border border-indigo-500/20 shadow-sm text-xs text-slate-300 space-y-2.5">
        <div class="flex items-center gap-2 font-bold text-sm text-white">
            <span class="text-amber-400 text-base">⏰</span>
            <span>Ketentuan Jam Pengajuan Izin & Sakit:</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                <div class="font-bold text-emerald-400 mb-1 flex items-center gap-1.5">
                    <span>📅</span> Jauh Hari (Malam / H-1)
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Dapat diajukan kapan saja untuk tanggal yang akan datang (bebas tanpa batasan jam).
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                <div class="font-bold text-amber-400 mb-1 flex items-center gap-1.5">
                    <span>☀️</span> Hari H (Maks. 06:30 WIB)
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Pengajuan izin/sakit hari H ditutup pukul <strong>06:30 WIB</strong> pagi sebelum jam presensi masuk dibuka.
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
                <div class="font-bold text-orange-400 mb-1 flex items-center gap-1.5">
                    <span>🏃</span> Izin Pulang Cepat
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Dapat diajukan kapan saja saat jam belajar berlangsung bagi siswa yang hadir di sekolah.
                </p>
            </div>
        </div>
    @if(!empty($todayHoliday))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3.5 shadow-xs">
        <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
            📅
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-sm text-rose-700">Hari Libur Resmi: {{ $todayHoliday->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 text-rose-800 uppercase">{{ $todayHoliday->type }}</span>
            </div>
            <p class="text-xs text-rose-800 mt-0.5">Hari ini sekolah diliburkan. Siswa tidak perlu mengajukan izin atau sakit untuk hari libur resmi.</p>
        </div>
    </div>
    @endif

    <!-- Main Leave Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form id="leave-form" action="{{ route('attendance.leave.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Hidden Inputs for Camera Stream & GPS Coordinates -->
            <input type="hidden" name="selfie_image" id="selfie_image">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy" id="accuracy">

            <!-- Jenis Ajuan & Tanggal -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Pengajuan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex items-center justify-center p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-indigo-600 has-checked:bg-indigo-50/60 has-checked:text-indigo-900 border-slate-200 hover:border-slate-300 text-slate-700">
                            <input type="radio" name="status" value="IZIN" class="sr-only leave-status-radio" {{ ($defaultType ?? 'IZIN') === 'IZIN' ? 'checked' : '' }} onchange="toggleEarlyLeaveField()">
                            <div class="flex items-center gap-2 font-bold text-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>Izin Harian</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-indigo-600 has-checked:bg-indigo-50/60 has-checked:text-indigo-900 border-slate-200 hover:border-slate-300 text-slate-700">
                            <input type="radio" name="status" value="SAKIT" class="sr-only leave-status-radio" {{ ($defaultType ?? '') === 'SAKIT' ? 'checked' : '' }} onchange="toggleEarlyLeaveField()">
                            <div class="flex items-center gap-2 font-bold text-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span>Sakit Harian</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-3.5 rounded-2xl border-2 cursor-pointer transition has-checked:border-orange-500 has-checked:bg-orange-50/60 has-checked:text-orange-950 border-slate-200 hover:border-slate-300 text-slate-700">
                            <input type="radio" name="status" value="PULANG_CEPAT" class="sr-only leave-status-radio" {{ ($defaultType ?? '') === 'PULANG_CEPAT' ? 'checked' : '' }} onchange="toggleEarlyLeaveField()">
                            <div class="flex items-center gap-2 font-bold text-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                <span>Izin Pulang Cepat</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tanggal Pengajuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition">
                    </div>

                    <!-- Input Khusus Pulang Cepat -->
                    <div id="early-leave-time-container" class="{{ ($defaultType ?? '') === 'PULANG_CEPAT' ? '' : 'hidden' }}">
                        <label for="early_leave_time" class="block text-xs font-bold text-orange-700 uppercase tracking-wider mb-2">
                            Jam Pulang Cepat (Rencana/Sekarang) <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="early_leave_time" id="early_leave_time" value="{{ date('H:i') }}"
                            class="w-full px-4 py-3 bg-orange-50/50 border border-orange-200 rounded-2xl text-sm font-bold text-orange-950 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition">
                        <p class="text-[11px] text-orange-600 mt-1">Saat disetujui, jam ini akan dicatat sebagai jam kepulangan resmi Anda.</p>
                    </div>
                </div>

                <!-- Alert Validasi Jam & Tanggal Cutoff 06:30 -->
                <div id="cutoff-warning-alert" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900 flex items-start gap-3">
                    <span class="text-rose-500 font-bold text-base shrink-0">⚠️</span>
                    <div>
                        <strong class="font-bold text-rose-950">Batas Waktu Pengajuan Hari H Ditutup:</strong>
                        <p class="mt-0.5 leading-relaxed text-rose-800">
                            Pengajuan izin atau sakit untuk hari ini maksimal diajukan sebelum pukul <strong>06:30 WIB</strong>.
                            Jika Anda sudah berada di sekolah dan perlu izin meninggalkan sekolah, silakan pilih opsi <strong>Izin Pulang Cepat</strong>.
                            Untuk izin hari besok atau seterusnya (jauh hari), silakan ganti tanggal ke tanggal yang akan datang.
                        </p>
                    </div>
                </div>

                <div id="advance-leave-notice" class="hidden p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center gap-2.5">
                    <span class="text-emerald-600 font-bold text-base">✨</span>
                    <div>
                        <strong>Pengajuan Jauh Hari Aktif:</strong> Anda mengajukan izin/sakit untuk tanggal mendatang. Pengajuan ini akan diteruskan ke Wali Kelas untuk ditinjau.
                    </div>
                </div>

                <!-- Info Banner jika sudah Hadir pagi ini -->
                @if($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']))
                    <div id="morning-checkin-alert" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-xs text-emerald-900 {{ ($defaultType ?? '') === 'PULANG_CEPAT' ? '' : 'hidden' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                            <strong>Presensi Masuk Pagi Terdeteksi:</strong> Anda sudah tercatat {{ $existingAttendance->status }} pukul {{ substr($existingAttendance->time, 0, 5) }} WIB. Data masuk pagi Anda akan tetap aman dan kepulangan cepat Anda akan diverifikasi Wali Kelas.
                        </div>
                    </div>
                @endif
            </div>

            <!-- Keterangan / Alasan -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan / Alasan Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea name="notes" id="notes" rows="3" required placeholder="Contoh: Mengalami demam tinggi sejak semalam dan berobat ke puskesmas / Ada keperluan keluarga mendesak..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition"></textarea>
            </div>

            <!-- Upload Dokumen / Surat Dokter Opsional -->
            <div>
                <label for="attachment" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Lampiran Surat Dokter / Surat Izin Orang Tua (Opsional)
                </label>
                <div class="relative flex items-center justify-between p-3.5 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Format diperbolehkan: PDF, JPG, PNG (Maks. 5MB). Lampiran ini membantu percepatan verifikasi oleh wali kelas.</p>
            </div>

            <!-- Live GPS Status Bar -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div id="gps-indicator" class="w-3.5 h-3.5 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
                    <div>
                        <span class="font-bold text-slate-800" id="gps-status-text">Mendeteksi Posisi GPS Realtime...</span>
                        <div class="text-[11px] text-slate-500" id="gps-coords-text">Mohon izinkan akses lokasi pada browser</div>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-center text-[11px]">
                    ✓ Bebas Batas Radius
                </span>
            </div>

            <!-- Live Camera Section (Strict Rule: Direct Camera Only) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Foto Selfie Langsung Kamera <span class="text-rose-500">* (Dilarang Dari Galeri)</span>
                </label>

                <div class="relative w-full max-w-md mx-auto aspect-4/3 bg-slate-950 rounded-3xl overflow-hidden shadow-xl border-4 border-slate-900 flex items-center justify-center">
                    <!-- Live Video Element -->
                    <video id="camera-feed" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                    
                    <!-- Captured Image Preview -->
                    <img id="selfie-preview" class="hidden w-full h-full object-cover" alt="Preview Foto Selfie Langsung">

                    <!-- Frame Canvas (hidden) -->
                    <canvas id="camera-canvas" class="hidden"></canvas>

                    <!-- Face Oval Guide -->
                    <div id="face-guide" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-4">
                        <div class="w-40 h-52 border-2 border-dashed border-emerald-400/80 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/10">
                            <span class="text-[10px] font-bold text-emerald-300 bg-slate-950/70 px-2.5 py-1 rounded-full backdrop-blur-xs">
                                Posisikan Wajah Siswa
                            </span>
                        </div>
                    </div>

                    <!-- Camera Starter Overlay -->
                    <div id="camera-prompt" class="absolute inset-0 bg-slate-950/90 backdrop-blur-xs flex flex-col items-center justify-center p-6 text-center text-white">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm mb-1">Kamera Selfie Langsung</h3>
                        <p class="text-xs text-slate-400 mb-4 max-w-xs">
                            Sistem memerlukan foto wajah terkini sebagai bukti kehadiran/kondisi hari ini.
                        </p>
                        <button type="button" onclick="startCamera()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg transition">
                            Nyalakan Kamera
                        </button>
                    </div>
                </div>

                <!-- Camera Controls -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                    <button type="button" id="btn-snap" onclick="takeSnapshot()" disabled
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl text-xs shadow-md transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        Jepret Foto Selfie
                    </button>

                    <button type="button" id="btn-retake" onclick="retakeSnapshot()" class="hidden px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition">
                        Ambil Ulang
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100">
                <button type="submit" id="btn-submit" disabled
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 text-sm tracking-wide transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    KIRIM PENGAJUAN SEKARANG
                </button>
                <p class="text-center text-[11px] text-slate-400 mt-2">
                    Ajuan akan diteruskan ke akun Wali Kelas dan Administrator untuk ditinjau dan disetujui.
                </p>
            </div>
        </form>
    </div>

    <!-- Riwayat Pengajuan Izin / Sakit / Pulang Cepat Terakhir -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-extrabold text-slate-900 text-base mb-4 flex items-center justify-between">
            <span>Riwayat Ajuan Izin, Sakit & Pulang Cepat Saya</span>
            <span class="text-xs font-semibold text-slate-500">{{ count($historyLeaves) }} Data Terakhir</span>
        </h3>

        @if(count($historyLeaves) === 0)
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada riwayat pengajuan izin, sakit, atau pulang cepat yang dicatat.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($historyLeaves as $item)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            @if($item->selfie_path)
                                <img src="{{ asset('storage/'.$item->selfie_path) }}" alt="Foto Selfie" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-bold shrink-0">
                                    {{ $item->is_early_leave || $item->status === 'PULANG_CEPAT' ? 'P.CEPAT' : $item->status }}
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2">
                                    @php
                                        $isPc = $item->is_early_leave || $item->status === 'PULANG_CEPAT';
                                        $badgeColor = $item->status === 'SAKIT' ? 'bg-rose-100 text-rose-800' : ($isPc ? 'bg-orange-100 text-orange-800' : 'bg-amber-100 text-amber-800');
                                        $badgeLabel = $isPc ? 'PULANG CEPAT' : $item->status;
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $badgeColor }}">
                                        {{ $badgeLabel }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ $item->date->translatedFormat('l, d M Y') }} • {{ substr($item->time, 0, 5) }} WIB
                                    </span>
                                </div>
                                @if($isPc)
                                    <p class="text-[11px] text-orange-700 font-bold mt-0.5">
                                        Rencana / Jam Pulang: {{ substr($item->early_leave_time ?? $item->time_out ?? $item->time, 0, 5) }} WIB
                                        @if($item->time_out)
                                            <span class="text-emerald-600 font-semibold">• Check-out: {{ substr($item->time_out, 0, 5) }} WIB</span>
                                        @endif
                                    </p>
                                @endif
                                <p class="text-xs text-slate-700 mt-1 font-medium">{{ $item->notes }}</p>
                                @if($item->rejection_reason)
                                    <p class="text-xs text-rose-600 mt-1 font-semibold">Alasan Penolakan: {{ $item->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            @if($item->verification_status === 'PERLU_VERIFIKASI')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($item->verification_status === 'DIVERIFIKASI')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    ✓ Disetujui
                                </span>
                            @elseif($item->verification_status === 'DITOLAK')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                    ✕ Ditolak
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $item->verification_status }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let videoStream = null;
    let currentLat = null;
    let currentLng = null;
    let currentAccuracy = null;
    let hasSnapshot = false;

    const video = document.getElementById('camera-feed');
    const preview = document.getElementById('selfie-preview');
    const canvas = document.getElementById('camera-canvas');
    const btnSnap = document.getElementById('btn-snap');
    const btnRetake = document.getElementById('btn-retake');
    const btnSubmit = document.getElementById('btn-submit');
    const promptBox = document.getElementById('camera-prompt');
    const faceGuide = document.getElementById('face-guide');
    const hiddenSelfie = document.getElementById('selfie_image');
    const hiddenLat = document.getElementById('latitude');
    const hiddenLng = document.getElementById('longitude');
    const hiddenAcc = document.getElementById('accuracy');
    const gpsIndicator = document.getElementById('gps-indicator');
    const gpsStatusText = document.getElementById('gps-status-text');
    const gpsCoordsText = document.getElementById('gps-coords-text');

    // Start WebRTC Camera
    async function startCamera() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 720 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            video.srcObject = videoStream;
            promptBox.classList.add('hidden');
            btnSnap.disabled = false;
        } catch (err) {
            alert("Gagal mengakses kamera: " + err.message + ". Pastikan izin kamera aktif pada peramban.");
        }
    }

    // Take Snapshot from live video canvas
    function takeSnapshot() {
        if (!videoStream) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');

        // Mirror snapshot
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        hiddenSelfie.value = dataUrl;

        preview.src = dataUrl;
        preview.classList.remove('hidden');
        video.classList.add('hidden');
        faceGuide.classList.add('hidden');
        btnSnap.classList.add('hidden');
        btnRetake.classList.remove('hidden');
        hasSnapshot = true;

        checkFormReady();
    }

    // Retake Snapshot
    function retakeSnapshot() {
        hiddenSelfie.value = '';
        preview.classList.add('hidden');
        video.classList.remove('hidden');
        faceGuide.classList.remove('hidden');
        btnSnap.classList.remove('hidden');
        btnRetake.classList.add('hidden');
        hasSnapshot = false;

        checkFormReady();
    }

    // Initialize GPS Geolocation
    function initGPS() {
        if (!navigator.geolocation) {
            gpsStatusText.innerText = "Perangkat tidak mendukung geolokasi GPS.";
            return;
        }

        navigator.geolocation.watchPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                currentAccuracy = pos.coords.accuracy;

                hiddenLat.value = currentLat;
                hiddenLng.value = currentLng;
                hiddenAcc.value = currentAccuracy;

                gpsIndicator.className = "w-3.5 h-3.5 rounded-full bg-emerald-500 shrink-0";
                gpsStatusText.innerText = "Lokasi Realtime Terekam (" + currentLat.toFixed(5) + ", " + currentLng.toFixed(5) + ")";
                gpsCoordsText.innerText = "Akurasi GPS: ±" + Math.round(currentAccuracy) + " meter";

                checkFormReady();
            },
            (err) => {
                gpsIndicator.className = "w-3.5 h-3.5 rounded-full bg-rose-500 shrink-0";
                gpsStatusText.innerText = "GPS Dinonaktifkan: " + err.message;
                gpsCoordsText.innerText = "Wajib menyalakan GPS di perangkat Anda untuk mengirim ajuan.";
                checkFormReady();
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 }
        );
    }

    function checkCutoffTime() {
        const selectedRadio = document.querySelector('input[name="status"]:checked');
        const selectedStatus = selectedRadio ? selectedRadio.value : 'IZIN';
        const dateInput = document.getElementById('date');
        const todayStr = '{{ date('Y-m-d') }}';
        const nowTimeStr = '{{ date('H:i') }}';
        const cutoffAlert = document.getElementById('cutoff-warning-alert');
        const advanceNotice = document.getElementById('advance-leave-notice');

        if (!dateInput) return true;

        const targetDate = dateInput.value;

        if (selectedStatus !== 'PULANG_CEPAT') {
            if (targetDate === todayStr && nowTimeStr > '06:30') {
                if (cutoffAlert) cutoffAlert.classList.remove('hidden');
                if (advanceNotice) advanceNotice.classList.add('hidden');
                return false;
            } else if (targetDate > todayStr) {
                if (cutoffAlert) cutoffAlert.classList.add('hidden');
                if (advanceNotice) advanceNotice.classList.remove('hidden');
                return true;
            } else {
                if (cutoffAlert) cutoffAlert.classList.add('hidden');
                if (advanceNotice) advanceNotice.classList.add('hidden');
                return true;
            }
        } else {
            if (cutoffAlert) cutoffAlert.classList.add('hidden');
            if (advanceNotice) advanceNotice.classList.add('hidden');
            return true;
        }
    }

    function checkFormReady() {
        const isCutoffValid = checkCutoffTime();
        if (hasSnapshot && currentLat !== null && currentLng !== null && isCutoffValid) {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    }

    function toggleEarlyLeaveField() {
        const selectedRadio = document.querySelector('input[name="status"]:checked');
        const timeContainer = document.getElementById('early-leave-time-container');
        const morningAlert = document.getElementById('morning-checkin-alert');
        const timeInput = document.getElementById('early_leave_time');
        
        if (selectedRadio && selectedRadio.value === 'PULANG_CEPAT') {
            if (timeContainer) timeContainer.classList.remove('hidden');
            if (morningAlert) morningAlert.classList.remove('hidden');
            if (timeInput) timeInput.required = true;
        } else {
            if (timeContainer) timeContainer.classList.add('hidden');
            if (morningAlert) morningAlert.classList.add('hidden');
            if (timeInput) timeInput.required = false;
        }

        checkFormReady();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initGPS();
        toggleEarlyLeaveField();
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.addEventListener('change', checkFormReady);
        }
    });
</script>
@endpush
