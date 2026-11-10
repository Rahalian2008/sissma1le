<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bookTitle }} - {{ $month }}/{{ $year }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h3 { margin: 0; font-size: 11pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 8.5pt; }
        .doc-title { text-align: center; margin: 10px 0 14px 0; }
        .doc-title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 3px 0; font-size: 9.5pt; font-family: monospace; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 6px; font-size: 9.5pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .recap-box { margin-top: 15px; font-size: 10pt; width: 450px; border: 1px solid #000; padding: 8px 12px; }
        .recap-box table { width: 100%; border-collapse: collapse; }
        .recap-box td { padding: 2px 4px; }

        .signatures { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 280px; text-align: center; font-size: 10pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="$bookTitle . ' - ' . $month . '/' . $year" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>{{ $bookTitle }}</h4>
        <p>Bulan: {{ DateTime::createFromFormat('!m', (int)$month)->format('F') }} {{ $year }} &bull; Sumber Dana: {{ $source === 'all' ? 'Semua Sumber Dana' : $source }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th style="width: 75px;">Tanggal</th>
                <th style="width: 120px;">No. Bukti Kas</th>
                <th>Uraian Transaksi</th>
                <th style="width: 140px;">Pihak Terkait</th>
                <th style="width: 110px;">Penerimaan (Rp)</th>
                <th style="width: 110px;">Pengeluaran (Rp)</th>
                <th style="width: 120px;">Saldo Kas (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $balance = 0; @endphp
            @forelse($transactions as $idx => $t)
                @php $balance += ($t->debit - $t->credit); @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td class="text-center font-mono font-bold">{{ $t->evidence_number }}</td>
                    <td>{{ $t->description }}</td>
                    <td>{{ $t->recipient_or_payer }}</td>
                    <td class="text-right font-mono">{{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-mono">{{ $t->credit > 0 ? number_format($t->credit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f7f7f7; font-weight: bold;">
                <td colspan="5" class="text-right">JUMLAH TOTAL:</td>
                <td class="text-right font-mono">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right font-mono">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                <td class="text-right font-mono">Rp {{ number_format($closingBalance, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="recap-box">
        <strong>REKAPITULASI SALDO {{ $type === 'cash' ? 'KAS TUNAI' : 'KAS BANK' }}:</strong>
        <table>
            <tr>
                <td style="width: 220px;">Total Penerimaan Bulan Ini</td>
                <td style="width: 10px;">:</td>
                <td class="text-right font-mono">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran Bulan Ini</td>
                <td>:</td>
                <td class="text-right font-mono">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: bold; border-top: 1px solid #444;">
                <td>Saldo Akhir {{ $type === 'cash' ? 'di Brankas' : 'di Rekening Bank' }}</td>
                <td>:</td>
                <td class="text-right font-mono">Rp {{ number_format($closingBalance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Mengetahui,<br>Kepala Sekolah selaku KPA,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>

        <div class="sig-box float-right">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>{{ auth()->user()?->getRoleDisplayName() ?? 'Bendahara Pengeluaran' }},</p>
            <br><br><br>
            <p><strong><u>{{ strtoupper(Auth::user()?->getOfficialName() ?? 'Bendahara Sekolah') }}</u></strong><br>
            NIP. {{ Auth::user()?->getOfficialNip() ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>
