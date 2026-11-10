<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Badge Tamu - {{ $guest->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .badge-card {
            width: 85mm;
            height: 125mm;
            border: 2px solid #1e293b;
            border-radius: 8mm;
            padding: 5mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            background: #fff;
            position: relative;
        }
        .badge-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }
        .badge-header h2 {
            font-size: 11pt;
            margin: 0;
            color: #0f172a;
            font-weight: 800;
        }
        .badge-header h3 {
            font-size: 8pt;
            margin: 1mm 0 0 0;
            color: #2563eb;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .ticket-no {
            display: inline-block;
            background: #0f172a;
            color: #fff;
            font-family: monospace;
            font-size: 9pt;
            font-weight: bold;
            padding: 1mm 3mm;
            border-radius: 3mm;
            margin-top: 2mm;
        }
        .selfie-img {
            width: 28mm;
            height: 28mm;
            object-fit: cover;
            border-radius: 4mm;
            border: 1.5px solid #2563eb;
            margin: 2mm auto;
            display: block;
        }
        .guest-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 1mm;
            line-height: 1.2;
        }
        .guest-inst {
            font-size: 8.5pt;
            color: #475569;
            font-weight: bold;
            margin-top: 0.5mm;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3mm;
            padding: 2mm;
            font-size: 7.5pt;
            text-align: left;
            margin: 2mm 0;
            line-height: 1.3;
        }
        .qr-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3mm;
            border-top: 1px dashed #cbd5e1;
            padding-top: 2mm;
        }
        .qr-section svg {
            width: 18mm;
            height: 18mm;
        }
        .footer-note {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 1mm;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="position: fixed; top: 15px; right: 15px;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 12px;">Cetak Badge Tamu</button>
    </div>

    <div class="badge-card">
        <div>
            <div class="badge-header">
                <h2>{{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</h2>
                <h3>KARTU TAMU &bull; VISITOR PASS</h3>
                <div class="ticket-no">{{ $guest->ticket_number }}</div>
            </div>

            <img src="{{ $guest->selfie_url }}" alt="Selfie" class="selfie-img" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%236366f1\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ffffff\' font-family=\'sans-serif\' font-size=\'38\' font-weight=\'bold\'>{{ strtoupper(substr($guest->name, 0, 2)) }}</text></svg>';">

            <div class="guest-name">{{ $guest->name }}</div>
            <div class="guest-inst">{{ $guest->institution }}</div>

            <div class="info-box">
                <div><strong>Tujuan:</strong> {{ $guest->destination_department }}</div>
                @if($guest->destination_person_name)
                    <div><strong>Bertemu:</strong> {{ $guest->destination_person_name }}</div>
                @endif
                <div><strong>Keperluan:</strong> {{ $guest->purpose }}</div>
                <div><strong>Waktu Masuk:</strong> {{ $guest->check_in_at?->format('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <div>
            <div class="qr-section">
                <div>{!! $qrCodeSvg !!}</div>
                <div style="text-align: left; font-size: 7pt; color: #334155;">
                    <strong>Wajib Dipakai</strong><br>
                    di Lingkungan Sekolah.<br>
                    Kembalikan ke pos satpam<br>
                    saat meninggalkan sekolah.
                </div>
            </div>
            <div class="footer-note">SIKATAS &bull; Sistem Informasi Tata Usaha Sekolah Terpadu</div>
        </div>
    </div>

</body>
</html>
