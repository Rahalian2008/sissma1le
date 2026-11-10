<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Demografi Peserta Didik - {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.35;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-title {
            text-align: center;
            margin: 15px 0 20px 0;
        }
        .header-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .header-title p {
            font-size: 10pt;
            margin: 4px 0 0 0;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            padding: 0;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 999;
        }
        .btn-print {
            background: #1e3a8a;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Laporan Demografi Peserta Didik'" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi Sekolah -->
        <x-print-kop :school="$school" />

    <div class="header-title">
        <h3>LAPORAN RESMI DEMOGRAFI & REKAPITULASI PESERTA DIDIK</h3>
        <p>Satuan Pendidikan: <strong>{{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</strong> &bull; Tahun Ajaran: <strong>{{ date('Y') }}/{{ date('Y')+1 }}</strong></p>
    </div>

    <!-- 1. Rekapitulasi Tingkat Kelas & Gender -->
    <div class="section-title">I. REKAPITULASI JUMLAH SISWA PER TINGKAT KELAS & GENDER</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th>Tingkat Kelas</th>
                <th style="width: 110px;">Laki-laki (L)</th>
                <th style="width: 110px;">Perempuan (P)</th>
                <th style="width: 110px;">Jumlah Total</th>
                <th style="width: 100px;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classStats as $idx => $cs)
                @php
                    $pct = $totalActive > 0 ? round(($cs->total / $totalActive) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">Kelas {{ $cs->current_class_level }}</td>
                    <td class="text-center">{{ $cs->male }}</td>
                    <td class="text-center">{{ $cs->female }}</td>
                    <td class="text-center font-bold">{{ $cs->total }}</td>
                    <td class="text-center">{{ $pct }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ $maleActive }}</td>
                <td class="text-center">{{ $femaleActive }}</td>
                <td class="text-center">{{ $totalActive }}</td>
                <td class="text-center">100%</td>
            </tr>
        </tfoot>
    </table>

    <!-- 2. Rincian Rombongan Belajar (Rombel) -->
    <div class="section-title">II. DISTRIBUSI SISWA PER ROMBONGAN BELAJAR (ROMBEL)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th>Rombongan Belajar</th>
                <th style="width: 80px;">Tingkat</th>
                <th style="width: 90px;">Laki-laki</th>
                <th style="width: 90px;">Perempuan</th>
                <th style="width: 90px;">Total Siswa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rombelStats as $idx => $rs)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $rs->current_rombel }}</td>
                    <td class="text-center">{{ $rs->current_class_level }}</td>
                    <td class="text-center">{{ $rs->male }}</td>
                    <td class="text-center">{{ $rs->female }}</td>
                    <td class="text-center font-bold">{{ $rs->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 3. Distribusi Kelompok Usia Siswa -->
    <div class="section-title">III. DISTRIBUSI KELOMPOK USIA PESERTA DIDIK (REAL-TIME DAPODIK)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th>Kelompok Usia</th>
                <th style="width: 110px;">Laki-laki (L)</th>
                <th style="width: 110px;">Perempuan (P)</th>
                <th style="width: 110px;">Jumlah Total</th>
                <th style="width: 100px;">Proporsi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($ageBrackets as $label => $data)
                @php
                    $agePct = $totalActive > 0 ? round(($data['total'] / $totalActive) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="font-bold">{{ $label }}</td>
                    <td class="text-center">{{ $data['L'] }}</td>
                    <td class="text-center">{{ $data['P'] }}</td>
                    <td class="text-center font-bold">{{ $data['total'] }}</td>
                    <td class="text-center">{{ $agePct }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 4. Jalur PPDB & Bantuan PIP -->
    <div class="section-title">IV. STATISTIK JALUR PENERIMAAN (PPDB) & AFIRMASI PIP</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Jalur Masuk PPDB</th>
                <th style="width: 120px;">Jumlah Siswa</th>
                <th>Kategori Bantuan Pendidikan</th>
                <th style="width: 120px;">Jumlah Siswa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @foreach($entryPathStats as $ep)
                        <div>&bull; {{ $ep->entry_path }}: <strong>{{ $ep->total }}</strong> siswa</div>
                    @endforeach
                </td>
                <td class="text-center font-bold">{{ $totalActive }} Siswa</td>
                <td>
                    <div>&bull; Penerima Bantuan PIP / KIP: <strong>{{ $pipActive }}</strong> siswa</div>
                    <div>&bull; Reguler (Non-PIP): <strong>{{ $nonPipActive }}</strong> siswa</div>
                </td>
                <td class="text-center font-bold">
                    {{ $totalActive > 0 ? round(($pipActive / $totalActive) * 100, 1) : 0 }}% PIP
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 5. Sebaran Domisili Wilayah & Transportasi -->
    <div class="section-title">V. SEBARAN DOMISILI WILAYAH KECAMATAN & TRANSPORTASI SISWA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kecamatan Tempat Tinggal (Domisili)</th>
                <th style="width: 80px;">Jumlah</th>
                <th>Moda Transportasi Utama</th>
                <th style="width: 80px;">Jumlah</th>
                <th>Radius Jarak ke Sekolah</th>
                <th style="width: 80px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(count($districtStats), count($transportStats), count($distanceStats));
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    <td>{{ $districtStats[$i]->residence_district ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $districtStats[$i]->total ?? '-' }}</td>
                    <td>{{ $transportStats[$i]->transportation ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $transportStats[$i]->total ?? '-' }}</td>
                    <td>{{ $distanceStats[$i]->distance_to_school ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $distanceStats[$i]->total ?? '-' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Catatan & Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="width: 55%; font-size: 9pt;">
                <p><strong>Catatan:</strong></p>
                <ol style="margin: 0; padding-left: 16px;">
                    <li>Data demografi diambil secara real-time dari basis data administrasi kesiswaan SMAN 1 Lengkong.</li>
                    <li>Digunakan sebagai dokumen pendukung akreditasi, pelaporan BOS, dan evaluasi capaian SPM.</li>
                </ol>
            </td>
            <td style="width: 45%; text-align: center;">
                <p>
                    {{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>
                    Kepala Sekolah,
                </p>
                <br><br><br><br>
                <p>
                    <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
                </p>
            </td>
        </tr>
    </table>
    </div>
</body>
</html>
