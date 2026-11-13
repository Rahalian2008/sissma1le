<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Peserta CBT - {{ $exam->title }} - {{ $school['name'] }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #fff;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="p-4 max-w-5xl mx-auto">

    <!-- FLOATING ACTION BUTTONS (NO-PRINT) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 text-white p-2.5 rounded-2xl shadow-xl backdrop-blur-sm">
        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Kartu Peserta
        </button>
    </div>

    <!-- FILTER PER KELAS (NO-PRINT) -->
    <div class="no-print mb-6 p-4 rounded-2xl bg-slate-100 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div>
            <strong>Filter Rombel:</strong> Cetak kartu peserta untuk rombel tertentu atau seluruh rombel.
        </div>
        <form method="GET" action="{{ route('cbt.documents.kartu_peserta', $exam->id) }}" class="flex items-center gap-2">
            <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Kelas Terdaftar --</option>
                @foreach($exam->classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- GRID KARTU PESERTA (2 KOLOM) -->
    <div class="grid grid-cols-2 gap-4">
        @forelse($cards as $card)
            @php $student = $card['student']; @endphp
            <div class="border-2 border-dashed border-slate-700 p-3 rounded-lg relative bg-white break-inside-avoid text-[11px] leading-tight">
                <!-- HEADER KARTU -->
                <div class="flex items-center gap-2 border-b-2 border-black pb-1.5 mb-2">
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                        @if(!empty($school['logo_url']))
                            <img src="{{ $school['logo_url'] }}" alt="Logo" class="max-h-10 max-w-10 object-contain">
                        @else
                            <div class="w-8 h-8 rounded-full border border-black flex items-center justify-center font-bold text-[8px]">SMA</div>
                        @endif
                    </div>
                    <div class="flex-1 text-center">
                        <div class="font-bold text-[9px] uppercase tracking-wide">KARTU PESERTA ASESMEN UJIAN ONLINE (CBT)</div>
                        <div class="font-black text-[11px] uppercase">{{ $school['name'] }}</div>
                        <div class="text-[8px] text-slate-600">Tahun Ajaran {{ $exam->academic_year ?? '2026/2027' }} • {{ $exam->title }}</div>
                    </div>
                </div>

                <!-- BODY KARTU: FOTO & DATA SISWA -->
                <div class="flex gap-2.5">
                    <!-- FOTO BOX -->
                    <div class="w-16 h-20 border border-black flex items-center justify-center flex-shrink-0 bg-slate-50 text-[9px] text-slate-400 font-bold overflow-hidden">
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            FOTO<br>2 x 3
                        @endif
                    </div>

                    <!-- TABEL IDENTITAS SISWA -->
                    <div class="flex-1">
                        <table class="w-full text-[10px]">
                            <tr>
                                <td class="w-20 font-bold">No. Peserta</td>
                                <td class="w-2">:</td>
                                <td class="font-bold font-mono">{{ $card['participant_number'] }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Nama Lengkap</td>
                                <td>:</td>
                                <td class="font-bold uppercase text-[10.5px] truncate max-w-[140px]">{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">NIS / NISN</td>
                                <td>:</td>
                                <td>{{ $student->nis }} / {{ $student->nisn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold">Kelas / Ruang</td>
                                <td>:</td>
                                <td>{{ $student->schoolClass?->name ?? '-' }} ({{ $card['room'] }})</td>
                            </tr>
                        </table>

                        <!-- KREDENSIAL AKUN CBT -->
                        <div class="mt-1.5 p-1.5 bg-slate-100 border border-black rounded text-[9.5px]">
                            <div class="grid grid-cols-2 gap-1 font-mono">
                                <div>Username: <strong>{{ $card['username'] }}</strong></div>
                                <div>PIN: <strong>{{ $card['password'] }}</strong></div>
                            </div>
                            <div class="text-[8.5px] text-slate-600 mt-0.5 font-sans">
                                Token: <strong class="font-mono text-indigo-700">{{ $card['token'] ?? '-' }}</strong> • Simpan kartu ini selama ujian.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER TANDA TANGAN -->
                <div class="flex justify-between items-end mt-2 pt-1 border-t border-slate-300 text-[8.5px]">
                    <div class="italic text-slate-500">
                        *Gunakan saat login CBT.
                    </div>
                    <div class="text-center w-36">
                        <div>Kepala Sekolah,</div>
                        <div class="h-6"></div>
                        <div class="font-bold underline">{{ $school['headmaster_name'] }}</div>
                        <div>NIP. {{ $school['headmaster_nip'] }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-12 text-center text-slate-400">
                Tidak ada data siswa terdaftar untuk rombel ujian ini.
            </div>
        @endforelse
    </div>

</body>
</html>
