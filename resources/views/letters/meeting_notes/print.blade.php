<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notula Rapat Dinas - {{ $meetingNote->meeting_number }}</title>
    <style>
        @page { size: portrait; margin: 15mm 15mm 15mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 11.5pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .doc-title p { margin: 3px 0; font-size: 10pt; font-family: monospace; }
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta-table td { padding: 4px 6px; font-size: 10.5pt; vertical-align: top; }
        .section-title { font-weight: bold; text-decoration: underline; margin-top: 15px; margin-bottom: 6px; font-size: 11pt; }
        .content-box { text-align: justify; white-space: pre-line; line-height: 1.5; font-size: 10.5pt; }
        .signatures { margin-top: 35px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 280px; text-align: center; font-size: 10.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Notula Rapat: ' . $meetingNote->meeting_title" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15mm 20mm; max-width: 215mm; margin: 0 auto;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>NOTULA / RISALAH RAPAT KEDINASAN</h4>
        <p>Nomor: {{ $meetingNote->meeting_number }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 140px;">Hari / Tanggal</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ $meetingNote->meeting_date?->translatedFormat('l, d F Y') }}</strong></td>
        </tr>
        <tr>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td>{{ $meetingNote->start_time ?: '08:30' }} s.d. {{ $meetingNote->end_time ?: 'Selesai' }} WIB</td>
        </tr>
        <tr>
            <td>Tempat Pelaksanaan</td>
            <td>:</td>
            <td>{{ $meetingNote->location }}</td>
        </tr>
        <tr>
            <td>Agenda / Acara</td>
            <td>:</td>
            <td><strong>{{ $meetingNote->meeting_title }}</strong> ({{ $meetingNote->meeting_type }})</td>
        </tr>
        <tr>
            <td>Pimpinan Rapat</td>
            <td>:</td>
            <td>{{ $meetingNote->chairperson_name }}</td>
        </tr>
        <tr>
            <td>Notulis</td>
            <td>:</td>
            <td>{{ $meetingNote->secretary_name }}</td>
        </tr>
        <tr>
            <td>Jumlah Peserta Hadir</td>
            <td>:</td>
            <td>{{ $meetingNote->attendees_count }} Orang (Daftar Hadir Terlampir)</td>
        </tr>
    </table>

    <div class="section-title">I. SUSUNAN ACARA & PEMBUKAAN</div>
    <div class="content-box">
        {!! nl2br(e($meetingNote->agenda_topics ?: '1. Pembukaan oleh Pembawa Acara\n2. Pengarahan dan Sambutan Kepala Sekolah\n3. Pembahasan Inti Agenda Rapat\n4. Tanya Jawab, Musyawarah dan Perumusan Keputusan\n5. Doa dan Penutup')) !!}
    </div>

    <div class="section-title">II. RINGKASAN PEMBAHASAN & JALANNYA RAPAT</div>
    <div class="content-box">
        {!! nl2br(e($meetingNote->discussion_summary ?: 'Rapat dibuka secara resmi oleh Pimpinan Rapat. Seluruh peserta rapat mendengarkan pengarahan pokok serta aktif memberikan masukan dan tanggapan teknis terkait optimalisasi program kerja sekolah.')) !!}
    </div>

    <div class="section-title">III. KESIMPULAN & KEPUTUSAN HASIL RAPAT</div>
    <div class="content-box">
        {!! nl2br(e($meetingNote->decisions_and_follow_up)) !!}
    </div>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Notulis Rapat,</p>
            <br><br><br>
            <p><strong><u>{{ $meetingNote->secretary_name }}</u></strong></p>
        </div>
        <div class="sig-box float-right">
            <p>{{ $school->district ?: 'Lengkong' }}, {{ $meetingNote->meeting_date?->translatedFormat('d F Y') }}<br>Pimpinan Rapat / Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $meetingNote->chairperson_name }}</u></strong><br>
            NIP. {{ $school->headmaster_nip }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>
