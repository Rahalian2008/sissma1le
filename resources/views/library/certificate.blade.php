<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piagam Penghargaan - {{ $member->name }} | {{ $schoolName }}</title>
    
    <!-- Google Fonts for Classical Luxury Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           A4 LANDSCAPE EXACT SPECIFICATIONS (297mm x 210mm)
           Pure Self-Contained CSS - Immune to build / purge issues
           ============================================================ */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 24px 16px;
            background-color: #0b1120;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        /* Toolbar Kontrol Non-Print */
        .no-print-toolbar {
            width: 100%;
            max-width: 297mm;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
            color: #fff;
        }

        .btn-back {
            background: #334155;
            color: #e2e8f0;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #475569; color: #fff; }

        .btn-print-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0f172a;
            border: none;
            padding: 9px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.4);
            transition: all 0.2s;
        }
        .btn-print-gold:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            transform: translateY(-1px);
        }

        /* Lembar Piagam (297mm x 210mm) */
        .certificate-sheet {
            width: 297mm;
            height: 210mm;
            max-width: 297mm;
            max-height: 210mm;
            min-height: 210mm;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.85);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 14mm 22mm 18mm 22mm;
        }

        /* Texture Background Halus & Security Guilloche */
        .cert-bg-layer {
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at center, #ffffff 45%, #fdfbf7 85%, #f7f3ea 100%);
            z-index: 1;
            pointer-events: none;
        }

        .cert-guilloche-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.035;
            background-image: radial-gradient(#b45309 0.75px, transparent 0.75px), radial-gradient(#0f2942 0.75px, #ffffff 0.75px);
            background-size: 16px 16px;
            background-position: 0 0, 8px 8px;
            z-index: 2;
            pointer-events: none;
        }

        /* BINGKAI EMAS DAN ROYAL NAVY GANDA (AUTHENTIC LUXURY FRAME) */
        .frame-outer-navy {
            position: absolute;
            inset: 6mm;
            border: 2.5px solid #0f2942;
            z-index: 10;
            pointer-events: none;
        }

        .frame-gold-border {
            position: absolute;
            inset: 7.5mm;
            border: 3px solid #b45309;
            box-shadow: 
                inset 0 0 0 1px #fef3c7,
                inset 0 0 0 3px #d97706,
                inset 0 0 0 4.5px #fde68a;
            z-index: 11;
            pointer-events: none;
        }

        .frame-inner-hairline {
            position: absolute;
            inset: 10.5mm;
            border: 1px solid #d97706;
            z-index: 12;
            pointer-events: none;
        }

        .frame-corner-square {
            position: absolute;
            width: 14px;
            height: 14px;
            border: 1.5px solid #b45309;
            background: #ffffff;
            z-index: 13;
            pointer-events: none;
            transform: rotate(45deg);
        }
        .cs-tl { top: 9.8mm; left: 9.8mm; }
        .cs-tr { top: 9.8mm; right: 9.8mm; }
        .cs-bl { bottom: 9.8mm; left: 9.8mm; }
        .cs-br { bottom: 9.8mm; right: 9.8mm; }

        /* SVG Corner Ornaments */
        .corner-filigree {
            position: absolute;
            width: 52px;
            height: 52px;
            z-index: 14;
            pointer-events: none;
        }
        .cf-tl { top: 6mm; left: 6mm; }
        .cf-tr { top: 6mm; right: 6mm; transform: scaleX(-1); }
        .cf-bl { bottom: 6mm; left: 6mm; transform: scaleY(-1); }
        .cf-br { bottom: 6mm; right: 6mm; transform: scale(-1); }

        /* Watermark Lambang Tut Wuri / Buku di Tengah */
        .watermark-emblem {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            height: 280px;
            opacity: 0.04;
            z-index: 3;
            pointer-events: none;
        }

        /* Content Layer Utama */
        .cert-content {
            position: relative;
            z-index: 20;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            width: 100%;
        }

        /* HEADER KOP */
        .header-kop {
            text-align: center;
        }

        .school-logo-img {
            height: 44px !important;
            max-height: 44px !important;
            width: auto !important;
            object-fit: contain;
            display: block;
            margin: 0 auto 3px auto;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.15));
        }

        .kop-gov {
            font-family: 'Cinzel', serif;
            font-size: 9pt;
            font-weight: 700;
            color: #334155;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.2;
        }

        .kop-school {
            font-family: 'Cinzel', serif;
            font-size: 13pt;
            font-weight: 900;
            color: #0f2942;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin: 2px 0 1px 0;
            line-height: 1.15;
        }

        .kop-sub {
            font-size: 7.5pt;
            font-weight: 700;
            color: #92400e;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
        }

        .divider-ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 4px auto 3px auto;
            max-width: 420px;
        }
        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #b45309, transparent);
        }
        .divider-diamond {
            color: #d97706;
            font-size: 10pt;
            line-height: 1;
        }

        /* CERTIFICATE TITLE */
        .title-block {
            text-align: center;
        }

        .cert-main-title {
            font-family: 'Cinzel', serif;
            font-size: 23pt;
            font-weight: 900;
            color: #0f2942;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.1;
            text-shadow: 0 1px 2px rgba(180, 83, 9, 0.2);
        }

        .cert-sub-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 8.5pt;
            font-style: italic;
            color: #64748b;
            letter-spacing: 1px;
            margin: 1px 0 2px 0;
        }

        .cert-reg-number {
            font-family: 'Plus Jakarta Sans', monospace;
            font-size: 8pt;
            color: #475569;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .cert-reg-number strong {
            color: #0f2942;
            font-weight: 700;
        }

        /* RECIPIENT BODY */
        .recipient-section {
            text-align: center;
            margin: 1px 0;
        }

        .awarded-to-text {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 9.5pt;
            font-style: italic;
            color: #475569;
            margin: 0 0 2px 0;
        }

        .recipient-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 23pt;
            font-weight: 800;
            color: #0f2942;
            letter-spacing: 1.5px;
            margin: 1px 0 2px 0;
            line-height: 1.1;
        }

        .recipient-name-underline {
            width: 140px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #d97706, #b45309, #d97706, transparent);
            margin: 0 auto 3px auto;
        }

        .recipient-meta {
            font-size: 9pt;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }

        /* PREDICATE RIBBON BADGE */
        .predicate-ribbon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 3px 18px;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
            border: 1.5px solid #d97706;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(180, 83, 9, 0.15);
            margin: 2px auto 3px auto;
        }
        .predicate-text {
            font-family: 'Cinzel', serif;
            font-size: 9.5pt;
            font-weight: 800;
            color: #78350f;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* CITATION NARRATIVE */
        .citation-text {
            font-size: 9pt;
            line-height: 1.35;
            color: #334155;
            max-width: 680px;
            margin: 2px auto;
            text-align: center;
        }

        /* LITERACY STATS PILL */
        .stats-strip {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            padding: 2.5px 14px;
            border-radius: 16px;
            margin: 2px auto 0 auto;
            font-size: 7.5pt;
            color: #475569;
            font-weight: 600;
        }
        .stats-strip span.dot {
            color: #cbd5e1;
        }

        /* FOOTER SIGNATURES & OFFICIAL SEAL */
        .signatures-row {
            display: grid;
            grid-template-columns: 1fr 130px 1fr;
            align-items: flex-end;
            margin-top: 2px;
            padding: 0 12px;
        }

        .sig-block {
            text-align: center;
        }
        .sig-role-title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #0f2942;
            margin: 0;
            line-height: 1.25;
        }
        .sig-location-date {
            font-size: 7.5pt;
            color: #64748b;
            margin: 0 0 1px 0;
            line-height: 1.2;
        }
        .sig-space {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-rule {
            width: 175px;
            height: 1px;
            background: #64748b;
            margin: 0 auto 3px auto;
        }
        .sig-name {
            font-size: 9pt;
            font-weight: 800;
            color: #0f2942;
            margin: 0;
            line-height: 1.15;
        }
        .sig-nip {
            font-family: monospace;
            font-size: 7.5pt;
            color: #64748b;
            margin: 1px 0 0 0;
        }

        /* OFFICIAL GOLD FOIL MEDAL SEAL */
        .seal-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 2px;
        }
        .gold-seal {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: radial-gradient(circle, #fef08a 0%, #facc15 40%, #ca8a04 80%, #854d0e 100%);
            border: 2px dashed #78350f;
            box-shadow: 
                0 4px 10px rgba(180, 83, 9, 0.35),
                inset 0 2px 4px rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 5;
        }
        .gold-seal-text-top {
            font-family: 'Cinzel', serif;
            font-size: 6pt;
            font-weight: 900;
            color: #451a03;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .gold-seal-star {
            color: #78350f;
            font-size: 8pt;
            line-height: 1;
            margin: 1px 0;
        }
        .gold-seal-text-bot {
            font-size: 5pt;
            font-weight: 800;
            color: #451a03;
            letter-spacing: 1px;
            line-height: 1;
        }
        .seal-ribbon-left {
            position: absolute;
            bottom: -11px;
            left: 10px;
            width: 13px;
            height: 22px;
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            z-index: 4;
            box-shadow: 1px 2px 4px rgba(0,0,0,0.25);
        }
        .seal-ribbon-right {
            position: absolute;
            bottom: -11px;
            right: 10px;
            width: 13px;
            height: 22px;
            background: linear-gradient(135deg, #991b1b, #7f1d1d);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            z-index: 4;
            box-shadow: 1px 2px 4px rgba(0,0,0,0.25);
        }
        .seal-cert-code {
            font-size: 6.5pt;
            font-family: monospace;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-top: 11px;
            text-transform: uppercase;
        }

        /* PRINT MEDIA QUERY KHUSUS */
        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .certificate-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                max-width: 297mm !important;
                max-height: 210mm !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOP TOOLBAR (NON-PRINT) -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('library.leaderboard') }}" class="btn-back">
                <span>&larr;</span>
                <span>Kembali ke Leaderboard</span>
            </a>
            <div>
                <div style="font-size: 13px; font-weight: 800; color: #fff;">Piagam Penghargaan Literasi Perpustakaan Digital</div>
                <div style="font-size: 11px; color: #94a3b8;">Penerima: <strong style="color: #fbbf24;">{{ $member->name }}</strong> ({{ $member->member_class_or_title }})</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" onclick="window.print()" class="btn-print-gold">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                <span>Cetak Piagam (A4 Landscape / PDF)</span>
            </button>
        </div>
    </div>

    <!-- CERTIFICATE SHEET (A4 LANDSCAPE: 297mm x 210mm) -->
    <div class="certificate-sheet">
        
        <!-- Texture & Guilloche Background -->
        <div class="cert-bg-layer"></div>
        <div class="cert-guilloche-pattern"></div>

        <!-- BORDER UTAMA (ROYAL NAVY + GOLD FRAME) -->
        <div class="frame-outer-navy"></div>
        <div class="frame-gold-border"></div>
        <div class="frame-inner-hairline"></div>

        <!-- Ornamen Sudut Kotak Diamond -->
        <div class="frame-corner-square cs-tl"></div>
        <div class="frame-corner-square cs-tr"></div>
        <div class="frame-corner-square cs-bl"></div>
        <div class="frame-corner-square cs-br"></div>

        <!-- SVG Classical Filigree Corner Flourishes -->
        <svg class="corner-filigree cf-tl" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10H60C45 15 35 25 30 40C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <path d="M10 10V60C15 45 25 35 40 30C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <circle cx="20" cy="20" r="4" fill="#d97706"/>
            <path d="M12 12C28 12 40 24 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 12C12 28 24 40 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <svg class="corner-filigree cf-tr" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10H60C45 15 35 25 30 40C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <path d="M10 10V60C15 45 25 35 40 30C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <circle cx="20" cy="20" r="4" fill="#d97706"/>
            <path d="M12 12C28 12 40 24 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 12C12 28 24 40 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <svg class="corner-filigree cf-bl" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10H60C45 15 35 25 30 40C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <path d="M10 10V60C15 45 25 35 40 30C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <circle cx="20" cy="20" r="4" fill="#d97706"/>
            <path d="M12 12C28 12 40 24 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 12C12 28 24 40 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <svg class="corner-filigree cf-br" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10H60C45 15 35 25 30 40C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <path d="M10 10V60C15 45 25 35 40 30C25 25 15 15 10 10Z" fill="#b45309" opacity="0.3"/>
            <circle cx="20" cy="20" r="4" fill="#d97706"/>
            <path d="M12 12C28 12 40 24 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 12C12 28 24 40 40 40" stroke="#b45309" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <!-- Watermark Buku & Cahaya di Tengah -->
        <svg class="watermark-emblem" viewBox="0 0 24 24" fill="#0f2942">
            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.45 9L12 4.34 20.55 9 12 13.66 3.45 9zM5 12.2v4.8C5 19.2 8.13 21 12 21s7-1.8 7-4V12.2l-7 3.82-7-3.82z"/>
        </svg>

        <!-- KONTEN PIAGAM UTAMA -->
        <div class="cert-content">
            
            <!-- 1. HEADER KOP RESMI -->
            <div class="header-kop">
                @if(!empty($schoolLogo))
                    <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}" class="school-logo-img" style="height: 44px; max-height: 44px; width: auto; object-fit: contain; margin: 0 auto 3px auto; display: block;">
                @endif
                <p class="kop-gov">PEMERINTAH PROVINSI {{ strtoupper($schoolProvince ?? 'JAWA BARAT') }} • DINAS PENDIDIKAN</p>
                <h4 class="kop-school">{{ $schoolName }}</h4>
                <p class="kop-sub">PERPUSTAKAAN DIGITAL TERPADU • AKREDITASI NASIONAL</p>
                
                <div class="divider-ornament">
                    <div class="divider-line"></div>
                    <div class="divider-diamond">✦</div>
                    <div class="divider-line"></div>
                </div>
            </div>

            <!-- 2. JUDUL PIAGAM -->
            <div class="title-block">
                <h1 class="cert-main-title">PIAGAM PENGHARGAAN</h1>
                <div class="cert-sub-title">Certificate of Literacy Achievement & Excellence</div>
                <div class="cert-reg-number">Nomor: <strong>{{ $certNumber }}</strong></div>
            </div>

            <!-- 3. PENERIMA PENGHARGAAN -->
            <div class="recipient-section">
                <p class="awarded-to-text">Piagam Penghargaan ini dengan bangga dan rasa hormat dianugerahkan kepada:</p>
                <div class="recipient-name">{{ $member->name }}</div>
                <div class="recipient-name-underline"></div>
                <div class="recipient-meta">{{ $member->member_identity }} • {{ $member->member_class_or_title }}</div>

                <!-- Predikat Juara / Duta Literasi -->
                <div class="predicate-ribbon">
                    <span style="color: #b45309; font-size: 11pt;">🏆</span>
                    <span class="predicate-text">{{ $predicate }}</span>
                    <span style="color: #b45309; font-size: 11pt;">🏆</span>
                </div>

                <!-- Pernyataan Apresiasi -->
                <div class="citation-text">
                    Atas dedikasi, keaktifan luar biasa, serta kecintaan yang tinggi dalam membudayakan minat baca dan berkontribusi aktif dalam menyukseskan <strong>Gerakan Literasi Sekolah (GLS)</strong> di Perpustakaan {{ $schoolName }} pada <strong>{{ $period }}</strong>.
                </div>

                <!-- Statistik Literasi (Kunjungan, E-book, Peminjaman) -->
                <div class="stats-strip">
                    <span>🏛️ <strong>{{ $offlineCount }}x</strong> Kunjungan Fisik</span>
                    <span class="dot">•</span>
                    <span>📱 <strong>{{ $onlineCount }}x</strong> Akses E-Book Online</span>
                    <span class="dot">•</span>
                    <span>📚 <strong>{{ $loanCount }}</strong> Buku Dipinjam</span>
                </div>
            </div>

            <!-- 4. TANDA TANGAN RESMI & SEGEL EMAS -->
            <div class="signatures-row">
                
                <!-- Kepala Sekolah (Kiri) -->
                <div class="sig-block">
                    <p class="sig-location-date">&nbsp;</p>
                    <p class="sig-role-title">Mengetahui,<br>Kepala {{ $schoolName }}</p>
                    <div class="sig-space"></div>
                    <div class="sig-rule"></div>
                    <div class="sig-name">{{ $headmasterName }}</div>
                    <div class="sig-nip">NIP. {{ $headmasterNip }}</div>
                </div>

                <!-- Segel Emas Resmi Medali 3D (Tengah) -->
                <div class="seal-wrapper">
                    <div class="gold-seal">
                        <div class="seal-ribbon-left"></div>
                        <div class="seal-ribbon-right"></div>
                        <div class="gold-seal-text-top">OFFICIAL</div>
                        <div class="gold-seal-star">★</div>
                        <div class="gold-seal-text-bot">SEAL</div>
                    </div>
                    <div class="seal-cert-code">ID: GLS-{{ strtoupper(substr(md5($certNumber), 0, 8)) }}</div>
                </div>

                <!-- Kepala Perpustakaan (Kanan) -->
                <div class="sig-block">
                    <p class="sig-location-date">Ditetapkan di: {{ \App\Models\SchoolSetting::get('school_subdistrict', 'Lengkong') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="sig-role-title">Kepala Perpustakaan<br>&nbsp;</p>
                    <div class="sig-space"></div>
                    <div class="sig-rule"></div>
                    <div class="sig-name">{{ $librarianName }}</div>
                    <div class="sig-nip">NIP. {{ $librarianNip }}</div>
                </div>

            </div>

        </div>
    </div>

</body>
</html>
