<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku e-Rapor Lengkap - {{ $student->name }} - SMAN 1 Lengkong</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
            break-after: page;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999; display: flex; gap: 8px; background: rgba(255,255,255,0.9); padding: 8px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Semua Halaman (1 Paket Buku Rapor)
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <!-- 1. HALAMAN SAMPUL -->
    <div style="display: flex; justify-content: center; align-items: center; min-height: 250mm;" class="page-break">
        <div style="width: 170mm; border: 3px double #000; padding: 15mm 10mm; box-sizing: border-box; text-align: center;">
            <h1 style="font-size: 20pt; font-weight: bold; margin: 0 0 5px 0;">RAPOR PESERTA DIDIK</h1>
            <h2 style="font-size: 16pt; font-weight: bold; margin: 0 0 5px 0;">SEKOLAH MENENGAH ATAS (SMA)</h2>
            <h3 style="margin-top: 10px; font-size: 11pt; font-weight: normal;">KURIKULUM NASIONAL / KURIKULUM MERDEKA</h3>

            <div style="margin: 25mm 0;">
                <img src="{{ asset('images/logo-tutwuri.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" style="width: 125px; height: auto;">
            </div>

            <p style="font-size: 12pt; margin: 4px 0;">Nama Peserta Didik:</p>
            <div style="border: 1px solid #000; padding: 8px 15px; display: inline-block; min-width: 90mm; font-size: 14pt; font-weight: bold;">
                {{ strtoupper($student->name) }}
            </div>

            <p style="margin-top: 15px; font-size: 12pt;">Nomor Induk Siswa Nasional (NISN):</p>
            <div style="font-size: 13pt; font-weight: bold; font-family: monospace;">
                {{ $student->nisn ?? $student->nis ?? '-' }}
            </div>

            <div style="margin-top: 25mm;">
                <h3 style="font-size: 13pt; font-weight: bold; margin: 3px 0;">KEMENTERIAN PENDIDIKAN DASAR DAN MENENGAH</h3>
                <h3 style="font-size: 13pt; font-weight: bold; margin: 3px 0;">REPUBLIK INDONESIA</h3>
                <p style="margin-top: 8px; font-weight: bold;">SMA NEGERI 1 LENGKONG</p>
                <p style="margin: 2px 0;">KABUPATEN SUKABUMI - JAWA BARAT</p>
            </div>
        </div>
    </div>

    <!-- 2. HALAMAN IDENTITAS -->
    <div style="padding: 10mm 5mm;" class="page-break">
        <h2 style="text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 25px; text-transform: uppercase;">
            KETERANGAN TENTANG DIRI PESERTA DIDIK
        </h2>

        <table style="width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 25px;">
            <tr>
                <td style="width: 25px; vertical-align: top;">1.</td>
                <td style="width: 220px; vertical-align: top;">Nama Peserta Didik</td>
                <td style="width: 15px; vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;"><strong>{{ strtoupper($student->name) }}</strong></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">2.</td>
                <td style="vertical-align: top;">Nomor Induk / NISN</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">3.</td>
                <td style="vertical-align: top;">Tempat, Tanggal Lahir</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->birth_place ?? 'Sukabumi' }}, {{ $student->birth_date ? Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">4.</td>
                <td style="vertical-align: top;">Jenis Kelamin</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->gender === 'L' || $student->gender === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">5.</td>
                <td style="vertical-align: top;">Agama</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->religion ?? 'Islam' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">6.</td>
                <td style="vertical-align: top;">Alamat Peserta Didik</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->address ?? 'Kec. Lengkong, Kab. Sukabumi' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">7.</td>
                <td style="vertical-align: top;">Sekolah Asal (SMP/MTs)</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">{{ $student->previous_school ?? 'SMP Negeri Lengkong' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">8.</td>
                <td style="vertical-align: top;">Nama Orang Tua</td>
                <td style="vertical-align: top; text-align: center;">:</td>
                <td style="vertical-align: top;">Ayah: {{ $student->father_name ?? '-' }} | Ibu: {{ $student->mother_name ?? '-' }}</td>
            </tr>
        </table>

        <!-- TTD KEPALA SEKOLAH DI KIRI -->
        <table style="width: 100%; margin-top: 35px;">
            <tr>
                <td style="width: 60%; text-align: left; vertical-align: top;">
                    <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 3px 0 65px 0;">Kepala Sekolah,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                    </p>
                    <p style="margin: 2px 0 0 0;">
                        NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                    </p>
                </td>
                <td style="width: 40%; text-align: center; vertical-align: top;">
                    <div style="width: 30mm; height: 40mm; border: 1px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 9pt; color: #666;">
                        Pas Foto<br>3 x 4 cm
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 3. HALAMAN RAPOR AKADEMIK -->
    <div style="padding: 5mm;" class="page-break">
        @include('rapor_kurnas.print.academic_content')
    </div>

    <!-- 4. HALAMAN RAPOR P5 -->
    <div style="padding: 5mm;">
        @include('rapor_kurnas.print.cocurricular_content')
    </div>
</body>
</html>

