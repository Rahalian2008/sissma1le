<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pertemuan Orang Tua - {{ $parentCall->call_code }}</title>
    <style>
        @page {
            size: {{ request('paper', 'a4') === 'f4' ? '215mm 330mm' : 'A4' }};
            margin: 15mm;
        }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 15mm; }
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
        .signature-table { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 33.3%; text-align: center; vertical-align: top; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <x-print-paper-toolbar :defaultPaper="request('paper', 'a4')" />

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>BERITA ACARA PERTEMUAN DENGAN ORANG TUA / WALI SISWA</h4>
        <p>Rujukan Surat Panggilan: {{ $parentCall->letter_number ?: $parentCall->call_code }}</p>
    </div>

    <p>Pada hari ini, <strong>{{ \Carbon\Carbon::parse($parentCall->call_date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($parentCall->call_date)->translatedFormat('d F Y') }}</strong> bertempat di {{ $parentCall->place ?: ('Ruang Bimbingan Konseling ' . ($school?->name ?: 'SMA Negeri 1 Lengkong')) }}, telah dilaksanakan pertemuan tatap muka musyawarah pembinaan antara pihak sekolah dan orang tua/wali murid dari:</p>

    <!-- Data Siswa -->
    <table class="meta">
        <tr>
            <td style="width: 28%;">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 70%; font-weight: bold;">{{ $parentCall->student?->name }}</td>
        </tr>
        <tr>
            <td>NISN / NIS</td>
            <td>:</td>
            <td>{{ $parentCall->student?->nisn ?: '-' }} / {{ $parentCall->student?->nis ?: '-' }}</td>
        </tr>
        <tr>
            <td>Kelas / Rombel</td>
            <td>:</td>
            <td>{{ $parentCall->student?->schoolClass?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Nama Orang Tua / Wali</td>
            <td>:</td>
            <td>....................................................................................................</td>
        </tr>
        <tr>
            <td>Hal Permasalahan</td>
            <td>:</td>
            <td>{{ $parentCall->reason }}</td>
        </tr>
    </table>

    <!-- Hasil Musyawarah -->
    <div class="section-box">
        <h5>1. Notula Pembahasan Masalah & Tanggapan Orang Tua</h5>
        <p style="margin: 0; text-align: justify;">{{ $parentCall->meeting_notes ?: 'Pihak sekolah telah menyampaikan evaluasi perkembangan dan permasalahan peserta didik. Orang tua/wali menyimak dengan saksama dan memahami arahan pembinaan yang diberikan demi kebaikan pendidikan ananda.' }}</p>
    </div>

    <!-- Pernyataan & Kesepakatan -->
    <div class="section-box">
        <h5>2. Surat Pernyataan Kesanggupan & Komitmen Bersama</h5>
        <p style="margin: 0; text-align: justify;">
            {{ $parentCall->parent_agreement ?: ('Orang tua/wali murid bersedia membimbing, mengawasi, serta memastikan ananda hadir tepat waktu dan mematuhi seluruh peraturan tata tertib ' . ($school?->name ?: 'SMA Negeri 1 Lengkong') . '. Apabila di kemudian hari ananda mengulangi pelanggaran yang sama, orang tua dan siswa bersedia menerima sanksi sesuai ketentuan sekolah yang berlaku.') }}
        </p>
    </div>

    <p style="margin-top: 15px;">Demikian Berita Acara dan Kesepakatan ini dibuat dalam keadaan sadar tanpa paksaan dari pihak manapun untuk dipergunakan sebagaimana mestinya.</p>

    <!-- Tanda Tangan 3 Pihak -->
    <table class="signature-table">
        <tr>
            <td>
                Peserta Didik,<br><br><br><br><br>
                <strong><u>{{ $parentCall->student?->name }}</u></strong><br>
                NISN. {{ $parentCall->student?->nisn ?: '-' }}
            </td>
            <td>
                Orang Tua / Wali Murid,<br><br><br><br><br>
                ( .................................................. )<br>
                Tanda Tangan & Nama Terang
            </td>
            <td>
                Guru BK / Konselor,<br><br><br><br><br>
                <strong><u>{{ $parentCall->counselor?->name ?: 'Konselor BK' }}</u></strong><br>
                NIP. {{ $parentCall->counselor?->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

