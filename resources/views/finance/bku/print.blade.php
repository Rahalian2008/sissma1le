<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Kas Umum (BKU)</title>
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
            margin-bottom: 12px;
        }
        .title-doc h3 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        table.bku-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table.bku-table th, table.bku-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.bku-table th {
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
    <x-print-toolbar :title="'Buku Kas Umum (BKU) Bulan ' . $month . '/' . $year" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>BUKU KAS UMUM (BKU)</h3>
            <p style="margin: 2px 0;">Bulan: {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }} | Sumber: {{ $source === 'all' ? 'Semua Sumber Dana' : $source }}</p>
        </div>

        <table class="bku-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No.</th>
                    <th style="width: 70px;">Tanggal</th>
                    <th style="width: 110px;">No. Bukti Kas</th>
                    <th>Uraian Transaksi Pembukuan</th>
                    <th style="width: 110px;">Penerimaan (Rp)</th>
                    <th style="width: 110px;">Pengeluaran (Rp)</th>
                    <th style="width: 120px;">Saldo Kas (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $running = 0; @endphp
                @foreach ($transactions as $index => $t)
                    @php $running += ($t->debit - $t->credit); @endphp
                    <tr>
                        <td style="text-align: center; font-family: monospace;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $t->transaction_date?->format('d/m/Y') }}</td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $t->evidence_number }}</td>
                        <td>{{ $t->description }} ({{ $t->recipient_or_payer }})</td>
                        <td style="text-align: right; font-family: monospace;">{{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}</td>
                        <td style="text-align: right; font-family: monospace;">{{ $t->credit > 0 ? number_format($t->credit, 0, ',', '.') : '-' }}</td>
                        <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($running, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="4" style="text-align: right;">TOTAL TRANSAKSI BULAN INI:</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($totalCredit, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-family: monospace;">{{ number_format($running, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 30px; width: 100%; display: flex; justify-content: space-between; font-size: 10pt;">
            <div style="width: 40%; text-align: center;">
                Mengetahui,<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                NIP. {{ $school->headmaster_nip }}
            </div>
            <div style="width: 40%; text-align: center;">
                {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
                {{ auth()->user()?->getRoleDisplayName() ?? 'Bendahara Sekolah' }},<br><br><br><br>
                <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? $school->treasurer_name ?? 'Bendahara') }}</u></strong><br>
                NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
            </div>
        </div>
    </div>
</body>
</html>
