<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman Sarpras - {{ $borrowing->borrowing_number }}</title>
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
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta-table td { padding: 4px 6px; font-size: 10.5pt; vertical-align: top; }
        .terms { font-size: 10pt; margin: 15px 0; line-height: 1.5; text-align: justify; }
        .terms ol { margin: 5px 0 5px 20px; padding: 0; }
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
    <x-print-toolbar :title="'Bukti Peminjaman Sarpras: ' . $borrowing->borrowing_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h4>SURAT BUKTI PEMINJAMAN SARANA PRASARANA</h4>
            <p>Nomor: <strong>{{ $borrowing->borrowing_number ?: ('000 / ' . str_pad((string)$borrowing->id, 3, '0', STR_PAD_LEFT) . ' / PL.04 - ' . ($school->name ?? 'SMAN 1 Lengkong') . ' / ' . \App\Services\LetterNumberService::toRoman((int)\Carbon\Carbon::parse($borrowing->borrow_date)->format('m')) . ' / ' . \Carbon\Carbon::parse($borrowing->borrow_date)->format('Y')) }}</strong></p>
        </div>

    <table class="meta-table">
        <tr>
            <td style="width: 150px;">Nama Peminjam</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $borrowing->borrower_name }}</strong> ({{ $borrowing->borrower_type }})</td>
        </tr>
        <tr>
            <td>Tanggal Peminjaman</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td>Batas Waktu Kembali</td>
            <td>:</td>
            <td><strong>{{ \Carbon\Carbon::parse($borrowing->expected_return_date)->translatedFormat('l, d F Y') }}</strong></td>
        </tr>
        <tr>
            <td>Sarana yang Dipinjam</td>
            <td>:</td>
            <td><strong>{{ $borrowing->good_or_asset_name }}</strong> (Jumlah: {{ $borrowing->quantity }} Unit)</td>
        </tr>
        <tr>
            <td>Status Saat Ini</td>
            <td>:</td>
            <td><strong>{{ $borrowing->status }}</strong></td>
        </tr>
        @if($borrowing->status === 'Dikembalikan')
        <tr>
            <td>Tanggal Kembali Nyata</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($borrowing->actual_return_date)->translatedFormat('d F Y') }} (Kondisi: {{ $borrowing->condition_on_return }})</td>
        </tr>
        @endif
        @if($borrowing->notes)
        <tr>
            <td>Keperluan / Catatan</td>
            <td>:</td>
            <td>{{ $borrowing->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="terms">
        <strong>KETENTUAN PEMINJAMAN SARANA PRASARANA SEKOLAH:</strong>
        <ol>
            <li>Peminjam bertanggung jawab penuh atas keutuhan, keamanan, dan kebersihan sarana yang dipinjam.</li>
            <li>Sarana hanya dipergunakan untuk keperluan kedinasan/pembelajaran satuan pendidikan {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}.</li>
            <li>Apabila terjadi kerusakan atau kehilangan selama masa peminjaman, peminjam wajib memperbaiki atau mengganti sarana tersebut sesuai dengan spesifikasi aslinya.</li>
            <li>Sarana wajib dikembalikan tepat waktu sesuai batas pengembalian yang telah disepakati.</li>
        </ol>
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Peminjam Sarana,</p>
            <br><br><br>
            <p><strong><u>{{ $borrowing->borrower_name }}</u></strong><br>
            {{ $borrowing->borrower_type }}</p>
        </div>
        <div class="sig-box float-right">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::parse($borrowing->borrow_date)->translatedFormat('d F Y') }}<br>{{ auth()->user()?->getRoleDisplayName() ?? 'Pengelola Sarpras / Petugas Peminjaman' }},</p>
            <br><br><br>
            <p><strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Sarpras') }}</u></strong><br>
            NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>
