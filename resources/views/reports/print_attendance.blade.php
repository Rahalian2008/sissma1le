<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Presensi - {{ $selectedClass?->name ?? 'Semua Kelas' }} ({{ $startDate }} s.d. {{ $endDate }}) [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.2cm 1.5cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10.5pt' : '10pt' }};
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        /* Controls Bar (Hidden in Print) */
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
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
        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #0f172a;
            border-radius: 6px;
            padding: 2px;
            border: 1px solid #334155;
        }
        .paper-toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .paper-toggle-btn.active {
            background: #6366f1;
            color: #ffffff;
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
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
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 10px 20px;
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

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210×297mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215×330mm)
                </a>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('reports.print.attendance') }}" method="GET">
                <input type="hidden" name="paper" value="{{ request('paper', 'a4') }}">
                <label>Mulai:</label>
                <input type="date" name="start_date" value="{{ $startDate }}">
                
                <label>Selesai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}">

                @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                    @php
                        $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
                    @endphp
                    <label>Pilihan Rombel Kelas:</label>
                    @if($isWaliKelasUser)
                        <select name="class_id">
                            @forelse($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                    Kelas {{ $c->name }}
                                </option>
                            @empty
                                <option value="">Belum ada kelas binaan</option>
                            @endforelse
                        </select>
                    @else
                        <select name="class_id">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                    Kelas {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                @endif

                <button type="submit" class="btn-filter">Terapkan Filter</button>
                <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak ({{ strtoupper(request('paper', 'a4')) }})</button>
            </form>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
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

        <!-- Tanda Tangan Pengesahan (Kepala Sekolah & Wakasek Bid. Kesiswaan) -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 20px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName ?? \App\Models\Teacher::getHeadmaster()['name'] }}</span><br>
                    NIP. {{ $headmasterNip ?? \App\Models\Teacher::getHeadmaster()['nip'] }}
                </td>
                <td style="width: 50%; text-align: right; padding-right: 20px;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Wakasek Bid. Kesiswaan</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $kesiswaanName ?? \App\Models\Teacher::getWakasekKesiswaan()['name'] }}</span><br>
                    NIP. {{ $kesiswaanNip ?? \App\Models\Teacher::getWakasekKesiswaan()['nip'] }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
