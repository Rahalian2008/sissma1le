<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantau Status Tiket Pengaduan - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $compFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
        $compLogo = $schoolLogoUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $compFavicon }}">
    <link rel="shortcut icon" href="{{ $compFavicon }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Header -->
    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-18 sm:h-20 flex items-center justify-between gap-4">
            <a href="{{ url('/pengaduan') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 to-indigo-600 p-0.5 shadow-md shrink-0 flex items-center justify-center">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                        @if($compLogo)
                            <img src="{{ $compLogo }}" alt="Logo {{ $school->name ?? 'Sekolah' }}" class="w-full h-full object-contain">
                        @else
                            <span class="font-heading font-black text-white text-base">S1L</span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="font-heading font-black text-slate-900 dark:text-white text-base tracking-tight leading-none">
                        {{ $school->name ?? 'SMAN 1 LENGKONG' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                        Pelacakan Status Pengaduan Publik
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ url('/pengaduan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 border border-indigo-200 dark:border-indigo-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Pengaduan Baru</span>
                </a>
                <a href="{{ route('website.home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="hidden sm:inline">Website</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-3xl mx-auto px-4 sm:px-6 py-8 w-full space-y-6">

        <!-- Search Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 dark:border-slate-800 space-y-4">
            <div class="space-y-1">
                <h2 class="text-lg font-heading font-bold text-slate-900 dark:text-white">
                    Masukkan Nomor Tiket Pengaduan
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Gunakan kode tiket unik yang Anda peroleh saat mengirim pengaduan (contoh: <code>ADU-20261125-8F2A</code>).
                </p>
            </div>

            <form action="{{ url('/pengaduan/tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <input type="text" name="ticket" value="{{ $ticket }}" required placeholder="ADU-YYYYMMDD-XXXX" class="w-full text-sm font-mono font-bold uppercase tracking-wider bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl px-4 py-3 text-slate-900 dark:text-white placeholder:normal-case placeholder:font-sans placeholder:font-normal focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Tiket</span>
                </button>
            </form>
        </div>

        @if($searched)
            @if($complaint)
                <!-- Found Result -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 dark:border-slate-800 space-y-6">
                    
                    <!-- Header Info -->
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
                        <div class="space-y-1">
                            <div class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $complaint->ticket_number }}
                            </div>
                            <h3 class="text-lg font-heading font-bold text-slate-900 dark:text-white">
                                {{ $complaint->title }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                Dikirim pada: {{ $complaint->created_at->translatedFormat('l, d F Y - H:i') }} WIB &bull; Kategori: <strong>{{ $complaint->category_label }}</strong>
                            </p>
                        </div>
                        <div>
                            @php $badge = $complaint->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }}">
                                <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Timeline Progress -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Progres Penanganan
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                            <!-- Step 1 -->
                            <div class="p-3 rounded-2xl border {{ $complaint->created_at ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700' }}">
                                <div class="font-bold text-indigo-700 dark:text-indigo-300">1. Terkirim</div>
                                <div class="text-[10px] text-slate-500 mt-1">{{ $complaint->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <!-- Step 2 -->
                            <div class="p-3 rounded-2xl border {{ in_array($complaint->status, ['DITELAAH', 'DIPROSES', 'SELESAI']) ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 text-slate-400' }}">
                                <div class="font-bold">2. Ditelaah</div>
                                <div class="text-[10px] mt-1">{{ in_array($complaint->status, ['DITELAAH', 'DIPROSES', 'SELESAI']) ? 'Pimpinan Sekolah' : 'Menunggu' }}</div>
                            </div>
                            <!-- Step 3 -->
                            <div class="p-3 rounded-2xl border {{ in_array($complaint->status, ['DIPROSES', 'SELESAI']) ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 text-slate-400' }}">
                                <div class="font-bold">3. Tindak Lanjut</div>
                                <div class="text-[10px] mt-1">{{ in_array($complaint->status, ['DIPROSES', 'SELESAI']) ? 'Sedang Berjalan' : '-' }}</div>
                            </div>
                            <!-- Step 4 -->
                            <div class="p-3 rounded-2xl border {{ $complaint->status === 'SELESAI' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 text-slate-400' }}">
                                <div class="font-bold">4. Selesai</div>
                                <div class="text-[10px] mt-1">{{ $complaint->status === 'SELESAI' ? ($complaint->handled_at?->format('d/m/Y H:i') ?? 'Tuntas') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggapan / Disposisi Resmi -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span>Tanggapan Resmi Pimpinan Sekolah:</span>
                        </div>
                        @if($complaint->disposition_notes)
                            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed pl-6">
                                {{ $complaint->disposition_notes }}
                            </p>
                            @if($complaint->handled_at)
                                <div class="text-[10px] text-slate-400 pl-6 pt-1">
                                    Diperbarui pada: {{ $complaint->handled_at->translatedFormat('d F Y H:i') }} WIB
                                </div>
                            @endif
                        @else
                            <p class="text-xs text-slate-500 italic pl-6">
                                Pengaduan Anda saat ini telah diterima dan sedang dalam antrean telaah oleh Kepala Sekolah & Wakil Kepala Sekolah. Tanggapan resmi akan diperbarui pada halaman ini.
                            </p>
                        @endif
                    </div>

                </div>
            @else
                <!-- Not Found -->
                <div class="p-8 rounded-3xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-rose-900 dark:text-rose-200">
                        Tiket Tidak Ditemukan
                    </h3>
                    <p class="text-xs text-rose-700 dark:text-rose-300 max-w-md mx-auto">
                        Nomor tiket <strong>"{{ $ticket }}"</strong> tidak ditemukan dalam database pengaduan. Pastikan format nomor tiket sudah tepat (contoh: <code>ADU-20261125-8F2A</code>).
                    </p>
                </div>
            @endif
        @endif

    </main>

    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} {{ $school->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Informasi Sekolah Terpadu (SIS-SMA1LE)
    </footer>

</body>
</html>
