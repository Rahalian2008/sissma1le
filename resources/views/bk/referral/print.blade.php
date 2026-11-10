<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar Alih Tangan Kasus - {{ $referral->referral_code }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #111; line-height: 1.35; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .kop h3 { margin: 2px 0; font-size: 11.5pt; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 2px 0; font-size: 8.5pt; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 3px; font-size: 11.5pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 10pt; margin-bottom: 15px; font-family: monospace; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10pt; }
        .meta-table td { padding: 3px 4px; vertical-align: top; }
        .section-title { font-weight: bold; font-size: 10.5pt; margin-top: 12px; margin-bottom: 3px; }
        .content-box { font-size: 10pt; margin-left: 12px; line-height: 1.4; text-align: justify; }
        .ttd-container { margin-top: 35px; display: flex; justify-content: space-between; font-size: 10pt; }
        .ttd-box { width: 230px; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <x-print-kop :school="$school" />

    <div class="title" style="margin-top: 14px;">SURAT PENGANTAR ALIH TANGAN KASUS (REFERRAL)</div>
    <div class="subtitle">Nomor: {{ $referral->referral_code }}</div>

    <table style="width: 100%; margin-bottom: 12px; font-size: 10pt;">
        <tr>
            <td width="15%">Kepada Yth.</td>
            <td width="2%">:</td>
            <td><strong>{{ $referral->target_expert_name }}</strong></td>
        </tr>
        <tr>
            <td>Instansi / Unit</td>
            <td>:</td>
            <td>{{ $referral->target_institution }} ({{ str_replace('_', ' ', $referral->expert_category) }})</td>
        </tr>
        <tr>
            <td>Di Tempat</td>
            <td>:</td>
            <td>-</td>
        </tr>
    </table>

    <p style="text-align: justify; margin-bottom: 10px;">Dengan hormat,<br>
    Sehubungan dengan layanan Bimbingan dan Konseling di SMA Negeri 1 Lengkong, bersama ini kami menghadapkan peserta didik kami yang memerlukan penanganan profesional lanjutan di luar kewenangan layanan bimbingan sekolah:</p>

    <table class="meta-table" style="background-color: #fafafa; border: 1px solid #ddd; padding: 6px;">
        <tr>
            <td width="24%"><strong>Nama Peserta Didik</strong></td>
            <td width="2%">:</td>
            <td width="74%"><strong>{{ $referral->student?->name }}</strong></td>
        </tr>
        <tr>
            <td><strong>Nomor Induk / NISN</strong></td>
            <td>:</td>
            <td>{{ $referral->student?->nis ?? '-' }} / {{ $referral->student?->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Kelas / Rombel</strong></td>
            <td>:</td>
            <td>{{ $referral->student?->schoolClass?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Kelamin / Usia</strong></td>
            <td>:</td>
            <td>{{ $referral->student?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
    </table>

    <div class="section-title">1. Gambaran / Gejala Permasalahan yang Dialami:</div>
    <div class="content-box">
        {!! nl2br(e($referral->problem_description)) !!}
    </div>

    <div class="section-title">2. Upaya Bimbingan / Intervensi yang Telah Dilakukan Sekolah:</div>
    <div class="content-box">
        {!! nl2br(e($referral->previous_interventions)) !!}
    </div>

    <div class="section-title">3. Alasan Alih Tangan Kasus (Indikasi Rujukan):</div>
    <div class="content-box">
        {!! nl2br(e($referral->referral_reason)) !!}
    </div>

    <div class="section-title">4. Harapan Bantuan / Rekomendasi yang Dimohonkan:</div>
    <div class="content-box">
        {!! nl2br(e($referral->expected_support)) !!}
    </div>

    <p style="text-align: justify; margin-top: 15px;">Demikian surat pengantar alih tangan kasus ini kami sampaikan dengan harapan ananda mendapatkan bimbingan dan terapi yang optimal. Kami sangat berterima kasih atas kerjasama dan bantuan profesional yang diberikan.</p>

    <div class="ttd-container" style="display: flex; justify-content: space-between;">
        <div class="ttd-box" style="text-align: left;">
            <p>Mengetahui,<br>Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? 'Pian Sopian, S.Pd' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '-' }}</p>
        </div>
        <div class="ttd-box" style="text-align: right;">
            <p>Lengkong, {{ $referral->referral_date?->translatedFormat('d F Y') }}<br>Guru Bimbingan Konseling,</p>
            <br><br><br>
            <p><strong><u>{{ $referral->counselor?->name ?? 'Guru BK' }}</u></strong><br>
            NIP. {{ $referral->counselor?->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>

