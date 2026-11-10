<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Hasil Seleksi SPMB SMA Negeri 1 Lengkong</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }
        .header-kop h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 8pt;
            margin: 0;
        }
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 12px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .signatures {
            width: 100%;
            margin-top: 20px;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9pt;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-family: sans-serif;
            font-size: 11px;
            border-radius: 8px;
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="print-btn-bar no-print">
        <span>REKAPITULASI HASIL SELEKSI SPMB &bull; {{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</span>
        <button onclick="window.print()" class="btn-print">Cetak Dokumen Resmi (Ctrl+P)</button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <div class="doc-title">REKAPITULASI PENETAPAN HASIL SELEKSI PENERIMAAN MURID BARU (SPMB)</div>
    <div class="doc-subtitle">
        TAHUN AJARAN {{ date('Y') }}/{{ date('Y')+1 }} &bull; 
        JALUR: <strong>{{ $track === 'TKA' ? 'TES KEMAMPUAN AKADEMIK (TKA)' : ($track === 'all' ? 'SEMUA JALUR SELEKSI' : strtoupper($track)) }}</strong>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Registrasi</th>
                <th>No. Ujian TKA</th>
                <th>Nama Calon Siswa</th>
                <th>NISN</th>
                <th>L/P</th>
                <th>Asal Sekolah</th>
                <th>Jalur SPMB</th>
                <th>Rapor SMP</th>
                <th>Rata TKA</th>
                <th>Skor Akhir (60:40)</th>
                <th>Rank TKA</th>
                <th>Status Seleksi</th>
                <th>Rombel</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $idx => $p)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $p->registration_number }}</td>
                <td style="font-family: monospace; color: #b45309;">{{ $p->tka_exam_number ?? '-' }}</td>
                <td style="font-weight: bold;">{{ $p->full_name }}</td>
                <td style="font-family: monospace;">{{ $p->nisn }}</td>
                <td style="text-align: center;">{{ $p->gender }}</td>
                <td>{{ $p->school_origin ?? '-' }}</td>
                <td>{{ $p->selection_track }}</td>
                <td style="text-align: center; font-family: monospace;">{{ number_format((float) $p->average_score, 2) }}</td>
                <td style="text-align: center; font-family: monospace;">{{ $p->tka_score_total !== null ? number_format((float) $p->tka_score_total, 2) : '-' }}</td>
                <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $p->tka_weighted_score !== null ? number_format((float) $p->tka_weighted_score, 2) : '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $p->tka_rank ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $p->status }}</td>
                <td style="text-align: center;">{{ $p->assigned_class ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align: center; padding: 15px;">Tidak ada data calon siswa pada kriteria ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Sebelah Kiri & Berstatus Mengetahui) -->
    <table class="signatures" style="width: 100%; margin-top: 25px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top;">
                Mengetahui,<br>
                Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }},<br><br><br><br>
                <strong><u>{{ strtoupper($school->headmaster_name ?? ($school->principal_name ?? 'PIAN SOPIAN, S.Pd.')) }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? ($school->principal_nip ?? '198009132008011005') }}
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                Ketua Panitia SPMB,<br><br><br><br>
                <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? (\App\Models\Teacher::getWakasekKesiswaan()['name'] ?? 'Panitia SPMB')) }}</u></strong><br>
                NIP. {{ auth()->user()?->getOfficialNip() ?? (\App\Models\Teacher::getWakasekKesiswaan()['nip'] ?? '-') }}
            </td>
        </tr>
    </table>
</body>
</html>

