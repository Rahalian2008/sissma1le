<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ $appName }} | {{ $schoolName }}</title>
    @php
        $loginOgImage = !empty($appLogoUrl) ? $appLogoUrl : url(asset('images/website/hero_school.jpg'));
    @endphp
    <!-- Open Graph / Social Sharing Meta -->
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="Masuk Portal - {{ $appName }} | {{ $schoolName }}">
    <meta property="og:description" content="Sistem Informasi Sekolah Terpadu {{ $schoolName }} - Masuk untuk guru, staf, kesiswaan, dan admin.">
    <meta property="og:image" content="{{ $loginOgImage }}">
    <meta property="og:image:url" content="{{ $loginOgImage }}">
    <meta property="og:image:secure_url" content="{{ $loginOgImage }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="image_src" href="{{ $loginOgImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Masuk - {{ $appName }} | {{ $schoolName }}">
    <meta name="twitter:description" content="Sistem Informasi Sekolah Terpadu {{ $schoolName }}">
    <meta name="twitter:image" content="{{ $loginOgImage }}">
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
            <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-3xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-emerald-400 p-0.5 shadow-2xl shadow-indigo-500/30 mb-3 sm:mb-4">
                <div class="w-full h-full bg-slate-950 rounded-[22px] flex items-center justify-center text-white font-black text-xl sm:text-2xl tracking-wider overflow-hidden">
                    @if(!empty($appLogoUrl))
                        <img src="{{ $appLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-2 rounded-[22px]">
                    @else
                        1LE
                    @endif
                </div>
            </div>
            <h1 class="text-xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $appName }}</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">{{ $appTagline }}</p>
            <p class="text-xs font-semibold text-emerald-400 tracking-wider uppercase mt-0.5">{{ $schoolName }}</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-2xl shadow-black/40">
            @if(session('error') || $errors->any())
                <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs sm:text-sm">
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-300 text-xs sm:text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Nomor Identitas (NISN / NIP / Username)</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-slate-950/60 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                        placeholder="Siswa: NISN | Guru/Admin: NIP | Super Admin">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Kata Sandi (Password)</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full pl-4 pr-11 py-3 bg-slate-950/60 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                            placeholder="Siswa: NISN | Guru/Admin: NIP | Password">
                        <button type="button" onclick="toggleLoginPassword()" id="btn-toggle-pwd" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition p-1">
                            <svg id="pwd-icon-show" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="pwd-icon-hide" class="w-4.5 h-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-0">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition duration-200 mt-2">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <!-- Special Portal for Parents (Tanpa Perlu Login - Cukup Masukkan NISN) -->
        <div class="mt-4 p-5 bg-gradient-to-r from-slate-900/90 via-indigo-950/80 to-slate-900/90 backdrop-blur-xl border border-indigo-500/30 rounded-2xl sm:rounded-3xl shadow-xl text-center">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30 mb-2">
                <span>👨‍👩‍👧</span> Portal Khusus Orang Tua / Wali
            </div>
            <h3 class="text-sm sm:text-base font-extrabold text-white">Tidak Perlu Akun & Password</h3>
            <p class="text-xs text-slate-300 mt-1 mb-3.5 leading-relaxed">
                Cukup masukkan nomor <strong>NISN</strong> anak Anda untuk memantau kehadiran hari ini (foto masuk/pulang), 7 kebiasaan anak hebat, dan prestasi.
            </p>
            <a href="{{ route('parent.portal') }}"
                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-lg shadow-emerald-600/30 text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <span>🔍 Buka Portal Orang Tua (Cek via NISN)</span>
                <span>→</span>
            </a>
        </div>

        <div class="mt-5 flex items-center justify-center gap-3 text-xs text-slate-400">
            <a href="{{ route('website.home') }}" class="hover:text-white transition flex items-center gap-1">
                <span>← Website Sekolah</span>
            </a>
            <span>•</span>
            <a href="{{ route('portal.select') }}" class="hover:text-indigo-300 text-indigo-400 transition flex items-center gap-1 font-semibold">
                <span>🌐 Portal SIS-SMA1LE</span>
            </a>
            <span>•</span>
            <a href="{{ route('kiosk.index') }}" target="_blank" class="hover:text-white transition flex items-center gap-1">
                <span>📺 Display TV</span>
            </a>
        </div>

        <p class="text-center text-xs text-slate-500 mt-5">
            &copy; {{ date('Y') }} {{ $schoolName }}. created--<a href="https://wa.me/6285887053005" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-emerald-400 hover:underline transition-colors font-medium">senks</a>--
        </p>
    </div>

    <script>
        function toggleLoginPassword() {
            const pwd = document.getElementById('password');
            const iconShow = document.getElementById('pwd-icon-show');
            const iconHide = document.getElementById('pwd-icon-hide');
            if (!pwd) return;
            if (pwd.type === 'password') {
                pwd.type = 'text';
                if (iconShow) iconShow.classList.add('hidden');
                if (iconHide) iconHide.classList.remove('hidden');
            } else {
                pwd.type = 'password';
                if (iconShow) iconShow.classList.remove('hidden');
                if (iconHide) iconHide.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
