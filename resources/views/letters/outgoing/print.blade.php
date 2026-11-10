<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keluar - {{ $outgoingLetter->letter_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header-kop h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 15pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 9.5pt;
            margin: 1px 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11.5pt;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .recipient-block {
            margin-bottom: 25px;
            line-height: 1.3;
        }
        .body-content {
            text-align: justify;
            text-indent: 30px;
            margin-bottom: 20px;
        }
        .signature-table {
            width: 100%;
            margin-top: 35px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            font-size: 13px;
        }
        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Keluar: ' . $outgoingLetter->letter_number" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15mm 20mm; max-width: 215mm; margin: 0 auto;">
        <!-- KOP SURAT DINAS -->
        <x-print-kop :school="$school" />

        <table class="meta-table">
            <tr>
                <td style="width: 15%;">Nomor</td>
                <td style="width: 2%;">:</td>
                <td style="width: 45%;"><strong>{{ $outgoingLetter->letter_number }}</strong></td>
                <td style="width: 38%; text-align: right;">{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ $outgoingLetter->letter_date?->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Sifat</td>
                <td>:</td>
                <td>Penting / Resmi</td>
                <td></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><strong><u>{{ $outgoingLetter->subject }}</u></strong></td>
                <td></td>
            </tr>
        </table>

        <div class="recipient-block">
            Kepada Yth.<br>
            <strong>{{ $outgoingLetter->recipient }}</strong><br>
            di Tempat
        </div>

        <div class="body-content">
            Dengan hormat, sehubungan dengan pelaksanaan program kerja satuan pendidikan dan tindak lanjut surat kedinasan, kami atas nama {{ $school->name }} dengan ini menyampaikan perihal <strong>{{ $outgoingLetter->subject }}</strong> untuk menjadi perhatian dan koordinasi sebagaimana mestinya.
        </div>

        <div class="body-content">
            Demikian surat ini kami sampaikan. Atas perhatian, dukungan, dan kerja sama yang baik dari Bapak/Ibu/Saudara, kami mengucapkan terima kasih.
        </div>

        <table class="signature-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    Kepala Sekolah,<br>
                    <br><br><br><br>
                    <strong><u>{{ $outgoingLetter->signer_name ?: ($school->headmaster_name ?? $school->principal_name) }}</u></strong><br>
                    NIP. {{ $outgoingLetter->signer_nip ?: ($school->headmaster_nip ?? $school->principal_nip) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
