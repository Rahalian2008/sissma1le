<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIR - {{ $room->name }} | {{ $school->name ?? 'SIKATAS' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
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
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .header-kop img {
            width: 75px;
            height: 75px;
            object-fit: contain;
            margin-right: 15px;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text h3 { margin: 0; font-size: 12pt; font-weight: normal; letter-spacing: 1px; }
        .header-text h2 { margin: 2px 0; font-size: 13pt; font-weight: bold; }
        .header-text h1 { margin: 2px 0; font-size: 15pt; font-weight: 800; }
        .header-text p { margin: 2px 0; font-size: 8.5pt; font-style: italic; }

        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f2f2f2;
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
            font-size: 9.5pt;
        }
        .ttd-table td {
            vertical-align: top;
            padding: 5px;
        }
        .ttd-space {
            height: 65px;
        }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Kartu Inventaris Ruangan (KIR): ' . $room->name" orientation="landscape" defaultPaper="f4" />

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
                    <div><strong>SISTEM INVENTARIS</strong> : SIPD e-BMD</div>
                    <div><strong>KODE UPB / NPSN</strong> : {{ $school->npsn ?? '20202265' }}</div>
                    <div><strong>TAHUN ANGGARAN</strong> : {{ date('Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center" style="margin-bottom: 15px;">
        <div class="font-bold" style="font-size: 13pt; text-decoration: underline; letter-spacing: 0.5px;">KARTU INVENTARIS RUANGAN (KIR) - SIPD e-BMD</div>
        <div style="font-size: 9pt; margin-top: 2px;">Berdasarkan Standar SIPD e-BMD Provinsi Jawa Barat & Permendagri No. 108 Tahun 2016</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;" class="font-bold">Nama Ruangan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;" class="font-bold uppercase">{{ $room->name }}</td>
            <td style="width: 18%;" class="font-bold">Penanggung Jawab (PJ)</td>
            <td style="width: 2%;">:</td>
            <td style="width: 25%;">{{ $room->person_in_charge ?? 'Belum Ditentukan' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Kode Ruangan</td>
            <td>:</td>
            <td>{{ $room->code }}</td>
            <td class="font-bold">Kondisi Ruangan</td>
            <td>:</td>
            <td>{{ $room->condition ?? 'Baik' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Kapasitas Ruang</td>
            <td>:</td>
            <td>{{ $room->capacity }} Orang</td>
            <td class="font-bold">Tahun Perolehan / Cetak</td>
            <td>:</td>
            <td>Tahun Anggaran {{ date('Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Kodefikasi SIPD e-BMD</th>
                <th style="width: 7%;">No. Reg</th>
                <th style="width: 18%;">Nama / Jenis Barang</th>
                <th style="width: 13%;">Merek / Model / Tipe</th>
                <th style="width: 10%;">No. Seri Pabrik</th>
                <th style="width: 6%;">Tahun</th>
                <th style="width: 8%;">Keadaan Barang</th>
                <th style="width: 6%;">Jumlah</th>
                <th style="width: 9%;">Harga Satuan</th>
                <th style="width: 5%;">KIB</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $idx => $ast)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-mono text-center">{{ $ast->sim_bmd_code ?? $ast->code }}</td>
                    <td class="font-mono text-center font-bold">{{ $ast->register_range ?? $ast->register_number }}</td>
                    <td class="font-bold">{{ $ast->name }}</td>
                    <td>{{ $ast->brand_model ?? '-' }}</td>
                    <td class="font-mono">{{ $ast->serial_number ?? '-' }}</td>
                    <td class="text-center">{{ $ast->procurement_year }}</td>
                    <td class="text-center">{{ $ast->condition }}</td>
                    <td class="text-center font-bold">{{ $ast->quantity ?? 1 }} {{ $ast->unit ?? 'Unit' }}</td>
                    <td class="text-right">Rp {{ number_format($ast->price, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">KIB {{ $ast->kib_type ?? 'B' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada barang inventaris yang terdaftar pada ruangan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8fafc;">
                <td colspan="8" class="text-right" style="padding: 6px;">TOTAL NILAI INVENTARIS RUANGAN:</td>
                <td class="text-center">{{ $assets->sum(fn($a) => $a->quantity ?? 1) }} Unit</td>
                <td class="text-right">Rp {{ number_format($assets->sum(fn($a) => $a->total_price ?? $a->price), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                <td style="width: 33%;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah selaku Pengguna Barang</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
                </td>
                <td style="width: 34%;">
                    Pengurus / Pengelola Barang Milik Daerah<br>
                    <strong>{{ auth()->user()?->getRoleDisplayName() ?? 'Pengelola Sarpras (SIPD e-BMD)' }}</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Operasional') }}</u></strong><br>
                    NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
                </td>
                <td style="width: 33%;">
                    {{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Penanggung Jawab Ruangan</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $room->person_in_charge ?? '( ................................................. )' }}</u></strong><br>
                    NIP. {{ \App\Models\Employee::where('name', $room->person_in_charge)->orWhereRaw("CONCAT(COALESCE(CONCAT(title_prefix, ' '), ''), name, COALESCE(CONCAT(', ', title_suffix), '')) = ?", [$room->person_in_charge])->first()?->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    </div>
</body>
</html>
