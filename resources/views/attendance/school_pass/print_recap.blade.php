<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Laporan Izin Keluar Lingkungan Sekolah - {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #000;
            line-height: 1.35;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .container {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
        }
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-left: 8px;
        }
        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
        .title-block {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .title-block h3 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .title-block p {
            margin: 4px 0 0 0;
            font-size: 10pt;
            color: #333;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 10pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 7px;
            font-size: 9.5pt;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        table.signatures td {
            vertical-align: top;
            font-size: 10pt;
            border: none;
        }
        .sig-space {
            height: 65px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toolbar Cetak (Non-Print) -->
        <div class="no-print-bar">
            <div>
                <strong>Laporan Resmi:</strong> Rekapitulasi Izin Keluar Lingkungan Sekolah (Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }})
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak Rekap (Print)</button>
                <button type="button" onclick="window.close()" class="btn-close">Tutup</button>
            </div>
        </div>

        <!-- Kop Surat Resmi dari Master Data Logo & Kop Sekolah -->
        <x-print-kop :school="$school" />

        <!-- Judul Laporan -->
        <div class="title-block">
            <h3>REKAPITULASI LAPORAN IZIN KELUAR LINGKUNGAN SEKOLAH (SEMENTARA)</h3>
            <p>
                Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
                @if($selectedClass) • Kelas: <strong>{{ $selectedClass->name }}</strong> @endif
                @if($status && $status !== 'ALL') • Status: <strong>{{ $status }}</strong> @endif
            </p>
        </div>

        <div class="meta-info">
            <span>Total Izin Keluar Tercatat: <strong>{{ $records->count() }} Dokumen</strong></span>
            <span>Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i') }} WIB</span>
        </div>

        <!-- Tabel Data Rekapitulasi -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 13%;">No. Surat</th>
                    <th style="width: 9%;">Tanggal</th>
                    <th style="width: 10%;">Jam Keluar / Kembali</th>
                    <th style="width: 22%;">Daftar Siswa (NIS & Kelas)</th>
                    <th style="width: 18%;">Tujuan & Keperluan</th>
                    <th style="width: 10%;">Pendamping</th>
                    <th style="width: 7%;">Status</th>
                    <th style="width: 7%;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $idx => $r)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $r->permit_number }}</td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt;">
                            Keluar: <strong>{{ substr($r->out_time, 0, 5) }}</strong><br>
                            Est. Balik: {{ $r->expected_back_time ? substr($r->expected_back_time, 0, 5) : '-' }}<br>
                            @if($r->actual_back_time)
                                <span style="color: #047857; font-weight: bold;">Kembali: {{ substr($r->actual_back_time, 0, 5) }}</span>
                            @endif
                        </td>
                        <td>
                            @foreach($r->students as $s)
                                <div style="margin-bottom: 2px;">
                                    <strong>{{ $s->name }}</strong> ({{ $s->schoolClass?->name ?? '-' }} - {{ $s->nis }})
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <strong>{{ $r->purpose }}</strong>
                            @if($r->destination) <br><span style="font-size: 8.5pt; color: #555;">Tujuan: {{ $r->destination }}</span> @endif
                            <br><span style="font-size: 8.5pt;">{{ Str::limit($r->reason, 75) }}</span>
                        </td>
                        <td>
                            {{ $r->accompanied_by ?: '-' }}
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $r->status }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt;">
                            {{ $r->approver?->name ? Str::limit($r->approver->name, 15) : ($r->creator?->name ? Str::limit($r->creator->name, 15) : '-') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #777;">
                            Tidak ada data izin keluar sekolah pada filter dan periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan: Kepala Sekolah Sebelah Kiri, Wakasek Kesiswaan Sebelah Kanan (Sesuai Permintaan User) -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; padding-left: 20px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $principalName }}</div>
                    <div>NIP: {{ $principalNip }}</div>
                </td>
                <td style="width: 50%; text-align: right; padding-right: 20px;">
                    Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                    <strong>Wakasek Bidang Kesiswaan</strong>,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $wakasekName }}</div>
                    <div>NIP: {{ $wakasekNip }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

