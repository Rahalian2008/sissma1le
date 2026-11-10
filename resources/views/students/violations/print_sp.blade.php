<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peringatan {{ $spLevel }} - {{ $violation->student?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h4 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; text-decoration: underline; color: #991b1b; }
        .title p { margin: 3px 0 0 0; font-size: 10.5pt; }
        .row { display: flex; margin-bottom: 5px; }
        .label { width: 220px; }
        .colon { width: 20px; text-align: center; }
        .value { flex: 1; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 5px 8px; }
        th { background-color: #fee2e2; text-align: center; }
        .signatures { margin-top: 30px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 30%; text-align: center; font-size: 10pt; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Peringatan (' . $spLevel . '): ' . ($violation->student?->name ?? '')" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div class="title">
            <h4>SURAT PERINGATAN ({{ $spLevel }})</h4>
            <p>Nomor: <strong>421.3 / {{ str_pad((string)$violation->id, 3, '0', STR_PAD_LEFT) }} / PS.01 - {{ $school->name ?? 'SMAN 1 Lengkong' }} / {{ \App\Services\LetterNumberService::toRoman((int)date('m')) }} / {{ date('Y') }}</strong></p>
        </div>

    <p>Diberikan kepada peserta didik yang tersebut di bawah ini:</p>

    <div style="margin-left: 20px; margin-bottom: 15px;">
        <div class="row">
            <div class="label">Nama Lengkap Siswa</div>
            <div class="colon">:</div>
            <div class="value">{{ $violation->student?->name }}</div>
        </div>
        <div class="row">
            <div class="label">Nomor Induk Siswa (NIS)</div>
            <div class="colon">:</div>
            <div class="value">{{ $violation->student?->nis }}</div>
        </div>
        <div class="row">
            <div class="label">Tingkat / Rombel</div>
            <div class="colon">:</div>
            <div class="value">{{ $violation->student?->current_class_level }} ({{ $violation->student?->current_rombel ?: '-' }})</div>
        </div>
        <div class="row">
            <div class="label">Nama Orang Tua / Wali</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $violation->student?->family?->father_name ?? $violation->student?->family?->mother_name ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Akumulasi Poin Pelanggaran</div>
            <div class="colon">:</div>
            <div class="value" style="color: #b91c1c;">{{ $totalPoints }} Poin (Kategori Pembinaan Khusus)</div>
        </div>
    </div>

    <p>Surat Peringatan ini diterbitkan sehubungan dengan adanya tindakan ketidakdisiplinan/pelanggaran tata tertib sekolah sebagai berikut:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 45%;">Bentuk Pelanggaran Tata Tertib</th>
                <th style="width: 15%;">Bobot Poin</th>
                <th style="width: 20%;">Petugas Pencatat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allViolations as $idx => $v)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ $v->violation_date ? $v->violation_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $v->violation_name }}</td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">+{{ $v->points }}</td>
                    <td>{{ $v->officer_name ?: 'Tim Ketertiban' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: justify;">
        Dengan diterbitkannya Surat Peringatan ini, peserta didik yang bersangkutan diwajibkan untuk memperbaiki sikap, menaati seluruh tata tertib sekolah, dan mengikuti sesi konseling pembinaan bersama Guru Bimbingan Konseling (BK). Apabila yang bersangkutan kembali mengulangi pelanggaran tata tertib, sekolah akan mengambil tindakan tegas sesuai regulasi tata tertib satuan pendidikan yang berlaku.
    </p>

    <div class="signatures">
        <div class="sig-block">
            Mengetahui,<br>
            Orang Tua / Wali Siswa<br><br><br><br>
            <strong><u>( .................................... )</u></strong>
        </div>
        <div class="sig-block">
            Wali Kelas {{ $violation->student?->current_rombel ?? '' }},<br><br><br><br>
            <strong><u>{{ $waliKelas?->formatted_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $waliKelas?->nip ?? '....................................' }}
        </div>
        <div class="sig-block">
            Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '....................................' }}
        </div>
    </div>
    </div>
</body>
</html>
