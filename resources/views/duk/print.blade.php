<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Urut Kepangkatan (DUK) Pegawai</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
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
            text-transform: uppercase;
        }
        table.duk-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table.duk-table th, table.duk-table td {
            border: 1px solid #000;
            padding: 4px 5px;
        }
        table.duk-table th {
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
    <x-print-toolbar :title="'Daftar Urut Kepangkatan (DUK) Pegawai'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>DAFTAR URUT KEPANGKATAN (DUK) PEGAWAI NEGERI SIPIL / PTK</h3>
            <p style="margin-top: 2px;">Keadaan Per: {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <table class="duk-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">No. Urut</th>
                    <th rowspan="2">Nama Lengkap & Gelar</th>
                    <th rowspan="2" style="width: 110px;">NIP / NUPTK</th>
                    <th colspan="2">Pangkat / Golongan</th>
                    <th colspan="2">Jabatan</th>
                    <th colspan="2">Masa Kerja</th>
                    <th colspan="2">Pendidikan Terakhir</th>
                    <th rowspan="2" style="width: 50px;">Status</th>
                </tr>
                <tr>
                    <th style="width: 55px;">Gol.</th>
                    <th style="width: 65px;">TMT</th>
                    <th>Nama Jabatan</th>
                    <th style="width: 65px;">TMT</th>
                    <th style="width: 30px;">Th</th>
                    <th style="width: 30px;">Bl</th>
                    <th style="width: 45px;">Tk.</th>
                    <th>Jurusan / Lembaga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankedEmployees as $index => $emp)
                    <tr>
                        <td style="text-align: center; font-family: monospace;">{{ $index + 1 }}</td>
                        <td><strong>{{ $emp->formatted_name }}</strong></td>
                        <td style="text-align: center; font-family: monospace;">{{ $emp->nip ?? '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $emp->rank_group ?? '-' }}</td>
                        <td style="text-align: center;">{{ $emp->rank_tmt?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $emp->position ?? '-' }}</td>
                        <td style="text-align: center;">{{ $emp->position_tmt?->format('d/m/Y') ?? '-' }}</td>
                        <td style="text-align: center;">{{ $emp->years_of_service_years ?? 0 }}</td>
                        <td style="text-align: center;">{{ $emp->years_of_service_months ?? 0 }}</td>
                        <td style="text-align: center;">{{ $emp->education_level ?? 'S1' }}</td>
                        <td>{{ $emp->education_major }} ({{ $emp->education_institution ?? '-' }})</td>
                        <td style="text-align: center;">{{ $emp->employment_status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 25px; float: right; width: 320px; text-align: center; font-size: 10pt;">
            {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name }}</u></strong><br>
            NIP. {{ $school->headmaster_nip }}
        </div>
    </div>
</body>
</html>
