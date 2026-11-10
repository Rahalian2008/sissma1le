<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Stiker Inventaris - {{ $school->name ?? 'SIKATAS' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .no-print-bar button, .no-print-bar a {
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }
        .no-print-bar button:hover, .no-print-bar a:hover {
            background: #1d4ed8;
        }

        /* Label Container Grid */
        .labels-grid {
            display: grid;
            gap: 4mm;
            width: 100%;
            margin: 0 auto;
        }

        .layout-grid_2col {
            grid-template-columns: repeat(2, 1fr);
            max-width: 190mm;
        }

        .layout-grid_3col {
            grid-template-columns: repeat(3, 1fr);
            max-width: 200mm;
        }

        .layout-single {
            grid-template-columns: 1fr;
            max-width: 90mm;
        }

        /* Label Card */
        .label-card {
            background: #fff;
            border: 2px solid #000;
            padding: 3.5mm 4mm;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            break-inside: avoid;
            min-height: 48mm;
        }

        .layout-grid_3col .label-card {
            padding: 2.5mm 3mm;
            min-height: 44mm;
        }

        .label-header {
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
            text-align: center;
        }
        .label-header h4 {
            margin: 0;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .label-header h3 {
            margin: 1px 0 0 0;
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 1.15;
        }
        .label-header .branch {
            font-size: 6pt;
            color: #334155;
            text-transform: uppercase;
            font-weight: 600;
        }

        .label-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 3mm;
            margin: 2mm 0;
            flex: 1;
        }

        .qr-box {
            width: 24mm;
            height: 24mm;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .qr-box svg {
            width: 100%;
            height: 100%;
        }

        .layout-grid_3col .qr-box {
            width: 20mm;
            height: 20mm;
        }

        .info-box {
            flex: 1;
            font-size: 7pt;
            line-height: 1.25;
            overflow: hidden;
        }
        .layout-grid_3col .info-box {
            font-size: 6.5pt;
        }
        .info-box .code-title {
            font-size: 5.5pt;
            text-transform: uppercase;
            color: #475569;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .info-box .code {
            font-family: monospace;
            font-weight: 800;
            font-size: 7.5pt;
            color: #0f172a;
            display: block;
            margin-bottom: 1px;
            word-break: break-all;
        }
        .info-box .item-name {
            font-weight: bold;
            font-size: 7.5pt;
            margin: 1.5px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
        }
        .info-box .meta-row {
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .label-footer {
            border-top: 1px dashed #000;
            padding-top: 1.5px;
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            color: #333;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                margin: 0;
                padding: 5mm;
            }
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            .labels-grid {
                gap: 3mm;
            }
            .label-card {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print no-print-bar">
        <div>
            <strong style="font-size: 14px;">Pratinjau Cetak Label Stiker Inventaris</strong>
            <span style="opacity: 0.8; margin-left: 8px;">
                Total: <strong>{{ count($labels) }} Label</strong>
                @if($room) &bull; Ruangan: <strong>{{ $room->name }}</strong> @endif
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 11px; opacity: 0.9;">Tata Letak:</span>
            <a href="{{ request()->fullUrlWithQuery(['layout' => 'grid_2col']) }}" style="background: {{ ($layout ?? 'grid_2col') === 'grid_2col' ? '#0284c7' : '#334155' }};">2 Kolom (Standar)</a>
            <a href="{{ request()->fullUrlWithQuery(['layout' => 'grid_3col']) }}" style="background: {{ ($layout ?? '') === 'grid_3col' ? '#0284c7' : '#334155' }};">3 Kolom (Compact)</a>
            
            <span style="font-size: 11px; opacity: 0.9; margin-left: 6px;">Kertas:</span>
            <button type="button" id="btnPaperF4" onclick="setLabelPaper('f4')" style="background: #2563eb; padding: 5px 10px; font-size: 11px;">F4 (330mm)</button>
            <button type="button" id="btnPaperA4" onclick="setLabelPaper('a4')" style="background: #475569; padding: 5px 10px; font-size: 11px;">A4 (297mm)</button>

            <button onclick="window.print()" style="background: #10b981;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Semua Label
            </button>
            <button onclick="window.close()" style="background: #64748b;">Tutup</button>
        </div>
    </div>

    <script>
        function setLabelPaper(size) {
            let style = document.getElementById('label-print-paper-style');
            if (!style) {
                style = document.createElement('style');
                style.id = 'label-print-paper-style';
                document.head.appendChild(style);
            }
            const btnF4 = document.getElementById('btnPaperF4');
            const btnA4 = document.getElementById('btnPaperA4');
            if (size === 'f4') {
                style.innerHTML = '@media print { @page { size: 215mm 330mm; margin: 8mm; } }';
                if (btnF4) btnF4.style.background = '#2563eb';
                if (btnA4) btnA4.style.background = '#475569';
            } else {
                style.innerHTML = '@media print { @page { size: 210mm 297mm; margin: 8mm; } }';
                if (btnF4) btnF4.style.background = '#475569';
                if (btnA4) btnA4.style.background = '#2563eb';
            }
        }
        setLabelPaper('f4');
    </script>

    @if(empty($labels) || count($labels) === 0)
        <div style="text-align: center; padding: 40px; color: #64748b; font-size: 14px; background: #fff; border-radius: 12px; max-width: 500px; margin: 40px auto; border: 1px dashed #cbd5e1;">
            <p>Tidak ada aset yang dipilih atau ditemukan untuk dicetak labelnya.</p>
            <a href="{{ route('sarpras.assets.labels.custom') }}" style="color: #2563eb; font-weight: bold; text-decoration: underline;">Buka Menu Kustomisasi Cetak Label</a>
        </div>
    @else
        <div class="labels-grid layout-{{ $layout ?? 'grid_2col' }}">
            @foreach($labels as $item)
                @php
                    $ast = $item['asset'];
                @endphp
                <div class="label-card">
                    <div class="label-header">
                        <h4>BARANG MILIK DAERAH (SIPD e-BMD)</h4>
                        <h3>{{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</h3>
                        <div class="branch">{{ strtoupper($school->branch_office ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</div>
                    </div>

                    <div class="label-body">
                        <div class="info-box">
                            <div class="code-title">KODE BARANG (SIPD e-BMD):</div>
                            <span class="code">{{ $ast->sim_bmd_code ?? $ast->code }}</span>
                            <div class="meta-row">
                                <span style="color: #64748b;">No. Reg:</span> 
                                <strong>{{ $ast->register_number ?? $ast->code }}</strong> (KIB {{ $ast->kib_type }})
                            </div>
                            <div class="item-name">{{ $ast->name }}</div>
                            <div class="meta-row"><span style="color: #64748b;">Merek/Tipe:</span> {{ $ast->brand_model ?? '-' }}</div>
                            <div class="meta-row"><span style="color: #64748b;">Ruang:</span> {{ $ast->room?->name ?? 'Gudang' }}</div>
                            <div class="meta-row"><span style="color: #64748b;">Tahun:</span> {{ $ast->procurement_year }}</div>
                        </div>
                        <div class="qr-box">
                            {!! $item['qrCodeSvg'] !!}
                        </div>
                    </div>

                    <div class="label-footer">
                        <span>Dilarang merusak / memindahkan label ini</span>
                        <span>SIKATAS &bull; {{ $school->city ?? 'Kabupaten Sukabumi' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</body>
</html>
