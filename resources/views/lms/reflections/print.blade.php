<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Refleksi Pembelajaran Guru - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #111827;
            background-color: #f3f4f6;
            margin: 0;
            padding: 15px 0;
        }
        .page {
            background: #ffffff;
            width: 297mm;
            min-height: 210mm;
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
            font-size: 10.5pt;
            margin: 0;
            font-weight: normal;
        }
        .header-kop h3 {
            font-size: 11.5pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-kop h2 {
            font-size: 13.5pt;
            margin: 2px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-kop p {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
            font-style: italic;
        }
        .title-doc {
            text-align: center;
            margin: 10px 0 14px 0;
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
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #1f2937;
            padding: 6px 8px;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 24px;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 33.33%;
        }
        .sign-space {
            height: 60px;
        }
        .btn-print-bar {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background-color: #4f46e5;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen Rekap Refleksi Guru (A4 Landscape)</button>
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
            <h1>REKAPITULASI CATATAN REFLEKSI PEMBELAJARAN GURU</h1>
            <p>KURIKULUM MERDEKA • TAHUN AJARAN 2026/2027</p>
        </div>

        <!-- Metadata -->
        <table class="info-table">
            <tr>
                <td style="width: 18%;"><strong>Nama Pendidik</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $teacher->name ?? ($courseClass->teacher->name ?? 'Semua Pendidik Binaan') }}</td>
                <td style="width: 18%;"><strong>Mata Pelajaran</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $courseClass->subject->name ?? 'Lintas Mata Pelajaran' }}</td>
            </tr>
            <tr>
                <td><strong>NIP / ID Guru</strong></td>
                <td>:</td>
                <td>{{ $teacher->nip ?? ($courseClass->teacher->nip ?? '-') }}</td>
                <td><strong>Kelas / Tingkat</strong></td>
                <td>:</td>
                <td>{{ $courseClass->schoolClass->name ?? 'Semua Tingkat' }}</td>
            </tr>
            <tr>
                <td><strong>Semester</strong></td>
                <td>:</td>
                <td>Ganjil</td>
                <td><strong>Dicetak Pada</strong></td>
                <td>:</td>
                <td>{{ date('d F Y') }}</td>
            </tr>
        </table>

        <!-- Tabel Catatan Refleksi -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 15%;">Kelas & Mapel</th>
                    <th style="width: 24%;">Aspek yang Berhasil Efektif</th>
                    <th style="width: 24%;">Kendala & Hambatan Belajar</th>
                    <th style="width: 23%;">Rencana Tindak Lanjut KBM</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reflections as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $item->reflection_date ? $item->reflection_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            <strong>{{ $item->courseClass->subject->name ?? 'Mapel' }}</strong><br>
                            <span style="font-size: 8pt; color: #4b5563;">{{ $item->courseClass->schoolClass->name ?? 'Kelas' }}</span>
                            @if($item->lessonPlan)
                                <div style="font-size: 7.5pt; color: #2563eb; margin-top: 2px;">
                                    Modul: Pertemuan {{ $item->lessonPlan->meeting_number }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->success_notes }}</td>
                        <td>{{ $item->challenges_notes }}</td>
                        <td>{{ $item->follow_up_action }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; font-style: italic;">
                            Belum ada riwayat catatan refleksi pembelajaran pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pengesahan Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala SMAN 1 Lengkong</strong>
                    <div class="sign-space"></div>
                    <strong>Drs. H. Ahmad Fauzi, M.M.Pd.</strong><br>
                    <span>NIP. 19680512 199403 1 005</span>
                </td>
                <td>
                    Mengetahui,<br>
                    <strong>Wakasek Kurikulum</strong>
                    <div class="sign-space"></div>
                    <strong>Iwan Setiawan, M.Pd.</strong><br>
                    <span>NIP. 19750820 200212 1 003</span>
                </td>
                <td>
                    Lengkong, {{ date('d F Y') }}<br>
                    <strong>Guru Mata Pelajaran</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $teacher->name ?? ($courseClass->teacher->name ?? 'Guru Pengampu') }}</strong><br>
                    <span>NIP. {{ $teacher->nip ?? ($courseClass->teacher->nip ?? '-') }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

