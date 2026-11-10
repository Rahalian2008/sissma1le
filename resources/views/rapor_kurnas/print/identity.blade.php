<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Siswa & Satuan Pendidikan - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 100%;
            max-width: 180mm;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .num {
            width: 25px;
            text-align: left;
        }
        .label {
            width: 200px;
        }
        .sep {
            width: 15px;
            text-align: center;
        }
        .val {
            font-weight: normal;
        }
        .photo-box {
            width: 30mm;
            height: 40mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            text-align: center;
            color: #666;
            margin-top: 15px;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            vertical-align: top;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; gap: 10px;">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            🖨️ Cetak Identitas
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #1e293b; border: none; padding: 10px 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <h2>KETERANGAN TENTANG DIRI PESERTA DIDIK</h2>

        <table>
            <tr>
                <td class="num">1.</td>
                <td class="label">Nama Peserta Didik (Lengkap)</td>
                <td class="sep">:</td>
                <td class="val"><strong>{{ strtoupper($student->name) }}</strong></td>
            </tr>
            <tr>
                <td class="num">2.</td>
                <td class="label">Nomor Induk / NISN</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num">3.</td>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->birth_place ?? 'Sukabumi' }}, {{ $student->birth_date ? Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="num">4.</td>
                <td class="label">Jenis Kelamin</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->gender === 'L' || $student->gender === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="num">5.</td>
                <td class="label">Agama</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->religion ?? 'Islam' }}</td>
            </tr>
            <tr>
                <td class="num">6.</td>
                <td class="label">Nomor Induk Kependudukan (NIK)</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num">7.</td>
                <td class="label">Alamat Peserta Didik</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->address ?? 'Kec. Lengkong, Kab. Sukabumi' }}</td>
            </tr>
            <tr>
                <td class="num">8.</td>
                <td class="label">Nomor Telepon / HP</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num">9.</td>
                <td class="label">Sekolah Asal (SMP/MTs)</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->previous_school ?? 'SMP Negeri Lengkong' }}</td>
            </tr>
            <tr>
                <td class="num">10.</td>
                <td class="label">Data Orang Tua</td>
                <td class="sep">:</td>
                <td class="val"></td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 15px;">a. Nama Ayah Kandung</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->father_name ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 15px;">b. Nama Ibu Kandung</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->mother_name ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 15px;">c. Alamat Orang Tua</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->address ?? '-' }}</td>
            </tr>
        </table>

        <div class="signature-section">
            <table class="signature-table">
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
                        <div class="photo-box" style="margin: 0 auto;">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                Pas Foto<br>3 x 4 cm
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

