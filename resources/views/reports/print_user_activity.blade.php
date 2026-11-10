<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Frekuensi Akses & Aktivitas Pengguna Sistem — {{ $schoolName }}</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image')));
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
        .no-print-bar {
            background: #1e293b;
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
        .print-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px 0;
        }
        .kop-surat-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 6px;
        }
        .kop-surat-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-td {
            width: 85px;
            text-align: center;
        }
        .logo-img {
            width: 75px;
            height: auto;
            max-height: 85px;
            object-fit: contain;
        }
        .kop-text-td {
            text-align: center;
            padding: 0 10px;
        }
        .kop-prov {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-dinas {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }
        .kop-school {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }
        .kop-address {
            font-size: 8.5pt;
            font-style: italic;
            font-family: Arial, Helvetica, sans-serif;
            margin: 2px 0 0 0;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0 12px 0;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .doc-title p {
            font-size: 10pt;
            margin: 3px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2.5px 4px;
            border: none;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #111;
            padding: 4px 6px;
        }
        .data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .center { text-align: center; }
        .signatures {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td {
            vertical-align: top;
            border: none;
            font-size: 10pt;
            line-height: 1.4;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { font-size: 10pt; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar -->
    <div class="no-print-bar">
        <div style="font-weight: bold;">
            Dokumen Laporan Audit Log & Frekuensi Akses Pengguna — {{ $schoolName }}
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ ! $isF4 ? 'active' : 'inactive' }}">A4</a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">F4</a>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak / Unduh PDF
            </button>
        </div>
    </div>

    <div class="print-container">
        <!-- Kop Surat Resmi -->
        @if($actualKopUrl)
            <div style="text-align: center; margin-bottom: 10px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="Kop Surat" style="width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <table class="kop-surat-table">
                <tr>
                    <td class="logo-td">
                        <img src="{{ asset('images/logo_jabar.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" class="logo-img" alt="Logo">
                    </td>
                    <td class="kop-text-td">
                        <div class="kop-prov">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                        <div class="kop-dinas">DINAS PENDIDIKAN</div>
                        <div class="kop-prov">{{ $schoolBranch }}</div>
                        <div class="kop-school">{{ $schoolName }}</div>
                        <div class="kop-address">{{ $schoolAddress }} | Email: {{ $schoolEmail }}</div>
                    </td>
                    <td class="logo-td">
                        <img src="{{ asset('images/logo_sma1le.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" class="logo-img" alt="Logo SMA1LE">
                    </td>
                </tr>
            </table>
        @endif

        <!-- Judul Dokumen -->
        <div class="doc-title">
            <h2>REKAPITULASI FREKUENSI AKSES & AKTIVITAS PENGGUNA APLIKASI</h2>
            <p>
                Kategori: <strong>{{ strtoupper($roleCategory === 'all' ? 'Semua Pengguna' : ($roleCategory === 'guru' ? 'Tenaga Pendidik / Guru' : ($roleCategory === 'administrasi' ? 'Tenaga Kependidikan / Administrasi' : 'Peserta Didik / Siswa'))) }}</strong>
                @if($startDate || $endDate)
                    • Periode: {{ $startDate ?: 'Awal' }} s.d. {{ $endDate ?: 'Sekarang' }}
                @endif
            </p>
        </div>

        <!-- Ringkasan Statistik -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9pt; border: 1px solid #111;">
            <tr style="background: #f8fafc; font-weight: bold; text-align: center;">
                <td style="padding: 5px; border: 1px solid #111;">Total Pengguna Terdata: {{ $summary['total_users'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #047857;">Pernah Masuk (Aktif): {{ $summary['active_users_count'] }}</td>
                <td style="padding: 5px; border: 1px solid #111; color: #2563eb;">Total Masuk (Login): {{ number_format($summary['total_logins'], 0, ',', '.') }} Kali</td>
                <td style="padding: 5px; border: 1px solid #111; color: #7c3aed;">Total Interaksi Aktivitas: {{ number_format($summary['total_activities'], 0, ',', '.') }} Aksi</td>
            </tr>
        </table>

        <!-- Tabel Data Pengguna -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 32%;">Nama & Identitas Akun</th>
                    <th style="width: 18%;">Peran / Kategori</th>
                    <th style="width: 15%;">Frekuensi Masuk (Login)</th>
                    <th style="width: 15%;">Total Aktivitas Sistem</th>
                    <th style="width: 16%;">Terakhir Masuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $idx => $u)
                    @php
                        $isGuru = $u->teacher || in_array($u->role, ['guru', 'wali_kelas', 'pembina_ekstra', 'koordinator_keagamaan', 'bk']);
                        $isSiswa = $u->student || in_array($u->role, ['siswa', 'sekretaris_kelas', 'sekretaris_ekstra']);
                        $roleLabel = $isGuru ? 'Guru' : ($isSiswa ? 'Siswa' : 'Tenaga Administrasi');
                    @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $u->name }}</strong><br>
                            <span style="font-size: 8pt; color: #444;">Username: {{ $u->username }}</span>
                        </td>
                        <td class="center">
                            {{ $roleLabel }}<br>
                            <span style="font-size: 8pt; color: #555;">({{ strtoupper(str_replace('_', ' ', $u->role)) }})</span>
                        </td>
                        <td class="center" style="font-weight: bold; color: {{ $u->login_count > 0 ? '#047857' : '#999' }};">
                            {{ $u->login_count }} Kali
                        </td>
                        <td class="center" style="font-weight: bold; color: {{ $u->total_activities_count > 0 ? '#1d4ed8' : '#999' }};">
                            {{ $u->total_activities_count }} Aksi
                        </td>
                        <td class="center" style="font-size: 8.5pt;">
                            @if($u->lastLoginAudit)
                                {{ $u->lastLoginAudit->created_at->format('d/m/Y H:i') }}
                            @else
                                <span style="color: #999; font-style: italic;">Belum Pernah</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- POSISI TANDA TANGAN: KEPALA SEKOLAH SELALU DI SEBELAH KIRI -->
        <table class="signatures">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 15px;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName }}</span><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 50%; text-align: right; padding-right: 15px;">
                    {{ $schoolCity }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Administrator Sistem Informasi</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Administrator' }}</span><br>
                    NIP / NUPTK. {{ auth()->user()->username ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
