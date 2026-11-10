<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Ijazah - {{ $diploma->student?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; font-size: 13pt; text-decoration: underline; }
        .row { display: flex; margin-bottom: 6px; }
        .label { width: 220px; font-weight: normal; }
        .colon { width: 20px; text-align: center; }
        .value { flex: 1; font-weight: bold; }
        .box { border: 1px solid #000; padding: 12px 15px; margin: 15px 0; background-color: #fafafa; border-radius: 4px; }
        .signatures { margin-top: 35px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        .fingerprint-box { width: 90px; height: 110px; border: 1px dashed #000; margin: 10px auto; display: flex; align-items: center; justify-content: center; font-size: 8pt; text-align: center; color: #555; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Tanda Terima Ijazah: ' . ($diploma->student?->name ?? '')" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 20px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div class="title">
            SURAT TANDA TERIMA PENYERAHAN BLANKO IJAZAH ASLI
        </div>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa pada hari ini, tanggal <strong>{{ $diploma->handover_date ? $diploma->handover_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>, telah diserahterimakan dokumen Ijazah Asli dari Bagian Tata Usaha <strong>{{ $school->name }}</strong> kepada:</p>

    <div style="margin-left: 20px; margin-bottom: 15px;">
        <div class="row">
            <div class="label">Nama Penerima / Pengambil</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->recipient_name ?: $diploma->student?->name }}</div>
        </div>
        <div class="row">
            <div class="label">Hubungan dengan Siswa</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->recipient_relation }}</div>
        </div>
    </div>

    <p>Adalah benar menerima dokumen kelulusan untuk peserta didik:</p>

    <div style="margin-left: 20px; margin-bottom: 15px;">
        <div class="row">
            <div class="label">Nama Lengkap Siswa</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->student?->name }}</div>
        </div>
        <div class="row">
            <div class="label">Nomor Induk Siswa (NIS)</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->student?->nis }}</div>
        </div>
        <div class="row">
            <div class="label">NISN</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->student?->nisn ?: '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Tahun Kelulusan</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->graduation_year }}</div>
        </div>
    </div>

    <div class="box">
        <div style="font-weight: bold; margin-bottom: 5px; text-decoration: underline;">RINCIAN DOKUMEN YANG DISERAHKAN:</div>
        <div class="row">
            <div class="label">1. Nomor Seri Blanko Ijazah Asli</div>
            <div class="colon">:</div>
            <div class="value" style="font-family: monospace; font-size: 11.5pt; color: #92400e;">{{ $diploma->diploma_number ?: '(Belum tercatat)' }}</div>
        </div>
        <div class="row">
            <div class="label">2. Nomor Surat Keterangan Lulus (SKL)</div>
            <div class="colon">:</div>
            <div class="value" style="font-family: monospace;">{{ $diploma->skl_number ?: '-' }}</div>
        </div>
        <div class="row">
            <div class="label">3. Status Cap Tiga Jari Tangan Kiri</div>
            <div class="colon">:</div>
            <div class="value">{{ $diploma->three_fingerprint_status ? 'SUDAH DILAKSANAKAN' : 'BELUM' }}</div>
        </div>
        <div class="row">
            <div class="label">4. Catatan Dokumen</div>
            <div class="colon">:</div>
            <div class="value" style="font-weight: normal;">{{ $diploma->notes ?: 'Diserahkan dalam keadaan baik, lengkap, dan tanpa pungutan biaya apapun.' }}</div>
        </div>
    </div>

    <p style="text-align: justify; font-size: 10pt;">
        <em>Pernyataan:</em> Penerima telah memeriksa keaslian, ejaan nama, tempat/tanggal lahir, serta nilai pada ijazah dan menyatakan bahwa dokumen tersebut telah diterima dalam keadaan baik. Segala bentuk kehilangan atau kerusakan dokumen fisik setelah tanggal serah terima menjadi tanggung jawab pemegang ijazah.
    </p>

    <div class="signatures">
        <div class="sig-block">
            Penerima / Pengambil,<br><br>
            <div class="fingerprint-box">
                [ Cap 3 Jari Tangan Kiri Siswa ]
            </div>
            <strong><u>{{ $diploma->recipient_name ?: $diploma->student?->name }}</u></strong><br>
            <span>({{ $diploma->recipient_relation }})</span>
        </div>
        <div class="sig-block">
            Yang Menyerahkan,<br>
            Petugas Tata Usaha {{ $school->name }}<br><br><br><br>
            <div style="display: flex; justify-content: center; margin-bottom: 5px;">
                {!! $qrCodeSvg !!}
            </div>
            <strong><u>{{ $diploma->handedOverBy?->name ?? 'Petugas Arsip TU' }}</u></strong><br>
            NIP. {{ $diploma->handedOverBy?->employee?->nip ?? '....................................' }}
        </div>
    </div>
    </div>
</body>
</html>
