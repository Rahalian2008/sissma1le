<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Ekstrakurikuler {{ $extra->name }} [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
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
            background: #0f172a;
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
            background: #1e293b;
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
            background: #0891b2;
            color: #ffffff;
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .btn-print {
            background: #059669;
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
            <strong>Pratinjau Dokumen:</strong> Presensi Ekstrakurikuler {{ $extra->name }} ({{ $records->count() }} Data)
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
            <a href="{{ route('extracurricular.report', ['extra_id' => $extra->id]) }}" class="btn-back">← Kembali</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak ({{ $isF4 ? 'F4' : 'A4' }})</button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @php
            $actualKopPath = $kopImageUrl ?? \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image')));
            $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        @endphp
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper" style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi" style="max-width: 100%; max-height: 125px; object-fit: contain;">
            </div>
        @else
            <div class="kop-header">
                <p style="text-transform: uppercase; font-size: 10pt; margin: 0;">PEMERINTAH PROVINSI JAWA BARAT<br>DINAS PENDIDIKAN</p>
                <h3>{{ $schoolName }}</h3>
                <p>{{ $schoolAddress }} | NPSN: {{ $schoolNpsn }}</p>
            </div>
        @endif

        <!-- Title -->
        <div class="title-block">
            <h2>REKAPITULASI PRESENSI EKSTRAKURIKULER {{ strtoupper($extra->name) }}</h2>
            <p>{{ $periodTitle ?? ('Periode: ' . $startDateFormatted . ' s.d. ' . $endDateFormatted) }}</p>
        </div>

        <!-- Meta Info -->
        <table class="meta-table">
            <tr>
                <td style="width: 20%;">Ekstrakurikuler</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;" class="font-bold">{{ $extra->name }} ({{ $extra->category }})</td>
                <td style="width: 18%;">Jadwal Latihan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ $extra->schedule_day ?? '-' }} ({{ substr($extra->start_time ?? '', 0, 5) }} - {{ substr($extra->end_time ?? '', 0, 5) }})</td>
            </tr>
            <tr>
                <td>Guru Pembina</td>
                <td>:</td>
                <td class="font-bold">{{ $extra->teacher_names }}</td>
                <td>Lokasi</td>
                <td>:</td>
                <td>{{ $extra->location ?? 'Lingkungan Sekolah' }}</td>
            </tr>
            @if($selectedClass)
            <tr>
                <td>Filter Rombel</td>
                <td>:</td>
                <td colspan="4" class="font-bold">Kelas {{ $selectedClass->name }}</td>
            </tr>
            @endif
        </table>

        @if(isset($studentRecaps) && $studentRecaps->isNotEmpty())
        <!-- Tabel Rekapitulasi Kehadiran Siswa (Format Standar Dinas Presensi Utama) -->
        <div style="margin-top: 10px; margin-bottom: 4px; font-weight: bold; font-size: 10pt; text-transform: uppercase;">
            I. Rekapitulasi Kumulatif Kehadiran Anggota
        </div>
        <table class="data-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIS / NISN</th>
                    <th style="width: 32%;">Nama Siswa</th>
                    <th style="width: 12%;">Kelas</th>
                    <th style="width: 6%;">H</th>
                    <th style="width: 6%;">T</th>
                    <th style="width: 6%;">I</th>
                    <th style="width: 6%;">S</th>
                    <th style="width: 6%;">A</th>
                    <th style="width: 6%;">Total</th>
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
                        <td class="text-center">{{ $row['izin'] }}</td>
                        <td class="text-center">{{ $row['sakit'] }}</td>
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
                    <td class="text-center">{{ $totals['izin'] ?? 0 }}</td>
                    <td class="text-center">{{ $totals['sakit'] ?? 0 }}</td>
                    <td class="text-center">{{ $totals['alpa'] ?? 0 }}</td>
                    <td class="text-center">{{ ($totals['hadir'] ?? 0) + ($totals['terlambat'] ?? 0) + ($totals['izin'] ?? 0) + ($totals['sakit'] ?? 0) + ($totals['alpa'] ?? 0) }}</td>
                    <td class="text-center">{{ $totals['avg_percentage'] ?? 0 }}%</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Tabel Log Presensi Kegiatan -->
        <div style="margin-top: 10px; margin-bottom: 4px; font-weight: bold; font-size: 10pt; text-transform: uppercase;">
            {{ isset($studentRecaps) && $studentRecaps->isNotEmpty() ? 'II. ' : '' }}Rincian Log Presensi Kegiatan
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Hari, Tanggal</th>
                    <th style="width: 14%;">NISN</th>
                    <th style="width: 26%;">Nama Siswa</th>
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
                        <td class="text-center">{{ \Carbon\Carbon::parse($r->date)->locale('id')->translatedFormat('D, d/m/Y') }}</td>
                        <td class="text-center font-mono">{{ $r->student?->nisn ?? $r->student?->nis ?? '-' }}</td>
                        <td><strong>{{ $r->student?->name }}</strong></td>
                        <td class="text-center">{{ $r->schoolClass?->name ?? ($r->student?->schoolClass?->name ?? '-') }}</td>
                        <td class="text-center font-mono">{{ substr($r->time, 0, 5) }}</td>
                        <td class="text-center font-mono">{{ $r->method }}</td>
                        <td class="text-center font-bold">{{ $r->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 16px;">Tidak ada data presensi pada rentang tanggal tersebut.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan (Kepala Sekolah WAJIB di Kiri) -->
        <table class="signature-grid">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <strong><u>{{ $headmasterName ?? \App\Models\SchoolSetting::get('headmaster_name', 'Drs. H. Sukardi, M.Pd.') }}</u></strong><br>
                    NIP. {{ $headmasterNip ?? \App\Models\SchoolSetting::get('headmaster_nip', '19680512 199403 1 004') }}
                </td>
                <td>
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ $printDateFormatted }}<br>
                    <strong>Pembina Ekstrakurikuler</strong>
                    <br><br><br><br>
                    <strong><u>{{ $extra->teacher?->name ?? (Auth::user()->name ?? 'Guru Pembina') }}</u></strong><br>
                    NIP. {{ $extra->teacher?->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
