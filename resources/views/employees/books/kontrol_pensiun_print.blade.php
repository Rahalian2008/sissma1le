<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Penjagaan Batas Usia Pensiun (BUP) - {{ $school->name }}</title>
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
    <x-print-toolbar :title="'Buku Penjagaan Batas Usia Pensiun (BUP)'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="title">
        BUKU PENJAGAAN BATAS USIA PENSIUN (BUP) PEGAWAI NEGERI SIPIL
    </div>
    <div style="text-align: center; margin-top: -10px; margin-bottom: 15px; font-size: 10pt;">
        Tahun Anggaran: <strong>{{ date('Y') }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 22%;">Nama Pegawai / NIP / NIK</th>
                <th style="width: 14%;">Jabatan / Tugas</th>
                <th style="width: 8%;">Pangkat / Gol.</th>
                <th style="width: 11%;">Tanggal Lahir</th>
                <th style="width: 7%;">Usia BUP</th>
                <th style="width: 12%;">TMT Pensiun</th>
                <th style="width: 11%;">Status Usulan</th>
                <th style="width: 12%;">Keterangan / No. SK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $index => $emp)
                @php
                    $calcTmt = $emp->calculated_pension_tmt;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $emp->formatted_name }}</strong><br>
                        <span style="font-family: monospace; font-size: 8.5pt;">NIP. {{ $emp->nip ?? '-' }}</span>
                    </td>
                    <td>{{ $emp->position ?? ($emp->employment_type ?? '-') }}</td>
                    <td style="text-align: center;">{{ $emp->rank_group ?? '-' }}</td>
                    <td style="text-align: center;">
                        {{ $emp->birth_date ? $emp->birth_date->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: center;">{{ $emp->retirement_age }} Thn</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $calcTmt ? $calcTmt->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: center; font-size: 8.5pt;">
                        {{ $emp->pension_status ?: 'Belum Waktunya' }}
                    </td>
                    <td style="font-size: 8pt;">
                        {{ $emp->pension_sk_number ?: ($emp->pension_notes ?: '-') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px;">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-block">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
        </div>
        <div class="sig-block">
            Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Urusan Tata Usaha / Kepegawaian<br><br><br><br>
            <strong><u>{{ $school->tu_head_name ?? '..' }}</u></strong><br>
            NIP. {{ $school->tu_head_nip ?? '..' }}
        </div>
    </div>
    </div>
</body>
</html>
