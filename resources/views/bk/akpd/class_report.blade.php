@extends('layouts.app')

@section('title', 'Laporan Profil Kebutuhan Kelas (AKPD) - ' . ($selectedClass?->name ?? 'Semua Kelas X'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Breadcrumb & Navigasi -->
    @include('bk.partials.nav')

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-800 via-teal-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-teal-200 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Profil Kebutuhan Tingkat Kelas (Standar POP-BK)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Profil Kebutuhan Kelas: {{ $selectedClass?->name ?? 'Semua Rombel Kelas X' }}
                </h1>
                <p class="text-xs sm:text-sm text-teal-100 max-w-2xl leading-relaxed">
                    Rekapitulasi persentase 4 bidang bimbingan, pemetaan butir masalah tertinggi, dan usulan topik materi Bimbingan Klasikal (RPL BK) Kurikulum Merdeka Fase E.
                </p>
            </div>

            <!-- Tombol Aksi & Filter -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('bk.akpd.print-class', [$akpd, 'class_id' => $selectedClass?->id]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-900/30 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Profil Kelas A4</span>
                </a>
                <a href="{{ route('bk.akpd.show', $akpd) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs hover:bg-white/30 transition-all">
                    <span>&larr; Matriks AKPD</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Kelas & Statistik Partisipasi -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Selector Kelas -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Pilih Rombel / Kelas X</span>
                <form action="{{ route('bk.akpd.class-report', $akpd) }}" method="GET">
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-xs font-bold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Rekap Seluruh Kelas X (Fase E) --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ ($selectedClass?->id == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <div class="mt-3 text-[11px] text-slate-400 leading-snug">
                    Wali Kelas: <strong>{{ $selectedClass?->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</strong>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tingkat Fase:</span>
                <span class="font-extrabold text-indigo-600 dark:text-indigo-400">Fase E (Kelas X)</span>
            </div>
        </div>

        <!-- Metric 1: Total Siswa -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xl">
                👥
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Siswa Terdaftar</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $classData['totalStudents'] }} <span class="text-xs font-normal text-slate-400">Siswa</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Siswa Mengisi -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center font-black text-xl">
                ✍️
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Respon Terkumpul</span>
                <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-0.5">
                    {{ $classData['respondentsCount'] }} <span class="text-xs font-normal text-slate-400">Responden</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: Partisipasi Kelas -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-xl">
                📈
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Persentase Partisipasi</span>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $classData['participationRate'] }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Rata-rata Kebutuhan 4 Bidang Layanan -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">
                    Grafik Komparasi Kebutuhan 4 Bidang Layanan BK di {{ $selectedClass?->name ?? 'Kelas X' }}
                </h3>
                <p class="text-xs text-slate-400">
                    Rata-rata persentase pemilihan butir masalah oleh seluruh peserta didik pada rombel terpilih.
                </p>
            </div>
            <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> &ge;50% Tinggi</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> 26-49% Sedang</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> &le;25% Rendah</span>
            </div>
        </div>

        <!-- Visual Bar Chart Representation -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach(['PRIBADI' => 'Pribadi', 'SOSIAL' => 'Sosial', 'BELAJAR' => 'Belajar', 'KARIER' => 'Karier'] as $fKey => $fLabel)
                @php
                    $stat = $classData['fieldAverages'][$fKey] ?? ['percentage' => 0, 'priority' => 'Rendah', 'color' => 'emerald'];
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Bidang {{ $fLabel }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $stat['percentage'] >= 50 ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($stat['percentage'] >= 26 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                            {{ $stat['priority'] }}
                        </span>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $stat['percentage'] }}%</span>
                        <span class="text-xs text-slate-400">Rata-rata</span>
                    </div>

                    <!-- Visual Column Bar -->
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-3 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 {{ $stat['percentage'] >= 50 ? 'bg-rose-500' : ($stat['percentage'] >= 26 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($stat['percentage'], 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top 10 Butir Masalah Terbanyak di Kelas (Dasar RPL) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-black">
                🎯
            </span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Top 10 Butir Kebutuhan Paling Banyak Dipilih Siswa (Prioritas Layanan Klasikal)
                </h3>
                <p class="text-xs text-slate-400">
                    Butir-butir dengan frekuensi tertinggi ini menjadi skala prioritas utama dalam penyusunan program bimbingan kelas.
                </p>
            </div>
        </div>

        <div class="space-y-2.5">
            @forelse ($classData['topQuestions'] as $idx => $q)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-xs flex items-center justify-center shrink-0">
                            #{{ $idx + 1 }}
                        </span>
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $q->field_of_service === 'PRIBADI' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($q->field_of_service === 'SOSIAL' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($q->field_of_service === 'BELAJAR' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300')) }}">
                                    {{ $q->field_of_service }} ({{ $q->code }})
                                </span>
                                @if($q->profile_dimension)
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $q->profile_dimension }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $q->statement }}</p>
                        </div>
                    </div>
                    <div class="text-right sm:shrink-0 pl-10 sm:pl-0">
                        <div class="text-base font-black text-rose-600 dark:text-rose-400">{{ $q->percentage }}%</div>
                        <div class="text-[10px] font-semibold text-slate-400">{{ $q->chosen_count }} dari {{ $classData['respondentsCount'] }} Siswa</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs">Belum ada respon yang diinputkan untuk kelas ini.</div>
            @endforelse
        </div>
    </div>

    <!-- Rekomendasi Topik RPL Bimbingan Klasikal Berdasarkan Hasil AKPD -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-black">
                📝
            </span>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Usulan Topik Materi Rencana Pelaksanaan Layanan (RPL BK 1 Lembar)
                </h3>
                <p class="text-xs text-slate-400">
                    Pemetaan terpadu butir masalah ke judul materi Bimbingan Klasikal untuk semester aktif.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            @forelse($classData['suggestedRplTopics'] as $t)
                <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-200/70 dark:border-indigo-900/50 flex flex-col justify-between space-y-2">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
                                {{ $t['field'] }} • Butir {{ $t['code'] }}
                            </span>
                            <span class="text-[11px] font-black text-rose-600 dark:text-rose-400">{{ $t['priority'] }} Kebutuhan</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white leading-snug">{{ $t['topic'] }}</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 italic">Masalah: "{{ $t['statement'] }}"</p>
                    </div>
                    <div class="pt-2 border-t border-indigo-100 dark:border-indigo-900/40 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                        <span>Sasaran: {{ $selectedClass?->name ?? 'Kelas X' }}</span>
                        <span>Metode: Klasikal / Diskusi Terarah</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-6 text-slate-400 text-xs">Belum ada usulan topik.</div>
            @endforelse
        </div>
    </div>

    <!-- Matriks Tabulasi Rekap Respon Siswa di Kelas Ini -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                    Matriks Distribusi Kebutuhan Seluruh Siswa di {{ $selectedClass?->name ?? 'Kelas X' }}
                </h3>
                <p class="text-xs text-slate-400">Total: {{ $classData['allStudents']->count() }} Siswa Terdaftar</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa &amp; NISN</th>
                        <th class="py-3 px-4 text-center">Status Asesmen</th>
                        <th class="py-3 px-4 text-center">Pribadi</th>
                        <th class="py-3 px-4 text-center">Sosial</th>
                        <th class="py-3 px-4 text-center">Belajar</th>
                        <th class="py-3 px-4 text-center">Karier</th>
                        <th class="py-3 px-4 text-center">Kebutuhan Dominan</th>
                        <th class="py-3 px-4 text-center">Aksi Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($classData['allStudents'] as $index => $stu)
                        @php
                            $resp = $classData['responseMap'][$stu->id] ?? null;
                            $scores = $resp?->score_per_field ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 font-mono">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $stu->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $stu->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($resp)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Sudah Mengisi
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        Belum Mengisi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-rose-600">
                                {{ $scores['PRIBADI']['percentage'] ?? '-' }}{{ isset($scores['PRIBADI']) ? '%' : '' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-amber-600">
                                {{ $scores['SOSIAL']['percentage'] ?? '-' }}{{ isset($scores['SOSIAL']) ? '%' : '' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-600">
                                {{ $scores['BELAJAR']['percentage'] ?? '-' }}{{ isset($scores['BELAJAR']) ? '%' : '' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">
                                {{ $scores['KARIER']['percentage'] ?? '-' }}{{ isset($scores['KARIER']) ? '%' : '' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($resp)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $resp->highest_need_field === 'PRIBADI' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($resp->highest_need_field === 'SOSIAL' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($resp->highest_need_field === 'BELAJAR' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300')) }}">
                                        {{ $resp->highest_need_field }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($resp)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('bk.akpd.individual', [$akpd, $stu]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold text-[11px] transition" title="Lihat Laporan Lengkap">
                                            Laporan
                                        </a>
                                        <a href="{{ route('bk.akpd.print-individual', [$akpd, $stu]) }}" target="_blank" class="p-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 transition" title="Cetak Format A4">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data peserta didik pada kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
