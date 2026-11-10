<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Bukti Peminjaman Buku - {{ $loan->loan_code }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 15mm 20mm; }
        .title { text-align: center; margin-bottom: 15px; }
        .title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 10pt; font-family: monospace; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 4px 6px; vertical-align: top; font-size: 10pt; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 6px 8px; font-size: 10pt; }
        table.data-table th { background-color: #f1f5f9; text-transform: uppercase; font-weight: bold; text-align: center; }
        .section-box { border: 1px dashed #333; padding: 8px 12px; margin-bottom: 15px; font-size: 9.5pt; background: #fafafa; }
        .signature-table { width: 100%; margin-top: 25px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 33%; vertical-align: top; font-size: 10pt; }
        @media print {
            body { margin: 10mm 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Struk Peminjaman (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    @if(!empty($actualKopUrl))
        <div style="text-align: center; margin-bottom: 15px; border-bottom: 3px double #000; padding-bottom: 8px;">
            <img src="{{ $actualKopUrl }}" alt="KOP SURAT RESMI" style="max-width: 100%; height: auto; max-height: 120px;">
        </div>
    @else
        <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 14pt; text-transform: uppercase;">PEMERINTAH DAERAH PROVINSI {{ $schoolProvince }}</h3>
            <h3 style="margin: 0; font-size: 12pt; text-transform: uppercase;">DINAS PENDIDIKAN - {{ $schoolBranch }}</h3>
            <h2 style="margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase;">{{ $schoolName }}</h2>
            <p style="margin: 2px 0; font-size: 9pt;">{{ $schoolAddress }} • Telp: {{ $schoolPhone }} • Email: {{ $schoolEmail }}</p>
        </div>
    @endif

    <div class="title">
        <h4>BUKTI TRANSAKSI PEMINJAMAN KOLEKSI PUSTAKA</h4>
        <p>KODE TRANSAKSI: {{ $loan->loan_code }}</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 22%;">Nama Peminjam</td>
            <td style="width: 2%;">:</td>
            <td style="width: 36%; font-weight: bold;">{{ $loan->member?->name ?: '-' }}</td>
            <td style="width: 18%;">Tanggal Pinjam</td>
            <td style="width: 2%;">:</td>
            <td style="width: 20%; font-weight: bold;">{{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Nomor Anggota</td>
            <td>:</td>
            <td style="font-family: monospace;">{{ $loan->member?->member_code ?: '-' }}</td>
            <td>Batas Kembali</td>
            <td>:</td>
            <td style="font-weight: bold; color: #b91c1c;">{{ \Carbon\Carbon::parse($loan->due_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Status / Kelas</td>
            <td>:</td>
            <td>{{ $loan->member?->member_class_or_title ?: '-' }}</td>
            <td>Status Pinjam</td>
            <td>:</td>
            <td><strong>{{ $loan->status }}</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Kode Buku / Barcode</th>
                <th>Judul Buku & Pengarang</th>
                <th style="width: 18%;">Kategori / Rak</th>
                <th style="width: 12%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $loan->book?->book_code ?: '-' }}</td>
                <td>
                    <strong>{{ $loan->book?->title ?: '-' }}</strong><br>
                    <span style="font-size: 9pt; color: #475569;">Penulis: {{ $loan->book?->author ?: '-' }} • Penerbit: {{ $loan->book?->publisher ?: '-' }}</span>
                </td>
                <td style="text-align: center;">
                    {{ $loan->book?->category?->name ?: 'Umum' }}<br>
                    <span style="font-size: 8.5pt;">Rak: {{ $loan->book?->rack_location ?: '-' }}</span>
                </td>
                <td style="text-align: center; font-weight: bold;">1 Eksemplar</td>
            </tr>
        </tbody>
    </table>

    <div class="section-box">
        <strong>Ketentuan Pengembalian Pustaka:</strong>
        <ol style="margin: 4px 0 0 16px; padding: 0;">
            <li>Buku wajib dikembalikan sebelum atau pada tanggal jatuh tempo: <strong>{{ \Carbon\Carbon::parse($loan->due_date)->translatedFormat('d F Y') }}</strong>.</li>
            <li>Keterlambatan pengembalian buku dikenakan denda administrasi sesuai peraturan perpustakaan yang berlaku.</li>
            <li>Simpan struk bukti peminjaman ini sebagai bukti fisik transaksi sirkulasi yang sah.</li>
        </ol>
    </div>

    <!-- Tanda Tangan: Kepala Sekolah di KIRI (Mengetahui), Peminjam di TENGAH, Petugas Perpustakaan di KANAN -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left;">
                Mengetahui,<br>
                Kepala {{ $schoolName }},<br><br><br><br>
                <strong><u>{{ $headmasterName }}</u></strong><br>
                NIP. {{ $headmasterNip }}
            </td>
            <td style="text-align: center;">
                Peminjam Buku,<br><br><br><br>
                <strong><u>{{ $loan->member?->name }}</u></strong><br>
                ID. {{ $loan->member?->member_code }}
            </td>
            <td style="text-align: right;">
                Lengkong, {{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}<br>
                Petugas Perpustakaan,<br><br><br><br>
                <strong><u>{{ $librarianName }}</u></strong><br>
                NIP. {{ $librarianNip }}
            </td>
        </tr>
    </table>
</body>
</html>

