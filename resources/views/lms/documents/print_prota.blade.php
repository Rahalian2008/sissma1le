<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Tahunan (PROTA) - {{ $subject->name ?? 'Mata Pelajaran' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
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
            vertical-align: middle;
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
            background-color: #2563eb;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen Program Tahunan (PROTA)</button>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
        <x-print-kop :school="$school ?? null" />

        <!-- Judul Dokumen -->
        <div class="title-doc">
            <h1>PROGRAM TAHUNAN (PROTA)</h1>
            <p>KURIKULUM MERDEKA • TAHUN AJARAN 2026/2027</p>
        </div>

        <!-- Identitas Mata Pelajaran -->
        <table class="info-table">
            <tr>
                <td style="width: 18%;"><strong>Satuan Pendidikan</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">SMA Negeri 1 Lengkong</td>
                <td style="width: 18%;"><strong>Fase / Kelas</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">Fase E / Kelas {{ $subject->grade ?? 'X' }}</td>
            </tr>
            <tr>
                <td><strong>Mata Pelajaran</strong></td>
                <td>:</td>
                <td>{{ $subject->name ?? 'Informatika' }}</td>
                <td><strong>Tahun Pelajaran</strong></td>
                <td>:</td>
                <td>2026/2027</td>
            </tr>
            <tr>
                <td><strong>Guru Pengampu</strong></td>
                <td>:</td>
                <td>{{ $teacher->name ?? ($courseClass->teacher->name ?? 'Guru Mata Pelajaran') }}</td>
                <td><strong>NIP / ID Guru</strong></td>
                <td>:</td>
                <td>{{ $teacher->nip ?? ($courseClass->teacher->nip ?? '-') }}</td>
            </tr>
        </table>

        <!-- Tabel Distribusi Prota -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">Semester</th>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode TP</th>
                    <th style="width: 65%;">Tujuan Pembelajaran (TP) & Lingkup Materi</th>
                    <th style="width: 12%;">Alokasi Waktu (JP)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Semester Ganjil -->
                @forelse($ganjilObjectives as $index => $tp)
                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ $ganjilObjectives->count() + 1 }}" style="text-align: center; font-weight: bold; background-color: #fafafa;">
                                GANJIL
                            </td>
                        @endif
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $tp->tp_code }}</td>
                        <td>
                            <strong>{{ $tp->title }}</strong><br>
                            <span style="font-size: 8.5pt; color: #374151;">{{ $tp->description }}</span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $tp->hours_allocation }} JP</td>
                    </tr>
                @empty
                    <tr>
                        <td style="text-align: center; font-weight: bold;">GANJIL</td>
                        <td colspan="4" style="text-align: center; font-style: italic; padding: 10px;">Belum ada Tujuan Pembelajaran yang dialokasikan pada semester ganjil.</td>
                    </tr>
                @endforelse
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 12px;">JUMLAH ALOKASI JAM SEMESTER GANJIL:</td>
                    <td style="text-align: center; font-size: 10pt; color: #1e3a8a;">{{ $ganjilHours }} JP</td>
                </tr>

                <!-- Semester Genap -->
                @forelse($genapObjectives as $index => $tp)
                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ $genapObjectives->count() + 1 }}" style="text-align: center; font-weight: bold; background-color: #fafafa;">
                                GENAP
                            </td>
                        @endif
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $tp->tp_code }}</td>
                        <td>
                            <strong>{{ $tp->title }}</strong><br>
                            <span style="font-size: 8.5pt; color: #374151;">{{ $tp->description }}</span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $tp->hours_allocation }} JP</td>
                    </tr>
                @empty
                    <tr>
                        <td style="text-align: center; font-weight: bold;">GENAP</td>
                        <td colspan="4" style="text-align: center; font-style: italic; padding: 10px;">Belum ada Tujuan Pembelajaran yang dialokasikan pada semester genap.</td>
                    </tr>
                @endforelse
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 12px;">JUMLAH ALOKASI JAM SEMESTER GENAP:</td>
                    <td style="text-align: center; font-size: 10pt; color: #1e3a8a;">{{ $genapHours }} JP</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background-color: #eff6ff; font-weight: bold; font-size: 10pt;">
                    <td colspan="4" style="text-align: right; padding: 8px;">TOTAL ALOKASI WAKTU TAHUNAN:</td>
                    <td style="text-align: center; color: #1e40af;">{{ $totalHours }} JP</td>
                </tr>
            </tfoot>
        </table>

        <!-- Pengesahan Tanda Tangan (Kepala Sekolah Selalu di Kiri & Berstatus Mengetahui) -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala SMAN 1 Lengkong</strong>
                    <div class="sign-space"></div>
                    <strong>{{ $school?->headmaster_name ?: \App\Models\SchoolSetting::get('headmaster_name', 'PIAN SOPIAN, S.Pd.') }}</strong><br>
                    <span>NIP. {{ $school?->headmaster_nip ?: \App\Models\SchoolSetting::get('headmaster_nip', '198009132008011005') }}</span>
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

