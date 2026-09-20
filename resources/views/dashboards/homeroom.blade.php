@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Wali Kelas -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-indigo-300 border border-white/10">
                Wali Kelas Binaan
            </span>
            <h2 class="text-2xl font-extrabold text-white mt-2">Kelas {{ $class->name ?? 'XI-IPA-1' }}</h2>
            <p class="text-xs text-slate-300 mt-1">Wali Kelas: {{ $teacher->name ?? Auth::user()->name }} • Total {{ $students->count() }} Siswa Binaan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index', ['class_id' => $class->id ?? null]) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs flex items-center gap-2 border border-white/20 transition">
                Cetak Rekap Kelas
            </a>
        </div>
    </div>

    <!-- Attendance Summary for Class Today -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Hadir Tepat Waktu</span>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $presentToday }} Siswa</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Terlambat</span>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $lateToday }} Siswa</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Belum / Tidak Hadir</span>
            <div class="text-2xl font-black text-rose-600 mt-2">{{ $absentToday }} Siswa</div>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Perlu Bimbingan (≥75 Poin)</span>
            <div class="text-2xl font-black text-indigo-600 mt-2">{{ $needGuidanceStudents->count() }} Siswa</div>
        </div>
    </div>

    <!-- Guidance Alert Notice if any students >= 75 points -->
    @if($needGuidanceStudents->isNotEmpty())
        <div class="p-5 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-950 flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold text-lg">!</div>
            <div>
                <h4 class="font-extrabold text-sm text-amber-900">NOTIFIKASI PERLU TINDAKAN: Siswa Memerlukan Pembinaan Wali Kelas</h4>
                <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                    Terdapat siswa yang telah mencapai akumulasi 75 poin pelanggaran. Sesuai dokumen tata tertib SMAN 1 Lengkong, segera lakukan sesi bimbingan konseling dan berikan catatan pembinaan.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($needGuidanceStudents as $ngs)
                        <a href="{{ route('guidance.create', ['student_id' => $ngs->id]) }}" class="px-3 py-1 bg-amber-500 text-white font-bold rounded-xl text-xs hover:bg-amber-600 transition">
                            Bimbing {{ $ngs->name }} ({{ $ngs->totalViolationPoints() }} Poin) →
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Papan Peringkat Presensi Kelas Binaan -->
    @include('dashboards.partials.attendance_rankings')

    <!-- Students Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Daftar Siswa Binaan Kelas {{ $class->name ?? '' }}</h3>
            <span class="text-xs text-slate-400">Poin Prestasi & Pelanggaran Dihitung Terpisah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Nama Siswa / NIS</th>
                        <th class="py-3.5 px-4">Presensi Hari Ini</th>
                        <th class="py-3.5 px-4">Prestasi Kumulatif</th>
                        <th class="py-3.5 px-4">Pelanggaran Kumulatif</th>
                        <th class="py-3.5 px-4">Status Pembinaan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $s)
                        @php
                            $att = $s->attendances->first();
                            $achPoints = $s->totalAchievementPoints();
                            $violPoints = $s->totalViolationPoints();
                            $guidance = $s->guidance_status;
                            $reward = $s->reward_status;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $s->name }}</div>
                                <div class="text-[11px] text-slate-400">NIS: {{ $s->nis }} • NISN: {{ $s->nisn }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($att)
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $att->status === 'HADIR' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $att->status }} ({{ substr($att->time, 0, 5) }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-500">
                                        Belum Presensi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-amber-600 text-sm">{{ $achPoints }} Poin</span>
                                @if($reward['is_eligible'])
                                    <div class="text-[10px] text-amber-700 font-semibold">{{ $reward['level'] }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-rose-600 text-sm">{{ $violPoints }} Poin</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $guidance['action_needed'] ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $guidance['level'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('reports.student.dossier', $s->id) }}" class="p-1.5 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 rounded-lg text-xs font-semibold" title="Lihat Berkas Perkembangan">
                                        Dossier
                                    </a>
                                    <a href="{{ route('habits.journal', ['student_id' => $s->id]) }}" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold" title="Jurnal 7 Kebiasaan">
                                        7 Kebiasaan
                                    </a>
                                    <a href="{{ route('guidance.create', ['student_id' => $s->id]) }}" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold" title="Input Pembinaan">
                                        Bina
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400">Belum ada siswa terdaftar di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analisis Tata Tertib Kelas Binaan -->
    @include('dashboards.partials.discipline_analytics')

    <!-- Capaian 7 Kebiasaan Anak Hebat Kelas Binaan -->
    @include('dashboards.partials.habits_analytics')
</div>
@endsection
