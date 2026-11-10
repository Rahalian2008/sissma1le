<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST Penerimaan Barang - {{ $receipt->receipt_number }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 10.5pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 14px 0; }
        .doc-title h4 { margin: 0; font-size: 12.5pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 2px 0; font-size: 10pt; font-family: monospace; }
        .statement { text-align: justify; margin-bottom: 10px; font-size: 10.5pt; line-height: 1.45; }
        table.party-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.party-table td { padding: 3px 5px; font-size: 10.5pt; vertical-align: top; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 6px; font-size: 9.5pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signatures { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 260px; text-align: center; font-size: 10pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'BAST Penerimaan Barang: ' . $receipt->receipt_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>BERITA ACARA SERAH TERIMA (BAST) PENERIMAAN BARANG</h4>
            <p>Nomor: <strong>{{ $receipt->receipt_number ?: ('000 / ' . str_pad((string)$receipt->id, 3, '0', STR_PAD_LEFT) . ' / PL.02 - ' . ($school->name ?? 'SMAN 1 Lengkong') . ' / ' . \App\Services\LetterNumberService::toRoman((int)\Carbon\Carbon::parse($receipt->receipt_date)->format('m')) . ' / ' . \Carbon\Carbon::parse($receipt->receipt_date)->format('Y')) }}</strong></p>
        </div>

    <div class="statement">
        Pada hari ini, <strong>{{ \Carbon\Carbon::parse($receipt->receipt_date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($receipt->receipt_date)->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini:
    </div>

    <table class="party-table">
        <tr>
            <td style="width: 25px;">1.</td>
            <td style="width: 140px;">Nama / Perusahaan</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $receipt->supplier_name ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>Keterangan</td>
            <td>:</td>
            <td>Pihak Penyedia / Rekanan Pengadaan / Pemberi Hibah, selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Nama Penerima</td>
            <td>:</td>
            <td><strong>{{ $receipt->receiver?->name ?? 'Pengurus Barang / Staf Sarpras' }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>Jabatan</td>
            <td>:</td>
            <td>Pengurus Barang / Panitia Penerima Hasil Pengadaan {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}, selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
        </tr>
    </table>

    <div class="statement">
        Menyatakan bahwa <strong>PIHAK PERTAMA</strong> telah menyerahkan kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> telah menerima dengan baik dan lengkap barang pengadaan bersumber dana <strong>{{ $receipt->funding_source }}</strong> dengan rincian sebagai berikut:
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>Nama Barang / Uraian Spesifikasi</th>
                <th style="width: 90px;">Jenis</th>
                <th style="width: 50px;">Vol</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 100px;">Harga Satuan (Rp)</th>
                <th style="width: 110px;">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $item->good?->name ?? 'Barang Inventaris' }}</strong>
                    @if($item->brand_model)
                    <div style="font-size: 8.5pt; color: #444;">Merk: {{ $item->brand_model }}</div>
                    @endif
                    @if($item->sim_bmd_code)
                    <div style="font-size: 8pt; font-family: monospace; color: #0284c7;">SIPD e-BMD: {{ $item->sim_bmd_code }} (KIB {{ $item->kib_type }})</div>
                    @endif
                </td>
                <td class="text-center">
                    {{ $item->item_type === 'inventaris' ? 'Aset Inventaris' : 'Habis Pakai' }}
                </td>
                <td class="text-center font-bold">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->good?->unit ?? 'Unit' }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($item->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #fafafa; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding-right: 10px;">JUMLAH TOTAL PENERIMAAN:</td>
                <td class="text-right font-mono">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="statement">
        Barang-barang tersebut di atas telah diperiksa dalam kondisi <strong>BAIK, LENGKAP, DAN SESUAI SPESIFIKASI</strong> serta telah dibukukan ke dalam administrasi persediaan gudang dan Buku Induk Inventaris (BII).
    </div>

    <div style="text-align: right; margin-top: 15px; margin-bottom: 5px; font-size: 9.5pt;">
        {{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::parse($receipt->receipt_date)->translatedFormat('d F Y') }}
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Yang Menyerahkan,<br><strong>PIHAK PERTAMA</strong></p>
            <br><br><br>
            <p><strong><u>{{ $receipt->supplier_name ?: 'Pihak Penyedia / Rekanan' }}</u></strong><br>
            Penyedia / Rekanan</p>
        </div>
        <div class="sig-box float-right">
            <p>Yang Menerima,<br><strong>PIHAK KEDUA</strong><br><span style="font-size: 8.5pt;">({{ $receipt->receiver?->getRoleDisplayName() ?? auth()->user()?->getRoleDisplayName() ?? 'Pengurus Barang / Petugas Logistik' }})</span></p>
            <br><br><br>
            <p><strong><u>{{ strtoupper($receipt->receiver?->getOfficialName() ?? auth()->user()?->getOfficialName() ?? 'Petugas Logistik') }}</u></strong><br>
            NIP. {{ $receipt->receiver?->getOfficialNip() ?? auth()->user()?->getOfficialNip() ?? '-' }}</p>
        </div>
        <div class="clear"></div>

        <div style="margin-top: 20px; text-align: center;">
            <p>Mengetahui,<br>Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} selaku Pengguna Barang,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>
    </div>
    </div>
</body>
</html>
