<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Pulang Cepat - {{ $earlyLeave->student?->name ?? 'Siswa' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .no-print-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }
        .btn-print {
            background: #d97706;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #b45309; }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 6px;
        }
        .title-block {
            text-align: center;
            margin: 14px 0 18px 0;
        }
        .title-block h3 {
            margin: 0;
            font-size: 13.5pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 25%;
            font-weight: 500;
        }
        .meta-table td.colon {
            width: 2%;
            text-align: center;
        }
        .meta-table td.value {
            width: 73%;
        }
        .info-box {
            border: 1px solid #333;
            background: #fafafa;
            border-radius: 4px;
            padding: 10px 14px;
            margin: 14px 0;
        }
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 25%;
            padding: 0 4px;
            font-size: 10pt;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10.5pt;
        }
        .sig-title {
            font-size: 9pt;
            color: #222;
        }
        .watermark-valid {
            display: inline-block;
            border: 1.5px solid #059669;
            color: #059669;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .school-archive-alert {
            margin-top: 22px;
            padding: 10px 14px;
            border: 1.5px dashed #d97706;
            background: #fffbeb;
            font-size: 9.5pt;
            text-align: justify;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .pos-security-note {
            margin-top: 18px;
            padding: 8px 12px;
            border: 1px dashed #64748b;
            font-size: 8.5pt;
            background: #fff;
            page-break-inside: avoid;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; }
            .info-box { background: transparent !important; }
            .school-archive-alert { background: transparent !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toolbar Cetak (Non-Print) -->
        <div class="no-print-bar">
            <div>
                <strong>Dokumen Resmi Sekolah:</strong> Surat Izin Pulang Cepat Siswa ({{ $earlyLeave->student?->name ?? 'Siswa' }})
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-print">
                    🖨️ Cetak Dokumen (Print)
                </button>
                <button type="button" onclick="window.close()" class="btn-close">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Kop Surat Resmi dari Master Data Logo & Kop Sekolah -->
        <x-print-kop :school="$school" />

        <!-- Judul Surat (Tanpa Nomor Surat Sesuai Ketentuan) -->
        <div class="title-block">
            <h3>SURAT IZIN PULANG CEPAT</h3>
        </div>

        <!-- Paragraf Pembuka -->
        <p style="text-align: justify; margin-bottom: 12px; text-indent: 30px;">
            Menerangkan dengan sebenarnya bahwa siswa yang tercantum di bawah ini:
        </p>

        <!-- Data Siswa -->
        <table class="meta-table">
            <tr>
                <td class="label">Nama Lengkap Siswa</td>
                <td class="colon">:</td>
                <td class="value"><strong>{{ strtoupper($earlyLeave->student?->name ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa (NIS / NISN)</td>
                <td class="colon">:</td>
                <td class="value">{{ $earlyLeave->student?->nis ?? '-' }} / {{ $earlyLeave->student?->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Rombongan Belajar</td>
                <td class="colon">:</td>
                <td class="value">{{ $earlyLeave->student?->schoolClass?->name ?? '-' }} (Tingkat {{ $earlyLeave->student?->schoolClass?->grade ?? 'X/XI/XII' }})</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td class="value">{{ $earlyLeave->student?->gender === 'L' ? 'Laki-laki' : ($earlyLeave->student?->gender === 'P' ? 'Perempuan' : '-') }}</td>
            </tr>
        </table>

        <!-- Rincian Izin Pulang -->
        <p style="text-align: justify; margin-bottom: 8px;">
            Diberikan izin untuk <strong>meninggalkan lingkungan sekolah / pulang mendahului jadwal KBM berakhir</strong> pada:
        </p>

        <div class="info-box">
            <table class="meta-table" style="margin: 0;">
                <tr>
                    <td class="label" style="width: 28%;">Hari, Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>{{ \Carbon\Carbon::parse($earlyLeave->date)->translatedFormat('l, d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Jam Kepulangan</td>
                    <td class="colon">:</td>
                    <td class="value"><strong style="color: #b45309;">Pukul {{ $earlyLeave->leave_time }} WIB</strong></td>
                </tr>
                <tr>
                    <td class="label">Kategori Alasan</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>{{ $earlyLeave->reason_category }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Keterangan / Rincian</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $earlyLeave->reason }}</td>
                </tr>
                @if($earlyLeave->pickup_name)
                    <tr>
                        <td class="label">Nama Penjemput</td>
                        <td class="colon">:</td>
                        <td class="value">
                            <strong>{{ $earlyLeave->pickup_name }}</strong> 
                            ({{ $earlyLeave->pickup_relationship ?? 'Keluarga' }})
                            @if($earlyLeave->pickup_phone) &bull; No. Telp: {{ $earlyLeave->pickup_phone }} @endif
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Paragraf Penutup -->
        <p style="text-align: justify; margin-top: 12px; text-indent: 30px;">
            Demikian surat izin pulang cepat ini diberikan dengan sebenarnya untuk dipergunakan sebagaimana mestinya dan menjadi dokumen resmi sekolah.
        </p>

        <!-- Tanda Tangan 4 Pihak (Pemohon/Siswa, Orang Tua, Wali Kelas, Petugas Piket Hari Ini) -->
        <table class="signatures">
            <tr>
                <td>
                    Pemohon (Siswa),
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $earlyLeave->student?->name ?? 'Siswa' }}</div>
                    <div class="sig-title">NIS: {{ $earlyLeave->student?->nis ?? '-' }}</div>
                </td>
                <td>
                    Orang Tua / Wali Siswa,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $earlyLeave->pickup_name ?: ($earlyLeave->student?->parent_name ?: '( ................................... )') }}</div>
                    <div class="sig-title">{{ $earlyLeave->pickup_relationship ?: 'Orang Tua / Wali' }}</div>
                </td>
                <td>
                    Wali Kelas {{ $earlyLeave->student?->schoolClass?->name ?? '' }},
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $homeroomTeacher?->name ?? ($earlyLeave->student?->schoolClass?->homeroomTeacher?->name ?? '( ................................... )') }}</div>
                    <div class="sig-title">NIP/NUPTK: {{ $homeroomTeacher?->nip ?? ($earlyLeave->student?->schoolClass?->homeroomTeacher?->nip ?? '-') }}</div>
                </td>
                <td>
                    Lengkong, {{ \Carbon\Carbon::parse($earlyLeave->date)->translatedFormat('d F Y') }}<br>
                    Petugas Piket ({{ \Carbon\Carbon::parse($earlyLeave->date)->translatedFormat('l') }}),
                    <div class="sig-space">
                        @if($earlyLeave->status === 'APPROVED')
                            <div class="watermark-valid">
                                ✓ DIVERIFIKASI
                            </div>
                        @endif
                    </div>
                    @php
                        $resolvedPicketOfficerName = $picketOfficer?->person_name ?? ($picketOfficer?->name ?? ($earlyLeave->approver?->name ?? 'Petugas Piket'));
                        $resolvedPicketOfficerNip = $picketOfficer?->nip 
                            ?? $picketOfficer?->teacher?->nip 
                            ?? $picketOfficer?->teacher?->nuptk 
                            ?? $picketOfficer?->user?->teacher?->nip 
                            ?? $picketOfficer?->user?->teacher?->nuptk 
                            ?? $picketOfficer?->user?->nip 
                            ?? \App\Models\Teacher::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nip')
                            ?? \App\Models\Teacher::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nuptk')
                            ?? \App\Models\User::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nip')
                            ?? ($earlyLeave->approver?->teacher?->nip ?? ($earlyLeave->approver?->nip ?? '-'));
                    @endphp
                    <div class="sig-name">{{ $resolvedPicketOfficerName }}</div>
                    <div class="sig-title">NIP/NUPTK: {{ $resolvedPicketOfficerNip }}</div>
                </td>
            </tr>
        </table>

        <!-- Catatan Wajib Dikembalikan ke Sekolah sebagai Dokumen Sekolah -->
        <div class="school-archive-alert">
            <strong>📌 CATATAN PENTING ARSIP KETERTIBAN SEKOLAH:</strong><br>
            Surat izin pulang cepat ini <strong>wajib ditandatangani oleh Orang Tua/Wali Siswa</strong> dan <strong>dikembalikan ke sekolah (Petugas Piket / Wali Kelas)</strong> pada hari berikutnya sebagai dokumen arsip resmi sekolah.
        </div>

        <!-- Lembar Periksa Pos Keamanan (Satpam) -->
        <div class="pos-security-note">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 75%; vertical-align: top;">
                        <strong>PEMERIKSAAN POS SATPAM GERBANG:</strong><br>
                        1. Petugas keamanan memeriksa surat sebelum membuka gerbang.<br>
                        2. Verifikasi jam keluar dan pihak penjemput.<br>
                        3. Kode Verifikasi: <code>{{ $earlyLeave->qr_code_token ?? substr(md5($earlyLeave->id), 0, 12) }}</code>
                    </td>
                    <td style="width: 25%; text-align: center; border-left: 1px dashed #94a3b8; padding-left: 10px;">
                        Paraf Satpam / Petugas Jaga:<br><br><br>
                        ( .................................... )
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
