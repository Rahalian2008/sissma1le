<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Cuti - {{ $leave->employee?->formatted_name }}</title>
    <style>
        @page { size: portrait; margin: 18mm 18mm 18mm 18mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 14px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .title-box { text-align: center; margin-bottom: 15px; }
        .title-box h4 { margin: 0; font-size: 12pt; text-decoration: underline; text-transform: uppercase; }
        .title-box p { margin: 2px 0; font-size: 10pt; }
        .content-table { width: 100%; border: none; margin: 8px 0 14px 15px; }
        .content-table td { border: none; padding: 2.5px 0; vertical-align: top; font-size: 10.5pt; }
        .signature-grid { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .signature-cell { width: 50%; float: left; text-align: center; font-size: 10pt; }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Izin Cuti: ' . ($leave->employee?->formatted_name ?? '')" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15mm 20mm; max-width: 215mm; margin: 0 auto;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="title-box">
        <h4>SURAT IZIN {{ strtoupper($leave->leave_type) }}</h4>
        <p>Nomor: {{ $leave->letter_number }}</p>
    </div>

    <p style="text-align: justify; text-indent: 30px;">
        Diberikan izin kepada Pegawai Negeri Sipil / Pegawai Pemerintah dengan Perjanjian Kerja di lingkungan {{ $school->name }}:
    </p>

    <table class="content-table">
        <tr>
            <td style="width: 200px;">Nama Pegawai</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ $leave->employee?->formatted_name }}</strong></td>
        </tr>
        <tr>
            <td>NIP / NI PPPK</td>
            <td>:</td>
            <td>{{ $leave->employee?->nip ?: ($leave->employee?->ni_pppk ?: '-') }}</td>
        </tr>
        <tr>
            <td>Pangkat / Golongan Ruang</td>
            <td>:</td>
            <td>{{ $leave->employee?->rank_group ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan Dinas</td>
            <td>:</td>
            <td>{{ $leave->employee?->position ?? '-' }}</td>
        </tr>
        <tr>
            <td>Unit Kerja</td>
            <td>:</td>
            <td>{{ $school->name }}</td>
        </tr>
        <tr>
            <td>Jenis Cuti Diberikan</td>
            <td>:</td>
            <td><strong>{{ $leave->leave_type }}</strong></td>
        </tr>
        <tr>
            <td>Lama Waktu Cuti</td>
            <td>:</td>
            <td><strong>{{ $leave->days_count }} ({{ \Illuminate\Support\Number::spell($leave->days_count, 'id') }}) hari kerja</strong></td>
        </tr>
        <tr>
            <td>Tanggal Pelaksanaan</td>
            <td>:</td>
            <td>Mulai tanggal <strong>{{ $leave->start_date->translatedFormat('d F Y') }}</strong> sampai dengan <strong>{{ $leave->end_date->translatedFormat('d F Y') }}</strong></td>
        </tr>
        <tr>
            <td>Alasan Permohonan Cuti</td>
            <td>:</td>
            <td>{{ $leave->reason }}</td>
        </tr>
        <tr>
            <td>Alamat Selama Cuti</td>
            <td>:</td>
            <td>{{ $leave->address_during_leave ?: '-' }}</td>
        </tr>
        @if($leave->replacementEmployee)
        <tr>
            <td>Pelaksana Tugas Harian (Pengganti)</td>
            <td>:</td>
            <td>{{ $leave->replacementEmployee->formatted_name }} ({{ $leave->replacementEmployee->position }})</td>
        </tr>
        @endif
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Selama menjalankan cuti, yang bersangkutan berhak menerima penghasilan sesuai peraturan perundang-undangan yang berlaku. Setelah masa cuti selesai, wajib melaporkan diri kembali kepada pimpinan unit kerja untuk melaksanakan tugas sebagaimana mestinya.
    </p>

    <p style="text-align: justify; text-indent: 30px;">
        Demikian Surat Izin Cuti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="signature-grid">
        <div class="signature-cell">
            <p>Mengetahui / Mengajukan,<br>Pegawai yang bersangkutan,</p>
            <br><br><br>
            <p><strong><u>{{ $leave->employee?->formatted_name }}</u></strong><br>
            NIP. {{ $leave->employee?->nip ?: ($leave->employee?->ni_pppk ?: '-') }}</p>
        </div>
        <div class="signature-cell">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ $leave->created_at->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            Pembina Tk. I, IV/b<br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>
    </div>
</body>
</html>
