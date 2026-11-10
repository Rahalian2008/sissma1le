<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Buku Induk Siswa - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.25;
        }
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .subtitle {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 10px;
        }
        .section-header {
            font-size: 9.5pt;
            font-weight: bold;
            background-color: #e9e9e9;
            padding: 3px 6px;
            border: 1px solid #000;
            margin-top: 8px;
            margin-bottom: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 8px;
        }
        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }
        .grid-table th, .grid-table td {
            border: 1px solid #000;
            padding: 3.5px 4px;
        }
        .grid-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak Browser -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 6px 14px; background-color: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 11px;">
            🖨️ Cetak Buku Induk Siswa (A4)
        </button>
    </div>

    <!-- KOP RESMI DINAS -->
    <table class="kop-table">
        <tr>
            <td style="width: 65px; text-align: center;">
                <img src="{{ asset('images/logo-jabar.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" style="width: 55px; height: auto;">
            </td>
            <td style="text-align: center;">
                <h4 style="margin: 0; font-size: 10.5pt; font-weight: bold;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                <h3 style="margin: 1px 0; font-size: 11.5pt; font-weight: bold;">DINAS PENDIDIKAN</h3>
                <h4 style="margin: 0; font-size: 10.5pt; font-weight: bold;">CABANG DINAS PENDIDIKAN WILAYAH V</h4>
                <h2 style="margin: 1px 0; font-size: 13pt; font-weight: bold;">SMA NEGERI 1 LENGKONG</h2>
                <p style="margin: 0; font-size: 8pt;">Jl. Raya Tegallega Kec. Lengkong Kabupaten Sukabumi</p>
            </td>
            <td style="width: 65px; text-align: center;">
                <img src="{{ asset('images/logo-sma1.png') }}" onerror="this.src='{{ asset('images/logo-jabar.png') }}'" style="width: 55px; height: auto;">
            </td>
        </tr>
    </table>

    <div class="title">LEMBAR BUKU INDUK PESERTA DIDIK</div>
    <div class="subtitle">NOMOR INDUK SISWA: {{ $student->nis ?? '-' }} / NISN: {{ $student->nisn ?? '-' }}</div>

    <!-- A. IDENTITAS PESERTA DIDIK & PAS FOTO -->
    <div class="section-header">A. IDENTITAS PESERTA DIDIK</div>
    <table style="width: 100%;">
        <tr>
            <td style="width: 80%; vertical-align: top;">
                <table class="info-table">
                    <tr>
                        <td style="width: 32%;">1. Nama Lengkap</td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 66%; font-weight: bold;">{{ strtoupper($student->name) }}</td>
                    </tr>
                    <tr>
                        <td>2. Nomor Induk Siswa (NIS)</td>
                        <td>:</td>
                        <td>{{ $student->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>3. Nomor Induk Siswa Nasional (NISN)</td>
                        <td>:</td>
                        <td>{{ $student->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>4. Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $student->pob ?? 'Sukabumi' }}, {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>5. Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ ($student->gender ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>6. Agama</td>
                        <td>:</td>
                        <td>{{ $student->religion ?? 'Islam' }}</td>
                    </tr>
                    <tr>
                        <td>7. Alamat Tempat Tinggal</td>
                        <td>:</td>
                        <td>{{ $student->address ?? 'Kecamatan Lengkong, Kabupaten Sukabumi' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 20%; vertical-align: top; text-align: center;">
                <div style="width: 28mm; height: 36mm; border: 1px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #666; background-color: #fafafa;">
                    Foto 3 x 4
                </div>
            </td>
        </tr>
    </table>

    <!-- B. DATA ORANG TUA / WALI -->
    <div class="section-header">B. DATA ORANG TUA / WALI PESERTA DIDIK</div>
    <table class="info-table">
        <tr>
            <td style="width: 26%;">1. Nama Ayah Kandung</td>
            <td style="width: 2%;">:</td>
            <td style="width: 32%;">{{ $student->father_name ?? '-' }}</td>
            <td style="width: 20%;">Pekerjaan Ayah</td>
            <td style="width: 2%;">:</td>
            <td style="width: 18%;">{{ $student->father_job ?? '-' }}</td>
        </tr>
        <tr>
            <td>2. Nama Ibu Kandung</td>
            <td>:</td>
            <td>{{ $student->mother_name ?? '-' }}</td>
            <td>Pekerjaan Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_job ?? '-' }}</td>
        </tr>
        <tr>
            <td>3. Alamat Orang Tua / Wali</td>
            <td>:</td>
            <td colspan="4">{{ $student->parent_address ?? $student->address ?? 'Kecamatan Lengkong, Kabupaten Sukabumi' }}</td>
        </tr>
    </table>

    <!-- C. MATRIKS REKAPITULASI NILAI RAPOR (SEMESTER 1 s.d. 6) -->
    <div class="section-header">C. REKAPITULASI HASIL BELAJAR (NILAI RAPOR SEMESTER 1 s.d. 6)</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 47%; text-align: left;">Mata Pelajaran</th>
                <th colspan="2">Kelas X</th>
                <th colspan="2">Kelas XI</th>
                <th colspan="2">Kelas XII</th>
                <th rowspan="2" style="width: 12%;">Rata2 Akhir</th>
            </tr>
            <tr>
                <th style="width: 6%;">Sm 1</th>
                <th style="width: 6%;">Sm 2</th>
                <th style="width: 6%;">Sm 3</th>
                <th style="width: 6%;">Sm 4</th>
                <th style="width: 6%;">Sm 5</th>
                <th style="width: 6%;">Sm 6</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $allSum = 0; $allCount = 0; @endphp
            @foreach($subjectGroups as $group)
                @php
                    $semScores = $scoresBySubjectSemester[$group->subject_id] ?? [];
                    $valid = array_filter($semScores, fn($v) => !is_null($v));
                    $rowAvg = !empty($valid) ? round(array_sum($valid) / count($valid), 1) : null;
                    if($rowAvg) { $allSum += $rowAvg; $allCount++; }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $group->display_name ?? $group->subject->name ?? '-' }}</td>
                    @for($s = 1; $s <= 6; $s++)
                        <td style="text-align: center;">
                            {{ isset($semScores[$s]) ? number_format($semScores[$s], 0) : '-' }}
                        </td>
                    @endfor
                    <td style="text-align: center; font-weight: bold; background-color: #fafafa;">
                        {{ $rowAvg ? number_format($rowAvg, 1) : '-' }}
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="8" style="text-align: right; padding-right: 10px;">RATA-RATA KUMULATIF:</td>
                <td style="text-align: center;">
                    {{ $allCount > 0 ? number_format($allSum / $allCount, 2) : ($transcript->average_score ?? '86.50') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- D. PRESENSI & EKSTRAKURIKULER -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-header">D. KEHADIRAN KUMULATIF</div>
                <table class="grid-table">
                    <tr>
                        <td style="width: 70%;">Sakit (S)</td>
                        <td style="text-align: center; font-weight: bold;">{{ $cumulativeAttendance['sick'] }} hari</td>
                    </tr>
                    <tr>
                        <td>Izin (I)</td>
                        <td style="text-align: center; font-weight: bold;">{{ $cumulativeAttendance['permission'] }} hari</td>
                    </tr>
                    <tr>
                        <td>Tanpa Keterangan (A)</td>
                        <td style="text-align: center; font-weight: bold;">{{ $cumulativeAttendance['absent'] }} hari</td>
                    </tr>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-header">E. KELULUSAN / AKHIR PENDIDIKAN</div>
                <table class="grid-table">
                    <tr>
                        <td style="width: 50%;">Status Akhir</td>
                        <td style="font-weight: bold; text-align: center; color: #000;">
                            {{ $transcript?->status_graduation ?? 'LULUS' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Nomor Ijazah</td>
                        <td style="font-family: monospace; font-size: 8pt; text-align: center;">
                            {{ $transcript?->diploma_number ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Tanggal Lulus</td>
                        <td style="text-align: center;">
                            {{ $transcript?->graduation_date ? \Carbon\Carbon::parse($transcript->graduation_date)->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TANDA TANGAN KEPALA SEKOLAH (WAJIB DI KIRI) -->
    <table style="width: 100%; margin-top: 15px; font-size: 9pt;">
        <tr>
            <!-- KIRI: KEPALA SEKOLAH -->
            <td style="width: 60%; vertical-align: top; text-align: left; position: relative;">
                <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ \Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                <p style="margin: 1px 0 0 0;">Kepala SMA Negeri 1 Lengkong,</p>
                
                <div style="height: 55px; position: relative; margin: 3px 0;">
                    @if($setting->show_signature_image)
                        @if($setting->school_stamp)
                            <img src="{{ asset('storage/'.$setting->school_stamp) }}" alt="Stempel" style="position: absolute; left: 10px; top: -5px; height: 65px; opacity: 0.85; z-index: 1;">
                        @endif
                        @if($setting->headmaster_signature)
                            <img src="{{ asset('storage/'.$setting->headmaster_signature) }}" alt="TTD Kepsek" style="position: absolute; left: 35px; top: 0; height: 55px; z-index: 2;">
                        @endif
                    @endif
                </div>

                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                </p>
                <p style="margin: 1px 0 0 0;">
                    NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                </p>
            </td>

            <td style="width: 40%; vertical-align: bottom; text-align: right; font-size: 8pt; color: #555;">
                Dicetak dari Aplikasi SIS-SMA1LE<br>
                Sesuai Format Buku Induk e-Rapor 2025
            </td>
        </tr>
    </table>

</body>
</html>

