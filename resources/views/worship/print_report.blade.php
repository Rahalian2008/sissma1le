<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $config['title'] }} - {{ $selectedClass?->name ?? 'Semua Kelas' }} [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $headmasterName = $headmasterName ?? (\App\Models\SchoolSetting::get('headmaster_name') ?? ($school['headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd'));
        $headmasterNip = $headmasterNip ?? (\App\Models\SchoolSetting::get('headmaster_nip') ?? ($school['headmaster_nip'] ?? '198009132008011005'));
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.1cm 1.4cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10pt' : '9.5pt' }};
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
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            flex-wrap: wrap;
            gap: 10px;
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
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .doc-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 10px 20px;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
        }
        .title-block {
            text-align: center;
            margin-bottom: 16px;
        }
        .title-block h2 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .title-block p {
            margin: 4px 0 0 0;
            font-size: 10pt;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
        }
        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .summary-box {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .summary-box div {
            border: 1px solid #666;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .signature-grid {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-grid td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 10pt;
        }
        @media print {
            .no-print-bar { display: none !important; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Dokumen:</strong> {{ $config['title'] }} ({{ $records->count() }} Data)
        </div>
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210 × 297 mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215 × 330 mm)
                </a>
            </div>
            <a href="{{ route('worship.report', $activity) }}" class="btn-back">← Kembali</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak ({{ $isF4 ? 'F4' : 'A4' }})</button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper" style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat" style="max-width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <div class="kop-header">
                <p style="text-transform: uppercase; font-size: 10pt; margin: 0;">PEMERINTAH PROVINSI {{ $schoolProvince ?? ($school['school_province'] ?? 'JAWA BARAT') }}<br>DINAS PENDIDIKAN</p>
                <h3>{{ $schoolName ?? ($school['school_name'] ?? 'SMA NEGERI 1 LENGKONG') }}</h3>
                <p>{{ $schoolAddress ?? ($school['school_address'] ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat') }} | Email: {{ $schoolEmail ?? ($school['school_email'] ?? 'sman1lengkong@gmail.com') }}</p>
            </div>
        @endif

        <!-- Title -->
        <div class="title-block">
            <h2>DOKUMEN LAPORAN {{ strtoupper($config['title']) }}</h2>
            <p>{{ $periodTitle ?? ('Periode: ' . \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') . ' s.d. ' . \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y')) }}</p>
        </div>

        <!-- Meta Info -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%;">Kelas</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;" class="font-bold">{{ $selectedClass?->name ?? 'Semua Kelas' }}</td>
                <td style="width: 18%;">Target Peserta</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $config['gender_label'] }}</td>
            </tr>
            <tr>
                <td>Jadwal Rutin</td>
                <td>:</td>
                <td>{{ $config['days_label'] }} [{{ $config['start_time'] }} - {{ $config['end_time'] }} WIB]</td>
                <td>Waktu Cetak</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') }} WIB</td>
            </tr>
        </table>

        <!-- Ringkasan Singkat -->
        <div class="summary-box">
            <div>Total Log: <strong>{{ $stats['total'] }}</strong></div>
            <div>Hadir: <strong>{{ $stats['hadir'] }}</strong></div>
            <div>Terlambat: <strong>{{ $stats['terlambat'] }}</strong></div>
            <div>Izin/Udzur: <strong>{{ $stats['izin_udzur'] }}</strong></div>
            <div>Alpa: <strong>{{ $stats['alpa'] }}</strong></div>
        </div>

        @if(isset($studentRecaps) && $studentRecaps->isNotEmpty())
        <!-- Tabel Rekapitulasi Kehadiran Siswa (Format Standar Dinas Presensi Utama) -->
        <div style="margin-top: 10px; margin-bottom: 4px; font-weight: bold; font-size: 10pt; text-transform: uppercase;">
            I. Rekapitulasi Kumulatif Kehadiran Siswa
        </div>
        <table class="data-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NISN / NIS</th>
                    <th style="width: 32%;">Nama Siswa</th>
                    <th style="width: 12%;">Kelas</th>
                    <th style="width: 7%;">H</th>
                    <th style="width: 7%;">T</th>
                    <th style="width: 7%;">I/Udzur</th>
                    <th style="width: 7%;">A</th>
                    <th style="width: 8%;">Total</th>
                    <th style="width: 10%;">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentRecaps as $idx => $row)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-mono">{{ $row['student']->nisn ?? $row['student']->nis ?? '-' }}</td>
                        <td><strong>{{ $row['student']->name }}</strong></td>
                        <td class="text-center">{{ $row['student']->schoolClass?->name ?? '-' }}</td>
                        <td class="text-center">{{ $row['hadir'] }}</td>
                        <td class="text-center">{{ $row['terlambat'] }}</td>
                        <td class="text-center">{{ $row['izin_udzur'] }}</td>
                        <td class="text-center">{{ $row['alpa'] }}</td>
                        <td class="text-center font-bold">{{ $row['total'] }}</td>
                        <td class="text-center font-bold">{{ $row['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f1f5f9;">
                    <td colspan="4" class="text-center">JUMLAH TOTAL KUMULATIF</td>
                    <td class="text-center">{{ $totals['hadir'] ?? 0 }}</td>
                    <td class="text-center">{{ $totals['terlambat'] ?? 0 }}</td>
                    <td class="text-center">{{ $totals['izin_udzur'] ?? 0 }}</td>
                    <td class="text-center">{{ $totals['alpa'] ?? 0 }}</td>
                    <td class="text-center">{{ ($totals['hadir'] ?? 0) + ($totals['terlambat'] ?? 0) + ($totals['izin_udzur'] ?? 0) + ($totals['alpa'] ?? 0) }}</td>
                    <td class="text-center">{{ $totals['avg_percentage'] ?? 0 }}%</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Tabel Log Presensi -->
        <div style="margin-top: 10px; margin-bottom: 4px; font-weight: bold; font-size: 10pt; text-transform: uppercase;">
            {{ isset($studentRecaps) && $studentRecaps->isNotEmpty() ? 'II. ' : '' }}Rincian Log Presensi Ibadah
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Hari, Tanggal</th>
                    <th style="width: 12%;">NISN</th>
                    <th style="width: 28%;">Nama Siswa</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 10%;">Jam</th>
                    <th style="width: 10%;">Metode</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $idx => $r)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->date)->locale('id')->translatedFormat('l, d/m/Y') }}</td>
                        <td class="text-center">{{ $r->student?->nisn }}</td>
                        <td class="font-bold">{{ $r->student?->name }}</td>
                        <td class="text-center">{{ $r->student?->schoolClass?->name ?? '-' }}</td>
                        <td class="text-center font-bold">{{ substr($r->time, 0, 5) }}</td>
                        <td class="text-center">{{ $r->method }}</td>
                        <td class="text-center font-bold">{{ $r->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada data presensi pada rentang tanggal dan filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Resmi (Kepala Sekolah WAJIB di Kiri) -->
        <table class="signature-grid">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <strong><u>{{ $headmasterName }}</u></strong><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td>
                    @php
                        $printUser = Auth::user();
                        $signerRole = 'Koordinator Keagamaan';
                        $signerName = $religionCoordinator['name'] ?? 'Koordinator Keagamaan';
                        $signerNip = $religionCoordinator['nip'] ?? '-';

                        if ($printUser?->hasRole('wali_kelas') && !empty($selectedClass)) {
                            $wkName = $selectedClass?->name ?? ($printUser->teacher?->homeroomClass?->name ?? '');
                            $signerRole = $wkName ? 'Wali Kelas ' . $wkName : 'Wali Kelas';
                            if (!empty($selectedClass?->homeroomTeacher)) {
                                $signerName = $selectedClass->homeroomTeacher->name;
                                $signerNip = $selectedClass->homeroomTeacher->nip;
                            } else {
                                $signerName = $printUser?->teacher?->name ?? ($printUser?->name ?? 'Wali Kelas');
                                $signerNip = $printUser?->teacher?->nip ?? '-';
                            }
                        } elseif ($printUser?->hasRole('sekretaris_kelas') && !empty($selectedClass)) {
                            $signerRole = 'Sekretaris Kelas ' . ($selectedClass->name ?? '');
                            $signerName = $printUser->name;
                            $signerNip = $printUser->student?->nisn ?? ($printUser->student?->nis ?? '-');
                        } else {
                            // Untuk Admin, Super Admin, Koordinator Keagamaan, atau Petugas lainnya:
                            // Wajib ditandatangani oleh Koordinator Keagamaan resmi dari Data Guru & Akun
                            $signerRole = 'Koordinator Keagamaan';
                            $signerName = $religionCoordinator['name'] ?? 'Koordinator Keagamaan';
                            $signerNip = $religionCoordinator['nip'] ?? '-';
                        }
                    @endphp
                    {{ $school['school_city'] ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}<br>
                    <strong>{{ $signerRole }}</strong>
                    <br><br><br><br>
                    <strong><u>{{ $signerName }}</u></strong><br>
                    NIP. {{ $signerNip ?: '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
