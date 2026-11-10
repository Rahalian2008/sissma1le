<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota Perpustakaan - {{ $member->name }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 15mm auto; max-width: 800px; color: #1e293b; }
        .no-print { text-align: right; margin-bottom: 20px; }
        .no-print button { padding: 8px 18px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .card-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .library-card {
            width: 330px;
            height: 205px;
            border-radius: 12px;
            border: 2px solid #312e81;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #1e1b4b 100%);
            color: #fff;
            padding: 12px 14px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            page-break-inside: avoid;
        }
        .card-header { display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.25); padding-bottom: 6px; margin-bottom: 8px; }
        .card-logo { width: 34px; height: 34px; object-fit: contain; }
        .card-header-text h3 { margin: 0; font-size: 8pt; text-transform: uppercase; letter-spacing: 0.5px; color: #fbbf24; }
        .card-header-text h2 { margin: 0; font-size: 9.5pt; font-weight: 800; }
        .card-body { display: flex; gap: 10px; align-items: center; }
        .card-photo { width: 55px; height: 70px; border-radius: 6px; border: 1.5px solid #fbbf24; background: #3730a3; display: flex; align-items: center; justify-content: center; font-size: 20pt; font-weight: bold; overflow: hidden; }
        .card-photo img { width: 100%; height: 100%; object-fit: cover; }
        .card-info { flex: 1; font-size: 7.5pt; line-height: 1.35; }
        .card-info div { margin-bottom: 2px; }
        .card-info .label { color: #cbd5e1; text-transform: uppercase; font-size: 6.5pt; }
        .card-info .val { font-weight: bold; color: #fff; }
        .card-footer { margin-top: 8px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.25); display: flex; justify-content: space-between; align-items: center; }
        .card-barcode { background: #fff; padding: 2px 6px; border-radius: 4px; display: inline-block; }
        .card-code { font-family: monospace; font-size: 7.5pt; font-weight: bold; color: #000; letter-spacing: 1px; text-align: center; }
        .card-back {
            width: 330px;
            height: 205px;
            border-radius: 12px;
            border: 2px solid #312e81;
            background: #f8fafc;
            color: #0f172a;
            padding: 12px 14px;
            box-sizing: border-box;
            position: relative;
            page-break-inside: avoid;
            font-size: 7pt;
            line-height: 1.3;
        }
        .card-back h4 { margin: 0 0 4px 0; font-size: 8pt; text-transform: uppercase; color: #312e81; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; }
        .card-back ol { margin: 4px 0 8px 14px; padding: 0; }
        .card-back li { margin-bottom: 2px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 6px; font-size: 6.5pt; }
        .signatures .sig-box { width: 48%; }
        .signatures .sig-left { text-align: left; }
        .signatures .sig-right { text-align: right; }
        @media print {
            body { margin: 5mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak Kartu Anggota (Print)</button>
    </div>

    <div class="card-container">
        <!-- Kartu Sisi Depan -->
        <div class="library-card">
            <div class="card-header">
                @if(!empty($schoolLogo))
                    <img src="{{ $schoolLogo }}" class="card-logo" alt="Logo">
                @endif
                <div class="card-header-text">
                    <h3>PERPUSTAKAAN {{ $schoolName ?? 'SMAN 1 LENGKONG' }}</h3>
                    <h2>KARTU ANGGOTA PERPUSTAKAAN</h2>
                </div>
            </div>

            <div class="card-body">
                <div class="card-photo">
                    @if($user && $user->avatar && (file_exists(public_path('storage/'.$user->avatar)) || Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)))
                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Foto">
                    @else
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    @endif
                </div>
                <div class="card-info">
                    <div>
                        <div class="label">Nama Anggota</div>
                        <div class="val">{{ $member->name }}</div>
                    </div>
                    <div>
                        <div class="label">{{ strtoupper($member->member_type) === 'SISWA' ? 'NISN / NIS' : 'NIP / NUPTK' }}</div>
                        <div class="val">{{ $member->identity_number ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="label">Kelas / Unit Kerja</div>
                        <div class="val">{{ $member->member_class_or_title }}</div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="card-barcode">
                    <div class="card-code">{{ $member->member_code }}</div>
                </div>
                <div style="font-size: 6.5pt; color: #a5b4fc; text-align: right;">
                    Berlaku s.d: Aktif Sekolah<br>
                    Status: <span style="color: #4ade80; font-weight: bold;">TERVERIFIKASI</span>
                </div>
            </div>
        </div>

        <!-- Kartu Sisi Belakang -->
        <div class="card-back">
            <h4>TATA TERTIB PEMUSTAKA</h4>
            <ol>
                <li>Kartu ini wajib dibawa saat berkunjung dan meminjam koleksi pustaka.</li>
                <li>Peminjaman buku maksimal 7 hari dan dapat diperpanjang jika belum diantre.</li>
                <li>Pemustaka wajib menjaga kebersihan dan keutuhan koleksi buku.</li>
                <li>Kerusakan atau kehilangan koleksi menjadi tanggung jawab pemegang kartu.</li>
            </ol>

            <div class="signatures">
                <div class="sig-box sig-left">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong><br><br><br>
                    <strong><u>{{ $headmasterName }}</u></strong><br>
                    NIP. {{ $headmasterNip }}
                </div>
                <div class="sig-box sig-right">
                    Lengkong, {{ date('d/m/Y') }}<br>
                    <strong>Kepala Perpustakaan</strong><br><br><br>
                    <strong><u>{{ $librarianName }}</u></strong><br>
                    NIP. {{ $librarianNip }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

