<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piagam Penghargaan - {{ $member->name }} | {{ $schoolName }}</title>
    
    <!-- Google Fonts for Classical Luxury Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Certificate Dimensions for A4 Landscape */
        .certificate-sheet {
            width: 297mm;
            min-height: 210mm;
            height: 210mm;
            margin: 20px auto;
            position: relative;
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Ornate Gold Border Guilloche */
        .outer-gold-border {
            position: absolute;
            inset: 10mm;
            border: 3.5px solid #b45309;
            box-shadow: inset 0 0 0 2px #fde68a, inset 0 0 0 6px #d97706, inset 0 0 0 8px #fef3c7;
            pointer-events: none;
            z-index: 10;
        }

        .inner-thin-border {
            position: absolute;
            inset: 14mm;
            border: 1px dashed #d97706;
            pointer-events: none;
            z-index: 10;
        }

        /* Ornate Victorian Gold Corner Elements */
        .corner-ornament {
            position: absolute;
            width: 55px;
            height: 55px;
            z-index: 12;
            pointer-events: none;
        }
        .corner-tl {
            top: 10mm;
            left: 10mm;
            border-top: 6px double #b45309;
            border-left: 6px double #b45309;
            background: radial-gradient(circle at top left, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        }
        .corner-tr {
            top: 10mm;
            right: 10mm;
            border-top: 6px double #b45309;
            border-right: 6px double #b45309;
            background: radial-gradient(circle at top right, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        }
        .corner-bl {
            bottom: 10mm;
            left: 10mm;
            border-bottom: 6px double #b45309;
            border-left: 6px double #b45309;
            background: radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        }
        .corner-br {
            bottom: 10mm;
            right: 10mm;
            border-bottom: 6px double #b45309;
            border-right: 6px double #b45309;
            background: radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        }

        /* Certificate Watermark */
        .cert-watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.04;
            pointer-events: none;
            z-index: 1;
        }

        /* Gold Foil Embossed Seal Medal */
        .gold-seal-badge {
            width: 82px;
            height: 82px;
            background: radial-gradient(circle, #fde047 0%, #eab308 50%, #ca8a04 85%, #854d0e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 15px rgba(180, 83, 9, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.6);
            border: 2px dashed #78350f;
            position: relative;
        }
        .gold-seal-ribbon-left {
            position: absolute;
            bottom: -22px;
            left: 14px;
            width: 22px;
            height: 42px;
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            z-index: -1;
            box-shadow: 1px 3px 6px rgba(0,0,0,0.3);
        }
        .gold-seal-ribbon-right {
            position: absolute;
            bottom: -22px;
            right: 14px;
            width: 22px;
            height: 42px;
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            z-index: -1;
            box-shadow: 1px 3px 6px rgba(0,0,0,0.3);
        }

        /* Print Media Query */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .certificate-sheet {
                margin: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="p-0 sm:p-6 antialiased">

    <!-- Top Non-Print Control Toolbar -->
    <div class="no-print max-w-5xl mx-auto mb-6 p-4 rounded-2xl bg-slate-900 border border-slate-800 text-white flex flex-wrap items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('library.leaderboard') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Kembali ke Rangking</span>
            </a>
            <div>
                <h2 class="text-sm font-bold text-white">Preview Piagam Penghargaan Perpustakaan</h2>
                <p class="text-[11px] text-slate-400">Penerima: <span class="text-amber-300 font-bold">{{ $member->name }}</span> ({{ $member->member_class_or_title }})</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Piagam (A4 Landscape / PDF)</span>
            </button>
        </div>
    </div>

    <!-- CERTIFICATE SHEET (A4 LANDSCAPE: 297mm x 210mm) -->
    <div class="certificate-sheet p-12 sm:p-14 relative bg-gradient-to-br from-amber-50/20 via-white to-amber-50/30">
        
        <!-- Ornate Borders & Corners -->
        <div class="outer-gold-border"></div>
        <div class="inner-thin-border"></div>
        <div class="corner-ornament corner-tl"></div>
        <div class="corner-ornament corner-tr"></div>
        <div class="corner-ornament corner-bl"></div>
        <div class="corner-ornament corner-br"></div>

        <!-- Watermark -->
        <div class="cert-watermark">
            <svg class="w-96 h-96 text-amber-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.45 9L12 4.34 20.55 9 12 13.66 3.45 9zM5 12.2v4.8C5 19.2 8.13 21 12 21s7-1.8 7-4V12.2l-7 3.82-7-3.82z"/>
            </svg>
        </div>

        <!-- CONTENT LAYER -->
        <div class="relative z-10 flex flex-col justify-between h-full py-2">
            
            <!-- TOP KOP & HEADER -->
            <div class="text-center space-y-1">
                @if(!empty($schoolLogo))
                    <div class="flex justify-center mb-1">
                        <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}" class="h-14 w-auto object-contain drop-shadow-sm">
                    </div>
                @endif
                <div class="flex items-center justify-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-amber-600"></div>
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-600 uppercase tracking-[0.25em]" style="font-family: 'Cinzel', serif;">
                        PEMERINTAH PROVINSI {{ strtoupper($schoolProvince ?? 'JAWA BARAT') }} • DINAS PENDIDIKAN
                    </p>
                    <div class="w-1.5 h-1.5 rounded-full bg-amber-600"></div>
                </div>

                <h4 class="text-sm sm:text-base font-extrabold text-slate-800 tracking-wider uppercase" style="font-family: 'Cinzel', serif;">
                    {{ $schoolName }}
                </h4>

                <p class="text-[10px] text-amber-800 font-bold tracking-widest uppercase">
                    Perpustakaan Digital & Terpadu • Akreditasi Nasional
                </p>

                <!-- Certificate Title -->
                <div class="pt-2">
                    <h1 class="text-3xl sm:text-4xl font-black text-amber-800 tracking-wider uppercase drop-shadow-xs" style="font-family: 'Cinzel', serif;">
                        PIAGAM PENGHARGAAN
                    </h1>
                    <p class="text-[11px] text-slate-500 font-serif italic -mt-0.5">
                        Certificate of Literacy Achievement & Excellence
                    </p>
                    <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-amber-600 to-transparent mx-auto mt-1"></div>
                    <p class="text-[10px] font-mono text-slate-500 mt-1">
                        Nomor: <strong class="text-slate-800">{{ $certNumber }}</strong>
                    </p>
                </div>
            </div>

            <!-- RECIPIENT BODY -->
            <div class="text-center my-auto space-y-2 px-8">
                <p class="text-xs text-slate-600 italic font-serif">
                    Piagam Penghargaan ini dengan bangga dan rasa hormat dianugerahkan kepada:
                </p>

                <!-- Recipient Name -->
                <div class="py-1">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 tracking-wide underline decoration-amber-500/60 decoration-2 underline-offset-8" style="font-family: 'Playfair Display', Georgia, serif;">
                        {{ $member->name }}
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-2">
                        {{ $member->member_identity }} • {{ $member->member_class_or_title }}
                    </p>
                </div>

                <!-- Predicate / Award Title -->
                <div class="inline-block px-5 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/40 text-amber-900 font-black text-xs sm:text-sm tracking-wider uppercase mt-1" style="font-family: 'Cinzel', serif;">
                    🏆 {{ $predicate }}
                </div>

                <!-- Citation Text -->
                <p class="text-xs sm:text-[13px] text-slate-600 max-w-2xl mx-auto leading-relaxed pt-1">
                    Atas dedikasi, keaktifan luar biasa, serta kecintaan yang tinggi dalam membudayakan minat baca dan berkontribusi aktif dalam menyukseskan <strong>Gerakan Literasi Sekolah (GLS)</strong> di Perpustakaan {{ $schoolName }} pada <strong>{{ $period }}</strong>.
                </p>

                <!-- Literacy Stats Pill -->
                <div class="inline-flex items-center gap-4 text-[10px] text-slate-500 font-mono bg-slate-50 border border-slate-200 px-4 py-1 rounded-full">
                    <span>🏛️ {{ $offlineCount }}x Kunjungan Fisik</span>
                    <span>•</span>
                    <span>✨ {{ $onlineCount }}x Akses E-Book Online</span>
                    <span>•</span>
                    <span>📚 {{ $loanCount }} Buku Terpinjam</span>
                </div>
            </div>

            <!-- FOOTER SIGNATURES & SEALS (Kepala Sekolah Selalu di Kiri) -->
            <div class="grid grid-cols-3 items-end pt-2 px-6">
                <!-- Kepala Sekolah -->
                <div class="text-center text-xs">
                    <p class="text-[11px] text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-800 text-xs mt-0.5">Kepala {{ $schoolName }}</p>
                    <div class="h-14 flex items-center justify-center">
                        <!-- Signature -->
                        <span class="text-2xl text-amber-600 font-serif italic opacity-75" style="font-family: 'Great Vibes', cursive;">{{ $headmasterName }}</span>
                    </div>
                    <div class="w-44 h-px bg-slate-400 mx-auto"></div>
                    <p class="font-bold text-slate-900 text-xs mt-1">{{ $headmasterName }}</p>
                    <p class="text-[10px] text-slate-500 font-mono">NIP. {{ $headmasterNip }}</p>
                </div>

                <!-- Center Gold Seal Badge -->
                <div class="flex flex-col items-center justify-center">
                    <div class="gold-seal-badge">
                        <div class="gold-seal-ribbon-left"></div>
                        <div class="gold-seal-ribbon-right"></div>
                        <div class="text-center text-amber-950 font-black">
                            <div class="text-xs uppercase tracking-tighter" style="font-family: 'Cinzel', serif;">OFFICIAL</div>
                            <div class="text-base">★</div>
                            <div class="text-[8px] uppercase tracking-wider font-extrabold">SEAL</div>
                        </div>
                    </div>
                    <div class="text-[9px] font-mono text-slate-400 mt-7 uppercase tracking-wider">
                        Verifikasi: GLS-{{ strtoupper(substr(md5($certNumber), 0, 8)) }}
                    </div>
                </div>

                <!-- Kepala Perpustakaan -->
                <div class="text-center text-xs">
                    <p class="text-[11px] text-slate-500">Ditetapkan di: {{ \App\Models\SchoolSetting::get('school_subdistrict', 'Lengkong') }}</p>
                    <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-800 text-xs">Kepala Perpustakaan</p>
                    <div class="h-14 flex items-center justify-center">
                        <!-- Digital Stamp Placeholder / Signature -->
                        <span class="text-2xl text-indigo-400 font-serif italic opacity-70" style="font-family: 'Great Vibes', cursive;">{{ $librarianName }}</span>
                    </div>
                    <div class="w-40 h-px bg-slate-400 mx-auto"></div>
                    <p class="font-bold text-slate-900 text-xs mt-1">{{ $librarianName }}</p>
                    <p class="text-[10px] text-slate-500 font-mono">NIP. {{ $librarianNip }}</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

