<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Presensi Siswa - {{ $school['name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/qrious.min.js') }}"></script>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: 5mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0f172a;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Top Bar for Actions (Hidden on Print) */
        .no-print-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #1e293b;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            border-bottom: 1px solid #334155;
        }

        .no-print-bar button, .no-print-bar a, .no-print-bar select {
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: white;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.4);
        }

        .btn-print:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-back {
            background: #334155;
            color: #e2e8f0;
        }

        .btn-back:hover {
            background: #475569;
        }

        .filter-select {
            background: #0f172a;
            color: #fff;
            border: 1px solid #475569 !important;
        }

        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #0f172a;
            border-radius: 8px;
            padding: 2px;
            border: 1px solid #334155;
        }

        .paper-toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .paper-toggle-btn.active {
            background: #6366f1;
            color: #ffffff;
        }

        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }

        /* Paper Container */
        .paper-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 24px auto;
            background: white;
            padding: 12mm 10mm;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            border-radius: 12px;
        }

        /* Card Grid Layout: 2 columns on A4 page (fits 8-10 cards per sheet) */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 85.6mm);
            gap: 6mm 10mm;
            justify-content: center;
        }

        /* Standard CR80 Card: 85.6mm x 53.98mm (KTP/ATM/Business Card size) */
        .id-card-wrapper {
            width: 85.6mm;
            height: 53.98mm;
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
        }

        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            border-radius: 3.5mm;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 0.3mm solid #cbd5e1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Front Face Styling */
        .card-front {
            background: linear-gradient(135deg, #09132b 0%, #152044 50%, #0c1b3a 100%);
            color: white;
            padding: 3mm 3.5mm;
        }

        /* Diagonal Accent Graphic */
        .card-front::before {
            content: '';
            position: absolute;
            top: -20mm;
            right: -15mm;
            width: 45mm;
            height: 60mm;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.25) 0%, rgba(99, 102, 241, 0.05) 70%, transparent 100%);
            transform: rotate(25deg);
            pointer-events: none;
        }

        .card-front::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1.2mm;
            background: linear-gradient(90deg, #38bdf8, #818cf8, #34d399, #f59e0b);
        }

        /* Card Header */
        .card-header {
            display: flex;
            align-items: center;
            gap: 2.5mm;
            border-bottom: 0.25mm solid rgba(255,255,255,0.15);
            padding-bottom: 1.5mm;
            position: relative;
            z-index: 2;
        }

        .header-logo {
            width: 7.5mm;
            height: 7.5mm;
            object-fit: contain;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3));
        }

        .header-text {
            flex: 1;
            line-height: 1.1;
        }

        .instansi-sub {
            font-size: 5pt;
            font-weight: 700;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .school-name {
            font-size: 7pt;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        .card-purpose-badge {
            font-size: 4.5pt;
            font-weight: 800;
            color: #38bdf8;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        /* Card Body */
        .card-body {
            display: flex;
            align-items: center;
            gap: 2.8mm;
            margin-top: 1.8mm;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        /* Student Photo */
        .photo-box {
            width: 17mm;
            height: 22mm;
            border-radius: 2mm;
            border: 0.4mm solid #38bdf8;
            background: #1e293b;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-fallback {
            font-weight: 900;
            font-size: 11pt;
            color: #38bdf8;
        }

        /* Details */
        .student-details {
            flex: 1;
            min-width: 0;
            line-height: 1.25;
        }

        .student-name {
            font-size: 7.8pt;
            font-weight: 900;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 38mm;
            letter-spacing: -0.1px;
        }

        .data-row {
            display: flex;
            font-size: 5.5pt;
            margin-top: 0.7mm;
        }

        .data-label {
            width: 11mm;
            color: #94a3b8;
            font-weight: 600;
        }

        .data-val {
            color: #f1f5f9;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', monospace;
        }

        .badge-class {
            display: inline-block;
            margin-top: 1mm;
            padding: 0.4mm 1.6mm;
            background: rgba(56, 189, 248, 0.18);
            border: 0.2mm solid rgba(56, 189, 248, 0.4);
            border-radius: 1mm;
            color: #38bdf8;
            font-size: 5pt;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Tech Section (QR Code & RFID Chip) */
        .tech-section {
            width: 18mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            gap: 1mm;
        }

        .qr-canvas-box {
            width: 15.5mm;
            height: 15.5mm;
            background: white;
            padding: 0.8mm;
            border-radius: 1.5mm;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-canvas-box canvas, .qr-canvas-box img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* RFID EMV Chip Icon Graphic */
        .rfid-chip {
            width: 9.5mm;
            height: 7.5mm;
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #d97706 100%);
            border-radius: 1.2mm;
            border: 0.2mm solid #78350f;
            position: relative;
            box-shadow: inset 0 0 1px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rfid-chip::after {
            content: '';
            position: absolute;
            width: 7mm;
            height: 5mm;
            border: 0.2mm solid rgba(120, 53, 15, 0.6);
            border-radius: 0.8mm;
        }

        .rfid-uid-tag {
            font-size: 4pt;
            font-family: monospace;
            color: #cbd5e1;
            font-weight: 700;
            letter-spacing: 0.2px;
            text-align: center;
            max-width: 18mm;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wave-symbol {
            display: inline-block;
            font-size: 6pt;
            color: #38bdf8;
            font-weight: bold;
        }

        /* Card Footer */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 4.6pt;
            color: #94a3b8;
            border-top: 0.2mm solid rgba(255,255,255,0.1);
            padding-top: 1mm;
            position: relative;
            z-index: 2;
        }

        .footer-status {
            color: #34d399;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 1mm;
        }

        .status-dot {
            width: 1.2mm;
            height: 1.2mm;
            border-radius: 50%;
            background: #34d399;
        }

        /* Back Face Styling */
        .card-back {
            background: #ffffff;
            color: #1e293b;
            padding: 3mm 3.5mm;
            border: 0.3mm solid #cbd5e1;
        }

        .back-header {
            text-align: center;
            border-bottom: 0.25mm solid #cbd5e1;
            padding-bottom: 1.2mm;
        }

        .back-title {
            font-size: 6pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.3px;
        }

        .back-sub {
            font-size: 4.5pt;
            color: #64748b;
        }

        .rules-list {
            margin-top: 1.5mm;
            font-size: 4.5pt;
            color: #334155;
            line-height: 1.35;
            list-style: decimal inside;
            padding-left: 0.5mm;
        }

        .rules-list li {
            margin-bottom: 0.6mm;
        }

        .back-bottom {
            margin-top: auto;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            border-top: 0.2mm solid #e2e8f0;
            padding-top: 1mm;
        }

        .school-contact {
            font-size: 4.2pt;
            color: #64748b;
            line-height: 1.2;
            max-width: 44mm;
        }

        .signature-box {
            text-align: center;
            width: 32mm;
        }

        .signature-date {
            font-size: 4.2pt;
            color: #64748b;
        }

        .signature-title {
            font-size: 4.5pt;
            font-weight: 700;
            color: #0f172a;
        }

        .signature-name {
            font-size: 5pt;
            font-weight: 900;
            color: #0f172a;
            text-decoration: underline;
            margin-top: 3.5mm;
        }

        .signature-nip {
            font-size: 4.2pt;
            color: #64748b;
        }

        /* Cutting marks guide */
        .cutting-guides {
            position: absolute;
            inset: -1.5mm;
            border: 0.2mm dashed #94a3b8;
            pointer-events: none;
            border-radius: 4mm;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 5mm 6mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .cards-grid {
                gap: 5mm 8mm !important;
            }

            .id-card {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('attendance.cards.index') }}" class="btn-back">
                &larr; Kembali ke Pusat Kartu
            </a>
            <span style="font-size: 13px; font-weight: 800; color: #fff;">
                Lembar Cetak Kartu Presensi ({{ $students->count() }} Siswa)
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4
                </a>
            </div>

            <!-- Pilihan Tipe Kartu -->
            <form method="GET" action="{{ url()->current() }}" style="display: flex; align-items: center; gap: 8px;">
                <input type="hidden" name="paper" value="{{ $isF4 ? 'f4' : 'a4' }}">
                @if(request('class_id'))
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                @endif
                @if(request('student_id'))
                    <input type="hidden" name="student_id" value="{{ request('student_id') }}">
                @endif

                <select name="mode" onchange="this.form.submit()" class="filter-select">
                    <option value="combo" {{ $mode === 'combo' ? 'selected' : '' }}>Kombinasi (QR Code + RFID)</option>
                    <option value="qr" {{ $mode === 'qr' ? 'selected' : '' }}>Khusus QR Code (NISN)</option>
                    <option value="rfid" {{ $mode === 'rfid' ? 'selected' : '' }}>Khusus Chip RFID</option>
                </select>

                <select name="side" onchange="this.form.submit()" class="filter-select">
                    <option value="both" {{ $side === 'both' ? 'selected' : '' }}>Tampak Depan & Belakang</option>
                    <option value="front" {{ $side === 'front' ? 'selected' : '' }}>Hanya Tampak Depan</option>
                    <option value="back" {{ $side === 'back' ? 'selected' : '' }}>Hanya Tampak Belakang</option>
                </select>
            </form>

            <button type="button" onclick="window.print()" class="btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF (Ctrl+P)</span>
            </button>
        </div>
    </div>

    <!-- Paper Container -->
    <div class="paper-container">
        <div class="cards-grid">
            @foreach($students as $st)
                @php
                    $rfidUid = $st->rfid_uid ?: ('RF-' . str_pad($st->id, 8, '0', STR_PAD_LEFT));
                    $nisn = $st->nisn ?: ('00' . str_pad($st->id, 8, '0', STR_PAD_LEFT));
                    $photoUrl = !empty($st->photo_path) ? asset('storage/' . $st->photo_path) : null;
                    $logoUrl = !empty($school['logo']) ? asset('storage/' . $school['logo']) : null;
                @endphp

                <!-- SISI DEPAN KARTU -->
                @if($side === 'front' || $side === 'both')
                <div class="id-card-wrapper">
                    <div class="cutting-guides"></div>
                    <div class="id-card card-front">
                        <!-- Header -->
                        <div class="card-header">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" class="header-logo" alt="Logo">
                            @else
                                <div class="header-logo" style="background:#38bdf8; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#0f172a; font-weight:900; font-size:4.5pt;">1LE</div>
                            @endif
                            <div class="header-text">
                                <div class="instansi-sub">PEMERINTAH DAERAH PROVINSI {{ $school['province'] }}</div>
                                <div class="school-name">{{ $school['name'] }}</div>
                                <div class="card-purpose-badge">KARTU PRESENSI & TANDA PELAJAR</div>
                            </div>
                            <div class="wave-symbol" title="Contactless Smart Card">)))</div>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Student Photo -->
                            <div class="photo-box">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $st->name }}">
                                @else
                                    <span class="photo-fallback">{{ strtoupper(substr($st->name, 0, 2)) }}</span>
                                @endif
                            </div>

                            <!-- Student Info -->
                            <div class="student-details">
                                <div class="student-name" title="{{ $st->name }}">{{ $st->name }}</div>
                                <div class="data-row">
                                    <span class="data-label">NISN</span>
                                    <span class="data-val">: {{ $nisn }}</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">NIS</span>
                                    <span class="data-val">: {{ $st->nis }}</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">TTL/JK</span>
                                    <span class="data-val">: {{ $st->gender === 'L' ? 'L' : 'P' }}</span>
                                </div>
                                <div>
                                    <span class="badge-class">{{ $st->schoolClass?->name ?? 'SISWA' }}</span>
                                </div>
                            </div>

                            <!-- Tech Section: QR & RFID -->
                            <div class="tech-section">
                                @if($mode === 'combo' || $mode === 'qr')
                                    <div class="qr-canvas-box" title="Scan QR Code NISN: {{ $nisn }}">
                                        <canvas class="qr-canvas" data-value="{{ $nisn }}"></canvas>
                                    </div>
                                @endif

                                @if($mode === 'combo' || $mode === 'rfid')
                                    @if($mode === 'rfid')
                                        <div class="rfid-chip" title="Chip Smart Card"></div>
                                    @endif
                                    <div class="rfid-uid-tag" title="UID: {{ $rfidUid }}">
                                        {{ substr($rfidUid, 0, 10) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer">
                            <span class="footer-status">
                                <span class="status-dot"></span> SISWA AKTIF • TA {{ $school['year'] }}
                            </span>
                            <span>SMAN 1 LENGKONG</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- SISI BELAKANG KARTU -->
                @if($side === 'back' || $side === 'both')
                <div class="id-card-wrapper">
                    <div class="cutting-guides"></div>
                    <div class="id-card card-back">
                        <div class="back-header">
                            <div class="back-title">Ketentuan Penggunaan Kartu Presensi</div>
                            <div class="back-sub">{{ $school['name'] }} • NPSN: {{ $school['npsn'] }}</div>
                        </div>

                        <ol class="rules-list">
                            <li>Kartu ini adalah identitas resmi presensi kehadiran SMA Negeri 1 Lengkong.</li>
                            <li>Wajib dibawa setiap hari dan ditap pada mesin RFID atau discan kamera QR.</li>
                            <li>Dilarang meminjamkan atau menyalahgunakan kartu ini kepada pihak lain.</li>
                            <li>Kehilangan atau kerusakan kartu wajib segera dilaporkan ke pihak sekolah.</li>
                        </ol>

                        <div class="back-bottom">
                            <div class="signature-box" style="text-align: left;">
                                <div class="signature-date">Sukabumi, Juli 2026</div>
                                <div class="signature-title">Kepala Sekolah,</div>
                                <div class="signature-name">{{ $school['headmaster_name'] }}</div>
                                <div class="signature-nip">NIP. {{ $school['headmaster_nip'] }}</div>
                            </div>

                            <div class="school-contact" style="text-align: right;">
                                <strong>{{ $school['name'] }}</strong><br>
                                {{ $school['address'] }}<br>
                                <span style="color:#0284c7;">https://sman1lengkong.sch.id</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Client-Side Instant QR Code Generator Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvases = document.querySelectorAll('.qr-canvas');
            canvases.forEach(function (canvas) {
                const val = canvas.getAttribute('data-value');
                if (val && window.QRious) {
                    new QRious({
                        element: canvas,
                        value: val,
                        size: 140,
                        level: 'M'
                    });
                }
            });
        });
    </script>
</body>
</html>
