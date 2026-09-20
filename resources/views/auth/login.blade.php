<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ $appName }} | {{ $schoolName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if(!empty($appLogoUrl))
        <link rel="icon" type="image/png" href="{{ $appLogoUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
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
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 bg-slate-950/60 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                        placeholder="Siswa: NISN | Guru/Admin: NIP | Password">
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

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} {{ $schoolName }}. Hak Cipta Dilindungi.
        </p>
    </div>
</body>
</html>
