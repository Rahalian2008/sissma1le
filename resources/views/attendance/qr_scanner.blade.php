@extends('layouts.app')

@section('title', 'Scan QR Code Presensi Siswa & Kelas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-sky-300 border border-white/10 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>SCANNER QR CODE AKTIF</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-white">Scan Kartu Presensi / Sesi QR Siswa</h2>
            <p class="text-xs text-slate-300 mt-1 max-w-xl">
                Arahkan kamera ke <strong>Kartu Presensi Siswa (QR Code NISN)</strong> atau <strong>QR Code Sesi Guru</strong> untuk pencatatan presensi masuk/pulang secara instan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(!Auth::user()->isStudent())
            <a href="{{ route('attendance.cards.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold border border-white/20 transition inline-flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Cetak Kartu Siswa</span>
            </a>
            @endif
            <a href="{{ route('attendance.history') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Presensi</span>
            </a>
        </div>
    </div>

    @if(isset($schedule))
    <!-- Schedule Info Banner -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full {{ $schedule['is_active'] ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
            <span class="font-bold text-slate-800 dark:text-slate-100">Jadwal Operasional Hari {{ $schedule['day_name'] }}:</span>
            <span class="px-2 py-0.5 rounded-md font-bold text-[10.5px] {{ $schedule['is_active'] ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300' }}">
                {{ $schedule['is_active'] ? 'Hari Aktif' : ($schedule['is_holiday'] ? 'Libur: '.$schedule['holiday_name'] : 'Hari Libur / Non-Aktif') }}
            </span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200/70 dark:border-emerald-800/70">
                🟢 Masuk: {{ $schedule['in_start'] }} - {{ $schedule['in_late_cutoff'] }} WIB (Tepat Waktu s.d. {{ $schedule['in_on_time'] }})
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200/70 dark:border-indigo-800/70">
                🔵 Pulang: {{ $schedule['out_start'] }} - {{ $schedule['out_end'] }} WIB
            </span>
        </div>
    </div>
    @endif

    @if(!$isEnabled)
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <span class="font-bold">Metode Presensi QR Code Sedang Dinonaktifkan</span>
                <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5">Pihak sekolah saat ini menonaktifkan fitur scan QR code. Anda dapat mengaktifkannya di menu Pengaturan Sekolah.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Scanner Main Column (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
                <!-- Camera Controls Header -->
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Kamera Scanner</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Camera Selector -->
                        <select id="camera-select" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-[11px] font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-indigo-500 max-w-[160px] truncate">
                            <option value="">Memuat Kamera...</option>
                        </select>
                        <button type="button" id="btn-toggle-camera" onclick="toggleCamera()" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition" title="Ganti / Restart Kamera">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Video Viewport / Reader -->
                <div class="relative w-full aspect-[4/3] bg-slate-950 rounded-2xl overflow-hidden shadow-inner border border-slate-800 flex items-center justify-center">
                    <div id="reader" class="w-full h-full"></div>
                    
                    <!-- Loading / Placeholder Overlay -->
                    <div id="scanner-placeholder" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-6 text-center text-slate-300 pointer-events-none">
                        <svg class="w-10 h-10 text-sky-400 animate-spin mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs font-bold text-white">Menghubungkan ke Lensa Kamera...</span>
                        <p class="text-[11px] text-slate-400 mt-1">Izinkan akses kamera di browser Anda saat diminta.</p>
                    </div>
                </div>

                <!-- Scanner Device Location -->
                @if(isset($devices) && $devices->isNotEmpty())
                    <div class="flex items-center gap-3 pt-2">
                        <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Titik Scan:</label>
                        <select id="select-qr-device" class="flex-1 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-indigo-500">
                            @foreach($devices as $dev)
                                <option value="{{ $dev->device_code }}">
                                    [{{ $dev->device_code }}] {{ $dev->device_name }} ({{ $dev->location_name ?? $dev->location }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Manual Code Input Fallback -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Atau Scan dengan USB Barcode / Masukkan NISN Manual:
                    </label>
                    <form onsubmit="event.preventDefault(); submitQrToken(document.getElementById('manual-token').value);" class="flex gap-2">
                        <input type="text" id="manual-token" placeholder="{{ $isEnabled ? 'Masukkan NISN siswa atau token sesi...' : 'Metode QR Code dinonaktifkan' }}"
                            autocomplete="off" {{ !$isEnabled ? 'disabled' : '' }}
                            class="flex-1 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 transition disabled:bg-slate-100 dark:disabled:bg-slate-800/40 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed">
                        <button type="submit" id="btn-manual-submit" {{ !$isEnabled ? 'disabled' : '' }} class="px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed text-white font-bold rounded-xl text-xs transition inline-flex items-center gap-1.5 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Proses</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result & Recent Scans Column (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Active Scan Result Card (Realtime Feedback) -->
            <div id="scan-result-card" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 transition-all">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Hasil Scan Terakhir</span>
                    <span id="scan-timestamp" class="text-[11px] font-mono text-slate-400">Menunggu scan...</span>
                </div>

                <!-- State: Idle -->
                <div id="result-idle" class="py-10 text-center text-slate-400 space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Dekatkan QR code kartu presensi siswa ke arah kamera.</p>
                </div>

                <!-- State: Success -->
                <div id="result-success" class="hidden py-4 space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-sky-400 text-white font-black text-xl flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0 overflow-hidden">
                            <span id="res-avatar">ST</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="res-student-name" class="text-sm font-black text-slate-900 dark:text-white truncate">Nama Siswa</h4>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                <span>NISN: <strong id="res-student-nisn" class="font-mono text-slate-700 dark:text-slate-300">0000000000</strong></span>
                                <span>•</span>
                                <span id="res-student-class" class="font-bold text-indigo-600 dark:text-indigo-400">Kelas</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span id="res-badge-status" class="px-2.5 py-0.5 bg-emerald-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider">
                                HADIR
                            </span>
                            <span id="res-time" class="text-xs font-black font-mono text-emerald-800 dark:text-emerald-300">07:05:12 WIB</span>
                        </div>
                        <p id="res-message" class="text-xs font-medium text-emerald-800 dark:text-emerald-300 leading-snug">
                            Presensi masuk kartu QR berhasil dicatat tepat waktu.
                        </p>
                    </div>
                </div>

                <!-- State: Error -->
                <div id="result-error" class="hidden py-4 space-y-3">
                    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 space-y-1">
                        <div class="flex items-center gap-2 font-bold text-xs text-rose-700 dark:text-rose-300">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Gagal Memproses Scan</span>
                        </div>
                        <p id="res-error-message" class="text-xs text-rose-800 dark:text-rose-300">
                            QR Code tidak dikenali atau di luar jam presensi sekolah.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Scans Today -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Presensi QR Hari Ini</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 font-semibold text-slate-500 dark:text-slate-400">Live Update</span>
                </div>

                <div id="recent-scans-list" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @forelse($todayScans ?? [] as $scan)
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <p class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $scan->student?->name ?? $scan->student_name }}</p>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                    {{ $scan->student?->schoolClass?->name ?? 'Kelas' }} • NISN: {{ $scan->nisn ?? $scan->student?->nisn }}
                                </span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $scan->status === 'HADIR' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : ($scan->status === 'TERLAMBAT' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300') }}">
                                    {{ $scan->status }}
                                </span>
                                <span class="block text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">{{ substr($scan->time, 0, 5) }} WIB</span>
                            </div>
                        </div>
                    @empty
                        <div id="no-scans-msg" class="py-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                            Belum ada presensi QR yang tercatat hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    let html5QrCode = null;
    let isProcessing = false;
    let lastScannedToken = "";
    let lastScanTime = 0;
    let audioCtx = null;

    // Web Audio Chimes for Instant Feedback
    function playBeep(type = 'success') {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            if (type === 'success') {
                // High double chime
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                osc.frequency.setValueAtTime(1174.66, audioCtx.currentTime + 0.08); // D6
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.25);
            } else {
                // Low buzzer
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, audioCtx.currentTime); // A3
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.35);
            }
        } catch (e) {
            console.log("Audio not supported or permitted:", e);
        }
    }

    async function initCamera() {
        const placeholder = document.getElementById('scanner-placeholder');
        const cameraSelect = document.getElementById('camera-select');

        try {
            if (typeof Html5Qrcode === "undefined") {
                console.error("Html5Qrcode library not loaded");
                placeholder.innerHTML = '<span class="text-xs text-rose-400 font-bold">Library scanner tidak dapat dimuat.</span>';
                return;
            }

            html5QrCode = new Html5Qrcode("reader");

            const cameras = await Html5Qrcode.getCameras();
            cameraSelect.innerHTML = "";

            if (!cameras || cameras.length === 0) {
                placeholder.innerHTML = '<span class="text-xs text-amber-400 font-bold">Tidak ada kamera yang terdeteksi. Silakan gunakan input manual / barcode reader.</span>';
                return;
            }

            let defaultCameraId = cameras[0].id;
            // Prefer back/environment camera if available
            cameras.forEach((cam, index) => {
                const opt = document.createElement('option');
                opt.value = cam.id;
                opt.text = cam.label || `Kamera ${index + 1}`;
                if (cam.label && (cam.label.toLowerCase().includes('back') || cam.label.toLowerCase().includes('rear') || cam.label.toLowerCase().includes('environment'))) {
                    defaultCameraId = cam.id;
                    opt.selected = true;
                }
                cameraSelect.appendChild(opt);
            });

            await startScanner(defaultCameraId);
            placeholder.classList.add('hidden');
        } catch (err) {
            console.error("Gagal memulai scanner kamera:", err);
            placeholder.innerHTML = `<span class="text-xs text-rose-400 font-bold">Izin Kamera Ditolak / Tidak Tersedia</span><p class="text-[11px] text-slate-400 mt-1">${err.message || 'Periksa pengaturan izin browser Anda.'}</p>`;
        }
    }

    async function startScanner(cameraId) {
        if (!html5QrCode) return;

        const config = {
            fps: 15,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.333333
        };

        try {
            await html5QrCode.start(
                cameraId,
                config,
                (decodedText) => onScanSuccess(decodedText),
                (errorMessage) => {
                    // Ignored per-frame detection failures
                }
            );
        } catch (e) {
            console.error("Error starting camera:", e);
        }
    }

    async function toggleCamera() {
        const cameraSelect = document.getElementById('camera-select');
        const selectedId = cameraSelect.value;
        if (!selectedId || !html5QrCode) return;

        try {
            if (html5QrCode.isScanning) {
                await html5QrCode.stop();
            }
            await startScanner(selectedId);
        } catch (e) {
            console.error("Error switching camera:", e);
        }
    }

    document.getElementById('camera-select')?.addEventListener('change', toggleCamera);

    async function onScanSuccess(decodedText) {
        const now = Date.now();
        // Prevent rapid re-scanning of the same token within 3.5 seconds
        if (decodedText === lastScannedToken && (now - lastScanTime) < 3500) {
            return;
        }

        if (isProcessing) return;

        lastScannedToken = decodedText;
        lastScanTime = now;
        await submitQrToken(decodedText);
    }

    async function submitQrToken(token) {
        token = (token || "").trim();
        if (!token) return;

        isProcessing = true;
        showLoadingState();

        const deviceCode = document.getElementById('select-qr-device')?.value || 'QR-GATE-01';

        try {
            const response = await fetch("{{ route('attendance.qr.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    qr_token: token,
                    device_code: deviceCode
                })
            });

            const res = await response.json();

            if (response.ok && res.success) {
                playBeep('success');
                showSuccessResult(res);
                addRecentScanRecord(res);
                document.getElementById('manual-token').value = "";
            } else {
                playBeep('error');
                showErrorResult(res.message || "QR Code tidak dikenali atau tidak valid.");
            }
        } catch (err) {
            playBeep('error');
            showErrorResult("Gagal menghubungi server aplikasi. Periksa jaringan Anda.");
        } finally {
            // Auto resume ready for next scan after 1.8s
            setTimeout(() => {
                isProcessing = false;
            }, 1800);
        }
    }

    function showLoadingState() {
        document.getElementById('scan-timestamp').innerText = new Date().toLocaleTimeString('id-ID');
        document.getElementById('result-idle').classList.add('hidden');
        document.getElementById('result-success').classList.add('hidden');
        document.getElementById('result-error').classList.add('hidden');
    }

    function showSuccessResult(res) {
        document.getElementById('result-idle').classList.add('hidden');
        document.getElementById('result-error').classList.add('hidden');
        const successBox = document.getElementById('result-success');
        successBox.classList.remove('hidden');

        const student = res.student || {};
        const name = student.name || 'Siswa SMA Negeri 1 Lengkong';
        const nisn = student.nisn || student.nis || '-';
        const studentClass = student.class || 'Kelas Terdaftar';

        document.getElementById('res-student-name').innerText = name;
        document.getElementById('res-student-nisn').innerText = nisn;
        document.getElementById('res-student-class').innerText = studentClass;
        document.getElementById('res-avatar').innerText = name.substring(0, 2).toUpperCase();

        const status = res.status || 'HADIR';
        const statusBadge = document.getElementById('res-badge-status');
        statusBadge.innerText = (res.session === 'checkout' ? 'PULANG' : status);

        if (res.session === 'checkout') {
            statusBadge.className = "px-2.5 py-0.5 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider";
        } else if (status === 'HADIR') {
            statusBadge.className = "px-2.5 py-0.5 bg-emerald-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider";
        } else {
            statusBadge.className = "px-2.5 py-0.5 bg-amber-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider";
        }

        document.getElementById('res-time').innerText = (res.time_out || res.time || '') + ' WIB';
        document.getElementById('res-message').innerText = res.message;
    }

    function showErrorResult(msg) {
        document.getElementById('result-idle').classList.add('hidden');
        document.getElementById('result-success').classList.add('hidden');
        const errBox = document.getElementById('result-error');
        errBox.classList.remove('hidden');
        document.getElementById('res-error-message').innerText = msg;
    }

    function addRecentScanRecord(res) {
        const noMsg = document.getElementById('no-scans-msg');
        if (noMsg) noMsg.remove();

        const list = document.getElementById('recent-scans-list');
        const student = res.student || {};
        const name = student.name || 'Siswa Terdaftar';
        const studentClass = student.class || 'Kelas';
        const nisn = student.nisn || student.nis || '-';
        const status = (res.session === 'checkout' ? 'PULANG' : (res.status || 'HADIR'));
        const time = (res.time_out || res.time || '00:00').substring(0, 5);

        let badgeClass = "bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300";
        if (status === 'PULANG') badgeClass = "bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300";
        else if (status === 'TERLAMBAT') badgeClass = "bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300";

        const item = document.createElement('div');
        item.className = "p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs animate-fade-in";
        item.innerHTML = `
            <div class="min-w-0 pr-2">
                <p class="font-bold text-slate-800 dark:text-slate-200 truncate">${name}</p>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">${studentClass} • NISN: ${nisn}</span>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold ${badgeClass}">${status}</span>
                <span class="block text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">${time} WIB</span>
            </div>
        `;
        list.prepend(item);
    }

    window.addEventListener('DOMContentLoaded', () => {
        @if($isEnabled)
            initCamera();
        @else
            const placeholder = document.getElementById('scanner-placeholder');
            if (placeholder) {
                placeholder.innerHTML = `
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-white font-bold text-sm">Metode Scan QR Code Non-Aktif</h4>
                        <p class="text-xs text-slate-400 mt-1">Lensa scanner tidak diaktifkan karena metode ini dinonaktifkan di pengaturan sekolah.</p>
                    </div>
                `;
            }
        @endif
    });
</script>
@endpush
