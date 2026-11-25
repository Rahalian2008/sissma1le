<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Berhasil Dikirim - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $compFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col items-center justify-center p-4 antialiased">

    <div class="max-w-xl w-full bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-200/80 dark:border-slate-800 text-center space-y-6">
        
        <!-- Icon Success Checkmark -->
        <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center shadow-lg shadow-emerald-500/20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Terkirim ke Pimpinan Sekolah
            </span>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 dark:text-white">
                Terima Kasih Atas Laporan Anda!
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
                Pengaduan Anda telah terdaftar dalam sistem dan diteruskan secara rahasia ke Kepala Sekolah & Jajaran Wakil Kepala Sekolah.
            </p>
        </div>

        <!-- Ticket Badge Box -->
        <div class="p-5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 space-y-3">
            <div class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                Nomor Tiket Pengaduan Anda
            </div>
            <div class="text-2xl sm:text-3xl font-mono font-black text-indigo-950 dark:text-indigo-200 tracking-wider" id="ticket_text">
                {{ $complaint->ticket_number }}
            </div>
            <button type="button" onclick="copyTicket()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-slate-700 border border-indigo-200 dark:border-indigo-700 shadow-sm transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                <span id="copy_btn_text">Salin Nomor Tiket</span>
            </button>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Simpan nomor tiket ini untuk melacak perkembangan telaah dan tindak lanjut dari pihak sekolah.
            </p>
        </div>

        <!-- Summary Details -->
        <div class="text-left p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Kategori:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ $complaint->category_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Judul:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ $complaint->title }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Identitas:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ $complaint->display_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status Awal:</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    Menunggu Telaah Pimpinan
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ url('/pengaduan/tracking?ticket=' . $complaint->ticket_number) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Pantau Status Pengaduan</span>
            </a>
            <a href="{{ route('website.home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-6 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition">
                <span>Kembali ke Website</span>
            </a>
        </div>
    </div>

    <script>
        function copyTicket() {
            const ticket = document.getElementById('ticket_text').innerText.trim();
            navigator.clipboard.writeText(ticket).then(() => {
                const btn = document.getElementById('copy_btn_text');
                btn.textContent = 'Nomor Tiket Disalin!';
                setTimeout(() => {
                    btn.textContent = 'Salin Nomor Tiket';
                }, 3000);
            });
        }
    </script>
</body>
</html>
