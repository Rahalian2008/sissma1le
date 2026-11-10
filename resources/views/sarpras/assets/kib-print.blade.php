<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIB {{ $kibType }} - {{ $kibInfo['title'] }} | {{ $school->name ?? 'SIKATAS' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.3;
            margin: 20px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header-kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .header-kop img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 15px;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text h3 { margin: 0; font-size: 11pt; font-weight: normal; letter-spacing: 1px; }
        .header-text h2 { margin: 2px 0; font-size: 12pt; font-weight: bold; }
        .header-text h1 { margin: 2px 0; font-size: 14pt; font-weight: 800; }
        .header-text p { margin: 2px 0; font-size: 8.5pt; font-style: italic; }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 5px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .ttd-section {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-table td {
            vertical-align: top;
            padding: 5px;
        }
        .ttd-space {
            height: 60px;
        }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Kartu Inventaris Barang (KIB ' . $kibType . ')'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

    <div style="margin-bottom: 10px; font-size: 8pt; text-transform: uppercase;">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 65%; padding: 0;">
                    <div><strong>PROVINSI</strong> : {{ strtoupper($school->province ?? 'JAWA BARAT') }}</div>
                    <div><strong>SKPD</strong> : DINAS PENDIDIKAN</div>
                    <div><strong>CABANG DINAS</strong> : {{ strtoupper($school->branch_office ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</div>
                    <div><strong>KABUPATEN</strong> : {{ strtoupper($school->city ?? 'KABUPATEN SUKABUMI') }}</div>
                    <div><strong>SATUAN PENDIDIKAN</strong> : {{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</div>
                </td>
                <td style="border: none; width: 35%; text-align: right; padding: 0;">
                    <div><strong>SISTEM ASET</strong> : SIPD e-BMD</div>
                    <div><strong>KODE UPB / NPSN</strong> : {{ $school->npsn ?? '20202265' }}</div>
                    <div><strong>TAHUN ANGGARAN</strong> : {{ date('Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center" style="margin-bottom: 15px;">
        <div class="font-bold" style="font-size: 13pt; text-decoration: underline; letter-spacing: 0.5px;">KARTU INVENTARIS BARANG (KIB {{ $kibType }}) - SIPD e-BMD</div>
        <div class="font-bold uppercase" style="font-size: 11pt; margin-top: 2px;">{{ $kibInfo['title'] }} (KODE AKUN: {{ $kibInfo['code_prefix'] }})</div>
        <div style="font-size: 8.5pt; color: #475569;">Sesuai Aturan Standar SIPD e-BMD Provinsi Jawa Barat & Permendagri No. 108 Tahun 2016</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;" class="font-bold">Satuan Kerja / OPD</td>
            <td style="width: 2%;">:</td>
            <td style="width: 45%;">Dinas Pendidikan Provinsi Jawa Barat / {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</td>
            <td style="width: 15%;" class="font-bold">Tahun Anggaran</td>
            <td style="width: 2%;">:</td>
            <td style="width: 21%;">{{ date('Y') }}</td>
        </tr>
        <tr>
            <td class="font-bold">Klasifikasi Aset</td>
            <td>:</td>
            <td>KIB {{ $kibType }} — {{ $kibInfo['title'] }}</td>
            <td class="font-bold">Total Unit Barang</td>
            <td>:</td>
            <td><strong>{{ $assets->sum(fn($a) => $a->quantity ?? 1) }} Unit</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Kodefikasi SIPD e-BMD</th>
                <th style="width: 8%;">No. Reg</th>
                <th style="width: 20%;">Nama / Jenis Barang</th>
                <th style="width: 13%;">Merek / Tipe / Spesifikasi</th>
                <th style="width: 10%;">No. Sertifikat / Pabrik</th>
                <th style="width: 5%;">Tahun</th>
                <th style="width: 10%;">Asal Usul / Sumber</th>
                <th style="width: 6%;">Keadaan</th>
                <th style="width: 10%;">Harga Perolehan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $idx => $ast)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-mono text-center">{{ $ast->sim_bmd_code ?? $ast->code }}</td>
                    <td class="font-mono text-center font-bold">{{ $ast->register_range ?? $ast->register_number }}</td>
                    <td class="font-bold">
                        {{ $ast->name }}
                        @if(($ast->quantity ?? 1) > 1)
                            <span style="font-size: 8pt; color: #1e40af; font-weight: bold;">(Jumlah: {{ $ast->quantity }} {{ $ast->unit ?? 'Unit' }})</span>
                        @endif
                        @if($ast->room)
                            <div style="font-size: 7.5pt; color: #2563eb; font-weight: normal;">📍 Lokasi KIR: {{ $ast->room->name }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $ast->brand_model ?? '-' }}
                        @if($ast->specification)
                            <div style="font-size: 7.5pt; color: #475569;">{{ $ast->specification }}</div>
                        @endif
                    </td>
                    <td class="font-mono text-center">{{ $ast->serial_number ?? '-' }}</td>
                    <td class="text-center">{{ $ast->procurement_year }}</td>
                    <td>{{ $ast->funding_source ?? 'BOSP' }}</td>
                    <td class="text-center">{{ $ast->condition }}</td>
                    <td class="text-right">
                        Rp {{ number_format($ast->total_price ?? $ast->price, 0, ',', '.') }}
                        @if(($ast->quantity ?? 1) > 1)
                            <div style="font-size: 7pt; color: #64748b;">(@ Rp {{ number_format($ast->price, 0, ',', '.') }})</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada barang inventaris pada kategori KIB {{ $kibType }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8fafc;">
                <td colspan="9" class="text-right" style="padding: 6px;">TOTAL NILAI ASET KIB {{ $kibType }}:</td>
                <td class="text-right">Rp {{ number_format($assets->sum(fn($a) => $a->total_price ?? $a->price), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah selaku Pengguna Barang</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
                </td>
                <td style="width: 50%;">
                    {{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>{{ auth()->user()?->getRoleDisplayName() ?? 'Pengurus / Pengelola BMD' }}</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Operasional') }}</u></strong><br>
                    NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    </div>
</body>
</html>
