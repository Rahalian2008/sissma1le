<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Presensi Kelas {{ $rombel }} - {{ $periodLabel ?? $monthLabel }} (1 Lembar Pas)</title>
    @php
        $studentCount = count($students);
        $densityClass = 'density-normal';
        if ($studentCount > 36) {
            $densityClass = 'density-ultra-compact';
        } elseif ($studentCount > 26) {
            $densityClass = 'density-compact';
        }
    @endphp
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            line-height: 1.15;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            padding: 2px 4px;
        }

        /* Toolbar Pratinjau (Hanya Layar Monitor) */
        .print-btn-bar {
            background: #0f172a;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            margin-bottom: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.15s;
        }
        .print-btn-bar button:hover {
            background: #1d4ed8;
        }
        .print-btn-bar a {
            color: #93c5fd;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
            background: #1e293b;
        }
        .print-btn-bar a:hover {
            background: #334155;
            color: #bfdbfe;
        }

        /* Kop Surat Resmi Lembaga Standar */
        .header-kop-banner {
            text-align: center;
            margin-bottom: 8px !important;
            padding-bottom: 3px !important;
            border-bottom: 2.5px double #000 !important;
        }
        .header-kop-banner img {
            max-width: 100%;
            width: 100%;
            height: auto;
            max-height: 120px !important;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .header-kop {
            text-align: center;
            margin-bottom: 8px !important;
            padding-bottom: 3px !important;
            border-bottom: 2.5px double #000 !important;
        }
        .header-kop table {
            width: 100% !important;
            border: none !important;
            border-collapse: collapse !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .header-kop table td {
            border: none !important;
            padding: 0 6px !important;
            vertical-align: middle !important;
        }
        .header-kop img {
            max-height: 65px !important;
            width: auto !important;
        }
        .header-kop h2 {
            font-size: 13pt !important;
            font-weight: bold !important;
            margin: 2px 0 !important;
            line-height: 1.15 !important;
        }
        .header-kop p {
            margin: 1px 0 !important;
            font-size: 8.5pt !important;
            line-height: 1.2 !important;
        }

        /* Huruf Kapital untuk Seluruh Nama */
        table.attendance-table td:nth-child(4) {
            text-transform: uppercase !important;
            font-weight: 600;
        }
        .signature-table strong u {
            text-transform: uppercase !important;
        }

        /* Judul Dokumen */
        .title-doc {
            text-align: center;
            margin-top: 1px;
            margin-bottom: 2px;
        }
        .title-doc h3 {
            font-size: 9.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.15;
        }
        .title-doc p {
            font-size: 7.2pt;
            margin: 1px 0 0 0;
            color: #111;
            line-height: 1.1;
        }

        /* Tabel Presensi Utama */
        table.attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        table.attendance-table th, 
        table.attendance-table td {
            border: 1px solid #000;
            vertical-align: middle;
        }
        table.attendance-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        /* Tingkat Kepadatan Ukuran Font & Baris (Adaptive Density) */
        /* 1. Normal Density (<= 26 Siswa) */
        .density-normal table.attendance-table th {
            padding: 2px 2px;
            font-size: 7pt;
            line-height: 1.1;
        }
        .density-normal table.attendance-table td {
            padding: 2px 3px;
            font-size: 7.5pt;
            line-height: 1.15;
        }
        .density-normal .sig-space {
            height: 28px;
        }

        /* 2. Compact Density (27 - 36 Siswa) */
        .density-compact table.attendance-table th {
            padding: 1.2px 1px;
            font-size: 6.6pt;
            line-height: 1.05;
        }
        .density-compact table.attendance-table td {
            padding: 1.2px 2px;
            font-size: 7pt;
            line-height: 1.1;
        }
        .density-compact .sig-space {
            height: 22px;
        }

        /* 3. Ultra Compact Density (> 36 Siswa, misal 41-45 Siswa) */
        .density-ultra-compact table.attendance-table th {
            padding: 0.8px 0.5px;
            font-size: 6.2pt;
            line-height: 1;
        }
        .density-ultra-compact table.attendance-table td {
            padding: 0.6px 2px;
            font-size: 6.5pt;
            line-height: 1.05;
        }
        .density-ultra-compact .sig-space {
            height: 16px;
        }
        .density-ultra-compact .signature-table {
            font-size: 7pt;
            margin-top: 2px;
        }
        .density-ultra-compact .legend-note {
            font-size: 6pt;
            margin-top: 1px;
            margin-bottom: 1px;
        }

        /* Catatan Kaki */
        .legend-note {
            margin-top: 2px;
            margin-bottom: 2px;
            font-size: 6.5pt;
            color: #334155;
            line-height: 1.1;
        }

        /* Tabel Tanda Tangan */
        .signature-table {
            width: 100%;
            margin-top: 3px;
            border-collapse: collapse;
            font-size: 7.5pt;
            line-height: 1.15;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .signature-table td {
            border: none !important;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }

        /* Aturan Khusus Cetak Kertas (Print Engine Strict 1 Lembar) */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0 !important;
            }
            html, body {
                width: 100%;
                height: 100%;
                overflow: hidden !important;
            }
            .page-container {
                width: 100%;
                height: 100%;
                max-height: 100vh;
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                break-after: avoid !important;
                break-inside: avoid !important;
            }
            table.attendance-table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            table.attendance-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signature-table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Presensi Kelas ' . $rombel . ' (' . ($periodLabel ?? $monthLabel) . ')'" orientation="landscape" defaultPaper="f4" margin="4mm 6mm 3mm 6mm" />

    <div class="no-print" style="padding: 6px 12px; background: #f1f5f9; border-bottom: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
        <span style="color: #475569;">Presensi Kelas <strong>{{ $rombel }}</strong> &bull; Periode: <strong>{{ $periodLabel ?? $monthLabel }}</strong> ({{ $studentCount }} Siswa)</span>
        <div>
            @if($mode === 'blank')
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'recap']) }}" style="display: inline-block; padding: 4px 10px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #1e293b; font-weight: 600;">
                    📊 Beralih ke Format Rekapitulasi
                </a>
            @else
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'blank']) }}" style="display: inline-block; padding: 4px 10px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #1e293b; font-weight: 600;">
                    📝 Beralih ke Format Blank (Kosong)
                </a>
            @endif
        </div>
    </div>

    <div class="page-container printable-sheet landscape-sheet {{ $densityClass }}">
        <!-- Kop Surat Resmi Universal Lembaga -->
        <x-print-kop :school="$school" />

        <!-- Judul & Meta Informasi -->
        <div class="title-doc">
            <h3>{{ $mode === 'blank' ? 'DAFTAR HADIR / PRESENSI PESERTA DIDIK' : 'REKAPITULASI PRESENSI KEHADIRAN PESERTA DIDIK' }}</h3>
            <p>Kelas / Rombel: <strong>{{ $rombel }}</strong> &bull; Periode: <strong>{{ $periodLabel ?? $monthLabel }}</strong> &bull; Semester: <strong>Ganjil/Genap</strong> &bull; Tahun Ajaran: <strong>{{ date('Y') }}/{{ date('Y')+1 }}</strong></p>
        </div>

        @if($mode === 'blank')
            <!-- TABEL BLANK PRESENSI KUSTOM (SIAP TULIS TANGAN) -->
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 22px;">No</th>
                        <th rowspan="2" style="width: 48px;">NIS</th>
                        <th rowspan="2" style="width: 58px;">NISN</th>
                        <th rowspan="2" style="min-width: 140px; text-align: left; padding-left: 5px;">Nama Lengkap Siswa</th>
                        <th rowspan="2" style="width: 20px;">L/P</th>
                        <th colspan="{{ count($days) }}">Hari &amp; Tanggal ({{ $periodLabel ?? $monthLabel }})</th>
                        <th colspan="4" style="width: 72px;">Jumlah</th>
                    </tr>
                    <tr>
                        @foreach ($days as $d)
                            <th style="width: 17px; padding: 0.5px 0; {{ $d['isWeekend'] ? 'background:#cbd5e1; color:#0f172a;' : '' }}">
                                <div>{{ $d['day'] }}</div>
                                <div style="font-size: 5.5pt; font-weight: normal; line-height: 1;">{{ $d['shortName'] }}</div>
                            </th>
                        @endforeach
                        <th style="width: 18px;">S</th>
                        <th style="width: 18px;">I</th>
                        <th style="width: 18px;">A</th>
                        <th style="width: 18px;">Jml</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $idx => $student)
                        <tr>
                            <td style="text-align: center; font-family: monospace;">{{ $idx + 1 }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $student->nis }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $student->nisn ?? '-' }}</td>
                            <td style="white-space: nowrap; font-weight: 600; padding-left: 5px; overflow: hidden; text-overflow: ellipsis; max-width: 180px; text-transform: uppercase;">{{ strtoupper($student->name) }}</td>
                            <td style="text-align: center;">{{ $student->gender }}</td>
                            @foreach ($days as $d)
                                <td style="text-align: center; {{ $d['isWeekend'] ? 'background:#f1f5f9;' : '' }}"></td>
                            @endforeach
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- TABEL REKAPITULASI PRESENSI KUSTOM (TERISI DATA SISTEM) -->
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 26px;">No</th>
                        <th rowspan="2" style="width: 65px;">NIS</th>
                        <th rowspan="2" style="width: 75px;">NISN</th>
                        <th rowspan="2" style="text-align: left; padding-left: 6px;">Nama Lengkap Siswa</th>
                        <th rowspan="2" style="width: 26px;">L/P</th>
                        <th colspan="4" style="width: 140px;">Rekapitulasi Kehadiran (Efektif: {{ $daysInMonth }} Hari)</th>
                        <th rowspan="2" style="width: 55px;">% Hadir</th>
                        <th rowspan="2" style="width: 95px;">Keterangan</th>
                    </tr>
                    <tr>
                        <th style="width: 35px;">H</th>
                        <th style="width: 35px;">S</th>
                        <th style="width: 35px;">I</th>
                        <th style="width: 35px;">A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $idx => $student)
                        @php
                            $records = $attendances->get($student->id, collect());
                            $h = $records->where('status', 'Hadir')->count();
                            $s = $records->where('status', 'Sakit')->count();
                            $i = $records->where('status', 'Izin')->count();
                            $a = $records->where('status', 'Alpa')->count();
                            $d = $records->where('status', 'Dispensasi')->count();
                            $effectiveHadir = $h + $d;
                            $pct = $daysInMonth > 0 ? round(($effectiveHadir / $daysInMonth) * 100, 1) : 100;
                            if ($records->isEmpty()) {
                                $h = $daysInMonth;
                                $pct = 100;
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center; font-family: monospace;">{{ $idx + 1 }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $student->nis }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $student->nisn ?? '-' }}</td>
                            <td style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; text-transform: uppercase;"><strong>{{ strtoupper($student->name) }}</strong></td>
                            <td style="text-align: center;">{{ $student->gender }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $h }}</td>
                            <td style="text-align: center;">{{ $s > 0 ? $s : '-' }}</td>
                            <td style="text-align: center;">{{ $i > 0 ? $i : '-' }}</td>
                            <td style="text-align: center; {{ $a > 0 ? 'font-weight:bold; color:#b91c1c;' : '' }}">{{ $a > 0 ? $a : '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $pct }}%</td>
                            <td style="text-align: center; font-size: 6.5pt; color: #475569;">
                                {{ $pct >= 85 ? 'Sangat Baik' : ($pct >= 75 ? 'Cukup' : 'Perlu Pembinaan') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Petunjuk Keterangan Singkat -->
        <div class="legend-note">
            <em>Petunjuk: H = Hadir &bull; S = Sakit &bull; I = Izin &bull; A = Alpa &bull; Kolom abu-abu adalah hari Sabtu/Minggu (libur). Dokumen ini terformat presisi 1 lembar A4/F4 landscape.</em>
        </div>

        <!-- Kolom Tanda Tangan Resmi (2 Kolom: Kepala Sekolah & Wali Kelas) -->
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    Kepala Sekolah
                    <div class="sig-space"></div>
                    <strong><u style="text-transform: uppercase;">{{ strtoupper($school->headmaster_name) }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip }}
                </td>
                <td style="width: 50%;">
                    {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas {{ $rombel }},
                    <div class="sig-space"></div>
                    <strong><u style="text-transform: uppercase;">{{ strtoupper($waliKelas?->formatted_name ?? 'Wali Kelas ' . $rombel) }}</u></strong><br>
                    NIP. {{ $waliKelas?->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
