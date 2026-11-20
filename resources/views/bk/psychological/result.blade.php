@extends('layouts.app')

@section('title', 'Laporan Analisis Psikotes AI - ' . $result->student->name)

@section('content')
@php
    $report = $result->ai_report ?? [];
    $priority = $report['prioritas_keputusan'] ?? [
        'level' => $result->priority_level ?? 'PRIORITAS_1',
        'label' => $result->priority_label ?? 'PENGEMBANGAN POTENSI',
        'deskripsi' => 'Peserta didik memiliki potensi unggul yang siap diakselerasi melalui pembinaan bakat berkesinambungan.',
        'alasan' => 'Skor dominan berada pada rentang tinggi dan konsisten.'
    ];

    $priorityColor = match($priority['level'] ?? 'PRIORITAS_1') {
        'PRIORITAS_1' => [
            'bg' => 'bg-emerald-500/10 dark:bg-emerald-950/40',
            'border' => 'border-emerald-500/30 dark:border-emerald-500/40',
            'text' => 'text-emerald-800 dark:text-emerald-300',
            'badge' => 'bg-emerald-600 text-white',
            'glow' => 'bg-emerald-500',
        ],
        'PRIORITAS_2' => [
            'bg' => 'bg-sky-500/10 dark:bg-sky-950/40',
            'border' => 'border-sky-500/30 dark:border-sky-500/40',
            'text' => 'text-sky-800 dark:text-sky-300',
            'badge' => 'bg-sky-600 text-white',
            'glow' => 'bg-sky-500',
        ],
        'PRIORITAS_3' => [
            'bg' => 'bg-amber-500/10 dark:bg-amber-950/40',
            'border' => 'border-amber-500/30 dark:border-amber-500/40',
            'text' => 'text-amber-800 dark:text-amber-300',
            'badge' => 'bg-amber-600 text-white',
            'glow' => 'bg-amber-500',
        ],
        default => [
            'bg' => 'bg-rose-500/10 dark:bg-rose-950/40',
            'border' => 'border-rose-500/30 dark:border-rose-500/40',
            'text' => 'text-rose-800 dark:text-rose-300',
            'badge' => 'bg-rose-600 text-white',
            'glow' => 'bg-rose-500',
        ]
    };

    $isOnlineAi = str_contains(strtolower((string) $result->ai_provider), 'gemini');
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.psychological.index') }}" class="hover:text-emerald-600 font-medium">Tes Psikotes</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">Hasil Evaluasi AI</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isCounselor() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
                <form action="{{ route('bk.psychological.generate-ai', $result) }}" method="POST" class="inline" onsubmit="return confirm('Jalankan analisis ulang psikometrik menggunakan Google Gemini AI?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer" title="Perbarui analisis menggunakan Google Gemini AI">
                        <span>⚡ Analisis Ulang AI</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('bk.psychological.holistic', $result->student_id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition" title="Lihat dan Cetak Laporan Terpadu 3-in-1">
                <span>📑 Laporan Terpadu 3-in-1</span>
            </a>
            <a href="{{ route('bk.psychological.print', $result) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-xs shadow-md shadow-teal-600/20 hover:bg-teal-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Per Tes (A4)</span>
            </a>
            @if(auth()->user()->isCounselor() || auth()->user()->hasRole('admin'))
                <a href="{{ route('bk.student.profile360', $result->student_id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profil 360 Siswa</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Banner Rekomendasi Laporan Terpadu 3-in-1 (Sintesis Harmonis) -->
    <div class="p-4 sm:p-5 rounded-3xl bg-linear-to-r from-indigo-950/40 via-purple-950/30 to-teal-950/40 border border-indigo-500/30 dark:border-indigo-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 text-lg shadow-md shadow-indigo-600/30">
                📑
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Laporan Asesmen Terpadu 3-in-1 (Anti-Bingung)</h4>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 max-w-2xl leading-relaxed">
                    Agar hasil tidak terpisah-pisah seperti puzzle, sistem menyediakan <strong>Laporan Sintesis Komprehensif</strong> yang memadukan <strong>Gaya Belajar (VAK)</strong>, <strong>Minat Karier (Holland)</strong>, dan <strong>Kecerdasan Majemuk (Gardner)</strong> menjadi satu kesatuan profil yang logis, utuh, dan mudah dipahami orang tua & guru.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('bk.psychological.holistic', $result->student_id) }}" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-1.5">
                <span>Lihat Sintesis 3-in-1</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('bk.psychological.holistic.print', $result->student_id) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-indigo-700 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-700 text-xs font-bold transition flex items-center gap-1" title="Cetak langsung dokumen resmi terpadu format A4">
                <span>🖨️ Cetak A4</span>
            </a>
        </div>
    </div>

    <!-- Profile Hero Card (Identitas & Dominansi) -->
    <div class="p-6 sm:p-8 rounded-3xl bg-linear-to-br from-slate-900 via-slate-800 to-teal-950 text-white shadow-xl relative overflow-hidden border border-slate-700">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        {{ $result->test->name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-300">
                        Selesai: {{ $result->taken_at ? $result->taken_at->format('d F Y, H:i') : '-' }} WIB
                    </span>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $isOnlineAi ? 'bg-indigo-500/30 text-indigo-200 border border-indigo-400/30' : 'bg-slate-700 text-slate-300' }}">
                        {{ $result->ai_provider ?? 'AI Engine' }}
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $result->profile_label }}
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl">
                        Sistem Analisis, Penilaian, Evaluasi, dan Rekomendasi Hasil Psikotes Terstandar POP-BK Berbasis AI.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-xs pt-2 border-t border-slate-700/60">
                    <div>Nama: <span class="font-bold text-teal-300">{{ $result->student->name }}</span></div>
                    <div>NISN: <span class="font-mono text-slate-300">{{ $result->student->nisn }}</span></div>
                    <div>Kelas: <span class="font-bold text-slate-300">{{ $result->student->schoolClass->name ?? '-' }}</span></div>
                    @if($result->holland_code)
                        <div>Holland Code: <span class="font-mono font-black text-amber-300 tracking-wider">{{ $result->holland_code }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Dominant Badge Showcase -->
            <div class="shrink-0 flex flex-col items-center justify-center p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md text-center min-w-[200px]">
                <span class="text-[10px] uppercase font-bold tracking-wider text-teal-300 mb-1">Dimensi Utama</span>
                <span class="text-2xl sm:text-3xl font-black text-white uppercase">{{ $result->dominant_dimension }}</span>
                @if($result->secondary_dimension)
                    <span class="text-xs text-slate-300 mt-1">Sekunder: <b>{{ $result->secondary_dimension }}</b></span>
                @endif
                <div class="mt-3 px-3 py-1 rounded-full text-[10px] font-bold bg-teal-500 text-slate-950 uppercase tracking-widest">
                    Terverifikasi
                </div>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Prioritas Keputusan & Rekomendasi Tindak Lanjut Utama (Bagian 7 & 12) -->
    <div class="p-6 rounded-3xl {{ $priorityColor['bg'] }} border-2 {{ $priorityColor['border'] }} shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $priorityColor['glow'] }} animate-ping"></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prioritas Keputusan Konseling</span>
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $priorityColor['badge'] }} uppercase tracking-wider">
                    {{ $priority['level'] ?? 'PRIORITAS_1' }}: {{ $priority['label'] ?? 'PENGEMBANGAN POTENSI' }}
                </span>
            </div>
            <span class="text-[11px] text-slate-500">Pedoman Pengambilan Keputusan POP-BK</span>
        </div>
        <p class="text-xs sm:text-sm font-semibold {{ $priorityColor['text'] }} leading-relaxed">
            {{ $priority['deskripsi'] ?? '-' }}
        </p>
        @if(!empty($priority['alasan']))
            <div class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-200/50 dark:border-slate-800">
                <span class="font-bold">Dasar Pertimbangan:</span> {{ $priority['alasan'] }}
            </div>
        @endif
    </div>

    <!-- Ringkasan Eksekutif (Bagian B) -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <div class="flex items-center gap-2.5 text-slate-900 dark:text-white font-bold text-sm">
            <div class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center font-black">
                B
            </div>
            <span>Ringkasan Eksekutif (Executive Summary)</span>
        </div>
        <div class="text-xs sm:text-sm leading-relaxed text-slate-700 dark:text-slate-200 space-y-2 whitespace-pre-line bg-slate-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
            {{ $report['ringkasan_eksekutif'] ?? $result->analysis_summary }}
        </div>
    </div>

    <!-- Grid: Penilaian Dimensi & Analisis Mendalam (Bagian C & D) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bagian C: Hasil Penilaian Keseluruhan (Grafik & Norma) -->
        <div class="lg:col-span-1 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                <div class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center font-black text-xs">
                    C
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Penilaian Keseluruhan</h3>
                    <p class="text-[11px] text-slate-400">Skor & Kategori Dimensi</p>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                @php
                    $percentages = $result->dimension_scores['percentages'] ?? [];
                    $rawScores = $result->dimension_scores['raw'] ?? [];
                    
                    $dimColors = [
                        'VISUAL' => 'from-blue-500 to-indigo-500',
                        'AUDITORI' => 'from-amber-500 to-orange-500',
                        'KINESTETIK' => 'from-emerald-500 to-teal-500',
                        'R' => 'from-rose-500 to-red-500',
                        'I' => 'from-sky-500 to-blue-500',
                        'A' => 'from-purple-500 to-pink-500',
                        'S' => 'from-emerald-500 to-teal-500',
                        'E' => 'from-amber-500 to-orange-500',
                        'C' => 'from-cyan-500 to-teal-500',
                    ];
                @endphp

                @foreach($percentages as $dim => $pct)
                    @php
                        $color = $dimColors[$dim] ?? 'from-teal-500 to-emerald-500';
                        $raw = $rawScores[$dim] ?? 0;
                        $kategori = match(true) {
                            $pct >= 80 => 'Sangat Tinggi',
                            $pct >= 65 => 'Tinggi',
                            $pct >= 50 => 'Sedang',
                            default => 'Perlu Penguatan',
                        };
                    @endphp
                    <div class="space-y-1.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $dim }}</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">
                                {{ $pct }}% <span class="text-[10px] text-slate-400 font-normal">({{ $raw }} pts)</span>
                            </span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full bg-linear-to-r {{ $color }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-[10px] text-slate-500 flex justify-between pt-0.5">
                            <span>Norma: <strong>{{ $kategori }}</strong></span>
                            <span>{{ $pct >= 65 ? 'Unggulan' : 'Menengah' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1">
                <div class="font-semibold text-slate-700 dark:text-slate-300">Pedoman Kategori:</div>
                <p>Kategori didasarkan pada proporsi respons instrumen psikotes, menghindari klaim IQ tanpa instrumen terstandardisasi khusus.</p>
            </div>
        </div>

        <!-- Bagian D: Analisis Mendalam Per Dimensi -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                    <div class="w-7 h-7 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center font-black text-xs">
                        D
                    </div>
                    <span>Analisis Mendalam Per Bidang & Dinamika Psikologis</span>
                </div>

                <div class="space-y-3 text-xs leading-relaxed text-slate-700 dark:text-slate-300">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">1. Gambaran Umum Modalitas:</div>
                        <p>{{ $report['analisis_mendalam']['gambaran_umum'] ?? $result->analysis_summary }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                        <div class="font-bold text-teal-700 dark:text-teal-300 text-xs">2. Dinamika Dominansi Teridentifikasi:</div>
                        <p>{{ $report['analisis_mendalam']['dinamika_dominansi'] ?? 'Dominansi ini berperan sentral dalam cara siswa menyerap materi ajar dan mengambil keputusan.' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                        <div class="font-bold text-amber-700 dark:text-amber-300 text-xs">3. Area Perhatian & Potensi Distraksi:</div>
                        <p>{{ $report['analisis_mendalam']['area_distraksi_atau_pertumbuhan'] ?? 'Perlu variasi metode agar siswa tidak jenuh saat berhadapan dengan tugas di luar preferensi alaminya.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bagian E: Profil Potensi Siswa (7 Dimensi Potensi) -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                    <div class="w-7 h-7 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-600 flex items-center justify-center font-black text-xs">
                        E
                    </div>
                    <span>Profil Potensi Siswa (Bakat & Kapasitas Holistik)</span>
                </div>

                @php
                    $potensi = $report['profil_potensi'] ?? [];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-sky-50/50 dark:bg-sky-950/20 border border-sky-100 dark:border-sky-900/40 space-y-1">
                        <div class="font-bold text-sky-800 dark:text-sky-300">💡 Potensi Intelektual:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_intelektual'] ?? 'Kemampuan berpikir analitis yang baik.' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 space-y-1">
                        <div class="font-bold text-emerald-800 dark:text-emerald-300">🌟 Potensi Bakat Spesifik:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_bakat'] ?? 'Bakat dominan terarah pada penyelesaian tugas fungsional.' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 space-y-1">
                        <div class="font-bold text-indigo-800 dark:text-indigo-300">📚 Potensi Akademik:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_akademik'] ?? 'Kesiapan belajar tinggi pada rumpun mata pelajaran relevan.' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40 space-y-1">
                        <div class="font-bold text-purple-800 dark:text-purple-300">👥 Potensi Sosial & Relasi:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_sosial'] ?? 'Mampu beradaptasi aktif dalam dinamika kelompok kerja.' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 space-y-1">
                        <div class="font-bold text-amber-800 dark:text-amber-300">👑 Potensi Kepemimpinan:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_kepemimpinan'] ?? 'Kapasitas menggerakkan diri dan memotivasi rekan sejawat.' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 space-y-1">
                        <div class="font-bold text-rose-800 dark:text-rose-300">🎨 Potensi Kreativitas & Inovasi:</div>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $potensi['potensi_kreativitas'] ?? 'Mampu melahirkan alternatif solusi orisinal.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian F: Kekuatan Utama vs Area Pengembangan -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-black text-xs">
                F
            </div>
            <span>Kekuatan Utama & Area Pengembangan Diri</span>
        </div>

        @php
            $growth = $report['kekuatan_dan_area_pengembangan'] ?? [];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <!-- Kekuatan Utama -->
            <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 space-y-2.5">
                <div class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                    <span>⭐ Kekuatan Utama Siswa:</span>
                </div>
                <ul class="space-y-1.5 list-disc list-inside text-slate-700 dark:text-slate-300 leading-relaxed">
                    @foreach($growth['kekuatan_utama'] ?? ['Kemampuan konsentrasi tinggi pada bidang yang diminati', 'Efikasi diri dan motivasi belajar yang sehat'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                @if(!empty($growth['kekuatan_pendukung']))
                    <div class="pt-2 border-t border-emerald-200/40 dark:border-emerald-900/30 text-[11px] text-emerald-700 dark:text-emerald-400">
                        <strong>Kekuatan Pendukung:</strong> {{ implode(', ', (array) $growth['kekuatan_pendukung']) }}
                    </div>
                @endif
            </div>

            <!-- Area Pengembangan -->
            <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 space-y-2.5">
                <div class="font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                    <span>💡 Area yang Perlu Dikembangkan & Dilatih:</span>
                </div>
                <ul class="space-y-1.5 list-disc list-inside text-slate-700 dark:text-slate-300 leading-relaxed">
                    @foreach($growth['area_pengembangan'] ?? ['Penyusunan jadwal dan skala prioritas belajar', 'Manajemen stres saat menghadapi ujian'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                @if(!empty($growth['hal_perlu_observasi']))
                    <div class="pt-2 border-t border-amber-200/40 dark:border-amber-900/30 text-[11px] text-amber-700 dark:text-amber-400">
                        <strong>Catatan Observasi Lanjutan:</strong> {{ implode(', ', (array) $growth['hal_perlu_observasi']) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bagian G & H: Rekomendasi Pendidikan, PTN/PTS & Karier Masa Depan -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
            <div class="w-7 h-7 rounded-xl bg-violet-100 dark:bg-violet-950/60 text-violet-600 flex items-center justify-center font-black text-xs">
                G & H
            </div>
            <span>Rekomendasi Pendidikan, Perguruan Tinggi, & Arah Karier Masa Depan</span>
        </div>

        @php
            $pendidikan = $report['rekomendasi_pendidikan'] ?? [];
            $ptKarir = $report['rekomendasi_perguruan_tinggi_dan_karir'] ?? [];
        @endphp

        <!-- Rekomendasi Program Studi PTN / PTS -->
        <div class="space-y-3">
            <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                <span>🎓 3 Alternatif Program Studi Perguruan Tinggi yang Relevan:</span>
                <span class="text-[10px] text-slate-400 font-normal">Diselaraskan dengan SNBP / SNBT / Jalur Prestasi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                @foreach($ptKarir['program_studi'] ?? [
                    ['jurusan' => 'Teknologi Informasi / Ilmu Komputer', 'fakultas' => 'Teknologi & Rekayasa', 'alasan' => 'Sesuai dengan cara berpikir terstruktur.'],
                    ['jurusan' => 'Manajemen & Komunikasi Bisnis', 'fakultas' => 'Ekonomi & Sosial', 'alasan' => 'Mendukung bakat interaksi dan persuasi.'],
                    ['jurusan' => 'Sains Terapan & Desain', 'fakultas' => 'MIPA / Seni Desain', 'alasan' => 'Menyalurkan daya kreativitas fungsional.']
                ] as $idx => $ps)
                    <div class="p-4 rounded-2xl bg-linear-to-b from-slate-50 to-indigo-50/20 dark:from-slate-800/40 dark:to-indigo-950/20 border border-slate-200 dark:border-slate-800 space-y-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300">
                            Pilihan {{ $idx + 1 }}: {{ $ps['fakultas'] ?? 'Rumpun Studi' }}
                        </span>
                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                            {{ $ps['jurusan'] ?? ($ps['major'] ?? '-') }}
                        </h4>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-snug">
                            {{ $ps['alasan'] ?? ($ps['prospect'] ?? '-') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rekomendasi Profesi & Peta Kompetensi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <!-- Profesi Masa Depan -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-2">
                <div class="text-xs font-bold text-indigo-800 dark:text-indigo-300">💼 Peluang Profesi / Karier Potensial:</div>
                <div class="space-y-2 text-xs">
                    @foreach($ptKarir['profesi_rekomendasi'] ?? [
                        ['profesi' => 'Spesialis Bidang Rekayasa Data / Analis', 'deskripsi' => 'Peran analitis strategis di era komputasi modern.'],
                        ['profesi' => 'Konsultan Manajemen / Operasional', 'deskripsi' => 'Pengorganisasian sistem dan kolaborasi tim kerja.'],
                    ] as $prof)
                        <div class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $prof['profesi'] ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $prof['deskripsi'] ?? '-' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mata Pelajaran & Ekskul Pendukung di SMA -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-2">
                <div class="text-xs font-bold text-teal-800 dark:text-teal-300">🏫 Mata Pelajaran & Kegiatan Ekstrakurikuler Relevan:</div>
                <div class="space-y-1.5 text-xs text-slate-700 dark:text-slate-300">
                    <div>
                        <strong class="text-slate-900 dark:text-white">Mata Pelajaran Kunci:</strong>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ implode(', ', (array) ($pendidikan['mata_pelajaran_relevan'] ?? ['Matematika', 'Informatika', 'Bahasa Inggris', 'Bahasa Indonesia'])) }}</p>
                    </div>
                    <div class="pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                        <strong class="text-slate-900 dark:text-white">Ekstrakurikuler Penunjang:</strong>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ implode(', ', (array) ($pendidikan['ekstrakurikuler_relevan'] ?? ['Kelompok Ilmiah Remaja (KIR)', 'IT Club', 'Pramuka / PMR'])) }}</p>
                    </div>
                    <div class="pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                        <strong class="text-slate-900 dark:text-white">Strategi Belajar Disarankan:</strong>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ $pendidikan['strategi_belajar_efektif'] ?? $result->study_recommendations }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian J: Rencana Tindak Lanjut (RTL 1 Bulan, 3 Bulan, 6 Bulan) -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-black text-xs">
                    J
                </div>
                <span>Rencana Tindak Lanjut (RTL) Terukur (1 Bulan, 3 Bulan, 6 Bulan)</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white uppercase tracking-wider">
                Timeline Aksi Nyata
            </span>
        </div>

        @php
            $rtl = $report['rencana_tindak_lanjut'] ?? [];
            $periods = [
                'jangka_pendek_1_bulan' => ['title' => '⏳ Jangka Pendek (Bulan Ke-1)', 'badge' => 'Bulan 1', 'color' => 'teal'],
                'jangka_menengah_3_bulan' => ['title' => '📅 Jangka Menengah (Bulan Ke-3)', 'badge' => 'Bulan 3', 'color' => 'indigo'],
                'jangka_panjang_6_bulan' => ['title' => '🎯 Jangka Panjang (Bulan Ke-6)', 'badge' => 'Bulan 6', 'color' => 'purple'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($periods as $key => $p)
                @php
                    $items = $rtl[$key] ?? [];
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 space-y-2.5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black text-slate-800 dark:text-white">{{ $p['title'] }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                {{ $p['badge'] }}
                            </span>
                        </div>

                        @if(!empty($items))
                            @foreach($items as $action)
                                <div class="space-y-1.5 text-xs">
                                    <div class="font-bold text-teal-700 dark:text-teal-400">{{ $action['target'] ?? 'Target Belajar' }}</div>
                                    <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">{{ $action['aktivitas'] ?? '-' }}</p>
                                    <div class="text-[10.5px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50 space-y-0.5">
                                        <div><strong>PIC:</strong> {{ $action['pic'] ?? 'Guru BK' }}</div>
                                        <div><strong>Indikator:</strong> {{ $action['indikator'] ?? 'Tercapai' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-slate-400 italic">Jadwal pendampingan reguler bimbingan konseling.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bagian K: Rekomendasi Khusus untuk 6 Pihak Sekolah & Orang Tua -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
            <div class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center font-black text-xs">
                K
            </div>
            <span>Rekomendasi Khusus untuk Ekosistem Sekolah & Orang Tua</span>
        </div>

        @php
            $stakeholders = $report['rekomendasi_pihak_sekolah'] ?? [];
            $roles = [
                'guru_bk' => ['label' => 'Guru Bimbingan Konseling (BK)', 'icon' => '🧠', 'color' => 'teal'],
                'wali_kelas' => ['label' => 'Wali Kelas', 'icon' => '📋', 'color' => 'blue'],
                'guru_mata_pelajaran' => ['label' => 'Guru Mata Pelajaran', 'icon' => '📖', 'color' => 'indigo'],
                'orang_tua' => ['label' => 'Orang Tua / Wali Murid', 'icon' => '🏡', 'color' => 'rose'],
                'kesiswaan' => ['label' => 'Wakasek Bidang Kesiswaan', 'icon' => '🚩', 'color' => 'amber'],
                'kepala_sekolah' => ['label' => 'Kepala Sekolah', 'icon' => '🏛️', 'color' => 'violet'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
            @foreach($roles as $rKey => $role)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-1.5">
                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>{{ $role['icon'] }}</span>
                        <span>{{ $role['label'] }}</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ $stakeholders[$rKey] ?? 'Pendampingan positif selaras dengan potensi dan kebutuhan peserta didik.' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bagian L: Kesimpulan Akhir & Landasan Ilmiah Resmi -->
    <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-xl space-y-4 border border-slate-800">
        <div class="flex items-center gap-2 text-white font-bold text-sm">
            <div class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-black text-xs">
                L
            </div>
            <span>Kesimpulan Akhir & Akreditasi Asesmen Psikometrik</span>
        </div>

        @php
            $kesimpulan = $report['kesimpulan_akhir'] ?? [];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-300">
            <div class="space-y-2">
                <div><strong class="text-white">Gambaran Menyeluruh:</strong> {{ $kesimpulan['gambaran_umum'] ?? '-' }}</div>
                <div><strong class="text-teal-300">Keunggulan Paling Menonjol:</strong> {{ $kesimpulan['kekuatan_paling_menonjol'] ?? '-' }}</div>
            </div>
            <div class="space-y-2">
                <div><strong class="text-amber-300">Area Pengembangan Utama:</strong> {{ $kesimpulan['area_pengembangan_utama'] ?? '-' }}</div>
                <div><strong class="text-white">Langkah Prioritas Pertama:</strong> {{ $kesimpulan['tindak_lanjut_prioritas'] ?? '-' }}</div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] text-slate-400">
            <div>
                Engine: <span class="font-bold text-teal-300">{{ $result->ai_provider ?? 'Google Gemini AI' }}</span> 
                @if($result->ai_analyzed_at)
                    &bull; {{ $result->ai_analyzed_at->format('d/m/Y H:i') }} WIB
                @endif
            </div>
            <div class="italic text-[10.5px]">
                Dokumen ini merupakan laporan evaluasi psikologis resmi internal SMAN 1 Lengkong berlandaskan panduan POP-BK Kemendikbud RI.
            </div>
        </div>
    </div>
</div>
@endsection
