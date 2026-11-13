<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kisi-Kisi Soal - {{ $exam->title }} - {{ $school['name'] }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
        }
        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm 15mm;
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
<body class="p-6 max-w-7xl mx-auto">

    <!-- FLOATING ACTION BUTTONS (NO-PRINT) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 text-white p-2.5 rounded-2xl shadow-xl backdrop-blur-sm">
        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-sans font-bold transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-sans font-bold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Dokumen
        </button>
    </div>

    <!-- KOP RESMI SEKOLAH -->
    <div class="border-b-4 border-double border-black pb-3 mb-4">
        <div class="flex items-center justify-between gap-4">
            <div class="w-20 h-20 flex items-center justify-center flex-shrink-0">
                @if(!empty($school['logo_url']))
                    <img src="{{ $school['logo_url'] }}" alt="Logo" class="max-h-20 max-w-20 object-contain">
                @else
                    <div class="w-16 h-16 border-2 border-black rounded-full flex items-center justify-center font-bold text-xs text-center font-sans">
                        LOGO
                    </div>
                @endif
            </div>
            <div class="flex-1 text-center font-serif leading-tight">
                <div class="text-xs uppercase tracking-wider">Pemerintah Daerah Provinsi Jawa Barat</div>
                <div class="text-xs uppercase tracking-wider">Dinas Pendidikan</div>
                <div class="text-xs uppercase tracking-wider">Cabang Dinas Pendidikan Wilayah V</div>
                <div class="text-xl font-bold uppercase tracking-wide mt-0.5">{{ $school['name'] }}</div>
                <div class="text-[11px] mt-0.5">{{ $school['address'] }} • NPSN: {{ $school['npsn'] }}</div>
                <div class="text-[10px] italic text-slate-700">Website: sman1lengkong.sch.id • Surel: sman1lengkong@gmail.com</div>
            </div>
            <div class="w-20 flex-shrink-0"></div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="text-center mb-4">
        <h2 class="text-base font-bold uppercase underline tracking-wider">KISI-KISI PENULISAN SOAL ASESMEN</h2>
        <div class="text-xs uppercase font-bold mt-0.5 tracking-wider">{{ $exam->title }}</div>
    </div>

    <!-- IDENTITAS MATA PELAJARAN & UJIAN -->
    <div class="grid grid-cols-2 gap-x-8 text-xs mb-4">
        <table class="w-full">
            <tr>
                <td class="w-36 py-0.5 font-bold">Satuan Pendidikan</td>
                <td class="w-3">:</td>
                <td>{{ $school['name'] }}</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Mata Pelajaran</td>
                <td>:</td>
                <td>{{ $exam->subject?->name ?? 'Umum' }}</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Kelas / Rombel</td>
                <td>:</td>
                <td>{{ $classes->pluck('name')->implode(', ') }}</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Tahun Ajaran / Semester</td>
                <td>:</td>
                <td>{{ $exam->academic_year ?? '2026/2027' }} / {{ ucfirst($exam->semester ?? 'Ganjil') }}</td>
            </tr>
        </table>
        <table class="w-full">
            <tr>
                <td class="w-36 py-0.5 font-bold">Kurikulum Acuan</td>
                <td class="w-3">:</td>
                <td>Kurikulum Merdeka</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Alokasi Waktu</td>
                <td>:</td>
                <td>{{ $exam->duration_minutes }} Menit</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Jumlah & Bentuk Soal</td>
                <td>:</td>
                <td>{{ $summary['total_questions'] }} Butir (Objektif & Non-Objektif)</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Guru Penyusun</td>
                <td>:</td>
                <td>{{ $exam->teacher?->name ?? 'Tim MGMP Sekolah' }}</td>
            </tr>
        </table>
    </div>

    <!-- TABEL KISI-KISI RESMI -->
    <table class="w-full border-collapse border border-black text-[11px] mb-6">
        <thead>
            <tr class="bg-slate-100 text-center font-bold">
                <th class="border border-black p-1.5 w-8">No</th>
                <th class="border border-black p-1.5 w-1/4">Capaian Pembelajaran / Kompetensi</th>
                <th class="border border-black p-1.5 w-1/5">Materi Pokok</th>
                <th class="border border-black p-1.5">Indikator Soal</th>
                <th class="border border-black p-1.5 w-14">Level Kognitif</th>
                <th class="border border-black p-1.5 w-24">Bentuk Soal</th>
                <th class="border border-black p-1.5 w-12">No. Soal</th>
                <th class="border border-black p-1.5 w-12">Bobot</th>
            </tr>
        </thead>
        <tbody>
            @php
                $typeLabels = [
                    'multiple_choice' => 'Pilihan Ganda',
                    'complex_multiple_choice' => 'PG Kompleks',
                    'true_false' => 'Benar / Salah',
                    'matching' => 'Menjodohkan',
                    'short_answer' => 'Isian Singkat',
                    'essay' => 'Uraian / Esai'
                ];
            @endphp
            @forelse($questions as $index => $q)
                <tr>
                    <td class="border border-black p-1.5 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ $q->learning_objective ?: 'Menguasai konsep dasar pada capaian pembelajaran fase terkait.' }}</td>
                    <td class="border border-black p-1.5">{{ $q->topic_material ?: ($exam->subject?->name ?? 'Materi Pokok') }}</td>
                    <td class="border border-black p-1.5">{{ $q->indicator ?: 'Disajikan stimulus / konteks masalah, peserta didik dapat menentukan jawaban yang tepat.' }}</td>
                    <td class="border border-black p-1.5 text-center font-bold">{{ $q->cognitive_level ?: 'L2' }}</td>
                    <td class="border border-black p-1.5 text-center">{{ $typeLabels[$q->question_type] ?? $q->question_type }}</td>
                    <td class="border border-black p-1.5 text-center font-bold">{{ $q->question_number }}</td>
                    <td class="border border-black p-1.5 text-center">{{ $q->score_weight }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="border border-black p-4 text-center italic">Belum ada butir soal yang diinputkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN RESMI -->
    <div class="flex justify-between items-start text-xs pt-4" style="page-break-inside: avoid;">
        <div class="text-center w-64">
            <div>Mengetahui,</div>
            <div class="font-bold">Kepala SMA Negeri 1 Lengkong</div>
            <div class="h-20"></div>
            <div class="font-bold underline">{{ $school['headmaster_name'] }}</div>
            <div>NIP. {{ $school['headmaster_nip'] }}</div>
        </div>
        <div class="text-center w-64">
            <div>Lengkong, {{ now()->translatedFormat('d F Y') }}</div>
            <div class="font-bold">Guru Mata Pelajaran</div>
            <div class="h-20"></div>
            <div class="font-bold underline">{{ $exam->teacher?->name ?? 'Guru Mata Pelajaran' }}</div>
            <div>NIP. {{ $exam->teacher?->nip ?? '-' }}</div>
        </div>
    </div>

</body>
</html>
