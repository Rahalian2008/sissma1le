<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Presensi Manual Siswa - {{ $schoolClass->name }} ({{ $periodLabel }})</title>
    @php
        $isF4 = strtolower(request('paper', $paper ?? 'f4')) === 'f4';
        $actualKopPath = !empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop')));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $totalDays = count($dates);
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '330mm 215mm' : 'A4' }} landscape;
            margin: {{ $isF4 ? '8mm 10mm 10mm 10mm' : '10mm 10mm 10mm 10mm' }};
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #0f172a;
            font-size: {{ $totalDays > 25 ? '8.5pt' : '9pt' }};
            line-height: 1.2;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* Controls Bar (Layar Saja, Tersembunyi saat Cetak) */
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        }
        .no-print-bar .controls-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .no-print-bar .controls-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .paper-toggle {
            display: inline-flex;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 2px;
        }
        .paper-toggle a {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 6px;
        }
        .paper-toggle a.active {
            background: #10b981;
            color: #ffffff;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-print {
            background: #10b981;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #059669;
        }
        .btn-back {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Container Dokumen */
        .page-sheet {
            width: 100%;
            padding: 10px 14px;
        }

        /* KOP Resmi */
        .kop-wrapper {
            margin-bottom: 8px;
        }
        .kop-img {
            width: 100%;
            max-height: 85px;
            object-fit: contain;
            display: block;
        }
        .kop-text-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            text-align: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .kop-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            margin: 1px 0;
            font-size: 13.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 0;
            font-size: 8pt;
            font-family: Arial, sans-serif;
            color: #334155;
        }

        /* Judul Dokumen */
        .doc-title-wrapper {
            text-align: center;
            margin: 6px 0 10px 0;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            text-decoration: underline;
        }
        .doc-subtitle {
            font-size: 8.5pt;
            font-weight: 600;
            margin: 2px 0 0 0;
            color: #1e293b;
        }

        /* Metadata Rombel */
        .meta-table {
            width: 100%;
            margin-bottom: 6px;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
        }
        .meta-table td {
            padding: 1px 4px;
            vertical-align: top;
        }
        .meta-label {
            width: 100px;
            font-weight: bold;
            color: #334155;
        }

        /* Tabel Presensi Grid SIKATAS */
        .attendance-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $totalDays > 25 ? '7.5pt' : '8pt' }};
            font-family: Arial, sans-serif;
        }
        .attendance-grid th, .attendance-grid td {
            border: 1px solid #1e293b;
            padding: 2.5px 2px;
            text-align: center;
            vertical-align: middle;
        }
        .attendance-grid th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.1;
        }
        .attendance-grid th .day-label {
            font-size: 6.5pt;
            font-weight: normal;
            display: block;
            color: #475569;
        }
        .attendance-grid th.holiday-th {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .attendance-grid td.holiday-cell {
            background-color: #f1f5f9;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 3px, rgba(148, 163, 184, 0.25) 3px, rgba(148, 163, 184, 0.25) 6px);
        }
        .attendance-grid td.text-left {
            text-align: left;
            padding-left: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 170px;
        }
        .attendance-grid tr:nth-child(even) td:not(.holiday-cell) {
            background-color: #fafafa;
        }

        /* Titi Mangsa & Kolom TTD */
        .signature-section {
            width: 100%;
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 32%;
            text-align: center;
        }
        .sig-space {
            height: 48px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Petunjuk Simbol */
        .legend-box {
            margin-top: 8px;
            font-size: 7.5pt;
            font-family: Arial, sans-serif;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #fff;
                color: #000;
            }
            .page-sheet {
                padding: 0 !important;
            }
            .attendance-grid th {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .attendance-grid th.holiday-th {
                background-color: #fecaca !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .attendance-grid td.holiday-cell {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (Hanya tampil di browser, tidak ikut tercetak) -->
    <div class="no-print-bar">
        <div class="controls-left">
            <span style="font-weight: 800; color: #34d399;">📋 SIKATAS PRESENSI</span>
            <span style="color: #64748b;">|</span>
            <span>Rombel: <strong>{{ $schoolClass->name }}</strong></span>
            <span>({{ $periodLabel }})</span>
            <div class="paper-toggle">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="{{ $isF4 ? 'active' : '' }}">F4 Folio</a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="{{ !$isF4 ? 'active' : '' }}">A4</a>
            </div>
        </div>

        <div class="controls-right">
            <a href="{{ route('tu.kesiswaan.absen_manual.index') }}" class="btn-action btn-back">
                ← Kembali ke Filter
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Lembar Presensi (Print)
            </button>
        </div>
    </div>

    <div class="page-sheet">

        <!-- KOP Surat Kedinasan -->
        @if($showKop)
            @if($actualKopUrl)
                <div class="kop-wrapper">
                    <img src="{{ $actualKopUrl }}" alt="KOP Surat SMAN 1 Lengkong" class="kop-img">
                </div>
            @else
                <div class="kop-text-container">
                    <img src="{{ !empty($school['logo']) ? asset('storage/'.$school['logo']) : asset('images/logo_jabar.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" alt="Logo" class="kop-logo">
                    <div class="kop-text">
                        <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                        <h4>DINAS PENDIDIKAN - CABANG DINAS PENDIDIKAN WILAYAH V</h4>
                        <h2>{{ $school['name'] ?? 'SMA NEGERI 1 LENGKONG' }}</h2>
                        <p>{{ $school['address'] ?? 'Jl. Raya Lengkong No. 01 Kec. Lengkong Kab. Sukabumi 43174' }} | NPSN: {{ $school['npsn'] ?? '20254044' }} | Akreditasi A</p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Judul Dokumen -->
        <div class="doc-title-wrapper">
            <h1 class="doc-title">DAFTAR HADIR MANUAL SISWA (LEMBAR PRESENSI KELAS)</h1>
            <p class="doc-subtitle">Sistem Informasi Kehadiran & Tata Tertib Siswa (SIKATAS) — Semester {{ $semester }} Tahun Pelajaran {{ $academicYear }}</p>
        </div>

        <!-- Metadata Rombel -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">Kelas / Rombel</td>
                <td>: <strong>{{ $schoolClass->name }}</strong> (Tingkat {{ $schoolClass->grade ?? '-' }})</td>
                <td class="meta-label" style="text-align: right;">Periode Presensi :</td>
                <td style="font-weight: bold; width: 220px;">{{ $periodLabel }}</td>
            </tr>
            <tr>
                <td class="meta-label">Wali Kelas</td>
                <td>: {{ $homeroomTeacher?->name ?? 'Belum Ditentukan' }} (NIP. {{ $homeroomTeacher?->nip ?: '-' }})</td>
                <td class="meta-label" style="text-align: right;">Jumlah Siswa :</td>
                <td style="font-weight: bold;">{{ $students->count() }} Orang (L: {{ $students->where('gender', 'L')->count() }}, P: {{ $students->where('gender', 'P')->count() }})</td>
            </tr>
        </table>

        <!-- Tabel Grid Presensi Siswa -->
        <table class="attendance-grid">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 24px;">NO</th>
                    <th rowspan="2" style="width: 75px;">NISN / NIS</th>
                    <th rowspan="2" style="min-width: 140px; text-align: left; padding-left: 6px;">NAMA LENGKAP SISWA</th>
                    <th rowspan="2" style="width: 24px;">L/P</th>
                    <th colspan="{{ $totalDays }}">TANGGAL KEHADIRAN / PARAF SISWA</th>
                    <th colspan="4" style="width: 80px;">REKAP</th>
                    <th rowspan="2" style="width: 45px;">KET</th>
                </tr>
                <tr>
                    @foreach($dates as $d)
                        <th class="{{ $d['is_holiday'] ? 'holiday-th' : '' }}" style="width: {{ $totalDays > 25 ? '18px' : '22px' }};" title="{{ $d['holiday_name'] ?? $d['date_string'] }}">
                            {{ $d['day_number'] }}
                            <span class="day-label">{{ $d['day_name_short'] }}</span>
                        </th>
                    @endforeach
                    <th style="width: 20px;">S</th>
                    <th style="width: 20px;">I</th>
                    <th style="width: 20px;">A</th>
                    <th style="width: 20px;">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $idx => $student)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="font-family: monospace; font-size: 7pt;">{{ $student->nisn ?: ($student->nis ?: '-') }}</td>
                        <td class="text-left font-bold">{{ $student->name }}</td>
                        <td>{{ $student->gender ?: 'L' }}</td>
                        
                        @foreach($dates as $d)
                            <td class="{{ $d['is_holiday'] ? 'holiday-cell' : '' }}">
                                @if($d['is_holiday'])
                                    <!-- Kolom Libur / Minggu Diarsir -->
                                @endif
                            </td>
                        @endforeach

                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 5 + $totalDays + 4 }}" style="padding: 16px; text-align: center; color: #64748b;">
                            Belum ada data siswa aktif yang terdaftar di kelas {{ $schoolClass->name }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Legenda Simbol -->
        <div class="legend-box">
            <span><strong>Keterangan Simbol:</strong> [ . ] = Hadir Tepat Waktu | [ S ] = Sakit | [ I ] = Izin | [ A ] = Alpa / Tanpa Keterangan</span>
            <span>• Kolom arsiran abu-abu = Hari Minggu / Libur Resmi Sekolah</span>
        </div>

        <!-- Bagian Pengesahan & Tanda Tangan -->
        <div class="signature-section">
            <div class="sig-box">
                <p style="margin: 0;">Mengetahui,</p>
                <p style="margin: 2px 0 0 0; font-weight: bold;">
                    {{ $signerRole === 'kepala_sekolah' ? 'Kepala SMAN 1 Lengkong,' : 'Wakasek Bidang Kesiswaan,' }}
                </p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $signerRole === 'kepala_sekolah' ? $headmasterName : $wakasekName }}</p>
                <p style="margin: 2px 0 0 0; font-size: 8pt;">NIP. {{ $signerRole === 'kepala_sekolah' ? $headmasterNip : $wakasekNip }}</p>
            </div>

            <div class="sig-box" style="visibility: hidden;">
                <!-- Ruang Kosong Tengah -->
            </div>

            <div class="sig-box">
                <p style="margin: 0;">Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 2px 0 0 0; font-weight: bold;">Wali Kelas {{ $schoolClass->name }},</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $homeroomTeacher?->name ?? '................................................' }}</p>
                <p style="margin: 2px 0 0 0; font-size: 8pt;">NIP. {{ $homeroomTeacher?->nip ?: '................................................' }}</p>
            </div>
        </div>

    </div>

</body>
</html>

