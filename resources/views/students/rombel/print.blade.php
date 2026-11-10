<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta Didik Rombel {{ $rombel }} - {{ $school->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; font-size: 13pt; text-decoration: underline; }
        .meta-box { margin-bottom: 15px; font-size: 10.5pt; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
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
    <x-print-toolbar :title="'Daftar Peserta Didik Rombel ' . $rombel" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="title">
        DAFTAR PESERTA DIDIK PER ROMBONGAN BELAJAR
    </div>

    <div class="meta-box">
        <div>
            <div>Rombongan Belajar : <strong>{{ $rombel }}</strong></div>
            <div>Wali Kelas : <strong>{{ $waliKelas?->formatted_name ?? $waliKelas?->name ?? '....................................' }}</strong></div>
        </div>
        <div style="text-align: right;">
            <div>Tahun Ajaran : <strong>{{ date('Y') }}/{{ date('Y')+1 }}</strong></div>
            <div>Jumlah Siswa : <strong>{{ $students->count() }} Orang (L: {{ $students->where('gender', 'L')->count() }}, P: {{ $students->where('gender', 'P')->count() }})</strong></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">NIS</th>
                <th style="width: 16%;">NISN</th>
                <th style="width: 32%;">Nama Lengkap Peserta Didik</th>
                <th style="width: 8%;">L/P</th>
                <th style="width: 25%;">Tempat, Tanggal Lahir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $idx => $s)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $s->nis }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $s->nisn ?: '-' }}</td>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ $s->gender }}</td>
                    <td>{{ $s->birth_place ?: '-' }}, {{ $s->birth_date ? $s->birth_date->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Belum ada siswa pada rombel ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-block">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '....................................' }}
        </div>
        <div class="sig-block">
            Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
            Wali Kelas {{ $rombel }}<br><br><br><br>
            <strong><u>{{ $waliKelas?->formatted_name ?? $waliKelas?->name ?? '....................................' }}</u></strong><br>
            NIP. {{ $waliKelas?->nip ?? '....................................' }}
        </div>
    </div>
    </div>
</body>
</html>
