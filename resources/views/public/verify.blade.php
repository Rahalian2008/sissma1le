<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Verifikasi Resmi SIKATAS SMA1LE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between p-4 sm:p-6 antialiased">
    <!-- Header Navigation -->
    <header class="max-w-2xl mx-auto w-full py-4 flex items-center justify-between border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-linear-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center font-black text-base shadow-sm">
                S
            </div>
            <div>
                <h1 class="text-sm font-black text-slate-900 leading-tight">SIKATAS SMA1LE</h1>
                <p class="text-[11px] text-slate-500">Layanan Verifikasi Dokumen & Aset Digital</p>
            </div>
        </div>
        <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
            Portal Masuk Staff &rarr;
        </a>
    </header>

    <!-- Main Verification Card -->
    <main class="max-w-2xl mx-auto w-full my-6">
        <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden">
            @if($isValid)
                <!-- Valid State Header -->
                <div class="bg-linear-to-r from-emerald-600 to-teal-700 p-6 sm:p-8 text-white text-center space-y-2">
                    <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto text-white shadow-inner mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-[11px] font-bold tracking-wider uppercase">Keaslian Terverifikasi</span>
                    <h2 class="text-lg sm:text-xl font-black">{{ $title }}</h2>
                    <p class="text-xs text-emerald-100 max-w-md mx-auto">
                        Dokumen/entitas ini terdaftar secara sah dan valid dalam pangkalan data resmi {{ $school->name ?? 'SMA Negeri 1 Teladan' }}.
                    </p>
                </div>

                <!-- Verification Body Content -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="rounded-2xl border border-slate-200 overflow-hidden divide-y divide-slate-100">
                        @foreach($details as $label => $val)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs hover:bg-slate-50/50 transition">
                                <span class="font-semibold text-slate-500">{{ $label }}</span>
                                <span class="font-bold text-slate-900 sm:text-right">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                            <span>Waktu Verifikasi:</span>
                            <span class="font-bold text-slate-700">{{ now()->translatedFormat('d F Y H:i:s') }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                            <span>Otoritas Penjamin:</span>
                            <span class="font-bold text-slate-700">{{ $school->name ?? 'SMA Negeri 1 Teladan' }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-500 leading-relaxed">
                            Verifikasi elektronik ini dibangkitkan secara otomatis melalui pemindaian QR Code SIKATAS SMA1LE dan memiliki kekuatan pembuktian digital yang sah.
                        </div>
                    </div>
                </div>
            @else
                <!-- Invalid State Header -->
                <div class="bg-linear-to-r from-rose-600 to-red-700 p-6 sm:p-8 text-white text-center space-y-2">
                    <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto text-white shadow-inner mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-[11px] font-bold tracking-wider uppercase">Tidak Valid / Ditolak</span>
                    <h2 class="text-lg sm:text-xl font-black">Dokumen Tidak Ditemukan</h2>
                    <p class="text-xs text-rose-100 max-w-md mx-auto">
                        Kode token atau nomor berkas yang Anda pindai tidak terdaftar di pangkalan data resmi kami atau telah kedaluwarsa.
                    </p>
                </div>

                <div class="p-6 sm:p-8 text-center space-y-4">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Harap periksa kembali keaslian fisik dokumen atau hubungi Tata Usaha {{ $school->name ?? 'SMA Negeri 1 Teladan' }} untuk konfirmasi lebih lanjut.
                    </p>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Kembali ke Halaman Utama
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-2xl mx-auto w-full py-4 text-center text-xs text-slate-400 border-t border-slate-200">
        &copy; {{ date('Y') }} SIKATAS SMA1LE &bull; {{ $school->name ?? 'SMA Negeri 1 Teladan' }}. Hak Cipta Dilindungi.
    </footer>
</body>
</html>
