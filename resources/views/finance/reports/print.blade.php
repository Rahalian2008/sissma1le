<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Realisasi Anggaran Sekolah TA {{ $year }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
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
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
        }
        .header-kop h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0;
        }
        .header-kop p {
            font-size: 9pt;
            margin: 1px 0;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-doc h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table.report-table th, table.report-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.report-table th {
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
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Laporan Realisasi Anggaran TA ' . $year" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>LAPORAN PERTANGGUNGJAWABAN & REALISASI ANGGARAN (SPJ)</h3>
            <p style="margin: 2px 0;">Tahun Anggaran {{ $year }}</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Sumber Dana & Program Kegiatan</th>
                    <th style="width: 110px;">Pagu Anggaran</th>
                    <th style="width: 110px;">Realisasi Belanja</th>
                    <th style="width: 110px;">Sisa Anggaran</th>
                    <th style="width: 50px;">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($budgets as $b)
                    <tr style="font-weight: bold; background-color: #f5f5f5;">
                        <td>[{{ $b->funding_source }}] Pagu Utama</td>
                        <td style="text-align: right; font-family: monospace;">{{ number_format($b->total_allocation, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-family: monospace;">{{ number_format($b->items->sum('realized_amount'), 0, ',', '.') }}</td>
                        <td style="text-align: right; font-family: monospace;">{{ number_format($b->total_allocation - $b->items->sum('realized_amount'), 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            {{ $b->total_allocation > 0 ? round(($b->items->sum('realized_amount') / $b->total_allocation) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @foreach($b->items as $item)
                        <tr>
                            <td style="padding-left: 15px;">- {{ $item->activity_name }}</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($item->allocated_amount, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($item->realized_amount, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($item->allocated_amount - $item->realized_amount, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                {{ $item->allocated_amount > 0 ? round(($item->realized_amount / $item->allocated_amount) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; width: 100%; display: flex; justify-content: space-between; font-size: 10pt;">
            <div style="width: 45%; text-align: center;">
                Mengetahui,<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                NIP. {{ $school->headmaster_nip }}
            </div>
            <div style="width: 45%; text-align: center;">
                {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
                {{ auth()->user()?->getRoleDisplayName() ?? 'Bendahara Sekolah' }},<br><br><br><br>
                <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? $school->treasurer_name ?? 'Bendahara') }}</u></strong><br>
                NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
            </div>
        </div>
    </div>
</body>
</html>
