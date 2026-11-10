<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Portofolio Karya Siswa - {{ $portfolio->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #111827;
            background-color: #f3f4f6;
            margin: 0;
            padding: 15px 0;
        }
        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .header-kop h4 {
            font-size: 10pt;
            margin: 0;
            font-weight: normal;
        }
        .header-kop h3 {
            font-size: 11pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop h2 {
            font-size: 13pt;
            margin: 2px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-kop p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        .title-doc {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .title-doc h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 9.5pt;
            margin: 4px 0 0 0;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .section-box {
            border: 1px solid #1f2937;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .section-box h3 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0 0 8px 0;
            border-bottom: 1px dashed #d1d5db;
            padding-bottom: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
            font-size: 10pt;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
        .sign-space {
            height: 65px;
        }
        .btn-print-bar {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background-color: #7c3aed;
            color: #fff;
            padding: 8px 18px;
            font-size: 10pt;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .btn-print-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Lembar Portofolio Siswa (A4)</button>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi -->
        <div class="header-kop">
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3>DINAS PENDIDIKAN</h3>
            <h3>CABANG DINAS PENDIDIKAN WILAYAH V</h3>
            <h2>SMA NEGERI 1 LENGKONG</h2>
            <p>Jalan Raya Lengkong No. 45, Sukabumi, Jawa Barat 43174 • Pos-el: info@sman1lengkong.sch.id</p>
        </div>

        <!-- Judul Dokumen -->
        <div class="title-doc">
            <h1>LEMBAR KURASI & BUKTI PORTOFOLIO KARYA SISWA</h1>
            <p>KURIKULUM MERDEKA • SISTEM MANAJEMEN PEMBELAJARAN SMA1LE</p>
        </div>

        <!-- Identitas Siswa & Karya -->
        <div class="section-box">
            <h3>I. IDENTITAS PESERTA DIDIK</h3>
            <table class="info-table">
                <tr>
                    <td style="width: 25%;"><strong>Nama Peserta Didik</strong></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 73%;"><strong>{{ $portfolio->student->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td><strong>NISN / NIS</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->student->nisn ?? '-' }} / {{ $portfolio->student->nis ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Kelas / Tingkat</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->student->schoolClass->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Mata Pelajaran</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->courseClass->subject->name ?? 'Lintas Mapel / Proyek Mandiri' }}</td>
                </tr>
            </table>
        </div>

        <!-- Detail Artefak Portofolio -->
        <div class="section-box">
            <h3>II. INFORMASI KARYA / ARTEFAK BELAJAR</h3>
            <table class="info-table">
                <tr>
                    <td style="width: 25%;"><strong>Judul Karya</strong></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 73%;"><strong>{{ $portfolio->title }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Kategori</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->category }} {{ $portfolio->is_featured ? '• (⭐ Karya Unggulan)' : '' }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Karya</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->artifact_date ? $portfolio->artifact_date->format('d F Y') : '-' }}</td>
                </tr>
                @if($portfolio->learningObjective)
                <tr>
                    <td><strong>Tujuan Pembelajaran</strong></td>
                    <td>:</td>
                    <td><strong>[TP {{ $portfolio->learningObjective->tp_code }}]</strong> {{ $portfolio->learningObjective->description }}</td>
                </tr>
                @endif
                @if($portfolio->external_url)
                <tr>
                    <td><strong>Tautan Portofolio</strong></td>
                    <td>:</td>
                    <td>{{ $portfolio->external_url }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Narasi Refleksi & Deskripsi Karya -->
        <div class="section-box">
            <h3>III. DESKRIPSI & REFLEKSI PEMBELAJARAN</h3>
            <p style="text-align: justify; margin: 4px 0;">
                {{ $portfolio->description ?: 'Peserta didik telah menyelesaikan artefak karya belajar ini dengan baik sesuai indikator ketercapaian tujuan pembelajaran.' }}
            </p>
        </div>

        <!-- Validasi Pendidik -->
        <div class="section-box">
            <h3>IV. STATUS VERIFIKASI & KURASI PENDIDIK</h3>
            <p style="margin: 4px 0;">
                Karya ini telah ditelaah dan diverifikasi oleh guru pengampu / pembina kompetensi pada sistem pembelajaran digital SMAN 1 Lengkong.
                @if($portfolio->verifier)
                    <br><strong>Pendidik Pembina:</strong> {{ $portfolio->verifier->name }} (ID: #{{ $portfolio->verified_by }})
                @endif
            </p>
        </div>

        <!-- Pengesahan Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Guru Pembina / Wali Kelas</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $portfolio->verifier->name ?? ($portfolio->courseClass->teacher->name ?? 'Guru Pengampu') }}</strong><br>
                    <span>Pendidik SMAN 1 Lengkong</span>
                </td>
                <td>
                    Lengkong, {{ date('d F Y') }}<br>
                    <strong>Peserta Didik Pembuat Karya</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $portfolio->student->name ?? 'Siswa' }}</strong><br>
                    <span>NISN. {{ $portfolio->student->nisn ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

