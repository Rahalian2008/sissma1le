<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Konseling - {{ $session->session_code }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 20mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 11pt; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta td { padding: 4px 6px; vertical-align: top; }
        .section-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; }
        .section-box h5 { margin: 0 0 6px 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .signature-table { width: 100%; margin-top: 35px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }
        @media print {
            body { margin: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Dokumen (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>BERITA ACARA LAYANAN BIMBINGAN & KONSELING</h4>
        <p>Nomor Registrasi: {{ $session->session_code }}</p>
    </div>

    <p>Pada hari ini, <strong>{{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}</strong> bertempat di {{ $session->place }}, telah dilaksanakan layanan Bimbingan dan Konseling berstandar Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) terhadap peserta didik:</p>

    <!-- Data Siswa & Layanan -->
    <table class="meta">
        <tr>
            <td style="width: 28%;">Nama Lengkap Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 70%; font-weight: bold;">{{ $session->student?->name }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa Nasional (NISN)</td>
            <td>:</td>
            <td>{{ $session->student?->nisn ?: '-' }} / NIS: {{ $session->student?->nis ?: '-' }}</td>
        </tr>
        <tr>
            <td>Kelas / Rombongan Belajar</td>
            <td>:</td>
            <td>{{ $session->student?->schoolClass?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Bidang Layanan BK</td>
            <td>:</td>
            <td><strong>Bimbingan {{ ucfirst(strtolower($session->field_of_service)) }}</strong> ({{ str_replace('_', ' ', $session->service_category) }})</td>
        </tr>
        <tr>
            <td>Jenis Layanan</td>
            <td>:</td>
            <td>{{ str_replace('_', ' ', $session->session_type) }}</td>
        </tr>
        <tr>
            <td>Topik / Masalah Bimbingan</td>
            <td>:</td>
            <td>{{ $session->topic }}</td>
        </tr>
    </table>

    <!-- Ringkasan Uraian Konseling -->
    <div class="section-box">
        <h5>1. Deskripsi Gejala & Latar Belakang Masalah</h5>
        <p style="margin: 0; text-align: justify;">{{ $session->problem_description }}</p>
    </div>

    @if($session->counseling_approach)
    <div class="section-box">
        <h5>2. Pendekatan & Teknik Konseling yang Diterapkan</h5>
        <p style="margin: 0;">{{ $session->counseling_approach }}</p>
    </div>
    @endif

    <div class="section-box">
        <h5>3. Kesepakatan & Komitmen Tindakan Siswa (Konseli)</h5>
        <p style="margin: 0; text-align: justify;">{{ $session->agreement_result ?: 'Siswa menyatakan komitmen untuk memperbaiki sikap, mematuhi tata tertib, dan melaksanakan jadwal bimbingan secara berkala.' }}</p>
    </div>

    @if($session->follow_up_plan)
    <div class="section-box">
        <h5>4. Rencana Tindak Lanjut & Pemantauan (RTL)</h5>
        <p style="margin: 0; text-align: justify;">{{ $session->follow_up_plan }}</p>
    </div>
    @endif

    <p style="font-size: 10pt; font-style: italic; margin-top: 10px;">
        * Berita acara ini bersifat rahasia (Confidential) sesuai dengan Kode Etik Profesi Bimbingan dan Konseling Indonesia (ABKIN).
    </p>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Siswa Konseli,<br><br><br><br><br>
                <strong><u>{{ $session->student?->name }}</u></strong><br>
                NISN. {{ $session->student?->nisn ?: '-' }}
            </td>
            <td>
                Lengkong, {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}<br>
                Guru BK / Konselor Sekolah,<br><br><br><br><br>
                <strong><u>{{ $session->counselor?->name ?: 'Konselor BK' }}</u></strong><br>
                NIP. {{ $session->counselor?->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

