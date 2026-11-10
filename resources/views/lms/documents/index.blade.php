@extends('layouts.app')

@section('title', 'Pusat Cetak & Arsip Dokumen Kurikulum - SMAN 1 Lengkong')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 border border-blue-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Modul H • Pusat Arsip & Standardisasi Dokumen Kedinasan
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Pusat Cetak & Arsip Kurikulum</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Portal terpadu satu pintu (*One-Stop Printing & Archive Hub*) untuk mencetak seluruh instrumen dan perangkat ajar Kurikulum Merdeka berstandar resmi Dinas Pendidikan Provinsi Jawa Barat.
                </p>
            </div>

            <!-- Global Export Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if($isLeader)
                    <a href="{{ route('lms.documents.export.matrix') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                        <span>📊</span>
                        <span>Ekspor Matriks Kesiapan (CSV)</span>
                    </a>
                @endif
                <a href="{{ route('lms.documents.export.journals') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 border border-slate-700">
                    <span>📖</span>
                    <span>Ekspor Jurnal KBM (CSV)</span>
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-slate-400 text-xs font-semibold">Modul Ajar Disahkan</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-300">{{ $stats['total_approved_modules'] }} Modul</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-blue-400 text-xs font-semibold">Jurnal KBM Terdata</span>
                <span class="text-xl sm:text-2xl font-black text-blue-300">{{ $stats['total_journals'] }} Agenda</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-purple-400 text-xs font-semibold">Soal Terhubung TP</span>
                <span class="text-xl sm:text-2xl font-black text-purple-300">{{ $stats['total_questions_tp'] }} Butir</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-amber-400 text-xs font-semibold">Catatan Refleksi</span>
                <span class="text-xl sm:text-2xl font-black text-amber-300">{{ $stats['total_reflections'] }} Dokumen</span>
            </div>
        </div>
    </div>

    <!-- Filter Kelas & Mata Pelajaran Terpilih -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('lms.documents.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Kelas & Mata Pelajaran untuk Dicetak:</label>
                <select name="course_class_id" onchange="this.form.submit()" class="w-full sm:w-96 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                    @foreach($courseClasses as $cc)
                        <option value="{{ $cc->id }}" {{ $selectedCourseClass?->id == $cc->id ? 'selected' : '' }}>
                            {{ $cc->subject->name ?? 'Mapel' }} — {{ $cc->schoolClass->name ?? 'Kelas' }} (Guru: {{ $cc->teacher->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-slate-500">
                Tahun Ajaran: <strong>2026/2027</strong> • Status Kop: <strong>Resmi Cabang Dinas Wilayah V</strong>
            </div>
        </form>
    </div>

    <!-- Catalog of Official Documents to Print -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. Program Tahunan (PROTA) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-xl">
                        📅
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Program Tahunan (PROTA)
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Distribusi alokasi jam pelajaran (JP) seluruh Tujuan Pembelajaran (TP) selama satu tahun ajaran penuh (Semester Ganjil dan Genap).
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.documents.prota.print', ['course_class_id' => $selectedCourseClass?->id]) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>🖨️</span>
                    <span>Cetak Dokumen PROTA</span>
                </a>
            </div>
        </div>

        <!-- 2. Program Semester (PROMES) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-xl">
                        🗓️
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Program Semester (PROMES)
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Matriks alokasi mingguan per bulan (Juli-Desember / Januari-Juni), minggu efektif, dan jadwal pelaksanaan tatap muka KBM.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4 flex items-center gap-2">
                <a href="{{ route('lms.documents.promes.print', ['course_class_id' => $selectedCourseClass?->id, 'semester' => 'GANJIL']) }}" target="_blank" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition text-center">
                    Cetak Ganjil
                </a>
                <a href="{{ route('lms.documents.promes.print', ['course_class_id' => $selectedCourseClass?->id, 'semester' => 'GENAP']) }}" target="_blank" class="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold shadow-md transition text-center border border-slate-700">
                    Cetak Genap
                </a>
            </div>
        </div>

        <!-- 3. Alur Tujuan Pembelajaran (ATP) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-xl">
                        🎯
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Alur Tujuan Pembelajaran (ATP)
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Peta urutan logis TP dari Capaian Pembelajaran (CP) Fase E/F, ruang lingkup materi, serta Profil Pelajar Pancasila.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.documents.atp.print', ['course_class_id' => $selectedCourseClass?->id]) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>🖨️</span>
                    <span>Cetak Dokumen ATP</span>
                </a>
            </div>
        </div>

        <!-- 4. Modul Ajar / RPP Digital -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-xl">
                        📝
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 uppercase">
                        A4 Portrait
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Modul Ajar / RPP Digital
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Rancangan modul KBM berstandar Kurikulum Merdeka (sintaks 3 tahap, diferensiasi, asesmen, LKPD, lembar pengesahan).
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.planning.index') }}" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>Buka Portal Modul Ajar →</span>
                </a>
            </div>
        </div>

        <!-- 5. Agenda & Jurnal Mengajar Guru -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-xl">
                        📖
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Agenda & Jurnal Mengajar
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Rekapitulasi pelaksanaan pembelajaran tatap muka harian, kehadiran siswa, ketercapaian TP, dan verifikasi kurikulum.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.journals.print', ['course_class_id' => $selectedCourseClass?->id]) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>🖨️</span>
                    <span>Cetak Agenda Guru</span>
                </a>
            </div>
        </div>

        <!-- 6. Kisi-Kisi Soal Asesmen -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 flex items-center justify-center text-xl">
                        📋
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Kisi-Kisi Penulisan Soal
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Matriks spesifikasi butir soal asesmen sumatif / formatif terpetakan ke TP, level kognitif L1-L3, bentuk soal, dan rubrik.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.assessment.kisi_kisi.print', ['subject_id' => $selectedCourseClass?->subject_id]) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>🖨️</span>
                    <span>Cetak Kisi-Kisi Asesmen</span>
                </a>
            </div>
        </div>

        <!-- 7. Rekapitulasi Catatan Refleksi -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 flex items-center justify-center text-xl">
                        💡
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 uppercase">
                        A4 Landscape
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Rekap Catatan Refleksi Guru
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Evaluasi berkala pendidik pasca pembelajaran (keberhasilan strategi, hambatan kelas, dan tindak lanjut perbaikan KBM).
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.reflections.print', ['course_class_id' => $selectedCourseClass?->id]) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>🖨️</span>
                    <span>Cetak Rekap Refleksi</span>
                </a>
            </div>
        </div>

        <!-- 8. Hasil Supervisi Akademik -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-xl">
                        🛡️
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 uppercase">
                        A4 Portrait
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Instrumen Supervisi Akademik
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Lembar hasil penilaian observasi kelas oleh Kepala Sekolah / Pengawas (4 pilar mutu, skor akhir, dan predikat akreditasi).
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.supervision.index') }}" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>Buka Portal Supervisi →</span>
                </a>
            </div>
        </div>

        <!-- 9. Kurasi Portofolio Siswa -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-fuchsia-50 dark:bg-fuchsia-950/60 border border-fuchsia-200 dark:border-fuchsia-800 flex items-center justify-center text-xl">
                        🎨
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-950/60 dark:text-fuchsia-300 uppercase">
                        A4 Portrait
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Portofolio & Karya Siswa
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Etalase bukti belajar otentik siswa, tugas terbaik, dan capaian proyek penguatan profil pelajar Pancasila (P5).
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('lms.portfolios.index') }}" class="w-full py-2.5 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>Buka Galeri Portofolio →</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

