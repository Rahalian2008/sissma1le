<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kontrol Setoran Tahfidz Juz 30 - {{ $student->name }} (NISN: {{ $student->nisn }}) [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $headmasterName = $headmasterName ?? (\App\Models\SchoolSetting::get('headmaster_name') ?? ($school['headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd'));
        $headmasterNip = $headmasterNip ?? (\App\Models\SchoolSetting::get('headmaster_nip') ?? ($school['headmaster_nip'] ?? '198009132008011005'));
        $schoolProvince = $schoolProvince ?? ($school['school_province'] ?? 'JAWA BARAT');
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
            padding: 3.5px 5px;
        }
        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .signature-grid {
            width: 100%;
            margin-top: 20px;
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
            <strong>Pratinjau Kartu Tahfidz:</strong> {{ $student->name }} (Progres: {{ $completedSurahs }}/{{ $totalSurahs }} Surat - {{ $completionPercentage }}%)
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
            <a href="{{ route('tahfidz.student', $student->id) }}" class="btn-back">← Kembali ke Profil</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Kartu Siswa (Print/PDF)</button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat" style="max-width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <div class="kop-header">
                <p style="text-transform: uppercase; font-size: 10pt; margin: 0;">PEMERINTAH PROVINSI {{ $schoolProvince }}<br>DINAS PENDIDIKAN<br>CABANG DINAS PENDIDIKAN WILAYAH V</p>
                <h3>{{ $school['school_name'] ?? 'SMA NEGERI 1 LENGKONG' }}</h3>
                <p>{{ $school['school_address'] ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }} | Telp: {{ $school['school_phone'] ?? '-' }}</p>
            </div>
        @endif

        <div class="title-block">
            <h2>BUKU KONTROL DIGITAL SETOR HAFALAN AL-QUR'AN (JUZ 30)</h2>
            <p style="margin: 2px 0 0 0; font-size: 9.5pt;">Program Peningkatan Karakter & Mutu Keagamaan Siswa</p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 18%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;" class="font-bold">{{ $student->name }}</td>
                <td style="width: 18%;">Kelas</td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;" class="font-bold">{{ $student->schoolClass?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>NISN / NIS</td>
                <td>:</td>
                <td>{{ $student->nisn }} / {{ $student->nis }}</td>
                <td>Wali Kelas</td>
                <td>:</td>
                <td>{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Target Kelas {{ $studentGrade }}</td>
                <td>:</td>
                <td class="font-bold">
                    <span style="color: #047857;">{{ $targetGradeCompleted }} dari {{ $targetGradeTotal }} Surat Selesai ({{ $targetGradePercentage }}%)</span>
                    <div style="font-size: 8pt; color: #0284c7; font-weight: bold; margin-top: 1px;">Ayat Tuntas: {{ $targetGradeCompletedAyat }} / {{ $targetGradeTotalAyat }} Ayat ({{ $targetGradeAyatPercentage }}%)</div>
                </td>
                <td>Akumulasi Juz 30</td>
                <td>:</td>
                <td>
                    <strong>{{ $completedSurahs }} / {{ $totalSurahs }} Surat ({{ $completionPercentage }}%)</strong>
                    <div style="font-size: 8pt; color: #555;">Tahun: {{ $student->academicYear?->name ?? '2026/2027' }}</div>
                </td>
            </tr>
        </table>

        <!-- Tabel 37 Surat Juz 30 -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 22%;">Nama Surat & Jml Ayat</th>
                    <th style="width: 13%;">Tgl Setor</th>
                    <th style="width: 10%;">Makhraj</th>
                    <th style="width: 10%;">Tajwid</th>
                    <th style="width: 10%;">Kelancaran</th>
                    <th style="width: 10%;">Hasil</th>
                    <th style="width: 21%;">Penguji / Paraf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surahs as $num => $s)
                    @php
                        $st = $surahStatus[$num] ?? ['has_deposited' => false, 'is_lulus' => false, 'latest' => null];
                        $latest = $st['latest'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $num }}</td>
                        <td class="font-bold">
                            {{ $s['name'] }} <span style="font-weight: normal; font-size: 8pt; color: #555;">({{ $s['total_ayah'] }} ayat)</span>
                            <span style="font-size: 7.5pt; font-weight: bold; background: #e2e8f0; padding: 1px 3px; border-radius: 3px; margin-left: 2px;">{{ $s['grade'] ?? 'X' }}</span>
                            @if($latest?->ayat_count)
                                <div style="font-size: 7.5pt; font-weight: normal; color: #047857;">Disetor: {{ $latest->ayat_count }} Ayat ({{ $latest->ayat_range }})</div>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $latest ? \Carbon\Carbon::parse($latest->deposit_date)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">{{ $latest?->makhraj ?? '-' }}</td>
                        <td class="text-center">{{ $latest?->tajwid ?? '-' }}</td>
                        <td class="text-center">{{ $latest?->kelancaran ?? '-' }}</td>
                        <td class="text-center font-bold">
                            @if($st['is_lulus'])
                                <span style="color: #047857;">LULUS</span>
                            @elseif($st['has_deposited'])
                                <span style="color: #b45309;">MENGULANG</span>
                            @else
                                <span style="color: #64748b;">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $latest?->examiner_name ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures -->
        <table class="signature-grid">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br><br>
                    <strong><u>{{ $headmasterName }}</u></strong><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td>
                    @php
                        $printUser = Auth::user();
                        $signerRole = 'Petugas / Guru Pembina Tahfidz';

                        if ($printUser?->hasRole('wali_kelas')) {
                            $wkName = $student->schoolClass?->name ?? ($printUser->teacher?->homeroomClass?->name ?? '');
                            $signerRole = $wkName ? 'Wali Kelas ' . $wkName : 'Wali Kelas';
                        } elseif ($printUser?->hasRole('koordinator_keagamaan')) {
                            $signerRole = 'Koordinator Keagamaan';
                        } elseif ($printUser?->hasRole('guru')) {
                            $signerRole = 'Guru Pembina Tahfidz';
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

                        $signerName = $printUser?->teacher?->name ?? ($printUser?->name ?? 'Guru Pembina Tahfidz');
                        if ($printUser?->hasRole('wali_kelas') && !empty($student->schoolClass?->homeroomTeacher)) {
                            $signerName = $student->schoolClass->homeroomTeacher->name;
                            $signerNip = $student->schoolClass->homeroomTeacher->nip;
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
