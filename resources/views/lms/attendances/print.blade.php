<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Pertemuan - {{ $attendance->title }} - {{ $courseClass->subject->name }} ({{ $courseClass->schoolClass->name }})</title>
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
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            border: 1px solid #333;
            padding: 6px 12px;
            font-size: 9.5pt;
            background: #fafafa;
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
            Dokumen Resmi Presensi Pertemuan Kelas — SMAN 1 Lengkong
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
            <h2>BERITA ACARA & DAFTAR PRESENSI PERTEMUAN</h2>
            <p>Tahun Ajaran {{ $courseClass->academicYear->name ?? '2026/2027' }}</p>
        </div>

        <!-- Metadata Mata Pelajaran & Sesi -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%; font-weight: bold;">Mata Pelajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $courseClass->subject->name }} ({{ $courseClass->subject->code ?? '-' }})</td>
                <td style="width: 18%; font-weight: bold;">Sesi / Pertemuan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $attendance->title }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kelas & Rombel</td>
                <td>:</td>
                <td>{{ $courseClass->schoolClass->name }}</td>
                <td style="font-weight: bold;">Hari / Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($attendance->meeting_date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Guru Pengampu</td>
                <td>:</td>
                <td>{{ $courseClass->teacher->name }}</td>
                <td style="font-weight: bold;">Waktu / Jam</td>
                <td>:</td>
                <td>{{ $attendance->start_time }} - {{ $attendance->end_time }} WIB</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Metode Presensi</td>
                <td>:</td>
                <td>{{ strtoupper($attendance->attendance_method) }}</td>
                <td style="font-weight: bold;">Target KKTP</td>
                <td>:</td>
                <td>{{ $courseClass->getEffectiveKktp() }}</td>
            </tr>
        </table>

        <!-- Ringkasan Statistik Kehadiran -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9pt; border: 1px solid #111;">
            <tr style="background: #f8fafc; font-weight: bold; text-align: center;">
                <td style="padding: 5px; border: 1px solid #111;">Total Siswa: {{ $summary['total'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #059669;">Hadir: {{ $summary['hadir'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #2563eb;">Izin: {{ $summary['izin'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #d97706;">Sakit: {{ $summary['sakit'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #dc2626;">Alpa: {{ $summary['alpa'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; font-weight: 900;">Persentase: {{ $summary['persentase'] }}%</td>
            </tr>
        </table>

        <!-- Tabel Presensi Siswa -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 15%;">NIS / NISN</th>
                    <th style="width: 32%;">Nama Siswa</th>
                    <th style="width: 8%;">L/P</th>
                    <th style="width: 13%;">Status</th>
                    <th style="width: 12%;">Waktu / Metode</th>
                    <th style="width: 16%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $idx => $st)
                    @php
                        $log = $logsMap[$st->id] ?? null;
                        $status = $log ? strtolower($log->status) : 'alpa';
                    @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $st->nisn ?? $st->nis ?? '-' }}</td>
                        <td><strong>{{ $st->name }}</strong></td>
                        <td class="center">{{ strtoupper(substr($st->gender ?? 'L', 0, 1)) }}</td>
                        <td class="center" style="font-weight: bold; text-transform: uppercase;">
                            @if($status === 'hadir')
                                HADIR
                            @elseif($status === 'izin')
                                IZIN
                            @elseif($status === 'sakit')
                                SAKIT
                            @else
                                ALPA
                            @endif
                        </td>
                        <td class="center" style="font-size: 8.5pt;">
                            @if($log)
                                {{ $log->recorded_at ? $log->recorded_at->format('H:i') : '-' }} ({{ strtoupper($log->verified_method ?? 'MANUAL') }})
                            @else
                                -
                            @endif
                        </td>
                        <td style="font-size: 8pt;">{{ $log?->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                    {{ \App\Models\SchoolSetting::get('school_city', 'Sukabumi') }}, {{ \Carbon\Carbon::parse($attendance->meeting_date)->translatedFormat('d F Y') }}<br>
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
