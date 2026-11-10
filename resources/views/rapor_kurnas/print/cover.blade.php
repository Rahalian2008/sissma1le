<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sampul e-Rapor - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .cover-page {
            width: 170mm;
            min-height: 250mm;
            border: 3px double #000;
            padding: 15mm 10mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }
        .header-title h1 {
            font-size: 20pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 1.5px;
        }
        .header-title h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }
        .header-title h3 {
            font-size: 14pt;
            font-weight: normal;
            margin: 0;
        }
        .logo-box {
            margin: 25mm 0;
        }
        .logo-box img {
            width: 130px;
            height: auto;
        }
        .student-info {
            margin: 15mm 0;
            width: 100%;
        }
        .student-info p {
            font-size: 12pt;
            margin: 4px 0;
        }
        .student-box {
            border: 1px solid #000;
            padding: 8px 15px;
            display: inline-block;
            margin-top: 5px;
            min-width: 90mm;
            font-size: 14pt;
            font-weight: bold;
        }
        .footer-info {
            margin-top: 20mm;
        }
        .footer-info h3 {
            font-size: 13pt;
            font-weight: bold;
            margin: 3px 0;
        }
        .footer-info p {
            font-size: 11pt;
            margin: 2px 0;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: none;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Print Button -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; gap: 10px;">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            🖨️ Cetak Sampul
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 10px 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <div class="cover-page">
        <div class="header-title">
            <h1>RAPOR PESERTA DIDIK</h1>
            <h2>SEKOLAH MENENGAH ATAS</h2>
            <h2>(SMA)</h2>
            <h3 style="margin-top: 10px; font-size: 11pt;">KURIKULUM NASIONAL / KURIKULUM MERDEKA</h3>
        </div>

        <div class="logo-box">
            <img src="{{ asset('images/logo-tutwuri.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" alt="Logo Tut Wuri Handayani">
        </div>

        <div class="student-info">
            <p>Nama Peserta Didik:</p>
            <div class="student-box">
                {{ strtoupper($student->name) }}
            </div>

            <p style="margin-top: 15px;">Nomor Induk Siswa Nasional (NISN):</p>
            <div style="font-size: 13pt; font-weight: bold; font-family: monospace;">
                {{ $student->nisn ?? $student->nis ?? '-' }}
            </div>
        </div>

        <div class="footer-info">
            <h3>KEMENTERIAN PENDIDIKAN DASAR DAN MENENGAH</h3>
            <h3>REPUBLIK INDONESIA</h3>
            <p style="margin-top: 8px; font-weight: bold;">SMA NEGERI 1 LENGKONG</p>
            <p>KABUPATEN SUKABUMI - JAWA BARAT</p>
            <p style="font-size: 10pt; color: #333;">Tahun Ajaran {{ $setting->academicYear->name ?? '2025/2026' }}</p>
        </div>
    </div>
</body>
</html>

