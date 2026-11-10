<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Tata Tertib - {{ $selectedClass?->name ?? 'Semua Kelas' }} ({{ $startDate }} s.d. {{ $endDate }}) [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.2cm 1.5cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10.5pt' : '10pt' }};
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #0f172a;
            border-radius: 6px;
            padding: 2px;
            border: 1px solid #334155;
        }
        .paper-toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .paper-toggle-btn.active {
            background: #6366f1;
            color: #ffffff;
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .paper-toggle-btn.inactive:hover {
            color: #f8fafc;
            background: #1e293b;
        }
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .no-print-bar form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .no-print-bar input, .no-print-bar select {
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #fff;
            font-size: 12px;
        }
        .no-print-bar button {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #10b981; color: #fff; }
        .btn-back { background: #475569; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .btn-filter { background: #6366f1; color: #fff; }

        .doc-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 15px 25px;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-header h4 { margin: 0; font-size: 11pt; font-weight: normal; }
        .kop-header h3 { margin: 2px 0; font-size: 13pt; font-weight: bold; }
        .kop-header h2 { margin: 2px 0; font-size: 16pt; font-weight: 900; }
        .kop-header p { margin: 2px 0 0 0; font-size: 8.5pt; color: #222; }

        .doc-title { text-align: center; margin: 14px 0 12px 0; }
        .doc-title h3 { margin: 0; font-size: 13pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 3px 0 0 0; font-size: 10pt; }

        .section-header {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
            border-left: 3px solid #333;
            padding-left: 6px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        table.data-table td.center { text-align: center; }
        table.data-table td.right { text-align: right; }

        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signatures td { vertical-align: top; font-size: 10pt; padding: 0 10px; }

        @media print {
            .no-print-bar { display: none !important; }
            body { background: none; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (No Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('reports.index') }}" class="btn-back">⬅ Kembali</a>
            <span style="font-weight: bold; font-size: 14px;">CETAK LAPORAN TATA TERTIB (PRESTASI & PELANGGARAN)</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210×297mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215×330mm)
                </a>
            </div>

            <form action="{{ route('reports.print.discipline') }}" method="GET">
                <input type="hidden" name="paper" value="{{ request('paper', 'a4') }}">
                <label>Mulai:</label>
                <input type="date" name="start_date" value="{{ $startDate }}">
                
                <label>Selesai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}">

                @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                    <label>Kelas:</label>
                    @if(Auth::user()->role === 'wali_kelas')
                        <input type="text" value="{{ $selectedClass?->name ?? 'Kelas Binaan' }}" readonly style="background: #334155; color: #fff; border: 1px solid #475569; padding: 5px 10px; border-radius: 6px; cursor: not-allowed; font-weight: bold; width: 120px;">
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @else
                        <select name="class_id">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                @endif

                <button type="submit" class="btn-filter">Terapkan Filter</button>
                <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak ({{ $isF4 ? 'F4 / Folio' : 'A4' }})</button>
            </form>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
        @else
            <div class="kop-header">
                <h4>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h4>
                <h3>DINAS PENDIDIKAN</h3>
                <h4>{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
                <h2>{{ strtoupper($schoolName) }}</h2>
                <p>NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</p>
                <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</p>
            </div>
        @endif

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h3>LAPORAN TATA TERTIB SISWA (PRESTASI & PELANGGARAN)</h3>
            <p>
                <strong>Periode:</strong> {{ $periodTitle ?? (\Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' s.d. ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y')) }}
                &nbsp;|&nbsp;
                <strong>Kelas:</strong> {{ $selectedClass?->name ?? 'Semua Kelas' }}
            </p>
        </div>

        <!-- Ringkasan Statistik Periode -->
        <div style="background: #fafafa; border: 1px solid #ccc; padding: 8px 12px; margin-bottom: 12px; font-size: 9pt; display: flex; justify-content: space-around;">
            <div>Total Prestasi Terverifikasi: <strong>{{ $stats['total_achievements'] }} Prestasi</strong> (+{{ $stats['total_achievement_points'] }} Poin)</div>
            <div>Total Pelanggaran Terverifikasi: <strong>{{ $stats['total_violations'] }} Kasus</strong> (-{{ $stats['total_violation_points'] }} Poin)</div>
        </div>

        <!-- Bagian 1: Daftar Prestasi Siswa -->
        <div class="section-header">A. Catatan Prestasi & Apresiasi Siswa (R1 - R10)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">Tanggal</th>
                    <th style="width: 65px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 55px;">Kelas</th>
                    <th>Judul / Bentuk Prestasi</th>
                    <th style="width: 70px;">Tingkat</th>
                    <th style="width: 40px;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($achievements as $idx => $ach)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $ach->date->format('d/m/Y') }}</td>
                        <td class="center">{{ $ach->student?->nis }}</td>
                        <td>{{ $ach->student?->name }}</td>
                        <td class="center">{{ $ach->student?->schoolClass?->name ?? '-' }}</td>
                        <td>{{ $ach->title }}</td>
                        <td class="center">{{ $ach->level ?? '-' }}</td>
                        <td class="center font-bold" style="color: #059669;">+{{ $ach->points }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center" style="padding: 10px;">Tidak ada catatan prestasi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Bagian 2: Daftar Pelanggaran Siswa -->
        <div class="section-header" style="margin-top: 18px;">B. Catatan Pelanggaran Tata Tertib Siswa (P1 - P6)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">Tanggal</th>
                    <th style="width: 65px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 55px;">Kelas</th>
                    <th>Bentuk Pelanggaran</th>
                    <th style="width: 70px;">Kategori</th>
                    <th style="width: 40px;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $idx => $vio)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $vio->date->format('d/m/Y') }}</td>
                        <td class="center">{{ $vio->student?->nis }}</td>
                        <td>{{ $vio->student?->name }}</td>
                        <td class="center">{{ $vio->student?->schoolClass?->name ?? '-' }}</td>
                        <td>{{ $vio->title }}</td>
                        <td class="center">{{ $vio->category?->name ?? '-' }}</td>
                        <td class="center font-bold" style="color: #dc2626;">-{{ $vio->points }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center" style="padding: 10px;">Tidak ada catatan pelanggaran pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Bagian 3: Rekap Siswa Perlu Perhatian / Apresiasi Khusus -->
        @if(count($studentRecaps) > 0)
            <div class="section-header" style="margin-top: 18px;">C. Rekapitulasi Akumulasi Poin Siswa</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 65px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 55px;">Kelas</th>
                        <th style="width: 75px;">Jml Prestasi</th>
                        <th style="width: 75px;">Poin Prestasi</th>
                        <th style="width: 75px;">Jml Pelanggaran</th>
                        <th style="width: 75px;">Poin Pelanggaran</th>
                        <th>Status Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentRecaps as $idx => $sr)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="center">{{ $sr['student']->nis }}</td>
                            <td>{{ $sr['student']->name }}</td>
                            <td class="center">{{ $sr['student']->schoolClass?->name ?? '-' }}</td>
                            <td class="center">{{ $sr['ach_count'] }}</td>
                            <td class="center font-bold" style="color: #059669;">{{ $sr['ach_points'] > 0 ? '+'.$sr['ach_points'] : '0' }}</td>
                            <td class="center">{{ $sr['vio_count'] }}</td>
                            <td class="center font-bold" style="color: #dc2626;">{{ $sr['vio_points'] > 0 ? '-'.$sr['vio_points'] : '0' }}</td>
                            <td>
                                @if($sr['ach_points'] >= 50)
                                    <strong>Penghargaan Unggul</strong>
                                @elseif($sr['vio_points'] >= 50)
                                    <strong style="color: #dc2626;">Perlu Pembinaan BK</strong>
                                @else
                                    Pemantauan Rutin
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Tanda Tangan Pengesahan (Kepala Sekolah & Wakasek Bid. Kesiswaan) -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 20px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName ?? \App\Models\Teacher::getHeadmaster()['name'] }}</span><br>
                    NIP. {{ $headmasterNip ?? \App\Models\Teacher::getHeadmaster()['nip'] }}
                </td>
                <td style="width: 50%; text-align: right; padding-right: 20px;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Wakasek Bid. Kesiswaan</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $kesiswaanName ?? \App\Models\Teacher::getWakasekKesiswaan()['name'] }}</span><br>
                    NIP. {{ $kesiswaanNip ?? \App\Models\Teacher::getWakasekKesiswaan()['nip'] }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
