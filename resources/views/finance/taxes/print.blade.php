<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Pembantu Pajak - Tahun {{ $year }}</title>
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
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 6px; font-size: 9pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

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
    <x-print-toolbar :title="'Buku Pembantu Pajak - Tahun ' . $year" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>BUKU PEMBANTU PAJAK</h4>
        <p>Tahun Anggaran: <strong>{{ $year }}</strong> &bull; Satuan Pendidikan: {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 75px;">Tanggal</th>
                <th style="width: 110px;">No. Bukti BKU</th>
                <th>Uraian Transaksi / Belanja</th>
                <th style="width: 80px;">Jenis Pajak</th>
                <th style="width: 95px;">Dasar Pengenaan (DPP)</th>
                <th style="width: 45px;">Tarif</th>
                <th style="width: 95px;">Pajak Dipotong</th>
                <th style="width: 100px;">No. NTPN / Billing</th>
                <th style="width: 75px;">Tgl Setor</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($taxes as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $t->transaction?->transaction_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center font-mono font-bold">{{ $t->transaction?->evidence_number ?? '-' }}</td>
                    <td>{{ $t->transaction?->description ?? 'Potongan Pajak Belanja' }}</td>
                    <td class="text-center font-bold">{{ $t->tax_type }}</td>
                    <td class="text-right font-mono">{{ number_format($t->tax_base, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $t->rate_percentage }}%</td>
                    <td class="text-right font-mono font-bold">{{ number_format($t->tax_amount, 0, ',', '.') }}</td>
                    <td class="text-center font-mono" style="font-size: 8.5pt;">{{ $t->ntpn ?? ($t->billing_code ?? '-') }}</td>
                    <td class="text-center font-mono">{{ $t->deposit_date ? date('d/m/Y', strtotime($t->deposit_date)) : '-' }}</td>
                    <td class="text-center font-bold" style="color: {{ $t->status === 'Disetor' ? '#006600' : '#990000' }};">{{ $t->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada rekaman pajak pada tahun ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f7f7f7; font-weight: bold;">
                <td colspan="7" class="text-right">JUMLAH TOTAL PEMOTONGAN PAJAK:</td>
                <td class="text-right font-mono">Rp {{ number_format($totalTaxes, 0, ',', '.') }}</td>
                <td colspan="3" class="text-left font-mono" style="font-size: 8.5pt;">
                    Disetor: Rp {{ number_format($depositedTaxes, 0, ',', '.') }} &bull; Sisa: Rp {{ number_format($pendingTaxes, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

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
