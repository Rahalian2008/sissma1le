@extends('layouts.app')

@section('title', 'Peta Gaya Belajar & Profil Psikologis Kelas - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <a href="{{ route('bk.psychological.index') }}" class="hover:text-emerald-600 font-medium">Tes Psikotes</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Peta Profil Kelas</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Peta Gaya Belajar & Profil Karier Kelas</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemetaan modalitas belajar (VAK) dan tipe minat karier (Holland RIASEC) per rombel untuk pembelajaran berdiferensiasi Kurikulum Merdeka</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.psychological.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-200 transition">
                <span>Daftar Asesmen Psikotes</span>
            </a>
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition">
                <span>Dashboard BK</span>
            </a>
        </div>
    </div>

    <!-- Filter Pemilihan Kelas -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form action="{{ route('bk.psychological.class-report') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Rombel / Kelas:</label>
            <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3 font-semibold">
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClass && $selectedClass->id == $c->id ? 'selected' : '' }}>
                        Kelas {{ $c->name }} ({{ $c->students_count ?? $c->students->count() }} Siswa)
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                Tampilkan Pemetaan
            </button>
        </form>

        @if($selectedClass)
            <div class="text-xs text-slate-500 dark:text-slate-400">
                Wali Kelas: <b class="text-slate-800 dark:text-white">{{ $selectedClass->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</b>
            </div>
        @endif
    </div>

    @if($selectedClass)
        @php
            $totalStudents = $selectedClass->students->count();
            $testedVak = $vakResults->count();
            $vakPct = $totalStudents > 0 ? round(($testedVak / $totalStudents) * 100) : 0;
            $testedRiasec = $riasecResults->count();
            $riasecPct = $totalStudents > 0 ? round(($testedRiasec / $totalStudents) * 100) : 0;
        @endphp

        <!-- Ringkasan Statistik Kelas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa Kelas</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalStudents }} <span class="text-xs font-normal text-slate-400">Siswa</span></div>
                <div class="text-[11px] text-slate-500">Rombel: {{ $selectedClass->name }}</div>
            </div>

            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-blue-200 dark:border-blue-900/50 shadow-xs space-y-1">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Partisipasi Tes Gaya Belajar</span>
                <div class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $testedVak }} / {{ $totalStudents }}</div>
                <div class="text-[11px] text-slate-500">{{ $vakPct }}% siswa telah mengisi tes VAK</div>
            </div>

            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-900/50 shadow-xs space-y-1">
                <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Partisipasi Tes Karier Holland</span>
                <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $testedRiasec }} / {{ $totalStudents }}</div>
                <div class="text-[11px] text-slate-500">{{ $riasecPct }}% siswa telah mengisi tes RIASEC</div>
            </div>

            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/50 shadow-xs space-y-1">
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Modalitas Terbesar</span>
                @php
                    arsort($vakSummary);
                    $topVak = array_key_first($vakSummary) ?: '-';
                @endphp
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 uppercase">{{ $topVak }}</div>
                <div class="text-[11px] text-slate-500">{{ $vakSummary[$topVak] ?? 0 }} siswa memiliki gaya ini</div>
            </div>
        </div>

        <!-- Visualisasi Distribusi Modalitas VAK & Minat Holland -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribusi VAK -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Distribusi Modalitas Gaya Belajar (VAK)
                    </h3>
                    <p class="text-xs text-slate-400">Komposisi kecenderungan cara belajar siswa di kelas {{ $selectedClass->name }}</p>
                </div>

                <div class="space-y-3.5 pt-2">
                    @php
                        $vakTotal = array_sum($vakSummary) ?: 1;
                        $vakColors = [
                            'VISUAL' => ['name' => 'Visual (Melihat & Mengamati)', 'bar' => 'from-blue-500 to-indigo-500', 'badge' => 'text-blue-600 bg-blue-50 dark:bg-blue-950/50'],
                            'AUDITORI' => ['name' => 'Auditori (Mendengar & Diskusi)', 'bar' => 'from-amber-500 to-orange-500', 'badge' => 'text-amber-600 bg-amber-50 dark:bg-amber-950/50'],
                            'KINESTETIK' => ['name' => 'Kinestetik (Aktivitas & Praktik)', 'bar' => 'from-emerald-500 to-teal-500', 'badge' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50'],
                        ];
                    @endphp

                    @foreach(['VISUAL', 'AUDITORI', 'KINESTETIK'] as $k)
                        @php
                            $cnt = $vakSummary[$k] ?? 0;
                            $pct = round(($cnt / $vakTotal) * 100);
                            $cfg = $vakColors[$k];
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $cfg['name'] }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $cnt }} Siswa ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div class="h-full rounded-full bg-linear-to-r {{ $cfg['bar'] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pedagogical Recommendation -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-xs space-y-1.5">
                    <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Saran Pembelajaran Berdiferensiasi (Kurikulum Merdeka):</span>
                    </div>
                    @if($topVak === 'VISUAL')
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-[11px]">
                            Kelas ini didominasi oleh pembelajar <b>Visual</b>. Guru mata pelajaran disarankan memperkaya bahan ajar dengan salindia infografis, video simulasi, mind map berwarna, serta penugasan pembuatan poster/diagram konsep.
                        </p>
                    @elseif($topVak === 'AUDITORI')
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-[11px]">
                            Kelas ini didominasi oleh pembelajar <b>Auditori</b>. Metode pembelajaran kooperatif seperti jigsaw, debat terstruktur, diskusi pleno kelas, serta tanya-jawab interaktif akan sangat menstimulasi pemahaman siswa.
                        </p>
                    @else
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-[11px]">
                            Kelas ini didominasi oleh pembelajar <b>Kinestetik</b>. Prioritaskan praktikum laboratorium, demonstrasi nyata, simulasi bermain peran (role-playing), dan proyek berbasis masalah (PBL) agar energi fisik tersalurkan secara konstruktif.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Distribusi Holland RIASEC -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        Distribusi Tipe Kepribadian Karier (Holland RIASEC)
                    </h3>
                    <p class="text-xs text-slate-400">Sebaran orientasi karier siswa untuk pendampingan pemilihan program studi</p>
                </div>

                <div class="space-y-3 pt-2">
                    @php
                        $riasecTotal = array_sum($riasecSummary) ?: 1;
                        $rLabels = [
                            'R' => ['label' => 'R - Realistic (Teknik, Mesin, Fisik)', 'color' => 'bg-rose-500'],
                            'I' => ['label' => 'I - Investigative (Sains, Riset, Analisis)', 'color' => 'bg-blue-500'],
                            'A' => ['label' => 'A - Artistic (Seni, Desain, Kreativitas)', 'color' => 'bg-pink-500'],
                            'S' => ['label' => 'S - Social (Edukasi, Pelayanan, Konseling)', 'color' => 'bg-emerald-500'],
                            'E' => ['label' => 'E - Enterprising (Bisnis, Kepemimpinan, Promosi)', 'color' => 'bg-amber-500'],
                            'C' => ['label' => 'C - Conventional (Data, Kearsipan, Akuntansi)', 'color' => 'bg-cyan-500'],
                        ];
                    @endphp

                    @foreach(['R', 'I', 'A', 'S', 'E', 'C'] as $code)
                        @php
                            $cnt = $riasecSummary[$code] ?? 0;
                            $pct = round(($cnt / $riasecTotal) * 100);
                            $info = $rLabels[$code];
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $info['label'] }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $cnt }} Siswa ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div class="h-full rounded-full {{ $info['color'] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 text-[11px] text-slate-600 dark:text-slate-400">
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">Pendampingan Guru BK:</span>
                    Data ini digunakan saat sesi perencanaan individual karier siswa menjelang SNBP/SNBT dan pemilihan jurusan mata pelajaran pilihan fase F (Kelas XI - XII).
                </div>
            </div>
        </div>

        <!-- Tabel Pemetaan Individual Siswa Per Kelas -->
        <div class="space-y-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Hasil Asesmen Individual Siswa Kelas {{ $selectedClass->name }}</h3>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">NISN</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Gaya Belajar (VAK)</th>
                                <th class="py-3 px-4">Minat Karier (RIASEC)</th>
                                <th class="py-3 px-4 text-center">Aksi Bimbingan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @php
                                $vakMap = $vakResults->keyBy('student_id');
                                $riasecMap = $riasecResults->keyBy('student_id');
                            @endphp
                            @forelse($selectedClass->students as $idx => $st)
                                @php
                                    $stVak = $vakMap->get($st->id);
                                    $stRiasec = $riasecMap->get($st->id);
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono text-slate-500">{{ $st->nisn ?: '-' }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                        {{ $st->name }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($stVak)
                                            <a href="{{ route('bk.psychological.result', $stVak) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition">
                                                <span>{{ $stVak->dominant_dimension }}</span>
                                                <span class="text-[10px] text-emerald-500">({{ $stVak->dimension_scores['percentages'][$stVak->dominant_dimension] ?? 0 }}%)</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Belum Ambil Tes</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($stRiasec)
                                            <a href="{{ route('bk.psychological.result', $stRiasec) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                                                <span>{{ $stRiasec->holland_code ?: $stRiasec->dominant_dimension }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Belum Ambil Tes</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('bk.student.profile360', $st->id) }}" class="px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 hover:bg-teal-100 transition text-[11px] font-semibold" title="Buka Rekam Jejak 360">
                                                Profil 360
                                            </a>
                                            <a href="{{ route('bk.counseling.create', ['student_id' => $st->id]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition text-[11px] font-semibold" title="Konseling Siswa">
                                                + Konseling
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Tidak ada data siswa dalam kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

