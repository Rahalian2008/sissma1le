<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Panggilan Orang Tua - {{ $violation->student?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; margin-bottom: 25px; }
        .title h4 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; text-decoration: underline; }
        .title p { margin: 3px 0 0 0; font-size: 10.5pt; }
        .row { display: flex; margin-bottom: 6px; }
        .label { width: 180px; }
        .colon { width: 20px; text-align: center; }
        .value { flex: 1; font-weight: bold; }
        .schedule-box { border: 1px solid #000; padding: 10px 15px; margin: 15px 0; background-color: #f8fafc; border-radius: 4px; }
        .signatures { margin-top: 35px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Panggilan Orang Tua: ' . ($violation->student?->name ?? '')" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10.5pt;">
            <div>
                <div>Nomor : <strong>421.3 / {{ str_pad((string)$violation->id, 3, '0', STR_PAD_LEFT) }} / KO.01.03 - {{ $school->name ?? 'SMAN 1 Lengkong' }} / {{ \App\Services\LetterNumberService::toRoman((int)date('m')) }} / {{ date('Y') }}</strong></div>
            <div>Lampiran : -</div>
            <div>Perihal : <strong>Panggilan Orang Tua / Wali Siswa</strong></div>
        </div>
        <div>
            <div>Lengkong, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 10px;">
                Kepada Yth.<br>
                Bapak/Ibu Orang Tua / Wali dari:<br>
                <strong>{{ $violation->student?->name }}</strong><br>
                di Tempat
            </div>
        </div>
    </div>

    <p>Dengan hormat,</p>
    <p>Sehubungan dengan perlunya koordinasi dan konsultasi bersama demi kelancaran proses pembinaan pendidikan dan perkembangan peserta didik di sekolah, dengan ini kami mengharapkan kehadiran Bapak/Ibu Orang Tua/Wali pada:</p>

    <div class="schedule-box">
        <div class="row">
            <div class="label">Hari / Tanggal</div>
            <div class="colon">:</div>
            <div class="value">{{ $callDate }}</div>
        </div>
        <div class="row">
            <div class="label">Waktu</div>
            <div class="colon">:</div>
            <div class="value">{{ $callTime }}</div>
        </div>
        <div class="row">
            <div class="label">Tempat</div>
            <div class="colon">:</div>
            <div class="value">{{ $callRoom }}</div>
        </div>
        <div class="row">
            <div class="label">Bertemu dengan</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">Guru Bimbingan Konseling (BK) / Wali Kelas ({{ $violation->student?->current_rombel }})</div>
        </div>
        <div class="row">
            <div class="label">Agenda / Masalah</div>
            <div class="colon">:</div>
            <div class="value" style="color: #991b1b;">Konsultasi Kedisiplinan & Tata Tertib Siswa (Catatan: {{ $violation->violation_name }})</div>
        </div>
    </div>

    <p style="text-align: justify;">
        Mengingat pentingnya agenda pembinaan ini bagi masa depan peserta didik, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktu yang telah ditentukan dan tidak diwakilkan.
    </p>

    <p>Demikian surat panggilan ini kami sampaikan. Atas perhatian, kerja sama, dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>

    <div class="signatures">
        <div class="sig-block">
            Wali Kelas {{ $violation->student?->current_rombel ?? '' }},<br><br><br><br>
            <strong><u>{{ $waliKelas?->formatted_name ?? ($violation->officer_name ?: 'Wali Kelas / Guru BK') }}</u></strong><br>
            NIP. {{ $waliKelas?->nip ?? ($violation->officer_nip ?? '....................................') }}
        </div>
        <div class="sig-block">
            Mengetahui,<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '....................................' }}
        </div>
    </div>
    </div>
</body>
</html>
