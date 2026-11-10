<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Agenda Legalisir Dokumen Siswa - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page {
            size: {{ request('paper', 'f4') === 'a4' ? 'A4 landscape' : '330mm 215mm' }};
            margin: 12mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 10px;
            background: #fff;
        }

        .title-box {
            text-align: center;
            margin-bottom: 14px;
        }

        .title-box h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .title-box p {
            margin: 2px 0 0 0;
            font-size: 9.5pt;
        }

        table.agenda-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.agenda-table th,
        table.agenda-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 9pt;
            vertical-align: middle;
        }

        table.agenda-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }

        .no-print {
            background: #0f172a;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: sans-serif;
            font-size: 12px;
        }

        .no-print a, .no-print button {
            cursor: pointer;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: bold;
            border: none;
        }

        .btn-print {
            background: #10b981;
            color: #fff;
        }

        .btn-paper {
            background: #334155;
            color: #cbd5e1;
            margin-left: 4px;
        }

        .btn-paper.active {
            background: #6366f1;
            color: #fff;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Pengaturan Ukuran Kertas & Cetak (Non-Print) -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: bold;">Pilihan Format Kertas:</span>
            <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="btn-paper {{ request('paper', 'f4') === 'f4' ? 'active' : '' }}">F4 / Folio Landscape (330 &times; 215 mm)</a>
            <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="btn-paper {{ request('paper') === 'a4' ? 'active' : '' }}">A4 Landscape (297 &times; 210 mm)</a>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Buku Agenda</button>
            <button onclick="window.close()" style="background: #475569; color: #fff; margin-left: 6px;">Tutup</button>
        </div>
    </div>

    <!-- Kop Surat Resmi Sekolah -->
    <x-print-kop :school="$school" />

    <div class="title-box">
        <h3>BUKU AGENDA & EKSPEDISI LEGALISIR DOKUMEN SISWA</h3>
        <p>SUBBAGIAN TATA USAHA &bull; ADMINISTRASI KESISWAAN {{ strtoupper($school->name ?? 'SMAN 1 LENGKONG') }}</p>
    </div>

    <table class="agenda-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tgl Masuk</th>
                <th style="width: 12%;">No. Agenda / Resi</th>
                <th style="width: 17%;">Nama Pemohon & NISN</th>
                <th style="width: 9%;">Tahun Lulus / Kelas</th>
                <th style="width: 14%;">Jenis Dokumen</th>
                <th style="width: 5%;">Jml</th>
                <th style="width: 15%;">Keperluan Legalisir</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 9%;">Paraf Pengambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($legalizations as $idx => $row)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ $row->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $row->receipt_number }}</td>
                    <td>
                        <strong>{{ $row->applicant_name }}</strong><br>
                        <small>NISN: {{ $row->nisn ?: '-' }}</small>
                    </td>
                    <td style="text-align: center;">{{ $row->graduation_year ?: ($row->student?->schoolClass?->name ?? 'Alumni') }}</td>
                    <td>{{ $row->document_type }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row->copies_count }}</td>
                    <td><small>{{ $row->purpose ?: '-' }}</small></td>
                    <td style="text-align: center; font-weight: bold; text-transform: uppercase;">
                        <small>{{ $row->status }}</small>
                    </td>
                    <td style="text-align: center; color: #888;">
                        @if($row->status === 'selesai')
                            <small style="color: #000;">[ Terambil ]</small>
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #888;">
                        Tidak ada catatan permohonan legalisir pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Kiri dengan Status Mengetahui) -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }},<br><br><br><br><br>
                <strong><u>{{ $headmasterName }}</u></strong><br>
                NIP. {{ $headmasterNip }}
            </td>
            <td>
                Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Pengelola Administrasi Kesiswaan,<br><br><br><br><br>
                <strong><u>{{ Auth::user()->name ?? 'Petugas Tata Usaha' }}</u></strong><br>
                NIP. {{ Auth::user()->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>

