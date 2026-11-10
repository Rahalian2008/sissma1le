<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Penerimaan SPMB - {{ $spmb->registration_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }
        .header-kop h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 9pt;
            margin: 0;
        }
        .letter-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .letter-number {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        table.biodata {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px 20px;
        }
        table.biodata td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .result-box {
            border: 2px solid #000;
            padding: 12px;
            text-align: center;
            margin: 16px 0;
            background-color: #f8fafc;
        }
        .result-box .status-text {
            font-size: 16pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
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
            margin-bottom: 16px;
            font-family: sans-serif;
            font-size: 12px;
            border-radius: 8px;
        }
        .btn-print {
            background: #059669;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="print-btn-bar no-print">
        <span>SURAT KEPUTUSAN HASIL SELEKSI SPMB &bull; {{ $spmb->full_name }}</span>
        <button onclick="window.print()" class="btn-print">Cetak Surat Keputusan (Ctrl+P)</button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <div class="letter-title">SURAT KEPUTUSAN KELULUSAN SELEKSI SPMB</div>
    <div class="letter-number">Nomor: 421.3/SPMB-{{ date('Y') }}/SMAN1LE/{{ str_pad((string)$spmb->id, 4, '0', STR_PAD_LEFT) }}</div>

    <p>
        Berdasarkan hasil verifikasi berkas, penilaian portofolio akademik, dan evaluasi hasil seleksi Penerimaan Siswa Baru Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}, Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} menerangkan bahwa:
    </p>

    <table class="biodata">
        <tr>
            <td style="width: 200px;">Nama Lengkap Siswa</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ strtoupper($spmb->full_name) }}</strong></td>
        </tr>
        <tr>
            <td>Nomor Registrasi SPMB</td>
            <td>:</td>
            <td style="font-family: monospace; font-weight: bold;">{{ $spmb->registration_number }}</td>
        </tr>
        @if($spmb->tka_exam_number)
        <tr>
            <td>Nomor Peserta Ujian TKA</td>
            <td>:</td>
            <td style="font-family: monospace; font-weight: bold;">{{ $spmb->tka_exam_number }}</td>
        </tr>
        @endif
        <tr>
            <td>NISN / NIK</td>
            <td>:</td>
            <td>{{ $spmb->nisn }} / {{ $spmb->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $spmb->birth_place ?? '-' }}, {{ $spmb->birth_date ? $spmb->birth_date->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Asal Sekolah (SMP/MTs)</td>
            <td>:</td>
            <td>{{ $spmb->school_origin ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jalur Pendaftaran</td>
            <td>:</td>
            <td><strong>{{ $spmb->selection_track }}</strong></td>
        </tr>
        @if(str_contains($spmb->selection_track, 'TKA') && $spmb->tka_weighted_score)
        <tr>
            <td>Skor Akhir Terbobot TKA</td>
            <td>:</td>
            <td><strong>{{ number_format((float) $spmb->tka_weighted_score, 2) }}</strong> (Peringkat #{{ $spmb->tka_rank ?? '-' }})</td>
        </tr>
        @endif
    </table>

    <div class="result-box">
        <div>Dinyatakan:</div>
        <div class="status-text">
            @if(in_array($spmb->status, ['Lolos Seleksi', 'Diterima', 'Daftar Ulang']))
                LOLOS SELEKSI (DITERIMA)
            @else
                {{ strtoupper($spmb->status) }}
            @endif
        </div>
        <div style="font-size: 10pt; margin-top: 4px;">
            Sebagai Peserta Didik Baru Kelas X {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}
            @if($spmb->assigned_class)
                <br>Penempatan Rombongan Belajar: <strong>{{ $spmb->assigned_class }}</strong>
            @endif
        </div>
    </div>

    <p>
        Kepada calon peserta didik yang dinyatakan <strong>LOLOS SELEKSI (DITERIMA)</strong> diwajibkan melaksanakan proses <strong>Daftar Ulang</strong> sesuai dengan jadwal dan persyaratan administratif yang telah ditetapkan panitia SPMB.
    </p>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Sebelah Kiri & Berstatus Mengetahui) -->
    <table class="signatures" style="width: 100%; margin-top: 30px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top;">
                Mengetahui,<br>
                Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }},<br><br><br><br><br>
                <strong><u>{{ strtoupper($school->headmaster_name ?? ($school->principal_name ?? 'PIAN SOPIAN, S.Pd.')) }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? ($school->principal_nip ?? '198009132008011005') }}
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                Ketua Panitia SPMB,<br><br><br><br><br>
                <strong><u>{{ strtoupper(\App\Models\Teacher::getWakasekKesiswaan()['name'] ?? 'Panitia SPMB') }}</u></strong><br>
                NIP. {{ \App\Models\Teacher::getWakasekKesiswaan()['nip'] ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

