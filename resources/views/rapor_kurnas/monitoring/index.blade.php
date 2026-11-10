@extends('layouts.app')

@section('title', 'Status Penilaian & Audit Monitoring - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header & Filter Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>📈</span>
                <span>Status Penilaian & Audit Monitoring</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pantau progres penginputan nilai rapor guru mata pelajaran, catatan wali kelas, dan capaian kokurikuler P5 secara realtime (Standar e-Rapor 2025).
            </p>
        </div>

        <!-- Filter Tingkat Kelas -->
        <form method="GET" action="{{ route('rapor_kurnas.monitoring') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Filter Tingkat:</label>
            <select name="grade" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-bold focus:ring-indigo-500">
                <option value="all" {{ $selectedGrade === 'all' ? 'selected' : '' }}>Semua Tingkat (X, XI, XII)</option>
                <option value="X" {{ $selectedGrade === 'X' ? 'selected' : '' }}>Tingkat X (Fase E)</option>
                <option value="XI" {{ $selectedGrade === 'XI' ? 'selected' : '' }}>Tingkat XI (Fase F)</option>
                <option value="XII" {{ $selectedGrade === 'XII' ? 'selected' : '' }}>Tingkat XII (Fase F)</option>
            </select>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kelas Ajar</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $totalCourseClasses }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Mata pelajaran aktif</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                    📚
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Progres Nilai Guru</p>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $completedCourses }} / {{ $totalCourseClasses }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $overallProgress }}% rombel tuntas dinilai</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                    ✅
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Progres Wali Kelas</p>
                    <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $completedHomerooms }} / {{ $classes->count() }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Catatan & absensi lengkap</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                    📋
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Projek P5 Aktif</p>
                    <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ count($projectMonitoring) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kokurikuler semester ini</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                    💡
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Section / Data Views -->
    <div x-data="{ activeTab: 'teachers' }" class="space-y-4">
        <!-- Tab Selector -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
            <button @click="activeTab = 'teachers'" :class="activeTab === 'teachers' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-indigo-600'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>👨‍🏫 Status Guru Mata Pelajaran</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-white/20">{{ count($monitoringData) }}</span>
            </button>
            <button @click="activeTab = 'homeroom'" :class="activeTab === 'homeroom' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-indigo-600'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>📋 Status Wali Kelas</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-white/20">{{ count($homeroomMonitoring) }}</span>
            </button>
            <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-indigo-600'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>💡 Status Kokurikuler (P5)</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-white/20">{{ count($projectMonitoring) }}</span>
            </button>
        </div>

        <!-- 1. TAB GURU MAPEL -->
        <div x-show="activeTab === 'teachers'" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Status Penilaian Guru Mata Pelajaran</h2>
                    <p class="text-xs text-slate-400">Daftar kelas ajar, ketersediaan TP, dan progres pengisian nilai rapor.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Guru Pengampu</th>
                            <th class="p-3">Mata Pelajaran</th>
                            <th class="p-3 text-center">Kelas</th>
                            <th class="p-3 text-center">Target Siswa</th>
                            <th class="p-3 text-center">Dinilai</th>
                            <th class="p-3 text-center">Status TP</th>
                            <th class="p-3 w-48">Progres Penilaian</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($monitoringData as $idx => $row)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-white">
                                    {{ $row['course']->teacher->name ?? '-' }}
                                    <span class="block text-[10px] font-normal text-slate-400">NIP. {{ $row['course']->teacher->nip ?? '-' }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $row['course']->subject->name ?? '-' }}</span>
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $row['course']->subject->code ?? '-' }}</span>
                                </td>
                                <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $row['course']->schoolClass->name ?? '-' }}
                                </td>
                                <td class="p-3 text-center font-bold">{{ $row['total_students'] }}</td>
                                <td class="p-3 text-center font-bold {{ $row['scored_count'] > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                    {{ $row['scored_count'] }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($row['tp_count'] > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            {{ $row['tp_count'] }} TP Siap
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Belum Ada TP
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[11px] font-bold">
                                            <span>{{ $row['percentage'] }}%</span>
                                            <span class="text-slate-400">{{ $row['scored_count'] }}/{{ $row['total_students'] }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $row['percentage'] >= 100 ? 'bg-emerald-500' : ($row['percentage'] > 0 ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-700') }}" style="width: {{ $row['percentage'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    @if($row['is_completed'])
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Lengkap
                                        </span>
                                    @elseif($row['scored_count'] > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            Proses
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('rapor_kurnas.scores', ['course_class_id' => $row['course']->id]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-100 text-[11px] inline-block transition">
                                        Buka Input
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-slate-400">
                                    Tidak ada data kelas ajar untuk kriteria yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TAB WALI KELAS -->
        <div x-show="activeTab === 'homeroom'" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Status Kelengkapan Wali Kelas</h2>
                    <p class="text-xs text-slate-400">Monitoring rekapitulasi kehadiran riil, catatan pengembangan diri, dan keputusan kenaikan kelas.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Nama Kelas</th>
                            <th class="p-3">Wali Kelas</th>
                            <th class="p-3 text-center">Jumlah Siswa</th>
                            <th class="p-3 text-center">Catatan Terisi</th>
                            <th class="p-3 text-center">Status Absensi</th>
                            <th class="p-3 text-center">Keputusan Kenaikan</th>
                            <th class="p-3 w-40">Progres</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($homeroomMonitoring as $idx => $hr)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $hr['class']->name }}</td>
                                <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $hr['class']->homeroomTeacher->name ?? 'Belum Ditugaskan' }}
                                </td>
                                <td class="p-3 text-center font-bold">{{ $hr['total_students'] }}</td>
                                <td class="p-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $hr['filled_notes'] }}/{{ $hr['total_students'] }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($hr['has_attendance'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200">
                                            ✓ Tersinkron
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">
                                            Belum Sinkron
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($hr['decided_promotion'] >= $hr['total_students'] && $hr['total_students'] > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200">
                                            100% Ditentukan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200">
                                            {{ $hr['decided_promotion'] }}/{{ $hr['total_students'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[11px] font-bold">
                                            <span>{{ $hr['percentage'] }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $hr['percentage'] >= 100 ? 'bg-emerald-500' : ($hr['percentage'] > 0 ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-700') }}" style="width: {{ $hr['percentage'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    @if($hr['is_completed'])
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Lengkap
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            Belum Lengkap
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('rapor_kurnas.homeroom', ['class_id' => $hr['class']->id]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-100 text-[11px] inline-block transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-slate-400">
                                    Tidak ada data kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. TAB KOKURIKULER P5 -->
        <div x-show="activeTab === 'projects'" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Status Penilaian Kokurikuler (P5)</h2>
                    <p class="text-xs text-slate-400">Pantau progres penilaian dimensi Profil Pelajar Pancasila per tema projek.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Tema Projek</th>
                            <th class="p-3">Nama Projek</th>
                            <th class="p-3 text-center">Fase / Tingkat</th>
                            <th class="p-3">Koordinator Projek</th>
                            <th class="p-3 text-center">Target Siswa</th>
                            <th class="p-3 text-center">Siswa Dinilai</th>
                            <th class="p-3 w-40">Progres</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($projectMonitoring as $idx => $pm)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-indigo-600 dark:text-indigo-400">{{ $pm['project']->theme }}</td>
                                <td class="p-3 font-semibold text-slate-800 dark:text-white">{{ $pm['project']->name }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded font-bold bg-slate-100 dark:bg-slate-800 text-[10px]">
                                        Fase {{ $pm['project']->fase }} (Kelas {{ $pm['project']->grade }})
                                    </span>
                                </td>
                                <td class="p-3 font-medium text-slate-700 dark:text-slate-300">
                                    {{ $pm['project']->coordinator->name ?? 'Tim Fasilitator' }}
                                </td>
                                <td class="p-3 text-center font-bold">{{ $pm['total_students'] }}</td>
                                <td class="p-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $pm['scored_students'] }}</td>
                                <td class="p-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[11px] font-bold">
                                            <span>{{ $pm['percentage'] }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $pm['percentage'] >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $pm['percentage'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('rapor_kurnas.cocurricular', ['project_id' => $pm['project']->id]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-100 text-[11px] inline-block transition">
                                        Input P5
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-400">
                                    Belum ada projek kokurikuler untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

