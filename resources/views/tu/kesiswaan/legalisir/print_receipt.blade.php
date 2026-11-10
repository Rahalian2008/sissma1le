<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Tanda Terima Legalisir - {{ $legalization->receipt_number }}</title>
    <style>
        @page {
            size: {{ request('paper', 'a4') === 'f4' ? '215mm 330mm' : 'A4 portrait' }};
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 10px;
            background: #fff;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .title-box {
            text-align: center;
            margin-bottom: 16px;
        }

        .title-box h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-box p {
            margin: 3px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
        }

        .receipt-badge {
            display: inline-block;
            border: 2px solid #000;
            padding: 4px 12px;
            font-family: monospace;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 1px;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10.5pt;
        }

        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        table.detail-table th,
        table.detail-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 10pt;
        }

        table.detail-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .notes-box {
            border: 1px dashed #444;
            padding: 8px 12px;
            margin-top: 10px;
            background-color: #fafafa;
            font-size: 9pt;
            line-height: 1.35;
        }

        .notes-box h5 {
            margin: 0 0 4px 0;
            font-size: 9.5pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10.5pt;
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
            <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="btn-paper {{ request('paper', 'a4') === 'a4' ? 'active' : '' }}">A4 (210 &times; 297 mm)</a>
            <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="btn-paper {{ request('paper') === 'f4' ? 'active' : '' }}">F4 / Folio (215 &times; 330 mm)</a>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Tanda Terima</button>
            <button onclick="window.close()" style="background: #475569; color: #fff; margin-left: 6px;">Tutup</button>
        </div>
    </div>

    <div class="container">
        <!-- Kop Surat Resmi Sekolah -->
        <x-print-kop :school="$school" />

        <!-- Judul Tanda Terima -->
        <div class="title-box">
            <h3>BUKTI TANDA TERIMA PERMOHONAN LEGALISIR DOKUMEN</h3>
            <p>PELAYANAN ADMINISTRASI KESISWAAN & TATA USAHA</p>
            <div class="receipt-badge">
                NO. AGENDA: {{ $legalization->receipt_number }}
            </div>
        </div>

        <!-- Data Pemohon -->
        <table class="info-table">
            <tr>
                <td style="width: 25%;">Tanggal Permohonan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 73%; font-weight: bold;">{{ $legalization->created_at->translatedFormat('l, d F Y - H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Nama Lengkap Pemohon</td>
                <td>:</td>
                <td style="font-weight: bold; text-transform: uppercase;">{{ $legalization->applicant_name }}</td>
            </tr>
            <tr>
                <td>NISN / NIS</td>
                <td>:</td>
                <td>{{ $legalization->nisn ?: '-' }} / {{ $legalization->nis ?: '-' }}</td>
            </tr>
            <tr>
                <td>Status / Tahun Kelulusan</td>
                <td>:</td>
                <td>{{ $legalization->graduation_year ?: ($legalization->student?->schoolClass?->name ? 'Siswa Aktif Kelas ' . $legalization->student->schoolClass->name : 'Alumni SMAN 1 Lengkong') }}</td>
            </tr>
            <tr>
                <td>Nomor WhatsApp / HP</td>
                <td>:</td>
                <td>{{ $legalization->phone_number ?: '-' }}</td>
            </tr>
            <tr>
                <td>Keperluan Legalisir</td>
                <td>:</td>
                <td>{{ $legalization->purpose ?: 'Melanjutkan Pendidikan / Keperluan Berkas Resmi' }}</td>
            </tr>
        </table>

        <!-- Rincian Dokumen yang Dilegalisir -->
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th style="width: 52%;">Jenis Dokumen yang Dilegalisir</th>
                    <th style="width: 20%;">Jumlah Lembar</th>
                    <th style="width: 20%;">Status Berkas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; font-weight: bold;">1</td>
                    <td>
                        <strong>{{ $legalization->document_type }}</strong>
                        @if($legalization->notes)
                            <br><small style="color: #444; font-style: italic;">Catatan: {{ $legalization->notes }}</small>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 11pt;">
                        {{ $legalization->copies_count }} Lembar
                    </td>
                    <td style="text-align: center; font-weight: bold; text-transform: uppercase;">
                        {{ $legalization->status }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Petunjuk Pengambilan & Verifikasi -->
        <div class="notes-box">
            <h5>Ketentuan & Informasi Pengambilan Legalisir:</h5>
            <ol style="margin: 0; padding-left: 18px;">
                <li>Lembar tanda terima ini <strong>wajib dibawa</strong> saat pengambilan dokumen hasil legalisir di loket Tata Usaha.</li>
                <li>Petugas berhak meminta pemohon memperlihatkan <strong>dokumen fisik asli</strong> untuk dicocokkan sebelum pengesahan stempel basah.</li>
                <li>Proses legalisir normal diselesaikan dalam waktu 1 &ndash; 2 hari kerja setelah berkas fotokopi dan dokumen asli dinyatakan valid.</li>
                <li>Layanan legalisir dokumen resmi di lingkungan {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} dilaksanakan sesuai ketentuan yang berlaku.</li>
            </ol>
        </div>

        <!-- Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    Pemohon / Siswa / Alumni,<br><br><br><br><br>
                    <strong><u>{{ $legalization->applicant_name }}</u></strong><br>
                    NISN. {{ $legalization->nisn ?: '-' }}
                </td>
                <td>
                    Lengkong, {{ $legalization->created_at->translatedFormat('d F Y') }}<br>
                    Petugas Pelayanan Adm. Kesiswaan,<br><br><br><br><br>
                    <strong><u>{{ $legalization->verifier?->name ?: 'Petugas Tata Usaha' }}</u></strong><br>
                    NIP. {{ $legalization->verifier?->nip ?: '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

