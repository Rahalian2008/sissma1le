@extends('layouts.app')

@section('title', 'Berkas Perkembangan Siswa: ' . $student->name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header with Action -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold text-indigo-300 border border-white/10">
                DOSSIER PERKEMBANGAN SISWA TERPADU
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">{{ $student->name }}</h2>
            <p class="text-xs text-slate-300 mt-1">NIS: {{ $student->nis }} • NISN: {{ $student->nisn }} • Kelas: {{ $student->schoolClass?->name }} • {{ $schoolName }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('reports.student.print', $student->id) }}" target="_blank"
                class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-2xl shadow-lg shadow-emerald-500/20 text-xs sm:text-sm flex items-center gap-2 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Ekspor PDF Resmi
            </a>
        </div>
    </div>

    <!-- 4-Pillar Summary Overview -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Tingkat Presensi</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $student->attendancePercentage() }}%</div>
            <p class="text-[10px] text-slate-400 mt-0.5">Total {{ $dossier['attendances']->count() }} Sesi Tercatat</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Poin Prestasi</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $student->totalAchievementPoints() }} Poin</div>
            <p class="text-[10px] text-amber-700 font-semibold mt-0.5">{{ $dossier['reward_status']['level'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Poin Pelanggaran</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $student->totalViolationPoints() }} Poin</div>
            <p class="text-[10px] font-semibold mt-0.5 {{ $dossier['guidance_status']['action_needed'] ? 'text-rose-700' : 'text-emerald-700' }}">
                {{ $dossier['guidance_status']['level'] }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase">7 Kebiasaan</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $dossier['ai_analysis']['metrics']['habit_score'] }}%</div>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $dossier['ai_analysis']['metrics']['habit_status'] }}</p>
        </div>
    </div>

    <!-- AI Synthesis Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-3">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
            Ringkasan Perkembangan Siswa (AI Analysis)
        </h3>
        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
            {{ $dossier['ai_analysis']['ringkasan'] }}
        </p>
    </div>

    <!-- Prestasi & Pelanggaran Tables (Separated strictly!) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Prestasi -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center justify-between">
                <span>1. Catatan Prestasi Terverifikasi</span>
                <span class="text-amber-600 text-xs font-black">{{ $student->totalAchievementPoints() }} Poin</span>
            </h3>
            <div class="space-y-2.5 text-xs">
                @forelse($dossier['achievements'] as $ach)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $ach->title }}</span>
                            <span class="font-black text-amber-600">+{{ $ach->points }}</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $ach->date->format('d/m/Y') }} • {{ $ach->level }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada data prestasi.</div>
                @endforelse
            </div>
        </div>

        <!-- Pelanggaran -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center justify-between">
                <span>2. Catatan Pelanggaran Tata Tertib</span>
                <span class="text-rose-600 text-xs font-black">{{ $student->totalViolationPoints() }} Poin</span>
            </h3>
            <div class="space-y-2.5 text-xs">
                @forelse($dossier['violations'] as $viol)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $viol->title }}</span>
                            <span class="font-black text-rose-600">{{ $viol->points }} Poin</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $viol->date->format('d/m/Y') }} • Lokasi: {{ $viol->location ?? '-' }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Alhamdulillah! Tidak ada catatan pelanggaran.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Catatan Pembinaan BK & Wali Kelas -->
    @if($dossier['guidances']->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <h3 class="text-base font-bold text-slate-900 mb-3">3. Riwayat Pembinaan & Konseling</h3>
            <div class="space-y-2.5 text-xs">
                @foreach($dossier['guidances'] as $g)
                    <div class="p-3.5 bg-purple-50/50 border border-purple-100 rounded-2xl">
                        <div class="flex items-center justify-between font-bold text-purple-950 mb-1">
                            <span>{{ $g->followUpLabel() }} ({{ $g->date->format('d M Y') }})</span>
                            <span class="px-2 py-0.5 bg-purple-200 rounded text-[10px]">{{ $g->status }}</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $g->recommendation }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
