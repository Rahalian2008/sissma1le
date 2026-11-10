<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Setoran Tahfidz Al-Qur'an (Juz 30) - {{ $selectedClass?->name ?? 'Semua Kelas' }} [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $headmasterName = $headmasterName ?? (\App\Models\SchoolSetting::get('headmaster_name') ?? ($school['headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd'));
        $headmasterNip = $headmasterNip ?? (\App\Models\SchoolSetting::get('headmaster_nip') ?? ($school['headmaster_nip'] ?? '198009132008011005'));
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.4cm' : '1.1cm 1.4cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '10pt' : '9.5pt' }};
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            flex-wrap: wrap;
            gap: 10px;
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
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .doc-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 10px 20px;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
        }
        .title-block {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-block h2 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 4px 6px;
        }
        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .summary-box {
            display: flex;
            gap: 15px;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .summary-box div {
            border: 1px solid #666;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .signature-grid {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-grid td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 9.5pt;
        }
        @media print {
            .no-print-bar { display: none !important; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Dokumen Laporan:</strong> Rekapitulasi Tahfidz Juz 30 ({{ $records->count() }} Data)
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210×297mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215×330mm)
                </a>
            </div>
            <a href="{{ route('tahfidz.report') }}" class="btn-back">← Kembali ke Laporan</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen (PDF/Print)</button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper" style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat" style="max-width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <div class="kop-header">
                <p style="text-transform: uppercase; font-size: 10pt; margin: 0;">PEMERINTAH PROVINSI {{ $schoolProvince ?? ($school['school_province'] ?? 'JAWA BARAT') }}<br>DINAS PENDIDIKAN</p>
                <h3>{{ $schoolName ?? ($school['school_name'] ?? 'SMA NEGERI 1 LENGKONG') }}</h3>
                <p>{{ $schoolAddress ?? ($school['school_address'] ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat') }} | Email: {{ $schoolEmail ?? ($school['school_email'] ?? 'sman1lengkong@gmail.com') }}</p>
            </div>
        @endif

        <div class="title-block">
            <h2>LAPORAN REKAPITULASI SETORAN HAFALAN AL-QUR'AN (JUZ 30)</h2>
            <p style="margin: 2px 0 0 0; font-size: 9.5pt;">Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }}</strong> s.d. <strong>{{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}</strong></p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 18%;">Kelas</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;" class="font-bold">{{ $selectedClass?->name ?? 'Semua Kelas' }}</td>
                <td style="width: 18%;">Target Juz</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;" class="font-bold">Juz 30 ('Amma)</td>
            </tr>
            <tr>
                <td>Waktu Cetak</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') }} WIB</td>
                <td>Wali Kelas</td>
                <td>:</td>
                <td>{{ $selectedClass?->homeroomTeacher?->name ?? '-' }}</td>
            </tr>
        </table>

        <!-- Ringkasan Singkat -->
        <div class="summary-box">
            <div>Total Log Setoran: <strong>{{ $stats['total'] }}</strong></div>
            <div>Lulus (Mutqin): <strong>{{ $stats['lulus'] }}</strong></div>
            <div>Perlu Mengulang: <strong>{{ $stats['mengulang'] }}</strong></div>
            <div>Siswa Terdata: <strong>{{ $stats['students_count'] }}</strong></div>
        </div>

        <!-- Tabel Rekapitulasi -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 13%;">Tgl Setor</th>
                    <th style="width: 11%;">NISN</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 8%;">Kelas</th>
                    <th style="width: 16%;">Surat & Ayat</th>
                    <th style="width: 12%;">Penilaian (M/T/K)</th>
                    <th style="width: 7%;">Hasil</th>
                    <th style="width: 9%;">Penguji</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $idx => $r)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($r->deposit_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $r->student?->nisn }}</td>
                        <td class="font-bold">{{ $r->student?->name }}</td>
                        <td class="text-center">{{ $r->student?->schoolClass?->name ?? '-' }}</td>
                        <td>{{ $r->surah_name }} ({{ $r->ayat_range }})</td>
                        <td class="text-center" style="font-size: 8pt;">{{ $r->makhraj }}/{{ $r->tajwid }}/{{ $r->kelancaran }}</td>
                        <td class="text-center font-bold">
                            @if($r->overall_grade === 'Lulus')
                                <span style="color: #047857;">LULUS</span>
                            @else
                                <span style="color: #b45309;">RE</span>
                            @endif
                        </td>
                        <td>{{ $r->examiner_name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 15px;">Tidak ada data setoran hafalan pada periode dan filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Resmi -->
        <table class="signature-grid">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br><br>
                    <strong><u>{{ $headmasterName ?? (\App\Models\SchoolSetting::get('headmaster_name') ?? ($school['headmaster_name'] ?? 'Drs. H. Ahmad Sudrajat, M.Pd.')) }}</u></strong><br>
                    NIP. {{ $headmasterNip ?? (\App\Models\SchoolSetting::get('headmaster_nip') ?? ($school['headmaster_nip'] ?? '-')) }}
                </td>
                <td>
                    @php
                        $printUser = Auth::user();
                        $signerRole = 'Petugas Pencatat';

                        if ($printUser?->hasRole('wali_kelas')) {
                            $wkName = $selectedClass?->name ?? ($printUser->teacher?->homeroomClass?->name ?? '');
                            $signerRole = $wkName ? 'Wali Kelas ' . $wkName : 'Wali Kelas';
                        } elseif ($printUser?->hasRole('koordinator_keagamaan')) {
                            $signerRole = 'Koordinator Keagamaan';
                        } elseif ($printUser?->hasRole('guru')) {
                            $signerRole = 'Guru Pembina / Mata Pelajaran';
                        } elseif ($printUser?->hasRole('kesiswaan')) {
                            $signerRole = 'Pembina Kesiswaan';
                        } elseif ($printUser?->hasRole('bk')) {
                            $signerRole = 'Guru Bimbingan Konseling';
                        } elseif ($printUser?->hasRole('kepala_sekolah')) {
                            $signerRole = 'Kepala Sekolah';
                        } elseif ($printUser?->hasAnyRole(['admin', 'super_admin'])) {
                            $signerRole = 'Administrator Sistem';
                        } elseif ($printUser?->hasRole('sekretaris_kelas')) {
                            $signerRole = 'Sekretaris Kelas ' . ($printUser->student?->schoolClass?->name ?? '');
                        }

                        $signerName = $printUser?->teacher?->name ?? ($printUser?->name ?? 'Petugas Pencatat');
                        if ($printUser?->hasRole('wali_kelas') && !empty($selectedClass?->homeroomTeacher)) {
                            $signerName = $selectedClass->homeroomTeacher->name;
                            $signerNip = $selectedClass->homeroomTeacher->nip;
                        } else {
                            $signerNip = $printUser?->teacher?->nip ?? ($printUser?->username ?? '-');
                        }
                    @endphp
                    {{ $school['school_city'] ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}<br>
                    <strong>{{ $signerRole }}</strong>
                    <br><br><br><br>
                    <strong><u>{{ $signerName }}</u></strong><br>
                    NIP. {{ $signerNip ?: '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
