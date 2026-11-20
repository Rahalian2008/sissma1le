@extends('layouts.app')

@section('title', 'Laporan Asesmen Holistik 360° AI - ' . $student->name)

@section('content')
@php
    $priority = $report['prioritas_keputusan'] ?? [
        'level' => 'PRIORITAS_1',
        'label' => 'PENGEMBANGAN POTENSI',
        'deskripsi' => 'Peserta didik memiliki profil potensi holistik yang prospektif.',
        'alasan' => 'Hasil integrasi seluruh asesmen menunjukkan keseimbangan minat dan bakat.'
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
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.psychological.index') }}" class="hover:text-emerald-600 font-medium">Tes Psikotes</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">Grand Sintesis 360°</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bk.student.profile360', $student->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Profil 360</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-xs shadow-md shadow-teal-600/20 hover:bg-teal-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Grand Laporan</span>
            </button>
        </div>
    </div>

    <!-- Hero Card Grand Sintesis -->
    <div class="p-6 sm:p-8 rounded-3xl bg-linear-to-br from-indigo-950 via-slate-900 to-teal-950 text-white shadow-xl relative overflow-hidden border border-slate-700">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500/30 text-indigo-200 border border-indigo-400/30">
                        Grand Asesmen Psikologis 360°
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-300">
                        Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        {{ $provider }}
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Sintesis Holistik Potensi & Arah Karier Siswa
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Penggabungan data multi-metode: Minat Karier Holland, Gaya Belajar VAK, Kecerdasan Majemuk Gardner, serta Rekam Jejak Kehadiran dan Karakter Siswa di SMAN 1 Lengkong.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-xs pt-2 border-t border-slate-700/60">
                    <div>Nama: <span class="font-bold text-teal-300">{{ $student->name }}</span></div>
                    <div>NISN: <span class="font-mono text-slate-300">{{ $student->nisn }}</span></div>
                    <div>Kelas: <span class="font-bold text-slate-300">{{ $student->schoolClass?->name ?? '-' }}</span></div>
                </div>
            </div>

            <div class="shrink-0 flex flex-col items-center justify-center p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md text-center min-w-[200px]">
                <span class="text-[10px] uppercase font-bold tracking-wider text-teal-300 mb-1">Status Portofolio</span>
                <span class="text-xl sm:text-2xl font-black text-white uppercase">Terpadu 360°</span>
                <div class="mt-3 px-3 py-1 rounded-full text-[10px] font-bold bg-teal-500 text-slate-950 uppercase tracking-widest">
                    Standar POP-BK
                </div>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Prioritas Keputusan Konseling -->
    <div class="p-6 rounded-3xl {{ $priorityColor['bg'] }} border-2 {{ $priorityColor['border'] }} shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $priorityColor['glow'] }} animate-ping"></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prioritas Rekomendasi Konseling</span>
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $priorityColor['badge'] }} uppercase tracking-wider">
                    {{ $priority['level'] ?? 'PRIORITAS_1' }}: {{ $priority['label'] ?? 'PENGEMBANGAN POTENSI' }}
                </span>
            </div>
        </div>
        <p class="text-xs sm:text-sm font-semibold {{ $priorityColor['text'] }} leading-relaxed">
            {{ $priority['deskripsi'] ?? '-' }}
        </p>
    </div>

    <!-- Ringkasan Eksekutif -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
            <div class="w-7 h-7 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center font-black text-xs">
                I
            </div>
            <span>Ringkasan Eksekutif Hasil Sintesis Holistik</span>
        </div>
        <div class="text-xs sm:text-sm leading-relaxed text-slate-700 dark:text-slate-200 bg-slate-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 whitespace-pre-line">
            {{ $report['ringkasan_eksekutif'] ?? 'Data sintesis asesmen holistik sedang diolah oleh sistem AI.' }}
        </div>
    </div>

    <!-- Profil 6 Bidang Master Prompt -->
    @php
        $potensi = $report['profil_potensi'] ?? [];
        $growth = $report['kekuatan_dan_area_pengembangan'] ?? [];
        $pendidikan = $report['rekomendasi_pendidikan'] ?? [];
        $ptKarir = $report['rekomendasi_perguruan_tinggi_dan_karir'] ?? [];
        $rtl = $report['rencana_tindak_lanjut'] ?? [];
        $stakeholders = $report['rekomendasi_pihak_sekolah'] ?? [];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kekuatan Utama & Bakat -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
            <div class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                <span>⭐ Peta Kekuatan & Potensi Utama:</span>
            </div>
            <ul class="space-y-2 list-disc list-inside text-xs text-slate-700 dark:text-slate-300">
                @foreach($growth['kekuatan_utama'] ?? ['Kemampuan berpikir logis dan analitis', 'Kesiapan adaptasi pada peminatan studi lanjut'] as $k)
                    <li>{{ $k }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Area Pengembangan Diri -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
            <div class="text-xs font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                <span>💡 Area Pengembangan & Regulasi Diri:</span>
            </div>
            <ul class="space-y-2 list-disc list-inside text-xs text-slate-700 dark:text-slate-300">
                @foreach($growth['area_pengembangan'] ?? ['Manajemen waktu belajar', 'Penguatan konsistensi kehadiran dan disiplin harian'] as $a)
                    <li>{{ $a }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Rekomendasi Program Studi PTN & Karier -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="text-xs font-bold text-indigo-800 dark:text-indigo-300">
            🎓 3 Alternatif Rumpun Studi Perguruan Tinggi & Karier Masa Depan:
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            @foreach($ptKarir['program_studi'] ?? [
                ['jurusan' => 'Teknologi Informasi & Sains Terapan', 'fakultas' => 'Teknologi', 'alasan' => 'Sesuai dengan cara berpikir terstruktur.'],
                ['jurusan' => 'Bisnis, Manajemen & Komunikasi', 'fakultas' => 'Ekonomi & Sosial', 'alasan' => 'Mendukung bakat interaksi strategis.'],
                ['jurusan' => 'Sains Sosial & Desain Inovasi', 'fakultas' => 'Humaniora', 'alasan' => 'Menyalurkan kreativitas bermakna.']
            ] as $idx => $ps)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Pilihan {{ $idx+1 }}</span>
                    <div class="font-black text-xs sm:text-sm text-slate-900 dark:text-white">{{ $ps['jurusan'] ?? ($ps['major'] ?? '-') }}</div>
                    <p class="text-[11px] text-slate-500 leading-snug">{{ $ps['alasan'] ?? ($ps['prospect'] ?? '-') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Timeline RTL & Kolaborasi Sekolah -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="text-xs font-bold text-teal-800 dark:text-teal-300">
            ⏱️ Rencana Tindak Lanjut (RTL) & Panduan Orang Tua:
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-teal-50/50 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/40 space-y-1">
                <strong>Bulan Ke-1:</strong>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">{{ $rtl['jangka_pendek_1_bulan'][0]['aktivitas'] ?? 'Sesi bimbingan konseling dan penyusunan personal learning plan.' }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 space-y-1">
                <strong>Bulan Ke-3:</strong>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">{{ $rtl['jangka_menengah_3_bulan'][0]['aktivitas'] ?? 'Eksplorasi seleksi SNBP/SNBT perguruan tinggi.' }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40 space-y-1">
                <strong>Bulan Ke-6:</strong>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">{{ $rtl['jangka_panjang_6_bulan'][0]['aktivitas'] ?? 'Evaluasi kemajuan belajar dan pembinaan bakat.' }}</p>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 text-xs">
            <strong>🏡 Rekomendasi Pendampingan Orang Tua di Rumah:</strong>
            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1">{{ $stakeholders['orang_tua'] ?? 'Memberikan iklim belajar yang apresiatif dan mendukung cita-cita pendidikan ananda.' }}</p>
        </div>
    </div>
</div>
@endsection
