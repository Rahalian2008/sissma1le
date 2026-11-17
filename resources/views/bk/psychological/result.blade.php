@extends('layouts.app')

@section('title', 'Hasil Asesmen Psikotes - ' . $result->student->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.psychological.index') }}" class="hover:text-emerald-600 font-medium">Tes Psikotes</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">Hasil Evaluasi</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isCounselor() || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
                <form action="{{ route('bk.psychological.generate-ai', $result) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer" title="Perbarui analisis diagnostik butir dan rencana tindak lanjut menggunakan AI">
                        <span>⚡ Analisis Ulang AI</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('bk.psychological.print', $result) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-xs shadow-md shadow-teal-600/20 hover:bg-teal-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Resmi (A4)</span>
            </a>
            @if(auth()->user()->isCounselor() || auth()->user()->hasRole('admin'))
                <a href="{{ route('bk.student.profile360', $result->student_id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profil 360 Siswa</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Profile Hero Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-linear-to-br from-slate-900 via-slate-800 to-teal-950 text-white shadow-xl relative overflow-hidden border border-slate-700">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        {{ $result->test->name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-300">
                        Selesai Diuji: {{ $result->taken_at ? $result->taken_at->format('d F Y, H:i') : '-' }} WIB
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $result->profile_label }}
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl">
                        Berdasarkan pemrosesan instrumen standar POP-BK, berikut adalah visualisasi persebaran skor dan pola kecenderungan psikologis peserta didik.
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
            <div class="shrink-0 flex flex-col items-center justify-center p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                <span class="text-[10px] uppercase font-bold tracking-wider text-teal-300 mb-1">Dimensi Utama</span>
                <span class="text-2xl sm:text-3xl font-black text-white uppercase">{{ $result->dominant_dimension }}</span>
                @if($result->secondary_dimension)
                    <span class="text-xs text-slate-300 mt-1">Sekunder: <b>{{ $result->secondary_dimension }}</b></span>
                @endif
                <div class="mt-3 px-3 py-1 rounded-full text-[10px] font-bold bg-teal-500 text-slate-950 uppercase tracking-widest">
                    Terverifikasi Otomatis
                </div>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Grid Dimensi & Skor Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grafik Skor Dimensi -->
        <div class="lg:col-span-1 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Persebaran Skor Dimensi</h3>
                <p class="text-xs text-slate-400">Tingkat dominansi berdasarkan skor persentase</p>
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

                    $dimNames = [
                        'VISUAL' => 'Visual (Penglihatan & Gambar)',
                        'AUDITORI' => 'Auditori (Pendengaran & Diskusi)',
                        'KINESTETIK' => 'Kinestetik (Gerak & Praktik Langsung)',
                        'R' => 'Realistic (Praktikal, Mesin, Fisik)',
                        'I' => 'Investigative (Analitis, Sains, Riset)',
                        'A' => 'Artistic (Kreatif, Seni, Ekspresi)',
                        'S' => 'Social (Membantu, Mengajar, Empati)',
                        'E' => 'Enterprising (Memimpin, Negosiasi, Bisnis)',
                        'C' => 'Conventional (Rapi, Data, Administratif)',
                    ];
                @endphp

                @foreach($percentages as $dim => $pct)
                    @php
                        $color = $dimColors[$dim] ?? 'from-teal-500 to-emerald-500';
                        $label = $dimNames[$dim] ?? ucfirst(strtolower(str_replace('_', ' ', $dim)));
                        $raw = $rawScores[$dim] ?? 0;
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $label }}</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $pct }}% <span class="text-[10px] text-slate-400 font-normal">({{ $raw }} pts)</span></span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-linear-to-r {{ $color }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1">
                <div class="font-semibold text-slate-700 dark:text-slate-300">Catatan Interpretasi:</div>
                <p>Persentase tertinggi mencerminkan preferensi primer siswa yang disarankan menjadi fokus utama pendekatan bimbingan belajar dan pengembangan karier.</p>
            </div>
        </div>

        <!-- Interpretasi & Rekomendasi Detail -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Box Kesimpulan & Arah Rekomendasi Strategis (Executive Conclusion) -->
            <div class="p-6 rounded-3xl bg-linear-to-r from-teal-500/10 via-emerald-500/10 to-indigo-500/10 border-2 border-teal-500/30 dark:border-teal-500/40 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-teal-500 animate-ping"></span>
                        <h3 class="text-sm font-extrabold uppercase tracking-wide text-teal-900 dark:text-teal-200">
                            Kesimpulan & Arah Rekomendasi Utama (Executive Summary)
                        </h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-600 text-white uppercase tracking-wider">
                        Rekomendasi POP-BK
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-teal-200/50 dark:border-slate-800 space-y-1">
                        <div class="text-[10px] uppercase font-bold text-slate-400">Pola Dominan Teridentifikasi:</div>
                        <div class="text-sm font-black text-teal-700 dark:text-teal-300">{{ $result->profile_label }}</div>
                        <div class="text-[11px] text-slate-500 leading-tight">Prioritas dimensi utama: <strong class="text-slate-800 dark:text-slate-200">{{ $result->dominant_dimension }}</strong> @if($result->secondary_dimension) &bull; Sekunder: <strong class="text-slate-800 dark:text-slate-200">{{ $result->secondary_dimension }}</strong>@endif</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-teal-200/50 dark:border-slate-800 space-y-1">
                        <div class="text-[10px] uppercase font-bold text-slate-400">Arah Rekomendasi Masa Depan:</div>
                        <div class="text-sm font-black text-indigo-700 dark:text-indigo-300">
                            @if(!empty($result->career_recommendations) && isset($result->career_recommendations[0]))
                                {{ $result->career_recommendations[0]['major'] ?? 'Pengembangan Bakat Akademik' }}
                            @else
                                Optimalisasi Minat Vokasional
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500 leading-tight">Diarahkan untuk peminatan SNBP, SNBT, dan rencana karier terarah.</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-teal-200/40 dark:border-slate-800 flex items-center justify-between text-[11px]">
                    <span class="text-slate-600 dark:text-slate-300">Pendampingan: <strong>Guru BK & Wali Kelas</strong></span>
                    <span class="text-teal-700 dark:text-teal-400 font-semibold">Tervalidasi Resmi untuk Portofolio Siswa</span>
                </div>
            </div>

            <!-- Ringkasan Analisis Psikologis -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                    <div class="w-7 h-7 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span>Ulasan Dinamika Psikologis & Kepribadian</span>
                </div>
                <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 space-y-2 whitespace-pre-line">
                    {{ $result->analysis_summary }}
                </div>
            </div>

            <!-- Strategi Belajar Efektif -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                    <div class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span>Strategi & Tips Belajar Mandiri yang Efektif</span>
                </div>
                <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line font-medium">
                    {{ $result->study_recommendations }}
                </div>
            </div>

            <!-- Rekomendasi Jurusan & Karier -->
            @if(!empty($result->career_recommendations))
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                            <div class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span>Rekomendasi Program Studi Perguruan Tinggi & Arah Karier</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($result->career_recommendations as $c)
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 space-y-2 hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300">
                                        {{ $c['field'] ?? 'Bidang Studi' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $c['major'] ?? '-' }}
                                </h4>
                                <div class="pt-1.5 border-t border-slate-200/60 dark:border-slate-700/60 text-[11px] text-slate-500 dark:text-slate-400">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Prospek Karier:</span> {{ $c['prospect'] ?? '-' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Analisis Butir & Indikator Soal Terperinci (Item Diagnostics) -->
            @if(!empty($result->item_analysis))
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                            <div class="w-7 h-7 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <span>Diagnostik Butir Soal Terperinci (Item-Level Diagnostic)</span>
                        </div>
                        <span class="text-[10.5px] text-slate-400">Total butir dianalisis: {{ $result->item_analysis['total_items_answered'] ?? '-' }} butir</span>
                    </div>

                    <!-- Kekuatan Utama (Skor Tertinggi) -->
                    @if(!empty($result->item_analysis['top_strengths']))
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                <span>⭐ Indikator Perilaku & Kekuatan Dominan Siswa</span>
                            </div>
                            <div class="space-y-2.5">
                                @foreach($result->item_analysis['top_strengths'] as $item)
                                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-200/60 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 uppercase">
                                                Dimensi: {{ $item['dimension'] ?? '-' }}
                                            </span>
                                            <span class="font-bold text-[11px] text-emerald-700 dark:text-emerald-300">Skor: {{ $item['score'] }}/4 (Sangat Kuat)</span>
                                        </div>
                                        <p class="font-medium text-slate-800 dark:text-slate-200 leading-snug">"{{ $item['statement'] }}"</p>
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 border-t border-emerald-200/40 dark:border-emerald-900/30">
                                            <strong class="text-emerald-800 dark:text-emerald-300">Interpretasi:</strong> {{ $item['interpretation'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Area Pertumbuhan / Kurang Diminati -->
                    @if(!empty($result->item_analysis['growth_areas']))
                        <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400">
                                <span>💡 Indikator Area Pertumbuhan & Preferensi Rendah</span>
                            </div>
                            <div class="space-y-2.5">
                                @foreach($result->item_analysis['growth_areas'] as $item)
                                    <div class="p-3.5 rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-200/60 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 uppercase">
                                                Dimensi: {{ $item['dimension'] ?? '-' }}
                                            </span>
                                            <span class="font-bold text-[11px] text-amber-700 dark:text-amber-300">Skor: {{ $item['score'] }}/4 (Jarang/Kurang Cocok)</span>
                                        </div>
                                        <p class="font-medium text-slate-800 dark:text-slate-200 leading-snug">"{{ $item['statement'] }}"</p>
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 border-t border-amber-200/40 dark:border-amber-900/30">
                                            <strong class="text-amber-800 dark:text-amber-300">Rekomendasi Intervensi:</strong> {{ $item['recommendation'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Matriks Rencana Tindak Lanjut (RTL) Layanan Bimbingan Konseling (POP-BK) -->
            @if(!empty($result->action_plan))
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                            <div class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <span>Matriks Rencana Tindak Lanjut (RTL) Layanan BK (Standar POP-BK)</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white uppercase tracking-wider self-start sm:self-auto">
                            Tindak Lanjut Komprehensif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Bidang Pribadi & Sosial -->
                        @if(isset($result->action_plan['bidang_pribadi']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-indigo-700 dark:text-indigo-400">
                                    <span>👤 1. Bidang Bimbingan Pribadi & Sosial</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Tujuan:</strong> {{ $result->action_plan['bidang_pribadi']['tujuan'] ?? '-' }}</div>
                                    <div><strong class="text-slate-900 dark:text-white">Strategi:</strong> {{ $result->action_plan['bidang_pribadi']['strategi_layanan'] ?? ($result->action_plan['bidang_pribadi']['layanan'] ?? '-') }}</div>
                                    @if(isset($result->action_plan['bidang_pribadi']['format_layanan']))
                                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            <strong>Format Layanan:</strong> {{ $result->action_plan['bidang_pribadi']['format_layanan'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 2. Bidang Belajar & Pembelajaran Berdiferensiasi -->
                        @if(isset($result->action_plan['bidang_belajar']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-teal-700 dark:text-teal-400">
                                    <span>📚 2. Bidang Bimbingan Belajar & Diferensiasi</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Tujuan:</strong> {{ $result->action_plan['bidang_belajar']['tujuan'] ?? '-' }}</div>
                                    <div><strong class="text-slate-900 dark:text-white">Strategi:</strong> {{ $result->action_plan['bidang_belajar']['strategi_layanan'] ?? ($result->action_plan['bidang_belajar']['layanan'] ?? '-') }}</div>
                                    @if(isset($result->action_plan['bidang_belajar']['diferensiasi_pembelajaran']))
                                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            <strong>Diferensiasi:</strong> {{ $result->action_plan['bidang_belajar']['diferensiasi_pembelajaran'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 3. Bidang Karier & Peminatan Studi Lanjut -->
                        @if(isset($result->action_plan['bidang_karier']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400">
                                    <span>🎯 3. Bidang Bimbingan Karier & Peminatan PTN</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Tujuan:</strong> {{ $result->action_plan['bidang_karier']['tujuan'] ?? '-' }}</div>
                                    <div><strong class="text-slate-900 dark:text-white">Strategi:</strong> {{ $result->action_plan['bidang_karier']['strategi_layanan'] ?? ($result->action_plan['bidang_karier']['layanan'] ?? '-') }}</div>
                                    @if(isset($result->action_plan['bidang_karier']['fokus_studi_lanjut']))
                                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            <strong>Fokus Studi:</strong> {{ $result->action_plan['bidang_karier']['fokus_studi_lanjut'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 4. Kolaborasi Guru Mata Pelajaran -->
                        @if(isset($result->action_plan['kolaborasi_guru_mapel']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                    <span>🤝 4. Kolaborasi Guru Mata Pelajaran & Wali Kelas</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Tujuan:</strong> {{ $result->action_plan['kolaborasi_guru_mapel']['tujuan'] ?? '-' }}</div>
                                    <div><strong class="text-slate-900 dark:text-white">Instruksional:</strong> {{ $result->action_plan['kolaborasi_guru_mapel']['rekomendasi_instruksional'] ?? ($result->action_plan['kolaborasi_guru_mapel']['rekomendasi'] ?? '-') }}</div>
                                    @if(isset($result->action_plan['kolaborasi_guru_mapel']['strategi_asesmen']))
                                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            <strong>Asesmen Kelas:</strong> {{ $result->action_plan['kolaborasi_guru_mapel']['strategi_asesmen'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 5. Panduan Peran Orang Tua di Rumah -->
                        @if(isset($result->action_plan['peran_orang_tua']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-rose-700 dark:text-rose-400">
                                    <span>🏡 5. Panduan Pendampingan Orang Tua di Rumah</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Tujuan:</strong> {{ $result->action_plan['peran_orang_tua']['tujuan'] ?? '-' }}</div>
                                    <div><strong class="text-slate-900 dark:text-white">Pendampingan:</strong> {{ $result->action_plan['peran_orang_tua']['rekomendasi_di_rumah'] ?? ($result->action_plan['peran_orang_tua']['rekomendasi'] ?? '-') }}</div>
                                    @if(isset($result->action_plan['peran_orang_tua']['larangan_dan_hindari']))
                                        <div class="text-[11px] text-rose-600 dark:text-rose-400 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                            <strong>Hindari:</strong> {{ $result->action_plan['peran_orang_tua']['larangan_dan_hindari'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 6. Indikator Evaluasi & Jadwal Monitoring -->
                        @if(isset($result->action_plan['indikator_evaluasi']))
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-cyan-700 dark:text-cyan-400">
                                    <span>⏱️ 6. Indikator Keberhasilan & Waktu Evaluasi</span>
                                </div>
                                <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                                    <div><strong class="text-slate-900 dark:text-white">Indikator:</strong> {{ $result->action_plan['indikator_evaluasi']['indikator'] ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                        <strong>Waktu Evaluasi:</strong> {{ $result->action_plan['indikator_evaluasi']['waktu_evaluasi'] ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Dasar Teori & Sumber Rujukan Ilmiah Resmi -->
            @if(!empty($result->scientific_sources))
                <div class="p-6 rounded-3xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                        <div class="w-7 h-7 rounded-xl bg-violet-100 dark:bg-violet-950/60 text-violet-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span>Dasar Teori Psikometris & Landasan Regulasi Resmi (Scientific References)</span>
                    </div>

                    <div class="text-xs space-y-3">
                        <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700">
                            <div class="font-bold text-slate-900 dark:text-white text-xs mb-1">
                                {{ $result->scientific_sources['theory_name'] ?? 'Teori Psikologis Standar' }}
                            </div>
                            <div class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                                <strong>Pionir & Penemu:</strong> {{ $result->scientific_sources['pioneers'] ?? '-' }}
                            </div>
                        </div>

                        @if(!empty($result->scientific_sources['primary_literature']))
                            <div class="space-y-1.5">
                                <div class="font-bold text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    📖 Literatur Primer & Referensi Akademik Terindeks:
                                </div>
                                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600 dark:text-slate-400 pl-1 leading-relaxed">
                                    @foreach($result->scientific_sources['primary_literature'] as $lit)
                                        <li>{{ $lit }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($result->scientific_sources['regulatory_framework']))
                            <div class="space-y-1.5 pt-2 border-t border-slate-200/60 dark:border-slate-800">
                                <div class="font-bold text-[11px] text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    🏛️ Kerangka Kerja Regulasi & Standar Profesi Resmi:
                                </div>
                                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600 dark:text-slate-400 pl-1 leading-relaxed">
                                    @foreach($result->scientific_sources['regulatory_framework'] as $reg)
                                        <li>{{ $reg }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

