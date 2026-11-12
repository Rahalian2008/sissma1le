<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Keluar Lingkungan Sekolah (Sementara) - {{ $firstStudent?->name ?? 'Siswa' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 14mm 6mm 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.32;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 8px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .container {
            max-width: 780px;
            margin: 0 auto;
        }
        .no-print-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
        }
        .btn-print {
            background: #0d9488;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }
        .btn-print:hover { background: #0f766e; }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 6px;
            font-size: 12px;
        }
        .title-block {
            text-align: center;
            margin: 6px 0 8px 0;
        }
        .title-block h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .table-students {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 6px 0;
            font-size: 9pt;
        }
        .table-students th, .table-students td {
            border: 1px solid #333;
            padding: 2.5px 6px;
        }
        .table-students th {
            background: #f8fafc;
            font-weight: bold;
            text-align: center;
        }
        .info-box {
            border: 1px solid #444;
            background: #fafafa;
            border-radius: 4px;
            padding: 5px 8px;
            margin: 5px 0;
            font-size: 9.5pt;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 28%;
            font-weight: 500;
        }
        .meta-table td.colon {
            width: 2%;
            text-align: center;
        }
        .meta-table td.value {
            width: 70%;
        }
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 4px;
            font-size: 9pt;
            line-height: 1.25;
        }
        .sig-space {
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9.5pt;
            line-height: 1.2;
        }
        .sig-title {
            font-size: 8.5pt;
            color: #222;
            line-height: 1.2;
        }
        .watermark-valid {
            display: inline-block;
            border: 1px solid #059669;
            color: #059669;
            padding: 1px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pos-security-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 8pt;
            line-height: 1.2;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .pos-security-table th, .pos-security-table td {
            border: 1px dashed #64748b;
            padding: 3px 6px;
            text-align: center;
        }
        .pos-security-table th {
            background: #f1f5f9;
            font-weight: bold;
        }

        /* STRICT 1-PAGE PRINT FORMAT */
        @media print {
            .no-print-bar { display: none !important; }
            html, body {
                height: 100% !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            .header-kop-banner {
                margin-bottom: 4px !important;
                padding-bottom: 2px !important;
            }
            .header-kop-banner img {
                max-height: 80px !important;
                height: auto !important;
            }
            .info-box { background: transparent !important; }
            .table-students th { background: transparent !important; }
            .pos-security-table th { background: transparent !important; }
            * {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toolbar Cetak (Non-Print) -->
        <div class="no-print-bar">
            <div>
                <strong>Dokumen Resmi Sekolah:</strong> Surat Izin Keluar Lingkungan Sekolah (Sementara)
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-print">
                    🖨️ Cetak Surat Izin (1 Lembar A4)
                </button>
                <button type="button" onclick="window.close()" class="btn-close">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Kop Surat Resmi Sekolah -->
        <x-print-kop :school="$school" />

        <!-- Judul Dokumen (Tanpa Nomor Surat Sesuai Ketentuan) -->
        <div class="title-block">
            <h3>SURAT IZIN KELUAR LINGKUNGAN SEKOLAH (SEMENTARA)</h3>
        </div>

        <!-- Paragraf Pembuka -->
        <p style="text-align: justify; margin: 0 0 3px 0; text-indent: 24px; font-size: 9.5pt;">
            Dengan ini memberikan izin meninggalkan lingkungan sekolah untuk sementara waktu kepada siswa-siswi berikut:
        </p>

        <!-- Tabel Siswa yang Diberi Izin (Mendukung > 1 Siswa / Delegasi Rombongan) -->
        <table class="table-students">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 38%;">Nama Lengkap Siswa</th>
                    <th style="width: 22%;">NIS / NISN</th>
                    <th style="width: 24%;">Kelas / Rombel</th>
                    <th style="width: 10%;">L/P</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permit->students as $idx => $st)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td><strong>{{ strtoupper($st->name) }}</strong></td>
                        <td style="text-align: center;">{{ $st->nis ?: '-' }} / {{ $st->nisn ?: '-' }}</td>
                        <td style="text-align: center;">{{ $st->schoolClass?->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $st->gender === 'L' ? 'L' : ($st->gender === 'P' ? 'P' : '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Rincian Izin Keluar Lingkungan Sekolah -->
        <p style="text-align: justify; margin: 3px 0 2px 0; font-size: 9.5pt;">
            Untuk melaksanakan keperluan di luar lingkungan sekolah dengan ketentuan sebagai berikut:
        </p>

        <div class="info-box">
            <table class="meta-table" style="margin: 0;">
                <tr>
                    <td class="label">Hari, Tanggal Izin</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>{{ \Carbon\Carbon::parse($permit->date)->translatedFormat('l, d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Waktu Keluar Sekolah</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>Pukul {{ $permit->out_time }} WIB</strong></td>
                </tr>
                <tr>
                    <td class="label">Estimasi Waktu Kembali</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if($permit->expected_back_time)
                            <strong>Pukul {{ $permit->expected_back_time }} WIB</strong>
                        @else
                            <em>Setelah keperluan selesai</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Keperluan / Tujuan</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>{{ $permit->purpose }}</strong></td>
                </tr>
                @if($permit->destination)
                    <tr>
                        <td class="label">Tempat / Lokasi Tujuan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $permit->destination }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Alasan / Rincian Urusan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $permit->reason }}</td>
                </tr>
                @if($permit->accompanied_by)
                    <tr>
                        <td class="label">Didampingi Oleh</td>
                        <td class="colon">:</td>
                        <td class="value"><strong>{{ $permit->accompanied_by }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Ketentuan Kepulangan</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if($permit->is_returning)
                            <span style="color: #047857; font-weight: bold;">[YA] Siswa wajib melapor kembali ke Pos Keamanan & Petugas Piket setibanya di sekolah.</span>
                        @else
                            <span>Siswa diizinkan tidak kembali ke sekolah (langsung pulang ke rumah atas dispensasi resmi).</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Ketentuan & Tata Tertib Siswa -->
        <div style="font-size: 8pt; color: #333; margin: 4px 0; line-height: 1.25;">
            <strong>Ketentuan Bagi Siswa:</strong>
            <ol style="margin: 1px 0 0 14px; padding: 0;">
                <li>Siswa wajib mengenakan seragam rapi dan menjaga nama baik SMAN 1 Lengkong selama berada di luar.</li>
                <li>Surat izin ini wajib ditunjukkan kepada Satpam di pintu gerbang utama saat keluar dan saat kembali masuk sekolah.</li>
                <li>Tidak dibenarkan singgah ke tempat lain di luar lokasi tujuan yang telah disetujui.</li>
            </ol>
        </div>

        <!-- Tanda Tangan 3 Pihak (Pemohon/Siswa, Wali Kelas, Petugas Piket Hari Itu) -->
        <table class="signatures">
            <tr>
                <td>
                    Pemohon (Siswa),<br>
                    <span style="font-size: 7.5pt; color: #475569;">(Perwakilan Pemohon)</span>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $firstStudent?->name ?? 'Perwakilan Siswa' }}</div>
                    <div class="sig-title">NIS: {{ $firstStudent?->nis ?? '-' }}</div>
                </td>
                <td>
                    Wali Kelas {{ $firstStudent?->schoolClass?->name ?? '' }},<br>
                    <span style="font-size: 7.5pt; color: #475569;">Kelas Siswa Bersangkutan</span>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $homeroomTeacher?->name ?? ($firstStudent?->schoolClass?->homeroomTeacher?->name ?? '( ................................... )') }}</div>
                    <div class="sig-title">NIP/NUPTK: {{ $homeroomTeacher?->nip ?? ($firstStudent?->schoolClass?->homeroomTeacher?->nip ?? '-') }}</div>
                </td>
                <td>
                    Lengkong, {{ \Carbon\Carbon::parse($permit->date)->translatedFormat('d F Y') }}<br>
                    Petugas Piket ({{ \Carbon\Carbon::parse($permit->date)->translatedFormat('l') }}),
                    <div class="sig-space">
                        @if($permit->status !== 'PENDING')
                            <div class="watermark-valid">✓ RESMI TERVERIFIKASI</div>
                        @endif
                    </div>
                    @php
                        $resolvedPicketOfficerName = $picketOfficer?->person_name ?? ($picketOfficer?->name ?? ($permit->approver?->name ?? 'Petugas Piket'));
                        $resolvedPicketOfficerNip = $picketOfficer?->nip 
                            ?? $picketOfficer?->teacher?->nip 
                            ?? $picketOfficer?->teacher?->nuptk 
                            ?? $picketOfficer?->user?->teacher?->nip 
                            ?? $picketOfficer?->user?->teacher?->nuptk 
                            ?? $picketOfficer?->user?->nip 
                            ?? \App\Models\Teacher::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nip')
                            ?? \App\Models\Teacher::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nuptk')
                            ?? \App\Models\User::where('name', $resolvedPicketOfficerName)->orWhere('name', 'like', "%{$resolvedPicketOfficerName}%")->value('nip')
                            ?? ($permit->approver?->teacher?->nip ?? ($permit->approver?->nip ?? '-'));
                    @endphp
                    <div class="sig-name">{{ $resolvedPicketOfficerName }}</div>
                    <div class="sig-title">NIP/NUPTK: {{ $resolvedPicketOfficerNip }}</div>
                </td>
            </tr>
        </table>

        <!-- Lembar Kontrol Pos Keamanan / Satpam Gerbang -->
        <table class="pos-security-table">
            <thead>
                <tr>
                    <th colspan="5" style="text-align: left; background: #e2e8f0; font-size: 7.5pt; padding: 2px 5px;">
                        <strong>LEMBAR KONTROL POS PENJAGAAN SATPAM / GERBANG SEKOLAH:</strong>
                    </th>
                </tr>
                <tr>
                    <th style="width: 20%;">Jam Keluar Gerbang</th>
                    <th style="width: 25%;">Paraf Satpam Keluar</th>
                    <th style="width: 20%;">Jam Masuk Kembali</th>
                    <th style="width: 25%;">Paraf Satpam Masuk</th>
                    <th style="width: 10%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 6px 4px;">
                        {{ $permit->out_time }} WIB
                    </td>
                    <td>( .......................... )</td>
                    <td>
                        @if($permit->actual_back_time)
                            <strong>{{ $permit->actual_back_time }} WIB</strong>
                        @else
                            ..... : ..... WIB
                        @endif
                    </td>
                    <td>( .......................... )</td>
                    <td style="font-size: 7pt; color: #475569;">
                        {{ $permit->actual_back_time ? 'Telah Kembali' : 'Di Luar' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
