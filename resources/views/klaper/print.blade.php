<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Klaper Siswa</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-kop h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        .header-kop h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0;
        }
        .header-kop p {
            font-size: 9pt;
            margin: 1px 0;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-doc h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        table.klaper-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        table.klaper-table th, table.klaper-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.klaper-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            font-size: 13px;
        }
        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Klaper Siswa - ' . date('Y')" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>BUKU KLAPER SISWA TAHUN AJARAN {{ date('Y') }}/{{ date('Y')+1 }}</h3>
        </div>

        <table class="klaper-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No.</th>
                    <th style="width: 70px;">NIS</th>
                    <th style="width: 90px;">NISN</th>
                    <th>Nama Lengkap Peserta Didik</th>
                    <th style="width: 35px;">L/P</th>
                    <th style="width: 140px;">Tempat, Tanggal Lahir</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 90px;">Tgl Masuk</th>
                    <th style="width: 90px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $s)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $s->nis }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $s->nisn ?? '-' }}</td>
                        <td><strong>{{ $s->name }}</strong></td>
                        <td style="text-align: center;">{{ $s->gender }}</td>
                        <td>{{ $s->birth_place }}, {{ $s->birth_date?->format('d/m/Y') }}</td>
                        <td style="text-align: center;">Kelas {{ $s->current_class_level }} {{ $s->current_rombel }}</td>
                        <td style="text-align: center;">{{ $s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: center;">{{ $s->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 25px; float: right; width: 300px; text-align: center; font-size: 10pt;">
            {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name }}</u></strong><br>
            NIP. {{ $school->headmaster_nip }}
        </div>
    </div>
</body>
</html>
