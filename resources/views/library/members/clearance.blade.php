<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Bebas Perpustakaan - {{ $member->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 15mm 20mm; }
        .title { text-align: center; margin-bottom: 25px; }
        .title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 10.5pt; }
        table.meta { width: 100%; border-collapse: collapse; margin: 15px 0 20px 20px; }
        table.meta td { padding: 4px 6px; vertical-align: top; }
        .content-body { text-align: justify; margin-bottom: 25px; }
        .status-box { border: 2px solid #16a34a; background-color: #f0fdf4; padding: 12px; border-radius: 6px; text-align: center; font-weight: bold; color: #166534; margin: 20px 0; }
        .status-box-warning { border: 2px solid #dc2626; background-color: #fef2f2; padding: 12px; border-radius: 6px; text-align: center; font-weight: bold; color: #991b1b; margin: 20px 0; }
        .signature-table { width: 100%; margin-top: 35px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: top; font-size: 11pt; }
        @media print {
            body { margin: 10mm 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Surat Keterangan Bebas Perpustakaan (Print)
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
        <h4>SURAT KETERANGAN BEBAS PERPUSTAKAAN</h4>
        <p>Nomor: 421.3/SKBP/PERPUS/{{ date('Y') }}/{{ str_pad((string)$member->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="content-body">
        <p>Yang bertanda tangan di bawah ini, Kepala Perpustakaan {{ $schoolName }}, menerangkan bahwa:</p>

        <table class="meta">
            <tr>
                <td style="width: 28%;">Nama Lengkap</td>
                <td style="width: 3%;">:</td>
                <td style="width: 69%; font-weight: bold;">{{ $member->name }}</td>
            </tr>
            <tr>
                <td>Nomor Anggota</td>
                <td>:</td>
                <td style="font-family: monospace;">{{ $member->member_code }}</td>
            </tr>
            <tr>
                <td>{{ strtoupper($member->member_type) === 'SISWA' ? 'NISN / NIS' : 'NIP / NUPTK' }}</td>
                <td>:</td>
                <td>{{ $member->identity_number ?: '-' }}</td>
            </tr>
            <tr>
                <td>Kelas / Jabatan</td>
                <td>:</td>
                <td>{{ $member->member_class_or_title }}</td>
            </tr>
        </table>

        @if($isCleared)
            <div class="status-box">
                MENYATAKAN BAHWA NAMA TERSEBUT DI ATAS BEBAS DARI SEGALA PINJAMAN KOLEKSI PUSTAKA DAN TANGGUNGAN ADMINISTRASI PERPUSTAKAAN
            </div>
            <p>
                Berdasarkan data sistem sirkulasi perpustakaan per tanggal <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>, yang bersangkutan telah mengembalikan seluruh buku pinjaman dalam keadaan baik dan tidak memiliki tunggakan denda maupun kewajiban lainnya di Perpustakaan {{ $schoolName }}.
            </p>
            <p>
                Surat keterangan ini diberikan sebagai kelengkapan administrasi kelulusan, mutasi/pindah sekolah, atau keperluan dinas lainnya.
            </p>
        @else
            <div class="status-box-warning">
                PERINGATAN: MASIH TERDAPAT {{ $activeLoansCount }} PINJAMAN BUKU AKTIF / TUNGGAKAN DENDA SEBESAR RP {{ number_format($unpaidFines, 0, ',', '.') }}
            </div>
            <p>
                Peserta didik / anggota bersangkutan belum dapat dinyatakan bebas perpustakaan sebelum menyelesaikan pengembalian koleksi buku fisik dan pelunasan tanggungan denda perpustakaan.
            </p>
        @endif
    </div>

    <!-- Tanda Tangan: Kepala Sekolah di KIRI (Mengetahui), Kepala Perpustakaan di KANAN -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left;">
                Mengetahui,<br>
                Kepala {{ $schoolName }},<br><br><br><br><br>
                <strong><u>{{ $headmasterName }}</u></strong><br>
                NIP. {{ $headmasterNip }}
            </td>
            <td style="text-align: right;">
                Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Perpustakaan,<br><br><br><br><br>
                <strong><u>{{ $librarianName }}</u></strong><br>
                NIP. {{ $librarianNip }}
            </td>
        </tr>
    </table>
</body>
</html>

