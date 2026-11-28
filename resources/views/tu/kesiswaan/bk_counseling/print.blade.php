<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Layanan Konseling &amp; Peminatan Siswa - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #111; line-height: 1.3; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #333; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signatures-table { width: 100%; margin-top: 25px; border: none; font-size: 10pt; }
        .signatures-table td { border: none; padding: 0; vertical-align: top; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Kop Surat Resmi dari Master Data Logo & Kop Sekolah -->
    <x-print-kop :school="$school" />

    <div class="title">REKAPITULASI DOKUMEN LAYANAN KONSELING &amp; PEMINATAN SISWA</div>
    <div class="subtitle">Laporan Terpadu Pelayanan Konseling Pribadi, Sosial, Belajar, dan Peminatan Studi Lanjut (POP-BK)</div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="18%">Nama Siswa &amp; Kelas</th>
                <th width="12%">Bidang Layanan</th>
                <th width="10%">Jenis Sesi</th>
                <th width="18%">Arah Peminatan / Karier</th>
                <th width="21%">Topik Konseling</th>
                <th width="10%">Status</th>
                <th width="8%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $s->student?->name ?? 'Siswa' }}</strong>
                        <div style="font-size: 8pt; color: #444;">{{ $s->student?->schoolClass?->name ?? '-' }}</div>
                    </td>
                    <td>{{ $s->field_of_service }}</td>
                    <td class="text-center">{{ $s->session_type }}</td>
                    <td>
                        @if($s->career_path)
                            <strong>{{ $s->career_path }}</strong>
                            @if($s->campus_name)<br><span style="font-size: 8pt; color: #444;">{{ $s->campus_name }}</span>@endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $s->topic }}</td>
                    <td class="text-center" style="text-transform: uppercase;"><strong>{{ $s->status }}</strong></td>
                    <td class="text-center">{{ $s->date?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada sesi konseling yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures-table">
        <tr>
            <td width="50%" class="text-center">
                Mengetahui,<br>
                Kepala SMA Negeri 1 Lengkong
                <br><br><br><br>
                <strong><u>{{ $headmaster['name'] ?? ($school->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd') }}</u></strong><br>
                NIP. {{ $headmaster['nip'] ?? ($school->headmaster_nip ?? '198009132008011005') }}
            </td>
            <td width="50%" class="text-center">
                Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                Koordinator Bimbingan Konseling (BK)
                <br><br><br><br>
                <strong><u>{{ $bkCoordinator['name'] ?? 'Rahmat Hidayat, S.Kom.' }}</u></strong><br>
                NIP. {{ $bkCoordinator['nip'] ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>
