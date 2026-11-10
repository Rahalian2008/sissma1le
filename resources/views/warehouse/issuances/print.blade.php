<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Bukti Pengeluaran Barang - {{ $issuance->issuance_number }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 3px 0; font-size: 10pt; font-family: monospace; }
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta-table td { padding: 3px 5px; font-size: 10.5pt; vertical-align: top; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; font-size: 10pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .signatures { margin-top: 35px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 280px; text-align: center; font-size: 10.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Bukti Pengeluaran Barang: ' . $issuance->issuance_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>SURAT BUKTI PENGELUARAN BARANG (SBB) / BON GUDANG</h4>
            <p>Nomor: <strong>{{ $issuance->issuance_number ?: ('000 / ' . str_pad((string)$issuance->id, 3, '0', STR_PAD_LEFT) . ' / PL.03 - ' . ($school->name ?? 'SMAN 1 Lengkong') . ' / ' . \App\Services\LetterNumberService::toRoman((int)\Carbon\Carbon::parse($issuance->issuance_date)->format('m')) . ' / ' . \Carbon\Carbon::parse($issuance->issuance_date)->format('Y')) }}</strong></p>
        </div>

    <table class="meta-table">
        <tr>
            <td style="width: 140px;">Tanggal Pengeluaran</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ \Carbon\Carbon::parse($issuance->issuance_date)->translatedFormat('d F Y') }}</strong></td>
        </tr>
        <tr>
            <td>Nama Penerima / Unit</td>
            <td>:</td>
            <td><strong>{{ $issuance->recipient_name }}</strong></td>
        </tr>
        <tr>
            <td>Keperluan / Peruntukan</td>
            <td>:</td>
            <td>{{ $issuance->purpose }}</td>
        </tr>
        @if($issuance->notes)
        <tr>
            <td>Catatan</td>
            <td>:</td>
            <td>{{ $issuance->notes }}</td>
        </tr>
        @endif
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th style="width: 120px;">Kode Barang</th>
                <th>Nama Barang / Logistik yang Dikeluarkan</th>
                <th style="width: 80px;">Kategori</th>
                <th style="width: 90px;">Jumlah / Vol</th>
                <th style="width: 80px;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issuance->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $item->good?->code ?? '-' }}</td>
                <td><strong>{{ $item->good?->name ?? 'Barang Gudang' }}</strong></td>
                <td class="text-center">{{ $item->good?->category ?? 'ATK' }}</td>
                <td class="text-center font-bold" style="font-size: 11pt;">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->good?->unit ?? 'Pcs' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 10pt; margin-top: 10px;">
        Barang-barang logistik di atas telah dikeluarkan dari persediaan gudang sekolah dan diserahterimakan kepada pihak pemohon untuk digunakan sebagaimana mestinya.
    </p>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>{{ $issuance->approver?->getRoleDisplayName() ?? auth()->user()?->getRoleDisplayName() ?? 'Pengurus / Pengelola Gudang' }},</p>
            <br><br><br>
            <p><strong><u>{{ strtoupper($issuance->approver?->getOfficialName() ?? auth()->user()?->getOfficialName() ?? 'Petugas Gudang') }}</u></strong><br>
            NIP. {{ $issuance->approver?->getOfficialNip() ?? auth()->user()?->getOfficialNip() ?? '-' }}</p>
        </div>
        <div class="sig-box float-right">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::parse($issuance->issuance_date)->translatedFormat('d F Y') }}<br>Penerima Barang / Pemohon,</p>
            <br><br><br>
            <p><strong><u>{{ $issuance->recipient_name }}</u></strong><br>
            Unit Kerja / Jabatan</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>
