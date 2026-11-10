<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Inventaris - {{ $asset->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            margin: 20px;
            color: #000;
        }
        .label-card {
            width: 85mm;
            height: 48mm;
            border: 2px solid #000;
            padding: 4mm;
            box-sizing: border-box;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .label-header {
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
            text-align: center;
        }
        .label-header h4 {
            margin: 0;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .label-header h3 {
            margin: 1px 0 0 0;
            font-size: 9.5pt;
            font-weight: bold;
        }
        .label-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4mm;
            margin: 2mm 0;
        }
        .qr-box {
            width: 25mm;
            height: 25mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-box svg {
            width: 100%;
            height: 100%;
        }
        .info-box {
            flex: 1;
            font-size: 7.5pt;
            line-height: 1.25;
        }
        .info-box .code {
            font-family: monospace;
            font-weight: bold;
            font-size: 8.5pt;
            margin-bottom: 2px;
            display: block;
        }
        .info-box .item-name {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 2px;
        }
        .label-footer {
            border-top: 1px dashed #000;
            padding-top: 2px;
            display: flex;
            justify-content: space-between;
            font-size: 6.5pt;
            color: #333;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 20px;
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
    <div class="print-btn-bar no-print">
        <span>Pratinjau Stiker Label Inventaris Barang Milik Sekolah</span>
        <div>
            <button onclick="window.print()">Cetak Label Stiker</button>
            <button onclick="window.close()" style="background: #475569; margin-left: 6px;">Tutup</button>
        </div>
    </div>

    <div class="label-card">
        <div class="label-header">
            <h4>BARANG MILIK DAERAH (SIPD e-BMD)</h4>
            <h3>{{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</h3>
            <div style="font-size: 6.5pt; color: #334155; text-transform: uppercase; font-weight: 600;">{{ strtoupper($school->branch_office ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</div>
        </div>

        <div class="label-body">
            <div class="info-box">
                <div style="font-size: 6.5pt; text-transform: uppercase; color: #475569; font-weight: bold; letter-spacing: 0.3px;">KODE BARANG (SIPD e-BMD):</div>
                <span class="code" style="font-size: 8.5pt; color: #0f172a; font-weight: 800; font-family: monospace;">{{ $asset->sim_bmd_code ?? $asset->code }}</span>
                <div style="font-size: 7.5pt; margin-top: 1px;"><span style="color: #64748b;">No. Register:</span> <strong>{{ $asset->register_number ?? $asset->code }}</strong> (KIB {{ $asset->kib_type }})</div>
                <div class="item-name" style="margin-top: 2px;">{{ $asset->name }}</div>
                <div><span style="color: #64748b;">Merek/Tipe:</span> {{ $asset->brand_model ?? '-' }}</div>
                <div><span style="color: #64748b;">Ruang:</span> {{ $asset->room?->name ?? 'Gudang' }}</div>
                <div><span style="color: #64748b;">Tahun:</span> {{ $asset->procurement_year }}</div>
            </div>
            <div class="qr-box">
                {!! $qrCodeSvg !!}
            </div>
        </div>

        <div class="label-footer">
            <span>Dilarang merusak / memindahkan label ini</span>
            <span>SIKATAS &bull; {{ $school->city ?? 'Kabupaten Sukabumi' }}</span>
        </div>
    </div>
</body>
</html>
