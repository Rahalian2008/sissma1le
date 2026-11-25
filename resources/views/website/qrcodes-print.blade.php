<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code Standee & Poster Resmi | {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Cinzel:wght@700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0f172a;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            line-height: 1.4;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Navigasi & Filter Layar (Hanya di Layar) */
        .screen-toolbar {
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .screen-toolbar .nav-pills {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .screen-toolbar a.pill {
            text-decoration: none;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.2s;
        }
        .screen-toolbar a.pill:hover,
        .screen-toolbar a.pill.active {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
            box-shadow: 0 2px 6px rgba(16,185,129,0.3);
        }
        .screen-toolbar .download-btn {
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .screen-toolbar .download-btn:hover {
            background: #1d4ed8;
        }

        /* Container Dokumen Cetak */
        .page-container {
            max-width: 900px;
            margin: 25px auto;
            background: #fff;
            padding: 30px 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-radius: 4px;
            position: relative;
        }

        /* Bingkai Standee Ornate / Poster */
        .standee-frame {
            border: 3px double #0f172a;
            border-radius: 16px;
            padding: 24px;
            position: relative;
            background: #fff;
        }
        .standee-frame::before {
            content: '';
            position: absolute;
            top: 4px; left: 4px; right: 4px; bottom: 4px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            pointer-events: none;
        }

        /* Header / Kop Sekolah */
        .kop-section {
            border-bottom: 2.5px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
            text-align: center;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }
        .kop-text h3 {
            font-size: 11pt;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .kop-text h2 {
            font-size: 12.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .kop-text h1 {
            font-size: 15pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .kop-text p {
            font-size: 8.5pt;
            color: #475569;
            font-style: italic;
        }

        /* Banner Judul Poster */
        .title-banner {
            text-align: center;
            margin-bottom: 18px;
            padding: 10px 16px;
            border-radius: 12px;
            position: relative;
        }
        .title-banner.tamu {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1.5px solid #10b981;
        }
        .title-banner.perpus {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1.5px solid #3b82f6;
        }
        .title-banner.pengaduan {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            border: 1.5px solid #f43f5e;
        }
        .title-banner h2 {
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .title-banner.tamu h2 { color: #065f46; }
        .title-banner.perpus h2 { color: #1e40af; }
        .title-banner.pengaduan h2 { color: #9f1239; }
        .title-banner p {
            font-size: 9.5pt;
            font-weight: 600;
            color: #334155;
        }

        /* Kotak Utama QR Code */
        .qr-card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 16px 0;
            text-align: center;
        }
        .qr-box {
            background: #fff;
            padding: 14px;
            border-radius: 16px;
            border: 2px solid #0f172a;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            display: inline-block;
            margin-bottom: 12px;
        }
        .qr-image {
            width: 220px;
            height: 220px;
            object-fit: contain;
            display: block;
        }
        .qr-link-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10pt;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            display: inline-block;
        }

        /* Narasi & Kata-Kata Resmi */
        .narrative-box {
            background: #f8fafc;
            border-left: 4px solid #0f172a;
            border-radius: 0 10px 10px 0;
            padding: 12px 18px;
            margin: 16px 0;
            text-align: justify;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.5;
        }
        .narrative-box.tamu { border-left-color: #059669; }
        .narrative-box.perpus { border-left-color: #2563eb; }
        .narrative-box.pengaduan { border-left-color: #e11d48; }

        /* Panduan 3 Langkah Scan */
        .scan-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 18px 0;
        }
        .step-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .step-number {
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 50%;
            background: #0f172a;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .step-item h4 {
            font-size: 9pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .step-item p {
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.3;
        }

        /* Footer Standee / Penutup */
        .standee-footer {
            margin-top: 20px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 8pt;
            color: #64748b;
        }
        .standee-footer .signature-space {
            text-align: center;
        }

        /* Mode 3-in-1 Grid Layout */
        .grid-3in1 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 16px 0;
        }
        .card-3in1 {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .card-3in1 .card-title {
            font-size: 11pt;
            font-weight: 900;
            margin-bottom: 2px;
        }
        .card-3in1 .card-subtitle {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 10px;
            min-height: 24px;
        }
        .card-3in1 .qr-image-small {
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin: 0 auto 10px auto;
            border: 1px solid #cbd5e1;
            padding: 6px;
            border-radius: 10px;
            display: block;
        }
        .card-3in1 .card-url {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8pt;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #0f172a;
            display: block;
            margin-top: 6px;
            word-break: break-all;
        }
        .card-3in1 .card-desc {
            font-size: 7.5pt;
            color: #334155;
            margin-top: 8px;
            text-align: justify;
            line-height: 1.35;
        }

        /* Aturan Print Otomatis (Page Breaks) */
        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .screen-toolbar, .no-print {
                display: none !important;
            }
            .page-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 15px 20px !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-after: always;
            }
            .page-container:last-child {
                page-break-after: auto;
            }
            .standee-frame {
                border-width: 2.5px;
                padding: 16px;
            }
            .qr-image {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR LAYAR (Tidak Ikut Tercetak) -->
    <div class="screen-toolbar no-print">
        <div class="nav-pills">
            <span style="font-size: 13px; font-weight: 800; color: #10b981; margin-right: 6px;">📄 DOKUMEN CETAK:</span>
            <a href="?type=all" class="pill {{ $type === 'all' ? 'active' : '' }}">📋 Lembar 3-in-1 (Semua)</a>
            <a href="?type=tamu" class="pill {{ $type === 'tamu' ? 'active' : '' }}">👥 1. Buku Tamu Sekolah</a>
            <a href="?type=perpus" class="pill {{ $type === 'perpus' ? 'active' : '' }}">📚 2. Buku Tamu Perpus</a>
            <a href="?type=pengaduan" class="pill {{ $type === 'pengaduan' ? 'active' : '' }}">📢 3. Kanal Pengaduan</a>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" onclick="window.print()" class="download-btn" style="background: #059669; cursor: pointer;">
                🖨️ Cetak Dokumen (Ctrl+P)
            </button>
            <a href="{{ asset('images/qrcodes/qrcode_buku_tamu.png') }}" download="QR_Buku_Tamu_SMAN1Lengkong.png" class="pill" title="Unduh Gambar QR Asli">⬇️ QR Tamu</a>
            <a href="{{ asset('images/qrcodes/qrcode_buku_perpus.png') }}" download="QR_Buku_Perpus_SMAN1Lengkong.png" class="pill" title="Unduh Gambar QR Asli">⬇️ QR Perpus</a>
            <a href="{{ asset('images/qrcodes/qrcode_pengaduan.png') }}" download="QR_Pengaduan_SMAN1Lengkong.png" class="pill" title="Unduh Gambar QR Asli">⬇️ QR Pengaduan</a>
        </div>
    </div>

    {{-- =========================================================================
         1. DOKUMEN KHUSUS: BUKU TAMU DIGITAL (Halaman Tunggal)
         ========================================================================= --}}
    @if($type === 'tamu' || $type === 'all_single')
    <div class="page-container">
        <div class="standee-frame">
            <!-- Kop Surat Resmi -->
            <x-print-kop :school="$school" />

            <!-- Banner Judul -->
            <div class="title-banner tamu">
                <h2>PRESENSI BUKU TAMU DIGITAL</h2>
                <p>PELAYANAN INFORMASI, RESEPSIONIS, &amp; KEAMANAN GERBANG SEKOLAH</p>
            </div>

            <!-- Pesan Selamat Datang & Himbauan -->
            <div class="narrative-box tamu">
                <strong>Selamat Datang di Kampus SMA Negeri 1 Lengkong.</strong><br>
                Demi kenyamanan, ketertiban administratif, serta pemenuhan standar keamanan lingkungan pendidikan, kami memohon kesediaan Bapak/Ibu/Saudara tamu kehormatan untuk mengisi <strong>Presensi Buku Tamu Digital</strong> secara mandiri dengan memindai (scan) QR Code di bawah ini. Kehadiran dan data Anda tercatat secara resmi pada sistem terpadu sekolah. Terima kasih atas kerja sama dan kunjungan Anda.
            </div>

            <!-- Area QR Code Utama -->
            <div class="qr-card-container">
                <div class="qr-box">
                    <img src="{{ asset('images/qrcodes/qrcode_buku_tamu.png') }}" alt="QR Code Buku Tamu" class="qr-image">
                </div>
                <div class="qr-link-badge">
                    🌐 https://sman1lengkong.school/kiosk-tamu
                </div>
                <div style="font-size: 8pt; color: #059669; font-weight: 700; margin-top: 4px;">
                    ✓ Terhubung Langsung ke Resepsionis &amp; Pos Satpam
                </div>
            </div>

            <!-- Panduan 3 Langkah Mudah -->
            <div class="scan-steps">
                <div class="step-item">
                    <span class="step-number">1</span>
                    <h4>Buka Kamera</h4>
                    <p>Buka aplikasi kamera bawaan ponsel pintar (smartphone) Anda.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">2</span>
                    <h4>Arahkan ke QR</h4>
                    <p>Sorotkan kamera tepat ke arah gambar QR Code di atas.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">3</span>
                    <h4>Isi Form Mandiri</h4>
                    <p>Ketuk link notifikasi yang muncul dan isi data kunjungan Anda.</p>
                </div>
            </div>

            <!-- Footer Dokumen -->
            <div class="standee-footer">
                <div>
                    <strong>SMA NEGERI 1 LENGKONG</strong><br>
                    Jl. Raya Lengkong No. 43174, Kab. Sukabumi &bull; Email: info@sman1lengkong.school
                </div>
                <div class="signature-space">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br><br><br>
                    <strong><u>{{ $school->headmaster_name ?? 'PIAN SOPIAN, S.Pd., MM.Pd.' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? '198009132008011005' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- =========================================================================
         2. DOKUMEN KHUSUS: BUKU TAMU PERPUSTAKAAN (Halaman Tunggal)
         ========================================================================= --}}
    @if($type === 'perpus' || $type === 'all_single')
    <div class="page-container">
        <div class="standee-frame">
            <!-- Kop Surat Resmi -->
            <x-print-kop :school="$school" />

            <!-- Banner Judul -->
            <div class="title-banner perpus">
                <h2>BUKU PENGUNJUNG PERPUSTAKAAN</h2>
                <p>PUSAT SUMBER BELAJAR, LITERASI, &amp; PERPUSTAKAAN DIGITAL SMAN 1 LENGKONG</p>
            </div>

            <!-- Pesan Selamat Datang & Himbauan -->
            <div class="narrative-box perpus">
                <em>“Buku adalah jendela dunia, dan membaca adalah kuncinya.”</em><br>
                Selamat datang para pegiat literasi, guru, dan peserta didik di <strong>Perpustakaan SMAN 1 Lengkong</strong>. Sebelum menikmati fasilitas ruang baca, meminjam buku fisik, atau mengakses koleksi e-book digital, silakan lakukan <strong>Presensi Mandiri Pemustaka</strong> dengan memindai QR Code di bawah. Kontribusi kehadiran Anda mendukung penguatan budaya literasi dan akreditasi perpustakaan sekolah.
            </div>

            <!-- Area QR Code Utama -->
            <div class="qr-card-container">
                <div class="qr-box">
                    <img src="{{ asset('images/qrcodes/qrcode_buku_perpus.png') }}" alt="QR Code Buku Tamu Perpustakaan" class="qr-image">
                </div>
                <div class="qr-link-badge">
                    🌐 https://sman1lengkong.school/perpustakaan/buku-tamu
                </div>
                <div style="font-size: 8pt; color: #2563eb; font-weight: 700; margin-top: 4px;">
                    ✓ Presensi Otomatis &bull; Terhubung Layanan E-Book &amp; Sirkulasi Mandiri
                </div>
            </div>

            <!-- Panduan 3 Langkah Mudah -->
            <div class="scan-steps">
                <div class="step-item">
                    <span class="step-number">1</span>
                    <h4>Arahkan Kamera</h4>
                    <p>Buka kamera smartphone dan arahkan ke kode QR di atas.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">2</span>
                    <h4>Pilih Identitas</h4>
                    <p>Pilih peran Anda (Siswa, Pendidik, Staf, atau Tamu Umum).</p>
                </div>
                <div class="step-item">
                    <span class="step-number">3</span>
                    <h4>Selamat Membaca</h4>
                    <p>Presensi selesai! Silakan manfaatkan fasilitas literasi kami.</p>
                </div>
            </div>

            <!-- Footer Dokumen -->
            <div class="standee-footer">
                <div>
                    <strong>UNIT PERPUSTAKAAN &amp; LITERASI DIGITAL</strong><br>
                    SMA Negeri 1 Lengkong &bull; Terakreditasi Perpustakaan Sekolah
                </div>
                <div class="signature-space">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br><br><br>
                    <strong><u>{{ $school->headmaster_name ?? 'PIAN SOPIAN, S.Pd., MM.Pd.' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? '198009132008011005' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- =========================================================================
         3. DOKUMEN KHUSUS: KANAL PENGADUAN & ASPIRASI (Halaman Tunggal)
         ========================================================================= --}}
    @if($type === 'pengaduan' || $type === 'all_single')
    <div class="page-container">
        <div class="standee-frame">
            <!-- Kop Surat Resmi -->
            <x-print-kop :school="$school" />

            <!-- Banner Judul -->
            <div class="title-banner pengaduan">
                <h2>KANAL PENGADUAN &amp; ASPIRASI PUBLIK</h2>
                <p>LAYANAN WHISTLEBLOWING &bull; SEKOLAH RAMAH ANAK &bull; BEBAS PERUNDUNGAN (ANTI-BULLYING)</p>
            </div>

            <!-- Pesan Komitmen Integritas & Kerahasiaan -->
            <div class="narrative-box pengaduan">
                <strong>Komitmen Kerahasiaan &amp; Perlindungan Pelapor 100%.</strong><br>
                SMA Negeri 1 Lengkong berkomitmen menghadirkan lingkungan belajar yang aman, tertib, berkeadilan, dan bebas dari segala bentuk perundungan (bullying), kekerasan, maupun gratifikasi. Apabila Anda memiliki <strong>aspirasi, kritik konstruktif, pengaduan fasilitas, atau menyaksikan dugaan pelanggaran</strong>, sampaikan secara terlindungi melalui pemindaian QR Code di bawah. Laporan Anda ditelaah dan ditindaklanjuti langsung oleh <strong>Kepala Sekolah &amp; Jajaran Wakil Kepala Sekolah</strong>.
            </div>

            <!-- Area QR Code Utama -->
            <div class="qr-card-container">
                <div class="qr-box">
                    <img src="{{ asset('images/qrcodes/qrcode_pengaduan.png') }}" alt="QR Code Pengaduan & Aspirasi" class="qr-image">
                </div>
                <div class="qr-link-badge">
                    🌐 https://sman1lengkong.school/pengaduan
                </div>
                <div style="font-size: 8pt; color: #e11d48; font-weight: 700; margin-top: 4px;">
                    🛡️ Opsi Laporan Anonim Tersedia &bull; Kerahasiaan Identitas Dijamin Penuh
                </div>
            </div>

            <!-- Panduan 3 Langkah Mudah -->
            <div class="scan-steps">
                <div class="step-item">
                    <span class="step-number">1</span>
                    <h4>Pindai QR Code</h4>
                    <p>Scan menggunakan kamera HP Anda tanpa perlu unduh aplikasi.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">2</span>
                    <h4>Tulis Kronologi</h4>
                    <p>Uraikan laporan/aspirasi secara jelas (bisa melampirkan foto/dokumen).</p>
                </div>
                <div class="step-item">
                    <span class="step-number">3</span>
                    <h4>Simpan No. Tiket</h4>
                    <p>Dapatkan tiket resmi untuk memantau status disposisi pimpinan.</p>
                </div>
            </div>

            <!-- Footer Dokumen -->
            <div class="standee-footer">
                <div>
                    <strong>TIM PENCEGAHAN &amp; PENANGANAN KEKERASAN (TPPK)</strong><br>
                    Kanal Pengaduan Resmi Eksekutif &bull; SMA Negeri 1 Lengkong
                </div>
                <div class="signature-space">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br><br><br>
                    <strong><u>{{ $school->headmaster_name ?? 'PIAN SOPIAN, S.Pd., MM.Pd.' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? '198009132008011005' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- =========================================================================
         4. DOKUMEN KOMPREHENSIF: 3-IN-1 STANDEE / POSTER (Default)
         ========================================================================= --}}
    @if($type === 'all')
    <div class="page-container" style="max-width: 1050px;">
        <div class="standee-frame">
            <!-- Kop Surat Resmi -->
            <x-print-kop :school="$school" />

            <!-- Banner Utama -->
            <div class="title-banner tamu" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0e7ff 100%); border-color: #6366f1;">
                <h2 style="color: #1e1b4b; font-size: 17pt;">LAYANAN MANDIRI PUBLIK BERBASIS DIGITAL</h2>
                <p style="color: #4338ca;">KEMUDAHAN AKSES INFORMASI, PRESENSI, &amp; PENGADUAN RESMI SMA NEGERI 1 LENGKONG</p>
            </div>

            <p style="text-align: center; font-size: 9pt; color: #475569; margin-bottom: 16px;">
                Silakan arahkan kamera smartphone Anda ke salah satu QR Code di bawah ini sesuai kebutuhan layanan:
            </p>

            <!-- 3 Kolom Kartu Berdampingan -->
            <div class="grid-3in1">
                
                <!-- 1. Buku Tamu Sekolah -->
                <div class="card-3in1" style="border-top: 4px solid #10b981;">
                    <div>
                        <div style="font-size: 20pt; margin-bottom: 2px;">👥</div>
                        <div class="card-title" style="color: #065f46;">BUKU TAMU SEKOLAH</div>
                        <div class="card-subtitle">Resepsionis, Lobi, &amp; Pos Satpam</div>
                        <img src="{{ asset('images/qrcodes/qrcode_buku_tamu.png') }}" alt="QR Buku Tamu" class="qr-image-small">
                        <span class="card-url">/kiosk-tamu</span>
                    </div>
                    <div class="card-desc">
                        Bagi tamu kehormatan, wali murid, dan dinas/instansi luar yang berkunjung ke lingkungan SMAN 1 Lengkong, mohon mengisi presensi tamu secara tertib.
                    </div>
                </div>

                <!-- 2. Buku Tamu Perpustakaan -->
                <div class="card-3in1" style="border-top: 4px solid #3b82f6;">
                    <div>
                        <div style="font-size: 20pt; margin-bottom: 2px;">📚</div>
                        <div class="card-title" style="color: #1e40af;">BUKU TAMU PERPUS</div>
                        <div class="card-subtitle">Presensi Mandiri Pemustaka</div>
                        <img src="{{ asset('images/qrcodes/qrcode_buku_perpus.png') }}" alt="QR Buku Perpus" class="qr-image-small">
                        <span class="card-url">/perpustakaan/buku-tamu</span>
                    </div>
                    <div class="card-desc">
                        Presensi mandiri bagi peserta didik, pendidik, dan pengunjung umum sebelum membaca atau meminjam koleksi literasi fisik &amp; digital perpustakaan.
                    </div>
                </div>

                <!-- 3. Kanal Pengaduan -->
                <div class="card-3in1" style="border-top: 4px solid #f43f5e;">
                    <div>
                        <div style="font-size: 20pt; margin-bottom: 2px;">📢</div>
                        <div class="card-title" style="color: #9f1239;">KANAL PENGADUAN</div>
                        <div class="card-subtitle">Jalur Terlindung Kepsek &amp; Wakasek</div>
                        <img src="{{ asset('images/qrcodes/qrcode_pengaduan.png') }}" alt="QR Pengaduan" class="qr-image-small">
                        <span class="card-url">/pengaduan</span>
                    </div>
                    <div class="card-desc">
                        Kanal whistleblowing resmi &amp; aman. Sampaikan aspirasi, kendala, atau laporan dugaan perundungan langsung ke pimpinan. Kerahasiaan terjamin 100%.
                    </div>
                </div>

            </div>

            <!-- Petunjuk Scan Singkat -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 16px; margin: 16px 0; text-align: center; font-size: 8.5pt; color: #334155;">
                📱 <strong>Petunjuk:</strong> Cukup buka kamera HP Anda &bull; Arahkan ke QR Code &bull; Klik tautan yang muncul di layar &bull; Tanpa perlu install aplikasi tambahan
            </div>

            <!-- Footer Dokumen -->
            <div class="standee-footer">
                <div>
                    <strong>SMA NEGERI 1 LENGKONG</strong><br>
                    Website Resmi: https://sman1lengkong.school &bull; Sukabumi, Jawa Barat
                </div>
                <div class="signature-space">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br><br><br>
                    <strong><u>{{ $school->headmaster_name ?? 'PIAN SOPIAN, S.Pd., MM.Pd.' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? '198009132008011005' }}
                </div>
            </div>
        </div>
    </div>
    @endif

</body>
</html>
