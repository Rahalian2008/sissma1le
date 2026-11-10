<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terpadu (Presensi, Tata Tertib, Karakter, Ibadah & Tahfidz) - {{ $selectedClass?->name ?? 'Semua Kelas' }} [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '330mm 215mm' : 'A4' }} landscape;
            margin: {{ $isF4 ? '0.8cm 1.2cm' : '0.9cm 1.2cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '9pt' : '8.5pt' }};
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        /* Controls Bar (Hidden in Print) */
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 18px;
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
            max-width: {{ $isF4 ? '330mm' : '297mm' }};
            margin: 0 auto;
            padding: 8px 12px;
        }

        /* KOP Surat */
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 3px double #000;
            padding-bottom: 4px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .kop-header .provinsi {
            font-size: 10.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .dinas {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .sekolah {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .kop-header .alamat {
            font-size: 8pt;
            font-style: italic;
            color: #333;
            margin-top: 2px;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin-bottom: 8px;
        }
        .doc-title h2 {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-title p {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
        }

        /* Meta Table */
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Table Laporan */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 8px;
        }
        table.report-table th, table.report-table td {
            border: 1px solid #333;
            padding: 3px 2px;
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
            margin-top: 12px;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .signatures td {
            vertical-align: top;
            padding: 4px 12px;
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
            <span style="font-weight: bold; font-size: 14px;">Dokumen Terpadu: Presensi, Tata Tertib, 7 Kebiasaan, Ibadah & Tahfidz</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (297×210mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (330×215mm)
                </a>
            </div>

            <form action="{{ route('reports.print.combined') }}" method="GET">
                <input type="hidden" name="paper" value="{{ request('paper', 'a4') }}">
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
                <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak ({{ strtoupper(request('paper', 'a4')) }})</button>
            </form>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
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
            <p>INTEGRASI REKAPITULASI PRESENSI UTAMA, TATA TERTIB, GERAKAN 7 KEBIASAAN, PERIBADATAN (DZUHUR, DHUHA, KEPUTRIAN), TAHFIDZ JUZ 30, DAN EKSTRAKURIKULER</p>
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
                    <th rowspan="2" style="width: 22px;">No</th>
                    <th rowspan="2" style="width: 55px;">NIS</th>
                    <th rowspan="2" style="width: 135px;">Nama Siswa</th>
                    <th rowspan="2" style="width: 45px;">Kelas</th>
                    <th colspan="6" style="background: #e2e8f0;">I. REKAPITULASI PRESENSI</th>
                    <th colspan="4" style="background: #fef3c7;">II. TATA TERTIB SEKOLAH</th>
                    <th colspan="9" style="background: #ccfbf1;">III. 7 KEBIASAAN ANAK HEBAT</th>
                    <th colspan="4" style="background: #dcfce7;">IV. PERIBADATAN & TAHFIDZ</th>
                    <th colspan="2" style="background: #cffafe;">V. EKSTRAKURIKULER</th>
                </tr>
                <tr>
                    <!-- Presensi Columns -->
                    <th style="width: 18px;" title="Hadir Tepat Waktu">H</th>
                    <th style="width: 18px;" title="Terlambat">T</th>
                    <th style="width: 18px;" title="Izin">I</th>
                    <th style="width: 18px;" title="Sakit">S</th>
                    <th style="width: 18px;" title="Alpa/Tanpa Keterangan">A</th>
                    <th style="width: 35px;">% Hdr</th>

                    <!-- Tata Tertib Columns -->
                    <th style="width: 32px;" title="Poin Apresiasi Prestasi">+Prest</th>
                    <th style="width: 70px;">Status Apresiasi</th>
                    <th style="width: 32px;" title="Poin Pelanggaran Tata Tertib">-Pelang</th>
                    <th style="width: 70px;">Tahap Pembinaan</th>

                    <!-- 7 Kebiasaan Columns -->
                    <th style="width: 16px;" title="K1: Bangun Pagi">K1</th>
                    <th style="width: 16px;" title="K2: Beribadah">K2</th>
                    <th style="width: 16px;" title="K3: Berolahraga">K3</th>
                    <th style="width: 16px;" title="K4: Makan Sehat">K4</th>
                    <th style="width: 16px;" title="K5: Gemar Membaca">K5</th>
                    <th style="width: 16px;" title="K6: Bermasyarakat">K6</th>
                    <th style="width: 16px;" title="K7: Tidur Tepat Waktu">K7</th>
                    <th style="width: 32px;">% Kns</th>
                    <th style="width: 55px;">Predikat</th>

                    <!-- Peribadatan & Tahfidz Columns -->
                    <th style="width: 25px;" title="Presensi Shalat Dzuhur / Jum'at">Dzuhur</th>
                    <th style="width: 25px;" title="Presensi Shalat Dhuha">Dhuha</th>
                    <th style="width: 28px;" title="Presensi Keputrian (Khusus Siswi Putri)">Keputrian</th>
                    <th style="width: 42px;" title="Target Capaian Surat Kurikulum Kelas">Tahfidz</th>

                    <!-- Ekstrakurikuler Columns -->
                    <th style="width: 75px;" title="Nama Ekstrakurikuler yang Diikuti">Ekskul</th>
                    <th style="width: 25px;" title="Jumlah Kehadiran Presensi Ekstrakurikuler">Hdr</th>
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
                            <td class="center" style="font-size: 7.5pt;">{{ $r['per_habit'][$h->id] ?? 0 }}</td>
                        @endforeach
                        <td class="center font-bold">{{ $r['habits_pct'] }}%</td>
                        <td class="center" style="font-size: 7.5pt;">{{ $r['habit_badge'] }}</td>

                        <!-- Peribadatan & Tahfidz -->
                        <td class="center">{{ $r['dzuhur_hadir'] ?? 0 }}</td>
                        <td class="center">{{ $r['dhuha_hadir'] ?? 0 }}</td>
                        <td class="center">
                            @if($r['is_male'] ?? true)
                                <span style="color: #94a3b8; font-weight: bold;" title="Tidak berlaku untuk siswa laki-laki">-</span>
                            @else
                                <span class="font-bold">{{ $r['keputrian_hadir'] ?? 0 }}</span>
                            @endif
                        </td>
                        <td class="center font-bold" style="font-size: 7.5pt; color: #047857;" title="Capaian Surat: {{ $r['tahfidz_completed_surahs'] ?? 0 }} dari {{ $r['tahfidz_target_surahs'] ?? 0 }} Surat Target Kelas {{ $r['student_grade'] ?? '-' }}">
                            {{ $r['tahfidz_completed_surahs'] ?? 0 }}/{{ $r['tahfidz_target_surahs'] ?? 0 }}
                        </td>

                        <!-- Ekstrakurikuler -->
                        <td class="center" style="font-size: 7pt; max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $r['extra_names'] ?? '-' }}">
                            {{ $r['extra_names'] ?? '-' }}
                        </td>
                        <td class="center font-bold">{{ $r['extra_hadir'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="29" class="center" style="padding: 15px;">Tidak ada data peserta didik pada kriteria filter yang dipilih.</td>
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

                    <!-- Peribadatan & Tahfidz Totals -->
                    <td class="center">{{ $totals['dzuhur'] ?? 0 }}</td>
                    <td class="center">{{ $totals['dhuha'] ?? 0 }}</td>
                    <td class="center" title="Total Kehadiran Keputrian ({{ $totals['female_students'] ?? 0 }} Siswi Putri)">
                        {{ $totals['keputrian'] ?? 0 }} <span style="font-size: 7pt; font-weight: normal; color: #64748b;">({{ $totals['female_students'] ?? 0 }}P)</span>
                    </td>
                    <td class="center font-bold" style="color: #047857; font-size: 7.5pt;" title="Total Surat Lulus Terakumulasi ({{ $totals['tahfidz_period_lulus'] ?? 0 }} Lulus Pada Periode Ini)">
                        {{ $totals['tahfidz_lulus'] ?? 0 }} Srt
                    </td>

                    <!-- Ekstrakurikuler Totals -->
                    <td colspan="2" class="center font-bold" title="Total Kehadiran Presensi Ekstrakurikuler">
                        {{ $totals['extra_hadir'] ?? 0 }} Hadir
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Keterangan Singkat -->
        <div style="font-size: 7.5pt; color: #444; margin-top: 4px; line-height: 1.3;">
            <em><strong>Keterangan:</strong> [Presensi] H: Hadir, T: Terlambat, I: Izin, S: Sakit, A: Alpa. [Tata Tertib] Poin Prestasi dan Pelanggaran dihitung terpisah secara mutlak tanpa saling menghapus. Batas penghargaan: ≥125 Poin. Batas pembinaan: ≥75 Poin (Wali Kelas), ≥125 Poin (SP1 BK), ≥176 Poin (SP2), 200 Poin (SP3), >200 Poin (Sidang Pleno). [7 Kebiasaan] K1: Bangun Pagi, K2: Beribadah, K3: Berolahraga, K4: Makan Sehat, K5: Gemar Membaca, K6: Bermasyarakat, K7: Tidur Cepat. [Peribadatan & Tahfidz] Dzuhur & Dhuha dihitung dari total kehadiran ibadah. Presensi Keputrian khusus siswi putri (tanda '-' untuk siswa laki-laki). Tahfidz menampilkan rasio surat mutqin terhadap target kurikulum kelasnya. [Ekstrakurikuler] Menampilkan nama ekskul yang diikuti serta akumulasi kehadiran presensi latihan ekstrakurikuler siswa.</em>
        </div>

        <!-- 2 Kolom Tanda Tangan Resmi Pengesahan (Kepala Sekolah & Wakasek Bid. Kesiswaan) -->
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
