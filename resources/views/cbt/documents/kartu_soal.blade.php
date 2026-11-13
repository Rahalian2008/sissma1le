<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Soal - {{ $exam->title }} - {{ $school['name'] }}</title>
    
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
                size: A4 portrait;
                margin: 12mm 15mm;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="p-6 max-w-4xl mx-auto">

    <!-- FLOATING ACTION BUTTONS (NO-PRINT) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 text-white p-2.5 rounded-2xl shadow-xl backdrop-blur-sm">
        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-sans font-bold transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-sans font-bold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Kartu Soal
        </button>
    </div>

    <!-- KOP RESMI SEKOLAH -->
    <div class="border-b-4 border-double border-black pb-3 mb-4">
        <div class="flex items-center justify-between gap-4">
            <div class="w-16 h-16 flex items-center justify-center flex-shrink-0">
                @if(!empty($school['logo_url']))
                    <img src="{{ $school['logo_url'] }}" alt="Logo" class="max-h-16 max-w-16 object-contain">
                @else
                    <div class="w-14 h-14 border border-black rounded-full flex items-center justify-center font-bold text-[10px] text-center font-sans">
                        LOGO
                    </div>
                @endif
            </div>
            <div class="flex-1 text-center font-serif leading-tight">
                <div class="text-[11px] uppercase tracking-wider">Pemerintah Daerah Provinsi Jawa Barat - Dinas Pendidikan</div>
                <div class="text-[11px] uppercase tracking-wider">Cabang Dinas Pendidikan Wilayah V</div>
                <div class="text-lg font-bold uppercase tracking-wide mt-0.5">{{ $school['name'] }}</div>
                <div class="text-[10px] mt-0.5">{{ $school['address'] }} • NPSN: {{ $school['npsn'] }}</div>
            </div>
            <div class="w-16 flex-shrink-0"></div>
        </div>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-base font-bold uppercase underline tracking-wider">KARTU SOAL BENTUK RESMI (KEMDIKBUD)</h2>
        <div class="text-xs uppercase font-bold mt-0.5">{{ $exam->title }}</div>
    </div>

    @php
        $typeNames = [
            'multiple_choice' => 'Pilihan Ganda',
            'complex_multiple_choice' => 'Pilihan Ganda Kompleks',
            'true_false' => 'Benar / Salah',
            'matching' => 'Menjodohkan',
            'short_answer' => 'Isian Singkat',
            'essay' => 'Uraian / Esai'
        ];
    @endphp

    @forelse($questions as $index => $q)
        <div class="border-2 border-black mb-8 p-4 text-xs {{ $index < $questions->count() - 1 ? 'page-break' : '' }}">
            <!-- HEADER KARTU SOAL -->
            <div class="flex justify-between items-center border-b-2 border-black pb-2 mb-3 bg-slate-50 p-2 font-bold">
                <div class="text-sm">KARTU SOAL NOMOR: <span class="underline">{{ $q->question_number }}</span></div>
                <div class="text-sm">BENTUK: {{ strtoupper($typeNames[$q->question_type] ?? $q->question_type) }}</div>
            </div>

            <!-- TABEL METADATA SOAL -->
            <table class="w-full border-collapse border border-black mb-4">
                <tr>
                    <td class="border border-black p-1.5 font-bold w-36 bg-slate-100">Satuan Pendidikan</td>
                    <td class="border border-black p-1.5 w-1/3">{{ $school['name'] }}</td>
                    <td class="border border-black p-1.5 font-bold w-32 bg-slate-100">Penyusun</td>
                    <td class="border border-black p-1.5">{{ $exam->teacher?->name ?? 'Tim Guru' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Mata Pelajaran</td>
                    <td class="border border-black p-1.5">{{ $exam->subject?->name ?? 'Umum' }}</td>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Tahun Ajaran</td>
                    <td class="border border-black p-1.5">{{ $exam->academic_year ?? '2026/2027' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Kelas / Fase</td>
                    <td class="border border-black p-1.5">{{ $exam->classes->pluck('name')->implode(', ') }}</td>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Buku Sumber</td>
                    <td class="border border-black p-1.5">Buku Guru & Siswa Kemdikbud</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Capaian Pembelajaran</td>
                    <td colspan="3" class="border border-black p-1.5">{{ $q->learning_objective ?: 'Menguasai capaian pembelajaran fase yang ditetapkan.' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Materi Pokok</td>
                    <td colspan="3" class="border border-black p-1.5">{{ $q->topic_material ?: ($exam->subject?->name ?? 'Materi') }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Indikator Soal</td>
                    <td colspan="3" class="border border-black p-1.5">{{ $q->indicator ?: 'Disajikan konteks materi, peserta didik dapat menyelesaikan persoalan dengan benar.' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Level Kognitif</td>
                    <td class="border border-black p-1.5 font-bold">{{ $q->cognitive_level ?: 'L2' }} ({{ $q->difficulty_level ?: 'Sedang' }})</td>
                    <td class="border border-black p-1.5 font-bold bg-slate-100">Bobot Skor</td>
                    <td class="border border-black p-1.5 font-bold">{{ $q->score_weight }}</td>
                </tr>
            </table>

            <!-- RUMUSAN BUTIR SOAL -->
            <div class="border border-black p-3 mb-3 min-h-[140px]">
                <div class="font-bold underline mb-2">RUMUSAN BUTIR SOAL:</div>
                <div class="leading-relaxed mb-3 whitespace-pre-wrap">{{ $q->question_text }}</div>

                @if(!empty($q->options) && is_array($q->options))
                    <div class="pl-4 space-y-1">
                        @if($q->question_type === 'multiple_choice' || $q->question_type === 'complex_multiple_choice')
                            @foreach($q->options as $opt)
                                <div><strong>{{ $opt['key'] }}.</strong> {{ $opt['text'] }}</div>
                            @endforeach
                        @elseif($q->question_type === 'true_false')
                            <table class="w-full border border-black text-[11px] mt-2">
                                <tr class="bg-slate-100 font-bold">
                                    <th class="border border-black p-1">Pernyataan</th>
                                    <th class="border border-black p-1 w-20 text-center">Pilihan</th>
                                </tr>
                                @foreach($q->options as $s)
                                    <tr>
                                        <td class="border border-black p-1">{{ $s['statement'] ?? '-' }}</td>
                                        <td class="border border-black p-1 text-center font-bold">[Benar / Salah]</td>
                                    </tr>
                                @endforeach
                            </table>
                        @elseif($q->question_type === 'matching')
                            <table class="w-full border border-black text-[11px] mt-2">
                                <tr class="bg-slate-100 font-bold">
                                    <th class="border border-black p-1 w-1/2">Premis (Pernyataan Kiri)</th>
                                    <th class="border border-black p-1 w-1/2">Respon (Pilihan Kanan)</th>
                                </tr>
                                @foreach($q->options as $p)
                                    <tr>
                                        <td class="border border-black p-1">{{ $p['premise'] ?? '-' }}</td>
                                        <td class="border border-black p-1">{{ $p['response'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                @endif
            </div>

            <!-- KUNCI JAWABAN & PEDOMAN PENSKORAN -->
            <div class="border border-black p-3 bg-slate-50">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-bold underline block mb-1">KUNCI JAWABAN:</span>
                        <div class="font-bold text-sm">
                            @if(is_array($q->correct_answer))
                                {{ json_encode($q->correct_answer) }}
                            @else
                                {{ $q->correct_answer ?: '(Pedoman guru)' }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="font-bold underline block mb-1">PEDOMAN PENSKORAN / PEMBAHASAN:</span>
                        <div class="italic text-[11px]">
                            {{ $q->explanation ?: 'Jawaban benar mendapatkan skor penuh sesuai bobot, salah mendapatkan 0.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="p-8 text-center border-2 border-dashed border-black">
            Belum ada butir soal dalam ujian ini.
        </div>
    @endforelse

</body>
</html>
