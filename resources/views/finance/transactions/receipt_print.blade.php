<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran - {{ $cashTransaction->evidence_number }}</title>
    <style>
        @page { size: portrait; margin: 15mm 15mm 15mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 10px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 13.5pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; }
        .doc-title p { margin: 3px 0; font-size: 10pt; font-family: monospace; }
        
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta-table td { padding: 4px 6px; font-size: 11pt; vertical-align: top; }
        
        .amount-box {
            border: 2px solid #000;
            padding: 8px 12px;
            font-size: 13pt;
            font-weight: bold;
            font-family: monospace;
            background-color: #f7f7f7;
            display: inline-block;
            margin: 10px 0;
        }

        .terbilang-box {
            background-color: #f2f2f2;
            border-left: 4px solid #000;
            padding: 6px 12px;
            font-style: italic;
            font-size: 10.5pt;
            margin: 6px 0 12px 0;
        }

        .tax-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        .tax-table th, .tax-table td { border: 1px solid #000; padding: 5px 8px; font-size: 10pt; }
        .tax-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .logistics-ref {
            border: 1px dashed #444;
            background: #fafafa;
            padding: 8px 12px;
            font-size: 10pt;
            margin: 10px 0;
            border-radius: 4px;
        }

        .signatures { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 31%; text-align: center; font-size: 10pt; float: left; }
        .clear { clear: both; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Kuitansi: ' . $cashTransaction->evidence_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>KUITANSI / BUKTI PENGELUARAN KAS</h4>
            <p>Nomor BKU: <strong>{{ $cashTransaction->evidence_number }}</strong> &bull; Sumber Dana: <strong>{{ $cashTransaction->funding_source }}</strong></p>
        </div>

    <table class="meta-table">
        <tr>
            <td style="width: 170px;">Tahun Anggaran</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $cashTransaction->transaction_date->format('Y') }}</strong></td>
        </tr>
        <tr>
            <td>Kode Rekening / Akun</td>
            <td>:</td>
            <td>
                <strong>{{ $cashTransaction->account_code ?? ($cashTransaction->budgetItem?->account_code ?? '5.1.02 (Belanja Barang & Jasa)') }}</strong>
                @if($cashTransaction->budgetItem)
                    &mdash; <span style="font-size: 10pt;">{{ $cashTransaction->budgetItem->activity_name }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Telah Diterima Dari</td>
            <td>:</td>
            <td><strong>Kuasa Pengguna Anggaran / Bendahara Pengeluaran {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</strong></td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td>:</td>
            <td>
                <div class="amount-box">Rp {{ number_format($amount, 0, ',', '.') }}</div>
                <div class="terbilang-box">"{{ $terbilang }}"</div>
            </td>
        </tr>
        <tr>
            <td>Untuk Pembayaran</td>
            <td>:</td>
            <td><strong>{{ $cashTransaction->description }}</strong></td>
        </tr>
        <tr>
            <td>Pihak Penerima / Rekanan</td>
            <td>:</td>
            <td><strong>{{ $cashTransaction->recipient_or_payer }}</strong></td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td>:</td>
            <td>{{ $cashTransaction->payment_method }}</td>
        </tr>
        @if($cashTransaction->spk_number || $cashTransaction->invoice_number)
        <tr>
            <td>Dasar SPK / Faktur</td>
            <td>:</td>
            <td>
                @if($cashTransaction->spk_number) SPK: <strong>{{ $cashTransaction->spk_number }}</strong> @endif
                @if($cashTransaction->invoice_number) &bull; Faktur/Nota: <strong>{{ $cashTransaction->invoice_number }}</strong> @endif
            </td>
        </tr>
        @endif
    </table>

    {{-- Keterkaitan Logistik Gudang & Sarpras --}}
    @if($cashTransaction->goodsReceipt)
        <div class="logistics-ref">
            <strong>KETERKAITAN LOGISTIK GUDANG (BARANG MASUK / BAST):</strong><br>
            Nomor BAST: <strong>{{ $cashTransaction->goodsReceipt->receipt_number }}</strong> | Tanggal Terima: {{ $cashTransaction->goodsReceipt->receipt_date->format('d/m/Y') }}<br>
            Penyedia: <strong>{{ $cashTransaction->goodsReceipt->supplier_name ?: '-' }}</strong> &bull; Total Nilai BAST: Rp {{ number_format($cashTransaction->goodsReceipt->total_amount, 0, ',', '.') }}
        </div>
    @elseif($cashTransaction->asset)
        <div class="logistics-ref">
            <strong>KETERKAITAN SARPRAS (PENGADAAN ASET INVENTARIS BMD):</strong><br>
            Aset: <strong>{{ $cashTransaction->asset->name }}</strong> (Kode: {{ $cashTransaction->asset->code }}) &bull; Kategori: {{ $cashTransaction->asset->kib_name }}<br>
            Nilai Perolehan Aset: Rp {{ number_format($cashTransaction->asset->price, 0, ',', '.') }}
        </div>
    @endif

    {{-- Rincian Pajak Jika Ada --}}
    @if($cashTransaction->taxes->isNotEmpty())
        <table class="tax-table">
            <thead>
                <tr>
                    <th>Jenis Potongan Pajak</th>
                    <th>Dasar Pengenaan Pajak (DPP)</th>
                    <th>Tarif (%)</th>
                    <th>Nominal Pajak Dipotong</th>
                    <th>Status / NTPN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cashTransaction->taxes as $tax)
                    <tr>
                        <td class="text-center font-bold">{{ $tax->tax_type }}</td>
                        <td class="text-right">Rp {{ number_format($tax->tax_base, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $tax->rate_percentage }}%</td>
                        <td class="text-right font-bold font-mono">Rp {{ number_format($tax->tax_amount, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $tax->status }} {{ $tax->ntpn ? '('.$tax->ntpn.')' : '' }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #fafafa; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL POTONGAN PAJAK:</td>
                    <td class="text-right font-mono">Rp {{ number_format($totalTax, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr style="background-color: #eee; font-weight: bold; font-size: 10.5pt;">
                    <td colspan="3" class="text-right">JUMLAH BERSIH YANG DIBAYARKAN (NETTO):</td>
                    <td class="text-right font-mono" style="color: #900;">Rp {{ number_format($netAmount, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div style="font-size: 9.5pt; font-style: italic; margin-bottom: 15px;">
            Terbilang Jumlah Bersih: "{{ $netTerbilang }}"
        </div>
    @endif

    <div class="signatures">
        <div class="sig-box">
            <p>Setuju dibayar,<br><strong>Kepala Sekolah</strong><br>Selaku Kuasa Pengguna Anggaran</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>

        <div class="sig-box">
            <p>Lunas dibayar,<br><strong>Bendahara Pengeluaran</strong><br>BOSP / Satuan Pendidikan</p>
            <br><br><br>
            <p><strong><u>{{ Auth::user()->name ?? 'Bendahara Sekolah' }}</u></strong><br>
            NIP. ................................................</p>
        </div>

        <div class="sig-box">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ $cashTransaction->transaction_date->translatedFormat('d F Y') }}<br>Yang Menerima Uang,<br><strong>Penerima / Rekanan</strong></p>
            <br><br><br>
            <p><strong><u>{{ $cashTransaction->recipient_or_payer }}</u></strong><br>
            Tanda Tangan / Cap Perusahaan</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>
