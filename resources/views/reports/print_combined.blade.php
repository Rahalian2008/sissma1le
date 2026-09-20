<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terpadu (Presensi, Tata Tertib & Karakter) - {{ $selectedClass?->name ?? 'Semua Kelas' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: 9.5pt;
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        /* Controls Bar (Hidden in Print) */
        .no-print-bar {
            background: #0f172a;
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
            background: #1e293b;
            color: #fff;
            font-size: 12px;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-print:hover { background: #059669; }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-back:hover { background: #334155; }
        .btn-filter {
            background: #6366f1;
            color: #fff;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        /* Printable Document Container */
        .doc-container {
            max-width: 297mm;
            margin: 0 auto;
            padding: 10px 15px;
        }

        /* KOP Surat */
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
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
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-header .provinsi {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .dinas {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .sekolah {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .kop-header .alamat {
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
            margin-top: 2px;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title h2 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-title p {
            font-size: 9pt;
            margin: 2px 0 0 0;
        }

        /* Meta Table */
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Table Laporan */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }
        table.report-table th, table.report-table td {
            border: 1px solid #333;
            padding: 4px 3px;
        }
        table.report-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        table.report-table tfoot td {
            font-weight: bold;
            background-color: #f8fafc;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Signatures */
        .signatures {
            width: 100%;
            margin-top: 15px;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .signatures td {
            vertical-align: top;
            padding: 5px 15px;
        }

        @media print {
            .no-print-bar { display: none !important; }
            body { background: #fff !important; }
            .doc-container { padding: 0 !important; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (Screen Only) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('reports.index') }}" class="btn-back">← Kembali ke Pusat Laporan</a>
            <span style="font-weight: bold; font-size: 14px;">Dokumen Terpadu: Presensi, Tata Tertib & 7 Kebiasaan</span>
        </div>

        <form action="{{ route('reports.print.combined') }}" method="GET">
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $selectedClass?->name ?? 'Kelas Binaan' }}" readonly style="cursor: not-allowed; width: 110px;">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                @endif
            @endif

            <input type="date" name="start_date" value="{{ $startDate }}">
            <input type="date" name="end_date" value="{{ $endDate }}">

            <button type="submit" class="btn-filter">Terapkan</button>
            <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen (A4)</button>
        </form>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($kopImageUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $kopImageUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
        @else
            <div class="kop-header">
                <div class="provinsi">PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</div>
                <div class="dinas">DINAS PENDIDIKAN</div>
                <div class="cabang" style="font-weight: bold; font-size: 10pt; letter-spacing: 0.5px;">{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</div>
                <div class="sekolah">{{ $schoolName }}</div>
                <div class="npsn" style="font-size: 8.5pt; margin-top: 1px;">NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</div>
                <div class="alamat">{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</div>
            </div>
        @endif

        <!-- Judul Dokumen -->
        <div class="doc-title">
            <h2>LAPORAN KOMPREHENSIF PERKEMBANGAN & KARAKTER SISWA</h2>
            <p>INTEGRASI REKAPITULASI PRESENSI, TATA TERTIB (PRESTASI & SANKSI), DAN GERAKAN 7 KEBIASAAN ANAK INDONESIA HEBAT</p>
        </div>

        <!-- Meta Table -->
        <table class="meta-table">
            <tr>
                <td style="width: 15%;"><strong>Rombongan Belajar</strong></td>
                <td style="width: 35%;">: {{ $selectedClass ? 'Kelas '.$selectedClass->name : 'Seluruh Kelas' }}</td>
                <td style="width: 15%;"><strong>Periode Pengamatan</strong></td>
                <td style="width: 35%;">: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Wali Kelas</strong></td>
                <td>: {{ $selectedClass?->homeroomTeacher?->name ?? 'Tim Kesiswaan' }}</td>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>

        <!-- Table Utama Laporan Terpadu -->
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">No</th>
                    <th rowspan="2" style="width: 60px;">NIS</th>
                    <th rowspan="2" style="width: 150px;">Nama Siswa</th>
                    <th rowspan="2" style="width: 50px;">Kelas</th>
                    <th colspan="6" style="background: #e2e8f0;">I. REKAPITULASI PRESENSI</th>
                    <th colspan="4" style="background: #fef3c7;">II. TATA TERTIB SEKOLAH</th>
                    <th colspan="9" style="background: #ccfbf1;">III. 7 KEBIASAAN ANAK HEBAT</th>
                </tr>
                <tr>
                    <!-- Presensi Columns -->
                    <th style="width: 20px;" title="Hadir Tepat Waktu">H</th>
                    <th style="width: 20px;" title="Terlambat">T</th>
                    <th style="width: 20px;" title="Izin">I</th>
                    <th style="width: 20px;" title="Sakit">S</th>
                    <th style="width: 20px;" title="Alpa/Tanpa Keterangan">A</th>
                    <th style="width: 38px;">% Hdr</th>

                    <!-- Tata Tertib Columns -->
                    <th style="width: 35px;" title="Poin Apresiasi Prestasi">+Prest</th>
                    <th style="width: 75px;">Status Apresiasi</th>
                    <th style="width: 35px;" title="Poin Pelanggaran Tata Tertib">-Pelang</th>
                    <th style="width: 75px;">Tahap Pembinaan</th>

                    <!-- 7 Kebiasaan Columns -->
                    <th style="width: 18px;" title="K1: Bangun Pagi">K1</th>
                    <th style="width: 18px;" title="K2: Beribadah">K2</th>
                    <th style="width: 18px;" title="K3: Berolahraga">K3</th>
                    <th style="width: 18px;" title="K4: Makan Sehat">K4</th>
                    <th style="width: 18px;" title="K5: Gemar Membaca">K5</th>
                    <th style="width: 18px;" title="K6: Bermasyarakat">K6</th>
                    <th style="width: 18px;" title="K7: Tidur Tepat Waktu">K7</th>
                    <th style="width: 35px;">% Kns</th>
                    <th style="width: 60px;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($combinedRecaps as $idx => $r)
                    @php $s = $r['student']; @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center font-bold">{{ $s->nis }}</td>
                        <td>{{ $s->name }}</td>
                        <td class="center">{{ $s->schoolClass?->name ?? '-' }}</td>

                        <!-- Presensi -->
                        <td class="center">{{ $r['hadir'] }}</td>
                        <td class="center">{{ $r['terlambat'] }}</td>
                        <td class="center">{{ $r['izin'] }}</td>
                        <td class="center">{{ $r['sakit'] }}</td>
                        <td class="center font-bold {{ $r['alpa'] > 0 ? 'color: #e11d48;' : '' }}">{{ $r['alpa'] }}</td>
                        <td class="center font-bold">{{ $r['presence_pct'] }}%</td>

                        <!-- Tata Tertib -->
                        <td class="center font-bold" style="color: #047857;">+{{ $r['ach_points'] }}</td>
                        <td class="center" style="font-size: 7.5pt;">{{ $r['reward_status']['level'] }}</td>
                        <td class="center font-bold" style="color: #b91c1c;">-{{ $r['vio_points'] }}</td>
                        <td class="center" style="font-size: 7.5pt;">{{ $r['guidance_status']['level'] }}</td>

                        <!-- 7 Kebiasaan -->
                        @foreach($habits as $h)
                            <td class="center" style="font-size: 8pt;">{{ $r['per_habit'][$h->id] ?? 0 }}</td>
                        @endforeach
                        <td class="center font-bold">{{ $r['habits_pct'] }}%</td>
                        <td class="center" style="font-size: 8pt;">{{ $r['habit_badge'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="23" class="center" style="padding: 15px;">Tidak ada data peserta didik pada kriteria filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="center">TOTAL / RATA-RATA KELAS</td>
                    <!-- Presensi Totals -->
                    <td class="center">{{ $totals['hadir'] }}</td>
                    <td class="center">{{ $totals['terlambat'] }}</td>
                    <td class="center">{{ $totals['izin'] }}</td>
                    <td class="center">{{ $totals['sakit'] }}</td>
                    <td class="center">{{ $totals['alpa'] }}</td>
                    <td class="center">{{ $totals['avg_presence'] }}%</td>

                    <!-- Tata Tertib Totals -->
                    <td class="center" style="color: #047857;">+{{ $totals['ach_points'] }}</td>
                    <td class="center">{{ $totals['ach_count'] }} Kegiatan</td>
                    <td class="center" style="color: #b91c1c;">-{{ $totals['vio_points'] }}</td>
                    <td class="center">{{ $totals['vio_count'] }} Kasus</td>

                    <!-- 7 Kebiasaan Totals -->
                    <td colspan="7" class="center">{{ $days }} Hari Pengamatan (Target {{ 7 * $days }} Checklist / Siswa)</td>
                    <td class="center">{{ $totals['avg_habits'] }}%</td>
                    <td class="center">Terpantau</td>
                </tr>
            </tfoot>
        </table>

        <!-- Keterangan Singkat -->
        <div style="font-size: 8pt; color: #444; margin-top: 4px; line-height: 1.3;">
            <em><strong>Keterangan:</strong> [Presensi] H: Hadir, T: Terlambat, I: Izin, S: Sakit, A: Alpa. [Tata Tertib] Poin Prestasi dan Pelanggaran dihitung terpisah secara mutlak tanpa saling menghapus. Batas penghargaan: ≥125 Poin. Batas pembinaan: ≥75 Poin (Wali Kelas), ≥125 Poin (SP1 BK), ≥176 Poin (SP2), 200 Poin (SP3), >200 Poin (Sidang Pleno). [7 Kebiasaan] K1: Bangun Pagi, K2: Beribadah, K3: Berolahraga, K4: Makan Sehat, K5: Gemar Membaca, K6: Bermasyarakat, K7: Tidur Cepat.</em>
        </div>

        <!-- 3 Kolom Tanda Tangan Resmi Pengesahan -->
        <table class="signatures">
            <tr>
                <td style="width: 33%; text-align: center;">
                    Mengetahui,<br>
                    <strong>Kepala {{ $schoolName }}</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName }}</span><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 33%; text-align: center;">
                    Mengetahui,<br>
                    <strong>Koordinator BK / Kesiswaan</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">Budi Santoso, S.Pd., M.Kons.</span><br>
                    NIP. 19750814 200501 1 008
                </td>
                <td style="width: 34%; text-align: center;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    @if($selectedClass && $selectedClass->homeroomTeacher)
                        <strong>Wali Kelas {{ $selectedClass->name }}</strong>
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ $selectedClass->homeroomTeacher->name }}</span><br>
                        NIP. {{ $selectedClass->homeroomTeacher->nip ?? '-' }}
                    @else
                        <strong>Petugas Administrator Sistem</strong>
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ Auth::user()->name }}</span><br>
                        NIP/ID. {{ Auth::user()->username }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
