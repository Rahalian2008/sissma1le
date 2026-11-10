<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Induk - {{ $student->name }} ({{ $student->nis }})</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
            position: relative;
        }
        .header-kop h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 9.5pt;
            margin: 1px 0;
            font-style: normal;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-doc h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 10pt;
            margin-top: 2px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 3px 6px;
            margin-top: 10px;
            margin-bottom: 6px;
            border: 1px solid #ccc;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
        }
        table.data-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        table.data-table td.label {
            width: 32%;
        }
        table.data-table td.separator {
            width: 2%;
            text-align: center;
        }
        table.data-table td.value {
            width: 66%;
        }
        .photo-box {
            width: 30mm;
            height: 40mm;
            border: 1px solid #000;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            float: right;
            margin-left: 10px;
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 25px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 10.5pt;
        }
        .qr-section {
            float: left;
            text-align: center;
            font-size: 8pt;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .print-btn-bar {
            background: #2563eb;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            font-size: 14px;
        }
        .print-btn-bar button {
            background: #fff;
            color: #2563eb;
            font-weight: bold;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Induk Siswa: ' . $student->name" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15px 25px;">
        <!-- KOP SURAT RESMI -->
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>LEMBAR BUKU INDUK SISWA</h3>
            <p>Nomor Registrasi: REG-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}-{{ $student->nis }}</p>
        </div>

        <!-- Pas Foto Box -->
        <div class="photo-box">
            PAS FOTO<br>3 x 4 cm
        </div>

        <!-- Bagian A -->
        <div class="section-title">A. KETERANGAN TENTANG DIRI PESERTA DIDIK</div>
        <table class="data-table">
            <tr>
                <td class="label">1. Nama Siswa (Lengkap)</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">2. Nomor Induk Siswa (NIS)</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->nis }}</td>
            </tr>
            <tr>
                <td class="label">3. NISN / NIK Siswa</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->nisn ?? '-' }} / {{ $student->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">4. Nomor Kartu Keluarga (No. KK)</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family_card_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">5. Jenis Kelamin</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">6. Tempat, Tanggal Lahir</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">7. Agama & Kepercayaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->religion ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">8. Golongan Darah</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->blood_type ?? 'Tidak Tahu' }}</td>
            </tr>
            <tr>
                <td class="label">9. Keadaan Jasmani (Tinggi / Berat)</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->height ? $student->height.' cm' : '-' }} / {{ $student->weight ? $student->weight.' kg' : '-' }}</td>
            </tr>
            <tr>
                <td class="label">10. Anak Ke- / Dari Bersaudara</td>
                <td class="separator">:</td>
                <td class="value">Ke-{{ $student->child_order ?? 1 }} dari {{ $student->siblings_count ?? 1 }} bersaudara</td>
            </tr>
            <tr>
                <td class="label">11. Alamat Tempat Tinggal</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->address ?? '-' }} {{ $student->rt_rw ? '(RT/RW: '.$student->rt_rw.')' : '' }}, Kec. {{ $student->residence_district ?? 'Lengkong' }}, Kode Pos {{ $student->postal_code ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">12. Tinggal Bersama & Jarak</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->living_with ?? 'Bersama Orang Tua' }} (Jarak: {{ $student->distance_to_school ?? '-' }}, Kendaraan: {{ $student->transportation ?? '-' }})</td>
            </tr>
            <tr>
                <td class="label">13. Nomor Telepon / HP Siswa</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->phone ?? '-' }} (Email: {{ $student->email ?? '-' }})</td>
            </tr>
        </table>

        <!-- Bagian B -->
        <div class="section-title">B. KETERANGAN PENERIMAAN</div>
        <table class="data-table">
            <tr>
                <td class="label">1. Asal Sekolah (SMP / MTs)</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->school_origin ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">2. No. Ijazah & Ujian SMP</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->junior_diploma_number ?? '-' }} / {{ $student->junior_exam_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">3. Tanggal Penerimaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">4. Jalur Masuk PPDB</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->entry_path ?? 'Zonasi' }}</strong></td>
            </tr>
            <tr>
                <td class="label">5. Diterima di Tingkat / Kelas</td>
                <td class="separator">:</td>
                <td class="value">Kelas {{ $student->admission_class ?? $student->current_class_level }} (Kini: Kelas {{ $student->current_class_level }} {{ $student->current_rombel }})</td>
            </tr>
            <tr>
                <td class="label">6. Program Bantuan PIP / KIP</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->is_pip ? 'Penerima Bantuan PIP (No. KIP: '.($student->pip_number ?? '-').')' : 'Bukan Penerima PIP' }}</td>
            </tr>
        </table>

        <!-- Bagian C -->
        <div class="section-title">C. KETERANGAN ORANG TUA / WALI</div>
        <table class="data-table">
            <tr>
                <td class="label" colspan="3" style="background:#f9f9f9; font-weight:bold;">1. Data Ayah Kandung:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">a. Nama Lengkap</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->family?->father_name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">b. NIK Ayah</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family?->father_nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">c. Pendidikan / Pekerjaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->father_education ?? '-' }} / {{ $student->family?->father_occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">d. Penghasilan Bulanan / No. Telp</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->father_income ?? '-' }} / {{ $student->family?->father_phone ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label" colspan="3" style="background:#f9f9f9; font-weight:bold;">2. Data Ibu Kandung:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">a. Nama Lengkap</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->family?->mother_name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">b. NIK Ibu</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family?->mother_nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">c. Pendidikan / Pekerjaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->mother_education ?? '-' }} / {{ $student->family?->mother_occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">d. Penghasilan Bulanan / No. Telp</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->mother_income ?? '-' }} / {{ $student->family?->mother_phone ?? '-' }}</td>
            </tr>

            @if($student->family?->guardian_name)
            <tr>
                <td class="label" colspan="3" style="background:#f9f9f9; font-weight:bold;">3. Data Wali Murid:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:15px;">Nama / Pekerjaan / Telp</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family->guardian_name }} / {{ $student->family->guardian_occupation ?? '-' }} / {{ $student->family->guardian_phone ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <div style="clear: both; margin-top: 15px;"></div>

        <!-- Tanda Tangan & QR Verifikasi -->
        <div class="signature-section">
            <div class="qr-section">
                {!! $qrCodeSvg !!}
                <div>Scan untuk verifikasi keaslian lembar buku induk</div>
            </div>

            <table class="signature-table" style="width: 55%; float: right;">
                <tr>
                    <td>
                        {{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}<br>
                        Kepala Sekolah {{ $school->name }},<br>
                        <br><br><br><br>
                        <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                        NIP. {{ $school->headmaster_nip }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
