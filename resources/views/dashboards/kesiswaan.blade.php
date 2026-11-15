@extends('layouts.app')

@section('title', 'Dashboard Kesiswaan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Kesiswaan -->
    <div class="bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-rose-300 border border-white/10">
                Bidang Kesiswaan & Kedisiplinan
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-2">SMAN 1 Lengkong - Kesiswaan</h2>
            <p class="text-xs text-slate-300 mt-1">Pemantauan Tingkat Kehadiran, Penegakan Tata Tertib, dan Penghargaan Siswa</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="{{ route('reports.index') }}" class="w-full sm:w-auto justify-center px-5 py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 text-xs sm:text-sm flex items-center gap-2">
                Pusat Laporan & Rekapitulasi
            </a>
        </div>
    </div>

    <!-- Menu Cepat Staf Kesiswaan -->
    @include('dashboards.partials.staff_quick_actions')

    <!-- School Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Total Siswa Terdaftar</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $totalStudents }}</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Seluruh kelas X, XI, XII</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Kehadiran Hari Ini</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $attendanceRateToday }}%</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $presentToday }} Hadir • {{ $lateToday }} Terlambat</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Total Poin Prestasi Sekolah</span>
            <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $totalAchievementPoints }}</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $totalVerifiedAchievements }} Prestasi Terverifikasi</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Total Poin Pelanggaran</span>
            <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 mt-2">{{ $totalViolationPoints }}</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $totalVerifiedViolations }} Kasus Terverifikasi</p>
        </div>
    </div>

    <!-- High Risk Guidance Students List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Siswa Membutuhkan Tindakan Kesiswaan & SP (≥125 Poin)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Batas toleransi SP1, SP2, SP3, dan Sidang Rapat Khusus Sekolah</p>
            </div>
            <span class="px-3 py-1 bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 text-xs font-bold rounded-full self-start sm:self-center">
                {{ $highRiskStudents->count() }} Siswa dalam Pantauan Khusus
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Nama Siswa / NIS</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4">Total Poin Pelanggaran</th>
                        <th class="py-3.5 px-4">Tahapan Sanksi Tata Tertib</th>
                        <th class="py-3.5 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($highRiskStudents as $student)
                        @php $gStatus = $student->guidance_status; @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $student->name }}
                                <div class="text-[11px] font-normal text-slate-400">NIS: {{ $student->nis }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">{{ $student->schoolClass?->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-sm font-black text-rose-600 dark:text-rose-400">{{ $student->totalViolationPoints() }} Poin</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                    {{ $gStatus['level'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('reports.student.dossier', $student->id) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition">
                                    Raport AI & Dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                Tidak ada siswa dalam kategori pelanggaran berat (≥125 poin).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agenda & Kegiatan Sekolah Terupdate -->
    @include('dashboards.partials.school_agendas_summary')

    <!-- Papan Peringkat Presensi Siswa (Alpa, Izin, Sakit) -->
    @include('dashboards.partials.attendance_rankings')

    <!-- Analisis Tata Tertib (Rangking, Sering Terjadi, Ambang Batas) -->
    @include('dashboards.partials.discipline_analytics')

    <!-- Capaian 7 Kebiasaan Anak Indonesia Hebat -->
    @include('dashboards.partials.habits_analytics')

    <!-- Ringkasan LMS Pembelajaran & Perpustakaan Digital Kesiswaan -->
    @include('dashboards.partials.lms_library_summary')
</div>
@endsection
