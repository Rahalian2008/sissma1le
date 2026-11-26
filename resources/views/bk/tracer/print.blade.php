<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tracer Study Alumni - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 15mm 20mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 9pt; }
        .title { text-align: center; margin-bottom: 18px; }
        .title h4 { margin: 0; font-size: 12pt; text-decoration: underline; text-transform: uppercase; font-weight: bold; }
        .title p { margin: 3px 0; font-size: 10pt; }
        
        table.summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        table.summary-table th, table.summary-table td { border: 1px solid #333; padding: 5px 8px; text-align: center; }
        table.summary-table th { background-color: #f2f2f2; font-weight: bold; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 9pt; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 4px 6px; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }

        .signature-table { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; font-size: 10pt; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }

        @media print {
            body { margin: 12mm 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #1e293b; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px;">
            🖨️ Cetak Dokumen (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>LAPORAN HASIL PENELUSURAN TAMATAN (TRACER STUDY ALUMNI)</h4>
        <p>BIMBINGAN KONSELING &bull; TAHUN ANGGARAN {{ date('Y') }}</p>
        @if($filterYear || $filterStatus)
            <p style="font-size: 9pt; font-style: italic;">
                Filter: 
                @if($filterYear) Tahun Lulus: <strong>{{ $filterYear }}</strong> @endif
                @if($filterStatus) &bull; Status: <strong>{{ $filterStatus }}</strong> @endif
            </p>
        @endif
    </div>

    <!-- 1. Ringkasan Rekapitulasi Statistik Serapan Alumni -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        I. Rekapitulasi Distribusi Tamatan / Alumni
    </div>
    @php
        $divisor = $total > 0 ? $total : 1;
        $pctK = round(($kuliahCount / $divisor) * 100, 1);
        $pctW = round(($kerjaCount / $divisor) * 100, 1);
        $pctU = round(($wirausahaCount / $divisor) * 100, 1);
        $pctP = round(($kursusCount / $divisor) * 100, 1);
        $pctM = round(($mencariCount / $divisor) * 100, 1);
    @endphp
    <table class="summary-table">
        <thead>
            <tr>
                <th>No</th>
                <th style="text-align: left;">Kategori Status Tamatan</th>
                <th>Jumlah Alumni</th>
                <th>Persentase (%)</th>
                <th style="text-align: left;">Keterangan / Sektor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td style="text-align: left; font-weight: bold;">Kuliah / Studi Lanjut (PTN, PTS, Kedinasan)</td>
                <td>{{ $kuliahCount }}</td>
                <td>{{ $pctK }}%</td>
                <td style="text-align: left;">Perguruan Tinggi Akademik &amp; Vokasi</td>
            </tr>
            <tr>
                <td>2</td>
                <td style="text-align: left; font-weight: bold;">Bekerja (Karyawan / ASN / TNI / Swasta)</td>
                <td>{{ $kerjaCount }}</td>
                <td>{{ $pctW }}%</td>
                <td style="text-align: left;">Dunia Usaha dan Dunia Industri (DUDI)</td>
            </tr>
            <tr>
                <td>3</td>
                <td style="text-align: left; font-weight: bold;">Wirausaha / Bisnis Mandiri</td>
                <td>{{ $wirausahaCount }}</td>
                <td>{{ $pctU }}%</td>
                <td style="text-align: left;">Pengusaha Muda, Startup, Freelance</td>
            </tr>
            <tr>
                <td>4</td>
                <td style="text-align: left; font-weight: bold;">Kursus / Pelatihan / Magang LN</td>
                <td>{{ $kursusCount }}</td>
                <td>{{ $pctP }}%</td>
                <td style="text-align: left;">Peningkatan Keterampilan &amp; Vokasional</td>
            </tr>
            <tr>
                <td>5</td>
                <td style="text-align: left; font-weight: bold;">Sedang Mencari Kerja / Persiapan Studi</td>
                <td>{{ $mencariCount }}</td>
                <td>{{ $pctM }}%</td>
                <td style="text-align: left;">Persiapan UTBK / Seleksi Kerja</td>
            </tr>
            <tr style="background-color: #fafafa; font-weight: bold;">
                <td colspan="2" style="text-align: right;">TOTAL ALUMNI TERDATA :</td>
                <td>{{ $total }}</td>
                <td>100.0%</td>
                <td style="text-align: left;">Responden Resmi Terverifikasi</td>
            </tr>
        </tbody>
    </table>

    <!-- 2. Tabel Rincian Data Alumni -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        II. Daftar Nama Tamatan Terdata
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 140px; text-align: left;">Nama Alumni &amp; NISN</th>
                <th style="width: 25px;">L/P</th>
                <th style="width: 50px;">Lulus</th>
                <th style="width: 70px;">Status</th>
                <th style="text-align: left;">Detail Institusi / Kampus / Kantor</th>
                <th style="width: 90px; text-align: left;">Domisili &amp; Kontak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni as $idx => $item)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $item->full_name }}</strong>
                    @if($item->nisn)<br><span style="font-size: 8pt; color: #555;">NISN: {{ $item->nisn }}</span>@endif
                </td>
                <td style="text-align: center;">{{ $item->gender }}</td>
                <td style="text-align: center;">{{ $item->graduation_year }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item->activity_status }}</td>
                <td>
                    @if($item->activity_status === 'KULIAH')
                        <strong>{{ $item->campus_name }}</strong>
                        @if($item->study_program)<br>Prodi: {{ $item->study_program }} ({{ $item->college_level ?? 'S1' }})@endif
                        @if($item->admission_track)<br><span style="font-size: 8pt; color: #555;">Jalur: {{ $item->admission_track }}</span>@endif
                    @elseif($item->activity_status === 'BEKERJA')
                        <strong>{{ $item->company_name }}</strong>
                        @if($item->job_position)<br>Posisi: {{ $item->job_position }}@endif
                        @if($item->work_field)<br><span style="font-size: 8pt; color: #555;">Sektor: {{ $item->work_field }}</span>@endif
                    @elseif($item->activity_status === 'WIRAUSAHA')
                        <strong>{{ $item->business_name }}</strong>
                        @if($item->business_field)<br>Bidang: {{ $item->business_field }}@endif
                    @else
                        <span style="color: #666; font-style: italic;">Sedang persiapan</span>
                    @endif
                </td>
                <td>
                    {{ $item->current_city ?: '-' }}
                    @if($item->phone)<br><span style="font-size: 8pt; color: #555;">{{ $item->phone }}</span>@endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 15px; color: #777;">
                    Tidak ada data alumni yang sesuai dengan filter pencarian.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala {{ $school->name ?? 'SMAN 1 Lengkong' }}
                <br><br><br><br>
                <strong><u>{{ $school->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd' }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? '198009132008011005' }}
            </td>
            <td>
                Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Koordinator Bimbingan Konseling (BK)
                <br><br><br><br>
                <strong><u>Rahmat Hidayat, S.Kom.</u></strong><br>
                NIP. -
            </td>
        </tr>
    </table>
</body>
</html>
