<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Konferensi Kasus - {{ $caseConference->conference_code }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #111; line-height: 1.3; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 12px; }
        .kop h2 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .kop h3 { margin: 1px 0; font-size: 11pt; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 1px 0; font-size: 8.5pt; }
        .title { text-align: center; font-weight: bold; font-size: 11pt; text-transform: uppercase; margin-bottom: 3px; }
        .subtitle { text-align: center; font-size: 9.5pt; margin-bottom: 12px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9.5pt; }
        .meta-table td { padding: 3px 5px; vertical-align: top; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 9.5pt; margin-top: 10px; margin-bottom: 3px; background-color: #f2f2f2; padding: 2px 5px; border-left: 3px solid #000; }
        .content-box { font-size: 9.5pt; margin-left: 8px; line-height: 1.35; }
        .content-box p { margin: 3px 0; }
        table.sign-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9pt; }
        table.sign-table th, table.sign-table td { border: 1px solid #000; padding: 4px 6px; }
        table.sign-table th { background-color: #f7f7f7; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <x-print-kop :school="$school" />

    <div class="title" style="margin-top: 14px;">BERITA ACARA SIDANG KONFERENSI KASUS (CASE CONFERENCE)</div>
    <div class="subtitle">Nomor Registrasi: {{ $caseConference->conference_code }}</div>

    <p style="margin-bottom: 8px;">Pada hari ini, <strong>{{ $caseConference->conference_date?->translatedFormat('l') }}</strong> tanggal <strong>{{ $caseConference->conference_date?->translatedFormat('d F Y') }}</strong> bertempat di {{ $caseConference->place }}, telah diselenggarakan Sidang Konferensi Kasus Terpadu Bimbingan dan Konseling:</p>

    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Nama Siswa</strong></td>
            <td width="2%">:</td>
            <td width="40%"><strong>{{ $caseConference->student?->name }}</strong></td>
            <td width="15%"><strong>NIS / NISN</strong></td>
            <td width="2%">:</td>
            <td width="21%">{{ $caseConference->student?->nis ?? '-' }} / {{ $caseConference->student?->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Kelas / Rombel</strong></td>
            <td>:</td>
            <td>{{ $caseConference->student?->schoolClass?->name ?? '-' }}</td>
            <td><strong>Pimpinan Sidang</strong></td>
            <td>:</td>
            <td>{{ $caseConference->leader_name }}</td>
        </tr>
        <tr>
            <td><strong>Topik Kasus</strong></td>
            <td>:</td>
            <td colspan="4"><strong>{{ $caseConference->case_title }}</strong></td>
        </tr>
    </table>

    <div class="section-title">A. LATAR BELAKANG KASUS</div>
    <div class="content-box">
        {!! nl2br(e($caseConference->case_background)) !!}
    </div>

    <div class="section-title">B. FAKTA DAN TEMUAN DATA</div>
    <div class="content-box">
        {!! nl2br(e($caseConference->facts_and_findings)) !!}
    </div>

    <div class="section-title">C. KEPUTUSAN BERSAMA SIDANG MUSYAWARAH</div>
    <div class="content-box">
        {!! nl2br(e($caseConference->agreed_decisions)) !!}
    </div>

    <div class="section-title">D. KOMITMEN TINDAK LANJUT</div>
    <div class="content-box">
        {!! nl2br(e($caseConference->follow_up_commitments)) !!}
    </div>

    <div class="section-title">E. DAFTAR HADIR DAN TANDA TANGAN PESERTA SIDANG</div>
    <table class="sign-table">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="34%">Nama Lengkap</th>
                <th width="30%">Jabatan / Hubungan</th>
                <th width="30%">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($caseConference->attendees ?? [] as $idx => $att)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $att['name'] ?? '-' }}</strong></td>
                    <td>{{ $att['role'] ?? '-' }}</td>
                    <td style="height: 35px; vertical-align: bottom; text-align: right; padding-right: 15px;">
                        <span style="color: #999; font-size: 8pt;">{{ $idx + 1 }}. ......................</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 25px; font-size: 9.5pt; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top; text-align: left;">
                <p>Mengetahui,<br>Kepala Sekolah,</p>
                <br><br><br>
                <p><strong><u>{{ $school->headmaster_name ?? 'Pian Sopian, S.Pd' }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? '-' }}</p>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <p>Lengkong, {{ $caseConference->conference_date?->translatedFormat('d F Y') }}<br>Koordinator BK / Pimpinan Sidang,</p>
                <br><br><br>
                <p><strong><u>{{ $caseConference->leader_name ?? 'Guru BK' }}</u></strong><br>
                NIP/NUPTK: -</p>
            </td>
        </tr>
    </table>
</body>
</html>

