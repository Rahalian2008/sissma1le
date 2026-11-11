<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2 Langkah - {{ $appName }} | {{ $schoolName }}</title>
    @php
        $loginOgImage = !empty($appLogoUrl) ? $appLogoUrl : url(asset('images/website/hero_school.jpg'));
    @endphp
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="Verifikasi 2 Langkah - {{ $appName }}">
    <meta property="og:description" content="Otentikasi Dua Langkah (Google Authenticator) {{ $schoolName }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-center items-center p-3 sm:p-6 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950">

    <div class="max-w-md w-full my-auto">
        <!-- Brand Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-3xl bg-gradient-to-tr from-emerald-500 via-teal-600 to-indigo-500 p-0.5 shadow-2xl shadow-emerald-500/30 mb-3 sm:mb-4">
                <div class="w-full h-full bg-slate-950 rounded-[22px] flex items-center justify-center text-emerald-400 font-black text-xl sm:text-2xl tracking-wider">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Otentikasi Dua Langkah</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Verifikasi identitas tambahan akun berkeamanan tinggi</p>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold mt-2">
                <span>{{ $user->name }}</span>
                <span class="text-slate-500">&bull;</span>
                <span class="uppercase text-[10px] text-emerald-400 font-mono">{{ str_replace('_', ' ', $user->role) }}</span>
            </div>
        </div>

        <!-- 2FA Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-2xl shadow-black/40">
            @if(session('error') || $errors->any())
                <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs sm:text-sm flex items-start gap-2.5">
                    <svg class="w-5 h-5 shrink-0 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>{{ session('error') ?? $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('login.two_factor.post') }}" method="POST" id="two-factor-form" class="space-y-4">
                @csrf

                <!-- Mode 1: 6-Digit TOTP Code -->
                <div id="totp-input-container">
                    <label for="code" class="block text-xs font-semibold text-slate-300 mb-2">
                        Buka aplikasi <span class="text-emerald-400 font-bold">Google Authenticator</span> di HP Anda dan masukkan 6 angka verifikasi:
                    </label>
                    <div class="relative">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" id="code" name="code"
                            autofocus autocomplete="one-time-code"
                            class="w-full text-center tracking-[0.5em] text-2xl font-mono py-3.5 bg-slate-950/80 border border-slate-700 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 transition shadow-inner font-black"
                            placeholder="······">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 text-center">
                        Kode 6-digit berubah otomatis setiap 30 detik.
                    </p>
                </div>

                <!-- Mode 2: Recovery Code (Hidden by default) -->
                <div id="recovery-input-container" class="hidden">
                    <label for="recovery_code" class="block text-xs font-semibold text-slate-300 mb-2">
                        Masukkan salah satu <span class="text-amber-400 font-bold">Kode Pemulihan Cadangan</span> Anda:
                    </label>
                    <input type="text" id="recovery_code" name="recovery_code" disabled
                        class="w-full text-center font-mono text-base py-3.5 bg-slate-950/80 border border-slate-700 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30 transition shadow-inner font-semibold"
                        placeholder="Contoh: a1b2-c3d4">
                    <p class="text-[11px] text-slate-400 mt-2 text-center">
                        Gunakan ini jika Anda kehilangan atau sedang tidak membawa ponsel Anda.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-2 py-3.5 px-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-bold text-sm rounded-2xl shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                    Verifikasi & Masuk
                </button>
            </form>

            <!-- Toggle Between Modes -->
            <div class="mt-4 pt-4 border-t border-slate-800 text-center">
                <button type="button" onclick="toggleChallengeMode()" id="toggle-mode-btn" class="text-xs text-slate-400 hover:text-emerald-400 font-medium transition cursor-pointer">
                    HP tertinggal atau tidak ada? Gunakan kode pemulihan cadangan &rarr;
                </button>
            </div>

            <!-- Cancel Button -->
            <form action="{{ route('login.two_factor.cancel') }}" method="POST" class="mt-3 text-center">
                @csrf
                <button type="submit" class="text-xs text-slate-500 hover:text-slate-300 transition cursor-pointer">
                    &larr; Batalkan dan kembali ke halaman login
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            Sistem Informasi Sekolah Terpadu &bull; {{ $schoolName }}
        </div>
    </div>

    <script>
        let isRecoveryMode = false;

        function toggleChallengeMode() {
            isRecoveryMode = !isRecoveryMode;
            const totpContainer = document.getElementById('totp-input-container');
            const recoveryContainer = document.getElementById('recovery-input-container');
            const totpInput = document.getElementById('code');
            const recoveryInput = document.getElementById('recovery_code');
            const toggleBtn = document.getElementById('toggle-mode-btn');

            if (isRecoveryMode) {
                totpContainer.classList.add('hidden');
                totpInput.disabled = true;
                totpInput.value = '';

                recoveryContainer.classList.remove('hidden');
                recoveryInput.disabled = false;
                recoveryInput.focus();

                toggleBtn.innerText = '← Kembali gunakan kode 6-digit Google Authenticator';
            } else {
                recoveryContainer.classList.add('hidden');
                recoveryInput.disabled = true;
                recoveryInput.value = '';

                totpContainer.classList.remove('hidden');
                totpInput.disabled = false;
                totpInput.focus();

                toggleBtn.innerText = 'HP tertinggal atau tidak ada? Gunakan kode pemulihan cadangan →';
            }
        }

        // Auto-submit when 6 digits are typed
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                document.getElementById('two-factor-form').submit();
            }
        });
    </script>
</body>
</html>

