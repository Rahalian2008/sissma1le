<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris & Sirkulasi Buku - {{ $schoolName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @page {
            size: {{ request('paper', 'a4') === 'f4' ? '330mm 215mm' : 'A4 landscape' }};
            margin: 10mm 15mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
            background: #f8fafc;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-sheet {
            max-width: {{ request('paper', 'a4') === 'f4' ? '330mm' : '297mm' }};
            margin: 20px auto;
            background: #ffffff;
            padding: 12mm 15mm;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
            }
            .print-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="antialiased">

    <!-- Non-print toolbar -->
    <x-print-paper-toolbar :defaultPaper="request('paper', 'a4')" />

    <!-- PRINT SHEET -->
    <div class="print-sheet">
        <!-- OFFICIAL SCHOOL KOP SURAT (DARI MASTER DATA LOGO & KOP SURAT) -->
        @if(!empty($actualKopUrl))
            <div class="mb-5 border-b-2 border-slate-900 pb-3">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat Resmi {{ $schoolName }}" class="w-full max-h-[140px] object-contain mx-auto block">
            </div>
        @else
            <div class="border-b-4 border-double border-slate-900 pb-3 mb-5">
                <div class="flex items-center justify-between gap-4">
                    <div class="w-20 h-20 flex items-center justify-center shrink-0">
                        @if(!empty($schoolLogo))
                            <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}" class="max-h-20 max-w-20 object-contain">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-sm">
                                📚
                            </div>
                        @endif
                    </div>
                    <div class="text-center flex-1 space-y-0.5">
                        <p class="text-[11px] font-bold text-slate-700 uppercase tracking-widest">
                            PEMERINTAH PROVINSI {{ strtoupper($schoolProvince ?? 'JAWA BARAT') }} • DINAS PENDIDIKAN
                        </p>
                        <p class="text-[10px] font-semibold text-slate-600 uppercase tracking-wider">
                            {{ strtoupper($schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}
                        </p>
                        <h1 class="text-xl font-black text-slate-900 uppercase tracking-wide">
                            {{ $schoolName }}
                        </h1>
                        <h2 class="text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Unit Perpustakaan Terpadu & Digital (E-Library)
                        </h2>
                        <p class="text-[10px] text-slate-600 font-medium">
                            NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium">
                            {{ $schoolAddress }} • Telp: {{ $schoolPhone }} • Email: {{ $schoolEmail }} • Web: {{ $schoolWebsite }}
                        </p>
                    </div>
                    <div class="w-20 text-right shrink-0">
                        <span class="px-2.5 py-1 rounded text-[9px] font-mono font-bold border border-slate-400 uppercase bg-slate-50">SNP-01</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- REPORT TITLE -->
        <div class="text-center mb-5">
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 uppercase tracking-wider">
                LAPORAN INVENTARIS, KETERSEDIAAN & SIRKULASI BUKU PERPUSTAKAAN
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Per Tanggal: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong> • Periode Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}
            </p>
        </div>

        <!-- SUMMARY MATRIX TABLE -->
        <div class="grid grid-cols-5 gap-3 mb-5 text-center">
            <div class="p-2.5 rounded-xl border border-slate-300 bg-slate-50">
                <div class="text-[9px] font-bold text-slate-500 uppercase">Total Judul Buku</div>
                <div class="text-base font-black text-slate-900 mt-0.5">{{ number_format($totalTitles) }}</div>
            </div>
            <div class="p-2.5 rounded-xl border border-slate-300 bg-slate-50">
                <div class="text-[9px] font-bold text-slate-500 uppercase">Total Eksemplar</div>
                <div class="text-base font-black text-slate-900 mt-0.5">{{ number_format($totalCopies) }}</div>
            </div>
            <div class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50/60">
                <div class="text-[9px] font-bold text-emerald-800 uppercase">Buku yang Ada (Rak)</div>
                <div class="text-base font-black text-emerald-700 mt-0.5">{{ number_format($availableCopies) }}</div>
            </div>
            <div class="p-2.5 rounded-xl border border-indigo-300 bg-indigo-50/60">
                <div class="text-[9px] font-bold text-indigo-800 uppercase">Sedang Dipinjam</div>
                <div class="text-base font-black text-indigo-700 mt-0.5">{{ number_format($borrowedCopies) }}</div>
            </div>
            <div class="p-2.5 rounded-xl border border-amber-300 bg-amber-50/60">
                <div class="text-[9px] font-bold text-amber-800 uppercase">Persentase Ketersediaan</div>
                <div class="text-base font-black text-amber-700 mt-0.5">
                    {{ round(($availableCopies / max(1, $totalCopies)) * 100) }}%
                </div>
            </div>
        </div>

        <!-- INVENTORY TABLE -->
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-left border-collapse text-[11px] border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold uppercase text-[9px] border-b border-slate-300">
                        <th class="py-2 px-2.5 border-r border-slate-300 text-center w-10">No</th>
                        <th class="py-2 px-2.5 border-r border-slate-300 w-28">Kode & ISBN</th>
                        <th class="py-2 px-2.5 border-r border-slate-300">Judul Buku & Pengarang</th>
                        <th class="py-2 px-2.5 border-r border-slate-300 w-32">Kategori / Rak</th>
                        <th class="py-2 px-2 border-r border-slate-300 text-center w-16">Total Eks</th>
                        <th class="py-2 px-2 border-r border-slate-300 text-center w-16">Ada (Rak)</th>
                        <th class="py-2 px-2 border-r border-slate-300 text-center w-16">Dipinjam</th>
                        <th class="py-2 px-2.5 text-center w-36">Status Ketersediaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($books as $idx => $b)
                        @php
                            $borrowed = max(0, $b->total_copies - $b->available_copies);
                            $isDigital = $b->book_type === 'digital' || $b->is_digital_flip;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-1.5 px-2.5 border-r border-slate-200 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-2.5 border-r border-slate-200 font-mono">
                                <strong>{{ $b->book_code }}</strong>
                                <div class="text-[9px] text-slate-500">{{ $b->isbn ?? '-' }}</div>
                            </td>
                            <td class="py-1.5 px-2.5 border-r border-slate-200">
                                <div class="font-bold text-slate-900">{{ $b->title }}</div>
                                <div class="text-[10px] text-slate-500">✍️ {{ $b->author }} • {{ $b->publisher ?? '-' }} ({{ $b->publish_year ?? '-' }})</div>
                            </td>
                            <td class="py-1.5 px-2.5 border-r border-slate-200">
                                <div>{{ $b->category->name ?? 'Umum' }}</div>
                                <div class="text-[9px] text-slate-500 font-mono">Rak: {{ $b->rack_location ?? 'Cloud' }}</div>
                            </td>
                            <td class="py-1.5 px-2 border-r border-slate-200 text-center font-mono font-bold">{{ $b->total_copies }}</td>
                            <td class="py-1.5 px-2 border-r border-slate-200 text-center font-mono font-bold text-emerald-700">
                                {{ $isDigital ? 'Digital' : $b->available_copies }}
                            </td>
                            <td class="py-1.5 px-2 border-r border-slate-200 text-center font-mono font-bold text-indigo-700">
                                {{ $borrowed > 0 ? $borrowed : '-' }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-semibold text-[10px]">
                                @if($isDigital)
                                    <span class="text-amber-800">E-Book Flip 3D (Online)</span>
                                @elseif($b->available_copies >= $b->total_copies)
                                    <span class="text-emerald-700 font-bold">Tersedia Lengkap</span>
                                @elseif($b->available_copies > 0)
                                    <span class="text-amber-700">Tersedia Sebagian ({{ $b->available_copies }})</span>
                                @else
                                    <span class="text-rose-700 font-bold">Habis Dipinjam (0)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- OFFICIAL SIGNATURES (Kepala Sekolah Selalu di Kiri) -->
        <div class="grid grid-cols-2 gap-8 pt-4 text-xs">
            <div class="text-center">
                <p class="text-[11px] text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-900">Kepala {{ $schoolName }}</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $headmasterName }}</p>
                <p class="text-[10px] text-slate-500 font-mono">NIP. {{ $headmasterNip }}</p>
            </div>

            <div class="text-center">
                <p class="text-[11px] text-slate-500">Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900">Kepala Perpustakaan</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $librarianName }}</p>
                <p class="text-[10px] text-slate-500 font-mono">NIP. {{ $librarianNip }}</p>
            </div>
        </div>
    </div>

</body>
</html>

