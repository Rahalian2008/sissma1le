<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transkrip Nilai Ijazah - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .subtitle {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 15px;
        }
        .identity-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 7px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .group-row {
            background-color: #e9e9e9;
            font-weight: bold;
        }
        .no-print {
            margin-bottom: 15px;
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
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Transkrip Ijazah (A4)
        </button>
    </div>

    <!-- KOP RESMI DINAS PENDIDIKAN JAWA BARAT -->
    <table class="kop-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                <img src="{{ asset('images/logo-jabar.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" style="width: 65px; height: auto;">
            </td>
            <td style="text-align: center;">
                <h4 style="margin: 0; font-size: 11pt; font-weight: bold;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                <h3 style="margin: 1px 0; font-size: 12pt; font-weight: bold;">DINAS PENDIDIKAN</h3>
                <h4 style="margin: 0; font-size: 11pt; font-weight: bold;">CABANG DINAS PENDIDIKAN WILAYAH V</h4>
                <h2 style="margin: 2px 0; font-size: 14pt; font-weight: bold;">SMA NEGERI 1 LENGKONG</h2>
                <p style="margin: 0; font-size: 8.5pt;">Jl. Raya Tegallega Kec. Lengkong Kabupaten Sukabumi</p>
                <p style="margin: 0; font-size: 8.5pt;">Email: sman1lengkong@gmail.com | Website: https://sman1lengkong.school</p>
            </td>
            <td style="width: 75px; text-align: center;">
                <img src="{{ asset('images/logo-sma1.png') }}" onerror="this.src='{{ asset('images/logo-jabar.png') }}'" style="width: 65px; height: auto;">
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="title">TRANSKRIP NILAI IJAZAH SMA</div>
    <div class="subtitle">NOMOR: {{ $transcript->transcript_number ?? '421.3/TR-001/SMA1LE/'.date('Y') }}</div>

    <!-- DATA IDENTITAS PESERTA DIDIK -->
    <table class="identity-table">
        <tr>
            <td style="width: 25%;">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%; font-weight: bold;">{{ strtoupper($student->name) }}</td>
        </tr>
        <tr>
            <td>Tempat dan Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $student->pob ?? 'Sukabumi' }}, {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa (NIS)</td>
            <td>:</td>
            <td>{{ $student->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa Nasional (NISN)</td>
            <td>:</td>
            <td>{{ $student->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Ijazah Nasional</td>
            <td>:</td>
            <td style="font-family: monospace; font-weight: bold;">{{ $transcript->diploma_number ?? 'DN-02/SMA/KURNAS/'.date('Y').'/........' }}</td>
        </tr>
        <tr>
            <td>Satuan Pendidikan Asal</td>
            <td>:</td>
            <td>SMA Negeri 1 Lengkong</td>
        </tr>
    </table>

    <!-- TABEL DAFTAR NILAI AKHIR IJAZAH -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 58%; text-align: left;">Mata Pelajaran</th>
                <th style="width: 18%;">Rata-rata Rapor</th>
                <th style="width: 18%;">Nilai Ijazah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupA = $subjectGroups->where('group_type', 'A');
                $groupB = $subjectGroups->where('group_type', 'B');
                $groupC = $subjectGroups->where('group_type', 'C');
                $totalFinal = 0;
                $countFinal = 0;
                $no = 1;
            @endphp

            @if($groupA->isNotEmpty())
                <tr class="group-row">
                    <td colspan="4">A. Kelompok Mata Pelajaran Umum</td>
                </tr>
                @foreach($groupA as $item)
                    @php
                        $scoreData = $scoresBySubject[$item->subject_id] ?? null;
                        $repAvg = $scoreData['report_average'] ?? null;
                        $fin = $scoreData['final_score'] ?? $repAvg ?? ($transcript->average_score ?? 85);
                        if($fin) { $totalFinal += $fin; $countFinal++; }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $repAvg ? number_format($repAvg, 1) : number_format($fin, 1) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($fin, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            @if($groupB->isNotEmpty())
                <tr class="group-row">
                    <td colspan="4">B. Kelompok Mata Pelajaran Pilihan</td>
                </tr>
                @foreach($groupB as $item)
                    @php
                        $scoreData = $scoresBySubject[$item->subject_id] ?? null;
                        $repAvg = $scoreData['report_average'] ?? null;
                        $fin = $scoreData['final_score'] ?? $repAvg ?? ($transcript->average_score ?? 86);
                        if($fin) { $totalFinal += $fin; $countFinal++; }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $repAvg ? number_format($repAvg, 1) : number_format($fin, 1) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($fin, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            @if($groupC->isNotEmpty())
                <tr class="group-row">
                    <td colspan="4">C. Muatan Lokal</td>
                </tr>
                @foreach($groupC as $item)
                    @php
                        $scoreData = $scoresBySubject[$item->subject_id] ?? null;
                        $repAvg = $scoreData['report_average'] ?? null;
                        $fin = $scoreData['final_score'] ?? $repAvg ?? ($transcript->average_score ?? 88);
                        if($fin) { $totalFinal += $fin; $countFinal++; }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $repAvg ? number_format($repAvg, 1) : number_format($fin, 1) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($fin, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            @php
                $averageTotal = $countFinal > 0 ? round($totalFinal / $countFinal, 2) : ($transcript->average_score ?? 86.50);
            @endphp
            <tr style="background-color: #f7f7f7; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 15px;">RATA-RATA NILAI IJAZAH:</td>
                <td style="text-align: center; font-size: 11pt;">{{ number_format($averageTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN KEPALA SEKOLAH (WAJIB DI KIRI SESUAI ATURAN DINAS) -->
    <table style="width: 100%; margin-top: 25px; font-size: 10pt;">
        <tr>
            <!-- KIRI: KEPALA SEKOLAH -->
            <td style="width: 55%; vertical-align: top; text-align: left; position: relative;">
                <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ $transcript->graduation_date ? \Carbon\Carbon::parse($transcript->graduation_date)->translatedFormat('d F Y') : \Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
                <p style="margin: 2px 0 0 0;">Kepala SMA Negeri 1 Lengkong,</p>
                
                <div style="height: 65px; position: relative; margin: 4px 0;">
                    @if($setting->show_signature_image)
                        @if($setting->school_stamp)
                            <img src="{{ asset('storage/'.$setting->school_stamp) }}" alt="Stempel" style="position: absolute; left: 10px; top: -5px; height: 75px; opacity: 0.85; z-index: 1;">
                        @endif
                        @if($setting->headmaster_signature)
                            <img src="{{ asset('storage/'.$setting->headmaster_signature) }}" alt="TTD Kepsek" style="position: absolute; left: 35px; top: 0; height: 65px; z-index: 2;">
                        @endif
                    @endif
                </div>

                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
                </p>
                <p style="margin: 2px 0 0 0;">
                    NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
                </p>
            </td>

            <!-- KANAN: PAS FOTO 3X4 SISWA -->
            <td style="width: 45%; vertical-align: top; text-align: center;">
                <div style="width: 30mm; height: 40mm; border: 1px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 8.5pt; color: #555; background-color: #fafafa;">
                    Pas Foto 3 x 4 cm
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

