<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Tamu Pengunjung Perpustakaan - {{ $schoolName ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.35;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Kop Surat Resmi Dinas */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }

        .kop-logo {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            max-width: 75px;
            max-height: 85px;
            object-contain: contain;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }

        .kop-provinsi {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-cabang {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-sekolah {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 8.5pt;
            margin: 0;
            line-height: 1.25;
        }

        .title-box {
            text-align: center;
            margin: 15px 0 12px 0;
        }

        .title-box h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 4px 0;
        }

        .title-box p {
            font-size: 10pt;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }

        .meta-table td {
            padding: 2px 4px;
        }

        /* Tabel Data Pengunjung */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 18px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
        }

        .data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; font-size: 9pt; }

        /* Tanda Tangan: Kepala Sekolah WAJIB di sebelah KIRI */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 10.5pt;
        }

        .signatures-table td {
            vertical-align: top;
            width: 50%;
        }

        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 20px;
            text-align: right;
            margin-bottom: 15px;
        }

        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 6px 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            font-size: 10pt;
        }

        @media print {
            .print-btn-bar { display: none !important; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button onclick="window.print()">🖨️ Cetak / Simpan PDF (A4)</button>
    </div>

    <div class="container">
        
        <!-- KOP SURAT DINAS -->
        @if(!empty($actualKopUrl))
            <div style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="Kop Surat" style="width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if(!empty($schoolLogo))
                            <img src="{{ $schoolLogo }}" alt="Logo">
                        @endif
                    </td>
                    <td class="kop-text">
                        <div class="kop-provinsi">PEMERINTAH DAERAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</div>
                        <div class="kop-cabang">DINAS PENDIDIKAN &bull; {{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</div>
                        <div class="kop-sekolah">{{ $schoolName ?? 'SMA NEGERI 1 LENGKONG' }}</div>
                        <div class="kop-alamat">
                            {{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }}<br>
                            Telp: {{ $schoolPhone ?? '(0266) 123456' }} &bull; Website: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }} &bull; Pos-el: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }}
                        </div>
                    </td>
                    <td class="kop-logo">
                        <!-- Spacer / Logo Kanan jika ada -->
                    </td>
                </tr>
            </table>
        @endif

        <!-- Judul Laporan -->
        <div class="title-box">
            <h2>LAPORAN BUKU TAMU PENGUNJUNG PERPUSTAKAAN</h2>
            <p><strong>{{ $periodLabel }}</strong></p>
        </div>

        <!-- Metadata Rekap Ringkas -->
        <table class="meta-table">
            <tr>
                <td style="width: 15%;"><strong>Unit Layanan</strong></td>
                <td style="width: 35%;">: Perpustakaan {{ $schoolName ?? 'SMAN 1 Lengkong' }}</td>
                <td style="width: 20%;"><strong>Total Pengunjung</strong></td>
                <td style="width: 30%;">: <strong>{{ number_format($totalVisitors) }} Orang</strong></td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>: {{ $periodLabel }}</td>
                <td><strong>Rincian Kategori</strong></td>
                <td>: Siswa: {{ $studentVisitors }} | Guru/Tendik: {{ $teacherVisitors }} | Umum: {{ $generalVisitors }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
                <td><strong>Status Verifikasi</strong></td>
                <td>: Dokumen Resmi Terverifikasi Sistem</td>
            </tr>
        </table>

        <!-- Tabel Rincian Kunjungan -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 14%;">Waktu Kunjungan</th>
                    <th style="width: 22%;">Nama Pengunjung</th>
                    <th style="width: 14%;">Identitas (NISN/NIP)</th>
                    <th style="width: 14%;">Kategori / Rombel</th>
                    <th style="width: 16%;">Keperluan</th>
                    <th style="width: 16%;">Keterangan / Rating</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitors as $idx => $v)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-mono">
                            {{ $v->visited_at ? $v->visited_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <strong>{{ $v->name }}</strong>
                        </td>
                        <td class="text-center font-mono">{{ $v->identity_number ?: '-' }}</td>
                        <td class="text-center">
                            {{ strtoupper($v->visitor_type) }}<br>
                            <span style="font-size: 8pt; color: #475569;">{{ $v->class_or_institution ?: '-' }}</span>
                        </td>
                        <td>{{ $v->purpose }}</td>
                        <td style="font-size: 8.5pt;">{{ $v->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px;">
                            <em>Tidak ada data pengunjung perpustakaan pada periode ini.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- TANDA TANGAN RESMI: KEPALA SEKOLAH DI SEBELAH KIRI (WAJIB) -->
        <table class="signatures-table">
            <tr>
                <!-- KIRI: KEPALA SEKOLAH -->
                <td style="text-align: left; padding-left: 20px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br>
                    {{ $schoolName ?? 'SMAN 1 Lengkong' }}
                    <br><br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</span><br>
                    NIP. {{ $headmasterNip ?? '19680512 199303 1 004' }}
                </td>

                <!-- KANAN: KEPALA / PENGELOLA PERPUSTAKAAN -->
                <td style="text-align: right; padding-right: 20px;">
                    Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Pengelola / Kepala Perpustakaan</strong><br>
                    {{ $schoolName ?? 'SMAN 1 Lengkong' }}
                    <br><br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $librarianName ?? 'Anne Listiyani, S.Si., M.Pd.' }}</span><br>
                    NIP. {{ $librarianNip ?? '19800417 201001 2 014' }}
                </td>
            </tr>
        </table>

    </div>

</body>
</html>

