<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penerima Beasiswa / Bantuan Siswa</title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #111; line-height: 1.3; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .kop h3 { margin: 2px 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 2px 0; font-size: 9pt; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 16px; font-size: 12pt; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 5px 7px; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .ttd-box { margin-top: 30px; float: right; width: 260px; text-align: center; font-size: 10pt; }
    </style>
</head>
<body onload="window.print()">
    <div class="kop">
        <h2>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h2>
        <h3>DINAS PENDIDIKAN - CABANG DINAS PENDIDIKAN WILAYAH V</h3>
        <h2>{{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</h2>
        <p>{{ $school->address ?? 'Jl. Raya Lengkong No. 45' }} | NPSN: {{ $school->npsn ?? '20202265' }}</p>
    </div>

    <div class="title">DAFTAR REKAPITULASI PENERIMA BEASISWA / BANTUAN SISWA</div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="12%">NISN</th>
                <th width="10%">Kelas</th>
                <th width="18%">Program Beasiswa</th>
                <th width="14%">Sumber Dana</th>
                <th width="12%">Nominal (Rp)</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDana = 0; @endphp
            @forelse ($scholarships as $index => $sch)
                @php $totalDana += $sch->amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sch->student?->name ?? '-' }}</td>
                    <td class="text-center">{{ $sch->student?->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $sch->student?->schoolClass?->name ?? '-' }}</td>
                    <td>{{ $sch->scholarship_name }}</td>
                    <td>{{ $sch->source }}</td>
                    <td class="text-right">{{ number_format($sch->amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $sch->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data beasiswa.</td>
                </tr>
            @endforelse
        </tbody>
        @if($scholarships->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f7f7f7;">
                    <td colspan="6" class="text-center">TOTAL KESELURUHAN DANA BANTUAN</td>
                    <td class="text-right">Rp {{ number_format($totalDana, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="ttd-box">
        <p>Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
        <br><br><br>
        <p><strong><u>{{ $school->headmaster_name ?? 'Drs. H. Pimpinan, M.Pd.' }}</u></strong><br>
        NIP. {{ $school->headmaster_nip ?? '19700101 199501 1 001' }}</p>
    </div>
</body>
</html>

