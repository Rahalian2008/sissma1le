<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta SPMB - {{ $spmb->registration_number }} - {{ $spmb->full_name }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
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
            margin-bottom: 10px;
        }
        .header-kop h1 {
            font-size: 12pt;
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
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }
        table.info-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        .photo-box {
            width: 80px;
            height: 105px;
            border: 1px solid #000;
            text-align: center;
            line-height: 105px;
            font-size: 8pt;
            color: #666;
            margin: 0 auto;
        }
        .box-schedule {
            border: 1.5px solid #000;
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 9pt;
            background-color: #fafafa;
        }
        .rules-box {
            border: 1px dashed #666;
            padding: 6px 10px;
            font-size: 8pt;
            margin-bottom: 12px;
        }
        .rules-box ol {
            margin: 2px 0 2px 16px;
            padding: 0;
        }
        .signatures {
            width: 100%;
            margin-top: 10px;
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
        <span>KARTU PESERTA SPMB &bull; {{ $spmb->full_name }}</span>
        <button onclick="window.print()" class="btn-print">Cetak Kartu (Ctrl+P)</button>
    </div>

    <div class="header-kop">
        <h1>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h1>
        <h1>DINAS PENDIDIKAN</h1>
        <h2>{{ strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG') }}</h2>
        <p>{{ $school->address ?? 'Jl. Raya Lengkong No. 01' }}, Kab. Sukabumi &bull; NPSN: {{ $school->npsn ?? '20202265' }} &bull; Website: www.sman1lengkong.sch.id</p>
    </div>

    <div class="doc-title">KARTU TANDA PESERTA SPMB TAHUN {{ date('Y') }}/{{ date('Y')+1 }}</div>
    <div class="doc-subtitle">JALUR PENDAFTARAN: <strong>{{ strtoupper($spmb->selection_track) }}</strong></div>

    <table class="info-table">
        <tr>
            <td style="width: 140px;">No. Registrasi SPMB</td>
            <td style="width: 10px;">:</td>
            <td style="font-weight: bold; font-family: monospace; font-size: 10pt;">{{ $spmb->registration_number }}</td>
            <td rowspan="7" style="width: 95px; text-align: center;">
                <div class="photo-box">Pasfoto 3 &times; 4</div>
            </td>
        </tr>
        @if($spmb->tka_exam_number)
        <tr>
            <td style="font-weight: bold; color: #b45309;">No. Peserta Ujian TKA</td>
            <td>:</td>
            <td style="font-weight: bold; font-family: monospace; font-size: 10pt; color: #b45309;">{{ $spmb->tka_exam_number }}</td>
        </tr>
        @endif
        <tr>
            <td>Nama Lengkap Siswa</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ strtoupper($spmb->full_name) }}</td>
        </tr>
        <tr>
            <td>NISN / NIK</td>
            <td>:</td>
            <td>{{ $spmb->nisn }} / {{ $spmb->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $spmb->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $spmb->birth_place ?? '-' }}, {{ $spmb->birth_date ? $spmb->birth_date->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Asal Sekolah (SMP/MTs)</td>
            <td>:</td>
            <td>{{ $spmb->school_origin ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nilai Rata-rata Rapor</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ number_format((float) $spmb->average_score, 2) }}</td>
            <td style="text-align: center; font-size: 7.5pt; color: #666;">QR Verifikasi</td>
        </tr>
    </table>

    @if(str_contains($spmb->selection_track, 'TKA'))
    <div class="box-schedule">
        <strong style="text-transform: uppercase;">Jadwal Pelaksanaan Tes Kemampuan Akademik (TKA):</strong><br>
        &bull; <strong>Sesi / Ruang Ujian:</strong> {{ $spmb->tka_exam_session ?? 'Sesi 1 (Laboratorium Komputer CBT SMAN 1 Lengkong)' }}<br>
        &bull; <strong>Materi Asesmen:</strong> Literasi Bahasa, Numerasi / Penalaran Matematika, Logika Sains<br>
        &bull; <strong>Status Ujian:</strong> {{ $spmb->tka_status ?? 'Terjadwal' }}
    </div>
    @endif

    <div class="rules-box">
        <strong>Ketentuan & Tata Tertib Peserta:</strong>
        <ol>
            <li>Kartu Tanda Peserta ini wajib dibawa saat verifikasi berkas fisik dan saat pelaksanaan ujian TKA.</li>
            <li>Hadir di lokasi 30 menit sebelum ujian dimulai dengan mengenakan seragam sekolah asal lengkap dan rapi.</li>
            <li>Hasil seleksi resmi diumumkan melalui papan pengumuman sekolah dan portal resmi SISSMA1LE.</li>
        </ol>
    </div>

    <table class="signatures">
        <tr>
            <td>
                Calon Siswa Peserta SPMB,<br><br><br><br>
                <strong><u>{{ strtoupper($spmb->full_name) }}</u></strong><br>
                NISN. {{ $spmb->nisn }}
            </td>
            <td>
                Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                Ketua Panitia SPMB,<br><br><br><br>
                <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Panitia SPMB') }}</u></strong><br>
                NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

