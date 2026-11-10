<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alur Tujuan Pembelajaran (ATP) - {{ $subject->name ?? 'Mata Pelajaran' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
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
            padding: 12mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
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
            margin: 8px 0 12px 0;
        }
        .title-doc h1 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 9pt;
            margin: 3px 0 0 0;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #1f2937;
            padding: 5px 6px;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 33.33%;
        }
        .sign-space {
            height: 55px;
        }
        .btn-print-bar {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background-color: #7c3aed;
            color: #fff;
            padding: 8px 18px;
            font-size: 9.5pt;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen Alur Tujuan Pembelajaran (ATP)</button>
    </div>

    <div class="page">
        <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
        <x-print-kop :school="$school ?? null" />

        <!-- Judul Dokumen -->
        <div class="title-doc">
            <h1>ALUR TUJUAN PEMBELAJARAN (ATP)</h1>
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
                <td><strong>Alokasi Waktu</strong></td>
                <td>:</td>
                <td>{{ $totalHours }} Jam Pelajaran (JP)</td>
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

        <!-- Tabel Matriks Alur Tujuan Pembelajaran -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 14%;">Elemen CP</th>
                    <th style="width: 24%;">Capaian Pembelajaran (CP)</th>
                    <th style="width: 8%;">Kode TP</th>
                    <th style="width: 32%;">Tujuan Pembelajaran (TP)</th>
                    <th style="width: 6%;">JP</th>
                    <th style="width: 6%;">Sem.</th>
                    <th style="width: 6%;">Profil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allObjectives as $idx => $tp)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $tp->outcome->element ?? 'Umum' }}</strong>
                        </td>
                        <td style="font-size: 8pt; color: #374151;">
                            {{ ($tp->outcome->description ?? $tp->outcome->cp_text ?? null) ? \Illuminate\Support\Str::limit($tp->outcome->description ?? $tp->outcome->cp_text, 140) : 'Peserta didik memahami konsep keilmuan dan penerapannya secara kritis.' }}
                        </td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold; color: #1e40af;">
                            {{ $tp->tp_code }}
                        </td>
                        <td>
                            <strong>{{ $tp->title }}</strong><br>
                            <span style="font-size: 8pt; color: #4b5563;">{{ $tp->description }}</span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $tp->hours_allocation }}</td>
                        <td style="text-align: center; font-size: 8pt;">{{ $tp->target_semester }}</td>
                        <td style="text-align: center; font-size: 7.5pt;">Bernalar Kritis, Mandiri</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 15px; font-style: italic;">
                            Belum ada Alur Tujuan Pembelajaran yang terkonfigurasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="5" style="text-align: right; padding-right: 8px;">TOTAL JAM PELAJARAN ALUR (ATP):</td>
                    <td style="text-align: center; color: #1e3a8a;">{{ $totalHours }} JP</td>
                    <td colspan="2"></td>
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
