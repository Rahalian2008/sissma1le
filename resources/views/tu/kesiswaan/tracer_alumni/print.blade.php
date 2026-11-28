<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Tracer Study Alumni - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #111; line-height: 1.3; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #333; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
        th, td { border: 1px solid #000; padding: 4.5px 6px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signatures-table { width: 100%; margin-top: 25px; border: none; font-size: 10pt; }
        .signatures-table td { border: none; padding: 0; vertical-align: top; }
        .section-header { font-weight: bold; font-size: 10.5pt; margin-top: 14px; margin-bottom: 4px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Kop Surat Resmi dari Master Data Logo & Kop Sekolah -->
    <x-print-kop :school="$school" />

    <div class="title">REKAPITULASI DOKUMEN TRACER STUDY ALUMNI</div>
    <div class="subtitle">Penelusuran Tamatan: Perguruan Tinggi (PTN/PTS), Dunia Usaha/Industri (DUDI), dan Wirausaha</div>

    <!-- Tabel Rekap Distribusi -->
    <div class="section-header">I. REKAPITULASI DISTRIBUSI TAMATAN / ALUMNI</div>
    @php
        $tot = $stats['total'] > 0 ? $stats['total'] : 1;
    @endphp
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Kategori Status Tamatan</th>
                <th width="20%">Jumlah Alumni</th>
                <th width="15%">Persentase (%)</th>
                <th width="25%">Keterangan / Sektor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Kuliah / Studi Lanjut (PTN, PTS, Kedinasan)</td>
                <td class="text-center font-bold">{{ $stats['kuliah'] }}</td>
                <td class="text-center">{{ round(($stats['kuliah'] / $tot) * 100, 1) }}%</td>
                <td>Perguruan Tinggi Akademik &amp; Vokasi</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Bekerja (Karyawan / ASN / TNI-Polri / Swasta)</td>
                <td class="text-center font-bold">{{ $stats['bekerja'] }}</td>
                <td class="text-center">{{ round(($stats['bekerja'] / $tot) * 100, 1) }}%</td>
                <td>Dunia Usaha dan Dunia Industri (DUDI)</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Wirausaha / Bisnis Mandiri</td>
                <td class="text-center font-bold">{{ $stats['wirausaha'] }}</td>
                <td class="text-center">{{ round(($stats['wirausaha'] / $tot) * 100, 1) }}%</td>
                <td>Pengusaha Muda, Startup, Freelance</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Kursus / Pelatihan / Magang LN</td>
                <td class="text-center font-bold">{{ $stats['kursus'] }}</td>
                <td class="text-center">{{ round(($stats['kursus'] / $tot) * 100, 1) }}%</td>
                <td>Peningkatan Keterampilan &amp; Vokasional</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Sedang Mencari Kerja / Persiapan Studi</td>
                <td class="text-center font-bold">{{ $stats['mencari_kerja'] }}</td>
                <td class="text-center">{{ round(($stats['mencari_kerja'] / $tot) * 100, 1) }}%</td>
                <td>Persiapan UTBK / Seleksi Kerja</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Belum Memungkinkan Bekerja</td>
                <td class="text-center font-bold">{{ $stats['belum'] }}</td>
                <td class="text-center">{{ round(($stats['belum'] / $tot) * 100, 1) }}%</td>
                <td>Fokus Keluarga / Domisili Luar Daerah</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f7f7f7;">
                <td colspan="2" class="text-center">TOTAL ALUMNI TERDATA :</td>
                <td class="text-center">{{ $stats['total'] }}</td>
                <td class="text-center">100.0%</td>
                <td>Responden Resmi Terverifikasi</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tabel Daftar Nama -->
    <div class="section-header">II. DAFTAR NAMA TAMATAN TERDATA</div>
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Nama Alumni &amp; NISN</th>
                <th width="4%">L/P</th>
                <th width="7%">Lulus</th>
                <th width="14%">Status</th>
                <th width="32%">Detail Institusi / Kampus / Kantor</th>
                <th width="20%">Domisili &amp; Kontak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni as $idx => $a)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $a->full_name }}</strong>
                    <div style="font-size: 8pt; color: #444;">NISN: {{ $a->nisn ?: '-' }}</div>
                </td>
                <td class="text-center">{{ $a->gender }}</td>
                <td class="text-center">{{ $a->graduation_year }}</td>
                <td class="text-center"><strong>{{ $a->activity_status_badge['label'] ?? $a->activity_status }}</strong></td>
                <td>
                    @if($a->activity_status === 'KULIAH')
                        <strong>{{ $a->campus_name ?: '-' }}</strong><br>
                        Prodi: {{ $a->study_program ?: '-' }} ({{ $a->college_level ?: 'S1' }})
                    @elseif($a->activity_status === 'BEKERJA')
                        <strong>{{ $a->company_name ?: '-' }}</strong><br>
                        Posisi: {{ $a->job_position ?: '-' }}
                    @elseif($a->activity_status === 'WIRAUSAHA')
                        <strong>{{ $a->business_name ?: '-' }}</strong><br>
                        Bidang: {{ $a->business_field ?: '-' }}
                    @elseif($a->activity_status === 'KURSUS_PELATIHAN')
                        <strong>{{ $a->training_institution ?: 'Lembaga Kursus' }}</strong><br>
                        Bidang: {{ $a->training_field ?: '-' }}
                    @else
                        {{ $a->activity_status_badge['label'] ?? '-' }}
                    @endif
                </td>
                <td>
                    {{ $a->current_city ?: ($a->current_address ?: '-') }}<br>
                    <span style="font-size: 8pt; color: #444;">Telp: {{ $a->phone ?: '-' }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data alumni yang sesuai dengan filter pencarian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures-table">
        <tr>
            <td width="50%" class="text-center">
                Mengetahui,<br>
                Kepala SMA Negeri 1 Lengkong
                <br><br><br><br>
                <strong><u>{{ $headmaster['name'] ?? ($school->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd') }}</u></strong><br>
                NIP. {{ $headmaster['nip'] ?? ($school->headmaster_nip ?? '198009132008011005') }}
            </td>
            <td width="50%" class="text-center">
                Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                Koordinator Bimbingan Konseling (BK)
                <br><br><br><br>
                <strong><u>{{ $bkCoordinator['name'] ?? 'Rahmat Hidayat, S.Kom.' }}</u></strong><br>
                NIP. {{ $bkCoordinator['nip'] ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>
