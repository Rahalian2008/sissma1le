<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .paper-f4 .children-row { gap: 28px; }
        .paper-f4 .node-box { min-width: 250px; padding: 12px 20px; }
        .paper-a4 .children-row { gap: 14px; }
        .paper-a4 .node-box { min-width: 190px; padding: 8px 12px; }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 20px;
        }
        .header-kop h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        .header-kop h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0;
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .node-box {
            border: 2px solid #1e293b;
            padding: 10px 16px;
            text-align: center;
            border-radius: 8px;
            background-color: #f8fafc;
            min-width: 220px;
            margin-bottom: 20px;
        }
        .node-box.head {
            background-color: #e0e7ff;
            border-color: #4338ca;
        }
        .node-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .node-person {
            font-size: 10pt;
            font-weight: 600;
        }
        .node-nip {
            font-size: 8.5pt;
            color: #475569;
        }
        .children-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
            flex-wrap: wrap;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar orientation="landscape" default-size="f4" title="Bagan Struktur Organisasi Sekolah (DSO)" />

    <div style="padding: 15px;">
        <x-print-kop :school="$school" />
        <div style="text-align: center; margin-bottom: 12px;">
            <h3 style="margin: 0; text-transform: uppercase; font-size: 12pt; text-decoration: underline; font-weight: bold;">BAGAN STRUKTUR ORGANISASI SEKOLAH (DSO)</h3>
        </div>

        <div class="chart-container">
            @foreach ($rootNodes as $root)
                <div class="node-box head">
                    <div class="node-title">{{ $root->position_name }}</div>
                    <div class="node-person">{{ $root->officer_name }}</div>
                    @if($root->officer_nip)<div class="node-nip">NIP. {{ $root->officer_nip }}</div>@endif
                </div>

                @if($root->children->isNotEmpty())
                    <div class="children-row">
                        @foreach ($root->children as $child)
                            <div class="node-box">
                                <div class="node-title" style="font-size: 10pt;">{{ $child->position_name }}</div>
                                <div class="node-person">{{ $child->officer_name }}</div>
                                @if($child->officer_nip)<div class="node-nip">NIP. {{ $child->officer_nip }}</div>@endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>

        <div style="margin-top: 40px; float: right; width: 280px; text-align: center; font-size: 10pt;">
            Ditetapkan di {{ $school->city ?? 'Kota' }}<br>
            Pada tanggal {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br>
            <strong><u>{{ $school->headmaster_name }}</u></strong><br>
            NIP. {{ $school->headmaster_nip }}
        </div>
    </div>
</body>
</html>
