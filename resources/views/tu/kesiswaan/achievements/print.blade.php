<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Siswa Berprestasi &amp; Penghargaan - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #111; line-height: 1.3; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #333; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9.5pt; }
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

    <div class="title">REKAPITULASI DATA SISWA BERPRESTASI &amp; PENGHARGAAN</div>
    <div class="subtitle">Catatan Rekam Jejak Prestasi Akademik, Non-Akademik, dan Apresiasi Karakter Siswa</div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="20%">Nama Siswa &amp; NISN</th>
                <th width="9%">Kelas</th>
                <th width="26%">Nama Prestasi / Perlombaan</th>
                <th width="16%">Kategori / Bidang</th>
                <th width="11%">Tingkat</th>
                <th width="6%">Poin</th>
                <th width="8%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPoin = 0; @endphp
            @forelse($achievements as $idx => $ach)
                @php $totalPoin += $ach->points; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $ach->student?->name ?? 'Siswa' }}</strong>
                        <div style="font-size: 8.5pt; color: #444;">NISN: {{ $ach->student?->nisn ?: '-' }}</div>
                    </td>
                    <td class="text-center">{{ $ach->student?->schoolClass?->name ?? '-' }}</td>
                    <td>
                        <strong>{{ $ach->title }}</strong>
                        @if($ach->description)<div style="font-size: 8pt; color: #555;">{{ $ach->description }}</div>@endif
                    </td>
                    <td>{{ $ach->category?->name ?? '-' }}</td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $ach->level }}</td>
                    <td class="text-center font-bold">+{{ $ach->points }}</td>
                    <td class="text-center">{{ $ach->date?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada data prestasi yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if($achievements->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f7f7f7;">
                    <td colspan="6" class="text-center">TOTAL POIN APRESIASI PRESTASI</td>
                    <td class="text-center font-bold">+{{ $totalPoin }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
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
                Wakil Kepala Sekolah Bidang Kesiswaan
                <br><br><br><br>
                <strong><u>{{ $kesiswaanTeacher?->name ?? 'Winda Dwi Atyani, S.Pd., M.Pd.' }}</u></strong><br>
                NIP. {{ $kesiswaanTeacher?->nip ?? '197911162008012007' }}
            </td>
        </tr>
    </table>
</body>
</html>
