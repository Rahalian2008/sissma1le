<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Presensi Semester - {{ $courseClass->subject->name }} ({{ $courseClass->schoolClass->name }})</title>
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
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .print-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px 0;
        }
        .kop-surat-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 6px;
        }
        .kop-surat-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-td {
            width: 85px;
            text-align: center;
        }
        .logo-img {
            width: 75px;
            height: auto;
            max-height: 85px;
            object-fit: contain;
        }
        .kop-text-td {
            text-align: center;
            padding: 0 10px;
        }
        .kop-prov {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-dinas {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }
        .kop-school {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }
        .kop-address {
            font-size: 8.5pt;
            font-style: italic;
            font-family: Arial, Helvetica, sans-serif;
            margin: 2px 0 0 0;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0 12px 0;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .doc-title p {
            font-size: 10pt;
            margin: 3px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2.5px 4px;
            border: none;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #111;
            padding: 4px 6px;
        }
        .data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .center { text-align: center; }
        .signatures {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td {
            vertical-align: top;
            border: none;
            font-size: 10pt;
            line-height: 1.4;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { font-size: 10pt; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar -->
    <div class="no-print-bar">
        <div style="font-weight: bold;">
            Dokumen Rekapitulasi Presensi Kumulatif Kelas — SMAN 1 Lengkong
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ ! $isF4 ? 'active' : 'inactive' }}">A4</a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">F4</a>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak / Unduh PDF
            </button>
        </div>
    </div>

    <div class="print-container">
        <!-- Kop Surat Resmi -->
        @if($actualKopUrl)
            <div style="text-align: center; margin-bottom: 10px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="Kop Surat" style="width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <table class="kop-surat-table">
                <tr>
                    <td class="logo-td">
                        <img src="{{ asset('images/logo_jabar.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" class="logo-img" alt="Logo">
                    </td>
                    <td class="kop-text-td">
                        <div class="kop-prov">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                        <div class="kop-dinas">DINAS PENDIDIKAN</div>
                        <div class="kop-prov">{{ \App\Models\SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</div>
                        <div class="kop-school">{{ \App\Models\SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG') }}</div>
                        <div class="kop-address">{{ \App\Models\SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kec. Lengkong, Kab. Sukabumi 43174') }} | Email: {{ \App\Models\SchoolSetting::get('school_email', 'sman1lengkong@gmail.com') }}</div>
                    </td>
                    <td class="logo-td">
                        <img src="{{ asset('images/logo_sma1le.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" class="logo-img" alt="Logo SMA1LE">
                    </td>
                </tr>
            </table>
        @endif

        <!-- Judul Dokumen -->
        <div class="doc-title">
            <h2>REKAPITULASI PRESENSI PEMBELAJARAN SISWA</h2>
            <p>Tahun Ajaran {{ $courseClass->academicYear->name ?? '2026/2027' }}</p>
        </div>

        <!-- Metadata Mata Pelajaran -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%; font-weight: bold;">Mata Pelajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $courseClass->subject->name }} ({{ $courseClass->subject->code ?? '-' }})</td>
                <td style="width: 18%; font-weight: bold;">Total Sesi Terlaksana</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $grandTotals['total_meetings'] }} Pertemuan</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kelas & Rombel</td>
                <td>:</td>
                <td>{{ $courseClass->schoolClass->name }}</td>
                <td style="font-weight: bold;">Target KKTP</td>
                <td>:</td>
                <td>{{ $courseClass->getEffectiveKktp() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Guru Pengampu</td>
                <td>:</td>
                <td>{{ $courseClass->teacher->name }}</td>
                <td style="font-weight: bold;">Jumlah Siswa</td>
                <td>:</td>
                <td>{{ $grandTotals['total_students'] }} Siswa Terdaftar</td>
            </tr>
        </table>

        <!-- Tabel Rekapitulasi Presensi Kumulatif Siswa -->
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 4%;">No</th>
                    <th rowspan="2" style="width: 14%;">NIS / NISN</th>
                    <th rowspan="2" style="width: 32%;">Nama Siswa</th>
                    <th rowspan="2" style="width: 6%;">L/P</th>
                    <th colspan="4" style="width: 28%;">Akumulasi Kehadiran</th>
                    <th rowspan="2" style="width: 16%;">Persentase Kehadiran (%)</th>
                </tr>
                <tr>
                    <th style="width: 7%; background: #ecfdf5; color: #047857;">H</th>
                    <th style="width: 7%; background: #eff6ff; color: #1d4ed8;">I</th>
                    <th style="width: 7%; background: #fffbeb; color: #b45309;">S</th>
                    <th style="width: 7%; background: #fef2f2; color: #b91c1c;">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $idx => $st)
                    @php
                        $rcp = $recap[$st->id] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'percentage' => 0];
                    @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $st->nisn ?? $st->nis ?? '-' }}</td>
                        <td><strong>{{ $st->name }}</strong></td>
                        <td class="center">{{ strtoupper(substr($st->gender ?? 'L', 0, 1)) }}</td>
                        <td class="center" style="font-weight: bold; color: #047857;">{{ $rcp['hadir'] }}</td>
                        <td class="center" style="color: #1d4ed8;">{{ $rcp['izin'] }}</td>
                        <td class="center" style="color: #b45309;">{{ $rcp['sakit'] }}</td>
                        <td class="center" style="color: #b91c1c;">{{ $rcp['alpa'] }}</td>
                        <td class="center" style="font-weight: bold;">{{ $rcp['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="4" class="center">TOTAL KUMULATIF KELAS</td>
                    <td class="center" style="color: #047857;">{{ $grandTotals['hadir'] }}</td>
                    <td class="center" style="color: #1d4ed8;">{{ $grandTotals['izin'] }}</td>
                    <td class="center" style="color: #b45309;">{{ $grandTotals['sakit'] }}</td>
                    <td class="center" style="color: #b91c1c;">{{ $grandTotals['alpa'] }}</td>
                    <td class="center">
                        @php
                            $allEntries = $grandTotals['hadir'] + $grandTotals['izin'] + $grandTotals['sakit'] + $grandTotals['alpa'];
                            $overallPct = $allEntries > 0 ? round(($grandTotals['hadir'] / $allEntries) * 100, 1) : 0;
                        @endphp
                        {{ $overallPct }}%
                    </td>
                </tr>
            </tfoot>
        </table>

        <div style="font-size: 8.5pt; color: #444; margin-top: 6px;">
            <em>Keterangan: H = Hadir, I = Izin, S = Sakit, A = Alpa/Tanpa Keterangan. Persentase kehadiran dihitung berdasarkan rasio kehadiran (H) terhadap total sesi pertemuan yang diselenggarakan.</em>
        </div>

        <!-- POSISI TANDA TANGAN: KEPALA SEKOLAH SELALU DI SEBELAH KIRI -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 15px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ \App\Models\Teacher::getHeadmaster()['name'] }}</span><br>
                    NIP. {{ \App\Models\Teacher::getHeadmaster()['nip'] }}
                </td>
                <td style="width: 50%; text-align: right; padding-right: 15px;">
                    {{ \App\Models\SchoolSetting::get('school_city', 'Sukabumi') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Guru Mata Pelajaran</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $courseClass->teacher->name }}</span><br>
                    NIP. {{ $courseClass->teacher->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
