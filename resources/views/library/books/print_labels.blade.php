<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Nomor Panggil & Barcode Buku [{{ strtoupper($paper ?? 'A4') }}] - {{ $schoolName ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $isF4 = strtolower($paper ?? 'a4') === 'f4';
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: 8mm 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }

        /* Toolbar Kontrol Cetak (Hanya tampil di layar) */
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
            flex-wrap: wrap;
            gap: 12px;
        }

        .bar-title {
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bar-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .btn-print {
            background: #10b981;
            color: #fff;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
        }
        .btn-print:hover {
            background: #059669;
        }

        .btn-back {
            background: #334155;
            color: #e2e8f0;
        }
        .btn-back:hover {
            background: #475569;
            color: #fff;
        }

        .toggle-group {
            display: inline-flex;
            background: #1e293b;
            border-radius: 8px;
            padding: 2px;
            border: 1px solid #334155;
        }

        .toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            color: #94a3b8;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .toggle-btn.active {
            background: #6366f1;
            color: #fff;
        }

        /* Panduan Penempelan Stiker di Buku */
        .instruction-box {
            max-width: 210mm;
            margin: 12px auto;
            background: #fff;
            border-left: 4px solid #6366f1;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 11px;
            color: #334155;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .instruction-box strong {
            color: #1e1b4b;
        }

        /* Container Lembar Kertas */
        .sheet-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            min-height: {{ $isF4 ? '330mm' : '297mm' }};
            margin: 10px auto 30px auto;
            background: #fff;
            padding: 6mm 4mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* Grid Stiker Label */
        .labels-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4mm 3mm;
            justify-content: flex-start;
        }

        /* KARTU KOMBINASI LENGKAP: Spine + Barcode */
        .label-unit-combo {
            display: flex;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            background: #fff;
            page-break-inside: avoid;
            width: 96mm;
            height: 44mm;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* KARTU KHUSUS SPINE / PUNGGUNG BUKU (Ukuran Standar: 3.5cm x 4.2cm) */
        .spine-box {
            width: 38mm;
            height: 100%;
            border-right: 1px solid #000;
            padding: 2.5mm 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            background: #fff;
            box-sizing: border-box;
        }

        .spine-box.standalone {
            border: 1px solid #000;
            border-radius: 3px;
            height: 44mm;
            page-break-inside: avoid;
        }

        .spine-header {
            font-size: 6.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
            line-height: 1.1;
        }

        .spine-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 1.5px;
        }

        .call-ddc {
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        .call-author {
            font-size: 11pt;
            font-weight: 800;
            letter-spacing: 0.8px;
            line-height: 1;
        }

        .call-title {
            font-size: 11pt;
            font-weight: 700;
            line-height: 1;
        }

        .call-copy {
            font-size: 8pt;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #000;
            border-top: 1px dashed #64748b;
            padding-top: 1.5px;
            margin-top: 1.5px;
            width: 100%;
        }

        /* KARTU KHUSUS BARCODE & NOMOR INDUK */
        .barcode-box {
            flex: 1;
            height: 100%;
            padding: 2.5mm 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            box-sizing: border-box;
        }

        .barcode-box.standalone {
            width: 58mm;
            height: 44mm;
            border: 1px solid #000;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .barcode-header {
            font-size: 6.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #000;
            width: 100%;
            padding-bottom: 1.5mm;
            line-height: 1.1;
        }

        .barcode-svg-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1.5mm 0 1mm 0;
            overflow: hidden;
        }

        .barcode-svg-wrapper svg {
            width: 100%;
            max-width: 48mm;
            height: 16mm;
            display: block;
        }

        .barcode-text-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8.5pt;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1;
        }

        .barcode-book-title {
            font-size: 6.5pt;
            font-weight: bold;
            color: #1e293b;
            line-height: 1.15;
            max-height: 5.5mm;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
            border-top: 0.5px solid #cbd5e1;
            padding-top: 1mm;
            margin-top: 1mm;
        }

        /* Print Media Query */
        @media print {
            body {
                background: #fff;
            }
            .no-print, .no-print-bar, .instruction-box {
                display: none !important;
            }
            .sheet-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                max-width: 100%;
                width: 100%;
            }
            .label-unit-combo {
                border: 0.5px dashed #475569 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol Navigasi & Cetak (Tampil di Layar Monitor Saja) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span style="font-size: 18px;">🏷️</span>
            <div>
                <div>Cetak Label Punggung (Call Number) & Barcode Buku</div>
                <div style="font-size: 10px; color: #94a3b8; font-weight: normal;">
                    Standar Perpustakaan Nasional RI (Perpusnas) & Klasifikasi DDC • {{ count($labelItems) }} Label Siap Cetak
                </div>
            </div>
        </div>

        <div class="bar-controls">
            <!-- Pilihan Jenis Format Stiker -->
            <div class="toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['label_type' => 'both']) }}" class="toggle-btn {{ ($labelType ?? 'both') === 'both' ? 'active' : '' }}" title="Cetak Label Punggung + Barcode Berdampingan">
                    📌 Kombinasi Lengkap
                </a>
                <a href="{{ request()->fullUrlWithQuery(['label_type' => 'spine']) }}" class="toggle-btn {{ ($labelType ?? '') === 'spine' ? 'active' : '' }}" title="Hanya Label Punggung (Call Number)">
                    🏷️ Punggung Saja
                </a>
                <a href="{{ request()->fullUrlWithQuery(['label_type' => 'barcode']) }}" class="toggle-btn {{ ($labelType ?? '') === 'barcode' ? 'active' : '' }}" title="Hanya Label Barcode Nomor Induk">
                    📟 Barcode Saja
                </a>
            </div>

            <!-- Pilihan Ukuran Kertas -->
            <div class="toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="toggle-btn {{ !($isF4 ?? false) ? 'active' : '' }}">
                    A4
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="toggle-btn {{ ($isF4 ?? false) ? 'active' : '' }}">
                    F4 (Folio)
                </a>
            </div>

            <!-- Tombol Cetak Dokumen -->
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Cetak Stiker Sekarang</span>
            </button>

            @if(isset($book) && $book)
                <a href="{{ route('library.books.show', $book) }}" class="btn-action btn-back">
                    &larr; Kembali
                </a>
            @else
                <a href="{{ route('library.books.index') }}" class="btn-action btn-back">
                    &larr; Katalog Buku
                </a>
            @endif
        </div>
    </div>

    <!-- Kotak Panduan Penempelan Stiker (Hanya tampil di monitor) -->
    <div class="instruction-box no-print">
        <span style="font-size: 20px;">💡</span>
        <div>
            <strong>Panduan Penempelan Stiker Buku Menurut Standar Nasional Perpustakaan (SNP):</strong>
            <ul style="margin: 4px 0 0 16px; padding: 0; line-height: 1.4;">
                <li><strong>Label Punggung (Call Number):</strong> Tempelkan pada bagian punggung buku (spine) dengan jarak <strong>2 - 3 cm</strong> dari tepi bawah punggung buku agar terlihat rapi dan seragam di rak perpustakaan.</li>
                <li><strong>Label Barcode & Nomor Induk:</strong> Tempelkan pada halaman judul pertama (title page) atau di bagian dalam sampul belakang buku untuk memudahkan pemindaian saat transaksi sirkulasi peminjaman.</li>
                <li>Gunakan kertas stiker label HVS/Glossy A4 atau stiker potong, lalu lapisi dengan lakban bening (clear tape) agar label tidak cepat pudar dan tahan lama.</li>
            </ul>
        </div>
    </div>

    <!-- Lembar Kertas Cetak -->
    <div class="sheet-container">
        <div class="labels-grid">
            @forelse($labelItems as $item)
                @if(($labelType ?? 'both') === 'both')
                    <!-- 1. KARTU KOMBINASI LENGKAP (Label Punggung di kiri + Barcode di kanan) -->
                    <div class="label-unit-combo">
                        <!-- Sisi Kiri: Label Punggung (Spine Label / Call Number) -->
                        <div class="spine-box">
                            <div class="spine-header">
                                {{ $schoolName ?? 'SMAN 1 LENGKONG' }}
                            </div>
                            <div class="spine-content">
                                <div class="call-ddc">{{ $item['ddc'] }}</div>
                                <div class="call-author">{{ $item['author'] }}</div>
                                <div class="call-title">{{ $item['title_letter'] }}</div>
                            </div>
                            <div class="call-copy">{{ $item['copy_number'] }}</div>
                        </div>

                        <!-- Sisi Kanan: Label Barcode & Nomor Induk -->
                        <div class="barcode-box">
                            <div class="barcode-header">
                                {{ $libraryName ?? 'PERPUSTAKAAN SMAN 1 LENGKONG' }}
                            </div>
                            <div class="barcode-svg-wrapper">
                                {!! $item['barcode_svg'] !!}
                            </div>
                            <div class="barcode-text-code">
                                {{ $item['book_code'] }}
                            </div>
                            <div class="barcode-book-title" title="{{ $item['book']->title }}">
                                {{ $item['book']->title }}
                            </div>
                        </div>
                    </div>
                @elseif(($labelType ?? '') === 'spine')
                    <!-- 2. KHUSUS LABEL PUNGGUNG (SPINE LABEL ONLY) -->
                    <div class="spine-box standalone">
                        <div class="spine-header">
                            {{ $schoolName ?? 'SMAN 1 LENGKONG' }}
                        </div>
                        <div class="spine-content">
                            <div class="call-ddc">{{ $item['ddc'] }}</div>
                            <div class="call-author">{{ $item['author'] }}</div>
                            <div class="call-title">{{ $item['title_letter'] }}</div>
                        </div>
                        <div class="call-copy">{{ $item['copy_number'] }}</div>
                    </div>
                @elseif(($labelType ?? '') === 'barcode')
                    <!-- 3. KHUSUS LABEL BARCODE & NOMOR INDUK (BARCODE ONLY) -->
                    <div class="barcode-box standalone">
                        <div class="barcode-header">
                            {{ $libraryName ?? 'PERPUSTAKAAN SMAN 1 LENGKONG' }}
                        </div>
                        <div class="barcode-svg-wrapper">
                            {!! $item['barcode_svg'] !!}
                        </div>
                        <div class="barcode-text-code">
                            {{ $item['book_code'] }}
                        </div>
                        <div class="barcode-book-title" title="{{ $item['book']->title }}">
                            {{ $item['book']->title }}
                        </div>
                    </div>
                @endif
            @empty
                <div style="padding: 40px; text-align: center; width: 100%; color: #64748b;">
                    Tidak ada koleksi buku yang dipilih untuk dicetak labelnya.
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>

