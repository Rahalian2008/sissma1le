@extends('layouts.app')

@section('title', 'Scan QR Code Presensi')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800 text-center">
        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-sky-300 border border-white/10">
            METODE QR CODE
        </span>
        <h2 class="text-xl sm:text-2xl font-black text-white mt-2">Scan QR Code Presensi Kelas</h2>
        <p class="text-xs text-slate-300 mt-1">Arahkan kamera Anda ke QR Code sesi pembelajaran yang ditampilkan oleh Guru/Admin.</p>
    </div>

    @if(!$isEnabled)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-left">
                <span class="font-bold">Metode Presensi QR Code Sedang Dinonaktifkan</span>
                <p class="text-[11px] text-amber-700 mt-0.5">Pihak sekolah saat ini menonaktifkan presensi scan QR code.</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 text-center space-y-5">
        <!-- QR Camera Scanner Box -->
        <div class="relative w-full aspect-square max-w-xs mx-auto bg-slate-950 rounded-3xl overflow-hidden shadow-xl border-4 border-slate-900 flex items-center justify-center">
            <video id="qr-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            
            <!-- Target Scanning Reticle Overlay -->
            <div class="absolute inset-0 p-8 flex items-center justify-center pointer-events-none">
                <div class="w-full h-full border-2 border-dashed border-sky-400/80 rounded-2xl relative">
                    <div class="absolute inset-x-0 top-1/2 h-0.5 bg-sky-400 shadow-lg shadow-sky-400 animate-pulse"></div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-500 font-medium">
            Kamera otomatis mendeteksi QR Code sesi kelas yang aktif.
        </p>

        <!-- Manual Token Fallback Input (In case camera cannot read due to reflection on screen) -->
        <div class="pt-4 border-t border-slate-100">
            <span class="text-xs font-semibold text-slate-400 block mb-2">Atau masukkan kode sesi secara manual:</span>
            <div class="flex gap-2">
                <input type="text" id="manual-token" placeholder="Masukkan Token QR (Contoh: SMA1LE_...)"
                    class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-indigo-500">
                <button type="button" onclick="submitQrToken(document.getElementById('manual-token').value)"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition">
                    Verifikasi
                </button>
            </div>
        </div>

        <div id="qr-result" class="hidden p-4 rounded-2xl text-xs"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let scannerStream = null;

    async function initQrScanner() {
        try {
            scannerStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false
            });
            document.getElementById('qr-video').srcObject = scannerStream;
        } catch (err) {
            console.log("Kamera belakang tidak tersedia, mencoba kamera default:", err);
            try {
                scannerStream = await navigator.mediaDevices.getUserMedia({ video: true });
                document.getElementById('qr-video').srcObject = scannerStream;
            } catch (e) {
                console.log("Kamera tidak dapat diakses:", e);
            }
        }
    }

    async function submitQrToken(token) {
        if (!token) {
            alert("Harap masukkan atau scan kode QR terlebih dahulu.");
            return;
        }

        const resultDiv = document.getElementById('qr-result');
        resultDiv.classList.remove('hidden');
        resultDiv.className = "p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900";
        resultDiv.innerText = "Memverifikasi sesi presensi QR...";

        try {
            const response = await fetch("{{ route('attendance.qr.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({ qr_token: token.trim() })
            });

            const res = await response.json();

            if (response.ok && res.success) {
                resultDiv.className = "p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900";
                resultDiv.innerHTML = `<strong>Berhasil!</strong> ${res.message}`;
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 1500);
            } else {
                resultDiv.className = "p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900";
                resultDiv.innerHTML = `<strong>Gagal:</strong> ${res.message || "QR Code tidak valid."}`;
            }
        } catch (err) {
            resultDiv.className = "p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900";
            resultDiv.innerHTML = `<strong>Error:</strong> Gagal terhubung ke server.`;
        }
    }

    window.addEventListener('DOMContentLoaded', initQrScanner);
</script>
@endpush
