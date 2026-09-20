<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Presensi - {{ $selectedClass?->name ?? 'Semua Kelas' }} ({{ $startDate }} s.d. {{ $endDate }})</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: 10.5pt;
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        /* Controls Bar (Hidden in Print) */
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .no-print-bar form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .no-print-bar input, .no-print-bar select {
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #fff;
            font-size: 12px;
        }
        .no-print-bar button {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            transition: 0.2s;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
        }
        .btn-print:hover {
            background: #059669;
        }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-back:hover {
            background: #334155;
        }
        .btn-filter {
            background: #6366f1;
            color: #fff;
        }
        .btn-filter:hover {
            background: #4f46e5;
        }

        /* Printable Document Container */
        .doc-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15px 25px;
        }

        /* KOP Surat */
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
            position: relative;
        }
        .kop-header h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .kop-header h3 {
            margin: 2px 0;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.8px;
        }
        .kop-header h2 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .kop-header p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #222;
        }

        .doc-title {
            text-align: center;
            margin: 14px 0 12px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 10pt;
        }

        /* Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }
        table.data-table td.center {
            text-align: center;
        }
        table.data-table td.right {
            text-align: right;
        }
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #f8fafc;
        }

        /* Signatures */
        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signatures td {
            vertical-align: top;
            font-size: 10pt;
            padding: 0 10px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: none;
            }
            .doc-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (No Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('reports.index') }}" class="btn-back">⬅ Kembali</a>
            <span style="font-weight: bold; font-size: 14px;">CETAK LAPORAN PRESENSI SISWA</span>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('reports.print.attendance') }}" method="GET">
            <label>Mulai:</label>
            <input type="date" name="start_date" value="{{ $startDate }}">
            
            <label>Selesai:</label>
            <input type="date" name="end_date" value="{{ $endDate }}">

            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <label>Kelas:</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $selectedClass?->name ?? 'Kelas Binaan' }}" readonly style="background: #334155; color: #fff; border: 1px solid #475569; padding: 5px 10px; border-radius: 6px; cursor: not-allowed; font-weight: bold; width: 120px;">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            @endif

            <button type="submit" class="btn-filter">Terapkan Filter</button>
            <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
        </form>
    </div>

    <!-- Printable Paper Area -->
    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($kopImageUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $kopImageUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
        @else
            <div class="kop-header">
                <h4>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h4>
                <h3>DINAS PENDIDIKAN</h3>
                <h4>{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
                <h2>{{ strtoupper($schoolName) }}</h2>
                <p>NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</p>
                <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</p>
            </div>
        @endif

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h3>LAPORAN REKAPITULASI PRESENSI SISWA</h3>
            <p>
                <strong>Periode:</strong> {{ $periodTitle ?? (\Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' s.d. ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y')) }}
                &nbsp;|&nbsp;
                <strong>Kelas:</strong> {{ $selectedClass?->name ?? 'Semua Kelas (Seluruh Siswa)' }}
            </p>
        </div>

        <!-- Tabel Data Presensi -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 75px;">NIS</th>
                    <th>Nama Lengkap Siswa</th>
                    <th style="width: 65px;">Kelas</th>
                    <th style="width: 40px;" title="Hadir">H</th>
                    <th style="width: 40px;" title="Terlambat">T</th>
                    <th style="width: 40px;" title="Izin">I</th>
                    <th style="width: 40px;" title="Sakit">S</th>
                    <th style="width: 40px;" title="Alpa">A</th>
                    <th style="width: 55px;">Total</th>
                    <th style="width: 60px;">% Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentRecaps as $idx => $row)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $row['student']->nis }}</td>
                        <td>{{ $row['student']->name }}</td>
                        <td class="center">{{ $row['student']->schoolClass?->name ?? '-' }}</td>
                        <td class="center">{{ $row['hadir'] }}</td>
                        <td class="center">{{ $row['terlambat'] }}</td>
                        <td class="center">{{ $row['izin'] }}</td>
                        <td class="center">{{ $row['sakit'] }}</td>
                        <td class="center">{{ $row['alpa'] }}</td>
                        <td class="center font-bold">{{ $row['total'] }}</td>
                        <td class="center font-bold">{{ $row['percentage'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="center" style="padding: 15px;">Tidak ada data presensi pada rentang waktu dan kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="center">JUMLAH TOTAL KUMULATIF</td>
                    <td class="center">{{ $totals['hadir'] }}</td>
                    <td class="center">{{ $totals['terlambat'] }}</td>
                    <td class="center">{{ $totals['izin'] }}</td>
                    <td class="center">{{ $totals['sakit'] }}</td>
                    <td class="center">{{ $totals['alpa'] }}</td>
                    <td class="center">{{ $totals['hadir'] + $totals['terlambat'] + $totals['izin'] + $totals['sakit'] + $totals['alpa'] }}</td>
                    <td class="center">{{ $totals['avg_percentage'] }}%</td>
                </tr>
            </tfoot>
        </table>

        <!-- Keterangan & Catatan Singkat -->
        <div style="font-size: 8.5pt; color: #444; margin-top: 8px;">
            <em>Keterangan: H = Hadir Tepat Waktu, T = Terlambat, I = Izin Resmi, S = Sakit, A = Alpa/Tanpa Keterangan. Persentase dihitung dari akumulasi kehadiran (H+T) terhadap total catatan.</em>
        </div>

        <!-- Tanda Tangan Pengesahan -->
        <table class="signatures">
            <tr>
                <td style="width: 50%;">
                    <br>
                    Mengetahui,<br>
                    <strong>Kepala {{ $schoolName }}</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName }}</span><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 50%; text-align: right;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    @if($selectedClass && $selectedClass->homeroomTeacher)
                        Wali Kelas {{ $selectedClass->name }}
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ $selectedClass->homeroomTeacher->name }}</span><br>
                        NIP. {{ $selectedClass->homeroomTeacher->nip ?? '-' }}
                    @else
                        Petugas Bagian Kesiswaan
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ Auth::user()->name }}</span><br>
                        NIP/ID. {{ Auth::user()->username }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
