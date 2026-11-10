<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi - {{ $disposition->incomingLetter->agenda_number }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.25;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .header-kop h1 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-kop h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
        }
        .header-kop p {
            font-size: 8pt;
            margin: 0;
        }
        .title-doc {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        table.disp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table.disp-table th, table.disp-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
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
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Lembar Disposisi: ' . ($disposition->incomingLetter->agenda_number ?? '')" orientation="portrait" defaultPaper="f4" />

    <div style="padding: 10px;">
        <x-print-kop :school="$school" />

        <div class="title-doc">LEMBAR DISPOSISI KEPALA SEKOLAH</div>

        <table class="disp-table">
            <tr>
                <td style="width: 50%;"><strong>No. Agenda:</strong> {{ $disposition->incomingLetter->agenda_number }}</td>
                <td style="width: 50%;"><strong>Sifat:</strong> [ {{ $disposition->incomingLetter->character }} ]</td>
            </tr>
            <tr>
                <td><strong>Surat Dari:</strong> {{ $disposition->incomingLetter->sender }}</td>
                <td><strong>Tanggal Surat:</strong> {{ $disposition->incomingLetter->letter_date?->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Surat:</strong> {{ $disposition->incomingLetter->letter_number }}</td>
                <td><strong>Diterima Tgl:</strong> {{ $disposition->incomingLetter->received_date?->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Perihal:</strong><br>
                    {{ $disposition->incomingLetter->subject }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Diteruskan Kepada Sdr:</strong><br>
                    <div style="font-size: 10pt; font-weight: bold; margin-top: 4px; color: #1e3a8a;">
                        &bull; {{ $disposition->target_role_or_user }}
                    </div>
                </td>
                <td>
                    <strong>Petunjuk / Instruksi:</strong><br>
                    <div style="margin-top: 4px; font-weight: 600;">
                        {{ $disposition->instructions }}
                    </div>
                </td>
            </tr>
            @if($disposition->due_date)
            <tr>
                <td colspan="2">
                    <strong>Batas Waktu Penyelesaian:</strong> {{ $disposition->due_date?->translatedFormat('d F Y') }}
                </td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="height: 60px;">
                    <strong>Catatan / Hasil Tindak Lanjut Penerima Disposisi:</strong><br>
                    {{ $disposition->follow_up_notes ?? '' }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 15px; width: 100%; display: flex; justify-content: space-between; font-size: 9pt;">
            <div style="width: 45%; text-align: center;">
                Penerima Disposisi,<br><br><br><br>
                <strong><u>( {{ $disposition->target_role_or_user ?: (auth()->user()?->name ?? 'Penerima Disposisi') }} )</u></strong>
            </div>
            <div style="width: 45%; text-align: center;">
                {{ $school->city ?? 'Kota' }}, {{ $disposition->disposition_date?->translatedFormat('d F Y') }}<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $school->headmaster_name }}</u></strong><br>
                NIP. {{ $school->headmaster_nip }}
            </div>
        </div>
    </div>
</body>
</html>
