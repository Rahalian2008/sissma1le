<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Rekapitulasi Diklat & Pengembangan Diri PTK - {{ $school->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; line-height: 1.3; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9pt; font-style: italic; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; font-size: 12.5pt; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9pt; }
        th, td { border: 1px solid #000; padding: 5px 6px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .signatures { margin-top: 30px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Rekapitulasi Diklat & Pengembangan Diri PTK'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="title">
        BUKU REKAPITULASI PELATIHAN & PENGEMBANGAN KEPROFESIAN BERKELANJUTAN (PKB) GURU & TENDIK
    </div>
    <div style="text-align: center; margin-top: -10px; margin-bottom: 15px; font-size: 10pt;">
        Tahun Kegiatan: <strong>{{ $selectedYear }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 22%;">Nama Lengkap Pegawai & NIP</th>
                <th style="width: 27%;">Nama Pelatihan / Diklat / Bimtek</th>
                <th style="width: 18%;">Lembaga Penyelenggara</th>
                <th style="width: 6%;">JP</th>
                <th style="width: 12%;">Tanggal Pelaksanaan</th>
                <th style="width: 12%;">No. Sertifikat / Piagam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trainings as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->employee?->formatted_name ?? '-' }}</strong><br>
                        <span style="font-family: monospace; font-size: 8.5pt;">NIP. {{ $item->employee?->nip ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->title }}</strong>
                        @if($item->notes)
                            <div style="font-size: 8pt; color: #555;">Ket: {{ $item->notes }}</div>
                        @endif
                    </td>
                    <td>{{ $item->institution_or_issuer ?? '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->training_hours ?? 32 }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">
                        {{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }}
                        @if($item->end_date && $item->end_date->ne($item->start_date))
                            - {{ $item->end_date->format('d/m/Y') }}
                        @endif
                    </td>
                    <td style="text-align: center; font-family: monospace; font-size: 8.5pt;">
                        {{ $item->document_number ?: '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">Belum ada data pelatihan / diklat yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if($trainings->isNotEmpty())
            <tfoot>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="4" style="text-align: right; padding-right: 10px;">Total Jam Pelatihan (JP) Terakumulasi:</td>
                    <td style="text-align: center;">{{ $trainings->sum('training_hours') }} JP</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signatures">
        <div class="sig-block">
            Mengetahui,<br>
            <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
        </div>
        <div class="sig-block">
            Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
            Koordinator Kepegawaian / PKB<br><br><br><br>
            <strong><u>{{ $school->tu_head_name ?? '..' }}</u></strong><br>
            NIP. {{ $school->tu_head_nip ?? '..' }}
        </div>
    </div>
    </div>
</body>
</html>
